<?php

namespace App\Services\Campaigns;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\CampaignMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CampaignInvitationService
{
    public function invite(
        Campaign $campaign,
        User $inviter,
        string $email
    ): CampaignInvitation {
        if (! $campaign->isOwner($inviter)) {
            abort(403);
        }

        $email = mb_strtolower(
            trim($email)
        );

        $invitedUser = User::query()
            ->whereRaw(
                'LOWER(email) = ?',
                [$email]
            )
            ->first();

        if (! $invitedUser) {
            throw ValidationException::withMessages([
                'email' =>
                    'Nenhum usuário cadastrado possui esse e-mail.',
            ]);
        }

        if ((int) $invitedUser->id === (int) $inviter->id) {
            throw ValidationException::withMessages([
                'email' =>
                    'O Mestre já é proprietário desta campanha.',
            ]);
        }

        if ($campaign->hasMember($invitedUser)) {
            throw ValidationException::withMessages([
                'email' =>
                    'Esse usuário já participa da campanha.',
            ]);
        }

        $pending = CampaignInvitation::query()
            ->where('campaign_id', $campaign->id)
            ->where('invited_user_id', $invitedUser->id)
            ->where(
                'status',
                CampaignInvitation::STATUS_PENDING
            )
            ->latest()
            ->first();

        if ($pending) {
            if (! $pending->isExpired()) {
                throw ValidationException::withMessages([
                    'email' =>
                        'Já existe um convite pendente para esse usuário.',
                ]);
            }

            $pending->update([
                'status' =>
                    CampaignInvitation::STATUS_EXPIRED,
                'responded_at' => now(),
            ]);
        }

        return CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'invited_by_user_id' => $inviter->id,
            'invited_user_id' => $invitedUser->id,
            'email' => $invitedUser->email,
            'token' => Str::random(64),
            'status' => CampaignInvitation::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function accept(
        CampaignInvitation $invitation,
        User $user
    ): CampaignMember {
        $this->assertCanAnswer(
            $invitation,
            $user
        );

        return DB::transaction(
            function () use (
                $invitation,
                $user
            ) {
                $member = CampaignMember::query()
                    ->firstOrCreate(
                        [
                            'campaign_id' =>
                                $invitation->campaign_id,
                            'user_id' =>
                                $user->id,
                        ],
                        [
                            'role' => 'player',
                            'joined_at' => now(),
                        ]
                    );

                if (! $member->joined_at) {
                    $member->update([
                        'joined_at' => now(),
                    ]);
                }

                $invitation->update([
                    'status' =>
                        CampaignInvitation::STATUS_ACCEPTED,
                    'responded_at' => now(),
                ]);

                /*
                | Cancela outros convites pendentes da mesma campanha
                | para o mesmo usuário.
                */

                CampaignInvitation::query()
                    ->where('campaign_id', $invitation->campaign_id)
                    ->where('invited_user_id', $user->id)
                    ->whereKeyNot($invitation->id)
                    ->where(
                        'status',
                        CampaignInvitation::STATUS_PENDING
                    )
                    ->update([
                        'status' =>
                            CampaignInvitation::STATUS_CANCELLED,
                        'responded_at' => now(),
                    ]);

                return $member;
            }
        );
    }

    public function decline(
        CampaignInvitation $invitation,
        User $user
    ): void {
        $this->assertCanAnswer(
            $invitation,
            $user
        );

        $invitation->update([
            'status' =>
                CampaignInvitation::STATUS_DECLINED,
            'responded_at' => now(),
        ]);
    }

    public function cancel(
        CampaignInvitation $invitation,
        User $user
    ): void {
        if (! $invitation->campaign->isOwner($user)) {
            abort(403);
        }

        if (
            $invitation->status
            !== CampaignInvitation::STATUS_PENDING
        ) {
            return;
        }

        $invitation->update([
            'status' =>
                CampaignInvitation::STATUS_CANCELLED,
            'responded_at' => now(),
        ]);
    }

    private function assertCanAnswer(
        CampaignInvitation $invitation,
        User $user
    ): void {
        abort_unless(
            $invitation->canBeAnsweredBy($user),
            403
        );

        if (
            $invitation->status
            !== CampaignInvitation::STATUS_PENDING
        ) {
            throw ValidationException::withMessages([
                'invitation' =>
                    'Esse convite não está mais pendente.',
            ]);
        }

        if ($invitation->isExpired()) {
            $invitation->update([
                'status' =>
                    CampaignInvitation::STATUS_EXPIRED,
                'responded_at' => now(),
            ]);

            throw ValidationException::withMessages([
                'invitation' =>
                    'Esse convite expirou.',
            ]);
        }
    }
}
