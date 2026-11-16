<?php

namespace App\Services\Combats;

use App\Models\Combat;
use App\Models\CombatRun;
use Illuminate\Support\Facades\DB;

/** Captura elenco e PV finais, preservando até participantes já removidos. */
class CombatArchiveSnapshotService
{
    public function __construct(private CombatPortraitSnapshotService $portraits) {}

    public function capture(Combat $combat, CombatRun $run): array
    {
        $combat->load(['campaign', 'players.character', 'npcs.npc']);
        $entries = [];
        foreach ($combat->players as $part) {
            $actor = $part->character;
            $health = $part->character_id
                ? DB::table('character_combat')->where('character_id', $part->character_id)->first(['current_hp', 'max_hp'])
                : null;
            $displayName = $actor?->name ?: $part->name ?: 'Jogador';
            $picturePath = $actor?->image_path
                ?: $this->portraits->uniqueCampaignPortrait($combat->campaign_id, $displayName);
            $entries['player:'.$part->id] = [
                'participant_type'=>'player', 'participant_id'=>(int)$part->id,
                'name'=>$displayName,
                'image'=>$picturePath, 'group'=>'player', 'type'=>'player',
                'current_hp'=>$health?->current_hp, 'max_hp'=>$health?->max_hp,
            ];
        }
        foreach ($combat->npcs as $part) {
            if (!$part->npc) continue;
            $entries['npc:'.$part->id] = [
                'participant_type'=>'npc','participant_id'=>(int)$part->id,
                'name'=>$part->npc->name, 'image'=>$part->npc->image_path,
                'group'=>$part->side === 'ally' ? 'ally' : 'enemy','type'=>'npc',
                'current_hp'=>(int)$part->current_hp, 'max_hp'=>(int)$part->max_hp,
            ];
        }
        // Traz participantes que entraram no meio e já saíram antes do encerramento.
        $roster = DB::table('combat_run_participants')->where('combat_run_id', $run->id)
            ->orderBy('id')->get();
        foreach ($roster as $item) {
            $key = $item->participant_type.':'.$item->participant_id;
            $entries[$key] ??= [
                'participant_type'=>$item->participant_type,'participant_id'=>(int)$item->participant_id,
                'name'=>$item->display_name,'image'=>$item->image_path,
                'group'=>$item->side,'type'=>$item->participant_type,
                'current_hp'=>null,'max_hp'=>null,
            ];
            $entries[$key]['joined_late'] = (bool)$item->joined_late;
            $entries[$key]['joined_round'] = $item->joined_round;
            $entries[$key]['left_at'] = $item->left_at;
        }
        $allies = []; $enemies = [];
        foreach ($entries as $entry) {
            $entry['portrait_data'] = $this->portraits->freeze($entry['image'] ?? null);
            if (($entry['group'] ?? '') === 'enemy') $enemies[] = $entry;
            else $allies[] = $entry;
        }
        return [
            'name'=>$combat->name, 'campaign'=>$combat->campaign?->name,
            'allies'=>$allies, 'enemies'=>$enemies,
            'started_label'=>$run->started_at?->format('d/m/Y H:i') ?? 'Não registrado',
            'ended_label'=>$run->ended_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ];
    }
}
