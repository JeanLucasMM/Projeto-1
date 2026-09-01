@php
    /*
    |--------------------------------------------------------------------------
    | TREINAMENTO & PROFICIÊNCIAS — V2 COMPACTO
    |--------------------------------------------------------------------------
    |
    | Usa CharacterFeature com os tipos:
    |
    | - armor_training
    | - weapon_training
    | - tool_proficiency
    | - language
    | - vehicle_proficiency
    |
    | A interface é propositalmente simples:
    |
    | - Armaduras: marcação direta.
    | - Armas: grupos + armas específicas.
    | - Ferramentas/Kits: seleção + rolagem.
    | - Idiomas: marcação múltipla.
    | - Veículos: seção opcional + rolagem.
    |
    */

    $trainingTypes = [
        'armor_training',
        'weapon_training',
        'tool_proficiency',
        'language',
        'vehicle_proficiency',
    ];

    $trainingPayload = $character->features
        ->filter(function ($feature) use ($trainingTypes) {
            return in_array(
                $feature->type,
                $trainingTypes,
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

    /*
    |--------------------------------------------------------------------------
    | BÔNUS DE PROFICIÊNCIA
    |--------------------------------------------------------------------------
    */

    $level = max(
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

    $proficiencyBonus = (int) (
        $character->proficiency_bonus
        ?? $calculatedProficiency
    );

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

    /*
    |--------------------------------------------------------------------------
    | MODIFICADORES DE ATRIBUTO
    |--------------------------------------------------------------------------
    |
    | Mesma leitura usada no Blade de Atributos.
    |
    */

    $abilityDefinitions = [
        'strength' => [
            'label' => 'Força',
            'short' => 'FOR',
        ],

        'dexterity' => [
            'label' => 'Destreza',
            'short' => 'DES',
        ],

        'constitution' => [
            'label' => 'Constituição',
            'short' => 'CON',
        ],

        'intelligence' => [
            'label' => 'Inteligência',
            'short' => 'INT',
        ],

        'wisdom' => [
            'label' => 'Sabedoria',
            'short' => 'SAB',
        ],

        'charisma' => [
            'label' => 'Carisma',
            'short' => 'CAR',
        ],
    ];

    $abilities = $character->abilities;

    $temporaryBonuses = is_array(
        $abilities?->temporary_bonuses
    )
        ? $abilities->temporary_bonuses
        : [];

    $abilityOverrides = is_array(
        $abilities?->overrides
    )
        ? $abilities->overrides
        : [];

    $abilityModifiers = [];

    foreach ($abilityDefinitions as $abilityKey => $definition) {
        $baseScore = (int) (
            $abilities?->{$abilityKey}
            ?? 10
        );

        if (array_key_exists($abilityKey, $abilityOverrides)) {
            $effectiveScore = (int) $abilityOverrides[$abilityKey];
        } else {
            $effectiveScore =
                $baseScore
                + (int) (
                    $temporaryBonuses[$abilityKey]
                    ?? 0
                );
        }

        $abilityModifiers[$abilityKey] =
            (int) floor(
                ($effectiveScore - 10) / 2
            );
    }
@endphp

@once
    @push('styles')
        <style>
            /*
            |--------------------------------------------------------------------------
            | TRAINING V2 — MESMA FAMÍLIA DE ATTACK / FEATURES
            |--------------------------------------------------------------------------
            */

            .training-v2 {
                width: 100%;
                max-width: 820px;
                margin-inline: auto;

                color: #432c21;
            }

            .training-v2-shell {
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
            | CABEÇALHO
            |--------------------------------------------------------------------------
            */

            .training-v2-header {
                display: flex;

                min-height: 38px;

                align-items: center;
                justify-content: space-between;

                gap: 10px;

                border-bottom:
                    1px solid
                    rgba(160,119,77,.34);

                background: #eadbc8;

                padding:
                    0 10px;
            }

            .training-v2-title-wrap {
                display: flex;

                min-width: 0;

                align-items: center;

                gap: 8px;
            }

            .training-v2-title {
                overflow: hidden;

                font-family: Georgia, serif;
                font-size: 14px;
                font-weight: 900;
                line-height: 1;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .training-v2-count {
                display: inline-flex;

                min-width: 22px;
                height: 22px;

                align-items: center;
                justify-content: center;

                border-radius: 999px;

                background:
                    rgba(255,253,248,.60);

                padding:
                    0 6px;

                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #7e5735;
            }

            .training-v2-vehicle-toggle {
                display: inline-flex;

                min-height: 27px;

                flex: 0 0 auto;

                align-items: center;
                justify-content: center;

                gap: 5px;

                border-radius: 6px;

                padding:
                    0 8px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                color: #8c6239;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .training-v2-vehicle-toggle:hover,
            .training-v2-vehicle-toggle.active {
                background:
                    rgba(255,253,248,.60);

                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | SEÇÕES
            |--------------------------------------------------------------------------
            */

            .training-v2-section {
                padding:
                    10px 11px 11px;
            }

            .training-v2-section + .training-v2-section {
                border-top:
                    1px solid
                    rgba(188,154,111,.26);
            }

            .training-v2-section:nth-child(odd) {
                background:
                    rgba(255,253,248,.46);
            }

            .training-v2-section:nth-child(even) {
                background:
                    rgba(247,240,230,.46);
            }

            .training-v2-section-head {
                display: flex;

                align-items: center;
                justify-content: space-between;

                gap: 10px;

                margin-bottom: 7px;
            }

            .training-v2-section-title {
                display: flex;

                align-items: center;

                gap: 6px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .105em;

                text-transform: uppercase;

                color: #6f472f;
            }

            .training-v2-section-count {
                display: inline-flex;

                min-width: 19px;
                height: 19px;

                align-items: center;
                justify-content: center;

                border-radius: 999px;

                background: #eadbc8;

                padding:
                    0 5px;

                font-size: 8px;
                font-weight: 900;

                color: #6b1d14;
            }

            /*
            |--------------------------------------------------------------------------
            | MARCAÇÕES
            |--------------------------------------------------------------------------
            */

            .training-v2-options {
                display: flex;

                flex-wrap: wrap;

                gap: 6px;
            }

            .training-v2-option {
                position: relative;

                display: inline-flex;

                min-height: 29px;

                align-items: center;
                justify-content: center;

                gap: 6px;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 7px;

                background: #fffdf8;

                padding:
                    0 9px;

                font-size: 9.5px;
                font-weight: 800;

                color: #745743;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            .training-v2-option:hover {
                background: #f4e8d8;
                color: #53150f;
            }

            .training-v2-option.active {
                border-color:
                    rgba(107,29,20,.34);

                background: #eadbc8;

                color: #53150f;
            }

            .training-v2-check {
                display: inline-flex;

                width: 12px;
                height: 12px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(140,98,57,.48);

                border-radius: 3px;

                background: #fffdf8;

                font-size: 8px;
                font-weight: 900;
                line-height: 1;

                color: transparent;
            }

            .training-v2-option.active
            .training-v2-check {
                border-color: #6b1d14;
                background: #6b1d14;
                color: #fffdf8;
            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOM
            |--------------------------------------------------------------------------
            */

            .training-v2-custom {
                display: flex;

                gap: 6px;

                margin-top: 8px;
            }

            .training-v2-input {
                min-width: 0;
                min-height: 32px;

                flex: 1;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 7px;

                background: #fffdf8;

                padding:
                    0 9px;

                font-size: 11px;

                color: #432c21;

                outline: none;
            }

            .training-v2-input:focus {
                border-color: #8c6239;

                box-shadow:
                    0 0 0 2px
                    rgba(140,98,57,.07);
            }

            .training-v2-add {
                display: inline-flex;

                min-height: 32px;

                align-items: center;
                justify-content: center;

                border-radius: 7px;

                background: #6b1d14;

                padding:
                    0 10px;

                font-size: 9px;
                font-weight: 900;

                color: #faf8f2;
            }

            .training-v2-add:hover {
                background: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | TAGS PERSONALIZADAS
            |--------------------------------------------------------------------------
            */

            .training-v2-tags {
                display: flex;

                flex-wrap: wrap;

                gap: 5px;

                margin-top: 7px;
            }

            .training-v2-tag {
                display: inline-flex;

                min-height: 26px;

                align-items: center;

                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.30);

                border-radius: 999px;

                background: #f7f0e6;

                color: #6f5544;
            }

            .training-v2-tag span {
                padding:
                    0 8px;

                font-size: 9px;
                font-weight: 800;
            }

            .training-v2-tag button {
                display: flex;

                width: 24px;
                height: 24px;

                align-items: center;
                justify-content: center;

                border-left:
                    1px solid
                    rgba(176,140,98,.24);

                color: #9a6e50;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .training-v2-tag button:hover {
                background: #eadbc8;
                color: #6b1d14;
            }

            /*
            |--------------------------------------------------------------------------
            | FERRAMENTAS / VEÍCULOS COM ROLAGEM
            |--------------------------------------------------------------------------
            */

            .training-v2-roll-list {
                margin-top: 8px;

                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.28);

                border-radius: 7px;

                background: #fffdf8;
            }

            .training-v2-roll-row {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    50px
                    82px
                    27px;

                align-items: center;

                min-height: 38px;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.22);

                background:
                    rgba(255,253,248,.84);
            }

            .training-v2-roll-row:nth-child(even) {
                background:
                    rgba(247,240,230,.72);
            }

            .training-v2-roll-row:last-child {
                border-bottom: 0;
            }

            .training-v2-roll-name {
                min-width: 0;

                overflow: hidden;

                padding:
                    0 9px;

                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .training-v2-ability {
                width: 100%;
                height: 28px;

                border:
                    1px solid
                    rgba(176,140,98,.30);

                border-radius: 6px;

                background: #fbf8f1;

                padding:
                    0 4px;

                font-size: 8.5px;
                font-weight: 900;

                color: #7d604d;

                outline: none;
            }

            .training-v2-roll {
                display: inline-flex;

                height: 28px;

                align-items: center;
                justify-content: center;

                gap: 4px;

                margin-left: 5px;

                border:
                    1px solid
                    rgba(107,29,20,.16);

                border-radius: 6px;

                background:
                    #f0e4d5;

                padding:
                    0 7px;

                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #6b1d14;

                transition:
                    background .12s ease;
            }

            .training-v2-roll:hover {
                background: #eadbc8;
            }

            .training-v2-remove {
                display: flex;

                width: 27px;
                height: 27px;

                align-items: center;
                justify-content: center;

                color: #9a6e50;
            }

            .training-v2-remove:hover {
                color: #991b1b;
            }

            /*
            |--------------------------------------------------------------------------
            | VEÍCULOS
            |--------------------------------------------------------------------------
            */

            .training-v2-vehicles {
                border-top:
                    1px solid
                    rgba(160,119,77,.28);

                background:
                    #f0e4d5;
            }

            /*
            |--------------------------------------------------------------------------
            | ROLL TOAST
            |--------------------------------------------------------------------------
            */

            .training-v2-toast {
                position: fixed;

                right: 20px;
                bottom: 20px;
                z-index: 260;

                width:
                    min(
                        300px,
                        calc(100vw - 32px)
                    );

                border:
                    1px solid
                    rgba(176,140,98,.62);

                border-radius: 10px;

                background: #fbf8f1;

                padding:
                    10px 12px;

                box-shadow:
                    0 16px 42px
                    rgba(43,29,23,.16);

                color: #432c21;
            }

            .training-v2-toast-title {
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #53150f;
            }

            .training-v2-toast-result {
                margin-top: 3px;

                font-size: 10px;
                line-height: 1.4;

                color: #7d604d;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 600px) {
                .training-v2-header {
                    align-items: flex-start;

                    padding:
                        8px 10px;
                }

                .training-v2-title {
                    white-space: normal;
                }

                .training-v2-roll-row {
                    grid-template-columns:
                        minmax(0, 1fr)
                        48px
                        72px
                        25px;
                }

                .training-v2-roll-name {
                    font-size: 10px;
                }
            }
        
            /*
            |--------------------------------------------------------------------------
            | TRAINING V3 — RESUMO DE FICHA + MODAL
            |--------------------------------------------------------------------------
            |
            | Na folha: leitura simples, semelhante à ficha de referência.
            | No modal: marcações e configuração completa.
            |
            */

            .training-v3 {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            /*
            |--------------------------------------------------------------------------
            | CAIXA COMPACTA NA FICHA
            |--------------------------------------------------------------------------
            */

            .training-v3-summary {
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 8px;

                background: #fbf8f1;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.70);
            }

            .training-v3-summary-header {
                display: flex;

                min-height: 31px;

                align-items: center;
                justify-content: space-between;

                gap: 8px;

                width: 100%;

                border-bottom:
                    1px solid
                    rgba(160,119,77,.34);

                background: #eadbc8;

                padding:
                    0 9px;

                text-align: left;

                transition:
                    background .12s ease;
            }

            .training-v3-summary-header:hover {
                background: #e4d0b9;
            }

            .training-v3-summary-title {
                min-width: 0;

                overflow: hidden;

                font-size: 8.5px;
                font-weight: 900;
                line-height: 1.12;
                letter-spacing: .075em;

                text-transform: uppercase;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .training-v3-summary-config {
                display: inline-flex;

                flex: 0 0 auto;

                align-items: center;

                gap: 4px;

                font-size: 8px;
                font-weight: 900;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | LINHAS
            |--------------------------------------------------------------------------
            */

            .training-v3-summary-section {
                padding:
                    6px 8px 7px;
            }

            .training-v3-summary-section +
            .training-v3-summary-section {
                border-top:
                    1px solid
                    rgba(188,154,111,.26);
            }

            .training-v3-summary-section:nth-child(odd) {
                background:
                    rgba(255,253,248,.82);
            }

            .training-v3-summary-section:nth-child(even) {
                background:
                    rgba(247,240,230,.72);
            }

            .training-v3-summary-label {
                display: block;

                margin-bottom: 4px;

                font-size: 6.5px;
                font-weight: 900;
                line-height: 1;
                letter-spacing: .08em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .training-v3-summary-value {
                font-family: Georgia, serif;
                font-size: 11px;
                line-height: 1.35;

                color: #5f4636;
            }

            .training-v3-summary-empty {
                color: #a58b76;
            }

            /*
            |--------------------------------------------------------------------------
            | ARMADURA — MARCAÇÕES
            |--------------------------------------------------------------------------
            */

            .training-v3-armor {
                display: grid;

                grid-template-columns:
                    repeat(4, minmax(0, 1fr));

                gap: 3px;
            }

            .training-v3-armor-item {
                display: flex;

                min-width: 0;

                align-items: center;
                justify-content: center;

                gap: 3px;

                padding:
                    2px 0;

                font-size: 7px;
                font-weight: 800;

                color: #9a806b;
            }

            .training-v3-armor-item.active {
                color: #53150f;
            }

            .training-v3-diamond {
                width: 7px;
                height: 7px;

                flex: 0 0 7px;

                border:
                    1px solid
                    rgba(140,98,57,.52);

                transform: rotate(45deg);

                background: #fffdf8;
            }

            .training-v3-armor-item.active
            .training-v3-diamond {
                border-color: #6b1d14;
                background: #6b1d14;
            }

            /*
            |--------------------------------------------------------------------------
            | FERRAMENTAS / VEÍCULOS NO RESUMO
            |--------------------------------------------------------------------------
            */

            .training-v3-roll-summary {
                display: flex;

                flex-direction: column;

                gap: 3px;
            }

            .training-v3-roll-summary-button {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    auto;

                align-items: center;

                gap: 7px;

                width: 100%;

                border-radius: 5px;

                padding:
                    2px 3px;

                text-align: left;

                transition:
                    background .12s ease;
            }

            .training-v3-roll-summary-button:hover {
                background: #f0e4d5;
            }

            .training-v3-roll-summary-name {
                min-width: 0;

                overflow: hidden;

                font-family: Georgia, serif;
                font-size: 10.5px;

                color: #5f4636;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .training-v3-roll-summary-bonus {
                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #6b1d14;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL
            |--------------------------------------------------------------------------
            */

            .training-v3-backdrop {
                position: absolute;
                inset: 0;

                background:
                    rgba(42,23,18,.48);

                backdrop-filter:
                    blur(2px);
            }

            .training-v3-modal {
                position: relative;
                z-index: 1;

                display: flex;

                width:
                    min(
                        760px,
                        calc(100vw - 28px)
                    );

                max-height:
                    min(
                        90vh,
                        900px
                    );

                flex-direction: column;

                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.68);

                border-radius: 14px;

                background: #fbf8f1;

                box-shadow:
                    0 22px 64px rgba(42,23,18,.22);
            }

            .training-v3-modal-header {
                display: flex;

                flex: 0 0 auto;

                align-items: center;
                justify-content: space-between;

                gap: 12px;

                border-bottom:
                    1px solid
                    rgba(160,119,77,.34);

                background: #eadbc8;

                padding:
                    12px 14px;
            }

            .training-v3-modal-kicker {
                display: block;

                margin-bottom: 2px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .training-v3-modal-title {
                font-family: Georgia, serif;
                font-size: 19px;
                font-weight: 900;

                color: #53150f;
            }

            .training-v3-modal-close {
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

            .training-v3-modal-close:hover {
                background:
                    rgba(255,253,248,.52);

                color: #53150f;
            }

            .training-v3-modal-body {
                min-height: 0;
                flex: 1;

                overflow-y: auto;

                padding: 12px;
            }

            .training-v3-editor-section {
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.30);

                border-radius: 8px;

                background: #fffdf8;
            }

            .training-v3-editor-section +
            .training-v3-editor-section {
                margin-top: 9px;
            }

            .training-v3-editor-title {
                display: flex;

                min-height: 29px;

                align-items: center;
                justify-content: space-between;

                border-bottom:
                    1px solid
                    rgba(160,119,77,.28);

                background: #f0e4d5;

                padding:
                    0 9px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .075em;

                text-transform: uppercase;

                color: #6f472f;
            }

            .training-v3-editor-content {
                padding:
                    9px;
            }

            .training-v3-editor-note {
                margin-top: 5px;

                font-size: 9px;
                line-height: 1.4;

                color: #8c705a;
            }

            .training-v3-modal-footer {
                display: flex;

                flex: 0 0 auto;

                align-items: center;
                justify-content: flex-end;

                border-top:
                    1px solid
                    rgba(160,119,77,.30);

                background: #f7f0e6;

                padding:
                    9px 13px;
            }

            .training-v3-done {
                display: inline-flex;

                min-height: 35px;

                align-items: center;
                justify-content: center;

                border-radius: 7px;

                background: #6b1d14;

                padding:
                    0 12px;

                font-size: 9.5px;
                font-weight: 900;
                letter-spacing: .025em;

                text-transform: uppercase;

                color: #faf8f2;
            }

            .training-v3-done:hover {
                background: #53150f;
            }

            @media (max-width: 520px) {
                .training-v3-summary-title {
                    white-space: normal;
                }

                .training-v3-armor {
                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | TRAINING V4 — LEGIBILIDADE + PADRÃO VISUAL
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | RESUMO NA FICHA
            |--------------------------------------------------------------------------
            */

            .training-v4 .training-v3-summary-header {
                min-height: 35px;

                padding:
                    0 10px;
            }

            .training-v4 .training-v3-summary-title {
                font-size: 10px;
                line-height: 1.15;
                letter-spacing: .07em;
            }

            .training-v4 .training-v3-summary-config {
                font-size: 9px;
            }

            .training-v4 .training-v3-summary-section {
                padding:
                    8px 9px 9px;
            }

            .training-v4 .training-v3-summary-label {
                margin-bottom: 5px;

                font-size: 7.5px;
                letter-spacing: .085em;
            }

            .training-v4 .training-v3-summary-value {
                font-size: 12.5px;
                line-height: 1.42;
            }

            .training-v4 .training-v3-armor {
                gap: 4px;
            }

            .training-v4 .training-v3-armor-item {
                gap: 4px;

                font-size: 8px;
            }

            .training-v4 .training-v3-diamond {
                width: 8px;
                height: 8px;

                flex-basis: 8px;
            }

            .training-v4 .training-v3-roll-summary {
                gap: 4px;
            }

            .training-v4 .training-v3-roll-summary-button {
                min-height: 27px;

                padding:
                    3px 4px;
            }

            .training-v4 .training-v3-roll-summary-name {
                font-size: 12px;
            }

            .training-v4 .training-v3-roll-summary-bonus {
                font-size: 11px;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL DE CONFIGURAÇÃO
            |--------------------------------------------------------------------------
            */

            .training-v4 .training-v3-modal {
                width:
                    min(
                        780px,
                        calc(100vw - 28px)
                    );
            }

            .training-v4 .training-v3-modal-header {
                padding:
                    14px 16px;
            }

            .training-v4 .training-v3-modal-kicker {
                font-size: 9px;
            }

            .training-v4 .training-v3-modal-title {
                font-size: 22px;
            }

            .training-v4 .training-v3-modal-body {
                padding: 14px;
            }

            .training-v4 .training-v3-editor-section +
            .training-v3-editor-section {
                margin-top: 11px;
            }

            .training-v4 .training-v3-editor-title {
                min-height: 33px;

                padding:
                    0 10px;

                font-size: 10px;
            }

            .training-v4 .training-v3-editor-content {
                padding: 11px;
            }

            .training-v4 .training-v3-editor-note {
                font-size: 10.5px;
            }

            /*
            | Opções do modal
            */

            .training-v4 .training-v2-option {
                min-height: 33px;

                gap: 7px;

                padding:
                    0 10px;

                font-size: 10.5px;
            }

            .training-v4 .training-v2-check {
                width: 13px;
                height: 13px;

                flex: 0 0 13px;

                font-size: 8px;
            }

            .training-v4 .training-v2-input {
                min-height: 36px;

                font-size: 12.5px;
            }

            .training-v4 .training-v2-add {
                min-height: 36px;

                padding:
                    0 11px;

                font-size: 10px;
            }

            .training-v4 .training-v2-tag {
                min-height: 29px;
            }

            .training-v4 .training-v2-tag span {
                font-size: 10.5px;
            }

            /*
            |--------------------------------------------------------------------------
            | LINHAS ROLÁVEIS
            |--------------------------------------------------------------------------
            */

            .training-v4 .training-v2-roll-row {
                grid-template-columns:
                    minmax(0, 1fr)
                    58px
                    91px
                    29px;

                min-height: 43px;
            }

            .training-v4 .training-v2-roll-name {
                padding:
                    0 10px;

                font-size: 12px;
            }

            .training-v4 .training-v2-ability {
                height: 31px;

                padding:
                    0 5px;

                font-size: 10px;
            }

            .training-v4 .training-v2-roll {
                height: 31px;

                gap: 5px;

                padding:
                    0 8px;

                font-size: 10.5px;
            }

            .training-v4 .training-v2-remove {
                width: 29px;
                height: 29px;

                font-size: 15px;
            }

            /*
            |--------------------------------------------------------------------------
            | RESULTADO DE DADOS — MESMO PADRÃO DE ATRIBUTOS / ATAQUES
            |--------------------------------------------------------------------------
            |
            | Reutiliza exatamente a linguagem visual character-roll-toast.
            |
            */

            .character-roll-toast {
                border-color:
                    rgba(176,140,98,.64) !important;

                background:
                    #fbf8f1 !important;

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

            @media (max-width: 520px) {
                .training-v4 .training-v3-summary-title {
                    font-size: 9.5px;
                }

                .training-v4 .training-v3-armor-item {
                    font-size: 8.5px;
                }

                .training-v4 .training-v2-roll-row {
                    grid-template-columns:
                        minmax(0, 1fr)
                        53px
                        82px
                        27px;
                }
            }

        </style>
    @endpush
@endonce

<section
    x-data="{
        entries: @js($trainingPayload),

        currentLevel: {{ $level }},
        proficiencyBonus: {{ $proficiencyBonus }},

        exhaustionLevel:
            {{ $initialExhaustionLevel }},

        abilityModifiers:
            @js($abilityModifiers),

        abilityDefinitions:
            @js($abilityDefinitions),

        diceRollUrl:
            @js(url('/api/roll')),

        vehiclesOpen:
            @js(
                $trainingPayload
                    ->contains(
                        fn ($entry) =>
                            $entry['type']
                            === 'vehicle_proficiency'
                    )
            ),

        editorOpen: false,

        openEditor() {
            this.editorOpen = true;
        },

        closeEditor() {
            this.editorOpen = false;
        },

        savingKey: null,

        customWeapon: '',
        customTool: '',
        customLanguage: '',
        customVehicle: '',

        rolling: false,
        lastRoll: null,
        rollToastOpen: false,
        rollToastTimer: null,

        armorOptions: [
            'Armaduras Leves',
            'Armaduras Médias',
            'Armaduras Pesadas',
            'Escudos',
        ],

        weaponOptions: [
            'Armas Simples',
            'Armas Marciais',
            'Todas as Armas',
        ],

        languageOptions: [
            'Comum',
            'Anão',
            'Élfico',
            'Gigante',
            'Gnômico',
            'Goblin',
            'Halfling',
            'Orc',
            'Abissal',
            'Celestial',
            'Dracônico',
            'Dialeto Profundo',
            'Infernal',
            'Primordial',
            'Silvestre',
            'Subcomum',
        ],

        toolOptions: [
            {
                name:
                    'Ferramentas de Ladrão',
                ability:
                    'dexterity',
            },
            {
                name:
                    'Kit de Herbalismo',
                ability:
                    'wisdom',
            },
            {
                name:
                    'Kit de Disfarce',
                ability:
                    'charisma',
            },
            {
                name:
                    'Kit de Falsificação',
                ability:
                    'intelligence',
            },
            {
                name:
                    'Kit de Veneno',
                ability:
                    'intelligence',
            },
            {
                name:
                    'Ferramentas de Navegador',
                ability:
                    'wisdom',
            },
            {
                name:
                    'Suprimentos de Alquimista',
                ability:
                    'intelligence',
            },
            {
                name:
                    'Ferramentas de Ferreiro',
                ability:
                    'strength',
            },
            {
                name:
                    'Ferramentas de Carpinteiro',
                ability:
                    'strength',
            },
            {
                name:
                    'Ferramentas de Artesão',
                ability:
                    'intelligence',
            },
        ],

        vehicleOptions: [
            {
                name:
                    'Veículos Terrestres',
                ability:
                    'wisdom',
            },
            {
                name:
                    'Veículos Aquáticos',
                ability:
                    'wisdom',
            },
            {
                name:
                    'Veículos Aéreos',
                ability:
                    'wisdom',
            },
        ],

        urls: {
            store:
                @js(
                    route(
                        'characters.features.store',
                        $character
                    )
                ),

            update:
                @js(
                    route(
                        'characters.features.update',
                        [
                            'character' =>
                                $character,

                            'feature' =>
                                '__FEATURE__',
                        ]
                    )
                ),

            destroy:
                @js(
                    route(
                        'characters.features.destroy',
                        [
                            'character' =>
                                $character,

                            'feature' =>
                                '__FEATURE__',
                        ]
                    )
                ),
        },

        normalizeName(value) {
            return String(
                value ?? ''
            )
                .trim()
                .toLocaleLowerCase(
                    'pt-BR'
                );
        },

        normalizeEntry(raw) {
            const data =
                raw?.data
                && typeof raw.data
                    === 'object'
                && !Array.isArray(
                    raw.data
                )
                    ? {
                        ...raw.data,
                    }
                    : {};

            return {
                id:
                    raw?.id ?? null,

                name:
                    raw?.name ?? '',

                type:
                    raw?.type ?? '',

                source:
                    raw?.source ?? null,

                level_acquired:
                    raw?.level_acquired
                    ?? null,

                description:
                    raw?.description
                    ?? null,

                data: {
                    ...data,

                    roll_ability:
                        (
                            data.roll_ability
                            && Object.prototype.hasOwnProperty.call(
                                this.abilityDefinitions,
                                data.roll_ability
                            )
                        )
                            ? data.roll_ability
                            : null,
                },
            };
        },

        entriesOf(type) {
            return this.entries
                .filter(
                    entry =>
                        entry.type === type
                )
                .map(
                    entry =>
                        this.normalizeEntry(
                            entry
                        )
                );
        },

        findEntry(type, name) {
            const normalized =
                this.normalizeName(
                    name
                );

            return this.entries
                .find(
                    entry =>
                        entry.type
                            === type
                        && this.normalizeName(
                            entry.name
                        ) === normalized
                )
                ?? null;
        },

        has(type, name) {
            return this.findEntry(
                type,
                name
            ) !== null;
        },

        count(type) {
            return this.entries
                .filter(
                    entry =>
                        entry.type
                            === type
                )
                .length;
        },

        isPreset(type, name) {
            if (
                type
                === 'weapon_training'
            ) {
                return this.weaponOptions
                    .some(
                        option =>
                            this.normalizeName(
                                option
                            )
                            === this.normalizeName(
                                name
                            )
                    );
            }

            if (
                type
                === 'language'
            ) {
                return this.languageOptions
                    .some(
                        option =>
                            this.normalizeName(
                                option
                            )
                            === this.normalizeName(
                                name
                            )
                    );
            }

            return false;
        },

        get customWeapons() {
            return this.entriesOf(
                'weapon_training'
            )
                .filter(
                    entry =>
                        !this.isPreset(
                            'weapon_training',
                            entry.name
                        )
                );
        },

        get customLanguages() {
            return this.entriesOf(
                'language'
            )
                .filter(
                    entry =>
                        !this.isPreset(
                            'language',
                            entry.name
                        )
                );
        },

        get toolEntries() {
            return this.entriesOf(
                'tool_proficiency'
            );
        },

        get vehicleEntries() {
            return this.entriesOf(
                'vehicle_proficiency'
            );
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

        payload(
            type,
            name,
            rollAbility = null
        ) {
            return {
                name:
                    String(name).trim(),

                type,

                source:
                    null,

                level_acquired:
                    this.currentLevel,

                description:
                    null,

                uses_max:
                    null,

                uses_current:
                    null,

                recovery:
                    null,

                data: {
                    activation:
                        'passive',

                    quick_text:
                        null,

                    counter_mode:
                        'spend',

                    recovery_custom:
                        null,

                    column:
                        'left',

                    roll_ability:
                        rollAbility,
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
                        ) === parseInt(
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

        async createEntry(
            type,
            name,
            rollAbility = null
        ) {
            const cleanName =
                String(
                    name ?? ''
                ).trim();

            if (
                !cleanName
                || this.findEntry(
                    type,
                    cleanName
                )
            ) {
                return;
            }

            const key =
                `${type}:${this.normalizeName(cleanName)}`;

            if (this.savingKey) {
                return;
            }

            this.savingKey =
                key;

            try {
                const response =
                    await fetch(
                        this.urls.store,
                        {
                            method:
                                'POST',

                            headers:
                                this.headers(),

                            body:
                                JSON.stringify(
                                    this.payload(
                                        type,
                                        cleanName,
                                        rollAbility
                                    )
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

            } catch (error) {
                console.error(
                    'Erro ao adicionar proficiência:',
                    error
                );

                window.alert(
                    error?.message
                    ?? 'Não foi possível salvar.'
                );

            } finally {
                this.savingKey = null;
            }
        },

        async deleteEntry(entry) {
            if (
                !entry?.id
                || this.savingKey
            ) {
                return;
            }

            this.savingKey =
                `delete:${entry.id}`;

            try {
                const response =
                    await fetch(
                        this.urls.destroy
                            .replace(
                                '__FEATURE__',
                                entry.id
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
                        ?? 'Não foi possível remover.'
                    );
                }

                this.entries =
                    this.entries.filter(
                        current =>
                            parseInt(
                                current.id
                            ) !== parseInt(
                                entry.id
                            )
                    );

            } catch (error) {
                console.error(
                    'Erro ao remover proficiência:',
                    error
                );

                window.alert(
                    error?.message
                    ?? 'Não foi possível remover.'
                );

            } finally {
                this.savingKey = null;
            }
        },

        async toggle(
            type,
            name,
            rollAbility = null
        ) {
            const existing =
                this.findEntry(
                    type,
                    name
                );

            if (existing) {
                await this.deleteEntry(
                    existing
                );

                return;
            }

            await this.createEntry(
                type,
                name,
                rollAbility
            );
        },

        async toggleWeapon(option) {
            const allWeapons =
                'Todas as Armas';

            if (
                option === allWeapons
            ) {
                const existingAll =
                    this.findEntry(
                        'weapon_training',
                        allWeapons
                    );

                if (existingAll) {
                    await this.deleteEntry(
                        existingAll
                    );

                    return;
                }

                for (const group of [
                    'Armas Simples',
                    'Armas Marciais',
                ]) {
                    const existing =
                        this.findEntry(
                            'weapon_training',
                            group
                        );

                    if (existing) {
                        await this.deleteEntry(
                            existing
                        );
                    }
                }

                await this.createEntry(
                    'weapon_training',
                    allWeapons
                );

                return;
            }

            const existingAll =
                this.findEntry(
                    'weapon_training',
                    allWeapons
                );

            if (existingAll) {
                await this.deleteEntry(
                    existingAll
                );
            }

            await this.toggle(
                'weapon_training',
                option
            );
        },

        async addCustom(
            type,
            property,
            rollAbility = null
        ) {
            const value =
                String(
                    this[property]
                    ?? ''
                ).trim();

            if (!value) {
                return;
            }

            await this.createEntry(
                type,
                value,
                rollAbility
            );

            this[property] = '';
        },

        async updateRollAbility(
            raw,
            ability
        ) {
            const id =
                parseInt(
                    raw?.id
                );

            const validAbility =
                Object.prototype
                    .hasOwnProperty
                    .call(
                        this.abilityDefinitions,
                        ability
                    );

            if (
                !id
                || !validAbility
                || this.savingKey
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Trabalha sobre o registro REAL de entries
            |--------------------------------------------------------------------------
            |
            | toolEntries / vehicleEntries são getters e retornam objetos
            | normalizados. Por isso atualizamos a fonte reativa principal
            | diretamente pelo ID.
            |
            */

            const index =
                this.entries.findIndex(
                    current =>
                        parseInt(
                            current.id
                        ) === id
                );

            if (index < 0) {
                return;
            }

            const current =
                this.normalizeEntry(
                    this.entries[index]
                );

            const oldAbility =
                current.data.roll_ability;

            const updated = {
                ...current,

                data: {
                    ...current.data,

                    roll_ability:
                        ability,
                },
            };

            /*
            |--------------------------------------------------------------------------
            | Atualização otimista
            |--------------------------------------------------------------------------
            */

            this.entries.splice(
                index,
                1,
                updated
            );

            this.savingKey =
                `update:${id}`;

            try {
                const response =
                    await fetch(
                        this.urls.update
                            .replace(
                                '__FEATURE__',
                                id
                            ),
                        {
                            method:
                                'PATCH',

                            headers:
                                this.headers(),

                            body:
                                JSON.stringify({
                                    data: {
                                        ...updated.data,

                                        roll_ability:
                                            ability,
                                    },
                                }),
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
                        ?? 'Não foi possível atualizar o atributo.'
                    );
                }

                const saved =
                    this.upsert(
                        data.feature
                    );

                /*
                |--------------------------------------------------------------------------
                | Segurança
                |--------------------------------------------------------------------------
                |
                | Se o backend não devolver o atributo solicitado, não deixamos
                | a interface silenciosamente voltar para Inteligência.
                |
                */

                if (
                    saved?.data?.roll_ability
                    !== ability
                ) {
                    throw new Error(
                        'O atributo escolhido não foi persistido pelo servidor.'
                    );
                }

            } catch (error) {
                const rollback =
                    this.normalizeEntry(
                        this.entries[
                            this.entries.findIndex(
                                currentEntry =>
                                    parseInt(
                                        currentEntry.id
                                    ) === id
                            )
                        ]
                        ?? current
                    );

                rollback.data.roll_ability =
                    oldAbility;

                const rollbackIndex =
                    this.entries.findIndex(
                        currentEntry =>
                            parseInt(
                                currentEntry.id
                            ) === id
                    );

                if (rollbackIndex >= 0) {
                    this.entries.splice(
                        rollbackIndex,
                        1,
                        rollback
                    );
                }

                console.error(
                    'Erro ao atualizar atributo:',
                    error
                );

                window.alert(
                    error?.message
                    ?? 'Não foi possível atualizar o atributo.'
                );

            } finally {
                this.savingKey = null;
            }
        },

        abilityShort(ability) {
            return this.abilityDefinitions[
                ability
            ]?.short
            ?? 'INT';
        },


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


        baseRollBonus(raw) {
            const entry =
                this.normalizeEntry(
                    raw
                );

            const ability =
                entry.data.roll_ability
                ?? 'intelligence';

            return (
                parseInt(
                    this.abilityModifiers[
                        ability
                    ]
                ) || 0
            ) + this.proficiencyBonus;
        },


        rollBonus(raw) {
            return (
                this.baseRollBonus(
                    raw
                )
                -
                this.exhaustionRollPenalty
            );
        },

        signed(value) {
            const number =
                parseInt(value) || 0;

            return number >= 0
                ? `+${number}`
                : `${number}`;
        },

        rollExpression(raw) {
            const bonus =
                this.rollBonus(
                    raw
                );

            if (bonus > 0) {
                return `1d20+${bonus}`;
            }

            if (bonus < 0) {
                return `1d20${bonus}`;
            }

            return '1d20';
        },

        async roll(raw) {
            if (
                this.rolling
                || !raw
            ) {
                return;
            }

            const entry =
                this.normalizeEntry(
                    raw
                );

            const expression =
                this.rollExpression(
                    entry
                );

            const baseBonus =
                this.baseRollBonus(
                    entry
                );

            const exhaustionPenalty =
                this.exhaustionRollPenalty;

            const bonus =
                baseBonus
                -
                exhaustionPenalty;

            this.rolling = true;

            let total = null;
            let die = null;
            let formatted = null;

            try {
                const response =
                    await fetch(
                        this.diceRollUrl,
                        {
                            method:
                                'POST',

                            headers:
                                this.headers(),

                            body:
                                JSON.stringify({
                                    expression,
                                }),
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
                        ?? 'Não foi possível rolar.'
                    );
                }

                total =
                    parseInt(
                        data?.data?.total
                        ?? data?.data?.result
                        ?? data?.data?.value
                        ?? data?.data?.sum
                        ?? data?.total
                        ?? data?.result
                    );

                formatted =
                    data?.formatted
                    ?? null;

                if (Number.isNaN(total)) {
                    total = null;
                }

                if (total !== null) {
                    die =
                        total - bonus;
                }

            } catch (error) {
                console.error(
                    'Erro ao rolar dado:',
                    error
                );

                die =
                    Math.floor(
                        Math.random() * 20
                    ) + 1;

                total =
                    die + bonus;

            } finally {
                this.rolling = false;
            }

            this.lastRoll = {
                label:
                    entry.name,

                expression,

                baseModifier:
                    baseBonus,

                exhaustionPenalty,

                modifier:
                    bonus,

                die,

                total,

                formatted,
            };

            this.rollToastOpen = true;

            window.dispatchEvent(
                new CustomEvent(
                    'character-dice-rolled',
                    {
                        detail:
                            this.lastRoll,
                    }
                )
            );

            if (this.rollToastTimer) {
                clearTimeout(
                    this.rollToastTimer
                );
            }

            this.rollToastTimer =
                setTimeout(
                    () => {
                        this.rollToastOpen =
                            false;
                    },
                    4200
                );
        },
    }"

    @character-exhaustion-updated.window="
        syncExhaustion(
            $event.detail
        )
    "

    @keydown.escape.window="
        if (editorOpen) {
            closeEditor()
        }
    "

    class="training-v2 training-v3 training-v4"
>

    {{-- ================================================================
         RESUMO COMPACTO NA FICHA
    ================================================================= --}}
    <div class="training-v3-summary">
        <button
            type="button"
            @click="openEditor()"
            class="training-v3-summary-header"
            title="Configurar treinamentos e proficiências"
        >
            <span class="training-v3-summary-title">
                Treinamento & Proficiências
            </span>


        </button>


        {{-- ARMADURAS --}}
        <div class="training-v3-summary-section">
            <span class="training-v3-summary-label">
                Treinamento com Armadura
            </span>

            <div class="training-v3-armor">
                <span
                    class="training-v3-armor-item"
                    :class="{
                        'active':
                            has(
                                'armor_training',
                                'Armaduras Leves'
                            )
                    }"
                >
                    <span class="training-v3-diamond"></span>
                    Leve
                </span>

                <span
                    class="training-v3-armor-item"
                    :class="{
                        'active':
                            has(
                                'armor_training',
                                'Armaduras Médias'
                            )
                    }"
                >
                    <span class="training-v3-diamond"></span>
                    Média
                </span>

                <span
                    class="training-v3-armor-item"
                    :class="{
                        'active':
                            has(
                                'armor_training',
                                'Armaduras Pesadas'
                            )
                    }"
                >
                    <span class="training-v3-diamond"></span>
                    Pesada
                </span>

                <span
                    class="training-v3-armor-item"
                    :class="{
                        'active':
                            has(
                                'armor_training',
                                'Escudos'
                            )
                    }"
                >
                    <span class="training-v3-diamond"></span>
                    Escudos
                </span>
            </div>
        </div>


        {{-- ARMAS --}}
        <div class="training-v3-summary-section">
            <span class="training-v3-summary-label">
                Armas
            </span>

            <div
                class="training-v3-summary-value"
                :class="{
                    'training-v3-summary-empty':
                        count('weapon_training') === 0
                }"
                x-text="
                    count('weapon_training') === 0
                        ? '—'
                        : (
                            has(
                                'weapon_training',
                                'Todas as Armas'
                            )
                                ? [
                                    'Todas as Armas',
                                    ...customWeapons.map(
                                        entry => entry.name
                                    )
                                ].join(', ')
                                : [
                                    ...(
                                        has(
                                            'weapon_training',
                                            'Armas Simples'
                                        )
                                            ? ['Armas Simples']
                                            : []
                                    ),
                                    ...(
                                        has(
                                            'weapon_training',
                                            'Armas Marciais'
                                        )
                                            ? ['Armas Marciais']
                                            : []
                                    ),
                                    ...customWeapons.map(
                                        entry => entry.name
                                    )
                                ].join(', ')
                        )
                "
            ></div>
        </div>


        {{-- FERRAMENTAS --}}
        <div class="training-v3-summary-section">
            <span class="training-v3-summary-label">
                Ferramentas & Kits
            </span>

            <div
                x-show="toolEntries.length > 0"
                x-cloak
                class="training-v3-roll-summary"
            >
                <template
                    x-for="entry in toolEntries"
                    :key="'summary-tool-' + entry.id"
                >
                    <button
                        type="button"
                        @click="roll(entry)"
                        class="training-v3-roll-summary-button"
                        :title="
                            'Rolar '
                            + entry.name
                            + ' ('
                            + rollExpression(entry)
                            + ')'
                        "
                    >
                        <span
                            class="training-v3-roll-summary-name"
                            x-text="entry.name"
                        ></span>

                        <strong
                            class="training-v3-roll-summary-bonus"
                            x-text="
                                '('
                                + signed(
                                    rollBonus(entry)
                                )
                                + ')'
                            "
                        ></strong>
                    </button>
                </template>
            </div>

            <div
                x-show="toolEntries.length === 0"
                x-cloak
                class="
                    training-v3-summary-value
                    training-v3-summary-empty
                "
            >
                —
            </div>
        </div>


        {{-- IDIOMAS --}}
        <div class="training-v3-summary-section">
            <span class="training-v3-summary-label">
                Idiomas
            </span>

            <div
                class="training-v3-summary-value"
                :class="{
                    'training-v3-summary-empty':
                        count('language') === 0
                }"
                x-text="
                    count('language') === 0
                        ? '—'
                        : entriesOf('language')
                            .map(
                                entry => entry.name
                            )
                            .join(', ')
                "
            ></div>
        </div>


        {{-- VEÍCULOS --}}
        <div
            x-show="vehicleEntries.length > 0"
            x-cloak
            class="training-v3-summary-section"
        >
            <span class="training-v3-summary-label">
                Veículos
            </span>

            <div class="training-v3-roll-summary">
                <template
                    x-for="entry in vehicleEntries"
                    :key="'summary-vehicle-' + entry.id"
                >
                    <button
                        type="button"
                        @click="roll(entry)"
                        class="training-v3-roll-summary-button"
                    >
                        <span
                            class="training-v3-roll-summary-name"
                            x-text="entry.name"
                        ></span>

                        <strong
                            class="training-v3-roll-summary-bonus"
                            x-text="
                                '('
                                + signed(
                                    rollBonus(entry)
                                )
                                + ')'
                            "
                        ></strong>
                    </button>
                </template>
            </div>
        </div>
    </div>


    {{-- ================================================================
         MODAL DE CONFIGURAÇÃO
    ================================================================= --}}
    <template x-teleport="body">
        <div
            x-show="editorOpen"
            x-cloak
            class="
                fixed
                inset-0
                z-[245]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="training-v3-backdrop"
                @click="closeEditor()"
            ></div>

            <article
                class="training-v3-modal"
                @click.stop
            >
                <header class="training-v3-modal-header">
                    <div>
                        <span class="training-v3-modal-kicker">
                            Ficha do personagem
                        </span>

                        <h3 class="training-v3-modal-title">
                            Treinamento & Proficiências
                        </h3>
                    </div>

                    <button
                        type="button"
                        @click="closeEditor()"
                        class="training-v3-modal-close"
                        title="Fechar"
                    >
                        ×
                    </button>
                </header>


                <div class="training-v3-modal-body">
                    {{-- ARMADURAS --}}
                    <section class="training-v3-editor-section">
                        <header class="training-v3-editor-title">
                            <span>Treinamento com Armadura</span>

                            <span
                                x-text="count('armor_training')"
                            ></span>
                        </header>

                        <div class="training-v3-editor-content">
                            <div class="training-v2-options">
                                <template
                                    x-for="option in armorOptions"
                                    :key="'armor-modal-' + option"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            toggle(
                                                'armor_training',
                                                option
                                            )
                                        "
                                        class="training-v2-option"
                                        :class="{
                                            'active':
                                                has(
                                                    'armor_training',
                                                    option
                                                )
                                        }"
                                    >
                                        <span class="training-v2-check">
                                            ✓
                                        </span>

                                        <span x-text="option"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </section>


                    {{-- ARMAS --}}
                    <section class="training-v3-editor-section">
                        <header class="training-v3-editor-title">
                            <span>Armas</span>

                            <span
                                x-text="count('weapon_training')"
                            ></span>
                        </header>

                        <div class="training-v3-editor-content">
                            <div class="training-v2-options">
                                <template
                                    x-for="option in weaponOptions"
                                    :key="'weapon-modal-' + option"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            toggle(
                                                'weapon_training',
                                                option
                                            )
                                        "
                                        class="training-v2-option"
                                        :class="{
                                            'active':
                                                has(
                                                    'weapon_training',
                                                    option
                                                )
                                        }"
                                    >
                                        <span class="training-v2-check">
                                            ✓
                                        </span>

                                        <span x-text="option"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="training-v2-custom">
                                <input
                                    type="text"
                                    x-model="customWeapon"
                                    @keydown.enter.prevent="
                                        addCustom(
                                            'weapon_training',
                                            'customWeapon'
                                        )
                                    "
                                    class="training-v2-input"
                                    placeholder="Arma específica, ex.: Rapieira"
                                >

                                <button
                                    type="button"
                                    @click="
                                        addCustom(
                                            'weapon_training',
                                            'customWeapon'
                                        )
                                    "
                                    class="training-v2-add"
                                >
                                    Adicionar
                                </button>
                            </div>

                            <div
                                x-show="customWeapons.length > 0"
                                x-cloak
                                class="training-v2-tags"
                            >
                                <template
                                    x-for="entry in customWeapons"
                                    :key="'weapon-tag-modal-' + entry.id"
                                >
                                    <span class="training-v2-tag">
                                        <span x-text="entry.name"></span>

                                        <button
                                            type="button"
                                            @click="deleteEntry(entry)"
                                        >
                                            ×
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </section>


                    {{-- FERRAMENTAS --}}
                    <section class="training-v3-editor-section">
                        <header class="training-v3-editor-title">
                            <span>Ferramentas & Kits</span>

                            <span
                                x-text="count('tool_proficiency')"
                            ></span>
                        </header>

                        <div class="training-v3-editor-content">
                            <div class="training-v2-options">
                                <template
                                    x-for="option in toolOptions"
                                    :key="'tool-modal-' + option.name"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            toggle(
                                                'tool_proficiency',
                                                option.name,
                                                option.ability
                                            )
                                        "
                                        class="training-v2-option"
                                        :class="{
                                            'active':
                                                has(
                                                    'tool_proficiency',
                                                    option.name
                                                )
                                        }"
                                    >
                                        <span class="training-v2-check">
                                            ✓
                                        </span>

                                        <span x-text="option.name"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="training-v2-custom">
                                <input
                                    type="text"
                                    x-model="customTool"
                                    @keydown.enter.prevent="
                                        addCustom(
                                            'tool_proficiency',
                                            'customTool',
                                            'intelligence'
                                        )
                                    "
                                    class="training-v2-input"
                                    placeholder="Outra ferramenta ou kit"
                                >

                                <button
                                    type="button"
                                    @click="
                                        addCustom(
                                            'tool_proficiency',
                                            'customTool',
                                            'intelligence'
                                        )
                                    "
                                    class="training-v2-add"
                                >
                                    Adicionar
                                </button>
                            </div>

                            <div
                                x-show="toolEntries.length > 0"
                                x-cloak
                                class="training-v2-roll-list"
                            >
                                <template
                                    x-for="entry in toolEntries"
                                    :key="'tool-modal-row-' + entry.id"
                                >
                                    <div class="training-v2-roll-row">
                                        <span
                                            class="training-v2-roll-name"
                                            x-text="entry.name"
                                        ></span>

                                        <select
                                            :value="
                                                entry.data?.roll_ability
                                                ?? 'intelligence'
                                            "
                                            @change="
                                                updateRollAbility(
                                                    entry,
                                                    $event.target.value
                                                )
                                            "
                                            class="training-v2-ability"
                                        >
                                            <template
                                                x-for="
                                                    (
                                                        definition,
                                                        abilityKey
                                                    ) in abilityDefinitions
                                                "
                                                :key="'tool-modal-ability-' + abilityKey"
                                            >
                                                <option
                                                    :value="abilityKey"
                                                    x-text="definition.short"
                                                ></option>
                                            </template>
                                        </select>

                                        <button
                                            type="button"
                                            @click="roll(entry)"
                                            class="training-v2-roll"
                                        >
                                            <span>🎲</span>

                                            <span
                                                x-text="rollExpression(entry)"
                                            ></span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="deleteEntry(entry)"
                                            class="training-v2-remove"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </section>


                    {{-- IDIOMAS --}}
                    <section class="training-v3-editor-section">
                        <header class="training-v3-editor-title">
                            <span>Idiomas</span>

                            <span
                                x-text="count('language')"
                            ></span>
                        </header>

                        <div class="training-v3-editor-content">
                            <div class="training-v2-options">
                                <template
                                    x-for="option in languageOptions"
                                    :key="'language-modal-' + option"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            toggle(
                                                'language',
                                                option
                                            )
                                        "
                                        class="training-v2-option"
                                        :class="{
                                            'active':
                                                has(
                                                    'language',
                                                    option
                                                )
                                        }"
                                    >
                                        <span class="training-v2-check">
                                            ✓
                                        </span>

                                        <span x-text="option"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="training-v2-custom">
                                <input
                                    type="text"
                                    x-model="customLanguage"
                                    @keydown.enter.prevent="
                                        addCustom(
                                            'language',
                                            'customLanguage'
                                        )
                                    "
                                    class="training-v2-input"
                                    placeholder="Outro idioma"
                                >

                                <button
                                    type="button"
                                    @click="
                                        addCustom(
                                            'language',
                                            'customLanguage'
                                        )
                                    "
                                    class="training-v2-add"
                                >
                                    Adicionar
                                </button>
                            </div>

                            <div
                                x-show="customLanguages.length > 0"
                                x-cloak
                                class="training-v2-tags"
                            >
                                <template
                                    x-for="entry in customLanguages"
                                    :key="'language-tag-modal-' + entry.id"
                                >
                                    <span class="training-v2-tag">
                                        <span x-text="entry.name"></span>

                                        <button
                                            type="button"
                                            @click="deleteEntry(entry)"
                                        >
                                            ×
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </section>


                    {{-- VEÍCULOS --}}
                    <section class="training-v3-editor-section">
                        <header class="training-v3-editor-title">
                            <span>Veículos · Opcional</span>

                            <button
                                type="button"
                                @click="vehiclesOpen = !vehiclesOpen"
                                class="training-v2-add"
                            >
                                <span
                                    x-text="
                                        vehiclesOpen
                                            ? 'Ocultar'
                                            : 'Ativar'
                                    "
                                ></span>
                            </button>
                        </header>

                        <div
                            x-show="vehiclesOpen"
                            x-cloak
                            class="training-v3-editor-content"
                        >
                            <div class="training-v2-options">
                                <template
                                    x-for="option in vehicleOptions"
                                    :key="'vehicle-modal-' + option.name"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            toggle(
                                                'vehicle_proficiency',
                                                option.name,
                                                option.ability
                                            )
                                        "
                                        class="training-v2-option"
                                        :class="{
                                            'active':
                                                has(
                                                    'vehicle_proficiency',
                                                    option.name
                                                )
                                        }"
                                    >
                                        <span class="training-v2-check">
                                            ✓
                                        </span>

                                        <span x-text="option.name"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="training-v2-custom">
                                <input
                                    type="text"
                                    x-model="customVehicle"
                                    @keydown.enter.prevent="
                                        addCustom(
                                            'vehicle_proficiency',
                                            'customVehicle',
                                            'wisdom'
                                        )
                                    "
                                    class="training-v2-input"
                                    placeholder="Veículo específico"
                                >

                                <button
                                    type="button"
                                    @click="
                                        addCustom(
                                            'vehicle_proficiency',
                                            'customVehicle',
                                            'wisdom'
                                        )
                                    "
                                    class="training-v2-add"
                                >
                                    Adicionar
                                </button>
                            </div>

                            <div
                                x-show="vehicleEntries.length > 0"
                                x-cloak
                                class="training-v2-roll-list"
                            >
                                <template
                                    x-for="entry in vehicleEntries"
                                    :key="'vehicle-modal-row-' + entry.id"
                                >
                                    <div class="training-v2-roll-row">
                                        <span
                                            class="training-v2-roll-name"
                                            x-text="entry.name"
                                        ></span>

                                        <select
                                            :value="
                                                entry.data?.roll_ability
                                                ?? 'wisdom'
                                            "
                                            @change="
                                                updateRollAbility(
                                                    entry,
                                                    $event.target.value
                                                )
                                            "
                                            class="training-v2-ability"
                                        >
                                            <template
                                                x-for="
                                                    (
                                                        definition,
                                                        abilityKey
                                                    ) in abilityDefinitions
                                                "
                                                :key="'vehicle-modal-ability-' + abilityKey"
                                            >
                                                <option
                                                    :value="abilityKey"
                                                    x-text="definition.short"
                                                ></option>
                                            </template>
                                        </select>

                                        <button
                                            type="button"
                                            @click="roll(entry)"
                                            class="training-v2-roll"
                                        >
                                            <span>🎲</span>

                                            <span
                                                x-text="rollExpression(entry)"
                                            ></span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="deleteEntry(entry)"
                                            class="training-v2-remove"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </section>
                </div>


                <footer class="training-v3-modal-footer">
                    <button
                        type="button"
                        @click="closeEditor()"
                        class="training-v3-done"
                    >
                        Concluir
                    </button>
                </footer>
            </article>
        </div>
    </template>


    {{-- ============================================================
         RESULTADO DA ROLAGEM — PADRÃO GLOBAL
    ============================================================= --}}
    <template x-teleport="body">
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

                        <span x-show="lastRoll?.baseModifier !== 0">
                            ·
                            <span
                                x-text="
                                    signed(
                                        lastRoll?.baseModifier
                                        ?? 0
                                    )
                                "
                            ></span>
                            modificador
                        </span>

                        <span
                            x-show="
                                (lastRoll?.exhaustionPenalty ?? 0) > 0
                            "
                            x-cloak
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
                    title="Fechar"
                >
                    ×
                </button>
            </div>
        </div>
    </template>

</section>