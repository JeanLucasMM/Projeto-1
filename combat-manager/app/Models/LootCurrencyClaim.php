<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LootCurrencyClaim extends Model
{
    protected $fillable = [
        'loot_currency_entry_id', 'character_id', 'user_id',
        'copper', 'silver', 'electrum', 'gold', 'platinum', 'claimed_at',
    ];

    protected $casts = [
        'loot_currency_entry_id' => 'integer',
        'character_id' => 'integer',
        'user_id' => 'integer',
        'copper' => 'integer',
        'silver' => 'integer',
        'electrum' => 'integer',
        'gold' => 'integer',
        'platinum' => 'integer',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
