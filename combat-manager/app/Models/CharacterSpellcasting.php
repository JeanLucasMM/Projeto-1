<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterSpellcasting extends Model
{
    protected $table = 'character_spellcasting';

    protected $fillable = [
        'character_id',
        'character_class_id',
        'sync_with_character_class',
        'class',
        'ability',
        'caster_level',
        'caster_type',
        'slot_mode',
        'effective_caster_level_override',
        'spell_save_dc_override',
        'spell_save_dc_bonus',
        'spell_attack_bonus_override',
        'spell_attack_bonus_extra',
        'spell_list_codes',
        'spell_limit',
        'prepared_limit',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'character_id' => 'integer',
        'character_class_id' => 'integer',
        'sync_with_character_class' => 'boolean',
        'caster_level' => 'integer',
        'effective_caster_level_override' => 'integer',
        'spell_save_dc_override' => 'integer',
        'spell_save_dc_bonus' => 'integer',
        'spell_attack_bonus_override' => 'integer',
        'spell_attack_bonus_extra' => 'integer',
        'spell_list_codes' => 'array',
        'spell_limit' => 'integer',
        'prepared_limit' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function characterClass(): BelongsTo
    {
        return $this->belongsTo(
            CharacterClass::class,
            'character_class_id'
        );
    }

    public function spells(): HasMany
    {
        return $this->hasMany(CharacterSpell::class, 'spellcasting_id');
    }

    public function getAbilityModifierAttribute(): int
    {
        $character = $this->relationLoaded('character')
            ? $this->character
            : $this->character()->with('abilities')->first();

        if (! $character) {
            return 0;
        }

        if (! $character->relationLoaded('abilities')) {
            $character->load('abilities');
        }

        $abilities = $character->abilities;

        if (! $abilities) {
            return 0;
        }

        $ability = strtolower(
            trim((string) $this->ability)
        );

        $shortKeys = [
            'strength' => 'str',
            'dexterity' => 'dex',
            'constitution' => 'con',
            'intelligence' => 'int',
            'wisdom' => 'wis',
            'charisma' => 'cha',
        ];

        if (! array_key_exists($ability, $shortKeys)) {
            return 0;
        }

        $shortKey = $shortKeys[$ability];

        $temporaryBonuses = is_array(
            $abilities->temporary_bonuses
        )
            ? $abilities->temporary_bonuses
            : [];

        $overrides = is_array(
            $abilities->overrides
        )
            ? $abilities->overrides
            : [];

        $baseScore = (int) (
            $abilities->{$ability}
            ?? 10
        );

        $hasFullOverride = array_key_exists(
            $ability,
            $overrides
        )
            && $overrides[$ability] !== null
            && $overrides[$ability] !== '';

        $hasShortOverride = array_key_exists(
            $shortKey,
            $overrides
        )
            && $overrides[$shortKey] !== null
            && $overrides[$shortKey] !== '';

        if ($hasFullOverride) {
            $effectiveScore = (int) $overrides[$ability];
        } elseif ($hasShortOverride) {
            $effectiveScore = (int) $overrides[$shortKey];
        } else {
            $temporaryBonus = (int) (
                $temporaryBonuses[$ability]
                ?? $temporaryBonuses[$shortKey]
                ?? 0
            );

            $effectiveScore = $baseScore + $temporaryBonus;
        }

        return (int) floor(
            ($effectiveScore - 10) / 2
        );
    }

    public function getSpellSaveDcAttribute(): int
    {
        if ($this->spell_save_dc_override !== null) {
            return (int) $this->spell_save_dc_override;
        }

        return 8
            + (int) ($this->character?->proficiency_bonus ?? 0)
            + $this->ability_modifier
            + (int) ($this->spell_save_dc_bonus ?? 0);
    }

    public function getSpellAttackBonusAttribute(): int
    {
        if ($this->spell_attack_bonus_override !== null) {
            return (int) $this->spell_attack_bonus_override;
        }

        return (int) ($this->character?->proficiency_bonus ?? 0)
            + $this->ability_modifier
            + (int) ($this->spell_attack_bonus_extra ?? 0);
    }

    public function isPactCaster(): bool
    {
        return $this->caster_type === 'pact';
    }

    public function isAutomatic(): bool
    {
        return $this->slot_mode === 'automatic';
    }
}
