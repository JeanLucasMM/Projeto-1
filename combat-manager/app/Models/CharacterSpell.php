<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSpell extends Model
{
    protected $table = 'character_spells';

    protected $fillable = [
        'character_id',
        'spell_id',
        'source_type',
        'source_class',
        'known',
        'prepared',
        'overrides',
    ];

    protected $casts = [
        'known' => 'boolean',
        'prepared' => 'boolean',
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

    public function isPrepared(): bool
    {
        return $this->prepared;
    }

    public function isKnown(): bool
    {
        return $this->known;
    }

    public function requiresConcentration(): bool
    {
        return (bool) ($this->spell?->concentration ?? false);
    }

    public function canCast(): bool
    {
        if (!$this->known) {
            return false;
        }

        if ($this->spell?->level === 0) {
            return true;
        }

        return $this->prepared;
    }
}