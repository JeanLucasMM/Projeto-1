<?php

namespace App\Services;

use App\Models\Combat;
use App\Models\CombatNpc;
use App\Models\Npc;
use App\Repositories\Contracts\CombatNpcRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CombatNpcService
{
    public function __construct(
        private CombatNpcRepositoryInterface $repository
    ) {
    }

    /**
     * Retorna todos os NPCs do combate.
     */
    public function getByCombat(int $combatId): Collection
    {
        return $this->repository->findByCombat($combatId);
    }

    /**
     * Verifica se o NPC já está presente.
     */
    public function alreadyExists(
        Combat $combat,
        Npc $npc
    ): bool {
        return $this->repository->exists(
            $combat->id,
            $npc->id
        );
    }

    /**
     * Adiciona um NPC ao combate.
     */
    public function addNpc(
        Combat $combat,
        Npc $npc,
        int $initiative = 0
    ): CombatNpc {

        return $this->repository->create([

            'combat_id' => $combat->id,

            'npc_id' => $npc->id,

            'initiative' => $initiative,

            'current_hp' => $npc->max_hp,

            'max_hp' => $npc->max_hp,

            'is_dead' => false,

        ]);
    }

    /**
     * Salva alterações.
     */
    public function save(CombatNpc $combatNpc): bool
    {
        return $this->repository->save($combatNpc);
    }

    /**
     * Remove um NPC.
     */
    public function remove(CombatNpc $combatNpc): bool
    {
        return $this->repository->delete($combatNpc);
    }

 
    /**
     * Atualiza iniciativa.
     */
    public function setInitiative(
        CombatNpc $combatNpc,
        int $initiative
    ): bool {

        $combatNpc->initiative = $initiative;

        return $this->repository->save($combatNpc);
    }

    public function findById(int $id): ?CombatNpc
{
    return $this->repository->findById($id);
}
public function damage(
    CombatNpc $combatNpc,
    int $damage
): void
{
    // Consome primeiro a Vida Temporária
    if ($combatNpc->temporary_hp > 0) {

        if ($damage <= $combatNpc->temporary_hp) {

            $combatNpc->temporary_hp -= $damage;
            $damage = 0;

        } else {

            $damage -= $combatNpc->temporary_hp;
            $combatNpc->temporary_hp = 0;

        }
    }

    // Aplica o restante do dano na vida normal
    if ($damage > 0) {

        $combatNpc->current_hp -= $damage;

        if ($combatNpc->current_hp < 0) {
            $combatNpc->current_hp = 0;
        }

    }

    $combatNpc->is_dead = $combatNpc->current_hp === 0;

    $combatNpc->save();
}

public function heal(
    CombatNpc $combatNpc,
    int $heal
): void
{
    $combatNpc->current_hp += $heal;

    if ($combatNpc->current_hp > $combatNpc->max_hp) {
        $combatNpc->current_hp = $combatNpc->max_hp;
    }

    if ($combatNpc->current_hp > 0) {
        $combatNpc->is_dead = false;
    }

    $combatNpc->save();
}
public function setTemporaryHp(
    CombatNpc $combatNpc,
    int $temporaryHp
): void
{
    $combatNpc->temporary_hp = max(0, $temporaryHp);

    $combatNpc->save();
}

}