<?php

namespace App\Repositories\Contracts;

use App\Models\Npc;

interface NpcRepositoryInterface
{
    public function create(array $data): Npc;

    public function findById(int $id): ?Npc;

    public function findAllByUser(
    int $userId,
    ?string $search = null,
    ?string $sort = null
);

    public function delete(Npc $npc): void;

    public function findByIdAndUser(int $id, int $userId): ?Npc;

    public function findAvailableForCombat(
    int $userId,
    int $combatId
    );
    
}