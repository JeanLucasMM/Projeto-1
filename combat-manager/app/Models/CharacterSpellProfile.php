<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSpellProfile extends Model
{
    protected $fillable = [
        'character_spell_id', 'spellcasting_id', 'known', 'prepared', 'always_prepared',
    ];

    protected $casts = [
        'known' => 'boolean', 'prepared' => 'boolean', 'always_prepared' => 'boolean',
    ];

    public function characterSpell(): BelongsTo
    {
        return $this->belongsTo(CharacterSpell::class, 'character_spell_id');
    }

    public function spellcasting(): BelongsTo
    {
        return $this->belongsTo(CharacterSpellcasting::class, 'spellcasting_id');
    }
}
