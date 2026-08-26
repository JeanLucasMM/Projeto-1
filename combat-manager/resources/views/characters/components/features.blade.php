@php
    /*
    |--------------------------------------------------------------------------
    | HABILIDADES
    |--------------------------------------------------------------------------
    |
    | Este componente usa character_features.
    |
    | Nesta primeira etapa exibimos:
    |
    | - class_feature
    | - custom
    | - registros legados sem type
    |
    | species_trait e feat permanecem na mesma tabela, mas receberão
    | componentes próprios nas próximas etapas.
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
        ->sortBy(function (array $feature) {
            return sprintf(
                '%04d-%s-%010d',
                (int) ($feature['level_acquired'] ?? 999),
                mb_strtolower((string) $feature['name']),
                (int) $feature['id']
            );
        })
        ->values();
@endphp

@once
    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            .character-features-v1 {
                color: #53150f;
            }

            .character-features-v1-header {
                display: flex;
                align-items: end;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid rgba(205,187,159,.56);
                padding: 0 0 10px;
            }

            .character-features-v1-kicker {
                display: block;
                margin-bottom: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: #a07855;
            }

            .character-features-v1-title {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .character-features-v1-title h2 {
                font-family: Georgia, serif;
                font-size: 21px;
                font-weight: 900;
                line-height: 1.05;
                color: #53150f;
            }

            .character-features-v1-count {
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

            .character-features-v1-subtitle {
                margin-top: 4px;
                font-size: 12px;
                color: #8c6239;
            }

            .character-features-v1-add {
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
                transition:
                    background .14s ease,
                    transform .14s ease;
            }

            .character-features-v1-add:hover {
                background: #53150f;
            }

            .character-features-v1-add:active {
                transform: translateY(1px);
            }

            .character-features-v1-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 0 22px;
                margin-top: 4px;
            }

            @media (min-width: 1180px) {
                .character-features-v1-grid {
                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));
                }
            }

            .character-feature-v1-card {
                position: relative;
                min-width: 0;
                border-bottom: 1px solid rgba(205,187,159,.54);
                padding: 13px 4px 13px 13px;
            }

            .character-feature-v1-card::before {
                content: "";
                position: absolute;
                top: 16px;
                bottom: 16px;
                left: 0;
                width: 2px;
                border-radius: 999px;
                background: rgba(140,98,57,.34);
            }

            .character-feature-v1-card:hover::before {
                background: #6b1d14;
            }

            .character-feature-v1-card-head {
                display: flex;
                min-width: 0;
                align-items: flex-start;
                gap: 10px;
            }

            .character-feature-v1-main {
                min-width: 0;
                flex: 1;
            }

            .character-feature-v1-name-row {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px 8px;
            }

            .character-feature-v1-name {
                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: 900;
                line-height: 1.2;
                color: #53150f;
            }

            .character-feature-v1-quick {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                background: rgba(140,98,57,.08);
                padding: 3px 7px;
                font-size: 10px;
                font-weight: 800;
                color: #8c6239;
            }

            .character-feature-v1-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 4px 7px;
                margin-top: 4px;
                font-size: 10px;
                font-weight: 700;
                color: #9a795b;
            }

            .character-feature-v1-meta span + span::before {
                content: "·";
                margin-right: 7px;
                color: #c0a789;
            }

            .character-feature-v1-description {
                margin-top: 8px;
                white-space: pre-line;
                font-size: 13px;
                line-height: 1.55;
                color: #4f3427;
            }

            .character-feature-v1-actions {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                gap: 5px;
            }

            .character-feature-v1-edit {
                display: flex;
                width: 31px;
                height: 31px;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
                color: #8c6239;
                transition:
                    background .14s ease,
                    color .14s ease;
            }

            .character-feature-v1-edit:hover {
                background: #efe9dc;
                color: #53150f;
            }

            .character-feature-v1-edit svg {
                width: 15px;
                height: 15px;
            }

            .character-feature-v1-tracker {
                display: inline-flex;
                height: 30px;
                flex: 0 0 auto;
                align-items: stretch;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #faf8f2;
            }

            .character-feature-v1-tracker button {
                display: flex;
                width: 28px;
                align-items: center;
                justify-content: center;
                background: transparent;
                font-size: 15px;
                font-weight: 800;
                color: #8c6239;
            }

            .character-feature-v1-tracker button:hover:not(:disabled) {
                background: #efe9dc;
                color: #53150f;
            }

            .character-feature-v1-tracker button:disabled {
                cursor: wait;
                opacity: .4;
            }

            .character-feature-v1-tracker strong {
                display: flex;
                min-width: 47px;
                align-items: center;
                justify-content: center;
                border-right: 1px solid rgba(216,199,171,.58);
                border-left: 1px solid rgba(216,199,171,.58);
                padding: 0 7px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #53150f;
            }

            .character-feature-v1-recovery {
                display: inline-flex;
                min-height: 26px;
                align-items: center;
                border-radius: 999px;
                background: rgba(107,29,20,.055);
                padding: 0 8px;
                font-size: 9px;
                font-weight: 800;
                color: #6b1d14;
            }

            .character-features-v1-empty {
                margin-top: 12px;
                border: 1px dashed rgba(205,187,159,.72);
                border-radius: 11px;
                padding: 22px 16px;
                text-align: center;
                color: #8c6239;
            }

            .character-features-v1-empty strong {
                display: block;
                font-family: Georgia, serif;
                font-size: 16px;
                color: #53150f;
            }

            .character-features-v1-empty p {
                margin-top: 3px;
                font-size: 12px;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL
            |--------------------------------------------------------------------------
            */

            .feature-editor-v1-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(42,23,18,.62);
                backdrop-filter: blur(2px);
            }

            .feature-editor-v1 {
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

            .feature-editor-v1-header {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                border-bottom: 1px solid rgba(205,187,159,.62);
                padding: 17px 19px;
            }

            .feature-editor-v1-header small {
                display: block;
                margin-bottom: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: #a07855;
            }

            .feature-editor-v1-header h3 {
                font-family: Georgia, serif;
                font-size: 22px;
                font-weight: 900;
                color: #53150f;
            }

            .feature-editor-v1-close {
                display: flex;
                width: 36px;
                height: 36px;
                flex: 0 0 36px;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
                color: #8c6239;
            }

            .feature-editor-v1-close:hover {
                background: #efe9dc;
                color: #53150f;
            }

            .feature-editor-v1-body {
                min-height: 0;
                flex: 1;
                overflow-y: auto;
                padding: 18px 19px 20px;
            }

            .feature-editor-v1-section + .feature-editor-v1-section {
                margin-top: 20px;
                border-top: 1px solid rgba(216,199,171,.58);
                padding-top: 18px;
            }

            .feature-editor-v1-label {
                display: block;
                margin-bottom: 6px;
                font-size: 11px;
                font-weight: 900;
                color: #53150f;
            }

            .feature-editor-v1-help {
                margin-top: 5px;
                font-size: 10px;
                line-height: 1.4;
                color: #9a795b;
            }

            .feature-editor-v1-input,
            .feature-editor-v1-select,
            .feature-editor-v1-textarea {
                width: 100%;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 9px;
                background: #fffdf9;
                color: #2f211b;
                outline: none;
                transition:
                    border-color .14s ease,
                    box-shadow .14s ease;
            }

            .feature-editor-v1-input,
            .feature-editor-v1-select {
                min-height: 42px;
                padding: 0 11px;
                font-size: 14px;
            }

            .feature-editor-v1-textarea {
                min-height: 126px;
                resize: vertical;
                padding: 10px 11px;
                font-size: 14px;
                line-height: 1.55;
            }

            .feature-editor-v1-input:focus,
            .feature-editor-v1-select:focus,
            .feature-editor-v1-textarea:focus {
                border-color: #8c6239;
                box-shadow: 0 0 0 2px rgba(140,98,57,.08);
            }

            .feature-editor-v1-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 13px;
            }

            @media (min-width: 640px) {
                .feature-editor-v1-grid-2 {
                    grid-template-columns:
                        minmax(0, 1fr)
                        minmax(0, 1fr);
                }

                .feature-editor-v1-grid-source {
                    grid-template-columns:
                        minmax(0, 1fr)
                        150px;
                }
            }

            .feature-editor-v1-choice-grid {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 7px;
            }

            @media (min-width: 640px) {
                .feature-editor-v1-choice-grid {
                    grid-template-columns:
                        repeat(5, minmax(0, 1fr));
                }
            }

            .feature-editor-v1-choice {
                min-height: 38px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #fffdf9;
                padding: 6px 8px;
                font-size: 10px;
                font-weight: 900;
                color: #8c6239;
            }

            .feature-editor-v1-choice:hover {
                background: #efe9dc;
            }

            .feature-editor-v1-choice.active {
                border-color: #6b1d14;
                background: #6b1d14;
                color: #faf8f2;
            }

            .feature-editor-v1-counter {
                margin-top: 12px;
                border: 1px solid rgba(205,187,159,.64);
                border-radius: 11px;
                background: #f5f0e6;
                padding: 13px;
            }

            .feature-editor-v1-counter-grid {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            @media (min-width: 640px) {
                .feature-editor-v1-counter-grid {
                    grid-template-columns:
                        110px
                        110px
                        minmax(0, 1fr);
                }
            }

            .feature-editor-v1-mode {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 7px;
                margin-top: 11px;
            }

            .feature-editor-v1-mode button {
                min-height: 38px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #faf8f2;
                padding: 6px 9px;
                font-size: 10px;
                font-weight: 900;
                color: #8c6239;
            }

            .feature-editor-v1-mode button.active {
                border-color: #6b1d14;
                background: rgba(107,29,20,.07);
                color: #6b1d14;
            }

            .feature-editor-v1-error {
                margin-top: 14px;
                border-left: 3px solid #991b1b;
                background: #fff1f2;
                padding: 9px 11px;
                font-size: 12px;
                color: #991b1b;
            }

            .feature-editor-v1-footer {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border-top: 1px solid rgba(205,187,159,.62);
                padding: 13px 19px;
            }

            .feature-editor-v1-footer-right {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-left: auto;
            }

            .feature-editor-v1-secondary,
            .feature-editor-v1-danger,
            .feature-editor-v1-save {
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

            .feature-editor-v1-secondary {
                border: 1px solid rgba(205,187,159,.74);
                background: #faf8f2;
                color: #8c6239;
            }

            .feature-editor-v1-secondary:hover {
                background: #efe9dc;
            }

            .feature-editor-v1-danger {
                color: #991b1b;
            }

            .feature-editor-v1-danger:hover {
                background: #fff1f2;
            }

            .feature-editor-v1-save {
                background: #6b1d14;
                color: #faf8f2;
            }

            .feature-editor-v1-save:hover {
                background: #53150f;
            }

            .feature-editor-v1-save:disabled,
            .feature-editor-v1-secondary:disabled,
            .feature-editor-v1-danger:disabled {
                cursor: wait;
                opacity: .45;
            }
        </style>
    @endpush
@endonce

<section
    x-data="{
        features: @js($featuresPayload),

        modalOpen: false,
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

        get visibleFeatures() {
            return [...this.features]
                .sort((a, b) => {
                    const levelA = parseInt(a.level_acquired) || 999;
                    const levelB = parseInt(b.level_acquired) || 999;

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
                    activation: data.activation ?? 'passive',
                    quick_text: data.quick_text ?? '',
                    counter_mode:
                        data.counter_mode === 'build'
                            ? 'build'
                            : 'spend',
                    recovery_custom:
                        data.recovery_custom ?? '',
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
                },
            };
        },

        openCreate() {
            this.editingId = null;
            this.form = this.blankForm();
            this.saveError = null;
            this.deleteConfirmOpen = false;
            this.modalOpen = true;
        },

        openEdit(raw) {
            const feature = this.normalizeFeature(raw);

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
            this.modalOpen = true;
        },

        closeModal() {
            if (this.saving) {
                return;
            }

            this.modalOpen = false;
            this.deleteConfirmOpen = false;
            this.editingId = null;
            this.form = null;
            this.saveError = null;
        },

        setActivation(value) {
            if (!this.form) return;

            this.form.data.activation = value;
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

        featureMeta(feature) {
            const parts = [];

            if (feature.source) {
                parts.push(feature.source);
            }

            if (feature.level_acquired) {
                parts.push(
                    `Nível ${feature.level_acquired}`
                );
            }

            parts.push(
                this.activationLabel(feature)
            );

            return parts;
        },

        payload() {
            const counter =
                !!this.form?.counter_enabled;

            let maximum = counter
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
                },
            };
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

            return feature;
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

                /*
                | closeModal bloqueia fechamento enquanto saving=true.
                | Liberamos o estado antes de fechar.
                */
                this.saving = false;
                this.closeModal();
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
                this.closeModal();
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
        if (modalOpen) {
            closeModal()
        }
    "

    class="character-features-v1"
>
    {{-- ============================================================
         CABEÇALHO
    ============================================================= --}}
    <div class="character-features-v1-header">
        <div>
            <span class="character-features-v1-kicker">
                Características
            </span>

            <div class="character-features-v1-title">
                <h2>Habilidades</h2>

                <span
                    class="character-features-v1-count"
                    x-text="visibleFeatures.length"
                ></span>
            </div>

            <p class="character-features-v1-subtitle">
                Características de classe e habilidades especiais do personagem.
            </p>
        </div>

        <button
            type="button"
            @click="openCreate()"
            class="character-features-v1-add"
        >
            <span class="text-[16px] leading-none">+</span>
            Nova Habilidade
        </button>
    </div>


    {{-- ============================================================
         LISTA
    ============================================================= --}}
    <div
        x-show="visibleFeatures.length > 0"
        x-cloak
        class="character-features-v1-grid"
    >
        <template
            x-for="feature in visibleFeatures"
            :key="'character-feature-' + feature.id"
        >
            <article class="character-feature-v1-card">
                <div class="character-feature-v1-card-head">
                    <div class="character-feature-v1-main">
                        <div class="character-feature-v1-name-row">
                            <h3
                                class="character-feature-v1-name"
                                x-text="feature.name"
                            ></h3>

                            <span
                                x-show="feature.data?.quick_text"
                                x-cloak
                                class="character-feature-v1-quick"
                                x-text="feature.data.quick_text"
                            ></span>
                        </div>

                        <div class="character-feature-v1-meta">
                            <template
                                x-for="(part, index) in featureMeta(feature)"
                                :key="'meta-' + feature.id + '-' + index"
                            >
                                <span x-text="part"></span>
                            </template>
                        </div>
                    </div>

                    <div class="character-feature-v1-actions">
                        <template x-if="feature.uses_max !== null">
                            <div class="flex items-center gap-5">
                                <div
                                    class="character-feature-v1-tracker"
                                    @click.stop
                                >
                                    <button
                                        type="button"
                                        @click="changeUses(feature, -1)"
                                        :disabled="
                                            busyUsesId !== null
                                            || feature.uses_current <= 0
                                        "
                                        title="Diminuir"
                                    >
                                        −
                                    </button>

                                    <strong
                                        x-text="
                                            feature.uses_current
                                            + ' / '
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
                                        title="Aumentar"
                                    >
                                        +
                                    </button>
                                </div>

                                <span
                                    class="character-feature-v1-recovery"
                                    x-text="recoveryLabel(feature)"
                                ></span>
                            </div>
                        </template>

                        <button
                            type="button"
                            @click="openEdit(feature)"
                            class="character-feature-v1-edit"
                            title="Editar Habilidade"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    d="M4 20h4l11-11-4-4L4 16v4Z"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="m13.5 6.5 4 4"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <p
                    x-show="feature.description"
                    x-cloak
                    class="character-feature-v1-description"
                    x-text="feature.description"
                ></p>
            </article>
        </template>
    </div>


    {{-- ============================================================
         VAZIO
    ============================================================= --}}
    <div
        x-show="visibleFeatures.length === 0"
        x-cloak
        class="character-features-v1-empty"
    >
        <strong>Nenhuma habilidade cadastrada</strong>

        <p>
            Adicione características de classe, habilidades especiais ou conteúdo personalizado.
        </p>
    </div>


    {{-- ============================================================
         EDITOR
    ============================================================= --}}
    <template x-teleport="body">
        <div
            x-show="modalOpen"
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
                class="feature-editor-v1-backdrop"
                @click="closeModal()"
            ></div>

            <section
                x-show="modalOpen"
                x-transition.opacity.duration.120ms
                class="feature-editor-v1"
                @click.stop
            >
                {{-- HEADER --}}
                <header class="feature-editor-v1-header">
                    <div>
                        <small
                            x-text="
                                editingId === null
                                    ? 'Nova Habilidade'
                                    : 'Editar Habilidade'
                            "
                        ></small>

                        <h3
                            x-text="
                                form?.name
                                || 'Habilidade'
                            "
                        ></h3>
                    </div>

                    <button
                        type="button"
                        @click="closeModal()"
                        class="feature-editor-v1-close"
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


                {{-- CORPO --}}
                <div
                    x-show="form"
                    class="feature-editor-v1-body"
                >
                    {{-- IDENTIDADE --}}
                    <section class="feature-editor-v1-section">
                        <label>
                            <span class="feature-editor-v1-label">
                                Nome
                            </span>

                            <input
                                type="text"
                                maxlength="120"
                                x-model="form.name"
                                placeholder="Ex.: Segundo Fôlego"
                                class="feature-editor-v1-input"
                            >
                        </label>

                        <div
                            class="
                                feature-editor-v1-grid
                                feature-editor-v1-grid-source

                                mt-3
                            "
                        >
                            <label>
                                <span class="feature-editor-v1-label">
                                    Origem
                                </span>

                                <input
                                    type="text"
                                    maxlength="100"
                                    x-model="form.source"
                                    placeholder="Ex.: Guerreiro"
                                    class="feature-editor-v1-input"
                                >
                            </label>

                            <label>
                                <span class="feature-editor-v1-label">
                                    Nível adquirido
                                </span>

                                <input
                                    type="number"
                                    min="1"
                                    max="255"
                                    x-model.number="form.level_acquired"
                                    placeholder="1"
                                    class="feature-editor-v1-input"
                                >
                            </label>
                        </div>
                    </section>


                    {{-- ATIVAÇÃO --}}
                    <section class="feature-editor-v1-section">
                        <span class="feature-editor-v1-label">
                            Ativação
                        </span>

                        <div class="feature-editor-v1-choice-grid">
                            <button
                                type="button"
                                @click="setActivation('passive')"
                                class="feature-editor-v1-choice"
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
                                class="feature-editor-v1-choice"
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
                                class="feature-editor-v1-choice"
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
                                class="feature-editor-v1-choice"
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
                                class="feature-editor-v1-choice"
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
                    <section class="feature-editor-v1-section">
                        <label>
                            <span class="feature-editor-v1-label">
                                Informação rápida
                            </span>

                            <input
                                type="text"
                                maxlength="180"
                                x-model="form.data.quick_text"
                                placeholder="Ex.: 1d10+2 PV"
                                class="feature-editor-v1-input"
                            >

                            <p class="feature-editor-v1-help">
                                Uma informação curta que aparecerá ao lado do nome.
                            </p>
                        </label>

                        <label class="mt-4 block">
                            <span class="feature-editor-v1-label">
                                Descrição
                            </span>

                            <textarea
                                maxlength="30000"
                                x-model="form.description"
                                placeholder="Descreva o funcionamento da habilidade..."
                                class="feature-editor-v1-textarea"
                            ></textarea>
                        </label>
                    </section>


                    {{-- RASTREADOR --}}
                    <section class="feature-editor-v1-section">
                        <span class="feature-editor-v1-label">
                            Rastreador
                        </span>

                        <div
                            class="
                                grid
                                max-w-sm
                                grid-cols-2
                                gap-7
                            "
                        >
                            <button
                                type="button"
                                @click="setCounterEnabled(false)"
                                class="feature-editor-v1-choice"
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
                                class="feature-editor-v1-choice"
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
                            class="feature-editor-v1-counter"
                        >
                            <div class="feature-editor-v1-counter-grid">
                                <label>
                                    <span class="feature-editor-v1-label">
                                        Atual
                                    </span>

                                    <input
                                        type="number"
                                        min="0"
                                        x-model.number="form.uses_current"
                                        @change="clampCounter()"
                                        class="feature-editor-v1-input"
                                    >
                                </label>

                                <label>
                                    <span class="feature-editor-v1-label">
                                        Máximo
                                    </span>

                                    <input
                                        type="number"
                                        min="1"
                                        x-model.number="form.uses_max"
                                        @change="clampCounter()"
                                        class="feature-editor-v1-input"
                                    >
                                </label>

                                <label>
                                    <span class="feature-editor-v1-label">
                                        Recuperação
                                    </span>

                                    <select
                                        x-model="form.recovery"
                                        class="feature-editor-v1-select"
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
                                <span class="feature-editor-v1-label">
                                    Recuperação personalizada
                                </span>

                                <input
                                    type="text"
                                    maxlength="120"
                                    x-model="form.data.recovery_custom"
                                    placeholder="Ex.: Após completar um ritual"
                                    class="feature-editor-v1-input"
                                >
                            </label>

                            <div class="feature-editor-v1-mode">
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

                            <p class="feature-editor-v1-help">
                                “Gastar usos” normalmente começa cheio. “Acumular” normalmente começa em zero.
                            </p>
                        </div>
                    </section>


                    {{-- ERRO --}}
                    <div
                        x-show="saveError"
                        x-cloak
                        class="feature-editor-v1-error"
                        x-text="saveError"
                    ></div>
                </div>


                {{-- FOOTER --}}
                <footer class="feature-editor-v1-footer">
                    <div>
                        <template x-if="editingId !== null">
                            <div>
                                <button
                                    x-show="!deleteConfirmOpen"
                                    type="button"
                                    @click="deleteConfirmOpen = true"
                                    :disabled="saving"
                                    class="feature-editor-v1-danger"
                                >
                                    Excluir
                                </button>

                                <div
                                    x-show="deleteConfirmOpen"
                                    x-cloak
                                    class="flex items-center gap-7"
                                >
                                    <span class="text-[11px] font-bold text-red-800">
                                        Excluir esta habilidade?
                                    </span>

                                    <button
                                        type="button"
                                        @click="deleteConfirmOpen = false"
                                        :disabled="saving"
                                        class="feature-editor-v1-secondary"
                                    >
                                        Não
                                    </button>

                                    <button
                                        type="button"
                                        @click="deleteFeature()"
                                        :disabled="saving"
                                        class="feature-editor-v1-danger"
                                    >
                                        Sim, excluir
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="feature-editor-v1-footer-right">
                        <button
                            type="button"
                            @click="closeModal()"
                            :disabled="saving"
                            class="feature-editor-v1-secondary"
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="saveFeature()"
                            :disabled="saving"
                            class="feature-editor-v1-save"
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