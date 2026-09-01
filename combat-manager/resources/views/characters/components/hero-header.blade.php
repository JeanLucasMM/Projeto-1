@props(['character'])

@php
    $combat = $character->combat;

    /*
    |--------------------------------------------------------------------------
    | VIDA
    |--------------------------------------------------------------------------
    */

    $currentHp = max(
        0,
        (int) ($combat?->current_hp ?? 0)
    );

    $maxHp = max(
        1,
        (int) ($combat?->max_hp ?? 1)
    );

    $temporaryHp = max(
        0,
        (int) ($combat?->temporary_hp ?? 0)
    );

    $temporaryMaxHp = max(
        0,
        (int) ($combat?->temporary_max_hp ?? 0)
    );

    /*
    |--------------------------------------------------------------------------
    | REGRA MORQUEN
    |--------------------------------------------------------------------------
    */

    $sheetSettings =
        is_array(
            $character->sheet_settings
            ?? null
        )
            ? $character->sheet_settings
            : [];

    $morquenRuleActive =
        (bool) data_get(
            $sheetSettings,
            'optional_rules.morquen',
            false
        );

    $exhaustionRuleActive =
        (bool) data_get(
            $sheetSettings,
            'optional_rules.exhaustion',
            false
        );

    $deathSuccessLimit =
        $morquenRuleActive
            ? 2
            : 3;

    $deathFailureLimit =
        $morquenRuleActive
            ? 6
            : 3;

    $exhaustionLevel =
        $exhaustionRuleActive
            ? min(
                6,
                max(
                    0,
                    (int) (
                        $combat?->exhaustion_level
                        ?? 0
                    )
                )
            )
            : 0;

    /*
    |--------------------------------------------------------------------------
    | DEATH SAVES
    |--------------------------------------------------------------------------
    */

    $deathSuccesses = min(
        $deathSuccessLimit,
        max(
            0,
            (int) ($combat?->death_save_successes ?? 0)
        )
    );

    $deathFailures = min(
        $deathFailureLimit,
        max(
            0,
            (int) ($combat?->death_save_failures ?? 0)
        )
    );

    /*
    |--------------------------------------------------------------------------
    | IDENTIDADE
    |--------------------------------------------------------------------------
    */

    $classes = $character->classes
        ->sortByDesc(
            fn ($class) => [
                (int) ($class->is_primary ?? false),
                (int) $class->level,
                -((int) ($class->sort_order ?? 0)),
            ]
        )
        ->values();

    $primaryClass =
        $character->primary_class ??
        $classes->first();

    $className =
        $primaryClass?->class ??
        'Classe não definida';

    $subclassName =
        $primaryClass?->subclass;

    $imageUrl =
        $character->image_path
            ? Storage::url(
                $character->image_path
            )
            : null;

    $hasMulticlass =
        $classes->count() > 1;

    /*
    |--------------------------------------------------------------------------
    | PROFICIÊNCIA
    |--------------------------------------------------------------------------
    */

    $calculatedProficiency = match (true) {
        (int) $character->level >= 17 => 6,
        (int) $character->level >= 13 => 5,
        (int) $character->level >= 9 => 4,
        (int) $character->level >= 5 => 3,
        default => 2,
    };

    $proficiencyBonus =
        $character->proficiency_bonus ??
        $calculatedProficiency;

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS
    |--------------------------------------------------------------------------
    */

    $abilities =
        $character->abilities;

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS EFETIVOS
    |--------------------------------------------------------------------------
    |
    | A ficha possui três camadas possíveis para cada atributo:
    |
    | 1. valor base              -> strength, dexterity...
    | 2. temporary_bonuses       -> compatibilidade com o sistema antigo
    | 3. overrides               -> valor temporário completo usado pelo
    |                              editor atual de atributos
    |
    | Exemplo:
    |
    | Destreza base = 18
    | override      = 22
    |
    | Para CA, ataques e qualquer fórmula temporária, devemos usar 22.
    |
    | Se não existir override, ainda respeitamos temporary_bonuses:
    |
    | Destreza base = 18
    | temporary     = +2
    | efetivo       = 20
    |
    */

    $abilityTemporaryBonuses =
        is_array(
            $abilities?->temporary_bonuses
        )
            ? $abilities->temporary_bonuses
            : [];

    $abilityOverrides =
        is_array(
            $abilities?->overrides
        )
            ? $abilities->overrides
            : [];

    $abilityKeyMap = [
        'str' => 'strength',
        'dex' => 'dexterity',
        'con' => 'constitution',
        'int' => 'intelligence',
        'wis' => 'wisdom',
        'cha' => 'charisma',
    ];

    $abilityScores = [];

    foreach (
        $abilityKeyMap as
        $shortKey => $fullKey
    ) {
        $baseScore =
            (int) (
                $abilities?->{$fullKey}
                ?? 10
            );

        /*
        | Alguns registros antigos podem ter chaves curtas.
        | Aceitamos os dois formatos para não perder compatibilidade.
        */
        $temporaryBonus =
            (int) (
                $abilityTemporaryBonuses[
                    $fullKey
                ]
                ?? $abilityTemporaryBonuses[
                    $shortKey
                ]
                ?? 0
            );

        $hasFullOverride =
            array_key_exists(
                $fullKey,
                $abilityOverrides
            )
            && $abilityOverrides[$fullKey] !== null
            && $abilityOverrides[$fullKey] !== '';

        $hasShortOverride =
            array_key_exists(
                $shortKey,
                $abilityOverrides
            )
            && $abilityOverrides[$shortKey] !== null
            && $abilityOverrides[$shortKey] !== '';

        if ($hasFullOverride) {
            $effectiveScore =
                (int) $abilityOverrides[
                    $fullKey
                ];
        } elseif ($hasShortOverride) {
            $effectiveScore =
                (int) $abilityOverrides[
                    $shortKey
                ];
        } else {
            $effectiveScore =
                $baseScore
                + $temporaryBonus;
        }

        $abilityScores[
            $shortKey
        ] =
            max(
                1,
                $effectiveScore
            );
    }

    $abilityModifiers = collect($abilityScores)
        ->mapWithKeys(
            fn ($score, $key) => [
                $key => (int) floor(
                    ($score - 10) / 2
                ),
            ]
        )
        ->all();

    /*
    |--------------------------------------------------------------------------
    | OVERRIDES DE COMBATE
    |--------------------------------------------------------------------------
    |
    | Mantemos o objeto completo para que Armadura e Dados Rápidos
    | não apaguem as configurações um do outro.
    |
    */

    $combatOverrides =
        $combat?->overrides
        ?? [];

    if (is_string($combatOverrides)) {

        $decodedCombatOverrides =
            json_decode(
                $combatOverrides,
                true
            );

        $combatOverrides =
            is_array($decodedCombatOverrides)
                ? $decodedCombatOverrides
                : [];
    }

    if (!is_array($combatOverrides)) {
        $combatOverrides = [];
    }

    /*
    |--------------------------------------------------------------------------
    | DEFESA / ARMADURA
    |--------------------------------------------------------------------------
    |
    | A Defesa Base continua sendo configurada no combate.
    | Regras de armadura e escudo pertencem ao item do inventário.
    |
    */

    $armorConfig = data_get(
        $combatOverrides,
        'armor',
        []
    );

    $storedArmorMode =
        $armorConfig['mode']
        ?? ['dex'];

    $armorMode =
        is_array($storedArmorMode)
            ? array_values(
                $storedArmorMode
            )
            : [
                $storedArmorMode,
            ];

    if (
        empty($armorMode)
        && !array_key_exists(
            'mode',
            $armorConfig
        )
    ) {
        $armorMode = [
            'dex',
        ];
    }

    $namedAcBonuses =
        collect(
            $armorConfig['bonuses']
            ?? []
        )
            ->map(
                fn ($bonus) => [
                    'name' =>
                        trim(
                            (string) (
                                $bonus['name']
                                ?? ''
                            )
                        ),

                    'value' =>
                        (int) (
                            $bonus['value']
                            ?? 0
                        ),
                ]
            )
            ->filter(
                fn ($bonus) =>
                    $bonus['name']
                    !== ''
            )
            ->values()
            ->all();

    $armorItemsPayload =
        $character->items
            ->filter(
                fn ($item) =>
                    is_array(
                        data_get(
                            $item->properties,
                            'armor'
                        )
                    )
            )
            ->map(
                function ($item) use ($character) {
                    $armor =
                        data_get(
                            $item->properties,
                            'armor',
                            []
                        );

                    return [
                        'id' =>
                            (int) $item->id,

                        'name' =>
                            (string) $item->name,

                        'image_url' =>
                            $item->image_path
                                ? route(
                                    'characters.items.image',
                                    [
                                        'character' =>
                                            $character,

                                        'item' =>
                                            $item,

                                        'v' =>
                                            substr(
                                                sha1(
                                                    $item->image_path
                                                ),
                                                0,
                                                12
                                            ),
                                    ],
                                    false
                                )
                                : null,

                        'equipped' =>
                            (bool) $item->equipped,

                        'rarity_label' =>
                            $item->rarity_label,

                        'category' =>
                            (string) (
                                $armor['category']
                                ?? 'custom'
                            ),

                        'category_custom' =>
                            $armor['category_custom']
                            ?? null,

                        'armor_type' =>
                            trim(
                                (string) (
                                    $armor['armor_type']
                                    ?? 'Armadura'
                                )
                            ),

                        'base_ac' =>
                            (int) (
                                $armor['base_ac']
                                ?? 10
                            ),

                        'magic_bonus' =>
                            (int) (
                                $armor['magic_bonus']
                                ?? 0
                            ),

                        'dexterity_mode' =>
                            in_array(
                                $armor['dexterity_mode']
                                ?? null,
                                [
                                    'none',
                                    'full',
                                    'capped',
                                ],
                                true
                            )
                                ? $armor['dexterity_mode']
                                : match (
                                    $armor['category']
                                    ?? null
                                ) {
                                    'light' =>
                                        'full',

                                    'medium' =>
                                        'capped',

                                    default =>
                                        'none',
                                },

                        'dexterity_cap' =>
                            max(
                                0,
                                (int) (
                                    $armor['dexterity_cap']
                                    ?? 2
                                )
                            ),

                        'ability_modifiers' =>
                            array_values(
                                array_filter(
                                    is_array(
                                        $armor['ability_modifiers']
                                        ?? null
                                    )
                                        ? $armor['ability_modifiers']
                                        : [],
                                    fn ($ability) =>
                                        in_array(
                                            $ability,
                                            [
                                                'strength',
                                                'constitution',
                                                'intelligence',
                                                'wisdom',
                                                'charisma',
                                            ],
                                            true
                                        )
                                )
                            ),

                        'equipped_url' =>
                            route(
                                'characters.items.equipped.update',
                                [
                                    'character' =>
                                        $character,

                                    'item' =>
                                        $item,
                                ],
                                false
                            ),
                    ];
                }
            )
            ->values()
            ->all();

    $shieldItemsPayload =
        $character->items
            ->filter(
                fn ($item) =>
                    is_array(
                        data_get(
                            $item->properties,
                            'shield'
                        )
                    )
            )
            ->map(
                function ($item) use ($character) {
                    $shield =
                        data_get(
                            $item->properties,
                            'shield',
                            []
                        );

                    return [
                        'id' =>
                            (int) $item->id,

                        'name' =>
                            (string) $item->name,

                        'image_url' =>
                            $item->image_path
                                ? route(
                                    'characters.items.image',
                                    [
                                        'character' =>
                                            $character,

                                        'item' =>
                                            $item,

                                        'v' =>
                                            substr(
                                                sha1(
                                                    $item->image_path
                                                ),
                                                0,
                                                12
                                            ),
                                    ],
                                    false
                                )
                                : null,

                        'equipped' =>
                            (bool) $item->equipped,

                        'rarity_label' =>
                            $item->rarity_label,

                        'label' =>
                            trim(
                                (string) (
                                    $shield['label']
                                    ?? 'Escudo'
                                )
                            ),

                        'ac_bonus' =>
                            (int) (
                                $shield['ac_bonus']
                                ?? 2
                            ),

                        'magic_bonus' =>
                            (int) (
                                $shield['magic_bonus']
                                ?? 0
                            ),

                        'equipped_url' =>
                            route(
                                'characters.items.equipped.update',
                                [
                                    'character' =>
                                        $character,

                                    'item' =>
                                        $item,
                                ],
                                false
                            ),
                    ];
                }
            )
            ->values()
            ->all();

    /*
    |--------------------------------------------------------------------------
    | DADOS DE VIDA
    |--------------------------------------------------------------------------
    */

    $hitDice = collect(
        $combat?->hit_dice ?? []
    )
        ->map(
            fn ($hitDie) => [
                'die' => strtolower(
                    (string) (
                        $hitDie['die'] ??
                        'd8'
                    )
                ),
                'current' => max(
                    0,
                    (int) (
                        $hitDie['current'] ??
                        0
                    )
                ),
                'maximum' => max(
                    0,
                    (int) (
                        $hitDie['maximum'] ??
                        0
                    )
                ),
            ]
        )
        ->filter(
            fn ($die) =>
                $die['maximum'] > 0
        )
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | DADOS RÁPIDOS
    |--------------------------------------------------------------------------
    */

    $speed =
        $combat?->speed;

    $initiative =
        $combat?->initiative_bonus;
@endphp


@once
    @push('styles')
        <style>
            @keyframes damage-hit {
                0%, 100% {
                    transform: translate3d(0, 0, 0) scale(1);
                    filter: brightness(1);
                }

                8% {
                    transform: translateX(-7px) rotate(-1deg) scale(.992);
                }

                18% {
                    transform: translateX(6px) rotate(.8deg);
                }

                30% {
                    transform: translateX(-5px);
                }

                42% {
                    transform: translateX(4px);
                }

                56% {
                    transform: translateX(-2px);
                }

                70% {
                    transform: translateX(1px);
                }
            }

            @keyframes damage-flash {
                0%, 100% {
                    box-shadow:
                        inset 0 0 0 rgba(185,28,28,0),
                        0 0 0 rgba(220,38,38,0);
                }

                25% {
                    box-shadow:
                        inset 0 0 70px rgba(185,28,28,.55),
                        0 0 20px rgba(220,38,38,.20);
                }

                60% {
                    box-shadow:
                        inset 0 0 25px rgba(185,28,28,.25),
                        0 0 8px rgba(220,38,38,.08);
                }
            }

            @keyframes photo-damage {
                0%, 100% {
                    transform: translate3d(0,0,0) scale(1);
                    filter: brightness(1);
                }

                15% {
                    transform: translateX(-5px) rotate(-1deg);
                    filter: brightness(1.12);
                }

                30% {
                    transform: translateX(5px) rotate(.8deg);
                    box-shadow:
                        inset 0 0 30px rgba(185,28,28,.35),
                        0 0 15px rgba(220,38,38,.18);
                }

                50% {
                    transform: translateX(-3px);
                }

                70% {
                    transform: translateX(2px);
                    filter: brightness(1);
                }
            }

            @keyframes heal {
                0%, 100% {
                    transform: scale(1);
                    filter: brightness(1);
                }

                18% {
                    transform: scale(1.018);
                }

                45% {
                    transform: scale(1.032);
                    filter: brightness(1.25);
                }

                75% {
                    transform: scale(1.01);
                    filter: brightness(1.08);
                }
            }

            @keyframes heal-flash {
                0%, 100% {
                    box-shadow:
                        inset 0 0 0 rgba(34,197,94,0),
                        0 0 0 rgba(34,197,94,0);
                }

                30% {
                    box-shadow:
                        inset 0 0 45px rgba(34,197,94,.22),
                        0 0 26px rgba(34,197,94,.45);
                }

                70% {
                    box-shadow:
                        inset 0 0 18px rgba(34,197,94,.10),
                        0 0 8px rgba(34,197,94,.12);
                }
            }

            @keyframes shield {
                0%, 100% {
                    box-shadow:
                        0 0 4px rgba(59,130,246,.12);
                }

                50% {
                    box-shadow:
                        0 0 12px rgba(56,189,248,.45),
                        0 0 28px rgba(96,165,250,.18);
                }
            }

            @keyframes bloodied {
                0%, 100% {
                    box-shadow:
                        0 0 0 rgba(127,29,29,.10);
                }

                50% {
                    box-shadow:
                        0 0 18px rgba(127,29,29,.30),
                        inset 0 0 18px rgba(127,29,29,.18);
                }
            }

            @keyframes critical {
                0%, 100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 rgba(220,38,38,.20);
                    filter: brightness(1);
                }

                50% {
                    transform: scale(1.01);

                    box-shadow:
                        0 0 26px rgba(220,38,38,.55),
                        inset 0 0 22px rgba(220,38,38,.28);

                    filter: brightness(1.05);
                }
            }

            @keyframes bonus-hp {
                0%, 100% {
                    filter: brightness(1);
                }

                50% {
                    filter: brightness(1.08);

                    box-shadow:
                        0 0 9px rgba(217,164,65,.32);
                }
            }

            @keyframes death-pulse {
                0%, 100% {
                    box-shadow:
                        0 0 0 rgba(140,98,57,.04);
                }

                50% {
                    box-shadow:
                        0 0 22px rgba(140,98,57,.15);
                }
            }

            @keyframes death-tab {
                0% {
                    transform: translateY(-8px);
                    opacity: 0;
                }

                100% {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes death-dead {
                0% {
                    opacity: 0;
                    transform: translate(-50%,-50%) scale(.92);
                }

                100% {
                    opacity: 1;
                    transform: translate(-50%,-50%) scale(1);
                }
            }

            @keyframes death-roll-orbit {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            @keyframes death-roll-pulse {
                0%, 100% {
                    transform: scale(1);
                    box-shadow:
                        0 8px 22px rgba(91,67,49,.17),
                        inset 0 0 0 rgba(255,255,255,0);
                }

                50% {
                    transform: scale(1.035);
                    box-shadow:
                        0 10px 30px rgba(91,67,49,.25),
                        inset 0 0 28px rgba(255,241,215,.10);
                }
            }

            @keyframes death-roll-number {
                0% {
                    opacity: .35;
                    transform: translateY(-2px) scale(.92);
                    filter: blur(.8px);
                }

                50% {
                    opacity: 1;
                    transform: translateY(1px) scale(1.06);
                    filter: blur(0);
                }

                100% {
                    opacity: .5;
                    transform: translateY(0) scale(.96);
                    filter: blur(.4px);
                }
            }

            @keyframes death-roll-settle {
                0% {
                    transform: scale(1.24);
                    filter: brightness(1.45);
                }

                55% {
                    transform: scale(.96);
                }

                100% {
                    transform: scale(1);
                    filter: brightness(1);
                }
            }

            .death-roll-orbit {
                animation:
                    death-roll-orbit 1.15s linear infinite;
            }

            .death-roll-active {
                animation:
                    death-roll-pulse .72s ease-in-out infinite;
            }

            .death-roll-number-active {
                animation:
                    death-roll-number .12s ease-in-out infinite;
            }

            .death-roll-number-settled {
                animation:
                    death-roll-settle .48s cubic-bezier(.2,.9,.2,1);
            }

            @keyframes death-d20-breathe {
                0%, 100% {
                    transform: scale(1);
                    filter:
                        drop-shadow(0 8px 12px rgba(91,67,49,.16));
                }

                50% {
                    transform: scale(1.035);
                    filter:
                        drop-shadow(0 12px 18px rgba(91,67,49,.25));
                }
            }

            @keyframes death-world-fade {
                0% {
                    opacity: 0;
                    backdrop-filter:
                        grayscale(0)
                        saturate(1)
                        brightness(1);
                }

                100% {
                    opacity: 1;
                    backdrop-filter:
                        grayscale(1)
                        saturate(.08)
                        brightness(.62);
                }
            }

            @keyframes death-card-arrive {
                0% {
                    opacity: 0;
                    transform:
                        translateY(18px)
                        scale(.965);
                }

                100% {
                    opacity: 1;
                    transform:
                        translateY(0)
                        scale(1);
                }
            }

            @keyframes stable-world-glow {
                0% {
                    opacity: 0;
                    transform: scale(.82);
                }

                25% {
                    opacity: 1;
                }

                72% {
                    opacity: .86;
                    transform: scale(1.08);
                }

                100% {
                    opacity: 0;
                    transform: scale(1.16);
                }
            }

            @keyframes stable-banner-arrive {
                0% {
                    opacity: 0;
                    transform:
                        translateY(12px)
                        scale(.94);
                }

                28% {
                    opacity: 1;
                    transform:
                        translateY(0)
                        scale(1.03);
                }

                100% {
                    opacity: 1;
                    transform:
                        translateY(0)
                        scale(1);
                }
            }

            .death-d20-breathe {
                animation:
                    death-d20-breathe 1.8s ease-in-out infinite;
            }

            .death-d20-spin {
                animation:
                    death-roll-orbit .95s linear infinite;
                transform-origin:
                    50% 50%;
            }

            .death-world-fade {
                animation:
                    death-world-fade .9s ease-out both;
            }

            /*
            |--------------------------------------------------------------------------
            | MORTE — SOMENTE A FICHA PERDE COR
            |--------------------------------------------------------------------------
            */

            .character-sheet-page {
                transition:
                    filter 1.25s ease,
                    opacity 1.25s ease;
            }

            .character-sheet-page.character-sheet-dead {
                filter:
                    grayscale(1)
                    saturate(.12)
                    brightness(.76);
            }

            .death-card-arrive {
                animation:
                    death-card-arrive 1.05s cubic-bezier(.16,.84,.22,1) both;
            }

            @keyframes death-hope-reveal {
                0% {
                    opacity: 0;
                    transform:
                        translateY(7px);
                    filter:
                        blur(3px);
                }

                100% {
                    opacity: 1;
                    transform:
                        translateY(0);
                    filter:
                        blur(0);
                }
            }

            @keyframes death-revive-reveal {
                0% {
                    opacity: 0;
                    transform:
                        translateY(10px)
                        scale(.96);
                }

                70% {
                    opacity: 1;
                    transform:
                        translateY(-1px)
                        scale(1.015);
                }

                100% {
                    opacity: 1;
                    transform:
                        translateY(0)
                        scale(1);
                }
            }

            .death-hope-reveal {
                animation:
                    death-hope-reveal
                    1.05s
                    ease-out
                    both;
            }

            .death-revive-reveal {
                animation:
                    death-revive-reveal
                    .7s
                    cubic-bezier(.2,.9,.2,1)
                    both;
            }

            .stable-world-glow {
                animation:
                    stable-world-glow 2.2s ease-out both;
            }

            .stable-banner-arrive {
                animation:
                    stable-banner-arrive .55s cubic-bezier(.2,.9,.2,1) both;
            }

            .animate-damage-red {
                animation:
                    damage-hit .55s cubic-bezier(.18,.89,.32,1.2),
                    damage-flash .55s ease-out;
            }

            .animate-damage-photo {
                animation:
                    photo-damage .55s cubic-bezier(.18,.89,.32,1.2);
            }

            .animate-heal-green {
                animation:
                    heal .65s cubic-bezier(.2,.9,.2,1),
                    heal-flash .75s ease-out;
            }

            .temp-hp-active {
                animation:
                    shield 3s ease-in-out infinite;
            }

            .bonus-hp-active {
                animation:
                    bonus-hp 3s ease-in-out infinite;
            }

            .bloodied-card {
                animation:
                    bloodied 2.1s ease-in-out infinite;
            }

            .critical-card {
                animation:
                    critical 2.5s ease-in-out infinite;
            }


            /*
            |--------------------------------------------------------------------------
            | MODAL DE PV — NÃO DEIXAR EFEITOS LUMINOSOS ATRAVESSAREM O BACKDROP
            |--------------------------------------------------------------------------
            |
            | O modal é teleportado para o body. Enquanto ele estiver aberto,
            | neutralizamos somente os efeitos visuais do Hero que fica atrás.
            | Ao fechar, todas as animações voltam ao comportamento normal.
            |
            */

            .hero-hp-modal-open.bloodied-card,
            .hero-hp-modal-open.critical-card,
            .hero-hp-modal-open .animate-damage-red,
            .hero-hp-modal-open .animate-damage-photo,
            .hero-hp-modal-open .animate-heal-green,
            .hero-hp-modal-open .temp-hp-active,
            .hero-hp-modal-open .bonus-hp-active,
            .hero-hp-modal-open .death-drawer-active {
                animation: none !important;
                box-shadow: none !important;
            }

            .hero-hp-modal-open .animate-heal-green,
            .hero-hp-modal-open .temp-hp-active,
            .hero-hp-modal-open .bonus-hp-active {
                filter: none !important;
            }

            .hero-hp-modal-open .hero-life-modal-source {
                filter:
                    saturate(.18)
                    brightness(.58);

                transition:
                    filter .12s ease;
            }

            .death-drawer-active {
                animation:
                    death-pulse 2.2s ease-in-out infinite;
            }

            .death-tab-enter {
                animation:
                    death-tab .22s ease-out;
            }

            .death-dead-overlay {
                animation:
                    death-dead .22s ease-out;
            }

            .clip-shield {
                clip-path: polygon(
                    50% 100%,
                    0 85%,
                    0 0,
                    100% 0,
                    100% 85%
                );
            }

            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            input[type="number"] {
                -moz-appearance: textfield;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTINUIDADE VISUAL DO HEADER
            |--------------------------------------------------------------------------
            */

            .hero-header-segment {
                position: relative;
                border-color: rgba(205,187,159,.72);
                background: #faf7f2;
            }

            .hero-header-segment::after {
                content: "";
                position: absolute;
                inset: 3px;
                pointer-events: none;
                border: 1px solid rgba(216,199,171,.72);
            }
        </style>
    @endpush
@endonce


<div
    x-data="{

        /*
        |--------------------------------------------------------------------------
        | VIDA
        |--------------------------------------------------------------------------
        */

        currentHp: {{ $currentHp }},
        ghostHp: {{ $currentHp }},
        maxHp: {{ $maxHp }},

        temporaryHp: {{ $temporaryHp }},
        temporaryMaxHp: {{ $temporaryMaxHp }},


        /*
        |--------------------------------------------------------------------------
        | DEATH SAVES
        |--------------------------------------------------------------------------
        */

        deathSaveSuccesses: {{ $deathSuccesses }},
        deathSaveFailures: {{ $deathFailures }},

        deathSuccessLimit: {{ $deathSuccessLimit }},
        deathFailureLimit: {{ $deathFailureLimit }},

        morquenRuleActive:
            @js($morquenRuleActive),

        exhaustionRuleActive:
            @js($exhaustionRuleActive),

        exhaustionLevel:
            {{ $exhaustionLevel }},

        exhaustionOpen: false,
        exhaustionDraft: {{ $exhaustionLevel }},
        savingExhaustion: false,
        exhaustionError: null,

        morquenReviving: false,
        morquenReviveError: null,

        /*
        |--------------------------------------------------------------------------
        | MORTE PADRÃO — ESPERANÇA / REVIVER
        |--------------------------------------------------------------------------
        |
        | Sem Regra Morquen:
        |
        | 0s  -> tela de morte normal;
        | 3s  -> aparece a mensagem de esperança;
        | 5s  -> aparece o botão manual Reviver.
        |
        */

        deathSequenceStarted: false,
        deathHopeVisible: false,
        deathReviveVisible: false,

        deathHopeTimer: null,
        deathReviveTimer: null,

        deathReviving: false,
        deathReviveError: null,

        morquenReviveUrl:
            @js(
                route(
                    'characters.morquen.determination-final',
                    $character,
                    false
                )
            ),

        deathDrawerOpen: false,
        deathRolling: false,

        /*
         * Durante os ~3 segundos de suspense, deathRollPreview recebe
         * números falsos rapidamente. deathRollResult só recebe o valor
         * verdadeiro quando a animação termina.
         */
        deathRollPreview: null,
        deathRollResult: null,
        deathRollSettled: false,

        stabilizationEffect: false,


        /*
        |--------------------------------------------------------------------------
        | EDIÇÃO DE VIDA
        |--------------------------------------------------------------------------
        */

        editingHp: false,
        directHp: {{ $currentHp }},

        editingTempHp: false,
        directTempHp: {{ $temporaryHp }},

        hpSettingsOpen: false,
        hpSettingsStep: 'confirm',

        directMaxHp: {{ $maxHp }},
        directTemporaryMaxHp: {{ $temporaryMaxHp }},


        /*
        |--------------------------------------------------------------------------
        | DEFESA / ARMADURA
        |--------------------------------------------------------------------------
        */

        armorOpen: false,

        /*
        | Os cálculos de CA acontecem no Alpine.
        |
        | IMPORTANTE:
        | abilityScores já contém o valor EFETIVO do atributo:
        | override temporário > bônus temporário antigo > valor base.
        |
        | Portanto armorBaseBonus, armaduras leves/médias e atributos
        | especiais de armaduras mágicas usam automaticamente o valor
        | temporário atual.
        |
        | Estrutura:
        | {
        |     str: 12,
        |     dex: 18,
        |     con: 19,
        |     int: 10,
        |     wis: 20,
        |     cha: 10
        | }
        */
        abilityScores:
            @js($abilityScores),

        armorMode:
            @js($armorMode),

        armorBonuses:
            @js($namedAcBonuses),

        armorItems:
            @js($armorItemsPayload),

        shieldItems:
            @js($shieldItemsPayload),

        armorEquipmentBusyId:
            null,

        armorEquipmentError:
            null,

        defensiveEquipUrl:
            @js(
                route(
                    'characters.items.equipped.update',
                    [
                        'character' =>
                            $character,

                        'item' =>
                            '__ITEM__',
                    ],
                    false
                )
            ),

        savingArmor:
            false,

        combatOverrides:
            @js($combatOverrides),


        /*
        |--------------------------------------------------------------------------
        | DESCANSO
        |--------------------------------------------------------------------------
        */

        resting: false,
        restingType: null,
        restError: null,


        /*
        |--------------------------------------------------------------------------
        | ANIMAÇÕES
        |--------------------------------------------------------------------------
        */

        shakingRed: false,
        shakingBlue: false,
        flashingGreen: false,


        /*
        |--------------------------------------------------------------------------
        | VIDA CALCULADA
        |--------------------------------------------------------------------------
        */

        get effectiveMaxHp() {
            return Math.max(
                1,
                this.maxHp +
                this.temporaryMaxHp
            );
        },


        get hpPercent() {
            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        this.currentHp /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get normalHpPercent() {
            const hp =
                Math.min(
                    this.currentHp,
                    this.maxHp
                );

            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        hp /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get bonusHpPercent() {
            const bonus =
                Math.max(
                    0,
                    this.currentHp -
                    this.maxHp
                );

            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        bonus /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get ghostHpPercent() {
            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        this.ghostHp /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get tempHpPercent() {
            return this.hpPercent;
        },


        /*
        |--------------------------------------------------------------------------
        | ESTADO DO PERSONAGEM
        |--------------------------------------------------------------------------
        */

        get isDowned() {
            return (
                this.currentHp <= 0 ||
                this.exhaustionLevel >= 6
            );
        },


        get isDead() {
            return (
                this.exhaustionLevel >= 6
                ||
                (
                    this.currentHp <= 0 &&
                    this.deathSaveFailures >=
                        this.deathFailureLimit
                )
            );
        },


        get isStable() {
            return (
                this.isDowned &&
                this.deathSaveSuccesses >=
                    this.deathSuccessLimit
            );
        },


        get isDying() {
            return (
                this.isDowned &&
                !this.isDead &&
                !this.isStable
            );
        },


        get isMorquenConsciousAtZero() {
            return (
                this.morquenRuleActive &&
                this.isDowned &&
                !this.isDead
            );
        },


        get morquenDeterminationLocked() {
            return !!(
                this.combatOverrides
                ?.morquen
                ?.determination_final_locked
            );
        },


        get canUseMorquenDetermination() {
            return (
                this.morquenRuleActive &&
                this.isDead &&
                (
                    !this.morquenDeterminationLocked ||
                    this.exhaustionLevel === 0
                )
            );
        },


        get isCritical() {
            return (
                !this.isDowned &&
                this.hpPercent <= 25
            );
        },


        get isBloodied() {
            return (
                !this.isDowned &&
                this.hpPercent <= 50 &&
                this.hpPercent > 25
            );
        },


        get statusLabel() {
            if (this.isDead) {
                return '';
            }

            if (this.isMorquenConsciousAtZero) {
                return 'Consciente';
            }

            if (this.isStable) {
                return 'Estabilizado';
            }

            if (this.isDowned) {
                return 'Inconsciente';
            }

            if (this.isCritical) {
                return 'Crítico';
            }

            if (this.isBloodied) {
                return 'Ferido';
            }

            return 'Estável';
        },


        get statusClass() {
            if (this.isDead) {
                return 'text-red-700';
            }

            if (this.isMorquenConsciousAtZero) {
                return 'text-amber-700';
            }

            if (this.isStable) {
                return 'text-emerald-700';
            }

            if (
                this.isDowned ||
                this.isCritical
            ) {
                return 'text-red-700';
            }

            if (this.isBloodied) {
                return 'text-amber-700';
            }

            return 'text-[#8c6239]';
        },


        /*
        |--------------------------------------------------------------------------
        | EXAUSTÃO
        |--------------------------------------------------------------------------
        |
        | Cada nível:
        | -2 em rolagens
        | -5 ft em deslocamento
        |
        | 6 níveis:
        | morte
        |
        */

        get exhaustionRollPenalty() {
            return Math.max(
                0,
                this.exhaustionLevel * 2
            );
        },


        get exhaustionSpeedPenalty() {
            return Math.max(
                0,
                this.exhaustionLevel * 5
            );
        },


        openExhaustionEditor() {
            if (!this.exhaustionRuleActive) {
                return;
            }

            this.exhaustionDraft =
                this.exhaustionLevel;

            this.exhaustionError =
                null;

            this.exhaustionOpen =
                true;
        },


        cancelExhaustionEditor() {
            if (this.savingExhaustion) {
                return;
            }

            this.exhaustionDraft =
                this.exhaustionLevel;

            this.exhaustionError =
                null;

            this.exhaustionOpen =
                false;
        },


        setExhaustionDraft(level) {
            this.exhaustionDraft =
                Math.min(
                    6,
                    Math.max(
                        0,
                        parseInt(level) || 0
                    )
                );
        },


        syncExhaustionGlobal(level) {
            const normalizedLevel =
                Math.min(
                    6,
                    Math.max(
                        0,
                        parseInt(level) || 0
                    )
                );

            window.SpellboundExhaustion = {
                level:
                    normalizedLevel,

                rollPenalty:
                    -(normalizedLevel * 2),

                speedPenalty:
                    normalizedLevel * 5,
            };

            window.dispatchEvent(
                new CustomEvent(
                    'character-exhaustion-updated',
                    {
                        detail: {
                            level:
                                normalizedLevel,

                            rollPenalty:
                                -(normalizedLevel * 2),

                            speedPenalty:
                                normalizedLevel * 5,
                        }
                    }
                )
            );
        },


        async saveExhaustion() {
            if (this.savingExhaustion) {
                return;
            }

            this.savingExhaustion =
                true;

            this.exhaustionError =
                null;

            const level =
                Math.min(
                    6,
                    Math.max(
                        0,
                        parseInt(
                            this.exhaustionDraft
                        ) || 0
                    )
                );

            try {
                const response =
                    await this.persistCombat({
                        exhaustion_level:
                            level,
                    });

                if (!response) {
                    throw new Error(
                        'Não foi possível salvar a exaustão.'
                    );
                }

                this.exhaustionLevel =
                    level;

                this.exhaustionDraft =
                    level;

                this.exhaustionOpen =
                    false;

                if (this.isDead) {
                    this.deathDrawerOpen =
                        false;
                }

            } catch (error) {
                this.exhaustionError =
                    error?.message
                    ?? 'Não foi possível salvar a exaustão.';

            } finally {
                this.savingExhaustion =
                    false;
            }
        },


        /*
        |--------------------------------------------------------------------------
        | DEFESA CALCULADA
        |--------------------------------------------------------------------------
        */

        abilityModifier(
            ability
        ) {
            /*
            | $abilityScores é enviado pelo PHP usando as chaves curtas:
            | str, dex, con, int, wis, cha.
            |
            | Alguns dados de armadura usam nomes completos.
            | Normalizamos os dois formatos para a mesma chave curta.
            */
            const map = {
                str: 'str',
                strength: 'str',

                dex: 'dex',
                dexterity: 'dex',

                con: 'con',
                constitution: 'con',

                int: 'int',
                intelligence: 'int',

                wis: 'wis',
                wisdom: 'wis',

                cha: 'cha',
                charisma: 'cha',
            };

            const normalizedAbility =
                String(
                    ability
                    ?? ''
                ).toLowerCase();

            const key =
                map[
                    normalizedAbility
                ]
                ?? normalizedAbility;

            const rawScore =
                this.abilityScores?.[
                    key
                ];

            const numericScore =
                Number(
                    rawScore
                );

            const score =
                Number.isFinite(
                    numericScore
                )
                    ? numericScore
                    : 10;

            return Math.floor(
                (
                    score
                    - 10
                ) / 2
            );
        },


        get armorBaseBonus() {
            return this.armorMode.reduce(
                (
                    total,
                    ability
                ) =>
                    total
                    + this.abilityModifier(
                        ability
                    ),
                0
            );
        },


        get armorNamedBonusTotal() {
            return this.armorBonuses.reduce(
                (
                    total,
                    bonus
                ) =>
                    total
                    + (
                        parseInt(
                            bonus.value
                        ) || 0
                    ),
                0
            );
        },


        get baseDefenseBodyAc() {
            return (
                10
                + this.armorBaseBonus
            );
        },


        get baseDefenseAc() {
            return (
                this.baseDefenseBodyAc
                + this.armorNamedBonusTotal
            );
        },


        get equippedArmorItems() {
            return this.armorItems.filter(
                item =>
                    !!item.equipped
            );
        },


        get equippedShieldItems() {
            return this.shieldItems.filter(
                item =>
                    !!item.equipped
            );
        },


        get activeArmorItem() {
            if (
                this.equippedArmorItems.length
                === 0
            ) {
                return null;
            }

            return [
                ...this.equippedArmorItems,
            ].sort(
                (
                    a,
                    b
                ) =>
                    this.armorItemBodyAc(
                        b
                    )
                    - this.armorItemBodyAc(
                        a
                    )
            )[0];
        },


        get activeShieldItem() {
            return (
                this.equippedShieldItems[
                    0
                ]
                ?? null
            );
        },


        armorDexContribution(
            item
        ) {
            if (!item) {
                return 0;
            }

            const dexterity =
                this.abilityModifier(
                    'dex'
                );

            if (
                item.dexterity_mode
                === 'full'
            ) {
                return dexterity;
            }

            if (
                item.dexterity_mode
                === 'capped'
            ) {
                const cap =
                    Math.max(
                        0,
                        parseInt(
                            item.dexterity_cap
                        ) || 0
                    );

                return Math.min(
                    dexterity,
                    cap
                );
            }

            return 0;
        },


        armorExtraAbilityBonus(
            item
        ) {
            if (!item) {
                return 0;
            }

            const map = {
                strength: 'str',
                constitution: 'con',
                intelligence: 'int',
                wisdom: 'wis',
                charisma: 'cha',
            };

            return (
                item.ability_modifiers
                ?? []
            ).reduce(
                (
                    total,
                    ability
                ) =>
                    total
                    + this.abilityModifier(
                        map[
                            ability
                        ]
                        ?? ability
                    ),
                0
            );
        },


        armorItemBodyAc(
            item
        ) {
            if (!item) {
                return 0;
            }

            return (
                (
                    parseInt(
                        item.base_ac
                    ) || 10
                )
                + this.armorDexContribution(
                    item
                )
                + this.armorExtraAbilityBonus(
                    item
                )
                + (
                    parseInt(
                        item.magic_bonus
                    ) || 0
                )
            );
        },


        shieldItemBonus(
            item
        ) {
            if (!item) {
                return 0;
            }

            return (
                (
                    parseInt(
                        item.ac_bonus
                    ) || 0
                )
                + (
                    parseInt(
                        item.magic_bonus
                    ) || 0
                )
            );
        },


        get equippedShieldBonus() {
            return this.equippedShieldItems.reduce(
                (
                    total,
                    item
                ) =>
                    total
                    + this.shieldItemBonus(
                        item
                    ),
                0
            );
        },


        get armorBodyAc() {
            return this.activeArmorItem
                ? this.armorItemBodyAc(
                    this.activeArmorItem
                )
                : this.baseDefenseBodyAc;
        },


        get totalAc() {
            return (
                this.armorBodyAc
                + this.equippedShieldBonus
                + this.armorNamedBonusTotal
            );
        },


        get armorDefenseLabel() {
            return this.activeArmorItem
                ? (
                    this.activeArmorItem.armor_type
                    || this.activeArmorItem.name
                    || 'Armadura'
                )
                : 'Defesa Base';
        },


        get baseDefenseFormulaLabel() {
            const labels = {
                str: 'FOR',
                dex: 'DES',
                con: 'CON',
                int: 'INT',
                wis: 'SAB',
                cha: 'CAR',
            };

            const pieces = [
                '10',
                ...this.armorMode.map(
                    ability =>
                        labels[
                            ability
                        ]
                        ?? String(
                            ability
                        ).toUpperCase()
                ),
            ];

            return pieces.join(
                ' + '
            );
        },


        armorFormulaLabel(
            item
        ) {
            if (!item) {
                return '';
            }

            const parts = [
                `Base ${parseInt(item.base_ac) || 0}`,
            ];

            if (
                item.dexterity_mode
                === 'full'
            ) {
                parts.push(
                    'DES completa'
                );
            } else if (
                item.dexterity_mode
                === 'capped'
            ) {
                parts.push(
                    `DES máx. +${Math.max(0, parseInt(item.dexterity_cap) || 0)}`
                );
            } else {
                parts.push(
                    'Sem DES'
                );
            }

            const abilityLabels = {
                strength: 'FOR',
                constitution: 'CON',
                intelligence: 'INT',
                wisdom: 'SAB',
                charisma: 'CAR',
            };

            (
                item.ability_modifiers
                ?? []
            ).forEach(
                ability => {
                    if (
                        abilityLabels[
                            ability
                        ]
                    ) {
                        parts.push(
                            abilityLabels[
                                ability
                            ]
                        );
                    }
                }
            );

            const magic =
                parseInt(
                    item.magic_bonus
                ) || 0;

            if (
                magic !== 0
            ) {
                parts.push(
                    `Mágico ${magic > 0 ? '+' : ''}${magic}`
                );
            }

            return parts.join(
                ' · '
            );
        },


        shieldFormulaLabel(
            item
        ) {
            if (!item) {
                return '';
            }

            const base =
                parseInt(
                    item.ac_bonus
                ) || 0;

            const magic =
                parseInt(
                    item.magic_bonus
                ) || 0;

            return (
                `Base ${base >= 0 ? '+' : ''}${base}`
                + ` · Mágico ${magic >= 0 ? '+' : ''}${magic}`
                + ` · Total +${base + magic}`
            );
        },


        /*
        |--------------------------------------------------------------------------
        | PERSISTÊNCIA DE COMBATE
        |--------------------------------------------------------------------------
        */

        async persistCombat(fields) {

            /*
            |--------------------------------------------------------------------------
            | MERGE DE OVERRIDES
            |--------------------------------------------------------------------------
            |
            | CharacterCombatController persiste o array completo de overrides.
            | Por isso, quando um componente envia somente o próprio namespace,
            | preservamos os namespaces que já existiam.
            |
            */

            if (
                Object.prototype.hasOwnProperty.call(
                    fields,
                    'overrides'
                )
            ) {

                let incomingOverrides =
                    fields.overrides;

                if (
                    typeof incomingOverrides ===
                    'string'
                ) {

                    try {

                        incomingOverrides =
                            JSON.parse(
                                incomingOverrides
                            );

                    } catch (error) {

                        incomingOverrides = {};
                    }
                }

                if (
                    !incomingOverrides ||
                    typeof incomingOverrides !==
                        'object' ||
                    Array.isArray(
                        incomingOverrides
                    )
                ) {

                    incomingOverrides = {};
                }

                this.combatOverrides = {
                    ...(
                        this.combatOverrides ||
                        {}
                    ),

                    ...incomingOverrides
                };

                fields = {
                    ...fields,

                    overrides:
                        JSON.stringify(
                            this.combatOverrides
                        )
                };
            }


            const formData =
                new FormData();

            formData.append(
                '_token',
                '{{ csrf_token() }}'
            );

            formData.append(
                '_method',
                'PATCH'
            );

            Object.entries(fields).forEach(
                ([key, value]) => {

                    formData.append(
                        key,
                        value
                    );

                }
            );

            try {

                const response =
                    await fetch(
                        '{{ route('characters.combat.update', $character) }}',
                        {
                            method: 'POST',

                            body:
                                formData,

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {
                    throw new Error(
                        'Falha ao atualizar combate.'
                    );
                }


                const data =
                    await response.json();


                /*
                |--------------------------------------------------------------------------
                | SINCRONIZA OVERRIDES RETORNADOS
                |--------------------------------------------------------------------------
                */

                let serverOverrides =
                    data?.combat?.overrides;

                if (
                    typeof serverOverrides ===
                    'string'
                ) {

                    try {

                        serverOverrides =
                            JSON.parse(
                                serverOverrides
                            );

                    } catch (error) {

                        serverOverrides = null;
                    }
                }

                if (
                    serverOverrides &&
                    typeof serverOverrides ===
                        'object' &&
                    !Array.isArray(
                        serverOverrides
                    )
                ) {

                    this.combatOverrides =
                        serverOverrides;
                }


                return data;

            } catch (error) {

                console.error(
                    'Erro ao atualizar combate:',
                    error
                );

                return null;
            }
        },



        /*
        |--------------------------------------------------------------------------
        | APLICA RESPOSTA DO DESCANSO
        |--------------------------------------------------------------------------
        */

        applyRestCombatState(combat) {

            if (!combat) {
                return;
            }

            if (
                combat.current_hp !== undefined &&
                combat.current_hp !== null
            ) {
                this.currentHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.current_hp
                        ) || 0
                    );
            }


            if (
                combat.max_hp !== undefined &&
                combat.max_hp !== null
            ) {
                this.maxHp =
                    Math.max(
                        1,
                        parseInt(
                            combat.max_hp
                        ) || 1
                    );
            }


            if (
                combat.temporary_hp !== undefined &&
                combat.temporary_hp !== null
            ) {
                this.temporaryHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.temporary_hp
                        ) || 0
                    );
            }


            if (
                combat.temporary_max_hp !== undefined &&
                combat.temporary_max_hp !== null
            ) {
                this.temporaryMaxHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.temporary_max_hp
                        ) || 0
                    );
            }


            if (
                combat.death_save_successes !== undefined &&
                combat.death_save_successes !== null
            ) {
                this.deathSaveSuccesses =
                    Math.min(
                        this.deathSuccessLimit,
                        Math.max(
                            0,
                            parseInt(
                                combat.death_save_successes
                            ) || 0
                        )
                    );
            }


            if (
                combat.death_save_failures !== undefined &&
                combat.death_save_failures !== null
            ) {
                this.deathSaveFailures =
                    Math.min(
                        this.deathFailureLimit,
                        Math.max(
                            0,
                            parseInt(
                                combat.death_save_failures
                            ) || 0
                        )
                    );
            }


            if (
                combat.exhaustion_level !== undefined &&
                combat.exhaustion_level !== null
            ) {
                this.exhaustionLevel =
                    Math.min(
                        6,
                        Math.max(
                            0,
                            parseInt(
                                combat.exhaustion_level
                            ) || 0
                        )
                    );
            }


            let returnedOverrides =
                combat.overrides;

            if (
                typeof returnedOverrides ===
                'string'
            ) {
                try {
                    returnedOverrides =
                        JSON.parse(
                            returnedOverrides
                        );
                } catch (error) {
                    returnedOverrides = null;
                }
            }

            if (
                returnedOverrides &&
                typeof returnedOverrides ===
                    'object' &&
                !Array.isArray(
                    returnedOverrides
                )
            ) {
                this.combatOverrides =
                    returnedOverrides;
            }


            /*
            |--------------------------------------------------------------------------
            | SINCRONIZA INPUTS
            |--------------------------------------------------------------------------
            */

            this.directHp =
                this.currentHp;

            this.ghostHp =
                this.currentHp;

            this.directTempHp =
                this.temporaryHp;

            this.directMaxHp =
                this.maxHp;

            this.directTemporaryMaxHp =
                this.temporaryMaxHp;


            /*
            |--------------------------------------------------------------------------
            | MORTE
            |--------------------------------------------------------------------------
            */

            if (this.currentHp > 0) {

                this.deathDrawerOpen = false;
                this.deathRollResult = null;

            }
        },


        /*
        |--------------------------------------------------------------------------
        | DESCANSO
        |--------------------------------------------------------------------------
        */

        async performRest(type) {

            if (this.resting) {
                return null;
            }

            if (
                type !== 'short' &&
                type !== 'long'
            ) {
                return null;
            }


            this.resting = true;
            this.restingType = type;
            this.restError = null;


            try {

                const response =
                    await fetch(
                        '{{ route('characters.rest', $character) }}',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            body:
                                JSON.stringify({
                                    type: type
                                })
                        }
                    );


                let data = null;

                try {
                    data =
                        await response.json();
                } catch (error) {
                    data = null;
                }


                if (!response.ok) {

                    throw new Error(
                        data?.message ??
                        'Não foi possível realizar o descanso.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ATUALIZA COMBATE
                |--------------------------------------------------------------------------
                */

                this.applyRestCombatState(
                    data?.combat
                );


                /*
                |--------------------------------------------------------------------------
                | ANIMAÇÃO
                |--------------------------------------------------------------------------
                */

                if (type === 'long') {

                    this.flashingGreen = true;

                    setTimeout(() => {
                        this.flashingGreen = false;
                    }, 900);
                }


                /*
                |--------------------------------------------------------------------------
                | EVENTO GLOBAL
                |--------------------------------------------------------------------------
                |
                | Todos os módulos da ficha podem ouvir:
                |
                | @character-rest-completed.window
                |
                | e ler:
                |
                | $event.detail.rest.type
                | $event.detail.combat
                |
                */

                window.dispatchEvent(
                    new CustomEvent(
                        'character-rest-completed',
                        {
                            detail: data
                        }
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | EVENTOS ESPECÍFICOS
                |--------------------------------------------------------------------------
                */

                window.dispatchEvent(
                    new CustomEvent(
                        type === 'long'
                            ? 'character-long-rest-completed'
                            : 'character-short-rest-completed',
                        {
                            detail: data
                        }
                    )
                );


                return data;

            } catch (error) {

                console.error(
                    'Erro ao realizar descanso:',
                    error
                );

                this.restError =
                    error?.message ??
                    'Não foi possível realizar o descanso.';

                return null;

            } finally {

                this.resting = false;
                this.restingType = null;
            }
        },


        /*
        |--------------------------------------------------------------------------
        | DEATH SAVES
        |--------------------------------------------------------------------------
        */

        openDeathDrawer() {
            if (!this.isDying) {
                return;
            }

            this.deathDrawerOpen =
                true;
        },


        closeDeathDrawer() {
            this.deathDrawerOpen =
                false;

            this.deathRollResult =
                null;

            this.deathRollPreview =
                null;

            this.deathRollSettled =
                false;
        },


        triggerStabilizationEffect() {
            this.stabilizationEffect =
                true;

            setTimeout(
                () => {
                    this.stabilizationEffect =
                        false;
                },
                2200
            );
        },


        async setDeathSave(
            type,
            index
        ) {
            if (!this.isDying) {
                return;
            }

            const isSuccess =
                type === 'success';

            const key =
                isSuccess
                    ? 'deathSaveSuccesses'
                    : 'deathSaveFailures';

            const limit =
                isSuccess
                    ? this.deathSuccessLimit
                    : this.deathFailureLimit;

            const normalizedIndex =
                Math.max(
                    0,
                    Math.min(
                        limit,
                        parseInt(
                            index
                        ) || 0
                    )
                );

            if (
                this[key] ===
                normalizedIndex
            ) {
                this[key] =
                    Math.max(
                        0,
                        normalizedIndex - 1
                    );
            } else {
                this[key] =
                    normalizedIndex;
            }

            await this.persistCombat({
                death_save_successes:
                    this.deathSaveSuccesses,

                death_save_failures:
                    this.deathSaveFailures,
            });

            if (this.isStable) {
                this.triggerStabilizationEffect();

                this.deathDrawerOpen =
                    false;
            }

            if (this.isDead) {
                this.deathDrawerOpen =
                    false;
            }
        },


        async rollDeathSave() {
            if (
                !this.isDying ||
                this.deathRolling
            ) {
                return;
            }

            this.deathRolling =
                true;

            this.deathRollResult =
                null;

            this.deathRollSettled =
                false;

            this.deathRollPreview =
                Math.floor(
                    Math.random() * 20
                )
                + 1;

            /*
            |--------------------------------------------------------------------------
            | SUSPENSE
            |--------------------------------------------------------------------------
            |
            | O resultado real é solicitado imediatamente, mas não é revelado
            | antes de aproximadamente 3 segundos.
            |
            | Enquanto isso, mostramos números falsos rapidamente. Isso é
            | somente apresentação: nenhuma falha/sucesso é aplicada antes do
            | valor verdadeiro ser revelado.
            |
            */

            const startedAt =
                Date.now();

            const previewTimer =
                setInterval(
                    () => {
                        this.deathRollPreview =
                            Math.floor(
                                Math.random() * 20
                            )
                            + 1;
                    },
                    85
                );

            let roll = 1;

            try {
                const response =
                    await fetch(
                        '{{ url('/api/roll') }}',
                        {
                            method:
                                'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body:
                                JSON.stringify({
                                    expression:
                                        '1d20',
                                }),
                        }
                    );

                if (response.ok) {
                    const data =
                        await response.json();

                    roll =
                        parseInt(
                            data?.data?.total
                            ?? data?.data?.result
                            ?? data?.data?.value
                            ?? data?.data?.sum
                            ?? data?.total
                            ?? data?.result
                            ?? 1
                        )
                        || 1;
                } else {
                    roll =
                        Math.floor(
                            Math.random() * 20
                        )
                        + 1;
                }

            } catch (error) {
                roll =
                    Math.floor(
                        Math.random() * 20
                    )
                    + 1;
            }

            /*
             * Garante que a tensão dure pelo menos 3 segundos,
             * mesmo que o endpoint responda instantaneamente.
             */
            const elapsed =
                Date.now() - startedAt;

            const remaining =
                Math.max(
                    0,
                    3000 - elapsed
                );

            await new Promise(
                resolve =>
                    setTimeout(
                        resolve,
                        remaining
                    )
            );

            clearInterval(
                previewTimer
            );

            /*
             * Agora o número assenta no valor verdadeiro.
             */
            this.deathRollPreview =
                roll;

            this.deathRollResult =
                roll;

            this.deathRollSettled =
                true;

            /*
             * Deixa o resultado final respirar antes de alterar os contadores
             * ou fechar a gaveta por estabilização/morte.
             */
            await new Promise(
                resolve =>
                    setTimeout(
                        resolve,
                        850
                    )
            );


            const exhaustionAdjustedRoll =
                roll
                - this.exhaustionRollPenalty;


            if (roll === 20) {
                this.currentHp =
                    1;

                this.directHp =
                    1;

                this.ghostHp =
                    1;

                this.deathSaveSuccesses =
                    0;

                this.deathSaveFailures =
                    0;

                await this.persistCombat({
                    current_hp:
                        1,

                    death_save_successes:
                        0,

                    death_save_failures:
                        0,
                });

                this.flashingGreen =
                    true;

                setTimeout(() => {
                    this.flashingGreen =
                        false;
                }, 900);

                this.deathDrawerOpen =
                    false;

            } else if (roll === 1) {
                /*
                 * 1 natural continua valendo duas falhas.
                 * A Regra Morquen altera crítico recebido, não o 1 natural.
                 */
                this.deathSaveFailures =
                    Math.min(
                        this.deathFailureLimit,
                        this.deathSaveFailures +
                            2
                    );

                await this.persistCombat({
                    death_save_failures:
                        this.deathSaveFailures,
                });

                if (this.isDead) {
                    this.deathDrawerOpen =
                        false;
                }

            } else if (exhaustionAdjustedRoll >= 10) {
                this.deathSaveSuccesses =
                    Math.min(
                        this.deathSuccessLimit,
                        this.deathSaveSuccesses +
                            1
                    );

                await this.persistCombat({
                    death_save_successes:
                        this.deathSaveSuccesses,
                });

                if (this.isStable) {
                    this.triggerStabilizationEffect();

                    this.deathDrawerOpen =
                        false;
                }

            } else {
                this.deathSaveFailures =
                    Math.min(
                        this.deathFailureLimit,
                        this.deathSaveFailures +
                            1
                    );

                await this.persistCombat({
                    death_save_failures:
                        this.deathSaveFailures,
                });

                if (this.isDead) {
                    this.deathDrawerOpen =
                        false;
                }
            }

            this.deathRolling =
                false;
        },


        /*
        |--------------------------------------------------------------------------
        | MORTE PADRÃO — SEQUÊNCIA DE ESPERANÇA
        |--------------------------------------------------------------------------
        */

        syncSheetDeathVisual(dead) {
            const sheet =
                this.$root.closest(
                    '.character-sheet-page'
                );

            if (!sheet) {
                return;
            }

            sheet.classList.toggle(
                'character-sheet-dead',
                !!dead
            );
        },


        clearDeathSequence() {
            if (this.deathHopeTimer) {
                clearTimeout(
                    this.deathHopeTimer
                );

                this.deathHopeTimer =
                    null;
            }

            if (this.deathReviveTimer) {
                clearTimeout(
                    this.deathReviveTimer
                );

                this.deathReviveTimer =
                    null;
            }

            this.deathSequenceStarted =
                false;

            this.deathHopeVisible =
                false;

            this.deathReviveVisible =
                false;

            this.deathReviveError =
                null;
        },


        syncDeathSequence(dead) {
            /*
             * Determinação Final já é o caminho de retorno da Regra Morquen.
             * A sequência abaixo existe para a morte padrão.
             */
            if (
                !dead ||
                this.morquenRuleActive
            ) {
                if (
                    this.deathSequenceStarted ||
                    this.deathHopeVisible ||
                    this.deathReviveVisible
                ) {
                    this.clearDeathSequence();
                }

                return;
            }

            if (this.deathSequenceStarted) {
                return;
            }

            this.deathSequenceStarted =
                true;

            this.deathHopeVisible =
                false;

            this.deathReviveVisible =
                false;

            this.deathReviveError =
                null;


            /*
             * 3 segundos:
             * a primeira fresta de esperança aparece.
             */
            this.deathHopeTimer =
                setTimeout(
                    () => {
                        if (!this.isDead) {
                            return;
                        }

                        this.deathHopeVisible =
                            true;

                        this.deathHopeTimer =
                            null;
                    },
                    3000
                );


            /*
             * Mais 2 segundos:
             * o caminho manual para retornar aparece.
             */
            this.deathReviveTimer =
                setTimeout(
                    () => {
                        if (!this.isDead) {
                            return;
                        }

                        this.deathReviveVisible =
                            true;

                        this.deathReviveTimer =
                            null;
                    },
                    5000
                );
        },


        async reviveManually() {
            if (
                this.morquenRuleActive ||
                !this.isDead ||
                !this.deathReviveVisible ||
                this.deathReviving
            ) {
                return;
            }

            this.deathReviving =
                true;

            this.deathReviveError =
                null;

            const data =
                await this.persistCombat({
                    current_hp:
                        1,

                    death_save_successes:
                        0,

                    death_save_failures:
                        0,
                });

            if (!data) {
                this.deathReviveError =
                    'Não foi possível reviver o personagem.';

                this.deathReviving =
                    false;

                return;
            }

            this.currentHp =
                1;

            this.directHp =
                1;

            this.ghostHp =
                1;

            this.deathSaveSuccesses =
                0;

            this.deathSaveFailures =
                0;

            this.deathDrawerOpen =
                false;

            this.deathRollResult =
                null;

            this.deathRollPreview =
                null;

            this.deathRollSettled =
                false;

            this.clearDeathSequence();

            this.flashingGreen =
                true;

            setTimeout(
                () => {
                    this.flashingGreen =
                        false;
                },
                1200
            );

            this.deathReviving =
                false;
        },


        /*
        |--------------------------------------------------------------------------
        | DETERMINAÇÃO FINAL
        |--------------------------------------------------------------------------
        */

        async reviveWithMorquen() {
            if (
                !this.canUseMorquenDetermination ||
                this.morquenReviving
            ) {
                return;
            }

            this.morquenReviving =
                true;

            this.morquenReviveError =
                null;

            try {
                const response =
                    await fetch(
                        this.morquenReviveUrl,
                        {
                            method:
                                'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    );

                const data =
                    await response
                        .json()
                        .catch(
                            () => ({})
                        );

                if (!response.ok) {
                    const messages =
                        data?.errors
                            ? Object.values(
                                data.errors
                            )
                                .flat()
                                .filter(
                                    Boolean
                                )
                            : [];

                    throw new Error(
                        messages.length
                            ? messages.join(
                                ' '
                            )
                            : (
                                data?.message
                                ?? 'Não foi possível usar Determinação Final.'
                            )
                    );
                }

                const combat =
                    data?.combat
                    ?? {};

                this.currentHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.current_hp
                        ) || 0
                    );

                this.directHp =
                    this.currentHp;

                this.ghostHp =
                    this.currentHp;

                this.temporaryHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.temporary_hp
                        ) || 0
                    );

                this.directTempHp =
                    this.temporaryHp;

                this.deathSaveSuccesses =
                    Math.max(
                        0,
                        parseInt(
                            combat.death_save_successes
                        ) || 0
                    );

                this.deathSaveFailures =
                    Math.max(
                        0,
                        parseInt(
                            combat.death_save_failures
                        ) || 0
                    );

                this.exhaustionLevel =
                    Math.min(
                        6,
                        Math.max(
                            0,
                            parseInt(
                                combat.exhaustion_level
                            ) || 0
                        )
                    );

                let returnedOverrides =
                    combat.overrides;

                if (
                    typeof returnedOverrides ===
                    'string'
                ) {
                    try {
                        returnedOverrides =
                            JSON.parse(
                                returnedOverrides
                            );
                    } catch (error) {
                        returnedOverrides =
                            null;
                    }
                }

                if (
                    returnedOverrides &&
                    typeof returnedOverrides ===
                        'object' &&
                    !Array.isArray(
                        returnedOverrides
                    )
                ) {
                    this.combatOverrides =
                        returnedOverrides;
                }

                this.deathDrawerOpen =
                    false;

                this.deathRollResult =
                    null;

                this.flashingGreen =
                    true;

                setTimeout(() => {
                    this.flashingGreen =
                        false;
                }, 1200);

            } catch (error) {
                console.error(
                    'Erro em Determinação Final:',
                    error
                );

                this.morquenReviveError =
                    error?.message
                    ?? 'Não foi possível voltar à vida.';

            } finally {
                this.morquenReviving =
                    false;
            }
        },


        /*
        |--------------------------------------------------------------------------
        | VIDA
        |--------------------------------------------------------------------------
        */

        changeHp(amount) {

            if (amount < 0) {

                let damage =
                    Math.abs(amount);

                const wasAtZero =
                    this.currentHp <= 0;

                /*
                 * Qualquer dano sofrido enquanto já está com 0 PV
                 * causa uma falha em death save.
                 *
                 * O controle simples de PV não conhece a origem do dano.
                 * Se for um crítico sem Regra Morquen, o jogador pode marcar
                 * manualmente a falha adicional nas bolinhas da gaveta.
                 * Com Morquen, o +1 automático já é o total correto do crítico.
                 */
                if (
                    wasAtZero &&
                    damage > 0 &&
                    !this.isDead
                ) {
                    if (this.isStable) {
                        this.deathSaveSuccesses =
                            0;
                    }

                    this.deathSaveFailures =
                        Math.min(
                            this.deathFailureLimit,
                            this.deathSaveFailures +
                                1
                        );

                    this.deathRollResult =
                        null;
                }


                if (this.temporaryHp > 0) {

                    const absorbed =
                        Math.min(
                            this.temporaryHp,
                            damage
                        );

                    this.temporaryHp -= absorbed;
                    damage -= absorbed;

                    this.directTempHp =
                        this.temporaryHp;

                    this.shakingBlue =
                        true;


                    setTimeout(() => {
                        this.shakingBlue = false;
                    }, 700);
                }


                if (damage > 0) {

                    const wasAlive =
                        this.currentHp > 0;


                    this.currentHp =
                        Math.max(
                            0,
                            this.currentHp -
                            damage
                        );


                    this.directHp =
                        this.currentHp;


                    this.shakingRed =
                        true;


                    setTimeout(() => {
                        this.shakingRed = false;
                    }, 700);


                    if (
                        wasAlive &&
                        this.currentHp === 0
                    ) {

                        this.deathRollResult =
                            null;

                        this.deathDrawerOpen =
                            true;
                    }
                }


                setTimeout(() => {

                    this.ghostHp =
                        this.currentHp;

                }, 350);


                if (
                    wasAtZero &&
                    !this.isDead
                ) {
                    this.deathDrawerOpen =
                        true;
                }

                if (this.isDead) {
                    this.deathDrawerOpen =
                        false;
                }


                this.persistCombat({
                    current_hp:
                        this.currentHp,

                    temporary_hp:
                        this.temporaryHp,

                    death_save_successes:
                        this.deathSaveSuccesses,

                    death_save_failures:
                        this.deathSaveFailures,
                });


                return;
            }


            if (amount > 0) {

                this.currentHp =
                    Math.min(
                        this.effectiveMaxHp,
                        this.currentHp +
                        amount
                    );


                this.directHp =
                    this.currentHp;

                this.ghostHp =
                    this.currentHp;


                if (this.currentHp > 0) {

                    this.deathSaveSuccesses = 0;
                    this.deathSaveFailures = 0;

                    this.deathDrawerOpen = false;
                    this.deathRollResult = null;
                }


                this.flashingGreen =
                    true;


                setTimeout(() => {
                    this.flashingGreen = false;
                }, 800);


                this.persistCombat({
                    current_hp:
                        this.currentHp,

                    death_save_successes:
                        this.deathSaveSuccesses,

                    death_save_failures:
                        this.deathSaveFailures
                });
            }
        },


        async handleDirectHp() {

            if (!this.editingHp) {
                return;
            }


            this.editingHp =
                false;


            let targetHp =
                parseInt(
                    this.directHp
                );


            if (Number.isNaN(targetHp)) {

                this.directHp =
                    this.currentHp;

                return;
            }


            targetHp =
                Math.max(
                    0,
                    Math.min(
                        this.effectiveMaxHp,
                        targetHp
                    )
                );


            if (
                targetHp ===
                this.currentHp
            ) {
                return;
            }


            const wasDowned =
                this.isDowned;


            if (
                targetHp <
                this.currentHp
            ) {

                this.shakingRed =
                    true;


                setTimeout(() => {
                    this.shakingRed = false;
                }, 700);

            } else {

                this.flashingGreen =
                    true;


                setTimeout(() => {
                    this.flashingGreen = false;
                }, 800);
            }


            this.currentHp =
                targetHp;

            this.directHp =
                targetHp;

            this.ghostHp =
                targetHp;


            if (
                wasDowned &&
                targetHp > 0
            ) {

                this.deathSaveSuccesses = 0;
                this.deathSaveFailures = 0;
                this.deathDrawerOpen = false;
                this.deathRollResult = null;
            }


            if (targetHp === 0) {

                this.deathRollResult =
                    null;

                this.deathDrawerOpen =
                    true;
            }


            await this.persistCombat({
                current_hp:
                    this.currentHp,

                death_save_successes:
                    this.deathSaveSuccesses,

                death_save_failures:
                    this.deathSaveFailures
            });
        },


        async handleTemporaryHp() {

            if (!this.editingTempHp) {
                return;
            }


            this.editingTempHp =
                false;


            let value =
                parseInt(
                    this.directTempHp
                );


            if (Number.isNaN(value)) {
                value = 0;
            }


            this.temporaryHp =
                Math.max(
                    0,
                    value
                );


            await this.persistCombat({
                temporary_hp:
                    this.temporaryHp
            });


            this.shakingBlue =
                true;


            setTimeout(() => {
                this.shakingBlue = false;
            }, 700);
        },


        /*
        |--------------------------------------------------------------------------
        | CONFIGURAÇÃO DE VIDA
        |--------------------------------------------------------------------------
        */

        openHpSettings() {

            this.directMaxHp =
                this.maxHp;

            this.directTemporaryMaxHp =
                this.temporaryMaxHp;

            this.hpSettingsStep =
                'confirm';

            this.hpSettingsOpen =
                true;
        },


        beginHpSettings() {

            this.hpSettingsStep =
                'edit';


            this.$nextTick(() => {

                this.$refs
                    .maxHpSettingsInput
                    ?.focus();

                this.$refs
                    .maxHpSettingsInput
                    ?.select();
            });
        },


        closeHpSettings() {

            this.hpSettingsOpen = false;

            this.hpSettingsStep =
                'confirm';

            this.directMaxHp =
                this.maxHp;

            this.directTemporaryMaxHp =
                this.temporaryMaxHp;
        },


        async saveHpSettings() {

            let newMaxHp =
                parseInt(
                    this.directMaxHp
                );

            let newTemporaryMaxHp =
                parseInt(
                    this.directTemporaryMaxHp
                );


            if (Number.isNaN(newMaxHp)) {

                newMaxHp =
                    this.maxHp;
            }


            if (
                Number.isNaN(
                    newTemporaryMaxHp
                )
            ) {

                newTemporaryMaxHp =
                    this.temporaryMaxHp;
            }


            newMaxHp =
                Math.max(
                    1,
                    newMaxHp
                );


            newTemporaryMaxHp =
                Math.max(
                    0,
                    newTemporaryMaxHp
                );


            const extraHpDifference =
                newTemporaryMaxHp -
                this.temporaryMaxHp;


            this.maxHp =
                newMaxHp;


            this.temporaryMaxHp =
                newTemporaryMaxHp;


            if (
                extraHpDifference > 0
            ) {

                this.currentHp =
                    Math.min(
                        this.effectiveMaxHp,
                        this.currentHp +
                        extraHpDifference
                    );
            }


            if (
                this.currentHp >
                this.effectiveMaxHp
            ) {

                this.currentHp =
                    this.effectiveMaxHp;
            }


            this.directHp =
                this.currentHp;

            this.ghostHp =
                this.currentHp;


            await this.persistCombat({
                max_hp:
                    this.maxHp,

                temporary_max_hp:
                    this.temporaryMaxHp,

                current_hp:
                    this.currentHp
            });


            if (
                extraHpDifference > 0
            ) {

                this.flashingGreen =
                    true;


                setTimeout(() => {
                    this.flashingGreen = false;
                }, 800);
            }


            this.closeHpSettings();
        },


        /*
        |--------------------------------------------------------------------------
        | DEFESA / EQUIPAMENTO
        |--------------------------------------------------------------------------
        */

        openArmor() {
            this.armorEquipmentError =
                null;

            this.armorOpen =
                true;
        },


        normalizeArmorEquipmentItem(
            raw
        ) {
            const armor =
                raw?.properties?.armor;

            if (!armor) {
                return null;
            }

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
                    category === 'light'
                        ? 'full'
                        : category === 'medium'
                            ? 'capped'
                            : 'none';
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
                        )
                    ]
                    : [];

            return {
                id:
                    parseInt(
                        raw.id
                    ),

                name:
                    raw.name
                    ?? 'Armadura',

                image_url:
                    raw.image_url
                    ?? null,

                equipped:
                    !!raw.equipped,

                rarity_label:
                    raw.rarity_label
                    ?? null,

                category,

                category_custom:
                    armor.category_custom
                    ?? null,

                armor_type:
                    String(
                        armor.armor_type
                        ?? 'Armadura'
                    ).trim(),

                base_ac:
                    parseInt(
                        armor.base_ac
                    ) || 0,

                magic_bonus:
                    parseInt(
                        armor.magic_bonus
                    ) || 0,

                dexterity_mode:
                    dexterityMode,

                dexterity_cap:
                    Math.max(
                        0,
                        parseInt(
                            armor.dexterity_cap
                        ) || 0
                    ),

                ability_modifiers:
                    abilityModifiers,

                equipped_url:
                    this.defensiveEquipUrl.replace(
                        '__ITEM__',
                        raw.id
                    ),
            };
        },


        normalizeShieldEquipmentItem(
            raw
        ) {
            const shield =
                raw?.properties?.shield;

            if (!shield) {
                return null;
            }

            return {
                id:
                    parseInt(
                        raw.id
                    ),

                name:
                    raw.name
                    ?? 'Escudo',

                image_url:
                    raw.image_url
                    ?? null,

                equipped:
                    !!raw.equipped,

                rarity_label:
                    raw.rarity_label
                    ?? null,

                label:
                    String(
                        shield.label
                        ?? 'Escudo'
                    ).trim(),

                ac_bonus:
                    parseInt(
                        shield.ac_bonus
                    ) || 0,

                magic_bonus:
                    parseInt(
                        shield.magic_bonus
                    ) || 0,

                equipped_url:
                    this.defensiveEquipUrl.replace(
                        '__ITEM__',
                        raw.id
                    ),
            };
        },


        syncDefenseEquipment(
            raw
        ) {
            if (!raw?.id) {
                return;
            }

            const id =
                parseInt(
                    raw.id
                );

            if (raw.deleted) {
                this.armorItems =
                    this.armorItems.filter(
                        item =>
                            parseInt(
                                item.id
                            ) !== id
                    );

                this.shieldItems =
                    this.shieldItems.filter(
                        item =>
                            parseInt(
                                item.id
                            ) !== id
                    );

                return;
            }

            const armor =
                this.normalizeArmorEquipmentItem(
                    raw
                );

            const shield =
                this.normalizeShieldEquipmentItem(
                    raw
                );

            const armorIndex =
                this.armorItems.findIndex(
                    item =>
                        parseInt(
                            item.id
                        ) === id
                );

            if (armor) {
                if (armorIndex >= 0) {
                    this.armorItems[
                        armorIndex
                    ] = armor;
                } else {
                    this.armorItems.push(
                        armor
                    );
                }
            } else if (
                armorIndex >= 0
            ) {
                this.armorItems.splice(
                    armorIndex,
                    1
                );
            }

            const shieldIndex =
                this.shieldItems.findIndex(
                    item =>
                        parseInt(
                            item.id
                        ) === id
                );

            if (shield) {
                if (shieldIndex >= 0) {
                    this.shieldItems[
                        shieldIndex
                    ] = shield;
                } else {
                    this.shieldItems.push(
                        shield
                    );
                }
            } else if (
                shieldIndex >= 0
            ) {
                this.shieldItems.splice(
                    shieldIndex,
                    1
                );
            }
        },


        async persistDefenseEquipment(
            item,
            equipped
        ) {
            if (!item) {
                return null;
            }

            const equippedUrl =
                item.equipped_url
                || this.defensiveEquipUrl.replace(
                    '__ITEM__',
                    item.id
                );

            const response =
                await fetch(
                    equippedUrl,
                    {
                        method:
                            'PATCH',

                        headers: {
                            'Accept':
                                'application/json',

                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':
                                document
                                    .querySelector(
                                        'meta[name=csrf-token]'
                                    )
                                    ?.getAttribute(
                                        'content'
                                    )
                                ?? '{{ csrf_token() }}',

                            'X-Requested-With':
                                'XMLHttpRequest',
                        },

                        body:
                            JSON.stringify({
                                equipped:
                                    !!equipped,
                            }),
                    }
                );

            const data =
                await response
                    .json()
                    .catch(
                        () => ({})
                    );

            if (!response.ok) {
                throw new Error(
                    data?.message
                    ?? 'Não foi possível alterar o equipamento.'
                );
            }

            this.syncDefenseEquipment(
                data.item
            );

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

            return data.item;
        },


        async toggleArmorItem(
            item
        ) {
            if (
                this.armorEquipmentBusyId
                !== null
                || !item
            ) {
                return;
            }

            this.armorEquipmentBusyId =
                item.id;

            this.armorEquipmentError =
                null;

            try {
                const shouldEquip =
                    !item.equipped;

                if (shouldEquip) {
                    const others =
                        this.armorItems.filter(
                            other =>
                                !!other.equipped
                                && parseInt(
                                    other.id
                                ) !==
                                parseInt(
                                    item.id
                                )
                        );

                    for (
                        const other
                        of others
                    ) {
                        await this.persistDefenseEquipment(
                            other,
                            false
                        );
                    }
                }

                await this.persistDefenseEquipment(
                    item,
                    shouldEquip
                );
            } catch (error) {
                this.armorEquipmentError =
                    error?.message
                    ?? 'Não foi possível alterar a armadura.';
            } finally {
                this.armorEquipmentBusyId =
                    null;
            }
        },


        async toggleShieldItem(
            item
        ) {
            if (
                this.armorEquipmentBusyId
                !== null
                || !item
            ) {
                return;
            }

            this.armorEquipmentBusyId =
                item.id;

            this.armorEquipmentError =
                null;

            try {
                const shouldEquip =
                    !item.equipped;

                if (shouldEquip) {
                    const others =
                        this.shieldItems.filter(
                            other =>
                                !!other.equipped
                                && parseInt(
                                    other.id
                                ) !==
                                parseInt(
                                    item.id
                                )
                        );

                    for (
                        const other
                        of others
                    ) {
                        await this.persistDefenseEquipment(
                            other,
                            false
                        );
                    }
                }

                await this.persistDefenseEquipment(
                    item,
                    shouldEquip
                );
            } catch (error) {
                this.armorEquipmentError =
                    error?.message
                    ?? 'Não foi possível alterar o escudo.';
            } finally {
                this.armorEquipmentBusyId =
                    null;
            }
        },


        addArmorBonus() {
            this.armorBonuses.push({
                name:
                    'Novo bônus',

                value:
                    1,
            });
        },


        removeArmorBonus(
            index
        ) {
            this.armorBonuses.splice(
                index,
                1
            );
        },


        async saveArmor() {
            this.savingArmor =
                true;

            try {
                await this.persistCombat({
                    overrides:
                        JSON.stringify({
                            armor: {
                                mode:
                                    this.armorMode,

                                bonuses:
                                    this.armorBonuses.map(
                                        bonus => ({
                                            name:
                                                bonus.name,

                                            value:
                                                parseInt(
                                                    bonus.value
                                                ) || 0,
                                        })
                                    ),
                            },
                        }),
                });

                this.armorOpen =
                    false;
            } finally {
                this.savingArmor =
                    false;
            }
        }
    }"

    x-effect="
        syncExhaustionGlobal(
            exhaustionLevel
        )
    "

    @character-equipment-updated.window="
        syncDefenseEquipment(
            $event.detail?.item
        )
    "
    :class="{
        'bloodied-card': isBloodied,
        'critical-card': isCritical,
        'hero-hp-modal-open': hpSettingsOpen
    }"

    class="
        relative
        mb-6
        overflow-visible
        rounded-2xl
        border
        border-[#cdbb9f]/60
        bg-[#faf8f2]
        shadow-md
    "
>


    {{-- ============================================================
         HEADER PRINCIPAL
    ============================================================= --}}

    <div class="px-4 py-3 sm:px-5 sm:py-4">

        <div
            class="
                flex
                min-w-0
                flex-col
                gap-3

                xl:flex-row
                xl:items-stretch
                xl:gap-0
            "
        >

            {{-- ====================================================
                 HERO DATA
            ===================================================== --}}

            <div
                class="
                    relative
                    z-20
                    min-w-0
                    flex-1

                    xl:pr-0
                "
            >

                @include(
                    'characters.components.hero-data',
                    [
                        'character' => $character,
                        'imageUrl' => $imageUrl,
                        'className' => $className,
                        'subclassName' => $subclassName,
                        'proficiencyBonus' => $proficiencyBonus,
                        'classes' => $classes,
                        'hasMulticlass' => $hasMulticlass,
                    ]
                )

            </div>


            {{-- ====================================================
                 CA + VIDA + DESCANSO
            ===================================================== --}}

            <div
                class="
                    relative
                    z-10
                    flex
                    min-w-0
                    overflow-visible

                    rounded-xl
                    border
                    border-[#cdbb9f]/70
                    bg-[#faf8f2]

                    shadow-[0_2px_6px_rgba(83,21,15,.05)]

                    xl:-ml-2.5
                    xl:h-[176px]
                    xl:w-[550px]
                    xl:shrink-0
                    xl:rounded-l-none
                    xl:rounded-r-[12px]
                    xl:border-l-0
                "
            >

                {{-- MOLDURA INTERNA --}}

                <div
                    class="
                        pointer-events-none
                        absolute
                        inset-[3px]
                        z-0

                        rounded-[9px]
                        border
                        border-[#d8c7ab]/60

                        xl:rounded-l-none
                        xl:border-l-0
                    "
                ></div>


                {{-- PONTE COM HERO-DATA --}}

                <div
                    class="
                        pointer-events-none
                        absolute
                        -left-3
                        bottom-[3px]
                        top-[3px]
                        hidden
                        w-4
                        bg-[#faf8f2]

                        xl:block
                    "
                ></div>


                {{-- =================================================
                     CLASSE DE ARMADURA
                ================================================== --}}

                <div
                    class="
                        relative
                        z-10
                        flex
                        w-[92px]
                        shrink-0
                        items-start
                        justify-center

                        pb-2
                        pl-2
                        pr-2.5
                        pt-2
                    "
                >

                    <div
                        class="
                            pointer-events-none
                            absolute
                            bottom-3
                            right-0
                            top-3
                            w-px
                            bg-[#d8c7ab]/55
                        "
                    ></div>


                    <div class="relative mt-0.5">

                        @include(
                            'characters.components.hero-armor',
                            [
                                'character' => $character,
                                'armorMode' => $armorMode,
                                'armorBonuses' => $namedAcBonuses,
                                'armorItems' => $armorItemsPayload,
                                'shieldItems' => $shieldItemsPayload,
                            ]
                        )

                    </div>

                </div>


                {{-- =================================================
                     VIDA
                ================================================== --}}

                <div
                    class="
                        relative
                        z-10
                        flex
                        min-w-0
                        flex-1
                        flex-col

                        px-3
                        pb-2
                        sm:px-3.5
                    "
                >

                    <div
                        class="
                            flex
                            min-h-0
                            w-full
                            flex-1
                            flex-col
                        "
                    >

                        {{-- =========================================
                             VIDA + HIT DICE
                        ========================================== --}}

                        <div class="relative w-full pt-2.5">

                            {{-- HIT DICE --}}

                            <div
                                class="
                                    pointer-events-none
                                    relative
                                    z-30
                                    -mb-5
                                    flex
                                    justify-end
                                    pr-0.5
                                "
                            >

                                <div class="pointer-events-auto">
                                    @include(
                                        'characters.components.hero-hit-dice',
                                        [
                                            'character' => $character,
                                            'hitDice' => $hitDice,
                                        ]
                                    )
                                </div>

                            </div>


                            {{-- BARRA DE VIDA --}}

                            <div
                                class="
                                    hero-life-modal-source

                                    relative
                                    z-20
                                    w-full
                                    origin-top
                                "
                            >

                                @include(
                                    'characters.components.hero-life',
                                    [
                                        'character' => $character,
                                        'maxHp' => $maxHp,
                                        'temporaryMaxHp' => $temporaryMaxHp,
                                    ]
                                )

                            </div>

                        </div>


                        {{-- =========================================
                             DADOS RÁPIDOS
                        ========================================== --}}

                        <div
                            class="
                                mt-auto
                                pt-1.5
                            "
                        >

                            @include(
                                'characters.components.hero-quick-stats',
                                [
                                    'character' =>
                                        $character,

                                    'proficiencyBonus' =>
                                        $proficiencyBonus,

                                    'exhaustionLevel' =>
                                        $exhaustionLevel,
                                ]
                            )

                        </div>


                        {{-- =========================================
                             RODAPÉ
                        ========================================== --}}

                        <div
                            class="
                                mt-1.5
                                flex
                                h-7
                                items-center
                                gap-2
                            "
                        >

                            {{-- DECORAÇÃO --}}

                            <div
                                class="
                                    flex
                                    min-w-0
                                    flex-1
                                    items-center
                                "
                            >

                                <div
                                    class="
                                        h-px
                                        flex-1
                                        bg-[#d8c7ab]/45
                                    "
                                ></div>

                                <span
                                    class="
                                        mx-2
                                        h-1
                                        w-1
                                        shrink-0
                                        rounded-full
                                        bg-[#cdbb9f]/80
                                    "
                                ></span>

                                <div
                                    class="
                                        h-px
                                        w-6
                                        bg-[#d8c7ab]/45
                                    "
                                ></div>

                            </div>


                            {{-- =====================================
                                 DESCANSO — V2
                            ====================================== --}}

                            <div
                                x-data="{
                                    restOpen: false,
                                    restMenuStyle: '',

                                    positionRestMenu() {
                                        const trigger =
                                            this.$refs.restTrigger;

                                        if (!trigger) {
                                            return;
                                        }

                                        const rect =
                                            trigger.getBoundingClientRect();

                                        const width = 258;
                                        const estimatedHeight = 238;
                                        const gap = 8;
                                        const margin = 8;

                                        const left =
                                            Math.max(
                                                margin,
                                                Math.min(
                                                    window.innerWidth
                                                    - width
                                                    - margin,
                                                    rect.right
                                                    - width
                                                )
                                            );

                                        let top =
                                            rect.bottom
                                            + gap;

                                        if (
                                            top
                                            + estimatedHeight
                                            > window.innerHeight
                                            - margin
                                            &&
                                            rect.top
                                            - estimatedHeight
                                            - gap
                                            >= margin
                                        ) {
                                            top =
                                                rect.top
                                                - estimatedHeight
                                                - gap;
                                        }

                                        this.restMenuStyle =
                                            `left:${Math.round(left)}px;top:${Math.round(top)}px;width:${width}px;`;
                                    },

                                    toggleRestMenu() {
                                        if (resting) {
                                            return;
                                        }

                                        this.restOpen =
                                            !this.restOpen;

                                        if (this.restOpen) {
                                            this.$nextTick(() => {
                                                this.positionRestMenu();
                                            });
                                        }
                                    }
                                }"

                                @keydown.escape.window="
                                    restOpen = false
                                "

                                @resize.window="
                                    if (restOpen) {
                                        positionRestMenu()
                                    }
                                "

                                @scroll.window="
                                    if (restOpen) {
                                        positionRestMenu()
                                    }
                                "

                                @character-rest-completed.window="
                                    restOpen = false
                                "

                                class="
                                    relative
                                    shrink-0
                                "
                            >

                                {{-- BOTÃO / FOGUEIRA --}}

                                <button
                                    x-ref="restTrigger"

                                    type="button"

                                    @click.stop="
                                        toggleRestMenu()
                                    "

                                    :aria-expanded="
                                        restOpen
                                    "

                                    :disabled="
                                        resting
                                    "

                                    class="
                                        group
                                        relative

                                        flex
                                        h-8
                                        w-8
                                        items-center
                                        justify-center

                                        overflow-hidden

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/85

                                        bg-[linear-gradient(180deg,#fffdf8_0%,#f3e9dc_100%)]

                                        text-[#6b1d14]

                                        shadow-[0_2px_5px_rgba(83,21,15,.07),inset_0_1px_0_rgba(255,255,255,.80)]

                                        transition-all
                                        duration-150

                                        hover:-translate-y-[1px]
                                        hover:border-[#b08c62]/75
                                        hover:bg-[linear-gradient(180deg,#fffdf8_0%,#eadbc8_100%)]
                                        hover:shadow-[0_4px_8px_rgba(83,21,15,.10),inset_0_1px_0_rgba(255,255,255,.84)]

                                        active:translate-y-0
                                        active:scale-[.97]

                                        disabled:cursor-wait
                                        disabled:opacity-60
                                    "

                                    :class="{
                                        'border-[#a0774d]/70 bg-[#eadbc8]':
                                            restOpen
                                    }"

                                    title="Descanso"
                                    aria-label="Abrir opções de descanso"
                                >
                                    {{-- brilho quente discreto --}}
                                    <span
                                        class="
                                            pointer-events-none
                                            absolute
                                            bottom-[-9px]
                                            left-1/2

                                            h-5
                                            w-5

                                            -translate-x-1/2

                                            rounded-full
                                            bg-[#c58b4a]/15
                                            blur-[5px]

                                            transition
                                            group-hover:bg-[#c58b4a]/25
                                        "
                                    ></span>

                                    {{-- FOGUEIRA --}}
                                    <svg
                                        class="
                                            relative
                                            z-10

                                            h-[18px]
                                            w-[18px]

                                            transition-transform
                                            duration-150

                                            group-hover:scale-[1.06]
                                        "

                                        :class="{
                                            'animate-pulse':
                                                resting
                                        }"

                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.55"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        {{-- chama externa --}}
                                        <path
                                            d="
                                                M12.1 3.4
                                                c.8 2.6-1.3 3.8-1.3 5.9
                                                0 1 .5 1.8 1.3 2.3
                                                .2-1.8 1.4-2.8 2.4-3.8
                                                .4 2.1 2.5 3.7 2.5 6.2
                                                0 2.9-2.2 5-5 5
                                                s-5-2.1-5-5
                                                c0-2.4 1.4-4.2 3.1-5.9
                                                .1-1.9 1-3.5 2-4.7Z
                                            "
                                        />

                                        {{-- chama interna --}}
                                        <path
                                            d="
                                                M12.1 12.2
                                                c1 1 1.6 1.8 1.6 2.9
                                                0 1.2-.8 2.1-1.9 2.1
                                                -1.1 0-1.9-.8-1.9-1.9
                                                0-1 .7-1.8 2.2-3.1Z
                                            "
                                            fill="currentColor"
                                            stroke="none"
                                            opacity=".34"
                                        />

                                        {{-- lenha --}}
                                        <path d="M6.1 20.1 17.9 17.3" />
                                        <path d="M6.1 17.3 17.9 20.1" />

                                    </svg>

                                </button>


                                {{-- =================================
                                     MENU DE DESCANSO
                                ================================== --}}

                                <template x-teleport="body">
                                    <div
                                        x-show="
                                            restOpen
                                        "

                                        x-cloak

                                        @click.stop
                                        @click.outside="
                                            restOpen = false
                                        "

                                    x-transition:enter="
                                        transition
                                        ease-out
                                        duration-150
                                    "

                                    x-transition:enter-start="
                                        opacity-0
                                        translate-y-1
                                        scale-[.97]
                                    "

                                    x-transition:enter-end="
                                        opacity-100
                                        translate-y-0
                                        scale-100
                                    "

                                    x-transition:leave="
                                        transition
                                        ease-in
                                        duration-100
                                    "

                                    x-transition:leave-start="
                                        opacity-100
                                        translate-y-0
                                        scale-100
                                    "

                                    x-transition:leave-end="
                                        opacity-0
                                        translate-y-1
                                        scale-[.97]
                                    "

                                        :style="
                                            restMenuStyle
                                        "

                                        class="
                                            fixed
                                            z-[500]

                                            w-[258px]
                                            origin-top-right

                                        overflow-hidden

                                        rounded-xl
                                        border
                                        border-[#b08c62]/55

                                        bg-[#fffdf8]

                                        shadow-[0_14px_34px_rgba(83,21,15,.16),0_2px_6px_rgba(83,21,15,.06)]
                                    "
                                >
                                    {{-- HEADER QUENTE --}}
                                    <div
                                        class="
                                            border-b
                                            border-[#a0774d]/25

                                            bg-[#eadbc8]

                                            px-3.5
                                            py-3

                                            shadow-[inset_0_1px_0_rgba(255,255,255,.70)]
                                        "
                                    >
                                        <div
                                            class="
                                                flex
                                                items-center
                                                gap-2.5
                                            "
                                        >
                                            <span
                                                class="
                                                    flex
                                                    h-8
                                                    w-8
                                                    shrink-0
                                                    items-center
                                                    justify-center

                                                    rounded-lg
                                                    border
                                                    border-[#a0774d]/25

                                                    bg-[#fffdf8]/65

                                                    text-[#6b1d14]
                                                "
                                            >
                                                <svg
                                                    class="h-[17px] w-[17px]"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.55"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="
                                                            M12.1 3.6
                                                            c.7 2.5-1.2 3.6-1.2 5.6
                                                            0 1 .5 1.7 1.2 2.2
                                                            .2-1.6 1.3-2.6 2.3-3.5
                                                            .4 2 2.4 3.5 2.4 5.9
                                                            0 2.8-2.1 4.8-4.8 4.8
                                                            s-4.8-2-4.8-4.8
                                                            c0-2.3 1.3-4 3-5.7
                                                            .1-1.8 1-3.3 1.9-4.5Z
                                                        "
                                                    />
                                                    <path d="m6.5 20 11-2.5" />
                                                    <path d="m6.5 17.5 11 2.5" />
                                                </svg>
                                            </span>

                                            <span class="min-w-0">
                                                <span
                                                    class="
                                                        block

                                                        font-serif
                                                        text-[14px]
                                                        font-black
                                                        leading-none

                                                        text-[#53150f]
                                                    "
                                                >
                                                    Descanso
                                                </span>

                                                <span
                                                    class="
                                                        mt-1
                                                        block

                                                        text-[9px]
                                                        leading-snug

                                                        text-[#76553f]
                                                    "
                                                >
                                                    Escolha como recuperar o personagem.
                                                </span>
                                            </span>
                                        </div>
                                    </div>


                                    <div
                                        class="
                                            space-y-2

                                            bg-[#fbf8f1]

                                            p-2.5
                                        "
                                    >
                                        {{-- =============================
                                             CURTO
                                        ============================== --}}

                                        <button
                                            type="button"

                                            @click="
                                                performRest('short')
                                            "

                                            :disabled="
                                                resting
                                            "

                                            class="
                                                group/rest
                                                flex
                                                w-full
                                                items-center
                                                gap-3

                                                rounded-xl
                                                border
                                                border-[#d8c7ab]/75

                                                bg-[#fffdf8]

                                                px-3
                                                py-2.5

                                                text-left

                                                shadow-[0_1px_2px_rgba(83,21,15,.035)]

                                                transition-all
                                                duration-150

                                                hover:border-[#b08c62]/70
                                                hover:bg-[#f7f0e6]
                                                hover:shadow-[0_3px_8px_rgba(83,21,15,.07)]

                                                disabled:cursor-wait
                                                disabled:opacity-55
                                            "
                                        >
                                            {{-- ÍCONE CURTO — CANECA / PAUSA --}}
                                            <span
                                                class="
                                                    flex
                                                    h-9
                                                    w-9
                                                    shrink-0
                                                    items-center
                                                    justify-center

                                                    rounded-lg
                                                    border
                                                    border-[#b08c62]/35

                                                    bg-[#f4e8d8]

                                                    text-[#7a3d29]

                                                    transition
                                                    group-hover/rest:bg-[#eadbc8]
                                                "
                                            >
                                                <svg
                                                    class="h-[19px] w-[19px]"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.55"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <path d="M7 9h9v5.2A3.8 3.8 0 0 1 12.2 18H10.8A3.8 3.8 0 0 1 7 14.2V9Z" />
                                                    <path d="M16 10.5h1.4a2.1 2.1 0 0 1 0 4.2H16" />
                                                    <path d="M9 6.5c0-1 1-1.2 1-2.2" />
                                                    <path d="M13 6.5c0-1 1-1.2 1-2.2" />
                                                    <path d="M6 20h12" />
                                                </svg>
                                            </span>

                                            <span
                                                class="
                                                    min-w-0
                                                    flex-1
                                                "
                                            >
                                                <span
                                                    class="
                                                        block

                                                        font-serif
                                                        text-[13px]
                                                        font-black
                                                        leading-tight

                                                        text-[#53150f]
                                                    "
                                                >
                                                    Descanso Curto
                                                </span>

                                                <span
                                                    x-show="
                                                        !resting ||
                                                        restingType !== 'short'
                                                    "

                                                    class="
                                                        mt-1
                                                        block

                                                        text-[9px]
                                                        leading-snug

                                                        text-[#7d604d]
                                                    "
                                                >
                                                    Recupera recursos de descanso curto.
                                                </span>

                                                <span
                                                    x-show="
                                                        resting &&
                                                        restingType === 'short'
                                                    "

                                                    x-cloak

                                                    class="
                                                        mt-1
                                                        block

                                                        text-[9px]
                                                        font-bold
                                                        leading-snug

                                                        text-[#6b1d14]
                                                    "
                                                >
                                                    Descansando...
                                                </span>
                                            </span>

                                            <span
                                                x-show="
                                                    !resting ||
                                                    restingType !== 'short'
                                                "

                                                class="
                                                    flex
                                                    h-7
                                                    w-7
                                                    shrink-0
                                                    items-center
                                                    justify-center

                                                    rounded-full

                                                    text-[#a0774d]

                                                    transition-transform
                                                    group-hover/rest:translate-x-[1px]
                                                    group-hover/rest:text-[#6b1d14]
                                                "
                                            >
                                                <svg
                                                    class="h-3.5 w-3.5"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <path d="m9 18 6-6-6-6" />
                                                </svg>
                                            </span>

                                            <span
                                                x-show="
                                                    resting &&
                                                    restingType === 'short'
                                                "

                                                x-cloak

                                                class="
                                                    flex
                                                    h-7
                                                    w-7
                                                    shrink-0
                                                    items-center
                                                    justify-center
                                                    gap-[2px]
                                                "
                                            >
                                                <span class="h-1 w-1 animate-pulse rounded-full bg-[#8c6239]"></span>
                                                <span class="h-1 w-1 animate-pulse rounded-full bg-[#8c6239] [animation-delay:120ms]"></span>
                                                <span class="h-1 w-1 animate-pulse rounded-full bg-[#8c6239] [animation-delay:240ms]"></span>
                                            </span>
                                        </button>


                                        {{-- =============================
                                             LONGO
                                        ============================== --}}

                                        <button
                                            type="button"

                                            @click="
                                                performRest('long')
                                            "

                                            :disabled="
                                                resting
                                            "

                                            class="
                                                group/rest
                                                flex
                                                w-full
                                                items-center
                                                gap-3

                                                rounded-xl
                                                border
                                                border-[#d8c7ab]/75

                                                bg-[#fffdf8]

                                                px-3
                                                py-2.5

                                                text-left

                                                shadow-[0_1px_2px_rgba(83,21,15,.035)]

                                                transition-all
                                                duration-150

                                                hover:border-[#b08c62]/70
                                                hover:bg-[#f7f0e6]
                                                hover:shadow-[0_3px_8px_rgba(83,21,15,.07)]

                                                disabled:cursor-wait
                                                disabled:opacity-55
                                            "
                                        >
                                            {{-- ÍCONE LONGO — LUA --}}
                                            <span
                                                class="
                                                    flex
                                                    h-9
                                                    w-9
                                                    shrink-0
                                                    items-center
                                                    justify-center

                                                    rounded-lg
                                                    border
                                                    border-[#b08c62]/35

                                                    bg-[#f4e8d8]

                                                    text-[#6f4f38]

                                                    transition
                                                    group-hover/rest:bg-[#eadbc8]
                                                "
                                            >
                                                <svg
                                                    class="h-[19px] w-[19px]"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.55"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="
                                                            M19 15.2
                                                            A7.6 7.6 0 0 1 8.8 5
                                                            7.7 7.7 0 1 0 19 15.2Z
                                                        "
                                                    />
                                                    <path d="M16.8 5.1v2.2" />
                                                    <path d="M15.7 6.2h2.2" />
                                                    <circle cx="19" cy="9.3" r=".65" fill="currentColor" stroke="none" />
                                                </svg>
                                            </span>

                                            <span
                                                class="
                                                    min-w-0
                                                    flex-1
                                                "
                                            >
                                                <span
                                                    class="
                                                        block

                                                        font-serif
                                                        text-[13px]
                                                        font-black
                                                        leading-tight

                                                        text-[#53150f]
                                                    "
                                                >
                                                    Descanso Longo
                                                </span>

                                                <span
                                                    x-show="
                                                        !resting ||
                                                        restingType !== 'long'
                                                    "

                                                    class="
                                                        mt-1
                                                        block

                                                        text-[9px]
                                                        leading-snug

                                                        text-[#7d604d]
                                                    "
                                                >
                                                    Recupera vida e todos os recursos.
                                                </span>

                                                <span
                                                    x-show="
                                                        resting &&
                                                        restingType === 'long'
                                                    "

                                                    x-cloak

                                                    class="
                                                        mt-1
                                                        block

                                                        text-[9px]
                                                        font-bold
                                                        leading-snug

                                                        text-[#6b1d14]
                                                    "
                                                >
                                                    Descansando...
                                                </span>
                                            </span>

                                            <span
                                                x-show="
                                                    !resting ||
                                                    restingType !== 'long'
                                                "

                                                class="
                                                    flex
                                                    h-7
                                                    w-7
                                                    shrink-0
                                                    items-center
                                                    justify-center

                                                    rounded-full

                                                    text-[#a0774d]

                                                    transition-transform
                                                    group-hover/rest:translate-x-[1px]
                                                    group-hover/rest:text-[#6b1d14]
                                                "
                                            >
                                                <svg
                                                    class="h-3.5 w-3.5"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <path d="m9 18 6-6-6-6" />
                                                </svg>
                                            </span>

                                            <span
                                                x-show="
                                                    resting &&
                                                    restingType === 'long'
                                                "

                                                x-cloak

                                                class="
                                                    flex
                                                    h-7
                                                    w-7
                                                    shrink-0
                                                    items-center
                                                    justify-center
                                                    gap-[2px]
                                                "
                                            >
                                                <span class="h-1 w-1 animate-pulse rounded-full bg-[#8c6239]"></span>
                                                <span class="h-1 w-1 animate-pulse rounded-full bg-[#8c6239] [animation-delay:120ms]"></span>
                                                <span class="h-1 w-1 animate-pulse rounded-full bg-[#8c6239] [animation-delay:240ms]"></span>
                                            </span>
                                        </button>


                                        {{-- =============================
                                             ERRO
                                        ============================== --}}

                                        <div
                                            x-show="
                                                restError
                                            "

                                            x-cloak

                                            class="
                                                rounded-lg
                                                border
                                                border-red-200

                                                bg-red-50

                                                px-3
                                                py-2
                                            "
                                        >
                                            <p
                                                class="
                                                    text-[9px]
                                                    font-bold
                                                    leading-snug

                                                    text-red-700
                                                "

                                                x-text="
                                                    restError
                                                "
                                            ></p>
                                        </div>
                                    </div>
                                    </div>
                                </template>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         EFEITO GLOBAL — ESTABILIZADO
    ============================================================= --}}

    <template x-teleport="body">

        <div
            x-show="
                stabilizationEffect
            "
            x-cloak

            class="
                pointer-events-none
                fixed
                inset-0
                z-[290]

                flex
                items-center
                justify-center

                overflow-hidden
            "
        >

            <div
                class="
                    stable-world-glow

                    absolute
                    left-1/2
                    top-1/2

                    h-[520px]
                    w-[520px]

                    -translate-x-1/2
                    -translate-y-1/2

                    rounded-full

                    bg-[radial-gradient(circle,rgba(16,185,129,.28)_0%,rgba(16,185,129,.12)_32%,rgba(16,185,129,0)_70%)]
                "
            ></div>


            <div
                class="
                    stable-banner-arrive

                    relative

                    overflow-hidden

                    rounded-2xl
                    border
                    border-emerald-700/25

                    bg-[#fbfaf5]/95

                    px-7
                    py-5

                    text-center

                    shadow-[0_24px_70px_rgba(6,78,59,.22)]
                    backdrop-blur-md
                "
            >
                <div
                    class="
                        pointer-events-none
                        absolute
                        inset-[4px]

                        rounded-[13px]
                        border
                        border-emerald-700/10
                    "
                ></div>

                <p
                    class="
                        relative

                        text-[10px]
                        font-black
                        uppercase
                        tracking-[0.28em]

                        text-emerald-700
                    "
                >
                    Salvamentos contra a morte
                </p>

                <p
                    class="
                        relative

                        mt-1.5

                        font-serif
                        text-[28px]
                        font-black

                        text-[#254d3f]
                    "
                >
                    Estabilizado
                </p>

                <p
                    class="
                        relative

                        mt-1

                        text-[11px]
                        font-semibold

                        text-[#557468]
                    "
                >
                    O pior passou. Por enquanto.
                </p>
            </div>

        </div>

    </template>


    {{-- ============================================================
         MORTE — CORPO INTEIRO DA FICHA
    ============================================================= --}}

    <template x-teleport="body">

        <div
            x-show="
                isDead
            "
            x-cloak

            x-effect="
                syncDeathSequence(
                    isDead
                );

                syncSheetDeathVisual(
                    isDead
                )
            "

            class="
                pointer-events-none

                fixed
                inset-0
                z-[300]

                flex
                items-center
                justify-center

                overflow-hidden

                p-5
            "
        >

            <div
                class="
                    death-card-arrive

                    pointer-events-auto
                    relative
                    z-10

                    w-full
                    max-w-[560px]

                    overflow-hidden

                    rounded-3xl
                    border
                    border-white/10

                    bg-[linear-gradient(180deg,rgba(27,24,23,.96)_0%,rgba(12,11,11,.98)_100%)]

                    text-center

                    shadow-[0_35px_120px_rgba(0,0,0,.55)]
                "
            >

                <div
                    class="
                        border-b
                        border-white/8

                        px-7
                        pb-5
                        pt-6
                    "
                >

                    <div
                        class="
                            mx-auto

                            flex
                            h-12
                            w-12
                            items-center
                            justify-center

                            rounded-full
                            border
                            border-red-400/20

                            bg-red-950/40
                        "
                    >
                        <span
                            class="
                                font-serif
                                text-[22px]
                                font-black

                                text-red-300
                            "
                        >
                            †
                        </span>
                    </div>


                    <p
                        class="
                            mt-4

                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.32em]

                            text-red-400
                        "
                    >
                        O último fôlego se foi
                    </p>


                    <template
                        x-if="
                            morquenRuleActive
                        "
                    >
                        <div>

                            <p
                                class="
                                    mt-4

                                    font-serif
                                    text-[22px]
                                    font-black
                                    leading-snug

                                    text-[#f7eee2]
                                "
                            >
                                “Vamos Elkas, ainda não é hora de desistir.”
                            </p>

                            <p
                                class="
                                    mt-2

                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.16em]

                                    text-[#cdbb9f]
                                "
                            >
                                — Clemente
                            </p>

                        </div>
                    </template>


                    <template
                        x-if="
                            !morquenRuleActive
                        "
                    >
                        <p
                            class="
                                mt-4

                                font-serif
                                text-[24px]
                                font-black

                                text-[#e8e1d8]
                            "
                        >
                            A jornada chegou ao fim.
                        </p>
                    </template>

                </div>


                <template
                    x-if="
                        !morquenRuleActive
                    "
                >
                    <div
                        class="
                            px-7
                            pb-7
                            pt-5
                        "
                    >

                        <div
                            x-show="
                                deathHopeVisible
                            "
                            x-cloak

                            class="
                                death-hope-reveal
                            "
                        >

                            <div
                                class="
                                    mx-auto

                                    h-px
                                    w-16

                                    bg-[linear-gradient(90deg,transparent,rgba(217,183,123,.48),transparent)]
                                "
                            ></div>


                            <p
                                class="
                                    mt-4

                                    font-serif
                                    text-[19px]
                                    font-black
                                    italic
                                    leading-snug

                                    text-[#e6d6bf]
                                "
                            >
                                ... Ou ainda a esperança?
                            </p>

                        </div>


                        <div
                            x-show="
                                deathReviveVisible
                            "
                            x-cloak

                            class="
                                death-revive-reveal

                                mt-4
                            "
                        >

                            <button
                                type="button"

                                @click="
                                    reviveManually()
                                "

                                :disabled="
                                    deathReviving
                                "

                                class="
                                    min-h-12
                                    w-full

                                    rounded-xl
                                    border
                                    border-[#d9b77b]/30

                                    bg-[linear-gradient(180deg,#7b251a_0%,#5b1711_100%)]

                                    px-5

                                    font-serif
                                    text-[16px]
                                    font-black

                                    text-[#fffaf2]

                                    shadow-[0_8px_28px_rgba(107,29,20,.34)]

                                    transition

                                    hover:-translate-y-0.5
                                    hover:border-[#d9b77b]/45
                                    hover:bg-[#7b251a]
                                    hover:shadow-[0_12px_34px_rgba(107,29,20,.42)]

                                    disabled:cursor-wait
                                    disabled:opacity-60
                                "
                            >
                                <span
                                    x-show="
                                        !deathReviving
                                    "
                                >
                                    Reviver !!
                                </span>

                                <span
                                    x-show="
                                        deathReviving
                                    "
                                    x-cloak
                                >
                                    Retornando...
                                </span>
                            </button>


                            <p
                                class="
                                    mt-2

                                    text-[9px]
                                    font-semibold
                                    leading-relaxed

                                    text-[#9f9187]
                                "
                            >
                                Retorna com 1 PV e limpa os salvamentos contra a morte.
                            </p>


                            <div
                                x-show="
                                    deathReviveError
                                "
                                x-cloak

                                class="
                                    mt-3

                                    rounded-xl
                                    border
                                    border-red-500/20

                                    bg-red-950/40

                                    px-4
                                    py-3
                                "
                            >
                                <p
                                    class="
                                        text-[10px]
                                        font-bold

                                        text-red-200
                                    "

                                    x-text="
                                        deathReviveError
                                    "
                                ></p>
                            </div>

                        </div>

                    </div>
                </template>


                <template
                    x-if="
                        morquenRuleActive
                    "
                >
                    <div
                        class="
                            px-7
                            pb-7
                            pt-5
                        "
                    >

                        <div
                            class="
                                rounded-xl
                                border
                                border-[#d9b77b]/15

                                bg-[#d9b77b]/5

                                px-4
                                py-3
                            "
                        >
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]

                                    text-[#f0d5ad]
                                "
                            >
                                Determinação Final
                            </p>

                            <p
                                class="
                                    mt-1.5

                                    text-[11px]
                                    leading-relaxed

                                    text-[#d8cbbb]
                                "
                            >
                                Sua alma ainda pode forçar o caminho de volta ao corpo.
                            </p>
                        </div>


                        <button
                            type="button"

                            @click="
                                reviveWithMorquen()
                            "

                            :disabled="
                                !canUseMorquenDetermination ||
                                morquenReviving
                            "

                            class="
                                mt-4

                                min-h-12
                                w-full

                                rounded-xl
                                border
                                border-[#d9b77b]/30

                                bg-[#6b1d14]

                                px-5

                                font-serif
                                text-[15px]
                                font-black

                                text-[#fffaf2]

                                shadow-[0_8px_24px_rgba(107,29,20,.32)]

                                transition

                                hover:bg-[#7b251a]

                                disabled:cursor-not-allowed
                                disabled:bg-[#403a37]
                                disabled:text-[#8e837b]
                                disabled:shadow-none
                            "
                        >
                            <span
                                x-show="
                                    !morquenReviving
                                "
                            >
                                Voltar à Vida
                            </span>

                            <span
                                x-show="
                                    morquenReviving
                                "
                                x-cloak
                            >
                                Retornando...
                            </span>
                        </button>


                        <div
                            x-show="
                                morquenDeterminationLocked &&
                                exhaustionLevel > 0
                            "

                            x-cloak

                            class="
                                mt-3

                                rounded-xl
                                border
                                border-amber-500/15

                                bg-amber-950/25

                                px-4
                                py-3
                            "
                        >
                            <p
                                class="
                                    text-[10px]
                                    font-bold
                                    leading-relaxed

                                    text-amber-200
                                "
                            >
                                Determinação Final permanece selada até toda a exaustão ser removida.
                                Exaustão atual:
                                <strong
                                    x-text="
                                        exhaustionLevel
                                    "
                                ></strong>.
                            </p>
                        </div>


                        <div
                            x-show="
                                morquenReviveError
                            "

                            x-cloak

                            class="
                                mt-3

                                rounded-xl
                                border
                                border-red-500/20

                                bg-red-950/40

                                px-4
                                py-3
                            "
                        >
                            <p
                                class="
                                    text-[10px]
                                    font-bold

                                    text-red-200
                                "

                                x-text="
                                    morquenReviveError
                                "
                            ></p>
                        </div>

                    </div>
                </template>

            </div>

        </div>

    </template>


    {{-- ============================================================
         GAVETA DE MORTE
    ============================================================= --}}

    <div
        x-show="
            isDying &&
            deathDrawerOpen
        "

        x-cloak

        x-transition:enter="
            transition-[transform,opacity]
            duration-300
            ease-out
        "

        x-transition:enter-start="
            translate-y-[-18px]
            opacity-0
            scale-[.98]
        "

        x-transition:enter-end="
            translate-y-0
            opacity-100
            scale-100
        "

        x-transition:leave="
            transition-[transform,opacity]
            duration-200
            ease-in
        "

        x-transition:leave-start="
            translate-y-0
            opacity-100
            scale-100
        "

        x-transition:leave-end="
            translate-y-[-12px]
            opacity-0
            scale-[.99]
        "

        class="
            absolute
            left-1/2
            top-full
            z-50

            w-full
            max-w-[40rem]

            -translate-x-1/2
        "
    >

        <div
            class="
                death-drawer-active

                overflow-hidden

                rounded-b-3xl

                border
                border-t-0
                border-[#b08c62]/45

                bg-[linear-gradient(180deg,#fbf8f1_0%,#f4eadc_100%)]

                shadow-[0_22px_55px_rgba(72,20,15,.18)]
            "
        >

            {{-- FAIXA SUPERIOR --}}

            <div
                class="
                    relative

                    overflow-hidden

                    bg-[linear-gradient(90deg,#725943_0%,#927257_50%,#725943_100%)]

                    px-4
                    pb-3
                    pt-2.5

                    text-[#fffaf2]
                "
            >

                <div
                    class="
                        pointer-events-none
                        absolute
                        inset-0

                        opacity-25

                        bg-[radial-gradient(circle_at_50%_0%,rgba(255,248,232,.72),transparent_45%)]
                    "
                ></div>


                <button
                    type="button"

                    @click="
                        closeDeathDrawer()
                    "

                    class="
                        relative
                        z-10

                        mx-auto

                        flex
                        h-5
                        w-16
                        items-center
                        justify-center

                        rounded-full

                        border
                        border-white/12

                        bg-white/7

                        transition

                        hover:bg-white/12
                    "

                    title="Recolher"
                >
                    <span
                        class="
                            h-1
                            w-7

                            rounded-full

                            bg-[#e7cfb7]/75
                        "
                    ></span>
                </button>


                <div
                    class="
                        relative
                        z-10

                        mt-2.5

                        text-center
                    "
                >

                    <p
                        class="
                            text-[9px]
                            font-black
                            uppercase
                            tracking-[0.20em]

                            text-[#f0d5ad]
                        "
                    >
                        Limiar entre vida e morte
                    </p>

                    <h3
                        class="
                            mt-1

                            font-serif
                            text-[20px]
                            font-black
                            leading-none
                        "

                        x-text="
                            morquenRuleActive
                                ? 'Cicatrizes do Passado'
                                : 'Salvamentos Contra a Morte'
                        "
                    ></h3>

                </div>

            </div>


            <div
                class="
                    px-4
                    pb-4
                    pt-3.5

                    sm:px-4
                "
            >

                {{-- MORQUEN ORGANIZADO --}}

                <div
                    x-show="
                        morquenRuleActive
                    "

                    x-cloak

                    class="
                        relative

                        overflow-hidden

                        rounded-2xl
                        border
                        border-amber-700/20

                        bg-[linear-gradient(135deg,#fff8e8_0%,#f7ead0_100%)]

                        px-4
                        py-3.5

                        shadow-[inset_0_1px_0_rgba(255,255,255,.75)]
                    "
                >

                    <div
                        class="
                            absolute
                            bottom-0
                            left-0
                            top-0

                            w-1

                            bg-[#8c6239]
                        "
                    ></div>


                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-4
                        "
                    >

                        <div class="min-w-0">

                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]

                                    text-[#8c6239]
                                "
                            >
                                Regra Morquen
                            </p>

                            <p
                                class="
                                    mt-0.5

                                    font-serif
                                    text-[15px]
                                    font-black

                                    text-[#53150f]
                                "
                            >
                                Espírito Inquebrável
                            </p>

                            <p
                                class="
                                    mt-1

                                    text-[10px]
                                    leading-relaxed

                                    text-[#765846]
                                "
                            >
                                Com 0 PV você permanece consciente, pode agir normalmente e continua realizando testes contra a morte no início de cada turno.
                            </p>

                        </div>


                        <div
                            x-show="
                                exhaustionLevel > 0
                            "

                            x-cloak

                            class="
                                shrink-0

                                rounded-lg
                                border
                                border-amber-700/20

                                bg-white/55

                                px-2.5
                                py-2

                                text-center
                            "
                        >
                            <p
                                class="
                                    text-[7px]
                                    font-black
                                    uppercase
                                    tracking-[0.10em]

                                    text-amber-900
                                "
                            >
                                Exaustão
                            </p>

                            <p
                                class="
                                    mt-0.5

                                    font-serif
                                    text-[18px]
                                    font-black
                                    leading-none

                                    text-[#6f4f38]
                                "

                                x-text="
                                    exhaustionLevel
                                "
                            ></p>
                        </div>

                    </div>


                    <div
                        class="
                            mt-3

                            grid
                            grid-cols-3
                            gap-2
                        "
                    >

                        <div
                            class="
                                rounded-lg
                                border
                                border-[#d8c7ab]/55

                                bg-white/55

                                px-2
                                py-2

                                text-center
                            "
                        >
                            <p
                                class="
                                    font-serif
                                    text-[14px]
                                    font-black

                                    text-[#53150f]
                                "
                            >
                                0 PV
                            </p>

                            <p
                                class="
                                    mt-0.5

                                    text-[7px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]

                                    text-[#8c6239]
                                "
                            >
                                Consciente
                            </p>
                        </div>


                        <div
                            class="
                                rounded-lg
                                border
                                border-emerald-700/15

                                bg-emerald-50/70

                                px-2
                                py-2

                                text-center
                            "
                        >
                            <p
                                class="
                                    font-serif
                                    text-[14px]
                                    font-black

                                    text-emerald-800
                                "
                            >
                                2
                            </p>

                            <p
                                class="
                                    mt-0.5

                                    text-[7px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]

                                    text-emerald-800/75
                                "
                            >
                                Sucessos
                            </p>
                        </div>


                        <div
                            class="
                                rounded-lg
                                border
                                border-red-700/15

                                bg-red-50/70

                                px-2
                                py-2

                                text-center
                            "
                        >
                            <p
                                class="
                                    font-serif
                                    text-[14px]
                                    font-black

                                    text-red-800
                                "
                            >
                                6
                            </p>

                            <p
                                class="
                                    mt-0.5

                                    text-[7px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]

                                    text-red-800/75
                                "
                            >
                                Falhas
                            </p>
                        </div>

                    </div>

                </div>


                {{-- ÁREA PRINCIPAL --}}

                <div
                    class="
                        mt-4

                        grid
                        grid-cols-[minmax(0,1fr)_116px_minmax(0,1fr)]

                        items-center
                        gap-4
                    "
                >

                    {{-- SUCESSOS --}}

                    <div
                        class="
                            rounded-2xl
                            border
                            border-emerald-700/15

                            bg-[linear-gradient(180deg,#f4fbf6_0%,#eaf6ee_100%)]

                            px-3
                            py-3.5

                            text-center

                            shadow-[inset_0_1px_0_rgba(255,255,255,.75)]
                        "
                    >

                        <p
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]

                                text-emerald-800
                            "
                        >
                            Sucessos
                        </p>

                        <p
                            class="
                                mt-0.5

                                font-serif
                                text-[17px]
                                font-black

                                text-emerald-900
                            "
                        >
                            <span
                                x-text="
                                    deathSaveSuccesses
                                "
                            ></span>
                            <span class="opacity-45">/</span>
                            <span
                                x-text="
                                    deathSuccessLimit
                                "
                            ></span>
                        </p>


                        <div
                            class="
                                mt-3

                                flex
                                flex-wrap
                                items-center
                                justify-center
                                gap-2
                            "
                        >

                            <template
                                x-for="
                                    n in deathSuccessLimit
                                "
                                :key="
                                    'success-' + n
                                "
                            >

                                <button
                                    type="button"

                                    @click="
                                        setDeathSave(
                                            'success',
                                            n
                                        )
                                    "

                                    :disabled="
                                        deathRolling
                                    "

                                    class="
                                        relative

                                        flex
                                        h-9
                                        w-9
                                        items-center
                                        justify-center

                                        rounded-full
                                        border-2

                                        transition-all
                                        duration-200

                                        disabled:cursor-wait
                                        disabled:opacity-55
                                    "

                                    :class="
                                        n <= deathSaveSuccesses
                                            ? 'border-emerald-600 bg-emerald-600 text-white shadow-[0_5px_14px_rgba(5,150,105,.20)] scale-105'
                                            : 'border-emerald-700/20 bg-white/75 text-transparent hover:border-emerald-600/45 hover:bg-white'
                                    "
                                >
                                    <span
                                        x-show="
                                            n <= deathSaveSuccesses
                                        "

                                        class="
                                            text-[15px]
                                            font-black
                                        "
                                    >
                                        ✓
                                    </span>
                                </button>

                            </template>

                        </div>

                    </div>


                    {{-- D20 FACETADO --}}

                    <div
                        class="
                            flex
                            flex-col
                            items-center
                            justify-center
                        "
                    >

                        <div
                            class="
                                relative

                                flex
                                h-[116px]
                                w-[116px]
                                items-center
                                justify-center
                            "
                        >

                            <button
                                type="button"

                                @click="
                                    rollDeathSave()
                                "

                                :disabled="
                                    deathRolling
                                "

                                class="
                                    group
                                    relative

                                    flex
                                    h-[106px]
                                    w-[106px]
                                    items-center
                                    justify-center

                                    bg-transparent

                                    transition-transform
                                    duration-200

                                    hover:-translate-y-1

                                    disabled:cursor-wait
                                "
                            >

                                <svg
                                    class="
                                        absolute
                                        inset-0

                                        h-full
                                        w-full

                                        overflow-visible

                                        drop-shadow-[0_7px_10px_rgba(69,50,36,.22)]
                                    "

                                    :class="{
                                        'death-d20-spin':
                                            deathRolling,

                                        'death-d20-breathe':
                                            !deathRolling
                                    }"

                                    viewBox="0 0 100 100"

                                    aria-hidden="true"
                                >
                                    <defs>
                                        <linearGradient
                                            id="death-d20-face-a"
                                            x1="0"
                                            y1="0"
                                            x2="1"
                                            y2="1"
                                        >
                                            <stop offset="0%" stop-color="#8f694e" />
                                            <stop offset="100%" stop-color="#76543d" />
                                        </linearGradient>

                                        <linearGradient
                                            id="death-d20-face-b"
                                            x1="0"
                                            y1="0"
                                            x2="1"
                                            y2="1"
                                        >
                                            <stop offset="0%" stop-color="#d1a47f" />
                                            <stop offset="100%" stop-color="#b97755" />
                                        </linearGradient>

                                        <linearGradient
                                            id="death-d20-face-c"
                                            x1="0"
                                            y1="0"
                                            x2="1"
                                            y2="1"
                                        >
                                            <stop offset="0%" stop-color="#76513b" />
                                            <stop offset="100%" stop-color="#54392b" />
                                        </linearGradient>
                                    </defs>


                                    {{-- Fundo preto forma os sulcos grossos --}}
                                    <path
                                        d="
                                            M50 4
                                            L88 27
                                            L88 73
                                            L50 96
                                            L12 73
                                            L12 27
                                            Z
                                        "
                                        fill="#2b211a"
                                    />


                                    {{-- Coroa superior --}}
                                    <polygon
                                        points="48,10 48,23 20,29 20,26"
                                        fill="url(#death-d20-face-b)"
                                    />

                                    <polygon
                                        points="52,10 80,26 80,29 52,23"
                                        fill="#a97955"
                                    />


                                    {{-- Faces superiores laterais --}}
                                    <polygon
                                        points="16,31 45,26 27,63 14,37"
                                        fill="#d5ad8a"
                                    />

                                    <polygon
                                        points="84,31 55,26 73,63 86,37"
                                        fill="#8c644b"
                                    />


                                    {{-- Triângulo central --}}
                                    <polygon
                                        points="50,26 70,65 30,65"
                                        fill="url(#death-d20-face-a)"
                                    />


                                    {{-- Laterais --}}
                                    <polygon
                                        points="14,42 25,66 14,73"
                                        fill="#a97859"
                                    />

                                    <polygon
                                        points="86,42 75,66 86,73"
                                        fill="#75503d"
                                    />


                                    {{-- Faces inferiores --}}
                                    <polygon
                                        points="16,76 28,68 43,89 24,79"
                                        fill="#c98d68"
                                    />

                                    <polygon
                                        points="84,76 72,68 57,89 76,79"
                                        fill="#6d4937"
                                    />

                                    <polygon
                                        points="31,69 69,69 50,92"
                                        fill="url(#death-d20-face-c)"
                                    />


                                    {{-- Contorno --}}
                                    <path
                                        d="
                                            M50 4
                                            L88 27
                                            L88 73
                                            L50 96
                                            L12 73
                                            L12 27
                                            Z
                                        "
                                        fill="none"
                                        stroke="#2b211a"
                                        stroke-width="4.8"
                                        stroke-linejoin="round"
                                    />


                                    {{-- Sulcos, como no ícone enviado --}}
                                    <g
                                        fill="none"
                                        stroke="#2b211a"
                                        stroke-width="4.2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M50 6 L50 24" />
                                        <path d="M15 31 L48 24" />
                                        <path d="M85 31 L52 24" />

                                        <path d="M49 25 L28 66" />
                                        <path d="M51 25 L72 66" />

                                        <path d="M28 67 L72 67" />

                                        <path d="M27 67 L14 75" />
                                        <path d="M73 67 L86 75" />

                                        <path d="M29 68 L47 92" />
                                        <path d="M71 68 L53 92" />
                                    </g>
                                </svg>


                                {{-- O dado gira; o número continua legível --}}
                                <span
                                    class="
                                        pointer-events-none
                                        relative
                                        z-10

                                        flex
                                        flex-col
                                        items-center
                                        justify-center

                                        text-[#fffaf2]
                                    "
                                >

                                    <span
                                        class="
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-[0.16em]

                                            text-[#efd9c7]
                                        "
                                    >
                                        d20
                                    </span>


                                    <span
                                        class="
                                            mt-[-2px]

                                            font-serif
                                            text-[32px]
                                            font-black
                                            leading-none

                                            tabular-nums

                                            drop-shadow-[0_2px_2px_rgba(0,0,0,.30)]
                                        "

                                        :class="
                                            deathRollSettled
                                                ? 'death-roll-number-settled'
                                                : (
                                                    deathRolling
                                                        ? 'death-roll-number-active'
                                                        : ''
                                                )
                                        "

                                        x-text="
                                            deathRolling
                                                ? (
                                                    deathRollPreview
                                                    ?? 20
                                                )
                                                : (
                                                    deathRollResult
                                                    ?? '?'
                                                )
                                        "
                                    ></span>

                                </span>

                            </button>

                        </div>


                        <div
                            class="
                                min-h-[38px]

                                text-center
                            "
                        >

                            <template
                                x-if="
                                    deathRolling &&
                                    !deathRollSettled
                                "
                            >
                                <div>

                                    <p
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.13em]

                                            text-[#6f4f38]
                                        "
                                    >
                                        O destino está girando...
                                    </p>

                                    <div
                                        class="
                                            mx-auto
                                            mt-1.5

                                            flex
                                            w-[68px]
                                            items-center
                                            gap-1
                                        "
                                    >
                                        <span
                                            class="
                                                h-1
                                                flex-1

                                                animate-pulse

                                                rounded-full

                                                bg-[#d8c7ab]
                                            "
                                        ></span>

                                        <span
                                            class="
                                                h-1
                                                flex-1

                                                animate-pulse

                                                rounded-full

                                                bg-[#b08c62]

                                                [animation-delay:180ms]
                                            "
                                        ></span>

                                        <span
                                            class="
                                                h-1
                                                flex-1

                                                animate-pulse

                                                rounded-full

                                                bg-[#6b1d14]

                                                [animation-delay:360ms]
                                            "
                                        ></span>
                                    </div>

                                </div>
                            </template>


                            <template
                                x-if="
                                    !deathRolling &&
                                    deathRollResult === null
                                "
                            >
                                <button
                                    type="button"

                                    @click="
                                        rollDeathSave()
                                    "

                                    class="
                                        rounded-full

                                        border
                                        border-[#b79a77]/45

                                        bg-white/65

                                        px-3
                                        py-1.5

                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.11em]

                                        text-[#6f4f38]

                                        shadow-sm

                                        transition

                                        hover:bg-white
                                    "
                                >
                                    Rolar contra a morte
                                </button>
                            </template>


                            <template
                                x-if="
                                    deathRollSettled &&
                                    deathRollResult !== null
                                "
                            >
                                <div
                                    class="
                                        rounded-full

                                        px-3
                                        py-1.5

                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.10em]
                                    "

                                    :class="
                                        deathRollResult >= 10
                                            ? 'bg-emerald-50 text-emerald-800'
                                            : 'bg-red-50 text-red-800'
                                    "

                                    x-text="
                                        deathRollResult === 20
                                            ? '20 natural · 1 PV'
                                            : (
                                                deathRollResult === 1
                                                    ? '1 natural · 2 falhas'
                                                    : (
                                                        deathRollResult >= 10
                                                            ? 'Sucesso'
                                                            : 'Falha'
                                                    )
                                            )
                                    "
                                ></div>
                            </template>

                        </div>

                    </div>


                    {{-- FALHAS --}}

                    <div
                        class="
                            rounded-2xl
                            border
                            border-red-700/15

                            bg-[linear-gradient(180deg,#fff7f6_0%,#fbeceb_100%)]

                            px-3
                            py-3.5

                            text-center

                            shadow-[inset_0_1px_0_rgba(255,255,255,.75)]
                        "
                    >

                        <p
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]

                                text-red-800
                            "
                        >
                            Falhas
                        </p>

                        <p
                            class="
                                mt-0.5

                                font-serif
                                text-[17px]
                                font-black

                                text-red-900
                            "
                        >
                            <span
                                x-text="
                                    deathSaveFailures
                                "
                            ></span>
                            <span class="opacity-45">/</span>
                            <span
                                x-text="
                                    deathFailureLimit
                                "
                            ></span>
                        </p>


                        <div
                            class="
                                mt-3

                                flex
                                flex-wrap
                                items-center
                                justify-center
                                gap-2
                            "
                        >

                            <template
                                x-for="
                                    n in deathFailureLimit
                                "
                                :key="
                                    'failure-' + n
                                "
                            >

                                <button
                                    type="button"

                                    @click="
                                        setDeathSave(
                                            'failure',
                                            n
                                        )
                                    "

                                    :disabled="
                                        deathRolling
                                    "

                                    class="
                                        relative

                                        flex
                                        h-9
                                        w-9
                                        items-center
                                        justify-center

                                        rounded-full
                                        border-2

                                        transition-all
                                        duration-200

                                        disabled:cursor-wait
                                        disabled:opacity-55
                                    "

                                    :class="
                                        n <= deathSaveFailures
                                            ? 'border-red-600 bg-red-600 text-white shadow-[0_5px_14px_rgba(220,38,38,.18)] scale-105'
                                            : 'border-red-700/20 bg-white/75 text-transparent hover:border-red-600/45 hover:bg-white'
                                    "
                                >
                                    <span
                                        x-show="
                                            n <= deathSaveFailures
                                        "

                                        class="
                                            text-[15px]
                                            font-black
                                        "
                                    >
                                        ×
                                    </span>
                                </button>

                            </template>

                        </div>

                    </div>

                </div>


                {{-- RODAPÉ EXPLICATIVO --}}

                <div
                    class="
                        mt-4

                        border-t
                        border-[#b08c62]/24

                        pt-3
                    "
                >

                    <div
                        class="
                            mx-auto

                            flex
                            w-fit
                            max-w-full
                            items-center
                            justify-center
                            gap-2.5

                            rounded-full
                            border
                            border-[#cdbb9f]/75

                            bg-[#fffaf2]/92

                            px-4
                            py-2

                            shadow-[0_2px_8px_rgba(91,67,49,.08)]
                        "
                    >

                        <span
                            class="
                                flex
                                h-5
                                w-5
                                shrink-0
                                items-center
                                justify-center

                                rounded-full
                                border
                                border-[#b08c62]/35

                                bg-[#ead8bd]

                                font-serif
                                text-[11px]
                                font-black
                                leading-none

                                text-[#6f4f38]
                            "
                            aria-hidden="true"
                        >
                            !
                        </span>


                        <p
                            class="
                                text-center
                                text-[11px]
                                font-semibold
                                leading-snug

                                text-[#5f4031]
                            "
                        >
                            <strong
                                class="
                                    font-serif
                                    text-[12px]
                                    font-black

                                    text-emerald-800
                                "

                                x-text="
                                    deathSuccessLimit
                                "
                            ></strong>

                            <span>
                                sucessos estabilizam
                            </span>

                            <span
                                class="
                                    mx-1.5

                                    text-[#b08c62]
                                "
                            >
                                •
                            </span>

                            <strong
                                class="
                                    font-serif
                                    text-[12px]
                                    font-black

                                    text-red-800
                                "

                                x-text="
                                    deathFailureLimit
                                "
                            ></strong>

                            <span>
                                falhas levam à morte
                            </span>
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         REABRIR GAVETA
    ============================================================= --}}

    <div
        x-show="
            isDying &&
            !deathDrawerOpen
        "

        x-cloak
        x-transition

        class="
            death-tab-enter
            absolute
            left-1/2
            top-full
            z-50

            -mt-[14px]
            -translate-x-1/2
        "
    >

        <button
            type="button"

            @click="
                openDeathDrawer()
            "

            class="
                group
                relative

                flex
                h-8
                items-center
                gap-2.5

                overflow-hidden

                rounded-xl

                border
                border-[#c9ad8a]/80

                bg-[#fffaf2]/95

                px-4

                text-[8px]
                font-black
                uppercase
                tracking-[0.12em]

                text-[#6f4f38]

                shadow-[0_7px_18px_rgba(91,67,49,.13),inset_0_1px_0_rgba(255,255,255,.88)]

                backdrop-blur-sm

                transition-all
                duration-200

                hover:-translate-y-0.5
                hover:border-[#aa845f]
                hover:bg-[#fffdf8]
                hover:shadow-[0_9px_22px_rgba(91,67,49,.17),inset_0_1px_0_rgba(255,255,255,.92)]
            "
        >

            <span
                class="
                    flex
                    h-3
                    w-3
                    shrink-0
                    items-center
                    justify-center

                    rounded-full

                    border
                    border-[#9b7655]/35

                    bg-[#ead8bd]

                    shadow-[inset_0_0_0_2px_rgba(255,250,242,.75)]
                "
            >
                <span
                    class="
                        h-1
                        w-1

                        rounded-full

                        bg-[#8c6239]
                    "
                ></span>
            </span>

            <span
                x-text="
                    morquenRuleActive
                        ? 'Cicatrizes do Passado'
                        : 'Salvamentos contra a Morte'
                "
            ></span>

            <svg
                class="
                    h-3
                    w-3

                    text-[#9a7656]

                    transition-transform
                    duration-200

                    group-hover:-translate-y-0.5
                "
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 15l7-7 7 7"
                />

            </svg>

        </button>

    </div>


    {{-- ============================================================
         MODAL DE VIDA — V2
    ============================================================= --}}
    <template x-teleport="body">
        <div
            x-show="hpSettingsOpen"
            x-cloak
            class="
                fixed
                inset-0
                z-[300]

                flex
                items-center
                justify-center

                p-4
                sm:p-6
            "
        >
            {{-- BACKDROP --}}
            <div
                x-show="hpSettingsOpen"
                x-transition.opacity.duration.150ms
                @click="closeHpSettings()"
                class="
                    absolute
                    inset-0

                    bg-black/90
                "

                style="
                    background-color:
                        rgba(8, 6, 5, 0.88);
                "
            ></div>

            {{-- PAINEL --}}
            <section
                x-show="hpSettingsOpen"
                x-transition:enter="
                    transition
                    ease-out
                    duration-150
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
                    duration-100
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
                @click.stop
                class="
                    relative
                    z-10

                    flex
                    max-h-[90vh]
                    w-full
                    max-w-[580px]
                    flex-col

                    overflow-hidden

                    rounded-[16px]
                    border
                    border-[#cdbb9f]

                    bg-[#faf8f2]

                    shadow-[0_24px_70px_rgba(42,23,18,.24)]
                "
            >
                {{-- =====================================================
                     CONFIRMAÇÃO
                ====================================================== --}}
                <template
                    x-if="
                        hpSettingsStep ===
                        'confirm'
                    "
                >
                    <div class="flex min-h-0 flex-1 flex-col">
                        {{-- CABEÇALHO --}}
                        <header
                            class="
                                flex
                                items-start
                                justify-between
                                gap-4

                                border-b
                                border-[#d8c7ab]/65

                                px-5
                                py-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.16em]
                                        text-[#8c6239]
                                    "
                                >
                                    Configuração de Combate
                                </p>

                                <h2
                                    class="
                                        mt-1
                                        font-serif
                                        text-[24px]
                                        font-black
                                        leading-tight
                                        text-[#53150f]
                                    "
                                >
                                    Alterar Pontos de Vida
                                </h2>
                            </div>

                            <button
                                type="button"
                                @click="closeHpSettings()"
                                class="
                                    flex
                                    h-9
                                    w-9
                                    shrink-0
                                    items-center
                                    justify-center

                                    rounded-lg

                                    text-[#8c6239]

                                    transition
                                    hover:bg-[#efe9dc]
                                    hover:text-[#53150f]
                                "
                                title="Fechar"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        d="M6 6l12 12M18 6 6 18"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </header>

                        {{-- CONTEÚDO --}}
                        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/70
                                    bg-[#f4f1e8]

                                    p-4
                                "
                            >
                                <p
                                    class="
                                        font-serif
                                        text-[16px]
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    Alteração permanente
                                </p>

                                <p
                                    class="
                                        mt-1.5
                                        text-[12px]
                                        leading-5
                                        text-[#6f5544]
                                    "
                                >
                                    A Vida Máxima pertence à ficha.
                                    A Vida Máxima Extra aumenta temporariamente
                                    o limite e concede os PV correspondentes.
                                </p>
                            </div>

                            <div
                                class="
                                    mt-4
                                    grid
                                    grid-cols-3
                                    overflow-hidden

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/65

                                    bg-[#fffdf9]
                                "
                            >
                                <div
                                    class="
                                        border-r
                                        border-[#d8c7ab]/55

                                        px-3
                                        py-3.5
                                        text-center
                                    "
                                >
                                    <span
                                        class="
                                            block
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Atual
                                    </span>

                                    <strong
                                        class="
                                            mt-1
                                            block
                                            font-serif
                                            text-[20px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="currentHp"
                                    ></strong>
                                </div>

                                <div
                                    class="
                                        border-r
                                        border-[#d8c7ab]/55

                                        px-3
                                        py-3.5
                                        text-center
                                    "
                                >
                                    <span
                                        class="
                                            block
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Máxima
                                    </span>

                                    <strong
                                        class="
                                            mt-1
                                            block
                                            font-serif
                                            text-[20px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="maxHp"
                                    ></strong>
                                </div>

                                <div
                                    class="
                                        px-3
                                        py-3.5
                                        text-center
                                    "
                                >
                                    <span
                                        class="
                                            block
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]
                                            text-[#9a6f16]
                                        "
                                    >
                                        Extra
                                    </span>

                                    <strong
                                        class="
                                            mt-1
                                            block
                                            font-serif
                                            text-[20px]
                                            font-black
                                            text-[#9a6f16]
                                        "
                                    >
                                        +<span x-text="temporaryMaxHp"></span>
                                    </strong>
                                </div>
                            </div>
                        </div>

                        {{-- AÇÕES --}}
                        <footer
                            class="
                                flex
                                items-center
                                justify-end
                                gap-2

                                border-t
                                border-[#d8c7ab]/65

                                px-5
                                py-3.5
                            "
                        >
                            <button
                                type="button"
                                @click="closeHpSettings()"
                                class="
                                    min-h-10
                                    rounded-lg

                                    px-4

                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]
                                    text-[#8c6239]

                                    transition
                                    hover:bg-[#efe9dc]
                                "
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                @click="beginHpSettings()"
                                class="
                                    min-h-10
                                    rounded-lg

                                    bg-[#6b1d14]

                                    px-5

                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]
                                    text-[#faf8f2]

                                    transition
                                    hover:bg-[#53150f]
                                "
                            >
                                Continuar
                            </button>
                        </footer>
                    </div>
                </template>

                {{-- =====================================================
                     EDIÇÃO
                ====================================================== --}}
                <template
                    x-if="
                        hpSettingsStep ===
                        'edit'
                    "
                >
                    <div class="flex min-h-0 flex-1 flex-col">
                        {{-- CABEÇALHO --}}
                        <header
                            class="
                                flex
                                items-start
                                justify-between
                                gap-4

                                border-b
                                border-[#d8c7ab]/65

                                px-5
                                py-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.16em]
                                        text-[#8c6239]
                                    "
                                >
                                    Configuração de Combate
                                </p>

                                <h2
                                    class="
                                        mt-1
                                        font-serif
                                        text-[24px]
                                        font-black
                                        leading-tight
                                        text-[#53150f]
                                    "
                                >
                                    Valores de Vida
                                </h2>

                                <p
                                    class="
                                        mt-1
                                        text-[12px]
                                        leading-5
                                        text-[#8c6239]
                                    "
                                >
                                    Ajuste o limite permanente e os pontos de vida extras.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="closeHpSettings()"
                                class="
                                    flex
                                    h-9
                                    w-9
                                    shrink-0
                                    items-center
                                    justify-center

                                    rounded-lg

                                    text-[#8c6239]

                                    transition
                                    hover:bg-[#efe9dc]
                                    hover:text-[#53150f]
                                "
                                title="Fechar"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        d="M6 6l12 12M18 6 6 18"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </header>

                        {{-- CONTEÚDO --}}
                        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                            <div
                                class="
                                    grid
                                    grid-cols-1
                                    gap-3

                                    sm:grid-cols-2
                                "
                            >
                                {{-- VIDA MÁXIMA --}}
                                <label
                                    class="
                                        block

                                        rounded-xl
                                        border
                                        border-[#d8c7ab]/70

                                        bg-[#fffdf9]

                                        p-3.5
                                    "
                                >
                                    <span
                                        class="
                                            block
                                            text-[11px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Vida Máxima
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[11px]
                                            leading-4
                                            text-[#8c6239]/80
                                        "
                                    >
                                        Limite permanente do personagem.
                                    </span>

                                    <input
                                        x-ref="maxHpSettingsInput"
                                        type="number"
                                        min="1"
                                        x-model.number="directMaxHp"
                                        class="
                                            mt-3
                                            h-12
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#cdbb9f]

                                            bg-white

                                            px-3

                                            font-serif
                                            text-[20px]
                                            font-black
                                            text-[#53150f]

                                            outline-none

                                            focus:border-[#6b1d14]
                                            focus:ring-2
                                            focus:ring-[#6b1d14]/10
                                        "
                                    >
                                </label>

                                {{-- VIDA EXTRA --}}
                                <label
                                    class="
                                        block

                                        rounded-xl
                                        border
                                        border-[#d7bd77]/65

                                        bg-[#f8efd9]/70

                                        p-3.5
                                    "
                                >
                                    <span
                                        class="
                                            block
                                            text-[11px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]
                                            text-[#9a6f16]
                                        "
                                    >
                                        Vida Máxima Extra
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[11px]
                                            leading-4
                                            text-[#9a6f16]/85
                                        "
                                    >
                                        Aumenta o limite e concede os PV adicionados.
                                    </span>

                                    <input
                                        type="number"
                                        min="0"
                                        x-model.number="directTemporaryMaxHp"
                                        class="
                                            mt-3
                                            h-12
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#d4b36b]/70

                                            bg-white

                                            px-3

                                            font-serif
                                            text-[20px]
                                            font-black
                                            text-[#9a6f16]

                                            outline-none

                                            focus:border-[#b88920]
                                            focus:ring-2
                                            focus:ring-[#d4b36b]/20
                                        "
                                    >
                                </label>
                            </div>

                            {{-- PREVIEW --}}
                            <div
                                class="
                                    mt-4

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/65

                                    bg-[#f4f1e8]/72

                                    p-4
                                "
                            >
                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-3
                                    "
                                >
                                    <div>
                                        <span
                                            class="
                                                block
                                                text-[10px]
                                                font-black
                                                uppercase
                                                tracking-[0.10em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Novo limite
                                        </span>

                                        <span
                                            class="
                                                mt-0.5
                                                block
                                                text-[11px]
                                                text-[#8c6239]/80
                                            "
                                        >
                                            Base + vida máxima extra
                                        </span>
                                    </div>

                                    <strong
                                        class="
                                            font-serif
                                            text-[22px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        <span
                                            x-text="
                                                Math.max(
                                                    1,
                                                    parseInt(
                                                        directMaxHp
                                                    ) || maxHp
                                                )
                                            "
                                        ></span>

                                        <span
                                            x-show="
                                                (
                                                    parseInt(
                                                        directTemporaryMaxHp
                                                    ) || 0
                                                ) > 0
                                            "
                                            class="text-[#9a6f16]"
                                        >
                                            +
                                            <span
                                                x-text="
                                                    Math.max(
                                                        0,
                                                        parseInt(
                                                            directTemporaryMaxHp
                                                        ) || 0
                                                    )
                                                "
                                            ></span>
                                        </span>
                                    </strong>
                                </div>

                                <div
                                    class="
                                        relative
                                        mt-3
                                        h-2.5
                                        overflow-hidden
                                        rounded-full
                                        bg-[#d8c7ab]
                                    "
                                >
                                    <div
                                        class="
                                            absolute
                                            inset-y-0
                                            left-0
                                            bg-emerald-700
                                        "
                                        :style="`
                                            width:${
                                                (
                                                    Math.max(
                                                        1,
                                                        parseInt(
                                                            directMaxHp
                                                        ) || maxHp
                                                    )
                                                    /
                                                    (
                                                        Math.max(
                                                            1,
                                                            parseInt(
                                                                directMaxHp
                                                            ) || maxHp
                                                        )
                                                        +
                                                        Math.max(
                                                            0,
                                                            parseInt(
                                                                directTemporaryMaxHp
                                                            ) || 0
                                                        )
                                                    )
                                                ) * 100
                                            }%;
                                        `"
                                    ></div>

                                    <div
                                        class="
                                            absolute
                                            inset-y-0
                                            right-0
                                            bg-[#d9a441]
                                        "
                                        :style="`
                                            width:${
                                                (
                                                    Math.max(
                                                        0,
                                                        parseInt(
                                                            directTemporaryMaxHp
                                                        ) || 0
                                                    )
                                                    /
                                                    (
                                                        Math.max(
                                                            1,
                                                            parseInt(
                                                                directMaxHp
                                                            ) || maxHp
                                                        )
                                                        +
                                                        Math.max(
                                                            0,
                                                            parseInt(
                                                                directTemporaryMaxHp
                                                            ) || 0
                                                        )
                                                    )
                                                ) * 100
                                            }%;
                                        `"
                                    ></div>
                                </div>

                                <div
                                    class="
                                        mt-3
                                        flex
                                        flex-wrap
                                        gap-x-5
                                        gap-y-1

                                        text-[11px]
                                        text-[#6f5544]
                                    "
                                >
                                    <span>
                                        Base:
                                        <strong
                                            class="font-black text-[#53150f]"
                                            x-text="
                                                Math.max(
                                                    1,
                                                    parseInt(
                                                        directMaxHp
                                                    ) || maxHp
                                                )
                                            "
                                        ></strong>
                                    </span>

                                    <span>
                                        Extra:
                                        <strong class="font-black text-[#9a6f16]">
                                            +<span
                                                x-text="
                                                    Math.max(
                                                        0,
                                                        parseInt(
                                                            directTemporaryMaxHp
                                                        ) || 0
                                                    )
                                                "
                                            ></span>
                                        </strong>
                                    </span>
                                </div>

                                {{-- CURA AUTOMÁTICA --}}
                                <div
                                    x-show="
                                        Math.max(
                                            0,
                                            (
                                                parseInt(
                                                    directTemporaryMaxHp
                                                ) || 0
                                            ) -
                                            temporaryMaxHp
                                        ) > 0
                                    "
                                    x-cloak
                                    class="
                                        mt-3

                                        rounded-lg
                                        border
                                        border-emerald-200

                                        bg-emerald-50

                                        px-3
                                        py-2.5
                                    "
                                >
                                    <p
                                        class="
                                            text-[11px]
                                            font-black
                                            text-emerald-800
                                        "
                                    >
                                        Cura automática:
                                        +
                                        <span
                                            x-text="
                                                Math.max(
                                                    0,
                                                    (
                                                        parseInt(
                                                            directTemporaryMaxHp
                                                        ) || 0
                                                    ) -
                                                    temporaryMaxHp
                                                )
                                            "
                                        ></span>
                                        PV
                                    </p>

                                    <p
                                        class="
                                            mt-0.5
                                            text-[11px]
                                            leading-4
                                            text-emerald-800/80
                                        "
                                    >
                                        Ao aumentar a Vida Máxima Extra, a mesma quantidade é adicionada à vida atual.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- AÇÕES --}}
                        <footer
                            class="
                                flex
                                items-center
                                justify-end
                                gap-2

                                border-t
                                border-[#d8c7ab]/65

                                px-5
                                py-3.5
                            "
                        >
                            <button
                                type="button"
                                @click="closeHpSettings()"
                                class="
                                    min-h-10
                                    rounded-lg

                                    px-4

                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]
                                    text-[#8c6239]

                                    transition
                                    hover:bg-[#efe9dc]
                                "
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                @click="saveHpSettings()"
                                class="
                                    min-h-10
                                    rounded-lg

                                    bg-[#6b1d14]

                                    px-5

                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]
                                    text-[#faf8f2]

                                    transition
                                    hover:bg-[#53150f]
                                "
                            >
                                Salvar
                            </button>
                        </footer>
                    </div>
                </template>
            </section>
        </div>
    </template>


</div>