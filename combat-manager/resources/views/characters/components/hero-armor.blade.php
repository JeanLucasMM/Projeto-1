@props(['character'])

@php
    $armorAbilityOptions = [
        'str' => 'FOR',
        'dex' => 'DES',
        'con' => 'CON',
        'int' => 'INT',
        'wis' => 'SAB',
        'cha' => 'CAR',
    ];

    /*
     * O modal é teleportado para <body>, então precisa receber o tema
     * novamente para continuar consumindo os mesmos tokens da ficha.
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
            /* ==========================================================
               CLASSE DE ARMADURA — CONTRATO VISUAL UNIFICADO
            ========================================================== */

            .character-sheet-theme[data-theme] .character-armor-trigger {
                color: var(--theme-text-primary);
            }

            .character-sheet-theme[data-theme] .character-armor-trigger-shape {
                fill: var(--theme-surface-card) !important;
                stroke: var(--theme-border-strong) !important;
                transition: fill .16s ease, stroke .16s ease, filter .16s ease;
            }

            .character-sheet-theme[data-theme] .character-armor-trigger-detail {
                stroke: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-trigger.is-equipped
            .character-armor-trigger-shape {
                fill: var(--theme-accent-softer) !important;
                stroke: var(--theme-accent) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-trigger:hover
            .character-armor-trigger-shape {
                stroke: var(--theme-accent) !important;
                filter: drop-shadow(0 3px 6px color-mix(in srgb, var(--theme-accent-deep) 12%, transparent));
            }

            .character-sheet-theme[data-theme] .character-armor-trigger
            [class*="text-[#8c6239]"] {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-trigger
            [class*="text-[#53150f]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal {
                color: var(--theme-text-primary);
            }

            .character-sheet-theme[data-theme] .character-armor-modal-header,
            .character-sheet-theme[data-theme] .character-armor-modal-footer {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-title {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-label {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-copy {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-body {
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal section {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
                box-shadow: var(--theme-module-shadow) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal section > button:first-child {
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal section > button:first-child:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal input {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal input::placeholder {
                color: var(--theme-input-placeholder) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal input:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-input-focus) 14%, transparent) !important;
            }

            /* Tipografia: texto neutro; tema como acento. */
            .character-sheet-theme[data-theme] .character-armor-modal [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="text-[#6b1d14]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="text-[#7d604d]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="text-[#6f4f38]"] {
                color: var(--theme-text-secondary) !important;
            }

            /* Bordas e superfícies legadas do partial. */
            .character-sheet-theme[data-theme] .character-armor-modal [class*="border-[#d6c09f]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="border-[#d8c7ab]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="border-[#c9ae8a]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="border-[#cdbb9f]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="border-[#b9966c]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="border-[#c5a783]"] {
                border-color: var(--theme-border) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#fffdfa]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#fffaf2]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#fffaf6]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#f5efe6]"] {
                background: var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#f0e2d2]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#f3eadf]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#f8efe3]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#f7eee2]"] {
                background: var(--theme-accent-softer) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#8c6239]"],
            .character-sheet-theme[data-theme] .character-armor-modal [class*="bg-[#6b1d14]"] {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-result {
                border: 1px solid color-mix(in srgb, var(--theme-accent-strong) 45%, transparent);
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-close {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-close:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-primary {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
                box-shadow: var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.08)) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-primary:hover {
                background: var(--theme-button-hover-background) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-secondary {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-secondary:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-ability-choice {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-ability-choice:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-ability-choice.is-active {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-equipment-action {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-armor-equipment-action.is-available {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            /* Menos informação concorrendo pela atenção. */
            .character-armor-modal-header-summary,
            .character-armor-modal-base-caption,
            .character-armor-modal-base-extras,
            .character-armor-modal-equipment-status,
            .character-armor-modal-equipment-count,
            .character-armor-modal-extras-help,
            .character-armor-modal-footer-note {
                display: none !important;
            }

            .character-sheet-theme[data-theme] .character-armor-modal-footer {
                justify-content: flex-end !important;
                gap: 8px !important;
                box-shadow: 0 -8px 22px color-mix(in srgb, var(--theme-accent-deep) 4%, transparent);
            }

            @media (max-width: 640px) {
                .character-sheet-theme[data-theme] .character-armor-modal {
                    max-height: 94dvh !important;
                    border-radius: 15px !important;
                }

                .character-sheet-theme[data-theme] .character-armor-modal-header,
                .character-sheet-theme[data-theme] .character-armor-modal-footer,
                .character-sheet-theme[data-theme] .character-armor-modal-body {
                    padding-inline: 15px !important;
                }
            }
        </style>
    @endpush
@endonce

<div
    x-data="{
        baseDefenseDrawerOpen: false,
        armorDrawerOpen: false,
        shieldDrawerOpen: false,
    }"
    @keydown.escape.window="
        if (armorOpen) {
            armorOpen = false
        }
    "
    class="relative flex justify-center"
>
    {{-- ============================================================
         ESCUDO DE CA
    ============================================================= --}}
    <div class="relative translate-y-7">
        <button
            type="button"
            @click="openArmor()"
            class="character-armor-trigger group relative h-24 w-20 focus:outline-none"
            :class="{ 'is-equipped': !!activeShieldItem }"
            title="Configurar Classe de Armadura"
        >
            <svg
                class="absolute inset-0 h-full w-full"
                viewBox="0 0 80 96"
                preserveAspectRatio="none"
                fill="none"
            >
                <path
                    class="character-armor-trigger-shape"
                    d="
                        M40 2
                        L73 13
                        V43
                        C73 63 60 79 40 91
                        C20 79 7 63 7 43
                        V13
                        Z
                    "
                    :fill="
                        activeShieldItem
                            ? '#f7f0e6'
                            : '#faf8f2'
                    "
                    :stroke="
                        activeShieldItem
                            ? '#b08c62'
                            : '#cdbb9f'
                    "
                    stroke-width="2"
                />

                <path
                    class="character-armor-trigger-detail"
                    d="
                        M40 8
                        L66 17
                        V42
                        C66 58 56 72 40 82
                        C24 72 14 58 14 42
                        V17
                        Z
                    "
                    :stroke="activeShieldItem ? '#8c6239' : '#8c6239'"
                    stroke-width="1"
                    opacity=".24"
                />
            </svg>

            <div class="relative z-10 flex h-full flex-col items-center justify-center pt-0.5">
                <span class="-mt-1 text-[7px] font-black uppercase leading-none tracking-[0.16em] text-[#8c6239]">
                    Classe de
                </span>

                <span class="mt-0.5 text-[7px] font-black uppercase leading-none tracking-[0.16em] text-[#8c6239]">
                    Armadura
                </span>

                <span
                    x-text="totalAc"
                    class="relative z-10 -mt-0.5 font-serif text-2xl font-black leading-none text-[#53150f]"
                ></span>

            </div>
        </button>
    </div>

    {{-- ============================================================
         MODAL
    ============================================================= --}}
    <template x-teleport="body">
        <div
            x-show="armorOpen"
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
                fixed inset-0 z-[180]
                flex items-center justify-center p-4
            "
        >
            <div
                @click="armorOpen = false"
                class="character-theme-modal-backdrop absolute inset-0"
            ></div>

            <div
                x-show="armorOpen"

                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                @click.stop
                class="
                    character-theme-modal
                    character-armor-modal
                    relative z-10 flex
                    max-h-[92dvh] w-full max-w-[760px]
                    flex-col overflow-hidden rounded-2xl border
                "
            >
                {{-- HEADER --}}
                <header
                    class="
                        character-theme-modal-header
                        character-armor-modal-header
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-4
                        border-b
                        border-[#a0774d]/30
                        bg-[#eadbc8]
                        px-5
                        py-3.5
                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                    "
                >
                    <div class="min-w-0 flex-1">

                        <div
                            class="
                                flex
                                min-w-0
                                items-baseline
                                gap-2.5
                            "
                        >
                            <h2
                                class="
                                    truncate

                                    font-serif
                                    text-xl
                                    font-black

                                    character-theme-modal-title
                                    character-armor-modal-title
                                    text-[#53150f]
                                "
                            >
                                Classe de Armadura
                            </h2>

                            <span
                                class="
                                    shrink-0

                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.10em]

                                    character-armor-modal-label
                                    text-[#8c6239]/75
                                "

                                x-text="armorDefenseLabel"
                            ></span>
                        </div>


                        <div
                            class="
                                character-armor-modal-header-summary

                                mt-2

                                flex
                                w-fit
                                max-w-full
                                min-w-0
                                flex-wrap
                                items-center
                                gap-x-1.5
                                gap-y-1

                                border-0

                                bg-transparent

                                px-0
                                py-0

                                shadow-none
                            "
                        >

                            <span
                                class="
                                    text-[11px]
                                    font-semibold
                                    leading-relaxed

                                    text-[#6f4f38]
                                "

                                x-text="
                                    activeArmorItem
                                        ? armorFormulaLabel(activeArmorItem)
                                        : baseDefenseFormulaLabel
                                "
                            ></span>


                            <span
                                x-show="
                                    equippedShieldBonus !== 0
                                "
                                x-cloak

                                class="
                                    text-[11px]
                                    font-semibold

                                    text-[#6f4f38]
                                "
                            >
                                ·
                                Escudo
                                <strong
                                    class="
                                        font-black
                                        text-[#53150f]
                                    "

                                    x-text="
                                        equippedShieldBonus > 0
                                            ? '+' + equippedShieldBonus
                                            : equippedShieldBonus
                                    "
                                ></strong>
                            </span>


                            <span
                                x-show="
                                    armorNamedBonusTotal !== 0
                                "
                                x-cloak

                                class="
                                    text-[11px]
                                    font-semibold

                                    text-[#6f4f38]
                                "
                            >
                                ·
                                Extras
                                <strong
                                    class="
                                        font-black
                                        text-[#53150f]
                                    "

                                    x-text="
                                        armorNamedBonusTotal > 0
                                            ? '+' + armorNamedBonusTotal
                                            : armorNamedBonusTotal
                                    "
                                ></strong>
                            </span>

                        </div>

                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <div
                            class="
                                flex
                                min-w-[54px]
                                flex-col
                                items-center
                                justify-center

                                pl-3
                                pr-1
                            "
                        >
                            <span
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    leading-none
                                    tracking-[0.14em]

                                    text-[#7d604d]/75
                                "
                            >
                                CA
                            </span>

                            <span
                                x-text="totalAc"

                                class="
                                    mt-1

                                    font-serif
                                    text-[26px]
                                    font-black
                                    leading-[0.86]

                                    text-[#53150f]
                                "
                            ></span>
                        </div>

                        <button
                            type="button"
                            @click="armorOpen = false"
                            class="character-armor-modal-close flex h-9 w-9 items-center justify-center rounded-lg transition"
                            title="Fechar"
                        >
                            ×
                        </button>
                    </div>
                </header>

                {{-- BODY --}}
                <div
                    class="
                        character-armor-modal-body
                        min-h-0
                        flex-1
                        space-y-3
                        overflow-y-auto
                        overflow-x-hidden
                        px-5
                        pt-[12px]
                        pb-[12px]
                        [scrollbar-width:none]
                        [&::-webkit-scrollbar]:hidden
                    "
                >
                    <div
                        x-show="armorEquipmentError"
                        x-cloak
                        class="rounded-lg border border-red-300/70 bg-red-50 px-3 py-2 text-[12px] font-bold text-red-800"
                        x-text="armorEquipmentError"
                    ></div>

                    {{-- DEFESA BASE --}}

                    <section
                        class="
                            overflow-hidden

                            rounded-xl
                            border
                            border-[#d6c09f]/85

                            bg-[linear-gradient(180deg,#fffdfa_0%,#fbf5ec_100%)]

                            shadow-[0_2px_8px_rgba(83,61,42,.055)]
                        "
                    >

                        {{-- CABEÇALHO / RESUMO DO CÁLCULO --}}

                        <button
                            type="button"

                            @click="
                                baseDefenseDrawerOpen =
                                    !baseDefenseDrawerOpen
                            "

                            class="
                                flex
                                w-full
                                items-center
                                justify-between
                                gap-4

                                px-3.5
                                py-3

                                text-left

                                transition

                                hover:bg-[#faf3e9]
                            "

                            :class="
                                baseDefenseDrawerOpen
                                    ? 'bg-[#faf3e9]'
                                    : ''
                            "

                            :aria-expanded="
                                baseDefenseDrawerOpen
                                    ? 'true'
                                    : 'false'
                            "
                        >

                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <span
                                        class="
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Defesa Base
                                    </span>

                                    <span
                                        class="
                                            character-armor-modal-base-caption

                                            text-[9px]
                                            font-semibold

                                            text-[#8c6239]/60
                                        "
                                    >
                                        cálculo sem armadura
                                    </span>
                                </div>


                                {{-- EQUAÇÃO VISÍVEL MESMO COM A GAVETA FECHADA --}}

                                <div
                                    class="
                                        mt-2

                                        flex
                                        min-w-0
                                        flex-wrap
                                        items-center
                                        gap-1.5
                                    "
                                >

                                    <span
                                        class="
                                            inline-flex
                                            h-7
                                            items-center

                                            rounded-md
                                            border
                                            border-[#d8c7ab]/75

                                            bg-[#fffaf2]

                                            px-2.5

                                            font-serif
                                            text-[13px]
                                            font-black

                                            text-[#53150f]
                                        "
                                    >
                                        10
                                    </span>


                                    @foreach ($armorAbilityOptions as $ability => $short)

                                        <template
                                            x-if="
                                                armorMode.includes(
                                                    '{{ $ability }}'
                                                )
                                            "
                                        >
                                            <div
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1.5
                                                "
                                            >

                                                <span
                                                    class="
                                                        font-serif
                                                        text-[15px]
                                                        font-bold

                                                        text-[#b08c62]
                                                    "
                                                >
                                                    +
                                                </span>


                                                <span
                                                    class="
                                                        inline-flex
                                                        h-7
                                                        items-center
                                                        gap-1

                                                        rounded-md
                                                        border
                                                        border-[#b9966c]/55

                                                        bg-[#f7ece0]

                                                        px-2.5

                                                        text-[10px]
                                                        font-black

                                                        text-[#6b1d14]
                                                    "
                                                >
                                                    {{ $short }}

                                                    <span
                                                        x-text="
                                                            abilityModifier('{{ $ability }}') >= 0
                                                                ? '+' + abilityModifier('{{ $ability }}')
                                                                : abilityModifier('{{ $ability }}')
                                                        "
                                                    ></span>
                                                </span>

                                            </div>
                                        </template>

                                    @endforeach


                                    <span
                                        class="
                                            mx-0.5

                                            font-serif
                                            text-[15px]
                                            font-bold

                                            text-[#b08c62]
                                        "
                                    >
                                        =
                                    </span>


                                    <strong
                                        class="
                                            character-armor-modal-result

                                            inline-flex
                                            h-7
                                            min-w-8
                                            items-center
                                            justify-center

                                            rounded-md

                                            bg-[#6b1d14]

                                            px-2.5

                                            font-serif
                                            text-[14px]
                                            font-black

                                            text-[#fffaf2]

                                            shadow-[0_2px_5px_rgba(83,21,15,.12)]
                                        "

                                        x-text="
                                            baseDefenseBodyAc
                                        "
                                    ></strong>


                                    <template
                                        x-if="
                                            armorNamedBonusTotal !== 0
                                        "
                                    >
                                        <div
                                            class="
                                                character-armor-modal-base-extras
                                                inline-flex
                                                items-center
                                                gap-1.5
                                            "
                                        >

                                            <span
                                                class="
                                                    ml-1

                                                    text-[9px]
                                                    font-bold

                                                    text-[#8c6239]/70
                                                "
                                            >
                                                com extras
                                            </span>


                                            <span
                                                class="
                                                    font-serif
                                                    text-[14px]
                                                    font-bold

                                                    text-[#b08c62]
                                                "
                                            >
                                                →
                                            </span>


                                            <strong
                                                class="
                                                    font-serif
                                                    text-[15px]
                                                    font-black

                                                    text-[#6b1d14]
                                                "

                                                x-text="
                                                    baseDefenseAc
                                                "
                                            ></strong>

                                        </div>
                                    </template>

                                </div>

                            </div>


                            {{-- CHEVRON --}}

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
                                    border-[#c9ae8a]/70

                                    bg-[#fffaf2]

                                    text-[#8c6239]

                                    shadow-[0_1px_3px_rgba(83,61,42,.06)]

                                    transition
                                "

                                :class="
                                    baseDefenseDrawerOpen
                                        ? 'rotate-180 bg-[#f0e2d2] text-[#6b1d14]'
                                        : ''
                                "

                                aria-hidden="true"
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        d="m7 10 5 5 5-5"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>

                        </button>


                        {{-- CONTEÚDO DA GAVETA --}}

                        <div
                            x-show="
                                baseDefenseDrawerOpen
                            "
                            x-cloak

                            x-transition:enter="transition ease-out duration-160"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-120"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"

                            class="
                                border-t
                                border-[#d8c7ab]/60

                                bg-[#fffdfa]/80

                                px-3.5
                                pb-3.5
                                pt-3
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

                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]

                                            text-[#8c6239]/75
                                        "
                                    >
                                        Atributos da Defesa Base
                                    </span>

                                    <p
                                        class="
                                            mt-0.5

                                            text-[10px]
                                            leading-relaxed

                                            text-[#7d604d]
                                        "
                                    >
                                        Selecione os modificadores que entram no cálculo acima.
                                    </p>

                                </div>


                                <div
                                    class="
                                        shrink-0
                                        text-right
                                    "
                                >

                                    <span
                                        class="
                                            block

                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]

                                            text-[#8c6239]/55
                                        "
                                    >
                                        Resultado
                                    </span>

                                    <strong
                                        class="
                                            font-serif
                                            text-[18px]
                                            font-black

                                            text-[#53150f]
                                        "

                                        x-text="
                                            baseDefenseAc
                                        "
                                    ></strong>

                                </div>

                            </div>


                            <div
                                class="
                                    mt-3

                                    grid
                                    grid-cols-3
                                    gap-1.5

                                    sm:grid-cols-6
                                "
                            >

                                @foreach ($armorAbilityOptions as $ability => $short)

                                    <button
                                        type="button"

                                        @click="
                                            armorMode =
                                                armorMode.includes('{{ $ability }}')
                                                    ? armorMode.filter(
                                                        entry =>
                                                            entry !== '{{ $ability }}'
                                                    )
                                                    : [
                                                        ...armorMode,
                                                        '{{ $ability }}'
                                                    ]
                                        "

                                        class="
                                            character-armor-ability-choice

                                            flex
                                            min-h-9
                                            items-center
                                            justify-center
                                            gap-1

                                            rounded-lg
                                            border

                                            px-2

                                            text-[10px]
                                            font-black

                                            transition
                                        "

                                        :class="{
                                            'is-active': armorMode.includes('{{ $ability }}')
                                        }"
                                    >

                                        <span>
                                            {{ $short }}
                                        </span>

                                        <span
                                            x-text="
                                                abilityModifier('{{ $ability }}') >= 0
                                                    ? '+' + abilityModifier('{{ $ability }}')
                                                    : abilityModifier('{{ $ability }}')
                                            "
                                        ></span>

                                    </button>

                                @endforeach

                            </div>

                        </div>

                    </section>


                    {{-- EQUIPAMENTOS --}}

                    <div
                        class="
                            grid
                            grid-cols-1
                            gap-3

                            lg:grid-cols-2
                        "
                    >

                    {{-- ARMADURAS --}}
                    <section
                        class="
                            overflow-hidden
                            rounded-xl
                            border
                            border-[#d6c09f]/85
                            bg-[#fffdfa]
                            shadow-[0_2px_7px_rgba(83,61,42,.05)]
                        "
                    >
                        <button
                            type="button"
                            @click="armorDrawerOpen = !armorDrawerOpen"
                            class="
                                flex
                                w-full
                                items-center
                                justify-between
                                gap-3
                                bg-[linear-gradient(180deg,#fffaf3_0%,#f7eee2_100%)]
                                px-3.5
                                py-3
                                text-left
                                transition
                                hover:bg-[#f2e6d7]
                            "
                            :class="
                                armorDrawerOpen
                                    ? 'border-b border-[#c9ae8a]/70 bg-[#f7eee2]'
                                    : ''
                            "
                            :aria-expanded="armorDrawerOpen ? 'true' : 'false'"
                        >
                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <strong
                                        class="
                                            font-serif
                                            text-[16px]
                                            leading-none

                                            text-[#53150f]
                                        "
                                    >
                                        Armadura
                                    </strong>

                                    <span
                                        x-show="activeArmorItem"
                                        x-cloak

                                        class="
                                            character-armor-modal-equipment-status

                                            inline-flex
                                            items-center
                                            gap-1

                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]

                                            text-[#8c6239]/75
                                        "
                                    >
                                        <span
                                            class="
                                                h-1.5
                                                w-1.5
                                                rounded-full
                                                bg-[#9b7655]
                                            "
                                        ></span>

                                        Equipado
                                    </span>
                                </div>


                                <span
                                    x-show="activeArmorItem"
                                    x-cloak

                                    class="
                                        mt-1
                                        block
                                        max-w-[240px]
                                        truncate

                                        text-[11px]
                                        font-semibold
                                        leading-none

                                        text-[#7d604d]
                                    "

                                    x-text="
                                        activeArmorItem
                                            ? activeArmorItem.name
                                            : ''
                                    "
                                ></span>

                            </div>

                            <div
                                class="
                                    flex
                                    shrink-0
                                    items-center
                                    gap-1.5
                                "
                            >
                                <span
                                    class="
                                        character-armor-modal-equipment-count

                                        min-w-6

                                        text-center
                                        text-[9px]
                                        font-black

                                        text-[#8c6239]/70
                                    "

                                    x-text="armorItems.length"
                                ></span>

                                <span
                                    class="
                                        flex
                                        h-8
                                        w-8
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border
                                        border-[#c9ae8a]/70

                                        bg-[#fffaf2]

                                        text-[#8c6239]

                                        shadow-[0_1px_3px_rgba(83,61,42,.06)]

                                        transition
                                    "

                                    :class="
                                        armorDrawerOpen
                                            ? 'rotate-180 bg-[#f0e2d2] text-[#6b1d14]'
                                            : ''
                                    "

                                    aria-hidden="true"
                                >
                                    <svg
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="m7 10 5 5 5-5"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </button>

                        <div
                            x-show="armorDrawerOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-160"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-120"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                        >

                        <div
                            x-show="armorItems.length === 0"
                            x-cloak
                            class="px-3 py-5 text-center text-[12px] text-[#8c6239]"
                        >
                            Nenhuma armadura criada no inventário.
                        </div>

                        <div
                            x-show="armorItems.length > 0"
                            x-cloak
                            class="divide-y divide-[#d8c7ab]/45"
                        >
                            <template
                                x-for="item in armorItems"
                                :key="'armor-item-' + item.id"
                            >
                                <div
                                    class="
                                        character-armor-equipment-row

                                        flex
                                        items-center
                                        gap-3
                                        px-3.5
                                        py-3
                                        transition
                                        hover:bg-[#faf4eb]
                                    "
                                    :class="
                                        item.equipped
                                            ? 'bg-[#f8efe3]'
                                            : ''
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            h-11
                                            w-11
                                            shrink-0
                                            items-center
                                            justify-center
                                            overflow-hidden
                                            rounded-lg
                                            border
                                            border-[#d8c7ab]/85
                                            bg-[#f5efe6]
                                        "
                                    >
                                        <template x-if="item.image_url">
                                            <img
                                                :src="item.image_url"
                                                :alt="item.name"
                                                class="h-full w-full object-cover"
                                            >
                                        </template>

                                        <template x-if="!item.image_url">
                                            <span class="font-serif text-[16px] text-[#8c6239]/45">
                                                ◇
                                            </span>
                                        </template>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <strong
                                                class="block min-w-0 flex-1 truncate font-serif text-[13px] text-[#53150f]"
                                                x-text="item.name"
                                            ></strong>
                                        </div>

                                        <p
                                            class="mt-0.5 truncate text-[11px] text-[#8c6239]"
                                            x-text="armorFormulaLabel(item)"
                                        ></p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <strong
                                            class="block font-serif text-[16px] text-[#53150f]"
                                            x-text="armorItemBodyAc(item)"
                                        ></strong>

                                        <button
                                            type="button"
                                            @click="toggleArmorItem(item)"
                                            :disabled="armorEquipmentBusyId !== null"
                                            class="
                                                character-armor-equipment-action

                                                mt-1
                                                rounded-md
                                                border
                                                min-w-[60px]
                                                px-2.5
                                                py-1.5
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                transition
                                                disabled:cursor-wait
                                                disabled:opacity-40
                                            "
                                            :class="{
                                                'is-available': !item.equipped
                                            }"
                                            x-text="
                                                item.equipped
                                                    ? 'Guardar'
                                                    : 'Equipar'
                                            "
                                        ></button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        </div>
                    </section>


                    {{-- ESCUDOS --}}
                    <section
                        class="
                            overflow-hidden
                            rounded-xl
                            border
                            border-[#d6c09f]/85
                            bg-[#fffdfa]
                            shadow-[0_2px_7px_rgba(83,61,42,.05)]
                        "
                    >
                        <button
                            type="button"
                            @click="shieldDrawerOpen = !shieldDrawerOpen"
                            class="
                                flex
                                w-full
                                items-center
                                justify-between
                                gap-3
                                bg-[linear-gradient(180deg,#fffaf3_0%,#f7eee2_100%)]
                                px-3.5
                                py-3
                                text-left
                                transition
                                hover:bg-[#f2e6d7]
                            "
                            :class="
                                shieldDrawerOpen
                                    ? 'border-b border-[#c9ae8a]/70 bg-[#f7eee2]'
                                    : ''
                            "
                            :aria-expanded="shieldDrawerOpen ? 'true' : 'false'"
                        >
                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <strong
                                        class="
                                            font-serif
                                            text-[16px]
                                            leading-none

                                            text-[#53150f]
                                        "
                                    >
                                        Escudo
                                    </strong>

                                    <span
                                        x-show="activeShieldItem"
                                        x-cloak

                                        class="
                                            character-armor-modal-equipment-status

                                            inline-flex
                                            items-center
                                            gap-1

                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]

                                            text-[#8c6239]/75
                                        "
                                    >
                                        <span
                                            class="
                                                h-1.5
                                                w-1.5
                                                rounded-full
                                                bg-[#9b7655]
                                            "
                                        ></span>

                                        Equipado
                                    </span>
                                </div>


                                <span
                                    x-show="activeShieldItem"
                                    x-cloak

                                    class="
                                        mt-1
                                        block
                                        max-w-[240px]
                                        truncate

                                        text-[11px]
                                        font-semibold
                                        leading-none

                                        text-[#7d604d]
                                    "

                                    x-text="
                                        activeShieldItem
                                            ? activeShieldItem.name
                                            : ''
                                    "
                                ></span>

                            </div>

                            <div
                                class="
                                    flex
                                    shrink-0
                                    items-center
                                    gap-1.5
                                "
                            >
                                <span
                                    class="
                                        character-armor-modal-equipment-count

                                        min-w-6

                                        text-center
                                        text-[9px]
                                        font-black

                                        text-[#8c6239]/70
                                    "

                                    x-text="shieldItems.length"
                                ></span>

                                <span
                                    class="
                                        flex
                                        h-8
                                        w-8
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border
                                        border-[#c9ae8a]/70

                                        bg-[#fffaf2]

                                        text-[#8c6239]

                                        shadow-[0_1px_3px_rgba(83,61,42,.06)]

                                        transition
                                    "

                                    :class="
                                        shieldDrawerOpen
                                            ? 'rotate-180 bg-[#f0e2d2] text-[#6b1d14]'
                                            : ''
                                    "

                                    aria-hidden="true"
                                >
                                    <svg
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="m7 10 5 5 5-5"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </button>

                        <div
                            x-show="shieldDrawerOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-160"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-120"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                        >

                        <div
                            x-show="shieldItems.length === 0"
                            x-cloak
                            class="px-3 py-5 text-center text-[12px] text-[#8c6239]"
                        >
                            Nenhum escudo criado no inventário.
                        </div>

                        <div
                            x-show="shieldItems.length > 0"
                            x-cloak
                            class="divide-y divide-[#d8c7ab]/45"
                        >
                            <template
                                x-for="item in shieldItems"
                                :key="'shield-item-' + item.id"
                            >
                                <div
                                    class="
                                        character-armor-equipment-row

                                        flex
                                        items-center
                                        gap-3
                                        px-3.5
                                        py-3
                                        transition
                                        hover:bg-[#faf4eb]
                                    "
                                    :class="
                                        item.equipped
                                            ? 'bg-[#f8efe3]'
                                            : ''
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            h-11
                                            w-11
                                            shrink-0
                                            items-center
                                            justify-center
                                            overflow-hidden
                                            rounded-lg
                                            border
                                            border-[#d8c7ab]/85
                                            bg-[#f5efe6]
                                        "
                                    >
                                        <template x-if="item.image_url">
                                            <img
                                                :src="item.image_url"
                                                :alt="item.name"
                                                class="h-full w-full object-cover"
                                            >
                                        </template>

                                        <template x-if="!item.image_url">
                                            <span class="font-serif text-[16px] text-[#8c6239]/45">
                                                ◇
                                            </span>
                                        </template>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <strong
                                                class="block min-w-0 flex-1 truncate font-serif text-[13px] text-[#53150f]"
                                                x-text="item.name"
                                            ></strong>
                                        </div>

                                        <p
                                            class="mt-0.5 truncate text-[11px] text-[#8c6239]"
                                            x-text="shieldFormulaLabel(item)"
                                        ></p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <strong
                                            class="block font-serif text-[16px] text-[#53150f]"
                                            x-text="'+' + shieldItemBonus(item)"
                                        ></strong>

                                        <button
                                            type="button"
                                            @click="toggleShieldItem(item)"
                                            :disabled="armorEquipmentBusyId !== null"
                                            class="
                                                character-armor-equipment-action

                                                mt-1
                                                rounded-md
                                                border
                                                min-w-[60px]
                                                px-2.5
                                                py-1.5
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                transition
                                                disabled:cursor-wait
                                                disabled:opacity-40
                                            "
                                            :class="{
                                                'is-available': !item.equipped
                                            }"
                                            x-text="
                                                item.equipped
                                                    ? 'Guardar'
                                                    : 'Equipar'
                                            "
                                        ></button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        </div>
                    </section>


                    </div>

                    {{-- BÔNUS EXTRAS --}}
                    <section
                        class="
                            rounded-xl
                            border
                            border-[#d6c09f]/85
                            bg-[linear-gradient(180deg,#fffdfa_0%,#fbf5ec_100%)]
                            p-3.5
                            shadow-[0_2px_8px_rgba(83,61,42,.05)]
                        "
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <strong class="font-serif text-[15px] text-[#53150f]">
                                    Ajustes extras
                                </strong>

                                <p class="character-armor-modal-extras-help mt-0.5 text-[11px] text-[#8c6239]">
                                    Efeitos temporários, habilidades ou ajustes que não pertencem ao item.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="addArmorBonus()"
                                class="character-armor-modal-secondary rounded-md border px-2.5 py-1.5 text-[10px] font-black transition"
                            >
                                + Adicionar
                            </button>
                        </div>

                        <div class="mt-2 space-y-1.5">
                            <template
                                x-for="(bonus, index) in armorBonuses"
                                :key="'armor-extra-' + index"
                            >
                                <div class="flex items-center gap-1.5">
                                    <input
                                        type="text"
                                        x-model="bonus.name"
                                        placeholder="Ex.: Defesa sem Armadura"
                                        class="min-w-0 flex-1 rounded-md border border-[#cdbb9f]/70 bg-[#fffdfa] px-2.5 py-2 text-[11px] text-[#53150f] outline-none transition focus:border-[#9b7655] focus:ring-2 focus:ring-[#b08c62]/10"
                                    >

                                    <input
                                        type="number"
                                        x-model.number="bonus.value"
                                        class="w-14 rounded-md border border-[#cdbb9f]/70 bg-[#fffdfa] px-2 py-2 text-center text-[11px] font-black text-[#53150f] outline-none transition focus:border-[#9b7655] focus:ring-2 focus:ring-[#b08c62]/10"
                                    >

                                    <button
                                        type="button"
                                        @click="removeArmorBonus(index)"
                                        class="flex h-7 w-7 items-center justify-center rounded-md text-red-700 transition hover:bg-red-50"
                                        title="Remover"
                                    >
                                        ×
                                    </button>
                                </div>
                            </template>

                            <div
                                x-show="armorBonuses.length === 0"
                                x-cloak
                                class="rounded-lg border border-dashed border-[#d8c7ab]/85 px-3 py-2 text-center text-[11px] text-[#8c6239]"
                            >
                                Nenhum bônus extra.
                            </div>
                        </div>
                    </section>
                </div>

                {{-- FOOTER --}}
                <footer
                    class="
                        character-theme-modal-header
                        character-armor-modal-footer
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-4
                        border-t
                        border-[#c9ae8a]/65
                        bg-[linear-gradient(180deg,#f3eadf_0%,#eee2d4_100%)]
                        px-5
                        py-3.5
                    "
                >
                    <span
                        class="
                            character-armor-modal-footer-note

                            text-[10px]
                            font-semibold

                            text-[#8c6239]/75
                        "
                    >
                        Equipamentos são aplicados na hora. Base e bônus precisam ser salvos.
                    </span>


                    <button
                        type="button"
                        @click="armorOpen = false"
                        class="
                            character-armor-modal-secondary
                            inline-flex
                            min-h-10
                            items-center
                            justify-center
                            rounded-xl
                            border
                            px-4
                            text-[10.5px]
                            font-black
                            uppercase
                            tracking-[0.055em]
                            transition
                        "
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"

                        @click="saveArmor()"

                        :disabled="savingArmor"

                        class="
                            character-armor-modal-primary

                            inline-flex
                            min-w-[132px]
                            items-center
                            justify-center

                            rounded-xl
                            border
                            border-[#5b1812]/30

                            bg-[#6b1d14]

                            px-4
                            py-2.5

                            text-[11px]
                            font-black
                            leading-none

                            text-[#fffaf2]

                            shadow-[0_3px_8px_rgba(83,21,15,.15)]

                            transition

                            hover:-translate-y-px
                            hover:bg-[#53150f]
                            hover:shadow-[0_5px_12px_rgba(83,21,15,.20)]

                            disabled:cursor-wait
                            disabled:translate-y-0
                            disabled:opacity-50
                        "
                    >

                        <span x-show="!savingArmor">
                            Salvar ajustes
                        </span>


                        <span
                            x-show="savingArmor"
                            x-cloak
                        >
                            Salvando...
                        </span>

                    </button>
                </footer>
            </div>
        </div>
    </template>
</div>