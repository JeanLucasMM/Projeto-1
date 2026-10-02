<?php

namespace App\Services\Loot;

use App\Models\Character;
use App\Models\CharacterItem;
use App\Models\CharacterWallet;
use App\Models\LootClaim;
use App\Models\LootCurrencyClaim;
use App\Models\LootCurrencyEntry;
use App\Models\LootCurrencyRecipient;
use App\Models\LootDrop;
use App\Models\LootDropItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LootClaimService
{
    private const COINS = [
        'copper',
        'silver',
        'electrum',
        'gold',
        'platinum',
    ];

    private const COIN_VALUES = [
        'copper' => 1,
        'silver' => 10,
        'electrum' => 50,
        'gold' => 100,
        'platinum' => 1000,
    ];

    private const WALLET_MAX_PER_COIN = 999999999;

    public function availableForCharacter(Character $character): array
    {
        $campaignIds = DB::table('campaign_characters')
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->pluck('campaign_id');

        if ($campaignIds->isEmpty()) {
            return [
                'items' => collect(),
                'currencies' => collect(),
            ];
        }

        $items = LootDropItem::query()
            ->with([
                'lootDrop:id,campaign_id,name,status',
                'lootDrop.campaign:id,name',
            ])
            ->where('release_status', LootDropItem::RELEASED)
            ->where('quantity_remaining', '>', 0)
            ->whereHas('lootDrop', function ($query) use ($campaignIds) {
                $query
                    ->whereIn('campaign_id', $campaignIds)
                    ->where('is_shop', false)
                    ->where('status', '!=', 'closed');
            })
            ->where(function ($query) use ($character) {
                $query
                    ->where('visibility_mode', LootDropItem::VISIBILITY_PARTY)
                    ->orWhere(function ($restricted) use ($character) {
                        $restricted
                            ->where(
                                'visibility_mode',
                                LootDropItem::VISIBILITY_RESTRICTED
                            )
                            ->whereHas('recipients', function ($recipients) use ($character) {
                                $recipients->where('character_id', $character->id);
                            });
                    });
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $currencies = LootCurrencyEntry::query()
            ->with([
                'lootDrop:id,campaign_id,name,status',
                'lootDrop.campaign:id,name',
                'recipients' => function ($query) use ($character) {
                    $query->where('character_id', $character->id);
                },
            ])
            ->where('release_status', LootCurrencyEntry::RELEASED)
            ->whereHas('lootDrop', function ($query) use ($campaignIds) {
                $query
                    ->whereIn('campaign_id', $campaignIds)
                    ->where('is_shop', false)
                    ->where('status', '!=', 'closed');
            })
            ->where(function ($query) use ($character) {
                $query
                    ->where('visibility_mode', LootCurrencyEntry::VISIBILITY_PARTY)
                    ->orWhere(function ($restricted) use ($character) {
                        $restricted
                            ->where(
                                'visibility_mode',
                                LootCurrencyEntry::VISIBILITY_RESTRICTED
                            )
                            ->whereHas('recipients', function ($recipients) use ($character) {
                                $recipients->where('character_id', $character->id);
                            });
                    });
            })
            ->get()
            ->filter(function (LootCurrencyEntry $entry) use ($character) {
                if ($entry->distribution_mode !== LootCurrencyEntry::DISTRIBUTION_SPLIT) {
                    return !$entry->isEmpty();
                }

                $recipient = $entry->recipients
                    ->firstWhere('character_id', $character->id);

                return $recipient
                    && $recipient->claimed_at === null
                    && collect(self::COINS)->contains(
                        fn ($coin) => (int) $recipient->{$coin . '_allocated'} > 0
                    );
            })
            ->values();

        return [
            'items' => $items,
            'currencies' => $currencies,
        ];
    }

    public function claimItem(
        Character $character,
        LootDropItem $lootItem,
        int $quantity = 1
    ): CharacterItem {
        $quantity = max(1, $quantity);

        return DB::transaction(function () use ($character, $lootItem, $quantity) {
            $locked = LootDropItem::query()
                ->with('lootDrop')
                ->whereKey($lootItem->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertCharacterCanSeeItem($character, $locked);

            if ((int) $locked->quantity_remaining < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'A quantidade solicitada não está mais disponível.',
                ]);
            }

            $characterItem = CharacterItem::query()->create([
                'character_id' => $character->id,
                'name' => $locked->name,
                'type' => $locked->type,
                'description' => $locked->description,
                'image_path' => $this->copyLootImageToCharacter(
                    $locked->image_path,
                    $character
                ),
                'quantity' => $quantity,
                'weight' => $locked->weight,
                'equipped' => false,
                'is_magical' => (bool) $locked->is_magical,
                'rarity' => $locked->rarity,
                'requires_attunement' => (bool) $locked->requires_attunement,
                'attuned' => false,
                'is_cursed' => (bool) $locked->is_cursed,
                'curse_name' => $locked->curse_name,
                'curse_description' => $locked->curse_description,
                'curse_revealed' => false,
                'armor_class' => $locked->armor_class,
                'damage' => $locked->damage,
                'attack_bonus' => $locked->attack_bonus,
                'damage_bonus' => $locked->damage_bonus,
                'ability_bonuses' => $locked->ability_bonuses ?? [],
                'properties' => $locked->properties ?? [],
                'modifiers' => $locked->modifiers ?? [],
                'notes' => $locked->notes,
            ]);

            $locked->quantity_remaining =
                (int) $locked->quantity_remaining - $quantity;

            if ($locked->quantity_remaining <= 0) {
                $locked->quantity_remaining = 0;
                $locked->depleted_at = now();
            }

            $locked->save();

            LootClaim::query()->create([
                'loot_drop_item_id' => $locked->id,
                'character_id' => $character->id,
                'user_id' => $character->user_id,
                'character_item_id' => $characterItem->id,
                'quantity' => $quantity,
                'claim_type' => 'loot',
                'price_paid_copper_value' => 0,
                'claimed_at' => now(),
            ]);

            $this->syncDropCompletionLocked(
                (int) $locked->loot_drop_id
            );

            return $characterItem->fresh();
        });
    }

    public function claimCurrency(
        Character $character,
        LootCurrencyEntry $entry,
        array $requestedAmounts = []
    ): array {
        return DB::transaction(function () use (
            $character,
            $entry,
            $requestedAmounts
        ) {
            $locked = LootCurrencyEntry::query()
                ->with('lootDrop')
                ->whereKey($entry->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertCharacterCanSeeCurrency($character, $locked);

            if (
                $locked->distribution_mode
                === LootCurrencyEntry::DISTRIBUTION_SPLIT
            ) {
                $amounts =
                    $this->splitAllocationForCharacter(
                        $character,
                        $locked
                    );
            } elseif (
                $locked->distribution_mode
                === 'open'
            ) {
                $amounts =
                    $this->requestedCurrency(
                        $locked,
                        $requestedAmounts
                    );
            } else {
                $amounts =
                    $this->remainingCurrency($locked);
            }

            if (
                collect($amounts)
                    ->every(
                        fn ($value) =>
                            (int) $value <= 0
                    )
            ) {
                throw ValidationException::withMessages([
                    'currency' =>
                        'Informe ao menos uma moeda para retirar.',
                ]);
            }

            $wallet = CharacterWallet::query()->firstOrCreate(
                ['character_id' => $character->id],
                [
                    'copper' => 0,
                    'silver' => 0,
                    'electrum' => 0,
                    'gold' => 0,
                    'platinum' => 0,
                ]
            );

            $wallet = CharacterWallet::query()
                ->whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            foreach (self::COINS as $coin) {
                $newValue =
                    (int) $wallet->{$coin}
                    +
                    (int) $amounts[$coin];

                if ($newValue > 999999999) {
                    throw ValidationException::withMessages([
                        $coin =>
                            'A bolsa excederia o limite permitido para esta moeda.',
                    ]);
                }

                $wallet->{$coin} = $newValue;

                $remainingField =
                    $coin . '_remaining';

                $locked->{$remainingField} = max(
                    0,
                    (int) $locked->{$remainingField}
                    -
                    (int) $amounts[$coin]
                );
            }

            $wallet->save();

            if (
                $locked->distribution_mode
                === LootCurrencyEntry::DISTRIBUTION_SPLIT
            ) {
                LootCurrencyRecipient::query()
                    ->where(
                        'loot_currency_entry_id',
                        $locked->id
                    )
                    ->where(
                        'character_id',
                        $character->id
                    )
                    ->update([
                        'claimed_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            if ($locked->isEmpty()) {
                $locked->depleted_at = now();
            } else {
                $locked->depleted_at = null;
            }

            $locked->save();

            LootCurrencyClaim::query()->create([
                'loot_currency_entry_id' => $locked->id,
                'character_id' => $character->id,
                'user_id' => $character->user_id,
                ...$amounts,
                'claimed_at' => now(),
            ]);

            $this->syncDropCompletionLocked(
                (int) $locked->loot_drop_id
            );

            return [
                'wallet' => $wallet->fresh(),
                'claimed' => $amounts,
                'entry' => $locked->fresh(),
            ];
        });
    }

    public function reverseItemClaim(
        LootClaim $claim,
        int $reversedByUserId
    ): array {
        $result = DB::transaction(
            function () use (
                $claim,
                $reversedByUserId
            ): array {
                $lockedClaim =
                    LootClaim::query()
                        ->whereKey(
                            $claim->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                if (
                    $lockedClaim->reversed_at
                    !== null
                ) {
                    throw ValidationException::withMessages([
                        'claim' =>
                            'Este resgate já foi recolhido.',
                    ]);
                }

                if (
                    ($lockedClaim->claim_type ?? 'loot')
                    === 'shop'
                ) {
                    throw ValidationException::withMessages([
                        'claim' =>
                            'Uma compra de loja não pode ser recolhida como Espólio.',
                    ]);
                }

                $lootItem =
                    LootDropItem::query()
                        ->whereKey(
                            $lockedClaim
                                ->loot_drop_item_id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $quantity =
                    max(
                        1,
                        (int) $lockedClaim
                            ->quantity
                    );

                $characterItem =
                    $lockedClaim
                        ->character_item_id
                    ? CharacterItem::query()
                        ->with([
                            'character:id,name,user_id',
                        ])
                        ->whereKey(
                            $lockedClaim
                                ->character_item_id
                        )
                        ->lockForUpdate()
                        ->first()
                    : null;

                if (!$characterItem) {
                    throw ValidationException::withMessages([
                        'claim' =>
                            'O item desse resgate não está mais no inventário. Não é seguro recriá-lo automaticamente no Espólio.',
                    ]);
                }

                if (
                    (int) $characterItem->quantity
                    <
                    $quantity
                ) {
                    throw ValidationException::withMessages([
                        'claim' =>
                            'A quantidade desse item no inventário foi alterada ou parte dele foi transferida. Recolha manualmente antes de devolver o resgate ao Espólio.',
                    ]);
                }

                $holderName =
                    $characterItem
                        ->character
                        ?->name;

                $imagePathToDelete =
                    null;

                if (
                    (int) $characterItem->quantity
                    ===
                    $quantity
                ) {
                    $imagePathToDelete =
                        is_string(
                            $characterItem
                                ->image_path
                        )
                            ? trim(
                                $characterItem
                                    ->image_path
                            )
                            : null;

                    $characterItem
                        ->delete();
                } else {
                    $characterItem
                        ->quantity =
                            (int) $characterItem
                                ->quantity
                            -
                            $quantity;

                    $characterItem
                        ->save();
                }

                $wasDepleted =
                    (int) $lootItem
                        ->quantity_remaining
                    <=
                    0;

                $lootItem
                    ->quantity_remaining =
                        min(
                            (int) $lootItem
                                ->quantity_total,
                            (int) $lootItem
                                ->quantity_remaining
                            +
                            $quantity
                        );

                $lootItem
                    ->depleted_at =
                        null;

                if (
                    $wasDepleted
                    &&
                    $lootItem->release_status
                    ===
                    LootDropItem::RELEASED
                ) {
                    $lootItem
                        ->release_status =
                            LootDropItem::RELEASE_WITHDRAWN;

                    $lootItem
                        ->withdrawn_at =
                            now();
                }

                $lootItem
                    ->save();

                $this->reopenAutoClosedDropLocked(
                    (int) $lootItem->loot_drop_id
                );

                $lockedClaim
                    ->forceFill([
                        'reversed_at' =>
                            now(),

                        'reversed_by_user_id' =>
                            $reversedByUserId,
                    ]);

                $lockedClaim
                    ->save();

                return [
                    'claim' =>
                        $lockedClaim->fresh([
                            'character:id,name,user_id',
                            'user:id,name,email',
                            'characterItem:id,character_id,name,quantity,image_path',
                            'characterItem.character:id,name,user_id',
                            'reversedBy:id,name',
                        ]),

                    'holder_name' =>
                        $holderName,

                    'quantity_restored' =>
                        $quantity,

                    'image_path_to_delete' =>
                        $imagePathToDelete,
                ];
            }
        );

        $imagePath =
            $result[
                'image_path_to_delete'
            ]
            ?? null;

        if (
            is_string($imagePath)
            &&
            trim($imagePath) !== ''
            &&
            Storage::disk('public')
                ->exists($imagePath)
        ) {
            Storage::disk('public')
                ->delete($imagePath);
        }

        unset(
            $result[
                'image_path_to_delete'
            ]
        );

        return $result;
    }


    public function shopsForCharacter(Character $character)
    {
        $campaignIds = DB::table('campaign_characters')
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->pluck('campaign_id');

        if ($campaignIds->isEmpty()) {
            return collect();
        }

        return LootDrop::query()
            ->whereIn('campaign_id', $campaignIds)
            ->where('is_shop', true)
            ->where('status', LootDrop::STATUS_RELEASED)
            ->with([
                'campaign:id,name',
                'shopkeeperNpc:id,name,image_path',
                'items' => function ($query) {
                    $query
                        ->where('release_status', LootDropItem::RELEASED)
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->latest('released_at')
            ->get()
            ->values();
    }

    public function purchaseShopItem(
        Character $character,
        LootDropItem $lootItem,
        int $quantity = 1
    ): array {
        $quantity = max(1, min(999999, $quantity));

        return DB::transaction(function () use (
            $character,
            $lootItem,
            $quantity
        ): array {
            $locked = LootDropItem::query()
                ->with('lootDrop')
                ->whereKey($lootItem->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertCharacterCanBuyShopItem(
                $character,
                $locked
            );

            if ((int) $locked->quantity_remaining < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'A loja não possui esta quantidade em estoque.',
                ]);
            }

            $unitPrice = $this->shopItemPriceInCopper($locked);

            if ($unitPrice <= 0) {
                throw ValidationException::withMessages([
                    'price' =>
                        'Este item está sem preço e não pode ser comprado.',
                ]);
            }

            if ($unitPrice > intdiv(PHP_INT_MAX, $quantity)) {
                throw ValidationException::withMessages([
                    'price' =>
                        'O valor total desta compra é muito alto.',
                ]);
            }

            $totalPrice = $unitPrice * $quantity;

            $wallet = CharacterWallet::query()->firstOrCreate(
                ['character_id' => $character->id],
                [
                    'copper' => 0,
                    'silver' => 0,
                    'electrum' => 0,
                    'gold' => 0,
                    'platinum' => 0,
                ]
            );

            $wallet = CharacterWallet::query()
                ->whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            $walletValue = $this->walletValueInCopper($wallet);

            if ($walletValue < $totalPrice) {
                throw ValidationException::withMessages([
                    'wallet' =>
                        'Você não possui moedas suficientes para esta compra.',
                ]);
            }

            $remainingValue = $walletValue - $totalPrice;
            $normalizedWallet = $this->copperValueToWallet($remainingValue);

            foreach (self::COINS as $coin) {
                $wallet->{$coin} = $normalizedWallet[$coin];
            }

            $wallet->save();

            $characterItem = CharacterItem::query()->create([
                'character_id' => $character->id,
                'name' => $locked->name,
                'type' => $locked->type,
                'description' => $locked->description,
                'image_path' => $this->copyLootImageToCharacter(
                    $locked->image_path,
                    $character
                ),
                'quantity' => $quantity,
                'weight' => $locked->weight,
                'equipped' => false,
                'is_magical' => (bool) $locked->is_magical,
                'rarity' => $locked->rarity,
                'requires_attunement' => (bool) $locked->requires_attunement,
                'attuned' => false,
                'is_cursed' => (bool) $locked->is_cursed,
                'curse_name' => $locked->curse_name,
                'curse_description' => $locked->curse_description,
                'curse_revealed' => false,
                'armor_class' => $locked->armor_class,
                'damage' => $locked->damage,
                'attack_bonus' => $locked->attack_bonus,
                'damage_bonus' => $locked->damage_bonus,
                'ability_bonuses' => $locked->ability_bonuses ?? [],
                'properties' => $locked->properties ?? [],
                'modifiers' => $locked->modifiers ?? [],
                'notes' => $locked->notes,
            ]);

            $locked->quantity_remaining = max(
                0,
                (int) $locked->quantity_remaining - $quantity
            );

            $locked->depleted_at =
                $locked->quantity_remaining <= 0
                    ? now()
                    : null;

            $locked->save();

            $claim = LootClaim::query()->create([
                'loot_drop_item_id' => $locked->id,
                'character_id' => $character->id,
                'user_id' => $character->user_id,
                'character_item_id' => $characterItem->id,
                'quantity' => $quantity,
                'claim_type' => 'shop',
                'price_paid_copper_value' => $totalPrice,
                'claimed_at' => now(),
            ]);

            return [
                'item' => $characterItem->fresh(),
                'shop_item' => $locked->fresh(),
                'wallet' => $wallet->fresh(),
                'claim' => $claim->fresh(),
                'paid_copper_value' => $totalPrice,
            ];
        });
    }

    public function assertCharacterCanSeeShopItem(
        Character $character,
        LootDropItem $item
    ): void {
        $item->loadMissing('lootDrop');

        abort_unless(
            $item->lootDrop
            && (bool) $item->lootDrop->is_shop
            && $item->lootDrop->status === LootDrop::STATUS_RELEASED
            && $item->release_status === LootDropItem::RELEASED,
            404
        );

        $this->assertCharacterBelongsToLootCampaign(
            $character,
            (int) $item->lootDrop->campaign_id
        );
    }

    public function assertCharacterCanBuyShopItem(
        Character $character,
        LootDropItem $item
    ): void {
        $this->assertCharacterCanSeeShopItem(
            $character,
            $item
        );

        abort_unless(
            (int) $item->quantity_remaining > 0,
            404
        );
    }

    public function syncDropCompletion(int $lootDropId): void
    {
        DB::transaction(
            fn () => $this->syncDropCompletionLocked($lootDropId)
        );
    }

    public function assertCharacterCanSeeItem(
        Character $character,
        LootDropItem $item
    ): void {
        abort_unless(
            $item->release_status === LootDropItem::RELEASED
            && (int) $item->quantity_remaining > 0
            && !(bool) $item->lootDrop?->is_shop,
            404
        );

        $this->assertCharacterBelongsToLootCampaign(
            $character,
            (int) $item->lootDrop->campaign_id
        );

        if ($item->visibility_mode === LootDropItem::VISIBILITY_RESTRICTED) {
            $allowed = DB::table('loot_item_recipients')
                ->where('loot_drop_item_id', $item->id)
                ->where('character_id', $character->id)
                ->exists();

            abort_unless($allowed, 404);
        }
    }

    public function assertCharacterCanSeeCurrency(
        Character $character,
        LootCurrencyEntry $entry
    ): void {
        abort_unless(
            $entry->release_status === LootCurrencyEntry::RELEASED,
            404
        );

        $this->assertCharacterBelongsToLootCampaign(
            $character,
            (int) $entry->lootDrop->campaign_id
        );

        if ($entry->visibility_mode === LootCurrencyEntry::VISIBILITY_RESTRICTED) {
            $allowed = DB::table('loot_currency_recipients')
                ->where('loot_currency_entry_id', $entry->id)
                ->where('character_id', $character->id)
                ->exists();

            abort_unless($allowed, 404);
        }
    }

    private function assertCharacterBelongsToLootCampaign(
        Character $character,
        int $campaignId
    ): void {
        $active = DB::table('campaign_characters')
            ->where('campaign_id', $campaignId)
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->exists();

        abort_unless($active, 404);
    }

    private function syncDropCompletionLocked(int $lootDropId): void
    {
        $drop = LootDrop::query()
            ->whereKey($lootDropId)
            ->lockForUpdate()
            ->first();

        if (
            !$drop
            || (bool) $drop->is_quick_access
            || (bool) $drop->is_shop
            || (
                $drop->status === LootDrop::STATUS_CLOSED
                && $drop->closed_reason !== 'depleted'
            )
        ) {
            return;
        }

        $hasItems = LootDropItem::query()
            ->where('loot_drop_id', $drop->id)
            ->exists();

        $hasCurrencies = LootCurrencyEntry::query()
            ->where('loot_drop_id', $drop->id)
            ->exists();

        if (!$hasItems && !$hasCurrencies) {
            return;
        }

        $hasRemainingItems = LootDropItem::query()
            ->where('loot_drop_id', $drop->id)
            ->where('quantity_remaining', '>', 0)
            ->exists();

        $hasRemainingCurrency = LootCurrencyEntry::query()
            ->where('loot_drop_id', $drop->id)
            ->where(function ($query) {
                foreach (self::COINS as $coin) {
                    $query->orWhere($coin . '_remaining', '>', 0);
                }
            })
            ->exists();

        if (!$hasRemainingItems && !$hasRemainingCurrency) {
            $drop->forceFill([
                'status' => LootDrop::STATUS_CLOSED,
                'closed_reason' => 'depleted',
                'closed_at' => now(),
            ])->save();
        }
    }

    private function reopenAutoClosedDropLocked(int $lootDropId): void
    {
        $drop = LootDrop::query()
            ->whereKey($lootDropId)
            ->lockForUpdate()
            ->first();

        if (
            !$drop
            || $drop->status !== LootDrop::STATUS_CLOSED
            || $drop->closed_reason !== 'depleted'
        ) {
            return;
        }

        $drop->forceFill([
            'status' => LootDrop::STATUS_RELEASED,
            'closed_reason' => null,
            'closed_at' => null,
            'released_at' => $drop->released_at ?? now(),
        ])->save();
    }

    private function shopItemPriceInCopper(LootDropItem $item): int
    {
        $total = 0;

        foreach (self::COIN_VALUES as $coin => $value) {
            $amount = max(0, (int) ($item->{'price_' . $coin} ?? 0));

            if ($amount > 0 && $value > intdiv(PHP_INT_MAX - $total, $amount)) {
                throw ValidationException::withMessages([
                    'price' => 'O preço configurado para este item é muito alto.',
                ]);
            }

            $total += $amount * $value;
        }

        return $total;
    }

    private function walletValueInCopper(CharacterWallet $wallet): int
    {
        $total = 0;

        foreach (self::COIN_VALUES as $coin => $value) {
            $amount = max(0, (int) $wallet->{$coin});

            if ($amount > 0 && $value > intdiv(PHP_INT_MAX - $total, $amount)) {
                throw ValidationException::withMessages([
                    'wallet' => 'O valor total da bolsa excede o limite suportado.',
                ]);
            }

            $total += $amount * $value;
        }

        return $total;
    }

    private function copperValueToWallet(int $value): array
    {
        $remaining = max(0, $value);
        $result = [
            'copper' => 0,
            'silver' => 0,
            'electrum' => 0,
            'gold' => 0,
            'platinum' => 0,
        ];

        foreach (
            [
                'platinum',
                'gold',
                'electrum',
                'silver',
                'copper',
            ] as $coin
        ) {
            $coinValue = self::COIN_VALUES[$coin];
            $amount = min(
                self::WALLET_MAX_PER_COIN,
                intdiv($remaining, $coinValue)
            );

            $result[$coin] = $amount;
            $remaining -= $amount * $coinValue;
        }

        if ($remaining > 0) {
            throw ValidationException::withMessages([
                'wallet' => 'Não foi possível representar o troco dentro dos limites da bolsa.',
            ]);
        }

        return $result;
    }

    private function requestedCurrency(
        LootCurrencyEntry $entry,
        array $requestedAmounts
    ): array {
        $amounts = [];

        foreach (self::COINS as $coin) {
            $requested = max(
                0,
                (int) ($requestedAmounts[$coin] ?? 0)
            );

            $remaining = max(
                0,
                (int) $entry->{$coin . '_remaining'}
            );

            if ($requested > $remaining) {
                throw ValidationException::withMessages([
                    $coin =>
                        'A quantidade escolhida é maior que o valor ainda disponível.',
                ]);
            }

            $amounts[$coin] = $requested;
        }

        return $amounts;
    }


    private function remainingCurrency(LootCurrencyEntry $entry): array
    {
        return collect(self::COINS)
            ->mapWithKeys(fn ($coin) => [
                $coin => (int) $entry->{$coin . '_remaining'},
            ])
            ->all();
    }

    private function splitAllocationForCharacter(
        Character $character,
        LootCurrencyEntry $entry
    ): array {
        $recipient = LootCurrencyRecipient::query()
            ->where('loot_currency_entry_id', $entry->id)
            ->where('character_id', $character->id)
            ->lockForUpdate()
            ->first();

        abort_unless($recipient, 404);

        if ($recipient->claimed_at !== null) {
            throw ValidationException::withMessages([
                'currency' => 'Este personagem já recolheu sua parte.',
            ]);
        }

        return collect(self::COINS)
            ->mapWithKeys(fn ($coin) => [
                $coin => (int) $recipient->{$coin . '_allocated'},
            ])
            ->all();
    }

    private function copyLootImageToCharacter(
        ?string $sourcePath,
        Character $character
    ): ?string {
        if (!is_string($sourcePath) || trim($sourcePath) === '') {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($sourcePath)) {
            return null;
        }

        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $filename = uniqid('loot_', true)
            . ($extension ? '.' . $extension : '');

        $destination = 'character-items/'
            . $character->id
            . '/'
            . $filename;

        $disk->copy($sourcePath, $destination);

        return $destination;
    }
}
