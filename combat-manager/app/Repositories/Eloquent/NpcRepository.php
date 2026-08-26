<?php

namespace App\Repositories\Eloquent;

use App\Models\Npc;
use App\Repositories\Contracts\NpcRepositoryInterface;
use Illuminate\Support\Collection;

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
        ?string $sort = null,
        ?string $folder = null
    ): Collection {
        $query = Npc::query()
            ->where('user_id', $userId);

        /*
        |--------------------------------------------------------------------------
        | Busca
        |--------------------------------------------------------------------------
        |
        | A pesquisa considera os principais campos textuais do NPC.
        |
        */

        $search = trim((string) $search);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")

                    ->orWhere('nickname', 'like', "%{$search}%")

                    ->orWhere('creature_type', 'like', "%{$search}%")

                    ->orWhere('size', 'like', "%{$search}%")

                    ->orWhere('alignment', 'like', "%{$search}%")

                    ->orWhere('challenge_rating', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filtro de pasta
        |--------------------------------------------------------------------------
        |
        | folder = null / vazio
        |     → todas as fichas
        |
        | folder = "none"
        |     → fichas que não pertencem a nenhuma pasta
        |
        | folder = ID
        |     → fichas pertencentes à pasta selecionada
        |
        */

        if ($folder !== null && $folder !== '') {

            if ($folder === 'none') {

                $query->whereNull('folder_id');

            } elseif (is_numeric($folder)) {

                $query->where(
                    'folder_id',
                    (int) $folder
                );
            }
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

            case 'newest':
                $query->orderByDesc('created_at');
                break;

            case 'oldest':
                $query->orderBy('created_at');
                break;

            case 'name_asc':
            default:
                $query->orderBy('name');
                break;
        }

        return $query->get();
    }

    public function delete(Npc $npc): void
    {
        $npc->delete();
    }

    public function findByIdAndUser(
        int $id,
        int $userId
    ): ?Npc {
        return Npc::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function findAvailableForCombat(
        int $userId,
        int $combatId
    ) {
        return Npc::query()
            ->where('user_id', $userId)
            ->whereNotIn('id', function ($query) use ($combatId) {
                $query->select('npc_id')
                    ->from('combat_npcs')
                    ->where('combat_id', $combatId);
            })
            ->orderBy('name')
            ->get();
    }
}