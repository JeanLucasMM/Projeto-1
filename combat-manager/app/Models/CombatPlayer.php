<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatPlayer extends Model
{
    protected $fillable = [
        'combat_id',
        'character_id',
        'name',
        'initiative',
    ];

    protected $casts = [
        'combat_id' => 'integer',
        'character_id' => 'integer',
        'initiative' => 'integer',
    ];

    /** Mantém o participante do turno ao inserir outros durante uma batalha ativa. */
    protected static function booted(): void
    {
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
        return $this->belongsTo(
            Combat::class
        );
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(
            Character::class
        );
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->character?->name
            ?? $this->name
            ?? 'Personagem';
    }

    public function getIsLinkedAttribute(): bool
    {
        return $this->character_id !== null
            && $this->character !== null;
    }
}
