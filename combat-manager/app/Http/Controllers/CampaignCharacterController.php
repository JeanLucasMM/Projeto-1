<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Services\Campaigns\CampaignCharacterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignCharacterController extends Controller
{
    public function __construct(
        private CampaignCharacterService $characterService
    ) {
    }


    public function store(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        $validated =
            $request->validate([
                'character_id' => [
                    'required',
                    'integer',
                    'exists:characters,id',
                ],
            ]);

        $character =
            Character::query()
                ->findOrFail(
                    $validated['character_id']
                );

        $link =
            $this->characterService
                ->attach(
                    $campaign,
                    $character,
                    Auth::user()
                );

        return back()->with(
            'success',
            $link->is_active
                ? "'{$character->name}' entrou na campanha e está Jogando agora."
                : "'{$character->name}' foi adicionado à campanha e está Descansando."
        );
    }


    public function update(
        Request $request,
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        $validated =
            $request->validate([
                'is_active' => [
                    'required',
                    'boolean',
                ],
            ]);

        $active =
            (bool) $validated['is_active'];

        $this->characterService
            ->setActive(
                $campaign,
                $character,
                Auth::user(),
                $active
            );

        return back()->with(
            'success',
            $active
                ? "'{$character->name}' agora está Jogando."
                : "'{$character->name}' agora está Descansando."
        );
    }


    public function destroy(
        Campaign $campaign,
        Character $character
    ): RedirectResponse {
        $this->characterService
            ->detach(
                $campaign,
                $character,
                Auth::user()
            );

        return back()->with(
            'success',
            "'{$character->name}' foi removido da campanha."
        );
    }
}