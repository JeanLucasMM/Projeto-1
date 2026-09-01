<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterPartyNote extends Model
{
    protected $fillable = [
        'campaign_id',
        'character_id',
        'notes',
        'pokes',
    ];

    protected $casts = [
        'pokes' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}