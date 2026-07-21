<?php

namespace App\Services;

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

    public function getByCombat(int $combatId): Collection
    {
        return $this->repository->findByCombat($combatId);
    }

    public function addPlayer(
        Combat $combat,
        string $name,
        int $initiative = 0 
    ): CombatPlayer {

        return $this->repository->create([
            'combat_id' => $combat->id,
            'name' => $name,
            'initiative' => $initiative,
        ]);
    }

 public function setInitiative(
    CombatPlayer $player,
    int $initiative
): bool {

    $player->initiative = $initiative;

    return $this->repository->save($player);

}

    public function remove(
        CombatPlayer $player
    ): bool {

        return $this->repository->delete($player);
    }

    public function find(int $id): ?CombatPlayer
    {
        return $this->repository->find($id);
    }
    
    

}