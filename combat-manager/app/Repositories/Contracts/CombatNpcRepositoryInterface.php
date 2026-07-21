<?php

namespace App\Repositories\Contracts;

use App\Models\CombatNpc;
use Illuminate\Database\Eloquent\Collection;

interface CombatNpcRepositoryInterface
{
    public function create(array $data): CombatNpc;

    public function findByCombat(int $combatId): Collection;

    public function exists(int $combatId, int $npcId): bool;

    public function delete(CombatNpc $combatNpc): bool;

    public function save(CombatNpc $combatNpc): bool;

    public function findById(int $id): ?CombatNpc;
}