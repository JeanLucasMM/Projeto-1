<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAttack extends Model
{
    protected $fillable = [
        'character_id',

        'name',
        'effect',
        'description',

        'attack_ability',
        'use_proficiency',
        'attack_bonus',

        'damage',
        'damage_type',
        'damage_abilities',
        'damage_bonus',

        'uses_current',
        'uses_max',
        'recovery',

        'visible',
        'sort_order',

        'data',
    ];

    protected $casts = [
        'use_proficiency' => 'boolean',

        'attack_bonus' => 'integer',
        'damage_bonus' => 'integer',

        'damage_abilities' => 'array',

        'uses_current' => 'integer',
        'uses_max' => 'integer',

        'visible' => 'boolean',
        'sort_order' => 'integer',

        'data' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(
            Character::class
        );
    }

    public function hasCounter(): bool
    {
        return $this->uses_max !== null;
    }
}