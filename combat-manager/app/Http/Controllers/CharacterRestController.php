<?php

namespace App\Http\Controllers;

use App\Enums\RestType;
use App\Models\Character;
use App\Services\Characters\CharacterRestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Throwable;

class CharacterRestController extends Controller
{
    public function store(
        Request $request,
        Character $character,
        CharacterRestService $restService
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | Autorização
        |--------------------------------------------------------------------------
        |
        | Descanso altera estado persistente da ficha. Como o Mestre está em
        | modo somente leitura, apenas o dono pode executar esta ação.
        |
        | A autorização fica fora do try para preservar corretamente o HTTP 403.
        |
        */

        Gate::authorize(
            'update',
            $character
        );

        try {
            $validated = $request->validate([
                'type' => [
                    'required',
                    'string',
                    Rule::in([
                        RestType::SHORT->value,
                        RestType::LONG->value,
                    ]),
                ],
            ]);

            $restType = RestType::from($validated['type']);

            [
                $character,
                $featureTrackers,
            ] = DB::transaction(
                function () use (
                    $character,
                    $restType,
                    $restService
                ): array {
                    $character = $restService->rest(
                        $character,
                        $restType
                    );

                    $this->recoverExhaustion(
                        $character,
                        $restType
                    );

                    $featureTrackers = $this->recoverFeatureTrackers(
                        $character,
                        $restType
                    );

                    $character->load('combat');

                    return [
                        $character,
                        $featureTrackers,
                    ];
                }
            );

            $combat = $character->combat;

            $hitDice = $combat?->hit_dice ?? [];

            if ($hitDice instanceof \Illuminate\Support\Collection) {
                $hitDice = $hitDice->all();
            }

            if (is_string($hitDice)) {
                $decoded = json_decode(
                    $hitDice,
                    true
                );

                $hitDice = is_array($decoded)
                    ? $decoded
                    : [];
            }

            if (!is_array($hitDice)) {
                $hitDice = [];
            }

            return response()->json([
                'success' => true,

                'rest' => [
                    'type' => $restType->value,
                ],

                'combat' => [
                    'current_hp' => (int) (
                        $combat?->current_hp
                        ?? 0
                    ),

                    'max_hp' => (int) (
                        $combat?->max_hp
                        ?? 1
                    ),

                    'temporary_hp' => (int) (
                        $combat?->temporary_hp
                        ?? 0
                    ),

                    'temporary_max_hp' => (int) (
                        $combat?->temporary_max_hp
                        ?? 0
                    ),

                    'death_save_successes' => (int) (
                        $combat?->death_save_successes
                        ?? 0
                    ),

                    'death_save_failures' => (int) (
                        $combat?->death_save_failures
                        ?? 0
                    ),

                    'exhaustion_level' => (int) (
                        $combat?->exhaustion_level
                        ?? 0
                    ),

                    'overrides' =>
                        $combat?->overrides
                        ?? [],

                    'hit_dice' => $hitDice,
                ],

                'feature_trackers' => $featureTrackers,
            ]);

        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,

                'message' => config('app.debug')
                    ? $exception->getMessage()
                    : 'Não foi possível realizar o descanso.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXAUSTÃO
    |--------------------------------------------------------------------------
    |
    | Um descanso longo remove exatamente 1 nível de exaustão.
    |
    | Quando a exaustão chega a 0, Determinação Final volta a ficar
    | disponível para personagens com Regra Morquen.
    |
    */

    private function recoverExhaustion(
        Character $character,
        RestType $restType
    ): void {
        if ($restType !== RestType::LONG) {
            return;
        }

        $combat =
            $character
                ->combat()
                ->lockForUpdate()
                ->first();

        if (!$combat) {
            return;
        }

        $currentExhaustion =
            min(
                6,
                max(
                    0,
                    (int) (
                        $combat->exhaustion_level
                        ?? 0
                    )
                )
            );

        if ($currentExhaustion <= 0) {
            return;
        }

        $newExhaustion =
            max(
                0,
                $currentExhaustion - 1
            );

        $overrides =
            $combat->overrides
            ?? [];

        if (is_string($overrides)) {
            $decoded =
                json_decode(
                    $overrides,
                    true
                );

            $overrides =
                is_array($decoded)
                    ? $decoded
                    : [];
        }

        if (!is_array($overrides)) {
            $overrides = [];
        }

        if ($newExhaustion === 0) {
            data_set(
                $overrides,
                'morquen.determination_final_locked',
                false
            );
        }

        $combat->exhaustion_level =
            $newExhaustion;

        $combat->overrides =
            $overrides;

        $combat->save();
    }


    private function recoverFeatureTrackers(
        Character $character,
        RestType $restType
    ): array {
        $recoveries = match ($restType) {
            RestType::SHORT => [
                'short_rest',
            ],

            RestType::LONG => [
                'short_rest',
                'long_rest',
            ],
        };

        $features = $character
            ->features()
            ->whereNotNull('uses_max')
            ->whereIn(
                'recovery',
                $recoveries
            )
            ->get();

        $trackers = [];

        foreach ($features as $feature) {
            $maximum = max(
                1,
                (int) $feature->uses_max
            );

            $data = is_array($feature->data)
                ? $feature->data
                : [];

            $counterMode = (
                $data['counter_mode']
                ?? 'spend'
            ) === 'build'
                ? 'build'
                : 'spend';

            $current = $counterMode === 'build'
                ? 0
                : $maximum;

            if (
                (int) $feature->uses_current
                !== $current
            ) {
                $feature->update([
                    'uses_current' => $current,
                ]);
            }

            $trackers[] = [
                'id' => (int) $feature->id,
                'uses_current' => $current,
                'uses_max' => $maximum,
                'recovery' => $feature->recovery,
                'counter_mode' => $counterMode,
            ];
        }

        return $trackers;
    }
}