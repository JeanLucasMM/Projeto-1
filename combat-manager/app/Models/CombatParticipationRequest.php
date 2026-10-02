<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatParticipationRequest extends Model
{
    protected $fillable = [
        'combat_id',
        'character_id',
        'status',
        'initiative',
        'expires_at',
        'responded_at',
    ];

    protected $casts = [
        'combat_id' => 'integer',
        'character_id' => 'integer',
        'initiative' => 'integer',
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}