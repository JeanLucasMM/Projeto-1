<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CombatPlayer;

class Combat extends Model
{
protected $fillable = [
    'user_id',
    'name',
    'current_round',
    'current_turn',
    'is_active',
];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function npcs(): HasMany
    {
        return $this->hasMany(CombatNpc::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(CombatPlayer::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CombatLog::class);
    }
}