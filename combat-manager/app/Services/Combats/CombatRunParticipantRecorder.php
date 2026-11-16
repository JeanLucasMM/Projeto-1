<?php

namespace App\Services\Combats;

use App\Models\Combat;
use App\Models\CombatNpc;
use App\Models\CombatPlayer;
use Illuminate\Support\Facades\DB;

/** Uma identidade por participante do encontro, preservada mesmo após sua remoção. */
class CombatRunParticipantRecorder
{
    public function seed(Combat $combat, int $runId): void
    {
        $combat->load(['players.character','npcs.npc']);
        foreach ($combat->players as $part) $this->record($part, $runId, false, false);
        foreach ($combat->npcs as $part) $this->record($part, $runId, false, false);
    }

    public function joined(CombatPlayer|CombatNpc $participant): void
    {
        $run = DB::table('combat_runs')->where('combat_id', $participant->combat_id)
            ->whereNull('ended_at')->orderByDesc('id')->first(['id','turns_advanced']);
        if (!$run) return;
        $combat = Combat::query()->find($participant->combat_id);
        if (!$combat || !$combat->is_active) return;
        $late = (int) $run->turns_advanced > 0 || (int) $combat->current_round > 1;
        $this->record($participant, (int) $run->id, $late, true);
    }

    public function left(CombatPlayer|CombatNpc $participant): void
    {
        $runId = DB::table('combat_runs')->where('combat_id', $participant->combat_id)
            ->whereNull('ended_at')->orderByDesc('id')->value('id');
        if (!$runId) return;
        $type = $participant instanceof CombatNpc ? 'npc' : 'player';
        DB::table('combat_run_participants')->where('combat_run_id', $runId)
            ->where('participant_type', $type)->where('participant_id', $participant->id)
            ->update(['left_at'=>now(),'updated_at'=>now()]);
        DB::table('combat_run_activity')->insert([
            'combat_run_id'=>$runId,'kind'=>'leave','participant_type'=>$type,
            'participant_id'=>$participant->id,
            'participant_name'=>$type === 'npc' ? ($participant->npc?->name ?? 'NPC') : ($participant->character?->name ?? $participant->name ?? 'Jogador'),
            'side'=>$type === 'npc' ? ($participant->side === 'ally' ? 'ally' : 'enemy') : 'player',
            'round'=>Combat::query()->whereKey($participant->combat_id)->value('current_round'),
            'occurred_at'=>now(),
        ]);
    }

    public function record(CombatPlayer|CombatNpc $participant, int $runId, bool $late, bool $announce): void
    {
        $isNpc = $participant instanceof CombatNpc;
        $type = $isNpc ? 'npc' : 'player';
        $actor = $isNpc ? $participant->npc : $participant->character;
        $name = mb_substr((string) ($actor?->name ?? ($isNpc ? 'NPC' : $participant->name ?? 'Jogador')), 0, 160);
        $side = $isNpc ? ($participant->side === 'ally' ? 'ally' : 'enemy') : 'player';
        $round = Combat::query()->whereKey($participant->combat_id)->value('current_round');
        $now = now();
        $inserted = DB::table('combat_run_participants')->insertOrIgnore([
            'combat_run_id'=>$runId,'participant_type'=>$type,'participant_id'=>$participant->id,
            'side'=>$side,'display_name'=>$name,'image_path'=>$actor?->image_path,
            'joined_at'=>$now,'joined_round'=>$round,'joined_late'=>$late,
            'created_at'=>$now,'updated_at'=>$now,
        ]);
        if ($inserted && $announce) {
            DB::table('combat_run_activity')->insert([
                'combat_run_id'=>$runId,'kind'=>'join','participant_type'=>$type,
                'participant_id'=>$participant->id,'participant_name'=>$name,
                'side'=>$side,'round'=>$round,'occurred_at'=>$now,
            ]);
        }
    }
}
