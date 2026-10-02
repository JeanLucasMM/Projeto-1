<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LootDrop extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_RELEASED = 'released';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'campaign_id',
        'combat_id',
        'created_by_user_id',
        'name',
        'description',
        'image_path',
        'status',
        'closed_reason',
        'is_quick_access',
        'is_shop',
        'shopkeeper_npc_id',
        'shop_attendant_name',
        'shop_image_path',
        'shop_background_path',
        'shop_has_counter',
        'shop_counter_color',
        'shop_background_color',
        'shop_image_zoom',
        'shop_image_position',
        'shop_background_zoom',
        'shop_background_position',
        'released_at',
        'closed_at',
    ];

    protected $casts = [
        'campaign_id' => 'integer',
        'combat_id' => 'integer',
        'created_by_user_id' => 'integer',
        'is_quick_access' => 'boolean',
        'is_shop' => 'boolean',
        'shopkeeper_npc_id' => 'integer',
        'shop_has_counter' => 'boolean',
        'shop_image_zoom' => 'integer',
        'shop_background_zoom' => 'integer',
        'released_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function shopkeeperNpc(): BelongsTo
    {
        return $this->belongsTo(
            Npc::class,
            'shopkeeper_npc_id'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(LootDropItem::class);
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(LootCurrencyEntry::class);
    }
}