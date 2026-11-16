<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSpellSlot extends Model
{
    protected $fillable = [
        'character_id',
        'slot_type',
        'slot_level',
        'current',
        'maximum',
        'calculated_maximum',
        'manual_maximum',
        'recovery',
    ];

    protected $casts = [
        'character_id' => 'integer',
        'slot_level' => 'integer',
        'current' => 'integer',
        'maximum' => 'integer',
        'calculated_maximum' => 'integer',
        'manual_maximum' => 'integer',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function hasAvailableSlot(): bool
    {
        return (int) $this->current > 0;
    }

    public function consumeOne(): bool
    {
        if (! $this->hasAvailableSlot()) {
            return false;
        }

        $this->current = max(0, (int) $this->current - 1);

        return $this->save();
    }

    public function restore(): bool
    {
        $this->current = max(0, (int) $this->maximum);

        return $this->save();
    }

    public function isPact(): bool
    {
        return str_starts_with((string) $this->slot_type, 'pact:');
    }
}
