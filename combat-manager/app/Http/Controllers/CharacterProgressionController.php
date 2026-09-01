<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CharacterProgressionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SUBIR DE NÍVEL
    |--------------------------------------------------------------------------
    */

    public function levelUp(
        Request $request,
        Character $character
    ): JsonResponse {
        return $this->changeLevel(
            $request,
            $character,
            +1
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VOLTAR UM NÍVEL
    |--------------------------------------------------------------------------
    |
    | A classe nunca é reduzida abaixo do nível 1.
    |
    | Caso no futuro seja necessário "remover" uma multiclass, isso deve ser
    | uma ação separada, porque envolve apagar a entrada de character_classes.
    |
    */

    public function levelDown(
        Request $request,
        Character $character
    ): JsonResponse {
        return $this->changeLevel(
            $request,
            $character,
            -1
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ALTERAÇÃO DE NÍVEL
    |--------------------------------------------------------------------------
    */

    private function changeLevel(
        Request $request,
        Character $character,
        int $direction
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        if (
            !in_array(
                $direction,
                [-1, 1],
                true
            )
        ) {
            abort(422);
        }

        $validated = $request->validate([
            'class_id' => [
                'required',
                'integer',
            ],
        ]);

        $result = DB::transaction(
            function () use (
                $character,
                $validated,
                $direction
            ): array {
                $lockedCharacter =
                    Character::query()
                        ->whereKey(
                            $character->getKey()
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $classes =
                    $lockedCharacter
                        ->classes()
                        ->lockForUpdate()
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();

                $selectedClass =
                    $classes->first(
                        fn ($class) =>
                            (int) $class->id
                            === (int) $validated['class_id']
                    );

                if (!$selectedClass) {
                    throw ValidationException::withMessages([
                        'class_id' =>
                            'A classe selecionada não pertence a este personagem.',
                    ]);
                }

                $currentTotal =
                    (int) $classes->sum(
                        fn ($class) =>
                            (int) $class->level
                    );


                /*
                |--------------------------------------------------------------------------
                | LIMITES
                |--------------------------------------------------------------------------
                */

                if (
                    $direction > 0
                    && $currentTotal >= 20
                ) {
                    throw ValidationException::withMessages([
                        'class_id' =>
                            'O personagem já atingiu o nível total 20.',
                    ]);
                }

                if (
                    $direction > 0
                    && (int) $selectedClass->level >= 20
                ) {
                    throw ValidationException::withMessages([
                        'class_id' =>
                            'Esta classe já atingiu o nível 20.',
                    ]);
                }

                if (
                    $direction < 0
                    && (int) $selectedClass->level <= 1
                ) {
                    throw ValidationException::withMessages([
                        'class_id' =>
                            'Uma classe não pode ser reduzida abaixo do nível 1.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CLASSE ESCOLHIDA
                |--------------------------------------------------------------------------
                */

                $selectedClass->level =
                    (int) $selectedClass->level
                    + $direction;

                $selectedClass->save();


                /*
                |--------------------------------------------------------------------------
                | NÍVEL TOTAL
                |--------------------------------------------------------------------------
                */

                $newTotal =
                    $currentTotal
                    + $direction;

                $newTotal =
                    max(
                        1,
                        min(
                            20,
                            $newTotal
                        )
                    );

                $lockedCharacter->level =
                    $newTotal;


                /*
                |--------------------------------------------------------------------------
                | PROFICIÊNCIA AUTOMÁTICA
                |--------------------------------------------------------------------------
                |
                | Proficiência Custom ligada:
                |     preserva o valor manual.
                |
                | Proficiência Custom desligada:
                |     recalcula pelo novo nível total.
                |
                */

                $settings =
                    is_array(
                        $lockedCharacter->sheet_settings
                        ?? null
                    )
                        ? $lockedCharacter->sheet_settings
                        : [];

                $progressionSettings =
                    is_array(
                        $settings['progression']
                        ?? null
                    )
                        ? $settings['progression']
                        : [];

                $customEnabled =
                    (bool) (
                        $progressionSettings[
                            'proficiency_custom_enabled'
                        ]
                        ?? false
                    );

                $calculatedProficiency =
                    $this->calculatedProficiency(
                        $newTotal
                    );

                if (!$customEnabled) {
                    $lockedCharacter->proficiency_bonus =
                        $calculatedProficiency;
                }

                $lockedCharacter->save();


                /*
                |--------------------------------------------------------------------------
                | RETORNO
                |--------------------------------------------------------------------------
                */

                $classes =
                    $lockedCharacter
                        ->classes()
                        ->orderByDesc('is_primary')
                        ->orderByDesc('level')
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();

                return [
                    'level' =>
                        $newTotal,

                    'proficiency_bonus' =>
                        (int) $lockedCharacter->proficiency_bonus,

                    'calculated_proficiency' =>
                        $calculatedProficiency,

                    'proficiency_custom_enabled' =>
                        $customEnabled,

                    'classes' =>
                        $classes
                            ->map(
                                fn ($class) => [
                                    'id' =>
                                        (int) $class->id,

                                    'class' =>
                                        (string) $class->class,

                                    'subclass' =>
                                        $class->subclass,

                                    'level' =>
                                        (int) $class->level,

                                    'is_primary' =>
                                        (bool) (
                                            $class->is_primary
                                            ?? false
                                        ),
                                ]
                            )
                            ->values()
                            ->all(),
                ];
            }
        );

        $isLevelUp =
            $direction > 0;

        return response()->json([
            'success' =>
                true,

            'message' =>
                $isLevelUp
                    ? 'Nível aumentado com sucesso.'
                    : 'Nível reduzido com sucesso.',

            'character' => [
                'id' =>
                    (int) $character->getKey(),

                'level' =>
                    $result['level'],

                'proficiency_bonus' =>
                    $result['proficiency_bonus'],
            ],

            'progression' => [
                'direction' =>
                    $isLevelUp
                        ? 'up'
                        : 'down',

                'calculated_proficiency' =>
                    $result['calculated_proficiency'],

                'proficiency_custom_enabled' =>
                    $result['proficiency_custom_enabled'],
            ],

            'classes' =>
                $result['classes'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PROFICIÊNCIA CUSTOM
    |--------------------------------------------------------------------------
    */

    public function updateProficiency(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $validated = $request->validate([
            'enabled' => [
                'required',
                'boolean',
            ],

            /*
             * Sem min/max de propósito.
             * O jogador pode escolher qualquer inteiro.
             */
            'value' => [
                'nullable',
                'integer',
            ],
        ]);

        $result = DB::transaction(
            function () use (
                $character,
                $validated
            ): array {
                $lockedCharacter =
                    Character::query()
                        ->whereKey(
                            $character->getKey()
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $enabled =
                    (bool) $validated['enabled'];

                if (
                    $enabled
                    && !array_key_exists(
                        'value',
                        $validated
                    )
                ) {
                    throw ValidationException::withMessages([
                        'value' =>
                            'Informe o valor da proficiência customizada.',
                    ]);
                }

                $level =
                    max(
                        1,
                        (int) (
                            $lockedCharacter->level
                            ?? 1
                        )
                    );

                $calculated =
                    $this->calculatedProficiency(
                        $level
                    );

                $settings =
                    is_array(
                        $lockedCharacter->sheet_settings
                        ?? null
                    )
                        ? $lockedCharacter->sheet_settings
                        : [];

                if (
                    !isset(
                        $settings['progression']
                    )
                    || !is_array(
                        $settings['progression']
                    )
                ) {
                    $settings['progression'] =
                        [];
                }

                $settings['progression'][
                    'proficiency_custom_enabled'
                ] =
                    $enabled;

                if ($enabled) {
                    $lockedCharacter->proficiency_bonus =
                        (int) $validated['value'];
                } else {
                    $lockedCharacter->proficiency_bonus =
                        $calculated;
                }

                $lockedCharacter->sheet_settings =
                    $settings;

                $lockedCharacter->save();

                return [
                    'proficiency_bonus' =>
                        (int) $lockedCharacter->proficiency_bonus,

                    'calculated_proficiency' =>
                        $calculated,

                    'proficiency_custom_enabled' =>
                        $enabled,

                    'sheet_settings' =>
                        $settings,
                ];
            }
        );

        return response()->json([
            'success' =>
                true,

            'message' =>
                $result['proficiency_custom_enabled']
                    ? 'Proficiência customizada aplicada.'
                    : 'Proficiência automática restaurada.',

            'character' => [
                'id' =>
                    (int) $character->getKey(),

                'proficiency_bonus' =>
                    $result['proficiency_bonus'],

                'sheet_settings' =>
                    $result['sheet_settings'],
            ],

            'progression' => [
                'calculated_proficiency' =>
                    $result['calculated_proficiency'],

                'proficiency_custom_enabled' =>
                    $result['proficiency_custom_enabled'],
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function calculatedProficiency(
        int $level
    ): int {
        return match (true) {
            $level >= 17 => 6,
            $level >= 13 => 5,
            $level >= 9 => 4,
            $level >= 5 => 3,
            default => 2,
        };
    }


    private function authorizeCharacter(
        Request $request,
        Character $character
    ): void {
        if (
            $request->user()
            && isset(
                $character->user_id
            )
        ) {
            abort_unless(
                (int) $character->user_id
                === (int) $request->user()->id,
                403
            );
        }
    }
}