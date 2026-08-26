<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSpellcasting extends Model
{
    protected $table = 'character_spellcasting';

    protected $fillable = [
        'character_id',
        'class',
        'ability',
        'spell_save_dc_override',
        'spell_attack_bonus_override',
        'metadata',
    ];

    protected $casts = [
        'spell_save_dc_override' => 'integer',
        'spell_attack_bonus_override' => 'integer',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function getAbilityModifierAttribute(): int
    {
        $abilities = $this->character->abilities;

        if (!$abilities) {
            return 0;
        }

        return $abilities->modifier(
            $this->ability
        );
    }

    public function getSpellSaveDcAttribute(): int
    {
        if (
            $this->spell_save_dc_override !== null
        ) {
            return $this->spell_save_dc_override;
        }

        return 8
            + (int) $this->character->proficiency_bonus
            + $this->ability_modifier;
    }

    public function getSpellAttackBonusAttribute(): int
    {
        if (
            $this->spell_attack_bonus_override !== null
        ) {
            return $this->spell_attack_bonus_override;
        }

        return
            (int) $this->character->proficiency_bonus
            + $this->ability_modifier;
    }
}