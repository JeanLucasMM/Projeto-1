<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterCombat extends Model
{
    protected $table = 'character_combat';

    protected $fillable = [
        'character_id',

        /*
        | Progressão
        */
        'experience_points',

        /*
        | Vida
        */
        'current_hp',
        'max_hp',
        'temporary_hp',
        'temporary_max_hp',

        /*
        | Dados de Vida
        */
        'hit_dice',

        /*
        | Defesa
        */
        'armor_class',

        /*
        | Movimento
        */
        'speed',

        /*
        | Iniciativa
        */
        'initiative_bonus',

        /*
        | Death Saves
        */
        'death_save_successes',
        'death_save_failures',

        /*
        | Concentração
        */
        'concentration_active',
        'concentration_spell_id',

        /*
        | Exaustão
        */
        'exhaustion_level',

        /*
        | Estados
        */
        'conditions',
        'damage_resistances',
        'damage_immunities',
        'damage_vulnerabilities',

        /*
        | Overrides
        */
        'overrides',
    ];

    protected $casts = [
        /*
        | Progressão
        */
        'experience_points' => 'integer',

        /*
        | Vida
        */
        'current_hp' => 'integer',
        'max_hp' => 'integer',
        'temporary_hp' => 'integer',
        'temporary_max_hp' => 'integer',

        /*
        | Dados de Vida
        */
        'hit_dice' => 'array',

        /*
        | Defesa
        */
        'armor_class' => 'integer',

        /*
        | Movimento
        */
        'speed' => 'integer',

        /*
        | Iniciativa
        */
        'initiative_bonus' => 'integer',

        /*
        | Death Saves
        */
        'death_save_successes' => 'integer',
        'death_save_failures' => 'integer',

        /*
        | Concentração
        */
        'concentration_active' => 'boolean',
        'concentration_spell_id' => 'integer',

        /*
        | Exaustão
        */
        'exhaustion_level' => 'integer',

        /*
        | Estados
        */
        'conditions' => 'array',
        'damage_resistances' => 'array',
        'damage_immunities' => 'array',
        'damage_vulnerabilities' => 'array',

        /*
        | Overrides
        */
        'overrides' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function concentrationSpell(): BelongsTo
    {
        return $this->belongsTo(
            Spell::class,
            'concentration_spell_id'
        );
    }

    public function isConcentrating(): bool
    {
        return (bool) $this->concentration_active;
    }

    /*
    |--------------------------------------------------------------------------
    | Dados de Vida
    |--------------------------------------------------------------------------
    */

    public function getTotalHitDiceAttribute(): int
    {
        return collect(
            $this->hit_dice ?? []
        )->sum(
            fn (array $hitDie) =>
                (int) ($hitDie['maximum'] ?? 0)
        );
    }

    public function getAvailableHitDiceAttribute(): int
    {
        return collect(
            $this->hit_dice ?? []
        )->sum(
            fn (array $hitDie) =>
                (int) ($hitDie['current'] ?? 0)
        );
    }
}