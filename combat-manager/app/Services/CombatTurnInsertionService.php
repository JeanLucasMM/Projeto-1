<?php

namespace App\Services;

use App\Models\CombatNpc;
use App\Models\CombatPlayer;

/**
 * Uma rodada em andamento usa o índice do participante como current_turn.
 * Quando a lista é reordenada por iniciativa depois de uma inclusão, mantemos
 * a identidade do combatente que já estava com o turno, não seu índice antigo.
 */
final class CombatTurnInsertionService
{
    public function __construct(private CombatInitiativeService $initiative)
    {
    }

    public function reanchorAfterInsertion(CombatNpc|CombatPlayer $participant): void
    {
        $combat = $participant->combat()->first();

        if (!$combat || !$combat->is_active) {
            return;
        }

        $newType = $participant instanceof CombatNpc ? 'npc' : 'player';
        $newId = (int) $participant->getKey();
        $ordered = $this->initiative->participants($combat);

        // Reconstruímos a ordem imediatamente ANTES desta inserção, sem
        // depender da quantidade ou iniciativa atribuída ao novo integrante.
        $previous = $ordered->reject(
            static fn ($entry): bool =>
                $entry->type === $newType && (int) $entry->id === $newId
        )->values();

        $previousActor = $previous->get((int) $combat->current_turn);
        if (!$previousActor) {
            return;
        }

        $newIndex = $ordered->search(
            static fn ($entry): bool =>
                $entry->type === $previousActor->type
                && (int) $entry->id === (int) $previousActor->id
        );

        if ($newIndex !== false && (int) $newIndex !== (int) $combat->current_turn) {
            $combat->current_turn = (int) $newIndex;
            $combat->saveQuietly();
        }
    }
}
