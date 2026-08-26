<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterResource extends Model
{
    protected $fillable = [
        'character_id',

        'name',
        'type',

        'current',
        'maximum',

        'recovery',
        'source',

        'data',
    ];

    protected $casts = [
        'current' => 'integer',
        'maximum' => 'integer',
        'data' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function getPercentAttribute(): float
    {
        if ($this->maximum <= 0) {
            return 0;
        }

        return max(
            0,
            min(
                100,
                ($this->current / $this->maximum) * 100
            )
        );
    }

    public function isEmpty(): bool
    {
        return $this->current <= 0;
    }

    public function hasUses(): bool
    {
        return $this->current > 0;
    }
}