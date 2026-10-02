@props([

    'character',

    'mechanicalCharacter' => null,

    'customizationClasses' => null,

    'hideZeroExhaustion' => false,

    'imageUrl' => null,

    'className' => 'Classe não definida',

    'subclassName' => null,

    'proficiencyBonus' => 2,

    'classes' => collect(),

    'hasMulticlass' => false,

])

@php

    /*

    |--------------------------------------------------------------------------

    | IDENTIDADE ORIGINAL x FICHA MECÂNICA

    |--------------------------------------------------------------------------

    |

    | $character:

    |   Character original. Continua dono de nome, espécie, antecedente,

    |   regras opcionais e personalização da identidade.

    |

    | $mechanicalCharacter:

    |   Character que representa a ficha mecânica atual. Na Forma Normal ele

    |   é o próprio $character. Em "Nova Ficha", ele é o Character auxiliar.

    |--------------------------------------------------------------------------

    */

    $mechanicalCharacter =

        $mechanicalCharacter

        ?? $character;





    /*

    |--------------------------------------------------------------------------

    | CLASSES

    |--------------------------------------------------------------------------

    */

    $sortedClasses = collect($classes)

        ->sortByDesc(function ($class) {

            return [

                (int) ($class->is_primary ?? false),

                (int) $class->level,

                -((int) ($class->sort_order ?? 0)),

            ];

        })

        ->values();

    $primaryClass = $sortedClasses->first();

    $primarySubclass =

        $primaryClass?->subclass

        ?? $subclassName;

    if ($sortedClasses->isNotEmpty()) {

        $formattedClasses = $sortedClasses

            ->map(function ($class) {

                return $class->class . ' ' . $class->level . 'º';

            })

            ->join(', ');

    } else {

        $formattedClasses =

            $primaryClass?->class

            ?? $className

            ?? 'Classe não definida';

    }

    /*

    |--------------------------------------------------------------------------

    | EXPERIÊNCIA

    |--------------------------------------------------------------------------

    */

    $xpEnabled = (bool) (

        $mechanicalCharacter->experience_enabled

        ?? $mechanicalCharacter->xp_enabled

        ?? false

    );

    $experience =

        $mechanicalCharacter->experience

        ?? $mechanicalCharacter->experience_points

        ?? $mechanicalCharacter->xp

        ?? null;

    $sheetSettings =

        is_array(

            $character->sheet_settings

            ?? null

        )

            ? $character->sheet_settings

            : [];

    $exhaustionRuleActive =

        (bool) data_get(

            $sheetSettings,

            'optional_rules.exhaustion',

            false

        );


    /*
    |--------------------------------------------------------------------------
    | TEMA ATUAL
    |--------------------------------------------------------------------------
    |
    | O editor de Exaustão usa x-teleport="body". Como ele deixa de herdar
    | as variáveis da ficha, reaplicamos explicitamente o tema no portal.
    |
    */
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
            /*
            |--------------------------------------------------------------------------
            | HERO DATA — EXAUSTÃO / PADRÃO GLOBAL
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-modal {
                display: flex;
                width: min(540px, calc(100vw - 28px));
                max-height: min(92dvh, 760px);
                flex-direction: column;
                overflow: hidden;

                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                color:
                    var(--theme-modal-text, var(--theme-text-primary)) !important;

                box-shadow:
                    var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.30)) !important;
            }

            @supports not (height: 1dvh) {
                .character-sheet-theme[data-theme]
                .hero-data-exhaustion-modal {
                    max-height: min(92vh, 760px);
                }
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-modal-header,
            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-modal-footer {
                flex: 0 0 auto;

                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-modal-body {
                min-height: 0;
                flex: 1 1 auto;
                overflow-y: auto;

                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width: thin;
                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 30%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-kicker,
            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-label {
                color:
                    var(--theme-modal-label, var(--theme-label)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-close {
                border:
                    1px solid
                    var(--theme-border);

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-close:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-level {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-level:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-level.is-active {
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
            .hero-data-exhaustion-level.is-lethal {
                border-color:
                    #991b1b !important;

                background:
                    #991b1b !important;

                color:
                    #fff !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-stat {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;

                color:
                    var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-stat strong,
            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-stat-value {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-stat.is-lethal {
                border-color:
                    rgba(153, 27, 27, .34) !important;

                background:
                    #fff1f2 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-stat.is-lethal
            .hero-data-exhaustion-stat-value {
                color:
                    #991b1b !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-note {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-inset) !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-note strong {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-secondary {
                border:
                    1px solid
                    var(--theme-border);

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-secondary:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-exhaustion-primary {
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
            .hero-data-exhaustion-primary:hover {
                background:
                    var(--theme-button-hover-background) !important;
            }

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            )
            .hero-data-exhaustion-stat.is-lethal {
                background:
                    rgba(127,29,29,.18) !important;
            }

            @media (max-width: 520px) {
                .character-sheet-theme[data-theme]
                .hero-data-exhaustion-modal {
                    width: calc(100vw - 20px);
                    border-radius: 15px;
                }

                .hero-data-exhaustion-level-grid {
                    grid-template-columns:
                        repeat(4, minmax(0, 1fr)) !important;
                }

                .hero-data-exhaustion-stat-grid {
                    grid-template-columns:
                        minmax(0, 1fr) !important;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | HERO DATA V2 — RETRATO / FEEDBACK DE DANO
            |--------------------------------------------------------------------------
            |
            | O Hero Header V22 continua sendo a fonte de verdade do impacto.
            | Este componente transforma esses estados em linguagem visual
            | dentro do card do personagem:
            |
            | azul    = PV temporários absorvendo;
            | dourado = Vida Máxima Extra sendo consumida;
            | vermelho= dano alcançou PV normais;
            | verde   = cura;
            | sangrando/crítico = estado persistente.
            |
            */


            /* ==============================================================
               BASE DO RETRATO
            ============================================================== */

            .character-sheet-theme[data-theme]
            .hero-data-portrait {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card-alt) !important;

                box-shadow:
                    0 2px 7px rgba(0,0,0,.07) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-portrait:hover {
                border-color:
                    var(--theme-border-strong) !important;

                box-shadow:
                    0 4px 12px rgba(0,0,0,.09) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-portrait-media {
                background:
                    var(--theme-surface-inset) !important;

                isolation:
                    isolate;
            }

            .hero-data-portrait-image {
                transform:
                    translateZ(0);

                will-change:
                    filter;
            }


            /* ==============================================================
               SANGRANDO — ESTADO PERSISTENTE
            ============================================================== */

            @keyframes hero-data-blood-vignette-v2 {
                0%, 64%, 100% {
                    opacity:
                        .10;
                }

                74% {
                    opacity:
                        .34;
                }

                82% {
                    opacity:
                        .15;
                }

                90% {
                    opacity:
                        .27;
                }
            }

            @keyframes hero-data-blood-dot-v2 {
                0%, 64%, 100% {
                    box-shadow:
                        0 0 0 rgba(185,28,28,0);

                    transform:
                        scale(1);
                }

                74% {
                    box-shadow:
                        0 0 7px rgba(185,28,28,.42);

                    transform:
                        scale(1.18);
                }

                90% {
                    box-shadow:
                        0 0 4px rgba(185,28,28,.26);

                    transform:
                        scale(1.08);
                }
            }

            .hero-data-health-vignette {
                opacity:
                    0;

                background:
                    radial-gradient(
                        ellipse at center,
                        transparent 33%,
                        rgba(127,29,29,.08) 62%,
                        rgba(127,29,29,.34) 100%
                    );

                mix-blend-mode:
                    multiply;
            }

            .hero-header-v22[data-health-state="bloodied"]
            .hero-data-health-vignette {
                animation:
                    hero-data-blood-vignette-v2
                    3.8s
                    ease-in-out
                    infinite;
            }

            .hero-header-v22[data-health-state="bloodied"]
            .hero-data-status-dot {
                animation:
                    hero-data-blood-dot-v2
                    3.8s
                    ease-in-out
                    infinite;
            }

            .hero-header-v22[data-health-state="bloodied"]
            .hero-data-status-strip {
                border-color:
                    rgba(180,83,9,.28) !important;

                background:
                    linear-gradient(
                        90deg,
                        rgba(255,251,235,.92) 0%,
                        var(--theme-surface-raised) 30%
                    ) !important;
            }


            /* ==============================================================
               CRÍTICO — BATIMENTO DUPLO
            ============================================================== */

            @keyframes hero-data-critical-vignette-v2 {
                0%, 100% {
                    opacity:
                        .18;
                }

                12% {
                    opacity:
                        .56;
                }

                22% {
                    opacity:
                        .22;
                }

                32% {
                    opacity:
                        .46;
                }

                54% {
                    opacity:
                        .18;
                }
            }

            @keyframes hero-data-critical-dot-v2 {
                0%, 100% {
                    transform:
                        scale(1);

                    box-shadow:
                        0 0 0 rgba(220,38,38,0);
                }

                12% {
                    transform:
                        scale(1.30);

                    box-shadow:
                        0 0 9px rgba(220,38,38,.56);
                }

                22% {
                    transform:
                        scale(1);
                }

                32% {
                    transform:
                        scale(1.20);

                    box-shadow:
                        0 0 6px rgba(220,38,38,.38);
                }
            }

            .hero-header-v22[data-health-state="critical"]
            .hero-data-health-vignette {
                background:
                    radial-gradient(
                        ellipse at center,
                        transparent 24%,
                        rgba(127,29,29,.14) 54%,
                        rgba(185,28,28,.54) 100%
                    );

                animation:
                    hero-data-critical-vignette-v2
                    2.15s
                    ease-in-out
                    infinite;
            }

            .hero-header-v22[data-health-state="critical"]
            .hero-data-status-dot {
                animation:
                    hero-data-critical-dot-v2
                    2.15s
                    ease-in-out
                    infinite;
            }

            .hero-header-v22[data-health-state="critical"]
            .hero-data-status-strip {
                border-color:
                    rgba(185,28,28,.34) !important;

                background:
                    linear-gradient(
                        90deg,
                        rgba(254,242,242,.96) 0%,
                        var(--theme-surface-raised) 34%
                    ) !important;
            }


            /* ==============================================================
               PV TEMPORÁRIOS — BLOQUEIO AZUL
            ============================================================== */

            @keyframes hero-data-shield-overlay-v2 {
                0%, 100% {
                    opacity:
                        0;
                }

                18% {
                    opacity:
                        .82;
                }

                62% {
                    opacity:
                        .18;
                }
            }

            @keyframes hero-data-shield-ring-v2 {
                0% {
                    opacity:
                        0;

                    transform:
                        scale(.56);
                }

                24% {
                    opacity:
                        .88;
                }

                100% {
                    opacity:
                        0;

                    transform:
                        scale(1.14);
                }
            }

            .hero-data-impact--shield {
                background:
                    radial-gradient(
                        circle at center,
                        rgba(186,230,253,.16) 0%,
                        rgba(56,189,248,.10) 46%,
                        rgba(3,105,161,.34) 100%
                    );

                animation:
                    hero-data-shield-overlay-v2
                    .60s
                    ease-out
                    both;
            }

            .hero-data-impact-shield-ring {
                position:
                    absolute;

                inset:
                    14%;

                border:
                    1px solid rgba(186,230,253,.82);

                border-radius:
                    999px;

                box-shadow:
                    0 0 18px rgba(56,189,248,.26),
                    inset 0 0 16px rgba(186,230,253,.14);

                animation:
                    hero-data-shield-ring-v2
                    .58s
                    ease-out
                    both;
            }

            .hero-data-impact-shield-crack {
                position:
                    absolute;

                inset:
                    0;

                opacity:
                    .72;

                background:
                    linear-gradient(
                        116deg,
                        transparent 0 47%,
                        rgba(224,242,254,.88) 48% 49%,
                        transparent 50% 100%
                    ),
                    linear-gradient(
                        68deg,
                        transparent 0 59%,
                        rgba(125,211,252,.66) 60% 61%,
                        transparent 62% 100%
                    );
            }


            /* ==============================================================
               VIDA MÁXIMA EXTRA — DOURADO
            ============================================================== */

            @keyframes hero-data-bonus-overlay-v2 {
                0%, 100% {
                    opacity:
                        0;
                }

                18% {
                    opacity:
                        .74;
                }

                56% {
                    opacity:
                        .18;
                }
            }

            @keyframes hero-data-bonus-sweep-v2 {
                0% {
                    transform:
                        translateX(-140%)
                        skewX(-17deg);
                }

                100% {
                    transform:
                        translateX(260%)
                        skewX(-17deg);
                }
            }

            .hero-data-impact--bonus {
                background:
                    radial-gradient(
                        circle at 62% 45%,
                        rgba(255,244,196,.32) 0%,
                        rgba(217,164,65,.14) 45%,
                        rgba(122,76,8,.28) 100%
                    );

                animation:
                    hero-data-bonus-overlay-v2
                    .64s
                    ease-out
                    both;
            }

            .hero-data-impact-bonus-sweep {
                position:
                    absolute;

                inset-block:
                    0;

                left:
                    0;

                width:
                    42%;

                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        rgba(255,255,255,.68),
                        transparent
                    );

                animation:
                    hero-data-bonus-sweep-v2
                    .52s
                    ease-out
                    both;
            }

            .hero-data-impact-bonus-spark {
                position:
                    absolute;

                right:
                    16%;

                top:
                    24%;

                width:
                    6px;

                height:
                    6px;

                border-radius:
                    999px;

                background:
                    rgba(255,239,172,.96);

                box-shadow:
                    12px 9px 0 -2px rgba(255,228,127,.82),
                    -8px 18px 0 -2px rgba(217,164,65,.72),
                    5px 28px 0 -3px rgba(255,248,213,.76);
            }


            /* ==============================================================
               DANO REAL — FLASH VERMELHO / CORTE
            ============================================================== */

            @keyframes hero-data-damage-overlay-soft-v2 {
                0%, 100% {
                    opacity:
                        0;
                }

                20% {
                    opacity:
                        .28;
                }

                56% {
                    opacity:
                        .08;
                }
            }

            @keyframes hero-data-damage-overlay-medium-v2 {
                0%, 100% {
                    opacity:
                        0;
                }

                16% {
                    opacity:
                        .46;
                }

                48% {
                    opacity:
                        .13;
                }
            }

            @keyframes hero-data-damage-overlay-heavy-v2 {
                0%, 100% {
                    opacity:
                        0;
                }

                12% {
                    opacity:
                        .66;
                }

                38% {
                    opacity:
                        .24;
                }

                64% {
                    opacity:
                        .08;
                }
            }

            @keyframes hero-data-damage-slash-v2 {
                0% {
                    opacity:
                        0;

                    transform:
                        translateX(-42%)
                        rotate(-14deg)
                        scaleX(.55);
                }

                22% {
                    opacity:
                        .82;
                }

                100% {
                    opacity:
                        0;

                    transform:
                        translateX(54%)
                        rotate(-14deg)
                        scaleX(1.08);
                }
            }

            .hero-data-impact--damage {
                background:
                    radial-gradient(
                        circle at center,
                        rgba(239,68,68,.22) 0%,
                        rgba(185,28,28,.14) 54%,
                        rgba(127,29,29,.46) 100%
                    );

                mix-blend-mode:
                    screen;
            }

            .hero-header-v22[data-impact-strength="soft"]
            .hero-data-impact--damage {
                animation:
                    hero-data-damage-overlay-soft-v2
                    .42s
                    ease-out
                    both;
            }

            .hero-header-v22[data-impact-strength="medium"]
            .hero-data-impact--damage {
                animation:
                    hero-data-damage-overlay-medium-v2
                    .54s
                    ease-out
                    both;
            }

            .hero-header-v22[data-impact-strength="heavy"]
            .hero-data-impact--damage {
                animation:
                    hero-data-damage-overlay-heavy-v2
                    .72s
                    ease-out
                    both;
            }

            .hero-data-impact-damage-slash {
                position:
                    absolute;

                left:
                    18%;

                top:
                    48%;

                width:
                    66%;

                height:
                    1px;

                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        rgba(254,226,226,.84),
                        rgba(239,68,68,.92),
                        transparent
                    );

                box-shadow:
                    0 0 7px rgba(239,68,68,.34);

                transform-origin:
                    center;

                animation:
                    hero-data-damage-slash-v2
                    .48s
                    ease-out
                    both;
            }

            .hero-header-v22[data-impact-strength="soft"]
            .hero-data-impact-damage-slash {
                opacity:
                    .40;
            }

            .hero-header-v22[data-impact-strength="heavy"]
            .hero-data-impact-damage-slash {
                height:
                    2px;

                box-shadow:
                    0 0 11px rgba(239,68,68,.48);
            }


            /* ==============================================================
               CURA
            ============================================================== */

            @keyframes hero-data-heal-overlay-v2 {
                0%, 100% {
                    opacity:
                        0;
                }

                28% {
                    opacity:
                        .42;
                }

                64% {
                    opacity:
                        .10;
                }
            }

            .hero-data-impact--heal {
                background:
                    radial-gradient(
                        circle at center,
                        rgba(167,243,208,.22) 0%,
                        rgba(16,185,129,.10) 50%,
                        rgba(4,120,87,.20) 100%
                    );

                animation:
                    hero-data-heal-overlay-v2
                    .60s
                    ease-out
                    both;
            }

            .hero-data-impact-heal-glow {
                position:
                    absolute;

                inset:
                    12%;

                border-radius:
                    50%;

                box-shadow:
                    inset 0 0 24px rgba(167,243,208,.16),
                    0 0 18px rgba(16,185,129,.12);
            }


            /* ==============================================================
               COMBINAÇÕES
            ============================================================== */

            /*
             * Golpes mistos podem ativar azul/dourado/vermelho no mesmo frame.
             * As camadas possuem z-index crescente para representar a defesa
             * sendo atravessada até chegar aos PV normais.
             */

            .hero-data-impact--shield {
                z-index:
                    12;
            }

            .hero-data-impact--bonus {
                z-index:
                    13;
            }

            .hero-data-impact--damage {
                z-index:
                    14;
            }


            /* ==============================================================
               MORTO / CAÍDO
            ============================================================== */

            .hero-header-v22:is(
                [data-health-state="downed"],
                [data-health-state="dead"]
            )
            .hero-data-health-vignette {
                opacity:
                    .38;

                background:
                    radial-gradient(
                        ellipse at center,
                        transparent 26%,
                        rgba(32,20,20,.18) 58%,
                        rgba(20,10,10,.62) 100%
                    );

                animation:
                    none;
            }


            /* ==============================================================
               MOVIMENTO REDUZIDO
            ============================================================== */

            @media (prefers-reduced-motion: reduce) {
                .hero-data-health-vignette,
                .hero-data-status-dot,
                .hero-data-impact,
                .hero-data-impact-shield-ring,
                .hero-data-impact-bonus-sweep,
                .hero-data-impact-damage-slash {
                    animation:
                        none !important;
                }
            }

        </style>
    @endpush
@endonce

<div

    class="

        hero-data

        flex

        min-w-0

        flex-col

        items-start

        gap-3

        select-none

        sm:flex-row

        sm:items-start

    "

>

    {{-- ============================================================

         RETRATO / NÍVEL / ESTADO / PROFICIÊNCIA

    ============================================================= --}}

    <div

        class="

            relative

            flex

            w-[166px]

            shrink-0

            flex-col

        "

    >

        {{-- FOTO / CONFIGURAÇÕES --}}

        <button

            type="button"

            @click="

                window.dispatchEvent(

                    new CustomEvent(

                        'open-character-customization',

                        {

                            detail: {

                                tab: 'themes'

                            }

                        }

                    )

                )

            "

            class="

                hero-data-portrait

                group

                relative

                block

                h-[170px]

                w-[166px]

                overflow-hidden

                rounded-xl

                border

                border-[#cdbb9f]/75

                bg-[#efe9dc]

                p-1

                text-left

                shadow-[0_2px_6px_rgba(83,21,15,.07)]

                transition-[border-color,box-shadow,background-color]

                duration-200

                hover:border-[#a0774d]/60

                hover:shadow-[0_3px_8px_rgba(83,21,15,.09)]

                focus:outline-none

                focus-visible:ring-2

                focus-visible:ring-[#8c6239]/25

                focus-visible:ring-offset-1

                focus-visible:ring-offset-[#faf8f2]

            "

            title="Abrir temas e configurações do personagem"

            aria-label="Abrir temas e configurações do personagem"

        >

            <div

                class="

                    hero-data-portrait-media

                    relative

                    h-full

                    w-full

                    overflow-hidden

                    rounded-lg

                    bg-[#f4f1e8]

                "

            >

                {{-- MOLDURA INTERNA --}}

                <div

                    class="

                        pointer-events-none

                        absolute

                        inset-[2px]

                        z-10

                        rounded-md

                        border

                        border-[#cdbb9f]/55

                    "

                ></div>

                @if ($imageUrl)

                    <img

                        src="{{ $imageUrl }}"

                        alt="{{ $character->name }}"

                        :class="{

                            'grayscale opacity-60 contrast-125': isDead || (isDowned && !isMorquenConsciousAtZero)

                        }"

                        class="

                            hero-data-portrait-image

                            h-full

                            w-full

                            object-cover

                            object-top

                            transition-all

                            duration-500

                        "

                    >

                @else

                    <div

                        :class="{

                            'grayscale opacity-60': isDead || (isDowned && !isMorquenConsciousAtZero)

                        }"

                        class="

                            hero-data-portrait-image
                            hero-data-portrait-placeholder

                            flex

                            h-full

                            w-full

                            items-center

                            justify-center

                            font-serif

                            text-3xl

                            font-black

                            text-[#53150f]

                        "

                    >

                        {{ strtoupper(mb_substr($character->name, 0, 2)) }}

                    </div>

                @endif


                {{-- ========================================================
                     ESTADO PERSISTENTE — SANGRANDO / CRÍTICO
                ========================================================= --}}

                <div
                    class="
                        hero-data-health-vignette
                        pointer-events-none
                        absolute
                        inset-0
                        z-[11]
                    "
                    aria-hidden="true"
                ></div>


                {{-- ========================================================
                     IMPACTO — PV TEMPORÁRIOS
                ========================================================= --}}

                <div
                    x-show="
                        hpImpactShieldAmount > 0
                    "
                    x-cloak
                    class="
                        hero-data-impact
                        hero-data-impact--shield

                        pointer-events-none
                        absolute
                        inset-0
                        z-[12]
                    "
                    aria-hidden="true"
                >
                    <span class="hero-data-impact-shield-ring"></span>
                    <span class="hero-data-impact-shield-crack"></span>
                </div>


                {{-- ========================================================
                     IMPACTO — VIDA MÁXIMA EXTRA
                ========================================================= --}}

                <div
                    x-show="
                        hpImpactBonusAmount > 0
                    "
                    x-cloak
                    class="
                        hero-data-impact
                        hero-data-impact--bonus

                        pointer-events-none
                        absolute
                        inset-0
                        z-[13]
                    "
                    aria-hidden="true"
                >
                    <span class="hero-data-impact-bonus-sweep"></span>
                    <span class="hero-data-impact-bonus-spark"></span>
                </div>


                {{-- ========================================================
                     IMPACTO — DANO REAL
                ========================================================= --}}

                <div
                    x-show="
                        hpImpactDamageAmount > 0
                    "
                    x-cloak
                    class="
                        hero-data-impact
                        hero-data-impact--damage

                        pointer-events-none
                        absolute
                        inset-0
                        z-[14]
                    "
                    aria-hidden="true"
                >
                    <span class="hero-data-impact-damage-flash"></span>
                    <span class="hero-data-impact-damage-slash"></span>
                </div>


                {{-- ========================================================
                     IMPACTO — CURA
                ========================================================= --}}

                <div
                    x-show="
                        hpImpactKind === 'heal'
                    "
                    x-cloak
                    class="
                        hero-data-impact
                        hero-data-impact--heal

                        pointer-events-none
                        absolute
                        inset-0
                        z-[15]
                    "
                    aria-hidden="true"
                >
                    <span class="hero-data-impact-heal-glow"></span>
                </div>

            </div>

        </button>





        {{-- ========================================================

             EXAUSTÃO

        ========================================================= --}}

        @if ($exhaustionRuleActive)

            <button

                type="button"

                x-show="

                    !@js($hideZeroExhaustion)

                    || exhaustionLevel > 0

                "

                x-cloak

                @click.stop="

                    openExhaustionEditor()

                "

                class="

                    hero-data-exhaustion-badge

                    group

                    absolute

                    left-2

                    top-2

                    z-30

                    flex

                    h-[40px]

                    w-[40px]

                    items-center

                    justify-center

                    rounded-full

                    border

                    border-[#cdbb9f]/80

                    bg-[#fffaf4]/95

                    shadow-[0_2px_6px_rgba(83,21,15,.06)]

                    transition

                    duration-150

                    hover:border-[#a0774d]/55

                    hover:bg-[#fffdf8]

                    focus:outline-none

                    focus-visible:ring-2

                    focus-visible:ring-[#8c6239]/20

                "

                :class="{

                    'border-[#9f5c52]/50':

                        exhaustionLevel >= 6

                }"

                :title="

                    'Exaustão: '

                    + exhaustionLevel

                "

                aria-label="Editar exaustão"

            >

                <span

                    class="

                        hero-data-exhaustion-content

                        relative

                        z-10

                        flex

                        flex-col

                        items-center

                        justify-center

                    "

                >

                    <img

                        src="{{ asset('images/exaustao.png') }}"

                        alt=""

                        class="

                            h-[9px]

                            w-[9px]

                            object-contain

                            opacity-55

                        "

                    >

                    <span

                        class="

                            mt-[1px]

                            font-serif

                            text-[15px]

                            font-black

                            leading-none

                            text-[#6b1d14]

                        "

                        :class="{

                            'text-[#8b1e16]':

                                exhaustionLevel >= 6

                        }"

                        x-text="

                            exhaustionLevel

                        "

                    ></span>

                </span>

            </button>

        @endif





        {{-- ========================================================

             NÍVEL

        ========================================================= --}}

        <div

            class="

                hero-data-level-badge

                absolute

                right-2

                top-2

                z-30

                flex

                h-[40px]

                w-[40px]

                items-center

                justify-center

                rounded-full

                border

                border-[#cdbb9f]/80

                bg-[#fffaf4]/95

                shadow-[0_2px_6px_rgba(83,21,15,.06)]

            "

        >

            <div

                class="

                    hero-data-level-content

                    relative

                    z-10

                    flex

                    flex-col

                    items-center

                    justify-center

                "

            >

                <span

                    class="

                        text-[5px]

                        font-black

                        uppercase

                        leading-none

                        tracking-[0.13em]

                        text-[#7d604d]

                    "

                >

                    Nível

                </span>

                <span

                    class="

                        mt-[2px]

                        font-serif

                        text-[16px]

                        font-black

                        leading-none

                        text-[#6b1d14]

                    "

                >

                    {{ $mechanicalCharacter->level }}

                </span>

            </div>

        </div>





        {{-- ========================================================

             XP OPCIONAL

        ========================================================= --}}

        @if ($xpEnabled)

            <div

                class="

                    hero-data-xp-badge

                    absolute

                    right-2

                    top-[48px]

                    z-20

                    flex

                    min-w-[54px]

                    items-center

                    justify-center

                    gap-1

                    rounded-md

                    border

                    border-[#d8c7ab]/70

                    bg-[#faf8f2]

                    px-1.5

                    py-1

                    shadow-[0_2px_5px_rgba(83,21,15,.07)]

                "

            >

                <span

                    class="

                        text-[5px]

                        font-black

                        uppercase

                        tracking-[0.16em]

                        text-[#8c6239]

                    "

                >

                    XP

                </span>

                @if ($experience !== null)

                    <span

                        class="

                            font-serif

                            text-[10px]

                            font-black

                            leading-none

                            text-[#53150f]

                        "

                    >

                        {{ $experience }}

                    </span>

                @endif

            </div>

        @endif





        {{-- ========================================================

             ESTADO + PROFICIÊNCIA

        ========================================================= --}}

        <div

            class="

                hero-data-status-strip

                relative

                z-20

                -mt-[18px]

                flex

                h-[24px]

                w-full

                items-center

                overflow-hidden

                rounded-b-lg

                border

                border-[#cdbb9f]/70

                bg-[#faf8f2]

                shadow-[0_2px_5px_rgba(83,21,15,.07)]

            "

        >

            {{-- ESTADO --}}

            <div

                class="

                    flex

                    min-w-0

                    flex-1

                    items-center

                    px-2

                "

            >

                <span

                    class="

                        hero-data-status-dot

                        mr-1.5

                        h-1.5

                        w-1.5

                        shrink-0

                        rounded-full

                        bg-[#8c6239]

                    "

                    :class="{

                        'bg-red-700':

                            isDead ||

                            isCritical,

                        'bg-amber-600':

                            isMorquenConsciousAtZero ||

                            isBloodied,

                        'bg-emerald-700':

                            (

                                isStable &&

                                !isMorquenConsciousAtZero

                            ) ||

                            (

                                !isDowned &&

                                !isCritical &&

                                !isBloodied

                            )

                    }"

                ></span>

                <span

                    x-text="statusLabel"

                    :class="statusClass"

                    class="

                        hero-data-status-text

                        truncate

                        text-[8px]

                        font-black

                        uppercase

                        tracking-[0.15em]

                    "

                ></span>

            </div>





            {{-- DIVISOR --}}

            <div

                class="

                    h-4

                    w-px

                    shrink-0

                    bg-[#d8c7ab]/70

                "

            ></div>





            {{-- PROFICIÊNCIA --}}

            <div

                class="

                    flex

                    h-full

                    shrink-0

                    items-center

                    gap-1

                    px-2

                "

            >

                <span

                    class="

                        text-[8px]

                        font-black

                        uppercase

                        tracking-[0.13em]

                        text-[#8c6239]

                    "

                >

                    Prof.

                </span>

                <span

                    class="

                        font-serif

                        text-[17px]

                        font-black

                        leading-none

                        text-[#6b1d14]

                        drop-shadow-[0_1px_1px_rgba(83,21,15,.12)]

                    "

                >

                    {{ $proficiencyBonus >= 0 ? '+' : '' }}{{ $proficiencyBonus }}

                </span>

            </div>

        </div>

    </div>





    {{-- ============================================================

         FICHA DE IDENTIDADE

    ============================================================= --}}

    <div

        class="

            relative

            flex

            min-w-0

            flex-1

            items-stretch

        "

    >

        <div

            class="

                hero-data-identity-panel

                relative

                flex

                h-[176px]

                min-w-0

                flex-1

                flex-col

                justify-center

                overflow-visible

                px-3

                py-2

            "

        >

                        {{-- ====================================================

                 SUPERFÍCIE DINÂMICA DA IDENTIDADE

            ===================================================== --}}

            <div

                class="

                    hero-data-identity-surface

                    pointer-events-none

                    absolute

                    inset-y-0

                    left-0

                "

                aria-hidden="true"

            ></div>





{{-- ====================================================

                 DETALHE DE JUNÇÃO

            ===================================================== --}}

            <div

                class="

                    hero-data-junction-dot

                    absolute

                    -left-[5px]

                    -top-[5px]

                    z-20

                    h-3

                    w-3

                    rounded-full

                    ring-2

                "

            ></div>





            {{-- ====================================================

                 CONTEÚDO

            ===================================================== --}}

            <div

                class="

                    hero-data-identity-content

                    relative

                    z-10

                    flex

                    min-h-0

                    w-full

                    flex-1

                    flex-col

                    justify-center

                    gap-1.5

                "

            >

                {{-- =================================================

                     NOME

                ================================================== --}}

                <div class="min-w-0">

                    <div

                        class="

                            hero-data-name-field

                            flex

                            h-[31px]

                            w-full

                            items-center

                            border

                            border-[#d8c7ab]/55

                            bg-[#f4f1e8]

                            px-2

                            shadow-[inset_0_1px_2px_rgba(83,21,15,.04)]

                        "

                    >

                        <h1

                            class="

                                truncate

                                font-serif

                                text-[19px]

                                font-black

                                leading-none

                                text-[#53150f]

                                sm:text-[21px]

                            "

                            title="{{ $character->name }}"

                        >

                            {{ $character->name }}

                        </h1>

                    </div>

                    <p

                        class="

                            hero-data-field-label

                            mt-[2px]

                            px-2

                            text-[6px]

                            font-extrabold

                            uppercase

                            tracking-[0.18em]

                            hero-data-exhaustion-kicker
                            text-[#8c6239]

                        "

                    >

                        Nome do Personagem

                    </p>

                </div>





                {{-- =================================================

                     GRID DE INFORMAÇÕES

                ================================================== --}}

                <div

                    class="

                        hero-data-info-grid

                        grid

                        min-h-0

                        grid-cols-2

                        gap-x-3

                        gap-y-1

                    "

                >

                    {{-- ANTECEDENTE --}}

                    <div class="min-w-0">

                        <div

                            class="

                                hero-data-info-field

                                flex

                                h-[23px]

                                items-center

                                border

                                border-[#d8c7ab]/45

                                bg-[#f4f1e8]

                                px-2

                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]

                            "

                        >

                            <p

                                class="

                                    truncate

                                    font-serif

                                    text-[11px]

                                    font-bold

                                    leading-none

                                    text-[#53150f]

                                    sm:text-xs

                                "

                                title="{{ $character->background ?? '—' }}"

                            >

                                {{ $character->background ?? '—' }}

                            </p>

                        </div>

                        <p

                            class="

                                mt-[2px]

                                px-2

                                text-[5.5px]

                                font-extrabold

                                uppercase

                                tracking-[0.14em]

                                text-[#8c6239]

                            "

                        >

                            Antecedente

                        </p>

                    </div>





                    {{-- CLASSE --}}

                    <div class="min-w-0">

                        <div

                            class="

                                hero-data-info-field

                                flex

                                h-[23px]

                                items-center

                                border

                                border-[#d8c7ab]/45

                                bg-[#f4f1e8]

                                px-2

                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]

                            "

                        >

                            <p

                                class="

                                    truncate

                                    font-serif

                                    text-[11px]

                                    font-bold

                                    leading-none

                                    text-[#53150f]

                                    sm:text-xs

                                "

                                title="{{ $formattedClasses }}"

                            >

                                {{ $formattedClasses }}

                            </p>

                        </div>

                        <p

                            class="

                                mt-[2px]

                                px-2

                                text-[5.5px]

                                font-extrabold

                                uppercase

                                tracking-[0.14em]

                                text-[#8c6239]

                            "

                        >

                            {{ $hasMulticlass || $sortedClasses->count() > 1 ? 'Classes' : 'Classe' }}

                        </p>

                    </div>





                    {{-- ESPÉCIE --}}

                    <div class="min-w-0">

                        <div

                            class="

                                hero-data-info-field

                                flex

                                h-[23px]

                                items-center

                                border

                                border-[#d8c7ab]/45

                                bg-[#f4f1e8]

                                px-2

                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]

                            "

                        >

                            <p

                                class="

                                    truncate

                                    font-serif

                                    text-[11px]

                                    font-bold

                                    leading-none

                                    text-[#53150f]

                                    sm:text-xs

                                "

                                title="{{ $character->species ?? '—' }}"

                            >

                                {{ $character->species ?? '—' }}

                            </p>

                        </div>

                        <p

                            class="

                                mt-[2px]

                                px-2

                                text-[5.5px]

                                font-extrabold

                                uppercase

                                tracking-[0.14em]

                                text-[#8c6239]

                            "

                        >

                            Espécie

                        </p>

                    </div>





                    {{-- SUBCLASSE --}}

                    <div class="min-w-0">

                        <div

                            class="

                                hero-data-info-field

                                flex

                                h-[23px]

                                items-center

                                border

                                border-[#d8c7ab]/45

                                bg-[#f4f1e8]

                                px-2

                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]

                            "

                        >

                            <p

                                class="

                                    truncate

                                    font-serif

                                    text-[11px]

                                    font-bold

                                    leading-none

                                    text-[#53150f]

                                    sm:text-xs

                                "

                                title="{{ $primarySubclass ?? '—' }}"

                            >

                                {{ $primarySubclass ?? '—' }}

                            </p>

                        </div>

                        <p

                            class="

                                mt-[2px]

                                px-2

                                text-[5.5px]

                                font-extrabold

                                uppercase

                                tracking-[0.14em]

                                text-[#8c6239]

                            "

                        >

                            Subclasse

                        </p>

                    </div>

                </div>

            </div>





            {{-- ====================================================

                 LINHAS DECORATIVAS SUPERIOR / INFERIOR

                 Servem também para continuar no Hero Header.

            ===================================================== --}}

            <div

                class="

                    hero-data-rule

                    hero-data-rule--top

                    pointer-events-none

                    absolute

                    left-2

                    top-[8px]

                    h-px

                    bg-[#d8c7ab]/55

                "

            ></div>

            <div

                class="

                    hero-data-rule

                    hero-data-rule--bottom

                    pointer-events-none

                    absolute

                    bottom-[8px]

                    left-2





                    h-px

                    bg-[#d8c7ab]/55

                "

            ></div>

        </div>

    </div>

</div>

@if ($exhaustionRuleActive)

{{-- ================================================================

     EXAUSTÃO — EDITOR

================================================================= --}}

<template x-teleport="body">

    <div

        x-show="exhaustionOpen"

        x-cloak

        x-transition:enter="character-theme-modal-layer-enter"
        x-transition:enter-start="character-theme-modal-layer-enter-start"
        x-transition:enter-end="character-theme-modal-layer-enter-end"
        x-transition:leave="character-theme-modal-layer-leave"
        x-transition:leave-start="character-theme-modal-layer-leave-start"
        x-transition:leave-end="character-theme-modal-layer-leave-end"

        @keydown.escape.window="

            cancelExhaustionEditor()

        "

        data-theme="{{ $characterTheme }}"

        class="

            character-sheet-theme
            character-theme-modal-layer

            fixed

            inset-0

            z-[320]

            flex

            items-center

            justify-center

            p-4

        "

        role="dialog"

        aria-modal="true"

        aria-label="Editar exaustão"

    >

        <div

            class="

                character-theme-modal-backdrop

                absolute

                inset-0

            "

            @click="

                cancelExhaustionEditor()

            "

        ></div>

        <div

            x-show="exhaustionOpen"

            x-transition:enter="character-theme-modal-panel-enter"
            x-transition:enter-start="character-theme-modal-panel-enter-start"
            x-transition:enter-end="character-theme-modal-panel-enter-end"
            x-transition:leave="character-theme-modal-panel-leave"
            x-transition:leave-start="character-theme-modal-panel-leave-start"
            x-transition:leave-end="character-theme-modal-panel-leave-end"

            @click.stop

            class="

                character-theme-modal
                hero-data-exhaustion-modal

                relative

                z-10

                rounded-2xl

                border

            "

        >

            {{-- HEADER --}}

            <div

                class="

                    character-theme-modal-header
                    hero-data-exhaustion-modal-header

                    flex

                    items-center

                    justify-between

                    gap-4

                    border-b

                    border-[#a0774d]/30

                    bg-[#eadbc8]

                    px-5

                    py-4

                    shadow-[inset_0_1px_0_rgba(255,255,255,.72)]

                "

            >

                <div class="min-w-0">

                    <p

                        class="

                            text-[9px]

                            font-black

                            uppercase

                            tracking-[0.18em]

                            text-[#8c6239]

                        "

                    >

                        Condição acumulativa

                    </p>

                    <h3

                        class="

                            character-theme-modal-title

                            mt-0.5

                            font-serif

                            text-[22px]

                            font-black

                            leading-none

                            text-[#53150f]

                        "

                    >

                        Exaustão

                    </h3>

                    <p

                        class="

                            character-theme-modal-copy

                            mt-1

                            text-[11px]

                            leading-relaxed

                            text-[#76553f]

                        "

                    >

                        Cada nível piora as rolagens e reduz o deslocamento.

                    </p>

                </div>

                <button

                    type="button"

                    @click="

                        cancelExhaustionEditor()

                    "

                    :disabled="

                        savingExhaustion

                    "

                    class="

                        hero-data-exhaustion-close

                        flex

                        h-9

                        w-9

                        shrink-0

                        items-center

                        justify-center

                        rounded-lg

                        text-[22px]

                        leading-none

                        text-[#8c6239]

                        transition

                        hover:bg-[#fffdf8]/55

                        hover:text-[#53150f]

                        disabled:opacity-50

                    "

                    aria-label="Fechar"

                >

                    ×

                </button>

            </div>

            {{-- BODY --}}

            <div class="hero-data-exhaustion-modal-body space-y-4 px-5 py-5">

                <div>

                    <p

                        class="

                            hero-data-exhaustion-label

                            text-[10px]

                            font-black

                            uppercase

                            tracking-[0.12em]

                            text-[#8c6239]

                        "

                    >

                        Nível de exaustão

                    </p>

                    <div

                        class="

                            hero-data-exhaustion-level-grid

                            mt-2

                            grid

                            grid-cols-7

                            gap-2

                        "

                    >

                        <template

                            x-for="

                                level in [0, 1, 2, 3, 4, 5, 6]

                            "

                            :key="

                                'exhaustion-level-' + level

                            "

                        >

                            <button

                                type="button"

                                @click="

                                    setExhaustionDraft(level)

                                "

                                class="

                                    hero-data-exhaustion-level

                                    flex

                                    h-11

                                    items-center

                                    justify-center

                                    rounded-lg

                                    border

                                    font-serif

                                    text-[16px]

                                    font-black

                                    transition

                                "

                                :class="{
                                    'is-active':
                                        exhaustionDraft === level,
                                    'is-lethal':
                                        exhaustionDraft === level
                                        && level >= 6
                                }"

                                x-text="

                                    level

                                "

                            ></button>

                        </template>

                    </div>

                </div>





                <div

                    class="

                        hero-data-exhaustion-stat-grid

                        grid

                        grid-cols-3

                        gap-2

                    "

                >

                    <div

                        class="

                            hero-data-exhaustion-stat

                            hero-data-exhaustion-stat

                            hero-data-exhaustion-stat

                            rounded-xl

                            border

                            border-[#d8c7ab]/70

                            bg-[#fffdf8]

                            px-3

                            py-3

                            text-center

                        "

                    >

                        <p

                            class="

                                hero-data-exhaustion-label

                                hero-data-exhaustion-label

                                hero-data-exhaustion-label

                                text-[9px]

                                font-black

                                uppercase

                                tracking-[0.10em]

                                text-[#8c6239]

                            "

                        >

                            Rolagens

                        </p>

                        <p

                            class="

                                hero-data-exhaustion-stat-value

                                hero-data-exhaustion-stat-value

                                mt-1

                                font-serif

                                text-[20px]

                                font-black

                                text-[#6b1d14]

                            "

                            x-text="

                                exhaustionDraft > 0

                                    ? '-' + (exhaustionDraft * 2)

                                    : '0'

                            "

                        ></p>

                    </div>

                    <div

                        class="

                            rounded-xl

                            border

                            border-[#d8c7ab]/70

                            bg-[#fffdf8]

                            px-3

                            py-3

                            text-center

                        "

                    >

                        <p

                            class="

                                text-[9px]

                                font-black

                                uppercase

                                tracking-[0.10em]

                                text-[#8c6239]

                            "

                        >

                            Deslocamento

                        </p>

                        <p

                            class="

                                mt-1

                                font-serif

                                text-[20px]

                                font-black

                                text-[#6b1d14]

                            "

                        >

                            <span

                                x-text="

                                    exhaustionDraft > 0

                                        ? '-' + (exhaustionDraft * 5)

                                        : '0'

                                "

                            ></span>

                            <span

                                class="

                                    text-[10px]

                                    text-[#8c6239]

                                "

                            >

                                ft

                            </span>

                        </p>

                    </div>

                    <div

                        class="

                            rounded-xl

                            border

                            border-[#d8c7ab]/70

                            bg-[#fffdf8]

                            px-3

                            py-3

                            text-center

                        "

                        :class="{
                            'is-lethal':
                                exhaustionDraft >= 6
                        }"

                    >

                        <p

                            class="

                                text-[9px]

                                font-black

                                uppercase

                                tracking-[0.10em]

                                text-[#8c6239]

                            "

                        >

                            Estado

                        </p>

                        <p

                            class="

                                hero-data-exhaustion-stat-value

                                mt-1

                                font-serif

                                text-[15px]

                                font-black

                                text-[#53150f]

                            "

                            

                            x-text="

                                exhaustionDraft >= 6

                                    ? 'Morte'

                                    : (

                                        exhaustionDraft > 0

                                            ? 'Exausto'

                                            : 'Normal'

                                    )

                            "

                        ></p>

                    </div>

                </div>





                <div

                    class="

                        hero-data-exhaustion-note

                        rounded-xl

                        border

                        border-[#d8c7ab]/60

                        bg-[#f4f1e8]/70

                        px-4

                        py-3

                    "

                >

                    <p

                        class="

                            text-[11px]

                            leading-relaxed

                            text-[#76553f]

                        "

                    >

                        Os efeitos são cumulativos:

                        cada nível aplica

                        <strong class="text-[#53150f]">−2 em todas as rolagens</strong>

                        e

                        <strong class="text-[#53150f]">−5 ft de deslocamento</strong>.

                        Ao atingir

                        <strong class="text-red-800">6 níveis</strong>,

                        o personagem morre.

                    </p>

                </div>





                <div

                    x-show="

                        exhaustionError

                    "

                    x-cloak

                    class="

                        rounded-lg

                        border

                        border-red-200

                        bg-red-50

                        px-3

                        py-2.5

                        text-[11px]

                        font-bold

                        text-red-700

                    "

                    x-text="

                        exhaustionError

                    "

                ></div>

            </div>

            {{-- FOOTER --}}

            <div

                class="

                    hero-data-exhaustion-modal-footer

                    flex

                    items-center

                    justify-end

                    gap-2

                    border-t

                    border-[#d8c7ab]/60

                    bg-[#f4f1e8]/70

                    px-5

                    py-3

                "

            >

                <button

                    type="button"

                    @click="

                        cancelExhaustionEditor()

                    "

                    :disabled="

                        savingExhaustion

                    "

                    class="

                        hero-data-exhaustion-secondary

                        rounded-lg

                        px-4

                        py-2.5

                        text-[10px]

                        font-black

                        uppercase

                        tracking-wider

                        text-[#8c6239]

                        transition

                        hover:bg-[#eadbc8]/55

                        hover:text-[#53150f]

                        disabled:opacity-50

                    "

                >

                    Cancelar

                </button>

                <button

                    type="button"

                    @click="

                        saveExhaustion()

                    "

                    :disabled="

                        savingExhaustion

                    "

                    class="

                        hero-data-exhaustion-primary

                        min-w-[132px]

                        rounded-lg

                        bg-[#6b1d14]

                        px-5

                        py-2.5

                        text-[10px]

                        font-black

                        uppercase

                        tracking-wider

                        text-[#fffaf2]

                        transition

                        hover:bg-[#53150f]

                        disabled:cursor-wait

                        disabled:opacity-60

                    "

                >

                    <span

                        x-show="

                            !savingExhaustion

                        "

                    >

                        Salvar

                    </span>

                    <span

                        x-show="

                            savingExhaustion

                        "

                        x-cloak

                    >

                        Salvando...

                    </span>

                </button>

            </div>

        </div>

    </div>

</template>

@endif





{{-- ================================================================

     CONFIGURAÇÕES DO PERSONAGEM

================================================================= --}}

@include(

    'characters.components.character-customization-modal',

    [

        'character' =>

            $character,

        'classes' =>

            $customizationClasses

            ?? $classes,

    ]

)