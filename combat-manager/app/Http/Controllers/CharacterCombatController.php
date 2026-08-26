<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterCombatController extends Controller
{
    public function update(
        Request $request,
        Character $character
    ): JsonResponse {
        abort_unless(
            $character->user_id === $request->user()->id,
            403
        );

        $combat = $character->combat()->firstOrCreate(
            [],
            [
                'experience_points' => 0,

                'current_hp' => 0,
                'max_hp' => 0,
                'temporary_hp' => 0,
                'temporary_max_hp' => 0,

                'hit_dice' => [],

                'armor_class' => 10,
                'speed' => 30,
                'initiative_bonus' => 0,

                'death_save_successes' => 0,
                'death_save_failures' => 0,

                'concentration_active' => false,
                'concentration_spell_id' => null,

                'exhaustion_level' => 0,

                'conditions' => [],
                'damage_resistances' => [],
                'damage_immunities' => [],
                'damage_vulnerabilities' => [],

                'overrides' => [],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | JSON enviado via FormData
        |--------------------------------------------------------------------------
        |
        | hit_dice e overrides chegam como strings JSON pela Blade.
        | Decodificamos antes da validação estrutural.
        |
        */

        $payload = $request->all();

        if ($request->has('hit_dice')) {

            $decodedHitDice =
                is_string($request->input('hit_dice'))
                    ? json_decode(
                        $request->input('hit_dice'),
                        true
                    )
                    : $request->input('hit_dice');

            $payload['hit_dice'] =
                is_array($decodedHitDice)
                    ? $decodedHitDice
                    : [];
        }

        if ($request->has('overrides')) {

            $decodedOverrides =
                is_string($request->input('overrides'))
                    ? json_decode(
                        $request->input('overrides'),
                        true
                    )
                    : $request->input('overrides');

            $payload['overrides'] =
                is_array($decodedOverrides)
                    ? $decodedOverrides
                    : [];
        }

        /*
        |--------------------------------------------------------------------------
        | Validação
        |--------------------------------------------------------------------------
        */

        $validated = validator(
            $payload,
            [
                /*
                |------------------------------------------------------------------
                | Progressão
                |------------------------------------------------------------------
                */

                'experience_points' => [
                    'sometimes',
                    'integer',
                    'min:0',
                ],

                /*
                |------------------------------------------------------------------
                | Vida
                |------------------------------------------------------------------
                */

                'current_hp' => [
                    'sometimes',
                    'integer',
                    'min:0',
                ],

                'max_hp' => [
                    'sometimes',
                    'integer',
                    'min:1',
                ],

                'temporary_hp' => [
                    'sometimes',
                    'integer',
                    'min:0',
                ],

                'temporary_max_hp' => [
                    'sometimes',
                    'integer',
                    'min:0',
                ],

                /*
                |------------------------------------------------------------------
                | Dados de Vida
                |------------------------------------------------------------------
                */

                'hit_dice' => [
                    'sometimes',
                    'array',
                ],

                'hit_dice.*.die' => [
                    'required',
                    'string',
                    'in:d4,d6,d8,d10,d12,d20',
                ],

                'hit_dice.*.current' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                'hit_dice.*.maximum' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                /*
                |------------------------------------------------------------------
                | Defesa
                |------------------------------------------------------------------
                */

                'armor_class' => [
                    'sometimes',
                    'integer',
                    'min:0',
                ],

                /*
                |------------------------------------------------------------------
                | Movimento
                |------------------------------------------------------------------
                */

                'speed' => [
                    'sometimes',
                    'integer',
                    'min:0',
                ],

                /*
                |------------------------------------------------------------------
                | Iniciativa
                |------------------------------------------------------------------
                */

                'initiative_bonus' => [
                    'sometimes',
                    'integer',
                ],

                /*
                |------------------------------------------------------------------
                | Death Saves
                |------------------------------------------------------------------
                */

                'death_save_successes' => [
                    'sometimes',
                    'integer',
                    'min:0',
                    'max:3',
                ],

                'death_save_failures' => [
                    'sometimes',
                    'integer',
                    'min:0',
                    'max:3',
                ],

                /*
                |------------------------------------------------------------------
                | Concentração
                |------------------------------------------------------------------
                */

                'concentration_active' => [
                    'sometimes',
                    'boolean',
                ],

                'concentration_spell_id' => [
                    'sometimes',
                    'nullable',
                    'integer',
                ],

                /*
                |------------------------------------------------------------------
                | Exaustão
                |------------------------------------------------------------------
                */

                'exhaustion_level' => [
                    'sometimes',
                    'integer',
                    'min:0',
                    'max:6',
                ],

                /*
                |------------------------------------------------------------------
                | Estados
                |------------------------------------------------------------------
                */

                'conditions' => [
                    'sometimes',
                    'array',
                ],

                'damage_resistances' => [
                    'sometimes',
                    'array',
                ],

                'damage_immunities' => [
                    'sometimes',
                    'array',
                ],

                'damage_vulnerabilities' => [
                    'sometimes',
                    'array',
                ],

                /*
                |------------------------------------------------------------------
                | Overrides
                |------------------------------------------------------------------
                */

                'overrides' => [
                    'sometimes',
                    'array',
                ],
            ]
        )->validate();

        /*
        |--------------------------------------------------------------------------
        | Normalização dos Dados de Vida
        |--------------------------------------------------------------------------
        |
        | Garante que:
        |
        | current <= maximum
        |
        | e que os tipos permaneçam consistentes.
        |
        */

        if (array_key_exists('hit_dice', $validated)) {

            $validated['hit_dice'] =
                collect($validated['hit_dice'])
                    ->map(function (array $hitDie) {

                        $maximum = max(
                            0,
                            (int) (
                                $hitDie['maximum'] ?? 0
                            )
                        );

                        $current = max(
                            0,
                            min(
                                $maximum,
                                (int) (
                                    $hitDie['current'] ?? 0
                                )
                            )
                        );

                        return [
                            'die' => strtolower(
                                trim(
                                    (string) (
                                        $hitDie['die'] ?? 'd8'
                                    )
                                )
                            ),

                            'current' =>
                                $current,

                            'maximum' =>
                                $maximum,
                        ];
                    })
                    ->filter(
                        fn (array $hitDie) =>
                            $hitDie['maximum'] > 0
                    )
                    ->values()
                    ->all();
        }

        /*
        |--------------------------------------------------------------------------
        | Normalização dos Estados
        |--------------------------------------------------------------------------
        */

        foreach ([
            'conditions',
            'damage_resistances',
            'damage_immunities',
            'damage_vulnerabilities',
        ] as $field) {

            if (array_key_exists($field, $validated)) {

                $validated[$field] =
                    array_values(
                        $validated[$field]
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Persistência
        |--------------------------------------------------------------------------
        */

        $combat->update(
            $validated
        );

        /*
        |--------------------------------------------------------------------------
        | Retorno
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'combat' =>
                $combat->fresh(),
        ]);
    }
}