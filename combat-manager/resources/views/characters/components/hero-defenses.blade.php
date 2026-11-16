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



            /* ==============================================================
               V25 — DEFESAS RESPONSIVAS CONFORME A LARGURA DO CARD

               Removemos a largura interna fixa, a altura rígida e o clamp.
               O usuário vê a descrição completa; o card cresce se preciso.
               O container query considera a largura real da coluna direita,
               e não apenas o tamanho da janela do navegador.
            ============================================================== */

            .character-sheet-theme[data-theme] .character-defenses-v22 {
                container-type: inline-size;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22 .character-defenses-summary {
                height: auto !important;
                min-height: 108px !important;
                overflow: visible !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22 .character-defenses-summary > div {
                width: 100% !important;
                max-width: none !important;
                min-width: 0;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22 .character-defenses-summary-text {
                display: block !important;
                width: 100%;
                min-width: 0;
                overflow: visible !important;
                text-overflow: clip !important;
                white-space: normal !important;
                overflow-wrap: anywhere;
                word-break: normal;
                -webkit-line-clamp: unset !important;
                -webkit-box-orient: unset !important;
            }

            .character-sheet-theme[data-theme]
            .character-defenses-v22 .character-defenses-summary-value {
                height: auto !important;
                align-items: flex-start !important;
                line-height: 1.35 !important;
            }

            /* Largura ampla: proporções definidas pela quantidade de texto. */
            @@container (min-width: 700px) {
                .character-defenses-summary-grid {
                    grid-template-columns: var(--defenses-grid-tracks, repeat(3,minmax(0,1fr))) !important;
                }
            }

            /* Largura intermediária: vulnerabilidades ocupa linha inteira. */
            @@container (min-width: 440px) and (max-width: 699px) {
                .character-defenses-summary-grid {
                    grid-template-columns: repeat(2,minmax(0,1fr)) !important;
                }

                .character-defenses-summary-grid > :last-child {
                    grid-column: 1 / -1;
                }

                .character-defenses-summary-grid > :nth-child(2) {
                    border-right: 0 !important;
                }

                .character-defenses-summary-grid > :first-child,
                .character-defenses-summary-grid > :nth-child(2) {
                    border-bottom: 1px solid var(--theme-border-soft) !important;
                }
            }

            /* Espaço estreito: leitura vertical sem conteúdo espremido. */
            @@container (max-width: 439px) {
                .character-defenses-summary-grid {
                    grid-template-columns: minmax(0,1fr) !important;
                }

                .character-defenses-summary-grid > div {
                    grid-column: 1 / -1;
                    border-right: 0 !important;
                }

                .character-defenses-summary-grid > :not(:last-child) {
                    border-bottom: 1px solid var(--theme-border-soft) !important;
                }
            }


            /* V35 — editor de Defesas, visual compacto e temático. */
            .character-sheet-theme[data-theme] .character-defenses-modal {
                width: min(750px, calc(100vw - 24px)) !important;
                max-height: min(88dvh, 770px) !important;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                border-radius: 16px !important;
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                background-image: none !important;
                box-shadow: 0 24px 70px rgba(0,0,0,.30) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-modal::before,
            .character-sheet-theme[data-theme] .character-defenses-modal::after {
                content: none !important;
                display: none !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-modal-header {
                padding: 16px 20px 14px !important;
                border-bottom: 1px solid var(--theme-border) !important;
                border-top: 0 !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
                background-image: none !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-modal-header::before,
            .character-sheet-theme[data-theme] .character-defenses-modal-header::after {
                display: none !important;
                box-shadow: none !important;
                background: none !important;
            }
            .character-sheet-theme[data-theme] .defenses-v35-eyebrow {
                color: var(--theme-label) !important;
                font-size: 9px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: .12em;
            }
            .character-sheet-theme[data-theme] .defenses-v35-title {
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .defenses-v35-subtitle {
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-tabs {
                padding: 5px 14px 0 !important;
                border-bottom: 1px solid var(--theme-border) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-tab {
                min-height: 37px;
                padding: 8px 4px !important;
                font-size: 10px !important;
                letter-spacing: .045em !important;
                font-weight: 800 !important;
                border-bottom: 2px solid transparent;
                color: var(--theme-text-secondary) !important;
                background: transparent !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-tab.is-active {
                border-bottom-color: var(--theme-accent) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-tab:hover:not(.is-active) {
                color: var(--theme-text-strong) !important;
                background: var(--theme-card-hover) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-tab-count {
                min-width: 17px;
                padding: 2px 5px;
                border: 1px solid var(--theme-border-soft) !important;
                border-radius: 999px;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
                font-size: 9px;
                line-height: 1;
            }
            .character-sheet-theme[data-theme] .character-defenses-tab.is-active .character-defenses-tab-count {
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-modal-body {
                min-height: 0;
                overflow-y: auto;
                padding: 17px 20px 20px !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            }
            .character-sheet-theme[data-theme] .defenses-v35-section-label {
                color: var(--theme-text-strong) !important;
                font-size: 11px;
                font-weight: 800;
            }
            .character-sheet-theme[data-theme] .defenses-v35-section-help {
                color: var(--theme-text-secondary) !important;
                font-size: 10px;
            }
            .character-sheet-theme[data-theme] .character-defenses-option {
                min-height: 36px;
                border: 1px solid var(--theme-border-soft) !important;
                border-radius: 8px;
                padding: 8px 10px;
                background: var(--theme-surface-card) !important;
                color: var(--theme-text-primary) !important;
                font-size: 11px;
                font-weight: 650;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-option:hover:not(.is-selected) {
                background: var(--theme-card-hover) !important;
                border-color: var(--theme-border-strong) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-option.is-selected {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .defenses-v35-check {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 15px;
                height: 15px;
                border: 1px solid var(--theme-border-strong);
                border-radius: 4px;
                color: transparent;
                flex: 0 0 auto;
            }
            .character-sheet-theme[data-theme] .is-selected .defenses-v35-check {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent) !important;
                color: var(--theme-button-text, #fff) !important;
            }
            .character-sheet-theme[data-theme] .defenses-v35-custom {
                margin-top: 18px;
                border-top: 1px solid var(--theme-border-soft);
                padding-top: 16px;
            }
            .character-sheet-theme[data-theme] .defenses-v35-input {
                min-width: 0;
                height: 36px;
                border: 1px solid var(--theme-input-border) !important;
                border-radius: 8px;
                padding: 0 12px;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
                outline: none;
            }
            .character-sheet-theme[data-theme] .defenses-v35-input:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-input-focus) 12%, transparent) !important;
            }
            .character-sheet-theme[data-theme] .defenses-v35-input::placeholder {
                color: var(--theme-input-placeholder) !important;
            }
            .character-sheet-theme[data-theme] .defenses-v35-add {
                height: 36px;
                padding-inline: 15px;
                border: 1px solid var(--theme-border) !important;
                border-radius: 8px;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
                font-size: 10px;
                font-weight: 800;
            }
            .character-sheet-theme[data-theme] .defenses-v35-add:hover {
                background: var(--theme-card-hover) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-custom-chip {
                background: var(--theme-surface-card) !important;
                color: var(--theme-text-primary) !important;
                border-color: var(--theme-border-soft) !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-modal-footer {
                position: relative !important;
                margin: 0 !important;
                min-height: 62px;
                padding: 11px 20px !important;
                border-top: 1px solid var(--theme-border) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .character-defenses-modal-footer > * {
                flex-shrink: 0;
            }
            .character-sheet-theme[data-theme] .character-defenses-modal :is(button,input):focus-visible {
                outline: 2px solid var(--theme-accent);
                outline-offset: 2px;
            }
            @media (max-width: 550px) {
                .character-sheet-theme[data-theme] .character-defenses-modal-header,
                .character-sheet-theme[data-theme] .character-defenses-modal-body,
                .character-sheet-theme[data-theme] .character-defenses-modal-footer {
                    padding-left: 14px !important;
                    padding-right: 14px !important;
                }
                .character-sheet-theme[data-theme] .character-defenses-tab { font-size: 9px !important; }
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

        defensesSizeObserver: null,

        // Mantém Atributos alinhado ao topo de Defesas mesmo quando a
        // descrição faz o card crescer além da altura original de 108px.
        init() {
            this.$nextTick(() => {
                const page = this.$root.closest('.character-sheet-page');
                const card = this.$root.querySelector('.character-defenses-summary');

                if (!page || !card || typeof ResizeObserver === 'undefined') {
                    return;
                }

                const syncHeight = () => {
                    const height = Math.ceil(card.getBoundingClientRect().height);
                    if (height > 0) {
                        page.style.setProperty('--sheet-defenses-height', `${height}px`);
                    }
                };

                this.defensesSizeObserver = new ResizeObserver(syncHeight);
                this.defensesSizeObserver.observe(card);
                syncHeight();
            });
        },

        destroy() {
            this.defensesSizeObserver?.disconnect();
        },


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

        /**
         * Ajusta a largura das colunas ao volume de texto já salvo.
         * Categorias vazias ficam compactas; descrições longas
         * aproveitam a largura livre. Não modifica os dados.
         */
        defenseSummaryTracks() {
            const lists = [
                this.savedResistances,
                this.savedImmunities,
                this.savedVulnerabilities,
            ];

            const weights = lists.map((list) => {
                if (!Array.isArray(list) || list.length === 0) {
                    return 1;
                }

                const length = this.formattedList(list).length;
                return Math.max(1.25, Math.min(4, length / 20));
            });

            // Cabeçalhos permanecem legíveis mesmo com coluna vazia.
            const minima = [125, 125, 165];

            return weights.map((weight, index) =>
                `minmax(${minima[index]}px, ${weight.toFixed(2)}fr)`
            ).join(' ');
        },

        activeValues() {
            return this.activeTab === 'immunities'
                ? this.immunities
                : (this.activeTab === 'vulnerabilities'
                    ? this.vulnerabilities
                    : this.resistances);
        },

        activeCustomProperty() {
            return this.activeTab === 'immunities'
                ? 'customImmunity'
                : (this.activeTab === 'vulnerabilities'
                    ? 'customVulnerability'
                    : 'customResistance');
        },

        activeCustomValue() {
            return this[this.activeCustomProperty()] ?? '';
        },

        setActiveCustomValue(value) {
            this[this.activeCustomProperty()] = value;
        },

        activeDescription() {
            return this.activeTab === 'immunities'
                ? 'O personagem não sofre dano destes tipos.'
                : (this.activeTab === 'vulnerabilities'
                    ? 'O personagem sofre dano dobrado destes tipos.'
                    : 'O personagem recebe dano reduzido destes tipos.');
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

            min-h-[108px] h-auto

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
                max-w-none
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
                :style="{ '--defenses-grid-tracks': defenseSummaryTracks() }"
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


    {{-- Editor: mesma fonte de dados e mesmo endpoint de salvamento. --}}
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
            x-on:keydown.escape.window="cancelEditor()"
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer character-defenses-modal-layer fixed inset-0 z-[180] flex items-center justify-center p-3 sm:p-6"
            role="dialog"
            aria-modal="true"
            aria-labelledby="defenses-modal-title-{{ $character->id }}"
        >
            <div class="character-theme-modal-backdrop absolute inset-0" x-on:click="cancelEditor()"></div>

            <div
                x-show="open"
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                x-on:click.stop
                class="character-theme-modal character-defenses-modal relative z-10 w-full"
            >
                {{-- Cabeçalho sem reflexo branco --}}
                <div class="character-theme-modal-header character-defenses-modal-header flex shrink-0 items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="defenses-v35-eyebrow">Configuração de combate</p>
                        <h3
                            id="defenses-modal-title-{{ $character->id }}"
                            class="defenses-v35-title mt-1 font-serif text-[21px] font-black leading-none"
                        >Defesas Especiais</h3>
                        <p class="defenses-v35-subtitle mt-1.5 text-[10px]">Resistências, imunidades e vulnerabilidades.</p>
                    </div>
                    <button
                        type="button"
                        x-on:click="cancelEditor()"
                        :disabled="saving"
                        class="character-defenses-modal-close flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition disabled:opacity-50"
                        aria-label="Fechar editor de defesas"
                        title="Fechar"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </div>

                {{-- Navegação compacta --}}
                <div class="character-defenses-tabs grid shrink-0 grid-cols-3 gap-1" role="tablist" aria-label="Categoria de defesa">
                    <button type="button" x-on:click="activeTab = 'resistances'"
                        :class="activeTab === 'resistances' ? 'is-active' : ''"
                        :aria-selected="activeTab === 'resistances' ? 'true' : 'false'"
                        class="character-defenses-tab flex min-w-0 items-center justify-center gap-1.5 rounded-t-md transition" role="tab">
                        <span class="truncate">Resistências</span>
                        <span class="character-defenses-tab-count" x-text="resistances.length"></span>
                    </button>
                    <button type="button" x-on:click="activeTab = 'immunities'"
                        :class="activeTab === 'immunities' ? 'is-active' : ''"
                        :aria-selected="activeTab === 'immunities' ? 'true' : 'false'"
                        class="character-defenses-tab flex min-w-0 items-center justify-center gap-1.5 rounded-t-md transition" role="tab">
                        <span class="truncate">Imunidades</span>
                        <span class="character-defenses-tab-count" x-text="immunities.length"></span>
                    </button>
                    <button type="button" x-on:click="activeTab = 'vulnerabilities'"
                        :class="activeTab === 'vulnerabilities' ? 'is-active' : ''"
                        :aria-selected="activeTab === 'vulnerabilities' ? 'true' : 'false'"
                        class="character-defenses-tab flex min-w-0 items-center justify-center gap-1.5 rounded-t-md transition" role="tab">
                        <span class="truncate">Vulnerabilidades</span>
                        <span class="character-defenses-tab-count" x-text="vulnerabilities.length"></span>
                    </button>
                </div>

                <div class="character-defenses-modal-body flex-1">
                    <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <p class="defenses-v35-section-label">Tipos de dano</p>
                            <p class="defenses-v35-section-help mt-0.5" x-text="activeDescription()"></p>
                        </div>
                        <span class="defenses-v35-section-help pt-0.5" x-text="`${activeValues().length} selecionado${activeValues().length === 1 ? '' : 's'}`"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3 lg:grid-cols-4">
                        <template x-for="option in damageTypes" :key="activeTab + '-' + option.value">
                            <button
                                type="button"
                                x-on:click="toggle(activeValues(), option.value)"
                                :class="activeValues().includes(option.value) ? 'is-selected' : ''"
                                :aria-pressed="activeValues().includes(option.value) ? 'true' : 'false'"
                                class="character-defenses-option flex min-w-0 items-center justify-between gap-2 text-left transition"
                            >
                                <span class="min-w-0 truncate" x-text="option.label"></span>
                                <span class="defenses-v35-check">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>
                                </span>
                            </button>
                        </template>
                    </div>

                    <div class="defenses-v35-custom">
                        <label class="defenses-v35-section-label block" for="defenses-custom-{{ $character->id }}">Personalizar</label>
                        <p class="defenses-v35-section-help mb-2 mt-0.5">Adicione uma condição específica que não esteja na lista.</p>
                        <div class="flex min-w-0 gap-2">
                            <input
                                id="defenses-custom-{{ $character->id }}"
                                type="text"
                                maxlength="160"
                                :value="activeCustomValue()"
                                x-on:input="setActiveCustomValue($event.target.value)"
                                x-on:keydown.enter.prevent="addCustom(activeValues(), activeCustomProperty())"
                                placeholder="Ex.: dano de armas não mágicas"
                                class="defenses-v35-input min-w-0 flex-1 text-[11px]"
                            >
                            <button
                                type="button"
                                x-on:click="addCustom(activeValues(), activeCustomProperty())"
                                class="defenses-v35-add shrink-0 transition"
                            >Adicionar</button>
                        </div>
                        <div x-show="customValues(activeValues()).length > 0" x-cloak class="mt-2.5 flex flex-wrap gap-1.5">
                            <template x-for="value in customValues(activeValues())" :key="activeTab + '-custom-' + value">
                                <button
                                    type="button"
                                    x-on:click="removeValue(activeValues(), value)"
                                    class="character-defenses-custom-chip inline-flex max-w-full items-center gap-2 rounded-full border px-2.5 py-1.5 text-[10px] transition"
                                    :title="'Remover ' + value"
                                >
                                    <span class="min-w-0 break-words text-left" x-text="value"></span>
                                    <span aria-hidden="true" class="shrink-0 text-sm leading-none">×</span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div x-show="error" x-cloak class="character-defenses-error mt-3 rounded-lg border px-3 py-2 text-[11px] font-bold" x-text="error"></div>
                </div>

                <div class="character-theme-modal-footer character-defenses-modal-footer flex shrink-0 items-center justify-end gap-2">
                    <button type="button" x-on:click="cancelEditor()" :disabled="saving"
                        class="character-defenses-secondary rounded-lg px-4 py-2.5 text-[10px] font-black uppercase transition disabled:opacity-50">Cancelar</button>
                    <button type="button" x-on:click="save()" :disabled="saving"
                        class="character-defenses-primary min-w-[140px] rounded-lg px-5 py-2.5 text-[10px] font-black uppercase transition disabled:opacity-60">
                        <span x-show="!saving">Salvar alterações</span>
                        <span x-show="saving" x-cloak>Salvando...</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>