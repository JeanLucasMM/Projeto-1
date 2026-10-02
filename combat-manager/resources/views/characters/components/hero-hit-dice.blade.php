@props([
    'character',
    'hitDice' => [],
])

@php
    $hitDice = collect($hitDice)
        ->map(fn ($die) => [
            'die' => strtolower((string) ($die['die'] ?? 'd8')),
            'current' => max(0, (int) ($die['current'] ?? 0)),
            'maximum' => max(0, (int) ($die['maximum'] ?? 0)),
        ])
        ->filter(fn ($die) => $die['maximum'] > 0)
        ->values();

    /*
    |--------------------------------------------------------------------------
    | CONSTITUIÇÃO EFETIVA
    |--------------------------------------------------------------------------
    |
    | Dados de Vida usam o modificador atual de CON. Respeitamos overrides
    | e bônus temporários, como no restante da ficha.
    |
    */

    $abilities = $character->abilities;

    $constitutionBase =
        (int) ($abilities?->constitution ?? 10);

    $temporaryBonuses =
        is_array($abilities?->temporary_bonuses ?? null)
            ? $abilities->temporary_bonuses
            : [];

    $abilityOverrides =
        is_array($abilities?->overrides ?? null)
            ? $abilities->overrides
            : [];

    $constitutionTemporary =
        (int) (
            $temporaryBonuses['constitution']
            ?? $temporaryBonuses['con']
            ?? 0
        );

    $constitutionOverride =
        $abilityOverrides['constitution']
        ?? $abilityOverrides['con']
        ?? null;

    $constitutionScore =
        $constitutionOverride !== null
        && $constitutionOverride !== ''
            ? max(1, (int) $constitutionOverride)
            : max(
                1,
                $constitutionBase + $constitutionTemporary
            );

    $constitutionModifier =
        (int) floor(
            ($constitutionScore - 10) / 2
        );


    /*
    |--------------------------------------------------------------------------
    | TEMA ATUAL
    |--------------------------------------------------------------------------
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
            @keyframes hit-dice-glow {
                0%, 100% {
                    box-shadow: 0 0 0 rgba(34, 197, 94, 0);
                }

                50% {
                    box-shadow: 0 0 0 rgba(34, 197, 94, .18);
                }
            }

            @keyframes hit-dice-danger {
                0%, 100% {
                    box-shadow: 0 0 0 rgba(220, 38, 38, 0);
                }

                50% {
                    box-shadow: 0 0 0 rgba(220, 38, 38, .18);
                }
            }

            @keyframes hit-dice-result {
                0% {
                    opacity: 0;
                    transform: translateY(-5px) scale(.97);
                }

                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            @keyframes hit-dice-pulse {
                0%, 100% {
                    box-shadow: 0 0 0 rgba(140, 29, 24, 0);
                }

                50% {
                    box-shadow: 0 0 0 rgba(140, 29, 24, .12);
                }
            }

            .hit-dice-good {
                animation: hit-dice-glow 3s ease-in-out infinite;
            }

            .hit-dice-danger {
                animation: hit-dice-danger 2s ease-in-out infinite;
            }

            .hit-dice-result {
                animation: hit-dice-result .18s ease-out;
            }

            .hit-dice-active {
                animation: hit-dice-pulse 2.4s ease-in-out infinite;
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
            | HERO HIT DICE V2 — PADRÃO VISUAL UNIFICADO
            |--------------------------------------------------------------------------
            */

            .hero-hit-dice-v2 .hit-dice-good,
            .hero-hit-dice-v2 .hit-dice-danger,
            .hero-hit-dice-v2 .hit-dice-active {
                animation: none !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-compact {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                box-shadow:
                    var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.07)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-label span:last-child {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-label:hover,
            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-roll:hover:not(:disabled),
            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-spend:hover:not(:disabled) {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-roll,
            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-spend {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-roll-result {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-primary) !important;
                box-shadow:
                    0 12px 32px rgba(0,0,0,.14),
                    var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.06)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-roll-result
            [class*="text-[#53150f]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-roll-result
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-roll-result
            [class*="text-[#8c7f70]"],
            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-roll-result
            [class*="text-[#b5a898]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-v2
            .hero-hit-dice-heal-total {
                color: #047857 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal {
                width: min(560px, calc(100vw - 28px)) !important;
                max-height: min(92dvh, 820px) !important;
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
                .hero-hit-dice-modal {
                    max-height: min(92vh, 820px) !important;
                }
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-header,
            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-footer {
                flex: 0 0 auto;
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;
                background:
                    var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-title {
                color:
                    var(--theme-modal-title, var(--theme-text-strong)) !important;
                font-size: 22px !important;
                line-height: 1.1 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-close {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-close:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-tabs {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-tab {
                min-height: 38px;
                color: var(--theme-text-secondary) !important;
                font-size: 10px !important;
                letter-spacing: .09em !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-tab:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-tab.is-active {
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-tab.is-active::after {
                content: "";
                position: absolute;
                inset-inline: 14px;
                bottom: 0;
                height: 2px;
                border-radius: 999px;
                background: var(--theme-accent-strong);
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-body {
                min-height: 0;
                flex: 1 1 auto;
                overflow-y: auto;
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                scrollbar-width: thin;
                scrollbar-color:
                    color-mix(in srgb, var(--theme-label) 30%, transparent)
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal
            [class*="text-[#4a1813]"],
            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal
            [class*="text-[#3c120a]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal
            [class*="text-[#8c7f70]"],
            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal
            [class*="text-[#a09383]"],
            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal
            [class*="text-[#b5a898]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-history-card {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-config-row,
            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-panel,
            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal input {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal input:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow:
                    0 0 0 3px
                    color-mix(in srgb, var(--theme-input-focus) 15%, transparent) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-button {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-button:hover {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-secondary {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-secondary:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-primary {
                border: 1px solid var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-primary:hover {
                background: var(--theme-button-hover-background) !important;
            }

            @media (max-width: 520px) {
                .character-sheet-theme[data-theme]
                .hero-hit-dice-modal {
                    width: calc(100vw - 20px) !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .hit-dice-result {
                    animation: none !important;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | HERO HIT DICE V3 — CONFIGURAÇÃO CLEAN / COMPLETA
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal {
                width:
                    min(
                        620px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-header {
                padding:
                    16px 18px !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-kicker {
                color:
                    var(--theme-modal-label, var(--theme-label)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-subtitle {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-config {
                padding:
                    18px 20px 20px !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-config-heading {
                border-bottom:
                    1px solid
                    var(--theme-divider);

                padding-bottom:
                    13px;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-con-badge {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-con-badge
            span {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-config-row {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-config-row:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-type-badge {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-type-badge
            span {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-field
            > span {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-field
            input {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-remove {
                border-color:
                    transparent !important;

                background:
                    transparent !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-remove:hover {
                border-color:
                    rgba(185,28,28,.18) !important;

                background:
                    #fff1f2 !important;

                color:
                    #b91c1c !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-config-empty {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card-alt) !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-config-empty
            p:first-child {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-panel {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-inset) !important;

                padding:
                    13px !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-panel
            > div:first-child {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-panel
            > p {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-button {
                min-height:
                    36px;

                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-add-button:hover {
                border-color:
                    var(--theme-accent) !important;

                background:
                    var(--theme-accent-softer) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary
            p:first-child,
            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary-card
            span {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary
            p:nth-child(2) {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary-ratio {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary-card {
                border-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-summary-card
            strong {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-modal-footer {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            @media (max-width: 560px) {
                .character-sheet-theme[data-theme]
                .hero-hit-dice-config-row {
                    grid-template-columns:
                        52px minmax(0,1fr) 32px;

                    gap:
                        8px;

                    padding:
                        10px !important;
                }

                .character-sheet-theme[data-theme]
                .hero-hit-dice-type-badge {
                    width:
                        52px;

                    height:
                        48px;
                }

                .character-sheet-theme[data-theme]
                .hero-hit-dice-config-heading {
                    align-items:
                        flex-start;

                    flex-direction:
                        column;
                }

                .character-sheet-theme[data-theme]
                .hero-hit-dice-con-badge {
                    align-self:
                        flex-start;
                }
            }

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .hero-hit-dice-remove:hover {
                background:
                    rgba(127,29,29,.18) !important;
            }


            /* HERO HIT DICE V4 — ESTADO VAZIO TEMATIZADO */
            .character-sheet-theme[data-theme]
            .hero-hit-dice-empty-history {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-empty-history > div {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hit-dice-empty-history > p:first-of-type,
            .character-sheet-theme[data-theme]
            .hero-hit-dice-empty-history > p:last-of-type {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .hero-hit-dice-empty-history {
                border-color: rgba(151,162,173,.24) !important;
                background: linear-gradient(180deg,#20262c 0%,#1a2025 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .hero-hit-dice-empty-history > div {
                color: #71808c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .hero-hit-dice-empty-history > p:first-of-type {
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .hero-hit-dice-empty-history > p:last-of-type {
                color: #87939d !important;
            }

        </style>
    @endpush

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data(
                'heroHitDice',
                (
                    initialDice,
                    constitutionModifier,
                    saveUrl,
                    rollUrl
                ) => ({
                    modalOpen: false,
                    modalTab: 'history',

                    saving: false,
                    rolling: false,

                    rollTimer: null,

                    constitutionModifier,

                    dice: initialDice,
                    history: [],

                    activeRoll: {
                        visible: false,
                        rolls: [],
                        total: 0,
                    },

                    get totalCurrent() {
                        return this.dice.reduce(
                            (total, die) =>
                                total + (parseInt(die.current) || 0),
                            0
                        );
                    },

                    get totalMaximum() {
                        return this.dice.reduce(
                            (total, die) =>
                                total + (parseInt(die.maximum) || 0),
                            0
                        );
                    },

                    get remainingPercent() {
                        if (this.totalMaximum <= 0) {
                            return 0;
                        }

                        return Math.max(
                            0,
                            Math.min(
                                100,
                                (
                                    this.totalCurrent /
                                    this.totalMaximum
                                ) * 100
                            )
                        );
                    },

                    get remainingClass() {
                        if (this.remainingPercent > 50) {
                            return 'bg-emerald-700 hit-dice-good';
                        }

                        if (this.remainingPercent > 25) {
                            return 'bg-amber-600';
                        }

                        return 'bg-red-700 hit-dice-danger';
                    },

                    get currentSummary() {
                        if (!this.dice.length) {
                            return '0';
                        }

                        return this.dice
                            .map(
                                die =>
                                    `${die.current}${String(die.die).toUpperCase()}`
                            )
                            .join(' / ');
                    },

                    get maximumSummary() {
                        if (!this.dice.length) {
                            return '0';
                        }

                        return this.dice
                            .map(
                                die =>
                                    `${die.maximum}${String(die.die).toUpperCase()}`
                            )
                            .join(' / ');
                    },

                    get activeRollSummary() {
                        if (!this.activeRoll.rolls.length) {
                            return '';
                        }

                        return this.activeRoll.rolls
                            .map(roll => roll.value)
                            .join(' + ');
                    },

                    get activeConstitutionTotal() {
                        return (
                            this.constitutionModifier *
                            this.activeRoll.rolls.length
                        );
                    },

                    get activeRollTotal() {
                        return Math.max(
                            0,
                            this.activeRoll.total +
                            this.activeConstitutionTotal
                        );
                    },

                    openModal(tab = 'history') {
                        this.modalTab = tab;
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                    },

                    clearRollTimer() {
                        if (this.rollTimer) {
                            clearTimeout(this.rollTimer);
                            this.rollTimer = null;
                        }
                    },

                    startRollWindow() {
                        this.clearRollTimer();

                        this.rollTimer = setTimeout(() => {
                            this.finishRollBatch();
                        }, 6000);
                    },

                    finishRollBatch() {
                        if (!this.activeRoll.rolls.length) {
                            this.activeRoll.visible = false;
                            this.clearRollTimer();
                            return;
                        }

                        const rawTotal =
                            Number(this.activeRoll.total) || 0;

                        const constitutionTotal =
                            this.constitutionModifier *
                            this.activeRoll.rolls.length;

                        const totalHealed = Math.max(
                            0,
                            rawTotal + constitutionTotal
                        );

                        const groups = Object.values(
                            this.activeRoll.rolls.reduce(
                                (grouped, roll) => {
                                    const die =
                                        String(
                                            roll.die ?? 'd8'
                                        ).toLowerCase();

                                    if (!grouped[die]) {
                                        grouped[die] = {
                                            die,
                                            values: [],
                                        };
                                    }

                                    grouped[die].values.push(
                                        parseInt(roll.value) || 0
                                    );

                                    return grouped;
                                },
                                {}
                            )
                        );

                        this.history.unshift({
                            id: Date.now(),

                            time: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                            }),

                            rolls: [...this.activeRoll.rolls],

                            groups,

                            rawTotal,

                            modifier: this.constitutionModifier,

                            constitutionTotal,

                            totalHealed,
                        });

                        this.activeRoll = {
                            visible: false,
                            rolls: [],
                            total: 0,
                        };

                        this.clearRollTimer();
                    },

                    normalizeDice() {
                        return this.dice
                            .map(die => {
                                const maximum = Math.max(
                                    0,
                                    parseInt(die.maximum) || 0
                                );

                                const current = Math.max(
                                    0,
                                    Math.min(
                                        maximum,
                                        parseInt(die.current) || 0
                                    )
                                );

                                return {
                                    die: String(die.die).toLowerCase(),
                                    current,
                                    maximum,
                                };
                            })
                            .filter(die => die.maximum > 0);
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | SINCRONIZA APÓS DESCANSO
                    |--------------------------------------------------------------------------
                    |
                    | O CharacterRestService é a fonte de verdade.
                    |
                    | Não salvamos nada aqui novamente.
                    | Apenas recebemos o estado final retornado pelo backend.
                    |
                    */
                    syncAfterRest(payload) {
                        if (!payload) {
                            return;
                        }

                        const restType =
                            payload?.rest?.type ?? null;

                        /*
                        |--------------------------------------------------------------------------
                        | DESCANSO CURTO
                        |--------------------------------------------------------------------------
                        |
                        | Dados de Vida não são recuperados.
                        |
                        */
                        if (restType !== 'long') {
                            return;
                        }

                        const serverDice =
                            payload?.combat?.hit_dice;

                        if (!Array.isArray(serverDice)) {
                            console.warn(
                                'Descanso longo concluído, mas combat.hit_dice não foi retornado.'
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | CANCELA ESTADOS TRANSITÓRIOS
                        |--------------------------------------------------------------------------
                        */

                        this.clearRollTimer();

                        this.rolling = false;

                        this.activeRoll = {
                            visible: false,
                            rolls: [],
                            total: 0,
                        };

                        /*
                        |--------------------------------------------------------------------------
                        | HISTÓRICO DA SESSÃO
                        |--------------------------------------------------------------------------
                        |
                        | É somente estado visual do componente.
                        |
                        */
                        this.history = [];

                        /*
                        |--------------------------------------------------------------------------
                        | ESTADO RETORNADO PELO SERVIDOR
                        |--------------------------------------------------------------------------
                        */

                        this.dice = serverDice
                            .map(die => {
                                const maximum = Math.max(
                                    0,
                                    parseInt(
                                        die?.maximum
                                    ) || 0
                                );

                                const current = Math.max(
                                    0,
                                    Math.min(
                                        maximum,
                                        parseInt(
                                            die?.current
                                        ) || 0
                                    )
                                );

                                return {
                                    die:
                                        String(
                                            die?.die ?? 'd8'
                                        ).toLowerCase(),

                                    current,

                                    maximum,
                                };
                            })
                            .filter(
                                die =>
                                    die.maximum > 0
                            );
                    },

                    async persistDice() {
                        const normalized = this.normalizeDice();

                        const formData = new FormData();

                        formData.append(
                            '_token',
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                ?.getAttribute('content')
                                ?? '{{ csrf_token() }}'
                        );

                        formData.append(
                            '_method',
                            'PATCH'
                        );

                        formData.append(
                            'hit_dice',
                            JSON.stringify(normalized)
                        );

                        try {
                            const response = await fetch(
                                saveUrl,
                                {
                                    method: 'POST',

                                    body: formData,

                                    headers: {
                                        'X-Requested-With':
                                            'XMLHttpRequest',

                                        'Accept':
                                            'application/json',
                                    },
                                }
                            );

                            if (!response.ok) {
                                throw new Error(
                                    'Falha ao salvar os Dados de Vida.'
                                );
                            }

                            return true;
                        } catch (error) {
                            console.error(
                                'Erro ao salvar Dados de Vida:',
                                error
                            );

                            return false;
                        }
                    },

                    updateDieMax(die, value) {
                        const newMaximum = Math.max(
                            0,
                            parseInt(value) || 0
                        );

                        const oldMaximum = Math.max(
                            0,
                            parseInt(die.maximum) || 0
                        );

                        const difference =
                            newMaximum - oldMaximum;

                        die.maximum = newMaximum;

                        if (difference > 0) {
                            die.current = Math.min(
                                die.maximum,
                                (
                                    parseInt(die.current) || 0
                                ) + difference
                            );
                        } else if (
                            die.current > die.maximum
                        ) {
                            die.current = die.maximum;
                        }
                    },

                    addDie(type) {
                        const existing = this.dice.find(
                            die => die.die === type
                        );

                        if (existing) {
                            this.updateDieMax(
                                existing,
                                (
                                    parseInt(
                                        existing.maximum
                                    ) || 0
                                ) + 1
                            );

                            return;
                        }

                        this.dice.push({
                            die: type,
                            current: 1,
                            maximum: 1,
                        });
                    },

                    removeDie(index) {
                        this.dice.splice(index, 1);
                    },

                    async spendDie(die) {
                        if (
                            this.rolling ||
                            (
                                parseInt(die.current) || 0
                            ) <= 0
                        ) {
                            return;
                        }

                        die.current = Math.max(
                            0,
                            (
                                parseInt(die.current) || 0
                            ) - 1
                        );

                        await this.persistDice();
                    },

                    async roll(die) {
                        if (
                            this.rolling ||
                            (
                                parseInt(die.current) || 0
                            ) <= 0
                        ) {
                            return;
                        }

                        this.rolling = true;

                        die.current = Math.max(
                            0,
                            (
                                parseInt(die.current) || 0
                            ) - 1
                        );

                        const faces =
                            parseInt(
                                String(die.die).replace(
                                    'd',
                                    ''
                                )
                            ) || 8;

                        let value =
                            Math.floor(
                                Math.random() * faces
                            ) + 1;

                        try {
                            const response = await fetch(
                                rollUrl,
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
                                            'XMLHttpRequest',
                                    },

                                    body: JSON.stringify({
                                        expression:
                                            `1d${faces}`,
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
                                    apiValue !== null &&
                                    !Number.isNaN(
                                        parseInt(apiValue)
                                    )
                                ) {
                                    value =
                                        parseInt(apiValue);
                                }
                            }
                        } catch (error) {
                            console.warn(
                                'API de dados indisponível. Usando rolagem local.',
                                error
                            );
                        }

                        if (!this.activeRoll.visible) {
                            this.activeRoll = {
                                visible: true,
                                rolls: [],
                                total: 0,
                            };
                        }

                        this.activeRoll.rolls.push({
                            die: die.die,
                            value,
                        });

                        this.activeRoll.total += value;

                        await this.persistDice();

                        this.startRollWindow();

                        this.rolling = false;
                    },

                    async saveConfig() {
                        this.saving = true;

                        try {
                            const success =
                                await this.persistDice();

                            if (success) {
                                this.modalOpen = false;
                            }
                        } finally {
                            this.saving = false;
                        }
                    },
                })
            );
        });
    </script>
@endonce


<div
    x-data="heroHitDice(
        @js($hitDice->all()),
        {{ $constitutionModifier }},
        @js(route('characters.combat.update', $character)),
        @js(url('/api/roll'))
    )"

    @keydown.escape.window="
        closeModal()
    "

    @character-rest-completed.window="
        syncAfterRest($event.detail)
    "

    class="
                hero-hit-dice-roll-result
        hero-hit-dice
        hero-hit-dice-v2
        relative
        w-fit
        max-w-full
    "
>

    {{-- ============================================================
         ESTRUTURA PRINCIPAL
    ============================================================= --}}

    <div class="relative">

        {{-- ========================================================
             CONTROLE COMPACTO DOS DADOS DE VIDA
        ========================================================= --}}

        <div
            class="
                hero-hit-dice-compact
                relative
                z-20
                flex
                items-center
                overflow-hidden
                rounded-full
                border
            "
        >
            {{-- NOME / ABRIR MODAL --}}
            <button
                type="button"

                @click="
                    openModal('history')
                "

                title="Abrir Dados de Vida"

                class="
                    hero-hit-dice-label
                    group
                    relative
                    z-10
                    flex
                    h-full
                    shrink-0
                    items-center
                    gap-1.5
                    px-2
                    py-1
                    transition
                "
            >
                <span
                    class="
                        hero-hit-dice-status-dot
                        h-1.5
                        w-1.5
                        shrink-0
                        rounded-full
                    "
                    :class="
                        remainingClass
                    "
                    aria-hidden="true"
                ></span>

                <span
                    class="
                        text-[7px]
                        font-black
                        uppercase
                        tracking-[0.13em]
                        text-[#8c6239]
                        transition
                        group-hover:text-[#53150f]
                    "
                >
                    Dados de Vida
                </span>
            </button>


            {{-- DADOS --}}
            <div
                class="
                    hero-hit-dice-values
                    relative
                    z-10
                    flex
                    items-center
                    gap-0.5
                    px-1.5
                    py-0.5
                "
            >
                <template
                    x-for="
                        (die, index) in dice
                    "
                    :key="
                        `${die.die}-${index}`
                    "
                >
                    <span
                        class="
                            hero-hit-dice-token
                            inline-flex
                            items-center
                        "
                    >
                        {{-- GASTAR --}}
                        <button
                            type="button"

                            @click="
                                spendDie(die)
                            "

                            :disabled="
                                rolling ||
                                parseInt(die.current) <= 0
                            "

                            title="Gastar 1 Dado de Vida"

                            class="
                                hero-hit-dice-spend
                                flex
                                h-5
                                w-4
                                items-center
                                justify-center
                                text-[10px]
                                font-black
                                transition
                                active:scale-90
                                disabled:cursor-not-allowed
                                disabled:opacity-20
                            "
                            aria-label="Gastar um Dado de Vida"
                        >
                            −
                        </button>


                        {{-- ROLAR --}}
                        <button
                            type="button"

                            @click="
                                roll(die)
                            "

                            :disabled="
                                rolling ||
                                parseInt(die.current) <= 0
                            "

                            title="Rolar Dado de Vida"

                            class="
                                hero-hit-dice-roll
                                px-0.5
                                py-0.5
                                font-serif
                                text-[10px]
                                font-black
                                tracking-wide
                                transition
                                active:scale-95
                                disabled:cursor-not-allowed
                                disabled:opacity-25
                            "

                            x-text="
                                `${die.current}${die.die}`
                            "
                        ></button>


                        <span
                            x-show="
                                index <
                                dice.length - 1
                            "

                            class="
                                hero-hit-dice-separator
                                mx-0.5
                                text-[9px]
                            "
                        >
                            /
                        </span>
                    </span>
                </template>


                <span
                    x-show="
                        dice.length === 0
                    "

                    class="
                        px-1
                        text-[9px]
                        font-bold
                        text-[#a09383]
                    "
                >
                    0d0
                </span>
            </div>
        </div>


        {{-- ========================================================
             RESULTADO DA ROLAGEM
             ABRE PARA BAIXO
        ========================================================= --}}

        <div
            x-show="
                activeRoll.visible
            "

            x-cloak

            x-transition:enter="
                transition
                ease-out
                duration-180
            "

            x-transition:enter-start="
                opacity-0
                -translate-y-1
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
                duration-140
            "

            x-transition:leave-start="
                opacity-100
                translate-y-0
                scale-100
            "

            x-transition:leave-end="
                opacity-0
                -translate-y-1
                scale-[.97]
            "

            class="
                absolute
                right-0
                top-full
                z-50

                mt-1.5

                min-w-[138px]

                overflow-hidden

                rounded-b-xl
                rounded-t-md

                border
                border-[#cdbb9f]/80

                bg-[#faf8f2]

                px-3
                pb-2.5
                pt-2

                text-center

                shadow-[0_7px_18px_rgba(83,21,15,.12)]
            "
        >

            {{-- CONEXÃO VISUAL --}}

            <div
                class="
                    pointer-events-none
                    absolute
                    left-1/2
                    top-0

                    h-px
                    w-[80%]

                    -translate-x-1/2

                    bg-[#d8c7ab]/70
                "
            ></div>


            {{-- TÍTULO --}}

            <div
                class="
                    text-[6.5px]
                    font-black
                    uppercase
                    tracking-[0.18em]
                    text-[#8c6239]
                "
            >
                Rolagem
            </div>


            {{-- RESULTADOS --}}

            <div
                class="
                    hit-dice-result
                    mt-1
                    font-serif
                    text-[15px]
                    font-black
                    leading-none
                    text-[#53150f]
                "

                x-text="
                    activeRollSummary
                "
            ></div>


            {{-- CÁLCULO --}}

            <div
                class="
                    mt-1.5
                    flex
                    items-center
                    justify-center
                    gap-1
                    text-[7.5px]
                    text-[#8c7f70]
                "
            >

                <span>
                    Soma
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        activeRoll.total
                    "
                ></span>

                <span
                    class="
                        text-[#b5a898]
                    "
                >
                    +
                </span>

                <span>
                    CON
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        activeConstitutionTotal >= 0
                            ? '+' + activeConstitutionTotal
                            : activeConstitutionTotal
                    "
                ></span>

            </div>


            {{-- TOTAL RECUPERADO --}}

            <div
                class="
                    mt-1.5

                    border-t
                    border-[#e8dfd1]

                    pt-1.5
                "
            >

                <span
                    class="
                        text-[6px]
                        font-black
                        uppercase
                        tracking-[0.16em]
                        text-[#8c6239]
                    "
                >
                    Recupera
                </span>

                <div
                    class="
                        hero-hit-dice-heal-total
                        mt-0.5
                        font-serif
                        text-[15px]
                        font-black
                        leading-none
                        text-[#8c1d18]
                    "
                >
                    +

                    <span
                        x-text="
                            activeRollTotal
                        "
                    ></span>

                    PV
                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         MODAL
    ============================================================= --}}

    <template x-teleport="body">

        <div
            x-show="
                modalOpen
            "

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
            aria-label="Dados de Vida"

            class="
                character-sheet-theme
                character-theme-modal-layer

                fixed
                inset-0
                z-[120]

                flex
                items-center
                justify-center

                p-4
            "
        >

            {{-- BACKDROP --}}

            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "

                @click="
                    closeModal()
                "
            ></div>


            {{-- PAINEL --}}

            <div
                x-show="
                    modalOpen
                "

                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                @click.stop

                class="
                    character-theme-modal
                    hero-hit-dice-modal

                    relative
                    z-10

                    flex
                    max-h-[92dvh]
                    w-full
                    max-w-[620px]
                    flex-col

                    overflow-hidden

                    rounded-2xl

                    border
                    border-[#cdbb9f]

                    bg-[#faf7f2]

                    shadow-2xl
                "
            >

                {{-- =================================================
                     HEADER
                ================================================== --}}

                <div
                    class="
                        character-theme-modal-header
                        hero-hit-dice-modal-header
                        flex
                        items-center
                        justify-between

                        border-b
                        border-[#e8dfd1]

                        bg-[#efe9dc]/70

                        px-4
                        py-3
                    "
                >

                    <div class="min-w-0">

                        <p
                            class="
                                hero-hit-dice-modal-kicker
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.14em]
                                text-[#8c7f70]
                            "
                        >
                            Recurso de descanso
                        </p>

                        <h3
                            class="
                                character-theme-modal-title
                                hero-hit-dice-modal-title
                                mt-0.5
                                font-serif
                                text-base
                                font-black
                                text-[#53150f]
                            "
                        >
                            Dados de Vida
                        </h3>

                        <p
                            class="
                                hero-hit-dice-modal-subtitle
                                mt-1
                                text-[11px]
                                leading-snug
                                text-[#8c7f70]
                            "
                        >
                            Gerencie os dados disponíveis para recuperação durante descansos.
                        </p>

                    </div>


                    <button
                        type="button"

                        @click="
                            closeModal()
                        "

                        class="
                            hero-hit-dice-modal-close
                            flex
                            h-7
                            w-7
                            items-center
                            justify-center

                            rounded-lg

                            text-[#8c7f70]

                            transition

                            hover:bg-[#e6d9c7]
                            hover:text-[#53150f]
                        "
                    >
                        ×
                    </button>

                </div>


                {{-- =================================================
                     ABAS
                ================================================== --}}

                <div
                    class="
                        hero-hit-dice-tabs
                        flex

                        border-b
                        border-[#e8dfd1]

                        bg-[#f5efe6]

                        px-2
                    "
                >

                    <button
                        type="button"

                        @click="
                            modalTab = 'history'
                        "

                        class="
                            hero-hit-dice-tab
                            relative
                            flex-1
                            py-2

                            text-[8px]
                            font-black
                            uppercase
                            tracking-widest

                            transition
                        "

                        :class="{
                            'is-active':
                                modalTab === 'history'
                        }"
                    >
                        Histórico

                    </button>


                    <button
                        type="button"

                        @click="
                            modalTab = 'config'
                        "

                        class="
                            hero-hit-dice-tab
                            relative
                            flex-1
                            py-2

                            text-[8px]
                            font-black
                            uppercase
                            tracking-widest

                            transition
                        "

                        :class="{
                            'is-active':
                                modalTab === 'config'
                        }"
                    >
                        Configurar

                    </button>

                </div>


                {{-- =================================================
                     HISTÓRICO
                ================================================== --}}

                <div
                    x-show="
                        modalTab === 'history'
                    "

                    class="
                        hero-hit-dice-modal-body
                        hero-hit-dice-history
                        min-h-0
                        flex-1
                        overflow-y-auto
                        p-4
                    "
                >

                    <div
                        class="
                            mb-3
                            flex
                            items-center
                            justify-between
                            gap-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c7f70]
                                "
                            >
                                Restauração
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    font-serif
                                    text-sm
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Rolagens recentes
                            </p>

                        </div>


                        <div
                            class="
                                shrink-0
                                rounded-md
                                border
                                border-[#d8cdbc]/70
                                bg-[#efe9dc]/70
                                px-2
                                py-1
                                text-[7.5px]
                                font-black
                                uppercase
                                tracking-wide
                                text-[#8c6239]
                            "
                        >
                            CON
                            {{ $constitutionModifier >= 0 ? '+' : '' }}{{ $constitutionModifier }}
                            / dado
                        </div>

                    </div>


                    <div class="space-y-1.5">

                        <template
                            x-for="
                                entry in history
                            "

                            :key="
                                entry.id
                            "
                        >

                            <div
                                class="
                                    hero-hit-dice-history-card
                                    rounded-lg
                                    border
                                    border-[#e4d9ca]
                                    bg-white/65
                                    px-2.5
                                    py-2
                                "
                            >

                                <div
                                    class="
                                        flex
                                        flex-wrap
                                        items-center
                                        gap-x-1.5
                                        gap-y-1
                                    "
                                >

                                    {{-- DADOS ROLADOS --}}

                                    <template
                                        x-for="
                                            (group, groupIndex) in entry.groups
                                        "

                                        :key="
                                            `${entry.id}-${group.die}`
                                        "
                                    >

                                        <span
                                            class="
                                                inline-flex
                                                min-w-0
                                                items-baseline
                                                gap-1
                                            "
                                        >

                                            <span
                                                class="
                                                    font-serif
                                                    text-[10px]
                                                    font-black
                                                    lowercase
                                                    text-[#53150f]
                                                "

                                                x-text="
                                                    group.die
                                                "
                                            ></span>

                                            <span
                                                class="
                                                    text-[8px]
                                                    text-[#b5a898]
                                                "
                                            >
                                                —
                                            </span>

                                            <span
                                                class="
                                                    font-serif
                                                    text-[10px]
                                                    font-bold
                                                    tracking-[0.01em]
                                                    text-[#6b1d14]
                                                "

                                                x-text="
                                                    group.values.join(', ')
                                                "
                                            ></span>

                                            <span
                                                x-show="
                                                    groupIndex <
                                                    entry.groups.length - 1
                                                "

                                                class="
                                                    ml-0.5
                                                    text-[8px]
                                                    text-[#c0b3a3]
                                                "
                                            >
                                                /
                                            </span>

                                        </span>

                                    </template>


                                    {{-- CONSTITUIÇÃO --}}

                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            text-[#b5a898]
                                        "

                                        x-text="
                                            entry.constitutionTotal >= 0
                                                ? '+'
                                                : '−'
                                        "
                                    ></span>

                                    <span
                                        class="
                                            inline-flex
                                            items-baseline
                                            gap-1
                                        "
                                    >

                                        <span
                                            class="
                                                font-serif
                                                text-[10px]
                                                font-black
                                                text-[#8c6239]
                                            "

                                            x-text="
                                                Math.abs(
                                                    entry.constitutionTotal
                                                )
                                            "
                                        ></span>

                                        <span
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#8c7f70]
                                            "
                                        >
                                            CON
                                        </span>

                                    </span>


                                    {{-- TOTAL --}}

                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            text-[#b5a898]
                                        "
                                    >
                                        =
                                    </span>

                                    <span
                                        class="
                                            inline-flex
                                            items-baseline
                                            gap-1
                                        "
                                    >

                                        <span
                                            class="
                                                hero-hit-dice-heal-total
                                                font-serif
                                                text-[13px]
                                                font-black
                                                text-[#8c1d18]
                                            "

                                            x-text="
                                                entry.totalHealed
                                            "
                                        ></span>

                                        <span
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#8c1d18]/75
                                            "
                                        >
                                            PV
                                        </span>

                                    </span>

                                </div>


                                <div
                                    class="
                                        mt-1
                                        flex
                                        items-center
                                        gap-1.5
                                        border-t
                                        border-[#eee6da]
                                        pt-1
                                    "
                                >

                                    <span
                                        class="
                                            h-px
                                            flex-1
                                            bg-[#eee6da]
                                        "
                                    ></span>

                                    <span
                                        class="
                                            text-[6.5px]
                                            font-bold
                                            tabular-nums
                                            text-[#a09383]
                                        "

                                        x-text="
                                            entry.time
                                        "
                                    ></span>

                                </div>

                            </div>

                        </template>


                        {{-- VAZIO --}}

                        <div
                            x-show="
                                history.length === 0
                            "

                            class="
                                hero-hit-dice-empty-history

                                rounded-xl
                                border
                                border-dashed
                                px-4
                                py-7
                                text-center
                            "
                        >

                            <div
                                class="
                                    font-serif
                                    text-lg
                                    text-[#a09383]
                                "
                            >
                                d
                            </div>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c7f70]
                                "
                            >
                                Nenhuma rolagem ainda
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    text-[#a09383]
                                "
                            >
                                Role um Dado de Vida para registrar o resultado.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     CONFIGURAÇÃO
                ================================================== --}}

                <div
                    x-show="
                        modalTab === 'config'
                    "

                    x-cloak

                    class="
                        hero-hit-dice-modal-body
                        hero-hit-dice-config
                        min-h-0
                        flex-1
                        overflow-y-auto
                        p-4
                    "
                >

                    <div
                        class="
                            hero-hit-dice-config-heading
                            mb-4
                            flex
                            items-end
                            justify-between
                            gap-4
                        "
                    >

                        <div class="min-w-0">
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.11em]
                                    text-[#8c7f70]
                                "
                            >
                                Dados atuais
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[11px]
                                    leading-snug
                                    text-[#a09383]
                                "
                            >
                                Ajuste o que ainda está disponível e a capacidade máxima de cada dado.
                            </p>
                        </div>

                        <div
                            class="
                                hero-hit-dice-con-badge
                                shrink-0
                                rounded-lg
                                border
                                px-2.5
                                py-1.5
                                text-right
                            "
                        >
                            <span
                                class="
                                    block
                                    text-[7px]
                                    font-black
                                    uppercase
                                    tracking-[0.10em]
                                "
                            >
                                Constituição
                            </span>

                            <strong
                                class="
                                    mt-0.5
                                    block
                                    font-serif
                                    text-[13px]
                                    leading-none
                                "
                            >
                                {{ $constitutionModifier >= 0 ? '+' : '' }}{{ $constitutionModifier }}
                            </strong>
                        </div>

                    </div>


                    {{-- DADOS EXISTENTES --}}

                    <div
                        class="
                            hero-hit-dice-current-list
                            space-y-2
                        "
                    >

                        <template
                            x-for="
                                (die, index) in dice
                            "

                            :key="
                                `${die.die}-${index}`
                            "
                        >

                            <div
                                class="
                                    hero-hit-dice-config-row

                                    grid
                                    grid-cols-[62px_minmax(0,1fr)_34px]
                                    items-center
                                    gap-3

                                    rounded-xl
                                    border

                                    p-3
                                "
                            >

                                {{-- TIPO --}}
                                <div
                                    class="
                                        hero-hit-dice-type-badge

                                        flex
                                        h-[52px]
                                        flex-col
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border
                                    "
                                >
                                    <strong
                                        class="
                                            font-serif
                                            text-[15px]
                                            font-black
                                            uppercase
                                            leading-none
                                        "

                                        x-text="
                                            die.die
                                        "
                                    ></strong>

                                    <span
                                        class="
                                            mt-1
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]
                                        "
                                    >
                                        dado
                                    </span>
                                </div>


                                {{-- VALORES --}}
                                <div
                                    class="
                                        grid
                                        min-w-0
                                        grid-cols-2
                                        gap-2
                                    "
                                >

                                    <label
                                        class="
                                            hero-hit-dice-field
                                            block
                                            min-w-0
                                        "
                                    >
                                        <span
                                            class="
                                                block
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.09em]
                                            "
                                        >
                                            Disponíveis
                                        </span>

                                        <input
                                            type="number"
                                            min="0"

                                            :max="
                                                die.maximum
                                            "

                                            x-model.number="
                                                die.current
                                            "

                                            @change="
                                                die.current = Math.max(
                                                    0,
                                                    Math.min(
                                                        parseInt(die.maximum) || 0,
                                                        parseInt(die.current) || 0
                                                    )
                                                )
                                            "

                                            class="
                                                mt-1.5
                                                h-10
                                                w-full

                                                rounded-lg
                                                border

                                                px-2

                                                text-center
                                                font-serif
                                                text-[15px]
                                                font-black

                                                outline-none
                                            "
                                        >
                                    </label>


                                    <label
                                        class="
                                            hero-hit-dice-field
                                            block
                                            min-w-0
                                        "
                                    >
                                        <span
                                            class="
                                                block
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.09em]
                                            "
                                        >
                                            Máximo
                                        </span>

                                        <input
                                            type="number"
                                            min="0"

                                            :value="
                                                die.maximum
                                            "

                                            @input="
                                                updateDieMax(
                                                    die,
                                                    $event.target.value
                                                )
                                            "

                                            class="
                                                mt-1.5
                                                h-10
                                                w-full

                                                rounded-lg
                                                border

                                                px-2

                                                text-center
                                                font-serif
                                                text-[15px]
                                                font-black

                                                outline-none
                                            "
                                        >
                                    </label>

                                </div>


                                {{-- REMOVER --}}
                                <button
                                    type="button"

                                    @click="
                                        removeDie(index)
                                    "

                                    class="
                                        hero-hit-dice-remove

                                        flex
                                        h-8
                                        w-8
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border

                                        text-[15px]
                                        transition
                                    "

                                    title="Remover este tipo de dado"
                                    aria-label="Remover tipo de dado"
                                >
                                    ×
                                </button>

                            </div>

                        </template>


                        <div
                            x-show="
                                dice.length === 0
                            "

                            class="
                                hero-hit-dice-config-empty

                                rounded-xl
                                border
                                border-dashed

                                px-4
                                py-6

                                text-center
                            "
                        >
                            <p
                                class="
                                    font-serif
                                    text-sm
                                    font-black
                                "
                            >
                                Nenhum Dado de Vida configurado
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[10px]
                                "
                            >
                                Adicione um tipo abaixo para começar.
                            </p>
                        </div>

                    </div>


                    {{-- ADICIONAR DADO --}}

                    <div
                        class="
                            hero-hit-dice-add-panel
                            mt-4

                            rounded-xl

                            border
                            border-[#e2d7c5]

                            bg-[#f5efe6]

                            p-2.5
                        "
                    >

                        <div
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.10em]
                                text-[#8c7f70]
                            "
                        >
                            Adicionar dado
                        </div>

                        <p
                            class="
                                mt-1
                                text-[10px]
                                leading-snug
                                text-[#a09383]
                            "
                        >
                            Escolha um tipo. Se ele já existir, o máximo aumenta em 1.
                        </p>


                        <div
                            class="
                                mt-3
                                grid
                                grid-cols-3
                                gap-2
                                sm:grid-cols-6
                            "
                        >

                            <template
                                x-for="
                                    type in [
                                        'd4',
                                        'd6',
                                        'd8',
                                        'd10',
                                        'd12',
                                        'd20'
                                    ]
                                "

                                :key="
                                    type
                                "
                            >

                                <button
                                    type="button"

                                    @click="
                                        addDie(type)
                                    "

                                    class="
                                        hero-hit-dice-add-button
                                        rounded-lg

                                        border
                                        border-[#d8cdbc]

                                        bg-[#faf7f2]

                                        min-h-[36px]
                                        py-2

                                        font-serif
                                        text-[11px]
                                        font-black
                                        text-[#4a1813]

                                        transition

                                        hover:border-[#bfae98]
                                        hover:bg-white

                                        active:scale-95
                                    "

                                    x-text="
                                        '+' + type
                                    "
                                ></button>

                            </template>

                        </div>

                    </div>


                    {{-- RESUMO --}}

                    <div
                        class="
                            hero-hit-dice-summary
                            mt-4

                            rounded-xl
                            border

                            p-3.5
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                justify-between
                                gap-3
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.10em]
                                    "
                                >
                                    Resumo
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[10px]
                                        leading-snug
                                    "
                                >
                                    Estado atual dos recursos de recuperação.
                                </p>
                            </div>

                            <span
                                class="
                                    hero-hit-dice-summary-ratio

                                    shrink-0
                                    rounded-lg
                                    border

                                    px-2.5
                                    py-1.5

                                    font-serif
                                    text-[16px]
                                    font-black
                                    tabular-nums
                                "
                            >
                                <span
                                    x-text="
                                        totalCurrent
                                    "
                                ></span>

                                <span class="opacity-40">/</span>

                                <span
                                    x-text="
                                        totalMaximum
                                    "
                                ></span>
                            </span>
                        </div>


                        <div
                            class="
                                mt-3
                                grid
                                grid-cols-2
                                gap-2
                            "
                        >

                            <div
                                class="
                                    hero-hit-dice-summary-card
                                    rounded-lg
                                    border
                                    p-2.5
                                "
                            >
                                <span
                                    class="
                                        block
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.09em]
                                    "
                                >
                                    Disponíveis
                                </span>

                                <strong
                                    class="
                                        mt-1
                                        block
                                        font-serif
                                        text-[14px]
                                        font-black
                                        leading-tight
                                    "

                                    x-text="
                                        currentSummary
                                    "
                                ></strong>
                            </div>


                            <div
                                class="
                                    hero-hit-dice-summary-card
                                    rounded-lg
                                    border
                                    p-2.5
                                "
                            >
                                <span
                                    class="
                                        block
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.09em]
                                    "
                                >
                                    Capacidade
                                </span>

                                <strong
                                    class="
                                        mt-1
                                        block
                                        font-serif
                                        text-[14px]
                                        font-black
                                        leading-tight
                                    "

                                    x-text="
                                        maximumSummary
                                    "
                                ></strong>
                            </div>

                        </div>

                    </div>



                </div>


                {{-- =================================================
                     AÇÕES DA CONFIGURAÇÃO
                ================================================== --}}

                <div
                    x-show="
                        modalTab === 'config'
                    "

                    x-cloak

                    class="
                        character-theme-modal-footer
                        hero-hit-dice-modal-footer

                        flex
                        shrink-0
                        items-center
                        justify-end
                        gap-2

                        border-t

                        px-5
                        py-3.5
                    "
                >

                    <button
                        type="button"

                        @click="
                            closeModal()
                        "

                        :disabled="
                            saving
                        "

                        class="
                            hero-hit-dice-secondary

                            rounded-lg

                            px-4
                            py-2.5

                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.06em]

                            transition

                            disabled:opacity-50
                        "
                    >
                        Cancelar
                    </button>


                    <button
                        type="button"

                        @click="
                            saveConfig()
                        "

                        :disabled="
                            saving
                        "

                        class="
                            hero-hit-dice-primary

                            min-w-[96px]

                            rounded-lg
                            border

                            px-4
                            py-2.5

                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.06em]

                            transition

                            disabled:opacity-50
                        "
                    >

                        <span
                            x-show="
                                !saving
                            "
                        >
                            Salvar
                        </span>

                        <span
                            x-show="
                                saving
                            "

                            x-cloak
                        >
                            Salvando...
                        </span>

                    </button>

                </div>

            </div>

        </div>

    </template>

</div>