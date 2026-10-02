<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\Character;
use App\Services\Campaigns\CampaignService;
use App\Services\Campaigns\CampaignLiveStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private CampaignLiveStateService $liveState
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
                ->whereHas(
                    'campaign'
                )
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

        $masterShieldInitial = [
            'players' => [],
        ];

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

            $masterShieldInitial =
                $this->liveState
                    ->masterShield(
                        $campaign
                    );
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
                ->standalone()
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
                'campaignCombats',
                'masterShieldInitial'
            )
        );
    }


    public function confirmDestroy(
        Campaign $campaign
    ): View {
        Gate::authorize(
            'delete',
            $campaign
        );

        return view(
            'campaigns.delete-confirmation',
            compact(
                'campaign'
            )
        );
    }


    public function destroy(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $campaign
        );

        $validated =
            $request->validate([
                'confirmation_text' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'understand' => [
                    'accepted',
                ],

                'delete_intent' => [
                    'required',
                    'in:archive_campaign',
                ],
            ]);

        $expectedText =
            'EXCLUIR '
            .
            $campaign->name;

        if (
            !hash_equals(
                $expectedText,
                trim(
                    $validated['confirmation_text']
                )
            )
        ) {
            throw ValidationException::withMessages([
                'confirmation_text' =>
                    'A confirmação não corresponde. Digite exatamente: '
                    .
                    $expectedText,
            ]);
        }

        Log::warning(
            'Campaign moved to trash by owner.',
            [
                'campaign_id' =>
                    (int) $campaign->id,

                'campaign_name' =>
                    $campaign->name,

                'user_id' =>
                    (int) Auth::id(),

                'ip' =>
                    $request->ip(),

                'route' =>
                    optional(
                        $request->route()
                    )->getName(),

                'method' =>
                    $request->method(),

                'user_agent' =>
                    $request->userAgent(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Exclusão segura
        |--------------------------------------------------------------------------
        |
        | Campaign usa SoftDeletes. Portanto, esta chamada NÃO dispara os
        | cascadeOnDelete das tabelas relacionadas. A campanha some das consultas
        | normais, mas os dados continuam preservados no banco para recuperação.
        |
        */

        $campaign->delete();

        return redirect()
            ->route(
                'campaigns.index'
            )
            ->with(
                'success',
                'Campanha movida para a lixeira. Os dados vinculados foram preservados.'
            );
    }
}