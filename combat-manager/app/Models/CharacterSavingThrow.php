<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSavingThrow extends Model
{
    protected $fillable = [
        'character_id',
        'ability',
        'proficient',
        'bonus_override',
        'temporary_bonus',
    ];

    protected $casts = [
        'proficient' => 'boolean',
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

        $abilities = $this->character->abilities;

        if (!$abilities) {
            return (int) $this->temporary_bonus;
        }

        $modifier = $abilities->modifier(
            $this->ability
        );

        $proficiency =
            $this->proficient
                ? (int) $this->character->proficiency_bonus
                : 0;

        return $modifier +
            $proficiency +
            (int) $this->temporary_bonus;
    }
}