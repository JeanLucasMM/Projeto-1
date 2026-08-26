<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSkill extends Model
{
    protected $fillable = [
        'character_id',
        'skill',
        'proficient',
        'expertise',
        'bonus_override',
        'temporary_bonus',
    ];

    protected $casts = [
        'proficient' => 'boolean',
        'expertise' => 'boolean',
        'bonus_override' => 'integer',
        'temporary_bonus' => 'integer',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function getBonusAttribute(): int
    {
        if ($this->bonus_override !== null) {
            return (int) $this->bonus_override +
                (int) $this->temporary_bonus;
        }

        return (int) $this->temporary_bonus;
    }
}