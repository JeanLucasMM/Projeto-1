<?php

namespace App\Services;

use App\DTOs\Combat\CombatParticipant;
use App\Models\Combat;
use Illuminate\Support\Collection;

class CombatInitiativeService
{
    public function __construct(
        private CombatNpcService $combatNpcService,
        private CombatPlayerService $combatPlayerService
    ) {
    }

    public function participants(Combat $combat): Collection
    {
        $participants = collect();

        /*
        |--------------------------------------------------------------------------
        | NPCs
        |--------------------------------------------------------------------------
        */

        foreach (
            $this->combatNpcService->getByCombat($combat->id)
            as $combatNpc
        ) {

            $participants->push(

                new CombatParticipant(

                    id: $combatNpc->id,

                    type: 'npc',

                    name: $combatNpc->npc->name,

                    initiative: $combatNpc->initiative,

                    currentHp: $combatNpc->current_hp,

                    maxHp: $combatNpc->max_hp,

                    dead: $combatNpc->is_dead,

                    model: $combatNpc

                )

            );

        }

        /*
        |--------------------------------------------------------------------------
        | Jogadores
        |--------------------------------------------------------------------------
        */

        foreach (
            $this->combatPlayerService->getByCombat($combat->id)
            as $player
        ) {

            $participants->push(

                new CombatParticipant(

                    id: $player->id,

                    type: 'player',

                    name: $player->name,

                    initiative: $player->initiative,

                    currentHp: $player->current_hp,

                    maxHp: $player->max_hp,

                    dead: false,

                    model: $player

                )

            );

        }

        return $participants
            ->sortByDesc('initiative')
            ->values();
    }
}