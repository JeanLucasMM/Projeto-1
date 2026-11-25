<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Entrada visual da campanha; mantém a antiga página funcional intacta. */
class CampaignOverviewController extends Controller
{
    public function show(Campaign $campaign): View
    {
        Gate::authorize('view', $campaign);

        $campaign->loadMissing([
            'owner', 'members.user', 'characters.classes',
            'characters.combat', 'characters.user',
        ]);

        $viewerId = (int) auth()->id();
        $isOwner = $campaign->isOwner($viewerId);

        $visibleMembers = $campaign->members->filter(static function ($member) use ($viewerId, $isOwner): bool {
            return $isOwner
                || !(bool) ($member->hidden_from_party ?? false)
                || (int) $member->user_id === $viewerId;
        })->values();

        $visibleIds = $visibleMembers->pluck('user_id')->map(static fn ($id): int => (int) $id)->all();
        if (!in_array($viewerId, $visibleIds, true)) {
            $visibleIds[] = $viewerId;
        }

        $activeCharacters = $campaign->characters->filter(static function ($character) use ($visibleIds, $isOwner): bool {
            return (bool) ($character->pivot->is_active ?? false)
                && ($isOwner || in_array((int) $character->user_id, $visibleIds, true));
        })->values();

        $myCharacter = $activeCharacters->first(
            static fn ($character) => (int) $character->user_id === $viewerId
        );

        return view('campaigns.overview', compact(
            'campaign', 'visibleMembers', 'activeCharacters', 'myCharacter', 'isOwner'
        ));
    }
}
