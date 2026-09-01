@props([
    'character',
])

@php
    /*
    |--------------------------------------------------------------------------
    | DEFINIÇÕES
    |--------------------------------------------------------------------------
    */

    $abilityDefinitions = [
        'strength' => [
            'label' => 'Força',
            'short' => 'FOR',
        ],

        'intelligence' => [
            'label' => 'Inteligência',
            'short' => 'INT',
        ],

        'dexterity' => [
            'label' => 'Destreza',
            'short' => 'DES',
        ],

        'wisdom' => [
            'label' => 'Sabedoria',
            'short' => 'SAB',
        ],

        'constitution' => [
            'label' => 'Constituição',
            'short' => 'CON',
        ],

        'charisma' => [
            'label' => 'Carisma',
            'short' => 'CAR',
        ],
    ];

    /*
    | A ordem acima reproduz a leitura visual:
    |
    | FOR | INT
    | DES | SAB
    | CON | CAR
    */

    $skillDefinitions = [
        'acrobatics' => [
            'label' => 'Acrobacia',
            'ability' => 'dexterity',
        ],

        'animal_handling' => [
            'label' => 'Adestrar Animais',
            'ability' => 'wisdom',
        ],

        'arcana' => [
            'label' => 'Arcanismo',
            'ability' => 'intelligence',
        ],

        'athletics' => [
            'label' => 'Atletismo',
            'ability' => 'strength',
        ],

        'deception' => [
            'label' => 'Enganação',
            'ability' => 'charisma',
        ],

        'history' => [
            'label' => 'História',
            'ability' => 'intelligence',
        ],

        'insight' => [
            'label' => 'Intuição',
            'ability' => 'wisdom',
        ],

        'intimidation' => [
            'label' => 'Intimidação',
            'ability' => 'charisma',
        ],

        'investigation' => [
            'label' => 'Investigação',
            'ability' => 'intelligence',
        ],

        'medicine' => [
            'label' => 'Medicina',
            'ability' => 'wisdom',
        ],

        'nature' => [
            'label' => 'Natureza',
            'ability' => 'intelligence',
        ],

        'perception' => [
            'label' => 'Percepção',
            'ability' => 'wisdom',
        ],

        'performance' => [
            'label' => 'Atuação',
            'ability' => 'charisma',
        ],

        'persuasion' => [
            'label' => 'Persuasão',
            'ability' => 'charisma',
        ],

        'religion' => [
            'label' => 'Religião',
            'ability' => 'intelligence',
        ],

        'sleight_of_hand' => [
            'label' => 'Prestidigitação',
            'ability' => 'dexterity',
        ],

        'stealth' => [
            'label' => 'Furtividade',
            'ability' => 'dexterity',
        ],

        'survival' => [
            'label' => 'Sobrevivência',
            'ability' => 'wisdom',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | PROFICIÊNCIA
    |--------------------------------------------------------------------------
    */

    $level =
        max(
            1,
            (int) (
                $character->level
                ?? 1
            )
        );

    $calculatedProficiency = match (true) {
        $level >= 17 => 6,
        $level >= 13 => 5,
        $level >= 9 => 4,
        $level >= 5 => 3,
        default => 2,
    };

    $proficiencyBonus =
        (int) (
            $character->proficiency_bonus
            ?? $calculatedProficiency
        );

    /*
    |--------------------------------------------------------------------------
    | MODELOS CARREGADOS
    |--------------------------------------------------------------------------
    */

    $abilities =
        $character->abilities;

    $temporaryBonuses =
        is_array(
            $abilities?->temporary_bonuses
        )
            ? $abilities->temporary_bonuses
            : [];

    $abilityOverrides =
        is_array(
            $abilities?->overrides
        )
            ? $abilities->overrides
            : [];

    $savingThrows =
        $character
            ->savingThrows
            ->keyBy('ability');

    $skills =
        $character
            ->skills
            ->keyBy('skill');

    /*
    |--------------------------------------------------------------------------
    | ESTADO INICIAL PARA O ALPINE
    |--------------------------------------------------------------------------
    */

    $initialAbilities = [];

    foreach (
        $abilityDefinitions as
        $abilityKey => $definition
    ) {
        $baseScore =
            (int) (
                $abilities?->{$abilityKey}
                ?? 10
            );

        $temporaryBonus =
            (int) (
                $temporaryBonuses[
                    $abilityKey
                ]
                ?? 0
            );

        /*
         * O Bônus Extra representa o SCORE temporário inteiro.
         * Compatibilidade com temporary_bonuses antigo:
         * Base 12 + bônus antigo 4 => Bônus Extra 16.
         */
        $override =
            array_key_exists(
                $abilityKey,
                $abilityOverrides
            )
                ? (int) $abilityOverrides[$abilityKey]
                : (
                    $temporaryBonus !== 0
                        ? $baseScore + $temporaryBonus
                        : null
                );

        $save =
            $savingThrows->get(
                $abilityKey
            );

        $abilitySkills = [];

        foreach (
            $skillDefinitions as
            $skillKey => $skillDefinition
        ) {
            if (
                $skillDefinition['ability']
                !== $abilityKey
            ) {
                continue;
            }

            $skill =
                $skills->get(
                    $skillKey
                );

            $abilitySkills[$skillKey] = [
                'key' =>
                    $skillKey,

                'label' =>
                    $skillDefinition[
                        'label'
                    ],

                'proficient' =>
                    (bool) (
                        $skill?->proficient
                        ?? false
                    ),

                'expertise' =>
                    (bool) (
                        $skill?->expertise
                        ?? false
                    ),

                'bonus_override' =>
                    $skill?->bonus_override,

                'temporary_bonus' =>
                    (int) (
                        $skill?->temporary_bonus
                        ?? 0
                    ),
            ];
        }

        $initialAbilities[$abilityKey] = [
            'key' =>
                $abilityKey,

            'label' =>
                $definition['label'],

            'short' =>
                $definition['short'],

            'score' =>
                $baseScore,

            // Mantido apenas para compatibilidade do payload.
            'temporary_bonus' =>
                0,

            'override' =>
                $override,

            'saving_throw' => [
                'proficient' =>
                    (bool) (
                        $save?->proficient
                        ?? false
                    ),

                'bonus_override' =>
                    $save?->bonus_override,

                'temporary_bonus' =>
                    (int) (
                        $save?->temporary_bonus
                        ?? 0
                    ),
            ],

            'skills' =>
                $abilitySkills,
        ];
    }
    $exhaustionRuleActive =
        (bool) data_get(
            is_array($character->sheet_settings ?? null)
                ? $character->sheet_settings
                : [],
            'optional_rules.exhaustion',
            false
        );

    $initialExhaustionLevel =
        $exhaustionRuleActive
            ? min(
                6,
                max(
                    0,
                    (int) (
                        $character->combat?->exhaustion_level
                        ?? 0
                    )
                )
            )
            : 0;

@endphp


@once
    @push('styles')
        <style>
            /*
            |--------------------------------------------------------------------------
            | ATRIBUTOS — V2 UNIFICADO
            |--------------------------------------------------------------------------
            |
            | O show já fornece a folha. Este componente passa a se comportar
            | como conteúdo da folha, e não como uma segunda ficha dentro dela.
            |
            */

            .character-stats-sheet {
                position: relative;
                background: transparent;
            }

            .character-stats-sheet::before {
                display: none;
            }

            .character-stats-v2-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin: 0 2px 10px;
                padding: 0 2px 9px;
                border-bottom: 1px solid rgba(205,187,159,.28);
            }

            .character-stats-v2-header-copy {
                min-width: 0;
            }

            .character-stats-v2-kicker {
                display: block;
                margin-bottom: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: #a07855;
            }

            .character-stats-v2-title {
                font-family: Georgia, serif;
                font-size: 19px;
                font-weight: 900;
                line-height: 1.05;
                color: #53150f;
            }

            .character-stats-v2-hint {
                flex: 0 0 auto;
                font-size: 9px;
                font-weight: 800;
                color: #a07855;
            }

            .character-stats-v2-grid {
                gap: 8px !important;
                padding: 0 2px 2px !important;
            }

            .character-stats-v2-column {
                min-width: 0;
            }

            /*
            |--------------------------------------------------------------------------
            | PAINEL DE ATRIBUTO
            |--------------------------------------------------------------------------
            */

            .character-stat-panel {
                position: relative;
                overflow: hidden;
                align-self: flex-start;

                border: 0;
                border-radius: 11px;

                background:
                    linear-gradient(
                        180deg,
                        rgba(255,253,249,.72) 0%,
                        rgba(250,248,242,.46) 100%
                    );

                box-shadow:
                    inset 0 0 0 1px rgba(205,187,159,.44);

                transition:
                    background .14s ease,
                    box-shadow .14s ease;
            }

            .character-stat-panel:hover {
                background:
                    linear-gradient(
                        180deg,
                        rgba(255,253,249,.94) 0%,
                        rgba(247,243,234,.72) 100%
                    );

                box-shadow:
                    inset 0 0 0 1px rgba(140,98,57,.34);
            }

            .character-stat-panel::after,
            .character-stat-panel-corner {
                display: none;
            }

            .character-stat-panel-head {
                border-bottom:
                    1px solid
                    rgba(216,199,171,.44) !important;

                padding:
                    9px 9px 8px !important;
            }

            .character-stat-rule {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0;
            }

            .character-stat-rule::before,
            .character-stat-rule::after {
                display: none;
            }

            .character-stat-name-button {
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;

                font-size: 10px !important;
                letter-spacing: .10em !important;

                transition:
                    color .15s ease,
                    opacity .15s ease;
            }

            .character-stat-name-button:hover {
                color: #6b1d14;
                opacity: .72;
                letter-spacing: .10em !important;
            }

            .character-stat-roll-main {
                margin-top: 7px !important;
                gap: 8px !important;
                padding: 2px 2px !important;
            }

            .character-stat-roll-main:hover {
                background: transparent !important;
            }

            .character-stat-value-orb {
                width: 46px !important;
                height: 46px !important;

                border-color:
                    rgba(205,187,159,.82) !important;

                background:
                    rgba(255,253,249,.92) !important;

                box-shadow:
                    inset 0 0 0 3px rgba(239,233,220,.58),
                    0 1px 2px rgba(83,21,15,.04);
            }

            .character-stat-value-orb:hover {
                border-color:
                    rgba(107,29,20,.34) !important;
            }

            /*
            |--------------------------------------------------------------------------
            | SALVAGUARDAS E PERÍCIAS
            |--------------------------------------------------------------------------
            */

            .character-stat-row {
                min-height: 29px;
                padding:
                    6px 9px !important;

                border-bottom-color:
                    rgba(216,199,171,.30) !important;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .character-stat-row:hover {
                background:
                    rgba(239,233,220,.52);
            }

            .character-stat-row:focus-visible,
            .character-stat-roll-main:focus-visible,
            .character-stat-name-button:focus-visible {
                outline: 2px solid rgba(107,29,20,.25);
                outline-offset: -2px;
            }

            .character-stat-row .text-\[10\.5px\] {
                font-size: 10.5px !important;
            }

            .character-stat-row .text-\[9px\] {
                font-size: 10px !important;
            }

            .character-stat-row .text-\[11px\] {
                font-size: 11.5px !important;
            }

            .character-stat-training-dot {
                width: 10px;
                height: 10px;
                flex: none;

                border:
                    1px solid
                    rgba(140,98,57,.58);

                border-radius: 9999px;
                background: #fffdf9;
            }

            .character-stat-training-dot.proficient {
                border-color: #6b1d14;
                background: #6b1d14;

                box-shadow:
                    inset 0 0 0 2px #faf8f2;
            }

            .character-stat-training-dot.expertise {
                border-color: #53150f;

                background:
                    radial-gradient(
                        circle,
                        #53150f 0 30%,
                        #faf8f2 32% 50%,
                        #53150f 52% 100%
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL
            |--------------------------------------------------------------------------
            */

            .character-ability-modal-v2-backdrop {
                background:
                    rgba(8, 6, 5, 0.88) !important;

                backdrop-filter:
                    blur(2px) !important;

                -webkit-backdrop-filter:
                    blur(2px) !important;
            }

            .character-ability-modal-v2 {
                width: min(680px, calc(100vw - 28px)) !important;
                max-width: 680px !important;
                max-height: 88vh !important;

                border-color:
                    rgba(205,187,159,.90) !important;

                border-radius:
                    16px !important;

                background:
                    #faf8f2 !important;

                box-shadow:
                    0 22px 64px rgba(42,23,18,.22) !important;
            }

            .character-ability-modal-v2-header {
                background:
                    #f4f1e8 !important;

                border-bottom-color:
                    rgba(205,187,159,.55) !important;
            }

            .character-ability-modal-v2 .character-modal-tab {
                min-height: 39px;
                font-size: 10.5px !important;
                letter-spacing: .06em !important;

                border-bottom:
                    2px solid
                    transparent;

                transition:
                    color .15s ease,
                    background .15s ease,
                    border-color .15s ease;
            }

            .character-ability-modal-v2 .character-modal-tab.active {
                color: #53150f;
                border-bottom-color: #6b1d14;
                background: rgba(239,233,220,.62);
            }

            .character-ability-modal-v2-body {
                padding: 16px 18px 18px !important;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .character-ability-modal-v2-body::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .character-ability-modal-v2 .character-modal-card {
                border:
                    1px solid
                    rgba(216,199,171,.62);

                border-radius: 12px;

                background:
                    rgba(255,253,249,.74);

                box-shadow: none;
            }

            .character-ability-modal-v2 .character-modal-score {
                box-shadow:
                    inset 0 0 0 3px rgba(239,233,220,.62),
                    0 1px 2px rgba(83,21,15,.04);
            }

            /*
            | Evita textos microscópicos no editor.
            */

            .character-ability-modal-v2 p {
                font-size: 11.5px;
                line-height: 1.48;
            }

            .character-ability-modal-v2 label > span,
            .character-ability-modal-v2 label > span > span {
                font-size: 10px;
            }

            .character-ability-modal-v2 input,
            .character-ability-modal-v2 select,
            .character-ability-modal-v2 textarea {
                min-height: 39px;
                font-size: 14px !important;
                line-height: 1.35;
            }

            .character-ability-modal-v2 button {
                font-size: 10.5px;
            }

            .character-ability-modal-v2-footer {
                background:
                    #f4f1e8 !important;

                border-top-color:
                    rgba(205,187,159,.55) !important;
            }

            /*
            |--------------------------------------------------------------------------
            | TOAST DE ROLAGEM
            |--------------------------------------------------------------------------
            */

            .character-roll-toast {
                border-color:
                    rgba(205,187,159,.84) !important;

                background:
                    #faf8f2 !important;

                box-shadow:
                    0 16px 42px rgba(43,29,23,.16),
                    0 2px 8px rgba(83,21,15,.08);
            }

            .character-roll-toast p:first-child {
                font-size: 11px !important;
            }

            .character-roll-toast p + p {
                font-size: 10px !important;
            }

            @media (max-width: 360px) {
                .character-stats-v2-hint {
                    display: none;
                }

                .character-stats-v2-grid {
                    grid-template-columns:
                        minmax(0, 1fr) !important;
                }
            }
        


            /*
            |--------------------------------------------------------------------------
            | V3 — PALETA DO HEADER
            |--------------------------------------------------------------------------
            |
            | A coluna já recebe o bege avermelhado do show.
            | Cada atributo é informação principal, então usa papel branco quente.
            |
            */

            .character-stats-v2-header {
                border-bottom-color:
                    rgba(160,119,77,.30);
            }

            .character-stats-v2-kicker,
            .character-stats-v2-hint {
                color:
                    #906541;
            }

            /*
            |--------------------------------------------------------------------------
            | ATRIBUTO
            |--------------------------------------------------------------------------
            */

            .character-stat-panel {
                border-radius:
                    8px;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf8 0%,
                        #f8f1e7 100%
                    );

                box-shadow:
                    inset 0 0 0 1px rgba(177,140,97,.38),
                    0 1px 2px rgba(83,21,15,.025);
            }

            .character-stat-panel:hover {
                background:
                    #fffdf8;

                box-shadow:
                    inset 0 0 0 1px rgba(107,29,20,.27),
                    0 2px 5px rgba(83,21,15,.035);
            }

            .character-stat-panel-head {
                border-bottom-color:
                    rgba(177,140,97,.30) !important;

                background:
                    linear-gradient(
                        180deg,
                        rgba(250,246,238,.82) 0%,
                        rgba(244,235,222,.62) 100%
                    );
            }

            .character-stat-value-orb {
                border-color:
                    rgba(171,132,90,.56) !important;

                background:
                    #fffdf9 !important;

                box-shadow:
                    inset 0 0 0 3px rgba(235,221,202,.62),
                    0 1px 2px rgba(83,21,15,.035);
            }

            /*
            |--------------------------------------------------------------------------
            | SALVAMENTO / PERÍCIAS
            |--------------------------------------------------------------------------
            */

            .character-stat-row {
                border-bottom-color:
                    rgba(184,149,106,.24) !important;

                background:
                    rgba(255,253,248,.58);
            }

            .character-stat-row:nth-child(even) {
                background:
                    rgba(247,239,228,.54);
            }

            .character-stat-row:hover {
                background:
                    #f1e2d0;
            }

            .character-stat-training-dot {
                border-color:
                    rgba(140,98,57,.62);

                background:
                    #fffdf8;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL
            |--------------------------------------------------------------------------
            */

            .character-ability-modal-v2 {
                border-color:
                    rgba(176,140,98,.70) !important;

                background:
                    #fbf8f1 !important;
            }

            .character-ability-modal-v2-header,
            .character-ability-modal-v2-footer {
                background:
                    #eadbc8 !important;

                border-color:
                    rgba(160,119,77,.34) !important;
            }

            .character-ability-modal-v2 .character-modal-card {
                border-color:
                    rgba(188,154,111,.48);

                background:
                    linear-gradient(
                        180deg,
                        #fffdf8 0%,
                        #f7eee2 100%
                    );
            }

            .character-ability-modal-v2 .character-modal-tab.active {
                background:
                    rgba(255,253,248,.62);
            }

            .character-roll-toast {
                border-color:
                    rgba(176,140,98,.64) !important;

                background:
                    #fbf8f1 !important;
            }
        
            /*
            |--------------------------------------------------------------------------
            | V5 — ATRIBUTOS / FICHA ORNAMENTAL
            |--------------------------------------------------------------------------
            |
            | A referência física é usada só como linguagem:
            | moldura, hierarquia do modificador e separação clara.
            | A paleta continua sendo a do site.
            |
            */

            .character-stats-v5 {
                width: 100%;
                color: #432c21;
            }

            /* CABEÇALHO */

            .character-stats-v5 .character-stats-v2-header {
                min-height: 49px;
                align-items: flex-end;

                margin: 0 1px 11px;

                border-bottom:
                    1px solid
                    rgba(160,119,77,.34);

                padding:
                    0 2px 9px;
            }

            .character-stats-v5 .character-stats-v2-kicker {
                margin-bottom: 3px;
                font-size: 8.5px;
                letter-spacing: .14em;
                color: #8c6239;
            }

            .character-stats-v5 .character-stats-v2-title {
                font-size: 19px;
                line-height: 1;
            }

            .character-stats-v5-proficiency {
                display: inline-flex;
                min-height: 34px;
                align-items: center;
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 8px;
                background: #fbf8f1;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.72);
            }

            .character-stats-v5-proficiency-label {
                display: flex;
                height: 32px;
                align-items: center;

                border-right:
                    1px solid
                    rgba(176,140,98,.28);

                background: #eadbc8;
                padding: 0 7px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;

                color: #6f472f;
            }

            .character-stats-v5-proficiency strong {
                min-width: 34px;
                padding: 0 7px;

                text-align: center;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;

                color: #53150f;
            }

            /* GRID */

            .character-stats-v5 .character-stats-v2-grid {
                gap: 9px !important;
                padding: 0 1px 2px !important;
            }

            .character-stats-v5 .character-stats-v2-column {
                gap: 9px !important;
            }

            /* PAINEL */

            .character-stats-v5 .character-stat-panel {
                position: relative;
                overflow: hidden;

                border-radius: 9px;

                background: #fbf8f1 !important;

                box-shadow:
                    inset 0 0 0 1px rgba(176,140,98,.34),
                    inset 0 1px 0 rgba(255,255,255,.72),
                    0 1px 2px rgba(83,21,15,.025) !important;

                transition:
                    box-shadow .14s ease,
                    background .14s ease;
            }

            .character-stats-v5 .character-stat-panel:hover {
                background: #fffdf8 !important;

                box-shadow:
                    inset 0 0 0 1px rgba(140,98,57,.46),
                    inset 0 1px 0 rgba(255,255,255,.78),
                    0 3px 8px rgba(83,21,15,.035) !important;
            }

            /* CANTOS ORNAMENTAIS */

            .character-stats-v5 .character-stat-panel-corner {
                position: absolute;
                z-index: 20;

                display: block !important;

                width: 9px;
                height: 9px;

                pointer-events: none;
                opacity: .36;
            }

            .character-stats-v5 .character-stat-panel-corner.tl {
                top: 4px;
                left: 4px;
                border-top: 1px solid #8c6239;
                border-left: 1px solid #8c6239;
            }

            .character-stats-v5 .character-stat-panel-corner.tr {
                top: 4px;
                right: 4px;
                border-top: 1px solid #8c6239;
                border-right: 1px solid #8c6239;
            }

            .character-stats-v5 .character-stat-panel-corner.bl {
                bottom: 4px;
                left: 4px;
                border-bottom: 1px solid #8c6239;
                border-left: 1px solid #8c6239;
            }

            .character-stats-v5 .character-stat-panel-corner.br {
                right: 4px;
                bottom: 4px;
                border-right: 1px solid #8c6239;
                border-bottom: 1px solid #8c6239;
            }

            /* CABEÇALHO DO ATRIBUTO */

            .character-stats-v5 .character-stat-panel-head {
                min-height: 100px;

                border-bottom:
                    1px solid
                    rgba(176,140,98,.28) !important;

                background:
                    linear-gradient(
                        180deg,
                        #f9f4ec 0%,
                        #f4e9db 100%
                    ) !important;

                padding:
                    10px 8px 11px !important;
            }

            .character-stats-v5 .character-stat-rule {
                gap: 7px;
            }

            .character-stats-v5 .character-stat-rule::before,
            .character-stats-v5 .character-stat-rule::after {
                content: '';

                display: block !important;

                width: 100%;
                max-width: 23px;
                height: 1px;

                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        rgba(140,98,57,.34)
                    );
            }

            .character-stats-v5 .character-stat-rule::after {
                transform: scaleX(-1);
            }

            .character-stats-v5 .character-stat-name-button {
                flex: 0 1 auto;

                font-size: 9.5px !important;
                letter-spacing: .105em !important;

                color: #53150f;
            }

            /* MODIFICADOR + SCORE */

            .character-stats-v5 .character-stat-roll-main {
                position: relative;
                min-height: 64px;

                margin-top: 8px !important;
                gap: 7px !important;
                padding: 3px 1px 0 !important;
            }

            .character-stats-v5 .character-stat-roll-main:hover {
                background: transparent !important;
            }

            .character-stats-v5 .character-stat-value-orb {
                position: relative;

                width: 54px !important;
                height: 54px !important;

                border:
                    1px solid
                    rgba(176,140,98,.52) !important;

                background: #fffdf8 !important;

                box-shadow:
                    inset 0 0 0 4px rgba(234,219,200,.60),
                    0 2px 5px rgba(83,21,15,.045) !important;
            }

            .character-stats-v5 .character-stat-value-orb::after {
                content: 'MOD.';

                position: absolute;
                right: 0;
                bottom: -10px;
                left: 0;

                text-align: center;

                font-size: 5.5px;
                font-weight: 900;
                letter-spacing: .08em;

                color: #9a7453;
            }

            .character-stats-v5
            .character-stat-value-orb
            > span {
                font-size: 24px !important;
            }

            .character-stats-v5
            .character-stat-roll-main
            > span:last-child {
                min-width: 40px;
            }

            .character-stats-v5
            .character-stat-roll-main
            > span:last-child
            > span:first-child {
                font-size: 22px !important;
                color: #6b1d14;
            }

            .character-stats-v5
            .character-stat-roll-main
            > span:last-child
            > span:last-child {
                margin-top: 3px;
                font-size: 6px !important;
                letter-spacing: .10em;
                color: #8c6239;
            }

            /* SALVAGUARDA */

            .character-stats-v5 .character-stat-save-row {
                position: relative;

                min-height: 32px;

                border-top:
                    1px solid
                    rgba(255,255,255,.46);

                border-bottom:
                    1px solid
                    rgba(160,119,77,.30) !important;

                background: #eadbc8 !important;

                padding:
                    6px 9px !important;

                color: #53150f;
            }

            .character-stats-v5 .character-stat-save-row::before {
                content: '';

                position: absolute;
                top: 6px;
                bottom: 6px;
                left: 0;

                width: 3px;

                border-radius:
                    0 999px 999px 0;

                background:
                    rgba(107,29,20,.42);
            }

            .character-stats-v5 .character-stat-save-row:hover {
                background: #e4d0b9 !important;
            }

            .character-stats-v5 .character-stat-save-row.is-proficient {
                background: #e7d2bb !important;
            }

            .character-stats-v5
            .character-stat-save-row
            .character-stat-training-dot {
                border-color:
                    rgba(107,29,20,.62);
            }

            .character-stats-v5
            .character-stat-save-row
            span:nth-child(2) {
                font-size: 10.5px !important;
                color: #53150f !important;
            }

            .character-stats-v5
            .character-stat-save-row
            span:last-child {
                font-size: 12px !important;
                color: #6b1d14 !important;
            }

            /* PERÍCIAS */

            .character-stats-v5
            .character-stat-row:not(.character-stat-save-row) {
                min-height: 29px;

                border-bottom-color:
                    rgba(188,154,111,.24) !important;

                background:
                    rgba(255,253,248,.78) !important;

                padding:
                    5px 9px !important;
            }

            .character-stats-v5
            .character-stat-row:not(.character-stat-save-row):nth-child(even) {
                background:
                    rgba(247,240,230,.70) !important;
            }

            .character-stats-v5
            .character-stat-row:not(.character-stat-save-row):hover {
                background:
                    #f4e8d8 !important;
            }

            .character-stats-v5
            .character-stat-row.is-trained {
                background:
                    rgba(245,235,222,.90) !important;
            }

            .character-stats-v5
            .character-stat-row.is-expertise {
                background:
                    rgba(238,222,203,.94) !important;
            }

            .character-stats-v5
            .character-stat-row:not(.character-stat-save-row)
            .text-\[9px\] {
                font-size: 9.5px !important;
                color: #5d4333 !important;
            }

            .character-stats-v5
            .character-stat-row:not(.character-stat-save-row)
            .text-\[11px\] {
                font-size: 11px !important;
                color: #8c6239 !important;
            }

            /* PROFICIÊNCIA / ESPECIALIZAÇÃO */

            .character-stats-v5 .character-stat-training-dot {
                width: 10px;
                height: 10px;

                border-color:
                    rgba(140,98,57,.54);

                background: #fffdf8;
            }

            .character-stats-v5
            .character-stat-training-dot.proficient {
                border-color: #6b1d14;
                background: #6b1d14;

                box-shadow:
                    inset 0 0 0 2px #fffdf8;
            }

            .character-stats-v5
            .character-stat-training-dot.expertise {
                border-color: #53150f;

                background:
                    radial-gradient(
                        circle,
                        #53150f 0 29%,
                        #fffdf8 31% 49%,
                        #53150f 51% 100%
                    );
            }

            @media (max-width: 360px) {
                .character-stats-v5-proficiency {
                    display: none;
                }

                .character-stats-v5 .character-stat-panel-head {
                    min-height: 94px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | V6 — ATRIBUTOS SIMPLES / MESMA LINGUAGEM DE ATTACK
            |--------------------------------------------------------------------------
            |
            | Menos ornamento, mais leitura.
            | O cartão continua lembrando uma ficha, mas usa a mesma geometria
            | e a mesma paleta dos outros partials.
            |
            */

            .character-stats-v6 {
                width: 100%;
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO
            |--------------------------------------------------------------------------
            */

            .character-stats-v6 .character-stats-v2-header {
                min-height: 46px;

                align-items: center;

                margin:
                    0 1px 10px;

                border-bottom:
                    1px solid
                    rgba(168,132,91,.32);

                padding:
                    0 2px 8px;
            }

            .character-stats-v6 .character-stats-v2-kicker {
                margin-bottom: 3px;

                font-size: 8.5px;
                letter-spacing: .13em;

                color: #8c6239;
            }

            .character-stats-v6 .character-stats-v2-title {
                font-size: 19px;
                line-height: 1;
            }

            /*
            | Proficiência mais discreta.
            */
            .character-stats-v6 .character-stats-v5-proficiency {
                min-height: 32px;

                border-color:
                    rgba(176,140,98,.32);

                border-radius: 7px;

                background: #fbf8f1;

                box-shadow: none;
            }

            .character-stats-v6 .character-stats-v5-proficiency-label {
                height: 30px;

                border-right-color:
                    rgba(160,119,77,.24);

                background: #eadbc8;

                padding:
                    0 7px;

                font-size: 7.5px;
            }

            .character-stats-v6 .character-stats-v5-proficiency strong {
                min-width: 32px;

                padding:
                    0 6px;

                font-size: 14px;
            }

            /*
            |--------------------------------------------------------------------------
            | GRID
            |--------------------------------------------------------------------------
            */

            .character-stats-v6 .character-stats-v2-grid {
                gap:
                    8px !important;

                padding:
                    0 1px 2px !important;
            }

            .character-stats-v6 .character-stats-v2-column {
                gap:
                    8px !important;
            }

            /*
            |--------------------------------------------------------------------------
            | CARD
            |--------------------------------------------------------------------------
            */

            .character-stats-v6 .character-stat-panel {
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.32);

                border-radius: 8px;

                background:
                    #fbf8f1 !important;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.66) !important;
            }

            .character-stats-v6 .character-stat-panel:hover {
                border-color:
                    rgba(140,98,57,.42);

                background:
                    #fffdf8 !important;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.72) !important;
            }

            /*
            | Remove os quatro cantos decorativos.
            | Eles estavam deixando a coluna mais carregada que Attack/Features.
            */
            .character-stats-v6 .character-stat-panel-corner {
                display: none !important;
            }

            /*
            |--------------------------------------------------------------------------
            | NOME + MODIFICADOR
            |--------------------------------------------------------------------------
            */

            .character-stats-v6 .character-stat-panel-head {
                min-height: 91px;

                border-bottom:
                    1px solid
                    rgba(176,140,98,.26) !important;

                background:
                    #f7f0e6 !important;

                padding:
                    9px 8px 9px !important;
            }

            /*
            | Sem linhas laterais: o nome ganha toda a largura disponível.
            */
            .character-stats-v6 .character-stat-rule {
                display: block;
            }

            .character-stats-v6 .character-stat-rule::before,
            .character-stats-v6 .character-stat-rule::after {
                display: none !important;
            }

            .character-stats-v6 .character-stat-name-button {
                display: block;

                width: 100%;
                max-width: none;

                min-height: 24px;

                overflow: visible;

                padding:
                    1px 2px;

                white-space: normal !important;
                overflow-wrap: normal;
                word-break: normal;
                text-overflow: clip;

                text-align: center;

                font-size: 9px !important;
                font-weight: 900;
                line-height: 1.18;
                letter-spacing: .085em !important;

                color: #53150f;
            }

            .character-stats-v6 .character-stat-name-button:hover {
                opacity: 1;
                color: #6b1d14;
            }

            .character-stats-v6 .character-stat-roll-main {
                min-height: 55px;

                margin-top:
                    4px !important;

                gap:
                    7px !important;

                padding:
                    0 !important;
            }

            .character-stats-v6 .character-stat-value-orb {
                width:
                    48px !important;

                height:
                    48px !important;

                border-color:
                    rgba(176,140,98,.45) !important;

                background:
                    #fffdf8 !important;

                box-shadow:
                    inset 0 0 0 3px rgba(234,219,200,.56) !important;
            }

            /*
            | Remove a etiqueta MOD. extra; o layout fica mais limpo.
            */
            .character-stats-v6 .character-stat-value-orb::after {
                display: none;
            }

            .character-stats-v6
            .character-stat-value-orb
            > span {
                font-size:
                    22px !important;
            }

            .character-stats-v6
            .character-stat-roll-main
            > span:last-child {
                min-width: 37px;
            }

            .character-stats-v6
            .character-stat-roll-main
            > span:last-child
            > span:first-child {
                font-size:
                    19px !important;
            }

            .character-stats-v6
            .character-stat-roll-main
            > span:last-child
            > span:last-child {
                margin-top: 3px;

                font-size:
                    6px !important;
            }

            /*
            |--------------------------------------------------------------------------
            | SALVAMENTO
            |--------------------------------------------------------------------------
            |
            | É a linha importante do atributo, então usa a mesma faixa bege
            | que aparece nos cabeçalhos de Attack/Features.
            |
            */

            .character-stats-v6 .character-stat-save-row {
                min-height: 31px;

                border-top:
                    0;

                border-bottom:
                    1px solid
                    rgba(160,119,77,.28) !important;

                background:
                    #eadbc8 !important;

                padding:
                    5px 8px !important;
            }

            .character-stats-v6 .character-stat-save-row::before {
                top: 7px;
                bottom: 7px;

                width: 2px;

                background:
                    rgba(107,29,20,.36);
            }

            .character-stats-v6 .character-stat-save-row:hover {
                background:
                    #e4d0b9 !important;
            }

            .character-stats-v6 .character-stat-save-row.is-proficient {
                background:
                    #e5cfb7 !important;
            }

            .character-stats-v6
            .character-stat-save-row
            span:nth-child(2) {
                font-size:
                    10px !important;
            }

            .character-stats-v6
            .character-stat-save-row
            span:last-child {
                font-size:
                    11.5px !important;
            }

            /*
            |--------------------------------------------------------------------------
            | PERÍCIAS
            |--------------------------------------------------------------------------
            */

            .character-stats-v6
            .character-stat-row:not(.character-stat-save-row) {
                min-height: 28px;

                border-bottom-color:
                    rgba(188,154,111,.22) !important;

                background:
                    rgba(255,253,248,.80) !important;

                padding:
                    5px 8px !important;
            }

            .character-stats-v6
            .character-stat-row:not(.character-stat-save-row):nth-child(even) {
                background:
                    rgba(247,240,230,.72) !important;
            }

            .character-stats-v6
            .character-stat-row:not(.character-stat-save-row):hover {
                background:
                    #f4e8d8 !important;
            }

            /*
            | Proficiência/expertise aparece principalmente pelo marcador.
            | Evita tingir a linha inteira e poluir o card.
            */
            .character-stats-v6
            .character-stat-row.is-trained,
            .character-stats-v6
            .character-stat-row.is-expertise {
                background:
                    inherit !important;
            }

            .character-stats-v6
            .character-stat-row:not(.character-stat-save-row)
            .text-\[9px\] {
                font-size:
                    9.5px !important;

                color:
                    #5d4333 !important;
            }

            .character-stats-v6
            .character-stat-row:not(.character-stat-save-row)
            .text-\[11px\] {
                font-size:
                    10.5px !important;

                color:
                    #8c6239 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | MARCADORES
            |--------------------------------------------------------------------------
            */

            .character-stats-v6 .character-stat-training-dot {
                width: 9px;
                height: 9px;

                border-color:
                    rgba(140,98,57,.52);

                background:
                    #fffdf8;
            }

            .character-stats-v6
            .character-stat-training-dot.proficient {
                border-color: #6b1d14;
                background: #6b1d14;

                box-shadow:
                    inset 0 0 0 2px #fffdf8;
            }

            .character-stats-v6
            .character-stat-training-dot.expertise {
                border-color: #53150f;

                background:
                    radial-gradient(
                        circle,
                        #53150f 0 29%,
                        #fffdf8 31% 49%,
                        #53150f 51% 100%
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 360px) {
                .character-stats-v6
                .character-stat-panel-head {
                    min-height: 88px;
                }

                .character-stats-v6
                .character-stat-name-button {
                    font-size:
                        8.5px !important;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | V7 — CABEÇALHOS MAIS COMPACTOS
            |--------------------------------------------------------------------------
            |
            | Mantém exatamente a tipografia e o tamanho do modificador/score.
            | A redução acontece só em espaços verticais, para aproximar:
            |
            | NOME DO ATRIBUTO
            |       ↓
            | MODIFICADOR + VALOR
            |
            | e também aproximar o título "Atributos" dos cartões.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | TÍTULO DA SEÇÃO
            |--------------------------------------------------------------------------
            */

            .character-stats-v7 .character-stats-v2-header {
                min-height:
                    40px;

                margin:
                    0 1px 1px;

                padding:
                    0 2px 6px;
            }

            /*
            |--------------------------------------------------------------------------
            | GRID
            |--------------------------------------------------------------------------
            */

            .character-stats-v7 .character-stats-v2-grid {
                gap:
                    7px !important;
            }

            .character-stats-v7 .character-stats-v2-column {
                gap:
                    7px !important;
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO DO ATRIBUTO
            |--------------------------------------------------------------------------
            |
            | Antes o bloco reservava 91px.
            | Agora ele fica próximo de 78px sem reduzir:
            |
            | - nome;
            | - círculo;
            | - modificador;
            | - score.
            |
            */

            .character-stats-v7 .character-stat-panel-head {
                min-height:
                    78px;

                padding:
                    6px 8px 6px !important;
            }

            /*
            |--------------------------------------------------------------------------
            | NOME
            |--------------------------------------------------------------------------
            */

            .character-stats-v7 .character-stat-name-button {
                min-height:
                    16px;

                padding:
                    0 2px 1px;

                line-height:
                    1.1;
            }

            /*
            |--------------------------------------------------------------------------
            | MODIFICADOR + VALOR
            |--------------------------------------------------------------------------
            |
            | O círculo continua 48x48.
            | Apenas removemos a área vazia entre nome e números.
            |
            */

            .character-stats-v7 .character-stat-roll-main {
                min-height:
                    48px;

                margin-top:
                    0 !important;

                padding:
                    0 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 360px) {
                .character-stats-v7
                .character-stat-panel-head {
                    min-height:
                        76px;
                }
            }

        </style>
    @endpush


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data(
                'characterAbilitiesPanel',
                (
                    initialAbilities,
                    proficiencyBonus,
                    initialExhaustionLevel,
                    saveUrlTemplate,
                    diceRollUrl
                ) => ({
                    abilities: initialAbilities,
                    proficiencyBonus: parseInt(proficiencyBonus) || 0,

                    exhaustionLevel:
                        Math.min(
                            6,
                            Math.max(
                                0,
                                parseInt(
                                    initialExhaustionLevel
                                ) || 0
                            )
                        ),

                    saveUrlTemplate,
                    diceRollUrl,

                    modalOpen: false,
                    activeAbility: null,
                    activeTab: 'ability',
                    draft: null,
                    originalScore: null,
                    saving: false,
                    saveError: null,
                    confirmScoreChange: false,

                    rolling: false,
                    lastRoll: null,
                    rollToastOpen: false,
                    rollToastTimer: null,


                    get exhaustionRollPenalty() {
                        return Math.max(
                            0,
                            this.exhaustionLevel * 2
                        );
                    },


                    syncExhaustion(payload) {
                        const level =
                            payload?.level
                            ?? 0;

                        this.exhaustionLevel =
                            Math.min(
                                6,
                                Math.max(
                                    0,
                                    parseInt(level) || 0
                                )
                            );
                    },


                    get current() {
                        return this.activeAbility
                            ? (this.abilities[this.activeAbility] ?? null)
                            : null;
                    },

                    get draftEffectiveScore() {
                        return this.effectiveScoreFrom(this.draft);
                    },

                    get draftBaseModifier() {
                        return this.modifierFromScore(this.draft?.score ?? 10);
                    },

                    get draftEffectiveModifier() {
                        return this.modifierFromScore(this.draftEffectiveScore);
                    },

                    get permanentScoreChanged() {
                        if (!this.draft) {
                            return false;
                        }

                        return (parseInt(this.draft.score) || 10)
                            !== (parseInt(this.originalScore) || 10);
                    },

                    signed(value) {
                        const number = parseInt(value) || 0;
                        return number >= 0 ? `+${number}` : `${number}`;
                    },

                    modifierFromScore(score) {
                        return Math.floor(((parseInt(score) || 10) - 10) / 2);
                    },

                    effectiveScoreFrom(ability) {
                        if (!ability) {
                            return 10;
                        }

                        if (
                            ability.override !== null &&
                            ability.override !== '' &&
                            !Number.isNaN(parseInt(ability.override))
                        ) {
                            return Math.max(1, parseInt(ability.override));
                        }

                        return Math.max(1, parseInt(ability.score) || 10);
                    },

                    currentScore(abilityKey) {
                        return this.effectiveScoreFrom(this.abilities[abilityKey]);
                    },

                    modifier(abilityKey) {
                        return this.modifierFromScore(this.currentScore(abilityKey));
                    },

                    savingThrowTotalFrom(ability) {
                        if (!ability) {
                            return 0;
                        }

                        const save = ability.saving_throw;
                        const temporary = parseInt(save.temporary_bonus) || 0;

                        if (
                            save.bonus_override !== null &&
                            save.bonus_override !== '' &&
                            !Number.isNaN(parseInt(save.bonus_override))
                        ) {
                            return parseInt(save.bonus_override) + temporary;
                        }

                        return this.modifierFromScore(this.effectiveScoreFrom(ability))
                            + (save.proficient ? this.proficiencyBonus : 0)
                            + temporary;
                    },

                    savingThrowTotal(abilityKey) {
                        return this.savingThrowTotalFrom(this.abilities[abilityKey]);
                    },

                    skillTotalFrom(ability, skill) {
                        if (!ability || !skill) {
                            return 0;
                        }

                        const temporary = parseInt(skill.temporary_bonus) || 0;

                        if (
                            skill.bonus_override !== null &&
                            skill.bonus_override !== '' &&
                            !Number.isNaN(parseInt(skill.bonus_override))
                        ) {
                            return parseInt(skill.bonus_override) + temporary;
                        }

                        let training = 0;

                        if (skill.expertise) {
                            training = this.proficiencyBonus * 2;
                        } else if (skill.proficient) {
                            training = this.proficiencyBonus;
                        }

                        return this.modifierFromScore(this.effectiveScoreFrom(ability))
                            + training
                            + temporary;
                    },

                    skillTotal(abilityKey, skillKey) {
                        const ability = this.abilities[abilityKey];
                        const skill = ability?.skills?.[skillKey];

                        return this.skillTotalFrom(ability, skill);
                    },

                    trainingClass(skill) {
                        if (skill?.expertise) return 'expertise';
                        if (skill?.proficient) return 'proficient';
                        return '';
                    },

                    skillTrainingMode(skill) {
                        if (skill?.expertise) return 'expertise';
                        if (skill?.proficient) return 'proficient';
                        return 'none';
                    },

                    setSkillTraining(skill, mode) {
                        if (mode === 'expertise') {
                            skill.proficient = true;
                            skill.expertise = true;
                            return;
                        }

                        if (mode === 'proficient') {
                            skill.proficient = true;
                            skill.expertise = false;
                            return;
                        }

                        skill.proficient = false;
                        skill.expertise = false;
                    },

                    openAbility(abilityKey, tab = 'ability') {
                        const source = this.abilities[abilityKey];

                        if (!source) {
                            return;
                        }

                        this.activeAbility = abilityKey;
                        this.activeTab = tab;
                        this.draft = JSON.parse(JSON.stringify(source));
                        this.originalScore = parseInt(source.score) || 10;
                        this.saveError = null;
                        this.confirmScoreChange = false;
                        this.modalOpen = true;
                    },

                    closeModal() {
                        if (this.saving) {
                            return;
                        }

                        this.modalOpen = false;
                        this.activeAbility = null;
                        this.activeTab = 'ability';
                        this.draft = null;
                        this.originalScore = null;
                        this.saveError = null;
                        this.confirmScoreChange = false;
                    },

                    setTab(tab) {
                        this.activeTab = tab;
                        this.saveError = null;
                    },

                    clearExtraScore() {
                        if (this.draft) {
                            this.draft.override = null;
                        }
                    },

                    async rollCheck(label, modifier) {
                        if (this.rolling) {
                            return;
                        }

                        const bonus =
                            parseInt(modifier) || 0;

                        const exhaustionPenalty =
                            this.exhaustionRollPenalty;

                        const effectiveBonus =
                            bonus
                            - exhaustionPenalty;

                        const expression =
                            effectiveBonus > 0
                                ? `1d20+${effectiveBonus}`
                                : (
                                    effectiveBonus < 0
                                        ? `1d20${effectiveBonus}`
                                        : '1d20'
                                );

                        this.rolling = true;

                        let total = null;
                        let die = null;
                        let formatted = null;

                        try {
                            const response = await fetch(this.diceRollUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN':
                                        document
                                            .querySelector('meta[name="csrf-token"]')
                                            ?.getAttribute('content')
                                        ?? '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ expression }),
                            });

                            const data = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                throw new Error(
                                    data?.message ?? 'Não foi possível rolar.'
                                );
                            }

                            total = parseInt(
                                data?.data?.total
                                ?? data?.data?.result
                                ?? data?.data?.value
                                ?? data?.data?.sum
                                ?? data?.total
                                ?? data?.result
                            );

                            formatted = data?.formatted ?? null;

                            if (Number.isNaN(total)) {
                                total = null;
                            }

                            if (total !== null) {
                                die = total - effectiveBonus;
                            }
                        } catch (error) {
                            console.error('Erro ao rolar dado:', error);

                            die = Math.floor(Math.random() * 20) + 1;
                            total = die + effectiveBonus;
                        } finally {
                            this.rolling = false;
                        }

                        this.lastRoll = {
                            label,
                            modifier:
                                bonus,
                            exhaustionPenalty:
                                exhaustionPenalty,
                            effectiveModifier:
                                effectiveBonus,
                            die,
                            total,
                            formatted,
                        };

                        this.rollToastOpen = true;

                        window.dispatchEvent(
                            new CustomEvent('character-dice-rolled', {
                                detail: this.lastRoll,
                            })
                        );

                        if (this.rollToastTimer) {
                            clearTimeout(this.rollToastTimer);
                        }

                        this.rollToastTimer = setTimeout(() => {
                            this.rollToastOpen = false;
                        }, 4200);
                    },

                    requestSave() {
                        if (!this.draft || this.saving) {
                            return;
                        }

                        if (this.permanentScoreChanged) {
                            this.confirmScoreChange = true;
                            return;
                        }

                        this.persistCurrent();
                    },

                    confirmPermanentScoreChange() {
                        this.confirmScoreChange = false;
                        this.persistCurrent();
                    },

                    async persistCurrent() {
                        if (!this.draft || !this.activeAbility || this.saving) {
                            return;
                        }

                        this.saving = true;
                        this.saveError = null;

                        const abilityKey = this.activeAbility;
                        const ability = this.draft;

                        const payload = {
                            ability: {
                                score: Math.max(1, parseInt(ability.score) || 10),
                                temporary_bonus: 0,
                                override:
                                    ability.override === null ||
                                    ability.override === ''
                                        ? null
                                        : Math.max(
                                            1,
                                            parseInt(ability.override) || 1
                                        ),
                            },

                            saving_throw: {
                                proficient: !!ability.saving_throw.proficient,
                                bonus_override:
                                    ability.saving_throw.bonus_override === null ||
                                    ability.saving_throw.bonus_override === ''
                                        ? null
                                        : parseInt(
                                            ability.saving_throw.bonus_override
                                        ),
                                temporary_bonus:
                                    parseInt(
                                        ability.saving_throw.temporary_bonus
                                    ) || 0,
                            },

                            skills: Object.values(ability.skills).map(skill => ({
                                skill: skill.key,
                                proficient: !!skill.proficient,
                                expertise: !!skill.expertise,
                                bonus_override:
                                    skill.bonus_override === null ||
                                    skill.bonus_override === ''
                                        ? null
                                        : parseInt(skill.bonus_override),
                                temporary_bonus:
                                    parseInt(skill.temporary_bonus) || 0,
                            })),
                        };

                        try {
                            const response = await fetch(
                                this.saveUrlTemplate.replace(
                                    '__ABILITY__',
                                    abilityKey
                                ),
                                {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN':
                                            document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                ?.getAttribute('content')
                                            ?? '{{ csrf_token() }}',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    body: JSON.stringify(payload),
                                }
                            );

                            const data = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                const validationMessages = data?.errors
                                    ? Object.values(data.errors)
                                        .flat()
                                        .filter(Boolean)
                                    : [];

                                throw new Error(
                                    validationMessages.length
                                        ? validationMessages.join(' ')
                                        : (
                                            data?.message
                                            ?? 'Não foi possível salvar.'
                                        )
                                );
                            }

                            this.draft.score = payload.ability.score;
                            this.draft.temporary_bonus = 0;
                            this.draft.override = payload.ability.override;

                            this.abilities[abilityKey] =
                                JSON.parse(JSON.stringify(this.draft));

                            /*
                             * Fechamento manual porque closeModal()
                             * bloqueia enquanto saving === true.
                             */
                            this.modalOpen = false;
                            this.activeAbility = null;
                            this.activeTab = 'ability';
                            this.draft = null;
                            this.originalScore = null;
                            this.saveError = null;
                            this.confirmScoreChange = false;

                        } catch (error) {
                            console.error('Erro ao salvar atributo:', error);

                            this.saveError =
                                error.message
                                ?? 'Não foi possível salvar.';
                        } finally {
                            this.saving = false;
                        }
                    },
                })
            );
        });
    </script>
@endonce


<section
    x-data="characterAbilitiesPanel(
        @js($initialAbilities),
        {{ $proficiencyBonus }},
        {{ $initialExhaustionLevel }},
        @js(
            route(
                'characters.stats.ability.update',
                [
                    'character' => $character,
                    'ability' => '__ABILITY__',
                ]
            )
        ),
        @js(url('/api/roll'))
    )"
    @keydown.escape.window="
        if (confirmScoreChange) {
            confirmScoreChange = false;
        } else {
            closeModal();
        }
    "

    @character-exhaustion-updated.window="
        syncExhaustion(
            $event.detail
        )
    "
    class="
        character-stats-sheet
        character-stats-v2
        character-stats-v5
        character-stats-v6
        character-stats-v7
        relative
        xl:-mt-[72px]
    "
>
    {{-- ============================================================
         TÍTULO
    ============================================================= --}}
    <header class="character-stats-v2-header">
        <div class="character-stats-v2-header-copy">

            <h2 class="character-stats-v2-title">
                Atributos
            </h2>
        </div>
    </header>


    {{-- ============================================================
         COLUNAS
    ============================================================= --}}

    <div
        class="
            character-stats-v2-grid
            relative
            z-10
            grid
            grid-cols-2
            items-start
        "
    >
        <template
            x-for="
                column in [
                    ['strength', 'dexterity', 'constitution'],
                    ['intelligence', 'wisdom', 'charisma']
                ]
            "
            :key="column.join('-')"
        >
            <div class="character-stats-v2-column min-w-0 space-y-2">
                <template x-for="abilityKey in column" :key="abilityKey">
                    <article class="character-stat-panel group w-full">
                        <span class="character-stat-panel-corner tl"></span>
                        <span class="character-stat-panel-corner tr"></span>
                        <span class="character-stat-panel-corner bl"></span>
                        <span class="character-stat-panel-corner br"></span>


                        {{-- NOME: ÚNICO PONTO QUE ABRE O MODAL --}}

                        <div
                            class="
                                character-stat-panel-head
                                relative
                                z-10
                                text-center
                            "
                        >
                            <div class="character-stat-rule">
                                <button
                                    type="button"
                                    @click="openAbility(abilityKey, 'ability')"
                                    class="
                                        character-stat-name-button
                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.13em]
                                        text-[#53150f]
                                    "
                                    :title="
                                        'Editar ' +
                                        abilities[abilityKey].label
                                    "
                                >
                                    <span
                                        x-text="abilities[abilityKey].label"
                                    ></span>
                                </button>
                            </div>


                            {{-- VALOR: ROLA TESTE DE ATRIBUTO --}}

                            <button
                                type="button"
                                @click="
                                    rollCheck(
                                        abilities[abilityKey].label,
                                        modifier(abilityKey)
                                    )
                                "
                                class="
                                    character-stat-roll-main
                                    flex
                                    w-full
                                    items-center
                                    justify-center
                                    rounded-lg
                                    transition
                                "
                                :title="
                                    'Rolar teste de ' +
                                    abilities[abilityKey].label
                                "
                            >
                                <span
                                    class="
                                        character-stat-value-orb
                                        flex
                                        h-[50px]
                                        w-[50px]
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        border
                                        border-[#cdbb9f]/90
                                        bg-[#fffdf9]
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-[23px]
                                            font-black
                                            leading-none
                                            text-[#53150f]
                                        "
                                        x-text="signed(modifier(abilityKey))"
                                    ></span>
                                </span>

                                <span
                                    class="
                                        flex
                                        min-w-[38px]
                                        flex-col
                                        items-center
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-[21px]
                                            font-black
                                            leading-none
                                            text-[#6b1d14]
                                        "
                                        x-text="currentScore(abilityKey)"
                                    ></span>

                                    <span
                                        class="
                                            mt-1
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-[0.11em]
                                            text-[#8c6239]/70
                                        "
                                    >
                                        Valor
                                    </span>
                                </span>
                            </button>
                        </div>


                        {{-- SALVAGUARDA: ROLA DADO --}}

                        <button
                            type="button"
                            @click="
                                rollCheck(
                                    abilities[abilityKey].label +
                                    ' — Salvaguarda',
                                    savingThrowTotal(abilityKey)
                                )
                            "
                            class="
                                character-stat-row
                                character-stat-save-row
                                relative
                                z-10
                                flex
                                w-full
                                items-center
                                gap-2
                                border-b
                                border-[#d8c7ab]/45
                                px-3
                                py-1.5
                                text-left
                                transition
                            " 
                            :class="{
                                'is-proficient':
                                    abilities[abilityKey]
                                        .saving_throw
                                        .proficient
                            }"
                            title="Rolar salvaguarda"
                        >
                            <span
                                class="character-stat-training-dot"
                                :class="{
                                    'proficient':
                                        abilities[abilityKey]
                                            .saving_throw
                                            .proficient
                                }"
                            ></span>

                            <span
                                class="
                                    min-w-0
                                    flex-1
                                    truncate
                                    text-[10.5px]
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Salvamento
                            </span>

                            <span
                                class="
                                    shrink-0
                                    font-serif
                                    text-[12px]
                                    font-black
                                    text-[#6b1d14]
                                "
                                x-text="
                                    signed(
                                        savingThrowTotal(abilityKey)
                                    )
                                "
                            ></span>
                        </button>


                        {{-- PERÍCIAS: ROLAM DADO --}}

                        <div class="relative z-10">
                            <template
                                x-for="
                                    skill in Object.values(
                                        abilities[abilityKey].skills
                                    )
                                "
                                :key="skill.key"
                            >
                                <button
                                    type="button"
                                    @click="
                                        rollCheck(
                                            skill.label,
                                            skillTotal(
                                                abilityKey,
                                                skill.key
                                            )
                                        )
                                    "
                                    class="
                                        character-stat-row
                                        flex
                                        w-full
                                        items-center
                                        gap-2
                                        border-b
                                        border-[#d8c7ab]/30
                                        px-3
                                        py-1.5
                                        text-left
                                        transition
                                        last:border-b-0
                                    "
                                    :class="{
                                        'is-trained':
                                            skill.proficient
                                            || skill.expertise,
                                        'is-expertise':
                                            skill.expertise
                                    }"
                                    :title="'Rolar ' + skill.label"
                                >
                                    <span
                                        class="character-stat-training-dot"
                                        :class="trainingClass(skill)"
                                    ></span>

                                    <span
                                        class="
                                            min-w-0
                                            flex-1
                                            truncate
                                            text-[9px]
                                            font-semibold
                                            text-[#5f3a27]
                                        "
                                        x-text="skill.label"
                                    ></span>

                                    <span
                                        class="
                                            shrink-0
                                            font-serif
                                            text-[11px]
                                            font-black
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            signed(
                                                skillTotal(
                                                    abilityKey,
                                                    skill.key
                                                )
                                            )
                                        "
                                    ></span>
                                </button>
                            </template>
                        </div>
                    </article>
                </template>
            </div>
        </template>
    </div>


    {{-- ============================================================
         TOAST DE ROLAGEM
    ============================================================= --}}

    <div
        x-show="rollToastOpen && lastRoll"
        x-cloak
        x-transition
        class="
            character-roll-toast
            fixed
            bottom-5
            left-1/2
            z-[190]
            w-[min(92vw,340px)]
            -translate-x-1/2
            overflow-hidden
            rounded-xl
            border
            border-[#cdbb9f]
            bg-[#faf8f2]
        "
    >
        <div class="flex items-center gap-3 px-3.5 py-3">
            <div
                class="
                    flex
                    h-11
                    w-11
                    shrink-0
                    items-center
                    justify-center
                    rounded-lg
                    bg-[#53150f]
                    font-serif
                    text-xl
                    font-black
                    text-[#f4f1e8]
                "
                x-text="lastRoll?.total ?? '—'"
            ></div>

            <div class="min-w-0 flex-1">
                <p
                    class="
                        truncate
                        text-[10px]
                        font-black
                        text-[#53150f]
                    "
                    x-text="lastRoll?.label ?? ''"
                ></p>

                <p
                    class="
                        mt-0.5
                        text-[8px]
                        font-bold
                        text-[#8c6239]
                    "
                >
                    <span x-text="lastRoll?.die ?? '—'"></span>
                    <span> no d20</span>

                    <span x-show="lastRoll?.modifier !== 0">
                        ·
                        <span
                            x-text="
                                signed(
                                    lastRoll?.modifier ?? 0
                                )
                            "
                        ></span>
                        modificador
                    </span>

                    <span
                        x-show="
                            (lastRoll?.exhaustionPenalty ?? 0) > 0
                        "
                    >
                        ·
                        <span
                            class="
                                font-black
                                text-[#8b1e16]
                            "

                            x-text="
                                '-' + (
                                    lastRoll?.exhaustionPenalty
                                    ?? 0
                                )
                            "
                        ></span>
                        exaustão
                    </span>
                </p>
            </div>

            <button
                type="button"
                @click="rollToastOpen = false"
                class="
                    flex
                    h-7
                    w-7
                    shrink-0
                    items-center
                    justify-center
                    rounded-md
                    text-[#8c6239]
                    hover:bg-[#efe9dc]
                "
            >
                ×
            </button>
        </div>
    </div>


    {{-- ============================================================
         MODAL — DUAS ABAS
    ============================================================= --}}

    <template x-teleport="body">
        <div
            x-show="modalOpen"
            x-cloak
            class="
                fixed
                inset-0
                z-[170]
                flex
                items-center
                justify-center
                p-4
            "
        >
            <div
                class="
                    character-ability-modal-v2-backdrop
                    absolute
                    inset-0
                "
                @click="closeModal()"
            ></div>


            <div
                x-show="modalOpen"
                @click.stop
                class="
                    character-ability-modal-v2
                    relative
                    z-10
                    flex
                    w-full
                    flex-col
                    overflow-hidden
                    border
                "
            >
                {{-- HEADER --}}

                <div
                    class="
                        character-ability-modal-v2-header
                        shrink-0
                        border-b
                    "
                >
                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-4
                            px-4
                            pb-3
                            pt-4
                        "
                    >
                        <div class="flex min-w-0 items-center gap-3">
                            <div
                                class="
                                    flex
                                    h-11
                                    w-11
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]/70
                                    bg-[#faf8f2]
                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#53150f]
                                "
                                x-text="draft?.short ?? ''"
                            ></div>

                            <div class="min-w-0">
                                <p
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.18em]
                                        text-[#8c6239]
                                    "
                                >
                                    Configuração
                                </p>

                                <h3
                                    class="
                                        mt-0.5
                                        truncate
                                        font-serif
                                        text-xl
                                        font-black
                                        text-[#53150f]
                                    "
                                    x-text="draft?.label ?? ''"
                                ></h3>
                            </div>
                        </div>


                    </div>


                    {{-- ABAS --}}

                    <div
                        class="
                            grid
                            grid-cols-2
                            border-t
                            border-[#d8c7ab]/45
                        "
                    >
                        <button
                            type="button"
                            @click="setTab('ability')"
                            class="
                                character-modal-tab
                                px-3
                                py-2.5
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-[#8c6239]
                            "
                            :class="{ 'active': activeTab === 'ability' }"
                        >
                            Atributo
                        </button>

                        <button
                            type="button"
                            @click="setTab('training')"
                            class="
                                character-modal-tab
                                px-3
                                py-2.5
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-[#8c6239]
                            "
                            :class="{ 'active': activeTab === 'training' }"
                        >
                            Salvamento & Perícias
                        </button>
                    </div>
                </div>


                {{-- CONTEÚDO --}}

                <div class="character-ability-modal-v2-body min-h-0 flex-1 overflow-y-auto">

                    {{-- ABA 1 — ATRIBUTO --}}

                    <div x-show="activeTab === 'ability'">
                        <div class="character-modal-card overflow-hidden">

                            {{-- VALOR PERMANENTE --}}

                            <div
                                class="
                                    border-b
                                    border-[#d8c7ab]/55
                                    p-4
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
                                    <div>
                                        <p
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.16em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Valor Permanente
                                        </p>

                                        <p
                                            class="
                                                mt-1
                                                max-w-[330px]
                                                text-[10px]
                                                leading-relaxed
                                                text-[#8c6239]/75
                                            "
                                        >
                                            Este é o valor real do personagem.
                                            Alterá-lo muda permanentemente o atributo.
                                        </p>
                                    </div>

                                    <div
                                        class="
                                            character-modal-score
                                            flex
                                            h-[62px]
                                            min-w-[92px]
                                            items-center
                                            justify-center
                                            gap-1.5
                                            rounded-xl
                                            border
                                            border-[#cdbb9f]
                                            bg-[#fffdf9]
                                            px-3
                                        "
                                    >
                                        <span
                                            class="
                                                font-serif
                                                text-[27px]
                                                font-black
                                                leading-none
                                                text-[#53150f]
                                            "
                                            x-text="draft?.score ?? 10"
                                        ></span>

                                        <span
                                            class="
                                                font-serif
                                                text-sm
                                                font-black
                                                text-[#8c6239]
                                            "
                                        >
                                            (<span
                                                x-text="
                                                    signed(
                                                        draftBaseModifier
                                                    )
                                                "
                                            ></span>)
                                        </span>
                                    </div>
                                </div>

                                <label class="mt-4 block">
                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]
                                            text-[#53150f]
                                        "
                                        x-text="draft?.label ?? 'Atributo'"
                                    ></span>

                                    <input
                                        type="number"
                                        min="1"
                                        x-model.number="draft.score"
                                        class="
                                            mt-1.5
                                            w-full
                                            rounded-xl
                                            border
                                            border-[#cdbb9f]
                                            bg-white
                                            px-3
                                            py-2.5
                                            text-center
                                            font-serif
                                            text-lg
                                            font-black
                                            text-[#53150f]
                                            outline-none
                                            transition
                                            focus:border-[#6b1d14]
                                            focus:ring-2
                                            focus:ring-[#6b1d14]/10
                                        "
                                    >
                                </label>

                                <div
                                    x-show="permanentScoreChanged"
                                    x-cloak
                                    class="
                                        mt-3
                                        rounded-lg
                                        border
                                        border-[#d4b36b]/55
                                        bg-[#f8efd9]/70
                                        px-3
                                        py-2
                                        text-[9px]
                                        font-bold
                                        leading-relaxed
                                        text-[#8a6418]
                                    "
                                >
                                    Você está alterando o valor permanente de
                                    <strong x-text="draft?.label ?? ''"></strong>
                                    de
                                    <strong x-text="originalScore"></strong>
                                    para
                                    <strong x-text="draft?.score ?? ''"></strong>.
                                    A confirmação será solicitada ao salvar.
                                </div>
                            </div>


                            {{-- BÔNUS EXTRA --}}

                            <div class="p-4">
                                <div
                                    class="
                                        flex
                                        items-start
                                        justify-between
                                        gap-4
                                    "
                                >
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p
                                                class="
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.16em]
                                                    text-[#6b1d14]
                                                "
                                            >
                                                Bônus Extra
                                            </p>

                                            <span
                                                x-show="
                                                    draft?.override !== null &&
                                                    draft?.override !== ''
                                                "
                                                x-cloak
                                                class="
                                                    rounded-md
                                                    bg-[#6b1d14]
                                                    px-1.5
                                                    py-0.5
                                                    text-[7px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    text-[#f4f1e8]
                                                "
                                            >
                                                Ativo
                                            </span>
                                        </div>

                                        <p
                                            class="
                                                mt-1
                                                max-w-[330px]
                                                text-[10px]
                                                leading-relaxed
                                                text-[#8c6239]/75
                                            "
                                        >
                                            Digite o score inteiro.
                                            Ex.: Uma poção que deixa sua em Força 16:
                                            digite 16. Ao apagar, volta para o valor original.
                                        </p>
                                    </div>

                                    <div
                                        class="
                                            character-modal-score
                                            flex
                                            h-[62px]
                                            min-w-[92px]
                                            items-center
                                            justify-center
                                            gap-1.5
                                            rounded-xl
                                            border
                                            border-[#d4b36b]/70
                                            bg-[#fffaf0]
                                            px-3
                                        "
                                    >
                                        <span
                                            class="
                                                font-serif
                                                text-[27px]
                                                font-black
                                                leading-none
                                                text-[#6b1d14]
                                            "
                                            x-text="draftEffectiveScore"
                                        ></span>

                                        <span
                                            class="
                                                font-serif
                                                text-sm
                                                font-black
                                                text-[#9a6f16]
                                            "
                                        >
                                            (<span
                                                x-text="
                                                    signed(
                                                        draftEffectiveModifier
                                                    )
                                                "
                                            ></span>)
                                        </span>
                                    </div>
                                </div>

                                <label class="mt-4 block">
                                    <span
                                        class="
                                            flex
                                            items-center
                                            justify-between
                                            gap-2
                                        "
                                    >
                                        <span
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.12em]
                                                text-[#53150f]
                                            "
                                        >
                                            Score Temporário
                                        </span>

                                        <button
                                            x-show="
                                                draft?.override !== null &&
                                                draft?.override !== ''
                                            "
                                            x-cloak
                                            type="button"
                                            @click="clearExtraScore()"
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#6b1d14]
                                                hover:underline
                                            "
                                        >
                                            Remover bônus
                                        </button>
                                    </span>

                                    <input
                                        type="number"
                                        min="1"
                                        x-model="draft.override"
                                        placeholder="Ex.: 16"
                                        class="
                                            mt-1.5
                                            w-full
                                            rounded-xl
                                            border
                                            border-[#d4b36b]/70
                                            bg-[#fffaf0]
                                            px-3
                                            py-2.5
                                            text-center
                                            font-serif
                                            text-lg
                                            font-black
                                            text-[#6b1d14]
                                            outline-none
                                            transition
                                            placeholder:text-[#b7a98f]
                                            focus:border-[#9a6f16]
                                            focus:ring-2
                                            focus:ring-[#d4b36b]/15
                                        "
                                    >
                                </label>

                                <div
                                    class="
                                        mt-3
                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/60
                                        bg-[#efe9dc]/45
                                        px-3
                                        py-2.5
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
                                        <span
                                            class="
                                                text-[9px]
                                                font-bold
                                                text-[#8c6239]
                                            "
                                        >
                                            Resultado usado na ficha
                                        </span>

                                        <strong
                                            class="
                                                font-serif
                                                text-base
                                                text-[#53150f]
                                            "
                                        >
                                            <span
                                                x-text="draftEffectiveScore"
                                            ></span>
                                            (<span
                                                x-text="
                                                    signed(
                                                        draftEffectiveModifier
                                                    )
                                                "
                                            ></span>)
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- ABA 2 — SALVAGUARDA & PERÍCIAS --}}

                    <div
                        x-show="activeTab === 'training'"
                        x-cloak
                        class="space-y-3"
                    >
                        <section class="character-modal-card p-4">
                            <div
                                class="
                                    flex
                                    items-center
                                    justify-between
                                    gap-3
                                "
                            >
                                <div>
                                    <p
                                        class="
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.15em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Salvaguarda
                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            text-[10px]
                                            text-[#8c6239]/75
                                        "
                                    >
                                        Treinamento e ajustes especiais.
                                    </p>
                                </div>

                                <span
                                    class="
                                        font-serif
                                        text-2xl
                                        font-black
                                        text-[#53150f]
                                    "
                                    x-text="
                                        draft
                                            ? signed(
                                                savingThrowTotalFrom(draft)
                                            )
                                            : '+0'
                                    "
                                ></span>
                            </div>

                            <label
                                class="
                                    mt-3
                                    flex
                                    items-center
                                    justify-between
                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/65
                                    bg-[#efe9dc]/45
                                    px-3
                                    py-2.5
                                "
                            >
                                <span>
                                    <span
                                        class="
                                            block
                                            text-[10px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Proficiência
                                    </span>
                                    <span
                                        class="
                                            mt-0.5
                                            block
                                            text-[8px]
                                            text-[#8c6239]/70
                                        "
                                    >
                                        Adiciona o bônus de proficiência.
                                    </span>
                                </span>

                                <input
                                    type="checkbox"
                                    x-model="draft.saving_throw.proficient"
                                    class="
                                        h-5
                                        w-5
                                        rounded
                                        border-[#cdbb9f]
                                        text-[#6b1d14]
                                        focus:ring-[#6b1d14]/20
                                    "
                                >
                            </label>

                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <label class="block">
                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wide
                                            text-[#8c6239]
                                        "
                                    >
                                        Bônus Extra
                                    </span>

                                    <input
                                        type="number"
                                        x-model.number="
                                            draft
                                                .saving_throw
                                                .temporary_bonus
                                        "
                                        class="
                                            mt-1
                                            w-full
                                            rounded-lg
                                            border
                                            border-[#cdbb9f]
                                            bg-white
                                            px-2.5
                                            py-2
                                            text-center
                                            text-sm
                                            font-black
                                            text-[#53150f]
                                            outline-none
                                            focus:border-[#6b1d14]
                                        "
                                    >
                                </label>

                                <label class="block">
                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wide
                                            text-[#8c6239]
                                        "
                                    >
                                        Total Manual
                                    </span>

                                    <input
                                        type="number"
                                        x-model="
                                            draft
                                                .saving_throw
                                                .bonus_override
                                        "
                                        placeholder="Automático"
                                        class="
                                            mt-1
                                            w-full
                                            rounded-lg
                                            border
                                            border-[#cdbb9f]
                                            bg-white
                                            px-2.5
                                            py-2
                                            text-center
                                            text-sm
                                            font-black
                                            text-[#53150f]
                                            outline-none
                                            placeholder:text-[#b7a98f]
                                            focus:border-[#6b1d14]
                                        "
                                    >
                                </label>
                            </div>
                        </section>


                        <section>
                            <div class="mb-2 px-1">
                                <p
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#8c6239]
                                    "
                                >
                                    Perícias
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[9px]
                                        text-[#8c6239]/70
                                    "
                                >
                                    Treinamento, expertise e ajustes especiais.
                                </p>
                            </div>

                            <div class="space-y-2">
                                <template
                                    x-for="
                                        skill in Object.values(
                                            draft?.skills ?? {}
                                        )
                                    "
                                    :key="skill.key"
                                >
                                    <div class="character-modal-card p-3">
                                        <div
                                            class="
                                                flex
                                                items-center
                                                justify-between
                                                gap-3
                                            "
                                        >
                                            <div class="min-w-0">
                                                <p
                                                    class="
                                                        truncate
                                                        text-[11px]
                                                        font-black
                                                        text-[#53150f]
                                                    "
                                                    x-text="skill.label"
                                                ></p>

                                                <p
                                                    class="
                                                        mt-0.5
                                                        text-[8px]
                                                        text-[#8c6239]/70
                                                    "
                                                >
                                                    Resultado atual
                                                </p>
                                            </div>

                                            <span
                                                class="
                                                    shrink-0
                                                    font-serif
                                                    text-xl
                                                    font-black
                                                    text-[#53150f]
                                                "
                                                x-text="
                                                    draft
                                                        ? signed(
                                                            skillTotalFrom(
                                                                draft,
                                                                skill
                                                            )
                                                        )
                                                        : '+0'
                                                "
                                            ></span>
                                        </div>

                                        <div
                                            class="
                                                mt-2.5
                                                grid
                                                grid-cols-3
                                                overflow-hidden
                                                rounded-lg
                                                border
                                                border-[#d8c7ab]/70
                                                bg-[#f4f1e8]/50
                                            "
                                        >
                                            <button
                                                type="button"
                                                @click="
                                                    setSkillTraining(
                                                        skill,
                                                        'none'
                                                    )
                                                "
                                                class="
                                                    px-1
                                                    py-2
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    transition
                                                "
                                                :class="
                                                    skillTrainingMode(skill)
                                                        === 'none'
                                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                                "
                                            >
                                                Normal
                                            </button>

                                            <button
                                                type="button"
                                                @click="
                                                    setSkillTraining(
                                                        skill,
                                                        'proficient'
                                                    )
                                                "
                                                class="
                                                    border-x
                                                    border-[#d8c7ab]/60
                                                    px-1
                                                    py-2
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    transition
                                                "
                                                :class="
                                                    skillTrainingMode(skill)
                                                        === 'proficient'
                                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                                "
                                            >
                                                Prof.
                                            </button>

                                            <button
                                                type="button"
                                                @click="
                                                    setSkillTraining(
                                                        skill,
                                                        'expertise'
                                                    )
                                                "
                                                class="
                                                    px-1
                                                    py-2
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    transition
                                                "
                                                :class="
                                                    skillTrainingMode(skill)
                                                        === 'expertise'
                                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                                "
                                            >
                                                Expertise
                                            </button>
                                        </div>

                                        <div class="mt-2 grid grid-cols-2 gap-2">
                                            <label class="block">
                                                <span
                                                    class="
                                                        text-[8px]
                                                        font-black
                                                        uppercase
                                                        tracking-wide
                                                        text-[#8c6239]
                                                    "
                                                >
                                                    Bônus Extra
                                                </span>

                                                <input
                                                    type="number"
                                                    x-model.number="
                                                        skill
                                                            .temporary_bonus
                                                    "
                                                    class="
                                                        mt-1
                                                        w-full
                                                        rounded-lg
                                                        border
                                                        border-[#cdbb9f]
                                                        bg-white
                                                        px-2
                                                        py-1.5
                                                        text-center
                                                        text-sm
                                                        font-black
                                                        text-[#53150f]
                                                        outline-none
                                                        focus:border-[#6b1d14]
                                                    "
                                                >
                                            </label>

                                            <label class="block">
                                                <span
                                                    class="
                                                        text-[8px]
                                                        font-black
                                                        uppercase
                                                        tracking-wide
                                                        text-[#8c6239]
                                                    "
                                                >
                                                    Total Manual
                                                </span>

                                                <input
                                                    type="number"
                                                    x-model="skill.bonus_override"
                                                    placeholder="Automático"
                                                    class="
                                                        mt-1
                                                        w-full
                                                        rounded-lg
                                                        border
                                                        border-[#cdbb9f]
                                                        bg-white
                                                        px-2
                                                        py-1.5
                                                        text-center
                                                        text-sm
                                                        font-black
                                                        text-[#53150f]
                                                        outline-none
                                                        placeholder:text-[#b7a98f]
                                                        focus:border-[#6b1d14]
                                                    "
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>


                    <div
                        x-show="saveError"
                        x-cloak
                        class="
                            mt-3
                            rounded-lg
                            border
                            border-red-200
                            bg-red-50
                            px-3
                            py-2.5
                            text-[10px]
                            font-bold
                            text-red-700
                        "
                        x-text="saveError"
                    ></div>
                </div>


                {{-- FOOTER --}}

                <div
                    class="
                        character-ability-modal-v2-footer
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3
                        border-t
                        px-4
                        py-3
                    "
                >
                    <p
                        class="
                            hidden
                            text-[8px]
                            font-bold
                            text-[#8c6239]/70
                            sm:block
                        "
                    >
                        As alterações só entram na ficha após salvar.
                    </p>

                    <div class="ml-auto flex items-center gap-2">


                        <button
                            type="button"
                            @click="requestSave()"
                            :disabled="saving"
                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-4
                                py-2
                                text-[9px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#f4f1e8]
                                transition
                                hover:bg-[#53150f]
                                disabled:opacity-50
                            "
                        >
                            <span x-show="!saving">Salvar</span>
                            <span x-show="saving" x-cloak>Salvando...</span>
                        </button>
                    </div>
                </div>


                {{-- CONFIRMAÇÃO DE ALTERAÇÃO PERMANENTE --}}

                <div
                    x-show="confirmScoreChange"
                    x-cloak

                    class="
                        absolute
                        inset-0
                        z-30

                        flex
                        items-center
                        justify-center

                        bg-black/90

                        p-4

                        backdrop-blur-[2px]
                    "

                    style="
                        background-color:
                            rgba(8, 6, 5, 0.90);
                    "
                >
                    <div
                        @click.stop
                        class="
                            w-full
                            max-w-sm
                            overflow-hidden
                            rounded-2xl
                            border
                            border-[#cdbb9f]
                            bg-[#faf8f2]
                            shadow-2xl
                        "
                    >
                        <div
                            class="
                                border-b
                                border-[#d8c7ab]/60
                                bg-[#efe9dc]/65
                                px-4
                                py-3
                            "
                        >
                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]
                                    text-[#8c6239]
                                "
                            >
                                Alteração permanente
                            </p>

                            <h4
                                class="
                                    mt-0.5
                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Confirmar novo atributo?
                            </h4>
                        </div>

                        <div class="p-4">
                            <p
                                class="
                                    text-[11px]
                                    leading-relaxed
                                    text-[#5f3a27]
                                "
                            >
                                Tem certeza que deseja mudar
                                <strong x-text="draft?.label ?? 'este atributo'"></strong>
                                de
                                <strong>
                                    <span x-text="originalScore"></span>
                                    (<span
                                        x-text="
                                            signed(
                                                modifierFromScore(
                                                    originalScore
                                                )
                                            )
                                        "
                                    ></span>)
                                </strong>
                                para
                                <strong>
                                    <span x-text="draft?.score ?? ''"></span>
                                    (<span
                                        x-text="
                                            signed(
                                                modifierFromScore(
                                                    draft?.score ?? 10
                                                )
                                            )
                                        "
                                    ></span>)
                                </strong>?
                            </p>

                            <div
                                class="
                                    mt-3
                                    rounded-lg
                                    border
                                    border-[#d4b36b]/55
                                    bg-[#f8efd9]/70
                                    px-3
                                    py-2.5
                                    text-[9px]
                                    leading-relaxed
                                    text-[#8a6418]
                                "
                            >
                                Essa alteração modifica o valor permanente.
                                O <strong>Bônus Extra</strong> é temporário e
                                não exige confirmação.
                            </div>
                        </div>

                        <div
                            class="
                                flex
                                justify-end
                                gap-2
                                border-t
                                border-[#d8c7ab]/60
                                bg-[#efe9dc]/45
                                px-4
                                py-3
                            "
                        >
                            <button
                                type="button"
                                @click="confirmScoreChange = false"
                                class="
                                    rounded-lg
                                    px-3
                                    py-2
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                    hover:bg-[#e8dfd1]
                                "
                            >
                                Voltar
                            </button>

                            <button
                                type="button"
                                @click="confirmPermanentScoreChange()"
                                class="
                                    rounded-lg
                                    bg-[#6b1d14]
                                    px-4
                                    py-2
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#f4f1e8]
                                    hover:bg-[#53150f]
                                "
                            >
                                Sim, alterar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</section>