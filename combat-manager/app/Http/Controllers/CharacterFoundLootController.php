<?php



namespace App\Http\Controllers;



use App\Models\Character;

use App\Models\CharacterItem;

use App\Models\LootCurrencyEntry;

use App\Models\LootDrop;

use App\Models\LootDropItem;

use App\Services\Loot\LootClaimService;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Gate;

use Symfony\Component\HttpFoundation\BinaryFileResponse;



class CharacterFoundLootController extends Controller

{

    public function __construct(

        private LootClaimService $lootClaimService

    ) {

    }



    public function index(Character $character): JsonResponse

    {

        Gate::authorize('update', $character);



        $available = $this->lootClaimService

            ->availableForCharacter($character);



        return response()->json([

            'success' => true,

            'count' =>

                $available['items']->count()

                + $available['currencies']->count(),

            'items' => $available['items']

                ->map(

                    fn (LootDropItem $item) =>

                        $this->itemPayload(

                            $character,

                            $item

                        )

                )

                ->values(),

            'currencies' => $available['currencies']

                ->map(

                    fn (LootCurrencyEntry $entry) =>

                        $this->currencyPayload(

                            $character,

                            $entry

                        )

                )

                ->values(),

        ]);

    }



    public function shops(Character $character): JsonResponse

    {

        Gate::authorize('update', $character);



        $shops = $this->lootClaimService

            ->shopsForCharacter($character);



        $wallet = $character->wallet()->first();



        return response()->json([

            'success' => true,

            'shop_count' => $shops->count(),

            'wallet' => [

                'copper' => (int) ($wallet?->copper ?? 0),

                'silver' => (int) ($wallet?->silver ?? 0),

                'electrum' => (int) ($wallet?->electrum ?? 0),

                'gold' => (int) ($wallet?->gold ?? 0),

                'platinum' => (int) ($wallet?->platinum ?? 0),

            ],

            'shops' => $shops

                ->map(

                    fn (LootDrop $shop) =>

                        $this->shopPayload(

                            $character,

                            $shop

                        )

                )

                ->values(),

        ]);

    }



    public function claimItem(

        Request $request,

        Character $character,

        LootDropItem $lootItem

    ): JsonResponse {

        Gate::authorize('update', $character);



        $data = $request->validate([

            'quantity' => [

                'nullable',

                'integer',

                'min:1',

                'max:999999',

            ],

        ]);



        $item = $this->lootClaimService->claimItem(

            $character,

            $lootItem,

            (int) ($data['quantity'] ?? 1)

        );



        $quantityRemaining = (int) (

            LootDropItem::query()

                ->whereKey($lootItem->id)

                ->value('quantity_remaining')

            ?? 0

        );



        return response()->json([

            'success' => true,

            'message' => 'Item adicionado ao inventário.',

            'character_item_id' => (int) $item->id,

            'quantity_remaining' => $quantityRemaining,

            'item' => $this->inventoryItemPayload($item),

        ]);

    }



    public function claimCurrency(

        Request $request,

        Character $character,

        LootCurrencyEntry $currency

    ): JsonResponse {

        Gate::authorize('update', $character);



        $data = $request->validate([

            'copper' => ['nullable', 'integer', 'min:0', 'max:999999999'],

            'silver' => ['nullable', 'integer', 'min:0', 'max:999999999'],

            'electrum' => ['nullable', 'integer', 'min:0', 'max:999999999'],

            'gold' => ['nullable', 'integer', 'min:0', 'max:999999999'],

            'platinum' => ['nullable', 'integer', 'min:0', 'max:999999999'],

        ]);



        $result = $this->lootClaimService->claimCurrency(

            $character,

            $currency,

            $data

        );



        return response()->json([

            'success' => true,

            'message' => 'Moedas adicionadas à bolsa.',

            'claimed' => $result['claimed'],

            'remaining' => [

                'copper' => (int) $result['entry']->copper_remaining,

                'silver' => (int) $result['entry']->silver_remaining,

                'electrum' => (int) $result['entry']->electrum_remaining,

                'gold' => (int) $result['entry']->gold_remaining,

                'platinum' => (int) $result['entry']->platinum_remaining,

            ],

            'wallet' => [

                'copper' => (int) $result['wallet']->copper,

                'silver' => (int) $result['wallet']->silver,

                'electrum' => (int) $result['wallet']->electrum,

                'gold' => (int) $result['wallet']->gold,

                'platinum' => (int) $result['wallet']->platinum,

            ],

        ]);

    }



    public function purchaseShopItem(

        Request $request,

        Character $character,

        LootDropItem $lootItem

    ): JsonResponse {

        Gate::authorize('update', $character);



        $data = $request->validate([

            'quantity' => [

                'nullable',

                'integer',

                'min:1',

                'max:999999',

            ],



            'payment' => [

                'nullable',

                'array',

            ],



            'payment.copper' => [

                'nullable',

                'integer',

                'min:0',

                'max:999999999',

            ],



            'payment.silver' => [

                'nullable',

                'integer',

                'min:0',

                'max:999999999',

            ],



            'payment.electrum' => [

                'nullable',

                'integer',

                'min:0',

                'max:999999999',

            ],



            'payment.gold' => [

                'nullable',

                'integer',

                'min:0',

                'max:999999999',

            ],



            'payment.platinum' => [

                'nullable',

                'integer',

                'min:0',

                'max:999999999',

            ],

        ]);



        $result = $this->lootClaimService

            ->purchaseShopItem(

                $character,

                $lootItem,

                (int) ($data['quantity'] ?? 1),

                is_array($data['payment'] ?? null)

                    ? $data['payment']

                    : []

            );



        return response()->json([

            'success' => true,

            'message' => 'Compra concluída.',

            'quantity_remaining' =>

                (int) $result['shop_item']->quantity_remaining,

            'paid_copper_value' =>

                (int) $result['paid_copper_value'],



            'payment' => [

                'copper' => (int) ($result['payment']['copper'] ?? 0),

                'silver' => (int) ($result['payment']['silver'] ?? 0),

                'electrum' => (int) ($result['payment']['electrum'] ?? 0),

                'gold' => (int) ($result['payment']['gold'] ?? 0),

                'platinum' => (int) ($result['payment']['platinum'] ?? 0),

            ],



            'change' => [

                'copper' => (int) ($result['change']['copper'] ?? 0),

                'silver' => (int) ($result['change']['silver'] ?? 0),

                'electrum' => (int) ($result['change']['electrum'] ?? 0),

                'gold' => (int) ($result['change']['gold'] ?? 0),

                'platinum' => (int) ($result['change']['platinum'] ?? 0),

            ],



            'wallet' => [

                'copper' => (int) $result['wallet']->copper,

                'silver' => (int) $result['wallet']->silver,

                'electrum' => (int) $result['wallet']->electrum,

                'gold' => (int) $result['wallet']->gold,

                'platinum' => (int) $result['wallet']->platinum,

            ],



            'item' =>

                $this->inventoryItemPayload(

                    $result['item']

                ),

        ]);

    }


    public function shopImage(

        Character $character,

        LootDrop $lootDrop

    ): BinaryFileResponse {

        Gate::authorize('update', $character);



        $available = $this->lootClaimService

            ->shopsForCharacter($character)

            ->firstWhere('id', $lootDrop->id);



        abort_unless($available, 404);



        $lootDrop->loadMissing(

            'shopkeeperNpc:id,name,image_path'

        );



        $path =

            is_string($lootDrop->shop_image_path)

            && trim($lootDrop->shop_image_path) !== ''

                ? $lootDrop->shop_image_path

                : $lootDrop->shopkeeperNpc?->image_path;



        return $this->serveLootImage($path);

    }



    public function shopBackgroundImage(

        Character $character,

        LootDrop $lootDrop

    ): BinaryFileResponse {

        Gate::authorize('update', $character);



        $available = $this->lootClaimService

            ->shopsForCharacter($character)

            ->firstWhere('id', $lootDrop->id);



        abort_unless($available, 404);



        return $this->serveLootImage(

            $lootDrop->shop_background_path

        );

    }



    public function shopItemImage(

        Character $character,

        LootDropItem $lootItem

    ): BinaryFileResponse {

        Gate::authorize('update', $character);



        $lootItem->loadMissing('lootDrop');



        $this->lootClaimService

            ->assertCharacterCanSeeShopItem(

                $character,

                $lootItem

            );



        return $this->serveLootImage(

            $lootItem->image_path

        );

    }



    public function itemImage(

        Character $character,

        LootDropItem $lootItem

    ): BinaryFileResponse {

        Gate::authorize('update', $character);



        $lootItem->loadMissing('lootDrop');



        $this->lootClaimService->assertCharacterCanSeeItem(

            $character,

            $lootItem

        );



        abort_unless(

            is_string($lootItem->image_path)

            && trim($lootItem->image_path) !== '',

            404

        );



        $relative = ltrim(

            str_replace(

                '\\\\',

                '/',

                $lootItem->image_path

            ),

            '/'

        );



        abort_if(

            str_contains($relative, '../'),

            404

        );



        $fullPath = storage_path(

            'app/public/' . $relative

        );



        abort_unless(

            is_file($fullPath),

            404

        );



        return response()->file(

            $fullPath,

            [

                'Cache-Control' =>

                    'private, no-cache, no-store, must-revalidate',

                'Pragma' => 'no-cache',

                'Expires' => '0',

                'X-Content-Type-Options' => 'nosniff',

            ]

        );

    }



    private function itemPayload(

        Character $character,

        LootDropItem $item

    ): array {

        return [

            'id' => (int) $item->id,

            'loot_drop_id' => (int) $item->loot_drop_id,

            'drop_name' => $item->lootDrop?->name,

            'campaign_name' =>

                $item->lootDrop?->campaign?->name,

            'name' => $item->name,

            'type' => $item->type,

            'description' => $item->description,

            'image_url' => $item->image_path

                ? route(

                    'characters.found-loot.items.image',

                    [

                        'character' => $character,

                        'lootItem' => $item,

                    ],

                    false

                )

                : null,

            'quantity_remaining' =>

                (int) $item->quantity_remaining,

            'weight' => $item->weight,

            'is_magical' => (bool) $item->is_magical,

            'rarity' => $item->rarity,

            'rarity_label' =>

                $this->rarityLabel($item->rarity),

            'requires_attunement' =>

                (bool) $item->requires_attunement,



            /*

            | A maldição não é revelada em Encontrados.

            | O snapshot completo continua no backend e será copiado

            | para character_items com curse_revealed = false.

            */

            'armor_class' => $item->armor_class,

            'damage' => $item->damage,

            'attack_bonus' => $item->attack_bonus,

            'damage_bonus' => $item->damage_bonus,

            'ability_bonuses' =>

                $item->ability_bonuses ?? [],

            'properties' =>

                $item->properties ?? [],

            'modifiers' =>

                $item->modifiers ?? [],

            'notes' => $item->notes,

        ];

    }



    private function shopPayload(

        Character $character,

        $shop

    ): array {

        $shop->loadMissing('shopkeeperNpc:id,name,image_path');



        $hasPresentationImage =

            (

                is_string($shop->shop_image_path)

                && trim($shop->shop_image_path) !== ''

            )

            ||

            (

                is_string($shop->shopkeeperNpc?->image_path)

                && trim($shop->shopkeeperNpc?->image_path) !== ''

            );



        return [

            'id' => (int) $shop->id,

            'name' => $shop->name,

            'description' => $shop->description,

            'campaign_name' => $shop->campaign?->name,

            'shop_attendant_name' => $shop->shop_attendant_name,

            'shopkeeper_name' => (function () use ($shop): ?string {

                $name = trim(

                    (string) (

                        $shop->shopkeeperNpc?->name

                        ?? $shop->shop_attendant_name

                        ?? ''

                    )

                );



                return $name !== '' ? $name : null;

            })(),

            'shop_has_counter' =>

                (bool) ($shop->shop_has_counter ?? true),

            'shop_counter_color' =>

                $shop->shop_counter_color ?: '#6B3A24',

            'shop_background_color' =>

                $shop->shop_background_color ?: '#6F4A33',

            'shop_image_zoom' => max(

                70,

                min(180, (int) ($shop->shop_image_zoom ?? 100))

            ),

            'shop_image_position' => in_array(

                $shop->shop_image_position,

                ['top', 'center', 'bottom'],

                true

            )

                ? $shop->shop_image_position

                : 'center',

            'shop_background_zoom' => max(

                100,

                min(180, (int) ($shop->shop_background_zoom ?? 100))

            ),

            'shop_background_position' => in_array(

                $shop->shop_background_position,

                ['top', 'center', 'bottom'],

                true

            )

                ? $shop->shop_background_position

                : 'center',

            'presentation_image_url' => $hasPresentationImage

                ? route(

                    'characters.shops.image',

                    [

                        'character' => $character,

                        'lootDrop' => $shop,

                    ],

                    false

                )

                : null,

            'background_image_url' => (

                is_string($shop->shop_background_path)

                && trim($shop->shop_background_path) !== ''

            )

                ? route(

                    'characters.shops.background',

                    [

                        'character' => $character,

                        'lootDrop' => $shop,

                    ],

                    false

                )

                : null,

            'items' => $shop->items

                ->map(

                    fn (LootDropItem $item) =>

                        $this->shopItemPayload(

                            $character,

                            $item

                        )

                )

                ->values(),

        ];

    }



    private function shopItemPayload(

        Character $character,

        LootDropItem $item

    ): array {

        $prices = [

            'copper' => max(0, (int) ($item->price_copper ?? 0)),

            'silver' => max(0, (int) ($item->price_silver ?? 0)),

            'electrum' => max(0, (int) ($item->price_electrum ?? 0)),

            'gold' => max(0, (int) ($item->price_gold ?? 0)),

            'platinum' => max(0, (int) ($item->price_platinum ?? 0)),

        ];



        return [

            'id' => (int) $item->id,

            'loot_drop_id' => (int) $item->loot_drop_id,

            'name' => $item->name,

            'type' => $item->type,

            'description' => $item->description,

            'image_url' => $item->image_path

                ? route(

                    'characters.shops.items.image',

                    [

                        'character' => $character,

                        'lootItem' => $item,

                    ],

                    false

                )

                : null,

            'quantity_remaining' =>

                (int) $item->quantity_remaining,

            'weight' => $item->weight,

            'is_magical' => (bool) $item->is_magical,

            'rarity' => $item->rarity,

            'rarity_label' =>

                $this->rarityLabel($item->rarity),

            'requires_attunement' =>

                (bool) $item->requires_attunement,



            /*

            | A vitrine precisa carregar o mesmo snapshot mecânico usado

            | pelo Inventário para que o Player possa inspecionar o item

            | antes da compra. Dados secretos de maldição continuam fora

            | deste payload, da mesma forma que em Itens Encontrados.

            */

            'armor_class' => $item->armor_class,

            'damage' => $item->damage,

            'attack_bonus' => $item->attack_bonus,

            'damage_bonus' => $item->damage_bonus,

            'ability_bonuses' =>

                $item->ability_bonuses ?? [],

            'properties' =>

                $item->properties ?? [],

            'modifiers' =>

                $item->modifiers ?? [],

            'notes' => $item->notes,



            'price' => $prices,

            'price_copper_value' =>

                $prices['copper']

                + ($prices['silver'] * 10)

                + ($prices['electrum'] * 50)

                + ($prices['gold'] * 100)

                + ($prices['platinum'] * 1000),

        ];

    }



    private function currencyPayload(

        Character $character,

        LootCurrencyEntry $entry

    ): array {

        $amounts = [];



        if (

            $entry->distribution_mode

            === LootCurrencyEntry::DISTRIBUTION_SPLIT

        ) {

            $recipient = $entry->recipients

                ->firstWhere(

                    'character_id',

                    $character->id

                );



            foreach (

                [

                    'copper',

                    'silver',

                    'electrum',

                    'gold',

                    'platinum',

                ] as $coin

            ) {

                $amounts[$coin] = (int) (

                    $recipient?->{$coin . '_allocated'}

                    ?? 0

                );

            }

        } else {

            foreach (

                [

                    'copper',

                    'silver',

                    'electrum',

                    'gold',

                    'platinum',

                ] as $coin

            ) {

                $amounts[$coin] = (int) (

                    $entry->{$coin . '_remaining'}

                );

            }

        }



        return [

            'id' => (int) $entry->id,

            'loot_drop_id' =>

                (int) $entry->loot_drop_id,

            'drop_name' => $entry->lootDrop?->name,

            'campaign_name' =>

                $entry->lootDrop?->campaign?->name,

            'label' => $entry->label,

            'distribution_mode' =>

                $entry->distribution_mode,

            'coins' => $amounts,

        ];

    }



    private function inventoryItemPayload(

        CharacterItem $item

    ): array {

        $curseVisible =

            (bool) $item->is_cursed

            && (bool) $item->curse_revealed;



        return [

            'id' => (int) $item->id,

            'character_id' =>

                (int) $item->character_id,

            'name' => $item->name,

            'type' => $item->type,

            'description' => $item->description,



            'image_path' => $item->image_path,

            'image_url' => $this->inventoryImageUrl($item),



            'quantity' => (int) $item->quantity,

            'weight' => $item->weight,

            'equipped' => (bool) $item->equipped,

            'is_magical' => (bool) $item->is_magical,

            'rarity' => $item->rarity,

            'rarity_label' =>

                $this->rarityLabel($item->rarity),

            'requires_attunement' =>

                (bool) $item->requires_attunement,

            'attuned' => (bool) $item->attuned,

            'armor_class' => $item->armor_class,

            'damage' => $item->damage,

            'attack_bonus' => $item->attack_bonus,

            'damage_bonus' => $item->damage_bonus,

            'ability_bonuses' =>

                $item->ability_bonuses ?? [],

            'properties' =>

                $item->properties ?? [],

            'modifiers' =>

                $item->modifiers ?? [],

            'notes' => $item->notes,



            'is_cursed' => $curseVisible,

            'curse_revealed' => $curseVisible,

            'curse_name' => $curseVisible
                ? $item->curse_name
                : null,

            'curse_description' => $curseVisible

                ? $item->curse_description

                : null,



            'created_at' =>

                $item->created_at?->toISOString(),

            'updated_at' =>

                $item->updated_at?->toISOString(),

        ];

    }



    private function serveLootImage(?string $path): BinaryFileResponse

    {

        abort_unless(

            is_string($path)

            && trim($path) !== '',

            404

        );



        $relative = ltrim(

            str_replace('\\\\', '/', $path),

            '/'

        );



        abort_if(

            str_contains($relative, '../'),

            404

        );



        $fullPath = storage_path(

            'app/public/' . $relative

        );



        abort_unless(

            is_file($fullPath),

            404

        );



        return response()->file(

            $fullPath,

            [

                'Cache-Control' =>

                    'private, no-cache, no-store, must-revalidate',

                'Pragma' => 'no-cache',

                'Expires' => '0',

                'X-Content-Type-Options' => 'nosniff',

            ]

        );

    }



    private function inventoryImageUrl(

        CharacterItem $item

    ): ?string {

        if (

            !is_string($item->image_path)

            || trim($item->image_path) === ''

        ) {

            return null;

        }



        return route(

            'characters.items.image',

            [

                'character' => $item->character_id,

                'item' => $item->id,

                'v' => substr(

                    sha1($item->image_path),

                    0,

                    12

                ),

            ],

            false

        );

    }



    private function rarityLabel(

        ?string $rarity

    ): ?string {

        if (!$rarity) {

            return null;

        }



        return match ($rarity) {

            'common' => 'Comum',

            'uncommon' => 'Incomum',

            'rare' => 'Raro',

            'very_rare' => 'Muito Raro',

            'legendary' => 'Lendário',

            'artifact' => 'Artefato',

            default => ucfirst(

                str_replace(

                    '_',

                    ' ',

                    $rarity

                )

            ),

        };

    }

}