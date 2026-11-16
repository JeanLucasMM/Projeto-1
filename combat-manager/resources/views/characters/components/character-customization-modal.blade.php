@props([
    'character',
    'classes' => collect(),
])

@php
    /*
    |--------------------------------------------------------------------------
    | DADOS INICIAIS
    |--------------------------------------------------------------------------
    */

    $sortedClasses =
        collect($classes)
            ->sortByDesc(function ($class) {
                return [
                    (int) ($class->is_primary ?? false),
                    (int) ($class->level ?? 0),
                    -((int) ($class->sort_order ?? 0)),
                ];
            })
            ->values();

    $sheetSettings =
        is_array(
            $character->sheet_settings
            ?? null
        )
            ? $character->sheet_settings
            : [];

    $displaySettings =
        is_array(
            $sheetSettings['display']
            ?? null
        )
            ? $sheetSettings['display']
            : [];

    /*
     * Compatibilidade:
     * - show_defenses é a chave nova;
     * - show_empty_defenses é aceita somente para registros já salvos
     *   durante a primeira implementação.
     */
    $initialShowDefenses =
        array_key_exists(
            'show_defenses',
            $displaySettings
        )
            ? (bool) $displaySettings['show_defenses']
            : (
                array_key_exists(
                    'show_empty_defenses',
                    $displaySettings
                )
                    ? (bool) $displaySettings['show_empty_defenses']
                    : true
            );

    $progressionSettings =
        is_array(
            $sheetSettings['progression']
            ?? null
        )
            ? $sheetSettings['progression']
            : [];

    $proficiencyCustomEnabled =
        (bool) (
            $progressionSettings[
                'proficiency_custom_enabled'
            ]
            ?? false
        );

    $characterLevel =
        max(
            1,
            (int) (
                $character->level
                ?? 1
            )
        );

    $calculatedProficiency =
        match (true) {
            $characterLevel >= 17 => 6,
            $characterLevel >= 13 => 5,
            $characterLevel >= 9 => 4,
            $characterLevel >= 5 => 3,
            default => 2,
        };

    $currentProficiency =
        (int) (
            $character->proficiency_bonus
            ?? $calculatedProficiency
        );

    $initialSettings =
        array_replace_recursive(
            [
                'display' => [
                    'show_defenses' =>
                        $initialShowDefenses,
                ],

                'progression' => [
                    'proficiency_custom_enabled' =>
                        $proficiencyCustomEnabled,
                ],

                'appearance' => [
                    'theme' =>
                        'spellbound',
                ],

                'optional_rules' => [
                    'morquen' => false,
                    'exhaustion' => false,
                    'transformations' => false,
                ],
            ],
            $sheetSettings
        );

    unset(
        $initialSettings['display']['show_empty_defenses'],
        $initialSettings['display']['show_experience']
    );

    // Imagem do disco public na origem atual, sem depender de APP_URL antigo.
    // O arquivo físico continua em storage/app/public/characters/...
    $initialImageUrl = $character->image_path
        ? '/storage/' . ltrim((string) $character->image_path, '/')
        : null;

    /*
    |--------------------------------------------------------------------------
    | TEMAS DA FICHA
    |--------------------------------------------------------------------------
    */

    $themeOptions = [
        'spellbound' => [
            'name' => 'Spellbound',
            'subtitle' => 'Vinho & Pergaminho',
            'description' =>
                'A identidade clássica da ficha: vinho, terracota, madeira e papel quente.',
        ],

        'emerald' => [
            'name' => 'Esmeralda',
            'subtitle' => 'Floresta & Jade',
            'description' =>
                'Verdes naturais, musgo e marfim para uma ficha orgânica e feérica.',
        ],

        'rose' => [
            'name' => 'Rosa Antigo',
            'subtitle' => 'Rosé & Creme',
            'description' =>
                'Rosa envelhecido, vinho rosado e creme com acabamento delicado.',
        ],

        'sapphire' => [
            'name' => 'Safira',
            'subtitle' => 'Azul & Ardósia',
            'description' =>
                'Azul profundo, tons frios e papel claro para uma presença nobre.',
        ],

        'arcane' => [
            'name' => 'Arcano',
            'subtitle' => 'Violeta & Ameixa',
            'description' =>
                'Violetas, lavanda envelhecida e ameixa com atmosfera de magia antiga.',
        ],

        'midnight' => [
            'name' => 'Meia-Noite',
            'subtitle' => 'Marinho & Névoa',
            'description' =>
                'Tema escuro azul-marinho com contraste frio e leitura confortável.',
        ],

        'obsidian' => [
            'name' => 'Obsidiana',
            'subtitle' => 'Carvão & Cobre',
            'description' =>
                'Carvão, vinho profundo e cobre para uma ficha escura e dramática.',
        ],

        'aurora' => [
            'name' => 'Aurora',
            'subtitle' => 'Petróleo & Violeta',
            'description' =>
                'Azul petróleo, verde-água e violeta em gradientes mágicos.',
        ],
    ];


    $initialPayload = [
        'name' =>
            $character->name
            ?? '',

        'image_url' =>
            $initialImageUrl,

        'background' =>
            $character->background
            ?? '',

        'species' =>
            $character->species
            ?? '',

        'level' =>
            $characterLevel,

        'experience' => [
            'enabled' => (bool) (
                $character->campaign_experience_enabled ?? false
            ),
            'points' => (int) (
                $character->campaign_experience_points ?? 0
            ),
            'campaign_id' => $character->campaign_experience_id ?? null,
        ],

        'proficiency' => [
            'current' =>
                $currentProficiency,

            'calculated' =>
                $calculatedProficiency,

            'custom_enabled' =>
                $proficiencyCustomEnabled,
        ],

        'settings' =>
            $initialSettings,

        'classes' =>
            $sortedClasses
                ->map(function ($class) {
                    return [
                        'id' =>
                            $class->id
                            ?? null,

                        'name' =>
                            $class->class
                            ?? 'Classe',

                        'subclass' =>
                            $class->subclass
                            ?? null,

                        'level' =>
                            (int) (
                                $class->level
                                ?? 0
                            ),

                        'is_primary' =>
                            (bool) (
                                $class->is_primary
                                ?? false
                            ),
                    ];
                })
                ->all(),
    ];

    /*
     * O JSON fica em data-* e não dentro de x-data.
     *
     * Isso evita misturar um objeto JavaScript enorme com o parser
     * de atributos HTML/Blade.
     */
    $encodedInitialPayload =
        e(
            json_encode(
                $initialPayload,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
        );
@endphp



@once
    @push('styles')
        <style>
            .character-sheet-theme[data-theme] .character-customization-v18-modal {
                border-color: var(--theme-modal-border) !important;
                background: var(--theme-modal-surface) !important;
                color: var(--theme-modal-text) !important;
                box-shadow: var(--theme-modal-shadow) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-header,
            .character-sheet-theme[data-theme] .character-customization-v18-footer {
                border-color: var(--theme-modal-border) !important;
                background: var(--theme-modal-header-background) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-body {
                background: var(--theme-modal-surface) !important;
                color: var(--theme-modal-text) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-title {
                color: var(--theme-modal-title) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-copy {
                color: var(--theme-modal-muted) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-tabs {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-tab {
                border-color: var(--theme-divider) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-tab--inactive {
                background: transparent !important;
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-tab--inactive:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-tab--active {
                background: var(--theme-surface-card) !important;
                color: var(--theme-text-strong) !important;
                box-shadow: inset 0 -2px 0 var(--theme-accent-strong);
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#6b1d14]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#93623d]"] {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#7d604d]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#725644]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#6f4c36]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#6f472f]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="text-[#5f4031]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="border-[#b08c62]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="border-[#a0774d]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="border-[#cdbb9f]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="border-[#d8c7ab]"] {
                border-color: var(--theme-border) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="bg-[#fffdf8]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="bg-[#fbf8f1]"] {
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="bg-[#f4f1e8]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="bg-[#efe9dc]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="bg-[#f7f0e6]"],
            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="bg-[#f7eee2]"] {
                background: var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal [class*="bg-[#eadbc8]"] {
                background: var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
            .character-sheet-theme[data-theme] .character-customization-v18-modal select,
            .character-sheet-theme[data-theme] .character-customization-v18-modal textarea {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-primary {
                border: 1px solid var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-primary:hover:not(:disabled) {
                background: var(--theme-button-hover-background) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-secondary {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-secondary:hover:not(:disabled) {
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-modal button[class*="bg-[#6b1d14]"] {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-theme-card {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
                box-shadow: 0 2px 8px rgba(0,0,0,.045) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-theme-card:hover {
                border-color: var(--theme-border-strong) !important;
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-theme-card--selected {
                border-color: var(--theme-accent-strong) !important;
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--theme-accent-strong) 18%, transparent), 0 5px 16px rgba(0,0,0,.07) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-theme-check--idle {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: transparent !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-theme-check--selected {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-accent-strong) !important;
                color: var(--theme-button-text) !important;
            }

            .character-customization-v18-confirm-backdrop {
                background: rgba(0,0,0,.56) !important;
                backdrop-filter: blur(5px);
            }

            .character-sheet-theme[data-theme] .character-customization-v18-confirm {
                border-color: var(--theme-modal-border) !important;
                background: var(--theme-modal-surface) !important;
                box-shadow: 0 24px 70px rgba(0,0,0,.30) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-confirm-header,
            .character-sheet-theme[data-theme] .character-customization-v18-confirm-footer {
                border-color: var(--theme-divider) !important;
                background: var(--theme-modal-header-background) !important;
            }

            .character-sheet-theme[data-theme] .character-customization-v18-confirm-summary {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
            }

            /* ============================================================
               PROGRESSÃO COM XP — implementação isolada por tema
               A aba normal e a confirmação de progressão são preservadas.
               ============================================================ */
            .character-sheet-theme[data-theme] .sb-xp-overview {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card-alt) !important;
                color: var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-heading {
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-label {
                color: var(--theme-label) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-muted {
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-class,
            .character-sheet-theme[data-theme] .sb-xp-level-box {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-ready {
                border-color: color-mix(in srgb, #c69b4c 57%, var(--theme-border) 43%) !important;
                background: color-mix(in srgb, #c69b4c 12%, var(--theme-surface-card) 88%) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-ready-title {
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-up {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-up:hover:not(:disabled) {
                background: var(--theme-button-hover-background) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-down {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .sb-xp-down:hover:not(:disabled) {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }


            /* Edição das classes e escolha de multiclasse sem interferir no tema. */
            .character-sheet-theme[data-theme] .sb-class-editor {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-card-alt) !important;
                color: var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .sb-class-edit-trigger {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .sb-class-edit-trigger:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }
            /* A edição fica vinculada ao nome, sem criar coluna central vazia. */
            .character-sheet-theme[data-theme] .sb-class-edit-inline {
                border: 1px solid transparent;
                background: transparent;
                color: var(--theme-label);
                opacity: .75;
            }
            .character-sheet-theme[data-theme] .sb-class-edit-inline:hover,
            .character-sheet-theme[data-theme] .sb-class-edit-inline:focus-visible {
                border-color: var(--theme-border);
                background: var(--theme-card-hover);
                color: var(--theme-accent-strong);
                opacity: 1;
            }
            .character-sheet-theme[data-theme] .sb-class-edit-inline svg { pointer-events: none; }
            .character-sheet-theme[data-theme] .sb-class-input {
                width: 100%;
                min-width: 0;
                border: 1px solid var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }
            .character-sheet-theme[data-theme] .sb-class-action {
                border: 1px solid var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }
            .character-sheet-theme[data-theme] .sb-class-action:disabled { opacity: .45; cursor: not-allowed; }
            .character-sheet-theme[data-theme] .sb-class-cancel {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .sb-photo-preview {
                background: var(--theme-surface-inset) !important;
            }


            /* ==============================================================
               V25 — CONFIGURAÇÕES ESSENCIAIS (IDENTIDADE / PROGRESSÃO)
               Os demais painéis continuam com as dimensões antigas.
               Em telas baixas ou com editor aberto, rolagem interna permanece
               para não cortar controles nem impedir acesso por teclado.
               ============================================================== */
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"]) {
                max-width: 750px;
                max-height: min(92dvh, 640px);
            }

            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-header {
                padding: 12px 18px 9px !important;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-header > div > div > p {
                display: none;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-header h2 {
                margin-top: 0 !important;
                font-size: 21px !important;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-tabs {
                margin-top: 11px !important;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-tabs > button {
                padding-block: 9px !important;
                font-size: 10px !important;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-body {
                padding: 13px 18px !important;
                overflow-y: auto;
                scrollbar-width: thin;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-footer {
                padding: 9px 18px !important;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-footer > p {
                display: none !important;
            }
            .character-sheet-theme[data-theme]
            .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"])
            .character-customization-v18-footer button {
                padding-block: 8px !important;
                font-size: 10px !important;
            }

            /* Identidade: não esticar linhas até a altura da fotografia. */
            .character-sheet-theme[data-theme] .sb-compact-identity > div:first-child {
                margin-bottom: 11px !important;
                padding-bottom: 9px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-identity > div:first-child h3 {
                font-size: 17px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-identity > div:first-child p {
                display: none;
            }
            .character-sheet-theme[data-theme] .sb-identity-layout {
                align-items: start;
                gap: 13px !important;
            }
            .character-sheet-theme[data-theme] .sb-identity-photo {
                max-width: 120px;
            }
            .character-sheet-theme[data-theme] .sb-identity-photo > span {
                margin-bottom: 4px !important;
            }
            .character-sheet-theme[data-theme] .sb-identity-photo .sb-photo-preview {
                width: 120px;
                max-width: 100%;
                aspect-ratio: 5 / 6;
                border-style: solid;
            }
            .character-sheet-theme[data-theme] .sb-identity-photo > p:not([x-show]) {
                display: none;
            }
            .character-sheet-theme[data-theme] .sb-identity-fields {
                align-content: start;
                grid-auto-rows: min-content;
                align-items: start;
                column-gap: 12px !important;
                row-gap: 11px !important;
            }
            .character-sheet-theme[data-theme] .sb-identity-fields label > span {
                margin-bottom: 5px !important;
            }
            .character-sheet-theme[data-theme] .sb-identity-fields input {
                height: 39px !important;
            }

            /* Progressão: classe, nível e ações em uma linha mais enxuta. */
            .character-sheet-theme[data-theme] .sb-compact-progression > div:first-child {
                padding-bottom: 9px !important;
                margin-bottom: 2px;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression > div:first-child h3 {
                font-size: 17px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression > div:first-child p {
                display: none;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression > div:first-child > div:last-child {
                padding: 6px 9px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression > div:first-child strong {
                font-size: 19px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-progress-standard article,
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-xp-class {
                padding: 7px 11px !important;
                min-height: 46px;
                border-radius: 10px;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-progress-standard article button.h-9 {
                height: 30px !important;
                width: 30px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-progress-standard article .min-w-\[54px\] {
                min-width: 48px;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-xp-overview {
                padding: 8px 11px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-xp-level-box {
                width: 28px;
                height: 28px;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-xp-class .sb-xp-down,
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-xp-class .sb-xp-up {
                height: 29px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression .sb-class-edit-trigger {
                padding-block: 6px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression > div:has(> button.sb-class-edit-trigger) {
                margin-top: 9px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression > div:has(> button.sb-class-edit-trigger) button {
                padding-block: 9px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression > div:has(> button > span > span.font-serif) {
                margin-top: 9px !important;
            }
            .character-sheet-theme[data-theme] .sb-compact-progression button:focus-visible,
            .character-sheet-theme[data-theme] .sb-compact-identity button:focus-visible {
                outline: 2px solid var(--theme-accent-strong);
                outline-offset: 2px;
            }
            @media (max-width: 639px) {
                .character-sheet-theme[data-theme] .sb-identity-layout {
                    grid-template-columns: 1fr !important;
                }
                .character-sheet-theme[data-theme] .sb-identity-photo {
                    width: 100%;
                    max-width: none;
                    display: grid;
                    grid-template-columns: 105px minmax(0, 1fr);
                    gap: 6px 12px;
                    align-items: center;
                }
                .character-sheet-theme[data-theme] .sb-identity-photo > span {grid-column: 1 / -1;}
                .character-sheet-theme[data-theme] .sb-identity-photo .sb-photo-preview {
                    width: 105px;
                    grid-row: span 4;
                }
                .character-sheet-theme[data-theme] .sb-identity-photo > button {
                    width: auto;
                    margin-top: 0 !important;
                }
                .character-sheet-theme[data-theme] .sb-identity-photo > p:not([x-show]) {display:none;}
                .character-sheet-theme[data-theme] .sb-identity-fields {row-gap: 9px !important;}
            }
            @media (max-height: 590px) {
                .character-sheet-theme[data-theme]
                .character-customization-v18-modal:is([data-active-tab="identity"], [data-active-tab="progression"]) {
                    max-height: calc(100dvh - 16px);
                }
            }

        </style>
    @endpush
@endonce


{{-- ================================================================
     ALPINE
================================================================= --}}

<script>
    document.addEventListener(
        'alpine:init',
        () => {
            Alpine.data(
                'characterCustomizationModal',
                () => ({
                    open: false,
                    activeTab: 'identity',

                    themeKeys:
                        @js(
                            array_keys(
                                $themeOptions
                            )
                        ),

                    saving: false,
                    saveError: null,

                    identityImageFile: null,
                    identityImagePreview: null,
                    identityImageRemove: false,
                    identityImageError: null,

                    photoSaving: false,
                    classUpdateUrlTemplate: '',
                    multiclassUrl: '',
                    editingClassId: null,
                    classEdit: { name: '', subclass: '' },
                    classSaving: false,
                    classError: null,
                    multiclassOpen: false,
                    multiclassForm: { name: '', subclass: '' },
                    multiclassSaving: false,
                    multiclassError: null,

                    xpThresholds: @js(\App\Support\CharacterExperience::table()),

                    levelingClassId: null,
                    levelingDirection: null,

                    levelConfirmOpen: false,
                    levelConfirmMode: null,
                    levelConfirmClass: null,

                    progressionError: null,
                    progressionMessage: null,

                    proficiencyDrawerOpen: false,
                    morquenRuleOpen: false,
                    exhaustionRuleOpen: false,

                    specialRulesUnlocked: false,
                    specialRulesCode: '',
                    specialRulesCodeError: null,

                    proficiencySaving: false,
                    proficiencyError: null,
                    proficiencyMessage: null,
                    customProficiencyValue: 2,

                    requiresPageRefresh: false,

                    initial: {
                        name: '',
                        background: '',
                        species: '',
                        image_url: null,
                        level: 1,

                        experience: {
                            enabled: false,
                            points: 0,
                            campaign_id: null,
                        },

                        proficiency: {
                            current: 2,
                            calculated: 2,
                            custom_enabled: false,
                        },

                        settings: {
                            display: {
                                show_defenses: true,
                            },

                            progression: {
                                proficiency_custom_enabled: false,
                            },

                            optional_rules: {
                                morquen: false,
                                exhaustion: false,
                                transformations: false,
                            },
                        },

                        classes: [],
                    },

                    /*
                     * IMPORTANTE:
                     *
                     * draft nunca é null.
                     * Os x-models abaixo são inicializados pelo Alpine
                     * mesmo quando o modal está escondido.
                     */
                    draft: {
                        name: '',
                        background: '',
                        species: '',
                        image_url: null,
                        level: 1,

                        experience: {
                            enabled: false,
                            points: 0,
                            campaign_id: null,
                        },

                        proficiency: {
                            current: 2,
                            calculated: 2,
                            custom_enabled: false,
                        },

                        settings: {
                            display: {
                                show_defenses: true,
                            },

                            progression: {
                                proficiency_custom_enabled: false,
                            },

                            optional_rules: {
                                morquen: false,
                                exhaustion: false,
                                transformations: false,
                            },
                        },

                        classes: [],
                    },

                    saveUrl: '',
                    imageSaveUrl: '',
                    levelUpUrl: '',
                    levelDownUrl: '',
                    proficiencyUrl: '',


                    /*
                    |--------------------------------------------------------------------------
                    | INIT
                    |--------------------------------------------------------------------------
                    */

                    init() {
                        this.saveUrl =
                            this.$el.dataset.saveUrl
                            ?? '';

                        this.imageSaveUrl =
                            this.$el.dataset.imageSaveUrl
                            ?? '';

                        this.levelUpUrl =
                            this.$el.dataset.levelUpUrl
                            ?? '';

                        this.levelDownUrl =
                            this.$el.dataset.levelDownUrl
                            ?? '';

                        this.proficiencyUrl =
                            this.$el.dataset.proficiencyUrl
                            ?? '';

                        this.classUpdateUrlTemplate =
                            this.$el.dataset.classUpdateUrlTemplate ?? '';
                        this.multiclassUrl =
                            this.$el.dataset.multiclassUrl ?? '';

                        try {
                            const parsed =
                                JSON.parse(
                                    this.$el.dataset.initial
                                    ?? '{}'
                                );

                            this.initial =
                                this.normalizeInitial(
                                    parsed
                                );

                        } catch (error) {
                            console.error(
                                'Erro ao carregar configuração inicial:',
                                error
                            );
                        }

                        this.resetDraft();

                        try {
                            this.specialRulesUnlocked =
                                sessionStorage.getItem(
                                    'spellbound.special_rules_unlocked'
                                ) === '1';
                        } catch (error) {
                            this.specialRulesUnlocked =
                                false;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | HELPERS
                    |--------------------------------------------------------------------------
                    */

                    canLevelUpWithExperience() {
                        const xp = this.draft?.experience ?? {};
                        if (!xp.enabled) return true;

                        const level = Math.max(1, Number(this.draft.level) || 1);
                        if (level >= 20) return false;

                        const next = Number(this.xpThresholds?.[level + 1]);
                        return Number.isFinite(next)
                            && Number(xp.points ?? 0) >= next;
                    },

                    xpUntilLevelUp() {
                        const level = Math.max(1, Number(this.draft.level) || 1);
                        const next = Number(this.xpThresholds?.[level + 1] ?? 0);
                        return Math.max(0, next - Number(this.draft?.experience?.points ?? 0));
                    },

                    formatXp(value) {
                        return Math.max(0, Number(value) || 0).toLocaleString('pt-BR');
                    },

                    xpNextThreshold() {
                        const level = Math.max(1, Number(this.draft.level) || 1);
                        if (level >= 20) return null;
                        const value = Number(this.xpThresholds?.[level + 1]);
                        return Number.isFinite(value) ? value : null;
                    },

                    xpLevelProgress() {
                        const level = Math.max(1, Number(this.draft.level) || 1);
                        if (level >= 20) return 100;

                        const current = Number(this.xpThresholds?.[level] ?? 0);
                        const next = this.xpNextThreshold();
                        if (next === null || next <= current) return 0;

                        const points = Math.max(0, Number(this.draft?.experience?.points) || 0);
                        return Math.round(
                            Math.max(0, Math.min(100, (points - current) * 100 / (next - current))) * 10
                        ) / 10;
                    },

                    clone(value) {
                        return JSON.parse(
                            JSON.stringify(
                                value
                            )
                        );
                    },

                    normalizeInitial(value) {
                        const source =
                            value
                            && typeof value === 'object'
                                ? value
                                : {};

                        const settings =
                            source.settings
                            && typeof source.settings === 'object'
                                ? source.settings
                                : {};

                        const display =
                            settings.display
                            && typeof settings.display === 'object'
                                ? settings.display
                                : {};

                        const progression =
                            settings.progression
                            && typeof settings.progression === 'object'
                                ? settings.progression
                                : {};

                        const optionalRules =
                            settings.optional_rules
                            && typeof settings.optional_rules === 'object'
                                ? settings.optional_rules
                                : {};

                        const appearance =
                            settings.appearance
                            && typeof settings.appearance === 'object'
                                ? settings.appearance
                                : {};

                        const proficiency =
                            source.proficiency
                            && typeof source.proficiency === 'object'
                                ? source.proficiency
                                : {};

                        return {
                            name:
                                String(
                                    source.name
                                    ?? ''
                                ),

                            image_url:
                                source.image_url
                                ? String(
                                    source.image_url
                                )
                                : null,

                            background:
                                String(
                                    source.background
                                    ?? ''
                                ),

                            species:
                                String(
                                    source.species
                                    ?? ''
                                ),

                            level:
                                Math.max(
                                    1,
                                    parseInt(
                                        source.level
                                    )
                                    || 1
                                ),

                            experience: {
                                enabled: !!source.experience?.enabled,
                                points: Math.max(0, Number(source.experience?.points ?? 0)),
                                campaign_id: source.experience?.campaign_id ?? null,
                            },

                            proficiency: {
                                current:
                                    parseInt(
                                        proficiency.current
                                    )
                                    || 0,

                                calculated:
                                    parseInt(
                                        proficiency.calculated
                                    )
                                    || 2,

                                custom_enabled:
                                    !!proficiency.custom_enabled,
                            },

                            settings: {
                                display: {
                                    show_defenses:
                                        Object.prototype.hasOwnProperty.call(
                                            display,
                                            'show_defenses'
                                        )
                                            ? !!display.show_defenses
                                            : true,
                                },

                                progression: {
                                    proficiency_custom_enabled:
                                        Object.prototype.hasOwnProperty.call(
                                            progression,
                                            'proficiency_custom_enabled'
                                        )
                                            ? !!progression.proficiency_custom_enabled
                                            : !!proficiency.custom_enabled,
                                },

                                appearance: {
                                    theme:
                                        this.normalizeTheme(
                                            appearance.theme
                                            ?? 'spellbound'
                                        ),
                                },

                                optional_rules: {
                                    ...optionalRules,

                                    morquen:
                                        !!optionalRules.morquen,

                                    exhaustion:
                                        !!optionalRules.exhaustion,

                                    transformations:
                                        !!optionalRules.transformations,
                                },
                            },

                            classes:
                                Array.isArray(
                                    source.classes
                                )
                                    ? source.classes
                                    : [],
                        };
                    },

                    normalizeTheme(value) {
                        const theme =
                            String(
                                value
                                ?? 'spellbound'
                            )
                                .trim()
                                .toLowerCase();

                        return this.themeKeys.includes(
                            theme
                        )
                            ? theme
                            : 'spellbound';
                    },

                    sheetThemeRoot() {
                        return document
                            .querySelector(
                                '.character-sheet-theme'
                            );
                    },

                    applyThemePreview(theme) {
                        const normalized =
                            this.normalizeTheme(
                                theme
                            );

                        if (
                            !this.draft.settings.appearance
                            || typeof this.draft.settings.appearance !== 'object'
                        ) {
                            this.draft.settings.appearance =
                                {};
                        }

                        this.draft.settings.appearance.theme =
                            normalized;

                        const root =
                            this.sheetThemeRoot();

                        if (root) {
                            root.setAttribute(
                                'data-theme',
                                normalized
                            );
                        }
                    },

                    restoreThemePreview() {
                        const initialTheme =
                            this.normalizeTheme(
                                this.initial
                                    ?.settings
                                    ?.appearance
                                    ?.theme
                                ?? 'spellbound'
                            );

                        const root =
                            this.sheetThemeRoot();

                        if (root) {
                            root.setAttribute(
                                'data-theme',
                                initialTheme
                            );
                        }
                    },

                    themeSelected(theme) {
                        return (
                            this.normalizeTheme(
                                this.draft
                                    ?.settings
                                    ?.appearance
                                    ?.theme
                                ?? 'spellbound'
                            )
                            ===
                            this.normalizeTheme(
                                theme
                            )
                        );
                    },


                    resetDraft() {
                        this.draft =
                            this.clone(
                                this.initial
                            );

                        this.identityImageFile =
                            null;

                        this.identityImagePreview =
                            this.draft.image_url
                            ?? null;

                        this.identityImageRemove =
                            false;

                        this.identityImageError =
                            null;

                        this.customProficiencyValue =
                            parseInt(
                                this.draft.proficiency.current
                            );

                        if (
                            Number.isNaN(
                                this.customProficiencyValue
                            )
                        ) {
                            this.customProficiencyValue =
                                this.draft.proficiency.calculated;
                        }
                    },

                    unlockSpecialRules() {
                        this.specialRulesCodeError =
                            null;

                        const code =
                            String(
                                this.specialRulesCode
                                ?? ''
                            )
                                .trim()
                                .toUpperCase();

                        if (code !== 'MORQUEN') {
                            this.specialRulesCodeError =
                                'Código inválido.';

                            return;
                        }

                        this.specialRulesUnlocked =
                            true;

                        this.specialRulesCode =
                            '';

                        try {
                            sessionStorage.setItem(
                                'spellbound.special_rules_unlocked',
                                '1'
                            );
                        } catch (error) {
                            // Mantém desbloqueado no componente.
                        }
                    },


                    lockSpecialRules() {
                        this.specialRulesUnlocked =
                            false;

                        this.specialRulesCode =
                            '';

                        this.specialRulesCodeError =
                            null;

                        this.morquenRuleOpen =
                            false;

                        try {
                            sessionStorage.removeItem(
                                'spellbound.special_rules_unlocked'
                            );
                        } catch (error) {
                            // Nada a fazer.
                        }
                    },


                    handleIdentityImage(event) {
                        const file =
                            event?.target?.files?.[0]
                            ?? null;

                        if (!file) {
                            return;
                        }

                        this.identityImageError =
                            null;

                        const allowedTypes = [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ];

                        if (
                            !allowedTypes.includes(
                                file.type
                            )
                        ) {
                            this.identityImageError =
                                'Use uma imagem JPG, PNG ou WEBP.';

                            event.target.value =
                                '';

                            return;
                        }

                        if (
                            file.size
                            > 5 * 1024 * 1024
                        ) {
                            this.identityImageError =
                                'A imagem deve ter no máximo 5 MB.';

                            event.target.value =
                                '';

                            return;
                        }

                        this.identityImageFile =
                            file;

                        this.identityImageRemove =
                            false;

                        if (
                            this.identityImagePreview
                            && this.identityImagePreview.startsWith(
                                'blob:'
                            )
                        ) {
                            URL.revokeObjectURL(
                                this.identityImagePreview
                            );
                        }

                        this.identityImagePreview =
                            URL.createObjectURL(
                                file
                            );
                    },


                    removeIdentityImage() {
                        if (
                            this.identityImagePreview
                            && this.identityImagePreview.startsWith(
                                'blob:'
                            )
                        ) {
                            URL.revokeObjectURL(
                                this.identityImagePreview
                            );
                        }

                        this.identityImageFile =
                            null;

                        this.identityImagePreview =
                            null;

                        this.identityImageRemove =
                            true;

                        this.identityImageError =
                            null;
                    },


                    async savePhotoNow() {
                        if (this.photoSaving || (!this.identityImageFile && !this.identityImageRemove)) return;
                        this.photoSaving = true;
                        this.identityImageError = null;
                        try {
                            await this.saveIdentityImage();
                            window.location.reload();
                        } catch (error) {
                            this.identityImageError = error?.message || 'Não foi possível salvar a foto.';
                        } finally {
                            this.photoSaving = false;
                        }
                    },

                    async saveIdentityImage() {
                        if (!this.identityImageFile && !this.identityImageRemove) return;
                        if (!this.imageSaveUrl) {
                            throw new Error('A rota de upload da foto não está disponível.');
                        }

                        const formData =
                            new FormData();

                        if (this.identityImageFile) {
                            formData.append(
                                'image',
                                this.identityImageFile
                            );
                        }

                        formData.append(
                            'remove_image',
                            this.identityImageRemove
                                ? '1'
                                : '0'
                        );

                        const response =
                            await fetch(
                                this.imageSaveUrl,
                                {
                                    method:
                                        'POST',

                                    headers: {
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
                                            ?? '',

                                        'X-Requested-With':
                                            'XMLHttpRequest',
                                    },

                                    body:
                                        formData,
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
                                        ?? 'Não foi possível atualizar a foto.'
                                    )
                            );
                        }

                        this.draft.image_url =
                            data
                                ?.character
                                ?.image_url
                            ?? null;

                        this.initial.image_url =
                            this.draft.image_url;
                    },


                    signed(value) {
                        const number =
                            parseInt(
                                value
                            );

                        if (
                            Number.isNaN(
                                number
                            )
                        ) {
                            return '—';
                        }

                        return number >= 0
                            ? `+${number}`
                            : `${number}`;
                    },

                    calculatedProficiency(level) {
                        const value =
                            Math.max(
                                1,
                                parseInt(
                                    level
                                )
                                || 1
                            );

                        if (value >= 17) return 6;
                        if (value >= 13) return 5;
                        if (value >= 9) return 4;
                        if (value >= 5) return 3;

                        return 2;
                    },

                    csrfToken() {
                        return document
                            .querySelector(
                                'meta[name="csrf-token"]'
                            )
                            ?.getAttribute(
                                'content'
                            )
                            ?? '';
                    },

                    async jsonResponse(response) {
                        const data =
                            await response
                                .json()
                                .catch(
                                    () => ({})
                                );

                        if (!response.ok) {
                            const messages =
                                data
                                    && data.errors
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
                                        data.message
                                        ?? 'Não foi possível concluir a operação.'
                                    )
                            );
                        }

                        return data;
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | MODAL
                    |--------------------------------------------------------------------------
                    */

                    openModal(tab = 'identity') {
                        this.resetDraft();

                        this.activeTab =
                            [
                                'identity',
                                'progression',
                                'themes',
                                'rules',
                            ].includes(tab)
                                ? tab
                                : 'identity';

                        this.saveError =
                            null;

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        this.proficiencyError =
                            null;

                        this.proficiencyMessage =
                            null;

                        this.proficiencyDrawerOpen =
                            false;

                        this.morquenRuleOpen =
                            false;

                        this.levelConfirmOpen =
                            false;

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;

                        this.editingClassId = null;
                        this.classError = null;
                        this.multiclassOpen = false;
                        this.multiclassError = null;

                        this.open =
                            true;
                    },

                    closeModal() {
                        if (
                            this.saving
                            || this.levelingClassId !== null
                            || this.proficiencySaving
                        ) {
                            return;
                        }

                        if (
                            this.requiresPageRefresh
                        ) {
                            window.location.reload();
                            return;
                        }

                        this.restoreThemePreview();

                        this.open =
                            false;

                        this.activeTab =
                            'identity';

                        this.saveError =
                            null;

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        this.proficiencyError =
                            null;

                        this.proficiencyMessage =
                            null;

                        this.levelConfirmOpen =
                            false;

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;

                        this.resetDraft();
                    },

                    setTab(tab) {
                        this.activeTab =
                            tab;

                        this.saveError =
                            null;
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | PROGRESSÃO
                    |--------------------------------------------------------------------------
                    */

                    beginClassEdit(item) {
                        if (!item?.id || this.classSaving) return;
                        this.multiclassOpen = false;
                        this.classError = null;
                        this.editingClassId = Number(item.id);
                        this.classEdit = {
                            name: String(item.name ?? ''),
                            subclass: String(item.subclass ?? ''),
                        };
                    },

                    cancelClassEdit() {
                        if (this.classSaving) return;
                        this.editingClassId = null;
                        this.classError = null;
                    },

                    async saveClassEdit() {
                        if (this.classSaving || !this.editingClassId) return;
                        const name = String(this.classEdit.name ?? '').trim();
                        const subclass = String(this.classEdit.subclass ?? '').trim();
                        if (!name) {
                            this.classError = 'Informe o nome da classe.';
                            return;
                        }
                        if (!this.classUpdateUrlTemplate) {
                            this.classError = 'A rota para editar classes não está configurada.';
                            return;
                        }
                        this.classSaving = true;
                        this.classError = null;
                        try {
                            const url = this.classUpdateUrlTemplate.replace(
                                '__CLASS_ID__', encodeURIComponent(String(this.editingClassId))
                            );
                            const response = await fetch(url, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken(),
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ name, subclass: subclass || null }),
                            });
                            const data = await this.jsonResponse(response);
                            this.draft.classes = this.draft.classes.map(item =>
                                Number(item.id) === Number(this.editingClassId)
                                    ? { ...item, name: data.class.name, subclass: data.class.subclass }
                                    : item
                            );
                            this.initial = this.clone(this.draft);
                            this.requiresPageRefresh = true;
                            this.progressionMessage = 'Classe e subclasse atualizadas.';
                            this.editingClassId = null;
                        } catch (error) {
                            this.classError = error?.message ?? 'Não foi possível atualizar a classe.';
                        } finally {
                            this.classSaving = false;
                        }
                    },

                    beginMulticlass() {
                        if (Number(this.draft.level) >= 20 || !this.canLevelUpWithExperience()) return;
                        this.editingClassId = null;
                        this.multiclassError = null;
                        this.multiclassForm = { name: '', subclass: '' };
                        this.multiclassOpen = true;
                    },

                    async createMulticlass() {
                        if (this.multiclassSaving || !this.multiclassOpen) return;
                        const name = String(this.multiclassForm.name ?? '').trim();
                        const subclass = String(this.multiclassForm.subclass ?? '').trim();
                        if (!name) {
                            this.multiclassError = 'Informe o nome da nova classe.';
                            return;
                        }
                        if (!this.multiclassUrl) {
                            this.multiclassError = 'A rota de multiclasse não está configurada.';
                            return;
                        }
                        if (!this.canLevelUpWithExperience()) {
                            this.multiclassError = 'Você ainda não tem XP suficiente para subir de nível.';
                            return;
                        }
                        this.multiclassSaving = true;
                        this.multiclassError = null;
                        try {
                            const response = await fetch(this.multiclassUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken(),
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({
                                    name,
                                    subclass: subclass || null,
                                    campaign_id: this.draft?.experience?.campaign_id ?? null,
                                }),
                            });
                            const data = await this.jsonResponse(response);
                            this.draft.level = Number(data?.character?.level) || this.draft.level;
                            this.draft.classes = Array.isArray(data?.classes)
                                ? data.classes.map(item => ({
                                    id: item.id,
                                    name: item.class ?? item.name ?? 'Classe',
                                    subclass: item.subclass ?? null,
                                    level: Number(item.level) || 1,
                                    is_primary: !!item.is_primary,
                                }))
                                : this.draft.classes;
                            if (data?.character?.proficiency_bonus !== undefined) {
                                this.draft.proficiency.current = Number(data.character.proficiency_bonus);
                            }
                            if (data?.progression?.calculated_proficiency !== undefined) {
                                this.draft.proficiency.calculated = Number(data.progression.calculated_proficiency);
                            }
                            this.initial = this.clone(this.draft);
                            this.requiresPageRefresh = true;
                            this.progressionMessage = data?.message || 'Nova classe adicionada com sucesso.';
                            this.multiclassOpen = false;
                        } catch (error) {
                            this.multiclassError = error?.message ?? 'Não foi possível adicionar a multiclasse.';
                        } finally {
                            this.multiclassSaving = false;
                        }
                    },

                    requestLevelChange(
                        item,
                        mode
                    ) {
                        if (
                            this.levelingClassId !== null
                            || !item
                            || !item.id
                            || !['up', 'down'].includes(
                                mode
                            )
                        ) {
                            return;
                        }

                        const currentLevel =
                            parseInt(
                                item.level
                            )
                            || 1;

                        if (
                            mode === 'up'
                            && (
                                this.draft.level >= 20
                                || currentLevel >= 20
                            )
                        ) {
                            return;
                        }

                        if (mode === 'up' && !this.canLevelUpWithExperience()) {
                            this.progressionError = 'Faltam '
                                + this.formatXp(this.xpUntilLevelUp())
                                + ' XP para subir de nível.';
                            return;
                        }

                        if (
                            mode === 'down'
                            && currentLevel <= 1
                        ) {
                            return;
                        }

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        this.levelConfirmMode =
                            mode;

                        this.levelConfirmClass =
                            this.clone(
                                item
                            );

                        this.levelConfirmOpen =
                            true;
                    },


                    cancelLevelConfirmation() {
                        if (
                            this.levelingClassId !== null
                        ) {
                            return;
                        }

                        this.levelConfirmOpen =
                            false;

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;
                    },


                    async confirmLevelChange() {
                        if (
                            !this.levelConfirmOpen
                            || !this.levelConfirmClass
                            || !this.levelConfirmMode
                        ) {
                            return;
                        }

                        const item =
                            this.clone(
                                this.levelConfirmClass
                            );

                        const mode =
                            this.levelConfirmMode;

                        if (mode === 'up' && !this.canLevelUpWithExperience()) {
                            this.progressionError = 'Você ainda não tem XP suficiente.';
                            this.cancelLevelConfirmation();
                            return;
                        }

                        this.levelConfirmOpen =
                            false;

                        await this.changeLevel(
                            item,
                            mode
                        );

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;
                    },


                    async changeLevel(
                        item,
                        mode
                    ) {
                        const isUp =
                            mode === 'up';

                        const url =
                            isUp
                                ? this.levelUpUrl
                                : this.levelDownUrl;

                        if (
                            this.levelingClassId !== null
                            || !url
                            || !item
                            || !item.id
                        ) {
                            return;
                        }

                        this.levelingClassId =
                            item.id;

                        this.levelingDirection =
                            mode;

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        try {
                            const response =
                                await fetch(
                                    url,
                                    {
                                        method:
                                            'PATCH',

                                        headers: {
                                            'Content-Type':
                                                'application/json',

                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                this.csrfToken(),

                                            'X-Requested-With':
                                                'XMLHttpRequest',
                                        },

                                        body:
                                            JSON.stringify({
                                                class_id:
                                                    item.id,

                                                campaign_id:
                                                    this.draft?.experience?.campaign_id ?? null,
                                            }),
                                    }
                                );

                            const data =
                                await this.jsonResponse(
                                    response
                                );

                            this.draft.level =
                                parseInt(
                                    data?.character?.level
                                )
                                || this.draft.level;

                            this.draft.classes =
                                Array.isArray(
                                    data?.classes
                                )
                                    ? data.classes.map(
                                        classItem => ({
                                            id:
                                                classItem.id,

                                            name:
                                                classItem.class
                                                ?? 'Classe',

                                            subclass:
                                                classItem.subclass
                                                ?? null,

                                            level:
                                                parseInt(
                                                    classItem.level
                                                )
                                                || 1,

                                            is_primary:
                                                !!classItem.is_primary,
                                        })
                                    )
                                    : this.draft.classes;

                            const currentProficiency =
                                parseInt(
                                    data
                                        ?.character
                                        ?.proficiency_bonus
                                );

                            if (
                                !Number.isNaN(
                                    currentProficiency
                                )
                            ) {
                                this.draft.proficiency.current =
                                    currentProficiency;
                            }

                            const calculated =
                                parseInt(
                                    data
                                        ?.progression
                                        ?.calculated_proficiency
                                );

                            this.draft.proficiency.calculated =
                                Number.isNaN(
                                    calculated
                                )
                                    ? this.calculatedProficiency(
                                        this.draft.level
                                    )
                                    : calculated;

                            this.draft.proficiency.custom_enabled =
                                !!data
                                    ?.progression
                                    ?.proficiency_custom_enabled;

                            this.draft
                                .settings
                                .progression
                                .proficiency_custom_enabled =
                                    this.draft
                                        .proficiency
                                        .custom_enabled;

                            this.customProficiencyValue =
                                this.draft.proficiency.current;

                            this.initial =
                                this.clone(
                                    this.draft
                                );

                            this.progressionMessage =
                                data?.message
                                ?? (
                                    isUp
                                        ? 'Nível aumentado.'
                                        : 'Nível reduzido.'
                                );

                            this.requiresPageRefresh =
                                true;

                        } catch (error) {
                            console.error(
                                isUp
                                    ? 'Erro ao subir de nível:'
                                    : 'Erro ao reduzir nível:',
                                error
                            );

                            this.progressionError =
                                error?.message
                                ?? (
                                    isUp
                                        ? 'Não foi possível subir de nível.'
                                        : 'Não foi possível reduzir o nível.'
                                );

                        } finally {
                            this.levelingClassId =
                                null;

                            this.levelingDirection =
                                null;
                        }
                    },


                    async saveCustomProficiency() {
                        if (
                            this.proficiencySaving
                            || !this.proficiencyUrl
                        ) {
                            return;
                        }

                        const value =
                            parseInt(
                                this.customProficiencyValue
                            );

                        if (
                            Number.isNaN(
                                value
                            )
                        ) {
                            this.proficiencyError =
                                'Informe um número inteiro para a proficiência.';

                            return;
                        }

                        await this.persistProficiency(
                            true,
                            value
                        );
                    },


                    async restoreAutomaticProficiency() {
                        if (
                            this.proficiencySaving
                            || !this.proficiencyUrl
                        ) {
                            return;
                        }

                        await this.persistProficiency(
                            false,
                            null
                        );
                    },


                    async persistProficiency(
                        enabled,
                        value
                    ) {
                        this.proficiencySaving =
                            true;

                        this.proficiencyError =
                            null;

                        this.proficiencyMessage =
                            null;

                        try {
                            const response =
                                await fetch(
                                    this.proficiencyUrl,
                                    {
                                        method:
                                            'PATCH',

                                        headers: {
                                            'Content-Type':
                                                'application/json',

                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                this.csrfToken(),

                                            'X-Requested-With':
                                                'XMLHttpRequest',
                                        },

                                        body:
                                            JSON.stringify({
                                                enabled:
                                                    !!enabled,

                                                value:
                                                    enabled
                                                        ? value
                                                        : null,
                                            }),
                                    }
                                );

                            const data =
                                await this.jsonResponse(
                                    response
                                );

                            const current =
                                parseInt(
                                    data
                                        ?.character
                                        ?.proficiency_bonus
                                );

                            if (
                                !Number.isNaN(
                                    current
                                )
                            ) {
                                this.draft.proficiency.current =
                                    current;
                            }

                            const calculated =
                                parseInt(
                                    data
                                        ?.progression
                                        ?.calculated_proficiency
                                );

                            if (
                                !Number.isNaN(
                                    calculated
                                )
                            ) {
                                this.draft.proficiency.calculated =
                                    calculated;
                            }

                            this.draft.proficiency.custom_enabled =
                                !!data
                                    ?.progression
                                    ?.proficiency_custom_enabled;

                            this.draft
                                .settings
                                .progression
                                .proficiency_custom_enabled =
                                    this.draft
                                        .proficiency
                                        .custom_enabled;

                            this.customProficiencyValue =
                                this.draft.proficiency.current;

                            this.initial =
                                this.clone(
                                    this.draft
                                );

                            this.proficiencyMessage =
                                data?.message
                                ?? (
                                    enabled
                                        ? 'Proficiência customizada aplicada.'
                                        : 'Proficiência automática restaurada.'
                                );

                            this.requiresPageRefresh =
                                true;

                        } catch (error) {
                            console.error(
                                'Erro ao atualizar proficiência:',
                                error
                            );

                            this.proficiencyError =
                                error?.message
                                ?? 'Não foi possível atualizar a proficiência.';

                        } finally {
                            this.proficiencySaving =
                                false;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | SALVAR
                    |--------------------------------------------------------------------------
                    */

                    async save() {
                        if (
                            this.saving
                            || this.photoSaving
                            || !this.saveUrl
                        ) {
                            return;
                        }

                        const name =
                            String(
                                this.draft.name
                                ?? ''
                            ).trim();

                        if (!name) {
                            this.saveError =
                                'Informe o nome do personagem.';

                            this.activeTab =
                                'identity';

                            return;
                        }

                        this.saving =
                            true;

                        this.saveError =
                            null;

                        const payload = {
                            name,

                            background:
                                String(
                                    this.draft.background
                                    ?? ''
                                ).trim(),

                            species:
                                String(
                                    this.draft.species
                                    ?? ''
                                ).trim(),

                            sheet_settings:
                                this.draft.settings,
                        };

                        try {
                            const response =
                                await fetch(
                                    this.saveUrl,
                                    {
                                        method:
                                            'PATCH',

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
                                                ?? '',

                                            'X-Requested-With':
                                                'XMLHttpRequest',
                                        },

                                        body:
                                            JSON.stringify(
                                                payload
                                            ),
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
                                    data
                                        && data.errors
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
                                            data.message
                                            ?? 'Não foi possível salvar.'
                                        )
                                );
                            }

                            /*
                             * A identidade textual já foi salva.
                             * Se houver troca/remoção de foto, concluímos o
                             * multipart antes do reload.
                             */
                            await this.saveIdentityImage();


                            /*
                             * Nesta etapa usamos reload propositalmente.
                             *
                             * Isso mantém Hero, Defesas, XP, foto e futuras
                             * regras totalmente sincronizados com o servidor
                             * sem duplicar estado entre vários componentes.
                             */
                            window.location.reload();

                        } catch (error) {
                            console.error(
                                'Erro ao salvar customização:',
                                error
                            );

                            this.saveError =
                                error
                                && error.message
                                    ? error.message
                                    : 'Não foi possível salvar.';

                        } finally {
                            this.saving =
                                false;
                        }
                    },
                })
            );
        }
    );
</script>


{{-- ================================================================
     ROOT
================================================================= --}}

<div
    x-data="characterCustomizationModal"

    data-initial="{!! $encodedInitialPayload !!}"

    data-save-url="{{ route(
        'characters.customization.update',
        $character
    ) }}"

    data-image-save-url="{{ route(
        'characters.customization.image',
        $character
    ) }}"

    data-level-up-url="{{ route(
        'characters.progression.level-up',
        $character
    ) }}"

    data-level-down-url="{{ route(
        'characters.progression.level-down',
        $character
    ) }}"

    data-class-update-url-template="{{ route(
        'characters.progression.class.update',
        ['character' => $character, 'classId' => '__CLASS_ID__']
    ) }}"

    data-multiclass-url="{{ route(
        'characters.progression.multiclass.store',
        $character
    ) }}"

    data-proficiency-url="{{ route(
        'characters.progression.proficiency',
        $character
    ) }}"

    @open-character-customization.window="
        openModal(
            $event.detail && $event.detail.tab
                ? $event.detail.tab
                : 'identity'
        )
    "

    @keydown.escape.window="
        if (levelConfirmOpen) {
            cancelLevelConfirmation()
        } else if (open) {
            closeModal()
        }
    "
>

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

            :data-theme="normalizeTheme(draft?.settings?.appearance?.theme)"

            class="
                character-sheet-theme
                character-theme-modal-layer
                character-customization-v18-layer

                fixed
                inset-0
                z-[190]

                flex
                items-center
                justify-center

                p-3
                sm:p-6
            "

            role="dialog"
            aria-modal="true"
            aria-label="Configurações do personagem"
        >

            {{-- BACKDROP --}}

            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="closeModal()"
            ></div>


            {{-- PAINEL --}}

            <div
                @click.stop
                :data-active-tab="activeTab"

                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    character-customization-v18-modal

                    relative
                    z-10

                    flex
                    max-h-[90vh]
                    w-full
                    max-w-[820px]
                    flex-col

                    overflow-hidden

                    rounded-2xl
                    border
                "
            >

                {{-- ====================================================
                     HEADER
                ===================================================== --}}

                <header
                    class="
                        character-theme-modal-header
                        character-customization-v18-header

                        shrink-0

                        border-b

                        px-4
                        pb-3
                        pt-4

                        sm:px-5
                    "
                >

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
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]

                                    text-[#8c6239]
                                "
                            >
                                Ficha do personagem
                            </p>

                            <h2
                                class="
                                    mt-1

                                    truncate

                                    font-serif
                                    text-[23px]
                                    font-black
                                    leading-none

                                    character-theme-modal-title
                                    character-customization-v18-title
                                "
                            >
                                Configurações
                            </h2>

                            <p
                                class="
                                    mt-1.5

                                    text-[12px]
                                    leading-relaxed

                                    character-theme-modal-copy
                                    character-customization-v18-copy
                                "
                            >
                                Identidade, progressão e regras opcionais da ficha.
                            </p>

                        </div>


                        <button
                            type="button"

                            @click="closeModal()"

                            :disabled="
                                saving
                                || levelingClassId !== null
                                || proficiencySaving
                            "

                            class="
                                flex
                                h-8
                                w-8
                                shrink-0
                                items-center
                                justify-center

                                rounded-lg

                                text-[#8c6239]

                                transition

                                hover:bg-[#fffdf8]/55
                                hover:text-[#53150f]

                                disabled:opacity-40
                            "

                            title="Fechar"
                            aria-label="Fechar configurações"
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


                    {{-- ABAS --}}

                    <nav
                        class="
                            mt-4

                            grid
                            grid-cols-4

                            overflow-hidden

                            rounded-lg
                            border
                            border-[#a0774d]/28

                            bg-[#f7eee2]
                        "
                        aria-label="Seções das configurações"
                    >

                        <button
                            type="button"

                            @click="setTab('identity')"

                            class="
                                border-r
                                border-[#a0774d]/22

                                px-2
                                py-3

                                character-customization-v18-tab

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.12em]

                                transition
                            "

                            :class="
                                activeTab === 'identity'
                                    ? 'character-customization-v18-tab--active'
                                    : 'character-customization-v18-tab--inactive'
                            "
                        >
                            Identidade
                        </button>


                        <button
                            type="button"

                            @click="setTab('progression')"

                            class="
                                border-r
                                border-[#a0774d]/22

                                px-2
                                py-3

                                character-customization-v18-tab

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.12em]

                                transition
                            "

                            :class="
                                activeTab === 'progression'
                                    ? 'character-customization-v18-tab--active'
                                    : 'character-customization-v18-tab--inactive'
                            "
                        >
                            Progressão
                        </button>


                        <button
                            type="button"

                            @click="setTab('themes')"

                            class="
                                border-r
                                border-[#a0774d]/22

                                px-2
                                py-3

                                character-customization-v18-tab

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.12em]

                                transition
                            "

                            :class="
                                activeTab === 'themes'
                                    ? 'character-customization-v18-tab--active'
                                    : 'character-customization-v18-tab--inactive'
                            "
                        >
                            Temas
                        </button>


                        <button
                            type="button"

                            @click="setTab('rules')"

                            class="
                                px-2
                                py-3

                                character-customization-v18-tab

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.12em]

                                transition
                            "

                            :class="
                                activeTab === 'rules'
                                    ? 'character-customization-v18-tab--active'
                                    : 'character-customization-v18-tab--inactive'
                            "
                        >
                            Regras Opcionais
                        </button>

                    </nav>

                </header>


                {{-- ====================================================
                     BODY
                ===================================================== --}}

                <div
                    class="
                        character-customization-v18-body

                        min-h-0
                        flex-1
                        overflow-y-auto
                        [scrollbar-width:none]
                        [&::-webkit-scrollbar]:hidden

                        p-4
                        sm:p-5
                    "
                >

                    {{-- =================================================
                         IDENTIDADE
                    ================================================== --}}

                    <section
                        class="sb-compact-identity"
                        x-show="
                            activeTab === 'identity'
                        "
                    >

                        <div
                            class="
                                mb-4

                                border-b
                                border-[#b08c62]/28

                                pb-3
                            "
                        >
                            <h3
                                class="
                                    font-serif
                                    text-[19px]
                                    font-black

                                    text-[#53150f]
                                "
                            >
                                Identidade do personagem
                            </h3>

                            <p
                                class="
                                    mt-1

                                    text-[11px]
                                    leading-relaxed

                                    text-[#7d604d]
                                "
                            >
                                Estes dados aparecem diretamente no cabeçalho da ficha.
                            </p>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-1
                                gap-4

                                sm:grid-cols-[120px_minmax(0,1fr)]
                                sb-identity-layout
                            "
                        >

                            {{-- FOTO --}}

                            <div class="sb-identity-photo">

                                <span
                                    class="
                                        mb-1.5
                                        block

                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.14em]

                                        text-[#8c6239]
                                    "
                                >
                                    Foto
                                </span>


                                <label
                                    class="
                                        group
                                        relative

                                        flex
                                        aspect-[4/5]
                                        w-full

                                        cursor-pointer
                                        items-center
                                        justify-center

                                        overflow-hidden

                                        rounded-xl
                                        border
                                        border-dashed
                                        border-[#cdbb9f]

                                        sb-photo-preview
                                        bg-[#efe9dc]/45

                                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]

                                        transition

                                        hover:border-[#a0774d]
                                        hover:bg-[#efe9dc]/70
                                    "
                                >

                                    <template
                                        x-if="
                                            identityImagePreview
                                        "
                                    >
                                        <img
                                            :src="
                                                identityImagePreview
                                            "

                                            x-on:error="identityImageError = 'Não foi possível carregar esta foto. Verifique o arquivo ou o link em /storage.'; identityImagePreview = null"

                                            alt="Prévia da foto do personagem"

                                            class="
                                                h-full
                                                w-full
                                                object-contain
                                                object-center
                                            "
                                        >
                                    </template>


                                    <template
                                        x-if="
                                            !identityImagePreview
                                        "
                                    >
                                        <div
                                            class="
                                                px-3
                                                text-center
                                            "
                                        >
                                            <svg
                                                class="
                                                    mx-auto
                                                    h-8
                                                    w-8

                                                    text-[#8c6239]/45
                                                "

                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M4 5h16v14H4zM8 13l2.5-2.5L14 14l2-2 4 4M8 9h.01"
                                                />
                                            </svg>

                                            <span
                                                class="
                                                    mt-2
                                                    block

                                                    text-[9px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.10em]

                                                    text-[#8c6239]
                                                "
                                            >
                                                Adicionar foto
                                            </span>

                                            <span
                                                class="
                                                    mt-1
                                                    block

                                                    text-[8px]

                                                    text-[#8c6239]/65
                                                "
                                            >
                                                JPG, PNG ou WEBP
                                            </span>
                                        </div>
                                    </template>


                                    <div
                                        class="
                                            pointer-events-none
                                            absolute
                                            inset-x-2
                                            bottom-2

                                            translate-y-2
                                            rounded-lg

                                            bg-[#2b1d17]/72

                                            px-2
                                            py-1.5

                                            text-center
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.09em]

                                            text-white

                                            opacity-0

                                            backdrop-blur-sm

                                            transition

                                            group-hover:translate-y-0
                                            group-hover:opacity-100
                                        "
                                    >
                                        Trocar foto
                                    </div>


                                    <input
                                        type="file"

                                        accept="image/jpeg,image/png,image/webp"

                                        @click="$event.target.value = ''"

                                        @change="
                                            handleIdentityImage(
                                                $event
                                            )
                                        "

                                        class="hidden"
                                    >

                                </label>


                                <button
                                    type="button"
                                    x-show="identityImageFile || identityImageRemove"
                                    x-cloak
                                    @click="savePhotoNow()"
                                    :disabled="photoSaving || saving"
                                    class="sb-class-action mt-2 w-full rounded-lg px-3 py-2 text-[10px] font-black uppercase tracking-[0.08em] disabled:opacity-40"
                                >
                                    <span x-text="photoSaving ? 'Salvando foto...' : (identityImageRemove ? 'Confirmar remoção' : 'Aplicar nova foto')"></span>
                                </button>
                                <p class="character-customization-v18-copy mt-1 text-[9px] leading-relaxed">
                                    A foto também é salva pelo botão Salvar alterações.
                                </p>

                                <button
                                    x-show="
                                        identityImagePreview
                                    "
                                    x-cloak

                                    type="button"

                                    @click="
                                        removeIdentityImage()
                                    "

                                    class="
                                        mt-2
                                        w-full

                                        rounded-lg

                                        px-2
                                        py-1.5

                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.08em]

                                        text-red-700

                                        transition

                                        hover:bg-red-50
                                    "
                                >
                                    Remover foto
                                </button>


                                <p
                                    x-show="
                                        identityImageError
                                    "
                                    x-cloak

                                    class="
                                        mt-2

                                        text-[9px]
                                        font-bold
                                        leading-relaxed

                                        text-red-700
                                    "

                                    x-text="
                                        identityImageError
                                    "
                                ></p>

                            </div>


                            {{-- DADOS DE IDENTIDADE --}}

                            <div
                                class="
                                    grid
                                    min-w-0
                                    grid-cols-1
                                    gap-3

                                    sm:grid-cols-2
                                    sb-identity-fields
                                "
                            >

                                {{-- NOME --}}

                                <label
                                    class="
                                        block
                                        min-w-0

                                        sm:col-span-2
                                    "
                                >
                                    <span
                                        class="
                                            mb-1.5
                                            block

                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Nome
                                    </span>

                                    <input
                                        type="text"

                                        x-model="draft.name"

                                        maxlength="120"

                                        class="
                                            h-11
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#cdbb9f]

                                            bg-[#fffdf8]

                                            px-3

                                            font-serif
                                            text-[15px]
                                            font-bold

                                            text-[#53150f]

                                            outline-none

                                            transition

                                            focus:border-[#8c6239]
                                            focus:ring-2
                                            focus:ring-[#8c6239]/10
                                        "
                                    >
                                </label>


                                {{-- ANTECEDENTE --}}

                                <label class="block min-w-0">

                                    <span
                                        class="
                                            mb-1.5
                                            block

                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Antecedente
                                    </span>

                                    <input
                                        type="text"

                                        x-model="draft.background"

                                        maxlength="120"

                                        placeholder="—"

                                        class="
                                            h-11
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#cdbb9f]

                                            bg-[#fffdf8]

                                            px-3

                                            font-serif
                                            text-[13px]
                                            font-bold

                                            text-[#53150f]

                                            outline-none

                                            transition

                                            focus:border-[#8c6239]
                                            focus:ring-2
                                            focus:ring-[#8c6239]/10
                                        "
                                    >

                                </label>


                                {{-- ESPÉCIE --}}

                                <label class="block min-w-0">

                                    <span
                                        class="
                                            mb-1.5
                                            block

                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Espécie
                                    </span>

                                    <input
                                        type="text"

                                        x-model="draft.species"

                                        maxlength="120"

                                        placeholder="—"

                                        class="
                                            h-11
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#cdbb9f]

                                            bg-[#fffdf8]

                                            px-3

                                            font-serif
                                            text-[13px]
                                            font-bold

                                            text-[#53150f]

                                            outline-none

                                            transition

                                            focus:border-[#8c6239]
                                            focus:ring-2
                                            focus:ring-[#8c6239]/10
                                        "
                                    >

                                </label>

                            </div>

                        </div>


                    </section>


                                        {{-- =================================================
                         PROGRESSÃO
                    ================================================== --}}

                    <section
                        class="sb-compact-progression"
                        x-show="activeTab === 'progression'"
                        x-cloak
                    >
                        <div
                            class="
                                flex items-center justify-between gap-4
                                border-b border-[#b08c62]/28 pb-3
                            "
                        >
                            <div>
                                <h3 class="font-serif text-[19px] font-black text-[#53150f]">
                                    Progressão
                                </h3>
                                <p
                                    x-show="!draft.experience?.enabled"
                                    class="mt-1 text-[11px] text-[#7d604d]"
                                >
                                    Níveis de classe e bônus de proficiência.
                                </p>
                            </div>

                            <div
                                class="
                                    flex items-baseline gap-2 rounded-lg
                                    border border-[#cdbb9f] bg-[#fffdf8]
                                    px-3 py-2
                                "
                            >
                                <span
                                    class="
                                        text-[8px] font-black uppercase
                                        tracking-[0.12em] text-[#8c6239]
                                    "
                                >
                                    Nível
                                </span>
                                <strong
                                    class="font-serif text-[21px] leading-none text-[#6b1d14]"
                                    x-text="draft.level"
                                ></strong>
                            </div>
                        </div>

                        {{-- =================================================
                             PROGRESSÃO COM XP — MODO ESSENCIAL
                             A ficha já possui um modal com XP detalhado.
                             Aqui mostramos apenas o estado da evolução
                             e as ações realmente disponíveis.
                        ================================================== --}}

                        <div
                            x-show="draft.experience?.enabled"
                            x-cloak
                            class="sb-progress-experience mt-2 space-y-1.5"
                        >
                            {{-- Uma única mensagem de estado; sem repetir
                                 total, marcos, porcentagens ou barras. --}}
                            <div
                                class="sb-xp-overview flex items-center gap-3 rounded-xl border px-3.5 py-3"
                                :class="Number(draft.level) < 20 && canLevelUpWithExperience() ? 'sb-xp-ready' : ''"
                            >
                                <span
                                    class="sb-xp-level-box flex h-9 w-9 shrink-0 items-center justify-center rounded-full border"
                                    aria-hidden="true"
                                >
                                    <svg
                                        class="h-[17px] w-[17px]"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M12 3v18M5 10l7-7 7 7" />
                                    </svg>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p
                                        class="sb-xp-heading text-[11px] font-black"
                                        x-text="Number(draft.level) >= 20 ? 'Nível máximo alcançado' : (canLevelUpWithExperience() ? 'Evolução liberada' : 'Progressão por experiência')"
                                    ></p>
                                    <p
                                        x-show="Number(draft.level) < 20"
                                        class="sb-xp-muted mt-0.5 text-[10px] leading-snug"
                                        x-text="canLevelUpWithExperience() ? 'Escolha a classe que receberá o novo nível.' : 'Faltam ' + formatXp(xpUntilLevelUp()) + ' XP para o nível ' + (Number(draft.level) + 1) + '.'"
                                    ></p>
                                </div>
                            </div>

                            {{-- Cada classe tem apenas a correção de nível.
                                 Subir só existe no DOM quando o XP permite. --}}
                            <div class="space-y-2">
                                <template x-for="item in draft.classes" :key="item.id || item.name">
                                    <article
                                        class="sb-xp-class flex min-w-0 items-center justify-between gap-3 rounded-xl border px-3.5 py-2.5"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <div class="flex min-w-0 items-center gap-1.5">
                                                <strong
                                                    class="sb-xp-heading min-w-0 truncate font-serif text-[14px] font-black"
                                                    x-text="item.name || 'Classe'"
                                                ></strong>
                                                <button
                                                    type="button"
                                                    @click="beginClassEdit(item)"
                                                    class="sb-class-edit-inline inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md transition"
                                                    :aria-label="'Editar classe e subclasse de ' + (item.name || 'Classe')"
                                                    title="Editar classe e subclasse"
                                                >
                                                    <svg aria-hidden="true" viewBox="0 0 24 24" class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L10 16l-4 1 1-4Z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p
                                                x-show="item.subclass"
                                                x-cloak
                                                class="sb-xp-muted mt-0.5 truncate text-[9px]"
                                                x-text="item.subclass"
                                            ></p>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-2">
                                            {{-- Com uma classe, o nível já é exibido
                                                 no cabeçalho. No multiclasse,
                                                 mostramos o nível de cada classe. --}}
                                            <span
                                                x-show="draft.classes.length > 1"
                                                x-cloak
                                                class="sb-xp-label text-[10px] font-bold"
                                                x-text="'Nv. ' + (Number(item.level) || 1)"
                                            ></span>

                                            <button
                                                type="button"
                                                @click="requestLevelChange(item, 'down')"
                                                :disabled="Number(item.level) <= 1 || levelingClassId !== null"
                                                class="sb-xp-down flex h-8 w-8 items-center justify-center rounded-lg border text-[15px] font-bold transition disabled:cursor-not-allowed disabled:opacity-30"
                                                :aria-label="'Corrigir nível da classe ' + (item.name || 'Classe') + ': diminuir um nível'"
                                                title="Corrigir nível (diminuir um)"
                                            >−</button>

                                            <template x-if="Number(draft.level) < 20 && Number(item.level) < 20 && canLevelUpWithExperience()">
                                                <button
                                                    type="button"
                                                    @click="requestLevelChange(item, 'up')"
                                                    :disabled="levelingClassId !== null"
                                                    class="sb-xp-up flex h-8 items-center justify-center gap-1.5 rounded-lg border px-3 text-[10px] font-black transition disabled:opacity-40"
                                                >
                                                    <span aria-hidden="true">↑</span>
                                                    Subir nível
                                                </button>
                                            </template>
                                        </div>
                                    </article>
                                </template>

                                <p
                                    x-show="!draft.classes.length"
                                    class="sb-xp-muted rounded-xl border border-dashed px-3 py-4 text-center text-[11px]"
                                >
                                    Nenhuma classe encontrada.
                                </p>
                            </div>
                        </div>

                        {{-- MODO SEM XP: mantém integralmente a lista original. --}}
                        <div x-show="!draft.experience?.enabled" x-cloak class="sb-progress-standard">
                        <div class="mt-2 space-y-1.5">
                            <template x-for="item in draft.classes" :key="item.id || item.name">
                                <article
                                    class="
                                        flex min-w-0 items-center justify-between gap-3
                                        rounded-xl border border-[#cdbb9f]/70
                                        bg-[#fffdf8] px-3 py-2.5
                                    "
                                >
                                    <div class="min-w-0">
                                        <div class="flex min-w-0 flex-wrap items-center gap-1.5">
                                            <strong
                                                class="min-w-0 truncate font-serif text-[14px] text-[#53150f]"
                                                x-text="item.name || 'Classe'"
                                            ></strong>
                                            <button
                                                type="button"
                                                @click="beginClassEdit(item)"
                                                class="sb-class-edit-inline inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md transition"
                                                :aria-label="'Editar classe e subclasse de ' + (item.name || 'Classe')"
                                                title="Editar classe e subclasse"
                                            >
                                                <svg aria-hidden="true" viewBox="0 0 24 24" class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9" />
                                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L10 16l-4 1 1-4Z" />
                                                </svg>
                                            </button>
                                            <span
                                                x-show="item.is_primary"
                                                x-cloak
                                                class="
                                                    rounded-full bg-[#eadbc8] px-2 py-0.5
                                                    text-[7px] font-black uppercase
                                                    tracking-[0.08em] text-[#6b1d14]
                                                "
                                            >
                                                Principal
                                            </span>
                                        </div>
                                        <p
                                            x-show="item.subclass"
                                            x-cloak
                                            class="mt-0.5 truncate text-[9px] text-[#7d604d]"
                                            x-text="item.subclass"
                                        ></p>
                                    </div>

                                    <div
                                        class="
                                            flex shrink-0 items-center overflow-hidden
                                            rounded-lg border border-[#cdbb9f] bg-[#f7f0e6]
                                        "
                                    >
                                        <button
                                            type="button"
                                            @click="requestLevelChange(item, 'down')"
                                            :disabled="Number(item.level) <= 1 || levelingClassId !== null"
                                            class="
                                                flex h-9 w-9 items-center justify-center
                                                border-r border-[#cdbb9f]
                                                text-base font-black text-[#6b1d14]
                                                transition hover:bg-[#eadbc8]
                                                disabled:cursor-not-allowed disabled:opacity-30
                                            "
                                            title="Reduzir um nível"
                                        >−</button>

                                        <div class="min-w-[54px] px-2 text-center">
                                            <span
                                                class="
                                                    block text-[7px] font-black uppercase
                                                    tracking-[0.08em] text-[#8c6239]
                                                "
                                            >Nível</span>
                                            <strong
                                                class="mt-0.5 block font-serif text-[16px] leading-none text-[#53150f]"
                                                x-text="item.level"
                                            ></strong>
                                        </div>

                                        <button
                                            type="button"
                                            @click="requestLevelChange(item, 'up')"
                                            :disabled="draft.level >= 20 || Number(item.level) >= 20 || levelingClassId !== null || !canLevelUpWithExperience()"
                                            class="
                                                flex h-9 w-9 items-center justify-center
                                                border-l border-[#cdbb9f] bg-[#6b1d14]
                                                text-base font-black text-[#fffaf2]
                                                transition hover:bg-[#53150f]
                                                disabled:cursor-not-allowed
                                                disabled:bg-[#eadbc8] disabled:text-[#8c6239]/55
                                            "
                                            :title="draft.experience?.enabled && !canLevelUpWithExperience()
                                                ? 'Faltam ' + formatXp(xpUntilLevelUp()) + ' XP para subir de nível'
                                                : 'Adicionar um nível'"
                                        >+</button>
                                    </div>
                                </article>
                            </template>

                            <div
                                x-show="!draft.classes.length"
                                class="
                                    rounded-xl border border-dashed border-[#cdbb9f]/75
                                    px-4 py-5 text-center text-[11px] text-[#8c6239]
                                "
                            >
                                Nenhuma classe encontrada.
                            </div>
                        </div>
                        </div>

                        {{-- Editor único de classe e subclasse; funciona com ou sem XP. --}}
                        <section x-show="editingClassId !== null" x-cloak
                            class="sb-class-editor mt-3 rounded-xl p-3.5">
                            <p class="text-[11px] font-black">Editar classe e subclasse</p>
                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <label class="block min-w-0">
                                    <span class="block text-[9px] font-bold">Classe</span>
                                    <input type="text" x-model="classEdit.name" maxlength="80" class="sb-class-input mt-1 rounded-lg px-3 py-2 text-[11px]" autocomplete="off">
                                </label>
                                <label class="block min-w-0">
                                    <span class="block text-[9px] font-bold">Subclasse (opcional)</span>
                                    <input type="text" x-model="classEdit.subclass" maxlength="80" class="sb-class-input mt-1 rounded-lg px-3 py-2 text-[11px]" autocomplete="off">
                                </label>
                            </div>
                            <p x-show="classError" x-text="classError" class="mt-2 text-[10px] font-bold text-red-600"></p>
                            <div class="mt-3 flex justify-end gap-2">
                                <button type="button" @click="cancelClassEdit()" :disabled="classSaving" class="sb-class-cancel rounded-lg px-3 py-2 text-[10px] font-bold">Cancelar</button>
                                <button type="button" @click="saveClassEdit()" :disabled="classSaving" class="sb-class-action rounded-lg px-3 py-2 text-[10px] font-black" x-text="classSaving ? 'Salvando...' : 'Salvar classe'"></button>
                            </div>
                        </section>

                        {{-- Uma nova classe consome um nível: não é apenas um cadastro. --}}
                        <div x-show="Number(draft.level) < 20 && (!draft.experience?.enabled || canLevelUpWithExperience())" x-cloak class="mt-3">
                            <button type="button" @click="beginMulticlass()" x-show="!multiclassOpen" x-cloak
                                class="sb-class-edit-trigger w-full rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-[0.07em]">
                                + Adicionar nova classe ao subir de nível
                            </button>
                        </div>
                        <section x-show="multiclassOpen" x-cloak class="sb-class-editor mt-3 rounded-xl p-3.5">
                            <p class="text-[11px] font-black">Nova classe — nível 1</p>
                            <p class="character-customization-v18-copy mt-1 text-[10px]">Ao confirmar, o nível total do personagem aumenta em 1.</p>
                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <label class="block min-w-0">
                                    <span class="block text-[9px] font-bold">Nova classe</span>
                                    <input type="text" x-model="multiclassForm.name" maxlength="80" class="sb-class-input mt-1 rounded-lg px-3 py-2 text-[11px]" placeholder="Ex.: Guerreiro">
                                </label>
                                <label class="block min-w-0">
                                    <span class="block text-[9px] font-bold">Subclasse (opcional)</span>
                                    <input type="text" x-model="multiclassForm.subclass" maxlength="80" class="sb-class-input mt-1 rounded-lg px-3 py-2 text-[11px]" placeholder="Ex.: Mestre de Batalha">
                                </label>
                            </div>
                            <p x-show="multiclassError" x-text="multiclassError" class="mt-2 text-[10px] font-bold text-red-600"></p>
                            <div class="mt-3 flex justify-end gap-2">
                                <button type="button" @click="multiclassOpen = false" :disabled="multiclassSaving" class="sb-class-cancel rounded-lg px-3 py-2 text-[10px] font-bold">Cancelar</button>
                                <button type="button" @click="createMulticlass()" :disabled="multiclassSaving" class="sb-class-action rounded-lg px-3 py-2 text-[10px] font-black" x-text="multiclassSaving ? 'Adicionando...' : 'Confirmar nova classe'"></button>
                            </div>
                        </section>

                        <div
                            x-show="progressionError"
                            x-cloak
                            class="
                                mt-2.5 rounded-lg border border-red-200 bg-red-50
                                px-3 py-2.5 text-[11px] font-bold text-red-700
                            "
                            x-text="progressionError"
                        ></div>

                        <div
                            x-show="progressionMessage"
                            x-cloak
                            class="
                                mt-2.5 rounded-lg border border-emerald-200 bg-emerald-50
                                px-3 py-2.5 text-[11px] font-bold text-emerald-800
                            "
                            x-text="progressionMessage"
                        ></div>

                        <div
                            class="
                                mt-3 overflow-hidden rounded-xl
                                border border-[#cdbb9f]/70 bg-[#f7eee2]/45
                            "
                        >
                            <button
                                type="button"
                                @click="proficiencyDrawerOpen = !proficiencyDrawerOpen"
                                class="
                                    flex w-full items-center justify-between gap-3
                                    px-3.5 py-3 text-left transition hover:bg-[#eadbc8]/38
                                "
                            >
                                <span class="min-w-0">
                                    <span class="flex items-center gap-2">
                                        <span class="font-serif text-[13px] font-black text-[#53150f]">
                                            Proficiência
                                        </span>
                                        <span
                                            class="rounded-full px-2 py-0.5 text-[8px] font-black uppercase tracking-[0.08em]"
                                            :class="
                                                draft.proficiency.custom_enabled
                                                    ? 'bg-[#6b1d14] text-[#fffaf2]'
                                                    : 'bg-[#eadbc8] text-[#8c6239]'
                                            "
                                            x-text="draft.proficiency.custom_enabled ? 'Custom' : 'Automática'"
                                        ></span>
                                    </span>
                                    <span class="mt-0.5 block text-[10px] text-[#7d604d]">
                                        Atual
                                        <strong
                                            class="font-serif text-[11px] text-[#53150f]"
                                            x-text="signed(draft.proficiency.current)"
                                        ></strong>
                                    </span>
                                </span>

                                <svg
                                    class="h-4 w-4 shrink-0 text-[#8c6239] transition-transform"
                                    :class="proficiencyDrawerOpen ? 'rotate-180' : ''"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            <div
                                x-show="proficiencyDrawerOpen"
                                x-cloak
                                x-transition
                                class="border-t border-[#cdbb9f]/55 bg-[#fffdf8]/70 p-3.5"
                            >
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                                    <label class="min-w-0 flex-1">
                                        <span
                                            class="
                                                text-[8px] font-black uppercase
                                                tracking-[0.10em] text-[#8c6239]
                                            "
                                        >Valor custom</span>
                                        <input
                                            type="number"
                                            min="-20"
                                            max="20"
                                            step="1"
                                            x-model.number="customProficiencyValue"
                                            class="
                                                mt-1.5 h-10 w-full rounded-lg
                                                border-[#cdbb9f] bg-[#fffdf8]
                                                text-sm font-bold text-[#53150f]
                                                focus:border-[#8c6239] focus:ring-[#8c6239]/10
                                            "
                                        >
                                    </label>

                                    <button
                                        type="button"
                                        @click="saveCustomProficiency()"
                                        :disabled="proficiencySaving"
                                        class="
                                            h-10 rounded-lg bg-[#6b1d14] px-4
                                            text-[9px] font-black uppercase tracking-[0.09em]
                                            text-[#fffaf2] transition hover:bg-[#53150f]
                                            disabled:opacity-50
                                        "
                                    >Aplicar</button>

                                    <button
                                        type="button"
                                        @click="restoreAutomaticProficiency()"
                                        :disabled="proficiencySaving || !draft.proficiency.custom_enabled"
                                        class="
                                            h-10 rounded-lg border border-[#cdbb9f]
                                            bg-[#f7f0e6] px-3 text-[9px] font-black
                                            uppercase tracking-[0.08em] text-[#6b1d14]
                                            transition hover:bg-[#eadbc8] disabled:opacity-35
                                        "
                                    >Automática</button>
                                </div>

                                <p class="mt-2 text-[9px] text-[#7d604d]">
                                    Valor automático atual:
                                    <strong
                                        class="font-serif text-[#53150f]"
                                        x-text="signed(draft.proficiency.calculated)"
                                    ></strong>
                                </p>

                                <p
                                    x-show="proficiencyError"
                                    x-cloak
                                    class="mt-2 text-[10px] font-bold text-red-700"
                                    x-text="proficiencyError"
                                ></p>

                                <p
                                    x-show="proficiencyMessage"
                                    x-cloak
                                    class="mt-2 text-[10px] font-bold text-emerald-800"
                                    x-text="proficiencyMessage"
                                ></p>
                            </div>
                        </div>
                    </section>


{{-- =================================================
                         TEMAS
                    ================================================== --}}

                    <section
                        x-show="
                            activeTab === 'themes'
                        "
                        x-cloak
                    >
                        <div
                            class="
                                mb-5
                                border-b
                                border-[#b08c62]/28
                                pb-3
                            "
                        >
                            <div
                                class="
                                    flex
                                    flex-wrap
                                    items-end
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
                                            tracking-[0.16em]
                                            text-[#93623d]
                                        "
                                    >
                                        Aparência da ficha
                                    </p>

                                    <h3
                                        class="
                                            mt-0.5
                                            font-serif
                                            text-[19px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Escolha seu tema
                                    </h3>

                                    <p
                                        class="
                                            mt-1
                                            max-w-[620px]
                                            text-[10px]
                                            leading-relaxed
                                            text-[#7d604d]
                                        "
                                    >
                                        A seleção altera apenas a ficha deste personagem.
                                        Clique em uma paleta para visualizar e use Salvar
                                        para manter a escolha.
                                    </p>
                                </div>

                                <div
                                    class="
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/60
                                        bg-[#fffdf8]/72
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
                                            tracking-[0.11em]
                                            text-[#93623d]
                                        "
                                    >
                                        Selecionado
                                    </span>

                                    <strong
                                        class="
                                            mt-0.5
                                            block
                                            font-serif
                                            text-[11px]
                                            text-[#53150f]
                                        "
                                        x-text="
                                            {
                                                spellbound: 'Spellbound',
                                                emerald: 'Esmeralda',
                                                rose: 'Rosa Antigo',
                                                sapphire: 'Safira',
                                                arcane: 'Arcano',
                                                midnight: 'Meia-Noite',
                                                obsidian: 'Obsidiana',
                                                aurora: 'Aurora'
                                            }[
                                                normalizeTheme(
                                                    draft
                                                        ?.settings
                                                        ?.appearance
                                                        ?.theme
                                                )
                                            ]
                                            ?? 'Spellbound'
                                        "
                                    ></strong>
                                </div>
                            </div>
                        </div>

                        <div
                            class="
                                character-theme-picker
                                grid
                                grid-cols-1
                                gap-3
                                sm:grid-cols-2
                            "
                        >
                            @foreach($themeOptions as $themeKey => $theme)
                                <button
                                    type="button"

                                    @click="
                                        applyThemePreview(
                                            @js($themeKey)
                                        )
                                    "

                                    :aria-pressed="
                                        themeSelected(
                                            @js($themeKey)
                                        )
                                    "

                                    :class="
                                        themeSelected(
                                            @js($themeKey)
                                        )
                                            ? 'character-customization-v18-theme-card--selected'
                                            : ''
                                    "

                                    class="
                                        character-customization-v18-theme-card

                                        group
                                        relative
                                        overflow-hidden
                                        rounded-xl
                                        border
                                        border-[#cdbb9f]/72
                                        bg-[#fffdf8]
                                        p-2.5
                                        text-left
                                        shadow-[0_2px_8px_rgba(83,21,15,.045)]
                                        transition
                                        duration-150
                                        hover:-translate-y-0.5
                                        hover:border-[#a0774d]/65
                                        hover:shadow-[0_5px_14px_rgba(83,21,15,.08)]
                                        focus:outline-none
                                        focus-visible:ring-2
                                        focus-visible:ring-[#8c6239]/25
                                    "
                                >
                                    <div
                                        class="character-theme-preview"
                                        data-preview-theme="{{ $themeKey }}"
                                        aria-hidden="true"
                                    >
                                        <span class="character-theme-preview__paper"></span>
                                        <span class="character-theme-preview__panel"></span>
                                        <span class="character-theme-preview__accent"></span>
                                        <span class="character-theme-preview__line"></span>
                                    </div>

                                    <div
                                        class="
                                            mt-2.5
                                            flex
                                            items-start
                                            justify-between
                                            gap-3
                                        "
                                    >
                                        <div class="min-w-0">
                                            <strong
                                                class="
                                                    block
                                                    truncate
                                                    font-serif
                                                    text-[14px]
                                                    font-black
                                                    text-[#53150f]
                                                "
                                            >
                                                {{ $theme['name'] }}
                                            </strong>

                                            <span
                                                class="
                                                    mt-0.5
                                                    block
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.10em]
                                                    text-[#93623d]
                                                "
                                            >
                                                {{ $theme['subtitle'] }}
                                            </span>
                                        </div>

                                        <span
                                            class="
                                                character-customization-v18-theme-check

                                                flex
                                                h-6
                                                w-6
                                                shrink-0
                                                items-center
                                                justify-center
                                                rounded-full
                                                border
                                                transition
                                            "

                                            :class="
                                                themeSelected(
                                                    @js($themeKey)
                                                )
                                                    ? 'character-customization-v18-theme-check--selected'
                                                    : 'character-customization-v18-theme-check--idle'
                                            "
                                        >
                                            <svg
                                                class="h-3.5 w-3.5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2.2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                aria-hidden="true"
                                            >
                                                <path d="m5 12 4 4L19 6" />
                                            </svg>
                                        </span>
                                    </div>

                                    <p
                                        class="
                                            mt-2
                                            text-[9px]
                                            leading-relaxed
                                            text-[#725644]
                                        "
                                    >
                                        {{ $theme['description'] }}
                                    </p>
                                </button>
                            @endforeach
                        </div>

                        <div
                            class="
                                mt-4
                                rounded-xl
                                border
                                border-[#d8c7ab]/65
                                bg-[#f7f0e6]/55
                                px-3
                                py-2.5
                            "
                        >
                            <p
                                class="
                                    text-[9px]
                                    leading-relaxed
                                    text-[#725644]
                                "
                            >
                                <strong class="text-[#53150f]">
                                    Prévia ao vivo:
                                </strong>
                                a paleta selecionada é aplicada na ficha enquanto
                                este modal estiver aberto. Fechar sem salvar restaura
                                o tema anterior.
                            </p>
                        </div>
                    </section>


                    {{-- =================================================
                         REGRAS
                    ================================================== --}}

                    <section
                        x-show="
                            activeTab === 'rules'
                        "
                    >
                        {{-- =================================================
                             REGRAS OPCIONAIS — VERSÃO LIMPA
                        ================================================== --}}

                        <div
                            class="
                                mb-4
                                border-b
                                border-[#b08c62]/28
                                pb-3
                            "
                        >
                            <h3
                                class="
                                    font-serif
                                    text-[19px]
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Regras Opcionais
                            </h3>

                            <p
                                class="
                                    mt-1
                                    text-[11px]
                                    leading-relaxed
                                    text-[#7d604d]
                                "
                            >
                                Personalize o que aparece na ficha e ative regras especiais do personagem.
                            </p>
                        </div>


                        <div class="space-y-2">

                            {{-- DEFESAS --}}
                            <label
                                class="
                                    flex
                                    cursor-pointer
                                    items-center
                                    justify-between
                                    gap-4

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    px-3.5
                                    py-3

                                    transition
                                    hover:bg-[#f7eee2]/55
                                "
                            >
                                <span class="min-w-0">
                                    <span
                                        class="
                                            block
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Exibir Defesas
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#7d604d]
                                        "
                                    >
                                        Mostra ou esconde completamente Resistências, Imunidades e Vulnerabilidades.
                                    </span>
                                </span>

                                <span
                                    class="
                                        relative
                                        h-5
                                        w-9
                                        shrink-0
                                        rounded-full
                                        transition
                                    "

                                    :class="
                                        draft.settings.display.show_defenses
                                            ? 'bg-[#6b1d14]'
                                            : 'bg-[#d8c7ab]'
                                    "
                                >
                                    <input
                                        type="checkbox"

                                        x-model="
                                            draft.settings.display.show_defenses
                                        "

                                        class="sr-only"
                                    >

                                    <span
                                        class="
                                            absolute
                                            top-0.5

                                            h-4
                                            w-4

                                            rounded-full
                                            bg-[#fffdf8]
                                            shadow-sm

                                            transition
                                        "

                                        :class="
                                            draft.settings.display.show_defenses
                                                ? 'translate-x-[18px]'
                                                : 'translate-x-0.5'
                                        "
                                    ></span>
                                </span>
                            </label>


                            {{-- EXAUSTÃO --}}
                            <label
                                class="
                                    flex
                                    cursor-pointer
                                    items-center
                                    justify-between
                                    gap-4

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    px-3.5
                                    py-3

                                    transition
                                    hover:bg-[#f7eee2]/55
                                "
                            >
                                <span class="min-w-0">
                                    <span
                                        class="
                                            block
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Exaustão
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#7d604d]
                                        "
                                    >
                                        Ativa níveis de Exaustão e suas penalidades na ficha.
                                    </span>
                                </span>

                                <span
                                    class="
                                        relative
                                        h-5
                                        w-9
                                        shrink-0
                                        rounded-full
                                        transition
                                    "

                                    :class="
                                        draft.settings.optional_rules.exhaustion
                                            ? 'bg-[#6b1d14]'
                                            : 'bg-[#d8c7ab]'
                                    "
                                >
                                    <input
                                        type="checkbox"

                                        x-model="
                                            draft.settings.optional_rules.exhaustion
                                        "

                                        class="sr-only"
                                    >

                                    <span
                                        class="
                                            absolute
                                            top-0.5

                                            h-4
                                            w-4

                                            rounded-full
                                            bg-[#fffdf8]
                                            shadow-sm

                                            transition
                                        "

                                        :class="
                                            draft.settings.optional_rules.exhaustion
                                                ? 'translate-x-[18px]'
                                                : 'translate-x-0.5'
                                        "
                                    ></span>
                                </span>
                            </label>


                            {{-- TRANSFORMAÇÕES --}}
                            <label
                                class="
                                    flex
                                    cursor-pointer
                                    items-center
                                    justify-between
                                    gap-4

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    px-3.5
                                    py-3

                                    transition
                                    hover:bg-[#f7eee2]/55
                                "
                            >
                                <span class="min-w-0">
                                    <span
                                        class="
                                            block
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Transformações
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#7d604d]
                                        "
                                    >
                                        Permite criar e utilizar formas alternativas para este personagem.
                                    </span>
                                </span>

                                <span
                                    class="
                                        relative
                                        h-5
                                        w-9
                                        shrink-0
                                        rounded-full
                                        transition
                                    "

                                    :class="
                                        draft.settings.optional_rules.transformations
                                            ? 'bg-[#6b1d14]'
                                            : 'bg-[#d8c7ab]'
                                    "
                                >
                                    <input
                                        type="checkbox"

                                        x-model="
                                            draft.settings.optional_rules.transformations
                                        "

                                        class="sr-only"
                                    >

                                    <span
                                        class="
                                            absolute
                                            top-0.5

                                            h-4
                                            w-4

                                            rounded-full
                                            bg-[#fffdf8]
                                            shadow-sm

                                            transition
                                        "

                                        :class="
                                            draft.settings.optional_rules.transformations
                                                ? 'translate-x-[18px]'
                                                : 'translate-x-0.5'
                                        "
                                    ></span>
                                </span>
                            </label>


                            {{-- =================================================
                                 REGRAS ESPECIAIS — ACESSO COMPACTO
                            ================================================== --}}

                            <form
                                x-show="
                                    !specialRulesUnlocked
                                "

                                x-cloak

                                @submit.prevent="
                                    unlockSpecialRules()
                                "

                                class="
                                    flex
                                    items-center
                                    gap-2

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    p-2
                                "
                            >
                                <input
                                    type="password"

                                    x-model="
                                        specialRulesCode
                                    "

                                    autocomplete="off"

                                    placeholder="Código de regra especial"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#fbf8f1]

                                        px-3

                                        text-[11px]
                                        font-semibold
                                        text-[#53150f]

                                        outline-none

                                        transition

                                        focus:border-[#8c6239]
                                        focus:ring-2
                                        focus:ring-[#8c6239]/10
                                    "
                                >

                                <button
                                    type="submit"

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        bg-[#6b1d14]

                                        px-3.5

                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.08em]
                                        text-[#fffaf2]

                                        transition
                                        hover:bg-[#53150f]
                                    "
                                >
                                    Liberar
                                </button>
                            </form>


                            <p
                                x-show="
                                    !specialRulesUnlocked
                                    && specialRulesCodeError
                                "

                                x-cloak

                                class="
                                    px-1
                                    text-[10px]
                                    font-bold
                                    text-red-700
                                "

                                x-text="
                                    specialRulesCodeError
                                "
                            ></p>


                            {{-- MORQUEN --}}
                            <div
                                x-show="
                                    specialRulesUnlocked
                                "

                                x-cloak

                                class="
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <label
                                    class="
                                        flex
                                        min-w-0
                                        flex-1
                                        cursor-pointer
                                        items-center
                                        justify-between
                                        gap-4

                                        rounded-xl
                                        border
                                        border-[#9b6d4c]/45

                                        bg-[#fffdf8]

                                        px-3.5
                                        py-3

                                        transition
                                        hover:bg-[#f7eee2]/55
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Regra Morquen
                                    </span>

                                    <span
                                        class="
                                            relative
                                            h-5
                                            w-9
                                            shrink-0
                                            rounded-full
                                            transition
                                        "

                                        :class="
                                            draft.settings.optional_rules.morquen
                                                ? 'bg-[#6b1d14]'
                                                : 'bg-[#d8c7ab]'
                                        "
                                    >
                                        <input
                                            type="checkbox"

                                            x-model="
                                                draft.settings.optional_rules.morquen
                                            "

                                            class="sr-only"
                                        >

                                        <span
                                            class="
                                                absolute
                                                top-0.5

                                                h-4
                                                w-4

                                                rounded-full
                                                bg-[#fffdf8]
                                                shadow-sm

                                                transition
                                            "

                                            :class="
                                                draft.settings.optional_rules.morquen
                                                    ? 'translate-x-[18px]'
                                                    : 'translate-x-0.5'
                                            "
                                        ></span>
                                    </span>
                                </label>

                                <button
                                    type="button"

                                    @click="
                                        lockSpecialRules()
                                    "

                                    class="
                                        flex
                                        h-10
                                        w-10
                                        shrink-0
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/70

                                        bg-[#f4f1e8]

                                        text-[#8c6239]

                                        transition

                                        hover:bg-[#eadbc8]
                                        hover:text-[#53150f]
                                    "

                                    title="Ocultar regra especial"
                                    aria-label="Ocultar regra especial"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M6 6l12 12M18 6 6 18" />
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </section>


                    {{-- ERRO --}}

                    <div
                        x-show="
                            saveError
                        "

                        class="
                            mt-4

                            rounded-lg
                            border
                            border-red-200

                            bg-red-50

                            px-3
                            py-3

                            text-[11px]
                            font-bold

                            text-red-700
                        "

                        x-text="
                            saveError
                        "
                    ></div>

                </div>


                {{-- ====================================================
                     FOOTER
                ===================================================== --}}

                <footer
                    class="
                        character-customization-v18-footer

                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3

                        border-t

                        px-4
                        py-3

                        sm:px-5
                    "
                >

                    <p
                        class="
                            hidden

                            text-[11px]
                            text-[#7d604d]

                            sm:block
                        "
                    >
                        Identidade, tema, opções da ficha e regras especiais são salvas juntas.
                    </p>


                    <div
                        class="
                            ml-auto

                            flex
                            items-center
                            gap-2
                        "
                    >

                        <button
                            type="button"

                            @click="closeModal()"

                            :disabled="
                                saving
                                || levelingClassId !== null
                                || proficiencySaving
                            "

                            class="
                                character-customization-v18-secondary

                                rounded-lg

                                px-3.5
                                py-2.5

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.11em]

                                text-[#8c6239]

                                transition

                                hover:bg-[#fffdf8]/55
                                hover:text-[#53150f]

                                disabled:opacity-50
                            "
                        >
                            Fechar
                        </button>


                        <button
                            type="button"

                            @click="save()"

                            :disabled="
                                saving
                                || !String(
                                    draft.name
                                    || ''
                                ).trim()
                            "

                            class="
                                character-customization-v18-primary

                                min-w-[118px]

                                rounded-lg

                                px-4
                                py-2.5

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.11em]

                                text-[#fffaf2]

                                shadow-[0_1px_2px_rgba(83,21,15,.14)]

                                transition

                                hover:bg-[#53150f]

                                disabled:cursor-wait
                                disabled:opacity-50
                            "
                        >
                            <span
                                x-show="
                                    !saving
                                "
                            >
                                Salvar alterações
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

                </footer>

            </div>


            {{-- ========================================================
                 CONFIRMAÇÃO DE ALTERAÇÃO DE NÍVEL
            ========================================================= --}}

            <div
                x-show="
                    levelConfirmOpen
                "
                x-cloak

                class="
                    character-customization-v18-confirm-layer

                    absolute
                    inset-0
                    z-30

                    flex
                    items-center
                    justify-center

                    p-4
                "
            >

                <div
                    class="
                        character-customization-v18-confirm-backdrop

                        absolute
                        inset-0
                    "

                    @click="
                        cancelLevelConfirmation()
                    "
                ></div>


                <div
                    @click.stop

                    x-transition:enter="
                        transition
                        ease-out
                        duration-150
                    "

                    x-transition:enter-start="
                        opacity-0
                        scale-[.98]
                    "

                    x-transition:enter-end="
                        opacity-100
                        scale-100
                    "

                    class="
                        character-customization-v18-confirm

                        relative
                        z-10

                        w-full
                        max-w-[430px]

                        overflow-hidden

                        rounded-2xl
                        border
                        border-[#b08c62]/75

                        bg-[#fbf8f1]

                        shadow-[0_24px_70px_rgba(42,23,18,.32)]
                    "
                >

                    {{-- CABEÇALHO --}}

                    <div
                        class="
                            character-customization-v18-confirm-header

                            border-b

                            px-4
                            py-3.5
                        "
                    >
                        <p
                            class="
                                text-[10px]
                                font-black
                                uppercase
                                tracking-[0.15em]

                                text-[#8c6239]
                            "
                        >
                            Alteração de progressão
                        </p>

                        <h4
                            class="
                                mt-1

                                font-serif
                                text-[20px]
                                font-black
                                leading-none

                                text-[#53150f]
                            "

                            x-text="
                                levelConfirmMode === 'up'
                                    ? 'Confirmar subida de nível'
                                    : 'Confirmar redução de nível'
                            "
                        ></h4>
                    </div>


                    {{-- CONTEÚDO --}}

                    <div class="p-4">

                        <p
                            class="
                                text-[12px]
                                leading-relaxed

                                text-[#5f4031]
                            "
                        >
                            Você está prestes a

                            <strong
                                class="
                                    font-serif
                                    text-[#53150f]
                                "

                                x-text="
                                    levelConfirmMode === 'up'
                                        ? 'subir'
                                        : 'voltar'
                                "
                            ></strong>

                            um nível em

                            <strong
                                class="
                                    font-serif
                                    text-[#53150f]
                                "

                                x-text="
                                    levelConfirmClass?.name
                                    || 'Classe'
                                "
                            ></strong>.
                        </p>


                        {{-- RESUMO --}}

                        <div
                            class="
                                character-customization-v18-confirm-summary

                                mt-3

                                grid
                                grid-cols-[1fr_auto_1fr]

                                items-center

                                rounded-xl
                                border
                                border-[#d8c7ab]/75

                                bg-[#fffdf8]

                                px-3
                                py-3
                            "
                        >

                            <div class="text-center">
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.12em]

                                        text-[#8c6239]
                                    "
                                >
                                    Atual
                                </p>

                                <p
                                    class="
                                        mt-1

                                        font-serif
                                        text-[22px]
                                        font-black
                                        leading-none

                                        text-[#53150f]
                                    "

                                    x-text="
                                        levelConfirmClass?.level
                                        ?? '—'
                                    "
                                ></p>
                            </div>


                            <span
                                class="
                                    px-3

                                    font-serif
                                    text-[20px]
                                    font-black

                                    text-[#b08c62]
                                "
                                aria-hidden="true"
                            >
                                →
                            </span>


                            <div class="text-center">
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.12em]

                                        text-[#8c6239]
                                    "
                                >
                                    Novo
                                </p>

                                <p
                                    class="
                                        mt-1

                                        font-serif
                                        text-[22px]
                                        font-black
                                        leading-none

                                        text-[#6b1d14]
                                    "

                                    x-text="
                                        levelConfirmClass
                                            ? (
                                                parseInt(
                                                    levelConfirmClass.level
                                                )
                                                + (
                                                    levelConfirmMode === 'up'
                                                        ? 1
                                                        : -1
                                                )
                                            )
                                            : '—'
                                    "
                                ></p>
                            </div>

                        </div>


                        {{-- AVISO --}}

                        <div
                            class="
                                mt-3

                                rounded-xl
                                border
                                border-amber-300/75

                                bg-amber-50

                                px-3
                                py-3
                            "
                        >
                            <p
                                class="
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]

                                    text-amber-900
                                "
                            >
                                Atenção
                            </p>

                            <p
                                class="
                                    mt-1

                                    text-[11px]
                                    leading-relaxed

                                    text-amber-900/85
                                "
                            >
                                Esta ação altera imediatamente o nível da classe e o nível total da ficha.

                                <span
                                    x-show="
                                        levelConfirmMode === 'up'
                                    "
                                >
                                    HP, características, recursos e escolhas concedidas pelo novo nível não serão adicionados automaticamente.
                                </span>

                                <span
                                    x-show="
                                        levelConfirmMode === 'down'
                                    "
                                >
                                    HP, características, recursos e escolhas obtidas nesse nível não serão removidos automaticamente.
                                </span>

                                Se a proficiência estiver em modo automático, ela será recalculada pelo novo nível total.
                            </p>
                        </div>

                    </div>


                    {{-- AÇÕES --}}

                    <div
                        class="
                            character-customization-v18-confirm-footer

                            flex
                            items-center
                            justify-end
                            gap-2

                            border-t
                            border-[#a0774d]/25

                            bg-[#f4f1e8]/75

                            px-4
                            py-3
                        "
                    >

                        <button
                            type="button"

                            @click="
                                cancelLevelConfirmation()
                            "

                            class="
                                rounded-lg

                                px-3.5
                                py-2.5

                                text-[10px]
                                font-black
                                uppercase
                                tracking-[0.10em]

                                text-[#8c6239]

                                transition

                                hover:bg-[#eadbc8]
                                hover:text-[#53150f]
                            "
                        >
                            Cancelar
                        </button>


                        <button
                            type="button"

                            @click="
                                confirmLevelChange()
                            "

                            class="
                                min-w-[126px]

                                rounded-lg
                                border

                                px-4
                                py-2.5

                                text-[10px]
                                font-black
                                uppercase
                                tracking-[0.10em]

                                transition
                            "

                            :class="
                                levelConfirmMode === 'up'
                                    ? 'border-[#6b1d14]/30 bg-[#6b1d14] text-[#fffaf2] hover:bg-[#53150f]'
                                    : 'border-[#9a4f42]/35 bg-[#7b2a21] text-white hover:bg-[#651f18]'
                            "

                            x-text="
                                levelConfirmMode === 'up'
                                    ? 'Sim, subir nível'
                                    : 'Sim, voltar nível'
                            "
                        ></button>

                    </div>

                </div>

            </div>

        </div>

    </template>

</div>