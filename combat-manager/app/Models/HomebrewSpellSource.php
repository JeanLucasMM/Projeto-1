<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomebrewSpellSource extends Model
{
    protected $fillable = [
        'provider',
        'source_code',
        'name',
        'authors',
        'url',
        'metadata',
        'origin_files',
        'spell_count',
        'is_available',
        'last_seen_at',
    ];

    protected $casts = [
        'authors' => 'array',
        'metadata' => 'array',
        'origin_files' => 'array',
        'spell_count' => 'integer',
        'is_available' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function spells(): HasMany
    {
        return $this->hasMany(
            Spell::class,
            'homebrew_spell_source_id'
        );
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(
            Campaign::class,
            'campaign_homebrew_spell_sources',
            'homebrew_spell_source_id',
            'campaign_id'
        )
            ->withPivot('enabled_by_user_id')
            ->withTimestamps();
    }
}
