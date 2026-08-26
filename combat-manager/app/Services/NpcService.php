<?php

namespace App\Services;

use App\Models\Npc;
use App\Repositories\Contracts\NpcRepositoryInterface;
use Illuminate\Support\Collection;

class NpcService
{
    public function __construct(
        private NpcRepositoryInterface $repository
    ) {}

    /**
     * Retorna todos os NPCs do usuário,
     * aplicando busca, ordenação e filtro de pasta.
     */
    public function getAllByUser(
        int $userId,
        ?string $search = null,
        ?string $sort = null,
        ?string $folder = null
    ): Collection {
        return $this->repository->findAllByUser(
            $userId,
            $search,
            $sort,
            $folder
        );
    }

    public function create(array $data): Npc
    {
        return $this->repository->create($data);
    }

    public function findByIdAndUser(
        int $id,
        int $userId
    ): ?Npc {
        return $this->repository->findByIdAndUser(
            $id,
            $userId
        );
    }

    public function delete(Npc $npc): void
    {
        $this->repository->delete($npc);
    }

    public function availableForCombat(
        int $userId,
        int $combatId
    ) {
        return $this->repository->findAvailableForCombat(
            $userId,
            $combatId
        );
    }
}