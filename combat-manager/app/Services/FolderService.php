<?php

namespace App\Services;

use App\Models\Folder;
use App\Repositories\Contracts\FolderRepositoryInterface;

class FolderService
{
    public function __construct(
        private FolderRepositoryInterface $repository
    ) {}

    public function create(array $data): Folder
    {
        return $this->repository->create($data);
    }

    public function getAllByUser(int $userId)
    {
        return $this->repository->getAllByUser($userId);
    }

    public function findByIdAndUser(int $id, int $userId): ?Folder
    {
        return $this->repository->findByIdAndUser($id, $userId);
    }

    public function update(Folder $folder, array $data): bool
    {
        return $this->repository->update($folder, $data);
    }

    public function delete(Folder $folder): bool
    {
        return $this->repository->delete($folder);
    }
}