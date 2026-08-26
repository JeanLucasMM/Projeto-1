<?php

namespace App\Http\Controllers;

use App\Enums\RestType;
use App\Models\Character;
use App\Services\Characters\CharacterRestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class CharacterRestController extends Controller
{
    public function store(
        Request $request,
        Character $character,
        CharacterRestService $restService
    ): JsonResponse {
        try {
            /*
            |--------------------------------------------------------------------------
            | VALIDAÇÃO
            |--------------------------------------------------------------------------
            */

            $validated =
                $request->validate([
                    'type' => [
                        'required',
                        'string',

                        Rule::in([
                            RestType::SHORT->value,
                            RestType::LONG->value,
                        ]),
                    ],
                ]);

            /*
            |--------------------------------------------------------------------------
            | TIPO
            |--------------------------------------------------------------------------
            */

            $restType =
                RestType::from(
                    $validated['type']
                );

            /*
            |--------------------------------------------------------------------------
            | EXECUTA DESCANSO
            |--------------------------------------------------------------------------
            */

            $character =
                $restService->rest(
                    $character,
                    $restType
                );

            $combat =
                $character->combat;

            /*
            |--------------------------------------------------------------------------
            | NORMALIZA HIT DICE PARA RESPOSTA JSON
            |--------------------------------------------------------------------------
            */

            $hitDice =
                $combat?->hit_dice ?? [];

            if ($hitDice instanceof \Illuminate\Support\Collection) {
                $hitDice =
                    $hitDice->all();
            }

            if (is_string($hitDice)) {
                $decoded =
                    json_decode(
                        $hitDice,
                        true
                    );

                $hitDice =
                    is_array($decoded)
                        ? $decoded
                        : [];
            }

            if (!is_array($hitDice)) {
                $hitDice = [];
            }

            /*
            |--------------------------------------------------------------------------
            | RESPOSTA
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,

                'rest' => [
                    'type' =>
                        $restType->value,
                ],

                'combat' => [
                    'current_hp' =>
                        (int) (
                            $combat?->current_hp
                            ?? 0
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | VIDA MÁXIMA PERMANENTE
                    |--------------------------------------------------------------------------
                    */

                    'max_hp' =>
                        (int) (
                            $combat?->max_hp
                            ?? 1
                        ),

                    'temporary_hp' =>
                        (int) (
                            $combat?->temporary_hp
                            ?? 0
                        ),

                    'temporary_max_hp' =>
                        (int) (
                            $combat?->temporary_max_hp
                            ?? 0
                        ),

                    'death_save_successes' =>
                        (int) (
                            $combat?->death_save_successes
                            ?? 0
                        ),

                    'death_save_failures' =>
                        (int) (
                            $combat?->death_save_failures
                            ?? 0
                        ),

                    'hit_dice' =>
                        $hitDice,
                ],
            ]);

        } catch (Throwable $exception) {
            /*
            |--------------------------------------------------------------------------
            | LOG
            |--------------------------------------------------------------------------
            */

            report(
                $exception
            );

            /*
            |--------------------------------------------------------------------------
            | JSON DE ERRO
            |--------------------------------------------------------------------------
            |
            | Em APP_DEBUG=true exibimos a causa real.
            |
            | Isso vai aparecer diretamente no menu da fogueira porque
            | performRest() já lê data.message.
            |
            */

            return response()->json([
                'success' => false,

                'message' =>
                    config('app.debug')
                        ? $exception->getMessage()
                        : 'Não foi possível realizar o descanso.',
            ], 500);
        }
    }
}