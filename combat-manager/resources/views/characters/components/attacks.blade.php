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

    /* V66.2.112 — ataques dinâmicos dos itens atualmente equipados.
       Nenhum CharacterAttack é duplicado no banco. */
    $equippedWeaponService = app(\App\Services\Characters\EquippedWeaponAttack::class);
    foreach ($character->items()->where('equipped', true)->get() as $equippedItem) {
        $weaponRow = $equippedWeaponService->row($character, $equippedItem);
        if ($weaponRow !== null) $rows[] = $weaponRow;
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

            /* V66.2.112 — Armas automáticas e munição, todas as paletas. */
            .character-sheet-theme[data-theme] .character-attack-weapon-ammo-row,
            .character-sheet-theme[data-theme] .character-attack-weapon-feature-row {height:auto!important;min-height:64px!important}
            .character-sheet-theme[data-theme] .character-attack-weapon-ammo {
                display:flex;flex-wrap:wrap;align-items:center;gap:4px 7px;min-width:0;
                padding:2px 1px 5px;margin-bottom:4px;border-bottom:1px solid var(--theme-border-soft);
                color:var(--theme-text-secondary);font-size:9px;
            }
            .character-sheet-theme[data-theme] .character-attack-weapon-ability {
                flex:0 0 auto;padding:2px 4px;border:1px solid var(--theme-border-soft);
                border-radius:4px;color:var(--theme-text-secondary);background:var(--theme-surface-card);
                font-size:8px;font-weight:800;white-space:nowrap;
            }
            .character-attack-weapon-ammo-label {font-weight:800;letter-spacing:.02em}
            .character-sheet-theme[data-theme] .character-attack-weapon-ammo-count {color:var(--theme-text-strong);font:800 12px Georgia,serif}
            .character-attack-weapon-ammo-actions {display:flex;flex-wrap:wrap;gap:3px;margin-left:auto}
            .character-sheet-theme[data-theme] .character-attack-weapon-ammo-actions button,
            .character-sheet-theme[data-theme] .character-attack-weapon-tracker button {
                min-height:23px;min-width:23px;padding:2px 5px;border:1px solid var(--theme-border-strong);
                border-radius:6px;color:var(--theme-text-primary);background:var(--theme-surface-card);
                font-size:10px;font-weight:800;white-space:nowrap;
            }
            .character-sheet-theme[data-theme] .character-attack-weapon-ammo-actions button:hover:not(:disabled),
            .character-sheet-theme[data-theme] .character-attack-weapon-tracker button:hover:not(:disabled) {
                background:var(--theme-card-hover);border-color:var(--theme-accent);
            }
            .character-attack-weapon-ammo-actions button:disabled,.character-attack-weapon-tracker button:disabled {opacity:.35;cursor:not-allowed}
            .character-attack-weapon-trackers {display:flex;flex-wrap:wrap;gap:4px;margin-top:4px}
            .character-sheet-theme[data-theme] .character-attack-weapon-tracker {
                display:inline-flex;align-items:center;gap:4px;max-width:100%;padding:2px 4px;
                background:var(--theme-surface-card);color:var(--theme-text-primary);
                border:1px solid var(--theme-border-soft);border-radius:6px;
            }
            .character-attack-weapon-tracker-label {max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:9px}
            .character-attack-weapon-tracker strong {font-size:10px;white-space:nowrap}
            @media(max-width:680px){.character-attack-weapon-ammo-actions {margin-left:0}}


            /* V66.2.115 — Contador compacto na linha do alcance, sem faixa extra. */
            .character-sheet-theme[data-theme]
            .character-attack-weapon-ammo-row:not(.character-attack-weapon-feature-row) {
                min-height:52px !important;
            }
            .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo {
                display:inline-flex;
                flex:0 0 auto;
                align-items:center;
                gap:4px;
                max-width:100%;
                min-height:22px;
                margin:0 0 0 4px;
                padding:1px 3px 1px 6px;
                border:1px solid var(--theme-border-soft);
                border-radius:999px;
                background:var(--theme-surface-card);
                color:var(--theme-text-secondary);
                line-height:1;
                white-space:nowrap;
                vertical-align:middle;
            }
            .character-sheet-theme[data-theme] .character-attack-weapon-ammo-icon {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                flex:0 0 12px;
                width:12px;
                height:14px;
                color:var(--theme-accent);
            }
            .character-attack-weapon-ammo-icon svg {width:12px;height:12px;display:block}
            .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo-count {
                font:800 10px/1.1 Georgia,serif;
                color:var(--theme-text-strong);
                font-variant-numeric:tabular-nums;
            }
            .character-attack-weapon-ammo-actions {
                display:inline-flex;
                flex-wrap:nowrap;
                align-items:center;
                gap:1px;
                margin:0 0 0 2px;
            }
            .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo-actions button {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                flex:0 0 18px;
                width:18px;
                height:18px;
                min-width:18px;
                min-height:18px;
                padding:0 !important;
                border:1px solid transparent !important;
                border-radius:5px;
                background:transparent !important;
                color:var(--theme-text-secondary) !important;
                font:800 12px/1 Arial,sans-serif;
                cursor:pointer;
                transition:color .15s ease,background .15s ease,border-color .15s ease;
            }
            .character-attack-weapon-ammo-actions button svg {width:12px;height:12px;display:block}
            .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo-actions button:hover:not(:disabled),
            .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo-actions button:focus-visible {
                background:var(--theme-card-hover) !important;
                border-color:var(--theme-border-strong) !important;
                color:var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo-actions button:focus-visible {
                outline:2px solid var(--theme-accent);
                outline-offset:1px;
            }
            .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo-actions button:disabled {
                opacity:.4 !important;
                cursor:default;
            }
            .character-sheet-theme[data-theme] .character-attack-weapon-ammo.is-empty .character-attack-weapon-ammo-count {
                color:var(--theme-text-secondary);
            }
            @media(max-width:680px) {
                .character-sheet-theme[data-theme] .character-attack-range .character-attack-weapon-ammo {
                    margin-left:2px;
                }
            }


            /* V66.2.113 — identidade e legibilidade do editor de ataques. */
            .character-sheet-theme[data-theme] .character-attack-origin-mark {
                display:inline-flex;align-items:center;justify-content:center;
                width:15px;height:15px;font-size:12px;opacity:.65;
                color:var(--theme-accent);flex:0 0 auto;
            }
            .character-sheet-theme[data-theme] .character-attack-origin-meta {
                border-left:1px solid var(--theme-border-soft);padding-left:7px;margin-left:3px;
                color:var(--theme-text-secondary);font-size:9px;font-weight:500;
            }
            .character-sheet-theme[data-theme] .character-attack-note-card {
                width:min(470px,calc(100vw - 24px));max-height:min(68dvh,660px);
                background:var(--theme-modal-surface,var(--theme-surface-card));
                border-color:var(--theme-modal-border,var(--theme-border));
                color:var(--theme-text-primary);box-shadow:0 16px 52px rgba(0,0,0,.28);
            }
            .character-sheet-theme[data-theme] .character-attack-note-head {
                background:var(--theme-modal-header-background,var(--theme-band-background));
                border-color:var(--theme-border);
                cursor:grab;
                touch-action:none;
                user-select:none;
                -webkit-user-select:none;
            }
            .character-sheet-theme[data-theme] .character-attack-note-head.is-dragging {
                cursor:grabbing;
            }
            .character-sheet-theme[data-theme] .character-attack-note-card {
                will-change:opacity,transform;
            }
            .character-sheet-theme[data-theme] .character-attack-note-body {
                overscroll-behavior:contain;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .character-attack-note-card {
                    transition-duration:.01ms!important;
                    animation-duration:.01ms!important;
                }
            }
            .character-attack-note-eyebrow,.character-attack-note-section-label {
                font:800 9px/1.4 Arial,sans-serif;letter-spacing:.10em;
                color:var(--theme-label);text-transform:uppercase;
            }
            .character-attack-note-title {font-size:19px;line-height:1.2;color:var(--theme-text-strong)}
            .character-attack-note-close {
                border:1px solid var(--theme-border);color:var(--theme-text-secondary);
                background:var(--theme-surface-card);font-size:20px;
            }
            .character-attack-note-close:hover {background:var(--theme-card-hover)}
            .character-attack-note-section-label {margin-bottom:8px}
            .character-attack-note-entry-list {display:flex;flex-direction:column;gap:8px}
            .character-sheet-theme[data-theme] .character-attack-note-entry {
                padding:11px 13px;border:1px solid var(--theme-border-soft);
                border-radius:10px;background:var(--theme-surface-card);
            }
            .character-attack-note-entry-title {color:var(--theme-text-strong);font:800 14px/1.3 Georgia,serif}
            .character-attack-note-entry-copy {
                margin-top:7px;color:var(--theme-text-primary);
                font:500 12px/1.7 Arial,sans-serif;white-space:pre-line;
                overflow-wrap:anywhere;
            }
            .character-attack-mastery-copy {margin-top:0;}
            .character-attack-note-entry-tag {
                padding:3px 6px;border:1px solid var(--theme-border-soft);border-radius:6px;
                color:var(--theme-label);font:700 9px Arial,sans-serif;white-space:nowrap;
            }
            .character-attack-note-empty {color:var(--theme-text-secondary);font-size:12px;line-height:1.6}
            .character-sheet-theme[data-theme] .character-attack-builder {
                background:var(--theme-modal-surface,var(--theme-surface-card));
                color:var(--theme-text-primary);
            }
            .character-attack-builder .attack-builder-kicker {font-size:9px;font-weight:800;letter-spacing:.12em;color:var(--theme-label)}
            .character-attack-builder .attack-builder-section-title {
                color:var(--theme-text-strong);font:800 18px/1.2 Georgia,serif;
            }
            .character-attack-builder .attack-builder-section-subtitle {color:var(--theme-text-secondary);font:500 11px/1.5 Arial,sans-serif}
            .character-sheet-theme[data-theme] .character-attack-builder .attack-builder-panel {
                border:1px solid var(--theme-border);border-radius:13px;
                background:var(--theme-surface-card);padding:16px;
            }
            .character-attack-builder .attack-builder-panel + .attack-builder-panel {margin-top:12px}
            .character-attack-builder .attack-builder-label {
                display:block;margin-bottom:6px;color:var(--theme-label);
                font:800 10px/1.4 Arial,sans-serif;letter-spacing:.035em;
            }
            .character-sheet-theme[data-theme] .character-attack-builder .attack-builder-input {
                display:block;width:100%;min-width:0;min-height:40px;padding:10px 12px;
                background:var(--theme-input-background,var(--theme-surface-raised));
                color:var(--theme-text-primary);border:1px solid var(--theme-border-strong);
                border-radius:9px;outline:none;font-size:13px;font-weight:600;
            }
            .character-attack-builder .attack-builder-input:focus {border-color:var(--theme-accent)!important}
            .character-attack-builder .attack-builder-input::placeholder {color:var(--theme-text-secondary);opacity:.75}
            .character-sheet-theme[data-theme] .character-attack-builder .attack-builder-toggle {
                border:1px solid var(--theme-border);background:var(--theme-surface-raised);
                color:var(--theme-text-secondary);padding:9px 12px;border-radius:9px;
                font-size:11px;font-weight:800;
            }
            .character-sheet-theme[data-theme] .character-attack-builder .attack-builder-toggle.is-selected {
                background:var(--theme-accent-softer);border-color:var(--theme-accent);color:var(--theme-text-strong);
            }
            .character-sheet-theme[data-theme] .character-attack-builder .attack-builder-preview {
                background:var(--theme-surface-raised);border:1px solid var(--theme-border-soft);
                border-radius:11px;padding:12px 14px;color:var(--theme-text-primary);
            }
            .character-attack-builder .attack-builder-preview strong {font:900 19px Georgia,serif;color:var(--theme-text-strong)}
            .character-attack-builder .attack-builder-step {font-size:11px;font-weight:800;padding:12px 8px;color:var(--theme-text-secondary);border-bottom:2px solid transparent}
            .character-attack-builder .attack-builder-step.is-active {color:var(--theme-text-strong);border-bottom-color:var(--theme-accent);background:var(--theme-accent-softer)}
            .character-attack-builder .attack-builder-damage {
                padding:13px;border-radius:11px;border:1px solid var(--theme-border-soft);background:var(--theme-surface-raised)
            }
            .character-attack-builder .attack-builder-hint {color:var(--theme-text-secondary);font-size:10px;line-height:1.5}
            .character-sheet-theme[data-theme] .character-attack-builder .attack-builder-error {
                border:1px solid var(--theme-state-damage,#d55);color:var(--theme-state-damage,#d55);
                background:var(--theme-surface-card);border-radius:9px;padding:10px;font-size:12px;
            }
            @media(max-width:540px){.character-attack-builder .attack-builder-panel{padding:12px!important}.character-attack-builder .attack-builder-section-title{font-size:16px}}
        </style>
    @endpush

    @push('styles')
        <style>
            /* V66.2.114 — Editor único, retorno ao layout de duas abas. */
            .character-sheet-theme[data-theme] .character-attack-unified {
                background:var(--theme-modal-surface,var(--theme-surface-card))!important;
                color:var(--theme-modal-text,var(--theme-text-primary))!important;
                border-color:var(--theme-modal-border,var(--theme-border))!important;
            }
            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-modal-card {
                background:var(--theme-surface-card)!important;
                border:1px solid var(--theme-border-soft)!important;
                border-radius:12px!important;
                padding:16px!important;
                color:var(--theme-text-primary)!important;
            }
            .character-sheet-theme[data-theme] .character-attack-unified input:not([type=checkbox]),
            .character-sheet-theme[data-theme] .character-attack-unified select,
            .character-sheet-theme[data-theme] .character-attack-unified textarea {
                background:var(--theme-input-background,var(--theme-surface-raised))!important;
                border-color:var(--theme-border-strong)!important;
                color:var(--theme-text-primary)!important;
                min-height:39px;
            }
            .character-sheet-theme[data-theme] .character-attack-unified input:disabled {opacity:.66}
            .character-sheet-theme[data-theme] .character-attack-unified option:disabled {color:var(--theme-text-secondary)}
            .character-attack-linked-hint {font-size:11px;line-height:1.4;margin-top:6px;color:var(--theme-text-secondary)}
            .character-attack-linked-hint span {font-weight:800;color:var(--theme-accent)}
            .character-attack-source-summary {margin-bottom:12px}
            .character-attack-source-heading {font:800 15px Georgia,serif;color:var(--theme-text-strong)}
            .character-attack-source-help {font-size:11px;line-height:1.6;color:var(--theme-text-secondary);margin:7px 0 12px}
            .character-attack-source-feature {border-top:1px solid var(--theme-border-soft);padding:10px 0}
            .character-attack-source-feature strong {display:block;font:800 13px Georgia,serif;color:var(--theme-text-strong)}
            .character-attack-source-feature p {white-space:pre-line;margin:5px 0 0;font-size:12px;line-height:1.6;color:var(--theme-text-primary)}
            .character-attack-source-feature small {font-size:10px;color:var(--theme-text-secondary)}

            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-editor-header {
                background:var(--theme-modal-header-background,var(--theme-band-background))!important;
                border-color:var(--theme-modal-border,var(--theme-border))!important;
            }
            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-editor-title,
            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-modal-card strong,
            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-modal-card label > span {
                color:var(--theme-text-strong)!important;
            }
            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-modal-tab {
                color:var(--theme-text-secondary)!important;
                border-color:var(--theme-border-soft)!important;
            }
            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-modal-tab.active {
                background:var(--theme-accent-softer,var(--theme-surface-raised))!important;
                border-bottom-color:var(--theme-accent)!important;
                color:var(--theme-text-strong)!important;
            }
            .character-sheet-theme[data-theme] .character-attack-unified .character-attack-editor-footer {
                background:var(--theme-modal-surface-soft,var(--theme-surface-card))!important;
                border-color:var(--theme-modal-border,var(--theme-border))!important;
            }
            @media(max-width:620px){.character-sheet-theme[data-theme] .character-attack-unified .character-attack-modal-card{padding:12px!important}}
        </style>
    @endpush


    @push('styles')
    <style>
        /* V66.2.120 — Hover de acerto e rolagens poligonais reutilizadas das Habilidades. */
        .character-sheet-theme[data-theme] .character-attacks-sheet .attack-v120-hit {
            min-height:34px; border:1px solid transparent;
            transition:background .18s ease,border-color .18s ease,box-shadow .18s ease,transform .16s ease;
        }
        .character-sheet-theme[data-theme] .character-attacks-sheet .attack-v120-hit:hover,
        .character-sheet-theme[data-theme] .character-attacks-sheet .attack-v120-hit:focus-visible {
            background:var(--theme-accent-softer,var(--theme-panel-soft))!important;
            border-color:var(--theme-accent,var(--theme-border-strong))!important;
            box-shadow:inset 0 0 0 1px var(--theme-border-soft)!important;
            color:var(--theme-text-strong)!important;
        }
        .character-sheet-theme[data-theme] .character-attacks-sheet .attack-v120-hit:active {transform:scale(.96)}
        .character-sheet-theme[data-theme] .character-attacks-sheet .attack-v120-hit:disabled {opacity:.65;cursor:wait}
        /* V66.2.139 — Dados independentes, atravessando a tela, sem o antigo modal alto.
           Somente o resumo inferior recebe uma superficie. Todos os tons vem do tema. */
        .attack-v139-scene {
            position:fixed; inset:0; z-index:310; isolation:isolate; pointer-events:none;
            color:var(--theme-text-primary); font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
        }
        /* No lançamento manual o fade global causava um frame vazio entre as camadas. */
        .attack-v139-scene.is-free-manual { transition:none!important; }
        .attack-v139-scene.is-free-manual::before { display:none; }
        .attack-v139-scene::before {
            content:""; position:absolute; left:50%; top:44%; width:min(72vw,600px); height:160px;
            transform:translate(-50%,-50%); border-radius:50%;
            background:radial-gradient(ellipse,var(--theme-accent-softer,transparent),transparent 72%);
            opacity:.48; filter:blur(24px); pointer-events:none;
        }
        .attack-v139-field {
            position:absolute; top:44%; left:50%; transform:translate(-50%,-50%);
            width:min(680px,calc(100vw - 26px)); min-height:84px;
            display:flex; align-items:center; justify-content:center; align-content:center;
            flex-wrap:wrap; gap:10px 14px; pointer-events:none;
        }
        .attack-v139-die {
            position:relative; width:68px; height:72px; flex:none;
            display:inline-flex; align-items:center; justify-content:center;
            opacity:1; transform-origin:center center;
        }
        .attack-v139-die[data-sides="100"] { width:140px; }
        .attack-v139-die.is-flying {
            animation:attack-v139-flight 1.34s cubic-bezier(.19,.71,.24,1) both;
            animation-delay:var(--roll-delay,0ms); will-change:transform,opacity;
        }
        .attack-v139-die.is-flying .sb-poly-die {
            animation:attack-v139-wobble .29s ease-in-out infinite alternate;
        }
        .attack-v139-die.is-landed {
            animation:attack-v139-impact .30s cubic-bezier(.16,.85,.28,1.2) both;
        }
        .attack-v139-die.is-landed .sb-poly-die { animation:attack-v139-stop .28s ease-out both; }
        .attack-v139-die .sb-poly-die { pointer-events:none; }
        .attack-v139-die-fallback { color:var(--theme-text-strong); font:900 20px Georgia,serif; }
        .attack-v139-more {
            display:inline-flex; align-items:center; justify-content:center; flex:none;
            min-width:65px; font:800 12px/1.35 system-ui,sans-serif;
            color:var(--theme-text-secondary); background:var(--theme-surface-card);
            border:1px solid var(--theme-border-soft); border-radius:999px; padding:7px 10px;
        }
        .attack-v139-hud {
            position:absolute; left:50%; bottom:clamp(15px,5.5vh,44px);
            transform:translateX(-50%); width:min(525px,calc(100vw - 24px));
            pointer-events:auto; background:var(--theme-modal-surface,var(--theme-surface-card));
            border:1px solid var(--theme-modal-border,var(--theme-border)); border-radius:14px;
            box-shadow:0 18px 42px rgba(0,0,0,.28),0 2px 10px rgba(0,0,0,.12);
            padding:10px 12px 10px 15px; backdrop-filter:blur(10px);
            animation:attack-v139-hud-enter .32s ease-out both;
        }
        .attack-v139-hud-head {display:flex; align-items:center; gap:12px; min-height:43px;}
        .attack-v139-hud-meta {min-width:0; flex:1; display:flex; flex-direction:column; gap:3px;}
        .attack-v139-kicker {
            color:var(--theme-text-secondary); font:800 9px/1 system-ui,sans-serif;
            text-transform:uppercase; letter-spacing:.11em;
        }
        .attack-v139-title {
            color:var(--theme-text-strong); font:900 14px/1.22 Georgia,serif;
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .attack-v139-outcome {flex:none; display:flex; flex-direction:column; gap:1px; align-items:flex-end;}
        .attack-v139-outcome small {color:var(--theme-text-secondary);font:750 9px system-ui,sans-serif;}
        .attack-v139-outcome strong {
            color:var(--theme-text-strong);font:900 30px/.95 Georgia,serif;
            font-variant-numeric:tabular-nums; min-width:1ch;
        }
        .attack-v139-outcome.is-ready strong {animation:attack-v139-pop .40s cubic-bezier(.2,.76,.25,1.28) both;}
        .attack-v139-close {
            flex:none; width:25px; height:25px; display:grid; place-items:center;
            font:500 18px/1 system-ui,sans-serif; cursor:pointer;
            color:var(--theme-text-secondary); background:transparent;
            border:1px solid var(--theme-border-soft); border-radius:7px;
        }
        .attack-v139-close:hover {color:var(--theme-text-strong); background:var(--theme-accent-softer);}
        .attack-v139-breakdown {
            display:flex; align-items:center; gap:7px; overflow-x:auto; overflow-y:hidden;
            border-top:1px solid var(--theme-border-soft); margin-top:7px; padding-top:8px;
            scrollbar-width:thin; white-space:nowrap;
        }
        .attack-v139-part {
            flex:none; display:inline-flex; align-items:baseline; gap:7px;
            color:var(--theme-text-secondary); background:var(--theme-surface-raised,var(--theme-surface-card));
            border:1px solid var(--theme-border-soft); border-radius:7px;
            padding:4px 7px; font:750 10px/1.2 system-ui,sans-serif;
        }
        .attack-v139-part strong {color:var(--theme-text-strong);font:900 13px Georgia,serif;}
        .attack-v139-status {font-size:10px;color:var(--theme-text-secondary);}
        .attack-v139-status.is-critical {color:var(--theme-accent);font-weight:850;}
        .attack-v139-status.is-fumble,.attack-v139-error {color:var(--theme-state-damage,#bd4b42);}
        .attack-v139-error {font:700 12px/1.4 system-ui,sans-serif;padding-top:8px;}
        /* V66.2.147 — Auras orgânicas: partículas independentes, sem órbitas/rodas.
           Crítico: detalhe dourado universal sobre a SUPERFÍCIE do tema atual. */
        .attack-v139-die.is-natural-20,
        .attack-v139-die.is-natural-1 {
            z-index:3;
            isolation:isolate;
            overflow:visible;
        }
        .attack-v139-die.is-natural-20 { --die-aura-rgb:247,188,76; --die-aura-soft:255,237,169; }
        .attack-v139-die.is-natural-1  { --die-aura-rgb:221,66,80; --die-aura-soft:255,159,159; }
        .attack-v139-die.is-natural-20::before,
        .attack-v139-die.is-natural-1::before {
            content:"";
            position:absolute; inset:-27px; z-index:0;
            border-radius:40%; pointer-events:none;
            background:
                radial-gradient(ellipse 31% 34% at 44% 48%,rgba(var(--die-aura-soft),.30),transparent 92%),
                radial-gradient(ellipse 19% 41% at 23% 57%,rgba(var(--die-aura-rgb),.23),transparent 90%),
                radial-gradient(ellipse 26% 40% at 77% 45%,rgba(var(--die-aura-rgb),.24),transparent 86%),
                radial-gradient(ellipse 37% 19% at 51% 20%,rgba(var(--die-aura-soft),.17),transparent 88%);
            filter:blur(11px); opacity:0;
            animation:attack-v147-aura-breathe 4.15s ease-in-out both;
        }
        .attack-v139-die.is-natural-20::after,
        .attack-v139-die.is-natural-1::after {
            content:"";
            position:absolute; inset:-24px; z-index:0; pointer-events:none;
            background:
                radial-gradient(ellipse 9px 25px at 21% 48%,rgba(var(--die-aura-rgb),.18),transparent 100%),
                radial-gradient(ellipse 7px 23px at 80% 53%,rgba(var(--die-aura-soft),.17),transparent 100%),
                radial-gradient(ellipse 20px 6px at 47% 17%,rgba(var(--die-aura-rgb),.14),transparent 100%);
            filter:blur(4px); opacity:0;
            animation:attack-v147-aura-wisps 4.15s ease-in-out both;
        }
        .attack-v147-motes {
            position:absolute; inset:-20px; z-index:1;
            pointer-events:none; overflow:visible;
        }
        .attack-v147-mote {
            position:absolute; left:var(--mote-x); top:var(--mote-y);
            width:var(--mote-size,4px); height:calc(var(--mote-size,4px) * 1.38);
            border-radius:50% 50% 60% 40%; opacity:0;
            background:radial-gradient(ellipse at 50% 40%,rgba(var(--die-aura-soft),.84),rgba(var(--die-aura-rgb),.35) 43%,transparent 77%);
            box-shadow:0 0 5px rgba(var(--die-aura-rgb),.20);
            filter:blur(.45px); will-change:transform,opacity;
            animation:attack-v147-mote var(--mote-time,2s) ease-in-out var(--mote-delay,0s) 2 both;
        }
        .attack-v147-mote:nth-child(1) { --mote-x:10%;--mote-y:37%;--mote-size:3px;--mote-dx:-8px;--mote-dy:-21px;--mote-time:2.1s;--mote-delay:-.2s; }
        .attack-v147-mote:nth-child(2) { --mote-x:84%;--mote-y:67%;--mote-size:4px;--mote-dx:7px;--mote-dy:-26px;--mote-time:1.95s;--mote-delay:-.6s; }
        .attack-v147-mote:nth-child(3) { --mote-x:70%;--mote-y:13%;--mote-size:2.5px;--mote-dx:8px;--mote-dy:-14px;--mote-time:2.25s;--mote-delay:-.1s; }
        .attack-v147-mote:nth-child(4) { --mote-x:22%;--mote-y:80%;--mote-size:3.5px;--mote-dx:-5px;--mote-dy:-32px;--mote-time:2.35s;--mote-delay:-.7s; }
        .attack-v147-mote:nth-child(5) { --mote-x:94%;--mote-y:26%;--mote-size:2px;--mote-dx:3px;--mote-dy:-16px;--mote-time:1.9s;--mote-delay:-.35s; }
        .attack-v147-mote:nth-child(6) { --mote-x:39%;--mote-y:94%;--mote-size:3px;--mote-dx:-10px;--mote-dy:-28px;--mote-time:2.05s;--mote-delay:-.95s; }
        .attack-v147-mote:nth-child(7) { --mote-x:5%;--mote-y:61%;--mote-size:2px;--mote-dx:5px;--mote-dy:-20px;--mote-time:1.85s;--mote-delay:-.55s; }
        .attack-v147-mote:nth-child(8) { --mote-x:80%;--mote-y:86%;--mote-size:3px;--mote-dx:10px;--mote-dy:-35px;--mote-time:2.4s;--mote-delay:-1.2s; }
        /* O brilho fica no ar, nunca altera a cor/material do poliedro. */
        .attack-v139-die.is-natural-20 .sb-poly-die,
        .attack-v139-die.is-natural-1 .sb-poly-die {
            position:relative; z-index:2;
            filter:none!important;
            animation:attack-v139-stop .28s ease-out both!important;
        }
        .attack-v139-die.is-natural-20.is-landed,
        .attack-v139-die.is-natural-1.is-landed {
            animation:attack-v146-hop 4.15s ease-out both!important;
        }
        .attack-v139-die.is-discarded .sb-poly-die { opacity:.54; filter:grayscale(.28); }
        .attack-v139-die.is-chosen:not(.is-natural-20):not(.is-natural-1) .sb-poly-die {
            filter:drop-shadow(0 0 4px rgba(242,234,210,.22));
        }
        .attack-v139-die.is-attack-d20,
        .attack-v139-die.is-check-d20 { pointer-events:auto; cursor:pointer; }
        .attack-v139-die.is-attack-d20:focus-visible {
            outline:2px solid var(--theme-accent); outline-offset:7px; border-radius:12px;
        }
        .attack-v146-roll-mode { font-weight:750; font-size:10px; }

        /* O dourado comunica o crítico; superfície, texto e controles herdam o tema.
           A cor do tema nunca é substituída por um bloco bege opaco. */
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-wrap {
            --attack-crit-metal:color-mix(in srgb,#eac16d 83%,var(--theme-accent,#eac16d) 17%);
            --attack-crit-edge:color-mix(in srgb,var(--attack-crit-metal) 62%,var(--theme-border-strong,var(--theme-border)) 38%);
            --attack-crit-surface:color-mix(in srgb,var(--theme-card-background,var(--theme-surface-card)) 90%,var(--attack-crit-metal) 10%);
            --attack-crit-glimmer:color-mix(in srgb,var(--attack-crit-metal) 33%,transparent);
            position:relative; display:flex; flex-direction:column; gap:3px;
            min-width:0;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-button.is-critical-ready,
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-button.is-critical-ready:hover,
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-button.is-critical-ready:focus-visible {
            border:1px solid var(--attack-crit-edge)!important;
            border-radius:8px;
            background:var(--attack-crit-surface)!important;
            color:var(--theme-text-strong)!important;
            animation:attack-v147-crit-pulse 1.65s ease-in-out infinite;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-button.is-critical-ready .character-attack-damage-expression,
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-button.is-critical-ready .character-attack-damage-type,
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-button.is-critical-ready .attack-v145-damage-join {
            color:var(--theme-text-strong)!important;
            opacity:1;
            animation:attack-v147-crit-letter 1.65s ease-in-out infinite;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-button.is-critical-ready .character-attack-damage-type {
            color:var(--theme-text-secondary)!important;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-prompt {
            display:flex; flex-wrap:nowrap; align-items:center; justify-content:flex-end;
            gap:5px; min-height:18px; min-width:0;
            color:var(--theme-text-secondary)!important;
            font:800 8.5px/1 system-ui,sans-serif;
            text-transform:uppercase; letter-spacing:.045em;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-prompt .attack-v147-crit-label {
            color:color-mix(in srgb,var(--theme-text-secondary) 77%,var(--attack-crit-metal) 23%)!important;
            overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-pill,
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-close {
            display:inline-flex; align-items:center; justify-content:center; flex:none;
            border:1px solid var(--theme-border-strong,var(--theme-border));
            border-radius:999px;
            background:var(--theme-surface-raised,var(--theme-surface-card))!important;
            color:var(--theme-text-strong)!important;
            box-shadow:0 1px 5px rgba(0,0,0,.08);
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-pill {
            min-width:36px; padding:2px 6px; font:900 9.5px/1 Georgia,serif;
            font-variant-numeric:tabular-nums;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-close {
            width:18px; height:18px; font:700 12px/1 system-ui,sans-serif;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-close:hover,
        .character-sheet-theme[data-theme] .character-attacks-v11-table .attack-v145-damage-close:focus-visible {
            border-color:var(--attack-crit-edge);
            background:var(--theme-card-hover,var(--theme-surface-raised))!important;
        }
        @keyframes attack-v139-flight {
            0%   {opacity:0; transform:translate3d(var(--roll-x),var(--roll-y),0) rotate(var(--roll-angle)) scale(.72);}
            12%  {opacity:1;}
            48%  {opacity:1; transform:translate3d(var(--roll-mid-x),-65px,0) rotate(195deg) scale(1.09);}
            72%  {transform:translate3d(17px,13px,0) rotate(338deg) scale(.95);}
            84%  {transform:translate3d(-8px,-11px,0) rotate(350deg) scale(1.035);}
            100% {opacity:1;transform:translate3d(0,0,0) rotate(360deg) scale(1);}
        }
        @keyframes attack-v139-wobble {
            from {transform:rotate(-9deg) scale(.96);} to {transform:rotate(9deg) scale(1.02);}
        }
        @keyframes attack-v139-impact {
            0% {transform:translateY(-11px) rotate(7deg) scale(1.13);}
            52% {transform:translateY(3px) rotate(-4deg) scale(.98);}
            100% {transform:translateY(0) rotate(0) scale(1);}
        }
        @keyframes attack-v139-stop {from{transform:rotate(5deg)}to{transform:rotate(0)}}
        @keyframes attack-v139-pop {from{opacity:.2;transform:translateY(5px) scale(1.22);}to{opacity:1;transform:none;}}
        @keyframes attack-v139-hud-enter {from{opacity:0;translate:0 12px;}to{opacity:1;translate:0 0;}}
        @keyframes attack-v146-hop {
            0%,5%,24%,42%,100% { transform:translate3d(0,0,0) scale(1); }
            14% { transform:translate3d(0,-9px,0) scale(1.025); }
            33% { transform:translate3d(0,-4px,0) scale(1.012); }
        }

        /* V66.2.148 — hover de Dano espelhando o de Acerto (d20), em todos os temas. */
        .character-sheet-theme[data-theme] .character-attacks-v11-table .character-attack-damage-all:not(.is-critical-ready) {
            min-height:34px; border:1px solid transparent; border-radius:6px;
            background:transparent!important;
            transition:background .18s ease,border-color .18s ease,box-shadow .18s ease,transform .16s ease;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .character-attack-damage-all:not(.is-critical-ready):hover,
        .character-sheet-theme[data-theme] .character-attacks-v11-table .character-attack-damage-all:not(.is-critical-ready):focus-visible {
            background:var(--theme-accent-softer,var(--theme-panel-soft))!important;
            border-color:var(--theme-accent,var(--theme-border-strong))!important;
            box-shadow:inset 0 0 0 1px var(--theme-border-soft)!important;
            color:var(--theme-text-strong)!important;
        }
        .character-sheet-theme[data-theme] .character-attacks-v11-table .character-attack-damage-all:not(.is-critical-ready):active {transform:scale(.96)}
        @keyframes attack-v147-aura-breathe {
            0% { opacity:0; transform:translate3d(0,3px,0) scale(.92); }
            16% { opacity:.55; transform:translate3d(-2px,-1px,0) scale(1.02); }
            31% { opacity:.40; transform:translate3d(2px,-3px,0) scale(1.065); }
            49% { opacity:.70; transform:translate3d(-1px,-4px,0) scale(1.04); }
            67% { opacity:.46; transform:translate3d(2px,-6px,0) scale(1.075); }
            82% { opacity:.55; transform:translate3d(-2px,-8px,0) scale(1.09); }
            100% { opacity:0; transform:translate3d(0,-12px,0) scale(1.14); }
        }
        @keyframes attack-v147-aura-wisps {
            0% { opacity:0; transform:translate3d(0,5px,0) scale(.96); }
            24% { opacity:.52; transform:translate3d(-2px,0,0) scale(1.02); }
            43% { opacity:.33; transform:translate3d(3px,-4px,0) scale(1.06); }
            65% { opacity:.65; transform:translate3d(-1px,-7px,0) scale(1.05); }
            81% { opacity:.38; transform:translate3d(2px,-11px,0) scale(1.09); }
            100% { opacity:0; transform:translate3d(0,-16px,0) scale(1.14); }
        }
        @keyframes attack-v147-mote {
            0% { opacity:0; transform:translate3d(0,9px,0) scale(.45); }
            22% { opacity:.66; transform:translate3d(calc(var(--mote-dx) * .19),calc(var(--mote-dy) * .17),0) scale(.88); }
            43% { opacity:.51; transform:translate3d(calc(var(--mote-dx) * .43),calc(var(--mote-dy) * .43),0) scale(1.06); }
            65% { opacity:.79; transform:translate3d(calc(var(--mote-dx) * .68),calc(var(--mote-dy) * .70),0) scale(.84); }
            100% { opacity:0; transform:translate3d(var(--mote-dx),var(--mote-dy),0) scale(.45); }
        }
        @keyframes attack-v147-crit-pulse {
            0%,100% { box-shadow:inset 0 0 0 1px color-mix(in srgb,var(--attack-crit-metal) 18%,transparent),0 0 7px color-mix(in srgb,var(--attack-crit-metal) 16%,transparent); }
            50% { box-shadow:inset 0 0 0 1px color-mix(in srgb,var(--attack-crit-metal) 30%,transparent),0 0 0 1px color-mix(in srgb,var(--attack-crit-metal) 13%,transparent),0 0 15px var(--attack-crit-glimmer); }
        }
        @keyframes attack-v147-crit-letter {
            0%,100% { text-shadow:0 0 0 transparent; }
            50% { text-shadow:0 0 7px color-mix(in srgb,var(--attack-crit-metal) 36%,transparent); }
        }

        /* V66.2.151 — D20: trajetórias e pontos de pouso por rolagem.
           translate (posição final) é separado de transform (voo/quique).
           Assim, o d20 não salta de volta ao centro ao revelar a face. */
        .attack-v139-die.is-d20-motion {
            translate:var(--sb-d20-end-x,0px) var(--sb-d20-end-y,0px);
            backface-visibility:hidden;
        }
        .attack-v139-die.is-d20-motion.is-flying {
            animation-name:var(--sb-d20-flight,sb-d20-arc) !important;
            animation-duration:var(--sb-d20-duration,1470ms) !important;
            animation-delay:var(--sb-d20-delay,0ms) !important;
            animation-timing-function:cubic-bezier(.21,.66,.29,1) !important;
            animation-fill-mode:both !important;
            will-change:transform,opacity;
        }
        /* O giro da malha usa velocidade contínua, não o easing das quedas. */
        .attack-v139-die.is-d20-motion.is-flying .sb-poly-die {
            animation:sb-d20-smooth-spin var(--sb-d20-spin-duration,640ms) linear infinite !important;
            backface-visibility:hidden;
        }
        .attack-v139-die.is-d20-motion.is-landed:not(.is-natural-20):not(.is-natural-1) {
            animation:sb-d20-touchdown 350ms cubic-bezier(.18,.75,.29,1) both !important;
        }
        .attack-v139-die.is-d20-motion.is-landed .sb-poly-die {
            animation:attack-v139-stop 300ms ease-out both;
        }
        /* Saída independente: o dado parte de onde realmente pousou. */
        .attack-v139-die.is-d20-motion.is-landed.is-departing:not(.is-natural-20):not(.is-natural-1),
        .attack-v139-die.is-d20-motion.is-natural-20.is-landed.is-departing,
        .attack-v139-die.is-d20-motion.is-natural-1.is-landed.is-departing {
            animation:sb-d20-depart 740ms cubic-bezier(.40,.05,.74,.50) both !important;
        }
        .attack-v139-die.is-d20-motion.is-departing::before,
        .attack-v139-die.is-d20-motion.is-departing::after,
        .attack-v139-die.is-d20-motion.is-departing .attack-v147-motes {
            opacity:0; animation:none!important;
        }
        @keyframes sb-d20-smooth-spin {
            from { transform:rotate(-360deg) scale(.97); }
            to { transform:rotate(0deg) scale(1.02); }
        }
        @keyframes sb-d20-touchdown {
            0% { transform:translate3d(0,-12px,0) rotate(12deg) scale(1.10); }
            48% { transform:translate3d(0,4px,0) rotate(-5deg) scale(.98); }
            72% { transform:translate3d(0,-4px,0) rotate(3deg) scale(1.025); }
            100% { transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-arc {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.69); }
            12% { opacity:1; }
            45% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(1.09); }
            78% { transform:translate3d(var(--sb-d20-late-x),var(--sb-d20-late-y),0) rotate(var(--sb-d20-r2)) scale(.95); }
            90% { transform:translate3d(-4px,-6px,0) rotate(9deg) scale(1.035); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-plunge {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.60); }
            15% { opacity:1; }
            39% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(.93); }
            74% { transform:translate3d(0,-45px,0) rotate(var(--sb-d20-r2)) scale(1.13); }
            89% { transform:translate3d(0,3px,0) rotate(-10deg) scale(.96); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-sweep {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.76); }
            9% { opacity:1; }
            40% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(1.04); }
            72% { transform:translate3d(var(--sb-d20-late-x),var(--sb-d20-late-y),0) rotate(var(--sb-d20-r2)) scale(1.03); }
            88% { transform:translate3d(9px,-4px,0) rotate(-6deg) scale(1.01); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-ricochet {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.75); }
            15% { opacity:1; }
            42% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(1.12); }
            67% { transform:translate3d(var(--sb-d20-late-x),14px,0) rotate(var(--sb-d20-r2)) scale(.92); }
            83% { transform:translate3d(-17px,-16px,0) rotate(35deg) scale(1.06); }
            93% { transform:translate3d(4px,-2px,0) rotate(-7deg) scale(.99); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-bank {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.70); }
            12% { opacity:1; }
            33% { transform:translate3d(var(--sb-d20-middle-x),calc(var(--sb-d20-middle-y) - 45px),0) rotate(var(--sb-d20-r1)) scale(1.09); }
            67% { transform:translate3d(var(--sb-d20-late-x),var(--sb-d20-late-y),0) rotate(var(--sb-d20-r2)) scale(.98); }
            85% { transform:translate3d(-9px,-12px,0) rotate(15deg) scale(1.06); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-skitter {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.78); }
            12% { opacity:1; }
            51% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(1.03); }
            73% { transform:translate3d(var(--sb-d20-late-x),11px,0) rotate(var(--sb-d20-r2)) scale(.94); }
            88% { transform:translate3d(16px,-6px,0) rotate(-15deg) scale(1.015); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-spiral {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.67); }
            17% { opacity:1; }
            34% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(1.13); }
            63% { transform:translate3d(calc(var(--sb-d20-late-x) * -1.2),calc(var(--sb-d20-late-y) - 25px),0) rotate(var(--sb-d20-r2)) scale(.96); }
            84% { transform:translate3d(18px,-16px,0) rotate(-18deg) scale(1.045); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-lob {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.71); }
            10% { opacity:1; }
            48% { transform:translate3d(var(--sb-d20-middle-x),calc(var(--sb-d20-middle-y) - 90px),0) rotate(var(--sb-d20-r1)) scale(1.04); }
            81% { transform:translate3d(var(--sb-d20-late-x),-23px,0) rotate(var(--sb-d20-r2)) scale(1.08); }
            92% { transform:translate3d(-5px,5px,0) rotate(6deg) scale(.98); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-reverse {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.78); }
            13% { opacity:1; }
            38% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(1.02); }
            69% { transform:translate3d(calc(var(--sb-d20-late-x) * -1),var(--sb-d20-late-y),0) rotate(var(--sb-d20-r2)) scale(.96); }
            86% { transform:translate3d(-13px,-5px,0) rotate(12deg) scale(1.035); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-bounce {
            0% { opacity:0; transform:translate3d(var(--sb-d20-from-x),var(--sb-d20-from-y),0) rotate(var(--sb-d20-r0)) scale(.77); }
            14% { opacity:1; }
            47% { transform:translate3d(var(--sb-d20-middle-x),var(--sb-d20-middle-y),0) rotate(var(--sb-d20-r1)) scale(1.06); }
            70% { transform:translate3d(var(--sb-d20-late-x),4px,0) rotate(var(--sb-d20-r2)) scale(.96); }
            82% { transform:translate3d(11px,-25px,0) rotate(-22deg) scale(1.065); }
            92% { transform:translate3d(-4px,0,0) rotate(4deg) scale(.98); }
            100% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
        }
        @keyframes sb-d20-depart {
            0% { opacity:1; transform:translate3d(0,0,0) rotate(0deg) scale(1); }
            16% { opacity:1; transform:translate3d(var(--sb-d20-exit-mid-x),-24px,0) rotate(48deg) scale(1.06); }
            100% { opacity:0; transform:translate3d(var(--sb-d20-exit-x),var(--sb-d20-exit-y),0) rotate(var(--sb-d20-exit-rot)) scale(.68); }
        }
        /* V66.2.152 — correção de regressão: um único transform para todo o
           voo, quique e rolamento de QUALQUER dado, inclusive dano e d100.
           Impede que CSS de fases anteriores recomece o giro ao pousar. */
        .attack-v139-die.is-physics,
        .attack-v139-die.is-physics.is-flying,
        .attack-v139-die.is-physics.is-landed,
        .attack-v139-die.is-physics.is-landed.is-departing {
            position:absolute !important;
            left:50%; top:50%;
            margin-left:-34px; margin-top:-36px;
            translate:none !important;
            animation:none !important;
            transform:translate3d(var(--sb-physics-x,0px),var(--sb-physics-y,0px),0);
            opacity:0;
            transform-origin:50% 50%;
            will-change:transform,opacity;
            backface-visibility:hidden;
        }
        .attack-v139-die.is-physics[data-sides="100"] { margin-left:-70px; }
        .attack-v139-die.is-physics .sb-poly-die .sb-poly-number {
            backface-visibility:hidden;
            transform-origin:center center;
        }
        /* A rolagem física é a única fonte de transform. O efeito genérico
           is-landed não pode girar a face mais uma vez ao revelar o valor. */
        .attack-v139-die.is-physics .sb-poly-die .sb-poly-face,
        .attack-v139-die.is-physics .sb-poly-die.is-landed .sb-poly-face,
        .attack-v139-die.is-physics .sb-poly-die.is-rolling .sb-poly-face {
            animation:none !important;
        }
        .attack-v139-die.is-physics .sb-poly-die,
        .attack-v139-die.is-physics.is-flying .sb-poly-die,
        .attack-v139-die.is-physics.is-landed .sb-poly-die {
            animation:none !important;
            transform:none;
            backface-visibility:hidden;
        }
        .attack-v139-die.is-physics.is-landed:is(.is-natural-20,.is-natural-1) .sb-poly-die {
            animation:attack-v146-hop 4.15s ease-in-out both !important;
        }
        .attack-v139-die.is-physics.is-departing::before,
        .attack-v139-die.is-physics.is-departing::after,
        .attack-v139-die.is-physics.is-departing .attack-v147-motes {
            animation:none !important; opacity:0 !important;
        }

        .attack-v139-field:has(.is-physics) .attack-v139-more {
            position:absolute; bottom:-90px; left:50%; transform:translateX(-50%);
        }
        @media(prefers-reduced-motion:reduce){
            .attack-v139-die.is-physics { opacity:1 !important; }
        }
        @media(max-width:600px){
            .attack-v139-field {top:41%;gap:5px 9px;width:calc(100vw - 16px);}
            .attack-v139-die {width:58px;height:62px;}
            .attack-v139-die[data-sides="100"] {width:138px;}
            .attack-v139-hud {bottom:12px;padding:9px 10px 9px 12px;}
            .attack-v139-title {font-size:13px;}
        }
        @media(prefers-reduced-motion:reduce){
            .attack-v139-die,.attack-v139-die .sb-poly-die,.attack-v139-hud,
            .attack-v139-outcome strong {animation:none!important;transition:none!important;}
            .attack-v145-damage-button,.attack-v145-damage-button .character-attack-damage-expression,.attack-v145-damage-button .character-attack-damage-type,.attack-v145-damage-button .attack-v145-damage-join {animation:none!important;}
            .attack-v139-die {opacity:1!important;transform:none!important;}
            .attack-v139-die.is-natural-20::before,.attack-v139-die.is-natural-20::after,
            .attack-v139-die.is-natural-1::before,.attack-v139-die.is-natural-1::after {animation:none!important;opacity:.28!important;}
            .attack-v147-mote {animation:none!important;opacity:0!important;}
        }
    </style>
    @endpush

    <script>
        /* V66.2.149 — gestos unificados (um clique, duplo esquerdo e duplo direito).
           Resolução de vantagem/desvantagem usa DUAS respostas independentes da API. */
        window.SpellboundD20Gestures = window.SpellboundD20Gestures || (() => {
            const leftTimers = new WeakMap();
            const rightClicks = new WeakMap();
            const DELAY = 360;
            const choice = value => ['advantage','disadvantage'].includes(value) ? value : 'normal';
            return {
                choice,
                left(event, callback) {
                    if (!event || typeof callback !== 'function') return;
                    const element = event.currentTarget;
                    if (!element || event.button !== 0) return;
                    if (event.detail >= 2) {
                        clearTimeout(leftTimers.get(element));
                        leftTimers.delete(element);
                        return;
                    }
                    clearTimeout(leftTimers.get(element));
                    leftTimers.set(element, setTimeout(() => {
                        leftTimers.delete(element);
                        callback('normal');
                    }, DELAY));
                },
                double(event, callback) {
                    event?.preventDefault?.();
                    const element = event?.currentTarget;
                    if (!element || typeof callback !== 'function') return;
                    clearTimeout(leftTimers.get(element));
                    leftTimers.delete(element);
                    callback('advantage');
                },
                right(event, callback) {
                    event?.preventDefault?.();
                    const element = event?.currentTarget;
                    if (!element || typeof callback !== 'function') return;
                    const now = Date.now();
                    const previous = rightClicks.get(element) || 0;
                    if (now - previous <= 510) {
                        rightClicks.delete(element);
                        clearTimeout(leftTimers.get(element));
                        leftTimers.delete(element);
                        callback('disadvantage');
                    } else {
                        rightClicks.set(element, now);
                    }
                },
                // Uma chamada da API para cada dado: os modificadores nunca são duplicados.
                async rollTwo({url, headers, expression='1d20', modifier=0, mode='advantage', fallback=false}) {
                    const selectedMode = choice(mode);
                    if (selectedMode === 'normal') throw new Error('Esta rotina exige vantagem ou desvantagem.');
                    const matched = String(expression).replace(/\s+/g,'').match(/^1d20([+-]\d+)?$/i);
                    if (!matched) throw new Error('Vantagem/desvantagem exige uma rolagem de 1d20.');
                    const bonus = Number(modifier) || 0;
                    const request = async () => {
                        try {
                            const response = await fetch(url, {
                                method:'POST', headers,
                                body:JSON.stringify({expression}),
                            });
                            const payload = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(payload?.message || 'Falha ao rolar d20.');
                            const data = payload?.data ?? payload;
                            const faceFromRoll = Number(Array.isArray(data?.rolls) ? data.rolls[0] : NaN);
                            const value = Number(data?.total ?? data?.result ?? data?.value ?? data?.sum ?? payload?.total ?? payload?.result);
                            const die = Number.isInteger(faceFromRoll) && faceFromRoll>=1 && faceFromRoll<=20
                                ? faceFromRoll : value - bonus;
                            if (!Number.isInteger(die) || die<1 || die>20) throw new Error('A API retornou uma face de d20 inválida.');
                            return {die, total:die+bonus, formatted:payload?.formatted || null};
                        } catch (error) {
                            if (!fallback) throw error;
                            const die = Math.floor(Math.random()*20)+1;
                            return {die,total:die+bonus,formatted:null};
                        }
                    };
                    const responses = await Promise.all([request(),request()]);
                    const rolls = responses.map(item => item.die);
                    const selectedIndex = selectedMode === 'advantage'
                        ? (rolls[1]>rolls[0] ? 1 : 0)
                        : (rolls[1]<rolls[0] ? 1 : 0);
                    return { rolls, selectedIndex, die:rolls[selectedIndex], total:responses[selectedIndex].total,
                        formatted:responses[selectedIndex].formatted, advantageMode:selectedMode };
                },
            };
        })();
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
                    trainingNames,
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
                    trainingNames,
                    urls,

                    modalOpen: false,
                    activeTab: 'attack',
                    editingId: null,
                    editingItemId: null,
                    pendingWeaponEquip: false,
                    editingWeaponSource: null,
                    // V66.2.116 — confirmação compacta; não é outro editor.
                    weaponEquipOpen: false,
                    weaponEquipItem: null,
                    weaponEquipAllowed: [],
                    weaponEquipBusy: false,
                    weaponEquipError: null,
                    trainingNames: Array.isArray(trainingNames) ? trainingNames : [],
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
                    notePosition: {top: 90, left: 20},
                    noteDrag: null,

                    masteryDrawerOpen: false,
                    masteryInfo: null,
                    masteryPosition: {top: 90, left: 20},
                    masteryDrag: null,

                    rolling: false,
                    rollToastOpen: false,
                    rollError: null,
                    lastRoll: null,
                    rollToastTimer: null,
                    rollStage: 'result',
                    rollMode: 'attack',
                    rollTitle: '',
                    rollGroups: [],
                    rollDiceTicker: null,
                    rollSequenceId: 0,
                    rollDiceDeparting: false,
                    rollDepartureTimer: null,
                    rollMotionOwner: null,
                    rollPhysicsAnimations: [],
                    rollPhysicsDuration: 2340,
                    sharedDiceToken: null,
                    sharedDiceCounter: 0,
                    sharedDiceStartedAt: 0,
                    sharedDiceMotionHandoff: null,
                    rollManualDiceTransferred: false,
                    sharedRollReplay: null,
                    manualRollOrigin: null,
                    manualRollOrigins: null,
                    criticalDamageWindowMs: 15000,
                    attackClickTimer: null,
                    rightAttackClick: {rowKey: null, at: 0},
                    rollSourceRow: null,

                    actionToastOpen: false,
                    actionToastMessage: '',
                    actionToastType: 'success',
                    actionToastTimer: null,

                    damageLogOpen: false,
                    damageLog: [],
                    damageLogSequence: 0,
                    weaponBusyId: null,

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
                        this.rollMotionOwner = 'spellbound-dice-' + Math.random().toString(36).slice(2);
                        this.loadDamageLog();
                        this.rows.forEach(row => this.resetCriticalDamageWindowState(row));
                        this.registerSharedDiceStage();
                    },

                    destroy() {
                        if (window.SpellboundDiceFX?.owner === this) {
                            window.SpellboundDiceFX = null;
                        }
                        this.stopRollDiceTicker();
                        this.cancelRollPhysics();
                        window.clearTimeout(this.rollToastTimer);
                        window.clearTimeout(this.rollDepartureTimer);
                        this.sharedDiceMotionHandoff=null;
                        this.rows.forEach(row => this.clearCriticalDamageWindow(row));
                    },

                    // V66.2.148: um único palco para todas as rolagens da ficha.
                    // Cada componente continua responsável pela API e pelo cálculo das próprias regras.
                    registerSharedDiceStage() {
                        const stage = this;
                        window.SpellboundDiceFX = {
                            owner: stage,
                            start(request = {}) {
                                if (stage.rolling) return null;
                                const mode = window.SpellboundD20Gestures.choice(request.advantageMode);
                                const originalExpression = String(request.expression || '1d20');
                                const visualExpression = mode === 'normal' ? originalExpression
                                    : originalExpression.replace(/^1d20/i, '2d20');
                                const items = Array.isArray(request.parts) && request.parts.length
                                    ? request.parts
                                    : [{label: request.label || 'Dado', expression: visualExpression}];
                                stage.sharedRollReplay = typeof request.reroll === 'function' ? request.reroll : null;
                                const token = `spellbound-${++stage.sharedDiceCounter}`;
                                stage.sharedDiceToken = token;
                                stage.sharedDiceStartedAt = Date.now();
                                stage.sharedDiceMotionHandoff = typeof request.onMotionStart === 'function'
                                    ? {token, callback:request.onMotionStart} : null;
                                const category = ['check','initiative','duration','death','free'].includes(request.kind)
                                    ? request.kind : 'check';
                                // Sem origem manual no salvamento, use as mesmas
                                // entradas aleatórias de Ataques/Perícias. A origem
                                // explícita segue o mesmo caminho dos Dados Livres.
                                const hasOrigin = !!request.origin ||
                                    (Array.isArray(request.origins) && request.origins.length > 0);
                                if (category === 'free' || (category === 'death' && hasOrigin)) {
                                    const raw = Array.isArray(request.origins) && request.origins.length
                                        ? request.origins : (request.origin ? [request.origin] : []);
                                    const mapped = [];
                                    let originalIndex = 0;
                                    for (const item of items) {
                                        const parsed = String(item?.expression || '')
                                            .replace(/\s+/g, '').match(/^(\d{1,2})d(\d{1,3})/i);
                                        const quantity = parsed ? Math.min(20, Number(parsed[1])) : 0;
                                        const sides = parsed ? Number(parsed[2]) : 0;
                                        for (let k = 0; k < quantity; k++, originalIndex++) {
                                            const input = raw[originalIndex] || raw[0] || {};
                                            const origin = {
                                                x: Number.isFinite(Number(input.x)) ? Number(input.x) : window.innerWidth / 2,
                                                y: Number.isFinite(Number(input.y)) ? Number(input.y) : window.innerHeight / 2,
                                                vx: Number(input.vx) || 0,
                                                vy: Number(input.vy) || 0,
                                                tilt: Number(input.tilt) || 0,
                                                scale: Number(input.scale) || 1,
                                            };
                                            if (sides === 100) {
                                                // Metades do d100 também podem ser relançadas de
                                                // pontos diferentes depois de permanecerem na ficha.
                                                const parts = Array.isArray(input.parts) ? input.parts : [];
                                                const first = parts[0] || {x:origin.x-35,y:origin.y-2,tilt:origin.tilt-5};
                                                const second = parts[1] || {x:origin.x+35,y:origin.y+2,tilt:origin.tilt+5};
                                                mapped.push({...origin,x:first.x,y:first.y,
                                                    vx:origin.vx-.34,vy:origin.vy-.12,tilt:first.tilt ?? origin.tilt-5});
                                                mapped.push({...origin,x:second.x,y:second.y,
                                                    vx:origin.vx+.34,vy:origin.vy+.12,tilt:second.tilt ?? origin.tilt+5});
                                            } else {
                                                mapped.push(origin);
                                            }
                                        }
                                    }
                                    stage.manualRollOrigins = mapped.length ? mapped : raw;
                                    stage.manualRollOrigin = stage.manualRollOrigins[0] || null;
                                }
                                try {
                                    stage.startRollAnimation(request.label || 'Rolagem', items, category);
                                } finally {
                                    stage.manualRollOrigin = null;
                                    stage.manualRollOrigins = null;
                                }
                                return token;
                            },
                            hideLandedFreeDice() {
                                if (stage.rollMode === 'free' && stage.rollStage === 'result') {
                                    stage.rollManualDiceTransferred = true;
                                }
                            },
                            async finish(token, result = {}) {
                                if (!token || token !== stage.sharedDiceToken) return false;
                                try {
                                    const remaining = Math.max(0, stage.visualMotionWaitMs() - (Date.now() - stage.sharedDiceStartedAt));
                                    if (remaining) await stage.waitRollAnimation(remaining);
                                    if (token !== stage.sharedDiceToken) return false;
                                    const groups = Array.isArray(result.results) && result.results.length
                                        ? result.results
                                        : [{
                                            total: result.total,
                                            modifier: result.modifier ?? 0,
                                            rolls: Array.isArray(result.rolls) ? result.rolls : [],
                                        }];
                                    await stage.settleRollAnimation(groups, result.selectedIndex ?? null);
                                    if (token !== stage.sharedDiceToken) return false;
                                    // Snapshot das posições e faces finais. Os dados livres serão
                                    // entregues à bandeja sem sair voando ou reiniciar a física.
                                    const landingSnapshot = result.fromTray
                                        ? stage.rollGroups.map(group => ({
                                            pieces: group.dice.map(die => ({
                                                x: window.innerWidth / 2 + (die.motion?.x ?? 0),
                                                y: window.innerHeight * (window.innerWidth < 600 ? .41 : .44)
                                                    + (die.motion?.y ?? 0),
                                                rotation: die.motion?.finalAngle ?? 0,
                                                face: die.display,
                                            })),
                                        })) : null;
                                    stage.sharedDiceToken = null;
                                    stage.sharedDiceMotionHandoff = null;
                                    stage.finishAnimatedRoll({
                                        label: result.label || stage.rollTitle,
                                        expression: result.expression || null,
                                        total: result.total,
                                        formatted: result.formatted || null,
                                        rolls: result.rolls || groups.flatMap(group => group.rolls || []),
                                        natural: result.natural ?? null,
                                        advantageMode: result.advantageMode || 'normal',
                                        selectedIndex: result.selectedIndex ?? null,
                                        parts: result.parts || null,
                                        fromSheet: true,
                                        fromTray: !!result.fromTray,
                                        exhaustionPenalty: result.exhaustionPenalty || 0,
                                    });
                                    return result.fromTray ? {landings: landingSnapshot} : true;
                                } catch (error) {
                                    if (token === stage.sharedDiceToken) {
                                        stage.sharedDiceToken = null;
                                        stage.sharedDiceMotionHandoff = null;
                                        stage.showRollError('Não foi possível apresentar a rolagem.');
                                    }
                                    return false;
                                }
                            },
                            fail(token, message) {
                                if (!token || token !== stage.sharedDiceToken) return;
                                stage.sharedDiceToken = null;
                                stage.sharedDiceMotionHandoff = null;
                                stage.showRollError(message || 'Não foi possível rolar.');
                            },
                            cancel(token) {
                                if (!token || token !== stage.sharedDiceToken) return;
                                stage.sharedDiceToken = null;
                                stage.sharedDiceMotionHandoff = null;
                                stage.stopRollDiceTicker();
                                stage.cancelRollPhysics();
                                window.clearTimeout(stage.rollDepartureTimer);
                                stage.rollDiceDeparting = false;
                                stage.rollToastOpen = false;
                                stage.rolling = false;
                            },
                        };
                    },


                    /* V66.2.112 — mantém apenas um ataque por item equipado. */
                    handleWeaponEquipment(detail) {
                        const id = Number(detail?.item?.id ?? detail?.weapon_attack?.item_id);
                        if (!Number.isInteger(id) || id < 1) return;
                        this.rows = this.rows.filter(row =>
                            !(row.source === 'weapon_item' && Number(row.item_id) === id)
                        );
                        if (detail?.weapon_attack && detail?.item?.equipped !== false) {
                            this.rows.push(detail.weapon_attack);
                        }
                        if (!detail?.weapon_attack && detail?.item?.equipped
                            && ['simple','martial','custom'].includes(detail?.item?.properties?.weapon?.category)) {
                            this.refreshWeaponById(id);
                        }
                        if (this.noteRow?.source === 'weapon_item'
                            && Number(this.noteRow.item_id) === id) {
                            this.noteRow = detail?.weapon_attack ?? null;
                            if (!this.noteRow) this.closeNote();
                        }
                    },

                    async changeWeaponAmmo(row, action) {
                        if (row?.source !== 'weapon_item' || !row?.ammo || this.weaponBusyId !== null) return false;
                        const id = Number(row.item_id);
                        this.weaponBusyId = id;
                        try {
                            const response = await fetch(
                                this.urls.itemEquipped.replace('__ITEM__', id),
                                {method:'PATCH', headers:this.jsonHeaders(),
                                 body:JSON.stringify({equipped:true,ammo_action:action})}
                            );
                            const result = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(result?.message || 'Não foi possível atualizar a munição.');
                            this.handleWeaponEquipment({item:result.item, weapon_attack:result.weapon_attack});
                            window.dispatchEvent(new CustomEvent('spellbound-weapon-ammo-updated', {detail:{item:result.item}}));
                            return true;
                        } catch (error) {
                            this.showActionToast(error.message, 'error');
                            return false;
                        } finally {
                            this.weaponBusyId = null;
                        }
                    },

                    async refreshWeaponById(id) {
                        try {
                            const response = await fetch(
                                this.urls.itemEquipped.replace('__ITEM__', id),
                                {method:'PATCH',headers:this.jsonHeaders(),body:JSON.stringify({equipped:true})}
                            );
                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message || 'Falha ao atualizar ataque.');
                            this.handleWeaponEquipment({item:data.item,weapon_attack:data.weapon_attack});
                        } catch (error) {
                            this.showActionToast(error.message || 'Falha ao atualizar ataque.', 'error');
                        }
                    },

                    async refreshWeaponProficiencies() {
                        const equipped = this.rows.filter(row => row.source === 'weapon_item');
                        for (const row of equipped) {
                            try {
                                const response = await fetch(
                                    this.urls.itemEquipped.replace('__ITEM__', row.item_id),
                                    {method:'PATCH',headers:this.jsonHeaders(),body:JSON.stringify({equipped:true})}
                                );
                                const data = await response.json().catch(() => ({}));
                                if (response.ok) this.handleWeaponEquipment({item:data.item,weapon_attack:data.weapon_attack});
                            } catch (error) {
                                this.showActionToast('Não foi possível atualizar a proficiência da arma.', 'error');
                            }
                        }
                    },

                    // Clique simples continua normal; aguardamos brevemente para não
                    // disparar uma rolagem antes do segundo clique do usuário.
                    queueAttackSingleClick(row, event = null) {
                        if (this.rolling || this.weaponBusyId !== null) return;
                        if (event && event.detail > 1) return;
                        window.clearTimeout(this.attackClickTimer);
                        this.attackClickTimer = window.setTimeout(() => {
                            this.attackClickTimer = null;
                            this.rollAttackRow(row, 'normal');
                        }, 270);
                    },

                    queueAttackDoubleClick(row) {
                        window.clearTimeout(this.attackClickTimer);
                        this.attackClickTimer = null;
                        this.rightAttackClick = {rowKey:null,at:0};
                        if (!this.rolling) this.rollAttackRow(row, 'advantage');
                    },

                    // Não dependemos de dblclick do navegador: clique direito
                    // gera contextmenu. Dois eventos próximos = desvantagem.
                    queueAttackRightClick(row, event = null) {
                        if (event && event.button !== 2) return;
                        if (this.rolling || this.weaponBusyId !== null) return;
                        const key = String(row?.key ?? row?.id ?? row?.item_id ?? '');
                        const now = Date.now();
                        if (this.rightAttackClick.rowKey === key && now - this.rightAttackClick.at <= 480) {
                            this.rightAttackClick = {rowKey:null,at:0};
                            window.clearTimeout(this.attackClickTimer);
                            this.attackClickTimer = null;
                            this.rollAttackRow(row, 'disadvantage');
                            return;
                        }
                        this.rightAttackClick = {rowKey:key,at:now};
                    },

                    rollAgainFromDie(mode) {
                        if (this.rolling) return;
                        if (this.rollMode === 'attack' && this.rollSourceRow) {
                            this.rollAttackRow(this.rollSourceRow, mode);
                        } else if (typeof this.sharedRollReplay === 'function') {
                            this.sharedRollReplay(mode);
                        }
                    },

                    handleDieRightClick(event = null) {
                        if (this.rollMode === 'attack' && this.rollSourceRow) {
                            this.queueAttackRightClick(this.rollSourceRow, event);
                        } else if (typeof this.sharedRollReplay === 'function') {
                            window.SpellboundD20Gestures.right(event, mode => this.rollAgainFromDie(mode));
                        }
                    },

                    async rollAttackRow(row, mode = 'normal') {
                        if (row?.ammo && Number(row.ammo.current) < 1) {
                            this.showActionToast('Sem munição. Recarregue a arma antes de atacar.', 'error');
                            return;
                        }
                        if (this.rolling || this.weaponBusyId !== null) return;
                        this.dismissCriticalDamageWindow(row);
                        const previous = this.lastRoll;
                        this.rollSourceRow = row;
                        await this.roll('Ataque: ' + row.name, this.attackExpressionWithExhaustion(row), mode);
                        if (row?.source === 'weapon_item' && row?.ammo &&
                            this.lastRoll && this.lastRoll !== previous && !this.rollError) {
                            await this.changeWeaponAmmo(row, 'decrement');
                        }
                        if (this.lastRoll && this.lastRoll !== previous && !this.rollError) {
                            if (Number(this.lastRoll.natural) === 20) {
                                this.activateCriticalDamageWindow(row);
                            } else {
                                this.dismissCriticalDamageWindow(row);
                            }
                        }
                    },

                    async changeWeaponFeatureUses(row, tracker, delta) {
                        if (row?.source !== 'weapon_item' || this.weaponBusyId !== null) return;
                        const max = Number(tracker.max) || 0;
                        const current = Number(tracker.current) || 0;
                        const next = Math.max(0, Math.min(max, current + delta));
                        if (next === current) return;
                        this.weaponBusyId = Number(row.item_id);
                        try {
                            const url = this.urls.itemFeatureUses
                                .replace('__ITEM__', row.item_id)
                                .replace('__FEATURE__', tracker.index);
                            const response = await fetch(url, {
                                method:'PATCH', headers:this.jsonHeaders(),
                                body:JSON.stringify({current:next}),
                            });
                            const result = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(result?.message || 'Não foi possível ajustar o rastreador.');
                            const saved = result?.item?.properties?.features?.[tracker.index]?.usage;
                            tracker.current = Number(saved?.current ?? next);
                            window.dispatchEvent(new CustomEvent('character-weapon-feature-updated', {detail:{item:result.item}}));
                        } catch (error) {
                            this.showActionToast(error.message, 'error');
                        } finally { this.weaponBusyId = null; }
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

                    get allowedAttackAbilities() {
                        if (this.editingItemId === null) return Object.keys(this.abilityLabels);
                        return this.editingWeaponSource?.allowed_abilities ?? ['strength'];
                    },

                    weaponAllowedAbilities(weapon) {
                        const values = Array.isArray(weapon?.allowed_abilities)
                            ? [...new Set(weapon.allowed_abilities.filter(a => ['strength','dexterity','none'].includes(a)))] : [];
                        if (values.length) return values;
                        const thrown=(weapon?.traits||[]).some(t=>['thrown','arremesso'].includes(String(t?.key||'').toLowerCase()));
                        const ranged=(Number(weapon?.range_normal_ft)||0)>0 || /rifle|pistola|mosquete|besta|arco|crossbow|bow|firearm/i.test(String(weapon?.weapon_type||''));
                        if (ranged && !thrown) return ['dexterity'];
                        if (['strength','dexterity','none'].includes(weapon?.damage?.ability)) {
                            return [weapon.damage.ability];
                        }
                        if ((weapon?.traits ?? []).some(t => ['finesse','acuidade'].includes(String(t?.key ?? '').toLowerCase()))) {
                            return ['strength','dexterity'];
                        }
                        return (Number(weapon?.range_normal_ft) || 0) > 0 ? ['dexterity'] : ['strength'];
                    },

                    trainingNormalize(name) {
                        return String(name ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                            .toLowerCase().replace(/[^a-z0-9 ]/g, ' ').replace(/\s+/g, ' ').trim();
                    },

                    weaponIsProficient(item) {
                        const weapon = item?.properties?.weapon ?? {};
                        const training = this.trainingNames.map(x => this.trainingNormalize(x));
                        const category = String(weapon.category ?? '');
                        return training.includes('todas as armas')
                            || (category === 'simple' && training.includes('armas simples'))
                            || (category === 'martial' && training.includes('armas marciais'))
                            || training.includes(this.trainingNormalize(weapon.weapon_type))
                            || training.includes(this.trainingNormalize(item.name));
                    },

                    formFromWeaponRow(row) {
                        const parts = Array.isArray(row?.edit_damage_parts) && row.edit_damage_parts.length
                            ? row.edit_damage_parts : (row?.damage_parts ?? []);
                        return {
                            ...this.blankForm(),
                            name: row?.name ?? '',
                            effect: row?.effect ?? '',
                            description: row?.description ?? '',
                            attack_ability: row?.attack_ability ?? 'none',
                            use_proficiency: !!row?.use_proficiency,
                            attack_bonus: Number(row?.attack_bonus) || 0,
                            range: row?.range ?? '',
                            masteries: Array.isArray(row?.masteries) ? [...row.masteries] : [],
                            damage_parts: parts.length ? parts.map(part => ({
                                id: part.id ?? this.newPartId(),
                                expression: part.expression ?? '', type: part.type ?? '',
                                abilities: [...(part.abilities ?? [])], bonus: Number(part.bonus) || 0,
                            })) : [this.blankDamagePart()],
                            counter_enabled: false, uses_current: null, uses_max: null,
                            visible: true, data: {},
                        };
                    },

                    syncLinkedDamageAbility() {
                        if (this.editingItemId === null || !this.form?.damage_parts?.length) return;
                        const first = this.form.damage_parts[0];
                        if (!Array.isArray(first.abilities)) return;
                        const hadWeaponAttribute = first.abilities.some(a => ['strength','dexterity'].includes(a));
                        if (hadWeaponAttribute) {
                            first.abilities = first.abilities.filter(a => !['strength','dexterity'].includes(a));
                            if (this.form.attack_ability !== 'none') first.abilities.push(this.form.attack_ability);
                        }
                    },

                    // Apenas a escolha do atributo ao equipar: o editor completo
                    // continua sendo o mesmo de criar/editar ataques.
                    openWeaponFromInventory(item) {
                        const weapon = item?.properties?.weapon;
                        if (!item || !weapon || item.equipped) return;
                        const allowed = this.weaponAllowedAbilities(weapon);
                        if (!allowed.length) return;
                        this.weaponEquipItem = item;
                        this.weaponEquipAllowed = allowed;
                        this.weaponEquipError = null;
                        this.weaponEquipOpen = true;
                    },

                    closeWeaponEquip() {
                        if (this.weaponEquipBusy) return;
                        this.weaponEquipOpen = false;
                        this.weaponEquipItem = null;
                        this.weaponEquipAllowed = [];
                        this.weaponEquipError = null;
                    },

                    async confirmWeaponEquip(ability) {
                        const item = this.weaponEquipItem;
                        if (!item || this.weaponEquipBusy || !this.weaponEquipAllowed.includes(ability)) return;
                        this.weaponEquipBusy = true;
                        this.weaponEquipError = null;
                        try {
                            // Nunca envia attack_config na confirmação; preserva as
                            // personalizações existentes e os usos da arma original.
                            const response = await fetch(
                                this.urls.itemEquipped.replace('__ITEM__', item.id),
                                {
                                    method: 'PATCH',
                                    headers: this.jsonHeaders(),
                                    body: JSON.stringify({
                                        equipped: true,
                                        weapon_attack_ability: ability,
                                    }),
                                }
                            );
                            const result = await response.json().catch(() => ({}));
                            if (!response.ok) {
                                throw new Error(
                                    Object.values(result?.errors || {}).flat().filter(Boolean).join(' ')
                                    || result?.message || 'Não foi possível equipar a arma.'
                                );
                            }
                            this.handleWeaponEquipment({item: result.item, weapon_attack: result.weapon_attack});
                            window.dispatchEvent(new CustomEvent('character-equipment-updated', {
                                detail: {item: result.item, weapon_attack: result.weapon_attack},
                            }));
                            this.weaponEquipBusy = false;
                            this.closeWeaponEquip();
                            this.showActionToast('Arma equipada.');
                        } catch (error) {
                            this.weaponEquipError = error?.message || 'Falha ao equipar a arma.';
                            this.weaponEquipBusy = false;
                        }
                    },

                    async saveLinkedWeaponAttack() {
                        if (this.editingItemId === null || this.saving || !this.form) return;
                        const payload = this.payload();
                        if (!payload.name) {this.saveError='Informe um nome para o ataque.';return;}
                        if (!this.allowedAttackAbilities.includes(payload.attack_ability)) {
                            this.saveError='Escolha um atributo permitido para esta arma.';return;
                        }
                        if (!(payload.data?.damage_parts?.length)) {
                            this.saveError='Informe ao menos uma parcela de dano.';return;
                        }
                        const itemId = this.editingItemId;
                        const willEquip = this.pendingWeaponEquip;
                        this.saving = true;
                        this.saveError = null;
                        try {
                            const origin = this.editingWeaponSource || {};
                            const defaultParts = (origin.weapon_original_damage_parts || []).map(part => ({
                                ...part, abilities:[...(part.abilities || [])],
                            }));
                            const primary = defaultParts[0];
                            if (primary?.abilities?.some(a=>['strength','dexterity'].includes(a))) {
                                primary.abilities=primary.abilities.filter(a=>!['strength','dexterity'].includes(a));
                                if(payload.attack_ability !== 'none')primary.abilities.push(payload.attack_ability);
                            }
                            const canonical = parts => JSON.stringify((parts||[]).map(part=>({
                                expression:String(part.expression||'').trim().toLowerCase(),
                                type:String(part.type||'').trim(),bonus:Number(part.bonus)||0,
                                abilities:[...(part.abilities||[])].sort(),
                            })));
                            const attackConfig = {
                                attack_ability:payload.attack_ability,
                                effect:payload.effect || '', description:payload.description || '',
                            };
                            if (payload.name !== origin.weapon_original_name) attackConfig.name=payload.name;
                            if (Number(payload.attack_bonus) !== Number(origin.weapon_original_attack_bonus||0)) {
                                attackConfig.attack_bonus=payload.attack_bonus;
                            }
                            if (String(payload.data?.range||'') !== String(origin.weapon_original_range||'')) {
                                attackConfig.range=payload.data?.range||'';
                            }
                            if (JSON.stringify(payload.data?.masteries||[]) !== JSON.stringify(origin.weapon_original_masteries||[])) {
                                attackConfig.masteries=payload.data?.masteries||[];
                            }
                            if (canonical(payload.data?.damage_parts) !== canonical(defaultParts)) {
                                attackConfig.damage_parts=payload.data?.damage_parts||[];
                            }
                            const response = await fetch(this.urls.itemEquipped.replace('__ITEM__',itemId),{
                                method:'PATCH',headers:this.jsonHeaders(),body:JSON.stringify({
                                    equipped:true, weapon_attack_ability:payload.attack_ability,
                                    attack_config:attackConfig,
                                }),
                            });
                            const result = await response.json().catch(()=>({}));
                            if (!response.ok) {
                                throw new Error(Object.values(result?.errors||{}).flat().join(' ') || result.message || 'Não foi possível salvar este ataque.');
                            }
                            this.handleWeaponEquipment({item:result.item,weapon_attack:result.weapon_attack});
                            window.dispatchEvent(new CustomEvent('character-weapon-feature-updated',{detail:{item:result.item}}));
                            window.dispatchEvent(new CustomEvent('character-equipment-updated',{
                                detail:{item:result.item,weapon_attack:result.weapon_attack},
                            }));
                            this.closeModal(true);
                            this.showActionToast(willEquip ? 'Arma equipada e ataque configurado.' : 'Ataque atualizado.');
                        } catch(error) {
                            this.saveError = error?.message || 'Não foi possível salvar o ataque.';
                        } finally { this.saving = false; }
                    },

                    openCreate() {
                        this.editingItemId = null;
                        this.pendingWeaponEquip = false;
                        this.editingWeaponSource = null;
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
                        if (row?.source === 'weapon_item') {
                            this.editingItemId = Number(row.item_id);
                            this.pendingWeaponEquip = false;
                            this.editingWeaponSource = row;
                            this.editingId = null;
                            this.form = this.formFromWeaponRow(row);
                            this.activeTab = 'attack';
                            this.saveError = null;
                            this.deleteConfirmOpen = false;
                            this.modalOpen = true;
                            return;
                        }
                        if (row?.source !== 'custom') return;
                        this.editingItemId = null;
                        this.editingWeaponSource = null;
                        this.pendingWeaponEquip = false;

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
                        this.editingItemId = null;
                        this.editingWeaponSource = null;
                        this.pendingWeaponEquip = false;
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
                        if (this.editingItemId !== null) return this.saveLinkedWeaponAttack();
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

                    // Os dois tipos abrem exatamente o mesmo editor de Ataques.
                    editAttackRow(row) {
                        this.closeNote();
                        this.openEdit(row);
                    },

                    notePreview(row) {
                        if (row?.source === 'weapon_item') {
                            const entries = row?.weapon_features ?? [];
                            const first = entries.find(item => item?.description);
                            if (first) return String(first.description).replace(/\s+/g, ' ').slice(0, 105);
                            const trait = (row?.weapon_traits ?? []).find(item => item?.description);
                            if (trait) return String(trait.description).replace(/\s+/g, ' ').slice(0, 105);
                        }
                        return String(row?.description || row?.notes || 'Clique para consultar os detalhes.')
                            .replace(/\s+/g, ' ').slice(0, 105);
                    },

                    // V66.2.117 — a janela de detalhes pode ser movida pelo cabeçalho.
                    // Pointer Events cobrem mouse, caneta e toque sem arrastar o texto.
                    startNoteDrag(event) {
                        if (!this.noteDrawerOpen || !event.isPrimary || event.button !== 0
                            || event.target.closest('button')) return;
                        const panel = this.$refs.attackNotePanel;
                        if (!panel) return;
                        const rect = panel.getBoundingClientRect();
                        this.notePosition = { left: rect.left, top: rect.top };
                        this.noteDrag = {
                            pointerId: event.pointerId,
                            x: event.clientX,
                            y: event.clientY,
                            left: rect.left,
                            top: rect.top,
                            width: rect.width,
                            height: rect.height,
                        };
                        if (event.currentTarget?.setPointerCapture) {
                            event.currentTarget.setPointerCapture(event.pointerId);
                        }
                        event.preventDefault();
                    },

                    moveNoteDrag(event) {
                        const drag = this.noteDrag;
                        if (!drag || drag.pointerId !== event.pointerId) return;
                        const margin = 8;
                        const maxLeft = Math.max(margin, window.innerWidth - drag.width - margin);
                        const maxTop = Math.max(margin, window.innerHeight - drag.height - margin);
                        this.notePosition = {
                            left: Math.max(margin, Math.min(maxLeft,
                                drag.left + event.clientX - drag.x)),
                            top: Math.max(margin, Math.min(maxTop,
                                drag.top + event.clientY - drag.y)),
                        };
                    },

                    endNoteDrag(event = null) {
                        if (!this.noteDrag) return;
                        if (event?.pointerId !== undefined
                            && event.pointerId !== this.noteDrag.pointerId) return;
                        this.noteDrag = null;
                    },

                    // V66.2.119 — Observações e Maestrias nascem exatamente no
                    // mesmo ponto da tela. O lugar de origem independe do botão
                    // clicado; somente o arraste posterior altera a posição.
                    attackInfoSpawnPosition() {
                        const margin = 12;
                        const viewportWidth = Math.max(0, window.innerWidth);
                        const viewportHeight = Math.max(0, window.innerHeight);
                        const panelWidth = Math.min(470, Math.max(0, viewportWidth - 2 * margin));
                        const panelMaxHeight = Math.min(viewportHeight * 0.68, 660);
                        return {
                            left: Math.max(margin, viewportWidth - panelWidth - margin),
                            top: Math.max(margin, Math.min(
                                Math.round(viewportHeight * 0.17),
                                viewportHeight - panelMaxHeight - margin
                            )),
                        };
                    },

                    openNote(row, event) {
                        this.endNoteDrag();
                        this.endMasteryDrag();
                        this.masteryDrawerOpen = false;
                        this.masteryInfo = null;
                        this.noteRow = row;
                        this.notePosition = this.attackInfoSpawnPosition();
                        this.noteDrawerOpen = true;
                    },

                    noteParagraphs() {
                        const text = String(this.noteRow?.description || this.noteRow?.notes || '').trim();
                        return text ? text.split(/\n\s*\n/).map(x => x.trim()).filter(Boolean) : [];
                    },

                    closeNote() {
                        this.endNoteDrag();
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

                    // V66.2.118 — o painel de Maestria usa a mesma gaveta móvel
                    // e a mesma animação de entrada da Observação.
                    startMasteryDrag(event) {
                        if (!this.masteryDrawerOpen || !event.isPrimary || event.button !== 0
                            || event.target.closest('button')) return;
                        const panel = this.$refs.attackMasteryPanel;
                        if (!panel) return;
                        const rect = panel.getBoundingClientRect();
                        this.masteryPosition = {left: rect.left, top: rect.top};
                        this.masteryDrag = {
                            pointerId: event.pointerId,
                            x: event.clientX,
                            y: event.clientY,
                            left: rect.left,
                            top: rect.top,
                            width: rect.width,
                            height: rect.height,
                        };
                        if (event.currentTarget?.setPointerCapture) {
                            event.currentTarget.setPointerCapture(event.pointerId);
                        }
                        event.preventDefault();
                    },

                    moveMasteryDrag(event) {
                        const drag = this.masteryDrag;
                        if (!drag || drag.pointerId !== event.pointerId) return;
                        const margin = 8;
                        const maxLeft = Math.max(margin, window.innerWidth - drag.width - margin);
                        const maxTop = Math.max(margin, window.innerHeight - drag.height - margin);
                        this.masteryPosition = {
                            left: Math.max(margin, Math.min(maxLeft,
                                drag.left + event.clientX - drag.x)),
                            top: Math.max(margin, Math.min(maxTop,
                                drag.top + event.clientY - drag.y)),
                        };
                    },

                    endMasteryDrag(event = null) {
                        if (!this.masteryDrag) return;
                        if (event?.pointerId !== undefined
                            && event.pointerId !== this.masteryDrag.pointerId) return;
                        this.masteryDrag = null;
                    },

                    positionMasteryAt(event) {
                        // Compartilha a âncora de Observações, inclusive no celular.
                        this.masteryPosition = this.attackInfoSpawnPosition();
                    },

                    openMastery(mastery, event = null) {
                        this.endNoteDrag();
                        this.endMasteryDrag();
                        this.noteDrawerOpen = false;
                        this.noteRow = null;
                        this.masteryInfo = {
                            name: mastery,
                            description: this.masteryDescriptions[mastery]
                                ?? 'Descrição não disponível.',
                        };
                        this.masteryDrawerOpen = true;
                        this.positionMasteryAt(event);
                    },

                    openMasteryGroup(row, event = null) {
                        this.endNoteDrag();
                        this.endMasteryDrag();
                        this.noteDrawerOpen = false;
                        this.noteRow = null;
                        const masteries = Array.isArray(row?.masteries) ? row.masteries : [];
                        this.masteryInfo = {
                            name: 'Maestrias',
                            description: null,
                            items: masteries.map(mastery => ({
                                name: mastery,
                                description: this.masteryDescriptions[mastery]
                                    ?? 'Descrição não disponível.',
                            })),
                        };
                        this.masteryDrawerOpen = true;
                        this.positionMasteryAt(event);
                    },

                    closeMastery() {
                        this.endMasteryDrag();
                        this.masteryDrawerOpen = false;
                        setTimeout(() => {
                            if (!this.masteryDrawerOpen) this.masteryInfo = null;
                        }, 180);
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
                                rolls: [],
                                faces: 0,
                                modifier: parseInt(value) || 0,
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

                        const rolled = data?.data ?? data ?? {};
                        return {
                            total,
                            formatted: data?.formatted ?? value,
                            rolls: Array.isArray(rolled.rolls)
                                ? rolled.rolls.map(Number).filter(Number.isFinite)
                                : [],
                            faces: Number(rolled.faces) || 0,
                            modifier: Number(rolled.modifier) || 0,
                        };
                    },

                    // V66.2.120 — mesmos dados poligonais das Habilidades.
                    // A animação simula o movimento; faces e total vêm da API /api/roll.
                    attackHitHint(row) {
                        const expr = this.attackExpressionWithExhaustion(row);
                        const ability = row?.attack_ability
                            ? (this.abilityLabels?.[row.attack_ability] || row.attack_ability)
                            : 'Sem atributo';
                        const proficiency = row?.use_proficiency ? 'com proficiência' : 'sem proficiência';
                        const penalty = this.exhaustionRollPenalty
                            ? ` · Exaustão -${this.exhaustionRollPenalty}` : '';
                        return `Rolar acerto: ${expr} · ${ability} · ${proficiency}${penalty}`;
                    },

                    makeRollGroup(part, index) {
                        const expression = String(part?.expression || '').replace(/\s+/g, '');
                        const parsed = expression.match(/^(\d{1,2})d(\d{1,3})([+-]\d+)?$/i);
                        const count = parsed ? Math.min(Number(parsed[1]), 20) : 0;
                        const sides = parsed ? Number(parsed[2]) : 0;
                        // V66.2.160: d100 são duas peças físicas independentes.
                        // A API continua rolando 1d100: a dezena e a unidade são
                        // apenas a representação visual da mesma face retornada.
                        const dice = [];
                        for (let dieIndex = 0; dieIndex < count; dieIndex++) {
                            const base = {sourceDieIndex:dieIndex, value:null, landed:false, selected:true};
                            if (sides === 100) {
                                dice.push({...base, id:`${index}-${dieIndex}-tens`, sides:10,
                                    percentilePart:'tens', display:'00'});
                                dice.push({...base, id:`${index}-${dieIndex}-units`, sides:10,
                                    percentilePart:'units', display:'0'});
                            } else {
                                dice.push({...base, id:`${index}-${dieIndex}`, sides, display:'—'});
                            }
                        }
                        return {
                            index,
                            label: part?.label || (index ? `Dano ${index + 1}` : 'Dado'),
                            expression,
                            modifier: parsed ? Number(parsed[3] || 0) : Number(expression) || 0,
                            total: null,
                            dice,
                        };
                    },

                    // V66.2.153 — exibimos até vinte dados, mantendo a animação aprovada.
                    // livres atravessando a tela. Todos os valores ainda vêm da API.
                    visibleRollDice() {
                        const shown = [];
                        let index = 0;
                        for (const group of this.rollGroups) {
                            for (const die of group.dice) {
                                // d100 ocupa dois espaços: nunca desenhar só a unidade.
                                const isTens = die.percentilePart === 'tens';
                                const isUnits = die.percentilePart === 'units';
                                const previous = shown[shown.length - 1];
                                const hasRoom = isTens ? shown.length <= 18 : isUnits
                                    ? shown.length < 20 && previous?.index === index - 1
                                        && previous?.die?.percentilePart === 'tens'
                                    : shown.length < 20;
                                if (hasRoom) shown.push({die, index});
                                index++;
                            }
                        }
                        return shown;
                    },

                    hiddenRollDiceCount() {
                        return Math.max(0,
                            this.rollGroups.reduce((sum, group) => sum + group.dice.length, 0) - 20
                        );
                    },

                    /* V66.2.152 — mesma física visual para TODOS os poliedros.
                       Uma sequência única por dado: arremesso -> primeiro impacto ->
                       quique amplo -> quique curto -> rolamento -> repouso.
                       O Web Animations API interpola transform sem trocar a animação ao pousar. */
                    rollPhysicsRandom(min, max) {
                        return min + Math.random() * (max - min);
                    },

                    rollPhysicsVisibleEntries() {
                        return this.visibleRollDice();
                    },

                    prepareDiceMotionPlans() {
                        // Posiciona os grupos maiores primeiro para não prensar o d100
                        // quando há vários dados de dano numa tela estreita.
                        const entries = [...this.rollPhysicsVisibleEntries()].sort((a,b)=>
                            (Number(b.die.sides) === 100 ? 1 : 0) - (Number(a.die.sides) === 100 ? 1 : 0));
                        const w = Math.max(300, Number(window.innerWidth) || 1024);
                        const h = Math.max(440, Number(window.innerHeight) || 768);
                        const mobile = w < 600;
                        const fieldY = (mobile ? .41 : .44) * h;
                        const rand = (a, b) => this.rollPhysicsRandom(a, b);
                        const maxX = Math.max(72, Math.min(w * .43, w / 2 - 73));
                        const maxTop = Math.max(85, Math.min(280, fieldY - 82));
                        const maxBottom = Math.max(54, Math.min(180, h - fieldY - (mobile ? 128 : 150)));
                        const anchors = [
                            [-.95,-.72],[-.68,-.30],[-.36,-.86],[.07,-.85],[.48,-.74],[.91,-.63],
                            [-.93,.16],[-.58,.48],[-.16,.84],[.29,.78],[.69,.48],[.96,.08],
                            [-.53,-.91],[.75,-.26],[-.17,.10],[.37,-.20],[-.89,.76],[.88,.82],
                        ];
                        // Em cada rolagem, embaralha as regiões. A posição nunca é função do índice.
                        for (let j = anchors.length - 1; j > 0; j--) {
                            const k = Math.floor(Math.random() * (j + 1));
                            [anchors[j], anchors[k]] = [anchors[k], anchors[j]];
                        }
                        const origins = ['left-upper','left-center','left-lower',
                            'right-upper','right-center','right-lower',
                            'top-left','top-center','top-right',
                            'bottom-left','bottom-center','bottom-right'];
                        for (let j=origins.length-1; j>0; j--) {
                            const k=Math.floor(Math.random()*(j+1));
                            [origins[j],origins[k]]=[origins[k],origins[j]];
                        }
                        const positions = [];
                        const dense = entries.length >= 6;
                        // Em celulares, muitos dados livres são distribuídos em
                        // posições embaralhadas de uma malha espaçada. A malha é
                        // apenas para colisões finais; os voos continuam aleatórios.
                        const compactSpots = [];
                        if (mobile && entries.length >= 9 && this.rollMode === 'free') {
                            const rows = Math.max(4,Math.ceil(entries.length/3));
                            const edgeX=Math.min(maxX-3,Math.max(80,maxX*.85));
                            for(let row=0;row<rows;row++){
                                for(let col=-1;col<=1;col++){
                                    compactSpots.push({
                                        x:col*edgeX+rand(-1,1),
                                        y:-maxTop+4+(maxTop+maxBottom-8)*(row/Math.max(1,rows-1))+rand(-1,1),
                                    });
                                }
                            }
                            for(let i=compactSpots.length-1;i>0;i--){
                                const j=Math.floor(Math.random()*(i+1));
                                [compactSpots[i],compactSpots[j]]=[compactSpots[j],compactSpots[i]];
                            }
                        }
                        let maxDuration = 0;
                        entries.forEach((entry, order) => {
                            const die = entry.die;
                            const radius = Number(die.sides) === 100 ? (mobile ? 69 : 70) : (mobile ? 29 : 34);
                            let landing = compactSpots[order] || null;
                            const fits = candidate => positions.every(point => {
                                const dx = candidate.x - point.x;
                                const dy = candidate.y - point.y;
                                const minGap = radius + point.radius + (dense ? 5 : 15);
                                return Math.hypot(dx,dy) >= minGap;
                            });
                            for (let attempt=0; !landing && attempt<170; attempt++) {
                                const anchor=anchors[(order + attempt) % anchors.length];
                                const x=Math.round(Math.max(-maxX,Math.min(maxX,
                                    anchor[0]*maxX + rand(-24,24))));
                                const y=Math.round(anchor[1] < 0
                                    ? anchor[1]*maxTop + rand(-11,11)
                                    : anchor[1]*maxBottom + rand(-11,11));
                                if (fits({x,y})) { landing={x,y}; break; }
                            }
                            if (!landing) {
                                // Reposição por amostragem uniforme: não herda o alinhamento
                                // da grade antiga quando muitos dados são lançados juntos.
                                for (let attempt=0; attempt<600; attempt++) {
                                    const candidate={x:Math.round(rand(-maxX,maxX)),
                                        y:Math.round(rand(-maxTop,maxBottom))};
                                    if (fits(candidate)) {landing=candidate;break;}
                                }
                            }
                            if (!landing) {
                                // Busca por uma grade densa como último recurso em telas estreitas.
                                const columns = mobile ? 3 : 5;
                                const gapX = radius * 2 + 7;
                                const gapY = mobile ? 75 : 88;
                                const candidate={
                                    x:Math.round((order % columns - (columns-1)/2)*gapX),
                                    y:Math.round((Math.floor(order/columns) - .8)*gapY),
                                };
                                landing={x:Math.max(-maxX,Math.min(maxX,candidate.x)),
                                    y:Math.max(-maxTop,Math.min(maxBottom,candidate.y))};
                            }
                            positions.push({x:landing.x,y:landing.y,radius});
                            const origin=origins[order % origins.length];
                            const sidePadding=rand(80,200);
                            let fromX=rand(-w*.37,w*.37), fromY=-fieldY-sidePadding;
                            if (origin.startsWith('left')) {
                                fromX=-w/2-sidePadding;
                                fromY=origin.endsWith('upper') ? -fieldY*.68 :
                                    origin.endsWith('lower') ? (h-fieldY)*.54 : rand(-fieldY*.22,(h-fieldY)*.3);
                            } else if (origin.startsWith('right')) {
                                fromX=w/2+sidePadding;
                                fromY=origin.endsWith('upper') ? -fieldY*.68 :
                                    origin.endsWith('lower') ? (h-fieldY)*.54 : rand(-fieldY*.22,(h-fieldY)*.3);
                            } else if (origin.startsWith('top')) {
                                fromY=-fieldY-sidePadding;
                                fromX=origin.endsWith('left') ? -w*.36 :
                                    origin.endsWith('right') ? w*.36 : rand(-w*.15,w*.15);
                            } else {
                                fromY=h-fieldY+sidePadding;
                                fromX=origin.endsWith('left') ? -w*.36 :
                                    origin.endsWith('right') ? w*.36 : rand(-w*.15,w*.15);
                            }
                            const travelDirection = Math.random() < .5 ? -1 : 1;
                            const impactX=landing.x-travelDirection*rand(30,75);
                            const impactY=landing.y;
                            const duration=Math.round(rand(Number(die.sides)===20 ? 2040 : 1900,
                                Number(die.sides)===20 ? 2520 : 2460));
                            const delay=Math.round(rand(0,Math.min(230,entries.length*28+60)));
                            const impactAt=rand(.50,.59);
                            const firstBounce=rand(24,49);
                            const secondBounce=firstBounce*rand(.25,.42);
                            const thirdBounce=secondBounce*rand(.15,.31);
                            const dir=travelDirection;
                            const finalAngle=dir*720;
                            const impactAngle=finalAngle-dir*rand(205,330);
                            const startAngle=impactAngle-dir*rand(620,1050);
                            const exitAngle=rand(0,Math.PI*2);
                            const exitRadius=Math.max(w,h)*rand(.68,1.06);
                            die.motion={
                                x:landing.x,y:landing.y,fromX,fromY,
                                impactX,impactY,impactAt,duration,delay,
                                firstBounce,secondBounce,thirdBounce,
                                startAngle,impactAngle,finalAngle,
                                bend:rand(-75,75),scale:rand(.94,1.03),
                                exitX:landing.x+Math.cos(exitAngle)*exitRadius,
                                exitY:landing.y+Math.sin(exitAngle)*exitRadius,
                                exitRotation:dir*rand(320,660),
                                // Estilo estático antes de o navegador iniciar o voo.
                                style:`--sb-physics-x:${landing.x}px;--sb-physics-y:${landing.y}px;`,
                            };
                            const freeOrigin = (this.rollMode === 'free' || this.rollMode === 'death')
                                ? (this.manualRollOrigins?.[entry.index] || (order === 0 ? this.manualRollOrigin : null))
                                : null;
                            if (freeOrigin) {
                                // Cada dado livre preserva seu ponto real de soltura.
                                // Rolagens agrupadas partem simultaneamente de origens independentes.
                                const origin = freeOrigin;
                                const vx = Math.max(-4.2, Math.min(4.2, Number(origin.vx) || 0));
                                const vy = Math.max(-4.2, Math.min(4.2, Number(origin.vy) || 0));
                                const speed = Math.min(1.7, Math.hypot(vx, vy) / 1.75);
                                const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
                                const m = die.motion;
                                m.fromTray = true;
                                m.fromScale = clamp(Number(origin.scale)||1,.85,1.12);
                                // A origem manual segue a MESMA regra para dados livres
                                // e salvamentos: o gesto define o lançamento, não o modo.
                                m.fromX = clamp(Number(origin.x) - w / 2, -w/2, w/2);
                                m.fromY = clamp(Number(origin.y) - fieldY, -fieldY, h-fieldY);
                                m.startAngle = clamp(Number(origin.tilt)||0,-180,180);
                                const forward = 135 + 82 * speed;
                                // O giro segue o sentido real do lançamento; não sorteia
                                // arbitrariamente uma rotação contrária ao arraste forte.
                                const spinDir = Math.abs(vx) > .16 ? Math.sign(vx) : dir;
                                // O pouso é definido ANTES do giro: converge naturalmente
                                // para uma volta completa, igual ao dado interativo na mesa.
                                // Isso elimina a virada brusca ao passar de palco para bandeja.
                                const rotations = Math.max(2,Math.min(5,Math.round(2.15+speed*.95+rand(-.28,.28))));
                                m.finalAngle = Math.round(m.startAngle / 360) * 360 + spinDir * 360 * rotations;
                                m.impactAngle = m.finalAngle - spinDir * rand(190,290) * (1 + .13 * speed);
                                // Arremesso manual: espalhamento proporcional à energia.
                                // Um gesto leve não recebe o mesmo impacto de um lançamento forte.
                                m.impactX = clamp(m.fromX + vx * forward + rand(-19,19)*(1+.38*speed), -maxX, maxX);
                                m.impactY = clamp(m.fromY + vy * (94 + speed * 36) + rand(-12,12)*(1+.36*speed), -maxTop, maxBottom);
                                // Momento do arraste propõe a zona de pouso. O resolvedor
                                // distribui as peças para que não terminem empilhadas,
                                // inclusive as duas metades físicas de um d100.
                                const desiredX=clamp(m.impactX + Math.sign(vx || (Math.random()-.5))
                                    * rand(30,78) * (1+.58*speed), -maxX, maxX);
                                const desiredY=clamp(m.impactY + rand(-9,24) + Math.sign(vy)*speed*9,
                                    -maxTop,maxBottom);
                                const others=positions.slice(0,-1);
                                const clearLanding=(point)=>others.every(other=>
                                    Math.hypot(point.x-other.x,point.y-other.y) >=
                                        radius+other.radius+(dense?5:12));
                                let finalPoint=compactSpots.length ? landing : {x:desiredX,y:desiredY};
                                if(!clearLanding(finalPoint)){
                                    finalPoint=null;
                                    // Procura o ponto livre mais próximo do impulso;
                                    // o ângulo áureo evita que os dados formem uma grade.
                                    const phase=rand(0,Math.PI*2);
                                    for(let trial=0;trial<640;trial++){
                                        const ring=Math.sqrt(trial+1)*14.6;
                                        const angle=phase+trial*2.39996323;
                                        const candidate={
                                            x:clamp(desiredX+Math.cos(angle)*ring,-maxX,maxX),
                                            y:clamp(desiredY+Math.sin(angle)*ring,-maxTop,maxBottom),
                                        };
                                        if(clearLanding(candidate)){finalPoint=candidate;break;}
                                    }
                                }
                                if(!finalPoint){
                                    // Dispersão uniforme: impede que colisões sucessivas
                                    // prendam todas as peças à mesma borda da tela.
                                    for(let trial=0;trial<800;trial++){
                                        const candidate={x:rand(-maxX,maxX),y:rand(-maxTop,maxBottom)};
                                        if(clearLanding(candidate)){finalPoint=candidate;break;}
                                    }
                                }
                                if(!finalPoint)finalPoint=landing;
                                m.x=finalPoint.x;
                                m.y=finalPoint.y;
                                positions[positions.length-1]={x:m.x,y:m.y,radius};
                                m.bend = rand(-27,27) + vx * (20 + speed * 8);
                                m.impactAt = rand(.47,.55) - Math.min(.055, speed*.03);
                                m.firstBounce = rand(16,28) + speed * 25;
                                m.secondBounce = m.firstBounce * rand(.29,.41);
                                m.thirdBounce = m.secondBounce * rand(.15,.28);
                                m.duration = Math.round(rand(2020,2320) + speed * 290);
                                if (speed > .48) {
                                    // Saída coerente com o impulso, com leve desvio.
                                    const exitAngle = Math.atan2(vy,vx) + rand(-.48,.48);
                                    const exitDistance = Math.max(w,h)*rand(.66,.98);
                                    m.exitX = m.x + Math.cos(exitAngle)*exitDistance;
                                    m.exitY = m.y + Math.sin(exitAngle)*exitDistance;
                                    m.exitRotation = spinDir*rand(350,690);
                                }
                                // No dado manual, começa no mesmo frame da soltura.
                                m.delay = 0;
                                // Colisão opcional com a borda real da janela. A trajetória
                                // reflete ao encontrar a parede e continua até o primeiro impacto.
                                // Afeta somente os dados livres, nunca os ataques já aprovados.
                                const collisionX = w / 2 - radius - 7;
                                const collisionY = h - fieldY - radius - 14;
                                const ceilingY = -fieldY + radius + 8;
                                const travelX = m.fromX + vx * rand(150,250);
                                const travelY = m.fromY + vy * rand(135,205);
                                if (travelX > collisionX || travelX < -collisionX) {
                                    m.wallCollision={axis:'x',value:travelX > 0 ? collisionX : -collisionX,
                                        at:rand(.27,.43)};
                                } else if (travelY > collisionY || travelY < ceilingY) {
                                    m.wallCollision={axis:'y',value:travelY > 0 ? collisionY : ceilingY,
                                        at:rand(.29,.45)};
                                } else if (Math.abs(m.fromX) > collisionX * .79 && Math.abs(vx) > .34 && Math.sign(vx) === Math.sign(m.fromX)) {
                                    m.wallCollision={axis:'x',value:Math.sign(vx)*collisionX,
                                        at:rand(.26,.39)};
                                }
                                maxDuration = Math.max(maxDuration, m.duration+m.delay);
                            }
                            maxDuration=Math.max(maxDuration,duration+delay);
                        });
                        this.rollPhysicsDuration=Math.ceil(maxDuration+100);
                    },

                    visualMotionWaitMs() {
                        return Math.max(1800, Number(this.rollPhysicsDuration) || 2340);
                    },

                    rollDieTravelStyle(entry) {
                        return entry?.die?.motion?.style || '';
                    },

                    cancelRollPhysics() {
                        (this.rollPhysicsAnimations || []).forEach(item => {
                            try { item.animation.cancel(); } catch (e) { /* já removida */ }
                        });
                        this.rollPhysicsAnimations=[];
                    },

                    rollPhysicsFrames(motion) {
                        const lerp=(a,b,t)=>a+(b-a)*t;
                        const easeOut=t=>1-Math.pow(1-t,2.65);
                        const frames=[];
                        // 96 quadros-alvo: renderização intermediária feita no compositor,
                        // sem setInterval, sem blocos de animação com novas origens.
                        for (let i=0; i<=96; i++) {
                            const t=i/96;
                            let x,y,angle,scale;
                            if (t <= motion.impactAt) {
                                const u=Math.min(1,t/motion.impactAt);
                                const wall=motion.wallCollision;
                                const hasWall=wall && wall.at>0 && wall.at<1;
                                if(hasWall && wall.axis==='x') {
                                    // A inversão de velocidade e contínua na posição da parede.
                                    x=u<=wall.at ? lerp(motion.fromX,wall.value,u/wall.at)
                                        : lerp(wall.value,motion.impactX,(u-wall.at)/(1-wall.at));
                                } else {
                                    x=lerp(motion.fromX,motion.impactX,u)+motion.bend*Math.sin(Math.PI*u);
                                }
                                const height=Math.max(45,
                                    Math.abs(motion.fromX-motion.impactX)*.075,
                                    Math.max(0,motion.fromY-motion.impactY)*.43);
                                if(hasWall && wall.axis==='y') {
                                    const after=Math.max(0,(u-wall.at)/(1-wall.at));
                                    y=u<=wall.at ? lerp(motion.fromY,wall.value,u/wall.at)
                                        : lerp(wall.value,motion.impactY,after)-height*.27*Math.sin(Math.PI*after);
                                } else {
                                    y=lerp(motion.fromY,motion.impactY,u)
                                        - height*Math.sin(Math.PI*u)*(0.25+.75*u);
                                }
                                angle=lerp(motion.startAngle,motion.impactAngle,Math.pow(u,.90));
                                scale=lerp(motion.fromTray ? (motion.fromScale||1) : .83,motion.scale,u);
                                if(motion.fromTray && hasWall) {
                                    // Compressão e contragiro curtos no contato com a parede.
                                    // Envelope senoidal com derivada zero nas extremidades:
                                    // o dado não salta de orientação ao ricochetear.
                                    const relative=(u-(wall.at-.115))/.23;
                                    if(relative>0 && relative<1) {
                                        const recoil=Math.pow(Math.sin(Math.PI*relative),2);
                                        const spinSign=Math.sign(motion.impactAngle-motion.startAngle)||1;
                                        angle+=spinSign*(wall.axis==='x'?-1:1)*8.5*recoil;
                                        scale-=.031*recoil;
                                    }
                                }
                            } else {
                                const finishAt=.955;
                                const v=Math.min(1,(t-motion.impactAt)/(finishAt-motion.impactAt));
                                // Três arcos reais de quique: amplitude e duração decrescentes.
                                const b1=.45, b2=.77, b3=.96;
                                let bounce=0;
                                if (v<b1) bounce=motion.firstBounce*Math.sin(Math.PI*v/b1);
                                else if (v<b2) bounce=motion.secondBounce*Math.sin(Math.PI*(v-b1)/(b2-b1));
                                else if (v<b3) bounce=motion.thirdBounce*Math.sin(Math.PI*(v-b2)/(b3-b2));
                                // Dado lançado à mão: conserva mais energia horizontal
                                // imediatamente após o impacto e perde velocidade aos poucos.
                                const travelProgress = motion.fromTray
                                    ? 1-Math.pow(1-v,1.82) : easeOut(v);
                                x=lerp(motion.impactX,motion.x,travelProgress);
                                // Continua exatamente do ponto de impacto, inclusive quando
                                // a soltura manual produz rolamento vertical adicional.
                                y=lerp(motion.impactY,motion.y,easeOut(v))-bounce;
                                angle=lerp(motion.impactAngle,motion.finalAngle,easeOut(v));
                                // Evita o pequeno salto de escala na transição voo/quique.
                                scale=lerp(motion.scale,1,Math.min(1,v/.26))
                                    +(.018*(1-v))*Math.abs(Math.sin(v*Math.PI*3));
                            }
                            frames.push({
                                offset:t,
                                transform:`translate3d(${x.toFixed(2)}px,${y.toFixed(2)}px,0) rotate(${angle.toFixed(2)}deg) scale(${scale.toFixed(3)})`,
                                opacity:motion.fromTray ? 1 : (t<.055 ? Math.max(0,t/.055) : 1),
                            });
                        }
                        return frames;
                    },

                    // Compensa o giro do poliedro: a face pode girar, mas a
                    // tipografia permanece sempre em pé durante o lançamento.
                    rollUprightNumberFrames(frames) {
                        return frames.map(frame => {
                            const angle = /rotate\((-?[\d.]+)deg\)/.exec(frame.transform);
                            return {offset:frame.offset, rotate:`${-(Number(angle?.[1]) || 0)}deg`};
                        });
                    },

                    rollPhysicsElements() {
                        const owner=this.rollMotionOwner;
                        if (!owner) return [];
                        const scene=[...document.querySelectorAll('.attack-v139-scene[data-motion-owner]')]
                            .find(node=>node.dataset.motionOwner===owner);
                        return scene ? [...scene.querySelectorAll('.attack-v139-die[data-motion-index]')] : [];
                    },

                    launchRollPhysics(sequence, retry=0) {
                        if (sequence !== this.rollSequenceId || !this.rollToastOpen) return;
                        const elements=this.rollPhysicsElements();
                        const entries=this.rollPhysicsVisibleEntries();
                        if (elements.length<entries.length && retry<4) {
                            window.setTimeout(()=>this.launchRollPhysics(sequence,retry+1),40);
                            return;
                        }
                        const reduced=window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;
                        elements.forEach(el=>{
                            const index=Number(el.dataset.motionIndex);
                            const entry=entries.find(item=>item.index===index);
                            const m=entry?.die?.motion;
                            if (!m) return;
                            const end=`translate3d(${m.x}px,${m.y}px,0)`;
                            const numbers=[...el.querySelectorAll('.sb-poly-number')];
                            if (reduced || !el.animate) {
                                el.style.transform=end;
                                el.style.opacity='1';
                                numbers.forEach(number => {number.style.rotate='0deg';});
                                return;
                            }
                            const frames=this.rollPhysicsFrames(m);
                            const options={duration:m.duration,delay:m.delay,easing:'linear',fill:'forwards',iterations:1};
                            const animation=el.animate(frames,options);
                            this.rollPhysicsAnimations.push({element:el,animation});
                            const counterFrames=this.rollUprightNumberFrames(frames);
                            numbers.forEach(number => {
                                const counter=number.animate(counterFrames,options);
                                this.rollPhysicsAnimations.push({element:number,animation:counter});
                            });
                        });
                        // Entrega a origem ao palco somente quando as animações foram
                        // instaladas: nenhum dado preparado some antes do primeiro frame.
                        const handoff=this.sharedDiceMotionHandoff;
                        if(handoff && handoff.token===this.sharedDiceToken && (this.rollMode==='free' || this.rollMode==='death')
                            && elements.length>=entries.length){
                            this.sharedDiceMotionHandoff=null;
                            const started=this.rollPhysicsAnimations.map(item=>item.animation.ready?.catch(()=>{})||Promise.resolve());
                            Promise.all(started).then(()=>{
                                if(this.sharedDiceToken!==handoff.token || !this.rollToastOpen)return;
                                window.requestAnimationFrame(()=>{
                                    if(this.sharedDiceToken===handoff.token && this.rollToastOpen){
                                        try{handoff.callback();}catch(_error){}
                                    }
                                });
                            });
                        }
                    },

                    departRollPhysics() {
                        const entries=this.rollPhysicsVisibleEntries();
                        const elements=this.rollPhysicsElements();
                        this.cancelRollPhysics();
                        const reduced=window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;
                        elements.forEach(el=>{
                            const index=Number(el.dataset.motionIndex);
                            const entry=entries.find(item=>item.index===index);
                            const m=entry?.die?.motion;
                            if (!m) return;
                            el.style.transform=`translate3d(${m.x}px,${m.y}px,0)`;
                            el.style.opacity='1';
                            if (reduced || !el.animate) { el.style.opacity='0'; return; }
                            // Mesma saída animada para todos os dados, inclusive morte.
                            const departure=[
                                {offset:0,opacity:1,transform:`translate3d(${m.x}px,${m.y}px,0) rotate(0deg) scale(1)`},
                                {offset:.26,opacity:1,transform:`translate3d(${m.x+(m.exitX-m.x)*.17}px,${m.y-21}px,0) rotate(${m.exitRotation*.18}deg) scale(1.03)`},
                                {offset:1,opacity:0,transform:`translate3d(${m.exitX}px,${m.exitY}px,0) rotate(${m.exitRotation}deg) scale(.78)`},
                            ];
                            const options={duration:780,easing:'cubic-bezier(.29,.12,.64,1)',fill:'forwards'};
                            const animation=el.animate(departure,options);
                            this.rollPhysicsAnimations.push({element:el,animation});
                            const counterFrames=this.rollUprightNumberFrames(departure);
                            el.querySelectorAll('.sb-poly-number').forEach(number => {
                                this.rollPhysicsAnimations.push({element:number,
                                    animation:number.animate(counterFrames,options)});
                            });
                        });
                    },

                    rollDieVisualClass(die) {
                        const classes = [die?.landed ? 'is-landed' : 'is-flying'];
                        if (die?.motion) {
                            classes.push('is-physics');
                            if (this.rollDiceDeparting) classes.push('is-departing');
                        }
                        if (['attack','check','initiative','death','duration','free'].includes(this.rollMode) && Number(die?.sides) === 20) {
                            if (this.rollMode === 'attack') classes.push('is-attack-d20');
                            else if (this.sharedRollReplay) classes.push('is-check-d20');
                            if (die?.landed) {
                                classes.push(die.selected ? 'is-chosen' : 'is-discarded');
                                if (die.selected && Number(die?.value) === 20) classes.push('is-natural-20');
                                if (die.selected && Number(die?.value) === 1) classes.push('is-natural-1');
                            }
                        }
                        return classes.join(' ');
                    },

                    resetCriticalDamageWindowState(row) {
                        if (!row || typeof row !== 'object') return;
                        row.damageCritActive = false;
                        row.damageCritEndsAt = null;
                        row.damageCritRemainingMs = 0;
                        row._damageCritTimeout = null;
                        row._damageCritInterval = null;
                    },

                    clearCriticalDamageWindow(row) {
                        if (!row || typeof row !== 'object') return;
                        if (row._damageCritTimeout) {
                            window.clearTimeout(row._damageCritTimeout);
                            row._damageCritTimeout = null;
                        }
                        if (row._damageCritInterval) {
                            window.clearInterval(row._damageCritInterval);
                            row._damageCritInterval = null;
                        }
                        row.damageCritActive = false;
                        row.damageCritEndsAt = null;
                        row.damageCritRemainingMs = 0;
                    },

                    activateCriticalDamageWindow(row) {
                        if (!row || !Array.isArray(row.damage_parts) || row.damage_parts.length === 0) return;
                        this.clearCriticalDamageWindow(row);
                        const duration = Math.max(4000, Number(this.criticalDamageWindowMs) || 15000);
                        const updateRemaining = () => {
                            if (!row.damageCritEndsAt) {
                                row.damageCritRemainingMs = 0;
                                return;
                            }
                            row.damageCritRemainingMs = Math.max(0, row.damageCritEndsAt - Date.now());
                            if (row.damageCritRemainingMs <= 0) {
                                this.clearCriticalDamageWindow(row);
                            }
                        };
                        row.damageCritActive = true;
                        row.damageCritEndsAt = Date.now() + duration;
                        row.damageCritRemainingMs = duration;
                        row._damageCritInterval = window.setInterval(updateRemaining, 100);
                        row._damageCritTimeout = window.setTimeout(() => this.clearCriticalDamageWindow(row), duration + 60);
                        updateRemaining();
                    },

                    dismissCriticalDamageWindow(row) {
                        this.clearCriticalDamageWindow(row);
                    },

                    isDamageCriticalReady(row) {
                        return !!(row?.damageCritActive && Number(row?.damageCritRemainingMs) > 0);
                    },

                    damageCriticalCountdownLabel(row) {
                        const ms = Math.max(0, Number(row?.damageCritRemainingMs) || 0);
                        const seconds = ms / 1000;
                        return seconds >= 10 ? `${seconds.toFixed(0)}s` : `${seconds.toFixed(1)}s`;
                    },

                    doubledDamageExpression(expression) {
                        const normalized = String(expression || '').replace(/\s+/g, '');
                        const parsed = normalized.match(/^(\d{1,2})d(\d{1,3})([+-]\d+)?$/i);
                        if (!parsed) return String(expression || '');
                        const count = Math.max(1, Number(parsed[1]) || 1);
                        const sides = Math.max(1, Number(parsed[2]) || 0);
                        const modifier = parsed[3] || '';
                        return `${Math.min(40, count * 2)}d${sides}${modifier}`;
                    },

                    damageExpressionForRow(row, part) {
                        const expression = String(part?.roll_expression || '');
                        return this.isDamageCriticalReady(row)
                            ? this.doubledDamageExpression(expression)
                            : expression;
                    },

                    startRollAnimation(label, parts, mode = 'attack') {
                        this.cancelRollPhysics();
                        this.stopRollDiceTicker();
                        clearTimeout(this.rollToastTimer);
                        clearTimeout(this.rollDepartureTimer);
                        this.rollDiceDeparting = false;
                        this.rollManualDiceTransferred = false;
                        this.rollSequenceId++;
                        this.rollMode = mode;
                        if (mode !== 'attack') this.rollSourceRow = null;
                        this.rollStage = 'rolling';
                        this.rollError = null;
                        this.lastRoll = null;
                        this.rollGroups = parts.map((part, index) => this.makeRollGroup(part, index));
                        // Mesma face do dado preparado no primeiro quadro: evita ? → —.
                        if (mode === 'free' || mode === 'death') this.rollGroups.forEach((group, index) => {
                            const source = parts[index] || {};
                            group.dice.forEach(die => {
                                if (die.percentilePart === 'tens') die.display = source.initialTens ?? '00';
                                else if (die.percentilePart === 'units') die.display = source.initialUnits ?? '0';
                                else die.display = source.initialFace ?? '?';
                            });
                        });
                        this.prepareDiceMotionPlans();
                        this.rollTitle = String(label || 'Rolagem');
                        this.rolling = true;
                        this.rollToastOpen = true;
                        const sequence=this.rollSequenceId;
                        this.$nextTick(() => {
                            window.requestAnimationFrame(() => this.launchRollPhysics(sequence));
                        });
                        this.rollDiceTicker = window.setInterval(() => {
                            for (const group of this.rollGroups) {
                                for (const die of group.dice) {
                                    if (!die.landed && die.sides > 0) {
                                        if (die.percentilePart === 'tens') {
                                            die.display = String(Math.floor(Math.random() * 10) * 10).padStart(2, '0');
                                        } else if (die.percentilePart === 'units') {
                                            die.display = String(Math.floor(Math.random() * 10));
                                        } else {
                                            die.display = String(Math.floor(Math.random() * die.sides) + 1);
                                        }
                                    }
                                }
                            }
                        }, 85);
                    },

                    stopRollDiceTicker() {
                        if (this.rollDiceTicker !== null) {
                            window.clearInterval(this.rollDiceTicker);
                            this.rollDiceTicker = null;
                        }
                    },

                    waitRollAnimation(ms) {
                        return new Promise(resolve => window.setTimeout(resolve, ms));
                    },

                    async settleRollAnimation(results, selectedIndex = null) {
                        this.rollStage = 'revealing';
                        let revealed = 0;
                        for (let groupIndex = 0; groupIndex < this.rollGroups.length; groupIndex++) {
                            const group = this.rollGroups[groupIndex];
                            const result = results[groupIndex] || {};
                            group.total = Number(result.total) || 0;
                            group.modifier = Number(result.modifier) || 0;
                            // O backend informa as faces reais, nunca são sorteadas no navegador.
                            const faces = Array.isArray(result.rolls) ? result.rolls : [];
                            for (let dieIndex = 0; dieIndex < group.dice.length; dieIndex++) {
                                const die = group.dice[dieIndex];
                                const logicalIndex = die.sourceDieIndex ?? dieIndex;
                                const actual = Number(faces[logicalIndex]);
                                if (die.percentilePart) {
                                    const valid = Number.isInteger(actual) && actual >= 1 && actual <= 100;
                                    const percentile = valid ? actual % 100 : null;
                                    die.value = valid ? (die.percentilePart === 'tens'
                                        ? Math.floor(percentile / 10) * 10 : percentile % 10) : null;
                                    die.display = !valid ? '—' : die.percentilePart === 'tens'
                                        ? String(die.value).padStart(2, '0') : String(die.value);
                                } else {
                                    die.value = Number.isInteger(actual) && actual >= 1 && actual <= die.sides
                                        ? actual : null;
                                    die.display = die.value === null ? '—' : String(die.value);
                                }
                                die.selected = selectedIndex === null || logicalIndex === selectedIndex;
                                die.landed = true;
                                // Só os vinte dados visíveis escalonam a aterrissagem.
                                // Dados adicionais revelam suas faces sem atrasar a rolagem.
                                if (revealed++ < 20) await this.waitRollAnimation(135);
                            }
                        }
                        this.stopRollDiceTicker();
                    },

                    scheduleRollToastClose(result = this.lastRoll) {
                        clearTimeout(this.rollToastTimer);
                        let delay = 4200;
                        if (this.rollMode === 'attack' && (Number(result?.natural) === 20 || Number(result?.natural) === 1)) {
                            delay = 5200;
                        }
                        clearTimeout(this.rollDepartureTimer);
                        if (this.rollMode !== 'free') {
                            this.rollDepartureTimer = window.setTimeout(() => {
                                if (this.rollToastOpen) {
                                    this.rollDiceDeparting = true;
                                    this.departRollPhysics();
                                }
                            }, delay - 800);
                        }
                        this.rollToastTimer = window.setTimeout(() => {
                            this.rollToastOpen = false;
                            this.rollDiceDeparting = false;
                        }, delay);
                    },

                    finishAnimatedRoll(result) {
                        this.stopRollDiceTicker();
                        this.lastRoll = result;
                        this.rollStage = 'result';
                        this.rolling = false;
                        this.scheduleRollToastClose(result);
                        window.dispatchEvent(new CustomEvent('character-dice-rolled', {detail: result}));
                    },

                    async roll(label, expression, mode = 'normal') {
                        if (this.rolling || !this.canRoll(expression)) return;
                        const choice = ['advantage','disadvantage'].includes(mode) ? mode : 'normal';
                        const match = String(expression).replace(/\s+/g,'').match(/^1d20([+-]\d+)?$/i);
                        if (choice !== 'normal' && !match) {
                            this.showActionToast('Vantagem e desvantagem exigem uma jogada de 1d20.', 'error');
                            return;
                        }
                        this.startRollAnimation(label, [{
                            label: choice === 'advantage' ? 'Vantagem · 2d20'
                                : choice === 'disadvantage' ? 'Desvantagem · 2d20' : 'Acerto · d20',
                            expression: choice === 'normal' ? expression : `2d20${match[1] || ''}`,
                        }], 'attack');
                        try {
                            const resultsPromise = choice === 'normal'
                                ? this.resolveExpression(expression).then(result => [result])
                                : Promise.all([
                                    this.resolveExpression(expression),
                                    this.resolveExpression(expression),
                                ]);
                            const [results] = await Promise.all([
                                resultsPromise,
                                this.waitRollAnimation(this.visualMotionWaitMs()),
                            ]);
                            const faces = results.map(result => Number(result.rolls?.[0]));
                            if (faces.some(face => !Number.isInteger(face) || face < 1 || face > 20)) {
                                throw new Error('A API não retornou as faces dos d20.');
                            }
                            // Só a face selecionada determina 20/1 natural.
                            const selectedIndex = choice === 'advantage'
                                ? (faces[1] > faces[0] ? 1 : 0)
                                : choice === 'disadvantage'
                                    ? (faces[1] < faces[0] ? 1 : 0)
                                    : 0;
                            const selected = results[selectedIndex];
                            const selectedFace = faces[selectedIndex];
                            await this.settleRollAnimation([{
                                total: selected.total,
                                modifier: selected.modifier,
                                rolls: faces,
                            }], selectedIndex);
                            this.finishAnimatedRoll({
                                label, expression, total: selected.total,
                                formatted: selected.formatted, parts: null,
                                rolls: faces, natural: selectedFace,
                                advantageMode: choice, selectedIndex,
                                exhaustionPenalty: this.exhaustionRollPenalty,
                            });
                        } catch (error) {
                            this.showRollError(error.message || 'Não foi possível rolar o acerto.');
                        } finally {
                            this.stopRollDiceTicker();
                            this.rolling = false;
                        }
                    },

                    async rollDamagePart(row, part) {
                        if (this.rolling || !this.canResolveDamage(part?.roll_expression)) return;
                        const expression = this.damageExpressionForRow(row, part);
                        this.dismissCriticalDamageWindow(row);
                        const label = `${row.name} — ${part.type_label || 'Dano'}`;
                        this.startRollAnimation(label, [
                            {label: part.type_label || 'Dano', expression}
                        ], 'damage');
                        try {
                            const [result] = await Promise.all([
                                this.resolveExpression(expression),
                                this.waitRollAnimation(this.visualMotionWaitMs()),
                            ]);
                            await this.settleRollAnimation([result]);
                            const values = [{
                                expression,
                                type_label: part.type_label || 'Dano',
                                total: result.total, formatted: result.formatted,
                            }];
                            this.recordDamageRoll(row, values, result.total);
                            this.finishAnimatedRoll({
                                label, expression: part.roll_expression, total: result.total,
                                formatted: result.formatted, parts: null, exhaustionPenalty: 0,
                            });
                        } catch (error) {
                            this.showRollError(error.message || 'Não foi possível rolar o dano.');
                        } finally {
                            this.stopRollDiceTicker();
                            this.rolling = false;
                        }
                    },

                    async rollAllDamage(row) {
                        if (this.rolling) return;
                        const parts = (row?.damage_parts ?? []).filter(part =>
                            this.canResolveDamage(part?.roll_expression)
                        );
                        if (!parts.length) return;
                        const resolvedParts = parts.map(part => ({
                            original_expression: part.roll_expression,
                            expression: this.damageExpressionForRow(row, part),
                            type_label: part.type_label || 'Dano',
                        }));
                        this.dismissCriticalDamageWindow(row);
                        const label = `${row.name} — Dano total`;
                        this.startRollAnimation(label, resolvedParts.map(part => ({
                            label: part.type_label || 'Dano',
                            expression: part.expression,
                        })), 'damage');
                        try {
                            const [results] = await Promise.all([
                                Promise.all(resolvedParts.map(part => this.resolveExpression(part.expression))),
                                this.waitRollAnimation(this.visualMotionWaitMs()),
                            ]);
                            await this.settleRollAnimation(results);
                            const resolved = resolvedParts.map((part, index) => ({
                                expression: part.expression,
                                type_label: part.type_label || 'Dano',
                                total: results[index].total,
                                formatted: results[index].formatted,
                            }));
                            const total = resolved.reduce((sum, part) => sum + part.total, 0);
                            this.recordDamageRoll(row, resolved, total);
                            this.finishAnimatedRoll({
                                label, expression: null, total, formatted: null,
                                parts: resolved, exhaustionPenalty: 0,
                            });
                        } catch (error) {
                            this.showRollError(error.message || 'Não foi possível rolar o dano.');
                        } finally {
                            this.stopRollDiceTicker();
                            this.rolling = false;
                        }
                    },

                    showRollError(message) {
                        this.cancelRollPhysics();
                        clearTimeout(this.rollDepartureTimer);
                        this.rollDiceDeparting = false;
                        this.stopRollDiceTicker();
                        this.lastRoll = null;
                        this.rollError = String(message || 'Falha na rolagem.');
                        this.rollStage = 'error';
                        this.rollToastOpen = true;
                        this.rolling = false;
                        clearTimeout(this.rollToastTimer);
                        this.rollToastTimer = window.setTimeout(() => {
                            this.rollToastOpen = false;
                        }, 5000);
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
        @js($character->features()->where('type', 'weapon_training')->pluck('name')->all()),
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

            itemEquipped: @js(route('characters.items.equipped.update', [
                'character' => $character, 'item' => '__ITEM__',
            ])),
            itemFeatureUses: @js(route('characters.items.features.uses.update', [
                'character' => $character, 'item' => '__ITEM__', 'feature' => '__FEATURE__',
            ])),
            roll: @js(url('/api/roll')),

            damageLogKey: @js(
                'spellbound:character:'
                . $character->id
                . ':attack-damage-log'
            ),
        }
    )"

    @keydown.escape.window="
        if (weaponEquipOpen) {
            closeWeaponEquip();
        } else if (settingsOpen) {
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

    x-on:spellbound-weapon-equip-request.window="openWeaponFromInventory($event.detail?.item)"
    x-on:character-equipment-updated.window="handleWeaponEquipment($event.detail)"
    x-on:character-training-updated.window="refreshWeaponProficiencies()"

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
                    <tr class="character-attack-row border-b border-[#d8c7ab]/42 last:border-b-0"
                        :class="{'character-attack-weapon-ammo-row': !!row.ammo, 'character-attack-weapon-feature-row': !!row.feature_trackers?.length}">

                        {{-- Nome + maestrias + alcance --}}

                        <td class="px-2.5 py-1 align-middle">
                            <div class="min-w-0">
                                <div class="flex min-w-0 items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="editAttackRow(row)"
                                        class="min-w-0 shrink cursor-pointer text-left" :title="row.source === 'weapon_item' ? 'Editar ataque vinculado ao Inventário' : 'Editar ataque'"
                                    >
                                        <span
                                            class="character-attack-name block truncate font-serif text-[13px] font-black leading-tight text-[#53150f] hover:underline focus-visible:underline"
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
                                                @click.stop="openMastery(mastery, $event)"
                                                class="character-attack-mastery-chip shrink-0 rounded px-1.5 py-0.5 text-[6px] font-black uppercase tracking-[0.06em] text-[#6b1d14]"
                                                :title="'Ver maestria ' + mastery"
                                                x-text="mastery"
                                            ></button>
                                        </template>

                                        <button
                                            x-show="(row.masteries?.length ?? 0) > 2"
                                            x-cloak
                                            type="button"
                                            @click.stop="openMasteryGroup(row, $event)"
                                            class="shrink-0 rounded border border-[#cdbb9f]/55 bg-[#faf8f2] px-1.5 py-0.5 text-[6px] font-black text-[#8c6239]"
                                            :title="'Ver todas as ' + row.masteries.length + ' maestrias'"
                                            x-text="'+' + (row.masteries.length - 2)"
                                        ></button>
                                    </div>
                                </div>

                                <div x-show="row.range || row.source === 'weapon_item'" x-cloak
                                     class="character-attack-range mt-0.5 flex flex-wrap items-center gap-1 text-[10px] font-bold leading-none text-[#8c6239]">
                                    <span x-show="row.range">→</span>
                                    <span x-show="row.range" x-text="row.range"></span>

                                    {{-- V66.2.115: munição compacta, ao lado do alcance da arma. --}}
                                    <div x-show="row.source === 'weapon_item' && row.ammo" x-cloak
                                         class="character-attack-weapon-ammo"
                                         :class="{'is-empty': Number(row.ammo?.current ?? 0) === 0}"
                                         role="group"
                                         :aria-label="'Carregamento: ' + (row.ammo?.current ?? 0) + ' de ' + (row.ammo?.capacity ?? 0) + ' munições'">
                                        <span class="character-attack-weapon-ammo-icon" aria-hidden="true">
                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linejoin="round">
                                                <path d="M5.4 5 8 2.2 10.6 5v7.8H5.4z"/>
                                                <path d="M5.4 9.6h5.2"/>
                                            </svg>
                                        </span>
                                        <strong class="character-attack-weapon-ammo-count" aria-live="polite"
                                                x-text="(row.ammo?.current ?? 0) + '/' + (row.ammo?.capacity ?? 0)"></strong>
                                        <div class="character-attack-weapon-ammo-actions">
                                            <button type="button" @click.stop="changeWeaponAmmo(row, 'decrement')"
                                                    :disabled="weaponBusyId !== null || !(row.ammo?.current > 0)"
                                                    title="Gastar uma munição" aria-label="Gastar uma munição">−</button>
                                            <button type="button" @click.stop="changeWeaponAmmo(row, 'increment')"
                                                    :disabled="weaponBusyId !== null || !(row.ammo?.current < row.ammo?.capacity)"
                                                    title="Adicionar uma munição" aria-label="Adicionar uma munição">+</button>
                                            <button type="button" @click.stop="changeWeaponAmmo(row, 'reload')"
                                                    :disabled="weaponBusyId !== null || !(row.ammo?.current < row.ammo?.capacity)"
                                                    title="Recarregar arma" aria-label="Recarregar arma">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M20 11a8 8 0 1 0-2.5 6"/>
                                                    <path d="M20 4v7h-7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>


                        {{-- Acerto --}}

                        <td class="px-1 py-1 text-center align-middle">
                            <button
                                type="button"
                                @click="queueAttackSingleClick(row, $event)"
                                @dblclick.prevent.stop="queueAttackDoubleClick(row)"
                                @contextmenu.prevent.stop="queueAttackRightClick(row, $event)"
                                class="character-attack-hit character-attack-roll attack-v120-hit inline-flex w-full items-center justify-center rounded-md px-1 py-1 font-serif text-[16px] font-black text-[#6b1d14]"
                                :disabled="rolling || weaponBusyId !== null"
                                :title="attackHitHint(row) + ' · Duplo clique: vantagem · Duplo clique direito: desvantagem'"
                                :aria-label="attackHitHint(row) + '. Duplo clique: vantagem. Duplo clique direito: desvantagem.'"
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
                            <div class="attack-v145-damage-wrap">
                                <button
                                    type="button"
                                    @click="rollAllDamage(row)"
                                    :disabled="
                                        rolling
                                        || !row.damage_parts
                                        || row.damage_parts.length === 0
                                    "
                                    class="character-attack-damage-all attack-v145-damage-button block min-h-[32px] px-2 py-1 text-left"
                                    :class="isDamageCriticalReady(row) ? 'is-critical-ready' : ''"
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
                                                    class="attack-v145-damage-join mr-0.5 text-[9px] font-black text-[#b29161]"
                                                >
                                                    +
                                                </span>

                                                <strong
                                                    class="character-attack-damage-expression whitespace-nowrap font-serif text-[13px] leading-none text-[#53150f]"
                                                    x-text="damageExpressionForRow(row, part) || '—'"
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
                                <div x-cloak x-show="isDamageCriticalReady(row)" class="attack-v145-damage-prompt">
                                    <span class="attack-v147-crit-label">Crítico · dados dobrados</span>
                                    <span class="attack-v145-damage-pill" x-text="damageCriticalCountdownLabel(row)"></span>
                                    <button type="button" class="attack-v145-damage-close" @click.stop="dismissCriticalDamageWindow(row)" aria-label="Fechar aviso de dano crítico">×</button>
                                </div>
                            </div>
                        </td>


                        {{-- Observação com título/texto separados + tracker --}}

                        <td class="px-1.5 py-1 align-middle">
                            <div class="flex items-center gap-1.5">
                                <button
                                    type="button"
                                    @click="openNote(row, $event)"
                                    class="character-attack-note-button min-w-0 flex-1 rounded-md border border-transparent px-1.5 py-1 text-left"
                                    title="Abrir observação completa"
                                >
                                    <strong
                                        class="character-attack-observation-title text-[10px] font-black leading-tight text-[#53150f]"
                                        x-text="row.source === 'weapon_item'
                                            ? ((row.weapon_features?.length || 0) + (row.weapon_traits?.length || 0) + (row.weapon_mastery_details?.length || 0)
                                               ? 'Habilidades e propriedades' : 'Arma equipada')
                                            : (row.effect || (row.description ? 'Observação' : 'Sem observações'))"
                                    ></strong>

                                    <span
                                        class="character-attack-observation-text mt-0.5 text-[8.5px] font-medium leading-[1.3] text-[#6b5548]"
                                        x-text="notePreview(row)"
                                    ></span>
                                </button>

                                <div x-show="row.source === 'weapon_item' && !!row.feature_trackers?.length" x-cloak
                                     class="character-attack-weapon-trackers">
                                    <template x-for="tracker in (row.feature_trackers ?? [])" :key="tracker.index">
                                        <div class="character-attack-weapon-tracker" :title="tracker.title">
                                            <span class="character-attack-weapon-tracker-label" x-text="tracker.title"></span>
                                            <button type="button" :disabled="weaponBusyId !== null || tracker.current <= 0"
                                                    @click.stop="changeWeaponFeatureUses(row, tracker, -1)" aria-label="Gastar uso">−</button>
                                            <strong x-text="tracker.current + '/' + tracker.max"></strong>
                                            <button type="button" :disabled="weaponBusyId !== null || tracker.current >= tracker.max"
                                                    @click.stop="changeWeaponFeatureUses(row, tracker, 1)" aria-label="Recuperar uso">+</button>
                                        </div>
                                    </template>
                                </div>
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


    {{-- V66.2.113 — leitura contextual das habilidades, próxima ao clique. --}}
    <template x-teleport="body">
        <div x-show="noteDrawerOpen" x-cloak data-theme="{{ $characterTheme }}"
             class="character-sheet-theme fixed inset-0 z-[185] pointer-events-none">
            <aside x-show="noteDrawerOpen" x-ref="attackNotePanel"
                   x-transition:enter="transform transition ease-out duration-200"
                   x-transition:enter-start="translate-x-[105%] opacity-0"
                   x-transition:enter-end="translate-x-0 opacity-100"
                   x-transition:leave="transform transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0 opacity-100"
                   x-transition:leave-end="translate-x-[105%] opacity-0"
                   @click.outside="closeNote()"
                   @keydown.escape.window="if(noteDrawerOpen) closeNote()"
                   @pointermove.window="moveNoteDrag($event)"
                   @pointerup.window="endNoteDrag($event)"
                   @pointercancel.window="endNoteDrag($event)"
                   @blur.window="endNoteDrag()"
                   :style="'top:' + notePosition.top + 'px;left:' + notePosition.left + 'px'"
                   class="character-theme-modal character-attack-note-card pointer-events-auto absolute flex flex-col overflow-hidden rounded-xl border shadow-2xl">
                <header class="character-attack-note-head flex shrink-0 items-center justify-between gap-3 border-b px-4 py-3"
                        :class="{'is-dragging': !!noteDrag}"
                        title="Arraste para mover esta janela"
                        @pointerdown="startNoteDrag($event)">
                    <div class="min-w-0">
                        <h3 class="character-attack-note-title truncate font-serif font-black"
                            x-text="noteRow?.name || 'Habilidades'"></h3>
                    </div>
                    <button type="button" @click="closeNote()" aria-label="Fechar detalhes"
                            class="character-attack-note-close inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg">×</button>
                </header>
                <div class="character-attack-note-body min-h-0 flex-1 overflow-y-auto p-3 sm:p-4">
                    <template x-if="noteRow?.source === 'weapon_item'">
                        <div class="space-y-3">
                            <div x-show="(noteRow?.weapon_features?.length ?? 0) > 0">
                                <div class="character-attack-note-section-label">HABILIDADES DA ARMA</div>
                                <div class="character-attack-note-entry-list">
                                    <template x-for="(feature, index) in (noteRow?.weapon_features ?? [])" :key="'f-'+index">
                                        <article class="character-attack-note-entry">
                                            <div class="flex items-start justify-between gap-2">
                                                <h4 x-text="feature.title" class="character-attack-note-entry-title"></h4>
                                                <span x-show="feature.tracker_index !== null && feature.tracker_index !== undefined"
                                                      class="character-attack-note-entry-tag">Rastreador</span>
                                            </div>
                                            <p x-show="feature.description" x-text="feature.description" class="character-attack-note-entry-copy"></p>
                                        </article>
                                    </template>
                                </div>
                            </div>
                            <div x-show="(noteRow?.weapon_traits?.length ?? 0) > 0">
                                <div class="character-attack-note-section-label">PROPRIEDADES DA ARMA</div>
                                <div class="character-attack-note-entry-list">
                                    <template x-for="(trait, index) in (noteRow?.weapon_traits ?? [])" :key="'t-'+index">
                                        <article class="character-attack-note-entry">
                                            <h4 x-text="trait.title" class="character-attack-note-entry-title"></h4>
                                            <p x-show="trait.description" x-text="trait.description" class="character-attack-note-entry-copy"></p>
                                        </article>
                                    </template>
                                </div>
                            </div>
                            <div x-show="(noteRow?.weapon_mastery_details?.length ?? 0) > 0">
                                <div class="character-attack-note-section-label">MAESTRIAS</div>
                                <div class="character-attack-note-entry-list">
                                    <template x-for="(mastery, index) in (noteRow?.weapon_mastery_details ?? [])" :key="'m-'+index">
                                        <article class="character-attack-note-entry">
                                            <h4 x-text="mastery.title" class="character-attack-note-entry-title"></h4>
                                            <p x-show="mastery.description" x-text="mastery.description" class="character-attack-note-entry-copy"></p>
                                        </article>
                                    </template>
                                </div>
                            </div>
                            <p class="character-attack-note-empty"
                               x-show="!(noteRow?.weapon_features?.length || noteRow?.weapon_traits?.length || noteRow?.weapon_mastery_details?.length)">
                                Nenhuma habilidade ou propriedade adicional cadastrada nesta arma.
                            </p>
                        </div>
                    </template>
                    <template x-if="noteRow?.source !== 'weapon_item'">
                        <div class="space-y-3">
                            <div x-show="noteRow?.effect" class="character-attack-note-entry">
                                <h4 class="character-attack-note-entry-title" x-text="noteRow?.effect"></h4>
                            </div>
                            <template x-for="(paragraph,index) in noteParagraphs()" :key="index">
                                <p x-text="paragraph" class="character-attack-note-entry-copy whitespace-pre-line"></p>
                            </template>
                            <p x-show="!noteRow?.effect && !noteParagraphs().length" class="character-attack-note-empty">Sem observações adicionais.</p>
                        </div>
                    </template>
                </div>
            </aside>
        </div>
    </template>

    {{-- =========================================================
         Maestrias — mesmo formato, arraste e animação das Observações
    ========================================================== --}}

    <template x-teleport="body">
        <div x-show="masteryDrawerOpen" x-cloak data-theme="{{ $characterTheme }}"
             class="character-sheet-theme fixed inset-0 z-[186] pointer-events-none">
            <aside x-show="masteryDrawerOpen" x-ref="attackMasteryPanel"
                   x-transition:enter="transform transition ease-out duration-200"
                   x-transition:enter-start="translate-x-[105%] opacity-0"
                   x-transition:enter-end="translate-x-0 opacity-100"
                   x-transition:leave="transform transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0 opacity-100"
                   x-transition:leave-end="translate-x-[105%] opacity-0"
                   @click.outside="closeMastery()"
                   @keydown.escape.window="if(masteryDrawerOpen) closeMastery()"
                   @pointermove.window="moveMasteryDrag($event)"
                   @pointerup.window="endMasteryDrag($event)"
                   @pointercancel.window="endMasteryDrag($event)"
                   @blur.window="endMasteryDrag()"
                   :style="'top:' + masteryPosition.top + 'px;left:' + masteryPosition.left + 'px'"
                   class="character-theme-modal character-attack-note-card pointer-events-auto absolute flex flex-col overflow-hidden rounded-xl border shadow-2xl">
                <header class="character-attack-note-head flex shrink-0 items-center justify-between gap-3 border-b px-4 py-3"
                        :class="{'is-dragging': !!masteryDrag}"
                        title="Arraste para mover esta janela"
                        @pointerdown="startMasteryDrag($event)">
                    <h3 class="character-attack-note-title min-w-0 truncate font-serif font-black"
                        x-text="masteryInfo?.name || 'Maestria'"></h3>
                    <button type="button" @click="closeMastery()" aria-label="Fechar maestria"
                            class="character-attack-note-close inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg">×</button>
                </header>
                <div class="character-attack-note-body min-h-0 flex-1 overflow-y-auto p-3 sm:p-4">
                    <article x-show="!masteryInfo?.items" class="character-attack-note-entry">
                        <p class="character-attack-note-entry-copy character-attack-mastery-copy"
                           x-text="masteryInfo?.description || 'Descrição não disponível.'"></p>
                    </article>
                    <div x-show="masteryInfo?.items" x-cloak class="character-attack-note-entry-list">
                        <template x-for="item in (masteryInfo?.items ?? [])" :key="item.name">
                            <article class="character-attack-note-entry">
                                <h4 class="character-attack-note-entry-title" x-text="item.name"></h4>
                                <p class="character-attack-note-entry-copy" x-text="item.description"></p>
                            </article>
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


    {{-- V66.2.139 — Dados voando na tela; somente uma barra compacta de resultado. --}}
    <template x-teleport="body">
        <div x-show="rollToastOpen" x-cloak
             x-transition.opacity.duration.180ms
             data-theme="{{ $characterTheme }}"
             class="character-sheet-theme attack-v139-scene"
             :class="{'is-free-manual':rollMode==='free','is-death-save':rollMode==='death'}"
             :data-motion-owner="rollMotionOwner"
             aria-label="Animação de rolagem">
            <template x-for="sequence in [rollSequenceId]" :key="sequence">
                <div>
                    {{-- Dados independentes: entram de lados alternados, quicam e revelam as faces do servidor. --}}
                    <div x-show="rollStage !== 'error' && !(rollMode === 'free' && rollManualDiceTransferred)" class="attack-v139-field" aria-hidden="true">
                        <template x-for="entry in visibleRollDice()" :key="entry.die.id">
                            <div class="attack-v139-die"
                                 :data-motion-index="entry.index"
                                 :data-sides="entry.die.sides"
                                 :style="rollDieTravelStyle(entry)"
                                 :class="rollDieVisualClass(entry.die)"
                                 :title="entry.die.sides === 20 && (rollMode === 'attack' || sharedRollReplay) ? 'Duplo clique: vantagem · Duplo clique direito: desvantagem' : ''"
                                 @dblclick.stop.prevent="rollAgainFromDie('advantage')"
                                 @contextmenu.stop.prevent="handleDieRightClick($event)">
                                <template x-if="entry.die.landed && ['attack','check','initiative','death','duration'].includes(rollMode) && entry.die.sides === 20 && entry.die.selected && (entry.die.value === 20 || entry.die.value === 1)">
                                    <span class="attack-v147-motes" aria-hidden="true">
                                        <i class="attack-v147-mote"></i><i class="attack-v147-mote"></i>
                                        <i class="attack-v147-mote"></i><i class="attack-v147-mote"></i>
                                        <i class="attack-v147-mote"></i><i class="attack-v147-mote"></i>
                                        <i class="attack-v147-mote"></i><i class="attack-v147-mote"></i>
                                    </span>
                                </template>
                                @foreach([4,6,8,10,12,20,100] as $sides)
                                    <template x-if="entry.die.sides === {{ $sides }}">
                                        <x-ui.poly-die :sides="$sides" size="64"
                                            x-bind:class="entry.die.landed ? 'is-landed' : 'is-rolling'">
                                            <span x-text="entry.die.display"></span>
                                        </x-ui.poly-die>
                                    </template>
                                @endforeach
                                <template x-if="![4,6,8,10,12,20,100].includes(entry.die.sides)">
                                    <span class="attack-v139-die-fallback" x-text="entry.die.display"></span>
                                </template>
                            </div>
                        </template>
                        <span x-show="hiddenRollDiceCount() > 0" class="attack-v139-more"
                              x-text="'+' + hiddenRollDiceCount() + ' dados'"></span>
                    </div>

                    {{-- Resumo de leitura, sem cartões individuais nem rolagem em modal alto. --}}
                    <div class="attack-v139-hud" role="status" aria-live="polite">
                        <div class="attack-v139-hud-head">
                            <div class="attack-v139-hud-meta">
                                <span class="attack-v139-kicker"
                                      x-text="({attack:'Acerto',damage:'Dano',check:'Teste',initiative:'Iniciativa',duration:'Duração',death:'Salvaguarda',free:'Dado livre'})[rollMode] || 'Rolagem'"></span>
                                <strong class="attack-v139-title" :title="rollTitle" x-text="rollTitle || 'Rolagem'"></strong>
                                <span class="attack-v139-status" x-show="rollStage !== 'result' && rollStage !== 'error'"
                                      x-text="rollStage === 'revealing' ? 'Revelando dados…' : 'Dados em movimento…'"></span>
                                <span class="attack-v139-status attack-v146-roll-mode" x-cloak
                                      x-show="rollStage === 'result' && lastRoll?.advantageMode && lastRoll.advantageMode !== 'normal'"
                                      x-text="(lastRoll?.advantageMode === 'advantage' ? 'Vantagem' : 'Desvantagem') + ' · [' + (lastRoll?.rolls ?? []).join(' / ') + '] → ' + (lastRoll?.natural ?? '—')"></span>
                                <span class="attack-v139-status is-critical" x-cloak
                                      x-show="rollStage === 'result' && rollMode === 'attack' && lastRoll?.natural === 20">20 natural · Crítico!</span>
                                <span class="attack-v139-status is-fumble" x-cloak
                                      x-show="rollStage === 'result' && rollMode === 'attack' && lastRoll?.natural === 1">1 natural · Falha crítica</span>
                                <span class="attack-v139-status" x-cloak
                                      x-show="rollStage === 'result' && (lastRoll?.exhaustionPenalty ?? 0) > 0"
                                      x-text="'Exaustão: -' + (lastRoll?.exhaustionPenalty || 0)"></span>
                            </div>
                            <div class="attack-v139-outcome" x-show="rollStage !== 'error'"
                                 :class="rollStage === 'result' ? 'is-ready' : ''">
                                <small x-text="rollStage === 'result' ? 'Resultado' : 'Rolando…'"></small>
                                <strong x-text="rollStage === 'result' ? (lastRoll?.total ?? '—') : '· · ·'"></strong>
                            </div>
                            <button type="button" class="attack-v139-close" @click="rollToastOpen = false"
                                    aria-label="Fechar rolagem">×</button>
                        </div>
                        <div x-show="rollStage === 'result' && ['damage','free'].includes(rollMode) && rollGroups.length > 1"
                             x-cloak class="attack-v139-breakdown" :aria-label="rollMode === 'free' ? 'Resultado de cada dado' : 'Dano por tipo'">
                            <template x-for="group in rollGroups" :key="group.index">
                                <span class="attack-v139-part">
                                    <span x-text="group.label"></span>
                                    <strong x-text="group.total ?? '—'"></strong>
                                </span>
                            </template>
                        </div>
                        <p x-show="rollStage === 'error'" x-cloak class="attack-v139-error"
                           x-text="rollError || 'Não foi possível rolar os dados.'"></p>
                    </div>
                </div>
            </template>
        </div>
    </template>


    {{-- V66.2.116 — Confirmação de atributo ao equipar (não é editor). --}}
    <template x-teleport="body">
        <div x-show="weaponEquipOpen" x-cloak
             x-transition.opacity.duration.130ms
             data-theme="{{ $characterTheme }}"
             class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[280] flex items-center justify-center p-4"
             role="dialog" aria-modal="true" aria-label="Escolher atributo da arma">
            <div class="character-theme-modal-backdrop absolute inset-0 backdrop-blur-[5px]"
                 @click="closeWeaponEquip()"></div>
            <div class="character-theme-modal relative z-10 w-full max-w-[450px] overflow-hidden rounded-xl border shadow-2xl"
                 style="background:var(--theme-modal-surface,var(--theme-surface-card));border-color:var(--theme-modal-border,var(--theme-border));color:var(--theme-modal-text,var(--theme-text-primary))"
                 @click.stop>
                <header class="flex items-center justify-between gap-3 border-b px-4 py-3"
                        style="background:var(--theme-modal-header-background,var(--theme-band-background));border-color:var(--theme-modal-border,var(--theme-border))">
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold uppercase tracking-wider" style="color:var(--theme-text-secondary)">Equipar arma</p>
                        <h3 class="truncate font-serif text-lg font-black leading-tight" style="color:var(--theme-text-strong)"
                            x-text="weaponEquipItem?.name || 'Arma'"></h3>
                    </div>
                    <button type="button" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border text-base"
                            style="border-color:var(--theme-border);color:var(--theme-text-secondary)"
                            :disabled="weaponEquipBusy" aria-label="Cancelar" @click="closeWeaponEquip()">×</button>
                </header>
                <div class="p-4">
                    <p class="mb-3 text-[12px] leading-relaxed" style="color:var(--theme-text-secondary)">
                        <span x-text="weaponEquipAllowed.length > 1 ? 'Qual atributo deseja usar no ataque e dano?' : 'Atributo permitido para esta arma:'"></span>
                    </p>
                    <div class="grid gap-2" :class="weaponEquipAllowed.length > 1 ? 'grid-cols-2' : 'grid-cols-1'">
                        <template x-for="ability in weaponEquipAllowed" :key="ability">
                            <button type="button" class="flex min-h-[58px] flex-col items-center justify-center gap-1 rounded-lg border px-3 py-2.5 transition"
                                    style="background:var(--theme-surface-raised);border-color:var(--theme-border-strong);color:var(--theme-text-strong)"
                                    :disabled="weaponEquipBusy" @click="confirmWeaponEquip(ability)"
                                    @mouseenter="$el.style.borderColor='var(--theme-accent)'"
                                    @mouseleave="$el.style.borderColor='var(--theme-border-strong)'">
                                <strong class="font-serif text-[17px]" x-text="ability === 'strength' ? 'FOR' : ability === 'dexterity' ? 'DES' : '—'"></strong>
                                <span class="text-[10px]" style="color:var(--theme-text-secondary)"
                                      x-text="ability === 'strength' ? 'Força' : ability === 'dexterity' ? 'Destreza' : 'Sem modificador'"></span>
                            </button>
                        </template>
                    </div>
                    <p x-show="weaponEquipError" x-cloak class="mt-3 text-[11px] leading-relaxed"
                       style="color:var(--theme-state-damage,var(--theme-text-strong))" x-text="weaponEquipError"></p>
                    <p x-show="weaponEquipBusy" x-cloak class="mt-3 text-center text-[11px]" style="color:var(--theme-text-secondary)">Equipando...</p>
                </div>
                <footer class="flex justify-end border-t px-4 py-2.5" style="border-color:var(--theme-modal-border,var(--theme-border))">
                    <button type="button" class="rounded-md border px-3 py-1.5 text-[11px] font-bold"
                            style="border-color:var(--theme-border-strong);color:var(--theme-text-primary)"
                            :disabled="weaponEquipBusy" @click="closeWeaponEquip()">Cancelar</button>
                </footer>
            </div>
        </div>
    </template>

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
                class="character-theme-modal character-attack-editor character-attack-unified relative z-10 flex max-h-[92dvh] w-full max-w-[880px] flex-col overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#faf8f2] shadow-2xl"
            >
                <div class="character-theme-modal-header character-attack-editor-header shrink-0 border-b border-[#a0774d]/30 bg-[#eadbc8] shadow-[inset_0_1px_0_rgba(255,255,255,.72)]">
                    <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                        <div>
                            <p class="character-attack-editor-kicker text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Ataques</p>
                            <h3 class="character-attack-editor-title mt-0.5 font-serif text-lg font-black leading-tight text-[#53150f]"
                                x-text="editingItemId !== null || editingId !== null ? 'Editar ataque' : 'Criar ataque'"></h3>
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
                            Acerto e Danos
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
                            Efeitos e Recursos
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
                                        x-model="form.attack_ability" @change="syncLinkedDamageAbility()"
                                        class="mt-1 w-full rounded-lg border border-[#cdbb9f] bg-white px-2.5 py-2 text-[13px] font-bold text-[#53150f]"
                                    >
                                        <template x-if="editingItemId === null"><option value="">Nenhum</option></template>
                                        <template x-if="editingItemId !== null && allowedAttackAbilities.includes('none')"><option value="none">Sem atributo</option></template>
                                        @foreach ($abilityLabels as $key => $label)
                                        <template x-if="editingItemId === null || allowedAttackAbilities.includes('{{ $key }}')">
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        </template>
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
                                    x-model="form.use_proficiency" :disabled="editingItemId !== null"
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
                        <section x-show="editingItemId !== null" x-cloak class="character-attack-modal-card character-attack-source-summary p-3.5">
                            <strong class="character-attack-source-heading">Mecânicas da arma equipada</strong>
                            <p class="character-attack-source-help">As habilidades e rastreadores abaixo são vinculados ao Inventário e não são duplicados.</p>
                            <template x-for="(entry,index) in (editingWeaponSource?.weapon_features ?? [])" :key="'source-'+index">
                                <article class="character-attack-source-feature">
                                    <strong x-text="entry.title"></strong>
                                    <p x-text="entry.description || 'Sem descrição adicional.'"></p>
                                    <small x-show="entry.tracker_index !== null">Rastreador sincronizado com o Inventário</small>
                                </article>
                            </template>
                            <template x-for="(entry,index) in (editingWeaponSource?.weapon_traits ?? [])" :key="'property-'+index">
                                <article class="character-attack-source-feature">
                                    <strong x-text="entry.title"></strong>
                                    <p x-text="entry.description || 'Propriedade da arma.'"></p>
                                </article>
                            </template>
                            <template x-for="(entry,index) in (editingWeaponSource?.weapon_mastery_details ?? [])" :key="'mastery-'+index">
                                <article class="character-attack-source-feature">
                                    <strong x-text="entry.title"></strong>
                                    <p x-text="entry.description || 'Maestria vinculada ao item.'"></p>
                                </article>
                            </template>
                            <p x-show="editingWeaponSource?.ammo" class="character-attack-source-help">
                                Carregamento: <strong x-text="(editingWeaponSource?.ammo?.current ?? 0) + '/' + (editingWeaponSource?.ammo?.capacity ?? 0)"></strong> · Ajuste a munição diretamente na linha do ataque.
                            </p>
                        </section>

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


                        {{-- Rastreador — equipamentos usam rastreadores do Inventário --}}

                        <section x-show="editingItemId === null" class="character-attack-modal-card p-3.5">
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
                                    x-model="form.counter_enabled" :disabled="editingItemId !== null"
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


                        <section x-show="editingItemId === null" class="character-attack-modal-card p-3.5">
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
                                    x-model="form.visible" :disabled="editingItemId !== null"
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
                        x-show="editingId !== null && editingItemId === null"
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

{{-- Controle global da ficha, isolado da tabela de Ataques. --}}
@include('characters.components.dice-tray', ['characterTheme' => $characterTheme])
