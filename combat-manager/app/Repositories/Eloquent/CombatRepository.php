<?php

namespace App\Repositories\Eloquent;

use App\Models\Combat;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\CombatRepositoryInterface;

class CombatRepository implements CombatRepositoryInterface
{
    public function create(array $data): Combat
    {
        return Combat::create($data);
    }

    public function findById(int $id): ?Combat
    {
        return Combat::find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return Combat::where('user_id', $userId)
            ->with([
                'npcs.npc'
            ])
            ->orderByDesc('created_at')
            ->get();
    }

    public function delete(Combat $combat): bool
    {
        return $combat->delete();
    }

    public function save(Combat $combat): bool
    {
        return $combat->save();
    }
}