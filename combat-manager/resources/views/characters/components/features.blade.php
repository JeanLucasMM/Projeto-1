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

    /*
     * Os modais usam x-teleport="body", então deixam de herdar as
     * variáveis visuais da ficha. Reaplicamos o tema nos portais.
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
            [x-cloak] {
                display: none !important;
            }

            /*
            |--------------------------------------------------------------------------
            | HABILIDADES — V4 UNIFICADO
            |--------------------------------------------------------------------------
            |
            | O show fornece a folha.
            | Este componente passa a funcionar como conteúdo editorial
            | dentro dela, com duas listas leves e sem a sensação de
            | "caixas dentro de caixas".
            |
            */

            .character-features-v3 {
                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO
            |--------------------------------------------------------------------------
            */

            .character-features-v3-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;

                margin-bottom: 10px;

                border-bottom:
                    1px solid
                    rgba(205,187,159,.28);

                padding:
                    0 2px 9px;
            }

            .character-features-v3-heading {
                min-width: 0;
            }

            .character-features-v3-kicker {
                display: block;

                margin-bottom: 2px;

                font-size: 9px;
                font-weight: 900;
                letter-spacing: .12em;

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
                font-size: 19px;
                font-weight: 900;
                line-height: 1.05;

                color: #53150f;
            }

            .character-features-v3-count {
                display: inline-flex;

                min-width: 24px;
                height: 24px;

                align-items: center;
                justify-content: center;

                border-radius: 999px;

                background:
                    rgba(239,233,220,.86);

                padding: 0 7px;

                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;

                color: #8c6239;
            }

            .character-features-v3-add {
                display: inline-flex;

                min-height: 36px;

                flex: 0 0 auto;

                align-items: center;
                justify-content: center;

                gap: 7px;

                border-radius: 9px;

                background: #6b1d14;

                padding: 0 12px;

                font-size: 10px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                color: #faf8f2;

                transition:
                    background .14s ease,
                    transform .14s ease;
            }

            .character-features-v3-add:hover {
                background: #53150f;
            }

            .character-features-v3-add:active {
                transform: translateY(1px);
            }

            /*
            |--------------------------------------------------------------------------
            | DUAS LISTAS
            |--------------------------------------------------------------------------
            */

            .character-features-v3-columns {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr);

                gap: 10px;

                margin-top: 2px;
            }

            .character-features-v3-column {
                min-width: 0;

                overflow: hidden;

                border-radius: 11px;

                background:
                    rgba(255,253,249,.32);

                padding: 3px 7px;
            }

            .character-features-v3-column + .character-features-v3-column {
                border-top: 0;
            }

            @media (min-width: 900px) {
                .character-features-v3-columns {
                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));

                    gap: 12px;
                }

                .character-features-v3-column:first-child {
                    padding-right: 7px;
                }

                .character-features-v3-column + .character-features-v3-column {
                    border-left: 0;

                    padding-left: 7px;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | LINHA DE HABILIDADE
            |--------------------------------------------------------------------------
            */

            .character-feature-v3-row {
                position: relative;

                display: flex;

                min-height: 58px;

                align-items: center;

                gap: 10px;

                border-bottom:
                    1px solid
                    rgba(216,199,171,.30);

                border-radius: 9px;

                padding:
                    8px 8px 8px 10px;

                cursor: pointer;

                transition:
                    background .12s ease,
                    box-shadow .12s ease;
            }

            .character-feature-v3-row:last-of-type {
                border-bottom-color:
                    transparent;
            }

            .character-feature-v3-row::before {
                content: "";

                position: absolute;

                top: 13px;
                bottom: 13px;
                left: 0;

                width: 2px;

                border-radius: 999px;

                background:
                    rgba(140,98,57,.22);

                transition:
                    background .12s ease;
            }

            .character-feature-v3-row:hover {
                background:
                    rgba(239,233,220,.46);

                box-shadow:
                    inset 0 0 0 1px rgba(205,187,159,.18);
            }

            .character-feature-v3-row:hover::before {
                background: #6b1d14;
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
                line-height: 1.2;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-feature-v3-quick {
                display: block;

                margin-top: 4px;

                overflow: hidden;

                font-size: 12px;
                line-height: 1.35;

                color: #6f5544;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-feature-v3-no-quick {
                display: block;

                margin-top: 4px;

                font-size: 10.5px;
                font-style: italic;

                color: #a78f78;
            }

            .character-feature-v3-right {
                display: flex;

                flex: 0 0 auto;

                align-items: center;

                gap: 6px;
            }

            /*
            |--------------------------------------------------------------------------
            | RASTREADOR
            |--------------------------------------------------------------------------
            */

            .character-feature-v3-tracker {
                display: inline-flex;

                height: 30px;

                align-items: stretch;

                overflow: hidden;

                border:
                    1px solid
                    rgba(205,187,159,.68);

                border-radius: 8px;

                background:
                    rgba(250,248,242,.88);
            }

            .character-feature-v3-tracker button {
                display: flex;

                width: 27px;

                align-items: center;
                justify-content: center;

                background: transparent;

                font-size: 15px;
                font-weight: 800;

                color: #8c6239;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .character-feature-v3-tracker button:hover:not(:disabled) {
                background:
                    #efe9dc;

                color: #53150f;
            }

            .character-feature-v3-tracker button:disabled {
                cursor: default;
                opacity: .30;
            }

            .character-feature-v3-tracker strong {
                display: flex;

                min-width: 44px;

                align-items: center;
                justify-content: center;

                border-right:
                    1px solid
                    rgba(216,199,171,.46);

                border-left:
                    1px solid
                    rgba(216,199,171,.46);

                padding: 0 6px;

                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #53150f;
            }

            .character-feature-v3-chevron {
                width: 15px;
                height: 15px;

                flex: 0 0 15px;

                color:
                    rgba(140,98,57,.55);

                transition:
                    transform .12s ease,
                    color .12s ease;
            }

            .character-feature-v3-row:hover
            .character-feature-v3-chevron {
                transform: translateX(2px);
                color: #6b1d14;
            }

            /*
            |--------------------------------------------------------------------------
            | ESTADO VAZIO
            |--------------------------------------------------------------------------
            */

            .character-features-v3-empty-column {
                margin: 5px 0;

                border:
                    1px dashed
                    rgba(205,187,159,.50);

                border-radius: 9px;

                padding:
                    16px 10px;

                text-align: center;

                font-size: 11px;
                line-height: 1.4;

                color: #aa8b68;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAIS
            |--------------------------------------------------------------------------
            */

            .feature-v3-backdrop {
                position: absolute;
                inset: 0;

                background:
                    rgba(42,23,18,.48);

                backdrop-filter:
                    blur(2px);
            }

            .feature-v3-modal {
                position: relative;
                z-index: 1;

                display: flex;

                width:
                    min(
                        720px,
                        calc(100vw - 28px)
                    );

                max-height:
                    min(
                        88vh,
                        860px
                    );

                flex-direction: column;

                overflow: hidden;

                border:
                    1px solid
                    rgba(205,187,159,.90);

                border-radius: 16px;

                background: #faf8f2;

                box-shadow:
                    0 22px 64px
                    rgba(42,23,18,.22);
            }

            .feature-v3-modal-header {
                display: flex;

                flex: 0 0 auto;

                align-items: flex-start;
                justify-content: space-between;

                gap: 16px;

                border-bottom:
                    1px solid
                    rgba(205,187,159,.52);

                background:
                    #f4f1e8;

                padding:
                    16px 18px;
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

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .feature-v3-modal-close:hover {
                background: #e9e1d4;
                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | LEITURA DA HABILIDADE
            |--------------------------------------------------------------------------
            */

            .feature-v3-detail-body {
                min-height: 0;
                flex: 1;

                overflow-y: auto;

                padding:
                    18px 20px 20px;
            }

            .feature-v3-detail-meta {
                display: flex;

                flex-wrap: wrap;

                gap: 6px;

                margin-bottom: 15px;
            }

            .feature-v3-detail-pill {
                display: inline-flex;

                min-height: 27px;

                align-items: center;

                border-radius: 999px;

                background:
                    rgba(239,233,220,.82);

                padding:
                    0 9px;

                font-size: 10.5px;
                font-weight: 800;

                color: #6f5544;
            }

            .feature-v3-detail-quick {
                margin-bottom: 17px;

                border-left:
                    3px solid
                    #8c6239;

                border-radius:
                    0 8px 8px 0;

                background:
                    rgba(239,233,220,.48);

                padding:
                    10px 12px;

                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;

                color: #53150f;
            }

            .feature-v3-detail-description {
                white-space: pre-line;

                font-size: 14px;
                line-height: 1.68;

                color: #3f2d24;
            }

            .feature-v3-detail-tracker {
                display: flex;

                flex-wrap: wrap;

                align-items: center;

                gap: 8px;

                margin-top: 18px;

                border-top:
                    1px solid
                    rgba(216,199,171,.40);

                padding-top: 14px;
            }

            .feature-v3-detail-footer {
                display: flex;

                align-items: center;
                justify-content: flex-end;

                gap: 8px;

                border-top:
                    1px solid
                    rgba(205,187,159,.50);

                background: #f4f1e8;

                padding:
                    12px 18px;
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

                padding:
                    17px 19px 20px;
            }

            .feature-editor-v3-section + .feature-editor-v3-section {
                margin-top: 18px;

                border-top:
                    1px solid
                    rgba(216,199,171,.38);

                padding-top: 17px;
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

                font-size: 11px;
                line-height: 1.45;

                color: #8c6e58;
            }

            .feature-editor-v3-input,
            .feature-editor-v3-select,
            .feature-editor-v3-textarea {
                width: 100%;

                border:
                    1px solid
                    rgba(205,187,159,.76);

                border-radius: 9px;

                background: #fffdf9;

                color: #2f211b;

                outline: none;

                transition:
                    border-color .14s ease,
                    box-shadow .14s ease;
            }

            .feature-editor-v3-input,
            .feature-editor-v3-select {
                min-height: 42px;

                padding:
                    0 11px;

                font-size: 14px;
            }

            .feature-editor-v3-textarea {
                min-height: 132px;

                resize: vertical;

                padding:
                    10px 11px;

                font-size: 14px;
                line-height: 1.55;
            }

            .feature-editor-v3-input:focus,
            .feature-editor-v3-select:focus,
            .feature-editor-v3-textarea:focus {
                border-color: #8c6239;

                box-shadow:
                    0 0 0 2px
                    rgba(140,98,57,.08);
            }

            .feature-editor-v3-grid {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr);

                gap: 12px;
            }

            @media (min-width: 640px) {
                .feature-editor-v3-grid-source {
                    grid-template-columns:
                        minmax(0, 1fr)
                        150px;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ESCOLHAS
            |--------------------------------------------------------------------------
            */

            .feature-editor-v3-choice-grid {
                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 7px;
            }

            @media (min-width: 640px) {
                .feature-editor-v3-choice-grid {
                    grid-template-columns:
                        repeat(
                            5,
                            minmax(0, 1fr)
                        );
                }
            }

            .feature-editor-v3-choice {
                min-height: 39px;

                border:
                    1px solid
                    rgba(205,187,159,.68);

                border-radius: 8px;

                background:
                    rgba(255,253,249,.86);

                padding:
                    6px 8px;

                font-size: 10.5px;
                font-weight: 900;

                color: #8c6239;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            .feature-editor-v3-choice:hover {
                background: #efe9dc;
            }

            .feature-editor-v3-choice.active {
                border-color: #6b1d14;
                background: #6b1d14;
                color: #faf8f2;
            }

            /*
            |--------------------------------------------------------------------------
            | POSIÇÃO
            |--------------------------------------------------------------------------
            */

            .feature-editor-v3-position {
                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 8px;
            }

            .feature-editor-v3-position button {
                min-height: 62px;

                border:
                    1px solid
                    rgba(205,187,159,.68);

                border-radius: 10px;

                background:
                    rgba(255,253,249,.86);

                padding:
                    9px 11px;

                text-align: left;

                color: #8c6239;

                transition:
                    border-color .12s ease,
                    background .12s ease;
            }

            .feature-editor-v3-position button:hover {
                background: #efe9dc;
            }

            .feature-editor-v3-position button.active {
                border-color: #6b1d14;

                background:
                    rgba(107,29,20,.07);

                color: #6b1d14;
            }

            .feature-editor-v3-position strong {
                display: block;

                font-size: 11.5px;
                font-weight: 900;
            }

            .feature-editor-v3-position span {
                display: block;

                margin-top: 3px;

                font-size: 10.5px;
                line-height: 1.35;

                color: #8c6e58;
            }

            /*
            |--------------------------------------------------------------------------
            | RASTREADOR NO EDITOR
            |--------------------------------------------------------------------------
            */

            .feature-editor-v3-counter {
                margin-top: 12px;

                border:
                    1px solid
                    rgba(205,187,159,.54);

                border-radius: 11px;

                background:
                    rgba(244,241,232,.74);

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
                min-height: 39px;

                border:
                    1px solid
                    rgba(205,187,159,.68);

                border-radius: 8px;

                background: #faf8f2;

                padding:
                    6px 9px;

                font-size: 10.5px;
                font-weight: 900;

                color: #8c6239;
            }

            .feature-editor-v3-mode button:hover {
                background: #efe9dc;
            }

            .feature-editor-v3-mode button.active {
                border-color: #6b1d14;

                background:
                    rgba(107,29,20,.07);

                color: #6b1d14;
            }

            /*
            |--------------------------------------------------------------------------
            | ERRO / FOOTER / BOTÕES
            |--------------------------------------------------------------------------
            */

            .feature-v3-error {
                margin-top: 14px;

                border-left:
                    3px solid
                    #991b1b;

                border-radius:
                    0 8px 8px 0;

                background: #fff1f2;

                padding:
                    9px 11px;

                font-size: 12px;
                line-height: 1.45;

                color: #991b1b;
            }

            .feature-v3-footer {
                display: flex;

                flex: 0 0 auto;

                align-items: center;
                justify-content: space-between;

                gap: 12px;

                border-top:
                    1px solid
                    rgba(205,187,159,.50);

                background: #f4f1e8;

                padding:
                    12px 18px;
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

                padding:
                    0 13px;

                font-size: 10.5px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .feature-v3-secondary {
                border:
                    1px solid
                    rgba(205,187,159,.70);

                background: #faf8f2;

                color: #8c6239;
            }

            .feature-v3-secondary:hover {
                background: #e9e1d4;
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

            @media (max-width: 560px) {
                .character-features-v3-header {
                    align-items: flex-start;
                }

                .character-features-v3-add {
                    min-height: 34px;

                    padding:
                        0 10px;

                    font-size: 9px;
                }

                .character-feature-v3-row {
                    align-items: flex-start;
                }

                .character-feature-v3-right {
                    padding-top: 1px;
                }

                .character-feature-v3-tracker {
                    height: 28px;
                }

                .character-feature-v3-tracker button {
                    width: 24px;
                }

                .character-feature-v3-tracker strong {
                    min-width: 40px;
                }

                .feature-editor-v3-position {
                    grid-template-columns:
                        minmax(0, 1fr);
                }
            }
        


            /*
            |--------------------------------------------------------------------------
            | V5 — PALETA DO HEADER
            |--------------------------------------------------------------------------
            |
            | Habilidades ficam sobre o bege avermelhado do show.
            | Cada habilidade, por ser informação importante, recebe papel claro.
            |
            */

            .character-features-v3-header {
                border-bottom-color:
                    rgba(160,119,77,.30);
            }

            .character-features-v3-kicker {
                color:
                    #906541;
            }

            .character-features-v3-count {
                background:
                    #dfcdb8;

                color:
                    #7a5133;
            }

            .character-features-v3-columns {
                gap:
                    10px;
            }

            .character-features-v3-column {
                background:
                    transparent;

                padding:
                    2px 0;
            }

            /*
            |--------------------------------------------------------------------------
            | HABILIDADE
            |--------------------------------------------------------------------------
            */

            .character-feature-v3-row {
                min-height:
                    58px;

                margin-bottom:
                    6px;

                border:
                    1px solid
                    rgba(180,143,100,.30);

                border-radius:
                    8px;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf8 0%,
                        #f8f0e5 100%
                    );

                padding:
                    8px 8px 8px 11px;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.58);
            }

            .character-feature-v3-row:last-of-type {
                border-bottom-color:
                    rgba(180,143,100,.30);
            }

            .character-feature-v3-row::before {
                top:
                    10px;

                bottom:
                    10px;

                background:
                    rgba(107,29,20,.22);
            }

            .character-feature-v3-row:hover {
                background:
                    #fffaf3;

                box-shadow:
                    inset 0 0 0 1px rgba(107,29,20,.16);
            }

            .character-feature-v3-quick {
                color:
                    #745743;
            }

            .character-feature-v3-no-quick {
                color:
                    #9b8069;
            }

            /*
            |--------------------------------------------------------------------------
            | RASTREADOR
            |--------------------------------------------------------------------------
            */

            .character-feature-v3-tracker {
                border-color:
                    rgba(175,139,96,.44);

                background:
                    #f6eee3;
            }

            .character-feature-v3-tracker strong {
                border-color:
                    rgba(188,154,111,.34);

                background:
                    rgba(255,253,248,.72);
            }

            .character-feature-v3-tracker button:hover:not(:disabled) {
                background:
                    #eadbc8;
            }

            .character-features-v3-empty-column {
                border-color:
                    rgba(175,139,96,.40);

                background:
                    rgba(255,253,248,.28);

                color:
                    #906f55;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAIS
            |--------------------------------------------------------------------------
            */

            .feature-v3-modal {
                border-color:
                    rgba(176,140,98,.68);

                background:
                    #fbf8f1;
            }

            .feature-v3-modal-header,
            .feature-v3-detail-footer,
            .feature-v3-footer {
                background:
                    #eadbc8;

                border-color:
                    rgba(160,119,77,.32);
            }

            .feature-v3-detail-pill {
                background:
                    #f0e4d5;
            }

            .feature-v3-detail-quick {
                background:
                    #f5eadc;
            }

            .feature-editor-v3-input,
            .feature-editor-v3-select,
            .feature-editor-v3-textarea {
                border-color:
                    rgba(181,145,103,.54);

                background:
                    #fffdf8;
            }

            .feature-editor-v3-counter {
                border-color:
                    rgba(181,145,103,.40);

                background:
                    #f0e3d3;
            }
        
            /*
            |--------------------------------------------------------------------------
            | V6 — HABILIDADES EDITORIAIS / LEITURA DIRETA NA FICHA
            |--------------------------------------------------------------------------
            |
            | Direção:
            | - mesma família cromática do header e dos ataques;
            | - fundo da seção em bege suave;
            | - conteúdo importante em papel quase branco;
            | - nome e descrição legíveis sem abrir modal;
            | - modal continua existindo para leitura completa e edição;
            | - tracker permanece operacional diretamente na ficha.
            |
            */

            .character-features-v6 {
                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO
            |--------------------------------------------------------------------------
            */

            .character-features-v6 .character-features-v3-header {
                margin-bottom: 12px;

                border-bottom:
                    1px solid
                    rgba(166,126,83,.30);

                padding:
                    0 1px 10px;
            }

            .character-features-v6 .character-features-v3-kicker {
                font-size: 8.5px;
                letter-spacing: .13em;
                color: #8c6239;
            }

            .character-features-v6 .character-features-v3-title-row h2 {
                font-size: 20px;
                line-height: 1;
            }

            .character-features-v6 .character-features-v3-count {
                min-width: 23px;
                height: 23px;

                border:
                    1px solid
                    rgba(166,126,83,.16);

                background: #eadbc8;

                color: #7c5335;
            }

            .character-features-v6 .character-features-v3-add {
                min-height: 36px;

                border:
                    1px solid
                    rgba(83,21,15,.16);

                border-radius: 8px;

                padding:
                    0 12px;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.08);
            }

            /*
            |--------------------------------------------------------------------------
            | COLUNAS
            |--------------------------------------------------------------------------
            */

            .character-features-v6 .character-features-v3-columns {
                align-items: start;

                gap: 12px;

                margin-top: 0;
            }

            .character-features-v6 .character-features-v3-column {
                display: flex;

                min-width: 0;

                flex-direction: column;

                gap: 8px;

                overflow: visible;

                border-radius: 0;

                background: transparent;

                padding: 0;
            }

            /*
            |--------------------------------------------------------------------------
            | CARD EDITORIAL
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-card {
                position: relative;

                overflow: hidden;

                border:
                    1px solid
                    rgba(177,138,96,.34);

                border-radius: 9px;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf8 0%,
                        #faf4eb 100%
                    );

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.72),
                    0 1px 2px rgba(83,21,15,.022);

                transition:
                    border-color .14s ease,
                    box-shadow .14s ease,
                    background .14s ease;
            }

            .character-feature-v6-card::before {
                content: '';

                position: absolute;

                top: 10px;
                bottom: 10px;
                left: 0;

                width: 3px;

                border-radius:
                    0 999px 999px 0;

                background:
                    rgba(107,29,20,.26);
            }

            .character-feature-v6-card:hover {
                border-color:
                    rgba(140,98,57,.48);

                background:
                    #fffaf3;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.78),
                    0 2px 6px rgba(83,21,15,.035);
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO DO CARD
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-head {
                display: flex;

                align-items: flex-start;
                justify-content: space-between;

                gap: 10px;

                padding:
                    10px 10px 7px 12px;
            }

            .character-feature-v6-heading {
                min-width: 0;
                flex: 1;
            }

            .character-feature-v6-name {
                display: block;

                width: fit-content;
                max-width: 100%;

                padding: 0;

                text-align: left;

                font-family: Georgia, serif;
                font-size: 15.5px;
                font-weight: 900;
                line-height: 1.22;

                color: #53150f;

                transition:
                    color .12s ease;
            }

            .character-feature-v6-name:hover {
                color: #6b1d14;
            }

            /*
            |--------------------------------------------------------------------------
            | METADADOS
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-meta {
                display: flex;

                flex-wrap: wrap;

                align-items: center;

                gap:
                    4px 7px;

                margin-top: 5px;
            }

            .character-feature-v6-meta-item {
                position: relative;

                display: inline-flex;

                align-items: center;

                font-size: 9.5px;
                font-weight: 800;
                line-height: 1.2;

                color: #8a684f;
            }

            .character-feature-v6-meta-item + .character-feature-v6-meta-item::before {
                content: '•';

                margin-right: 7px;

                color:
                    rgba(140,98,57,.45);
            }

            /*
            |--------------------------------------------------------------------------
            | CONTEÚDO DIRETO
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-body {
                padding:
                    0 12px 9px;
            }

            .character-feature-v6-quick {
                margin-bottom: 7px;

                border-left:
                    2px solid
                    rgba(140,98,57,.50);

                background:
                    rgba(235,219,199,.42);

                padding:
                    5px 8px;

                font-size: 11.5px;
                font-weight: 800;
                line-height: 1.4;

                color: #6d432d;
            }

            .character-feature-v6-description {
                display: -webkit-box;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;

                white-space: pre-line;

                font-size: 12px;
                line-height: 1.48;

                color: #5f4636;
            }

            .character-feature-v6-description.no-quick {
                -webkit-line-clamp: 4;
            }

            .character-feature-v6-empty-description {
                font-size: 11px;
                font-style: italic;
                line-height: 1.4;

                color: #9a806b;
            }

            /*
            |--------------------------------------------------------------------------
            | FOOTER DO CARD
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-footer {
                display: flex;

                min-height: 38px;

                align-items: center;
                justify-content: space-between;

                gap: 8px;

                border-top:
                    1px solid
                    rgba(184,149,106,.24);

                background:
                    rgba(238,225,208,.34);

                padding:
                    6px 8px 6px 12px;
            }

            .character-feature-v6-footer-left {
                display: flex;

                min-width: 0;

                flex-wrap: wrap;

                align-items: center;

                gap: 6px;
            }

            .character-feature-v6-recovery {
                display: inline-flex;

                min-height: 24px;

                align-items: center;

                border:
                    1px solid
                    rgba(177,138,96,.30);

                border-radius: 999px;

                background:
                    rgba(255,253,248,.64);

                padding:
                    0 8px;

                font-size: 9px;
                font-weight: 900;

                color: #805b40;
            }

            .character-feature-v6-detail-button {
                display: inline-flex;

                min-height: 28px;

                flex: 0 0 auto;

                align-items: center;
                justify-content: center;

                gap: 4px;

                border-radius: 7px;

                padding:
                    0 7px;

                font-size: 9.5px;
                font-weight: 900;

                color: #8c6239;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .character-feature-v6-detail-button:hover {
                background: #eadbc8;
                color: #53150f;
            }

            .character-feature-v6-detail-button svg {
                width: 13px;
                height: 13px;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKER
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-tracker {
                display: inline-flex;

                height: 30px;

                flex: 0 0 auto;

                align-items: stretch;

                overflow: hidden;

                border:
                    1px solid
                    rgba(172,133,91,.42);

                border-radius: 7px;

                background:
                    #f6eee3;
            }

            .character-feature-v6-tracker button {
                display: flex;

                width: 26px;

                align-items: center;
                justify-content: center;

                background: transparent;

                font-size: 14px;
                font-weight: 900;

                color: #8c6239;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .character-feature-v6-tracker button:hover:not(:disabled) {
                background: #eadbc8;
                color: #53150f;
            }

            .character-feature-v6-tracker button:disabled {
                opacity: .28;
            }

            .character-feature-v6-tracker strong {
                display: flex;

                min-width: 43px;

                align-items: center;
                justify-content: center;

                border-right:
                    1px solid
                    rgba(184,149,106,.30);

                border-left:
                    1px solid
                    rgba(184,149,106,.30);

                background:
                    rgba(255,253,248,.72);

                padding: 0 6px;

                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | ESTADO VAZIO
            |--------------------------------------------------------------------------
            */

            .character-features-v6 .character-features-v3-empty-column {
                margin: 0;

                border:
                    1px dashed
                    rgba(168,130,89,.36);

                border-radius: 8px;

                background:
                    rgba(255,253,248,.24);

                padding:
                    18px 12px;

                font-size: 11px;

                color: #8c705a;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL — MESMA PALETA, SEM MUDAR A LÓGICA
            |--------------------------------------------------------------------------
            */

            .character-features-v6 ~ template .feature-v3-modal,
            .feature-v3-modal {
                border-color:
                    rgba(174,135,94,.64);

                background:
                    #fbf8f1;
            }

            .feature-v3-modal-header,
            .feature-v3-detail-footer,
            .feature-v3-footer {
                background:
                    #eadbc8;

                border-color:
                    rgba(160,119,77,.30);
            }

            .feature-v3-detail-body,
            .feature-editor-v3-body {
                background:
                    #fbf8f1;
            }

            .feature-v3-detail-quick {
                background:
                    #f2e4d2;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 760px) {
                .character-feature-v6-head {
                    gap: 8px;
                }

                .character-feature-v6-name {
                    font-size: 15px;
                }

                .character-feature-v6-description {
                    -webkit-line-clamp: 4;
                }
            }

            @media (max-width: 520px) {
                .character-features-v6 .character-features-v3-header {
                    align-items: flex-start;
                }

                .character-feature-v6-head {
                    flex-direction: column;
                }

                .character-feature-v6-tracker {
                    align-self: flex-start;
                }

                .character-feature-v6-footer {
                    align-items: flex-start;
                }
            }

        


            /*
            |--------------------------------------------------------------------------
            | V7 — PALETA DO ATTACK
            |--------------------------------------------------------------------------
            |
            | O layout editorial V6 permanece intacto.
            | Ajustamos apenas cores, bordas e superfícies.
            |
            */

            .character-features-v6 .character-features-v3-header {
                border-bottom-color:
                    var(
                        --sheet-border,
                        rgba(176,140,98,.34)
                    );
            }

            .character-features-v6 .character-features-v3-kicker {
                color:
                    var(
                        --sheet-muted,
                        #7d604d
                    );
            }

            .character-features-v6 .character-features-v3-count {
                border-color:
                    rgba(160,119,77,.14);

                background:
                    var(
                        --sheet-band,
                        #eadbc8
                    );

                color:
                    var(
                        --sheet-label,
                        #6f472f
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | CARD
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-card {
                border-color:
                    var(
                        --sheet-border,
                        rgba(176,140,98,.34)
                    );

                background:
                    var(
                        --sheet-paper,
                        #fbf8f1
                    );

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.70),
                    0 1px 2px
                    rgba(83,21,15,.022);
            }

            .character-feature-v6-card::before {
                background:
                    rgba(107,29,20,.22);
            }

            .character-feature-v6-card:hover {
                border-color:
                    rgba(140,98,57,.44);

                background:
                    var(
                        --sheet-paper-strong,
                        #fffdf8
                    );
            }

            .character-feature-v6-name {
                color:
                    var(
                        --sheet-heading,
                        #53150f
                    );
            }

            .character-feature-v6-meta-item {
                color:
                    var(
                        --sheet-muted,
                        #7d604d
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | INFORMAÇÃO RÁPIDA / DESCRIÇÃO
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-quick {
                border-left-color:
                    rgba(160,119,77,.48);

                background:
                    var(
                        --sheet-band-soft,
                        #f0e4d5
                    );

                color:
                    var(
                        --sheet-label,
                        #6f472f
                    );
            }

            .character-feature-v6-description {
                color:
                    var(
                        --sheet-text,
                        #432c21
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-footer {
                border-top-color:
                    rgba(188,154,111,.26);

                background:
                    var(
                        --sheet-paper-alt,
                        #f7f0e6
                    );
            }

            .character-feature-v6-recovery {
                border-color:
                    rgba(176,140,98,.30);

                background:
                    rgba(255,253,248,.72);

                color:
                    var(
                        --sheet-muted,
                        #7d604d
                    );
            }

            .character-feature-v6-detail-button {
                color:
                    #8c6239;
            }

            .character-feature-v6-detail-button:hover {
                background:
                    var(
                        --sheet-band,
                        #eadbc8
                    );

                color:
                    var(
                        --sheet-heading,
                        #53150f
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKER
            |--------------------------------------------------------------------------
            */

            .character-feature-v6-tracker {
                border-color:
                    rgba(176,140,98,.38);

                background:
                    #f6eee3;
            }

            .character-feature-v6-tracker strong {
                border-color:
                    rgba(188,154,111,.30);

                background:
                    rgba(255,253,248,.80);
            }

            .character-feature-v6-tracker button:hover:not(:disabled) {
                background:
                    var(
                        --sheet-band,
                        #eadbc8
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | VAZIO
            |--------------------------------------------------------------------------
            */

            .character-features-v6 .character-features-v3-empty-column {
                border-color:
                    rgba(176,140,98,.34);

                background:
                    rgba(251,248,241,.54);

                color:
                    var(
                        --sheet-muted,
                        #7d604d
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | MODAIS
            |--------------------------------------------------------------------------
            */

            .feature-v3-modal {
                border-color:
                    rgba(176,140,98,.62) !important;

                background:
                    var(
                        --sheet-paper,
                        #fbf8f1
                    ) !important;
            }

            .feature-v3-modal-header,
            .feature-v3-detail-footer,
            .feature-v3-footer {
                background:
                    var(
                        --sheet-band,
                        #eadbc8
                    ) !important;

                border-color:
                    var(
                        --sheet-border-strong,
                        rgba(160,119,77,.36)
                    ) !important;
            }

            .feature-v3-detail-body,
            .feature-editor-v3-body {
                background:
                    var(
                        --sheet-paper,
                        #fbf8f1
                    ) !important;
            }

            .feature-v3-detail-quick {
                background:
                    var(
                        --sheet-band-soft,
                        #f0e4d5
                    ) !important;
            }

            .feature-editor-v3-input,
            .feature-editor-v3-select,
            .feature-editor-v3-textarea {
                border-color:
                    rgba(176,140,98,.48) !important;

                background:
                    var(
                        --sheet-paper-strong,
                        #fffdf8
                    ) !important;
            }

            .feature-editor-v3-counter {
                border-color:
                    rgba(176,140,98,.38) !important;

                background:
                    var(
                        --sheet-paper-alt,
                        #f7f0e6
                    ) !important;
            }
        

            /*
            |--------------------------------------------------------------------------
            | V8 — ALINHAMENTO COM ATTACK / ALTURA UNIFORME
            |--------------------------------------------------------------------------
            |
            | Ajustes solicitados:
            | - mesma vibração visual de Attack;
            | - padding horizontal igual, inclusive na esquerda;
            | - botão de nova habilidade no título;
            | - cards com altura visual consistente.
            |
            */

            .character-features-v8 {
                padding: 0 5px;
            }

            .character-features-v8 .character-features-v3-header {
                min-height: 40px;

                align-items: flex-start;

                margin-bottom: 10px;

                border-bottom-color:
                    rgba(168,132,91,.32);

                padding:
                    1px 5px 8px;
            }

            .character-features-v8 .character-features-v3-heading {
                width: 100%;
            }

            .character-features-v8 .character-features-v3-kicker {
                margin-bottom: 4px;

                font-size: 9px;
                letter-spacing: .12em;

                color: #8c6239;
            }

            .character-features-v8 .character-features-v3-title-row {
                display: flex;

                align-items: center;
                flex-wrap: wrap;

                gap: 8px;
            }

            .character-features-v8 .character-features-v3-title-row h2 {
                font-size: 20px;
                line-height: 1;
            }

            .character-features-v8 .character-features-v3-count {
                min-width: 24px;
                height: 24px;
            }

            .character-features-v8 .character-features-v3-add-inline {
                min-height: 30px;

                margin-left: 4px;

                gap: 6px;

                border:
                    1px solid
                    rgba(83,21,15,.12);

                border-radius: 999px;

                padding:
                    0 11px;

                font-size: 9.5px;
                letter-spacing: .04em;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.08);
            }

            .character-features-v8 .character-features-v3-columns {
                gap: 12px;

                margin-top: 0;
            }

            .character-features-v8 .character-features-v3-column {
                gap: 10px;
            }

            .character-features-v8 .character-feature-v6-card {
                display: flex;
                flex-direction: column;

                min-height: 178px;
                height: 178px;

                border-radius: 8px;
            }

            .character-features-v8 .character-feature-v6-card::before {
                top: 11px;
                bottom: 11px;
            }

            .character-features-v8 .character-feature-v6-head {
                gap: 12px;

                padding:
                    12px 12px 8px 14px;
            }

            .character-features-v8 .character-feature-v6-name {
                font-size: 15.5px;
                line-height: 1.18;
            }

            .character-features-v8 .character-feature-v6-meta {
                margin-top: 6px;
                gap: 4px 8px;
            }

            .character-features-v8 .character-feature-v6-body {
                display: flex;
                flex: 1;
                flex-direction: column;
                min-height: 0;

                padding:
                    0 14px 10px;
            }

            .character-features-v8 .character-feature-v6-quick {
                display: -webkit-box;

                min-height: 42px;

                margin-bottom: 8px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                padding:
                    7px 9px;

                font-size: 11.5px;
                line-height: 1.45;
            }

            .character-features-v8 .character-feature-v6-description {
                display: -webkit-box;
                flex: 1;
                min-height: 0;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;

                font-size: 12.5px;
                line-height: 1.52;
            }

            .character-features-v8 .character-feature-v6-description.no-quick {
                -webkit-line-clamp: 4;
            }

            .character-features-v8 .character-feature-v6-empty-description {
                margin-top: auto;
            }

            .character-features-v8 .character-feature-v6-footer {
                min-height: 40px;

                padding:
                    7px 10px 7px 14px;
            }

            .character-features-v8 .character-feature-v6-recovery {
                min-height: 25px;
                padding: 0 9px;
            }

            .character-features-v8 .character-feature-v6-detail-button {
                min-height: 29px;
                padding: 0 8px;
            }

            .character-features-v8 .character-feature-v6-tracker {
                height: 31px;
            }

            .character-features-v8 .character-feature-v6-tracker button {
                width: 27px;
            }

            .character-features-v8 .character-feature-v6-tracker strong {
                min-width: 44px;
            }

            .character-features-v8 .character-features-v3-empty-column {
                margin: 0;
                min-height: 178px;

                display: flex;
                align-items: center;
                justify-content: center;
            }

            @media (max-width: 899px) {
                .character-features-v8 {
                    padding: 0 4px;
                }

                .character-features-v8 .character-feature-v6-card,
                .character-features-v8 .character-features-v3-empty-column {
                    min-height: 170px;
                    height: auto;
                }
            }

            @media (max-width: 640px) {
                .character-features-v8 .character-features-v3-header {
                    padding: 1px 2px 8px;
                }

                .character-features-v8 .character-features-v3-title-row {
                    align-items: flex-start;
                }

                .character-features-v8 .character-features-v3-add-inline {
                    margin-left: 0;
                }

                .character-features-v8 .character-feature-v6-card,
                .character-features-v8 .character-features-v3-empty-column {
                    min-height: 164px;
                    height: auto;
                }
            }

        

            /*
            |--------------------------------------------------------------------------
            | V9 — MESMA LINHA DO ATTACK / RESUMO ATÉ INFORMAÇÃO RÁPIDA
            |--------------------------------------------------------------------------
            */

            .character-features-v9 {
                width: 100%;
                max-width: 820px;
                margin-inline: auto;
                padding: 0;
            }

            /*
            | Cabeçalho com a mesma geometria do Attack.
            | O próprio texto Habilidades cria uma nova feature.
            */
            .character-features-v9 .character-features-v9-header {
                display: flex;
                min-height: 40px;
                align-items: center;
                gap: 10px;
                margin-bottom: 2px;
                border-bottom: 1px solid rgba(168,132,91,.32);
                padding: 1px 5px 8px;
            }

            .character-features-v9-title {
                display: inline-flex;
                min-width: 0;
                align-items: center;
                gap: 8px;
                border-radius: 8px;
                padding: 4px 5px;
                text-align: left;
                transition: background .14s ease;
            }

            .character-features-v9-title:hover {
                background: rgba(255,252,246,.52);
            }

            .character-features-v9-title:focus-visible {
                outline: 2px solid rgba(107,29,20,.22);
                outline-offset: 2px;
            }

            .character-features-v9-title-text {
                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
                line-height: 1;
                color: #53150f;
            }

            .character-features-v9 .character-features-v3-count {
                min-width: 24px;
                height: 23px;
                border: 0;
                border-radius: 999px;
                background: #eadbc8;
                padding: 0 7px;
                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;
                color: #7e5735;
            }

            /*
            | As duas colunas começam exatamente dentro da mesma largura
            | máxima de 820px usada pelo Attack.
            */
            .character-features-v9 .character-features-v9-columns {
                grid-template-columns: minmax(0, 1fr);
                gap: 8px;
                margin-top: 8px;
                padding: 0;
            }

            .character-features-v9 .character-features-v9-column {
                display: flex;
                min-width: 0;
                flex-direction: column;
                gap: 8px;
                overflow: visible;
                padding: 0;
                background: transparent;
            }

            @media (min-width: 900px) {
                .character-features-v9 .character-features-v9-columns {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 12px;
                }

                .character-features-v9 .character-features-v9-column:first-child,
                .character-features-v9 .character-features-v9-column + .character-features-v9-column {
                    padding: 0;
                    border: 0;
                }
            }

            /*
            | Cards têm a mesma altura no desktop e mostram somente:
            | nome + metadados + informação rápida.
            */
            .character-feature-v9-card {
                position: relative;
                display: flex;
                height: 104px;
                min-height: 104px;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid rgba(176,140,98,.34);
                border-radius: 8px;
                background: #fbf8f1;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.70);
                cursor: pointer;
                transition: background .14s ease, border-color .14s ease;
            }

            .character-feature-v9-card::before {
                content: '';
                position: absolute;
                top: 10px;
                bottom: 10px;
                left: 0;
                width: 3px;
                border-radius: 0 999px 999px 0;
                background: rgba(107,29,20,.22);
            }

            .character-feature-v9-card:hover {
                border-color: rgba(140,98,57,.46);
                background: #fffdf8;
            }

            .character-feature-v9-card:focus-visible {
                outline: 2px solid rgba(107,29,20,.20);
                outline-offset: 2px;
            }

            .character-feature-v9-head {
                display: flex;
                min-width: 0;
                align-items: flex-start;
                justify-content: space-between;
                gap: 9px;
                padding: 10px 10px 6px 12px;
            }

            .character-feature-v9-heading {
                min-width: 0;
                flex: 1;
            }

            .character-feature-v9-name {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 1;
                font-family: Georgia, serif;
                font-size: 14.5px;
                font-weight: 900;
                line-height: 1.18;
                color: #53150f;
            }

            .character-feature-v9-meta {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 4px 7px;
                margin-top: 4px;
                overflow: hidden;
                white-space: nowrap;
            }

            .character-feature-v9-meta-item {
                position: relative;
                display: inline-flex;
                flex: 0 0 auto;
                align-items: center;
                font-size: 8.5px;
                font-weight: 800;
                line-height: 1.15;
                color: #7d604d;
            }

            .character-feature-v9-meta-item + .character-feature-v9-meta-item::before {
                content: '•';
                margin-right: 7px;
                color: rgba(140,98,57,.42);
            }

            .character-feature-v9-quick-wrap {
                min-height: 0;
                flex: 1;
                padding: 0 10px 10px 12px;
            }

            .character-feature-v9-quick,
            .character-feature-v9-quick-empty {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                min-height: 36px;
                border-left: 2px solid rgba(160,119,77,.42);
                background: #f0e4d5;
                padding: 5px 8px;
                font-size: 10.5px;
                line-height: 1.35;
                color: #6f472f;
            }

            .character-feature-v9-quick-empty {
                font-style: italic;
                color: #9a806b;
            }

            .character-feature-v9-tracker {
                display: inline-flex;
                height: 30px;
                flex: 0 0 auto;
                align-items: stretch;
                overflow: hidden;
                border: 1px solid rgba(176,140,98,.38);
                border-radius: 7px;
                background: #f6eee3;
            }

            .character-feature-v9-tracker button {
                display: flex;
                width: 26px;
                align-items: center;
                justify-content: center;
                background: transparent;
                font-size: 14px;
                font-weight: 900;
                color: #8c6239;
            }

            .character-feature-v9-tracker button:hover:not(:disabled) {
                background: #eadbc8;
                color: #53150f;
            }

            .character-feature-v9-tracker button:disabled {
                opacity: .28;
            }

            .character-feature-v9-tracker strong {
                display: flex;
                min-width: 43px;
                align-items: center;
                justify-content: center;
                border-right: 1px solid rgba(188,154,111,.30);
                border-left: 1px solid rgba(188,154,111,.30);
                background: rgba(255,253,248,.80);
                padding: 0 6px;
                font-family: Georgia, serif;
                font-size: 11.5px;
                font-weight: 900;
                color: #53150f;
            }

            .character-features-v9 .character-features-v9-empty {
                display: flex;
                height: 104px;
                min-height: 104px;
                align-items: center;
                justify-content: center;
                margin: 0;
                border-color: rgba(176,140,98,.34);
                background: rgba(251,248,241,.54);
                color: #7d604d;
            }

            @media (max-width: 899px) {
                .character-feature-v9-card,
                .character-features-v9 .character-features-v9-empty {
                    height: auto;
                    min-height: 104px;
                }
            }

            @media (max-width: 520px) {
                .character-features-v9 .character-features-v9-header {
                    padding-right: 2px;
                    padding-left: 2px;
                }

                .character-feature-v9-head {
                    gap: 7px;
                }

                .character-feature-v9-name {
                    -webkit-line-clamp: 2;
                }
            }

        

            /*
            |--------------------------------------------------------------------------
            | V10 — BARRA DE DIVISÃO ESQUERDA / DIREITA
            |--------------------------------------------------------------------------
            |
            | Replica a linguagem do cabeçalho da tabela de Ataques:
            | faixa bege clara, texto pequeno em caixa alta e divisão central.
            |
            */

            .character-features-v10 .character-features-v10-column-bar {
                display: none;
            }

            @media (min-width: 900px) {
                .character-features-v10 .character-features-v10-column-bar {
                    display: grid;

                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));

                    min-height: 27px;

                    align-items: center;

                    overflow: hidden;

                    margin-top: 2px;
                    margin-bottom: 8px;

                    border:
                        1px solid
                        rgba(176,140,98,.34);

                    border-radius:
                        8px 8px 0 0;

                    background:
                        #eadbc8;

                    box-shadow:
                        inset 0 1px 0
                        rgba(255,255,255,.60);
                }

                .character-features-v10 .character-features-v10-column-label {
                    display: flex;

                    min-width: 0;
                    height: 100%;

                    align-items: center;

                    padding:
                        0 10px;

                    font-size: 8.5px;
                    font-weight: 900;
                    line-height: 1;
                    letter-spacing: .10em;

                    text-transform: uppercase;

                    color: #6f472f;
                }

                .character-features-v10 .character-features-v10-column-label +
                .character-features-v10-column-label {
                    border-left:
                        1px solid
                        rgba(160,119,77,.30);
                }

                .character-features-v10 .character-features-v9-columns {
                    margin-top: 0;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | V11 — MESMA GEOMETRIA VISUAL DE ATTACK
            |--------------------------------------------------------------------------
            |
            | Attack permanece intocado.
            |
            | Features agora usa a mesma lógica:
            | - cabeçalho com a mesma largura/padding;
            | - uma única superfície clara;
            | - faixa bege como cabeçalho;
            | - linhas internas em vez de cards soltos;
            | - alturas consistentes.
            |
            */

            .character-features-v11 {
                width: 100%;
                max-width: 820px;
                margin-inline: auto;
                padding: 0;
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO
            |--------------------------------------------------------------------------
            */

            .character-features-v11 .character-features-v9-header {
                min-height: 40px;

                align-items: center;

                margin-bottom: 2px;

                border-bottom:
                    1px solid
                    rgba(168,132,91,.32);

                padding:
                    1px 5px 8px;
            }

            .character-features-v11 .character-features-v9-title {
                gap: 8px;

                border-radius: 8px;

                padding:
                    4px 5px;
            }

            .character-features-v11 .character-features-v9-title-text {
                font-size: 17px;
                line-height: 1;
            }

            .character-features-v11 .character-features-v3-count {
                min-width: 24px;
                height: 23px;

                background: #eadbc8;

                padding: 0 7px;

                font-size: 11px;

                color: #7e5735;
            }

            /*
            |--------------------------------------------------------------------------
            | SUPERFÍCIE ÚNICA
            |--------------------------------------------------------------------------
            */

            .character-features-v11-table-wrap {
                margin-top: 2px;

                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 8px;

                background: #fbf8f1;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.70);
            }

            /*
            |--------------------------------------------------------------------------
            | BARRA ESQUERDA / DIREITA
            |--------------------------------------------------------------------------
            */

            .character-features-v11 .character-features-v10-column-bar {
                margin: 0;

                min-height: 27px;

                border: 0;
                border-bottom:
                    1px solid
                    rgba(160,119,77,.36);

                border-radius: 0;

                background: #eadbc8;

                box-shadow: none;
            }

            .character-features-v11 .character-features-v10-column-label {
                padding:
                    0 10px;

                font-size: 8.5px;
                line-height: 1;
                letter-spacing: .10em;

                color: #6f472f;
            }

            .character-features-v11
            .character-features-v10-column-label +
            .character-features-v10-column-label {
                border-left:
                    1px solid
                    rgba(160,119,77,.28);
            }

            /*
            |--------------------------------------------------------------------------
            | DUAS COLUNAS
            |--------------------------------------------------------------------------
            */

            .character-features-v11 .character-features-v9-columns {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr);

                gap: 0;

                margin: 0;

                padding: 0;
            }

            .character-features-v11 .character-features-v9-column {
                display: flex;

                min-width: 0;

                flex-direction: column;

                gap: 0;

                padding: 0;

                background: transparent;
            }

            /*
            |--------------------------------------------------------------------------
            | LINHAS DE HABILIDADE
            |--------------------------------------------------------------------------
            */

            .character-features-v11 .character-feature-v9-card {
                display: flex;

                height: 100px;
                min-height: 100px;

                flex-direction: column;

                overflow: hidden;

                margin: 0;

                border: 0;
                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);

                border-radius: 0;

                background:
                    rgba(255,253,248,.82);

                box-shadow: none;

                transition:
                    background .14s ease;
            }

            .character-features-v11
            .character-features-v9-column
            .character-feature-v9-card:nth-of-type(even) {
                background:
                    rgba(247,240,230,.72);
            }

            .character-features-v11 .character-feature-v9-card::before {
                display: none;
            }

            .character-features-v11 .character-feature-v9-card:hover {
                border-color:
                    rgba(188,154,111,.28);

                background:
                    #f4e8d8;
            }

            /*
            | Última linha não precisa duplicar a borda externa.
            */
            .character-features-v11
            .character-features-v9-column
            > article:last-of-type {
                border-bottom: 0;
            }

            .character-features-v11 .character-feature-v9-head {
                gap: 9px;

                padding:
                    10px 10px 5px;
            }

            .character-features-v11 .character-feature-v9-name {
                font-size: 14px;
                line-height: 1.15;
            }

            .character-features-v11 .character-feature-v9-meta {
                gap:
                    4px 7px;

                margin-top: 3px;
            }

            .character-features-v11 .character-feature-v9-meta-item {
                font-size: 8.5px;
                line-height: 1.15;
            }

            /*
            |--------------------------------------------------------------------------
            | INFORMAÇÃO RÁPIDA
            |--------------------------------------------------------------------------
            */

            .character-features-v11 .character-feature-v9-quick-wrap {
                display: flex;

                min-height: 0;

                flex: 1;

                align-items: stretch;

                padding:
                    0 10px 9px;
            }

            .character-features-v11 .character-feature-v9-quick,
            .character-features-v11 .character-feature-v9-quick-empty {
                width: 100%;

                min-height: 35px;

                border-left:
                    2px solid
                    rgba(160,119,77,.42);

                background: #f0e4d5;

                padding:
                    5px 8px;

                font-size: 10.5px;
                line-height: 1.35;

                color: #6f472f;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKER
            |--------------------------------------------------------------------------
            */

            .character-features-v11 .character-feature-v9-tracker {
                height: 30px;

                border-color:
                    rgba(176,140,98,.38);

                border-radius: 7px;

                background: #f6eee3;
            }

            .character-features-v11
            .character-feature-v9-tracker
            button {
                width: 27px;
            }

            .character-features-v11
            .character-feature-v9-tracker
            strong {
                min-width: 43px;

                background:
                    rgba(255,253,248,.82);
            }

            /*
            |--------------------------------------------------------------------------
            | VAZIO
            |--------------------------------------------------------------------------
            */

            .character-features-v11 .character-features-v9-empty {
                display: flex;

                height: 100px;
                min-height: 100px;

                align-items: center;
                justify-content: center;

                margin: 0;

                border: 0;
                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);

                border-radius: 0;

                background:
                    rgba(255,253,248,.52);

                color: #7d604d;
            }

            /*
            |--------------------------------------------------------------------------
            | DESKTOP
            |--------------------------------------------------------------------------
            */

            @media (min-width: 900px) {
                .character-features-v11 .character-features-v9-columns {
                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));
                }

                .character-features-v11
                .character-features-v9-column +
                .character-features-v9-column {
                    border-left:
                        1px solid
                        rgba(160,119,77,.28);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 899px) {
                .character-features-v11-table-wrap {
                    border-radius: 8px;
                }

                .character-features-v11 .character-features-v10-column-bar {
                    display: none;
                }

                .character-features-v11
                .character-features-v9-column +
                .character-features-v9-column {
                    border-top:
                        1px solid
                        rgba(160,119,77,.28);
                }

                .character-features-v11 .character-feature-v9-card,
                .character-features-v11 .character-features-v9-empty {
                    height: auto;
                    min-height: 100px;
                }
            }

            @media (max-width: 520px) {
                .character-features-v11 .character-features-v9-header {
                    padding-right: 5px;
                    padding-left: 5px;
                }

                .character-features-v11 .character-feature-v9-name {
                    -webkit-line-clamp: 2;
                }
            }



            /*
            |--------------------------------------------------------------------------
            | V12 — ALTURA MÁXIMA / SCROLL INTERNO
            |--------------------------------------------------------------------------
            |
            | Mantém o cabeçalho e a faixa Esquerda / Direita sempre visíveis.
            | Somente o corpo das habilidades rola no desktop.
            | No mobile, evitamos nested scroll e deixamos a página crescer.
            |
            */

            .character-features-v12 .character-features-v11-table-wrap {
                border-radius: 10px;
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.72),
                    0 2px 8px rgba(83,21,15,.025);
            }

            .character-features-v12 .character-features-v10-column-bar {
                min-height: 29px;
                background:
                    linear-gradient(
                        180deg,
                        #eadbc8 0%,
                        #e5d2ba 100%
                    );
            }

            .character-features-v12-scroll {
                position: relative;
            }

            .character-features-v12 .character-feature-v9-card {
                transition:
                    background .14s ease,
                    box-shadow .14s ease;
            }

            .character-features-v12 .character-feature-v9-card:hover,
            .character-features-v12 .character-feature-v9-card:focus-within {
                background: #fffaf3;
                box-shadow:
                    inset 3px 0 0 rgba(107,29,20,.62);
            }

            .character-features-v12 .character-feature-v9-quick,
            .character-features-v12 .character-feature-v9-quick-empty {
                border-radius: 0 5px 5px 0;
                background:
                    rgba(234,219,200,.62);
            }

            @media (min-width: 900px) {
                .character-features-v12-scroll {
                    max-height: 400px;
                    overflow-x: hidden;
                    overflow-y: auto;

                    overscroll-behavior: contain;
                    scrollbar-gutter: stable;
                    scrollbar-width: thin;
                    scrollbar-color:
                        rgba(140,98,57,.46)
                        rgba(234,219,200,.20);
                }

                .character-features-v12-scroll::-webkit-scrollbar {
                    width: 8px;
                }

                .character-features-v12-scroll::-webkit-scrollbar-track {
                    background:
                        rgba(234,219,200,.20);
                }

                .character-features-v12-scroll::-webkit-scrollbar-thumb {
                    border:
                        2px solid
                        rgba(251,248,241,.92);

                    border-radius: 999px;
                    background:
                        rgba(140,98,57,.46);
                }

                .character-features-v12-scroll::-webkit-scrollbar-thumb:hover {
                    background:
                        rgba(107,29,20,.56);
                }
            }

            @media (max-width: 899px) {
                .character-features-v12-scroll {
                    max-height: none;
                    overflow: visible;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | V13 — LEGIBILIDADE / CONTRASTE
            |--------------------------------------------------------------------------
            |
            | Mantém a paleta Spellbound, mas separa melhor papel, texto e faixas.
            | Informação rápida inexistente não produz placeholder nem caixa vazia.
            |
            */

            .character-features-v12 .character-features-v9-title-text {
                color: #48150f;
            }

            .character-features-v12 .character-features-v3-count {
                background: #e8d6c0;
                color: #694328;
            }

            .character-features-v12 .character-features-v10-column-bar {
                border-bottom-color: rgba(130, 87, 52, .34);
                background:
                    linear-gradient(
                        180deg,
                        #ead7bf 0%,
                        #e3ccb1 100%
                    );
            }

            .character-features-v12 .character-features-v10-column-label {
                font-size: 9px;
                font-weight: 900;
                color: #5b3522;
            }

            .character-features-v12 .character-feature-v9-card {
                background: #fffdf8;
            }

            .character-features-v12
            .character-features-v9-column
            .character-feature-v9-card:nth-of-type(even) {
                background: #fbf6ee;
            }

            .character-features-v12 .character-feature-v9-card:hover,
            .character-features-v12 .character-feature-v9-card:focus-within {
                background: #fff9ef;
            }

            .character-features-v12 .character-feature-v9-name {
                font-size: 15px;
                line-height: 1.16;
                color: #45140f;
            }

            .character-features-v12 .character-feature-v9-meta {
                margin-top: 4px;
            }

            .character-features-v12 .character-feature-v9-meta-item {
                font-size: 9.25px;
                font-weight: 800;
                line-height: 1.18;
                color: #6e4d39;
            }

            .character-features-v12
            .character-feature-v9-meta-item
            + .character-feature-v9-meta-item::before {
                color: rgba(111, 71, 47, .48);
            }

            .character-features-v12 .character-feature-v9-quick-wrap {
                padding-bottom: 9px;
            }

            .character-features-v12 .character-feature-v9-quick {
                min-height: 35px;
                border-left-color: rgba(116, 73, 43, .66);
                border-radius: 0 5px 5px 0;
                background: #f1e3d0;
                padding: 6px 9px;
                font-size: 11.5px;
                font-weight: 500;
                line-height: 1.4;
                color: #4a3427;
            }

            .character-features-v12 .character-feature-v9-tracker {
                border-color: rgba(143, 99, 59, .46);
                background: #f7efe4;
            }

            .character-features-v12
            .character-feature-v9-tracker
            strong {
                font-size: 12.5px;
                color: #45140f;
            }

            .character-features-v12
            .character-feature-v9-tracker
            button {
                color: #7a5234;
            }

            @media (max-width: 899px) {
                .character-features-v12 .character-feature-v9-name {
                    font-size: 15px;
                }

                .character-features-v12 .character-feature-v9-meta-item {
                    font-size: 9px;
                }

                .character-features-v12 .character-feature-v9-quick {
                    font-size: 11.25px;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | V14 — MESMA PALETA DE TRAÇOS / TALENTOS
            |--------------------------------------------------------------------------
            |
            | Mantém toda a geometria e comportamento de Features.
            | Apenas aproxima as superfícies, textos e interações da paleta
            | usada em Traços de Espécie + Talentos.
            |
            */

            .character-features-v12 .character-features-v11-table-wrap {
                border-color: rgba(154, 111, 69, .42);
                background: #f3e7d8;
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.88),
                    0 2px 5px rgba(83,21,15,.035);
            }

            .character-features-v12 .character-features-v9-title-text {
                color: #48160f;
            }

            .character-features-v12 .character-features-v3-count {
                border-color: rgba(135, 91, 50, .24);
                background: #fffaf2;
                color: #6b3f25;
            }

            .character-features-v12 .character-features-v10-column-bar {
                border-bottom-color: rgba(143, 96, 53, .44);
                background:
                    linear-gradient(
                        180deg,
                        #ead8c1 0%,
                        #e2cdb3 100%
                    );
            }

            .character-features-v12 .character-features-v10-column-label {
                color: #5b3522;
            }

            .character-features-v12
            .character-features-v9-column
            + .character-features-v9-column {
                border-left-color: rgba(143, 96, 53, .30);
            }

            .character-features-v12 .character-feature-v9-card {
                border-bottom-color: rgba(169, 128, 83, .28);
                background: #fffdf8;
            }

            .character-features-v12
            .character-features-v9-column
            .character-feature-v9-card:nth-of-type(even) {
                background: #f8f0e6;
            }

            .character-features-v12 .character-feature-v9-card:hover,
            .character-features-v12 .character-feature-v9-card:focus-within {
                background: #f1dfcc;
                box-shadow:
                    inset 3px 0 0 #7f261d;
            }

            .character-features-v12 .character-feature-v9-name {
                color: #49170f;
            }

            .character-features-v12 .character-feature-v9-meta-item {
                color: #674936;
            }

            .character-features-v12
            .character-feature-v9-meta-item
            + .character-feature-v9-meta-item::before {
                color: rgba(103, 73, 54, .42);
            }

            .character-features-v12 .character-feature-v9-quick {
                border-left-color: #93623d;
                background: #eadbc8;
                color: #462e22;
            }

            .character-features-v12 .character-feature-v9-tracker {
                border-color: rgba(155, 111, 68, .38);
                background: #fffaf2;
            }

            .character-features-v12
            .character-feature-v9-tracker
            strong {
                border-color: rgba(155, 111, 68, .28);
                background: #fffdf8;
                color: #49170f;
            }

            .character-features-v12
            .character-feature-v9-tracker
            button {
                color: #754625;
            }

            .character-features-v12
            .character-feature-v9-tracker
            button:hover:not(:disabled) {
                background: #eadbc8;
                color: #53150f;
            }

            .character-features-v12 .character-features-v9-empty {
                background: rgba(255,253,248,.52);
                color: #745846;
            }

            @media (min-width: 900px) {
                .character-features-v12-scroll {
                    scrollbar-color:
                        rgba(126, 83, 47, .42)
                        rgba(234, 216, 194, .36);
                }

                .character-features-v12-scroll::-webkit-scrollbar-track {
                    background: rgba(234, 216, 194, .32);
                }

                .character-features-v12-scroll::-webkit-scrollbar-thumb {
                    border: 2px solid transparent;
                    background: rgba(126, 83, 47, .42);
                    background-clip: padding-box;
                }

                .character-features-v12-scroll::-webkit-scrollbar-thumb:hover {
                    background: rgba(107, 29, 20, .52);
                    background-clip: padding-box;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | V15 — THEME CORE / PADRÃO VISUAL UNIFICADO
            |--------------------------------------------------------------------------
            |
            | Esta camada final não altera a regra das habilidades.
            | Ela consolida a apresentação usando o mesmo contrato visual
            | de Ataques, Transformações e demais editores da ficha.
            |
            */

            /* ==============================================================
               MÓDULO PRINCIPAL
            ============================================================== */

            .character-sheet-theme[data-theme]
            .character-features-v15 {
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
                margin-inline: 0 !important;

                color:
                    var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v9-header {
                min-height: 42px;

                margin-bottom: 4px !important;

                border-bottom-color:
                    var(
                        --theme-divider-strong,
                        var(--theme-border)
                    ) !important;

                padding:
                    2px 5px 9px !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v9-title {
                border-radius: 8px;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v9-title:hover {
                background:
                    var(--theme-accent-softer) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v9-title-text {
                font-size: 18px !important;
                line-height: 1 !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v3-count {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               TABELA / DUAS COLUNAS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v11-table-wrap {
                overflow: hidden;

                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    var(--theme-module-shadow) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v10-column-bar {
                min-height: 29px !important;

                border-bottom-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-band-background) !important;

                box-shadow:
                    none !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v10-column-label {
                font-size: 9px !important;
                font-weight: 900;
                letter-spacing: .11em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v10-column-label
            + .character-features-v10-column-label {
                border-left-color:
                    var(--theme-border) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v9-column
            + .character-features-v9-column {
                border-left-color:
                    var(--theme-border) !important;
            }


            /* ==============================================================
               CARDS / LINHAS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-card,
            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v9-column
            .character-feature-v9-card:nth-of-type(even) {
                border-bottom-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-card-background) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    none !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-card:hover,
            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-card:focus-within {
                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    inset 3px 0 0
                    color-mix(
                        in srgb,
                        var(--theme-accent) 72%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-name {
                font-size: 14.5px !important;
                line-height: 1.17 !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-meta-item {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-meta-item
            + .character-feature-v9-meta-item::before {
                color:
                    var(--theme-divider-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-quick {
                border-left-color:
                    var(--theme-accent) !important;

                background:
                    var(--theme-surface-inset) !important;

                color:
                    var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-quick-empty {
                border-left-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-inset) !important;

                color:
                    var(--theme-text-secondary) !important;
            }


            /* ==============================================================
               TRACKER
            ============================================================== */

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-tracker,
            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v3-tracker {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-tracker strong,
            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v3-tracker strong {
                border-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-surface-card) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-tracker button,
            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v3-tracker button {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v9-tracker
            button:hover:not(:disabled),
            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-feature-v3-tracker
            button:hover:not(:disabled) {
                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               ESTADO VAZIO / SCROLL
            ============================================================== */

            .character-sheet-theme[data-theme]
            .character-features-v15
            .character-features-v9-empty {
                border-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-surface-card-alt) !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            @media (min-width: 900px) {
                .character-sheet-theme[data-theme]
                .character-features-v15
                .character-features-v12-scroll {
                    overflow-x: hidden !important;
                    overflow-y: auto !important;

                    scrollbar-width: none !important;
                    -ms-overflow-style: none !important;
                }

                .character-sheet-theme[data-theme]
                .character-features-v15
                .character-features-v12-scroll::-webkit-scrollbar {
                    width: 0 !important;
                    height: 0 !important;
                    display: none !important;
                }
            }


            /* ==============================================================
               MODAIS — CONTRATO GLOBAL
            ============================================================== */

            .character-sheet-theme[data-theme]
            .feature-v3-backdrop {
                background:
                    var(
                        --theme-modal-backdrop,
                        rgba(0,0,0,.82)
                    ) !important;

                -webkit-backdrop-filter:
                    blur(8px) !important;

                backdrop-filter:
                    blur(8px) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-modal {
                width:
                    min(
                        780px,
                        calc(100vw - 28px)
                    ) !important;

                max-height:
                    min(
                        92dvh,
                        900px
                    ) !important;

                border-color:
                    var(
                        --theme-modal-border,
                        var(--theme-border)
                    ) !important;

                border-radius:
                    18px !important;

                background:
                    var(
                        --theme-modal-surface,
                        var(--theme-paper-strong)
                    ) !important;

                color:
                    var(
                        --theme-modal-text,
                        var(--theme-text-primary)
                    ) !important;

                box-shadow:
                    var(
                        --theme-modal-shadow,
                        0 28px 80px rgba(0,0,0,.30)
                    ) !important;
            }

            @supports not (height: 1dvh) {
                .character-sheet-theme[data-theme]
                .feature-v3-modal {
                    max-height:
                        min(
                            92vh,
                            900px
                        ) !important;
                }
            }

            .character-sheet-theme[data-theme]
            .feature-v3-modal-header,
            .character-sheet-theme[data-theme]
            .feature-v3-detail-footer,
            .character-sheet-theme[data-theme]
            .feature-v3-footer {
                border-color:
                    var(
                        --theme-modal-border,
                        var(--theme-border)
                    ) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-modal-header {
                padding:
                    17px 19px !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-modal-kicker {
                margin-bottom:
                    4px !important;

                font-size:
                    9.5px !important;

                letter-spacing:
                    .14em !important;

                color:
                    var(
                        --theme-modal-label,
                        var(--theme-label)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-modal-title {
                font-size:
                    24px !important;

                line-height:
                    1.12 !important;

                color:
                    var(
                        --theme-modal-title,
                        var(--theme-text-strong)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-modal-close {
                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    var(
                        --theme-modal-control-shadow,
                        0 2px 7px rgba(0,0,0,.08)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-modal-close:hover {
                background:
                    var(--theme-card-hover) !important;

                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               MODAL — LEITURA
            ============================================================== */

            .character-sheet-theme[data-theme]
            .feature-v3-detail-body,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-body {
                min-height:
                    0 !important;

                background:
                    var(
                        --theme-modal-surface,
                        var(--theme-paper-strong)
                    ) !important;

                color:
                    var(--theme-text-primary) !important;

                scrollbar-width:
                    thin;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 35%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-detail-body {
                padding:
                    20px 21px 22px !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-detail-pill {
                min-height:
                    29px !important;

                border:
                    1px solid
                    var(--theme-border-soft);

                background:
                    var(--theme-accent-softer) !important;

                font-size:
                    11px !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-detail-quick {
                border-left-color:
                    var(--theme-accent) !important;

                background:
                    var(--theme-surface-inset) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-detail-description {
                font-size:
                    14px !important;

                line-height:
                    1.72 !important;

                color:
                    var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-detail-tracker {
                border-top-color:
                    var(--theme-divider) !important;
            }


            /* ==============================================================
               MODAL — EDITOR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .feature-editor-v3-body {
                padding:
                    18px 20px 21px !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-section
            + .feature-editor-v3-section {
                margin-top:
                    19px !important;

                border-top-color:
                    var(--theme-divider) !important;

                padding-top:
                    18px !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-label {
                margin-bottom:
                    7px !important;

                font-size:
                    11.5px !important;

                font-weight:
                    900 !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-help {
                font-size:
                    11.5px !important;

                line-height:
                    1.5 !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-input,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-select,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-textarea {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;

                box-shadow:
                    none !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-input,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-select {
                min-height:
                    43px !important;

                font-size:
                    14px !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-textarea {
                min-height:
                    142px !important;

                font-size:
                    14px !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-input::placeholder,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-textarea::placeholder {
                color:
                    var(--theme-input-placeholder) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-input:focus,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-select:focus,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-textarea:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 15%,
                        transparent
                    ) !important;
            }


            /* ==============================================================
               ESCOLHAS / POSIÇÃO / RASTREADOR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .feature-editor-v3-choice,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-position button,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-mode button {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-choice:hover,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-position button:hover,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-mode button:hover {
                background:
                    var(--theme-card-hover) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-choice.active {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;

                box-shadow:
                    0 3px 10px
                    color-mix(
                        in srgb,
                        var(--theme-accent) 15%,
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-position button.active,
            .character-sheet-theme[data-theme]
            .feature-editor-v3-mode button.active {
                border-color:
                    var(--theme-accent) !important;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-position strong {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-position span {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-editor-v3-counter {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-inset) !important;
            }


            /* ==============================================================
               FOOTER / BOTÕES
            ============================================================== */

            .character-sheet-theme[data-theme]
            .feature-v3-detail-footer,
            .character-sheet-theme[data-theme]
            .feature-v3-footer {
                position:
                    relative;

                flex-shrink:
                    0;

                padding:
                    12px 18px !important;

                box-shadow:
                    0 -8px 22px
                    color-mix(
                        in srgb,
                        var(--theme-accent-deep) 4%,
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .feature-v3-primary {
                border:
                    1px solid
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;

                box-shadow:
                    var(
                        --theme-modal-control-shadow,
                        0 2px 7px rgba(0,0,0,.08)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-primary:hover {
                background:
                    var(--theme-button-hover-background) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-secondary {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-secondary:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-danger {
                color:
                    #991b1b !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-danger:hover {
                background:
                    #fff1f2 !important;
            }

            .character-sheet-theme[data-theme]
            .feature-v3-error {
                border-left-color:
                    #991b1b !important;

                background:
                    #fff1f2 !important;

                color:
                    #991b1b !important;
            }


            /* ==============================================================
               DARK THEMES
            ============================================================== */

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .feature-v3-danger:hover,
            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .feature-v3-error {
                background:
                    rgba(127,29,29,.18) !important;
            }


            /* ==============================================================
               RESPONSIVO
            ============================================================== */

            @media (max-width: 640px) {
                .character-sheet-theme[data-theme]
                .feature-v3-modal {
                    width:
                        calc(100vw - 20px) !important;

                    max-height:
                        94dvh !important;

                    border-radius:
                        15px !important;
                }

                .character-sheet-theme[data-theme]
                .feature-v3-modal-header {
                    padding:
                        14px 15px !important;
                }

                .character-sheet-theme[data-theme]
                .feature-v3-detail-body,
                .character-sheet-theme[data-theme]
                .feature-editor-v3-body {
                    padding:
                        15px !important;
                }

                .character-sheet-theme[data-theme]
                .feature-v3-footer {
                    align-items:
                        flex-end;

                    flex-direction:
                        column;

                    gap:
                        10px;
                }

                .character-sheet-theme[data-theme]
                .feature-v3-footer-right {
                    width:
                        100%;

                    justify-content:
                        flex-end;
                }
            }

                    /* Efeitos temporários — tokens de todos os temas, sem cores fixas. */
            .feature-active-effects { margin: 0 2px 12px; }
            .feature-active-effects__list { display: grid; gap: 7px; }
            .feature-active-effect {
                display: flex; align-items: center; gap: 10px; padding: 8px 11px;
                border: 1px solid var(--theme-border, #bea688); border-radius: 9px;
                background: var(--theme-surface-card, #fffdf8); color: var(--theme-text-primary, #34231b);
            }
            .feature-active-effect > div:first-child { flex: 1; }
            .feature-active-effect small { display: block; margin-top: 3px; color: var(--theme-text-secondary, #745743); font-size: 10px; }
            .feature-active-effect__time { text-align: center; min-width: 42px; }
            .feature-active-effect__time strong { font-size: 19px; color: var(--theme-text-strong, #53150f); }
            .feature-effect-action {
                border: 1px solid var(--theme-border, #bea688); border-radius: 8px;
                background: var(--theme-paper-strong, #fffdf8); color: var(--theme-text-primary, #34231b);
                font: 800 10px/1.2 system-ui, sans-serif; padding: 7px 9px; cursor: pointer;
            }
            .feature-effect-action:hover { background: var(--theme-hover, #eee2d5); }
            .feature-effect-action:disabled { opacity: .4; cursor: wait; }
            .feature-effect-error { padding: 7px 9px; color: var(--theme-state-damage, #a22); font-size: 11px; font-weight: 700; }
            .feature-effect-toggle { display: flex; align-items: center; gap: 8px; cursor: pointer;
                color: var(--theme-text-primary, #34231b); font-size: 12px; font-weight: 700; }
            .feature-effect-toggle input { accent-color: var(--theme-accent-strong, #6b1d14); width: 16px; height: 16px; }
            .feature-effect-editor { border: 1px solid var(--theme-border-soft, #ddc8b0); border-radius: 10px;
                background: var(--theme-panel-soft, #f6eee5); padding: 12px; margin-top: 10px; }
            .feature-effect-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 7px; }
            .feature-effect-dialog { width: min(440px, 95vw); }
            .feature-check-stats { display: grid; grid-template-columns: repeat(3, 1fr); overflow: hidden;
                border: 1px solid var(--theme-border, #ddc8b0); border-radius: 11px; }
            .feature-check-stats > div { text-align: center; padding: 10px; border-right: 1px solid var(--theme-border-soft, #ddc8b0); }
            .feature-check-stats > div:last-child { border-right: none; }
            .feature-check-stats span { display: block; font-size: 9px; font-weight: 800; color: var(--theme-text-secondary, #745743); }
            .feature-check-stats strong { display: block; margin-top: 4px; font: 900 23px Georgia, serif; color: var(--theme-text-strong, #53150f); }
            .feature-check-result { margin-top: 12px; display: flex; align-items: center; gap: 10px;
                border-radius: 9px; padding: 12px; background: var(--theme-panel-soft, #f6eee5);
                color: var(--theme-text-primary, #34231b); }
            .feature-check-result strong { font: 900 23px Georgia, serif; }
            @media (max-width: 520px) { .feature-effect-row { grid-template-columns: 1fr; } }

            /* V59 — Duração e concentração: dados SVG V57, controlados pelo tema. */
            /* V59.1: mesma linguagem da rolagem de Transformacoes, com SVG V57. */
            .feature-v59-dice-modal { width: min(550px, calc(100vw - 26px)); }
            .feature-v59-dice-modal .feature-v3-modal-header {
                padding: 20px 23px 16px !important;
            }
            .feature-v59-dice-modal .feature-v3-modal-title {
                font: 900 25px/1.18 Georgia,serif;
            }
            .feature-v59-dice-modal .feature-v3-detail-body.feature-v59-dice-main {
                padding: 21px 18px 20px !important;
            }
            .feature-v59-dice-modal .feature-v59-expression {
                font: 900 21px/1.22 Georgia,serif;
                letter-spacing: 0;
                color: var(--theme-text-strong,#53150f);
            }
            .feature-v59-dice-modal .feature-v59-dice-tray {
                min-height: 140px;
                padding: 20px 0 13px;
                gap: 10px 15px;
            }
            .feature-v59-dice-modal .feature-v59-die { min-width: 76px; }
            .feature-v59-dice-modal .feature-v59-bonus {
                border: 0; background: transparent; padding: 3px;
                font: 800 19px/1 system-ui,sans-serif;
                color: var(--theme-accent,#93684c);
            }
            .feature-v59-dice-modal .feature-v59-total { min-height: 57px; }
            .feature-v59-dice-modal .feature-v59-total strong { font-size: 46px; }
            .feature-v59-dice-modal .feature-v59-footer { gap: 9px; padding: 15px 20px !important; }
            .feature-v59-dice-modal .feature-v59-footer button { min-height: 39px; }
            @media (max-width: 480px) {
                .feature-v59-dice-modal .feature-v3-modal-header { padding: 14px 15px !important; }
                .feature-v59-dice-modal .feature-v3-detail-body.feature-v59-dice-main { padding: 18px 10px !important; }
                .feature-v59-dice-modal .feature-v59-dice-tray { gap: 7px; }
                .feature-v59-dice-modal .feature-v59-die { min-width: 62px; }
                .feature-v59-dice-modal .feature-v59-footer { justify-content: center; padding: 12px !important; }
            }
            .feature-v59-dice-main { padding: 20px 18px 17px; text-align: center; }
            .feature-v59-expression { display: block; font-size: 11px; font-weight: 850;
                color: var(--theme-text-secondary, #805d48); letter-spacing: .04em; }
            .feature-v59-dice-tray { display: flex; justify-content: center; flex-wrap: wrap;
                align-items: center; gap: 8px 13px; min-height: 105px; padding: 11px 0; }
            .feature-v59-die { display: inline-flex; align-items: center; justify-content: center; min-width: 64px; }
            .feature-v59-bonus { display: inline-block; border: 1px solid var(--theme-border-soft, #d6bda4);
                border-radius: 8px; color: var(--theme-text-secondary, #805d48); padding: 5px 8px;
                font-weight: 850; font-size: 13px; }
            .feature-v59-total { display: flex; align-items: baseline; justify-content: center; gap: 8px;
                min-height: 52px; color: var(--theme-text-secondary, #805d48); font-size: 11px; }
            .feature-v59-total strong { font: 900 38px/1.15 Georgia, serif;
                color: var(--theme-text-strong, #53150f); font-variant-numeric: tabular-nums; }
            .feature-v59-mode { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 12px; }
            .feature-v59-manual { max-width: 220px; margin: 12px auto 0; }
            .feature-v59-manual label { display:block; font-size: 10px; font-weight: 800;
                color:var(--theme-text-secondary,#805d48); margin-bottom: 5px; }
            .feature-v59-manual input { width: 100%; min-height: 43px; border: 1px solid var(--theme-border, #cbb496);
                border-radius: 8px; background: var(--theme-paper-strong, #fffdf8);
                color: var(--theme-text-primary, #34231b); padding: 5px 10px;
                font-size: 20px; font-weight: 800; text-align: center; outline: none; }
            .feature-v59-manual input:focus { outline: 2px solid var(--theme-accent, #a4765d); outline-offset: 1px; }
            .feature-v59-footer { display:flex; align-items:center; justify-content:flex-end; gap:7px;
                flex-wrap:wrap; }
            .feature-v59-error { margin-top: 9px; font-size: 11px; color: var(--theme-state-damage,#a22); font-weight:700; }
            .feature-v59-dice-modal .sb-poly-die.is-rolling { will-change: transform; }
            .feature-v59-dice-modal .sb-poly-die.is-landed {
                animation: feature-v59-land .25s cubic-bezier(.2,.85,.24,1) both;
            }
            @keyframes feature-v59-land {
                from { transform: translateY(-6px) scale(1.075); }
                to { transform: translateY(0) scale(1); }
            }
            @media (prefers-reduced-motion: reduce) {
                .feature-v59-dice-modal .sb-poly-die.is-landed { animation: none; }
            }
            .feature-v59-result-state { margin: 9px auto 0; padding: 10px; border-radius: 8px;
                background:var(--theme-panel-soft,#f2e7db); color:var(--theme-text-primary,#34231b);
                font-weight:800; font-size:12px; }
            @media (max-width: 440px) {
                .feature-v59-dice-main { padding: 16px 10px; }
                .feature-v59-dice-tray { gap: 6px; }
                .feature-v59-footer { justify-content:center; }
            }

            /* V61: a janela de Concentração usa a mesma linguagem visual do Grimório.
               Classes sb-* são compartilhadas com concentration-check-modal.blade.php. */
            .feature-check-unified {
                --spellbook-state-concentration: var(--theme-state-concentration, #75558a);
                --spellbook-modal-surface: var(--theme-modal-surface, var(--theme-paper-strong, #fffdf8));
                --spellbook-modal-header-background: var(--theme-modal-header-background, var(--theme-paper-alt, #f7f0e6));
                --spellbook-modal-border: var(--theme-modal-border, var(--theme-border, #cdbb9f));
                --spellbook-panel-soft: var(--theme-panel-soft, #f8f0e6);
                --spellbook-title: var(--theme-text-strong, #53150f);
                --spellbook-muted: var(--theme-text-secondary, #7d604d);
                --spellbook-label: var(--theme-label, #704326);
                --spellbook-modal-text: var(--theme-modal-text, var(--theme-text-primary, #34231b));
                --spellbook-paper-strong: var(--theme-paper-strong, #fffdf8);
                --spellbook-paper-alt: var(--theme-paper-alt, #f7f0e6);
                --spellbook-border-soft: var(--theme-border-soft, #d6c1ad);
                --spellbook-border: var(--theme-border, #cdbb9f);
                --spellbook-button-bg: var(--theme-button-bg, #6b1d14);
                --spellbook-button-text: var(--theme-button-text, #fffaf2);
                --spellbook-button-hover: var(--theme-button-hover, #53150f);
                --spellbook-modal-backdrop: var(--theme-modal-backdrop, rgba(0,0,0,.78));
                background: var(--spellbook-modal-backdrop);
                color: var(--spellbook-modal-text);
            }
            .feature-check-unified .feature-check-shell {
                width: min(448px, 100%);
                max-width: 448px;
                max-height: calc(100dvh - 32px);
                display: flex;
                flex-direction: column;
                border-radius: 22px;
                border: 1px solid var(--spellbook-modal-border);
                background: var(--spellbook-modal-surface);
                box-shadow: var(--theme-modal-shadow, 0 22px 60px rgba(0,0,0,.20));
                overflow: hidden;
            }
            .feature-check-unified .feature-check-header {
                flex: 0 0 auto;
                background: var(--spellbook-modal-header-background);
                border-bottom: 1px solid var(--spellbook-border-soft);
                border-top: 4px solid var(--spellbook-state-concentration);
            }
            .feature-check-unified .feature-check-content {
                min-height: 0;
                overflow-y: auto;
                background: var(--spellbook-modal-surface);
            }
            .feature-check-unified .feature-check-footer {
                flex: 0 0 auto;
                background: var(--spellbook-paper-alt);
                border-top: 1px solid var(--spellbook-border-soft);
            }
            .feature-check-unified .feature-check-stats {
                display: grid;
                grid-template-columns: repeat(3,minmax(0,1fr));
                overflow: hidden;
                background: var(--spellbook-panel-soft);
                border: 1px solid var(--spellbook-border-soft);
                border-radius: 12px;
            }
            .feature-check-unified .feature-check-stats > div { min-width:0; padding:12px 8px; text-align:center; }
            .feature-check-unified .feature-check-stats > div + div { border-left:1px solid var(--spellbook-border-soft); }
            .feature-check-unified .feature-check-stats span {
                display: block; font-size: 7px; font-weight: 900;
                letter-spacing:.10em; text-transform:uppercase; color:var(--spellbook-label);
            }
            .feature-check-unified .feature-check-stats strong {
                display:block; margin-top:5px; font:900 22px/1 Georgia,serif;
                color:var(--spellbook-title); font-variant-numeric:tabular-nums;
            }
            .feature-check-unified .feature-check-result {
                display:flex; align-items:center; justify-content:center; gap:16px;
                min-height:92px; padding:10px 12px;
                background:var(--spellbook-panel-soft);
                border:1px solid var(--spellbook-border-soft);
                border-radius:12px;
            }
            .feature-check-unified .feature-check-die { flex:0 0 70px; display:grid; place-items:center; }
            .feature-check-unified .feature-check-die .sb-poly-die .sb-poly-number {
                font-size:22px !important; font-weight:750 !important; letter-spacing:-.01em;
            }
            .feature-check-unified .feature-check-result-copy { min-width:0; flex:1; }
            .feature-check-unified .feature-check-result-copy small {
                display:block; color:var(--spellbook-label); font-size:8px; font-weight:850;
                text-transform:uppercase; letter-spacing:.08em;
            }
            .feature-check-unified .feature-check-result-copy strong {
                display:block; font:900 25px/1.2 Georgia,serif;
                color:var(--spellbook-title); font-variant-numeric:tabular-nums;
            }
            .feature-check-unified .feature-check-result-copy p {
                margin-top:2px; font-size:10px; line-height:1.4; font-weight:700;
                color:var(--spellbook-muted);
            }
            .feature-check-unified .feature-check-manual {
                width:100%; border:1px solid var(--spellbook-border);
                background:var(--theme-input-bg, var(--spellbook-paper-strong));
                border-radius:10px; padding:10px 12px;
                color:var(--theme-input-text, var(--spellbook-title));
                outline:none; font:800 15px/1.4 system-ui,sans-serif;
            }
            .feature-check-unified .feature-check-manual:focus-visible {
                border-color:var(--spellbook-state-concentration);
                box-shadow:0 0 0 3px color-mix(in srgb,var(--spellbook-state-concentration) 17%,transparent);
            }
            .feature-check-unified .feature-check-actions {
                display:flex; flex-wrap:wrap; justify-content:flex-end; gap:7px;
            }
            .feature-check-unified .feature-check-actions button { min-height:35px; }
            .feature-check-unified .feature-check-actions .sb-button-primary {
                background:var(--theme-button-background,var(--spellbook-button-bg));
                color:var(--spellbook-button-text);
            }
            .feature-check-unified .feature-check-actions .sb-button-primary:hover {
                background:var(--theme-button-hover-background,var(--spellbook-button-hover));
            }
            .feature-check-unified .feature-check-actions .sb-button-soft {
                background:var(--spellbook-paper-strong);
                border-color:var(--spellbook-border);
                color:var(--theme-accent-strong,var(--spellbook-title));
            }
            .feature-check-unified .feature-check-actions .sb-button-soft:hover {
                background:var(--theme-hover,var(--spellbook-panel-soft));
            }
            .feature-check-unified .feature-check-state {
                padding:9px 11px; border-radius:10px;
                background:var(--spellbook-panel-soft);
                border:1px solid var(--spellbook-border-soft);
                color:var(--spellbook-title);
                font-size:10px; font-weight:750; line-height:1.5;
            }
            @media (max-width:420px) {
                .feature-check-unified .feature-check-shell { max-height:calc(100dvh - 20px); }
                .feature-check-unified .feature-check-actions { justify-content:center; }
                .feature-check-unified .feature-check-actions button { padding:7px 9px; }
            }
</style>
    @endpush
@endonce

{{-- Usa a mesma paleta e SVG V57 das Transformações. --}}
@include('components.ui.poly-die-styles')

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
        effects: [],
        effectConcentration: null,
        effectBusyId: null,
        effectError: '',
        effectNotice: null,
        effectNoticeTimer: null,
        effectReplacementOpen: false,
        pendingEffectFeature: null,
        featureCheckOpen: false,
        featureCheck: null,
        featureCheckFeature: null,
        featureCheckRolling: false,
        featureCheckRoll: null,
        featureCheckManualMode: false,
        featureCheckManualTotal: '',
        featureCheckDisplay: '—',
        featureCheckResolved: false,
        featureDurationOpen: false,
        featureDurationFeature: null,
        featureDurationConfirmed: false,
        featureDurationMode: 'digital',
        featureDurationExpression: '',
        featureDurationDice: [],
        featureDurationBonus: 0,
        featureDurationTotal: null,
        featureDurationManualTotal: '',
        featureDurationRolling: false,
        featureDurationError: '',
        _featureDiceTicker: null,
        _featureDiceNonce: 0,
        _effectTimer: null,
        _effectDamageHandler: null,

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
            effectsState: @js(route('characters.features.effects.state', $character)),
            effectsActivate: @js(route('characters.features.effects.activate', [
                'character' => $character, 'feature' => '__FEATURE__',
            ])),
            effectsStop: @js(route('characters.features.effects.stop', [
                'character' => $character, 'feature' => '__FEATURE__',
            ])),
            effectCheck: @js(route('characters.features.effects.concentration.check', [
                'character' => $character, 'feature' => '__FEATURE__',
            ])),
            diceRoll: @js(url('/api/roll')),
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

        init() {
            this.refreshEffects();
            this._effectTimer = window.setInterval(() => this.refreshEffects(), 8000);
            this._effectDamageHandler = event => this.handleEffectDamage(event);
            window.addEventListener('character-damage-applied', this._effectDamageHandler);
            this._effectConcentrationChanged = () => this.refreshEffects();
            this._roundsAdvanced = () => this.refreshEffects();
            window.addEventListener('character-rounds-advanced', this._roundsAdvanced);
            window.addEventListener('character-concentration-changed', this._effectConcentrationChanged);
        },
        destroy() {
            if (this._effectTimer) window.clearInterval(this._effectTimer);
            this.stopFeatureDiceTicker();
            this._featureDiceNonce++;
            if (this.effectNoticeTimer) window.clearTimeout(this.effectNoticeTimer);
            if (this._effectDamageHandler) window.removeEventListener('character-damage-applied', this._effectDamageHandler);
            if (this._effectConcentrationChanged) window.removeEventListener('character-concentration-changed', this._effectConcentrationChanged);
            if (this._roundsAdvanced) window.removeEventListener('character-rounds-advanced', this._roundsAdvanced);
        },
        effectEnabled(feature) {
            return !!feature?.data?.effect_enabled;
        },
        activeEffect(feature) {
            return this.effects.find(effect => Number(effect.feature_id) === Number(feature?.id)) ?? null;
        },
        effectUrl(key, featureId) {
            return this.urls[key].replace('__FEATURE__', String(featureId));
        },
        async effectRequest(url, method = 'GET', payload = null) {
            const options = {method, headers: this.jsonHeaders()};
            if (payload !== null) options.body = JSON.stringify(payload);
            const response = await fetch(url, options);
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const validation = Object.values(data?.errors ?? {}).flat().filter(Boolean);
                throw new Error(validation.join(' ') || data?.message || 'Não foi possível atualizar o efeito.');
            }
            return data;
        },
        async refreshEffects() {
            try {
                const data = await this.effectRequest(this.urls.effectsState);
                this.effects = Array.isArray(data.effects) ? data.effects : [];
                this.effectConcentration = data.concentration || null;
                // Compartilha a lista com a gaveta lateral da ficha.
                window.dispatchEvent(new CustomEvent('character-feature-effects-updated', {
                    detail: {
                        characterId: Number(@js((int) $character->id)),
                        effects: this.effects,
                    },
                }));
            } catch (error) {
                // A ficha continua operacional em caso de falha momentânea de rede.
            }
        },
        extractFeatureDiceTotal(data) {
            const value = data?.data?.total ?? data?.data?.result ?? data?.data?.value
                ?? data?.data?.sum ?? data?.total ?? data?.result ?? null;
            if (value === null || value === undefined || value === '') {
                throw new Error('O serviço de dados não retornou um resultado.');
            }
            const number = Number(value);
            if (!Number.isSafeInteger(number)) throw new Error('Resultado de dado inválido.');
            return number;
        },
        parseFeatureDurationFormula(expression) {
            const text = String(expression || '').trim().toLowerCase().replace(/\s+/g, '');
            const match = text.match(/^(\d{1,2})d(\d{1,3})([+-]\d{1,3})?$/);
            if (!match) throw new Error('Use uma fórmula como 1d4 ou 2d6+1.');
            const count = Number(match[1]);
            const sides = Number(match[2]);
            const bonus = Number(match[3] || 0);
            if (count < 1 || count > 10 || sides < 2 || sides > 100 || Math.abs(bonus) > 100) {
                throw new Error('Esta fórmula ultrapassa os limites de 10 dados e 100 faces.');
            }
            return {text, count, sides, bonus, min: Math.max(1, count + bonus),
                max: Math.max(1, count * sides + bonus)};
        },
        stopFeatureDiceTicker() {
            if (this._featureDiceTicker !== null) {
                window.clearInterval(this._featureDiceTicker);
                this._featureDiceTicker = null;
            }
        },
        openFeatureDuration(feature, confirmed = false) {
            let formula;
            try { formula = this.parseFeatureDurationFormula(feature?.data?.effect_duration_dice || '1d4'); }
            catch (error) { this.effectError = error.message; return; }
            this.effectError = '';
            this.featureDurationFeature = feature;
            this.featureDurationConfirmed = confirmed;
            this.featureDurationExpression = formula.text;
            this.featureDurationBonus = formula.bonus;
            this.featureDurationDice = Array.from({length: formula.count}, (_, index) => ({
                index, sides: formula.sides, display: '—', value: null, landed: false,
            }));
            this.featureDurationMode = 'digital';
            this.featureDurationManualTotal = '';
            this.featureDurationTotal = null;
            this.featureDurationError = '';
            this.featureDurationOpen = true;
            this.rollFeatureDuration();
        },
        closeFeatureDuration() {
            if (this.effectBusyId !== null) return;
            this._featureDiceNonce++;
            this.stopFeatureDiceTicker();
            this.featureDurationRolling = false;
            this.featureDurationOpen = false;
            this.featureDurationFeature = null;
            this.featureDurationDice = [];
            this.featureDurationError = '';
        },
        async rollFeatureDuration() {
            if (!this.featureDurationOpen || this.featureDurationRolling || this.effectBusyId !== null) return;
            const nonce = ++this._featureDiceNonce;
            this.featureDurationMode = 'digital';
            this.featureDurationError = '';
            this.featureDurationRolling = true;
            this.featureDurationTotal = null;
            this.stopFeatureDiceTicker();

            // Rolagem de duração permanece exclusivamente dentro do modal.
            // Não abrir a camada global SpellboundDiceFX nesta seção.
            const dice = this.featureDurationDice;
            // A animacao e apenas visual: o resultado valido vem da API de dados.
            dice.forEach(die => {
                die.value = null;
                die.landed = false;
                die.display = String(Math.floor(Math.random() * die.sides) + 1);
            });
            this._featureDiceTicker = window.setInterval(() => {
                if (nonce !== this._featureDiceNonce) return;
                dice.forEach(die => {
                    if (!die.landed) {
                        die.display = String(Math.floor(Math.random() * die.sides) + 1);
                    }
                });
            }, 65);

            try {
                // Espera a rede E a animacao minima para impedir resultado instantaneo.
                // Se a API falhar, nao gera nenhum resultado local para confirmar.
                const [values] = await Promise.all([
                    Promise.all(dice.map(die => this.effectRequest(
                        this.urls.diceRoll, 'POST', {expression: `1d${die.sides}`}
                    ).then(result => {
                        const value = this.extractFeatureDiceTotal(result);
                        if (value < 1 || value > die.sides) throw new Error('O dado retornou uma face invalida.');
                        return value;
                    }))),
                    new Promise(resolve => window.setTimeout(resolve, 1150)),
                ]);
                if (nonce !== this._featureDiceNonce) return;
                // Cada dado para em sequencia, como um lancamento real.
                for (let i = 0; i < dice.length; i++) {
                    if (nonce !== this._featureDiceNonce) return;
                    dice[i].value = values[i];
                    dice[i].display = String(values[i]);
                    dice[i].landed = true;
                    await new Promise(resolve => window.setTimeout(resolve, 135));
                }
                if (nonce !== this._featureDiceNonce) return;
                this.featureDurationTotal = Math.max(1, Math.min(10000,
                    values.reduce((sum, value) => sum + value, this.featureDurationBonus)));
            } catch (error) {
                if (nonce === this._featureDiceNonce) {
                    this.featureDurationError = error?.message || 'Falha na rolagem.';
                    dice.forEach(die => { die.value = null; die.display = '—'; die.landed = false; });
                }
            } finally {
                if (nonce === this._featureDiceNonce) {
                    this.stopFeatureDiceTicker();
                    this.featureDurationRolling = false;
                }
            }
        },
        async confirmFeatureDuration() {
            if (!this.featureDurationFeature || this.featureDurationRolling || this.effectBusyId !== null) return;
            let total;
            if (this.featureDurationMode === 'manual') {
                const formula = this.parseFeatureDurationFormula(this.featureDurationExpression);
                total = Number(this.featureDurationManualTotal);
                if (!Number.isSafeInteger(total) || !String(this.featureDurationManualTotal).trim()
                    || total < formula.min || total > formula.max) {
                    this.featureDurationError = `Informe um total válido entre ${formula.min} e ${formula.max} rodadas.`;
                    return;
                }
            } else {
                total = this.featureDurationTotal;
                if (!Number.isSafeInteger(total)) {
                    this.featureDurationError = 'Role os dados antes de confirmar.';
                    return;
                }
            }
            this.featureDurationError = '';
            await this.activateFeature(this.featureDurationFeature, this.featureDurationConfirmed, total);
        },
        async activateFeature(feature, confirmed = false, durationOverride = null) {
            if (this.effectBusyId !== null || !this.effectEnabled(feature) || this.activeEffect(feature)) return;
            if (!confirmed && feature?.data?.effect_requires_concentration && this.effectConcentration) {
                this.pendingEffectFeature = feature;
                this.effectReplacementOpen = true;
                return;
            }
            if (feature?.data?.effect_duration_mode === 'dice' && durationOverride === null) {
                this.effectReplacementOpen = false;
                this.pendingEffectFeature = null;
                this.openFeatureDuration(feature, confirmed);
                return;
            }
            this.effectBusyId = Number(feature.id);
            this.effectError = '';
            try {
                const payload = {confirm_replace_concentration: confirmed};
                if (durationOverride !== null) payload.duration_roll_total = durationOverride;
                const result = await this.effectRequest(this.effectUrl('effectsActivate', feature.id), 'POST', payload);
                this.effectReplacementOpen = false;
                this.pendingEffectFeature = null;
                await this.refreshEffects();
                if (this.detailFeature?.id === feature.id) this.detailOpen = false;
                const duration = Number(result.effect?.duration_remaining ?? 0);
                this.effectNotice = {
                    name: String(feature.name || 'Habilidade'),
                    duration: duration,
                    concentration: !!result.effect?.requires_concentration,
                };
                if (this.effectNoticeTimer) window.clearTimeout(this.effectNoticeTimer);
                this.effectNoticeTimer = window.setTimeout(() => { this.effectNotice = null; }, 5500);
                window.dispatchEvent(new CustomEvent('character-concentration-changed'));
                this._featureDiceNonce++;
                this.stopFeatureDiceTicker();
                this.featureDurationRolling = false;
                this.featureDurationOpen = false;
                this.featureDurationFeature = null;
            } catch (error) {
                this.effectError = error?.message || 'Não foi possível ativar a habilidade.';
                if (this.featureDurationOpen) this.featureDurationError = this.effectError;
                await this.refreshEffects();
            } finally { this.effectBusyId = null; }
        },
        async stopFeatureEffect(feature) {
            if (this.effectBusyId !== null) return;
            this.effectBusyId = Number(feature.id);
            this.effectError = '';
            try {
                await this.effectRequest(this.effectUrl('effectsStop', feature.id), 'POST', {});
                await this.refreshEffects();
                window.dispatchEvent(new CustomEvent('character-concentration-changed'));
            } catch (error) { this.effectError = error?.message || 'Não foi possível encerrar o efeito.'; }
            finally {
                this.effectBusyId = null;
                window.dispatchEvent(new CustomEvent('character-feature-effect-stop-finished', {
                    detail: {
                        characterId: Number(@js((int) $character->id)),
                        error: this.effectError,
                    },
                }));
            }
        },
        async handleEffectDamage(event) {
            const detail = event?.detail ?? {};
            const characterId = Number(@js((int) $character->id));
            if (![Number(detail.characterId ?? 0), Number(detail.mechanicalCharacterId ?? 0)].includes(characterId)) return;
            const damage = Math.max(0, parseInt(detail.damage ?? 0) || 0);
            if (damage < 1 || this.featureCheckOpen || this.effectConcentration?.source !== 'feature') return;
            const effect = this.effects.find(item => Number(item.feature_id) === Number(this.effectConcentration?.id));
            if (!effect?.requires_concentration) return;
            try {
                const result = await this.effectRequest(this.effectUrl('effectCheck', effect.feature_id), 'POST', {damage});
                if (result.concentration_check?.required) {
                    this.featureCheck = result.concentration_check;
                    this.featureCheckFeature = effect;
                    this.featureCheckRoll = null;
                    this.featureCheckResolved = false;
                    this.featureCheckManualMode = false;
                    this.featureCheckManualTotal = '';
                    this.featureCheckDisplay = '—';
                    this.featureCheckOpen = true;
                }
            } catch (error) { this.effectError = error?.message || 'Falha ao preparar o teste.'; }
        },
        async rollFeatureConcentration(mode = 'normal') {
            if (!this.featureCheck || !this.featureCheckFeature || this.featureCheckRolling || this.featureCheckResolved) return;
            const nonce = ++this._featureDiceNonce;
            // Concentração também conserva somente seu dado interno.
            // Mantém os gestos de vantagem/desvantagem sem duplicar o HUD.
            const choice = window.SpellboundD20Gestures.choice(mode);
            this.featureCheckRolling = true;
            this.effectError = '';
            this.featureCheckManualMode = false;
            this.featureCheckRoll = null;
            this.featureCheckDisplay = String(Math.floor(Math.random() * 20) + 1);
            this.stopFeatureDiceTicker();
            this._featureDiceTicker = window.setInterval(() => {
                if (nonce === this._featureDiceNonce) {
                    this.featureCheckDisplay = String(Math.floor(Math.random() * 20) + 1);
                }
            }, 65);
            try {
                const check = this.featureCheck;
                const [roll] = await Promise.all([
                    choice === 'normal'
                        ? this.effectRequest(this.urls.diceRoll, 'POST', {expression:check.expression || '1d20'})
                        : window.SpellboundD20Gestures.rollTwo({
                            url:this.urls.diceRoll, expression:check.expression || '1d20',
                            modifier:Number(check.modifier || 0), mode:choice,
                            headers:this.jsonHeaders(),
                        }),
                    new Promise(resolve => window.setTimeout(resolve, 1200)),
                ]);
                if (nonce !== this._featureDiceNonce) return;
                const modifier = Number(check.modifier || 0);
                const total = choice === 'normal' ? this.extractFeatureDiceTotal(roll) : roll.total;
                const die = choice === 'normal' ? total - modifier : roll.die;
                if (!Number.isSafeInteger(die) || die < 1 || die > 20) {
                    throw new Error('O resultado do d20 nao corresponde ao modificador do teste.');
                }
                this.stopFeatureDiceTicker();
                this.featureCheckDisplay = String(die);
                this.featureCheckRoll = {die, total, passed: null};
                await new Promise(resolve => window.setTimeout(resolve, 180));
            } catch (error) {
                if (nonce === this._featureDiceNonce) {
                    this.effectError = error?.message || 'Erro ao rolar Constituicao.';
                    this.featureCheckDisplay = '—';
                }
            } finally {
                if (nonce === this._featureDiceNonce) {
                    this.stopFeatureDiceTicker();
                    this.featureCheckRolling = false;
                }
            }
        },
        async confirmFeatureConcentration() {
            if (!this.featureCheck || !this.featureCheckFeature || this.featureCheckRolling || this.featureCheckResolved) return;
            let total;
            if (this.featureCheckManualMode) {
                total = Number(this.featureCheckManualTotal);
                const modifier = Number(this.featureCheck.modifier || 0);
                if (!String(this.featureCheckManualTotal).trim() || !Number.isSafeInteger(total)
                    || total < 1 + modifier || total > 20 + modifier) {
                    this.effectError = 'Informe o resultado total do teste, incluindo modificadores.';
                    return;
                }
            } else {
                total = this.featureCheckRoll?.total;
                if (!Number.isSafeInteger(total)) {
                    this.effectError = 'Role o d20 primeiro.';
                    return;
                }
            }
            this.featureCheckRolling = true;
            this.effectError = '';
            try {
                const check = this.featureCheck;
                const result = await this.effectRequest(this.effectUrl('effectCheck', this.featureCheckFeature.feature_id), 'POST', {
                    damage: check.damage, roll_total: total,
                });
                this.featureCheckRoll = {
                    die: total - Number(check.modifier || 0), total,
                    passed: !!result?.result?.passed,
                };
                this.featureCheckDisplay = String(this.featureCheckRoll.die);
                this.featureCheckResolved = true;
                await this.refreshEffects();
                if (!result?.result?.passed) window.dispatchEvent(new CustomEvent('character-concentration-changed'));
            } catch (error) { this.effectError = error?.message || 'Erro ao registrar salvamento.'; }
            finally { this.featureCheckRolling = false; }
        },
        closeFeatureCheck() {
            if (this.featureCheckRolling) return;
            this._featureDiceNonce++;
            this.stopFeatureDiceTicker();
            this.featureCheckOpen = false;
            this.featureCheck = null;
            this.featureCheckFeature = null;
            this.featureCheckRoll = null;
            this.featureCheckManualMode = false;
            this.featureCheckManualTotal = '';
            this.featureCheckDisplay = '—';
            this.featureCheckResolved = false;
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
                    effect_enabled: !!data.effect_enabled,
                    effect_requires_concentration: !!data.effect_requires_concentration,
                    effect_duration_mode: data.effect_duration_mode === 'dice' ? 'dice' : 'fixed',
                    effect_duration_value: Number(data.effect_duration_value ?? 1),
                    effect_duration_dice: data.effect_duration_dice ?? '1d4',
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
                    effect_enabled: false,
                    effect_requires_concentration: false,
                    effect_duration_mode: 'fixed',
                    effect_duration_value: 1,
                    effect_duration_dice: '1d4',
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
                    effect_enabled: !!this.form?.data?.effect_enabled,
                    effect_requires_concentration: !!this.form?.data?.effect_requires_concentration,
                    effect_duration_mode: this.form?.data?.effect_duration_mode === 'dice' ? 'dice' : 'fixed',
                    effect_duration_value: Math.max(1, Number(this.form?.data?.effect_duration_value || 1)),
                    effect_duration_dice: String(this.form?.data?.effect_duration_dice || '1d4').trim(),
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

        /*
        |--------------------------------------------------------------------------
        | SINCRONIZA APÓS DESCANSO
        |--------------------------------------------------------------------------
        |
        | O backend é a fonte de verdade.
        | O hero-header dispara character-rest-completed quando o descanso termina.
        |
        */

        syncAfterRest(payload) {
            const trackers =
                Array.isArray(
                    payload?.feature_trackers
                )
                    ? payload.feature_trackers
                    : [];

            if (!trackers.length) {
                return;
            }

            for (const tracker of trackers) {
                const id =
                    parseInt(
                        tracker?.id
                    );

                if (!id) {
                    continue;
                }

                const index =
                    this.features.findIndex(
                        feature =>
                            parseInt(feature.id)
                            === id
                    );

                if (index < 0) {
                    continue;
                }

                const feature =
                    this.normalizeFeature(
                        this.features[index]
                    );

                if (
                    tracker?.uses_max
                    !== undefined
                    && tracker?.uses_max
                    !== null
                ) {
                    feature.uses_max =
                        Math.max(
                            1,
                            parseInt(
                                tracker.uses_max
                            ) || 1
                        );
                }

                if (
                    tracker?.uses_current
                    !== undefined
                    && tracker?.uses_current
                    !== null
                ) {
                    const maximum =
                        feature.uses_max
                        ?? Math.max(
                            0,
                            parseInt(
                                tracker.uses_current
                            ) || 0
                        );

                    feature.uses_current =
                        Math.max(
                            0,
                            Math.min(
                                maximum,
                                parseInt(
                                    tracker.uses_current
                                ) || 0
                            )
                        );
                }

                if (
                    tracker?.recovery
                    !== undefined
                ) {
                    feature.recovery =
                        tracker.recovery;
                }

                if (
                    tracker?.counter_mode
                    === 'build'
                    || tracker?.counter_mode
                    === 'spend'
                ) {
                    feature.data.counter_mode =
                        tracker.counter_mode;
                }

                this.features[index] =
                    feature;

                if (
                    this.detailFeature
                    && parseInt(
                        this.detailFeature.id
                    ) === id
                ) {
                    this.detailFeature =
                        this.normalizeFeature(
                            feature
                        );
                }

                if (
                    this.form
                    && parseInt(
                        this.editingId
                    ) === id
                ) {
                    this.form.uses_current =
                        feature.uses_current;

                    this.form.uses_max =
                        feature.uses_max;
                }
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

    @character-feature-effect-stop.window="
        if (Number($event.detail?.characterId) === Number(@js((int) $character->id))) {
            stopFeatureEffect({ id: Number($event.detail.featureId) });
        }
    "

    @character-rest-completed.window="
        syncAfterRest(
            $event.detail
        )
    "

    @keydown.escape.window="
        if (editorOpen) {
            closeEditor()
        } else if (detailOpen) {
            closeDetail()
        }
    "

    class="character-features-v3 character-features-v4 character-features-v6 character-features-v8 character-features-v9 character-features-v10 character-features-v11 character-features-v12 character-features-v15"
>
    <div class="character-features-v3-header character-features-v9-header">
        <button
            type="button"
            @click="openCreate()"
            class="character-features-v9-title"
            title="Adicionar nova habilidade"
        >
            <span class="character-features-v9-title-text">
                Habilidades
            </span>

        </button>
    </div>


    {{-- Efeitos ativos são apresentados na gaveta Rodadas e Efeitos do show.blade.php. --}}

{{-- ============================================================
         DIVISÃO DAS DUAS LISTAS
    ============================================================= --}}
    <div class="character-features-v11-table-wrap">
        <div
            class="character-features-v10-column-bar"
        aria-hidden="true"
    >
        <span class="character-features-v10-column-label">
            Esquerda
        </span>

        <span class="character-features-v10-column-label">
            Direita
        </span>
    </div>

    <div class="character-features-v12-scroll">
        <div class="character-features-v3-columns character-features-v9-columns">
        {{-- ESQUERDA --}}
        <div class="character-features-v3-column character-features-v9-column">
            <template
                x-for="feature in leftFeatures"
                :key="'left-feature-' + feature.id"
            >
                <article
                    class="character-feature-v9-card"
                    @click="openDetail(feature)"
                    role="button"
                    tabindex="0"
                    @keydown.enter.prevent="openDetail(feature)"
                    @keydown.space.prevent="openDetail(feature)"
                    title="Abrir habilidade completa"
                >
                    <div class="character-feature-v9-head">
                        <div class="character-feature-v9-heading">
                            <h3
                                class="character-feature-v9-name"
                                x-text="feature.name"
                            ></h3>

                            <div class="character-feature-v9-meta">
                                <span
                                    class="character-feature-v9-meta-item"
                                    x-text="activationLabel(feature)"
                                ></span>

                                <span
                                    x-show="feature.source"
                                    x-cloak
                                    class="character-feature-v9-meta-item"
                                    x-text="feature.source"
                                ></span>

                                <span
                                    x-show="feature.level_acquired"
                                    x-cloak
                                    class="character-feature-v9-meta-item"
                                >
                                    Nível
                                    <span
                                        class="ml-1"
                                        x-text="feature.level_acquired"
                                    ></span>
                                </span>
                            </div>
                        </div>

                        <template x-if="feature.uses_max !== null">
                            <div
                                class="character-feature-v9-tracker"
                                @click.stop
                            >
                                <button
                                    type="button"
                                    @click="changeUses(feature, -1)"
                                    :disabled="
                                        busyUsesId !== null
                                        || feature.uses_current <= 0
                                    "
                                    title="Diminuir uso"
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
                                    title="Aumentar uso"
                                >
                                    +
                                </button>
                            </div>
                        </template>
                        <button x-show="effectEnabled(feature)" x-cloak type="button"
                            class="feature-effect-action"
                            @click.stop="activeEffect(feature) ? stopFeatureEffect(feature) : activateFeature(feature)"
                            :disabled="effectBusyId !== null"
                            x-text="activeEffect(feature) ? 'Encerrar' : 'Ativar'">
                        </button>
                    </div>

                    <div
                        x-show="feature.data?.quick_text"
                        x-cloak
                        class="character-feature-v9-quick-wrap"
                    >
                        <p
                            class="character-feature-v9-quick"
                            x-text="feature.data.quick_text"
                        ></p>
                    </div>
                </article>
            </template>

            <div
                x-show="leftFeatures.length === 0"
                x-cloak
                class="character-features-v3-empty-column character-features-v9-empty"
            >
                Nenhuma habilidade nesta coluna.
            </div>
        </div>

        {{-- DIREITA --}}
        <div class="character-features-v3-column character-features-v9-column">
            <template
                x-for="feature in rightFeatures"
                :key="'right-feature-' + feature.id"
            >
                <article
                    class="character-feature-v9-card"
                    @click="openDetail(feature)"
                    role="button"
                    tabindex="0"
                    @keydown.enter.prevent="openDetail(feature)"
                    @keydown.space.prevent="openDetail(feature)"
                    title="Abrir habilidade completa"
                >
                    <div class="character-feature-v9-head">
                        <div class="character-feature-v9-heading">
                            <h3
                                class="character-feature-v9-name"
                                x-text="feature.name"
                            ></h3>

                            <div class="character-feature-v9-meta">
                                <span
                                    class="character-feature-v9-meta-item"
                                    x-text="activationLabel(feature)"
                                ></span>

                                <span
                                    x-show="feature.source"
                                    x-cloak
                                    class="character-feature-v9-meta-item"
                                    x-text="feature.source"
                                ></span>

                                <span
                                    x-show="feature.level_acquired"
                                    x-cloak
                                    class="character-feature-v9-meta-item"
                                >
                                    Nível
                                    <span
                                        class="ml-1"
                                        x-text="feature.level_acquired"
                                    ></span>
                                </span>
                            </div>
                        </div>

                        <template x-if="feature.uses_max !== null">
                            <div
                                class="character-feature-v9-tracker"
                                @click.stop
                            >
                                <button
                                    type="button"
                                    @click="changeUses(feature, -1)"
                                    :disabled="
                                        busyUsesId !== null
                                        || feature.uses_current <= 0
                                    "
                                    title="Diminuir uso"
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
                                    title="Aumentar uso"
                                >
                                    +
                                </button>
                            </div>
                        </template>
                        <button x-show="effectEnabled(feature)" x-cloak type="button"
                            class="feature-effect-action"
                            @click.stop="activeEffect(feature) ? stopFeatureEffect(feature) : activateFeature(feature)"
                            :disabled="effectBusyId !== null"
                            x-text="activeEffect(feature) ? 'Encerrar' : 'Ativar'">
                        </button>
                    </div>

                    <div
                        x-show="feature.data?.quick_text"
                        x-cloak
                        class="character-feature-v9-quick-wrap"
                    >
                        <p
                            class="character-feature-v9-quick"
                            x-text="feature.data.quick_text"
                        ></p>
                    </div>
                </article>
            </template>

            <div
                x-show="rightFeatures.length === 0"
                x-cloak
                class="character-features-v3-empty-column character-features-v9-empty"
            >
                Nenhuma habilidade nesta coluna.
            </div>
        </div>
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
            data-theme="{{ $characterTheme }}"
            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"
            class="
                character-sheet-theme
                character-theme-modal-layer
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
                class="feature-v3-backdrop character-theme-modal-backdrop"
                @click="closeDetail()"
            ></div>

            <section
                x-show="detailOpen"
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                class="feature-v3-modal character-theme-modal"
                @click.stop
            >
                <header class="feature-v3-modal-header character-theme-modal-header">
                    <div>
                        <span class="feature-v3-modal-kicker">
                            Habilidade
                        </span>

                        <h3
                            class="feature-v3-modal-title character-theme-modal-title"
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
                    <button type="button" x-show="effectEnabled(detailFeature)" x-cloak
                        @click="activeEffect(detailFeature) ? stopFeatureEffect(detailFeature) : activateFeature(detailFeature)"
                        class="feature-v3-secondary"
                        :disabled="effectBusyId !== null"
                        x-text="activeEffect(detailFeature) ? 'Encerrar efeito' : 'Ativar habilidade'"></button>
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
            data-theme="{{ $characterTheme }}"
            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"
            class="
                character-sheet-theme
                character-theme-modal-layer
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
                class="feature-v3-backdrop character-theme-modal-backdrop"
                @click="closeEditor()"
            ></div>

            <section
                x-show="editorOpen"
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                class="feature-v3-modal character-theme-modal"
                @click.stop
            >
                <header class="feature-v3-modal-header character-theme-modal-header">
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
                            class="feature-v3-modal-title character-theme-modal-title"
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

                    {{-- EFEITO TEMPORÁRIO --}}
                    <section class="feature-editor-v3-section">
                        <label class="feature-effect-toggle">
                            <input type="checkbox" x-model="form.data.effect_enabled">
                            <span>Esta habilidade cria um efeito temporário com contador</span>
                        </label>
                        <div x-show="form.data.effect_enabled" x-cloak class="feature-effect-editor">
                            <label class="feature-editor-v3-label">Duração da habilidade</label>
                            <div class="feature-effect-row">
                                <select x-model="form.data.effect_duration_mode" class="feature-editor-v3-select">
                                    <option value="fixed">Rodadas fixas</option>
                                    <option value="dice">Rolagem ao ativar</option>
                                </select>
                                <input x-show="form.data.effect_duration_mode === 'fixed'" type="number"
                                    min="1" max="10000" x-model.number="form.data.effect_duration_value"
                                    class="feature-editor-v3-input" aria-label="Quantidade de rodadas">
                                <input x-show="form.data.effect_duration_mode === 'dice'" type="text"
                                    maxlength="40" x-model="form.data.effect_duration_dice" placeholder="Ex.: 1d4"
                                    class="feature-editor-v3-input" aria-label="Dados da duração">
                            </div>
                            <label class="feature-effect-toggle mt-3">
                                <input type="checkbox" x-model="form.data.effect_requires_concentration">
                                <span>Exige concentração (compartilhada com feitiços e transformações)</span>
                            </label>
                            <p class="feature-editor-v3-help">O contador começa quando o jogador ativa a habilidade. Se houver usos, a ativação consome um uso. O avanço da iniciativa reduz as rodadas automaticamente.</p>
                        </div>
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
    {{-- Confirmar substituição da concentração atual --}}
    <template x-teleport="body">
        <div x-show="effectReplacementOpen" x-cloak data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[280] flex items-center justify-center p-4">
            <div class="feature-v3-backdrop character-theme-modal-backdrop" @click="effectReplacementOpen = false"></div>
            <section class="feature-v3-modal character-theme-modal feature-effect-dialog" role="alertdialog" aria-modal="true">
                <header class="feature-v3-modal-header character-theme-modal-header">
                    <h3 class="feature-v3-modal-title character-theme-modal-title">Substituir concentração?</h3>
                </header>
                <div class="feature-v3-detail-body">
                    Você está concentrado em <strong x-text="effectConcentration?.name || 'outro efeito'"></strong>.
                    Ativar <strong x-text="pendingEffectFeature?.name"></strong> encerrará a concentração anterior.
                    <p x-show="effectConcentration?.source === 'transformation'" class="mt-2">
                        A transformação será encerrada e poderá aplicar exaustão.
                    </p>
                </div>
                <footer class="feature-v3-detail-footer">
                    <button class="feature-v3-secondary" @click="effectReplacementOpen = false">Cancelar</button>
                    <button class="feature-v3-primary" @click="activateFeature(pendingEffectFeature, true)"
                        :disabled="effectBusyId !== null">Encerrar e ativar</button>
                </footer>
            </section>
        </div>
    </template>

    {{-- Duração variável de Habilidades — V59: não ativa até o usuário confirmar. --}}
    <template x-teleport="body">
        <div x-show="featureDurationOpen" x-cloak data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[295] flex items-center justify-center p-4">
            <div class="feature-v3-backdrop character-theme-modal-backdrop" x-on:click="closeFeatureDuration()"></div>
            <section class="feature-v3-modal character-theme-modal feature-v59-dice-modal" role="dialog" aria-modal="true"
                aria-label="Duração da habilidade">
                <header class="feature-v3-modal-header character-theme-modal-header">
                    <div>
                        <span class="feature-v3-modal-kicker">Habilidade</span>
                        <h3 class="feature-v3-modal-title character-theme-modal-title">Duração da habilidade</h3>
                        <p class="feature-editor-v3-help" x-text="featureDurationFeature?.name"></p>
                    </div>
                    <button type="button" class="feature-v3-modal-close" x-on:click="closeFeatureDuration()" aria-label="Fechar">×</button>
                </header>
                <div class="feature-v3-detail-body feature-v59-dice-main">
                    <span class="feature-v59-expression" x-text="featureDurationExpression + ' rodadas'"></span>
                    <div class="feature-v59-dice-tray" x-show="featureDurationMode !== 'manual'">
                        <template x-for="die in featureDurationDice" :key="die.index">
                            <div class="feature-v59-die">
                            <template x-if="die.sides === 4">
                                <x-ui.poly-die sides="4" size="76" x-bind:class="die.landed ? 'is-landed' : (featureDurationRolling ? 'is-rolling' : '')"><span x-text="die.display"></span></x-ui.poly-die>
                            </template>
                            <template x-if="die.sides === 6">
                                <x-ui.poly-die sides="6" size="76" x-bind:class="die.landed ? 'is-landed' : (featureDurationRolling ? 'is-rolling' : '')"><span x-text="die.display"></span></x-ui.poly-die>
                            </template>
                            <template x-if="die.sides === 8">
                                <x-ui.poly-die sides="8" size="76" x-bind:class="die.landed ? 'is-landed' : (featureDurationRolling ? 'is-rolling' : '')"><span x-text="die.display"></span></x-ui.poly-die>
                            </template>
                            <template x-if="die.sides === 10">
                                <x-ui.poly-die sides="10" size="76" x-bind:class="die.landed ? 'is-landed' : (featureDurationRolling ? 'is-rolling' : '')"><span x-text="die.display"></span></x-ui.poly-die>
                            </template>
                            <template x-if="die.sides === 12">
                                <x-ui.poly-die sides="12" size="76" x-bind:class="die.landed ? 'is-landed' : (featureDurationRolling ? 'is-rolling' : '')"><span x-text="die.display"></span></x-ui.poly-die>
                            </template>
                            <template x-if="die.sides === 20">
                                <x-ui.poly-die sides="20" size="76" x-bind:class="die.landed ? 'is-landed' : (featureDurationRolling ? 'is-rolling' : '')"><span x-text="die.display"></span></x-ui.poly-die>
                            </template>
                            <template x-if="die.sides === 100">
                                <x-ui.poly-die sides="100" size="76" x-bind:class="die.landed ? 'is-landed' : (featureDurationRolling ? 'is-rolling' : '')"><span x-text="die.display"></span></x-ui.poly-die>
                            </template>
                            <template x-if="![4,6,8,10,12,20,100].includes(die.sides)">
                                <span class="feature-v59-bonus" x-text="die.display"></span>
                            </template>
                            </div>
                        </template>
                        <span x-show="featureDurationBonus !== 0" class="feature-v59-bonus"
                            x-text="featureDurationBonus > 0 ? '+' + featureDurationBonus : featureDurationBonus"></span>
                    </div>
                    <div class="feature-v59-total" x-show="featureDurationMode !== 'manual'">
                        <span x-text="featureDurationRolling ? 'Rolando' : 'Total'"></span>
                        <strong aria-live="polite" x-text="featureDurationRolling ? '···' : (featureDurationTotal ?? '—')"></strong>
                        <span>rodadas</span>
                    </div>
                    <div class="feature-v59-manual" x-show="featureDurationMode === 'manual'" x-cloak>
                        <label for="feature-v59-duration-manual">Total rolado na mesa (rodadas)</label>
                        <input id="feature-v59-duration-manual" type="number" step="1" min="1"
                            x-model="featureDurationManualTotal" placeholder="0">
                    </div>
                    <p x-show="featureDurationError" x-cloak class="feature-v59-error" x-text="featureDurationError"></p>
                    <div class="feature-v59-mode" x-show="featureDurationMode === 'manual'">
                        <button type="button" class="feature-v3-secondary" x-on:click="featureDurationMode = 'digital'; featureDurationError = ''">Ver dados</button>
                    </div>
                </div>
                <footer class="feature-v3-detail-footer feature-v59-footer">
                    <button type="button" class="feature-v3-secondary"
                        x-on:click="featureDurationMode = 'manual'; featureDurationError = ''"
                        :disabled="effectBusyId !== null || featureDurationRolling">Rolar na mesa</button>
                    <button type="button" class="feature-v3-secondary" x-show="featureDurationMode === 'digital'"
                        x-on:click="rollFeatureDuration()" :disabled="featureDurationRolling || effectBusyId !== null">
                        Rolar novamente
                    </button>
                    <button type="button" class="feature-v3-primary" x-on:click="confirmFeatureDuration()"
                        :disabled="featureDurationRolling || effectBusyId !== null"
                        x-text="effectBusyId !== null ? 'Ativando...' : (featureDurationRolling ? 'Rolando...' : 'Usar resultado')"></button>
                </footer>
            </section>
        </div>
    </template>

    {{-- V61: mesma estrutura visual de concentration-check-modal.blade.php do Grimório. --}}
    <template x-teleport="body">
        <div x-show="featureCheckOpen" x-cloak data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-spellbook-v4 sb-modal-backdrop feature-check-unified fixed inset-0 z-[290] flex items-center justify-center p-4 backdrop-blur-sm"
            x-on:click.self="closeFeatureCheck()">
            <section class="sb-modal-shell feature-check-shell w-full max-w-md overflow-hidden rounded-[22px] border"
                role="alertdialog" aria-modal="true" aria-label="Salvamento de concentração">
                <header class="sb-modal-header feature-check-header px-5 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex min-w-0 items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                style="background:color-mix(in srgb,var(--spellbook-state-concentration) 14%,var(--spellbook-paper-strong) 86%);color:var(--spellbook-state-concentration);">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M12 2v4M12 18v4M2 12h4M18 12h4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M19.1 4.9l-2.8 2.8M7.7 16.3l-2.8 2.8"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[7px] font-black uppercase tracking-[0.14em]"
                                    style="color:var(--spellbook-state-concentration);">Concentração</p>
                                <h3 class="sb-title mt-0.5 font-serif text-[22px] font-black leading-tight">
                                    Salvamento de Constituição
                                </h3>
                                <p class="sb-muted mt-1 text-[9px] leading-4">
                                    <span class="font-bold" style="color:var(--spellbook-title);"
                                        x-text="featureCheckFeature?.name || 'Habilidade'"></span>
                                    precisa manter a concentração após receber dano.
                                </p>
                            </div>
                        </div>
                        <button type="button" x-on:click="closeFeatureCheck()" :disabled="featureCheckRolling"
                            class="sb-button-soft flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border text-base disabled:opacity-40"
                            aria-label="Fechar">×</button>
                    </div>
                </header>

                <div class="feature-check-content px-5 py-4">
                    <div class="sb-panel feature-check-stats sb-shadow">
                        <div><span>CD</span><strong x-text="featureCheck?.dc ?? 10"></strong></div>
                        <div><span>Dano</span><strong x-text="featureCheck?.damage ?? 0"></strong></div>
                        <div><span>Mod.</span>
                            <strong x-text="Number(featureCheck?.modifier ?? 0) >= 0 ? '+' + Number(featureCheck?.modifier ?? 0) : String(Number(featureCheck?.modifier ?? 0))"></strong>
                        </div>
                    </div>

                    <p x-show="Number(featureCheck?.exhaustion_penalty ?? 0) > 0" x-cloak
                        class="sb-muted mt-2 text-center text-[9px] font-semibold"
                        x-text="'Exaustão: -' + Number(featureCheck?.exhaustion_penalty ?? 0) + ' no salvamento'"></p>

                    <button type="button" x-show="!featureCheckRoll && !featureCheckRolling && !featureCheckManualMode && !featureCheckResolved" x-cloak
                        x-on:click="window.SpellboundD20Gestures.left($event, mode => rollFeatureConcentration(mode))"
                        x-on:dblclick.prevent="window.SpellboundD20Gestures.double($event, mode => rollFeatureConcentration(mode))"
                        x-on:contextmenu.prevent="window.SpellboundD20Gestures.right($event, mode => rollFeatureConcentration(mode))"
                        class="sb-button-primary mt-4 w-full rounded-xl px-4 py-3 text-[9px] font-black uppercase tracking-[0.1em]">
                        Rolar Constituição
                    </button>

                    <div x-show="!featureCheckManualMode && (featureCheckRolling || featureCheckRoll)" x-cloak
                        class="sb-panel feature-check-result mt-4">
                        <div class="feature-check-die">
                            <x-ui.poly-die sides="20" size="70"
                                x-bind:class="featureCheckRolling && !featureCheckRoll ? 'is-rolling' : (featureCheckRoll ? 'is-landed' : '')">
                                <span x-text="featureCheckDisplay"></span>
                            </x-ui.poly-die>
                        </div>
                        <div class="feature-check-result-copy">
                            <small x-text="featureCheckRolling && !featureCheckRoll ? 'Rolando d20...' : (featureCheckRolling ? 'Registrando...' : 'Resultado total')"></small>
                            <strong x-text="featureCheckRolling && !featureCheckRoll ? '···' : (featureCheckRoll?.total ?? '—')"></strong>
                            <p x-show="featureCheckRoll && !featureCheckResolved && !featureCheckRolling" x-cloak
                                x-text="(Number(featureCheckRoll?.total ?? 0) >= Number(featureCheck?.dc ?? 10) ? 'Sucesso' : 'Falha') + ' · aguardando confirmação'"></p>
                            <p x-show="featureCheckResolved" x-cloak
                                x-text="featureCheckRoll?.passed ? 'Sucesso · Concentração mantida' : 'Falha · Concentração encerrada'"></p>
                        </div>
                    </div>

                    <div x-show="featureCheckManualMode && !featureCheckResolved" x-cloak class="mt-4">
                        <label class="sb-label mb-2 block text-[9px] font-black" for="feature-concentration-manual-total">
                            Total rolado na mesa (com modificador)
                        </label>
                        <input id="feature-concentration-manual-total" type="number" step="1"
                            class="feature-check-manual" x-model="featureCheckManualTotal" placeholder="Informe o resultado total">
                    </div>

                    <p x-show="featureCheckResolved && featureCheckManualMode" x-cloak
                        class="feature-check-state mt-3"
                        x-text="featureCheckRoll?.passed ? 'Sucesso · Concentração mantida' : 'Falha · Concentração encerrada'"></p>
                    <p x-show="effectError" x-cloak role="alert" class="mt-3 text-[10px] font-bold"
                        style="color:var(--theme-state-damage,#a22);" x-text="effectError"></p>
                </div>

                <footer class="sb-paper-alt feature-check-footer flex justify-end px-5 py-3">
                    <div class="feature-check-actions" x-show="!featureCheckResolved" x-cloak>
                        <button type="button" x-show="!featureCheckManualMode" x-on:click="featureCheckManualMode = true; effectError = ''"
                            :disabled="featureCheckRolling"
                            class="sb-button-soft rounded-lg border px-3 py-2 text-[8px] font-black uppercase tracking-[0.04em] disabled:opacity-40">
                            Rolar na mesa
                        </button>
                        <button type="button" x-show="featureCheckManualMode" x-on:click="featureCheckManualMode = false; effectError = ''"
                            :disabled="featureCheckRolling"
                            class="sb-button-soft rounded-lg border px-3 py-2 text-[8px] font-black uppercase tracking-[0.04em] disabled:opacity-40">
                            Voltar aos dados
                        </button>
                        <button type="button" x-show="!featureCheckManualMode && featureCheckRoll"
                            x-on:click="window.SpellboundD20Gestures.left($event, mode => rollFeatureConcentration(mode))"
                        x-on:dblclick.prevent="window.SpellboundD20Gestures.double($event, mode => rollFeatureConcentration(mode))"
                        x-on:contextmenu.prevent="window.SpellboundD20Gestures.right($event, mode => rollFeatureConcentration(mode))" :disabled="featureCheckRolling"
                            class="sb-button-soft rounded-lg border px-3 py-2 text-[8px] font-black uppercase tracking-[0.04em] disabled:opacity-40">
                            Rolar novamente
                        </button>
                        <button type="button" x-show="featureCheckManualMode || featureCheckRoll" x-cloak
                            x-on:click="confirmFeatureConcentration()" :disabled="featureCheckRolling || (!featureCheckManualMode && !featureCheckRoll)"
                            class="sb-button-primary rounded-lg px-3 py-2 text-[8px] font-black uppercase tracking-[0.04em] disabled:opacity-40"
                            x-text="featureCheckRolling && featureCheckRoll ? 'Registrando...' : 'Usar resultado'"></button>
                    </div>
                    <button type="button" x-show="featureCheckResolved" x-cloak x-on:click="closeFeatureCheck()"
                        class="sb-button-soft rounded-lg border px-4 py-2 text-[8px] font-black uppercase tracking-[0.06em]">
                        Fechar
                    </button>
                </footer>
            </section>
        </div>
    </template>
    {{-- Confirmação discreta ao ativar, sem confundir o sucesso com erro. --}}
    <template x-teleport="body">
        <div x-show="effectNotice" x-cloak data-theme="{{ $characterTheme }}"
            class="character-sheet-theme fixed bottom-5 left-1/2 -translate-x-1/2 z-[330] w-[min(92vw,360px)]"
            role="status" aria-live="polite">
            <div style="display:flex;align-items:center;gap:11px;padding:12px 14px;border-radius:13px;
                border:1px solid var(--theme-border,#cbb496);
                background:var(--theme-paper-strong,#fffdf8);
                color:var(--theme-text-primary,#34231b);
                box-shadow:0 12px 38px #0003;">
                <span style="display:grid;place-items:center;width:32px;height:32px;flex-shrink:0;
                    border-radius:50%;background:var(--theme-accent-soft,#e8dbca);
                    color:var(--theme-accent-strong,#53150f);font-size:17px;">✓</span>
                <div style="min-width:0;flex:1">
                    <strong style="display:block;font:900 13px/1.35 Georgia,serif;color:var(--theme-text-strong,#53150f);"
                        x-text="(effectNotice?.name || 'Habilidade') + ' ativada'"></strong>
                    <small style="display:block;margin-top:2px;font:600 10px/1.4 system-ui,sans-serif;color:var(--theme-text-secondary,#866b55);"
                        x-text="(effectNotice?.duration || 0) + ' rodadas' + (effectNotice?.concentration ? ' · Concentração' : '')"></small>
                </div>
                <button type="button" @click="effectNotice = null" aria-label="Dispensar aviso"
                    style="font-size:17px;color:var(--theme-text-secondary,#866b55);">×</button>
            </div>
        </div>
    </template>
</section>