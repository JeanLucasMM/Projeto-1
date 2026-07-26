<?php

namespace App\Repositories\Contracts;

use App\Models\Folder;

interface FolderRepositoryInterface
{
    public function create(array $data): Folder;

    public function getAllByUser(int $userId);

    public function findByIdAndUser(int $id, int $userId): ?Folder;

    public function update(Folder $folder, array $data): bool;

    public function delete(Folder $folder): bool;
}