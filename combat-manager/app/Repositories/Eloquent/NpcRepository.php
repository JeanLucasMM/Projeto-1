<?php

namespace App\Repositories\Eloquent;

use App\Models\Npc;
use App\Repositories\Contracts\NpcRepositoryInterface;
use App\Models\CombatNpc;

class NpcRepository implements NpcRepositoryInterface
{
    public function create(array $data): Npc
    {
        return Npc::create($data);
    }

    public function findById(int $id): ?Npc
    {
        return Npc::find($id);
    }

public function findAllByUser(
    int $userId,
    ?string $search = null,
    ?string $sort = null
)
{
    $query = Npc::where('user_id', $userId);

    /*
    |--------------------------------------------------------------------------
    | Busca
    |--------------------------------------------------------------------------
    */

    if (!empty($search)) {

        $query->where(function ($q) use ($search) {

            $q->where('name', 'like', "%{$search}%")
                ->orWhere('creature_type', 'like', "%{$search}%")
                ->orWhere('size', 'like', "%{$search}%");

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Ordenação
    |--------------------------------------------------------------------------
    */

    switch ($sort) {

        case 'name_desc':
            $query->orderByDesc('name');
            break;

        case 'cr_desc':
            $query->orderByDesc('challenge_rating');
            break;

        case 'cr_asc':
            $query->orderBy('challenge_rating');
            break;

        default:
            $query->orderBy('name');

    }

    return $query->get();
}

public function delete(Npc $npc): void
{
    $npc->delete();
}
    
    public function findByIdAndUser(int $id, int $userId): ?Npc
    {
    return Npc::where('id', $id)
        ->where('user_id', $userId)
        ->first();
    }

    public function findAvailableForCombat(
    int $userId,
    int $combatId
)
{
    return Npc::where('user_id', $userId)

        ->whereNotIn('id', function ($query) use ($combatId) {

            $query->select('npc_id')
                ->from('combat_npcs')
                ->where('combat_id', $combatId);

        })

        ->orderBy('name')
        ->get();
}
}