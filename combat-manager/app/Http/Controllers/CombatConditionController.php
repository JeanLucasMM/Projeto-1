<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Combat;
use App\Models\CombatPlayer;
use App\Support\CombatConditions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CombatConditionController extends Controller
{
    public function update(Request $request, Combat $combat, CombatPlayer $combatPlayer): JsonResponse
    {
        abort_unless((int) $combat->user_id === (int) Auth::id(), 404);
        abort_unless((int) $combatPlayer->combat_id === (int) $combat->id, 404);
        abort_unless($combatPlayer->character_id !== null, 422, 'O participante precisa ter uma ficha vinculada.');

        $data = $request->validate([
            'condition' => ['required', 'string', Rule::in(array_keys(CombatConditions::all()))],
            'active' => ['required', 'boolean'],
            'duration_turns' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'source' => ['nullable', 'string', 'max:120'],
            'exhaustion_level' => ['nullable', 'integer', 'min:0', 'max:6'],
        ]);

        $key = $data['condition'];
        $active = (bool) $data['active'];

        DB::transaction(function () use ($combat, $combatPlayer, $key, $active, $data): void {
            if ($key === 'exhaustion') {
                // A exaustão usa o campo já existente; nunca criamos uma segunda fonte de verdade.
                $level = $active ? (int) ($data['exhaustion_level'] ?? 1) : 0;
                // Compartilha a exaustão com a ficha original, mesmo com a regra opcional oculta.
                $character = Character::findOrFail($combatPlayer->character_id);
                $characterCombat = $character->combat()->firstOrCreate([], [
                    'experience_points' => 0, 'current_hp' => 0, 'max_hp' => 0,
                    'temporary_hp' => 0, 'temporary_max_hp' => 0,
                    'hit_dice' => [], 'armor_class' => 10, 'speed' => 30,
                    'initiative_bonus' => 0, 'death_save_successes' => 0,
                    'death_save_failures' => 0, 'concentration_active' => false,
                    'concentration_spell_id' => null,
                    'exhaustion_level' => 0, 'conditions' => [],
                    'damage_resistances' => [], 'damage_immunities' => [],
                    'damage_vulnerabilities' => [], 'overrides' => [],
                ]);
                $characterCombat->exhaustion_level = $level;
                $characterCombat->save();
                return;
            }

            $query = DB::table('combat_player_conditions')
                ->where('combat_id', $combat->id)
                ->where('combat_player_id', $combatPlayer->id)
                ->where('condition_key', $key);

            if (!$active) {
                $query->update(['is_active' => false, 'updated_at' => now()]);
                return;
            }

            DB::table('combat_player_conditions')->updateOrInsert(
                ['combat_player_id' => $combatPlayer->id, 'condition_key' => $key],
                [
                    'combat_id' => $combat->id,
                    'character_id' => $combatPlayer->character_id,
                    'source' => $data['source'] ?? null,
                    'applied_round' => max(0, (int) $combat->current_round),
                    'elapsed_turns' => 0,
                    'duration_turns' => $data['duration_turns'] ?? null,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        });

        return response()->json(['success' => true, 'conditions' => $this->conditionsForPlayer($combatPlayer)]);
    }

    public function sheet(Character $character): JsonResponse
    {
        Gate::authorize('view', $character);
        // O Mestre pode aplicar condições mesmo durante a preparação, quando
        // combats.is_active ainda é false. As condições pertencem ao
        // PARTICIPANTE (combat_player_id), não simplesmente ao personagem.
        //
        // Priorizar a condição ativa atualizada mais recentemente evita outro
        // erro: o mesmo personagem pode estar inscrito em dois encontros e o
        // maior combat_player.id pode não ser aquele que recebeu a condição.
        $conditionPlayerId = DB::table('combat_player_conditions')
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->value('combat_player_id');

        $combatPlayer = $conditionPlayerId !== null
            ? CombatPlayer::query()
                ->whereKey((int) $conditionPlayerId)
                ->where('character_id', $character->id)
                ->with('combat')
                ->first()
            : null;
        $conditionSource = $combatPlayer ? 'latest_active_condition' : 'none';

        if (!$combatPlayer) {
            // Sem condições próprias, um encontro ativo continua sendo útil
            // para indicar turno/rodada e exaustão.
            $combatPlayer = CombatPlayer::query()
                ->where('character_id', $character->id)
                ->whereHas('combat', fn ($query) => $query->where('is_active', true))
                ->with('combat')
                ->latest('id')
                ->first();
            $conditionSource = $combatPlayer ? 'active_combat' : 'none';
        }

        $conditions = $combatPlayer ? $this->conditionsForPlayer($combatPlayer) : [];
        $round = $combatPlayer?->combat?->current_round;
        $turn = $combatPlayer?->combat?->current_turn;
        if (!$combatPlayer) {
            $exhaustion = (int) DB::table('character_combat')->where('character_id', $character->id)->value('exhaustion_level');
            if ($exhaustion > 0) {
                $conditions[] = array_merge(
                    ['key' => 'exhaustion', 'elapsed_turns' => 0, 'duration_turns' => null, 'source' => null, 'level' => $exhaustion],
                    CombatConditions::all()['exhaustion']
                );
            }
        }

        return response()->json([
            'conditions' => $conditions,
            'combat_id' => $combatPlayer?->combat_id,
            'round' => $round,
            'turn' => $turn,
            'is_current_turn' => $combatPlayer !== null && $this->playerIsCurrentActor($combatPlayer),
            // Diagnóstico não sensível para distinguir 'sem encontro' de erro no JS.
            'condition_source' => $conditionSource,
        ]);
    }

    private function conditionsForPlayer(CombatPlayer $player): array
    {
        $conditions = DB::table('combat_player_conditions')
            ->where('combat_player_id', $player->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn ($item) => array_merge(
                [
                    'key' => $item->condition_key,
                    'elapsed_turns' => (int) $item->elapsed_turns,
                    'duration_turns' => $item->duration_turns !== null ? (int) $item->duration_turns : null,
                    'source' => $item->source,
                ],
                CombatConditions::all()[$item->condition_key] ?? ['name' => $item->condition_key, 'effect' => '', 'symbol' => 'circle']
            ))
            ->all();

        $exhaustion = (int) DB::table('character_combat')->where('character_id', $player->character_id)
            ->value('exhaustion_level');
        if ($exhaustion > 0) {
            $conditions[] = array_merge(
                ['key' => 'exhaustion', 'elapsed_turns' => 0, 'duration_turns' => null, 'source' => null, 'level' => $exhaustion],
                CombatConditions::all()['exhaustion']
            );
        }
        return $conditions;
    }

    private function playerIsCurrentActor(CombatPlayer $player): bool
    {
        $combat = $player->combat;
        if (!$combat || !$combat->is_active) {
            return false;
        }
        $participants = app(\App\Services\CombatInitiativeService::class)->participants($combat)->values();
        $actor = $participants->get((int) $combat->current_turn);
        if (!$actor) {
            return false;
        }
        $type = is_array($actor) ? ($actor['type'] ?? '') : (method_exists($actor, 'isNpc') ? ($actor->isNpc() ? 'npc' : 'player') : ($actor->type ?? ''));
        $id = is_array($actor) ? ($actor['id'] ?? null) : ($actor->id ?? null);
        return $type === 'player' && (int) $id === (int) $player->id;
    }
}
