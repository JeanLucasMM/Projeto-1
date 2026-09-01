<?php

namespace App\Services\Campaigns;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CampaignService
{
    public function create(
        User $owner,
        array $data
    ): Campaign {
        return Campaign::create([
            'owner_user_id' => $owner->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function ownedBy(User $user): Collection
    {
        return Campaign::query()
            ->where('owner_user_id', $user->id)
            ->withCount([
                'members',
                'characters',
            ])
            ->latest()
            ->get();
    }

    public function joinedBy(User $user): Collection
    {
        return Campaign::query()
            ->whereHas(
                'members',
                fn ($query) =>
                    $query->where(
                        'user_id',
                        $user->id
                    )
            )
            ->with([
                'owner',
            ])
            ->withCount([
                'members',
                'characters',
            ])
            ->latest()
            ->get();
    }
}
