@props(['character'])

@once
    @push('styles')
        <style>
            @keyframes partyWoundedPulseV3 {
                0%, 100% {
                    box-shadow:
                        0 0 0 1px rgba(159, 48, 37, .18),
                        0 0 0 0 rgba(159, 48, 37, 0);
                }

                50% {
                    box-shadow:
                        0 0 0 2px rgba(159, 48, 37, .36),
                        0 0 20px rgba(159, 48, 37, .14);
                }
            }

            @keyframes partyCriticalPulseV3 {
                0%, 100% {
                    box-shadow:
                        0 0 0 1px rgba(127, 38, 29, .24),
                        0 0 0 0 rgba(185, 28, 28, 0);
                }

                50% {
                    box-shadow:
                        0 0 0 3px rgba(185, 28, 28, .54),
                        0 0 26px rgba(185, 28, 28, .26);
                }
            }

            .party-v3-portrait--wounded {
                animation: partyWoundedPulseV3 1.75s ease-in-out infinite;
            }

            .party-v3-portrait--critical,
            .party-v3-portrait--down {
                animation: partyCriticalPulseV3 1.05s ease-in-out infinite;
            }

            .party-v3-portrait--down img {
                filter: grayscale(.82) saturate(.35) brightness(.86);
            }

            @media (prefers-reduced-motion: reduce) {
                .party-v3-portrait--wounded,
                .party-v3-portrait--critical,
                .party-v3-portrait--down {
                    animation: none !important;
                }
            }


            /* ============================================================
             * PARTY — ALIADOS / RETRATOS REFINADOS
             * Remove a caixa grande e concentra o acabamento nas molduras.
             * ========================================================== */

            .party-social-strip {
                position: relative;
                width: min(100%, 560px);
                margin-inline: auto;
                padding-top: 2px;
            }

            .party-social-header {
                position: relative;
                z-index: 2;
                display: flex;
                align-items: baseline;
                justify-content: center;
                gap: 8px;
            }

            .party-social-header__title {
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 18px;
                font-weight: 900;
                line-height: 1;
                color: #53150f;
            }

            .party-social-header__divider {
                width: 4px;
                height: 4px;
                border-radius: 9999px;
                background: #a0523d;
                box-shadow: 0 0 0 3px rgba(160, 82, 61, .08);
            }

            .party-social-header__campaign {
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 18px;
                font-weight: 900;
                line-height: 1;
                letter-spacing: .01em;
                text-transform: none;
                color: #8c6239;
            }

            .party-social-rule {
                position: relative;
                z-index: 2;
                width: 48px;
                height: 1px;
                margin: 10px auto 0;
                background: linear-gradient(90deg, transparent, rgba(127, 38, 29, .55), transparent);
            }

            .party-social-roster {
                position: relative;
                z-index: 2;
                margin-top: 14px;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: flex-start;
                gap: 18px;
            }

            .party-social-ally {
                position: relative;
                width: 150px;
                text-align: center;
            }

            .party-social-ally__portrait-frame {
                position: relative;
                width: 138px;
                margin-inline: auto;
                padding: 4px;
                border-radius: 16px;
                background:
                    linear-gradient(180deg, #e6d2b2 0%, #c9a77a 100%);
                border: 1px solid rgba(140, 98, 57, .58);
                box-shadow:
                    0 10px 22px rgba(83, 21, 15, .08),
                    inset 0 1px 0 rgba(255,250,242,.68);
                transition:
                    transform .18s ease,
                    box-shadow .18s ease,
                    border-color .18s ease;
            }

            .party-social-ally__portrait-frame::before {
                content: "";
                position: absolute;
                inset: 3px;
                border: 1px solid rgba(255,250,242,.46);
                border-radius: 12px;
                pointer-events: none;
            }

            .party-social-ally__portrait-frame::after {
                content: "";
                position: absolute;
                left: 50%;
                top: -2px;
                width: 42px;
                height: 2px;
                transform: translateX(-50%);
                border-radius: 9999px;
                background: linear-gradient(90deg, transparent, rgba(127,38,29,.76), transparent);
                pointer-events: none;
            }

            .party-social-ally:hover .party-social-ally__portrait-frame,
            .party-social-ally:focus-within .party-social-ally__portrait-frame {
                transform: translateY(-3px);
                border-color: rgba(127, 38, 29, .52);
                box-shadow:
                    0 16px 30px rgba(83, 21, 15, .13),
                    inset 0 1px 0 rgba(255,250,242,.72);
            }

            .party-social-ally__portrait {
                position: relative;
                width: 130px;
                height: 162px;
                overflow: hidden;
                isolation: isolate;
                border-radius: 11px;
                background:
                    linear-gradient(
                        0deg,
                        rgba(83, 21, 15, .30) 0%,
                        rgba(83, 21, 15, .14) 18%,
                        rgba(83, 21, 15, .04) 34%,
                        transparent 50%
                    ),
                    linear-gradient(
                        180deg,
                        #f8f3eb 0%,
                        #eadbc8 100%
                    );
                box-shadow:
                    inset 0 0 0 1px rgba(205, 187, 159, .45),
                    inset 0 1px 0 rgba(255,255,255,.72);
            }

            /*
             * Apenas a arte do personagem e o fallback ficam sobre o fundo.
             * Nome, status e ações preservam seus próprios z-index altos.
             */
            .party-social-ally__portrait > img,
            .party-social-ally__portrait > .party-social-ally__fallback {
                position: relative;
                z-index: 1;
            }

            .party-social-ally__portrait::before {
                content: "";
                position: absolute;
                inset: 0;
                z-index: 2;
                pointer-events: none;
                background:
                    linear-gradient(
                        180deg,
                        rgba(255,255,255,.055),
                        transparent 22%
                    );
            }

            .party-social-ally__portrait::after {
                content: "";
                position: absolute;
                left: 14px;
                right: 14px;
                bottom: 0;
                z-index: 3;
                height: 2px;
                border-radius: 9999px 9999px 0 0;
                background: linear-gradient(90deg, transparent, #c89c62, transparent);
                opacity: .88;
                pointer-events: none;
            }

            .party-social-ally__identity {
                position: absolute;
                left: 10px;
                right: 10px;
                bottom: 12px;
                z-index: 10;
                text-align: left;
                pointer-events: none;
            }

            .party-social-ally__name {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 10px;
                font-weight: 900;
                line-height: 1.08;
                color: #fffaf2;
                text-shadow: 0 2px 6px rgba(42,23,18,.58);
            }

            .party-social-ally__health {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 11;
                height: 6px;
                overflow: hidden;
                background: rgba(32, 18, 15, .22);
            }

            .party-social-ally__health-fill {
                height: 100%;
                border-radius: 0 9999px 9999px 0;
                background: linear-gradient(90deg, #7f261d 0%, #a0523d 100%);
                transition: width .35s ease, background .2s ease;
            }

            .party-social-ally__health-fill--healthy {
                background: linear-gradient(90deg, #8c6239 0%, #c89c62 100%);
            }

            .party-social-ally__health-fill--wounded {
                background: linear-gradient(90deg, #8f3a2d 0%, #b5654f 100%);
            }

            .party-social-ally__health-fill--critical,
            .party-social-ally__health-fill--down {
                background: linear-gradient(90deg, #6b1d14 0%, #9f3025 100%);
            }

            .party-social-ally__exhaustion {
                position: absolute;
                left: 9px;
                top: 9px;
                z-index: 12;
                display: inline-flex;
                min-height: 22px;
                align-items: center;
                border: 1px solid rgba(200,156,98,.52);
                border-radius: 9999px;
                background: rgba(83, 21, 15, .88);
                padding: 0 7px;
                font-size: 7px;
                font-weight: 900;
                color: #f6e4a8;
                box-shadow: 0 4px 12px rgba(42,23,18,.16);
                backdrop-filter: blur(5px);
            }

            .party-social-menu {
                position: absolute;
                left: 50%;
                top: 34px;
                z-index: 40;
                width: 196px;
                transform: translateX(-50%);
                overflow: hidden;
                border: 1px solid rgba(140, 98, 57, .42);
                border-radius: 14px;
                background:
                    linear-gradient(180deg, rgba(255,253,248,.99), rgba(247,239,227,.99));
                box-shadow:
                    0 18px 38px rgba(83, 21, 15, .18),
                    inset 0 1px 0 rgba(255,255,255,.95);
                text-align: left;
                transform-origin: top center;
                will-change: transform, opacity;
            }

            .party-social-menu::before {
                content: "";
                position: absolute;
                left: 10px;
                right: 10px;
                top: 8px;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(160,82,61,.35), transparent);
                pointer-events: none;
            }

            .party-social-menu__header {
                padding: 14px 14px 10px;
                border-bottom: 1px solid rgba(205, 187, 159, .52);
                background: rgba(234, 219, 200, .32);
            }

            .party-social-menu__name {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.2;
                color: #53150f;
            }

            .party-social-menu__item-action {
                display: flex;
                width: 100%;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 10px 14px;
                border-bottom: 1px solid rgba(205, 187, 159, .42);
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .11em;
                text-transform: uppercase;
                color: #6b1d14;
                transition: background-color .15s ease, color .15s ease;
            }

            .party-social-menu__item-action:hover {
                background: rgba(234, 219, 200, .56);
                color: #53150f;
            }

            .party-social-menu__item-action::after {
                content: "→";
                font-size: 12px;
                font-weight: 700;
                color: #a0523d;
            }

            .party-social-menu__poke {
                padding: 10px 12px 12px;
            }

            .party-social-menu__label {
                margin-bottom: 8px;
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .16em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .party-social-menu__poke-grid {
                display: grid;
                grid-template-columns: repeat(6, minmax(0, 1fr));
                gap: 4px;
            }

            .party-social-menu__poke-button {
                display: flex;
                height: 28px;
                width: 28px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205, 187, 159, .52);
                border-radius: 8px;
                background: rgba(255,250,242,.82);
                font-size: 15px;
                transition:
                    transform .14s ease,
                    border-color .14s ease,
                    background-color .14s ease;
            }

            .party-social-menu__poke-button:hover {
                transform: translateY(-1px) scale(1.04);
                border-color: rgba(160,82,61,.45);
                background: #f3eadf;
            }

            @media (max-width: 640px) {
                .party-social-strip {
                    width: 100%;
                }

                .party-social-roster {
                    gap: 14px;
                }

                .party-social-ally {
                    width: 144px;
                }

                .party-social-ally__portrait-frame {
                    width: 132px;
                }

                .party-social-ally__portrait {
                    width: 124px;
                    height: 154px;
                }
            }

            /* ============================================================
             * DIÁRIO — CABEÇALHO E PAGINAÇÃO EDITORIAL
             * ========================================================== */

            .party-diary-toolbar {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 18px;
            }

            .party-diary-heading {
                min-width: 0;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 21px;
                font-weight: 700;
                line-height: 1.1;
                color: #53150f;
                letter-spacing: -.012em;
            }

            .party-diary-heading__name {
                font-weight: 900;
                color: inherit;
            }

            .party-diary-pages {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;
                gap: 5px;
            }

            .party-diary-page-button,
            .party-diary-add-button,
            .party-diary-delete-button {
                position: relative;
                display: inline-flex;
                height: 30px;
                min-width: 30px;
                align-items: center;
                justify-content: center;
                border: 0;
                background: transparent;
                padding: 0 7px;
                color: #8c6239;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .08em;
                transition:
                    color .15s ease,
                    background-color .15s ease;
            }

            .party-diary-page-button {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            }

            .party-diary-page-button::after {
                content: "";
                position: absolute;
                left: 7px;
                right: 7px;
                bottom: 2px;
                height: 2px;
                border-radius: 9999px;
                background: #7f261d;
                opacity: 0;
                transform: scaleX(.45);
                transition:
                    opacity .15s ease,
                    transform .15s ease;
            }

            .party-diary-page-button:hover,
            .party-diary-add-button:hover,
            .party-diary-delete-button:hover {
                color: #53150f;
            }

            .party-diary-page-button.is-active {
                color: #53150f;
            }

            .party-diary-page-button.is-active::after {
                opacity: 1;
                transform: scaleX(1);
            }

            .party-diary-add-button {
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 18px;
                font-weight: 400;
            }

            .party-diary-delete-button {
                min-width: 28px;
                color: #a4816c;
            }

            .party-diary-delete-button:hover {
                color: #9f3025;
                background: rgba(159,48,37,.055);
                border-radius: 9999px;
            }

            @media (max-width: 640px) {
                .party-social-ally {
                    width: 158px;
                }

                .party-social-ally__portrait-frame,
                .party-social-ally__portrait {
                    width: 138px;
                }

                .party-social-ally__portrait {
                    height: 168px;
                }

                .party-diary-toolbar {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 8px;
                }

                .party-diary-pages {
                    justify-content: flex-start;
                }
            }

            /* ============================================================
             * DIÁRIO — FOLHA SIMPLES
             * ========================================================== */

            .party-diary-paper {
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(205, 187, 159, .72);
                border-radius: 13px;
                background:
                    linear-gradient(
                        90deg,
                        rgba(127, 38, 29, .055) 0,
                        rgba(127, 38, 29, .055) 1px,
                        transparent 1px
                    ) 44px 0 / 100% 100% no-repeat,
                    #fffdf8;
                box-shadow:
                    0 12px 30px rgba(83, 21, 15, .055),
                    inset 0 1px 0 rgba(255, 255, 255, .95);
            }

            .party-diary-paper::after {
                content: "";
                position: absolute;
                inset: 0;
                pointer-events: none;
                background:
                    linear-gradient(
                        180deg,
                        rgba(255,255,255,.20),
                        transparent 22%
                    );
            }

            .party-diary-title {
                transition:
                    background-color .15s ease,
                    box-shadow .15s ease;
            }

            .party-diary-title:hover {
                background: rgba(234, 219, 200, .28);
            }

            .party-diary-title:focus {
                background: rgba(255, 250, 242, .78);
                box-shadow:
                    inset 0 -1px 0 rgba(140, 98, 57, .28);
            }

            .party-diary-writing {
                background-image:
                    repeating-linear-gradient(
                        to bottom,
                        transparent 0,
                        transparent 34px,
                        rgba(201, 173, 138, .27) 34px,
                        rgba(201, 173, 138, .27) 35px
                    );
                background-position: 0 3px;
            }

            /*
            |--------------------------------------------------------------------------
            | PARTY V4 — PADRÃO VISUAL DO SISTEMA
            |--------------------------------------------------------------------------
            |
            | Papel neutro + acento do tema. A Party deixa de usar molduras
            | douradas/vinho como linguagem dominante e passa a seguir a mesma
            | hierarquia editorial da ficha.
            |
            */


            /* ==============================================================
               CONTAINER / ESTADOS GERAIS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .party-v4 {
                color:
                    var(--theme-text-primary);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            > .min-h-0.flex-1 {
                scrollbar-width:
                    thin;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 25%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .party-v4-action-notice {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;
            }


            /* ==============================================================
               ALIADOS — MÓDULO
            ============================================================== */

            .character-sheet-theme[data-theme]
            .party-v4-social-module {
                width:
                    100%;

                max-width:
                    none;

                margin:
                    0;

                border:
                    1px solid
                    var(--theme-border);

                border-radius:
                    18px;

                background:
                    var(--theme-module-background);

                padding:
                    16px;

                box-shadow:
                    0 3px 12px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-header {
                align-items:
                    center;

                justify-content:
                    flex-start;

                gap:
                    9px;

                padding:
                    0 2px;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-header__title {
                font-size:
                    20px;

                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-header__divider {
                width:
                    4px;

                height:
                    4px;

                background:
                    var(--theme-accent-strong);

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-accent) 10%,
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-header__campaign {
                font-size:
                    14px;

                font-weight:
                    700;

                color:
                    var(--theme-text-secondary);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-rule {
                width:
                    100%;

                height:
                    1px;

                margin:
                    12px 0 0;

                background:
                    var(--theme-divider);
            }

            .character-sheet-theme[data-theme]
            .party-v4-campaign-select {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4-campaign-select:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 12%,
                        transparent
                    );
            }


            /* ==============================================================
               RETRATOS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-roster {
                display:
                    flex;

                flex-wrap:
                    nowrap;

                justify-content:
                    safe center;

                align-items:
                    flex-start;

                gap:
                    14px;

                width:
                    100%;

                margin-top:
                    14px;

                overflow-x:
                    auto;

                overflow-y:
                    visible;

                padding:
                    2px 4px 10px;

                scroll-behavior:
                    smooth;

                scroll-snap-type:
                    x proximity;

                scrollbar-width:
                    none;

                -ms-overflow-style:
                    none;

                overscroll-behavior-inline:
                    contain;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-roster::-webkit-scrollbar {
                display:
                    none;

                width:
                    0;

                height:
                    0;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally {
                width:
                    154px;

                flex:
                    0 0 154px;

                scroll-snap-align:
                    center;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__portrait-frame {
                width:
                    154px;

                padding:
                    4px;

                border:
                    1px solid
                    var(--theme-border);

                border-radius:
                    16px;

                background:
                    var(--theme-surface-card);

                box-shadow:
                    0 4px 12px rgba(0,0,0,.05);

                cursor:
                    pointer;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__portrait-frame::before {
                inset:
                    3px;

                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-border) 70%,
                        white
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__portrait-frame::after {
                top:
                    -1px;

                width:
                    48px;

                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        var(--theme-accent-strong),
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally:hover
            .party-social-ally__portrait-frame,
            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally:focus-within
            .party-social-ally__portrait-frame {
                transform:
                    translateY(-2px);

                border-color:
                    var(--theme-border-strong);

                box-shadow:
                    0 10px 22px rgba(0,0,0,.075);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__portrait {
                width:
                    146px;

                height:
                    176px;

                border-radius:
                    12px;

                background:
                    var(--theme-surface-inset);

                box-shadow:
                    inset 0 0 0 1px
                    var(--theme-border-soft);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__portrait::before {
                background:
                    linear-gradient(
                        180deg,
                        rgba(255,255,255,.05),
                        transparent 24%
                    ),
                    linear-gradient(
                        0deg,
                        rgba(15,15,15,.46) 0%,
                        rgba(15,15,15,.17) 25%,
                        transparent 50%
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__portrait::after {
                left:
                    12px;

                right:
                    12px;

                height:
                    3px;

                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        var(--theme-accent-strong),
                        transparent
                    );

                opacity:
                    .72;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__name {
                font-size:
                    12px;

                line-height:
                    1.12;

                color:
                    #fff;
            }


            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__exhaustion {
                left:
                    8px;

                top:
                    8px;

                min-height:
                    22px;

                border:
                    1px solid
                    rgba(255,255,255,.22);

                background:
                    rgba(46,35,26,.78);

                color:
                    #f8e7af;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__health {
                height:
                    7px;

                background:
                    rgba(20,20,20,.30);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__health-fill--healthy {
                background:
                    linear-gradient(
                        90deg,
                        #047857,
                        #10b981
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__health-fill--wounded {
                background:
                    linear-gradient(
                        90deg,
                        #b45309,
                        #f59e0b
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__health-fill--critical,
            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally__health-fill--down {
                background:
                    linear-gradient(
                        90deg,
                        #991b1b,
                        #ef4444
                    );
            }


            /* ==============================================================
               HEALTH ANIMATION — MESMA LINGUAGEM DO HERO
            ============================================================== */

            @keyframes partyV4Bloodied {
                0%, 68%, 100% {
                    box-shadow:
                        0 4px 12px rgba(0,0,0,.05),
                        0 0 0 rgba(185,28,28,0);
                }

                76% {
                    box-shadow:
                        0 4px 12px rgba(0,0,0,.05),
                        0 0 0 2px rgba(185,28,28,.23),
                        0 0 15px rgba(185,28,28,.13);
                }

                91% {
                    box-shadow:
                        0 4px 12px rgba(0,0,0,.05),
                        0 0 0 1px rgba(185,28,28,.16),
                        0 0 8px rgba(185,28,28,.08);
                }
            }

            @keyframes partyV4Critical {
                0%, 100% {
                    box-shadow:
                        0 4px 12px rgba(0,0,0,.05),
                        0 0 0 rgba(220,38,38,0);
                }

                12% {
                    box-shadow:
                        0 4px 12px rgba(0,0,0,.05),
                        0 0 0 3px rgba(220,38,38,.38),
                        0 0 20px rgba(220,38,38,.22);
                }

                22% {
                    box-shadow:
                        0 4px 12px rgba(0,0,0,.05);
                }

                32% {
                    box-shadow:
                        0 4px 12px rgba(0,0,0,.05),
                        0 0 0 2px rgba(220,38,38,.27),
                        0 0 12px rgba(220,38,38,.14);
                }
            }

            .party-v4
            .party-v3-portrait--wounded {
                animation:
                    partyV4Bloodied
                    3.8s
                    ease-in-out
                    infinite !important;
            }

            .party-v4
            .party-v3-portrait--critical {
                animation:
                    partyV4Critical
                    2.15s
                    ease-in-out
                    infinite !important;
            }

            .party-v4
            .party-v3-portrait--down {
                animation:
                    none !important;

                box-shadow:
                    inset 0 0 0 1px rgba(127,29,29,.30),
                    0 0 0 2px rgba(127,29,29,.10);
            }


            /* ==============================================================
               SOCIAL MENU
            ============================================================== */

            .character-sheet-theme[data-theme]
            .party-v4
            .party-v4-social-menu {
                width:
                    220px;

                border-color:
                    var(--theme-border);

                background:
                    var(--theme-surface-raised);

                box-shadow:
                    0 18px 42px rgba(0,0,0,.18);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu::before {
                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        var(--theme-accent),
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__header {
                border-color:
                    var(--theme-divider);

                background:
                    var(--theme-band-background);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__name {
                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__item-action {
                border-color:
                    var(--theme-divider);

                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__item-action:hover {
                background:
                    var(--theme-card-hover);

                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__item-action::after {
                color:
                    var(--theme-accent-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__label {
                color:
                    var(--theme-label);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__poke-button {
                border-color:
                    var(--theme-border);

                background:
                    var(--theme-surface-card);

                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-menu__poke-button:hover {
                border-color:
                    var(--theme-border-strong);

                background:
                    var(--theme-card-hover);
            }


            /* ==============================================================
               DIÁRIO
            ============================================================== */

            .character-sheet-theme[data-theme]
            .party-v4-diary-section {
                border-top:
                    1px solid
                    var(--theme-divider);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-diary-heading {
                font-size:
                    22px;

                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-diary-page-button,
            .character-sheet-theme[data-theme]
            .party-v4
            .party-diary-add-button {
                color:
                    var(--theme-label);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-diary-page-button:hover,
            .character-sheet-theme[data-theme]
            .party-v4
            .party-diary-add-button:hover,
            .character-sheet-theme[data-theme]
            .party-v4
            .party-diary-page-button.is-active {
                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-diary-page-button::after {
                background:
                    var(--theme-accent-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-v4-diary-paper {
                border-color:
                    var(--theme-border);

                background:
                    linear-gradient(
                        90deg,
                        color-mix(
                            in srgb,
                            var(--theme-accent) 7%,
                            transparent
                        ) 0,
                        color-mix(
                            in srgb,
                            var(--theme-accent) 7%,
                            transparent
                        ) 1px,
                        transparent 1px
                    )
                    44px 0 / 100% 100% no-repeat,
                    var(--theme-surface-card);

                box-shadow:
                    0 8px 24px rgba(0,0,0,.045),
                    inset 0 1px 0 rgba(255,255,255,.40);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-v4-diary-title {
                color:
                    var(--theme-text-strong);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-v4-diary-title:hover {
                background:
                    var(--theme-accent-softer);
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-v4-diary-writing {
                color:
                    var(--theme-text-primary);

                background-image:
                    repeating-linear-gradient(
                        to bottom,
                        transparent 0,
                        transparent 34px,
                        var(--theme-divider) 34px,
                        var(--theme-divider) 35px
                    );
            }


            /* ==============================================================
               MODAL — ENTREGAR ITEM
            ============================================================== */

            .character-sheet-theme[data-theme]
            .party-v4-give-modal {
                border-color:
                    var(--theme-modal-border, var(--theme-border));

                background:
                    var(--theme-modal-surface, var(--theme-paper-strong));

                color:
                    var(--theme-modal-text, var(--theme-text-primary));

                box-shadow:
                    var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.30));
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-modal__header {
                border-color:
                    var(--theme-modal-border, var(--theme-border));

                background:
                    var(--theme-modal-header-background, var(--theme-band-background));
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-modal__header
            > p:first-child {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-modal__header
            h3 {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-modal__subtitle {
                margin-top:
                    4px;

                font-size:
                    10px;

                color:
                    var(--theme-text-secondary);
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-modal__body {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong));

                scrollbar-width:
                    thin;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 25%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-search,
            .character-sheet-theme[data-theme]
            .party-v4-give-quantity {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-search:focus,
            .character-sheet-theme[data-theme]
            .party-v4-give-quantity:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 12%,
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-item {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-item:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-modal__footer {
                border-color:
                    var(--theme-modal-border, var(--theme-border));

                background:
                    var(--theme-modal-header-background, var(--theme-band-background));
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-submit {
                border-color:
                    var(--theme-accent-strong);

                background:
                    var(--theme-button-background);

                color:
                    var(--theme-button-text);
            }

            .character-sheet-theme[data-theme]
            .party-v4-give-submit:hover:not(:disabled) {
                background:
                    var(--theme-button-hover-background);
            }


            /* ==============================================================
               RESPONSIVO
            ============================================================== */

            @media (max-width: 640px) {
                .character-sheet-theme[data-theme]
                .party-v4-social-module {
                    padding:
                        12px;
                }

                .character-sheet-theme[data-theme]
                .party-v4
                .party-social-roster {
                    justify-content:
                        safe center;

                    gap:
                        12px;

                    padding-inline:
                        2px;
                }

                .character-sheet-theme[data-theme]
                .party-v4
                .party-social-ally {
                    width:
                        148px;

                    flex-basis:
                        148px;
                }

                .character-sheet-theme[data-theme]
                .party-v4
                .party-social-ally__portrait-frame {
                    width:
                        148px;
                }

                .character-sheet-theme[data-theme]
                .party-v4
                .party-social-ally__portrait {
                    width:
                        140px;

                    height:
                        170px;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .party-v4
                .party-v3-portrait--wounded,
                .party-v4
                .party-v3-portrait--critical {
                    animation:
                        none !important;
                }
            }


            /*
             * Quando os cards cabem, ficam centralizados.
             * Quando excedem a largura, o overflow horizontal assume sem
             * deixar o primeiro card preso fora da área rolável.
             */
            @supports not (justify-content: safe center) {
                .character-sheet-theme[data-theme]
                .party-v4
                .party-social-roster {
                    justify-content:
                        flex-start;
                }

                .character-sheet-theme[data-theme]
                .party-v4
                .party-social-roster::before,
                .character-sheet-theme[data-theme]
                .party-v4
                .party-social-roster::after {
                    content:
                        "";

                    flex:
                        0 0 max(
                            0px,
                            calc(
                                (
                                    100% -
                                    (
                                        154px * 3
                                        +
                                        14px * 2
                                    )
                                ) / 2
                            )
                        );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PARTY V9 — MENU SOCIAL SOBRE OS CARDS
            |--------------------------------------------------------------------------
            |
            | Mantemos a estrutura antiga que já funcionava:
            | o menu continua absoluto dentro do próprio retrato.
            |
            | O defeito era de stacking context. Os retratos usam transform no
            | hover, criando contextos de empilhamento próprios; por isso cards
            | vizinhos podiam ser pintados por cima do menu, mesmo ele tendo
            | z-index alto.
            |
            | A correção eleva o CARD INTEIRO quando seu menu está aberto.
            |
            */

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-roster {
                isolation:
                    isolate;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally {
                position:
                    relative;

                z-index:
                    1;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally:hover {
                z-index:
                    2;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally--menu-open {
                z-index:
                    100 !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally--menu-open
            .party-social-ally__portrait-frame {
                position:
                    relative;

                z-index:
                    101 !important;

                overflow:
                    visible !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-ally--menu-open
            .party-social-menu {
                z-index:
                    120 !important;

                pointer-events:
                    auto;
            }

            /*
             * O menu antigo começa alguns pixels abaixo do topo do retrato e
             * termina pouco além da moldura. O padding extra só existe enquanto
             * há um menu aberto, evitando qualquer corte pelo scroll horizontal.
             */
            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-roster--menu-open {
                padding-bottom:
                    42px;
            }

            /*
             * Um card vizinho nunca sobe acima do card ativo, mesmo durante
             * hover/focus.
             */
            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-roster--menu-open
            .party-social-ally:not(.party-social-ally--menu-open) {
                z-index:
                    1 !important;
            }

            .character-sheet-theme[data-theme]
            .party-v4
            .party-social-roster--menu-open
            .party-social-ally:not(.party-social-ally--menu-open)
            .party-social-ally__portrait-frame {
                z-index:
                    auto !important;
            }

        /* Indicador discreto sobre o retrato; não altera o layout dos cards. */
        .party-v4 .party-concentration-badge {
            position: absolute; z-index: 13; top: 9px; right: 9px;
            display: flex; align-items: center; justify-content: center;
            width: 27px; height: 27px; border-radius: 9999px;
            color: #faf6ff; background: rgba(86, 50, 117, .94);
            border: 1px solid rgba(255,255,255,.45);
            box-shadow: 0 3px 12px rgba(56, 33, 80, .22);
        }
        .party-v4 .party-concentration-line {
            margin-top: 7px; padding: 8px 10px;
            border-top: 1px solid var(--theme-border, #d8c6ad);
            font-size: 10px; color: var(--theme-text-primary, #50392d);
        }
        </style>
    @endpush
@endonce

<div
    @character-inventory-synced.window="
        if (loaded && Array.isArray($event.detail?.items)) {
            items = $event.detail.items;
        }
    "
    x-data="{
        bootstrapUrl: @js(route('characters.party.index', $character)),
        statesUrl: @js(route('characters.party.states', $character)),
        concentrationsUrl: @js(route('characters.party.concentrations', $character)),
        concentrationPollCount: 0,
        partyBaseUrl: @js(url('/characters/' . $character->id . '/party')),
        csrf: @js(csrf_token()),

        loaded: false,
        loading: false,

        campaigns: [],
        campaign: null,
        campaignId: null,

        members: [],
        memberMenuId: null,

        polling: false,
        pollTimer: null,

        pages: [
            {
                id: 'page-1',
                title: 'Página 1',
                content: '',
            },
        ],
        diaryPageIndex: 0,
        diaryState: 'idle',
        diaryTimer: null,
        diaryRevision: 0,
        diarySavedRevision: 0,
        diarySavePromise: null,

        items: [],
        itemSearch: '',
        giveItemOpen: false,
        selectedMemberId: null,
        transferItemId: null,
        transferQuantity: 1,
        transferring: false,

        actionMessage: null,
        actionError: null,

        normalizeDiaryPages(sourcePages = []) {
            const pages = Array.isArray(sourcePages)
                ? sourcePages
                : [];

            if (pages.length === 0) {
                return [
                    {
                        id: 'page-1',
                        title: 'Página 1',
                        content: '',
                    },
                ];
            }

            return pages.map(
                (page, index) => ({
                    ...page,
                    id:
                        String(page?.id ?? '').trim()
                        || `page-${index + 1}`,
                    title:
                        String(page?.title ?? '').trim()
                        || `Página ${index + 1}`,
                    content:
                        String(page?.content ?? ''),
                })
            );
        },

        get selectedMember() {
            return this.members.find(
                member =>
                    Number(member.id)
                    === Number(this.selectedMemberId)
            ) ?? null;
        },

        get selectedItem() {
            return this.items.find(
                item =>
                    Number(item.id)
                    === Number(this.transferItemId)
            ) ?? null;
        },

        get filteredItems() {
            const term = String(this.itemSearch ?? '')
                .trim()
                .toLowerCase();

            if (!term) {
                return this.items;
            }

            return this.items.filter(
                item =>
                    String(item.name ?? '')
                        .toLowerCase()
                        .includes(term)
                    ||
                    String(item.type ?? '')
                        .toLowerCase()
                        .includes(term)
            );
        },

        get filteredMundaneItems() {
            return this.filteredItems.filter(
                item =>
                    String(item.type ?? '').toLowerCase()
                    === 'mundane'
            );
        },

        get filteredWonderfulItems() {
            return this.filteredItems.filter(
                item =>
                    String(item.type ?? '').toLowerCase()
                    === 'wonderful'
            );
        },

        get filteredTechnologicalItems() {
            return this.filteredItems.filter(
                item =>
                    String(item.type ?? '').toLowerCase()
                    === 'technological'
            );
        },

        async ensureLoaded() {
            if (!this.loaded && !this.loading) {
                await this.loadParty();
            }

            this.startPolling();
        },

        async loadParty(campaignId = null) {
            this.loading = true;
            this.actionError = null;

            try {
                const url = new URL(
                    this.bootstrapUrl,
                    window.location.origin
                );

                if (campaignId) {
                    url.searchParams.set(
                        'campaign_id',
                        campaignId
                    );
                }

                const response = await fetch(
                    url.toString(),
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        cache: 'no-store',
                    }
                );

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível carregar a Party.'
                    );
                }

                this.campaigns = Array.isArray(data?.campaigns)
                    ? data.campaigns
                    : [];

                this.campaign = data?.campaign ?? null;
                this.campaignId = this.campaign?.id ?? null;

                this.members = Array.isArray(data?.members)
                    ? data.members
                    : [];
                // Consulta independente: não exige substituir o PartyController antigo.
                void this.refreshConcentrations();

                this.pages = this.normalizeDiaryPages(
                    data?.pages
                );
                this.diaryRevision = 0;
                this.diarySavedRevision = 0;
                this.diaryState = 'idle';

                this.diaryPageIndex = Math.min(
                    this.diaryPageIndex,
                    this.pages.length - 1
                );

                this.items = Array.isArray(data?.items)
                    ? data.items
                    : [];

                this.loaded = true;
            } catch (error) {
                console.error(
                    'Erro ao carregar Party:',
                    error
                );

                this.actionError = error?.message
                    ?? 'Não foi possível carregar a Party.';
            } finally {
                this.loading = false;
            }
        },

        async changeCampaign() {
            const nextCampaignId = this.campaignId;
            const previousCampaignId = this.campaign?.id ?? null;

            // A seleção muda antes do evento change. Salve a campanha
            // anterior antes de substituir suas páginas na interface.
            if (this.diaryTimer) {
                clearTimeout(this.diaryTimer);
                this.diaryTimer = null;
            }

            if (!(await this.saveDiary())) {
                this.campaignId = previousCampaignId;
                this.actionError = 'Não foi possível salvar o Diário. Tente novamente antes de trocar de campanha.';
                return;
            }

            this.stopPolling();
            this.memberMenuId = null;
            this.giveItemOpen = false;
            this.selectedMemberId = null;
            this.transferItemId = null;
            this.transferQuantity = 1;
            this.diaryPageIndex = 0;

            await this.loadParty(nextCampaignId);
            this.startPolling();
        },

        startPolling() {
            if (this.pollTimer || !this.campaignId) {
                return;
            }

            this.refreshStates();

            this.pollTimer = setInterval(
                () => {
                    this.refreshStates();
                },
                1500
            );
        },

        stopPolling() {
            if (!this.pollTimer) {
                return;
            }

            clearInterval(
                this.pollTimer
            );

            this.pollTimer = null;
        },

        async refreshConcentrations() {
            if (!this.campaignId || document.hidden) return;
            const requestedCampaignId = this.campaignId;
            try {
                const url = new URL(this.concentrationsUrl, window.location.origin);
                url.searchParams.set('campaign_id', this.campaignId);
                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store', credentials: 'same-origin',
                });
                if (!response.ok) return;
                const data = await response.json();
                if (Number(this.campaignId) !== Number(requestedCampaignId)) return;
                const states = data?.members || {};
                this.members = this.members.map(member => ({
                    ...member,
                    concentration: states[String(member.id)] || { active: false, spell_name: null },
                }));
            } catch (error) {
                // Falha no indicador não compromete as demais informações da Party.
                console.warn('Concentração da Party indisponível:', error);
            }
        },

        async refreshStates() {
            if (
                this.polling
                || !this.campaignId
                || document.hidden
            ) {
                return;
            }

            this.polling = true;
            this.concentrationPollCount++;
            if (this.concentrationPollCount % 3 === 0) {
                void this.refreshConcentrations();
            }

            try {
                const url = new URL(
                    this.statesUrl,
                    window.location.origin
                );

                url.searchParams.set(
                    'campaign_id',
                    this.campaignId
                );

                const response = await fetch(
                    url.toString(),
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        cache: 'no-store',
                    }
                );

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                const states = data?.members ?? {};

                this.members = this.members.map(
                    member => {
                        const state = states?.[
                            String(member.id)
                        ];

                        if (!state) {
                            return member;
                        }

                        return {
                            ...member,
                            ...state,
                        };
                    }
                );
            } finally {
                this.polling = false;
            }
        },

        toggleMemberMenu(memberId) {
            this.memberMenuId =
                Number(this.memberMenuId)
                === Number(memberId)
                    ? null
                    : memberId;
        },

        openGiveItem(member) {
            this.memberMenuId = null;
            this.selectedMemberId = member.id;
            this.giveItemOpen = true;
            this.transferItemId = null;
            this.transferQuantity = 1;
            this.itemSearch = '';
            this.actionMessage = null;
            this.actionError = null;
        },

        closeGiveItem() {
            this.giveItemOpen = false;
            this.selectedMemberId = null;
            this.transferItemId = null;
            this.transferQuantity = 1;
            this.itemSearch = '';
        },

        async pokeMember(member, emoji) {
            if (!this.campaignId || !member) {
                return;
            }

            this.memberMenuId = null;
            this.actionMessage = null;
            this.actionError = null;

            try {
                const response = await fetch(
                    `${this.partyBaseUrl}/${this.campaignId}/poke`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            recipient_character_id: member.id,
                            emoji: emoji,
                        }),
                    }
                );

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível cutucar.'
                    );
                }

                this.actionMessage = data?.message
                    ?? `${member.name} foi cutucado.`;

                window.setTimeout(
                    () => {
                        this.actionMessage = null;
                    },
                    2200
                );
            } catch (error) {
                this.actionError = error?.message
                    ?? 'Não foi possível cutucar.';
            }
        },

        chooseItem(item) {
            this.transferItemId = item.id;
            this.transferQuantity = 1;
            this.actionMessage = null;
            this.actionError = null;
        },

        normalizeTransferQuantity() {
            const item = this.selectedItem;

            if (!item) {
                this.transferQuantity = 1;
                return;
            }

            const maximum = Math.max(
                1,
                Number(item.quantity) || 1
            );

            this.transferQuantity = Math.max(
                1,
                Math.min(
                    maximum,
                    Number(this.transferQuantity) || 1
                )
            );
        },

        async transferItem() {
            if (
                this.transferring
                || !this.campaignId
                || !this.selectedMember
                || !this.selectedItem
            ) {
                return;
            }

            this.normalizeTransferQuantity();

            const item = this.selectedItem;
            const member = this.selectedMember;

            const confirmed = window.confirm(
                `Entregar ${this.transferQuantity}× ${item.name} para ${member.name}?`
            );

            if (!confirmed) {
                return;
            }

            this.transferring = true;
            this.actionMessage = null;
            this.actionError = null;

            try {
                const response = await fetch(
                    `${this.partyBaseUrl}/${this.campaignId}/transfer-item`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            recipient_character_id: member.id,
                            item_id: item.id,
                            quantity: this.transferQuantity,
                        }),
                    }
                );

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? Object
                            .values(data?.errors ?? {})
                            .flat()
                            .join(' ')
                        ?? 'Não foi possível entregar o item.'
                    );
                }

                this.items = Array.isArray(data?.items)
                    ? data.items
                    : [];

                window.dispatchEvent(
                    new CustomEvent('party-item-transfer-completed', {
                        detail: {
                            senderCharacterId: @js($character->id),
                            recipientCharacterId: member.id,
                            itemId: item.id,
                        },
                    })
                );

                this.actionMessage = null;

                window.dispatchEvent(new CustomEvent('spellbound-notify', {
                    detail: {
                        title: 'Item enviado!',
                        message: `${data?.quantity ?? this.transferQuantity}× ${data?.item_name ?? item.name} para ${data?.recipient_name ?? member.name}.`,
                        variant: 'success',
                        duration: 5000,
                    },
                }));

                this.closeGiveItem();
            } catch (error) {
                this.actionError = error?.message
                    ?? 'Não foi possível entregar o item.';

                window.dispatchEvent(new CustomEvent('spellbound-notify', {
                    detail: {
                        title: 'Falha no envio',
                        message: this.actionError,
                        variant: 'error',
                    },
                }));
            } finally {
                this.transferring = false;
            }
        },

        newPageId() {
            if (
                window.crypto
                && typeof window.crypto.randomUUID === 'function'
            ) {
                return window.crypto.randomUUID();
            }

            return `page-${Date.now()}-${Math.random().toString(16).slice(2)}`;
        },

        addDiaryPage() {
            if (this.pages.length >= 50) {
                return;
            }

            const nextNumber =
                this.pages.length + 1;

            this.pages.push({
                id: this.newPageId(),
                title: `Página ${nextNumber}`,
                content: '',
            });

            this.diaryPageIndex =
                this.pages.length - 1;

            this.queueDiarySave();
        },

        deleteDiaryPage() {
            if (this.pages.length <= 1) {
                return;
            }

            if (!window.confirm('Excluir esta página do Diário?')) {
                return;
            }

            this.pages.splice(
                this.diaryPageIndex,
                1
            );

            this.diaryPageIndex = Math.max(
                0,
                Math.min(
                    this.diaryPageIndex,
                    this.pages.length - 1
                )
            );

            this.queueDiarySave();
        },

        queueDiarySave() {
            // Toda alteração recebe uma versão própria. Uma resposta antiga
            // jamais deve substituir o texto que o jogador ainda está editando.
            this.diaryRevision += 1;
            this.diaryState = 'typing';

            if (this.diaryTimer) {
                clearTimeout(this.diaryTimer);
            }

            this.diaryTimer = setTimeout(() => {
                this.diaryTimer = null;
                void this.saveDiary();
            }, 700);
        },

        async saveDiary() {
            // Use a campanha das páginas carregadas, não a seleção do
            // <select>, que já mudou quando changeCampaign() é executado.
            const campaignId = this.campaign?.id;
            if (!campaignId) {
                return true;
            }

            // Requisições sequenciais: impede que um PATCH mais antigo
            // termine depois e sobrescreva uma edição mais recente.
            if (this.diarySavePromise) {
                return this.diarySavePromise;
            }

            if (this.diarySavedRevision >= this.diaryRevision) {
                return true;
            }

            if (this.diaryTimer) {
                clearTimeout(this.diaryTimer);
                this.diaryTimer = null;
            }

            const savePendingChanges = async () => {
                while (this.diarySavedRevision < this.diaryRevision) {
                    const revision = this.diaryRevision;
                    // Snapshot imutável desta revisão, antes da requisição.
                    const payload = JSON.stringify({
                        pages: this.pages.map(page => ({
                            id: page.id,
                            title: page.title,
                            content: page.content,
                        })),
                    });

                    this.diaryState = 'saving';

                    try {
                        const response = await fetch(
                            `${this.partyBaseUrl}/${campaignId}/notes`,
                            {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.csrf,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: payload,
                            }
                        );

                        if (!response.ok) {
                            const data = await response.json().catch(() => null);
                            throw new Error(
                                data?.message
                                ?? 'Não foi possível salvar o Diário.'
                            );
                        }

                        // NÃO aplicar data.pages ao modelo: durante o fetch
                        // o jogador pode continuar digitando ou pressionar Enter.
                        // O servidor persiste o snapshot, mas a tela conserva
                        // texto, seleção, foco e posição atual do cursor.
                        this.diarySavedRevision = revision;
                    } catch (error) {
                        console.error('Erro ao salvar Diário da Party:', error);
                        this.diaryState = 'error';
                        return false;
                    }
                }

                this.diaryState = 'saved';
                window.setTimeout(() => {
                    if (
                        this.diaryState === 'saved'
                        && this.diarySavedRevision === this.diaryRevision
                    ) {
                        this.diaryState = 'idle';
                    }
                }, 1600);

                return true;
            };

            const pending = savePendingChanges();
            this.diarySavePromise = pending;

            try {
                return await pending;
            } finally {
                if (this.diarySavePromise === pending) {
                    this.diarySavePromise = null;
                }
            }
        },

        portraitClass(member) {
            return {
                wounded: 'party-v3-portrait--wounded',
                critical: 'party-v3-portrait--critical',
                down: 'party-v3-portrait--down',
            }[member?.health_state] ?? '';
        },

        stateLabel(member) {
            return {
                healthy: 'Estável',
                wounded: 'Sangrando',
                critical: 'Crítico',
                down: '0 PV',
                unknown: 'Sem dados',
            }[member?.health_state] ?? '—';
        },

        stateClass(member) {
            return {
                healthy: 'text-emerald-700',
                wounded: 'text-red-700',
                critical: 'text-red-800',
                down: 'text-red-900',
                unknown: 'text-stone-500',
            }[member?.health_state] ?? 'text-stone-500';
        },
    }"

    x-effect="
        if (drawer === 'party') {
            ensureLoaded()
        } else {
            stopPolling()
        }
    "

    class="party-v4 flex h-full min-h-0 flex-col"
>
    <div class="min-h-0 flex-1 overflow-y-auto px-6 py-7 sm:px-8">
        <div
            x-show="loading && !loaded"
            class="py-16 text-center text-[10px] font-black uppercase tracking-[0.20em] text-[#8c6239]"
        >
            Reunindo a Party...
        </div>

        <template x-if="loaded && campaigns.length === 0">
            <div class="py-16 text-center">
                <h3 class="font-serif text-xl font-black text-[#53150f]">
                    Nenhuma Party
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-[11px] leading-relaxed text-[#8c6239]">
                    Esta ficha ainda não está compartilhada em uma campanha.
                </p>
            </div>
        </template>

        <template x-if="loaded && campaigns.length > 0">
            <div class="space-y-4">
                <section>
                    {{-- ============================================================
                         ALIADOS — IDENTIDADE SPELLBOUND
                    ============================================================= --}}
                    <div class="party-social-strip party-v4-social-module">
                        <div class="party-social-header">
                            <span class="party-social-header__title">
                                Aliados
                            </span>

                            <span class="party-social-header__divider"></span>

                            <span
                                class="party-social-header__campaign"
                                x-text="campaign?.name ?? 'Campanha'"
                            ></span>
                        </div>

                        <div class="party-social-rule"></div>

                        <div class="mt-1 flex justify-center">
                            <select
                                x-show="campaigns.length > 1"
                                x-cloak
                                x-model.number="campaignId"
                                x-on:change="changeCampaign()"
                                class="
                                    party-v4-campaign-select
                                    rounded-lg
                                    border
                                    px-3
                                    py-1.5
                                    text-[9px]
                                    font-bold
                                    focus:outline-none
                                "
                            >
                                <template x-for="option in campaigns" :key="option.id">
                                    <option :value="option.id" x-text="option.name"></option>
                                </template>
                            </select>
                        </div>

                        <div
                            x-show="members.length === 0"
                            class="mx-auto mt-4 max-w-sm py-4 text-center text-[9px] text-[#8c6239]"
                        >
                            Ainda não há outros personagens compartilhados nesta campanha.
                        </div>

                        <div
                            x-show="members.length > 0"

                            class="party-social-roster"

                            :class="{
                                'party-social-roster--menu-open':
                                    memberMenuId !== null,
                            }"
                        >
                            <template x-for="member in members" :key="member.id">
                                <article
                                    class="party-social-ally"

                                    :class="{
                                        'party-social-ally--menu-open':
                                            Number(memberMenuId)
                                            === Number(member.id),
                                    }"

                                    :title="member.name"
                                >
                                    <div
                                        class="party-social-ally__portrait-frame"
                                        x-on:click="toggleMemberMenu(member.id)"
                                        x-on:click.outside="
                                            if (Number(memberMenuId) === Number(member.id)) {
                                                memberMenuId = null
                                            }
                                        "
                                    >
                                        <div
                                            class="party-social-ally__portrait"
                                            :class="portraitClass(member)"
                                        >
                                            <template x-if="member.image_url">
                                                <img
                                                    :src="member.image_url"
                                                    x-on:error="member.image_url = null"
                                                    :alt="member.name"
                                                    class="h-full w-full object-cover object-top"
                                                >
                                            </template>

                                            <template x-if="!member.image_url">
                                                <div
                                                    class="party-social-ally__fallback flex h-full w-full items-center justify-center font-serif text-4xl font-black text-[#53150f]"
                                                    x-text="String(member.name ?? '?').slice(0, 1).toUpperCase()"
                                                ></div>
                                            </template>

                                            <span
                                                x-show="member.exhaustion_enabled && Number(member.exhaustion) > 0"
                                                x-cloak
                                                class="party-social-ally__exhaustion"
                                                x-text="`EX ${member.exhaustion}`"
                                            ></span>

                                            <span x-show="member.concentration?.active" x-cloak
                                                class="party-concentration-badge"
                                                :title="`Concentrando: ${member.concentration?.spell_name || 'Feitiço'}`"
                                                aria-label="Concentração ativa">✦</span>

                                            <div class="party-social-ally__identity">
                                                <span
                                                    class="party-social-ally__name"
                                                    x-text="member.name"
                                                ></span>
                                            </div>

                                            <div class="party-social-ally__health">
                                                <div
                                                    class="party-social-ally__health-fill"
                                                    :class="{
                                                        'party-social-ally__health-fill--healthy': member.health_state === 'healthy',
                                                        'party-social-ally__health-fill--wounded': member.health_state === 'wounded',
                                                        'party-social-ally__health-fill--critical': member.health_state === 'critical',
                                                        'party-social-ally__health-fill--down': member.health_state === 'down',
                                                    }"
                                                    :style="`width: ${Math.max(0, Math.min(100, Number(member.hp_percent ?? 0)))}%`"
                                                ></div>
                                            </div>
                                        </div>

                                        <div
                                            x-show="Number(memberMenuId) === Number(member.id)"
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave="transition ease-in duration-160"
                                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                                            class="party-social-menu party-v4-social-menu"
                                            x-on:click.stop
                                        >
                                            <div class="party-social-menu__header">
                                                <p
                                                    class="party-social-menu__name"
                                                    x-text="member.name"
                                                ></p>
                                            </div>

                                            <div x-show="member.concentration?.active" x-cloak class="party-concentration-line">
                                                <span class="font-bold">✦ Concentrando:</span>
                                                <span x-text="member.concentration?.spell_name || 'Feitiço'"></span>
                                            </div>

                                            <button
                                                type="button"
                                                x-on:click="openGiveItem(member)"
                                                class="party-social-menu__item-action"
                                            >
                                                <span>Entregar item</span>
                                            </button>

                                            <div class="party-social-menu__poke">
                                                <p class="party-social-menu__label">
                                                    Cutucar
                                                </p>

                                                <div class="party-social-menu__poke-grid">
                                                    <template
                                                        x-for="
                                                            poke
                                                            in
                                                            [
                                                                { emoji: '👉', label: 'Ei!' },
                                                                { emoji: '👀', label: 'Olho' },
                                                                { emoji: '💩', label: 'Merda' },
                                                                { emoji: '💀', label: 'Caveira' },
                                                                { emoji: '❤️', label: 'Coração' },
                                                                { emoji: '🔥', label: 'Fogo' },
                                                            ]
                                                        "
                                                        :key="poke.emoji"
                                                    >
                                                        <button
                                                            type="button"
                                                            x-on:click="pokeMember(member, poke.emoji)"
                                                            class="party-social-menu__poke-button"
                                                            :title="poke.label"
                                                            x-text="poke.emoji"
                                                        ></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>

                        {{-- NPCs aliados do encontro ativo, no MESMO módulo dos aliados da campanha.
                             Componente independente da Party, sem substituir os estados sociais. --}}
                        @include('characters.components.party-combat-allies', [
                            'character' => $character,
                        ])
                    </div>
                </section>

                <div
                    x-show="actionMessage || actionError"
                    x-cloak
                    class="
                        party-v4-action-notice
                        rounded-xl
                        border
                        px-4
                        py-3
                        text-[10px]
                        font-bold
                    "
                    :class="
                        actionError
                            ? 'border-red-200 bg-red-50 text-red-700'
                            : 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    "
                    x-text="actionError ?? actionMessage"
                ></div>

                <section class="party-v4-diary-section pt-4">
                    {{-- ============================================================
                         DIÁRIO — CABEÇALHO + PAGINAÇÃO NA MESMA LINHA
                    ============================================================= --}}

                    <div class="party-diary-toolbar">
                        <h2 class="party-diary-heading">
                            Diário de
                            <span class="party-diary-heading__name">{{ $character->name }}</span>
                        </h2>

                        <div class="party-diary-pages" aria-label="Páginas do diário">
                            <template x-for="(page, index) in pages" :key="page.id">
                                <button
                                    type="button"
                                    x-on:click="diaryPageIndex = index"
                                    :title="page.title || `Página ${index + 1}`"
                                    class="party-diary-page-button"
                                    :class="{
                                        'is-active': diaryPageIndex === index,
                                    }"
                                    x-text="String(index + 1).padStart(2, '0')"
                                ></button>
                            </template>

                            <button
                                type="button"
                                x-on:click="addDiaryPage()"
                                class="party-diary-add-button"
                                title="Adicionar folha"
                            >
                                +
                            </button>

                            <button
                                x-show="pages.length > 1"
                                x-cloak
                                type="button"
                                x-on:click="deleteDiaryPage()"
                                class="party-diary-delete-button"
                                title="Excluir folha atual"
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M4 7h16" />
                                    <path d="M9 7V4h6v3" />
                                    <path d="M8 11v6" />
                                    <path d="M12 11v6" />
                                    <path d="M16 11v6" />
                                    <path d="M6 7l1 13h10l1-13" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- FOLHA --}}
                    <div
                        x-show="pages[diaryPageIndex]"
                        x-cloak
                        class="party-diary-paper party-v4-diary-paper mt-3"
                    >
                        <div class="relative z-10 px-5 pb-5 pt-4 sm:px-7 sm:pb-6 sm:pt-5">
                            {{-- O PRÓPRIO TÍTULO É EDITÁVEL --}}
                            <input
                                type="text"
                                x-model="pages[diaryPageIndex].title"
                                x-on:input="queueDiarySave()"
                                maxlength="120"
                                placeholder="Página sem título"
                                title="Clique para editar"
                                class="party-diary-title party-v4-diary-title block w-full rounded-md border-0 bg-transparent px-2 py-1 font-serif text-[20px] font-black leading-tight placeholder:opacity-40 focus:ring-0"
                            >

                            <textarea
                                x-model="pages[diaryPageIndex].content"
                                x-on:input="queueDiarySave()"
                                rows="13"
                                maxlength="20000"
                                placeholder="Escreva..."
                                class="party-diary-writing party-v4-diary-writing mt-2 min-h-[360px] w-full resize-y border-0 bg-transparent py-2 pl-7 pr-2 font-serif text-[15px] leading-[35px] placeholder:opacity-40 focus:ring-0"
                            ></textarea>

                            <div
                                x-show="diaryState !== 'idle'"
                                x-cloak
                                aria-live="polite"
                                class="mt-2 flex items-center justify-end gap-2 text-[10px]"
                                style="color: var(--theme-text-secondary)"
                            >
                                <span
                                    x-text="({
                                        typing: 'Alterações pendentes...',
                                        saving: 'Salvando...',
                                        saved: 'Salvo',
                                        error: 'Não foi possível salvar.'
                                    })[diaryState] ?? ''"
                                ></span>
                                <button
                                    x-show="diaryState === 'error'"
                                    x-cloak
                                    type="button"
                                    x-on:click="saveDiary()"
                                    class="font-bold underline underline-offset-2"
                                    style="color: var(--theme-accent-strong)"
                                >
                                    Tentar novamente
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </template>
    </div>

    {{-- ================================================================
         ENTREGAR ITEM — MODAL
         Abre sobre a Party e fecha clicando fora.
    ================================================================= --}}
    <div
        x-show="giveItemOpen && selectedMember"
        x-cloak

        x-transition:enter="character-theme-modal-layer-enter"
        x-transition:enter-start="character-theme-modal-layer-enter-start"
        x-transition:enter-end="character-theme-modal-layer-enter-end"
        x-transition:leave="character-theme-modal-layer-leave"
        x-transition:leave-start="character-theme-modal-layer-leave-start"
        x-transition:leave-end="character-theme-modal-layer-leave-end"

        role="dialog"
        aria-modal="true"
        aria-label="Entregar item"

        class="
            character-theme-modal-layer
            party-v4-modal-layer

            fixed
            inset-0
            z-[200]

            flex
            items-center
            justify-center

            p-4
            sm:p-6
        "
    >
        <div
            class="
                character-theme-modal-backdrop
                absolute
                inset-0
            "
            x-on:click="
                closeGiveItem()
            "
        ></div>

        <section
            x-on:click.stop

            x-transition:enter="character-theme-modal-panel-enter"
            x-transition:enter-start="character-theme-modal-panel-enter-start"
            x-transition:enter-end="character-theme-modal-panel-enter-end"
            x-transition:leave="character-theme-modal-panel-leave"
            x-transition:leave-start="character-theme-modal-panel-leave-start"
            x-transition:leave-end="character-theme-modal-panel-leave-end"

            class="
                character-theme-modal
                party-v4-give-modal

                relative
                z-10

                flex
                max-h-[88dvh]
                w-full
                max-w-[620px]
                flex-col
                overflow-hidden

                rounded-2xl
                border

                shadow-2xl
            "
        >
            <header
                class="
                    party-v4-give-modal__header

                    border-b

                    px-5
                    py-4
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
                    Entregar item
                </p>

                <h3
                    class="
                        mt-1
                        truncate
                        font-serif
                        text-xl
                        font-black
                        text-[#53150f]
                    "
                    x-text="
                        selectedMember?.name
                        ?? ''
                    "
                ></h3>

                <p class="party-v4-give-modal__subtitle">
                    Escolha um item do inventário para transferir.
                </p>
            </header>

            <div
                class="
                    party-v4-give-modal__body
                    min-h-0
                    flex-1
                    overflow-y-auto
                    p-5
                "
            >
                <input
                    type="search"
                    x-model.debounce.200ms="
                        itemSearch
                    "
                    placeholder="Buscar item..."
                    class="
                        party-v4-give-search
                        w-full
                        rounded-xl
                        border
                        px-3
                        py-2.5
                        text-xs
                        focus:outline-none
                    "
                >

                <div
                    x-show="
                        filteredItems.length
                        ===
                        0
                    "
                    class="
                        mt-4
                        rounded-xl
                        border
                        border-dashed
                        border-[#cdbb9f]
                        px-4
                        py-8
                        text-center
                        text-[10px]
                        text-[#8c6239]
                    "
                >
                    Nenhum item disponível.
                </div>

                <div
                    x-show="
                        filteredMundaneItems.length
                        >
                        0
                    "
                    class="
                        mt-5
                    "
                >
                    <div
                        class="
                            mb-2
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <h4
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]
                                text-[#53150f]
                            "
                        >
                            Itens Mundanos
                        </h4>

                        <span
                            class="
                                h-px
                                flex-1
                                bg-[#cdbb9f]/65
                            "
                        ></span>

                        <span
                            class="
                                text-[8px]
                                font-bold
                                text-[#8c6239]
                            "
                            x-text="
                                filteredMundaneItems.length
                            "
                        ></span>
                    </div>

                    <div
                        class="
                            space-y-2
                        "
                    >
                        <template
                            x-for="
                                item
                                in
                                filteredMundaneItems
                            "
                            :key="
                                item.id
                            "
                        >

                            <button
                                type="button"
                                x-on:click="
                                    chooseItem(
                                        item
                                    )
                                "
                                class="
                                    party-v4-give-item
                                    flex
                                    w-full
                                    items-center
                                    gap-3
                                    rounded-xl
                                    border
                                    p-3
                                    text-left
                                    transition
                                "
                                :class="
                                    Number(
                                        transferItemId
                                    )
                                    ===
                                    Number(
                                        item.id
                                    )
                                        ? 'border-[#6b1d14] ring-2 ring-[#6b1d14]/10'
                                        : 'border-[#cdbb9f]/55'
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-11
                                        w-11
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/55
                                        bg-[#eadbc8]/45
                                    "
                                >
                                    <template
                                        x-if="
                                            item.image_url
                                        "
                                    >
                                        <img
                                            :src="
                                                item.image_url
                                            "
                                            :alt="
                                                item.name
                                            "
                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                            "
                                        >
                                    </template>

                                    <template
                                        x-if="
                                            !item.image_url
                                        "
                                    >
                                        <svg
                                            class="
                                                h-5
                                                w-5
                                                text-[#8c6239]
                                            "
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                d="M5 8.5h14v10H5v-10Z"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />

                                            <path
                                                d="M8 8.5V6.8A2.8 2.8 0 0 1 10.8 4h2.4A2.8 2.8 0 0 1 16 6.8v1.7"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </template>
                                </div>

                                <div
                                    class="
                                        min-w-0
                                        flex-1
                                    "
                                >
                                    <p
                                        class="
                                            truncate
                                            text-[11px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            item.name
                                        "
                                    ></p>

                                    <p
                                        class="
                                            mt-1
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            `×${item.quantity}${item.rarity_label ? ` · ${item.rarity_label}` : ''}`
                                        "
                                    ></p>
                                </div>

                                <div
                                    x-show="
                                        Number(
                                            transferItemId
                                        )
                                        ===
                                        Number(
                                            item.id
                                        )
                                    "
                                    x-cloak
                                    class="
                                        flex
                                        h-6
                                        w-6
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-[#6b1d14]
                                        text-[#fffaf2]
                                    "
                                >
                                    ✓
                                </div>
                            </button>

                        </template>
                    </div>
                </div>


                <div
                    x-show="
                        filteredWonderfulItems.length
                        >
                        0
                    "
                    class="
                        mt-6
                    "
                >
                    <div
                        class="
                            mb-2
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <h4
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]
                                text-[#53150f]
                            "
                        >
                            Itens Maravilhosos
                        </h4>

                        <span
                            class="
                                h-px
                                flex-1
                                bg-[#cdbb9f]/65
                            "
                        ></span>

                        <span
                            class="
                                text-[8px]
                                font-bold
                                text-[#8c6239]
                            "
                            x-text="
                                filteredWonderfulItems.length
                            "
                        ></span>
                    </div>

                    <div
                        class="
                            space-y-2
                        "
                    >
                        <template
                            x-for="
                                item
                                in
                                filteredWonderfulItems
                            "
                            :key="
                                item.id
                            "
                        >

                            <button
                                type="button"
                                x-on:click="
                                    chooseItem(
                                        item
                                    )
                                "
                                class="
                                    party-v4-give-item
                                    flex
                                    w-full
                                    items-center
                                    gap-3
                                    rounded-xl
                                    border
                                    p-3
                                    text-left
                                    transition
                                "
                                :class="
                                    Number(
                                        transferItemId
                                    )
                                    ===
                                    Number(
                                        item.id
                                    )
                                        ? 'border-[#6b1d14] ring-2 ring-[#6b1d14]/10'
                                        : 'border-[#cdbb9f]/55'
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-11
                                        w-11
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/55
                                        bg-[#eadbc8]/45
                                    "
                                >
                                    <template
                                        x-if="
                                            item.image_url
                                        "
                                    >
                                        <img
                                            :src="
                                                item.image_url
                                            "
                                            :alt="
                                                item.name
                                            "
                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                            "
                                        >
                                    </template>

                                    <template
                                        x-if="
                                            !item.image_url
                                        "
                                    >
                                        <svg
                                            class="
                                                h-5
                                                w-5
                                                text-[#8c6239]
                                            "
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                d="M5 8.5h14v10H5v-10Z"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />

                                            <path
                                                d="M8 8.5V6.8A2.8 2.8 0 0 1 10.8 4h2.4A2.8 2.8 0 0 1 16 6.8v1.7"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </template>
                                </div>

                                <div
                                    class="
                                        min-w-0
                                        flex-1
                                    "
                                >
                                    <p
                                        class="
                                            truncate
                                            text-[11px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            item.name
                                        "
                                    ></p>

                                    <p
                                        class="
                                            mt-1
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            `×${item.quantity}${item.rarity_label ? ` · ${item.rarity_label}` : ''}`
                                        "
                                    ></p>
                                </div>

                                <div
                                    x-show="
                                        Number(
                                            transferItemId
                                        )
                                        ===
                                        Number(
                                            item.id
                                        )
                                    "
                                    x-cloak
                                    class="
                                        flex
                                        h-6
                                        w-6
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-[#6b1d14]
                                        text-[#fffaf2]
                                    "
                                >
                                    ✓
                                </div>
                            </button>

                        </template>
                    </div>
                </div>


                <div
                    x-show="
                        filteredTechnologicalItems.length
                        >
                        0
                    "
                    class="
                        mt-6
                    "
                >
                    <div
                        class="
                            mb-2
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <h4
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]
                                text-[#53150f]
                            "
                        >
                            Itens Tecnológicos
                        </h4>

                        <span
                            class="
                                h-px
                                flex-1
                                bg-[#cdbb9f]/65
                            "
                        ></span>

                        <span
                            class="
                                text-[8px]
                                font-bold
                                text-[#8c6239]
                            "
                            x-text="
                                filteredTechnologicalItems.length
                            "
                        ></span>
                    </div>

                    <div
                        class="
                            space-y-2
                        "
                    >
                        <template
                            x-for="
                                item
                                in
                                filteredTechnologicalItems
                            "
                            :key="
                                item.id
                            "
                        >

                            <button
                                type="button"
                                x-on:click="
                                    chooseItem(
                                        item
                                    )
                                "
                                class="
                                    party-v4-give-item
                                    flex
                                    w-full
                                    items-center
                                    gap-3
                                    rounded-xl
                                    border
                                    p-3
                                    text-left
                                    transition
                                "
                                :class="
                                    Number(
                                        transferItemId
                                    )
                                    ===
                                    Number(
                                        item.id
                                    )
                                        ? 'border-[#6b1d14] ring-2 ring-[#6b1d14]/10'
                                        : 'border-[#cdbb9f]/55'
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-11
                                        w-11
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/55
                                        bg-[#eadbc8]/45
                                    "
                                >
                                    <template
                                        x-if="
                                            item.image_url
                                        "
                                    >
                                        <img
                                            :src="
                                                item.image_url
                                            "
                                            :alt="
                                                item.name
                                            "
                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                            "
                                        >
                                    </template>

                                    <template
                                        x-if="
                                            !item.image_url
                                        "
                                    >
                                        <svg
                                            class="
                                                h-5
                                                w-5
                                                text-[#8c6239]
                                            "
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                d="M5 8.5h14v10H5v-10Z"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />

                                            <path
                                                d="M8 8.5V6.8A2.8 2.8 0 0 1 10.8 4h2.4A2.8 2.8 0 0 1 16 6.8v1.7"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </template>
                                </div>

                                <div
                                    class="
                                        min-w-0
                                        flex-1
                                    "
                                >
                                    <p
                                        class="
                                            truncate
                                            text-[11px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            item.name
                                        "
                                    ></p>

                                    <p
                                        class="
                                            mt-1
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            `×${item.quantity}${item.rarity_label ? ` · ${item.rarity_label}` : ''}`
                                        "
                                    ></p>
                                </div>

                                <div
                                    x-show="
                                        Number(
                                            transferItemId
                                        )
                                        ===
                                        Number(
                                            item.id
                                        )
                                    "
                                    x-cloak
                                    class="
                                        flex
                                        h-6
                                        w-6
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-[#6b1d14]
                                        text-[#fffaf2]
                                    "
                                >
                                    ✓
                                </div>
                            </button>

                        </template>
                    </div>
                </div>

            </div>

            <footer
                class="
                    party-v4-give-modal__footer
                    border-t
                    px-5
                    py-4
                "
            >
                <div
                    x-show="
                        selectedItem
                    "
                    x-cloak
                    class="
                        flex
                        items-end
                        gap-3
                    "
                >
                    <label
                        class="
                            shrink-0
                        "
                    >
                        <span
                            class="
                                mb-1
                                block
                                text-[8px]
                                font-black
                                uppercase
                                tracking-wider
                                text-[#8c6239]
                            "
                        >
                            Qtd.
                        </span>

                        <input
                            type="number"
                            min="1"
                            :max="
                                selectedItem?.quantity
                                ?? 1
                            "
                            x-model.number="
                                transferQuantity
                            "
                            x-on:change="
                                normalizeTransferQuantity()
                            "
                            class="
                                party-v4-give-quantity
                                w-20
                                rounded-lg
                                border
                                px-3
                                py-2.5
                                text-center
                                text-sm
                                font-black
                                focus:outline-none
                            "
                        >
                    </label>

                    <button
                        type="button"
                        x-on:click="
                            transferItem()
                        "
                        :disabled="
                            transferring
                        "
                        class="
                            party-v4-give-submit
                            flex-1
                            rounded-lg
                            border
                            px-5
                            py-2.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-widest
                            transition
                            disabled:opacity-50
                        "
                    >
                        <span
                            x-show="
                                !transferring
                            "
                        >
                            Entregar
                        </span>

                        <span
                            x-show="
                                transferring
                            "
                            x-cloak
                        >
                            Entregando...
                        </span>
                    </button>
                </div>

                <p
                    x-show="
                        !selectedItem
                    "
                    class="
                        text-center
                        text-[9px]
                        text-[#8c6239]
                    "
                >
                    Selecione um item.
                </p>
            </footer>
        </section>
    </div>
</div>