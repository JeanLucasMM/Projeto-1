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

    class="character-features-v3 character-features-v4 character-features-v6 character-features-v8 character-features-v9 character-features-v10 character-features-v11"
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

            <span
                class="character-features-v3-count"
                x-text="features.length"
            ></span>
        </button>
    </div>

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
                    </div>

                    <div class="character-feature-v9-quick-wrap">
                        <p
                            x-show="feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v9-quick"
                            x-text="feature.data.quick_text"
                        ></p>

                        <p
                            x-show="!feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v9-quick-empty"
                        >
                            Sem informação rápida.
                        </p>
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
                    </div>

                    <div class="character-feature-v9-quick-wrap">
                        <p
                            x-show="feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v9-quick"
                            x-text="feature.data.quick_text"
                        ></p>

                        <p
                            x-show="!feature.data?.quick_text"
                            x-cloak
                            class="character-feature-v9-quick-empty"
                        >
                            Sem informação rápida.
                        </p>
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