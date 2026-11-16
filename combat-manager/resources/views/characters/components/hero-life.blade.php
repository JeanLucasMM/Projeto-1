@props(['character', 'maxHp'])


@once
    @push('styles')
        <style>
            /*
            |--------------------------------------------------------------------------
            | HERO LIFE V2 — CLEAN / SEMÂNTICO / DAMAGE CORE
            |--------------------------------------------------------------------------
            |
            | O Hero Header controla mecânica, modal de PV e intensidade do
            | impacto. Esta Blade controla barra, ações rápidas e feedback.
            |
            */

            .character-sheet-theme[data-theme]
            .hero-life-title {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-life-title:hover {
                color:
                    var(--theme-text-strong) !important;
            }


            /* LIMITES */

            .hero-life-limit-badge--extra {
                border-color:
                    rgba(180,132,28,.30) !important;

                background:
                    rgba(244,234,208,.90) !important;

                color:
                    #8a6416 !important;
            }

            .hero-life-limit-badge--reduced {
                border-color:
                    rgba(126,34,206,.24) !important;

                background:
                    rgba(237,233,254,.90) !important;

                color:
                    #6b21a8 !important;
            }

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .hero-life-limit-badge--extra {
                background:
                    rgba(180,132,28,.16) !important;
            }

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .hero-life-limit-badge--reduced {
                background:
                    rgba(126,34,206,.18) !important;

                color:
                    #d8b4fe !important;
            }


            /* BARRA */

            .hero-life-bar {
                isolation:
                    isolate;
            }

            .hp-damage-sweep {
                opacity:
                    .68;
            }

            .hp-shield-impact {
                opacity:
                    .82;
            }


            /* PAINEL RÁPIDO */

            .character-sheet-theme[data-theme]
            .hero-hp-quick-panel {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 16px 42px rgba(0,0,0,.18),
                    var(
                        --theme-modal-control-shadow,
                        0 2px 7px rgba(0,0,0,.06)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-pointer {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-card {
                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-card--damage {
                border-color:
                    rgba(153,27,27,.22) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-card--heal {
                border-color:
                    rgba(4,120,87,.20) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-input {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-card--damage
            .hero-hp-quick-input:focus {
                border-color:
                    rgba(153,27,27,.56) !important;

                box-shadow:
                    0 0 0 3px
                    rgba(153,27,27,.08) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-card--heal
            .hero-hp-quick-input:focus {
                border-color:
                    rgba(4,120,87,.52) !important;

                box-shadow:
                    0 0 0 3px
                    rgba(4,120,87,.08) !important;
            }

            .hero-hp-quick-action--damage {
                border-color:
                    rgba(127,38,29,.24) !important;

                background:
                    #7f261d !important;

                color:
                    #fffaf2 !important;
            }

            .hero-hp-quick-action--damage:hover:not(:disabled) {
                background:
                    #651e18 !important;
            }

            .hero-hp-quick-action--heal {
                border-color:
                    rgba(6,95,70,.18) !important;

                background:
                    #047857 !important;

                color:
                    #fff !important;
            }

            .hero-hp-quick-action--heal:hover:not(:disabled) {
                background:
                    #065f46 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-footer {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-footer
            button {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-quick-footer
            button:hover {
                color:
                    var(--theme-text-strong) !important;
            }


            /* PV TEMPORÁRIOS — mesmos tokens do painel de dano/cura. */
            .character-sheet-theme[data-theme] .hero-temp-hp-apply {
                border-color: var(--theme-border-strong, var(--theme-border)) !important;
                background: var(--theme-button-background, var(--theme-accent)) !important;
                color: var(--theme-button-text, #fff) !important;
            }

            .character-sheet-theme[data-theme] .hero-temp-hp-apply:hover:not(:disabled) {
                background: var(--theme-button-hover-background, var(--theme-accent-strong)) !important;
            }

            .character-sheet-theme[data-theme] .hero-hp-quick-panel label {
                color: var(--theme-text-strong) !important;
            }

            /* FEEDBACK */

            .hero-hp-feedback {
                -webkit-backdrop-filter:
                    blur(6px);

                backdrop-filter:
                    blur(6px);
            }

            .hero-hp-feedback--damage {
                border-color:
                    rgba(239,68,68,.38) !important;

                background:
                    rgba(42,13,10,.94) !important;
            }

            .hero-hp-feedback--shield {
                border-color:
                    rgba(125,211,252,.42) !important;

                background:
                    rgba(8,47,73,.94) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-hp-feedback--heal {
                border-color:
                    rgba(4,120,87,.24) !important;

                background:
                    var(--theme-surface-raised) !important;
            }


            /* RESPONSIVO / ACESSIBILIDADE */

            @media (max-width: 520px) {
                .hero-hp-quick-panel {
                    width:
                        min(
                            300px,
                            calc(100vw - 24px)
                        ) !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .hp-damage-sweep,
                .hp-shield-impact,
                .hero-hp-feedback {
                    animation:
                        none !important;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | HERO LIFE V4 — BADGES FORA DA ÁREA DOS DADOS DE VIDA
            |--------------------------------------------------------------------------
            |
            | O Hero Header posiciona "Dados de Vida" sobre a extremidade
            | direita do cabeçalho de vida. Quando +X / -X usavam ml-auto,
            | eles ficavam fisicamente atrás desse componente e uma pequena
            | faixa dourada/roxa aparecia nas bordas arredondadas.
            |
            | Os badges agora ficam ao lado de "Pontos de Vida".
            | A faixa dourada de Vida Máxima Extra DENTRO da barra é mantida.
            |
            */

            .hero-life-limit-group {
                margin-left:
                    2px;
            }

            /*
             * O Hero Header V19+ também criou um badge de redução via ::after.
             * Como esta Blade já possui o badge -X real, removemos apenas
             * esse pseudo-elemento duplicado.
             */
            .hero-life-modal-source[data-hp-reduction-active="true"]
            .hero-life-heading::after {
                content:
                    none !important;

                display:
                    none !important;
            }


            /*
            |--------------------------------------------------------------------------
            | HERO LIFE V5 — ESTADOS / IMPACTOS EM CAMADAS
            |--------------------------------------------------------------------------
            */


            /* ==============================================================
               SANGRANDO / CRÍTICO
            ============================================================== */

            @keyframes hero-life-blood-vignette-v5 {
                0%, 68%, 100% {
                    opacity:
                        .08;
                }

                76% {
                    opacity:
                        .30;
                }

                84% {
                    opacity:
                        .13;
                }

                91% {
                    opacity:
                        .23;
                }
            }

            @keyframes hero-life-critical-vignette-v5 {
                0%, 100% {
                    opacity:
                        .18;
                }

                12% {
                    opacity:
                        .48;
                }

                22% {
                    opacity:
                        .22;
                }

                32% {
                    opacity:
                        .40;
                }

                52% {
                    opacity:
                        .18;
                }
            }

            .hero-life-state-vignette {
                opacity:
                    0;

                background:
                    radial-gradient(
                        ellipse at center,
                        transparent 34%,
                        rgba(127,29,29,.08) 62%,
                        rgba(127,29,29,.30) 100%
                    );

                mix-blend-mode:
                    screen;
            }

            .hero-header-v22[data-health-state="bloodied"]
            .hero-life-state-vignette {
                animation:
                    hero-life-blood-vignette-v5
                    3.8s
                    ease-in-out
                    infinite;
            }

            .hero-header-v22[data-health-state="critical"]
            .hero-life-state-vignette {
                background:
                    radial-gradient(
                        ellipse at center,
                        transparent 26%,
                        rgba(153,27,27,.12) 56%,
                        rgba(185,28,28,.42) 100%
                    );

                animation:
                    hero-life-critical-vignette-v5
                    2.15s
                    ease-in-out
                    infinite;
            }


            /* ==============================================================
               FLASH VERMELHO AO RECEBER DANO
            ============================================================== */

            @keyframes hero-life-hit-flash-soft-v5 {
                0%, 100% {
                    opacity:
                        0;
                }

                20% {
                    opacity:
                        .22;
                }

                55% {
                    opacity:
                        .07;
                }
            }

            @keyframes hero-life-hit-flash-medium-v5 {
                0%, 100% {
                    opacity:
                        0;
                }

                18% {
                    opacity:
                        .36;
                }

                48% {
                    opacity:
                        .12;
                }
            }

            @keyframes hero-life-hit-flash-heavy-v5 {
                0%, 100% {
                    opacity:
                        0;
                }

                14% {
                    opacity:
                        .52;
                }

                38% {
                    opacity:
                        .20;
                }

                64% {
                    opacity:
                        .07;
                }
            }

            .hero-life-hit-flash {
                background:
                    linear-gradient(
                        90deg,
                        rgba(127,29,29,.10) 0%,
                        rgba(239,68,68,.42) 50%,
                        rgba(127,29,29,.10) 100%
                    );

                mix-blend-mode:
                    screen;
            }

            .hero-header-v22[data-impact-strength="soft"]
            .hero-life-hit-flash {
                animation:
                    hero-life-hit-flash-soft-v5
                    .42s
                    ease-out
                    both;
            }

            .hero-header-v22[data-impact-strength="medium"]
            .hero-life-hit-flash {
                animation:
                    hero-life-hit-flash-medium-v5
                    .54s
                    ease-out
                    both;
            }

            .hero-header-v22[data-impact-strength="heavy"]
            .hero-life-hit-flash {
                animation:
                    hero-life-hit-flash-heavy-v5
                    .72s
                    ease-out
                    both;
            }


            /* ==============================================================
               DANO NA VIDA MÁXIMA EXTRA — DOURADO
            ============================================================== */

            @keyframes hero-life-bonus-impact-v5 {
                0% {
                    opacity:
                        0;

                    filter:
                        brightness(1);

                    transform:
                        scaleX(.88);
                }

                20% {
                    opacity:
                        1;

                    filter:
                        brightness(1.55)
                        saturate(1.28);

                    transform:
                        scaleX(1.04);
                }

                52% {
                    opacity:
                        .72;

                    filter:
                        brightness(1.15);

                    transform:
                        scaleX(1);
                }

                100% {
                    opacity:
                        0;

                    filter:
                        brightness(1);

                    transform:
                        scaleX(.98);
                }
            }

            @keyframes hero-life-bonus-shine-v5 {
                0% {
                    transform:
                        translateX(-130%)
                        skewX(-18deg);
                }

                100% {
                    transform:
                        translateX(180%)
                        skewX(-18deg);
                }
            }

            .hero-life-bonus-impact {
                border-left:
                    1px solid rgba(255,238,178,.86);

                border-right:
                    1px solid rgba(146,96,12,.45);

                background:
                    linear-gradient(
                        90deg,
                        rgba(122,76,8,.70) 0%,
                        rgba(217,164,65,.95) 50%,
                        rgba(255,226,132,.90) 100%
                    );

                box-shadow:
                    inset 0 0 16px rgba(255,244,196,.38),
                    0 0 12px rgba(217,164,65,.24);

                transform-origin:
                    right center;

                animation:
                    hero-life-bonus-impact-v5
                    .62s
                    cubic-bezier(.18,.82,.22,1)
                    both;
            }

            .hero-life-bonus-impact__shine {
                position:
                    absolute;

                inset-block:
                    0;

                left:
                    0;

                width:
                    44%;

                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        rgba(255,255,255,.65),
                        transparent
                    );

                animation:
                    hero-life-bonus-shine-v5
                    .50s
                    ease-out
                    both;
            }

            .hero-life-bonus-impact__crack {
                position:
                    absolute;

                inset:
                    0;

                opacity:
                    .62;

                background:
                    linear-gradient(
                        118deg,
                        transparent 0 46%,
                        rgba(103,63,8,.55) 47% 49%,
                        transparent 50% 100%
                    ),
                    linear-gradient(
                        72deg,
                        transparent 0 61%,
                        rgba(255,248,215,.70) 62% 64%,
                        transparent 65% 100%
                    );
            }


            /* ==============================================================
               PV TEMPORÁRIOS — ESCUDO / RACHADURA
            ============================================================== */

            @keyframes hero-life-shield-ring-v5 {
                0% {
                    opacity:
                        0;

                    transform:
                        scale(.72);
                }

                28% {
                    opacity:
                        .82;
                }

                100% {
                    opacity:
                        0;

                    transform:
                        scale(1.16);
                }
            }

            @keyframes hero-life-shield-crack-v5 {
                0%, 100% {
                    opacity:
                        0;
                }

                22% {
                    opacity:
                        .80;
                }

                64% {
                    opacity:
                        .26;
                }
            }

            .hp-shield-impact {
                overflow:
                    hidden;
            }

            .hero-life-shield-impact__ring {
                position:
                    absolute;

                inset:
                    2px 18%;

                border:
                    1px solid rgba(186,230,253,.78);

                border-radius:
                    999px;

                box-shadow:
                    0 0 14px rgba(56,189,248,.24),
                    inset 0 0 12px rgba(186,230,253,.18);

                animation:
                    hero-life-shield-ring-v5
                    .58s
                    ease-out
                    both;
            }

            .hero-life-shield-impact__crack {
                position:
                    absolute;

                inset:
                    0;

                background:
                    linear-gradient(
                        114deg,
                        transparent 0 47%,
                        rgba(224,242,254,.84) 48% 49%,
                        transparent 50% 100%
                    ),
                    linear-gradient(
                        66deg,
                        transparent 0 58%,
                        rgba(125,211,252,.62) 59% 60%,
                        transparent 61% 100%
                    );

                animation:
                    hero-life-shield-crack-v5
                    .54s
                    ease-out
                    both;
            }


            /* ==============================================================
               SWEEP DE DANO — VARIANTES
            ============================================================== */

            .hero-header-v22[data-impact-strength="soft"]
            .hp-damage-sweep {
                opacity:
                    .48;

                animation-duration:
                    .40s !important;
            }

            .hero-header-v22[data-impact-strength="medium"]
            .hp-damage-sweep {
                opacity:
                    .72;

                animation-duration:
                    .52s !important;
            }

            .hero-header-v22[data-impact-strength="heavy"]
            .hp-damage-sweep {
                opacity:
                    .94;

                width:
                    48% !important;

                left:
                    28% !important;

                animation-duration:
                    .66s !important;
            }


            /* ==============================================================
               MOVIMENTO REDUZIDO
            ============================================================== */

            @media (prefers-reduced-motion: reduce) {
                .hero-life-state-vignette,
                .hero-life-hit-flash,
                .hero-life-bonus-impact,
                .hero-life-bonus-impact__shine,
                .hero-life-shield-impact__ring,
                .hero-life-shield-impact__crack {
                    animation:
                        none !important;
                }
            }

        </style>
    @endpush
@endonce

<div class="hero-life relative flex min-w-0 flex-1 flex-col gap-1.5">

    {{-- CABEÇALHO --}}

    <div
        class="
            hero-life-heading
            flex
            min-w-0
            items-center
            gap-1.5
            px-0.5
        "
    >

        {{-- TÍTULO + EDIÇÃO DE VIDA --}}

        <button

            type="button"

            @click.stop="openHpSettings()"

            class="
                hero-life-title
                group
                inline-flex
                min-w-0
                items-center
                gap-1.5
                text-[9px]
                font-black
                uppercase
                tracking-[0.11em]
                transition-colors
                focus:outline-none
            "

            title="Configurar Pontos de Vida"

        >

            <span>Pontos de Vida</span>





        </button>

        <div
            class="
                hero-life-limit-group
                flex
                shrink-0
                items-center
                gap-1
            "
        >
            <template x-if="temporaryMaxHp > 0">
                <span
                    class="
                        hero-life-limit-badge
                        hero-life-limit-badge--extra
                        inline-flex
                        min-h-[18px]
                        items-center
                        rounded
                        border
                        px-1.5
                        py-0.5
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.05em]
                    "
                    title="Vida Máxima Extra"
                >
                    +<span x-text="temporaryMaxHp"></span>
                </span>
            </template>

            <template x-if="maxHpReduction > 0">
                <span
                    class="
                        hero-life-limit-badge
                        hero-life-limit-badge--reduced
                        inline-flex
                        min-h-[18px]
                        items-center
                        rounded
                        border
                        px-1.5
                        py-0.5
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.05em]
                    "
                    title="Vida Máxima Reduzida"
                >
                    −<span x-text="maxHpReduction"></span>
                </span>
            </template>
        </div>
    </div>

    {{-- BARRA DE VIDA --}}

    <div

        :class="{
            'ring-1 ring-sky-400/40':
                temporaryHp > 0
        }"

        class="hero-life-bar relative flex h-8 w-full items-center justify-between overflow-hidden rounded-md border border-black/40 bg-[#1f1b1b] shadow-inner"

    >

        {{-- GHOST --}}

        <div

            class="absolute inset-y-0 left-0 bg-red-950/80 transition-all duration-1000 ease-out"

            :style="`width:${ghostHpPercent}%`"

        ></div>

        {{-- VIDA NORMAL --}}

        <div

            class="absolute inset-y-0 left-0 z-[5] transition-all duration-700 ease-out"

            :class="

                hpPercent > 50

                    ? 'bg-emerald-700'

                    : (

                        hpPercent > 25

                            ? 'bg-amber-600'

                            : 'bg-red-700'

                    )

            "

            :style="`width:${normalHpPercent}%`"

        ></div>

        {{-- VIDA EXTRA --}}

        <template x-if="bonusHpPercent > 0">

            <div

                class="absolute inset-y-0 z-[8] overflow-hidden border-l border-[#f4d58a]/70 bg-gradient-to-r from-[#9a6f16] via-[#d9a441] to-[#efd27a] transition-all duration-700 ease-out"

                :style="`left:${normalHpPercent}%;width:${bonusHpPercent}%`"

            >

                <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/15 via-transparent to-black/10"></div>

            </div>

        </template>

        {{-- VIDA TEMPORÁRIA --}}

        <template x-if="temporaryHp > 0">

            <div

                class="pointer-events-none absolute inset-y-0 left-0 z-[15] overflow-hidden transition-all duration-500 ease-out"

                :style="`width:${tempHpPercent}%`"

            >

                <div class="absolute inset-0 border-y border-sky-200/50 bg-gradient-to-b from-sky-300/25 via-sky-400/20 to-sky-600/30"></div>

                <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/15 to-transparent"></div>

                <div class="absolute inset-0 opacity-20 bg-[repeating-linear-gradient(-45deg,transparent,transparent_4px,rgba(186,230,253,.45)_4px,rgba(186,230,253,.45)_5px)]"></div>

            </div>

        </template>

        {{-- ============================================================
             ESTADO PERSISTENTE — SANGRANDO / CRÍTICO
        ============================================================= --}}

        <div
            class="
                hero-life-state-vignette
                pointer-events-none
                absolute
                inset-0
                z-[20]
            "
            aria-hidden="true"
        ></div>


        {{-- ============================================================
             IMPACTO VISUAL — DANO REAL

        ============================================================= --}}

        <div

            x-show="
                hpImpactDamageAmount > 0
            "

            x-cloak

            class="

                hp-damage-sweep

                pointer-events-none

                absolute

                inset-y-0

                left-[38%]

                z-[24]

                w-[36%]

                bg-gradient-to-r

                from-transparent

                via-red-200/70

                to-transparent

                mix-blend-screen

            "

        ></div>

        <div
            x-show="
                hpImpactDamageAmount > 0
            "
            x-cloak
            class="
                hero-life-hit-flash
                pointer-events-none
                absolute
                inset-0
                z-[25]
            "
            aria-hidden="true"
        ></div>


        {{-- ============================================================
             IMPACTO VISUAL — VIDA MÁXIMA EXTRA
        ============================================================= --}}

        <div
            x-show="
                hpImpactBonusAmount > 0
            "
            x-cloak
            class="
                hero-life-bonus-impact
                pointer-events-none
                absolute
                inset-y-0
                z-[26]
                overflow-hidden
            "
            :style="`
                left:${
                    Math.min(
                        100,
                        (
                            maxHp /
                            potentialMaxHp
                        ) * 100
                    )
                    +
                    bonusHpPercent
                }%;
                width:${
                    Math.min(
                        100,
                        (
                            hpImpactBonusAmount /
                            potentialMaxHp
                        ) * 100
                    )
                }%;
            `"
            aria-hidden="true"
        >
            <span class="hero-life-bonus-impact__shine"></span>
            <span class="hero-life-bonus-impact__crack"></span>
        </div>


        {{-- ============================================================

             IMPACTO VISUAL — PV TEMPORÁRIOS

        ============================================================= --}}

        <div

            x-show="
                hpImpactShieldAmount > 0
            "

            x-cloak

            class="

                hp-shield-impact

                pointer-events-none

                absolute

                inset-0

                z-[23]

                border

                border-sky-200/55

                bg-[radial-gradient(circle_at_50%_50%,rgba(186,230,253,.34),transparent_68%)]

            "

        >
            <span class="hero-life-shield-impact__ring"></span>
            <span class="hero-life-shield-impact__crack"></span>
        </div>



        {{-- DANO --}}

        <div class="relative z-30 flex h-full items-center px-1">

            <button

                type="button"

                title="-5"

                @click="changeHp(-5)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                «

            </button>

            <button

                type="button"

                title="-1"

                @click="changeHp(-1)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                ‹

            </button>

        </div>

        {{-- VALORES --}}

        <div class="relative z-30 flex min-w-0 flex-1 items-center justify-center gap-2">

            <button

                type="button"

                @click.stop="

                    toggleHpQuick()

                "

                :class="

                    hpQuickOpen

                        ? 'bg-white/12 ring-1 ring-white/15'

                        : 'hover:bg-white/8'

                "

                class="

                    inline-flex

                    h-6

                    items-center

                    rounded-md

                    px-2

                    text-xs

                    font-bold

                    tracking-wider

                    text-white

                    drop-shadow-[0_1px_2px_rgba(0,0,0,.8)]

                    transition

                    focus:outline-none

                    focus:ring-1

                    focus:ring-[#d7b58b]/50

                "

                title="Aplicar dano ou cura"

            >

                <span x-text="currentHp"></span>

                <span class="mx-0.5 text-white/50">/</span>

                <span x-text="effectiveMaxHp"></span>

            </button>



            {{-- VIDA TEMPORÁRIA: abre um único painel, sem input dentro da barra. --}}
            <button
                type="button"
                @click.stop="
                    if (editingTempHp) {
                        editingTempHp = false;
                        directTempHp = temporaryHp;
                    } else {
                        directTempHp = temporaryHp;
                        if (hpQuickOpen) closeHpQuick();
                        editingTempHp = true;
                        $nextTick(() => {
                            $refs.tempHp.focus();
                            $refs.tempHp.select();
                        });
                    }
                "
                :aria-expanded="editingTempHp ? 'true' : 'false'"
                :class="temporaryHp > 0
                    ? 'border-sky-300/70 bg-sky-100/90 text-sky-800 hover:bg-sky-100'
                    : 'border-white/20 bg-black/25 text-white/75 hover:bg-black/40 hover:text-white'"
                class="inline-flex h-6 min-w-[48px] items-center justify-center gap-1 rounded-md border px-2 text-[10px] font-black tabular-nums transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300/70"
                title="Definir pontos de vida temporários"
                aria-label="Abrir editor de pontos de vida temporários"
                aria-controls="hero-temp-hp-panel-{{ $character->id }}"
            >
                <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z" />
                </svg>
                <span x-text="temporaryHp"></span>
            </button>

        </div>

        {{-- CURA --}}

        <div class="relative z-30 flex h-full items-center px-1">

            <button

                type="button"

                title="+1"

                @click="changeHp(1)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                ›

            </button>

            <button

                type="button"

                title="+5"

                @click="changeHp(5)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                »

            </button>

        </div>

    </div>



    {{-- PV TEMPORÁRIOS — painel independente; salvar somente ao confirmar. --}}
    <div
        id="hero-temp-hp-panel-{{ $character->id }}"
        x-show="editingTempHp"
        x-cloak
        x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition duration-100 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"
        @keydown.escape.stop="editingTempHp = false; directTempHp = temporaryHp"
        @click.stop
        @click.outside="if (editingTempHp) { editingTempHp = false; directTempHp = temporaryHp; }"
        class="hero-hp-quick-panel absolute left-1/2 top-full z-[116] mt-2 w-[min(285px,calc(100vw-2rem))] -translate-x-1/2 rounded-xl border p-3"
        role="group"
        aria-label="Definir pontos de vida temporários"
    >
        <span class="hero-hp-quick-pointer absolute -top-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 border-l border-t" aria-hidden="true"></span>

        <div class="relative flex items-center gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-sky-500/25 bg-sky-400/10 text-sky-500" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z" />
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <label for="hero-temp-hp-value-{{ $character->id }}" class="block text-[10px] font-black uppercase tracking-[.08em]">
                    PV temporários
                </label>
                <p class="hero-hp-quick-footer mt-0.5 text-[9px]">Defina o valor total.</p>
            </div>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <input
                id="hero-temp-hp-value-{{ $character->id }}"
                x-ref="tempHp"
                type="number"
                min="0"
                step="1"
                inputmode="numeric"
                x-model.number="directTempHp"
                @keydown.enter.prevent="if (Number.isInteger(Number(directTempHp)) && Number(directTempHp) >= 0 && directTempHp !== '') handleTemporaryHp()"
                class="hero-hp-quick-input h-9 min-w-0 flex-1 rounded-lg border px-3 text-center font-serif text-sm font-black focus:outline-none"
                aria-label="Valor de PV temporários"
            >
            <button
                type="button"
                @click="handleTemporaryHp()"
                :disabled="directTempHp === '' || !Number.isInteger(Number(directTempHp)) || Number(directTempHp) < 0"
                class="hero-temp-hp-apply inline-flex h-9 shrink-0 items-center justify-center rounded-lg border px-3 text-[10px] font-black transition disabled:cursor-not-allowed disabled:opacity-40"
            >
                Aplicar
            </button>
        </div>
        <div class="hero-hp-quick-footer mt-2 flex items-center justify-between gap-2 text-[9px]">
            <span>Use 0 para remover.</span>
            <button type="button" @click="editingTempHp = false; directTempHp = temporaryHp" class="text-[9px] font-bold">Fechar</button>
        </div>
    </div>

    {{-- ================================================================

         DANO / CURA RÁPIDOS

    ================================================================= --}}

    <div

        x-show="hpQuickOpen"

        x-cloak

        x-transition:enter="transition duration-150 ease-out"

        x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]"

        x-transition:enter-end="opacity-100 translate-y-0 scale-100"

        x-transition:leave="transition duration-100 ease-in"

        x-transition:leave-start="opacity-100 translate-y-0 scale-100"

        x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"

        @keydown.escape.window="

            closeHpQuick()

        "

        @click.stop

        @click.outside="
            closeHpQuick()
        "

        class="
            hero-hp-quick-panel

            absolute

            left-1/2

            top-full

            z-[115]

            mt-2

            w-[min(320px,calc(100vw-2rem))]

            -translate-x-1/2

            rounded-xl

            border

            border-[#cdbb9f]/85

            bg-[#fffaf2]

            p-2

            text-[#34231b]

            shadow-[0_14px_35px_rgba(52,35,27,.22)]

        "

    >

        {{-- Ponteiro --}}

        <span

            class="
                hero-hp-quick-pointer

                absolute

                -top-1.5

                left-1/2

                h-3

                w-3

                -translate-x-1/2

                rotate-45

                border-l

                border-t

                border-[#cdbb9f]/85

                bg-[#fffaf2]

            "

            aria-hidden="true"

        ></span>

        <div

            class="

                relative

                grid

                grid-cols-2

                gap-2

            "

        >

            {{-- DANO --}}

            <div

                class="
                    hero-hp-quick-card hero-hp-quick-card--damage

                    rounded-lg

                    border

                    border-[#a0523d]/22

                    bg-[#f8f0e6]

                    p-2

                "

            >

                <label

                    for="hero-quick-damage-{{ $character->id }}"

                    class="

                        block

                        text-[7px]

                        font-black

                        uppercase

                        tracking-[0.13em]

                        text-[#8b2a20]

                    "

                >

                    Dano

                </label>

                <div

                    class="

                        mt-1

                        flex

                        items-center

                        gap-1.5

                    "

                >

                    <input

                        id="hero-quick-damage-{{ $character->id }}"

                        x-ref="hpQuickDamageInput"

                        type="number"

                        min="1"

                        step="1"

                        inputmode="numeric"

                        x-model.number="hpQuickDamage"

                        @keydown.enter.prevent="

                            applyHpQuick('damage')

                        "

                        :disabled="hpQuickBusy"

                        placeholder="0"

                        class="
                            hero-hp-quick-input

                            h-8

                            min-w-0

                            flex-1

                            rounded-md

                            border

                            border-[#cdbb9f]

                            bg-[#fffdf8]

                            px-2

                            text-center

                            font-serif

                            text-sm

                            font-black

                            text-[#53150f]

                            placeholder:text-[#b89e88]

                            focus:border-[#8b2a20]/55

                            focus:outline-none

                            focus:ring-2

                            focus:ring-[#8b2a20]/10

                            disabled:opacity-50

                        "

                    >

                    <button

                        type="button"

                        @click="

                            applyHpQuick('damage')

                        "

                        :disabled="

                            hpQuickBusy

                            ||

                            !hpQuickDamage

                            ||

                            Number(hpQuickDamage) < 1

                        "

                        class="
                            hero-hp-quick-action hero-hp-quick-action--damage

                            flex

                            h-8

                            w-8

                            shrink-0

                            items-center

                            justify-center

                            rounded-md

                            border

                            border-[#7f261d]/25

                            bg-[#7f261d]

                            font-serif

                            text-sm

                            font-black

                            text-[#fffaf2]

                            transition

                            hover:bg-[#6b1d14]

                            disabled:cursor-not-allowed

                            disabled:opacity-35

                        "

                        title="Aplicar dano"

                    >

                        −

                    </button>

                </div>

            </div>



            {{-- CURA --}}

            <div

                class="
                    hero-hp-quick-card hero-hp-quick-card--heal

                    rounded-lg

                    border

                    border-emerald-800/15

                    bg-[#f8f0e6]

                    p-2

                "

            >

                <label

                    for="hero-quick-heal-{{ $character->id }}"

                    class="

                        block

                        text-[7px]

                        font-black

                        uppercase

                        tracking-[0.13em]

                        text-emerald-800

                    "

                >

                    Cura

                </label>

                <div

                    class="

                        mt-1

                        flex

                        items-center

                        gap-1.5

                    "

                >

                    <input

                        id="hero-quick-heal-{{ $character->id }}"

                        type="number"

                        min="1"

                        step="1"

                        inputmode="numeric"

                        x-model.number="hpQuickHeal"

                        @keydown.enter.prevent="

                            applyHpQuick('heal')

                        "

                        :disabled="hpQuickBusy"

                        placeholder="0"

                        class="
                            hero-hp-quick-input

                            h-8

                            min-w-0

                            flex-1

                            rounded-md

                            border

                            border-[#cdbb9f]

                            bg-[#fffdf8]

                            px-2

                            text-center

                            font-serif

                            text-sm

                            font-black

                            text-emerald-900

                            placeholder:text-[#b89e88]

                            focus:border-emerald-800/40

                            focus:outline-none

                            focus:ring-2

                            focus:ring-emerald-800/10

                            disabled:opacity-50

                        "

                    >

                    <button

                        type="button"

                        @click="

                            applyHpQuick('heal')

                        "

                        :disabled="

                            hpQuickBusy

                            ||

                            !hpQuickHeal

                            ||

                            Number(hpQuickHeal) < 1

                        "

                        class="
                            hero-hp-quick-action hero-hp-quick-action--heal

                            flex

                            h-8

                            w-8

                            shrink-0

                            items-center

                            justify-center

                            rounded-md

                            border

                            border-emerald-900/15

                            bg-emerald-800

                            font-serif

                            text-sm

                            font-black

                            text-white

                            transition

                            hover:bg-emerald-900

                            disabled:cursor-not-allowed

                            disabled:opacity-35

                        "

                        title="Aplicar cura"

                    >

                        +

                    </button>

                </div>

            </div>

        </div>

        <div

            class="
            hero-hp-quick-footer

                mt-1.5

                flex

                items-center

                justify-between

                gap-2

                px-0.5

            "

        >

            <span

                class="

                    text-[7px]

                    font-semibold

                    text-[#8a6d5a]

                "

            >

                Enter aplica o valor.

            </span>

            <button

                type="button"

                @click="

                    closeHpQuick()

                "

                :disabled="hpQuickBusy"

                class="

                    text-[7px]

                    font-black

                    uppercase

                    tracking-[0.08em]

                    text-[#93623d]

                    transition

                    hover:text-[#53150f]

                    disabled:opacity-40

                "

            >

                Fechar

            </button>

        </div>

    </div>



    {{-- ================================================================

         FEEDBACK FLUTUANTE DE PV

    ================================================================= --}}

    <div

        x-show="hpFeedbackVisible"

        x-cloak

        class="

            pointer-events-none

            absolute

            left-10

            top-[15px]

            z-[80]

        "

        aria-live="polite"

    >

        {{-- DANO REAL --}}

        <template x-if="hpFeedbackKind === 'damage'">

            <div

                class="
                    hero-hp-feedback hero-hp-feedback--damage

                    hp-feedback-float

                    flex

                    items-center

                    gap-1.5

                    rounded-lg

                    border

                    border-[#7f261d]/45

                    bg-[#2a0d0a]/94

                    px-2

                    py-1

                    shadow-[0_8px_20px_rgba(83,21,15,.28)]

                "

            >

                <span

                    class="

                        font-serif

                        text-[15px]

                        font-black

                        leading-none

                        text-[#ffd8cf]

                        tabular-nums

                    "

                >

                    -<span x-text="hpFeedbackAmount"></span>

                </span>

                <span

                    class="

                        text-[7px]

                        font-black

                        uppercase

                        tracking-[0.10em]

                        text-[#dca89b]

                    "

                >

                    PV

                </span>

                <template x-if="hpFeedbackSecondaryAmount > 0">

                    <span

                        class="

                            ml-0.5

                            border-l

                            border-white/15

                            pl-1.5

                            text-[7px]

                            font-bold

                            text-sky-200

                        "

                    >

                        <span x-text="hpFeedbackSecondaryAmount"></span>

                        absorvido

                    </span>

                </template>

            </div>

        </template>



        {{-- DANO ABSORVIDO POR PV TEMPORÁRIOS --}}

        <template x-if="hpFeedbackKind === 'shield'">

            <div

                class="
                    hero-hp-feedback hero-hp-feedback--shield

                    hp-feedback-float

                    flex

                    items-center

                    gap-1.5

                    rounded-lg

                    border

                    border-sky-300/45

                    bg-sky-950/95

                    px-2

                    py-1

                    shadow-[0_8px_20px_rgba(14,116,144,.22)]

                "

            >

                <svg

                    class="

                        h-3

                        w-3

                        shrink-0

                        text-sky-200

                    "

                    viewBox="0 0 24 24"

                    fill="none"

                    stroke="currentColor"

                    stroke-width="1.8"

                    stroke-linecap="round"

                    stroke-linejoin="round"

                    aria-hidden="true"

                >

                    <path d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z" />

                </svg>

                <span

                    class="

                        font-serif

                        text-[14px]

                        font-black

                        leading-none

                        text-sky-100

                        tabular-nums

                    "

                >

                    -<span x-text="hpFeedbackAmount"></span>

                </span>

                <span

                    class="

                        text-[7px]

                        font-black

                        uppercase

                        tracking-[0.08em]

                        text-sky-300

                    "

                >

                    Absorvido

                </span>

            </div>

        </template>



        {{-- CURA --}}

        <template x-if="hpFeedbackKind === 'heal'">

            <div

                class="
                    hero-hp-feedback hero-hp-feedback--heal

                    hp-heal-rise

                    flex

                    items-center

                    gap-1.5

                    rounded-lg

                    border

                    border-emerald-700/30

                    bg-[#f4f0e8]/96

                    px-2

                    py-1

                    shadow-[0_8px_20px_rgba(52,83,61,.16)]

                "

            >

                <span

                    class="

                        font-serif

                        text-[15px]

                        font-black

                        leading-none

                        text-emerald-800

                        tabular-nums

                    "

                >

                    +<span x-text="hpFeedbackAmount"></span>

                </span>

                <span

                    class="

                        text-[7px]

                        font-black

                        uppercase

                        tracking-[0.10em]

                        text-emerald-700/80

                    "

                >

                    PV

                </span>

            </div>

        </template>

    </div>

</div>
