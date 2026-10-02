<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CombatParticipationRequest;
use App\Models\CombatPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CharacterCombatInvitationController extends Controller
{
    public function show(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeOwner(
            $request,
            $character
        );

        /*
        |--------------------------------------------------------------------------
        | Expira convites abandonados
        |--------------------------------------------------------------------------
        |
        | Não precisamos de scheduler. A ficha do Player e a tela do Mestre
        | consultam o estado periodicamente; a primeira consulta após o prazo
        | transforma "pending" em "declined".
        |
        */

        CombatParticipationRequest::query()
            ->where(
                'character_id',
                $character->id
            )
            ->where(
                'status',
                'pending'
            )
            ->where(
                'expires_at',
                '<=',
                now()
            )
            ->update([
                'status' =>
                    'declined',

                'responded_at' =>
                    now(),

                'updated_at' =>
                    now(),
            ]);

        $call =
            CombatParticipationRequest::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->whereIn(
                    'status',
                    [
                        'pending',
                        'accepted',
                    ]
                )
                ->whereNull(
                    'initiative'
                )
                ->whereHas(
                    'combat',
                    fn ($query) =>
                        $query->where(
                            'is_active',
                            true
                        )
                )
                ->with([
                    'combat.campaign',
                ])
                ->latest('id')
                ->first();

        return response()->json([
            'success' =>
                true,

            'call' =>
                $call
                    ? $this->payload(
                        $call
                    )
                    : null,
        ]);
    }


    public function respond(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeOwner(
            $request,
            $character
        );

        $validated =
            $request->validate([
                'request_id' => [
                    'required',
                    'integer',
                ],

                'accepted' => [
                    'required',
                    'boolean',
                ],
            ]);

        return DB::transaction(
            function () use (
                $character,
                $validated
            ): JsonResponse {
                $call =
                    CombatParticipationRequest::query()
                        ->whereKey(
                            (int) $validated[
                                'request_id'
                            ]
                        )
                        ->where(
                            'character_id',
                            $character->id
                        )
                        ->with([
                            'combat.campaign',
                        ])
                        ->lockForUpdate()
                        ->firstOrFail();

                if (
                    $call->status
                    !==
                    'pending'
                ) {
                    return response()->json(
                        [
                            'success' =>
                                false,

                            'message' =>
                                'Este convite já foi respondido.',
                        ],
                        409
                    );
                }

                if (
                    $call->expires_at
                    ->lte(
                        now()
                    )
                ) {
                    $call->update([
                        'status' =>
                            'declined',

                        'responded_at' =>
                            now(),
                    ]);

                    return response()->json([
                        'success' =>
                            true,

                        'timed_out' =>
                            true,

                        'call' =>
                            null,
                    ]);
                }

                if (
                    !(bool) $validated[
                        'accepted'
                    ]
                ) {
                    $call->update([
                        'status' =>
                            'declined',

                        'responded_at' =>
                            now(),
                    ]);

                    return response()->json([
                        'success' =>
                            true,

                        'call' =>
                            null,
                    ]);
                }

                $combat =
                    $call->combat;

                abort_unless(
                    $combat
                    &&
                    $combat->is_active,
                    422,
                    'Este combate não está mais ativo.'
                );

                $campaign =
                    $combat->campaign;

                abort_unless(
                    $campaign,
                    422,
                    'O combate não está vinculado a uma campanha.'
                );

                /*
                | O personagem precisa continuar ativo na Party no momento
                | em que o Player aceita o chamado.
                */
                $isStillActive =
                    $campaign
                        ->characters()
                        ->where(
                            'characters.id',
                            $character->id
                        )
                        ->wherePivot(
                            'is_active',
                            true
                        )
                        ->exists();

                abort_unless(
                    $isStillActive,
                    422,
                    'Este personagem não está mais ativo nesta campanha.'
                );

                CombatPlayer::query()
                    ->firstOrCreate(
                        [
                            'combat_id' =>
                                $combat->id,

                            'character_id' =>
                                $character->id,
                        ],
                        [
                            'name' =>
                                $character->name,

                            'initiative' =>
                                0,
                        ]
                    );

                $call->update([
                    'status' =>
                        'accepted',

                    'responded_at' =>
                        now(),
                ]);

                $call->refresh();

                return response()->json([
                    'success' =>
                        true,

                    'call' =>
                        $this->payload(
                            $call->loadMissing([
                                'combat.campaign',
                            ])
                        ),
                ]);
            }
        );
    }


    public function initiative(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeOwner(
            $request,
            $character
        );

        $validated =
            $request->validate([
                'request_id' => [
                    'required',
                    'integer',
                ],

                /*
                 * Contrato atual:
                 * - die_roll = resultado natural do d20.
                 *
                 * Compatibilidade:
                 * - initiative = total final enviado por versões antigas
                 *   do Hero Quick Stats.
                 *
                 * O frontend atual envia ambos.
                 */
                'die_roll' => [
                    'nullable',
                    'required_without:initiative',
                    'integer',
                    'between:1,20',
                ],

                'initiative' => [
                    'nullable',
                    'required_without:die_roll',
                    'integer',
                    'between:-100,200',
                ],
            ]);

        return DB::transaction(
            function () use (
                $character,
                $validated
            ): JsonResponse {
                $call =
                    CombatParticipationRequest::query()
                        ->whereKey(
                            (int) $validated[
                                'request_id'
                            ]
                        )
                        ->where(
                            'character_id',
                            $character->id
                        )
                        ->with([
                            'combat',
                        ])
                        ->lockForUpdate()
                        ->firstOrFail();

                abort_unless(
                    $call->status
                    ===
                    'accepted',
                    422,
                    'Aceite o combate antes de rolar iniciativa.'
                );

                $combat =
                    $call->combat;

                abort_unless(
                    $combat
                    &&
                    $combat->is_active,
                    422,
                    'Este combate não está mais ativo.'
                );

                if (
                    $call->initiative
                    !==
                    null
                ) {
                    return response()->json([
                        'success' =>
                            true,

                        'die_roll' =>
                            null,

                        'modifier' =>
                            null,

                        'total' =>
                            (int) $call->initiative,

                        'already_submitted' =>
                            true,
                    ]);
                }

                $dieRoll =
                    array_key_exists(
                        'die_roll',
                        $validated
                    )
                    && $validated['die_roll'] !== null
                        ? (int) $validated['die_roll']
                        : null;

                $modifier =
                    $this->initiativeModifier(
                        $character
                    );

                $exhaustionPenalty =
                    $this->initiativeExhaustionPenalty(
                        $character
                    );

                /*
                 * Se o frontend enviou o total final, preservamos exatamente
                 * o valor apresentado ao Player. Isso mantém compatibilidade
                 * com dado físico, versões antigas e penalidade de Exaustão.
                 *
                 * Sem total final, calculamos no servidor.
                 */
                $total =
                    array_key_exists(
                        'initiative',
                        $validated
                    )
                    && $validated['initiative'] !== null
                        ? (int) $validated['initiative']
                        : (
                            (int) $dieRoll
                            +
                            $modifier
                            -
                            $exhaustionPenalty
                        );

                $combatPlayer =
                    CombatPlayer::query()
                        ->firstOrCreate(
                            [
                                'combat_id' =>
                                    $combat->id,

                                'character_id' =>
                                    $character->id,
                            ],
                            [
                                'name' =>
                                    $character->name,

                                'initiative' =>
                                    0,
                            ]
                        );

                $combatPlayer->forceFill([
                    'name' =>
                        $character->name,

                    'initiative' =>
                        $total,
                ])->save();

                $call->update([
                    'initiative' =>
                        $total,
                ]);

                $this->syncInitiativeToQuickStats(
                    $character,
                    $total
                );

                DB::table(
                    'combat_logs'
                )->insert([
                    'combat_id' =>
                        $combat->id,

                    'round' =>
                        (int) (
                            $combat->current_round
                            ?? 1
                        ),

                    'turn' =>
                        (int) (
                            $combat->current_turn
                            ?? 0
                        ),

                    'description' =>
                        "{$character->name} entrou no combate com iniciativa {$total}.",

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);

                return response()->json([
                    'success' =>
                        true,

                    'die_roll' =>
                        $dieRoll,

                    'modifier' =>
                        $modifier,

                    'exhaustion_penalty' =>
                        $exhaustionPenalty,

                    'total' =>
                        $total,
                ]);
            }
        );
    }


    private function authorizeOwner(
        Request $request,
        Character $character
    ): void {
        abort_unless(
            $request->user()
            &&
            (int) $character->user_id
                ===
                (int) $request->user()->id,
            403
        );
    }


    private function payload(
        CombatParticipationRequest $call
    ): array {
        $combat =
            $call->combat;

        $phase =
            $call->status
            ===
            'pending'
                ? 'join'
                : 'initiative';

        $secondsLeft =
            $phase === 'join'
                ? max(
                    0,
                    now()->diffInSeconds(
                        $call->expires_at,
                        false
                    )
                )
                : null;

        return [
            'request_id' =>
                (int) $call->id,

            'combat_id' =>
                (int) $call->combat_id,

            'combat_name' =>
                (string) (
                    $combat?->name
                    ?? 'Combate'
                ),

            'campaign_name' =>
                $combat?->campaign?->name
                    ? (string) $combat->campaign->name
                    : null,

            'phase' =>
                $phase,

            'seconds_left' =>
                $secondsLeft,
        ];
    }


    private function initiativeModifier(
        Character $character
    ): int {
        $character->loadMissing([
            'abilities',
            'combat',
        ]);

        $dexterity =
            (int) (
                $character->abilities?->dexterity
                ?? 10
            );

        $dexterityModifier =
            (int) floor(
                (
                    $dexterity
                    -
                    10
                )
                /
                2
            );

        $overrides =
            $this->normalizedOverrides(
                $character->combat?->overrides
                ?? []
            );

        $initiative =
            data_get(
                $overrides,
                'quick_stats.initiative',
                []
            );

        if (!is_array($initiative)) {
            $initiative =
                [];
        }

        /*
         * O bloco quick_stats.initiative pode existir sem a chave "mode".
         * Isso acontece em fichas antigas que possuem apenas bonus/combat_value.
         *
         * Antes, a validação usava ?? 'none', mas o ramo verdadeiro do ternário
         * voltava a acessar $initiative['mode'] diretamente, causando:
         *
         * Undefined array key "mode"
         *
         * Normalizamos o valor uma única vez e nunca acessamos a chave sem
         * fallback.
         */
        $initiativeMode =
            (string) (
                $initiative['mode']
                ?? 'none'
            );

        $mode =
            in_array(
                $initiativeMode,
                [
                    'none',
                    'proficient',
                    'expertise',
                ],
                true
            )
                ? $initiativeMode
                : 'none';

        $proficiencyBonus =
            max(
                0,
                (int) (
                    $character->proficiency_bonus
                    ?? $this->proficiencyBonusByLevel(
                        (int) (
                            $character->level
                            ?? 1
                        )
                    )
                )
            );

        $training =
            match ($mode) {
                'expertise' =>
                    $proficiencyBonus * 2,

                'proficient' =>
                    $proficiencyBonus,

                default =>
                    0,
            };

        return
            $dexterityModifier
            +
            $training
            +
            (int) (
                $initiative['bonus']
                ?? 0
            );
    }


    private function initiativeExhaustionPenalty(
        Character $character
    ): int {
        $character->loadMissing(
            'combat'
        );

        $sheetSettings =
            is_array(
                $character->sheet_settings
                ?? null
            )
                ? $character->sheet_settings
                : [];

        $ruleEnabled =
            (bool) data_get(
                $sheetSettings,
                'optional_rules.exhaustion',
                false
            );

        if (!$ruleEnabled) {
            return 0;
        }

        $level =
            max(
                0,
                min(
                    6,
                    (int) (
                        $character->combat
                            ?->exhaustion_level
                        ?? 0
                    )
                )
            );

        return $level * 2;
    }


    private function proficiencyBonusByLevel(
        int $level
    ): int {
        $level =
            max(
                1,
                $level
            );

        return match (true) {
            $level >= 17 =>
                6,

            $level >= 13 =>
                5,

            $level >= 9 =>
                4,

            $level >= 5 =>
                3,

            default =>
                2,
        };
    }


    private function syncInitiativeToQuickStats(
        Character $character,
        int $initiative
    ): void {
        $combat =
            $character->combat;

        if (!$combat) {
            return;
        }

        $overrides =
            $this->normalizedOverrides(
                $combat->overrides
                ?? []
            );

        Arr::set(
            $overrides,
            'quick_stats.initiative.combat_value',
            $initiative
        );

        $casts =
            method_exists(
                $combat,
                'getCasts'
            )
                ? $combat->getCasts()
                : [];

        $combat->overrides =
            array_key_exists(
                'overrides',
                $casts
            )
                ? $overrides
                : json_encode(
                    $overrides,
                    JSON_UNESCAPED_UNICODE
                    |
                    JSON_UNESCAPED_SLASHES
                );

        $combat->save();
    }


    private function normalizedOverrides(
        mixed $value
    ): array {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded =
                json_decode(
                    $value,
                    true
                );

            return is_array($decoded)
                ? $decoded
                : [];
        }

        return [];
    }
}