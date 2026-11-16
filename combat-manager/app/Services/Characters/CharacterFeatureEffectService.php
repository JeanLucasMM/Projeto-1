<?php

namespace App\Services\Characters;

use App\Models\Character;
use App\Models\CharacterFeature;
use App\Models\CharacterFeatureEffectState;
use App\Models\Combat;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Estados persistentes de habilidades temporárias. A iniciativa é a fonte do relógio. */
class CharacterFeatureEffectService
{
    public function activeFor(Character $character): array
    {
        return CharacterFeatureEffectState::query()
            ->where('character_id', $character->id)->where('is_active', true)
            ->with(['feature', 'combat'])->orderBy('id')->get()
            ->map(fn (CharacterFeatureEffectState $state) => $this->payload($state))
            ->all();
    }

    public function payload(CharacterFeatureEffectState $state): array
    {
        return [
            'feature_id' => (int) $state->character_feature_id,
            'name' => (string) ($state->feature?->name ?? 'Habilidade'),
            'active' => (bool) $state->is_active,
            'requires_concentration' => (bool) $state->requires_concentration,
            'duration_total_rounds' => $state->duration_total_rounds,
            'duration_remaining' => $state->duration_remaining,
            'roll_expression' => $state->roll_expression,
            'rolled_total' => $state->rolled_total,
            'combat_id' => $state->combat_id,
            'activated_round' => $state->activated_round,
            'activated_turn' => $state->activated_turn,
            'tracking' => $state->combat_id !== null && (bool) ($state->combat?->is_active ?? false),
            'current_round' => $state->combat?->current_round !== null ? (int) $state->combat->current_round : null,
            // duration_total_rounds - duration_remaining preserva o histórico inclusive
            // após início, fim ou reset de um encontro, e após avanço manual.
            'elapsed_rounds' => ($state->duration_total_rounds !== null && $state->duration_remaining !== null)
                ? max(0, (int) $state->duration_total_rounds - (int) $state->duration_remaining) : 0,
        ];
    }

    /** Deve ser chamado com o Character bloqueado, dentro de uma transação. */
    public function activate(Character $character, CharacterFeature $feature, bool $confirm): array
    {
        $data = is_array($feature->data) ? $feature->data : [];
        if (!(bool) ($data['effect_enabled'] ?? false)) {
            throw ValidationException::withMessages(['effect' => 'A habilidade não possui efeito temporário habilitado.']);
        }
        $mode = (string) ($data['effect_duration_mode'] ?? 'fixed');
        $requires = (bool) ($data['effect_requires_concentration'] ?? false);
        $manager = app(CharacterConcentrationManager::class);
        $state = CharacterFeatureEffectState::query()
            ->where('character_feature_id', $feature->id)->lockForUpdate()->first();
        if ($state?->is_active) {
            throw ValidationException::withMessages(['effect' => 'Esta habilidade já está ativa.']);
        }
        if ($requires && $manager->hasActiveConcentration($character) && !$confirm) {
            throw ValidationException::withMessages([
                'concentration' => 'Você já mantém outra concentração. Confirme o encerramento antes de ativar a habilidade.',
            ]);
        }
        if ($feature->uses_max !== null) {
            $available = (int) ($feature->uses_current ?? $feature->uses_max);
            if ($available < 1) {
                throw ValidationException::withMessages(['uses' => 'Esta habilidade não possui usos disponíveis.']);
            }
        }
        $expression = null;
        if ($mode === 'dice') {
            $expression = strtolower(trim((string) ($data['effect_duration_dice'] ?? '1d4')));
            if (!preg_match('/^(\d{1,2})d(\d{1,3})([+-]\d{1,3})?$/', $expression, $matches)) {
                throw ValidationException::withMessages(['effect' => 'Expressão de duração inválida. Use, por exemplo, 1d4 ou 2d6+1.']);
            }
            $count = (int) $matches[1];
            $faces = (int) $matches[2];
            $bonus = (int) ($matches[3] ?? 0);
            if ($count < 1 || $count > 10 || $faces < 2 || $faces > 100 || abs($bonus) > 100) {
                throw ValidationException::withMessages(['effect' => 'Limites de dados excedidos.']);
            }
            $rolled = $bonus;
            for ($i = 0; $i < $count; $i++) {
                $rolled += random_int(1, $faces);
            }
            $duration = max(1, min(10000, $rolled));
        } else {
            $duration = max(1, min(10000, (int) ($data['effect_duration_value'] ?? 1)));
        }

        $replaced = null;
        if ($requires) {
            $replaced = $manager->replaceForConcentration($character, 'feature', (int) $feature->id);
        }
        $encounter = $this->activeEncounter($character);
        $payload = [
            'character_id' => (int) $character->id,
            'character_feature_id' => (int) $feature->id,
            'is_active' => true,
            'requires_concentration' => $requires,
            'duration_total_rounds' => $duration,
            'duration_remaining' => $duration,
            'roll_expression' => $expression,
            'rolled_total' => $expression !== null ? $duration : null,
            'combat_id' => $encounter?->id,
            'activated_round' => $encounter?->current_round,
            'activated_turn' => $encounter?->current_turn,
            'last_processed_round' => $encounter?->current_round,
            'activated_at' => now(),
        ];
        if ($state) {
            $state->fill($payload)->save();
        } else {
            $state = CharacterFeatureEffectState::create($payload);
        }
        if ($feature->uses_max !== null) {
            $feature->uses_current = (int) ($feature->uses_current ?? $feature->uses_max) - 1;
            $feature->save();
        }
        if ($requires) {
            $manager->startFeature($character, $feature);
        }
        $state->load('feature');
        return ['effect' => $this->payload($state), 'replaced_concentration' => $replaced];
    }

    /** Caller holds Character lock; no refund of feature usage. */
    public function stop(Character $character, CharacterFeature $feature): void
    {
        $state = CharacterFeatureEffectState::query()
            ->where('character_id', $character->id)
            ->where('character_feature_id', $feature->id)
            ->lockForUpdate()->first();
        if (!$state?->is_active) return;
        $state->is_active = false;
        $state->duration_remaining = 0;
        $state->save();
        if ($state->requires_concentration) {
            app(CharacterConcentrationManager::class)->clearConcentrationIfFeature($character, (int) $feature->id);
        }
    }

    /** Estados disponíveis para a iniciativa do Mestre, indexados pelo CombatPlayer ID. */
    public function encounterTimers(Combat $combat): array
    {
        $players = DB::table('combat_players')->where('combat_id', $combat->id)
            ->whereNotNull('character_id')->get(['id', 'character_id']);
        if ($players->isEmpty()) return [];
        $effects = CharacterFeatureEffectState::query()
            ->whereIn('character_id', $players->pluck('character_id'))
            ->where('is_active', true)->with(['feature', 'combat'])->get()->groupBy('character_id');
        $timers = [];
        foreach ($players as $player) {
            $timers[(string) $player->id] = ($effects->get($player->character_id) ?? collect())
                ->map(fn (CharacterFeatureEffectState $state) => $this->payload($state))->all();
        }
        return $timers;
    }

    public function attachEncounter(Combat $combat): void
    {
        $ids = DB::table('combat_players')->where('combat_id', $combat->id)
            ->whereNotNull('character_id')->pluck('character_id');
        if ($ids->isEmpty()) return;
        CharacterFeatureEffectState::query()->whereIn('character_id', $ids)
            ->where('is_active', true)->whereNull('combat_id')->get()
            ->each(function (CharacterFeatureEffectState $state) use ($combat): void {
                $state->combat_id = $combat->id;
                $state->activated_round = (int) $combat->current_round;
                $state->activated_turn = (int) $combat->current_turn;
                $state->last_processed_round = (int) $combat->current_round;
                $state->save();
            });
    }

    public function detachEncounter(Combat $combat): void
    {
        CharacterFeatureEffectState::query()->where('combat_id', $combat->id)
            ->where('is_active', true)->update([
                'combat_id' => null, 'activated_round' => null,
                'activated_turn' => null, 'last_processed_round' => null,
            ]);
    }

    /** Called in same transaction as the round transition. */
    public function advanceRound(Combat $combat, int $previousRound): void
    {
        $round = (int) $combat->current_round;
        if (!$combat->is_active || $round <= $previousRound) return;
        $states = CharacterFeatureEffectState::query()
            ->where('combat_id', $combat->id)->where('is_active', true)
            ->lockForUpdate()->get();
        foreach ($states as $state) {
            $last = max((int) ($state->last_processed_round ?? $previousRound), $previousRound);
            $elapsed = max(0, $round - $last);
            if ($elapsed < 1) continue;
            $state->last_processed_round = $round;
            $state->duration_remaining = max(0, (int) $state->duration_remaining - $elapsed);
            if ($state->duration_remaining === 0) {
                $state->is_active = false;
                if ($state->requires_concentration) {
                    $character = Character::find($state->character_id);
                    if ($character) {
                        app(CharacterConcentrationManager::class)->clearConcentrationIfFeature(
                            $character, (int) $state->character_feature_id
                        );
                    }
                }
            }
            $state->save();
        }
    }

    private function activeEncounter(Character $character): ?object
    {
        return DB::table('combat_players as cp')
            ->join('combats as c', 'c.id', '=', 'cp.combat_id')
            ->where('cp.character_id', $character->id)->where('c.is_active', true)
            ->orderByDesc('c.id')->first(['c.id', 'c.current_round', 'c.current_turn']);
    }
}