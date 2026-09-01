<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignInvitation extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'campaign_id',
        'invited_by_user_id',
        'invited_user_id',
        'email',
        'token',
        'status',
        'expires_at',
        'responded_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'invited_by_user_id'
        );
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'invited_user_id'
        );
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function canBeAnsweredBy(User $user): bool
    {
        if (
            $this->invited_user_id !== null
            && (int) $this->invited_user_id === (int) $user->id
        ) {
            return true;
        }

        return mb_strtolower($this->email)
            === mb_strtolower($user->email);
    }
}
