<?php

namespace App\Http\Controllers;

use App\Models\CampaignCharacter;
use App\Models\Character;
use App\Support\CharacterExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CharacterSheetController extends Controller
{
    public function show(
        Request $request,
        Character $character
    ): View {
        Gate::authorize('view', $character);

        $character->load([
            'classes',
            'abilities',
            'skills',
            'savingThrows',
            'features',
            'resources',
            'combat.concentrationSpell',
            'spellcasting',
            'characterSpells.spell',
            'spellSlots',
            'items',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Experiência da campanha
        |--------------------------------------------------------------------------
        |
        | XP pertence ao vínculo Campaign <-> Character. A campanha decide se a
        | regra está ativa e somente o Mestre altera o valor. A ficha apenas lê.
        |
        | campaign_id fixa o contexto quando a ficha é aberta pela campanha.
        | Para acesso direto, priorizamos um vínculo ativo com XP habilitado.
        |
        */
        $user = $request->user();

        $experienceLinks = CampaignCharacter::query()
            ->with('campaign')
            ->where('character_id', $character->id)
            ->whereHas('campaign', function ($query) use ($character, $user): void {
                if ((int) $character->user_id !== (int) $user->id) {
                    $query->where('owner_user_id', $user->id);
                }
            })
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->get();

        $requestedCampaignId = $request->integer('campaign_id');

        if ($requestedCampaignId > 0) {
            $experienceLink = $experienceLinks->first(
                fn (CampaignCharacter $link) =>
                    (int) $link->campaign_id === $requestedCampaignId
            );
        } else {
            $experienceLink = $experienceLinks->first(
                fn (CampaignCharacter $link) =>
                    (bool) ($link->campaign?->experience_enabled ?? false)
            );
        }

        $experienceEnabled = (bool) (
            $experienceLink?->campaign?->experience_enabled
            ?? false
        );

        $character->setAttribute(
            'campaign_experience_enabled',
            $experienceEnabled
        );

        $character->setAttribute(
            'campaign_experience_points',
            $experienceEnabled
                ? CharacterExperience::effectiveTotal(
                    (int) ($character->level ?? 1),
                    (int) ($experienceLink?->experience_points ?? 0)
                )
                : null
        );

        $character->setAttribute(
            'campaign_experience_progress',
            $experienceEnabled
                ? CharacterExperience::progress(
                    (int) ($character->level ?? 1),
                    (int) $character->campaign_experience_points
                )
                : null
        );

        $character->setAttribute(
            'campaign_experience_name',
            $experienceEnabled
                ? $experienceLink?->campaign?->name
                : null
        );

        $character->setAttribute(
            'campaign_experience_id',
            $experienceEnabled
                ? (int) ($experienceLink?->campaign_id ?? 0)
                : null
        );

        return view('characters.show', [
            'character' => $character,
        ]);
    }
}
