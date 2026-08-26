<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSpellSlot extends Model
{
    protected $table = 'character_spell_slots';

    protected $fillable = [
        'character_id',
        'slot_type',
        'slot_level',
        'current',
        'maximum',
        'metadata',
    ];

    protected $casts = [
        'slot_level' => 'integer',
        'current' => 'integer',
        'maximum' => 'integer',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function isPactMagic(): bool
    {
        return $this->slot_type === 'pact';
    }

    public function isEmpty(): bool
    {
        return $this->current <= 0;
    }

    public function hasAvailableSlot(): bool
    {
        return $this->current > 0;
    }

    public function consume(int $amount = 1): bool
    {
        if ($amount <= 0) {
            return true;
        }

        if ($this->current < $amount) {
            return false;
        }

        $this->current -= $amount;

        $this->save();

        return true;
    }

    public function restore(?int $amount = null): void
    {
        if ($amount === null) {
            $this->current = $this->maximum;
        } else {
            $this->current = min(
                $this->maximum,
                $this->current + max(0, $amount)
            );
        }

        $this->save();
    }
}