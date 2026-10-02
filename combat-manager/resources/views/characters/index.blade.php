<x-app-layout>

    @php
        /*
        |--------------------------------------------------------------------------
        | CAMPANHAS DOS PERSONAGENS
        |--------------------------------------------------------------------------
        |
        | O Index precisa mostrar a campanha sem exigir mudança no controller.
        | Fazemos uma consulta única para todos os Characters da tela e agrupamos
        | por character_id, evitando uma consulta por card.
        |
        */

        $characterIds = $characters
            ->pluck('id')
            ->filter()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RELAÇÕES USADAS PELO RESUMO
        |--------------------------------------------------------------------------
        |
        | A CA real do Spellbound não é apenas character_combat.armor_class.
        | Ela depende dos atributos efetivos, configuração de defesa base e
        | equipamentos do inventário. Carregamos tudo em lote para evitar N+1.
        |
        */

        if (method_exists($characters, 'loadMissing')) {
            $characters->loadMissing([
                'abilities',
                'items',
                'classes',
                'combat',
            ]);
        }

        $campaignsByCharacter = collect();

        if ($characterIds->isNotEmpty()) {
            $campaignsByCharacter =
                \Illuminate\Support\Facades\DB::table('campaign_characters')
                    ->join(
                        'campaigns',
                        'campaigns.id',
                        '=',
                        'campaign_characters.campaign_id'
                    )
                    ->whereIn(
                        'campaign_characters.character_id',
                        $characterIds
                    )
                    ->select([
                        'campaign_characters.character_id',
                        'campaign_characters.is_active',
                        'campaigns.id',
                        'campaigns.name',
                    ])
                    ->orderByDesc('campaign_characters.is_active')
                    ->orderBy('campaigns.name')
                    ->get()
                    ->groupBy('character_id');
        }
    @endphp

    <style>
        /*
        |--------------------------------------------------------------------------
        | INDEX DE PERSONAGENS — V3
        |--------------------------------------------------------------------------
        |
        | O card deixa de ser um thumbnail genérico e passa a funcionar como um
        | pequeno dossiê do personagem: retrato, identidade, campanha e combate.
        |
        */

        .characters-index-v2 {
            --index-paper: #fffdf8;
            --index-paper-alt: #f7f0e6;
            --index-band: #eadbc8;
            --index-band-soft: #f0e4d5;
            --index-line: rgba(176, 140, 98, .34);
            --index-line-strong: rgba(140, 98, 57, .45);
            --index-wine: #53150f;
            --index-wine-soft: #6b1d14;
            --index-gold: #8c6239;
            --index-text: #34231b;
            --index-muted: #755847;
        }

        .character-roster-card {
            position: relative;
            display: grid;
            grid-template-columns: 214px minmax(0, 1fr);
            min-height: 328px;
            overflow: hidden;
            border: 1px solid var(--index-line);
            border-radius: 18px;
            background:
                linear-gradient(
                    180deg,
                    rgba(255, 253, 248, .98),
                    rgba(248, 242, 233, .96)
                );
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.84),
                0 6px 18px rgba(83,21,15,.055);
            transition:
                transform .18s ease,
                border-color .18s ease,
                box-shadow .18s ease;
        }

        .character-roster-card:hover {
            transform: translateY(-2px);
            border-color: rgba(140, 98, 57, .52);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.9),
                0 12px 28px rgba(83,21,15,.09);
        }

        .character-roster-card.is-dead {
            border-color: rgba(83, 21, 15, .48);
        }

        .character-roster-portrait {
            position: relative;
            min-height: 100%;
            overflow: hidden;
            border-right: 1px solid var(--index-line);
            background:
                radial-gradient(
                    circle at 50% 18%,
                    rgba(255,255,255,.62),
                    transparent 40%
                ),
                linear-gradient(
                    180deg,
                    #efe4d4 0%,
                    #e4d2bd 100%
                );
        }

        .character-roster-portrait::after {
            content: '';
            pointer-events: none;
            position: absolute;
            inset: 0;
            z-index: 2;
            background:
                linear-gradient(
                    180deg,
                    transparent 55%,
                    rgba(42, 23, 18, .12) 78%,
                    rgba(42, 23, 18, .34) 100%
                );
        }

        .character-roster-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            transition:
                transform .45s ease,
                filter .25s ease;
        }

        .character-roster-card:hover .character-roster-image {
            transform: scale(1.025);
        }

        .character-roster-card.is-dead .character-roster-image {
            filter:
                grayscale(.82)
                saturate(.35)
                contrast(.95)
                brightness(.70);
        }

        .character-roster-level {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 6;
            display: inline-flex;
            min-width: 48px;
            height: 48px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(176,140,98,.45);
            border-radius: 999px;
            background: rgba(255,253,248,.92);
            box-shadow:
                inset 0 0 0 3px rgba(234,219,200,.48),
                0 4px 12px rgba(42,23,18,.10);
            backdrop-filter: blur(5px);
            color: var(--index-wine);
        }

        .character-roster-level small {
            font-size: 6px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: var(--index-gold);
        }

        .character-roster-level strong {
            margin-top: 2px;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 17px;
            font-weight: 900;
            line-height: 1;
        }

        .character-roster-photo-caption {
            position: absolute;
            right: 12px;
            bottom: 12px;
            left: 12px;
            z-index: 6;
        }

        .character-roster-photo-caption span {
            display: inline-flex;
            max-width: 100%;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 999px;
            background: rgba(42,23,18,.58);
            padding: 5px 9px;
            color: #fffaf2;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .06em;
            backdrop-filter: blur(5px);
        }

        .character-roster-status-dot {
            width: 6px;
            height: 6px;
            flex: 0 0 auto;
            border-radius: 999px;
            background: #c89c62;
        }

        .character-roster-status-dot.healthy {
            background: #4f8d75;
        }

        .character-roster-status-dot.wounded {
            background: #bd7a42;
        }

        .character-roster-status-dot.critical,
        .character-roster-status-dot.down {
            background: #b44736;
        }

        .character-roster-status-dot.dead {
            background: #e5c7c0;
        }

        .character-roster-body {
            display: flex;
            min-width: 0;
            flex-direction: column;
            padding: 18px 19px 16px;
        }

        .character-roster-kicker {
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--index-gold);
        }

        .character-roster-name {
            margin-top: 2px;
            overflow: hidden;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.08;
            color: var(--index-wine);
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .character-roster-summary {
            margin-top: 5px;
            color: var(--index-muted);
            font-size: 10.5px;
            font-weight: 700;
            line-height: 1.45;
        }

        .character-roster-campaign {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 9px;
            margin-top: 12px;
            border-top: 1px solid rgba(176,140,98,.22);
            border-bottom: 1px solid rgba(176,140,98,.22);
            background: rgba(234,219,200,.34);
            padding: 8px 9px;
        }

        .character-roster-campaign-icon {
            display: flex;
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(140,98,57,.28);
            border-radius: 8px;
            background: rgba(255,253,248,.72);
            color: var(--index-wine-soft);
        }

        .character-roster-campaign-copy {
            min-width: 0;
            flex: 1;
        }

        .character-roster-campaign-copy small {
            display: block;
            font-size: 6.5px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: var(--index-gold);
        }

        .character-roster-campaign-copy strong {
            display: block;
            margin-top: 3px;
            overflow: hidden;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.1;
            color: var(--index-wine);
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .character-roster-campaign-more {
            flex: 0 0 auto;
            font-size: 8px;
            font-weight: 900;
            color: var(--index-gold);
        }

        .character-roster-identity {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 7px 12px;
            margin-top: 12px;
        }

        .character-roster-detail {
            min-width: 0;
        }

        .character-roster-detail dt {
            font-size: 6.5px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #9a7453;
        }

        .character-roster-detail dd {
            margin-top: 3px;
            overflow: hidden;
            color: var(--index-text);
            font-size: 10px;
            font-weight: 800;
            line-height: 1.25;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .character-roster-combat {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            overflow: hidden;
            margin-top: 13px;
            border: 1px solid rgba(176,140,98,.28);
            border-radius: 10px;
            background: rgba(247,240,230,.70);
        }

        .character-roster-stat {
            min-width: 0;
            padding: 7px 5px 8px;
            text-align: center;
        }

        .character-roster-stat + .character-roster-stat {
            border-left: 1px solid rgba(176,140,98,.23);
        }

        .character-roster-stat span {
            display: block;
            font-size: 6px;
            font-weight: 900;
            letter-spacing: .11em;
            text-transform: uppercase;
            color: var(--index-gold);
        }

        .character-roster-stat strong {
            display: block;
            margin-top: 2px;
            overflow: hidden;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.1;
            color: var(--index-wine);
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .character-roster-health {
            margin-top: 10px;
        }

        .character-roster-health-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 5px;
        }

        .character-roster-health-head span {
            font-size: 6.5px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--index-gold);
        }

        .character-roster-health-head strong {
            font-size: 9px;
            font-weight: 900;
            color: var(--index-wine);
        }

        .character-roster-health-track {
            height: 5px;
            overflow: hidden;
            border-radius: 999px;
            background: #e0d1bd;
        }

        .character-roster-health-fill {
            height: 100%;
            border-radius: inherit;
            background:
                linear-gradient(
                    90deg,
                    #8c6239,
                    #c89c62
                );
            transition: width .3s ease;
        }

        .character-roster-health-fill.wounded {
            background:
                linear-gradient(
                    90deg,
                    #8f3a2d,
                    #b5654f
                );
        }

        .character-roster-health-fill.critical,
        .character-roster-health-fill.down,
        .character-roster-health-fill.dead {
            background:
                linear-gradient(
                    90deg,
                    #6b1d14,
                    #9f3025
                );
        }

        .character-roster-footer {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
            padding-top: 14px;
        }

        .character-roster-open {
            display: inline-flex;
            min-height: 38px;
            flex: 1;
            align-items: center;
            justify-content: center;
            border: 1px solid #6b1d14;
            border-radius: 9px;
            background: #6b1d14;
            padding: 0 14px;
            color: #fffaf2;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
            transition:
                background .15s ease,
                transform .15s ease;
        }

        .character-roster-open:hover {
            transform: translateY(-1px);
            background: #53150f;
        }

        .character-roster-delete {
            display: flex;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(127,38,29,.20);
            border-radius: 9px;
            color: #7f261d;
            transition:
                background .15s ease,
                color .15s ease,
                border-color .15s ease;
        }

        .character-roster-delete:hover {
            border-color: #7f261d;
            background: #7f261d;
            color: #fffaf2;
        }

        /*
        |--------------------------------------------------------------------------
        | MEMORIAL — PERSONAGEM MORTO
        |--------------------------------------------------------------------------
        |
        | Não inventamos uma data de morte, pois o modelo atual não possui dead_at.
        | A tela memorial aparece somente quando o limite de falhas foi atingido.
        |
        */

        .character-roster-memorial {
            position: absolute;
            inset: 0;
            z-index: 8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(
                    circle at 50% 38%,
                    rgba(83,21,15,.36),
                    rgba(25,15,13,.86) 70%
                );
            padding: 18px;
            text-align: center;
            color: #fffaf2;
            backdrop-filter: blur(1px);
        }

        .character-roster-memorial-mark {
            display: flex;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(234,219,200,.46);
            border-radius: 999px;
            background: rgba(83,21,15,.42);
            box-shadow:
                0 0 0 5px rgba(234,219,200,.06);
        }

        .character-roster-memorial small {
            margin-top: 13px;
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .20em;
            text-transform: uppercase;
            color: #d7b99f;
        }

        .character-roster-memorial h3 {
            margin-top: 4px;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 21px;
            font-weight: 900;
            line-height: 1.05;
        }

        .character-roster-memorial p {
            margin-top: 7px;
            max-width: 170px;
            font-size: 8.5px;
            font-weight: 700;
            line-height: 1.45;
            color: #eadbc8;
        }

        .character-roster-memorial-rule {
            width: 44px;
            height: 1px;
            margin-top: 11px;
            background: rgba(234,219,200,.42);
        }

        .character-index-create {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #6b1d14;
            border-radius: 10px;
            background: #6b1d14;
            padding: 0 17px;
            color: #fffaf2;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .11em;
            text-transform: uppercase;
            transition:
                transform .15s ease,
                background .15s ease;
        }

        .character-index-create:hover {
            transform: translateY(-1px);
            background: #53150f;
        }

        @media (max-width: 720px) {
            .character-roster-card {
                grid-template-columns: minmax(0, 1fr);
            }

            .character-roster-portrait {
                min-height: 280px;
                border-right: 0;
                border-bottom: 1px solid var(--index-line);
            }

            .character-roster-body {
                padding: 16px;
            }
        }

        @media (max-width: 420px) {
            .character-roster-combat {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .character-roster-stat:nth-child(3) {
                border-left: 0;
                border-top: 1px solid rgba(176,140,98,.23);
            }

            .character-roster-stat:nth-child(4) {
                border-top: 1px solid rgba(176,140,98,.23);
            }
        }
    </style>


    <div

        x-data="{

            createOpen: {{ $errors->any() ? 'true' : 'false' }},

            advancedOpen: false,

            xpEnabled: {{ old('xp_enabled') ? 'true' : 'false' }},

            customProfEnabled: {{ old('custom_prof_enabled') ? 'true' : 'false' }},

            multiClass: {{ old('multi_class_enabled') ? 'true' : 'false' }},

            imagePreview: null,

            classes: [

                {

                    class: '{{ old('classes.0.class', '') }}',

                    subclass: '{{ old('classes.0.subclass', '') }}',

                    level: {{ (int) old('classes.0.level', 1) }}

                }

            ],

            addClass() {

                if (this.classes.length < 6) {

                    this.multiClass = true;

                    this.classes.push({

                        class: '',

                        subclass: '',

                        level: 1

                    });

                }

            },

            removeClass(index) {

                if (this.classes.length <= 1) return;

                this.classes.splice(index, 1);

                this.multiClass = this.classes.length > 1;

            },

            get totalLevel() {

                return this.classes.reduce(

                    (total, item) => total + (parseInt(item.level) || 0),

                    0

                );

            },

            updatePreview(event) {

                const file = event.target.files[0];

                if (!file) {

                    this.imagePreview = null;

                    return;

                }

                const reader = new FileReader();

                reader.onload = e => this.imagePreview = e.target.result;

                reader.readAsDataURL(file);

            },

            closeCreate() {

                this.createOpen = false;

            }

        }"

        class="characters-index-v2 min-h-full px-5 py-8 md:px-8"

        @keydown.escape.window="closeCreate()"

    >

        <div class="mx-auto max-w-[1480px]">

            {{-- ================================================================
                 CABEÇALHO
            ================================================================= --}}

            <header
                class="
                    mb-7
                    flex
                    flex-col
                    gap-5
                    border-b
                    border-[#cdbb9f]/55
                    pb-5
                    sm:flex-row
                    sm:items-end
                    sm:justify-between
                "
            >
                <div>
                    <p
                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-[0.23em]
                            text-[#8c6239]
                        "
                    >
                        Grimório de Aventureiro
                    </p>

                    <div class="mt-1 flex items-center gap-3">
                        <h1
                            class="
                                font-serif
                                text-[30px]
                                font-black
                                leading-none
                                text-[#53150f]
                            "
                        >
                            Personagens
                        </h1>

                        @if ($characters->isNotEmpty())
                            <span
                                class="
                                    inline-flex
                                    h-7
                                    min-w-7
                                    items-center
                                    justify-center
                                    rounded-full
                                    bg-[#eadbc8]
                                    px-2
                                    font-serif
                                    text-[11px]
                                    font-black
                                    text-[#6b1d14]
                                "
                            >
                                {{ $characters->count() }}
                            </span>
                        @endif
                    </div>

                    <p
                        class="
                            mt-2
                            max-w-xl
                            text-[11px]
                            leading-relaxed
                            text-[#755847]
                        "
                    >
                        Seus aventureiros, vínculos de campanha e estado atual em um só lugar.
                    </p>
                </div>

                <button
                    type="button"
                    @click="createOpen = true"
                    class="character-index-create"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 5v14M5 12h14"
                        />
                    </svg>

                    Forjar personagem
                </button>
            </header>


            @if (session('success'))
                <div
                    class="
                        mb-6
                        flex
                        items-center
                        gap-2
                        rounded-xl
                        border
                        border-[#8c6239]/20
                        bg-[#eadbc8]/45
                        px-4
                        py-3
                        text-[11px]
                        font-semibold
                        text-[#53150f]
                    "
                >
                    <span class="font-black">✓</span>
                    {{ session('success') }}
                </div>
            @endif


            {{-- ================================================================
                 LISTA
            ================================================================= --}}

            @if ($characters->isEmpty())

                <div
                    class="
                        rounded-2xl
                        border
                        border-dashed
                        border-[#cdbb9f]
                        bg-[#fffdf8]/75
                        px-6
                        py-16
                        text-center
                    "
                >
                    <div
                        class="
                            mx-auto
                            mb-4
                            flex
                            h-14
                            w-14
                            items-center
                            justify-center
                            rounded-full
                            border
                            border-[#cdbb9f]/60
                            bg-[#eadbc8]/55
                            text-[#6b1d14]
                        "
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.7"
                                d="M12 5v14M5 12h14"
                            />
                        </svg>
                    </div>

                    <h2
                        class="
                            font-serif
                            text-xl
                            font-black
                            text-[#53150f]
                        "
                    >
                        Nenhuma história começou ainda
                    </h2>

                    <p
                        class="
                            mx-auto
                            mt-2
                            max-w-md
                            text-[11px]
                            leading-relaxed
                            text-[#755847]
                        "
                    >
                        Forje seu primeiro personagem e deixe o Spellbound registrar o restante da jornada.
                    </p>

                    <button
                        type="button"
                        @click="createOpen = true"
                        class="character-index-create mt-5"
                    >
                        Forjar primeiro personagem
                    </button>
                </div>

            @else

                <div
                    class="
                        grid
                        grid-cols-1
                        gap-5
                        2xl:grid-cols-2
                    "
                >
                    @foreach ($characters as $character)

                        @php
                            $primaryClass =
                                $character->primary_class;

                            $combat =
                                $character->combat;

                            $currentHp =
                                max(
                                    0,
                                    (int) (
                                        $combat?->current_hp
                                        ?? 0
                                    )
                                );

                            $baseMaxHp =
                                max(
                                    0,
                                    (int) (
                                        $combat?->max_hp
                                        ?? 0
                                    )
                                );

                            $bonusMaxHp =
                                (int) (
                                    $combat?->temporary_max_hp
                                    ?? 0
                                );

                            $effectiveMaxHp =
                                max(
                                    0,
                                    $baseMaxHp
                                    +
                                    $bonusMaxHp
                                );

                            $temporaryHp =
                                max(
                                    0,
                                    (int) (
                                        $combat?->temporary_hp
                                        ?? 0
                                    )
                                );

                            $hpPercent =
                                $effectiveMaxHp > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            (int) round(
                                                (
                                                    $currentHp
                                                    /
                                                    $effectiveMaxHp
                                                )
                                                * 100
                                            )
                                        )
                                    )
                                    : 0;

                            $deathSuccesses =
                                max(
                                    0,
                                    (int) (
                                        $combat?->death_save_successes
                                        ?? 0
                                    )
                                );

                            $deathFailures =
                                max(
                                    0,
                                    (int) (
                                        $combat?->death_save_failures
                                        ?? 0
                                    )
                                );

                            $morquenEnabled =
                                (bool) data_get(
                                    $character->sheet_settings,
                                    'optional_rules.morquen',
                                    false
                                );

                            $deathFailureLimit =
                                $morquenEnabled
                                    ? 6
                                    : 3;

                            /*
                             * Zero PV não significa morte automaticamente.
                             * A morte acontece quando as falhas de death save
                             * atingem o limite da regra ativa.
                             */
                            $isDead =
                                $combat !== null
                                &&
                                $currentHp <= 0
                                &&
                                $deathFailures
                                    >=
                                    $deathFailureLimit;

                            $status =
                                'Estável';

                            $statusKey =
                                'healthy';

                            if ($isDead) {
                                $status =
                                    'Morto';

                                $statusKey =
                                    'dead';

                            } elseif (
                                $currentHp <= 0
                                &&
                                $deathSuccesses >= 3
                            ) {
                                $status =
                                    'Estabilizado';

                                $statusKey =
                                    'down';

                            } elseif ($currentHp <= 0) {
                                $status =
                                    'Caído';

                                $statusKey =
                                    'down';

                            } elseif ($hpPercent <= 25) {
                                $status =
                                    'Crítico';

                                $statusKey =
                                    'critical';

                            } elseif ($hpPercent <= 50) {
                                $status =
                                    'Ferido';

                                $statusKey =
                                    'wounded';
                            }

                            $usesExperience =
                                (bool) data_get(
                                    $combat?->overrides,
                                    'uses_experience',
                                    false
                                );

                            $campaignLinks =
                                $campaignsByCharacter->get(
                                    $character->id,
                                    collect()
                                );

                            $activeCampaign =
                                $campaignLinks->first(
                                    fn ($campaign) =>
                                        (bool) $campaign->is_active
                                );

                            $displayCampaign =
                                $activeCampaign
                                ??
                                $campaignLinks->first();

                            $campaignCount =
                                $campaignLinks->count();

                            $classSummary =
                                $character->classes
                                    ->map(
                                        function ($class) {
                                            $name =
                                                trim(
                                                    (string) (
                                                        $class->class
                                                        ?? ''
                                                    )
                                                );

                                            $level =
                                                (int) (
                                                    $class->level
                                                    ?? 0
                                                );

                                            $subclass =
                                                trim(
                                                    (string) (
                                                        $class->subclass
                                                        ?? ''
                                                    )
                                                );

                                            $label =
                                                $name !== ''
                                                    ? $name
                                                    : 'Classe';

                                            if ($level > 0) {
                                                $label .= ' ' . $level;
                                            }

                                            if ($subclass !== '') {
                                                $label .= ' · ' . $subclass;
                                            }

                                            return $label;
                                        }
                                    )
                                    ->filter()
                                    ->implode(' / ');

                            if ($classSummary === '') {
                                $classSummary =
                                    $primaryClass?->class
                                    ?? 'Classe não definida';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | CLASSE DE ARMADURA CALCULADA
                            |--------------------------------------------------------------------------
                            |
                            | O campo character_combat.armor_class é apenas um valor persistido
                            | legado/base e não representa necessariamente a CA que aparece na
                            | ficha. O Show calcula:
                            |
                            |   corpo = melhor armadura equipada OU defesa base
                            |   total = corpo + escudos equipados + bônus extras
                            |
                            | Abaixo reproduzimos a mesma regra em PHP para o Index.
                            |
                            */

                            $abilities =
                                $character->abilities;

                            $abilityTemporaryBonuses =
                                $abilities?->temporary_bonuses
                                ?? [];

                            if (is_string($abilityTemporaryBonuses)) {
                                $decoded =
                                    json_decode(
                                        $abilityTemporaryBonuses,
                                        true
                                    );

                                $abilityTemporaryBonuses =
                                    is_array($decoded)
                                        ? $decoded
                                        : [];
                            }

                            if (!is_array($abilityTemporaryBonuses)) {
                                $abilityTemporaryBonuses = [];
                            }

                            $abilityOverrides =
                                $abilities?->overrides
                                ?? [];

                            if (is_string($abilityOverrides)) {
                                $decoded =
                                    json_decode(
                                        $abilityOverrides,
                                        true
                                    );

                                $abilityOverrides =
                                    is_array($decoded)
                                        ? $decoded
                                        : [];
                            }

                            if (!is_array($abilityOverrides)) {
                                $abilityOverrides = [];
                            }

                            $abilityKeyMap = [
                                'str' => 'strength',
                                'dex' => 'dexterity',
                                'con' => 'constitution',
                                'int' => 'intelligence',
                                'wis' => 'wisdom',
                                'cha' => 'charisma',
                            ];

                            $abilityModifiers = [];

                            foreach (
                                $abilityKeyMap
                                as $shortKey => $fullKey
                            ) {
                                $baseScore =
                                    (int) (
                                        $abilities?->{$fullKey}
                                        ?? 10
                                    );

                                $temporaryBonus =
                                    (int) (
                                        $abilityTemporaryBonuses[$fullKey]
                                        ?? $abilityTemporaryBonuses[$shortKey]
                                        ?? 0
                                    );

                                $hasFullOverride =
                                    array_key_exists(
                                        $fullKey,
                                        $abilityOverrides
                                    )
                                    &&
                                    $abilityOverrides[$fullKey] !== null
                                    &&
                                    $abilityOverrides[$fullKey] !== '';

                                $hasShortOverride =
                                    array_key_exists(
                                        $shortKey,
                                        $abilityOverrides
                                    )
                                    &&
                                    $abilityOverrides[$shortKey] !== null
                                    &&
                                    $abilityOverrides[$shortKey] !== '';

                                if ($hasFullOverride) {
                                    $effectiveScore =
                                        (int) $abilityOverrides[$fullKey];

                                } elseif ($hasShortOverride) {
                                    $effectiveScore =
                                        (int) $abilityOverrides[$shortKey];

                                } else {
                                    $effectiveScore =
                                        $baseScore
                                        +
                                        $temporaryBonus;
                                }

                                $effectiveScore =
                                    max(
                                        1,
                                        $effectiveScore
                                    );

                                $abilityModifiers[$shortKey] =
                                    (int) floor(
                                        ($effectiveScore - 10)
                                        / 2
                                    );
                            }


                            /*
                             * Configuração da Defesa Base:
                             * overrides.armor.mode + overrides.armor.bonuses
                             */
                            $combatOverrides =
                                $combat?->overrides
                                ?? [];

                            if (is_string($combatOverrides)) {
                                $decoded =
                                    json_decode(
                                        $combatOverrides,
                                        true
                                    );

                                $combatOverrides =
                                    is_array($decoded)
                                        ? $decoded
                                        : [];
                            }

                            if (!is_array($combatOverrides)) {
                                $combatOverrides = [];
                            }

                            $armorConfig =
                                data_get(
                                    $combatOverrides,
                                    'armor',
                                    []
                                );

                            if (!is_array($armorConfig)) {
                                $armorConfig = [];
                            }

                            $storedArmorMode =
                                $armorConfig['mode']
                                ?? ['dex'];

                            $armorMode =
                                is_array($storedArmorMode)
                                    ? array_values(
                                        $storedArmorMode
                                    )
                                    : [
                                        $storedArmorMode,
                                    ];

                            if (
                                empty($armorMode)
                                &&
                                !array_key_exists(
                                    'mode',
                                    $armorConfig
                                )
                            ) {
                                $armorMode = [
                                    'dex',
                                ];
                            }

                            $baseDefenseBodyAc =
                                10
                                +
                                collect($armorMode)
                                    ->sum(
                                        fn ($ability) =>
                                            (int) (
                                                $abilityModifiers[$ability]
                                                ?? 0
                                            )
                                    );

                            $armorNamedBonusTotal =
                                collect(
                                    $armorConfig['bonuses']
                                    ?? []
                                )
                                    ->filter(
                                        fn ($bonus) =>
                                            trim(
                                                (string) (
                                                    $bonus['name']
                                                    ?? ''
                                                )
                                            ) !== ''
                                    )
                                    ->sum(
                                        fn ($bonus) =>
                                            (int) (
                                                $bonus['value']
                                                ?? 0
                                            )
                                    );


                            /*
                             * Normalizador de properties dos itens.
                             */
                            $itemProperties =
                                static function ($item): array {
                                    $properties =
                                        $item->properties
                                        ?? [];

                                    if (is_string($properties)) {
                                        $decoded =
                                            json_decode(
                                                $properties,
                                                true
                                            );

                                        $properties =
                                            is_array($decoded)
                                                ? $decoded
                                                : [];
                                    }

                                    return is_array($properties)
                                        ? $properties
                                        : [];
                                };

                            $armorAbilityMap = [
                                'strength' => 'str',
                                'constitution' => 'con',
                                'intelligence' => 'int',
                                'wisdom' => 'wis',
                                'charisma' => 'cha',

                                // Compatibilidade defensiva com registros antigos.
                                'str' => 'str',
                                'con' => 'con',
                                'int' => 'int',
                                'wis' => 'wis',
                                'cha' => 'cha',
                            ];

                            $armorBodyFromItem =
                                function (
                                    $item
                                ) use (
                                    $itemProperties,
                                    $abilityModifiers,
                                    $armorAbilityMap
                                ): ?int {
                                    $properties =
                                        $itemProperties(
                                            $item
                                        );

                                    $armor =
                                        data_get(
                                            $properties,
                                            'armor'
                                        );

                                    if (!is_array($armor)) {
                                        return null;
                                    }

                                    $baseAc =
                                        (int) (
                                            $armor['base_ac']
                                            ?? 10
                                        );

                                    $magicBonus =
                                        (int) (
                                            $armor['magic_bonus']
                                            ?? 0
                                        );

                                    $dexterityMode =
                                        $armor['dexterity_mode']
                                        ?? match (
                                            $armor['category']
                                            ?? null
                                        ) {
                                            'light' =>
                                                'full',

                                            'medium' =>
                                                'capped',

                                            default =>
                                                'none',
                                        };

                                    $dexterity =
                                        (int) (
                                            $abilityModifiers['dex']
                                            ?? 0
                                        );

                                    $dexterityContribution =
                                        match ($dexterityMode) {
                                            'full' =>
                                                $dexterity,

                                            'capped' =>
                                                min(
                                                    $dexterity,
                                                    max(
                                                        0,
                                                        (int) (
                                                            $armor['dexterity_cap']
                                                            ?? 2
                                                        )
                                                    )
                                                ),

                                            default =>
                                                0,
                                        };

                                    $extraAbilityBonus =
                                        collect(
                                            is_array(
                                                $armor['ability_modifiers']
                                                ?? null
                                            )
                                                ? $armor['ability_modifiers']
                                                : []
                                        )
                                            ->sum(
                                                function (
                                                    $ability
                                                ) use (
                                                    $armorAbilityMap,
                                                    $abilityModifiers
                                                ) {
                                                    $key =
                                                        $armorAbilityMap[$ability]
                                                        ?? $ability;

                                                    return (int) (
                                                        $abilityModifiers[$key]
                                                        ?? 0
                                                    );
                                                }
                                            );

                                    return (
                                        $baseAc
                                        +
                                        $dexterityContribution
                                        +
                                        $extraAbilityBonus
                                        +
                                        $magicBonus
                                    );
                                };


                            /*
                             * Se mais de uma armadura tiver sido marcada como equipada por
                             * algum dado antigo, o Show escolhe a que produzir a maior CA.
                             */
                            $equippedArmorBodyAc =
                                $character->items
                                    ->filter(
                                        fn ($item) =>
                                            (bool) $item->equipped
                                    )
                                    ->map(
                                        fn ($item) =>
                                            $armorBodyFromItem(
                                                $item
                                            )
                                    )
                                    ->filter(
                                        fn ($value) =>
                                            $value !== null
                                    )
                                    ->max();

                            $armorBodyAc =
                                $equippedArmorBodyAc !== null
                                    ? (int) $equippedArmorBodyAc
                                    : $baseDefenseBodyAc;


                            /*
                             * Escudos acumulam ac_bonus + magic_bonus, exatamente como
                             * no cálculo usado pelo Show.
                             */
                            $equippedShieldBonus =
                                $character->items
                                    ->filter(
                                        fn ($item) =>
                                            (bool) $item->equipped
                                    )
                                    ->sum(
                                        function (
                                            $item
                                        ) use (
                                            $itemProperties
                                        ): int {
                                            $properties =
                                                $itemProperties(
                                                    $item
                                                );

                                            $shield =
                                                data_get(
                                                    $properties,
                                                    'shield'
                                                );

                                            if (!is_array($shield)) {
                                                return 0;
                                            }

                                            return (
                                                (int) (
                                                    $shield['ac_bonus']
                                                    ?? 0
                                                )
                                                +
                                                (int) (
                                                    $shield['magic_bonus']
                                                    ?? 0
                                                )
                                            );
                                        }
                                    );

                            $armorClass =
                                $armorBodyAc
                                +
                                $equippedShieldBonus
                                +
                                $armorNamedBonusTotal;

                            $initiative =
                                (int) (
                                    $combat?->initiative_bonus
                                    ?? 0
                                );

                            $speed =
                                (int) (
                                    $combat?->speed
                                    ?? 0
                                );

                            $exhaustion =
                                max(
                                    0,
                                    (int) (
                                        $combat?->exhaustion_level
                                        ?? 0
                                    )
                                );
                        @endphp


                        <article
                            class="
                                character-roster-card
                                {{ $isDead ? 'is-dead' : '' }}
                            "
                        >

                            {{-- RETRATO --}}
                            <div class="character-roster-portrait">

                                @if ($character->image_path)
                                    <img
                                        src="{{ Storage::url($character->image_path) }}"
                                        alt="{{ $character->name }}"
                                        class="character-roster-image"
                                    >
                                @else
                                    <div
                                        class="
                                            absolute
                                            inset-0
                                            flex
                                            items-center
                                            justify-center
                                            text-[#8c6239]/30
                                        "
                                    >
                                        <svg
                                            class="h-20 w-20"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 21a7 7 0 0114 0"
                                            />
                                        </svg>
                                    </div>
                                @endif


                                <div class="character-roster-level">
                                    <small>Nível</small>
                                    <strong>{{ $character->level }}</strong>
                                </div>


                                @if (!$isDead)
                                    <div class="character-roster-photo-caption">
                                        <span>
                                            <i
                                                class="
                                                    character-roster-status-dot
                                                    {{ $statusKey }}
                                                "
                                            ></i>

                                            {{ $status }}
                                        </span>
                                    </div>
                                @endif


                                @if ($isDead)
                                    <div class="character-roster-memorial">
                                        <div class="character-roster-memorial-mark">
                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M12 3v18M7 8h10"
                                                />
                                            </svg>
                                        </div>

                                        <small>Em memória</small>

                                        <h3>{{ $character->name }}</h3>

                                        <div class="character-roster-memorial-rule"></div>

                                        <p>
                                            A jornada terminou
                                            @if ($displayCampaign)
                                                em
                                                <strong>{{ $displayCampaign->name }}</strong>.
                                            @else
                                                em combate.
                                            @endif

                                            A ficha permanece como registro da aventura.
                                        </p>
                                    </div>
                                @endif

                            </div>


                            {{-- DOSSIÊ --}}
                            <div class="character-roster-body">

                                <div>
                                    <span class="character-roster-kicker">
                                        {{ $character->species ?? 'Espécie não definida' }}
                                        ·
                                        Nível {{ $character->level }}
                                    </span>

                                    <h2 class="character-roster-name">
                                        {{ $character->name }}
                                    </h2>

                                    <p class="character-roster-summary">
                                        {{ $classSummary }}
                                    </p>
                                </div>


                                {{-- CAMPANHA --}}
                                <div class="character-roster-campaign">
                                    <div class="character-roster-campaign-icon">
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.6"
                                                d="M4 19V6.5A1.5 1.5 0 015.5 5H18l2 3-2 3H5.5"
                                            />
                                        </svg>
                                    </div>

                                    <div class="character-roster-campaign-copy">
                                        <small>
                                            {{ $activeCampaign
                                                ? 'Campanha atual'
                                                : (
                                                    $displayCampaign
                                                        ? 'Campanha vinculada'
                                                        : 'Campanha'
                                                )
                                            }}
                                        </small>

                                        <strong>
                                            {{ $displayCampaign?->name
                                                ?? 'Nenhuma campanha vinculada'
                                            }}
                                        </strong>
                                    </div>

                                    @if ($campaignCount > 1)
                                        <span class="character-roster-campaign-more">
                                            +{{ $campaignCount - 1 }}
                                        </span>
                                    @endif
                                </div>


                                {{-- IDENTIDADE --}}
                                <dl class="character-roster-identity">
                                    <div class="character-roster-detail">
                                        <dt>Antecedente</dt>
                                        <dd>
                                            {{ $character->background
                                                ?? 'Não definido'
                                            }}
                                        </dd>
                                    </div>

                                    <div class="character-roster-detail">
                                        <dt>Alinhamento</dt>
                                        <dd>
                                            {{ $character->alignment
                                                ?? 'Não definido'
                                            }}
                                        </dd>
                                    </div>

                                    <div class="character-roster-detail">
                                        <dt>Estado</dt>
                                        <dd>{{ $status }}</dd>
                                    </div>

                                    <div class="character-roster-detail">
                                        <dt>Exaustão</dt>
                                        <dd>
                                            {{ $exhaustion > 0
                                                ? $exhaustion . ' nível' . ($exhaustion > 1 ? 's' : '')
                                                : 'Nenhuma'
                                            }}
                                        </dd>
                                    </div>
                                </dl>


                                {{-- COMBATE --}}
                                <div class="character-roster-combat">

                                    <div class="character-roster-stat">
                                        <span>CA</span>
                                        <strong>{{ $armorClass }}</strong>
                                    </div>

                                    <div class="character-roster-stat">
                                        <span>PV</span>
                                        <strong>
                                            {{ $currentHp }}/{{ $effectiveMaxHp }}
                                        </strong>
                                    </div>

                                    <div class="character-roster-stat">
                                        <span>Iniciativa</span>
                                        <strong>
                                            {{ $initiative >= 0 ? '+' : '' }}{{ $initiative }}
                                        </strong>
                                    </div>

                                    <div class="character-roster-stat">
                                        <span>Desloc.</span>
                                        <strong>
                                            {{ $speed > 0 ? $speed . 'ft' : '—' }}
                                        </strong>
                                    </div>

                                </div>


                                {{-- VIDA --}}
                                <div class="character-roster-health">

                                    <div class="character-roster-health-head">
                                        <span>
                                            Pontos de vida
                                            @if ($temporaryHp > 0)
                                                · {{ $temporaryHp }} temporários
                                            @endif
                                        </span>

                                        <strong>
                                            {{ $hpPercent }}%
                                        </strong>
                                    </div>

                                    <div class="character-roster-health-track">
                                        <div
                                            class="
                                                character-roster-health-fill
                                                {{ $statusKey }}
                                            "
                                            style="width: {{ $hpPercent }}%"
                                        ></div>
                                    </div>

                                </div>


                                @if ($usesExperience)
                                    <div
                                        class="
                                            mt-2
                                            flex
                                            items-center
                                            justify-between
                                            border-t
                                            border-[#cdbb9f]/20
                                            pt-2
                                            text-[8px]
                                            font-bold
                                            text-[#8c6239]
                                        "
                                    >
                                        <span>Experiência</span>

                                        <strong class="text-[#53150f]">
                                            {{ number_format(
                                                (int) (
                                                    $combat?->experience_points
                                                    ?? 0
                                                ),
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                            XP
                                        </strong>
                                    </div>
                                @endif


                                {{-- AÇÕES --}}
                                <div class="character-roster-footer">

                                    <a
                                        href="{{ route('characters.show', $character) }}"
                                        class="character-roster-open"
                                    >
                                        {{ $isDead
                                            ? 'Abrir memorial da ficha'
                                            : 'Abrir ficha'
                                        }}
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('characters.destroy', $character) }}"
                                        onsubmit="return confirm('Tem certeza que deseja remover este personagem? Esta ação não pode ser desfeita.');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Excluir personagem"
                                            class="character-roster-delete"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 011 1h-4a1 1 0 01-1-1V3m-6 4h16"
                                                />
                                            </svg>
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </article>

                    @endforeach
                </div>

            @endif

        </div>

        {{-- Modal --}}

        <div

            x-show="createOpen"

            x-cloak

            x-transition.opacity

            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#2b1d17]/60 p-4 backdrop-blur-sm"

        >

            <div

                x-show="createOpen"

                x-transition

                @click.outside="closeCreate()"

                class="relative flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#f4f1e8] shadow-2xl"

            >

                <div class="flex shrink-0 items-center justify-between border-b border-[#cdbb9f]/70 bg-[#efe9dc]/60 px-5 py-4">

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">

                            Novo Personagem

                        </p>

                        <h2 class="mt-1 font-serif text-xl font-black text-[#53150f]">

                            Forjar Aventureiro

                        </h2>

                    </div>

                    <button

                        type="button"

                        @click="closeCreate()"

                        class="flex h-9 w-9 items-center justify-center rounded-lg text-[#8c6239] hover:bg-[#6b1d14]/10 hover:text-[#6b1d14]"

                    >

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18"/>

                        </svg>

                    </button>

                </div>

                <form

                    method="POST"

                    action="{{ route('characters.store') }}"

                    enctype="multipart/form-data"

                    class="min-h-0 overflow-y-auto"

                >

                    @csrf

                    <input type="hidden" name="multi_class_enabled" :value="classes.length > 1 ? 1 : 0">

                    <input type="hidden" name="level" :value="totalLevel">

                    <div class="space-y-5 p-5 sm:p-6">

                        {{-- Identidade --}}

                        <div>

                            <div class="mb-4">

                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">

                                    Identidade

                                </p>

                                <p class="mt-1 text-xs text-[#8c6239]/70">

                                    Informações básicas do aventureiro.

                                </p>

                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                                <div class="sm:col-span-2">

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Nome

                                    </label>

                                    <input

                                        type="text"

                                        name="name"

                                        value="{{ old('name') }}"

                                        required

                                        maxlength="120"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                                <div>

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Espécie

                                    </label>

                                    <input

                                        type="text"

                                        name="species"

                                        value="{{ old('species') }}"

                                        required

                                        maxlength="80"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                                <div>

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Antecedente

                                    </label>

                                    <input

                                        type="text"

                                        name="background"

                                        value="{{ old('background') }}"

                                        required

                                        maxlength="120"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                                <div>

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Alinhamento

                                    </label>

                                    <input

                                        type="text"

                                        name="alignment"

                                        value="{{ old('alignment') }}"

                                        required

                                        maxlength="50"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                            </div>

                        </div>

                        {{-- Classes --}}

                        <div class="border-t border-[#cdbb9f]/40 pt-5">

                            <div class="mb-4 flex items-end justify-between gap-3">

                                <div>

                                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">

                                        Classes

                                    </p>

                                    <p class="mt-1 text-xs text-[#8c6239]/70">

                                        Defina a composição inicial do personagem.

                                    </p>

                                </div>

                                <div class="rounded-lg border border-[#cdbb9f]/50 bg-[#efe9dc] px-3 py-1.5 text-center">

                                    <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Nível Total

                                    </span>

                                    <span class="block font-serif text-lg font-black text-[#53150f]" x-text="totalLevel"></span>

                                </div>

                            </div>

                            <div class="space-y-3">

                                <template x-for="(item, index) in classes" :key="index">

                                    <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40 p-3">

                                        <div class="mb-3 flex items-center justify-between gap-2">

                                            <span class="text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                Classe <span x-text="index + 1"></span>

                                            </span>

                                            <button

                                                x-show="classes.length > 1"

                                                type="button"

                                                @click="removeClass(index)"

                                                class="text-[9px] font-black uppercase tracking-widest text-red-700 hover:text-red-900"

                                            >

                                                Remover

                                            </button>

                                        </div>

                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">

                                            <div class="sm:col-span-5">

                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                    Classe

                                                </label>

                                                <input

                                                    type="text"

                                                    :name="`classes[${index}][class]`"

                                                    x-model="item.class"

                                                    required

                                                    maxlength="80"

                                                    placeholder="Ex.: Clérigo"

                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                                >

                                            </div>

                                            <div class="sm:col-span-5">

                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                    Subclasse

                                                </label>

                                                <input

                                                    type="text"

                                                    :name="`classes[${index}][subclass]`"

                                                    x-model="item.subclass"

                                                    maxlength="80"

                                                    placeholder="Opcional"

                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                                >

                                            </div>

                                            <div class="sm:col-span-2">

                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                    Nível

                                                </label>

                                                <input

                                                    type="number"

                                                    :name="`classes[${index}][level]`"

                                                    x-model.number="item.level"

                                                    min="1"

                                                    max="20"

                                                    required

                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                                >

                                            </div>

                                        </div>

                                    </div>

                                </template>

                            </div>

                            <button

                                type="button"

                                x-show="classes.length === 1"

                                @click="addClass()"

                                class="mt-3 inline-flex items-center gap-2 rounded-lg border border-[#cdbb9f] bg-[#f4f1e8] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#6b1d14] transition hover:border-[#6b1d14]/30 hover:bg-[#efe9dc]"

                            >

                                <span class="text-sm">+</span>

                                Adicionar multiclasse

                            </button>

                            <button

                                type="button"

                                x-show="classes.length > 1"

                                @click="addClass()"

                                class="mt-3 text-[9px] font-black uppercase tracking-widest text-[#6b1d14] hover:text-[#53150f]"

                                x-show="classes.length < 6"

                            >

                                + Adicionar outra classe

                            </button>

                        </div>

                        {{-- Avançado --}}

                        <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40">

                            <button

                                type="button"

                                @click="advancedOpen = !advancedOpen"

                                class="flex w-full items-center justify-between px-4 py-3 text-left"

                            >

                                <div>

                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-[#8c6239]">

                                        Configurações avançadas

                                    </span>

                                    <p class="mt-0.5 text-xs text-[#8c6239]/70">

                                        XP e regras personalizadas.

                                    </p>

                                </div>

                                <svg

                                    class="h-4 w-4 text-[#8c6239] transition-transform"

                                    :class="{ 'rotate-180': advancedOpen }"

                                    fill="none"

                                    viewBox="0 0 24 24"

                                    stroke="currentColor"

                                >

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 9l-7 7-7-7"/>

                                </svg>

                            </button>

                            <div x-show="advancedOpen" x-transition class="border-t border-[#cdbb9f]/40 px-4 pb-4">

                                <div class="space-y-3 pt-4">

                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] p-3">

                                        <label class="flex cursor-pointer items-center gap-3">

                                            <input

                                                type="checkbox"

                                                name="xp_enabled"

                                                value="1"

                                                x-model="xpEnabled"

                                                class="rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30"

                                            >

                                            <span>

                                                <span class="block text-[10px] font-black uppercase tracking-widest text-[#53150f]">

                                                    Usar experiência

                                                </span>

                                                <span class="text-[11px] text-[#8c6239]">

                                                    Ative somente se a campanha usar XP.

                                                </span>

                                            </span>

                                        </label>

                                        <div x-show="xpEnabled" x-transition class="mt-3">

                                            <input

                                                type="number"

                                                name="experience_points"

                                                min="0"

                                                value="{{ old('experience_points', 0) }}"

                                                :disabled="!xpEnabled"

                                                placeholder="XP inicial"

                                                class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                            >

                                        </div>

                                    </div>

                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] p-3">

                                        <label class="flex cursor-pointer items-center gap-3">

                                            <input

                                                type="checkbox"

                                                name="custom_prof_enabled"

                                                value="1"

                                                x-model="customProfEnabled"

                                                class="rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30"

                                            >

                                            <span>

                                                <span class="block text-[10px] font-black uppercase tracking-widest text-[#53150f]">

                                                    Proficiência personalizada

                                                </span>

                                                <span class="text-[11px] text-[#8c6239]">

                                                    Sobrescreva o valor padrão.

                                                </span>

                                            </span>

                                        </label>

                                        <div x-show="customProfEnabled" x-transition class="mt-3">

                                            <input

                                                type="number"

                                                name="proficiency_bonus"

                                                value="{{ old('proficiency_bonus') }}"

                                                :disabled="!customProfEnabled"

                                                placeholder="Bônus de proficiência"

                                                class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                            >

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- Foto --}}

                        <div class="border-t border-[#cdbb9f]/40 pt-5">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                                <div class="relative flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#efe9dc]">

                                    <svg class="h-7 w-7 text-[#8c6239]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>

                                    </svg>

                                    <img

                                        x-show="imagePreview"

                                        x-cloak

                                        :src="imagePreview"

                                        alt="Pré-visualização"

                                        class="absolute inset-0 h-full w-full object-cover object-top"

                                    >

                                </div>

                                <div class="flex-1">

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Foto de Perfil

                                    </label>

                                    <input

                                        type="file"

                                        name="image"

                                        accept="image/*"

                                        @change="updatePreview"

                                        class="block w-full text-sm text-[#8c6239] file:mr-4 file:cursor-pointer file:rounded-xl file:border-0 file:bg-[#efe9dc] file:px-4 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-[#6b1d14] hover:file:bg-[#6b1d14] hover:file:text-[#f4f1e8]"

                                    >

                                </div>

                            </div>

                        </div>

                        @if ($errors->any())

                            <div class="rounded-xl border border-red-800/20 bg-red-50/50 p-4 text-sm text-red-900">

                                <p class="mb-2 text-[9px] font-black uppercase tracking-widest text-red-800">

                                    Corrija os campos abaixo

                                </p>

                                <ul class="space-y-1">

                                    @foreach ($errors->all() as $error)

                                        <li class="flex gap-2">

                                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-red-600"></span>

                                            <span>{{ $error }}</span>

                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif

                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-[#cdbb9f]/50 bg-[#efe9dc]/40 px-5 py-4 sm:flex-row sm:justify-end">

                        <button

                            type="button"

                            @click="closeCreate()"

                            class="rounded-xl px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#8c6239] hover:bg-[#8c6239]/10"

                        >

                            Cancelar

                        </button>

                        <button

                            type="submit"

                            class="rounded-xl bg-[#6b1d14] px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#f4f1e8] transition hover:bg-[#53150f]"

                        >

                            Confirmar Criação

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>