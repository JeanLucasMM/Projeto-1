<?php

namespace App\Services\Characters;

use App\Models\Character;
use App\Models\CharacterFeature;
use App\Models\CharacterFeatureEffectState;
use App\Models\Spell;
use App\Models\CharacterTransformation;
use App\Models\CharacterTransformationState;
use App\Models\Combat;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Única coordenação da concentração do Character ORIGINAL.
 * A forma mecânica não recebe concentração independente.
 * Os chamadores fazem a transição em transação e bloqueiam o Character.
 */
class CharacterConcentrationManager
{
    public function lockCharacter(Character $character): Character
    {
        return Character::query()->whereKey($character->id)
            ->lockForUpdate()->firstOrFail();
    }

    /** Concentração atual da ficha ORIGINAL. Consultar após bloquear Character. */
    public function hasActiveConcentration(Character $character): bool
    {
        $state = CharacterTransformationState::query()
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->first();

        if ($state?->hasActiveTransformation()) {
            $form = CharacterTransformation::query()
                ->where('character_id', $character->id)
                ->whereKey($state->character_transformation_id)
                ->first();
            if ($form?->requires_concentration) {
                return true;
            }
        }

        if (CharacterFeatureEffectState::query()->where('character_id', $character->id)
            ->where('is_active', true)->where('requires_concentration', true)->exists()) {
            return true;
        }
        $combat = $this->combatForUpdate($character, false);
        return (bool) ($combat?->concentration_active);
    }


    /** Fonte única para avisos de substituição na interface. */
    public function currentConcentration(Character $character): ?array
    {
        $formState = CharacterTransformationState::query()
            ->where('character_id', $character->id)->where('is_active', true)
            ->with('transformation')->first();
        if ($formState?->transformation?->requires_concentration) {
            $linkedId = $this->linkedSpellId($formState->transformation);
            if ($linkedId > 0) {
                $combat = $this->combatForUpdate($character, false);
                if ($combat && (bool) $combat->concentration_active
                    && (int) ($combat->concentration_spell_id ?? 0) === $linkedId) {
                    return [
                        'source' => 'spell', 'id' => $linkedId,
                        'name' => (string) (Spell::query()->find($linkedId)?->name ?? 'Feitiço'),
                        'transformation_id' => (int) $formState->transformation->id,
                    ];
                }
            }
            return [
                'source' => 'transformation',
                'id' => (int) $formState->character_transformation_id,
                'name' => (string) $formState->transformation->name,
            ];
        }
        $feature = CharacterFeatureEffectState::query()
            ->where('character_id', $character->id)->where('is_active', true)
            ->where('requires_concentration', true)->with('feature')->first();
        if ($feature) {
            return [
                'source' => 'feature', 'id' => (int) $feature->character_feature_id,
                'name' => (string) ($feature->feature?->name ?? 'Habilidade'),
            ];
        }
        $combat = $this->combatForUpdate($character, false);
        if (!$combat || !(bool) $combat->concentration_active) return null;
        $id = (int) ($combat->concentration_spell_id ?? 0);
        if ($id < 1) return null;
        return [
            'source' => 'spell', 'id' => $id,
            'name' => (string) (Spell::query()->find($id)?->name ?? 'Feitiço'),
        ];
    }

    /** No interior de uma transação, após bloquear o Character original. */
    public function replaceForConcentration(Character $character, string $source, int $id): ?array
    {
        $current = $this->currentConcentration($character);
        if (!$current || ($current['source'] === $source && (int) $current['id'] === $id)) {
            return null;
        }
        $ending = $current;
        if ($current['source'] === 'transformation') {
            $state = CharacterTransformationState::query()
                ->where('character_id', $character->id)->lockForUpdate()->first();
            $form = CharacterTransformation::query()->find($current['id']);
            if ($state && $form) {
                $ending['exhaustion'] = $this->endTransformation($character, $state, $form);
            }
        } elseif ($current['source'] === 'feature') {
            $feature = CharacterFeature::query()->where('character_id', $character->id)
                ->find($current['id']);
            if ($feature) {
                app(CharacterFeatureEffectService::class)->stop($character, $feature);
            }
        } else {
            $this->clearSpellConcentration($character);
        }
        return $ending;
    }

    public function replaceFeatureWithSpell(Character $character): ?array
    {
        $current = $this->currentConcentration($character);
        if (!$current || $current['source'] !== 'feature') return null;
        return $this->replaceForConcentration($character, 'spell', -1);
    }

    /** Concentração de habilidade passa a ser identificada no estado global. */
    public function startFeature(Character $character, CharacterFeature $feature): void
    {
        $combat = $this->combatForUpdate($character, true);
        $overrides = $this->arrayValue($combat->overrides);
        $overrides['concentration'] = [
            ...$this->startMetadata($character),
            'source' => 'feature', 'feature_id' => (int) $feature->id,
        ];
        $combat->concentration_active = true;
        $combat->concentration_spell_id = null;
        $combat->overrides = $overrides;
        $combat->save();
    }

    public function clearConcentrationIfFeature(Character $character, int $featureId): void
    {
        $combat = $this->combatForUpdate($character, false);
        if (!$combat || !$combat->concentration_active) return;
        $meta = $this->arrayValue($combat->overrides)['concentration'] ?? [];
        if (($meta['source'] ?? null) === 'feature'
            && (int) ($meta['feature_id'] ?? 0) === $featureId) {
            $this->clearSpellConcentration($character);
        }
    }

    /** O frontend usa o mesmo contrato de CD e resultado que feitiços e transformações. */
    public function resolveFeature(Character $character, int $featureId, int $damage, int $total): array
    {
        return DB::transaction(function () use ($character, $featureId, $damage, $total): array {
            $character = $this->lockCharacter($character);
            $state = CharacterFeatureEffectState::query()
                ->where('character_id', $character->id)
                ->where('character_feature_id', $featureId)
                ->lockForUpdate()->first();
            $meta = $this->currentConcentration($character);
            if (!$state?->is_active || !$state->requires_concentration
                || ($meta['source'] ?? null) !== 'feature'
                || (int) ($meta['id'] ?? 0) !== $featureId) {
                throw ValidationException::withMessages([
                    'concentration' => 'A habilidade concentrada mudou antes da conclusão do teste.',
                ]);
            }
            $dc = max(10, intdiv(max(1, $damage), 2));
            $passed = $total >= $dc;
            if (!$passed) {
                $feature = CharacterFeature::query()->where('character_id', $character->id)
                    ->findOrFail($featureId);
                app(CharacterFeatureEffectService::class)->stop($character, $feature);
            }
            return [
                'passed' => $passed, 'dc' => $dc, 'total' => $total,
                'concentration_ended' => !$passed,
                'transformation_ended' => false, 'feature_ended' => !$passed,
            ];
        });
    }

    /**
     * Marca formas pré-existentes quando um combate se inicia.
     * O contador só avança enquanto o combate está ativo.
     */
    public function attachEncounterTransformations(Combat $combat): void
    {
        $ids = DB::table('combat_players')
            ->where('combat_id', $combat->id)
            ->whereNotNull('character_id')
            ->pluck('character_id');

        if ($ids->isEmpty()) {
            return;
        }

        $states = CharacterTransformationState::query()
            ->whereIn('character_id', $ids)
            ->where('is_active', true)
            ->whereNull('combat_id')
            ->with('transformation')
            ->get();

        foreach ($states as $state) {
            $runtime = is_array($state->runtime_state) ? $state->runtime_state : [];
            // Migra em memória os estados ativos anteriores ao contador em rodadas.
            if ($state->duration_remaining !== null
                && ($runtime['duration_unit'] ?? null) !== 'rounds'
            ) {
                $multiplier = match ((string) ($state->transformation?->duration_type ?? '')) {
                    'minutes' => 10,
                    'hours' => 600,
                    default => 1,
                };
                $state->duration_remaining = min(2000000,
                    max(1, (int) $state->duration_remaining) * $multiplier);
                $runtime['duration_unit'] = 'rounds';
                $state->runtime_state = $runtime;
            }
            $state->combat_id = (int) $combat->id;
            $state->activated_round = max(1, (int) $combat->current_round);
            $state->activated_turn = (int) $combat->current_turn;
            $state->save();
        }
    }

    /** O reset pausa a duração; uma reativação do encontro retoma a partir da primeira rodada. */
    public function detachEncounterTransformations(Combat $combat): void
    {
        CharacterTransformationState::query()
            ->where('combat_id', $combat->id)
            ->where('is_active', true)
            ->update([
                'combat_id' => null,
                'activated_round' => null,
                'activated_turn' => null,
            ]);
    }

    /**
     * Avanço de rodada idempotente, usando duration_remaining em RODADAS.
     * Aciona encerramento/exaustão ao atingir zero.
     * Deve ser executado na mesma transação de CombatService::nextTurn().
     */
    public function advanceEncounterRound(Combat $combat, int $previousRound): array
    {
        $currentRound = (int) $combat->current_round;
        if (!$combat->is_active || $currentRound <= $previousRound) {
            return [];
        }

        $ids = DB::table('combat_players')
            ->where('combat_id', $combat->id)
            ->whereNotNull('character_id')
            ->pluck('character_id');

        if ($ids->isEmpty()) {
            return [];
        }

        $states = CharacterTransformationState::query()
            ->whereIn('character_id', $ids)
            ->where('combat_id', $combat->id)
            ->where('is_active', true)
            ->whereNotNull('duration_remaining')
            ->lockForUpdate()
            ->get();

        $expired = [];
        foreach ($states as $state) {
            $lastProcessed = max(
                $previousRound,
                (int) ($state->activated_round ?? $previousRound)
            );
            $elapsed = max(0, $currentRound - $lastProcessed);
            if ($elapsed === 0) {
                continue;
            }

            $remaining = max(0, (int) $state->duration_remaining - $elapsed);
            if ($remaining > 0) {
                $state->duration_remaining = $remaining;
                $state->save();
                continue;
            }

            $character = Character::query()->whereKey($state->character_id)->first();
            $form = CharacterTransformation::query()
                ->where('character_id', $state->character_id)
                ->whereKey($state->character_transformation_id)
                ->first();
            if ($character) {
                $this->endTransformation($character, $state, $form);
                $expired[] = [
                    'character_id' => (int) $character->id,
                    'transformation' => (string) ($form?->name ?? 'Transformação'),
                ];
            }
        }
        return $expired;
    }

    /** Informação pública para a iniciativa e polling do Mestre. */
    public function encounterTimers(Combat $combat): array
    {
        $players = DB::table('combat_players')
            ->where('combat_id', $combat->id)
            ->whereNotNull('character_id')
            ->get(['id', 'character_id']);
        if ($players->isEmpty()) {
            return [];
        }

        $states = CharacterTransformationState::query()
            ->whereIn('character_id', $players->pluck('character_id'))
            ->where('is_active', true)
            ->with('transformation')
            ->get()
            ->keyBy('character_id');

        $result = [];
        foreach ($players as $player) {
            $state = $states->get($player->character_id);
            if (!$state?->hasActiveTransformation()) {
                continue;
            }
            $form = $state->transformation;
            $result[(string) $player->id] = [
                'character_id' => (int) $player->character_id,
                'transformation_id' => (int) $state->character_transformation_id,
                'name' => (string) ($form?->name ?? 'Transformação'),
                'requires_concentration' => (bool) ($form?->requires_concentration ?? false),
                'duration_type' => (string) ($form?->duration_type ?? 'indefinite'),
                'duration_remaining' => $state->duration_remaining !== null
                    ? (int) $state->duration_remaining : null,
                'tracking' => (int) ($state->combat_id ?? 0) === (int) $combat->id,
            ];
        }
        return $result;
    }

    public function startTransformation(Character $character, CharacterTransformation $transformation): void
    {
        if (!$transformation->requires_concentration) {
            return;
        }
        $this->replaceForConcentration($character, 'transformation', (int) $transformation->id);

        $combat = $this->combatForUpdate($character, true);
        $overrides = $this->arrayValue($combat->overrides);
        $meta = $this->startMetadata($character);
        $overrides['concentration'] = [
            ...$meta,
            'source' => 'transformation',
            'transformation_id' => (int) $transformation->id,
        ];
        $combat->concentration_active = true;
        $combat->concentration_spell_id = null;
        $combat->overrides = $overrides;
        $combat->save();
    }

    /** Iniciar concentração de feitiço mesmo em fichas antigas sem character_combat. */
    public function startSpell(Character $character, int $spellId): void
    {
        $combat = $this->combatForUpdate($character, true);
        $overrides = $this->arrayValue($combat->overrides);
        $overrides['concentration'] = [
            ...$this->startMetadata($character),
            'source' => 'spell',
        ];
        $combat->concentration_active = true;
        $combat->concentration_spell_id = $spellId;
        $combat->overrides = $overrides;
        $combat->save();
    }

    /**
     * Chamado dentro da MESMA transação da conjuração, após bloquear o Character.
     * Encerrar a forma e aplicar a exaustão é atômico com consumir o recurso.
     */
    public function replaceTransformationWithSpell(Character $character): ?array
    {
        $state = CharacterTransformationState::query()
            ->where('character_id', $character->id)
            ->lockForUpdate()->first();
        if (!$state?->hasActiveTransformation()) {
            return null;
        }
        $transformation = CharacterTransformation::query()
            ->where('character_id', $character->id)
            ->whereKey($state->character_transformation_id)
            ->lockForUpdate()->first();
        if (!$transformation?->requires_concentration) {
            return null;
        }
        $exhaustion = $this->endTransformation($character, $state, $transformation);
        return [
            'id' => (int) $transformation->id,
            'name' => (string) $transformation->name,
            'exhaustion' => $exhaustion,
        ];
    }

    /** End by stop button, failed save, HP depleted or manual return. Caller holds transaction. */
    public function endTransformation(
        Character $character,
        CharacterTransformationState $state,
        ?CharacterTransformation $transformation
    ): ?array {
        $endExhaustion = $transformation
            ? $this->applyTransformationExhaustion(
                $character,
                $transformation,
                CharacterTransformation::EXHAUSTION_ON_END
            )
            : null;

        $state->fill([
            'character_transformation_id' => null,
            'is_active' => false,
            'activated_at' => null,
            'combat_id' => null,
            'activated_round' => null,
            'activated_turn' => null,
            'duration_remaining' => null,
            'monster_state' => null,
            'runtime_state' => null,
        ]);
        $state->save();

        if ($transformation?->requires_concentration) {
            $combat = $this->combatForUpdate($character, false);
            if ($combat) {
                $linkedSpellId = $this->linkedSpellId($transformation);
                if ($linkedSpellId > 0 && (int) ($combat->concentration_spell_id ?? 0) === $linkedSpellId) {
                    // Encerrar a forma vinculada também encerra a concentração da magia.
                    $this->clearSpellConcentration($character);
                } else {
                $meta = $this->arrayValue($combat->overrides)['concentration'] ?? [];
                // Preserve unrelated spell if a legacy inconsistent record contains one.
                if (
                    (int) ($combat->concentration_spell_id ?? 0) === 0
                    && (!isset($meta['transformation_id'])
                        || (int) $meta['transformation_id'] === (int) $transformation->id)
                ) {
                    $this->clearSpellConcentration($character);
                }
                }
            }
        }
        return $endExhaustion;
    }

    /** Ends whichever concentration is active; independent non-concentration forms stay active. */
    public function stop(Character $character): array
    {
        return DB::transaction(function () use ($character): array {
            $character = $this->lockCharacter($character);
            $state = CharacterTransformationState::query()
                ->where('character_id', $character->id)
                ->lockForUpdate()->first();
            if ($state?->hasActiveTransformation()) {
                $transformation = CharacterTransformation::query()
                    ->where('character_id', $character->id)
                    ->whereKey($state->character_transformation_id)
                    ->lockForUpdate()->first();
                if ($transformation?->requires_concentration) {
                    $exhaustion = $this->endTransformation($character, $state, $transformation);
                    return ['source' => 'transformation', 'transformation_ended' => true, 'exhaustion' => $exhaustion];
                }
            }
            $current = $this->currentConcentration($character);
            if (($current['source'] ?? null) === 'feature') {
                $feature = CharacterFeature::query()->where('character_id', $character->id)
                    ->find($current['id']);
                if ($feature) {
                    app(CharacterFeatureEffectService::class)->stop($character, $feature);
                }
                return ['source' => 'feature', 'feature_ended' => true,
                    'feature_name' => $current['name'], 'transformation_ended' => false, 'exhaustion' => null];
            }
            $this->clearSpellConcentration($character);
            return ['source' => 'spell', 'transformation_ended' => false, 'exhaustion' => null];
        });
    }

    /** Clear only the shared concentration flags; do not touch the form. */
    public function clearSpellConcentration(Character $character): void
    {
        $combat = $this->combatForUpdate($character, false);
        if (!$combat) {
            return;
        }
        // Se o feitiço sustentava uma forma, a forma termina junto.
        $spellId = (int) ($combat->concentration_spell_id ?? 0);
        if ($spellId > 0) {
            $state = CharacterTransformationState::query()
                ->where('character_id', $character->id)->where('is_active', true)
                ->with('transformation')->lockForUpdate()->first();
            if ($state?->transformation && $this->linkedSpellId($state->transformation) === $spellId) {
                $this->endTransformation($character, $state, $state->transformation);
            }
        }
        $overrides = $this->arrayValue($combat->overrides);
        unset($overrides['concentration']);
        $combat->concentration_active = false;
        $combat->concentration_spell_id = null;
        $combat->overrides = $overrides;
        $combat->save();
    }

    public function resolveTransformation(
        Character $character,
        int $expectedTransformationId,
        int $damage,
        int $total
    ): array {
        return DB::transaction(function () use ($character, $expectedTransformationId, $damage, $total): array {
            $character = $this->lockCharacter($character);
            $state = CharacterTransformationState::query()
                ->where('character_id', $character->id)->lockForUpdate()->first();
            if (!$state?->hasActiveTransformation()
                || (int) $state->character_transformation_id !== $expectedTransformationId
            ) {
                throw ValidationException::withMessages(['concentration' => 'A transformação mudou antes de concluir o teste.']);
            }
            $transformation = CharacterTransformation::query()
                ->where('character_id', $character->id)
                ->whereKey($expectedTransformationId)->lockForUpdate()->first();
            if (!$transformation?->requires_concentration) {
                throw ValidationException::withMessages(['concentration' => 'Esta transformação não está mais concentrada.']);
            }
            $dc = max(10, intdiv(max(1, $damage), 2));
            $passed = $total >= $dc;
            $exhaustion = $passed ? null : $this->endTransformation($character, $state, $transformation);
            return [
                'passed' => $passed,
                'dc' => $dc,
                'total' => $total,
                'concentration_ended' => !$passed,
                'transformation_ended' => !$passed,
                'exhaustion' => $exhaustion,
            ];
        });
    }

    public function resolveSpell(Character $character, int $expectedSpellId, int $damage, int $total): array
    {
        return DB::transaction(function () use ($character, $expectedSpellId, $damage, $total): array {
            $character = $this->lockCharacter($character);
            $combat = $this->combatForUpdate($character, false);
            if (!$combat || !(bool) $combat->concentration_active
                || (int) ($combat->concentration_spell_id ?? 0) !== $expectedSpellId
            ) {
                throw ValidationException::withMessages(['concentration' => 'A concentração mudou antes de concluir o teste.']);
            }
            $linkedState = CharacterTransformationState::query()
                ->where('character_id', $character->id)->where('is_active', true)
                ->with('transformation')->first();
            $linkedFormActive = $linkedState?->transformation
                && $this->linkedSpellId($linkedState->transformation) === $expectedSpellId;
            $dc = max(10, intdiv(max(1, $damage), 2));
            $passed = $total >= $dc;
            if (!$passed) {
                $this->clearSpellConcentration($character);
            }
            return [
                'passed' => $passed,
                'dc' => $dc,
                'total' => $total,
                'concentration_ended' => !$passed,
                'transformation_ended' => !$passed && $linkedFormActive,
            ];
        });
    }

    /** Keep the existing transformation exhaustion contract, including shared original-character exhaustion. */
    public function applyTransformationExhaustion(
        Character $character,
        CharacterTransformation $transformation,
        string $timing
    ): ?array {
        // A exaustão pertence ao personagem original, tanto em Nova Ficha
        // quanto em Ficha de Monstro. Nunca alterar o NPC do Cofre.
        if (!$transformation->applies_exhaustion
            || $transformation->exhaustion_timing !== $timing
            || (int) ($transformation->exhaustion_levels ?? 0) < 1
        ) {
            return null;
        }
        $target = $this->lockCharacter($character);
        $combat = $this->combatForUpdate($target, true);
        $requested = max(1, min(6, (int) ($transformation->exhaustion_levels ?? 0)));
        $before = max(0, min(6, (int) ($combat->exhaustion_level ?? 0)));
        $after = min(6, $before + $requested);
        $combat->exhaustion_level = $after;
        $combat->save();
        return [
            'timing' => $timing,
            'character_id' => (int) $target->id,
            'before' => $before,
            'requested' => $requested,
            'added' => max(0, $after - $before),
            'level' => $after,
        ];
    }

    /** Um vínculo em settings não exige migration nem outra fonte de concentração. */
    private function linkedSpellId(?CharacterTransformation $transformation): int
    {
        if (!$transformation || $transformation->casting_time !== 'spell') return 0;
        return (int) data_get($transformation->settings, 'linked_spell_id', 0);
    }

    private function combatForUpdate(Character $character, bool $create): ?object
    {
        $combat = $character->combat()->lockForUpdate()->first();
        if (!$combat && $create) {
            $combat = $character->combat()->create([
                'experience_points' => 0, 'current_hp' => 0, 'max_hp' => 1,
                'temporary_hp' => 0, 'temporary_max_hp' => 0, 'hit_dice' => [],
                'armor_class' => 10, 'speed' => 30, 'initiative_bonus' => 0,
                'death_save_successes' => 0, 'death_save_failures' => 0,
                'concentration_active' => false, 'concentration_spell_id' => null,
                'exhaustion_level' => 0, 'conditions' => [],
                'damage_resistances' => [], 'damage_immunities' => [],
                'damage_vulnerabilities' => [], 'overrides' => [],
            ]);
        }
        return $combat;
    }

    private function startMetadata(Character $character): array
    {
        $encounter = DB::table('combat_players as cp')
            ->join('combats as c', 'c.id', '=', 'cp.combat_id')
            ->where('cp.character_id', $character->id)
            ->where('c.is_active', true)
            ->orderByDesc('c.id')->first(['c.id', 'c.current_round']);
        return [
            'combat_id' => $encounter ? (int) $encounter->id : null,
            'started_round' => $encounter ? max(1, (int) $encounter->current_round) : null,
            'started_at' => now()->toIso8601String(),
        ];
    }

    private function arrayValue(mixed $value): array
    {
        if (is_array($value)) return $value;
        if (is_object($value)) return (array) $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
}
