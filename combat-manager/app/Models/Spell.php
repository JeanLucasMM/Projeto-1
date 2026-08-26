<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Spell extends Model
{
    protected $fillable = [
        'name',
        'level',
        'school',

        'casting_time',
        'range',
        'components',
        'duration',

        'ritual',
        'concentration',

        'description',

        'effects',
        'saving_throw',
        'attack_type',
        'upcast',

        'source',
        'slug',
        'active',
    ];

    protected $casts = [
        'level' => 'integer',

        'components' => 'array',
        'ritual' => 'boolean',
        'concentration' => 'boolean',

        'effects' => 'array',
        'upcast' => 'array',

        'active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Personagens que possuem a magia
    |--------------------------------------------------------------------------
    */

    public function characterSpells(): HasMany
    {
        return $this->hasMany(CharacterSpell::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Combates utilizando esta magia para concentração
    |--------------------------------------------------------------------------
    */

    public function concentrationCombats(): HasMany
    {
        return $this->hasMany(
            CharacterCombat::class,
            'concentration_spell_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCantrip(): bool
    {
        return $this->level === 0;
    }

    public function requiresConcentration(): bool
    {
        return $this->concentration;
    }

    public function isRitual(): bool
    {
        return $this->ritual;
    }
}