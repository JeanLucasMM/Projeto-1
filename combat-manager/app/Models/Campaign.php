<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'owner_user_id',
        'name',
        'description',
    ];


    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_user_id'
        );
    }


    public function invitations(): HasMany
    {
        return $this->hasMany(
            CampaignInvitation::class
        );
    }


    public function members(): HasMany
    {
        return $this->hasMany(
            CampaignMember::class
        );
    }


    public function characterLinks(): HasMany
    {
        return $this->hasMany(
            CampaignCharacter::class
        );
    }


    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(
            Character::class,
            'campaign_characters'
        )
            ->withPivot([
                'is_active',
            ])
            ->withTimestamps();
    }


    public function combats(): HasMany
    {
        return $this->hasMany(
            Combat::class
        );
    }


    public function isOwner(User|int $user): bool
    {
        $userId =
            $user instanceof User
                ? $user->id
                : $user;

        return (int) $this->owner_user_id
            ===
            (int) $userId;
    }


    public function hasMember(User|int $user): bool
    {
        $userId =
            $user instanceof User
                ? $user->id
                : $user;

        return $this->members()
            ->where(
                'user_id',
                $userId
            )
            ->exists();
    }


    public function canBeViewedBy(
        User $user
    ): bool {
        return $this->isOwner($user)
            ||
            $this->hasMember($user);
    }
}