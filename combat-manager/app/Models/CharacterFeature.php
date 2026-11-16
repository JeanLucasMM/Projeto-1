<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CharacterFeature extends Model
{
    protected $fillable = [
        'character_id', 'name', 'type', 'source', 'level_acquired',
        'description', 'uses_max', 'uses_current', 'recovery', 'data',
    ];

    protected $casts = [
        'level_acquired' => 'integer', 'uses_max' => 'integer',
        'uses_current' => 'integer', 'data' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function effectState(): HasOne
    {
        return $this->hasOne(CharacterFeatureEffectState::class, 'character_feature_id');
    }

    public function hasCounter(): bool
    {
        return $this->uses_max !== null;
    }

    public function getUsesLabelAttribute(): ?string
    {
        return !$this->hasCounter() ? null : sprintf('%d/%d',
            (int) ($this->uses_current ?? 0), (int) $this->uses_max);
    }

    public function getActivationAttribute(): string
    {
        $data = is_array($this->data) ? $this->data : [];
        return (string) ($data['activation'] ?? 'passive');
    }

    public function getQuickTextAttribute(): ?string
    {
        $data = is_array($this->data) ? $this->data : [];
        $text = trim((string) ($data['quick_text'] ?? ''));
        return $text !== '' ? $text : null;
    }

    public function getColumnAttribute(): string
    {
        $data = is_array($this->data) ? $this->data : [];
        return ($data['column'] ?? 'left') === 'right' ? 'right' : 'left';
    }

    public function getCounterModeAttribute(): string
    {
        $data = is_array($this->data) ? $this->data : [];
        return ($data['counter_mode'] ?? 'spend') === 'build' ? 'build' : 'spend';
    }
}