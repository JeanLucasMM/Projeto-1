<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LootClaim extends Model
{
    protected $fillable = [
        'loot_drop_item_id',
        'character_id',
        'user_id',
        'character_item_id',
        'quantity',
        'claim_type',
        'price_paid_copper_value',
        'claimed_at',
        'reversed_at',
        'reversed_by_user_id',
    ];

    protected $casts = [
        'loot_drop_item_id' => 'integer',
        'character_id' => 'integer',
        'user_id' => 'integer',
        'character_item_id' => 'integer',
        'quantity' => 'integer',
        'price_paid_copper_value' => 'integer',
        'claimed_at' => 'datetime',
        'reversed_at' => 'datetime',
        'reversed_by_user_id' => 'integer',
    ];

    public function lootItem(): BelongsTo
    {
        return $this->belongsTo(
            LootDropItem::class,
            'loot_drop_item_id'
        );
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(
            Character::class
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function characterItem(): BelongsTo
    {
        return $this->belongsTo(
            CharacterItem::class
        );
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reversed_by_user_id'
        );
    }
}
