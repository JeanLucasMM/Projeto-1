<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Character extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'species',
        'background',
        'alignment',
        'level',
        'experience_points',
        'proficiency_bonus',
        'heroic_inspiration',
        'image_path',
    ];

    protected $casts = [
        'level' => 'integer',
        'experience_points' => 'integer',
        'proficiency_bonus' => 'integer',
        'heroic_inspiration' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Usuário
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Classes
    |--------------------------------------------------------------------------
    */

    public function classes(): HasMany
    {
        return $this->hasMany(CharacterClass::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Atributos
    |--------------------------------------------------------------------------
    */

    public function abilities(): HasOne
    {
        return $this->hasOne(CharacterAbility::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Perícias
    |--------------------------------------------------------------------------
    */

    public function skills(): HasMany
    {
        return $this->hasMany(CharacterSkill::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Salvaguardas
    |--------------------------------------------------------------------------
    */

    public function savingThrows(): HasMany
    {
        return $this->hasMany(CharacterSavingThrow::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Características
    |--------------------------------------------------------------------------
    */

    public function features(): HasMany
    {
        return $this->hasMany(CharacterFeature::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Recursos
    |--------------------------------------------------------------------------
    */

    public function resources(): HasMany
    {
        return $this->hasMany(CharacterResource::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Combate
    |--------------------------------------------------------------------------
    */

    public function combat(): HasOne
    {
        return $this->hasOne(CharacterCombat::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Magias
    |--------------------------------------------------------------------------
    */

    public function spellcasting(): HasMany
    {
        return $this->hasMany(CharacterSpellcasting::class);
    }

    public function characterSpells(): HasMany
    {
        return $this->hasMany(CharacterSpell::class);
    }

    /*
    | Alias de compatibilidade.
    |
    | Permite:
    |
    | $character->spells
    |
    | e também:
    |
    | ->with('spells.spell')
    |
    */

    public function spells(): HasMany
    {
        return $this->characterSpells();
    }

    public function spellSlots(): HasMany
    {
        return $this->hasMany(CharacterSpellSlot::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Itens
    |--------------------------------------------------------------------------
    */

    public function items(): HasMany
    {
        return $this->hasMany(CharacterItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Dados de Vida
    |--------------------------------------------------------------------------
    |
    | Neste momento os dados de vida estão armazenados como JSON em
    | character_combat.hit_dice.
    |
    | Portanto não existe relação hitDice().
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Nível total das classes
    |--------------------------------------------------------------------------
    */

    public function getTotalClassLevelsAttribute(): int
    {
        if ($this->relationLoaded('classes')) {
            return (int) $this->classes->sum('level');
        }

        return (int) $this->classes()->sum('level');
    }

    /*
    |--------------------------------------------------------------------------
    | Vida
    |--------------------------------------------------------------------------
    */

    public function getEffectiveMaxHpAttribute(): int
    {
        if (!$this->combat) {
            return 0;
        }

        return max(
            0,
            (int) $this->combat->max_hp +
            (int) $this->combat->temporary_max_hp
        );
    }

    public function getHpLabelAttribute(): string
    {
        if (!$this->combat) {
            return '—';
        }

        return sprintf(
            '%d/%d',
            $this->combat->current_hp,
            $this->effective_max_hp
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Classe principal
    |--------------------------------------------------------------------------
    */

    public function getPrimaryClassAttribute(): ?CharacterClass
    {
        if ($this->relationLoaded('classes')) {
            $classes = $this->classes;
        } else {
            $classes = $this->classes()->get();
        }

        $primary = $classes->firstWhere('is_primary', true);

        if ($primary) {
            return $primary;
        }

        return $classes
            ->sortByDesc('level')
            ->sortBy('sort_order')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Rótulo das classes
    |--------------------------------------------------------------------------
    */

    public function getClassLabelAttribute(): string
    {
        if (!$this->relationLoaded('classes')) {
            $this->load('classes');
        }

        if ($this->classes->isEmpty()) {
            return '—';
        }

        return $this->classes
            ->sortBy('sort_order')
            ->map(function (CharacterClass $class) {
                $label = $class->class;

                if ($class->level > 0) {
                    $label .= ' ' . $class->level;
                }

                return $label;
            })
            ->implode(' / ');
    }

    /*
    |--------------------------------------------------------------------------
    | Attaques
    |--------------------------------------------------------------------------
    */

    public function attacks(): HasMany
{
    return $this->hasMany(
        CharacterAttack::class
    )->orderBy('sort_order');
}

public function wallet(): HasOne
{
    return $this->hasOne(CharacterWallet::class);
}
}