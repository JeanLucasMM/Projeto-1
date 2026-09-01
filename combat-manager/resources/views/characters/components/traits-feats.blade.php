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
            | TRAÇOS + TALENTOS — V2
            |--------------------------------------------------------------------------
            |
            | Pequenos painéis editoriais, na mesma família de Attack/Features.
            |
            */

            .character-traits-feats {
                width: 100%;
                max-width: 820px;

                margin-inline: auto;

                color: #432c21;
            }

            .character-traits-feats-grid {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr);

                gap: 10px;
            }

            @media (min-width: 760px) {
                .character-traits-feats-grid {
                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));

                    gap: 12px;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PAINEL — V2
            |--------------------------------------------------------------------------
            */

            .character-mini-feature-panel {
                display: flex;

                min-width: 0;
                height: 362px;

                flex-direction: column;

                overflow: hidden;

                border:
                    1px solid
                    #d6c3a6;

                border-radius: 9px;

                background: #f7f1e8;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.82),
                    0 1px 0
                    rgba(83,21,15,.025);
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO
            |--------------------------------------------------------------------------
            */

            .character-mini-feature-header {
                display: flex;

                min-height: 36px;

                flex: 0 0 36px;

                align-items: center;
                justify-content: space-between;

                gap: 8px;

                width: 100%;

                border-bottom:
                    1px solid
                    #d3bea0;

                background:
                    linear-gradient(
                        180deg,
                        #eee0cf 0%,
                        #e8d6c1 100%
                    );

                padding:
                    0 10px;

                text-align: left;

                transition:
                    background .12s ease;
            }

            .character-mini-feature-header:hover {
                background:
                    linear-gradient(
                        180deg,
                        #ead9c5 0%,
                        #e3ceb5 100%
                    );
            }

            .character-mini-feature-header-copy {
                display: flex;

                min-width: 0;

                align-items: center;

                gap: 7px;
            }

            .character-mini-feature-title {
                overflow: hidden;

                text-overflow: ellipsis;
                white-space: nowrap;

                font-family: Georgia, serif;
                font-size: 14px;
                font-weight: 900;
                line-height: 1;

                color: #53150f;
            }

            .character-mini-feature-count {
                display: inline-flex;

                min-width: 21px;
                height: 21px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.26);

                border-radius: 999px;

                background: #faf7f0;

                padding:
                    0 6px;

                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #7b5438;
            }

            .character-mini-feature-add {
                display: inline-flex;

                width: 23px;
                height: 23px;

                flex: 0 0 23px;

                align-items: center;
                justify-content: center;

                border-radius: 6px;

                color: #8c6239;

                font-size: 15px;
                font-weight: 900;
                line-height: 1;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .character-mini-feature-header:hover
            .character-mini-feature-add {
                background:
                    rgba(255,253,248,.64);

                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | LISTA
            |--------------------------------------------------------------------------
            */

            .character-mini-feature-list {
                min-height: 0;

                flex: 1 1 auto;

                overflow-y: auto;
                overflow-x: hidden;

                background: #fbf8f2;

                overscroll-behavior: contain;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .character-mini-feature-list::-webkit-scrollbar {
                width: 0;
                height: 0;

                display: none;
            }

            .character-mini-feature-row {
                display: block;

                width: 100%;

                border-bottom:
                    1px solid
                    #e5d8c4;

                background: #fffdfa;

                padding:
                    9px 10px 10px;

                text-align: left;

                transition:
                    background .12s ease,
                    box-shadow .12s ease;
            }

            .character-mini-feature-row:nth-child(even) {
                background: #f8f2e9;
            }

            .character-mini-feature-row:last-child {
                border-bottom: 0;
            }

            .character-mini-feature-row:hover {
                background: #f2e5d5;

                box-shadow:
                    inset 3px 0 0
                    rgba(107,29,20,.28);
            }

            .character-mini-feature-row-top {
                display: flex;

                min-width: 0;

                align-items: flex-start;
                justify-content: space-between;

                gap: 8px;
            }

            .character-mini-feature-name {
                min-width: 0;

                flex: 1;

                overflow: hidden;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.25;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-mini-feature-level {
                display: inline-flex;

                min-height: 21px;

                flex: 0 0 auto;

                align-items: center;

                border:
                    1px solid
                    #dbc6aa;

                border-radius: 999px;

                background: #faf6ee;

                padding:
                    0 7px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .035em;

                color: #8c6239;
            }

            .character-mini-feature-meta {
                display: flex;

                flex-wrap: wrap;

                gap:
                    3px 7px;

                margin-top: 3px;

                font-size: 8.5px;
                font-weight: 800;
                line-height: 1.2;

                color: #80634e;
            }

            .character-mini-feature-quick {
                display: -webkit-box;

                margin-top: 6px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                border-left:
                    2px solid
                    #b68a60;

                background: #f1e6d8;

                padding:
                    4px 7px;

                font-size: 10.5px;
                line-height: 1.38;

                color: #5f4636;
            }

            .character-mini-feature-empty {
                display: flex;

                min-height: 100%;

                align-items: center;
                justify-content: center;

                padding:
                    16px 12px;

                text-align: center;

                font-size: 10px;
                line-height: 1.4;

                color: #927762;
            }

            @media (max-width: 759px) {
                .character-mini-feature-panel {
                    height: 330px;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL
            |--------------------------------------------------------------------------
            */

            .character-simple-feature-backdrop {
                position: absolute;
                inset: 0;

                background:
                    rgba(42,23,18,.48);

                backdrop-filter:
                    blur(2px);
            }

            .character-simple-feature-modal {
                position: relative;
                z-index: 1;

                display: flex;

                width:
                    min(
                        650px,
                        calc(100vw - 28px)
                    );

                max-height:
                    min(
                        86vh,
                        780px
                    );

                flex-direction: column;

                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.68);

                border-radius: 14px;

                background: #fbf8f1;

                box-shadow:
                    0 22px 64px
                    rgba(42,23,18,.22);
            }

            .character-simple-feature-modal-header {
                display: flex;

                align-items: flex-start;
                justify-content: space-between;

                gap: 14px;

                border-bottom:
                    1px solid
                    rgba(160,119,77,.32);

                background: #eadbc8;

                padding:
                    14px 16px;
            }

            .character-simple-feature-kicker {
                display: block;

                margin-bottom: 3px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .character-simple-feature-modal-title {
                font-family: Georgia, serif;
                font-size: 21px;
                font-weight: 900;
                line-height: 1.15;

                color: #53150f;
            }

            .character-simple-feature-close {
                display: flex;

                width: 32px;
                height: 32px;

                flex: 0 0 32px;

                align-items: center;
                justify-content: center;

                border-radius: 7px;

                font-size: 19px;

                color: #8c6239;
            }

            .character-simple-feature-close:hover {
                background:
                    rgba(255,253,248,.50);

                color: #53150f;
            }

            .character-simple-feature-body {
                min-height: 0;

                overflow-y: auto;

                padding:
                    16px 18px 19px;
            }

            .character-simple-feature-pills {
                display: flex;

                flex-wrap: wrap;

                gap: 6px;

                margin-bottom: 13px;
            }

            .character-simple-feature-pill {
                display: inline-flex;

                min-height: 25px;

                align-items: center;

                border:
                    1px solid
                    rgba(176,140,98,.25);

                border-radius: 999px;

                background: #f0e4d5;

                padding:
                    0 8px;

                font-size: 9.5px;
                font-weight: 800;

                color: #6f5544;
            }

            .character-simple-feature-detail-quick {
                margin-bottom: 14px;

                border-left:
                    3px solid
                    #8c6239;

                background:
                    #f0e4d5;

                padding:
                    8px 10px;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.45;

                color: #53150f;
            }

            .character-simple-feature-description {
                white-space: pre-line;

                font-size: 13px;
                line-height: 1.62;

                color: #432c21;
            }

            .character-simple-feature-modal-footer {
                display: flex;

                align-items: center;
                justify-content: flex-end;

                gap: 8px;

                border-top:
                    1px solid
                    rgba(160,119,77,.30);

                background: #f7f0e6;

                padding:
                    10px 16px;
            }

            /*
            |--------------------------------------------------------------------------
            | EDITOR
            |--------------------------------------------------------------------------
            */

            .character-simple-feature-editor-grid {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr);

                gap: 12px;
            }

            @media (min-width: 620px) {
                .character-simple-feature-editor-grid.two {
                    grid-template-columns:
                        minmax(0, 1fr)
                        120px;
                }
            }

            .character-simple-feature-label {
                display: block;

                margin-bottom: 5px;

                font-size: 10px;
                font-weight: 900;

                color: #53150f;
            }

            .character-simple-feature-input,
            .character-simple-feature-textarea {
                width: 100%;

                border:
                    1px solid
                    rgba(176,140,98,.48);

                border-radius: 8px;

                background: #fffdf8;

                color: #2f211b;

                outline: none;

                transition:
                    border-color .12s ease,
                    box-shadow .12s ease;
            }

            .character-simple-feature-input {
                min-height: 40px;

                padding:
                    0 10px;

                font-size: 13px;
            }

            .character-simple-feature-textarea {
                min-height: 105px;

                resize: vertical;

                padding:
                    9px 10px;

                font-size: 13px;
                line-height: 1.5;
            }

            .character-simple-feature-textarea.large {
                min-height: 150px;
            }

            .character-simple-feature-input:focus,
            .character-simple-feature-textarea:focus {
                border-color: #8c6239;

                box-shadow:
                    0 0 0 2px
                    rgba(140,98,57,.08);
            }

            .character-simple-feature-field + .character-simple-feature-field,
            .character-simple-feature-editor-grid + .character-simple-feature-field,
            .character-simple-feature-field + .character-simple-feature-editor-grid {
                margin-top: 12px;
            }

            /*
            |--------------------------------------------------------------------------
            | BOTÕES
            |--------------------------------------------------------------------------
            */

            .character-simple-feature-secondary,
            .character-simple-feature-danger,
            .character-simple-feature-primary {
                display: inline-flex;

                min-height: 36px;

                align-items: center;
                justify-content: center;

                border-radius: 7px;

                padding:
                    0 11px;

                font-size: 9.5px;
                font-weight: 900;
                letter-spacing: .025em;

                text-transform: uppercase;
            }

            .character-simple-feature-secondary {
                border:
                    1px solid
                    rgba(176,140,98,.46);

                background: #fbf8f1;

                color: #8c6239;
            }

            .character-simple-feature-secondary:hover {
                background: #eadbc8;
            }

            .character-simple-feature-danger {
                margin-right: auto;

                color: #991b1b;
            }

            .character-simple-feature-danger:hover {
                background: #fff1f2;
            }

            .character-simple-feature-primary {
                background: #6b1d14;
                color: #faf8f2;
            }

            .character-simple-feature-primary:hover {
                background: #53150f;
            }

            .character-simple-feature-primary:disabled,
            .character-simple-feature-secondary:disabled,
            .character-simple-feature-danger:disabled {
                cursor: wait;
                opacity: .45;
            }

            .character-simple-feature-error {
                margin-top: 12px;

                border-left:
                    3px solid #991b1b;

                background: #fff1f2;

                padding:
                    8px 10px;

                font-size: 11px;
                line-height: 1.4;

                color: #991b1b;
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

    class="character-traits-feats"
>
    <div class="character-traits-feats-grid">

        {{-- ============================================================
             TRAÇOS DE ESPÉCIE
        ============================================================= --}}
        <section class="character-mini-feature-panel">
            <button
                type="button"
                @click="openCreate('species_trait')"
                class="character-mini-feature-header"
                title="Adicionar Traço de Espécie"
            >
                <span class="character-mini-feature-header-copy">
                    <span class="character-mini-feature-title">
                        Traços de Espécie
                    </span>

                    <span
                        class="character-mini-feature-count"
                        x-text="speciesTraits.length"
                    ></span>
                </span>

                <span
                    class="character-mini-feature-add"
                    aria-hidden="true"
                >
                    +
                </span>
            </button>

            <div class="character-mini-feature-list">
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
                    class="character-mini-feature-empty"
                >
                    Nenhum traço cadastrado.
                </div>
            </div>
        </section>


        {{-- ============================================================
             TALENTOS
        ============================================================= --}}
        <section class="character-mini-feature-panel">
            <button
                type="button"
                @click="openCreate('feat')"
                class="character-mini-feature-header"
                title="Adicionar Talento"
            >
                <span class="character-mini-feature-header-copy">
                    <span class="character-mini-feature-title">
                        Talentos
                    </span>

                    <span
                        class="character-mini-feature-count"
                        x-text="feats.length"
                    ></span>
                </span>

                <span
                    class="character-mini-feature-add"
                    aria-hidden="true"
                >
                    +
                </span>
            </button>

            <div class="character-mini-feature-list">
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
                    class="character-mini-feature-empty"
                >
                    Nenhum talento cadastrado.
                </div>
            </div>
        </section>
    </div>


    {{-- ================================================================
         MODAL DE LEITURA
    ================================================================= --}}
    <template x-teleport="body">
        <div
            x-show="detailOpen && detailEntry"
            x-cloak
            class="
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
                class="character-simple-feature-modal"
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
                        class="character-simple-feature-description italic text-[#927762]"
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
            class="
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
                class="character-simple-feature-modal"
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