<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\Combat;
use App\Models\CombatNpc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CharacterPartyCombatAlliesController extends Controller
{
    public function index(Request $request, Character $character): JsonResponse
    {
        Gate::authorize('update', $character);

        $campaignIds = DB::table('campaign_characters')
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->pluck('campaign_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $requestedId = $request->integer('campaign_id');
        $campaignId = $requestedId > 0 ? $requestedId : ($campaignIds[0] ?? null);
        if ($campaignId === null) {
            return response()->json(['success' => true, 'campaign_id' => null, 'combat_id' => null, 'allies' => []]);
        }

        abort_unless(in_array((int) $campaignId, $campaignIds, true), 404);

        $campaign = Campaign::query()->findOrFail($campaignId);
        $combat = Combat::query()
            ->where('campaign_id', $campaign->id)
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();

        if (!$combat) {
            return response()->json([
                'success' => true,
                'campaign_id' => (int) $campaignId,
                'combat_id' => null,
                'allies' => [],
            ]);
        }

        $allies = $combat->npcs()
            ->with('npc')
            ->where('side', CombatNpc::SIDE_ALLY)
            ->orderBy('id')
            ->get()
            ->filter(fn (CombatNpc $combatNpc) => $combatNpc->npc !== null)
            ->map(function (CombatNpc $combatNpc): array {
                $npc = $combatNpc->npc;
                $max = max(1, (int) $combatNpc->max_hp);
                $current = min($max, max(0, (int) $combatNpc->current_hp));
                $percent = round(min(100, max(0, 100 * $current / $max)), 1);
                $imagePath = trim((string) ($npc->image_path ?? ''));
                $imagePath = preg_replace('#^/?storage/#', '', str_replace('\\', '/', $imagePath)) ?? '';
                $imagePath = ltrim($imagePath, '/');
                return [
                    'id' => (int) $combatNpc->id,
                    'name' => (string) $npc->name,
                    'image_url' => $imagePath !== '' ? asset('storage/' . $imagePath) : null,
                    'current_hp' => $current,
                    'max_hp' => $max,
                    'temporary_hp' => max(0, (int) ($combatNpc->temporary_hp ?? 0)),
                    'hp_percent' => $percent,
                    'is_dead' => (bool) $combatNpc->is_dead || $current <= 0,
                ];
            })->values()->all();

        return response()->json([
            'success' => true,
            'campaign_id' => (int) $campaignId,
            'combat_id' => (int) $combat->id,
            'combat_name' => (string) $combat->name,
            'allies' => $allies,
        ]);
    }
}
