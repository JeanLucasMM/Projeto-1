<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Combat extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'name',
        'current_round',
        'current_turn',
        'is_active',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'campaign_id' => 'integer',
        'current_round' => 'integer',
        'current_turn' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(
            Campaign::class
        );
    }

    public function npcs(): HasMany
    {
        return $this->hasMany(
            CombatNpc::class
        );
    }

    public function players(): HasMany
    {
        return $this->hasMany(
            CombatPlayer::class
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(
            CombatLog::class
        );
    }
}