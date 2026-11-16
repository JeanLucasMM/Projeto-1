<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterSpell extends Model
{
    protected $fillable = [
        'character_id',
        'spell_id',
        'spellcasting_id',
        'source_type',
        'source_class',
        'known',
        'prepared',
        'always_prepared',
        'free_casts_current',
        'free_casts_max',
        'free_casts_recovery',
        'cast_without_slot',
        'overrides',
        'personal_notes',
        'personal_media_path',
    ];

    protected $casts = [
        'character_id' => 'integer',
        'spell_id' => 'integer',
        'spellcasting_id' => 'integer',
        'known' => 'boolean',
        'prepared' => 'boolean',
        'always_prepared' => 'boolean',
        'free_casts_current' => 'integer',
        'free_casts_max' => 'integer',
        'cast_without_slot' => 'boolean',
        'overrides' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }

    public function spellcasting(): BelongsTo
    {
        return $this->belongsTo(CharacterSpellcasting::class, 'spellcasting_id');
    }

    public function castingProfiles(): HasMany
    {
        return $this->hasMany(CharacterSpellProfile::class, 'character_spell_id');
    }

    public function isPrepared(): bool
    {
        return (bool) ($this->prepared || $this->always_prepared);
    }

    public function isKnown(): bool
    {
        return (bool) $this->known;
    }

    public function requiresConcentration(): bool
    {
        return (bool) $this->spell?->concentration;
    }

    public function hasFreeCast(): bool
    {
        return $this->free_casts_current !== null
            && (int) $this->free_casts_current > 0;
    }
}
