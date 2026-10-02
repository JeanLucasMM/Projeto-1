<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LootItemRecipient extends Model
{
    protected $fillable = ['loot_drop_item_id', 'character_id'];

    protected $casts = [
        'loot_drop_item_id' => 'integer',
        'character_id' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(LootDropItem::class, 'loot_drop_item_id');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
