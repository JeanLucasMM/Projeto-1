<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterFeatureEffectState extends Model
{
    protected $fillable = [
        'character_id', 'character_feature_id', 'is_active', 'requires_concentration',
        'duration_total_rounds', 'duration_remaining', 'roll_expression', 'rolled_total',
        'combat_id', 'activated_round', 'activated_turn', 'last_processed_round', 'activated_at',
    ];

    protected $casts = [
        'character_id' => 'integer', 'character_feature_id' => 'integer',
        'is_active' => 'boolean', 'requires_concentration' => 'boolean',
        'duration_total_rounds' => 'integer', 'duration_remaining' => 'integer',
        'rolled_total' => 'integer', 'combat_id' => 'integer',
        'activated_round' => 'integer', 'activated_turn' => 'integer',
        'last_processed_round' => 'integer', 'activated_at' => 'datetime',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(CharacterFeature::class, 'character_feature_id');
    }
}
