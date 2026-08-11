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
     * Calcula o HP máximo suportando tanto o formato npc-builder quanto o 5emm / Padrão.
     */
    private function calculateMaxHp(Npc $npc): int
    {
        $json = $npc->json_data ?? [];
        $isBuilder = ($json['format'] ?? null) === 'npc-builder';

        if ($isBuilder) {
            $builderCombat = $json['combat'] ?? [];
            $hpMode = $builderCombat['hp_mode'] ?? 'average';

            if ($hpMode === 'custom' && isset($builderCombat['custom_hp'])) {
                $maxHp = (int) $builderCombat['custom_hp'];
            } else {
                $hdCount = (int) ($builderCombat['hit_dice_count'] ?? 1);
                $hdFace = (int) str_replace('d', '', $builderCombat['hit_die'] ?? '8');
                $hpExtra = (int) ($builderCombat['hp_mod_extra'] ?? 0);
                
                $conScore = (int) ($json['abilities']['con'] ?? 10);
                $conMod = floor(($conScore - 10) / 2);
                
                $avgPerDie = match($hdFace) {
                    4 => 2.5,
                    6 => 3.5,
                    8 => 4.5,
                    10 => 5.5,
                    12 => 6.5,
                    20 => 10.5,
                    default => ($hdFace / 2) + 0.5
                };

                $maxHp = (int) floor(($hdCount * $avgPerDie) + ($hdCount * $conMod) + $hpExtra);
            }

            return $maxHp < 1 ? 1 : $maxHp;
        }

        // Formato 5emm / Padrão
        $maxHp = (int) ($npc->calculated_hp ?? 0);
        return $maxHp < 1 ? 10 : $maxHp;
    }

    /**
     * Adiciona um NPC ao combate.
     *
     * O HP usado no combate deve ser o HP calculado
     * pelo NPC, inclusive para NPCs do formato npc-builder.
     */
    public function addNpc(
        Combat $combat,
        Npc $npc,
        int $initiative = 0
    ): CombatNpc {
        $maxHp = $this->calculateMaxHp($npc);

        return $this->repository->create([
            'combat_id' => $combat->id,
            'npc_id' => $npc->id,
            'initiative' => $initiative,
            'current_hp' => $maxHp,
            'max_hp' => $maxHp,
            'temporary_hp' => 0,
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

    /**
     * Busca um NPC do combate pelo ID.
     */
    public function findById(int $id): ?CombatNpc
    {
        return $this->repository->findById($id);
    }

    /**
     * Aplica dano ao NPC.
     *
     * A vida temporária é consumida primeiro.
     * O restante é aplicado à vida normal.
     */
    public function damage(
        CombatNpc $combatNpc,
        int $damage
    ): void {
        $damage = max(0, $damage);

        if ($damage === 0) {
            return;
        }

        // Consome primeiro a Vida Temporária.
        if ($combatNpc->temporary_hp > 0) {
            if ($damage <= $combatNpc->temporary_hp) {
                $combatNpc->temporary_hp -= $damage;
                $damage = 0;
            } else {
                $damage -= $combatNpc->temporary_hp;
                $combatNpc->temporary_hp = 0;
            }
        }

        // Aplica o restante do dano na vida normal.
        if ($damage > 0) {
            $combatNpc->current_hp = max(
                0,
                (int) $combatNpc->current_hp - $damage
            );
        }

        $combatNpc->is_dead = $combatNpc->current_hp === 0;

        $combatNpc->save();
    }

    /**
     * Cura o NPC.
     *
     * A vida nunca ultrapassa o max_hp armazenado no CombatNpc.
     */
    public function heal(
        CombatNpc $combatNpc,
        int $heal
    ): void {
        $heal = max(0, $heal);

        if ($heal === 0) {
            return;
        }

        $currentHp = (int) $combatNpc->current_hp;
        $maxHp = (int) $combatNpc->max_hp;

        $combatNpc->current_hp = min(
            $maxHp,
            $currentHp + $heal
        );

        if ($combatNpc->current_hp > 0) {
            $combatNpc->is_dead = false;
        }

        $combatNpc->save();
    }

    /**
     * Define a Vida Temporária.
     */
    public function setTemporaryHp(
        CombatNpc $combatNpc,
        int $temporaryHp
    ): void {
        $combatNpc->temporary_hp = max(0, $temporaryHp);

        $combatNpc->save();
    }
}