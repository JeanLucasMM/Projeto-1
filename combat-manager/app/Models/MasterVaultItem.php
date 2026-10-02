<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterVaultItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'type', 'description', 'image_path',
        'default_quantity', 'weight', 'is_magical', 'rarity',
        'requires_attunement', 'is_cursed', 'curse_name', 'curse_description',
        'armor_class', 'damage', 'attack_bonus', 'damage_bonus',
        'ability_bonuses', 'properties', 'modifiers', 'notes',
        'tags', 'favorite',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'default_quantity' => 'integer',
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
        'tags' => 'array',
        'favorite' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lootItems(): HasMany
    {
        return $this->hasMany(LootDropItem::class, 'vault_item_id');
    }
}
