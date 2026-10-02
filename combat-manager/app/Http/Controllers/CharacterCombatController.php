<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CombatParticipationRequest;
use App\Models\CombatPlayer;
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
        | HISTÓRICO DE MORTE — ESTADO ANTERIOR
        |--------------------------------------------------------------------------
        |
        | Guardamos o estado antes da atualização para registrar uma morte apenas
        | na transição vivo/caído -> morto. Atualizações posteriores enquanto o
        | personagem continuar morto não incrementam o contador novamente.
        |
        */

        $previousCurrentHp =
            max(
                0,
                (int) (
                    $combat->current_hp
                    ?? 0
                )
            );

        $previousDeathFailures =
            max(
                0,
                (int) (
                    $combat->death_save_failures
                    ?? 0
                )
            );

        $wasDead =
            $previousCurrentHp <= 0
            &&
            $previousDeathFailures >= $deathFailureLimit;

        $overridesBeforeUpdate =
            $this->normalizedCombatOverrides(
                $combat->overrides
                ?? []
            );

        $deathHistoryBeforeUpdate =
            data_get(
                $overridesBeforeUpdate,
                'death_history',
                []
            );

        if (!is_array($deathHistoryBeforeUpdate)) {
            $deathHistoryBeforeUpdate = [];
        }

        /*
        | Se o personagem já estava morto antes deste recurso existir, não temos
        | como reconstruir a data real. Mesmo assim, preservamos o fato histórico
        | antes que uma cura ou reset dos death saves apague o estado observável.
        */
        if (
            $wasDead
            &&
            max(
                0,
                (int) (
                    $deathHistoryBeforeUpdate['count']
                    ?? 0
                )
            )
            ===
            0
        ) {
            $deathHistoryBeforeUpdate = [
                'count' => 1,
                'last_at' => null,
                'events' => [],
            ];
        }

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

        $freshCombat =
            $combat->fresh();

        $finalOverrides =
            $this->normalizedCombatOverrides(
                $freshCombat?->overrides
                ?? []
            );

        /*
        | O histórico é interno do sistema. Mesmo que algum componente da ficha
        | envie o objeto overrides sem death_history, preservamos o registro.
        */
        if (!empty($deathHistoryBeforeUpdate)) {
            data_set(
                $finalOverrides,
                'death_history',
                $deathHistoryBeforeUpdate
            );
        }

        $finalCurrentHp =
            max(
                0,
                (int) (
                    $freshCombat?->current_hp
                    ?? 0
                )
            );

        $finalDeathFailures =
            max(
                0,
                (int) (
                    $freshCombat?->death_save_failures
                    ?? 0
                )
            );

        $isDead =
            $finalCurrentHp <= 0
            &&
            $finalDeathFailures >= $deathFailureLimit;

        if (
            !$wasDead
            &&
            $isDead
        ) {
            $deathHistory =
                data_get(
                    $finalOverrides,
                    'death_history',
                    []
                );

            if (!is_array($deathHistory)) {
                $deathHistory = [];
            }

            $events =
                $deathHistory['events']
                ?? [];

            if (!is_array($events)) {
                $events = [];
            }

            $deathAt =
                now()->toIso8601String();

            $events[] = [
                'at' =>
                    $deathAt,

                'failure_limit' =>
                    $deathFailureLimit,

                'morquen' =>
                    $morquenRuleActive,
            ];

            /*
            | Mantemos as ocorrências recentes para consulta sem permitir que o
            | JSON cresça indefinidamente. O contador total nunca é truncado.
            */
            $events =
                array_slice(
                    array_values(
                        array_filter(
                            $events,
                            fn ($event) =>
                                is_array($event)
                        )
                    ),
                    -20
                );

            $deathHistory['count'] =
                max(
                    0,
                    (int) (
                        $deathHistory['count']
                        ?? 0
                    )
                )
                +
                1;

            $deathHistory['last_at'] =
                $deathAt;

            $deathHistory['events'] =
                $events;

            data_set(
                $finalOverrides,
                'death_history',
                $deathHistory
            );
        }

        $storedOverrides =
            $this->normalizedCombatOverrides(
                $freshCombat?->overrides
                ?? []
            );

        if (
            $finalOverrides
            !==
            $storedOverrides
        ) {
            $freshCombat->overrides =
                $finalOverrides;

            $freshCombat->save();

            $freshCombat =
                $freshCombat->fresh();
        }

        /*
        |--------------------------------------------------------------------------
        | RETORNO
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'combat' =>
                $freshCombat,
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

                $deathHistory =
                    data_get(
                        $overrides,
                        'death_history',
                        []
                    );

                if (!is_array($deathHistory)) {
                    $deathHistory = [];
                }

                if (
                    max(
                        0,
                        (int) (
                            $deathHistory['count']
                            ?? 0
                        )
                    )
                    ===
                    0
                ) {
                    data_set(
                        $overrides,
                        'death_history',
                        [
                            'count' => 1,
                            'last_at' => null,
                            'events' => [],
                        ]
                    );
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


    /*
    |--------------------------------------------------------------------------
    | CHAMADO DE COMBATE DA CAMPANHA
    |--------------------------------------------------------------------------
    |
    | A Quick Stat continua salvando normalmente pelo método update().
    | Estes endpoints existem apenas para a interação iniciada pelo Mestre:
    | participar -> rolar iniciativa -> entrar na fila do combate.
    |
    */

    public function combatCall(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        CombatParticipationRequest::query()
            ->where('character_id', $character->id)
            ->where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->update([
                'status' => 'declined',
                'responded_at' => now(),
                'updated_at' => now(),
            ]);

        $call = CombatParticipationRequest::query()
            ->where('character_id', $character->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->whereNull('initiative')
            ->whereHas(
                'combat',
                fn ($query) => $query->where('is_active', true)
            )
            ->with(['combat.campaign'])
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'call' => $call ? $this->combatCallPayload($call) : null,
        ]);
    }


    public function respondCombatCall(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated = $request->validate([
            'request_id' => ['required', 'integer'],
            'accepted' => ['required', 'boolean'],
        ]);

        return DB::transaction(function () use ($character, $validated): JsonResponse {
            $call = CombatParticipationRequest::query()
                ->whereKey((int) $validated['request_id'])
                ->where('character_id', $character->id)
                ->with(['combat.campaign'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($call->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este chamado já foi respondido.',
                ], 409);
            }

            if ($call->expires_at->lte(now())) {
                $call->update([
                    'status' => 'declined',
                    'responded_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'timed_out' => true,
                    'call' => null,
                ]);
            }

            if (!(bool) $validated['accepted']) {
                $call->update([
                    'status' => 'declined',
                    'responded_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'call' => null,
                ]);
            }

            $combat = $call->combat;

            abort_unless(
                $combat && $combat->is_active,
                422,
                'Este combate não está mais ativo.'
            );

            $campaign = $combat->campaign;

            abort_unless(
                $campaign,
                422,
                'Este combate não está vinculado a uma campanha.'
            );

            $isActiveCharacter = $campaign
                ->characters()
                ->where('characters.id', $character->id)
                ->wherePivot('is_active', true)
                ->exists();

            abort_unless(
                $isActiveCharacter,
                422,
                'Este personagem não está mais ativo nesta campanha.'
            );

            CombatPlayer::query()->firstOrCreate(
                [
                    'combat_id' => $combat->id,
                    'character_id' => $character->id,
                ],
                [
                    'name' => $character->name,
                    'initiative' => 0,
                ]
            );

            $call->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'call' => $this->combatCallPayload(
                    $call->fresh()->loadMissing(['combat.campaign'])
                ),
            ]);
        });
    }


    public function submitCombatCallInitiative(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated = $request->validate([
            'request_id' => [
                'required',
                'integer',
            ],

            /*
            | O Player envia o valor FINAL que decidiu usar.
            | Isso também permite rolagem com dado físico,
            | vantagem, desvantagem e efeitos especiais.
            */
            'initiative' => [
                'required',
                'integer',
                'between:-100,200',
            ],
        ]);

        return DB::transaction(function () use ($character, $validated): JsonResponse {
            $call = CombatParticipationRequest::query()
                ->whereKey((int) $validated['request_id'])
                ->where('character_id', $character->id)
                ->with(['combat'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $call->status === 'accepted',
                422,
                'Aceite o combate antes de rolar iniciativa.'
            );

            $combat = $call->combat;

            abort_unless(
                $combat && $combat->is_active,
                422,
                'Este combate não está mais ativo.'
            );

            if ($call->initiative !== null) {
                return response()->json([
                    'success' => true,
                    'total' => (int) $call->initiative,
                    'already_submitted' => true,
                ]);
            }

            $modifier =
                $this->quickStatInitiativeModifier(
                    $character
                );

            $total =
                (int) $validated[
                    'initiative'
                ];

            $combatPlayer = CombatPlayer::query()->firstOrCreate(
                [
                    'combat_id' => $combat->id,
                    'character_id' => $character->id,
                ],
                [
                    'name' => $character->name,
                    'initiative' => 0,
                ]
            );

            $combatPlayer->forceFill([
                'name' => $character->name,
                'initiative' => $total,
            ])->save();

            $call->update(['initiative' => $total]);

            $this->storeQuickStatCombatInitiative(
                $character,
                $modifier,
                $total
            );

            DB::table('combat_logs')->insert([
                'combat_id' => $combat->id,
                'round' => (int) ($combat->current_round ?? 1),
                'turn' => (int) ($combat->current_turn ?? 0),
                'description' => "{$character->name} entrou no combate com iniciativa {$total}.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' =>
                    true,

                'modifier' =>
                    $modifier,

                'total' =>
                    $total,
            ]);
        });
    }


    private function combatCallPayload(
        CombatParticipationRequest $call
    ): array {
        $phase = $call->status === 'pending' ? 'join' : 'initiative';

        return [
            'request_id' => (int) $call->id,
            'combat_id' => (int) $call->combat_id,
            'combat_name' => (string) ($call->combat?->name ?? 'Combate'),
            'campaign_name' => $call->combat?->campaign?->name
                ? (string) $call->combat->campaign->name
                : null,
            'phase' => $phase,
            'seconds_left' => $phase === 'join'
                ? max(0, now()->diffInSeconds($call->expires_at, false))
                : null,
        ];
    }


    private function quickStatInitiativeModifier(
        Character $character
    ): int {
        $character->loadMissing([
            'abilities',
            'combat',
        ]);

        $abilities =
            $character->abilities;

        $temporaryBonuses =
            is_array(
                $abilities?->temporary_bonuses
            )
                ? $abilities->temporary_bonuses
                : [];

        $abilityOverrides =
            is_array(
                $abilities?->overrides
            )
                ? $abilities->overrides
                : [];

        $baseDexterity =
            (int) (
                $abilities?->dexterity
                ?? 10
            );

        $hasDexterityOverride =
            array_key_exists(
                'dexterity',
                $abilityOverrides
            )
            &&
            $abilityOverrides[
                'dexterity'
            ] !== null
            &&
            $abilityOverrides[
                'dexterity'
            ] !== '';

        $hasShortDexterityOverride =
            array_key_exists(
                'dex',
                $abilityOverrides
            )
            &&
            $abilityOverrides[
                'dex'
            ] !== null
            &&
            $abilityOverrides[
                'dex'
            ] !== '';

        if ($hasDexterityOverride) {
            $dexterityScore =
                max(
                    1,
                    (int) $abilityOverrides[
                        'dexterity'
                    ]
                );
        } elseif (
            $hasShortDexterityOverride
        ) {
            $dexterityScore =
                max(
                    1,
                    (int) $abilityOverrides[
                        'dex'
                    ]
                );
        } else {
            $dexterityScore =
                max(
                    1,
                    $baseDexterity
                    +
                    (int) (
                        $temporaryBonuses[
                            'dexterity'
                        ]
                        ?? $temporaryBonuses[
                            'dex'
                        ]
                        ?? 0
                    )
                );
        }

        $dexterityModifier =
            (int) floor(
                (
                    $dexterityScore
                    -
                    10
                )
                /
                2
            );

        $combat = $character->combat;
        $overrides = $this->normalizedCombatOverrides($combat?->overrides ?? []);
        $initiativeConfig = data_get($overrides, 'quick_stats.initiative', []);

        if (!is_array($initiativeConfig)) {
            $initiativeConfig = [];
        }

        $mode = in_array(
            $initiativeConfig['mode'] ?? 'none',
            ['none', 'proficient', 'expertise'],
            true
        )
            ? $initiativeConfig['mode']
            : 'none';

        $legacyInitiative = (int) ($combat?->initiative_bonus ?? 0);

        $initiativeBonus = array_key_exists('bonus', $initiativeConfig)
            ? (int) $initiativeConfig['bonus']
            : ($legacyInitiative !== 0
                ? $legacyInitiative - $dexterityModifier
                : 0);

        $proficiencyBonus = max(
            0,
            (int) ($character->proficiency_bonus ?? $this->proficiencyBonusByLevel(
                (int) ($character->level ?? 1)
            ))
        );

        $training = match ($mode) {
            'expertise' => $proficiencyBonus * 2,
            'proficient' => $proficiencyBonus,
            default => 0,
        };

        return $dexterityModifier + $training + $initiativeBonus;
    }


    private function quickStatInitiativeExhaustionPenalty(
        Character $character
    ): int {
        $character->loadMissing('combat');

        $level = max(
            0,
            min(6, (int) ($character->combat?->exhaustion_level ?? 0))
        );

        return $level * 2;
    }


    private function storeQuickStatCombatInitiative(
        Character $character,
        int $modifier,
        int $total
    ): void {
        $combat = $character->combat()->firstOrCreate([], [
            'experience_points' => 0,
            'current_hp' => 0,
            'max_hp' => 1,
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
        ]);

        $overrides = $this->normalizedCombatOverrides($combat->overrides ?? []);
        data_set($overrides, 'quick_stats.initiative.combat_value', $total);

        $combat->initiative_bonus = $modifier;
        $combat->overrides = $overrides;
        $combat->save();
    }


    private function normalizedCombatOverrides(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }


    private function proficiencyBonusByLevel(int $level): int
    {
        $level = max(1, $level);

        return match (true) {
            $level >= 17 => 6,
            $level >= 13 => 5,
            $level >= 9 => 4,
            $level >= 5 => 3,
            default => 2,
        };
    }

}