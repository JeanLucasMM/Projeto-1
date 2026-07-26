<?php

namespace App\Services;

use App\Models\Npc;
use App\Repositories\Contracts\NpcRepositoryInterface;


class NpcService
{
    public function __construct(
        private NpcRepositoryInterface $repository
    ) {}

public function getAllByUser(
    int $userId,
    ?string $search = null,
    ?string $sort = null
)
{
    return $this->repository->findAllByUser(
        $userId,
        $search,
        $sort
    );
}

    public function create(array $data): Npc
{
        return $this->repository->create($data);
}
    
    public function findByIdAndUser(int $id, int $userId): ?Npc
{
    return $this->repository->findByIdAndUser($id, $userId);
}

    public function delete(Npc $npc): void
{
    $this->repository->delete($npc);
}

public function availableForCombat(
    int $userId,
    int $combatId
)

{
    return $this->repository->findAvailableForCombat(
        $userId,
        $combatId
    );
}
}