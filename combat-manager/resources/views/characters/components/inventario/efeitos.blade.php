            /* ==============================================================
               WALLET PANEL
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel {
                right:
                    0;

                width:
                    min(
                        390px,
                        calc(100vw - 28px)
                    );

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    16px;

                background:
                    var(--theme-surface-raised) !important;

                box-shadow:
                    0 22px 60px rgba(0,0,0,.18),
                    0 2px 8px rgba(0,0,0,.04) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel-head {
                min-height:
                    72px;

                align-items:
                    flex-start;

                border-bottom:
                    1px solid
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;

                padding:
                    14px 15px 12px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-kicker {
                display:
                    block;

                margin-bottom:
                    3px;

                font-size:
                    8px !important;

                font-weight:
                    900;

                line-height:
                    1;

                letter-spacing:
                    .12em;

                text-transform:
                    uppercase;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel-head
            strong {
                display:
                    block;

                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    18px;

                line-height:
                    1.05;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-panel-head
            > div
            > span:last-child {
                display:
                    block;

                margin-top:
                    4px;

                font-size:
                    10px;

                line-height:
                    1.3;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-saving {
                min-height:
                    24px;

                display:
                    inline-flex;

                align-items:
                    center;

                border:
                    1px solid
                    var(--theme-border);

                border-radius:
                    999px;

                background:
                    var(--theme-surface-raised);

                padding:
                    0 8px;

                font-size:
                    8px;

                font-weight:
                    800;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary {
                display:
                    flex;

                align-items:
                    center;

                justify-content:
                    space-between;

                gap:
                    12px;

                margin:
                    12px 12px 0;

                border:
                    1px solid
                    var(--theme-border);

                border-radius:
                    12px;

                background:
                    var(--theme-surface-card-alt);

                padding:
                    10px 12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary
            > div {
                display:
                    flex;

                min-width:
                    0;

                flex-direction:
                    column;

                gap:
                    2px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary
            span {
                font-size:
                    9px;

                font-weight:
                    800;

                text-transform:
                    uppercase;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary
            strong {
                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    22px;

                line-height:
                    1;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-summary-note {
                text-transform:
                    none !important;

                letter-spacing:
                    0 !important;

                font-size:
                    10px !important;

                color:
                    var(--theme-text-secondary) !important;
            }


            /* ==============================================================
               COIN CARDS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coins {
                display:
                    grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap:
                    8px;

                padding:
                    10px 12px 12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card {
                display:
                    grid;

                grid-template-columns:
                    32px minmax(0, 1fr) 68px;

                align-items:
                    center;

                gap:
                    8px;

                min-height:
                    58px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    12px;

                background:
                    var(--theme-surface-card) !important;

                padding:
                    8px;

                transition:
                    border-color .16s ease,
                    background .16s ease,
                    box-shadow .16s ease;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 3px 9px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card--platinum {
                grid-column:
                    1 / -1;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-icon {
                width:
                    32px;

                height:
                    32px;

                border-radius:
                    10px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-name {
                display:
                    flex;

                min-width:
                    0;

                flex-direction:
                    column;

                gap:
                    2px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-name
            b {
                font-size:
                    10px;

                line-height:
                    1;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            .inventory-v14-coin-name
            small {
                font-size:
                    9px;

                line-height:
                    1;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            input {
                width:
                    68px;

                height:
                    34px;

                justify-self:
                    end;

                border:
                    1px solid
                    var(--theme-input-border) !important;

                border-radius:
                    9px;

                background:
                    var(--theme-input-bg) !important;

                padding:
                    0 8px;

                text-align:
                    right;

                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    13px;

                font-weight:
                    800;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-coin-card
            input:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 12%,
                        transparent
                    ) !important;
            }


            /* ==============================================================
               RESPONSIVO
            ============================================================== */

            @media (max-width: 680px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-toolbar {
                    align-items:
                        stretch;

                    flex-direction:
                        column;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-toolbar-right {
                    width:
                        100%;

                    margin-left:
                        0;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-wallet-wrap,
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-wallet-trigger {
                    width:
                        100%;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-wallet-panel {
                    left:
                        0;

                    right:
                        auto;

                    width:
                        min(
                            390px,
                            calc(100vw - 36px)
                        );
                }
            }

            @media (max-width: 460px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-actions {
                    display:
                        grid;

                    grid-template-columns:
                        1fr 1fr;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-primary-action {
                    grid-column:
                        1 / -1;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-coins {
                    grid-template-columns:
                        1fr;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v38-coin-card--platinum {
                    grid-column:
                        auto;
                }
            }

            /* ==============================================================
               SINTONIZAÇÃO
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v23-attunement-panel {
                margin-top:
                    14px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    16px;

                background:
                    var(--theme-module-background) !important;

                padding:
                    15px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-attunement {
                border-bottom:
                    0 !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-section-head {
                margin-bottom:
                    10px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-diamond {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-title-line
            h3 {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-section-title
            p {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-count {
                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-text-action {
                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    0 2px 8px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-image {
                border-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-copy
            small,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-copy
            > span,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-empty {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-slot-copy
            strong {
                color:
                    var(--theme-text-strong) !important;
            }



            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V36 — SINTONIZAÇÃO MAIS LIMPA
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-shell {
                margin-top:
                    14px;

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    18px;

                background:
                    linear-gradient(
                        180deg,
                        color-mix(
                            in srgb,
                            var(--theme-module-background) 94%,
                            white
                        ) 0%,
                        var(--theme-module-background) 100%
                    ) !important;

                padding:
                    12px !important;

                box-shadow:
                    0 2px 10px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-head {
                min-height:
                    auto;

                margin:
                    0 0 12px !important;

                border:
                    0 !important;

                border-radius:
                    14px;

                background:
                    var(--theme-band-background) !important;

                padding:
                    12px 14px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-title {
                gap:
                    12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-icon {
                width:
                    40px;

                height:
                    40px;

                flex-basis:
                    40px;

                border-radius:
                    12px;

                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 18%,
                        var(--theme-border)
                    ) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-accent-strong) !important;

                font-size:
                    14px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-copy {
                min-width:
                    0;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-line {
                gap:
                    10px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-line
            h3 {
                font-size:
                    18px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-count {
                min-height:
                    26px;

                padding:
                    0 10px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 14%,
                        var(--theme-border)
                    );

                background:
                    var(--theme-surface-card) !important;

                font-size:
                    12px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-copy
            p {
                margin-top:
                    3px;

                font-size:
                    12px !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-action {
                display:
                    inline-flex;

                min-height:
                    36px;

                align-items:
                    center;

                justify-content:
                    center;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    10px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    0 12px !important;

                font-size:
                    10px;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    0 2px 6px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-action:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slots {
                gap:
                    10px;

                padding:
                    0 !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot {
                min-height:
                    108px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    14px;

                background:
                    var(--theme-surface-card) !important;

                padding:
                    10px !important;

                box-shadow:
                    0 2px 8px rgba(0,0,0,.03);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-filled {
                background:
                    linear-gradient(
                        180deg,
                        var(--theme-surface-card) 0%,
                        color-mix(
                            in srgb,
                            var(--theme-surface-card) 90%,
                            var(--theme-band-background)
                        ) 100%
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-empty {
                border-style:
                    dashed;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 6px 16px rgba(0,0,0,.05);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-content {
                display:
                    grid;

                grid-template-columns:
                    54px minmax(0, 1fr);

                align-items:
                    center;

                gap:
                    12px;

                min-height:
                    100%;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-image {
                width:
                    54px !important;

                height:
                    54px !important;

                flex-basis:
                    54px;

                border-radius:
                    11px;

                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-copy {
                display:
                    grid;

                grid-template-rows:
                    auto
                    minmax(34px, auto)
                    auto;

                min-height:
                    64px;

                align-content:
                    stretch;

                align-items:
                    start;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-meta {
                display:
                    flex;

                align-items:
                    center;

                justify-content:
                    space-between;

                gap:
                    8px;

                margin-bottom:
                    5px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-copy
            small {
                margin:
                    0 !important;

                font-size:
                    9px !important;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-rarity {
                display:
                    inline-flex;

                align-items:
                    center;

                min-height:
                    20px;

                max-width:
                    120px;

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    999px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    0 8px;

                font-size:
                    9px;

                font-weight:
                    800;

                color:
                    var(--theme-text-secondary) !important;

                text-overflow:
                    ellipsis;

                white-space:
                    nowrap;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-copy
            strong {
                overflow:
                    hidden;

                display:
                    -webkit-box;

                min-height:
                    32px;

                -webkit-line-clamp:
                    2;

                -webkit-box-orient:
                    vertical;

                font-size:
                    14px !important;

                line-height:
                    1.15;

                white-space:
                    normal !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-hint {
                align-self:
                    end;

                margin-top:
                    6px;

                font-size:
                    10px;

                line-height:
                    1.1;

                color:
                    var(--theme-text-secondary) !important;
            }


            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-filled
            .inventory-v36-slot-image {
                align-self:
                    center;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-attunement-slot.is-filled
            .inventory-v36-slot-copy {
                height:
                    100%;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty {
                min-height:
                    70px;

                justify-content:
                    flex-start;

                gap:
                    10px;

                padding:
                    2px 2px 2px 1px;

                text-align:
                    left;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty
            .inventory-v14-slot-plus {
                display:
                    flex;

                width:
                    36px;

                height:
                    36px;

                flex:
                    0 0 36px;

                align-items:
                    center;

                justify-content:
                    center;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    11px;

                background:
                    var(--theme-surface-raised) !important;

                font-size:
                    18px;

                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty-copy {
                display:
                    flex;

                flex-direction:
                    column;

                gap:
                    3px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty-copy
            strong {
                font-size:
                    13px;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-slot-empty-copy
            small {
                font-size:
                    10px;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned {
                align-items:
                    center;

                gap:
                    7px;

                margin-top:
                    10px;

                border-top:
                    1px solid
                    var(--theme-divider);

                padding:
                    10px 2px 0 !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned
            > span {
                margin-right:
                    2px;

                font-size:
                    10px;

                letter-spacing:
                    .08em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned
            button {
                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    0 1px 4px rgba(0,0,0,.025);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v36-extra-attuned
            button:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            @media (max-width: 760px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-head {
                    align-items:
                        flex-start;

                    flex-direction:
                        column;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-action {
                    width:
                        100%;
                }
            }

            @media (max-width: 560px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-shell {
                    padding:
                        10px !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-attunement-head {
                    padding:
                        11px !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v36-slot-rarity {
                    max-width:
                        96px;
                }
            }

            /* ==============================================================
               GAVETAS / CATEGORIAS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v23-list {
                margin-top:
                    16px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-drawer {
                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    15px;

                background:
                    var(--theme-module-background) !important;

                box-shadow:
                    0 2px 10px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-drawer
            + .inventory-v24-group-drawer {
                margin-top:
                    10px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-trigger {
                min-height:
                    58px;

                margin:
                    0 !important;

                border:
                    0 !important;

                border-radius:
                    0 !important;

                padding:
                    11px 14px !important;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-trigger:hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-copy
            h3 {
                font-size:
                    18px !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-copy
            p {
                font-size:
                    11px !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-group-count {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-chevron {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v24-group-content {
                border-top:
                    1px solid
                    var(--theme-divider);

                background:
                    var(--theme-surface-card-alt) !important;

                padding:
                    12px;
            }


            /* ==============================================================
               BUSCA / ORDENAÇÃO
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-sort
            select {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search:focus-within,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-sort
            select:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 11%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search-input {
                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search-icon,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-search-clear,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v26-group-sort
            > span {
                color:
                    var(--theme-label) !important;
            }


            /* ==============================================================
               CARDS DE ITEM
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    0 2px 8px rgba(0,0,0,.04) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card::before {
                background:
                    var(--theme-border-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card.magical::before {
                background:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card:hover,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card:focus-within {
                transform:
                    translateY(-1px) !important;

                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 7px 20px rgba(0,0,0,.06) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v10-thumb {
                border-color:
                    var(--theme-border-soft) !important;

                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v27-equipped-badge {
                border-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 25%,
                        var(--theme-border)
                    ) !important;

                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card
            h4 {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card
            em,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-item-card
            p {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-strip {
                border-top-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-head,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-recovery,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-passive {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v22-card-feature-name {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker
            button {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker
            button:hover {
                background:
                    var(--theme-card-hover) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v11-mini-tracker
            strong {
                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               MODAIS — CONTRATO GLOBAL
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-modal {
                border-color:
                    var(
                        --theme-modal-border,
                        var(--theme-border)
                    ) !important;

                background:
                    var(
                        --theme-modal-surface,
                        var(--theme-paper-strong)
                    ) !important;

                color:
                    var(
                        --theme-modal-text,
                        var(--theme-text-primary)
                    ) !important;

                box-shadow:
                    var(
                        --theme-modal-shadow,
                        0 28px 80px rgba(0,0,0,.30)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal {
                width:
                    min(
                        860px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal {
                width:
                    min(
                        960px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal,
            .character-sheet-theme[data-theme]
            .inventory-v35-found-modal {
                width:
                    min(
                        560px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-warning-modal {
                width:
                    min(
                        500px,
                        calc(100vw - 28px)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            > div:first-child,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            > header:first-child {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            h3,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            h4,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            h5,
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            strong {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#6b1d14]"] {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#6b5548]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="text-[#92745c]"] {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="border-[#cdbb9f]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="border-[#d8c7ab]"] {
                border-color:
                    var(--theme-border) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#efe9dc]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#f7f0e6]"] {
                background:
                    var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#faf8f2]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-[#fffdf8]"],
            .character-sheet-theme[data-theme]
            .inventory-v35-modal
            [class*="bg-white"] {
                background:
                    var(--theme-surface-card) !important;
            }


            /* ==============================================================
               DETALHE
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v17-detail-topbar {
                min-height:
                    54px;

                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;

                padding:
                    10px 14px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v17-detail-scroll {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 30%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v15-detail-equip,
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v18-attunement-toggle {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v15-detail-equip.is-stowed,
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            .inventory-v18-attunement-toggle.is-unattuned {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            button[class*="bg-[#6b1d14]"] {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal
            button[class*="bg-[#6b1d14]"]:hover {
                background:
                    var(--theme-button-hover-background) !important;
            }


            /* ==============================================================
               EDITOR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            > div:first-child {
                min-height:
                    66px;

                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v32-editor-scroll {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-label) 30%,
                        transparent
                    )
                    transparent;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-input,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-select,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-textarea {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-input:focus,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-select:focus,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-textarea:focus {
                border-color:
                    var(--theme-input-focus) !important;

                box-shadow:
                    0 0 0 3px
                    color-mix(
                        in srgb,
                        var(--theme-input-focus) 12%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-choice,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-chip {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-choice:hover,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-chip:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-choice.active,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-chip.active {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v10-module,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v11-ability-card,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v11-detail-mechanics {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-card-alt) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v11-ability-header,
            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v34-defense-head {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer {
                border-color:
                    var(--theme-modal-border, var(--theme-border)) !important;

                background:
                    var(
                        --theme-modal-header-background,
                        var(--theme-band-background)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:nth-last-child(2) {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:nth-last-child(2):hover {
                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:last-child {
                border:
                    1px solid
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-editor-modal
            .inventory-v35-editor-footer
            > button:last-child:hover {
                background:
                    var(--theme-button-hover-background) !important;
            }


            /* ==============================================================
               DELETE CONFIRM
            ============================================================== */

            .inventory-v35-delete-layer {
                background:
                    rgba(8,8,8,.60);

                -webkit-backdrop-filter:
                    blur(6px);

                backdrop-filter:
                    blur(6px);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-delete-modal {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-delete-modal
            > div:first-child {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }


            /* ==============================================================
               PICKER / WARNING / FOUND
            ============================================================== */

            .character-sheet-theme[data-theme]
            :is(
                .inventory-v35-picker-modal,
                .inventory-v35-warning-modal,
                .inventory-v35-found-modal
            )
            > div:first-child {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal
            > div:nth-child(2) {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal
            button[class*="border-b"] {
                border-color:
                    var(--theme-divider) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-picker-modal
            button[class*="border-b"]:hover {
                background:
                    var(--theme-card-hover) !important;
            }


            /* ==============================================================
               EQUIPADOS
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal {
                position:
                    relative;

                z-index:
                    10;

                width:
                    min(
                        620px,
                        calc(100vw - 28px)
                    ) !important;

                max-height:
                    min(
                        82dvh,
                        760px
                    );

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-modal-border, var(--theme-border)) !important;

                border-radius:
                    18px;

                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                box-shadow:
                    var(
                        --theme-modal-shadow,
                        0 28px 80px rgba(0,0,0,.30)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-header {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-kicker,
            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-hint {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-title-row
            h3 {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-count {
                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-list-wrap {
                background:
                    var(--theme-modal-surface, var(--theme-paper-strong)) !important;

                scrollbar-width:
                    thin;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-row {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-equipped-modal
            .inventory-v29-equipped-row:hover {
                background:
                    var(--theme-card-hover) !important;
            }


            /* ==============================================================
               TOOLTIP
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-floating-tooltip {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 16px 42px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35-floating-tooltip
            strong {
                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               TOAST
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35-toast,
            .inventory-v35
            .inventory-v35-toast {
                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 18px 46px rgba(0,0,0,.20) !important;
            }


            /* ==============================================================
               RESPONSIVO
            ============================================================== */

            @media (max-width: 760px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v23-attunement-panel {
                    padding:
                        12px !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v24-group-content {
                    padding:
                        9px;
                }
            }

            @media (max-width: 560px) {
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v23-main-topbar {
                    align-items:
                        stretch;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v14-toolbar-right {
                    justify-content:
                        stretch !important;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v14-wallet-wrap,
                .character-sheet-theme[data-theme]
                .inventory-v35
                .inventory-v14-wallet-trigger {
                    width:
                        100%;
                }

                .character-sheet-theme[data-theme]
                .inventory-v35-modal {
                    width:
                        calc(100vw - 18px) !important;

                    border-radius:
                        15px !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .inventory-v35
                *,
                .inventory-v35-modal-layer
                * {
                    scroll-behavior:
                        auto !important;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V40 — MIDNIGHT REFINADO
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-toolbar {
                border-color: rgba(151,162,173,.24) !important;
                background: linear-gradient(180deg,#22282e 0%,#1d2329 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025),0 3px 10px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-primary-action {
                border-color: #5c7891 !important;
                background: #4d6d88 !important;
                color: #f7fafc !important;
                box-shadow: 0 3px 10px rgba(0,0,0,.16) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-primary-action:hover {
                background: #5a7893 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-secondary-action,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-trigger {
                border-color: rgba(151,162,173,.25) !important;
                background: #252c33 !important;
                color: #e5e8eb !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-secondary-action:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-trigger:hover {
                border-color: rgba(154,170,185,.38) !important;
                background: #2d353d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-shell {
                border-color: rgba(151,162,173,.24) !important;
                background: linear-gradient(180deg,#20262c 0%,#1b2026 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025),0 3px 10px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-head {
                border: 1px solid rgba(151,162,173,.16) !important;
                background: linear-gradient(180deg,#2b3239 0%,#272e35 100%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-icon {
                border-color: rgba(111,140,166,.28) !important;
                background: #222a31 !important;
                color: #83a0b8 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-count,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-action {
                border-color: rgba(151,162,173,.22) !important;
                background: #252c33 !important;
                color: #e4e8eb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-action:hover {
                background: #2d353d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-slot {
                border-color: rgba(151,162,173,.24) !important;
                background: #20262c !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018),0 2px 7px rgba(0,0,0,.15) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-slot.is-filled {
                background: linear-gradient(180deg,#22292f 0%,#1d2329 100%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-attunement-slot:hover {
                border-color: rgba(126,151,173,.45) !important;
                background: #293138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-slot-image {
                border-color: rgba(151,162,173,.20) !important;
                background: #171c21 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v36-slot-rarity {
                border-color: rgba(151,162,173,.20) !important;
                background: #272e35 !important;
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-drawer {
                border-color: rgba(151,162,173,.24) !important;
                background: #1d2328 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.02),0 4px 12px rgba(0,0,0,.20) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-trigger {
                background: linear-gradient(180deg,#242b32 0%,#20272d 100%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-trigger:hover {
                background: #2a3239 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v24-group-content {
                border-color: rgba(151,162,173,.15) !important;
                background: #181e23 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v14-group-count {
                border-color: rgba(151,162,173,.22) !important;
                background: #2a3138 !important;
                color: #bbc2c8 !important;
            }

            /* Corrige a faixa clara herdada da V28. */
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-tools {
                border-bottom: 1px solid rgba(151,162,173,.14) !important;
                background: #252b31 !important;
                padding: 8px 10px !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search {
                border: 0 !important;
                background: transparent !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-input,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-sort select {
                border: 1px solid #46525d !important;
                background: #171c21 !important;
                color: #e9edf0 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-input::placeholder {
                color: #7f8992 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-icon,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search-clear,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-sort > span {
                color: #929da6 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-search:focus-within .inventory-v26-group-search-input,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v26-group-sort select:focus {
                border-color: #718ea8 !important;
                box-shadow: 0 0 0 3px rgba(111,140,166,.12) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-item-card {
                border-color: rgba(151,162,173,.24) !important;
                background: linear-gradient(180deg,#22282e 0%,#1e242a 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018),0 4px 12px rgba(0,0,0,.20) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-item-card:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-item-card:focus-within {
                border-color: rgba(126,151,173,.45) !important;
                background: linear-gradient(180deg,#293139 0%,#232a31 100%) !important;
                box-shadow: 0 10px 24px rgba(0,0,0,.28) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-card-thumb {
                border-color: rgba(151,162,173,.18) !important;
                background: radial-gradient(circle at 50% 42%,#232a31 0%,#171c21 72%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.022) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-card-copy h4 {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-card-copy em {
                color: #9ba5ae !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v39-feature-strip {
                border-top-color: rgba(151,162,173,.14) !important;
                background: #1a2025 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v22-card-feature-row {
                border-color: rgba(151,162,173,.16) !important;
                background: #22282e !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v22-card-feature-row:hover {
                background: #293138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v27-equipped-badge {
                border-color: rgba(151,162,173,.28) !important;
                background: rgba(36,44,51,.94) !important;
                color: #d9dde1 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v27-equipped-badge::before {
                background: #b85f5a !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-panel {
                border-color: rgba(151,162,173,.26) !important;
                background: #20262c !important;
                box-shadow: 0 24px 64px rgba(0,0,0,.34) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-panel-head {
                border-color: rgba(151,162,173,.15) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-wallet-summary {
                border-color: rgba(151,162,173,.20) !important;
                background: #1a2025 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-coin-card {
                border-color: rgba(151,162,173,.18) !important;
                background: #252b31 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-coin-card:hover {
                background: #2c343b !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v40
            .inventory-v38-coin-card input {
                border-color: #46525d !important;
                background: #171c21 !important;
                color: #eef1f3 !important;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V41 — MIDNIGHT / MODAIS E THUMBNAILS
            |--------------------------------------------------------------------------
            |
            | Corrige os últimos blocos claros que ainda vinham das versões antigas:
            | detalhe do item, habilidades, trackers, thumbnails de Equipados e slots.
            | Esta camada atua SOMENTE no tema Midnight.
            |
            */

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal {
                border-color: rgba(151,162,173,.28) !important;
                background: #20262c !important;
                color: #d9dde1 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-detail-topbar {
                border-color: rgba(151,162,173,.16) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-detail-scroll {
                background: #20262c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-thumb {
                border-color: rgba(151,162,173,.24) !important;
                background: radial-gradient(circle at 50% 42%, #262d34 0%, #171c21 76%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.025), 0 6px 18px rgba(0,0,0,.22) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-thumb img {
                background: transparent !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-item-kicker {
                color: #91a0ad !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-item-title {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-classification {
                color: #9ca6ae !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-status-badge {
                border-color: rgba(184,95,90,.24) !important;
                background: rgba(184,95,90,.08) !important;
                color: #d99994 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-status-dot {
                background: #c76863 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal p,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#4f3427]"],
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#5f3a27]"],
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#6b5548]"] {
                color: #c5cbd0 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#6b1d14]"] {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="text-[#8c6239]"] {
                color: #a7b0b8 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-detail-section-title {
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-detail-section-title::after {
                background: linear-gradient(90deg, rgba(157,170,182,.30), transparent) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-card {
                border-color: rgba(151,162,173,.22) !important;
                background: #1d2328 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018), 0 3px 10px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-header {
                border-bottom-color: rgba(151,162,173,.14) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-header h3 {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-ability-body {
                background: #20262c !important;
                color: #c8ced3 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker {
                border-color: rgba(151,162,173,.24) !important;
                background: #171c21 !important;
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker button,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker button {
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker button:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker button:hover {
                background: #2d353d !important;
                color: #eef1f3 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v10-tracker strong,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-mini-tracker strong {
                color: #eef1f3 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-recovery-pill {
                background: #2a3138 !important;
                color: #aab3bb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v11-detail-mechanics {
                border-color: rgba(151,162,173,.20) !important;
                background: #1a2025 !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal [class*="bg-[#efe9dc]"] {
                border-color: rgba(151,162,173,.18) !important;
                background: #242b31 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement {
                border-color: rgba(151,162,173,.22) !important;
                background: linear-gradient(180deg, #272e35 0%, #22282e 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.018) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-icon {
                border-color: rgba(151,162,173,.22) !important;
                background: #171c21 !important;
                color: #83a0b8 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-kicker {
                color: #91a0ad !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-copy strong {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-detail-modal .inventory-v17-attunement-copy p {
                color: #a4adb5 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal {
                border-color: rgba(151,162,173,.28) !important;
                background: #20262c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-header {
                border-color: rgba(151,162,173,.16) !important;
                background: #2a3138 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-list-wrap {
                background: #20262c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row {
                border-color: rgba(151,162,173,.20) !important;
                background: #252c33 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row:hover,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row:focus-visible {
                border-color: rgba(126,151,173,.42) !important;
                background: #2d353d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-thumb {
                border-color: rgba(151,162,173,.22) !important;
                background: radial-gradient(circle at 50% 42%, #262d34 0%, #171c21 78%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-thumb > span {
                color: #71808c !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-copy strong {
                color: #f1efeb !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-copy span {
                color: #9ca6ae !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-chevron {
                color: #73818d !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v35-equipped-modal .inventory-v29-equipped-row:hover .inventory-v29-equipped-chevron {
                color: #8faac0 !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v41 .inventory-v36-slot-image,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v41 .inventory-v14-slot-image {
                border-color: rgba(151,162,173,.20) !important;
                background: radial-gradient(circle at 50% 45%, #252c33 0%, #171c21 78%) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v41 .inventory-v36-slot-rarity {
                border-color: rgba(151,162,173,.20) !important;
                background: #293138 !important;
                color: #aab3bb !important;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTORY V42 — DARK THEMES CLEANUP
            |--------------------------------------------------------------------------
            */

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-attunement-shell,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-shell,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-attunement-slot,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-slot,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v39-item-card,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v29-equipped-row,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-card,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-card,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v10-thumb,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v10-thumb,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v29-equipped-thumb,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-thumb,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-slot-image,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-slot-image {
                box-shadow: 0 4px 12px rgba(0,0,0,.18) !important;
            }

            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v36-attunement-head,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-head,
            .character-sheet-theme[data-theme="midnight"]
            .inventory-v42
            .inventory-v24-group-trigger,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v24-group-trigger {
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-shell {
                background: linear-gradient(180deg, #241d1a 0%, #1d1816 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-head {
                background: linear-gradient(180deg, #2d2522 0%, #261f1c 100%) !important;
                border: 1px solid rgba(154,122,102,.15) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-slot {
                border-color: rgba(154,122,102,.22) !important;
                background: #221c19 !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-attunement-slot.is-filled {
                background: linear-gradient(180deg, #27201d 0%, #211b18 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v36-slot-image,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v14-slot-image,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-thumb,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v10-thumb {
                border-color: rgba(154,122,102,.18) !important;
                background: radial-gradient(circle at 50% 45%, #2a221f 0%, #171311 78%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row {
                border-color: rgba(154,122,102,.22) !important;
                background: linear-gradient(180deg, #27201d 0%, #211b18 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card:hover,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v39-item-card:focus-within,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row:hover,
            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v29-equipped-row:focus-visible {
                background: linear-gradient(180deg, #2d2522 0%, #261f1c 100%) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-card {
                border-color: rgba(154,122,102,.18) !important;
                background: #211b18 !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-header {
                background: #2d2522 !important;
                border-bottom-color: rgba(154,122,102,.14) !important;
            }

            .character-sheet-theme[data-theme="obsidian"]
            .inventory-v42
            .inventory-v35-detail-modal
            .inventory-v11-ability-body {
                background: #241d1a !important;
            }



            /* ============================================================
               INVENTÁRIO V43 — ITENS DE USO ÚNICO
            ============================================================ */

            .inventory-v43-item-used {
                border-color: rgba(112,106,98,.42) !important;
                background: linear-gradient(135deg, rgba(232,230,225,.94), rgba(217,214,208,.88)) !important;
                box-shadow: none !important;
            }

            .inventory-v43-item-used::before {
                background: rgba(92,88,82,.54) !important;
            }

            .inventory-v43-item-used .inventory-v39-card-main,
            .inventory-v43-item-used .inventory-v39-feature-strip {
                opacity: .76;
            }

            .inventory-v43-item-used .inventory-v39-card-thumb img {
                filter: grayscale(1);
                opacity: .62;
            }

            .inventory-v43-used-badge {
                position: absolute;
                right: 6px;
                bottom: 6px;
                z-index: 4;
                display: inline-flex;
                min-height: 22px;
                align-items: center;
                border: 1px solid rgba(91,86,80,.34);
                border-radius: 999px;
                background: rgba(74,71,67,.88);
                padding: 0 7px;
                color: #fff;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .075em;
                text-transform: uppercase;
                box-shadow: 0 3px 8px rgba(31,29,27,.16);
            }

            .inventory-v43-used-bar {
                display: flex;
                min-height: 48px;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border-top: 1px solid rgba(112,106,98,.28);
                background: rgba(112,106,98,.10);
                padding: 8px 11px 8px 13px;
            }

            .inventory-v43-used-copy {
                min-width: 0;
            }

            .inventory-v43-used-copy strong {
                display: block;
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                color: #595550;
            }

            .inventory-v43-used-copy span {
                display: block;
                margin-top: 1px;
                font-size: 9px;
                font-weight: 700;
                color: #7b756d;
            }

            .inventory-v43-discard-action,
            .inventory-v43-single-use-action {
                display: inline-flex;
                min-height: 30px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(107,29,20,.25);
                border-radius: 9px;
                background: rgba(255,253,249,.88);
                padding: 0 10px;
                color: #6b1d14;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .035em;
                text-transform: uppercase;
                transition: background .14s ease, border-color .14s ease, color .14s ease;
            }

            .inventory-v43-discard-action:hover,
            .inventory-v43-single-use-action:hover {
                border-color: rgba(107,29,20,.48);
                background: #6b1d14;
                color: #faf8f2;
            }

            .inventory-v43-single-use-action:disabled,
            .inventory-v43-discard-action:disabled {
                cursor: wait;
                opacity: .48;
            }

            .inventory-v43-detail-used {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-top: 16px;
                border: 1px solid rgba(112,106,98,.30);
                border-radius: 12px;
                background: rgba(112,106,98,.085);
                padding: 10px 12px;
            }

            .inventory-v43-detail-used strong {
                display: block;
                font-family: Georgia, serif;
                font-size: 14px;
                color: #595550;
            }

            .inventory-v43-detail-used p {
                margin-top: 2px;
                font-size: 10px;
                line-height: 1.45;
                color: #77716a;
            }

            .inventory-v43-single-use-note {
                margin-top: 10px;
                border-left: 2px solid rgba(107,29,20,.46);
                padding: 7px 0 7px 10px;
                color: #6b5548;
                font-size: 10px;
                line-height: 1.5;
            }



            /* ===============================================================
               INVENTÁRIO V44 — AUDITORIA DE TEMAS / LEGIBILIDADE
               O modal de itens é teleportado para <body>, fora de .inventory-v43.
               Por isso as regras são ancoradas no próprio modal e no
               data-theme copiado para o portal, cobrindo os oito temas.
               =============================================================== */

            /* Largura ligeiramente maior. Respeita o viewport e não altera
               os modais de edição, escolha ou confirmação. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal {
                width: min(940px, calc(100vw - 28px)) !important;
                max-width: 940px !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-detail-topbar {
                box-shadow: none !important;
                background-image: none !important;
                background-color: var(--theme-band) !important;
            }

            /* Nome, classificação, descrição e rótulos jamais ficam vinho
               escuro sobre uma superfície escura. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-item-title,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-attunement-copy strong,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-ability-header h3,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v21-tooltip-title {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-classification,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v20-weapon-line,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v20-detail-line,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-ability-body,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v21-tooltip-text,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-attunement-copy p {
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-item-kicker,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-detail-section-title,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v20-detail-line strong,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-attunement-kicker,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-detail-topbar > span:first-child {
                color: var(--theme-label) !important;
            }

            /* Classes text-[cor fixa] herdadas das versões V10–V20. Escopo
               restrito aos modais do inventário, sem alterar botões de ação
               ou avisos vermelhos de perigo. */
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#4f3427]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#5f3a27]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#5f4638]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#7c6657]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#6b5548]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#76553f]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#7d604d]"] {
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#92745c]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#a07855]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="text-[#9a795b]"] {
                color: var(--theme-text-secondary) !important;
            }

            /* A caixa de propriedades e as habilidades estavam com papel
               branco/bege e texto branco nos temas escuros. Unificar as duas
               superfícies elimina esse contraste invertido. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-ability-card,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-detail-mechanics,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-attunement {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card-alt) !important;
                color: var(--theme-text-primary) !important;
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-ability-header {
                border-bottom-color: var(--theme-divider) !important;
                background: var(--theme-surface-raised) !important;
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-ability-body {
                background: var(--theme-surface-card-alt) !important;
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-ability-body p,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-ability-body [class*="text-[#4f3427]"] {
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-detail-section-title::after {
                background: linear-gradient(90deg, var(--theme-divider), transparent) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v10-thumb {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
                box-shadow: 0 6px 18px rgba(0,0,0,.10) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-status-badge {
                border-color: var(--theme-border) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-status-dot {
                background: var(--theme-accent) !important;
            }

            /* Trackers, recuperação, mecânicas, sintonização e tooltips. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v10-tracker,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-mini-tracker,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v21-tooltip-panel,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-attunement-icon {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
                color: var(--theme-text-strong) !important;
                box-shadow: none !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v10-tracker button,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-mini-tracker button,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v10-tracker strong,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-mini-tracker strong {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v10-tracker button:hover,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-mini-tracker button:hover {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v11-recovery-pill,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-attunement-action.is-secondary {
                border-color: var(--theme-border) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }

            /* Corrige também as superfícies auxiliares teletransportadas:
               editor, itens encontrados, equipados e confirmações. */
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#efe9dc]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#f4f1e8]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#f7f0e6]"] {
                background: var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#faf8f2]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#fffdf9]"],
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#fffdf8]"] {
                background: var(--theme-surface-card) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-modal .inventory-v17-attunement-action.is-primary,
            .character-sheet-theme[data-theme] .inventory-v35-modal button[class*="bg-[#6b1d14]"] {
                border-color: var(--theme-accent-strong) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#6b1d14]"] strong,
            .character-sheet-theme[data-theme] .inventory-v35-modal [class*="bg-[#6b1d14]"] span {
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-modal input:not([type="checkbox"]),
            .character-sheet-theme[data-theme] .inventory-v35-modal textarea,
            .character-sheet-theme[data-theme] .inventory-v35-modal select {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-modal input::placeholder,
            .character-sheet-theme[data-theme] .inventory-v35-modal textarea::placeholder {
                color: var(--theme-input-placeholder) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35-modal input:focus,
            .character-sheet-theme[data-theme] .inventory-v35-modal textarea:focus,
            .character-sheet-theme[data-theme] .inventory-v35-modal select:focus {
                border-color: var(--theme-input-focus) !important;
            }

            /* Estados de alerta e itens já usados: neutros, mas legíveis nos
               temas claros/escuros. Nunca transforma alerta em cor de ação. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v43-detail-used {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
                color: var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v43-detail-used p {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v43-item-used {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card-alt) !important;
                box-shadow: none !important;
            }

            @media (max-width: 640px) {
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal {
                    width: calc(100vw - 20px) !important;
                    max-width: calc(100vw - 20px) !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-detail-scroll {
                    padding-inline: 16px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-item-title {
                    font-size: clamp(22px, 6vw, 30px) !important;
                    overflow-wrap: anywhere;
                }
            }


            /* ==============================================================
               INVENTÁRIO V45 — SUPERFÍCIES, CONTROLES E PREVISÃO DE HABILIDADES
               Mantém dados, consumo, equipamento e Alpine da V44.
               ============================================================= */

            /* A V44 alterava a div da busca, mas deixava o input branco.
               Corrigir o elemento real e o seletor nativo de pesquisa. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-tools {
                border-bottom: 1px solid var(--theme-divider) !important;
                background: var(--theme-surface-card-alt) !important;
                padding: 10px 12px !important;
                gap: 10px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search {
                border: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search-input,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-sort select {
                min-height: 37px !important;
                border: 1px solid var(--theme-input-border) !important;
                border-radius: 9px !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
                box-shadow: none !important;
                font-size: 11px !important;
                font-weight: 650 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search-input {
                padding-inline: 33px !important;
                -webkit-appearance: none;
                appearance: none;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search-input::placeholder {
                color: var(--theme-input-placeholder) !important;
                opacity: 1 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search-input::-webkit-search-cancel-button {
                -webkit-appearance: none;
                appearance: none;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-sort {
                min-width: 0;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-sort > span {
                color: var(--theme-label) !important;
                font-size: 9px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search-input:focus,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-sort select:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-input-focus) 12%, transparent) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search-clear {
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-search-clear:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-empty,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-empty strong {
                color: var(--theme-text-secondary) !important;
            }

            /* Chevron / fechar gaveta: mesmo comportamento nos oito temas. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v24-group-chevron {
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v24-group-trigger:hover .inventory-v24-group-chevron,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v24-group-drawer.is-open .inventory-v24-group-chevron {
                border-color: var(--theme-border-strong) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v24-group-chevron svg {
                stroke: currentColor !important;
            }

            /* Cartões de inventário: menos enfeites, mais espaço para habilidades. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v39-item-card {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
                border-radius: 13px !important;
                box-shadow: 0 3px 12px rgba(0,0,0,.065) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v39-item-card:hover,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v39-item-card:focus-within {
                border-color: var(--theme-border-strong) !important;
                background: var(--theme-card-hover) !important;
                transform: translateY(-1px) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v39-feature-strip {
                border-top: 1px solid var(--theme-divider) !important;
                background: var(--theme-surface-card-alt) !important;
                padding: 10px 10px 12px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-head {
                margin-bottom: 7px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-list {
                display: flex;
                flex-direction: column !important;
                gap: 7px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-row {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                justify-content: flex-start !important;
                min-height: 0 !important;
                padding: 8px !important;
                gap: 6px !important;
                border: 1px solid var(--theme-border-soft) !important;
                border-radius: 9px !important;
                background: var(--theme-surface-card) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-row:hover {
                background: var(--theme-card-hover) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v45-feature-copy {
                min-width: 0;
                width: 100%;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-name {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
                overflow: visible !important;
                text-overflow: clip !important;
                max-width: none !important;
                line-height: 1.3 !important;
                font-size: 11px !important;
                font-weight: 850 !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v45-feature-description {
                display: -webkit-box;
                width: 100%;
                margin-top: 3px;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                line-clamp: 2;
                overflow-wrap: anywhere;
                font-size: 9px;
                font-weight: 450;
                line-height: 1.45;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-tracker {
                display: flex !important;
                width: 100% !important;
                min-width: 0 !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                justify-content: flex-start !important;
                gap: 6px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-recovery {
                max-width: none !important;
                width: auto !important;
                font-size: 9px !important;
                line-height: 1.2 !important;
                white-space: normal !important;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-passive {
                min-height: 22px !important;
                display: inline-flex !important;
                align-items: center !important;
                width: fit-content !important;
                padding: 3px 8px !important;
                font-size: 9px !important;
                line-height: 1.1 !important;
                border: 1px solid var(--theme-border-soft) !important;
                border-radius: 7px !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-more {
                min-height: 21px !important;
                font-size: 9px !important;
                line-height: 1.3 !important;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v43-single-use-action,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v43-single-use-action {
                min-height: 27px !important;
                max-width: 100%;
                border: 1px solid var(--theme-accent-strong) !important;
                border-radius: 7px !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
                padding: 3px 10px !important;
                font-size: 9px !important;
                font-weight: 850 !important;
                white-space: normal !important;
                text-align: center;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v43-single-use-action:hover,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v43-single-use-action:hover {
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v11-mini-tracker {
                flex: 0 0 auto;
                background: var(--theme-surface-inset) !important;
                border-color: var(--theme-border) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-row .inventory-v11-mini-tracker button {
                width: 23px !important;
                height: 26px !important;
                font-size: 12px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v22-card-feature-row .inventory-v11-mini-tracker strong {
                min-width: 38px !important;
                font-size: 10px !important;
            }

            /* Editor: corrige a classe legada .inventory-v10-feature-card,
               que permaneceu clara apesar de o modal obedecer ao tema. */
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card,
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-module,
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v31-tracker-editor {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-card-alt) !important;
                color: var(--theme-text-primary) !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card > div:first-child,
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-module > div:first-child {
                border-bottom-color: var(--theme-divider) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card > div:not(:first-child) {
                background: var(--theme-surface-card-alt) !important;
                color: var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal label > span,
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card strong,
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card [class*="text-[#53150f]"] {
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card [class*="text-[#8c6239]"] {
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card .inventory-v10-input,
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card .inventory-v10-textarea,
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v10-feature-card .inventory-v10-select {
                border: 1px solid var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v45-editor-close {
                flex: 0 0 36px;
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v45-editor-close:hover {
                border-color: var(--theme-border-strong) !important;
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-editor-modal .inventory-v35-editor-footer {
                margin: 0 !important;
                border-color: var(--theme-divider) !important;
                background: var(--theme-modal-header-background) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v45-detail-close,
            .character-sheet-theme[data-theme] .inventory-v35-modal .inventory-v14-close {
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v45-detail-close:hover,
            .character-sheet-theme[data-theme] .inventory-v35-modal .inventory-v14-close:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            @@media (max-width: 680px) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-tools {
                    grid-template-columns: minmax(0, 1fr) !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-sort {
                    grid-template-columns: auto minmax(0, 1fr);
                }
            }


            /* =====================================================================
               V48 — CARDS COMPACTOS APROVADOS NO PROTÓTIPO V3
               Fotografia à esquerda, identificação à direita, até duas
               habilidades e consumo rápido. Altura invariável por item.
               ===================================================================== */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v39-items-grid {
                display: grid !important;
                grid-template-columns: repeat(auto-fill, minmax(min(100%, 304px), 1fr)) !important;
                align-items: stretch !important;
                gap: 12px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card * {
                box-sizing: border-box;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card {
                display: flex !important;
                flex-direction: column !important;
                width: 100% !important;
                height: 222px !important;
                min-height: 222px !important;
                max-height: 222px !important;
                overflow: hidden !important;
                border: 1px solid var(--theme-border) !important;
                border-radius: 14px !important;
                background: var(--theme-surface-card) !important;
                box-shadow: none !important;
                color: var(--theme-text-primary) !important;
                transition: background .15s ease, border-color .15s ease, box-shadow .15s ease !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card:hover,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card:focus-visible {
                transform: none !important;
                border-color: var(--theme-border-strong) !important;
                background: var(--theme-card-hover) !important;
                box-shadow: 0 5px 15px rgba(0,0,0,.08) !important;
                outline: none;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card::before {
                content: none !important;
                display: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-hero {
                display: flex !important;
                flex: 0 0 121px !important;
                height: 121px !important;
                min-height: 0 !important;
                gap: 11px !important;
                padding: 11px 11px 8px !important;
                align-items: flex-start !important;
                margin: 0 !important;
                overflow: hidden !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art-col {
                display: flex;
                flex: 0 0 78px;
                width: 78px;
                min-width: 78px;
                flex-direction: column;
                gap: 6px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art {
                display: flex !important;
                position: relative;
                width: 78px !important;
                height: 78px !important;
                min-width: 78px !important;
                min-height: 78px !important;
                max-width: 78px !important;
                max-height: 78px !important;
                flex: 0 0 78px !important;
                overflow: hidden;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--theme-border-soft) !important;
                border-radius: 9px !important;
                background: var(--theme-surface-inset) !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art img {
                display: block;
                width: 100% !important;
                height: 100% !important;
                object-fit: contain !important;
                padding: 2px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art .inventory-v43-used-badge {
                right: 4px;
                bottom: 4px;
                min-height: 18px;
                padding: 0 5px;
                font-size: 7px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-equip {
                display: inline-flex;
                width: 78px;
                min-height: 22px;
                align-items: center;
                justify-content: center;
                gap: 4px;
                border: 1px solid var(--theme-border-strong) !important;
                border-radius: 6px;
                padding: 2px 4px;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
                font-size: 8px;
                font-weight: 800;
                letter-spacing: .01em;
                white-space: nowrap;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-equip:hover,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-equip:focus-visible {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-equip.is-equipped {
                border-color: var(--theme-accent) !important;
                color: var(--theme-text-strong) !important;
                background: var(--theme-accent-softer) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-equip:disabled {
                cursor: wait;
                opacity: .55;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-identity {
                display: flex !important;
                min-width: 0 !important;
                min-height: 0 !important;
                flex: 1 1 auto !important;
                flex-direction: column !important;
                align-items: flex-start;
                gap: 4px;
                margin: 0;
                padding: 0;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-name,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-classification,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-technical {
                margin: 0 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-name {
                display: -webkit-box !important;
                width: 100% !important;
                max-height: 40px !important;
                overflow: hidden !important;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow-wrap: anywhere;
                color: var(--theme-text-strong) !important;
                font-family: Georgia, 'Times New Roman', serif;
                font-weight: 900;
                font-size: 15px !important;
                line-height: 1.28 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-classification {
                display: -webkit-box !important;
                max-height: 31px !important;
                overflow: hidden !important;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                color: var(--theme-text-secondary) !important;
                font-size: 10px !important;
                line-height: 1.4 !important;
                font-style: italic;
                overflow-wrap: anywhere;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-technical {
                max-height: 15px;
                overflow: hidden;
                color: var(--theme-text-secondary) !important;
                font-size: 9px;
                line-height: 15px;
                white-space: nowrap;
                text-overflow: ellipsis;
                opacity: .82;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-abilities {
                display: flex !important;
                flex: 1 1 auto !important;
                min-height: 0 !important;
                flex-direction: column !important;
                gap: 4px;
                overflow: hidden !important;
                border-top: 1px solid var(--theme-border-soft) !important;
                background: transparent !important;
                padding: 7px 11px 8px !important;
                margin: 0 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-abilities-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex: 0 0 12px;
                min-height: 12px;
                gap: 5px;
                color: var(--theme-label) !important;
                font-size: 8px;
                letter-spacing: .09em;
                font-weight: 900;
                text-transform: uppercase;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-skill-list {
                display: flex;
                flex-direction: column;
                min-height: 0;
                gap: 3px;
                overflow: hidden;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-skill {
                display: flex;
                flex: 0 0 25px;
                min-height: 25px;
                min-width: 0;
                align-items: center;
                justify-content: space-between;
                gap: 7px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-skill-name {
                display: block;
                min-width: 0;
                flex: 1 1 auto;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 11px;
                font-weight: 800;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-passive {
                flex: 0 0 auto;
                font-size: 9px;
                color: var(--theme-text-secondary) !important;
                opacity: .78;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-tracker {
                display: flex;
                flex: 0 0 auto;
                max-width: 125px;
                min-width: 0;
                align-items: center;
                gap: 3px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-tracker .inventory-v11-mini-tracker {
                display: flex;
                height: 23px;
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-tracker .inventory-v11-mini-tracker button {
                width: 22px !important;
                height: 21px !important;
                color: var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-tracker .inventory-v11-mini-tracker button:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-tracker .inventory-v11-mini-tracker strong {
                min-width: 31px !important;
                padding-inline: 3px !important;
                font-size: 10px !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-use {
                flex: 0 0 auto;
                min-height: 22px;
                border: 1px solid var(--theme-border-strong) !important;
                border-radius: 7px;
                background: var(--theme-surface-raised) !important;
                padding-inline: 7px;
                color: var(--theme-text-strong) !important;
                font-size: 9px;
                font-weight: 900;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-use:hover {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-use:disabled {
                opacity: .5;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-footer-note {
                color: var(--theme-text-secondary) !important;
                font-size: 9px;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-discard {
                border: 1px solid var(--theme-border) !important;
                border-radius: 7px;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
                font-size: 9px;
                padding: 4px 6px;
            }
            @media (max-width: 560px) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v39-items-grid {
                    grid-template-columns: minmax(0, 1fr) !important;
                }
            }

            /* =====================================================================
               V49 — CARTÕES CALMOS: status integrado à foto, descrição curta,
               habilidades em linhas coesas e tooltip de leitura sem recortes.
               Usam exclusivamente os tokens dos 8 temas existentes.
               ===================================================================== */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card {
                height: 240px !important;
                min-height: 240px !important;
                max-height: 240px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-hero {
                /* Recupera espaço vazio sob a foto para a descrição. */
                flex: 0 0 104px !important;
                height: 104px !important;
                gap: 11px !important;
                padding: 10px 11px 6px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art-col {
                flex: 0 0 76px;
                width: 76px;
                min-width: 76px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art {
                width: 76px !important;
                height: 76px !important;
                min-width: 76px !important;
                min-height: 76px !important;
                max-width: 76px !important;
                max-height: 76px !important;
                flex-basis: 76px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art .inventory-v43-used-badge {
                top: 4px;
                left: 4px;
                right: auto;
                bottom: auto;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip {
                position: absolute;
                right: 4px;
                bottom: 4px;
                z-index: 2;
                display: flex;
                width: 23px;
                height: 23px;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--theme-border-strong) !important;
                border-radius: 7px;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-secondary) !important;
                font-size: 14px;
                font-weight: 900;
                box-shadow: 0 2px 5px rgba(0,0,0,.12);
                transition: background .15s ease, border-color .15s ease, transform .15s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip.is-equipped {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip:hover,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip:focus-visible {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-soft) !important;
                color: var(--theme-text-strong) !important;
                transform: translateY(-1px);
                outline: 2px solid var(--theme-accent-softer);
                outline-offset: 1px;
            }
            /* V58: durante a atualização, nenhum outro ícone perde opacidade. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip:disabled {
                cursor: wait;
                opacity: 1 !important;
            }
            /* V58 — mão fechada = equipado; mão aberta = desequipado.
               Ícones PNG convertidos em máscaras neutras.
               O desenho das mãos permanece o mesmo; a paleta vem do tema ativo. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon {
                position: relative;
                display: block;
                flex: 0 0 17px;
                width: 17px;
                height: 17px;
                transition: opacity .15s ease, transform .15s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon::before,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon::after {
                position: absolute;
                inset: 0;
                display: block;
                content: '';
                -webkit-mask-repeat: no-repeat;
                mask-repeat: no-repeat;
                -webkit-mask-position: center;
                mask-position: center;
                -webkit-mask-size: contain;
                mask-size: contain;
            }
            /* Camada interna: substitui o branco fixo do PNG pelo papel do tema. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon::before {
                background-color: var(--theme-surface-card) !important;
                -webkit-mask-image: var(--inventory-hand-fill);
                mask-image: var(--inventory-hand-fill);
            }
            /* Traços da mão: seguem o contraste/acento do tema. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon::after {
                background-color: var(--theme-text-secondary) !important;
                -webkit-mask-image: var(--inventory-hand-line);
                mask-image: var(--inventory-hand-line);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon.is-equipped {
                --inventory-hand-fill: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACgCAYAAACLz2ctAAAL1klEQVR42u2deaxcVR3HPzPzurfYKtgQikSK0NKGyhoIBZS0KgSaaEBJSBRcwZAomkCUhviHCxIX0ERJNGKClVCNVBSxaA0plQgoEIVipbUu1YDdaG3fo29m7vjH+R3nvNuZ9+7M3Ll3lu83OZl5y8zcOfdzf9s595wCUjOVgGrw8+nARcClwDLgDcAC+9t+YC/wAvAb4HFg6yTvJUlTwgdQANYAG4DDQC1hO2yvWWPvEb6nJDVVIQBmJbAlBlYVqNhjFPw+iv0tfM0W4MIG7y9JE1QMnn85AKxiLWrBAkbB6/zPdzT5LEn6v1WaBzwYs3a1DltoFR+0z0CWUIq73ZnAYwbKeIsWL4lFHLfnjwEz5I4HF6SitSQnuGDJQQFYH8BX61Lz770+9tlpfy8pw7htJEGGOdLkZPvX3ZoBfHEIb2mSHXswRxJk6iP9Hk/265VUClxkCOPrgdfZiTkIHABGG7zWv64GLAH+AEwPLEw3Fbrks4BtwWcWOLpeOBs4xlrVvte+2P8Vm7xWAHbB4tUCgJYBq4FLgKXAcXbCSgbeKPAP4GlccfhXuKIxBtw48DBwuZ28rGp1/rMeBq4IjgVccXu1lYHOBU6y7zTLwB0F9gDPAo8Cm4Htsew6knPsjtXzutI6v9yi+9sF3G2wApxhMFQzcL3xpMR/7hl2LEvt2Ha1+F5jwEPAxU36SkqxRncmsDF2AspMLA7HWzX4n3CkYi2wLiiV1DJu/jN/ANzGxJGWSnDMzb5TpcFxP4QbMhSEKYYIHr6bgNdovzjsLU85B9iStnKb3ymsNb4KXBtAqIy5A/j8VXxXA8vRqQtsB+BuueO0jiW8uO4UhJ3B58sQ36I7xeFBbaGV/4LccWcJx9oM63ODCuGnBWF78F0auFxZvs5c+zmCsLWkY7YVaGs5lEgGqfl4+ekMC+1tlTd6yfpFwMeAU60DNXWps/6smAX8iPVtsdcsTq8dy1zc1PZFdvUKwM4UWd9us6J3xX5fkwU8+mqtAZcBJ/bi1dqn8sOXS4BVDS7qIvWJDSXqs27ivy8MOoD+iryaieO9UjpWsAZ8IFbmKnL07QSNRlx8HF5KO5EZ6ZEO8jM55gAXMHEERErH0BRws29m4kaUvBbb78/GTXyYj5v4MAa8ArwE/B54BtgdnK8CAzTpwcO2Qplr10oyNeAI8EaD8Abc7KCxhO+xG/gh8I5Y2DQQoyzeEr+H/CYHDEv7PvB8g3KNn/wQtrK1eClsExNn3xQHBcAPc/SYplr3aoStFPnjkx5qwNesvtg2hL1G7jyFa12XTzRKLbrQQpAl+/e42dz4wnarFr1YiJa638fFlN6jbK54E3B8OxAq05Q60TRzy8uAn+EGEWglMRGAUhrxe9nKOPe0agV7DUAVn/vbEl4LvI8WbvCSBZTSkh/yu9OSySiJKxaAUpoARsCbgA9SH7oTgFLmYdSN5parAlDKUn5G02m4Mf0praAAlNKWn6DwTnssCEApS3ngzo8BKQClTAFcjBsnnjQbFoBStwCcj9tJAAEo5aEZuAnGfZUFawmJwdEICWbc9xKARepr5ElDol4B0N9fsEinRADmYarLwHXAJ6lPlpSGxE/n/fkV4Hrge9RnwygWlAXMHL5I8AnArDStAXza/0IAZh7zCT4BmIvbvQ64V/BJxRzgu17wSVkDOKKYT8oLQCUcUm4A+oRD8EmZAxgmHIJPyhRAJRxSbgAq4ZByA1DwSbkB6OF7v+CTsgbQw3dFEPMh+KQsAPSboVwIPBBAp/tNpK4D6FfLfAuwAbfFljaXkTIB0Md303G7fh9rMAo+qaXYrVPX+1XgPHs+oi6VsrCAHr41wCfsue7jkDIBsGBx3jzgm0HMp4xXygRAvxDhbbjFCLWpoJQZgH4/3+XUb6EUfFKmFrAGfA639keidYAlKQ0AS7gyyzJLPiJlvVIeFvBTuFnOkbpPygpAP+JxEnANCVdAl6Q0AQS4CjfcVlXsJyXIFVID0AP3XvtZ8EmZWUC/A84K3H5gcr9S5gACXBlkwpKUGYA+210p9ytlDaBfuXQ+cFaLcaMkdQyg//tbcfP9arKAUtYWEEtAUPwn5REDApyirpLyANAnIIuVgEhZA+gTkCLwZgEo5eWCZ1oWLAClXACcjbvzTZIyd8EewBnqKikvCzhLFlDKE0DFfVKuAI7j7vuVpFwALKMRECkHAGsCUOoFC3gQGFNXSXkB+JpBGFpFScrMBUfAAXWVlIcF9H/fIwso5QngPwWglFcMCPB3dZWUJ4A71FVSHgBGMQuo+4GlTAH0Md924FXqq6NKUqYueC+wU4mIlIcFLNnjn2NuWZIysYB+OtZT6i4pDwC9y31SiYjUgiISTGJpZW2YrcB/qN8tJ0mTaZwEk1iSWsAibjz4WSUiUgsAjk7FS6srpP5aAEoJdSQAkE4B9C53kz1XHChNlTOM4iYzpwrgC7hhOcWB0lQ6HCQhHbtgcHuCjAOPxKCUpEYWcE8SxoptvPGP23itNHwA7kgbQL8t15PAS9Q3LZSkRtrWSnablOySueGfyA1LTeRHzv4yVfzXjhv1wN1nGY6yYamRkYqAv3ULwKJlw4/b73TPsBTXPhLextHuhtUA30XrxkiNPeTzuJGzYjcA9Nt2/RQ3R1DJiBTPgDcn5avdDatLuEr3t9EsaelonjYncb904EK9aT0WN1F1AfVJC9LwWr8CrgB9KrA/iXFqFxifjOwG7kFDc1L9/D9j8E0Z/3UCoP/AAnA37p6Rklyx4j/qM6YSsdVpHW8E+C9uGd+3B5ZRGj74SriFrG4yC9jVGDD++vmWeh+vWHAoVTUANwLvooXKSDEF8otG/O3KiIde61rlKo1CciFovwPOCa4IaXiy372W/e5rxRAVUzyAKnCzsuGhdL8APzf4WkpGSyleBSXcGjLHAefjVtZXLDgcioAPAS+TsPzSDRXsw4/BzYTw94XW1Aa2VexxQ7seNU0L5V3xQeBGVJweBvlY744Uc4qONWKPd9nBlWUpBtr6beyCMev4qijhtnl9LnawaoMH4CUp5xOpyB/McuCQxYKKBwcPvgc6ha9b1Pqs+BXgr8DV1OcRahJr/2e8BdwQ7LvpcA+ZbprNmsWDfwTmABcZhCrN9D+AJeCzwC+p3wPSs1lSyR43KCnp++bDqOeAacG57flUvYDbef0pQdi3LbLYrwqs7MXEI0m98QSLCZUZ91/zRmNtv8EXjzdPA/4tCPsy630kVuulXyFcgZvOLwj7I+6LgH8BC6kPudLvEJ5rZRpB2PtxXw1Y1a+ud7LhuhXALiUmPQufPycf73fXOxmES3CrbQnC3mrj9nj7IMIXd8cnAL8VhD0H3zcC+AZ2BMtDOBv4USzwFQz5wbcuOD8DP3waZlWfb5D+q2Vb67vPwCsyRGP3YXp/Fe7+Arnk7LPdr8dGsIZOPtg9HdgSdJCmc3W3zheOchQZ8llLHsJpwBdjLkKxYfoutwrcMEwxX6tx4SrgT4oNu1Lj2wG8bZBLLZ3Ghb5T5gBfCrK0itxyR+O6Ndxi8wsFX/JSDcB5wKMxNyIQW0s0xoFbmvSvNIk1DDvqGuDF2JUtEKe2epuBC4IwRzPU24gNfZA8F7ccyE6B2NTi+aTtZer3a8vqpeyW5wO34lZkCMsLlSHMmqOYxYuA79hwZ7zeKqWYpADMAz6KWyY2Xm4YdBirsaJ9Gbgft04PSjSyjQ9LwOW4+1YPDTCMUYOQYwy4Fzgz1h+q7eUAIsDJlvE90aB+WAlOYNRHlq7S4LvsAL4CLI2BJ3ebI4jxzl8OfAY3xDfaJFss9xCUUQBcozHx/cB63A3ic/sRvGEwy77U4N1UaBkvBlbjVnU9ZZKTVm3QZ4UU+rAWe/TPJ0sUduK2zN0E/AJ3g1cY40X00apkwxYXNINxxIA822Kn5QbkItwOAHnpAO5moK1Wv3vCno81qAZE9OH63MMcmHoYazTe8XOWAXgycKI9LsbtDrXA2jzchInp1FcKSKIIOBxrh3DrLL+I231qu1m73cCRBiWoQr9Zu0b6H3TySrOuM3kcAAAAAElFTkSuQmCC');
                --inventory-hand-line: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACgCAYAAACLz2ctAAAXJ0lEQVR42u2de5RkRX3HP/d2z87Mzg7swrI8ljfEZQUBeYhhkShRAjE8AyuGQIiigcCJIj6SACbhaFR8IYSX5kRN0KwLhKeKUXQFBExgCUZeCkIQMMvusizLvmb6kT/qV+mau919f/fRPbe763dOn+mZufdW3apvfX+PqvpVgJdWUgKqzu+vB94CHAXsC2wLzJH/rQFWA48CPwTuAR5r8ywvXmLBBxAAxwO3AOuBuvKzXu45Xp7hPtOLl5YSOIA5Arg3AqwqUJGfNefvtcj/3HvuBRY1eb4XL1uAz8pnHIBV5FNLwIA15z77+6ed54e+ub00k3Hg5gjb1TN+XFa8Wcrw4mULtTsCLBOgTCRkPA0jTsj3ZcCwV8f9CyS3YwPFPSX5udQBX71DH/vspZGyk7yXB27BQFdWeJjlFp1t7/tYF8AXBeFHW3jHFphlhade7nV7sldHUrO4WoCJzW0tHfMqsBbY0OTemvP7PsBDwAzpzE63iauSDwKejDgn0feaCWwln6q818uR62y9fayxi2ESMAHhD4px/wQmGLxROvcV4EXgAeBKYDGNwDECOIBvO86ChsGWAacBuwCz5LOL/O3uBI5JHbgjUhekjouBK4D75R1eATbLu60W0C4B3gPsHWkf72F3kPWsHAf8OzCZUP09D3wJWCjP2V9Yo6q4dx1wuKKeRwhQ4pwSW+7+ct9CqdvzCd9pI3AbcGSLtvKSo6nwRuB7kQ6YZGpwOPqpOte4MxUXA99Qst+T4rkCDDn2pPspyf+Qa59WsuD1wEVMnWmpOHVu9U6VJvW+DTNl6EHYAZV7PrCJ9MFhyzxJWbMKjDngixN7zVgKB2Uy5Tu5scZXgNMdEHqPOQP47Ci+vAlzZI3LaQF8XALwRUF4Qs510QDYfr/MgzB7eAXgajoTHNZ8nssQLbD3/KbLdXZZ/pNeHWdzOC7uYnyu2edSJ46YVCwLfnIa6u2C8EIPwnTgO8pRubVpAuCxGTrP3vPOaaq7q9oPKSoIixYzCqTxZgLXtIj9xclaUXsTOdRnpRM8zvqMrDKRoi3tz2sk1ljz9mB7sarugiZGdbvPT4CTgXkRBtob+LDE8NKwyP452IAHZmCxS4AFEebaAfgjTOA9iWNynlfFupDLuBj/NWWA+IQ2ne/+/oUeAuC1Ld4hKouVYaQa8LjYpX4xQwz7LU4QbtnDGdWlFp3mhk/e1wMAPD/iyAQtwlO2vX5LCcJWNm3oPM+2YRgpZyBCObZRblAGjK1hPUMJBgvEJEx4QJcBeLXzTpoy7ezMInQB7iXOYE+zkqaUtwovCqqt8zGGmfaaLw3WqoGuA84RUE0mfFc7FTdTcc+BwCNO/dK804HAwwnNkCSOzwxxUJYCp7a4xrblL4XVNzn/2wuzKudgYDdgNjCKmWNeIfc8CCx3HKqkdewJ+w9hHA1T7JpyAFkW/HABGfCiSB2TlrNXTEimjllRMw+z+vsc4PvEL5ywn5XAN4GjI4zYF6rZ2jMnK+y//84BFHsXEIALMpRl1eIqRTlfA37Olgsj7OIH9zMpn6gzeBdTV9+E/QLAsxXhl6/nEE4oKT3sbgIwy/tYW/AOkm2WShLkjy56qIs9PSMLCIuGXM2OsRdzUPdVzMLOosgmqVMWoAP8WnG9DcuUEqpQ6xWHzjMuEDW+fYzN3jMA1LDARE5lTRbovTfnCGRNG4c59FMobXikqOQd04DQL9/uL+m2QzAkanlf4HbM9oR6knp4AHrA5WG/T0oY51oBYNirAKx7TPWkWCY8HXiX2IglLXoHVTbkZFPl8Yw05eTNfv8J/AtmkcM4Jta6v4BKM9sU0liJ/R3gNdIF8Kc1DHOhIgzzdxkHj73vnzscGnEN9rhylqZ4J3dDlA3DXE7yhQ/LMNtKXdkG2F0ci12A95Nsu+kHtO8ziDag3bx9acx1H0miStqAr4rJvNBOLonUrVvsdzHwVgnfDNPY8TeO2Z+8PWaz/52YQPkLSjPqXHlW1QOweQMFwFM0VodE5Srgc2TPNmBje5cBX25xzfGY+e+wy+rqs5jtAsMCugmmbm91t35uK2x2BFOzSrRi+wXAb2u0yKDagBaEd0rjvlfsnRWY+c7lOTpF9hl/BnwFOBOzqPRhAeVqAV+ti+y3EpObZtgxd9rJJI1FD8di9ma3EhsL/D1MpojAA7A1MEqYPCufbaE685QSZlXJg10oK07OcMCiHWQVUc2PYFagL4oZIG92yvAqOEZF2oWXZTqX5Kcq7T0knzRl5WX7fU+eVUnxDqOYBQ1xddwLxT4UH4iemg210mE7zC60nexCWa3kfvk5lLL+M4GfKQA4W8wbPAD7Q/Jiv6cyPq+MSf8RJ8M0Upr0jAr2m2U6L6/k8IyKEqg9FQcMyG+li2e/HpGwQA1cB3b2WBssKQIAS2KUn4XJdmoXS3rpDPsVik3LBQBfFfhT4J8cr9Dbgum97DgpVB7pcJrB74Kv5sGXmq3swH1cce2jkXsGEoB2/ZgLPp8yIr1YVluiuHZJkZhwOgBYdmw+D758bLWaDOp1wIltrjtRrhlCP/fcVwAsCfOdBXzVgy9XqQiwbsUsj3/I+d9D8rdbHe0zcE5IqYnN58HXGRAup5E7p5npM3BOSNmDr2PqtxkISzQSHAXyvVQ08HWLAW0CIQ++7kktYuNNFmiAdJUByx0AX78voAgGqY6d7EzX4fDM56WrAHQdDu/t9hf7hUUHYBaH4yGPQc+AWcGXdobjbZi9up79io+XehEBaMF3ZgrwnYrZIL2D54TBGizlnMH3B47Np63oScAtHQoXePYruL+Qx0Ott7sI+JbTmJpnnyzgG/Wk4m3AtCO3ijmr4hbMjilteq6zgJtJluneSx+xX9YH2wNNZmBO/Z5LY+9rnJyHyfUcevANdmgqiw1oVe/ngTfJd83zzsWkAZuOjABeCsR+WR5uwXc8JhWXnQDXgO8mYU0PPh+YTwXAQLzcceBKx+aLa8wPYfLgzSGfvaleBhSANo3YRZgsmprM6J/C5BPZCXiVbIHMfmGNgQ29ZCmgJIDbj8YWyrhn3IjJPrU9ZuP5Oj/uve2XpZA68LeY3B9xJ3C/iDmXbVsaSQ83+L71tl8aAFqvdV9xPmoKr/eMCEtuwGfC9+yXsaAPodtVdQUmC+i4A0IfcO4P9uv6YgQ747EbcBrxuX/XCgC3ZWrCoU1FGXleijGItB1uwXYKZrot7mC9i8XZcFV0jfgz0TT24Ubf//3DkloAWsAtVoyAFZhzw2bT2IVlT1isxFT2AUVd7stbDXj1W2xj06ZOOwCzuTlO/X5RmCyMlNMuIbYF+I9of7z9I3JNp/I4e/AVEIAWbMehm7+9Hdgqwn6gTwXxduAXTf7+S+B3fZf1l2gWD1jAHaEYybeLCt6uibqNA6A9u+NlzEEnp2DOmgCT1f3GCCN7GQAA2s6eDRykYM0bmHqwnXutZld+3SnzRgd09Dj4+k391rsFQOs8HIhZ7xd3GPHD4iVXcng59zh5e5SCB1+fvVOoLOiAiDpuBb6VDqjDlB63q/rt2R1+6dYAOyEAeyuuuUfAEmQsy4sH4BYOyF4K6n28DfsNKgB96CUDAK3BHwJ7KBr0OQFgzTOglzxV8Ih4wXEAXIHuaHfPft4LTgTAmQpgVYD1tJ8hCfEqyUtKAA7HXDOBWWjQ6nl24eqgnE/sB1qOABxVMOB6YcF2zysz2Adke0kJQM1o1s7zDsIRXJ79cgbgBPEzGyPKZ3knxUtiAE4SPxNhj6CPY8J+V8Ge/XIEYD0BAIfRHQE/5JvcixaAdjS/Svwy+BLmePa45ET9rII9+3VIBW8UELqs2EzGYlSwPc/Ms6CXRCq4jtnlFidbKZyVkPiYohcPwC3UyioFA+6ELj/gTK9+vWgBaON2v1YAcHd0G889A/aHadbVgv5Hcc2uimtqAsB+CkgHHoCdL+hpxTULlcAqYwLXXoo7oIKiALAaYcB2ANsHmIVuP8iYZz/PgEkKegqT1bTdrrSZmByAk4rnjuGlyOArHABXAc8oHJEFmGVZQYwdONQHzkg/sl+dxqqlWhEA6KbheMIBUCs5TBgwUJTrWbCYUi4aA1ow/Yfi2kXoFxyMe/YrpJSKBkCrcn+qcET2BeYRn4bNhmN8TLB4MkMxwLRrRKt5ANCq3MeAl2gc09BKDsYkotRkXdja93fhZFiBB818/gSKXI5aBgww88EPKxyRoxUMaF9kvAfVWT+r3wCzBaMe028jSgBuiMNL0gypP1AA8BgBliYeONTjtmA/esAjtN/bXUM3n78ZRcbbJBlSAe6SCrSzA7fGZNLaqHz+HM9+hVK/caZTjfbH61py2oBibUCYYHQAPIqZlouzA09UAtC+jD8vuBgySzHIakrbfb1DXJlVMDIyJoDvRpyTZrKYqTmi42S2Z79CyFiMeRUIq+2lIKtVGowlAaB98I2Ke2cAR7Flpvx2I8qz4PTaf8PSB7UYvExgjmqLw8nTeQPQZjf4KSZfcxhT2fckYMAQkwDTs9/0sp92u8R+imue1HZ8klFSkhHwbwo1fBjwBrbMmN/KsxrHT89Np2hjsiUaCUvbDdZfKCImqbKWgjnxfJL49X8fAF5rU07079t49psWGaKxq7EdViYxp1+FMSRVA57tBABtUPpRTEZUYip9PGaFjNYjHhdPzEt37b85MerXLtHaALxR8cyX0W3jSDXpbFnvH5XscD5mW6e2rO0LxDqDwH6BRCHicv6FQiRHx5AIwM8xM2dhJwBoTzW6FbNGMM4ZOQ3Yky3Tt4UtXmBUQOilO+w3jpnZ0JzjUsIcWBTnAd+txVfaA6tLQsfXoDu74xxgDbqlWhUZkT4s0x2ZE8P0FiMTwI60n4YLIwCsdwKALgt+TfS9JiSzEBMd1yz5DoEdvPrtOPuNifcbt2yqhInpHhPzvBATgF6uiJJkAqAtbCVwLfFTcwCfJz6Nb1QVz/Xg66jMIz6tsm2PqtjzcfbfctF2qlOtsqx8tYHpLwGr5UXaFXgYcBJmc1NZ+fx53ivuGPvNUrJfIM7H62IIwfb9DyLOascAaG3Bl4ArlSz4D0L7lQQg34nuJjQaFPbbXomNkkQx3hWDhTJmIfJNWvWbFYCuLXgF8KLCFiwDHxE7QZuqrQzs7EkrV/bbRrzfmvL6YeB9CvX7Y+BXChzkBkBrC64BPq7U+2cDb06oisfEA/Psl72/hkSraNlvNWZ1k0a+kRRXQY6dFgIPAIcIM7azASYw88RJKhsC/ytetwdgegDuJgxYVQLwZWG1ds8MBKivk+vVx+qGOb2UPdb1AiX1zgCuw0TLtds4axKame21aOp+mo2J+yUB3wkKMwzgDrm+RIKTlEo5vlwJk0NmO1GxceeG7I4JZv9Y6Y3ZcrbCzKpszrmD+v1orVFp8yQabT1wp5Ic3isaKkwCwDw3INuwzEUCxJKCDT+O2capnSWx5ewsQPSiJ5pdErRxWaIb5ynYLxT2e9jRhNM26ktSgWOB7wgLal76DZhY0yjJTlt/HrPcy7NfdrvPEpINOg8Tnw3Dks7hYv+XkgIw7xQMVQHcdzEB6rISUD90nJMkddqVYq4hLBL4dlSCz2W/lcBVSvb7voAvIMXJ9p3IAWI94L8EHpEXiqvYdsBtwoL1BPWyjsk8z35NwbeDfKpKLJTFkTgGOFRZzt9nwVKnGt9S8X4yOkaVlVwGvBsz5VNJOJDWAi94AP4/+OaKrVxL2GclGosJ4khmKWaGJLHqzdsLbuUVr5AY0qk0Zk2CGM94N2AJjT0KdWV5o5j5zY0JGqNfwTcPmJ8AfCEmQL0K+BHts1VYu28dZm7/1QT91DUA2gqVgZ9hZjLegu4Yh30x+05vEkM4TADCERpxro0DBkDbRvNF7SZJMDmCWUJ/KXCkwuwpAX8tIZoSGZJZdroDAsc1vwUT1NR6xvcApwuzDSk9ONeQXoOZn64OCPgCxylLog5niNNxLGaBcRz4QrHtD5Xfa2nZr1udYMsYFRvv0AQgfBJ4p3N/JYEzZVdnvISZd+5XANqFpfPRLa13ZUgG6kHADYpyrPr9HeDeLLZftzvBzg3OF2bbg/j5YitrgLfKzzkSqtF68/bv6wWI6/sIgJZ1bBQgTAi+sthvu0soJU4saVwCfCIP8HW7E2yFF4ihu2MCEIJJeHQfZh1bLdLYmiX+iKf8Eo1pvKCHgTcm4NsqBRCGpC22lShFnNh+ulNUtTa+O61OSCvPeCVm1ewp4rVqHBMwu+tqmBRxM6QRa+iTbFpPeY7cX3PYNOgh4A1jllPtRPtcfu3At1q00N2K620b/0bAF5t0sugxMMuEh2LmEOclZMIHgT+RMMCclCPRHkOwXlT7WqdBgwIDbxthLY1T1srhWIFZLLJUWbb1et8hxJGL6p1uO8hS+AHAt8U21DomVk7FrKSZKx2SBoiWPTdh5pTXyQivT3MbuYNhTEC3dYbOt3O8L4jmuUpZBzu1eh5wdZ6qtwiGuH2ZfYDbgb1TgPAWzIqal6WTailjUqFjxG/CxBDXia040ULdBDkCLaohxuRjN41D+lhbSd5jLfBp4I+V903KwP4bTHwwd/AVQd3YET1fVMLhKUAIZt75erGJbH7qtB3mgtHaiZsdMG6Wv7tl1FO0uS2nLOp1RGxU+z1wVGDa97A293biQMxNCL4rgb+gMZ9f7zcAuiCcCXxdVISNNyWp37PAucBDoq5mZmDEKBiJgLLmgNAFYE3xvMB5ZtkpI4wY/llkSFh8NWa67JoE91rwfRMzEWBnOuqd6PwiJQGyL/gJzKJWEjonVu4EPodJkDNLVFkaIIYZ/6+RWs7PtG31EmZW5Doae280YrXP9cBZKRm+JwHo1qUuLPhlx8stp3je3cBnhBFn0phgr+QEwLwlzAi8Oo09GR8U1ZnEFrXe7uWYvT1Bp8FX1PiXNXZfLyBcRCOOl6aTHgQ+ReOosXEae5IrBQFfmjJDx3xZLbbju8VhSMrC1tyxsxyh0+Ydt7+KJjUB4QpMJtYyZiVNIIBJahvuhFmzdobc/zRm84y1dYaczp+uWGCSralussjV0j5nixP3toTlVhz2/HPgC87v9W68eJFnAFy78O3AF2kkx65mHDz3i5G9TLzEkqjpEUfdZ3Vg8gCg67RUJUS0Tup7IHAm8Icpwz82xvcrzI62ZZ0KtfQqAG39StIoY8DFwIU0ZgKCHNTlckwc8m5Mws1N8swRGicHhTHOQxrGaJasM3AGXl3CPuvl5yhm+uw4TJaqtAPQHbw3S+RgxXSArxcAGA3VALxJ7JR3OGokz2PmfyJs8F+Y7aWrBJS2vcpSn5JTbpCiLV2Gtd8301jbN4pZsHEYZg72qIzv5ToakzKYL2vSvh6Aberq7js9DROl38cZ2QH5OxA14HHM2SjPAM8JKF8RdTghZScJfgc0As8jwu5z5F0WinrNM0Gny3r3AB8TMyRswehelINmloQMnnHUVoVG1H6QP+5sTV0cr3Od9it5KOXnwc+WUf2s0wHVSAcMGvDc37+Cme6kQ1pioNnQDVKPA+8Xx8LtlMkBAGNV3tN953/FLL1yY6xeOugtu+z4+8C3MMus+hWMtSYmx0bgq0w9UKbE4GR+LRQQwZxR8lHMcv5KpAMrTgfWeojpKk3e5WnMHPjCCPC8up1GIEZH/X7AX2F2dG1o0rkVhyGLAMqaA7jJJv9fg5kBOYmpydx7BniDQMuhE76pR5jxSIknHoJZEBu2CWNE2yzIoQ3rkZ84ccBWdXkGM699FyYD2YsRG6+bMzgegCmZMQrGsgDyYLGd9hNA7sz0nthk8908hpmpuU++b2wSDejYmj0PwM6CsU7zWYBRAeCemOSOe2JShszFBI3niNc9RGOXnja2ZjdEuZ/XMIsLHgeeAJ4StlvJltlgrXnRU2zXTP4PJhQO5RAsSlIAAAAASUVORK5CYII=');
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon.is-unequipped {
                --inventory-hand-fill: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACgCAYAAACLz2ctAAAJyklEQVR42u2dW8wcZRnHf3toIJVyqLUNBaJIGyOKUS+sNdggVQiICJ6ixguiKBqj6I0aCVyo9Qav1HhA1MSoFQjEENRIS+KhAaoQoImnWEGKgLQWlFIK7c6MF/O82XeH+ba7s7Mz7+z8f8mb3X79DrP7/uZ53vN2EPOgB0T2/AzgEmAz8CpgJXAscAD4N7AT+BVwG/Bszs8LMTEdoGvP1wPfB54EkgnK34DPAcd4EgoxlXwde36lRTgn18AiWux9Lbbi/s99/QFgo/2evt5WMal8PXv8tifTkYx040pk359YKn6/JJRUXRPLla4X5XycJE6+w1OIly0D7/k7xqTjaa5PNEi6vteOG9fJ6GXk+JQnXzJjcen6aWtL4sk17fUF/4aL/J7namANsBY4Hvgf8DiwGzjkSdEFXgbcb73bsqJQZNe0HTjfxDvs/f8q4HS7xmOAvVYe8XrTLkqqR92Qm/AE4KPArcD+JaLTg8BPgAu9KPMjr82XlFhcOr7Qk+5y4HaTbanrux64IHNziQDpeo8ft8rLS4fZnqorvwE+aNExnqHdN07AGNhmN8ajmf+PvevL+9vbgU2ZFC4Ck+/FwC8zPdfBGJnGCVlFGUxwfUcy1/cFSRimfKcDf/bEiwrIUIWI8VGkm+T6vuelY0kYwPDKCuC+EnuuoZbYe31b1CYMo7cLsLUF8vnFvc53S8L65TuX6Wcqml7c2OJDwHJGpw5rr5C2pd8fA6dm2oNteO0R6Wqc/cBdNrYYKy5V2/F4vdc2SlpWXBTcFUoE7LZQwIvsMWrxTfhK4CyTsisBq8Glmk1eSmojkaXeN4fgQJvaP7G93jUtF9CxNqSQ3BaWk86nSkA42R4TCVgdfdIVK23G3XjHKwKKENrEElC0Fwkoam8TCeHahtnBaX8QWwKKuUjXZ3RmKA83ZRtJQFFm8ysBnrd/v4ThHpNlpEv+9wF7GO6BcREyloBiVg6Rron8EHAp8DqGY6Q+DwF3ALeQHiGSoKNDCqUagBOBp2jvYgT/dT8I/IP8PSbREu/PNrTHRAJS/x6Tz0tCCVhGJJxlD8x1XgelsIQaB2z3TTlt/TvZBqTbRLeYkPJIEbC2jU4z7TGRuaLoDd0zAa8lXWUUF0nFElDM0nyLbOzwCm94RgKKSiVMgMsYbnqSgKJSAQHOBF5NgT0mElDMykx7TCSgKItT1AsWdVJoj4kEFLPihl5WKAKKOkkkoGhsN1oICSgkoBASUEhAISSgkIBCSEAhAYWQgEICCiEBhQQUQgIKCSiEQ+sBhSKgEBJQSEAhJKCQgEJIQCEBhZCAQgIKIQGFBBRCAgoJKCSgEBJQSEAhJKCQgEJIQCEBhZCAQgIKIQGFBBRCAgoJKIQEFBJQCAkoJKAQElBIQCGmp1+B4B2GH+ueZJ4nQKxqkIBl05tCLidppOqQgGVEvMST6VTgbGA9sBpYBRwEHgd2AX8AHs6IKxElYOGo5+R5L3A5sBFYMeZnDgK/A64Dfu79fM+iZ6IqEpPKB/Am4A6vfZcAA+BIThlkvu9uE7eT83tnxf3OE4GnvOZBojJzcfV4Q8l1NrV8HwMOexc1mKCSY/u+KCPi+zI99J4ElIDj5NuSEarIC4lyRHxPSSJKwAUU0I98iaXVMio1ykhcRmqWgAsmoPsjGyztDuZQoVkRd5qIFBBRAgYqYNGZEDeg/DVgWaaSyxzScb3hGHgDcGMmIkZ1NXxFeZVcJPrFwKU2xhfNWYCuFSfiBhPxLq+zIhFbJKAbm/uEF4arutasiDcAd1pnZZKI2EPz340W0AlwsgnQqSHq+CJGdh03WUTMpua+fe8y+/ppwHGMzkmLhgkIcA7pDEdcY0X6bcTIS813e50V1zly45NXefKKACg6FbfOHuMAUlrXuxa8zspO4Hrgj6Tz0J8E3mnfp7ZiwwVcHXA0j70hog057Ve1ARssoOtwrLHHTuAiJowubFDkW5AI2IQo0lXUW7xOSBNRb3eBBHSVuY9qxwCFBBxpA97M6N4O0V6SKgV0027bga9YG1Irl0WlbUAn4dXANQw3IElCUVknxEn4ZeDKTI9TiEp6wZGl4K8DH2Y4v6ppLlGJgJDOtfaBH5IujXoezbWqM1KhgL6ENwMXA08zukZPtINDdQnoS3g7cAHpOKE2mbeLZ+yxU4eAvoR3Am8FHpGEreJAnREwK+Eu4FzgrybhQPXTmghYq4BOwh6wG9gM3GtSSsLFxKXcJ0IREIbjhI8B5wG/lYQLi3Po70V6w/NcDRPZ738SeDvwC0m4cLix30PAntAEhOGS/YOk2zh/JgkXkr2W7YIT0JdwAHyA9Cg2SbgYxF76PczwfMigBPQvtAtcQXqiQp/hYUSiuSkYYEdRn+rYI9EDfm1SbvZehFYuN7cX/CXgn/Y8acIFO/E/zfCgIB0W1Kzi6msf6aFPhYJIHXtC3MlXWknT/PafO7XsvwzXhQYvoEMraRYj/d7Y9CaU2xp6HulKGv/MOZWw0+9/gJWzCBjCtkx/Jc35aCVNE3B1cxvpREOh9BtqJHwN6WeHuGN/FXHCK2747JwaR1PmKuE64C+SMOgjeXdY2l24ww3c3bQWuEcSBivgRYsW/fIkXEm6kkYShiXfPQwPCl1YnIQvssauJAxHwIsXOfrl9dKXAVslYRDy3RrQCEplEroxpu9S7ofiqEw+7jcgXVa3blE7H0cbcXcv+FrvjpSE1RSXda5qS+pdSkL3wq+WhJWn3t9bU6hHi1cuLbWSJpIocxtwjmzK7aVta/uNk9ANWF/myScJy2/3udR7SZtT79FmTd4FPCcJ59buuybzfgsP90GJb0Mracos7oN9vulFPq1YP0ok3Ei6O0sSliPfTyXf9BKeRbo/VQPWs6Xdmyy7dCXf9BK+HHhAEhYaaE6A7+SMvYoJcb20k0hPYtCsyeTjfAmwJWf2SRSYunOP31jijVYZzRAHgI+ozVeuhB1vrHC/omHuAHMC3Ae8VuN88501WQ9sy9z5bRQxzmSCb5Eud9M4XwWdE4DPkJ5j56fluCURz++Q7QTektN2FhWk5LWkG+Kfa0FEzIr3GPBZ76ZUe6+mXjKku+9+QHq0rB8Rmx4Vo5wb6mHgiww/51lRL5C2oRs3/Crwr5zhiSMNEDL2rjV7nfdas+OkjHiKeoGkZV/ElaRHhGxl9ONosxU9sCgTU92hSu7vuN7rgKUXXuwhncM9OyNaP0TxdCcM24f+SQyrgE1WiW8EzgROCPT6nwDuJ10wusOi3jMZ8YI9h1ECvjA1J7zwWJDTbKzsDOAVpPsiTgGOA5ZbOXYO72cMPGtCHbTHR4E/kX4Exm573JvT8/fPcAmW/wNP+p5H2nYh0gAAAABJRU5ErkJggg==');
                --inventory-hand-line: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACgCAYAAACLz2ctAAATVElEQVR42u2dfbQcZXnAf7OzN4Gb5AZCPkggIQnit1Dt4bMhgMGoWFsQiko9p1ih2haP9Q+rpa09taX1KPa0PVIEK1YOFeRDqyBRiBiEEogFI2JVoGiiJpDEhJAbbu7H7vaP53nPvnecmZ39vLM7z3POnL17d3Z2Zt7fPF/v875vgEk3JAQq+vfxwPnAOuBVwALgMOAA8CzwCLABuAt4Meb7JiaZJdAN4ATgc8BeoJZh+wnw58BsD0ITk6bgc/IB1XAOrinVaFXvf1Xd3Gfu/98HTtfjlO22mmSFL9TXaz2YJiPQpW0V3b+mpvgdBqGJAyv0AAti9nOQOPgmmgAvuk15f781xRwHTZyfSR8BV87QiKEHhXt9vwdfrc3NmesX1Jf0gWv2/PrGb7HIdXrkuRhYAiwDRoD9wE7gaWAsoiFXAls1ui01uK/P6LFWAEel7FfRY28E3qjgTXifLwRW6TnOBnbp9nMvmg70fCyi7pOHcD5wOfA14FcJ2ukZ4D+B8zwtc6Pn8yVptcv1+L6sBD6TwRyf50F3GXCPwpZ0fv8OvDnycJnkGL4S8D5tvDhzGI1U3bYJuEQ1WjXB7/upalAiWskHf10KgFXgXgX4l5HPq975xf32RmBtTIrIJEfwHQXcHYlcp1KCiDQg47a5+jtDMQAE1PN+FzYRpDQ6v8nI+X3EIMyXlPR1FfC/HnhZoYrm95I+f48HX5q4z7ckHKfaALos5/dZzxwbhDnQfvOA73Uwco3b5jQRAKG5v06fQ9W7vqvMJ8xHtAtwc5fhe6qJTIPb56VdOhf/Oi/ME4RBAeGrAK8HvqUmKs0kfR14UPNxxwJvAU7M+FuPAKeRrbAgUDgWArubuJ5Nurm0zoX6GidV/Z1tSFGESyXVTCf11vcLgYc8vypOW9yuecA4OQPYk0HjbG5C0/gBURZttgE4OuFYa1P8Upcm+qDua918M6DtX+f5RnGNdLX3nbJuoQYKoRc0bJ8hAK+JBC9Deo7++c1SrZjUw/K4RcS9F/e0f5TkpPHDGSJFF7EeOwMA/si7lkbntyIlKJn0XAmDsMcacCO/3unvtjMyQuMa+boeA3hmxmPO0tebUszwn+bBDJcKBF9NX5ckPPmj6huSIWhwn3+xxwHeA02e3xdS9lmWF6e8SDJHI804QJ5q4p64yPGZHp77001o1Kq+Ppmyz9I8RMFFA7CMVKzEyYEWNNeLGSDtlBxo4TsHUzTziGlAk5mUqgFYjKAnr8fDADQpvFgmfMA1TAvXX4ox0zUD0KRb0JWpJ6ghPsUTpnxmAJq07H7VgHF9v4j6GJMhpOR/N9LlOBbRkjUD0KRdGUNqIt8FXAC8lnqO1JefIpVDX0aKIJxGtIFOLfg3RwD7iC9G+E7E3GQ5XlrX2cMdPt7WFo63kPi+YDd46f+IH2NSIb5Y4146PMbEouDiZRgcNKuA1R5wrquyxPRBU/5wgHOB+4EPe2Y4MABNWhGn8fxZFZIYKVMfX1wFPg5cr8cotQOhATjYaZigwWfNtr8DdQoZJnqVQlkyAE16CXWIlHVdiQwDqNDiGBOLgqebpKLLI8B9yBiYFcDFxE8f4iCsAZ/U6HiMetmbSQ6i4C05iIIXkW2Myf0k1weuT/le22NMzAT3jw8YdKldbwDOAnYgszT4W6ipl9ek/E4NuFTPr2IA2gPSjGxDZm+YRX32LX+rqBY9hMxfk8TPK4FXe1GxATiAGrDWhet4t3fsJO01oa7LD9Q/jEpF4T2zFaYMwGLLJi+tkiYVYBi4LWWfYywNY1FwM9pvh15zOeO9mU36GJiWxpgYgMV1EUZb+M6hlPOaZxowXw0c9Pn598SCGID5ArBwVdkGoD08BqAFNcUVA9C0nwFoMiOglQxAA6bbZjv32tEAzFdjBYMGmAFoYgCaWPBhAJoYgCam/QzA/oUkGETwDcD+0yoD1V9sANqDYgCaWBBiYtKuWD2giWlAk8GOgg1AEwPQNFaBNZYBONhAB0VrKwOwvShuIuWzgy0cbzzD8YI+u0cGYJcaIkDWb9uWsM83mgDGHe8F4LmEfe7uMIBWEd3nJtPtf0nMZ78APqF/TzXZFhfFfLYbmY20kdbtNz/ZAGxDqtogDwEvBW5E1vP9KLBSzWkzDVbR/R/U492CTBj598i8K+O0OAtpnsVmSO2MKX4K+IMYbVFr43jvjHwWMvMrXAYGYH4hLHvATbXhsLvjzfL+nswBfCUDMN8QTnb4mBNFuHHmA1rQ1eyDZgCazBisFgUXwBoEdtNNBgW+ai/NqQFo4oMXAntSgpkNeWDAABxc7efa9ryECPtDeYi2DcDBlSnVgt9CFpr5KvAE8Clk2YUxctCzYnnAwQ48qtrGTwDnRz7LQ8+KAVgAcStZliPasZqHkzMAixOU5LJnxXzAwTW/fSEGoIkBaNrPADQxMQALpo1s2KYBaEAYgAa7AWgC2NJaBqBpPwPQwDAxAE2KI93uCw6YPpyvFvm7WjDfy7RsjwAMPbgqGSGtWHMYgJ16wh1MxwJrgBOAxcBCZJanncDjwBZkcp+KB+6ggmjar8sA+vD8HnAZcDowL+U7B4HvANcD/xUBsWjm2YKQDsB3BlICfiuwXuGrIAWQ0a0CzAHeDHwFeFjBDfSzGoMzGY9pvy4C6OD7I2AT8Hp970NUjtlCz0esAqcquJuBiyN+YWiQGIBp8F0FXKdguRLwMEOjBrpfSSF0IH4JmfbsohkA0cDuEwB9zXcl9YkYwzbOxYFYURBvUxB909yvIJp0EMDQg+TTntYLOnROYQTEWz0fkT4C0bRflwB0s4NeDQw1uNnfRCZbfDmwFDgReA8y+2dWEKvAKREQTSMWVFxjv02DCDcZY3QbR3KAaXKKRs21DJsf2NQUxIsjD1HYQBMdAexjei+M2x7rAsxBn2/unr1WFcg5Me3i2v9Lrdy/UovaD+CPvZOIygSwHJnveMgzz4EXeIRIInqdmtn7mvAR44KVLBoxTLnmTo+TNfPbBQDdvMdLFYAgobHPAXYpfJMRzeVrsyAGxE0tBiubY0xzWd8P6etyYC7T+6Sd7O9gasrg6xKADrazkSRzNeZm361aKaTxtLW1GBDPAU5GekjaDVaceZjQ17/04I3KDw2H/glCXpJitm5p4XgORAfV/wBnqY/YDIjRYOUy4CTgDUhX30XUpy6Lyte7ZIpNUqTVvuDFKZ/tBkaQVX9a9S9dCdd3PRA/CazN8DC5Y5yqWxT0pIfunkiKx8xvDjWgCziWpNzsXcDR6ie2KtUY03wWcBqyGEwzPmL0eHFyhV5b2eDrDxOc9r1Fmuo4qk0I43zER1QLnqoRdhbTXPP+jpPvA9fob0wZEv0BYJp8Q/2uXQrh8g5ohqiPuAU4U4OV/25DKz0P/JZh0F9pGOfnJeUAAe5FSrN2AUcCK+hMgrcaE6ysUY34UJPH2qyuwkFaW1arKOa3lKeDu0a6g+ljO+LkK8CbgB3A/A5CGAfiFtVkp9M4ob0HeJc+IOMdgq/ISqqte9es0+2KDjYiqzj+lQdCnAb4gjr4twDLkFUkt9O5Za2qnvYpaeplnf7WeuB1yDCAMeBHCudj3o2tdQi+YMABzN31OU3210wvKk3qx71S/cFXIznE2V28WWEGWDpdvBAM6Ia6KWl9wZP0uC/Y14R/B3zAO06SNrkKWRZgJ3AYcBxweBcAjKZvhryt7JnbSofhG2QZ6ua1tuNgVrRR/xX4Q+r9q0k9CR9WEJ/V7x2HjAnphjjIJr2tnSVUiyyz8wog2qhl4PNIadQ4yX2tAO9FClh/pe+PQ3pN+lUGXfu58Ty1DNdamwkAfQjvAH4H6YJLG2j+dmR5+/363eV9DuGgSg1ZNHuW9z7NbRqbyRyPg/AeZJjlbtIHma/XNM0h1ZorgAWm/XInh3tBRbWByzTayn3pZJLRQfgQcC7w8wYQnoqU64Mkg4/RlIlJfmSOB1UjAA/MpAaMQvg4Mj74xwphUh/ry5Fyq2E13cs07Dftl49rnEO9L73awEqN5gFAB2EIPI0khR9VKJMgXIr0ZBylwcli2i9iMGnf/5vj+X9oJuFlKQ/jc3kB0KVoQqQbbj1wfwMIhxXU1eo/LlKTbDJzMt/z/5wJPimFoadaiYa72dHsEsJ7gbcgFcdpEKLm+CR9mjpVSWPmt7X0y7wITCWkJyuqKQONgLfnDUD/BA8CFyB9wo0g3ICUWu1UCFdi4357bX7nRVIuU9oWSWmYXWrtcgcgnhM7hQxQvz4DhLcBv029kiYvEBYl+FgYef8isuh1VKqe+Z2gXuCRKwD9Ew2Q3pCrqU9klHTCNwCXAL/UJ3IV0/slTbqn/XzzG6oFe0PC/lCvTm+ap3AGnq4Qyf9VNUqupWiXN+nF36cpgHka7ldmSDMUQZYzvf/XAfjpSFTs35ePAT+jhdrKmTJrITIAfR9wXoNGPls13wakunpEnd5JA7Dj2m+hZiB8pTCm1ueyBNdqD1JudyhPaZhGF9psJc2fAZ/QwMRV0sw1+DoqQ0gONhr5HlBXKM6tqiEDxZ6nPgAs9wD6kVUzlTSXagCzR9+vxIoYOqkUjubXC4XdUNV3pzyYt7bzkM70QjXRSpoDpFfSnA/cTL2SZgUye5Npv/bgW6BplkrETdoPvDHhOyHSc3WX/q/SjwD6EN6jF9uokuZsJKl9SEP/fqykyRN8hyPLaVRjeJhAZqSIimubu5COhpbMb14A9CHcjFTSbCe9iOEk6rNoHdQbuNh4ahq+UKPeUgwX+5GCkrkp3PzHoJkXl6B+CXAnUi3j4IyT55GZEkaRhPWzupn5bQxfgPS9j1Af2eib311INdNwjPYLkQkBzmwQPPaNBvQ1YTOVNEcgU2ssVFPQ7pw0RZIVHnxRJbAHuDwGPl8+TrZS/b58wp0PuACpnF7bQBOi5uJJJI+1G+nGqw3gvemE5nN+cyWiiAJPG34vwfcLVTGc4qVjWpa8LtfqLnQvkqjOUklzHzJ+9TmFsJMzMQySz7caSehXElygPciEAmnyMeInJx0YAB2EzVbS3Knacqfe5FW0Ngg+GED4DgeOpz6zbZSBIfWfr0BWMkhSCncCX2P6VMhtmbq8i0tO346U7J+sEAYJoFyEVGe4Kmt3w8cKCKBzQRYgvUezY0ym659/HngVcG3CcWpI6usCpAu1I3PqhH1yEx1sd2paYI1nAuJgeatGcQ9ooDKCdKQfzHDTBgm+IaSyfGkESP/hDvXhHAG+nWKNysDfAl+lPiUyRQAw+qT6lTRpEK5HChY2Ui+wHNFGGBtgAKNabyQFljLSBVoDtjaA70FkWbaADs6j3Y9OelwlTVI6YC0yuOYufQ2RfOFcvbHjAwSfuwcjSHJ5UQNNFSI9HRPADxLiAfeA70VK4/bS4ens+jVKdL0m25A+5FJKVHYyUrjwZWRiJOdwz6deeDnepxD6RaPzkR6hJepupC34PYRUOc8GnkiAz60mFQK/j1S9dMz0DsIT7yLitwFf9BzspMj+MWRaEJAE6xT1Cc0PIYUQB9Q8V3J6j2qR8xpWH3ee93A1AmQIyZMeT/pkni7v+jeadmmUgSgcgO5mTiLl4ndQX6E9SbOPIgUPP0N6T6oetE6LHlII3TbBzFRgx1mrWerLztHXYc8kVjN8v6YpqkuAf0rZd1Lv7TWalgkbaNTCAuhrwtM1QlvUAEKQCt4b1GwNK2TRvJjTJlPUp3cb96Dt1oI2pcg2pNq9rACWmoDOj3T36f7Xqj/XCL6bFdSuwTdIKQcH4WuQXpPlNO662wK8H6m8WZBiYkoxcEB3V1Typ8WtRR6IZo7hUiz7VPN/vsF33D27XeGbSkjfGIApEK5G+o9PzAAhqhH+Wf2/I3X/NA3Xi96jUpvfdQOJ9uv9+BfgNxv4li7guA54n5fe6urSZYPW5eSKGI4EbtI0jauwaXSt/6jBzG71JYc9rVPtIXyt/I7vwx5Q+F6BLM64rsF3fXflH6gv6NipCdwLBSCef1TSJ/+KmBudJjfq9qTCO6wOv9/I3TTBpQyfB0yf7/qgplXmIjV6f9JA40VN7igy8Otz3fb5igAgkSf4UuBT6udl1YYgVSE3IcNBn9FGchOfz9KGK3fBNAcJqReniSc0GHL+2SL1fX8XuDDjb/gafSsy6Ggr6UMhDMAWrs0NcDoB+Dek3J8mQXTyXaQH5sdIreEOdfBdpFztQnu47seyXss8DbBWIxMFnUtzs4j5vp7zfz+kGrQreb4iAxgNTlAz8xfUx4+kLbLTjLzgpWiStGGQ4e9SzLl3Yvyz055lLwPwEerFBz3XfEUTv2BhGTIg/hDTF1tJW2inXze3VIV7vwP4oAdiiC0x2/Mo2cmJSDJ61GugKc+c9jt0/jVsQ5LvSxLuhUmPtaF/81dr+uEXkYb0e0DyDKTfWxM9z0fV7TgyAp5pvRyCuACZIuRmpi9HG23oKepr41V7BKf7nYpu7hzi9t2OzGS1JgJaOY/g2ZNQD0J8J3whUku4BjgNeCXSb5xHeU5TKA8gRaOPMn3G+kbzMBqAOdOIcYsZLgd+AylhehkycP4YjVCHdTusC/eziiSYRzVVMopM2PlDTQc9ra+7YqLnGj1MKLcq/w8s9iiCvyZ2IwAAAABJRU5ErkJggg==');
                opacity: .75;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip-icon.is-equipped::after {
                background-color: var(--theme-accent-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip:hover .inventory-v49-equip-icon,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-equip:focus-visible .inventory-v49-equip-icon {
                opacity: 1;
                transform: scale(1.06);
            }
            /* V49.4 — Até três linhas com reticências; altura geral = 240px.
               O espaço adicional vem do cabeçalho, sem apertar rastreadores. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-description {
                display: -webkit-box !important;
                flex: 0 0 47px !important;
                width: 100% !important;
                height: 47px !important;
                min-height: 47px !important;
                max-height: 47px !important;
                min-width: 0 !important;
                overflow: hidden !important;
                margin: 0 !important;
                padding: 2px 11px 3px !important;
                white-space: normal !important;
                text-overflow: ellipsis !important;
                -webkit-box-orient: vertical !important;
                -webkit-line-clamp: 3 !important;
                line-clamp: 3;
                overflow-wrap: anywhere !important;
                color: var(--theme-text-secondary) !important;
                font-size: 10.5px;
                line-height: 14px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-abilities {
                flex: 1 1 auto !important;
                gap: 3px !important;
                padding: 6px 10px 7px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-skill-list {
                gap: 0 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill {
                flex: 0 0 28px !important;
                min-height: 28px !important;
                gap: 5px !important;
                border-bottom: 1px solid var(--theme-border-soft);
                border-left: 2px solid transparent;
                border-radius: 4px;
                padding: 0 2px 0 5px;
                transition: border-color .15s ease, background .15s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill:hover {
                border-left-color: var(--theme-accent);
                background: var(--theme-accent-softer) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill .inventory-v48-skill-name {
                font-size: 10.5px !important;
                line-height: 1.2;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill .inventory-v48-passive {
                font-size: 8.5px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill .inventory-v48-tracker {
                margin-left: auto;
                max-width: 110px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill .inventory-v11-mini-tracker {
                height: 22px !important;
                border: 1px solid var(--theme-border-soft) !important;
                border-radius: 6px !important;
                background: var(--theme-surface-raised) !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill .inventory-v11-mini-tracker button {
                width: 20px !important;
                height: 20px !important;
                font-size: 13px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill .inventory-v11-mini-tracker strong {
                min-width: 30px !important;
                font-size: 9.5px !important;
                line-height: 20px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-skill .inventory-v48-use {
                min-height: 20px !important;
                max-width: 106px;
                padding-inline: 5px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 8px;
            }
            .character-sheet-theme[data-theme].inventory-v49-skill-tooltip {
                position: fixed;
                z-index: 10000;
                pointer-events: none;
                width: min(310px, calc(100vw - 24px));
                max-height: min(235px, calc(100dvh - 24px));
                overflow: hidden;
                border: 1px solid var(--theme-border-strong);
                border-radius: 12px;
                background: var(--theme-surface-raised);
                color: var(--theme-text-primary);
                box-shadow: var(--theme-shadow-strong, 0 18px 38px rgba(0,0,0,.24));
                padding: 12px 13px;
                line-height: 1.48;
            }
            .character-sheet-theme[data-theme].inventory-v49-skill-tooltip strong {
                display: block;
                margin-bottom: 5px;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 13px;
                color: var(--theme-text-strong);
            }
            .character-sheet-theme[data-theme].inventory-v49-skill-tooltip p {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 8;
                font-size: 11px;
                white-space: pre-line;
                overflow-wrap: anywhere;
            }
            .character-sheet-theme[data-theme].inventory-v49-skill-tooltip small {
                display: block;
                margin-top: 8px;
                font-size: 9px;
                color: var(--theme-text-secondary);
            }
            @media (max-width: 400px) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card {
                    height: 252px !important;
                    min-height: 252px !important;
                    max-height: 252px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-description {
                    flex-basis: 47px !important;
                    height: 47px !important;
                    min-height: 47px !important;
                    max-height: 47px !important;
                }
            }


            /* ==============================================================
               SPELLBOUND V66.1.28 — Artefatos / Vestígios em Inventário
               Superfícies douradas locais, compatíveis com os 8 temas.
               ============================================================= */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card.inventory-v50-artifact {
                position: relative;
                border-color: rgba(185,135,49,.76) !important;
                background: radial-gradient(circle at 0% 0%, rgba(237,198,103,.20), transparent 65%),
                            var(--theme-surface-card) !important;
                box-shadow: inset 0 0 0 1px rgba(244,207,119,.19), 0 5px 16px rgba(113,77,21,.12) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card.inventory-v50-artifact::before {
                display: block !important;
                content: '';
                position: absolute;
                z-index: 2;
                top: 8px;
                bottom: 8px;
                left: 0;
                width: 3px;
                border-radius: 0 4px 4px 0;
                background: linear-gradient(#f2d789,#b57a25,#f2d789);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card.inventory-v50-artifact:hover,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card.inventory-v50-artifact:focus-visible {
                border-color: #d6a44f !important;
                background: radial-gradient(circle at 0% 0%, rgba(246,200,98,.24), transparent 66%),
                            var(--theme-card-hover) !important;
                box-shadow: inset 0 0 0 1px rgba(247,212,134,.25), 0 9px 22px rgba(113,77,21,.16) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v50-artifact .inventory-v48-art {
                border-color: rgba(183,135,56,.73) !important;
                background: radial-gradient(circle at 44% 38%, rgba(239,207,132,.18), transparent 75%),
                            var(--theme-surface-inset) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v50-artifact .inventory-v48-name {
                color: var(--theme-text-strong) !important;
                text-shadow: 0 0 12px rgba(207,151,53,.09);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v50-artifact .inventory-v48-abilities {
                border-top-color: rgba(190,143,66,.44) !important;
                background: linear-gradient(90deg,rgba(216,163,68,.09),transparent 76%) !important;
            }
            /* V66.1.90 — etapa do Vestígio integrada ao título dos poderes.
               Os 8 temas fornecem as cores; nenhum espaço adicional no topo. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v90-vestige-stage {
                display: inline-flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                gap: 4px;
                max-width: 48%;
                height: 14px;
                border: 1px solid color-mix(in srgb, var(--theme-accent) 42%, transparent);
                border-radius: 999px;
                padding: 0 6px;
                background: color-mix(in srgb, var(--theme-accent) 12%, var(--theme-surface-card) 88%);
                color: var(--theme-text-strong);
                font-size: 8px;
                font-weight: 800;
                letter-spacing: .005em;
                line-height: 1;
                text-transform: none;
                white-space: nowrap;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v90-vestige-stage > span {
                color: color-mix(in srgb, var(--theme-accent) 83%, var(--theme-text-strong) 17%);
                font-size: 10px;
                line-height: 1;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v90-vestige-stage b {
                min-width: 0;
                overflow: hidden;
                font-weight: 900;
                text-overflow: ellipsis;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v90-vestige-stage[x-cloak] {
                display: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v50-vestige .inventory-v48-abilities-header {
                color: var(--theme-text-strong) !important;
            }
            /* O modal teleportado é um elemento com data-theme, não descendente da página. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-artifact {
                border: 1px solid rgba(186,138,56,.82) !important;
                background: radial-gradient(circle at 0 0,rgba(236,187,82,.11),transparent 50%),
                            var(--theme-surface-card) !important;
                box-shadow: 0 24px 75px rgba(35,24,12,.36), inset 0 0 0 1px rgba(245,208,120,.19) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-artifact .inventory-v17-detail-topbar {
                border-bottom-color: rgba(183,137,55,.48) !important;
                background: linear-gradient(90deg,rgba(224,174,75,.19),rgba(224,174,75,.045)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-artifact .inventory-v10-thumb {
                border-color: rgba(183,137,55,.73) !important;
                background: radial-gradient(circle at 50% 35%,rgba(239,193,95,.20),transparent 70%),
                            var(--theme-surface-inset) !important;
                box-shadow: 0 0 0 4px rgba(209,159,66,.08) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-artifact .inventory-v17-item-title {
                color: var(--theme-text-strong) !important;
            }
            .inventory-v50-detail-stage-line {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 11px;
                color: #a8782a;
                font-size: 11px;
            }
            .inventory-v50-detail-stage-mark {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 25px;
                height: 25px;
                border: 1px solid #d3ae6a;
                border-radius: 8px;
                background: rgba(213,164,78,.14);
            }
            .inventory-v50-detail-stage-line strong {
                font-family: Georgia, serif;
                font-size: 15px;
            }
            .inventory-v50-detail-intro {
                margin-top: 13px;
                border-top: 1px solid rgba(186,139,65,.27);
                padding-top: 12px;
                white-space: pre-line;
                font-size: 12px;
                line-height: 1.7;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v50-detail-stage-line,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v50-detail-intro {
                color: var(--theme-text-primary) !important;
            }
            .inventory-v50-powers { display: grid; gap: 12px; }
            .inventory-v50-stage-panel {
                overflow: hidden;
                border: 1px solid rgba(188,142,66,.58);
                border-radius: 14px;
                background: rgba(216,165,76,.07);
            }
            .inventory-v50-stage-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
                border-bottom: 1px solid rgba(184,143,67,.34);
                background: linear-gradient(90deg,rgba(224,169,73,.19),rgba(224,169,73,.045));
                padding: 12px 14px;
            }
            .inventory-v50-stage-eyebrow {
                display: block;
                color: #a67b36;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .11em;
                text-transform: uppercase;
            }
            .inventory-v50-stage-header h3 {
                margin-top: 2px;
                font-family: Georgia, serif;
                font-size: 19px;
                font-weight: 900;
            }
            .inventory-v50-stage-status {
                border: 1px solid rgba(179,139,78,.36);
                border-radius: 999px;
                padding: 5px 9px;
                font-size: 9px;
                font-weight: 800;
            }
            .inventory-v50-stage-status.is-current {
                border-color: rgba(178,129,43,.75);
                background: rgba(231,174,67,.17);
            }
            .inventory-v50-stage-lore {
                padding: 12px 14px 0;
                font-size: 12px;
                line-height: 1.65;
                white-space: pre-line;
            }
            .inventory-v50-stage-features { display: grid; gap: 8px; padding: 12px 14px 14px; }
            .inventory-v50-stage-feature {
                border: 1px solid rgba(185,140,63,.34);
                border-radius: 10px;
                background: var(--theme-surface-card);
                padding: 11px 13px;
            }
            .inventory-v50-stage-feature-head {
                display: flex;
                align-items: baseline;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 6px 12px;
            }
            .inventory-v50-stage-feature-head strong {
                font-family: Georgia, serif;
                font-size: 15px;
            }
            .inventory-v50-stage-feature-head span {
                color: #ae8139;
                font-size: 9px;
                font-weight: 800;
            }
            .inventory-v50-stage-feature p {
                margin-top: 7px;
                white-space: pre-line;
                font-size: 12px;
                line-height: 1.7;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v50-powers,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v50-powers p,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v50-powers strong,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v50-powers h3 {
                color: var(--theme-text-primary) !important;
            }
            .inventory-v50-found-item { transition: border-color .15s ease, background .15s ease, box-shadow .15s ease; }
            .character-sheet-theme[data-theme] .inventory-v50-found-item.inventory-v50-found-artifact {
                border-color: rgba(187,142,60,.77) !important;
                background: radial-gradient(circle at 0 0,rgba(236,193,101,.18),transparent 65%),
                            var(--theme-surface-card) !important;
                box-shadow: inset 0 0 0 1px rgba(247,212,132,.14), 0 5px 15px rgba(111,78,23,.10);
            }
            .inventory-v50-found-badges { display: flex; flex-wrap: wrap; gap: 6px; margin: 5px 0 4px; }
            .inventory-v50-found-gold-badge,
            .inventory-v50-found-stage {
                display: inline-flex;
                align-items: center;
                min-height: 21px;
                border: 1px solid rgba(183,135,55,.56);
                border-radius: 999px;
                background: rgba(214,168,83,.15);
                padding: 2px 9px;
                color: #9a6b25;
                font-size: 9px;
                font-weight: 900;
            }
            .character-sheet-theme[data-theme] .inventory-v50-found-artifact .inventory-v50-found-name,
            .character-sheet-theme[data-theme] .inventory-v50-found-artifact .inventory-v50-found-badges span {
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v50-found-artifact .inventory-v50-found-claim {
                border-color: #b88536 !important;
                background: linear-gradient(145deg,#a87b2f,#78511b) !important;
                color: #fff8e8 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v50-found-artifact .inventory-v50-found-claim:hover {
                background: linear-gradient(145deg,#c08e3c,#8d6021) !important;
            }
            /* V66.1.29 — Evolução dourada dos Vestígios */
            .inventory-v51-topbar-stage {
                display: inline-flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                min-height: 26px;
                border: 1px solid rgba(207,161,75,.75);
                border-radius: 999px;
                background: linear-gradient(110deg,rgba(228,182,91,.24),rgba(187,132,43,.10));
                color: #dfb869;
                padding: 3px 11px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                white-space: nowrap;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v51-topbar-stage {
                color: var(--theme-text-strong) !important;
            }
            .inventory-v51-notice-layer {
                position: fixed;
                z-index: 500;
                top: max(18px, env(safe-area-inset-top));
                left: 50%;
                width: min(470px, calc(100vw - 24px));
                transform: translateX(-50%);
                pointer-events: none;
            }
            .inventory-v51-notice {
                position: relative;
                display: flex;
                align-items: center;
                gap: 15px;
                overflow: hidden;
                border: 1px solid #e4b65d;
                border-radius: 17px;
                background: radial-gradient(circle at 13% 20%,rgba(244,213,140,.21),transparent 48%),
                            linear-gradient(130deg,#372817,#5a3b17 65%,#312315);
                padding: 17px 17px 17px 19px;
                box-shadow: 0 0 0 2px rgba(239,190,89,.16),0 14px 45px rgba(22,15,7,.50),
                            0 0 35px rgba(245,194,80,.38);
                color: #ffe7ad;
                pointer-events: auto;
                animation: inventory-v51-notice-arrive .55s ease-out both;
            }
            .inventory-v51-notice::after {
                content: '';
                position: absolute;
                inset: -150% auto -150% -40%;
                width: 38%;
                transform: rotate(20deg);
                background: linear-gradient(90deg,transparent,rgba(255,239,172,.12),transparent);
                animation: inventory-v51-shine 3.3s ease-in-out 1;
                pointer-events: none;
            }
            .inventory-v51-notice-crest {
                display: flex;
                width: 47px;
                height: 47px;
                flex: 0 0 47px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(255,225,143,.70);
                border-radius: 13px;
                background: linear-gradient(155deg,rgba(249,210,118,.26),rgba(226,167,49,.10));
                box-shadow: 0 0 20px rgba(255,195,73,.26);
                color: #ffe1a0;
                font-size: 28px;
            }
            .inventory-v51-notice-copy { display:flex; flex:1; min-width:0; flex-direction:column; gap:3px; }
            .inventory-v51-notice-copy span {
                color: #d5ad64;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .13em;
                text-transform: uppercase;
            }
            .inventory-v51-notice-copy strong {
                color: #fff0c7;
                font-family: Georgia,serif;
                font-size: clamp(18px,3vw,24px);
                font-weight: 900;
                line-height: 1.2;
                overflow-wrap: anywhere;
            }
            .inventory-v51-notice-copy small { color: #d8b980; font-size: 11px; }
            .inventory-v51-notice-dismiss {
                display:flex; width:28px; height:28px; align-self:flex-start; flex:0 0 28px;
                align-items:center; justify-content:center; border:1px solid rgba(244,211,138,.35);
                border-radius:8px; background:rgba(0,0,0,.12); color:#ffe7b3;
                font-size:18px; cursor:pointer;
            }
            .inventory-v51-notice-dismiss:hover { background:rgba(255,223,147,.15); }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card.inventory-v51-ascending {
                border-color: #f3c768 !important;
                animation: inventory-v51-item-awaken 2.1s ease-in-out 4 !important;
                box-shadow: 0 0 0 2px rgba(244,200,105,.48),
                            0 0 30px rgba(251,200,87,.42),
                            inset 0 0 25px rgba(243,190,89,.18) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v51-ascending-detail {
                animation: inventory-v51-detail-awaken 2.1s ease-in-out 4 !important;
            }
            @keyframes inventory-v51-item-awaken {
                0%,100% { filter:brightness(1); }
                50% { filter:brightness(1.17) drop-shadow(0 0 12px rgba(252,201,90,.64)); }
            }
            @keyframes inventory-v51-detail-awaken {
                0%,100% { filter:brightness(1); }
                50% { filter:brightness(1.12) drop-shadow(0 0 18px rgba(244,193,82,.45)); }
            }
            @keyframes inventory-v51-notice-arrive {
                from { opacity:0; transform:translateY(-22px) scale(.96); }
                to { opacity:1; transform:translateY(0) scale(1); }
            }
            @keyframes inventory-v51-shine {
                from { left:-45%; }
                to { left:145%; }
            }
            @media (max-width: 620px) {
                .inventory-v51-notice { gap:10px; padding:12px; }
                .inventory-v51-notice-crest { width:37px; height:37px; flex-basis:37px; font-size:21px; }
                .inventory-v51-topbar-stage { padding:3px 7px; font-size:10px; }
            }
            @media (prefers-reduced-motion: reduce) {
                .inventory-v50-found-item { transition: none; }
                .inventory-v51-notice,
                .inventory-v51-notice::after,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card.inventory-v51-ascending,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v51-ascending-detail {
                    animation: none !important;
                }
            }
            /* V66.1.30 — Negrito seguro nas descrições e poderes salvos no Cofre. */
            .inventory-v35-detail-modal [x-html] strong,
            .inventory-v50-found-item [x-html] strong {font-weight: 850;color: inherit;}


            /* ================================================================
               SPELLBOUND V66.1.82 — INVENTÁRIO DO PLAYER / DETALHES DO ITEM
               Igualdade de hierarquia editorial com o Cofre do Mestre.
               Respeita os temas do personagem e não altera a lógica do item.
               ================================================================ */

            /* A etiqueta acima do nome existe só para Artefato / Vestígio.
               A classificação fica 1px maior em todos os itens. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-classification {
                font-size:14px !important;
                line-height:1.45 !important;
            }

            /* V66.1.83 — O espaçamento solicitado é HORIZONTAL, como uma
               escadinha de texto, não linhas vazias entre classificação e arma.
               O bloco mantém a distância vertical normal do Inventário. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-weapon-spacing {
                margin-top:9px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-weapon-spacing .inventory-v20-weapon-line {
                margin-left:20px !important;
                font-size:13px !important;
                line-height:1.45 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-weapon-spacing .inventory-v20-detail-line {
                margin-left:28px !important;
            }

            /* A descrição não é mais uma seção independente abaixo da foto;
               integra a coluna à direita, após Propriedades e Maestrias. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-inline-description {
                margin-top:13px;
                padding-top:12px;
                border-top:1px solid var(--theme-divider, rgba(205,187,159,.55));
                color:var(--theme-text-primary, #5f4636) !important;
                font-size:13px;
                font-weight:500;
                line-height:1.65;
                white-space:pre-line;
                overflow-wrap:anywhere;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-inline-description :is(p,div) {
                margin-bottom:.4em;
            }

            /* A visualização do item reconhece as naturezas/raridades do Cofre.
               Um Artefato Maravilhoso recebe apenas o ouro acetinado interno;
               não há halo externo nem alteração de opacidade dos textos. */
            @property --inventory-v82-gold-angle {
                syntax:'<angle>';
                inherits:false;
                initial-value:0deg;
            }
            @keyframes inventory-v82-perimeter {
                to { --inventory-v82-gold-angle:360deg; }
            }
            @keyframes inventory-v82-gold-reflection {
                0%,100% { background-position:130% 50%,center; }
                50%     { background-position:-30% 50%,center; }
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] {
                position:relative;
                isolation:isolate;
                border-color:rgba(190,150,81,.70) !important;
                box-shadow:0 24px 75px rgba(35,24,12,.33),inset 0 0 0 1px rgba(228,188,107,.16) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]::before {
                content:'';
                position:absolute;
                inset:0;
                z-index:5;
                padding:2px;
                border-radius:inherit;
                pointer-events:none;
                background:conic-gradient(from var(--inventory-v82-gold-angle),
                    transparent 0deg 302deg,
                    rgba(237,189,92,.10) 312deg,
                    rgba(255,243,196,.74) 332deg,
                    #fffcea 342deg,
                    rgba(216,169,79,.30) 356deg,
                    transparent 360deg);
                -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                -webkit-mask-composite:xor;
                mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                mask-composite:exclude;
                animation:inventory-v82-perimeter 8.5s linear infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-scroll {
                background:
                    linear-gradient(107deg,transparent 29%,rgba(243,203,122,.035) 40%,rgba(255,242,194,.20) 49%,rgba(246,208,130,.055) 58%,transparent 72%) 130% 50%/230% 100% no-repeat,
                    var(--theme-modal-surface,var(--theme-surface-card)) !important;
                animation:inventory-v82-gold-reflection 12s ease-in-out infinite;
            }
            /* Mundano, Tecnológico e demais raridades mantêm integralmente as
               cores já definidas pelos oito temas da ficha. */
            @media (max-width:640px) {
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-weapon-spacing {
                    margin-top:8px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-weapon-spacing .inventory-v20-weapon-line {
                    margin-left:14px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-weapon-spacing .inventory-v20-detail-line {
                    margin-left:20px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v82-inline-description {
                    font-size:12.5px;
                }
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-scroll {
                    animation:none !important;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.85 — ITENS CONSUMIDOS NO CARD DO PLAYER
               Preserva habilidades no card e transfere Descartar para a linha
               usada. Tons neutros compatíveis com os 8 temas da ficha.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used {
                --v85-discard-paper: color-mix(in srgb, var(--theme-surface-card) 87%, #929292 13%);
                --v85-discard-ink: color-mix(in srgb, var(--theme-text-primary) 91%, #8b8b8b 9%);
                --v85-discard-border: color-mix(in srgb, var(--theme-border-strong) 85%, #8f8f8f 15%);
                border-color: var(--v85-discard-border) !important;
                background: linear-gradient(125deg,
                    var(--v85-discard-paper),
                    color-mix(in srgb, var(--v85-discard-paper) 97%, #949494 3%)) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.10) !important;
                filter: none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used:is(:hover,:focus-visible) {
                border-color: var(--v85-discard-border) !important;
                background: var(--v85-discard-paper) !important;
                box-shadow:0 4px 13px rgba(39,39,39,.09) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used::before {
                content:none !important;
                display:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v48-art {
                border-color:var(--v85-discard-border) !important;
                background:color-mix(in srgb,var(--v85-discard-paper) 91%,#a6a6a6 9%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v48-art img {
                filter:grayscale(1) saturate(0) contrast(.97) !important;
                opacity:.91 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used :is(
                .inventory-v48-name, .inventory-v48-classification,
                .inventory-v48-technical, .inventory-v49-description,
                .inventory-v48-abilities-header) {
                color:var(--v85-discard-ink) !important;
                text-shadow:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v48-abilities {
                border-top-color:var(--v85-discard-border) !important;
                background:color-mix(in srgb,var(--v85-discard-paper) 95%,#8a8a8a 5%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card .inventory-v49-skill.inventory-v85-consumed-skill,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card .inventory-v49-skill.inventory-v85-consumed-skill:hover {
                border-bottom-color:var(--v85-discard-border, #aaa) !important;
                border-left-color:transparent !important;
                background:color-mix(in srgb, var(--theme-surface-card) 83%, #b5b5b5 17%) !important;
                cursor:default;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card .inventory-v85-consumed-skill .inventory-v48-skill-name,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card .inventory-v85-consumed-skill .inventory-v48-passive {
                color:color-mix(in srgb,var(--theme-text-strong) 78%,#777777 22%) !important;
                text-decoration:none !important;
                opacity:.85;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card .inventory-v85-consumed-skill .inventory-v48-tracker {
                max-width:128px !important;
                gap:4px;
            }
            /* ================================================================
               SPELLBOUND V66.1.88 — DESCARTE CENTRALIZADO E DISCRETO
               Botão fora das habilidades, sem ícone e sem tooltip acidental.
               O cinza acompanha a superfície de cada um dos oito temas.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v48-abilities-header {
                flex:0 0 12px !important;
                min-height:12px !important;
                gap:5px;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v48-skill-list {
                flex:0 0 auto;
                min-height:0;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v85-consumed-skill .inventory-v48-skill-name,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v85-consumed-skill .inventory-v48-passive {
                color:var(--v85-discard-ink) !important;
                opacity:.93 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v88-discard-footer {
                display:flex;
                align-items:center;
                justify-content:center;
                flex:0 0 auto;
                min-height:30px;
                width:100%;
                margin-top:auto;
                padding:3px 0 0;
                border:0;
                background:transparent;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card .inventory-v88-discard-footer[x-cloak] {
                display:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v88-discard-action {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                min-width:114px;
                min-height:26px;
                padding:3px 15px;
                border:1px solid color-mix(in srgb,var(--theme-border-strong) 82%,var(--theme-accent) 18%) !important;
                border-radius:8px;
                background:color-mix(in srgb,var(--theme-surface-raised) 82%,var(--theme-button-soft-bg) 18%) !important;
                color:var(--theme-text-primary) !important;
                box-shadow:none !important;
                font-family:inherit;
                font-size:10px;
                font-weight:800;
                line-height:1.25;
                letter-spacing:.015em;
                text-transform:none;
                white-space:nowrap;
                opacity:1 !important;
                cursor:pointer;
                transition:background .16s ease,border-color .16s ease,color .16s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v88-discard-action:is(:hover,:focus-visible) {
                border-color:var(--theme-accent) !important;
                background:color-mix(in srgb,var(--theme-surface-raised) 62%,var(--theme-button-soft-bg) 38%) !important;
                color:var(--theme-text-strong) !important;
                outline:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v88-discard-action:focus-visible {
                outline:2px solid var(--theme-accent);
                outline-offset:2px;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v43-item-used .inventory-v88-discard-action:disabled {
                opacity:.52 !important;
                cursor:wait;
            }
            /* Temas escuros: mantém a habilidade sombreada sem criar um
               retângulo cinza-claro desconectado da paleta da ficha. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v48-item-card .inventory-v49-skill.inventory-v85-consumed-skill,
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v48-item-card .inventory-v49-skill.inventory-v85-consumed-skill:hover {
                background:color-mix(in srgb,var(--theme-surface-card) 91%,#a3a3a3 9%) !important;
            }


            /* ================================================================
               SPELLBOUND V66.1.89 — CONFIRMAÇÃO DE DESCARTE TEMÁTICA
               Modal teleportado: herda data-theme e os tokens da ficha.
               Nenhum vinho, vermelho, bege ou texto escuro fixo.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v89-discard-modal {
                border-color: var(--theme-modal-border-strong, var(--theme-border-strong)) !important;
                background: var(--theme-modal-surface, var(--theme-surface-card)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 24px 60px rgba(0,0,0,.3)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-head {
                border-bottom: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-eyebrow {
                color: var(--theme-modal-label, var(--theme-label)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-title,
            .character-sheet-theme[data-theme] .inventory-v89-discard-body strong {
                color: var(--theme-modal-title, var(--theme-text-strong)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-body {
                background: var(--theme-modal-surface, var(--theme-surface-card)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-body p {
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-footer {
                border-top: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface-soft, var(--theme-paper)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-keep {
                border: 1px solid var(--theme-modal-border, var(--theme-border));
                background: var(--theme-button-soft-bg) !important;
                color: var(--theme-button-soft-text) !important;
                transition: border-color .15s, background .15s;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-keep:is(:hover,:focus-visible) {
                border-color: var(--theme-accent) !important;
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-confirm {
                border: 1px solid transparent;
                background: var(--theme-button-bg, var(--theme-accent-strong)) !important;
                color: var(--theme-button-text, var(--theme-on-accent)) !important;
                transition: background .15s, border-color .15s;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-confirm:is(:hover,:focus-visible) {
                background: var(--theme-button-hover, var(--theme-accent-deep)) !important;
                color: var(--theme-button-text, var(--theme-on-accent)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-modal button:focus-visible {
                outline: 2px solid var(--theme-focus, var(--theme-accent)) !important;
                outline-offset: 2px;
            }
            .character-sheet-theme[data-theme] .inventory-v89-discard-modal button:disabled {
                opacity: .52 !important;
                cursor: wait;
            }

            /* ================================================================
               SPELLBOUND V66.1.89 — DESCRIÇÃO MAIS PRÓXIMA DA IMAGEM
               O topo devolve espaço para 4 linhas de descrição; cards com
               metadados adicionais mantêm cabeçalho ligeiramente maior.
               A altura geral de 240px e a seção Habilidades não mudam.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-hero {
                flex: 0 0 88px !important;
                height: 88px !important;
                padding: 7px 11px 3px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-description {
                flex: 0 0 63px !important;
                height: 63px !important;
                min-height: 63px !important;
                max-height: 63px !important;
                -webkit-line-clamp: 4 !important;
                line-clamp: 4;
                padding-top: 2px !important;
                padding-bottom: 3px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-hero.inventory-v89-hero-complex {
                flex-basis: 98px !important;
                height: 98px !important;
                padding: 8px 11px 3px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-hero.inventory-v89-hero-complex + .inventory-v49-description {
                flex-basis: 53px !important;
                height: 53px !important;
                min-height: 53px !important;
                max-height: 53px !important;
                -webkit-line-clamp: 3 !important;
                line-clamp: 3;
            }
            @media (max-width: 400px) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-hero {
                    flex-basis: 88px !important;
                    height: 88px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v49-description {
                    flex-basis: 63px !important;
                    height: 63px !important;
                    min-height: 63px !important;
                    max-height: 63px !important;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.91 — CARTÃO DO VESTÍGIO EQUILIBRADO
               Uma hierarquia clara dentro dos mesmos 240px:
               retrato/identificação -> 3 linhas de resumo -> poderes.
               Sem alterar o restante da grade ou a progressão do Vestígio.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v50-vestige {
                isolation:isolate;
                border-color:color-mix(in srgb,#c39b58 67%,var(--theme-border-strong) 33%) !important;
                box-shadow:inset 0 1px 0 rgba(245,209,131,.12),
                           0 4px 13px rgba(80,61,30,.075) !important;
            }
            /* Não deixar uma barra dourada fixa na borda esquerda. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card.inventory-v50-vestige::before {
                display:none !important;
                content:none !important;
            }
            /* Tanto Vestígios armados quanto os de outras categorias recebem
               a mesma área de descrição. Evita que o resumo toque o rodapé. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-hero,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-hero.inventory-v89-hero-complex {
                flex:0 0 96px !important;
                height:96px !important;
                min-height:96px !important;
                gap:10px !important;
                padding:9px 11px 3px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-name {
                font-size:16px !important;
                line-height:1.18 !important;
                letter-spacing:-.012em;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-classification {
                font-size:9.6px !important;
                line-height:1.4 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-technical {
                opacity:.93 !important;
                font-size:9px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v49-description,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-hero.inventory-v89-hero-complex + .inventory-v49-description {
                display:-webkit-box !important;
                flex:0 0 55px !important;
                height:55px !important;
                min-height:55px !important;
                max-height:55px !important;
                overflow:hidden !important;
                padding:3px 11px 6px !important;
                margin:0 !important;
                font-size:10.5px !important;
                line-height:14px !important;
                -webkit-box-orient:vertical !important;
                -webkit-line-clamp:3 !important;
                line-clamp:3;
                text-overflow:ellipsis !important;
                color:var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-abilities {
                flex:1 1 auto !important;
                min-height:0 !important;
                padding:7px 10px 7px !important;
                gap:3px !important;
                border-top:1px solid color-mix(in srgb,#c39b58 42%,var(--theme-border-soft) 58%) !important;
                background:linear-gradient(100deg,
                   color-mix(in srgb,#c89d55 6%,transparent),transparent 80%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-abilities-header {
                flex:0 0 15px !important;
                min-height:15px !important;
                font-size:8px !important;
                line-height:15px !important;
                letter-spacing:.085em;
                gap:9px;
            }
            /* O grau é parte do título dos poderes, não uma segunda insígnia
               flutuante. Oito temas: fundo ausente e texto semântico. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v90-vestige-stage {
                height:auto !important;
                min-height:14px !important;
                max-width:40% !important;
                border:0 !important;
                border-radius:0 !important;
                padding:0 !important;
                background:transparent !important;
                box-shadow:none !important;
                gap:4px !important;
                color:color-mix(in srgb,var(--theme-text-strong) 70%,#bb914f 30%) !important;
                font-size:8px !important;
                letter-spacing:.055em !important;
                line-height:14px !important;
                text-transform:uppercase !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v90-vestige-stage > span {
                color:#d0a55f !important;
                font-size:10px !important;
                line-height:1 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-skill-list {
                gap:0 !important;
                padding:0 !important;
                min-height:0 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v49-skill {
                flex:0 0 27px !important;
                min-height:27px !important;
            }
            @media(max-width:400px) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v48-hero,
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v48-hero.inventory-v89-hero-complex {
                    flex-basis:96px !important;
                    height:96px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v49-description {
                    flex-basis:55px !important;
                    height:55px !important;
                    min-height:55px !important;
                    max-height:55px !important;
                }
            }

            /* ================================================================
               SPELLBOUND V66.1.92 — CARD VESTÍGIO: PROGRESSÃO COMPACTA
               Mantém os 240px e todos os tokens dos oito temas; não toca nas
               habilidades nem na progressão do modal de detalhes.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v49-description.inventory-v92-vestige-description,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-hero.inventory-v89-hero-complex + .inventory-v49-description.inventory-v92-vestige-description {
                display:block !important;
                flex:0 0 53px !important;
                box-sizing:border-box !important;
                width:100% !important;
                height:53px !important;
                min-height:53px !important;
                max-height:53px !important;
                margin:0 !important;
                padding:3px 11px 8px !important;
                overflow:hidden !important;
                -webkit-line-clamp:unset !important;
                line-clamp:unset !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-description-copy {
                display:-webkit-box !important;
                max-height:42px !important;
                overflow:hidden !important;
                -webkit-box-orient:vertical !important;
                -webkit-line-clamp:3 !important;
                line-clamp:3;
                font-size:10.5px;
                line-height:14px;
                overflow-wrap:anywhere;
                text-overflow:ellipsis;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-hero,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-hero.inventory-v89-hero-complex {
                flex:0 0 96px !important;
                height:96px !important;
                min-height:96px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-abilities {
                flex:1 1 auto !important;
                min-height:0 !important;
                padding:8px 10px 9px !important;
                gap:8px !important;
                overflow:hidden !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v48-abilities-header {
                flex:0 0 15px !important;
                min-height:15px !important;
                color:var(--theme-text-strong) !important;
                font-size:9px !important;
                letter-spacing:.09em !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stages {
                display:grid;
                grid-template-columns:repeat(3,minmax(0,1fr));
                gap:6px;
                width:100%;
                min-height:29px;
                margin-top:2px;
                pointer-events:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stages[x-cloak] {
                display:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage {
                display:inline-flex;
                min-width:0;
                height:27px;
                align-items:center;
                justify-content:center;
                gap:5px;
                padding:0 5px;
                border:1px solid var(--theme-border-soft);
                border-radius:7px;
                background:color-mix(in srgb,var(--theme-surface-card) 87%,var(--theme-accent) 13%);
                color:var(--theme-text-secondary);
                font-size:9px;
                font-weight:750;
                white-space:nowrap;
                text-overflow:ellipsis;
                overflow:hidden;
                opacity:.65;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage.is-reached {
                opacity:.86;
                color:var(--theme-text-primary);
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage.is-current {
                opacity:1;
                border-color:color-mix(in srgb,#d3a75d 58%,var(--theme-border-strong) 42%);
                background:linear-gradient(112deg,
                    color-mix(in srgb,var(--theme-surface-card) 86%,#d3a75d 14%),
                    color-mix(in srgb,var(--theme-surface-card) 91%,#edcd8d 9%));
                color:var(--theme-text-strong);
                box-shadow:inset 0 1px 0 color-mix(in srgb,#ffe8ba 18%,transparent);
                font-weight:900;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-stage-marker {
                width:4px;
                height:4px;
                flex:0 0 4px;
                border-radius:50%;
                background:currentColor;
                opacity:.48;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage.is-current .inventory-v92-stage-marker {
                width:6px;
                height:6px;
                flex-basis:6px;
                background:#d3a75d;
                opacity:1;
                box-shadow:0 0 6px color-mix(in srgb,#d3a75d 38%,transparent);
            }
            @media(max-width:400px) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v92-vestige-stages {gap:4px;}
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v92-vestige-stage {font-size:8px;padding:0 3px;gap:3px;}
            }



            /* ==================================================================
               SPELLBOUND V66.1.93 — LUZES PROGRESSIVAS DOS VESTÍGIOS
               - Todas as etapas alcançadas permanecem iluminadas.
               - A etapa atual tem uma luz mais intensa.
               - O evento já existente de ascensão (inventory-v51-ascending)
                 acende os indicadores em sequência uma única vez.
               - Sem alterar níveis, persistência, tooltips nem modais.
               - Cores harmonizadas com os 8 temas por meio dos tokens da ficha.
               ================================================================== */

            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage {
                position:relative;
                isolation:isolate;
                transition:background .38s ease, border-color .38s ease,
                           box-shadow .38s ease, color .38s ease, opacity .38s ease;
            }

            /* Inerte -> Desperta -> Exaltada: ficam ACESAS cumulativamente. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage.is-reached {
                opacity:1;
                border-color:color-mix(in srgb,#cba15b 49%,var(--theme-border-soft) 51%);
                background:linear-gradient(125deg,
                    color-mix(in srgb,var(--theme-surface-card) 88%,#d6ac65 12%),
                    color-mix(in srgb,var(--theme-surface-card) 93%,#f6dca0 7%));
                color:var(--theme-text-primary);
                box-shadow:inset 0 1px 0 color-mix(in srgb,#fff1cb 22%,transparent),
                           0 0 6px color-mix(in srgb,#d9aa5f 10%,transparent);
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage.is-reached .inventory-v92-stage-marker {
                width:6px;
                height:6px;
                flex-basis:6px;
                opacity:1;
                background:#e1b56b;
                box-shadow:0 0 0 2px rgba(224,177,96,.13),
                           0 0 7px rgba(235,188,103,.68),
                           0 0 12px rgba(224,169,75,.26);
            }

            /* A etapa vigente continua sobressaindo, sem apagar as anteriores. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage.is-current {
                opacity:1;
                border-color:color-mix(in srgb,#e2b96f 73%,var(--theme-border-strong) 27%);
                background:linear-gradient(115deg,
                    color-mix(in srgb,var(--theme-surface-card) 80%,#d6a75c 20%),
                    color-mix(in srgb,var(--theme-surface-card) 88%,#ffe0a0 12%));
                box-shadow:inset 0 1px 0 color-mix(in srgb,#ffedc3 30%,transparent),
                           0 0 9px color-mix(in srgb,#e1ae58 19%,transparent);
                color:var(--theme-text-strong);
                font-weight:900;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage.is-current .inventory-v92-stage-marker {
                width:7px;
                height:7px;
                flex-basis:7px;
                background:#f1cb83;
                box-shadow:0 0 0 2px rgba(240,192,113,.18),
                           0 0 9px rgba(245,203,126,.88),
                           0 0 15px rgba(231,172,78,.45);
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage:not(.is-reached) .inventory-v92-stage-marker {
                opacity:.24;
                box-shadow:none;
            }

            /* Pequeno halo no interior de cada indicador, sem brilho no entorno
               do card nem filete estacionário em qualquer lateral. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage::after {
                content:'';
                position:absolute;
                inset:0;
                border-radius:inherit;
                pointer-events:none;
                z-index:1;
                background:radial-gradient(ellipse 66% 110% at 25% 50%,
                    rgba(255,244,208,.78),rgba(237,192,104,.23) 48%,transparent 88%);
                opacity:0;
            }
            @keyframes inventory-v93-awaken-light {
                0%,100% {filter:brightness(1);transform:scale(1);}
                26% {filter:brightness(1.45);transform:scale(1.018);}
                53% {filter:brightness(1.22);transform:scale(1.008);}
                70% {filter:brightness(1.38);transform:scale(1.012);}
            }
            @keyframes inventory-v93-awaken-halo {
                0%,100% {opacity:0;}
                30% {opacity:.68;}
                67% {opacity:.25;}
            }
            @keyframes inventory-v93-awaken-dot {
                0%,100% {transform:scale(1);}
                27% {transform:scale(1.65);}
                57% {transform:scale(.96);}
                72% {transform:scale(1.18);}
            }

            /* A classe de ascensão já existia: usamos o mesmo evento para
               acender as etapas da esquerda para a direita apenas uma vez. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige.inventory-v51-ascending .inventory-v92-vestige-stage.is-reached {
                animation:inventory-v93-awaken-light 1.55s ease-out both;
                animation-delay:var(--inventory-v93-light-delay,0s);
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige.inventory-v51-ascending .inventory-v92-vestige-stage.is-reached::after {
                animation:inventory-v93-awaken-halo 1.55s ease-out both;
                animation-delay:var(--inventory-v93-light-delay,0s);
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige.inventory-v51-ascending .inventory-v92-vestige-stage.is-reached .inventory-v92-stage-marker {
                animation:inventory-v93-awaken-dot 1.55s ease-out both;
                animation-delay:var(--inventory-v93-light-delay,0s);
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage:nth-child(1) {--inventory-v93-light-delay:.08s;}
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage:nth-child(2) {--inventory-v93-light-delay:.37s;}
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v50-vestige .inventory-v92-vestige-stage:nth-child(3) {--inventory-v93-light-delay:.66s;}

            @media (prefers-reduced-motion: reduce) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v92-vestige-stage,
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v92-vestige-stage.is-reached,
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v92-vestige-stage.is-reached::after,
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v50-vestige .inventory-v92-vestige-stage.is-reached .inventory-v92-stage-marker {
                    animation:none !important;
                    transition:none !important;
                }
            }



            /* =====================================================================
               SPELLBOUND V66.1.94 — COFRE -> INVENTÁRIO: PORT DOS MATERIAIS
               Fonte: arquivos do Cofre enviados em 07/11/2026.
               Material, circuito e efeitos de perímetro seguem os mesmos valores
               do Cofre. Só os seletores e dimensões mudam para o card do Player.
               APENAS Mundano/Tecnológico × Lendário/Artefato.
               Vestígios, maravilhosos, consumidos e controles NÃO são afetados.
               ===================================================================== */
            @property --vault-v41-metal-angle {
                syntax:'<angle>'; inherits:false; initial-value:0deg;
            }
            @property --vault-v52-edge-angle {
                syntax:'<angle>'; inherits:false; initial-value:0deg;
            }
            @keyframes vault-v41-metal-sun-pass { to { --vault-v41-metal-angle:360deg; } }
            @keyframes vault-v52-edge-orbit { to { --vault-v52-edge-angle:360deg; } }
            @keyframes vault-v61-silver-artifact-glint {
                0%,18% { background-position:0 0,0 0,132% 0; opacity:.24; }
                30% { background-position:0 0,0 0,74% 0; opacity:.40; }
                46% { background-position:0 0,0 0,18% 0; opacity:.56; }
                58% { background-position:0 0,0 0,-18% 0; opacity:.48; }
                76% { background-position:0 0,0 0,-92% 0; opacity:.28; }
                100% { background-position:0 0,0 0,-132% 0; opacity:.24; }
            }
            @keyframes vault-v46-soft-interior-glint {
                0%,21% {background-position:0 0,115% 0;opacity:.13;}
                37% {opacity:.30;}
                55% {background-position:0 0,-115% 0;opacity:.23;}
                66%,100% {background-position:0 0,-115% 0;opacity:.13;}
            }
            @keyframes vault-v60-mundane-silver-aura {
                0%,100% {box-shadow:0 0 0 1px rgba(248,250,255,.32),0 0 10px rgba(194,204,223,.10);}
                36% {box-shadow:0 0 0 2px rgba(255,255,255,.93),0 0 18px 5px rgba(222,230,245,.66),0 0 32px 9px rgba(167,179,202,.28);}
                59% {box-shadow:0 0 0 1px rgba(245,249,255,.60),0 0 15px 3px rgba(219,226,239,.37);}
            }
            @keyframes vault-v49-tech-core {
                0%,100% {background-position:6% 8%,95% 96%,0% 50%,center,center;}
                50% {background-position:96% 68%,7% 17%,100% 50%,center,center;}
            }
            @keyframes vault-v49-tech-inside {
                0%,100% {opacity:.46;background-position:9% 10%,0% 50%,12% 82%;}
                45% {opacity:.77;background-position:88% 65%,100% 50%,86% 22%;}
                70% {opacity:.60;background-position:72% 19%,60% 50%,55% 66%;}
            }
            @keyframes vault-v51-trace-forward {
                0%,12% {opacity:.08;stroke-dashoffset:155;}
                34% {opacity:.78;}
                63% {opacity:.33;stroke-dashoffset:-120;}
                100% {opacity:.08;stroke-dashoffset:-215;}
            }
            @keyframes vault-v51-trace-reverse {
                0%,10% {opacity:.06;stroke-dashoffset:-165;}
                43% {opacity:.66;}
                72% {opacity:.29;stroke-dashoffset:110;}
                100% {opacity:.06;stroke-dashoffset:185;}
            }
            @keyframes vault-v51-trace-branch {
                0%,22% {opacity:.08;stroke-dashoffset:135;}
                51% {opacity:.63;}
                77% {opacity:.22;stroke-dashoffset:-90;}
                100% {opacity:.08;stroke-dashoffset:-155;}
            }
            @keyframes vault-v51-orbit-a {
                0%,100% {transform:translate3d(0,0,0) scale(.6);opacity:.16;}
                24% {transform:translate3d(17px,-29px,0) scale(1.1);opacity:.85;}
                58% {transform:translate3d(-9px,-47px,0) scale(.72);opacity:.24;}
                80% {transform:translate3d(-22px,12px,0) scale(1.25);opacity:.72;}
            }
            @keyframes vault-v51-orbit-b {
                0%,100% {transform:translate3d(0,0,0) scale(1);opacity:.13;}
                27% {transform:translate3d(-16px,18px,0) scale(.55);opacity:.72;}
                50% {transform:translate3d(15px,38px,0) scale(1.15);opacity:.25;}
                76% {transform:translate3d(24px,-19px,0) scale(.8);opacity:.85;}
            }
            @keyframes vault-v51-orbit-c {
                0%,100% {transform:translate3d(0,0,0) scale(.7);opacity:.12;}
                23% {transform:translate3d(13px,14px,0) scale(1.05);opacity:.7;}
                53% {transform:translate3d(-13px,-22px,0) scale(.7);opacity:.24;}
                76% {transform:translate3d(19px,-11px,0) scale(1.1);opacity:.83;}
            }
            @keyframes vault-v51-orbit-d {
                0%,100% {transform:translate3d(0,0,0) scale(.6);opacity:.12;}
                26% {transform:translate3d(-13px,-19px,0) scale(.9);opacity:.72;}
                49% {transform:translate3d(7px,17px,0) scale(.7);opacity:.25;}
                79% {transform:translate3d(-17px,4px,0) scale(1.15);opacity:.82;}
            }
            @keyframes inventory-v97-ghost-fade-a {
                0%,14%,100% {opacity:0;}
                26% {opacity:.10;}
                45% {opacity:.38;}
                68% {opacity:.20;}
                84% {opacity:.04;}
            }
            @keyframes inventory-v97-ghost-fade-b {
                0%,10%,100% {opacity:0;}
                32% {opacity:.12;}
                52% {opacity:.34;}
                72% {opacity:.18;}
                88% {opacity:.05;}
            }
            @keyframes inventory-v97-ghost-fade-c {
                0%,18%,100% {opacity:0;}
                38% {opacity:.09;}
                57% {opacity:.30;}
                76% {opacity:.16;}
                90% {opacity:.03;}
            }
            @keyframes inventory-v97-ghost-fade-d {
                0%,12%,100% {opacity:0;}
                29% {opacity:.11;}
                48% {opacity:.32;}
                67% {opacity:.17;}
                86% {opacity:.04;}
            }
            /* Escopo: somente os quatro tipos, inclusive em temas escuros. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]),
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                position:relative;
                isolation:isolate;
                transition:border-color .24s ease,box-shadow .24s ease,background .24s ease !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) > :is(.inventory-v48-hero,.inventory-v49-description,.inventory-v48-abilities),
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) > :is(.inventory-v48-hero,.inventory-v49-description,.inventory-v48-abilities) {
                position:relative;
                z-index:2;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-energy[x-cloak] {
                display:none !important;
            }

            /* MUNDANO LENDÁRIO — mesmas cores de aço polido do Cofre. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="legendary"] {
                border-color:#a7adb3 !important;
                background:linear-gradient(124deg,#fafafa 0%,#f0f1f2 45%,#fafbfc 100%) !important;
                box-shadow:0 2px 10px rgba(55,59,66,.08),inset 0 1px 0 #fff !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="legendary"]:hover {
                border-color:#939da7 !important;
                box-shadow:0 9px 22px rgba(55,59,66,.15),inset 0 1px 0 #fff !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="legendary"] .inventory-v48-art {
                background:linear-gradient(137deg,#f9f9f9 0%,#e7e9eb 42%,#bfc5cb 88%,#eeeef0) !important;
                border-color:#b6bcc2 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="legendary"] .inventory-v48-abilities {
                background:#cfd6df !important;border-top-color:rgba(116,128,144,.44) !important;
            }
            /* MUNDANO ARTEFATO — prata perolada, brilho mais intenso. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="artifact"] {
                border:1.5px solid #bcc0c8 !important;
                background:
                    radial-gradient(ellipse 95% 150% at 91% 20%,rgba(255,255,255,.96) 0%,transparent 68%),
                    radial-gradient(ellipse 78% 140% at 9% 80%,rgba(178,180,186,.56) 0%,transparent 76%),
                    linear-gradient(113deg,#cbced4 0%,#fdfdfd 32%,#fff 47%,#c8cbd2 80%,#fdfdfd 100%) !important;
                box-shadow:0 0 0 2px rgba(255,255,255,.90),0 9px 30px rgba(93,98,119,.20),inset 0 0 25px rgba(255,255,255,.94) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="artifact"]:hover {
                border-color:#989da9 !important;
                box-shadow:0 0 0 2px rgba(255,255,255,.98),0 15px 37px rgba(99,105,122,.27),0 0 23px rgba(219,224,235,.55),inset 0 0 30px rgba(255,255,255,.98) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="artifact"] .inventory-v48-art {
                background:radial-gradient(circle at 44% 35%,#fff 2%,#eeeff1 36%,#aeb3bd 78%,#d9dbe0 100%) !important;
                border-color:rgba(138,145,159,.66) !important;
                box-shadow:inset 0 0 28px rgba(255,255,255,.85),inset 0 0 0 1px rgba(255,255,255,.72) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="artifact"] .inventory-v48-abilities {
                background:#d6dde6 !important;border-top-color:rgba(128,141,161,.48) !important;
            }
            /* Reflexo PERIMETRAL: apenas o perímetro, nunca a barra da esquerda. */
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                    content:'' !important;display:block !important;
                    position:absolute !important;inset:0 !important;
                    width:auto !important;height:auto !important;
                    z-index:6 !important;padding:1.6px !important;
                    border-radius:inherit !important;
                    pointer-events:none !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    box-shadow:none !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="legendary"]::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="legendary"]::before {
                    background:conic-gradient(from var(--vault-v41-metal-angle),
                        transparent 0deg 302deg,#979da4 315deg,#c4c8cd 324deg,#e9ebee 333deg,#fff 341deg,#bec4c9 350deg,transparent 360deg) !important;
                    opacity:.95;animation:vault-v41-metal-sun-pass 10s linear infinite !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="artifact"]::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="artifact"]::before {
                    background:conic-gradient(from var(--vault-v41-metal-angle),
                        transparent 0deg 296deg,#8e939d 307deg,#d8dbe2 320deg,#fff 331deg,#f6f8fc 339deg,#aeb2bc 348deg,transparent 360deg) !important;
                    filter:drop-shadow(0 0 2px rgba(238,241,252,.8));
                    animation:vault-v41-metal-sun-pass 4.7s linear infinite !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"]::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="legendary"]::before {
                    background:conic-gradient(from var(--vault-v52-edge-angle),
                        transparent 0deg 306deg,rgba(139,209,227,.08) 315deg,rgba(188,235,245,.42) 325deg,#fff 335deg,rgba(155,219,237,.63) 342deg,transparent 355deg 360deg) !important;
                    opacity:.78;animation:vault-v52-edge-orbit 14s linear infinite !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"]::before {
                    padding:2px !important;
                    background:conic-gradient(from var(--vault-v52-edge-angle),
                        transparent 0deg 298deg,rgba(100,193,223,.06) 306deg,rgba(100,202,230,.30) 314deg,
                        rgba(153,234,252,.87) 325deg,#faffff 335deg,rgba(125,223,247,.96) 343deg,rgba(84,194,229,.34) 351deg,transparent 359deg 360deg) !important;
                    filter:drop-shadow(0 0 2.5px rgba(96,207,238,.62));
                    animation:vault-v52-edge-orbit 8.7s linear infinite !important;
                }
            }
            /* As superfícies de texto acompanham o METAL do Cofre,
               não o fundo vinho/cinza herdado de cada tema. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-name {color:#454b54 !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.inventory-v48-classification,.inventory-v48-technical,.inventory-v49-description,.inventory-v48-abilities-header,.inventory-v48-passive) {color:#596371 !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-skill-name {color:#444d5b !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v49-skill {border-bottom-color:rgba(121,133,150,.36) !important;}

            /* TECNOLÓGICO LENDÁRIO — mesmas superfícies azul perolado do Cofre. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"] {
                border-color:#8db9cc !important;
                background:linear-gradient(132deg,#f7fbfd 0%,#eef6fa 58%,#e4f1f6 100%) !important;
                box-shadow:0 0 0 1px rgba(129,181,204,.18),0 8px 23px rgba(86,142,170,.12) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] {
                border-color:#90b5cf !important;
                background:linear-gradient(132deg,#f7fbfe 0%,#eef4fb 58%,#e7f0fb 100%) !important;
                box-shadow:0 0 0 1px rgba(123,157,198,.17),0 8px 24px rgba(88,119,168,.12) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"]:hover {
                border-color:#6fa0ba !important;
                box-shadow:0 0 0 1px rgba(120,178,205,.22),0 14px 30px rgba(83,144,175,.18) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]:hover {
                border-color:#7098bf !important;
                box-shadow:0 0 0 1px rgba(109,151,198,.22),0 14px 30px rgba(87,122,175,.18) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"] .inventory-v48-art {
                background:radial-gradient(ellipse at 50% 36%,#d7f1fb 0%,#89bfd7 56%,#5e93ad 82%,#446f89 100%) !important;
                border-color:#8db9cc !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art {
                background:radial-gradient(ellipse at 50% 35%,#ddeeff 0%,#98bbdc 56%,#688db7 80%,#4c6f99 100%) !important;
                border-color:#90b5cf !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-abilities {
                border-top-color:rgba(122,180,204,.28) !important;
                background:linear-gradient(115deg,rgba(218,240,248,.58),rgba(236,248,252,.66)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-name {color:#29566e !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.inventory-v48-classification,.inventory-v48-technical,.inventory-v49-description,.inventory-v48-abilities-header,.inventory-v48-passive) {color:#47697b !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-skill-name {color:#326c88 !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v49-skill {border-bottom-color:rgba(104,169,195,.30) !important;}
            /* A iluminação atravessa o CORPO; os pseudo-elementos não interceptam cliques. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::after,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::after {
                content:'';display:block;position:absolute;inset:1px;z-index:0;
                border-radius:inherit;pointer-events:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="legendary"]::after {
                background:radial-gradient(ellipse 70% 95% at 81% 16%,rgba(255,255,255,.62),transparent 73%),
                           linear-gradient(112deg,transparent 36%,rgba(255,255,255,.45) 49%,transparent 62%) 115% 0/240% 100% no-repeat;
                opacity:.20;animation:vault-v46-soft-interior-glint 13s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="artifact"]::after {
                background:
                    radial-gradient(ellipse 72% 108% at 52% 48%,rgba(255,255,255,.28),transparent 72%),
                    radial-gradient(ellipse 52% 72% at 78% 22%,rgba(255,255,255,.40),transparent 70%),
                    linear-gradient(108deg,transparent 0%,transparent 34%,rgba(255,255,255,.10) 41%,rgba(255,255,255,.58) 48%,rgba(255,255,255,.92) 50%,rgba(255,255,255,.58) 52%,rgba(255,255,255,.10) 59%,transparent 66%,transparent 100%) 132% 0/230% 100% no-repeat;
                opacity:.44;animation:vault-v61-silver-artifact-glint 6.2s ease-in-out infinite,
                    vault-v60-mundane-silver-aura 5.4s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"]::after {
                background:radial-gradient(ellipse 67% 93% at 88% 12%,rgba(135,225,249,.28),transparent 80%),
                    linear-gradient(112deg,transparent 37%,rgba(255,255,255,.75) 49%,transparent 63%) 115% 0/250% 100% no-repeat;
                opacity:.20;animation:vault-v46-soft-interior-glint 12s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]::after {
                background:radial-gradient(ellipse 67% 93% at 88% 12%,rgba(135,225,249,.51),transparent 80%),
                    linear-gradient(112deg,transparent 37%,rgba(255,255,255,.75) 49%,transparent 63%) 115% 0/250% 100% no-repeat;
                opacity:.25;animation:vault-v49-tech-inside 9.5s ease-in-out infinite;
            }
            /* Circuitos: paths e duração da V51 DO COFRE (não redesenhados). */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-energy {
                position:absolute;inset:0;display:block;z-index:2;
                pointer-events:none;overflow:hidden;border-radius:inherit;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-circuits {
                position:absolute;inset:4px 1px;width:calc(100% - 2px);height:calc(100% - 8px);
                fill:none;stroke:#d7fbff;stroke-width:1.5;
                stroke-linecap:round;stroke-linejoin:round;
                filter:drop-shadow(0 0 2px rgba(218,251,255,.26));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-trace {
                opacity:.24;
                stroke-dasharray:18 142;stroke-dashoffset:155;
                animation:vault-v51-trace-forward 7.2s linear infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-trace:nth-child(2) {
                stroke-dasharray:12 126;animation:vault-v51-trace-reverse 9.1s linear -3.4s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-trace:nth-child(3) {
                stroke-dasharray:15 102;animation:vault-v51-trace-branch 6.7s ease-in-out -1.1s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-trace:nth-child(4) {
                stroke-dasharray:9 111;animation:vault-v51-trace-reverse 10.3s linear -4.9s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"] .inventory-v94-trace {opacity:.22;stroke-width:1.05;animation-duration:11.5s;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace {opacity:.27;stroke:#e9feff;stroke-width:1.7;filter:drop-shadow(0 0 2px rgba(208,249,255,.22));}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i {
                display:block;position:absolute;left:var(--x);top:var(--y);
                width:var(--size);height:var(--size);border-radius:999px;
                background:rgba(240,255,255,.72);box-shadow:0 0 4px rgba(214,250,255,.48),0 0 10px rgba(162,238,248,.18);opacity:.54;
                animation:var(--motion) calc(var(--duration) * .9) ease-in-out var(--delay) infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i:nth-child(1) {--x:15%;--y:22%;--size:3px;--motion:vault-v51-orbit-a;--duration:7.7s;--delay:-1.4s;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i:nth-child(2) {--x:73%;--y:14%;--size:2px;--motion:vault-v51-orbit-b;--duration:11.2s;--delay:-6.1s;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i:nth-child(3) {--x:34%;--y:75%;--size:4px;--motion:vault-v51-orbit-c;--duration:8.5s;--delay:-2.9s;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i:nth-child(4) {--x:84%;--y:56%;--size:2px;--motion:vault-v51-orbit-d;--duration:9.3s;--delay:-4.2s;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i:nth-child(5) {--x:23%;--y:48%;--size:2px;--motion:vault-v51-orbit-b;--duration:6.9s;--delay:-3.7s;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i:nth-child(6) {--x:67%;--y:87%;--size:3px;--motion:vault-v51-orbit-a;--duration:10.6s;--delay:-8.2s;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art > img {
                position:relative;z-index:3;opacity:1 !important;
                filter:drop-shadow(0 6px 9px rgba(8,25,42,.20)) !important;
            }
            /* O botão Equipar fica acima dos circuitos e continua clicável. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v49-equip {z-index:5;}

            /* Modal: mesmo material nas bordas e no retrato, preservando os
               tokens de tema para todos os botões, textos e ferramentas. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]),
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                position:relative;isolation:isolate;
                --inventory-v94-metal-color:#adb5bf;
                border-color:var(--inventory-v94-metal-color) !important;
                box-shadow:var(--theme-modal-shadow,0 24px 60px rgba(0,0,0,.28)),0 0 0 1px color-mix(in srgb,var(--inventory-v94-metal-color) 35%,transparent) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {--inventory-v94-metal-color:#78acc5;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] {--inventory-v94-metal-color:#5ba8cb;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="artifact"] {--inventory-v94-metal-color:#b0b7c3;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                position:relative;isolation:isolate;
                background:linear-gradient(137deg,#f9f9f9 0%,#e7e9eb 42%,#bfc5cb 88%,#eeeef0) !important;
                border-color:#a7adb3 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="artifact"] .inventory-v94-detail-art {
                background:radial-gradient(circle at 44% 35%,#fff 2%,#eeeff1 36%,#aeb3bd 78%,#d9dbe0 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                position:relative;isolation:isolate;
                border-color:#8db9cc !important;
                background:radial-gradient(ellipse at 50% 36%,#d7f1fb 0%,#89bfd7 56%,#5e93ad 82%,#446f89 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-detail-art {
                border-color:#90b5cf !important;
                background:radial-gradient(ellipse at 50% 35%,#ddeeff 0%,#98bbdc 56%,#688db7 80%,#4c6f99 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v94-detail-art > img {position:relative;z-index:3;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v94-detail-art .inventory-v94-tech-energy--detail {inset:5px;z-index:2;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v94-detail-art .inventory-v94-tech-circuits {
                position:absolute;inset:0;width:100%;height:100%;fill:none;
                stroke:#b1f6ff;stroke-width:1.35;stroke-linecap:round;
                filter:drop-shadow(0 0 2px rgba(136,232,250,.40));
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-detail-art .inventory-v94-trace {stroke-width:1.75;animation-duration:6.8s;}
            /* Mantém superfícies do modal pertencentes ao tema, mas adiciona
               tintas frias sutis como no Cofre, sem apagar temas escuros. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-scroll {
                background:linear-gradient(115deg,
                    color-mix(in srgb,var(--theme-modal-surface,var(--theme-paper)) 97%,#b7bfce 3%),
                    var(--theme-modal-surface,var(--theme-paper))) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-scroll {
                background:linear-gradient(115deg,
                    color-mix(in srgb,var(--theme-modal-surface,var(--theme-paper)) 96%,#86bad5 4%),
                    var(--theme-modal-surface,var(--theme-paper))) !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::after,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::after,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-trace,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v94-tech-nodes i,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v94-trace {
                    animation:none !important;
                }
            }


            /* Preview teleportado: a camada precisa ter posicionamento próprio,
               pois a regra do card é propositalmente limitada ao Inventário. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v94-detail-art .inventory-v94-tech-energy--detail {
                position:absolute;
                display:block;
                inset:5px;
                z-index:2;
                pointer-events:none;
                overflow:hidden;
                border-radius:inherit;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v94-tech-energy--detail[x-cloak] {
                display:none !important;
            }
            /* Efeitos decorativos nunca interceptam controles do Player. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v17-detail-topbar {
                position:relative;z-index:8;
            }



            /* =====================================================================
               SPELLBOUND V66.1.95 — NATUREZAS / RARIDADES COM A PALETA DA FICHA
               Referência estética: Cofre de Itens atual. O Inventário conserva
               o acabamento de prata, azul-circuito e ouro; as superfícies seguem
               os oito temas da ficha, incluindo Midnight/Obsidian.
               Não cobre o retrato com ícone, não altera Vestígios/itens usados.
               ===================================================================== */

            /* Materiais de raridade do Cofre, aplicados como esmaltes leves
               sobre o próprio papel de cada tema (evita cartões brancos no escuro). */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="legendary"] {
                --v95-tint:#aab5c4;--v95-edge:#9caab9;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="artifact"] {
                --v95-tint:#b9b5cb;--v95-edge:#adb1c5;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] {
                --v95-tint:#8fc2d8;--v95-edge:#74aabd;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] {
                --v95-tint:#81afd2;--v95-edge:#648fb7;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] {
                --v95-tint:#d9bb81;--v95-edge:#c5a064;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige) {
                border-color:color-mix(in srgb,var(--v95-edge) 66%,var(--theme-border-strong) 34%) !important;
                background:linear-gradient(134deg,
                    color-mix(in srgb,var(--theme-surface-card) 96%,var(--v95-tint) 4%) 0%,
                    color-mix(in srgb,var(--theme-surface-card) 90%,var(--v95-tint) 10%) 55%,
                    color-mix(in srgb,var(--theme-surface-card) 82%,var(--v95-tint) 18%) 100%) !important;
                box-shadow:inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 11%,transparent),
                    0 5px 16px color-mix(in srgb,var(--v95-edge) 15%,transparent) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige):is(:hover,:focus-visible) {
                border-color:var(--v95-edge) !important;
                background:linear-gradient(134deg,
                    color-mix(in srgb,var(--theme-card-hover) 92%,var(--v95-tint) 8%),
                    color-mix(in srgb,var(--theme-card-hover) 78%,var(--v95-tint) 22%)) !important;
                box-shadow:0 8px 21px color-mix(in srgb,var(--v95-edge) 22%,transparent) !important;
            }
            /* O retrato preserva a cor da natureza sem sobrepor a arte. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige) .inventory-v48-art {
                background:radial-gradient(ellipse at 47% 36%,
                    color-mix(in srgb,var(--theme-surface-inset) 69%,var(--theme-paper-strong) 31%) 0%,
                    color-mix(in srgb,var(--theme-surface-inset) 75%,var(--v95-tint) 25%) 68%,
                    color-mix(in srgb,var(--theme-surface-inset) 52%,var(--v95-tint) 48%) 100%) !important;
                border-color:color-mix(in srgb,var(--v95-edge) 51%,var(--theme-border-soft) 49%) !important;
                box-shadow:inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 35%,transparent) !important;
            }
            /* A faixa de habilidades nunca deve virar um painel cinza/azul claro
               independente do fundo do personagem. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige) .inventory-v48-abilities {
                border-top-color:color-mix(in srgb,var(--v95-edge) 31%,var(--theme-border-soft) 69%) !important;
                background:color-mix(in srgb,var(--theme-surface-card-alt) 93%,var(--v95-tint) 7%) !important;
            }
            /* Legibilidade: textos são do personagem, não cinza ou azul fixo
               que só funcionam em um tema. Um toque de cor apenas no título. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige) .inventory-v48-name {
                color:color-mix(in srgb,var(--theme-text-strong) 89%,var(--v95-edge) 11%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige)
            :is(.inventory-v48-classification,.inventory-v48-technical,.inventory-v49-description,.inventory-v48-passive) {
                color:var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige)
            :is(.inventory-v48-abilities-header,.inventory-v48-skill-name) {
                color:var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]):not(.inventory-v50-vestige) .inventory-v49-skill {
                border-bottom-color:color-mix(in srgb,var(--v95-edge) 24%,var(--theme-border-soft) 76%) !important;
            }

            /* Os dois temas noturnos exigem reflexos menos brancos no interior.
               O card continua escuro, não uma peça clara em uma grade escura. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"])::after {
                opacity:.12 !important;
                box-shadow:none !important;
            }

            /* Maravilhoso Lendário: ouro suave e feixe curto orbital, como no
               Cofre, sem atribuir selo ou estágios de Vestígio. */
            @property --inventory-v95-gold-angle {
                syntax:'<angle>';inherits:false;initial-value:0deg;
            }
            @keyframes inventory-v95-gold-orbit {to{--inventory-v95-gold-angle:360deg;}}
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"]::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"]::before {
                    content:'' !important;
                    display:block !important;
                    position:absolute !important;
                    inset:0 !important;
                    top:0 !important;right:0 !important;bottom:0 !important;left:0 !important;
                    width:auto !important;height:auto !important;
                    z-index:6 !important;
                    padding:1.6px !important;
                    border-radius:inherit !important;
                    pointer-events:none !important;
                    background:conic-gradient(from var(--inventory-v95-gold-angle),
                        transparent 0deg 306deg,rgba(236,191,109,.04) 312deg,
                        rgba(247,222,160,.72) 325deg,rgba(255,250,227,.97) 336deg,
                        rgba(221,169,73,.48) 347deg,transparent 360deg) !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    box-shadow:none !important;
                    animation:inventory-v95-gold-orbit 12s linear infinite !important;
                }
            }
            /* Visualização do Player: o material aparece nas bordas e na
               fotografia, mas todos os textos/ações continuam com seu tema. */
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"] {
                position:relative;
                isolation:isolate;
                border-color:color-mix(in srgb,#c5a064 62%,var(--theme-border-strong) 38%) !important;
            }
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"] .inventory-v94-detail-art {
                background:radial-gradient(ellipse at 49% 36%,
                    color-mix(in srgb,var(--theme-surface-inset) 81%,#f0d59e 19%),
                    color-mix(in srgb,var(--theme-surface-inset) 63%,#c6a265 37%)) !important;
                border-color:color-mix(in srgb,#c5a064 57%,var(--theme-border-soft) 43%) !important;
            }
            /* Anteriormente o retrato dos Mundanos/Tecnológicos era sempre prata
               ou azul saturado: aplicar o mesmo esmalte adaptativo do card. */
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                background:radial-gradient(circle at 48% 36%,
                    color-mix(in srgb,var(--theme-surface-inset) 77%,#e9edf5 23%),
                    color-mix(in srgb,var(--theme-surface-inset) 67%,#a8b0c0 33%)) !important;
                border-color:color-mix(in srgb,var(--theme-border-strong) 50%,#abb5c2 50%) !important;
            }
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                background:radial-gradient(circle at 48% 36%,
                    color-mix(in srgb,var(--theme-surface-inset) 78%,#acd8e9 22%),
                    color-mix(in srgb,var(--theme-surface-inset) 63%,#7faac6 37%)) !important;
                border-color:color-mix(in srgb,var(--theme-border-strong) 50%,#76a7c1 50%) !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v48-item-card[data-nature="wonderful"][data-rarity="legendary"]::before,
                .character-sheet-theme[data-theme]
                .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"]::before {
                    animation:none !important;
                }
            }

        

            /* =====================================================================
               SPELLBOUND V66.1.96 — SINCRONIZAÇÃO DE ANIMAÇÕES COFRE / PLAYER
               - mantém EXATAMENTE a paleta adaptativa aprovada na V66.1.95;
               - o perímetro animado é exclusivo dos ARTEFATOS;
               - os LENDÁRIOS recebem acentos estáticos e reflexos contidos;
               - os circuitos têm o mesmo caminho/duração nos cards e no modal,
                 respeitando o enquadramento quadrado do Player;
               - efeitos presos ao item, nunca ao painel de categoria.
               ===================================================================== */

            /* Posicionamento independente: impede que ::before de um item use
               a seção ou a grade como ancestral de posicionamento. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"], [data-nature="technological"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]),
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] {
                position:relative !important;
                isolation:isolate !important;
                overflow:hidden !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal:is(
                [data-nature="mundane"], [data-nature="technological"]
            ):is([data-rarity="legendary"],[data-rarity="artifact"]),
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"] {
                position:relative !important;
                isolation:isolate !important;
            }

            /* Os lendários são nobres, não estrondosos: borda estática e esmalte
               da V95. Sem feixe dourado cruzando o painel inteiro. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            )[data-rarity="legendary"]::before,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal:is(
                [data-nature="mundane"],[data-nature="technological"],[data-nature="wonderful"]
            )[data-rarity="legendary"]::before {
                content:none !important;
                display:none !important;
                animation:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] {
                --v95-tint:#c3aa83;
                --v95-edge:#ad9061;
                border-color:color-mix(in srgb,var(--theme-border-strong) 67%,#bda072 33%) !important;
                box-shadow:inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 12%,transparent),
                    0 4px 12px color-mix(in srgb,#bc9763 11%,transparent) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-abilities {
                border-top-color:color-mix(in srgb,#bd9969 18%,var(--theme-border-soft) 82%) !important;
                background:color-mix(in srgb,var(--theme-surface-card-alt) 96%,#c7ab81 4%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"] {
                border-color:color-mix(in srgb,var(--theme-border-strong) 68%,#bda072 32%) !important;
            }

            /* Artefato: luz CURTA circula por todo o perímetro do próprio item.
               A máscara recorta o centro e evita faixa/barra fixa à esquerda. */
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v48-item-card:not(.inventory-v43-item-used):is(
                    [data-nature="mundane"],[data-nature="technological"]
                )[data-rarity="artifact"]::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal:is(
                    [data-nature="mundane"],[data-nature="technological"]
                )[data-rarity="artifact"]::before {
                    content:'' !important;
                    display:block !important;
                    position:absolute !important;
                    inset:0 !important;
                    width:auto !important;
                    height:auto !important;
                    border:0 !important;
                    border-radius:inherit !important;
                    padding:1.6px !important;
                    z-index:6 !important;
                    pointer-events:none !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    box-shadow:none !important;
                }
            }

            /* Mesmos quatro caminhos do Cofre, proporcionais em vez de esticados
               verticalmente no quadrado. Nenhuma linha grande corta a arte. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"]:is(
                [data-rarity="legendary"],[data-rarity="artifact"]
            ) .inventory-v94-tech-energy,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is(
                [data-rarity="legendary"],[data-rarity="artifact"]
            ) .inventory-v94-tech-energy--detail {
                position:absolute !important;
                inset:4px !important;
                z-index:2 !important;
                overflow:hidden !important;
                border-radius:inherit !important;
                pointer-events:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"]:is(
                [data-rarity="legendary"],[data-rarity="artifact"]
            ) .inventory-v94-tech-circuits,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is(
                [data-rarity="legendary"],[data-rarity="artifact"]
            ) .inventory-v94-tech-circuits {
                position:absolute !important;
                inset:0 !important;
                width:100% !important;
                height:100% !important;
                fill:none !important;
                stroke:#9beefb !important;
                stroke-width:1.65 !important;
                stroke-linecap:round !important;
                stroke-linejoin:round !important;
                filter:drop-shadow(0 0 2.5px rgba(144,236,255,.42)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"] .inventory-v94-trace,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="legendary"] .inventory-v94-trace {
                opacity:.22;
                stroke-width:1.0 !important;
                animation-duration:11.8s !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace {
                opacity:0;
                stroke:#f2ffff;
                stroke-width:1.85 !important;
                filter:drop-shadow(0 0 2.5px rgba(214,250,255,.20));
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(1),
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(1) {
                stroke-dasharray:26 126;
                animation:vault-v51-trace-forward 5.9s linear -.6s infinite, inventory-v97-ghost-fade-a 4.6s ease-in-out -1.1s infinite !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(2),
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(2) {
                stroke-dasharray:18 118;
                animation:vault-v51-trace-reverse 7.1s linear -2.5s infinite, inventory-v97-ghost-fade-b 5.5s ease-in-out -2.1s infinite !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(3),
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(3) {
                stroke-dasharray:22 108;
                animation:vault-v51-trace-branch 5.1s ease-in-out -1.4s infinite, inventory-v97-ghost-fade-c 4.1s ease-in-out -1.8s infinite !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(4),
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-trace:nth-child(4) {
                stroke-dasharray:14 124;
                animation:vault-v51-trace-reverse 8.2s linear -4.6s infinite, inventory-v97-ghost-fade-d 6.2s ease-in-out -3.3s infinite !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-tech-nodes i {
                display:block;
                position:absolute;
                left:var(--x);
                top:var(--y);
                width:var(--size);
                height:var(--size);
                border-radius:999px;
                background:rgba(240,255,255,.72);
                box-shadow:0 0 5px rgba(214,250,255,.58),0 0 10px rgba(112,221,241,.24);
                animation:var(--motion) calc(var(--duration) * .9) ease-in-out var(--delay) infinite;
                pointer-events:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-tech-nodes i:nth-child(1){--x:15%;--y:22%;--size:3px;--motion:vault-v51-orbit-a;--duration:7.7s;--delay:-1.4s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-tech-nodes i:nth-child(2){--x:73%;--y:14%;--size:2px;--motion:vault-v51-orbit-b;--duration:11.2s;--delay:-6.1s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-tech-nodes i:nth-child(3){--x:34%;--y:75%;--size:4px;--motion:vault-v51-orbit-c;--duration:8.5s;--delay:-2.9s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-tech-nodes i:nth-child(4){--x:84%;--y:56%;--size:2px;--motion:vault-v51-orbit-d;--duration:9.3s;--delay:-4.2s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-tech-nodes i:nth-child(5){--x:23%;--y:48%;--size:2px;--motion:vault-v51-orbit-b;--duration:6.9s;--delay:-3.7s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-tech-nodes i:nth-child(6){--x:67%;--y:87%;--size:3px;--motion:vault-v51-orbit-a;--duration:10.6s;--delay:-8.2s;}

            /* Uma camada luminosa percorre o retrato técnico como na V49/V51
               do Cofre, sem substituir a cor de fundo aprovada na V95. */
            @keyframes inventory-v96-tech-portrait {
                0%,100% {opacity:.36;background-position:0% 35%,120% 50%;}
                48% {opacity:.70;background-position:100% 50%,-22% 50%;}
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art::before,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-detail-art::before {
                content:'';
                display:block;
                position:absolute;
                inset:1px;
                z-index:1;
                border-radius:inherit;
                pointer-events:none;
                background:
                    radial-gradient(ellipse 70% 87% at 20% 20%,rgba(120,222,246,.20),transparent 77%) 0% 35%/130% 100% no-repeat,
                    linear-gradient(108deg,transparent 28%,rgba(181,245,255,.06) 42%,rgba(226,254,255,.23) 50%,rgba(163,229,250,.05) 57%,transparent 74%) 120% 50%/210% 100% no-repeat;
                animation:inventory-v96-tech-portrait 9.5s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"] .inventory-v48-art::before,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="legendary"] .inventory-v94-detail-art::before {
                content:'';
                position:absolute;
                inset:0;
                z-index:1;
                pointer-events:none;
                border-radius:inherit;
                background:linear-gradient(125deg,rgba(153,202,224,.09),transparent 68%);
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art > img,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art > img {
                position:relative;
                z-index:3 !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-detail-art::before,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v94-tech-nodes i {
                    animation:none !important;
                }
            }





            /* ===================================================================
               SPELLBOUND V66.1.98 — PORT DIRETO DOS EFEITOS DO COFRE
               FONTES: estilos-complementares V50/V51/V53/V54 e Blade do Cofre.
               Os seletores (card/modal) mudaram; os traçados, os keyframes,
               os tempos e as coordenadas são os originais. Sem novos circuitos.
               =================================================================== */
            /* O SVG original não pode ter stroke fixo/opacity global impostos
               pelas tentativas anteriores: cada path tem sua própria animação. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-energy,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v54-preview-energy {
                pointer-events:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                position:relative !important;
                isolation:isolate;
                overflow:hidden !important;
            }
            /* Restaurar o mesmo contorno interno e posicionamento do Cofre. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art::before {
                content:'' !important;
                display:block !important;
                position:absolute !important;
                inset:10px !important;
                z-index:0 !important;
                background:none !important;
                border:1px solid rgba(238,251,253,.64) !important;
                border-radius:13px !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.30) !important;
                animation:none !important;
                pointer-events:none;
            }
            /* Anulação exclusiva do reflexo artificial V96, substituído pelas
               mesmas ondas de circuitos do Cofre. */
            @keyframes vault-v50-circuit-waves {
                0% { transform:translate3d(-19%,0,0); }
                50% { transform:translate3d(2%,0,0); }
                100% { transform:translate3d(19%,0,0); }
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art::after {
                content:'' !important;
                display:block !important;
                position:absolute !important;
                inset:10px -27% !important;
                z-index:0 !important;
                border-radius:13px !important;
                pointer-events:none !important;
                background:url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20320%20230%22%20preserveAspectRatio%3D%22none%22%3E%0A%3Cg%20fill%3D%22none%22%20stroke%3D%22%235b97aa%22%20stroke-width%3D%221.5%22%20opacity%3D%22.58%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%0A%3Cpath%20d%3D%22M-55%2044%20H4%20L27%2067%20H72%20L93%2053%20H139%20L158%2070%20H204%20L226%2052%20H270%20L298%2065%20H350%22%2F%3E%0A%3Cpath%20d%3D%22M-55%20173%20H15%20L38%20150%20H92%20L115%20167%20H169%20L195%20148%20H241%20L259%20163%20H350%22%2F%3E%0A%3Cpath%20d%3D%22M-55%20117%20H28%20L48%20100%20H106%20L128%20117%20H188%20L209%2098%20H265%20L282%20112%20H350%22%20opacity%3D%22.55%22%2F%3E%0A%3C%2Fg%3E%3Cg%20fill%3D%22%23f6ffff%22%20stroke%3D%22%236aa6bb%22%20stroke-width%3D%221.2%22%20opacity%3D%22.8%22%3E%0A%3Ccircle%20cx%3D%2272%22%20cy%3D%2267%22%20r%3D%223.5%22%2F%3E%3Ccircle%20cx%3D%22204%22%20cy%3D%2270%22%20r%3D%223%22%2F%3E%3Ccircle%20cx%3D%2292%22%20cy%3D%22150%22%20r%3D%223.5%22%2F%3E%3Ccircle%20cx%3D%22241%22%20cy%3D%22148%22%20r%3D%223%22%2F%3E%3Ccircle%20cx%3D%22188%22%20cy%3D%22117%22%20r%3D%223%22%2F%3E%0A%3C%2Fg%3E%3C%2Fsvg%3E") center/100% 100% no-repeat !important;
                opacity:.46 !important;
                animation:vault-v50-circuit-waves 12s linear infinite !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-energy {
                position:absolute;inset:0;z-index:2;display:block;
                overflow:hidden;pointer-events:none;border-radius:inherit;
                isolation:isolate;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-circuits {
                position:absolute;inset:8px 3px;width:calc(100% - 6px);height:calc(100% - 16px);
                overflow:visible;fill:none;stroke:#d7fbff;stroke-width:1.5;
                stroke-linecap:round;stroke-linejoin:round;
                filter:drop-shadow(0 0 2px rgba(218,251,255,.26));
            }
            @keyframes vault-v51-trace-forward {
                0%,12% {opacity:.08;stroke-dashoffset:155;}
                34% {opacity:.78;}
                63% {opacity:.33;stroke-dashoffset:-120;}
                100% {opacity:.08;stroke-dashoffset:-215;}
            }
            @keyframes vault-v51-trace-reverse {
                0%,10% {opacity:.06;stroke-dashoffset:-165;}
                43% {opacity:.66;}
                72% {opacity:.29;stroke-dashoffset:110;}
                100% {opacity:.06;stroke-dashoffset:185;}
            }
            @keyframes vault-v51-trace-branch {
                0%,22% {opacity:.08;stroke-dashoffset:135;}
                51% {opacity:.63;}
                77% {opacity:.22;stroke-dashoffset:-90;}
                100% {opacity:.08;stroke-dashoffset:-155;}
            }
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-circuits .vault-v51-trace {
                stroke-dasharray:18 142;stroke-dashoffset:155;
                animation:vault-v51-trace-forward 7.2s linear infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-circuits .vault-v51-trace:nth-child(2) {
                stroke-dasharray:12 126;animation:vault-v51-trace-reverse 9.1s linear -3.4s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-circuits .vault-v51-trace:nth-child(3) {
                stroke-dasharray:15 102;animation:vault-v51-trace-branch 6.7s ease-in-out -1.1s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-circuits .vault-v51-trace:nth-child(4) {
                stroke-dasharray:9 111;animation:vault-v51-trace-reverse 10.3s linear -4.9s infinite;
            }
            /* Cada partícula segue uma rota não sincronizada; nenhuma fila única
               da esquerda para direita. São 4 trajetórias e tempos diferentes. */
            @keyframes vault-v51-orbit-a {
                0%,100%{transform:translate3d(0,0,0) scale(.60);opacity:.16;}
                24%{transform:translate3d(17px,-29px,0) scale(1.1);opacity:.85;}
                58%{transform:translate3d(-9px,-47px,0) scale(.72);opacity:.24;}
                80%{transform:translate3d(-22px,12px,0) scale(1.25);opacity:.72;}
            }
            @keyframes vault-v51-orbit-b {
                0%,100%{transform:translate3d(0,0,0) scale(1);opacity:.13;}
                27%{transform:translate3d(-16px,18px,0) scale(.55);opacity:.72;}
                50%{transform:translate3d(15px,38px,0) scale(1.15);opacity:.25;}
                76%{transform:translate3d(24px,-19px,0) scale(.8);opacity:.85;}
            }
            @keyframes vault-v51-orbit-c {
                0%,100%{transform:translate3d(0,0,0) scale(.8);opacity:.18;}
                22%{transform:translate3d(13px,31px,0) scale(1.25);opacity:.80;}
                46%{transform:translate3d(-18px,15px,0) scale(.55);opacity:.33;}
                81%{transform:translate3d(-10px,-26px,0) scale(1);opacity:.78;}
            }
            @keyframes vault-v51-orbit-d {
                0%,100%{transform:translate3d(0,0,0) scale(.7);opacity:.18;}
                29%{transform:translate3d(-21px,-11px,0) scale(1.12);opacity:.62;}
                52%{transform:translate3d(3px,-35px,0) scale(.64);opacity:.14;}
                79%{transform:translate3d(22px,20px,0) scale(1.15);opacity:.90;}
            }
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark {
                --size:3px;position:absolute;
                width:var(--size);height:var(--size);border-radius:100%;
                background:rgba(237,255,255,.76);box-shadow:0 0 4px rgba(214,250,255,.55),0 0 10px rgba(162,238,248,.22);opacity:.62;
                left:var(--x);top:var(--y);
                will-change:transform,opacity;
                animation:var(--motion) var(--duration) ease-in-out var(--delay) infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(1){--x:15%;--y:22%;--size:3px;--motion:vault-v51-orbit-a;--duration:7.7s;--delay:-1.4s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(2){--x:73%;--y:14%;--size:2px;--motion:vault-v51-orbit-b;--duration:11.2s;--delay:-6.1s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(3){--x:34%;--y:75%;--size:4px;--motion:vault-v51-orbit-c;--duration:8.5s;--delay:-2.9s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(4){--x:84%;--y:56%;--size:2px;--motion:vault-v51-orbit-d;--duration:9.3s;--delay:-4.2s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(5){--x:23%;--y:48%;--size:2px;--motion:vault-v51-orbit-b;--duration:6.9s;--delay:-3.7s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(6){--x:67%;--y:87%;--size:3px;--motion:vault-v51-orbit-a;--duration:10.6s;--delay:-8.2s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(7){--x:47%;--y:11%;--size:2px;--motion:vault-v51-orbit-d;--duration:8.8s;--delay:-5.4s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(8){--x:88%;--y:83%;--size:3px;--motion:vault-v51-orbit-c;--duration:12.4s;--delay:-7.1s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(9){--x:12%;--y:90%;--size:2px;--motion:vault-v51-orbit-b;--duration:9.9s;--delay:-3.0s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(10){--x:78%;--y:37%;--size:3px;--motion:vault-v51-orbit-c;--duration:7.9s;--delay:-6.3s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(11){--x:29%;--y:15%;--size:2px;--motion:vault-v51-orbit-d;--duration:13.1s;--delay:-2.5s;}
            .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark:nth-of-type(12){--x:55%;--y:65%;--size:3px;--motion:vault-v51-orbit-a;--duration:10.1s;--delay:-4.7s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                position:relative !important;
                isolation:isolate !important;
                overflow:hidden !important;
                border:1px solid rgba(101,161,185,.76) !important;
                background:radial-gradient(ellipse at 50% 41%,#daeff4 2%,#77a9bb 68%,#386079 100%) !important;
                box-shadow:inset 0 0 0 1px rgba(255,255,255,.31),0 4px 15px rgba(31,92,119,.12) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art::before {
                content:'';
                position:absolute;
                inset:10px;
                z-index:0;
                pointer-events:none;
                border:1px solid rgba(154,239,254,.34);
                border-radius:10px;
                background:
                    linear-gradient(90deg,rgba(148,237,249,.47),rgba(148,237,249,.47)) 0 22%/42% 1px no-repeat,
                    linear-gradient(135deg,transparent 49%,rgba(148,237,249,.44) 49.5% 50.5%,transparent 51%) 54% 22%/48px 45px no-repeat,
                    linear-gradient(90deg,rgba(148,237,249,.43),rgba(148,237,249,.43)) 100% 75%/42% 1px no-repeat,
                    linear-gradient(135deg,transparent 49%,rgba(148,237,249,.36) 49.5% 50.5%,transparent 51%) 75% 67%/46px 37px no-repeat,
                    radial-gradient(circle,#7ce7f8 0 2px,transparent 2.7px) 16% 20%/11px 11px no-repeat,
                    radial-gradient(circle,#7ce7f8 0 2px,transparent 2.7px) 85% 77%/11px 11px no-repeat;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art::after {
                content:'';
                position:absolute;
                inset:0;
                z-index:1;
                pointer-events:none;
                background:
                    radial-gradient(circle,rgba(164,251,255,.95) 0 1.3px,transparent 2px) 10% 110%/60px 80px no-repeat,
                    radial-gradient(circle,rgba(180,229,255,.79) 0 1px,transparent 1.8px) 92% 115%/49px 88px no-repeat,
                    radial-gradient(circle,rgba(146,253,255,.72) 0 1.4px,transparent 2.3px) 70% 105%/71px 100px no-repeat;
                opacity:.34;
                animation:vault-v47-tech-sparks 9s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-detail-art::after {
                opacity:.68;
                animation-duration:6.6s;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art > img {
                position:relative !important;
                z-index:3 !important;
                opacity:1 !important;
                transform:none !important;
                filter:drop-shadow(0 5px 7px rgba(11,40,57,.22)) !important;
                object-fit:contain !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art > svg {
                position:relative;z-index:3;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-energy {
                display:block;
                position:absolute;
                inset:0;
                z-index:2;
                border-radius:inherit;
                overflow:hidden;
                pointer-events:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-circuits {
                position:absolute;
                inset:6px 3px;
                width:calc(100% - 6px);
                height:calc(100% - 12px);
                fill:none;
                stroke:#ddfdff;
                stroke-width:1.35px;
                stroke-linecap:round;
                stroke-linejoin:round;
                filter:drop-shadow(0 0 2px rgba(221,252,255,.24));
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-trace {
                opacity:.24;
                stroke-dasharray:18 142;
                stroke-dashoffset:155;
                animation:vault-v51-trace-forward 9.5s linear -1.3s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-trace:nth-child(2) {
                stroke-dasharray:12 126;
                animation:vault-v51-trace-reverse 11.1s linear -3.4s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-trace:nth-child(3) {
                stroke-dasharray:15 102;
                animation:vault-v51-trace-branch 8.7s ease-in-out -1.1s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-trace:nth-child(4) {
                stroke-dasharray:9 111;
                animation:vault-v51-trace-reverse 12.3s linear -4.9s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace {
                opacity:0;
                stroke:#f2ffff;
                stroke-width:1.75px;
                filter:drop-shadow(0 0 2px rgba(214,250,255,.18));
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace:nth-child(1) {
                stroke-dasharray:26 126;
                animation:vault-v51-trace-forward 6.2s linear -.6s infinite, inventory-v97-ghost-fade-a 4.8s ease-in-out -1.1s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace:nth-child(2) {
                stroke-dasharray:18 118;
                animation:vault-v51-trace-reverse 8.1s linear -2.5s infinite, inventory-v97-ghost-fade-b 5.8s ease-in-out -2.1s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace:nth-child(3) {
                stroke-dasharray:22 108;
                animation:vault-v51-trace-branch 5.4s ease-in-out -1.4s infinite, inventory-v97-ghost-fade-c 4.4s ease-in-out -1.8s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace:nth-child(4) {
                stroke-dasharray:14 124;
                animation:vault-v51-trace-reverse 9.1s linear -4.6s infinite, inventory-v97-ghost-fade-d 6.5s ease-in-out -3.3s infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark {
                --v54-size:2px;
                display:block;
                position:absolute;
                top:var(--v54-y);
                left:var(--v54-x);
                width:var(--v54-size);
                height:var(--v54-size);
                border-radius:50%;
                background:#e3fcff;
                box-shadow:0 0 6px #adf6ff,0 0 12px rgba(108,223,244,.45);
                animation:var(--v54-motion) var(--v54-duration) ease-in-out var(--v54-delay) infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(1){--v54-x:15%;--v54-y:22%;--v54-size:3px;--v54-motion:vault-v51-orbit-a;--v54-duration:7.7s;--v54-delay:-1.4s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(2){--v54-x:73%;--v54-y:14%;--v54-size:2px;--v54-motion:vault-v51-orbit-b;--v54-duration:11.2s;--v54-delay:-6.1s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(3){--v54-x:34%;--v54-y:75%;--v54-size:3px;--v54-motion:vault-v51-orbit-c;--v54-duration:8.5s;--v54-delay:-2.9s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(4){--v54-x:84%;--v54-y:56%;--v54-size:2px;--v54-motion:vault-v51-orbit-d;--v54-duration:9.3s;--v54-delay:-4.2s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(5){--v54-x:23%;--v54-y:48%;--v54-size:2px;--v54-motion:vault-v51-orbit-b;--v54-duration:6.9s;--v54-delay:-3.7s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(6){--v54-x:67%;--v54-y:87%;--v54-size:3px;--v54-motion:vault-v51-orbit-a;--v54-duration:10.6s;--v54-delay:-8.2s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(7){--v54-x:47%;--v54-y:11%;--v54-size:2px;--v54-motion:vault-v51-orbit-d;--v54-duration:8.8s;--v54-delay:-5.4s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(8){--v54-x:88%;--v54-y:83%;--v54-size:3px;--v54-motion:vault-v51-orbit-c;--v54-duration:12.4s;--v54-delay:-7.1s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(9){--v54-x:12%;--v54-y:90%;--v54-size:2px;--v54-motion:vault-v51-orbit-b;--v54-duration:9.9s;--v54-delay:-3.0s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(10){--v54-x:78%;--v54-y:37%;--v54-size:3px;--v54-motion:vault-v51-orbit-c;--v54-duration:7.9s;--v54-delay:-6.3s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(11){--v54-x:29%;--v54-y:15%;--v54-size:2px;--v54-motion:vault-v51-orbit-d;--v54-duration:13.1s;--v54-delay:-2.5s;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(12){--v54-x:55%;--v54-y:65%;--v54-size:3px;--v54-motion:vault-v51-orbit-a;--v54-duration:10.1s;--v54-delay:-4.7s;}
            /* Patamar Lendário: atividade contida; Artefato: 12 partículas. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="legendary"] .vault-v54-preview-spark:nth-of-type(n+6) {display:none;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] .inventory-v94-detail-art {
                box-shadow:inset 0 0 0 1px rgba(255,255,255,.42),0 4px 18px rgba(31,92,119,.19),0 0 0 1px rgba(108,207,231,.28) !important;
                animation:vault-v54-photo-aura 7.6s ease-in-out infinite;
            }
            @keyframes vault-v54-photo-aura {
                0%,100% {box-shadow:inset 0 0 0 1px rgba(255,255,255,.42),0 4px 18px rgba(31,92,119,.19),0 0 0 1px rgba(108,207,231,.28);}
                50% {box-shadow:inset 0 0 0 1px rgba(255,255,255,.52),0 5px 18px rgba(31,92,119,.21),0 0 0 2px rgba(129,218,244,.49),0 0 20px rgba(94,213,244,.19);}
            }
            @property --vault-v54-edge-angle {
                syntax:'<angle>';
                inherits:false;
                initial-value:0deg;
            }
            @keyframes vault-v54-edge-orbit {
                to { --vault-v54-edge-angle:360deg; }
            }
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"]::before {
                    content:'' !important;
                    display:block !important;
                    position:absolute !important;
                    inset:0 !important;
                    z-index:8 !important;
                    box-sizing:border-box !important;
                    padding:1.7px !important;
                    border:0 !important;
                    border-radius:inherit !important;
                    pointer-events:none !important;
                    background:conic-gradient(
                        from var(--vault-v54-edge-angle),
                        transparent 0deg 305deg,
                        rgba(141,207,230,.08) 316deg,
                        rgba(201,240,249,.53) 330deg,
                        #ffffff 339deg,
                        rgba(138,215,237,.65) 349deg,
                        transparent 359deg 360deg
                    ) !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    opacity:.88 !important;
                    filter:none !important;
                    animation:vault-v54-edge-orbit 14s linear infinite !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"]::before {
                    padding:2.5px !important;
                    background:conic-gradient(
                        from var(--vault-v54-edge-angle),
                        transparent 0deg 290deg,
                        rgba(100,195,220,.08) 302deg,
                        rgba(146,229,249,.52) 316deg,
                        #f9ffff 333deg,
                        rgba(116,221,247,.98) 342deg,
                        rgba(93,194,228,.45) 352deg,
                        transparent 359deg 360deg
                    ) !important;
                    filter:drop-shadow(0 0 3px rgba(91,200,234,.52)) !important;
                    opacity:1 !important;
                    animation:vault-v54-edge-orbit 8.7s linear infinite !important;
                }
            }

            /* O rosto do item fica acima das luzes, sem opacidade reduzida. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art > img,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art > img {
                position:relative !important;
                z-index:3 !important;
                opacity:1 !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-circuits .vault-v51-trace,
                .character-sheet-theme[data-theme] .inventory-v35 .vault-v51-tech-energy .vault-v51-spark,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .vault-v54-preview-trace,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal .vault-v54-preview-spark,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v48-art::after {
                    animation:none !important;
                }
            }



            /* =====================================================================
               SPELLBOUND V66.1.99 — MODAL TECNOLÓGICO INTEGRADO AOS 8 TEMAS
               Escopo restrito ao modal de detalhes do Player, nature=technological.
               Sem alterações nas animações V51 / V54 / perímetro de V66.1.98.
               ===================================================================== */

            /* O tema define o papel/fundo; o tecnológico adiciona somente um
               esmalte ciano muito discreto e uma borda metalizada. Elimina o
               cabeçalho âmbar herdado da regra genérica dos Artefatos. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                --inv99-tech-glaze:#77b8cf;
                --inv99-tech-edge:#6aa9bf;
                --inv99-tech-strength:6%;
                border-color:color-mix(in srgb,var(--theme-modal-border,var(--theme-border-strong)) 49%,var(--inv99-tech-edge) 51%) !important;
                background:
                    radial-gradient(ellipse 65% 82% at 100% 0%,rgba(105,195,221,.075),transparent 72%),
                    linear-gradient(136deg,
                        var(--theme-modal-surface,var(--theme-surface-card)) 0%,
                        color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 96%,var(--inv99-tech-glaze) 4%) 100%) !important;
                color:var(--theme-modal-text,var(--theme-text-primary)) !important;
                box-shadow:var(--theme-modal-shadow,0 24px 60px rgba(0,0,0,.28)),
                    0 0 0 1px color-mix(in srgb,var(--inv99-tech-edge) 23%,transparent) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="legendary"] {
                --inv99-tech-edge:#78a2b5;
                --inv99-tech-glaze:#91b8c8;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"] {
                --inv99-tech-edge:#5bb3d2;
                --inv99-tech-glaze:#77bdd5;
            }

            /* Específico o bastante para vencer o dourado do seletor genérico
               .inventory-v50-detail-artifact, sem mudar outros Artefatos. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-artifact[data-nature="technological"] .inventory-v17-detail-topbar,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="legendary"] .inventory-v17-detail-topbar {
                border-bottom-color:color-mix(in srgb,var(--theme-divider) 75%,var(--inv99-tech-edge) 25%) !important;
                /* --theme-modal-header-background é um GRADIENTE nos 8 temas;
                   preservá-lo como camada, nunca passá-lo para color-mix(). */
                background:
                    linear-gradient(102deg,rgba(100,190,218,.075),rgba(100,190,218,.014)),
                    var(--theme-modal-header-background,var(--theme-band-background)) !important;
                box-shadow:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-topbar > span:first-child {
                color:var(--theme-label) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-scroll {
                background:
                    radial-gradient(ellipse 58% 70% at 97% 25%,rgba(114,194,216,.035),transparent 80%),
                    var(--theme-modal-surface,var(--theme-surface-card)) !important;
                color:var(--theme-text-primary) !important;
            }

            /* Os tokens dos oito temas são a fonte única de contraste.
               O leve metal azulado não deve pintar de azul todos os textos. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            :is(.inventory-v17-item-title,.inventory-v11-ability-header h3,.inventory-v21-tooltip-title) {
                color:var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            :is(.inventory-v17-classification,.inventory-v20-weapon-line,.inventory-v20-detail-line,.inventory-v82-inline-description,.inventory-v11-ability-body,.inventory-v21-tooltip-text) {
                color:var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            :is(.inventory-v17-item-kicker,.inventory-v11-detail-section-title,.inventory-v20-detail-line strong,.inventory-v11-recovery-pill) {
                color:var(--theme-label) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .inventory-v82-inline-description {
                border-top-color:color-mix(in srgb,var(--theme-divider) 86%,var(--inv99-tech-edge) 14%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .inventory-v11-detail-section-title::after {
                background:linear-gradient(90deg,color-mix(in srgb,var(--theme-divider) 80%,var(--inv99-tech-edge) 20%),transparent) !important;
            }

            /* A caixa e o interior das habilidades nunca devem virar um azul
               claro fixo em um tema escuro, nem escurecer num tema claro. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-card {
                border-color:color-mix(in srgb,var(--theme-border) 79%,var(--inv99-tech-edge) 21%) !important;
                background:color-mix(in srgb,var(--theme-surface-card-alt) 96%,var(--inv99-tech-glaze) 4%) !important;
                color:var(--theme-text-primary) !important;
                box-shadow:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-header {
                border-bottom-color:color-mix(in srgb,var(--theme-divider) 80%,var(--inv99-tech-edge) 20%) !important;
                background:color-mix(in srgb,var(--theme-surface-raised) 94%,var(--inv99-tech-glaze) 6%) !important;
                box-shadow:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-body {
                background:var(--theme-surface-card-alt) !important;
                color:var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-body :is(p,span,div) {
                color:var(--theme-text-primary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-recovery-pill {
                border-color:var(--theme-border) !important;
                background:var(--theme-accent-softer) !important;
                color:var(--theme-text-strong) !important;
            }

            /* Elementos de ação pertencem ao tema, não ao tipo do item. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .inventory-v17-detail-topbar > button[class*="bg-[#6b1d14]"] {
                background:var(--theme-button-background) !important;
                border-color:var(--theme-accent-strong) !important;
                color:var(--theme-button-text) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .inventory-v17-detail-topbar > .inventory-v45-detail-close {
                color:var(--theme-text-secondary) !important;
                background:var(--theme-surface-inset) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .inventory-v17-detail-topbar > .inventory-v45-detail-close:hover {
                color:var(--theme-text-strong) !important;
                background:var(--theme-card-hover) !important;
            }

            /* O retrato e sua borda acompanham a superfície do personagem.
               NÃO tocar nos pseudo-elementos, SVG, animações V51/V54 ou seu
               box-shadow pulsante, já aprovados na versão anterior. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-artifact[data-nature="technological"] .inventory-v94-detail-art,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="legendary"] .inventory-v94-detail-art {
                border-color:color-mix(in srgb,var(--theme-border-strong) 37%,var(--inv99-tech-edge) 63%) !important;
                background:
                    radial-gradient(ellipse at 49% 39%,
                        color-mix(in srgb,var(--theme-surface-inset) 75%,#9ad5e5 25%),
                        color-mix(in srgb,var(--theme-surface-inset) 53%,#508eae 47%)) !important;
            }

            @media (prefers-reduced-motion:reduce) {
                /* Respeita o ajuste de movimento sem alterar cores/contraste. */
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-scroll {
                    animation:none !important;
                }
            }



            /* =====================================================================
               SPELLBOUND V66.2.00 — REFINO FINAL DAS CORES ESPECIAIS DO INVENTÁRIO
               Objetivos:
               - remover o quadrado interno estranho das artes especiais;
               - deixar Artefatos Maravilhosos mais dourados, porém como aura;
               - harmonizar Mundanos Lendários/Artefatos com todas as cores-tema;
               - manter o Tecnológico com a animação aprovada e uma leitura limpa.
               ===================================================================== */

            /* ---------- Foto / retrato: remove o quadrado interno estranho ---------- */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art::before,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art::before {
                content:none !important;
                display:none !important;
                background:none !important;
                border:none !important;
                box-shadow:none !important;
                animation:none !important;
            }

            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                overflow:hidden !important;
                isolation:isolate;
            }

            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art > img,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art > img {
                position:relative !important;
                z-index:3 !important;
                padding:4px !important;
                filter:drop-shadow(0 3px 6px rgba(0,0,0,.10));
            }

            /* ---------- MARAVILHOSOS: dourado em aura, sem pintar o tema ---------- */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="wonderful"][data-rarity="legendary"] {
                border-color:color-mix(in srgb,var(--theme-border-strong) 64%,#c89b43 36%) !important;
                background:
                    radial-gradient(circle at 88% 14%,rgba(230,195,101,.10),transparent 34%),
                    linear-gradient(138deg,
                        var(--theme-surface-card) 0%,
                        color-mix(in srgb,var(--theme-surface-card) 97%,#d7ae61 3%) 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.08) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="wonderful"][data-rarity="legendary"]:hover {
                border-color:color-mix(in srgb,var(--theme-border-strong) 52%,#d8ab4d 48%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="wonderful"][data-rarity="artifact"] {
                border-color:rgba(205,161,78,.88) !important;
                background:
                    radial-gradient(circle at 83% 16%,rgba(233,203,112,.14),transparent 32%),
                    radial-gradient(circle at 10% 100%,rgba(255,232,172,.05),transparent 26%),
                    linear-gradient(138deg,
                        var(--theme-surface-card) 0%,
                        color-mix(in srgb,var(--theme-surface-card) 93%,#dcb15d 7%) 100%) !important;
                box-shadow:0 12px 28px rgba(34,20,8,.06), inset 0 1px 0 rgba(255,248,223,.11) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="wonderful"][data-rarity="artifact"]::after,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-scroll::after {
                content:'';
                position:absolute;
                inset:0;
                pointer-events:none;
                border-radius:inherit;
                background:radial-gradient(circle at 50% 35%,rgba(255,229,154,.10),transparent 38%);
                z-index:0;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"] {
                border-color:color-mix(in srgb,var(--theme-modal-border,var(--theme-border-strong)) 66%,#ca9e46 34%) !important;
                background:
                    radial-gradient(circle at 88% 14%,rgba(240,201,112,.08),transparent 30%),
                    linear-gradient(136deg,
                        var(--theme-modal-surface,var(--theme-surface-card)) 0%,
                        color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 97%,#cfa55a 3%) 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] {
                border-color:rgba(205,161,78,.84) !important;
                background:
                    radial-gradient(circle at 88% 12%,rgba(241,211,127,.11),transparent 30%),
                    linear-gradient(136deg,
                        var(--theme-modal-surface,var(--theme-surface-card)) 0%,
                        color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 95%,#d6a64f 5%) 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="legendary"] .inventory-v17-detail-topbar,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-topbar {
                background:
                    linear-gradient(104deg,rgba(214,174,87,.08),rgba(214,174,87,.02)),
                    var(--theme-modal-header-background,var(--theme-band-background)) !important;
                border-bottom-color:color-mix(in srgb,var(--theme-divider) 72%,#d6a64f 28%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="wonderful"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                border-color:color-mix(in srgb,var(--theme-border-soft) 32%,#ce9f42 68%) !important;
                background:
                    radial-gradient(circle at 50% 50%,rgba(255,240,188,.34),transparent 30%),
                    radial-gradient(circle at 50% 50%,rgba(214,172,86,.16),transparent 56%),
                    linear-gradient(145deg,
                        color-mix(in srgb,var(--theme-surface-inset) 92%,#f5edcf 8%) 0%,
                        color-mix(in srgb,var(--theme-surface-inset) 86%,#d2a44c 14%) 100%) !important;
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.14),
                    0 0 0 1px rgba(207,160,74,.08),
                    0 8px 24px rgba(199,148,52,.08) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-card {
                border-color:color-mix(in srgb,var(--theme-border) 72%,#cea14d 28%) !important;
                background:color-mix(in srgb,var(--theme-surface-card-alt) 96%,#dbb263 4%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-header {
                border-bottom-color:color-mix(in srgb,var(--theme-divider) 70%,#d1a553 30%) !important;
                background:color-mix(in srgb,var(--theme-surface-raised) 95%,#e1bb70 5%) !important;
            }

            /* ---------- MUNDANOS: metal neutro elegante, ainda ligado ao tema ---------- */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="legendary"] {
                border-color:color-mix(in srgb,var(--theme-border-strong) 72%,#95a1ad 28%) !important;
                background:
                    radial-gradient(circle at 86% 15%,rgba(160,171,185,.09),transparent 32%),
                    linear-gradient(140deg,
                        var(--theme-surface-card) 0%,
                        color-mix(in srgb,var(--theme-surface-card) 98%,#aeb7c2 2%) 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"][data-rarity="artifact"] {
                border-color:color-mix(in srgb,var(--theme-border-strong) 58%,#8493a5 42%) !important;
                background:
                    radial-gradient(circle at 86% 14%,rgba(177,188,201,.12),transparent 32%),
                    linear-gradient(140deg,
                        var(--theme-surface-card) 0%,
                        color-mix(in srgb,var(--theme-surface-card) 95%,#a3afbc 5%) 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.10) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                border-color:color-mix(in srgb,var(--theme-border-soft) 45%,#9aa6b4 55%) !important;
                background:
                    radial-gradient(circle at 50% 48%,rgba(255,255,255,.28),transparent 28%),
                    linear-gradient(145deg,
                        color-mix(in srgb,var(--theme-surface-inset) 96%,#eef2f6 4%) 0%,
                        color-mix(in srgb,var(--theme-surface-inset) 90%,#b1bdc7 10%) 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.10), 0 8px 18px rgba(45,57,73,.05) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="legendary"],
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="artifact"] {
                border-color:color-mix(in srgb,var(--theme-modal-border,var(--theme-border-strong)) 70%,#92a0ae 30%) !important;
                background:
                    radial-gradient(circle at 86% 15%,rgba(170,183,196,.08),transparent 33%),
                    linear-gradient(140deg,
                        var(--theme-modal-surface,var(--theme-surface-card)) 0%,
                        color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 97%,#a6b1bc 3%) 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-topbar {
                background:
                    linear-gradient(104deg,rgba(170,183,196,.08),rgba(170,183,196,.02)),
                    var(--theme-modal-header-background,var(--theme-band-background)) !important;
                border-bottom-color:color-mix(in srgb,var(--theme-divider) 76%,#9aa6b4 24%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-card {
                border-color:color-mix(in srgb,var(--theme-border) 74%,#98a4b2 26%) !important;
                background:color-mix(in srgb,var(--theme-surface-card-alt) 97%,#aab4c0 3%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-header {
                border-bottom-color:color-mix(in srgb,var(--theme-divider) 76%,#9aa6b4 24%) !important;
                background:color-mix(in srgb,var(--theme-surface-raised) 97%,#b0bac5 3%) !important;
            }

            /* ---------- TECNOLÓGICOS: mantém a animação, mas melhora a base ---------- */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"],
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] {
                background:
                    radial-gradient(circle at 86% 15%,rgba(104,210,230,.08),transparent 30%),
                    linear-gradient(140deg,
                        var(--theme-surface-card) 0%,
                        color-mix(in srgb,var(--theme-surface-card) 96%,#7dc7de 4%) 100%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] {
                border-color:color-mix(in srgb,var(--theme-border-strong) 40%,#59b4d1 60%) !important;
                box-shadow:0 10px 24px rgba(36,86,105,.08), inset 0 1px 0 rgba(255,255,255,.09) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="legendary"] {
                border-color:color-mix(in srgb,var(--theme-border-strong) 54%,#79aec1 46%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                border-color:color-mix(in srgb,var(--theme-border-soft) 32%,#64bfd9 68%) !important;
                background:
                    radial-gradient(circle at 50% 48%,rgba(201,247,255,.14),transparent 28%),
                    linear-gradient(145deg,
                        color-mix(in srgb,var(--theme-surface-inset) 96%,#d9f4fb 4%) 0%,
                        color-mix(in srgb,var(--theme-surface-inset) 88%,#76b7cc 12%) 100%) !important;
            }

            /* ---------- Ajustes tipográficos e divisórias dos cards especiais ---------- */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-name {
                color:var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.inventory-v48-classification,.inventory-v48-technical,.inventory-v49-description,.inventory-v48-abilities-header,.inventory-v48-passive) {
                color:var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-skill-name {
                color:var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v49-skill {
                border-bottom-color:var(--theme-divider) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:is([data-nature="wonderful"],[data-nature="mundane"],[data-nature="technological"]):is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-abilities {
                background:color-mix(in srgb,var(--theme-surface-card-alt) 98%,transparent) !important;
                border-top-color:var(--theme-divider) !important;
            }

            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="wonderful"][data-rarity="artifact"]::after,
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-scroll::after {
                    display:none !important;
                }
            }





            /* ===============================================================
               SPELLBOUND V66.2.04 — COFRE FINAL => INVENTÁRIO DO PLAYER
               Fonte única: estilos-complementares.blade(4).php, aprovado.
               Copiados: V38 (materiais), V51/V52/V54 (tecnologia),
               V55/V56/V60/V61 (mundanos), V65/V69/V79/V81 (magia).
               Somente no escopo dos seis temas claros. Não redesenha o tema.
               Apenas os seletores e a distribuição do card são adaptados.
               =============================================================== */
            .inventory-v203-liquid-gold {position:absolute;display:none;pointer-events:none;}
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v203-liquid-gold {display:none !important;}

            /* Paletas V38, com substituições FINAIS V51 e V79 onde aplicáveis. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="legendary"] {
                --vault-edge:#c59d58;--vault-pale:#fff7e7;--vault-shade:#f1dfbc;--vault-ink:#956a29;--vault-modal-tint:#f7e9cb;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="artifact"] {
                --vault-edge:#c4a16b;--vault-pale:#fffaf0;--vault-shade:#f1e3c2;--vault-ink:#74491f;--vault-modal-tint:#f2dfb8;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="technological"][data-rarity="legendary"] {
                --vault-edge:#83b2c6;--vault-pale:#edf8fb;--vault-shade:#7caec4;--vault-ink:#336e91;--vault-modal-tint:#daedf7;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="technological"][data-rarity="artifact"] {
                --vault-edge:#83b2c6;--vault-pale:#eaf5fa;--vault-shade:#7caec4;--vault-ink:#27536a;--vault-modal-tint:#daedf7;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="mundane"][data-rarity="legendary"] {
                --vault-edge:#a7adb3;--vault-pale:#f5f8fa;--vault-shade:#c5d0d9;--vault-ink:#545962;--vault-modal-tint:#eff0f2;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="mundane"][data-rarity="artifact"] {
                --vault-edge:#b5b9c3;--vault-pale:#f6f3f8;--vault-shade:#bbb0c4;--vault-ink:#414653;--vault-modal-tint:#f0f1f5;
            }

            /* Base compacta dos seis cards. O texto sempre sobre as películas. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) {
                position:relative !important; isolation:isolate !important;
                border-color:var(--vault-edge) !important;
                background:linear-gradient(125deg,#fffdfa 35%,var(--vault-pale)) !important;
                box-shadow:0 0 0 1px color-mix(in srgb,var(--vault-edge) 19%,transparent),
                           0 8px 21px color-mix(in srgb,var(--vault-edge) 14%,transparent) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) > :is(.inventory-v48-hero,.inventory-v49-description,.inventory-v48-abilities) {
                position:relative;z-index:2;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-name {
                color:color-mix(in srgb,var(--vault-ink) 34%,#422820) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) :is(.inventory-v48-classification,.inventory-v48-technical,.inventory-v49-description,.inventory-v48-abilities-header,.inventory-v48-passive) {
                color:color-mix(in srgb,var(--vault-ink) 39%,#6f5441) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-skill-name {
                color:color-mix(in srgb,var(--vault-ink) 50%,#422820) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-art {
                position:relative;isolation:isolate;overflow:hidden;
                border-color:var(--vault-edge) !important;
                background:linear-gradient(155deg,var(--vault-pale),var(--vault-shade)) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.86) !important;
            }

            /* Lendário Maravilhoso: ouro concentrado no retrato e nos PODERES.
               O Cofre V69 usa uma plaqueta, não uma faixa inteiramente sólida. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-abilities {
                border-top-color:rgba(190,145,68,.22) !important;
                background:linear-gradient(112deg,rgba(253,251,246,.72),rgba(255,254,250,.86)) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-abilities::before {
                content:'';position:absolute;inset:4px 6px;z-index:0;pointer-events:none;
                border:1px solid rgba(190,145,68,.29);border-radius:9px;
                background:linear-gradient(112deg,rgba(248,242,230,.62),rgba(255,252,245,.94) 52%,rgba(246,237,218,.70));
                box-shadow:inset 0 1px 0 rgba(255,255,252,.91),0 2px 6px rgba(138,99,40,.035);
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-abilities > * {
                position:relative;z-index:1;
            }
            /* O fundo branco do Player por baixo do rótulo mantém a legibilidade. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-skill-name {
                color:#715638 !important;
            }

            /* Maravilhoso ARTEFATO + Vestígio: literalmente os gradientes V79. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] {
                border-color:#c4a16b !important;
                background:linear-gradient(118deg,#fffaf0 0%,#fdf8ed 51%,#f9f0dd 100%) !important;
                box-shadow:0 0 0 1px rgba(193,155,96,.17),0 8px 22px rgba(105,77,38,.12) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"]:hover {
                border-color:#b99256 !important;
                box-shadow:0 0 0 1px rgba(194,150,79,.25),0 13px 27px rgba(105,76,38,.16) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v48-art {
                background:linear-gradient(150deg,#fbf3df,#f1e3c2 64%,#e8d29f) !important;
                border-color:#c9a66c !important;
            }
            /* O span já existia: a mesma película dourada V79 APENAS no lado
               do conteúdo, sem aumentar a saturação da imagem à esquerda. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] > .inventory-v203-liquid-gold {
                position:absolute;display:block;inset:2px 2px 2px 31%;z-index:0;
                border-radius:0 13px 13px 0;overflow:hidden;pointer-events:none;
                background:
                    radial-gradient(ellipse 74% 65% at 51% 53%,rgba(229,176,71,.17) 0%,rgba(245,214,143,.10) 53%,transparent 83%),
                    linear-gradient(105deg,transparent 29%,rgba(225,172,75,.025) 37%,rgba(234,186,91,.13) 43%,rgba(251,225,159,.38) 48%,rgba(255,251,229,.61) 51%,rgba(225,175,77,.22) 57%,transparent 71%) 140% 50% / 225% 100% no-repeat;
                animation:vault-v79-liquid-pass 10.2s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v48-abilities {
                border-top-color:rgba(188,150,81,.35) !important;
                background:linear-gradient(112deg,rgba(247,231,198,.68),rgba(255,250,238,.81) 58%,rgba(246,235,212,.72)) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v48-name {
                color:#74491f !important;
            }
            /* Feixe V65, SEM filete fixo na esquerda. */
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"]::before,
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]::before {
                    content:'' !important;display:block !important;position:absolute !important;
                    inset:-2px !important;width:auto !important;height:auto !important;
                    z-index:8 !important;padding:2px !important;
                    border:0 !important;border-radius:inherit !important;pointer-events:none !important;
                    background:conic-gradient(from var(--vestige-angle),transparent 0deg 276deg,
                        rgba(234,190,95,.12) 291deg,rgba(255,241,180,.62) 310deg,
                        #fff9db 324deg,rgba(255,220,123,.95) 335deg,
                        rgba(208,147,53,.56) 351deg,transparent 360deg) !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    animation:vault-vestige-aura 8.5s linear infinite !important;
                }
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"]::after {
                content:'';display:block;position:absolute;inset:1px;z-index:1;
                border-radius:12px;pointer-events:none;
                background:radial-gradient(ellipse 68% 95% at 72% 18%,rgba(255,249,224,.46),transparent 76%),
                  linear-gradient(110deg,transparent 31%,rgba(255,246,209,.06) 41%,rgba(255,236,172,.55) 48%,rgba(255,253,230,.84) 51%,rgba(255,236,172,.38) 55%,transparent 68%) 144% 0 / 236% 100% no-repeat,
                  linear-gradient(120deg,rgba(249,229,183,.22),transparent 72%);
                opacity:.20 !important;animation:vault-v65-golden-interior 8.5s ease-in-out infinite !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"]::before {
                content:none !important;display:none !important;
            }

            /* TECNOLÓGICO: mesmas cores/fundo V51. No card compacto do Player,
               somente a EXTENSÃO espacial da elipse é reduzida para que
               o azul fique no canto inferior, como no Cofre largo.
               Não muda uma cor, caminho ou tempo da animação V51/V54. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                border-color:#83b2c6 !important;
                background:radial-gradient(ellipse 58% 61% at 98% 92%,rgba(169,224,241,.34),transparent 77%),
                           linear-gradient(116deg,#f2fbfd 0%,#fcfeff 44%,#dceef6 100%) !important;
                box-shadow:0 5px 16px rgba(45,105,133,.11),inset 0 1px 0 rgba(255,255,255,.96) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] {
                border-color:#adc0c9 !important;
                background:radial-gradient(ellipse 54% 58% at 97% 91%,rgba(168,223,238,.16),transparent 77%),
                           linear-gradient(116deg,#fffcf8 0%,#fffefd 42%,#eef5f7 100%) !important;
                box-shadow:0 5px 16px rgba(74,99,109,.08),inset 0 1px 0 rgba(255,255,255,.97) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] {
                animation:vault-v51-artifact-halo 11.7s ease-in-out infinite !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art {
                background:radial-gradient(ellipse at 50% 41%,#daeff4 2%,#77a9bb 68%,#386079 100%) !important;
                border-color:#72a6ba !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.28) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] .inventory-v48-art {
                background:radial-gradient(ellipse at 50% 39%,#e8f4f6 6%,#9bbbc7 61%,#58788a 100%) !important;
                border-color:#8bb0bd !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.46) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-name {
                color:#244f67 !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.inventory-v48-classification,.inventory-v48-technical,.inventory-v49-description,.inventory-v48-abilities-header) {
                color:#607f91 !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] .inventory-v48-name {
                color:#365163 !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] :is(.inventory-v48-classification,.inventory-v48-technical,.inventory-v49-description,.inventory-v48-abilities-header,.inventory-v48-passive) {
                color:#7d7d79 !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] .inventory-v48-skill-name {
                color:#6f5336 !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] .inventory-v49-skill {
                border-bottom-color:rgba(186,160,110,.30) !important;
            }
            /* O Cofre usa fundo claro no corpo e um painel azul na área dos
               poderes. Ao compactar, preserva a mesma separação de materiais. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-abilities {
                border-top-color:rgba(100,170,197,.29) !important;
                background:linear-gradient(115deg,rgba(207,236,247,.52),rgba(233,250,254,.58)) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"] .inventory-v48-abilities {
                border-top-color:rgba(184,165,117,.23) !important;
                background:linear-gradient(112deg,rgba(253,251,246,.74),rgba(250,252,250,.88) 60%,rgba(239,246,248,.78)) !important;
            }
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                    content:'' !important;display:block !important;position:absolute !important;
                    inset:-1.5px !important;width:auto !important;height:auto !important;
                    z-index:8 !important;padding:1.5px !important;border:0 !important;
                    border-radius:inherit !important;pointer-events:none !important;
                    background:conic-gradient(from var(--vault-v52-edge-angle),transparent 0deg 306deg,
                        rgba(139,209,227,.08) 315deg,rgba(188,235,245,.42) 325deg,
                        rgba(255,255,255,.95) 335deg,rgba(155,219,237,.63) 342deg,transparent 355deg 360deg) !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    opacity:.78 !important;animation:vault-v52-edge-orbit 14s linear infinite !important;
                }
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="legendary"]::before {
                    background:conic-gradient(from var(--vault-v52-edge-angle),transparent 0deg 308deg,
                        rgba(167,214,226,.04) 317deg,rgba(213,234,240,.22) 327deg,
                        rgba(255,255,255,.92) 336deg,rgba(172,217,228,.36) 343deg,transparent 355deg 360deg) !important;
                    opacity:.62 !important;animation-duration:15.4s !important;
                }
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"]::before {
                    inset:-2px !important;padding:2.2px !important;
                    background:conic-gradient(from var(--vault-v52-edge-angle),transparent 0deg 298deg,
                        rgba(100,193,223,.06) 306deg,rgba(100,202,230,.30) 314deg,
                        rgba(153,234,252,.87) 325deg,#faffff 335deg,
                        rgba(125,223,247,.96) 343deg,rgba(84,194,229,.34) 351deg,transparent 359deg 360deg) !important;
                    filter:drop-shadow(0 0 2.5px rgba(96,207,238,.62)) !important;
                    opacity:1 !important;animation-duration:8.7s !important;
                }
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"]::after {
                content:'';display:block;position:absolute;inset:1px;z-index:1;
                border-radius:12px;pointer-events:none;
                background:radial-gradient(ellipse 62% 85% at 78% 24%,rgba(255,255,255,.50),transparent 73%),
                           linear-gradient(115deg,transparent 36%,rgba(191,236,248,.19) 49%,transparent 62%) 120% 50%/240% 100% no-repeat;
                opacity:.12 !important;animation:vault-v51-artifact-wash 15s ease-in-out infinite !important;
            }
            /* Os circuitos animados permanecem no elemento original V51, e a
               arte é isolada acima deles; não criamos outra camada SVG. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v48-art > img {
                position:relative !important;z-index:4 !important;
                filter:drop-shadow(0 5px 9px rgba(0,25,46,.32)) !important;
            }

            /* MUNDANOS: aço V45 e prata viva V60, nunca fundos mágicos. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="legendary"] {
                border-color:#a7adb3 !important;
                background:linear-gradient(124deg,#fafafa 0%,#f0f1f2 45%,#fafbfc 100%) !important;
                box-shadow:0 2px 10px rgba(55,59,66,.08),inset 0 1px 0 #fff !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="artifact"] {
                border:1.5px solid #bcc0c8 !important;
                background:radial-gradient(ellipse 95% 150% at 91% 20%,rgba(255,255,255,.96) 0%,transparent 68%),
                    radial-gradient(ellipse 78% 140% at 9% 80%,rgba(178,180,186,.56) 0%,transparent 76%),
                    linear-gradient(113deg,#cbced4 0%,#fdfdfd 32%,#fff 47%,#c8cbd2 80%,#fdfdfd 100%) !important;
                box-shadow:0 0 0 2px rgba(255,255,255,.90),0 9px 30px rgba(93,98,119,.20),
                           inset 0 0 25px rgba(255,255,255,.94) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="legendary"] .inventory-v48-art {
                background:linear-gradient(137deg,#f9f9f9 0%,#e7e9eb 42%,#bfc5cb 88%,#eeeef0) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="artifact"] .inventory-v48-art {
                background:radial-gradient(circle at 44% 35%,#fff 2%,#eeeff1 36%,#aeb3bd 78%,#d9dbe0 100%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="legendary"] .inventory-v48-abilities {
                border-top-color:rgba(149,157,166,.34) !important;
                background:linear-gradient(105deg,rgba(216,221,227,.78),rgba(239,241,243,.91) 58%,rgba(225,229,233,.72)) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="artifact"] .inventory-v48-abilities {
                border-top-color:rgba(164,174,190,.47) !important;
                background:linear-gradient(110deg,rgba(221,225,233,.90),rgba(252,253,255,.92) 44%,rgba(211,218,228,.83)) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="artifact"]::after {
                content:'';display:block;position:absolute;inset:2px;z-index:1;
                border-radius:12px;pointer-events:none;
                background:radial-gradient(ellipse 72% 108% at 52% 48%,rgba(255,255,255,.28),transparent 72%),
                    radial-gradient(ellipse 52% 72% at 78% 22%,rgba(255,255,255,.40),transparent 70%),
                    linear-gradient(108deg,transparent 0%,transparent 34%,rgba(255,255,255,.10) 41%,rgba(255,255,255,.58) 48%,rgba(255,255,255,.92) 50%,rgba(255,255,255,.58) 52%,rgba(255,255,255,.10) 59%,transparent 66%,transparent 100%) 132% 0 / 230% 100% no-repeat;
                opacity:.44 !important;
                animation:vault-v61-silver-artifact-glint 6.6s ease-in-out infinite !important;
            }
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="legendary"]::before,
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="artifact"]::before {
                    content:'' !important;display:block !important;position:absolute !important;
                    inset:-2px !important;width:auto !important;height:auto !important;
                    z-index:8 !important;padding:2px !important;border-radius:inherit !important;
                    border:0 !important;pointer-events:none !important;
                    background:conic-gradient(from var(--vault-v56-silver-angle),transparent 0deg 270deg,
                        rgba(222,229,237,.12) 296deg,rgba(255,255,255,.94) 319deg,
                        rgba(182,193,209,.55) 337deg,transparent 360deg) !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    animation:vault-v56-silver-orbit 14s linear infinite !important;
                }
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="mundane"][data-rarity="artifact"]::before {
                    inset:-3px !important;padding:2.5px !important;
                    background:conic-gradient(from var(--vault-v56-silver-angle),transparent 0deg 256deg,
                        rgba(225,234,245,.24) 277deg,#fff 299deg,#d7e4f6 319deg,
                        rgba(255,255,255,.95) 334deg,transparent 354deg) !important;
                    filter:drop-shadow(0 0 3px rgba(242,247,255,.70)) !important;
                    animation-duration:7.2s !important;
                }
            }

            /* Modal: mesmas superfícies reais do Mestre, só os controles
               continuam com as variáveis funcionais de cada tema do Player. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) {
                position:relative;isolation:isolate;
                border-color:var(--vault-edge) !important;
                background:linear-gradient(155deg,#fffaf0,#faf7f0) !important;
                box-shadow:0 24px 65px rgba(35,25,17,.20),
                           0 0 0 1px color-mix(in srgb,var(--vault-edge) 20%,transparent) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v17-detail-topbar {
                border-bottom-color:var(--vault-edge) !important;
                background:linear-gradient(110deg,var(--vault-modal-tint),#fbf8f1) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v17-detail-scroll {
                background:linear-gradient(120deg,#fffaf2,#fcf6e9 60%,#fffaf2) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v94-detail-art {
                border-color:var(--vault-edge) !important;
                background:radial-gradient(circle at 50% 35%,#fff,var(--vault-modal-tint)) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.8) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v17-item-title {
                color:var(--vault-ink) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v11-ability-card {
                border-color:color-mix(in srgb,var(--vault-edge) 52%,transparent) !important;
                background:color-mix(in srgb,#fff 72%,var(--vault-pale) 28%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v11-ability-header {
                background:color-mix(in srgb,#fff 60%,var(--vault-pale) 40%) !important;
                border-bottom-color:color-mix(in srgb,var(--vault-edge) 44%,transparent) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v11-ability-body {
                background:color-mix(in srgb,#fff 84%,var(--vault-pale) 16%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] {
                border-color:#c2a171 !important;
                background:linear-gradient(155deg,#fffaf0,#fbf5e8) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-scroll {
                background:radial-gradient(ellipse 72% 88% at 68% 34%,rgba(232,184,85,.10),transparent 74%),
                    radial-gradient(ellipse 62% 92% at 38% 68%,rgba(244,214,150,.08),transparent 78%),
                    linear-gradient(106deg,transparent 26%,rgba(230,177,75,.018) 38%,rgba(246,212,145,.12) 45%,rgba(255,250,222,.27) 50%,rgba(227,179,82,.12) 56%,transparent 69%) var(--vault-v81-liquid-x) 50% / 235% 100% no-repeat,
                    linear-gradient(120deg,#fffaf2,#fcf6e9 60%,#fffaf2) !important;
                opacity:1 !important;
                animation:vault-v81-modal-gold-travel 11.5s ease-in-out infinite !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-topbar {
                background:linear-gradient(100deg,#f1dfb9,#f8edd6) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v94-detail-art {
                border-color:#c8a365 !important;
                background:radial-gradient(circle at 50% 35%,#fff9e9,#f0e1c3) !important;
                box-shadow:0 0 0 3px rgba(217,180,106,.14) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                border-color:#76a9c1 !important;
                background:linear-gradient(119deg,#edf8fb 0%,#fbfeff 42%,#dcebf2 100%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-topbar {
                background:linear-gradient(115deg,rgba(205,233,244,.87),rgba(249,253,255,.93)) !important;
                border-bottom-color:rgba(100,157,181,.42) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-scroll {
                background:linear-gradient(119deg,#edf8fb 0%,#fbfeff 42%,#dcebf2 100%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v94-detail-art {
                background:radial-gradient(ellipse at 50% 41%,#daeff4 2%,#77a9bb 68%,#386079 100%) !important;
                border-color:#72a6ba !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-item-title {
                color:#26566d !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-card {
                background:#f6fcfe !important;border-color:rgba(96,146,171,.38) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-header {
                background:#d6edf6 !important;border-bottom-color:rgba(96,146,171,.24) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                border-color:#a5acb4 !important;
                background:linear-gradient(124deg,#f5f6f8,#f9fafb 49%,#e7e9ec) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-topbar {
                background:linear-gradient(110deg,#dfe2e6,#f5f6f8 68%,#e9ecef) !important;
                border-bottom-color:rgba(150,159,170,.40) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v17-detail-scroll {
                background:linear-gradient(124deg,#f5f6f8,#f9fafb 49%,#e7e9ec) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="artifact"] {
                border-color:#b5b9c3 !important;
                background:linear-gradient(115deg,#f8f9fb,#fff,#eff0f4) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="artifact"] .inventory-v17-detail-scroll {
                background:radial-gradient(ellipse at 87% 12%,rgba(255,255,255,.91),transparent 58%),
                          linear-gradient(125deg,#fff,#f0f0f2) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"][data-rarity="artifact"] .inventory-v94-detail-art {
                background:radial-gradient(ellipse at 46% 32%,#fff,#dee2e9) !important;
                border-color:#aeb2bd !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-card {
                background:#e5e8ed !important;border-color:rgba(137,149,166,.44) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .inventory-v11-ability-header {
                background:#d8dde5 !important;border-bottom-color:rgba(127,141,160,.34) !important;
            }

            /* SEM animação nem tratamento novo nos itens consumidos. */
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card[data-rarity="artifact"]::before,
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card[data-rarity="artifact"]::after,
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card[data-rarity="artifact"] > .inventory-v203-liquid-gold,
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35-detail-modal[data-rarity="artifact"]::before,
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-scroll {
                    animation:none !important;
                }
            }


            @property --vestige-angle { syntax:'<angle>'; inherits:false; initial-value:0deg; }

            @keyframes vault-vestige-aura { to { --vestige-angle: 360deg; } }

            @keyframes vault-v65-golden-interior {
                0%,14% {
                    background-position:0 0, 144% 0, 0 0;
                    opacity:.30;
                    box-shadow:inset 0 0 16px rgba(241,207,139,.11),0 0 8px rgba(224,173,83,.12);
                }
                32% {
                    background-position:0 0, 90% 0, 0 0;
                    opacity:.43;
                }
                50% {
                    background-position:0 0, 14% 0, 0 0;
                    opacity:.68;
                    box-shadow:inset 0 0 24px rgba(248,216,149,.30),0 0 20px 2px rgba(239,193,102,.27);
                }
                69% {
                    background-position:0 0, -80% 0, 0 0;
                    opacity:.47;
                }
                88%,100% {
                    background-position:0 0, -144% 0, 0 0;
                    opacity:.30;
                    box-shadow:inset 0 0 16px rgba(241,207,139,.11),0 0 8px rgba(224,173,83,.12);
                }
            }

            @keyframes vault-v79-liquid-pass {
                0%,9%   {background-position:50% 50%,140% 50%;opacity:.56;}
                43%     {background-position:50% 50%,53% 50%;opacity:.88;}
                76%     {background-position:50% 50%,-22% 50%;opacity:.68;}
                100%    {background-position:50% 50%,-52% 50%;opacity:.55;}
            }

            @property --vault-v81-liquid-x {
                syntax:'<percentage>';
                inherits:false;
                initial-value:140%;
            }

            @keyframes vault-v81-modal-gold-travel {
                0%,100% { --vault-v81-liquid-x:140%; }
                50% { --vault-v81-liquid-x:-25%; }
            }

            @keyframes vault-v51-artifact-halo {
                0%,100% { box-shadow:0 5px 16px rgba(45,105,133,.11),inset 0 1px 0 rgba(255,255,255,.96),0 0 0 1px rgba(102,173,197,.20),0 0 12px rgba(94,191,215,.08); }
                29% { box-shadow:0 8px 22px rgba(45,105,133,.19),inset 0 1px 0 rgba(255,255,255,.96),0 0 0 1px rgba(114,194,216,.34),0 0 18px rgba(94,191,215,.19); }
                61% { box-shadow:0 9px 23px rgba(45,105,133,.15),inset 0 1px 0 rgba(255,255,255,.96),0 0 0 1px rgba(113,184,211,.28),0 0 17px rgba(94,191,215,.13); }
            }

            @keyframes vault-v51-artifact-wash {
                0%,100% {opacity:.10;background-position:0 0,120% 50%;}
                24% {opacity:.25;background-position:0 0,85% 50%;}
                57% {opacity:.14;background-position:0 0,28% 50%;}
                79% {opacity:.24;background-position:0 0,-30% 50%;}
            }

            @property --vault-v56-silver-angle {
                syntax:'<angle>';
                initial-value:0deg;
                inherits:false;
            }

            @keyframes vault-v56-silver-orbit {
                to {--vault-v56-silver-angle:360deg;}
            }


            /* ===============================================================
               SPELLBOUND V66.2.06 — LIMPEZA DA ÁREA DE HABILIDADES ESPECIAIS
               - remove a “caixa dentro da caixa” nos cards claros;
               - preserva o material dourado/prateado/azul do Cofre;
               - deixa os trackers 1/1, 2/2 etc. tingidos pela raridade/natureza.
               =============================================================== */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-abilities {
                position: relative;
                overflow: hidden;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-abilities::before {
                content: none !important;
                display: none !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill {
                background: transparent !important;
                border-left-width: 0 !important;
                border-radius: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                box-shadow: none !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill:hover {
                border-left-color: transparent !important;
                background: color-mix(in srgb, var(--vault-pale) 42%, transparent) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v11-mini-tracker,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v48-use,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v11-mini-tracker,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-use {
                border-color: color-mix(in srgb, var(--vault-edge) 62%, rgba(255,255,255,.30)) !important;
                background: linear-gradient(135deg,
                    color-mix(in srgb, var(--vault-pale) 92%, #ffffff 8%) 0%,
                    color-mix(in srgb, var(--vault-pale) 74%, var(--vault-shade) 26%) 100%) !important;
                color: color-mix(in srgb, var(--vault-ink) 68%, #5e4333 32%) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.75), 0 1px 2px color-mix(in srgb, var(--vault-edge) 10%, transparent) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v11-mini-tracker button,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v11-mini-tracker button,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v11-mini-tracker strong,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v11-mini-tracker strong,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v48-use,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-use {
                color: color-mix(in srgb, var(--vault-ink) 70%, #4b3224 30%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v11-mini-tracker button:hover,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v11-mini-tracker button:hover,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v48-use:hover,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-use:hover {
                background: linear-gradient(135deg,
                    color-mix(in srgb, var(--vault-pale) 84%, #ffffff 16%) 0%,
                    color-mix(in srgb, var(--vault-pale) 58%, var(--vault-shade) 42%) 100%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v49-skill .inventory-v48-passive,
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal:is([data-rarity="legendary"],[data-rarity="artifact"]):is([data-nature="wonderful"],[data-nature="technological"],[data-nature="mundane"]) .inventory-v48-passive {
                color: color-mix(in srgb, var(--vault-ink) 44%, #876446 56%) !important;
            }



            /* ===============================================================
               SPELLBOUND V66.2.08 — CORREÇÕES PONTUAIS SOBRE O BLADE ORIGINAL
               1. Cards de armas: o resumo deve terminar antes de Habilidades.
               2. Artefatos Maravilhosos: habilidades com o próprio ouro.
               Nenhuma alteração na estrutura, progressão ou outras naturezas.
               =============================================================== */

            /* A descrição da arma complexa usa exatamente 3 linhas inteiras.
               O limite passa ao span que contém o texto; a caixa externa não
               pode permitir que o início de uma 4ª linha vaze sobre a borda. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v50-vestige)
            .inventory-v48-hero.inventory-v89-hero-complex + .inventory-v49-description {
                display: block !important;
                flex: 0 0 53px !important;
                box-sizing: border-box !important;
                width: 100% !important;
                height: 53px !important;
                min-height: 53px !important;
                max-height: 53px !important;
                padding: 3px 11px 8px !important;
                margin: 0 !important;
                overflow: hidden !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v50-vestige)
            .inventory-v48-hero.inventory-v89-hero-complex + .inventory-v49-description
            > .inventory-v92-description-copy {
                display: -webkit-box !important;
                -webkit-box-orient: vertical !important;
                -webkit-line-clamp: 3 !important;
                line-clamp: 3;
                max-height: 42px !important;
                line-height: 14px !important;
                overflow: hidden !important;
                overflow-wrap: anywhere;
                text-overflow: ellipsis;
            }

            /* O Cofre já diferencia a raridade. No Player, a superfície das
               habilidades dos Artefatos Maravilhosos deve ser pergaminho
               dourado, sem cartões brancos internos destoando do material. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-panel {
                border-color: rgba(189,143,66,.60) !important;
                background: linear-gradient(145deg,#f9edd4 0%,#f5e4bf 58%,#fff3dc 100%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-header {
                border-bottom-color: rgba(186,141,64,.38) !important;
                background: linear-gradient(105deg,#f1dcae 0%,#f8e8c7 67%,#fbefd7 100%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-feature {
                border-color: rgba(184,138,63,.42) !important;
                background: linear-gradient(125deg,#f9ebcf 0%,#f8e7c5 55%,#fff1d5 100%) !important;
                box-shadow: inset 0 1px 0 rgba(255,254,246,.65) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v11-ability-card {
                border-color: rgba(184,138,63,.44) !important;
                background: linear-gradient(125deg,#f9ebcf,#f7e3bb 64%,#fff0d5) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v11-ability-header {
                border-bottom-color: rgba(184,138,63,.35) !important;
                background: linear-gradient(110deg,#f1dcae,#f9e9ca) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v11-ability-body {
                background: linear-gradient(125deg,#faedd5,#f8e5c1) !important;
            }

            /* Os temas noturnos mantêm a superfície escura aprovada, mas os
               Artefatos conservam sua identidade com um esmalte ouro-bronze
               na área de habilidades, sem cartão claro ou perda de contraste. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v50-stage-panel,.inventory-v11-ability-card) {
                border-color: rgba(199,151,72,.45) !important;
                background: linear-gradient(120deg,
                    color-mix(in srgb,var(--theme-surface-card) 88%,#c39448 12%),
                    color-mix(in srgb,var(--theme-surface-card) 92%,#cfaa61 8%)) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v50-stage-header,.inventory-v11-ability-header) {
                border-bottom-color: rgba(199,151,72,.32) !important;
                background: color-mix(in srgb,var(--theme-surface-raised) 82%,#b7873e 18%) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v50-stage-feature,.inventory-v11-ability-body) {
                border-color: rgba(196,152,78,.40) !important;
                background: linear-gradient(115deg,
                    color-mix(in srgb,var(--theme-surface-card) 85%,#b98d47 15%),
                    color-mix(in srgb,var(--theme-surface-card) 91%,#d3ae68 9%)) !important;
            }


            /* ================================================================
               SPELLBOUND V66.2.10 — VESTÍGIOS LEVES, OURO COM PROFUNDIDADE
               Base: V66.2.09. Substitui animações por estágio que causavam
               repintura contínua e mantém uma única passagem de luz no topo.
               Sem alteração da lógica de poderes, proficiência ou usos.
               ================================================================ */

            /* Identidade do Vestígio: três superfícies distintas, sem painéis
               duplicados com o mesmo amarelo. Renderização inteiramente estática
               na região rolável. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-panel {
                border-color:rgba(190,142,66,.56) !important;
                background:linear-gradient(137deg,#fff9ed 0%,#fbf1da 55%,#f5e4bf 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.85),0 5px 15px rgba(150,101,38,.05) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-header {
                border-bottom-color:rgba(185,137,56,.38) !important;
                background:linear-gradient(105deg,#ead09a 0%,#f4e0b8 42%,#fff4dc 100%) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-feature {
                border-color:rgba(199,157,90,.38) !important;
                background:linear-gradient(118deg,#fffdfa 0%,#fff9ee 62%,#f9ead0 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.84) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-feature-head span {
                color:#a7762b !important;
            }

            /* Midnight/Obsidian: manter fundo escuro, separar estágios e
               habilidades com bronze suave, sem telas opacas sobre o texto. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-panel {
                border-color:rgba(202,156,79,.43) !important;
                background:linear-gradient(130deg,
                    color-mix(in srgb,var(--theme-surface-card) 91%,#b78a42 9%),
                    color-mix(in srgb,var(--theme-surface-card) 85%,#bd9650 15%)) !important;
                box-shadow:inset 0 1px 0 rgba(245,216,154,.07) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-header {
                border-bottom-color:rgba(207,164,88,.37) !important;
                background:linear-gradient(105deg,
                    color-mix(in srgb,var(--theme-surface-raised) 69%,#ab7d35 31%),
                    color-mix(in srgb,var(--theme-surface-raised) 86%,#e1b868 14%)) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-feature {
                border-color:rgba(198,150,74,.38) !important;
                background:linear-gradient(115deg,
                    color-mix(in srgb,var(--theme-surface-card) 96%,#e3bd73 4%),
                    color-mix(in srgb,var(--theme-surface-card) 89%,#c09349 11%)) !important;
                box-shadow:inset 0 1px 0 rgba(252,223,158,.035) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-feature-head span {
                color:#ddb46c !important;
            }

            /* Otimização: desativar a camada que antes redesenhava, frame a
               frame, cada estágio e seu cabeçalho durante a rolagem. */
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"]
            .inventory-v50-stage-panel::before,
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"]
            .inventory-v50-stage-header::after {
                content:none !important;
                animation:none !important;
            }

            /* A antiga V81 também animava um gradiente do tamanho de toda
               a região rolável do modal. No Vestígio, ela fica estática para
               evitar repintura de centenas de linhas a cada frame. A luz
               de borda V65 segue ativa e há um glint barato no cabeçalho. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"]
            .inventory-v17-detail-scroll {
                animation:none !important;
                background:
                    radial-gradient(ellipse 72% 88% at 68% 34%,rgba(232,184,85,.10),transparent 74%),
                    radial-gradient(ellipse 62% 92% at 38% 68%,rgba(244,214,150,.08),transparent 78%),
                    linear-gradient(120deg,#fffaf2,#fcf6e9 60%,#fffaf2) !important;
            }

            /* Um único reflexo compositor (transform/opacity) no cabeçalho
               fixo, em vez de múltiplos gradientes animados por habilidade. */
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"]
            .inventory-v17-detail-topbar {
                position:relative;
                overflow:hidden;
            }
            .character-sheet-theme[data-theme]
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"]
            .inventory-v17-detail-topbar::after {
                content:'';
                position:absolute;
                inset:0 auto 0 -24%;
                width:24%;
                background:linear-gradient(90deg,transparent,rgba(255,242,195,.24),transparent);
                pointer-events:none;
                opacity:0;
                transform:translate3d(0,0,0);
                animation:inventory-v210-topbar-glint 13s ease-in-out infinite;
            }
            @keyframes inventory-v210-topbar-glint {
                0%,18% {opacity:0;transform:translate3d(0,0,0);}
                28% {opacity:.65;}
                65% {opacity:.65;}
                77%,100% {opacity:0;transform:translate3d(525%,0,0);}
            }

            /* Trackers de Artefato Maravilhoso: ouro próprio, inclusive
               botões +/- e contagem. Isolado desta natureza e raridade. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker,.inventory-v48-use,.inventory-v11-recovery-pill) {
                border-color:rgba(187,140,61,.58) !important;
                background:linear-gradient(135deg,#faf0da,#f0d9a6 60%,#f8edcf) !important;
                color:#754b20 !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.8) !important;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker) :is(button,strong),
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v48-use,.inventory-v11-recovery-pill) {
                color:#754b20 !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker,.inventory-v48-use,.inventory-v11-recovery-pill) {
                border-color:rgba(200,155,77,.54) !important;
                background:linear-gradient(135deg,
                    color-mix(in srgb,var(--theme-surface-raised) 80%,#a67934 20%),
                    color-mix(in srgb,var(--theme-surface-raised) 86%,#d9b469 14%)) !important;
                color:#f2daad !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker) :is(button,strong),
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            :is(.inventory-v48-item-card,.inventory-v35-detail-modal)[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v48-use,.inventory-v11-recovery-pill) {
                color:#f2daad !important;
            }

            @media(prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"]
                .inventory-v17-detail-topbar::after {animation:none !important;content:none !important;}
            }



            /* V66.2.12 — Artefato Tecnológico: marcadores de uso em ciano.
               Restrito ao item tecnológico Artefato. Não toca na animação
               V51/V54, no tema do modal ou na lógica dos contadores. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"],
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] {
                --inv212-tech-usage-border: rgba(92,163,188,.60);
                --inv212-tech-usage-fg: #285e77;
                --inv212-tech-usage-bg: linear-gradient(125deg,#e6f5f9 0%,#cceaf2 64%,#e0f3f8 100%);
                --inv212-tech-usage-hover: #b3dee9;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"],
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] {
                --inv212-tech-usage-border: rgba(104,193,215,.46);
                --inv212-tech-usage-fg: #bdebf3;
                --inv212-tech-usage-bg: linear-gradient(125deg,rgba(56,112,135,.24),rgba(72,150,176,.19) 65%,rgba(53,117,145,.24));
                --inv212-tech-usage-hover: rgba(92,177,203,.25);
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker,.inventory-v11-recovery-pill,.inventory-v48-use),
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker,.inventory-v11-recovery-pill,.inventory-v48-use) {
                border: 1px solid var(--inv212-tech-usage-border) !important;
                background: var(--inv212-tech-usage-bg) !important;
                color: var(--inv212-tech-usage-fg) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.13) !important;
            }
            /* A ficha possui overrides mais específicos para o rótulo /DIA
               e para o mini-tracker do card. Preservar o ciano também neles. */
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal.inventory-v50-detail-artifact[data-nature="technological"][data-rarity="artifact"]
            .inventory-v11-ability-header .inventory-v11-recovery-pill {
                color: var(--inv212-tech-usage-fg) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used):not([data-rarity="legendary"])[data-nature="technological"][data-rarity="artifact"]
            .inventory-v49-skill .inventory-v11-mini-tracker {
                border-color: var(--inv212-tech-usage-border) !important;
                background: var(--inv212-tech-usage-bg) !important;
                color: var(--inv212-tech-usage-fg) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.13) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker) :is(button,strong),
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker) :is(button,strong) {
                color: var(--inv212-tech-usage-fg) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal[data-nature="technological"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker) button:hover:not(:disabled),
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            :is(.inventory-v10-tracker,.inventory-v11-mini-tracker) button:hover:not(:disabled) {
                background: var(--inv212-tech-usage-hover) !important;
                color: var(--inv212-tech-usage-fg) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used):not([data-rarity="legendary"])[data-nature="technological"][data-rarity="artifact"]
            .inventory-v49-skill .inventory-v11-mini-tracker :is(button,strong) {
                color: var(--inv212-tech-usage-fg) !important;
            }


            /* ================================================================
               SPELLBOUND V66.2.16 — BRILHO INTERNO REALÇADO (ARTEFATO)
               Mantém o reflexo de ponta a ponta da V66.2.15, mas com
               ouro mais vivo e profundidade no mesmo feixe animado.
               A película continua ATRÁS dos textos e controles (z-index:2).
               Nenhuma camada HTML ou animação extra; temas escuros intactos.
               ================================================================ */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] > .inventory-v203-liquid-gold {
                display:block !important;
                position:absolute !important;
                inset:0 !important;
                left:0 !important;
                top:0 !important;
                width:100% !important;
                height:100% !important;
                z-index:2 !important;
                overflow:hidden !important;
                border-radius:inherit !important;
                pointer-events:none !important;
                /* O tom leve cobre imagem, cabeçalho, descrição e habilidades. */
                background:
                    radial-gradient(ellipse 84% 76% at 24% 26%,rgba(255,232,170,.14),transparent 84%),
                    radial-gradient(ellipse 92% 92% at 83% 76%,rgba(239,185,83,.12),transparent 86%),
                    linear-gradient(110deg,
                        rgba(248,213,135,.085) 0%,
                        rgba(255,240,191,.115) 48%,
                        rgba(246,200,111,.09) 100%) !important;
                background-size:100% 100% !important;
                opacity:1 !important;
                transform:none !important;
                animation:none !important;
                will-change:auto !important;
            }
            /* Feixe de luz mais aparente percorre a largura COMPLETA.
               Continua animando apenas transform para preservar desempenho. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] > .inventory-v203-liquid-gold::after {
                content:'';
                position:absolute;
                inset:0;
                pointer-events:none;
                border-radius:inherit;
                /* Duas reflexões de ouro no mesmo elemento em movimento:
                   uma aura larga e um brilho fino, sem tocar nas letras. */
                background:
                    radial-gradient(ellipse 32% 78% at 52% 50%,
                        rgba(255,238,165,.42) 0%,
                        rgba(245,196,97,.23) 49%,
                        transparent 100%),
                    linear-gradient(105deg,
                        transparent 16%,
                        rgba(230,170,67,.065) 28%,
                        rgba(238,185,80,.21) 38%,
                        rgba(254,230,155,.39) 45%,
                        rgba(255,249,206,.60) 50%,
                        rgba(248,207,111,.32) 56%,
                        rgba(222,163,58,.12) 66%,
                        transparent 83%);
                transform:translate3d(-105%,0,0);
                will-change:transform;
                animation:inventory-v214-left-to-right 10.5s linear infinite;
            }
            /* A película secundária se torna apenas um acabamento homogêneo.
               Remove o reflexo fixo deslocado para a metade direita. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"]::after {
                z-index:1 !important;
                border-radius:inherit !important;
                background:
                    radial-gradient(ellipse 80% 70% at 20% 10%,rgba(255,248,208,.31),transparent 78%),
                    radial-gradient(ellipse 80% 85% at 84% 94%,rgba(236,183,84,.16),transparent 86%) !important;
                opacity:.44 !important;
                animation:none !important;
                box-shadow:none !important;
            }
            @keyframes inventory-v214-left-to-right {
                0%,8%   { transform:translate3d(-105%,0,0); }
                82%,100% { transform:translate3d(105%,0,0); }
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
                .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] > .inventory-v203-liquid-gold::after {
                    animation:none !important;
                    transform:none !important;
                    opacity:0 !important;
                    will-change:auto !important;
                }
            }

            /* ====================================================================
               SPELLBOUND V66.2.21 — FLUXO ELÉTRICO CONTÍNUO
               O pulso percorre o caminho sem estacionar enquanto está visível.
               Quatro janelas de atividade e os delays/durações individuais
               mantêm o aspecto irregular, sem saltos nem redraw caro por filtros.
               ==================================================================== */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] {
                position:relative !important;
                isolation:isolate;
                --inv219-current:rgba(73,179,214,.94);
                --inv219-visibility:.68;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] {
                --inv219-current:rgba(165,236,251,.91);
                --inv219-visibility:.74;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] > .inventory-v217-tech-wave {
                display:block;
                position:absolute;
                inset:0;
                overflow:hidden;
                border-radius:inherit;
                z-index:3;
                pointer-events:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v217-tech-wave svg {
                display:block;
                width:100%;
                height:100%;
                fill:none;
                stroke-linecap:round;
                stroke-linejoin:round;
            }
            /* Sem drop-shadow por traço: filtros SVG repetidos causavam
               repintura desnecessária e perda de fluidez no Inventário. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v219-signal {
                fill:none;
                stroke:var(--inv219-current);
                stroke-width:1.22;
                vector-effect:non-scaling-stroke;
                stroke-dasharray:var(--inv219-len,14) var(--inv219-gap,94);
                stroke-dashoffset:18;
                opacity:0;
                filter:none;
                animation:inventory-v219-signal var(--inv219-duration,15s) linear var(--inv219-delay,0s) infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v219-signal.is-alternate {
                animation-name:inventory-v219-signal-alternate;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v219-signal.is-late {
                animation-name:inventory-v219-signal-late;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v219-signal.is-drifter {
                animation-name:inventory-v219-signal-drifter;
            }
            /* V66.2.25 — BRILHO SUTILMENTE MAIS VIVO
               Leve aumento de luminosidade/espessura sem efeitos de filtro
               por traço, preservando a fluidez validada na V66.2.24. */
            /* V66.2.24 — PULSO QUE CHEGA DE UMA ORIGEM
               Antes de ficar visível, o dash está completamente fora do
               início de cada caminho. A entrada revela o primeiro traço e
               mantém velocidade praticamente constante até desaparecer.
               As quatro janelas continuam com horários independentes. */
            @keyframes inventory-v219-signal {
                0%,17% {stroke-dashoffset:18;opacity:0;}
                22% {stroke-dashoffset:2;opacity:.07;}
                27% {stroke-dashoffset:-14;opacity:.19;}
                35% {stroke-dashoffset:-41;opacity:var(--inv219-peak,.64);}
                44% {stroke-dashoffset:-70;opacity:calc(var(--inv219-peak,.64) * .72);}
                53% {stroke-dashoffset:-99;opacity:.12;}
                58%,100% {stroke-dashoffset:-115;opacity:0;}
            }
            @keyframes inventory-v219-signal-alternate {
                0%,34% {stroke-dashoffset:18;opacity:0;}
                39% {stroke-dashoffset:2;opacity:.07;}
                44% {stroke-dashoffset:-14;opacity:.18;}
                53% {stroke-dashoffset:-43;opacity:var(--inv219-peak,.66);}
                61% {stroke-dashoffset:-69;opacity:calc(var(--inv219-peak,.66) * .72);}
                69% {stroke-dashoffset:-96;opacity:.12;}
                74%,100% {stroke-dashoffset:-113;opacity:0;}
            }
            @keyframes inventory-v219-signal-late {
                0%,51% {stroke-dashoffset:18;opacity:0;}
                56% {stroke-dashoffset:2;opacity:.07;}
                61% {stroke-dashoffset:-14;opacity:.19;}
                70% {stroke-dashoffset:-43;opacity:var(--inv219-peak,.68);}
                78% {stroke-dashoffset:-69;opacity:calc(var(--inv219-peak,.68) * .72);}
                87% {stroke-dashoffset:-98;opacity:.11;}
                92%,100% {stroke-dashoffset:-114;opacity:0;}
            }
            @keyframes inventory-v219-signal-drifter {
                0%,9% {stroke-dashoffset:18;opacity:0;}
                14% {stroke-dashoffset:2;opacity:.06;}
                19% {stroke-dashoffset:-14;opacity:.16;}
                28% {stroke-dashoffset:-43;opacity:var(--inv219-peak,.60);}
                36% {stroke-dashoffset:-69;opacity:calc(var(--inv219-peak,.60) * .72);}
                45% {stroke-dashoffset:-98;opacity:.10;}
                50%,100% {stroke-dashoffset:-114;opacity:0;}
            }
            /* O nome, descrição e as habilidades permanecem acima do efeito. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] > :is(.inventory-v48-hero,.inventory-v49-description) {
                position:relative;
                z-index:4;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] > .inventory-v48-abilities {
                position:static !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] > .inventory-v48-abilities > * {
                position:relative;
                z-index:4;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v219-signal {
                    animation:none !important;
                    opacity:0 !important;
                    filter:none !important;
                }
            }

            /* ================================================================
               SPELLBOUND V66.2.27 — FLUXO TECNOLÓGICO COM BIFURCAÇÃO REAL
               Entradas pelas bordas, encontro de energia em dois nós, dois
               braços após cada encontro e extinção gradual. Sem filtros SVG.
               Dez caminhos no total, sem alterar a animação exclusiva da foto.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] {
                /* V66.2.35: nos temas claros, o ciano precisa ter contraste
                   contra o fundo quase branco. O halo ainda é suave. */
                --inv227-current:rgba(25,132,171,.98);
                --inv227-head:rgba(16,172,209,.98);
                --inv227-node-color:rgba(12,158,194,.94);
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] {
                --inv227-current:rgba(143,227,246,.74);
                --inv227-head:rgba(223,251,255,.87);
                --inv227-node-color:rgba(199,247,253,.78);
            }
            /* Apenas temas claros: linhas um pouco mais presentes para
               que energia e bifurcações possam ser vistas sobre o azul pálido.
               A cor, velocidade e distribuição dos temas escuros não mudam. */
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] .inventory-v227-trace {
                stroke-width:1.26;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] .inventory-v227-quiet {
                stroke-width:1.03;
            }
            .character-sheet-theme[data-theme]:not(:is([data-theme="midnight"],[data-theme="obsidian"]))
            .inventory-v35 .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="technological"][data-rarity="artifact"] .inventory-v227-head {
                stroke-width:2.1;
            }
            /* A camada exterior nunca entra no quadrado da fotografia:
               canto superior esquerdo recortado, inclusive em mobile. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] > .inventory-v217-tech-wave {
                clip-path:polygon(102px 0,100% 0,100% 100%,0 100%,0 112px,102px 112px);
                pointer-events:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v227-trace,
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v227-head {
                fill:none;
                vector-effect:non-scaling-stroke;
                stroke-linejoin:round;
                stroke-linecap:round;
                stroke-dashoffset:18;
                opacity:0;
                filter:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v227-trace {
                stroke:var(--inv227-current);
                stroke-width:1.08;
                stroke-dasharray:13 110;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v227-quiet {
                stroke-width:.87;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v227-head {
                stroke:var(--inv227-head);
                stroke-width:1.9;
                stroke-dasharray:2.5 120;
            }
            /* V66.2.31: sem ciclos CSS fixos. Cada cartão sorteia eventos
               e move, via Web Animations, apenas as trilhas selecionadas. */
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] :is(.inventory-v227-stem,.inventory-v227-arm,.inventory-v227-roam,.inventory-v227-head) {
                animation:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v227-node {
                fill:var(--inv227-node-color);
                opacity:0;
                transform-box:fill-box;
                transform-origin:center;
                animation:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] .inventory-v231-spark {
                fill:var(--inv227-head);
                opacity:0;
                filter:none;
                pointer-events:none;
            }
            /* O traço do tronco chega primeiro. O nó pisca após a chegada.
               As duas saídas começam juntas e logo perdem intensidade. */
            @keyframes inventory-v227-stem {
                0%,12% {stroke-dashoffset:18;opacity:0;}
                17% {stroke-dashoffset:1;opacity:.10;}
                24% {stroke-dashoffset:-31;opacity:calc(var(--inv227-peak,.48) * .65);}
                31% {stroke-dashoffset:-72;opacity:var(--inv227-peak,.48);}
                37% {stroke-dashoffset:-111;opacity:.10;}
                40%,100% {stroke-dashoffset:-127;opacity:0;}
            }
            @keyframes inventory-v227-arm {
                0%,33% {stroke-dashoffset:18;opacity:0;}
                38% {stroke-dashoffset:1;opacity:.11;}
                45% {stroke-dashoffset:-33;opacity:var(--inv227-peak,.40);}
                52% {stroke-dashoffset:-74;opacity:calc(var(--inv227-peak,.40) * .55);}
                58%,100% {stroke-dashoffset:-119;opacity:0;}
            }
            @keyframes inventory-v227-ping {
                0%,32% {opacity:0;transform:scale(.65);}
                36% {opacity:.16;transform:scale(.85);}
                40% {opacity:.52;transform:scale(1.05);}
                45% {opacity:.08;transform:scale(1.24);}
                49%,100% {opacity:0;transform:scale(.7);}
            }
            @keyframes inventory-v227-roam {
                0%,19% {stroke-dashoffset:18;opacity:0;}
                24% {stroke-dashoffset:1;opacity:.08;}
                34% {stroke-dashoffset:-39;opacity:var(--inv227-peak,.36);}
                43% {stroke-dashoffset:-82;opacity:calc(var(--inv227-peak,.36) * .58);}
                52%,100% {stroke-dashoffset:-121;opacity:0;}
            }
            @keyframes inventory-v227-roam-offset {
                0%,48% {stroke-dashoffset:18;opacity:0;}
                53% {stroke-dashoffset:1;opacity:.08;}
                63% {stroke-dashoffset:-39;opacity:var(--inv227-peak,.37);}
                72% {stroke-dashoffset:-82;opacity:calc(var(--inv227-peak,.37) * .58);}
                81%,100% {stroke-dashoffset:-121;opacity:0;}
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35
                .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"] :is(.inventory-v227-trace,.inventory-v227-head,.inventory-v227-node) {
                    animation:none !important;
                    opacity:0 !important;
                }
            }

            /* =====================================================================
               SPELLBOUND V66.2.34 — MINIATURA TECNOLÓGICA EQUILIBRADA (TEMAS ESCUROS)
               Somente a miniatura do card: seis pontos suaves, trajetórias
               suaves, circuitos de fundo atenuados. Não modifica o efeito
               aleatório do card, a borda nem o modal de detalhes.
               ===================================================================== */
            @keyframes inventory-v233-photo-spark {
                0%,100% {transform:translate3d(0,0,0) scale(.65);opacity:.06;}
                32% {transform:translate3d(2px,-3px,0) scale(.90);opacity:.22;}
                72% {transform:translate3d(-2px,2px,0) scale(.76);opacity:.11;}
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            .inventory-v48-art .vault-v51-tech-energy .vault-v51-spark:nth-of-type(n+7) {
                display:none !important;
                animation:none !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            .inventory-v48-art .vault-v51-tech-energy .vault-v51-spark:nth-of-type(-n+6) {
                --size:2px;
                background:rgba(214,248,253,.52);
                box-shadow:0 0 3px rgba(189,231,244,.24);
                animation:inventory-v233-photo-spark var(--duration,9s) ease-in-out var(--delay,0s) infinite !important;
            }
            /* Dois pontos adicionais menores para devolver vida à foto sem encobrir o objeto. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            .inventory-v48-art .vault-v51-tech-energy .vault-v51-spark:nth-of-type(n+5):nth-of-type(-n+6) {
                --size:1.5px;
                background:rgba(209,245,252,.44);
                box-shadow:0 0 2px rgba(188,231,244,.19);
            }
            /* O SVG e a onda impressa na moldura deixam de competir com o item. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            .inventory-v48-art .vault-v51-tech-circuits {
                opacity:.22 !important;
                filter:none !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
            .inventory-v48-art::after {
                opacity:.09 !important;
                animation:none !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
                .inventory-v48-item-card[data-nature="technological"][data-rarity="artifact"]
                .inventory-v48-art .vault-v51-tech-energy .vault-v51-spark {
                    animation:none !important;
                    opacity:.13 !important;
                }
            }



            /* =====================================================================
               SPELLBOUND V66.2.36 — ARTEFATO MARAVILHOSO NOS TEMAS ESCUROS
               A hierarquia permanece: Lendário com ouro simbólico, Artefato
               com identidade própria. Escopo SOMENTE Artifacts Wonderful
               (incluindo Vestígios) em Midnight/Obsidian, sem alterar temas
               claros, Lendários, rastreadores, estágios ou lógica do item.
               ===================================================================== */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] {
                position:relative;
                isolation:isolate;
                border-color:rgba(217,173,91,.91) !important;
                background:
                    radial-gradient(ellipse 92% 81% at 83% 12%,rgba(214,163,68,.20) 0%,rgba(187,130,42,.095) 47%,transparent 83%),
                    radial-gradient(ellipse 80% 74% at 8% 94%,rgba(211,159,77,.12),transparent 78%),
                    linear-gradient(135deg,#373027 0%,#302b25 48%,#302923 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,229,161,.18),
                           0 0 0 1px rgba(214,167,80,.10),
                           0 7px 20px rgba(0,0,0,.18) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"]:is(:hover,:focus-visible) {
                border-color:rgba(238,194,107,.96) !important;
                box-shadow:inset 0 1px 0 rgba(255,231,173,.23),
                           0 0 0 1px rgba(215,167,80,.13),
                           0 9px 22px rgba(0,0,0,.20) !important;
            }
            /* Usa a película já existente no Blade. Fica atrás do conteúdo,
               cobre o card inteiro e não cria barra lateral ou camada sobre
               o texto. Uma única animação leve, preservando o feixe da borda. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] > .inventory-v203-liquid-gold {
                display:block !important;
                position:absolute !important;
                inset:0 !important;
                overflow:hidden !important;
                border-radius:inherit !important;
                pointer-events:none !important;
                z-index:1 !important;
                background:none !important;
                animation:none !important;
                opacity:1 !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] > .inventory-v203-liquid-gold::after {
                content:'';
                position:absolute;
                inset:0;
                border-radius:inherit;
                background:linear-gradient(108deg,transparent 10%,
                    rgba(227,178,86,.018) 32%,rgba(234,185,91,.085) 41%,
                    rgba(255,223,151,.16) 49%,rgba(255,240,184,.20) 52%,
                    rgba(213,156,66,.075) 60%,transparent 82%);
                transform:translate3d(-105%,0,0);
                animation:inventory-v236-dark-gold-flow 12.5s linear infinite;
                pointer-events:none;
            }
            @keyframes inventory-v236-dark-gold-flow {
                0%,10% {transform:translate3d(-105%,0,0);}
                80%,100% {transform:translate3d(105%,0,0);}
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] > :is(.inventory-v48-hero,.inventory-v49-description,.inventory-v48-abilities) {
                position:relative;
                z-index:2;
            }
            /* Imagem e tipografia mais claras, mas sem tingir a arte real. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v48-art {
                border-color:rgba(205,160,77,.77) !important;
                background:radial-gradient(ellipse at 52% 46%,rgba(199,151,67,.24),transparent 70%),
                           linear-gradient(145deg,#352d24,#262b2d) !important;
                box-shadow:inset 0 1px 0 rgba(255,225,168,.18),
                           0 0 11px rgba(222,170,78,.08) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v48-name {
                color:#f5e3bd !important;
                text-shadow:0 0 12px rgba(233,177,83,.12);
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] :is(.inventory-v48-classification,.inventory-v48-technical) {
                color:#d3bfa0 !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v49-description {
                color:#decfb9 !important;
            }
            /* Habilidades têm seu próprio ouro sutil, mas sem caixa interna
               extra: o fundo é aplicado diretamente à seção existente. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v48-abilities {
                border-top-color:rgba(220,172,88,.53) !important;
                background:linear-gradient(114deg,#3b3126 0%,#332c25 48%,#373025 100%) !important;
                box-shadow:inset 0 1px 0 rgba(254,217,150,.045) !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v48-abilities-header {
                color:#d8bb85 !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] :is(.inventory-v48-skill-name,.inventory-v48-passive) {
                color:#f1dfba !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v49-skill {
                border-bottom-color:rgba(213,174,105,.32) !important;
            }
            /* Os três indicadores continuam cumulativos e funcionais; apenas
               recebem material mais quente e legível, sem novos contêineres. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v92-vestige-stage {
                border-color:rgba(202,156,80,.53) !important;
                background:linear-gradient(120deg,rgba(101,77,44,.55),rgba(77,60,39,.38)) !important;
                color:#f0dbb3 !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="artifact"] .inventory-v92-vestige-stage.is-current {
                border-color:rgba(240,192,108,.80) !important;
                background:linear-gradient(115deg,rgba(129,93,44,.57),rgba(104,79,43,.48)) !important;
                color:#fff0ca !important;
                box-shadow:inset 0 1px 0 rgba(255,230,163,.19),0 0 10px rgba(235,177,78,.13) !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
                .inventory-v48-item-card[data-nature="wonderful"][data-rarity="artifact"] > .inventory-v203-liquid-gold::after {
                    animation:none !important;
                    opacity:0 !important;
                }
            }


            /* =====================================================================
               SPELLBOUND V66.2.37 — CONTRASTE DOS LENDÁRIOS / MODAL DO ARTEFATO
               Somente os temas MIDNIGHT e OBSIDIAN.
               - Lendários maravilhosos: restaurar contraste, ouro discreto e
                 nitidez da arte, sem transformá-los em Artefatos.
               - Artefatos maravilhosos: fundo e gavetas com bronze em camadas,
                 separação real entre níveis, texto legível e halo suave.
               - Preserva borda animada, estágio, trackers e eventos existentes.
               ===================================================================== */

            /* Lendário: contraste refinado sem aplicar película opaca. */
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] {
                border-color:rgba(197,163,99,.72) !important;
                background:
                    radial-gradient(ellipse 72% 60% at 86% 16%,rgba(199,151,74,.105),transparent 78%),
                    linear-gradient(130deg,#2d2f30 0%,#2d2e2e 52%,#302d29 100%) !important;
                box-shadow:inset 0 1px 0 rgba(237,207,148,.10),
                    0 5px 14px rgba(0,0,0,.12) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-art {
                border-color:rgba(199,165,104,.66) !important;
                background:radial-gradient(ellipse at 50% 40%,#514b3f 0%,#363533 72%,#2e3031 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,232,190,.13),
                    0 0 9px rgba(210,161,74,.07) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-art > img {
                opacity:1 !important;
                filter:brightness(1.16) contrast(1.10) saturate(1.08)
                    drop-shadow(0 3px 5px rgba(0,0,0,.17)) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-name {
                color:#faf2e4 !important;
                text-shadow:0 1px 0 rgba(0,0,0,.12);
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] :is(.inventory-v48-classification,.inventory-v48-technical) {
                color:#c3b4a3 !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v49-description {
                color:#d0c7bd !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-abilities {
                border-top-color:rgba(199,158,91,.36) !important;
                background:linear-gradient(112deg,#2b2b29 0%,#302d28 55%,#2f2d29 100%) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-abilities-header {
                color:#d5c4aa !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-skill-name {
                color:#f3e2c4 !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v48-passive {
                color:#b9a892 !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35
            .inventory-v48-item-card:not(.inventory-v43-item-used)[data-nature="wonderful"][data-rarity="legendary"] .inventory-v49-skill {
                border-bottom-color:rgba(197,167,117,.34) !important;
            }

            /* Artefato em detalhes: o fundo deve ter corpo dourado em TODA
               a área rolável, e não apenas nos cartões de estágio. */
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] {
                border-color:rgba(223,174,85,.91) !important;
                background:linear-gradient(140deg,#393127,#2d2d2b 51%,#302b25) !important;
                box-shadow:0 24px 62px rgba(0,0,0,.35),
                    inset 0 0 0 1px rgba(252,219,155,.09) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-topbar {
                border-bottom-color:rgba(208,158,76,.52) !important;
                background:linear-gradient(104deg,#4a3b2b 0%,#35332e 55%,#34312b 100%) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-detail-scroll {
                background:
                    radial-gradient(ellipse 90% 44% at 75% 5%,rgba(203,152,63,.14),transparent 80%),
                    radial-gradient(ellipse 72% 51% at 4% 85%,rgba(182,126,46,.09),transparent 85%),
                    linear-gradient(135deg,#302e2b 0%,#2c2d2d 52%,#322e28 100%) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v94-detail-art {
                border-color:rgba(226,174,85,.80) !important;
                background:radial-gradient(ellipse 80% 74% at 50% 44%,#534633 0%,#38352d 62%,#2d3030 100%) !important;
                box-shadow:0 0 0 3px rgba(211,159,72,.07),
                    inset 0 1px 0 rgba(255,227,164,.13) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v17-item-title {
                color:#fbe9c8 !important;
                text-shadow:0 0 13px rgba(217,159,75,.09);
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v50-stage-eyebrow,.inventory-v50-stage-feature-head span) {
                color:#e7bd72 !important;
            }
            /* Três materiais distintos: painel, cabeçalho e habilidades.
               Evita o bloco uniforme e acinzentado da versão anterior. */
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-panel,
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v11-ability-card {
                border-color:rgba(209,161,82,.57) !important;
                background:linear-gradient(126deg,#40362a 0%,#37312a 54%,#403526 100%) !important;
                box-shadow:inset 0 1px 0 rgba(246,218,157,.08) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-header,
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v11-ability-header {
                border-bottom-color:rgba(213,164,82,.40) !important;
                background:linear-gradient(110deg,#655035 0%,#4f4434 48%,#453b30 100%) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal.inventory-v50-detail-vestige[data-nature="wonderful"][data-rarity="artifact"] .inventory-v50-stage-feature,
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"] .inventory-v11-ability-body {
                border-color:rgba(209,166,94,.46) !important;
                background:linear-gradient(112deg,#463b2d 0%,#3c352d 46%,#3d352c 100%) !important;
                box-shadow:inset 0 1px 0 rgba(254,227,172,.08) !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v50-stage-header h3,.inventory-v50-stage-feature-head strong,.inventory-v11-ability-header h3) {
                color:#f8e9cb !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            :is(.inventory-v50-stage-feature p,.inventory-v50-stage-lore,.inventory-v11-ability-body) {
                color:#e8dfd2 !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            .inventory-v50-stage-status {
                border-color:rgba(218,177,110,.52) !important;
                background:rgba(72,57,35,.65) !important;
                color:#e9d3ab !important;
            }
            .character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35-detail-modal[data-nature="wonderful"][data-rarity="artifact"]
            .inventory-v50-stage-status.is-current {
                border-color:rgba(238,189,101,.76) !important;
                background:linear-gradient(120deg,#6c4e27,#493927) !important;
                color:#fff0cc !important;
            }

            /* ===============================================================
               SPELLBOUND V66.2.39 — IDENTIDADE DO INVENTÁRIO
               Gavetas, sintonização e bolsa compacta; cards de raridade preservados.
               =============================================================== */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-overview-wallet {
                display:flex;flex:0 0 auto;min-width:0;position:relative;margin-left:auto;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-overview-wallet .inventory-v38-wallet-trigger {
                border-radius:12px !important;min-height:52px;
                background:linear-gradient(120deg,var(--theme-band-background),var(--theme-surface-raised)) !important;
                border-color:color-mix(in srgb,var(--theme-accent-strong) 22%,var(--theme-border)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-overview-wallet .inventory-v38-wallet-panel {
                z-index:80;max-width:calc(100vw - 28px);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-attune-actions {
                display:flex;align-items:center;gap:8px;flex-wrap:wrap;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-settings-button {
                display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:36px;
                padding:0 11px;border:1px solid var(--theme-border) !important;
                border-radius:10px;color:var(--theme-text-secondary) !important;
                background:var(--theme-surface-raised) !important;font-size:10px;font-weight:800;
                transition:border-color .2s,transform .2s;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-settings-button:hover {
                border-color:var(--theme-border-strong) !important;transform:translateY(-1px);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-settings-button svg {width:15px;height:15px;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-attune-config {
                display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
                margin:0 0 11px;padding:12px 14px;border:1px solid var(--theme-border);
                border-radius:12px;background:var(--theme-surface-raised);color:var(--theme-text-strong);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-copy {
                display:flex;flex-direction:column;gap:3px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-copy strong {font-size:12px;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-copy small {
                font-size:10px;color:var(--theme-text-secondary);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-stepper {
                display:flex;align-items:center;gap:6px;border:1px solid var(--theme-border);
                border-radius:10px;background:var(--theme-surface-card);padding:3px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-stepper button {
                width:30px;height:29px;border-radius:7px;color:var(--theme-text-strong);
                background:var(--theme-surface-raised);font-size:17px;font-weight:700;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-stepper button:hover:not(:disabled) {
                background:var(--theme-card-hover);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-stepper button:disabled {opacity:.3;cursor:not-allowed;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-config-stepper strong {
                min-width:28px;text-align:center;font-size:13px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-shell {
                border-radius:17px !important;padding:12px !important;
                box-shadow:0 5px 16px rgba(0,0,0,.035);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-head {
                background:linear-gradient(110deg,var(--theme-band-background),var(--theme-surface-card)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slots {
                display:grid !important;grid-template-columns:repeat(3,minmax(0,1fr));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot {
                position:relative;isolation:isolate;overflow:hidden;
                transition:border-color .22s,box-shadow .22s,transform .22s;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot:hover {
                transform:translateY(-2px);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="legendary"] {
                border-color:color-mix(in srgb,#b69559 48%,var(--theme-border)) !important;
                background:linear-gradient(118deg,var(--theme-surface-card),color-mix(in srgb,#d6b675 13%,var(--theme-surface-card))) !important;
                box-shadow:inset 0 1px 0 color-mix(in srgb,#ffdf9f 14%,transparent),0 3px 10px rgba(0,0,0,.035);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="legendary"] .inventory-v36-slot-rarity {
                color:color-mix(in srgb,#a87e3f 76%,var(--theme-text-strong));
                border-color:color-mix(in srgb,#b69559 55%,var(--theme-border));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="artifact"] {
                border-color:#c79a4d !important;
                background:linear-gradient(116deg,color-mix(in srgb,#efcd85 19%,var(--theme-surface-card)),
                    var(--theme-surface-card) 55%,color-mix(in srgb,#f2d59b 16%,var(--theme-surface-card))) !important;
                box-shadow:0 3px 13px rgba(157,104,31,.11),inset 0 0 0 1px rgba(216,174,89,.13);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="artifact"]::before {
                content:'';position:absolute;inset:0;z-index:0;pointer-events:none;
                background:linear-gradient(116deg,transparent 22%,rgba(235,184,84,.17) 47%,transparent 69%);
                background-size:220% 100%;animation:inventory-v238-attune-sheen 9s ease-in-out infinite;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="artifact"] > * {
                position:relative;z-index:1;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="artifact"] .inventory-v36-slot-rarity {
                border-color:rgba(204,160,75,.52) !important;color:color-mix(in srgb,#ad7a2b 80%,var(--theme-text-strong)) !important;
                background:color-mix(in srgb,#f4d595 23%,var(--theme-surface-card));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-nature="technological"][data-rarity="artifact"] {
                border-color:#6ba8c0 !important;
                background:linear-gradient(120deg,color-mix(in srgb,#8dcce0 18%,var(--theme-surface-card)),
                    var(--theme-surface-card) 56%,color-mix(in srgb,#91d8ea 13%,var(--theme-surface-card))) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-nature="technological"][data-rarity="artifact"]::before {
                background:linear-gradient(105deg,transparent 20%,rgba(99,203,235,.12) 48%,transparent 68%);background-size:220% 100%;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-nature="mundane"][data-rarity="artifact"] {
                border-color:#a0aebd !important;
                background:linear-gradient(118deg,color-mix(in srgb,#ced9e0 18%,var(--theme-surface-card)),var(--theme-surface-card));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-slot-crest {
                position:absolute;right:2px;top:2px;display:grid;place-items:center;
                width:17px;height:17px;color:#e3b35d;font-size:13px;
                text-shadow:0 0 7px rgba(238,182,91,.5);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-group-copy {
                display:flex;align-items:center;gap:13px;min-width:0;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-group-title {min-width:0;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-nature-icon {
                display:grid;place-items:center;flex:none;width:38px;height:38px;
                border:1px solid color-mix(in srgb,var(--theme-accent-strong) 24%,var(--theme-border));
                border-radius:12px;color:var(--theme-accent-strong);
                background:var(--theme-surface-raised);box-shadow:inset 0 1px 0 rgba(255,255,255,.06);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-nature-icon svg {width:20px;height:20px;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-nature-icon[data-nature="wonderful"] {
                color:#bd8c46;border-color:color-mix(in srgb,#bd8c46 42%,var(--theme-border));
                background:color-mix(in srgb,#dfc48b 13%,var(--theme-surface-raised));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-nature-icon[data-nature="technological"] {
                color:#4f9bb5;border-color:color-mix(in srgb,#6cabc2 42%,var(--theme-border));
                background:color-mix(in srgb,#90cfe5 11%,var(--theme-surface-raised));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-nature-icon[data-nature="mundane"] {
                color:var(--theme-text-secondary);border-color:var(--theme-border);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v24-group-trigger {
                min-height:68px !important;padding:12px 15px !important;
                background:linear-gradient(110deg,var(--theme-surface-card-alt),var(--theme-module-background)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-rich-description :is(strong,b) {
                font-weight:800;color:inherit;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-rich-description :is(em,i) {font-style:italic;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-rich-description a,
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text a {
                color:var(--theme-accent-strong);text-decoration:underline;text-underline-offset:2px;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text p {margin:0 0 .6em;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text p:last-child {margin-bottom:0;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text :is(strong,b) {font-weight:800;color:inherit;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text :is(h2,h3,h4) {
                margin:.8em 0 .3em;font-family:Georgia,serif;font-weight:800;color:inherit;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text :is(h2,h3) {font-size:1.22em;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text h4 {font-size:1.1em;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text ul {list-style:disc;padding-left:1.6em;margin:.55em 0;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text ol {list-style:decimal;padding-left:1.6em;margin:.55em 0;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text li {margin:.18em 0;}
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text blockquote {
                border-left:2px solid color-mix(in srgb,var(--theme-accent-strong) 55%,var(--theme-border));
                padding-left:12px;margin:.8em 0;font-style:italic;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text hr {
                border:0;border-top:1px solid var(--theme-divider);margin:.9em 0;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text :is(pre,code) {
                background:var(--theme-surface-card-alt);border-radius:4px;padding:1px 4px;
            }
            .character-sheet-theme[data-theme] .inventory-v35-detail-modal .inventory-v238-rich-text pre {padding:8px;white-space:pre-wrap;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-rich-description .inventory-v92-description-copy {
                display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3;line-clamp:3;
                overflow:hidden;line-height:14px;max-height:42px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-rich-description :is(p,ul,ol,li,h2,h3,h4) {
                display:inline;margin:0;padding:0;list-style:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-rich-description a {pointer-events:none;}
            @keyframes inventory-v238-attune-sheen {
                0%,17% {background-position:130% 0;opacity:.15;}
                48% {background-position:45% 0;opacity:.8;}
                82%,100% {background-position:-65% 0;opacity:.1;}
            }
            /* Temas escuros: ouro quente, jamais trocar pelo destaque azul padrão. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v36-attunement-slot[data-rarity="legendary"] {
                background:linear-gradient(115deg,#292d30,#332f28) !important;
                border-color:#74644a !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v36-attunement-slot[data-nature="wonderful"][data-rarity="artifact"] {
                background:linear-gradient(120deg,#353027,#282c2f 56%,#3c3124) !important;
                border-color:#bd8b37 !important;
                box-shadow:0 0 0 1px rgba(222,176,80,.1),inset 0 0 25px rgba(196,139,47,.065);
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v36-attunement-slot[data-rarity="legendary"] .inventory-v36-slot-copy strong,
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v36-attunement-slot[data-rarity="artifact"] .inventory-v36-slot-copy strong {
                color:#f0e3ce !important;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v238-nature-icon[data-nature="wonderful"] {color:#e0b96c;}
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
            .inventory-v35 .inventory-v238-nature-icon[data-nature="technological"] {color:#8cc9e2;}
            /* A modal de equipados continua fiel ao tema com raridades distintas. */
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-row {
                border-radius:10px;border:1px solid var(--theme-border) !important;
                background:var(--theme-surface-card) !important;margin:0 0 7px;
                box-shadow:0 2px 7px rgba(0,0,0,.035);
            }
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-row[data-rarity="legendary"] {
                border-color:color-mix(in srgb,#b79a5b 50%,var(--theme-border)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-row[data-rarity="artifact"] {
                border-color:color-mix(in srgb,#cb9f47 70%,var(--theme-border)) !important;
                background:linear-gradient(110deg,var(--theme-surface-card),color-mix(in srgb,#cb9f47 10%,var(--theme-surface-card))) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-name {
                display:flex;align-items:center;gap:5px;min-width:0;
            }
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-name > strong {
                overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;
            }
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-nature {
                width:18px;height:18px;display:grid;place-items:center;flex:none;
                border-radius:5px;color:var(--theme-accent-strong);
            }
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-nature[data-nature="wonderful"] {color:#bb914a;}
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-nature[data-nature="technological"] {color:#5b9fbb;}
            .character-sheet-theme[data-theme] .inventory-v29-equipped-modal .inventory-v238-equipped-rarity {
                margin-left:auto;flex:none;padding:2px 5px;border-radius:999px;
                border:1px solid color-mix(in srgb,#bb914a 40%,var(--theme-border));
                color:color-mix(in srgb,#a5834d 78%,var(--theme-text-strong));
                font-size:8px;font-weight:800;
            }
            @media(max-width:670px){
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-overview-wallet {margin-left:0;width:100%;}
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-overview-wallet .inventory-v38-wallet-wrap {width:100%;}
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slots {grid-template-columns:1fr !important;}
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-attune-actions {width:100%;}
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-group-copy {gap:9px;}
            }
            @media(prefers-reduced-motion:reduce){
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="artifact"]::before {animation:none !important;opacity:.25;}
            }

            /* Spellbound V66.2.39 — Barra compacta e sintonia sem excesso visual. */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v38-toolbar {
                display:flex;align-items:center;justify-content:space-between;
                gap:12px;flex-wrap:wrap;overflow:visible;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-toolbar-wallet {
                display:flex;position:relative;min-width:0;margin-left:auto;z-index:25;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-toolbar-wallet .inventory-v38-wallet-trigger {
                min-height:43px !important;border-radius:11px !important;
                background:linear-gradient(110deg,var(--theme-band-background),var(--theme-surface-raised)) !important;
                border-color:color-mix(in srgb,var(--theme-accent-strong) 20%,var(--theme-border)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-toolbar-wallet .inventory-v38-wallet-panel {
                z-index:90;max-width:calc(100vw - 26px);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button {
                display:inline-flex;align-items:center;gap:9px;
                padding:3px 1px;border:0;border-radius:5px;background:transparent;
                font:inherit;color:var(--theme-text-strong) !important;
                cursor:pointer;text-align:left;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button > span {
                font:800 21px/1.2 Georgia,'Times New Roman',serif;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button svg {
                width:16px;height:16px;flex:none;opacity:.48;
                color:var(--theme-text-secondary);transition:transform .18s,opacity .18s,color .18s;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:hover svg,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button[aria-expanded="true"] svg,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:focus-visible svg {
                opacity:1;transform:rotate(30deg);color:var(--theme-accent-strong);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:focus-visible {
                outline:2px solid var(--theme-accent-strong);outline-offset:3px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v238-nature-icon .inventory-v239-technological-icon {
                display:block;width:22px;height:22px;object-fit:contain;
                filter:drop-shadow(0 1px 2px rgba(25,85,118,.22));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-tools {display:block !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v26-group-tools .inventory-v26-group-search {
                width:100%;max-width:none;flex:1 1 auto;min-width:0;
            }
            @media(max-width:670px){
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v38-toolbar {
                    flex-direction:column;align-items:stretch;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-toolbar-wallet {margin:0;width:100%;}
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-toolbar-wallet .inventory-v38-wallet-wrap,
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-toolbar-wallet .inventory-v38-wallet-trigger {width:100%;}
            }

            /* ===============================================================
               SPELLBOUND V66.2.40 — SINTONIZAÇÃO
               Miniaturas mais legíveis; engrenagem centralizada; modal temático.
               =============================================================== */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button {
                gap:10px;align-items:center;vertical-align:middle;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v240-gear {
                display:inline-grid;place-items:center;flex:none;width:28px;height:28px;
                border:1px solid color-mix(in srgb,var(--theme-border) 78%,var(--theme-accent-strong));
                border-radius:9px;background:var(--theme-surface-raised);
                color:var(--theme-text-secondary);transition:background .18s,border-color .18s,color .18s;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v240-gear svg {
                display:block;width:17px;height:17px;opacity:1 !important;transform:none !important;
                stroke:currentColor;overflow:visible;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:hover .inventory-v240-gear,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:focus-visible .inventory-v240-gear {
                color:var(--theme-accent-strong);border-color:var(--theme-border-strong);
                background:var(--theme-card-hover);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot {
                min-height:108px;padding:12px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-content {
                grid-template-columns:68px minmax(0,1fr);gap:13px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-image {
                display:flex;align-items:center;justify-content:center;overflow:hidden;
                width:68px !important;height:68px !important;flex:0 0 68px;
                border-radius:13px !important;
                border:1px solid var(--theme-border-soft) !important;
                background:radial-gradient(circle at 46% 43%,var(--theme-surface-raised),var(--theme-surface-inset)) !important;
                box-shadow:0 3px 12px rgba(0,0,0,.07),inset 0 1px 0 rgba(255,255,255,.09);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-image img {
                display:block;width:100%;height:100%;object-fit:contain;object-position:center;
                padding:3px;filter:drop-shadow(0 2px 3px rgba(0,0,0,.12));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="legendary"] .inventory-v36-slot-image {
                border-color:color-mix(in srgb,#b69763 55%,var(--theme-border)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot[data-rarity="artifact"] .inventory-v36-slot-image {
                border-color:color-mix(in srgb,#c79a4d 72%,var(--theme-border)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-copy {
                min-height:70px;grid-template-rows:auto minmax(34px,1fr) auto;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v240-no-slots {
                text-align:center;border:1px dashed var(--theme-border);border-radius:12px;
                padding:16px 12px;color:var(--theme-text-secondary);font-size:12px;
                background:var(--theme-surface-card);
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v240-no-slots strong {color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-extra-attuned {
                display:flex;flex-wrap:wrap;align-items:center;gap:8px;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-extra-attuned > small {
                color:var(--theme-text-secondary);font-size:10px;
            }

            /* Teleport: seu próprio nó recebe data-theme para herdar a paleta. */
            .character-sheet-theme[data-theme].inventory-v240-modal-layer {
                position:fixed;inset:0;z-index:290;display:flex;align-items:center;
                justify-content:center;padding:20px;isolation:isolate;
            }
            .character-sheet-theme[data-theme] .inventory-v240-backdrop {
                position:absolute;inset:0;background:rgba(10,12,15,.74);
                backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal {
                width:min(100%,490px);max-height:min(92vh,700px);overflow-y:auto;
                position:relative;z-index:1;padding:0 !important;
                border:1px solid var(--theme-border-strong) !important;
                border-radius:18px !important;
                background:var(--theme-surface-card) !important;
                color:var(--theme-text-strong) !important;
                box-shadow:0 24px 65px rgba(0,0,0,.32),0 0 0 1px rgba(255,255,255,.035);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-header {
                display:flex;align-items:center;gap:13px;padding:19px 20px;
                border-bottom:1px solid var(--theme-border);
                background:linear-gradient(125deg,var(--theme-band-background),var(--theme-surface-card));
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-symbol {
                flex:none;width:44px;height:44px;border-radius:12px;
                display:grid;place-items:center;font-size:22px;
                color:var(--theme-accent-strong);background:var(--theme-surface-raised);
                border:1px solid var(--theme-border);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-heading {flex:1;min-width:0;}
            .character-sheet-theme[data-theme] .inventory-v240-modal-heading small {
                display:block;margin-bottom:3px;color:var(--theme-label);font-size:9px;
                font-weight:800;letter-spacing:.12em;
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-heading h3 {
                margin:0;color:var(--theme-text-strong);font:800 21px/1.2 Georgia,'Times New Roman',serif;
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-close {
                border:1px solid var(--theme-border);border-radius:9px;
                background:var(--theme-surface-raised);color:var(--theme-text-secondary);
                display:grid;place-items:center;flex:none;width:32px;height:32px;
                font-size:22px;line-height:1;cursor:pointer;
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-close:hover {background:var(--theme-card-hover);color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .inventory-v240-modal-content {padding:20px 23px 18px;}
            .character-sheet-theme[data-theme] .inventory-v240-modal-intro {
                font-size:12px;line-height:1.65;color:var(--theme-text-secondary);margin:0 0 20px;
            }
            .character-sheet-theme[data-theme] .inventory-v240-space-control {
                display:flex;align-items:stretch;justify-content:center;gap:10px;margin:0 auto 19px;
            }
            .character-sheet-theme[data-theme] .inventory-v240-space-control > button {
                flex:none;width:52px;height:68px;border:1px solid var(--theme-border-strong);
                border-radius:12px;background:var(--theme-surface-raised);
                color:var(--theme-text-strong);font-size:26px;font-weight:500;
                transition:background .15s,transform .15s;
            }
            .character-sheet-theme[data-theme] .inventory-v240-space-control > button:hover:not(:disabled) {
                background:var(--theme-card-hover);transform:translateY(-1px);
            }
            .character-sheet-theme[data-theme] .inventory-v240-space-control > button:disabled {opacity:.25;cursor:not-allowed;}
            .character-sheet-theme[data-theme] .inventory-v240-current-limit {
                min-width:140px;max-width:190px;flex:1;display:flex;flex-direction:column;
                justify-content:center;align-items:center;gap:1px;
                border-radius:12px;border:1px solid var(--theme-border);
                background:linear-gradient(120deg,var(--theme-surface-raised),var(--theme-surface-card-alt));
            }
            .character-sheet-theme[data-theme] .inventory-v240-current-limit strong {
                font:800 31px/1 Georgia,'Times New Roman',serif;color:var(--theme-accent-strong);
                font-variant-numeric:tabular-nums;
            }
            .character-sheet-theme[data-theme] .inventory-v240-current-limit span {
                font-size:10px;font-weight:700;color:var(--theme-text-secondary);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-stats {
                display:flex;justify-content:center;align-items:center;gap:5px;flex-wrap:wrap;
                margin:0;font-size:11px;color:var(--theme-text-secondary);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-stats strong {color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .inventory-v240-overflow-warning {
                display:flex;flex-direction:column;gap:5px;border:1px solid color-mix(in srgb,#be9051 48%,var(--theme-border));
                background:color-mix(in srgb,#b9914f 11%,var(--theme-surface-card));
                padding:11px 13px;margin:18px 0 2px;border-radius:11px;
            }
            .character-sheet-theme[data-theme] .inventory-v240-overflow-warning strong {
                color:var(--theme-text-strong);font-size:11px;
            }
            .character-sheet-theme[data-theme] .inventory-v240-overflow-warning span {
                color:var(--theme-text-secondary);font-size:11px;line-height:1.55;
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-note {
                padding:0;margin:16px 0 0;font-size:10px;line-height:1.5;color:var(--theme-text-secondary);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-footer {
                display:flex;align-items:center;justify-content:space-between;gap:10px;
                padding:13px 20px;border-top:1px solid var(--theme-border);
                background:var(--theme-surface-raised);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-footer button {
                min-height:38px;border-radius:9px;padding:0 15px;font-size:11px;font-weight:800;
            }
            .character-sheet-theme[data-theme] .inventory-v240-restore {
                border:1px solid var(--theme-border);background:var(--theme-surface-card);
                color:var(--theme-text-secondary);
            }
            .character-sheet-theme[data-theme] .inventory-v240-done {
                border:1px solid var(--theme-accent-strong);
                background:var(--theme-accent-strong);color:var(--theme-on-accent,#fff);
            }
            .character-sheet-theme[data-theme] .inventory-v240-modal-footer button:hover {filter:brightness(1.07);}
            .character-sheet-theme[data-theme] .inventory-v240-modal-layer button:focus-visible {
                outline:2px solid var(--theme-accent-strong);outline-offset:3px;
            }
            @media(max-width:600px) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot {min-height:103px;}
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-image {
                    width:62px !important;height:62px !important;flex-basis:62px;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-content {grid-template-columns:62px minmax(0,1fr);}
                .character-sheet-theme[data-theme] .inventory-v240-modal {width:min(100%,480px);}
                .character-sheet-theme[data-theme] .inventory-v240-modal-header {padding:15px;}
                .character-sheet-theme[data-theme] .inventory-v240-modal-content {padding:17px;}
                .character-sheet-theme[data-theme] .inventory-v240-modal-heading h3 {font-size:18px;}
            }
            /* ===============================================================
               SPELLBOUND V66.2.41 — SINTONIZAÇÃO COMPACTA
               Cartões mais limpos, raridade integrada e modal minimalista.
               Mantém o limite 0–12 e os vínculos existentes sem alterações.
               =============================================================== */
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-shell {
                padding:13px !important;border-radius:17px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-head {
                min-height:47px !important;padding:2px 3px 12px !important;margin:0 0 10px !important;
                border:0 !important;border-radius:0 !important;background:transparent !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-title {gap:10px !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-icon {
                flex:0 0 30px;width:30px;height:30px;border-radius:10px;
                font-size:12px;border-color:color-mix(in srgb,var(--theme-accent-strong) 24%,var(--theme-border)) !important;
                background:color-mix(in srgb,var(--theme-accent-strong) 7%,var(--theme-surface-raised)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-copy p {display:none !important;}
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button {
                gap:7px !important;display:inline-flex !important;align-items:center !important;
                font:800 20px/1.15 Georgia,'Times New Roman',serif !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v240-gear {
                width:19px !important;height:19px !important;display:inline-flex !important;
                justify-content:center !important;align-items:center !important;
                border:0 !important;border-radius:50% !important;padding:0 !important;
                background:transparent !important;color:var(--theme-text-secondary) !important;
                opacity:.66;transition:opacity .18s,color .18s !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v240-gear svg {
                width:15px !important;height:15px !important;display:block !important;
                flex:none !important;opacity:1 !important;transform:none !important;
                stroke:currentColor !important;stroke-width:1.8;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:hover .inventory-v240-gear,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:focus-visible .inventory-v240-gear {
                background:transparent !important;border:0 !important;
                color:var(--theme-accent-strong) !important;opacity:1 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:hover .inventory-v240-gear svg,
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v239-attune-title-button:focus-visible .inventory-v240-gear svg {
                transform:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slots {
                gap:10px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot {
                min-height:92px !important;padding:11px !important;border-radius:13px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-content {
                grid-template-columns:62px minmax(0,1fr) !important;gap:12px !important;
                align-items:center;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-image {
                width:62px !important;height:62px !important;flex:0 0 62px !important;
                border-radius:11px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-copy {
                display:flex !important;flex-direction:column !important;
                align-items:flex-start !important;justify-content:center !important;
                gap:5px !important;height:auto !important;min-height:62px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-meta {
                margin:0 !important;display:flex;justify-content:flex-start !important;gap:0 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-copy > strong {
                min-height:0 !important;font-size:14px !important;line-height:1.18 !important;
                margin:0 !important;color:var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v241-slot-kind {
                display:inline-flex;align-items:center;gap:4px;max-width:100%;
                font-size:10px;line-height:1.1;font-weight:650;
                color:var(--theme-text-secondary);text-align:left;
                overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v241-slot-kind[data-rarity="legendary"] {
                color:color-mix(in srgb,#c6a06b 75%,var(--theme-text-strong));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v241-slot-kind[data-rarity="artifact"] {
                color:color-mix(in srgb,#d7ab56 82%,var(--theme-text-strong));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v241-slot-kind[data-nature="technological"][data-rarity="artifact"] {
                color:color-mix(in srgb,#7bbdd2 85%,var(--theme-text-strong));
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v241-kind-spark {
                color:inherit;font-size:10px;flex:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-empty {
                min-height:65px !important;gap:9px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-empty .inventory-v14-slot-plus {
                width:33px;height:33px;flex-basis:33px;border-style:dashed !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-empty-copy small {
                font-size:10px;line-height:1.2;
            }
            .character-sheet-theme[data-theme] .inventory-v35 .inventory-v240-no-slots {
                padding:13px !important;
            }
            /* Modal: uma única decisão, sem painéis de explicação repetidos. */
            .character-sheet-theme[data-theme] .inventory-v241-modal {
                width:min(100%,400px) !important;border-radius:16px !important;
                box-shadow:0 22px 60px rgba(0,0,0,.36) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-header {
                gap:10px;padding:17px 20px 13px !important;
                background:linear-gradient(110deg,var(--theme-surface-card-alt),var(--theme-surface-card)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-heading h3 {
                font-size:20px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-heading small {
                display:block;margin:5px 0 0 !important;font-size:10px !important;
                letter-spacing:.03em !important;font-weight:600 !important;
                color:var(--theme-text-secondary) !important;text-transform:none !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-close {
                height:29px;width:29px;border-radius:9px;font-size:initial;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-close svg {height:15px;width:15px;}
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-content {
                padding:23px 23px 19px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-space-control {
                gap:10px;margin:0 auto 14px !important;max-width:280px;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-space-control > button {
                width:48px !important;height:61px !important;border-radius:11px !important;font-size:23px;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-current-limit {
                min-width:112px;max-width:160px;gap:2px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-current-limit strong {
                font-size:34px !important;line-height:1 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-current-limit span {
                font-size:10px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-stats {
                font-size:10px !important;gap:6px;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-overflow-warning {
                margin:14px 0 0 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-footer {
                padding:12px 20px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-restore {
                background:transparent !important;border:0 !important;padding:0 6px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-done {
                min-width:90px;
            }
            @media (max-width:600px) {
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-attunement-slot {min-height:90px !important;}
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-content {
                    grid-template-columns:56px minmax(0,1fr) !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-image {
                    width:56px !important;height:56px !important;flex-basis:56px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v35 .inventory-v36-slot-copy {min-height:56px !important;}
                .character-sheet-theme[data-theme] .inventory-v241-modal .inventory-v240-modal-content {padding:20px 15px !important;}
            }

            /* ================================================================
               SPELLBOUND V66.2.42 — ITENS ENCONTRADOS
               Aparência por natureza + raridade, sem transformar Artefatos
               Tecnológicos em ouro nem sobrescrever a paleta da ficha.
               Mantém a ação original de resgatar uma unidade por clique.
               ================================================================ */
            .character-sheet-theme[data-theme] .inventory-v35-found-modal {
                width:min(700px,calc(100vw - 28px)) !important;
                max-width:700px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal > div:first-child {
                padding:15px 19px !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-refresh,
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-close {
                display:inline-flex;align-items:center;justify-content:center;gap:6px;
                height:32px;border:1px solid var(--theme-border) !important;
                border-radius:9px;background:var(--theme-surface-card) !important;
                color:var(--theme-text-secondary) !important;
                font-size:10px;font-weight:750;letter-spacing:.02em;
                transition:background .16s ease,color .16s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-refresh {padding:0 11px;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-close {width:32px;padding:0;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-refresh svg {width:13px;height:13px;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-close svg {width:15px;height:15px;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-refresh:hover:not(:disabled),
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-close:hover {background:var(--theme-card-hover) !important;color:var(--theme-text-strong) !important;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-refresh:disabled {opacity:.6;cursor:wait;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-header-title {
                display:flex;align-items:center;gap:10px;min-width:0;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-header-title h3 {
                margin:0;font:800 21px/1.15 Georgia,serif;
                color:var(--theme-text-strong) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-total {
                display:inline-flex;min-width:23px;height:23px;justify-content:center;align-items:center;
                border:1px solid var(--theme-border);border-radius:99px;
                padding:0 7px;background:var(--theme-surface-card);
                font-size:11px;font-weight:800;color:var(--theme-text-primary);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-subtitle {
                margin:4px 0 0;font-size:10px;font-weight:500;
                color:var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-section-title {
                display:flex;justify-content:space-between;align-items:center;
                gap:10px;margin:0 0 10px;
                font-size:10px;font-weight:850;letter-spacing:.075em;text-transform:uppercase;
                color:var(--theme-label);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-section-title span {
                border:1px solid var(--theme-border);border-radius:99px;
                background:var(--theme-surface-card-alt);
                min-width:24px;text-align:center;padding:3px 8px;font-size:10px;
                letter-spacing:0;color:var(--theme-text-secondary);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-list {
                display:grid;gap:12px;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v50-found-item.inventory-v242-found-card {
                --found-edge:var(--theme-border);
                --found-tint:rgba(145,122,103,.035);
                --found-tint-secondary:rgba(145,122,103,.045);
                --found-ink:var(--theme-text-strong);
                --found-meta:var(--theme-text-secondary);
                --found-photo:var(--theme-surface-inset);
                --found-photo-edge:var(--theme-border);
                --found-action:var(--theme-button-background);
                --found-action-ink:var(--theme-button-text);
                --found-aura:rgba(136,123,108,.06);
                position:relative;isolation:isolate;overflow:hidden;
                border:1px solid var(--found-edge) !important;
                border-radius:16px !important;
                background:radial-gradient(ellipse 66% 130% at 0 0,var(--found-tint),transparent 84%),
                           linear-gradient(125deg,transparent 50%,var(--found-tint-secondary)),
                           var(--theme-surface-card) !important;
                box-shadow:0 9px 24px rgba(0,0,0,.06),inset 0 1px rgba(255,255,255,.08) !important;
                padding:0 !important;
                transition:border-color .2s ease,box-shadow .2s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="wonderful"] {
                --found-edge:rgba(182,149,99,.52);
                --found-tint:rgba(214,169,100,.085);
                --found-tint-secondary:rgba(226,181,112,.045);
                --found-photo-edge:rgba(182,149,99,.48);
                --found-aura:rgba(219,181,105,.20);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="wonderful"][data-rarity="legendary"] {
                --found-edge:rgba(184,146,85,.7);
                --found-tint:rgba(221,184,107,.13);
                --found-tint-secondary:rgba(227,187,108,.075);
                --found-ink:#84602f;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="wonderful"][data-rarity="artifact"] {
                --found-edge:rgba(211,161,70,.9);
                --found-tint:rgba(235,194,99,.22);
                --found-tint-secondary:rgba(228,180,93,.10);
                --found-ink:#8c6025;
                --found-aura:rgba(235,194,99,.33);
                --found-photo-edge:rgba(210,156,66,.78);
                --found-action:linear-gradient(120deg,#a9782c,#8b5d24);
                --found-action-ink:#fffaf0;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="technological"] {
                --found-edge:rgba(105,158,182,.50);
                --found-tint:rgba(112,187,208,.11);
                --found-tint-secondary:rgba(128,205,223,.075);
                --found-ink:#315f78;
                --found-photo-edge:rgba(111,179,200,.60);
                --found-aura:rgba(129,208,226,.29);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="technological"][data-rarity="legendary"] {
                --found-edge:rgba(94,162,187,.76);
                --found-tint:rgba(111,196,225,.16);
                --found-tint-secondary:rgba(128,199,224,.095);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="technological"][data-rarity="artifact"] {
                --found-edge:rgba(73,165,197,.90);
                --found-tint:rgba(88,190,222,.23);
                --found-tint-secondary:rgba(118,210,230,.13);
                --found-ink:#235d79;
                --found-aura:rgba(92,213,237,.39);
                --found-photo-edge:rgba(72,162,193,.84);
                --found-action:linear-gradient(120deg,#286d8a,#235776);
                --found-action-ink:#f4fdff;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="mundane"] {
                --found-edge:rgba(145,152,159,.54);
                --found-tint:rgba(165,175,184,.11);
                --found-tint-secondary:rgba(167,179,191,.085);
                --found-ink:#566371;
                --found-photo-edge:rgba(143,154,165,.65);
                --found-aura:rgba(177,188,200,.25);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="mundane"][data-rarity="legendary"] {
                --found-edge:rgba(139,158,175,.85);
                --found-tint:rgba(180,196,209,.20);
                --found-tint-secondary:rgba(180,192,201,.10);
                --found-ink:#4d5e70;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-card[data-nature="mundane"][data-rarity="artifact"] {
                --found-edge:rgba(139,165,188,.94);
                --found-tint:rgba(198,210,221,.27);
                --found-tint-secondary:rgba(188,204,218,.16);
                --found-ink:#445972;
                --found-photo-edge:rgba(148,170,192,.90);
                --found-action:linear-gradient(120deg,#566879,#3d5264);
                --found-action-ink:#fff;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="wonderful"][data-rarity="legendary"] {--found-ink:#e1c08d;}
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="wonderful"][data-rarity="artifact"] {
                --found-ink:#f0d49e;--found-tint:rgba(218,172,78,.16);
                --found-tint-secondary:rgba(224,173,81,.12);
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="technological"] {--found-ink:#a0d7ed;}
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="technological"][data-rarity="artifact"] {
                --found-tint:rgba(60,162,197,.19);
                --found-tint-secondary:rgba(55,154,190,.13);
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="mundane"] {--found-ink:#d5dce4;}
            /* Reforça nomes nas paletas escuras sem alterar o brilho das bordas. */
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="technological"][data-rarity="artifact"] {
                --found-ink:#b9eafa;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="mundane"][data-rarity="legendary"] {
                --found-ink:#dce4ee;
            }
            .character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"]) .inventory-v35-found-modal
            .inventory-v242-found-card[data-nature="mundane"][data-rarity="artifact"] {
                --found-ink:#e8f0f7;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-main {
                display:grid;grid-template-columns:104px minmax(0,1fr);
                gap:16px;align-items:start;padding:16px 17px 15px;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-thumb {
                position:relative;display:flex;align-items:center;justify-content:center;
                width:104px;height:104px;flex:none;overflow:hidden;
                border-radius:13px;border:1px solid var(--found-photo-edge);
                background:radial-gradient(ellipse at 50% 42%,var(--found-aura),transparent 86%),var(--found-photo) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.16),0 4px 12px rgba(0,0,0,.045);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-thumb img {
                display:block;width:100%;height:100%;object-fit:contain;padding:6px;
                filter:drop-shadow(0 3px 3px rgba(0,0,0,.15));
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-fallback {
                font-size:30px;color:var(--found-ink);opacity:.7;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-copy {min-width:0;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-topline {
                display:flex;align-items:flex-start;justify-content:space-between;gap:13px;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-heading {min-width:0;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-name {
                margin:0;font:800 20px/1.14 Georgia,serif;
                overflow-wrap:anywhere;color:var(--found-ink) !important;
                letter-spacing:-.015em;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-classification {
                margin:5px 0 0;font-size:10px;line-height:1.55;
                font-style:italic;font-weight:650;color:var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-quantity {
                display:inline-flex;align-items:center;gap:7px;white-space:nowrap;
                border:1px solid var(--found-edge);border-radius:99px;
                background:var(--theme-surface-card-alt);
                padding:4px 5px 4px 10px;
                font-size:9px;font-weight:750;color:var(--theme-text-secondary);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-quantity strong {
                display:inline-flex;align-items:center;justify-content:center;
                min-width:21px;height:21px;border-radius:99px;
                background:var(--theme-surface-inset);
                font-size:11px;color:var(--found-ink) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-description {
                margin-top:10px;max-height:4.65em;overflow:hidden;
                font-size:11px;line-height:1.55;color:var(--theme-text-primary) !important;
                overflow-wrap:break-word;
                -webkit-mask-image:linear-gradient(#000 0%,#000 82%,transparent 100%);
                mask-image:linear-gradient(#000 0%,#000 82%,transparent 100%);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-description :is(p,ul,ol,blockquote) {
                margin:0 0 3px;color:inherit;font-size:inherit;line-height:inherit;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-description strong {color:inherit;font-weight:800;}
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-footer {
                position:relative;display:flex;align-items:center;justify-content:space-between;
                flex-wrap:wrap;gap:9px 14px;
                border-top:1px solid var(--found-edge);
                padding:10px 13px 11px 17px;
                background:linear-gradient(108deg,var(--found-tint),transparent 80%);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-meta {
                min-width:0;flex:1;display:flex;flex-wrap:wrap;align-items:center;gap:7px 11px;
                font-size:9px;line-height:1.4;color:var(--theme-text-secondary) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-vestige {
                font-size:9px;font-weight:850;color:var(--found-ink);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-claim {
                display:inline-flex;align-items:center;justify-content:center;gap:9px;
                min-height:37px;flex:none;
                border:1px solid transparent !important;border-radius:9px;
                background:var(--found-action) !important;
                color:var(--found-action-ink) !important;
                padding:8px 12px 8px 14px;
                font-size:10px;font-weight:850;letter-spacing:.012em;
                line-height:1.1;transition:filter .17s ease,transform .17s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-claim svg {
                width:14px;height:14px;flex:none;
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-claim:hover:not(:disabled) {
                filter:brightness(1.10);transform:translateY(-1px);
            }
            .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-claim:disabled {opacity:.6;cursor:wait;}
            @media(max-width:540px) {
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-main {
                    grid-template-columns:75px minmax(0,1fr);gap:11px;padding:12px 12px 11px;
                }
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-thumb {
                    width:75px;height:82px;border-radius:10px;
                }
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-name {font-size:17px;}
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-topline {flex-wrap:wrap;gap:6px;}
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-quantity {font-size:9px;padding:3px 4px 3px 8px;}
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-description {max-height:4.6em;font-size:10px;}
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-footer {padding:9px 11px;}
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-claim {font-size:9px;min-height:34px;}
            }
            @media(prefers-reduced-motion:reduce) {
                .character-sheet-theme[data-theme] .inventory-v35-found-modal .inventory-v242-found-claim {transition:none !important;}
            }


            /* ===============================================================
               SPELLBOUND V66.2.111 — EQUIPADOS: LISTA COMPACTA
               Apenas foto, nome e raridade. Não altera o painel de itens,
               os temas ou a lógica de equipar e abrir detalhes.
               =============================================================== */
            .character-sheet-theme[data-theme] .inventory-v35-equipped-modal.inventory-v311-equipped-modal {
                display: flex !important;
                flex-direction: column;
                width: min(570px, calc(100vw - 28px)) !important;
                max-width: 570px !important;
                max-height: min(82dvh, 650px) !important;
                min-height: 0 !important;
                border: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
                border-radius: 16px !important;
                background: var(--theme-modal-surface, var(--theme-surface-card)) !important;
                box-shadow: var(--theme-modal-shadow, 0 22px 64px rgba(0,0,0,.25)) !important;
                overflow: hidden !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-header {
                display: flex !important;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 17px 19px !important;
                background: var(--theme-modal-header-background, var(--theme-band-background)) !important;
                border-bottom: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-title-row {
                display: flex !important;
                align-items: center;
                gap: 10px;
                min-width: 0;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-title-row h3 {
                margin: 0;
                font: 750 clamp(19px, 3.7vw, 23px)/1.2 Georgia, serif !important;
                color: var(--theme-modal-title, var(--theme-text-strong)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-count {
                display: inline-grid !important;
                place-items: center;
                flex: 0 0 auto;
                min-width: 24px;
                height: 24px;
                padding: 0 6px;
                border-radius: 99px;
                border: 1px solid var(--theme-border) !important;
                background: var(--theme-surface-card) !important;
                color: var(--theme-text-secondary) !important;
                font: 700 11px Arial, sans-serif !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-close {
                display: grid;
                place-items: center;
                flex: 0 0 32px;
                width: 32px;
                height: 32px;
                padding: 0;
                border: 1px solid var(--theme-modal-border, var(--theme-border)) !important;
                border-radius: 9px;
                background: transparent !important;
                color: var(--theme-text-secondary) !important;
                cursor: pointer;
                transition: background .15s ease, color .15s ease, border-color .15s ease;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-close svg {
                width: 16px; height: 16px;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-close:is(:hover,:focus-visible) {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
                border-color: var(--theme-border-strong) !important;
                outline: none;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-body {
                flex: 1 1 auto;
                min-height: 0;
                max-height: none !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding: 11px 13px 14px !important;
                background: var(--theme-modal-surface, var(--theme-surface-card)) !important;
                scrollbar-width: thin;
                scrollbar-color: var(--theme-border-strong) transparent;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-list {
                display: flex !important;
                flex-direction: column !important;
                gap: 5px;
                padding: 0 !important;
                margin: 0 !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-row {
                display: grid !important;
                grid-template-columns: 48px minmax(0,1fr) 16px !important;
                align-items: center !important;
                gap: 12px !important;
                width: 100% !important;
                height: auto !important;
                min-height: 66px !important;
                padding: 8px 11px !important;
                margin: 0 !important;
                border: 1px solid transparent !important;
                border-bottom-color: var(--theme-divider, var(--theme-border)) !important;
                border-radius: 9px !important;
                background: transparent !important;
                box-shadow: none !important;
                color: var(--theme-text-primary) !important;
                text-align: left !important;
                cursor: pointer;
                transition: background .15s ease, border-color .15s ease !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-row:last-child {
                border-bottom-color: transparent !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-row:is(:hover,:focus-visible) {
                background: var(--theme-card-hover) !important;
                border-color: var(--theme-border) !important;
                transform: none !important;
                outline: none;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-thumb {
                display: grid !important;
                place-items: center;
                flex: 0 0 auto;
                width: 48px !important;
                height: 48px !important;
                min-width: 48px !important;
                min-height: 48px !important;
                overflow: hidden;
                border: 1px solid var(--theme-border) !important;
                border-radius: 9px !important;
                background: var(--theme-surface-raised) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-thumb img {
                width: 100%; height: 100%; object-fit: contain; padding: 2px;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-fallback {
                display: grid !important; place-items: center; width: 100%; height: 100%;
                color: var(--theme-label) !important;
                font: 700 22px Georgia, serif !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-fallback span {
                display: inline; color: inherit !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-copy {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center;
                align-items: flex-start !important;
                min-width: 0;
                gap: 4px;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-name {
                display: block;
                max-width: 100%;
                margin: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: var(--theme-text-strong) !important;
                font: 700 15px/1.22 Georgia, serif !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-rarity {
                display: block !important;
                margin: 0 !important;
                color: var(--theme-text-secondary) !important;
                font: 600 11px/1.25 Arial, sans-serif !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-rarity:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                color: color-mix(in srgb, var(--theme-accent) 65%, var(--theme-text-secondary) 35%) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-chevron {
                display: grid;
                place-items: center;
                width: 16px !important;
                height: 20px !important;
                margin: 0 !important;
                color: var(--theme-label) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-chevron svg {
                width: 14px; height: 14px;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-row:hover .inventory-v311-equipped-chevron {
                color: var(--theme-accent-strong, var(--theme-accent)) !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-empty {
                min-height: 160px;
                padding: 35px 18px !important;
                color: var(--theme-text-secondary) !important;
                text-align: center;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-empty strong {
                color: var(--theme-text-strong) !important;
                font: 700 16px Georgia, serif !important;
            }
            .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-empty p {
                margin-top: 7px;
                color: var(--theme-text-secondary) !important;
                font: 500 12px/1.4 Arial, sans-serif !important;
            }
            @media (max-width: 480px) {
                .character-sheet-theme[data-theme] .inventory-v35-equipped-modal.inventory-v311-equipped-modal {
                    width: calc(100vw - 20px) !important;
                    max-height: 88dvh !important;
                }
                .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-header {
                    padding: 13px 15px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-body {
                    padding: 8px 9px 10px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-row {
                    grid-template-columns: 44px minmax(0,1fr) 14px !important;
                    min-height: 61px !important;
                    gap: 10px !important;
                    padding: 7px 8px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-thumb {
                    width: 44px !important; height: 44px !important;
                    min-width: 44px !important; min-height: 44px !important;
                }
                .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-name {
                    font-size: 14px !important;
                }
            }
            @media (prefers-reduced-motion: reduce) {
                .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-row,
                .character-sheet-theme[data-theme] .inventory-v311-equipped-modal .inventory-v311-equipped-close {
                    transition: none !important;
                }
            }
