<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\Character;
use App\Services\Campaigns\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(
        private CampaignService $campaignService
    ) {
    }


    public function index(): View
    {
        $user =
            Auth::user();

        $ownedCampaigns =
            $this->campaignService
                ->ownedBy($user);

        $joinedCampaigns =
            $this->campaignService
                ->joinedBy($user);

        $pendingInvitations =
            CampaignInvitation::query()
                ->with([
                    'campaign.owner',
                ])
                ->where(
                    'invited_user_id',
                    $user->id
                )
                ->where(
                    'status',
                    CampaignInvitation::STATUS_PENDING
                )
                ->where(
                    function ($query) {
                        $query
                            ->whereNull(
                                'expires_at'
                            )
                            ->orWhere(
                                'expires_at',
                                '>',
                                now()
                            );
                    }
                )
                ->latest()
                ->get();

        return view(
            'campaigns.index',
            compact(
                'ownedCampaigns',
                'joinedCampaigns',
                'pendingInvitations'
            )
        );
    }


    public function store(
        Request $request
    ): RedirectResponse {
        $validated =
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

        $campaign =
            $this->campaignService
                ->create(
                    Auth::user(),
                    $validated
                );

        return redirect()
            ->route(
                'campaigns.show',
                $campaign
            )
            ->with(
                'success',
                'Campanha criada com sucesso.'
            );
    }


    public function show(
        Campaign $campaign
    ): View {
        Gate::authorize(
            'view',
            $campaign
        );

        $user =
            Auth::user();

        $campaign->load([
            'owner',

            'members.user',

            'invitations' =>
                function ($query) {
                    $query
                        ->with(
                            'invitedUser'
                        )
                        ->latest();
                },

            'characters.classes',

            'characters.combat',

            'characters.user',
        ]);

        $isOwner =
            $campaign->isOwner(
                $user
            );

        $isMember =
            $campaign->hasMember(
                $user
            );

        /*
        |--------------------------------------------------------------------------
        | Combates são informação de preparação do Mestre
        |--------------------------------------------------------------------------
        |
        | Players não recebem no payload da tela a lista de encontros planejados.
        |
        */

        $campaignCombats =
            collect();

        if ($isOwner) {
            $campaign->load([
                'combats' =>
                    function ($query) {
                        $query
                            ->withCount([
                                'npcs',
                                'players',
                            ])
                            ->orderByDesc(
                                'is_active'
                            )
                            ->orderByDesc(
                                'updated_at'
                            );
                    },
            ]);

            $campaignCombats =
                $campaign->combats;
        }

        /*
        |--------------------------------------------------------------------------
        | Fichas disponíveis para compartilhar
        |--------------------------------------------------------------------------
        |
        | O usuário recebe somente as próprias fichas ainda não vinculadas.
        |
        */

        $linkedIds =
            $campaign->characters
                ->pluck('id');

        $availableCharacters =
            Character::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->whereNotIn(
                    'id',
                    $linkedIds
                )
                ->with([
                    'classes',
                    'combat',
                ])
                ->orderBy(
                    'name'
                )
                ->get();

        return view(
            'campaigns.show',
            compact(
                'campaign',
                'isOwner',
                'isMember',
                'availableCharacters',
                'campaignCombats'
            )
        );
    }


    public function destroy(
        Campaign $campaign
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $campaign
        );

        $campaign->delete();

        return redirect()
            ->route(
                'campaigns.index'
            )
            ->with(
                'success',
                'Campanha removida.'
            );
    }
}