<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Services\Campaigns\CampaignInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CampaignInvitationController extends Controller
{
    public function __construct(
        private CampaignInvitationService $invitationService
    ) {
    }

    public function store(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {


        

        Gate::authorize(
            'invite',
            $campaign
        );

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],
        ]);

        $invitation =
            $this->invitationService->invite(
                $campaign,
                Auth::user(),
                $validated['email']
            );

        return back()->with(
            'success',
            "Convite enviado para {$invitation->email}."
        );
    }

    public function accept(
        CampaignInvitation $invitation
    ): RedirectResponse {
        $this->invitationService->accept(
            $invitation,
            Auth::user()
        );

        return redirect()
            ->route(
                'campaigns.show',
                $invitation->campaign
            )
            ->with(
                'success',
                'Convite aceito. Agora escolha a ficha que deseja compartilhar.'
            );
    }

    public function decline(
        CampaignInvitation $invitation
    ): RedirectResponse {
        $this->invitationService->decline(
            $invitation,
            Auth::user()
        );

        return back()->with(
            'success',
            'Convite recusado.'
        );
    }

    public function destroy(
        Campaign $campaign,
        CampaignInvitation $invitation
    ): RedirectResponse {
        abort_unless(
            (int) $invitation->campaign_id
                === (int) $campaign->id,
            404
        );

        Gate::authorize(
            'invite',
            $campaign
        );

        $this->invitationService->cancel(
            $invitation,
            Auth::user()
        );

        return back()->with(
            'success',
            'Convite cancelado.'
        );
    }
}
