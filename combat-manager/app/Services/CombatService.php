<?php

namespace App\Services;

use App\Models\Combat;
use App\Repositories\Contracts\CombatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CombatService
{
    public function __construct(
        private CombatRepositoryInterface $repository
    ) {
    }

    public function create(
        int $userId,
        string $name,
        ?int $campaignId = null
    ): Combat {
        return $this->repository->create([
            'user_id' =>
                $userId,

            'campaign_id' =>
                $campaignId,

            'name' =>
                $name,

            'current_round' =>
                1,

            'current_turn' =>
                0,

            'is_active' =>
                false,
        ]);
    }

    public function findById(
        int $id
    ): ?Combat {
        return $this->repository
            ->findById(
                $id
            );
    }

    public function getAllByUser(
        int $userId
    ): Collection {
        return $this->repository
            ->findByUser(
                $userId
            );
    }

    public function delete(
        Combat $combat
    ): bool {
        return $this->repository
            ->delete(
                $combat
            );
    }

    public function startCombat(
        Combat $combat
    ): bool {
        $combat->is_active =
            true;

        $combat->current_round =
            1;

        $combat->current_turn =
            0;

        return $this->repository
            ->save(
                $combat
            );
    }

    public function resetCombat(
        Combat $combat
    ): bool {
        $combat->is_active =
            false;

        $combat->current_round =
            1;

        $combat->current_turn =
            0;

        return $this->repository
            ->save(
                $combat
            );
    }

    public function nextTurn(
        Combat $combat,
        int $participantsCount
    ): bool {
        if ($participantsCount === 0) {
            return false;
        }

        $combat->current_turn++;

        if (
            $combat->current_turn
            >= $participantsCount
        ) {
            $combat->current_turn =
                0;

            $combat->current_round++;
        }

        return $this->repository
            ->save(
                $combat
            );
    }

    public function save(
        Combat $combat
    ): bool {
        return $this->repository
            ->save(
                $combat
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Lista legada
    |--------------------------------------------------------------------------
    |
    | O CombatController principal já usa CombatInitiativeService.
    | Mantemos este método coerente para qualquer tela/código legado.
    |
    */

    public function initiativeList(
        Combat $combat
    ): Collection {
        $npcs = $combat
            ->npcs()
            ->with('npc')
            ->get()
            ->map(
                function ($combatNpc) {
                    return [
                        'id' =>
                            $combatNpc->id,

                        'type' =>
                            'npc',

                        'name' =>
                            $combatNpc->npc->name,

                        'initiative' =>
                            $combatNpc->initiative,

                        'current_hp' =>
                            $combatNpc->current_hp,

                        'max_hp' =>
                            $combatNpc->max_hp,

                        'dead' =>
                            (bool) $combatNpc->is_dead,
                    ];
                }
            );

        $players = $combat
            ->players()
            ->with([
                'character.combat',
                'character.classes',
            ])
            ->get()
            ->map(
                function ($player) {
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

                    return [
                        'id' =>
                            $player->id,

                        'type' =>
                            'player',

                        'character_id' =>
                            $character?->id,

                        'linked' =>
                            $character !== null,

                        'name' =>
                            $character?->name
                            ?? $player->name
                            ?? 'Personagem',

                        'initiative' =>
                            $player->initiative,

                        'current_hp' =>
                            $characterCombat?->current_hp,

                        'max_hp' =>
                            $effectiveMaxHp,

                        /*
                        | CombatPlayer manual não possui HP próprio.
                        */
                        'dead' =>
                            false,
                    ];
                }
            );

        return $npcs
            ->merge(
                $players
            )
            ->sortByDesc(
                'initiative'
            )
            ->values();
    }
}