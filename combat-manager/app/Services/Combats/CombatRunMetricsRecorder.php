<?php

namespace App\Services\Combats;

use App\Models\CharacterCombat;
use App\Models\CombatNpc;
use Illuminate\Support\Facades\DB;

/**
 * Conta VARIAÇÕES DE PV OBSERVADAS na ficha (não atribui autor do ataque).
 * Não reconstitui eventos anteriores à instalação nem conta alterações SQL diretas.
 */
class CombatRunMetricsRecorder
{
    public function npcChanged(CombatNpc $participant): void
    {
        if (! $participant->wasChanged('current_hp')) return;
        $before = (int) $participant->getOriginal('current_hp');
        $after = (int) $participant->current_hp;
        $this->appendToCombat((int) $participant->combat_id,
            $participant->side === CombatNpc::SIDE_ALLY ? 'ally' : 'enemy', $before, $after, $participant->npc?->name ?? 'NPC', 'npc', (int) $participant->id);
    }

    public function characterChanged(CharacterCombat $state): void
    {
        if (! $state->wasChanged('current_hp')) return;
        $before = (int) $state->getOriginal('current_hp');
        $after = (int) $state->current_hp;
        // Um personagem pode pertencer a mais de um encontro: apenas combates ativos.
        $ids = DB::table('combat_players as cp')
            ->join('combats as c', 'c.id', '=', 'cp.combat_id')
            ->where('cp.character_id', (int) $state->character_id)
            ->where('c.is_active', true)
            ->distinct()->get(['cp.combat_id', 'cp.id']);
        foreach ($ids as $participant) {
            $this->appendToCombat((int) $participant->combat_id, 'player', $before, $after,
                $state->character?->name ?? 'Jogador', 'player', (int) $participant->id);
        }
    }

    private function appendToCombat(int $combatId, string $group, int $before, int $after, string $name, string $type, int $participantId): void
    {
        $delta = $after - $before;
        if ($delta === 0) return;
        $run = DB::table('combat_runs')->where('combat_id', $combatId)
            ->whereNull('ended_at')->orderByDesc('id')->first(['id','clock_paused']);
        if (!$run || $run->clock_paused) return;
        $runId = $run->id;
        $combat = DB::table('combats')->where('id', $combatId)->first(['current_round', 'current_turn', 'is_active']);
        if (! $combat || ! $combat->is_active) return;
        DB::table('combat_run_metrics')->insert([
            'combat_run_id' => $runId,
            'target_group' => $group,
            'participant_type' => $type,
            'participant_id' => $participantId,
            'target_name' => (function_exists('mb_substr') ? mb_substr($name, 0, 160) : substr($name, 0, 160)),
            'round' => $combat->current_round,
            'turn' => $combat->current_turn,
            'kind' => $delta < 0 ? 'damage' : 'healing',
            'amount' => abs($delta),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
