<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Apenas leituras de concentração entre integrantes ativos da mesma campanha. */
class PartyConcentrationController extends Controller
{
    public function index(Request $request, Character $character): JsonResponse
    {
        Gate::authorize('update', $character);
        $validated = $request->validate([
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
        ]);
        $campaign = Campaign::query()->findOrFail((int) $validated['campaign_id']);
        $isMember = $campaign->characters()
            ->where('characters.id', $character->id)
            ->wherePivot('is_active', true)
            ->exists();
        abort_unless($isMember, 403);

        $members = $campaign->characters()
            ->wherePivot('is_active', true)
            ->where('characters.id', '<>', $character->id)
            ->with('combat')
            ->get();
        $activeIds = $members->filter(fn ($member) =>
            (bool) $member->combat?->concentration_active
            && (int) ($member->combat?->concentration_spell_id ?? 0) > 0
        )->pluck('combat.concentration_spell_id')->filter()->unique()->values();
        $names = $activeIds->isEmpty() ? collect()
            : DB::table('spells')->whereIn('id', $activeIds->all())->pluck('name', 'id');

        $states = $members->mapWithKeys(function ($member) use ($names): array {
            $spellId = $member->combat?->concentration_active
                ? (int) ($member->combat?->concentration_spell_id ?? 0) : 0;
            $name = $spellId > 0 ? $names->get($spellId) : null;
            return [(string) $member->id => [
                'active' => $name !== null,
                'spell_name' => $name,
            ]];
        })->all();

        return response()->json(['success' => true, 'members' => $states]);
    }
}
