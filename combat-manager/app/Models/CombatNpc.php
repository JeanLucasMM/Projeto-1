<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatNpc extends Model
{
    public const SIDE_ENEMY = 'enemy';
    public const SIDE_ALLY = 'ally';

    protected $fillable = [
        'combat_id',
        'npc_id',
        'initiative',
        'current_hp',
        'max_hp',
        'temporary_hp',
        'is_dead',
        'resource_trackers',
        'side',
    ];

    protected $casts = [
        'is_dead' => 'boolean',
        'resource_trackers' => 'array',
        'combat_id' => 'integer',
        'npc_id' => 'integer',
        'initiative' => 'integer',
        'current_hp' => 'integer',
        'max_hp' => 'integer',
        'temporary_hp' => 'integer',
    ];

    /** Mantém o participante do turno ao inserir outros durante uma batalha ativa. */
    protected static function booted(): void
    {
        static::updated(static function (self $participant): void {
            app(\App\Services\Combats\CombatRunMetricsRecorder::class)->npcChanged($participant);
        });
        static::deleting(static function (self $participant): void {
            app(\App\Services\Combats\CombatRunParticipantRecorder::class)->left($participant);
        });
        static::created(static function (self $participant): void {
            app(\App\Services\CombatTurnInsertionService::class)
                ->reanchorAfterInsertion($participant);
            app(\App\Services\Combats\CombatRunParticipantRecorder::class)->joined($participant);
        });
    }

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }

    public function isAlly(): bool
    {
        return $this->side === self::SIDE_ALLY;
    }

    public function getResource(string $feature, int $default): int
    {
        $resources = $this->resource_trackers ?? [];
        return (int) ($resources[$feature] ?? $default);
    }

    public function setResource(string $feature, int $value): void
    {
        $resources = $this->resource_trackers ?? [];
        $resources[$feature] = $value;
        $this->resource_trackers = $resources;
        $this->save();
    }
}
