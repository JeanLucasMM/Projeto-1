@props([
    'character',
])

@php
    $combat = $character->combat;

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE DANO PADRÃO
    |--------------------------------------------------------------------------
    |
    | A chave é o valor salvo no banco.
    | O label é apenas a apresentação em português.
    |
    */

    $damageTypes = [
        ['value' => 'acid', 'label' => 'Ácido'],
        ['value' => 'bludgeoning', 'label' => 'Contundente'],
        ['value' => 'cold', 'label' => 'Frio'],
        ['value' => 'fire', 'label' => 'Fogo'],
        ['value' => 'force', 'label' => 'Energia'],
        ['value' => 'lightning', 'label' => 'Elétrico'],
        ['value' => 'necrotic', 'label' => 'Necrótico'],
        ['value' => 'piercing', 'label' => 'Perfurante'],
        ['value' => 'poison', 'label' => 'Veneno'],
        ['value' => 'psychic', 'label' => 'Psíquico'],
        ['value' => 'radiant', 'label' => 'Radiante'],
        ['value' => 'slashing', 'label' => 'Cortante'],
        ['value' => 'thunder', 'label' => 'Trovejante'],
    ];

    $damageTypeLabels = collect($damageTypes)
        ->pluck('label', 'value')
        ->all();

    /*
    |--------------------------------------------------------------------------
    | NORMALIZAÇÃO DO VALOR VINDO DO MODEL
    |--------------------------------------------------------------------------
    |
    | Aceita array, Collection e JSON para manter compatibilidade com
    | registros antigos ou casts diferentes no CharacterCombat.
    |
    */

    $normalizeList = static function ($value): array {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            $value = is_array($decoded)
                ? $decoded
                : [];
        }

        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(
                fn ($entry) =>
                    trim((string) $entry)
            )
            ->filter()
            ->unique(
                fn (string $entry) =>
                    mb_strtolower($entry)
            )
            ->values()
            ->all();
    };

    $damageResistances =
        $normalizeList(
            $combat?->damage_resistances
        );

    $damageImmunities =
        $normalizeList(
            $combat?->damage_immunities
        );

    $damageVulnerabilities =
        $normalizeList(
            $combat?->damage_vulnerabilities
        );

    /* Tema atual para componentes teleportados */
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


{{-- ================================================================
     CORREÇÃO DE SOBREPOSIÇÃO COM ATRIBUTOS

     O wrapper .character-sheet-hero-defenses pertence ao show.blade
     e ocupa toda a largura da linha para posicionar Defesas apenas
     na coluna direita. A área vazia da coluna esquerda, porém, ainda
     recebe eventos de ponteiro e bloqueia os atributos abaixo.

     Desabilitamos eventos no wrapper e os reativamos somente no
     conteúdo real de Defesas. Assim a área transparente "atravessa"
     os cliques sem alterar a geometria atual.
================================================================= --}}
@once
    @push('styles')
        <style>
            .character-sheet-hero-defenses {
                pointer-events: none;
            }

            .character-sheet-hero-defenses > * {
                pointer-events: auto;
            }


            /* ==============================================================
               V22 — DEFESAS / PADRÃO VISUAL UNIFICADO
            ============================================================== */

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-summary {
                border-color: var(--theme-border) !important;
                background: var(--theme-module-background) !important;
                box-shadow: var(--theme-module-shadow) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-header {
                border-bottom-color: var(--theme-divider-strong, var(--theme-border)) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-title {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-title:hover {
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-summary-grid {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
                box-shadow: var(--theme-module-shadow) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-summary-grid > div {
                border-color: var(--theme-border-soft) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-summary-grid [class*="bg-[#eadbc8]"] {
                background: var(--theme-band-background) !important;
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-v22 .character-defenses-summary-grid [class*="bg-[#fffdf8]/80"] {
                background: var(--theme-surface-card) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-modal {
                width: min(820px, calc(100vw - 28px)) !important;
                max-height: min(92dvh, 880px) !important;
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                border-radius: 18px !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.30)) !important;
            }

            @supports not (height: 1dvh) {
                .character-sheet-theme[data-theme] .character-defenses-modal {
                    max-height: min(92vh, 880px) !important;
                }
            }

            .character-sheet-theme[data-theme] .character-defenses-modal-header,
            .character-sheet-theme[data-theme] .character-defenses-modal-footer {
                flex: 0 0 auto;
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-modal-body {
                min-height: 0;
                flex: 1 1 auto;
                overflow-y: auto;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                scrollbar-width: thin;
                scrollbar-color: color-mix(in srgb, var(--theme-label) 30%, transparent) transparent;
            }

            .character-sheet-theme[data-theme] .character-defenses-modal [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme] .character-defenses-modal [class*="text-[#6b1d14]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-modal [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme] .character-defenses-modal [class*="text-[#76553f]"],
            .character-sheet-theme[data-theme] .character-defenses-modal [class*="text-[#7a583f]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-modal-close {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-modal-close:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-tabs {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-tab {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-tab:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-tab[class*="border-[#6b1d14]"],
            .character-sheet-theme[data-theme] .character-defenses-tab[class*="border-[#7b2a21]"] {
                border-bottom-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-tab .character-defenses-tab-count {
                border: 1px solid var(--theme-border-soft);
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-option {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-option:hover {
                border-color: var(--theme-border-strong) !important;
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-option[class*="bg-[#7a2418]"],
            .character-sheet-theme[data-theme] .character-defenses-option[class*="bg-[#6b1d14]"],
            .character-sheet-theme[data-theme] .character-defenses-option[class*="bg-[#7b2a21]"] {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
                box-shadow: 0 3px 10px color-mix(in srgb, var(--theme-accent) 15%, transparent) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-option span.rounded-full {
                border-color: var(--theme-border) !important;
                background: var(--theme-input-bg) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-option[class*="bg-[#7a2418]"] span.rounded-full,
            .character-sheet-theme[data-theme] .character-defenses-option[class*="bg-[#6b1d14]"] span.rounded-full,
            .character-sheet-theme[data-theme] .character-defenses-option[class*="bg-[#7b2a21]"] span.rounded-full {
                border-color: color-mix(in srgb, var(--theme-button-text) 58%, transparent) !important;
                background: color-mix(in srgb, var(--theme-button-text) 10%, transparent) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-custom-card {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-custom-card input {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-custom-card input::placeholder {
                color: var(--theme-input-placeholder) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-custom-card input:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-input-focus) 15%, transparent) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-add-custom {
                border-color: var(--theme-border) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-add-custom:hover {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-soft) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-custom-chip {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-custom-chip:hover {
                border-color: rgba(185,28,28,.35) !important;
                background: #fff1f2 !important;
                color: #991b1b !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-secondary {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-secondary:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-primary {
                border: 1px solid var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
                box-shadow: var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.08)) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-primary:hover {
                background: var(--theme-button-hover-background) !important;
            }

            .character-sheet-theme[data-theme] .character-defenses-error {
                border-color: rgba(185,28,28,.28) !important;
                background: #fff1f2 !important;
                color: #991b1b !important;
            }

            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .character-defenses-custom-chip:hover,
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .character-defenses-error {
                background: rgba(127,29,29,.18) !important;
            }


            /*
            |--------------------------------------------------------------------------
            | V23 — RESUMO DE DEFESAS / LEITURA EM DUAS LINHAS
            |--------------------------------------------------------------------------
            |
            | O resumo não força mais Resistências, Imunidades e
            | Vulnerabilidades em uma única linha. Mantemos a seção compacta,
            | mas cada valor pode ocupar até duas linhas antes do corte.
            |
            */

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-summary {
                padding-top:
                    10px !important;

                padding-bottom:
                    8px !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-header {
                min-height:
                    30px !important;

                padding-bottom:
                    5px !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-summary-grid {
                margin-top:
                    2px !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-summary-label {
                min-height:
                    16px;

                display:
                    flex;

                align-items:
                    center;

                padding-top:
                    4px !important;

                padding-bottom:
                    4px !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-summary-value {
                display:
                    flex;

                min-height:
                    32px;

                align-items:
                    center;

                padding-top:
                    4px !important;

                padding-bottom:
                    4px !important;

                font-size:
                    10px !important;

                line-height:
                    1.22 !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-summary-text {
                display:
                    -webkit-box;

                width:
                    100%;

                min-width:
                    0;

                overflow:
                    hidden;

                -webkit-box-orient:
                    vertical;

                -webkit-line-clamp:
                    2;

                white-space:
                    normal;

                overflow-wrap:
                    anywhere;

                text-overflow:
                    ellipsis;
            }

            @media (max-width: 639px) {
                .character-sheet-theme[data-theme]
                .character-defenses-v22
                .character-defenses-summary-text {
                    -webkit-line-clamp:
                        3;
                }

                .character-sheet-theme[data-theme]
                .character-defenses-v22
                .character-defenses-summary-value {
                    min-height:
                        38px;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | V24 — CABEÇALHOS DO RESUMO MAIS LEGÍVEIS
            |--------------------------------------------------------------------------
            |
            | Resistências / Imunidades / Vulnerabilidades passam a ter a mesma
            | presença visual dos cabeçalhos de Ataques, sem aumentar demais
            | a altura do módulo.
            |
            */

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-summary-label {
                min-height:
                    20px !important;

                padding:
                    5px 10px 4px !important;

                font-size:
                    9px !important;

                font-weight:
                    900 !important;

                line-height:
                    1 !important;

                letter-spacing:
                    .09em !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22
            .character-defenses-summary-value {
                min-height:
                    34px !important;

                padding-inline:
                    10px !important;

                font-size:
                    10.5px !important;

                line-height:
                    1.24 !important;
            }

        </style>
    @endpush
@endonce


<div
    data-defenses-layout="v14-sheet"

    x-data="{
        open: false,
        saving: false,
        error: null,

        activeTab: 'resistances',

        damageTypes:
            @js($damageTypes),

        damageTypeLabels:
            @js($damageTypeLabels),

        resistances:
            @js($damageResistances),

        immunities:
            @js($damageImmunities),

        vulnerabilities:
            @js($damageVulnerabilities),

        savedResistances:
            @js($damageResistances),

        savedImmunities:
            @js($damageImmunities),

        savedVulnerabilities:
            @js($damageVulnerabilities),

        customResistance: '',
        customImmunity: '',
        customVulnerability: '',


        /*
        |--------------------------------------------------------------------------
        | LABELS / APRESENTAÇÃO
        |--------------------------------------------------------------------------
        */

        labelFor(value) {
            return (
                this.damageTypeLabels[value]
                ?? value
            );
        },

        formattedList(list) {
            if (!list.length) {
                return '—';
            }

            return list
                .map(
                    value =>
                        this.labelFor(value)
                )
                .join(' · ');
        },

        customValues(list) {
            return list.filter(
                value =>
                    !Object.prototype.hasOwnProperty.call(
                        this.damageTypeLabels,
                        value
                    )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | EDITOR
        |--------------------------------------------------------------------------
        */

        openEditor() {
            this.resistances =
                [...this.savedResistances];

            this.immunities =
                [...this.savedImmunities];

            this.vulnerabilities =
                [...this.savedVulnerabilities];

            this.customResistance = '';
            this.customImmunity = '';
            this.customVulnerability = '';

            this.error = null;
            this.activeTab = 'resistances';
            this.open = true;
        },

        cancelEditor() {
            if (this.saving) {
                return;
            }

            this.resistances =
                [...this.savedResistances];

            this.immunities =
                [...this.savedImmunities];

            this.vulnerabilities =
                [...this.savedVulnerabilities];

            this.customResistance = '';
            this.customImmunity = '';
            this.customVulnerability = '';

            this.error = null;
            this.activeTab = 'resistances';
            this.open = false;
        },

        toggle(list, value) {
            const index =
                list.indexOf(value);

            if (index === -1) {
                list.push(value);
                return;
            }

            list.splice(
                index,
                1
            );
        },

        addCustom(list, property) {
            const value =
                String(this[property] ?? '')
                    .trim();

            if (!value) {
                return;
            }

            const exists =
                list.some(
                    item =>
                        String(item)
                            .toLocaleLowerCase() ===
                        value.toLocaleLowerCase()
                );

            if (!exists) {
                list.push(value);
            }

            this[property] = '';
        },

        removeValue(list, value) {
            const index =
                list.indexOf(value);

            if (index !== -1) {
                list.splice(
                    index,
                    1
                );
            }
        },


        /*
        |--------------------------------------------------------------------------
        | PERSISTÊNCIA
        |--------------------------------------------------------------------------
        */

        async save() {
            if (this.saving) {
                return;
            }

            this.saving = true;
            this.error = null;

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

            formData.append(
                'damage_resistances',
                JSON.stringify(
                    this.resistances
                )
            );

            formData.append(
                'damage_immunities',
                JSON.stringify(
                    this.immunities
                )
            );

            formData.append(
                'damage_vulnerabilities',
                JSON.stringify(
                    this.vulnerabilities
                )
            );

            try {
                const response =
                    await fetch(
                        '{{ route('characters.combat.update', $character) }}',
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body:
                                formData,
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
                        data?.message
                        ?? 'Não foi possível salvar as defesas.'
                    );
                }

                const combat =
                    data?.combat ?? {};

                this.resistances =
                    Array.isArray(
                        combat.damage_resistances
                    )
                        ? [...combat.damage_resistances]
                        : [...this.resistances];

                this.immunities =
                    Array.isArray(
                        combat.damage_immunities
                    )
                        ? [...combat.damage_immunities]
                        : [...this.immunities];

                this.vulnerabilities =
                    Array.isArray(
                        combat.damage_vulnerabilities
                    )
                        ? [...combat.damage_vulnerabilities]
                        : [...this.vulnerabilities];

                this.savedResistances =
                    [...this.resistances];

                this.savedImmunities =
                    [...this.immunities];

                this.savedVulnerabilities =
                    [...this.vulnerabilities];

                this.open = false;

                window.dispatchEvent(
                    new CustomEvent(
                        'character-defenses-updated',
                        {
                            detail: {
                                combat: combat,
                            }
                        }
                    )
                );

            } catch (error) {
                console.error(
                    'Erro ao salvar defesas:',
                    error
                );

                this.error =
                    error?.message
                    ?? 'Não foi possível salvar as defesas.';

            } finally {
                this.saving = false;
            }
        },
    }"

    class="
                                        character-defenses-add-custom
                                        character-defenses-add-custom
                                        character-defenses-add-custom
        character-defenses-v22
        relative
        min-w-0
        w-full
    "
>

    {{-- ================================================================
         VISUALIZAÇÃO NA FICHA — V21 UNIFICADA

         A geometria agora segue diretamente Ataques/Habilidades:
         - mesmo recuo externo;
         - conteúdo central em 820px;
         - cabeçalho com 40px;
         - título 17px;
         - filete no mesmo eixo;
         - quadro interno com borda, faixa bege e papel claro.
    ================================================================= --}}

    <section
        class="
            character-defenses-summary
            relative
            min-w-0
            overflow-hidden

            md:h-[108px]

            rounded-xl
            border
            border-[#b08c62]/35

            bg-[linear-gradient(180deg,rgba(255,253,248,.965)_0%,rgba(248,242,233,.94)_100%)]

            px-[17px]
            pb-[10px]
            pt-[15px]

            shadow-[inset_0_1px_0_rgba(255,255,255,.78),0_3px_10px_rgba(83,21,15,.040)]
        "
    >
        <div
            class="
                mx-auto
                w-full
                max-w-[820px]
            "
        >
            {{-- ============================================================
                 CABEÇALHO — MESMA GEOMETRIA DE ATAQUES
            ============================================================= --}}

            <div
                class="
                    character-defenses-header
                    flex
                    min-h-[40px]
                    min-w-0
                    items-center

                    border-b
                    border-[#a8845b]/32

                    px-[5px]
                    pb-[8px]
                    pt-[1px]
                "
            >
                <button
                    type="button"

                    @click="openEditor()"

                    class="
                        character-defenses-title
                        inline-flex
                        min-w-0
                        items-center

                        rounded-lg

                        px-[5px]
                        py-1

                        font-serif
                        text-[17px]
                        font-black
                        leading-none

                        text-[#53150f]

                        transition

                        hover:bg-[#fffdf8]/55
                        hover:text-[#6b1d14]

                        focus:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-[#8c6239]/20
                    "

                    title="Editar defesas"
                >
                    Defesas
                </button>
            </div>


            {{-- ============================================================
                 QUADRO DE DEFESAS

                 Em vez de três linhas soltas, o resumo passa a se comportar
                 como a tabela de Ataques / grade de Habilidades.
            ============================================================= --}}

            <div
                class="
                    character-defenses-summary-grid
                    mt-[2px]

                    grid
                    min-w-0

                    grid-cols-1
                    overflow-hidden

                    rounded-lg
                    border
                    border-[#b08c62]/35

                    bg-[#fbf8f1]

                    shadow-[inset_0_1px_0_rgba(255,255,255,.70)]

                    sm:grid-cols-3
                "
            >
                {{-- RESISTÊNCIAS --}}

                <div
                    class="
                        min-w-0

                        border-b
                        border-[#b08c62]/25

                        sm:border-b-0
                        sm:border-r
                    "
                >
                    <div
                        class="
                            character-defenses-summary-label
                            bg-[#eadbc8]

                            px-2.5
                            py-[4px]

                            text-[7px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.12em]

                            text-[#6f472f]
                        "
                    >
                        Resistências
                    </div>

                    <div
                        class="
                            character-defenses-summary-value
                            min-w-0

                            bg-[#fffdf8]/80

                            px-2.5
                            py-[5px]

                            font-serif
                            text-[10.5px]
                            font-bold
                            leading-none

                            text-[#53150f]
                        "
                    >
                        <span
                            class="
                                character-defenses-summary-text
                                block
                                min-w-0
                            "

                            x-text="formattedList(resistances)"
                            :title="formattedList(resistances)"
                        ></span>
                    </div>
                </div>


                {{-- IMUNIDADES --}}

                <div
                    class="
                        min-w-0

                        border-b
                        border-[#b08c62]/25

                        sm:border-b-0
                        sm:border-r
                    "
                >
                    <div
                        class="
                            character-defenses-summary-label
                            bg-[#eadbc8]

                            px-2.5
                            py-[4px]

                            text-[7px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.12em]

                            text-[#6f472f]
                        "
                    >
                        Imunidades
                    </div>

                    <div
                        class="
                            character-defenses-summary-value
                            min-w-0

                            bg-[#fffdf8]/80

                            px-2.5
                            py-[5px]

                            font-serif
                            text-[10.5px]
                            font-bold
                            leading-none

                            text-[#53150f]
                        "
                    >
                        <span
                            class="
                                character-defenses-summary-text
                                block
                                min-w-0
                            "

                            x-text="formattedList(immunities)"
                            :title="formattedList(immunities)"
                        ></span>
                    </div>
                </div>


                {{-- VULNERABILIDADES --}}

                <div class="min-w-0">
                    <div
                        class="
                            character-defenses-summary-label
                            bg-[#eadbc8]

                            px-2.5
                            py-[4px]

                            text-[7px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.12em]

                            text-[#6f472f]
                        "
                    >
                        Vulnerabilidades
                    </div>

                    <div
                        class="
                            character-defenses-summary-value
                            min-w-0

                            bg-[#fffdf8]/80

                            px-2.5
                            py-[5px]

                            font-serif
                            text-[10.5px]
                            font-bold
                            leading-none

                            text-[#6b1d14]
                        "
                    >
                        <span
                            class="
                                character-defenses-summary-text
                                block
                                min-w-0
                            "

                            x-text="formattedList(vulnerabilities)"
                            :title="formattedList(vulnerabilities)"
                        ></span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ================================================================
         EDITOR
    ================================================================= --}}

    <template x-teleport="body">

        <div
            x-show="open"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            @keydown.escape.window="cancelEditor()"

            data-theme="{{ $characterTheme }}"

            class="
                character-sheet-theme
                character-theme-modal-layer
                character-defenses-modal-layer
                fixed
                inset-0
                z-[180]

                flex
                items-center
                justify-center

                p-3
                sm:p-6
            "

            role="dialog"
            aria-modal="true"
            aria-label="Defesas Especiais"
        >

            {{-- BACKDROP --}}

            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="cancelEditor()"
            ></div>


            {{-- PAINEL CENTRAL --}}

            <div
                x-show="open"
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                @click.stop

                class="
                    character-theme-modal
                    character-defenses-modal
                    relative
                    z-10

                    flex
                    max-h-[92dvh]
                    w-full
                    max-w-[820px]
                    flex-col

                    overflow-hidden

                    rounded-2xl
                    border
                    border-[#b98e63]/70

                    bg-[#faf8f2]

                    shadow-[0_26px_80px_rgba(42,23,18,.30)]
                "
            >

                {{-- =====================================================
                     CABEÇALHO
                ====================================================== --}}

                <div
                    class="
                        character-theme-modal-header
                        character-defenses-modal-header
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-4

                        border-b
                        border-[#a0774d]/30

                        bg-[#eadbc8]

                        px-4
                        py-3
                        sm:px-5

                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                    "
                >

                    <div class="flex min-w-0 items-center gap-3">

                        <div class="min-w-0">

                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.16em]
                                    text-[#8c6239]
                                "
                            >
                                Configuração de combate
                            </p>

                            <h3
                                class="
                                    mt-0.5
                                    truncate
                                    font-serif
                                    text-[20px]
                                    font-black
                                    leading-none
                                    text-[#53150f]
                                "
                            >
                                Defesas Especiais
                            </h3>

                            <p
                                class="
                                    mt-1
                                    text-[11px]
                                    leading-snug
                                    text-[#7a583f]
                                "
                            >
                                Resistências, imunidades e vulnerabilidades a dano.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"

                        @click="cancelEditor()"

                        :disabled="saving"

                        class="
                            character-defenses-modal-close
                            flex
                            h-9
                            w-9
                            shrink-0
                            items-center
                            justify-center

                            rounded-lg

                            text-[#8c6239]

                            transition

                            hover:bg-[#fffdf8]/55
                            hover:text-[#53150f]

                            disabled:opacity-50
                        "

                        title="Fechar"
                        aria-label="Fechar editor de defesas"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        >
                            <path d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>

                </div>


                {{-- =====================================================
                     ABAS
                ====================================================== --}}

                <div
                    class="
                        character-defenses-tabs
                        grid
                        shrink-0
                        grid-cols-3

                        border-b
                        border-[#d8c7ab]/60

                        bg-[#f4f1e8]/60

                        px-3
                        sm:px-5
                    "
                >

                    {{-- RESISTÊNCIAS --}}

                    <button
                        type="button"

                        @click="activeTab = 'resistances'"

                        class="
                            character-defenses-tab
                            relative

                            flex
                            min-w-0
                            items-center
                            justify-center
                            gap-2

                            border-b-2
                            px-2
                            py-2.5

                            text-[12px]
                            font-black
                            uppercase
                            tracking-[0.11em]

                            transition
                        "

                        :class="
                            activeTab === 'resistances'
                                ? 'border-[#6b1d14] bg-[#fffdf9]/55 text-[#53150f]'
                                : 'border-transparent text-[#8c6239] hover:bg-[#fffdf8]/50 hover:text-[#6b1d14]'
                        "
                    >
                        <span class="truncate">
                            Resistências
                        </span>

                        <span
                            class="
                                character-defenses-tab-count
                                inline-flex
                                min-w-[20px]
                                items-center
                                justify-center

                                rounded-full

                                bg-[#e7dccb]

                                px-1.5
                                py-0.5

                                font-serif
                                text-[11px]
                                font-black
                                leading-none
                                text-[#6b1d14]
                            "

                            x-text="resistances.length"
                        ></span>
                    </button>


                    {{-- IMUNIDADES --}}

                    <button
                        type="button"

                        @click="activeTab = 'immunities'"

                        class="
                            character-defenses-tab
                            relative

                            flex
                            min-w-0
                            items-center
                            justify-center
                            gap-2

                            border-b-2
                            px-2
                            py-2.5

                            text-[12px]
                            font-black
                            uppercase
                            tracking-[0.11em]

                            transition
                        "

                        :class="
                            activeTab === 'immunities'
                                ? 'border-[#6b1d14] bg-[#fffdf9]/55 text-[#53150f]'
                                : 'border-transparent text-[#8c6239] hover:bg-[#fffdf8]/50 hover:text-[#6b1d14]'
                        "
                    >
                        <span class="truncate">
                            Imunidades
                        </span>

                        <span
                            class="
                                character-defenses-tab-count
                                inline-flex
                                min-w-[20px]
                                items-center
                                justify-center

                                rounded-full

                                bg-[#e7dccb]

                                px-1.5
                                py-0.5

                                font-serif
                                text-[11px]
                                font-black
                                leading-none
                                text-[#6b1d14]
                            "

                            x-text="immunities.length"
                        ></span>
                    </button>


                    {{-- VULNERABILIDADES --}}

                    <button
                        type="button"

                        @click="activeTab = 'vulnerabilities'"

                        class="
                            character-defenses-tab
                            relative

                            flex
                            min-w-0
                            items-center
                            justify-center
                            gap-2

                            border-b-2
                            px-2
                            py-2.5

                            text-[12px]
                            font-black
                            uppercase
                            tracking-[0.11em]

                            transition
                        "

                        :class="
                            activeTab === 'vulnerabilities'
                                ? 'border-[#7b2a21] bg-[#fffdf9]/55 text-[#6b1d14]'
                                : 'border-transparent text-[#8c6239] hover:bg-[#fffdf8]/50 hover:text-[#6b1d14]'
                        "
                    >
                        <span class="truncate">
                            Vulnerabilidades
                        </span>

                        <span
                            class="
                                character-defenses-tab-count
                                inline-flex
                                min-w-[20px]
                                items-center
                                justify-center

                                rounded-full

                                bg-[#ead8d2]

                                px-1.5
                                py-0.5

                                font-serif
                                text-[11px]
                                font-black
                                leading-none
                                text-[#7b2a21]
                            "

                            x-text="vulnerabilities.length"
                        ></span>
                    </button>

                </div>


                {{-- =====================================================
                     CONTEÚDO
                ====================================================== --}}

                <div
                    class="
                        character-defenses-modal-body
                        min-h-0
                        flex-1
                        overflow-y-auto

                        px-4
                        py-4
                        sm:px-5
                    "
                >

                    {{-- =================================================
                         RESISTÊNCIAS
                    ================================================== --}}

                    <section
                        x-show="activeTab === 'resistances'"
                        x-cloak
                    >

                        <div
                            class="
                                mb-3
                                flex
                                items-end
                                justify-between
                                gap-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#8c6239]
                                    "
                                >
                                    Tipos de dano
                                </p>

                                <p
                                    class="
                                        mt-1
                                        max-w-[520px]
                                        text-[12px]
                                        leading-relaxed
                                        text-[#76553f]
                                    "
                                >
                                    Selecione os tipos de dano aos quais o personagem recebe dano reduzido.
                                </p>
                            </div>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-1.5

                                sm:grid-cols-3
                                lg:grid-cols-4
                            "
                        >
                            <template
                                x-for="option in damageTypes"
                                :key="'resistance-' + option.value"
                            >
                                <button
                                    type="button"

                                    @click="toggle(resistances, option.value)"

                                    class="
                                        character-defenses-option
                                        flex
                                        min-h-[36px]
                                        min-w-0
                                        items-center
                                        justify-between
                                        gap-2

                                        rounded-lg
                                        border

                                        px-2.5
                                        py-2

                                        text-left
                                        text-[11px]
                                        font-bold

                                        transition
                                    "

                                    :class="
                                        resistances.includes(option.value)
                                            ? 'border-[#6b1d14] bg-[#7a2418] text-[#fffaf2] shadow-[inset_0_0_0_1px_rgba(255,255,255,.12)]'
                                            : 'border-[#d8c7ab]/80 bg-[#fffdf9] text-[#53150f] hover:border-[#c5ab88] hover:bg-[#f4f1e8]'
                                    "
                                >
                                    <span
                                        class="min-w-0 truncate"
                                        x-text="option.label"
                                    ></span>

                                    <span
                                        class="
                                            flex
                                            h-4
                                            w-4
                                            shrink-0
                                            items-center
                                            justify-center

                                            rounded-full
                                            border
                                        "

                                        :class="
                                            resistances.includes(option.value)
                                                ? 'border-[#fffaf2]/55 bg-[#fffaf2]/10'
                                                : 'border-[#cdbb9f] bg-white'
                                        "
                                        aria-hidden="true"
                                    >
                                        <svg
                                            x-show="resistances.includes(option.value)"
                                            x-cloak
                                            class="h-2.5 w-2.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </span>
                                </button>
                            </template>
                        </div>


                        {{-- PERSONALIZADA --}}

                        <div
                            class="
                                character-defenses-custom-card
                                mt-4

                                rounded-xl
                                border
                                border-[#d8c7ab]/60

                                bg-[#f4f1e8]/40

                                p-3
                            "
                        >
                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#8c6239]
                                "
                            >
                                Resistência personalizada
                            </p>

                            <div
                                class="
                                    mt-2
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <input
                                    type="text"

                                    x-model="customResistance"

                                    @keydown.enter.prevent="
                                        addCustom(
                                            resistances,
                                            'customResistance'
                                        )
                                    "

                                    maxlength="160"

                                    placeholder="Ex.: dano de armas não mágicas"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/85

                                        bg-[#fffdf9]

                                        px-3

                                        text-[12px]
                                        text-[#53150f]

                                        outline-none

                                        placeholder:text-[#a88769]/70

                                        focus:border-[#8c6239]
                                        focus:ring-1
                                        focus:ring-[#8c6239]/15
                                    "
                                >

                                <button
                                    type="button"

                                    @click="
                                        addCustom(
                                            resistances,
                                            'customResistance'
                                        )
                                    "

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#eadbc8]

                                        px-3

                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-wide
                                        text-[#6b1d14]

                                        transition

                                        hover:bg-[#ecdcc8]
                                    "
                                >
                                    Adicionar
                                </button>
                            </div>


                            <div
                                x-show="customValues(resistances).length"
                                x-cloak

                                class="
                                    mt-2.5
                                    flex
                                    flex-wrap
                                    gap-1.5
                                "
                            >
                                <template
                                    x-for="value in customValues(resistances)"
                                    :key="'custom-resistance-' + value"
                                >
                                    <button
                                        type="button"

                                        @click="removeValue(resistances, value)"

                                        class="
                                            character-defenses-custom-chip
                                            inline-flex
                                            max-w-full
                                            items-center
                                            gap-1.5

                                            rounded-full
                                            border
                                            border-[#d8c7ab]/80

                                            bg-[#fffdf9]

                                            px-2.5
                                            py-1

                                            text-[8.5px]
                                            font-bold
                                            text-[#53150f]

                                            transition

                                            hover:border-red-300
                                            hover:bg-red-50
                                            hover:text-red-700
                                        "

                                        title="Remover"
                                    >
                                        <span
                                            class="truncate"
                                            x-text="value"
                                        ></span>

                                        <span
                                            class="
                                                shrink-0
                                                text-[11px]
                                                leading-none
                                            "
                                            aria-hidden="true"
                                        >
                                            ×
                                        </span>
                                    </button>
                                </template>
                            </div>

                        </div>

                    </section>


                    {{-- =================================================
                         IMUNIDADES
                    ================================================== --}}

                    <section
                        x-show="activeTab === 'immunities'"
                        x-cloak
                    >

                        <div
                            class="
                                mb-3
                                flex
                                items-end
                                justify-between
                                gap-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#8c6239]
                                    "
                                >
                                    Tipos de dano
                                </p>

                                <p
                                    class="
                                        mt-1
                                        max-w-[520px]
                                        text-[12px]
                                        leading-relaxed
                                        text-[#76553f]
                                    "
                                >
                                    Selecione os tipos de dano que não causam dano ao personagem.
                                </p>
                            </div>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-1.5

                                sm:grid-cols-3
                                lg:grid-cols-4
                            "
                        >
                            <template
                                x-for="option in damageTypes"
                                :key="'immunity-' + option.value"
                            >
                                <button
                                    type="button"

                                    @click="toggle(immunities, option.value)"

                                    class="
                                        character-defenses-option
                                        flex
                                        min-h-[36px]
                                        min-w-0
                                        items-center
                                        justify-between
                                        gap-2

                                        rounded-lg
                                        border

                                        px-2.5
                                        py-2

                                        text-left
                                        text-[11px]
                                        font-bold

                                        transition
                                    "

                                    :class="
                                        immunities.includes(option.value)
                                            ? 'border-[#6b1d14] bg-[#6b1d14] text-[#fffaf2] shadow-[inset_0_0_0_1px_rgba(255,255,255,.12)]'
                                            : 'border-[#d8c7ab]/80 bg-[#fffdf9] text-[#53150f] hover:border-[#c5ab88] hover:bg-[#f4f1e8]'
                                    "
                                >
                                    <span
                                        class="min-w-0 truncate"
                                        x-text="option.label"
                                    ></span>

                                    <span
                                        class="
                                            flex
                                            h-4
                                            w-4
                                            shrink-0
                                            items-center
                                            justify-center

                                            rounded-full
                                            border
                                        "

                                        :class="
                                            immunities.includes(option.value)
                                                ? 'border-[#fffaf2]/55 bg-[#fffaf2]/10'
                                                : 'border-[#cdbb9f] bg-white'
                                        "
                                        aria-hidden="true"
                                    >
                                        <svg
                                            x-show="immunities.includes(option.value)"
                                            x-cloak
                                            class="h-2.5 w-2.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </span>
                                </button>
                            </template>
                        </div>


                        {{-- PERSONALIZADA --}}

                        <div
                            class="
                                character-defenses-custom-card
                                mt-4

                                rounded-xl
                                border
                                border-[#d8c7ab]/60

                                bg-[#f4f1e8]/40

                                p-3
                            "
                        >
                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#8c6239]
                                "
                            >
                                Imunidade personalizada
                            </p>

                            <div
                                class="
                                    mt-2
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <input
                                    type="text"

                                    x-model="customImmunity"

                                    @keydown.enter.prevent="
                                        addCustom(
                                            immunities,
                                            'customImmunity'
                                        )
                                    "

                                    maxlength="160"

                                    placeholder="Adicionar imunidade personalizada"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/85

                                        bg-[#fffdf9]

                                        px-3

                                        text-[12px]
                                        text-[#53150f]

                                        outline-none

                                        placeholder:text-[#a88769]/70

                                        focus:border-[#8c6239]
                                        focus:ring-1
                                        focus:ring-[#8c6239]/15
                                    "
                                >

                                <button
                                    type="button"

                                    @click="
                                        addCustom(
                                            immunities,
                                            'customImmunity'
                                        )
                                    "

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#eadbc8]

                                        px-3

                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-wide
                                        text-[#6b1d14]

                                        transition

                                        hover:bg-[#ecdcc8]
                                    "
                                >
                                    Adicionar
                                </button>
                            </div>


                            <div
                                x-show="customValues(immunities).length"
                                x-cloak

                                class="
                                    mt-2.5
                                    flex
                                    flex-wrap
                                    gap-1.5
                                "
                            >
                                <template
                                    x-for="value in customValues(immunities)"
                                    :key="'custom-immunity-' + value"
                                >
                                    <button
                                        type="button"

                                        @click="removeValue(immunities, value)"

                                        class="
                                            character-defenses-custom-chip
                                            inline-flex
                                            max-w-full
                                            items-center
                                            gap-1.5

                                            rounded-full
                                            border
                                            border-[#d8c7ab]/80

                                            bg-[#fffdf9]

                                            px-2.5
                                            py-1

                                            text-[8.5px]
                                            font-bold
                                            text-[#53150f]

                                            transition

                                            hover:border-red-300
                                            hover:bg-red-50
                                            hover:text-red-700
                                        "

                                        title="Remover"
                                    >
                                        <span
                                            class="truncate"
                                            x-text="value"
                                        ></span>

                                        <span
                                            class="
                                                shrink-0
                                                text-[11px]
                                                leading-none
                                            "
                                            aria-hidden="true"
                                        >
                                            ×
                                        </span>
                                    </button>
                                </template>
                            </div>

                        </div>

                    </section>


                    {{-- =================================================
                         VULNERABILIDADES
                    ================================================== --}}

                    <section
                        x-show="activeTab === 'vulnerabilities'"
                        x-cloak
                    >

                        <div
                            class="
                                mb-3
                                flex
                                items-end
                                justify-between
                                gap-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#7b2a21]
                                    "
                                >
                                    Tipos de dano
                                </p>

                                <p
                                    class="
                                        mt-1
                                        max-w-[520px]
                                        text-[12px]
                                        leading-relaxed
                                        text-[#76553f]
                                    "
                                >
                                    Selecione os tipos de dano que causam dano aumentado ao personagem.
                                </p>
                            </div>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-1.5

                                sm:grid-cols-3
                                lg:grid-cols-4
                            "
                        >
                            <template
                                x-for="option in damageTypes"
                                :key="'vulnerability-' + option.value"
                            >
                                <button
                                    type="button"

                                    @click="toggle(vulnerabilities, option.value)"

                                    class="
                                        character-defenses-option
                                        flex
                                        min-h-[36px]
                                        min-w-0
                                        items-center
                                        justify-between
                                        gap-2

                                        rounded-lg
                                        border

                                        px-2.5
                                        py-2

                                        text-left
                                        text-[11px]
                                        font-bold

                                        transition
                                    "

                                    :class="
                                        vulnerabilities.includes(option.value)
                                            ? 'border-[#7b2a21] bg-[#7b2a21] text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,.10)]'
                                            : 'border-[#d8c7ab]/80 bg-[#fffdf9] text-[#53150f] hover:border-[#c5ab88] hover:bg-[#f4f1e8]'
                                    "
                                >
                                    <span
                                        class="min-w-0 truncate"
                                        x-text="option.label"
                                    ></span>

                                    <span
                                        class="
                                            flex
                                            h-4
                                            w-4
                                            shrink-0
                                            items-center
                                            justify-center

                                            rounded-full
                                            border
                                        "

                                        :class="
                                            vulnerabilities.includes(option.value)
                                                ? 'border-white/55 bg-white/10'
                                                : 'border-[#cdbb9f] bg-white'
                                        "
                                        aria-hidden="true"
                                    >
                                        <svg
                                            x-show="vulnerabilities.includes(option.value)"
                                            x-cloak
                                            class="h-2.5 w-2.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </span>
                                </button>
                            </template>
                        </div>


                        {{-- PERSONALIZADA --}}

                        <div
                            class="
                                character-defenses-custom-card
                                mt-4

                                rounded-xl
                                border
                                border-[#d8c7ab]/60

                                bg-[#f4f1e8]/40

                                p-3
                            "
                        >
                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#7b2a21]
                                "
                            >
                                Vulnerabilidade personalizada
                            </p>

                            <div
                                class="
                                    mt-2
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <input
                                    type="text"

                                    x-model="customVulnerability"

                                    @keydown.enter.prevent="
                                        addCustom(
                                            vulnerabilities,
                                            'customVulnerability'
                                        )
                                    "

                                    maxlength="160"

                                    placeholder="Ex.: Corte, impacto e perfurante de ataques não mágicos"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/85

                                        bg-[#fffdf9]

                                        px-3

                                        text-[12px]
                                        text-[#53150f]

                                        outline-none

                                        placeholder:text-[#a88769]/70

                                        focus:border-[#8c6239]
                                        focus:ring-1
                                        focus:ring-[#8c6239]/15
                                    "
                                >

                                <button
                                    type="button"

                                    @click="
                                        addCustom(
                                            vulnerabilities,
                                            'customVulnerability'
                                        )
                                    "

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#eadbc8]

                                        px-3

                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-wide
                                        text-[#6b1d14]

                                        transition

                                        hover:bg-[#ecdcc8]
                                    "
                                >
                                    Adicionar
                                </button>
                            </div>


                            <div
                                x-show="customValues(vulnerabilities).length"
                                x-cloak

                                class="
                                    mt-2.5
                                    flex
                                    flex-wrap
                                    gap-1.5
                                "
                            >
                                <template
                                    x-for="value in customValues(vulnerabilities)"
                                    :key="'custom-vulnerability-' + value"
                                >
                                    <button
                                        type="button"

                                        @click="removeValue(vulnerabilities, value)"

                                        class="
                                            character-defenses-custom-chip
                                            inline-flex
                                            max-w-full
                                            items-center
                                            gap-1.5

                                            rounded-full
                                            border
                                            border-[#d8c7ab]/80

                                            bg-[#fffdf9]

                                            px-2.5
                                            py-1

                                            text-[8.5px]
                                            font-bold
                                            text-[#6b1d14]

                                            transition

                                            hover:border-red-300
                                            hover:bg-red-50
                                            hover:text-red-700
                                        "

                                        title="Remover"
                                    >
                                        <span
                                            class="truncate"
                                            x-text="value"
                                        ></span>

                                        <span
                                            class="
                                                shrink-0
                                                text-[11px]
                                                leading-none
                                            "
                                            aria-hidden="true"
                                        >
                                            ×
                                        </span>
                                    </button>
                                </template>
                            </div>

                        </div>

                    </section>


                    {{-- ERRO --}}

                    <div
                        x-show="error"
                        x-cloak

                        class="
                            character-defenses-error
                            mt-4

                            rounded-lg
                            border
                            border-red-200

                            bg-red-50

                            px-3
                            py-2.5

                            text-[11px]
                            font-bold
                            text-red-700
                        "

                        x-text="error"
                    ></div>

                </div>


                {{-- =====================================================
                     AÇÕES
                ====================================================== --}}

                <div
                    class="
                        character-theme-modal-footer
                        character-defenses-modal-footer
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3

                        border-t
                        border-[#d8c7ab]/60

                        bg-[#f4f1e8]/70

                        px-4
                        py-3
                        sm:px-5
                    "
                >

 


                    {{-- BOTÕES --}}

                    <div
                        class="
                            ml-auto
                            flex
                            items-center
                            justify-end
                            gap-2
                        "
                    >




                        <button
                            type="button"
                            @click="cancelEditor()"
                            :disabled="saving"
                            class="
                                character-defenses-secondary
                                rounded-lg
                                px-4
                                py-2.5
                                text-[11px]
                                font-black
                                uppercase
                                tracking-wider
                                transition
                                disabled:opacity-50
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"

                            @click="save()"

                            :disabled="saving"

                            class="
                                character-defenses-primary
                                min-w-[124px]

                                rounded-lg

                                bg-[#6b1d14]

                                px-5
                                py-2.5

                                text-[12px]
                                font-black
                                uppercase
                                tracking-wider
                                text-[#fffaf2]

                                shadow-[0_1px_2px_rgba(83,21,15,.14)]

                                transition

                                hover:bg-[#53150f]

                                disabled:cursor-wait
                                disabled:opacity-60
                            "
                        >
                            <span x-show="!saving">
                                Salvar alterações
                            </span>

                            <span
                                x-show="saving"
                                x-cloak
                            >
                                Salvando...
                            </span>
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </template>

</div>