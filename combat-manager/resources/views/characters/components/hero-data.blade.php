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
    | EXPERIÊNCIA DA CAMPANHA
    |--------------------------------------------------------------------------
    |
    | XP pertence ao Character original dentro da campanha.
    | Formas/transformações podem trocar a ficha mecânica, mas não criam
    | progressão paralela de experiência.
    |
    */

    $xpEnabled = (bool) (
        $character->campaign_experience_enabled
        ?? false
    );

    $experience = $xpEnabled
        ? max(
            0,
            (int) (
                $character->campaign_experience_points
                ?? 0
            )
        )
        : null;

    $experienceCampaignName = $xpEnabled
        ? (
            $character->campaign_experience_name
            ?? null
        )
        : null;

    $experienceLevel =
        max(
            1,
            min(
                \App\Support\CharacterExperience::MAX_LEVEL,
                (int) ($character->level ?? 1)
            )
        );

    $experienceProgress =
        $xpEnabled
            ? (
                is_array(
                    $character->campaign_experience_progress
                    ?? null
                )
                    ? $character->campaign_experience_progress
                    : \App\Support\CharacterExperience::progress(
                        $experienceLevel,
                        (int) ($experience ?? 0)
                    )
            )
            : null;

    // Circunferência real do anel SVG; sempre proporcional ao XP do nível.
    $xpRingPercent = max(0.0, min(100.0, (float) ($experienceProgress['percent'] ?? 0)));
    $xpRingCircumference = 2 * pi() * 20;
    $xpRingOffset = $xpRingCircumference * (1 - ($xpRingPercent / 100));
    $xpRingAngle = deg2rad($xpRingPercent * 3.6);
    $xpRingHeadX = 24 + 20 * sin($xpRingAngle);
    $xpRingHeadY = 24 - 20 * cos($xpRingAngle);
    $xpRingGradientId = 'hero-xp-ring-' . (int) $character->getKey() . '-' . (int) $mechanicalCharacter->getKey();

    $experienceCanLevelUp = $xpEnabled
        && !($experienceProgress['max_level'] ?? true)
        && (bool) ($experienceProgress['ready_for_next_level'] ?? false);

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



            /*
            |--------------------------------------------------------------------------
            | EXPERIÊNCIA — PROGRESSÃO D&D 1–20
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme] {
                --hero-xp-gold: #c69a47;
                --hero-xp-gold-strong: #8a611d;

                /*
                 * O dourado da experiência continua presente, mas se adapta ao
                 * accent do tema para não parecer um elemento estranho.
                 */
                --hero-xp-ring:
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold) 72%,
                        var(--theme-accent-strong) 28%
                    );

                --hero-xp-ring-strong:
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold-strong) 62%,
                        var(--theme-accent-strong) 38%
                    );

                --hero-xp-ring-bright:
                    color-mix(
                        in srgb,
                        #f7e0a4 54%,
                        var(--hero-xp-ring) 46%
                    );

                --hero-xp-gold-soft:
                    color-mix(
                        in srgb,
                        var(--hero-xp-ring) 32%,
                        transparent
                    );

                --hero-xp-track:
                    color-mix(
                        in srgb,
                        var(--hero-xp-ring) 24%,
                        var(--theme-border-strong) 76%
                    );

                --hero-xp-track-inner:
                    color-mix(
                        in srgb,
                        var(--theme-surface-raised) 72%,
                        var(--hero-xp-ring) 28%
                    );
            }

            .character-sheet-theme:is(
                [data-theme="midnight"],
                [data-theme="obsidian"]
            ) {
                --hero-xp-gold: #e0bc70;
                --hero-xp-gold-strong: #f0d28f;

                --hero-xp-ring:
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold) 82%,
                        var(--theme-accent-strong) 18%
                    );

                --hero-xp-ring-strong:
                    var(--hero-xp-gold-strong);

                --hero-xp-ring-bright:
                    color-mix(
                        in srgb,
                        #ffe8ad 68%,
                        var(--hero-xp-ring) 32%
                    );

                --hero-xp-track:
                    color-mix(
                        in srgb,
                        var(--hero-xp-ring) 30%,
                        var(--theme-border-strong) 70%
                    );
            }

            .character-sheet-theme[data-theme]
            .hero-data-level-badge--xp {
                border-color:
                    color-mix(
                        in srgb,
                        var(--hero-xp-ring) 66%,
                        var(--theme-border) 34%
                    ) !important;

                background:
                    linear-gradient(
                        180deg,
                        color-mix(in srgb, var(--theme-surface-raised) 92%, #ffffff 8%),
                        var(--theme-surface-raised)
                    ) !important;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    0 0 0 2px
                    color-mix(
                        in srgb,
                        var(--theme-surface-raised) 94%,
                        transparent
                    ),
                    0 0 0 4px
                    color-mix(
                        in srgb,
                        var(--hero-xp-ring) 28%,
                        transparent
                    ),
                    0 4px 14px
                    color-mix(
                        in srgb,
                        var(--hero-xp-ring) 18%,
                        transparent
                    ) !important;

                cursor:
                    pointer;
            }

            .character-sheet-theme[data-theme]
            .hero-data-level-badge--xp:hover {
                border-color:
                    var(--hero-xp-ring-bright) !important;

                background:
                    var(--theme-card-hover) !important;

                transform:
                    translateY(-1px);
            }

            .character-sheet-theme[data-theme]
            .hero-data-level-ring {
                position: absolute;
                inset: -4px;
                border-radius: 999px;
                background:
                    radial-gradient(
                        circle at center,
                        transparent calc(100% - 6px),
                        var(--hero-xp-track-inner) calc(100% - 6px),
                        var(--hero-xp-track-inner) calc(100% - 4px),
                        transparent calc(100% - 4px)
                    ),
                    conic-gradient(
                        from -90deg,
                        var(--hero-xp-ring-bright) 0deg,
                        var(--hero-xp-ring) calc(var(--hero-xp-progress-angle, 0deg) * .72),
                        var(--hero-xp-ring-strong) var(--hero-xp-progress-angle, 0deg),
                        var(--hero-xp-track) var(--hero-xp-progress-angle, 0deg),
                        var(--hero-xp-track) 360deg
                    );

                -webkit-mask:
                    radial-gradient(
                        farthest-side,
                        transparent calc(100% - 5px),
                        #000 calc(100% - 4px)
                    );

                mask:
                    radial-gradient(
                        farthest-side,
                        transparent calc(100% - 5px),
                        #000 calc(100% - 4px)
                    );

                pointer-events: none;
                filter:
                    drop-shadow(0 0 4px var(--hero-xp-gold-soft));
            }

            .character-sheet-theme[data-theme]
            .hero-data-level-progress-head {
                position: absolute;
                inset: -4px;
                border-radius: 999px;
                pointer-events: none;
                transform: rotate(var(--hero-xp-progress-angle, 0deg));
            }

            .character-sheet-theme[data-theme]
            .hero-data-level-progress-head::after {
                content: "";
                position: absolute;
                left: 50%;
                top: 1px;
                width: 6px;
                height: 6px;
                border-radius: 999px;
                transform: translateX(-50%);
                background:
                    radial-gradient(circle at 35% 35%, #fff9eb 0%, var(--hero-xp-ring-bright) 36%, var(--hero-xp-ring-strong) 100%);
                box-shadow:
                    0 0 0 2px var(--theme-surface-raised),
                    0 0 9px color-mix(in srgb, var(--hero-xp-ring) 48%, transparent);
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-progress {
                overflow: hidden;
                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold) 26%,
                        var(--theme-border) 74%
                    );
                background:
                    var(--theme-surface-inset);
                box-shadow:
                    inset 0 1px 2px rgba(0,0,0,.08);
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-progress-fill {
                height: 100%;
                border-radius: inherit;
                background:
                    linear-gradient(
                        90deg,
                        color-mix(
                            in srgb,
                            var(--hero-xp-ring-strong) 86%,
                            #6a4616 14%
                        ),
                        var(--hero-xp-ring),
                        color-mix(
                            in srgb,
                            var(--hero-xp-gold) 76%,
                            #f8df9c 24%
                        )
                    );
                box-shadow:
                    0 0 7px var(--hero-xp-gold-soft);
            }

            /*
             * O modal de XP é um portal global. Ele não pode participar do
             * stacking context da ficha nem ficar escondido sob drawers.
             */
            .character-sheet-theme[data-theme].hero-data-xp-modal-layer {
                position: fixed !important;
                inset: 0 !important;
                z-index: 9999 !important;
                isolation: isolate;
            }

            .character-sheet-theme[data-theme].hero-data-xp-modal-layer
            .character-theme-modal-backdrop {
                position: absolute !important;
                inset: 0 !important;
                z-index: 0 !important;

                background:
                    var(
                        --theme-modal-backdrop,
                        rgba(18, 14, 12, .64)
                    ) !important;

                -webkit-backdrop-filter:
                    blur(4px);

                backdrop-filter:
                    blur(4px);
            }

            .character-sheet-theme[data-theme].hero-data-xp-modal-layer
            .hero-data-xp-modal {
                position: relative !important;
                z-index: 2 !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-modal {
                width: min(520px, calc(100vw - 28px));
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

            .character-sheet-theme[data-theme]
            .hero-data-xp-modal-header {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-modal {
                border-top:
                    3px solid
                    var(--hero-xp-ring) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-modal-kicker,
            .character-sheet-theme[data-theme]
            .hero-data-xp-modal-label {
                color:
                    var(--theme-modal-label, var(--theme-label)) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-modal-title,
            .character-sheet-theme[data-theme]
            .hero-data-xp-modal-value {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-modal-copy {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-level-seal {
                border-color:
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold) 40%,
                        var(--theme-border) 60%
                    ) !important;
                background:
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold) 10%,
                        var(--theme-surface-raised) 90%
                    ) !important;
                box-shadow:
                    inset 0 0 0 2px var(--theme-surface-raised),
                    inset 0 0 0 3px color-mix(in srgb, var(--hero-xp-gold) 18%, transparent),
                    0 3px 9px color-mix(in srgb, var(--hero-xp-gold) 12%, transparent);
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-stat {
                border-color:
                    var(--theme-border) !important;
                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-highlight {
                border-color:
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold) 34%,
                        var(--theme-border) 66%
                    ) !important;
                background:
                    color-mix(
                        in srgb,
                        var(--hero-xp-gold) 9%,
                        var(--theme-surface-inset) 91%
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-gold {
                color:
                    var(--hero-xp-gold-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-close {
                border:
                    1px solid var(--theme-border);
                background:
                    var(--theme-surface-raised) !important;
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .hero-data-xp-close:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            @media (max-width: 520px) {
                .character-sheet-theme[data-theme]
                .hero-data-xp-modal {
                    width: calc(100vw - 20px);
                    border-radius: 15px;
                }
            }


            .character-sheet-theme[data-theme] .hero-data-xp-level-action {
                border: 1px solid color-mix(in srgb, var(--hero-xp-ring) 73%, var(--theme-border));
                background: color-mix(in srgb, var(--hero-xp-ring) 76%, var(--theme-accent-strong));
                color: var(--theme-button-text);
            }
            .character-sheet-theme[data-theme] .hero-data-xp-level-action:hover {
                filter: brightness(1.09);
            }
            /* XP: progresso apenas na roda; prontidão destacada no retrato. */
            .character-sheet-theme[data-theme] .hero-data-level-caption {
                color: var(--theme-label) !important;
            }
            .character-sheet-theme[data-theme] .hero-data-level-number {
                color: var(--theme-text-strong) !important;
            }
            @keyframes spellbound-xp-level-ready {
                0%, 100% {
                    filter: drop-shadow(0 0 5px color-mix(in srgb, var(--hero-xp-ring) 34%, transparent));
                }
                50% {
                    filter: drop-shadow(0 0 14px color-mix(in srgb, var(--hero-xp-ring-bright) 86%, transparent));
                }
            }
            .character-sheet-theme[data-theme] .hero-data-portrait--level-ready {
                border-color: var(--hero-xp-ring) !important;
                box-shadow:
                    0 0 0 2px color-mix(in srgb, var(--hero-xp-ring) 58%, transparent),
                    0 0 20px color-mix(in srgb, var(--hero-xp-ring) 34%, transparent) !important;
                animation: spellbound-xp-level-ready 2.2s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme] .hero-data-level-badge--ready {
                border-color: var(--hero-xp-ring-bright) !important;
                box-shadow:
                    0 0 0 2px var(--theme-surface-raised),
                    0 0 0 5px color-mix(in srgb, var(--hero-xp-ring) 62%, transparent),
                    0 0 24px color-mix(in srgb, var(--hero-xp-ring-bright) 44%, transparent) !important;
            }
            .character-sheet-theme[data-theme] .hero-data-level-ready-cta {
                border-color: color-mix(in srgb, var(--hero-xp-ring) 68%, var(--theme-border));
                background: color-mix(in srgb, var(--hero-xp-ring) 17%, var(--theme-surface-card));
                color: var(--theme-text-strong);
                box-shadow: 0 0 12px color-mix(in srgb, var(--hero-xp-ring) 23%, transparent);
            }
            .character-sheet-theme[data-theme] .hero-data-level-ready-cta:hover {
                background: color-mix(in srgb, var(--hero-xp-ring) 28%, var(--theme-surface-card));
            }
            @media (prefers-reduced-motion: reduce) {
                .character-sheet-theme[data-theme] .hero-data-portrait--level-ready { animation: none !important; }
            }

            /* Anel do XP: duas linhas nítidas e arco SVG preciso em todos os temas. */
            .character-sheet-theme[data-theme] .hero-data-xp-ring-svg {
                position: absolute;
                inset: -6px;
                width: calc(100% + 12px);
                height: calc(100% + 12px);
                overflow: visible;
                pointer-events: none;
                filter: drop-shadow(0 0 2.3px color-mix(in srgb, var(--hero-xp-ring) 32%, transparent));
            }
            .character-sheet-theme[data-theme] .hero-data-xp-ring-track {
                fill: none;
                stroke: var(--hero-xp-track);
                stroke-width: 4.2;
                opacity: .98;
            }
            .character-sheet-theme[data-theme] .hero-data-xp-ring-fill {
                fill: none;
                stroke-width: 4.2;
                stroke-linecap: round;
                transform: rotate(-90deg);
                transform-origin: 24px 24px;
                transition: stroke-dashoffset .55s ease;
                filter: drop-shadow(0 0 1.4px color-mix(in srgb, var(--hero-xp-ring) 64%, transparent));
            }
            .character-sheet-theme[data-theme] .hero-data-xp-ring-head {
                fill: var(--hero-xp-ring-bright);
                stroke: var(--theme-surface-raised);
                stroke-width: 1.25;
                filter: drop-shadow(0 0 2.4px var(--hero-xp-ring));
            }
            .character-sheet-theme[data-theme] .hero-data-level-badge--xp {
                border-color: color-mix(in srgb, var(--hero-xp-ring) 46%, var(--theme-border) 54%) !important;
                box-shadow: 0 0 0 1.5px var(--theme-surface-raised),
                            0 0 0 2.7px color-mix(in srgb, var(--hero-xp-ring) 28%, transparent),
                            0 3px 11px color-mix(in srgb, var(--hero-xp-ring) 22%, transparent) !important;
            }
            .character-sheet-theme[data-theme] .hero-data-level-badge--ready {
                box-shadow: 0 0 0 2px var(--theme-surface-raised),
                            0 0 0 4px color-mix(in srgb, var(--hero-xp-ring) 72%, transparent),
                            0 0 18px color-mix(in srgb, var(--hero-xp-ring-bright) 50%, transparent) !important;
            }
            @media (prefers-reduced-motion: reduce) {
                .character-sheet-theme[data-theme] .hero-data-xp-ring-fill { transition: none; }
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

                                tab: 'identity'

                            }

                        }

                    )

                )

            "

            class="

                hero-data-portrait
                @if ($experienceCanLevelUp) hero-data-portrait--level-ready @endif

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

                            object-contain

                            object-center

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

             NÍVEL + PROGRESSO DE EXPERIÊNCIA

        ========================================================= --}}

        @if ($xpEnabled)

            <button
                type="button"

                @click.stop="
                    xpProgressOpen =
                        true
                "

                class="
                    hero-data-level-badge
                    hero-data-level-badge--xp
                    @if ($experienceCanLevelUp) hero-data-level-badge--ready @endif

                    absolute
                    right-2
                    top-2
                    z-30

                    flex
                    h-[36px]
                    w-[36px]
                    items-center
                    justify-center

                    rounded-full
                    border

                    shadow-[0_2px_6px_rgba(83,21,15,.06)]

                    transition
                    duration-150

                    focus:outline-none
                    focus-visible:ring-2
                    focus-visible:ring-[#b88a3b]/35
                "



                title="Abrir progresso de experiência"
                aria-label="Abrir progresso de experiência"
            >
                <svg
                    class="hero-data-xp-ring-svg"
                    viewBox="0 0 48 48"
                    aria-hidden="true"
                    focusable="false"
                >
                    <defs>
                        <linearGradient id="{{ $xpRingGradientId }}" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="var(--hero-xp-ring-bright)" />
                            <stop offset="62%" stop-color="var(--hero-xp-ring)" />
                            <stop offset="100%" stop-color="var(--hero-xp-ring-strong)" />
                        </linearGradient>
                    </defs>
                    <circle class="hero-data-xp-ring-track" cx="24" cy="24" r="20" />
                    <circle
                        class="hero-data-xp-ring-fill"
                        cx="24" cy="24" r="20"
                        stroke="url(#{{ $xpRingGradientId }})"
                        stroke-dasharray="{{ number_format($xpRingCircumference, 3, '.', '') }}"
                        stroke-dashoffset="{{ number_format($xpRingOffset, 3, '.', '') }}"
                    />
                    @if ($xpRingPercent > 0.1)
                        <circle
                            class="hero-data-xp-ring-head"
                            cx="{{ number_format($xpRingHeadX, 3, '.', '') }}"
                            cy="{{ number_format($xpRingHeadY, 3, '.', '') }}"
                            r="3.1"
                        />
                    @endif
                </svg>

                <span
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
                            hero-data-level-caption
                            text-[5.5px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.10em]
                        "
                    >
                        Nv.
                    </span>

                    <span
                        class="
                            hero-data-level-number
                            mt-[0px]
                            font-serif
                            text-[15px]
                            font-black
                            leading-none
                        "
                    >
                        {{ $mechanicalCharacter->level }}
                    </span>
                </span>
            </button>

        @else

            <div
                class="
                    hero-data-level-badge

                    absolute
                    right-2
                    top-2
                    z-30

                    flex
                    h-[36px]
                    w-[36px]
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
                            hero-data-level-caption
                            text-[5.5px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.10em]
                        "
                    >
                        Nv.
                    </span>

                    <span
                        class="
                            hero-data-level-number
                            mt-[0px]
                            font-serif
                            text-[15px]
                            font-black
                            leading-none
                        "
                    >
                        {{ $mechanicalCharacter->level }}
                    </span>
                </div>
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

        @if ($experienceCanLevelUp)
            <button
                type="button"
                @click.stop="xpProgressOpen = true"
                class="hero-data-level-ready-cta mt-1.5 w-full rounded-full border px-2 py-1 text-center text-[8px] font-black uppercase tracking-[0.09em] transition"
                title="Você pode subir de nível"
            >
                ✦ Subir de nível
            </button>
        @endif



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

@if ($xpEnabled)
{{-- EXPERIÊNCIA — MODAL SIMPLIFICADO --}}
<template x-teleport="body">
    <div
        x-show="xpProgressOpen"
        x-cloak
        @keydown.escape.window="xpProgressOpen = false"
        data-theme="{{ $characterTheme }}"
        class="character-sheet-theme character-theme-modal-layer hero-data-xp-modal-layer fixed inset-0 flex items-center justify-center p-4"
        style="position:fixed !important;inset:0 !important;z-index:9999 !important;"
        role="dialog" aria-modal="true" aria-label="Experiência do personagem"
    >
        <div class="character-theme-modal-backdrop absolute inset-0" @click="xpProgressOpen = false"></div>
        <section
            x-show="xpProgressOpen"
            x-transition:enter="character-theme-modal-panel-enter"
            x-transition:enter-start="character-theme-modal-panel-enter-start"
            x-transition:enter-end="character-theme-modal-panel-enter-end"
            x-transition:leave="character-theme-modal-panel-leave"
            x-transition:leave-start="character-theme-modal-panel-leave-start"
            x-transition:leave-end="character-theme-modal-panel-leave-end"
            @click.stop
            class="character-theme-modal hero-data-xp-modal relative z-10 overflow-hidden rounded-2xl border"
        >
            <header class="hero-data-xp-modal-header flex items-center justify-between gap-3 border-b px-5 py-4">
                <div>
                    <span class="hero-data-xp-modal-label block text-[9px] font-black uppercase tracking-[0.13em]">Experiência</span>
                    <h3 class="hero-data-xp-modal-title mt-1 font-serif text-[21px] font-black">{{ $character->name }}</h3>
                </div>
                <button type="button" class="hero-data-xp-close h-9 w-9 rounded-lg text-xl" @click="xpProgressOpen = false" aria-label="Fechar">×</button>
            </header>
            <div class="p-5">
                <div class="flex items-center gap-3">
                    <div class="hero-data-xp-level-seal flex h-[54px] w-[54px] shrink-0 flex-col items-center justify-center rounded-full border">
                        <span class="hero-data-xp-modal-label text-[8px] font-black uppercase tracking-[0.09em]">Nv.</span>
                        <strong class="hero-data-xp-modal-value font-serif text-[24px] leading-none">{{ $experienceLevel }}</strong>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="hero-data-xp-modal-label text-[9px] font-black uppercase tracking-wider">XP atual</p>
                        <p class="hero-data-xp-modal-value mt-1 font-serif text-[25px] font-black leading-tight">
                            {{ number_format((int) $experience, 0, ',', '.') }} <span class="text-[11px]">XP</span>
                        </p>
                        @if (!($experienceProgress['max_level'] ?? false))
                            <p class="hero-data-xp-modal-copy mt-1.5 text-[11px]">
                                Próximo nível: <strong class="hero-data-xp-gold">{{ number_format((int) $experienceProgress['next_threshold'], 0, ',', '.') }} XP</strong>
                            </p>
                        @else
                            <p class="hero-data-xp-gold mt-1.5 text-[11px] font-black">Nível máximo alcançado</p>
                        @endif
                    </div>
                </div>
                @if (!($experienceProgress['max_level'] ?? false))
                    <div class="mt-5 flex items-center justify-between gap-3">
                        <p class="hero-data-xp-modal-copy text-[10px] font-semibold">Progresso para o nível {{ $experienceLevel + 1 }}</p>
                        <strong class="hero-data-xp-gold text-[12px]">{{ number_format((float) $experienceProgress['percent'], 1, ',', '.') }}%</strong>
                    </div>
                    <div class="hero-data-xp-progress mt-2 h-2 w-full rounded-full" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) round((float) $experienceProgress['percent']) }}">
                        <div class="hero-data-xp-progress-fill" style="width:{{ number_format((float) $experienceProgress['percent'], 2, '.', '') }}%"></div>
                    </div>
                    @if ($experienceCanLevelUp)
                        <div class="hero-data-xp-highlight mt-4 rounded-xl border px-4 py-3">
                            <p class="hero-data-xp-gold text-[11px] font-black">✦ Você pode subir para o nível {{ $experienceLevel + 1 }}!</p>
                            <p class="hero-data-xp-modal-copy mt-1 text-[10px]">Escolha uma classe para receber seu novo nível.</p>
                        </div>
                        <button
                            type="button"
                            @click="xpProgressOpen = false; window.dispatchEvent(new CustomEvent('open-character-customization', {detail: {tab:'progression'}}))"
                            class="hero-data-xp-level-action mt-3 w-full rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-wide transition"
                        >
                            ↑ Subir de nível
                        </button>
                    @else
                        <p class="hero-data-xp-modal-copy mt-4 text-center text-[11px]">
                            Faltam <strong class="hero-data-xp-gold">{{ number_format((int) $experienceProgress['remaining'], 0, ',', '.') }} XP</strong> para subir de nível.
                        </p>
                    @endif
                @else
                    <p class="hero-data-xp-modal-copy mt-5 text-center text-[11px]">A progressão de personagem termina no nível 20.</p>
                @endif
            </div>
        </section>
    </div>
</template>
@endif


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