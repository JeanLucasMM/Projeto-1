@props(['character'])

@php
    $abilityLabels = [
        'strength' => 'Força',
        'dexterity' => 'Destreza',
        'constitution' => 'Constituição',
        'intelligence' => 'Inteligência',
        'wisdom' => 'Sabedoria',
        'charisma' => 'Carisma',
    ];

    $damageTypeLabels = [
        'acid' => 'Ácido',
        'bludgeoning' => 'Impacto',
        'cold' => 'Frio',
        'fire' => 'Fogo',
        'force' => 'Energia',
        'lightning' => 'Elétrico',
        'necrotic' => 'Necrótico',
        'piercing' => 'Perfurante',
        'poison' => 'Veneno',
        'psychic' => 'Psíquico',
        'radiant' => 'Radiante',
        'slashing' => 'Cortante',
        'thunder' => 'Trovejante',
    ];

    $masteryOptions = [
        'Cleave',
        'Graze',
        'Nick',
        'Push',
        'Sap',
        'Slow',
        'Topple',
        'Vex',
    ];

    $masteryDescriptions = [
        'Cleave' =>
            'Se você acertar uma criatura com uma jogada de ataque corpo a corpo usando esta arma, pode realizar uma jogada de ataque corpo a corpo com a arma contra uma segunda criatura a até 5 ft da primeira e que também esteja ao seu alcance. Se acertar, a segunda criatura sofre o dano da arma, mas você não adiciona seu modificador de atributo a esse dano, a menos que o modificador seja negativo. Você só pode realizar esse ataque extra uma vez por turno.',

        'Graze' =>
            'Se sua jogada de ataque com esta arma errar uma criatura, você pode causar dano a ela igual ao modificador do atributo usado para realizar a jogada de ataque. Esse dano é do mesmo tipo causado pela arma e só pode ser aumentado aumentando esse modificador de atributo.',

        'Nick' =>
            'Quando você realiza o ataque extra da propriedade Leve, pode fazê-lo como parte da ação Ataque em vez de usar uma Ação Bônus. Você só pode realizar esse ataque extra uma vez por turno.',

        'Push' =>
            'Se você acertar uma criatura com esta arma, pode empurrá-la até 10 ft em linha reta para longe de você, desde que ela seja Grande ou menor.',

        'Sap' =>
            'Se você acertar uma criatura com esta arma, ela terá Desvantagem na próxima jogada de ataque que fizer antes do início do seu próximo turno.',

        'Slow' =>
            'Se você acertar uma criatura com esta arma e causar dano a ela, pode reduzir o Deslocamento dela em 10 ft até o início do seu próximo turno. Se a criatura for atingida mais de uma vez por armas com esta propriedade, a redução de Deslocamento não excede 10 ft.',

        'Topple' =>
            'Se você acertar uma criatura com esta arma, pode forçá-la a realizar uma salvaguarda de Constituição com CD igual a 8 + o modificador de atributo usado na jogada de ataque + seu Bônus de Proficiência. Se falhar, a criatura fica Caída.',

        'Vex' =>
            'Se você acertar uma criatura com esta arma e causar dano a ela, terá Vantagem na próxima jogada de ataque contra essa criatura antes do fim do seu próximo turno.',
    ];

    $abilities = $character->abilities;
    $overrides = is_array($abilities?->overrides)
        ? $abilities->overrides
        : [];
    $temporaryBonuses = is_array($abilities?->temporary_bonuses)
        ? $abilities->temporary_bonuses
        : [];

    $abilityModifiers = [];

    foreach (array_keys($abilityLabels) as $abilityKey) {
        $base = (int) ($abilities?->{$abilityKey} ?? 10);

        $score = array_key_exists($abilityKey, $overrides)
            && $overrides[$abilityKey] !== null
            && $overrides[$abilityKey] !== ''
                ? (int) $overrides[$abilityKey]
                : $base + (int) ($temporaryBonuses[$abilityKey] ?? 0);

        $abilityModifiers[$abilityKey] =
            (int) floor(($score - 10) / 2);
    }

    $proficiencyBonus =
        (int) ($character->proficiency_bonus ?? 2);

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

    $signed = static fn (int $value): string =>
        $value >= 0 ? '+' . $value : (string) $value;

    $appendModifier = static function (
        string $expression,
        int $modifier
    ) use ($signed): string {
        $expression = trim($expression);

        if ($modifier === 0) {
            return $expression;
        }

        if ($expression === '') {
            return (string) $modifier;
        }

        return $expression . $signed($modifier);
    };

    $buildDamageParts = static function (
        array $rawParts
    ) use (
        $abilityModifiers,
        $damageTypeLabels,
        $appendModifier
    ): array {
        $parts = [];

        foreach ($rawParts as $index => $rawPart) {
            if (!is_array($rawPart)) {
                continue;
            }

            $expression =
                trim((string) ($rawPart['expression'] ?? ''));

            $type =
                trim((string) ($rawPart['type'] ?? ''));

            $abilities = is_array($rawPart['abilities'] ?? null)
                ? array_values(array_unique($rawPart['abilities']))
                : [];

            $bonus =
                (int) ($rawPart['bonus'] ?? 0);

            $modifier = $bonus;

            foreach ($abilities as $abilityKey) {
                $modifier +=
                    (int) ($abilityModifiers[$abilityKey] ?? 0);
            }

            $parts[] = [
                'id' => (string) (
                    $rawPart['id']
                    ?? ('damage-' . $index)
                ),
                'expression' => $expression,
                'type' => $type,
                'type_label' => $damageTypeLabels[$type]
                    ?? ($type !== '' ? ucfirst($type) : 'Sem tipo'),
                'abilities' => $abilities,
                'bonus' => $bonus,
                'modifier' => $modifier,
                'roll_expression' =>
                    $appendModifier($expression, $modifier),
            ];
        }

        return $parts;
    };

    $rows = [];

    /*
    |--------------------------------------------------------------------------
    | Ataques do personagem
    |--------------------------------------------------------------------------
    |
    | O inventário e os ataques são domínios independentes.
    | CharacterItem não cria, altera, oculta nem remove CharacterAttack.
    | Tudo que aparece nesta seção vem exclusivamente de $character->attacks.
    |
    */

    foreach ($character->attacks as $attack) {
        $attackModifier =
            (int) ($attack->attack_bonus ?? 0);

        if (
            $attack->attack_ability
            && array_key_exists(
                $attack->attack_ability,
                $abilityModifiers
            )
        ) {
            $attackModifier +=
                (int) $abilityModifiers[
                    $attack->attack_ability
                ];
        }

        if ($attack->use_proficiency) {
            $attackModifier += $proficiencyBonus;
        }

        $data =
            is_array($attack->data) ? $attack->data : [];

        $rawDamageParts =
            is_array($data['damage_parts'] ?? null)
                ? $data['damage_parts']
                : [];

        if (
            count($rawDamageParts) === 0
            && (
                trim((string) ($attack->damage ?? '')) !== ''
                || trim((string) ($attack->damage_type ?? '')) !== ''
                || (int) ($attack->damage_bonus ?? 0) !== 0
                || count(
                    is_array($attack->damage_abilities)
                        ? $attack->damage_abilities
                        : []
                ) > 0
            )
        ) {
            $rawDamageParts[] = [
                'id' =>
                    'legacy-' . $attack->id . '-damage-0',
                'expression' =>
                    (string) ($attack->damage ?? ''),
                'type' =>
                    (string) ($attack->damage_type ?? ''),
                'abilities' =>
                    is_array($attack->damage_abilities)
                        ? $attack->damage_abilities
                        : [],
                'bonus' =>
                    (int) ($attack->damage_bonus ?? 0),
            ];
        }

        $masteries =
            $data['masteries'] ?? [];

        if (is_string($masteries)) {
            $masteries = [$masteries];
        }

        $masteries = is_array($masteries)
            ? array_values(array_filter(array_map(
                static fn ($value) => trim((string) $value),
                $masteries
            )))
            : [];

        $notes = array_filter([
            trim((string) ($attack->effect ?? '')),
            trim((string) ($attack->description ?? '')),
        ]);

        $rows[] = [
            'key' => 'custom-' . $attack->id,
            'source' => 'custom',
            'source_label' => 'Ataque',
            'id' => $attack->id,
            'editable' => true,
            'name' => $attack->name,
            'effect' => $attack->effect,
            'description' => $attack->description,
            'attack_ability' => $attack->attack_ability,
            'use_proficiency' =>
                (bool) $attack->use_proficiency,
            'attack_bonus' =>
                (int) $attack->attack_bonus,
            'attack_modifier' => $attackModifier,
            'attack_expression' =>
                '1d20' . $signed($attackModifier),
            'damage_parts' =>
                $buildDamageParts($rawDamageParts),
            'range' =>
                trim((string) ($data['range'] ?? '')) ?: null,
            'masteries' => $masteries,
            'notes' => implode(' ', $notes),
            'uses_current' => $attack->uses_current,
            'uses_max' => $attack->uses_max,
            'recovery' => $attack->recovery,
            'counter_mode' =>
                ($data['counter_mode'] ?? 'spend') === 'build'
                    ? 'build'
                    : 'spend',
            'visible' => (bool) $attack->visible,
            'sort_order' => (int) $attack->sort_order,
            'data' => $data,
        ];
    }

    usort(
        $rows,
        static fn (array $a, array $b): int =>
            $a['sort_order'] <=> $b['sort_order']
    );

    /*
     * O editor e os painéis de Ataques usam x-teleport="body".
     * Por isso reaplicamos o tema diretamente nos portais para que
     * continuem consumindo os mesmos tokens visuais da ficha.
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
            .character-attacks-sheet {
                position: relative;
                background: linear-gradient(
                    180deg,
                    rgba(250,248,242,.99),
                    rgba(247,243,234,.99)
                );
            }

            .character-attacks-sheet::before {
                content: "";
                position: absolute;
                inset: 3px;
                pointer-events: none;
                border: 1px solid rgba(216,199,171,.42);
                border-radius: 12px;
            }

            .character-attack-row {
                transition: background .14s ease;
            }

            .character-attack-row:hover {
                background: rgba(239,233,220,.42);
            }

            .character-attack-roll,
            .character-attack-note-button {
                transition:
                    color .14s ease,
                    background .14s ease,
                    transform .14s ease,
                    border-color .14s ease;
            }

            .character-attack-roll:hover,
            .character-attack-note-button:hover {
                background: rgba(239,233,220,.68);
            }

            .character-attack-roll:active {
                transform: scale(.98);
            }

            .character-attack-modal-tab {
                border-bottom: 2px solid transparent;
                transition:
                    color .15s ease,
                    background .15s ease,
                    border-color .15s ease;
            }

            .character-attack-modal-tab.active {
                color: #53150f;
                border-bottom-color: #6b1d14;
                background: rgba(239,233,220,.58);
            }

            .character-attack-modal-card {
                border: 1px solid rgba(216,199,171,.68);
                border-radius: 12px;
                background: linear-gradient(
                    180deg,
                    rgba(255,253,249,.98),
                    rgba(247,243,234,.96)
                );
            }

            .character-attack-mastery-chip {
                border:
                    1px solid
                    rgba(107,29,20,.20);

                background:
                    rgba(107,29,20,.055);

                transition:
                    background .14s ease,
                    border-color .14s ease,
                    transform .14s ease;
            }

            .character-attack-mastery-chip:hover {
                border-color:
                    rgba(107,29,20,.48);

                background:
                    rgba(107,29,20,.11);
            }

            .character-attack-mastery-chip:active {
                transform:
                    scale(.97);
            }

            .character-attack-note-drawer {
                box-shadow:
                    0 24px 54px rgba(43,29,23,.18),
                    0 4px 14px rgba(83,21,15,.10);
            }

            .character-attack-toast {
                box-shadow:
                    0 16px 42px rgba(43,29,23,.18),
                    0 2px 8px rgba(83,21,15,.10);
            }

            /*
            |--------------------------------------------------------------------------
            | V5 — densidade, legibilidade e interação
            |--------------------------------------------------------------------------
            */

            .character-attack-damage-all {
                width: 100%;
                border-radius: 8px;
                transition:
                    background .14s ease,
                    box-shadow .14s ease;
            }

            .character-attack-damage-all:hover {
                background: rgba(239,233,220,.58);
                box-shadow: inset 0 0 0 1px rgba(205,187,159,.42);
            }

            .character-attack-damage-all:disabled {
                cursor: default;
                background: transparent;
                box-shadow: none;
            }

            .character-attack-observation-title {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-attack-observation-text {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 1;
            }

            .character-attack-mastery-strip {
                scrollbar-width: none;
            }

            .character-attack-mastery-strip::-webkit-scrollbar {
                display: none;
            }

            /*
            | O modal continua compacto, mas a tipografia deixa de ser microscópica.
            */

            .character-attack-editor input,
            .character-attack-editor select,
            .character-attack-editor textarea {
                font-size: 13px !important;
                line-height: 1.35 !important;
            }

            .character-attack-editor .character-attack-modal-tab {
                font-size: 11px !important;
                letter-spacing: .08em !important;
            }

            .character-attack-editor .character-attack-modal-card p {
                line-height: 1.45 !important;
            }


            /*
            |--------------------------------------------------------------------------
            | V6 — criação discreta + ataques ocultos
            |--------------------------------------------------------------------------
            */

            .character-attacks-sheet [x-cloak] {
                display: none !important;
            }



            /*
            |--------------------------------------------------------------------------
            | V11 — FOLHA UNIFICADA / ATAQUES MAIS COMPACTOS
            |--------------------------------------------------------------------------
            |
            | A área principal deixa de parecer uma caixa isolada e passa a funcionar
            | como uma seção editorial da folha. A largura é limitada para que Ataques
            | não domine a coluna principal, mantendo a tipografia confortável.
            |
            */

            .character-attacks-sheet {
                width: 100%;
                max-width: 800px;
                margin-inline: auto;
                overflow: hidden;
                border: 0 !important;
                border-radius: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
            }

            .character-attacks-sheet::before {
                display: none !important;
            }

            .character-attacks-v11-header {
                display: flex;
                align-items: center;
                gap: 10px;
                min-height: 42px;
                padding: 2px 4px 9px;
                border-bottom: 1px solid rgba(205,187,159,.38);
            }

            .character-attacks-v11-title {
                display: inline-flex;
                min-width: 0;
                align-items: center;
                gap: 8px;
                border-radius: 8px;
                padding: 4px 6px;
                transition: background .14s ease;
            }

            .character-attacks-v11-title:hover {
                background: rgba(239,233,220,.58);
            }

            .character-attacks-v11-title h2 {
                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
                line-height: 1;
                color: #53150f;
            }

            .character-attacks-v11-count {
                display: inline-flex;
                min-width: 24px;
                height: 23px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: #efe9dc;
                padding: 0 7px;
                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;
                color: #8c6239;
            }

            .character-attacks-v11-add {
                display: inline-flex;
                width: 23px;
                height: 23px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 7px;
                background: rgba(250,248,242,.82);
                font-size: 14px;
                font-weight: 800;
                color: #8c6239;
            }

            .character-attacks-v11-table-wrap {
                overflow-x: auto;
                scrollbar-width: thin;
                scrollbar-color: rgba(140,98,57,.28) transparent;
            }

            .character-attacks-v11-table {
                width: 100%;
                min-width: 650px !important;
                table-layout: fixed;
                border-collapse: collapse;
            }

            .character-attacks-v11-table thead tr {
                border-bottom: 1px solid rgba(205,187,159,.40) !important;
                background: rgba(239,233,220,.24) !important;
            }

            .character-attacks-v11-table th {
                border-right: 0 !important;
                padding-top: 6px !important;
                padding-bottom: 6px !important;
                font-size: 8.5px !important;
                line-height: 1 !important;
                letter-spacing: .10em !important;
                color: #7b5c48 !important;
            }

            .character-attacks-v11-table td {
                border-right: 0 !important;
                padding-top: 7px !important;
                padding-bottom: 7px !important;
            }

            .character-attacks-v11-table .character-attack-row {
                border-bottom: 1px solid rgba(216,199,171,.34) !important;
            }

            .character-attacks-v11-table .character-attack-row:hover {
                background: rgba(239,233,220,.28);
            }

            .character-attacks-v11-table .character-attack-name {
                font-size: 14px !important;
                line-height: 1.15 !important;
            }

            .character-attacks-v11-table .character-attack-range {
                margin-top: 3px !important;
                font-size: 10.5px !important;
                line-height: 1.15 !important;
            }

            .character-attacks-v11-table .character-attack-hit {
                font-size: 15px !important;
                line-height: 1 !important;
            }

            .character-attacks-v11-table .character-attack-damage-expression {
                font-size: 13px !important;
                line-height: 1 !important;
            }

            .character-attacks-v11-table .character-attack-damage-type {
                font-size: 7.5px !important;
                line-height: 1 !important;
                letter-spacing: .045em !important;
            }

            .character-attacks-v11-table .character-attack-observation-title {
                font-size: 10.5px !important;
                line-height: 1.2 !important;
            }

            .character-attacks-v11-table .character-attack-observation-text {
                margin-top: 2px !important;
                font-size: 9.5px !important;
                line-height: 1.25 !important;
            }

            .character-attacks-v11-table .character-attack-mastery-chip {
                font-size: 7px !important;
                line-height: 1 !important;
            }

            .character-attacks-v11-settings-button {
                display: flex;
                width: 26px !important;
                height: 26px !important;
                align-items: center;
                justify-content: center;
                border-radius: 8px !important;
            }

            @media (max-width: 820px) {
                .character-attacks-sheet {
                    max-width: 100%;
                }
            }

        


            /*
            |--------------------------------------------------------------------------
            | V12 — PALETA DO HEADER
            |--------------------------------------------------------------------------
            |
            | A seção externa continua transparente porque pertence ao show.
            | A tabela, por ser conteúdo operacional importante, recebe o
            | mesmo papel quase branco usado nos campos do header.
            |
            */

            .character-attacks-sheet {
                max-width: 820px;
            }

            .character-attacks-v11-header {
                min-height: 40px;

                border-bottom-color:
                    rgba(168,132,91,.32);

                padding:
                    1px 5px 8px;
            }

            .character-attacks-v11-title {
                padding:
                    4px 5px;
            }

            .character-attacks-v11-title:hover {
                background:
                    rgba(255,252,246,.52);
            }

            .character-attacks-v11-count {
                background:
                    #eadbc8;

                color:
                    #7e5735;
            }

            .character-attacks-v11-settings-button {
                border-color:
                    rgba(175,139,96,.48) !important;

                background:
                    #f8f2e8 !important;

                color:
                    #8c6239 !important;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.60);
            }

            .character-attacks-v11-settings-button:hover {
                background:
                    #fffaf2 !important;

                color:
                    #53150f !important;
            }

            /*
            |--------------------------------------------------------------------------
            | TABELA
            |--------------------------------------------------------------------------
            */

            .character-attacks-v11-table-wrap {
                margin-top:
                    2px;

                overflow-x:
                    auto;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius:
                    8px;

                background:
                    #fbf8f1;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.70);
            }

            .character-attacks-v11-table {
                background:
                    #fbf8f1;
            }

            .character-attacks-v11-table thead tr {
                border-bottom-color:
                    rgba(160,119,77,.36) !important;

                background:
                    #eadbc8 !important;
            }

            .character-attacks-v11-table th {
                color:
                    #6f472f !important;
            }

            .character-attacks-v11-table .character-attack-row {
                border-bottom-color:
                    rgba(188,154,111,.28) !important;

                background:
                    rgba(255,253,248,.82);
            }

            .character-attacks-v11-table .character-attack-row:nth-child(even) {
                background:
                    rgba(247,240,230,.72);
            }

            .character-attacks-v11-table .character-attack-row:hover {
                background:
                    #f4e8d8 !important;
            }

            .character-attacks-v11-table td {
                color:
                    #432c21;
            }

            .character-attacks-v11-table .character-attack-range,
            .character-attacks-v11-table .character-attack-observation-text {
                color:
                    #7d604d !important;
            }

            .character-attacks-v11-table .character-attack-damage-type {
                color:
                    #875a3c !important;
            }

            .character-attacks-v11-table .character-attack-mastery-chip {
                border-color:
                    rgba(107,29,20,.18) !important;

                background:
                    rgba(107,29,20,.05) !important;
            }

            /*
            |--------------------------------------------------------------------------
            | INTERAÇÕES
            |--------------------------------------------------------------------------
            */

            .character-attack-roll:hover,
            .character-attack-note-button:hover,
            .character-attack-damage-all:hover {
                background:
                    #f0e1cf !important;
            }

            /*
            |--------------------------------------------------------------------------
            | EDITOR / PAINÉIS
            |--------------------------------------------------------------------------
            */

            .character-attack-editor,
            .character-attack-note-drawer,
            .character-attack-toast {
                border-color:
                    rgba(176,140,98,.62) !important;

                background:
                    #fbf8f1 !important;
            }

            .character-attack-modal-card {
                border-color:
                    rgba(188,154,111,.48) !important;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf8 0%,
                        #f7eee2 100%
                    ) !important;
            }

            .character-attack-modal-tab.active {
                background:
                    #eadbc8 !important;
            }


            /*
            |--------------------------------------------------------------------------
            | V13 — ATAQUES FLUIDOS + 4 LINHAS + THEME CORE
            |--------------------------------------------------------------------------
            |
            | A seção passa a seguir a mesma disciplina visual de Habilidades:
            | - ocupa 100% da largura disponível;
            | - cresce naturalmente até quatro linhas visíveis;
            | - não reserva espaços vazios;
            | - a quinta linha em diante entra em scroll interno;
            | - scrollbar invisível com indicação discreta de continuidade;
            | - todos os painéis e modais consomem os tokens --theme-*.
            |
            */

            .character-sheet-theme[data-theme]
            .character-attacks-sheet {
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
                margin-inline: 0 !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-header,
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table-frame,
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table-wrap,
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table {
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-title h2 {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-header {
                border-bottom-color: var(--theme-divider-strong, var(--theme-border)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-settings-button {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-label) !important;
                box-shadow: var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.08)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-settings-button:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            /*
             * A área cresce conforme os ataques existentes.
             * Não reservamos linhas vazias: 1 ataque = 1 linha, 2 = 2 linhas etc.
             * A partir do 5º, a altura para em quatro linhas e o restante rola.
             */
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table-wrap {
                --attack-head-height: 28px;
                --attack-row-height: 58px;

                height: auto !important;
                min-height: 0 !important;
                max-height: calc(
                    var(--attack-head-height)
                    + (var(--attack-row-height) * 4)
                ) !important;

                overflow-x: hidden !important;
                overflow-y: auto !important;

                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
                box-shadow: var(--theme-module-shadow) !important;

                scrollbar-width: none !important;
                -ms-overflow-style: none !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table-wrap::-webkit-scrollbar {
                width: 0 !important;
                height: 0 !important;
                display: none !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table {
                table-layout: fixed !important;
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table thead {
                position: sticky;
                top: 0;
                z-index: 6;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table thead tr {
                height: var(--attack-head-height) !important;
                border-bottom-color: var(--theme-border) !important;
                background: var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table th {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-row {
                height: var(--attack-row-height) !important;
                border-bottom-color: var(--theme-border-soft) !important;
                background: var(--theme-card-background) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-row:nth-child(even) {
                background: var(--theme-card-background) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-row:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table td {
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-name,
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-hit,
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-damage-expression,
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-observation-title {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-range,
            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-observation-text {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-damage-type {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-mastery-chip {
                border-color: var(--theme-border) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-mastery-chip:hover {
                border-color: var(--theme-accent) !important;
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-roll:hover,
            .character-sheet-theme[data-theme]
            .character-attack-note-button:hover,
            .character-sheet-theme[data-theme]
            .character-attack-damage-all:hover {
                background: var(--theme-card-hover) !important;
            }

            /* Indicador sutil de que existem ataques abaixo. */
            .character-attacks-scroll-hint {
                display: flex;
                width: 20px;
                height: 14px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: color-mix(in srgb, var(--theme-paper-strong) 78%, transparent);
                color: color-mix(in srgb, var(--theme-label) 58%, transparent);
                font-size: 13px;
                font-weight: 900;
                line-height: 1;
                box-shadow: 0 1px 5px rgba(0,0,0,.08);
                backdrop-filter: blur(3px);
            }

            /* ---------------- PORTAIS / PAINÉIS FLUTUANTES ---------------- */

            .character-sheet-theme[data-theme].character-attack-settings-panel,
            .character-sheet-theme[data-theme]
            .character-attack-note-drawer,
            .character-sheet-theme[data-theme]
            .character-attack-toast {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 24px 70px rgba(0,0,0,.28)) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel
            > div:first-child,
            .character-sheet-theme[data-theme]
            .character-attack-note-drawer > div:first-child {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            /* ---------------- MODAL PRINCIPAL ---------------- */

            .character-sheet-theme[data-theme]
            .character-attack-backdrop {
                background:
                    var(--theme-modal-backdrop, rgba(0, 0, 0, .82)) !important;

                -webkit-backdrop-filter:
                    blur(8px) !important;

                backdrop-filter:
                    blur(8px) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.32)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-header,
            .character-sheet-theme[data-theme]
            .character-attack-editor-footer {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor .character-attack-modal-card {
                border-color: var(--theme-border) !important;
                background: var(--theme-card-background) !important;
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor input:not([type="checkbox"]),
            .character-sheet-theme[data-theme]
            .character-attack-editor select,
            .character-sheet-theme[data-theme]
            .character-attack-editor textarea {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor input:not([type="checkbox"]):focus,
            .character-sheet-theme[data-theme]
            .character-attack-editor select:focus,
            .character-sheet-theme[data-theme]
            .character-attack-editor textarea:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-input-focus) 16%, transparent) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor input::placeholder,
            .character-sheet-theme[data-theme]
            .character-attack-editor textarea::placeholder {
                color: var(--theme-input-placeholder) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor input[type="checkbox"] {
                accent-color: var(--theme-accent) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-modal-tab {
                color: var(--theme-text-secondary) !important;
                border-bottom-color: transparent !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-modal-tab:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-modal-tab.active {
                border-bottom-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-primary-action {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
                box-shadow: var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.10)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-primary-action:hover {
                background: var(--theme-button-hover-background) !important;
                box-shadow: 0 4px 12px color-mix(in srgb, var(--theme-accent) 18%, transparent) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-secondary-action {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-secondary-action:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-delete-dialog {
                border-color: color-mix(in srgb, var(--theme-state-damage) 30%, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-text-primary) !important;
                box-shadow: var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.34)) !important;
            }

            @media (max-width: 760px) {
                .character-sheet-theme[data-theme]
                .character-attacks-v11-table-wrap {
                    overflow-x: auto !important;
                }

                .character-sheet-theme[data-theme]
                .character-attacks-v11-table {
                    min-width: 650px !important;
                }
            }



            /*
            |--------------------------------------------------------------------------
            | V14 — POPUP DE ATAQUES OCULTOS / LEGIBILIDADE
            |--------------------------------------------------------------------------
            | O painel continua compacto, mas deixa de usar tipografia microscópica.
            | A superfície segue o tema; o ícone de acesso passa a ser um olho.
            */

            .character-sheet-theme[data-theme].character-attack-settings-panel {
                font-size: 12px;
                line-height: 1.4;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel
            > div:first-child {
                min-height: 62px;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel
            > div:nth-child(2) {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel
            > div:nth-child(2)::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel
            button:focus-visible {
                outline: 2px solid var(--theme-input-focus);
                outline-offset: 2px;
            }


            /*
            |--------------------------------------------------------------------------
            | V15 — GRADE DA TABELA + EDITOR DE ATAQUE REFINADO
            |--------------------------------------------------------------------------
            |
            | A tabela ganha divisores verticais discretos para separar Nome,
            | Acerto, Dano e Observação. O editor usa contraste tipográfico mais
            | forte e deixa a cor do tema como acento, não como cor de todo o texto.
            |
            */

            /* ---------------- TABELA / GRADE ---------------- */

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table th + th {
                border-left:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-border-strong) 72%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table td + td {
                border-left:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-border) 68%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table th {
                padding-inline:
                    11px !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table td {
                padding-inline:
                    11px !important;
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table thead tr {
                box-shadow:
                    inset 0 -1px 0
                    color-mix(
                        in srgb,
                        var(--theme-border-strong) 34%,
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .character-attacks-v11-table .character-attack-row:last-child td {
                border-bottom-color:
                    transparent !important;
            }

            /* ---------------- BACKDROP ---------------- */

            .character-sheet-theme[data-theme]
            .character-attack-backdrop {
                background:
                    var(--theme-modal-backdrop, rgba(0, 0, 0, .82)) !important;

                -webkit-backdrop-filter:
                    blur(8px) !important;

                backdrop-filter:
                    blur(8px) !important;
            }

            /* ---------------- ESTRUTURA DO EDITOR ---------------- */

            .character-sheet-theme[data-theme]
            .character-attack-editor {
                --attack-modal-ink:
                    color-mix(
                        in srgb,
                        var(--theme-text-strong) 88%,
                        #000 12%
                    );

                --attack-modal-copy:
                    color-mix(
                        in srgb,
                        var(--theme-text-primary) 92%,
                        #000 8%
                    );

                --attack-modal-muted:
                    color-mix(
                        in srgb,
                        var(--theme-text-secondary) 90%,
                        #000 10%
                    );

                max-width:
                    820px !important;

                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-border-strong) 82%,
                        transparent
                    ) !important;

                border-radius:
                    18px !important;

                box-shadow:
                    0 34px 100px rgba(0,0,0,.48),
                    0 10px 30px rgba(0,0,0,.24) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-header {
                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;

                box-shadow:
                    inset 0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 58%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-header
            > div:first-child {
                min-height:
                    72px;

                padding:
                    15px 18px 13px !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-kicker {
                margin-bottom:
                    3px;

                font-size:
                    10px !important;

                letter-spacing:
                    .14em !important;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-title {
                font-size:
                    24px !important;

                line-height:
                    1.05 !important;

                color:
                    var(--attack-modal-ink) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-close {
                width:
                    38px !important;

                height:
                    38px !important;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-border) 74%,
                        transparent
                    );

                background:
                    color-mix(
                        in srgb,
                        var(--theme-surface-raised) 88%,
                        transparent
                    ) !important;

                color:
                    var(--attack-modal-ink) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-close:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-body {
                padding:
                    18px !important;

                background:
                    linear-gradient(
                        180deg,
                        color-mix(
                            in srgb,
                            var(--theme-modal-surface, var(--theme-paper-strong)) 97%,
                            var(--theme-paper-alt) 3%
                        ) 0%,
                        color-mix(
                            in srgb,
                            var(--theme-modal-surface, var(--theme-paper-strong)) 91%,
                            var(--theme-paper-alt) 9%
                        ) 100%
                    ) !important;

                scrollbar-width:
                    none;

                -ms-overflow-style:
                    none;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-body::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            /* ---------------- ABAS ---------------- */

            .character-sheet-theme[data-theme]
            .character-attack-modal-tab {
                min-height:
                    44px;

                padding:
                    11px 14px !important;

                font-size:
                    11px !important;

                letter-spacing:
                    .085em !important;

                color:
                    var(--attack-modal-muted) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-modal-tab.active {
                color:
                    var(--attack-modal-ink) !important;

                box-shadow:
                    inset 0 -2px 0
                    var(--theme-accent) !important;
            }

            /* ---------------- CARDS / TIPOGRAFIA ---------------- */

            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card {
                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-border) 86%,
                        transparent
                    ) !important;

                border-radius:
                    14px !important;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    inset 0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 68%,
                        transparent
                    ),
                    0 2px 8px rgba(0,0,0,.025) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card
            [class*="text-[#5f3a27]"],
            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card
            [class*="text-[#6b5548]"] {
                color:
                    var(--attack-modal-ink) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card
            [class*="text-[#8c6239]"] {
                color:
                    var(--attack-modal-muted) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card
            p:not([class*="uppercase"]) {
                font-size:
                    11.5px !important;

                line-height:
                    1.5 !important;

                color:
                    var(--attack-modal-muted) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card
            p[class*="uppercase"],
            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card
            label > span[class*="uppercase"] {
                font-size:
                    10.5px !important;

                line-height:
                    1.2 !important;

                letter-spacing:
                    .095em !important;

                color:
                    var(--attack-modal-ink) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor
            .character-attack-modal-card strong {
                color:
                    var(--attack-modal-ink) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor input:not([type="checkbox"]),
            .character-sheet-theme[data-theme]
            .character-attack-editor select,
            .character-sheet-theme[data-theme]
            .character-attack-editor textarea {
                min-height:
                    41px;

                border-radius:
                    10px !important;

                font-size:
                    13.5px !important;

                color:
                    var(--attack-modal-copy) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor textarea {
                line-height:
                    1.55 !important;
            }

            /* ---------------- RODAPÉ ---------------- */

            .character-sheet-theme[data-theme]
            .character-attack-editor-footer {
                min-height:
                    62px;

                padding:
                    11px 16px !important;

                border-top-color:
                    color-mix(
                        in srgb,
                        var(--theme-border-strong) 70%,
                        transparent
                    ) !important;

                background:
                    var(
                        --theme-modal-footer-background,
                        var(--theme-modal-header-background, var(--theme-band-background))
                    ) !important;

                box-shadow:
                    0 -8px 20px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .character-attack-editor-footer button {
                min-height:
                    38px;

                padding-inline:
                    14px !important;

                font-size:
                    10.5px !important;
            }

            /* ---------------- DARK THEMES ---------------- */

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .character-attack-editor {
                --attack-modal-ink:
                    var(--theme-text-strong);

                --attack-modal-copy:
                    var(--theme-text-primary);

                --attack-modal-muted:
                    var(--theme-text-secondary);
            }

            @media (max-width: 760px) {
                .character-sheet-theme[data-theme]
                .character-attacks-v11-table th,
                .character-sheet-theme[data-theme]
                .character-attacks-v11-table td {
                    padding-inline:
                        9px !important;
                }

                .character-sheet-theme[data-theme]
                .character-attack-editor {
                    border-radius:
                        14px !important;
                }

                .character-sheet-theme[data-theme]
                .character-attack-editor-body {
                    padding:
                        13px !important;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | V16 — PADRÃO GLOBAL DE MODAIS / PAINÉIS FLUTUANTES
            |--------------------------------------------------------------------------
            |
            | Consolida o Attack no contrato atual da ficha:
            | - texto principal neutro e forte;
            | - cor do tema apenas como acento;
            | - superfícies e bordas por tokens --theme-*;
            | - backdrop global;
            | - movimento global dos modais;
            | - popover, gavetas e toast com a mesma hierarquia visual.
            |
            */

            .character-sheet-theme[data-theme].character-attack-settings-panel {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                border-radius: 14px !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow:
                    0 18px 52px rgba(0,0,0,.20),
                    var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.08)) !important;
                transform-origin: top right;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel > div:first-child,
            .character-sheet-theme[data-theme].character-attack-settings-panel > div:last-child {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel > div:nth-child(2) {
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme].character-attack-settings-panel [class*="text-[#6b5548]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel [class*="text-[#8c6239]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel [class*="bg-[#faf8f2]"] {
                background: var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel button[title="Mostrar novamente na ficha"] {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel button[title="Mostrar novamente na ficha"]:hover {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel button[title="Fechar"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel button[title="Fechar"]:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme].character-attack-settings-panel > div:nth-child(2) > div:hover {
                border-color: var(--theme-border) !important;
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 24px 70px rgba(0,0,0,.28)) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer > div:first-child {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer [class*="text-[#53150f]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer [class*="text-[#4f3427]"] {
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer [class*="text-[#8c6239]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer button {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer button:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer section {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-note-drawer > div:nth-child(2) {
                scrollbar-width: thin;
                scrollbar-color: color-mix(in srgb, var(--theme-label) 28%, transparent) transparent;
            }

            .character-sheet-theme[data-theme] .character-attack-toast {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-text-primary) !important;
                box-shadow:
                    0 18px 52px rgba(0,0,0,.18),
                    var(--theme-modal-control-shadow, 0 2px 7px rgba(0,0,0,.08)) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-toast > div > div:first-child {
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-toast [class*="text-[#53150f]"] {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-toast [class*="text-[#5f3a27]"] {
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-toast [class*="text-[#8c6239]"] {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-toast [class*="bg-[#efe9dc]"] {
                background: var(--theme-accent-softer) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-toast [class*="border-[#d8c7ab]"] {
                border-color: var(--theme-divider) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-toast button:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-editor {
                max-height: min(92dvh, 900px) !important;
            }

            @supports not (height: 1dvh) {
                .character-sheet-theme[data-theme] .character-attack-editor {
                    max-height: min(92vh, 900px) !important;
                }
            }

            .character-sheet-theme[data-theme] .character-attack-editor-header,
            .character-sheet-theme[data-theme] .character-attack-editor-footer {
                flex: 0 0 auto;
            }

            .character-sheet-theme[data-theme] .character-attack-editor-body {
                min-height: 0 !important;
                flex: 1 1 auto !important;
            }

            .character-sheet-theme[data-theme] .character-attack-delete-dialog {
                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-state-damage, #b91c1c) 30%,
                        var(--theme-border)
                    ) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-delete-dialog > div:first-child,
            .character-sheet-theme[data-theme] .character-attack-delete-dialog > div:last-child {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-delete-dialog h4 {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-delete-dialog > div:nth-child(2) {
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-delete-dialog > div:last-child button:first-child {
                border: 1px solid var(--theme-border);
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .character-attack-delete-dialog > div:last-child button:first-child:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .character-attack-settings-panel,
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .character-attack-note-drawer,
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .character-attack-toast {
                box-shadow: 0 24px 70px rgba(0,0,0,.42) !important;
            }

            /*
            |--------------------------------------------------------------------------
            | V17 — FEEDBACK DE AÇÕES + HISTÓRICO DE DANO
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme].character-attack-action-toast,
            .character-sheet-theme[data-theme]
            .character-attack-action-toast,
            .character-sheet-theme[data-theme]
            .character-attack-damage-log {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 24px 70px rgba(0,0,0,.28)) !important;
            }

            /*
             * V19 — TOAST TOTALMENTE OPACO / SEM ARO CLARO
             *
             * O toast usa uma superfície sólida do próprio tema. Nada de
             * transparência, glass ou borda luminosa: ele deve parecer um
             * pequeno painel da ficha destacado apenas por profundidade e
             * pelo acento lateral.
             */
            .character-sheet-theme[data-theme].character-attack-action-toast,
            .character-sheet-theme[data-theme]
            .character-attack-action-toast {
                position: relative;
                isolation: isolate;

                opacity: 1 !important;

                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(
                        --theme-toast-surface,
                        var(--theme-surface-raised, var(--theme-paper-strong))
                    ) !important;

                background-color:
                    var(
                        --theme-toast-surface,
                        var(--theme-surface-raised, var(--theme-paper-strong))
                    ) !important;

                background-image:
                    none !important;

                -webkit-backdrop-filter:
                    none !important;

                backdrop-filter:
                    none !important;

                box-shadow:
                    0 18px 42px rgba(0,0,0,.34),
                    0 3px 10px rgba(0,0,0,.16) !important;
            }

            .character-sheet-theme[data-theme].character-attack-action-toast::before,
            .character-sheet-theme[data-theme]
            .character-attack-action-toast::before {
                content: "";
                position: absolute;
                inset: 8px auto 8px 0;
                z-index: 2;
                width: 3px;
                border-radius: 0 999px 999px 0;

                background:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme].character-attack-action-toast::after,
            .character-sheet-theme[data-theme]
            .character-attack-action-toast::after {
                display: none !important;
                content: none !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast-icon {
                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent) 26%,
                        var(--theme-paper-strong) 74%
                    ) !important;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 22%,
                        var(--theme-paper-strong) 78%
                    ) !important;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    none !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast-title {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast-message {
                color:
                    var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast-close {
                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-border-strong) 55%,
                        var(--theme-paper-strong) 45%
                    ) !important;

                background:
                    var(--theme-paper-alt) !important;

                color:
                    var(--theme-text-secondary) !important;

                box-shadow:
                    none !important;

                transition:
                    background .14s ease,
                    color .14s ease,
                    border-color .14s ease,
                    transform .14s ease;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast-close:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-hover) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast-close:active {
                transform:
                    scale(.96);
            }

            /*
             * Temas escuros recebem somente uma sombra um pouco mais profunda.
             * A superfície continua sendo exatamente o paper-strong do tema.
             */
            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            ).character-attack-action-toast,
            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .character-attack-action-toast {
                box-shadow:
                    0 20px 48px rgba(0,0,0,.52),
                    0 4px 12px rgba(0,0,0,.24) !important;
            }

            /*
             * Erro continua sendo estado funcional. A superfície ainda pertence
             * ao tema, enquanto a faixa/ícone usam a cor semântica de dano.
             */
            .character-sheet-theme[data-theme].character-attack-action-toast.is-error,
            .character-sheet-theme[data-theme]
            .character-attack-action-toast.is-error {
                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-state-damage, #991b1b) 24%,
                        var(--theme-paper-strong) 76%
                    ) !important;

                background:
                    var(
                        --theme-toast-surface,
                        var(--theme-surface-raised, var(--theme-paper-strong))
                    ) !important;
            }

            .character-sheet-theme[data-theme].character-attack-action-toast.is-error::before,
            .character-sheet-theme[data-theme]
            .character-attack-action-toast.is-error::before {
                background:
                    var(--theme-state-damage, #991b1b) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast.is-error
            .character-attack-action-toast-icon {
                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-state-damage, #991b1b) 32%,
                        var(--theme-paper-strong) 68%
                    ) !important;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-state-damage, #991b1b) 18%,
                        var(--theme-paper-strong) 82%
                    ) !important;

                color:
                    var(--theme-state-damage, #991b1b) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-action-toast.is-error
            .character-attack-action-toast-title {
                color:
                    var(--theme-state-damage, #991b1b) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-damage-log-header {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-damage-log-entry {
                border-color: var(--theme-border) !important;
                background: var(--theme-card-background) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-damage-log-part {
                border-color: var(--theme-border-soft) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-attack-history-button {
                position: relative;
            }

            .character-sheet-theme[data-theme]
            .character-attack-history-count {
                position: absolute;
                right: -5px;
                top: -5px;
                display: flex;
                min-width: 16px;
                height: 16px;
                align-items: center;
                justify-content: center;
                border: 2px solid var(--theme-paper-strong);
                border-radius: 999px;
                background: var(--theme-accent-strong);
                padding: 0 4px;
                color: var(--theme-button-text);
                font-size: 8px;
                font-weight: 900;
                line-height: 1;
            }

            @media (prefers-reduced-motion: reduce) {
                .character-attack-note-drawer,
                .character-attack-toast {
                    transition-duration: 1ms !important;
                }
            }

        </style>
    @endpush


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data(
                'characterAttacksPanel',
                (
                    initialRows,
                    abilityModifiers,
                    proficiencyBonus,
                    initialExhaustionLevel,
                    abilityLabels,
                    damageTypeLabels,
                    masteryOptions,
                    masteryDescriptions,
                    urls
                ) => ({
                    rows: initialRows,
                    abilityModifiers,
                    proficiencyBonus:
                        parseInt(proficiencyBonus) || 0,

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

                    abilityLabels,
                    damageTypeLabels,
                    masteryOptions,
                    masteryDescriptions,
                    urls,

                    modalOpen: false,
                    activeTab: 'attack',
                    editingId: null,
                    form: null,
                    saving: false,
                    saveError: null,
                    deleteConfirmOpen: false,

                    settingsOpen: false,
                    visibilitySavingId: null,

                    settingsPosition: {
                        top: 72,
                        right: 16,
                    },

                    noteDrawerOpen: false,
                    noteRow: null,

                    masteryDrawerOpen: false,
                    masteryInfo: null,

                    rolling: false,
                    rollToastOpen: false,
                    rollError: null,
                    lastRoll: null,
                    rollToastTimer: null,

                    actionToastOpen: false,
                    actionToastMessage: '',
                    actionToastType: 'success',
                    actionToastTimer: null,

                    damageLogOpen: false,
                    damageLog: [],
                    damageLogSequence: 0,

                    get visibleRows() {
                        return this.rows.filter(
                            row =>
                                row.visible !== false
                        );
                    },

                    get hiddenRows() {
                        return this.rows.filter(
                            row =>
                                row.source === 'custom'
                                &&
                                row.visible === false
                        );
                    },

                    init() {
                        this.loadDamageLog();
                    },

                    showActionToast(
                        message,
                        type = 'success'
                    ) {
                        this.actionToastMessage =
                            String(message || '').trim();

                        if (!this.actionToastMessage) {
                            return;
                        }

                        this.actionToastType =
                            type === 'error'
                                ? 'error'
                                : 'success';

                        this.actionToastOpen =
                            true;

                        clearTimeout(
                            this.actionToastTimer
                        );

                        this.actionToastTimer =
                            setTimeout(
                                () => {
                                    this.actionToastOpen =
                                        false;
                                },
                                3600
                            );
                    },

                    damageLogStorageKey() {
                        return String(
                            this.urls
                                ?.damageLogKey
                            || 'spellbound:damage-log'
                        );
                    },

                    loadDamageLog() {
                        try {
                            const saved =
                                sessionStorage
                                    .getItem(
                                        this.damageLogStorageKey()
                                    );

                            const parsed =
                                saved
                                    ? JSON.parse(saved)
                                    : [];

                            this.damageLog =
                                Array.isArray(parsed)
                                    ? parsed.slice(0, 20)
                                    : [];
                        } catch (error) {
                            this.damageLog = [];
                        }
                    },

                    persistDamageLog() {
                        try {
                            sessionStorage.setItem(
                                this.damageLogStorageKey(),
                                JSON.stringify(
                                    this.damageLog.slice(0, 20)
                                )
                            );
                        } catch (error) {
                            // O histórico continua funcionando em memória.
                        }
                    },

                    recordDamageRoll(
                        row,
                        parts,
                        total
                    ) {
                        const normalizedParts =
                            (Array.isArray(parts) ? parts : [])
                                .map(part => ({
                                    expression:
                                        String(
                                            part?.expression
                                            ?? ''
                                        ),

                                    type_label:
                                        String(
                                            part?.type_label
                                            || 'Dano'
                                        ),

                                    total:
                                        parseInt(
                                            part?.total
                                        ) || 0,

                                    formatted:
                                        String(
                                            part?.formatted
                                            ?? ''
                                        ),
                                }));

                        if (normalizedParts.length === 0) {
                            return;
                        }

                        const now = new Date();

                        this.damageLogSequence += 1;

                        this.damageLog.unshift({
                            id:
                                `${now.getTime()}-${this.damageLogSequence}`,

                            attack_name:
                                row?.name
                                || 'Ataque',

                            time:
                                now.toLocaleTimeString(
                                    'pt-BR',
                                    {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    }
                                ),

                            total:
                                parseInt(total) || 0,

                            parts:
                                normalizedParts,
                        });

                        if (this.damageLog.length > 20) {
                            this.damageLog =
                                this.damageLog.slice(0, 20);
                        }

                        this.persistDamageLog();
                    },

                    openDamageLog() {
                        this.closeSettings();
                        this.closeNote();
                        this.closeMastery();
                        this.damageLogOpen = true;
                    },

                    closeDamageLog() {
                        this.damageLogOpen = false;
                    },

                    clearDamageLog() {
                        this.damageLog = [];
                        this.persistDamageLog();
                    },

                    positionSettings() {
                        const button =
                            this.$refs
                                .settingsButton;

                        if (!button) {
                            return;
                        }

                        const panel =
                            this.$refs
                                .settingsPanel;

                        const rect =
                            button
                                .getBoundingClientRect();

                        const viewportWidth =
                            window.innerWidth;

                        const viewportHeight =
                            window.innerHeight;

                        const margin =
                            16;

                        const gap =
                            18;

                        const panelWidth =
                            panel?.offsetWidth
                            || 340;

                        const fallbackHeight =
                            Math.min(
                                430,
                                126
                                +
                                (
                                    Math.min(
                                        this.hiddenRows.length,
                                        4
                                    )
                                    *
                                    62
                                )
                            );

                        const panelHeight =
                            panel?.offsetHeight
                            || fallbackHeight;

                        const right =
                            Math.max(
                                margin,
                                viewportWidth
                                -
                                rect.right
                            );

                        let top =
                            rect.bottom
                            +
                            gap;

                        const maxTop =
                            Math.max(
                                margin,
                                viewportHeight
                                -
                                panelHeight
                                -
                                margin
                            );

                        /*
                         * Mantemos o painel preferencialmente abaixo do botão.
                         * Só o trazemos para cima o mínimo necessário para ele
                         * continuar inteiro dentro da viewport.
                         */
                        top =
                            Math.min(
                                top,
                                maxTop
                            );

                        this.settingsPosition = {
                            top,
                            right:
                                Math.min(
                                    right,
                                    Math.max(
                                        margin,
                                        viewportWidth
                                        -
                                        panelWidth
                                        -
                                        margin
                                    )
                                ),
                        };
                    },

                    toggleSettings() {
                        if (
                            this.settingsOpen
                        ) {
                            this.settingsOpen =
                                false;

                            return;
                        }

                        this.settingsOpen =
                            true;

                        this.$nextTick(() => {
                            this.positionSettings();
                        });
                    },

                    closeSettings() {
                        this.settingsOpen =
                            false;
                    },

                    get settingsStyle() {
                        return `
                            top: ${this.settingsPosition.top}px;
                            right: ${this.settingsPosition.right}px;
                        `;
                    },

                    signed(value) {
                        const n =
                            parseInt(value) || 0;

                        return n >= 0
                            ? `+${n}`
                            : `${n}`;
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


                    attackExpressionWithExhaustion(
                        row
                    ) {
                        const baseModifier =
                            parseInt(
                                row?.attack_modifier
                            ) || 0;

                        const effectiveModifier =
                            baseModifier
                            -
                            this.exhaustionRollPenalty;

                        return (
                            '1d20'
                            +
                            (
                                effectiveModifier >= 0
                                    ? `+${effectiveModifier}`
                                    : `${effectiveModifier}`
                            )
                        );
                    },

                    modifierFor(key) {
                        return parseInt(
                            this.abilityModifiers[key] ?? 0
                        ) || 0;
                    },

                    appendModifier(
                        expression,
                        modifier
                    ) {
                        const base =
                            String(
                                expression ?? ''
                            ).trim();

                        const bonus =
                            parseInt(modifier) || 0;

                        if (bonus === 0) {
                            return base;
                        }

                        if (!base) {
                            return `${bonus}`;
                        }

                        return (
                            base
                            +
                            this.signed(bonus)
                        );
                    },

                    damageTypeLabel(type) {
                        if (!type) {
                            return 'Sem tipo';
                        }

                        return (
                            this.damageTypeLabels[type]
                            ?? type
                        );
                    },

                    recoveryLabel(value) {
                        return {
                            none:
                                'Sem recuperação automática',
                            short_rest:
                                'Descanso curto',
                            long_rest:
                                'Descanso longo',
                        }[value] ?? '—';
                    },

                    counterModeLabel(mode) {
                        return mode === 'build'
                            ? 'Contagem'
                            : 'Usos';
                    },

                    newPartId() {
                        return (
                            `damage-${Date.now()}-`
                            +
                            Math.random()
                                .toString(36)
                                .slice(2, 8)
                        );
                    },

                    blankDamagePart() {
                        return {
                            id: this.newPartId(),
                            expression: '',
                            type: '',
                            abilities: [],
                            bonus: 0,
                        };
                    },

                    damagePartModifier(part) {
                        let total =
                            parseInt(
                                part?.bonus
                            ) || 0;

                        (
                            part?.abilities ?? []
                        ).forEach(
                            ability => {
                                total +=
                                    this.modifierFor(
                                        ability
                                    );
                            }
                        );

                        return total;
                    },

                    buildDamagePart(part) {
                        const modifier =
                            this.damagePartModifier(
                                part
                            );

                        return {
                            id:
                                part?.id
                                ?? this.newPartId(),

                            expression:
                                String(
                                    part?.expression
                                    ?? ''
                                ),

                            type:
                                part?.type ?? '',

                            type_label:
                                this.damageTypeLabel(
                                    part?.type
                                ),

                            abilities:
                                Array.isArray(
                                    part?.abilities
                                )
                                    ? [
                                        ...part.abilities,
                                    ]
                                    : [],

                            bonus:
                                parseInt(
                                    part?.bonus
                                ) || 0,

                            modifier,

                            roll_expression:
                                this.appendModifier(
                                    part?.expression
                                    ?? '',
                                    modifier
                                ),
                        };
                    },

                    customAttackModifier(raw) {
                        let total =
                            parseInt(
                                raw?.attack_bonus
                            ) || 0;

                        if (
                            raw?.attack_ability
                        ) {
                            total +=
                                this.modifierFor(
                                    raw.attack_ability
                                );
                        }

                        if (
                            raw?.use_proficiency
                        ) {
                            total +=
                                this.proficiencyBonus;
                        }

                        return total;
                    },

                    buildCustomRow(raw) {
                        const data =
                            raw?.data ?? {};

                        let damageParts =
                            Array.isArray(
                                data?.damage_parts
                            )
                                ? data.damage_parts
                                : [];

                        if (
                            damageParts.length === 0
                            &&
                            (
                                raw?.damage
                                ||
                                raw?.damage_type
                                ||
                                parseInt(
                                    raw?.damage_bonus
                                )
                                ||
                                (
                                    raw
                                        ?.damage_abilities
                                        ?.length
                                    ?? 0
                                )
                            )
                        ) {
                            damageParts = [
                                {
                                    id:
                                        this.newPartId(),

                                    expression:
                                        raw?.damage
                                        ?? '',

                                    type:
                                        raw?.damage_type
                                        ?? '',

                                    abilities:
                                        Array.isArray(
                                            raw
                                                ?.damage_abilities
                                        )
                                            ? raw
                                                .damage_abilities
                                            : [],

                                    bonus:
                                        parseInt(
                                            raw?.damage_bonus
                                        ) || 0,
                                },
                            ];
                        }

                        const attackModifier =
                            this.customAttackModifier(
                                raw
                            );

                        return {
                            key:
                                `custom-${raw.id}`,

                            source:
                                'custom',

                            source_label:
                                'Personalizado',

                            id:
                                raw.id,

                            editable:
                                true,

                            name:
                                raw.name,

                            effect:
                                raw.effect ?? null,

                            description:
                                raw.description
                                ?? null,

                            attack_ability:
                                raw.attack_ability
                                ?? null,

                            use_proficiency:
                                !!raw.use_proficiency,

                            attack_bonus:
                                parseInt(
                                    raw.attack_bonus
                                ) || 0,

                            attack_modifier:
                                attackModifier,

                            attack_expression:
                                `1d20${this.signed(
                                    attackModifier
                                )}`,

                            damage_parts:
                                damageParts.map(
                                    part =>
                                        this.buildDamagePart(
                                            part
                                        )
                                ),

                            range:
                                data?.range ?? null,

                            masteries:
                                Array.isArray(
                                    data?.masteries
                                )
                                    ? data.masteries
                                    : [],

                            notes:
                                [
                                    raw?.effect,
                                    raw?.description,
                                ]
                                    .filter(Boolean)
                                    .join(' '),

                            uses_current:
                                raw.uses_current ===
                                    null
                                    ? null
                                    : parseInt(
                                        raw.uses_current
                                    ) || 0,

                            uses_max:
                                raw.uses_max ===
                                    null
                                    ? null
                                    : parseInt(
                                        raw.uses_max
                                    ) || 0,

                            recovery:
                                raw.recovery
                                ?? null,

                            counter_mode:
                                data
                                    ?.counter_mode ===
                                    'build'
                                    ? 'build'
                                    : 'spend',

                            visible:
                                raw.visible !==
                                    false,

                            sort_order:
                                parseInt(
                                    raw.sort_order
                                ) || 0,

                            data,
                        };
                    },

                    blankForm() {
                        return {
                            name: '',
                            effect: '',
                            description: '',
                            attack_ability:
                                'strength',
                            use_proficiency:
                                true,
                            attack_bonus:
                                0,
                            range:
                                '',
                            masteries:
                                [],
                            damage_parts: [
                                this.blankDamagePart(),
                            ],
                            counter_enabled:
                                false,
                            counter_mode:
                                'spend',
                            uses_current:
                                null,
                            uses_max:
                                null,
                            recovery:
                                'none',
                            visible:
                                true,
                            sort_order:
                                0,
                            data:
                                {},
                        };
                    },

                    openCreate() {
                        this.editingId = null;
                        this.form =
                            this.blankForm();
                        this.activeTab =
                            'attack';
                        this.saveError = null;
                        this.deleteConfirmOpen =
                            false;
                        this.modalOpen = true;
                    },

                    openEdit(row) {
                        if (
                            row.source !==
                                'custom'
                        ) {
                            return;
                        }

                        this.editingId =
                            row.id;

                        this.form = {
                            name:
                                row.name ?? '',

                            effect:
                                row.effect ?? '',

                            description:
                                row.description
                                ?? '',

                            attack_ability:
                                row.attack_ability
                                ?? null,

                            use_proficiency:
                                !!row
                                    .use_proficiency,

                            attack_bonus:
                                parseInt(
                                    row.attack_bonus
                                ) || 0,

                            range:
                                row.range ?? '',

                            masteries:
                                Array.isArray(
                                    row.masteries
                                )
                                    ? [
                                        ...row.masteries,
                                    ]
                                    : [],

                            damage_parts:
                                Array.isArray(
                                    row.damage_parts
                                )
                                &&
                                row
                                    .damage_parts
                                    .length > 0
                                    ? row
                                        .damage_parts
                                        .map(
                                            part => ({
                                                id:
                                                    part.id
                                                    ??
                                                    this
                                                        .newPartId(),

                                                expression:
                                                    part
                                                        .expression
                                                    ?? '',

                                                type:
                                                    part.type
                                                    ?? '',

                                                abilities:
                                                    Array.isArray(
                                                        part
                                                            .abilities
                                                    )
                                                        ? [
                                                            ...part
                                                                .abilities,
                                                        ]
                                                        : [],

                                                bonus:
                                                    parseInt(
                                                        part
                                                            .bonus
                                                    ) || 0,
                                            })
                                        )
                                    : [
                                        this
                                            .blankDamagePart(),
                                    ],

                            counter_enabled:
                                row.uses_max !==
                                    null,

                            counter_mode:
                                row
                                    .counter_mode ===
                                    'build'
                                    ? 'build'
                                    : 'spend',

                            uses_current:
                                row.uses_current,

                            uses_max:
                                row.uses_max,

                            recovery:
                                row.recovery
                                ?? 'none',

                            visible:
                                row.visible !==
                                    false,

                            sort_order:
                                parseInt(
                                    row.sort_order
                                ) || 0,

                            data:
                                row.data ?? {},
                        };

                        this.activeTab =
                            'attack';
                        this.saveError =
                            null;
                        this.deleteConfirmOpen =
                            false;
                        this.modalOpen =
                            true;
                    },

                    closeModal(force = false) {
                        if (this.saving && !force) {
                            return;
                        }

                        this.modalOpen =
                            false;
                        this.activeTab =
                            'attack';
                        this.editingId =
                            null;
                        this.form =
                            null;
                        this.saveError =
                            null;
                        this.deleteConfirmOpen =
                            false;
                    },

                    setTab(tab) {
                        this.activeTab = tab;
                        this.saveError = null;
                    },

                    addDamagePart() {
                        this.form
                            .damage_parts
                            .push(
                                this.blankDamagePart()
                            );
                    },

                    removeDamagePart(index) {
                        if (
                            this.form
                                .damage_parts
                                .length <= 1
                        ) {
                            this.form
                                .damage_parts[0] =
                                this.blankDamagePart();
                            return;
                        }

                        this.form
                            .damage_parts
                            .splice(
                                index,
                                1
                            );
                    },

                    toggleDamageAbility(
                        part,
                        key
                    ) {
                        const list =
                            Array.isArray(
                                part.abilities
                            )
                                ? part.abilities
                                : [];

                        part.abilities =
                            list.includes(key)
                                ? list.filter(
                                    value =>
                                        value !== key
                                )
                                : [...list, key];
                    },

                    toggleMastery(mastery) {
                        const list =
                            Array.isArray(
                                this.form
                                    .masteries
                            )
                                ? this.form
                                    .masteries
                                : [];

                        this.form.masteries =
                            list.includes(mastery)
                                ? list.filter(
                                    value =>
                                        value !== mastery
                                )
                                : [
                                    ...list,
                                    mastery,
                                ];
                    },

                    setCounterMode(mode) {
                        this.form.counter_mode =
                            mode;

                        const maximum =
                            Math.max(
                                1,
                                parseInt(
                                    this.form.uses_max
                                ) || 1
                            );

                        this.form.uses_max =
                            maximum;

                        this.form.uses_current =
                            mode === 'build'
                                ? 0
                                : maximum;
                    },

                    get formAttackModifier() {
                        return this
                            .customAttackModifier(
                                this.form
                            );
                    },

                    get formAttackExpression() {
                        return (
                            '1d20'
                            +
                            this.signed(
                                this
                                    .formAttackModifier
                            )
                        );
                    },

                    previewDamagePart(part) {
                        return this
                            .buildDamagePart(part);
                    },

                    payload() {
                        const counter =
                            !!this.form
                                ?.counter_enabled;

                        let maximum =
                            counter
                                ? parseInt(
                                    this.form
                                        ?.uses_max
                                )
                                : null;

                        if (
                            counter
                            &&
                            (
                                !maximum
                                ||
                                maximum < 1
                            )
                        ) {
                            maximum = 1;
                        }

                        let current =
                            counter
                                ? parseInt(
                                    this.form
                                        ?.uses_current
                                )
                                : null;

                        if (
                            counter
                            &&
                            Number.isNaN(
                                current
                            )
                        ) {
                            current =
                                this.form
                                    ?.counter_mode ===
                                    'build'
                                    ? 0
                                    : maximum;
                        }

                        const damageParts =
                            (
                                this.form
                                    ?.damage_parts
                                ?? []
                            )
                                .map(
                                    part => ({
                                        id:
                                            part.id
                                            ??
                                            this
                                                .newPartId(),

                                        expression:
                                            String(
                                                part
                                                    .expression
                                                ?? ''
                                            ).trim(),

                                        type:
                                            part.type
                                            || null,

                                        abilities:
                                            Array.isArray(
                                                part
                                                    .abilities
                                            )
                                                ? part
                                                    .abilities
                                                : [],

                                        bonus:
                                            parseInt(
                                                part.bonus
                                            ) || 0,
                                    })
                                )
                                .filter(
                                    part =>
                                        part.expression
                                        ||
                                        part.type
                                        ||
                                        part
                                            .abilities
                                            .length > 0
                                        ||
                                        part.bonus !==
                                            0
                                );

                        const first =
                            damageParts[0]
                            ?? null;

                        return {
                            name:
                                String(
                                    this.form
                                        ?.name
                                    ?? ''
                                ).trim(),

                            effect:
                                String(
                                    this.form
                                        ?.effect
                                    ?? ''
                                ).trim()
                                || null,

                            description:
                                String(
                                    this.form
                                        ?.description
                                    ?? ''
                                ).trim()
                                || null,

                            attack_ability:
                                this.form
                                    ?.attack_ability
                                || null,

                            use_proficiency:
                                !!this.form
                                    ?.use_proficiency,

                            attack_bonus:
                                parseInt(
                                    this.form
                                        ?.attack_bonus
                                ) || 0,

                            /*
                            | Compatibilidade com
                            | os campos antigos.
                            */

                            damage:
                                first
                                    ?.expression
                                || null,

                            damage_type:
                                first?.type
                                || null,

                            damage_abilities:
                                first
                                    ?.abilities
                                ?? [],

                            damage_bonus:
                                parseInt(
                                    first?.bonus
                                ) || 0,

                            uses_current:
                                counter
                                    ? Math.max(
                                        0,
                                        Math.min(
                                            maximum,
                                            current
                                        )
                                    )
                                    : null,

                            uses_max:
                                counter
                                    ? maximum
                                    : null,

                            recovery:
                                counter
                                    ? (
                                        this.form
                                            ?.recovery
                                        || 'none'
                                    )
                                    : null,

                            visible:
                                !!this.form
                                    ?.visible,

                            sort_order:
                                Math.max(
                                    0,
                                    parseInt(
                                        this.form
                                            ?.sort_order
                                    ) || 0
                                ),

                            data: {
                                ...(
                                    this.form
                                        ?.data
                                    ?? {}
                                ),

                                range:
                                    String(
                                        this.form
                                            ?.range
                                        ?? ''
                                    ).trim()
                                    || null,

                                masteries:
                                    Array.isArray(
                                        this.form
                                            ?.masteries
                                    )
                                        ? this.form
                                            .masteries
                                        : [],

                                counter_mode:
                                    this.form
                                        ?.counter_mode ===
                                        'build'
                                        ? 'build'
                                        : 'spend',

                                damage_parts:
                                    damageParts,
                            },
                        };
                    },

                    baseHeaders() {
                        return {
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
                                ?? '{{ csrf_token() }}',

                            'X-Requested-With':
                                'XMLHttpRequest',
                        };
                    },

                    jsonHeaders() {
                        return {
                            ...this.baseHeaders(),

                            'Content-Type':
                                'application/json',
                        };
                    },

                    async saveAttack() {
                        if (
                            this.saving
                            ||
                            !this.form
                        ) {
                            return;
                        }

                        const payload =
                            this.payload();

                        if (!payload.name) {
                            this.saveError =
                                'Informe um nome para o ataque.';
                            return;
                        }

                        this.saving = true;
                        this.saveError = null;

                        const editing =
                            this.editingId !==
                                null;

                        const url =
                            editing
                                ? this.urls
                                    .update
                                    .replace(
                                        '__ATTACK__',
                                        this.editingId
                                    )
                                : this.urls
                                    .store;

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
                                            this.jsonHeaders(),

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

                            if (
                                !response.ok
                            ) {
                                const errors =
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
                                    errors.length
                                        ? errors.join(
                                            ' '
                                        )
                                        : (
                                            data?.message
                                            ??
                                            'Não foi possível salvar o ataque.'
                                        )
                                );
                            }

                            const row =
                                this.buildCustomRow(
                                    data.attack
                                );

                            if (editing) {
                                const index =
                                    this.rows
                                        .findIndex(
                                            item =>
                                                item
                                                    .source ===
                                                    'custom'
                                                &&
                                                parseInt(
                                                    item.id
                                                ) ===
                                                    parseInt(
                                                        row.id
                                                    )
                                        );

                                if (index >= 0) {
                                    this.rows[
                                        index
                                    ] =
                                        row;
                                }
                            } else {
                                this.rows.push(
                                    row
                                );
                            }

                            this.rows.sort(
                                (a, b) =>
                                    (
                                        parseInt(
                                            a.sort_order
                                        ) || 0
                                    )
                                    -
                                    (
                                        parseInt(
                                            b.sort_order
                                        ) || 0
                                    )
                            );

                            this.closeModal(true);

                            this.showActionToast(
                                editing
                                    ? 'Ataque atualizado.'
                                    : 'Ataque criado.'
                            );

                        } catch (error) {
                            this.saveError =
                                error.message
                                ??
                                'Não foi possível salvar o ataque.';
                        } finally {
                            this.saving =
                                false;
                        }
                    },

                    async deleteAttack() {
                        if (
                            !this.editingId
                            ||
                            this.saving
                        ) {
                            return;
                        }

                        this.saving = true;
                        this.saveError = null;

                        try {
                            const response =
                                await fetch(
                                    this.urls
                                        .destroy
                                        .replace(
                                            '__ATTACK__',
                                            this.editingId
                                        ),
                                    {
                                        method:
                                            'DELETE',

                                        headers:
                                            this.baseHeaders(),
                                    }
                                );

                            const data =
                                await response
                                    .json()
                                    .catch(
                                        () => ({})
                                    );

                            if (
                                !response.ok
                            ) {
                                throw new Error(
                                    data?.message
                                    ??
                                    'Não foi possível excluir o ataque.'
                                );
                            }

                            const id =
                                parseInt(
                                    this.editingId
                                );

                            this.rows =
                                this.rows.filter(
                                    row =>
                                        !(
                                            row.source ===
                                                'custom'
                                            &&
                                            parseInt(
                                                row.id
                                            ) === id
                                        )
                                );

                            this.closeModal(true);

                            this.showActionToast(
                                'Ataque excluído.'
                            );

                        } catch (error) {
                            this.saveError =
                                error.message
                                ??
                                'Não foi possível excluir o ataque.';
                        } finally {
                            this.saving =
                                false;
                        }
                    },

                    async changeUses(
                        row,
                        delta
                    ) {
                        if (
                            row.source !==
                                'custom'
                            ||
                            row.uses_max ===
                                null
                        ) {
                            return;
                        }

                        const maximum =
                            Math.max(
                                0,
                                parseInt(
                                    row.uses_max
                                ) || 0
                            );

                        const current =
                            Math.max(
                                0,
                                parseInt(
                                    row.uses_current
                                ) || 0
                            );

                        const next =
                            Math.max(
                                0,
                                Math.min(
                                    maximum,
                                    current
                                    +
                                    parseInt(
                                        delta
                                    )
                                )
                            );

                        if (
                            next === current
                        ) {
                            return;
                        }

                        try {
                            const response =
                                await fetch(
                                    this.urls
                                        .uses
                                        .replace(
                                            '__ATTACK__',
                                            row.id
                                        ),
                                    {
                                        method:
                                            'PATCH',

                                        headers:
                                            this.jsonHeaders(),

                                        body:
                                            JSON.stringify({
                                                uses_current:
                                                    next,
                                            }),
                                    }
                                );

                            const data =
                                await response
                                    .json()
                                    .catch(
                                        () => ({})
                                    );

                            if (
                                !response.ok
                            ) {
                                throw new Error(
                                    data?.message
                                    ??
                                    'Não foi possível atualizar o rastreador.'
                                );
                            }

                            row.uses_current =
                                parseInt(
                                    data
                                        .uses_current
                                ) || 0;

                        } catch (error) {
                            this.showRollError(
                                error.message
                                ??
                                'Não foi possível atualizar o rastreador.'
                            );
                        }
                    },

                    rowPayload(
                        row,
                        visible
                    ) {
                        const damageParts =
                            Array.isArray(
                                row.damage_parts
                            )
                                ? row.damage_parts.map(
                                    part => ({
                                        id:
                                            part.id
                                            ?? this.newPartId(),

                                        expression:
                                            String(
                                                part.expression
                                                ?? ''
                                            ).trim(),

                                        type:
                                            part.type
                                            || null,

                                        abilities:
                                            Array.isArray(
                                                part.abilities
                                            )
                                                ? part.abilities
                                                : [],

                                        bonus:
                                            parseInt(
                                                part.bonus
                                            ) || 0,
                                    })
                                )
                                : [];

                        const first =
                            damageParts[0]
                            ?? null;

                        return {
                            name:
                                String(
                                    row.name
                                    ?? ''
                                ).trim(),

                            effect:
                                String(
                                    row.effect
                                    ?? ''
                                ).trim()
                                || null,

                            description:
                                String(
                                    row.description
                                    ?? ''
                                ).trim()
                                || null,

                            attack_ability:
                                row.attack_ability
                                || null,

                            use_proficiency:
                                !!row.use_proficiency,

                            attack_bonus:
                                parseInt(
                                    row.attack_bonus
                                ) || 0,

                            damage:
                                first?.expression
                                || null,

                            damage_type:
                                first?.type
                                || null,

                            damage_abilities:
                                first?.abilities
                                ?? [],

                            damage_bonus:
                                parseInt(
                                    first?.bonus
                                ) || 0,

                            uses_current:
                                row.uses_current ===
                                    null
                                    ? null
                                    : Math.max(
                                        0,
                                        parseInt(
                                            row.uses_current
                                        ) || 0
                                    ),

                            uses_max:
                                row.uses_max ===
                                    null
                                    ? null
                                    : Math.max(
                                        1,
                                        parseInt(
                                            row.uses_max
                                        ) || 1
                                    ),

                            recovery:
                                row.uses_max ===
                                    null
                                    ? null
                                    : (
                                        row.recovery
                                        || 'none'
                                    ),

                            visible:
                                !!visible,

                            sort_order:
                                Math.max(
                                    0,
                                    parseInt(
                                        row.sort_order
                                    ) || 0
                                ),

                            data: {
                                ...(
                                    row.data
                                    ?? {}
                                ),

                                range:
                                    row.range
                                    || null,

                                masteries:
                                    Array.isArray(
                                        row.masteries
                                    )
                                        ? row.masteries
                                        : [],

                                counter_mode:
                                    row.counter_mode ===
                                        'build'
                                        ? 'build'
                                        : 'spend',

                                damage_parts:
                                    damageParts,
                            },
                        };
                    },

                    async setAttackVisibility(
                        row,
                        visible
                    ) {
                        if (
                            row.source !==
                                'custom'
                            ||
                            this.visibilitySavingId !==
                                null
                        ) {
                            return;
                        }

                        this.visibilitySavingId =
                            row.id;

                        try {
                            const response =
                                await fetch(
                                    this.urls
                                        .update
                                        .replace(
                                            '__ATTACK__',
                                            row.id
                                        ),
                                    {
                                        method:
                                            'PATCH',

                                        headers:
                                            this.jsonHeaders(),

                                        body:
                                            JSON.stringify(
                                                this.rowPayload(
                                                    row,
                                                    visible
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

                            if (
                                !response.ok
                            ) {
                                const errors =
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
                                    errors.length
                                        ? errors.join(
                                            ' '
                                        )
                                        : (
                                            data?.message
                                            ??
                                            'Não foi possível alterar a visibilidade do ataque.'
                                        )
                                );
                            }

                            const updatedRow =
                                this.buildCustomRow(
                                    data.attack
                                );

                            const index =
                                this.rows.findIndex(
                                    item =>
                                        item.source ===
                                            'custom'
                                        &&
                                        parseInt(
                                            item.id
                                        ) ===
                                            parseInt(
                                                updatedRow.id
                                            )
                                );

                            if (
                                index >= 0
                            ) {
                                this.rows[index] =
                                    updatedRow;
                            }

                        } catch (error) {
                            this.showRollError(
                                error.message
                                ??
                                'Não foi possível alterar a visibilidade do ataque.'
                            );

                        } finally {
                            this.visibilitySavingId =
                                null;
                        }
                    },

                    openNote(row) {
                        this.masteryDrawerOpen =
                            false;

                        this.masteryInfo =
                            null;

                        this.noteRow = row;
                        this.noteDrawerOpen =
                            true;
                    },

                    closeNote() {
                        this.noteDrawerOpen =
                            false;

                        setTimeout(
                            () => {
                                if (
                                    !this
                                        .noteDrawerOpen
                                ) {
                                    this.noteRow =
                                        null;
                                }
                            },
                            180
                        );
                    },

                    openMastery(mastery) {
                        this.noteDrawerOpen =
                            false;

                        this.noteRow =
                            null;

                        this.masteryInfo = {
                            name:
                                mastery,

                            description:
                                this
                                    .masteryDescriptions[
                                        mastery
                                    ]
                                ?? 'Descrição não disponível.',
                        };

                        this.masteryDrawerOpen =
                            true;
                    },

                    openMasteryGroup(row) {
                        this.noteDrawerOpen =
                            false;

                        this.noteRow =
                            null;

                        const masteries =
                            Array.isArray(
                                row?.masteries
                            )
                                ? row.masteries
                                : [];

                        this.masteryInfo = {
                            name:
                                'Maestrias',

                            description:
                                null,

                            items:
                                masteries.map(
                                    mastery => ({
                                        name:
                                            mastery,

                                        description:
                                            this
                                                .masteryDescriptions[
                                                    mastery
                                                ]
                                            ?? 'Descrição não disponível.',
                                    })
                                ),
                        };

                        this.masteryDrawerOpen =
                            true;
                    },


                    closeMastery() {
                        this.masteryDrawerOpen =
                            false;

                        setTimeout(
                            () => {
                                if (
                                    !this
                                        .masteryDrawerOpen
                                ) {
                                    this.masteryInfo =
                                        null;
                                }
                            },
                            180
                        );
                    },

                    canRoll(expression) {
                        return /\d+d\d+/i.test(
                            String(
                                expression
                                ?? ''
                            )
                        );
                    },

                    isStaticDamage(expression) {
                        return /^[+-]?\d+$/.test(
                            String(
                                expression
                                ?? ''
                            ).trim()
                        );
                    },

                    canResolveDamage(expression) {
                        const value =
                            String(
                                expression
                                ?? ''
                            ).trim();

                        return (
                            value !== ''
                            &&
                            (
                                this.canRoll(
                                    value
                                )
                                ||
                                this.isStaticDamage(
                                    value
                                )
                            )
                        );
                    },

                    extractRollTotal(data) {
                        const candidate =
                            data?.data?.total
                            ??
                            data?.data?.result
                            ??
                            data?.data?.value
                            ??
                            data?.data?.sum
                            ??
                            data?.total
                            ??
                            data?.result
                            ??
                            null;

                        if (
                            candidate ===
                                null
                        ) {
                            return null;
                        }

                        const parsed =
                            parseInt(
                                candidate
                            );

                        return Number.isNaN(
                            parsed
                        )
                            ? null
                            : parsed;
                    },

                    async resolveExpression(
                        expression
                    ) {
                        const value =
                            String(
                                expression
                                ?? ''
                            ).trim();

                        if (
                            this.isStaticDamage(
                                value
                            )
                        ) {
                            return {
                                total:
                                    parseInt(
                                        value
                                    ) || 0,

                                formatted:
                                    value,
                            };
                        }

                        if (
                            !this.canRoll(
                                value
                            )
                        ) {
                            throw new Error(
                                'Expressão de dano inválida.'
                            );
                        }

                        const response =
                            await fetch(
                                this.urls.roll,
                                {
                                    method:
                                        'POST',

                                    headers:
                                        this
                                            .jsonHeaders(),

                                    body:
                                        JSON.stringify({
                                            expression:
                                                value,
                                        }),
                                }
                            );

                        const data =
                            await response
                                .json()
                                .catch(
                                    () => ({})
                                );

                        if (
                            !response.ok
                        ) {
                            throw new Error(
                                data?.message
                                ??
                                'Não foi possível rolar.'
                            );
                        }

                        const total =
                            this
                                .extractRollTotal(
                                    data
                                );

                        if (
                            total ===
                                null
                        ) {
                            throw new Error(
                                'A rolagem não retornou um total válido.'
                            );
                        }

                        return {
                            total,

                            formatted:
                                data?.formatted
                                ??
                                value,
                        };
                    },

                    scheduleRollToastClose() {
                        clearTimeout(
                            this
                                .rollToastTimer
                        );

                        this.rollToastTimer =
                            setTimeout(
                                () => {
                                    this
                                        .rollToastOpen =
                                        false;
                                },
                                6500
                            );
                    },

                    async roll(
                        label,
                        expression
                    ) {
                        if (
                            this.rolling
                            ||
                            !this.canRoll(
                                expression
                            )
                        ) {
                            return;
                        }

                        this.rolling =
                            true;

                        this.rollError =
                            null;

                        try {
                            const result =
                                await this
                                    .resolveExpression(
                                        expression
                                    );

                            this.lastRoll = {
                                label,
                                expression,

                                exhaustionPenalty:
                                    this.exhaustionRollPenalty,

                                total:
                                    result.total,

                                formatted:
                                    result
                                        .formatted,

                                parts:
                                    null,
                            };

                            this.rollToastOpen =
                                true;

                            this
                                .scheduleRollToastClose();

                        } catch (error) {
                            this.showRollError(
                                error.message
                                ??
                                'Não foi possível rolar.'
                            );

                        } finally {
                            this.rolling =
                                false;
                        }
                    },

                    async rollDamagePart(
                        row,
                        part
                    ) {
                        if (
                            this.rolling
                            ||
                            !this
                                .canResolveDamage(
                                    part
                                        ?.roll_expression
                                )
                        ) {
                            return;
                        }

                        this.rolling =
                            true;

                        this.rollError =
                            null;

                        try {
                            const result =
                                await this
                                    .resolveExpression(
                                        part
                                            .roll_expression
                                    );

                            this.lastRoll = {
                                label:
                                    row.name
                                    +
                                    ' — '
                                    +
                                    (
                                        part
                                            .type_label
                                        || 'Dano'
                                    ),

                                expression:
                                    part
                                        .roll_expression,

                                exhaustionPenalty:
                                    0,

                                total:
                                    result.total,

                                formatted:
                                    result
                                        .formatted,

                                parts:
                                    null,
                            };

                            this.recordDamageRoll(
                                row,
                                [
                                    {
                                        expression:
                                            part.roll_expression,
                                        type_label:
                                            part.type_label
                                            || 'Dano',
                                        total:
                                            result.total,
                                        formatted:
                                            result.formatted,
                                    },
                                ],
                                result.total
                            );

                            this.rollToastOpen =
                                true;

                            this
                                .scheduleRollToastClose();

                        } catch (error) {
                            this.showRollError(
                                error.message
                                ??
                                'Não foi possível calcular o dano.'
                            );

                        } finally {
                            this.rolling =
                                false;
                        }
                    },

                    async rollAllDamage(row) {
                        if (
                            this.rolling
                        ) {
                            return;
                        }

                        const parts =
                            (
                                row
                                    ?.damage_parts
                                ?? []
                            ).filter(
                                part =>
                                    this
                                        .canResolveDamage(
                                            part
                                                ?.roll_expression
                                        )
                            );

                        if (
                            parts.length ===
                                0
                        ) {
                            return;
                        }

                        this.rolling =
                            true;

                        this.rollError =
                            null;

                        try {
                            const resolved =
                                await Promise.all(
                                    parts.map(
                                        async part => {
                                            const result =
                                                await this
                                                    .resolveExpression(
                                                        part
                                                            .roll_expression
                                                    );

                                            return {
                                                expression:
                                                    part
                                                        .roll_expression,

                                                type_label:
                                                    part
                                                        .type_label
                                                    || 'Dano',

                                                total:
                                                    result
                                                        .total,

                                                formatted:
                                                    result
                                                        .formatted,
                                            };
                                        }
                                    )
                                );

                            const total =
                                resolved.reduce(
                                    (
                                        sum,
                                        part
                                    ) =>
                                        sum
                                        +
                                        (
                                            parseInt(
                                                part
                                                    .total
                                            ) || 0
                                        ),
                                    0
                                );

                            this.lastRoll = {
                                label:
                                    row.name
                                    +
                                    ' — Dano Total',

                                expression:
                                    null,

                                exhaustionPenalty:
                                    0,

                                total,

                                formatted:
                                    null,

                                parts:
                                    resolved,
                            };

                            this.recordDamageRoll(
                                row,
                                resolved,
                                total
                            );

                            this.rollToastOpen =
                                true;

                            this
                                .scheduleRollToastClose();

                        } catch (error) {
                            this.showRollError(
                                error.message
                                ??
                                'Não foi possível rolar todos os danos.'
                            );

                        } finally {
                            this.rolling =
                                false;
                        }
                    },

                    showRollError(message) {
                        this.lastRoll = null;
                        this.rollError =
                            message;
                        this.rollToastOpen =
                            true;

                        clearTimeout(
                            this.rollToastTimer
                        );

                        this.rollToastTimer =
                            setTimeout(
                                () => {
                                    this.rollToastOpen =
                                        false;
                                },
                                4500
                            );
                    },
                })
            );
        });
    </script>
@endonce


<section
    x-data="characterAttacksPanel(
        @js($rows),
        @js($abilityModifiers),
        {{ $proficiencyBonus }},
        {{ $initialExhaustionLevel }},
        @js($abilityLabels),
        @js($damageTypeLabels),
        @js($masteryOptions),
        @js($masteryDescriptions),
        {
            store: @js(route(
                'characters.attacks.store',
                ['character' => $character]
            )),

            update: @js(route(
                'characters.attacks.update',
                [
                    'character' => $character,
                    'attack' => '__ATTACK__',
                ]
            )),

            destroy: @js(route(
                'characters.attacks.destroy',
                [
                    'character' => $character,
                    'attack' => '__ATTACK__',
                ]
            )),

            uses: @js(route(
                'characters.attacks.uses.update',
                [
                    'character' => $character,
                    'attack' => '__ATTACK__',
                ]
            )),

            roll: @js(url('/api/roll')),

            damageLogKey: @js(
                'spellbound:character:'
                . $character->id
                . ':attack-damage-log'
            ),
        }
    )"

    @keydown.escape.window="
        if (settingsOpen) {
            closeSettings();
        } else if (damageLogOpen) {
            closeDamageLog();
        } else if (deleteConfirmOpen) {
            deleteConfirmOpen = false;
        } else if (noteDrawerOpen) {
            closeNote();
        } else if (masteryDrawerOpen) {
            closeMastery();
        } else {
            closeModal();
        }
    "

    @character-exhaustion-updated.window="
        syncExhaustion(
            $event.detail
        )
    "

    @resize.window="
        if (settingsOpen) {
            positionSettings();
        }
    "

    @scroll.window="
        if (settingsOpen) {
            positionSettings();
        }
    "

    data-theme="{{ $characterTheme }}"
    class="character-attacks-sheet character-sheet-theme relative"
>

    {{-- =========================================================
         Cabeçalho
    ========================================================== --}}

    <div class="character-attacks-v11-header relative z-20">
        {{-- O título continua abrindo a criação, mas agora integra a seção à folha. --}}
        <button
            type="button"
            @click="openCreate()"
            class="character-attacks-v11-title"
            title="Criar novo ataque"
        >
            <h2>Ataques</h2>



        </button>



        {{-- Histórico de dano + configuração / ataques ocultos --}}

        <div class="ml-auto flex items-center gap-1.5">
            <button
                type="button"
                @click.stop="openDamageLog()"
                class="character-attacks-v11-settings-button character-attack-history-button flex items-center justify-center rounded-lg border border-[#cdbb9f]/70 bg-[#f4f1e8] text-[#8c6239] transition hover:bg-[#efe9dc] hover:text-[#53150f]"
                title="Histórico de dano desta sessão"
                aria-label="Histórico de dano"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M3 12a9 9 0 1 0 3-6.7L3 8"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M3 4v4h4M12 7v5l3 2"
                    />
                </svg>

                <span
                    x-show="damageLog.length > 0"
                    x-cloak
                    class="character-attack-history-count"
                    x-text="damageLog.length > 99 ? '99+' : damageLog.length"
                ></span>
            </button>

            <div class="relative">
            <button
                x-ref="settingsButton"

                type="button"

                @click.stop="
                    toggleSettings()
                "

                class="
                    character-attacks-v11-settings-button
                    flex
                    items-center
                    justify-center

                    rounded-lg

                    border
                    border-[#cdbb9f]/70

                    bg-[#f4f1e8]

                    text-[#8c6239]

                    transition

                    hover:bg-[#efe9dc]
                    hover:text-[#53150f]
                "

                :class="{
                    'bg-[#efe9dc] text-[#53150f]':
                        settingsOpen
                }"

                title="Ver ataques ocultos"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                    />

                    <circle
                        cx="12"
                        cy="12"
                        r="2.5"
                        stroke-width="1.7"
                    />
                </svg>
            </button>
            </div>
        </div>


        {{-- 
            O painel é teleportado para <body>.
            Assim ele não é cortado pelo overflow-hidden da ficha.
        --}}

        <template x-teleport="body">
            <div
                x-show="settingsOpen"

                x-ref="settingsPanel"

                x-cloak

                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                @click.outside="
                    closeSettings()
                "

                :style="
                    settingsStyle
                "

                data-theme="{{ $characterTheme }}"

                class="
                    character-sheet-theme
                    character-attack-settings-panel
                    fixed
                    z-[250]

                    w-[340px]
                    max-w-[calc(100vw-32px)]

                    overflow-hidden

                    rounded-xl

                    border
                    border-[#cdbb9f]

                    bg-[#faf8f2]

                    shadow-[0_18px_45px_rgba(43,29,23,.20),0_3px_12px_rgba(83,21,15,.10)]
                "
            >
                <div
                    class="
                        border-b
                        border-[#d8c7ab]/55

                        bg-[#efe9dc]/55

                        px-4
                        py-3.5
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
                        <div>
                            <p
                                class="
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.13em]
                                    text-[#8c6239]
                                "
                            >
                                Configurações
                            </p>

                            <h3
                                class="
                                    mt-0.5

                                    font-serif
                                    text-[18px]
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Ataques ocultos
                            </h3>
                        </div>

                        <div
                            class="
                                flex
                                items-center
                                gap-1.5
                            "
                        >
                            <span
                                class="
                                    rounded-md

                                    border
                                    border-[#cdbb9f]/55

                                    bg-[#faf8f2]

                                    px-2.5
                                    py-1.5

                                    text-[10px]
                                    font-black
                                    text-[#8c6239]
                                "

                                x-text="
                                    hiddenRows.length
                                "
                            ></span>

                            <button
                                type="button"

                                @click="
                                    closeSettings()
                                "

                                class="
                                    flex
                                    h-7
                                    w-7
                                    items-center
                                    justify-center

                                    rounded-md

                                    text-[16px]
                                    text-[#8c6239]

                                    transition

                                    hover:bg-[#e8dfd1]
                                    hover:text-[#53150f]
                                "

                                title="Fechar"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                </div>


                <div
                    class="
                        max-h-[min(18rem,calc(100vh-160px))]
                        overflow-y-auto

                        p-3
                    "
                >
                    <template
                        x-if="
                            hiddenRows.length ===
                                0
                        "
                    >
                        <div
                            class="
                                rounded-lg

                                px-3
                                py-4

                                text-center
                            "
                        >
                            <p
                                class="
                                    text-[12px]
                                    font-bold
                                    text-[#6b5548]
                                "
                            >
                                Nenhum ataque oculto.
                            </p>

                            <p
                                class="
                                    mt-1

                                    text-[10px]
                                    leading-relaxed
                                    text-[#8c6239]/70
                                "
                            >
                                Quando você esconder um ataque,
                                ele continuará salvo e aparecerá aqui.
                            </p>
                        </div>
                    </template>


                    <template
                        x-for="
                            row in hiddenRows
                        "

                        :key="
                            'hidden-' +
                            row.key
                        "
                    >
                        <div
                            class="
                                flex
                                items-center
                                gap-2

                                rounded-lg

                                border
                                border-transparent

                                px-2.5
                                py-2.5

                                transition

                                hover:border-[#d8c7ab]/55
                                hover:bg-[#efe9dc]/45
                            "
                        >
                            <button
                                type="button"

                                @click="
                                    closeSettings();
                                    openEdit(row);
                                "

                                class="
                                    min-w-0
                                    flex-1
                                    text-left
                                "

                                title="Editar ataque oculto"
                            >
                                <span
                                    class="
                                        block
                                        truncate

                                        font-serif
                                        text-[14px]
                                        font-black
                                        text-[#53150f]
                                    "

                                    x-text="
                                        row.name
                                    "
                                ></span>

                                <span
                                    class="
                                        mt-0.5
                                        block
                                        truncate

                                        text-[10px]
                                        font-medium
                                        leading-snug
                                        text-[#8c6239]/75
                                    "

                                    x-text="
                                        row.effect
                                        ||
                                        row.description
                                        ||
                                        'Ataque oculto'
                                    "
                                ></span>
                            </button>


                            <button
                                type="button"

                                @click.stop="
                                    setAttackVisibility(
                                        row,
                                        true
                                    )
                                "

                                :disabled="
                                    visibilitySavingId !==
                                        null
                                "

                                class="
                                    flex
                                    h-9
                                    shrink-0
                                    items-center
                                    gap-1.5

                                    rounded-lg

                                    border
                                    border-[#cdbb9f]/65

                                    bg-[#faf8f2]

                                    px-3

                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]
                                    text-[#6b1d14]

                                    transition

                                    hover:bg-[#efe9dc]

                                    disabled:opacity-45
                                "

                                title="Mostrar novamente na ficha"
                            >
                                <svg
                                    class="
                                        h-3.5
                                        w-3.5
                                    "

                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.7"
                                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.5"
                                        stroke-width="1.7"
                                    />
                                </svg>

                                <span
                                    x-text="
                                        parseInt(
                                            visibilitySavingId
                                        ) ===
                                        parseInt(
                                            row.id
                                        )
                                            ? 'Salvando'
                                            : 'Mostrar'
                                    "
                                ></span>
                            </button>
                        </div>
                    </template>
                </div>


                <div
                    class="
                        border-t
                        border-[#d8c7ab]/55

                        bg-[#efe9dc]/35

                        px-4
                        py-2.5
                    "
                >
                    <p
                        class="
                            text-[9px]
                            leading-relaxed
                            text-[#8c6239]/65
                        "
                    >
                        Para criar um ataque novo,
                        clique no título “Ataques” da ficha.
                    </p>
                </div>
            </div>
        </template>
    </div>



    {{-- =========================================================
         Tabela compacta — V5
    ========================================================== --}}

    <div
        class="character-attacks-v11-table-frame relative z-10"
        x-data="{
            attackCanScrollDown: false,

            updateAttackScrollState() {
                const el = this.$refs.attackScroll;

                if (!el) {
                    this.attackCanScrollDown = false;
                    return;
                }

                this.attackCanScrollDown =
                    el.scrollHeight > el.clientHeight + 2
                    && (
                        el.scrollTop + el.clientHeight
                        < el.scrollHeight - 2
                    );
            },
        }"
        x-init="$nextTick(() => updateAttackScrollState())"
        x-effect="
            visibleRows.length;
            $nextTick(() => updateAttackScrollState());
        "
        @resize.window="updateAttackScrollState()"
    >
        <div
            x-ref="attackScroll"
            @scroll="updateAttackScrollState()"
            class="character-attacks-v11-table-wrap"
        >
        <table class="character-attacks-v11-table">
            <thead>
                <tr class="border-b border-[#cdbb9f]/70 bg-[#efe9dc]/42">
                    <th class="w-[27%] px-2.5 py-1 text-left text-[7px] font-black uppercase tracking-[0.14em] text-[#53150f]">
                        Nome
                    </th>

                    <th class="w-[10%] px-1.5 py-1 text-center text-[7px] font-black uppercase tracking-[0.14em] text-[#53150f]">
                        Acerto
                    </th>

                    <th class="w-[38%] px-2.5 py-1 text-left text-[7px] font-black uppercase tracking-[0.14em] text-[#53150f]">
                        Dano
                    </th>

                    <th class="w-[25%] px-2.5 py-1 text-left text-[7px] font-black uppercase tracking-[0.14em] text-[#53150f]">
                        Observação
                    </th>
                </tr>
            </thead>

            <tbody>
                <template x-for="row in visibleRows" :key="row.key">
                    <tr class="character-attack-row border-b border-[#d8c7ab]/42 last:border-b-0">

                        {{-- Nome + maestrias + alcance --}}

                        <td class="px-2.5 py-1 align-middle">
                            <div class="min-w-0">
                                <div class="flex min-w-0 items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="row.editable && openEdit(row)"
                                        class="min-w-0 shrink text-left"
                                        :class="row.editable ? 'cursor-pointer' : 'cursor-default'"
                                    >
                                        <span
                                            class="character-attack-name block truncate font-serif text-[13px] font-black leading-tight text-[#53150f]"
                                            :class="row.editable ? 'hover:underline' : ''"
                                            x-text="row.name"
                                        ></span>
                                    </button>

                                    <div
                                        x-show="row.masteries && row.masteries.length"
                                        x-cloak
                                        class="character-attack-mastery-strip flex min-w-0 flex-1 items-center gap-1 overflow-hidden"
                                    >
                                        <template
                                            x-for="mastery in (row.masteries ?? []).slice(0, 2)"
                                            :key="mastery"
                                        >
                                            <button
                                                type="button"
                                                @click.stop="openMastery(mastery)"
                                                class="character-attack-mastery-chip shrink-0 rounded px-1.5 py-0.5 text-[6px] font-black uppercase tracking-[0.06em] text-[#6b1d14]"
                                                :title="'Ver maestria ' + mastery"
                                                x-text="mastery"
                                            ></button>
                                        </template>

                                        <button
                                            x-show="(row.masteries?.length ?? 0) > 2"
                                            x-cloak
                                            type="button"
                                            @click.stop="openMasteryGroup(row)"
                                            class="shrink-0 rounded border border-[#cdbb9f]/55 bg-[#faf8f2] px-1.5 py-0.5 text-[6px] font-black text-[#8c6239]"
                                            :title="'Ver todas as ' + row.masteries.length + ' maestrias'"
                                            x-text="'+' + (row.masteries.length - 2)"
                                        ></button>
                                    </div>
                                </div>

                                <div
                                    x-show="row.range"
                                    x-cloak
                                    class="character-attack-range mt-0.5 flex items-center gap-1 text-[10px] font-bold leading-none text-[#8c6239]"
                                >
                                    <span>→</span>
                                    <span x-text="row.range"></span>
                                </div>
                            </div>
                        </td>


                        {{-- Acerto --}}

                        <td class="px-1 py-1 text-center align-middle">
                            <button
                                type="button"
                                @click="
                                    roll(
                                        row.name + ' — Acerto',
                                        attackExpressionWithExhaustion(
                                            row
                                        )
                                    )
                                "
                                class="character-attack-hit character-attack-roll inline-flex w-full items-center justify-center rounded-md px-1 py-1 font-serif text-[16px] font-black text-[#6b1d14]"
                                :title="
                                    'Rolar '
                                    +
                                    attackExpressionWithExhaustion(
                                        row
                                    )
                                "
                                x-text="
                                    signed(
                                        row.attack_modifier
                                        - exhaustionRollPenalty
                                    )
                                "
                            ></button>
                        </td>


                        {{-- Dano: uma única área clicável para todos os dados --}}

                        <td class="px-1.5 py-1 align-middle">
                            <button
                                type="button"
                                @click="rollAllDamage(row)"
                                :disabled="
                                    rolling
                                    || !row.damage_parts
                                    || row.damage_parts.length === 0
                                "
                                class="character-attack-damage-all block min-h-[32px] px-2 py-1 text-left"
                                :title="
                                    row.damage_parts && row.damage_parts.length
                                        ? 'Rolar todo o dano de ' + row.name
                                        : ''
                                "
                            >
                                <span class="flex flex-wrap items-baseline gap-x-1.5 gap-y-0.5">
                                    <template
                                        x-for="(part, partIndex) in row.damage_parts"
                                        :key="part.id ?? partIndex"
                                    >
                                        <span class="inline-flex items-baseline gap-1">
                                            <span
                                                x-show="partIndex > 0"
                                                class="mr-0.5 text-[9px] font-black text-[#b29161]"
                                            >
                                                +
                                            </span>

                                            <strong
                                                class="character-attack-damage-expression whitespace-nowrap font-serif text-[13px] leading-none text-[#53150f]"
                                                x-text="part.roll_expression || '—'"
                                            ></strong>

                                            <span
                                                class="character-attack-damage-type whitespace-nowrap text-[6.5px] font-black uppercase tracking-[0.055em] text-[#8c6239]"
                                                x-text="part.type_label"
                                            ></span>
                                        </span>
                                    </template>

                                    <span
                                        x-show="!row.damage_parts || row.damage_parts.length === 0"
                                        class="text-[9px] font-bold text-[#8c6239]/65"
                                    >
                                        —
                                    </span>
                                </span>

                      
                            </button>
                        </td>


                        {{-- Observação com título/texto separados + tracker --}}

                        <td class="px-1.5 py-1 align-middle">
                            <div class="flex items-center gap-1.5">
                                <button
                                    type="button"
                                    @click="openNote(row)"
                                    class="character-attack-note-button min-w-0 flex-1 rounded-md border border-transparent px-1.5 py-1 text-left"
                                    title="Abrir observação completa"
                                >
                                    <strong
                                        class="character-attack-observation-title text-[10px] font-black leading-tight text-[#53150f]"
                                        x-text="row.effect || (row.description ? 'Observação' : 'Sem observações')"
                                    ></strong>

                                    <span
                                        class="character-attack-observation-text mt-0.5 text-[8.5px] font-medium leading-[1.3] text-[#6b5548]"
                                        x-text="
                                            row.description
                                            || (
                                                row.effect
                                                    ? 'Clique para ler os detalhes.'
                                                    : 'Nenhuma informação adicional.'
                                            )
                                        "
                                    ></span>
                                </button>

                                <div
                                    x-show="row.uses_max !== null"
                                    x-cloak
                                    class="flex shrink-0 items-center gap-1 rounded-lg border border-[#d8c7ab]/55 bg-[#f4f1e8]/75 px-1 py-1"
                                >
                                    <button
                                        type="button"
                                        @click="changeUses(row, -1)"
                                        :disabled="
                                            row.source !== 'custom'
                                            || parseInt(row.uses_current) <= 0
                                        "
                                        class="flex h-5 w-5 items-center justify-center rounded border border-[#cdbb9f]/65 bg-[#faf8f2] text-[10px] font-black text-[#6b1d14] hover:bg-[#efe9dc] disabled:opacity-30"
                                        title="Diminuir"
                                    >
                                        −
                                    </button>

                                    <div class="min-w-[29px] text-center leading-none">
                                        <strong class="font-serif text-[10px] text-[#53150f]">
                                            <span x-text="row.uses_current"></span>/<span x-text="row.uses_max"></span>
                                        </strong>

                                        <span
                                            x-show="row.counter_mode === 'build'"
                                            x-cloak
                                            class="mt-0.5 block text-[5px] font-black uppercase tracking-[0.07em] text-[#8c6239]"
                                        >
                                            Cont.
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        @click="changeUses(row, 1)"
                                        :disabled="
                                            row.source !== 'custom'
                                            || parseInt(row.uses_current) >= parseInt(row.uses_max)
                                        "
                                        class="flex h-5 w-5 items-center justify-center rounded border border-[#cdbb9f]/65 text-[10px] font-black transition disabled:opacity-30"
                                        :class="
                                            row.counter_mode === 'build'
                                                ? 'bg-[#6b1d14] text-[#f4f1e8] hover:bg-[#53150f]'
                                                : 'bg-[#faf8f2] text-[#6b1d14] hover:bg-[#efe9dc]'
                                        "
                                        title="Aumentar"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>

                <tr x-show="visibleRows.length === 0" x-cloak>
                    <td
                        colspan="4"
                        class="px-4 py-5 text-center text-[10px] font-bold text-[#8c6239]/65"
                    >
                        Nenhum ataque visível.
                    </td>
                </tr>
            </tbody>
        </table>
        </div>

        <div
            x-show="attackCanScrollDown"
            x-cloak
            class="character-attacks-scroll-hint pointer-events-none absolute bottom-1.5 right-2 z-20"
            aria-hidden="true"
        >
            ⌄
        </div>
    </div>


    {{-- =========================================================
         Gaveta de observação — somente título + texto
    ========================================================== --}}

    <template x-teleport="body">
        <div
            x-show="noteDrawerOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme pointer-events-none fixed inset-0 z-[185]"
        >
            <aside
                x-show="noteDrawerOpen"
                x-transition:enter="transform transition ease-out duration-200"
                x-transition:enter-start="translate-x-[105%] opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transform transition ease-in duration-150"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-[105%] opacity-0"
                @click.outside="closeNote()"
                class="character-theme-modal character-attack-note-drawer pointer-events-auto absolute right-4 top-24 w-[min(92vw,400px)] overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#faf8f2]"
            >
                <div class="character-theme-modal-header flex items-start justify-between gap-3 border-b border-[#d8c7ab]/60 bg-[#efe9dc]/72 px-4 py-3.5">
                    <h3
                        class="character-theme-modal-title min-w-0 flex-1 font-serif text-[20px] font-black leading-tight text-[#53150f]"
                        x-text="noteRow?.effect || 'Observação'"
                    ></h3>

                    <button
                        type="button"
                        @click="closeNote()"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-lg text-[#8c6239] hover:bg-[#e8dfd1] hover:text-[#53150f]"
                    >
                        ×
                    </button>
                </div>

                <div class="max-h-[58vh] overflow-y-auto px-4 py-4">
                    <p
                        class="character-theme-modal-copy whitespace-pre-line text-[13px] font-medium leading-[1.7] text-[#4f3427]"
                        x-text="
                            noteRow?.description
                            || (
                                noteRow?.effect
                                    ? 'Sem texto adicional.'
                                    : noteRow?.notes || 'Sem observações.'
                            )
                        "
                    ></p>
                </div>
            </aside>
        </div>
    </template>


    {{-- =========================================================
         Gaveta de maestrias
    ========================================================== --}}

    <template x-teleport="body">
        <div
            x-show="masteryDrawerOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme pointer-events-none fixed inset-0 z-[186]"
        >
            <aside
                x-show="masteryDrawerOpen"
                x-transition:enter="transform transition ease-out duration-200"
                x-transition:enter-start="translate-x-[105%] opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transform transition ease-in duration-150"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-[105%] opacity-0"
                @click.outside="closeMastery()"
                class="character-theme-modal character-attack-note-drawer pointer-events-auto absolute right-4 top-24 w-[min(92vw,430px)] overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#faf8f2]"
            >
                <div class="character-theme-modal-header flex items-start justify-between gap-3 border-b border-[#d8c7ab]/60 bg-[#efe9dc]/72 px-4 py-3">
                    <div class="min-w-0">
                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#8c6239]">
                            Maestria de Arma
                        </p>

                        <h3
                            class="character-theme-modal-title mt-0.5 truncate font-serif text-[20px] font-black text-[#53150f]"
                            x-text="masteryInfo?.name ?? ''"
                        ></h3>
                    </div>

                    <button
                        type="button"
                        @click="closeMastery()"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-lg text-[#8c6239] hover:bg-[#e8dfd1] hover:text-[#53150f]"
                    >
                        ×
                    </button>
                </div>

                <div class="max-h-[62vh] overflow-y-auto px-4 py-4">
                    <p
                        x-show="!masteryInfo?.items"
                        class="character-theme-modal-copy whitespace-pre-line text-[13px] font-medium leading-[1.7] text-[#4f3427]"
                        x-text="masteryInfo?.description ?? ''"
                    ></p>

                    <div
                        x-show="masteryInfo?.items"
                        x-cloak
                        class="space-y-3"
                    >
                        <template
                            x-for="item in (masteryInfo?.items ?? [])"
                            :key="item.name"
                        >
                            <section class="rounded-xl border border-[#d8c7ab]/55 bg-[#fffdf9] p-3">
                                <h4
                                    class="font-serif text-[15px] font-black text-[#53150f]"
                                    x-text="item.name"
                                ></h4>

                                <p
                                    class="mt-1.5 text-[12px] font-medium leading-[1.65] text-[#4f3427]"
                                    x-text="item.description"
                                ></p>
                            </section>
                        </template>
                    </div>
                </div>
            </aside>
        </div>
    </template>


    {{-- =========================================================
         Histórico de dano — sessão atual
    ========================================================== --}}

    <template x-teleport="body">
        <div
            x-show="damageLogOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme pointer-events-none fixed inset-0 z-[240]"
        >
            <aside
                x-show="damageLogOpen"
                x-transition:enter="transform transition ease-out duration-200"
                x-transition:enter-start="translate-x-[105%] opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transform transition ease-in duration-150"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-[105%] opacity-0"
                @click.outside="closeDamageLog()"
                class="character-theme-modal character-attack-damage-log pointer-events-auto absolute right-4 top-20 flex max-h-[min(74vh,680px)] w-[min(94vw,460px)] flex-col overflow-hidden rounded-2xl border"
            >
                <div class="character-attack-damage-log-header flex shrink-0 items-start justify-between gap-3 border-b px-4 py-3.5">
                    <div class="min-w-0">
                        <p class="text-[8px] font-black uppercase tracking-[0.14em]" style="color: var(--theme-label);">
                            Combate
                        </p>
                        <div class="mt-0.5 flex items-baseline gap-2">
                            <h3 class="font-serif text-[20px] font-black" style="color: var(--theme-text-strong);">
                                Histórico de dano
                            </h3>
                            <span
                                class="rounded-full px-2 py-0.5 text-[8px] font-black"
                                style="background: var(--theme-accent-softer); color: var(--theme-label);"
                                x-text="damageLog.length"
                            ></span>
                        </div>
                        <p class="mt-1 text-[9px]" style="color: var(--theme-text-secondary);">
                            Mantém as últimas 20 rolagens de dano desta sessão.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="closeDamageLog()"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-lg"
                        style="color: var(--theme-text-secondary);"
                        aria-label="Fechar histórico"
                    >
                        ×
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto p-3.5">
                    <div
                        x-show="damageLog.length === 0"
                        x-cloak
                        class="rounded-xl border border-dashed px-4 py-10 text-center"
                        style="border-color: var(--theme-border); background: var(--theme-surface-card-alt);"
                    >
                        <p class="font-serif text-[16px] font-black" style="color: var(--theme-text-strong);">
                            Nenhum dano rolado ainda
                        </p>
                        <p class="mx-auto mt-1 max-w-xs text-[10px] leading-5" style="color: var(--theme-text-secondary);">
                            Quando você rolar o dano de um ataque, cada tipo e seu resultado ficarão registrados aqui.
                        </p>
                    </div>

                    <div x-show="damageLog.length > 0" x-cloak class="space-y-2.5">
                        <template x-for="entry in damageLog" :key="entry.id">
                            <article class="character-attack-damage-log-entry rounded-xl border px-3.5 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <strong
                                            class="block truncate font-serif text-[14px] font-black"
                                            style="color: var(--theme-text-strong);"
                                            x-text="entry.attack_name"
                                        ></strong>
                                        <span
                                            class="mt-0.5 block text-[8px] font-bold uppercase tracking-[0.08em]"
                                            style="color: var(--theme-text-secondary);"
                                            x-text="entry.time"
                                        ></span>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <strong
                                            class="block font-serif text-[21px] font-black leading-none"
                                            style="color: var(--theme-accent-strong);"
                                            x-text="entry.total"
                                        ></strong>
                                        <span class="text-[7px] font-black uppercase tracking-[0.1em]" style="color: var(--theme-label);">
                                            total
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-2.5 space-y-1.5">
                                    <template x-for="(part, partIndex) in (entry.parts ?? [])" :key="partIndex">
                                        <div class="character-attack-damage-log-part flex items-center justify-between gap-3 rounded-lg border px-2.5 py-2">
                                            <div class="min-w-0">
                                                <span
                                                    class="block truncate text-[9px] font-black uppercase tracking-[0.06em]"
                                                    style="color: var(--theme-label);"
                                                    x-text="part.type_label"
                                                ></span>
                                                <span
                                                    class="mt-0.5 block truncate font-mono text-[9px]"
                                                    style="color: var(--theme-text-secondary);"
                                                    x-text="part.formatted || part.expression"
                                                ></span>
                                            </div>

                                            <strong
                                                class="shrink-0 font-serif text-[17px] font-black"
                                                style="color: var(--theme-text-strong);"
                                                x-text="part.total"
                                            ></strong>
                                        </div>
                                    </template>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>

                <footer
                    x-show="damageLog.length > 0"
                    x-cloak
                    class="flex shrink-0 items-center justify-between gap-3 border-t px-4 py-3"
                    style="border-color: var(--theme-border); background: var(--theme-modal-footer-background, var(--theme-band-background));"
                >
                    <span class="text-[8px] font-semibold" style="color: var(--theme-text-secondary);">
                        O histórico é limpo ao encerrar a sessão do navegador.
                    </span>

                    <button
                        type="button"
                        @click="clearDamageLog()"
                        class="rounded-lg border px-3 py-2 text-[8px] font-black uppercase tracking-[0.08em]"
                        style="border-color: var(--theme-border); background: var(--theme-surface-raised); color: var(--theme-text-strong);"
                    >
                        Limpar
                    </button>
                </footer>
            </aside>
        </div>
    </template>


    {{-- =========================================================
         Feedback de ações — criar / editar / excluir
    ========================================================== --}}

    <template x-teleport="body">
        <div
            x-show="actionToastOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-3 opacity-0 scale-[.985]"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0 opacity-100 scale-100"
            x-transition:leave-end="translate-y-2 opacity-0 scale-[.99]"
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-attack-action-toast fixed left-1/2 z-[260] w-[min(92vw,390px)] -translate-x-1/2 overflow-hidden rounded-xl border" style="bottom: clamp(76px, 11vh, 104px);"
            :class="actionToastType === 'error' ? 'is-error' : ''"
            role="status"
            aria-live="polite"
        >
            <div class="flex items-center gap-3 px-3.5 py-3">
                <span class="character-attack-action-toast-icon flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-[15px] font-black">
                    <span x-text="actionToastType === 'error' ? '!' : '✓'"></span>
                </span>

                <div class="min-w-0 flex-1">
                    <p class="character-attack-action-toast-title text-[10px] font-black uppercase tracking-[0.08em]">
                        Ataques
                    </p>
                    <p
                        class="character-attack-action-toast-message mt-0.5 text-[12px] font-semibold"
                        x-text="actionToastMessage"
                    ></p>
                </div>

                <button
                    type="button"
                    @click="actionToastOpen = false"
                    class="character-attack-action-toast-close flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-base"
                    aria-label="Fechar aviso"
                >
                    ×
                </button>
            </div>
        </div>
    </template>


    {{-- =========================================================
         Resultado de rolagem
    ========================================================== --}}

    <div
        x-show="rollToastOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-3 opacity-0 scale-[.985]"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-2 opacity-0 scale-[.99]"
        class="character-attack-toast fixed bottom-5 left-1/2 z-[195] w-[min(94vw,470px)] -translate-x-1/2 overflow-hidden rounded-xl border border-[#cdbb9f] bg-[#faf8f2]"
    >
        <div class="flex items-start gap-3 px-3.5 py-3">
            <div
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[#53150f] font-serif text-[18px] font-black text-[#f4f1e8]"
                x-text="lastRoll?.total ?? '!'"
            ></div>

            <div class="min-w-0 flex-1">
                <p
                    class="truncate text-[10px] font-black text-[#53150f]"
                    x-text="lastRoll?.label ?? 'Erro'"
                ></p>

                {{-- Rolagem simples --}}
                <div
                    x-show="lastRoll && !lastRoll?.parts"
                    class="mt-1 text-[8px] font-bold leading-relaxed text-[#8c6239]"
                >
                    <span x-text="lastRoll?.formatted || lastRoll?.expression || ''"></span>

                    <span
                        x-show="
                            (lastRoll?.exhaustionPenalty ?? 0) > 0
                        "
                        x-cloak
                        class="
                            ml-1
                            font-black
                            text-[#8b1e16]
                        "
                    >
                        · Exaustão
                        <span
                            x-text="
                                '-' + (
                                    lastRoll?.exhaustionPenalty
                                    ?? 0
                                )
                            "
                        ></span>
                    </span>
                </div>

                {{-- Rolagem conjunta de todos os danos --}}
                <div
                    x-show="lastRoll?.parts && lastRoll.parts.length"
                    x-cloak
                    class="mt-2"
                >
                    <div class="flex flex-wrap items-center gap-1.5">
                        <template
                            x-for="(part, partIndex) in (lastRoll?.parts ?? [])"
                            :key="partIndex"
                        >
                            <span class="inline-flex items-center gap-1 rounded-md bg-[#efe9dc]/65 px-2 py-1.5 text-[9px] font-semibold leading-relaxed text-[#5f3a27]">
                                <span>(</span>

                                <span
                                    class="font-mono"
                                    x-text="part.formatted || part.expression"
                                ></span>

                                <span
                                    class="font-black uppercase tracking-[0.05em] text-[#8c6239]"
                                    x-text="part.type_label"
                                ></span>

                                <span
                                    class="font-serif font-black text-[#53150f]"
                                    x-text="'= ' + part.total"
                                ></span>

                                <span>)</span>
                            </span>
                        </template>
                    </div>

                    <div class="mt-2 flex items-baseline justify-end gap-1.5 border-t border-[#d8c7ab]/55 pt-2">
                        <span class="font-serif text-[17px] font-black text-[#53150f]">
                            =
                        </span>

                        <strong
                            class="font-serif text-[18px] text-[#53150f]"
                            x-text="lastRoll?.total"
                        ></strong>

                        <span class="text-[8px] font-black uppercase tracking-[0.1em] text-[#8c6239]">
                            Total
                        </span>
                    </div>
                </div>

                <p
                    x-show="rollError"
                    class="mt-1 text-[8px] font-bold text-red-700"
                    x-text="rollError"
                ></p>
            </div>

            <button
                type="button"
                @click="rollToastOpen = false"
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-[#8c6239] hover:bg-[#efe9dc]"
            >
                ×
            </button>
        </div>
    </div>


    {{-- =========================================================
         Modal
    ========================================================== --}}

    <template x-teleport="body">
        <div
            x-show="modalOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[180] flex items-center justify-center p-4"
        >
            <div
                class="
                    character-theme-modal-backdrop
                    character-attack-backdrop
                    absolute
                    inset-0

                    backdrop-blur-[8px]
                "

                @click="closeModal()"
            ></div>

            <div
                x-show="modalOpen"

                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                @click.stop
                class="character-theme-modal character-attack-editor relative z-10 flex max-h-[92dvh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#faf8f2] shadow-2xl"
            >
                <div class="character-theme-modal-header character-attack-editor-header shrink-0 border-b border-[#a0774d]/30 bg-[#eadbc8] shadow-[inset_0_1px_0_rgba(255,255,255,.72)]">
                    <div class="flex items-center justify-between gap-4 px-4 pb-3 pt-4">
                        <div>
                            <p class="character-attack-editor-kicker text-[10px] font-black uppercase tracking-[0.18em] text-[#8c6239]">
                                Ataques
                            </p>

                            <h3 class="character-attack-editor-title mt-0.5 font-serif text-xl font-black text-[#53150f]">
                                <span
                                    x-text="
                                        editingId
                                            ? 'Editar Ataque'
                                            : 'Novo Ataque'
                                    "
                                ></span>
                            </h3>
                        </div>

                        <button
                            type="button"
                            @click="closeModal()"
                            class="character-attack-editor-close flex h-9 w-9 items-center justify-center rounded-lg text-lg text-[#8c6239] hover:bg-[#e8dfd1]"
                        >
                            ×
                        </button>
                    </div>

                    <div class="grid grid-cols-2 border-t border-[#d8c7ab]/45">
                        <button
                            type="button"
                            @click="setTab('attack')"
                            class="character-attack-modal-tab px-3 py-2.5 text-[11px] font-black uppercase tracking-[0.12em] text-[#8c6239]"
                            :class="{
                                'active':
                                    activeTab === 'attack'
                            }"
                        >
                            Ataque & Danos
                        </button>

                        <button
                            type="button"
                            @click="setTab('details')"
                            class="character-attack-modal-tab px-3 py-2.5 text-[11px] font-black uppercase tracking-[0.12em] text-[#8c6239]"
                            :class="{
                                'active':
                                    activeTab === 'details'
                            }"
                        >
                            Efeito & Rastreador
                        </button>
                    </div>
                </div>


                <div
                    x-show="form"
                    class="character-attack-editor-body min-h-0 flex-1 overflow-y-auto p-4"
                >

                    {{-- =================================================
                         Aba 1
                    ================================================== --}}

                    <div
                        x-show="
                            activeTab === 'attack'
                        "
                        class="space-y-2.5"
                    >
                        <section class="character-attack-modal-card p-3.5">
                            <label class="block">
                                <span class="text-[10px] font-black uppercase tracking-[0.13em] text-[#53150f]">
                                    Nome
                                </span>

                                <input
                                    type="text"
                                    maxlength="120"
                                    x-model="form.name"
                                    placeholder="Ex.: Golpe Flamejante"
                                    class="mt-1.5 w-full rounded-xl border border-[#cdbb9f] bg-white px-3 py-2.5 text-sm font-bold text-[#53150f] outline-none focus:border-[#6b1d14]"
                                >
                            </label>
                        </section>


                        {{-- Acerto --}}

                        <section class="character-attack-modal-card p-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.14em] text-[#8c6239]">
                                        Acerto
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-[#8c6239]/70">
                                        Atributo + proficiência + bônus adicional.
                                    </p>
                                </div>

                                <strong
                                    class="font-serif text-2xl text-[#53150f]"
                                    x-text="
                                        signed(
                                            formAttackModifier
                                        )
                                    "
                                ></strong>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <label>
                                    <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                        Atributo de Acerto
                                    </span>

                                    <select
                                        x-model="
                                            form.attack_ability
                                        "
                                        class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-[13px] font-bold text-[#53150f]"
                                    >
                                        <option value="">
                                            Nenhum
                                        </option>

                                        @foreach ($abilityLabels as $key => $label)
                                            <option
                                                value="{{ $key }}"
                                            >
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                <label>
                                    <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                        Bônus de Acerto
                                    </span>

                                    <input
                                        type="number"
                                        x-model.number="
                                            form.attack_bonus
                                        "
                                        class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-center text-sm font-black text-[#53150f]"
                                    >
                                </label>
                            </div>

                            <label class="mt-3 flex items-center justify-between rounded-lg border border-[#d8c7ab]/60 bg-[#efe9dc]/40 px-3 py-2.5">
                                <span>
                                    <span class="block text-[11px] font-black text-[#53150f]">
                                        Adicionar Proficiência
                                    </span>

                                    <span class="mt-0.5 block text-[9px] text-[#8c6239]/70">
                                        Bônus atual: +{{ $proficiencyBonus }}
                                    </span>
                                </span>

                                <input
                                    type="checkbox"
                                    x-model="
                                        form.use_proficiency
                                    "
                                    class="h-5 w-5 rounded border-[#cdbb9f] text-[#6b1d14]"
                                >
                            </label>

                            <div class="mt-3 rounded-lg border border-[#d8c7ab]/55 bg-[#faf8f2] px-3 py-2 text-center">
                                <span class="text-[10px] font-bold text-[#8c6239]">
                                    Rolagem:
                                </span>

                                <strong
                                    class="ml-1 font-mono text-[13px] text-[#53150f]"
                                    x-text="
                                        formAttackExpression
                                    "
                                ></strong>
                            </div>
                        </section>


                        {{-- Alcance e maestria --}}

                        <section class="character-attack-modal-card p-3.5">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_1.7fr]">
                                <label>
                                    <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                        Alcance
                                    </span>

                                    <input
                                        type="text"
                                        x-model="form.range"
                                        placeholder="Ex.: 5 ft, 30/120 ft"
                                        class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-[13px] font-bold text-[#53150f]"
                                    >
                                </label>

                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                        Maestrias
                                    </p>

                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @foreach ($masteryOptions as $mastery)
                                            <button
                                                type="button"
                                                @click="
                                                    toggleMastery(
                                                        '{{ $mastery }}'
                                                    )
                                                "
                                                class="rounded-lg border px-2 py-1.5 text-[10px] font-black uppercase tracking-wide transition"
                                                :class="
                                                    form.masteries.includes(
                                                        '{{ $mastery }}'
                                                    )
                                                        ? 'border-[#6b1d14] bg-[#6b1d14] text-[#f4f1e8]'
                                                        : 'border-[#d8c7ab] bg-[#faf8f2] text-[#8c6239] hover:bg-[#efe9dc]'
                                                "
                                            >
                                                {{ $mastery }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>


                        {{-- Danos separados --}}

                        <section class="character-attack-modal-card p-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.14em] text-[#8c6239]">
                                        Partes de Dano
                                    </p>

                                    <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/70">
                                        Cada parte mantém seu próprio dado, tipo, atributos e bônus.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="addDamagePart()"
                                    class="shrink-0 rounded-lg border border-[#cdbb9f] bg-[#f4f1e8] px-2.5 py-1.5 text-[10px] font-black uppercase tracking-wide text-[#6b1d14] hover:bg-[#efe9dc]"
                                >
                                    + Dano
                                </button>
                            </div>

                            <div class="mt-3 space-y-2">
                                <template
                                    x-for="
                                        (
                                            part,
                                            partIndex
                                        )
                                        in
                                        form.damage_parts
                                    "
                                    :key="part.id"
                                >
                                    <div class="rounded-xl border border-[#d8c7ab]/60 bg-[#faf8f2] p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-[10px] font-black uppercase tracking-[0.12em] text-[#53150f]">
                                                Dano
                                                <span
                                                    x-text="
                                                        partIndex + 1
                                                    "
                                                ></span>
                                            </p>

                                            <button
                                                type="button"
                                                @click="
                                                    removeDamagePart(
                                                        partIndex
                                                    )
                                                "
                                                class="text-[10px] font-black uppercase tracking-wide text-red-700 hover:underline"
                                            >
                                                Remover
                                            </button>
                                        </div>

                                        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-[1fr_1fr_90px]">
                                            <label>
                                                <span class="text-[9px] font-black uppercase tracking-wide text-[#8c6239]">
                                                    Dado / Expressão
                                                </span>

                                                <input
                                                    type="text"
                                                    x-model="
                                                        part.expression
                                                    "
                                                    placeholder="Ex.: 1d8"
                                                    class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-center font-mono text-[13px] font-black text-[#53150f]"
                                                >
                                            </label>

                                            <label>
                                                <span class="text-[9px] font-black uppercase tracking-wide text-[#8c6239]">
                                                    Tipo
                                                </span>

                                                <select
                                                    x-model="part.type"
                                                    class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-[12px] font-bold text-[#53150f]"
                                                >
                                                    <option value="">
                                                        Sem tipo
                                                    </option>

                                                    @foreach ($damageTypeLabels as $key => $label)
                                                        <option
                                                            value="{{ $key }}"
                                                        >
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>

                                            <label>
                                                <span class="text-[9px] font-black uppercase tracking-wide text-[#8c6239]">
                                                    Bônus
                                                </span>

                                                <input
                                                    type="number"
                                                    x-model.number="
                                                        part.bonus
                                                    "
                                                    class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2 py-2 text-center text-[13px] font-black text-[#53150f]"
                                                >
                                            </label>
                                        </div>

                                        <div class="mt-2">
                                            <p class="text-[9px] font-black uppercase tracking-wide text-[#8c6239]">
                                                Atributos adicionados
                                            </p>

                                            <div class="mt-1 grid grid-cols-2 gap-1 sm:grid-cols-3">
                                                @foreach ($abilityLabels as $key => $label)
                                                    <button
                                                        type="button"
                                                        @click="
                                                            toggleDamageAbility(
                                                                part,
                                                                '{{ $key }}'
                                                            )
                                                        "
                                                        class="flex items-center justify-between rounded-lg border px-2 py-1.5 text-[9px] font-black transition"
                                                        :class="
                                                            part.abilities.includes(
                                                                '{{ $key }}'
                                                            )
                                                                ? 'border-[#6b1d14] bg-[#6b1d14] text-[#f4f1e8]'
                                                                : 'border-[#d8c7ab] bg-[#fffdf9] text-[#8c6239] hover:bg-[#efe9dc]'
                                                        "
                                                    >
                                                        <span>
                                                            {{ $label }}
                                                        </span>

                                                        <span>
                                                            {{ $signed(
                                                                $abilityModifiers[
                                                                    $key
                                                                ] ?? 0
                                                            ) }}
                                                        </span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="mt-2 flex items-center justify-between gap-3 rounded-lg bg-[#efe9dc]/55 px-2.5 py-2">
                                            <span class="text-[9px] font-black uppercase tracking-wide text-[#8c6239]">
                                                Resultado
                                            </span>

                                            <strong
                                                class="font-mono text-[13px] text-[#53150f]"
                                                x-text="
                                                    previewDamagePart(
                                                        part
                                                    ).roll_expression
                                                    || '—'
                                                "
                                            ></strong>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>


                    {{-- =================================================
                         Aba 2
                    ================================================== --}}

                    <div
                        x-show="
                            activeTab === 'details'
                        "
                        x-cloak
                        class="space-y-2.5"
                    >
                        <section class="character-attack-modal-card p-3.5">
                            <label class="block">
                                <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                    Efeito
                                </span>

                                <input
                                    type="text"
                                    maxlength="255"
                                    x-model="form.effect"
                                    placeholder="Ex.: ao quarto acerto, causa..."
                                    class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-[13px] font-bold text-[#53150f]"
                                >
                            </label>

                            <label class="mt-3 block">
                                <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                    Descrição / Observações
                                </span>

                                <textarea
                                    rows="5"
                                    x-model="
                                        form.description
                                    "
                                    placeholder="Regra completa, condições, detalhes importantes..."
                                    class="mt-1 w-full resize-none rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-[12px] leading-relaxed text-[#53150f]"
                                ></textarea>
                            </label>
                        </section>


                        {{-- Rastreador --}}

                        <section class="character-attack-modal-card p-3.5">
                            <label class="flex items-center justify-between rounded-lg border border-[#d8c7ab]/60 bg-[#efe9dc]/40 px-3 py-2.5">
                                <span>
                                    <span class="block text-[11px] font-black text-[#53150f]">
                                        Usar Rastreador
                                    </span>

                                    <span class="mt-0.5 block text-[9px] text-[#8c6239]/70">
                                        Controle usos que diminuem ou uma contagem que aumenta.
                                    </span>
                                </span>

                                <input
                                    type="checkbox"
                                    x-model="
                                        form.counter_enabled
                                    "
                                    class="h-5 w-5 rounded border-[#cdbb9f] text-[#6b1d14]"
                                >
                            </label>

                            <div
                                x-show="
                                    form.counter_enabled
                                "
                                x-cloak
                                class="mt-2.5 space-y-2.5"
                            >
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <button
                                        type="button"
                                        @click="
                                            setCounterMode(
                                                'spend'
                                            )
                                        "
                                        class="rounded-xl border p-3 text-left transition"
                                        :class="
                                            form.counter_mode === 'spend'
                                                ? 'border-[#6b1d14] bg-[#6b1d14]/5'
                                                : 'border-[#d8c7ab] bg-[#faf8f2] hover:bg-[#efe9dc]'
                                        "
                                    >
                                        <div class="flex items-center justify-between gap-2">
                                            <strong class="text-[12px] text-[#53150f]">
                                                Rastreador de Usos
                                            </strong>

                                            <span class="font-serif text-sm font-black text-[#6b1d14]">
                                                5/5 → 4/5
                                            </span>
                                        </div>

                                        <p class="mt-1 text-[10px] leading-relaxed text-[#8c6239]/75">
                                            Começa cheio e diminui conforme o recurso é gasto.
                                        </p>
                                    </button>

                                    <button
                                        type="button"
                                        @click="
                                            setCounterMode(
                                                'build'
                                            )
                                        "
                                        class="rounded-xl border p-3 text-left transition"
                                        :class="
                                            form.counter_mode === 'build'
                                                ? 'border-[#6b1d14] bg-[#6b1d14]/5'
                                                : 'border-[#d8c7ab] bg-[#faf8f2] hover:bg-[#efe9dc]'
                                        "
                                    >
                                        <div class="flex items-center justify-between gap-2">
                                            <strong class="text-[12px] text-[#53150f]">
                                                Contador Progressivo
                                            </strong>

                                            <span class="font-serif text-sm font-black text-[#6b1d14]">
                                                0/4 → 4/4
                                            </span>
                                        </div>

                                        <p class="mt-1 text-[10px] leading-relaxed text-[#8c6239]/75">
                                            Começa em zero e soma até atingir o gatilho do efeito.
                                        </p>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <label>
                                        <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                            Atual
                                        </span>

                                        <input
                                            type="number"
                                            min="0"
                                            x-model.number="
                                                form.uses_current
                                            "
                                            class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2 py-2 text-center text-sm font-black text-[#53150f]"
                                        >
                                    </label>

                                    <label>
                                        <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                            Máximo / Gatilho
                                        </span>

                                        <input
                                            type="number"
                                            min="1"
                                            x-model.number="
                                                form.uses_max
                                            "
                                            class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2 py-2 text-center text-sm font-black text-[#53150f]"
                                        >
                                    </label>
                                </div>

                                <label class="block">
                                    <span class="text-[10px] font-black uppercase tracking-wide text-[#8c6239]">
                                        Recuperação
                                    </span>

                                    <select
                                        x-model="
                                            form.recovery
                                        "
                                        class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-[13px] font-bold text-[#53150f]"
                                    >
                                        <option value="none">
                                            Sem recuperação automática
                                        </option>

                                        <option value="short_rest">
                                            Descanso curto
                                        </option>

                                        <option value="long_rest">
                                            Descanso longo
                                        </option>
                                    </select>
                                </label>
                            </div>
                        </section>


                        <section class="character-attack-modal-card p-3.5">
                            <label class="flex items-center justify-between gap-4">
                                <span>
                                    <span class="block text-[11px] font-black text-[#53150f]">
                                        Mostrar na ficha
                                    </span>

                                    <span class="mt-0.5 block text-[9px] text-[#8c6239]/70">
                                        Ocultar não apaga o ataque.
                                    </span>
                                </span>

                                <input
                                    type="checkbox"
                                    x-model="form.visible"
                                    class="h-5 w-5 rounded border-[#cdbb9f] text-[#6b1d14]"
                                >
                            </label>
                        </section>
                    </div>


                    <div
                        x-show="saveError"
                        x-cloak
                        class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-[12px] font-bold text-red-700"
                        x-text="saveError"
                    ></div>
                </div>


                {{-- Footer --}}

                <div class="character-attack-editor-footer flex shrink-0 items-center justify-between gap-3 border-t border-[#d8c7ab]/60 bg-[#efe9dc]/45 px-4 py-3">
                    <button
                        x-show="editingId"
                        x-cloak
                        type="button"
                        @click="
                            deleteConfirmOpen =
                                true
                        "
                        :disabled="saving"
                        class="rounded-lg px-3 py-2 text-[11px] font-black uppercase tracking-widest text-red-700 hover:bg-red-50 disabled:opacity-50"
                    >
                        Excluir
                    </button>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            type="button"
                            @click="closeModal()"
                            :disabled="saving"
                            class="character-attack-secondary-action rounded-lg border px-3 py-2 text-[11px] font-black uppercase tracking-widest text-[#8c6239] hover:bg-[#e8dfd1] disabled:opacity-50"
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="saveAttack()"
                            :disabled="saving"
                            class="character-attack-primary-action rounded-lg border bg-[#6b1d14] px-4 py-2 text-[11px] font-black uppercase tracking-widest text-[#f4f1e8] hover:bg-[#53150f] disabled:opacity-50"
                        >
                            <span x-show="!saving">
                                Salvar
                            </span>

                            <span x-show="saving" x-cloak>
                                Salvando...
                            </span>
                        </button>
                    </div>
                </div>


                {{-- Confirmação de exclusão --}}

                <div
                    x-show="deleteConfirmOpen"
                    x-cloak

                    x-transition:enter="character-theme-modal-layer-enter"
                    x-transition:enter-start="character-theme-modal-layer-enter-start"
                    x-transition:enter-end="character-theme-modal-layer-enter-end"
                    x-transition:leave="character-theme-modal-layer-leave"
                    x-transition:leave-start="character-theme-modal-layer-leave-start"
                    x-transition:leave-end="character-theme-modal-layer-leave-end"

                    class="
                        character-theme-modal-layer
                        character-theme-modal-backdrop
                        character-attack-backdrop
                        absolute
                        inset-0
                        z-30

                        flex
                        items-center
                        justify-center

                        p-4
                        backdrop-blur-[8px]
                    "
                >
                    <div
                        x-show="deleteConfirmOpen"

                        x-transition:enter="character-theme-modal-panel-enter"
                        x-transition:enter-start="character-theme-modal-panel-enter-start"
                        x-transition:enter-end="character-theme-modal-panel-enter-end"
                        x-transition:leave="character-theme-modal-panel-leave"
                        x-transition:leave-start="character-theme-modal-panel-leave-start"
                        x-transition:leave-end="character-theme-modal-panel-leave-end"

                        @click.stop
                        class="character-theme-modal character-attack-delete-dialog w-full max-w-sm overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#faf8f2] shadow-2xl"
                    >
                        <div class="character-theme-modal-header border-b border-[#a0774d]/30 bg-[#eadbc8] px-4 py-3 shadow-[inset_0_1px_0_rgba(255,255,255,.72)]">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-red-700">
                                Excluir ataque
                            </p>

                            <h4 class="character-theme-modal-title mt-0.5 font-serif text-lg font-black text-[#53150f]">
                                Confirmar exclusão?
                            </h4>
                        </div>

                        <div class="character-theme-modal-copy p-4 text-[13px] leading-relaxed text-[#5f3a27]">
                            O ataque
                            <strong
                                x-text="
                                    form?.name
                                    ?? ''
                                "
                            ></strong>
                            será removido permanentemente.
                        </div>

                        <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/60 bg-[#efe9dc]/45 px-4 py-3">
                            <button
                                type="button"
                                @click="
                                    deleteConfirmOpen =
                                        false
                                "
                                class="rounded-lg px-3 py-2 text-[11px] font-black uppercase tracking-widest text-[#8c6239] hover:bg-[#e8dfd1]"
                            >
                                Voltar
                            </button>

                            <button
                                type="button"
                                @click="deleteAttack()"
                                :disabled="saving"
                                class="rounded-lg bg-red-700 px-4 py-2 text-[11px] font-black uppercase tracking-widest text-white hover:bg-red-800 disabled:opacity-50"
                            >
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</section>