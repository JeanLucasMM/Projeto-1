<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LootCurrencyRecipient extends Model
{
    protected $fillable = [
        'loot_currency_entry_id', 'character_id',
        'copper_allocated', 'silver_allocated', 'electrum_allocated',
        'gold_allocated', 'platinum_allocated', 'claimed_at',
    ];

    protected $casts = [
        'loot_currency_entry_id' => 'integer',
        'character_id' => 'integer',
        'copper_allocated' => 'integer',
        'silver_allocated' => 'integer',
        'electrum_allocated' => 'integer',
        'gold_allocated' => 'integer',
        'platinum_allocated' => 'integer',
        'claimed_at' => 'datetime',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(LootCurrencyEntry::class, 'loot_currency_entry_id');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
