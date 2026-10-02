<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LootCurrencyEntry extends Model
{
    public const RELEASE_HIDDEN = 'hidden';
    public const RELEASED = 'released';
    public const RELEASE_WITHDRAWN = 'withdrawn';

    public const VISIBILITY_PARTY = 'party';
    public const VISIBILITY_RESTRICTED = 'restricted';

    public const DISTRIBUTION_FIRST_CLAIM = 'first_claim';
    public const DISTRIBUTION_SPLIT = 'split';

    protected $fillable = [
        'loot_drop_id', 'label',
        'copper_total', 'silver_total', 'electrum_total', 'gold_total', 'platinum_total',
        'copper_remaining', 'silver_remaining', 'electrum_remaining', 'gold_remaining', 'platinum_remaining',
        'release_status', 'visibility_mode', 'distribution_mode',
        'released_at', 'withdrawn_at', 'depleted_at',
    ];

    protected $casts = [
        'loot_drop_id' => 'integer',
        'copper_total' => 'integer',
        'silver_total' => 'integer',
        'electrum_total' => 'integer',
        'gold_total' => 'integer',
        'platinum_total' => 'integer',
        'copper_remaining' => 'integer',
        'silver_remaining' => 'integer',
        'electrum_remaining' => 'integer',
        'gold_remaining' => 'integer',
        'platinum_remaining' => 'integer',
        'released_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'depleted_at' => 'datetime',
    ];

    public function lootDrop(): BelongsTo
    {
        return $this->belongsTo(LootDrop::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(LootCurrencyRecipient::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(LootCurrencyClaim::class);
    }

    public function isEmpty(): bool
    {
        return collect([
            $this->copper_remaining,
            $this->silver_remaining,
            $this->electrum_remaining,
            $this->gold_remaining,
            $this->platinum_remaining,
        ])->every(fn ($value) => (int) $value <= 0);
    }
}
