<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterItem extends Model
{
    protected $fillable = [
        'character_id',

        'name',
        'type',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Imagem
        |--------------------------------------------------------------------------
        */

        'image_path',

        'quantity',
        'weight',

        'equipped',

        /*
        |--------------------------------------------------------------------------
        | Item mágico
        |--------------------------------------------------------------------------
        */

        'is_magical',
        'rarity',

        'requires_attunement',
        'attuned',

        /*
        |--------------------------------------------------------------------------
        | Maldição
        |--------------------------------------------------------------------------
        */

        'is_cursed',
        'curse_description',
        'curse_revealed',

        /*
        |--------------------------------------------------------------------------
        | Combate
        |--------------------------------------------------------------------------
        */

        'armor_class',
        'damage',
        'attack_bonus',
        'damage_bonus',

        /*
        |--------------------------------------------------------------------------
        | Dados estruturados
        |--------------------------------------------------------------------------
        */

        'ability_bonuses',
        'properties',
        'modifiers',

        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'weight' => 'decimal:2',

        'equipped' => 'boolean',

        'is_magical' => 'boolean',

        'requires_attunement' => 'boolean',
        'attuned' => 'boolean',

        'is_cursed' => 'boolean',
        'curse_revealed' => 'boolean',

        'armor_class' => 'integer',
        'attack_bonus' => 'integer',
        'damage_bonus' => 'integer',

        'ability_bonuses' => 'array',
        'properties' => 'array',
        'modifiers' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getIsAttunableAttribute(): bool
    {
        return $this->is_magical
            && $this->requires_attunement;
    }

    public function getIsCurrentlyAttunedAttribute(): bool
    {
        return $this->requires_attunement
            && $this->attuned;
    }

    public function getCanShowCurseAttribute(): bool
    {
        return $this->is_cursed
            && $this->curse_revealed;
    }

    public function getRarityLabelAttribute(): ?string
    {
        if (!$this->rarity) {
            return null;
        }

        return match ($this->rarity) {
            'common' => 'Comum',
            'uncommon' => 'Incomum',
            'rare' => 'Raro',
            'very_rare' => 'Muito Raro',
            'legendary' => 'Lendário',
            'artifact' => 'Artefato',

            default => ucfirst(
                str_replace('_', ' ', $this->rarity)
            ),
        };
    }
}