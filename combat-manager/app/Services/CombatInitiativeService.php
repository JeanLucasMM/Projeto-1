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

    public function participants(
        Combat $combat
    ): Collection {
        $participants =
            collect();

        /*
        |--------------------------------------------------------------------------
        | NPCs
        |--------------------------------------------------------------------------
        */

        foreach (
            $this->combatNpcService
                ->getByCombat(
                    $combat->id
                )
            as $combatNpc
        ) {
            $participants->push(
                new CombatParticipant(
                    id:
                        $combatNpc->id,

                    type:
                        'npc',

                    name:
                        $combatNpc->npc->name,

                    initiative:
                        $combatNpc->initiative,

                    currentHp:
                        $combatNpc->current_hp,

                    maxHp:
                        $combatNpc->max_hp,

                    dead:
                        $combatNpc->is_dead,

                    model:
                        $combatNpc
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Jogadores
        |--------------------------------------------------------------------------
        |
        | Participante manual:
        |     não possui Character e continua sem HP.
        |
        | Character vinculada:
        |     HP vem de character_combat. Nunca copiamos vida para
        |     combat_players.
        |
        */

        foreach (
            $this->combatPlayerService
                ->getByCombat(
                    $combat->id
                )
            as $player
        ) {
            $character =
                $player->character;

            $characterCombat =
                $character?->combat;

            $effectiveMaxHp =
                null;

            if ($characterCombat) {
                $effectiveMaxHp =
                    max(
                        0,
                        (int) $characterCombat->max_hp
                        +
                        (int) $characterCombat->temporary_max_hp
                    );
            }

            $participants->push(
                new CombatParticipant(
                    id:
                        $player->id,

                    type:
                        'player',

                    name:
                        $character?->name
                        ?? $player->name
                        ?? 'Personagem',

                    initiative:
                        $player->initiative,

                    currentHp:
                        $characterCombat?->current_hp,

                    maxHp:
                        $effectiveMaxHp,

                    /*
                    | Zero HP não significa automaticamente morte para Player,
                    | pois death saves continuam em CharacterCombat.
                    */
                    dead:
                        false,

                    model:
                        $player
                )
            );
        }

        return $participants
            ->sortByDesc(
                'initiative'
            )
            ->values();
    }
}