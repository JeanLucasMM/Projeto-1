<?php

namespace App\Http\Controllers;

use App\Models\Combat;
use App\Models\CombatNpc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * V65.2 - limitações e bônus de PV máximos SOMENTE para esta instância em combate.
 * A mesma autorização aplicada pelos endpoints damageNpc/healNpc/temporaryHp:
 * somente o usuário proprietário do combate pode fazer a alteração.
 */
class CombatNpcMaxHpController extends Controller
{
    public function update(Request $request, int $combatId, int $combatNpcId): JsonResponse
    {
        $data = $request->validate([
            'max_hp_bonus' => ['required', 'integer', 'min:0', 'max:999999'],
            'max_hp_reduction' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);

        $combat = Combat::query()->findOrFail($combatId);
        abort_unless((int) $combat->user_id === (int) Auth::id(), 404);

        $result = DB::transaction(function () use ($combatId, $combatNpcId, $data) {
            $npc = CombatNpc::query()
                ->where('combat_id', $combatId)
                ->lockForUpdate()
                ->findOrFail($combatNpcId);

            $originalMaximum = max(1, (int) ($npc->base_max_hp ?? $npc->max_hp));
            $bonus = (int) $data['max_hp_bonus'];
            $reduction = (int) $data['max_hp_reduction'];
            $effective = max(1, $originalMaximum + $bonus - $reduction);

            $npc->base_max_hp = $originalMaximum;
            $npc->max_hp_bonus = $bonus;
            $npc->max_hp_reduction = $reduction;
            $previousEffectiveMaximum = max(1, (int) $npc->max_hp);
            $npc->max_hp = $effective;
            // Ao AUMENTAR o máximo efetivo, os PV atuais ganham a diferença.
            // Reduções nunca causam dano por si só: só limitam ao novo máximo.
            $increase = max(0, $effective - $previousEffectiveMaximum);
            $npc->current_hp = min($effective, max(0, (int) $npc->current_hp) + $increase);
            $npc->save();

            return [
                'success' => true,
                'base_max_hp' => $originalMaximum,
                'max_hp_bonus' => $bonus,
                'max_hp_reduction' => $reduction,
                'max_hp' => $effective,
                'current_hp' => (int) $npc->current_hp,
                'temporary_hp' => (int) ($npc->temporary_hp ?? 0),
            ];
        });

        return response()->json($result);
    }
}
