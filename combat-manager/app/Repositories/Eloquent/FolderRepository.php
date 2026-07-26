<?php

namespace App\Repositories\Eloquent;

use App\Models\Folder;
use App\Repositories\Contracts\FolderRepositoryInterface;

class FolderRepository implements FolderRepositoryInterface
{
    public function create(array $data): Folder
    {
        return Folder::create($data);
    }

    public function getAllByUser(int $userId)
    {
        return Folder::where('user_id', $userId)
            ->orderBy('name')
            ->get();
    }

    public function findByIdAndUser(int $id, int $userId): ?Folder
    {
        return Folder::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function update(Folder $folder, array $data): bool
    {
        return $folder->update($data);
    }

    public function delete(Folder $folder): bool
    {
        return $folder->delete();
    }
}