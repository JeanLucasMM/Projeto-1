<?php

namespace App\Repositories\Eloquent;

use App\Models\CombatPlayer;
use App\Repositories\Contracts\CombatPlayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CombatPlayerRepository implements CombatPlayerRepositoryInterface
{
    public function create(array $data): CombatPlayer
    {
        return CombatPlayer::create($data);
    }

    public function save(CombatPlayer $player): bool
    {
        return $player->save();
    }

    public function delete(CombatPlayer $player): bool
    {
        return $player->delete();
    }

    public function findByCombat(int $combatId): Collection
    {
        return CombatPlayer::where('combat_id', $combatId)
            ->orderByDesc('initiative')
            ->get();
    }

    public function find(int $id): ?CombatPlayer
    {
        return CombatPlayer::find($id);
    }
}