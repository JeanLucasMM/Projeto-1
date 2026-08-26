<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAbility extends Model
{
    protected $fillable = [
        'character_id',

        'strength',
        'dexterity',
        'constitution',
        'intelligence',
        'wisdom',
        'charisma',

        'temporary_bonuses',
        'overrides',
    ];

    protected $casts = [
        'strength' => 'integer',
        'dexterity' => 'integer',
        'constitution' => 'integer',
        'intelligence' => 'integer',
        'wisdom' => 'integer',
        'charisma' => 'integer',

        'temporary_bonuses' => 'array',
        'overrides' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Valor atual
    |--------------------------------------------------------------------------
    |
    | Exemplo:
    |
    | strength = 13
    | temporary_bonuses['strength'] = 12
    |
    | currentStrength = 25
    |
    */

    public function currentScore(string $ability): int
    {
        $base = (int) ($this->{$ability} ?? 10);

        $temporary = (int) (
            $this->temporary_bonuses[$ability] ?? 0
        );

        $overrideKey = $ability;

        if (
            is_array($this->overrides) &&
            array_key_exists($overrideKey, $this->overrides)
        ) {
            return (int) $this->overrides[$overrideKey];
        }

        return $base + $temporary;
    }

    /*
    |--------------------------------------------------------------------------
    | Modificador
    |--------------------------------------------------------------------------
    */

    public function modifier(string $ability): int
    {
        $score = $this->currentScore($ability);

        return (int) floor(($score - 10) / 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Modificadores acessíveis pelo Model
    |--------------------------------------------------------------------------
    */

    public function getStrengthModifierAttribute(): int
    {
        return $this->modifier('strength');
    }

    public function getDexterityModifierAttribute(): int
    {
        return $this->modifier('dexterity');
    }

    public function getConstitutionModifierAttribute(): int
    {
        return $this->modifier('constitution');
    }

    public function getIntelligenceModifierAttribute(): int
    {
        return $this->modifier('intelligence');
    }

    public function getWisdomModifierAttribute(): int
    {
        return $this->modifier('wisdom');
    }

    public function getCharismaModifierAttribute(): int
    {
        return $this->modifier('charisma');
    }

    /*
    |--------------------------------------------------------------------------
    | Scores atuais
    |--------------------------------------------------------------------------
    */

    public function getCurrentStrengthAttribute(): int
    {
        return $this->currentScore('strength');
    }

    public function getCurrentDexterityAttribute(): int
    {
        return $this->currentScore('dexterity');
    }

    public function getCurrentConstitutionAttribute(): int
    {
        return $this->currentScore('constitution');
    }

    public function getCurrentIntelligenceAttribute(): int
    {
        return $this->currentScore('intelligence');
    }

    public function getCurrentWisdomAttribute(): int
    {
        return $this->currentScore('wisdom');
    }

    public function getCurrentCharismaAttribute(): int
    {
        return $this->currentScore('charisma');
    }
}