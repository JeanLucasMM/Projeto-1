<?php

namespace App\Services;

use App\Models\Character;
use App\Models\Combat;
use App\Models\CombatPlayer;
use App\Repositories\Contracts\CombatPlayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CombatPlayerService
{
    public function __construct(
        private CombatPlayerRepositoryInterface $repository
    ) {
    }

    public function getByCombat(
        int $combatId
    ): Collection {
        return $this->repository
            ->findByCombat(
                $combatId
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Participante manual
    |--------------------------------------------------------------------------
    */

    public function addPlayer(
        Combat $combat,
        string $name,
        int $initiative = 0
    ): CombatPlayer {
        return $this->repository->create([
            'combat_id' =>
                $combat->id,

            'character_id' =>
                null,

            'name' =>
                $name,

            'initiative' =>
                $initiative,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Character real
    |--------------------------------------------------------------------------
    |
    | O CombatPlayer nÃ£o duplica HP.
    | character_id aponta para a ficha real e a vida continua em
    | character_combat.
    |
    */

    public function addCharacter(
        Combat $combat,
        Character $character,
        int $initiative = 0
    ): CombatPlayer {
        return $this->repository->create([
            'combat_id' =>
                $combat->id,

            'character_id' =>
                $character->id,

            /*
            | Snapshot/fallback.
            | Se a Character for removida futuramente, o histÃ³rico continua
            | tendo um nome para exibir.
            */
            'name' =>
                $character->name,

            'initiative' =>
                $initiative,
        ]);
    }

    public function characterAlreadyExists(
        Combat $combat,
        Character $character
    ): bool {
        return $this->repository
            ->existsCharacter(
                $combat->id,
                $character->id
            );
    }

    public function setInitiative(
        CombatPlayer $player,
        int $initiative
    ): bool {
        $player->initiative =
            $initiative;

        return $this->repository
            ->save(
                $player
            );
    }

    public function remove(
        CombatPlayer $player
    ): bool {
        return $this->repository
            ->delete(
                $player
            );
    }

    public function find(
        int $id
    ): ?CombatPlayer {
        return $this->repository
            ->find(
                $id
            );
    }
}