{{-- V66.2.43: contrato para a futura Regra Opcional do Mestre.
     A camada de regras deve fornecer a permissão resolvida por campanha e papel.
     A restrição de criação não afeta edição, espólios ou sintonização.
     IMPORTANTE: a rota POST também deverá validar a regra no backend. --}}
@props(['character', 'inventoryCanCreateItems' => true])

@php
    $rarityLabels = [
        'common' => 'Comum',
        'uncommon' => 'Incomum',
        'rare' => 'Raro',
        'very_rare' => 'Muito Raro',
        'legendary' => 'Lendário',
        'artifact' => 'Artefato',
    ];

    $natureLabels = [
        'wonderful' => 'Item Maravilhoso',
        'mundane' => 'Item Mundano',
        'technological' => 'Item Tecnológico',
    ];

    $abilityLabels = [
        'strength' => 'Força',
        'dexterity' => 'Destreza',
        'constitution' => 'Constituição',
        'intelligence' => 'Inteligência',
        'wisdom' => 'Sabedoria',
        'charisma' => 'Carisma',
        'none' => 'Sem atributo',
        'custom' => 'Custom',
    ];

    $recoveryLabels = [
        'day' => 'Dia',
        'short_rest' => 'Descanso Curto',
        'long_rest' => 'Descanso Longo',
        'dawn' => 'Amanhecer',
        'single_use' => 'Uso Único',
        'custom' => 'Custom',
    ];

    $weaponTraitDefinitions = [
        [
            'key' => 'ammunition',
            'name' => 'Munição',
            'description' => 'Você só pode usar uma arma com a propriedade Munição para realizar um ataque à distância se tiver munição disponível. O tipo de munição necessário é especificado junto com o alcance da arma. Cada ataque consome uma peça de munição. Sacar a munição faz parte do ataque, e você precisa de uma mão livre para carregar uma arma de uma mão. Após um combate, você pode gastar 1 minuto para recuperar metade da munição usada, arredondando para baixo; o restante é perdido.',
        ],
        [
            'key' => 'finesse',
            'name' => 'Acuidade',
            'description' => 'Ao realizar um ataque com uma arma que possui Acuidade, você pode usar o modificador de Força ou de Destreza, à sua escolha, para as jogadas de ataque e dano. Você deve usar o mesmo modificador para ambas as jogadas.',
        ],
        [
            'key' => 'heavy',
            'name' => 'Pesada',
            'description' => 'Você tem Desvantagem nas jogadas de ataque com uma arma Pesada se ela for uma arma corpo a corpo e sua Força for menor que 13, ou se ela for uma arma à distância e sua Destreza for menor que 13.',
        ],
        [
            'key' => 'light',
            'name' => 'Leve',
            'description' => 'Quando você usa a ação Atacar no seu turno e ataca com uma arma Leve, pode fazer um ataque extra como uma Ação Bônus mais tarde no mesmo turno. Esse ataque extra deve ser feito com uma arma Leve diferente, e você não adiciona seu modificador de habilidade ao dano do ataque extra, a menos que esse modificador seja negativo.',
        ],
        [
            'key' => 'loading',
            'name' => 'Carregamento',
            'description' => 'Você só pode disparar uma munição de uma arma com Carregamento quando usa uma ação, uma Ação Bônus ou uma Reação para dispará-la, independentemente do número de ataques que normalmente poderia realizar.',
        ],
        [
            'key' => 'range',
            'name' => 'Distância',
            'description' => 'Uma arma de longo alcance apresenta dois valores de alcance. O primeiro é o alcance normal da arma em pés e o segundo é o alcance máximo. Ao atacar um alvo além do alcance normal, você tem Desvantagem na jogada de ataque. Você não pode atacar um alvo além do alcance máximo.',
        ],
        [
            'key' => 'reach',
            'name' => 'Alcance',
            'description' => 'Uma arma com Alcance adiciona 5 pés ao seu alcance quando você ataca com ela e também ao determinar seu alcance para Ataques de Oportunidade realizados com essa arma.',
        ],
        [
            'key' => 'thrown',
            'name' => 'Arremesso',
            'description' => 'Se uma arma possuir a propriedade Arremesso, você pode arremessá-la para realizar um ataque à distância e pode sacá-la como parte desse ataque. Se for uma arma corpo a corpo, use o mesmo modificador de habilidade para as jogadas de ataque e dano que usaria em um ataque corpo a corpo com essa arma.',
        ],
        [
            'key' => 'two_handed',
            'name' => 'Duas Mãos',
            'description' => 'Uma arma de Duas Mãos exige o uso das duas mãos para atacar com ela.',
        ],
        [
            'key' => 'versatile',
            'name' => 'Versátil',
            'description' => 'Uma arma Versátil pode ser usada com uma ou duas mãos. Um valor de dano alternativo aparece associado à propriedade. A arma causa esse dano quando usada com as duas mãos para realizar um ataque corpo a corpo.',
        ],
    ];

    $masteryDefinitions = [
        [
            'key' => 'cleave',
            'name' => 'Cleave',
            'description' => 'Se você acertar uma criatura com uma jogada de ataque corpo a corpo usando esta arma, pode realizar uma jogada de ataque corpo a corpo com a arma contra uma segunda criatura que esteja a até 5 pés da primeira e também dentro do seu alcance. Se acertar, a segunda criatura sofre o dano da arma, mas você não adiciona seu modificador de habilidade a esse dano, a menos que o modificador seja negativo. Você só pode realizar esse ataque extra uma vez por turno.',
        ],
        [
            'key' => 'graze',
            'name' => 'Graze',
            'description' => 'Se sua jogada de ataque com esta arma errar uma criatura, você pode causar a ela dano igual ao modificador de habilidade usado para realizar a jogada de ataque. Esse dano é do mesmo tipo causado pela arma e só pode ser aumentado aumentando esse modificador de habilidade.',
        ],
        [
            'key' => 'nick',
            'name' => 'Nick',
            'description' => 'Quando você realiza o ataque extra concedido pela propriedade Leve, pode fazê-lo como parte da ação Atacar em vez de usar uma Ação Bônus. Você só pode realizar esse ataque extra uma vez por turno.',
        ],
        [
            'key' => 'push',
            'name' => 'Push',
            'description' => 'Se você acertar uma criatura com esta arma, pode empurrá-la até 10 pés em linha reta para longe de você, desde que ela seja de tamanho Grande ou menor.',
        ],
        [
            'key' => 'sap',
            'name' => 'Sap',
            'description' => 'Se você acertar uma criatura com esta arma, ela terá Desvantagem na próxima jogada de ataque que realizar antes do início do seu próximo turno.',
        ],
        [
            'key' => 'slow',
            'name' => 'Slow',
            'description' => 'Se você acertar uma criatura com esta arma e causar dano a ela, pode reduzir o Deslocamento dela em 10 pés até o início do seu próximo turno. Se a criatura for atingida mais de uma vez por armas com esta maestria, a redução de Deslocamento não ultrapassa 10 pés.',
        ],
        [
            'key' => 'topple',
            'name' => 'Topple',
            'description' => 'Se você acertar uma criatura com esta arma, pode forçá-la a realizar um teste de resistência de Constituição. A CD é igual a 8 + o modificador de habilidade usado na jogada de ataque + seu Bônus de Proficiência. Em caso de falha, a criatura fica Caída.',
        ],
        [
            'key' => 'vex',
            'name' => 'Vex',
            'description' => 'Se você acertar uma criatura com esta arma e causar dano a ela, terá Vantagem na sua próxima jogada de ataque contra essa criatura antes do fim do seu próximo turno.',
        ],
    ];

    $wallet = $character->wallet;

    $walletPayload = [
        'copper' => (int) ($wallet?->copper ?? 0),
        'silver' => (int) ($wallet?->silver ?? 0),
        'electrum' => (int) ($wallet?->electrum ?? 0),
        'gold' => (int) ($wallet?->gold ?? 0),
        'platinum' => (int) ($wallet?->platinum ?? 0),
    ];

    /*
    |--------------------------------------------------------------------------
    | Imagens dos itens
    |--------------------------------------------------------------------------
    |
    | A imagem é servida pela rota autenticada characters.items.image.
    | Assim a Blade não depende de /storage, APP_URL ou symlink/junction.
    |
    */

    $itemsPayload = $character->items->map(function ($item) use ($rarityLabels, $character) {
        $curseVisible = (bool) $item->is_cursed && (bool) $item->curse_revealed;

        return [
            'id' => $item->id,
            'name' => $item->name,
            'type' => $item->type,
            'description' => $item->description,
            'image_path' => $item->image_path,
            'image_url' => $item->image_path
                ? route(
                    'characters.items.image',
                    [
                        'character' => $character,
                        'item' => $item,
                        'v' => substr(
                            sha1($item->image_path),
                            0,
                            12
                        ),
                    ],
                    false
                )
                : null,
            'equipped' => (bool) $item->equipped,
            'is_magical' => (bool) $item->is_magical,
            'rarity' => $item->rarity,
            'rarity_label' => $item->rarity ? ($rarityLabels[$item->rarity] ?? ucfirst(str_replace('_', ' ', $item->rarity))) : null,
            'requires_attunement' => (bool) $item->requires_attunement,
            'attuned' => (bool) $item->attuned,
            'armor_class' => $item->armor_class,
            'damage' => $item->damage,
            'attack_bonus' => $item->attack_bonus,
            'damage_bonus' => $item->damage_bonus,
            'ability_bonuses' => is_array($item->ability_bonuses) ? $item->ability_bonuses : [],
            'properties' => is_array($item->properties) ? $item->properties : [],
            'modifiers' => is_array($item->modifiers) ? $item->modifiers : [],
            'notes' => $item->notes,
            'is_cursed' => $curseVisible,
            'curse_revealed' => $curseVisible,
            'curse_description' => $curseVisible ? $item->curse_description : null,
        ];
    })->values();


    /*
    |--------------------------------------------------------------------------
    | TEMA ATUAL
    |--------------------------------------------------------------------------
    |
    | O Inventário possui vários painéis teleportados para o body. Reaplicamos
    | o tema em cada portal para que todos consumam o mesmo contrato visual da
    | ficha, sem voltar para o bege/vinho fixo.
    |
    */

    $sheetSettings =
        is_array($character->sheet_settings ?? null)
            ? $character->sheet_settings
            : [];

    $allowedThemes = [
        'spellbound',
        'emerald',
        'rose',
        'sapphire',
        'arcane',
        'midnight',
        'obsidian',
        'aurora',
    ];

    $characterTheme =
        strtolower(
            trim(
                (string) data_get(
                    $sheetSettings,
                    'appearance.theme',
                    'spellbound'
                )
            )
        );

    if (!in_array($characterTheme, $allowedThemes, true)) {
        $characterTheme = 'spellbound';
    }

    $inventoryGroupStorageKey = 'character-inventory-group-drawers-' . $character->id;
    $inventoryCanCreateItems = (bool) $inventoryCanCreateItems;
@endphp

@include('characters.components.inventario.aparencia')

@include('characters.components.inventario.comportamento')

<section
    x-data="characterInventoryV10(
        @js($itemsPayload),
        @js($walletPayload),
        @js($rarityLabels),
        @js($natureLabels),
        @js($abilityLabels),
        @js($recoveryLabels),
        @js($weaponTraitDefinitions),
        @js($masteryDefinitions),
        {
            characterProficiency: @js((int) ($character->proficiency_bonus ?? 2)),
            sync: @js(route('characters.items.sync', ['character' => $character])),
            store: @js(route('characters.items.store', ['character' => $character])),
            update: @js(route('characters.items.update', ['character' => $character, 'item' => '__ITEM__'])),
            destroy: @js(route('characters.items.destroy', ['character' => $character, 'item' => '__ITEM__'])),
            equipped: @js(route('characters.items.equipped.update', ['character' => $character, 'item' => '__ITEM__'])),
            attunement: @js(route('characters.items.attunement.update', ['character' => $character, 'item' => '__ITEM__'])),
            featureUses: @js(route('characters.items.features.uses.update', ['character' => $character, 'item' => '__ITEM__', 'feature' => '__FEATURE__'])),
            wallet: @js(route('characters.wallet.update', ['character' => $character])),
            foundLootIndex: @js(route('characters.found-loot.index', ['character' => $character])),
            foundLootClaimItem: @js(route('characters.found-loot.items.claim', ['character' => $character, 'lootItem' => '__LOOT_ITEM__'])),
            foundLootClaimCurrency: @js(route('characters.found-loot.currency.claim', ['character' => $character, 'currency' => '__CURRENCY__'])),
        },
        @js($inventoryCanCreateItems)
    )"
    @party-item-transfer-completed.window="refreshInventory(true)"
    x-on:character-proficiency-updated.window="applyVestigeProficiency($event.detail)"
    x-on:character-progression-updated.window="applyVestigeProficiency($event.detail)"
    x-on:character-level-up-completed.window="applyVestigeProficiency($event.detail)"
    @shop-purchase-completed.window="
        if ($event.detail?.item) {
            upsertItem($event.detail.item);
        }

        if ($event.detail?.wallet) {
            wallet = {
                copper: normalizeMoney($event.detail.wallet.copper),
                silver: normalizeMoney($event.detail.wallet.silver),
                electrum: normalizeMoney($event.detail.wallet.electrum),
                gold: normalizeMoney($event.detail.wallet.gold),
                platinum: normalizeMoney($event.detail.wallet.platinum),
            };
        }
    "
    @character-equipment-updated.window="
        if ($event.detail?.item?.id) {
            const syncedItem =
                items.find(
                    current =>
                        parseInt(current.id) ===
                        parseInt($event.detail.item.id)
                );

            if (syncedItem) {
                syncedItem.equipped =
                    !!$event.detail.item.equipped;
            }
        }
    "
    @keydown.escape.window="
        if (walletOpen) {
            walletOpen = false;
        } else if (attunementWarningOpen) {
            attunementWarningOpen = false;
            pendingAttunementItem = null;
        } else if (attunementPickerOpen) {
            attunementPickerOpen = false;
        } else if (singleUseConfirmOpen) {
            cancelSingleUse();
        } else if (discardUsedConfirmOpen) {
            cancelDiscardUsedItem();
        } else if (deleteConfirmOpen) {
            deleteConfirmOpen = false;
        } else if (editorOpen) {
            closeEditor();
        } else if (detailOpen) {
            closeDetail();
        } else if (equippedDrawerOpen) {
            equippedDrawerOpen = false;
        } else if (foundItemsOpen) {
            foundItemsOpen = false;
        }
    "
    class="inventory-v10 inventory-v14 inventory-v15 inventory-v16 inventory-v17 inventory-v18 inventory-v19 inventory-v20 inventory-v21 inventory-v22 inventory-v23 inventory-v24 inventory-v25 inventory-v26 inventory-v27 inventory-v28 inventory-v29 inventory-v30 inventory-v31 inventory-v32 inventory-v33 inventory-v34 inventory-v35 inventory-v39 inventory-v40 inventory-v41 inventory-v42"
>
@include('characters.components.inventario.painel')
@include('characters.components.inventario.detalhes')
@include('characters.components.inventario.criacao_edicao')
@include('characters.components.inventario.auxiliares')
</section>

{{-- Centro de notificações reutilizável da ficha --}}
@include('characters.components.ui.notification-center')