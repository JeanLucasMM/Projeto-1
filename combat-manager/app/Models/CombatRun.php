<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CombatRun extends Model
{
    protected $fillable = [
        'combat_id', 'started_at', 'ended_at', 'end_round', 'end_turn', 'metrics_started_at', 'turns_advanced', 'final_snapshot', 'active_seconds', 'clock_started_at', 'clock_paused', 'clock_complete',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'end_round' => 'integer',
        'end_turn' => 'integer',
        'metrics_started_at' => 'datetime',
        'turns_advanced' => 'integer',
        'final_snapshot' => 'array',
        'active_seconds' => 'integer',
        'clock_started_at' => 'datetime',
        'clock_paused' => 'boolean',
        'clock_complete' => 'boolean',
    ];

    public function archive(): HasOne
    {
        return $this->hasOne(CombatRunArchive::class);
    }

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }
}
