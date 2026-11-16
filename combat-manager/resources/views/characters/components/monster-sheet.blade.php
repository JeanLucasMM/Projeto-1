@props([
    'character',
    'transformation',
    'state',
    'npc',
    'sheetData' => null,
    'unsupportedFormat' => false,
])

@php
    /*
    |--------------------------------------------------------------------------
    | MONSTER SHEET — V18
    |--------------------------------------------------------------------------
    |
    | Esta Blade NÃO interpreta JSON de NPC.
    |
    | MonsterTransformationSheetResolver já converte:
    | - Native / npc-builder
    | - Normal / 5emm
    |
    | para o mesmo contrato de renderização.
    |--------------------------------------------------------------------------
    */

    $sheet =
        is_array(
            $sheetData
        )
            ? $sheetData
            : [];


    /*
    |--------------------------------------------------------------------------
    | TEMA VISUAL DA FICHA TRANSFORMADA
    |--------------------------------------------------------------------------
    |
    | Este componente é teleportado para <body>. Reaplicamos o tema do
    | Character original no portal para manter os mesmos tokens --theme-*.
    |
    */
    $characterTheme =
        trim(
            (string) data_get(
                $character?->sheet_settings,
                'appearance.theme',
                'spellbound'
            )
        );

    if ($characterTheme === '') {
        $characterTheme = 'spellbound';
    }

    $characterTheme =
        preg_replace(
            '/[^a-z0-9_-]/i',
            '',
            $characterTheme
        )
        ?: 'spellbound';

    $monsterState =
        is_array(
            $state?->monster_state
        )
            ? $state->monster_state
            : [];

    $sheetMaxHp =
        max(
            1,
            (int) (
                $sheet['max_hp']
                ?? 1
            )
        );

    $storedMaxHp =
        isset(
            $monsterState['max_hp']
        )
            ? (int) $monsterState['max_hp']
            : null;

    /*
     * Recuperação visual de estados antigos criados quando uma forma Native
     * ainda era interpretada como 1 PV.
     */
    $monsterMaxHp =
        (
            $storedMaxHp !== null
            && $storedMaxHp > 1
        )
            ? max(
                1,
                $storedMaxHp
            )
            : $sheetMaxHp;

    $storedCurrentHp =
        isset(
            $monsterState['current_hp']
        )
            ? (int) $monsterState['current_hp']
            : null;

    $monsterCurrentHp =
        (
            $storedMaxHp !== null
            && $storedMaxHp <= 1
            && $sheetMaxHp > 1
            && $storedCurrentHp !== null
            && $storedCurrentHp <= 1
        )
            ? $sheetMaxHp
            : max(
                0,
                min(
                    $monsterMaxHp,
                    $storedCurrentHp
                    ?? $monsterMaxHp
                )
            );

    $monsterTemporaryHp =
        max(
            0,
            (int) (
                $monsterState['temporary_hp']
                ?? 0
            )
        );

    $monsterName =
        trim(
            (string) (
                $sheet['name']
                ?? $npc?->name
                ?? 'Forma de Monstro'
            )
        );

    $monsterSubtitle =
        trim(
            (string) (
                $sheet['subtitle']
                ?? ''
            )
        );

    $armorClass =
        (int) (
            $sheet['armor_class']
            ?? 10
        );

    $initiative =
        (int) (
            $sheet['initiative']
            ?? 0
        );

    $speed =
        trim(
            (string) (
                $sheet['speed']
                ?? '—'
            )
        );

    $jump =
        is_array(
            $sheet['jump']
            ?? null
        )
            ? $sheet['jump']
            : null;

    $jumpText =
        trim(
            (string) (
                $jump['text']
                ?? ''
            )
        );

    $challengeRating =
        $sheet['challenge_rating']
        ?? null;

    $proficiencyBonus =
        isset(
            $sheet['proficiency_bonus']
        )
            ? (int) $sheet['proficiency_bonus']
            : null;

    $abilities =
        collect(
            $sheet['abilities']
            ?? []
        )
            ->keyBy('key');

    $abilityColumns = [
        [
            $abilities->get('str'),
            $abilities->get('int'),
        ],

        [
            $abilities->get('dex'),
            $abilities->get('wis'),
        ],

        [
            $abilities->get('con'),
            $abilities->get('cha'),
        ],
    ];

    $details =
        collect(
            $sheet['details']
            ?? []
        )
            ->filter(
                fn ($detail) =>
                    is_array(
                        $detail
                    )
                    && trim(
                        (string) (
                            $detail['label']
                            ?? ''
                        )
                    )
                    !== ''
                    && trim(
                        (string) (
                            $detail['value']
                            ?? ''
                        )
                    )
                    !== ''
            )
            ->values();

    $sections =
        collect(
            $sheet['sections']
            ?? []
        )
            ->filter(
                fn ($section) =>
                    is_array(
                        $section
                    )
                    && !empty(
                        $section['entries']
                        ?? []
                    )
            )
            ->values();

    $signed =
        fn ($value): string =>
            (
                (int) $value >= 0
                    ? '+'
                    : ''
            )
            . (int) $value;

    $castingTimeLabels = [
        'none' =>
            'Sem ação definida',

        'spell' =>
            'Feitiço',

        'bonus_action' =>
            'Ação Bônus',

        'action' =>
            'Ação Padrão',
    ];

    $castingTimeLabel =
        $castingTimeLabels[
            $transformation?->casting_time
            ?? 'none'
        ]
        ?? 'Transformação';

    /*
    |--------------------------------------------------------------------------
    | Retrato
    |--------------------------------------------------------------------------
    |
    | 1. Foto própria da transformação.
    | 2. Foto do NPC do Cofre.
    | 3. Monograma.
    |
    | Nunca usa a foto original do Character.
    |--------------------------------------------------------------------------
    */
    $imagePath =
        $transformation?->image_path
        ?: (
            $sheet['image_path']
            ?? $npc?->image_path
            ?? null
        );

    $imageUrl =
        null;

    if (
        is_string(
            $imagePath
        )
        && trim(
            $imagePath
        )
        !== ''
    ) {
        $imagePath =
            trim(
                $imagePath
            );

        if (
            str_starts_with(
                $imagePath,
                'http://'
            )
            || str_starts_with(
                $imagePath,
                'https://'
            )
            || str_starts_with(
                $imagePath,
                '/storage/'
            )
        ) {
            $imageUrl =
                $imagePath;

        } else {
            $imageUrl =
                asset(
                    'storage/'
                    . ltrim(
                        $imagePath,
                        '/'
                    )
                );
        }
    }

    $initials =
        mb_strtoupper(
            mb_substr(
                preg_replace(
                    '/\s+/u',
                    '',
                    $monsterName
                ),
                0,
                2
            )
        );

    $balanceId =
        'monster-transformation-sheet-'
        . (
            $transformation?->id
            ?? 'active'
        );


    /*
    |--------------------------------------------------------------------------
    | CACHE TEMPORÁRIO DOS TRACKERS
    |--------------------------------------------------------------------------
    |
    | O activated_at muda a cada nova transformação, portanto os trackers
    | começam limpos em uma nova ativação, mas sobrevivem a reloads durante
    | a transformação atual.
    |--------------------------------------------------------------------------
    */
    $trackerSessionToken =
        sha1(
            implode(
                '|',
                [
                    (string) (
                        $character?->id
                        ?? 0
                    ),

                    (string) (
                        $transformation?->id
                        ?? 0
                    ),

                    (string) (
                        $state?->activated_at
                        ?? $state?->updated_at
                        ?? 'active'
                    ),
                ]
            )
        );

    $trackerNamespace =
        'spellbound:monster-transform-tracker:'
        . (
            $character?->id
            ?? 0
        )
        . ':'
        . (
            $transformation?->id
            ?? 0
        )
        . ':'
        . $trackerSessionToken;
@endphp


@once
    <style>
        /*
        |--------------------------------------------------------------------------
        | OVERLAY
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-overlay {
            --ms-paper: var(--theme-paper);
            --ms-paper-soft: var(--theme-paper-strong);
            --ms-ink: var(--theme-text-primary);
            --ms-primary: var(--theme-accent-strong);
            --ms-primary-dark: var(--theme-button-hover);
            --ms-accent: var(--theme-label);
            --ms-muted: var(--theme-text-secondary);
            --ms-border: var(--theme-border);
            --ms-border-soft: var(--theme-border-soft);
            --ms-row-a: var(--theme-surface-card);
            --ms-row-b: var(--theme-surface-card);

            position: fixed;
            inset: 0;
            z-index: 2147483000;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 15px 66px;

            font-family:
                Georgia,
                Cambria,
                "Times New Roman",
                Times,
                serif;
        }

        .monster-sheet-v18-overlay,
        .monster-sheet-v18-overlay * {
            box-sizing: border-box;
        }

        .monster-sheet-v18-backdrop {
            position: absolute;
            inset: 0;

            background:
                rgba(26,18,15,.77);

            backdrop-filter:
                blur(6px);
        }

        .monster-sheet-v18-shell {
            position: relative;
            z-index: 2;

            width:
                min(
                    1160px,
                    calc(100vw - 132px)
                );

            max-height:
                calc(100vh - 30px);
        }

        .monster-sheet-v18-panel {
            max-height:
                calc(100vh - 30px);

            overflow-y: auto;
            overflow-x: hidden;

            border:
                2px solid
                var(--ms-primary);

            border-radius:
                16px;

            background:
                var(--ms-paper);

            color:
                var(--ms-ink);

            box-shadow:
                0 28px 74px
                rgba(18,10,8,.38);

            scrollbar-width: thin;
            scrollbar-color:
                var(--ms-primary)
                transparent;
        }

        .monster-sheet-v18-panel::-webkit-scrollbar {
            width: 6px;
        }

        .monster-sheet-v18-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .monster-sheet-v18-panel::-webkit-scrollbar-thumb {
            border-radius:
                999px;

            background:
                var(--ms-primary);
        }


        /*
        |--------------------------------------------------------------------------
        | DESTRANSFORMAR
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-untransform {
            position: absolute;
            top: 50%;
            left: -46px;
            z-index: 5;

            display: flex;

            width: 40px;
            min-height: 142px;

            align-items: center;
            justify-content: center;

            transform:
                translateY(-50%);

            border:
                1px solid
                rgba(244,241,232,.66);

            border-right: 0;

            border-radius:
                10px 0 0 10px;

            background:
                var(--ms-primary);

            color:
                #fffaf2;

            box-shadow:
                0 10px 28px
                rgba(20,10,8,.28);

            transition:
                transform .16s ease,
                background .16s ease;
        }

        .monster-sheet-v18-untransform:hover {
            transform:
                translateY(-50%)
                translateX(-2px);

            background:
                var(--ms-primary-dark);
        }

        .monster-sheet-v18-untransform:disabled {
            cursor: wait;
            opacity: .5;
        }

        .monster-sheet-v18-untransform span {
            writing-mode:
                vertical-rl;

            transform:
                rotate(180deg);

            font-size: 9px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
        }


        /*
        |--------------------------------------------------------------------------
        | DUAS COLUNAS FÍSICAS
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-sheet {
            position: relative;

            overflow: hidden;

            background:
                var(--ms-paper-soft);
        }

        .monster-sheet-v18-columns {
            display: grid;

            grid-template-columns:
                minmax(0,1fr)
                minmax(0,1fr);

            align-items: stretch;
        }

        .monster-sheet-v18-column {
            min-width: 0;

            padding:
                14px 18px 18px;
        }

        .monster-sheet-v18-column--right {
            border-left:
                1px solid
                var(--ms-primary);
        }

        .monster-sheet-v18-fixed,
        .monster-sheet-v18-dynamic {
            min-width: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | IDENTIDADE
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-identity {
            display: grid;

            grid-template-columns:
                minmax(0,1fr)
                78px;

            gap: 12px;
            align-items: start;

            margin-bottom: 5px;
        }

        .monster-sheet-v18-kicker {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;

            margin-bottom: 5px;
        }

        .monster-sheet-v18-form-name {
            color:
                var(--ms-accent);

            font-size: 8px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .monster-sheet-v18-pill {
            display: inline-flex;

            min-height: 18px;

            align-items: center;

            border:
                1px solid
                rgba(147,98,61,.28);

            border-radius:
                999px;

            background:
                #f2e9da;

            padding:
                0 7px;

            color:
                #75513a;

            font-size: 7px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .monster-sheet-v18-title {
            margin: 0;

            color:
                var(--ms-primary);

            font-size: 27px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .monster-sheet-v18-subtitle {
            margin:
                3px 0 0;

            color:
                var(--ms-muted);

            font-size: 13.5px;
            font-style: italic;
            line-height: 1.22;
        }

        .monster-sheet-v18-portrait {
            display: flex;

            width: 78px;
            height: 88px;

            align-items: center;
            justify-content: center;

            overflow: hidden;

            border:
                1px solid
                rgba(139,39,27,.38);

            border-radius:
                9px;

            background:
                #efe6d8;

            color:
                var(--ms-primary);

            box-shadow:
                0 2px 7px
                rgba(53,30,23,.12);

            font-size: 18px;
            font-weight: 900;
        }

        .monster-sheet-v18-portrait img {
            width: 100%;
            height: 100%;

            object-fit: cover;
            object-position: top center;
        }

        .monster-sheet-v18-rule {
            margin:
                7px 0;

            border: 0;

            border-top:
                1px solid
                var(--ms-primary);
        }


        /*
        |--------------------------------------------------------------------------
        | NÚCLEO MECÂNICO
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-core-line {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;

            column-gap: 5px;
            row-gap: 1px;

            min-height: 21px;

            color:
                var(--ms-ink);

            font-size: 13.5px;
            line-height: 1.32;
        }

        .monster-sheet-v18-core-line strong {
            color:
                var(--ms-primary);

            font-weight: 900;
        }

        .monster-sheet-v18-separator {
            color:
                rgba(107,29,20,.42);
        }


        /*
        |--------------------------------------------------------------------------
        | VIDA COMPACTA
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | PONTOS DE VIDA — LEITURA CRUA DE STATBLOCK
        |--------------------------------------------------------------------------
        |
        | Nada de barra de progresso ou controles +/- permanentes.
        | O valor é apresentado como informação da ficha e abre um pequeno
        | editor manual somente quando clicado.
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-hp-wrap {
            position: relative;

            width: fit-content;
            max-width: 100%;

            margin:
                1px 0 5px;
        }

        .monster-sheet-v18-hp-summary {
            display: inline-flex;

            align-items: baseline;
            flex-wrap: wrap;

            gap: 5px;

            min-height: 21px;

            border: 0;

            background: transparent;

            padding: 0;

            color:
                var(--ms-ink);

            font-family: inherit;
            font-size: 13.5px;
            line-height: 1.32;

            text-align: left;

            cursor: pointer;

            transition:
                color .12s ease;
        }

        .monster-sheet-v18-hp-summary:hover,
        .monster-sheet-v18-hp-summary[aria-expanded="true"] {
            color:
                var(--ms-primary);
        }

        .monster-sheet-v18-hp-summary:focus-visible {
            outline:
                1px solid
                rgba(107,29,20,.48);

            outline-offset:
                3px;

            border-radius:
                2px;
        }

        .monster-sheet-v18-hp-label {
            color:
                var(--ms-primary);

            font-weight: 900;

            white-space: nowrap;
        }

        .monster-sheet-v18-hp-stat {
            white-space: nowrap;
        }

        .monster-sheet-v18-hp-stat strong {
            color:
                var(--ms-ink);

            font-weight: 900;
        }

        .monster-sheet-v18-hp-separator {
            color:
                rgba(107,29,20,.36);
        }

        .monster-sheet-v18-hp-temp {
            color:
                #7a573e;

            font-size: 11px;
            font-style: italic;

            white-space: nowrap;
        }

        .monster-sheet-v18-hp-editor {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            z-index: 20;

            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(0,1fr)
                );

            gap: 8px;

            width:
                min(
                    400px,
                    calc(100vw - 70px)
                );

            border:
                1px solid
                rgba(107,29,20,.34);

            border-radius:
                7px;

            background:
                #fffdf8;

            padding:
                8px;

            box-shadow:
                0 8px 24px
                rgba(45,27,20,.16);
        }

        .monster-sheet-v18-hp-field {
            min-width: 0;
        }

        .monster-sheet-v18-hp-field-label {
            display: block;

            margin-bottom: 4px;

            color:
                var(--ms-primary);

            font-size: 9px;
            font-weight: 900;
            letter-spacing: .025em;
        }

        .monster-sheet-v18-hp-control {
            display: grid;

            grid-template-columns:
                minmax(0,1fr)
                auto;

            overflow: hidden;

            border:
                1px solid
                var(--ms-border);

            border-radius:
                5px;

            background:
                #fff;
        }

        .monster-sheet-v18-hp-control:focus-within {
            border-color:
                rgba(107,29,20,.58);
        }

        .monster-sheet-v18-hp-control input {
            min-width: 0;
            width: 100%;
            height: 28px;

            border: 0;
            outline: 0;

            background:
                transparent;

            padding:
                0 8px;

            color:
                var(--ms-ink);

            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
        }

        .monster-sheet-v18-hp-control input::-webkit-outer-spin-button,
        .monster-sheet-v18-hp-control input::-webkit-inner-spin-button {
            margin: 0;

            appearance: none;
        }

        .monster-sheet-v18-hp-control input[type="number"] {
            appearance: textfield;
        }

        .monster-sheet-v18-hp-control button {
            min-width: 48px;
            height: 28px;

            border: 0;
            border-left:
                1px solid
                rgba(107,29,20,.18);

            background:
                #f3e7d8;

            padding:
                0 8px;

            color:
                var(--ms-primary);

            font-family: inherit;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .025em;
            text-transform: uppercase;

            transition:
                background .12s ease;
        }

        .monster-sheet-v18-hp-control button:hover {
            background:
                #ead8c1;
        }

        .monster-sheet-v18-hp-control button:disabled {
            cursor: wait;
            opacity: .48;
        }

        .monster-sheet-v18-error {
            margin:
                4px 0 6px;

            color:
                #8b1e16;

            font-size: 9px;
            font-weight: 700;
            line-height: 1.3;
        }

        .monster-sheet-v18-movement-row {
            display: grid;

            grid-template-columns:
                minmax(0,1fr)
                auto;

            gap: 12px;

            align-items: baseline;

            min-height: 21px;

            color:
                var(--ms-ink);

            font-size: 13.5px;
            line-height: 1.32;
        }

        .monster-sheet-v18-movement-left {
            min-width: 0;
        }

        .monster-sheet-v18-movement-row strong {
            color:
                var(--ms-primary);

            font-weight: 900;
        }

        .monster-sheet-v18-initiative {
            justify-self: end;

            white-space: nowrap;
        }

        .monster-sheet-v18-jump-row {
            display: flex;
            flex-wrap: wrap;

            gap: 5px;

            min-height: 21px;

            color:
                var(--ms-ink);

            font-size: 13px;
            line-height: 1.32;
        }

        .monster-sheet-v18-jump-row strong {
            color:
                var(--ms-primary);

            font-weight: 900;
        }


        /*
        |--------------------------------------------------------------------------
        | ATRIBUTOS
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-abilities {
            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0,1fr)
                );

            gap: 9px;

            margin:
                8px 0 10px;
        }

        .monster-sheet-v18-ability-group {
            overflow: hidden;
        }

        .monster-sheet-v18-ability-head,
        .monster-sheet-v18-ability-row {
            display: grid;

            grid-template-columns:
                minmax(0,1.18fr)
                .70fr
                .70fr;

            align-items: center;

            text-align: center;
        }

        .monster-sheet-v18-ability-head {
            min-height: 20px;

            color:
                var(--ms-muted);

            font-size: 9px;
            text-transform: uppercase;
        }

        .monster-sheet-v18-ability-row {
            min-height: 28px;

            color:
                var(--ms-ink);

            font-size: 12.5px;
        }

        .monster-sheet-v18-ability-row:nth-child(2) {
            background:
                var(--ms-row-a);
        }

        .monster-sheet-v18-ability-row:nth-child(3) {
            background:
                var(--ms-row-b);
        }

        .monster-sheet-v18-ability-name {
            display: flex;

            align-items: baseline;
            justify-content: space-between;

            gap: 6px;

            padding:
                0 8px;

            color:
                var(--ms-primary);

            font-weight: 900;
            text-align: left;
        }

        .monster-sheet-v18-ability-score {
            color:
                var(--ms-ink);

            font-weight: 400;
        }


        /*
        |--------------------------------------------------------------------------
        | DETALHES
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-details {
            margin-bottom: 4px;
        }

        .monster-sheet-v18-detail {
            border-top:
                1px solid
                var(--ms-border);

            padding:
                4px 0;

            color:
                var(--ms-ink);

            font-size: 13px;
            line-height: 1.36;

            overflow-wrap: anywhere;
        }

        .monster-sheet-v18-detail:last-child {
            border-bottom:
                1px solid
                var(--ms-border);
        }

        .monster-sheet-v18-detail strong {
            color:
                var(--ms-primary);

            font-weight: 900;
        }


        /*
        |--------------------------------------------------------------------------
        | SEÇÕES
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-section {
            margin-top: 13px;
        }

        .monster-sheet-v18-dynamic
        > .monster-sheet-v18-section:first-child,
        .monster-sheet-v18-source
        > .monster-sheet-v18-section:first-child {
            margin-top: 0;
        }

        .monster-sheet-v18-section-title {
            margin:
                0 0 8px;

            border-bottom:
                2px solid
                var(--ms-primary);

            padding-bottom: 3px;

            color:
                var(--ms-primary);

            font-size: 16px;
            font-weight: 900;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .monster-sheet-v18-entry {
            margin-bottom: 9px;

            color:
                var(--ms-ink);

            font-size: 13.3px;
            line-height: 1.45;
            text-align: justify;

            overflow-wrap: anywhere;
        }

        .monster-sheet-v18-entry-title {
            display: inline;

            margin: 0;

            color:
                #241813;

            font-size: inherit;
            font-style: italic;
            font-weight: 900;
        }

        /*
        |--------------------------------------------------------------------------
        | RICH TEXT — MESMA ESTRUTURA SALVA PELO TIPTAP
        |--------------------------------------------------------------------------
        |
        | O título da habilidade continua corrido com o PRIMEIRO parágrafo,
        | como em um statblock de D&D.
        |
        | A partir do segundo parágrafo, a separação do editor é respeitada.
        | Parágrafos vazios continuam ocupando uma linha real.
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-entry-body {
            display: inline;
        }

        .monster-sheet-v18-entry-title
        + .monster-sheet-v18-entry-body::before {
            content: " ";
        }

        .monster-sheet-v18-entry-body > p {
            margin: 0;
        }

        .monster-sheet-v18-entry-body > p:first-child:not(:empty) {
            display: inline;
        }

        .monster-sheet-v18-entry-body > p:first-child:empty {
            display: block;

            min-height:
                1em;
        }

        .monster-sheet-v18-entry-body > p + p {
            display: block;

            margin-top:
                .42em;
        }

        /*
         * Tiptap representa uma linha vazia como <p></p>.
         * Sem conteúdo mínimo o navegador eliminaria visualmente esse espaço.
         */
        .monster-sheet-v18-entry-body > p:empty {
            display: block;

            min-height:
                .78em;

            margin:
                0;
        }

        .monster-sheet-v18-entry-body > p:empty::before {
            content:
                "\00a0";
        }

        .monster-sheet-v18-entry-body br {
            display: inline;
        }

        .monster-sheet-v18-entry-body ul,
        .monster-sheet-v18-entry-body ol {
            display: block;

            margin:
                .42em 0
                .50em 1.32em;

            padding-left:
                .72em;
        }

        .monster-sheet-v18-entry-body ul {
            list-style:
                disc outside;
        }

        .monster-sheet-v18-entry-body ol {
            list-style:
                decimal outside;
        }

        .monster-sheet-v18-entry-body li {
            display:
                list-item;

            margin:
                .13em 0;

            padding-left:
                .08em;
        }

        .monster-sheet-v18-entry-body li > p {
            display:
                inline;

            margin:
                0;
        }

        .monster-sheet-v18-entry-body li > p + p {
            display:
                block;

            margin-top:
                .30em;
        }

        .monster-sheet-v18-entry-body blockquote {
            display: block;

            margin:
                .52em 0;

            border-left:
                2px solid
                rgba(107,29,20,.36);

            padding-left:
                .72em;

            color:
                #554239;
        }

        .monster-sheet-v18-entry-body blockquote p {
            margin:
                .28em 0;
        }

        .monster-sheet-v18-entry-body h1,
        .monster-sheet-v18-entry-body h2,
        .monster-sheet-v18-entry-body h3,
        .monster-sheet-v18-entry-body h4,
        .monster-sheet-v18-entry-body h5,
        .monster-sheet-v18-entry-body h6 {
            display: block;

            margin:
                .58em 0
                .22em;

            color:
                var(--ms-primary);

            font-family:
                inherit;

            font-size:
                1em;

            font-weight:
                900;

            line-height:
                1.28;
        }

        .monster-sheet-v18-entry-body hr {
            display: block;

            margin:
                .62em 0;

            border: 0;

            border-top:
                1px solid
                rgba(147,98,61,.30);
        }

        .monster-sheet-v18-entry-body pre {
            display: block;

            overflow-x:
                auto;

            margin:
                .48em 0;

            border:
                1px solid
                rgba(147,98,61,.20);

            border-radius:
                4px;

            background:
                rgba(234,219,200,.34);

            padding:
                .45em .58em;

            white-space:
                pre-wrap;
        }

        .monster-sheet-v18-entry-body code {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;

            font-size:
                .88em;
        }

        .monster-sheet-v18-entry-body strong,
        .monster-sheet-v18-entry-body b {
            font-weight: 900;
        }

        .monster-sheet-v18-entry-body em,
        .monster-sheet-v18-entry-body i {
            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | MULTIATAQUE
        |--------------------------------------------------------------------------
        |
        | O bloco inteiro segue a convenção solicitada: negrito + itálico.
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-entry.is-multiattack,
        .monster-sheet-v18-entry.is-multiattack
        .monster-sheet-v18-entry-title,
        .monster-sheet-v18-entry.is-multiattack
        .monster-sheet-v18-entry-body,
        .monster-sheet-v18-entry.is-multiattack
        .monster-sheet-v18-entry-body * {
            font-weight: 900;
            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | TRACKER TEMPORÁRIO
        |--------------------------------------------------------------------------
        |
        | Pequeno o suficiente para continuar parecendo parte de um statblock.
        | O estado real fica somente no localStorage da ativação atual.
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-tracker {
            display: inline-flex;

            min-height: 19px;

            align-items: center;

            gap: 2px;

            margin:
                0 3px;

            overflow: hidden;

            vertical-align: middle;

            border:
                1px solid
                rgba(147,98,61,.34);

            border-radius:
                999px;

            background:
                #f2e8d9;

            color:
                #704326;

            font-family:
                Georgia,
                Cambria,
                "Times New Roman",
                serif;

            font-size: 8px;
            font-style: normal !important;
            font-weight: 900 !important;

            line-height: 1;
        }

        .monster-sheet-v18-tracker button {
            display: inline-flex;

            width: 18px;
            height: 18px;

            align-items: center;
            justify-content: center;

            border: 0;

            background:
                transparent;

            padding: 0;

            color:
                #6b1d14;

            font-family: inherit;
            font-size: 10px;
            font-style: normal !important;
            font-weight: 900 !important;

            line-height: 1;

            transition:
                background .12s ease,
                color .12s ease;
        }

        .monster-sheet-v18-tracker button:hover {
            background:
                rgba(107,29,20,.08);
        }

        .monster-sheet-v18-tracker button:disabled {
            cursor: default;
            opacity: .30;
        }

        .monster-sheet-v18-tracker-value {
            min-width: 36px;

            padding:
                0 3px;

            text-align: center;

            color:
                #53150f;

            font-size: 8px;
            font-style: normal !important;
            font-weight: 900 !important;

            white-space: nowrap;
        }

        .monster-sheet-v18-tracker-reset {
            border-left:
                1px solid
                rgba(147,98,61,.20)
                !important;

            color:
                #93623d
                !important;

            font-size:
                9px
                !important;
        }

        .monster-sheet-v18-tracker.is-empty {
            border-color:
                rgba(127,38,29,.34);

            background:
                #fff0ea;
        }

        .monster-sheet-v18-tracker.is-empty
        .monster-sheet-v18-tracker-value {
            color:
                #7f261d;
        }

        .monster-sheet-v18-entry.is-multiattack
        .monster-sheet-v18-tracker,
        .monster-sheet-v18-entry.is-multiattack
        .monster-sheet-v18-tracker * {
            font-style: normal !important;
        }


        /*
        |--------------------------------------------------------------------------
        | FONTE INVISÍVEL DE MEDIÇÃO
        |--------------------------------------------------------------------------
        */
        .monster-sheet-v18-source {
            position: absolute;
            top: 0;
            left: -100000px;

            z-index: -1;

            visibility: hidden;
            pointer-events: none;

            width: 500px;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVIDADE
        |--------------------------------------------------------------------------
        */
        @media (max-width: 900px) {
            .monster-sheet-v18-overlay {
                align-items: stretch;

                padding:
                    10px;
            }

            .monster-sheet-v18-shell {
                width: 100%;
                max-height:
                    calc(100vh - 20px);

                padding-top:
                    40px;
            }

            .monster-sheet-v18-panel {
                max-height:
                    calc(100vh - 50px);
            }

            .monster-sheet-v18-untransform {
                top: 0;
                left: 0;

                width: auto;
                min-height: 34px;

                transform: none;

                border-right:
                    1px solid
                    rgba(244,241,232,.66);

                border-radius:
                    8px;

                padding:
                    0 11px;
            }

            .monster-sheet-v18-untransform:hover {
                transform:
                    translateY(-1px);
            }

            .monster-sheet-v18-untransform span {
                writing-mode:
                    horizontal-tb;

                transform: none;
            }

            .monster-sheet-v18-columns {
                grid-template-columns:
                    1fr;
            }

            .monster-sheet-v18-column--right {
                border-top:
                    1px solid
                    var(--ms-primary);

                border-left: 0;
            }

            .monster-sheet-v18-column--right.is-empty {
                display: none;
            }
        }

        @media (max-width: 650px) {
            .monster-sheet-v18-column {
                padding:
                    12px;
            }

            .monster-sheet-v18-identity {
                grid-template-columns:
                    minmax(0,1fr)
                    62px;
            }

            .monster-sheet-v18-portrait {
                width: 62px;
                height: 72px;
            }

            .monster-sheet-v18-title {
                font-size: 23px;
            }

            .monster-sheet-v18-abilities {
                grid-template-columns:
                    1fr;
            }
        }

        @media (max-width: 650px) {
            .monster-sheet-v18-movement-row {
                grid-template-columns:
                    minmax(0,1fr)
                    auto;

                gap: 8px;
            }

            .monster-sheet-v18-hp-editor {
                grid-template-columns:
                    1fr;

                width:
                    min(
                        300px,
                        calc(100vw - 44px)
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TESTE DE CONCENTRAÇÃO
        |--------------------------------------------------------------------------
        |
        | O aviso surge somente quando a forma que exige concentração recebe
        | dano e permanece ativa. A falha NÃO encerra a transformação de forma
        | automática; ela apenas informa o resultado ao jogador.
        |--------------------------------------------------------------------------
        */
        @keyframes monster-sheet-v18-concentration-pulse {
            0%,
            100% {
                box-shadow:
                    0 16px 42px
                    rgba(37,20,15,.24),
                    0 0 0 0
                    rgba(107,29,20,.20);
            }

            50% {
                box-shadow:
                    0 16px 42px
                    rgba(37,20,15,.24),
                    0 0 0 7px
                    rgba(107,29,20,0);
            }
        }

        .monster-sheet-v18-concentration {
            position: fixed;

            top: 22px;
            right: 22px;

            z-index:
                2147483100;

            width:
                min(
                    360px,
                    calc(100vw - 44px)
                );

            overflow: hidden;

            border:
                1px solid
                rgba(107,29,20,.42);

            border-radius:
                14px;

            background:
                #fffdf8;

            color:
                #2b1d17;

            box-shadow:
                0 16px 42px
                rgba(37,20,15,.24);

            animation:
                monster-sheet-v18-concentration-pulse
                1.3s
                ease-out
                2;
        }

        .monster-sheet-v18-concentration-head {
            display: flex;

            align-items: flex-start;
            justify-content: space-between;

            gap: 12px;

            border-bottom:
                1px solid
                rgba(147,98,61,.26);

            background:
                linear-gradient(
                    180deg,
                    #f0e1ce,
                    #ead8c1
                );

            padding:
                11px 12px 10px;
        }

        .monster-sheet-v18-concentration-kicker {
            color:
                #93623d;

            font-size: 7px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .monster-sheet-v18-concentration-title {
            margin:
                3px 0 0;

            color:
                #53150f;

            font-family:
                Georgia,
                Cambria,
                "Times New Roman",
                serif;

            font-size: 16px;
            font-weight: 900;
            line-height: 1.05;
        }

        .monster-sheet-v18-concentration-close {
            display: flex;

            width: 26px;
            height: 26px;

            flex: 0 0 auto;

            align-items: center;
            justify-content: center;

            border: 0;

            border-radius:
                7px;

            background:
                transparent;

            color:
                #704326;

            font-size: 17px;
            line-height: 1;

            transition:
                background .12s ease,
                color .12s ease;
        }

        .monster-sheet-v18-concentration-close:hover {
            background:
                rgba(107,29,20,.08);

            color:
                #53150f;
        }

        .monster-sheet-v18-concentration-body {
            padding:
                11px 12px 12px;
        }

        .monster-sheet-v18-concentration-facts {
            display: flex;

            flex-wrap: wrap;

            gap: 5px;

            margin-bottom: 9px;
        }

        .monster-sheet-v18-concentration-fact {
            display: inline-flex;

            min-height: 20px;

            align-items: center;

            border:
                1px solid
                rgba(147,98,61,.24);

            border-radius:
                999px;

            background:
                #f8f0e6;

            padding:
                0 7px;

            color:
                #704326;

            font-size: 8px;
            font-weight: 900;
            letter-spacing: .025em;
        }

        .monster-sheet-v18-concentration-copy {
            margin: 0;

            color:
                #674936;

            font-size: 10px;
            line-height: 1.45;
        }

        .monster-sheet-v18-concentration-actions {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 10px;

            margin-top: 10px;
        }

        .monster-sheet-v18-concentration-roll {
            display: inline-flex;

            min-height: 31px;

            align-items: center;
            justify-content: center;

            border:
                1px solid
                rgba(83,21,15,.28);

            border-radius:
                8px;

            background:
                #6b1d14;

            padding:
                0 12px;

            color:
                #fffaf2;

            font-family:
                Georgia,
                Cambria,
                "Times New Roman",
                serif;

            font-size: 9px;
            font-weight: 900;
            letter-spacing: .035em;
            text-transform: uppercase;

            transition:
                background .12s ease,
                transform .12s ease;
        }

        .monster-sheet-v18-concentration-roll:hover {
            background:
                #53150f;

            transform:
                translateY(-1px);
        }

        .monster-sheet-v18-concentration-roll:disabled {
            cursor: wait;

            opacity: .48;

            transform: none;
        }

        .monster-sheet-v18-concentration-result {
            display: flex;

            align-items: center;

            gap: 9px;

            min-width: 0;
        }

        .monster-sheet-v18-concentration-result-copy {
            min-width: 0;
        }

        .monster-sheet-v18-concentration-result-label {
            display: block;

            color:
                #93623d;

            font-size: 7px;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .monster-sheet-v18-concentration-result-total {
            display: block;

            margin-top: 2px;

            color:
                #53150f;

            font-family:
                Georgia,
                Cambria,
                "Times New Roman",
                serif;

            font-size: 15px;
            font-weight: 900;
            line-height: 1;
        }

        .monster-sheet-v18-concentration-result-total.is-success {
            color:
                #315d45;
        }

        .monster-sheet-v18-concentration-result-total.is-failure {
            color:
                #8b2a20;
        }

        .monster-sheet-v18-concentration-error {
            margin-top: 8px;

            color:
                #8b2a20;

            font-size: 9px;
            font-weight: 700;
            line-height: 1.35;
        }

        @media (max-width: 650px) {
            .monster-sheet-v18-concentration {
                top: 12px;
                right: 12px;
                left: 12px;

                width: auto;
            }
        }



        /*
        |--------------------------------------------------------------------------
        | V19 — THEME CORE / MODAIS
        |--------------------------------------------------------------------------
        |
        | Mantém o statblock legível e neutro. A cor escolhida aparece em
        | controles, linhas e estados; títulos e texto mecânico permanecem com
        | a tinta principal da ficha em vez de ficarem todos coloridos.
        |
        */

        .character-sheet-theme[data-theme].monster-sheet-v18-overlay {
            color: var(--theme-text-primary);
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-backdrop {
            background: var(--theme-modal-backdrop, rgba(0,0,0,.82)) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            backdrop-filter: blur(8px) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-panel {
            border-color: var(--theme-border-strong) !important;
            background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            color: var(--theme-text-primary) !important;
            box-shadow: var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.32)) !important;
            scrollbar-color: color-mix(in srgb, var(--theme-accent) 36%, transparent) transparent;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-panel::-webkit-scrollbar-thumb {
            background: color-mix(in srgb, var(--theme-accent) 36%, transparent) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-sheet {
            background: var(--theme-surface-card) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-column--right {
            border-left-color: var(--theme-border) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-untransform {
            border-color: color-mix(in srgb, var(--theme-button-text) 42%, transparent) !important;
            background: var(--theme-button-background) !important;
            color: var(--theme-button-text) !important;
            box-shadow: var(--theme-button-shadow) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-untransform:hover {
            background: var(--theme-button-hover-background) !important;
            box-shadow: var(--theme-button-hover-shadow) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-title,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-section-title,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-entry-title,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-ability-name,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-core-line strong,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-detail strong,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-hp-label,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-movement-row strong,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-jump-row strong {
            color: var(--theme-text-strong) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-form-name,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-kicker,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-hp-field-label {
            color: var(--theme-label) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-subtitle,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-ability-head,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-hp-temp {
            color: var(--theme-text-secondary) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-rule,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-section-title {
            border-color: var(--theme-accent) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-pill {
            border-color: var(--theme-border) !important;
            background: var(--theme-accent-softer) !important;
            color: var(--theme-label) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-portrait,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-hp-editor,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-hp-control,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-tracker {
            border-color: var(--theme-border) !important;
            background: var(--theme-surface-raised) !important;
            color: var(--theme-text-primary) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-ability-row:nth-child(2),
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-ability-row:nth-child(3) {
            background: var(--theme-card-background) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-detail {
            border-color: var(--theme-border-soft) !important;
            color: var(--theme-text-primary) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-entry-body blockquote,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-entry-body pre {
            border-color: var(--theme-border) !important;
            background: var(--theme-surface-inset) !important;
            color: var(--theme-text-primary) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-tracker button,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-tracker-reset {
            color: var(--theme-label) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-tracker button:hover {
            background: var(--theme-card-hover) !important;
        }

        /* ---------------- CONCENTRAÇÃO ---------------- */

        .character-sheet-theme[data-theme].monster-sheet-v18-concentration {
            border-color: var(--theme-modal-border, var(--theme-border)) !important;
            background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
            color: var(--theme-modal-text, var(--theme-text-primary)) !important;
            box-shadow: var(--theme-modal-shadow, 0 24px 70px rgba(0,0,0,.30)) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-head {
            border-color: var(--theme-border) !important;
            background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-kicker {
            color: var(--theme-label) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-title,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-result-total {
            color: var(--theme-text-strong) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-copy,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-result-label {
            color: var(--theme-text-secondary) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-close,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-fact {
            border-color: var(--theme-border) !important;
            background: var(--theme-surface-raised) !important;
            color: var(--theme-text-primary) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-close:hover {
            background: var(--theme-card-hover) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-roll {
            border-color: var(--theme-button-border) !important;
            background: var(--theme-button-background) !important;
            color: var(--theme-button-text) !important;
            box-shadow: var(--theme-button-shadow) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-roll:hover {
            background: var(--theme-button-hover-background) !important;
            box-shadow: var(--theme-button-hover-shadow) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-result-total.is-success {
            color: var(--theme-state-heal) !important;
        }

        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-result-total.is-failure,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-concentration-error,
        .character-sheet-theme[data-theme]
        .monster-sheet-v18-error {
            color: var(--theme-state-damage) !important;
        }

        /* Navegação contextual, sem ocupar o conteúdo do statblock. */
        .monster-sheet-v18-view-original {
            position: absolute;
            top: 16px;
            left: -49px;
            z-index: 7;
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border: 1px solid var(--theme-border, #a78969);
            border-radius: 11px;
            background: var(--theme-paper-strong, #fffdf8);
            color: var(--theme-text-primary, #38251d);
            box-shadow: 0 5px 18px #0002;
            cursor: pointer;
            transition: background .15s ease, transform .15s ease;
        }
        .monster-sheet-v18-view-original:hover {
            background: var(--theme-card-hover, var(--theme-paper, #f2e6d8));
            transform: translateX(-2px);
        }
        .monster-sheet-v18-view-original:focus-visible,
        .monster-sheet-v18-reopen:focus-visible {
            outline: 2px solid var(--theme-accent-strong, #6b1d14);
            outline-offset: 3px;
        }
        .monster-sheet-v18-view-original svg {
            width: 18px;
            height: 18px;
        }
        /* Atalho de retorno — reaproveita as cores dos botões laterais do tema.
           A transformação continua ativa; este botão só troca a visualização. */
        .character-sheet-theme[data-theme].monster-sheet-v18-reopen {
            position: fixed;
            z-index: 25;
            top: 12px;
            left: 50%;
            bottom: auto;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            width: auto;
            max-width: min(90vw, 370px);
            min-height: 48px;
            padding: 6px 14px 6px 7px;
            transform: translateX(-50%);
            border: 1px solid var(--theme-border, rgba(160, 130, 100, .45));
            border-radius: 13px;
            background: var(--theme-side-tab-background, var(--theme-button-background, #6b1d14));
            color: var(--theme-side-tab-text, var(--theme-button-text, #fffaf2));
            box-shadow: 0 8px 22px rgba(0, 0, 0, .17),
                inset 0 1px 0 color-mix(in srgb, currentColor 12%, transparent);
            font-family: system-ui, sans-serif;
            line-height: 1.15;
            text-align: left;
            cursor: pointer;
            transition: background .16s ease, box-shadow .16s ease, transform .16s ease;
        }
        .character-sheet-theme[data-theme].monster-sheet-v18-reopen:hover {
            background: var(--theme-side-tab-hover-background,
                var(--theme-button-hover-background, var(--theme-button-background, #6b1d14)));
            box-shadow: 0 11px 28px rgba(0, 0, 0, .22),
                inset 0 1px 0 color-mix(in srgb, currentColor 16%, transparent);
            transform: translate(-50%, -2px);
        }
        .character-sheet-theme[data-theme].monster-sheet-v18-reopen:active {
            transform: translate(-50%, 0) scale(.985);
        }
        .character-sheet-theme[data-theme].monster-sheet-v18-reopen:focus-visible {
            outline: 2px solid var(--theme-accent-strong, currentColor);
            outline-offset: 3px;
        }
        .monster-sheet-v18-reopen-symbol {
            display: grid;
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 1px solid color-mix(in srgb, currentColor 19%, transparent);
            border-radius: 9px;
            background: color-mix(in srgb, currentColor 11%, transparent);
        }
        .monster-sheet-v18-reopen-symbol svg {
            width: 17px;
            height: 17px;
        }
        .monster-sheet-v18-reopen-copy {
            display: flex;
            min-width: 0;
            flex: 1 1 auto;
            flex-direction: column;
            gap: 2px;
        }
        .monster-sheet-v18-reopen-kicker {
            display: block;
            font-size: 8px;
            font-weight: 850;
            letter-spacing: .11em;
            opacity: .76;
            text-transform: uppercase;
        }
        .monster-sheet-v18-reopen-name {
            display: block;
            min-width: 0;
            overflow: hidden;
            font-size: 11px;
            font-weight: 850;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .monster-sheet-v18-reopen-chevron {
            flex: 0 0 14px;
            width: 14px;
            height: 14px;
            margin-left: 1px;
            opacity: .75;
        }
        @media (max-width: 760px) {
            .monster-sheet-v18-view-original {
                top: 7px;
                left: 7px;
                width: 32px;
                height: 32px;
            }
            .character-sheet-theme[data-theme].monster-sheet-v18-reopen {
                top: 9px;
                max-width: min(92vw, 350px);
                min-height: 44px;
                padding: 5px 11px 5px 6px;
                gap: 8px;
            }
            .monster-sheet-v18-reopen-symbol {
                flex-basis: 31px;
                width: 31px;
                height: 31px;
            }
        }
    </style>
@endonce


<div
    x-data="{
        hpCurrent: {{ $monsterCurrentHp }},
        hpMax: {{ $monsterMaxHp }},
        temporaryHp: {{ $monsterTemporaryHp }},

        hpEditorOpen: false,
        damageAmount: '',
        healingAmount: '',

        busy: false,
        deTransforming: false,
        error: null,
        monsterVisible: true,
        originalDrawerOpen: false,
        destroy() {
            document.body.classList.remove('character-monster-active', 'character-monster-view-visible');
        },
        monsterViewKey: @js('spellbound:monster:view:' . $character->id . ':' . $trackerSessionToken),
        initMonsterView() {
            try {
                this.monsterVisible = sessionStorage.getItem(this.monsterViewKey) !== 'hidden';
            } catch (error) {
                this.monsterVisible = true;
            }
        },
        hideMonsterView() {
            this.monsterVisible = false;
            try { sessionStorage.setItem(this.monsterViewKey, 'hidden'); } catch (error) {}
        },
        showMonsterView() {
            this.monsterVisible = true;
            try { sessionStorage.removeItem(this.monsterViewKey); } catch (error) {}
            this.$nextTick(() => this.scheduleBalance());
        },
        concentrationCheck: null,
        concentrationRoll: null,
        concentrationRolling: false,
        concentrationError: null,

        diceRollUrl:
            @js(url('/api/roll')),

        balanceId: @js($balanceId),

        trackerNamespace:
            @js($trackerNamespace),

        balanceTimer: null,
        balanceObserver: null,
        lastBalanceWidth: 0,

        damageUrl: @js(route(
            'characters.transformations.monster.damage',
            $character
        )),

        healUrl: @js(route(
            'characters.transformations.monster.heal',
            $character
        )),

        deactivateUrl: @js(route(
            'characters.transformations.deactivate',
            $character
        )),

        csrfToken() {
            return document
                .querySelector(
                    'meta[name=&quot;csrf-token&quot;]'
                )
                ?.getAttribute(
                    'content'
                )
                || '{{ csrf_token() }}';
        },

        async parseResponse(response) {
            const data =
                await response
                    .json()
                    .catch(
                        () => ({})
                    );

            if (!response.ok) {
                const messages =
                    data?.errors
                        ? Object
                            .values(
                                data.errors
                            )
                            .flat()
                            .filter(Boolean)
                        : [];

                throw new Error(
                    messages.length
                        ? messages.join(' ')
                        : (
                            data?.message
                            || 'Não foi possível concluir a operação.'
                        )
                );
            }

            return data;
        },

        syncMonsterState(data) {
            this.hpCurrent =
                Number(
                    data?.monster_state
                        ?.current_hp
                    ?? this.hpCurrent
                );

            this.hpMax =
                Number(
                    data?.monster_state
                        ?.max_hp
                    ?? this.hpMax
                );

            this.temporaryHp =
                Number(
                    data?.monster_state
                        ?.temporary_hp
                    ?? this.temporaryHp
                );
        },

        toggleHpEditor() {
            this.hpEditorOpen =
                !this.hpEditorOpen;

            if (
                this.hpEditorOpen
            ) {
                this.$nextTick(
                    () =>
                        this.$refs
                            .damageAmount
                            ?.focus()
                );
            }
        },

        closeHpEditor() {
            if (
                this.busy
            ) {
                return;
            }

            this.hpEditorOpen =
                false;
        },

        async submitManualDamage() {
            const amount =
                parseInt(
                    this.damageAmount
                );

            if (
                Number.isNaN(
                    amount
                )
                || amount <= 0
            ) {
                this.damageAmount =
                    '';

                return;
            }

            await this.applyDamage(
                amount
            );

            if (!this.error) {
                this.damageAmount =
                    '';
            }
        },

        async submitManualHealing() {
            const amount =
                parseInt(
                    this.healingAmount
                );

            if (
                Number.isNaN(
                    amount
                )
                || amount <= 0
            ) {
                this.healingAmount =
                    '';

                return;
            }

            await this.applyHealing(
                amount
            );

            if (!this.error) {
                this.healingAmount =
                    '';
            }
        },


        openConcentrationCheck(check) {
            if (
                !check
                || !check.required
            ) {
                return;
            }

            this.hpEditorOpen =
                false;

            this.concentrationCheck =
                check;

            this.concentrationRoll =
                null;

            this.concentrationError =
                null;

            window.dispatchEvent(
                new CustomEvent(
                    'character-concentration-check',
                    {
                        detail:
                            check,
                    }
                )
            );
        },

        closeConcentrationCheck() {
            if (
                this.concentrationRolling
            ) {
                return;
            }

            this.concentrationCheck =
                null;

            this.concentrationRoll =
                null;

            this.concentrationError =
                null;
        },

        extractRollTotal(data) {
            const candidate =
                data?.data?.total
                ?? data?.data?.result
                ?? data?.data?.value
                ?? data?.data?.sum
                ?? data?.total
                ?? data?.result
                ?? null;

            if (
                candidate === null
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

        async rollConcentrationCheck() {
            if (
                !this.concentrationCheck
                || this.concentrationRolling
            ) {
                return;
            }

            const check =
                this.concentrationCheck;

            const expression =
                String(
                    check.expression
                    || '1d20'
                )
                    .trim();

            this.concentrationRolling =
                true;

            this.concentrationError =
                null;

            try {
                const response =
                    await fetch(
                        this.diceRollUrl,
                        {
                            method:
                                'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    this.csrfToken(),

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body:
                                JSON.stringify({
                                    expression,
                                }),
                        }
                    );

                const data =
                    await this.parseResponse(
                        response
                    );

                const total =
                    this.extractRollTotal(
                        data
                    );

                if (
                    total === null
                ) {
                    throw new Error(
                        'A rolagem não retornou um total válido.'
                    );
                }

                const modifier =
                    parseInt(
                        check.modifier
                    )
                    || 0;

                const die =
                    total
                    - modifier;

                const difficultyClass =
                    Math.max(
                        1,
                        parseInt(
                            check.dc
                        )
                        || 10
                    );

                const passed =
                    total
                    >= difficultyClass;

                this.concentrationRoll = {
                    die,
                    total,
                    modifier,
                    passed,
                    formatted:
                        data?.formatted
                        ?? expression,
                };

                window.dispatchEvent(
                    new CustomEvent(
                        'character-dice-rolled',
                        {
                            detail: {
                                label:
                                    check.label
                                    || 'Salvamento de Constituição',

                                modifier:
                                    check.base_modifier
                                    ?? modifier,

                                exhaustionPenalty:
                                    check.exhaustion_penalty
                                    ?? 0,

                                effectiveModifier:
                                    modifier,

                                die,
                                total,

                                formatted:
                                    data?.formatted
                                    ?? expression,

                                concentration: {
                                    dc:
                                        difficultyClass,

                                    passed,

                                    damage:
                                        check.damage
                                        ?? null,

                                    context:
                                        check.context
                                        ?? {},
                                },
                            },
                        }
                    )
                );

            } catch (error) {
                console.error(
                    'Erro no teste de concentração:',
                    error
                );

                this.concentrationError =
                    error?.message
                    || 'Não foi possível rolar o teste de concentração.';

            } finally {
                this.concentrationRolling =
                    false;
            }
        },


        async applyDamage(amount) {
            if (
                this.busy
                || this.deTransforming
            ) {
                return;
            }

            const damage =
                Math.max(
                    1,
                    parseInt(
                        amount
                    )
                    || 1
                );

            this.busy = true;
            this.error = null;

            try {
                const response =
                    await fetch(
                        this.damageUrl,
                        {
                            method:
                                'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    this.csrfToken(),

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body:
                                JSON.stringify({
                                    damage,
                                }),
                        }
                    );

                const data =
                    await this.parseResponse(
                        response
                    );

                this.syncMonsterState(
                    data
                );

                if (data?.deactivated) {
                    this.clearTrackerCache();

                    window.setTimeout(
                        () =>
                            window.location
                                .reload(),
                        180
                    );

                } else if (
                    data?.concentration_check
                        ?.required
                ) {
                    this.openConcentrationCheck(
                        data.concentration_check
                    );
                }

            } catch (error) {
                console.error(
                    'Erro ao aplicar dano à transformação:',
                    error
                );

                this.error =
                    error?.message
                    || 'Não foi possível aplicar o dano.';

            } finally {
                this.busy = false;
            }
        },

        async applyHealing(amount) {
            if (
                this.busy
                || this.deTransforming
            ) {
                return;
            }

            const healing =
                Math.max(
                    1,
                    parseInt(
                        amount
                    )
                    || 1
                );

            this.busy = true;
            this.error = null;

            try {
                const response =
                    await fetch(
                        this.healUrl,
                        {
                            method:
                                'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    this.csrfToken(),

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body:
                                JSON.stringify({
                                    healing,
                                }),
                        }
                    );

                const data =
                    await this.parseResponse(
                        response
                    );

                this.syncMonsterState(
                    data
                );

            } catch (error) {
                console.error(
                    'Erro ao curar a transformação:',
                    error
                );

                this.error =
                    error?.message
                    || 'Não foi possível aplicar a cura.';

            } finally {
                this.busy = false;
            }
        },

        async deTransform() {
            if (
                this.busy
                || this.deTransforming
            ) {
                return;
            }

            this.deTransforming = true;
            this.error = null;

            try {
                const response =
                    await fetch(
                        this.deactivateUrl,
                        {
                            method:
                                'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    this.csrfToken(),

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body:
                                JSON.stringify({}),
                        }
                    );

                await this.parseResponse(
                    response
                );

                this.clearTrackerCache();

                window.location.reload();

            } catch (error) {
                console.error(
                    'Erro ao destransformar:',
                    error
                );

                this.error =
                    error?.message
                    || 'Não foi possível destransformar.';

                this.deTransforming = false;
            }
        },

        /*
        |--------------------------------------------------------------------------
        | TRACKERS TEMPORÁRIOS — localStorage
        |--------------------------------------------------------------------------
        */

        trackerStorageKey(key) {
            return (
                this.trackerNamespace
                + ':'
                + String(
                    key
                    || 'entry'
                )
            );
        },

        readTrackerValue(
            key,
            initial,
            capacity
        ) {
            const safeInitial =
                Math.max(
                    0,
                    Math.min(
                        Number(capacity) || 1,
                        Number(initial) || 0
                    )
                );

            try {
                const stored =
                    window.localStorage
                        .getItem(
                            this.trackerStorageKey(
                                key
                            )
                        );

                if (
                    stored === null
                    || stored === ''
                ) {
                    return safeInitial;
                }

                const parsed =
                    parseInt(
                        stored
                    );

                if (
                    Number.isNaN(
                        parsed
                    )
                ) {
                    return safeInitial;
                }

                return Math.max(
                    0,
                    Math.min(
                        Number(capacity) || 1,
                        parsed
                    )
                );

            } catch (error) {
                return safeInitial;
            }
        },

        writeTrackerValue(
            key,
            value,
            capacity
        ) {
            const normalized =
                Math.max(
                    0,
                    Math.min(
                        Number(capacity) || 1,
                        parseInt(
                            value
                        )
                        || 0
                    )
                );

            try {
                window.localStorage
                    .setItem(
                        this.trackerStorageKey(
                            key
                        ),
                        String(
                            normalized
                        )
                    );
            } catch (error) {
                /*
                 * Se o navegador bloquear localStorage, o tracker continua
                 * funcionando visualmente durante a renderização atual.
                 */
            }

            return normalized;
        },

        trackerDisplay(
            current,
            reset,
            label
        ) {
            if (
                reset === 'recharge'
            ) {
                return (
                    current > 0
                        ? 'Pronto'
                        : 'Usado'
                )
                + ' · '
                + label;
            }

            return (
                current
                + '/'
                + label
            );
        },

        refreshTrackerDisplays(
            scope = null
        ) {
            const root =
                scope
                || document.querySelector(
                    `[data-balance-root='${this.balanceId}']`
                );

            if (!root) {
                return;
            }

            root
                .querySelectorAll(
                    '[data-tracker-key]'
                )
                .forEach(
                    tracker => {
                        const key =
                            tracker.dataset
                                .trackerKey
                            || '';

                        const initial =
                            Number(
                                tracker.dataset
                                    .trackerInitial
                                || 0
                            );

                        const capacity =
                            Math.max(
                                1,
                                Number(
                                    tracker.dataset
                                        .trackerCapacity
                                    || 1
                                )
                            );

                        const reset =
                            tracker.dataset
                                .trackerReset
                            || '';

                        const label =
                            tracker.dataset
                                .trackerLabel
                            || 'Usos';

                        const current =
                            this.readTrackerValue(
                                key,
                                initial,
                                capacity
                            );

                        tracker.classList
                            .toggle(
                                'is-empty',
                                current <= 0
                            );

                        const valueNode =
                            tracker.querySelector(
                                '[data-tracker-value]'
                            );

                        if (valueNode) {
                            valueNode.textContent =
                                this.trackerDisplay(
                                    current,
                                    reset,
                                    label
                                );
                        }

                        const decrement =
                            tracker.querySelector(
                                '[data-tracker-action=decrement]'
                            );

                        if (decrement) {
                            decrement.disabled =
                                current <= 0;
                        }

                        const increment =
                            tracker.querySelector(
                                '[data-tracker-action=increment]'
                            );

                        if (increment) {
                            increment.disabled =
                                current >= capacity;
                        }
                    }
                );
        },

        handleTrackerClick(event) {
            const button =
                event.target
                    ?.closest(
                        '[data-tracker-action]'
                    );

            if (!button) {
                return;
            }

            const tracker =
                button.closest(
                    '[data-tracker-key]'
                );

            if (!tracker) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const key =
                tracker.dataset
                    .trackerKey
                || '';

            const initial =
                Number(
                    tracker.dataset
                        .trackerInitial
                    || 0
                );

            const capacity =
                Math.max(
                    1,
                    Number(
                        tracker.dataset
                            .trackerCapacity
                        || 1
                    )
                );

            let current =
                this.readTrackerValue(
                    key,
                    initial,
                    capacity
                );

            const action =
                button.dataset
                    .trackerAction
                || '';

            if (
                action === 'decrement'
            ) {
                current--;

            } else if (
                action === 'increment'
            ) {
                current++;

            } else if (
                action === 'reset'
            ) {
                current =
                    initial;
            }

            this.writeTrackerValue(
                key,
                current,
                capacity
            );

            const root =
                document.querySelector(
                    `[data-balance-root='${this.balanceId}']`
                );

            this.refreshTrackerDisplays(
                root
            );

            /*
             * O texto pode mudar de largura (ex.: Pronto -> Usado),
             * então recalculamos a distribuição das colunas.
             */
            this.scheduleBalance();
        },

        clearTrackerCache() {
            try {
                const prefix =
                    this.trackerNamespace
                    + ':';

                const keys =
                    [];

                for (
                    let index = 0;
                    index < window.localStorage.length;
                    index++
                ) {
                    const key =
                        window.localStorage
                            .key(
                                index
                            );

                    if (
                        key
                        && key.startsWith(
                            prefix
                        )
                    ) {
                        keys.push(
                            key
                        );
                    }
                }

                keys.forEach(
                    key =>
                        window.localStorage
                            .removeItem(
                                key
                            )
                );

            } catch (error) {
                /*
                 * Cache é apenas conveniência de UI.
                 */
            }
        },


        /*
        |--------------------------------------------------------------------------
        | BALANCEAMENTO SEMÂNTICO
        |--------------------------------------------------------------------------
        |
        | Cada entrada é indivisível.
        | O corte pode ocorrer entre entradas da mesma seção.
        | Se isso acontecer, o título da seção é repetido na coluna direita.
        |--------------------------------------------------------------------------
        */

        initBalance() {
            this.$nextTick(
                () => {
                    window.requestAnimationFrame(
                        () => {
                            this.balance();

                            this.refreshTrackerDisplays();

                            const root =
                                document.querySelector(
                                    `[data-balance-root='${this.balanceId}']`
                                );

                            if (
                                root
                                && 'ResizeObserver' in window
                            ) {
                                this.lastBalanceWidth =
                                    Math.round(
                                        root
                                            .getBoundingClientRect()
                                            .width
                                    );

                                this.balanceObserver =
                                    new ResizeObserver(
                                        entries => {
                                            const width =
                                                Math.round(
                                                    entries?.[0]
                                                        ?.contentRect
                                                        ?.width
                                                    ?? 0
                                                );

                                            if (
                                                width > 0
                                                && width !== this.lastBalanceWidth
                                            ) {
                                                this.lastBalanceWidth =
                                                    width;

                                                this.scheduleBalance();
                                            }
                                        }
                                    );

                                this.balanceObserver.observe(
                                    root
                                );
                            }

                            if (
                                document.fonts
                                && document.fonts.ready
                            ) {
                                document.fonts.ready.then(
                                    () =>
                                        this.scheduleBalance()
                                );
                            }
                        }
                    );
                }
            );
        },

        scheduleBalance() {
            if (this.balanceTimer) {
                window.clearTimeout(
                    this.balanceTimer
                );
            }

            this.balanceTimer =
                window.setTimeout(
                    () =>
                        this.balance(),
                    80
                );
        },

        outerHeight(element) {
            if (!element) {
                return 0;
            }

            const rect =
                element
                    .getBoundingClientRect();

            const style =
                window.getComputedStyle(
                    element
                );

            return (
                rect.height
                + (
                    parseFloat(
                        style.marginTop
                    )
                    || 0
                )
                + (
                    parseFloat(
                        style.marginBottom
                    )
                    || 0
                )
            );
        },

        renderRange(
            container,
            atoms,
            sourceSections,
            suppressFirstTitle = false
        ) {
            if (!container) {
                return;
            }

            container.replaceChildren();

            let activeSection =
                null;

            let wrapper =
                null;

            let renderedSectionCount =
                0;

            for (const atom of atoms) {
                if (
                    activeSection
                    !== atom.sectionIndex
                ) {
                    activeSection =
                        atom.sectionIndex;

                    const sourceSection =
                        sourceSections[
                            atom.sectionIndex
                        ];

                    wrapper =
                        document.createElement(
                            'section'
                        );

                    wrapper.className =
                        'monster-sheet-v18-section';

                    const sourceTitle =
                        sourceSection
                            ?.querySelector(
                                '[data-balance-title]'
                            );

                    const hideTitle =
                        suppressFirstTitle
                        && renderedSectionCount === 0;

                    if (
                        sourceTitle
                        && !hideTitle
                    ) {
                        const title =
                            sourceTitle
                                .cloneNode(
                                    true
                                );

                        title.removeAttribute(
                            'data-balance-title'
                        );

                        wrapper.appendChild(
                            title
                        );
                    }

                    container.appendChild(
                        wrapper
                    );

                    renderedSectionCount++;
                }

                const entry =
                    atom.element
                        .cloneNode(
                            true
                        );

                entry.removeAttribute(
                    'data-balance-entry'
                );

                wrapper?.appendChild(
                    entry
                );
            }

            this.refreshTrackerDisplays(
                container
            );
        },

        balance() {
            const root =
                document.querySelector(
                    `[data-balance-root='${this.balanceId}']`
                );

            if (!root) {
                return;
            }

            const fixedLeft =
                root.querySelector(
                    '[data-fixed-left]'
                );

            const left =
                root.querySelector(
                    '[data-balance-left]'
                );

            const right =
                root.querySelector(
                    '[data-balance-right]'
                );

            const rightColumn =
                root.querySelector(
                    '[data-right-column]'
                );

            const source =
                root.querySelector(
                    '[data-balance-source]'
                );

            if (
                !fixedLeft
                || !left
                || !right
                || !source
            ) {
                return;
            }

            const sourceSections =
                Array.from(
                    source.querySelectorAll(
                        '[data-balance-section]'
                    )
                );

            /*
             * Mobile: mantém ordem natural em uma coluna.
             */
            if (
                window.innerWidth <= 900
                || root.clientWidth <= 760
            ) {
                const atoms = [];

                sourceSections.forEach(
                    (section, sectionIndex) => {
                        Array
                            .from(
                                section.querySelectorAll(
                                    '[data-balance-entry]'
                                )
                            )
                            .forEach(
                                (element, entryIndex) => {
                                    atoms.push({
                                        sectionIndex,
                                        entryIndex,
                                        element,
                                    });
                                }
                            );
                    }
                );

                this.renderRange(
                    left,
                    atoms,
                    sourceSections
                );

                right.replaceChildren();

                rightColumn?.classList.add(
                    'is-empty'
                );

                return;
            }

            source.style.width =
                `${left.clientWidth}px`;

            /*
             * Precisamos medir DEPOIS de definir a mesma largura da coluna real.
             */
            const sectionMetrics =
                [];

            const atoms =
                [];

            sourceSections.forEach(
                (section, sectionIndex) => {
                    const title =
                        section.querySelector(
                            '[data-balance-title]'
                        );

                    const titleHeight =
                        this.outerHeight(
                            title
                        );

                    const entries =
                        Array.from(
                            section.querySelectorAll(
                                '[data-balance-entry]'
                            )
                        );

                    const metrics =
                        entries.map(
                            element => ({
                                element,
                                height:
                                    this.outerHeight(
                                        element
                                    ),
                            })
                        );

                    sectionMetrics.push({
                        titleHeight,
                        entries:
                            metrics,
                    });

                    metrics.forEach(
                        (metric, entryIndex) => {
                            atoms.push({
                                sectionIndex,
                                entryIndex,
                                element:
                                    metric.element,
                                height:
                                    metric.height,
                            });
                        }
                    );
                }
            );

            if (!atoms.length) {
                left.replaceChildren();
                right.replaceChildren();

                rightColumn?.classList.add(
                    'is-empty'
                );

                return;
            }

            const rangeHeight =
                (
                    range,
                    suppressFirstTitle = false
                ) => {
                    if (!range.length) {
                        return 0;
                    }

                    let height = 0;
                    let previousSection =
                        null;

                    let sectionCount =
                        0;

                    for (const atom of range) {
                        if (
                            previousSection
                            !== atom.sectionIndex
                        ) {
                            previousSection =
                                atom.sectionIndex;

                            const hideTitle =
                                suppressFirstTitle
                                && sectionCount === 0;

                            if (!hideTitle) {
                                height +=
                                    sectionMetrics[
                                        atom.sectionIndex
                                    ]?.titleHeight
                                    ?? 0;
                            }

                            sectionCount++;
                        }

                        height +=
                            atom.height;
                    }

                    return height;
                };

            const fixedHeight =
                this.outerHeight(
                    fixedLeft
                );

            let bestIndex =
                0;

            let bestScore =
                Number.POSITIVE_INFINITY;

            /*
             * Avalia todos os cortes possíveis e escolhe aquele que deixa
             * as duas alturas finais mais próximas.
             */
            for (
                let index = 0;
                index <= atoms.length;
                index++
            ) {
                const leftAtoms =
                    atoms.slice(
                        0,
                        index
                    );

                const rightAtoms =
                    atoms.slice(
                        index
                    );

                const continuesSameSection =
                    leftAtoms.length > 0
                    && rightAtoms.length > 0
                    && leftAtoms[
                        leftAtoms.length - 1
                    ].sectionIndex
                        ===
                    rightAtoms[0]
                        .sectionIndex;

                const leftHeight =
                    fixedHeight
                    + rangeHeight(
                        leftAtoms
                    );

                const rightHeight =
                    rangeHeight(
                        rightAtoms,
                        continuesSameSection
                    );

                const score =
                    Math.abs(
                        leftHeight
                        - rightHeight
                    );

                if (score < bestScore) {
                    bestScore =
                        score;

                    bestIndex =
                        index;
                }
            }

            const leftAtoms =
                atoms.slice(
                    0,
                    bestIndex
                );

            const rightAtoms =
                atoms.slice(
                    bestIndex
                );

            const continuesSameSection =
                leftAtoms.length > 0
                && rightAtoms.length > 0
                && leftAtoms[
                    leftAtoms.length - 1
                ].sectionIndex
                    ===
                rightAtoms[0]
                    .sectionIndex;

            this.renderRange(
                left,
                leftAtoms,
                sourceSections
            );

            this.renderRange(
                right,
                rightAtoms,
                sourceSections,
                continuesSameSection
            );

            rightColumn?.classList.toggle(
                'is-empty',
                rightAtoms.length === 0
            );
        },
    }"
    @character-original-drawer-state.window="originalDrawerOpen = !!$event.detail?.open"
    x-effect="document.body.classList.toggle('character-monster-active', true); document.body.classList.toggle('character-monster-view-visible', monsterVisible && !originalDrawerOpen)"
    x-init="initMonsterView(); initBalance()"
    class="contents"
>
    <template x-teleport="body">
        <div
            class="monster-sheet-v18-overlay character-sheet-theme"
            x-show="monsterVisible && !originalDrawerOpen"
            x-cloak
            role="dialog"
            aria-modal="true"
            aria-label="Ficha da transformação {{ $transformation->name }}"
        
            data-theme="{{ $characterTheme }}">
            <div
                class="monster-sheet-v18-backdrop"
                aria-hidden="true"
            ></div>


            <div class="monster-sheet-v18-shell">

                <button
                    type="button"
                    class="monster-sheet-v18-view-original"
                    @click="hideMonsterView()"
                    title="Ver a ficha original sem encerrar a transformação"
                    aria-label="Ver ficha original sem destransformar"
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m11 19-7-7 7-7"></path><path d="M4 12h16"></path>
                </svg></button>

                <button
                    type="button"
                    @click="
                        deTransform()
                    "
                    :disabled="
                        busy
                        || deTransforming
                    "
                    class="monster-sheet-v18-untransform"
                    title="Voltar à Forma Normal"
                >
                    <span
                        x-show="
                            !deTransforming
                        "
                    >
                        Destransformar
                    </span>

                    <span
                        x-show="
                            deTransforming
                        "
                        x-cloak
                    >
                        Voltando...
                    </span>
                </button>


                <div class="monster-sheet-v18-panel">

                    @if (!empty($sheet))

                        <div
                            class="monster-sheet-v18-sheet"
                            data-balance-root="{{ $balanceId }}"
                            @click="
                                handleTrackerClick(
                                    $event
                                )
                            "
                        >

                            <div class="monster-sheet-v18-columns">

                                {{-- ============================================
                                     COLUNA ESQUERDA
                                ============================================= --}}
                                <div class="monster-sheet-v18-column">

                                    <div
                                        class="monster-sheet-v18-fixed"
                                        data-fixed-left
                                    >

                                        <div class="monster-sheet-v18-identity">

                                            <div class="min-w-0">

                                                <h1 class="monster-sheet-v18-title">
                                                    {{ $monsterName }}
                                                </h1>

                                                @if ($monsterSubtitle !== '')
                                                    <p class="monster-sheet-v18-subtitle">
                                                        {{ $monsterSubtitle }}
                                                    </p>
                                                @endif

                                            </div>


                                            <div class="monster-sheet-v18-portrait">

                                                @if ($imageUrl)
                                                    <img
                                                        src="{{ $imageUrl }}"
                                                        alt="Retrato de {{ $transformation->name }}"
                                                    >
                                                @else
                                                    {{ $initials }}
                                                @endif

                                            </div>

                                        </div>


                                        <hr class="monster-sheet-v18-rule">


                                        <div class="monster-sheet-v18-core-line">
                                            <strong>
                                                Classe de Armadura:
                                            </strong>

                                            <span>
                                                {{ $armorClass }}
                                            </span>
                                        </div>


                                        <div
                                            class="monster-sheet-v18-hp-wrap"
                                            @click.outside="
                                                closeHpEditor()
                                            "
                                        >

                                            <button
                                                type="button"
                                                class="monster-sheet-v18-hp-summary"
                                                @click="
                                                    toggleHpEditor()
                                                "
                                                :aria-expanded="
                                                    hpEditorOpen
                                                        ? 'true'
                                                        : 'false'
                                                "
                                                title="Clique para aplicar dano ou cura"
                                            >
                                                <span class="monster-sheet-v18-hp-label">
                                                    Pontos de Vida:
                                                </span>

                                                <span class="monster-sheet-v18-hp-stat">
                                                    <strong x-text="hpCurrent"></strong>/<strong x-text="hpMax"></strong>
                                                </span>
                                            </button>


                                            <div
                                                x-show="
                                                    hpEditorOpen
                                                "
                                                x-cloak
                                                x-transition.opacity.duration.120ms
                                                class="monster-sheet-v18-hp-editor"
                                            >

                                                <label class="monster-sheet-v18-hp-field">

                                                    <span class="monster-sheet-v18-hp-field-label">
                                                        Quanto de dano
                                                    </span>

                                                    <span class="monster-sheet-v18-hp-control">

                                                        <input
                                                            x-ref="damageAmount"
                                                            type="number"
                                                            min="1"
                                                            step="1"
                                                            inputmode="numeric"
                                                            x-model="damageAmount"
                                                            @keydown.enter.prevent="
                                                                submitManualDamage()
                                                            "
                                                            placeholder="0"
                                                            :disabled="
                                                                busy
                                                                || deTransforming
                                                            "
                                                        >

                                                        <button
                                                            type="button"
                                                            @click="
                                                                submitManualDamage()
                                                            "
                                                            :disabled="
                                                                busy
                                                                || deTransforming
                                                                || !damageAmount
                                                            "
                                                        >
                                                            Dano
                                                        </button>

                                                    </span>

                                                </label>


                                                <label class="monster-sheet-v18-hp-field">

                                                    <span class="monster-sheet-v18-hp-field-label">
                                                        Quanto de Cura
                                                    </span>

                                                    <span class="monster-sheet-v18-hp-control">

                                                        <input
                                                            type="number"
                                                            min="1"
                                                            step="1"
                                                            inputmode="numeric"
                                                            x-model="healingAmount"
                                                            @keydown.enter.prevent="
                                                                submitManualHealing()
                                                            "
                                                            placeholder="0"
                                                            :disabled="
                                                                busy
                                                                || deTransforming
                                                            "
                                                        >

                                                        <button
                                                            type="button"
                                                            @click="
                                                                submitManualHealing()
                                                            "
                                                            :disabled="
                                                                busy
                                                                || deTransforming
                                                                || !healingAmount
                                                            "
                                                        >
                                                            Cura
                                                        </button>

                                                    </span>

                                                </label>

                                            </div>

                                        </div>


                                        <div class="monster-sheet-v18-movement-row">

                                            <div class="monster-sheet-v18-movement-left">
                                                <strong>
                                                    Deslocamento:
                                                </strong>

                                                <span>
                                                    {{ $speed }}
                                                </span>
                                            </div>


                                            <div class="monster-sheet-v18-initiative">
                                                <strong>
                                                    Iniciativa:
                                                </strong>

                                                <span>
                                                    {{ $signed(
                                                        $initiative
                                                    ) }}
                                                </span>
                                            </div>

                                        </div>


                                        @if ($jumpText !== '')
                                            <div class="monster-sheet-v18-jump-row">
                                                <strong>
                                                    Salto:
                                                </strong>

                                                <span>
                                                    {{ $jumpText }}
                                                </span>
                                            </div>
                                        @endif


                                        @if (
                                            $challengeRating !== null
                                            && trim(
                                                (string) $challengeRating
                                            ) !== ''
                                        )
                                            <div class="monster-sheet-v18-core-line">

                                                <strong>
                                                    Nível de Desafio:
                                                </strong>

                                                <span>
                                                    {{ $challengeRating }}
                                                </span>

                                                @if (
                                                    $proficiencyBonus
                                                    !== null
                                                )
                                                    <span>
                                                        (PB
                                                        {{ $signed(
                                                            $proficiencyBonus
                                                        ) }})
                                                    </span>
                                                @endif

                                            </div>
                                        @endif


                                        <p
                                            x-show="
                                                error
                                            "
                                            x-cloak
                                            class="monster-sheet-v18-error"
                                            x-text="error"
                                        ></p>


                                        <hr class="monster-sheet-v18-rule">


                                        @if ($abilities->isNotEmpty())

                                            <div class="monster-sheet-v18-abilities">

                                                @foreach (
                                                    $abilityColumns
                                                    as $column
                                                )
                                                    @php
                                                        $column =
                                                            collect(
                                                                $column
                                                            )
                                                                ->filter()
                                                                ->values();
                                                    @endphp

                                                    @if ($column->isNotEmpty())

                                                        <div class="monster-sheet-v18-ability-group">

                                                            <div class="monster-sheet-v18-ability-head">
                                                                <span></span>
                                                                <span>Mod</span>
                                                                <span>Salv</span>
                                                            </div>

                                                            @foreach (
                                                                $column
                                                                as $ability
                                                            )
                                                                <div class="monster-sheet-v18-ability-row">

                                                                    <div class="monster-sheet-v18-ability-name">

                                                                        <span>
                                                                            {{ $ability['short'] }}
                                                                        </span>

                                                                        <span class="monster-sheet-v18-ability-score">
                                                                            {{ $ability['score'] }}
                                                                        </span>

                                                                    </div>

                                                                    <span>
                                                                        {{ $signed(
                                                                            $ability['modifier']
                                                                        ) }}
                                                                    </span>

                                                                    <span>
                                                                        {{ $signed(
                                                                            $ability['save']
                                                                        ) }}
                                                                    </span>

                                                                </div>
                                                            @endforeach

                                                        </div>

                                                    @endif
                                                @endforeach

                                            </div>

                                        @endif


                                        @if ($details->isNotEmpty())

                                            <div class="monster-sheet-v18-details">

                                                @foreach (
                                                    $details
                                                    as $detail
                                                )
                                                    <div class="monster-sheet-v18-detail">

                                                        <strong>
                                                            {{ $detail['label'] }}:
                                                        </strong>

                                                        {{ $detail['value'] }}

                                                    </div>
                                                @endforeach

                                            </div>

                                        @endif

                                    </div>


                                    <div
                                        class="monster-sheet-v18-dynamic"
                                        data-balance-left
                                    ></div>

                                </div>


                                {{-- ============================================
                                     COLUNA DIREITA
                                ============================================= --}}
                                <div
                                    class="
                                        monster-sheet-v18-column
                                        monster-sheet-v18-column--right
                                    "
                                    data-right-column
                                >
                                    <div
                                        class="monster-sheet-v18-dynamic"
                                        data-balance-right
                                    ></div>
                                </div>

                            </div>


                            {{-- ================================================
                                 FONTE INVISÍVEL PARA O BALANCEADOR
                            ================================================= --}}
                            <div
                                class="monster-sheet-v18-source"
                                data-balance-source
                                aria-hidden="true"
                            >

                                @foreach (
                                    $sections
                                    as $section
                                )
                                    <section
                                        class="monster-sheet-v18-section"
                                        data-balance-section
                                    >

                                        <h2
                                            class="monster-sheet-v18-section-title"
                                            data-balance-title
                                        >
                                            {{ $section['title'] }}
                                        </h2>


                                        @foreach (
                                            $section['entries']
                                            as $entry
                                        )
                                            @php
                                                $entryVariant =
                                                    (string) (
                                                        $entry['variant']
                                                        ?? 'entry'
                                                    );

                                                $entryTracker =
                                                    is_array(
                                                        $entry['tracker']
                                                        ?? null
                                                    )
                                                        ? $entry['tracker']
                                                        : null;

                                                $entryTrackerKey =
                                                    (string) (
                                                        $entry['id']
                                                        ?? sha1(
                                                            (
                                                                $entry['title']
                                                                ?? ''
                                                            )
                                                            . '|'
                                                            . (
                                                                $entry['html']
                                                                ?? ''
                                                            )
                                                        )
                                                    );
                                            @endphp

                                            <div
                                                class="
                                                    monster-sheet-v18-entry
                                                    {{ $entryVariant === 'multiattack'
                                                        ? 'is-multiattack'
                                                        : ''
                                                    }}
                                                "
                                                data-balance-entry
                                            >

                                                @if (
                                                    trim(
                                                        (string) (
                                                            $entry['title']
                                                            ?? ''
                                                        )
                                                    )
                                                    !== ''
                                                )
                                                    <h3 class="monster-sheet-v18-entry-title">
                                                        {{ rtrim(
                                                            $entry['title'],
                                                            '. '
                                                        ) }}.
                                                    </h3>
                                                @endif


                                                @if ($entryTracker)
                                                    <span
                                                        class="monster-sheet-v18-tracker"
                                                        data-tracker-key="{{ $entryTrackerKey }}"
                                                        data-tracker-initial="{{ $entryTracker['initial'] }}"
                                                        data-tracker-capacity="{{ $entryTracker['capacity'] }}"
                                                        data-tracker-reset="{{ $entryTracker['reset'] }}"
                                                        data-tracker-label="{{ $entryTracker['label'] }}"
                                                        title="Tracker temporário desta transformação"
                                                    >
                                                        <button
                                                            type="button"
                                                            data-tracker-action="decrement"
                                                            title="Gastar 1 uso"
                                                        >
                                                            −
                                                        </button>

                                                        <strong
                                                            class="monster-sheet-v18-tracker-value"
                                                            data-tracker-value
                                                        >
                                                            {{ $entryTracker['reset'] === 'recharge'
                                                                ? 'Pronto · ' . $entryTracker['label']
                                                                : $entryTracker['initial'] . '/' . $entryTracker['label']
                                                            }}
                                                        </strong>

                                                        <button
                                                            type="button"
                                                            data-tracker-action="increment"
                                                            title="Recuperar 1 uso"
                                                        >
                                                            +
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="monster-sheet-v18-tracker-reset"
                                                            data-tracker-action="reset"
                                                            title="Restaurar o tracker"
                                                        >
                                                            ↺
                                                        </button>
                                                    </span>
                                                @endif


                                                @if (
                                                    trim(
                                                        strip_tags(
                                                            (string) (
                                                                $entry['html']
                                                                ?? ''
                                                            )
                                                        )
                                                    )
                                                    !== ''
                                                )
                                                    <div class="monster-sheet-v18-entry-body">
                                                        {!! $entry['html'] !!}
                                                    </div>
                                                @endif

                                            </div>
                                        @endforeach

                                    </section>
                                @endforeach

                            </div>

                        </div>

                    @else
                        <div
                            class="
                                px-6
                                py-10
                                text-center
                                font-serif
                                text-[13px]
                                text-[#755847]
                            "
                        >
                            @if ($unsupportedFormat)
                                Esta transformação está vinculada a uma criatura de formato externo.
                                Transformações de criatura aceitam somente fichas criadas pelo Construtor de NPC do Spellbound.
                                Use “Destransformar” e edite a transformação para escolher uma criatura compatível.
                            @else
                                Não foi possível interpretar a ficha desta criatura.
                                Use “Destransformar” para voltar à Forma Normal.
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <button
            type="button"
            x-show="!monsterVisible && !originalDrawerOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="monster-sheet-v18-reopen character-sheet-theme"
            @click="showMonsterView()"
            title="Reabrir a ficha da criatura sem encerrar a transformação"
            aria-label="Voltar à ficha da criatura {{ $transformation->name }}"
        >
            <span class="monster-sheet-v18-reopen-symbol" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 12h16M13 5l7 7-7 7" />
                </svg>
            </span>
            <span class="monster-sheet-v18-reopen-copy">
                <span class="monster-sheet-v18-reopen-kicker">Transformação ativa</span>
                <span class="monster-sheet-v18-reopen-name">Ver criatura · {{ $transformation->name }}</span>
            </span>
            <svg class="monster-sheet-v18-reopen-chevron" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m9 18 6-6-6-6" />
            </svg>
        </button>
    </template>

    <template x-teleport="body">
        <div
            x-show="concentrationCheck"
            x-cloak
            class="monster-sheet-v18-concentration character-sheet-theme"
            role="alertdialog"
            aria-live="assertive"
            aria-label="Teste de concentração"
            @keydown.escape.window="
                if (concentrationCheck) {
                    closeConcentrationCheck()
                }
            "
        
            data-theme="{{ $characterTheme }}">
            <header class="monster-sheet-v18-concentration-head">

                <div class="min-w-0">
                    <span class="monster-sheet-v18-concentration-kicker">
                        Concentração
                    </span>

                    <h3 class="monster-sheet-v18-concentration-title">
                        Salvamento de Constituição
                    </h3>
                </div>


                <button
                    type="button"
                    class="monster-sheet-v18-concentration-close"
                    @click="
                        closeConcentrationCheck()
                    "
                    :disabled="
                        concentrationRolling
                    "
                    title="Fechar"
                    aria-label="Fechar teste de concentração"
                >
                    ×
                </button>

            </header>


            <div class="monster-sheet-v18-concentration-body">

                <div class="monster-sheet-v18-concentration-facts">

                    <span class="monster-sheet-v18-concentration-fact">
                        CD
                        <span
                            class="ml-1"
                            x-text="
                                concentrationCheck
                                    ?.dc
                                ?? 10
                            "
                        ></span>
                    </span>

                    <span class="monster-sheet-v18-concentration-fact">
                        Dano
                        <span
                            class="ml-1"
                            x-text="
                                concentrationCheck
                                    ?.damage
                                ?? 0
                            "
                        ></span>
                    </span>

                    <span class="monster-sheet-v18-concentration-fact">
                        Mod
                        <span
                            class="ml-1"
                            x-text="
                                (() => {
                                    const value =
                                        parseInt(
                                            concentrationCheck
                                                ?.modifier
                                            ?? 0
                                        )
                                        || 0;

                                    return value >= 0
                                        ? `+${value}`
                                        : `${value}`;
                                })()
                            "
                        ></span>
                    </span>

                    <template
                        x-if="
                            Number(
                                concentrationCheck
                                    ?.exhaustion_penalty
                                ?? 0
                            )
                            > 0
                        "
                    >
                        <span class="monster-sheet-v18-concentration-fact">
                            Exaustão
                            <span
                                class="ml-1"
                                x-text="
                                    `-${concentrationCheck.exhaustion_penalty}`
                                "
                            ></span>
                        </span>
                    </template>

                </div>


                <p class="monster-sheet-v18-concentration-copy">
                    O dano exige um teste para manter a concentração.
                    A falha é informada, mas não encerra a transformação automaticamente.
                </p>


                <div class="monster-sheet-v18-concentration-actions">

                    <template x-if="!concentrationRoll">
                        <button
                            type="button"
                            class="monster-sheet-v18-concentration-roll"
                            @click="
                                rollConcentrationCheck()
                            "
                            :disabled="
                                concentrationRolling
                            "
                        >
                            <span
                                x-show="
                                    !concentrationRolling
                                "
                            >
                                Rolar Constituição
                            </span>

                            <span
                                x-show="
                                    concentrationRolling
                                "
                                x-cloak
                            >
                                Rolando...
                            </span>
                        </button>
                    </template>


                    <template x-if="concentrationRoll">
                        <div class="monster-sheet-v18-concentration-result">

                            <x-dice.polyhedral
                                :sides="20"
                                :size="42"
                            >
                                <span
                                    class="tabular-nums"
                                    x-text="
                                        concentrationRoll
                                            ?.die
                                        ?? ''
                                    "
                                ></span>
                            </x-dice.polyhedral>


                            <span class="monster-sheet-v18-concentration-result-copy">

                                <span class="monster-sheet-v18-concentration-result-label">
                                    Resultado
                                </span>

                                <strong
                                    class="monster-sheet-v18-concentration-result-total"
                                    :class="{
                                        'is-success':
                                            concentrationRoll
                                                ?.passed,

                                        'is-failure':
                                            concentrationRoll
                                                && !concentrationRoll.passed
                                    }"
                                >
                                    <span
                                        x-text="
                                            concentrationRoll
                                                ?.total
                                            ?? ''
                                        "
                                    ></span>

                                    <span>
                                        —
                                    </span>

                                    <span
                                        x-text="
                                            concentrationRoll
                                                ?.passed
                                                ? 'Sucesso'
                                                : 'Falha'
                                        "
                                    ></span>
                                </strong>

                            </span>

                        </div>
                    </template>


                    <button
                        type="button"
                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-[0.05em]
                            text-[#755847]
                            hover:text-[#53150f]
                        "
                        @click="
                            closeConcentrationCheck()
                        "
                        :disabled="
                            concentrationRolling
                        "
                    >
                        Fechar
                    </button>

                </div>


                <p
                    x-show="
                        concentrationError
                    "
                    x-cloak
                    class="monster-sheet-v18-concentration-error"
                    x-text="concentrationError"
                ></p>

            </div>
        </div>
    </template>
</div>