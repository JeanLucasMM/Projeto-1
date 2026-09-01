<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CharacterCombatController extends Controller
{
    public function update(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize(
            'update',
            $character
        );

        /*
        |--------------------------------------------------------------------------
        | REGRA MORQUEN
        |--------------------------------------------------------------------------
        |
        | Cicatrizes do Passado altera os limites dos death saves:
        |
        | padrão  -> 3 sucessos / 3 falhas
        | Morquen -> 2 sucessos / 6 falhas
        |
        */

        $sheetSettings =
            is_array(
                $character->sheet_settings
                ?? null
            )
                ? $character->sheet_settings
                : [];

        $morquenRuleActive =
            (bool) data_get(
                $sheetSettings,
                'optional_rules.morquen',
                false
            );

        $deathSuccessLimit =
            $morquenRuleActive
                ? 2
                : 3;

        $deathFailureLimit =
            $morquenRuleActive
                ? 6
                : 3;

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
        | ARRAYS ENVIADOS VIA FORMDATA
        |--------------------------------------------------------------------------
        |
        | Alguns componentes da ficha enviam arrays como JSON.stringify(...).
        | Decodificamos esses campos antes da validação para que o Laravel receba
        | arrays reais, inclusive quando o valor enviado for [].
        |
        */

        $payload = $request->all();

        foreach ([
            'hit_dice',
            'conditions',
            'damage_resistances',
            'damage_immunities',
            'damage_vulnerabilities',
            'overrides',
        ] as $field) {

            if (!$request->has($field)) {
                continue;
            }

            $value =
                $request->input($field);

            if (!is_string($value)) {
                $payload[$field] = $value;
                continue;
            }

            $decoded =
                json_decode(
                    $value,
                    true
                );

            /*
            | JSON inválido permanece como string para que a validação falhe.
            | Assim evitamos apagar dados silenciosamente por causa de payload
            | malformado.
            */
            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                $payload[$field] = $decoded;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAÇÃO
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
                    'max:' . $deathSuccessLimit,
                ],

                'death_save_failures' => [
                    'sometimes',
                    'integer',
                    'min:0',
                    'max:' . $deathFailureLimit,
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
                | Estados e Defesas Especiais
                |------------------------------------------------------------------
                */

                'conditions' => [
                    'sometimes',
                    'array',
                ],

                'conditions.*' => [
                    'string',
                    'max:160',
                ],

                'damage_resistances' => [
                    'sometimes',
                    'array',
                ],

                'damage_resistances.*' => [
                    'string',
                    'max:160',
                ],

                'damage_immunities' => [
                    'sometimes',
                    'array',
                ],

                'damage_immunities.*' => [
                    'string',
                    'max:160',
                ],

                'damage_vulnerabilities' => [
                    'sometimes',
                    'array',
                ],

                'damage_vulnerabilities.*' => [
                    'string',
                    'max:160',
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
        | NORMALIZAÇÃO DOS DADOS DE VIDA
        |--------------------------------------------------------------------------
        |
        | Garante current <= maximum e mantém os tipos consistentes.
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
        | NORMALIZAÇÃO DOS ESTADOS E DEFESAS ESPECIAIS
        |--------------------------------------------------------------------------
        |
        | Remove valores vazios, espaços extras e duplicatas sem alterar o texto
        | efetivamente salvo pelo usuário.
        |
        */

        foreach ([
            'conditions',
            'damage_resistances',
            'damage_immunities',
            'damage_vulnerabilities',
        ] as $field) {

            if (!array_key_exists($field, $validated)) {
                continue;
            }

            $validated[$field] =
                collect($validated[$field])
                    ->map(
                        fn ($value) =>
                            trim((string) $value)
                    )
                    ->filter(
                        fn (string $value) =>
                            $value !== ''
                    )
                    ->unique(
                        fn (string $value) =>
                            mb_strtolower($value)
                    )
                    ->values()
                    ->all();
        }

        /*
        |--------------------------------------------------------------------------
        | DETERMINAÇÃO FINAL — RECARGA
        |--------------------------------------------------------------------------
        |
        | Após usar Determinação Final, o bloqueio permanece enquanto houver
        | exaustão. Quando a exaustão chega a 0, liberamos a habilidade.
        |
        */

        if (
            array_key_exists(
                'exhaustion_level',
                $validated
            )
            && (int) $validated['exhaustion_level'] === 0
        ) {
            $existingOverrides =
                $combat->overrides
                ?? [];

            if (is_string($existingOverrides)) {
                $decodedOverrides =
                    json_decode(
                        $existingOverrides,
                        true
                    );

                $existingOverrides =
                    is_array($decodedOverrides)
                        ? $decodedOverrides
                        : [];
            }

            if (!is_array($existingOverrides)) {
                $existingOverrides = [];
            }

            $incomingOverrides =
                $validated['overrides']
                ?? [];

            if (!is_array($incomingOverrides)) {
                $incomingOverrides = [];
            }

            $mergedOverrides =
                array_replace_recursive(
                    $existingOverrides,
                    $incomingOverrides
                );

            data_set(
                $mergedOverrides,
                'morquen.determination_final_locked',
                false
            );

            $validated['overrides'] =
                $mergedOverrides;
        }

        /*
        |--------------------------------------------------------------------------
        | PERSISTÊNCIA
        |--------------------------------------------------------------------------
        */

        $combat->update(
            $validated
        );

        /*
        |--------------------------------------------------------------------------
        | RETORNO
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'combat' =>
                $combat->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REGRA MORQUEN — DETERMINAÇÃO FINAL
    |--------------------------------------------------------------------------
    |
    | Enquanto morto pela sexta falha da Regra Morquen:
    |
    | - retorna com 1 PV;
    | - recebe PV temporários iguais à metade do máximo de PV;
    | - zera sucessos e falhas contra a morte;
    | - recebe 3 níveis de exaustão;
    | - não pode usar novamente enquanto ainda possuir exaustão.
    |
    */

    public function determinationFinal(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize(
            'update',
            $character
        );

        $sheetSettings =
            is_array(
                $character->sheet_settings
                ?? null
            )
                ? $character->sheet_settings
                : [];

        $morquenRuleActive =
            (bool) data_get(
                $sheetSettings,
                'optional_rules.morquen',
                false
            );

        if (!$morquenRuleActive) {
            throw ValidationException::withMessages([
                'morquen' =>
                    'A Regra Morquen não está ativa para este personagem.',
            ]);
        }

        $result = DB::transaction(
            function () use (
                $character
            ): array {
                $lockedCharacter =
                    Character::query()
                        ->whereKey(
                            $character->getKey()
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $combat =
                    $lockedCharacter
                        ->combat()
                        ->lockForUpdate()
                        ->first();

                if (!$combat) {
                    throw ValidationException::withMessages([
                        'combat' =>
                            'Os dados de combate do personagem ainda não existem.',
                    ]);
                }

                $currentHp =
                    max(
                        0,
                        (int) (
                            $combat->current_hp
                            ?? 0
                        )
                    );

                $deathFailures =
                    max(
                        0,
                        (int) (
                            $combat->death_save_failures
                            ?? 0
                        )
                    );

                if (
                    $currentHp > 0
                    || $deathFailures < 6
                ) {
                    throw ValidationException::withMessages([
                        'morquen' =>
                            'Determinação Final só pode ser usada enquanto o personagem estiver morto.',
                    ]);
                }

                $exhaustion =
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

                $determinationLocked =
                    (bool) data_get(
                        $overrides,
                        'morquen.determination_final_locked',
                        false
                    );

                /*
                 * Mesmo que a flag antiga continue true, exaustão 0 libera
                 * novamente a habilidade, como exige a regra.
                 */
                if (
                    $determinationLocked
                    && $exhaustion > 0
                ) {
                    throw ValidationException::withMessages([
                        'morquen' =>
                            'Determinação Final está indisponível até toda a exaustão ser removida.',
                    ]);
                }

                $maxHp =
                    max(
                        1,
                        (int) (
                            $combat->max_hp
                            ?? 1
                        )
                    );

                $temporaryHp =
                    intdiv(
                        $maxHp,
                        2
                    );

                $newExhaustion =
                    min(
                        6,
                        $exhaustion + 3
                    );

                data_set(
                    $overrides,
                    'morquen.determination_final_locked',
                    true
                );

                data_set(
                    $overrides,
                    'morquen.last_determination_final_at',
                    now()->toIso8601String()
                );

                $combat->current_hp =
                    1;

                $combat->temporary_hp =
                    $temporaryHp;

                $combat->death_save_successes =
                    0;

                $combat->death_save_failures =
                    0;

                $combat->exhaustion_level =
                    $newExhaustion;

                $combat->overrides =
                    $overrides;

                $combat->save();

                return [
                    'combat' =>
                        $combat->fresh(),

                    'temporary_hp_granted' =>
                        $temporaryHp,

                    'exhaustion_added' =>
                        3,
                ];
            }
        );

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Determinação Final trouxe o personagem de volta à vida.',

            'combat' =>
                $result['combat'],

            'morquen' => [
                'temporary_hp_granted' =>
                    $result['temporary_hp_granted'],

                'exhaustion_added' =>
                    $result['exhaustion_added'],
            ],
        ]);
    }

}