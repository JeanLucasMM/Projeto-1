@props([
    'character',
    'proficiencyBonus' => 2,
    'exhaustionLevel' => 0,
])

@php
    $combat = $character->combat;
    $abilities = $character->abilities;

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS EFETIVOS
    |--------------------------------------------------------------------------
    |
    | Quick Stats usa os valores mecânicos atuais, incluindo overrides
    | e bônus temporários.
    |
    */

    $temporaryBonuses =
        is_array($abilities?->temporary_bonuses ?? null)
            ? $abilities->temporary_bonuses
            : [];

    $abilityOverrides =
        is_array($abilities?->overrides ?? null)
            ? $abilities->overrides
            : [];

    $resolveAbilityScore =
        static function (
            string $key,
            string $shortKey
        ) use (
            $abilities,
            $temporaryBonuses,
            $abilityOverrides
        ): int {
            $base =
                (int) data_get(
                    $abilities,
                    $key,
                    10
                );

            $temporary =
                (int) (
                    $temporaryBonuses[$key]
                    ?? $temporaryBonuses[$shortKey]
                    ?? 0
                );

            $override =
                $abilityOverrides[$key]
                ?? $abilityOverrides[$shortKey]
                ?? null;

            if (
                $override !== null
                &&
                $override !== ''
            ) {
                return max(
                    1,
                    (int) $override
                );
            }

            return max(
                1,
                $base + $temporary
            );
        };

    $strengthScore =
        $resolveAbilityScore(
            'strength',
            'str'
        );

    $dexterityScore =
        $resolveAbilityScore(
            'dexterity',
            'dex'
        );

    $wisdomScore =
        $resolveAbilityScore(
            'wisdom',
            'wis'
        );

    $strengthModifier =
        (int) floor(
            ($strengthScore - 10) / 2
        );

    $dexterityModifier =
        (int) floor(
            ($dexterityScore - 10) / 2
        );

    $wisdomModifier =
        (int) floor(
            ($wisdomScore - 10) / 2
        );

    /*
    |--------------------------------------------------------------------------
    | OVERRIDES / QUICK STATS
    |--------------------------------------------------------------------------
    */

    $overrides =
        $combat?->overrides
        ?? [];

    if (is_string($overrides)) {
        $decodedOverrides =
            json_decode(
                $overrides,
                true
            );

        $overrides =
            is_array($decodedOverrides)
                ? $decodedOverrides
                : [];
    }

    if (!is_array($overrides)) {
        $overrides = [];
    }

    $quickStats =
        data_get(
            $overrides,
            'quick_stats',
            []
        );

    if (!is_array($quickStats)) {
        $quickStats = [];
    }

    /*
    |--------------------------------------------------------------------------
    | INICIATIVA
    |--------------------------------------------------------------------------
    */

    $initiativeConfig =
        $quickStats['initiative']
        ?? [];

    $storedInitiativeMode =
        $initiativeConfig['mode']
        ?? 'none';

    $initiativeMode =
        in_array(
            $storedInitiativeMode,
            [
                'none',
                'proficient',
                'expertise',
            ],
            true
        )
            ? $storedInitiativeMode
            : 'none';

    /*
    | Se o personagem já tinha initiative_bonus salvo antes deste componente,
    | tentamos preservar um valor customizado diferente de zero.
    */

    $legacyInitiative =
        (int) (
            $combat?->initiative_bonus
            ?? 0
        );

    $initiativeBonus =
        array_key_exists(
            'bonus',
            $initiativeConfig
        )
            ? (int) $initiativeConfig['bonus']
            : (
                $legacyInitiative !== 0
                    ? (
                        $legacyInitiative
                        - $dexterityModifier
                    )
                    : 0
            );

    $initiativeCombatValue =
        array_key_exists(
            'combat_value',
            $initiativeConfig
        )
        &&
        $initiativeConfig['combat_value'] !== null
        &&
        $initiativeConfig['combat_value'] !== ''
            ? (int) $initiativeConfig['combat_value']
            : null;

    /*
    |--------------------------------------------------------------------------
    | DESLOCAMENTO / SALTOS
    |--------------------------------------------------------------------------
    */

    $movementConfig =
        $quickStats['movement']
        ?? [];

    $storedSpeed =
        max(
            0,
            (int) (
                $combat?->speed
                ?? 30
            )
        );

    $movementBase =
        max(
            0,
            (int) (
                $movementConfig['base']
                ?? (
                    $storedSpeed > 0
                        ? $storedSpeed
                        : 30
                )
            )
        );

    $movementBonus =
        (int) (
            $movementConfig['bonus']
            ?? 0
        );

    // Deslocamentos especiais são opcionais e vivem em overrides.quick_stats.
    // 0 significa que o personagem não possui aquele deslocamento.
    $movementTypes = is_array($movementConfig['types'] ?? null)
        ? $movementConfig['types']
        : [];

    $movementTypeSpeeds = [];
    foreach (['climb', 'swim', 'fly', 'burrow'] as $movementType) {
        $movementTypeSpeeds[$movementType] = max(
            0,
            min(999, (int) ($movementTypes[$movementType] ?? 0))
        );
    }

    $movementHover = (bool) ($movementConfig['hover'] ?? false);


    $jumpEnabled =
        (bool) (
            $movementConfig['jump_enabled']
            ?? true
        );

    $jumpHorizontalBonus =
        (int) (
            $movementConfig[
                'jump_horizontal_bonus'
            ]
            ?? 0
        );

    $jumpVerticalBonus =
        (int) (
            $movementConfig[
                'jump_vertical_bonus'
            ]
            ?? 0
        );

    /*
    |--------------------------------------------------------------------------
    | PERCEPÇÃO PASSIVA
    |--------------------------------------------------------------------------
    |
    | Regra:
    |
    | 10 + modificador de Percepção + bônus adicional.
    |
    | Procuramos uma perícia "Perception / Percepção" caso o modelo possua
    | essa relação. Se não houver, o modificador de Percepção é SAB.
    |
    */

    $characterSkills =
        collect(
            $character->skills
            ?? []
        );

    $perceptionSkill =
        $characterSkills->first(
            function ($skill) {
                $candidates = [
                    data_get(
                        $skill,
                        'key'
                    ),

                    data_get(
                        $skill,
                        'slug'
                    ),

                    data_get(
                        $skill,
                        'name'
                    ),

                    data_get(
                        $skill,
                        'skill'
                    ),

                    data_get(
                        $skill,
                        'skill.key'
                    ),

                    data_get(
                        $skill,
                        'skill.slug'
                    ),

                    data_get(
                        $skill,
                        'skill.name'
                    ),
                ];

                foreach ($candidates as $candidate) {
                    $normalized =
                        mb_strtolower(
                            trim(
                                (string) $candidate
                            )
                        );

                    if (
                        in_array(
                            $normalized,
                            [
                                'perception',
                                'percepção',
                                'percepcao',
                            ],
                            true
                        )
                    ) {
                        return true;
                    }
                }

                return false;
            }
        );

    $directPerceptionModifier =
        data_get(
            $perceptionSkill,
            'modifier'
        )
        ?? data_get(
            $perceptionSkill,
            'total_modifier'
        )
        ?? data_get(
            $perceptionSkill,
            'calculated_modifier'
        );

    if (
        $directPerceptionModifier !== null
        &&
        is_numeric(
            $directPerceptionModifier
        )
    ) {
        $perceptionSkillModifier =
            (int) $directPerceptionModifier;
    } else {
        $perceptionProficient =
            (bool) (
                data_get(
                    $perceptionSkill,
                    'proficient'
                )
                ?? data_get(
                    $perceptionSkill,
                    'enabled'
                )
                ?? data_get(
                    $perceptionSkill,
                    'pivot.proficient'
                )
                ?? false
            );

        $perceptionExpertise =
            (bool) (
                data_get(
                    $perceptionSkill,
                    'expertise'
                )
                ?? data_get(
                    $perceptionSkill,
                    'pivot.expertise'
                )
                ?? false
            );

        $perceptionExtraBonus =
            (int) (
                data_get(
                    $perceptionSkill,
                    'bonus'
                )
                ?? data_get(
                    $perceptionSkill,
                    'extra_bonus'
                )
                ?? data_get(
                    $perceptionSkill,
                    'pivot.bonus'
                )
                ?? 0
            );

        $perceptionSkillModifier =
            $wisdomModifier
            +
            (
                $perceptionExpertise
                    ? (
                        (int) $proficiencyBonus
                        * 2
                    )
                    : (
                        $perceptionProficient
                            ? (int) $proficiencyBonus
                            : 0
                    )
            )
            +
            $perceptionExtraBonus;
    }

    $perceptionConfig =
        $quickStats['passive_perception']
        ?? [];

    $passivePerceptionBonus =
        (int) (
            $perceptionConfig['bonus']
            ?? 0
        );


    $combatCallEnabled =
        auth()->check()
        &&
        session('dashboard_mode') === 'player'
        &&
        (int) auth()->id() === (int) $character->user_id;

    $combatCallConfig = [
        'enabled' => $combatCallEnabled,
        'status_url' => $combatCallEnabled
            ? route('characters.combat-call.show', $character)
            : null,
        'respond_url' => $combatCallEnabled
            ? route('characters.combat-call.respond', $character)
            : null,
        'initiative_url' => $combatCallEnabled
            ? route('characters.combat-call.initiative', $character)
            : null,
    ];
    /*
    |--------------------------------------------------------------------------
    | TEMA ATUAL
    |--------------------------------------------------------------------------
    |
    | Os editores usam x-teleport="body" e precisam reaplicar o tema.
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
            /*
            |--------------------------------------------------------------------------
            | HERO QUICK STATS V2 — PADRÃO VISUAL UNIFICADO
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stats-bar {
                height: 60px !important;
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
                box-shadow:
                    var(--theme-module-shadow, 0 3px 12px rgba(0,0,0,.05)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stat-item {
                border-color: var(--theme-border-soft) !important;
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stat-item:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stat-item
            > span:first-of-type {
                font-size: 8px !important;
                line-height: 1 !important;
                letter-spacing: .08em !important;
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stat-item
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stat-item
            [class*="text-[#6b1d14]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stat-item
            [class*="text-[#8c6239]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-v2
            .hero-quick-stats-bar[data-current-turn="true"] {
                border-color: var(--theme-accent) !important;
                box-shadow:
                    inset 0 0 0 1px
                    color-mix(in srgb, var(--theme-accent) 18%, transparent),
                    var(--theme-module-shadow, 0 3px 12px rgba(0,0,0,.05)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal {
                display: flex;
                width: min(560px, calc(100vw - 28px)) !important;
                max-width: 560px !important;
                max-height: min(92dvh, 860px) !important;
                flex-direction: column;
                overflow: hidden;
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;
                border-radius: 18px !important;
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color:
                    var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow:
                    var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.30)) !important;
            }

            @supports not (height: 1dvh) {
                .character-sheet-theme[data-theme]
                .hero-quick-stats-modal {
                    max-height: min(92vh, 860px) !important;
                }
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            > div:first-child {
                flex: 0 0 auto;
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;
                background:
                    var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            /* Sem o reflexo branco superior nos modais de Quick Stats. */
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            > div:first-child {
                box-shadow: none !important;
                background-image: none !important;
            }

            /* Controles extras de deslocamento vinculados aos tokens do tema. */
            .character-sheet-theme[data-theme]
            .hero-quick-movement-card {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-movement-muted {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-movement-switch {
                border-color: var(--theme-border-strong) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-movement-switch.is-active {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-movement-input {
                border: 1px solid var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-movement-input:focus {
                outline: none !important;
                border-color: var(--theme-input-focus) !important;
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--theme-input-focus) 12%, transparent) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-movement-heading {
                color: var(--theme-text-strong) !important;
            }


            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            > div:not(:first-child) {
                min-height: 0;
                overflow-y: auto;
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                scrollbar-width: thin;
                scrollbar-color:
                    color-mix(in srgb, var(--theme-label) 30%, transparent)
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            h3,
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            h4 {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#6b1d14]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#4a1813]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#3c120a]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#93623d]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#755847]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#80604b]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="text-[#a1846b]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="border-[#cdbb9f]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="border-[#d8c7ab]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="border-[#a0774d]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="border-[#93623d]"] {
                border-color: var(--theme-border) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="bg-[#f4f1e8]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="bg-[#f3e7d8]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="bg-[#efe9dc]"] {
                background: var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="bg-[#faf8f2]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="bg-[#fffdf8]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            [class*="bg-white"] {
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            input,
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            select {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            input:focus,
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            select:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow:
                    0 0 0 3px
                    color-mix(in srgb, var(--theme-input-focus) 15%, transparent) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            button[class*="bg-[#6b1d14]"] {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            button[class*="bg-[#6b1d14]"]:hover {
                background: var(--theme-button-hover, var(--theme-accent-deep)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            button[class*="bg-[#efe9dc]"],
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal
            button[class*="bg-[#e8dfd1]"] {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-close {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-close:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal-footer {
                position: sticky;
                bottom: 0;
                z-index: 5;
                margin-inline: -16px;
                margin-bottom: -16px;
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;
                background:
                    var(--theme-modal-header-background, var(--theme-band-background)) !important;
                padding: 12px 16px 14px !important;
                box-shadow:
                    0 -8px 20px
                    color-mix(in srgb, var(--theme-accent-deep) 4%, transparent);
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal-footer
            > button:first-child {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal-footer
            > button:last-child {
                border: 1px solid var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-combat-call-modal {
                width: min(500px, calc(100vw - 28px)) !important;
                max-width: 500px !important;
            }

            .character-sheet-theme[data-theme]
            .hero-combat-call-countdown {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            @media (max-width: 560px) {
                .character-sheet-theme[data-theme]
                .hero-quick-stats-modal {
                    width: calc(100vw - 20px) !important;
                    border-radius: 15px !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .hero-quick-stats-modal {
                    transition-duration: 1ms !important;
                }
            }

            /* V32 — Conteúdo rolável e rodapé independente. O rodapé nunca
               cobre Pairar nem Saltos, inclusive em resoluções baixas. */
            .character-sheet-theme[data-theme] .hero-quick-movement-modal {
                width: min(540px, calc(100vw - 24px)) !important;
                max-width: 540px !important;
                max-height: min(90dvh, 735px) !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal > .hero-quick-movement-scroll {
                min-height: 0 !important;
                flex: 1 1 auto !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
                overscroll-behavior: contain;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                scrollbar-width: thin;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal > .hero-quick-movement-footer {
                position: relative !important;
                bottom: auto !important;
                margin: 0 !important;
                flex: 0 0 auto !important;
                min-height: auto !important;
                overflow: visible !important;
                padding: 12px 16px !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-movement-option {
                border-color: var(--theme-border-soft) !important;
                background: var(--theme-surface-raised) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-movement-modal .hero-quick-movement-switch {
                min-height: 32px;
                cursor: pointer;
            }
            @media (max-height: 600px) {
                .character-sheet-theme[data-theme] .hero-quick-movement-modal {
                    max-height: calc(100dvh - 18px) !important;
                }
            }

            /* V33 — Superfície leve: menos caixas, espaçamento consistente. */
            .character-sheet-theme[data-theme] .hero-quick-movement-modal {
                width: min(520px, calc(100vw - 24px)) !important;
                max-width: 520px !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: min(90dvh, 710px) !important;
                padding: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal > .hero-quick-movement-scroll {
                width: 100%;
                min-height: 0 !important;
                flex: 1 1 auto !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-section {
                border-color: var(--theme-border-soft, var(--theme-border)) !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-speed-row:hover {
                background: var(--theme-card-hover, var(--theme-surface-raised)) !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-speed-label {
                color: var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-speed-dot {
                border-color: var(--theme-border-strong, var(--theme-border)) !important;
                color: var(--theme-label) !important;
                background: var(--theme-surface-raised) !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-speed-dot.is-on {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-quiet-toggle {
                border-color: var(--theme-border) !important;
                color: var(--theme-text-secondary) !important;
                background: transparent !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-quiet-toggle.is-on {
                border-color: var(--theme-accent) !important;
                color: var(--theme-text-strong) !important;
                background: var(--theme-accent-softer) !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-adjust {
                color: var(--theme-label) !important;
                border: 0 !important;
                background: none !important;
                padding: 6px !important;
            }
            .character-sheet-theme[data-theme]
            .hero-quick-movement-modal .hero-movement-adjust:hover {
                color: var(--theme-text-strong) !important;
            }
            /* Anula a regra legada sticky do footer e o scroll herdado
               pelo seletor genérico > div:not(:first-child). */
            .character-sheet-theme[data-theme]
            .hero-quick-stats-modal.hero-quick-movement-modal > .hero-quick-stats-modal-footer.hero-quick-movement-footer {
                position: relative !important;
                inset: auto !important;
                flex: 0 0 auto !important;
                width: 100% !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 11px 16px 12px !important;
                overflow: visible !important;
                border-top: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
                border-bottom: 0 !important;
                border-radius: 0 !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
                box-shadow: none !important;
            }
            @media (max-width: 520px) {
                .character-sheet-theme[data-theme]
                .hero-quick-movement-modal .hero-movement-section {
                    min-width: 0;
                }
            }

            /* V34 — Iniciativa e Percepção: mesma linguagem limpa de Movimento. */
            .character-sheet-theme[data-theme] .hero-quick-editor-modal {
                width: min(500px, calc(100vw - 24px)) !important;
                max-width: 500px !important;
                max-height: min(90dvh, 710px) !important;
                min-height: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-modal > .hero-quick-editor-header {
                flex: 0 0 auto;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
                border-bottom: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
                box-shadow: none !important;
                background-image: none !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-modal > .hero-quick-editor-scroll {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
                padding: 16px 20px !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-modal .hero-quick-editor-section + .hero-quick-editor-section {
                border-top: 1px solid var(--theme-border-soft, var(--theme-border));
                margin-top: 14px;
                padding-top: 14px;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-heading {
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-muted {
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-label {
                color: var(--theme-label) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-input {
                background: var(--theme-input-bg) !important;
                border: 1px solid var(--theme-input-border) !important;
                color: var(--theme-input-text) !important;
                outline: 0;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-input:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--theme-input-focus) 13%, transparent) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-segments {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-segment {
                color: var(--theme-text-secondary) !important;
                background: transparent !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-segment + .hero-quick-editor-segment {
                border-left: 1px solid var(--theme-border-soft, var(--theme-border));
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-segment[aria-pressed="true"] {
                color: var(--theme-button-text) !important;
                background: var(--theme-button-background) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-button {
                color: var(--theme-text-strong) !important;
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-button:hover:not(:disabled) {
                background: var(--theme-card-hover) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-primary {
                color: var(--theme-button-text) !important;
                background: var(--theme-button-background) !important;
                border: 1px solid var(--theme-accent-strong) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-editor-primary:hover:not(:disabled) {
                background: var(--theme-button-hover-background, var(--theme-button-background)) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-stats-modal.hero-quick-editor-modal > .hero-quick-stats-modal-footer.hero-quick-editor-footer {
                flex: 0 0 auto !important;
                position: relative !important;
                inset: auto !important;
                min-height: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 12px 18px !important;
                border: 0 !important;
                border-top: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
                border-radius: 0 !important;
                overflow: visible !important;
                box-shadow: none !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }
            .character-sheet-theme[data-theme] .hero-quick-movement-caption {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
                white-space: normal;
                overflow-wrap: anywhere;
            }

        </style>
    @endpush
@endonce

@once
    <script>
        document.addEventListener(
            'alpine:init',
            () => {
                Alpine.data(
                    'heroQuickStats',
                    (
                        proficiencyBonus,
                        dexterityModifier,
                        strengthScore,
                        strengthModifier,
                        perceptionSkillModifier,
                        initialExhaustionLevel,
                        initialConfig,
                        initialOverrides,
                        persistCombatCallback,
                        rollUrl,
                        combatCallConfig
                    ) => ({
                        proficiencyBonus:
                            parseInt(
                                proficiencyBonus
                            ) || 0,

                        dexterityModifier:
                            parseInt(
                                dexterityModifier
                            ) || 0,

                        strengthScore:
                            parseInt(
                                strengthScore
                            ) || 0,

                        strengthModifier:
                            parseInt(
                                strengthModifier
                            ) || 0,

                        perceptionSkillModifier:
                            parseInt(
                                perceptionSkillModifier
                            ) || 0,

                        exhaustionLevel:
                            Math.min(
                                6,
                                Math.max(
                                    0,
                                    parseInt(
                                        initialExhaustionLevel
                                    ) || 0
                                )
                            ),

                        combatOverrides:
                            (
                                initialOverrides
                                &&
                                typeof initialOverrides === 'object'
                                &&
                                !Array.isArray(initialOverrides)
                            )
                                ? { ...initialOverrides }
                                : {},

                        persistCombatCallback,
                        rollUrl,

                        combatCallEnabled:
                            !!combatCallConfig?.enabled,

                        combatCallStatusUrl:
                            combatCallConfig?.status_url
                            ?? null,

                        combatCallRespondUrl:
                            combatCallConfig?.respond_url
                            ?? null,

                        combatCallInitiativeUrl:
                            combatCallConfig?.initiative_url
                            ?? null,

                        combatCall: null,
                        combatCallSecondsLeft: null,
                        combatCallBusy: false,
                        combatCallRolling: false,
                        combatCallDie: null,
                        combatCallFinalInitiative: null,
                        combatCallRollingPreview: 20,
                        combatCallRollingTimer: null,

                        /*
                         * Proteção contra uma resposta atrasada do polling
                         * reabrir um chamado cuja iniciativa já foi enviada.
                         */
                        completedCombatCallRequestId: null,
                        combatCallError: null,
                        combatCallPollTimer: null,
                        combatCallCountdownTimer: null,

                        initiativeOpen: false,
                        movementOpen: false,
                        jumpDetailsOpen: false,
                        perceptionOpen: false,

                        saving: false,
                        rollingInitiative: false,

                        initiativeMode:
                            initialConfig?.initiative?.mode
                            ?? 'none',

                        initiativeBonus:
                            parseInt(
                                initialConfig?.initiative?.bonus
                            ) || 0,

                        combatInitiative:
                            initialConfig?.initiative?.combat_value === null
                            ||
                            initialConfig?.initiative?.combat_value === undefined
                            ||
                            initialConfig?.initiative?.combat_value === ''
                                ? null
                                : parseInt(
                                    initialConfig
                                        .initiative
                                        .combat_value
                                ),

                        lastInitiativeRoll: null,

                        movementBase:
                            Math.max(
                                0,
                                Number.isFinite(Number(initialConfig?.movement?.base))
                                && initialConfig?.movement?.base !== null
                                && initialConfig?.movement?.base !== undefined
                                && initialConfig?.movement?.base !== ''
                                    ? Math.trunc(Number(initialConfig.movement.base))
                                    : 30
                            ),

                        movementBonus:
                            parseInt(
                                initialConfig?.movement?.bonus
                            ) || 0,

                        movementTypes: {
                            climb: Math.max(0, Number(initialConfig?.movement?.types?.climb) || 0),
                            swim: Math.max(0, Number(initialConfig?.movement?.types?.swim) || 0),
                            fly: Math.max(0, Number(initialConfig?.movement?.types?.fly) || 0),
                            burrow: Math.max(0, Number(initialConfig?.movement?.types?.burrow) || 0),
                        },

                        movementHover: !!initialConfig?.movement?.hover,


                        jumpEnabled:
                            initialConfig
                                ?.movement
                                ?.jump_enabled
                                ?? true,

                        jumpHorizontalBonus:
                            parseInt(
                                initialConfig
                                    ?.movement
                                    ?.jump_horizontal_bonus
                            ) || 0,

                        jumpVerticalBonus:
                            parseInt(
                                initialConfig
                                    ?.movement
                                    ?.jump_vertical_bonus
                            ) || 0,

                        passivePerceptionBonus:
                            parseInt(
                                initialConfig
                                    ?.passive_perception
                                    ?.bonus
                            ) || 0,

                        /*
                        |--------------------------------------------------------------------------
                        | FUTURA INTEGRAÇÃO COM O COMBATE
                        |--------------------------------------------------------------------------
                        |
                        | Nenhuma ligação é feita aqui ainda.
                        |
                        | O componente apenas:
                        |
                        | 1. emite character-initiative-updated;
                        | 2. aceita character-turn-state.
                        |
                        */

                        isCurrentTurn: false,

                        get initiativeProficiencyBonus() {
                            if (
                                this.initiativeMode ===
                                'expertise'
                            ) {
                                return (
                                    this.proficiencyBonus
                                    * 2
                                );
                            }

                            if (
                                this.initiativeMode ===
                                'proficient'
                            ) {
                                return (
                                    this.proficiencyBonus
                                );
                            }

                            return 0;
                        },

                        get initiativeModifier() {
                            return (
                                this.dexterityModifier
                                +
                                this.initiativeProficiencyBonus
                                +
                                (
                                    parseInt(
                                        this.initiativeBonus
                                    ) || 0
                                )
                            );
                        },

                        get initiativeModifierLabel() {
                            return (
                                this.initiativeModifier >= 0
                                    ? '+'
                                    : ''
                            ) + this.initiativeModifier;
                        },

                        /*
                        |--------------------------------------------------------------------------
                        | VALOR EXIBIDO NO HEADER
                        |--------------------------------------------------------------------------
                        |
                        | O Header mostra sempre o modificador REAL da iniciativa.
                        | O resultado rolado/manual de combate fica apenas no modal.
                        |
                        */

                        get initiativeDisplay() {
                            return this.initiativeModifierLabel;
                        },


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


                        syncExhaustion(payload) {
                            const level =
                                payload?.level
                                ?? 0;

                            this.exhaustionLevel =
                                Math.min(
                                    6,
                                    Math.max(
                                        0,
                                        parseInt(level) || 0
                                    )
                                );
                        },


                        get movementTotal() {
                            return Math.max(
                                0,
                                (
                                    parseInt(
                                        this.movementBase
                                    ) || 0
                                )
                                +
                                (
                                    parseInt(
                                        this.movementBonus
                                    ) || 0
                                )
                                -
                                this.exhaustionSpeedPenalty
                            );
                        },

                        movementTypeTotal(type) {
                            const speed = Math.max(0, parseInt(this.movementTypes?.[type]) || 0);
                            return speed > 0
                                ? Math.max(0, speed - this.exhaustionSpeedPenalty)
                                : 0;
                        },

                        toggleMovementType(type) {
                            if (!['climb', 'swim', 'fly', 'burrow'].includes(type)) {
                                return;
                            }

                            const previous = parseInt(this.movementTypes?.[type]) || 0;
                            this.movementTypes[type] = previous > 0
                                ? 0
                                : Math.max(5, this.movementBase || 30);

                            if (type === 'fly' && this.movementTypes.fly === 0) {
                                this.movementHover = false;
                            }
                        },

                        get movementOtherSummary() {
                            // Exibe os tipos com velocidade. Pairar pertence ao Voo,
                            // não é um quinto tipo de deslocamento independente.
                            const types = [
                                ['climb', 'Escalada'],
                                ['swim', 'Natação'],
                                ['fly', 'Voo'],
                                ['burrow', 'Escavação'],
                            ];

                            return types
                                .filter(([key]) => Number(this.movementTypes?.[key]) > 0)
                                .map(([key, label]) => {
                                    const speed = this.movementTypeTotal(key);
                                    const hover = key === 'fly' && this.movementHover
                                        ? ' (Pairar)'
                                        : '';
                                    return `${label} ${speed} ft${hover}`;
                                })
                                .join(' · ');
                        },

                        get movementHeaderSummary() {
                            // Preserva a leitura da Quick Stat quando há muitos modos.
                            // O modal e o title exibem a lista integral.
                            const visibleTypes = [
                                ['fly', 'Voo'],
                                ['climb', 'Escalada'],
                                ['swim', 'Natação'],
                                ['burrow', 'Escavação'],
                            ]
                                .filter(([key]) => Number(this.movementTypes?.[key]) > 0)
                                .map(([key, label]) => {
                                    const hover = key === 'fly' && this.movementHover ? ' (Pairar)' : '';
                                    return `${label} ${this.movementTypeTotal(key)} ft${hover}`;
                                });

                            if (visibleTypes.length <= 2) {
                                return visibleTypes.join(' · ');
                            }

                            return `${visibleTypes[0]} · +${visibleTypes.length - 1} tipos`;
                        },

                        /*
                        |--------------------------------------------------------------------------
                        | SALTOS
                        |--------------------------------------------------------------------------
                        |
                        | Com 10 ft de corrida:
                        |
                        | Horizontal = valor de FOR.
                        | Vertical   = 3 + modificador de FOR.
                        |
                        | Sem corrida, a distância é reduzida pela metade.
                        |
                        */

                        get horizontalJump() {
                            return Math.max(
                                0,
                                this.strengthScore
                                +
                                (
                                    parseInt(
                                        this.jumpHorizontalBonus
                                    ) || 0
                                )
                            );
                        },

                        get verticalJump() {
                            return Math.max(
                                0,
                                3
                                +
                                this.strengthModifier
                                +
                                (
                                    parseInt(
                                        this.jumpVerticalBonus
                                    ) || 0
                                )
                            );
                        },

                        get passivePerception() {
                            return Math.max(
                                0,
                                10
                                +
                                this.perceptionSkillModifier
                                +
                                (
                                    parseInt(
                                        this.passivePerceptionBonus
                                    ) || 0
                                )
                            );
                        },

                        get quickStatsPayload() {
                            return {
                                initiative: {
                                    mode:
                                        [
                                            'none',
                                            'proficient',
                                            'expertise',
                                        ].includes(
                                            this.initiativeMode
                                        )
                                            ? this.initiativeMode
                                            : 'none',

                                    bonus:
                                        parseInt(
                                            this.initiativeBonus
                                        ) || 0,

                                    combat_value:
                                        this.combatInitiative === null
                                        ||
                                        this.combatInitiative === ''
                                            ? null
                                            : (
                                                parseInt(
                                                    this.combatInitiative
                                                ) || 0
                                            ),
                                },

                                movement: {
                                    base:
                                        Math.max(
                                            0,
                                            parseInt(
                                                this.movementBase
                                            ) || 0
                                        ),

                                    bonus:
                                        parseInt(
                                            this.movementBonus
                                        ) || 0,

                                    types: {
                                        climb: Math.max(0, Math.min(999, parseInt(this.movementTypes.climb) || 0)),
                                        swim: Math.max(0, Math.min(999, parseInt(this.movementTypes.swim) || 0)),
                                        fly: Math.max(0, Math.min(999, parseInt(this.movementTypes.fly) || 0)),
                                        burrow: Math.max(0, Math.min(999, parseInt(this.movementTypes.burrow) || 0)),
                                    },

                                    hover: !!this.movementHover && Number(this.movementTypes.fly) > 0,

                                    jump_enabled:
                                        !!this.jumpEnabled,

                                    jump_horizontal_bonus:
                                        parseInt(
                                            this.jumpHorizontalBonus
                                        ) || 0,

                                    jump_vertical_bonus:
                                        parseInt(
                                            this.jumpVerticalBonus
                                        ) || 0,
                                },

                                passive_perception: {
                                    bonus:
                                        parseInt(
                                            this.passivePerceptionBonus
                                        ) || 0,
                                },
                            };
                        },

                        /*
                        |--------------------------------------------------------------
                        | CHAMADO DE COMBATE — não altera a Quick Stat existente
                        |--------------------------------------------------------------
                        */

                        init() {
                            if (
                                !this.combatCallEnabled
                                ||
                                !this.combatCallStatusUrl
                            ) {
                                return;
                            }

                            this.checkCombatCall();

                            this.combatCallPollTimer = window.setInterval(
                                () => this.checkCombatCall(),
                                2000
                            );

                            this.combatCallCountdownTimer = window.setInterval(
                                () => this.tickCombatCall(),
                                1000
                            );
                        },

                        destroy() {
                            if (this.combatCallPollTimer) {
                                window.clearInterval(this.combatCallPollTimer);
                            }

                            if (this.combatCallCountdownTimer) {
                                window.clearInterval(this.combatCallCountdownTimer);
                            }

                            if (this.combatCallRollingTimer) {
                                window.clearInterval(this.combatCallRollingTimer);
                                this.combatCallRollingTimer = null;
                            }
                        },

                        get combatCallCountdownLabel() {
                            const seconds = Math.max(
                                0,
                                parseInt(this.combatCallSecondsLeft) || 0
                            );

                            return `00:${String(seconds).padStart(2, '0')}`;
                        },

                        get combatCallEffectiveBonus() {
                            return (
                                this.initiativeModifier
                                -
                                this.exhaustionRollPenalty
                            );
                        },

                        get combatCallEffectiveBonusLabel() {
                            const value =
                                this.combatCallEffectiveBonus;

                            return (
                                value >= 0
                                    ? '+'
                                    : ''
                            ) + value;
                        },

                        get combatCallRolledTotal() {
                            const die =
                                parseInt(
                                    this.combatCallDie
                                );

                            if (
                                Number.isNaN(die)
                                ||
                                die < 1
                                ||
                                die > 20
                            ) {
                                return null;
                            }

                            return (
                                die
                                +
                                this.combatCallEffectiveBonus
                            );
                        },

                        /*
                         * A iniciativa FINAL é um valor separado do d20.
                         *
                         * - Ao rolar pelo Spellbound, ela é preenchida
                         *   automaticamente com d20 + bônus efetivo.
                         *
                         * - Se usar dado físico ou outra regra, o Player
                         *   informa diretamente o valor FINAL.
                         *
                         * - Nunca enviamos automaticamente após a rolagem.
                         */
                        get combatCallFinalValue() {
                            const value =
                                parseInt(
                                    this.combatCallFinalInitiative
                                );

                            if (
                                Number.isNaN(value)
                                ||
                                value < -100
                                ||
                                value > 200
                            ) {
                                return null;
                            }

                            return value;
                        },

                        async checkCombatCall() {
                            if (
                                !this.combatCallEnabled
                                ||
                                !this.combatCallStatusUrl
                                ||
                                document.hidden
                                ||
                                this.combatCallBusy
                                ||
                                this.combatCallRolling
                            ) {
                                return;
                            }

                            try {
                                const response = await fetch(
                                    this.combatCallStatusUrl,
                                    {
                                        method: 'GET',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        cache: 'no-store',
                                    }
                                );

                                if (response.status === 401 || response.status === 403) {
                                    this.destroy();
                                    return;
                                }

                                if (!response.ok) {
                                    return;
                                }

                                const data = await response.json();
                                let nextCall = data?.call ?? null;

                                /*
                                 * Um GET iniciado imediatamente antes do POST
                                 * da iniciativa pode terminar depois do POST e
                                 * ainda carregar o estado antigo
                                 * "accepted / initiative".
                                 *
                                 * Nesse caso ignoramos somente o MESMO
                                 * request_id já concluído. Um novo chamado,
                                 * com outro id, continua aparecendo normalmente.
                                 */
                                if (
                                    nextCall
                                    &&
                                    this.completedCombatCallRequestId !== null
                                    &&
                                    Number(
                                        nextCall.request_id
                                    )
                                    ===
                                    Number(
                                        this.completedCombatCallRequestId
                                    )
                                ) {
                                    nextCall =
                                        null;
                                }

                                const changedRequest =
                                    nextCall?.request_id !== this.combatCall?.request_id;

                                if (changedRequest) {
                                    this.combatCallDie =
                                        null;

                                    this.combatCallFinalInitiative =
                                        null;

                                    this.combatCallError =
                                        null;
                                }

                                this.combatCall = nextCall;
                                this.combatCallSecondsLeft =
                                    nextCall?.phase === 'join'
                                        ? (parseInt(nextCall.seconds_left) || 0)
                                        : null;
                            } catch (error) {
                                // polling silencioso
                            }
                        },

                        tickCombatCall() {
                            if (
                                this.combatCall?.phase !== 'join'
                                ||
                                this.combatCallBusy
                            ) {
                                return;
                            }

                            this.combatCallSecondsLeft = Math.max(
                                0,
                                (parseInt(this.combatCallSecondsLeft) || 0) - 1
                            );

                            if (this.combatCallSecondsLeft <= 0) {
                                this.respondCombatCall(false);
                            }
                        },

                        async respondCombatCall(accepted) {
                            if (
                                this.combatCall?.phase !== 'join'
                                ||
                                this.combatCallBusy
                            ) {
                                return;
                            }

                            this.combatCallBusy = true;
                            this.combatCallError = null;

                            try {
                                const response = await fetch(
                                    this.combatCallRespondUrl,
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document
                                                .querySelector('meta[name="csrf-token"]')
                                                ?.getAttribute('content')
                                                ?? '{{ csrf_token() }}',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        body: JSON.stringify({
                                            request_id: this.combatCall.request_id,
                                            accepted: !!accepted,
                                        }),
                                    }
                                );

                                const data = await response.json().catch(() => ({}));

                                if (!response.ok) {
                                    throw new Error(
                                        data?.message
                                        ?? 'Não foi possível responder ao combate.'
                                    );
                                }

                                this.combatCall =
                                    data?.call
                                    ?? null;

                                this.combatCallSecondsLeft =
                                    null;

                                this.combatCallDie =
                                    null;

                                this.combatCallFinalInitiative =
                                    null;
                            } catch (error) {
                                this.combatCallError =
                                    error?.message
                                    ?? 'Não foi possível responder ao combate.';
                            } finally {
                                this.combatCallBusy = false;
                            }
                        },

                        async rollCombatCallInitiative(mode = 'normal') {
                            if (
                                this.combatCall?.phase !== 'initiative'
                                ||
                                this.combatCallBusy
                                ||
                                this.combatCallRolling
                            ) {
                                return;
                            }

                            this.combatCallRolling = true;
                            this.combatCallError = null;
                            const sbStage = window.SpellboundDiceFX;
                            const choice = window.SpellboundD20Gestures.choice(mode);
                            const sbToken = sbStage?.start?.({
                                label:'Iniciativa · Convocação', expression:'1d20', kind:'initiative',
                                advantageMode:choice, reroll:next => this.rollCombatCallInitiative(next),
                            }) ?? null;

                            if (this.combatCallRollingTimer) {
                                window.clearInterval(
                                    this.combatCallRollingTimer
                                );
                            }

                            this.combatCallRollingPreview =
                                Math.floor(
                                    Math.random() * 20
                                ) + 1;

                            this.combatCallRollingTimer =
                                window.setInterval(
                                    () => {
                                        this.combatCallRollingPreview =
                                            Math.floor(
                                                Math.random() * 20
                                            ) + 1;
                                    },
                                    70
                                );

                            let roll = Math.floor(Math.random() * 20) + 1;
                            let combined = null;
                            const startedAt = Date.now();

                            if (choice !== 'normal') {
                                combined = await window.SpellboundD20Gestures.rollTwo({
                                    url:this.rollUrl, expression:'1d20', modifier:0, mode:choice, fallback:true,
                                    headers:{'Content-Type':'application/json','Accept':'application/json',
                                        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}',
                                        'X-Requested-With':'XMLHttpRequest'},
                                });
                                roll = combined.die;
                            } else {
                            try {
                                const response = await fetch(
                                    this.rollUrl,
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document
                                                .querySelector('meta[name="csrf-token"]')
                                                ?.getAttribute('content')
                                                ?? '{{ csrf_token() }}',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        body: JSON.stringify({ expression: '1d20' }),
                                    }
                                );

                                if (response.ok) {
                                    const result = await response.json();
                                    const apiValue =
                                        result?.data?.total
                                        ?? result?.data?.result
                                        ?? result?.data?.value
                                        ?? result?.data?.sum
                                        ?? result?.total
                                        ?? result?.result
                                        ?? null;

                                    if (
                                        apiValue !== null
                                        &&
                                        !Number.isNaN(parseInt(apiValue))
                                    ) {
                                        roll = Math.max(1, Math.min(20, parseInt(apiValue)));
                                    }
                                }
                            } catch (error) {
                                // mesmo fallback local já usado pela iniciativa atual
                            }
                            }

                            const elapsed = Date.now() - startedAt;

                            if (elapsed < 700) {
                                await new Promise(
                                    resolve => window.setTimeout(resolve, 700 - elapsed)
                                );
                            }

                            if (this.combatCallRollingTimer) {
                                window.clearInterval(
                                    this.combatCallRollingTimer
                                );

                                this.combatCallRollingTimer =
                                    null;
                            }

                            this.combatCallRollingPreview =
                                roll;

                            this.combatCallDie =
                                roll;

                            this.combatCallFinalInitiative =
                                (
                                    roll
                                    +
                                    this.combatCallEffectiveBonus
                                );

                            if (sbToken) await sbStage.finish(sbToken, {
                                label:'Iniciativa · Convocação', expression:'1d20',
                                rolls:combined?.rolls ?? [roll], natural:roll, total:this.combatCallFinalInitiative,
                                advantageMode:choice, selectedIndex:combined?.selectedIndex ?? null,
                                modifier:this.combatCallEffectiveBonus,
                            });
                            this.combatCallRolling =
                                false;

                            /*
                             * Importante:
                             * NÃO envia automaticamente.
                             *
                             * O Player precisa ver o d20, o bônus e a
                             * iniciativa final antes de confirmar.
                             */
                        },

                        async submitCombatCallInitiative() {
                            if (
                                this.combatCall?.phase !== 'initiative'
                                ||
                                this.combatCallBusy
                                ||
                                this.combatCallRolling
                            ) {
                                return;
                            }

                            const parsedDieRoll =
                                parseInt(
                                    this.combatCallDie
                                );

                            const dieRoll =
                                (
                                    Number.isNaN(
                                        parsedDieRoll
                                    )
                                    ||
                                    parsedDieRoll < 1
                                    ||
                                    parsedDieRoll > 20
                                )
                                    ? null
                                    : parsedDieRoll;

                            const finalInitiative =
                                this.combatCallFinalValue;

                            if (
                                finalInitiative === null
                            ) {
                                this.combatCallError =
                                    'Informe a iniciativa final entre -100 e 200.';

                                return;
                            }

                            this.combatCallBusy = true;
                            this.combatCallError = null;

                            const requestId =
                                this.combatCall.request_id;

                            try {
                                const response = await fetch(
                                    this.combatCallInitiativeUrl,
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document
                                                .querySelector('meta[name="csrf-token"]')
                                                ?.getAttribute('content')
                                                ?? '{{ csrf_token() }}',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        /*
                                         * Enviamos os dois formatos por
                                         * compatibilidade:
                                         *
                                         * - die_roll:
                                         *     controller novo calcula o total.
                                         *
                                         * - initiative:
                                         *     controllers antigos esperavam o
                                         *     total final já calculado.
                                         */
                                        body: JSON.stringify({
                                            request_id:
                                                requestId,

                                            die_roll:
                                                dieRoll,

                                            initiative:
                                                finalInitiative,
                                        }),
                                    }
                                );

                                const responseText =
                                    await response.text();

                                let data = {};

                                if (responseText) {
                                    try {
                                        data =
                                            JSON.parse(
                                                responseText
                                            );
                                    } catch (error) {
                                        data = {};
                                    }
                                }

                                if (!response.ok) {
                                    const validationMessage =
                                        data?.errors
                                            ? Object
                                                .values(
                                                    data.errors
                                                )
                                                .flat()
                                                .find(
                                                    message =>
                                                        !!message
                                                )
                                            : null;

                                    throw new Error(
                                        validationMessage
                                        ?? data?.message
                                        ?? (
                                            `Não foi possível enviar a iniciativa. HTTP ${response.status}.`
                                        )
                                    );
                                }

                                const total =
                                    parseInt(
                                        data?.total
                                        ?? finalInitiative
                                    );

                                if (!Number.isNaN(total)) {
                                    this.combatInitiative =
                                        total;

                                    this.lastInitiativeRoll =
                                        dieRoll;

                                    this.emitInitiativeChange(
                                        'combat-call'
                                    );
                                }

                                this.completedCombatCallRequestId =
                                    requestId;

                                this.combatCall =
                                    null;

                                this.combatCallDie =
                                    null;

                                this.combatCallFinalInitiative =
                                    null;

                                this.combatCallSecondsLeft =
                                    null;
                            } catch (error) {
                                this.combatCallError =
                                    error?.message
                                    ?? 'Não foi possível enviar a iniciativa.';
                            } finally {
                                this.combatCallBusy = false;
                            }
                        },

                        closeAll() {
                            this.initiativeOpen = false;
                            this.movementOpen = false;
                            this.perceptionOpen = false;
                        },

                        syncTurnState(payload) {
                            if (!payload) {
                                return;
                            }

                            this.isCurrentTurn =
                                !!payload.isCurrentTurn;
                        },

                        emitInitiativeChange(
                            source = 'manual'
                        ) {
                            window.dispatchEvent(
                                new CustomEvent(
                                    'character-initiative-updated',
                                    {
                                        detail: {
                                            value:
                                                this.combatInitiative,

                                            modifier:
                                                this.initiativeModifier,

                                            source,
                                        },
                                    }
                                )
                            );
                        },

                        async persist(
                            closeAfterSave = true
                        ) {
                            if (
                                this.saving
                                ||
                                typeof this.persistCombatCallback !==
                                    'function'
                            ) {
                                return false;
                            }

                            this.saving = true;

                            try {
                                const nextOverrides = {
                                    ...this.combatOverrides,

                                    quick_stats:
                                        this.quickStatsPayload,
                                };

                                const response =
                                    await this.persistCombatCallback({
                                        speed:
                                            this.movementTotal,

                                        initiative_bonus:
                                            this.initiativeModifier,

                                        overrides:
                                            JSON.stringify(
                                                nextOverrides
                                            ),
                                    });

                                if (!response) {
                                    throw new Error(
                                        'Não foi possível salvar os dados rápidos.'
                                    );
                                }

                                this.combatOverrides =
                                    nextOverrides;

                                if (closeAfterSave) {
                                    this.closeAll();
                                }

                                return true;

                            } catch (error) {
                                console.error(
                                    'Erro ao salvar dados rápidos:',
                                    error
                                );

                                return false;

                            } finally {
                                this.saving = false;
                            }
                        },

                        async rollInitiative(mode = 'normal') {
                            if (
                                this.rollingInitiative
                                ||
                                this.saving
                            ) {
                                return;
                            }

                            this.rollingInitiative =
                                true;
                            const sbStage = window.SpellboundDiceFX;
                            const choice = window.SpellboundD20Gestures.choice(mode);
                            const sbToken = sbStage?.start?.({
                                label:'Iniciativa', expression:'1d20', kind:'initiative',
                                advantageMode:choice, reroll:next => this.rollInitiative(next),
                            }) ?? null;

                            let roll =
                                Math.floor(
                                    Math.random() * 20
                                ) + 1;
                            let combined = null;

                            if (choice !== 'normal') {
                                combined = await window.SpellboundD20Gestures.rollTwo({
                                    url:this.rollUrl, expression:'1d20', modifier:0, mode:choice, fallback:true,
                                    headers:{'Content-Type':'application/json','Accept':'application/json',
                                        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}',
                                        'X-Requested-With':'XMLHttpRequest'},
                                });
                                roll = combined.die;
                            } else {
                            try {
                                const response =
                                    await fetch(
                                        this.rollUrl,
                                        {
                                            method: 'POST',

                                            headers: {
                                                'Content-Type':
                                                    'application/json',

                                                'Accept':
                                                    'application/json',

                                                'X-CSRF-TOKEN':
                                                    document
                                                        .querySelector(
                                                            'meta[name="csrf-token"]'
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
                                                    expression:
                                                        '1d20',
                                                }),
                                        }
                                    );

                                if (response.ok) {
                                    const result =
                                        await response.json();

                                    const apiValue =
                                        result?.data?.total
                                        ?? result?.data?.result
                                        ?? result?.data?.value
                                        ?? result?.data?.sum
                                        ?? result?.total
                                        ?? result?.result
                                        ?? null;

                                    if (
                                        apiValue !== null
                                        &&
                                        !Number.isNaN(
                                            parseInt(
                                                apiValue
                                            )
                                        )
                                    ) {
                                        roll =
                                            parseInt(
                                                apiValue
                                            );
                                    }
                                }

                            } catch (error) {
                                console.warn(
                                    'API de dados indisponível. Usando rolagem local.',
                                    error
                                );
                            }
                            }

                            this.lastInitiativeRoll =
                                roll;

                            this.combatInitiative =
                                roll
                                +
                                this.initiativeModifier
                                -
                                this.exhaustionRollPenalty;

                            if (sbToken) await sbStage.finish(sbToken, {
                                label:'Iniciativa', expression:'1d20',
                                rolls:combined?.rolls ?? [roll], natural:roll, total:this.combatInitiative,
                                advantageMode:choice, selectedIndex:combined?.selectedIndex ?? null,
                                modifier:this.initiativeModifier - this.exhaustionRollPenalty,
                            });

                            const success =
                                await this.persist(
                                    false
                                );

                            if (success) {
                                this.emitInitiativeChange(
                                    'roll'
                                );
                            }

                            this.rollingInitiative =
                                false;
                        },

                        async saveManualInitiative() {
                            if (
                                this.combatInitiative === ''
                            ) {
                                this.combatInitiative =
                                    null;
                            }

                            if (
                                this.combatInitiative !== null
                            ) {
                                const parsed =
                                    parseInt(
                                        this.combatInitiative
                                    );

                                this.combatInitiative =
                                    Number.isNaN(parsed)
                                        ? null
                                        : parsed;
                            }

                            const success =
                                await this.persist(
                                    false
                                );

                            if (success) {
                                this.emitInitiativeChange(
                                    'manual'
                                );
                            }
                        },

                        async clearCombatInitiative() {
                            this.combatInitiative =
                                null;

                            this.lastInitiativeRoll =
                                null;

                            const success =
                                await this.persist(
                                    false
                                );

                            if (success) {
                                this.emitInitiativeChange(
                                    'clear'
                                );
                            }
                        },

                        resetMovementDefault() {
                            this.movementBase = 30;
                            this.movementBonus = 0;
                        },
                    })
                );
            }
        );
    </script>
@endonce


<div
    x-data="heroQuickStats(
        {{ (int) $proficiencyBonus }},
        {{ $dexterityModifier }},
        {{ $strengthScore }},
        {{ $strengthModifier }},
        {{ $perceptionSkillModifier }},
        {{ (int) $exhaustionLevel }},
        @js([
            'initiative' => [
                'mode' =>
                    $initiativeMode,

                'bonus' =>
                    $initiativeBonus,

                'combat_value' =>
                    $initiativeCombatValue,
            ],

            'movement' => [
                'base' =>
                    $movementBase,

                'bonus' =>
                    $movementBonus,

                'types' => $movementTypeSpeeds,
                'hover' => $movementHover,

                'jump_enabled' =>
                    $jumpEnabled,

                'jump_horizontal_bonus' =>
                    $jumpHorizontalBonus,

                'jump_vertical_bonus' =>
                    $jumpVerticalBonus,
            ],

            'passive_perception' => [
                'bonus' =>
                    $passivePerceptionBonus,
            ],
        ]),
        @js($overrides),
        (fields) => persistCombat(fields),
        @js(url('/api/roll')),
        @js($combatCallConfig)
    )"

    @keydown.escape.window="
        closeAll()
    "

    @character-turn-state.window="
        syncTurnState(
            $event.detail
        )
    "

    @character-exhaustion-updated.window="
        syncExhaustion(
            $event.detail
        )
    "

    class="hero-quick-stats hero-quick-stats-v2 w-full"
>
    {{-- ============================================================
         BARRA COMPACTA
    ============================================================= --}}

    <div
        class="
            hero-quick-stats-bar
            grid
            h-[58px]
            w-full
            grid-cols-3
            overflow-hidden
            rounded-lg
            border
            border-[#d8c7ab]/70
            bg-[#faf8f2]
        "

        :data-current-turn="
            isCurrentTurn
                ? 'true'
                : 'false'
        "
    >
        {{-- INICIATIVA --}}
        <button
            type="button"

            @click="
                initiativeOpen = true
            "

            class="
                hero-quick-stat-item hero-quick-stat-item--initiative
                group
                flex
                min-w-0
                flex-col
                items-center
                justify-center
                border-r
                border-[#d8c7ab]/55
                px-2
                py-1
                text-center
                transition
                duration-150
                hover:bg-[#f4f1e8]/70
            "

            title="Iniciativa"
        >
            <span
                class="
                    text-[7px]
                    font-black
                    uppercase
                    tracking-[0.11em]
                    text-[#8c6239]
                "
            >
                Iniciativa
            </span>

            <span
                class="
                    mt-1
                    font-serif
                    text-[18px]
                    font-black
                    leading-none
                    text-[#53150f]
                "

                x-text="
                    initiativeDisplay
                "
            ></span>
        </button>


        {{-- DESLOCAMENTO --}}
        <button
            type="button"

            @click="
                movementOpen = true
            "

            class="
                hero-quick-stat-item hero-quick-stat-item--movement
                group
                flex
                min-w-0
                flex-col
                items-center
                justify-center
                border-r
                border-[#d8c7ab]/55
                px-2
                py-1
                text-center
                transition
                duration-150
                hover:bg-[#f4f1e8]/70
            "

            :title="movementOtherSummary ? 'Deslocamentos: ' + movementOtherSummary : 'Deslocamento e saltos'"
        >
            <span
                class="
                    text-[7px]
                    font-black
                    uppercase
                    tracking-[0.09em]
                    text-[#8c6239]
                "
            >
                Deslocamento
            </span>

            <span
                class="
                    mt-0.5
                    whitespace-nowrap
                    font-serif
                    text-[17px]
                    font-black
                    leading-none
                    text-[#53150f]
                "
            >
                <span
                    x-text="
                        movementTotal
                    "
                ></span>

                <span
                    class="
                        text-[8px]
                        font-black
                        text-[#8c6239]
                    "
                >
                    ft
                </span>
            </span>

            <span
                x-show="
                    jumpEnabled && !movementOtherSummary
                "

                x-cloak

                class="
                    mt-1.5
                    whitespace-nowrap
                    text-[9px]
                    font-bold
                    leading-none
                    text-[#8c6239]/85
                "
            >
                <span
                    class="
                        font-black
                        text-[#6b1d14]
                    "
                >
                    Salto
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        horizontalJump + 'ft'
                    "
                ></span>

                <span class="text-[#8c6239]/70">
                    (horiz.)
                </span>

                <span class="mx-0.5 text-[#c0ad8e]">
                    ·
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        verticalJump + 'ft'
                    "
                ></span>

                <span class="text-[#8c6239]/70">
                    (vert.)
                </span>
            </span>
            <span
                x-show="movementOtherSummary"
                x-cloak
                class="hero-quick-movement-muted hero-quick-movement-caption mt-1 max-w-full text-center text-[8px] font-semibold leading-[1.25]"
                x-text="movementHeaderSummary"
                :title="movementOtherSummary"
            ></span>
        </button>


        {{-- PERCEPÇÃO PASSIVA --}}
        <button
            type="button"

            @click="
                perceptionOpen = true
            "

            class="
                hero-quick-stat-item hero-quick-stat-item--perception
                group
                flex
                min-w-0
                flex-col
                items-center
                justify-center
                px-2
                py-1
                text-center
                transition
                duration-150
                hover:bg-[#f4f1e8]/70
            "

            title="Percepção Passiva"
        >
            <span
                class="
                    whitespace-nowrap
                    text-[7px]
                    font-black
                    uppercase
                    tracking-[0.07em]
                    text-[#8c6239]
                "
            >
                Percepção Passiva
            </span>

            <span
                class="
                    mt-1
                    font-serif
                    text-[18px]
                    font-black
                    leading-none
                    text-[#53150f]
                "

                x-text="
                    passivePerception
                "
            ></span>
        </button>
    </div>


    {{-- ============================================================
         CHAMADO DE COMBATE DA CAMPANHA

         Este modal é adicional. A barra Quick Stat e seus modais originais
         permanecem exatamente com o mesmo HTML/CSS.
    ============================================================= --}}
    <template x-teleport="body">
        <div
                role="dialog"
                aria-modal="true"

            x-show="combatCall !== null"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            class="
                character-sheet-theme
                character-theme-modal-layer
                hero-quick-stats-modal-layer
                fixed
                inset-0
                z-[210]
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
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                @click.stop
                class="
                    character-theme-modal
                    hero-quick-stats-modal
                    hero-combat-call-modal
                    relative
                    z-10
                    w-full
                    max-w-sm
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#cdbb9f]
                    bg-[#faf8f2]
                    shadow-2xl
                "
            >
                <div
                    class="
                        flex
                        items-center
                        justify-between
                        border-b
                        border-[#a0774d]/30
                        bg-[#eadbc8]
                        px-4
                        py-3
                    "
                >
                    <div class="min-w-0">
                        <p
                            class="
                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.2em]
                                text-[#8c6239]
                            "
                        >
                            Chamado do Mestre
                        </p>

                        <h3
                            class="
                                truncate
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                            x-text="combatCall?.combat_name ?? 'Combate'"
                        ></h3>
                    </div>

                    <div
                        x-show="combatCall?.phase === 'join'"
                        class="
                            hero-combat-call-countdown
                            rounded-lg
                            border
                            border-[#cdbb9f]
                            bg-white
                            px-2.5
                            py-1
                            font-serif
                            text-lg
                            font-black
                            text-[#53150f]
                        "
                        x-text="combatCallCountdownLabel"
                    ></div>
                </div>

                <div
                    x-show="combatCall?.phase === 'join'"
                    class="space-y-4 p-4"
                >
                    <div class="text-center">
                        <p
                            class="
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            Participar do combate?
                        </p>

                        <p
                            class="
                                mt-1.5
                                text-[11px]
                                leading-5
                                text-[#8c6239]
                            "
                        >
                            Se não responder em um minuto, sua resposta será Não automaticamente.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            @click="respondCombatCall(false)"
                            :disabled="combatCallBusy"
                            class="
                                rounded-lg
                                border
                                border-[#cdbb9f]
                                bg-[#efe9dc]
                                px-3
                                py-2.5
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#53150f]
                                transition
                                hover:bg-[#fffdf8]/55
                                disabled:opacity-50
                            "
                        >
                            Não
                        </button>

                        <button
                            type="button"
                            @click="respondCombatCall(true)"
                            :disabled="combatCallBusy"
                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-3
                                py-2.5
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#f4f1e8]
                                transition
                                hover:bg-[#53150f]
                                disabled:opacity-50
                            "
                        >
                            Sim
                        </button>
                    </div>
                </div>

                <div
                    x-show="combatCall?.phase === 'initiative'"
                    x-cloak
                    class="p-4 sm:p-5"
                >
                    <div class="text-center">
                        <p
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]
                                text-[#93623d]
                            "
                        >
                            Você entrou no combate
                        </p>

                        <h4
                            class="
                                mt-1
                                font-serif
                                text-[22px]
                                font-black
                                leading-none
                                text-[#53150f]
                            "
                        >
                            Defina sua iniciativa
                        </h4>

                        <p
                            class="
                                mx-auto
                                mt-2
                                max-w-[330px]
                                text-[10px]
                                leading-relaxed
                                text-[#755847]
                            "
                        >
                            Role o d20 pelo Spellbound ou informe diretamente
                            sua iniciativa final.
                        </p>
                    </div>


                    {{-- ====================================================
                         D20 CANÔNICO
                    ===================================================== --}}

                    <button
                        type="button"
                        @click="window.SpellboundD20Gestures.left($event, mode => rollCombatCallInitiative(mode))"
                        @dblclick.prevent="window.SpellboundD20Gestures.double($event, mode => rollCombatCallInitiative(mode))"
                        @contextmenu.prevent="window.SpellboundD20Gestures.right($event, mode => rollCombatCallInitiative(mode))"
                        :disabled="
                            combatCallBusy
                            ||
                            combatCallRolling
                        "
                        class="
                            group
                            mx-auto
                            mt-4
                            block
                            disabled:cursor-wait
                        "
                    >
                        <span
                            class="
                                inline-flex
                                transition
                                duration-150
                                group-hover:-translate-y-0.5
                            "
                        >
                            <x-dice.polyhedral
                                :sides="20"
                                :size="88"
                            >
                                <span
                                    class="tabular-nums"
                                    x-text="
                                        combatCallRolling
                                            ? combatCallRollingPreview
                                            : (
                                                combatCallDie
                                                ?? 20
                                            )
                                    "
                                ></span>
                            </x-dice.polyhedral>
                        </span>

                        <span
                            class="
                                mt-1.5
                                block
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.13em]
                                text-[#93623d]
                            "
                        >
                            <span
                                x-show="
                                    !combatCallRolling
                                    &&
                                    combatCallDie === null
                                "
                            >
                                Rolar d20
                            </span>

                            <span
                                x-show="
                                    !combatCallRolling
                                    &&
                                    combatCallDie !== null
                                "
                                x-cloak
                            >
                                Rolar novamente
                            </span>

                            <span
                                x-show="
                                    combatCallRolling
                                "
                                x-cloak
                            >
                                Rolando...
                            </span>
                        </span>
                    </button>


                    {{-- ====================================================
                         BÔNUS x INICIATIVA FINAL
                    ===================================================== --}}

                    <div
                        class="
                            mt-5
                            grid
                            grid-cols-[1fr_auto_1fr]
                            items-stretch
                            gap-2
                        "
                    >
                        {{-- Bônus efetivo --}}
                        <div
                            class="
                                rounded-xl
                                border
                                border-[#cdbb9f]/80
                                bg-[#f3e7d8]/55
                                px-3
                                py-3
                            "
                        >
                            <span
                                class="
                                    block
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]
                                    text-[#93623d]
                                "
                            >
                                Bônus
                            </span>

                            <strong
                                class="
                                    mt-1
                                    block
                                    font-serif
                                    text-[24px]
                                    font-black
                                    leading-none
                                    text-[#53150f]
                                    tabular-nums
                                "
                                x-text="
                                    combatCallEffectiveBonusLabel
                                "
                            ></strong>

                            <p
                                class="
                                    mt-1.5
                                    text-[8px]
                                    leading-snug
                                    text-[#80604b]
                                "
                            >
                                <span
                                    x-text="
                                        initiativeModifierLabel
                                    "
                                ></span>

                                <span
                                    x-show="
                                        exhaustionRollPenalty > 0
                                    "
                                    x-cloak
                                >
                                    · Exaustão
                                    -<span
                                        x-text="
                                            exhaustionRollPenalty
                                        "
                                    ></span>
                                </span>
                            </p>
                        </div>


                        {{-- Separador matemático --}}
                        <div
                            class="
                                flex
                                items-center
                                justify-center
                                px-1
                                font-serif
                                text-[17px]
                                font-black
                                text-[#a1846b]
                            "
                            aria-hidden="true"
                        >
                            =
                        </div>


                        {{-- Final editável --}}
                        <label
                            class="
                                rounded-xl
                                border
                                border-[#93623d]/35
                                bg-[#fffdf8]
                                px-3
                                py-3
                                shadow-[inset_0_1px_2px_rgba(83,21,15,.04)]
                                transition
                                focus-within:border-[#6b1d14]/55
                                focus-within:ring-2
                                focus-within:ring-[#93623d]/10
                            "
                        >
                            <span
                                class="
                                    block
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]
                                    text-[#93623d]
                                "
                            >
                                Iniciativa final
                            </span>

                            <input
                                type="number"
                                min="-100"
                                max="200"
                                step="1"
                                inputmode="numeric"
                                x-model.number="
                                    combatCallFinalInitiative
                                "
                                @input="
                                    combatCallError = null
                                "
                                @keydown.enter.prevent="
                                    if (
                                        combatCallFinalValue !== null
                                    ) {
                                        submitCombatCallInitiative()
                                    }
                                "
                                placeholder="—"
                                class="
                                    mt-0.5
                                    block
                                    w-full
                                    border-0
                                    bg-transparent
                                    p-0
                                    font-serif
                                    text-[24px]
                                    font-black
                                    leading-none
                                    text-[#53150f]
                                    placeholder:text-[#b9a28f]
                                    focus:border-0
                                    focus:outline-none
                                    focus:ring-0
                                    tabular-nums
                                "
                            >

                            <span
                                class="
                                    mt-1.5
                                    block
                                    text-[8px]
                                    leading-snug
                                    text-[#80604b]
                                "
                            >
                                Informe o total, não o valor do dado.
                            </span>
                        </label>
                    </div>


                    {{-- ====================================================
                         RESULTADO DA ROLAGEM
                    ===================================================== --}}

                    <div
                        x-show="
                            combatCallDie !== null
                            &&
                            !combatCallRolling
                        "
                        x-cloak
                        class="
                            mt-3
                            flex
                            items-center
                            justify-between
                            gap-3
                            rounded-lg
                            border
                            border-[#d8c7ab]/65
                            bg-[#f8f0e6]/72
                            px-3
                            py-2
                        "
                    >
                        <span
                            class="
                                text-[8px]
                                font-bold
                                text-[#755847]
                            "
                        >
                            Rolagem:
                            <strong
                                class="
                                    ml-1
                                    font-serif
                                    text-[11px]
                                    text-[#53150f]
                                "
                                x-text="
                                    combatCallDie
                                "
                            ></strong>

                            <span class="mx-1">
                                +
                            </span>

                            <strong
                                class="
                                    font-serif
                                    text-[11px]
                                    text-[#53150f]
                                "
                                x-text="
                                    combatCallEffectiveBonusLabel
                                "
                            ></strong>
                        </span>

                        <strong
                            class="
                                font-serif
                                text-[13px]
                                font-black
                                text-[#53150f]
                            "
                        >
                            =
                            <span
                                x-text="
                                    combatCallRolledTotal
                                "
                            ></span>
                        </strong>
                    </div>


                    {{-- ====================================================
                         CONFIRMAÇÃO
                    ===================================================== --}}

                    <button
                        type="button"
                        @click="
                            submitCombatCallInitiative()
                        "
                        :disabled="
                            combatCallBusy
                            ||
                            combatCallRolling
                            ||
                            combatCallFinalValue === null
                        "
                        class="
                            mt-4
                            w-full
                            rounded-lg
                            border
                            border-[#53150f]/35
                            bg-[#6b1d14]
                            px-3
                            py-2.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-[0.12em]
                            text-[#fffaf2]
                            shadow-[0_7px_18px_rgba(83,21,15,.14)]
                            transition
                            hover:bg-[#53150f]
                            disabled:cursor-not-allowed
                            disabled:bg-[#b98f86]
                            disabled:shadow-none
                            disabled:opacity-70
                        "
                    >
                        <span
                            x-show="
                                !combatCallBusy
                            "
                        >
                            Confirmar iniciativa
                        </span>

                        <span
                            x-show="
                                combatCallBusy
                            "
                            x-cloak
                        >
                            Enviando...
                        </span>
                    </button>
                </div>

                <div
                    x-show="combatCallError"
                    x-cloak
                    class="
                        border-t
                        border-red-900/15
                        bg-red-50
                        px-4
                        py-2.5
                        text-center
                        text-[10px]
                        font-bold
                        text-red-800
                    "
                    x-text="combatCallError"
                ></div>
            </div>
        </div>
    </template>


    {{-- ============================================================
         MODAL — INICIATIVA
    ============================================================= --}}

        <template x-teleport="body">
        <div role="dialog" aria-modal="true" aria-label="Editar iniciativa"
            x-show="initiativeOpen" x-cloak
            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer hero-quick-stats-modal-layer fixed inset-0 z-[160] flex items-center justify-center p-4">
            <div class="character-theme-modal-backdrop absolute inset-0" @click="initiativeOpen = false"></div>
            <div @click.stop
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                class="character-theme-modal hero-quick-stats-modal hero-quick-editor-modal relative z-10 w-full overflow-hidden rounded-2xl border">
                <header class="hero-quick-editor-header flex items-center justify-between gap-3 px-4 py-3">
                    <div>
                        <p class="hero-quick-editor-label text-[9px] font-black uppercase tracking-[.16em]">Combate</p>
                        <h3 class="hero-quick-editor-heading font-serif text-xl font-black">Iniciativa</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <strong class="hero-quick-editor-heading font-serif text-xl tabular-nums" x-text="initiativeModifierLabel"></strong>
                        <button type="button" class="hero-quick-stats-close flex h-8 w-8 items-center justify-center rounded-lg" @click="initiativeOpen = false" aria-label="Fechar">×</button>
                    </div>
                </header>
    
                <div class="hero-quick-editor-scroll">
                    <section class="hero-quick-editor-section">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <span class="hero-quick-editor-label block text-[9px] font-black uppercase tracking-wide">DES</span>
                                <strong class="hero-quick-editor-heading font-serif text-lg" x-text="dexterityModifier >= 0 ? '+' + dexterityModifier : dexterityModifier"></strong>
                            </div>
                            <div>
                                <span class="hero-quick-editor-label block text-[9px] font-black uppercase tracking-wide">Prof.</span>
                                <strong class="hero-quick-editor-heading font-serif text-lg" x-text="initiativeProficiencyBonus >= 0 ? '+' + initiativeProficiencyBonus : initiativeProficiencyBonus"></strong>
                            </div>
                            <div>
                                <span class="hero-quick-editor-label block text-[9px] font-black uppercase tracking-wide">Extra</span>
                                <strong class="hero-quick-editor-heading font-serif text-lg" x-text="Number(initiativeBonus) >= 0 ? '+' + (parseInt(initiativeBonus) || 0) : (parseInt(initiativeBonus) || 0)"></strong>
                            </div>
                        </div>
                    </section>
    
                    <section class="hero-quick-editor-section">
                        <p class="hero-quick-editor-label mb-2 text-[9px] font-black uppercase tracking-[.08em]">Proficiência na iniciativa</p>
                        <div class="hero-quick-editor-segments grid grid-cols-3 overflow-hidden rounded-lg">
                            <button type="button" class="hero-quick-editor-segment py-2 text-[10px] font-bold transition" :aria-pressed="initiativeMode === 'none'" @click="initiativeMode = 'none'">Nenhuma</button>
                            <button type="button" class="hero-quick-editor-segment py-2 text-[10px] font-bold transition" :aria-pressed="initiativeMode === 'proficient'" @click="initiativeMode = 'proficient'">Proficiência</button>
                            <button type="button" class="hero-quick-editor-segment py-2 text-[10px] font-bold transition" :aria-pressed="initiativeMode === 'expertise'" @click="initiativeMode = 'expertise'">Expertise</button>
                        </div>
                        <label class="mt-3 flex items-center justify-between gap-3">
                            <span class="hero-quick-editor-muted text-[11px] font-medium">Bônus adicional</span>
                            <input type="number" x-model.number="initiativeBonus" aria-label="Bônus adicional de iniciativa"
                                class="hero-quick-editor-input h-9 w-24 rounded-lg px-2 text-center text-[12px] font-bold tabular-nums">
                        </label>
                    </section>
    
                    <section class="hero-quick-editor-section">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="hero-quick-editor-heading text-[11px] font-bold">Iniciativa do combate</p>
                                <p class="hero-quick-editor-muted text-[9px]">Role ou informe o total.</p>
                            </div>
                            <input type="number" x-model.number="combatInitiative" placeholder="—" aria-label="Total da iniciativa de combate"
                                class="hero-quick-editor-input h-10 w-24 rounded-lg px-2 text-center font-serif text-lg font-black tabular-nums">
                        </div>
                        <div class="mt-2.5 grid grid-cols-2 gap-2">
                            <button type="button" class="hero-quick-editor-primary rounded-lg px-2 py-2.5 text-[10px] font-bold transition disabled:opacity-50"
                                @click="window.SpellboundD20Gestures.left($event, mode => rollInitiative(mode))"
                                @dblclick.prevent="window.SpellboundD20Gestures.double($event, mode => rollInitiative(mode))"
                                @contextmenu.prevent="window.SpellboundD20Gestures.right($event, mode => rollInitiative(mode))"
                                :disabled="rollingInitiative || saving"
                                x-text="rollingInitiative ? 'Rolando...' : 'Rolar iniciativa'"></button>
                            <button type="button" class="hero-quick-editor-button rounded-lg px-2 py-2.5 text-[10px] font-bold transition disabled:opacity-50"
                                @click="saveManualInitiative()" :disabled="saving">Usar valor</button>
                        </div>
                        <p x-show="lastInitiativeRoll !== null" x-cloak
                            class="hero-quick-editor-muted mt-2 text-center text-[10px]"
                            x-text="'d20 (' + lastInitiativeRoll + ') ' + (initiativeModifier >= 0 ? '+' : '') + initiativeModifier + ' = ' + combatInitiative"></p>
                        <button type="button" x-show="combatInitiative !== null" x-cloak
                            class="hero-quick-editor-muted mt-2 block w-full py-1 text-center text-[9px] underline underline-offset-2"
                            @click="clearCombatInitiative()">Limpar resultado</button>
                    </section>
                </div>
    
                <footer class="hero-quick-stats-modal-footer hero-quick-editor-footer flex justify-end gap-2">
                    <button type="button" class="hero-quick-editor-button rounded-lg px-3 py-2 text-[10px] font-black uppercase" @click="initiativeOpen = false">Cancelar</button>
                    <button type="button" class="hero-quick-editor-primary rounded-lg px-4 py-2 text-[10px] font-black uppercase disabled:opacity-50" :disabled="saving" @click="persist()">Salvar</button>
                </footer>
            </div>
        </div>
    </template>

        {{-- ============================================================
         MODAL — DESLOCAMENTO
    ============================================================= --}}

    <template x-teleport="body">
        <div
                role="dialog"
                aria-modal="true"

            x-show="
                movementOpen
            "

            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"


            class="
                character-sheet-theme
                character-theme-modal-layer
                hero-quick-stats-modal-layer
                fixed
                inset-0
                z-[160]
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
                    movementOpen = false
                "
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                @click.stop

                class="
                    character-theme-modal
                    hero-quick-stats-modal
                    hero-quick-movement-modal
                    relative
                    z-10
                    w-full
                    max-w-sm
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#cdbb9f]
                    bg-[#faf8f2]
                    shadow-2xl
                "
            >
                <div
                    class="
                        flex
                        items-center
                        justify-between
                        border-b
                        border-[#a0774d]/30

                        bg-[#eadbc8]

                        px-4
                        py-3

                        shadow-none
                    "
                >
                    <div>
                        <p
                            class="
                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.2em]
                                text-[#8c6239]
                            "
                        >
                            Movimento
                        </p>

                        <h3
                            class="
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            Deslocamento
                        </h3>
                    </div>

                    <button
                        type="button"

                        @click="
                            movementOpen = false
                        "

                        class="
                            hero-quick-stats-close
                            flex
                            h-8
                            w-8
                            items-center
                            justify-center
                            rounded-lg
                            text-[#8c6239]
                            transition
                            hover:bg-[#fffdf8]/55
                            hover:text-[#53150f]
                        "
                    >
                        ×
                    </button>
                </div>

                <div class="hero-quick-movement-scroll px-4 py-3.5 sm:px-5 sm:py-4">

                    {{-- Caminhada: o valor atual é a informação principal. --}}
                    <section class="hero-movement-section pb-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h4 class="hero-quick-movement-heading text-[12px] font-bold">Caminhada</h4>
                                <p class="hero-quick-movement-muted text-[9px]">Velocidade principal</p>
                            </div>
                            <strong class="hero-quick-movement-heading font-serif text-[19px] leading-none tabular-nums" x-text="movementTotal + ' ft'"></strong>
                        </div>
                        <div class="mt-2.5 grid grid-cols-2 gap-2.5">
                            <label class="min-w-0">
                                <span class="hero-quick-movement-muted block text-[9px] font-medium">Base (ft)</span>
                                <input type="number" min="0" max="999" step="1" inputmode="numeric"
                                    x-model.number="movementBase"
                                    class="hero-quick-movement-input mt-1 h-9 w-full rounded-lg px-2 text-center text-[12px] font-bold tabular-nums">
                            </label>
                            <label class="min-w-0">
                                <span class="hero-quick-movement-muted block text-[9px] font-medium">Bônus (ft)</span>
                                <input type="number" min="-999" max="999" step="1" inputmode="numeric"
                                    x-model.number="movementBonus"
                                    class="hero-quick-movement-input mt-1 h-9 w-full rounded-lg px-2 text-center text-[12px] font-bold tabular-nums">
                            </label>
                        </div>
                    </section>

                    {{-- Uma linha por opção, sem cartões dentro de cartões. --}}
                    <section class="hero-movement-section border-t py-3.5">
                        <h4 class="hero-quick-movement-heading mb-2.5 text-[12px] font-bold">Outros deslocamentos</h4>
                        <div class="grid grid-cols-1 gap-x-4 gap-y-1.5 sm:grid-cols-2">
                            <template x-for="movementOption in [
                                {key:'climb', label:'Escalada'},
                                {key:'swim', label:'Natação'},
                                {key:'fly', label:'Voo'},
                                {key:'burrow', label:'Escavação'}
                            ]" :key="movementOption.key">
                                <div class="hero-movement-speed-row flex min-w-0 items-center gap-2 rounded-lg px-1.5 py-1.5">
                                    <button type="button"
                                        @click="toggleMovementType(movementOption.key)"
                                        :aria-pressed="Number(movementTypes[movementOption.key]) > 0"
                                        :title="'Ativar ou desativar ' + movementOption.label"
                                        class="hero-movement-speed-toggle group flex min-w-0 flex-1 items-center gap-2 text-left">
                                        <span class="hero-movement-speed-dot flex h-[17px] w-[17px] shrink-0 items-center justify-center rounded-[5px] border text-[10px] font-bold transition"
                                            :class="Number(movementTypes[movementOption.key]) > 0 ? 'is-on' : ''"
                                            x-text="Number(movementTypes[movementOption.key]) > 0 ? '✓' : '+'"></span>
                                        <span class="hero-movement-speed-label truncate text-[11px] font-medium"
                                            x-text="movementOption.label"></span>
                                    </button>
                                    <label x-show="Number(movementTypes[movementOption.key]) > 0" x-cloak
                                        class="flex shrink-0 items-center gap-1">
                                        <input type="number" min="1" max="999" step="1" inputmode="numeric"
                                            x-model.number="movementTypes[movementOption.key]"
                                            :aria-label="'Velocidade de ' + movementOption.label + ' em pés'"
                                            class="hero-quick-movement-input h-8 w-[57px] rounded-lg px-1 text-center text-[11px] font-semibold tabular-nums">
                                        <span class="hero-quick-movement-muted text-[9px]">ft</span>
                                    </label>
                                </div>
                            </template>
                        </div>
                        <div x-show="Number(movementTypes.fly) > 0" x-cloak
                            class="mt-1.5 flex items-center justify-between gap-2 px-1.5 py-1.5">
                            <span class="hero-quick-movement-heading text-[11px] font-medium">Pairar <span class="hero-quick-movement-muted font-normal">(Hover)</span></span>
                            <button type="button" @click="movementHover = !movementHover"
                                :aria-pressed="movementHover"
                                :class="{'is-on':movementHover}"
                                class="hero-movement-quiet-toggle rounded-lg border px-2.5 py-1.5 text-[10px] font-semibold transition"
                                x-text="movementHover ? 'Ativado ✓' : 'Ativar'"></button>
                        </div>
                    </section>

                    {{-- Saltos sempre visíveis; ajustes opcionais recolhidos por padrão. --}}
                    <section class="hero-movement-section border-t pt-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="hero-quick-movement-heading text-[12px] font-bold">Saltos</h4>
                                <p class="hero-quick-movement-muted mt-0.5 text-[9px]"
                                    x-text="horizontalJump + ' ft horizontal · ' + verticalJump + ' ft vertical'"></p>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <button type="button" @click="jumpEnabled = !jumpEnabled"
                                    :aria-pressed="jumpEnabled"
                                    :class="{'is-on':jumpEnabled}"
                                    class="hero-movement-quiet-toggle rounded-lg border px-2.5 py-1.5 text-[10px] font-semibold transition"
                                    x-text="jumpEnabled ? 'Na ficha ✓' : 'Oculto'"></button>
                                <button type="button" @click="jumpDetailsOpen = !jumpDetailsOpen"
                                    :aria-expanded="jumpDetailsOpen"
                                    class="hero-movement-adjust text-[10px] font-semibold transition"
                                    x-text="jumpDetailsOpen ? 'Fechar' : 'Ajustar'"></button>
                            </div>
                        </div>
                        <div x-show="jumpDetailsOpen" x-cloak class="pt-3">
                            <div class="grid grid-cols-2 gap-2.5">
                                <label class="min-w-0">
                                    <span class="hero-quick-movement-muted block text-[9px]">Bônus horizontal (ft)</span>
                                    <input type="number" min="-999" max="999" step="1" inputmode="numeric"
                                        x-model.number="jumpHorizontalBonus"
                                        class="hero-quick-movement-input mt-1 h-9 w-full rounded-lg px-2 text-center text-[12px] font-semibold tabular-nums">
                                </label>
                                <label class="min-w-0">
                                    <span class="hero-quick-movement-muted block text-[9px]">Bônus vertical (ft)</span>
                                    <input type="number" min="-999" max="999" step="1" inputmode="numeric"
                                        x-model.number="jumpVerticalBonus"
                                        class="hero-quick-movement-input mt-1 h-9 w-full rounded-lg px-2 text-center text-[12px] font-semibold tabular-nums">
                                </label>
                            </div>
                            <p class="hero-quick-movement-muted mt-2 text-[9px] leading-relaxed">Com corrida: horizontal = FOR; vertical = 3 + modificador de FOR. Sem corrida, metade.</p>
                        </div>
                    </section>
                </div>

                    {{-- AÇÕES --}}
                    <div
                        class="
                            hero-quick-stats-modal-footer
                            hero-quick-movement-footer
                            flex
                            justify-end
                            gap-2
                            border-t
                            border-[#d8c7ab]/50
                            pt-3
                        "
                    >
                        <button
                            type="button"

                            @click="
                                movementOpen = false
                            "

                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                                transition
                                hover:bg-[#efe9dc]
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"

                            @click="
                                persist()
                            "

                            :disabled="
                                saving
                            "

                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-4
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#f4f1e8]
                                transition
                                hover:bg-[#53150f]
                                disabled:opacity-50
                            "
                        >
                            Salvar
                        </button>
                    </div>
            </div>
        </div>
    </template>


    {{-- ============================================================
         MODAL — PERCEPÇÃO PASSIVA
    ============================================================= --}}

        <template x-teleport="body">
        <div role="dialog" aria-modal="true" aria-label="Editar percepção passiva"
            x-show="perceptionOpen" x-cloak
            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer hero-quick-stats-modal-layer fixed inset-0 z-[160] flex items-center justify-center p-4">
            <div class="character-theme-modal-backdrop absolute inset-0" @click="perceptionOpen = false"></div>
            <div @click.stop
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                class="character-theme-modal hero-quick-stats-modal hero-quick-editor-modal relative z-10 w-full overflow-hidden rounded-2xl border">
                <header class="hero-quick-editor-header flex items-center justify-between gap-3 px-4 py-3">
                    <div>
                        <p class="hero-quick-editor-label text-[9px] font-black uppercase tracking-[.16em]">Sentidos</p>
                        <h3 class="hero-quick-editor-heading font-serif text-xl font-black">Percepção Passiva</h3>
                    </div>
                    <button type="button" class="hero-quick-stats-close flex h-8 w-8 items-center justify-center rounded-lg" @click="perceptionOpen = false" aria-label="Fechar">×</button>
                </header>
    
                <div class="hero-quick-editor-scroll">
                    <section class="hero-quick-editor-section">
                        <div class="flex items-center justify-between gap-2">
                            <p class="hero-quick-editor-muted text-[10px] font-medium">10 + Percepção + bônus</p>
                            <strong class="hero-quick-editor-heading font-serif text-2xl font-black tabular-nums" x-text="passivePerception"></strong>
                        </div>
                        <div class="hero-quick-editor-muted mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[9px]">
                            <span>Base <strong class="hero-quick-editor-heading">10</strong></span>
                            <span>·</span>
                            <span>Percepção <strong class="hero-quick-editor-heading" x-text="(perceptionSkillModifier >= 0 ? '+' : '') + perceptionSkillModifier"></strong></span>
                            <span>·</span>
                            <span>Extra <strong class="hero-quick-editor-heading" x-text="(Number(passivePerceptionBonus) >= 0 ? '+' : '') + (parseInt(passivePerceptionBonus) || 0)"></strong></span>
                        </div>
                    </section>
                    <section class="hero-quick-editor-section">
                        <label class="flex items-center justify-between gap-3">
                            <span class="hero-quick-editor-muted text-[11px] font-medium">Bônus adicional</span>
                            <input type="number" x-model.number="passivePerceptionBonus" aria-label="Bônus adicional de Percepção Passiva"
                                class="hero-quick-editor-input h-9 w-24 rounded-lg px-2 text-center text-[12px] font-bold tabular-nums">
                        </label>
                        <p class="hero-quick-editor-muted mt-2 text-[9px] leading-relaxed">Usa o modificador de Percepção; sem perícia cadastrada, utiliza Sabedoria.</p>
                    </section>
                </div>
    
                <footer class="hero-quick-stats-modal-footer hero-quick-editor-footer flex justify-end gap-2">
                    <button type="button" class="hero-quick-editor-button rounded-lg px-3 py-2 text-[10px] font-black uppercase" @click="perceptionOpen = false">Cancelar</button>
                    <button type="button" class="hero-quick-editor-primary rounded-lg px-4 py-2 text-[10px] font-black uppercase disabled:opacity-50" :disabled="saving" @click="persist()">Salvar</button>
                </footer>
            </div>
        </div>
    </template>
</div>