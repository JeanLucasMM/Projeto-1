<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(
        User $user,
        Campaign $campaign
    ): bool {
        return $campaign->isOwner($user)
            || $campaign->hasMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(
        User $user,
        Campaign $campaign
    ): bool {
        return $campaign->isOwner($user);
    }

    public function delete(
        User $user,
        Campaign $campaign
    ): bool {
        return $campaign->isOwner($user);
    }

    public function invite(
        User $user,
        Campaign $campaign
    ): bool {
        return $campaign->isOwner($user);
    }

    public function manageCharacters(
        User $user,
        Campaign $campaign
    ): bool {
        return $campaign->isOwner($user);
    }
}