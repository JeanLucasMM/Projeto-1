<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CharacterSpellLoadout extends Model
{
    protected $fillable = [
        'character_id',
        'spellcasting_id',
        'name',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'character_id' => 'integer',
        'spellcasting_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function spellcasting(): BelongsTo
    {
        return $this->belongsTo(
            CharacterSpellcasting::class,
            'spellcasting_id'
        );
    }

    public function spells(): BelongsToMany
    {
        return $this->belongsToMany(
            Spell::class,
            'character_spell_loadout_spells',
            'spell_loadout_id',
            'spell_id'
        )->withTimestamps();
    }
}
