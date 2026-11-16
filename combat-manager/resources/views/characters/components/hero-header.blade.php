@props([
    'character',
    'identityCharacter' => null,
    'inventoryCharacter' => null,
    'imageOverrideUrl' => null,
])

@php
    /*
     * character          = ficha mecânica atual
     * identityCharacter  = Character original
     * inventoryCharacter = Character original (inventário compartilhado)
     */
    $identityCharacter =
        $identityCharacter
        ?? $character;

    $inventoryCharacter =
        $inventoryCharacter
        ?? $identityCharacter;

    $combat = $character->combat;

    /*
     * A Exaustão é compartilhada entre Forma Normal e Nova Ficha.
     * Ela pertence exclusivamente ao Character original.
     */
    $identityCombat =
        $identityCharacter->combat;

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
            $identityCharacter->sheet_settings
            ?? null
        )
            ? $identityCharacter->sheet_settings
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
                        $identityCombat?->exhaustion_level
                        ?? 0
                    )
                )
            )
            : 0;

    /*
     * Na ficha original o controle de Exaustão continua sempre visível
     * quando a regra estiver ativa, inclusive em nível 0.
     *
     * Na "Nova Ficha", o selo aparece somente quando aquela ficha auxiliar
     * realmente possui Exaustão. Ao reduzir para 0 pelo modal, ele some.
     */
    $hideZeroExhaustion =
        (bool) (
            $character->is_transformation_form
            ?? false
        );

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

    /*
     * O cabeçalho exibe as classes da ficha mecânica,
     * mas o modal aberto pela foto continua editando o Character original.
     */
    $identityClasses =
        $identityCharacter
            ->classes
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
        $imageOverrideUrl
        ?: (
            $identityCharacter->image_path
                ? Storage::url(
                    $identityCharacter->image_path
                )
                : null
        );

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
    | REDUÇÃO DE VIDA MÁXIMA
    |--------------------------------------------------------------------------
    |
    | Guardamos a redução em combat.overrides.hp.max_reduction para não
    | exigir uma nova coluna no banco e para preservar a Vida Máxima base.
    |
    | A redução nunca pode eliminar 100% do limite: o personagem mantém
    | ao menos 1 PV máximo efetivo.
    |
    */

    $maxHpReduction =
        min(
            max(
                0,
                $maxHp + $temporaryMaxHp - 1
            ),
            max(
                0,
                (int) data_get(
                    $combatOverrides,
                    'hp.max_reduction',
                    0
                )
            )
        );

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
        $inventoryCharacter->items
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
                function ($item) use ($inventoryCharacter) {
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
                                            $inventoryCharacter,

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
                                        $inventoryCharacter,

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
        $inventoryCharacter->items
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
                function ($item) use ($inventoryCharacter) {
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
                                            $inventoryCharacter,

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
                                        $inventoryCharacter,

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

    /*
    |--------------------------------------------------------------------------
    | TEMA ATUAL
    |--------------------------------------------------------------------------
    |
    | Alguns painéis do Hero Header usam x-teleport="body". Como esses
    | portais deixam de herdar as variáveis da ficha, reaplicamos o tema
    | explicitamente nos elementos que precisam seguir a paleta escolhida.
    |
    */
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

    $characterTheme = strtolower(trim((string) data_get(
        $sheetSettings,
        'appearance.theme',
        'spellbound'
    )));

    if (!in_array($characterTheme, $allowedThemes, true)) {
        $characterTheme = 'spellbound';
    }

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


            /*
            |--------------------------------------------------------------------------
            | FEEDBACK DE IMPACTO — BARRA DE PV
            |--------------------------------------------------------------------------
            */

            @keyframes hp-damage-sweep {
                0% {
                    opacity: 0;
                    transform:
                        translateX(24%)
                        skewX(-16deg);
                }

                16% {
                    opacity: .95;
                }

                100% {
                    opacity: 0;
                    transform:
                        translateX(-46%)
                        skewX(-16deg);
                }
            }

            @keyframes hp-shield-impact {
                0% {
                    opacity: 0;
                    transform: scaleX(.82);
                    filter: brightness(1);
                }

                22% {
                    opacity: .95;
                    transform: scaleX(1.02);
                    filter: brightness(1.5);
                }

                100% {
                    opacity: 0;
                    transform: scaleX(1.08);
                    filter: brightness(1);
                }
            }

            @keyframes hp-feedback-float {
                0% {
                    opacity: 0;
                    transform:
                        translateY(6px)
                        scale(.88);
                    filter: blur(1px);
                }

                16% {
                    opacity: 1;
                    transform:
                        translateY(0)
                        scale(1.04);
                    filter: blur(0);
                }

                72% {
                    opacity: 1;
                    transform:
                        translateY(-8px)
                        scale(1);
                }

                100% {
                    opacity: 0;
                    transform:
                        translateY(-14px)
                        scale(.96);
                }
            }

            @keyframes hp-heal-rise {
                0% {
                    opacity: 0;
                    transform:
                        translateY(7px)
                        scale(.9);
                }

                20% {
                    opacity: .95;
                    transform:
                        translateY(0)
                        scale(1.04);
                }

                100% {
                    opacity: 0;
                    transform:
                        translateY(-13px)
                        scale(.98);
                }
            }

            .animate-damage-blue {
                animation:
                    shield-hit .46s cubic-bezier(.2,.9,.2,1),
                    shield-flash .62s ease-out;
            }

            .hp-damage-sweep {
                animation:
                    hp-damage-sweep
                    .52s
                    cubic-bezier(.2,.78,.2,1)
                    both;
            }

            .hp-shield-impact {
                animation:
                    hp-shield-impact
                    .58s
                    cubic-bezier(.2,.9,.2,1)
                    both;
            }

            .hp-feedback-float {
                animation:
                    hp-feedback-float
                    1.05s
                    cubic-bezier(.16,.84,.22,1)
                    both;
            }

            .hp-heal-rise {
                animation:
                    hp-heal-rise
                    1.05s
                    cubic-bezier(.16,.84,.22,1)
                    both;
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

            @keyframes shield-hit {
                0%, 100% {
                    transform: translate3d(0,0,0) scale(1);
                }

                18% {
                    transform: translateX(-3px) scale(.995);
                }

                38% {
                    transform: translateX(3px) scale(1.006);
                }

                62% {
                    transform: translateX(-1px);
                }
            }

            @keyframes shield-flash {
                0%, 100% {
                    box-shadow:
                        inset 0 0 0 rgba(56,189,248,0),
                        0 0 0 rgba(56,189,248,0);
                }

                28% {
                    box-shadow:
                        inset 0 0 42px rgba(56,189,248,.28),
                        0 0 20px rgba(56,189,248,.22);
                }

                68% {
                    box-shadow:
                        inset 0 0 16px rgba(56,189,248,.10),
                        0 0 7px rgba(56,189,248,.08);
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
                    border-color:
                        rgba(205,187,159,.60);

                    box-shadow:
                        0 0 0 rgba(127,29,29,0),
                        inset 0 0 0 rgba(127,29,29,0);
                }

                50% {
                    border-color:
                        rgba(127,29,29,.28);

                    box-shadow:
                        0 0 13px rgba(127,29,29,.15),
                        inset 0 0 12px rgba(127,29,29,.045);
                }
            }

            @keyframes critical {
                0%, 100% {
                    border-color:
                        rgba(205,187,159,.60);

                    box-shadow:
                        0 0 0 rgba(153,27,27,0),
                        inset 0 0 0 rgba(153,27,27,0);
                }

                50% {
                    border-color:
                        rgba(153,27,27,.40);

                    box-shadow:
                        0 0 17px rgba(153,27,27,.22),
                        inset 0 0 15px rgba(153,27,27,.065);
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
                    bloodied
                    2.8s
                    ease-in-out
                    infinite;
            }

            .critical-card {
                animation:
                    critical
                    2.35s
                    ease-in-out
                    infinite;
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
            .hero-hp-modal-open .animate-damage-blue,
            .hero-hp-modal-open .animate-damage-photo,
            .hero-hp-modal-open .animate-heal-green,
            .hero-hp-modal-open .hp-damage-sweep,
            .hero-hp-modal-open .hp-shield-impact,
            .hero-hp-modal-open .hp-feedback-float,
            .hero-hp-modal-open .hp-heal-rise,
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

            /*
            |--------------------------------------------------------------------------
            | HERO HEADER V18 — PADRÃO VISUAL / PORTAIS / MOTION
            |--------------------------------------------------------------------------
            |
            | Mantém toda a lógica mecânica e as animações semânticas de vida,
            | morte e cura. Esta camada cuida apenas de:
            | - portais que precisam herdar o tema;
            | - menu de descanso;
            | - modal de configuração de PV;
            | - gaveta/reabertura de salvamentos contra a morte;
            | - hierarquia visual consistente com os demais componentes.
            |
            */

            /* ==============================================================
               MENU DE DESCANSO
            ============================================================== */

            .character-sheet-theme[data-theme].hero-rest-popover {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                color:
                    var(--theme-modal-text, var(--theme-text-primary)) !important;

                box-shadow:
                    0 18px 52px rgba(0,0,0,.20),
                    var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.08)) !important;
            }

            .character-sheet-theme[data-theme].hero-rest-popover
            .hero-rest-popover-header {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme].hero-rest-popover
            .hero-rest-popover-body {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            }

            .character-sheet-theme[data-theme].hero-rest-popover
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme].hero-rest-popover
            [class*="text-[#6b1d14]"] {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme].hero-rest-popover
            [class*="text-[#76553f]"],
            .character-sheet-theme[data-theme].hero-rest-popover
            [class*="text-[#7d604d]"],
            .character-sheet-theme[data-theme].hero-rest-popover
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme].hero-rest-popover
            [class*="text-[#a0774d]"] {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme].hero-rest-popover
            .hero-rest-option {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.06)) !important;
            }

            .character-sheet-theme[data-theme].hero-rest-popover
            .hero-rest-option:hover:not(:disabled) {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme].hero-rest-popover
            .hero-rest-option
            > span:first-child {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               MODAL DE VIDA
            ============================================================== */

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal {
                width:
                    min(
                        580px,
                        calc(100vw - 28px)
                    ) !important;

                max-height:
                    min(
                        92dvh,
                        820px
                    ) !important;

                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                border-radius:
                    18px !important;

                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                color:
                    var(--theme-modal-text, var(--theme-text-primary)) !important;

                box-shadow:
                    var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.30)) !important;
            }

            @supports not (height: 1dvh) {
                .character-sheet-theme[data-theme]
                .hero-hp-settings-modal {
                    max-height:
                        min(
                            92vh,
                            820px
                        ) !important;
                }
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            header,
            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            footer {
                flex:
                    0 0 auto;

                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            header
            > button {
                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            header
            > button:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            [class*="text-[#53150f]"] {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            [class*="text-[#6f5544]"] {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            [class*="border-[#d8c7ab]"],
            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            [class*="border-[#cdbb9f]"] {
                border-color:
                    var(--theme-border) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            [class*="bg-[#f4f1e8]"],
            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            [class*="bg-[#fffdf9]"] {
                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            input[type="number"] {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;

                box-shadow:
                    none !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            input[type="number"]:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 15%,
                        transparent
                    ) !important;
            }

            /*
             * Vida Máxima Extra continua dourada por significado mecânico,
             * mas a superfície deixa de ser um bloco bege fixo.
             */
            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-extra-card {
                border-color:
                    color-mix(
                        in srgb,
                        #b88920 34%,
                        var(--theme-border)
                    ) !important;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-surface-card) 92%,
                        #d9a441 8%
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-preview-card {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            footer
            button:first-child {
                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            footer
            button:first-child:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            footer
            button:last-child {
                border:
                    1px solid
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;

                box-shadow:
                    var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.08)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            footer
            button:last-child:hover {
                background:
                    var(--theme-button-hover-background) !important;
            }


            /* ==============================================================
               SALVAMENTOS CONTRA A MORTE
            ============================================================== */

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-drawer-panel {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-module-background) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 22px 55px
                    color-mix(
                        in srgb,
                        var(--theme-accent-deep) 12%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-drawer-band {
                background:
                    linear-gradient(
                        90deg,
                        var(--theme-accent-deep) 0%,
                        var(--theme-accent-strong) 50%,
                        var(--theme-accent-deep) 100%
                    ) !important;

                color:
                    var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-drawer-band
            [class*="text-[#f0d5ad]"] {
                color:
                    color-mix(
                        in srgb,
                        var(--theme-button-text) 78%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-tab-button {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    var(--theme-modal-control-shadow, 0 7px 18px rgba(0,0,0,.10)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-tab-button:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-tab-button
            > span:first-child {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-accent-soft) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-tab-button
            > span:first-child
            > span {
                background:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-header-v18
            .hero-death-tab-button
            svg {
                color:
                    var(--theme-label) !important;
            }


            /* ==============================================================
               ACESSIBILIDADE / MOVIMENTO REDUZIDO
            ============================================================== */

            @media (prefers-reduced-motion: reduce) {
                .hero-rest-popover,
                .hero-death-drawer-panel,
                .hero-death-tab-button {
                    transition-duration:
                        1ms !important;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | VIDA MÁXIMA REDUZIDA
            |--------------------------------------------------------------------------
            |
            | A barra completa representa o potencial total:
            | Vida Máxima + Vida Máxima Extra.
            |
            | A redução ocupa visualmente a extremidade direita em roxo,
            | deixando explícita a parte do limite que está bloqueada.
            |
            */

            .hero-life-modal-source[data-hp-reduction-active="true"]
            .hero-life-bar::after {
                content: "";

                position:
                    absolute;

                inset-block:
                    0;

                right:
                    0;

                z-index:
                    18;

                width:
                    var(--hero-max-hp-reduction, 0%);

                pointer-events:
                    none;

                border-left:
                    1px solid rgba(221,214,254,.62);

                background:
                    linear-gradient(
                        90deg,
                        rgba(88,28,135,.92) 0%,
                        rgba(107,33,168,.96) 48%,
                        rgba(76,29,149,.98) 100%
                    );

                box-shadow:
                    inset 8px 0 16px rgba(35,16,55,.18);

                transition:
                    width .7s ease;
            }

            .hero-life-modal-source[data-hp-reduction-active="true"]
            .hero-life-bar::before {
                content: "";

                position:
                    absolute;

                inset-block:
                    0;

                right:
                    0;

                z-index:
                    19;

                width:
                    var(--hero-max-hp-reduction, 0%);

                pointer-events:
                    none;

                opacity:
                    .28;

                background:
                    repeating-linear-gradient(
                        -45deg,
                        rgba(255,255,255,.16) 0,
                        rgba(255,255,255,.16) 2px,
                        transparent 2px,
                        transparent 6px
                    );

                transition:
                    width .7s ease;
            }

            .hero-life-modal-source[data-hp-reduction-active="true"]
            .hero-life-heading::after {
                content:
                    "−" attr(data-hp-reduction);

                display:
                    inline-flex;

                min-height:
                    18px;

                align-items:
                    center;

                border:
                    1px solid rgba(126,34,206,.24);

                border-radius:
                    4px;

                background:
                    rgba(237,233,254,.88);

                padding:
                    2px 6px;

                font-size:
                    8px;

                font-weight:
                    900;

                line-height:
                    1;

                letter-spacing:
                    .04em;

                color:
                    #6b21a8;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-reduction-card {
                border-color:
                    color-mix(
                        in srgb,
                        #7e22ce 34%,
                        var(--theme-border)
                    ) !important;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-surface-card) 92%,
                        #7e22ce 8%
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-reduction-card
            input {
                border-color:
                    color-mix(
                        in srgb,
                        #7e22ce 38%,
                        var(--theme-input-border)
                    ) !important;

                color:
                    #6b21a8 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-reduction-card
            input:focus {
                border-color:
                    #7e22ce !important;

                box-shadow:
                    0 0 0 3px
                    rgba(126,34,206,.12) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-reduction-label,
            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-reduction-value {
                color:
                    #6b21a8 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-reduction-preview {
                background:
                    linear-gradient(
                        90deg,
                        #581c87 0%,
                        #7e22ce 52%,
                        #4c1d95 100%
                    ) !important;
            }

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .hero-hp-settings-modal
            .hero-hp-reduction-card {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-surface-card) 84%,
                        #7e22ce 16%
                    ) !important;
            }


            /*
            |--------------------------------------------------------------------------
            | HERO HEADER V20 — CARDS DE VIDA ALINHADOS
            |--------------------------------------------------------------------------
            |
            | Padrão visual reutilizável:
            | - cards irmãos com mesma altura;
            | - área de título reservada;
            | - área de descrição reservada;
            | - controle principal ancorado na mesma linha inferior.
            |
            */

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-limit-grid {
                align-items:
                    stretch;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-limit-card {
                display:
                    flex;

                min-width:
                    0;

                min-height:
                    176px;

                flex-direction:
                    column;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-limit-title {
                display:
                    flex;

                min-height:
                    30px;

                align-items:
                    flex-start;

                font-size:
                    10.5px !important;

                line-height:
                    1.28 !important;

                letter-spacing:
                    .07em !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-limit-copy {
                min-height:
                    48px;

                margin-top:
                    5px !important;

                font-size:
                    11px !important;

                line-height:
                    1.45 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-limit-input {
                margin-top:
                    auto !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-limit-summary {
                display:
                    grid;

                grid-template-columns:
                    repeat(3, minmax(0, 1fr));

                gap:
                    10px 14px;

                align-items:
                    center;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-settings-modal
            .hero-hp-limit-summary
            > span {
                min-width:
                    0;
            }

            @media (max-width: 639px) {
                .character-sheet-theme[data-theme]
                .hero-hp-settings-modal
                .hero-hp-limit-card {
                    min-height:
                        0;
                }

                .character-sheet-theme[data-theme]
                .hero-hp-settings-modal
                .hero-hp-limit-title,
                .character-sheet-theme[data-theme]
                .hero-hp-settings-modal
                .hero-hp-limit-copy {
                    min-height:
                        0;
                }

                .character-sheet-theme[data-theme]
                .hero-hp-settings-modal
                .hero-hp-limit-input {
                    margin-top:
                        12px !important;
                }

                .character-sheet-theme[data-theme]
                .hero-hp-settings-modal
                .hero-hp-limit-summary {
                    grid-template-columns:
                        1fr;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | HERO HEADER V21 — SISTEMA DE DANO / ESTADO
            |--------------------------------------------------------------------------
            |
            | Regra visual:
            | - impactos são curtos e acontecem somente quando o valor muda;
            | - Ferido/Crítico são estados persistentes, mas não fazem o Hero
            |   inteiro pulsar eternamente;
            | - PV temporários reagem em azul sem sacudir o retrato quando
            |   absorvem todo o golpe;
            | - cura usa luz verde curta, sem movimento agressivo.
            |
            */

            @keyframes hero-impact-portrait-soft {
                0%, 100% {
                    transform:
                        translate3d(0,0,0)
                        rotate(0deg);
                }

                32% {
                    transform:
                        translate3d(-1px,0,0)
                        rotate(-.15deg);
                }

                62% {
                    transform:
                        translate3d(1px,0,0)
                        rotate(.12deg);
                }
            }

            @keyframes hero-impact-portrait-medium {
                0%, 100% {
                    transform:
                        translate3d(0,0,0)
                        rotate(0deg);
                }

                18% {
                    transform:
                        translate3d(-3px,0,0)
                        rotate(-.45deg);
                }

                42% {
                    transform:
                        translate3d(2px,0,0)
                        rotate(.32deg);
                }

                68% {
                    transform:
                        translate3d(-1px,0,0)
                        rotate(-.12deg);
                }
            }

            @keyframes hero-impact-portrait-heavy {
                0%, 100% {
                    transform:
                        translate3d(0,0,0)
                        rotate(0deg)
                        scale(1);
                }

                14% {
                    transform:
                        translate3d(-5px,0,0)
                        rotate(-.7deg)
                        scale(.992);
                }

                34% {
                    transform:
                        translate3d(4px,0,0)
                        rotate(.55deg)
                        scale(.997);
                }

                58% {
                    transform:
                        translate3d(-2px,0,0)
                        rotate(-.24deg)
                        scale(1);
                }

                78% {
                    transform:
                        translate3d(1px,0,0)
                        rotate(.10deg);
                }
            }

            @keyframes hero-life-hit-soft {
                0%, 100% {
                    filter:
                        brightness(1);
                }

                35% {
                    filter:
                        brightness(1.12)
                        saturate(1.08);
                }
            }

            @keyframes hero-life-hit-medium {
                0%, 100% {
                    filter:
                        brightness(1);
                    box-shadow:
                        inset 0 0 0 rgba(220,38,38,0);
                }

                34% {
                    filter:
                        brightness(1.18)
                        saturate(1.12);
                    box-shadow:
                        inset 0 0 22px rgba(220,38,38,.22);
                }
            }

            @keyframes hero-life-hit-heavy {
                0%, 100% {
                    filter:
                        brightness(1);
                    box-shadow:
                        inset 0 0 0 rgba(220,38,38,0);
                }

                28% {
                    filter:
                        brightness(1.28)
                        saturate(1.18);
                    box-shadow:
                        inset 0 0 34px rgba(220,38,38,.32);
                }

                62% {
                    filter:
                        brightness(1.08);
                    box-shadow:
                        inset 0 0 12px rgba(220,38,38,.12);
                }
            }

            @keyframes hero-shield-impact-v21 {
                0%, 100% {
                    filter:
                        brightness(1);
                    box-shadow:
                        inset 0 0 0 rgba(56,189,248,0);
                }

                34% {
                    filter:
                        brightness(1.16);
                    box-shadow:
                        inset 0 0 28px rgba(56,189,248,.26),
                        0 0 10px rgba(56,189,248,.12);
                }
            }

            @keyframes hero-heal-impact-v21 {
                0%, 100% {
                    filter:
                        brightness(1);
                    box-shadow:
                        inset 0 0 0 rgba(34,197,94,0);
                }

                38% {
                    filter:
                        brightness(1.16);
                    box-shadow:
                        inset 0 0 24px rgba(34,197,94,.18),
                        0 0 10px rgba(34,197,94,.10);
                }
            }

            @keyframes hero-critical-bar-breathe-v21 {
                0%, 100% {
                    border-color:
                        rgba(153,27,27,.44);

                    box-shadow:
                        inset 0 0 0 1px rgba(153,27,27,.10),
                        0 0 0 rgba(153,27,27,0);
                }

                50% {
                    border-color:
                        rgba(185,28,28,.72);

                    box-shadow:
                        inset 0 0 0 1px rgba(185,28,28,.18),
                        0 0 12px rgba(153,27,27,.12);
                }
            }


            /* ==============================================================
               ESTADOS PERSISTENTES
            ============================================================== */

            .hero-header-v21[data-health-state="bloodied"]
            .hero-life-bar {
                border-color:
                    rgba(180,83,9,.58) !important;

                box-shadow:
                    inset 0 0 0 1px rgba(180,83,9,.08),
                    inset 0 -8px 18px rgba(127,29,29,.06) !important;
            }

            .hero-header-v21[data-health-state="bloodied"]
            .hero-data-status-strip {
                box-shadow:
                    inset 3px 0 0 rgba(180,83,9,.48) !important;
            }

            .hero-header-v21[data-health-state="bloodied"]
            .hero-data-portrait {
                border-color:
                    rgba(180,83,9,.34) !important;
            }


            .hero-header-v21[data-health-state="critical"]
            .hero-life-bar {
                animation:
                    hero-critical-bar-breathe-v21
                    2.8s
                    ease-in-out
                    infinite;

                border-color:
                    rgba(153,27,27,.58) !important;
            }

            .hero-header-v21[data-health-state="critical"]
            .hero-data-status-strip {
                box-shadow:
                    inset 3px 0 0 rgba(185,28,28,.72) !important;
            }

            .hero-header-v21[data-health-state="critical"]
            .hero-data-portrait {
                border-color:
                    rgba(185,28,28,.46) !important;

                box-shadow:
                    0 0 0 1px rgba(185,28,28,.05),
                    0 4px 12px rgba(127,29,29,.08) !important;
            }


            .hero-header-v21[data-health-state="downed"]
            .hero-life-bar,
            .hero-header-v21[data-health-state="dead"]
            .hero-life-bar {
                border-color:
                    rgba(127,29,29,.62) !important;
            }


            /* ==============================================================
               IMPACTO — RETRATO
            ============================================================== */

            .hero-header-v21[data-impact-kind="damage"]
            .hero-data-portrait,
            .hero-header-v21[data-impact-kind="mixed"]
            .hero-data-portrait {
                transform-origin:
                    center center;
            }

            .hero-header-v21[data-impact-kind="damage"][data-impact-strength="soft"]
            .hero-data-portrait,
            .hero-header-v21[data-impact-kind="mixed"][data-impact-strength="soft"]
            .hero-data-portrait {
                animation:
                    hero-impact-portrait-soft
                    .34s
                    ease-out
                    both !important;
            }

            .hero-header-v21[data-impact-kind="damage"][data-impact-strength="medium"]
            .hero-data-portrait,
            .hero-header-v21[data-impact-kind="mixed"][data-impact-strength="medium"]
            .hero-data-portrait {
                animation:
                    hero-impact-portrait-medium
                    .46s
                    cubic-bezier(.2,.8,.2,1)
                    both !important;
            }

            .hero-header-v21[data-impact-kind="damage"][data-impact-strength="heavy"]
            .hero-data-portrait,
            .hero-header-v21[data-impact-kind="mixed"][data-impact-strength="heavy"]
            .hero-data-portrait {
                animation:
                    hero-impact-portrait-heavy
                    .62s
                    cubic-bezier(.18,.82,.22,1)
                    both !important;
            }


            /* ==============================================================
               IMPACTO — BARRA DE VIDA
            ============================================================== */

            .hero-header-v21[data-impact-kind="damage"][data-impact-strength="soft"]
            .hero-life-bar,
            .hero-header-v21[data-impact-kind="mixed"][data-impact-strength="soft"]
            .hero-life-bar {
                animation:
                    hero-life-hit-soft
                    .34s
                    ease-out
                    both !important;
            }

            .hero-header-v21[data-impact-kind="damage"][data-impact-strength="medium"]
            .hero-life-bar,
            .hero-header-v21[data-impact-kind="mixed"][data-impact-strength="medium"]
            .hero-life-bar {
                animation:
                    hero-life-hit-medium
                    .46s
                    ease-out
                    both !important;
            }

            .hero-header-v21[data-impact-kind="damage"][data-impact-strength="heavy"]
            .hero-life-bar,
            .hero-header-v21[data-impact-kind="mixed"][data-impact-strength="heavy"]
            .hero-life-bar {
                animation:
                    hero-life-hit-heavy
                    .62s
                    ease-out
                    both !important;
            }

            .hero-header-v21[data-impact-kind="shield"]
            .hero-life-bar,
            .hero-header-v21[data-impact-kind="mixed"]
            .hp-shield-impact {
                animation:
                    hero-shield-impact-v21
                    .48s
                    ease-out
                    both !important;
            }

            .hero-header-v21[data-impact-kind="heal"]
            .hero-life-bar {
                animation:
                    hero-heal-impact-v21
                    .52s
                    ease-out
                    both !important;
            }

            /*
             * Neutraliza as animações legadas aplicadas pelo hero-data.
             * A V21 passa a ser a única autoridade sobre o movimento do retrato.
             */
            .hero-header-v21
            .hero-data-portrait.animate-damage-photo,
            .hero-header-v21
            .hero-data-portrait.animate-damage-red,
            .hero-header-v21
            .hero-data-portrait.animate-heal-green {
                box-shadow:
                    inherit;
            }


            /* ==============================================================
               MODAL DE PV ABERTO
            ============================================================== */

            .hero-header-v21.hero-hp-modal-open
            .hero-life-bar,
            .hero-header-v21.hero-hp-modal-open
            .hero-data-portrait {
                animation:
                    none !important;
            }


            /* ==============================================================
               MOVIMENTO REDUZIDO
            ============================================================== */

            @media (prefers-reduced-motion: reduce) {
                .hero-header-v21
                .hero-data-portrait,
                .hero-header-v21
                .hero-life-bar,
                .hero-header-v21
                .hp-shield-impact {
                    animation:
                        none !important;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | HERO HEADER V22 — ESTADOS DE VIDA / IMPACTOS VARIANTES
            |--------------------------------------------------------------------------
            |
            | Filosofia:
            | - estado persistente = discreto, legível, sem tremer o layout;
            | - impacto = curto, reativo e proporcional;
            | - cada camada de proteção possui uma linguagem própria:
            |   azul (PV temporários), dourado (Vida Máxima Extra),
            |   vermelho (PV normais).
            |
            */


            /* ==============================================================
               ESTADO: SANGRANDO
            ============================================================== */

            @keyframes hero-bloodied-portrait-v22 {
                0%, 68%, 100% {
                    filter:
                        saturate(1)
                        brightness(1);

                    box-shadow:
                        0 0 0 1px rgba(153,27,27,.04),
                        0 4px 12px rgba(127,29,29,.04);
                }

                76% {
                    filter:
                        saturate(1.14)
                        brightness(1.025);

                    box-shadow:
                        0 0 0 1px rgba(185,28,28,.20),
                        0 0 14px rgba(153,27,27,.14),
                        0 4px 14px rgba(127,29,29,.08);
                }

                84% {
                    filter:
                        saturate(1.02)
                        brightness(1);

                    box-shadow:
                        0 0 0 1px rgba(153,27,27,.08),
                        0 0 4px rgba(153,27,27,.05);
                }

                91% {
                    filter:
                        saturate(1.10)
                        brightness(1.018);

                    box-shadow:
                        0 0 0 1px rgba(185,28,28,.15),
                        0 0 9px rgba(153,27,27,.10);
                }
            }

            @keyframes hero-bloodied-bar-v22 {
                0%, 68%, 100% {
                    box-shadow:
                        inset 0 0 0 1px rgba(153,27,27,.08),
                        inset 0 0 0 rgba(127,29,29,0);
                }

                76% {
                    box-shadow:
                        inset 0 0 0 1px rgba(185,28,28,.20),
                        inset 0 0 18px rgba(127,29,29,.14),
                        0 0 8px rgba(153,27,27,.08);
                }

                91% {
                    box-shadow:
                        inset 0 0 0 1px rgba(185,28,28,.14),
                        inset 0 0 11px rgba(127,29,29,.09);
                }
            }

            .hero-header-v22[data-health-state="bloodied"]
            .hero-data-portrait {
                animation:
                    hero-bloodied-portrait-v22
                    3.8s
                    ease-in-out
                    infinite;
            }

            .hero-header-v22[data-health-state="bloodied"]
            .hero-life-bar {
                animation:
                    hero-bloodied-bar-v22
                    3.8s
                    ease-in-out
                    infinite;
            }

            .hero-header-v22[data-health-state="bloodied"]
            .hero-data-status-strip {
                box-shadow:
                    inset 3px 0 0 rgba(185,28,28,.52),
                    inset 12px 0 18px -18px rgba(185,28,28,.55) !important;
            }


            /* ==============================================================
               ESTADO: CRÍTICO
            ============================================================== */

            @keyframes hero-critical-double-beat-v22 {
                0%, 100% {
                    filter:
                        brightness(1)
                        saturate(1);

                    box-shadow:
                        inset 0 0 0 1px rgba(153,27,27,.16),
                        0 0 0 rgba(153,27,27,0);
                }

                12% {
                    filter:
                        brightness(1.08)
                        saturate(1.18);

                    box-shadow:
                        inset 0 0 22px rgba(153,27,27,.20),
                        0 0 11px rgba(153,27,27,.12);
                }

                22% {
                    filter:
                        brightness(1)
                        saturate(1.03);
                }

                32% {
                    filter:
                        brightness(1.055)
                        saturate(1.13);

                    box-shadow:
                        inset 0 0 14px rgba(153,27,27,.14),
                        0 0 7px rgba(153,27,27,.08);
                }

                52% {
                    filter:
                        brightness(1)
                        saturate(1);
                }
            }

            @keyframes hero-critical-portrait-v22 {
                0%, 100% {
                    box-shadow:
                        0 0 0 1px rgba(185,28,28,.15),
                        0 4px 12px rgba(127,29,29,.08);
                }

                12% {
                    box-shadow:
                        0 0 0 1px rgba(220,38,38,.38),
                        0 0 18px rgba(185,28,28,.20),
                        0 4px 14px rgba(127,29,29,.10);
                }

                32% {
                    box-shadow:
                        0 0 0 1px rgba(220,38,38,.28),
                        0 0 11px rgba(185,28,28,.13);
                }
            }

            .hero-header-v22[data-health-state="critical"]
            .hero-life-bar {
                animation:
                    hero-critical-double-beat-v22
                    2.15s
                    ease-in-out
                    infinite !important;
            }

            .hero-header-v22[data-health-state="critical"]
            .hero-data-portrait {
                animation:
                    hero-critical-portrait-v22
                    2.15s
                    ease-in-out
                    infinite;
            }


            /* ==============================================================
               DANO REAL — FLASH VERMELHO
            ============================================================== */

            @keyframes hero-damage-red-flash-v22 {
                0%, 100% {
                    filter:
                        brightness(1)
                        saturate(1);

                    box-shadow:
                        0 0 0 1px rgba(220,38,38,0),
                        0 4px 12px rgba(127,29,29,.05);
                }

                20% {
                    filter:
                        sepia(.20)
                        saturate(1.75)
                        brightness(1.08);

                    box-shadow:
                        0 0 0 2px rgba(239,68,68,.30),
                        0 0 24px rgba(220,38,38,.24),
                        inset 0 0 18px rgba(220,38,38,.10);
                }

                52% {
                    filter:
                        sepia(.06)
                        saturate(1.20)
                        brightness(1.02);

                    box-shadow:
                        0 0 0 1px rgba(220,38,38,.12),
                        0 0 10px rgba(220,38,38,.10);
                }
            }

            .hero-header-v22[data-impact-damage="true"][data-impact-strength="soft"]
            .hero-data-portrait {
                animation:
                    hero-impact-portrait-soft
                    .34s
                    ease-out
                    both,
                    hero-damage-red-flash-v22
                    .42s
                    ease-out
                    both !important;
            }

            .hero-header-v22[data-impact-damage="true"][data-impact-strength="medium"]
            .hero-data-portrait {
                animation:
                    hero-impact-portrait-medium
                    .48s
                    cubic-bezier(.2,.8,.2,1)
                    both,
                    hero-damage-red-flash-v22
                    .54s
                    ease-out
                    both !important;
            }

            .hero-header-v22[data-impact-damage="true"][data-impact-strength="heavy"]
            .hero-data-portrait {
                animation:
                    hero-impact-portrait-heavy
                    .66s
                    cubic-bezier(.18,.82,.22,1)
                    both,
                    hero-damage-red-flash-v22
                    .72s
                    ease-out
                    both !important;
            }


            /* ==============================================================
               VIDA MÁXIMA EXTRA — IMPACTO DOURADO
            ============================================================== */

            @keyframes hero-bonus-portrait-v22 {
                0%, 100% {
                    filter:
                        brightness(1)
                        saturate(1);
                }

                24% {
                    filter:
                        brightness(1.10)
                        saturate(1.20)
                        sepia(.10);

                    box-shadow:
                        0 0 13px rgba(217,164,65,.16);
                }

                55% {
                    filter:
                        brightness(1.02)
                        saturate(1.06);
                }
            }

            .hero-header-v22[data-impact-bonus="true"][data-impact-damage="false"]
            .hero-data-portrait {
                animation:
                    hero-bonus-portrait-v22
                    .56s
                    ease-out
                    both !important;
            }


            /* ==============================================================
               PV TEMPORÁRIOS — BLOQUEIO AZUL
            ============================================================== */

            @keyframes hero-shield-portrait-v22 {
                0%, 100% {
                    filter:
                        brightness(1);

                    box-shadow:
                        0 0 0 rgba(56,189,248,0);
                }

                24% {
                    filter:
                        brightness(1.08)
                        saturate(1.10);

                    box-shadow:
                        0 0 0 1px rgba(125,211,252,.28),
                        0 0 18px rgba(56,189,248,.18);
                }

                62% {
                    filter:
                        brightness(1.02);

                    box-shadow:
                        0 0 7px rgba(56,189,248,.07);
                }
            }

            .hero-header-v22[data-impact-shield="true"][data-impact-damage="false"][data-impact-bonus="false"]
            .hero-data-portrait {
                animation:
                    hero-shield-portrait-v22
                    .56s
                    ease-out
                    both !important;
            }


            /* ==============================================================
               TRANSIÇÕES ENTRE ESTADOS
            ============================================================== */

            .hero-header-v22
            .hero-data-status-strip,
            .hero-header-v22
            .hero-data-portrait,
            .hero-header-v22
            .hero-life-bar {
                transition:
                    border-color .28s ease,
                    box-shadow .28s ease,
                    filter .28s ease;
            }


            /* ==============================================================
               MODAL / MOVIMENTO REDUZIDO
            ============================================================== */

            .hero-header-v22.hero-hp-modal-open
            .hero-data-portrait,
            .hero-header-v22.hero-hp-modal-open
            .hero-life-bar {
                animation:
                    none !important;
            }

            @media (prefers-reduced-motion: reduce) {
                .hero-header-v22
                .hero-data-portrait,
                .hero-header-v22
                .hero-life-bar,
                .hero-header-v22
                .hero-data-status-strip {
                    animation:
                        none !important;
                }
            }



            /*
            |--------------------------------------------------------------------------
            | HERO HEADER V24 — SUPERFÍCIE CONTÍNUA SEM ALTERAR A ESTRUTURA
            |--------------------------------------------------------------------------
            |
            | O painel CA/Vida permanece integralmente no DOM. Não removemos
            | main-panel, frame, bridge, includes, Alpine, eventos ou modais.
            |
            | Em desktop, Classic/Spellbound e os dois temas escuros deixam de
            | desenhar uma segunda placa sobre o fundo do Header. O gradiente/fundo
            | do hero-header-segment passa a aparecer continuamente por baixo.
            |
            | Em telas menores mantemos o comportamento original do componente.
            |
            */

            @media (min-width: 1280px) {
                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-main-panel {
                    border-color:
                        transparent !important;

                    background:
                        transparent !important;

                    box-shadow:
                        none !important;

                    filter:
                        none !important;
                }

                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-main-frame {
                    border-color:
                        transparent !important;

                    background:
                        transparent !important;

                    box-shadow:
                        none !important;

                    opacity:
                        0 !important;
                }

                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-main-bridge {
                    background:
                        transparent !important;

                    box-shadow:
                        none !important;

                    opacity:
                        0 !important;
                }

                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-armor-zone,
                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-vitals-zone,
                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-life-zone,
                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-quick-stats-slot,
                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-footer {
                    background:
                        transparent !important;

                    box-shadow:
                        none !important;
                }

                /*
                 * A CA pode continuar tendo sua divisão estrutural, mas sem
                 * criar halo ou segunda camada de profundidade.
                 */
                .character-sheet-theme:is(
                    [data-theme="spellbound"],
                    [data-theme="midnight"],
                    [data-theme="obsidian"]
                )
                .hero-header-segment
                .hero-header-armor-divider {
                    box-shadow:
                        none !important;
                }
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
        maxHpReduction: {{ $maxHpReduction }},


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

        exhaustionUsesIdentityCharacter:
            @js(
                (int) $identityCharacter->id
                !==
                (int) $character->id
            ),

        exhaustionLevel:
            {{ $exhaustionLevel }},

        exhaustionOpen: false,
        exhaustionDraft: {{ $exhaustionLevel }},
        savingExhaustion: false,
        exhaustionError: null,

        /*
        |--------------------------------------------------------------------------
        | EXPERIÊNCIA
        |--------------------------------------------------------------------------
        |
        | O nível e o modal de XP precisam compartilhar o mesmo escopo Alpine.
        |
        */

        xpProgressOpen: false,

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

        hpQuickOpen: false,
        hpQuickDamage: null,
        hpQuickHeal: null,
        hpQuickBusy: false,

        editingTempHp: false,
        directTempHp: {{ $temporaryHp }},

        hpSettingsOpen: false,
        hpSettingsStep: 'confirm',

        directMaxHp: {{ $maxHp }},
        directTemporaryMaxHp: {{ $temporaryMaxHp }},
        directMaxHpReduction: {{ $maxHpReduction }},


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
                            $inventoryCharacter,

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

        /*
        |--------------------------------------------------------------------------
        | FEEDBACK DE IMPACTO
        |--------------------------------------------------------------------------
        |
        | Um único estado controla dano, escudo e cura. Os booleanos antigos
        | permanecem apenas como ponte para hero-life/hero-data, mas agora são
        | acionados exclusivamente por playHpImpact().
        |
        */
        hpImpactKind: null,
        hpImpactStrength: 'soft',
        hpImpactTimer: null,

        /*
         * Quantidades por camada visual.
         *
         * damage = atingiu PV normais
         * shield = absorvido por PV temporários
         * bonus  = consumiu a faixa de Vida Máxima Extra
         */
        hpImpactDamageAmount: 0,
        hpImpactShieldAmount: 0,
        hpImpactBonusAmount: 0,

        shakingRed: false,
        shakingBlue: false,
        flashingGreen: false,

        hpFeedbackVisible: false,
        hpFeedbackKind: null,
        hpFeedbackAmount: 0,
        hpFeedbackSecondaryAmount: 0,
        hpFeedbackTimer: null,


        /*
        |--------------------------------------------------------------------------
        | VIDA CALCULADA
        |--------------------------------------------------------------------------
        */

        get potentialMaxHp() {
            return Math.max(
                1,
                this.maxHp +
                this.temporaryMaxHp
            );
        },


        get effectiveMaxHp() {
            return Math.max(
                1,
                this.potentialMaxHp -
                this.maxHpReduction
            );
        },


        get maxHpReductionPercent() {
            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        this.maxHpReduction /
                        this.potentialMaxHp
                    ) * 100
                )
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
                        this.potentialMaxHp
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
                        this.potentialMaxHp
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
                        this.potentialMaxHp
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


        get healthState() {
            if (this.isDead) {
                return 'dead';
            }

            if (this.isDowned) {
                return 'downed';
            }

            if (this.isCritical) {
                return 'critical';
            }

            if (this.isBloodied) {
                return 'bloodied';
            }

            return 'healthy';
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
                    await this.persistCombat(
                        {
                            exhaustion_level:
                                level,
                        },
                        true
                    );

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

        async persistCombat(
            fields,
            useIdentityCharacter = false
        ) {

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

                const targetUrl =
                    useIdentityCharacter
                        ? @js(
                            route(
                                'characters.combat.update',
                                $identityCharacter
                            )
                        )
                        : @js(
                            route(
                                'characters.combat.update',
                                $character
                            )
                        );

                const response =
                    await fetch(
                        targetUrl,
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


            let restOverrides =
                combat.overrides;

            if (
                typeof restOverrides ===
                'string'
            ) {
                try {
                    restOverrides =
                        JSON.parse(
                            restOverrides
                        );
                } catch (error) {
                    restOverrides =
                        null;
                }
            }

            if (
                restOverrides &&
                typeof restOverrides ===
                    'object' &&
                !Array.isArray(
                    restOverrides
                )
            ) {
                this.combatOverrides =
                    restOverrides;

                this.maxHpReduction =
                    Math.max(
                        0,
                        Math.min(
                            this.maxHp +
                            this.temporaryMaxHp -
                            1,
                            parseInt(
                                restOverrides?.hp
                                    ?.max_reduction
                            ) || 0
                        )
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
                !this.exhaustionUsesIdentityCharacter
                &&
                combat.exhaustion_level !== undefined
                &&
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
                    this.playHpImpact(
                        'heal',
                        1,
                        0,
                        'soft'
                    );
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

                this.playHpImpact(
                    'heal',
                    Math.max(
                        1,
                        this.currentHp
                    ),
                    0,
                    'medium'
                );

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

            this.playHpImpact(
                'heal',
                Math.max(
                    1,
                    this.currentHp
                ),
                0,
                'medium'
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

                if (
                    !this.exhaustionUsesIdentityCharacter
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

                this.playHpImpact(
                    'heal',
                    Math.max(
                        1,
                        this.currentHp
                    ),
                    0,
                    'medium'
                );

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
        | TRANSFORMAÇÃO ESPECIAL — EVENTO DE VIDA
        |--------------------------------------------------------------------------
        |
        | O Hero não conhece as transformações.
        | Ele apenas informa quando os PV reais diminuem.
        |--------------------------------------------------------------------------
        */
        dispatchCharacterDamageApplied(
            damage
        ) {
            const parsedDamage =
                Math.max(
                    0,
                    parseInt(
                        damage
                    ) || 0
                );

            if (parsedDamage < 1) {
                return;
            }

            window.dispatchEvent(
                new CustomEvent(
                    'character-damage-applied',
                    {
                        detail: {
                            characterId:
                                {{ (int) $identityCharacter->id }},

                            mechanicalCharacterId:
                                {{ (int) $character->id }},

                            damage:
                                parsedDamage,
                        },
                    }
                )
            );
        },


        dispatchCharacterHpChanged(
            previousHp,
            currentHp
        ) {
            const previous =
                Math.max(
                    0,
                    parseInt(
                        previousHp
                    ) || 0
                );

            const current =
                Math.max(
                    0,
                    parseInt(
                        currentHp
                    ) || 0
                );

            if (
                current >= previous
            ) {
                return;
            }

            window.dispatchEvent(
                new CustomEvent(
                    'character-hp-changed',
                    {
                        detail: {
                            characterId:
                                {{ (int) $identityCharacter->id }},

                            previousHp:
                                previous,

                            currentHp:
                                current,

                            maxHp:
                                this.effectiveMaxHp,
                        },
                    }
                )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | DANO / CURA RÁPIDOS
        |--------------------------------------------------------------------------
        |
        | Esse painel não cria uma segunda regra de PV.
        | Ele apenas converte o valor digitado em changeHp(), preservando:
        |
        | - PV temporários;
        | - death saves;
        | - concentração;
        | - gatilhos de transformação;
        | - animações e persistência existentes.
        |--------------------------------------------------------------------------
        */

        toggleHpQuick() {
            if (this.hpQuickBusy) {
                return;
            }

            if (this.hpQuickOpen) {
                this.closeHpQuick();
                return;
            }

            this.hpQuickDamage =
                null;

            this.hpQuickHeal =
                null;

            this.hpQuickOpen =
                true;

            this.$nextTick(() => {
                this.$refs
                    .hpQuickDamageInput
                    ?.focus();
            });
        },


        closeHpQuick() {
            if (this.hpQuickBusy) {
                return;
            }

            this.hpQuickOpen =
                false;

            this.hpQuickDamage =
                null;

            this.hpQuickHeal =
                null;
        },


        async applyHpQuick(kind) {
            if (this.hpQuickBusy) {
                return;
            }

            const rawValue =
                kind === 'damage'
                    ? this.hpQuickDamage
                    : this.hpQuickHeal;

            const amount =
                Math.max(
                    0,
                    parseInt(
                        rawValue
                    ) || 0
                );

            if (amount < 1) {
                return;
            }

            this.hpQuickBusy =
                true;

            try {
                await this.changeHp(
                    kind === 'damage'
                        ? -amount
                        : amount
                );

                this.hpQuickOpen =
                    false;

                this.hpQuickDamage =
                    null;

                this.hpQuickHeal =
                    null;

            } finally {
                this.hpQuickBusy =
                    false;
            }
        },


        /*
        |--------------------------------------------------------------------------
        | IMPACTO VISUAL CENTRALIZADO
        |--------------------------------------------------------------------------
        |
        | kind:
        | - damage = dano que atingiu PV normais
        | - shield = dano totalmente absorvido por PV temporários
        | - bonus  = dano contido na faixa de Vida Máxima Extra
        | - mixed  = duas ou mais camadas foram atingidas
        | - heal   = cura / recuperação
        |
        | strength:
        | - soft   = menos de 8% do máximo efetivo
        | - medium = de 8% até menos de 20%
        | - heavy  = 20% ou mais
        |
        | A intensidade é apenas visual. Nenhuma regra mecânica depende dela.
        |--------------------------------------------------------------------------
        */

        resolveHpImpactStrength(
            amount,
            forcedStrength = null
        ) {
            if (
                ['soft', 'medium', 'heavy']
                    .includes(
                        forcedStrength
                    )
            ) {
                return forcedStrength;
            }

            const normalizedAmount =
                Math.max(
                    0,
                    parseInt(
                        amount
                    ) || 0
                );

            const reference =
                Math.max(
                    1,
                    this.effectiveMaxHp
                );

            const ratio =
                normalizedAmount /
                reference;

            if (ratio >= .20) {
                return 'heavy';
            }

            if (ratio >= .08) {
                return 'medium';
            }

            return 'soft';
        },


        clearHpImpact() {
            if (this.hpImpactTimer) {
                window.clearTimeout(
                    this.hpImpactTimer
                );

                this.hpImpactTimer =
                    null;
            }

            this.hpImpactKind =
                null;

            this.hpImpactStrength =
                'soft';

            this.hpImpactDamageAmount =
                0;

            this.hpImpactShieldAmount =
                0;

            this.hpImpactBonusAmount =
                0;

            this.shakingRed =
                false;

            this.shakingBlue =
                false;

            this.flashingGreen =
                false;
        },


        playHpImpact(
            kind,
            amount = 0,
            secondaryAmount = 0,
            forcedStrength = null,
            bonusAmount = 0
        ) {
            const normalizedAmount =
                Math.max(
                    0,
                    parseInt(
                        amount
                    ) || 0
                );

            const normalizedSecondary =
                Math.max(
                    0,
                    parseInt(
                        secondaryAmount
                    ) || 0
                );

            const normalizedBonus =
                Math.max(
                    0,
                    parseInt(
                        bonusAmount
                    ) || 0
                );

            const damageAmount =
                (
                    kind === 'damage'
                    ||
                    kind === 'mixed'
                )
                    ? normalizedAmount
                    : 0;

            const shieldAmount =
                kind === 'shield'
                    ? normalizedAmount
                    : normalizedSecondary;

            const effectiveBonusAmount =
                kind === 'bonus'
                    ? Math.max(
                        normalizedAmount,
                        normalizedBonus
                    )
                    : normalizedBonus;

            if (
                normalizedAmount < 1
                &&
                normalizedSecondary < 1
                &&
                normalizedBonus < 1
                &&
                kind !== 'heal'
            ) {
                return;
            }

            if (this.hpImpactTimer) {
                window.clearTimeout(
                    this.hpImpactTimer
                );

                this.hpImpactTimer =
                    null;
            }

            /*
             * Zera um frame antes para reiniciar corretamente a animação
             * quando vários golpes chegam em sequência.
             */
            this.hpImpactKind =
                null;

            this.hpImpactDamageAmount =
                0;

            this.hpImpactShieldAmount =
                0;

            this.hpImpactBonusAmount =
                0;

            this.shakingRed =
                false;

            this.shakingBlue =
                false;

            this.flashingGreen =
                false;

            this.$nextTick(() => {
                this.hpImpactKind =
                    kind;

                this.hpImpactDamageAmount =
                    damageAmount;

                this.hpImpactShieldAmount =
                    shieldAmount;

                this.hpImpactBonusAmount =
                    effectiveBonusAmount;

                this.hpImpactStrength =
                    this.resolveHpImpactStrength(
                        Math.max(
                            normalizedAmount,
                            normalizedSecondary,
                            normalizedBonus
                        ),
                        forcedStrength
                    );

                this.shakingRed =
                    damageAmount > 0;

                this.shakingBlue =
                    shieldAmount > 0;

                this.flashingGreen =
                    kind === 'heal';

                const duration =
                    this.hpImpactStrength === 'heavy'
                        ? 820
                        : (
                            this.hpImpactStrength === 'medium'
                                ? 640
                                : 480
                        );

                this.hpImpactTimer =
                    window.setTimeout(
                        () => {
                            this.clearHpImpact();
                        },
                        duration
                    );
            });
        },


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK VISUAL DE VIDA
        |--------------------------------------------------------------------------
        |
        | kind:
        | - damage = dano que realmente atingiu PV
        | - shield = dano totalmente absorvido por PV temporários
        | - heal   = cura efetivamente recebida
        |
        | secondaryAmount é usado para informar quanto do mesmo golpe foi
        | absorvido pelos PV temporários quando também houve dano real.
        |--------------------------------------------------------------------------
        */

        playHpFeedback(
            kind,
            amount,
            secondaryAmount = 0
        ) {
            const normalizedAmount =
                Math.max(
                    0,
                    parseInt(
                        amount
                    ) || 0
                );

            const normalizedSecondary =
                Math.max(
                    0,
                    parseInt(
                        secondaryAmount
                    ) || 0
                );

            if (
                normalizedAmount < 1
                &&
                normalizedSecondary < 1
            ) {
                return;
            }

            if (this.hpFeedbackTimer) {
                window.clearTimeout(
                    this.hpFeedbackTimer
                );

                this.hpFeedbackTimer =
                    null;
            }

            /*
             * Esconde antes de mostrar novamente para reiniciar a animação
             * mesmo quando o usuário aperta +1/-1 em sequência rápida.
             */
            this.hpFeedbackVisible =
                false;

            this.$nextTick(() => {
                this.hpFeedbackKind =
                    kind;

                this.hpFeedbackAmount =
                    normalizedAmount;

                this.hpFeedbackSecondaryAmount =
                    normalizedSecondary;

                this.hpFeedbackVisible =
                    true;

                this.hpFeedbackTimer =
                    window.setTimeout(
                        () => {
                            this.hpFeedbackVisible =
                                false;

                            this.hpFeedbackTimer =
                                null;
                        },
                        1150
                    );
            });
        },


        /*
        |--------------------------------------------------------------------------
        | VIDA
        |--------------------------------------------------------------------------
        */

        async changeHp(amount) {

            if (amount < 0) {

                const previousHp =
                    this.currentHp;

                const receivedDamage =
                    Math.abs(amount);

                let damage =
                    receivedDamage;

                let absorbedByTemporaryHp =
                    0;

                /*
                 * Quantidade de PV que, antes do golpe, estava acima da
                 * Vida Máxima base. Essa é a faixa dourada.
                 */
                const bonusHpBeforeDamage =
                    Math.max(
                        0,
                        previousHp -
                        this.maxHp
                    );

                let damageToBonusHp =
                    0;

                let damageToBaseHp =
                    0;

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

                    absorbedByTemporaryHp =
                        absorbed;

                    this.temporaryHp -= absorbed;
                    damage -= absorbed;

                    this.directTempHp =
                        this.temporaryHp;

                }


                if (damage > 0) {

                    /*
                     * O dano real atravessa primeiro a faixa dourada de Vida
                     * Máxima Extra e só depois alcança os PV normais.
                     */
                    damageToBonusHp =
                        Math.min(
                            bonusHpBeforeDamage,
                            damage
                        );

                    damageToBaseHp =
                        Math.max(
                            0,
                            damage -
                            damageToBonusHp
                        );

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


                /*
                 * Impacto visual por camada:
                 *
                 * - shield: PV temporários absorveram tudo;
                 * - bonus: o golpe ficou inteiramente na faixa dourada;
                 * - damage: atingiu diretamente os PV normais;
                 * - mixed: atravessou mais de uma camada.
                 */
                if (damageToBaseHp > 0) {
                    this.playHpImpact(
                        (
                            absorbedByTemporaryHp > 0
                            ||
                            damageToBonusHp > 0
                        )
                            ? 'mixed'
                            : 'damage',
                        damageToBaseHp,
                        absorbedByTemporaryHp,
                        null,
                        damageToBonusHp
                    );
                } else if (damageToBonusHp > 0) {
                    this.playHpImpact(
                        'bonus',
                        damageToBonusHp,
                        absorbedByTemporaryHp,
                        null,
                        damageToBonusHp
                    );
                } else if (
                    absorbedByTemporaryHp > 0
                ) {
                    this.playHpImpact(
                        'shield',
                        absorbedByTemporaryHp
                    );
                }



                /*
                 * Feedback do golpe:
                 *
                 * - se algo atravessou o escudo, o valor principal é o dano
                 *   real e o absorvido aparece como informação secundária;
                 * - se tudo foi absorvido, o feedback é azul.
                 */
                if (damage > 0) {
                    this.playHpFeedback(
                        'damage',
                        damage,
                        absorbedByTemporaryHp
                    );
                } else if (
                    absorbedByTemporaryHp > 0
                ) {
                    this.playHpFeedback(
                        'shield',
                        absorbedByTemporaryHp
                    );
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


                const data =
                    await this.persistCombat({
                        current_hp:
                            this.currentHp,

                        temporary_hp:
                            this.temporaryHp,

                        death_save_successes:
                            this.deathSaveSuccesses,

                        death_save_failures:
                            this.deathSaveFailures,
                    });

                if (data) {
                    this.dispatchCharacterHpChanged(
                        previousHp,
                        this.currentHp
                    );

                    this.dispatchCharacterDamageApplied(
                        receivedDamage
                    );
                }


                return;
            }


            if (amount > 0) {

                const previousHp =
                    this.currentHp;

                this.currentHp =
                    Math.min(
                        this.effectiveMaxHp,
                        this.currentHp +
                        amount
                    );

                const healedAmount =
                    Math.max(
                        0,
                        this.currentHp -
                        previousHp
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


                if (healedAmount > 0) {
                    this.playHpImpact(
                        'heal',
                        healedAmount
                    );

                    this.playHpFeedback(
                        'heal',
                        healedAmount
                    );
                }


                await this.persistCombat({
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


            const previousHp =
                this.currentHp;

            const wasDowned =
                this.isDowned;


            const hpDifference =
                targetHp -
                this.currentHp;

            if (hpDifference < 0) {

                const directDamage =
                    Math.abs(
                        hpDifference
                    );

                this.playHpImpact(
                    'damage',
                    directDamage
                );

                this.playHpFeedback(
                    'damage',
                    directDamage
                );

            } else {

                this.playHpImpact(
                    'heal',
                    hpDifference
                );

                this.playHpFeedback(
                    'heal',
                    hpDifference
                );
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


            const data =
                await this.persistCombat({
                    current_hp:
                        this.currentHp,

                    death_save_successes:
                        this.deathSaveSuccesses,

                    death_save_failures:
                        this.deathSaveFailures
                });

            if (data) {
                this.dispatchCharacterHpChanged(
                    previousHp,
                    this.currentHp
                );

                if (
                    this.currentHp < previousHp
                ) {
                    this.dispatchCharacterDamageApplied(
                        previousHp
                        - this.currentHp
                    );
                }
            }
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


            const previousTemporaryHp =
                this.temporaryHp;


            this.temporaryHp =
                Math.max(
                    0,
                    value
                );


            await this.persistCombat({
                temporary_hp:
                    this.temporaryHp
            });


            if (
                this.temporaryHp !==
                previousTemporaryHp
            ) {
                this.playHpImpact(
                    'shield',
                    Math.max(
                        1,
                        Math.abs(
                            this.temporaryHp -
                            previousTemporaryHp
                        )
                    ),
                    0,
                    'soft'
                );
            }
        },


        /*
        |--------------------------------------------------------------------------
        | CONFIGURAÇÃO DE VIDA
        |--------------------------------------------------------------------------
        */

        openHpSettings() {

            this.hpQuickOpen =
                false;

            this.hpQuickDamage =
                null;

            this.hpQuickHeal =
                null;

            this.directMaxHp =
                this.maxHp;

            this.directTemporaryMaxHp =
                this.temporaryMaxHp;

            this.directMaxHpReduction =
                this.maxHpReduction;

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

            this.directMaxHpReduction =
                this.maxHpReduction;
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

            let newMaxHpReduction =
                parseInt(
                    this.directMaxHpReduction
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


            if (
                Number.isNaN(
                    newMaxHpReduction
                )
            ) {

                newMaxHpReduction =
                    this.maxHpReduction;
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


            const newPotentialMaxHp =
                Math.max(
                    1,
                    newMaxHp +
                    newTemporaryMaxHp
                );


            newMaxHpReduction =
                Math.max(
                    0,
                    Math.min(
                        newPotentialMaxHp - 1,
                        newMaxHpReduction
                    )
                );


            const extraHpDifference =
                newTemporaryMaxHp -
                this.temporaryMaxHp;


            this.maxHp =
                newMaxHp;


            this.temporaryMaxHp =
                newTemporaryMaxHp;

            this.maxHpReduction =
                newMaxHpReduction;


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
                    this.currentHp,

                overrides: {
                    hp: {
                        ...(
                            this.combatOverrides?.hp
                            || {}
                        ),

                        max_reduction:
                            this.maxHpReduction
                    }
                }
            });


            if (
                extraHpDifference > 0
            ) {
                this.playHpImpact(
                    'heal',
                    extraHpDifference,
                    0,
                    'soft'
                );
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
    :data-health-state="healthState"
    :data-impact-kind="hpImpactKind || 'none'"
    :data-impact-strength="hpImpactStrength"
    :data-impact-damage="
        hpImpactDamageAmount > 0
            ? 'true'
            : 'false'
    "
    :data-impact-shield="
        hpImpactShieldAmount > 0
            ? 'true'
            : 'false'
    "
    :data-impact-bonus="
        hpImpactBonusAmount > 0
            ? 'true'
            : 'false'
    "

    :class="{
        'hero-hp-modal-open': hpSettingsOpen
    }"

    class="
        hero-header-segment
        hero-header-v18
        hero-header-v21
        hero-header-v22
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

    <div class="hero-header-content px-4 py-3 sm:px-5 sm:py-4">

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
                    hero-header-identity-zone
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
                        'character' => $identityCharacter,
                        'mechanicalCharacter' => $character,
                        'customizationClasses' => $identityClasses,
                        'hideZeroExhaustion' => $hideZeroExhaustion,
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
                    hero-header-main-panel
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
                        hero-header-main-frame
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
                        hero-header-main-bridge
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
                        hero-header-armor-zone
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
                            hero-header-armor-divider
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
                        hero-header-vitals-zone
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

                        <div class="hero-header-life-zone relative w-full pt-2.5">

                            {{-- HIT DICE --}}

                            <div
                                class="
                                    hero-header-hit-dice-slot
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
                                :style="`
                                    --hero-max-hp-reduction:${maxHpReductionPercent}%;
                                `"
                                :data-hp-reduction="maxHpReduction"
                                :data-hp-reduction-active="
                                    maxHpReduction > 0
                                        ? 'true'
                                        : 'false'
                                "
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
                                hero-header-quick-stats-slot
                                mt-auto
                                pt-1.5
                            "
                        >

                            @include(
                                'characters.components.hero-quick-stats',
                                [
                                    'character' =>
                                        $inventoryCharacter,

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
                                hero-header-footer
                                mt-1
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
                                    hero-header-rest-slot
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
                                        hero-header-rest-button
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

                                        x-transition:enter="character-theme-modal-panel-enter"
                                        x-transition:enter-start="character-theme-modal-panel-enter-start"
                                        x-transition:enter-end="character-theme-modal-panel-enter-end"
                                        x-transition:leave="character-theme-modal-panel-leave"
                                        x-transition:leave-start="character-theme-modal-panel-leave-start"
                                        x-transition:leave-end="character-theme-modal-panel-leave-end"

                                        @click.stop
                                        @click.outside="
                                            restOpen = false
                                        "

                                        :style="
                                            restMenuStyle
                                        "

                                        data-theme="{{ $characterTheme }}"

                                        role="menu"
                                        aria-label="Opções de descanso"

                                        class="
                                            character-sheet-theme
                                            hero-rest-popover

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
                                            hero-rest-popover-header
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
                                            hero-rest-popover-body
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
                                                hero-rest-option
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
                                                hero-rest-option
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
            relative
            z-50

            mx-auto
            w-full
            max-w-[40rem]
        "
    >

        <div
            class="
                death-drawer-active
                hero-death-drawer-panel

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
                    hero-death-drawer-band
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


                    {{-- ============================================================
                         D20 — SALVAMENTO CONTRA A MORTE
                         Usa o componente central de dados do Spellbound.
                    ============================================================= --}}

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

                            {{-- Órbita discreta durante o suspense --}}
                            <span
                                x-show="
                                    deathRolling
                                "

                                x-cloak

                                class="
                                    death-roll-orbit

                                    pointer-events-none
                                    absolute
                                    inset-[2px]

                                    rounded-full
                                    border
                                    border-dashed
                                    border-[#8c6239]/35
                                "
                            ></span>


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
                                    h-[108px]
                                    w-[108px]
                                    items-center
                                    justify-center

                                    rounded-full
                                    bg-transparent

                                    outline-none

                                    transition
                                    duration-200

                                    hover:scale-[1.035]

                                    focus-visible:ring-2
                                    focus-visible:ring-[#7f261d]/30
                                    focus-visible:ring-offset-2

                                    disabled:cursor-wait
                                "
                            >

                                {{-- Halo de fundo --}}
                                <span
                                    class="
                                        pointer-events-none
                                        absolute
                                        inset-[5px]

                                        rounded-full
                                        border
                                        border-[#cda979]/30

                                        bg-[#eadbc8]/20

                                        transition
                                        duration-200

                                        group-hover:border-[#a0523d]/35
                                        group-hover:bg-[#eadbc8]/30
                                    "
                                ></span>


                                {{-- D20 oficial --}}
                                <div
                                    class="
                                        relative
                                        z-10

                                        flex
                                        items-center
                                        justify-center

                                        transition
                                        duration-200
                                    "

                                    :class="{
                                        'scale-[1.045]':
                                            deathRolling,

                                        'death-d20-breathe':
                                            !deathRolling
                                    }"
                                >
                                    <x-dice.polyhedral
                                        :sides="20"
                                        :size="96"
                                    >
                                        <span
                                            class="
                                                tabular-nums
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
                                    </x-dice.polyhedral>
                                </div>

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
            relative
            z-50

            mx-auto
            mt-2
            w-fit
        "
    >

        <button
            type="button"

            @click="
                openDeathDrawer()
            "

            class="
                hero-death-tab-button
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

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            @keydown.escape.window="closeHpSettings()"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Configurar pontos de vida"

            class="
                character-sheet-theme
                character-theme-modal-layer
                hero-hp-settings-layer

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
                @click="closeHpSettings()"
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
            ></div>

            {{-- PAINEL --}}
            <section
                x-show="hpSettingsOpen"

                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                @click.stop
                class="
                    character-theme-modal
                    hero-hp-settings-modal

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
                                    hero-hp-limit-grid
                                    grid
                                    grid-cols-1
                                    gap-3

                                    sm:grid-cols-3
                                "
                            >
                                {{-- VIDA MÁXIMA --}}
                                <label
                                    class="
                                        hero-hp-limit-card
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
                                            hero-hp-limit-title
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
                                            hero-hp-limit-copy
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
                                            hero-hp-limit-input
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
                                        hero-hp-limit-card
                                        hero-hp-extra-card
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
                                            hero-hp-limit-title
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
                                            hero-hp-limit-copy
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
                                            hero-hp-limit-input
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

                                {{-- VIDA MÁXIMA REDUZIDA --}}
                                <label
                                    class="
                                        hero-hp-limit-card
                                        hero-hp-reduction-card
                                        block

                                        rounded-xl
                                        border

                                        p-3.5
                                    "
                                >
                                    <span
                                        class="
                                            hero-hp-limit-title
                                            hero-hp-reduction-label
                                            block
                                            text-[11px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]
                                        "
                                    >
                                        Vida Máxima Reduzida
                                    </span>

                                    <span
                                        class="
                                            hero-hp-limit-copy
                                            mt-1
                                            block
                                            text-[11px]
                                            leading-4
                                            text-violet-800/75
                                        "
                                    >
                                        Reduz o limite sem alterar a Vida Máxima base.
                                    </span>

                                    <input
                                        type="number"
                                        min="0"
                                        :max="
                                            Math.max(
                                                0,
                                                (
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
                                                - 1
                                            )
                                        "
                                        x-model.number="directMaxHpReduction"
                                        class="
                                            hero-hp-limit-input
                                            mt-3
                                            h-12
                                            w-full

                                            rounded-lg
                                            border

                                            bg-white

                                            px-3

                                            font-serif
                                            text-[20px]
                                            font-black

                                            outline-none
                                        "
                                    >
                                </label>
                            </div>

                            {{-- PREVIEW --}}
                            <div
                                class="
                                    hero-hp-preview-card
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
                                            Base + extra − redução
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
                                                    -
                                                    Math.min(
                                                        Math.max(
                                                            0,
                                                            parseInt(
                                                                directMaxHpReduction
                                                            ) || 0
                                                        ),
                                                        Math.max(
                                                            0,
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
                                                            ) - 1
                                                        )
                                                    )
                                                )
                                            "
                                        ></span>
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

                                    <div
                                        class="
                                            hero-hp-reduction-preview
                                            absolute
                                            inset-y-0
                                            right-0
                                        "
                                        :style="`
                                            width:${
                                                (
                                                    Math.min(
                                                        Math.max(
                                                            0,
                                                            parseInt(
                                                                directMaxHpReduction
                                                            ) || 0
                                                        ),
                                                        Math.max(
                                                            0,
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
                                                            ) - 1
                                                        )
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
                                        hero-hp-limit-summary
                                        mt-3

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

                                    <span>
                                        Reduzida:
                                        <strong
                                            class="
                                                hero-hp-reduction-value
                                                font-black
                                            "
                                        >
                                            −<span
                                                x-text="
                                                    Math.min(
                                                        Math.max(
                                                            0,
                                                            parseInt(
                                                                directMaxHpReduction
                                                            ) || 0
                                                        ),
                                                        Math.max(
                                                            0,
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
                                                            ) - 1
                                                        )
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