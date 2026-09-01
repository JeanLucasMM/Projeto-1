<?php

namespace App\Repositories\Contracts;

use App\Models\CombatPlayer;
use Illuminate\Database\Eloquent\Collection;

interface CombatPlayerRepositoryInterface
{
    public function create(array $data): CombatPlayer;

    public function save(
        CombatPlayer $player
    ): bool;

    public function delete(
        CombatPlayer $player
    ): bool;

    public function findByCombat(
        int $combatId
    ): Collection;

    public function find(
        int $id
    ): ?CombatPlayer;

    public function existsCharacter(
        int $combatId,
        int $characterId
    ): bool;
}