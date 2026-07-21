<?php

namespace App\Repositories\Eloquent;

use App\Models\CombatNpc;
use App\Repositories\Contracts\CombatNpcRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CombatNpcRepository implements CombatNpcRepositoryInterface
{
    public function create(array $data): CombatNpc
    {
        return CombatNpc::create($data);
    }

    public function findByCombat(int $combatId): Collection
    {
     return CombatNpc::with('npc')
        ->where('combat_id', $combatId)
        ->orderByDesc('initiative')
        ->orderBy('id')
        ->get();
    }

    public function exists(int $combatId, int $npcId): bool
    {
        return CombatNpc::where('combat_id', $combatId)
            ->where('npc_id', $npcId)
            ->exists();
    }

    public function delete(CombatNpc $combatNpc): bool
    {
        return $combatNpc->delete();
    }

    public function save(CombatNpc $combatNpc): bool
    {
        return $combatNpc->save();
    }
    public function findById(int $id): ?CombatNpc
{
    return CombatNpc::find($id);
}
}