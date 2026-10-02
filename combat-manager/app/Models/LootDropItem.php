<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LootDropItem extends Model
{
    public const RELEASE_HIDDEN = 'hidden';
    public const RELEASED = 'released';
    public const RELEASE_WITHDRAWN = 'withdrawn';

    public const VISIBILITY_PARTY = 'party';
    public const VISIBILITY_RESTRICTED = 'restricted';

    protected $fillable = [
        'loot_drop_id',
        'vault_item_id',
        'name',
        'type',
        'description',
        'image_path',
        'weight',
        'is_magical',
        'rarity',
        'requires_attunement',
        'is_cursed',
        'curse_name',
        'curse_description',
        'armor_class',
        'damage',
        'attack_bonus',
        'damage_bonus',
        'ability_bonuses',
        'properties',
        'modifiers',
        'notes',
        'quantity_total',
        'quantity_remaining',
        'release_status',
        'visibility_mode',
        'sort_order',
        'released_at',
        'withdrawn_at',
        'depleted_at',

        /*
        |--------------------------------------------------------------------------
        | Loja
        |--------------------------------------------------------------------------
        |
        | O preço fica salvo no snapshot do item dentro da sessão de loja.
        | Assim alterações futuras no Vault não mudam uma loja que já foi criada.
        |
        */
        'price_copper',
        'price_silver',
        'price_electrum',
        'price_gold',
        'price_platinum',
    ];

    protected $casts = [
        'loot_drop_id' => 'integer',
        'vault_item_id' => 'integer',
        'weight' => 'decimal:2',
        'is_magical' => 'boolean',
        'requires_attunement' => 'boolean',
        'is_cursed' => 'boolean',
        'armor_class' => 'integer',
        'attack_bonus' => 'integer',
        'damage_bonus' => 'integer',
        'ability_bonuses' => 'array',
        'properties' => 'array',
        'modifiers' => 'array',
        'quantity_total' => 'integer',
        'quantity_remaining' => 'integer',
        'sort_order' => 'integer',
        'released_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'depleted_at' => 'datetime',
        'price_copper' => 'integer',
        'price_silver' => 'integer',
        'price_electrum' => 'integer',
        'price_gold' => 'integer',
        'price_platinum' => 'integer',
    ];

    public function lootDrop(): BelongsTo
    {
        return $this->belongsTo(LootDrop::class);
    }

    public function vaultItem(): BelongsTo
    {
        return $this->belongsTo(MasterVaultItem::class, 'vault_item_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(LootItemRecipient::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(LootClaim::class);
    }
}
