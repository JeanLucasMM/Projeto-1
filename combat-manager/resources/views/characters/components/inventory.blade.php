@props(['character'])

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
@endphp

@once
    @push('styles')
        <style>
            .inventory-v10 { position: relative; min-height: 100%; color: #53150f; }
            .inventory-v10 [x-cloak] { display: none !important; }

            .inventory-v10-input,
            .inventory-v10-select,
            .inventory-v10-textarea {
                width: 100%;
                border: 1px solid rgba(205,187,159,.86);
                border-radius: 10px;
                background: rgba(255,255,255,.82);
                color: #53150f;
                outline: none;
                transition: border-color .14s ease, box-shadow .14s ease, background .14s ease;
            }

            .inventory-v10-input,
            .inventory-v10-select {
                min-height: 44px;
                padding: 9px 11px;
                font-size: 14px;
                font-weight: 700;
            }

            .inventory-v10-textarea {
                padding: 11px 12px;
                font-size: 14px;
                line-height: 1.65;
                resize: vertical;
            }

            .inventory-v10-input:focus,
            .inventory-v10-select:focus,
            .inventory-v10-textarea:focus {
                border-color: #8c6239;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(140,98,57,.08);
            }

            .inventory-v12-topbar {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid rgba(216,199,171,.55);
                padding-bottom: 14px;
            }

            .inventory-v12-actions {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
            }

            .inventory-v12-money-wrap {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                justify-content: flex-end;
                gap: 10px;
                margin-left: auto;
            }

            .inventory-v12-wallet {
                min-width: min(100%, 430px);
                border: 1px solid rgba(205,187,159,.7);
                border-radius: 16px;
                background: linear-gradient(180deg, rgba(250,248,242,.98), rgba(244,239,230,.9));
                box-shadow: 0 2px 12px rgba(83,21,15,.04);
                padding: 10px 12px;
            }

            .inventory-v12-wallet-head {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px;
            }

            .inventory-v12-wallet-title {
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .14em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v12-wallet-grid {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 8px;
            }

            .inventory-v12-coin {
                display: flex;
                flex-direction: column;
                gap: 3px;
                border: 1px solid rgba(205,187,159,.6);
                border-radius: 12px;
                background: rgba(255,255,255,.52);
                padding: 8px 8px 7px;
            }

            .inventory-v12-coin span {
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v12-coin input {
                width: 100%;
                border: 0;
                background: transparent;
                padding: 0;
                font-family: Georgia, serif;
                font-size: 18px;
                font-weight: 900;
                color: #53150f;
                text-align: right;
                outline: none;
            }

            .inventory-v12-mini-action {
                position: relative;
                display: inline-flex;
                min-height: 40px;
                align-items: center;
                gap: 8px;
                border-radius: 12px;
                border: 1px solid rgba(205,187,159,.7);
                padding: 0 13px;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                transition: background .15s ease, color .15s ease, border-color .15s ease;
            }

            .inventory-v12-mini-action:hover {
                background: rgba(239,233,220,.62);
                color: #53150f;
                border-color: rgba(140,98,57,.45);
            }

            .inventory-v12-counter-badge {
                display: inline-flex;
                min-width: 22px;
                height: 22px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: rgba(107,29,20,.08);
                padding: 0 6px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #6b1d14;
            }

            .inventory-v12-equipped-card {
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 16px;
                background: linear-gradient(180deg, rgba(255,253,249,.98), rgba(248,244,236,.96));
                padding: 12px;
                box-shadow: 0 3px 10px rgba(83,21,15,.04);
            }

            .inventory-v12-equipped-card + .inventory-v12-equipped-card {
                margin-top: 10px;
            }

            @media (max-width: 640px) {
                .inventory-v12-wallet {
                    min-width: 100%;
                }

                .inventory-v12-wallet-grid {
                    grid-template-columns: repeat(5, minmax(52px, 1fr));
                }

                .inventory-v12-coin input {
                    font-size: 16px;
                }
            }

            .inventory-v10-money {
                width: 42px;
                border: 0;
                border-bottom: 1px solid rgba(205,187,159,.82);
                background: transparent;
                padding: 1px 2px 2px;
                text-align: right;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #53150f;
                outline: none;
            }

            .inventory-v10-thumb {
                overflow: hidden;
                background: radial-gradient(circle at 50% 42%, rgba(205,187,159,.30), rgba(239,233,220,.46));
            }
            .inventory-v10-thumb img { width: 100%; height: 100%; object-fit: cover; }
            .inventory-v10-row { transition: background .14s ease; }
            .inventory-v10-row:hover { background: rgba(239,233,220,.40); }

            .inventory-v10-slot {
                min-height: 88px;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 12px;
                background: rgba(250,248,242,.76);
                transition: border-color .14s ease, background .14s ease, transform .14s ease;
            }
            .inventory-v10-slot:hover {
                border-color: rgba(107,29,20,.34);
                background: rgba(239,233,220,.52);
                transform: translateY(-1px);
            }

            .inventory-v10-choice {
                min-height: 42px;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 10px;
                background: rgba(250,248,242,.82);
                color: #8c6239;
                padding: 8px 11px;
                font-size: 11px;
                font-weight: 900;
                transition: background .14s ease, border-color .14s ease, color .14s ease;
            }
            .inventory-v10-choice:hover { background: rgba(239,233,220,.62); color: #53150f; }
            .inventory-v10-choice.active { border-color: #6b1d14; background: #6b1d14; color: #f4f1e8; }

            .inventory-v10-chip {
                position: relative;
                display: inline-flex;
                align-items: center;
                min-height: 30px;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 999px;
                background: rgba(250,248,242,.86);
                padding: 5px 10px;
                color: #8c6239;
                font-size: 10px;
                font-weight: 900;
                transition: background .14s ease, border-color .14s ease, color .14s ease;
            }
            .inventory-v10-chip:hover { background: rgba(239,233,220,.70); color: #53150f; }
            .inventory-v10-chip.active { border-color: rgba(107,29,20,.42); background: rgba(107,29,20,.08); color: #53150f; }

            .inventory-v10-tooltip-panel {
                pointer-events: none;
                position: absolute;
                left: 50%;
                bottom: calc(100% + 8px);
                z-index: 80;
                width: 260px;
                transform: translateX(-50%) translateY(4px);
                border: 1px solid rgba(205,187,159,.92);
                border-radius: 10px;
                background: #faf8f2;
                padding: 9px 10px;
                color: #5f3a27;
                box-shadow: 0 12px 28px rgba(43,29,23,.16);
                font-size: 11px;
                font-weight: 600;
                line-height: 1.45;
                opacity: 0;
                visibility: hidden;
                transition: opacity .12s ease, transform .12s ease, visibility .12s ease;
            }
            .inventory-v10-tooltip:hover .inventory-v10-tooltip-panel {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(0);
            }

            .inventory-v10-module {
                border: 1px solid rgba(216,199,171,.72);
                border-radius: 14px;
                background: linear-gradient(180deg, rgba(255,253,249,.96), rgba(247,243,234,.86));
            }

            .inventory-v10-feature-card {
                border: 1px solid rgba(216,199,171,.68);
                border-radius: 13px;
                background: rgba(255,253,249,.86);
            }

            .inventory-v10-detail-paper {
                background:
                    radial-gradient(circle at 16% 0%, rgba(255,255,255,.92), transparent 34%),
                    linear-gradient(180deg, rgba(250,248,242,.995), rgba(247,243,234,.99));
            }

            .inventory-v11-item-card {
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 16px;
                background:
                    linear-gradient(
                        135deg,
                        rgba(255,253,249,.96),
                        rgba(248,244,235,.90)
                    );
                box-shadow:
                    0 1px 0 rgba(255,255,255,.72) inset,
                    0 4px 14px rgba(83,21,15,.045);
                transition:
                    transform .16s ease,
                    border-color .16s ease,
                    box-shadow .16s ease,
                    background .16s ease;
            }

            .inventory-v11-item-card::before {
                content: '';
                position: absolute;
                left: 0;
                top: 12px;
                bottom: 12px;
                width: 3px;
                border-radius: 0 999px 999px 0;
                background: rgba(140,98,57,.32);
            }

            .inventory-v11-item-card.magical::before {
                background: #6b1d14;
            }

            .inventory-v11-item-card:hover,
            .inventory-v11-item-card:focus-within {
                transform: translateY(-1px);
                border-color: rgba(140,98,57,.52);
                background:
                    linear-gradient(
                        135deg,
                        rgba(255,254,251,.99),
                        rgba(247,241,230,.96)
                    );
                box-shadow:
                    0 1px 0 rgba(255,255,255,.84) inset,
                    0 10px 24px rgba(83,21,15,.075);
            }

            .inventory-v11-item-card:focus {
                outline: none;
                box-shadow:
                    0 0 0 3px rgba(140,98,57,.08),
                    0 10px 24px rgba(83,21,15,.075);
            }

            .inventory-v11-feature-strip {
                border-top: 1px solid rgba(216,199,171,.52);
                background: rgba(239,233,220,.26);
            }

            .inventory-v11-feature-usage {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 8px;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 10px;
                background: rgba(255,253,249,.72);
                padding: 6px 7px 6px 9px;
            }

            .inventory-v11-usage-name {
                max-width: 128px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;
                color: #53150f;
            }

            .inventory-v11-usage-recovery {
                white-space: nowrap;
                font-size: 9px;
                font-weight: 900;
                color: #8c6239;
            }

            .inventory-v11-mini-tracker {
                display: inline-flex;
                flex-shrink: 0;
                align-items: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.76);
                border-radius: 8px;
                background: #faf8f2;
            }

            .inventory-v11-mini-tracker button {
                display: flex;
                width: 27px;
                height: 27px;
                align-items: center;
                justify-content: center;
                color: #8c6239;
                font-size: 14px;
                font-weight: 900;
                transition: background .12s ease, color .12s ease;
            }

            .inventory-v11-mini-tracker button:hover {
                background: rgba(239,233,220,.78);
                color: #53150f;
            }

            .inventory-v11-mini-tracker strong {
                min-width: 43px;
                padding: 0 5px;
                text-align: center;
                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;
                color: #53150f;
            }

            .inventory-v11-detail-mechanics {
                border: 1px solid rgba(205,187,159,.55);
                border-radius: 14px;
                background: rgba(255,253,249,.58);
                padding: 13px 14px;
            }

            .inventory-v11-detail-section-title {
                display: flex;
                align-items: center;
                gap: 9px;
                margin-bottom: 12px;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .10em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v11-detail-section-title::after {
                content: '';
                height: 1px;
                flex: 1;
                background: linear-gradient(
                    90deg,
                    rgba(205,187,159,.75),
                    transparent
                );
            }

            .inventory-v11-ability-card {
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 14px;
                background: rgba(255,253,249,.76);
                box-shadow: 0 2px 8px rgba(83,21,15,.035);
            }

            .inventory-v11-ability-header {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px 14px;
                border-bottom: 1px solid rgba(216,199,171,.52);
                background: rgba(239,233,220,.38);
                padding: 10px 12px;
            }

            .inventory-v11-ability-body {
                padding: 11px 13px 13px;
            }

            .inventory-v11-recovery-pill {
                display: inline-flex;
                align-items: center;
                min-height: 28px;
                border-radius: 999px;
                background: rgba(107,29,20,.065);
                padding: 0 9px;
                white-space: nowrap;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .045em;
                text-transform: uppercase;
                color: #6b1d14;
            }

            .inventory-v10-tooltip-panel {
                width: min(390px, 82vw);
                max-height: 280px;
                overflow: hidden;
                padding: 12px 13px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                line-height: 1.58;
                text-align: left;
            }

            .inventory-v10-tracker {
                display: inline-flex;
                align-items: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.82);
                border-radius: 10px;
                background: rgba(255,253,249,.96);
                box-shadow: 0 1px 0 rgba(255,255,255,.75) inset;
            }
            .inventory-v10-tracker button {
                display: flex;
                width: 30px;
                height: 30px;
                align-items: center;
                justify-content: center;
                color: #8c6239;
                font-size: 15px;
                font-weight: 900;
            }
            .inventory-v10-tracker button:hover { background: rgba(239,233,220,.72); color: #53150f; }
            .inventory-v10-tracker strong {
                min-width: 52px;
                padding: 0 8px;
                text-align: center;
                font-family: Georgia, serif;
                font-size: 12px;
                color: #53150f;
            }

            /* ============================================================
               INVENTÁRIO V14 — frente simplificada
            ============================================================ */

            .inventory-v14 {
                color: #53150f;
            }

            .inventory-v14-toolbar {
                position: relative;
                z-index: 45;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid rgba(205,187,159,.58);
                padding: 0 0 14px;
            }

            .inventory-v14-actions,
            .inventory-v14-toolbar-right {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .inventory-v14-actions {
                flex-wrap: wrap;
            }

            .inventory-v14-toolbar-right {
                margin-left: auto;
            }

            .inventory-v14-primary,
            .inventory-v14-action,
            .inventory-v14-close,
            .inventory-v14-wallet-trigger {
                border: 1px solid rgba(205,187,159,.78);
                background: #faf8f2;
                transition:
                    background .14s ease,
                    border-color .14s ease,
                    color .14s ease;
            }

            .inventory-v14-primary,
            .inventory-v14-action {
                display: inline-flex;
                min-height: 40px;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border-radius: 10px;
                padding: 0 14px;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .035em;
            }

            .inventory-v14-primary {
                border-color: #6b1d14;
                background: #6b1d14;
                color: #faf8f2;
            }

            .inventory-v14-primary:hover {
                border-color: #53150f;
                background: #53150f;
            }

            .inventory-v14-plus {
                margin-top: -1px;
                font-size: 18px;
                font-weight: 500;
                line-height: 1;
            }

            .inventory-v14-action {
                color: #6b1d14;
            }

            .inventory-v14-action-muted {
                color: #8c6239;
            }

            .inventory-v14-action:hover,
            .inventory-v14-close:hover,
            .inventory-v14-wallet-trigger:hover {
                border-color: rgba(140,98,57,.50);
                background: #f1ece2;
            }

            .inventory-v14-badge {
                display: inline-flex;
                min-width: 22px;
                height: 22px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: rgba(107,29,20,.075);
                padding: 0 6px;
                font-family: Georgia, serif;
                font-size: 12px;
                color: #6b1d14;
            }

            .inventory-v14-close {
                display: flex;
                width: 40px;
                height: 40px;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                color: #8c6239;
            }

            .inventory-v14-close svg {
                width: 18px;
                height: 18px;
            }

            .inventory-v14-wallet-wrap {
                position: relative;
            }

            .inventory-v14-wallet-trigger {
                display: flex;
                min-width: 126px;
                height: 44px;
                align-items: center;
                gap: 9px;
                border-radius: 12px;
                padding: 4px 10px 4px 8px;
                text-align: left;
                color: #53150f;
            }

            .inventory-v14-pouch {
                display: flex;
                width: 31px;
                height: 31px;
                flex: 0 0 31px;
                align-items: center;
                justify-content: center;
                border-radius: 9px;
                background: #eee3d1;
                color: #8c6239;
            }

            .inventory-v14-pouch svg {
                width: 25px;
                height: 25px;
            }

            .inventory-v14-pouch path,
            .inventory-v14-pouch circle {
                stroke: currentColor;
                stroke-width: 1.45;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            .inventory-v14-wallet-copy {
                display: flex;
                min-width: 45px;
                flex-direction: column;
                line-height: 1.05;
            }

            .inventory-v14-wallet-label {
                margin-bottom: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v14-wallet-copy strong {
                font-family: Georgia, serif;
                font-size: 18px;
                font-weight: 900;
                color: #53150f;
            }

            .inventory-v14-wallet-chevron {
                width: 15px;
                height: 15px;
                margin-left: auto;
                color: #8c6239;
                transition: transform .14s ease;
            }

            .inventory-v14-wallet-chevron.is-open {
                transform: rotate(180deg);
            }

            .inventory-v14-wallet-panel {
                position: absolute;
                top: calc(100% + 8px);
                right: 0;
                z-index: 90;
                width: 330px;
                overflow: hidden;
                border: 1px solid #cdbb9f;
                border-radius: 14px;
                background: #faf8f2;
            }

            .inventory-v14-wallet-panel-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                border-bottom: 1px solid rgba(205,187,159,.55);
                padding: 12px 13px;
            }

            .inventory-v14-wallet-panel-head > div {
                min-width: 0;
            }

            .inventory-v14-wallet-panel-head strong {
                display: block;
                font-family: Georgia, serif;
                font-size: 15px;
                color: #53150f;
            }

            .inventory-v14-wallet-panel-head span {
                display: block;
                margin-top: 1px;
                font-size: 10px;
                color: #8c6239;
            }

            .inventory-v14-saving {
                flex: 0 0 auto;
                font-size: 10px !important;
                font-weight: 800;
                color: #8c6239 !important;
            }

            .inventory-v14-coins {
                padding: 5px 8px 8px;
            }

            .inventory-v14-coin-row {
                display: grid;
                min-height: 48px;
                grid-template-columns: 34px minmax(0, 1fr) 86px;
                align-items: center;
                gap: 9px;
                border-bottom: 1px solid rgba(216,199,171,.48);
                padding: 5px 4px;
            }

            .inventory-v14-coin-row:last-child {
                border-bottom: 0;
            }

            .inventory-v14-coin-icon {
                display: flex;
                width: 28px;
                height: 28px;
                align-items: center;
                justify-content: center;
                border: 1px solid currentColor;
                border-radius: 50%;
            }

            .inventory-v14-coin-icon svg {
                width: 22px;
                height: 22px;
            }

            .inventory-v14-coin-icon circle,
            .inventory-v14-coin-icon path {
                stroke: currentColor;
                stroke-width: 1.25;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            .inventory-v14-coin-icon.coin-copper {
                background: #f3ddcf;
                color: #a4512d;
            }

            .inventory-v14-coin-icon.coin-silver {
                background: #ececea;
                color: #777a7d;
            }

            .inventory-v14-coin-icon.coin-electrum {
                background: #eee7c8;
                color: #8f7e38;
            }

            .inventory-v14-coin-icon.coin-gold {
                background: #f5e3ad;
                color: #b17812;
            }

            .inventory-v14-coin-icon.coin-platinum {
                background: #edf0ed;
                color: #7b8582;
            }

            .inventory-v14-coin-name {
                display: flex;
                min-width: 0;
                align-items: baseline;
                gap: 7px;
            }

            .inventory-v14-coin-name b {
                width: 23px;
                font-size: 11px;
                color: #53150f;
            }

            .inventory-v14-coin-name small {
                overflow: hidden;
                font-size: 12px;
                color: #6b5548;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v14-coin-row input {
                width: 100%;
                height: 34px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #fffdf9;
                padding: 0 9px;
                text-align: right;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
                color: #53150f;
                outline: none;
            }

            .inventory-v14-coin-row input:focus {
                border-color: #8c6239;
                box-shadow: 0 0 0 2px rgba(140,98,57,.07);
            }

            .inventory-v14-attunement {
                border-bottom: 1px solid rgba(205,187,159,.58);
                padding: 18px 0;
            }

            .inventory-v14-section-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-bottom: 12px;
            }

            .inventory-v14-section-title {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 10px;
            }

            .inventory-v14-diamond {
                display: flex;
                width: 27px;
                height: 27px;
                flex: 0 0 27px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.75);
                border-radius: 8px;
                color: #b29161;
                font-size: 10px;
            }

            .inventory-v14-title-line {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .inventory-v14-title-line h3 {
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
                line-height: 1.1;
                color: #53150f;
            }

            .inventory-v14-count {
                display: inline-flex;
                min-height: 24px;
                align-items: center;
                border-radius: 999px;
                background: #efe9dc;
                padding: 0 8px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #8c6239;
            }

            .inventory-v14-section-title p {
                margin-top: 3px;
                font-size: 12px;
                color: #8c6239;
            }

            .inventory-v14-text-action {
                flex: 0 0 auto;
                border-bottom: 1px solid transparent;
                padding: 3px 0;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                color: #6b1d14;
            }

            .inventory-v14-text-action:hover {
                border-bottom-color: currentColor;
            }

            .inventory-v14-slots {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 9px;
            }

            .inventory-v14-slot {
                min-height: 82px;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 12px;
                background: #faf8f2;
                padding: 8px;
                text-align: left;
                transition: border-color .14s ease, background .14s ease;
            }

            .inventory-v14-slot:hover {
                border-color: rgba(140,98,57,.56);
                background: #f5f0e6;
            }

            .inventory-v14-slot-content {
                display: flex;
                height: 100%;
                align-items: center;
                gap: 10px;
            }

            .inventory-v14-slot-image {
                display: flex;
                width: 58px !important;
                height: 58px !important;
                flex: 0 0 58px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(216,199,171,.65);
                border-radius: 9px;
                background: #fffdf9;
            }

            .inventory-v14-slot-image span {
                font-family: Georgia, serif;
                font-size: 20px;
                color: rgba(140,98,57,.34);
            }

            .inventory-v14-slot-copy {
                min-width: 0;
                flex: 1;
            }

            .inventory-v14-slot-copy small {
                display: block;
                margin-bottom: 3px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .065em;
                text-transform: uppercase;
                color: #a07855;
            }

            .inventory-v14-slot-copy strong {
                display: block;
                overflow: hidden;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
                line-height: 1.15;
                color: #53150f;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v14-slot-copy > span {
                display: block;
                margin-top: 3px;
                overflow: hidden;
                font-size: 11px;
                color: #8c6239;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v14-slot-empty {
                display: flex;
                min-height: 64px;
                align-items: center;
                justify-content: center;
                gap: 7px;
                color: #aa8b68;
                font-size: 11px;
                font-weight: 800;
            }

            .inventory-v14-slot-plus {
                font-size: 18px;
                font-weight: 400;
            }

            .inventory-v14-extra-attuned {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px;
                margin-top: 9px;
            }

            .inventory-v14-extra-attuned > span {
                margin-right: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v14-extra-attuned button {
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 999px;
                background: #faf8f2;
                padding: 4px 9px;
                font-size: 10px;
                font-weight: 800;
                color: #53150f;
            }

            .inventory-v14 > .pt-4 {
                padding-top: 18px !important;
            }

            .inventory-v14-group-head {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 10px;
                border-bottom: 1px solid rgba(205,187,159,.58);
                padding-bottom: 9px;
            }

            .inventory-v14-group-head > div {
                min-width: 0;
            }

            .inventory-v14-group-head h3 {
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
                line-height: 1.12;
                color: #53150f;
            }

            .inventory-v14-group-head p {
                margin-top: 3px;
                font-size: 12px;
                color: #9a795b;
            }

            .inventory-v14-group-count {
                display: inline-flex;
                min-width: 30px;
                height: 28px;
                margin-left: auto;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.64);
                border-radius: 999px;
                background: #faf8f2;
                padding: 0 8px;
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                color: #8c6239;
            }

            .inventory-v14 .inventory-v11-item-card {
                border-color: rgba(205,187,159,.68);
                border-radius: 14px;
                background: #faf8f2;
                box-shadow: none;
            }

            .inventory-v14 .inventory-v11-item-card::before {
                top: 10px;
                bottom: 10px;
                width: 3px;
                background: rgba(140,98,57,.36);
            }

            .inventory-v14 .inventory-v11-item-card.magical::before {
                background: #6b1d14;
            }

            .inventory-v14 .inventory-v11-item-card:hover,
            .inventory-v14 .inventory-v11-item-card:focus-within {
                transform: none;
                border-color: rgba(140,98,57,.55);
                background: #f7f2e8;
                box-shadow: none;
            }

            .inventory-v14 .inventory-v11-item-card > div:first-child {
                gap: 14px !important;
                padding: 13px 14px 13px 17px !important;
            }

            .inventory-v14 .inventory-v11-item-card > div:first-child > .inventory-v10-thumb {
                width: 82px !important;
                height: 82px !important;
                border-radius: 11px !important;
                background: #f3eee4;
            }

            .inventory-v14 .inventory-v11-item-card h4 {
                font-size: 18px !important;
                line-height: 1.15 !important;
            }

            .inventory-v14 .inventory-v11-item-card em {
                font-size: 13px !important;
                line-height: 1.35 !important;
            }

            .inventory-v14 .inventory-v11-item-card p {
                font-size: 12px !important;
            }

            .inventory-v14 .inventory-v11-feature-strip {
                border-top-color: rgba(216,199,171,.52);
                background: #f4efe5;
            }

            .inventory-v14 .inventory-v11-feature-usage {
                background: #faf8f2;
            }

            @media (max-width: 760px) {
                .inventory-v14-toolbar {
                    align-items: flex-start;
                }

                .inventory-v14-toolbar-right {
                    position: static;
                }

                .inventory-v14-wallet-panel {
                    right: 0;
                    width: min(330px, calc(100vw - 34px));
                }

                .inventory-v14-slots {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 560px) {
                .inventory-v14-toolbar {
                    flex-direction: column;
                }

                .inventory-v14-toolbar-right {
                    width: 100%;
                    justify-content: flex-end;
                }

                .inventory-v14-actions {
                    width: 100%;
                }

                .inventory-v14-primary,
                .inventory-v14-action {
                    flex: 1;
                }

                .inventory-v14-section-head {
                    align-items: flex-start;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V15 — EQUIPAMENTO MAIS ORGANIZADO
            |--------------------------------------------------------------------------
            */

            /*
            | O topo já possui espaçamento suficiente; removemos o filete longo
            | para não adicionar mais uma divisão visual ao drawer.
            */
            .inventory-v15 .inventory-v14-toolbar {
                border-bottom: 0;

                padding-bottom: 10px;
            }

            /*
            |--------------------------------------------------------------------------
            | ESTADO + AÇÃO NO CARD
            |--------------------------------------------------------------------------
            */

            .inventory-v15-card-actions {
                display: flex;

                align-items: center;
                justify-content: flex-end;

                gap: 7px;

                margin-top: 8px;
            }

            .inventory-v15-equipped-state {
                display: inline-flex;

                min-height: 28px;

                align-items: center;

                gap: 6px;

                border:
                    1px solid
                    rgba(205,187,159,.44);

                border-radius: 999px;

                background:
                    rgba(247,240,230,.72);

                padding:
                    0 8px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                color: #92745c;
            }

            .inventory-v15-equipped-state.is-active {
                border-color:
                    rgba(107,29,20,.18);

                background:
                    rgba(107,29,20,.055);

                color: #6b1d14;
            }

            .inventory-v15-equipped-dot {
                width: 6px;
                height: 6px;

                flex: 0 0 6px;

                border:
                    1px solid
                    rgba(140,98,57,.55);

                border-radius: 50%;

                background: #faf8f2;
            }

            .inventory-v15-equipped-state.is-active
            .inventory-v15-equipped-dot {
                border-color: #6b1d14;
                background: #6b1d14;
            }

            .inventory-v15-equip-toggle,
            .inventory-v15-detail-equip {
                display: inline-flex;

                min-height: 30px;

                align-items: center;
                justify-content: center;

                gap: 6px;

                border:
                    1px solid
                    rgba(205,187,159,.70);

                border-radius: 8px;

                padding:
                    0 9px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                transition:
                    border-color .14s ease,
                    background .14s ease,
                    color .14s ease;
            }

            .inventory-v15-equip-toggle svg,
            .inventory-v15-detail-equip svg {
                width: 13px;
                height: 13px;

                flex: 0 0 13px;
            }

            /*
            | Equipar é ação positiva.
            */
            .inventory-v15-equip-toggle.is-stowed,
            .inventory-v15-detail-equip.is-stowed {
                border-color: #6b1d14;

                background: #6b1d14;

                color: #faf8f2;
            }

            .inventory-v15-equip-toggle.is-stowed:hover,
            .inventory-v15-detail-equip.is-stowed:hover {
                border-color: #53150f;
                background: #53150f;
            }

            /*
            | Guardar apenas muda o estado; não deve parecer destrutivo.
            */
            .inventory-v15-equip-toggle.is-equipped,
            .inventory-v15-detail-equip.is-equipped {
                border-color:
                    rgba(176,140,98,.52);

                background: #f7f0e6;

                color: #8c6239;
            }

            .inventory-v15-equip-toggle.is-equipped:hover,
            .inventory-v15-detail-equip.is-equipped:hover {
                border-color:
                    rgba(140,98,57,.56);

                background: #eadbc8;

                color: #53150f;
            }

            .inventory-v15-equip-toggle:disabled,
            .inventory-v15-detail-equip:disabled {
                cursor: wait;
                opacity: .40;
            }

            /*
            |--------------------------------------------------------------------------
            | DRAWER DE EQUIPADOS
            |--------------------------------------------------------------------------
            */

            .inventory-v15-equipped-drawer-actions {
                display: flex;

                flex-wrap: wrap;

                align-items: center;
                justify-content: flex-end;

                gap: 7px;

                margin-top: 12px;

                border-top:
                    1px solid
                    rgba(216,199,171,.46);

                padding-top: 10px;
            }

            .inventory-v15-drawer-detail {
                display: inline-flex;

                min-height: 30px;

                align-items: center;
                justify-content: center;

                border-radius: 8px;

                padding:
                    0 9px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .04em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v15-drawer-detail:hover {
                background: #efe9dc;
                color: #53150f;
            }

            @media (max-width: 560px) {
                .inventory-v15-card-actions {
                    justify-content: flex-start;
                }

                .inventory-v15-equipped-state {
                    margin-right: auto;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V16 — POLAROIDS + EQUIPADOS
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | TOOLBAR SEM X
            |--------------------------------------------------------------------------
            */

            .inventory-v16 .inventory-v14-toolbar-right {
                min-height: 44px;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKERS MAIS COMPACTOS
            |--------------------------------------------------------------------------
            |
            | Reduzimos o volume dos controles sem reduzir demais o texto.
            |
            */

            .inventory-v16 .inventory-v11-feature-usage {
                gap: 5px;

                border-radius: 8px;

                padding:
                    4px 5px 4px 7px;
            }

            .inventory-v16 .inventory-v11-usage-name {
                max-width: 108px;

                font-size: 10px;
            }

            .inventory-v16 .inventory-v11-usage-recovery {
                font-size: 8px;
            }

            .inventory-v16 .inventory-v11-mini-tracker {
                border-radius: 7px;
            }

            .inventory-v16 .inventory-v11-mini-tracker button {
                width: 22px;
                height: 22px;

                font-size: 12px;
            }

            .inventory-v16 .inventory-v11-mini-tracker strong {
                min-width: 34px;

                padding:
                    0 4px;

                font-size: 10.5px;
            }

            .inventory-v16 .inventory-v10-tracker {
                border-radius: 8px;
            }

            .inventory-v16 .inventory-v10-tracker button {
                width: 25px;
                height: 25px;

                font-size: 13px;
            }

            .inventory-v16 .inventory-v10-tracker strong {
                min-width: 42px;

                padding:
                    0 6px;

                font-size: 11px;
            }

            /*
            |--------------------------------------------------------------------------
            | LISTA PRINCIPAL — CARDS TIPO POLAROID
            |--------------------------------------------------------------------------
            */

            .inventory-v16-items-grid {
                display: grid;

                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(200px, 1fr)
                    );

                gap: 11px;
            }

            .inventory-v16 .inventory-v16-polaroid {
                display: flex;

                min-width: 0;
                min-height: 0;

                flex-direction: column;

                overflow: hidden;

                border-radius: 12px;

                background: #faf8f2;

                box-shadow:
                    0 1px 0 rgba(255,255,255,.78) inset;
            }

            .inventory-v16 .inventory-v16-polaroid::before {
                top: 8px;
                bottom: 8px;

                width: 3px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child {
                display: block !important;

                flex: 1;

                padding:
                    9px 9px 8px 12px !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .inventory-v10-thumb {
                width: 100% !important;
                height: 132px !important;

                border-radius: 8px !important;

                background: #f3eee4;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .inventory-v10-thumb
            img {
                object-fit: contain;

                padding: 4px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                margin-top: 9px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            h4 {
                display: -webkit-box;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 15px !important;
                line-height: 1.15 !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            em {
                display: -webkit-box;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10.5px !important;
                line-height: 1.32 !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            p {
                font-size: 10.5px !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v15-card-actions {
                justify-content: space-between;

                gap: 5px;

                margin-top: 7px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v15-equipped-state {
                min-height: 25px;

                padding:
                    0 7px;

                font-size: 7.5px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v15-equip-toggle {
                min-height: 27px;

                padding:
                    0 8px;

                font-size: 8px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .hidden.shrink-0 {
                display: none !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v11-feature-strip {
                margin-top: auto;

                padding:
                    7px 8px 8px 11px !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v11-feature-strip
            > div {
                gap: 5px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v11-feature-strip
            > div
            > span:first-child {
                width: 100%;

                margin: 0 0 1px;

                font-size: 7px;
            }

            /*
            |--------------------------------------------------------------------------
            | GAVETA DE EQUIPADOS
            |--------------------------------------------------------------------------
            */

            .inventory-v16-equipped-drawer {
                position: absolute;
                inset-block: 0;
                right: 0;
                z-index: 10;

                display: flex;

                width:
                    min(
                        760px,
                        92vw
                    );

                height: 100%;

                flex-direction: column;

                border-left:
                    1px solid
                    #cdbb9f;

                background:
                    linear-gradient(
                        180deg,
                        #faf8f2 0%,
                        #f7f1e8 100%
                    );

                box-shadow:
                    -18px 0 50px
                    rgba(43,29,23,.16);
            }

            .inventory-v16-equipped-header {
                display: flex;

                flex: 0 0 auto;

                align-items: flex-start;
                justify-content: space-between;

                gap: 18px;

                border-bottom:
                    1px solid
                    rgba(205,187,159,.58);

                background:
                    rgba(239,233,220,.58);

                padding:
                    18px 20px 16px;
            }

            .inventory-v16-equipped-header-copy {
                min-width: 0;
            }

            .inventory-v16-equipped-kicker {
                display: block;

                margin-bottom: 4px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .13em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v16-equipped-title-row {
                display: flex;

                align-items: center;

                gap: 9px;
            }

            .inventory-v16-equipped-title-row h3 {
                font-family: Georgia, serif;
                font-size: 24px;
                font-weight: 900;
                line-height: 1;

                color: #53150f;
            }

            .inventory-v16-equipped-count {
                display: inline-flex;

                min-width: 27px;
                height: 27px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.30);

                border-radius: 999px;

                background: #faf8f2;

                padding:
                    0 7px;

                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #6b1d14;
            }

            .inventory-v16-equipped-header p {
                margin-top: 6px;

                font-size: 11px;
                line-height: 1.4;

                color: #8c6239;
            }

            .inventory-v16-close-hint {
                flex: 0 0 auto;

                margin-top: 4px;

                font-size: 8px;
                font-weight: 800;
                letter-spacing: .04em;

                color:
                    rgba(140,98,57,.62);
            }

            .inventory-v16-equipped-body {
                min-height: 0;

                flex: 1;

                overflow-y: auto;

                padding:
                    14px 15px 18px;
            }

            .inventory-v16-equipped-grid {
                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 12px;
            }

            .inventory-v16-equipped-card {
                display: flex;

                min-width: 0;

                flex-direction: column;

                overflow: hidden;

                border:
                    1px solid
                    rgba(205,187,159,.70);

                border-radius: 12px;

                background: #faf8f2;

                box-shadow:
                    0 1px 0 rgba(255,255,255,.86) inset;

                transition:
                    border-color .14s ease,
                    background .14s ease;
            }

            .inventory-v16-equipped-card:hover {
                border-color:
                    rgba(140,98,57,.52);

                background: #fffdf9;
            }

            .inventory-v16-equipped-main {
                display: block;

                width: 100%;

                padding:
                    9px;

                text-align: left;
            }

            .inventory-v16-equipped-photo {
                display: flex;

                width: 100%;
                height: 154px;

                align-items: center;
                justify-content: center;

                overflow: hidden;

                border:
                    1px solid
                    rgba(216,199,171,.60);

                border-radius: 8px;

                background:
                    #f3eee4;
            }

            .inventory-v16-equipped-photo img {
                width: 100%;
                height: 100%;

                object-fit: contain;

                padding: 5px;
            }

            .inventory-v16-equipped-photo > span {
                font-family: Georgia, serif;
                font-size: 28px;

                color:
                    rgba(140,98,57,.28);
            }

            .inventory-v16-equipped-copy {
                padding:
                    9px 2px 1px;
            }

            .inventory-v16-equipped-badges {
                display: flex;

                flex-wrap: wrap;

                gap: 5px;

                margin-bottom: 5px;
            }

            .inventory-v16-equipped-badge {
                display: inline-flex;

                min-height: 20px;

                align-items: center;

                border-radius: 999px;

                background:
                    rgba(107,29,20,.065);

                padding:
                    0 6px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .04em;

                text-transform: uppercase;

                color: #6b1d14;
            }

            .inventory-v16-equipped-badge.is-attuned {
                background: #efe9dc;

                color: #8c6239;
            }

            .inventory-v16-equipped-copy h4 {
                display: -webkit-box;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
                line-height: 1.12;

                color: #53150f;
            }

            .inventory-v16-equipped-copy em {
                display: -webkit-box;

                margin-top: 4px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10.5px;
                font-weight: 700;
                line-height: 1.35;

                color: #6b5548;
            }

            .inventory-v16-equipped-mechanics {
                display: -webkit-box;

                margin-top: 6px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10px;
                font-weight: 800;
                line-height: 1.35;

                color: #8c6239;
            }

            .inventory-v16-equipped-description {
                display: -webkit-box;

                margin-top: 4px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10px;
                line-height: 1.4;

                color:
                    rgba(107,85,72,.90);
            }

            .inventory-v16-equipped-trackers {
                display: flex;

                flex-wrap: wrap;

                align-items: center;

                gap: 5px;

                margin-top: auto;

                border-top:
                    1px solid
                    rgba(216,199,171,.48);

                background: #f4efe5;

                padding:
                    8px 9px;
            }

            .inventory-v16-tracker-label {
                width: 100%;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .09em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.78);
            }

            .inventory-v16-more-trackers {
                display: inline-flex;

                min-width: 23px;
                height: 23px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.58);

                border-radius: 999px;

                background: #faf8f2;

                padding:
                    0 6px;

                font-size: 8px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v16-equipped-actions {
                display: flex;

                align-items: center;
                justify-content: space-between;

                gap: 7px;

                border-top:
                    1px solid
                    rgba(216,199,171,.48);

                padding:
                    8px 9px;
            }

            .inventory-v16-view-item {
                min-height: 28px;

                border-radius: 7px;

                padding:
                    0 8px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v16-view-item:hover {
                background: #efe9dc;

                color: #53150f;
            }

            .inventory-v16-equipped-empty {
                display: flex;

                min-height: 220px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                border:
                    1px dashed
                    rgba(205,187,159,.68);

                border-radius: 14px;

                background:
                    rgba(255,253,249,.62);

                padding:
                    30px;

                text-align: center;
            }

            .inventory-v16-empty-mark {
                margin-bottom: 8px;

                font-family: Georgia, serif;
                font-size: 30px;

                color:
                    rgba(140,98,57,.35);
            }

            .inventory-v16-equipped-empty strong {
                font-family: Georgia, serif;
                font-size: 18px;

                color: #53150f;
            }

            .inventory-v16-equipped-empty p {
                max-width: 300px;

                margin-top: 4px;

                font-size: 10.5px;
                line-height: 1.45;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 720px) {
                .inventory-v16-items-grid {
                    grid-template-columns:
                        repeat(
                            2,
                            minmax(0, 1fr)
                        );
                }

                .inventory-v16-equipped-drawer {
                    width:
                        min(
                            620px,
                            94vw
                        );
                }
            }

            @media (max-width: 520px) {
                .inventory-v16-items-grid,
                .inventory-v16-equipped-grid {
                    grid-template-columns: 1fr;
                }

                .inventory-v16-equipped-header {
                    padding:
                        15px 14px 13px;
                }

                .inventory-v16-close-hint {
                    display: none;
                }

                .inventory-v16-equipped-body {
                    padding:
                        11px 10px 14px;
                }

                .inventory-v16
                .inventory-v16-polaroid
                > div:first-child
                > .inventory-v10-thumb {
                    height: 160px !important;
                }

                .inventory-v16-equipped-photo {
                    height: 180px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V17 — MODAL EDITORIAL + SINTONIZAÇÃO
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-modal {
                border-color:
                    rgba(176,140,98,.58) !important;

                background:
                    linear-gradient(
                        180deg,
                        #fbf8f1 0%,
                        #f8f2e8 100%
                    ) !important;

                box-shadow:
                    0 28px 80px rgba(43,29,23,.24),
                    0 1px 0 rgba(255,255,255,.86) inset !important;
            }

            /*
            |--------------------------------------------------------------------------
            | TOPBAR
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-topbar {
                min-height: 52px;

                border-bottom-color:
                    rgba(160,119,77,.30) !important;

                background:
                    linear-gradient(
                        180deg,
                        #f1e5d6 0%,
                        #eadbc8 100%
                    ) !important;

                padding:
                    9px 13px 9px 16px !important;
            }

            .inventory-v17-detail-topbar
            > span:first-child {
                font-size: 8.5px !important;
                letter-spacing: .13em !important;

                color: #7d604d !important;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTEÚDO PRINCIPAL
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-scroll {
                scrollbar-color:
                    rgba(140,98,57,.45)
                    transparent;
            }

            .inventory-v17-detail-identity {
                align-self: stretch !important;
            }

            .inventory-v17-title-row {
                display: flex;

                align-items: flex-start;
                justify-content: space-between;

                gap: 12px;
            }

            .inventory-v17-item-kicker {
                display: block;

                margin-bottom: 4px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.78);
            }

            .inventory-v17-item-title {
                font-family: Georgia, serif;
                font-size: 30px;
                font-weight: 900;
                line-height: 1.02;

                color: #53150f;
            }

            .inventory-v17-status-badge {
                display: inline-flex;

                min-height: 25px;

                flex: 0 0 auto;

                align-items: center;

                gap: 6px;

                border:
                    1px solid
                    rgba(107,29,20,.16);

                border-radius: 999px;

                background:
                    rgba(107,29,20,.055);

                padding:
                    0 8px;

                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .055em;

                text-transform: uppercase;

                color: #6b1d14;
            }

            .inventory-v17-status-dot {
                width: 6px;
                height: 6px;

                border-radius: 50%;

                background: #6b1d14;
            }

            .inventory-v17-classification {
                display: block;

                margin-top: 7px;

                font-size: 13px;
                font-weight: 700;
                line-height: 1.4;

                color: #6b5548;
            }

            /*
            |--------------------------------------------------------------------------
            | SINTONIZAÇÃO
            |--------------------------------------------------------------------------
            |
            | Antes era um pill pequeno junto da classificação.
            | Agora vira um estado de gameplay claro, visível e acionável.
            |
            */

            .inventory-v17-attunement {
                display: grid;

                grid-template-columns:
                    38px
                    minmax(0, 1fr)
                    auto;

                align-items: center;

                gap: 10px;

                margin-top: 14px;

                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.42);

                border-radius: 11px;

                background:
                    linear-gradient(
                        180deg,
                        #f5eadc 0%,
                        #f0e2d1 100%
                    );

                padding:
                    9px 10px;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.62);
            }

            .inventory-v17-attunement.is-attuned {
                border-color:
                    rgba(107,29,20,.30);

                background:
                    linear-gradient(
                        180deg,
                        rgba(107,29,20,.070) 0%,
                        rgba(107,29,20,.040) 100%
                    );
            }

            .inventory-v17-attunement-icon {
                display: flex;

                width: 38px;
                height: 38px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 9px;

                background: #fffdf8;

                font-family: Georgia, serif;
                font-size: 18px;

                color: #8c6239;
            }

            .inventory-v17-attunement.is-attuned
            .inventory-v17-attunement-icon {
                border-color:
                    rgba(107,29,20,.22);

                background:
                    rgba(107,29,20,.055);

                color: #6b1d14;
            }

            .inventory-v17-attunement-copy {
                min-width: 0;
            }

            .inventory-v17-attunement-kicker {
                display: block;

                margin-bottom: 1px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .11em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v17-attunement-copy strong {
                display: block;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v17-attunement-copy p {
                margin-top: 2px;

                max-width: 470px;

                font-size: 9.5px;
                line-height: 1.38;

                color: #7d604d;
            }

            .inventory-v17-attunement-action {
                display: inline-flex;

                min-height: 32px;

                flex: 0 0 auto;

                align-items: center;
                justify-content: center;

                gap: 5px;

                border-radius: 8px;

                padding:
                    0 10px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            .inventory-v17-attunement-action.is-primary {
                border:
                    1px solid #6b1d14;

                background: #6b1d14;

                color: #faf8f2;
            }

            .inventory-v17-attunement-action.is-primary:hover {
                border-color: #53150f;
                background: #53150f;
            }

            .inventory-v17-attunement-action.is-secondary {
                border:
                    1px solid
                    rgba(107,29,20,.22);

                background:
                    rgba(255,253,248,.72);

                color: #6b1d14;
            }

            .inventory-v17-attunement-action.is-secondary:hover {
                background:
                    rgba(107,29,20,.070);
            }

            /*
            |--------------------------------------------------------------------------
            | MECÂNICAS
            |--------------------------------------------------------------------------
            */

            .inventory-v17
            .inventory-v11-detail-mechanics {
                margin-top: 14px !important;

                border-color:
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                padding:
                    11px 12px;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.70);
            }

            .inventory-v17
            .inventory-v11-detail-mechanics
            p {
                font-size: 12.5px !important;
                line-height: 1.5 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | SEÇÕES DO MODAL
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-section {
                margin-top: 24px !important;
            }

            .inventory-v17
            .inventory-v11-detail-section-title {
                margin-bottom: 10px;

                font-size: 9px;
                letter-spacing: .095em;
            }

            .inventory-v17
            .inventory-v11-ability-card {
                border-color:
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.68);
            }

            .inventory-v17
            .inventory-v11-ability-header {
                border-bottom-color:
                    rgba(188,154,111,.28);

                background: #f0e4d5;

                padding:
                    9px 11px;
            }

            .inventory-v17
            .inventory-v11-ability-body {
                padding:
                    10px 12px 12px;
            }

            /*
            |--------------------------------------------------------------------------
            | FOTO
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-modal
            .inventory-v10-thumb {
                border-color:
                    rgba(176,140,98,.42) !important;

                border-radius: 12px !important;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf8 0%,
                        #f2eadf 100%
                    ) !important;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.76),
                    0 5px 18px rgba(83,21,15,.05) !important;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 640px) {
                .inventory-v17-attunement {
                    grid-template-columns:
                        36px
                        minmax(0, 1fr);
                }

                .inventory-v17-attunement-action {
                    grid-column:
                        1 / -1;

                    width: 100%;
                }

                .inventory-v17-item-title {
                    font-size: 26px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V18 — SINTONIZAÇÃO NA BARRA DE AÇÕES
            |--------------------------------------------------------------------------
            |
            | A sintonização deixa de interromper o fluxo de leitura do item.
            | Ela passa a ser uma ação compacta ao lado de Equipar/Guardar.
            |
            */

            .inventory-v18-attunement-toggle {
                display: inline-flex;

                min-height: 30px;

                align-items: center;
                justify-content: center;

                gap: 5px;

                border:
                    1px solid
                    rgba(176,140,98,.48);

                border-radius: 8px;

                padding:
                    0 9px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            /*
            | Ainda não sintonizado:
            | ação disponível, mas sem competir com Editar.
            */
            .inventory-v18-attunement-toggle.is-unattuned {
                background: #f7f0e6;

                color: #8c6239;
            }

            .inventory-v18-attunement-toggle.is-unattuned:hover {
                border-color:
                    rgba(107,29,20,.30);

                background: #eadbc8;

                color: #6b1d14;
            }

            /*
            | Já sintonizado:
            | o estado fica visível pelo vinho suave, sem virar um banner.
            */
            .inventory-v18-attunement-toggle.is-attuned {
                border-color:
                    rgba(107,29,20,.22);

                background:
                    rgba(107,29,20,.065);

                color: #6b1d14;
            }

            .inventory-v18-attunement-toggle.is-attuned:hover {
                border-color:
                    rgba(107,29,20,.34);

                background:
                    rgba(107,29,20,.10);

                color: #53150f;
            }

            .inventory-v18-attunement-toggle:disabled {
                cursor: wait;
                opacity: .40;
            }

            .inventory-v18-attunement-mark {
                font-family: Georgia, serif;
                font-size: 10px;
                line-height: 1;
            }

            /*
            | Como o painel grande saiu, a mecânica volta a ficar próxima
            | da classificação e a leitura segue naturalmente para baixo.
            */
            .inventory-v18
            .inventory-v11-detail-mechanics {
                margin-top: 13px !important;
            }

            @media (max-width: 640px) {
                .inventory-v18-attunement-toggle {
                    min-height: 29px;

                    padding:
                        0 8px;

                    font-size: 8px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V19 — MECÂNICAS MAIS LIMPAS
            |--------------------------------------------------------------------------
            */

            .inventory-v19
            .inventory-v19-detail-mechanics {
                padding: 0 !important;

                overflow: visible;

                border: 0 !important;

                background:
                    transparent !important;

                box-shadow:
                    none !important;
            }

            .inventory-v19-weapon-block {
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.72);
            }

            /*
            | Identidade mecânica + dano.
            */
            .inventory-v19-weapon-heading {
                display: flex;

                min-height: 48px;

                align-items: center;
                justify-content: space-between;

                gap: 18px;

                background: #f7f0e6;

                padding:
                    9px 12px;
            }

            .inventory-v19-weapon-heading
            > strong {
                min-width: 0;

                font-family: Georgia, serif;
                font-size: 13.5px;
                font-weight: 900;
                line-height: 1.25;

                color: #53150f;
            }

            .inventory-v19-damage-line {
                display: inline-flex;

                flex: 0 0 auto;

                align-items: baseline;

                gap: 5px;

                color: #7d604d;
            }

            .inventory-v19-damage-line
            > span {
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .07em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v19-damage-line
            > b {
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #6b1d14;
            }

            /*
            | Propriedades / Maestrias em linhas próprias.
            */
            .inventory-v19-mechanic-row {
                display: grid;

                grid-template-columns:
                    88px
                    minmax(0, 1fr);

                align-items: center;

                gap: 9px;

                min-height: 40px;

                border-top:
                    1px solid
                    rgba(188,154,111,.26);

                padding:
                    7px 12px;
            }

            .inventory-v19-mechanic-label {
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .075em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v19-mechanic-values {
                display: flex;

                min-width: 0;

                flex-wrap: wrap;

                gap: 6px;
            }

            .inventory-v19
            .inventory-v19-mechanic-values
            .inventory-v10-chip {
                min-height: 27px;

                padding:
                    0 9px;

                font-size: 9px;
            }

            /*
            | Armadura / escudo / propriedades custom continuam suportados.
            */
            .inventory-v19-other-mechanics {
                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                padding:
                    10px 12px;

                font-size: 12px;
                font-weight: 700;
                line-height: 1.5;

                color: #5f3a27;
            }

            .inventory-v19-weapon-block
            + .inventory-v19-other-mechanics {
                margin-top: 8px;
            }

            @media (max-width: 560px) {
                .inventory-v19-weapon-heading {
                    align-items: flex-start;

                    flex-direction: column;

                    gap: 4px;
                }

                .inventory-v19-mechanic-row {
                    grid-template-columns: 1fr;

                    gap: 5px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V20 — MECÂNICA EDITORIAL
            |--------------------------------------------------------------------------
            |
            | Sem caixa, sem tabela e sem chips.
            | A informação vira parte natural da leitura do item.
            |
            */

            .inventory-v20-editorial-mechanics {
                margin-top: 10px;

                max-width: 100%;

                color: #5f4636;
            }

            .inventory-v20-weapon-text {
                display: flex;

                flex-direction: column;

                gap: 4px;
            }

            /*
            | Arma Marcial - Katana (1d10 Cortante/Energia)
            */
            .inventory-v20-weapon-line {
                font-family: Georgia, serif;
                font-size: 12.5px;
                font-weight: 700;
                font-style: italic;
                line-height: 1.45;

                color: #6b5548;
            }

            /*
            | Propriedades: Acuidade, Leve
            | Maestrias: (Sap)
            */
            .inventory-v20-detail-line {
                font-size: 10.5px;
                line-height: 1.45;

                color: #6b5548;
            }

            .inventory-v20-detail-line
            strong {
                margin-right: 4px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .065em;

                text-transform: uppercase;

                color: #8c6239;
            }

            /*
            | Remove visualmente qualquer herança da caixa V19.
            */
            .inventory-v20
            .inventory-v19-detail-mechanics {
                border: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            @media (max-width: 560px) {
                .inventory-v20-weapon-line {
                    font-size: 12px;
                }

                .inventory-v20-detail-line {
                    font-size: 10px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V21 — TOOLTIPS EDITORIAIS
            |--------------------------------------------------------------------------
            |
            | Propriedades e Maestrias continuam parecendo texto normal.
            | A descrição só aparece ao passar o mouse ou focar o termo.
            |
            */

            .inventory-v21-inline-tooltip {
                position: relative;

                display: inline;

                cursor: help;
            }

            /*
            | Não vira chip, botão, badge ou link.
            | Mantém exatamente a aparência tipográfica da linha V20.
            */
            .inventory-v21-inline-term {
                display: inline;

                padding: 0;

                border: 0;

                background: transparent;

                color: inherit;

                font: inherit;
                line-height: inherit;

                text-decoration:
                    underline
                    dotted
                    rgba(140,98,57,.34);

                text-decoration-thickness: 1px;
                text-underline-offset: 2px;

                outline: none;
            }

            .inventory-v21-inline-term:focus-visible {
                border-radius: 2px;

                outline:
                    1px solid
                    rgba(140,98,57,.38);

                outline-offset: 2px;
            }

            /*
            |--------------------------------------------------------------------------
            | PAINEL
            |--------------------------------------------------------------------------
            */

            .inventory-v21-tooltip-panel {
                position: absolute;

                left: 0;
                bottom:
                    calc(100% + 8px);

                z-index: 80;

                display: block;

                width:
                    min(
                        280px,
                        72vw
                    );

                max-width: 280px;

                visibility: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.58);

                border-radius: 8px;

                background: #fbf8f1;

                padding:
                    8px 9px;

                opacity: 0;

                box-shadow:
                    0 10px 28px
                    rgba(43,29,23,.16),
                    inset 0 1px 0
                    rgba(255,255,255,.74);

                pointer-events: none;

                transform:
                    translateY(3px);

                transition:
                    opacity .12s ease,
                    transform .12s ease,
                    visibility .12s ease;

                white-space: normal;

                text-align: left;
            }

            .inventory-v21-inline-tooltip:hover
            .inventory-v21-tooltip-panel,
            .inventory-v21-inline-tooltip:focus-within
            .inventory-v21-tooltip-panel {
                visibility: visible;

                opacity: 1;

                transform:
                    translateY(0);
            }

            .inventory-v21-tooltip-panel::after {
                content: '';

                position: absolute;

                top: 100%;
                left: 14px;

                width: 8px;
                height: 8px;

                border-right:
                    1px solid
                    rgba(176,140,98,.58);

                border-bottom:
                    1px solid
                    rgba(176,140,98,.58);

                background: #fbf8f1;

                transform:
                    translateY(-4px)
                    rotate(45deg);
            }

            .inventory-v21-tooltip-title {
                display: block;

                margin-bottom: 3px;

                font-family: Georgia, serif;
                font-size: 11.5px;
                font-weight: 900;
                line-height: 1.2;

                color: #53150f;
            }

            .inventory-v21-tooltip-text {
                display: block;

                font-size: 10px;
                font-weight: 500;
                line-height: 1.45;

                color: #6b5548;
            }

            /*
            | Em telas estreitas, posiciona o tooltip de forma mais segura.
            */
            @media (max-width: 560px) {
                .inventory-v21-tooltip-panel {
                    width:
                        min(
                            240px,
                            76vw
                        );
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V22 — CONTEÚDO DOS POLAROIDS
            |--------------------------------------------------------------------------
            |
            | A aparência externa do card permanece a mesma.
            | Mudanças:
            |
            | - nenhuma ação Equipar/Guardar dentro do polaroid;
            | - apenas o badge Equipado permanece quando aplicável;
            | - estado de sintonização não é repetido no card;
            | - habilidades passivas também são mostradas;
            | - a área de habilidades tem altura controlada para manter os
            |   cards visualmente equilibrados.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | CORPO PRINCIPAL
            |--------------------------------------------------------------------------
            */

            .inventory-v22
            .inventory-v16-polaroid
            > div:first-child {
                display: flex !important;

                flex-direction: column;
            }

            .inventory-v22
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                display: flex;

                min-height: 0;

                flex: 1;
                flex-direction: column;
            }

            /*
            | Sem área de ações, o texto usa melhor o espaço restante.
            */
            .inventory-v22
            .inventory-v16-polaroid
            .inventory-v15-card-actions {
                display: none !important;
            }

            /*
            |--------------------------------------------------------------------------
            | HABILIDADES
            |--------------------------------------------------------------------------
            */

            .inventory-v22
            .inventory-v22-card-feature-strip {
                min-height: 92px;

                margin-top: auto;

                border-top:
                    1px solid
                    rgba(216,199,171,.52);

                background: #f4efe5;

                padding:
                    7px 8px 8px 11px !important;
            }

            .inventory-v22-card-feature-head {
                display: flex;

                min-height: 17px;

                align-items: center;
                justify-content: space-between;

                gap: 6px;

                margin-bottom: 4px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .085em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.78);
            }

            .inventory-v22-card-feature-count {
                display: inline-flex;

                min-width: 18px;
                height: 18px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.54);

                border-radius: 999px;

                background:
                    rgba(255,253,249,.66);

                padding:
                    0 5px;

                font-size: 7px;
                letter-spacing: 0;

                color: #8c6239;
            }

            .inventory-v22-card-feature-list {
                display: flex;

                flex-direction: column;

                gap: 4px;
            }

            .inventory-v22-card-feature-row {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    auto;

                min-height: 27px;

                align-items: center;

                gap: 5px;

                border:
                    1px solid
                    rgba(205,187,159,.52);

                border-radius: 7px;

                background:
                    rgba(255,253,249,.72);

                padding:
                    2px 4px 2px 7px;
            }

            .inventory-v22-card-feature-name {
                min-width: 0;

                overflow: hidden;

                font-family: Georgia, serif;
                font-size: 9.5px;
                font-weight: 900;
                line-height: 1.15;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKER DA HABILIDADE
            |--------------------------------------------------------------------------
            */

            .inventory-v22-card-feature-tracker {
                display: flex;

                align-items: center;

                gap: 3px;
            }

            .inventory-v22
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width: 19px;
                height: 19px;

                font-size: 10px;
            }

            .inventory-v22
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width: 29px;

                padding:
                    0 3px;

                font-size: 9px;
            }

            .inventory-v22-card-feature-recovery {
                max-width: 29px;

                overflow: hidden;

                font-size: 7px;
                font-weight: 800;

                color: #8c6239;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /*
            | Habilidade sem tracker.
            */
            .inventory-v22-card-feature-passive {
                display: inline-flex;

                min-height: 19px;

                align-items: center;

                border-radius: 999px;

                background:
                    rgba(140,98,57,.055);

                padding:
                    0 6px;

                font-size: 6.5px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.76);
            }

            .inventory-v22-card-feature-more {
                display: flex;

                align-items: center;
                justify-content: space-between;

                gap: 6px;

                min-height: 18px;

                padding:
                    0 3px;

                font-size: 7px;
                font-weight: 800;

                color:
                    rgba(140,98,57,.75);
            }

            .inventory-v22-card-feature-more
            span:last-child {
                color:
                    rgba(107,29,20,.66);
            }

            .inventory-v22-card-feature-empty {
                display: flex;

                min-height: 50px;

                align-items: center;
                justify-content: center;

                font-size: 8px;
                font-style: italic;

                color:
                    rgba(140,98,57,.52);
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 520px) {
                .inventory-v22
                .inventory-v22-card-feature-strip {
                    min-height: 88px;
                }

                .inventory-v22-card-feature-name {
                    font-size: 10px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V23 — TELA PRINCIPAL EDITORIAL
            |--------------------------------------------------------------------------
            |
            | O objetivo é trazer para a tela principal a mesma linguagem
            | visual do modal de visualização:
            |
            | - superfícies de papel claras;
            | - cabeçalhos bege-avermelhados;
            | - vinho apenas para hierarquia e ações;
            | - blocos bem definidos, sem excesso de linhas;
            | - polaroids preservados.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | TOPO
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            .inventory-v23-main-topbar {
                min-height: 58px;

                border:
                    1px solid
                    rgba(176,140,98,.42);

                border-radius: 13px;

                background:
                    linear-gradient(
                        180deg,
                        #f2e7d8 0%,
                        #eadbc8 100%
                    );

                padding:
                    8px 10px;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.72);

                margin-bottom: 12px;
            }

            .inventory-v23
            .inventory-v14-actions {
                gap: 7px;
            }

            .inventory-v23
            .inventory-v14-primary,
            .inventory-v23
            .inventory-v14-action {
                min-height: 36px;

                border-radius: 9px;

                padding:
                    0 12px;

                font-size: 10px;
            }

            .inventory-v23
            .inventory-v14-wallet-trigger {
                height: 38px;

                border-color:
                    rgba(176,140,98,.46);

                border-radius: 9px;

                background:
                    rgba(255,253,248,.78);
            }

            .inventory-v23
            .inventory-v14-pouch {
                width: 28px;
                height: 28px;

                flex-basis: 28px;

                border-radius: 7px;
            }

            .inventory-v23
            .inventory-v14-wallet-copy strong {
                font-size: 16px;
            }

            /*
            |--------------------------------------------------------------------------
            | SINTONIZAÇÃO
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            .inventory-v23-attunement-panel {
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 13px;

                background:
                    #fbf8f1;

                padding: 0;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.70);
            }

            .inventory-v23
            .inventory-v23-attunement-panel
            .inventory-v14-section-head {
                min-height: 58px;

                margin: 0;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);

                background:
                    linear-gradient(
                        180deg,
                        #f7efe4 0%,
                        #f0e3d2 100%
                    );

                padding:
                    9px 11px;
            }

            .inventory-v23
            .inventory-v14-section-title {
                gap: 9px;
            }

            .inventory-v23
            .inventory-v14-diamond {
                width: 31px;
                height: 31px;

                flex-basis: 31px;

                border-color:
                    rgba(176,140,98,.36);

                background:
                    #fffdf8;

                color: #8c6239;
            }

            .inventory-v23
            .inventory-v14-title-line h3 {
                font-size: 19px;
            }

            .inventory-v23
            .inventory-v14-count {
                min-height: 22px;

                background:
                    rgba(107,29,20,.055);

                font-size: 10.5px;

                color: #6b1d14;
            }

            .inventory-v23
            .inventory-v14-section-title p {
                margin-top: 2px;

                font-size: 10px;

                color: #92745c;
            }

            .inventory-v23
            .inventory-v14-text-action {
                min-height: 29px;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 7px;

                background:
                    rgba(255,253,248,.72);

                padding:
                    0 8px;

                font-size: 8px;

                color: #6b1d14;
            }

            .inventory-v23
            .inventory-v14-text-action:hover {
                border-bottom-color:
                    rgba(176,140,98,.38);

                background: #eadbc8;
            }

            .inventory-v23
            .inventory-v14-slots {
                gap: 8px;

                padding:
                    10px 11px 11px;
            }

            .inventory-v23
            .inventory-v14-slot {
                min-height: 70px;

                border-color:
                    rgba(205,187,159,.62);

                border-radius: 10px;

                background: #fffdf8;

                padding: 7px;
            }

            .inventory-v23
            .inventory-v14-slot.is-filled {
                background:
                    linear-gradient(
                        180deg,
                        #fffdf9 0%,
                        #faf6ee 100%
                    );
            }

            .inventory-v23
            .inventory-v14-slot-image {
                width: 48px !important;
                height: 48px !important;

                flex-basis: 48px;

                border-radius: 8px;
            }

            .inventory-v23
            .inventory-v14-slot-copy small {
                margin-bottom: 2px;

                font-size: 7px;
            }

            .inventory-v23
            .inventory-v14-slot-copy strong {
                font-size: 13px;
            }

            .inventory-v23
            .inventory-v14-slot-copy > span {
                margin-top: 2px;

                font-size: 9px;
            }

            .inventory-v23
            .inventory-v14-slot-empty {
                min-height: 52px;

                font-size: 9px;
            }

            .inventory-v23
            .inventory-v14-extra-attuned {
                margin: 0;

                border-top:
                    1px solid
                    rgba(188,154,111,.24);

                padding:
                    8px 11px 10px;
            }

            /*
            |--------------------------------------------------------------------------
            | LISTA / GRUPOS
            |--------------------------------------------------------------------------
            */

            .inventory-v23-list {
                display: flex;

                flex-direction: column;

                gap: 12px;

                padding-top: 12px;
            }

            .inventory-v23-group-panel {
                overflow: hidden;

                margin: 0;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 13px;

                background:
                    rgba(251,248,241,.74);

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.72);
            }

            .inventory-v23
            .inventory-v23-group-panel
            .inventory-v14-group-head {
                min-height: 58px;

                margin: 0;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);

                background:
                    linear-gradient(
                        180deg,
                        #f7efe4 0%,
                        #f0e3d2 100%
                    );

                padding:
                    9px 12px;
            }

            .inventory-v23
            .inventory-v14-group-head h3 {
                font-size: 19px;
            }

            .inventory-v23
            .inventory-v14-group-head p {
                margin-top: 2px;

                font-size: 10px;

                color: #92745c;
            }

            .inventory-v23
            .inventory-v14-group-count {
                min-width: 27px;
                height: 25px;

                border-color:
                    rgba(176,140,98,.34);

                background: #fffdf8;

                font-size: 11px;
            }

            .inventory-v23
            .inventory-v23-group-panel
            .inventory-v16-items-grid {
                gap: 10px;

                padding:
                    11px;
            }

            /*
            |--------------------------------------------------------------------------
            | POLAROIDS — MESMA APARÊNCIA, LEITURA MAIS LIMPA
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            .inventory-v16-polaroid {
                border-color:
                    rgba(176,140,98,.42);

                background:
                    linear-gradient(
                        180deg,
                        #fffdf9 0%,
                        #faf6ee 100%
                    );
            }

            .inventory-v23
            .inventory-v16-polaroid:hover,
            .inventory-v23
            .inventory-v16-polaroid:focus-within {
                border-color:
                    rgba(140,98,57,.54);

                background:
                    #fffdf9;

                box-shadow:
                    0 8px 20px
                    rgba(83,21,15,.055);
            }

            .inventory-v23-card-mechanics {
                color: #7d604d !important;
            }

            .inventory-v23
            .inventory-v22-card-feature-strip {
                background:
                    linear-gradient(
                        180deg,
                        #f4efe5 0%,
                        #f0e6d8 100%
                    );
            }

            .inventory-v23
            .inventory-v22-card-feature-row {
                background:
                    rgba(255,253,249,.82);
            }

            /*
            |--------------------------------------------------------------------------
            | EMPTY STATE
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            > .py-16 {
                margin-top: 12px;

                border:
                    1px dashed
                    rgba(176,140,98,.48);

                border-radius: 13px;

                background:
                    rgba(251,248,241,.70);
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 760px) {
                .inventory-v23
                .inventory-v23-main-topbar {
                    align-items: flex-start;
                }

                .inventory-v23
                .inventory-v14-toolbar-right {
                    align-self: flex-start;
                }

                .inventory-v23
                .inventory-v14-slots {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 520px) {
                .inventory-v23
                .inventory-v23-main-topbar {
                    border-radius: 11px;

                    padding:
                        8px;
                }

                .inventory-v23
                .inventory-v14-primary,
                .inventory-v23
                .inventory-v14-action {
                    padding:
                        0 10px;
                }

                .inventory-v23
                .inventory-v14-wallet-trigger {
                    min-width: 112px;
                }

                .inventory-v23
                .inventory-v23-group-panel
                .inventory-v16-items-grid {
                    padding:
                        9px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V24 — CATEGORIAS EM GAVETAS
            |--------------------------------------------------------------------------
            |
            | O cabeçalho permanece sempre visível. Clique nele para esconder
            | ou revelar os polaroids. O estado fica salvo para o personagem.
            |
            */

            .inventory-v24
            .inventory-v24-group-drawer {
                transition:
                    border-color .14s ease,
                    background .14s ease;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-collapsed {
                background:
                    rgba(247,240,230,.72);
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO CLICÁVEL
            |--------------------------------------------------------------------------
            */

            .inventory-v24
            .inventory-v23-group-panel
            .inventory-v24-group-trigger {
                display: flex;

                width: 100%;

                align-items: center;

                margin: 0;

                border-bottom: 0;

                text-align: left;

                cursor: pointer;

                transition:
                    background .14s ease;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-open
            .inventory-v24-group-trigger {
                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);
            }

            .inventory-v24
            .inventory-v24-group-trigger:hover {
                background:
                    linear-gradient(
                        180deg,
                        #f3e7d8 0%,
                        #ead9c5 100%
                    ) !important;
            }

            .inventory-v24
            .inventory-v24-group-trigger:focus-visible {
                outline:
                    2px solid
                    rgba(140,98,57,.30);

                outline-offset:
                    -2px;
            }

            .inventory-v24-group-copy {
                min-width: 0;

                flex: 1;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTADOR + CHEVRON
            |--------------------------------------------------------------------------
            */

            .inventory-v24-group-meta {
                display: flex;

                flex: 0 0 auto;

                align-items: center;

                gap: 8px;
            }

            .inventory-v24
            .inventory-v24-group-trigger
            .inventory-v14-group-count {
                margin-left: 0;
            }

            .inventory-v24-group-chevron {
                display: flex;

                width: 29px;
                height: 29px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 8px;

                background:
                    rgba(255,253,248,.68);

                color: #8c6239;

                transition:
                    border-color .14s ease,
                    background .14s ease,
                    color .14s ease;
            }

            .inventory-v24-group-chevron svg {
                width: 15px;
                height: 15px;

                transition:
                    transform .16s ease;
            }

            .inventory-v24-group-chevron.is-open svg {
                transform:
                    rotate(180deg);
            }

            .inventory-v24
            .inventory-v24-group-trigger:hover
            .inventory-v24-group-chevron {
                border-color:
                    rgba(140,98,57,.48);

                background: #fffdf8;

                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTEÚDO RECOLHÍVEL
            |--------------------------------------------------------------------------
            */

            .inventory-v24-group-content {
                overflow: hidden;

                transform-origin: top;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-collapsed
            .inventory-v24-group-trigger {
                min-height: 54px;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-collapsed
            .inventory-v24-group-copy p {
                opacity: .76;
            }

            @media (max-width: 520px) {
                .inventory-v24-group-meta {
                    gap: 6px;
                }

                .inventory-v24-group-chevron {
                    width: 27px;
                    height: 27px;
                }
            }

        

            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V26 — FILTROS POR CATEGORIA + CARDS COMPACTOS
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | FERRAMENTAS DENTRO DA GAVETA
            |--------------------------------------------------------------------------
            */

            .inventory-v26-group-tools {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    182px;

                gap: 7px;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.22);

                background:
                    rgba(247,240,230,.52);

                padding:
                    7px 10px;
            }

            .inventory-v26-group-search {
                position: relative;

                display: flex;

                min-width: 0;

                align-items: center;
            }

            .inventory-v26-group-search-icon {
                position: absolute;
                left: 9px;

                display: flex;

                width: 14px;
                height: 14px;

                align-items: center;
                justify-content: center;

                color:
                    rgba(140,98,57,.68);

                pointer-events: none;
            }

            .inventory-v26-group-search-icon svg {
                width: 14px;
                height: 14px;
            }

            .inventory-v26-group-search-input {
                width: 100%;
                min-height: 32px;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 8px;

                background: #fffdf8;

                padding:
                    0 30px 0 31px;

                font-size: 10px;
                font-weight: 600;

                color: #53150f;

                outline: none;
            }

            .inventory-v26-group-search-input::placeholder {
                color:
                    rgba(140,98,57,.54);
            }

            .inventory-v26-group-search-input:focus {
                border-color:
                    rgba(107,29,20,.32);

                box-shadow:
                    0 0 0 2px
                    rgba(107,29,20,.04);
            }

            .inventory-v26-group-search-clear {
                position: absolute;
                right: 6px;

                display: flex;

                width: 21px;
                height: 21px;

                align-items: center;
                justify-content: center;

                border-radius: 6px;

                font-size: 13px;

                color: #8c6239;
            }

            .inventory-v26-group-search-clear:hover {
                background: #efe9dc;

                color: #53150f;
            }

            .inventory-v26-group-sort {
                display: grid;

                grid-template-columns:
                    auto
                    minmax(0, 1fr);

                align-items: center;

                gap: 6px;
            }

            .inventory-v26-group-sort > span {
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .065em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v26-group-sort select {
                width: 100%;
                min-height: 32px;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 8px;

                background: #fffdf8;

                padding:
                    0 7px;

                font-size: 9px;
                font-weight: 800;

                color: #6b1d14;

                outline: none;
            }

            /*
            |--------------------------------------------------------------------------
            | SEM RESULTADOS NA CATEGORIA
            |--------------------------------------------------------------------------
            */

            .inventory-v26-group-empty {
                display: flex;

                min-height: 92px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                gap: 3px;

                padding:
                    14px;

                text-align: center;

                color:
                    rgba(140,98,57,.56);
            }

            .inventory-v26-group-empty > span {
                font-family: Georgia, serif;
                font-size: 20px;
            }

            .inventory-v26-group-empty strong {
                font-family: Georgia, serif;
                font-size: 12px;

                color: #53150f;
            }

            .inventory-v26-group-empty button {
                margin-top: 4px;

                font-size: 8px;
                font-weight: 900;

                color: #8c6239;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V39 — CARDS MAIORES E MAIS LEGÍVEIS
            |--------------------------------------------------------------------------
            |
            | A V26 tinha compactado demais os cards do inventário.
            | Aqui voltamos a uma leitura confortável e com mais cara de módulo
            | da ficha: imagem grande, título forte, subtítulo legível e área
            | de habilidades menos apertada.
            |
            */


            /* ==============================================================
               GRID
            ============================================================== */

            .inventory-v39
            .inventory-v39-items-grid {
                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(220px, 1fr)
                    ) !important;

                gap:
                    14px !important;

                padding:
                    12px !important;
            }

            @media (max-width: 760px) {
                .inventory-v39
                .inventory-v39-items-grid {
                    grid-template-columns:
                        repeat(
                            auto-fill,
                            minmax(200px, 1fr)
                        ) !important;
                }
            }

            @media (max-width: 540px) {
                .inventory-v39
                .inventory-v39-items-grid {
                    grid-template-columns:
                        1fr !important;
                }
            }


            /* ==============================================================
               CARD BASE
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card {
                display:
                    flex;

                min-height:
                    338px;

                flex-direction:
                    column;

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    18px;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    0 4px 12px rgba(0,0,0,.04) !important;

                transition:
                    transform .16s ease,
                    border-color .16s ease,
                    box-shadow .16s ease,
                    background .16s ease;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card::before {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-border-strong) 85%,
                        white
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card.magical::before {
                background:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card:hover,
            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card:focus-within {
                transform:
                    translateY(-2px) !important;

                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 10px 24px rgba(0,0,0,.07) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-main {
                display:
                    flex !important;

                min-height:
                    0;

                flex:
                    1;

                flex-direction:
                    column;

                gap:
                    12px;

                padding:
                    12px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-thumb {
                width:
                    100% !important;

                height:
                    152px !important;

                border:
                    1px solid
                    var(--theme-border-soft) !important;

                border-radius:
                    14px !important;

                background:
                    linear-gradient(
                        180deg,
                        var(--theme-surface-inset) 0%,
                        color-mix(
                            in srgb,
                            var(--theme-surface-inset) 88%,
                            var(--theme-band-background)
                        ) 100%
                    ) !important;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.45);
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-thumb
            img {
                width:
                    100%;

                height:
                    100%;

                object-fit:
                    contain;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-thumb
            .inventory-v27-equipped-badge {
                top:
                    10px !important;

                right:
                    10px !important;

                min-height:
                    24px;

                padding:
                    0 9px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 16%,
                        var(--theme-border)
                    ) !important;

                border-radius:
                    999px;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-surface-raised) 85%,
                        white
                    ) !important;

                font-size:
                    8px;

                letter-spacing:
                    .08em;

                text-transform:
                    uppercase;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-copy {
                display:
                    flex;

                flex:
                    1;

                flex-direction:
                    column;

                gap:
                    7px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-copy
            h4 {
                overflow:
                    hidden;

                display:
                    -webkit-box;

                min-height:
                    44px;

                -webkit-line-clamp:
                    2;

                -webkit-box-orient:
                    vertical;

                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    17px !important;

                font-weight:
                    900;

                line-height:
                    1.18 !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-copy
            em {
                overflow:
                    hidden;

                display:
                    -webkit-box;

                min-height:
                    30px;

                -webkit-line-clamp:
                    2;

                -webkit-box-orient:
                    vertical;

                margin-top:
                    0 !important;

                font-size:
                    12px !important;

                font-weight:
                    600;

                line-height:
                    1.25 !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-main
            > .hidden.shrink-0.self-center.text-\[\#8c6239\]\/35 {
                display:
                    none !important;
            }


            /* ==============================================================
               FEATURE STRIP
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-feature-strip {
                min-height:
                    106px;

                margin-top:
                    auto;

                border-top:
                    1px solid
                    var(--theme-divider) !important;

                background:
                    var(--theme-surface-card-alt) !important;

                padding:
                    9px 10px 10px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-head {
                min-height:
                    22px;

                margin-bottom:
                    7px;

                font-size:
                    8.5px !important;

                letter-spacing:
                    .12em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-count {
                min-width:
                    20px;

                height:
                    20px;

                font-size:
                    8px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-list {
                gap:
                    6px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row {
                min-height:
                    29px;

                gap:
                    8px;

                border:
                    1px solid
                    var(--theme-border-soft) !important;

                border-radius:
                    10px;

                background:
                    var(--theme-surface-card) !important;

                padding:
                    4px 6px 4px 8px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-name {
                font-size:
                    11px !important;

                line-height:
                    1.15 !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-passive {
                min-height:
                    19px;

                padding:
                    0 7px;

                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                font-size:
                    7px !important;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width:
                    20px;

                height:
                    20px;

                font-size:
                    11px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width:
                    34px;

                font-size:
                    9px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-recovery {
                max-width:
                    none;

                font-size:
                    8px;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-more {
                min-height:
                    18px;

                margin-top:
                    2px;

                font-size:
                    8px !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-more
            span:last-child {
                color:
                    var(--theme-accent-strong) !important;
            }


            /* ==============================================================
               FALLBACK — SEM HABILIDADES
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card
            .inventory-v39-feature-strip:empty {
                display:
                    none;
            }

            /*
            |--------------------------------------------------------------------------
            | POLAROIDS MAIS COMPACTOS
            |--------------------------------------------------------------------------
            |
            | Mantém a mesma aparência, apenas reduz densidade e redundância.
            |
            */

            .inventory-v26
            .inventory-v26-items-grid {
                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(168px, 1fr)
                    ) !important;

                gap: 9px !important;

                padding:
                    9px !important;
            }

            .inventory-v26
            .inventory-v16-polaroid {
                min-height: 0;
            }

            /*
            | Foto menor: era 132px.
            */
            .inventory-v26
            .inventory-v16-polaroid
            > div:first-child {
                padding:
                    7px 7px 6px 10px !important;
            }

            .inventory-v26
            .inventory-v16-polaroid
            > div:first-child
            > .inventory-v10-thumb {
                height: 104px !important;

                border-radius: 7px !important;
            }

            .inventory-v26
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                margin-top: 7px;
            }

            /*
            | Título continua claramente legível.
            */
            .inventory-v26
            .inventory-v16-polaroid
            h4 {
                font-size: 14px !important;
                line-height: 1.12 !important;
            }

            /*
            | A categoria já diz "Itens Maravilhosos/Mundanos".
            | No card mostramos apenas raridade + sintonizável.
            */
            .inventory-v26
            .inventory-v16-polaroid
            em {
                margin-top: 2px;

                font-size: 9px !important;
                line-height: 1.25 !important;

                -webkit-line-clamp: 1;
            }

            /*
            | Mecânica continua útil, mas compacta.
            */
            .inventory-v26
            .inventory-v23-card-mechanics {
                margin-top: 5px !important;

                font-size: 9.5px !important;
                line-height: 1.3 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | HABILIDADES MAIS BAIXAS
            |--------------------------------------------------------------------------
            */

            .inventory-v26
            .inventory-v22-card-feature-strip {
                min-height: 68px;

                padding:
                    5px 6px 6px 9px !important;
            }

            .inventory-v26
            .inventory-v22-card-feature-head {
                min-height: 14px;

                margin-bottom: 3px;

                font-size: 6.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-count {
                min-width: 16px;
                height: 16px;

                font-size: 6.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-list {
                gap: 3px;
            }

            .inventory-v26
            .inventory-v22-card-feature-row {
                min-height: 23px;

                gap: 4px;

                padding:
                    1px 3px 1px 6px;
            }

            .inventory-v26
            .inventory-v22-card-feature-name {
                font-size: 8.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-passive {
                min-height: 17px;

                padding:
                    0 5px;

                font-size: 6px;
            }

            .inventory-v26
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width: 17px;
                height: 17px;

                font-size: 9px;
            }

            .inventory-v26
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width: 27px;

                font-size: 8px;
            }

            .inventory-v26
            .inventory-v22-card-feature-recovery {
                max-width: 24px;

                font-size: 6.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-empty {
                min-height: 36px;

                font-size: 7px;
            }

            .inventory-v26
            .inventory-v22-card-feature-more {
                min-height: 15px;

                font-size: 6.5px;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 660px) {
                .inventory-v26-group-tools {
                    grid-template-columns: 1fr;
                }

                .inventory-v26-group-sort {
                    grid-template-columns:
                        50px
                        minmax(0, 1fr);
                }
            }

            @media (max-width: 520px) {
                .inventory-v26
                .inventory-v26-items-grid {
                    grid-template-columns:
                        repeat(
                            2,
                            minmax(0, 1fr)
                        ) !important;
                }

                .inventory-v26
                .inventory-v16-polaroid
                > div:first-child
                > .inventory-v10-thumb {
                    height: 112px !important;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V27 — CARD SEM FAIXA + SELO EQUIPADO NA FOTO
            |--------------------------------------------------------------------------
            */

            .inventory-v27 .inventory-v11-item-card::before,
            .inventory-v27 .inventory-v16-polaroid::before {
                content: none !important;
                display: none !important;
            }

            .inventory-v27 .inventory-v16-polaroid > div:first-child {
                padding-left: 9px !important;
            }

            .inventory-v27 .inventory-v27-thumb {
                position: relative;
                overflow: hidden;
            }

            .inventory-v27 .inventory-v27-equipped-badge {
                position: absolute;
                top: 7px;
                right: 7px;
                z-index: 2;

                display: inline-flex;
                align-items: center;
                gap: 4px;

                min-height: 18px;
                padding: 0 7px;

                border: 1px solid rgba(205,187,159,.72);
                border-radius: 999px;

                background: rgba(255,248,242,.92);
                box-shadow: 0 1px 4px rgba(83,21,15,.08);

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .055em;
                text-transform: uppercase;
                color: #6b1d14;
                backdrop-filter: blur(2px);
            }

            .inventory-v27 .inventory-v27-equipped-badge::before {
                content: '';
                width: 5px;
                height: 5px;
                border-radius: 999px;
                background: #6b1d14;
                opacity: .9;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V28 — CONTRASTE DOS CARDS + SCROLLBAR OCULTA
            |--------------------------------------------------------------------------
            */

            .inventory-v28 .inventory-v23-group-panel {
                border-color: rgba(176,140,98,.46);
                background: linear-gradient(180deg, #f4ebde 0%, #efe3d2 100%);
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.78),
                    0 1px 0 rgba(140,98,57,.04);
            }

            .inventory-v28 .inventory-v23 .inventory-v14-group-head,
            .inventory-v28 .inventory-v14-group-head {
                border-bottom-color: rgba(176,140,98,.32);
                background: linear-gradient(180deg, #f2e4d3 0%, #ebdac5 100%);
            }

            .inventory-v28 .inventory-v26-group-tools {
                background: rgba(255,252,247,.48);
                border-bottom: 1px solid rgba(188,154,111,.22);
            }

            .inventory-v28 .inventory-v26-group-search-input,
            .inventory-v28 .inventory-v26-group-sort select {
                background: rgba(255,255,255,.90);
                border-color: rgba(188,154,111,.52);
                color: #5c2018;
                box-shadow: 0 1px 0 rgba(255,255,255,.75) inset;
            }

            .inventory-v28 .inventory-v26-group-search-input::placeholder {
                color: rgba(125,96,77,.78);
            }

            .inventory-v28 .inventory-v16-polaroid {
                border: 1px solid rgba(176,140,98,.58) !important;
                background: linear-gradient(180deg, #fffefa 0%, #fcf8f1 100%) !important;
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.94),
                    0 8px 18px rgba(83,21,15,.035);
            }

            .inventory-v28 .inventory-v16-polaroid:hover,
            .inventory-v28 .inventory-v16-polaroid:focus-within {
                border-color: rgba(140,98,57,.62) !important;
                background: linear-gradient(180deg, #fffefd 0%, #fdf9f2 100%) !important;
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.96),
                    0 10px 24px rgba(83,21,15,.06);
            }

            .inventory-v28 .inventory-v10-thumb,
            .inventory-v28 .inventory-v27-thumb {
                background: linear-gradient(180deg, #fffdfa 0%, #f5ede1 100%) !important;
                border-color: rgba(188,154,111,.72) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.92);
            }

            .inventory-v28 .inventory-v23-card-mechanics {
                color: #8a6542 !important;
            }

            .inventory-v28 .inventory-v22-card-feature-strip {
                border-top: 1px solid rgba(188,154,111,.32);
                background: linear-gradient(180deg, #f5eee3 0%, #f0e4d4 100%) !important;
            }

            .inventory-v28 .inventory-v22-card-feature-row {
                background: rgba(255,251,246,.94);
                border-color: rgba(188,154,111,.34);
            }

            .inventory-v28 .inventory-v22-card-feature-empty,
            .inventory-v28 .inventory-v22-card-feature-more {
                color: #8a6542;
            }

            .inventory-v28 .inventory-v22-card-feature-passive {
                background: rgba(250,242,234,.95);
                border-color: rgba(205,187,159,.72);
            }

            .inventory-v28 .inventory-v22-card-feature-recovery {
                color: #8a6542;
            }

            .inventory-v28 .inventory-v27-equipped-badge {
                border-color: rgba(188,154,111,.72);
                background: rgba(255,250,244,.95);
                color: #6b1d14;
                box-shadow: 0 1px 6px rgba(83,21,15,.08);
            }

            .character-drawer > .min-h-0.flex-1.overflow-y-auto {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .character-drawer > .min-h-0.flex-1.overflow-y-auto::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v28 .overflow-y-auto,
            .inventory-v28 .inventory-v17-detail-scroll,
            .inventory-v28 .inventory-v16-equipped-body {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v28 .overflow-y-auto::-webkit-scrollbar,
            .inventory-v28 .inventory-v17-detail-scroll::-webkit-scrollbar,
            .inventory-v28 .inventory-v16-equipped-body::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V29 — ITENS EQUIPADOS COMO LISTA SIMPLES
            |--------------------------------------------------------------------------
            */

            .inventory-v29-equipped-modal {
                position: relative;
                z-index: 10;

                width: min(520px, 94vw);
                max-height: min(72vh, 620px);

                overflow: hidden;

                border: 1px solid rgba(176,140,98,.58);
                border-radius: 14px;

                background:
                    linear-gradient(
                        180deg,
                        #fbf8f1 0%,
                        #f7f0e6 100%
                    );

                box-shadow:
                    0 28px 80px rgba(43,29,23,.24),
                    inset 0 1px 0 rgba(255,255,255,.86);
            }

            .inventory-v29-equipped-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;

                border-bottom: 1px solid rgba(188,154,111,.30);

                background:
                    linear-gradient(
                        180deg,
                        #f2e6d7 0%,
                        #eadbc8 100%
                    );

                padding: 13px 15px 12px;
            }

            .inventory-v29-equipped-kicker {
                display: block;

                margin-bottom: 3px;

                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v29-equipped-title-row {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .inventory-v29-equipped-title-row h3 {
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
                line-height: 1.05;

                color: #53150f;
            }

            .inventory-v29-equipped-count {
                display: inline-flex;
                min-width: 24px;
                height: 24px;
                align-items: center;
                justify-content: center;

                border: 1px solid rgba(176,140,98,.32);
                border-radius: 999px;

                background: rgba(255,253,248,.78);

                padding: 0 6px;

                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;

                color: #6b1d14;
            }

            .inventory-v29-equipped-hint {
                flex: 0 0 auto;
                margin-top: 5px;

                font-size: 7.5px;
                font-weight: 700;

                color: rgba(140,98,57,.66);
            }

            .inventory-v29-equipped-list-wrap {
                max-height: calc(min(72vh, 620px) - 66px);
                overflow-y: auto;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v29-equipped-list-wrap::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v29-equipped-list {
                display: flex;
                flex-direction: column;

                padding: 7px;
            }

            .inventory-v29-equipped-row {
                display: grid;
                grid-template-columns: 48px minmax(0, 1fr) 24px;
                align-items: center;
                gap: 10px;

                width: 100%;
                min-height: 60px;

                border: 1px solid transparent;
                border-radius: 9px;

                padding: 6px 7px;

                text-align: left;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    transform .12s ease;
            }

            .inventory-v29-equipped-row + .inventory-v29-equipped-row {
                margin-top: 2px;
            }

            .inventory-v29-equipped-row:hover,
            .inventory-v29-equipped-row:focus-visible {
                border-color: rgba(176,140,98,.34);
                background: #fffdf8;
                outline: none;
            }

            .inventory-v29-equipped-thumb {
                display: flex;
                width: 48px;
                height: 48px;
                align-items: center;
                justify-content: center;

                overflow: hidden;

                border: 1px solid rgba(188,154,111,.52);
                border-radius: 8px;

                background: #f3ece1;
            }

            .inventory-v29-equipped-thumb img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                padding: 2px;
            }

            .inventory-v29-equipped-thumb > span {
                font-family: Georgia, serif;
                font-size: 17px;

                color: rgba(140,98,57,.30);
            }

            .inventory-v29-equipped-copy {
                min-width: 0;
            }

            .inventory-v29-equipped-copy strong {
                display: block;
                overflow: hidden;

                font-family: Georgia, serif;
                font-size: 13.5px;
                font-weight: 900;
                line-height: 1.15;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v29-equipped-copy span {
                display: block;
                overflow: hidden;

                margin-top: 3px;

                font-size: 9px;
                font-style: italic;
                font-weight: 600;

                color: #7d604d;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v29-equipped-chevron {
                display: flex;
                width: 24px;
                height: 24px;
                align-items: center;
                justify-content: center;

                color: rgba(140,98,57,.48);

                transition: color .12s ease, transform .12s ease;
            }

            .inventory-v29-equipped-chevron svg {
                width: 14px;
                height: 14px;
            }

            .inventory-v29-equipped-row:hover
            .inventory-v29-equipped-chevron {
                color: #6b1d14;
                transform: translateX(1px);
            }

            .inventory-v29-equipped-empty {
                display: flex;
                min-height: 160px;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                padding: 24px;
                text-align: center;
            }

            .inventory-v29-equipped-empty > span {
                margin-bottom: 4px;

                font-family: Georgia, serif;
                font-size: 24px;

                color: rgba(140,98,57,.36);
            }

            .inventory-v29-equipped-empty strong {
                font-family: Georgia, serif;
                font-size: 15px;

                color: #53150f;
            }

            .inventory-v29-equipped-empty p {
                margin-top: 3px;

                font-size: 9.5px;

                color: #8c6239;
            }

            @media (max-width: 520px) {
                .inventory-v29-equipped-header {
                    padding: 12px;
                }

                .inventory-v29-equipped-hint {
                    display: none;
                }

                .inventory-v29-equipped-row {
                    grid-template-columns: 44px minmax(0,1fr) 20px;
                    min-height: 56px;
                }

                .inventory-v29-equipped-thumb {
                    width: 44px;
                    height: 44px;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V30 — CARDS MAIS LIMPOS + TRACKERS MENORES
            |--------------------------------------------------------------------------
            |
            | A imagem já comunica o tipo do item. Portanto, os polaroids não
            | repetem mais "Arma Marcial", "Escudo", "Armadura" etc.
            |
            | Os rastreadores continuam funcionais, mas ocupam menos espaço.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | CORPO DO POLAROID
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                margin-top: 6px;
            }

            .inventory-v30
            .inventory-v16-polaroid
            h4 {
                margin-bottom: 1px;
            }

            .inventory-v30
            .inventory-v16-polaroid
            em {
                margin-top: 1px !important;
            }

            /*
            |--------------------------------------------------------------------------
            | ÁREA DE HABILIDADES
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v22-card-feature-strip {
                min-height: 58px;

                padding:
                    4px 5px 5px 7px !important;
            }

            .inventory-v30
            .inventory-v22-card-feature-head {
                min-height: 12px;

                margin-bottom: 2px;

                font-size: 6px;
            }

            .inventory-v30
            .inventory-v22-card-feature-count {
                min-width: 15px;
                height: 15px;

                padding:
                    0 4px;

                font-size: 6px;
            }

            .inventory-v30
            .inventory-v22-card-feature-list {
                gap: 2px;
            }

            .inventory-v30
            .inventory-v22-card-feature-row {
                min-height: 20px;

                gap: 3px;

                border-radius: 6px;

                padding:
                    1px 2px 1px 5px;
            }

            .inventory-v30
            .inventory-v22-card-feature-name {
                font-size: 8px;
                line-height: 1.1;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKER
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v22-card-feature-tracker {
                gap: 2px;
            }

            .inventory-v30
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker {
                border-radius: 6px;
            }

            .inventory-v30
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width: 15px;
                height: 15px;

                font-size: 8px;
                line-height: 1;
            }

            .inventory-v30
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width: 23px;

                padding:
                    0 2px;

                font-size: 7.5px;
                line-height: 1;
            }

            .inventory-v30
            .inventory-v22-card-feature-recovery {
                max-width: 22px;

                font-size: 6px;
                line-height: 1;

                color: #8a6542;
            }

            /*
            |--------------------------------------------------------------------------
            | PASSIVAS
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v22-card-feature-passive {
                min-height: 15px;

                padding:
                    0 4px;

                font-size: 5.5px;
                letter-spacing: .035em;
            }

            .inventory-v30
            .inventory-v22-card-feature-empty {
                min-height: 30px;

                font-size: 6.5px;
            }

            .inventory-v30
            .inventory-v22-card-feature-more {
                min-height: 13px;

                font-size: 6px;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 520px) {
                .inventory-v30
                .inventory-v22-card-feature-name {
                    font-size: 8.5px;
                }

                .inventory-v30
                .inventory-v22-card-feature-row
                .inventory-v11-mini-tracker button {
                    width: 16px;
                    height: 16px;
                }

                .inventory-v30
                .inventory-v22-card-feature-row
                .inventory-v11-mini-tracker strong {
                    min-width: 24px;

                    font-size: 8px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V31 — CRIAÇÃO / TOOLTIPS / TRACKERS
            |--------------------------------------------------------------------------
            */

            /*
            | O módulo Arma não pode cortar tooltips das propriedades.
            */
            .inventory-v31
            .inventory-v31-weapon-module {
                overflow: visible !important;
            }

            .inventory-v31
            .inventory-v31-weapon-module
            .inventory-v10-tooltip-panel {
                z-index: 180;
            }

            /*
            | Escolha do comportamento das cargas.
            */
            .inventory-v31-counter-choice {
                display: flex;

                min-height: 52px;

                flex-direction: column;

                align-items: flex-start;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.72);

                border-radius: 9px;

                background:
                    rgba(255,253,249,.72);

                padding:
                    7px 9px;

                text-align: left;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            .inventory-v31-counter-choice strong {
                font-size: 10px;

                color: #53150f;
            }

            .inventory-v31-counter-choice span {
                margin-top: 2px;

                font-size: 8.5px;
                line-height: 1.3;

                color: #8c6239;
            }

            .inventory-v31-counter-choice:hover {
                background: #f7f0e6;
            }

            .inventory-v31-counter-choice.active {
                border-color:
                    rgba(107,29,20,.42);

                background:
                    rgba(107,29,20,.065);
            }

            .inventory-v31-counter-choice.active strong {
                color: #6b1d14;
            }

            @media (max-width: 520px) {
                .inventory-v31-counter-choice {
                    min-height: 48px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V32 — TOOLTIP FLUTUANTE + SCROLL SEM BARRAS
            |--------------------------------------------------------------------------
            */

            /*
            | Continua rolando com mouse, trackpad, teclado e touch.
            | Apenas a barra visual é escondida.
            */
            .inventory-v32-editor-scroll {
                overflow-x: hidden !important;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v32-editor-scroll::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v32-editor-scroll > *,
            .inventory-v32-editor-scroll section,
            .inventory-v32-editor-scroll .inventory-v10-module {
                max-width: 100%;
                min-width: 0;
            }

            /*
            | Tooltip fora do fluxo do editor.
            | Não pode mais criar scrollbar horizontal nem ser cortado.
            */
            .inventory-v32-floating-tooltip {
                position: fixed;

                z-index: 9999;

                width:
                    min(
                        390px,
                        calc(100vw - 24px)
                    );

                max-height:
                    calc(100vh - 24px);

                overflow-y: auto;
                overflow-x: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.72);

                border-radius: 10px;

                background: #fbf8f1;

                padding:
                    10px 11px;

                color: #5f3a27;

                box-shadow:
                    0 14px 32px
                    rgba(43,29,23,.18);

                pointer-events: none;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v32-floating-tooltip::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v32-floating-tooltip strong {
                display: block;

                margin-bottom: 4px;

                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v32-floating-tooltip p {
                margin: 0;

                font-size: 11px;
                font-weight: 600;
                line-height: 1.52;

                color: #6b5548;
            }

            /*
            | O módulo pode voltar a controlar o próprio acabamento.
            | O tooltip não está mais dentro dele.
            */
            .inventory-v32
            .inventory-v31-weapon-module {
                overflow: hidden !important;
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V34 — CRIADOR DE ARMADURAS E ESCUDOS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-defense-module {
                border-color:
                    rgba(176,140,98,.46) !important;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf9 0%,
                        #f8f2e8 100%
                    ) !important;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.88);
            }

            .inventory-v34-defense-head {
                display: flex;

                align-items: flex-start;

                gap: 11px;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.30);

                background:
                    linear-gradient(
                        180deg,
                        #f2e5d5 0%,
                        #ead9c5 100%
                    );

                padding:
                    12px 14px;
            }

            .inventory-v34-defense-head-icon {
                display: flex;

                width: 36px;
                height: 36px;

                flex: 0 0 36px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.48);

                border-radius: 10px;

                background:
                    rgba(255,253,249,.80);

                color: #6b1d14;
            }

            .inventory-v34-defense-head-icon svg {
                width: 19px;
                height: 19px;
            }

            .inventory-v34-auto-equip {
                display: inline-flex;

                min-height: 20px;

                align-items: center;

                border:
                    1px solid
                    rgba(107,29,20,.15);

                border-radius: 999px;

                background:
                    rgba(107,29,20,.055);

                padding:
                    0 7px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .06em;

                text-transform: uppercase;

                color: #6b1d14;
            }

            .inventory-v34-remove {
                flex: 0 0 auto;

                border-radius: 7px;

                padding:
                    5px 7px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .04em;

                text-transform: uppercase;

                color: #a03d32;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .inventory-v34-remove:hover {
                background:
                    rgba(160,61,50,.07);

                color: #7c211a;
            }

            /*
            |--------------------------------------------------------------------------
            | SEÇÕES
            |--------------------------------------------------------------------------
            */

            .inventory-v34-section-label {
                display: flex;

                align-items: flex-start;

                gap: 9px;
            }

            .inventory-v34-section-label > span {
                display: flex;

                width: 26px;
                height: 26px;

                flex: 0 0 26px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 8px;

                background: #fffdf8;

                font-family: Georgia, serif;
                font-size: 9px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-section-label strong {
                display: block;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.2;

                color: #53150f;
            }

            .inventory-v34-section-label p {
                margin-top: 2px;

                font-size: 9.5px;
                line-height: 1.4;

                color: #8c6239;
            }

            .inventory-v34-rule-panel {
                border-top:
                    1px solid
                    rgba(188,154,111,.24);

                padding-top: 15px;
            }

            .inventory-v34-field-label {
                display: block;

                font-size: 9px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | CATEGORIAS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-armor-category {
                display: flex;

                min-height: 82px;

                flex-direction: column;

                align-items: flex-start;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.68);

                border-radius: 10px;

                background: #fffdf8;

                padding:
                    9px 10px;

                text-align: left;

                transition:
                    border-color .14s ease,
                    background .14s ease,
                    box-shadow .14s ease;
            }

            .inventory-v34-armor-category:hover {
                border-color:
                    rgba(140,98,57,.50);

                background: #f8f2e8;
            }

            .inventory-v34-armor-category.active {
                border-color:
                    rgba(107,29,20,.42);

                background:
                    linear-gradient(
                        180deg,
                        rgba(107,29,20,.075),
                        rgba(107,29,20,.035)
                    );

                box-shadow:
                    inset 0 0 0 1px
                    rgba(107,29,20,.045);
            }

            .inventory-v34-category-mark {
                display: flex;

                width: 23px;
                height: 23px;

                align-items: center;
                justify-content: center;

                margin-bottom: 5px;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 7px;

                background: #f7f0e6;

                font-family: Georgia, serif;
                font-size: 9px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-armor-category.active
            .inventory-v34-category-mark {
                border-color:
                    rgba(107,29,20,.26);

                background:
                    rgba(107,29,20,.08);

                color: #6b1d14;
            }

            .inventory-v34-armor-category strong {
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v34-armor-category small {
                margin-top: 2px;

                font-size: 8px;
                font-weight: 700;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | CAMPOS NUMÉRICOS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-number-field {
                display: flex;

                min-height: 44px;

                align-items: center;

                border:
                    1px solid
                    rgba(205,187,159,.86);

                border-radius: 10px;

                background:
                    rgba(255,255,255,.82);

                overflow: hidden;
            }

            .inventory-v34-number-field > span {
                padding-left: 10px;

                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-number-field input {
                width: 100%;
                min-width: 0;
                height: 42px;

                border: 0;

                background: transparent;

                padding:
                    0 8px;

                text-align: center;

                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;

                color: #53150f;

                outline: none;
            }

            .inventory-v34-number-field:focus-within {
                border-color: #8c6239;

                box-shadow:
                    0 0 0 3px
                    rgba(140,98,57,.08);
            }

            /*
            |--------------------------------------------------------------------------
            | REGRAS DE DESTREZA
            |--------------------------------------------------------------------------
            */

            .inventory-v34-rule-choice {
                display: flex;

                min-height: 62px;

                flex-direction: column;

                align-items: flex-start;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.66);

                border-radius: 9px;

                background: #fffdf8;

                padding:
                    8px 9px;

                text-align: left;

                transition:
                    border-color .12s ease,
                    background .12s ease;
            }

            .inventory-v34-rule-choice strong {
                font-size: 9.5px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v34-rule-choice span {
                margin-top: 2px;

                font-size: 7.5px;
                line-height: 1.25;

                color: #8c6239;
            }

            .inventory-v34-rule-choice.active {
                border-color:
                    rgba(107,29,20,.42);

                background:
                    rgba(107,29,20,.055);
            }

            .inventory-v34-cap-row {
                display: flex;

                flex-wrap: wrap;

                align-items: center;

                gap: 6px;

                margin-top: 10px;

                border:
                    1px solid
                    rgba(205,187,159,.50);

                border-radius: 9px;

                background:
                    rgba(247,240,230,.66);

                padding:
                    7px 8px;
            }

            .inventory-v34-cap-button {
                display: flex;

                min-width: 42px;
                height: 30px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.74);

                border-radius: 7px;

                background: #fffdf8;

                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-cap-button.active {
                border-color: #6b1d14;

                background: #6b1d14;

                color: #faf8f2;
            }

            .inventory-v34-cap-custom {
                display: flex;

                height: 30px;

                align-items: center;

                gap: 5px;

                border:
                    1px solid
                    rgba(205,187,159,.74);

                border-radius: 7px;

                background: #fffdf8;

                padding:
                    0 7px;
            }

            .inventory-v34-cap-custom span {
                font-size: 7px;
                font-weight: 900;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v34-cap-custom input {
                width: 42px;

                border: 0;

                background: transparent;

                text-align: center;

                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #53150f;

                outline: 0;
            }

            /*
            |--------------------------------------------------------------------------
            | ATRIBUTOS EXTRAS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-ability {
                display: flex;

                min-width: 0;
                min-height: 48px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.64);

                border-radius: 9px;

                background: #fffdf8;

                padding:
                    5px 3px;

                transition:
                    border-color .12s ease,
                    background .12s ease;
            }

            .inventory-v34-ability strong {
                font-family: Georgia, serif;
                font-size: 10px;

                color: #53150f;
            }

            .inventory-v34-ability span {
                max-width: 100%;

                margin-top: 1px;

                overflow: hidden;

                font-size: 6.5px;

                color: #8c6239;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v34-ability.active {
                border-color:
                    rgba(107,29,20,.40);

                background:
                    rgba(107,29,20,.06);
            }

            /*
            |--------------------------------------------------------------------------
            | FÓRMULA / RESUMOS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-formula-card {
                display: flex;

                align-items: center;

                gap: 10px;

                border:
                    1px solid
                    rgba(107,29,20,.20);

                border-radius: 11px;

                background:
                    linear-gradient(
                        180deg,
                        rgba(107,29,20,.055),
                        rgba(107,29,20,.025)
                    );

                padding:
                    10px 11px;
            }

            .inventory-v34-formula-icon {
                display: flex;

                width: 34px;
                height: 34px;

                flex: 0 0 34px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(107,29,20,.18);

                border-radius: 9px;

                background:
                    rgba(255,253,249,.76);

                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: 900;

                color: #6b1d14;
            }

            .inventory-v34-formula-card span {
                display: block;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .07em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v34-formula-card strong {
                display: block;

                margin-top: 2px;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.3;

                color: #53150f;
            }

            .inventory-v34-formula-card small {
                display: block;

                margin-top: 2px;

                font-size: 8px;
                line-height: 1.35;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | ESCUDO — SOMA VISUAL
            |--------------------------------------------------------------------------
            */

            .inventory-v34-shield-total {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    auto
                    minmax(0, 1fr)
                    auto
                    minmax(0, 1.15fr);

                align-items: stretch;

                gap: 7px;
            }

            .inventory-v34-shield-total > div {
                display: flex;

                min-height: 58px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.62);

                border-radius: 9px;

                background: #fffdf8;
            }

            .inventory-v34-shield-total > div.is-total {
                border-color:
                    rgba(107,29,20,.28);

                background:
                    rgba(107,29,20,.055);
            }

            .inventory-v34-shield-total span {
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .06em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v34-shield-total strong {
                margin-top: 2px;

                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v34-shield-total > b {
                display: flex;

                align-items: center;
                justify-content: center;

                font-family: Georgia, serif;
                font-size: 15px;

                color:
                    rgba(140,98,57,.48);
            }

            .inventory-v34-equipment-note {
                display: flex;

                align-items: center;

                gap: 8px;

                border-top:
                    1px solid
                    rgba(188,154,111,.22);

                padding-top: 11px;

                font-size: 8.5px;
                line-height: 1.4;

                color: #8c6239;
            }

            .inventory-v34-equipment-note svg {
                width: 16px;
                height: 16px;

                flex: 0 0 16px;

                color: #6b1d14;
            }

            @media (max-width: 560px) {
                .inventory-v34-defense-head {
                    padding:
                        10px 11px;
                }

                .inventory-v34-shield-total {
                    grid-template-columns:
                        1fr
                        auto
                        1fr;

                    gap: 5px;
                }

                .inventory-v34-shield-total
                > div.is-total {
                    grid-column:
                        1 / -1;
                }

                .inventory-v34-shield-total
                > b:nth-of-type(2) {
                    display: none;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V35 — SISTEMA VISUAL UNIFICADO
            |--------------------------------------------------------------------------
            |
            | Direção:
            | - papel neutro;
            | - cor do tema apenas como acento;
            | - tipografia escura e legível;
            | - modais com o mesmo contrato global;
            | - menos caixas decorativas;
            | - hierarquia editorial;
            | - estado mecânico continua semântico.
            |
            */


            /* ==============================================================
               BASE
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35 {
                color:
                    var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#6b1d14]"] {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#92745c]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#9a795b]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#6b5548]"] {
                color:
                    var(--theme-text-secondary) !important;
            }


            /* ==============================================================
               TOOLBAR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v23-main-topbar {
                align-items:
                    center;

                min-height:
                    58px;

                border-bottom:
                    1px solid
                    var(--theme-divider) !important;

                padding:
                    0 0 14px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-primary {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;

                box-shadow:
                    0 2px 8px
                    color-mix(
                        in srgb,
                        var(--theme-accent-deep) 12%,
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-primary:hover {
                border-color:
                    var(--theme-accent-deep) !important;

                background:
                    var(--theme-button-hover-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-action,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-trigger {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-action:hover,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-trigger:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-badge {
                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               BOLSA
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-pouch {
                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-label,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-chevron {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-copy
            strong {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                box-shadow:
                    0 18px 50px rgba(0,0,0,.18),
                    var(
                        --theme-modal-control-shadow,
                        0 2px 7px rgba(0,0,0,.06)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel-head {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel-head
            strong,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-name
            b {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel-head
            span,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-name
            small {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-row {
                border-color:
                    var(--theme-divider) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-row
            input {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-row
            input:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 12%,
                        transparent
                    ) !important;
            }



            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V38 — TOOLBAR / BOLSA DE MOEDAS
            |--------------------------------------------------------------------------
            |
            | A área superior deixa de parecer uma faixa de botões isolados.
            | Ela passa a funcionar como uma barra de ações compacta, enquanto
            | a Bolsa ganha hierarquia própria e um editor de moedas mais claro.
            |
            */


            /* ==============================================================
               TOOLBAR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-toolbar {
                min-height:
                    68px;

                align-items:
                    center;

                gap:
                    14px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    16px;

                background:
                    linear-gradient(
                        180deg,
                        var(--theme-surface-card) 0%,
                        var(--theme-surface-card-alt) 100%
                    ) !important;

                padding:
                    10px 12px !important;

                box-shadow:
                    0 2px 10px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-actions {
                align-items:
                    center;

                gap:
                    8px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-primary-action {
                min-height:
                    42px;

                border-radius:
                    11px;

                padding:
                    0 15px;

                font-size:
                    11px;

                letter-spacing:
                    .01em;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-primary-action
            .inventory-v14-plus {
                font-size:
                    17px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-secondary-action {
                min-height:
                    42px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    11px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    0 13px;

                font-size:
                    11px;

                font-weight:
                    800;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    0 1px 4px rgba(0,0,0,.025);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-secondary-action:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-secondary-action
            .inventory-v14-badge {
                min-width:
                    24px;

                height:
                    24px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 12%,
                        var(--theme-border)
                    );

                background:
                    var(--theme-accent-soft) !important;

                font-size:
                    11px;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-toolbar-right {
                margin-left:
                    auto;
            }


            /* ==============================================================
               WALLET TRIGGER
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-wrap {
                position:
                    relative;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger {
                min-width:
                    180px;

                min-height:
                    46px;

                gap:
                    10px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    12px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    6px 10px 6px 8px;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 2px 7px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 4px 12px rgba(0,0,0,.05);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-pouch {
                width:
                    34px;

                height:
                    34px;

                flex-basis:
                    34px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 14%,
                        var(--theme-border)
                    );

                border-radius:
                    10px;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-wallet-copy {
                display:
                    flex;

                min-width:
                    0;

                flex:
                    1;

                flex-direction:
                    column;

                align-items:
                    flex-start;

                gap:
                    2px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-wallet-label {
                font-size:
                    8px;

                line-height:
                    1;

                letter-spacing:
                    .10em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-total-line {
                display:
                    flex;

                align-items:
                    baseline;

                gap:
                    5px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-total-line
            strong {
                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    15px;

                line-height:
                    1;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-total-line
            small {
                font-size:
                    9px;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-wallet-chevron {
                width:
                    16px;

                height:
                    16px;

                color:
                    var(--theme-label) !important;
            }


            /* ==============================================================
               WALLET PANEL
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel {
                right:
                    0;

                width:
                    min(
                        390px,
                        calc(100vw - 28px)
                    );

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    16px;

                background:
                    var(--theme-surface-raised) !important;

                box-shadow:
                    0 22px 60px rgba(0,0,0,.18),
                    0 2px 8px rgba(0,0,0,.04) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel-head {
                min-height:
                    72px;

                align-items:
                    flex-start;

                border-bottom:
                    1px solid
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;

                padding:
                    14px 15px 12px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-kicker {
                display:
                    block;

                margin-bottom:
                    3px;

                font-size:
                    8px !important;

                font-weight:
                    900;

                line-height:
                    1;

                letter-spacing:
                    .12em;

                text-transform:
                    uppercase;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel-head
            strong {
                display:
                    block;

                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    18px;

                line-height:
                    1.05;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel-head
            > div
            > span:last-child {
                display:
                    block;

                margin-top:
                    4px;

                font-size:
                    10px;

                line-height:
                    1.3;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-saving {
                min-height:
                    24px;

                display:
                    inline-flex;

                align-items:
                    center;

                border:
                    1px solid
                    var(--theme-border);

                border-radius:
                    999px;

                background:
                    var(--theme-surface-raised);

                padding:
                    0 8px;

                font-size:
                    8px;

                font-weight:
                    800;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary {
                display:
                    flex;

                align-items:
                    center;

                justify-content:
                    space-between;

                gap:
                    12px;

                margin:
                    12px 12px 0;

                border:
                    1px solid
                    var(--theme-border);

                border-radius:
                    12px;

                background:
                    var(--theme-surface-card-alt);

                padding:
                    10px 12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary
            > div {
                display:
                    flex;

                min-width:
                    0;

                flex-direction:
                    column;

                gap:
                    2px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary
            span {
                font-size:
                    9px;

                font-weight:
                    800;

                text-transform:
                    uppercase;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary
            strong {
                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    22px;

                line-height:
                    1;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary-note {
                text-transform:
                    none !important;

                letter-spacing:
                    0 !important;

                font-size:
                    10px !important;

                color:
                    var(--theme-text-secondary) !important;
            }


            /* ==============================================================
               COIN CARDS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coins {
                display:
                    grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap:
                    8px;

                padding:
                    10px 12px 12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card {
                display:
                    grid;

                grid-template-columns:
                    32px minmax(0, 1fr) 68px;

                align-items:
                    center;

                gap:
                    8px;

                min-height:
                    58px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    12px;

                background:
                    var(--theme-surface-card) !important;

                padding:
                    8px;

                transition:
                    border-color .16s ease,
                    background .16s ease,
                    box-shadow .16s ease;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 3px 9px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card--platinum {
                grid-column:
                    1 / -1;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-icon {
                width:
                    32px;

                height:
                    32px;

                border-radius:
                    10px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-name {
                display:
                    flex;

                min-width:
                    0;

                flex-direction:
                    column;

                gap:
                    2px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-name
            b {
                font-size:
                    10px;

                line-height:
                    1;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-name
            small {
                font-size:
                    9px;

                line-height:
                    1;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            input {
                width:
                    68px;

                height:
                    34px;

                justify-self:
                    end;

                border:
                    1px solid
                    var(--theme-input-border) !important;

                border-radius:
                    9px;

                background:
                    var(--theme-input-bg) !important;

                padding:
                    0 8px;

                text-align:
                    right;

                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    13px;

                font-weight:
                    800;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            input:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 12%,
                        transparent
                    ) !important;
            }


            /* ==============================================================
               RESPONSIVO
            ============================================================== */

            @media (max-width: 680px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-toolbar {
                    align-items:
                        stretch;

                    flex-direction:
                        column;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-toolbar-right {
                    width:
                        100%;

                    margin-left:
                        0;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-wallet-wrap,
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-wallet-trigger {
                    width:
                        100%;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-wallet-panel {
                    left:
                        0;

                    right:
                        auto;

                    width:
                        min(
                            390px,
                            calc(100vw - 36px)
                        );
                }
            }

            @media (max-width: 460px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-actions {
                    display:
                        grid;

                    grid-template-columns:
                        1fr 1fr;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-primary-action {
                    grid-column:
                        1 / -1;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-coins {
                    grid-template-columns:
                        1fr;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-coin-card--platinum {
                    grid-column:
                        auto;
                }
            }

            /* ==============================================================
               SINTONIZAÇÃO
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v23-attunement-panel {
                margin-top:
                    14px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    16px;

                background:
                    var(--theme-module-background) !important;

                padding:
                    15px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-attunement {
                border-bottom:
                    0 !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-section-head {
                margin-bottom:
                    10px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-diamond {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-title-line
            h3 {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-section-title
            p {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-count {
                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-text-action {
                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    0 2px 8px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-image {
                border-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-copy
            small,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-copy
            > span,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-empty {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-copy
            strong {
                color:
                    var(--theme-text-strong) !important;
            }



            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V36 — SINTONIZAÇÃO MAIS LIMPA
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-shell {
                margin-top:
                    14px;

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    18px;

                background:
                    linear-gradient(
                        180deg,
                        color-mix(
                            in srgb,
                            var(--theme-module-background) 94%,
                            white
                        ) 0%,
                        var(--theme-module-background) 100%
                    ) !important;

                padding:
                    12px !important;

                box-shadow:
                    0 2px 10px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-head {
                min-height:
                    auto;

                margin:
                    0 0 12px !important;

                border:
                    0 !important;

                border-radius:
                    14px;

                background:
                    var(--theme-band-background) !important;

                padding:
                    12px 14px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-title {
                gap:
                    12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-icon {
                width:
                    40px;

                height:
                    40px;

                flex-basis:
                    40px;

                border-radius:
                    12px;

                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 18%,
                        var(--theme-border)
                    ) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-accent-strong) !important;

                font-size:
                    14px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-copy {
                min-width:
                    0;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-line {
                gap:
                    10px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-line
            h3 {
                font-size:
                    18px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-count {
                min-height:
                    26px;

                padding:
                    0 10px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 14%,
                        var(--theme-border)
                    );

                background:
                    var(--theme-surface-card) !important;

                font-size:
                    12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-copy
            p {
                margin-top:
                    3px;

                font-size:
                    12px !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-action {
                display:
                    inline-flex;

                min-height:
                    36px;

                align-items:
                    center;

                justify-content:
                    center;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    10px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    0 12px !important;

                font-size:
                    10px;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    0 2px 6px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-action:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slots {
                gap:
                    10px;

                padding:
                    0 !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot {
                min-height:
                    108px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    14px;

                background:
                    var(--theme-surface-card) !important;

                padding:
                    10px !important;

                box-shadow:
                    0 2px 8px rgba(0,0,0,.03);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-filled {
                background:
                    linear-gradient(
                        180deg,
                        var(--theme-surface-card) 0%,
                        color-mix(
                            in srgb,
                            var(--theme-surface-card) 90%,
                            var(--theme-band-background)
                        ) 100%
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-empty {
                border-style:
                    dashed;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 6px 16px rgba(0,0,0,.05);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-content {
                display:
                    grid;

                grid-template-columns:
                    54px minmax(0, 1fr);

                align-items:
                    center;

                gap:
                    12px;

                min-height:
                    100%;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-image {
                width:
                    54px !important;

                height:
                    54px !important;

                flex-basis:
                    54px;

                border-radius:
                    11px;

                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-copy {
                display:
                    grid;

                grid-template-rows:
                    auto
                    minmax(34px, auto)
                    auto;

                min-height:
                    64px;

                align-content:
                    stretch;

                align-items:
                    start;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-meta {
                display:
                    flex;

                align-items:
                    center;

                justify-content:
                    space-between;

                gap:
                    8px;

                margin-bottom:
                    5px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-copy
            small {
                margin:
                    0 !important;

                font-size:
                    9px !important;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-rarity {
                display:
                    inline-flex;

                align-items:
                    center;

                min-height:
                    20px;

                max-width:
                    120px;

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    999px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    0 8px;

                font-size:
                    9px;

                font-weight:
                    800;

                color:
                    var(--theme-text-secondary) !important;

                text-overflow:
                    ellipsis;

                white-space:
                    nowrap;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-copy
            strong {
                overflow:
                    hidden;

                display:
                    -webkit-box;

                min-height:
                    32px;

                -webkit-line-clamp:
                    2;

                -webkit-box-orient:
                    vertical;

                font-size:
                    14px !important;

                line-height:
                    1.15;

                white-space:
                    normal !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-hint {
                align-self:
                    end;

                margin-top:
                    6px;

                font-size:
                    10px;

                line-height:
                    1.1;

                color:
                    var(--theme-text-secondary) !important;
            }


            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-filled
            .inventory-v36-slot-image {
                align-self:
                    center;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-filled
            .inventory-v36-slot-copy {
                height:
                    100%;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty {
                min-height:
                    70px;

                justify-content:
                    flex-start;

                gap:
                    10px;

                padding:
                    2px 2px 2px 1px;

                text-align:
                    left;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty
            .inventory-v14-slot-plus {
                display:
                    flex;

                width:
                    36px;

                height:
                    36px;

                flex:
                    0 0 36px;

                align-items:
                    center;

                justify-content:
                    center;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    11px;

                background:
                    var(--theme-surface-raised) !important;

                font-size:
                    18px;

                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty-copy {
                display:
                    flex;

                flex-direction:
                    column;

                gap:
                    3px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty-copy
            strong {
                font-size:
                    13px;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty-copy
            small {
                font-size:
                    10px;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned {
                align-items:
                    center;

                gap:
                    7px;

                margin-top:
                    10px;

                border-top:
                    1px solid
                    var(--theme-divider);

                padding:
                    10px 2px 0 !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned
            > span {
                margin-right:
                    2px;

                font-size:
                    10px;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned
            button {
                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    0 1px 4px rgba(0,0,0,.025);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned
            button:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            @media (max-width: 760px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-head {
                    align-items:
                        flex-start;

                    flex-direction:
                        column;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-action {
                    width:
                        100%;
                }
            }

            @media (max-width: 560px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-shell {
                    padding:
                        10px !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-head {
                    padding:
                        11px !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-slot-rarity {
                    max-width:
                        96px;
                }
            }

            /* ==============================================================
               GAVETAS / CATEGORIAS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v23-list {
                margin-top:
                    16px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-drawer {
                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    15px;

                background:
                    var(--theme-module-background) !important;

                box-shadow:
                    0 2px 10px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-drawer
            + .inventory-v24-group-drawer {
                margin-top:
                    10px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-trigger {
                min-height:
                    58px;

                margin:
                    0 !important;

                border:
                    0 !important;

                border-radius:
                    0 !important;

                padding:
                    11px 14px !important;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-trigger:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-copy
            h3 {
                font-size:
                    18px !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-copy
            p {
                font-size:
                    11px !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-group-count {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-chevron {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-content {
                border-top:
                    1px solid
                    var(--theme-divider);

                background:
                    var(--theme-surface-card-alt) !important;

                padding:
                    12px;
            }


            /* ==============================================================
               BUSCA / ORDENAÇÃO
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-sort
            select {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search:focus-within,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-sort
            select:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 11%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search-input {
                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search-icon,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search-clear,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-sort
            > span {
                color:
                    var(--theme-label) !important;
            }


            /* ==============================================================
               CARDS DE ITEM
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    0 2px 8px rgba(0,0,0,.04) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card::before {
                background:
                    var(--theme-border-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card.magical::before {
                background:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card:hover,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card:focus-within {
                transform:
                    translateY(-1px) !important;

                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 7px 20px rgba(0,0,0,.06) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v10-thumb {
                border-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v27-equipped-badge {
                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 25%,
                        var(--theme-border)
                    ) !important;

                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card
            h4 {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card
            em,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card
            p {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-strip {
                border-top-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-head,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-recovery,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-passive {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-name {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker
            button {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker
            button:hover {
                background:
                    var(--theme-card-hover) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker
            strong {
                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               MODAIS — CONTRATO GLOBAL
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-modal {
                border-color:
                    var(
                        --theme-modal-border,
                        var(--theme-border)
                    ) !important;

                background:
                    var(
                        --theme-modal-surface,
                        var(--theme-paper-strong)
                    ) !important;

                color:
                    var(
                        --theme-modal-text,
                        var(--theme-text-primary)
                    ) !important;

                box-shadow:
                    var(
                        --theme-modal-shadow,
                        0 28px 80px rgba(0,0,0,.30)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal {
                width:
                    min(
                        860px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal {
                width:
                    min(
                        960px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal,
            .character-sheet-theme[data-theme]
            .inventory-v35-found-modal {
                width:
                    min(
                        560px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-warning-modal {
                width:
                    min(
                        500px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            > div:first-child,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            > header:first-child {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            h3,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            h4,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            h5,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            strong {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#6b1d14]"] {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#6b5548]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#92745c]"] {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="border-[#cdbb9f]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="border-[#d8c7ab]"] {
                border-color:
                    var(--theme-border) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#efe9dc]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#f7f0e6]"] {
                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#faf8f2]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#fffdf8]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-white"] {
                background:
                    var(--theme-surface-card) !important;
            }


            /* ==============================================================
               DETALHE
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v17-detail-topbar {
                min-height:
                    54px;

                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;

                padding:
                    10px 14px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v17-detail-scroll {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 30%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v15-detail-equip,
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v18-attunement-toggle {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v15-detail-equip.is-stowed,
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v18-attunement-toggle.is-unattuned {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            button[class*="bg-[#6b1d14]"] {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            button[class*="bg-[#6b1d14]"]:hover {
                background:
                    var(--theme-button-hover-background) !important;
            }


            /* ==============================================================
               EDITOR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            > div:first-child {
                min-height:
                    66px;

                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v32-editor-scroll {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 30%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-input,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-select,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-textarea {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-input:focus,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-select:focus,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-textarea:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 12%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-choice,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-chip {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-choice:hover,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-chip:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-choice.active,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-chip.active {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-module,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v11-ability-card,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v11-detail-mechanics {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v11-ability-header,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v34-defense-head {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:nth-last-child(2) {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:nth-last-child(2):hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:last-child {
                border:
                    1px solid
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:last-child:hover {
                background:
                    var(--theme-button-hover-background) !important;
            }


            /* ==============================================================
               DELETE CONFIRM
            ============================================================== */

            .inventory-v35-delete-layer {
                background:
                    rgba(8,8,8,.60);

                -webkit-backdrop-filter:
                    blur(6px);

                backdrop-filter:
                    blur(6px);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-delete-modal {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-delete-modal
            > div:first-child {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }


            /* ==============================================================
               PICKER / WARNING / FOUND
            ============================================================== */

            .character-sheet-theme[data-theme]
            :is(
                .inventory-v35-picker-modal,
                .inventory-v35-warning-modal,
                .inventory-v35-found-modal
            )
            > div:first-child {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal
            > div:nth-child(2) {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal
            button[class*="border-b"] {
                border-color:
                    var(--theme-divider) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal
            button[class*="border-b"]:hover {
                background:
                    var(--theme-card-hover) !important;
            }


            /* ==============================================================
               EQUIPADOS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal {
                position:
                    relative;

                z-index:
                    10;

                width:
                    min(
                        620px,
                        calc(100vw - 28px)
                    ) !important;

                max-height:
                    min(
                        82dvh,
                        760px
                    );

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-modal-border, var(--theme-border)) !important;

                border-radius:
                    18px;

                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                box-shadow:
                    var(
                        --theme-modal-shadow,
                        0 28px 80px rgba(0,0,0,.30)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-header {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-kicker,
            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-hint {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-title-row
            h3 {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-count {
                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-list-wrap {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-row {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-row:hover {
                background:
                    var(--theme-card-hover) !important;
            }


            /* ==============================================================
               TOOLTIP
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-floating-tooltip {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 16px 42px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-floating-tooltip
            strong {
                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               TOAST
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-toast,
            .inventory-v35
            .inventory-v35-toast {
                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 18px 46px rgba(0,0,0,.20) !important;
            }


            /* ==============================================================
               RESPONSIVO
            ============================================================== */

            @media (max-width: 760px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v23-attunement-panel {
                    padding:
                        12px !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v24-group-content {
                    padding:
                        9px;
                }
            }

            @media (max-width: 560px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v23-main-topbar {
                    align-items:
                        stretch;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v14-toolbar-right {
                    justify-content:
                        stretch !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v14-wallet-wrap,
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v14-wallet-trigger {
                    width:
                        100%;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35-modal {
                    width:
                        calc(100vw - 18px) !important;

                    border-radius:
                        15px !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .inventory-v35
                *,
                .inventory-v35-modal-layer
                * {
                    scroll-behavior:
                        auto !important;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V40 — MIDNIGHT REFINADO
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-toolbar {
                border-color: rgba(151,162,173,.24) !important;
                background: linear-gradient(180deg,#22282e 0%,#1d2329 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025),0 3px 10px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-primary-action {
                border-color: #5c7891 !important;
                background: #4d6d88 !important;
                color: #f7fafc !important;
                box-shadow: 0 3px 10px rgba(0,0,0,.16) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-primary-action:hover {
                background: #5a7893 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-secondary-action,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-trigger {
                border-color: rgba(151,162,173,.25) !important;
                background: #252c33 !important;
                color: #e5e8eb !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-secondary-action:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-trigger:hover {
                border-color: rgba(154,170,185,.38) !important;
                background: #2d353d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-shell {
                border-color: rgba(151,162,173,.24) !important;
                background: linear-gradient(180deg,#20262c 0%,#1b2026 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025),0 3px 10px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-head {
                border: 1px solid rgba(151,162,173,.16) !important;
                background: linear-gradient(180deg,#2b3239 0%,#272e35 100%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-icon {
                border-color: rgba(111,140,166,.28) !important;
                background: #222a31 !important;
                color: #83a0b8 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-count,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-action {
                border-color: rgba(151,162,173,.22) !important;
                background: #252c33 !important;
                color: #e4e8eb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-action:hover {
                background: #2d353d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-slot {
                border-color: rgba(151,162,173,.24) !important;
                background: #20262c !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018),0 2px 7px rgba(0,0,0,.15) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-slot.is-filled {
                background: linear-gradient(180deg,#22292f 0%,#1d2329 100%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-slot:hover {
                border-color: rgba(126,151,173,.45) !important;
                background: #293138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-slot-image {
                border-color: rgba(151,162,173,.20) !important;
                background: #171c21 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-slot-rarity {
                border-color: rgba(151,162,173,.20) !important;
                background: #272e35 !important;
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-drawer {
                border-color: rgba(151,162,173,.24) !important;
                background: #1d2328 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.02),0 4px 12px rgba(0,0,0,.20) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-trigger {
                background: linear-gradient(180deg,#242b32 0%,#20272d 100%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-trigger:hover {
                background: #2a3239 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-content {
                border-color: rgba(151,162,173,.15) !important;
                background: #181e23 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v14-group-count {
                border-color: rgba(151,162,173,.22) !important;
                background: #2a3138 !important;
                color: #bbc2c8 !important;
            }

            /* Corrige a faixa clara herdada da V28. */
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-tools {
                border-bottom: 1px solid rgba(151,162,173,.14) !important;
                background: #252b31 !important;
                padding: 8px 10px !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search {
                border: 0 !important;
                background: transparent !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-input,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-sort select {
                border: 1px solid #46525d !important;
                background: #171c21 !important;
                color: #e9edf0 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-input::placeholder {
                color: #7f8992 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-icon,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-clear,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-sort > span {
                color: #929da6 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search:focus-within .inventory-v26-group-search-input,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-sort select:focus {
                border-color: #718ea8 !important;
                box-shadow: 0 0 0 3px rgba(111,140,166,.12) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-item-card {
                border-color: rgba(151,162,173,.24) !important;
                background: linear-gradient(180deg,#22282e 0%,#1e242a 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018),0 4px 12px rgba(0,0,0,.20) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-item-card:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-item-card:focus-within {
                border-color: rgba(126,151,173,.45) !important;
                background: linear-gradient(180deg,#293139 0%,#232a31 100%) !important;
                box-shadow: 0 10px 24px rgba(0,0,0,.28) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-card-thumb {
                border-color: rgba(151,162,173,.18) !important;
                background: radial-gradient(circle at 50% 42%,#232a31 0%,#171c21 72%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.022) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-card-copy h4 {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-card-copy em {
                color: #9ba5ae !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-feature-strip {
                border-top-color: rgba(151,162,173,.14) !important;
                background: #1a2025 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v22-card-feature-row {
                border-color: rgba(151,162,173,.16) !important;
                background: #22282e !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v22-card-feature-row:hover {
                background: #293138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v27-equipped-badge {
                border-color: rgba(151,162,173,.28) !important;
                background: rgba(36,44,51,.94) !important;
                color: #d9dde1 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v27-equipped-badge::before {
                background: #b85f5a !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-panel {
                border-color: rgba(151,162,173,.26) !important;
                background: #20262c !important;
                box-shadow: 0 24px 64px rgba(0,0,0,.34) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-panel-head {
                border-color: rgba(151,162,173,.15) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-summary {
                border-color: rgba(151,162,173,.20) !important;
                background: #1a2025 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-coin-card {
                border-color: rgba(151,162,173,.18) !important;
                background: #252b31 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-coin-card:hover {
                background: #2c343b !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-coin-card input {
                border-color: #46525d !important;
                background: #171c21 !important;
                color: #eef1f3 !important;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V41 — MIDNIGHT / MODAIS E THUMBNAILS
            |--------------------------------------------------------------------------
            |
            | Corrige os últimos blocos claros que ainda vinham das versões antigas:
            | detalhe do item, habilidades, trackers, thumbnails de Equipados e slots.
            | Esta camada atua SOMENTE no tema Midnight.
            |
            */

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal {
                border-color: rgba(151,162,173,.28) !important;
                background: #20262c !important;
                color: #d9dde1 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-detail-topbar {
                border-color: rgba(151,162,173,.16) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-detail-scroll {
                background: #20262c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-thumb {
                border-color: rgba(151,162,173,.24) !important;
                background: radial-gradient(circle at 50% 42%, #262d34 0%, #171c21 76%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025), 0 6px 18px rgba(0,0,0,.22) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-thumb img {
                background: transparent !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-item-kicker {
                color: #91a0ad !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-item-title {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-classification {
                color: #9ca6ae !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-status-badge {
                border-color: rgba(184,95,90,.24) !important;
                background: rgba(184,95,90,.08) !important;
                color: #d99994 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-status-dot {
                background: #c76863 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal p,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#4f3427]"],
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#5f3a27]"],
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#6b5548]"] {
                color: #c5cbd0 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#6b1d14]"] {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#8c6239]"] {
                color: #a7b0b8 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-detail-section-title {
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-detail-section-title::after {
                background: linear-gradient(90deg, rgba(157,170,182,.30), transparent) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-card {
                border-color: rgba(151,162,173,.22) !important;
                background: #1d2328 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018), 0 3px 10px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-header {
                border-bottom-color: rgba(151,162,173,.14) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-header h3 {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-body {
                background: #20262c !important;
                color: #c8ced3 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker {
                border-color: rgba(151,162,173,.24) !important;
                background: #171c21 !important;
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker button,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker button {
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker button:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker button:hover {
                background: #2d353d !important;
                color: #eef1f3 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker strong,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker strong {
                color: #eef1f3 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-recovery-pill {
                background: #2a3138 !important;
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-detail-mechanics {
                border-color: rgba(151,162,173,.20) !important;
                background: #1a2025 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="bg-[#efe9dc]"] {
                border-color: rgba(151,162,173,.18) !important;
                background: #242b31 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement {
                border-color: rgba(151,162,173,.22) !important;
                background: linear-gradient(180deg, #272e35 0%, #22282e 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-icon {
                border-color: rgba(151,162,173,.22) !important;
                background: #171c21 !important;
                color: #83a0b8 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-kicker {
                color: #91a0ad !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-copy strong {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-copy p {
                color: #a4adb5 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal {
                border-color: rgba(151,162,173,.28) !important;
                background: #20262c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-header {
                border-color: rgba(151,162,173,.16) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-list-wrap {
                background: #20262c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row {
                border-color: rgba(151,162,173,.20) !important;
                background: #252c33 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row:focus-visible {
                border-color: rgba(126,151,173,.42) !important;
                background: #2d353d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-thumb {
                border-color: rgba(151,162,173,.22) !important;
                background: radial-gradient(circle at 50% 42%, #262d34 0%, #171c21 78%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-thumb > span {
                color: #71808c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-copy strong {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-copy span {
                color: #9ca6ae !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-chevron {
                color: #73818d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row:hover .inventory-v29-equipped-chevron {
                color: #8faac0 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v41 .inventory-v36-slot-image,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v41 .inventory-v14-slot-image {
                border-color: rgba(151,162,173,.20) !important;
                background: radial-gradient(circle at 50% 45%, #252c33 0%, #171c21 78%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v41 .inventory-v36-slot-rarity {
                border-color: rgba(151,162,173,.20) !important;
                background: #293138 !important;
                color: #aab3bb !important;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTORY V42 — DARK THEMES CLEANUP
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-attunement-shell,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-shell,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-attunement-slot,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-slot,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v39-item-card,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v29-equipped-row,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-card,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-card,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v10-thumb,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v10-thumb,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v29-equipped-thumb,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-thumb,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-slot-image,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-slot-image {
                box-shadow: 0 4px 12px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-attunement-head,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-head,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v24-group-trigger,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v24-group-trigger {
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-shell {
                background: linear-gradient(180deg, #241d1a 0%, #1d1816 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-head {
                background: linear-gradient(180deg, #2d2522 0%, #261f1c 100%) !important;
                border: 1px solid rgba(154,122,102,.15) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-slot {
                border-color: rgba(154,122,102,.22) !important;
                background: #221c19 !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-slot.is-filled {
                background: linear-gradient(180deg, #27201d 0%, #211b18 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-slot-image,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v14-slot-image,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-thumb,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v10-thumb {
                border-color: rgba(154,122,102,.18) !important;
                background: radial-gradient(circle at 50% 45%, #2a221f 0%, #171311 78%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row {
                border-color: rgba(154,122,102,.22) !important;
                background: linear-gradient(180deg, #27201d 0%, #211b18 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card:hover,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card:focus-within,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row:hover,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row:focus-visible {
                background: linear-gradient(180deg, #2d2522 0%, #261f1c 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-card {
                border-color: rgba(154,122,102,.18) !important;
                background: #211b18 !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-header {
                background: #2d2522 !important;
                border-bottom-color: rgba(154,122,102,.14) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-body {
                background: #241d1a !important;
            }



            /* ============================================================
               INVENTÁRIO V43 — ITENS DE USO ÚNICO
            ============================================================ */

            .inventory-v43-item-used {
                border-color: rgba(112,106,98,.42) !important;
                background: linear-gradient(135deg, rgba(232,230,225,.94), rgba(217,214,208,.88)) !important;
                box-shadow: none !important;
            }

            .inventory-v43-item-used::before {
                background: rgba(92,88,82,.54) !important;
            }

            .inventory-v43-item-used .inventory-v39-card-main,
            .inventory-v43-item-used .inventory-v39-feature-strip {
                opacity: .76;
            }

            .inventory-v43-item-used .inventory-v39-card-thumb img {
                filter: grayscale(1);
                opacity: .62;
            }

            .inventory-v43-used-badge {
                position: absolute;
                right: 6px;
                bottom: 6px;
                z-index: 4;
                display: inline-flex;
                min-height: 22px;
                align-items: center;
                border: 1px solid rgba(91,86,80,.34);
                border-radius: 999px;
                background: rgba(74,71,67,.88);
                padding: 0 7px;
                color: #fff;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .075em;
                text-transform: uppercase;
                box-shadow: 0 3px 8px rgba(31,29,27,.16);
            }

            .inventory-v43-used-bar {
                display: flex;
                min-height: 48px;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border-top: 1px solid rgba(112,106,98,.28);
                background: rgba(112,106,98,.10);
                padding: 8px 11px 8px 13px;
            }

            .inventory-v43-used-copy {
                min-width: 0;
            }

            .inventory-v43-used-copy strong {
                display: block;
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                color: #595550;
            }

            .inventory-v43-used-copy span {
                display: block;
                margin-top: 1px;
                font-size: 9px;
                font-weight: 700;
                color: #7b756d;
            }

            .inventory-v43-discard-action,
            .inventory-v43-single-use-action {
                display: inline-flex;
                min-height: 30px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(107,29,20,.25);
                border-radius: 9px;
                background: rgba(255,253,249,.88);
                padding: 0 10px;
                color: #6b1d14;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .035em;
                text-transform: uppercase;
                transition: background .14s ease, border-color .14s ease, color .14s ease;
            }

            .inventory-v43-discard-action:hover,
            .inventory-v43-single-use-action:hover {
                border-color: rgba(107,29,20,.48);
                background: #6b1d14;
                color: #faf8f2;
            }

            .inventory-v43-single-use-action:disabled,
            .inventory-v43-discard-action:disabled {
                cursor: wait;
                opacity: .48;
            }

            .inventory-v43-detail-used {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-top: 16px;
                border: 1px solid rgba(112,106,98,.30);
                border-radius: 12px;
                background: rgba(112,106,98,.085);
                padding: 10px 12px;
            }

            .inventory-v43-detail-used strong {
                display: block;
                font-family: Georgia, serif;
                font-size: 14px;
                color: #595550;
            }

            .inventory-v43-detail-used p {
                margin-top: 2px;
                font-size: 10px;
                line-height: 1.45;
                color: #77716a;
            }

            .inventory-v43-single-use-note {
                margin-top: 10px;
                border-left: 2px solid rgba(107,29,20,.46);
                padding: 7px 0 7px 10px;
                color: #6b5548;
                font-size: 10px;
                line-height: 1.5;
            }

        </style>
    @endpush

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('characterInventoryV10', (
                initialItems,
                initialWallet,
                rarityLabels,
                natureLabels,
                abilityLabels,
                recoveryLabels,
                weaponTraitDefinitions,
                masteryDefinitions,
                urls
            ) => ({
                items: initialItems,
                rarityLabels,
                natureLabels,
                abilityLabels,
                recoveryLabels,
                weaponTraitDefinitions,
                masteryDefinitions,
                urls,

                detailOpen: false,
                detailItem: null,

                editorOpen: false,
                editingId: null,
                form: null,

                editorTooltip: {
                    open: false,
                    title: '',
                    text: '',
                    left: 0,
                    top: 0,
                },
                modulePickerOpen: false,
                saving: false,
                saveError: null,
                deleteConfirmOpen: false,

                // Uso único: confirmação de consumo e descarte do item já utilizado.
                singleUseConfirmOpen: false,
                pendingSingleUse: null,
                discardUsedConfirmOpen: false,
                pendingDiscardItem: null,

                imageFile: null,
                imagePreview: null,

                foundItemsOpen: false,
                foundLootItems: [],
                foundLootCurrencies: [],
                foundLootLoading: false,
                foundLootLoaded: false,
                foundLootError: null,
                foundLootBusyItemId: null,
                foundLootBusyCurrencyId: null,
                foundLootCurrencyDrafts: {},

                equippedDrawerOpen: false,
                attunementPickerOpen: false,

                /*
                |--------------------------------------------------------------------------
                | Gavetas dos grupos do inventário
                |--------------------------------------------------------------------------
                */

                inventoryGroupOpen: {
                    wonderful: true,
                    technological: true,
                    mundane: true,
                },

                inventoryGroupStorageKey:
                    @js(
                        'character-inventory-group-drawers-'
                        . $character->id
                    ),

                /*
                |--------------------------------------------------------------------------
                | Pesquisa e ordenação por categoria
                |--------------------------------------------------------------------------
                |
                | Cada gaveta possui seu próprio termo e ordenação.
                |
                */

                inventoryGroupSearch: {
                    wonderful: '',
                    technological: '',
                    mundane: '',
                },

                inventoryGroupSort: {
                    wonderful: 'default',
                    technological: 'default',
                    mundane: 'default',
                },
                attunementWarningOpen: false,
                pendingAttunementItem: null,

                busyItemId: null,
                busyFeatureKey: null,

                wallet: {
                    copper: parseInt(initialWallet?.copper) || 0,
                    silver: parseInt(initialWallet?.silver) || 0,
                    electrum: parseInt(initialWallet?.electrum) || 0,
                    gold: parseInt(initialWallet?.gold) || 0,
                    platinum: parseInt(initialWallet?.platinum) || 0,
                },
                walletOpen: false,
                walletSaving: false,
                walletPendingSave: false,
                walletTimer: null,

                toastOpen: false,
                toastMessage: null,
                toastError: false,
                toastTimer: null,

                init() {
                    this.items =
                        this.items.map(
                            item =>
                                this.normalizeItem(
                                    item
                                )
                        );

                    this.restoreInventoryGroupDrawers();
                },

                get totalCount() {
                    return this.items.length;
                },

                get foundLootCount() {
                    return this.foundLootItems.length + this.foundLootCurrencies.length;
                },

                get hasFoundLoot() {
                    return this.foundLootCount > 0;
                },

                get walletTotalCoins() {
                    return [
                        this.wallet.copper,
                        this.wallet.silver,
                        this.wallet.electrum,
                        this.wallet.gold,
                        this.wallet.platinum,
                    ].reduce(
                        (total, value) => total + Math.max(0, parseInt(value) || 0),
                        0
                    );
                },

                get equippedItems() {
                    return this.items
                        .filter(item => !!item.equipped)
                        .sort((a, b) => {
                            if (!!a.attuned !== !!b.attuned) {
                                return a.attuned ? -1 : 1;
                            }

                            return String(a.name ?? '').localeCompare(String(b.name ?? ''));
                        });
                },

                /*
                |--------------------------------------------------------------------------
                | Pesquisa / ordenação por categoria
                |--------------------------------------------------------------------------
                */

                normalizeInventorySearch(value) {
                    return String(
                        value
                        ?? ''
                    )
                        .normalize('NFD')
                        .replace(
                            /[\u0300-\u036f]/g,
                            ''
                        )
                        .toLocaleLowerCase('pt-BR')
                        .trim();
                },

                rarityRank(item) {
                    const ranks = {
                        common: 1,
                        uncommon: 2,
                        rare: 3,
                        very_rare: 4,
                        legendary: 5,
                        artifact: 6,
                    };

                    return (
                        ranks[
                            item?.rarity
                        ]
                        ?? 0
                    );
                },

                inventoryNatureLabel(item) {
                    return {
                        wonderful:
                            'Item Maravilhoso',

                        technological:
                            'Item Tecnológico',

                        mundane:
                            'Item Mundano',
                    }[
                        this.itemNature(
                            item
                        )
                    ]
                    ?? '';
                },

                rawInventoryItems(
                    nature
                ) {
                    return this.items.filter(
                        item =>
                            this.itemNature(
                                item
                            ) === nature
                    );
                },

                groupSearchValue(
                    nature
                ) {
                    return this.normalizeInventorySearch(
                        this.inventoryGroupSearch[
                            nature
                        ]
                        ?? ''
                    );
                },

                groupSearchActive(
                    nature
                ) {
                    return (
                        this.groupSearchValue(
                            nature
                        ) !== ''
                    );
                },

                matchesInventoryGroupSearch(
                    item,
                    nature
                ) {
                    const query =
                        this.groupSearchValue(
                            nature
                        );

                    if (!query) {
                        return true;
                    }

                    const weapon =
                        item?.properties
                            ?.weapon;

                    const haystack =
                        [
                            item?.name,
                            item?.rarity,
                            item?.rarity_label,
                            this.rarityLabel(
                                item
                            ),
                            this.inventoryNatureLabel(
                                item
                            ),
                            weapon?.weapon_type,
                            this.classificationLine(
                                item
                            ),
                            this.weaponDetailLine(
                                item
                            ),
                        ]
                            .filter(Boolean)
                            .map(
                                value =>
                                    this.normalizeInventorySearch(
                                        value
                                    )
                            )
                            .join(' ');

                    return haystack.includes(
                        query
                    );
                },

                sortInventoryGroupItems(
                    items,
                    nature
                ) {
                    const result =
                        [...items];

                    const mode =
                        this.inventoryGroupSort[
                            nature
                        ]
                        ?? 'default';

                    const byName =
                        (
                            a,
                            b
                        ) =>
                            String(
                                a?.name
                                ?? ''
                            ).localeCompare(
                                String(
                                    b?.name
                                    ?? ''
                                ),
                                'pt-BR',
                                {
                                    sensitivity:
                                        'base',
                                }
                            );

                    if (
                        mode
                        === 'name_asc'
                    ) {
                        return result.sort(
                            byName
                        );
                    }

                    if (
                        mode
                        === 'name_desc'
                    ) {
                        return result.sort(
                            (
                                a,
                                b
                            ) =>
                                byName(
                                    b,
                                    a
                                )
                        );
                    }

                    if (
                        mode
                        === 'rarity_desc'
                    ) {
                        return result.sort(
                            (
                                a,
                                b
                            ) => {
                                const rarity =
                                    this.rarityRank(
                                        b
                                    )
                                    - this.rarityRank(
                                        a
                                    );

                                return rarity !== 0
                                    ? rarity
                                    : byName(
                                        a,
                                        b
                                    );
                            }
                        );
                    }

                    if (
                        mode
                        === 'rarity_asc'
                    ) {
                        return result.sort(
                            (
                                a,
                                b
                            ) => {
                                const rarity =
                                    this.rarityRank(
                                        a
                                    )
                                    - this.rarityRank(
                                        b
                                    );

                                return rarity !== 0
                                    ? rarity
                                    : byName(
                                        a,
                                        b
                                    );
                            }
                        );
                    }

                    return result;
                },

                visibleInventoryItems(
                    nature
                ) {
                    return this.sortInventoryGroupItems(
                        this.rawInventoryItems(
                            nature
                        ).filter(
                            item =>
                                this.matchesInventoryGroupSearch(
                                    item,
                                    nature
                                )
                        ),
                        nature
                    );
                },

                get wonderfulItems() {
                    return this.visibleInventoryItems(
                        'wonderful'
                    );
                },

                get technologicalItems() {
                    return this.visibleInventoryItems(
                        'technological'
                    );
                },

                get mundaneItems() {
                    return this.visibleInventoryItems(
                        'mundane'
                    );
                },

                groupVisibleCount(
                    nature
                ) {
                    return this.visibleInventoryItems(
                        nature
                    ).length;
                },

                groupTotalCount(
                    nature
                ) {
                    return this.rawInventoryItems(
                        nature
                    ).length;
                },

                get inventoryGroups() {
                    return [
                        {
                            key: 'wonderful',
                            title: 'Itens Maravilhosos',
                            subtitle: 'Itens mágicos, artefatos e criações especiais.',
                            items: this.wonderfulItems,
                            total: this.groupTotalCount(
                                'wonderful'
                            ),
                        },
                        {
                            key: 'technological',
                            title: 'Itens Tecnológicos',
                            subtitle: 'Tecnologia, dispositivos e equipamentos especiais.',
                            items: this.technologicalItems,
                            total: this.groupTotalCount(
                                'technological'
                            ),
                        },
                        {
                            key: 'mundane',
                            title: 'Itens Mundanos',
                            subtitle: 'Equipamentos e objetos comuns.',
                            items: this.mundaneItems,
                            total: this.groupTotalCount(
                                'mundane'
                            ),
                        },
                    ].filter(
                        group =>
                            group.total > 0
                    );
                },

                /*
                |--------------------------------------------------------------------------
                | Gavetas das categorias
                |--------------------------------------------------------------------------
                */

                isInventoryGroupOpen(key) {
                    return (
                        this.inventoryGroupOpen[
                            key
                        ]
                        ?? true
                    );
                },

                isInventoryGroupExpanded(key) {
                    return this.isInventoryGroupOpen(
                        key
                    );
                },

                toggleInventoryGroup(key) {
                    this.inventoryGroupOpen[
                        key
                    ] =
                        !this.isInventoryGroupOpen(
                            key
                        );

                    this.persistInventoryGroupDrawers();
                },

                restoreInventoryGroupDrawers() {
                    try {
                        const saved =
                            JSON.parse(
                                localStorage.getItem(
                                    this.inventoryGroupStorageKey
                                )
                                ?? '{}'
                            );

                        [
                            'wonderful',
                            'technological',
                            'mundane',
                        ].forEach(
                            key => {
                                if (
                                    typeof saved?.[
                                        key
                                    ] === 'boolean'
                                ) {
                                    this.inventoryGroupOpen[
                                        key
                                    ] =
                                        saved[key];
                                }
                            }
                        );
                    } catch (error) {
                        /*
                        | Se localStorage estiver indisponível,
                        | usamos o estado padrão.
                        */
                    }
                },

                persistInventoryGroupDrawers() {
                    try {
                        localStorage.setItem(
                            this.inventoryGroupStorageKey,
                            JSON.stringify(
                                this.inventoryGroupOpen
                            )
                        );
                    } catch (error) {
                        /*
                        | Persistência é apenas conveniência de UX.
                        */
                    }
                },

                get attunedItems() {
                    return this.items.filter(
                        item =>
                            this.itemNature(item) === 'wonderful'
                            && item.requires_attunement
                            && item.attuned
                    );
                },

                get eligibleAttunementItems() {
                    return this.items.filter(
                        item =>
                            this.itemNature(item) === 'wonderful'
                            && item.requires_attunement
                            && !item.attuned
                    );
                },

                get primaryAttunementSlots() {
                    return [
                        this.attunedItems[0] ?? null,
                        this.attunedItems[1] ?? null,
                        this.attunedItems[2] ?? null,
                    ];
                },

                get extraAttunedItems() {
                    return this.attunedItems.slice(3);
                },

                blankProperties() {
                    return {
                        nature: 'wonderful',
                        inventory: { equippable: false },
                        custom_properties: [],
                        features: [],
                        optional_rules: {},
                    };
                },

                blankWeapon() {
                    return {
                        category: 'simple',
                        category_custom: '',
                        weapon_type: '',
                        magic_bonus: 0,
                        traits: [],
                        damage: {
                            count: 1,
                            die: 'd4',
                            ability: 'dexterity',
                            ability_custom: '',
                            type: 'Perfurante',
                        },
                        extra_damage: [],
                        masteries: [],
                    };
                },

                blankArmor() {
                    return {
                        category: 'light',
                        category_custom: '',
                        armor_type: '',
                        base_ac: 11,
                        magic_bonus: 0,

                        /*
                        | A fórmula defensiva pertence ao item.
                        |
                        | Leve: Destreza completa.
                        | Média: Destreza limitada.
                        | Pesada: sem Destreza.
                        */
                        dexterity_mode: 'full',
                        dexterity_cap: 2,

                        /*
                        | Armaduras mágicas/custom podem adicionar outros
                        | modificadores de atributo à própria fórmula.
                        */
                        ability_modifiers: [],
                    };
                },

                armorDexterityDefaults(
                    category
                ) {
                    if (
                        category
                        === 'light'
                    ) {
                        return {
                            mode: 'full',
                            cap: 2,
                        };
                    }

                    if (
                        category
                        === 'medium'
                    ) {
                        return {
                            mode: 'capped',
                            cap: 2,
                        };
                    }

                    return {
                        mode: 'none',
                        cap: 2,
                    };
                },

                normalizeArmorData(
                    raw
                ) {
                    const source =
                        raw
                        && typeof raw === 'object'
                        && !Array.isArray(raw)
                            ? raw
                            : {};

                    const armor = {
                        ...this.blankArmor(),
                        ...source,
                    };

                    const category =
                        [
                            'light',
                            'medium',
                            'heavy',
                            'custom',
                        ].includes(
                            armor.category
                        )
                            ? armor.category
                            : 'custom';

                    const defaults =
                        this.armorDexterityDefaults(
                            category
                        );

                    let dexterityMode =
                        armor.dexterity_mode;

                    if (
                        ![
                            'none',
                            'full',
                            'capped',
                        ].includes(
                            dexterityMode
                        )
                    ) {
                        dexterityMode =
                            defaults.mode;
                    }

                    let dexterityCap =
                        Math.max(
                            0,
                            parseInt(
                                armor.dexterity_cap
                            ) || defaults.cap
                        );

                    if (
                        dexterityMode
                        !== 'capped'
                    ) {
                        dexterityCap =
                            defaults.cap;
                    }

                    const abilityModifiers =
                        Array.isArray(
                            armor.ability_modifiers
                        )
                            ? [
                                ...new Set(
                                    armor.ability_modifiers
                                        .map(
                                            value =>
                                                String(
                                                    value
                                                    ?? ''
                                                )
                                        )
                                        .filter(
                                            ability =>
                                                [
                                                    'strength',
                                                    'constitution',
                                                    'intelligence',
                                                    'wisdom',
                                                    'charisma',
                                                ].includes(
                                                    ability
                                                )
                                        )
                                ),
                            ]
                            : [];

                    return {
                        ...armor,
                        category,
                        category_custom:
                            category === 'custom'
                                ? (
                                    armor.category_custom
                                    ?? ''
                                )
                                : '',
                        armor_type:
                            armor.armor_type
                            ?? '',
                        base_ac:
                            Math.max(
                                0,
                                parseInt(
                                    armor.base_ac
                                ) || 0
                            ),
                        magic_bonus:
                            parseInt(
                                armor.magic_bonus
                            ) || 0,
                        dexterity_mode:
                            dexterityMode,
                        dexterity_cap:
                            dexterityCap,
                        ability_modifiers:
                            abilityModifiers,
                    };
                },

                setArmorCategory(
                    category
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                    ) {
                        return;
                    }

                    const armor =
                        this.form.properties.armor;

                    armor.category =
                        category;

                    const defaults =
                        this.armorDexterityDefaults(
                            category
                        );

                    armor.dexterity_mode =
                        defaults.mode;

                    armor.dexterity_cap =
                        defaults.cap;
                },

                setArmorDexterityMode(
                    mode
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                        || ![
                            'none',
                            'full',
                            'capped',
                        ].includes(
                            mode
                        )
                    ) {
                        return;
                    }

                    this.form.properties.armor.dexterity_mode =
                        mode;

                    if (
                        mode === 'capped'
                        && (
                            parseInt(
                                this.form.properties.armor.dexterity_cap
                            ) || 0
                        ) <= 0
                    ) {
                        this.form.properties.armor.dexterity_cap =
                            2;
                    }
                },

                toggleArmorAbilityModifier(
                    ability
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                        || ![
                            'strength',
                            'constitution',
                            'intelligence',
                            'wisdom',
                            'charisma',
                        ].includes(
                            ability
                        )
                    ) {
                        return;
                    }

                    const current =
                        Array.isArray(
                            this.form.properties.armor.ability_modifiers
                        )
                            ? [
                                ...this.form.properties.armor.ability_modifiers
                            ]
                            : [];

                    if (
                        current.includes(
                            ability
                        )
                    ) {
                        this.form.properties.armor.ability_modifiers =
                            current.filter(
                                entry =>
                                    entry !== ability
                            );

                        return;
                    }

                    this.form.properties.armor.ability_modifiers = [
                        ...current,
                        ability,
                    ];
                },

                armorDexterityRuleLabel(
                    armor
                ) {
                    if (!armor) {
                        return '';
                    }

                    if (
                        armor.dexterity_mode
                        === 'full'
                    ) {
                        return 'DES completa';
                    }

                    if (
                        armor.dexterity_mode
                        === 'capped'
                    ) {
                        return (
                            'DES máx. +'
                            + Math.max(
                                0,
                                parseInt(
                                    armor.dexterity_cap
                                ) || 0
                            )
                        );
                    }

                    return 'Sem DES';
                },

                armorAbilityShortLabel(
                    ability
                ) {
                    return {
                        strength: 'FOR',
                        constitution: 'CON',
                        intelligence: 'INT',
                        wisdom: 'SAB',
                        charisma: 'CAR',
                    }[
                        ability
                    ]
                    ?? String(
                        ability
                        ?? ''
                    ).toUpperCase();
                },

                armorFormulaText(
                    armor
                ) {
                    if (!armor) {
                        return '';
                    }

                    const base =
                        Math.max(
                            0,
                            parseInt(
                                armor.base_ac
                            ) || 0
                        );

                    const parts = [
                        `CA ${base}`,
                    ];

                    if (
                        armor.dexterity_mode
                        === 'full'
                    ) {
                        parts.push(
                            '+ DES'
                        );
                    }

                    if (
                        armor.dexterity_mode
                        === 'capped'
                    ) {
                        parts.push(
                            '+ DES'
                            + ' (máx. +'
                            + Math.max(
                                0,
                                parseInt(
                                    armor.dexterity_cap
                                ) || 0
                            )
                            + ')'
                        );
                    }

                    (
                        armor.ability_modifiers
                        ?? []
                    ).forEach(
                        ability => {
                            parts.push(
                                '+ '
                                + this.armorAbilityShortLabel(
                                    ability
                                )
                            );
                        }
                    );

                    const magic =
                        parseInt(
                            armor.magic_bonus
                        ) || 0;

                    if (magic !== 0) {
                        parts.push(
                            (
                                magic > 0
                                    ? '+'
                                    : ''
                            )
                            + magic
                            + ' mágico'
                        );
                    }

                    return parts.join(
                        ' '
                    );
                },

                setArmorDexterityCap(
                    cap
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                    ) {
                        return;
                    }

                    const armor =
                        this.form.properties.armor;

                    armor.dexterity_mode =
                        'capped';

                    armor.dexterity_cap =
                        Math.max(
                            0,
                            parseInt(
                                cap
                            ) || 0
                        );
                },

                shieldTotalBonus(
                    shield
                ) {
                    if (!shield) {
                        return 0;
                    }

                    return (
                        (
                            parseInt(
                                shield.ac_bonus
                            ) || 0
                        )
                        + (
                            parseInt(
                                shield.magic_bonus
                            ) || 0
                        )
                    );
                },

                blankShield() {
                    return {
                        label: 'Escudo',
                        ac_bonus: 2,
                        magic_bonus: 0,
                    };
                },

                blankFeature() {
                    return {
                        title: '',
                        description: '',

                        // Estado apenas da interface do editor. Não é salvo no item.
                        editor_open: true,

                        usage: {
                            enabled: false,

                            /*
                            | spend: começa cheio e vai diminuindo.
                            | build: começa vazio e vai acumulando.
                            */
                            counter_mode: 'spend',

                            current: 1,
                            max: 1,
                            recovery: 'day',
                            recovery_custom: '',
                        },
                    };
                },

                blankForm() {
                    return {
                        name: '',
                        description: '',
                        image_url: null,
                        remove_image: false,
                        equipped: false,
                        rarity: 'common',
                        requires_attunement: false,
                        attuned: false,
                        properties: this.blankProperties(),
                        ability_bonuses: {},
                        modifiers: {},
                        is_cursed: false,
                        curse_description: null,
                    };
                },

                normalizeItem(raw) {
                    const properties = this.normalizeProperties(raw?.properties, raw);

                    return {
                        id: raw.id,
                        name: raw.name ?? '',
                        type: properties.nature,
                        description: raw.description ?? '',
                        image_url: raw.image_url ?? null,
                        equipped: !!raw.equipped,
                        is_magical: properties.nature === 'wonderful',
                        rarity: raw.rarity ?? 'common',
                        rarity_label: raw.rarity_label ?? this.rarityLabels[raw.rarity] ?? null,
                        requires_attunement: !!raw.requires_attunement,
                        attuned: !!raw.attuned,
                        armor_class: raw.armor_class ?? null,
                        damage: raw.damage ?? null,
                        attack_bonus: raw.attack_bonus ?? null,
                        damage_bonus: raw.damage_bonus ?? null,
                        ability_bonuses: raw.ability_bonuses ?? {},
                        properties,
                        modifiers: raw.modifiers ?? {},
                        notes: raw.notes ?? '',
                        is_cursed: !!raw.is_cursed,
                        curse_revealed: !!raw.curse_revealed,
                        curse_description: raw.curse_description ?? null,
                    };
                },

                normalizeProperties(raw, rawItem = null) {
                    const properties = raw && typeof raw === 'object' && !Array.isArray(raw)
                        ? JSON.parse(JSON.stringify(raw))
                        : {};

                    let nature = properties.nature;

                    if (!['wonderful', 'mundane', 'technological'].includes(nature)) {
                        if (rawItem?.type === 'technological') {
                            nature = 'technological';
                        } else if (rawItem?.is_magical) {
                            nature = 'wonderful';
                        } else {
                            nature = 'mundane';
                        }
                    }

                    properties.nature = nature;
                    properties.inventory = {
                        equippable: !!properties.inventory?.equippable,
                    };

                    if (properties.weapon && typeof properties.weapon === 'object') {
                        properties.weapon = this.normalizeWeapon(properties.weapon);
                    } else {
                        delete properties.weapon;
                    }

                    if (properties.armor && typeof properties.armor === 'object') {
                        properties.armor =
                            this.normalizeArmorData(
                                properties.armor
                            );
                    } else {
                        delete properties.armor;
                    }

                    if (properties.shield && typeof properties.shield === 'object') {
                        properties.shield = {
                            ...this.blankShield(),
                            ...properties.shield,
                        };
                    } else {
                        delete properties.shield;
                    }

                    /*
                    | Armadura e Escudo são equipamento por definição.
                    | Isso garante que itens antigos também ganhem o botão Equipar.
                    */
                    if (
                        properties.armor
                        || properties.shield
                    ) {
                        properties.inventory.equippable =
                            true;
                    }

                    properties.custom_properties = Array.isArray(properties.custom_properties)
                        ? properties.custom_properties.map(entry => ({
                            title: entry?.title ?? '',
                            value: entry?.value ?? '',
                            description: entry?.description ?? '',
                        }))
                        : [];

                    properties.features = Array.isArray(properties.features)
                        ? properties.features.map(feature => ({
                            title: feature?.title ?? '',
                            description: feature?.description ?? '',
                            usage: this.normalizeFeatureUsage(feature?.usage),

                            // Controle visual do accordion. Não vai para o payload final.
                            editor_open: !!feature?.editor_open,
                        }))
                        : [];

                    properties.optional_rules = properties.optional_rules && typeof properties.optional_rules === 'object'
                        ? properties.optional_rules
                        : {};

                    return properties;
                },

                normalizeWeapon(raw) {
                    const weapon = {
                        ...this.blankWeapon(),
                        ...raw,
                    };

                    if (Array.isArray(raw.damage_parts) && !raw.damage && raw.damage_parts[0]) {
                        weapon.damage = {
                            count: parseInt(raw.damage_parts[0].count) || 1,
                            die: raw.damage_parts[0].die ?? 'd4',
                            ability: 'none',
                            ability_custom: '',
                            type: raw.damage_parts[0].type ?? '',
                        };
                    } else {
                        weapon.damage = {
                            ...this.blankWeapon().damage,
                            ...(raw.damage ?? {}),
                        };
                    }

                    if (['Arma Simples', 'Simples'].includes(raw.category)) {
                        weapon.category = 'simple';
                    } else if (['Arma Marcial', 'Marcial'].includes(raw.category)) {
                        weapon.category = 'martial';
                    } else if (!['simple', 'martial', 'custom'].includes(weapon.category)) {
                        weapon.category = 'custom';
                        weapon.category_custom = raw.category ?? '';
                    }

                    weapon.traits = Array.isArray(raw.traits)
                        ? raw.traits.map(entry => {
                            const key = entry?.key ?? '';
                            const custom = !!entry?.custom;
                            const definition = this.weaponTraitDefinitions.find(
                                current => current.key === key
                            );

                            return {
                                key,
                                name: custom
                                    ? (entry?.name ?? '')
                                    : (definition?.name ?? entry?.name ?? ''),
                                description: custom
                                    ? (entry?.description ?? '')
                                    : (definition?.description ?? entry?.description ?? ''),
                                custom,
                            };
                        })
                        : [];

                    if (Array.isArray(raw.masteries)) {
                        weapon.masteries = raw.masteries.map(entry => {
                            const key = entry?.key ?? '';
                            const custom = !!entry?.custom;
                            const definition = this.masteryDefinitions.find(
                                current => current.key === key
                            );

                            return {
                                key,
                                name: custom
                                    ? (entry?.name ?? '')
                                    : (definition?.name ?? entry?.name ?? ''),
                                description: custom
                                    ? (entry?.description ?? '')
                                    : (definition?.description ?? entry?.description ?? ''),
                                custom,
                            };
                        });
                    } else if (raw.mastery) {
                        const legacyKey = String(raw.mastery).toLowerCase();
                        const definition = this.masteryDefinitions.find(
                            current => current.key === legacyKey
                        );

                        weapon.masteries = [{
                            key: legacyKey,
                            name: definition?.name ?? raw.mastery,
                            description: definition?.description ?? raw.mastery_description ?? '',
                            custom: false,
                        }];
                    } else {
                        weapon.masteries = [];
                    }

                    weapon.extra_damage = Array.isArray(raw.extra_damage)
                        ? raw.extra_damage.map(part => ({
                            count: Math.max(1, parseInt(part?.count) || 1),
                            die: part?.die ?? 'd6',
                            type: part?.type ?? '',
                        }))
                        : [];

                    return weapon;
                },

                normalizeFeatureUsage(raw) {
                    /*
                    |--------------------------------------------------------------------------
                    | Booleano do tracker
                    |--------------------------------------------------------------------------
                    |
                    | Dados vindos de FormData / snapshots podem chegar como strings
                    | ("0", "1", "false", "true"). Em JavaScript, a string
                    | "0" é truthy; portanto não podemos testar apenas !raw.enabled.
                    |
                    | Uma habilidade só deve possuir tracker quando o valor indicar
                    | explicitamente que ele está habilitado.
                    |
                    */
                    const enabledRaw =
                        raw?.enabled;

                    let enabled = false;

                    if (typeof enabledRaw === 'boolean') {
                        enabled = enabledRaw;
                    } else if (typeof enabledRaw === 'number') {
                        enabled = enabledRaw !== 0;
                    } else if (typeof enabledRaw === 'string') {
                        enabled = [
                            '1',
                            'true',
                            'on',
                            'yes',
                        ].includes(
                            enabledRaw
                                .trim()
                                .toLowerCase()
                        );
                    }

                    if (
                        !raw
                        || !enabled
                    ) {
                        return {
                            enabled: false,
                            counter_mode: 'spend',
                            current: 1,
                            max: 1,
                            recovery: 'day',
                            recovery_custom: '',
                        };
                    }

                    const max =
                        Math.max(
                            1,
                            parseInt(
                                raw.max
                            ) || 1
                        );

                    const counterMode =
                        [
                            'spend',
                            'build',
                        ].includes(
                            raw.counter_mode
                        )
                            ? raw.counter_mode
                            : 'spend';

                    const parsedCurrent =
                        parseInt(
                            raw.current
                        );

                    const defaultCurrent =
                        counterMode === 'build'
                            ? 0
                            : max;

                    const current =
                        Math.max(
                            0,
                            Math.min(
                                max,
                                Number.isFinite(
                                    parsedCurrent
                                )
                                    ? parsedCurrent
                                    : defaultCurrent
                            )
                        );

                    const recovery =
                        Object.prototype
                            .hasOwnProperty
                            .call(
                                this.recoveryLabels,
                                raw.recovery
                            )
                            ? raw.recovery
                            : 'day';

                    return {
                        enabled: true,
                        counter_mode:
                            counterMode,
                        current,
                        max,
                        recovery,
                        recovery_custom:
                            raw.recovery_custom
                            ?? '',
                    };
                },

                itemNature(item) {
                    return item?.properties?.nature ?? (item?.is_magical ? 'wonderful' : 'mundane');
                },

                natureLabel(item) {
                    return this.natureLabels[this.itemNature(item)] ?? 'Item';
                },

                rarityLabel(item) {
                    return item?.rarity_label || this.rarityLabels[item?.rarity] || null;
                },

                classificationLine(item) {
                    const pieces = [this.natureLabel(item)];
                    const rarity = this.rarityLabel(item);

                    if (rarity) pieces.push(rarity);

                    let text = pieces.join(', ');

                    if (item?.requires_attunement) {
                        text += ' (Sintonizável)';
                    }

                    return text;
                },

                cardClassificationLine(item) {
                    const rarity =
                        this.rarityLabel(
                            item
                        );

                    let text =
                        rarity
                        || this.natureLabel(
                            item
                        );

                    if (
                        item?.requires_attunement
                    ) {
                        text += ' · Sintonizável';
                    }

                    return text;
                },

                weaponCategoryLabel(weapon) {
                    if (!weapon) return '';
                    if (weapon.category === 'simple') return 'Arma Simples';
                    if (weapon.category === 'martial') return 'Arma Marcial';

                    return weapon.category_custom || 'Arma';
                },

                armorCategoryLabel(armor) {
                    if (!armor) return '';

                    return {
                        light: 'Armadura Leve',
                        medium: 'Armadura Média',
                        heavy: 'Armadura Pesada',
                        custom: armor.category_custom || 'Armadura',
                    }[armor.category] ?? 'Armadura';
                },

                abilityLabel(damage) {
                    if (!damage || damage.ability === 'none') return '';
                    if (damage.ability === 'custom') return damage.ability_custom || 'Custom';

                    return this.abilityLabels[damage.ability] ?? damage.ability ?? '';
                },

                weaponDamageLabel(weapon) {
                    if (!weapon?.damage) return '';

                    const count = Math.max(1, parseInt(weapon.damage.count) || 1);
                    const die = weapon.damage.die || '';
                    const ability = this.abilityLabel(weapon.damage);
                    const damageType = weapon.damage.type || '';
                    let text = die ? `${count}${die}` : '';

                    if (ability) text += `${text ? ' + ' : ''}${ability}`;
                    if (damageType) text += `${text ? ' · ' : ''}${damageType}`;

                    return text;
                },

                weaponSummary(item) {
                    const weapon = item?.properties?.weapon;
                    if (!weapon) return '';

                    return [
                        this.weaponCategoryLabel(weapon),
                        weapon.weapon_type || null,
                        ...(weapon.traits ?? []).map(trait => trait.name).filter(Boolean),
                        this.weaponDamageLabel(weapon) || null,
                    ].filter(Boolean).join(' · ');
                },

                /*
                |--------------------------------------------------------------------------
                | Apresentação enxuta no modal
                |--------------------------------------------------------------------------
                */

                /*
                |--------------------------------------------------------------------------
                | Apresentação editorial no modal
                |--------------------------------------------------------------------------
                |
                | Exemplo:
                |
                | Arma Marcial - Katana (1d10 Cortante/Energia)
                | Propriedades: Acuidade, Leve
                | Maestrias: (Sap)
                |
                */

                weaponDetailDamage(item) {
                    const weapon =
                        item?.properties?.weapon;

                    const damage =
                        weapon?.damage;

                    if (!damage) {
                        return '';
                    }

                    const count =
                        Math.max(
                            1,
                            parseInt(
                                damage.count
                            ) || 1
                        );

                    const die =
                        damage.die
                        || '';

                    const dice =
                        die
                            ? `${count}${die}`
                            : '';

                    const damageTypes = [];

                    if (damage.type) {
                        damageTypes.push(
                            damage.type
                        );
                    }

                    (
                        weapon?.extra_damage
                        ?? []
                    ).forEach(
                        extra => {
                            if (
                                extra?.type
                                && !damageTypes.includes(
                                    extra.type
                                )
                            ) {
                                damageTypes.push(
                                    extra.type
                                );
                            }
                        }
                    );

                    return [
                        dice,
                        damageTypes.length
                            ? damageTypes.join('/')
                            : null,
                    ]
                        .filter(Boolean)
                        .join(' ');
                },

                weaponDetailLine(item) {
                    const weapon =
                        item?.properties?.weapon;

                    if (!weapon) {
                        return '';
                    }

                    const identity =
                        [
                            this.weaponCategoryLabel(
                                weapon
                            ),

                            weapon.weapon_type
                                || null,
                        ]
                            .filter(Boolean)
                            .join(' - ');

                    const damage =
                        this.weaponDetailDamage(
                            item
                        );

                    if (
                        identity
                        && damage
                    ) {
                        return `${identity} (${damage})`;
                    }

                    return identity
                        || damage;
                },

                weaponPropertiesText(item) {
                    return (
                        item?.properties
                            ?.weapon
                            ?.traits
                        ?? []
                    )
                        .map(
                            trait =>
                                String(
                                    trait?.name
                                    ?? ''
                                ).trim()
                        )
                        .filter(Boolean)
                        .join(', ');
                },

                weaponMasteriesText(item) {
                    return (
                        item?.properties
                            ?.weapon
                            ?.masteries
                        ?? []
                    )
                        .map(
                            mastery => {
                                const name =
                                    String(
                                        mastery?.name
                                        ?? ''
                                    ).trim();

                                return name
                                    ? `(${name})`
                                    : null;
                            }
                        )
                        .filter(Boolean)
                        .join(' ');
                },

                nonWeaponMechanicalLines(item) {
                    const lines = [];

                    const armor =
                        this.armorSummary(item);

                    const shield =
                        this.shieldSummary(item);

                    if (armor) {
                        lines.push(armor);
                    }

                    if (shield) {
                        lines.push(shield);
                    }

                    (
                        item?.properties
                            ?.custom_properties
                        ?? []
                    ).forEach(
                        entry => {
                            const line =
                                [
                                    entry.title,
                                    entry.value,
                                ]
                                    .filter(Boolean)
                                    .join(' · ');

                            if (line) {
                                lines.push(line);
                            }
                        }
                    );

                    return lines;
                },

                armorSummary(item) {
                    const armor =
                        item?.properties?.armor;

                    if (!armor) {
                        return '';
                    }

                    return [
                        this.armorCategoryLabel(
                            armor
                        ),
                        armor.armor_type
                            || null,
                        this.armorFormulaText(
                            armor
                        ),
                    ]
                        .filter(Boolean)
                        .join(' · ');
                },

                shieldSummary(item) {
                    const shield =
                        item?.properties?.shield;

                    if (!shield) {
                        return '';
                    }

                    const base =
                        parseInt(
                            shield.ac_bonus
                        ) || 0;

                    const magic =
                        parseInt(
                            shield.magic_bonus
                        ) || 0;

                    return [
                        shield.label
                            || 'Escudo',
                        `Base ${base >= 0 ? '+' : ''}${base}`,
                        `Mágico ${magic >= 0 ? '+' : ''}${magic}`,
                        `Total +${base + magic}`,
                    ]
                        .filter(Boolean)
                        .join(' · ');
                },

                mechanicalLines(item) {
                    const lines = [];
                    const weapon = this.weaponSummary(item);
                    const armor = this.armorSummary(item);
                    const shield = this.shieldSummary(item);

                    if (weapon) lines.push(weapon);
                    if (armor) lines.push(armor);
                    if (shield) lines.push(shield);

                    (item?.properties?.custom_properties ?? []).forEach(entry => {
                        const line = [entry.title, entry.value].filter(Boolean).join(' · ');
                        if (line) lines.push(line);
                    });

                    return lines;
                },

                mechanicalPreview(item) {
                    return this.mechanicalLines(item).slice(0, 2).join('  •  ');
                },

                hasModule(module) {
                    return !!this.form?.properties?.[module];
                },

                showEditorTooltip(
                    event,
                    title,
                    content
                ) {
                    const tooltipText =
                        String(
                            content
                            ?? ''
                        ).trim();

                    if (!tooltipText) {
                        return;
                    }

                    const target =
                        event?.currentTarget;

                    if (!target) {
                        return;
                    }

                    const rect =
                        target.getBoundingClientRect();

                    const viewportWidth =
                        window.innerWidth;

                    const viewportHeight =
                        window.innerHeight;

                    const width =
                        Math.min(
                            390,
                            viewportWidth - 24
                        );

                    const estimatedHeight =
                        Math.min(
                            250,
                            viewportHeight - 24
                        );

                    let left =
                        rect.left
                        + (
                            rect.width / 2
                        )
                        - (
                            width / 2
                        );

                    left =
                        Math.max(
                            12,
                            Math.min(
                                left,
                                viewportWidth
                                - width
                                - 12
                            )
                        );

                    let top =
                        rect.bottom
                        + 8;

                    if (
                        top
                        + estimatedHeight
                        > viewportHeight
                        - 12
                    ) {
                        top =
                            Math.max(
                                12,
                                rect.top
                                - estimatedHeight
                                - 8
                            );
                    }

                    this.editorTooltip = {
                        open: true,
                        title:
                            String(
                                title
                                ?? ''
                            ).trim(),
                        text:
                            tooltipText,
                        left,
                        top,
                    };
                },

                hideEditorTooltip() {
                    this.editorTooltip.open =
                        false;
                },

                openCreate() {
                    this.editingId = null;
                    this.form = this.blankForm();
                    this.modulePickerOpen = false;
                    this.imageFile = null;
                    this.imagePreview = null;
                    this.saveError = null;
                    this.deleteConfirmOpen = false;
                    this.editorOpen = true;
                },

                openEdit(item) {
                    const normalized = this.normalizeItem(item);

                    this.detailOpen = false;
                    this.detailItem = null;
                    this.editingId = normalized.id;
                    this.form = {
                        ...this.blankForm(),
                        ...JSON.parse(JSON.stringify(normalized)),
                        properties: this.normalizeProperties(normalized.properties, normalized),
                        remove_image: false,
                    };
                    this.modulePickerOpen = false;
                    this.imageFile = null;
                    this.imagePreview = normalized.image_url ?? null;
                    this.saveError = null;
                    this.deleteConfirmOpen = false;
                    this.editorOpen = true;
                },

                closeEditor() {
                    if (this.saving) return;

                    this.hideEditorTooltip();

                    if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.imagePreview);
                    }

                    this.editorOpen = false;
                    this.editingId = null;
                    this.form = null;
                    this.modulePickerOpen = false;
                    this.imageFile = null;
                    this.imagePreview = null;
                    this.saveError = null;
                    this.deleteConfirmOpen = false;
                },

                openDetail(item) {
                    this.detailItem = this.normalizeItem(item);
                    this.detailOpen = true;
                },

                closeDetail() {
                    this.detailOpen = false;
                    this.detailItem = null;
                },

                setNature(nature) {
                    if (!this.form) return;

                    this.form.properties.nature = ['wonderful', 'mundane', 'technological'].includes(nature)
                        ? nature
                        : 'wonderful';

                    if (this.form.properties.nature !== 'wonderful') {
                        this.form.requires_attunement = false;
                        this.form.attuned = false;
                    }
                },

                setEquippable(value) {
                    if (!this.form) return;

                    this.form.properties.inventory.equippable = !!value;

                    if (!value) {
                        this.form.equipped = false;
                    }
                },

                addModule(module) {
                    if (!this.form) return;

                    if (module === 'weapon' && !this.form.properties.weapon) {
                        this.form.properties.weapon = this.blankWeapon();
                    }

                    if (module === 'armor' && !this.form.properties.armor) {
                        this.form.properties.armor =
                            this.blankArmor();

                        this.form.properties.inventory.equippable =
                            true;
                    }

                    if (module === 'shield' && !this.form.properties.shield) {
                        this.form.properties.shield =
                            this.blankShield();

                        this.form.properties.inventory.equippable =
                            true;
                    }

                    if (module === 'custom') {
                        this.addCustomProperty();
                    }

                    this.modulePickerOpen = false;
                },

                removeModule(module) {
                    if (!this.form) return;
                    delete this.form.properties[module];
                },

                addCustomProperty() {
                    this.form.properties.custom_properties.push({
                        title: '',
                        value: '',
                        description: '',
                    });
                },

                removeCustomProperty(index) {
                    this.form.properties.custom_properties.splice(index, 1);
                },

                traitSelected(key) {
                    return !!this.form?.properties?.weapon?.traits?.some(
                        trait => trait.key === key && !trait.custom
                    );
                },

                toggleTrait(definition) {
                    const traits = this.form.properties.weapon.traits;
                    const index = traits.findIndex(
                        trait => trait.key === definition.key && !trait.custom
                    );

                    if (index >= 0) {
                        traits.splice(index, 1);
                        return;
                    }

                    traits.push({
                        key: definition.key,
                        name: definition.name,
                        description: definition.description,
                        custom: false,
                    });
                },

                addCustomTrait() {
                    this.form.properties.weapon.traits.push({
                        key: `custom-trait-${Date.now()}`,
                        name: '',
                        description: '',
                        custom: true,
                    });
                },

                removeTrait(index) {
                    this.form.properties.weapon.traits.splice(index, 1);
                },

                masterySelected(key) {
                    return !!this.form?.properties?.weapon?.masteries?.some(
                        mastery => mastery.key === key && !mastery.custom
                    );
                },

                toggleMastery(definition) {
                    const masteries = this.form.properties.weapon.masteries;
                    const index = masteries.findIndex(
                        mastery => mastery.key === definition.key && !mastery.custom
                    );

                    if (index >= 0) {
                        masteries.splice(index, 1);
                        return;
                    }

                    masteries.push({
                        key: definition.key,
                        name: definition.name,
                        description: definition.description,
                        custom: false,
                    });
                },

                addCustomMastery() {
                    this.form.properties.weapon.masteries.push({
                        key: `custom-mastery-${Date.now()}`,
                        name: '',
                        description: '',
                        custom: true,
                    });
                },

                removeMastery(index) {
                    this.form.properties.weapon.masteries.splice(index, 1);
                },

                addExtraDamage() {
                    this.form.properties.weapon.extra_damage.push({
                        count: 1,
                        die: 'd6',
                        type: '',
                    });
                },

                removeExtraDamage(index) {
                    this.form.properties.weapon.extra_damage.splice(index, 1);
                },

                addFeature() {
                    // Mantém somente uma habilidade aberta por vez para o editor
                    // continuar confortável mesmo em itens com muitas habilidades.
                    this.form.properties.features.forEach(
                        feature => feature.editor_open = false
                    );

                    const feature = this.blankFeature();
                    feature.editor_open = true;

                    this.form.properties.features.push(feature);
                },

                toggleFeatureEditor(feature) {
                    const shouldOpen = !feature.editor_open;

                    this.form.properties.features.forEach(
                        entry => entry.editor_open = false
                    );

                    feature.editor_open = shouldOpen;
                },

                removeFeature(index) {
                    this.form.properties.features.splice(index, 1);
                },

                setFeatureUsage(
                    feature,
                    enabled
                ) {
                    feature.usage.enabled =
                        !!enabled;

                    if (!enabled) {
                        feature.usage.counter_mode =
                            'spend';

                        feature.usage.current =
                            1;

                        feature.usage.max =
                            1;

                        feature.usage.recovery =
                            'day';

                        feature.usage.recovery_custom =
                            '';

                        return;
                    }

                    feature.usage.max =
                        Math.max(
                            1,
                            parseInt(
                                feature.usage.max
                            ) || 1
                        );

                    if (
                        ![
                            'spend',
                            'build',
                        ].includes(
                            feature.usage.counter_mode
                        )
                    ) {
                        feature.usage.counter_mode =
                            'spend';
                    }

                    /*
                    | Ao habilitar um tracker novo:
                    | - consumo começa cheio;
                    | - acúmulo começa vazio.
                    */
                    feature.usage.current =
                        feature.usage.counter_mode
                        === 'build'
                            ? 0
                            : feature.usage.max;
                },

                setFeatureCounterMode(
                    feature,
                    mode
                ) {
                    if (
                        ![
                            'spend',
                            'build',
                        ].includes(
                            mode
                        )
                    ) {
                        return;
                    }

                    feature.usage.counter_mode =
                        mode;

                    feature.usage.max =
                        Math.max(
                            1,
                            parseInt(
                                feature.usage.max
                            ) || 1
                        );

                    feature.usage.current =
                        mode === 'build'
                            ? 0
                            : feature.usage.max;
                },

                clampFeatureUsage(feature) {
                    feature.usage.max =
                        Math.max(
                            1,
                            parseInt(
                                feature.usage.max
                            ) || 1
                        );

                    feature.usage.current =
                        Math.max(
                            0,
                            Math.min(
                                feature.usage.max,
                                parseInt(
                                    feature.usage.current
                                ) || 0
                            )
                        );
                },

                recoveryLabel(usage) {
                    if (!usage?.enabled) return '';
                    if (usage.recovery === 'custom') {
                        return usage.recovery_custom || 'Custom';
                    }

                    return this.recoveryLabels[usage.recovery] ?? usage.recovery ?? '';
                },

                trackedFeatureEntries(item) {
                    const features = item?.properties?.features ?? [];

                    return features
                        .map((feature, index) => ({
                            feature,
                            index,
                        }))
                        .filter(entry => entry.feature?.usage?.enabled);
                },

                singleUseFeatureEntries(item) {
                    return this.trackedFeatureEntries(item)
                        .filter(
                            entry =>
                                entry.feature?.usage?.recovery
                                === 'single_use'
                        );
                },

                isSingleUseItem(item) {
                    const tracked =
                        this.trackedFeatureEntries(item);

                    if (tracked.length === 0) {
                        return false;
                    }

                    const singleUse =
                        tracked.filter(
                            entry =>
                                entry.feature?.usage?.recovery
                                === 'single_use'
                        );

                    /*
                    | Um item só é tratado como descartável por inteiro quando
                    | todos os trackers ativos dele são de Uso Único. Habilidades
                    | passivas não interferem nessa regra.
                    */
                    return (
                        singleUse.length > 0
                        && singleUse.length === tracked.length
                    );
                },

                singleUseFeatureUsed(feature) {
                    const usage =
                        feature?.usage;

                    return (
                        !!usage?.enabled
                        && usage.recovery === 'single_use'
                        && (parseInt(usage.current) || 0) <= 0
                    );
                },

                singleUseItemUsed(item) {
                    if (!this.isSingleUseItem(item)) {
                        return false;
                    }

                    const entries =
                        this.singleUseFeatureEntries(item);

                    return (
                        entries.length > 0
                        &&
                        entries.every(
                            entry =>
                                this.singleUseFeatureUsed(
                                    entry.feature
                                )
                        )
                    );
                },

                requestSingleUse(item, featureIndex) {
                    const feature =
                        item?.properties?.features?.[featureIndex];

                    const usage =
                        feature?.usage;

                    if (
                        !usage?.enabled
                        || usage.recovery !== 'single_use'
                    ) {
                        return;
                    }

                    if (this.singleUseFeatureUsed(feature)) {
                        this.showToast(
                            this.isSingleUseItem(item)
                                ? 'Este item já foi usado.'
                                : 'Este uso único já foi gasto.',
                            true
                        );
                        return;
                    }

                    const max =
                        Math.max(
                            1,
                            parseInt(usage.max) || 1
                        );

                    const current =
                        Math.max(
                            0,
                            Math.min(
                                max,
                                parseInt(usage.current) || 0
                            )
                        );

                    this.pendingSingleUse = {
                        itemId: parseInt(item.id),
                        featureIndex: parseInt(featureIndex),
                        itemName: item?.name ?? 'Item',
                        featureName: feature?.title ?? 'Habilidade',
                        marksWholeItem: this.isSingleUseItem(item),
                        current,
                        max,
                        remainingAfterUse:
                            Math.max(
                                0,
                                current - 1
                            ),
                        isLastCharge:
                            current <= 1,
                    };

                    this.singleUseConfirmOpen = true;
                },

                cancelSingleUse() {
                    if (this.busyFeatureKey !== null) return;

                    this.singleUseConfirmOpen = false;
                    this.pendingSingleUse = null;
                },

                requestDiscardUsedItem(item) {
                    if (!item || !this.singleUseItemUsed(item)) {
                        return;
                    }

                    this.pendingDiscardItem = {
                        id: parseInt(item.id),
                        name: item?.name ?? 'Item',
                    };

                    this.discardUsedConfirmOpen = true;
                },

                cancelDiscardUsedItem() {
                    if (this.busyItemId !== null) return;

                    this.discardUsedConfirmOpen = false;
                    this.pendingDiscardItem = null;
                },

                handleFeatureRecoveryChange(feature) {
                    if (!feature?.usage) return;

                    if (feature.usage.recovery === 'single_use') {
                        /*
                        | Uso Único significa que as cargas gastas não voltam.
                        | O item pode possuir 1, 3, 5 ou qualquer outro máximo.
                        | Ele só fica definitivamente "Usado" ao chegar a 0.
                        */
                        const wasBuild =
                            feature.usage.counter_mode
                            === 'build';

                        feature.usage.counter_mode =
                            'spend';

                        feature.usage.max =
                            Math.max(
                                1,
                                parseInt(
                                    feature.usage.max
                                ) || 1
                            );

                        if (wasBuild) {
                            feature.usage.current =
                                feature.usage.max;
                        }

                        feature.usage.recovery_custom =
                            '';

                        this.clampFeatureUsage(feature);
                        return;
                    }

                    this.clampFeatureUsage(feature);
                },

                /*
                |--------------------------------------------------------------------------
                | Habilidades visíveis nos cards
                |--------------------------------------------------------------------------
                |
                | Diferente de trackedFeatureEntries(), esta lista inclui também
                | habilidades passivas. Assim itens como a Cursiva não ficam com
                | a parte inferior vazia só porque suas habilidades não possuem
                | rastreador.
                |
                */

                cardFeatureEntries(item) {
                    const features =
                        item?.properties?.features
                        ?? [];

                    return features
                        .map(
                            (
                                feature,
                                index
                            ) => ({
                                feature,
                                index,
                            })
                        )
                        .filter(
                            entry =>
                                entry.feature
                                && (
                                    String(
                                        entry.feature.title
                                        ?? ''
                                    ).trim()
                                    || String(
                                        entry.feature.description
                                        ?? ''
                                    ).trim()
                                )
                        );
                },

                handleImage(event) {
                    const file = event.target.files?.[0] ?? null;
                    if (!file) return;

                    if (file.size > 5 * 1024 * 1024) {
                        this.saveError = 'A imagem deve ter no máximo 5 MB.';
                        event.target.value = '';
                        return;
                    }

                    if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.imagePreview);
                    }

                    this.imageFile = file;
                    this.form.remove_image = false;
                    this.imagePreview = URL.createObjectURL(file);
                },

                removeImage() {
                    if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.imagePreview);
                    }

                    this.imageFile = null;
                    this.imagePreview = null;
                    this.form.remove_image = true;
                },

                cleanProperties() {
                    const properties = this.normalizeProperties(this.form.properties, this.form);

                    properties.custom_properties = properties.custom_properties
                        .map(entry => ({
                            title: String(entry.title ?? '').trim(),
                            value: String(entry.value ?? '').trim(),
                            description: String(entry.description ?? '').trim(),
                        }))
                        .filter(entry => entry.title || entry.value || entry.description);

                    properties.features = properties.features
                        .map(feature => ({
                            title: String(feature.title ?? '').trim(),
                            description: String(feature.description ?? '').trim(),
                            usage: this.normalizeFeatureUsage(feature.usage),
                        }))
                        .filter(feature => feature.title || feature.description);

                    if (properties.weapon) {
                        properties.weapon.traits = properties.weapon.traits
                            .map(trait => ({
                                key: String(trait.key ?? '').trim(),
                                name: String(trait.name ?? '').trim(),
                                description: String(trait.description ?? '').trim(),
                                custom: !!trait.custom,
                            }))
                            .filter(trait => trait.name);

                        properties.weapon.masteries = properties.weapon.masteries
                            .map(mastery => ({
                                key: String(mastery.key ?? '').trim(),
                                name: String(mastery.name ?? '').trim(),
                                description: String(mastery.description ?? '').trim(),
                                custom: !!mastery.custom,
                            }))
                            .filter(mastery => mastery.name);

                        properties.weapon.extra_damage = properties.weapon.extra_damage
                            .map(part => ({
                                count: Math.max(1, parseInt(part.count) || 1),
                                die: String(part.die ?? '').trim(),
                                type: String(part.type ?? '').trim(),
                            }))
                            .filter(part => part.die || part.type);
                    }

                    return properties;
                },

                buildPayload() {
                    const properties = this.cleanProperties();
                    const wonderful = properties.nature === 'wonderful';
                    const equippable = !!properties.inventory?.equippable;
                    const requiresAttunement = wonderful && !!this.form.requires_attunement;

                    return {
                        name: String(this.form.name ?? '').trim(),
                        type: properties.nature,
                        description: String(this.form.description ?? '').trim() || null,
                        quantity: 1,
                        weight: null,
                        equipped: equippable ? !!this.form.equipped : false,
                        is_magical: wonderful,
                        rarity: this.form.rarity || 'common',
                        requires_attunement: requiresAttunement,
                        attuned: requiresAttunement ? !!this.form.attuned : false,
                        ability_bonuses: this.form.ability_bonuses ?? {},
                        modifiers: this.form.modifiers ?? {},
                        properties,
                    };
                },

                appendFormValue(formData, key, value) {
                    if (Array.isArray(value)) {
                        value.forEach((entry, index) => {
                            this.appendFormValue(formData, `${key}[${index}]`, entry);
                        });
                        return;
                    }

                    if (value && typeof value === 'object' && !(value instanceof File)) {
                        Object.entries(value).forEach(([childKey, childValue]) => {
                            this.appendFormValue(formData, `${key}[${childKey}]`, childValue);
                        });
                        return;
                    }

                    if (typeof value === 'boolean') {
                        formData.append(key, value ? '1' : '0');
                        return;
                    }

                    formData.append(key, value === null ? '' : String(value));
                },

                buildFormData(payload, editing) {
                    const formData = new FormData();

                    if (editing) {
                        formData.append('_method', 'PATCH');
                    }

                    Object.entries(payload).forEach(([key, value]) => {
                        this.appendFormValue(formData, key, value);
                    });

                    if (this.imageFile) {
                        formData.append('image', this.imageFile);
                    }

                    if (this.form.remove_image) {
                        formData.append('remove_image', '1');
                    }

                    return formData;
                },

                baseHeaders() {
                    return {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                            ?? '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    };
                },

                jsonHeaders() {
                    return {
                        ...this.baseHeaders(),
                        'Content-Type': 'application/json',
                    };
                },

                async saveItem() {
                    if (this.saving || !this.form) return;

                    const payload = this.buildPayload();

                    if (!payload.name) {
                        this.saveError = 'Informe um nome para o item.';
                        return;
                    }

                    this.saving = true;
                    this.saveError = null;

                    const editing = this.editingId !== null;
                    const url = editing
                        ? this.urls.update.replace('__ITEM__', this.editingId)
                        : this.urls.store;

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: this.baseHeaders(),
                            body: this.buildFormData(payload, editing),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível salvar o item.')
                            );
                        }

                        const item = this.normalizeItem(data.item);
                        this.upsertItem(item);

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item:
                                            data.item,
                                    },
                                }
                            )
                        );

                        this.closeEditor();
                        this.openDetail(item);
                        this.showToast(editing ? 'Item atualizado.' : 'Item criado.');
                    } catch (error) {
                        this.saveError = error.message ?? 'Não foi possível salvar o item.';
                    } finally {
                        this.saving = false;
                    }
                },

                async deleteItem() {
                    if (!this.editingId || this.saving) return;

                    this.saving = true;

                    try {
                        const response = await fetch(
                            this.urls.destroy.replace('__ITEM__', this.editingId),
                            {
                                method: 'DELETE',
                                headers: this.baseHeaders(),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível excluir o item.'
                            );
                        }

                        const id = parseInt(this.editingId);

                        this.items = this.items.filter(
                            item => parseInt(item.id) !== id
                        );

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item: {
                                            id,
                                            deleted: true,
                                        },
                                    },
                                }
                            )
                        );

                        this.editorOpen = false;
                        this.editingId = null;
                        this.form = null;
                        this.deleteConfirmOpen = false;

                        this.showToast('Item removido do inventário.');
                    } catch (error) {
                        this.saveError = error.message ?? 'Não foi possível excluir o item.';
                    } finally {
                        this.saving = false;
                    }
                },

                upsertItem(raw) {
                    const item = this.normalizeItem(raw);
                    const index = this.items.findIndex(
                        current => parseInt(current.id) === parseInt(item.id)
                    );

                    if (index >= 0) {
                        this.items[index] = item;
                    } else {
                        this.items.push(item);
                    }

                    if (
                        this.detailItem
                        && parseInt(this.detailItem.id) === parseInt(item.id)
                    ) {
                        this.detailItem = item;
                    }

                    return item;
                },

                async toggleEquipped(item) {
                    if (
                        this.busyItemId !== null
                        || !item?.properties?.inventory?.equippable
                    ) {
                        return;
                    }

                    this.busyItemId = item.id;
                    const previous = !!item.equipped;
                    item.equipped = !previous;

                    try {
                        const response = await fetch(
                            this.urls.equipped.replace('__ITEM__', item.id),
                            {
                                method: 'PATCH',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({
                                    equipped: item.equipped,
                                }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível alterar o equipamento.'
                            );
                        }

                        this.upsertItem(data.item);

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item:
                                            data.item,
                                    },
                                }
                            )
                        );
                    } catch (error) {
                        item.equipped = previous;
                        this.showToast(error.message, true);
                    } finally {
                        this.busyItemId = null;
                    }
                },

                requestAttunement(item) {
                    if (!item || item.attuned || !item.requires_attunement) return;

                    if (this.attunedItems.length >= 3) {
                        this.pendingAttunementItem = item;
                        this.attunementWarningOpen = true;
                        return;
                    }

                    this.applyAttunement(item, true);
                },

                confirmExtraAttunement() {
                    const item = this.pendingAttunementItem;

                    if (!item) {
                        this.attunementWarningOpen = false;
                        return;
                    }

                    this.pendingAttunementItem = null;
                    this.attunementWarningOpen = false;
                    this.attunementPickerOpen = false;

                    this.applyAttunement(item, true);
                },

                async applyAttunement(item, attuned) {
                    if (this.busyItemId !== null) return;

                    this.busyItemId = item.id;
                    const previous = !!item.attuned;
                    item.attuned = !!attuned;

                    try {
                        const response = await fetch(
                            this.urls.attunement.replace('__ITEM__', item.id),
                            {
                                method: 'PATCH',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({
                                    attuned: !!attuned,
                                }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível alterar a sintonização.'
                            );
                        }

                        this.upsertItem(data.item);
                    } catch (error) {
                        item.attuned = previous;
                        this.showToast(
                            error.message ?? 'Não foi possível alterar a sintonização.',
                            true
                        );
                    } finally {
                        this.busyItemId = null;
                    }
                },

                async persistFeatureUses(item, featureIndex, next) {
                    const feature =
                        item?.properties?.features?.[featureIndex];

                    const usage =
                        feature?.usage;

                    if (!usage?.enabled) return null;
                    if (this.busyFeatureKey !== null) return null;

                    const key = `${item.id}:${featureIndex}`;
                    const max = Math.max(1, parseInt(usage.max) || 1);
                    const current = Math.max(
                        0,
                        Math.min(max, parseInt(usage.current) || 0)
                    );

                    const normalizedNext = Math.max(
                        0,
                        Math.min(max, parseInt(next) || 0)
                    );

                    if (normalizedNext === current) {
                        return item;
                    }

                    this.busyFeatureKey = key;
                    usage.current = normalizedNext;

                    try {
                        const response = await fetch(
                            this.urls.featureUses
                                .replace('__ITEM__', item.id)
                                .replace('__FEATURE__', featureIndex),
                            {
                                method: 'PATCH',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({
                                    current: normalizedNext,
                                }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível atualizar os usos.'
                            );
                        }

                        return this.upsertItem(data.item);
                    } catch (error) {
                        usage.current = current;
                        this.showToast(
                            error.message ?? 'Não foi possível atualizar os usos.',
                            true
                        );
                        return null;
                    } finally {
                        this.busyFeatureKey = null;
                    }
                },

                async changeFeatureUses(item, featureIndex, delta) {
                    const feature =
                        item?.properties?.features?.[featureIndex];

                    const usage =
                        feature?.usage;

                    if (!usage?.enabled) return;
                    if (this.busyFeatureKey !== null) return;

                    const max = Math.max(1, parseInt(usage.max) || 1);
                    const current = Math.max(
                        0,
                        Math.min(max, parseInt(usage.current) || 0)
                    );

                    /*
                    | Uso Único não possui botão de "desfazer".
                    | O gasto passa pela confirmação própria e, depois de usado,
                    | o item fica aguardando descarte.
                    */
                    if (usage.recovery === 'single_use') {
                        if (delta < 0) {
                            if (current <= 0) {
                                this.showToast(
                                    'Este item já foi usado.',
                                    true
                                );
                                return;
                            }

                            this.requestSingleUse(
                                item,
                                featureIndex
                            );
                            return;
                        }

                        if (current <= 0 && delta > 0) {
                            this.showToast(
                                'Itens de uso único não podem ser restaurados pelo contador.',
                                true
                            );
                            return;
                        }
                    }

                    const next = Math.max(
                        0,
                        Math.min(max, current + delta)
                    );

                    if (next === current) return;

                    await this.persistFeatureUses(
                        item,
                        featureIndex,
                        next
                    );
                },

                async confirmSingleUse() {
                    const pending =
                        this.pendingSingleUse;

                    if (!pending || this.busyFeatureKey !== null) {
                        return;
                    }

                    const item =
                        this.items.find(
                            current =>
                                parseInt(current.id)
                                === parseInt(pending.itemId)
                        )
                        ?? (
                            parseInt(this.detailItem?.id)
                            === parseInt(pending.itemId)
                                ? this.detailItem
                                : null
                        );

                    if (!item) {
                        this.cancelSingleUse();
                        return;
                    }

                    const featureIndex =
                        parseInt(pending.featureIndex);

                    this.singleUseConfirmOpen = false;
                    this.pendingSingleUse = null;

                    const feature =
                        item?.properties
                            ?.features
                            ?.[featureIndex];

                    const usage =
                        feature?.usage;

                    const max =
                        Math.max(
                            1,
                            parseInt(usage?.max) || 1
                        );

                    const current =
                        Math.max(
                            0,
                            Math.min(
                                max,
                                parseInt(
                                    usage?.current
                                ) || 0
                            )
                        );

                    if (current <= 0) {
                        this.showToast(
                            'Este item já está sem cargas.',
                            true
                        );
                        return;
                    }

                    const updated =
                        await this.persistFeatureUses(
                            item,
                            featureIndex,
                            current - 1
                        );

                    if (!updated) return;

                    const updatedFeature =
                        updated?.properties
                            ?.features
                            ?.[featureIndex];

                    const remaining =
                        Math.max(
                            0,
                            parseInt(
                                updatedFeature
                                    ?.usage
                                    ?.current
                            ) || 0
                        );

                    this.showToast(
                        this.singleUseItemUsed(updated)
                            ? 'Última carga gasta. O item agora está Usado e pode ser descartado.'
                            : (
                                remaining > 0
                                    ? (
                                        'Uso registrado. Restam '
                                        + remaining
                                        + ' carga'
                                        + (
                                            remaining === 1
                                                ? ''
                                                : 's'
                                        )
                                        + '.'
                                    )
                                    : 'Uso registrado.'
                            )
                    );
                },

                async confirmDiscardUsedItem() {
                    const pending =
                        this.pendingDiscardItem;

                    if (!pending || this.busyItemId !== null) {
                        return;
                    }

                    const item =
                        this.items.find(
                            current =>
                                parseInt(current.id)
                                === parseInt(pending.id)
                        );

                    if (!item || !this.singleUseItemUsed(item)) {
                        this.cancelDiscardUsedItem();
                        return;
                    }

                    this.busyItemId = item.id;

                    try {
                        const response = await fetch(
                            this.urls.destroy.replace('__ITEM__', item.id),
                            {
                                method: 'DELETE',
                                headers: this.baseHeaders(),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível jogar o item fora.'
                            );
                        }

                        const id = parseInt(item.id);

                        this.items = this.items.filter(
                            current => parseInt(current.id) !== id
                        );

                        if (
                            this.detailItem
                            && parseInt(this.detailItem.id) === id
                        ) {
                            this.closeDetail();
                        }

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item: {
                                            id,
                                            deleted: true,
                                        },
                                    },
                                }
                            )
                        );

                        this.discardUsedConfirmOpen = false;
                        this.pendingDiscardItem = null;
                        this.showToast('Item descartado do inventário.');
                    } catch (error) {
                        this.showToast(
                            error.message ?? 'Não foi possível jogar o item fora.',
                            true
                        );
                    } finally {
                        this.busyItemId = null;
                    }
                },

                openFoundItems() {
                    this.foundItemsOpen = true;
                    this.loadFoundLoot();
                },

                normalizeFoundLootItem(raw) {
                    return {
                        id: parseInt(raw?.id) || 0,
                        loot_drop_id: parseInt(raw?.loot_drop_id) || 0,
                        drop_name: raw?.drop_name ?? '',
                        campaign_name: raw?.campaign_name ?? '',
                        name: raw?.name ?? '',
                        type: raw?.type ?? 'mundane',
                        description: raw?.description ?? '',
                        image_url: raw?.image_url ?? null,
                        quantity_remaining: Math.max(0, parseInt(raw?.quantity_remaining) || 0),
                        weight: raw?.weight === null || raw?.weight === undefined || raw?.weight === ''
                            ? null
                            : Number(raw.weight),
                        is_magical: !!raw?.is_magical,
                        rarity: raw?.rarity ?? null,
                        rarity_label: raw?.rarity_label ?? (raw?.rarity ? (this.rarityLabels?.[raw.rarity] ?? raw.rarity) : null),
                        requires_attunement: !!raw?.requires_attunement,
                        armor_class: raw?.armor_class ?? null,
                        damage: raw?.damage ?? null,
                        attack_bonus: raw?.attack_bonus ?? null,
                        damage_bonus: raw?.damage_bonus ?? null,
                        ability_bonuses: raw?.ability_bonuses ?? {},
                        properties: raw?.properties ?? {},
                        modifiers: raw?.modifiers ?? {},
                        notes: raw?.notes ?? '',
                    };
                },

                normalizeFoundLootCurrency(raw) {
                    const coins = raw?.coins ?? {};

                    return {
                        id: parseInt(raw?.id) || 0,
                        loot_drop_id: parseInt(raw?.loot_drop_id) || 0,
                        drop_name: raw?.drop_name ?? '',
                        campaign_name: raw?.campaign_name ?? '',
                        label: raw?.label ?? 'Moedas',
                        distribution_mode: raw?.distribution_mode ?? 'first_claim',
                        coins: {
                            copper: this.normalizeMoney(coins?.copper),
                            silver: this.normalizeMoney(coins?.silver),
                            electrum: this.normalizeMoney(coins?.electrum),
                            gold: this.normalizeMoney(coins?.gold),
                            platinum: this.normalizeMoney(coins?.platinum),
                        },
                    };
                },

                foundLootNatureLabel(item) {
                    const nature = item?.type ?? (item?.is_magical ? 'wonderful' : 'mundane');
                    return this.natureLabels?.[nature] ?? String(nature ?? 'Item');
                },

                foundLootCoinLines(entry) {
                    const labels = {
                        copper: 'PC',
                        silver: 'PP',
                        electrum: 'PE',
                        gold: 'PO',
                        platinum: 'PL',
                    };

                    return Object.entries(labels)
                        .map(([coin, label]) => ({
                            coin,
                            label,
                            amount: this.normalizeMoney(entry?.coins?.[coin]),
                        }))
                        .filter(line => line.amount > 0);
                },

                foundLootCurrencyModeLabel(entry) {
                    if (entry?.distribution_mode === 'split') {
                        return 'Sua parte já está separada';
                    }

                    if (entry?.distribution_mode === 'open') {
                        return 'Caixa comum da Party';
                    }

                    return 'Reservado para você';
                },

                ensureFoundLootCurrencyDraft(entry) {
                    const id = parseInt(entry?.id) || 0;

                    if (!id) return;

                    if (!this.foundLootCurrencyDrafts[id]) {
                        this.foundLootCurrencyDrafts[id] = {
                            copper: 0,
                            silver: 0,
                            electrum: 0,
                            gold: 0,
                            platinum: 0,
                        };
                    }
                },

                foundLootCurrencyDraftAmount(entry, coin) {
                    this.ensureFoundLootCurrencyDraft(entry);

                    return this.normalizeMoney(
                        this.foundLootCurrencyDrafts?.[entry?.id]?.[coin]
                    );
                },

                async loadFoundLoot(silent = false) {
                    if (this.foundLootLoading) return;

                    this.foundLootLoading = true;
                    if (!silent) this.foundLootError = null;

                    try {
                        const response = await fetch(this.urls.foundLootIndex, {
                            method: 'GET',
                            headers: this.baseHeaders(),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível carregar os itens encontrados.')
                            );
                        }

                        this.foundLootItems = Array.isArray(data?.items)
                            ? data.items
                                .map(item => this.normalizeFoundLootItem(item))
                                .filter(item => item.id > 0)
                            : [];

                        this.foundLootCurrencies = Array.isArray(data?.currencies)
                            ? data.currencies
                                .map(entry => this.normalizeFoundLootCurrency(entry))
                                .filter(entry => entry.id > 0)
                            : [];

                        for (const entry of this.foundLootCurrencies) {
                            this.ensureFoundLootCurrencyDraft(entry);
                        }

                        this.foundLootLoaded = true;
                        this.foundLootError = null;
                    } catch (error) {
                        this.foundLootError = error?.message
                            ?? 'Não foi possível carregar os itens encontrados.';

                        if (!silent) {
                            this.showToast(this.foundLootError, true);
                        }
                    } finally {
                        this.foundLootLoading = false;
                    }
                },

                async claimFoundLootItem(item) {
                    const itemId = parseInt(item?.id) || 0;

                    if (
                        !itemId
                        || this.foundLootBusyItemId !== null
                        || this.foundLootBusyCurrencyId !== null
                    ) {
                        return;
                    }

                    this.foundLootBusyItemId = itemId;

                    try {
                        const response = await fetch(
                            this.urls.foundLootClaimItem.replace('__LOOT_ITEM__', itemId),
                            {
                                method: 'POST',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({ quantity: 1 }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível adicionar o item ao inventário.')
                            );
                        }

                        if (data?.item) {
                            this.upsertItem(data.item);
                        }

                        const remaining = Math.max(0, parseInt(data?.quantity_remaining) || 0);

                        if (remaining > 0) {
                            const found = this.foundLootItems.find(
                                current => parseInt(current.id) === itemId
                            );

                            if (found) found.quantity_remaining = remaining;
                        } else {
                            this.foundLootItems = this.foundLootItems.filter(
                                current => parseInt(current.id) !== itemId
                            );
                        }

                        this.showToast(data?.message ?? 'Item adicionado ao inventário.');
                    } catch (error) {
                        this.showToast(
                            error?.message ?? 'Não foi possível adicionar o item ao inventário.',
                            true
                        );

                        await this.loadFoundLoot(true);
                    } finally {
                        this.foundLootBusyItemId = null;
                    }
                },

                async claimFoundLootCurrency(entry) {
                    const currencyId = parseInt(entry?.id) || 0;

                    if (
                        !currencyId
                        || this.foundLootBusyCurrencyId !== null
                        || this.foundLootBusyItemId !== null
                    ) {
                        return;
                    }

                    this.ensureFoundLootCurrencyDraft(entry);

                    const payload = {};

                    if (entry?.distribution_mode === 'open') {
                        for (const coin of [
                            'copper',
                            'silver',
                            'electrum',
                            'gold',
                            'platinum',
                        ]) {
                            payload[coin] =
                                this.foundLootCurrencyDraftAmount(
                                    entry,
                                    coin
                                );
                        }

                        const hasAmount =
                            Object.values(payload)
                                .some(value => value > 0);

                        if (!hasAmount) {
                            this.showToast(
                                'Informe quanto você quer retirar da caixa da Party.',
                                true
                            );
                            return;
                        }
                    }

                    this.foundLootBusyCurrencyId = currencyId;

                    try {
                        const response = await fetch(
                            this.urls.foundLootClaimCurrency.replace('__CURRENCY__', currencyId),
                            {
                                method: 'POST',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify(payload),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível adicionar as moedas à bolsa.')
                            );
                        }

                        if (data?.wallet) {
                            this.wallet = {
                                copper: this.normalizeMoney(data.wallet.copper),
                                silver: this.normalizeMoney(data.wallet.silver),
                                electrum: this.normalizeMoney(data.wallet.electrum),
                                gold: this.normalizeMoney(data.wallet.gold),
                                platinum: this.normalizeMoney(data.wallet.platinum),
                            };
                        }

                        this.foundLootCurrencyDrafts[currencyId] = {
                            copper: 0,
                            silver: 0,
                            electrum: 0,
                            gold: 0,
                            platinum: 0,
                        };

                        await this.loadFoundLoot(true);

                        this.showToast(
                            data?.message
                            ?? 'Moedas adicionadas à bolsa.'
                        );
                    } catch (error) {
                        this.showToast(
                            error?.message ?? 'Não foi possível adicionar as moedas à bolsa.',
                            true
                        );

                        await this.loadFoundLoot(true);
                    } finally {
                        this.foundLootBusyCurrencyId = null;
                    }
                },

                normalizeMoney(value) {
                    const digits = String(value ?? '')
                        .replace(/\D/g, '');

                    return Math.max(
                        0,
                        parseInt(digits, 10) || 0
                    );
                },

                formatMoney(value) {
                    return String(
                        this.normalizeMoney(value)
                    ).replace(
                        /\B(?=(\d{3})+(?!\d))/g,
                        ','
                    );
                },

                handleMoneyInput(currency, event) {
                    const value = this.normalizeMoney(
                        event?.target?.value
                    );

                    this.wallet[currency] = value;

                    if (event?.target) {
                        event.target.value = this.formatMoney(value);
                    }

                    this.saveWallet();
                },

                saveWallet() {
                    clearTimeout(this.walletTimer);

                    this.walletTimer = setTimeout(() => {
                        this.walletTimer = null;
                        this.persistWallet();
                    }, 300);
                },

                async persistWallet() {
                    if (this.walletSaving) {
                        this.walletPendingSave = true;
                        return;
                    }

                    const payload = {
                        copper: this.normalizeMoney(this.wallet.copper),
                        silver: this.normalizeMoney(this.wallet.silver),
                        electrum: this.normalizeMoney(this.wallet.electrum),
                        gold: this.normalizeMoney(this.wallet.gold),
                        platinum: this.normalizeMoney(this.wallet.platinum),
                    };

                    this.walletSaving = true;
                    this.walletPendingSave = false;

                    try {
                        const response = await fetch(this.urls.wallet, {
                            method: 'PATCH',
                            headers: this.jsonHeaders(),
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (
                                        data?.message
                                        ?? 'Não foi possível salvar as moedas.'
                                    )
                            );
                        }
                    } catch (error) {
                        this.showToast(
                            error?.message
                            ?? 'Não foi possível salvar as moedas.',
                            true
                        );
                    } finally {
                        this.walletSaving = false;

                        const current = {
                            copper: this.normalizeMoney(this.wallet.copper),
                            silver: this.normalizeMoney(this.wallet.silver),
                            electrum: this.normalizeMoney(this.wallet.electrum),
                            gold: this.normalizeMoney(this.wallet.gold),
                            platinum: this.normalizeMoney(this.wallet.platinum),
                        };

                        const changedDuringRequest =
                            Object.keys(payload).some(
                                key => current[key] !== payload[key]
                            );

                        if (
                            this.walletPendingSave
                            || changedDuringRequest
                        ) {
                            this.walletPendingSave = false;
                            this.saveWallet();
                        }
                    }
                },

                showToast(message, error = false) {
                    this.toastMessage = message;
                    this.toastError = !!error;
                    this.toastOpen = true;

                    clearTimeout(this.toastTimer);

                    this.toastTimer = setTimeout(() => {
                        this.toastOpen = false;
                    }, 4200);
                },
            }));
        });
    </script>
@endonce

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
        }
    )"
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
    {{-- ============================================================
         TOPO — V14 SIMPLIFICADO
    ============================================================= --}}
    <div
        class="
            inventory-v14-toolbar
            inventory-v23-main-topbar
            inventory-v38-toolbar
        "
    >
        <div
            class="
                inventory-v14-actions
                inventory-v38-actions
            "
        >
            <button
                type="button"
                @click="openCreate()"
                class="inventory-v14-primary inventory-v38-primary-action"
            >
                <span class="inventory-v14-plus">+</span>
                <span>Novo Item</span>
            </button>

            <button
                type="button"
                @click="equippedDrawerOpen = true"
                class="inventory-v14-action inventory-v38-secondary-action"
            >
                <span>Equipados</span>
                <span class="inventory-v14-badge" x-text="equippedItems.length"></span>
            </button>

            <button
                type="button"
                @click="openFoundItems()"
                class="inventory-v14-action inventory-v14-action-muted inventory-v38-secondary-action"
            >
                <span>Encontrados</span>

                <span
                    x-show="foundLootLoaded && foundLootCount > 0"
                    x-cloak
                    class="inventory-v14-badge"
                    x-text="foundLootCount"
                ></span>
            </button>
        </div>

        <div class="inventory-v14-toolbar-right inventory-v38-toolbar-right">
            {{-- Bolsa compacta --}}
            <div
                class="inventory-v14-wallet-wrap inventory-v38-wallet-wrap"
                @click.outside="walletOpen = false"
            >
                <button
                    type="button"
                    @click="walletOpen = !walletOpen"
                    class="inventory-v14-wallet-trigger inventory-v38-wallet-trigger"
                    :aria-expanded="walletOpen"
                    aria-label="Abrir bolsa de moedas"
                >
                    <span class="inventory-v14-pouch" aria-hidden="true">
                        <svg viewBox="0 0 32 32" fill="none">
                            <path d="M11 5.5h10l-2.2 4.1c4.5 2.1 7.2 6.1 7.2 10.2 0 5.1-4.2 7.7-10 7.7S6 24.9 6 19.8c0-4.1 2.7-8.1 7.2-10.2L11 5.5Z" />
                            <path d="M10.2 10.2h11.6M9.2 13.1c4.3 1 9.3 1 13.6 0" />
                            <circle cx="13" cy="18" r="2.25" />
                            <circle cx="18.5" cy="18.8" r="2.25" />
                            <circle cx="15.8" cy="22.1" r="2.25" />
                        </svg>
                    </span>

                    <span class="inventory-v14-wallet-copy">
                        <span class="inventory-v14-wallet-label">Bolsa de moedas</span>

                        <span class="inventory-v38-wallet-total-line">
                            <strong x-text="formatMoney(walletTotalCoins)"></strong>
                            <small>moedas</small>
                        </span>
                    </span>

                    <svg
                        class="inventory-v14-wallet-chevron"
                        :class="{ 'is-open': walletOpen }"
                        viewBox="0 0 20 20"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path d="M5 7.5 10 12.5 15 7.5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div
                    x-show="walletOpen"
                    x-cloak
                    x-transition.opacity.duration.120ms
                    class="inventory-v14-wallet-panel inventory-v38-wallet-panel"
                >
                    <div
                        class="
                            inventory-v14-wallet-panel-head
                            inventory-v38-wallet-panel-head
                        "
                    >
                        <div>


                            <strong>Bolsa de moedas</strong>

                        </div>

                        <span
                            x-show="walletSaving"
                            x-cloak
                            class="
                                inventory-v14-saving
                                inventory-v38-wallet-saving
                            "
                        >
                            Salvando…
                        </span>
                    </div>

                    <div class="inventory-v38-wallet-summary">
                        <div>
                            <span>Total na bolsa</span>

                            <strong
                                x-text="
                                    formatMoney(
                                        walletTotalCoins
                                    )
                                "
                            ></strong>
                        </div>

                        <span class="inventory-v38-wallet-summary-note">
                            moedas
                        </span>
                    </div>

                    <div
                        class="
                            inventory-v14-coins
                            inventory-v38-coins
                        "
                    >
                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-copper" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="M12 7.3v9.4M8.8 9.5h6.4M8.8 14.5h6.4"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PC</b><small>Cobre</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.copper)"
                                @input="handleMoneyInput('copper', $event)"
                                @blur="$event.target.value = formatMoney(wallet.copper)"
                                aria-label="Peças de Cobre"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-silver" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PP</b><small>Prata</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.silver)"
                                @input="handleMoneyInput('silver', $event)"
                                @blur="$event.target.value = formatMoney(wallet.silver)"
                                aria-label="Peças de Prata"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-electrum" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="M8.3 12h7.4M12 8.3v7.4"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PE</b><small>Electro</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.electrum)"
                                @input="handleMoneyInput('electrum', $event)"
                                @blur="$event.target.value = formatMoney(wallet.electrum)"
                                aria-label="Peças de Electro"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-gold" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PO</b><small>Ouro</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.gold)"
                                @input="handleMoneyInput('gold', $event)"
                                @blur="$event.target.value = formatMoney(wallet.gold)"
                                aria-label="Peças de Ouro"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card inventory-v38-coin-card--platinum">
                            <span class="inventory-v14-coin-icon coin-platinum" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="M8.5 8.5h7v7h-7zM10.5 6.5h3M10.5 17.5h3"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PL</b><small>Platina</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.platinum)"
                                @input="handleMoneyInput('platinum', $event)"
                                @blur="$event.target.value = formatMoney(wallet.platinum)"
                                aria-label="Peças de Platina"
                            >
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================
         SINTONIZAÇÃO — V14
    ============================================================= --}}
    <section
        class="
            inventory-v14-attunement
            inventory-v23-attunement-panel
            inventory-v36-attunement-shell
        "
    >
        <div
            class="
                inventory-v14-section-head
                inventory-v36-attunement-head
            "
        >
            <div
                class="
                    inventory-v14-section-title
                    inventory-v36-attunement-title
                "
            >
                <span
                    class="
                        inventory-v14-diamond
                        inventory-v36-attunement-icon
                    "
                >
                    ✦
                </span>

                <div class="inventory-v36-attunement-copy">
                    <div
                        class="
                            inventory-v14-title-line
                            inventory-v36-attunement-line
                        "
                    >
                        <h3>Sintonização</h3>

                        <span
                            class="
                                inventory-v14-count
                                inventory-v36-attunement-count
                            "
                            x-text="attunedItems.length + '/3'"
                        ></span>
                    </div>

                    <p>Itens vinculados ao personagem.</p>
                </div>
            </div>

            <button
                type="button"
                @click="attunementPickerOpen = true"
                class="
                    inventory-v14-text-action
                    inventory-v36-attunement-action
                "
            >
                Escolher item
            </button>
        </div>

        <div
            class="
                inventory-v14-slots
                inventory-v36-attunement-slots
            "
        >
            <template
                x-for="(slot, index) in primaryAttunementSlots"
                :key="'slot-' + index"
            >
                <button
                    type="button"
                    @click="
                        slot
                            ? openDetail(slot)
                            : attunementPickerOpen = true
                    "
                    class="
                        inventory-v14-slot
                        inventory-v36-attunement-slot
                    "
                    :class="{ 'is-filled': !!slot, 'is-empty': !slot }"
                >
                    <template x-if="slot">
                        <div
                            class="
                                inventory-v14-slot-content
                                inventory-v36-slot-content
                            "
                        >
                            <div
                                class="
                                    inventory-v10-thumb
                                    inventory-v14-slot-image
                                    inventory-v36-slot-image
                                "
                            >
                                <template x-if="slot.image_url">
                                    <img :src="slot.image_url" :alt="slot.name">
                                </template>

                                <template x-if="!slot.image_url">
                                    <span>◆</span>
                                </template>
                            </div>

                            <div
                                class="
                                    inventory-v14-slot-copy
                                    inventory-v36-slot-copy
                                "
                            >
                                <div class="inventory-v36-slot-meta">
                                    <small>
                                        Espaço
                                        <span x-text="index + 1"></span>
                                    </small>

                                    <span
                                        class="inventory-v36-slot-rarity"
                                        x-text="rarityLabel(slot)"
                                    ></span>
                                </div>

                                <strong x-text="slot.name"></strong>

                                <span class="inventory-v36-slot-hint">
                                    Clique para ver detalhes
                                </span>
                            </div>
                        </div>
                    </template>

                    <template x-if="!slot">
                        <div
                            class="
                                inventory-v14-slot-empty
                                inventory-v36-slot-empty
                            "
                        >
                            <span class="inventory-v14-slot-plus">+</span>

                            <div class="inventory-v36-slot-empty-copy">
                                <strong>Espaço livre</strong>
                                <small>Escolher item para sintonizar</small>
                            </div>
                        </div>
                    </template>
                </button>
            </template>
        </div>

        <div
            x-show="extraAttunedItems.length > 0"
            x-cloak
            class="
                inventory-v14-extra-attuned
                inventory-v36-extra-attuned
            "
        >
            <span>Extras</span>

            <template x-for="item in extraAttunedItems" :key="'extra-' + item.id">
                <button
                    type="button"
                    @click="openDetail(item)"
                    x-text="item.name"
                ></button>
            </template>
        </div>
    </section>

    {{-- LISTA --}}
    <div class="inventory-v23-list">
        <template x-for="group in inventoryGroups" :key="group.key">
            <section
                class="
                    inventory-v23-group-panel
                    inventory-v24-group-drawer
                "
                :class="{
                    'is-open':
                        isInventoryGroupExpanded(
                            group.key
                        ),

                    'is-collapsed':
                        !isInventoryGroupExpanded(
                            group.key
                        )
                }"
            >
                <button
                    type="button"
                    @click="
                        toggleInventoryGroup(
                            group.key
                        )
                    "
                    class="
                        inventory-v14-group-head
                        inventory-v24-group-trigger
                    "
                    :aria-expanded="
                        isInventoryGroupExpanded(
                            group.key
                        )
                            ? 'true'
                            : 'false'
                    "
                >
                    <div class="inventory-v24-group-copy">
                        <h3 x-text="group.title"></h3>
                        <p x-text="group.subtitle"></p>
                    </div>

                    <div class="inventory-v24-group-meta">
                        <span
                            class="inventory-v14-group-count"
                            x-text="
                                groupSearchActive(
                                    group.key
                                )
                                    ? (
                                        group.items.length
                                        + '/'
                                        + group.total
                                    )
                                    : group.total
                            "
                        ></span>

                        <span
                            class="inventory-v24-group-chevron"
                            :class="{
                                'is-open':
                                    isInventoryGroupOpen(
                                        group.key
                                    )
                            }"
                            aria-hidden="true"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    d="m8 10 4 4 4-4"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>
                    </div>
                </button>

                <div
                    x-show="
                        isInventoryGroupExpanded(
                            group.key
                        )
                    "
                    x-transition.opacity.duration.120ms
                    class="inventory-v24-group-content"
                >
                    <div class="inventory-v26-group-tools">
                        <div class="inventory-v26-group-search">
                            <span
                                class="inventory-v26-group-search-icon"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <circle
                                        cx="11"
                                        cy="11"
                                        r="6.5"
                                        stroke-width="1.7"
                                    />

                                    <path
                                        d="m16 16 4 4"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <input
                                type="search"
                                x-model.debounce.120ms="
                                    inventoryGroupSearch[
                                        group.key
                                    ]
                                "
                                class="inventory-v26-group-search-input"
                                :placeholder="
                                    'Pesquisar em '
                                    + group.title.toLocaleLowerCase(
                                        'pt-BR'
                                    )
                                    + '...'
                                "
                                :aria-label="
                                    'Pesquisar em '
                                    + group.title
                                "
                            >

                            <button
                                x-show="
                                    inventoryGroupSearch[
                                        group.key
                                    ]
                                "
                                x-cloak
                                type="button"
                                @click="
                                    inventoryGroupSearch[
                                        group.key
                                    ] = ''
                                "
                                class="inventory-v26-group-search-clear"
                                title="Limpar pesquisa"
                            >
                                ×
                            </button>
                        </div>

                        <label class="inventory-v26-group-sort">
                            <span>Ordenar</span>

                            <select
                                x-model="
                                    inventoryGroupSort[
                                        group.key
                                    ]
                                "
                            >
                                <option value="default">
                                    Ordem atual
                                </option>

                                <option value="name_asc">
                                    Nome · A–Z
                                </option>

                                <option value="name_desc">
                                    Nome · Z–A
                                </option>

                                <option value="rarity_desc">
                                    Raridade · Maior
                                </option>

                                <option value="rarity_asc">
                                    Raridade · Menor
                                </option>
                            </select>
                        </label>
                    </div>

                    <div
                        x-show="group.items.length === 0"
                        x-cloak
                        class="inventory-v26-group-empty"
                    >
                        <span>◇</span>

                        <strong>
                            Nenhum item encontrado
                        </strong>

                        <button
                            type="button"
                            @click="
                                inventoryGroupSearch[
                                    group.key
                                ] = ''
                            "
                        >
                            Limpar pesquisa
                        </button>
                    </div>

                    <div
                        x-show="group.items.length > 0"
                        x-cloak
                        class="
                            inventory-v16-items-grid
                            inventory-v26-items-grid
                            inventory-v39-items-grid
                        "
                    >
                    <template x-for="item in group.items" :key="group.key + '-' + item.id">
                        <article
                            role="button"
                            tabindex="0"
                            @click="openDetail(item)"
                            @keydown.enter.prevent="openDetail(item)"
                            class="inventory-v11-item-card inventory-v16-polaroid inventory-v39-item-card cursor-pointer"
                            :class="{
                                'magical': item.is_magical,
                                'inventory-v43-item-used': singleUseItemUsed(item)
                            }"
                        >
                            <div class="flex gap-3.5 p-3.5 inventory-v39-card-main">
                                <div
                                    class="inventory-v10-thumb inventory-v27-thumb inventory-v39-card-thumb flex h-[78px] w-[78px] shrink-0 items-center justify-center rounded-xl border border-[#d8c7ab]/60"
                                >
                                    <span
                                        x-show="item.equipped"
                                        x-cloak
                                        class="inventory-v27-equipped-badge"
                                    >
                                        Equipado
                                    </span>

                                    <span
                                        x-show="singleUseItemUsed(item)"
                                        x-cloak
                                        class="inventory-v43-used-badge"
                                    >
                                        Usado
                                    </span>

                                    <template x-if="item.image_url">
                                        <img :src="item.image_url" :alt="item.name">
                                    </template>

                                    <template x-if="!item.image_url">
                                        <span class="font-serif text-[26px] text-[#8c6239]/30">◆</span>
                                    </template>
                                </div>

                                <div class="min-w-0 flex-1 inventory-v39-card-copy">
                                    <div class="flex flex-wrap items-start gap-x-2.5 gap-y-1">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4
                                                    class="font-serif text-[17px] font-black leading-tight text-[#53150f]"
                                                    x-text="item.name"
                                                ></h4>

                                            </div>

                                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                                <em
                                                    class="text-[12px] font-semibold leading-snug text-[#6b5548]"
                                                    x-text="cardClassificationLine(item)"
                                                ></em>

                                            </div>
                                        </div>

                                    </div>


                                </div>

                                <div class="hidden shrink-0 self-center text-[#8c6239]/35 sm:block">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>

                            <div
                                x-show="
                                    cardFeatureEntries(item).length > 0
                                    && !singleUseItemUsed(item)
                                "
                                x-cloak
                                class="
                                    inventory-v11-feature-strip
                                    inventory-v22-card-feature-strip
                                    inventory-v39-feature-strip
                                "
                                @click.stop
                            >
                                <div class="inventory-v22-card-feature-head">
                                    <span>Habilidades</span>

                                    <span
                                        x-show="cardFeatureEntries(item).length > 0"
                                        x-cloak
                                        class="inventory-v22-card-feature-count"
                                        x-text="cardFeatureEntries(item).length"
                                    ></span>
                                </div>

                                <div
                                    x-show="cardFeatureEntries(item).length === 0"
                                    x-cloak
                                    class="inventory-v22-card-feature-empty"
                                >
                                    Nenhuma habilidade
                                </div>

                                <div
                                    x-show="cardFeatureEntries(item).length > 0"
                                    x-cloak
                                    class="inventory-v22-card-feature-list"
                                >
                                    <template
                                        x-for="entry in cardFeatureEntries(item).slice(0, 2)"
                                        :key="'card-feature-' + item.id + '-' + entry.index"
                                    >
                                        <div class="inventory-v22-card-feature-row">
                                            <span
                                                class="inventory-v22-card-feature-name"
                                                :title="entry.feature.title || 'Habilidade'"
                                                x-text="entry.feature.title || 'Habilidade'"
                                            ></span>

                                            <template x-if="entry.feature?.usage?.enabled">
                                                <div class="inventory-v22-card-feature-tracker">
                                                    <button
                                                        x-show="
                                                            entry.feature.usage.recovery === 'single_use'
                                                            && !singleUseFeatureUsed(entry.feature)
                                                        "
                                                        x-cloak
                                                        type="button"
                                                        class="inventory-v43-single-use-action"
                                                        @click.stop="requestSingleUse(item, entry.index)"
                                                        :disabled="busyFeatureKey !== null"
                                                    >
                                                        <span>Usar</span>
                                                        <span
                                                            class="ml-1 opacity-75"
                                                            x-text="
                                                                '· '
                                                                + entry.feature.usage.current
                                                                + '/'
                                                                + entry.feature.usage.max
                                                            "
                                                        ></span>
                                                    </button>

                                                    <span
                                                        x-show="singleUseFeatureUsed(entry.feature)"
                                                        x-cloak
                                                        class="inventory-v43-used-badge !static"
                                                    >
                                                        Usado
                                                    </span>

                                                    <div
                                                        x-show="entry.feature.usage.recovery !== 'single_use'"
                                                        x-cloak
                                                        class="inventory-v11-mini-tracker"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click.stop="
                                                                changeFeatureUses(
                                                                    item,
                                                                    entry.index,
                                                                    -1
                                                                )
                                                            "
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="
                                                                entry.feature.usage.counter_mode === 'build'
                                                                    ? 'Reduzir carga acumulada'
                                                                    : 'Gastar carga'
                                                            "
                                                        >
                                                            −
                                                        </button>

                                                        <strong
                                                            x-text="
                                                                entry.feature.usage.current
                                                                + '/'
                                                                + entry.feature.usage.max
                                                            "
                                                        ></strong>

                                                        <button
                                                            type="button"
                                                            @click.stop="
                                                                changeFeatureUses(
                                                                    item,
                                                                    entry.index,
                                                                    1
                                                                )
                                                            "
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="
                                                                entry.feature.usage.counter_mode === 'build'
                                                                    ? 'Acumular carga'
                                                                    : 'Recuperar carga'
                                                            "
                                                        >
                                                            +
                                                        </button>
                                                    </div>

                                                    <span
                                                        x-show="recoveryLabel(entry.feature.usage)"
                                                        x-cloak
                                                        class="inventory-v22-card-feature-recovery"
                                                        x-text="
                                                            entry.feature.usage.recovery === 'single_use'
                                                                ? 'Uso Único'
                                                                : '/ ' + recoveryLabel(entry.feature.usage)
                                                        "
                                                    ></span>
                                                </div>
                                            </template>

                                            <template x-if="!entry.feature?.usage?.enabled">
                                                <span class="inventory-v22-card-feature-passive">
                                                    Passiva
                                                </span>
                                            </template>
                                        </div>
                                    </template>

                                    <div
                                        x-show="cardFeatureEntries(item).length > 2"
                                        x-cloak
                                        class="inventory-v22-card-feature-more"
                                    >
                                        <span
                                            x-text="
                                                '+'
                                                + (
                                                    cardFeatureEntries(item).length
                                                    - 2
                                                )
                                                + ' habilidade'
                                                + (
                                                    (
                                                        cardFeatureEntries(item).length
                                                        - 2
                                                    ) === 1
                                                        ? ''
                                                        : 's'
                                                )
                                            "
                                        ></span>

                                        <span>Ver no item</span>
                                    </div>
                                </div>
                            </div>

                            <div
                                x-show="singleUseItemUsed(item)"
                                x-cloak
                                class="inventory-v43-used-bar"
                                @click.stop
                            >
                                <div class="inventory-v43-used-copy">
                                    <strong>Item usado</strong>
                                    <span>O efeito de uso único já foi consumido.</span>
                                </div>

                                <button
                                    type="button"
                                    class="inventory-v43-discard-action"
                                    @click.stop="requestDiscardUsedItem(item)"
                                    :disabled="busyItemId !== null"
                                >
                                    Jogar fora?
                                </button>
                            </div>
                        </article>
                    </template>
                </div>
                </div>
            </section>
        </template>

        <div x-show="totalCount === 0" x-cloak class="py-16 text-center">
            <p class="font-serif text-[19px] font-black text-[#53150f]">
                Nenhum item ainda
            </p>

            <p class="mt-1 text-[12px] text-[#8c6239]">
                Crie um item ou escolha algo liberado pelo Mestre.
            </p>
        </div>
    </div>

    {{-- VISUALIZAÇÃO COMPLETA --}}
    <template x-teleport="body">
        <div
            x-show="detailOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Detalhes do item"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[230]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="closeDetail()"
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-detail-modal
                    inventory-v10-detail-paper
                    inventory-v17-detail-modal

                    relative
                    z-10

                    flex
                    max-h-[92dvh]
                    w-full
                    max-w-[860px]
                    flex-col
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
            >
                <div class="inventory-v17-detail-topbar flex shrink-0 items-center gap-2 border-b border-[#d8c7ab]/55 bg-[#efe9dc]/28 px-4 py-2.5">
                    <span class="mr-auto text-[9px] font-black uppercase tracking-[0.11em] text-[#8c6239]/75">
                        Detalhes do Item
                    </span>

                    <button
                        x-show="detailItem?.properties?.inventory?.equippable"
                        x-cloak
                        type="button"
                        @click="toggleEquipped(detailItem)"
                        :disabled="busyItemId !== null"
                        class="inventory-v15-detail-equip"
                        :class="{
                            'is-equipped':
                                detailItem?.equipped,

                            'is-stowed':
                                !detailItem?.equipped
                        }"
                        x-text="detailItem?.equipped ? 'Guardar' : 'Equipar'"
                    ></button>


                    <button
                        x-show="detailItem?.requires_attunement"
                        x-cloak
                        type="button"
                        @click="
                            detailItem?.attuned
                                ? applyAttunement(
                                    detailItem,
                                    false
                                )
                                : requestAttunement(
                                    detailItem
                                )
                        "
                        :disabled="busyItemId !== null"
                        class="inventory-v18-attunement-toggle"
                        :class="{
                            'is-attuned':
                                detailItem?.attuned,

                            'is-unattuned':
                                !detailItem?.attuned
                        }"
                        :title="
                            detailItem?.attuned
                                ? 'Remover sintonização'
                                : 'Sintonizar item'
                        "
                    >
                        <span
                            class="inventory-v18-attunement-mark"
                            x-text="
                                detailItem?.attuned
                                    ? '◆'
                                    : '◇'
                            "
                        ></span>

                        <span
                            x-text="
                                detailItem?.attuned
                                    ? 'Dessintonizar'
                                    : 'Sintonizar'
                            "
                        ></span>
                    </button>

                    <button
                        type="button"
                        @click="openEdit(detailItem)"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#6b1d14] px-3 py-2 text-[9px] font-black uppercase tracking-[0.04em] text-[#f4f1e8] transition hover:bg-[#53150f]"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.2 5.2l3.6 3.6M4 20l4.4-1 10.4-10.4a2.55 2.55 0 00-3.6-3.6L4.8 15.4 4 20z" />
                        </svg>
                        Editar
                    </button>

                    <button
                        type="button"
                        @click="closeDetail()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-xl text-[#8c6239] transition hover:bg-[#efe9dc] hover:text-[#53150f]"
                        title="Fechar"
                    >
                        ×
                    </button>
                </div>

                <div class="inventory-v17-detail-scroll min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-7 sm:py-6">
                    <template x-if="detailItem">
                        <article>
                            {{-- Cabeçalho editorial --}}
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-[158px_minmax(0,1fr)]">
                                <div
                                    class="inventory-v10-thumb flex aspect-square w-full items-center justify-center rounded-2xl border border-[#d8c7ab]/65 shadow-[0_5px_18px_rgba(83,21,15,.06)]"
                                >
                                    <template x-if="detailItem.image_url">
                                        <img :src="detailItem.image_url" :alt="detailItem.name">
                                    </template>

                                    <template x-if="!detailItem.image_url">
                                        <span class="font-serif text-[40px] text-[#8c6239]/28">◆</span>
                                    </template>
                                </div>

                                <div class="inventory-v17-detail-identity min-w-0 self-center">
                                    <div class="inventory-v17-title-row">
                                        <div class="min-w-0 flex-1">
                                            <span class="inventory-v17-item-kicker">
                                                Item do Inventário
                                            </span>

                                            <h2
                                                class="inventory-v17-item-title"
                                                x-text="detailItem.name"
                                            ></h2>
                                        </div>

                                        <span
                                            x-show="detailItem.equipped"
                                            x-cloak
                                            class="inventory-v17-status-badge is-equipped"
                                        >
                                            <span class="inventory-v17-status-dot"></span>
                                            Equipado
                                        </span>
                                    </div>

                                    <em
                                        class="inventory-v17-classification"
                                        x-text="classificationLine(detailItem)"
                                    ></em>

                                    <div
                                        x-show="singleUseItemUsed(detailItem)"
                                        x-cloak
                                        class="inventory-v43-detail-used"
                                    >
                                        <div>
                                            <strong>Este item já foi usado</strong>
                                            <p>O efeito de uso único foi consumido. Você pode manter o item como registro ou descartá-lo.</p>
                                        </div>

                                        <button
                                            type="button"
                                            class="inventory-v43-discard-action"
                                            @click="requestDiscardUsedItem(detailItem)"
                                            :disabled="busyItemId !== null"
                                        >
                                            Jogar fora
                                        </button>
                                    </div>

                                    <div
                                        x-show="
                                            detailItem.properties?.weapon
                                            || nonWeaponMechanicalLines(detailItem).length > 0
                                        "
                                        x-cloak
                                        class="inventory-v20-editorial-mechanics"
                                    >
                                        {{-- ARMA --}}
                                        <template x-if="detailItem.properties?.weapon">
                                            <div class="inventory-v20-weapon-text">
                                                <p
                                                    class="inventory-v20-weapon-line"
                                                    x-text="weaponDetailLine(detailItem)"
                                                ></p>

                                                <p
                                                    x-show="
                                                        detailItem.properties?.weapon?.traits?.length
                                                    "
                                                    x-cloak
                                                    class="inventory-v20-detail-line"
                                                >
                                                    <strong>Propriedades:</strong>

                                                    <template
                                                        x-for="
                                                            (
                                                                trait,
                                                                traitIndex
                                                            ) in (
                                                                detailItem.properties
                                                                    ?.weapon
                                                                    ?.traits
                                                                ?? []
                                                            )
                                                        "
                                                        :key="
                                                            'detail-property-'
                                                            + detailItem.id
                                                            + '-'
                                                            + (
                                                                trait.key
                                                                ?? trait.name
                                                                ?? traitIndex
                                                            )
                                                        "
                                                    >
                                                        <span class="inventory-v21-inline-tooltip">
                                                            <span
                                                                class="inventory-v21-inline-term"
                                                                tabindex="0"
                                                                x-text="trait.name || 'Propriedade'"
                                                            ></span>

                                                            <span
                                                                x-show="
                                                                    traitIndex
                                                                    < (
                                                                        detailItem.properties
                                                                            ?.weapon
                                                                            ?.traits
                                                                            ?.length
                                                                        ?? 0
                                                                    ) - 1
                                                                "
                                                                aria-hidden="true"
                                                            >, </span>

                                                            <span
                                                                class="inventory-v21-tooltip-panel"
                                                                role="tooltip"
                                                            >
                                                                <span
                                                                    class="inventory-v21-tooltip-title"
                                                                    x-text="trait.name || 'Propriedade'"
                                                                ></span>

                                                                <span
                                                                    class="inventory-v21-tooltip-text"
                                                                    x-text="
                                                                        trait.description
                                                                        || 'Sem descrição adicional.'
                                                                    "
                                                                ></span>
                                                            </span>
                                                        </span>
                                                    </template>
                                                </p>

                                                <p
                                                    x-show="
                                                        detailItem.properties?.weapon?.masteries?.length
                                                    "
                                                    x-cloak
                                                    class="inventory-v20-detail-line"
                                                >
                                                    <strong>Maestrias:</strong>

                                                    <template
                                                        x-for="
                                                            (
                                                                mastery,
                                                                masteryIndex
                                                            ) in (
                                                                detailItem.properties
                                                                    ?.weapon
                                                                    ?.masteries
                                                                ?? []
                                                            )
                                                        "
                                                        :key="
                                                            'detail-mastery-'
                                                            + detailItem.id
                                                            + '-'
                                                            + (
                                                                mastery.key
                                                                ?? mastery.name
                                                                ?? masteryIndex
                                                            )
                                                        "
                                                    >
                                                        <span class="inventory-v21-inline-tooltip">
                                                            <span
                                                                class="inventory-v21-inline-term"
                                                                tabindex="0"
                                                            >
                                                                (<span
                                                                    x-text="
                                                                        mastery.name
                                                                        || 'Maestria'
                                                                    "
                                                                ></span>)
                                                            </span>

                                                            <span
                                                                x-show="
                                                                    masteryIndex
                                                                    < (
                                                                        detailItem.properties
                                                                            ?.weapon
                                                                            ?.masteries
                                                                            ?.length
                                                                        ?? 0
                                                                    ) - 1
                                                                "
                                                                aria-hidden="true"
                                                            > </span>

                                                            <span
                                                                class="inventory-v21-tooltip-panel"
                                                                role="tooltip"
                                                            >
                                                                <span
                                                                    class="inventory-v21-tooltip-title"
                                                                    x-text="
                                                                        mastery.name
                                                                        || 'Maestria'
                                                                    "
                                                                ></span>

                                                                <span
                                                                    class="inventory-v21-tooltip-text"
                                                                    x-text="
                                                                        mastery.description
                                                                        || 'Sem descrição adicional.'
                                                                    "
                                                                ></span>
                                                            </span>
                                                        </span>
                                                    </template>
                                                </p>
                                            </div>
                                        </template>

                                        {{-- ARMADURA / ESCUDO / CUSTOM --}}
                                        <template
                                            x-for="(line, lineIndex) in nonWeaponMechanicalLines(detailItem)"
                                            :key="'detail-other-line-' + lineIndex"
                                        >
                                            <p
                                                class="inventory-v20-detail-line"
                                                :class="{
                                                    'mt-1':
                                                        lineIndex > 0
                                                }"
                                                x-text="line"
                                            ></p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Descrição --}}
                            <section
                                x-show="detailItem.description"
                                x-cloak
                                class="inventory-v17-detail-section mt-7"
                            >
                                <div class="inventory-v11-detail-section-title">
                                    Descrição
                                </div>

                                <p
                                    class="whitespace-pre-line text-[14px] leading-[1.78] text-[#4f3427]"
                                    x-text="detailItem.description"
                                ></p>
                            </section>

                            {{-- Propriedades custom --}}
                            <section
                                x-show="detailItem.properties?.custom_properties?.length"
                                x-cloak
                                class="inventory-v17-detail-section mt-7"
                            >
                                <div class="inventory-v11-detail-section-title">
                                    Propriedades
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <template
                                        x-for="(entry, index) in detailItem.properties.custom_properties"
                                        :key="'detail-custom-' + index"
                                    >
                                        <div class="rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/22 px-3.5 py-3">
                                            <div class="flex flex-wrap items-baseline gap-2">
                                                <h3
                                                    class="font-serif text-[16px] font-black text-[#53150f]"
                                                    x-text="entry.title"
                                                ></h3>

                                                <strong
                                                    x-show="entry.value"
                                                    class="text-[12px] text-[#8c6239]"
                                                    x-text="entry.value"
                                                ></strong>
                                            </div>

                                            <p
                                                x-show="entry.description"
                                                class="mt-1.5 whitespace-pre-line text-[12px] leading-[1.65] text-[#5f3a27]"
                                                x-text="entry.description"
                                            ></p>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- Habilidades --}}
                            <section
                                x-show="detailItem.properties?.features?.length"
                                x-cloak
                                class="inventory-v17-detail-section mt-7"
                            >
                                <div class="inventory-v11-detail-section-title">
                                    Habilidades
                                </div>

                                <div class="space-y-3">
                                    <template
                                        x-for="(feature, featureIndex) in detailItem.properties.features"
                                        :key="'detail-feature-' + featureIndex"
                                    >
                                        <div class="inventory-v11-ability-card">
                                            <div class="inventory-v11-ability-header">
                                                <h3
                                                    class="min-w-0 flex-1 font-serif text-[17px] font-black leading-tight text-[#53150f]"
                                                    x-text="feature.title || 'Habilidade'"
                                                ></h3>

                                                <div
                                                    x-show="feature.usage?.enabled"
                                                    x-cloak
                                                    class="flex shrink-0 flex-wrap items-center justify-end gap-2"
                                                >
                                                    <button
                                                        x-show="
                                                            feature.usage.recovery === 'single_use'
                                                            && !singleUseFeatureUsed(feature)
                                                        "
                                                        x-cloak
                                                        type="button"
                                                        class="inventory-v43-single-use-action"
                                                        @click="requestSingleUse(detailItem, featureIndex)"
                                                        :disabled="busyFeatureKey !== null"
                                                    >
                                                        <span>Usar item</span>
                                                        <span
                                                            class="ml-1 opacity-75"
                                                            x-text="
                                                                '· '
                                                                + feature.usage.current
                                                                + '/'
                                                                + feature.usage.max
                                                            "
                                                        ></span>
                                                    </button>

                                                    <span
                                                        x-show="
                                                            feature.usage.recovery === 'single_use'
                                                            && singleUseFeatureUsed(feature)
                                                        "
                                                        x-cloak
                                                        class="inventory-v43-used-badge !static"
                                                    >
                                                        Usado
                                                    </span>

                                                    <div
                                                        x-show="feature.usage.recovery !== 'single_use'"
                                                        x-cloak
                                                        class="inventory-v10-tracker"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click="changeFeatureUses(detailItem, featureIndex, -1)"
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Reduzir carga acumulada'
                                                                    : 'Gastar carga'
                                                            "
                                                        >
                                                            −
                                                        </button>

                                                        <strong
                                                            x-text="feature.usage.current + '/' + feature.usage.max"
                                                        ></strong>

                                                        <button
                                                            type="button"
                                                            @click="changeFeatureUses(detailItem, featureIndex, 1)"
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Acumular carga'
                                                                    : 'Recuperar carga'
                                                            "
                                                        >
                                                            +
                                                        </button>
                                                    </div>

                                                    <span
                                                        class="inventory-v11-recovery-pill"
                                                        x-text="
                                                            feature.usage.recovery === 'single_use'
                                                                ? 'Uso Único'
                                                                : '/ ' + recoveryLabel(feature.usage)
                                                        "
                                                    ></span>
                                                </div>
                                            </div>

                                            <div class="inventory-v11-ability-body">
                                                <p
                                                    x-show="feature.description"
                                                    class="whitespace-pre-line text-[13px] leading-[1.72] text-[#4f3427]"
                                                    x-text="feature.description"
                                                ></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            <section
                                x-show="detailItem.is_cursed"
                                x-cloak
                                class="mt-7 rounded-r-xl border-l-2 border-red-700 bg-red-50/60 px-3.5 py-3"
                            >
                                <p class="text-[10px] font-black uppercase tracking-[0.08em] text-red-700">
                                    Maldição Revelada
                                </p>

                                <p
                                    class="mt-1 whitespace-pre-line text-[13px] leading-relaxed text-red-900"
                                    x-text="detailItem.curse_description"
                                ></p>
                            </section>
                        </article>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- =========================================================
         CONFIRMAÇÃO — USO ÚNICO
    ========================================================== --}}
    <template x-teleport="body">
        <div
            x-show="singleUseConfirmOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[270] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Confirmar uso do item"
        >
            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="cancelSingleUse()"
            ></div>

            <div
                class="character-theme-modal relative z-10 w-full max-w-[430px] overflow-hidden rounded-2xl border shadow-2xl"
            >
                <div class="border-b border-[#d8c7ab]/55 bg-[#efe9dc]/38 px-5 py-4">
                    <span class="text-[9px] font-black uppercase tracking-[0.11em] text-[#8c6239]">
                        Uso permanente
                    </span>
                    <h4 class="mt-1 font-serif text-[20px] font-black text-[#53150f]">
                        Gastar 1 carga?
                    </h4>
                </div>

                <div class="px-5 py-5">
                    <p class="text-[13px] leading-[1.7] text-[#5f4638]">
                        Você está prestes a gastar 1 carga de
                        <strong x-text="pendingSingleUse?.itemName ?? 'este item'"></strong>.
                    </p>

                    <div
                        class="mt-3 rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/30 px-4 py-3"
                    >
                        <span class="text-[9px] font-black uppercase tracking-[0.08em] text-[#8c6239]">
                            Cargas
                        </span>

                        <strong
                            class="mt-1 block font-serif text-[18px] font-black text-[#53150f]"
                            x-text="
                                (pendingSingleUse?.current ?? 0)
                                + '/'
                                + (pendingSingleUse?.max ?? 1)
                                + ' → '
                                + (pendingSingleUse?.remainingAfterUse ?? 0)
                                + '/'
                                + (pendingSingleUse?.max ?? 1)
                            "
                        ></strong>
                    </div>

                    <p
                        x-show="!pendingSingleUse?.isLastCharge"
                        x-cloak
                        class="mt-3 text-[12px] leading-[1.65] text-[#7c6657]"
                    >
                        Essa carga é consumida permanentemente e não poderá ser recuperada pelo contador.
                    </p>

                    <p
                        x-show="
                            pendingSingleUse?.isLastCharge
                            && pendingSingleUse?.marksWholeItem
                        "
                        x-cloak
                        class="mt-3 text-[12px] leading-[1.65] text-[#7c6657]"
                    >
                        Esta é a última carga. Depois da confirmação, o item será marcado como <strong>Usado</strong> e poderá ser descartado.
                    </p>

                    <p
                        x-show="
                            pendingSingleUse?.isLastCharge
                            && !pendingSingleUse?.marksWholeItem
                        "
                        x-cloak
                        class="mt-3 text-[12px] leading-[1.65] text-[#7c6657]"
                    >
                        Esta é a última carga desta habilidade. Depois da confirmação, ela ficará esgotada permanentemente.
                    </p>
                </div>

                <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/55 bg-[#efe9dc]/20 px-5 py-3.5">
                    <button
                        type="button"
                        @click="cancelSingleUse()"
                        :disabled="busyFeatureKey !== null"
                        class="rounded-lg px-3.5 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] text-[#8c6239] disabled:opacity-50"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        @click="confirmSingleUse()"
                        :disabled="busyFeatureKey !== null"
                        class="rounded-lg bg-[#6b1d14] px-4 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] text-[#faf8f2] hover:bg-[#53150f] disabled:opacity-50"
                    >
                        <span x-text="busyFeatureKey !== null ? 'Gastando...' : 'Gastar 1 carga'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>


    {{-- =========================================================
         CONFIRMAÇÃO — DESCARTAR ITEM USADO
    ========================================================== --}}
    <template x-teleport="body">
        <div
            x-show="discardUsedConfirmOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[271] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Descartar item usado"
        >
            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="cancelDiscardUsedItem()"
            ></div>

            <div
                class="character-theme-modal relative z-10 w-full max-w-[410px] overflow-hidden rounded-2xl border shadow-2xl"
            >
                <div class="border-b border-[#d8c7ab]/55 bg-[#efe9dc]/38 px-5 py-4">
                    <span class="text-[9px] font-black uppercase tracking-[0.11em] text-[#8c6239]">
                        Item usado
                    </span>
                    <h4 class="mt-1 font-serif text-[20px] font-black text-[#53150f]">
                        Jogar fora?
                    </h4>
                </div>

                <div class="px-5 py-5">
                    <p class="text-[13px] leading-[1.7] text-[#5f4638]">
                        <strong x-text="pendingDiscardItem?.name ?? 'Este item'"></strong>
                        já foi consumido. Ao jogar fora, ele será removido permanentemente do inventário.
                    </p>
                </div>

                <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/55 bg-[#efe9dc]/20 px-5 py-3.5">
                    <button
                        type="button"
                        @click="cancelDiscardUsedItem()"
                        :disabled="busyItemId !== null"
                        class="rounded-lg px-3.5 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] text-[#8c6239] disabled:opacity-50"
                    >
                        Manter
                    </button>

                    <button
                        type="button"
                        @click="confirmDiscardUsedItem()"
                        :disabled="busyItemId !== null"
                        class="rounded-lg bg-red-700 px-4 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] text-white hover:bg-red-800 disabled:opacity-50"
                    >
                        <span x-text="busyItemId !== null ? 'Descartando...' : 'Jogar fora'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>


    {{-- EDITOR --}}
    <template x-teleport="body">
        <div
            x-show="editorOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Editor de item"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[240]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="closeEditor()"
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-editor-modal

                    relative
                    z-10

                    flex
                    max-h-[94dvh]
                    w-full
                    max-w-[960px]
                    flex-col
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
            >
                <div class="flex shrink-0 items-center justify-between border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-5 py-3.5">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Item</p>
                        <h3 class="font-serif text-[21px] font-black text-[#53150f]" x-text="editingId ? 'Editar Item' : 'Novo Item'"></h3>
                    </div>

                    <button type="button" @click="closeEditor()" class="flex h-9 w-9 items-center justify-center rounded-lg text-xl text-[#8c6239] hover:bg-[#e8dfd1]">×</button>
                </div>

                <div
                    x-show="form"
                    @scroll="hideEditorTooltip()"
                    class="
                        inventory-v32-editor-scroll
                        min-h-0
                        flex-1
                        overflow-y-auto
                        overflow-x-hidden
                        px-5
                        py-5
                        sm:px-6
                    "
                >
                    <section>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-[160px_minmax(0,1fr)]">
                            <div>
                                <label class="inventory-v10-thumb relative flex aspect-square w-full cursor-pointer items-center justify-center rounded-xl border border-dashed border-[#cdbb9f] bg-[#efe9dc]/30">
                                    <template x-if="imagePreview">
                                        <img :src="imagePreview" alt="Prévia do item">
                                    </template>
                                    <template x-if="!imagePreview">
                                        <div class="text-center">
                                            <svg class="mx-auto h-7 w-7 text-[#8c6239]/45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 5h16v14H4zM8 13l2.5-2.5L14 14l2-2 4 4M8 9h.01" />
                                            </svg>
                                            <span class="mt-2 block text-[10px] font-black uppercase text-[#8c6239]">Adicionar Foto</span>
                                        </div>
                                    </template>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        @click="$event.target.value = null"
                                        @change="handleImage($event)"
                                        class="hidden"
                                    >
                                </label>

                                <button
                                    x-show="imagePreview"
                                    x-cloak
                                    type="button"
                                    @click="removeImage()"
                                    class="mt-2 w-full text-[9px] font-black uppercase text-red-700 hover:underline"
                                >
                                    Remover Foto
                                </button>
                            </div>

                            <div class="space-y-4">
                                <label class="block">
                                    <span class="text-[12px] font-black text-[#53150f]">Nome</span>
                                    <input type="text" maxlength="120" x-model="form.name" placeholder="Ex.: Machado Lampejo Real" class="inventory-v10-input mt-1.5">
                                </label>

                                <div>
                                    <div class="mb-1.5 flex items-center justify-between gap-3">
                                        <span class="text-[12px] font-black text-[#53150f]">É equipável?</span>
                                        <span class="text-[10px] text-[#8c6239]/70">Permite Equipar / Guardar.</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 sm:max-w-xs">
                                        <button type="button" @click="setEquippable(true)" class="inventory-v10-choice" :class="{ 'active': form.properties.inventory.equippable }">Sim</button>
                                        <button type="button" @click="setEquippable(false)" class="inventory-v10-choice" :class="{ 'active': !form.properties.inventory.equippable }">Não</button>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[12px] font-black text-[#53150f]">Natureza do item</span>
                                    <div class="mt-1.5 grid grid-cols-1 gap-2 sm:grid-cols-3">
                                        <template x-for="(label, key) in natureLabels" :key="key">
                                            <button
                                                type="button"
                                                @click="setNature(key)"
                                                class="inventory-v10-choice"
                                                :class="{ 'active': form.properties.nature === key }"
                                                x-text="label"
                                            ></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_220px]">
                                    <label>
                                        <span class="text-[12px] font-black text-[#53150f]">Raridade</span>
                                        <select x-model="form.rarity" class="inventory-v10-select mt-1.5">
                                            <template x-for="(label, key) in rarityLabels" :key="key">
                                                <option :value="key" x-text="label"></option>
                                            </template>
                                        </select>
                                    </label>

                                    <div x-show="form.properties.nature === 'wonderful'" x-cloak>
                                        <span class="text-[12px] font-black text-[#53150f]">Sintonização</span>
                                        <button
                                            type="button"
                                            @click="form.requires_attunement = !form.requires_attunement; if (!form.requires_attunement) form.attuned = false"
                                            class="mt-1.5 flex min-h-[44px] w-full items-center justify-between gap-3 rounded-lg border px-3"
                                            :class="form.requires_attunement ? 'border-[#6b1d14]/45 bg-[#6b1d14]/[0.06]' : 'border-[#cdbb9f]/75 bg-white/70'"
                                        >
                                            <span class="text-[11px] font-black text-[#53150f]">Sintonizável</span>
                                            <span class="relative h-5 w-9 rounded-full" :class="form.requires_attunement ? 'bg-[#6b1d14]' : 'bg-[#cdbb9f]'">
                                                <span class="absolute top-0.5 h-4 w-4 rounded-full bg-white shadow transition-all" :class="form.requires_attunement ? 'left-[18px]' : 'left-0.5'"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="mt-6 border-t border-[#d8c7ab]/60 pt-5">
                        <h4 class="font-serif text-[18px] font-black text-[#53150f]">Aparência e Descrição</h4>
                        <p class="mt-0.5 text-[11px] text-[#8c6239]/70">O texto principal que será mostrado na ficha do item.</p>
                        <textarea
                            rows="6"
                            x-model="form.description"
                            placeholder="Descreva a aparência, origem, sensação e funcionamento geral do item..."
                            class="inventory-v10-textarea mt-3"
                        ></textarea>
                    </section>

                    {{-- =====================================================
                         PROPRIEDADES MODULARES
                    ====================================================== --}}
                    <section class="mt-7 border-t border-[#d8c7ab]/60 pt-5">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h4 class="font-serif text-[18px] font-black text-[#53150f]">Propriedades do Item</h4>
                                <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/72">
                                    Adicione apenas o que o sistema precisa entender. O restante continua livre na descrição e nas habilidades.
                                </p>
                            </div>

                            <div class="relative">
                                <button
                                    type="button"
                                    @click="modulePickerOpen = !modulePickerOpen"
                                    class="inline-flex min-h-[40px] items-center gap-2 rounded-lg border border-[#6b1d14]/30 bg-[#6b1d14]/[0.05] px-3.5 text-[10px] font-black uppercase tracking-[0.06em] text-[#6b1d14] transition hover:bg-[#6b1d14]/[0.10]"
                                >
                                    <span class="text-[15px] leading-none">+</span>
                                    Adicionar Propriedade
                                </button>

                                <div
                                    x-show="modulePickerOpen"
                                    x-cloak
                                    @click.outside="modulePickerOpen = false"
                                    class="absolute right-0 top-[46px] z-40 w-[min(88vw,330px)] overflow-hidden rounded-xl border border-[#cdbb9f] bg-[#faf8f2] p-2 shadow-xl"
                                >
                                    <button
                                        x-show="!hasModule('weapon')"
                                        x-cloak
                                        type="button"
                                        @click="addModule('weapon')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">◆</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Arma</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Dano, atributo, propriedades e maestrias.</span>
                                        </span>
                                    </button>

                                    <button
                                        x-show="!hasModule('armor')"
                                        x-cloak
                                        type="button"
                                        @click="addModule('armor')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">◆</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Armadura</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Categoria, tipo e Classe de Armadura.</span>
                                        </span>
                                    </button>

                                    <button
                                        x-show="!hasModule('shield')"
                                        x-cloak
                                        type="button"
                                        @click="addModule('shield')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">◆</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Escudo</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Bônus de CA e bônus adicional.</span>
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="addModule('custom')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">+</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Custom</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Uma propriedade livre com título, valor e explicação.</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>


                        {{-- ARMA --}}
                        <div
                            x-show="hasModule('weapon')"
                            x-cloak
                            class="inventory-v10-module inventory-v31-weapon-module mt-4"
                        >
                            <div class="flex items-start justify-between gap-4 border-b border-[#d8c7ab]/55 bg-[#efe9dc]/42 px-4 py-3">
                                <div>
                                    <h5 class="font-serif text-[17px] font-black text-[#53150f]">Arma</h5>
                                    <p class="mt-0.5 text-[10px] text-[#8c6239]/72">Configuração mecânica curta para ataques e exibição na ficha.</p>
                                </div>
                                <button type="button" @click="removeModule('weapon')" class="rounded-md px-2 py-1 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                            </div>

                            <div class="space-y-5 p-4">
                                <div>
                                    <span class="text-[11px] font-black text-[#53150f]">Tipo de Arma</span>
                                    <div class="mt-2 grid grid-cols-3 gap-2 sm:max-w-lg">
                                        <button type="button" @click="form.properties.weapon.category = 'simple'" class="inventory-v10-choice" :class="{ 'active': form.properties.weapon.category === 'simple' }">Simples</button>
                                        <button type="button" @click="form.properties.weapon.category = 'martial'" class="inventory-v10-choice" :class="{ 'active': form.properties.weapon.category === 'martial' }">Marcial</button>
                                        <button type="button" @click="form.properties.weapon.category = 'custom'" class="inventory-v10-choice" :class="{ 'active': form.properties.weapon.category === 'custom' }">Custom</button>
                                    </div>

                                    <input
                                        x-show="form.properties.weapon.category === 'custom'"
                                        x-cloak
                                        type="text"
                                        maxlength="120"
                                        x-model="form.properties.weapon.category_custom"
                                        placeholder="Ex.: Arma Exótica"
                                        class="inventory-v10-input mt-2 sm:max-w-lg"
                                    >
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_150px]">
                                    <label>
                                        <span class="text-[11px] font-black text-[#53150f]">Modelo / Tipo</span>
                                        <input type="text" maxlength="120" x-model="form.properties.weapon.weapon_type" placeholder="Ex.: Faca, Machado, Cajado, Foice Grande..." class="inventory-v10-input mt-1.5">
                                    </label>

                                    <label>
                                        <span class="text-[11px] font-black text-[#53150f]">Bônus</span>
                                        <input type="number" min="-99" max="99" x-model.number="form.properties.weapon.magic_bonus" class="inventory-v10-input mt-1.5 text-center">
                                    </label>
                                </div>

                                <div>
                                    <div class="flex items-end justify-between gap-3">
                                        <div>
                                            <span class="text-[11px] font-black text-[#53150f]">Propriedades da Arma</span>
                                            <p class="mt-0.5 text-[10px] text-[#8c6239]/70">Passe o mouse sobre cada propriedade para ler a regra completa.</p>
                                        </div>
                                    </div>

                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <template x-for="definition in weaponTraitDefinitions" :key="definition.key">
                                            <button
                                                type="button"
                                                @click="toggleTrait(definition)"
                                                @mouseenter="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @mouseleave="hideEditorTooltip()"
                                                @focus="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @blur="hideEditorTooltip()"
                                                class="inventory-v10-chip"
                                                :class="{ 'active': traitSelected(definition.key) }"
                                            >
                                                <span x-text="definition.name"></span>
                                            </button>
                                        </template>

                                        <button type="button" @click="addCustomTrait()" class="inventory-v10-chip">+ Custom</button>
                                    </div>

                                    <div class="mt-3 space-y-2">
                                        <template x-for="(trait, index) in form.properties.weapon.traits" :key="trait.key">
                                            <div x-show="trait.custom" x-cloak class="grid grid-cols-1 gap-2 rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/24 p-3 sm:grid-cols-[180px_minmax(0,1fr)_auto]">
                                                <input type="text" maxlength="120" x-model="trait.name" placeholder="Nome da propriedade" class="inventory-v10-input">
                                                <input type="text" maxlength="2000" x-model="trait.description" placeholder="O que esta propriedade faz?" class="inventory-v10-input">
                                                <button type="button" @click="removeTrait(index)" class="rounded-md px-2 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[11px] font-black text-[#53150f]">Dano</span>
                                    <div class="mt-2 grid grid-cols-2 gap-2 md:grid-cols-[86px_105px_minmax(0,1fr)_minmax(0,1fr)]">
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Dados</span>
                                            <input type="number" min="1" max="99" x-model.number="form.properties.weapon.damage.count" class="inventory-v10-input mt-1 text-center">
                                        </label>
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Dado</span>
                                            <input type="text" maxlength="20" x-model="form.properties.weapon.damage.die" placeholder="d4" class="inventory-v10-input mt-1 text-center">
                                        </label>
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Atributo</span>
                                            <select x-model="form.properties.weapon.damage.ability" class="inventory-v10-select mt-1">
                                                <template x-for="(label, key) in abilityLabels" :key="key">
                                                    <option :value="key" x-text="label"></option>
                                                </template>
                                            </select>
                                        </label>
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Tipo de Dano</span>
                                            <input type="text" maxlength="80" x-model="form.properties.weapon.damage.type" placeholder="Ex.: Perfurante" class="inventory-v10-input mt-1">
                                        </label>
                                    </div>

                                    <input
                                        x-show="form.properties.weapon.damage.ability === 'custom'"
                                        x-cloak
                                        type="text"
                                        maxlength="80"
                                        x-model="form.properties.weapon.damage.ability_custom"
                                        placeholder="Atributo ou modificador custom"
                                        class="inventory-v10-input mt-2 sm:max-w-md"
                                    >
                                </div>

                                <div>
                                    <div class="flex items-end justify-between gap-3 border-b border-[#d8c7ab]/45 pb-2">
                                        <div>
                                            <span class="text-[11px] font-black text-[#53150f]">Danos Extras</span>
                                            <p class="mt-0.5 text-[10px] text-[#8c6239]/70">Ex.: 2d6 Elétrico além do dano principal.</p>
                                        </div>
                                        <button type="button" @click="addExtraDamage()" class="text-[9px] font-black uppercase text-[#6b1d14] hover:underline">+ Dano Extra</button>
                                    </div>

                                    <div class="mt-2 space-y-2">
                                        <template x-for="(part, index) in form.properties.weapon.extra_damage" :key="'extra-damage-' + index">
                                            <div class="grid grid-cols-[72px_96px_minmax(0,1fr)_auto] gap-2">
                                                <input type="number" min="1" max="99" x-model.number="part.count" class="inventory-v10-input text-center">
                                                <input type="text" maxlength="20" x-model="part.die" placeholder="d6" class="inventory-v10-input text-center">
                                                <input type="text" maxlength="80" x-model="part.type" placeholder="Tipo de dano" class="inventory-v10-input">
                                                <button type="button" @click="removeExtraDamage(index)" class="rounded-md px-2 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <div>
                                        <span class="text-[11px] font-black text-[#53150f]">Maestrias</span>
                                        <p class="mt-0.5 text-[10px] text-[#8c6239]/70">Pode selecionar mais de uma. Passe o mouse sobre cada maestria para ler a regra completa.</p>
                                    </div>

                                    <div class="mt-2 grid grid-cols-2 gap-2 md:grid-cols-4">
                                        <template x-for="definition in masteryDefinitions" :key="definition.key">
                                            <button
                                                type="button"
                                                @click="toggleMastery(definition)"
                                                @mouseenter="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @mouseleave="hideEditorTooltip()"
                                                @focus="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @blur="hideEditorTooltip()"
                                                class="rounded-xl border px-3 py-2.5 text-left transition"
                                                :class="masterySelected(definition.key) ? 'border-[#6b1d14]/45 bg-[#6b1d14]/[0.07]' : 'border-[#d8c7ab]/65 bg-white/55 hover:bg-[#efe9dc]/55'"
                                            >
                                                <strong class="block text-[11px] text-[#53150f]" x-text="definition.name"></strong>
                                                <span class="mt-0.5 block line-clamp-2 text-[9px] leading-relaxed text-[#8c6239]" x-text="definition.description"></span>
                                            </button>
                                        </template>
                                    </div>

                                    <button type="button" @click="addCustomMastery()" class="mt-2 text-[9px] font-black uppercase text-[#6b1d14] hover:underline">+ Maestria Custom</button>

                                    <div class="mt-3 space-y-2">
                                        <template x-for="(mastery, index) in form.properties.weapon.masteries" :key="mastery.key">
                                            <div x-show="mastery.custom" x-cloak class="rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/24 p-3">
                                                <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-2">
                                                    <input type="text" maxlength="120" x-model="mastery.name" placeholder="Nome da maestria" class="inventory-v10-input">
                                                    <button type="button" @click="removeMastery(index)" class="rounded-md px-2 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                                </div>
                                                <textarea rows="2" maxlength="5000" x-model="mastery.description" placeholder="O que esta maestria faz?" class="inventory-v10-textarea mt-2"></textarea>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- ARMADURA --}}
                        <div
                            x-show="hasModule('armor')"
                            x-cloak
                            class="
                                inventory-v10-module
                                inventory-v34-defense-module
                                mt-4
                                overflow-hidden
                            "
                        >
                            {{-- Cabeçalho --}}
                            <div class="inventory-v34-defense-head">
                                <div class="inventory-v34-defense-head-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M8.2 5.2 12 3l3.8 2.2 3.2 1.4-1.3 4.1v8.1H6.3v-8.1L5 6.6l3.2-1.4Z"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M8.2 5.2 9.4 8h5.2l1.2-2.8"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h5 class="font-serif text-[18px] font-black text-[#53150f]">
                                            Armadura
                                        </h5>

                                        <span class="inventory-v34-auto-equip">
                                            Equipável
                                        </span>
                                    </div>

                                    <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/78">
                                        Defina aqui a fórmula da armadura. Quando o item estiver equipado,
                                        a Classe de Armadura poderá usar estes dados diretamente.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="removeModule('armor')"
                                    class="inventory-v34-remove"
                                >
                                    Remover
                                </button>
                            </div>

                            <div class="space-y-5 p-4 sm:p-5">
                                {{-- Categoria --}}
                                <section>
                                    <div class="inventory-v34-section-label">
                                        <span>01</span>

                                        <div>
                                            <strong>Categoria da Armadura</strong>
                                            <p>
                                                A categoria já configura uma regra defensiva padrão.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-2 gap-2 lg:grid-cols-4">
                                        <button
                                            type="button"
                                            @click="setArmorCategory('light')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'light'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                L
                                            </span>

                                            <strong>Leve</strong>

                                            <small>
                                                Destreza completa
                                            </small>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorCategory('medium')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'medium'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                M
                                            </span>

                                            <strong>Média</strong>

                                            <small>
                                                DES até +2
                                            </small>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorCategory('heavy')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'heavy'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                P
                                            </span>

                                            <strong>Pesada</strong>

                                            <small>
                                                Não soma DES
                                            </small>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorCategory('custom')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'custom'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                ◆
                                            </span>

                                            <strong>Especial</strong>

                                            <small>
                                                Fórmula livre
                                            </small>
                                        </button>
                                    </div>

                                    <input
                                        x-show="form.properties.armor.category === 'custom'"
                                        x-cloak
                                        type="text"
                                        maxlength="120"
                                        x-model="form.properties.armor.category_custom"
                                        placeholder="Ex.: Armadura Astral"
                                        class="inventory-v10-input mt-2"
                                    >
                                </section>

                                {{-- Identidade / números --}}
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>02</span>

                                        <div>
                                            <strong>Defesa do Item</strong>
                                            <p>
                                                Base da armadura e bônus mágico próprio do equipamento.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="
                                            mt-3
                                            grid
                                            grid-cols-1
                                            gap-3
                                            sm:grid-cols-[minmax(0,1fr)_128px_128px]
                                        "
                                    >
                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Tipo / Modelo
                                            </span>

                                            <input
                                                type="text"
                                                maxlength="120"
                                                x-model="form.properties.armor.armor_type"
                                                placeholder="Ex.: Meia Armadura"
                                                class="inventory-v10-input mt-1.5"
                                            >
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                CA Base
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="99"
                                                    x-model.number="form.properties.armor.base_ac"
                                                >
                                            </div>
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Bônus Mágico
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <span>+</span>

                                                <input
                                                    type="number"
                                                    min="-99"
                                                    max="99"
                                                    x-model.number="form.properties.armor.magic_bonus"
                                                >
                                            </div>
                                        </label>
                                    </div>
                                </section>

                                {{-- Destreza --}}
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>03</span>

                                        <div>
                                            <strong>Destreza na CA</strong>
                                            <p>
                                                Leve usa tudo, Média normalmente limita e Pesada não usa.
                                                Armaduras especiais podem quebrar essa regra.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-3 gap-2">
                                        <button
                                            type="button"
                                            @click="setArmorDexterityMode('none')"
                                            class="inventory-v34-rule-choice"
                                            :class="{
                                                'active':
                                                    form.properties.armor.dexterity_mode === 'none'
                                            }"
                                        >
                                            <strong>Sem DES</strong>
                                            <span>Não adiciona Destreza.</span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityMode('full')"
                                            class="inventory-v34-rule-choice"
                                            :class="{
                                                'active':
                                                    form.properties.armor.dexterity_mode === 'full'
                                            }"
                                        >
                                            <strong>DES completa</strong>
                                            <span>Usa o modificador inteiro.</span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityMode('capped')"
                                            class="inventory-v34-rule-choice"
                                            :class="{
                                                'active':
                                                    form.properties.armor.dexterity_mode === 'capped'
                                            }"
                                        >
                                            <strong>DES limitada</strong>
                                            <span>Usa somente até um limite.</span>
                                        </button>
                                    </div>

                                    <div
                                        x-show="form.properties.armor.dexterity_mode === 'capped'"
                                        x-cloak
                                        class="inventory-v34-cap-row"
                                    >
                                        <span class="inventory-v34-field-label">
                                            Limite
                                        </span>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityCap(2)"
                                            class="inventory-v34-cap-button"
                                            :class="{
                                                'active':
                                                    parseInt(form.properties.armor.dexterity_cap) === 2
                                            }"
                                        >
                                            +2
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityCap(3)"
                                            class="inventory-v34-cap-button"
                                            :class="{
                                                'active':
                                                    parseInt(form.properties.armor.dexterity_cap) === 3
                                            }"
                                        >
                                            +3
                                        </button>

                                        <label class="inventory-v34-cap-custom">
                                            <span>Outro</span>

                                            <input
                                                type="number"
                                                min="0"
                                                max="99"
                                                x-model.number="form.properties.armor.dexterity_cap"
                                            >
                                        </label>
                                    </div>
                                </section>

                                {{-- Atributos extras --}}
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>04</span>

                                        <div>
                                            <strong>Atributos Adicionais</strong>
                                            <p>
                                                Opcional. Ideal para armaduras mágicas ou especiais que
                                                também somam outro modificador à CA.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-5 gap-2">
                                        <template
                                            x-for="ability in [
                                                'strength',
                                                'constitution',
                                                'intelligence',
                                                'wisdom',
                                                'charisma'
                                            ]"
                                            :key="'armor-extra-ability-' + ability"
                                        >
                                            <button
                                                type="button"
                                                @click="toggleArmorAbilityModifier(ability)"
                                                class="inventory-v34-ability"
                                                :class="{
                                                    'active':
                                                        form.properties.armor.ability_modifiers.includes(
                                                            ability
                                                        )
                                                }"
                                            >
                                                <strong
                                                    x-text="
                                                        {
                                                            strength: 'FOR',
                                                            constitution: 'CON',
                                                            intelligence: 'INT',
                                                            wisdom: 'SAB',
                                                            charisma: 'CAR'
                                                        }[ability]
                                                    "
                                                ></strong>

                                                <span
                                                    x-text="
                                                        {
                                                            strength: 'Força',
                                                            constitution: 'Constituição',
                                                            intelligence: 'Inteligência',
                                                            wisdom: 'Sabedoria',
                                                            charisma: 'Carisma'
                                                        }[ability]
                                                    "
                                                ></span>
                                            </button>
                                        </template>
                                    </div>
                                </section>

                                {{-- Fórmula --}}
                                <section class="inventory-v34-formula-card">
                                    <div class="inventory-v34-formula-icon">
                                        Σ
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <span>
                                            Fórmula da Armadura
                                        </span>

                                        <strong
                                            x-text="
                                                armorFormulaText(
                                                    form.properties.armor
                                                )
                                            "
                                        ></strong>

                                        <small>
                                            Esta fórmula pertence ao item e poderá ser usada
                                            automaticamente quando ele estiver equipado.
                                        </small>
                                    </div>
                                </section>
                            </div>
                        </div>


                        {{-- ESCUDO --}}
                        <div
                            x-show="hasModule('shield')"
                            x-cloak
                            class="
                                inventory-v10-module
                                inventory-v34-defense-module
                                mt-4
                                overflow-hidden
                            "
                        >
                            {{-- Cabeçalho --}}
                            <div class="inventory-v34-defense-head">
                                <div class="inventory-v34-defense-head-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h5 class="font-serif text-[18px] font-black text-[#53150f]">
                                            Escudo
                                        </h5>

                                        <span class="inventory-v34-auto-equip">
                                            Equipável
                                        </span>
                                    </div>

                                    <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/78">
                                        O escudo armazena o bônus base e o bônus mágico separadamente.
                                        Quando equipado, ambos podem entrar na CA.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="removeModule('shield')"
                                    class="inventory-v34-remove"
                                >
                                    Remover
                                </button>
                            </div>

                            <div class="space-y-5 p-4 sm:p-5">
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>01</span>

                                        <div>
                                            <strong>Identidade do Escudo</strong>
                                            <p>
                                                Nome mecânico do equipamento e seus bônus defensivos.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="
                                            mt-3
                                            grid
                                            grid-cols-1
                                            gap-3
                                            sm:grid-cols-[minmax(0,1fr)_132px_132px]
                                        "
                                    >
                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Nome / Tipo
                                            </span>

                                            <input
                                                type="text"
                                                maxlength="120"
                                                x-model="form.properties.shield.label"
                                                placeholder="Ex.: Escudo"
                                                class="inventory-v10-input mt-1.5"
                                            >
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Bônus Base
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <span>+</span>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="99"
                                                    x-model.number="form.properties.shield.ac_bonus"
                                                >
                                            </div>
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Bônus Mágico
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <span>+</span>

                                                <input
                                                    type="number"
                                                    min="-99"
                                                    max="99"
                                                    x-model.number="form.properties.shield.magic_bonus"
                                                >
                                            </div>
                                        </label>
                                    </div>
                                </section>

                                <section class="inventory-v34-shield-total">
                                    <div>
                                        <span>
                                            Base
                                        </span>

                                        <strong
                                            x-text="
                                                '+'
                                                + (
                                                    parseInt(
                                                        form.properties.shield.ac_bonus
                                                    ) || 0
                                                )
                                            "
                                        ></strong>
                                    </div>

                                    <b>+</b>

                                    <div>
                                        <span>
                                            Mágico
                                        </span>

                                        <strong
                                            x-text="
                                                (
                                                    parseInt(
                                                        form.properties.shield.magic_bonus
                                                    ) || 0
                                                ) >= 0
                                                    ? '+'
                                                        + (
                                                            parseInt(
                                                                form.properties.shield.magic_bonus
                                                            ) || 0
                                                        )
                                                    : (
                                                        parseInt(
                                                            form.properties.shield.magic_bonus
                                                        ) || 0
                                                    )
                                            "
                                        ></strong>
                                    </div>

                                    <b>=</b>

                                    <div class="is-total">
                                        <span>
                                            Bônus de CA
                                        </span>

                                        <strong
                                            x-text="
                                                (
                                                    shieldTotalBonus(
                                                        form.properties.shield
                                                    ) >= 0
                                                        ? '+'
                                                        : ''
                                                )
                                                + shieldTotalBonus(
                                                    form.properties.shield
                                                )
                                            "
                                        ></strong>
                                    </div>
                                </section>

                                <div class="inventory-v34-equipment-note">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>

                                    <span>
                                        Este módulo torna o item equipável automaticamente.
                                        A ficha poderá reconhecer o escudo quando ele estiver em uso.
                                    </span>
                                </div>
                            </div>
                        </div>


                        {{-- PROPRIEDADES CUSTOM --}}
                        <div class="mt-4 space-y-3">
                            <template x-for="(property, index) in form.properties.custom_properties" :key="'custom-property-' + index">
                                <div class="inventory-v10-module overflow-hidden">
                                    <div class="flex items-center justify-between border-b border-[#d8c7ab]/50 bg-[#efe9dc]/34 px-4 py-2.5">
                                        <strong class="font-serif text-[15px] text-[#53150f]">Propriedade Custom</strong>
                                        <button type="button" @click="removeCustomProperty(index)" class="rounded-md px-2 py-1 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
                                        <label>
                                            <span class="text-[11px] font-black text-[#53150f]">Título</span>
                                            <input type="text" maxlength="150" x-model="property.title" placeholder="Ex.: Defesa" class="inventory-v10-input mt-1.5">
                                        </label>
                                        <label>
                                            <span class="text-[11px] font-black text-[#53150f]">Valor</span>
                                            <input type="text" maxlength="250" x-model="property.value" placeholder="Ex.: +2" class="inventory-v10-input mt-1.5">
                                        </label>
                                        <label class="sm:col-span-2">
                                            <span class="text-[11px] font-black text-[#53150f]">Explicação</span>
                                            <textarea rows="2" maxlength="5000" x-model="property.description" placeholder="Explique esta propriedade, se necessário..." class="inventory-v10-textarea mt-1.5"></textarea>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </section>


                    {{-- =====================================================
                         HABILIDADES
                    ====================================================== --}}
                    <section class="mt-7 border-t border-[#d8c7ab]/60 pt-5">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h4 class="font-serif text-[19px] font-black text-[#53150f]">Habilidades</h4>
                                <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/72">
                                    Efeitos passivos e habilidades limitadas ficam aqui. Cada habilidade pode ter seu próprio Rastreador.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="addFeature()"
                                class="inline-flex min-h-[40px] items-center gap-2 rounded-lg bg-[#6b1d14] px-3.5 text-[10px] font-black uppercase tracking-[0.06em] text-[#f4f1e8] hover:bg-[#53150f]"
                            >
                                <span class="text-[15px] leading-none">+</span>
                                Adicionar Habilidade
                            </button>
                        </div>

                        <div class="mt-3 space-y-3">
                            <template x-for="(feature, index) in form.properties.features" :key="'feature-editor-' + index">
                                <article class="inventory-v10-feature-card overflow-hidden">
                                    <div class="flex items-center gap-2 border-b border-[#d8c7ab]/50 bg-[#efe9dc]/32 px-3 py-2">
                                        <button
                                            type="button"
                                            @click="toggleFeatureEditor(feature)"
                                            class="group flex min-w-0 flex-1 items-center gap-3 rounded-lg px-1 py-1 text-left transition hover:bg-[#eadbc8]/35"
                                            :aria-expanded="feature.editor_open"
                                        >
                                            <span
                                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-[#cdbb9f]/70 bg-[#faf8f2] font-serif text-[12px] font-black text-[#8c6239]"
                                                x-text="String(index + 1).padStart(2, '0')"
                                            ></span>

                                            <span class="min-w-0 flex-1">
                                                <strong
                                                    class="block truncate font-serif text-[15px] font-black text-[#53150f]"
                                                    x-text="String(feature.title ?? '').trim() || 'Habilidade'"
                                                ></strong>

                                                <span
                                                    x-show="!feature.editor_open && String(feature.description ?? '').trim()"
                                                    x-cloak
                                                    class="mt-0.5 block truncate text-[9px] text-[#8c6239]/70"
                                                    x-text="feature.description"
                                                ></span>
                                            </span>

                                            <svg
                                                class="h-4 w-4 shrink-0 text-[#8c6239] transition-transform duration-200"
                                                :class="{ 'rotate-180': feature.editor_open }"
                                                viewBox="0 0 20 20"
                                                fill="none"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M5 7.5 10 12.5 15 7.5"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            @click.stop="removeFeature(index)"
                                            class="shrink-0 rounded-md px-2 py-1 text-[9px] font-black uppercase text-red-700 hover:bg-red-50"
                                        >
                                            Remover
                                        </button>
                                    </div>

                                    <div
                                        x-show="feature.editor_open"
                                        x-cloak
                                        x-transition.opacity.duration.120ms
                                        class="space-y-4 p-4"
                                    >
                                        <label class="block">
                                            <span class="text-[11px] font-black text-[#53150f]">Nome da Habilidade</span>
                                            <input type="text" maxlength="150" x-model="feature.title" placeholder="Ex.: Luz da Ressurreição" class="inventory-v10-input mt-1.5">
                                        </label>

                                        <label class="block">
                                            <span class="text-[11px] font-black text-[#53150f]">Descrição</span>
                                            <textarea rows="4" maxlength="15000" x-model="feature.description" placeholder="Descreva o efeito da habilidade..." class="inventory-v10-textarea mt-1.5"></textarea>
                                        </label>

                                        <div class="border-t border-[#d8c7ab]/48 pt-4">
                                            <span class="text-[11px] font-black text-[#53150f]">Uso</span>
                                            <div class="mt-2 grid grid-cols-2 gap-2 sm:max-w-md">
                                                <button
                                                    type="button"
                                                    @click="setFeatureUsage(feature, false)"
                                                    class="inventory-v10-choice"
                                                    :class="{ 'active': !feature.usage.enabled }"
                                                >
                                                    Passiva
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="setFeatureUsage(feature, true)"
                                                    class="inventory-v10-choice"
                                                    :class="{ 'active': feature.usage.enabled }"
                                                >
                                                    Limitada
                                                </button>
                                            </div>

                                            <div
                                                x-show="feature.usage.enabled"
                                                x-cloak
                                                class="
                                                    inventory-v31-tracker-editor
                                                    mt-3
                                                    rounded-xl
                                                    border
                                                    border-[#d8c7ab]/60
                                                    bg-[#efe9dc]/24
                                                    p-3.5
                                                "
                                            >
                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                    <div>
                                                        <strong class="block text-[11px] text-[#53150f]">
                                                            Rastreador da Habilidade
                                                        </strong>

                                                        <span class="mt-0.5 block text-[10px] text-[#8c6239]/70">
                                                            Escolha se as cargas são consumidas ou acumuladas.
                                                        </span>
                                                    </div>

                                                    <div class="inventory-v10-tracker">
                                                        <button
                                                            type="button"
                                                            @click="
                                                                feature.usage.current =
                                                                    Math.max(
                                                                        0,
                                                                        feature.usage.current - 1
                                                                    )
                                                            "
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Reduzir carga acumulada'
                                                                    : 'Gastar carga'
                                                            "
                                                        >
                                                            −
                                                        </button>

                                                        <strong
                                                            x-text="
                                                                feature.usage.current
                                                                + '/'
                                                                + feature.usage.max
                                                            "
                                                        ></strong>

                                                        <button
                                                            type="button"
                                                            @click="
                                                                feature.usage.current =
                                                                    Math.min(
                                                                        feature.usage.max,
                                                                        feature.usage.current + 1
                                                                    )
                                                            "
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Acumular carga'
                                                                    : 'Recuperar carga'
                                                            "
                                                        >
                                                            +
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                        Comportamento
                                                    </span>

                                                    <div class="mt-1.5 grid grid-cols-2 gap-2">
                                                        <button
                                                            type="button"
                                                            @click="
                                                                setFeatureCounterMode(
                                                                    feature,
                                                                    'spend'
                                                                )
                                                            "
                                                            :disabled="feature.usage.recovery === 'single_use'"
                                                            class="inventory-v31-counter-choice disabled:cursor-not-allowed disabled:opacity-45"
                                                            :class="{
                                                                'active':
                                                                    feature.usage.counter_mode === 'spend'
                                                            }"
                                                        >
                                                            <strong>Consome cargas</strong>
                                                            <span>Começa no máximo e vai diminuindo.</span>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            @click="
                                                                setFeatureCounterMode(
                                                                    feature,
                                                                    'build'
                                                                )
                                                            "
                                                            :disabled="feature.usage.recovery === 'single_use'"
                                                            class="inventory-v31-counter-choice disabled:cursor-not-allowed disabled:opacity-45"
                                                            :class="{
                                                                'active':
                                                                    feature.usage.counter_mode === 'build'
                                                            }"
                                                        >
                                                            <strong>Acumula cargas</strong>
                                                            <span>Começa em 0 e sobe a cada uso.</span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div
                                                    class="
                                                        mt-3
                                                        grid
                                                        grid-cols-2
                                                        gap-3
                                                        md:grid-cols-[120px_120px_minmax(0,1fr)]
                                                    "
                                                >
                                                    {{-- Máximo vem primeiro por ser a regra-base do tracker. --}}
                                                    <label>
                                                        <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                            Máximo
                                                        </span>

                                                        <input
                                                            type="number"
                                                            min="1"
                                                            x-model.number="feature.usage.max"
                                                            @change="clampFeatureUsage(feature)"
                                                            class="inventory-v10-input mt-1 text-center"
                                                        >
                                                    </label>

                                                    <label>
                                                        <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                            Atual
                                                        </span>

                                                        <input
                                                            type="number"
                                                            min="0"
                                                            :max="feature.usage.max"
                                                            x-model.number="feature.usage.current"
                                                            @change="clampFeatureUsage(feature)"
                                                            class="inventory-v10-input mt-1 text-center"
                                                        >
                                                    </label>

                                                    <label>
                                                        <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                            Recupera
                                                        </span>

                                                        <select
                                                            x-model="feature.usage.recovery"
                                                            @change="handleFeatureRecoveryChange(feature)"
                                                            class="inventory-v10-select mt-1"
                                                        >
                                                            <template
                                                                x-for="(label, key) in recoveryLabels"
                                                                :key="key"
                                                            >
                                                                <option
                                                                    :value="key"
                                                                    x-text="label"
                                                                ></option>
                                                            </template>
                                                        </select>
                                                    </label>
                                                </div>

                                                <p
                                                    x-show="feature.usage.recovery === 'single_use'"
                                                    x-cloak
                                                    class="inventory-v43-single-use-note"
                                                >
                                                    Uso Único não recupera cargas. Defina o <strong>Máximo</strong> e o <strong>Atual</strong> normalmente; cada confirmação gasta 1 carga permanentemente. O item só fica <strong>Usado</strong> quando chegar a 0.
                                                </p>

                                                <input
                                                    x-show="feature.usage.recovery === 'custom'"
                                                    x-cloak
                                                    type="text"
                                                    maxlength="120"
                                                    x-model="feature.usage.recovery_custom"
                                                    placeholder="Ex.: Ao terminar uma cena"
                                                    class="inventory-v10-input mt-3"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </template>

                            <div
                                x-show="form.properties.features.length === 0"
                                x-cloak
                                class="rounded-xl border border-dashed border-[#cdbb9f]/75 bg-[#efe9dc]/20 px-4 py-6 text-center"
                            >
                                <p class="font-serif text-[15px] font-black text-[#53150f]">Nenhuma habilidade adicionada</p>
                                <p class="mt-1 text-[10px] text-[#8c6239]/72">Itens simples podem ficar assim. Adicione habilidades apenas quando houver efeitos próprios.</p>
                            </div>
                        </div>
                    </section>


                    <div x-show="editingId && form.is_cursed" x-cloak class="mt-5 border-l-2 border-red-700 bg-red-50/60 px-4 py-3">
                        <p class="text-[9px] font-black uppercase tracking-[0.08em] text-red-700">Maldição Revelada</p>
                        <p class="mt-1 whitespace-pre-line text-[12px] leading-relaxed text-red-900" x-text="form.curse_description"></p>
                    </div>

                    <div x-show="saveError" x-cloak class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[11px] font-bold text-red-700" x-text="saveError"></div>
                </div>

                <div
                    class="
                        inventory-v35-editor-footer

                        flex
                        shrink-0
                        items-center
                        gap-2

                        border-t

                        px-5
                        py-3.5
                    "
                >
                    <button
                        x-show="editingId"
                        x-cloak
                        type="button"
                        @click="deleteConfirmOpen = true"
                        :disabled="saving"
                        class="rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-[0.06em] text-red-700 hover:bg-red-50 disabled:opacity-50"
                    >
                        Excluir
                    </button>

                    <button type="button" @click="closeEditor()" :disabled="saving" class="ml-auto rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-[0.06em] text-[#8c6239] hover:bg-[#e8dfd1] disabled:opacity-50">Cancelar</button>

                    <button
                        type="button"
                        @click="saveItem()"
                        :disabled="saving"
                        class="rounded-lg bg-[#6b1d14] px-5 py-2.5 text-[9px] font-black uppercase tracking-[0.07em] text-[#f4f1e8] hover:bg-[#53150f] disabled:opacity-50"
                    >
                        <span x-show="!saving">Salvar Item</span>
                        <span x-show="saving" x-cloak>Salvando…</span>
                    </button>
                </div>

                <div
                    x-show="deleteConfirmOpen"
                    x-cloak
                    class="
                        inventory-v35-delete-layer
                        absolute
                        inset-0
                        z-50

                        flex
                        items-center
                        justify-center

                        p-4
                    "
                >
                    <div
                        class="
                            inventory-v35-delete-modal

                            w-full
                            max-w-sm
                            overflow-hidden

                            rounded-2xl
                            border

                            shadow-2xl
                        "
                    >
                        <div class="border-b border-[#d8c7ab]/60 bg-[#efe9dc]/55 px-4 py-3">
                            <h4 class="font-serif text-[18px] font-black text-[#53150f]">Excluir item?</h4>
                        </div>
                        <p class="px-4 py-4 text-[12px] leading-relaxed text-[#6b5548]"><strong x-text="form?.name ?? ''"></strong> será removido permanentemente do inventário.</p>
                        <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/60 px-4 py-3">
                            <button type="button" @click="deleteConfirmOpen = false" class="rounded-lg px-3 py-2 text-[9px] font-black uppercase text-[#8c6239]">Voltar</button>
                            <button type="button" @click="deleteItem()" :disabled="saving" class="rounded-lg bg-red-700 px-3 py-2 text-[9px] font-black uppercase text-white disabled:opacity-50">Excluir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>


    {{-- TOOLTIP FLUTUANTE DO EDITOR --}}
    <template x-teleport="body">
        <div
            x-show="editorTooltip.open"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="
                character-sheet-theme
                inventory-v32-floating-tooltip
                inventory-v35-floating-tooltip
            "
            :style="`
                left: ${editorTooltip.left}px;
                top: ${editorTooltip.top}px;
            `"
            role="tooltip"
        >
            <strong
                x-show="editorTooltip.title"
                x-cloak
                x-text="editorTooltip.title"
            ></strong>

            <p x-text="editorTooltip.text"></p>
        </div>
    </template>


    {{-- =========================================================
         SELETOR DE SINTONIZAÇÃO
    ========================================================== --}}
    <template x-teleport="body">
        <div
            x-show="attunementPickerOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Escolher item para sintonização"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[245]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="attunementPickerOpen = false"
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-picker-modal

                    relative
                    z-10

                    w-full
                    max-w-[560px]
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
            >
                <div class="flex items-center justify-between border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-4 py-3">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Sintonização</p>
                        <h3 class="font-serif text-[19px] font-black text-[#53150f]">Escolher Item</h3>
                    </div>
                    <button type="button" @click="attunementPickerOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-xl text-[#8c6239] hover:bg-[#e8dfd1]">×</button>
                </div>

                <div class="max-h-[62vh] overflow-y-auto p-2">
                    <template x-for="item in eligibleAttunementItems" :key="'eligible-' + item.id">
                        <button
                            type="button"
                            @click="requestAttunement(item); if (!attunementWarningOpen) attunementPickerOpen = false"
                            class="flex w-full items-center gap-3 border-b border-[#d8c7ab]/45 px-2.5 py-3 text-left last:border-b-0 hover:bg-[#efe9dc]/45"
                        >
                            <div class="inventory-v10-thumb flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-[#d8c7ab]/55">
                                <template x-if="item.image_url"><img :src="item.image_url" :alt="item.name"></template>
                                <template x-if="!item.image_url"><span class="font-serif text-[17px] text-[#8c6239]/40">◆</span></template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate font-serif text-[15px] font-black text-[#53150f]" x-text="item.name"></strong>
                                <span class="mt-0.5 block truncate text-[10px] italic text-[#8c6239]" x-text="classificationLine(item)"></span>
                            </div>
                        </button>
                    </template>

                    <div x-show="eligibleAttunementItems.length === 0" x-cloak class="px-4 py-9 text-center">
                        <p class="font-serif text-[16px] font-black text-[#53150f]">Nenhum item disponível</p>
                        <p class="mt-1 text-[10px] text-[#8c6239]">Apenas Itens Maravilhosos sintonizáveis ainda não sintonizados aparecem aqui.</p>
                    </div>
                </div>
            </div>
        </div>
    </template>


    {{-- LIMITE DE 3 --}}
    <template x-teleport="body">
        <div
            x-show="attunementWarningOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Limite de sintonização"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[250]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="
                    attunementWarningOpen = false;
                    pendingAttunementItem = null
                "
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-warning-modal

                    relative
                    z-10

                    w-full
                    max-w-[500px]
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
            >
                <div class="border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-4 py-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Limite Padrão</p>
                    <h3 class="mt-0.5 font-serif text-[19px] font-black text-[#53150f]">Sintonizar além de 3 itens?</h3>
                </div>

                <div class="px-4 py-4 text-[12px] leading-relaxed text-[#6b5548]">
                    Você já possui <strong x-text="attunedItems.length"></strong> itens sintonizados. O limite padrão é 3.
                    <p class="mt-2">Continue apenas se uma regra, habilidade, efeito ou decisão do Mestre permitir ultrapassar esse limite.</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/60 px-4 py-3">
                    <button type="button" @click="attunementWarningOpen = false; pendingAttunementItem = null" class="rounded-lg px-3 py-2 text-[9px] font-black uppercase text-[#8c6239]">Cancelar</button>
                    <button type="button" @click="confirmExtraAttunement()" class="rounded-lg bg-[#6b1d14] px-3 py-2 text-[9px] font-black uppercase text-[#f4f1e8]">Sintonizar Mesmo Assim</button>
                </div>
            </div>
        </div>
    </template>


    {{-- ITENS EQUIPADOS — LISTA SIMPLES --}}
    <template x-teleport="body">
        <div
            x-show="equippedDrawerOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Itens equipados"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[238]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="equippedDrawerOpen = false"
            ></div>

            <section
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-equipped-modal
                    inventory-v29-equipped-modal
                "
            >
                <header class="inventory-v29-equipped-header">
                    <div>
                        <span class="inventory-v29-equipped-kicker">
                            Em uso
                        </span>

                        <div class="inventory-v29-equipped-title-row">
                            <h3>Itens Equipados</h3>

                            <span
                                class="inventory-v29-equipped-count"
                                x-text="equippedItems.length"
                            ></span>
                        </div>
                    </div>

                    <span class="inventory-v29-equipped-hint">
                        Clique em um item para abrir
                    </span>
                </header>

                <div class="inventory-v29-equipped-list-wrap">
                    <template x-if="equippedItems.length === 0">
                        <div class="inventory-v29-equipped-empty">
                            <span>◇</span>
                            <strong>Nenhum item equipado</strong>
                            <p>Itens marcados como equipados aparecerão aqui.</p>
                        </div>
                    </template>

                    <div
                        x-show="equippedItems.length > 0"
                        x-cloak
                        class="inventory-v29-equipped-list"
                    >
                        <template
                            x-for="item in equippedItems"
                            :key="'equipped-simple-' + item.id"
                        >
                            <button
                                type="button"
                                class="inventory-v29-equipped-row"
                                @click="
                                    equippedDrawerOpen = false;
                                    openDetail(item)
                                "
                            >
                                <div class="inventory-v29-equipped-thumb">
                                    <template x-if="item.image_url">
                                        <img
                                            :src="item.image_url"
                                            :alt="item.name"
                                        >
                                    </template>

                                    <template x-if="!item.image_url">
                                        <span>◆</span>
                                    </template>
                                </div>

                                <div class="inventory-v29-equipped-copy">
                                    <strong x-text="item.name"></strong>

                                    <span
                                        x-text="cardClassificationLine(item)"
                                    ></span>
                                </div>

                                <span
                                    class="inventory-v29-equipped-chevron"
                                    aria-hidden="true"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="m9 6 6 6-6 6"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
            </section>
        </div>
    </template>


    {{-- ITENS ENCONTRADOS --}}
    <template x-teleport="body">
        <div
            x-show="foundItemsOpen"
            x-cloak
            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"
            data-theme="{{ $characterTheme }}"
            role="dialog"
            aria-modal="true"
            aria-label="Itens encontrados"
            class="character-sheet-theme character-theme-modal-layer inventory-v35-modal-layer fixed inset-0 z-[235] flex items-center justify-center p-4"
        >
            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="foundItemsOpen = false"
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                class="character-theme-modal inventory-v35-modal inventory-v35-found-modal relative z-10 w-full max-w-[680px] overflow-hidden rounded-2xl border shadow-2xl"
            >
                <div class="flex items-center justify-between gap-3 border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-4 py-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Itens encontrados</p>
                            <span
                                x-show="foundLootLoaded"
                                x-cloak
                                class="inline-flex h-[22px] min-w-[22px] items-center justify-center rounded-full bg-[#6b1d14]/[.08] px-1.5 font-serif text-[11px] font-black text-[#6b1d14]"
                                x-text="foundLootCount"
                            ></span>
                        </div>
                        <h3 class="font-serif text-[19px] font-black text-[#53150f]">Itens encontrados</h3>
                        <p class="mt-0.5 text-[10px] text-[#8c6239]">Itens e moedas liberados pelo Mestre aparecem aqui.</p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <button
                            type="button"
                            @click="loadFoundLoot()"
                            :disabled="foundLootLoading"
                            class="inline-flex h-8 items-center justify-center rounded-lg border border-[#cdbb9f]/70 bg-[#faf8f2] px-3 text-[9px] font-black uppercase tracking-[0.06em] text-[#8c6239] hover:bg-[#e8dfd1] disabled:cursor-wait disabled:opacity-60"
                        >
                            <span x-text="foundLootLoading ? 'Atualizando...' : 'Atualizar'"></span>
                        </button>
                        <button
                            type="button"
                            @click="foundItemsOpen = false"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-xl text-[#8c6239] hover:bg-[#e8dfd1]"
                        >×</button>
                    </div>
                </div>

                <div class="max-h-[72vh] overflow-y-auto px-4 py-4">
                    <div
                        x-show="foundLootLoading && !foundLootLoaded"
                        x-cloak
                        class="px-5 py-12 text-center"
                    >
                        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-[#cdbb9f] border-t-[#6b1d14]"></div>
                        <p class="mt-3 text-[11px] font-bold text-[#8c6239]">Procurando espólios disponíveis...</p>
                    </div>

                    <div
                        x-show="foundLootError"
                        x-cloak
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 text-center"
                    >
                        <p class="text-[11px] font-bold text-red-700" x-text="foundLootError"></p>
                        <button
                            type="button"
                            class="mt-3 rounded-lg border border-red-200 bg-white px-3 py-2 text-[9px] font-black uppercase tracking-[0.05em] text-red-700"
                            @click="loadFoundLoot()"
                        >Tentar novamente</button>
                    </div>

                    <div
                        x-show="foundLootLoaded && !foundLootLoading && !foundLootError && !hasFoundLoot"
                        x-cloak
                        class="px-5 py-12 text-center"
                    >
                        <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full border border-[#cdbb9f]/65 bg-[#efe9dc]/55 font-serif text-[17px] text-[#8c6239]">?</div>
                        <p class="mt-3 font-serif text-[17px] font-black text-[#53150f]">Nenhum item disponível</p>
                        <p class="mx-auto mt-1.5 max-w-sm text-[11px] leading-relaxed text-[#8c6239]">
                            Quando o Mestre liberar um Espólio para este personagem, os itens e moedas aparecerão aqui.
                        </p>
                    </div>

                    <div
                        x-show="foundLootLoaded && !foundLootError && foundLootItems.length > 0"
                        x-cloak
                    >
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Itens</p>
                                <p class="mt-0.5 text-[10px] text-[#9a795b]">Cada clique move uma unidade para o inventário.</p>
                            </div>
                            <span class="rounded-full bg-[#efe9dc] px-2 py-1 font-serif text-[11px] font-black text-[#8c6239]" x-text="foundLootItems.length"></span>
                        </div>

                        <div class="grid gap-2.5">
                            <template x-for="item in foundLootItems" :key="'found-loot-item-' + item.id">
                                <article class="rounded-xl border border-[#cdbb9f]/65 bg-[#faf8f2] p-3">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#d8c7ab]/70 bg-[#fffdf9]">
                                            <template x-if="item.image_url">
                                                <img :src="item.image_url" :alt="item.name" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!item.image_url">
                                                <span class="font-serif text-xl text-[#b79b7f]">◆</span>
                                            </template>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-start justify-between gap-2">
                                                <div class="min-w-0">
                                                    <h4 class="font-serif text-[16px] font-black leading-tight text-[#53150f]" x-text="item.name"></h4>
                                                    <p class="mt-0.5 text-[9px] font-black uppercase tracking-[0.055em] text-[#8c6239]">
                                                        <span x-text="foundLootNatureLabel(item)"></span>
                                                        <template x-if="item.rarity_label">
                                                            <span> · <span x-text="item.rarity_label"></span></span>
                                                        </template>
                                                    </p>
                                                </div>
                                                <span class="shrink-0 rounded-full bg-[#6b1d14]/[.07] px-2 py-1 text-[9px] font-black text-[#6b1d14]">
                                                    Disponível: <span x-text="item.quantity_remaining"></span>
                                                </span>
                                            </div>

                                            <p
                                                x-show="item.campaign_name || item.drop_name"
                                                x-cloak
                                                class="mt-1.5 text-[9px] text-[#9a795b]"
                                            >
                                                <span x-show="item.campaign_name" x-text="item.campaign_name"></span>
                                                <span x-show="item.campaign_name && item.drop_name"> · </span>
                                                <span x-show="item.drop_name" x-text="item.drop_name"></span>
                                            </p>

                                            <p
                                                x-show="item.description"
                                                x-cloak
                                                class="mt-2 max-h-[58px] overflow-hidden text-[10px] leading-[1.45] text-[#6b5548]"
                                                x-text="item.description"
                                            ></p>

                                            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1">
                                                <span
                                                    x-show="item.requires_attunement"
                                                    x-cloak
                                                    class="text-[9px] font-bold text-[#6b1d14]"
                                                >Requer sintonia</span>
                                                <span
                                                    x-show="item.weight !== null && item.weight > 0"
                                                    x-cloak
                                                    class="text-[9px] text-[#8c6239]"
                                                >Peso: <span x-text="item.weight"></span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-end">
                                        <button
                                            type="button"
                                            @click="claimFoundLootItem(item)"
                                            :disabled="foundLootBusyItemId !== null || foundLootBusyCurrencyId !== null"
                                            class="inline-flex min-h-[36px] items-center justify-center rounded-lg border border-[#6b1d14] bg-[#6b1d14] px-3.5 text-[9px] font-black uppercase tracking-[0.05em] text-[#faf8f2] hover:bg-[#53150f] disabled:cursor-wait disabled:opacity-60"
                                        >
                                            <span x-text="parseInt(foundLootBusyItemId) === parseInt(item.id) ? 'Adicionando...' : 'Adicionar ao inventário'"></span>
                                        </button>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    <div
                        x-show="foundLootLoaded && !foundLootError && foundLootCurrencies.length > 0"
                        x-cloak
                        :class="{ 'mt-5': foundLootItems.length > 0 }"
                    >
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Moedas</p>
                                <p class="mt-0.5 text-[10px] text-[#9a795b]">As moedas resgatadas entram diretamente na bolsa.</p>
                            </div>
                            <span class="rounded-full bg-[#efe9dc] px-2 py-1 font-serif text-[11px] font-black text-[#8c6239]" x-text="foundLootCurrencies.length"></span>
                        </div>

                        <div class="grid gap-2.5">
                            <template x-for="entry in foundLootCurrencies" :key="'found-loot-currency-' + entry.id">
                                <article class="rounded-2xl border border-[#cdbb9f]/65 bg-[#faf8f2] p-4">
                                    <div class="flex flex-col gap-4">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="font-serif text-[16px] font-black text-[#53150f]" x-text="entry.label || 'Moedas'"></h4>
                                                    <span
                                                        class="rounded-full border border-[#cdbb9f] bg-white px-2.5 py-1 text-[9px] font-black text-[#76553f]"
                                                        x-text="foundLootCurrencyModeLabel(entry)"
                                                    ></span>
                                                </div>

                                                <p
                                                    x-show="entry.campaign_name || entry.drop_name"
                                                    x-cloak
                                                    class="mt-1 text-[10px] text-[#9a795b]"
                                                >
                                                    <span x-show="entry.campaign_name" x-text="entry.campaign_name"></span>
                                                    <span x-show="entry.campaign_name && entry.drop_name"> · </span>
                                                    <span x-show="entry.drop_name" x-text="entry.drop_name"></span>
                                                </p>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="mb-2 text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">
                                                <span x-text="entry.distribution_mode === 'open' ? 'Disponível na caixa' : 'Valor disponível para você'"></span>
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="line in foundLootCoinLines(entry)" :key="entry.id + '-' + line.coin">
                                                    <span class="rounded-xl border border-[#d8c7ab]/70 bg-[#fffdf9] px-3 py-2 text-[10px] font-black text-[#6b5548]">
                                                        <span x-text="line.label"></span>
                                                        <span class="ml-1" x-text="formatMoney(line.amount)"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        <div
                                            x-show="entry.distribution_mode === 'open'"
                                            x-cloak
                                            class="rounded-xl border border-[#d8c7ab]/70 bg-white p-3"
                                        >
                                            <p class="text-[10px] font-bold leading-5 text-[#76553f]">
                                                Combine a divisão com a Party e informe apenas o que você vai retirar agora.
                                            </p>

                                            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                                <template x-for="line in foundLootCoinLines(entry)" :key="'claim-' + entry.id + '-' + line.coin">
                                                    <label>
                                                        <span class="mb-1 block text-[8px] font-black uppercase tracking-wider text-[#8c6239]" x-text="line.label"></span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            :max="line.amount"
                                                            x-model.number="foundLootCurrencyDrafts[entry.id][line.coin]"
                                                            class="h-10 w-full rounded-lg border border-[#cdbb9f] bg-[#fffdf9] px-2 text-[11px] font-black text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                                        >
                                                    </label>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="flex justify-end">
                                            <button
                                                type="button"
                                                @click="claimFoundLootCurrency(entry)"
                                                :disabled="foundLootBusyCurrencyId !== null || foundLootBusyItemId !== null"
                                                class="inline-flex min-h-[40px] items-center justify-center rounded-xl bg-[#6b1d14] px-4 text-[9px] font-black uppercase tracking-[0.05em] text-white transition hover:bg-[#53150f] disabled:cursor-wait disabled:opacity-60"
                                            >
                                                <span
                                                    x-text="parseInt(foundLootBusyCurrencyId) === parseInt(entry.id)
                                                        ? 'Adicionando...'
                                                        : (entry.distribution_mode === 'open' ? 'Retirar da caixa' : 'Adicionar à bolsa')"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </template>

    {{-- TOAST --}}
    <div
        x-show="toastOpen"
        x-cloak

        x-transition:enter="
            transition
            ease-out
            duration-180
        "
        x-transition:enter-start="
            opacity-0
            translate-y-2
            scale-[.985]
        "
        x-transition:enter-end="
            opacity-100
            translate-y-0
            scale-100
        "
        x-transition:leave="
            transition
            ease-in
            duration-120
        "
        x-transition:leave-start="
            opacity-100
            translate-y-0
            scale-100
        "
        x-transition:leave-end="
            opacity-0
            translate-y-1
            scale-[.99]
        "

        class="
            inventory-v35-toast

            fixed
            bottom-5
            left-1/2
            z-[270]

            w-[min(92vw,380px)]
            -translate-x-1/2

            rounded-xl
            border

            px-4
            py-3

            shadow-xl
        "
        :class="toastError ? 'border-red-300' : 'border-[#cdbb9f]'"
    >
        <div class="flex items-center gap-3">
            <strong class="font-serif text-[16px]" :class="toastError ? 'text-red-700' : 'text-[#53150f]'" x-text="toastError ? '!' : '✓'"></strong>
            <p class="min-w-0 flex-1 text-[11px] font-bold" :class="toastError ? 'text-red-700' : 'text-[#53150f]'" x-text="toastMessage"></p>
            <button type="button" @click="toastOpen = false" class="text-lg text-[#8c6239]">×</button>
        </div>
    </div>
</section>