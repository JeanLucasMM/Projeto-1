@php

    /*

    |--------------------------------------------------------------------------

    | TRAÇOS DE ESPÉCIE + TALENTOS

    |--------------------------------------------------------------------------

    |

    | Reutiliza character_features:

    |

    | species_trait = Traço de Espécie

    | feat          = Talento

    |

    | Não há rastreador nesta interface.

    | A leitura rápida mostra nome, nível, fonte e resumo.

    | O conteúdo completo fica no modal.

    |

    */

    $traitsFeatsPayload = $character->features

        ->filter(function ($feature) {

            return in_array(

                $feature->type,

                [

                    'species_trait',

                    'feat',

                ],

                true

            );

        })

        ->map(function ($feature) {

            return [

                'id' => $feature->id,

                'name' => $feature->name,

                'type' => $feature->type,

                'source' => $feature->source,

                'level_acquired' => $feature->level_acquired,

                'description' => $feature->description,

                'data' => is_array($feature->data)

                    ? $feature->data

                    : [],

            ];

        })

        ->values();

    $speciesName = trim(

        (string) (

            $character->species

            ?? ''

        )

    );

@endphp

@once
    @push('styles')
        <style>
            /*
            |--------------------------------------------------------------------------
            | TRAÇOS DE ESPÉCIE + TALENTOS — V6
            |--------------------------------------------------------------------------
            |
            | Estrutura visual alinhada aos módulos de Ataques e Habilidades:
            | título de módulo, tabela única, duas colunas e faixas temáticas.
            | Nenhuma cor de tema é fixa; tudo deriva dos tokens --theme-*.
            |
            */

            .character-traits-feats-v6 {
                width: 100%;
                min-width: 0;
                color: var(--theme-text, #432c21);
            }

            .character-traits-feats-v6-head {
                display: flex;
                min-height: 34px;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 7px;
                padding: 0 10px 7px;
                border-bottom: 1px solid var(--theme-divider-strong, var(--theme-border-strong));
            }

            .character-traits-feats-v6-title {
                margin: 0;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 18px;
                font-weight: 900;
                line-height: 1.1;
                color: var(--theme-title, #53150f);
            }

            .character-traits-feats-v6-hint {
                font-size: 8px;
                font-weight: 800;
                letter-spacing: .06em;
                color: var(--theme-muted, #674936);
            }

            .character-traits-feats-v6-table {
                overflow: hidden;
                border: 1px solid var(--theme-border, rgba(176,140,98,.34));
                border-radius: 9px;
                background: var(--theme-card-background, var(--theme-paper-strong));
                box-shadow: var(--theme-module-shadow, 0 3px 10px rgba(83,21,15,.04));
            }

            .character-traits-feats-v6-columns {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: stretch;
            }

            .character-traits-feats-v6-column {
                min-width: 0;
                background: transparent;
            }

            .character-traits-feats-v6-column + .character-traits-feats-v6-column {
                border-left: 1px solid var(--theme-border-strong, rgba(160,119,77,.36));
            }

            .character-traits-feats-v6-column-head {
                display: flex;
                width: 100%;
                min-height: 30px;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                border: 0;
                border-bottom: 1px solid var(--theme-border-strong, rgba(160,119,77,.36));
                background: var(--theme-band-background, var(--theme-band));
                padding: 0 10px;
                text-align: left;
                color: var(--theme-title, #53150f);
                cursor: pointer;
                transition: background .14s ease, color .14s ease;
            }

            .character-traits-feats-v6-column-head:hover,
            .character-traits-feats-v6-column-head:focus-visible {
                background: color-mix(
                    in srgb,
                    var(--theme-band) 76%,
                    var(--theme-accent) 24%
                );
                outline: none;
            }

            .character-traits-feats-v6-column-label {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .14em;
                text-transform: uppercase;
            }

            .character-traits-feats-v6-add {
                display: inline-flex;
                width: 20px;
                height: 20px;
                flex: 0 0 20px;
                align-items: center;
                justify-content: center;
                border: 1px solid color-mix(in srgb, var(--theme-accent) 28%, transparent);
                border-radius: 6px;
                background: color-mix(in srgb, var(--theme-paper-strong) 44%, transparent);
                font-family: ui-sans-serif, system-ui, sans-serif;
                font-size: 14px;
                font-weight: 700;
                line-height: 1;
                color: var(--theme-accent-strong, #6b1d14);
                transition: background .14s ease, transform .14s ease;
            }

            .character-traits-feats-v6-column-head:hover .character-traits-feats-v6-add,
            .character-traits-feats-v6-column-head:focus-visible .character-traits-feats-v6-add {
                background: color-mix(in srgb, var(--theme-paper-strong) 70%, transparent);
                transform: translateY(-1px);
            }

            .character-traits-feats-v6-list {
                min-width: 0;
                background: transparent;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-row {
                display: block;
                width: 100%;
                min-width: 0;
                border: 0;
                border-bottom: 1px solid var(--theme-border-soft, rgba(169,128,83,.28));
                border-radius: 0 !important;
                background: var(--theme-card-background, var(--theme-paper-strong)) !important;
                padding: 9px 10px 10px !important;
                text-align: left;
                color: var(--theme-text, #432c21);
                cursor: pointer;
                box-shadow: none !important;
                transition: background .14s ease, box-shadow .14s ease;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-row:nth-child(even) {
                background: var(--theme-card-alt-background, var(--theme-paper-alt)) !important;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-row:last-child {
                border-bottom: 0;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-row:hover,
            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-row:focus-visible {
                background: var(--theme-card-hover, var(--theme-hover)) !important;
                box-shadow: inset 3px 0 0 var(--theme-accent) !important;
                outline: none;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-row-top {
                display: flex;
                min-width: 0;
                align-items: flex-start;
                justify-content: space-between;
                gap: 8px;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-name {
                min-width: 0;
                flex: 1;
                overflow: hidden;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 14.5px;
                font-weight: 900;
                line-height: 1.2;
                color: var(--theme-text-strong, var(--theme-title)) !important;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-level {
                display: inline-flex;
                min-height: 22px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--theme-border, rgba(176,140,98,.34)) !important;
                border-radius: 999px !important;
                background: var(--theme-paper-strong, #fffdf8) !important;
                padding: 0 7px;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .035em;
                color: var(--theme-label, #704326) !important;
                box-shadow: inset 0 1px 0 color-mix(in srgb, var(--theme-paper-strong) 74%, transparent) !important;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 3px 7px;
                margin-top: 3px;
                font-size: 9px;
                font-weight: 700;
                line-height: 1.25;
                color: var(--theme-text-secondary, var(--theme-muted)) !important;
            }

            .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-quick {
                display: -webkit-box;
                margin-top: 6px;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                border-left: 2px solid var(--theme-accent) !important;
                border-radius: 0 !important;
                background: var(--theme-inset-background, var(--theme-band-soft)) !important;
                padding: 5px 8px;
                font-size: 10.5px;
                line-height: 1.38;
                color: var(--theme-text-primary, var(--theme-text)) !important;
                box-shadow: none !important;
            }

            .character-traits-feats-v6-empty {
                display: flex;
                min-height: 74px;
                align-items: center;
                justify-content: center;
                padding: 14px 12px;
                text-align: center;
                font-size: 10px;
                line-height: 1.4;
                color: var(--theme-muted, #674936);
                background: var(--theme-card-background, var(--theme-paper-strong));
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL DE LEITURA / EDITOR
            |--------------------------------------------------------------------------
            */

            .character-simple-feature-overlay {
                isolation: isolate;
            }

            .character-simple-feature-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, .68);
                -webkit-backdrop-filter: blur(8px) saturate(.78);
                backdrop-filter: blur(8px) saturate(.78);
            }

            .character-simple-feature-modal {
                position: relative;
                z-index: 1;
                display: flex;
                width: min(680px, calc(100vw - 28px));
                max-height: min(90vh, 780px);
                flex-direction: column;
                overflow: hidden;
                border: 1px solid var(--theme-border-strong, #b99b78);
                border-radius: 14px;
                background: var(--theme-paper, #fbf8f1);
                color: var(--theme-text-primary, #432c21);
                box-shadow:
                    0 28px 80px rgba(0,0,0,.34),
                    0 6px 18px color-mix(in srgb, var(--theme-accent-deep, #53150f) 14%, transparent),
                    inset 0 1px 0 rgba(255,255,255,.62);
            }

            .character-simple-feature-modal--editor {
                width: min(700px, calc(100vw - 28px));
            }

            .character-simple-feature-modal-header {
                position: relative;
                display: flex;
                min-height: 68px;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid var(--theme-border-strong, #b99b78);
                background: var(--theme-band-background, #eadbc8);
                padding: 13px 16px 12px 18px;
            }

            .character-simple-feature-modal-header::after {
                content: '';
                position: absolute;
                right: 18px;
                bottom: 0;
                left: 18px;
                height: 1px;
                background: linear-gradient(
                    90deg,
                    transparent,
                    color-mix(in srgb, var(--theme-accent, #93623d) 32%, transparent),
                    transparent
                );
                pointer-events: none;
            }

            .character-simple-feature-kicker {
                display: block;
                margin-bottom: 4px;
                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .15em;
                text-transform: uppercase;
                color: var(--theme-label, #704326);
            }

            .character-simple-feature-modal-title {
                margin: 0;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 22px;
                font-weight: 900;
                line-height: 1.05;
                color: var(--theme-title, #53150f);
            }

            .character-simple-feature-close {
                display: flex;
                width: 30px;
                height: 30px;
                flex: 0 0 30px;
                align-items: center;
                justify-content: center;
                border: 1px solid transparent;
                border-radius: 8px;
                background: transparent;
                font-size: 18px;
                line-height: 1;
                color: var(--theme-label, #704326);
                cursor: pointer;
                transition:
                    border-color .14s ease,
                    background .14s ease,
                    color .14s ease;
            }

            .character-simple-feature-close:hover,
            .character-simple-feature-close:focus-visible {
                border-color: var(--theme-border, #cdbb9f);
                background: color-mix(in srgb, var(--theme-paper-strong, #fffdf8) 78%, transparent);
                color: var(--theme-title, #53150f);
                outline: none;
            }

            .character-simple-feature-body {
                min-height: 0;
                overflow-y: auto;
                background: var(--theme-paper, #fbf8f1);
                padding: 18px;
                scrollbar-width: thin;
                scrollbar-color: var(--theme-border-strong, #b99b78) transparent;
            }

            .character-simple-feature-modal--editor
            .character-simple-feature-body {
                background:
                    radial-gradient(
                        circle at 88% 0%,
                        color-mix(in srgb, var(--theme-accent, #93623d) 4%, transparent),
                        transparent 34%
                    ),
                    var(--theme-paper, #fbf8f1);
            }

            .character-simple-feature-pills {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 14px;
            }

            .character-simple-feature-pill {
                display: inline-flex;
                min-height: 25px;
                align-items: center;
                border: 1px solid var(--theme-border, #cdbb9f);
                border-radius: 999px;
                background: var(--theme-accent-softer, #f8f0e7);
                padding: 0 8px;
                font-size: 9.5px;
                font-weight: 800;
                color: var(--theme-label, #704326);
            }

            .character-simple-feature-detail-quick {
                margin-bottom: 14px;
                border-left: 3px solid var(--theme-accent, #93623d);
                background: var(--theme-inset-background, #eadbc8);
                padding: 9px 11px;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.45;
                color: var(--theme-text-strong, #53150f);
            }

            .character-simple-feature-description {
                white-space: pre-line;
                font-size: 13.5px;
                line-height: 1.66;
                color: var(--theme-text-primary, #432c21);
            }

            .character-simple-feature-description-empty {
                color: var(--theme-muted, #674936);
            }

            .character-simple-feature-modal-footer {
                display: flex;
                min-height: 56px;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                border-top: 1px solid var(--theme-border-strong, #b99b78);
                background: var(--theme-paper-alt, #f7f0e6);
                padding: 10px 16px;
            }

            .character-simple-feature-editor-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 12px;
            }

            @media (min-width: 620px) {
                .character-simple-feature-editor-grid.two {
                    grid-template-columns: minmax(0, 1fr) 132px;
                }
            }

            .character-simple-feature-field {
                display: block;
                min-width: 0;
            }

            .character-simple-feature-label {
                display: flex;
                min-height: 16px;
                align-items: center;
                margin-bottom: 5px;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .075em;
                text-transform: uppercase;
                color: var(--theme-label, #704326);
            }

            .character-simple-feature-input,
            .character-simple-feature-textarea {
                display: block;
                width: 100%;
                border: 1px solid var(--theme-input-border, #cdbb9f) !important;
                border-radius: 8px;
                background: var(--theme-input-bg, #fffdf8) !important;
                color: var(--theme-input-text, #2f211b) !important;
                outline: none;
                box-shadow:
                    inset 0 1px 0 color-mix(in srgb, var(--theme-paper-strong, #fffdf8) 78%, transparent),
                    0 1px 2px color-mix(in srgb, var(--theme-accent-deep, #53150f) 4%, transparent);
                transition:
                    border-color .14s ease,
                    box-shadow .14s ease,
                    background .14s ease;
            }

            .character-simple-feature-input:hover,
            .character-simple-feature-textarea:hover {
                border-color: var(--theme-border-strong, #b99b78) !important;
            }

            .character-simple-feature-input::placeholder,
            .character-simple-feature-textarea::placeholder {
                color: var(--theme-input-placeholder, #8c7464) !important;
                opacity: .88;
            }

            .character-simple-feature-input {
                min-height: 38px;
                padding: 0 10px;
                font-size: 12px;
                line-height: 38px;
            }

            .character-simple-feature-textarea {
                min-height: 82px;
                resize: vertical;
                padding: 9px 10px;
                font-size: 12px;
                line-height: 1.5;
            }

            .character-simple-feature-textarea.large {
                min-height: 122px;
            }

            .character-simple-feature-input:focus,
            .character-simple-feature-textarea:focus {
                border-color: var(--theme-input-focus, var(--theme-accent, #93623d)) !important;
                background: var(--theme-paper-strong, #fffdf8) !important;
                box-shadow:
                    0 0 0 2px color-mix(in srgb, var(--theme-input-focus, #93623d) 13%, transparent),
                    inset 0 1px 0 color-mix(in srgb, var(--theme-paper-strong, #fffdf8) 86%, transparent);
            }

            .character-simple-feature-field + .character-simple-feature-field,
            .character-simple-feature-editor-grid + .character-simple-feature-field,
            .character-simple-feature-field + .character-simple-feature-editor-grid {
                margin-top: 13px;
            }

            .character-simple-feature-secondary,
            .character-simple-feature-danger,
            .character-simple-feature-primary {
                display: inline-flex;
                min-height: 34px;
                align-items: center;
                justify-content: center;
                border-radius: 7px;
                padding: 0 12px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .045em;
                text-transform: uppercase;
                cursor: pointer;
                transition:
                    transform .12s ease,
                    background .14s ease,
                    color .14s ease,
                    border-color .14s ease,
                    opacity .14s ease;
            }

            .character-simple-feature-secondary {
                border: 1px solid var(--theme-border-strong, #b99b78);
                background: var(--theme-paper-strong, #fffdf8);
                color: var(--theme-label, #704326);
            }

            .character-simple-feature-secondary:hover:not(:disabled) {
                background: var(--theme-button-soft-bg, var(--theme-band, #eadbc8));
                color: var(--theme-button-soft-text, var(--theme-title, #53150f));
            }

            .character-simple-feature-danger {
                margin-right: auto;
                border: 1px solid rgba(153,27,27,.18);
                background: color-mix(in srgb, #fff1f2 70%, var(--theme-paper, #fbf8f1) 30%);
                color: #991b1b;
            }

            .character-simple-feature-danger:hover:not(:disabled) {
                border-color: rgba(153,27,27,.30);
                background: color-mix(in srgb, #fee2e2 74%, var(--theme-paper, #fbf8f1) 26%);
            }

            .character-simple-feature-primary {
                border: 1px solid color-mix(in srgb, var(--theme-button-background, #6b1d14) 78%, black 22%);
                background: var(--theme-button-background, #6b1d14);
                color: var(--theme-button-text, #fffaf2);
                box-shadow: 0 2px 5px color-mix(in srgb, var(--theme-accent-deep, #53150f) 16%, transparent);
            }

            .character-simple-feature-primary:hover:not(:disabled) {
                background: var(--theme-button-hover-background, #53150f);
                transform: translateY(-1px);
            }

            .character-simple-feature-primary:disabled,
            .character-simple-feature-secondary:disabled,
            .character-simple-feature-danger:disabled {
                cursor: wait;
                opacity: .42;
                transform: none;
            }

            .character-simple-feature-error {
                margin-top: 13px;
                border: 1px solid rgba(153,27,27,.18);
                border-left: 3px solid #991b1b;
                border-radius: 7px;
                background: color-mix(in srgb, #fff1f2 72%, var(--theme-paper, #fbf8f1) 28%);
                padding: 8px 10px;
                font-size: 11px;
                line-height: 1.4;
                color: #991b1b;
            }

            @media (max-width: 619px) {
                .character-simple-feature-modal,
                .character-simple-feature-modal--editor {
                    width: min(100%, calc(100vw - 20px));
                    max-height: calc(100vh - 20px);
                    border-radius: 12px;
                }

                .character-simple-feature-modal-header {
                    min-height: 62px;
                    padding: 11px 12px 10px 14px;
                }

                .character-simple-feature-body {
                    padding: 14px;
                }

                .character-simple-feature-modal-footer {
                    padding: 9px 12px;
                }
            }

            @media (max-width: 759px) {
                .character-traits-feats-v6-head {
                    padding-inline: 6px;
                }

                .character-traits-feats-v6-hint {
                    display: none;
                }

                .character-traits-feats-v6-columns {
                    grid-template-columns: minmax(0, 1fr);
                }

                .character-traits-feats-v6-column + .character-traits-feats-v6-column {
                    border-top: 1px solid var(--theme-border-strong);
                    border-left: 0;
                }

                .character-sheet-theme[data-theme] .character-traits-feats-v6 .character-mini-feature-name {
                    font-size: 14px;
                }
            }
        </style>
    @endpush
@endonce

<section

    x-data="{

        entries: @js($traitsFeatsPayload),

        speciesName: @js($speciesName),

        detailOpen: false,

        detailEntry: null,

        editorOpen: false,

        editingId: null,

        form: null,

        saving: false,

        saveError: null,

        urls: {

            store: @js(route('characters.features.store', $character)),

            update: @js(route(

                'characters.features.update',

                [

                    'character' => $character,

                    'feature' => '__FEATURE__',

                ]

            )),

            destroy: @js(route(

                'characters.features.destroy',

                [

                    'character' => $character,

                    'feature' => '__FEATURE__',

                ]

            )),

        },

        get speciesTraits() {

            return this.sortedEntries.filter(

                entry =>

                    entry.type === 'species_trait'

            );

        },

        get feats() {

            return this.sortedEntries.filter(

                entry =>

                    entry.type === 'feat'

            );

        },

        get sortedEntries() {

            return [...this.entries]

                .sort((a, b) => {

                    const levelA =

                        parseInt(

                            a.level_acquired

                        ) || 999;

                    const levelB =

                        parseInt(

                            b.level_acquired

                        ) || 999;

                    if (levelA !== levelB) {

                        return levelA - levelB;

                    }

                    return String(

                        a.name ?? ''

                    ).localeCompare(

                        String(

                            b.name ?? ''

                        ),

                        'pt-BR'

                    );

                });

        },

        typeLabel(type) {

            return type === 'species_trait'

                ? 'Traço de Espécie'

                : 'Talento';

        },

        normalizeEntry(raw) {

            const data =

                raw?.data

                && typeof raw.data === 'object'

                && !Array.isArray(raw.data)

                    ? { ...raw.data }

                    : {};

            return {

                id:

                    raw?.id ?? null,

                name:

                    raw?.name ?? '',

                type:

                    raw?.type === 'species_trait'

                        ? 'species_trait'

                        : 'feat',

                source:

                    raw?.source ?? '',

                level_acquired:

                    raw?.level_acquired === null

                    || raw?.level_acquired === undefined

                        ? ''

                        : parseInt(

                            raw.level_acquired

                        ),

                description:

                    raw?.description ?? '',

                data: {

                    ...data,

                    quick_text:

                        data.quick_text

                        ?? '',

                },

            };

        },

        blankForm(type) {

            return {

                id: null,

                name: '',

                type:

                    type === 'species_trait'

                        ? 'species_trait'

                        : 'feat',

                source:

                    type === 'species_trait'

                        ? this.speciesName

                        : '',

                level_acquired: '',

                description: '',

                data: {

                    quick_text: '',

                },

            };

        },

        openCreate(type) {

            this.editingId = null;

            this.form =

                this.blankForm(

                    type

                );

            this.saveError = null;

            this.editorOpen = true;

        },

        openDetail(raw) {

            this.detailEntry =

                this.normalizeEntry(

                    raw

                );

            this.detailOpen = true;

        },

        closeDetail() {

            this.detailOpen = false;

            this.detailEntry = null;

        },

        openEdit(raw) {

            const entry =

                this.normalizeEntry(

                    raw

                );

            this.detailOpen = false;

            this.detailEntry = null;

            this.editingId =

                entry.id;

            this.form = {

                ...entry,

                data: {

                    ...entry.data,

                },

            };

            this.saveError = null;

            this.editorOpen = true;

        },

        closeEditor() {

            if (this.saving) {

                return;

            }

            this.editorOpen = false;

            this.editingId = null;

            this.form = null;

            this.saveError = null;

        },

        csrf() {

            return document

                .querySelector(

                    'meta[name=csrf-token]'

                )

                ?.getAttribute(

                    'content'

                )

                ?? @js(csrf_token());

        },

        headers() {

            return {

                'Content-Type':

                    'application/json',

                'Accept':

                    'application/json',

                'X-CSRF-TOKEN':

                    this.csrf(),

                'X-Requested-With':

                    'XMLHttpRequest',

            };

        },

        payload() {

            return {

                name:

                    String(

                        this.form?.name

                        ?? ''

                    ).trim(),

                type:

                    this.form?.type

                    === 'species_trait'

                        ? 'species_trait'

                        : 'feat',

                source:

                    String(

                        this.form?.source

                        ?? ''

                    ).trim()

                    || null,

                level_acquired:

                    this.form?.level_acquired

                    === ''

                    || this.form?.level_acquired

                    === null

                        ? null

                        : Math.max(

                            1,

                            parseInt(

                                this.form

                                    .level_acquired

                            ) || 1

                        ),

                description:

                    String(

                        this.form?.description

                        ?? ''

                    ).trim()

                    || null,

                uses_max: null,

                uses_current: null,

                recovery: null,

                data: {

                    activation:

                        'passive',

                    quick_text:

                        String(

                            this.form?.data

                                ?.quick_text

                            ?? ''

                        ).trim()

                        || null,

                    counter_mode:

                        'spend',

                    recovery_custom:

                        null,

                    column:

                        'left',

                },

            };

        },

        upsert(raw) {

            const entry =

                this.normalizeEntry(

                    raw

                );

            const index =

                this.entries.findIndex(

                    current =>

                        parseInt(

                            current.id

                        )

                        === parseInt(

                            entry.id

                        )

                );

            if (index >= 0) {

                this.entries[index] =

                    entry;

            } else {

                this.entries.push(

                    entry

                );

            }

            return entry;

        },

        async saveEntry() {

            if (

                this.saving

                || !this.form

            ) {

                return;

            }

            const payload =

                this.payload();

            if (!payload.name) {

                this.saveError =

                    'Informe um nome.';

                return;

            }

            this.saving = true;

            this.saveError = null;

            const editing =

                this.editingId

                !== null;

            const url =

                editing

                    ? this.urls.update

                        .replace(

                            '__FEATURE__',

                            this.editingId

                        )

                    : this.urls.store;

            try {

                const response =

                    await fetch(

                        url,

                        {

                            method:

                                editing

                                    ? 'PATCH'

                                    : 'POST',

                            headers:

                                this.headers(),

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

                    throw new Error(

                        data?.message

                        ?? 'Não foi possível salvar.'

                    );

                }

                this.upsert(

                    data.feature

                );

                this.saving = false;

                this.closeEditor();

            } catch (error) {

                this.saveError =

                    error?.message

                    ?? 'Não foi possível salvar.';

                this.saving = false;

            }

        },

        async deleteEntry() {

            if (

                this.saving

                || this.editingId

                === null

            ) {

                return;

            }

            const confirmed =

                window.confirm(

                    'Excluir este registro?'

                );

            if (!confirmed) {

                return;

            }

            this.saving = true;

            this.saveError = null;

            try {

                const response =

                    await fetch(

                        this.urls.destroy

                            .replace(

                                '__FEATURE__',

                                this.editingId

                            ),

                        {

                            method:

                                'DELETE',

                            headers:

                                this.headers(),

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

                        ?? 'Não foi possível excluir.'

                    );

                }

                this.entries =

                    this.entries.filter(

                        entry =>

                            parseInt(

                                entry.id

                            )

                            !== parseInt(

                                this.editingId

                            )

                    );

                this.saving = false;

                this.closeEditor();

            } catch (error) {

                this.saveError =

                    error?.message

                    ?? 'Não foi possível excluir.';

                this.saving = false;

            }

        },

    }"

    @keydown.escape.window="

        if (editorOpen) {

            closeEditor()

        } else if (detailOpen) {

            closeDetail()

        }

    "

    class="character-traits-feats character-traits-feats-v6"

>

    <div class="character-traits-feats-v6-head">
        <h2 class="character-traits-feats-v6-title">
            Traços &amp; Talentos
        </h2>


    </div>

    <div class="character-traits-feats-v6-table">
        <div class="character-traits-feats-v6-columns">

            {{-- ============================================================
                 TRAÇOS DE ESPÉCIE
            ============================================================= --}}
            <section class="character-traits-feats-v6-column">
                <button
                    type="button"
                    @click="openCreate('species_trait')"
                    class="character-traits-feats-v6-column-head"
                    title="Adicionar Traço de Espécie"
                    aria-label="Adicionar Traço de Espécie"
                >
                    <span class="character-traits-feats-v6-column-label">
                        Traços de Espécie
                    </span>

                    <span
                        class="character-traits-feats-v6-add"
                        aria-hidden="true"
                    >+</span>
                </button>

                <div class="character-traits-feats-v6-list">
                    <template
                        x-for="entry in speciesTraits"
                        :key="'species-trait-' + entry.id"
                    >
                        <button
                            type="button"
                            @click="openDetail(entry)"
                            class="character-mini-feature-row"
                        >
                            <span class="character-mini-feature-row-top">
                                <strong
                                    class="character-mini-feature-name"
                                    x-text="entry.name"
                                ></strong>

                                <span
                                    class="character-mini-feature-level"
                                    x-text="
                                        entry.level_acquired
                                            ? 'Nível ' + entry.level_acquired
                                            : 'Nível —'
                                    "
                                ></span>
                            </span>

                            <span
                                x-show="entry.source"
                                x-cloak
                                class="character-mini-feature-meta"
                            >
                                <span x-text="entry.source"></span>
                            </span>

                            <span
                                x-show="entry.data?.quick_text"
                                x-cloak
                                class="character-mini-feature-quick"
                                x-text="entry.data.quick_text"
                            ></span>
                        </button>
                    </template>

                    <div
                        x-show="speciesTraits.length === 0"
                        x-cloak
                        class="character-traits-feats-v6-empty"
                    >
                        Nenhum traço cadastrado.
                    </div>
                </div>
            </section>

            {{-- ============================================================
                 TALENTOS
            ============================================================= --}}
            <section class="character-traits-feats-v6-column">
                <button
                    type="button"
                    @click="openCreate('feat')"
                    class="character-traits-feats-v6-column-head"
                    title="Adicionar Talento"
                    aria-label="Adicionar Talento"
                >
                    <span class="character-traits-feats-v6-column-label">
                        Talentos
                    </span>

                    <span
                        class="character-traits-feats-v6-add"
                        aria-hidden="true"
                    >+</span>
                </button>

                <div class="character-traits-feats-v6-list">
                    <template
                        x-for="entry in feats"
                        :key="'feat-' + entry.id"
                    >
                        <button
                            type="button"
                            @click="openDetail(entry)"
                            class="character-mini-feature-row"
                        >
                            <span class="character-mini-feature-row-top">
                                <strong
                                    class="character-mini-feature-name"
                                    x-text="entry.name"
                                ></strong>

                                <span
                                    class="character-mini-feature-level"
                                    x-text="
                                        entry.level_acquired
                                            ? 'Nível ' + entry.level_acquired
                                            : 'Nível —'
                                    "
                                ></span>
                            </span>

                            <span
                                x-show="entry.source"
                                x-cloak
                                class="character-mini-feature-meta"
                            >
                                <span x-text="entry.source"></span>
                            </span>

                            <span
                                x-show="entry.data?.quick_text"
                                x-cloak
                                class="character-mini-feature-quick"
                                x-text="entry.data.quick_text"
                            ></span>
                        </button>
                    </template>

                    <div
                        x-show="feats.length === 0"
                        x-cloak
                        class="character-traits-feats-v6-empty"
                    >
                        Nenhum talento cadastrado.
                    </div>
                </div>
            </section>
        </div>
    </div>


    {{-- ================================================================

         MODAL DE LEITURA

    ================================================================= --}}

    <template x-teleport="body">

        <div

            x-show="detailOpen && detailEntry"

            x-cloak

            :data-theme="document.querySelector('.character-sheet-theme[data-theme]')?.dataset.theme || 'spellbound'"

            class="

                character-sheet-theme

                character-simple-feature-overlay

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

                class="character-simple-feature-backdrop"

                @click="closeDetail()"

            ></div>

            <article

                class="character-simple-feature-modal character-simple-feature-modal--detail"

                @click.stop

            >

                <header class="character-simple-feature-modal-header">

                    <div class="min-w-0">

                        <span

                            class="character-simple-feature-kicker"

                            x-text="

                                detailEntry

                                    ? typeLabel(detailEntry.type)

                                    : ''

                            "

                        ></span>

                        <h3

                            class="character-simple-feature-modal-title"

                            x-text="detailEntry?.name ?? ''"

                        ></h3>

                    </div>

                    <button

                        type="button"

                        @click="closeDetail()"

                        class="character-simple-feature-close"

                        title="Fechar"

                    >

                        ×

                    </button>

                </header>

                <div class="character-simple-feature-body">

                    <div class="character-simple-feature-pills">

                        <span

                            class="character-simple-feature-pill"

                            x-text="

                                detailEntry?.level_acquired

                                    ? 'Nível ' + detailEntry.level_acquired

                                    : 'Nível não informado'

                            "

                        ></span>

                        <span

                            x-show="detailEntry?.source"

                            x-cloak

                            class="character-simple-feature-pill"

                            x-text="detailEntry?.source"

                        ></span>

                    </div>

                    <div

                        x-show="detailEntry?.data?.quick_text"

                        x-cloak

                        class="character-simple-feature-detail-quick"

                        x-text="detailEntry?.data?.quick_text"

                    ></div>

                    <p

                        x-show="detailEntry?.description"

                        x-cloak

                        class="character-simple-feature-description"

                        x-text="detailEntry?.description"

                    ></p>

                    <p

                        x-show="!detailEntry?.description"

                        x-cloak

                        class="character-simple-feature-description character-simple-feature-description-empty italic"

                    >

                        Nenhuma descrição completa cadastrada.

                    </p>

                </div>

                <footer class="character-simple-feature-modal-footer">

                    <button

                        type="button"

                        @click="openEdit(detailEntry)"

                        class="character-simple-feature-primary"

                    >

                        Editar

                    </button>

                </footer>

            </article>

        </div>

    </template>





    {{-- ================================================================

         MODAL DE CRIAÇÃO / EDIÇÃO

    ================================================================= --}}

    <template x-teleport="body">

        <div

            x-show="editorOpen && form"

            x-cloak

            :data-theme="document.querySelector('.character-sheet-theme[data-theme]')?.dataset.theme || 'spellbound'"

            class="

                character-sheet-theme

                character-simple-feature-overlay

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

                class="character-simple-feature-backdrop"

                @click="closeEditor()"

            ></div>

            <article

                class="character-simple-feature-modal character-simple-feature-modal--editor"

                @click.stop

            >

                <header class="character-simple-feature-modal-header">

                    <div class="min-w-0">

                        <span

                            class="character-simple-feature-kicker"

                            x-text="

                                form

                                    ? typeLabel(form.type)

                                    : ''

                            "

                        ></span>

                        <h3

                            class="character-simple-feature-modal-title"

                            x-text="

                                editingId

                                    ? 'Editar'

                                    : 'Adicionar'

                            "

                        ></h3>

                    </div>

                    <button

                        type="button"

                        @click="closeEditor()"

                        class="character-simple-feature-close"

                        title="Fechar"

                    >

                        ×

                    </button>

                </header>

                <div class="character-simple-feature-body">

                    <div class="character-simple-feature-editor-grid two">

                        <label class="character-simple-feature-field">

                            <span class="character-simple-feature-label">

                                Nome

                            </span>

                            <input

                                type="text"

                                maxlength="120"

                                x-model="form.name"

                                class="character-simple-feature-input"

                                placeholder="Ex.: Visão no Escuro"

                            >

                        </label>

                        <label class="character-simple-feature-field">

                            <span class="character-simple-feature-label">

                                Nível obtido

                            </span>

                            <input

                                type="number"

                                min="1"

                                max="255"

                                x-model="form.level_acquired"

                                class="character-simple-feature-input"

                                placeholder="1"

                            >

                        </label>

                    </div>

                    <label class="character-simple-feature-field">

                        <span

                            class="character-simple-feature-label"

                            x-text="

                                form?.type === 'species_trait'

                                    ? 'Espécie / origem'

                                    : 'Fonte'

                            "

                        ></span>

                        <input

                            type="text"

                            maxlength="100"

                            x-model="form.source"

                            class="character-simple-feature-input"

                            :placeholder="

                                form?.type === 'species_trait'

                                    ? 'Ex.: Elfa (Drow)'

                                    : 'Ex.: Talento de 4º nível'

                            "

                        >

                    </label>

                    <label class="character-simple-feature-field">

                        <span class="character-simple-feature-label">

                            Informação rápida

                        </span>

                        <textarea

                            maxlength="180"

                            x-model="form.data.quick_text"

                            class="character-simple-feature-textarea"

                            placeholder="Resumo curto para aparecer diretamente na ficha."

                        ></textarea>

                    </label>

                    <label class="character-simple-feature-field">

                        <span class="character-simple-feature-label">

                            Descrição completa

                        </span>

                        <textarea

                            maxlength="30000"

                            x-model="form.description"

                            class="

                                character-simple-feature-textarea

                                large

                            "

                            placeholder="Regra ou descrição completa."

                        ></textarea>

                    </label>

                    <div

                        x-show="saveError"

                        x-cloak

                        class="character-simple-feature-error"

                        x-text="saveError"

                    ></div>

                </div>

                <footer class="character-simple-feature-modal-footer">

                    <button

                        x-show="editingId !== null"

                        x-cloak

                        type="button"

                        @click="deleteEntry()"

                        :disabled="saving"

                        class="character-simple-feature-danger"

                    >

                        Excluir

                    </button>

                    <button

                        type="button"

                        @click="closeEditor()"

                        :disabled="saving"

                        class="character-simple-feature-secondary"

                    >

                        Cancelar

                    </button>

                    <button

                        type="button"

                        @click="saveEntry()"

                        :disabled="saving"

                        class="character-simple-feature-primary"

                        x-text="

                            saving

                                ? 'Salvando...'

                                : 'Salvar'

                        "

                    ></button>

                </footer>

            </article>

        </div>

    </template>

</section>