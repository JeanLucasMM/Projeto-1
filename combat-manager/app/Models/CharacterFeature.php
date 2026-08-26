<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterFeature extends Model
{
    protected $fillable = [
        'character_id',

        /*
        |--------------------------------------------------------------------------
        | IdentificaÃ§Ã£o
        |--------------------------------------------------------------------------
        */

        'name',
        'type',
        'source',
        'level_acquired',

        /*
        |--------------------------------------------------------------------------
        | ConteÃºdo
        |--------------------------------------------------------------------------
        */

        'description',

        /*
        |--------------------------------------------------------------------------
        | Rastreador
        |--------------------------------------------------------------------------
        */

        'uses_max',
        'uses_current',
        'recovery',

        /*
        |--------------------------------------------------------------------------
        | Dados adicionais
        |--------------------------------------------------------------------------
        */

        'data',
    ];

    protected $casts = [
        'level_acquired' => 'integer',

        'uses_max' => 'integer',
        'uses_current' => 'integer',

        'data' => 'array',
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

    public function hasCounter(): bool
    {
        return $this->uses_max !== null;
    }

    public function getUsesLabelAttribute(): ?string
    {
        if (!$this->hasCounter()) {
            return null;
        }

        return sprintf(
            '%d/%d',
            (int) ($this->uses_current ?? 0),
            (int) $this->uses_max
        );
    }

    public function getActivationAttribute(): string
    {
        $data = is_array($this->data)
            ? $this->data
            : [];

        return (string) (
            $data['activation']
            ?? 'passive'
        );
    }

    public function getQuickTextAttribute(): ?string
    {
        $data = is_array($this->data)
            ? $this->data
            : [];

        $value = trim(
            (string) (
                $data['quick_text']
                ?? ''
            )
        );

        return $value !== ''
            ? $value
            : null;
    }

    public function getCounterModeAttribute(): string
    {
        $data = is_array($this->data)
            ? $this->data
            : [];

        return (
            $data['counter_mode']
            ?? 'spend'
        ) === 'build'
            ? 'build'
            : 'spend';
    }
}