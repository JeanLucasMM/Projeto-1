<?php

namespace App\Http\Controllers;

use App\Models\Combat;
use App\Models\CombatNpc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CombatNpcSideController extends Controller
{
    public function update(Request $request, Combat $combat, CombatNpc $combatNpc): RedirectResponse
    {
        abort_unless((int) $combat->user_id === (int) Auth::id(), 404);
        abort_unless((int) $combatNpc->combat_id === (int) $combat->id, 404);

        $data = $request->validate([
            'side' => ['required', 'string', Rule::in([
                CombatNpc::SIDE_ENEMY,
                CombatNpc::SIDE_ALLY,
            ])],
        ]);

        $combatNpc->side = $data['side'];
        $combatNpc->save();

        return back()->with('success', $combatNpc->isAlly()
            ? 'NPC classificado como aliado.'
            : 'NPC classificado como inimigo.');
    }
}
