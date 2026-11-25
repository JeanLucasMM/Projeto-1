@props([
    'character',
    'identityCharacter' => null,
    'inventoryCharacter' => null,
    'imageOverrideUrl' => null,
])

{{-- Módulo visual exclusivo do d20 contra a morte; nunca define física. --}}
@include('characters.components.dice.death-save-effects')

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


@include('components.ui.poly-die-styles')

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

            /* A ficha continua legível e clicável; cinza, sem escurecimento excessivo. */
            .character-sheet-page.character-sheet-dead {
                filter:grayscale(.85) saturate(.62) brightness(.94);
            }
            .character-sheet-page.character-sheet-dead .hero-header-segment {
                border-color:rgba(102,96,94,.57)!important;
            }
            .character-sheet-page.character-sheet-dead a,
            .character-sheet-page.character-sheet-dead button {pointer-events:auto}


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


            /* V66.2.173 — destino: d20 cinemático e morte por salvamento. */
            .death-save-action-grid{gap:12px!important}
            .death-save-action{min-width:0;isolation:isolate}
            .death-save-prompt{min-height:15px;max-width:158px;text-align:center;font:800 9px/1.35 system-ui,sans-serif;letter-spacing:.055em;text-transform:uppercase;color:var(--theme-text-secondary,#805b4b)}
            .death-save-die-stage{position:relative;width:156px;height:168px;display:grid;place-items:center;isolation:isolate;overflow:visible}
            .death-save-ambient{position:absolute;inset:8px 0 6px;border-radius:50%;pointer-events:none;background:radial-gradient(circle,rgba(166,131,91,.18),rgba(111,68,52,.055) 49%,transparent 73%);animation:death-save-idle-glow 2.9s ease-in-out infinite}
            .death-save-die-button{position:relative;z-index:2;width:135px;height:135px;display:grid;place-items:center;border:0!important;padding:0;background:transparent!important;cursor:grab;touch-action:none;user-select:none;outline-offset:2px;transform:translate3d(var(--death-grab-x,0px),var(--death-grab-y,0px),0) rotate(var(--death-grab-angle,0deg));transition:transform .26s cubic-bezier(.22,.83,.23,1)}
            .death-save-die-button:hover:not(:disabled){filter:brightness(1.07)}
            .death-save-die-button:focus-visible{outline:2px solid var(--theme-accent,#946c4c);border-radius:24px}
            .death-save-die-stage.is-dragged .death-save-die-button{cursor:grabbing;transition:none!important}
            .death-save-die-button:disabled{cursor:default}
            .death-save-die-art{display:grid;place-items:center;filter:drop-shadow(0 10px 8px rgba(40,22,20,.26));transition:filter .3s ease;transform-origin:50% 52%}
            .death-save-die-art .sb-poly-die{pointer-events:none;--sb-die-base:color-mix(in srgb,var(--theme-accent-strong,#813d35) 75%,#72463e 25%)}
            .death-save-die-art .sb-poly-number{z-index:1;font-weight:850;text-shadow:0 2px 2px rgba(0,0,0,.24);animation:none!important;rotate:none!important}
            .death-save-die-art .sb-poly-face{animation:none!important}
            .death-save-die-shadow{position:absolute;z-index:0;bottom:12px;left:calc(50% - 37px);width:74px;height:16px;border-radius:50%;pointer-events:none;background:radial-gradient(ellipse,rgba(38,23,18,.37),rgba(38,23,18,.02) 72%);filter:blur(5px);transition:opacity .2s ease,transform .3s ease}
            .death-save-die-stage.is-dragged .death-save-die-shadow{opacity:.42;transform:scale(.75)}
            .death-save-die-stage.is-rolling .death-save-ambient{animation:death-save-charged-glow 1.45s ease-in-out infinite}
            .death-save-die-stage.is-rolling .death-save-die-button{transition:none!important;filter:none!important}
            .death-save-die-stage.is-rolling .death-save-die-shadow{animation:death-save-shadow-roll 3.9s ease-out both}
            .death-save-die-stage.is-rolling .death-save-die-art .sb-poly-face{transform-origin:50% 50%;animation:death-save-face-spin 3.9s cubic-bezier(.15,.62,.14,1) both!important}
            .death-save-motion-a{animation:death-save-fall-a 3.9s cubic-bezier(.15,.59,.18,1) both!important}
            .death-save-motion-b{animation:death-save-fall-b 3.9s cubic-bezier(.15,.59,.18,1) both!important}
            .death-save-motion-c{animation:death-save-fall-c 3.9s cubic-bezier(.15,.59,.18,1) both!important}
            .death-save-motion-d{animation:death-save-fall-d 3.9s cubic-bezier(.15,.59,.18,1) both!important}
            @keyframes death-save-face-spin{0%{transform:rotate(0deg) scale(.92)}19%{transform:rotate(580deg) scale(1.06)}40%{transform:rotate(980deg) scale(1)}59%{transform:rotate(1360deg) scale(.99)}76%{transform:rotate(1650deg) scale(.99)}89%{transform:rotate(1770deg) scale(1)}100%{transform:rotate(1800deg) scale(1)}}
            @keyframes death-save-fall-a{0%{transform:translate3d(var(--death-start-x,0px),var(--death-start-y,0px),0) scale(.80)}18%{transform:translate3d(24px,0px,0) scale(1.07)}31%{transform:translate3d(45px,-36px,0) scale(.98)}44%{transform:translate3d(-14px,2px,0) scale(1.03)}58%{transform:translate3d(-31px,-18px,0) scale(.99)}72%{transform:translate3d(15px,1px,0) scale(1.01)}87%{transform:translate3d(9px,-5px,0) scale(1)}100%{transform:translate3d(0,0,0) scale(1)}}
            @keyframes death-save-fall-b{0%{transform:translate3d(var(--death-start-x,0px),var(--death-start-y,0px),0) scale(.76)}19%{transform:translate3d(-34px,1px,0) scale(1.07)}34%{transform:translate3d(-42px,-34px,0) scale(.98)}47%{transform:translate3d(19px,1px,0) scale(1.03)}60%{transform:translate3d(31px,-15px,0) scale(.99)}75%{transform:translate3d(-12px,0,0) scale(1.01)}89%{transform:translate3d(-7px,-4px,0) scale(1)}100%{transform:translate3d(0,0,0) scale(1)}}
            @keyframes death-save-fall-c{0%{transform:translate3d(var(--death-start-x,0px),var(--death-start-y,0px),0) scale(.79)}20%{transform:translate3d(-38px,0,0) scale(1.07)}32%{transform:translate3d(48px,-40px,0) scale(.98)}47%{transform:translate3d(-17px,0,0) scale(1.03)}60%{transform:translate3d(19px,-16px,0) scale(.99)}73%{transform:translate3d(-11px,0,0) scale(1.01)}90%{transform:translate3d(5px,-4px,0) scale(1)}100%{transform:translate3d(0,0,0) scale(1)}}
            @keyframes death-save-fall-d{0%{transform:translate3d(var(--death-start-x,0px),var(--death-start-y,0px),0) scale(.82)}21%{transform:translate3d(39px,1px,0) scale(1.07)}34%{transform:translate3d(-43px,-35px,0) scale(.98)}48%{transform:translate3d(22px,0,0) scale(1.03)}62%{transform:translate3d(-21px,-19px,0) scale(.99)}75%{transform:translate3d(12px,0,0) scale(1.01)}89%{transform:translate3d(-6px,-4px,0) scale(1)}100%{transform:translate3d(0,0,0) scale(1)}}
            @keyframes death-save-shadow-roll{0%{transform:scale(.38);opacity:.20}18%,44%,72%{transform:scale(1.13);opacity:.7}31%,58%,86%{transform:scale(.73);opacity:.34}100%{transform:scale(1);opacity:.58}}
            @keyframes death-save-idle-glow{0%,100%{opacity:.4;transform:scale(.95)}50%{opacity:.85;transform:scale(1.08)}}
            @keyframes death-save-charged-glow{0%,100%{opacity:.4;transform:scale(1)}50%{opacity:.92;transform:scale(1.22)}}
            .death-save-die-stage.is-settled::before,.death-save-die-stage.is-settled::after{content:'';position:absolute;inset:8px;border-radius:50%;pointer-events:none;z-index:1;opacity:0}
            .death-save-die-stage.is-natural-20::before{background:radial-gradient(circle,rgba(255,233,152,.55),rgba(244,188,61,.27) 38%,transparent 72%);animation:death-save-aura-gold 3.8s ease-in-out both}
            .death-save-die-stage.is-natural-20::after{background:radial-gradient(circle at 21% 35%,#fff5bf 0 2px,transparent 3px),radial-gradient(circle at 81% 27%,#ffe19b 0 2px,transparent 3px),radial-gradient(circle at 86% 78%,#ffe8aa 0 1px,transparent 3px),radial-gradient(circle at 26% 82%,#ffd17a 0 1.5px,transparent 3px);animation:death-save-aura-gold 3.8s ease-in-out both}
            .death-save-die-stage.is-natural-1::before{background:radial-gradient(circle,rgba(255,157,157,.38),rgba(181,38,49,.25) 42%,transparent 73%);animation:death-save-aura-red 3.2s ease-in-out both}
            .death-save-die-stage.is-natural-1::after{background:radial-gradient(circle at 16% 38%,#ff9c8c 0 2px,transparent 3px),radial-gradient(circle at 79% 25%,#f36d69 0 2px,transparent 3px),radial-gradient(circle at 85% 83%,#cb4646 0 1.5px,transparent 3px);animation:death-save-aura-red 3.2s ease-in-out both}
            @keyframes death-save-aura-gold{0%{opacity:0;transform:scale(.7)}18%{opacity:.86;transform:scale(1.06)}38%{opacity:.3;transform:scale(1.2)}60%{opacity:.9;transform:scale(1.15)}78%{opacity:.4;transform:scale(1.26)}100%{opacity:0;transform:scale(1.42)}}
            @keyframes death-save-aura-red{0%{opacity:0;transform:scale(.7)}20%{opacity:.88;transform:scale(1.05)}45%{opacity:.32;transform:scale(1.20)}69%{opacity:.83;transform:scale(1.18)}100%{opacity:0;transform:scale(1.38)}}
            .death-save-caption{min-height:36px;max-width:190px;text-align:center;color:var(--theme-text-secondary,#775746);font:750 10px/1.3 system-ui,sans-serif}
            .death-save-caption strong{display:block;font:900 10px/1.45 system-ui,sans-serif;color:var(--theme-text-strong,#51312b)}
            .death-save-caption strong.is-gold{color:#b88024;text-shadow:0 1px 7px rgba(245,197,78,.22)}
            .death-save-caption strong.is-red{color:#c4544a}
            .death-save-error{max-width:182px;margin:4px auto;text-align:center;font:700 10px/1.45 system-ui,sans-serif;color:var(--theme-state-damage,#be4b45)}
            .death-save-retry{padding:6px 12px;border:1px solid color-mix(in srgb,var(--theme-accent,#986c53) 45%,transparent);border-radius:999px;background:var(--theme-surface-card,#fff8eb);color:var(--theme-text-strong,#5a3831);font:800 10px/1.3 system-ui,sans-serif;cursor:pointer;transition:background .2s}
            .death-save-retry:hover{background:var(--theme-card-hover,#eee2d3)}
            .death-save-last-breath::before{content:'';position:absolute;inset:0;pointer-events:none;z-index:0;background:radial-gradient(circle,transparent 12%,rgba(69,8,15,.48) 85%),linear-gradient(135deg,rgba(129,22,25,.3),transparent 52%);animation:death-save-vignette 3.4s ease-out both}
            .death-save-last-breath .death-card-arrive{animation:death-save-final-card 1.25s cubic-bezier(.16,.8,.2,1) both!important}
            @keyframes death-save-vignette{0%{opacity:0}23%{opacity:.9}42%{opacity:.35}66%{opacity:1}100%{opacity:.76}}
            @keyframes death-save-final-card{0%{opacity:0;transform:translateY(38px) scale(.9);filter:blur(3px) grayscale(0)}45%{opacity:1;transform:translateY(-4px) scale(1.015);filter:blur(0) grayscale(.5)}100%{opacity:1;transform:translateY(0) scale(1);filter:blur(0) grayscale(1)}}
            @media(max-width:650px){.death-save-action-grid{grid-template-columns:minmax(0,1fr)!important;gap:14px!important}.death-save-action{order:-1}.death-save-die-stage{height:148px}.death-save-die-art .sb-poly-die{scale:.87}.death-save-action-grid>div{width:100%}}
            @media(prefers-reduced-motion:reduce){.death-save-die-stage,.death-save-die-button,.death-save-die-art .sb-poly-face,.death-save-ambient,.death-save-die-shadow,.death-save-die-stage::before,.death-save-die-stage::after,.death-save-last-breath::before,.death-save-last-breath .death-card-arrive{animation:none!important;transition:none!important}}
            /* V66.2.175 — faixa de salvamentos: sem modal, sem backdrop, ficha sempre utilizável. */
            .sb-death-strip { --sb-death-red:#b75050; --sb-death-green:#248268; }
            .sb-death-strip-inner{display:flex;flex-direction:column;gap:6px;padding:10px 13px 9px;border-radius:15px;border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 70%,var(--sb-death-red) 30%);background:linear-gradient(120deg,color-mix(in srgb,var(--theme-surface-card) 95%,var(--sb-death-red) 5%),var(--theme-surface-card));box-shadow:0 7px 22px rgba(45,19,17,.13),inset 0 1px 0 rgba(255,255,255,.14);color:var(--theme-text-primary)}
            .sb-death-strip-heading{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:1px 1px 3px;border-bottom:1px solid color-mix(in srgb,var(--theme-border-soft) 80%,transparent)}
            .sb-death-strip-heading strong{display:block;font:900 15px/1.14 Georgia,serif;color:var(--theme-text-strong)}
            .sb-death-strip-overline{display:block;margin-bottom:3px;font:850 8px/1 system-ui,sans-serif;letter-spacing:.17em;text-transform:uppercase;color:var(--sb-death-red)}
            .sb-death-strip-subtitle{margin-top:3px;font:650 9px/1.2 system-ui,sans-serif;color:var(--theme-text-secondary)}
            .sb-death-strip-collapse{width:25px;height:25px;border-radius:8px;display:grid;place-items:center;color:var(--theme-text-secondary);border:1px solid var(--theme-border-soft);background:var(--theme-surface-raised);cursor:pointer}
            .sb-death-strip-collapse:hover{background:var(--theme-card-hover);color:var(--theme-text-strong)}
            .sb-death-strip-action{display:flex;align-items:center;justify-content:center;gap:13px;min-height:73px}
            .sb-death-track{flex:1 1 130px;min-width:0;max-width:165px;padding:7px 9px 9px;border:1px solid color-mix(in srgb,var(--theme-border-soft) 84%,var(--sb-death-green) 16%);border-radius:11px;background:color-mix(in srgb,var(--theme-surface-raised) 93%,var(--sb-death-green) 7%)}
            .sb-death-track.is-failure{border-color:color-mix(in srgb,var(--theme-border-soft) 84%,var(--sb-death-red) 16%);background:color-mix(in srgb,var(--theme-surface-raised) 93%,var(--sb-death-red) 7%)}
            .sb-death-track-head{display:flex;align-items:center;justify-content:space-between;gap:4px;margin-bottom:6px;color:var(--theme-text-secondary);font:850 9px/1.1 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.05em}
            .sb-death-track-head strong{font:900 12px/1 Georgia,serif;color:var(--sb-death-green)}
            .sb-death-track.is-failure .sb-death-track-head strong{color:var(--sb-death-red)}
            .sb-death-track-pips{display:flex;justify-content:center;gap:9px;flex-wrap:wrap}
            .sb-death-pip{width:23px;height:23px;border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 76%,var(--sb-death-green) 24%);border-radius:50%;background:var(--theme-surface-card);color:transparent;display:grid;place-items:center;cursor:pointer;transition:transform .18s,background .18s,border-color .18s}
            .sb-death-track.is-failure .sb-death-pip{border-color:color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 76%,var(--sb-death-red) 24%)}
            .sb-death-pip.is-filled{background:var(--sb-death-green);border-color:var(--sb-death-green);color:white}
            .sb-death-track.is-failure .sb-death-pip.is-filled{background:var(--sb-death-red);border-color:var(--sb-death-red)}
            .sb-death-pip:hover:not(:disabled){transform:scale(1.08)}
            .sb-death-die-launch{position:relative;flex:none;width:81px;height:81px;display:grid;place-items:center;background:none;border:0;outline-offset:1px;cursor:grab;touch-action:none;user-select:none;transition:filter .16s ease}
            .sb-death-die-launch .sb-poly-die{pointer-events:none;transform:translate3d(var(--death-grab-x,0px),var(--death-grab-y,0px),0) rotate(var(--death-grab-angle,0deg));filter:drop-shadow(0 7px 7px rgba(0,0,0,.26));transition:transform .22s cubic-bezier(.2,.76,.28,1)}
            .sb-death-die-launch .sb-poly-number{rotate:calc(-1 * var(--death-grab-angle,0deg))}
            .sb-death-die-launch.is-grabbed{cursor:grabbing}.sb-death-die-launch.is-grabbed .sb-poly-die{transition:none!important}
            .sb-death-die-launch:hover:not(:disabled){filter:brightness(1.1)}
            .sb-death-die-launch.is-busy .sb-poly-die{opacity:.36}
            .sb-death-roll-cta{flex:none;padding:10px 12px;border:1px solid var(--theme-accent-strong,var(--theme-accent));border-radius:10px;background:var(--theme-button-background,var(--theme-accent));color:var(--theme-button-text,#fff);font:850 10px/1 system-ui,sans-serif;cursor:pointer;white-space:nowrap;transition:filter .18s ease,transform .18s ease}
            .sb-death-roll-cta:hover:not(:disabled){filter:brightness(1.13);transform:translateY(-1px)}
            .sb-death-roll-cta:disabled,.sb-death-die-launch:disabled{opacity:.6;cursor:not-allowed}
            .sb-death-strip-feedback{display:flex;align-items:center;gap:10px;justify-content:center;min-height:10px;text-align:center;font:750 10px/1.25 system-ui,sans-serif;color:var(--theme-text-secondary)}
            .sb-death-strip-feedback .is-gold{color:#c6963f}.sb-death-strip-feedback .is-red,.sb-death-strip-feedback .is-error{color:var(--sb-death-red)}
            .sb-death-save-retry{border:1px solid var(--theme-border-soft);border-radius:7px;padding:4px 8px;background:var(--theme-surface-raised);font:850 9px/1 system-ui,sans-serif;color:var(--theme-text-strong)}
            @media(max-width:620px){.sb-death-strip-inner{gap:7px;padding:9px}.sb-death-strip-action{flex-wrap:wrap;gap:6px}.sb-death-track{flex:1 1 35%;max-width:none;order:2}.sb-death-die-launch{order:0;width:83px;height:83px}.sb-death-roll-cta{order:1}.sb-death-strip-subtitle{font-size:9px}}
            /* V66.2.176 — faixa leve e dados liberados para toda a viewport. */
            .sb-death-strip{--sb-death-red:#a65b5d;--sb-death-green:#337c67}
            .sb-death-strip-inner{position:relative;gap:8px!important;padding:13px 15px 10px!important;border-radius:18px!important;border:1px solid color-mix(in srgb,var(--theme-border-soft) 75%,var(--sb-death-red) 25%)!important;background:linear-gradient(145deg,color-mix(in srgb,var(--theme-surface-card) 97%,var(--sb-death-red) 3%),var(--theme-surface-card))!important;box-shadow:0 7px 21px rgba(42,19,17,.09),inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 20%,transparent)!important}
            .sb-death-strip-heading{padding:0 0 7px!important;border-bottom:1px solid color-mix(in srgb,var(--theme-border-soft) 75%,transparent)!important}
            .sb-death-strip-identity{display:flex;align-items:center;gap:10px;min-width:0}
            .sb-death-strip-symbol{width:32px;height:32px;display:grid;place-items:center;flex:none;border-radius:10px;background:color-mix(in srgb,var(--theme-accent) 12%,var(--theme-surface-raised));border:1px solid color-mix(in srgb,var(--theme-accent) 19%,var(--theme-border-soft));color:var(--theme-accent-strong,var(--theme-accent));font-size:19px;box-shadow:inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 23%,transparent)}
            .sb-death-strip-heading strong{font:900 16px/1.12 Georgia,serif!important;letter-spacing:-.02em}
            .sb-death-strip-subtitle{font:650 10px/1.32 system-ui,sans-serif!important;margin-top:3px!important}
            .sb-death-strip-collapse{width:28px!important;height:28px!important;border-radius:9px!important}
            .sb-death-strip-action{gap:15px!important;min-height:110px!important;align-items:center!important}
            .sb-death-track{flex:1 1 142px!important;max-width:none!important;padding:10px 12px 12px!important;border-radius:12px!important;background:color-mix(in srgb,var(--theme-surface-card) 91%,var(--sb-death-green) 9%)!important;box-shadow:inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 20%,transparent)}
            .sb-death-track.is-failure{background:color-mix(in srgb,var(--theme-surface-card) 91%,var(--sb-death-red) 9%)!important}
            .sb-death-track-head{margin-bottom:10px!important;font-size:10px!important;letter-spacing:.065em!important}
            .sb-death-track-head strong{font-size:16px!important}
            .sb-death-track-pips{gap:12px!important}
            .sb-death-pip{width:26px!important;height:26px!important;border-width:1.5px!important;box-shadow:inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 17%,transparent)}
            .sb-death-die-home{position:relative;flex:none;display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:113px;gap:1px}
            .sb-death-die-home-hint{font:800 9px/1.1 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.11em;color:var(--theme-text-secondary);opacity:.8}
            .sb-death-die-launch{width:111px!important;height:111px!important;border-radius:20px!important}
            .sb-death-die-launch .sb-poly-die{transform:none!important;filter:drop-shadow(0 12px 9px rgba(0,0,0,.19))!important;transition:filter .22s ease,scale .22s ease!important}
            .sb-death-die-launch:hover:not(:disabled) .sb-poly-die{scale:1.065;filter:drop-shadow(0 16px 10px rgba(0,0,0,.24)) drop-shadow(0 0 9px color-mix(in srgb,var(--theme-accent) 18%,transparent))!important}
            .sb-death-die-launch.is-ghosted .sb-poly-die{visibility:hidden!important}
            .sb-death-die-launch.is-busy .sb-poly-die{opacity:.32}
            .sb-death-roll-cta{display:flex!important;align-items:center;justify-content:center;flex-direction:column;gap:4px;padding:9px!important;min-width:54px!important;border-radius:12px!important;font-size:9px!important;line-height:1.1!important}
            .sb-death-strip-feedback{font:750 10px/1.25 system-ui,sans-serif!important}
            .sb-death-strip-feedback:empty{display:none}
            .sb-death-drag-ghost{position:fixed;left:0;top:0;z-index:455;pointer-events:none!important;user-select:none;will-change:top,left,transform;transform:translate3d(-50%,-50%,0) rotate(var(--sb-death-ghost-angle,0deg));filter:drop-shadow(0 20px 16px rgba(0,0,0,.32));display:grid;place-items:center;color:var(--theme-text-strong)}
            .sb-death-drag-ghost .sb-poly-die{scale:1.02;--sb-die-base:color-mix(in srgb,var(--theme-accent-strong,#813d35) 75%,#72463e 25%);filter:drop-shadow(0 2px 2px rgba(0,0,0,.17))}
            .sb-death-drag-ghost .sb-poly-number{rotate:var(--sb-death-ghost-counter,0deg)!important}
            @media(max-width:720px){.sb-death-strip-inner{padding:10px!important}.sb-death-strip-action{gap:7px!important;flex-wrap:wrap}.sb-death-track{flex:1 1 32%!important;order:2;padding:9px 7px!important}.sb-death-die-home{order:0;min-width:100px}.sb-death-roll-cta{order:1;min-width:57px!important}.sb-death-pip{width:24px!important;height:24px!important}.sb-death-track-pips{gap:6px!important}}
            @media(max-width:720px){
                .sb-death-strip-action{display:grid!important;grid-template-columns:minmax(0,1fr) minmax(0,1fr)!important;align-items:center;justify-items:center;gap:10px!important}
                .sb-death-die-home{grid-column:1;grid-row:1;order:initial!important}
                .sb-death-roll-cta{grid-column:2;grid-row:1;order:initial!important;min-width:76px!important;min-height:50px}
                .sb-death-track{width:100%;max-width:none;min-width:0!important;flex:none!important;order:initial!important}
                .sb-death-track.is-success{grid-column:1;grid-row:2}
                .sb-death-track.is-failure{grid-column:2;grid-row:2}
            }
            @media(prefers-reduced-motion:reduce){.sb-death-drag-ghost,.sb-death-die-launch .sb-poly-die{transition:none!important;animation:none!important}}

            /* V66.2.177 — Salvamento compacto: apenas contadores, dado e ações necessárias. */
            .sb-death-strip{max-width:690px!important}
            .sb-death-strip-inner{padding:12px 16px 11px!important;border-radius:17px!important;border-color:color-mix(in srgb,var(--theme-border-soft) 72%,var(--sb-death-red) 28%)!important;background:linear-gradient(165deg,color-mix(in srgb,var(--theme-surface-card) 98%,var(--sb-death-red) 2%),var(--theme-surface-card))!important;box-shadow:0 10px 26px rgba(50,20,18,.09),inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 23%,transparent)!important}
            .sb-death-strip-heading{padding:0 0 9px!important;border-bottom:1px solid color-mix(in srgb,var(--theme-border-soft) 76%,transparent)!important}
            .sb-death-strip-identity{gap:0!important}
            .sb-death-strip-heading strong{font:900 17px/1.15 Georgia,serif!important;letter-spacing:-.014em!important}
            .sb-death-strip-subtitle{font-size:10px!important;color:var(--theme-text-secondary)!important}
            .sb-death-strip-collapse{width:24px!important;height:24px!important;border-radius:7px!important;background:transparent!important;font:800 20px/1 system-ui,sans-serif!important;color:var(--theme-text-secondary)!important;border:1px solid transparent!important}
            .sb-death-strip-collapse:hover,.sb-death-strip-collapse:focus-visible{background:var(--theme-card-hover)!important;border-color:var(--theme-border-soft)!important;color:var(--theme-text-strong)!important}
            .sb-death-strip-action{min-height:91px!important;gap:14px!important}
            .sb-death-track{padding:8px 11px!important;background:transparent!important;border:1px solid color-mix(in srgb,var(--theme-border-soft) 78%,var(--sb-death-green) 22%)!important;border-radius:10px!important;box-shadow:none!important}
            .sb-death-track.is-failure{background:transparent!important;border-color:color-mix(in srgb,var(--theme-border-soft) 78%,var(--sb-death-red) 22%)!important}
            .sb-death-track-head{margin-bottom:7px!important;font-size:9px!important;letter-spacing:.07em!important}
            .sb-death-track-pips{gap:10px!important}
            .sb-death-pip{width:25px!important;height:25px!important;box-shadow:inset 0 1px 1px rgba(0,0,0,.025)!important}
            .sb-death-die-home{min-width:106px!important}
            .sb-death-die-launch{width:104px!important;height:104px!important}
            .sb-death-die-launch .sb-poly-die{transition:scale .2s ease,filter .2s ease!important}
            .sb-death-die-home-hint{font-size:9px!important;letter-spacing:.06em!important}
            .sb-death-roll-cta{display:grid!important;place-items:center!important;min-width:70px!important;min-height:37px!important;padding:8px 11px!important;font:850 10px/1 system-ui,sans-serif!important;border-radius:9px!important;box-shadow:0 5px 12px color-mix(in srgb,var(--theme-accent) 13%,transparent)!important}
            .sb-death-strip-feedback{min-height:20px!important;gap:12px!important;font-size:10px!important}
            .sb-death-save-retry{padding:5px 8px!important;border-radius:8px!important}
            .sb-death-drag-ghost{will-change:transform,opacity!important;contain:layout style;filter:drop-shadow(0 16px 14px rgba(0,0,0,.26))!important}
            .sb-death-drag-ghost .sb-poly-die{transition:none!important}
            @media(max-width:720px){.sb-death-strip-inner{padding:10px!important}.sb-death-strip-action{min-height:0!important;gap:9px!important}.sb-death-die-launch{width:96px!important;height:96px!important}.sb-death-track{padding:7px 8px!important}.sb-death-roll-cta{min-width:86px!important;min-height:40px!important}}

            /* Memorial persistente. O navegador é utilizável e não recebe filtro cinza. */
            body.sb-character-deceased .sb-nav83 {
                background:linear-gradient(180deg,#303033,#29282b)!important;
                border-right:1px solid #65534d!important;
                color:#e6d4c3!important;
                filter:none!important;
                box-shadow:inset -1px 0 0 rgba(225,188,141,.09);
                transition:background .65s ease,border-color .65s ease;
            }
            body.sb-character-deceased .sb-nav83__top,
            body.sb-character-deceased .sb-nav83__footer {border-color:rgba(219,185,149,.17)!important}
            body.sb-character-deceased .sb-nav83 .sb-nav83__logo {
                background:#66504a!important;color:#fff3e5!important;
                box-shadow:inset 0 0 0 1px rgba(233,189,144,.35),0 2px 8px rgba(0,0,0,.18)!important;
            }
            body.sb-character-deceased .sb-nav83 .sb-nav83__link {
                color:#c7b3a5!important;background:transparent!important;
            }
            body.sb-character-deceased .sb-nav83 .sb-nav83__link:is(:hover,:focus-visible),
            body.sb-character-deceased .sb-nav83 .sb-nav83__link--active {
                color:#f7e3ce!important;background:rgba(206,160,129,.15)!important;
            }
            body.sb-character-deceased .sb-nav83 .sb-nav83__link--active::before {
                background:#c9a08a!important;
            }
            body.sb-character-deceased .sb-nav83__tooltip,
            body.sb-character-deceased .sb-sheet-tools-sidebar .sb-nav83__tooltip {
                background:#383235!important;color:#f6e5d7!important;
                border-color:#77615a!important;
            }
            body.sb-character-deceased .sb-sheet-tools-sidebar {
                border-top-color:rgba(212,168,141,.22)!important;
            }
            body.sb-character-deceased .sb-sheet-tools-sidebar .sb-nav83__link {
                color:#cbb8a9!important;background:transparent!important;
            }
            body.sb-character-deceased .sb-sheet-tools-sidebar .sb-nav83__link:is(:hover,:focus-visible),
            body.sb-character-deceased .sb-sheet-tools-sidebar .sb-nav83__link.is-open {
                color:#f6e4d1!important;background:rgba(206,160,129,.15)!important;
            }
            /* O escurecimento não captura eventos da navegação. */
            body.sheet-death-screen-active .sb-nav83,
            body.sb-character-deceased .sb-nav83 {pointer-events:auto}

            /* V66.2.179 — Salvamentos como ferramenta flutuante, sem aumentar header. */
            .sb-death-floating{position:fixed!important;right:82px;bottom:25px;left:auto;top:auto;z-index:285!important;width:min(420px,calc(100vw - 88px))!important;max-width:none!important;margin:0!important;pointer-events:auto;color:var(--theme-text-primary);font-family:system-ui,-apple-system,'Segoe UI',sans-serif}
            /* V66.2.184: um portal remanescente nunca pode aparecer com PV positivos.
               Por padrão, o componente é invisível; Alpine libera a exibição
               somente se todas as regras de salvamento forem atendidas. */
            .sb-death-floating:not([data-death-visible="true"]){display:none!important}
            .sb-death-floating .sb-death-strip-inner{gap:7px!important;padding:12px 14px 10px!important;border-radius:18px!important;border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 77%,#8b3d39 23%)!important;background:linear-gradient(160deg,var(--theme-surface-card),color-mix(in srgb,var(--theme-surface-card) 92%,#a36356 8%))!important;box-shadow:0 15px 42px rgba(31,21,19,.23),0 3px 9px rgba(0,0,0,.075),inset 0 1px 0 rgba(255,255,255,.32)!important}
            .sb-death-floating .sb-death-strip-heading{cursor:grab;touch-action:none;user-select:none;padding:0 0 7px!important;gap:7px!important;border-bottom:1px solid color-mix(in srgb,var(--theme-border-soft) 64%,transparent)!important}
            .sb-death-floating .sb-death-strip-heading.is-moving{cursor:grabbing}
            .sb-death-floating .sb-death-strip-heading strong{font:900 16px/1.15 Georgia,serif!important;letter-spacing:0!important}
            .sb-death-floating .sb-death-strip-subtitle{font:650 9px/1.15 system-ui,sans-serif!important;letter-spacing:.01em;opacity:.77;margin-top:2px!important}
            .sb-death-floating .sb-death-strip-collapse{flex:none;width:24px!important;height:24px!important;border-radius:50%!important;border:1px solid color-mix(in srgb,var(--theme-border-soft) 70%,transparent)!important;background:color-mix(in srgb,var(--theme-surface-raised) 84%,transparent)!important;font:900 16px/1 system-ui,sans-serif!important}
            .sb-death-floating .sb-death-strip-action{display:grid!important;grid-template-columns:minmax(0,1fr) 104px minmax(0,1fr)!important;gap:6px!important;align-items:center!important;min-height:97px!important}
            .sb-death-floating .sb-death-track,.sb-death-floating .sb-death-track.is-failure{order:initial!important;grid-row:1!important;flex:none!important;max-width:none!important;min-width:0!important;width:100%!important;border:0!important;border-radius:0!important;border-top:1px solid color-mix(in srgb,var(--theme-border-soft) 60%,transparent)!important;background:none!important;padding:9px 2px 3px!important;box-shadow:none!important}
            .sb-death-floating .sb-death-track.is-success{grid-column:1!important}.sb-death-floating .sb-death-track.is-failure{grid-column:3!important}
            .sb-death-floating .sb-death-track-head{margin-bottom:12px!important;font:850 8px/1 system-ui,sans-serif!important;letter-spacing:.1em!important}
            .sb-death-floating .sb-death-track-head strong{font:900 14px/1 Georgia,serif!important}
            .sb-death-floating .sb-death-track-pips{gap:7px!important;justify-content:center!important}
            .sb-death-floating .sb-death-pip{width:22px!important;height:22px!important;border-radius:50%!important;border-width:1px!important;transition:transform .15s ease,background .2s ease!important}
            .sb-death-floating .sb-death-die-home{grid-column:2!important;grid-row:1!important;order:initial!important;min-width:0!important;width:104px!important;height:105px!important;display:grid!important;place-items:center!important;align-self:center!important}
            .sb-death-floating .sb-death-die-launch{display:grid!important;place-items:center!important;width:102px!important;height:102px!important;grid-row:1!important;grid-column:1!important;touch-action:none!important;cursor:grab!important}
            .sb-death-floating .sb-death-die-launch:disabled{cursor:not-allowed!important}
            .sb-death-floating .sb-death-die-launch .sb-poly-die{transition:scale .23s cubic-bezier(.22,.75,.24,1),filter .23s ease!important;filter:drop-shadow(0 10px 8px rgba(0,0,0,.24))!important;pointer-events:none}
            .sb-death-floating .sb-death-die-launch:hover:not(:disabled) .sb-poly-die{scale:1.065;filter:drop-shadow(0 14px 11px rgba(0,0,0,.29)) drop-shadow(0 0 10px color-mix(in srgb,var(--theme-accent) 20%,transparent))!important}
            .sb-death-floating .sb-death-die-home-hint{grid-row:1;grid-column:1;align-self:end;justify-self:center;margin-bottom:-1px;font:900 8px/1 system-ui,sans-serif!important}
            .sb-death-floating .sb-death-strip-feedback{min-height:16px!important;gap:8px!important;justify-content:center!important;font:800 10px/1.25 system-ui,sans-serif!important}
            .sb-death-floating .sb-death-save-retry{font-size:9px!important;border-radius:9px!important;cursor:pointer}
            .sb-death-drag-ghost{z-index:450!important;transform-origin:50% 50%;filter:drop-shadow(0 17px 17px rgba(0,0,0,.28))!important}
            .hero-header-segment .sb-death-compact-tab{position:absolute!important;left:50%;bottom:-17px;right:auto;top:auto;transform:translateX(-50%);z-index:55!important;margin:0!important;white-space:nowrap}
            .sb-death-compact-tab .hero-death-tab-button{min-height:37px;height:auto!important;padding:9px 13px!important;gap:7px!important;border-radius:999px!important;background:var(--theme-surface-card)!important;color:var(--theme-text-strong)!important;border:1px solid color-mix(in srgb,var(--theme-border-soft) 72%,var(--theme-accent) 28%)!important;box-shadow:0 8px 20px rgba(0,0,0,.12)!important;font-size:9px!important}
            .sb-death-tab-count{font:900 9px/1 Georgia,serif;color:var(--theme-state-damage,#b7514e);padding-left:5px;border-left:1px solid var(--theme-border-soft)}
            .sb-death-compact-tab .hero-death-tab-button.is-open{border-color:color-mix(in srgb,var(--theme-accent) 42%,var(--theme-border-soft))!important;box-shadow:0 10px 24px rgba(0,0,0,.14)!important}
            @media(max-width:620px){.sb-death-floating{right:10px;bottom:12px;width:min(410px,calc(100vw - 20px))!important}.sb-death-floating .sb-death-strip-inner{padding:10px!important}.sb-death-floating .sb-death-strip-action{grid-template-columns:minmax(0,1fr) 84px minmax(0,1fr)!important;min-height:82px!important;gap:4px!important}.sb-death-floating .sb-death-die-home{width:84px!important;height:88px!important}.sb-death-floating .sb-death-die-launch{width:84px!important;height:84px!important}.sb-death-floating .sb-death-die-launch .sb-poly-die{scale:.74}.sb-death-floating .sb-death-pip{width:20px!important;height:20px!important}.sb-death-floating .sb-death-track-pips{gap:4px!important}.hero-header-segment .sb-death-compact-tab{bottom:-17px!important;top:auto!important;left:50%!important;right:auto!important}}
            @media(prefers-reduced-motion:reduce){.sb-death-floating .sb-death-die-launch .sb-poly-die{transition:none!important}}

            /* V66.2.181 — A despedida usa a apresentação clássica.
               Apenas mantém contraste e disponibiliza a navegação. */
            .sb-death-final-card .death-revive-reveal button {font-family:Georgia,serif}
            .sb-death-finale {background:rgba(10,9,10,.54);backdrop-filter:blur(3px)}
            .sb-death-final-card {box-shadow:0 28px 92px rgba(0,0,0,.54)!important}

            /* V66.2.181 — Atalho preso ao próprio header, estado de morte persistente. */
            .hero-header-segment .sb-death-compact-tab{pointer-events:auto}
            .hero-header-segment .sb-death-compact-tab .hero-death-tab-button{
                background:var(--theme-surface-card,#fffaf2)!important;
                border:1px solid color-mix(in srgb,var(--theme-border-soft,#cab5a0) 70%,var(--theme-accent,#9c6f54) 30%)!important;
                color:var(--theme-text-strong,#553b2f)!important;
                box-shadow:0 5px 16px rgba(55,33,28,.11),inset 0 1px 0 rgba(255,255,255,.35)!important;
            }
            .hero-header-segment .sb-death-compact-tab .hero-death-tab-button:hover{
                background:var(--theme-card-hover,#faf2e8)!important;
                transform:translateY(-2px);
            }
            /* V66.2.182 — o estado faz parte do retrato, não do rodapé do hero. */
            .hero-header-identity-zone.sb-identity-deceased .hero-data-status-strip{
                cursor:pointer!important;outline-offset:2px;
                border-color:#958885!important;
                background:linear-gradient(180deg,#e8e3de,#d9d4ce)!important;
                transition:background .18s ease,box-shadow .18s ease;
            }
            .hero-header-identity-zone.sb-identity-deceased .hero-data-status-strip:is(:hover,:focus-visible){
                background:#eee7e0!important;box-shadow:0 0 0 2px rgba(154,132,121,.24)!important;
            }
            .hero-header-identity-zone.sb-identity-deceased .hero-data-status-text{color:#645957!important;letter-spacing:.09em!important;font-size:9px!important}
            .hero-header-identity-zone.sb-identity-deceased .hero-data-status-dot{background:#766b69!important;box-shadow:0 0 0 3px rgba(122,109,101,.12)}
            /* Remover recorte imposto pelo Theme Core: aba metade abaixo da borda. */
            .character-sheet-theme[data-theme] .hero-header-segment:has(.sb-death-compact-tab){overflow:visible!important}
            .hero-header-segment .sb-death-compact-tab{
                bottom:0!important;left:50%!important;top:auto!important;right:auto!important;
                transform:translate(-50%,50%)!important;z-index:210!important;pointer-events:auto!important;
            }
            .hero-header-segment .sb-death-compact-tab .hero-death-tab-button{min-height:33px!important;border-radius:999px!important}
            @media(max-width:620px){.hero-header-segment .sb-death-compact-tab{bottom:0!important}}
            /* Confirmação de reviver: não bloqueia a ficha até uma cura ser solicitada. */
            .sb-revive-confirm-layer{position:fixed;inset:0;z-index:650;display:grid;place-items:center;padding:16px;isolation:isolate}
            .sb-revive-confirm-backdrop{position:absolute;inset:0;background:rgba(15,13,15,.65);backdrop-filter:blur(3px)}
            .sb-revive-confirm-card{position:relative;width:min(390px,100%);padding:27px 25px 22px;text-align:center;
                border:1px solid #9d8177;border-radius:23px;background:linear-gradient(180deg,#faf6f1,#eee4da);
                box-shadow:0 27px 80px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.7);color:#463735}
            .sb-revive-confirm-seal{margin:0 auto 10px;display:grid;place-items:center;width:39px;height:39px;border-radius:50%;
                border:1px solid #b9a39a;background:#e9dfd8;color:#7a625b;font:600 22px/1 Georgia,serif}
            .sb-revive-confirm-overline{font:850 9px/1 system-ui,sans-serif;letter-spacing:.17em;text-transform:uppercase;color:#947b71}
            .sb-revive-confirm-card h2{margin:9px 0 10px;font:900 21px/1.17 Georgia,serif;color:#3e302e}
            .sb-revive-confirm-copy{font:600 12px/1.55 system-ui,sans-serif;color:#70605a}
            .sb-revive-confirm-copy strong{color:#463735}
            .sb-revive-confirm-error{margin:11px 0 0;color:#a2322a;font:750 11px/1.4 system-ui,sans-serif}
            .sb-revive-confirm-actions{margin-top:24px;display:grid;grid-template-columns:1fr 1fr;gap:10px}
            .sb-revive-confirm-actions button{padding:11px 12px;border-radius:10px;font:850 11px/1 system-ui,sans-serif;cursor:pointer;transition:filter .2s ease}
            .sb-revive-confirm-actions button:disabled{opacity:.6;cursor:wait}
            .sb-revive-confirm-cancel{background:#fffaf5;border:1px solid #d6c5b6;color:#65544e}
            .sb-revive-confirm-accept{background:#684a43;border:1px solid #684a43;color:#fff8ef;box-shadow:0 6px 15px rgba(78,40,34,.16)}
            .sb-revive-confirm-actions button:hover:not(:disabled){filter:brightness(1.07)}

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
        deathSequenceDismissed: (() => {
            try { return sessionStorage.getItem('spellbound-death-ack-{{ (int) $character->id }}') === '1'; }
            catch (_error) { return false; }
        })(),
        deathHopeVisible: false,
        deathReviveVisible: false,

        deathHopeTimer: null,
        deathReviveTimer: null,

        deathReviving: false,
        deathReviveError: null,
        // Cura sobre um personagem já falecido requer confirmação explícita.
        reviveHealPrompt: null,
        reviveHealBusy: false,
        reviveHealError: null,

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
        deathRollError: null,
        deathRollPendingResult: null,
        deathRollVariant: 0,
        deathRollSequence: 0,
        deathAutoPrompted: false,
        deathTurnActive: false,
        deathTurnSeen: false,
        deathTurnKey: null,
        deathTurnResolved: false,
        deathByRoll: false,
        deathDieDrag: null,
        // A peça arrastada vive no body; jamais fica recortada pelo cabeçalho.
        deathDieGhost: null,
        deathGhostFrame: null,
        deathGhostTarget: null,
        deathDieOffset: {x:0,y:0,tilt:0},
        deathDieRelease: {x:0,y:0},
        deathPanelAt: null,
        deathPanelDragging: null,

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
                return 'Falecido';
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
                return 'text-[#786663]';
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

        // O painel é criado apenas quando o personagem realmente precisa
        // fazer salvamentos. Incluímos a checagem explícita de PV para evitar
        // depender de uma janela aberta em uma instância anterior do Alpine.
        deathSavePanelEligible() {
            return Number(this.currentHp) <= 0
                && Number(this.exhaustionLevel) < 6
                && this.isDying
                && !this.isDead
                && !this.isStable
                && this.deathSaveSuccesses < this.deathSuccessLimit
                && this.deathSaveFailures < this.deathFailureLimit;
        },

        deathSavePanelVisible() {
            return this.deathSavePanelEligible() && !!this.deathDrawerOpen;
        },

        // Em navegações internas, um x-teleport de uma ficha anterior pode
        // sobreviver fora da árvore da ficha. Limpar esses órfãos no início
        // evita que uma rolagem antiga apareça sobre outro personagem.
        clearOrphanDeathPanels() {
            document.querySelectorAll('.sb-death-floating').forEach((panel) => {
                panel.remove();
            });
        },

        openDeathDrawer() {
            if (!this.deathSavePanelEligible()) return;
            this.deathDrawerOpen = true;
        },

        closeDeathDrawer() {
            if (this.deathRolling) return;
            this.deathDrawerOpen = false;
            this.cancelDeathDieDrag();
        },

        // Um pedido inicial quando o personagem chega a 0 PV. Fechar a gaveta
        // manualmente não abre a mesma gaveta novamente a cada atualização.
        syncDeathSaveAvailability(dying) {
            // Não reabrir o painel se o personagem recuperou PV,
            // estabilizou ou morreu, mesmo após uma atualização externa.
            const alive = this.currentHp > 0 && !this.isDead;
            const resolved = !this.deathSavePanelEligible();
            if (resolved) {
                this.deathDrawerOpen = false;
                if (!this.deathRolling) this.cancelDeathDieDrag();
                if (this.isStable) this.deathTurnResolved = true;
                if (alive) {
                    this.deathAutoPrompted = false;
                    this.deathTurnResolved = false;
                    this.deathByRoll = false;
                    this.deathRollPendingResult = null;
                }
                return;
            }
            if (dying && !this.deathAutoPrompted) {
                this.deathAutoPrompted = true;
                this.deathDrawerOpen = true;
            }
            if (!dying) {
                this.deathAutoPrompted = false;
                this.deathTurnResolved = false;
                this.deathByRoll = false;
                this.deathDrawerOpen = false;
                this.deathRollPendingResult = null;
                this.cancelDeathDieDrag();
            }
        },

        // A ficha já compartilha character-turn-state com Iniciativa.
        // A mudança false -> true marca um novo turno; quando a origem envia
        // rodada/ID, detectamos também mudança da chave sem duplicar o aviso.
        syncDeathTurnState(payload) {
            if (!payload || typeof payload !== 'object') return;
            const active = !!payload.isCurrentTurn;
            this.deathTurnSeen = true;
            const suppliedKey = payload.turnKey ?? payload.turn_id
                ?? payload.currentTurnId ?? payload.round_id
                ?? payload.round_number ?? payload.round ?? null;
            const key = suppliedKey == null ? null : String(suppliedKey);
            const newTurn = active && (!this.deathTurnActive
                || (key !== null && key !== this.deathTurnKey));
            this.deathTurnActive = active;
            if (key !== null) this.deathTurnKey = key;
            if (newTurn && this.deathRollPendingResult === null && !this.deathRolling) {
                this.deathTurnResolved = false;
                if (this.isDying) {
                    this.deathDrawerOpen = true;
                    this.deathRollResult = null;
                    this.deathRollPreview = null;
                    this.deathRollSettled = false;
                    this.deathRollError = null;
                    this.deathDieOffset = {x:0,y:0,tilt:0};
                }
            }
        },

        deathDieStyle() {
            // A miniatura nunca é deslocada: a peça na mão é movida pelo body.
            return '--death-grab-x:0px;--death-grab-y:0px;--death-grab-angle:0deg;';
        },

        // A janela fica no body e nunca amplia o cabeçalho da ficha.
        deathPanelStorageKey(){ return 'spellbound-death-panel-v179-{{ (int) $character->id }}'; },
        restoreDeathPanelPosition(){
            try {
                const at=JSON.parse(localStorage.getItem(this.deathPanelStorageKey())||'null');
                if(at && Number.isFinite(at.x) && Number.isFinite(at.y)) {
                    this.deathPanelAt={x:at.x,y:at.y};
                    this.$nextTick(()=>this.clampDeathPanelPosition());
                }
            } catch(_error) {}
        },
        deathPanelStyle(){
            const at=this.deathPanelAt;
            return at ? `left:${at.x}px;top:${at.y}px;right:auto;bottom:auto;` : '';
        },
        clampDeathPanelPosition(){
            if(!this.deathPanelAt) return;
            const el=this.$refs?.deathFloatingPanel;
            const w=el?.offsetWidth||430, h=el?.offsetHeight||180;
            this.deathPanelAt={
                x:Math.max(7,Math.min(window.innerWidth-w-7,this.deathPanelAt.x)),
                y:Math.max(7,Math.min(window.innerHeight-h-7,this.deathPanelAt.y)),
            };
        },
        beginDeathPanelDrag(event){
            if(event.pointerType==='mouse' && event.button!==0) return;
            if(event.target.closest('button'))return;
            const rect=this.$refs?.deathFloatingPanel?.getBoundingClientRect();
            if(!rect)return;
            this.deathPanelAt={x:rect.left,y:rect.top};
            this.deathPanelDragging={id:event.pointerId,x:event.clientX,y:event.clientY,left:rect.left,top:rect.top};
        },
        moveDeathPanelDrag(event){
            const drag=this.deathPanelDragging;
            if(!drag || drag.id!==event.pointerId)return;
            this.deathPanelAt={x:drag.left+event.clientX-drag.x,y:drag.top+event.clientY-drag.y};
            this.clampDeathPanelPosition();
        },
        endDeathPanelDrag(){
            if(!this.deathPanelDragging)return;
            this.deathPanelDragging=null;
            try{localStorage.setItem(this.deathPanelStorageKey(),JSON.stringify(this.deathPanelAt));}catch(_error){}
        },

        beginDeathDieDrag(event) {
            if (!this.isDying || this.deathRolling || (this.deathTurnSeen && this.deathTurnResolved)
                || this.deathRollPendingResult !== null) return;
            if (event.pointerType === 'mouse' && event.button !== 0) return;
            this.removeDeathDieGhost();
            try{this.$refs?.deathSaveDie?.setPointerCapture?.(event.pointerId);}catch(_error){}
            const rect=this.$refs?.deathSaveDie?.getBoundingClientRect();
            this.deathDieDrag = {
                id:event.pointerId, fromX:event.clientX, fromY:event.clientY,
                centerX:rect ? rect.left+rect.width/2 : event.clientX,
                centerY:rect ? rect.top+rect.height/2 : event.clientY,
                lastX:event.clientX,lastY:event.clientY,
                lastTime:event.timeStamp || performance.now(),
                worldX:event.clientX,worldY:event.clientY,
                vx:0,vy:0,tilt:0,moved:false,
                samples:[{x:event.clientX,y:event.clientY,t:event.timeStamp||performance.now()}],
            };
            this.deathDieOffset = {x:0,y:0,tilt:0};
        },

        buildDeathDieGhost(drag) {
            if (this.deathDieGhost || !drag) return;
            const button=this.$refs?.deathSaveDie;
            const poly=button?.querySelector('.sb-poly-die');
            if (!poly) return;
            const ghost=document.createElement('div');
            ghost.className='character-sheet-theme sb-death-drag-ghost';
            ghost.setAttribute('data-theme',this.$root.closest('[data-theme]')?.getAttribute('data-theme')||'spellbound');
            ghost.setAttribute('aria-hidden','true');
            const art=poly.cloneNode(true);
            // Não registrar uma segunda instância Alpine dentro da peça fantasma.
            [art,...art.querySelectorAll('*')].forEach(node=>{
                for(const attr of [...node.attributes]) {
                    if (/^(?:x-|@|:)/.test(attr.name)) node.removeAttribute(attr.name);
                }
            });
            ghost.appendChild(art);
            document.body.appendChild(ghost);
            this.deathDieGhost=ghost;
            button.classList.add('is-ghosted');
        },

        // Coalescer eventos do mouse em um único transform por quadro.
        flushDeathDieGhost() {
            if(this.deathGhostFrame!==null){window.cancelAnimationFrame(this.deathGhostFrame);this.deathGhostFrame=null;}
            const ghost=this.deathDieGhost, at=this.deathGhostTarget;
            if(!ghost || !at)return;
            ghost.style.transform=`translate3d(${at.x.toFixed(2)}px,${at.y.toFixed(2)}px,0) translate3d(-50%,-50%,0) rotate(${at.tilt.toFixed(2)}deg)`;
            ghost.style.setProperty('--sb-death-ghost-counter',(-at.tilt).toFixed(2)+'deg');
        },
        moveDeathDieGhost(drag) {
            if(!this.deathDieGhost || !drag)return;
            this.deathGhostTarget={x:Number(drag.worldX)||0,y:Number(drag.worldY)||0,tilt:Number(drag.tilt)||0};
            if(this.deathGhostFrame!==null)return;
            this.deathGhostFrame=window.requestAnimationFrame(()=>this.flushDeathDieGhost());
        },

        // O palco físico é compartilhado, mas pode precisar de um quadro
        // para montar o DOM. A peça não deve congelar nessa passagem.
        continueDeathDieFlight(drag,impulse){
            const ghost=this.deathDieGhost;
            if(!ghost || !drag || !ghost.animate)return;
            const current=ghost.style.transform;
            const speed=Math.max(.25,Math.min(1.6,impulse.speed||.5));
            const distance=Math.max(7,Math.min(16,13*speed));
            const dx=(impulse.vx/(impulse.speed||1))*distance;
            const dy=(impulse.vy/(impulse.speed||1))*distance;
            const end=`translate3d(${(drag.worldX+dx).toFixed(2)}px,${(drag.worldY+dy).toFixed(2)}px,0) translate3d(-50%,-50%,0) rotate(${(drag.tilt+speed*55).toFixed(2)}deg)`;
            ghost.animate([
                {transform:current,opacity:1,offset:0},
                {transform:end,opacity:.88,offset:1},
            ],{duration:170,easing:'cubic-bezier(.2,.35,.45,1)',fill:'forwards'});
        },

        removeDeathDieGhost() {
            if(this.deathGhostFrame!==null)window.cancelAnimationFrame(this.deathGhostFrame);
            this.deathGhostFrame=null;
            this.deathGhostTarget=null;
            this.deathDieGhost?.remove();
            this.deathDieGhost=null;
            this.$refs?.deathSaveDie?.classList.remove('is-ghosted');
        },

        putDeathDieGhostBack() {
            const ghost=this.deathDieGhost;
            const rect=this.$refs?.deathSaveDie?.getBoundingClientRect();
            if (!ghost || !rect) {this.removeDeathDieGhost();return;}
            ghost.style.transition='transform .22s cubic-bezier(.2,.83,.25,1),opacity .22s ease';
            ghost.style.transform=`translate3d(${(rect.left+rect.width/2).toFixed(2)}px,${(rect.top+rect.height/2).toFixed(2)}px,0) translate3d(-50%,-50%,0) rotate(0deg)`;
            ghost.style.opacity='0';
            window.setTimeout(()=>{if(this.deathDieGhost===ghost)this.removeDeathDieGhost();else ghost.remove();},210);
        },

        moveDeathDieDrag(event) {
            const drag=this.deathDieDrag;
            if (!drag || drag.id!==event.pointerId) return;
            const dx=event.clientX-drag.fromX;
            const dy=event.clientY-drag.fromY;
            if (Math.hypot(dx,dy)>6) drag.moved=true;
            const now=event.timeStamp||performance.now();
            const dt=Math.max(8,now-drag.lastTime);
            const deltaX=event.clientX-drag.lastX, deltaY=event.clientY-drag.lastY;
            if(Math.hypot(deltaX,deltaY)>.15){
                drag.vx=drag.vx*.44+(deltaX/dt)*.56;
                drag.vy=drag.vy*.44+(deltaY/dt)*.56;
                drag.lastX=event.clientX;drag.lastY=event.clientY;
                drag.lastTime=now;
                drag.samples.push({x:event.clientX,y:event.clientY,t:now});
                drag.samples=drag.samples.filter(item=>now-item.t<150).slice(-16);
            }
            if(drag.moved){
                this.buildDeathDieGhost(drag);
                // Cursor conserva o ponto do poliedro em que o jogador clicou.
                const margin=window.innerWidth<600?63:71;
                drag.worldX=Math.max(margin,Math.min(Math.max(margin,innerWidth-margin),drag.centerX+dx));
                drag.worldY=Math.max(margin,Math.min(Math.max(margin,innerHeight-margin),drag.centerY+dy));
                drag.tilt=Math.max(-32,Math.min(32,drag.vx*18+drag.vy*4));
                this.moveDeathDieGhost(drag);
            }
        },

        deathThrowImpulse(drag,at) {
            const samples=drag?.samples||[];
            if(samples.length<2)return {vx:0,vy:0,speed:0};
            const last=samples[samples.length-1];
            const idle=Math.max(0,at-last.t);
            if(idle>135)return {vx:0,vy:0,speed:0};
            let first=samples[0];
            for(let i=samples.length-2;i>=0;i--){
                first=samples[i];if(last.t-first.t>=70)break;
            }
            const dt=Math.max(12,last.t-first.t);
            const vx=(last.x-first.x)/dt,vy=(last.y-first.y)/dt;
            const freshness=Math.max(0,1-idle/150);
            return {vx:vx*freshness,vy:vy*freshness,speed:Math.hypot(vx,vy)*freshness};
        },

        releaseDeathDieDrag(event) {
            const drag=this.deathDieDrag;
            if (!drag || drag.id!==event.pointerId) return;
            this.moveDeathDieDrag(event);
            this.flushDeathDieGhost();
            const traveled=Math.hypot(event.clientX-drag.fromX,event.clientY-drag.fromY);
            const impulse=this.deathThrowImpulse(drag,event.timeStamp||performance.now());
            this.deathDieDrag=null;
            // Clique e soltura lenta não rolam. O jogador precisa lançar
            // ativamente o d20, como os Dados Livres.
            const thrown=traveled>=23 && impulse.speed>=.33;
            if(thrown) {
                this.continueDeathDieFlight(drag,impulse);
                void this.rollDeathSave({
                    x:drag.worldX,y:drag.worldY,
                    vx:Math.max(-2.9,Math.min(2.9,impulse.vx*2.2)),
                    vy:Math.max(-2.9,Math.min(2.9,impulse.vy*2.2)),
                    tilt:drag.tilt,
                });
            } else {
                // Acompanha a mão de volta com amortecimento.
                this.putDeathDieGhostBack();
            }
        },

        cancelDeathDieDrag() {
            this.deathDieDrag=null;
            this.deathDieOffset={x:0,y:0,tilt:0};
            this.removeDeathDieGhost();
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
                this.deathTurnResolved = true;
                this.deathDrawerOpen = false;
                this.cancelDeathDieDrag();
            }
            if (this.isDead) {
                this.deathDrawerOpen = false;
                this.cancelDeathDieDrag();
            }
        },


        // Mesma física e mesmo resultado da API usados pelos Ataques.
        // O d20 do salvamento é maior, mas NÃO usa um segundo palco/modal.
        async rollDeathSave(origin=null) {
            if (!this.isDying || this.deathRolling || (this.deathTurnSeen && this.deathTurnResolved)
                || this.deathRollPendingResult !== null) {this.removeDeathDieGhost();return;}
            const fx=window.SpellboundDiceFX;
            if (!fx?.start || !fx?.finish || !fx?.fail) {
                this.removeDeathDieGhost();
                this.deathRollError='O motor de dados ainda não está pronto. Abra a ficha completamente e tente novamente.';
                return;
            }
            // Sem origem: o dado entra pelos mesmos lugares e com a mesma
            // física dos ataques. Com origem: arremesso igual aos Dados Livres.
            const token=fx.start({
                kind:'death',
                label:'Salvamento contra a morte',
                expression:'1d20',
                parts:[{label:'d20',expression:'1d20',initialFace:'?'}],
                ...(origin ? {
                    origin:{...origin,scale:1},
                    onMotionStart:()=>this.removeDeathDieGhost(),
                } : {}),
            });
            if (!token) {
                this.removeDeathDieGhost();
                this.deathRollError='Conclua a rolagem atual antes de realizar este salvamento.';
                return;
            }
            this.deathRolling=true;
            this.deathRollError=null;
            this.deathRollSettled=false;
            this.deathRollResult=null;
            this.deathRollSequence++;
            this.deathDieOffset={x:0,y:0,tilt:0};
            try {
                const response=await fetch('{{ url('/api/roll') }}',{
                    method:'POST',
                    headers:{'Content-Type':'application/json','Accept':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'},
                    body:JSON.stringify({expression:'1d20'}),
                });
                const payload=await response.json().catch(()=>({}));
                if (!response.ok) throw new Error(payload?.message||'Não foi possível rolar o dado.');
                const data=payload?.data??payload;
                const face=Array.isArray(data?.rolls)&&data.rolls.length?Number(data.rolls[0]):NaN;
                if (!Number.isInteger(face) || face<1 || face>20)
                    throw new Error('A API não retornou uma face válida de d20.');
                const finished=await fx.finish(token,{
                    label:'Salvamento contra a morte',
                    expression:'1d20',
                    total:face,
                    rolls:[face],
                    results:[{total:face,rolls:[face],modifier:0}],
                    natural:face,
                    exhaustionPenalty:this.exhaustionRollPenalty || 0,
                });
                if (!finished) throw new Error('A apresentação da rolagem foi interrompida.');
                this.deathRollResult=face;
                this.deathRollSettled=true;
                this.deathRollPendingResult=face;
                // Aplica o resultado apenas após a animação terminar.
                await this.commitDeathSaveRoll();
            } catch(error) {
                this.removeDeathDieGhost();
                fx.fail(token,error?.message||'Falha ao rolar o dado.');
                this.deathRollError=error?.message||'Não foi possível realizar o salvamento.';
                if (this.deathRollPendingResult===null) {
                    this.deathRollResult=null;
                    this.deathRollSettled=false;
                }
            } finally {
                this.removeDeathDieGhost();
                this.deathRolling=false;
                this.deathDieOffset={x:0,y:0,tilt:0};
            }
        },

        async commitDeathSaveRoll() {
            const roll=Number(this.deathRollPendingResult);
            if (!this.isDying || !Number.isInteger(roll) || roll<1 || roll>20) return;
            this.deathRollError=null;
            const adjustedRoll=roll-this.exhaustionRollPenalty;
            const changes=roll===20
                ? {current_hp:1,death_save_successes:0,death_save_failures:0}
                : roll===1
                    ? {death_save_failures:Math.min(this.deathFailureLimit,this.deathSaveFailures+2)}
                    : adjustedRoll>=10
                        ? {death_save_successes:Math.min(this.deathSuccessLimit,this.deathSaveSuccesses+1)}
                        : {death_save_failures:Math.min(this.deathFailureLimit,this.deathSaveFailures+1)};
            // Persistência antes da alteração local: evita falsos sucessos ou mortes.
            const saved=await this.persistCombat(changes);
            if (!saved) {
                this.deathRollError='O resultado foi obtido, mas não foi salvo. Tente salvar novamente.';
                return;
            }
            this.deathRollPendingResult=null;
            this.deathTurnResolved=true;
            if (roll===20) {
                const previousHp=this.currentHp;
                this.currentHp=1;
                this.directHp=1;
                this.ghostHp=1;
                this.deathSaveSuccesses=0;
                this.deathSaveFailures=0;
                this.deathByRoll=false;
                this.deathDrawerOpen=false;
                this.dispatchCharacterHpChanged(previousHp,1);
                this.playHpImpact('heal',1,0,'medium');
                return;
            }
            if (changes.death_save_successes!==undefined)
                this.deathSaveSuccesses=changes.death_save_successes;
            if (changes.death_save_failures!==undefined) {
                const lethal=changes.death_save_failures>=this.deathFailureLimit;
                this.deathByRoll=lethal;
                this.deathSaveFailures=changes.death_save_failures;
            }
            if (this.isStable) {
                this.triggerStabilizationEffect();
                this.deathDrawerOpen = false;
                this.deathTurnResolved = true;
                this.cancelDeathDieDrag();
            }
            if (this.isDead) {
                this.deathDrawerOpen = false;
                this.cancelDeathDieDrag();
            }
        },

        /*
        |--------------------------------------------------------------------------
        | MORTE PADRÃO — SEQUÊNCIA DE ESPERANÇA
        |--------------------------------------------------------------------------
        */

        syncSheetDeathVisual(dead) {
            const deceased = !!dead;
            const showingFarewell = deceased && !this.deathSequenceDismissed;
            const sheet = this.$root.closest('.character-sheet-page');
            sheet?.classList.toggle('character-sheet-dead', deceased);
            // Persistir a homenagem visual sem bloquear leitura ou navegação.
            document.body.classList.toggle('sb-character-deceased', deceased);
            document.body.classList.toggle('sheet-death-screen-active', showingFarewell);
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

        dismissDeathSequence() {
            if (!this.isDead) return;
            this.deathSequenceDismissed = true;
            try { sessionStorage.setItem('spellbound-death-ack-{{ (int) $character->id }}','1'); }
            catch (_error) {}
            this.clearDeathSequence();
            this.syncSheetDeathVisual(true);
        },

        reopenDeathSequence() {
            if (!this.isDead) return;
            this.deathSequenceDismissed = false;
            try { sessionStorage.removeItem('spellbound-death-ack-{{ (int) $character->id }}'); }
            catch (_error) {}
            this.clearDeathSequence();
            // Quem revisita um personagem falecido não espera novamente 5 segundos.
            this.deathSequenceStarted = true;
            this.deathHopeVisible = !this.morquenRuleActive;
            this.deathReviveVisible = !this.morquenRuleActive;
            this.syncSheetDeathVisual(true);
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
                if (!dead) {
                    this.deathSequenceDismissed = false;
                    try { sessionStorage.removeItem('spellbound-death-ack-{{ (int) $character->id }}'); }
                    catch (_error) {}
                }
                if (
                    this.deathSequenceStarted ||
                    this.deathHopeVisible ||
                    this.deathReviveVisible
                ) {
                    this.clearDeathSequence();
                }

                return;
            }

            if (this.deathSequenceDismissed) {
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

            this.deathSequenceDismissed =
                false;

            this.deathDrawerOpen =
                false;

            this.deathRollResult =
                null;

            this.deathRollPreview =
                null;

            this.deathRollSettled =
                false;

            this.clearDeathSequence();
            this.deathByRoll = false;
            try { sessionStorage.removeItem('spellbound-death-ack-{{ (int) $character->id }}'); }
            catch (_error) {}
            this.syncSheetDeathVisual(false);

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

        // Um personagem morto não pode voltar por um clique acidental em Cura.
        // O pedido conserva o valor exato; nenhuma mutação acontece até confirmar.
        requestReviveHealing(amount) {
            const parsed = Math.max(0, Math.trunc(Number(amount) || 0));
            if (!this.isDead || parsed < 1) return false;
            this.reviveHealPrompt = { amount: parsed };
            this.reviveHealError = null;
            this.directHp = this.currentHp;
            return true;
        },

        cancelReviveHealing() {
            if (this.reviveHealBusy) return;
            this.reviveHealPrompt = null;
            this.reviveHealError = null;
            this.directHp = this.currentHp;
        },

        async confirmReviveHealing() {
            if (this.reviveHealBusy || !this.reviveHealPrompt) return;
            const amount = this.reviveHealPrompt.amount;
            this.reviveHealBusy = true;
            this.reviveHealError = null;
            try {
                // 6 níveis de exaustão continuam fatais; cura de PV não os remove.
                if (this.exhaustionLevel >= 6) {
                    this.reviveHealError = 'É necessário reduzir a exaustão fatal antes de curar.';
                    return;
                }
                const saved = await this.changeHp(amount, true);
                if (saved === false) {
                    this.reviveHealError = 'Não foi possível salvar a cura. Tente novamente.';
                    return;
                }
                this.reviveHealPrompt = null;
                this.deathSequenceDismissed = false;
                this.clearDeathSequence();
                try { sessionStorage.removeItem('spellbound-death-ack-{{ (int) $character->id }}'); } catch (_error) {}
                this.syncSheetDeathVisual(false);
            } finally {
                this.reviveHealBusy = false;
            }
        },

        async changeHp(amount, reviveConfirmed = false) {
            if (amount > 0 && this.isDead && !reviveConfirmed) {
                this.requestReviveHealing(amount);
                return false;
            }

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
                const previousHp = this.currentHp;
                const nextHp = Math.min(this.effectiveMaxHp, previousHp + amount);
                const healedAmount = Math.max(0, nextHp - previousHp);
                if (healedAmount < 1) return true;
                const nextSuccesses = nextHp > 0 ? 0 : this.deathSaveSuccesses;
                const nextFailures = nextHp > 0 ? 0 : this.deathSaveFailures;

                // Salvar ANTES de alterar os PV locais. Erro não ressuscita
                // visualmente nem libera a temática cinza por engano.
                const healSaved = await this.persistCombat({
                    current_hp: nextHp,
                    death_save_successes: nextSuccesses,
                    death_save_failures: nextFailures,
                });
                if (!healSaved) return false;

                this.currentHp = nextHp;
                this.directHp = nextHp;
                this.ghostHp = nextHp;
                this.deathSaveSuccesses = nextSuccesses;
                this.deathSaveFailures = nextFailures;
                if (nextHp > 0) {
                    this.deathDrawerOpen = false;
                    this.deathRollResult = null;
                    this.deathByRoll = false;
                }
                this.playHpImpact('heal', healedAmount);
                this.playHpFeedback('heal', healedAmount);
                return true;
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

            if (this.isDead && targetHp > this.currentHp) {
                this.requestReviveHealing(targetHp - this.currentHp);
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

    x-init="clearOrphanDeathPanels()"

    x-effect="
        syncExhaustionGlobal(exhaustionLevel);
        syncDeathSaveAvailability(isDying)
    "

    @character-turn-state.window="syncDeathTurnState($event.detail)"
    @pointermove.window="moveDeathDieDrag($event); moveDeathPanelDrag($event)"
    @pointerup.window="releaseDeathDieDrag($event); endDeathPanelDrag()"
    @pointercancel.window="cancelDeathDieDrag(); endDeathPanelDrag()"
    @resize.window="clampDeathPanelPosition()"
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
                :class="{'sb-identity-deceased':isDead}"
                @click="if (isDead && $event.target.closest('.hero-data-status-strip')) reopenDeathSequence()"
                @keydown.enter="if (isDead && $event.target.classList.contains('hero-data-status-strip')) reopenDeathSequence()"
                x-effect="const strip=$el.querySelector('.hero-data-status-strip'); if(strip){strip.setAttribute('role',isDead?'button':'status');strip.tabIndex=isDead?0:-1;strip.title=isDead?'Falecido · Rever despedida':'';}"
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
         REABRIR GAVETA — sem aumentar a altura do cabeçalho
    ============================================================= --}}
    <div
        x-show="isDying && !isStable && !isDead && deathSaveSuccesses < deathSuccessLimit && deathSaveFailures < deathFailureLimit"

        x-cloak
        x-transition
        data-theme="{{ $characterTheme }}"

        class="
            death-tab-enter
            sb-death-compact-tab
            character-sheet-theme
            absolute
            z-50

            mx-auto
            mt-2
            w-fit
        "
    >

        <button
            type="button"

            @click="deathDrawerOpen ? closeDeathDrawer() : openDeathDrawer()"

             :class="{'is-open':deathDrawerOpen}"
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

            <span x-text="morquenRuleActive ? 'Cicatrizes' : 'Último fôlego'"></span>
            <span class="sb-death-tab-count" x-text="deathSaveSuccesses+' / '+deathSaveFailures"></span>

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




    {{-- Confirmação obrigatória: cura em personagem já falecido. --}}
    <template x-teleport="body">
        <div x-show="reviveHealPrompt !== null" x-cloak
             class="sb-revive-confirm-layer character-sheet-theme"
             data-theme="{{ $characterTheme }}"
             role="dialog" aria-modal="true" aria-labelledby="sb-revive-confirm-title"
             @keydown.escape.window="if (reviveHealPrompt && !reviveHealBusy) cancelReviveHealing()">
            <div class="sb-revive-confirm-backdrop" @click="cancelReviveHealing()"></div>
            <div class="sb-revive-confirm-card">
                <div class="sb-revive-confirm-seal" aria-hidden="true">✝</div>
                <p class="sb-revive-confirm-overline">Cura de personagem falecido</p>
                <h2 id="sb-revive-confirm-title">Deseja reviver o personagem?</h2>
                <p class="sb-revive-confirm-copy">A cura irá devolver <strong x-text="reviveHealPrompt?.amount ?? 0"></strong> PV e limpar os salvamentos contra a morte.</p>
                <p class="sb-revive-confirm-error" x-show="reviveHealError" x-text="reviveHealError" role="alert"></p>
                <div class="sb-revive-confirm-actions">
                    <button type="button" class="sb-revive-confirm-cancel" @click="cancelReviveHealing()" :disabled="reviveHealBusy">Cancelar</button>
                    <button type="button" class="sb-revive-confirm-accept" @click="confirmReviveHealing()" :disabled="reviveHealBusy" x-text="reviveHealBusy?'Reviver…':'Sim, reviver'"></button>
                </div>
            </div>
        </div>
    </template>

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
                isDead &&
                !deathSequenceDismissed
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

            :class="{'death-save-last-breath':deathByRoll}"
            class="sb-death-finale
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
                    sb-death-final-card

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
                            sb-death-final-seal
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

                    <button
                        type="button"
                        @click="dismissDeathSequence()"
                        class="
                            absolute
                            right-5
                            top-5

                            rounded-full
                            border
                            border-white/10

                            bg-white/5

                            px-3
                            py-1.5

                            text-[9px]
                            font-black
                            uppercase
                            tracking-[0.14em]

                            text-[#d8c5bc]

                            transition
                            hover:border-[#d9b77b]/35
                            hover:bg-white/10
                            hover:text-white
                        "
                    >
                        Continuar na ficha
                    </button>


                    <p class="sb-death-final-eyebrow mt-4 text-[10px] font-black uppercase tracking-[0.28em] text-red-300">
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
                                    Reviver
                                </span>

                                <span
                                    x-show="
                                        deathReviving
                                    "
                                    x-cloak
                                >
                                    Revivendo...
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
         V66.2.179 — D20 manual, painel flutuante fora do fluxo do header.
         O componente visual usa a física única de SpellboundDiceFX.
    ============================================================= --}}
    <template x-teleport="body">
    {{-- O x-if desmonta o portal por completo ao recuperar PV / estabilizar.
         O x-show sozinho não basta quando Alpine mantém DOM teletransportado
         durante algumas navegações internas. --}}
    <template x-if="deathSavePanelVisible()">
    <section
        x-show="deathSavePanelVisible()"
        :data-death-visible="deathSavePanelVisible() ? 'true' : 'false'"
        x-cloak
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="sb-death-strip sb-death-floating character-sheet-theme"
        data-theme="{{ $characterTheme }}"
        x-ref="deathFloatingPanel"
        :style="deathPanelStyle()"
        x-init="restoreDeathPanelPosition()"
        aria-label="Salvamentos contra a morte"
    >
        <div class="sb-death-strip-inner">
            <div class="sb-death-strip-heading" @pointerdown="beginDeathPanelDrag($event)" :class="{'is-moving':!!deathPanelDragging}">
                <div class="sb-death-strip-identity">
                    <div>
                        <strong x-text="morquenRuleActive ? 'Cicatrizes do Passado' : 'Último fôlego'">Salvamentos contra a morte</strong>
                        <p class="sb-death-strip-subtitle"
                           x-text="deathRolling ? 'Rolando…' : deathTurnSeen && deathTurnResolved ? 'Turno resolvido' : 'Arraste e solte o d20'"></p>
                    </div>
                </div>
                <button type="button" class="sb-death-strip-collapse" @click="closeDeathDrawer()"
                        :disabled="deathRolling" aria-label="Recolher salvamentos" title="Recolher">
                    <span aria-hidden="true">−</span>
                </button>
            </div>
            <div class="sb-death-strip-action">
                <div class="sb-death-track is-success" aria-label="Sucessos">
                    <div class="sb-death-track-head"><span>Sucessos</span><strong x-text="deathSaveSuccesses + '/' + deathSuccessLimit"></strong></div>
                    <div class="sb-death-track-pips">
                        <template x-for="n in deathSuccessLimit" :key="'sb-ds-ok-'+n">
                            <button type="button" :disabled="deathRolling" @click="setDeathSave('success',n)"
                                    class="sb-death-pip" :class="n <= deathSaveSuccesses ? 'is-filled' : ''"
                                    :aria-label="'Ajustar sucessos para '+n"><span x-show="n <= deathSaveSuccesses" aria-hidden="true">✓</span></button>
                        </template>
                    </div>
                </div>

                <div class="sb-death-die-home">
                    <button type="button" x-ref="deathSaveDie" class="sb-death-die-launch"
                            :class="{'is-busy':deathRolling, 'is-grabbed':!!deathDieDrag}"
                            :disabled="deathRolling || deathRollPendingResult!==null || (deathTurnSeen && deathTurnResolved)"
                            @pointerdown.stop.prevent="beginDeathDieDrag($event)"
                            :style="deathDieStyle()"
                            aria-label="Arrastar e lançar o d20 de salvamento contra a morte" title="Segure e arremesse o d20 para realizar o salvamento">
                        <x-ui.poly-die :sides="20" size="100">
                            <span class="tabular-nums" x-text="deathRollResult === null ? '?' : String(deathRollResult)"></span>
                        </x-ui.poly-die>
                    </button>
                    <span class="sb-death-die-home-hint" x-show="deathRolling" x-cloak>Rolando…</span>
                </div>

                <div class="sb-death-track is-failure" aria-label="Falhas">
                    <div class="sb-death-track-head"><span>Falhas</span><strong x-text="deathSaveFailures + '/' + deathFailureLimit"></strong></div>
                    <div class="sb-death-track-pips">
                        <template x-for="n in deathFailureLimit" :key="'sb-ds-fail-'+n">
                            <button type="button" :disabled="deathRolling" @click="setDeathSave('failure',n)"
                                    class="sb-death-pip" :class="n <= deathSaveFailures ? 'is-filled' : ''"
                                    :aria-label="'Ajustar falhas para '+n"><span x-show="n <= deathSaveFailures" aria-hidden="true">×</span></button>
                        </template>
                    </div>
                </div>
            </div>
            <div class="sb-death-strip-feedback" aria-live="polite">
                <span x-show="deathRollResult!==null && !deathRollError" x-cloak
                      :class="deathRollResult===20?'is-gold':deathRollResult===1?'is-red':''"
                      x-text="deathRollResult===20?'20 natural · +1 PV':deathRollResult===1?'1 natural · +2 falhas':((deathRollResult-exhaustionRollPenalty)>=10?'Sucesso':'Falha')"></span>
                <span x-show="morquenRuleActive && !deathRollError && deathRollResult===null" x-cloak>Regra Morquen ativa</span>
                <span x-show="deathRollError" x-cloak class="is-error" role="alert" x-text="deathRollError"></span>
                <button type="button" x-show="deathRollPendingResult!==null && !deathRolling" x-cloak
                        class="sb-death-save-retry" @click="commitDeathSaveRoll()">Salvar resultado</button>
            </div>
        </div>
    </section>
    </template>
    </template>


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