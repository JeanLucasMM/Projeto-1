<?php

namespace App\Services;

use App\Models\Combat;
use App\Repositories\Contracts\CombatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CombatService
{
    public function __construct(
        private CombatRepositoryInterface $repository
    ) {
    }

    public function create(int $userId, string $name): Combat
    {
        return $this->repository->create([
            'user_id'        => $userId,
            'name'           => $name,
            'current_round'  => 1,
            'current_turn'   => 0,
            'is_active'      => false,
        ]);
    }

    public function findById(int $id): ?Combat
    {
        return $this->repository->findById($id);
    }

    public function getAllByUser(int $userId): Collection
    {
        return $this->repository->findByUser($userId);
    }

    public function delete(Combat $combat): bool
    {
        return $this->repository->delete($combat);
    }

    /**
     * Inicia o combate, resetando turnos e definindo como ativo.
     */
    public function startCombat(Combat $combat): bool
    {
        $combat->is_active = true;
        $combat->current_round = 1;
        $combat->current_turn = 0;

        return $this->repository->save($combat);
    }

    /**
     * Reseta o status do combate de volta ao estado inicial.
     */
    public function resetCombat(Combat $combat): bool
    {
        $combat->is_active = false;
        $combat->current_round = 1;
        $combat->current_turn = 0;

        return $this->repository->save($combat);
    }

    /**
     * Avança o turno e, se necessário, incrementa a rodada automaticamente.
     */
    public function nextTurn(Combat $combat, int $participantsCount): bool
    {
        if ($participantsCount === 0) {
            return false;
        }

        $combat->current_turn++;

        // Se o turno atual alcançar o número total de participantes, reinicia a fila e sobe a rodada
        if ($combat->current_turn >= $participantsCount) {
            $combat->current_turn = 0;
            $combat->current_round++;
        }

        return $this->repository->save($combat);
    }

    public function save(Combat $combat): bool
    {
        return $this->repository->save($combat);
    }

    public function initiativeList(Combat $combat): Collection
    {
        $npcs = $combat->npcs()
            ->with('npc')
            ->get()
            ->map(function ($combatNpc) {
                return [
                    'id'         => $combatNpc->id,
                    'type'       => 'npc',
                    'name'       => $combatNpc->npc->name,
                    'initiative' => $combatNpc->initiative,
                    'current_hp' => $combatNpc->current_hp,
                    'max_hp'     => $combatNpc->max_hp,
                    'dead'       => $combatNpc->is_dead,
                ];
            });

        $players = $combat->players()
            ->get()
            ->map(function ($player) {
                return [
                    'id'         => $player->id,
                    'type'       => 'player',
                    'name'       => $player->name,
                    'initiative' => $player->initiative,
                    'current_hp' => null,
                    'max_hp'     => null,
                    'dead'       => false,
                ];
            });

        return $npcs
            ->merge($players)
            ->sortByDesc('initiative')
            ->values();
    }
}