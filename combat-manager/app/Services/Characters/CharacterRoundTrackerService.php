<?php

namespace App\Services\Characters;

use App\Models\Character;
use App\Models\CharacterTransformation;
use App\Models\CharacterTransformationState;
use App\Models\CharacterFeature;
use App\Models\CharacterFeatureEffectState;
use Illuminate\Support\Facades\DB;
use App\Models\Spell;
use App\Services\Spells\SpellDurationResolver;
use Illuminate\Validation\ValidationException;

/**
 * Avanço manual local dos efeitos do personagem. Nunca altera a iniciativa do Mestre.
 * Em combate ativo, o avanço cabe exclusivamente ao CombatController.
 */
class CharacterRoundTrackerService
{
    public function advanceOutsideCombat(Character $character, int $rounds): array
    {
        $rounds = max(1, min(10, $rounds));

        return DB::transaction(function () use ($character, $rounds): array {
            $concentration = app(CharacterConcentrationManager::class);
            $character = $concentration->lockCharacter($character);

            $inEncounter = DB::table('combat_players as cp')
                ->join('combats as c', 'c.id', '=', 'cp.combat_id')
                ->where('cp.character_id', $character->id)
                ->where('c.is_active', true)->exists();
            if ($inEncounter) {
                throw ValidationException::withMessages([
                    'rounds' => 'Em combate, somente a iniciativa do Mestre avança as rodadas.',
                ]);
            }

            $updated = false;
            $transformationEnded = false;
            $endedFeatures = [];

            $state = CharacterTransformationState::query()
                ->where('character_id', $character->id)->where('is_active', true)
                ->lockForUpdate()->first();

            if ($state) {
                $runtime = is_array($state->runtime_state) ? $state->runtime_state : [];
                $runtime['manual_elapsed_rounds'] = min(
                    2000000, max(0, (int) ($runtime['manual_elapsed_rounds'] ?? 0)) + $rounds
                );
                if ($state->duration_remaining !== null) {
                    $state->duration_remaining = max(0, (int) $state->duration_remaining - $rounds);
                }
                $state->runtime_state = $runtime;
                if ($state->duration_remaining !== null && $state->duration_remaining < 1) {
                    $form = CharacterTransformation::query()->where('character_id', $character->id)
                        ->whereKey($state->character_transformation_id)->lockForUpdate()->first();
                    $concentration->endTransformation($character, $state, $form);
                    $transformationEnded = true;
                } else {
                    $state->save();
                }
                $updated = true;
            }

            $effects = CharacterFeatureEffectState::query()
                ->where('character_id', $character->id)->where('is_active', true)
                ->lockForUpdate()->get();
            foreach ($effects as $effect) {
                $effect->duration_remaining = max(0, (int) $effect->duration_remaining - $rounds);
                if ($effect->duration_remaining < 1) {
                    $feature = CharacterFeature::query()->where('character_id', $character->id)
                        ->whereKey($effect->character_feature_id)->first();
                    if ($feature) {
                        app(CharacterFeatureEffectService::class)->stop($character, $feature);
                    } else {
                        $effect->is_active = false;
                        $effect->save();
                    }
                    $endedFeatures[] = (int) $effect->character_feature_id;
                } else {
                    $effect->save();
                }
                $updated = true;
            }

            // Magias de duração conhecida expiram ao atingir seu limite;
            // magias especiais continuam em acompanhamento manual.
            $combat = $character->combat()->lockForUpdate()->first();
            if ($combat && $combat->concentration_active
                && (int) ($combat->concentration_spell_id ?? 0) > 0) {
                $overrides = is_array($combat->overrides) ? $combat->overrides
                    : (is_object($combat->overrides) ? (array) $combat->overrides
                        : (json_decode((string) $combat->overrides, true) ?: []));
                $meta = is_array($overrides['concentration'] ?? null) ? $overrides['concentration'] : [];
                $meta['manual_elapsed_rounds'] = min(
                    2000000, max(0, (int) ($meta['manual_elapsed_rounds'] ?? 0)) + $rounds
                );
                $total = isset($meta['duration_total_rounds']) && is_numeric($meta['duration_total_rounds'])
                    ? (int) $meta['duration_total_rounds'] : null;
                if ($total === null) {
                    $total = app(SpellDurationResolver::class)->resolve(
                        Spell::query()->find((int) $combat->concentration_spell_id)
                    )['total_rounds'];
                }
                // Se a magia começou em um combate já encerrado, suas
                // rodadas de iniciativa continuam contando no limite total.
                $pastCombatRounds = 0;
                if (!empty($meta['combat_id']) && !empty($meta['started_round'])) {
                    $pastCombat = DB::table('combats')->where('id', (int) $meta['combat_id'])
                        ->first(['current_round']);
                    if ($pastCombat) {
                        $pastCombatRounds = max(0,
                            (int) $pastCombat->current_round - (int) $meta['started_round']);
                    }
                }
                if ($total !== null && $total > 0
                    && ((int) $meta['manual_elapsed_rounds'] + $pastCombatRounds) >= $total) {
                    $concentration->clearSpellConcentration($character);
                } else {
                    $overrides['concentration'] = $meta;
                    $combat->overrides = $overrides;
                    $combat->save();
                }
                $updated = true;
            }

            if (!$updated) {
                throw ValidationException::withMessages([
                    'rounds' => 'Não há transformação, habilidade ou concentração ativa para avançar.',
                ]);
            }

            return [
                'success' => true,
                'rounds_advanced' => $rounds,
                'transformation_ended' => $transformationEnded,
                'ended_feature_ids' => $endedFeatures,
                'message' => $rounds === 1 ? 'Uma rodada avançada.' : "{$rounds} rodadas avançadas.",
            ];
        });
    }
}