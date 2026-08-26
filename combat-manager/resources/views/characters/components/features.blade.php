@php
    /*
    |--------------------------------------------------------------------------
    | HABILIDADES — V3
    |--------------------------------------------------------------------------
    |
    | A lista é dividida em duas colunas escolhidas pelo usuário.
    | Na folha mostramos apenas:
    |
    | - nome
    | - informação rápida
    | - rastreador, quando existir
    |
    | O conteúdo completo fica em um modal de leitura.
    |
    */

    $featuresPayload = $character->features
        ->filter(function ($feature) {
            return in_array(
                $feature->type,
                [
                    null,
                    '',
                    'class_feature',
                    'custom',
                ],
                true
            );
        })
        ->map(function ($feature) {
            return [
                'id' => $feature->id,
                'name' => $feature->name,
                'type' => $feature->type ?: 'class_feature',
                'source' => $feature->source,
                'level_acquired' => $feature->level_acquired,
                'description' => $feature->description,
                'uses_max' => $feature->uses_max,
                'uses_current' => $feature->uses_current,
                'recovery' => $feature->recovery,
                'data' => is_array($feature->data)
                    ? $feature->data
                    : [],
            ];
        })
        ->values();
@endphp

@once
    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            .character-features-v3 {
                color: #53150f;
            }

            .character-features-v3-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid rgba(205,187,159,.58);
                padding: 0 0 10px;
            }

            .character-features-v3-heading {
                min-width: 0;
            }

            .character-features-v3-kicker {
                display: block;
                margin-bottom: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .13em;
                text-transform: uppercase;
                color: #a07855;
            }

            .character-features-v3-title-row {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .character-features-v3-title-row h2 {
                font-family: Georgia, serif;
                font-size: 21px;
                font-weight: 900;
                line-height: 1.05;
                color: #53150f;
            }

            .character-features-v3-count {
                display: inline-flex;
                min-width: 27px;
                height: 25px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: #efe9dc;
                padding: 0 8px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #8c6239;
            }

            .character-features-v3-add {
                display: inline-flex;
                min-height: 38px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                gap: 7px;
                border-radius: 9px;
                background: #6b1d14;
                padding: 0 13px;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .045em;
                text-transform: uppercase;
                color: #faf8f2;
                transition: background .14s ease;
            }

            .character-features-v3-add:hover {
                background: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | DUAS LISTAS
            |--------------------------------------------------------------------------
            */

            .character-features-v3-columns {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 0;
                margin-top: 2px;
            }

            .character-features-v3-column {
                min-width: 0;
                padding-top: 4px;
            }

            .character-features-v3-column + .character-features-v3-column {
                border-top: 1px solid rgba(205,187,159,.46);
            }

            @media (min-width: 900px) {
                .character-features-v3-columns {
                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));
                }

                .character-features-v3-column:first-child {
                    padding-right: 16px;
                }

                .character-features-v3-column + .character-features-v3-column {
                    border-top: 0;
                    border-left: 1px solid rgba(205,187,159,.52);
                    padding-left: 16px;
                }
            }

            .character-feature-v3-row {
                position: relative;
                display: flex;
                min-height: 54px;
                align-items: center;
                gap: 10px;
                border-bottom: 1px solid rgba(216,199,171,.52);
                padding: 9px 2px;
                cursor: pointer;
                transition: background .12s ease;
            }

            .character-feature-v3-row:hover {
                background: rgba(239,233,220,.34);
            }

            .character-feature-v3-copy {
                min-width: 0;
                flex: 1;
            }

            .character-feature-v3-name {
                overflow: hidden;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
                line-height: 1.18;
                color: #53150f;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-feature-v3-quick {
                display: block;
                margin-top: 4px;
                overflow: hidden;
                font-size: 12px;
                line-height: 1.3;
                color: #7b5c48;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-feature-v3-no-quick {
                display: block;
                margin-top: 4px;
                font-size: 11px;
                font-style: italic;
                color: #b09982;
            }

            .character-feature-v3-right {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                gap: 6px;
            }

            .character-feature-v3-tracker {
                display: inline-flex;
                height: 30px;
                align-items: stretch;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #faf8f2;
            }

            .character-feature-v3-tracker button {
                display: flex;
                width: 27px;
                align-items: center;
                justify-content: center;
                color: #8c6239;
                font-size: 15px;
                font-weight: 800;
            }

            .character-feature-v3-tracker button:hover:not(:disabled) {
                background: #efe9dc;
                color: #53150f;
            }

            .character-feature-v3-tracker button:disabled {
                opacity: .35;
            }

            .character-feature-v3-tracker strong {
                display: flex;
                min-width: 45px;
                align-items: center;
                justify-content: center;
                border-right: 1px solid rgba(216,199,171,.58);
                border-left: 1px solid rgba(216,199,171,.58);
                padding: 0 6px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #53150f;
            }

            .character-feature-v3-chevron {
                width: 16px;
                height: 16px;
                flex: 0 0 16px;
                color: #b18c6c;
            }

            .character-features-v3-empty-column {
                padding: 22px 6px;
                text-align: center;
                font-size: 11px;
                color: #aa8b68;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAIS COMPARTILHADOS
            |--------------------------------------------------------------------------
            */

            .feature-v3-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(42,23,18,.62);
                backdrop-filter: blur(2px);
            }

            .feature-v3-modal {
                position: relative;
                z-index: 1;
                display: flex;
                width: min(760px, 100%);
                max-height: min(88vh, 850px);
                flex-direction: column;
                overflow: hidden;
                border: 1px solid #cdbb9f;
                border-radius: 15px;
                background: #faf8f2;
            }

            .feature-v3-modal-header {
                display: flex;
                flex: 0 0 auto;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                border-bottom: 1px solid rgba(205,187,159,.62);
                padding: 17px 19px;
            }

            .feature-v3-modal-kicker {
                display: block;
                margin-bottom: 3px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: #a07855;
            }

            .feature-v3-modal-title {
                font-family: Georgia, serif;
                font-size: 23px;
                font-weight: 900;
                line-height: 1.15;
                color: #53150f;
            }

            .feature-v3-modal-close {
                display: flex;
                width: 36px;
                height: 36px;
                flex: 0 0 36px;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
                color: #8c6239;
            }

            .feature-v3-modal-close:hover {
                background: #efe9dc;
                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL DE LEITURA
            |--------------------------------------------------------------------------
            */

            .feature-v3-detail-body {
                min-height: 0;
                flex: 1;
                overflow-y: auto;
                padding: 19px;
            }

            .feature-v3-detail-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 7px;
                margin-bottom: 15px;
            }

            .feature-v3-detail-pill {
                display: inline-flex;
                min-height: 27px;
                align-items: center;
                border-radius: 999px;
                background: #efe9dc;
                padding: 0 9px;
                font-size: 10px;
                font-weight: 800;
                color: #7b5c48;
            }

            .feature-v3-detail-quick {
                margin-bottom: 16px;
                border-left: 3px solid #8c6239;
                background: rgba(239,233,220,.48);
                padding: 10px 12px;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
                color: #53150f;
            }

            .feature-v3-detail-description {
                white-space: pre-line;
                font-size: 14px;
                line-height: 1.65;
                color: #3f2d24;
            }

            .feature-v3-detail-tracker {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 8px;
                margin-top: 18px;
                border-top: 1px solid rgba(216,199,171,.58);
                padding-top: 14px;
            }

            .feature-v3-detail-footer {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                border-top: 1px solid rgba(205,187,159,.62);
                padding: 12px 19px;
            }

            /*
            |--------------------------------------------------------------------------
            | EDITOR
            |--------------------------------------------------------------------------
            */

            .feature-editor-v3-body {
                min-height: 0;
                flex: 1;
                overflow-y: auto;
                padding: 18px 19px 20px;
            }

            .feature-editor-v3-section + .feature-editor-v3-section {
                margin-top: 20px;
                border-top: 1px solid rgba(216,199,171,.58);
                padding-top: 18px;
            }

            .feature-editor-v3-label {
                display: block;
                margin-bottom: 6px;
                font-size: 11px;
                font-weight: 900;
                color: #53150f;
            }

            .feature-editor-v3-help {
                margin-top: 5px;
                font-size: 10px;
                line-height: 1.4;
                color: #9a795b;
            }

            .feature-editor-v3-input,
            .feature-editor-v3-select,
            .feature-editor-v3-textarea {
                width: 100%;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 9px;
                background: #fffdf9;
                color: #2f211b;
                outline: none;
            }

            .feature-editor-v3-input,
            .feature-editor-v3-select {
                min-height: 42px;
                padding: 0 11px;
                font-size: 14px;
            }

            .feature-editor-v3-textarea {
                min-height: 126px;
                resize: vertical;
                padding: 10px 11px;
                font-size: 14px;
                line-height: 1.55;
            }

            .feature-editor-v3-input:focus,
            .feature-editor-v3-select:focus,
            .feature-editor-v3-textarea:focus {
                border-color: #8c6239;
                box-shadow: 0 0 0 2px rgba(140,98,57,.08);
            }

            .feature-editor-v3-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 13px;
            }

            @media (min-width: 640px) {
                .feature-editor-v3-grid-source {
                    grid-template-columns:
                        minmax(0, 1fr)
                        150px;
                }
            }

            .feature-editor-v3-choice-grid {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 7px;
            }

            @media (min-width: 640px) {
                .feature-editor-v3-choice-grid {
                    grid-template-columns:
                        repeat(5, minmax(0, 1fr));
                }
            }

            .feature-editor-v3-choice {
                min-height: 38px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #fffdf9;
                padding: 6px 8px;
                font-size: 10px;
                font-weight: 900;
                color: #8c6239;
            }

            .feature-editor-v3-choice:hover {
                background: #efe9dc;
            }

            .feature-editor-v3-choice.active {
                border-color: #6b1d14;
                background: #6b1d14;
                color: #faf8f2;
            }

            .feature-editor-v3-position {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .feature-editor-v3-position button {
                min-height: 58px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 10px;
                background: #fffdf9;
                padding: 8px 10px;
                text-align: left;
                color: #8c6239;
            }

            .feature-editor-v3-position button.active {
                border-color: #6b1d14;
                background: rgba(107,29,20,.07);
                color: #6b1d14;
            }

            .feature-editor-v3-position strong {
                display: block;
                font-size: 11px;
                font-weight: 900;
            }

            .feature-editor-v3-position span {
                display: block;
                margin-top: 2px;
                font-size: 10px;
                line-height: 1.3;
                color: #9a795b;
            }

            .feature-editor-v3-counter {
                margin-top: 12px;
                border: 1px solid rgba(205,187,159,.64);
                border-radius: 11px;
                background: #f5f0e6;
                padding: 13px;
            }

            .feature-editor-v3-counter-grid {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            @media (min-width: 640px) {
                .feature-editor-v3-counter-grid {
                    grid-template-columns:
                        110px
                        110px
                        minmax(0, 1fr);
                }
            }

            .feature-editor-v3-mode {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 7px;
                margin-top: 11px;
            }

            .feature-editor-v3-mode button {
                min-height: 38px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #faf8f2;
                padding: 6px 9px;
                font-size: 10px;
                font-weight: 900;
                color: #8c6239;
            }

            .feature-editor-v3-mode button.active {
                border-color: #6b1d14;
                background: rgba(107,29,20,.07);
                color: #6b1d14;
            }

            .feature-v3-error {
                margin-top: 14px;
                border-left: 3px solid #991b1b;
                background: #fff1f2;
                padding: 9px 11px;
                font-size: 12px;
                color: #991b1b;
            }

            .feature-v3-footer {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border-top: 1px solid rgba(205,187,159,.62);
                padding: 13px 19px;
            }

            .feature-v3-footer-right {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-left: auto;
            }

            .feature-v3-secondary,
            .feature-v3-danger,
            .feature-v3-primary {
                display: inline-flex;
                min-height: 39px;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
                padding: 0 13px;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            .feature-v3-secondary {
                border: 1px solid rgba(205,187,159,.74);
                background: #faf8f2;
                color: #8c6239;
            }

            .feature-v3-secondary:hover {
                background: #efe9dc;
            }

            .feature-v3-danger {
                color: #991b1b;
            }

            .feature-v3-danger:hover {
                background: #fff1f2;
            }

            .feature-v3-primary {
                background: #6b1d14;
                color: #faf8f2;
            }

            .feature-v3-primary:hover {
                background: #53150f;
            }

            .feature-v3-primary:disabled,
            .feature-v3-secondary:disabled,
            .feature-v3-danger:disabled {
                cursor: wait;
                opacity: .45;
            }
        </style>
    @endpush
@endonce

<section
    x-data="{
        features: @js($featuresPayload),

        detailOpen: false,
        detailFeature: null,

        editorOpen: false,
        deleteConfirmOpen: false,

        editingId: null,
        form: null,

        saving: false,
        busyUsesId: null,
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
            uses: @js(route(
                'characters.features.uses.update',
                [
                    'character' => $character,
                    'feature' => '__FEATURE__',
                ]
            )),
        },

        activationLabels: {
            passive: 'Passiva',
            action: 'Ação',
            bonus_action: 'Ação Bônus',
            reaction: 'Reação',
            special: 'Especial',
        },

        recoveryLabels: {
            none: 'Sem recuperação',
            short_rest: 'Desc. Curto',
            long_rest: 'Desc. Longo',
            day: 'Dia',
            dawn: 'Amanhecer',
            single_use: 'Uso Único',
            custom: 'Personalizado',
        },

        get leftFeatures() {
            return this.sortedFeatures.filter(
                feature =>
                    this.featureColumn(feature) === 'left'
            );
        },

        get rightFeatures() {
            return this.sortedFeatures.filter(
                feature =>
                    this.featureColumn(feature) === 'right'
            );
        },

        get sortedFeatures() {
            return [...this.features]
                .sort((a, b) => {
                    const levelA =
                        parseInt(a.level_acquired) || 999;

                    const levelB =
                        parseInt(b.level_acquired) || 999;

                    if (levelA !== levelB) {
                        return levelA - levelB;
                    }

                    return String(a.name ?? '')
                        .localeCompare(
                            String(b.name ?? ''),
                            'pt-BR'
                        );
                });
        },

        featureColumn(feature) {
            return feature?.data?.column === 'right'
                ? 'right'
                : 'left';
        },

        normalizeFeature(raw) {
            const data =
                raw?.data
                && typeof raw.data === 'object'
                && !Array.isArray(raw.data)
                    ? { ...raw.data }
                    : {};

            return {
                id: raw?.id ?? null,
                name: raw?.name ?? '',
                type: raw?.type || 'class_feature',
                source: raw?.source ?? '',
                level_acquired:
                    raw?.level_acquired === null
                    || raw?.level_acquired === undefined
                        ? ''
                        : parseInt(raw.level_acquired),
                description: raw?.description ?? '',
                uses_max:
                    raw?.uses_max === null
                    || raw?.uses_max === undefined
                        ? null
                        : parseInt(raw.uses_max),
                uses_current:
                    raw?.uses_current === null
                    || raw?.uses_current === undefined
                        ? null
                        : parseInt(raw.uses_current),
                recovery: raw?.recovery ?? null,
                data: {
                    activation:
                        data.activation ?? 'passive',
                    quick_text:
                        data.quick_text ?? '',
                    counter_mode:
                        data.counter_mode === 'build'
                            ? 'build'
                            : 'spend',
                    recovery_custom:
                        data.recovery_custom ?? '',
                    column:
                        data.column === 'right'
                            ? 'right'
                            : 'left',
                },
            };
        },

        blankForm() {
            return {
                name: '',
                type: 'class_feature',
                source: '',
                level_acquired: '',
                description: '',

                counter_enabled: false,
                uses_current: 1,
                uses_max: 1,
                recovery: 'none',

                data: {
                    activation: 'passive',
                    quick_text: '',
                    counter_mode: 'spend',
                    recovery_custom: '',
                    column: 'left',
                },
            };
        },

        openDetail(raw) {
            this.detailFeature =
                this.normalizeFeature(raw);

            this.detailOpen = true;
        },

        closeDetail() {
            this.detailOpen = false;
            this.detailFeature = null;
        },

        openCreate() {
            this.editingId = null;
            this.form = this.blankForm();
            this.saveError = null;
            this.deleteConfirmOpen = false;
            this.editorOpen = true;
        },

        openEdit(raw) {
            const feature =
                this.normalizeFeature(raw);

            this.detailOpen = false;
            this.detailFeature = null;

            this.editingId = feature.id;

            this.form = {
                ...feature,
                counter_enabled:
                    feature.uses_max !== null,
                uses_current:
                    feature.uses_current ?? 1,
                uses_max:
                    feature.uses_max ?? 1,
                recovery:
                    feature.recovery ?? 'none',
                data: {
                    ...feature.data,
                },
            };

            this.saveError = null;
            this.deleteConfirmOpen = false;
            this.editorOpen = true;
        },

        closeEditor() {
            if (this.saving) {
                return;
            }

            this.editorOpen = false;
            this.deleteConfirmOpen = false;
            this.editingId = null;
            this.form = null;
            this.saveError = null;
        },

        setActivation(value) {
            if (!this.form) return;

            this.form.data.activation = value;
        },

        setPosition(value) {
            if (!this.form) return;

            this.form.data.column =
                value === 'right'
                    ? 'right'
                    : 'left';
        },

        setCounterEnabled(enabled) {
            if (!this.form) return;

            this.form.counter_enabled = !!enabled;

            if (!enabled) {
                return;
            }

            this.form.uses_max = Math.max(
                1,
                parseInt(this.form.uses_max) || 1
            );

            if (
                this.form.data.counter_mode === 'build'
            ) {
                this.form.uses_current = Math.max(
                    0,
                    Math.min(
                        this.form.uses_max,
                        parseInt(this.form.uses_current) || 0
                    )
                );
            } else {
                this.form.uses_current = Math.max(
                    0,
                    Math.min(
                        this.form.uses_max,
                        parseInt(this.form.uses_current)
                        || this.form.uses_max
                    )
                );
            }
        },

        setCounterMode(mode) {
            if (!this.form) return;

            this.form.data.counter_mode =
                mode === 'build'
                    ? 'build'
                    : 'spend';

            if (this.editingId === null) {
                this.form.uses_current =
                    this.form.data.counter_mode === 'build'
                        ? 0
                        : Math.max(
                            1,
                            parseInt(this.form.uses_max) || 1
                        );
            }
        },

        clampCounter() {
            if (!this.form?.counter_enabled) {
                return;
            }

            this.form.uses_max = Math.max(
                1,
                parseInt(this.form.uses_max) || 1
            );

            this.form.uses_current = Math.max(
                0,
                Math.min(
                    this.form.uses_max,
                    parseInt(this.form.uses_current) || 0
                )
            );
        },

        activationLabel(feature) {
            const key =
                feature?.data?.activation
                ?? 'passive';

            return this.activationLabels[key]
                ?? 'Especial';
        },

        recoveryLabel(feature) {
            if (
                feature?.uses_max === null
                || feature?.uses_max === undefined
            ) {
                return '';
            }

            if (feature.recovery === 'custom') {
                return feature?.data?.recovery_custom
                    || 'Personalizado';
            }

            return this.recoveryLabels[
                feature.recovery
                ?? 'none'
            ] ?? '';
        },

        csrf() {
            return document
                .querySelector(
                    'meta[name=csrf-token]'
                )
                ?.getAttribute('content')
                ?? @js(csrf_token());
        },

        jsonHeaders() {
            return {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrf(),
                'X-Requested-With': 'XMLHttpRequest',
            };
        },

        upsertFeature(raw) {
            const feature =
                this.normalizeFeature(raw);

            const index =
                this.features.findIndex(
                    current =>
                        parseInt(current.id)
                        === parseInt(feature.id)
                );

            if (index >= 0) {
                this.features[index] = feature;
            } else {
                this.features.push(feature);
            }

            if (
                this.detailFeature
                && parseInt(this.detailFeature.id)
                    === parseInt(feature.id)
            ) {
                this.detailFeature = feature;
            }

            return feature;
        },

        payload() {
            const counter =
                !!this.form?.counter_enabled;

            const maximum = counter
                ? Math.max(
                    1,
                    parseInt(this.form?.uses_max) || 1
                )
                : null;

            let current = counter
                ? parseInt(this.form?.uses_current)
                : null;

            if (
                counter
                && Number.isNaN(current)
            ) {
                current =
                    this.form?.data?.counter_mode === 'build'
                        ? 0
                        : maximum;
            }

            if (counter) {
                current = Math.max(
                    0,
                    Math.min(
                        maximum,
                        current
                    )
                );
            }

            return {
                name: String(
                    this.form?.name ?? ''
                ).trim(),

                type: 'class_feature',

                source: String(
                    this.form?.source ?? ''
                ).trim() || null,

                level_acquired:
                    this.form?.level_acquired === ''
                    || this.form?.level_acquired === null
                        ? null
                        : Math.max(
                            1,
                            parseInt(
                                this.form.level_acquired
                            ) || 1
                        ),

                description: String(
                    this.form?.description ?? ''
                ).trim() || null,

                uses_max: counter
                    ? maximum
                    : null,

                uses_current: counter
                    ? current
                    : null,

                recovery: counter
                    ? (
                        this.form?.recovery
                        || 'none'
                    )
                    : null,

                data: {
                    activation:
                        this.form?.data?.activation
                        || 'passive',

                    quick_text: String(
                        this.form?.data?.quick_text
                        ?? ''
                    ).trim() || null,

                    counter_mode:
                        this.form?.data?.counter_mode
                        === 'build'
                            ? 'build'
                            : 'spend',

                    recovery_custom:
                        this.form?.recovery === 'custom'
                            ? (
                                String(
                                    this.form?.data
                                        ?.recovery_custom
                                    ?? ''
                                ).trim()
                                || null
                            )
                            : null,

                    column:
                        this.form?.data?.column === 'right'
                            ? 'right'
                            : 'left',
                },
            };
        },

        async saveFeature() {
            if (
                this.saving
                || !this.form
            ) {
                return;
            }

            const payload = this.payload();

            if (!payload.name) {
                this.saveError =
                    'Informe um nome para a habilidade.';
                return;
            }

            this.saving = true;
            this.saveError = null;

            const editing =
                this.editingId !== null;

            const url = editing
                ? this.urls.update.replace(
                    '__FEATURE__',
                    this.editingId
                )
                : this.urls.store;

            try {
                const response = await fetch(
                    url,
                    {
                        method: editing
                            ? 'PATCH'
                            : 'POST',
                        headers:
                            this.jsonHeaders(),
                        body:
                            JSON.stringify(payload),
                    }
                );

                const data =
                    await response
                        .json()
                        .catch(() => ({}));

                if (!response.ok) {
                    const errors =
                        data?.errors
                            ? Object.values(
                                data.errors
                            )
                                .flat()
                                .filter(Boolean)
                            : [];

                    throw new Error(
                        errors.length
                            ? errors.join(' ')
                            : (
                                data?.message
                                ?? 'Não foi possível salvar a habilidade.'
                            )
                    );
                }

                this.upsertFeature(
                    data.feature
                );

                this.saving = false;
                this.closeEditor();
            } catch (error) {
                this.saveError =
                    error?.message
                    ?? 'Não foi possível salvar a habilidade.';
            } finally {
                this.saving = false;
            }
        },

        async deleteFeature() {
            if (
                this.saving
                || this.editingId === null
            ) {
                return;
            }

            this.saving = true;
            this.saveError = null;

            try {
                const response = await fetch(
                    this.urls.destroy.replace(
                        '__FEATURE__',
                        this.editingId
                    ),
                    {
                        method: 'DELETE',
                        headers:
                            this.jsonHeaders(),
                    }
                );

                const data =
                    await response
                        .json()
                        .catch(() => ({}));

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível remover a habilidade.'
                    );
                }

                this.features =
                    this.features.filter(
                        feature =>
                            parseInt(feature.id)
                            !== parseInt(
                                this.editingId
                            )
                    );

                this.saving = false;
                this.closeEditor();
            } catch (error) {
                this.saveError =
                    error?.message
                    ?? 'Não foi possível remover a habilidade.';
                this.saving = false;
            }
        },

        async changeUses(
            raw,
            delta
        ) {
            if (this.busyUsesId !== null) {
                return;
            }

            const feature =
                this.normalizeFeature(raw);

            if (feature.uses_max === null) {
                return;
            }

            const next = Math.max(
                0,
                Math.min(
                    feature.uses_max,
                    (
                        parseInt(
                            feature.uses_current
                        ) || 0
                    ) + delta
                )
            );

            if (
                next
                === feature.uses_current
            ) {
                return;
            }

            this.busyUsesId =
                feature.id;

            try {
                const response = await fetch(
                    this.urls.uses.replace(
                        '__FEATURE__',
                        feature.id
                    ),
                    {
                        method: 'PATCH',
                        headers:
                            this.jsonHeaders(),
                        body:
                            JSON.stringify({
                                current: next,
                            }),
                    }
                );

                const data =
                    await response
                        .json()
                        .catch(() => ({}));

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível atualizar os usos.'
                    );
                }

                this.upsertFeature(
                    data.feature
                );
            } catch (error) {
                console.error(
                    'Erro ao atualizar habilidade:',
                    error
                );
            } finally {
                this.busyUsesId = null;
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

    class="character-features-v3"
>
    <div class="character-features-v3-header">
        <div class="character-features-v3-heading">
            <span class="character-features-v3-kicker">
                Características
            </span>

            <div class="character-features-v3-title-row">
                <h2>Habilidades</h2>

                <span
                    class="character-features-v3-count"
                    x-text="features.length"
                ></span>
            </div>
        </div>

        <button
            type="button"
            @click="openCreate()"
            class="character-features-v3-add"
        >
            <span class="text-[16px] leading-none">+</span>
            Nova Habilidade
        </button>
    </div>

    <div class="character-features-v3-columns">
        {{-- ESQUERDA --}}
        <div class="character-features-v3-column">
            <template
                x-for="feature in leftFeatures"
                :key="'left-feature-' + feature.id"
            >
                <article
                    class="character-feature-v3-row"
                    @click="openDetail(feature)"
                >
                    <div class="character-feature-v3-copy">
                        <h3
                            class="character-feature-v3-name"
                            x-text="feature.name"
                        ></h3>

                        <span
                            x-show="feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v3-quick"
                            x-text="feature.data.quick_text"
                        ></span>

                        <span
                            x-show="!feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v3-no-quick"
                        >
                            Abrir detalhes
                        </span>
                    </div>

                    <div class="character-feature-v3-right">
                        <template x-if="feature.uses_max !== null">
                            <div
                                class="character-feature-v3-tracker"
                                @click.stop
                            >
                                <button
                                    type="button"
                                    @click="changeUses(feature, -1)"
                                    :disabled="
                                        busyUsesId !== null
                                        || feature.uses_current <= 0
                                    "
                                >
                                    −
                                </button>

                                <strong
                                    x-text="
                                        feature.uses_current
                                        + '/'
                                        + feature.uses_max
                                    "
                                ></strong>

                                <button
                                    type="button"
                                    @click="changeUses(feature, 1)"
                                    :disabled="
                                        busyUsesId !== null
                                        || feature.uses_current >= feature.uses_max
                                    "
                                >
                                    +
                                </button>
                            </div>
                        </template>

                        <svg
                            class="character-feature-v3-chevron"
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                d="m7 5 5 5-5 5"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>
                </article>
            </template>

            <div
                x-show="leftFeatures.length === 0"
                x-cloak
                class="character-features-v3-empty-column"
            >
                Nenhuma habilidade nesta coluna.
            </div>
        </div>

        {{-- DIREITA --}}
        <div class="character-features-v3-column">
            <template
                x-for="feature in rightFeatures"
                :key="'right-feature-' + feature.id"
            >
                <article
                    class="character-feature-v3-row"
                    @click="openDetail(feature)"
                >
                    <div class="character-feature-v3-copy">
                        <h3
                            class="character-feature-v3-name"
                            x-text="feature.name"
                        ></h3>

                        <span
                            x-show="feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v3-quick"
                            x-text="feature.data.quick_text"
                        ></span>

                        <span
                            x-show="!feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v3-no-quick"
                        >
                            Abrir detalhes
                        </span>
                    </div>

                    <div class="character-feature-v3-right">
                        <template x-if="feature.uses_max !== null">
                            <div
                                class="character-feature-v3-tracker"
                                @click.stop
                            >
                                <button
                                    type="button"
                                    @click="changeUses(feature, -1)"
                                    :disabled="
                                        busyUsesId !== null
                                        || feature.uses_current <= 0
                                    "
                                >
                                    −
                                </button>

                                <strong
                                    x-text="
                                        feature.uses_current
                                        + '/'
                                        + feature.uses_max
                                    "
                                ></strong>

                                <button
                                    type="button"
                                    @click="changeUses(feature, 1)"
                                    :disabled="
                                        busyUsesId !== null
                                        || feature.uses_current >= feature.uses_max
                                    "
                                >
                                    +
                                </button>
                            </div>
                        </template>

                        <svg
                            class="character-feature-v3-chevron"
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                d="m7 5 5 5-5 5"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>
                </article>
            </template>

            <div
                x-show="rightFeatures.length === 0"
                x-cloak
                class="character-features-v3-empty-column"
            >
                Nenhuma habilidade nesta coluna.
            </div>
        </div>
    </div>

    {{-- ============================================================
         MODAL DE LEITURA
    ============================================================= --}}
    <template x-teleport="body">
        <div
            x-show="detailOpen"
            x-cloak
            class="
                fixed
                inset-0
                z-[255]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="feature-v3-backdrop"
                @click="closeDetail()"
            ></div>

            <section
                x-show="detailOpen"
                x-transition.opacity.duration.120ms
                class="feature-v3-modal"
                @click.stop
            >
                <header class="feature-v3-modal-header">
                    <div>
                        <span class="feature-v3-modal-kicker">
                            Habilidade
                        </span>

                        <h3
                            class="feature-v3-modal-title"
                            x-text="
                                detailFeature?.name
                                || 'Habilidade'
                            "
                        ></h3>
                    </div>

                    <button
                        type="button"
                        @click="closeDetail()"
                        class="feature-v3-modal-close"
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

                <div class="feature-v3-detail-body">
                    <div class="feature-v3-detail-meta">
                        <span
                            x-show="detailFeature?.source"
                            x-cloak
                            class="feature-v3-detail-pill"
                            x-text="detailFeature?.source"
                        ></span>

                        <span
                            x-show="detailFeature?.level_acquired"
                            x-cloak
                            class="feature-v3-detail-pill"
                            x-text="
                                'Nível '
                                + detailFeature?.level_acquired
                            "
                        ></span>

                        <span
                            class="feature-v3-detail-pill"
                            x-text="
                                activationLabel(
                                    detailFeature
                                )
                            "
                        ></span>

                        <span
                            class="feature-v3-detail-pill"
                            x-text="
                                featureColumn(detailFeature)
                                === 'right'
                                    ? 'Coluna direita'
                                    : 'Coluna esquerda'
                            "
                        ></span>
                    </div>

                    <div
                        x-show="detailFeature?.data?.quick_text"
                        x-cloak
                        class="feature-v3-detail-quick"
                        x-text="
                            detailFeature?.data?.quick_text
                        "
                    ></div>

                    <p
                        x-show="detailFeature?.description"
                        x-cloak
                        class="feature-v3-detail-description"
                        x-text="detailFeature?.description"
                    ></p>

                    <p
                        x-show="!detailFeature?.description"
                        x-cloak
                        class="feature-v3-detail-description italic text-[#9a795b]"
                    >
                        Nenhuma descrição cadastrada.
                    </p>

                    <div
                        x-show="detailFeature?.uses_max !== null"
                        x-cloak
                        class="feature-v3-detail-tracker"
                    >
                        <div
                            class="character-feature-v3-tracker"
                        >
                            <button
                                type="button"
                                @click="
                                    changeUses(
                                        detailFeature,
                                        -1
                                    )
                                "
                                :disabled="
                                    busyUsesId !== null
                                    || detailFeature?.uses_current <= 0
                                "
                            >
                                −
                            </button>

                            <strong
                                x-text="
                                    detailFeature?.uses_current
                                    + '/'
                                    + detailFeature?.uses_max
                                "
                            ></strong>

                            <button
                                type="button"
                                @click="
                                    changeUses(
                                        detailFeature,
                                        1
                                    )
                                "
                                :disabled="
                                    busyUsesId !== null
                                    || detailFeature?.uses_current >= detailFeature?.uses_max
                                "
                            >
                                +
                            </button>
                        </div>

                        <span
                            class="feature-v3-detail-pill"
                            x-text="
                                recoveryLabel(
                                    detailFeature
                                )
                            "
                        ></span>
                    </div>
                </div>

                <footer class="feature-v3-detail-footer">
                    <button
                        type="button"
                        @click="
                            openEdit(
                                detailFeature
                            )
                        "
                        class="feature-v3-primary"
                    >
                        Editar
                    </button>
                </footer>
            </section>
        </div>
    </template>

    {{-- ============================================================
         EDITOR
    ============================================================= --}}
    <template x-teleport="body">
        <div
            x-show="editorOpen"
            x-cloak
            class="
                fixed
                inset-0
                z-[260]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="feature-v3-backdrop"
                @click="closeEditor()"
            ></div>

            <section
                x-show="editorOpen"
                x-transition.opacity.duration.120ms
                class="feature-v3-modal"
                @click.stop
            >
                <header class="feature-v3-modal-header">
                    <div>
                        <span
                            class="feature-v3-modal-kicker"
                            x-text="
                                editingId === null
                                    ? 'Nova Habilidade'
                                    : 'Editar Habilidade'
                            "
                        ></span>

                        <h3
                            class="feature-v3-modal-title"
                            x-text="
                                form?.name
                                || 'Habilidade'
                            "
                        ></h3>
                    </div>

                    <button
                        type="button"
                        @click="closeEditor()"
                        class="feature-v3-modal-close"
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

                <div
                    x-show="form"
                    class="feature-editor-v3-body"
                >
                    <section class="feature-editor-v3-section">
                        <label>
                            <span class="feature-editor-v3-label">
                                Nome
                            </span>

                            <input
                                type="text"
                                maxlength="120"
                                x-model="form.name"
                                placeholder="Ex.: Segundo Fôlego"
                                class="feature-editor-v3-input"
                            >
                        </label>

                        <div
                            class="
                                feature-editor-v3-grid
                                feature-editor-v3-grid-source
                                mt-3
                            "
                        >
                            <label>
                                <span class="feature-editor-v3-label">
                                    Origem
                                </span>

                                <input
                                    type="text"
                                    maxlength="100"
                                    x-model="form.source"
                                    placeholder="Ex.: Guerreiro"
                                    class="feature-editor-v3-input"
                                >
                            </label>

                            <label>
                                <span class="feature-editor-v3-label">
                                    Nível adquirido
                                </span>

                                <input
                                    type="number"
                                    min="1"
                                    max="255"
                                    x-model.number="form.level_acquired"
                                    placeholder="1"
                                    class="feature-editor-v3-input"
                                >
                            </label>
                        </div>
                    </section>

                    {{-- POSIÇÃO --}}
                    <section class="feature-editor-v3-section">
                        <span class="feature-editor-v3-label">
                            Posição na ficha
                        </span>

                        <div class="feature-editor-v3-position">
                            <button
                                type="button"
                                @click="setPosition('left')"
                                :class="{
                                    'active':
                                        form.data.column === 'left'
                                }"
                            >
                                <strong>Lista esquerda</strong>
                                <span>
                                    Exibe esta habilidade na primeira coluna.
                                </span>
                            </button>

                            <button
                                type="button"
                                @click="setPosition('right')"
                                :class="{
                                    'active':
                                        form.data.column === 'right'
                                }"
                            >
                                <strong>Lista direita</strong>
                                <span>
                                    Exibe esta habilidade na segunda coluna.
                                </span>
                            </button>
                        </div>
                    </section>

                    {{-- ATIVAÇÃO --}}
                    <section class="feature-editor-v3-section">
                        <span class="feature-editor-v3-label">
                            Ativação
                        </span>

                        <div class="feature-editor-v3-choice-grid">
                            <button
                                type="button"
                                @click="setActivation('passive')"
                                class="feature-editor-v3-choice"
                                :class="{
                                    'active':
                                        form.data.activation === 'passive'
                                }"
                            >
                                Passiva
                            </button>

                            <button
                                type="button"
                                @click="setActivation('action')"
                                class="feature-editor-v3-choice"
                                :class="{
                                    'active':
                                        form.data.activation === 'action'
                                }"
                            >
                                Ação
                            </button>

                            <button
                                type="button"
                                @click="setActivation('bonus_action')"
                                class="feature-editor-v3-choice"
                                :class="{
                                    'active':
                                        form.data.activation === 'bonus_action'
                                }"
                            >
                                Ação Bônus
                            </button>

                            <button
                                type="button"
                                @click="setActivation('reaction')"
                                class="feature-editor-v3-choice"
                                :class="{
                                    'active':
                                        form.data.activation === 'reaction'
                                }"
                            >
                                Reação
                            </button>

                            <button
                                type="button"
                                @click="setActivation('special')"
                                class="feature-editor-v3-choice"
                                :class="{
                                    'active':
                                        form.data.activation === 'special'
                                }"
                            >
                                Especial
                            </button>
                        </div>
                    </section>

                    {{-- TEXTO --}}
                    <section class="feature-editor-v3-section">
                        <label>
                            <span class="feature-editor-v3-label">
                                Informação rápida
                            </span>

                            <input
                                type="text"
                                maxlength="180"
                                x-model="form.data.quick_text"
                                placeholder="Ex.: 1d10+2 PV"
                                class="feature-editor-v3-input"
                            >

                            <p class="feature-editor-v3-help">
                                Este é o único texto descritivo mostrado diretamente na folha.
                            </p>
                        </label>

                        <label class="mt-4 block">
                            <span class="feature-editor-v3-label">
                                Descrição completa
                            </span>

                            <textarea
                                maxlength="30000"
                                x-model="form.description"
                                placeholder="Descreva o funcionamento completo da habilidade..."
                                class="feature-editor-v3-textarea"
                            ></textarea>

                            <p class="feature-editor-v3-help">
                                A descrição completa aparece apenas ao abrir a habilidade.
                            </p>
                        </label>
                    </section>

                    {{-- RASTREADOR --}}
                    <section class="feature-editor-v3-section">
                        <span class="feature-editor-v3-label">
                            Rastreador
                        </span>

                        <div
                            class="
                                grid
                                max-w-sm
                                grid-cols-2
                                gap-2
                            "
                        >
                            <button
                                type="button"
                                @click="setCounterEnabled(false)"
                                class="feature-editor-v3-choice"
                                :class="{
                                    'active':
                                        !form.counter_enabled
                                }"
                            >
                                Sem Rastreador
                            </button>

                            <button
                                type="button"
                                @click="setCounterEnabled(true)"
                                class="feature-editor-v3-choice"
                                :class="{
                                    'active':
                                        form.counter_enabled
                                }"
                            >
                                Com Rastreador
                            </button>
                        </div>

                        <div
                            x-show="form.counter_enabled"
                            x-cloak
                            class="feature-editor-v3-counter"
                        >
                            <div class="feature-editor-v3-counter-grid">
                                <label>
                                    <span class="feature-editor-v3-label">
                                        Atual
                                    </span>

                                    <input
                                        type="number"
                                        min="0"
                                        x-model.number="form.uses_current"
                                        @change="clampCounter()"
                                        class="feature-editor-v3-input"
                                    >
                                </label>

                                <label>
                                    <span class="feature-editor-v3-label">
                                        Máximo
                                    </span>

                                    <input
                                        type="number"
                                        min="1"
                                        x-model.number="form.uses_max"
                                        @change="clampCounter()"
                                        class="feature-editor-v3-input"
                                    >
                                </label>

                                <label>
                                    <span class="feature-editor-v3-label">
                                        Recuperação
                                    </span>

                                    <select
                                        x-model="form.recovery"
                                        class="feature-editor-v3-select"
                                    >
                                        <option value="none">
                                            Sem recuperação
                                        </option>

                                        <option value="short_rest">
                                            Descanso Curto
                                        </option>

                                        <option value="long_rest">
                                            Descanso Longo
                                        </option>

                                        <option value="day">
                                            Dia
                                        </option>

                                        <option value="dawn">
                                            Amanhecer
                                        </option>

                                        <option value="single_use">
                                            Uso Único
                                        </option>

                                        <option value="custom">
                                            Personalizado
                                        </option>
                                    </select>
                                </label>
                            </div>

                            <label
                                x-show="form.recovery === 'custom'"
                                x-cloak
                                class="mt-3 block"
                            >
                                <span class="feature-editor-v3-label">
                                    Recuperação personalizada
                                </span>

                                <input
                                    type="text"
                                    maxlength="120"
                                    x-model="form.data.recovery_custom"
                                    placeholder="Ex.: Após completar um ritual"
                                    class="feature-editor-v3-input"
                                >
                            </label>

                            <div class="feature-editor-v3-mode">
                                <button
                                    type="button"
                                    @click="setCounterMode('spend')"
                                    :class="{
                                        'active':
                                            form.data.counter_mode === 'spend'
                                    }"
                                >
                                    Gastar usos
                                </button>

                                <button
                                    type="button"
                                    @click="setCounterMode('build')"
                                    :class="{
                                        'active':
                                            form.data.counter_mode === 'build'
                                    }"
                                >
                                    Acumular
                                </button>
                            </div>
                        </div>
                    </section>

                    <div
                        x-show="saveError"
                        x-cloak
                        class="feature-v3-error"
                        x-text="saveError"
                    ></div>
                </div>

                <footer class="feature-v3-footer">
                    <div>
                        <template x-if="editingId !== null">
                            <div>
                                <button
                                    x-show="!deleteConfirmOpen"
                                    type="button"
                                    @click="deleteConfirmOpen = true"
                                    :disabled="saving"
                                    class="feature-v3-danger"
                                >
                                    Excluir
                                </button>

                                <div
                                    x-show="deleteConfirmOpen"
                                    x-cloak
                                    class="flex items-center gap-2"
                                >
                                    <span class="text-[11px] font-bold text-red-800">
                                        Excluir?
                                    </span>

                                    <button
                                        type="button"
                                        @click="deleteConfirmOpen = false"
                                        :disabled="saving"
                                        class="feature-v3-secondary"
                                    >
                                        Não
                                    </button>

                                    <button
                                        type="button"
                                        @click="deleteFeature()"
                                        :disabled="saving"
                                        class="feature-v3-danger"
                                    >
                                        Sim
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="feature-v3-footer-right">
                        <button
                            type="button"
                            @click="closeEditor()"
                            :disabled="saving"
                            class="feature-v3-secondary"
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="saveFeature()"
                            :disabled="saving"
                            class="feature-v3-primary"
                        >
                            <span
                                x-text="
                                    saving
                                        ? 'Salvando...'
                                        : 'Salvar'
                                "
                            ></span>
                        </button>
                    </div>
                </footer>
            </section>
        </div>
    </template>
</section>