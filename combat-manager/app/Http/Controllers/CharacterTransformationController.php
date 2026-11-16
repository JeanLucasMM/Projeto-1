<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterTransformation;
use App\Models\CharacterTransformationState;
use App\Models\Npc;
use App\Services\Transformations\MonsterTransformationSheetResolver;
use App\Services\Characters\ConcentrationCheckService;
use App\Services\Characters\CharacterConcentrationManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CharacterTransformationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CRIAR TRANSFORMAÇÃO
    |--------------------------------------------------------------------------
    */
    public function store(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $this->ensureRuleEnabled(
            $character
        );

        $validated =
            $this->validateDefinition(
                $request
            );

        $normalized =
            $this->normalizeDefinition(
                $validated,
                $character
            );

        $imagePath =
            $this->storeUploadedImage(
                $request
            );

        try {
            $transformation =
                DB::transaction(
                    function () use (
                        $character,
                        $normalized,
                        $imagePath
                    ): CharacterTransformation {
                        $nextSortOrder =
                            (
                                (int) $character
                                    ->transformations()
                                    ->max('sort_order')
                            )
                            + 10;

                        $transformation =
                            CharacterTransformation::create([
                                ...$normalized,

                                'character_id' =>
                                    (int) $character->id,

                                'form_character_id' =>
                                    null,

                                'image_path' =>
                                    $imagePath,

                                'sort_order' =>
                                    max(
                                        0,
                                        $nextSortOrder
                                    ),

                                'settings' =>
                                    $normalized['settings'],
                            ]);

                        /*
                         * Nova Ficha nasce como uma cópia mecânica da ficha
                         * atual. Inventário, Grimório, Party, Diário e campanha
                         * permanecem no Character original.
                         */
                        if ($transformation->isCharacterSheet()) {
                            $this->ensureCharacterSheet(
                                $character,
                                $transformation
                            );
                        }

                        return $transformation
                            ->fresh([
                                'formCharacter',
                            ]);
                    }
                );

        } catch (\Throwable $exception) {
            if ($imagePath) {
                $this->deleteImage(
                    $imagePath
                );
            }

            throw $exception;
        }

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Transformação criada.',

            'transformation' =>
                $this->transformationPayload(
                    $transformation
                ),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR TRANSFORMAÇÃO
    |--------------------------------------------------------------------------
    */
    public function update(
        Request $request,
        Character $character,
        CharacterTransformation $transformation
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $this->ensureRuleEnabled(
            $character
        );

        $this->ensureTransformationBelongsToCharacter(
            $character,
            $transformation
        );

        $activeState =
            CharacterTransformationState::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();

        if (
            $activeState
            && (int) $activeState->character_transformation_id
                === (int) $transformation->id
        ) {
            throw ValidationException::withMessages([
                'transformation' =>
                    'Destransforme antes de editar a forma que está ativa.',
            ]);
        }

        $validated =
            $this->validateDefinition(
                $request,
                true
            );

        $normalized =
            $this->normalizeDefinition(
                $validated,
                $character
            );

        $normalized['settings'] = array_replace(
            is_array($transformation->settings) ? $transformation->settings : [],
            $normalized['settings']
        );


        /*
        |--------------------------------------------------------------------------
        | Proteção da futura Nova Ficha
        |--------------------------------------------------------------------------
        |
        | Depois que uma Nova Ficha possuir Character mecânico próprio,
        | não permitimos transformá-la em Ficha de Monstro apenas editando
        | a definição. Isso evita deixar uma ficha auxiliar órfã.
        |
        */
        if (
            $transformation->form_character_id !== null
            && $normalized['type']
                !== CharacterTransformation::TYPE_CHARACTER_SHEET
        ) {
            throw ValidationException::withMessages([
                'type' =>
                    'Uma Nova Ficha já configurada não pode ser convertida em Ficha de Monstro.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | USOS ATUAIS
        |--------------------------------------------------------------------------
        |
        | Ao editar o máximo, preservamos a quantidade que já foi gasta.
        |
        | Exemplo:
        | 3 máximos / 1 atual = 2 gastos
        | novo máximo 5       = 3 atuais
        |
        */
        $newUsesMax =
            $normalized['uses_max'];

        $newUsesCurrent =
            null;

        if ($newUsesMax !== null) {
            $oldUsesMax =
                $transformation->uses_max;

            if ($oldUsesMax === null) {
                $newUsesCurrent =
                    $newUsesMax;

            } else {
                $oldUsesCurrent =
                    $transformation->uses_current !== null
                        ? max(
                            0,
                            (int) $transformation->uses_current
                        )
                        : max(
                            0,
                            (int) $oldUsesMax
                        );

                $spent =
                    max(
                        0,
                        (int) $oldUsesMax
                        - $oldUsesCurrent
                    );

                $newUsesCurrent =
                    max(
                        0,
                        $newUsesMax
                        - $spent
                    );
            }
        }

        $normalized['uses_current'] =
            $newUsesCurrent;


        /*
        |--------------------------------------------------------------------------
        | FOTO
        |--------------------------------------------------------------------------
        */
        $oldImagePath =
            $transformation->image_path;

        $newImagePath =
            $this->storeUploadedImage(
                $request
            );

        $removeImage =
            $request->boolean(
                'remove_image'
            );

        $finalImagePath =
            $newImagePath
                ?? (
                    $removeImage
                        ? null
                        : $oldImagePath
                );

        try {
            DB::transaction(
                function () use (
                    $character,
                    $transformation,
                    $normalized,
                    $finalImagePath
                ): void {
                    $transformation->fill([
                        ...$normalized,

                        'image_path' =>
                            $finalImagePath,
                    ]);

                    $transformation->save();

                    if (
                        $transformation->isCharacterSheet()
                        && $transformation->form_character_id === null
                    ) {
                        $this->ensureCharacterSheet(
                            $character,
                            $transformation
                        );
                    }
                }
            );

        } catch (\Throwable $exception) {
            if ($newImagePath) {
                $this->deleteImage(
                    $newImagePath
                );
            }

            throw $exception;
        }


        /*
         * A imagem antiga só é apagada depois que o update foi confirmado.
         */
        if (
            $oldImagePath
            && $oldImagePath !== $finalImagePath
        ) {
            $this->deleteImage(
                $oldImagePath
            );
        }

        $transformation->refresh();

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Transformação atualizada.',

            'transformation' =>
                $this->transformationPayload(
                    $transformation
                ),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARAR / ABRIR NOVA FICHA
    |--------------------------------------------------------------------------
    */
    public function prepareCharacterSheet(
        Request $request,
        Character $character,
        CharacterTransformation $transformation
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $this->ensureRuleEnabled(
            $character
        );

        $this->ensureTransformationBelongsToCharacter(
            $character,
            $transformation
        );

        if (!$transformation->isCharacterSheet()) {
            throw ValidationException::withMessages([
                'transformation' =>
                    'Somente uma transformação do tipo Nova Ficha possui ficha mecânica própria.',
            ]);
        }

        $formCharacter =
            DB::transaction(
                function () use (
                    $character,
                    $transformation
                ): Character {
                    $lockedTransformation =
                        CharacterTransformation::query()
                            ->whereKey(
                                $transformation->id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();

                    return $this->ensureCharacterSheet(
                        $character,
                        $lockedTransformation
                    );
                }
            );

        $transformation->refresh();

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Ficha mecânica pronta.',

            'form_character_id' =>
                (int) $formCharacter->id,

            'edit_url' =>
                route(
                    'characters.show',
                    $character
                )
                . '?transformation_sheet='
                . (int) $transformation->id,

            'transformation' =>
                $this->transformationPayload(
                    $transformation
                ),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | EXCLUIR TRANSFORMAÇÃO
    |--------------------------------------------------------------------------
    */
    public function destroy(
        Request $request,
        Character $character,
        CharacterTransformation $transformation
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $this->ensureRuleEnabled(
            $character
        );

        $this->ensureTransformationBelongsToCharacter(
            $character,
            $transformation
        );

        $activeState =
            CharacterTransformationState::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'character_transformation_id',
                    $transformation->id
                )
                ->first();

        if ($activeState) {
            throw ValidationException::withMessages([
                'transformation' =>
                    'Volte à Forma Normal antes de excluir a transformação ativa.',
            ]);
        }

        $imagePath =
            $transformation->image_path;

        $formCharacterId =
            $transformation->form_character_id;

        DB::transaction(
            function () use (
                $character,
                $transformation,
                $formCharacterId
            ): void {
                $lockedTransformation =
                    CharacterTransformation::query()
                        ->whereKey(
                            $transformation->id
                        )
                        ->lockForUpdate()
                        ->first();

                if (!$lockedTransformation) {
                    return;
                }

                $lockedTransformation->delete();

                /*
                 * Nova Ficha usa um Character auxiliar exclusivamente mecânico.
                 * Se ele realmente estiver marcado como forma auxiliar, removemos
                 * junto para não deixar registro órfão.
                 */
                if ($formCharacterId !== null) {
                    $formCharacter =
                        Character::query()
                            ->whereKey(
                                $formCharacterId
                            )
                            ->where(
                                'user_id',
                                $character->user_id
                            )
                            ->where(
                                'is_transformation_form',
                                true
                            )
                            ->lockForUpdate()
                            ->first();

                    if ($formCharacter) {
                        $formCharacter->delete();
                    }
                }
            }
        );

        if ($imagePath) {
            $this->deleteImage(
                $imagePath
            );
        }

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Transformação excluída.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSFORMAR
    |--------------------------------------------------------------------------
    |
    | Nesta etapa o botão já controla o estado ativo da transformação.
    |
    | A troca física dos módulos mecânicos da ficha será conectada sobre
    | este estado. Portanto o estado já é persistente e não é apenas visual.
    |
    */
    public function activate(
        Request $request,
        Character $character,
        CharacterTransformation $transformation
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $this->ensureRuleEnabled(
            $character
        );

        $this->ensureTransformationBelongsToCharacter(
            $character,
            $transformation
        );

        $activationExhaustion =
            null;

        $confirmedReplacement = $request->boolean('confirm_replace_concentration');
        $requestedDuration = $request->input('duration_total');
        $durationRollMode = $request->input('duration_roll_mode', 'manual');
        if (!in_array($durationRollMode, ['manual', 'automatic'], true)) {
            throw ValidationException::withMessages(['duration_roll_mode' => 'Modo inválido.']);
        }

        DB::transaction(
            function () use (
                $character,
                $transformation,
                &$activationExhaustion,
                $confirmedReplacement,
                $requestedDuration,
                $durationRollMode
            ): void {
                // Serializa conjuração e transformação na ficha original.
                app(CharacterConcentrationManager::class)->lockCharacter($character);

                $lockedTransformation =
                    CharacterTransformation::query()
                        ->whereKey(
                            $transformation->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $linkedSpellId = $lockedTransformation->casting_time === 'spell'
                    ? (int) data_get($lockedTransformation->settings, 'linked_spell_id', 0)
                    : 0;
                $linkedToSpell = $linkedSpellId > 0;
                if ($lockedTransformation->casting_time === 'spell' && !$linkedToSpell) {
                    throw ValidationException::withMessages(['linked_spell_id' => 'Esta transformação precisa de um feitiço vinculado.']);
                }
                if ($linkedToSpell) {
                    $current = app(CharacterConcentrationManager::class)->currentConcentration($character);
                    if (($current['source'] ?? null) !== 'spell'
                        || (int) ($current['id'] ?? 0) !== $linkedSpellId) {
                        throw ValidationException::withMessages([
                            'concentration' => 'Conjure primeiro o feitiço vinculado e mantenha sua concentração.',
                        ]);
                    }
                }

                $state =
                    CharacterTransformationState::query()
                        ->where(
                            'character_id',
                            $character->id
                        )
                        ->lockForUpdate()
                        ->first();

                /*
                 * Clicar novamente na forma já ativa não consome outro uso.
                 */
                if (
                    $state
                    && $state->is_active
                    && (int) $state->character_transformation_id
                        === (int) $lockedTransformation->id
                ) {
                    return;
                }

                if (
                    $state
                    && $state->is_active
                    && $state->character_transformation_id !== null
                ) {
                    throw ValidationException::withMessages([
                        'transformation' =>
                            'Volte à Forma Normal antes de assumir outra transformação.',
                    ]);
                }

                // O usuário deve consentir com a interrupção do feitiço anterior.
                // Verificado após o lock e antes de consumir usos ou aplicar exaustão.
                if ($lockedTransformation->requires_concentration
                    && !$linkedToSpell
                    && !$confirmedReplacement
                    && app(CharacterConcentrationManager::class)
                        ->hasActiveConcentration($character)
                ) {
                    throw ValidationException::withMessages([
                        'concentration' => 'Você já mantém uma concentração. Confirme a substituição antes de transformar.',
                    ]);
                }

                /*
                |------------------------------------------------------------------
                | A FORMA PRECISA ESTAR MECANICAMENTE PRONTA
                |------------------------------------------------------------------
                */
                if ($lockedTransformation->isCharacterSheet()) {
                    /*
                     * Compatibilidade com Nova Ficha criada antes desta versão.
                     */
                    if ($lockedTransformation->form_character_id === null) {
                        $this->ensureCharacterSheet(
                            $character,
                            $lockedTransformation
                        );

                        $lockedTransformation->refresh();
                    }

                    if ($lockedTransformation->form_character_id === null) {
                        throw ValidationException::withMessages([
                            'transformation' =>
                                'Não foi possível preparar a ficha mecânica desta transformação.',
                        ]);
                    }
                }

                $monsterState =
                    null;

                if ($lockedTransformation->isMonsterSheet()) {
                    $sourceNpc =
                        $lockedTransformation
                            ->sourceNpc()
                            ->first();

                    if (!$sourceNpc) {
                        throw ValidationException::withMessages([
                            'source_npc_id' =>
                                'A criatura vinculada a esta transformação não existe mais no Cofre.',
                        ]);
                    }

                    $this->ensureMonsterNpcSupported(
                        $sourceNpc
                    );

                    $monsterMaxHp =
                        $this->monsterTemplateMaxHp(
                            $sourceNpc
                        );

                    /*
                     * Este é o estado mutável da cópia transformada.
                     * O NPC do Cofre nunca é alterado.
                     */
                    $monsterState = [
                        'source_npc_id' =>
                            (int) $sourceNpc->id,

                        'current_hp' =>
                            $monsterMaxHp,

                        'max_hp' =>
                            $monsterMaxHp,

                        'temporary_hp' =>
                            0,

                        'initialized_at' =>
                            now()->toIso8601String(),
                    ];
                }

                /*
                |------------------------------------------------------------------
                | GASTO DE USO
                |------------------------------------------------------------------
                */
                if ($lockedTransformation->uses_max !== null) {
                    $currentUses =
                        $lockedTransformation->uses_current !== null
                            ? max(
                                0,
                                (int) $lockedTransformation->uses_current
                            )
                            : max(
                                0,
                                (int) $lockedTransformation->uses_max
                            );

                    if ($currentUses < 1) {
                        throw ValidationException::withMessages([
                            'uses' =>
                                'Esta transformação não possui usos disponíveis.',
                        ]);
                    }

                    $lockedTransformation->uses_current =
                        $currentUses - 1;

                    $lockedTransformation->save();
                }

                // O estado persistido conta RODADAS, inclusive para minutos e horas.
                // 1 minuto = 10 rodadas; 1 hora = 600 rodadas (6 segundos/rodada).
                $durationMultiplier = match ((string) $lockedTransformation->duration_type) {
                    'rounds' => 1,
                    'minutes' => 10,
                    'hours' => 600,
                    default => 0,
                };
                $durationRemaining = $durationMultiplier > 0
                    ? min(2000000, max(1, (int) $lockedTransformation->duration_value) * $durationMultiplier)
                    : null;
                $durationFormula = (string) data_get($lockedTransformation->settings, 'duration_formula', '');
                if ($durationFormula !== '') {
                    $range = $this->durationDiceRange($durationFormula);
                    if ($range === null || filter_var($requestedDuration, FILTER_VALIDATE_INT) === false) {
                        throw ValidationException::withMessages([
                            'duration_total' => 'Role os dados de duração ou informe um resultado válido antes de transformar.',
                        ]);
                    }
                    $rolled = (int) $requestedDuration;
                    if ($rolled < $range['min'] || $rolled > $range['max']) {
                        throw ValidationException::withMessages([
                            'duration_total' => 'O resultado está fora dos limites da fórmula.',
                        ]);
                    }
                    $durationRemaining = $rolled;
                }

                $encounter = DB::table('combat_players as cp')
                    ->join('combats as c', 'c.id', '=', 'cp.combat_id')
                    ->where('cp.character_id', $character->id)
                    ->where('c.is_active', true)
                    ->orderByDesc('c.id')
                    ->first(['c.id', 'c.current_round', 'c.current_turn']);

                $runtimeState = [
                    'activation' =>
                        $linkedToSpell ? 'spell' : 'manual',
                    'linked_spell_id' => $linkedToSpell ? $linkedSpellId : null,
                    'concentration_source' => $linkedToSpell ? 'spell' : 'transformation',

                    'requires_concentration' =>
                        (bool) $lockedTransformation
                            ->requires_concentration,

                    'duration_unit' => 'rounds',
                    'duration_total_rounds' => $durationRemaining,
                    'duration_formula' => $durationFormula !== '' ? $durationFormula : null,
                    'duration_roll_mode' => $durationFormula !== '' ? $durationRollMode : null,

                    /*
                     * Regra estabelecida para Ficha de Monstro:
                     * dano excedente da forma volta para o Character original.
                     */
                    'overflow_damage_to_character' =>
                        $lockedTransformation->isMonsterSheet(),
                ];

                $activationExhaustion =
                    $this->applyTransformationExhaustion(
                        $character,
                        $lockedTransformation,
                        CharacterTransformation::EXHAUSTION_ON_ACTIVATION
                    );

                $runtimeState['activation_exhaustion'] =
                    $activationExhaustion;


                // Antes de tornar a forma ativa, encerre a concentração anterior,
                // inclusive quando ela pertence a uma Habilidade temporária.
                if ($lockedTransformation->requires_concentration && !$linkedToSpell) {
                    app(CharacterConcentrationManager::class)->replaceForConcentration(
                        $character, 'transformation', (int) $lockedTransformation->id
                    );
                }

                $payload = [
                    'character_transformation_id' =>
                        (int) $lockedTransformation->id,

                    'is_active' =>
                        true,

                    'activated_at' =>
                        now(),

                    'combat_id' =>
                        $encounter?->id,

                    'activated_round' =>
                        $encounter?->current_round,

                    'activated_turn' =>
                        $encounter?->current_turn,

                    'duration_remaining' =>
                        $durationRemaining,

                    'monster_state' =>
                        $monsterState,

                    'runtime_state' =>
                        $runtimeState,
                ];

                if ($state) {
                    $state->fill(
                        $payload
                    );

                    $state->save();

                } else {
                    CharacterTransformationState::create([
                        'character_id' =>
                            (int) $character->id,

                        ...$payload,
                    ]);
                }

                // Concentração da forma substitui a do feitiço, atomicamente.
                // Formas que não exigem concentração preservam o feitiço atual.
                if (!$linkedToSpell) {
                    app(CharacterConcentrationManager::class)
                        ->startTransformation($character, $lockedTransformation);
                }
            }
        );

        $transformation->refresh();

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Transformação ativada.',

            'transformation' =>
                $this->transformationPayload(
                    $transformation
                ),

            'exhaustion' =>
                $activationExhaustion,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VOLTAR À FORMA NORMAL
    |--------------------------------------------------------------------------
    */
    public function deactivate(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        // A regra pode ter sido desligada enquanto a forma estava ativa.
        // Nunca impedir o retorno seguro à Forma Normal.

        $endExhaustion =
            null;

        DB::transaction(
            function () use (
                $character,
                &$endExhaustion
            ): void {
                app(CharacterConcentrationManager::class)->lockCharacter($character);
                $state =
                    CharacterTransformationState::query()
                        ->where(
                            'character_id',
                            $character->id
                        )
                        ->lockForUpdate()
                        ->first();

                if (
                    !$state
                    || !$state->is_active
                ) {
                    return;
                }

                $activeTransformation =
                    $state->character_transformation_id !== null
                        ? CharacterTransformation::query()
                            ->whereKey(
                                $state->character_transformation_id
                            )
                            ->lockForUpdate()
                            ->first()
                        : null;

                $endExhaustion = app(CharacterConcentrationManager::class)
                    ->endTransformation($character, $state, $activeTransformation);
            }
        );

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Forma Normal restaurada.',

            'exhaustion' =>
                $endExhaustion,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CONCENTRAÇÃO — TRANSFORMAÇÃO ATIVA
    |--------------------------------------------------------------------------
    |
    | A vida da "Nova Ficha" é persistida pelo fluxo normal de Character.
    | Quando o Hero informa que houve dano, este endpoint:
    |
    | - confirma que a transformação ainda está ativa;
    | - confirma que ela exige concentração;
    | - calcula a salvaguarda de Constituição da própria ficha mecânica;
    | - reutiliza ConcentrationCheckService.
    |--------------------------------------------------------------------------
    */
    public function concentrationCheck(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $validated =
            $request->validate([
                'damage' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:1000000',
                ],
                'roll_total' => ['nullable', 'integer', 'min:-1000', 'max:1000'],
                'transformation_id' => ['nullable', 'integer', 'min:1'],
            ]);

        $state =
            CharacterTransformationState::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->with(
                    'transformation'
                )
                ->first();

        $transformation =
            $state?->transformation;

        if (
            !$transformation
            || !$transformation->requires_concentration
        ) {
            throw ValidationException::withMessages([
                'concentration' =>
                    'A transformação ativa não exige concentração.',
            ]);
        }

        $check =
            $this->buildTransformationConcentrationCheck(
                $character,
                $transformation,
                (int) $validated['damage']
            );

        if (!$check) {
            throw ValidationException::withMessages([
                'concentration' =>
                    'Não foi possível calcular o teste de concentração desta forma.',
            ]);
        }

        if (array_key_exists('roll_total', $validated) && $validated['roll_total'] !== null) {
            // Um teste antigo não pode encerrar uma forma que já foi trocada.
            if ((int) ($validated['transformation_id'] ?? 0) !== (int) $transformation->id) {
                throw ValidationException::withMessages([
                    'transformation_id' => 'A transformação mudou antes da resolução do teste.',
                ]);
            }
            // O vínculo de feitiço é a fonte única da concentração.
            $linkedSpellId = $transformation->casting_time === 'spell'
                ? (int) data_get($transformation->settings, 'linked_spell_id', 0)
                : 0;
            $result = $linkedSpellId > 0
                ? app(CharacterConcentrationManager::class)->resolveSpell(
                    $character, $linkedSpellId, (int) $validated['damage'],
                    (int) $validated['roll_total']
                )
                : app(CharacterConcentrationManager::class)->resolveTransformation(
                    $character, (int) $transformation->id,
                    (int) $validated['damage'], (int) $validated['roll_total']
                );
            return response()->json([
                'success' => true,
                'resolved' => true,
                'result' => $result,
            ]);
        }

        return response()->json([
            'success' => true,
            'concentration_check' => $check,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FICHA DE MONSTRO — DANO
    |--------------------------------------------------------------------------
    |
    | O dano é aplicado à cópia temporária do monstro.
    |
    | Quando a forma chega a 0 PV:
    | - a transformação termina automaticamente;
    | - qualquer dano excedente retorna à ficha original;
    | - os PV temporários do Character original absorvem esse excedente antes
    |   dos PV normais, preservando a regra normal da ficha.
    |
    */
    public function damageMonster(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $this->ensureRuleEnabled(
            $character
        );

        $validated =
            $request->validate([
                'damage' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:1000000',
                ],
            ]);

        $result =
            DB::transaction(
                function () use (
                    $character,
                    $validated
                ): array {
                    app(CharacterConcentrationManager::class)->lockCharacter($character);
                    $state =
                        CharacterTransformationState::query()
                            ->where(
                                'character_id',
                                $character->id
                            )
                            ->lockForUpdate()
                            ->first();

                    $transformation =
                        $this->activeMonsterTransformation(
                            $state
                        );

                    $monsterState =
                        $this->normalizedMonsterState(
                            $state,
                            $transformation
                        );

                    $damage =
                        max(
                            1,
                            (int) $validated['damage']
                        );

                    $temporaryHp =
                        max(
                            0,
                            (int) (
                                $monsterState['temporary_hp']
                                ?? 0
                            )
                        );

                    $currentHp =
                        max(
                            0,
                            (int) (
                                $monsterState['current_hp']
                                ?? 0
                            )
                        );

                    $remainingDamage =
                        $damage;

                    $absorbedByTemp =
                        min(
                            $temporaryHp,
                            $remainingDamage
                        );

                    $temporaryHp -=
                        $absorbedByTemp;

                    $remainingDamage -=
                        $absorbedByTemp;

                    $absorbedByForm =
                        min(
                            $currentHp,
                            $remainingDamage
                        );

                    $currentHp -=
                        $absorbedByForm;

                    $remainingDamage -=
                        $absorbedByForm;

                    $overflowDamage =
                        max(
                            0,
                            $remainingDamage
                        );

                    $monsterState['temporary_hp'] =
                        $temporaryHp;

                    $monsterState['current_hp'] =
                        $currentHp;

                    $state->monster_state =
                        $monsterState;

                    $deactivated =
                        $currentHp <= 0;

                    $characterCombatPayload =
                        null;

                    $endExhaustion =
                        null;

                    $concentrationCheck =
                        null;

                    if ($deactivated) {
                        if ($overflowDamage > 0) {
                            $combat =
                                $character
                                    ->combat()
                                    ->lockForUpdate()
                                    ->first();

                            if (!$combat) {
                                throw ValidationException::withMessages([
                                    'combat' =>
                                        'A ficha original não possui dados de combate para receber o dano excedente.',
                                ]);
                            }

                            $characterTemporaryHp =
                                max(
                                    0,
                                    (int) (
                                        $combat->temporary_hp
                                        ?? 0
                                    )
                                );

                            $characterCurrentHp =
                                max(
                                    0,
                                    (int) (
                                        $combat->current_hp
                                        ?? 0
                                    )
                                );

                            $remainingOverflow =
                                $overflowDamage;

                            $absorbedByCharacterTemp =
                                min(
                                    $characterTemporaryHp,
                                    $remainingOverflow
                                );

                            $characterTemporaryHp -=
                                $absorbedByCharacterTemp;

                            $remainingOverflow -=
                                $absorbedByCharacterTemp;

                            $characterCurrentHp =
                                max(
                                    0,
                                    $characterCurrentHp
                                    - $remainingOverflow
                                );

                            $combat->temporary_hp =
                                $characterTemporaryHp;

                            $combat->current_hp =
                                $characterCurrentHp;

                            $combat->save();

                            $characterCombatPayload = [
                                'current_hp' =>
                                    $characterCurrentHp,

                                'temporary_hp' =>
                                    $characterTemporaryHp,

                                'max_hp' =>
                                    max(
                                        1,
                                        (int) (
                                            $combat->max_hp
                                            ?? 1
                                        )
                                    ),
                            ];
                        }

                        $endExhaustion = app(CharacterConcentrationManager::class)
                            ->endTransformation($character, $state, $transformation);

                    } else {
                        $state->save();

                        if (
                            $transformation->requires_concentration
                        ) {
                            $concentrationCheck =
                                $this->buildTransformationConcentrationCheck(
                                    $character,
                                    $transformation,
                                    $damage
                                );
                        }
                    }

                    return [
                        'damage' =>
                            $damage,

                        'monster_state' =>
                            $monsterState,

                        'overflow_damage' =>
                            $overflowDamage,

                        'deactivated' =>
                            $deactivated,

                        'character_combat' =>
                            $characterCombatPayload,

                        'concentration_check' =>
                            $concentrationCheck,

                        'exhaustion' =>
                            $endExhaustion,
                    ];
                }
            );

        return response()->json([
            'success' =>
                true,

            'message' =>
                $result['deactivated']
                    ? (
                        $result['overflow_damage'] > 0
                            ? 'A forma chegou a 0 PV. O dano excedente retornou à ficha original.'
                            : 'A forma chegou a 0 PV e foi encerrada.'
                    )
                    : 'Dano aplicado à forma.',

            ...$result,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FICHA DE MONSTRO — CURA
    |--------------------------------------------------------------------------
    */
    public function healMonster(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $this->ensureRuleEnabled(
            $character
        );

        $validated =
            $request->validate([
                'healing' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:1000000',
                ],
            ]);

        $monsterState =
            DB::transaction(
                function () use (
                    $character,
                    $validated
                ): array {
                    $state =
                        CharacterTransformationState::query()
                            ->where(
                                'character_id',
                                $character->id
                            )
                            ->lockForUpdate()
                            ->first();

                    $transformation =
                        $this->activeMonsterTransformation(
                            $state
                        );

                    $monsterState =
                        $this->normalizedMonsterState(
                            $state,
                            $transformation
                        );

                    $maxHp =
                        max(
                            1,
                            (int) (
                                $monsterState['max_hp']
                                ?? 1
                            )
                        );

                    $currentHp =
                        max(
                            0,
                            min(
                                $maxHp,
                                (int) (
                                    $monsterState['current_hp']
                                    ?? $maxHp
                                )
                            )
                        );

                    $currentHp =
                        min(
                            $maxHp,
                            $currentHp
                            + max(
                                1,
                                (int) $validated['healing']
                            )
                        );

                    $monsterState['current_hp'] =
                        $currentHp;

                    $state->monster_state =
                        $monsterState;

                    $state->save();

                    return $monsterState;
                }
            );

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Cura aplicada à forma.',

            'monster_state' =>
                $monsterState,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS DE RUNTIME
    |--------------------------------------------------------------------------
    */
    private function activeMonsterTransformation(
        ?CharacterTransformationState $state
    ): CharacterTransformation {
        if (
            !$state
            || !$state->is_active
            || $state->character_transformation_id === null
        ) {
            throw ValidationException::withMessages([
                'transformation' =>
                    'Nenhuma transformação está ativa.',
            ]);
        }

        $transformation =
            CharacterTransformation::query()
                ->whereKey(
                    $state->character_transformation_id
                )
                ->lockForUpdate()
                ->first();

        if (
            !$transformation
            || !$transformation->isMonsterSheet()
        ) {
            throw ValidationException::withMessages([
                'transformation' =>
                    'A transformação ativa não é uma Ficha de Monstro.',
            ]);
        }

        return $transformation;
    }


    private function normalizedMonsterState(
        CharacterTransformationState $state,
        CharacterTransformation $transformation
    ): array {
        $monsterState =
            is_array(
                $state->monster_state
            )
                ? $state->monster_state
                : [];

        $sourceNpc =
            $transformation
                ->sourceNpc()
                ->first();

        if (!$sourceNpc) {
            throw ValidationException::withMessages([
                'source_npc_id' =>
                    'A criatura vinculada a esta transformação não existe mais no Cofre.',
            ]);
        }

        $this->ensureMonsterNpcSupported(
            $sourceNpc
        );

        $templateMaxHp =
            $this->monsterTemplateMaxHp(
                $sourceNpc
            );

        $storedMaxHp =
            isset(
                $monsterState['max_hp']
            )
                ? (int) $monsterState['max_hp']
                : null;

        /*
         * Estados antigos criados antes do suporte dual-format podiam ter
         * sido inicializados como 1 PV quando o NPC era Native. Nesse caso
         * recuperamos o valor real do template sem tocar no NPC do Cofre.
         */
        $maxHp =
            max(
                1,
                (
                    $storedMaxHp !== null
                    && $storedMaxHp > 1
                )
                    ? $storedMaxHp
                    : $templateMaxHp
            );

        $currentHp =
            max(
                0,
                min(
                    $maxHp,
                    (int) (
                        $monsterState['current_hp']
                        ?? $maxHp
                    )
                )
            );

        return [
            ...$monsterState,

            'source_npc_id' =>
                (int) $sourceNpc->id,

            'current_hp' =>
                $currentHp,

            'max_hp' =>
                $maxHp,

            'temporary_hp' =>
                max(
                    0,
                    (int) (
                        $monsterState['temporary_hp']
                        ?? 0
                    )
                ),
        ];
    }


    private function clearActiveState(
        CharacterTransformationState $state
    ): void {
        $state->fill([
            'character_transformation_id' =>
                null,

            'is_active' =>
                false,

            'activated_at' =>
                null,

            'combat_id' =>
                null,

            'activated_round' =>
                null,

            'activated_turn' =>
                null,

            'duration_remaining' =>
                null,

            'monster_state' =>
                null,

            'runtime_state' =>
                null,
        ]);

        $state->save();
    }


    /** Limites de fórmulas simples; usado no cadastro e na ativação. */
    private function durationDiceRange(string $formula): ?array
    {
        $formula = preg_replace('/\s+/', '', strtolower($formula));
        if (!preg_match('/^(\d{1,2})d(4|6|8|10|12|20|100)([+-]\d{1,3})?$/', $formula, $matches)) {
            return null;
        }
        $count = (int) $matches[1];
        $sides = (int) $matches[2];
        $modifier = isset($matches[3]) ? (int) $matches[3] : 0;
        if ($count < 1 || $count > 12 || $modifier < -100 || $modifier > 100) return null;
        $min = $count + $modifier;
        $max = $count * $sides + $modifier;
        if ($min < 1 || $max > 10000) return null;
        return ['min' => $min, 'max' => $max];
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DA DEFINIÇÃO
    |--------------------------------------------------------------------------
    */
    private function validateDefinition(
        Request $request,
        bool $editing = false
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'type' => [
                'required',
                Rule::in([
                    CharacterTransformation::TYPE_CHARACTER_SHEET,
                    CharacterTransformation::TYPE_MONSTER_SHEET,
                ]),
            ],

            'source_npc_id' => [
                'nullable',
                'integer',
            ],

            'linked_spell_id' => ['nullable', 'integer', 'min:1'],

            'casting_time' => [
                'required',
                Rule::in([
                    CharacterTransformation::CASTING_NONE,
                    CharacterTransformation::CASTING_SPELL,
                    CharacterTransformation::CASTING_BONUS_ACTION,
                    CharacterTransformation::CASTING_ACTION,
                ]),
            ],

            'requires_concentration' => [
                'required',
                'boolean',
            ],

            'duration_formula' => ['nullable', 'string', 'max:16'],
            'duration_type' => [
                'required',
                Rule::in([
                    'dice',
                    CharacterTransformation::DURATION_INDEFINITE,
                    CharacterTransformation::DURATION_ROUNDS,
                    CharacterTransformation::DURATION_MINUTES,
                    CharacterTransformation::DURATION_HOURS,
                ]),
            ],

            'duration_value' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000000',
            ],

            'is_special' => [
                'required',
                'boolean',
            ],

            'special_trigger' => [
                'nullable',
                Rule::in([
                    CharacterTransformation::TRIGGER_HALF_HP,
                    CharacterTransformation::TRIGGER_ONE_OR_ZERO_HP,
                ]),
            ],

            'has_limited_uses' => [
                'required',
                'boolean',
            ],

            'uses_max' => [
                'nullable',
                'integer',
                'min:1',
                'max:999',
            ],

            'recovery' => [
                'nullable',
                Rule::in([
                    CharacterTransformation::RECOVERY_DAILY,
                    CharacterTransformation::RECOVERY_SHORT_REST,
                ]),
            ],

            'applies_exhaustion' => [
                'required',
                'boolean',
            ],

            'exhaustion_levels' => [
                'nullable',
                'integer',
                'min:1',
                'max:6',
            ],

            'exhaustion_timing' => [
                'nullable',
                Rule::in([
                    CharacterTransformation::EXHAUSTION_ON_ACTIVATION,
                    CharacterTransformation::EXHAUSTION_ON_END,
                ]),
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'remove_image' => [
                'nullable',
                'boolean',
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAÇÃO DA DEFINIÇÃO
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | NOVA FICHA — CHARACTER AUXILIAR
    |--------------------------------------------------------------------------
    |
    | A ficha auxiliar persiste apenas a mecânica própria da forma.
    |
    | Copiado:
    | - classes;
    | - atributos;
    | - perícias;
    | - salvaguardas;
    | - habilidades / traços / talentos;
    | - recursos;
    | - combate;
    | - ataques personalizados.
    |
    | Compartilhado pelo Character original:
    | - inventário;
    | - feitiços / slots;
    | - Party;
    | - Diário;
    | - campanhas.
    |--------------------------------------------------------------------------
    */
    private function ensureCharacterSheet(
        Character $sourceCharacter,
        CharacterTransformation $transformation
    ): Character {
        if (!$transformation->isCharacterSheet()) {
            throw ValidationException::withMessages([
                'transformation' =>
                    'A transformação selecionada não utiliza Nova Ficha.',
            ]);
        }

        if ($transformation->form_character_id !== null) {
            $existing =
                Character::query()
                    ->whereKey(
                        $transformation->form_character_id
                    )
                    ->where(
                        'user_id',
                        $sourceCharacter->user_id
                    )
                    ->where(
                        'is_transformation_form',
                        true
                    )
                    ->first();

            if ($existing) {
                return $existing;
            }

            $transformation->form_character_id =
                null;

            $transformation->save();
        }

        $sourceCharacter->loadMissing([
            'classes',
            'abilities',
            'skills',
            'savingThrows',
            'features',
            'resources',
            'combat',
            'attacks',
        ]);

        /*
         * replicate() mantém campos do Character que possam ter sido
         * acrescentados ao projeto sem obrigar esta rotina a conhecer cada
         * coluna, mas remove os campos que precisam ser próprios da forma.
         */
        $formCharacter =
            $sourceCharacter->replicate([
                'id',
                'image_path',
                'sheet_settings',
                'is_transformation_form',
                'created_at',
                'updated_at',
            ]);

        $formCharacter->image_path =
            null;

        $formCharacter->is_transformation_form =
            true;

        $formCharacter->sheet_settings =
            $this->transformationSheetSettings(
                $sourceCharacter
            );

        $formCharacter->save();

        $this->cloneMechanicalCollection(
            $sourceCharacter->classes,
            $formCharacter->classes()
        );

        $this->cloneMechanicalCollection(
            $sourceCharacter->skills,
            $formCharacter->skills()
        );

        $this->cloneMechanicalCollection(
            $sourceCharacter->savingThrows,
            $formCharacter->savingThrows()
        );

        $this->cloneMechanicalCollection(
            $sourceCharacter->features,
            $formCharacter->features()
        );

        $this->cloneMechanicalCollection(
            $sourceCharacter->resources,
            $formCharacter->resources()
        );

        $this->cloneMechanicalCollection(
            $sourceCharacter->attacks,
            $formCharacter->attacks()
        );

        $this->cloneMechanicalSingle(
            $sourceCharacter->abilities,
            $formCharacter->abilities()
        );

        $this->cloneMechanicalSingle(
            $sourceCharacter->combat,
            $formCharacter->combat()
        );

        $transformation->form_character_id =
            (int) $formCharacter->id;

        $transformation->save();

        return $formCharacter;
    }


    private function cloneMechanicalCollection(
        iterable $models,
        $relation
    ): void {
        foreach ($models as $model) {
            if (!$model) {
                continue;
            }

            $copy =
                $model->replicate([
                    'id',
                    'character_id',
                    'created_at',
                    'updated_at',
                ]);

            $relation->save(
                $copy
            );
        }
    }


    private function cloneMechanicalSingle(
        $model,
        $relation
    ): void {
        if (!$model) {
            return;
        }

        $copy =
            $model->replicate([
                'id',
                'character_id',
                'created_at',
                'updated_at',
            ]);

        $relation->save(
            $copy
        );
    }


    private function transformationSheetSettings(
        Character $character
    ): array {
        $settings =
            is_array(
                $character->sheet_settings
                ?? null
            )
                ? $character->sheet_settings
                : [];

        /*
         * Transformações continuam pertencendo ao Character original.
         */
        data_set(
            $settings,
            'optional_rules.transformations',
            false
        );

        return $settings;
    }


    private function normalizeDefinition(
        array $validated,
        Character $character
    ): array {
        $type =
            (string) $validated['type'];

        // Vincule apenas uma magia já presente no Grimório do próprio personagem.
        $linkedSpellId = null;
        if (($validated['casting_time'] ?? 'none') === 'spell') {
            $linkedSpellId = (int) ($validated['linked_spell_id'] ?? 0);
            if ($linkedSpellId < 1 || !\App\Models\CharacterSpell::query()
                ->where('character_id', $character->id)
                ->where('spell_id', $linkedSpellId)
                ->whereHas('spell', fn ($query) => $query->where('concentration', true))
                ->exists()) {
                throw ValidationException::withMessages([
                    'linked_spell_id' => 'Escolha uma magia de concentração presente no Grimório deste personagem.',
                ]);
            }
        }

        $sourceNpcId =
            null;

        if (
            $type
            === CharacterTransformation::TYPE_MONSTER_SHEET
        ) {
            if (
                empty(
                    $validated['source_npc_id']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'source_npc_id' =>
                        'Escolha uma criatura do Cofre para esta transformação.',
                ]);
            }

            $sourceNpcId =
                (int) $validated['source_npc_id'];

            $sourceNpc =
                Npc::query()
                    ->whereKey(
                        $sourceNpcId
                    )
                    ->where(
                        'user_id',
                        (int) $character->user_id
                    )
                    ->first();

            if (!$sourceNpc) {
                throw ValidationException::withMessages([
                    'source_npc_id' =>
                        'A criatura escolhida não está disponível no seu Cofre.',
                ]);
            }

            $this->ensureMonsterNpcSupported(
                $sourceNpc
            );
        }


        $durationType = (string) $validated['duration_type'];
        $durationFormula = null;
        if ($durationType === 'dice') {
            $durationFormula = preg_replace('/\s+/', '', (string) ($validated['duration_formula'] ?? ''));
            if ($this->durationDiceRange($durationFormula) === null) {
                throw ValidationException::withMessages([
                    'duration_formula' => 'Use até 12 dados D4, D6, D8, D10, D12, D20 ou D100; exemplo: 2d6+1.',
                ]);
            }
            // A duração dinâmica continua no contrato existente de rodadas.
            $durationType = CharacterTransformation::DURATION_ROUNDS;
        }

        $durationValue = null;

        if ($durationFormula !== null) {
            // Somente um valor temporário; o resultado real é definido na ativação.
            $durationValue = 1;
        } elseif (
            $durationType
            !== CharacterTransformation::DURATION_INDEFINITE
        ) {
            if (
                empty(
                    $validated['duration_value']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'duration_value' =>
                        'Informe a duração da transformação.',
                ]);
            }

            $durationValue =
                max(
                    1,
                    (int) $validated['duration_value']
                );
        }


        $isSpecial =
            (bool) $validated['is_special'];

        $specialTrigger =
            null;

        if ($isSpecial) {
            if (
                empty(
                    $validated['special_trigger']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'special_trigger' =>
                        'Escolha quando a transformação especial deve acontecer.',
                ]);
            }

            $specialTrigger =
                (string) $validated['special_trigger'];
        }


        $hasLimitedUses =
            (bool) $validated['has_limited_uses'];

        $usesMax =
            null;

        $recovery =
            null;

        if ($hasLimitedUses) {
            if (
                empty(
                    $validated['uses_max']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'uses_max' =>
                        'Informe a quantidade de usos da transformação.',
                ]);
            }

            if (
                empty(
                    $validated['recovery']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'recovery' =>
                        'Escolha quando os usos são recuperados.',
                ]);
            }

            $usesMax =
                max(
                    1,
                    (int) $validated['uses_max']
                );

            $recovery =
                (string) $validated['recovery'];
        }


        $appliesExhaustion =
            (bool) $validated['applies_exhaustion'];

        $exhaustionLevels =
            0;

        $exhaustionTiming =
            null;

        if ($appliesExhaustion) {
            if (
                empty(
                    $validated['exhaustion_levels']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'exhaustion_levels' =>
                        'Informe quantos níveis de Exaustão são aplicados.',
                ]);
            }

            if (
                empty(
                    $validated['exhaustion_timing']
                    ?? null
                )
            ) {
                throw ValidationException::withMessages([
                    'exhaustion_timing' =>
                        'Escolha quando a Exaustão deve ser aplicada.',
                ]);
            }

            $exhaustionLevels =
                max(
                    1,
                    min(
                        6,
                        (int) $validated['exhaustion_levels']
                    )
                );

            $exhaustionTiming =
                (string) $validated['exhaustion_timing'];
        }


        return [
            'name' =>
                trim(
                    (string) $validated['name']
                ),

            'type' =>
                $type,

            'source_npc_id' =>
                $sourceNpcId,

            'casting_time' =>
                (string) $validated['casting_time'],

            'requires_concentration' =>
                $linkedSpellId !== null || (bool) $validated['requires_concentration'],

            'settings' => [
                'linked_spell_id' => $linkedSpellId,
                'duration_formula' => $durationFormula,
            ],

            'duration_type' =>
                $durationType,

            'duration_value' =>
                $durationValue,

            'is_special' =>
                $isSpecial,

            'special_trigger' =>
                $specialTrigger,

            'uses_max' =>
                $usesMax,

            /*
             * Na criação este valor será sobrescrito logo abaixo;
             * na edição ele é recalculado preservando usos gastos.
             */
            'uses_current' =>
                $usesMax,

            'recovery' =>
                $recovery,

            'applies_exhaustion' =>
                $appliesExhaustion,

            'exhaustion_levels' =>
                $exhaustionLevels,

            'exhaustion_timing' =>
                $exhaustionTiming,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | AUTORIZAÇÃO
    |--------------------------------------------------------------------------
    */
    private function authorizeCharacter(
        Request $request,
        Character $character
    ): void {
        abort_unless(
            $request->user()
            && (int) $character->user_id
                === (int) $request->user()->id,
            403
        );
    }


    private function ensureRuleEnabled(
        Character $character
    ): void {
        $enabled =
            (bool) data_get(
                $character->sheet_settings,
                'optional_rules.transformations',
                false
            );

        if (!$enabled) {
            throw ValidationException::withMessages([
                'transformations' =>
                    'Ative Transformações em Regras Opcionais antes de utilizar uma forma.',
            ]);
        }
    }


    private function ensureTransformationBelongsToCharacter(
        Character $character,
        CharacterTransformation $transformation
    ): void {
        abort_unless(
            (int) $transformation->character_id
                === (int) $character->id,
            404
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATO SUPORTADO PARA FICHA DE MONSTRO
    |--------------------------------------------------------------------------
    |
    | Por decisão de produto, Transformações de criatura aceitam somente NPCs
    | criados pelo Construtor de NPC do Spellbound:
    |
    | json_data.format === "npc-builder"
    |
    | Importações externas continuam válidas no Cofre, apenas não podem ser
    | usadas como fonte de transformação.
    |--------------------------------------------------------------------------
    */
    private function ensureMonsterNpcSupported(
        Npc $npc
    ): void {
        $resolver =
            app(
                MonsterTransformationSheetResolver::class
            );

        if (
            !$resolver->supports(
                $npc
            )
        ) {
            throw ValidationException::withMessages([
                'source_npc_id' =>
                    'Esta criatura usa um formato externo. Para Transformações, escolha uma ficha criada pelo Construtor de NPC do Spellbound.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EXAUSTÃO DA TRANSFORMAÇÃO
    |--------------------------------------------------------------------------
    |
    | Soma níveis à ficha original e respeita o limite 0–6.
    | O momento vem da própria definição:
    |
    | - activation
    | - end
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | EXAUSTÃO DA TRANSFORMAÇÃO
    |--------------------------------------------------------------------------
    |
    | Cada tipo respeita a própria ficha:
    |
    | - Ficha de Monstro:
    |     não recebe Exaustão da transformação e não altera o Character original.
    |
    | - Nova Ficha:
    |     recebe Exaustão no Character auxiliar apontado por form_character_id.
    |
    | O momento continua vindo da definição:
    |
    | - activation
    | - end
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | EXAUSTÃO DA TRANSFORMAÇÃO
    |--------------------------------------------------------------------------
    |
    | Regra de propriedade:
    |
    | - Forma Normal + Nova Ficha compartilham a MESMA Exaustão.
    | - O valor pertence sempre ao Character original.
    | - A Nova Ficha apenas lê e edita esse mesmo valor.
    | - Ficha de Monstro também aplica exaustão na ficha original.
    |--------------------------------------------------------------------------
    */
    private function applyTransformationExhaustion(
        Character $character,
        CharacterTransformation $transformation,
        string $timing
    ): ?array {
        return app(CharacterConcentrationManager::class)
            ->applyTransformationExhaustion($character, $transformation, $timing);
    }


    /*
    |--------------------------------------------------------------------------
    | TESTE DE CONCENTRAÇÃO DA TRANSFORMAÇÃO
    |--------------------------------------------------------------------------
    |
    | O payload segue um contrato genérico para que o mesmo serviço possa ser
    | reutilizado mais tarde pelo sistema de Feitiços.
    |--------------------------------------------------------------------------
    */
    private function buildTransformationConcentrationCheck(
        Character $character,
        CharacterTransformation $transformation,
        int $damage
    ): ?array {
        if (
            !$transformation->requires_concentration
        ) {
            return null;
        }

        $baseModifier =
            null;

        /*
         * Ficha de Monstro não usa Exaustão do player.
         * Nova Ficha usa a Exaustão compartilhada do Character original.
         */
        $exhaustionCharacter =
            null;

        $context = [
            'source' =>
                'transformation',

            'character_id' =>
                (int) $character->id,

            'transformation_id' =>
                (int) $transformation->id,

            'transformation_name' =>
                (string) $transformation->name,

            'transformation_type' =>
                (string) $transformation->type,
        ];

        /*
        |--------------------------------------------------------------------------
        | FICHA DE MONSTRO
        |--------------------------------------------------------------------------
        */
        if ($transformation->isMonsterSheet()) {
            $sourceNpc =
                $transformation
                    ->sourceNpc()
                    ->first();

            if (!$sourceNpc) {
                return null;
            }

            $sheet =
                app(
                    MonsterTransformationSheetResolver::class
                )->resolve(
                    $sourceNpc
                );

            $constitution =
                collect(
                    $sheet['abilities']
                    ?? []
                )
                    ->first(
                        fn ($ability) =>
                            is_array($ability)
                            && (
                                $ability['key']
                                ?? null
                            )
                            ===
                            'con'
                    );

            $baseModifier =
                (int) (
                    $constitution['save']
                    ?? $constitution['modifier']
                    ?? 0
                );

            $context['npc_id'] =
                (int) $sourceNpc->id;
        }

        /*
        |--------------------------------------------------------------------------
        | NOVA FICHA
        |--------------------------------------------------------------------------
        */
        if ($transformation->isCharacterSheet()) {
            $formCharacter =
                $transformation
                    ->formCharacter()
                    ->first();

            if (!$formCharacter) {
                return null;
            }

            $baseModifier =
                $this->characterConstitutionSaveModifier(
                    $formCharacter
                );

            $exhaustionCharacter =
                $character;

            $context['form_character_id'] =
                (int) $formCharacter->id;
        }

        if ($baseModifier === null) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | EXAUSTÃO
        |--------------------------------------------------------------------------
        |
        | - Ficha de Monstro:
        |     penalty = 0. A exaustão do Character original não atravessa
        |     para a ficha do monstro.
        |
        | - Nova Ficha:
        |     usa o exhaustion_level do Character original compartilhado.
        |--------------------------------------------------------------------------
        */
        $exhaustionRuleActive =
            (bool) data_get(
                is_array(
                    $character->sheet_settings
                    ?? null
                )
                    ? $character->sheet_settings
                    : [],
                'optional_rules.exhaustion',
                false
            );

        $exhaustionLevel =
            0;

        if (
            $exhaustionRuleActive
            && $exhaustionCharacter
        ) {
            $combat =
                $exhaustionCharacter
                    ->combat()
                    ->first();

            $exhaustionLevel =
                max(
                    0,
                    min(
                        6,
                        (int) (
                            $combat?->exhaustion_level
                            ?? 0
                        )
                    )
                );
        }

        $exhaustionPenalty =
            $exhaustionLevel
            * 2;

        return app(
            ConcentrationCheckService::class
        )->forDamage(
            damage:
                $damage,

            constitutionSaveModifier:
                $baseModifier,

            exhaustionPenalty:
                $exhaustionPenalty,

            context:
                $context
        );
    }


    private function characterConstitutionSaveModifier(
        Character $character
    ): int {
        $abilities =
            $character
                ->abilities()
                ->first();

        $temporaryBonuses =
            is_array(
                $abilities?->temporary_bonuses
            )
                ? $abilities->temporary_bonuses
                : [];

        $overrides =
            is_array(
                $abilities?->overrides
            )
                ? $abilities->overrides
                : [];

        $baseScore =
            (int) (
                $abilities?->constitution
                ?? 10
            );

        $effectiveScore =
            array_key_exists(
                'constitution',
                $overrides
            )
                ? (int) $overrides['constitution']
                : (
                    $baseScore
                    +
                    (int) (
                        $temporaryBonuses[
                            'constitution'
                        ]
                        ?? 0
                    )
                );

        $abilityModifier =
            (int) floor(
                (
                    $effectiveScore
                    - 10
                )
                / 2
            );

        $savingThrow =
            $character
                ->savingThrows()
                ->where(
                    'ability',
                    'constitution'
                )
                ->first();

        $temporarySaveBonus =
            (int) (
                $savingThrow?->temporary_bonus
                ?? 0
            );

        if (
            $savingThrow
            && $savingThrow->bonus_override !== null
        ) {
            return
                (int) $savingThrow->bonus_override
                + $temporarySaveBonus;
        }

        $level =
            max(
                1,
                (int) (
                    $character->level
                    ?? 1
                )
            );

        $calculatedProficiency =
            match (true) {
                $level >= 17 => 6,
                $level >= 13 => 5,
                $level >= 9 => 4,
                $level >= 5 => 3,
                default => 2,
            };

        $proficiencyBonus =
            (int) (
                $character->proficiency_bonus
                ?? $calculatedProficiency
            );

        return
            $abilityModifier
            +
            (
                (bool) (
                    $savingThrow?->proficient
                    ?? false
                )
                    ? $proficiencyBonus
                    : 0
            )
            +
            $temporarySaveBonus;
    }


    /*
    |--------------------------------------------------------------------------
    | FOTO
    |--------------------------------------------------------------------------
    */
    private function storeUploadedImage(
        Request $request
    ): ?string {
        $image =
            $request->file(
                'image'
            );

        if (!$image) {
            return null;
        }

        return $image->store(
            'character-transformations',
            'public'
        );
    }


    private function deleteImage(
        string $path
    ): void {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk =
            Storage::disk(
                'public'
            );

        $disk->delete(
            $path
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PV MÁXIMO DO TEMPLATE — NPC BUILDER
    |--------------------------------------------------------------------------
    |
    | Native:
    |   json_data.format === "npc-builder"
    |   Calcula o PV exatamente a partir das opções salvas pelo Builder.
    |
    | Normal / 5emm:
    |   Usa o max_hp persistido no NPC, que já é normalizado na importação.
    |
    */
    private function monsterTemplateMaxHp(
        $sourceNpc
    ): int {
        if (!$sourceNpc instanceof \App\Models\Npc) {
            return max(
                1,
                (int) (
                    $sourceNpc?->max_hp
                    ?? 1
                )
            );
        }

        return app(
            MonsterTransformationSheetResolver::class
        )->maxHp(
            $sourceNpc
        );
    }


    private function imageUrl(
        ?string $path
    ): ?string {
        if (!$path) {
            return null;
        }

        $path =
            str_replace(
                '\\',
                '/',
                trim(
                    $path
                )
            );

        if ($path === '') {
            return null;
        }

        if (
            str_starts_with(
                $path,
                'http://'
            )
            || str_starts_with(
                $path,
                'https://'
            )
        ) {
            return $path;
        }

        foreach (
            [
                'storage/app/public/',
                'public/storage/',
            ]
            as $prefix
        ) {
            $position =
                stripos(
                    $path,
                    $prefix
                );

            if ($position !== false) {
                $path =
                    substr(
                        $path,
                        $position
                        + strlen(
                            $prefix
                        )
                    );

                break;
            }
        }

        $path =
            ltrim(
                $path,
                '/'
            );

        foreach (
            [
                'storage/',
                'public/',
            ]
            as $prefix
        ) {
            if (
                str_starts_with(
                    $path,
                    $prefix
                )
            ) {
                $path =
                    substr(
                        $path,
                        strlen(
                            $prefix
                        )
                    );
            }
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk =
            Storage::disk(
                'public'
            );

        $candidates = [
            $path,
        ];

        $basename =
            basename(
                $path
            );

        if ($basename !== '') {
            $candidates[] =
                'character-transformations/'
                . $basename;
        }

        $resolvedPath =
            collect(
                $candidates
            )
                ->filter()
                ->unique()
                ->first(
                    fn ($candidate) =>
                        $disk->exists(
                            $candidate
                        )
                );

        if (!$resolvedPath) {
            return null;
        }

        /*
         * URL relativa: usa o mesmo host e porta da página atual.
         * Isso evita quebrar miniaturas por APP_URL antigo ou endereço Hamachi.
         */
        return
            '/storage/'
            . ltrim(
                $resolvedPath,
                '/'
            );
    }

    private function npcPortraitUrl(?Npc $npc): ?string
    {
        if (!$npc) return null;
        $data = $npc->json_data ?? [];
        if (is_string($data)) $data = json_decode($data, true) ?: [];
        if (!is_array($data)) $data = [];
        foreach ([
            $npc->image_path ?? null,
            data_get($data, 'image_path'),
            data_get($data, 'image'),
            data_get($data, 'portrait'),
        ] as $path) {
            if (!is_string($path) || trim($path) === '') continue;
            $url = $this->imageUrl($path);
            if ($url !== null) return $url;
        }
        return null;
    }

    private function transformationPayload(
        CharacterTransformation $transformation
    ): array {
        return [
            'id' =>
                (int) $transformation->id,

            'name' =>
                (string) $transformation->name,

            'type' =>
                (string) $transformation->type,

            'source_npc_id' =>
                $transformation->source_npc_id,

            'form_character_id' =>
                $transformation->form_character_id,

            'sheet_edit_url' =>
                (
                    $transformation->isCharacterSheet()
                    && $transformation->form_character_id !== null
                )
                    ? (
                        route(
                            'characters.show',
                            [
                                'character' =>
                                    $transformation->character_id,
                            ]
                        )
                        . '?transformation_sheet='
                        . (int) $transformation->id
                    )
                    : null,

            'image_path' =>
                $transformation->image_path,

            // Usa a imagem escolhida pelo jogador; sem ela, a foto do Cofre.
            'image_url' =>
                $this->imageUrl($transformation->image_path)
                ?: ($transformation->isMonsterSheet()
                    ? $this->npcPortraitUrl($transformation->sourceNpc)
                    : null),

            'casting_time' =>
                (string) $transformation->casting_time,

            'linked_spell_id' => (int) data_get($transformation->settings, 'linked_spell_id', 0) ?: null,

            'requires_concentration' =>
                (bool) $transformation->requires_concentration,

            'duration_type' =>
                (string) $transformation->duration_type,

            'duration_value' =>
                $transformation->duration_value,

            'is_special' =>
                (bool) $transformation->is_special,

            'special_trigger' =>
                $transformation->special_trigger,

            'uses_max' =>
                $transformation->uses_max,

            'uses_current' =>
                $transformation->uses_current,

            'recovery' =>
                $transformation->recovery,

            'applies_exhaustion' =>
                (bool) $transformation->applies_exhaustion,

            'exhaustion_levels' =>
                (int) $transformation->exhaustion_levels,

            'exhaustion_timing' =>
                $transformation->exhaustion_timing,
        ];
    }
}