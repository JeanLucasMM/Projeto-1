            [x-cloak] { display: none !important; }

            /*
             * Esta tela precisa ocupar a área de conteúdo desde o topo.
             * O layout base do app aplica wrappers/paddings verticais que
             * criavam uma faixa vazia acima do header do Cofre.
             *
             * Mantemos a sidebar intacta e zeramos somente os wrappers
             * ancestrais quando eles contêm esta página.
             */
            main:has(.master-vault-page),
            .py-12:has(.master-vault-page),
            .max-w-7xl:has(.master-vault-page),
            .sm\:px-6:has(.master-vault-page),
            .lg\:px-8:has(.master-vault-page) {
                max-width: 100% !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }

            .master-vault-page {
                --vault-ink: #53150f;
                --vault-accent: #6b1d14;
                --vault-muted: #8c6239;
                --vault-border: rgba(176,140,98,.35);
                --vault-border-soft: rgba(205,187,159,.58);
                --vault-paper: #f8f5ee;
                --vault-paper-strong: #fffdf8;
                --vault-band: #efe9dc;
                min-height: 100%;
                color: var(--vault-ink);
            }

            .master-vault-shell {
                width: min(1480px, calc(100% - 32px));
                margin: 0 auto;
                padding: 28px 0 42px;
            }

            .master-vault-hero {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 24px;
                border-bottom: 1px solid var(--vault-border);
                padding-bottom: 21px;
            }

            .master-vault-eyebrow,
            .master-vault-field > span {
                color: var(--vault-muted);
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .master-vault-title {
                margin: 4px 0 0;
                font-family: Georgia, serif;
                font-size: clamp(29px, 3vw, 42px);
                font-weight: 900;
                line-height: 1;
            }

            .master-vault-subtitle {
                max-width: 730px;
                margin: 10px 0 0;
                color: #765a46;
                font-size: 13px;
                line-height: 1.6;
            }

            .master-vault-primary,
            .master-vault-secondary,
            .master-vault-filter,
            .master-vault-tab {
                border: 0;
                cursor: pointer;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
            }

            .master-vault-primary {
                display: inline-flex;
                min-height: 43px;
                align-items: center;
                justify-content: center;
                gap: 7px;
                border-radius: 11px;
                background: var(--vault-accent);
                padding: 0 15px;
                color: #f8f3e9;
                transition: background .15s ease, transform .15s ease;
            }

            .master-vault-primary:hover {
                background: #53150f;
                transform: translateY(-1px);
            }

            .master-vault-secondary {
                min-height: 37px;
                border: 1px solid var(--vault-border-soft);
                border-radius: 9px;
                background: rgba(255,253,248,.76);
                padding: 0 11px;
                color: var(--vault-muted);
            }

            .master-vault-secondary:hover {
                border-color: rgba(107,29,20,.32);
                background: #fffdf8;
                color: var(--vault-accent);
            }

            .master-vault-stats {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 1px;
                overflow: hidden;
                margin-top: 18px;
                border: 1px solid var(--vault-border-soft);
                border-radius: 14px;
                background: var(--vault-border-soft);
            }

            .master-vault-stat {
                background: rgba(255,253,248,.92);
                padding: 12px 15px;
            }

            .master-vault-stat span {
                display: block;
                color: var(--vault-muted);
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .1em;
                text-transform: uppercase;
            }

            .master-vault-stat strong {
                display: block;
                margin-top: 2px;
                font-family: Georgia, serif;
                font-size: 22px;
            }

            .master-vault-toolbar {
                display: grid;
                grid-template-columns: minmax(240px, 1fr) auto;
                gap: 13px;
                margin-top: 19px;
            }

            .master-vault-search { position: relative; }

            .master-vault-search svg {
                position: absolute;
                left: 13px;
                top: 50%;
                width: 17px;
                height: 17px;
                transform: translateY(-50%);
                color: #947459;
                pointer-events: none;
            }

            .master-vault-search input,
            .master-vault-field input,
            .master-vault-field textarea,
            .master-vault-field select,
            .master-vault-repeat input,
            .master-vault-repeat textarea {
                width: 100%;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 10px;
                background: rgba(255,255,255,.76);
                color: var(--vault-ink);
                outline: none;
            }

            .master-vault-search input {
                height: 43px;
                padding: 0 14px 0 40px;
                font-size: 12px;
                font-weight: 700;
            }

            .master-vault-field input,
            .master-vault-field select,
            .master-vault-repeat input {
                min-height: 42px;
                padding: 8px 10px;
                font-size: 12px;
            }

            .master-vault-field textarea,
            .master-vault-repeat textarea {
                min-height: 100px;
                padding: 10px;
                font-size: 12px;
                line-height: 1.55;
                resize: vertical;
            }

            .master-vault-search input:focus,
            .master-vault-field input:focus,
            .master-vault-field textarea:focus,
            .master-vault-field select:focus,
            .master-vault-repeat input:focus,
            .master-vault-repeat textarea:focus {
                border-color: rgba(107,29,20,.52);
                background: #fff;
                box-shadow: 0 0 0 3px rgba(107,29,20,.07);
            }

            .master-vault-filters {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .master-vault-filter {
                min-height: 43px;
                border: 1px solid var(--vault-border-soft);
                border-radius: 10px;
                background: rgba(255,253,248,.72);
                padding: 0 11px;
                color: var(--vault-muted);
            }

            .master-vault-filter.is-active {
                border-color: var(--vault-accent);
                background: var(--vault-accent);
                color: #f8f3e9;
            }

            .master-vault-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
                gap: 14px;
                margin-top: 20px;
            }

            .master-vault-card {
                position: relative;
                overflow: hidden;
                border: 1px solid var(--vault-border-soft);
                border-radius: 15px;
                background: linear-gradient(145deg, rgba(255,253,248,.98), rgba(246,241,231,.96));
                box-shadow: 0 5px 16px rgba(83,21,15,.05);
                transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
            }

            .master-vault-card:hover {
                transform: translateY(-2px);
                border-color: rgba(140,98,57,.48);
                box-shadow: 0 12px 28px rgba(83,21,15,.08);
            }

            .master-vault-card::before {
                content: '';
                position: absolute;
                left: 0;
                top: 13px;
                bottom: 13px;
                width: 3px;
                border-radius: 0 999px 999px 0;
                background: #96765c;
            }

            .master-vault-card[data-nature="wonderful"]::before { background: #6b1d14; }
            .master-vault-card[data-nature="technological"]::before { background: #486a83; }

            .master-vault-card-main {
                display: grid;
                grid-template-columns: 72px minmax(0, 1fr);
                gap: 12px;
                padding: 14px 14px 12px 17px;
            }

            .master-vault-thumb,
            .master-vault-editor-image {
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.58);
                background: radial-gradient(circle at 50% 40%, rgba(205,187,159,.28), rgba(239,233,220,.55));
                color: #987a61;
            }

            .master-vault-thumb {
                width: 72px;
                height: 72px;
                border-radius: 12px;
            }

            .master-vault-thumb img,
            .master-vault-editor-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .master-vault-card h2 {
                overflow: hidden;
                margin: 0;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
            }

            .master-vault-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                margin-top: 5px;
            }

            .master-vault-chip {
                display: inline-flex;
                min-height: 21px;
                align-items: center;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 999px;
                background: rgba(255,255,255,.52);
                padding: 0 7px;
                color: #80634d;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            .master-vault-card p {
                display: -webkit-box;
                overflow: hidden;
                margin: 7px 0 0;
                color: #755b48;
                font-size: 11px;
                line-height: 1.45;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
            }

            .master-vault-card-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-top: 1px solid rgba(205,187,159,.48);
                padding: 8px 11px 9px 16px;
            }

            .master-vault-card-actions-left {
                display: flex;
                gap: 2px;
            }

            .master-vault-link-action,
            .master-vault-favorite {
                border: 0;
                background: transparent;
                color: var(--vault-muted);
                cursor: pointer;
            }

            .master-vault-link-action {
                min-height: 31px;
                border-radius: 8px;
                padding: 0 8px;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
            }

            .master-vault-link-action:hover {
                background: rgba(107,29,20,.06);
                color: var(--vault-accent);
            }

            .master-vault-favorite {
                display: inline-flex;
                width: 31px;
                height: 31px;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
            }

            .master-vault-favorite.is-active {
                background: rgba(181,130,68,.10);
                color: #8a571f;
            }

            .master-vault-empty {
                margin-top: 20px;
                border: 1px dashed rgba(176,140,98,.5);
                border-radius: 17px;
                background: rgba(255,253,248,.48);
                padding: 68px 24px;
                text-align: center;
            }

            .master-vault-empty h3 {
                margin: 0;
                font-family: Georgia, serif;
                font-size: 21px;
            }

            .master-vault-empty p {
                max-width: 520px;
                margin: 8px auto 0;
                color: #80634e;
                font-size: 12px;
                line-height: 1.55;
            }

            .master-vault-modal-layer {
                position: fixed;
                inset: 0;
                z-index: 130;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 22px;
                background: rgba(15,10,8,.70);
                backdrop-filter: blur(7px);
            }

            .master-vault-modal {
                display: flex;
                width: min(980px, 100%);
                max-height: calc(100vh - 44px);
                flex-direction: column;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 18px;
                background: #f8f5ee;
                box-shadow: 0 28px 80px rgba(31,18,12,.30);
            }

            .master-vault-modal-head,
            .master-vault-modal-foot {
                flex: 0 0 auto;
                background: #efe9dc;
            }

            .master-vault-modal-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                border-bottom: 1px solid rgba(205,187,159,.62);
                padding: 14px 17px;
            }

            .master-vault-modal-head p {
                margin: 0 0 2px;
                color: var(--vault-muted);
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .13em;
                text-transform: uppercase;
            }

            .master-vault-modal-head h2 {
                margin: 0;
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
            }

            .master-vault-close {
                display: inline-flex;
                width: 35px;
                height: 35px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 9px;
                background: transparent;
                color: var(--vault-muted);
                font-size: 22px;
                cursor: pointer;
            }

            .master-vault-close:hover { background: rgba(107,29,20,.07); color: var(--vault-accent); }

            .master-vault-modal-body {
                min-height: 0;
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 17px;
            }

            .master-vault-editor-grid {
                display: grid;
                grid-template-columns: 210px minmax(0, 1fr);
                gap: 17px;
            }

            .master-vault-image-column {
                position: sticky;
                top: 0;
                align-self: start;
            }

            .master-vault-editor-image {
                aspect-ratio: 1 / 1;
                border-radius: 15px;
            }

            .master-vault-image-actions {
                display: grid;
                gap: 7px;
                margin-top: 8px;
            }

            .master-vault-image-actions label {
                display: inline-flex;
                min-height: 37px;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--vault-border-soft);
                border-radius: 9px;
                background: rgba(255,253,248,.74);
                color: var(--vault-muted);
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                border-bottom: 1px solid rgba(205,187,159,.55);
                padding-bottom: 9px;
            }

            .master-vault-tab {
                min-height: 33px;
                border-radius: 8px;
                background: transparent;
                padding: 0 9px;
                color: var(--vault-muted);
            }

            .master-vault-tab.is-active {
                background: var(--vault-accent);
                color: #f8f3e9;
            }

            .master-vault-fields {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
                margin-top: 14px;
            }

            .master-vault-field.is-wide { grid-column: 1 / -1; }
            .master-vault-field > span { display: block; margin-bottom: 5px; }

            .master-vault-natures {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 7px;
            }

            .master-vault-nature {
                min-height: 41px;
                border: 1px solid var(--vault-border-soft);
                border-radius: 9px;
                background: rgba(255,253,248,.70);
                color: var(--vault-muted);
                font-size: 9px;
                font-weight: 900;
                cursor: pointer;
            }

            .master-vault-nature.is-active {
                border-color: var(--vault-accent);
                background: rgba(107,29,20,.08);
                color: var(--vault-accent);
            }

            .master-vault-toggle-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 11px;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 11px;
                background: rgba(255,253,248,.58);
                padding: 10px 11px;
            }

            .master-vault-toggle-row strong { display: block; font-size: 11px; }
            .master-vault-toggle-row small { display: block; margin-top: 2px; color: #876a53; font-size: 9px; line-height: 1.35; }

            .master-vault-switch {
                position: relative;
                width: 39px;
                height: 22px;
                flex: 0 0 auto;
                border: 0;
                border-radius: 999px;
                background: #d5c8b5;
                cursor: pointer;
            }

            .master-vault-switch::after {
                content: '';
                position: absolute;
                left: 3px;
                top: 3px;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: white;
                box-shadow: 0 2px 5px rgba(0,0,0,.15);
                transition: transform .14s ease;
            }

            .master-vault-switch.is-on { background: var(--vault-accent); }
            .master-vault-switch.is-on::after { transform: translateX(17px); }

            .master-vault-section {
                margin-top: 14px;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 13px;
                background: rgba(255,253,248,.56);
                padding: 12px;
            }

            .master-vault-section h3 {
                margin: 0;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
            }

            .master-vault-section p {
                margin: 3px 0 0;
                color: #856950;
                font-size: 9px;
                line-height: 1.5;
            }

            .master-vault-repeat { display: grid; gap: 8px; margin-top: 10px; }
            .master-vault-repeat-row { display: grid; grid-template-columns: 160px minmax(0, 1fr) 34px; gap: 7px; }

            .master-vault-remove {
                width: 34px;
                height: 34px;
                align-self: center;
                border: 0;
                border-radius: 8px;
                background: rgba(165,47,39,.07);
                color: #a33029;
                font-size: 17px;
                cursor: pointer;
            }

            .master-vault-error {
                margin-top: 11px;
                border: 1px solid rgba(178,57,48,.26);
                border-radius: 9px;
                background: rgba(178,57,48,.07);
                padding: 9px 10px;
                color: #8d2c25;
                font-size: 10px;
                font-weight: 800;
            }

            .master-vault-modal-foot {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                border-top: 1px solid rgba(205,187,159,.62);
                padding: 11px 15px;
            }

            .master-vault-danger {
                min-height: 37px;
                border: 0;
                border-radius: 8px;
                background: transparent;
                padding: 0 9px;
                color: #a33029;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-danger:hover { background: rgba(163,48,41,.08); }
            .master-vault-modal-actions { display: flex; gap: 7px; }

            .master-vault-toast {
                position: fixed;
                right: 22px;
                bottom: 22px;
                z-index: 160;
                min-width: 250px;
                max-width: 380px;
                border: 1px solid var(--vault-border-soft);
                border-radius: 11px;
                background: #fffdf8;
                padding: 10px 12px;
                color: var(--vault-ink);
                box-shadow: 0 16px 40px rgba(43,29,23,.18);
                font-size: 10px;
                font-weight: 800;
            }

            .master-vault-toast.is-error { border-color: rgba(178,57,48,.30); color: #8f2e27; }

            @media (max-width: 900px) {
                .master-vault-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .master-vault-toolbar { grid-template-columns: 1fr; }
                .master-vault-editor-grid { grid-template-columns: 1fr; }
                .master-vault-image-column { position: static; width: min(250px, 100%); }
            }

            @media (max-width: 640px) {
                .master-vault-shell { width: calc(100% - 20px); padding-top: 18px; }
                .master-vault-hero { align-items: stretch; flex-direction: column; }
                .master-vault-primary { width: 100%; }
                .master-vault-stats,
                .master-vault-fields,
                .master-vault-natures { grid-template-columns: 1fr; }
                .master-vault-repeat-row { grid-template-columns: 1fr 34px; }
                .master-vault-repeat-row > :first-child { grid-column: 1 / -1; }
            }
        
            /* ==========================================================
               VAULT V3 — MÓDULOS MECÂNICOS
            ========================================================== */
            .master-vault-module-toolbar {
                grid-column: 1 / -1;
                display: grid;
                grid-template-columns: minmax(0,1fr) auto;
                gap: 12px;
                align-items: center;
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 14px;
                background: rgba(255,253,248,.56);
                padding: 12px;
            }

            .master-vault-module-toolbar strong,
            .master-vault-module-card h3,
            .master-vault-subsection-title {
                font-family: Georgia, serif;
                color: var(--vault-ink);
            }

            .master-vault-module-toolbar strong {
                display: block;
                font-size: 13px;
            }

            .master-vault-module-toolbar small {
                display: block;
                margin-top: 2px;
                color: #866950;
                font-size: 9px;
                line-height: 1.45;
            }

            .master-vault-module-buttons {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 6px;
            }

            .master-vault-module-add {
                min-height: 34px;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 9px;
                background: rgba(255,255,255,.62);
                padding: 0 10px;
                color: var(--vault-muted);
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-module-add:hover,
            .master-vault-module-add.is-active {
                border-color: rgba(107,29,20,.40);
                background: rgba(107,29,20,.07);
                color: var(--vault-accent);
            }

            .master-vault-module-card {
                grid-column: 1 / -1;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.64);
                border-radius: 15px;
                background: linear-gradient(145deg, rgba(255,253,248,.92), rgba(246,241,231,.78));
            }

            .master-vault-module-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                border-bottom: 1px solid rgba(205,187,159,.50);
                background: rgba(239,233,220,.62);
                padding: 11px 13px;
            }

            .master-vault-module-head h3 {
                margin: 0;
                font-size: 15px;
                font-weight: 900;
            }

            .master-vault-module-head p {
                margin: 2px 0 0;
                color: #85684f;
                font-size: 9px;
                line-height: 1.45;
            }

            .master-vault-module-remove {
                min-height: 30px;
                border: 0;
                border-radius: 8px;
                background: transparent;
                padding: 0 8px;
                color: #a03b33;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-module-remove:hover {
                background: rgba(160,59,51,.08);
            }

            .master-vault-module-body { padding: 13px; }

            .master-vault-subsection + .master-vault-subsection {
                margin-top: 14px;
                padding-top: 14px;
                border-top: 1px solid rgba(205,187,159,.46);
            }

            .master-vault-subsection-title {
                margin: 0 0 3px;
                font-size: 12px;
                font-weight: 900;
            }

            .master-vault-hint {
                margin: 0 0 9px;
                color: #876c55;
                font-size: 9px;
                line-height: 1.45;
            }

            .master-vault-choice-grid {
                display: grid;
                grid-template-columns: repeat(4,minmax(0,1fr));
                gap: 7px;
            }

            .master-vault-choice-grid.is-3 {
                grid-template-columns: repeat(3,minmax(0,1fr));
            }

            .master-vault-rule-choice {
                min-height: 54px;
                border: 1px solid rgba(205,187,159,.65);
                border-radius: 10px;
                background: rgba(255,255,255,.52);
                padding: 8px 9px;
                text-align: left;
                color: #785c47;
                cursor: pointer;
            }

            .master-vault-rule-choice strong {
                display: block;
                color: var(--vault-ink);
                font-size: 10px;
                font-weight: 900;
            }

            .master-vault-rule-choice span {
                display: block;
                margin-top: 2px;
                font-size: 8px;
                line-height: 1.35;
            }

            .master-vault-rule-choice.is-active {
                border-color: rgba(107,29,20,.46);
                background: rgba(107,29,20,.07);
            }

            .master-vault-rule-choice.is-active strong,
            .master-vault-rule-choice.is-active span {
                color: var(--vault-accent);
            }

            .master-vault-inline-grid {
                display: grid;
                grid-template-columns: repeat(2,minmax(0,1fr));
                gap: 10px;
            }

            .master-vault-inline-grid.is-3 {
                grid-template-columns: repeat(3,minmax(0,1fr));
            }

            .master-vault-chip-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .master-vault-rule-chip {
                min-height: 32px;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 999px;
                background: rgba(255,255,255,.54);
                padding: 0 10px;
                color: #7d624d;
                font-size: 8px;
                font-weight: 900;
                cursor: pointer;
            }

            .master-vault-rule-chip.is-active {
                border-color: rgba(107,29,20,.48);
                background: rgba(107,29,20,.09);
                color: var(--vault-accent);
            }

            .master-vault-damage-row {
                display: grid;
                grid-template-columns: 82px 92px minmax(150px,1fr) minmax(130px,1fr) 34px;
                gap: 7px;
                align-items: end;
            }

            .master-vault-feature-card {
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 13px;
                background: rgba(255,253,248,.55);
                padding: 11px;
            }

            .master-vault-feature-card + .master-vault-feature-card {
                margin-top: 9px;
            }

            .master-vault-feature-head {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .master-vault-feature-head input {
                flex: 1 1 auto;
            }

            .master-vault-counter-box {
                margin-top: 10px;
                border-top: 1px dashed rgba(176,140,98,.38);
                padding-top: 10px;
            }

            .master-vault-counter-grid {
                display: grid;
                grid-template-columns: repeat(4,minmax(0,1fr));
                gap: 8px;
                margin-top: 8px;
            }

            .master-vault-formula {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 10px;
                border: 1px solid rgba(107,29,20,.16);
                border-radius: 11px;
                background: rgba(107,29,20,.045);
                padding: 9px 11px;
            }

            .master-vault-formula span {
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .master-vault-formula strong {
                display: block;
                margin-top: 1px;
                font-family: Georgia, serif;
                font-size: 13px;
                color: var(--vault-ink);
            }

            @media (max-width: 760px) {
                .master-vault-module-toolbar { grid-template-columns: 1fr; }
                .master-vault-module-buttons { justify-content: flex-start; }
                .master-vault-choice-grid,
                .master-vault-choice-grid.is-3,
                .master-vault-inline-grid.is-3,
                .master-vault-counter-grid {
                    grid-template-columns: repeat(2,minmax(0,1fr));
                }
                .master-vault-damage-row {
                    grid-template-columns: repeat(2,minmax(0,1fr));
                }
                .master-vault-damage-row > :nth-child(3),
                .master-vault-damage-row > :nth-child(4) {
                    grid-column: span 2;
                }
            }

            @media (max-width: 520px) {
                .master-vault-choice-grid,
                .master-vault-choice-grid.is-3,
                .master-vault-inline-grid,
                .master-vault-inline-grid.is-3,
                .master-vault-counter-grid,
                .master-vault-damage-row {
                    grid-template-columns: 1fr;
                }
                .master-vault-damage-row > :nth-child(3),
                .master-vault-damage-row > :nth-child(4) {
                    grid-column: auto;
                }
            }

        

            /* ==========================================================
               VAULT V4 — EDITOR BASEADO NO INVENTÁRIO DO PLAYER
               Mantém o backend e o contrato mecânico da Fase 3.
            ========================================================== */

            .master-vault-v4-layer {
                position: fixed;
                inset: 0;
                z-index: 240;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }

            .master-vault-v4-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(18, 12, 9, .74);
                backdrop-filter: blur(8px);
            }

            .master-vault-v4-modal {
                position: relative;
                z-index: 1;
                display: flex;
                width: min(960px, 100%);
                max-height: 94dvh;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid rgba(205, 187, 159, .78);
                border-radius: 18px;
                background:
                    linear-gradient(
                        180deg,
                        #fbf8f1 0%,
                        #f7f1e7 100%
                    );
                box-shadow:
                    0 28px 80px rgba(31, 18, 12, .32);
            }

            .master-vault-v4-head {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                border-bottom: 1px solid rgba(216, 199, 171, .66);
                background: rgba(239, 233, 220, .74);
                padding: 14px 20px;
            }

            .master-vault-v4-head-kicker {
                margin: 0;
                color: #8c6239;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .14em;
                text-transform: uppercase;
            }

            .master-vault-v4-head h2 {
                margin: 2px 0 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 21px;
                font-weight: 900;
                line-height: 1.08;
            }

            .master-vault-v4-head-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 6px;
            }

            .master-vault-v4-head-chip {
                display: inline-flex;
                min-height: 20px;
                align-items: center;
                border: 1px solid rgba(205, 187, 159, .62);
                border-radius: 999px;
                background: rgba(255, 255, 255, .48);
                padding: 0 7px;
                color: #83644d;
                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
            }

            .master-vault-v4-close {
                display: inline-flex;
                width: 36px;
                height: 36px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 10px;
                background: transparent;
                color: #8c6239;
                font-size: 22px;
                line-height: 1;
                cursor: pointer;
            }

            .master-vault-v4-close:hover {
                background: rgba(107, 29, 20, .07);
                color: #6b1d14;
            }

            .master-vault-v4-scroll {
                min-height: 0;
                flex: 1 1 auto;
                overflow-x: hidden;
                overflow-y: auto;
                padding: 20px;
                scrollbar-width: thin;
                scrollbar-color:
                    rgba(140, 98, 57, .34)
                    transparent;
            }

            .master-vault-v4-scroll::-webkit-scrollbar {
                width: 6px;
            }

            .master-vault-v4-scroll::-webkit-scrollbar-track {
                background: transparent;
            }

            .master-vault-v4-scroll::-webkit-scrollbar-thumb {
                border-radius: 999px;
                background: rgba(140, 98, 57, .32);
            }

            .master-vault-v4-identity {
                display: grid;
                grid-template-columns: 160px minmax(0, 1fr);
                gap: 20px;
            }

            .master-vault-v4-photo {
                position: relative;
            }

            .master-vault-v4-photo-frame {
                position: relative;
                display: flex;
                aspect-ratio: 1 / 1;
                width: 100%;
                cursor: pointer;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px dashed rgba(180, 145, 103, .72);
                border-radius: 13px;
                background:
                    radial-gradient(
                        circle at 50% 40%,
                        rgba(205,187,159,.24),
                        rgba(239,233,220,.42)
                    );
                color: rgba(140,98,57,.72);
                transition:
                    border-color .15s ease,
                    background .15s ease;
            }

            .master-vault-v4-photo-frame:hover {
                border-color: rgba(107,29,20,.42);
                background: rgba(239,233,220,.58);
            }

            .master-vault-v4-photo-frame img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .master-vault-v4-photo-empty {
                text-align: center;
            }

            .master-vault-v4-photo-empty svg {
                width: 30px;
                height: 30px;
                margin: 0 auto;
                opacity: .62;
            }

            .master-vault-v4-photo-empty span {
                display: block;
                margin-top: 8px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
            }

            .master-vault-v4-photo-remove {
                width: 100%;
                margin-top: 7px;
                border: 0;
                background: transparent;
                color: #9e3129;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v4-photo-remove:hover {
                text-decoration: underline;
            }

            .master-vault-v4-stack {
                display: grid;
                gap: 15px;
            }

            .master-vault-v4-field > span,
            .master-vault-v4-label {
                display: block;
                margin-bottom: 5px;
                color: #53150f;
                font-size: 11px;
                font-weight: 900;
            }

            .master-vault-v4-help {
                color: rgba(140,98,57,.74);
                font-size: 9px;
                line-height: 1.45;
            }

            .master-vault-v4-input,
            .master-vault-v4-select,
            .master-vault-v4-textarea {
                width: 100%;
                border: 1px solid rgba(205,187,159,.76);
                border-radius: 9px;
                background: rgba(255,255,255,.72);
                color: #53150f;
                outline: none;
                transition:
                    border-color .14s ease,
                    box-shadow .14s ease,
                    background .14s ease;
            }

            .master-vault-v4-input,
            .master-vault-v4-select {
                min-height: 42px;
                padding: 8px 11px;
            }

            .master-vault-v4-textarea {
                min-height: 104px;
                padding: 10px 11px;
                line-height: 1.55;
                resize: vertical;
            }

            .master-vault-v4-input:focus,
            .master-vault-v4-select:focus,
            .master-vault-v4-textarea:focus {
                border-color: rgba(107,29,20,.46);
                background: #fff;
                box-shadow: 0 0 0 3px rgba(107,29,20,.06);
            }

            .master-vault-v4-choice-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 7px;
            }

            .master-vault-v4-choice-grid.is-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .master-vault-v4-choice {
                min-height: 42px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 9px;
                background: rgba(255,255,255,.56);
                color: #7b5e47;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .04em;
                cursor: pointer;
                transition:
                    border-color .14s ease,
                    background .14s ease,
                    color .14s ease;
            }

            .master-vault-v4-choice:hover {
                background: rgba(239,233,220,.62);
            }

            .master-vault-v4-choice.is-active {
                border-color: rgba(107,29,20,.44);
                background: rgba(107,29,20,.07);
                color: #6b1d14;
            }

            .master-vault-v4-grid-2 {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .master-vault-v4-grid-3 {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }

            .master-vault-v4-toggle-card {
                display: flex;
                min-height: 44px;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 10px;
                background: rgba(255,255,255,.52);
                padding: 8px 11px;
            }

            .master-vault-v4-toggle-card strong {
                display: block;
                color: #53150f;
                font-size: 10px;
            }

            .master-vault-v4-toggle-card small {
                display: block;
                margin-top: 2px;
                color: rgba(140,98,57,.74);
                font-size: 8px;
                line-height: 1.35;
            }

            .master-vault-v4-switch {
                position: relative;
                width: 38px;
                height: 21px;
                flex: 0 0 auto;
                border: 0;
                border-radius: 999px;
                background: #cdbb9f;
                cursor: pointer;
                transition: background .15s ease;
            }

            .master-vault-v4-switch::after {
                content: '';
                position: absolute;
                top: 2px;
                left: 2px;
                width: 17px;
                height: 17px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 2px 5px rgba(0,0,0,.14);
                transition: transform .15s ease;
            }

            .master-vault-v4-switch.is-on {
                background: #6b1d14;
            }

            .master-vault-v4-switch.is-on::after {
                transform: translateX(17px);
            }

            .master-vault-v4-divider {
                height: 1px;
                margin: 20px 0;
                background: rgba(216,199,171,.55);
            }

            .master-vault-v4-modules-head {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 16px;
            }

            .master-vault-v4-section-kicker {
                color: #8c6239;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .master-vault-v4-section-title {
                margin: 2px 0 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 17px;
                font-weight: 900;
            }

            .master-vault-v4-module-actions {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 6px;
            }

            .master-vault-v4-module-action {
                min-height: 34px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 9px;
                background: rgba(255,255,255,.58);
                padding: 0 10px;
                color: #7a5d46;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v4-module-action:hover,
            .master-vault-v4-module-action.is-active {
                border-color: rgba(107,29,20,.40);
                background: rgba(107,29,20,.07);
                color: #6b1d14;
            }

            .master-vault-v4-module {
                margin-top: 14px;
                overflow: hidden;
                border: 1px solid rgba(216,199,171,.64);
                border-radius: 14px;
                background: rgba(255,253,248,.60);
            }

            .master-vault-v4-module-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                border-bottom: 1px solid rgba(216,199,171,.54);
                background: rgba(239,233,220,.46);
                padding: 12px 14px;
            }

            .master-vault-v4-module-head h3 {
                margin: 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 17px;
                font-weight: 900;
            }

            .master-vault-v4-module-head p {
                margin: 2px 0 0;
                color: rgba(140,98,57,.76);
                font-size: 9px;
                line-height: 1.45;
            }

            .master-vault-v4-remove {
                border: 0;
                border-radius: 8px;
                background: transparent;
                padding: 6px 8px;
                color: #a3322a;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v4-remove:hover {
                background: rgba(163,50,42,.07);
            }

            .master-vault-v4-module-body {
                display: grid;
                gap: 18px;
                padding: 14px;
            }

            .master-vault-v4-rule {
                border: 1px solid rgba(216,199,171,.52);
                border-radius: 12px;
                background: rgba(255,255,255,.42);
                padding: 12px;
            }

            .master-vault-v4-rule-title {
                margin: 0;
                color: #53150f;
                font-size: 10px;
                font-weight: 900;
            }

            .master-vault-v4-rule-copy {
                margin: 2px 0 0;
                color: rgba(140,98,57,.74);
                font-size: 8.5px;
                line-height: 1.45;
            }

            .master-vault-v4-chip-row {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 9px;
            }

            .master-vault-v4-chip-button {
                min-height: 31px;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 999px;
                background: rgba(255,255,255,.55);
                padding: 0 10px;
                color: #7b604a;
                font-size: 8px;
                font-weight: 900;
                cursor: pointer;
            }

            .master-vault-v4-chip-button.is-active {
                border-color: rgba(107,29,20,.44);
                background: rgba(107,29,20,.08);
                color: #6b1d14;
            }

            .master-vault-v4-repeat {
                display: grid;
                gap: 8px;
                margin-top: 10px;
            }

            .master-vault-v4-repeat-row {
                display: grid;
                grid-template-columns: minmax(150px,.8fr) minmax(0,1.4fr) auto;
                gap: 8px;
                align-items: start;
            }

            .master-vault-v4-damage-grid {
                display: grid;
                grid-template-columns: 78px 90px minmax(140px,1fr) minmax(140px,1fr);
                gap: 8px;
                margin-top: 9px;
            }

            .master-vault-v4-formula {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 10px;
                border: 1px solid rgba(107,29,20,.14);
                border-radius: 10px;
                background: rgba(107,29,20,.04);
                padding: 9px 11px;
            }

            .master-vault-v4-formula span {
                color: #8c6239;
                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
            }

            .master-vault-v4-formula strong {
                display: block;
                margin-top: 1px;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 13px;
            }

            .master-vault-v4-subsection {
                margin-top: 18px;
            }

            .master-vault-v4-subsection-head {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 12px;
                border-bottom: 1px solid rgba(216,199,171,.48);
                padding-bottom: 8px;
            }

            .master-vault-v4-subsection-head h3 {
                margin: 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 15px;
                font-weight: 900;
            }

            .master-vault-v4-subsection-head p {
                margin: 2px 0 0;
                color: rgba(140,98,57,.74);
                font-size: 8.5px;
            }

            .master-vault-v4-link {
                border: 0;
                background: transparent;
                color: #6b1d14;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v4-link:hover {
                text-decoration: underline;
            }

            .master-vault-v4-feature {
                margin-top: 9px;
                border: 1px solid rgba(216,199,171,.52);
                border-radius: 12px;
                background: rgba(255,255,255,.40);
                padding: 11px;
            }

            .master-vault-v4-feature-head {
                display: grid;
                grid-template-columns: minmax(0,1fr) auto;
                gap: 8px;
            }

            .master-vault-v4-counter {
                margin-top: 10px;
                border-top: 1px dashed rgba(176,140,98,.36);
                padding-top: 10px;
            }

            .master-vault-v4-secret {
                margin-top: 20px;
                overflow: hidden;
                border: 1px solid rgba(126,69,49,.24);
                border-radius: 14px;
                background:
                    linear-gradient(
                        145deg,
                        rgba(107,29,20,.035),
                        rgba(255,253,248,.58)
                    );
            }

            .master-vault-v4-secret-head {
                border-bottom: 1px solid rgba(126,69,49,.16);
                padding: 11px 13px;
            }

            .master-vault-v4-secret-head span {
                display: block;
                color: #9b4c38;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .master-vault-v4-secret-head h3 {
                margin: 2px 0 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 15px;
                font-weight: 900;
            }

            .master-vault-v4-secret-body {
                display: grid;
                gap: 12px;
                padding: 13px;
            }

            .master-vault-v4-foot {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border-top: 1px solid rgba(216,199,171,.66);
                background: rgba(239,233,220,.72);
                padding: 11px 16px;
            }

            .master-vault-v4-foot-actions {
                display: flex;
                gap: 7px;
            }

            .master-vault-v4-danger {
                min-height: 38px;
                border: 0;
                border-radius: 9px;
                background: transparent;
                padding: 0 10px;
                color: #a3322a;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v4-danger:hover {
                background: rgba(163,50,42,.07);
            }

            .master-vault-v4-secondary {
                display: inline-flex;
                min-height: 38px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 9px;
                background: rgba(255,255,255,.55);
                padding: 0 12px;
                color: #7b5f48;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v4-secondary:hover {
                background: #fffdf8;
                color: #6b1d14;
            }

            .master-vault-v4-primary {
                display: inline-flex;
                min-height: 38px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 9px;
                background: #6b1d14;
                padding: 0 14px;
                color: #f8f3e9;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v4-primary:hover {
                background: #53150f;
            }

            .master-vault-v4-primary:disabled,
            .master-vault-v4-secondary:disabled,
            .master-vault-v4-danger:disabled {
                cursor: wait;
                opacity: .58;
            }

            @media (max-width: 760px) {
                .master-vault-v4-identity {
                    grid-template-columns: 1fr;
                }

                .master-vault-v4-photo {
                    width: min(190px, 100%);
                }

                .master-vault-v4-grid-3,
                .master-vault-v4-damage-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .master-vault-v4-repeat-row {
                    grid-template-columns: 1fr auto;
                }

                .master-vault-v4-repeat-row > :first-child {
                    grid-column: 1 / -1;
                }

                .master-vault-v4-modules-head {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .master-vault-v4-module-actions {
                    justify-content: flex-start;
                }
            }

            @media (max-width: 520px) {
                .master-vault-v4-layer {
                    padding: 8px;
                }

                .master-vault-v4-scroll {
                    padding: 14px;
                }

                .master-vault-v4-grid-2,
                .master-vault-v4-grid-3,
                .master-vault-v4-choice-grid,
                .master-vault-v4-choice-grid.is-3,
                .master-vault-v4-damage-grid {
                    grid-template-columns: 1fr;
                }

                .master-vault-v4-foot {
                    align-items: stretch;
                    flex-direction: column;
                }

                .master-vault-v4-foot-actions {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    width: 100%;
                }

                .master-vault-v4-danger {
                    width: 100%;
                }
            }

        

            /* ==========================================================
               VAULT V5 — WORKBENCH DE CRIAÇÃO
            ========================================================== */

            .master-vault-v4-layer { padding: 10px; }

            .master-vault-v4-modal {
                width: min(1680px, 97vw);
                max-height: 97dvh;
                border-radius: 20px;
            }

            .master-vault-v4-head {
                min-height: 78px;
                padding: 17px 28px;
            }

            .master-vault-v4-head-kicker {
                font-size: 10px;
                letter-spacing: .16em;
            }

            .master-vault-v4-head h2 { font-size: 27px; }

            .master-vault-v4-head-chip {
                min-height: 24px;
                padding: 0 9px;
                font-size: 9px;
            }

            .master-vault-v4-close {
                width: 42px;
                height: 42px;
                font-size: 27px;
            }

            .master-vault-v4-scroll {
                padding: 26px 30px 32px;
            }

            .master-vault-v4-identity {
                grid-template-columns: 220px minmax(0, 1fr);
                gap: 28px;
            }

            .master-vault-v4-stack { gap: 18px; }

            .master-vault-v4-field > span,
            .master-vault-v4-label {
                margin-bottom: 7px;
                font-size: 13px;
            }

            .master-vault-v4-help {
                font-size: 11px;
                line-height: 1.5;
            }

            .master-vault-v4-input,
            .master-vault-v4-select {
                min-height: 46px;
                padding: 10px 13px;
                font-size: 14px;
            }

            .master-vault-v4-textarea {
                min-height: 118px;
                padding: 12px 13px;
                font-size: 14px;
                line-height: 1.6;
            }

            .master-vault-v4-choice {
                min-height: 48px;
                padding: 7px 11px;
                font-size: 11px;
            }

            .master-vault-v4-toggle-card {
                min-height: 50px;
                padding: 10px 13px;
            }

            .master-vault-v4-toggle-card strong { font-size: 12px; }
            .master-vault-v4-toggle-card small { font-size: 10px; }

            .master-vault-v4-divider { margin: 28px 0; }
            .master-vault-v4-section-kicker { font-size: 10px; }
            .master-vault-v4-section-title { font-size: 22px; }

            .master-vault-v4-module-head { padding: 15px 18px; }
            .master-vault-v4-module-head h3 { font-size: 20px; }
            .master-vault-v4-module-head p { font-size: 11px; }

            .master-vault-v4-module-body {
                gap: 20px;
                padding: 18px;
            }

            .master-vault-v4-rule { padding: 15px; }
            .master-vault-v4-rule-title { font-size: 12px; }
            .master-vault-v4-rule-copy { font-size: 10px; }

            .master-vault-v4-chip-button {
                min-height: 36px;
                padding: 0 12px;
                font-size: 10px;
            }

            .master-vault-v4-subsection { margin-top: 26px; }
            .master-vault-v4-subsection-head { padding-bottom: 11px; }
            .master-vault-v4-subsection-head h3 { font-size: 20px; }
            .master-vault-v4-subsection-head p { font-size: 11px; }

            .master-vault-v4-link,
            .master-vault-v4-remove,
            .master-vault-v4-module-action,
            .master-vault-v4-danger,
            .master-vault-v4-secondary,
            .master-vault-v4-primary {
                font-size: 10px;
            }

            .master-vault-v4-foot {
                min-height: 64px;
                padding: 12px 22px;
            }

            /* Categoria do item */
            .master-vault-v5-category-shell {
                overflow: hidden;
                border: 1px solid rgba(216,199,171,.68);
                border-radius: 16px;
                background: linear-gradient(
                    145deg,
                    rgba(255,253,248,.88),
                    rgba(245,238,227,.72)
                );
            }

            .master-vault-v5-category-head {
                border-bottom: 1px solid rgba(216,199,171,.52);
                background: rgba(239,233,220,.48);
                padding: 16px 18px;
            }

            .master-vault-v5-category-head strong {
                display: block;
                margin-top: 2px;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 21px;
            }

            .master-vault-v5-category-head p {
                max-width: 780px;
                margin: 4px 0 0;
                color: #7b5f49;
                font-size: 11px;
                line-height: 1.5;
            }

            .master-vault-v5-category-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0,1fr));
                gap: 10px;
                padding: 14px;
            }

            .master-vault-v5-category {
                display: grid;
                grid-template-columns: 44px minmax(0,1fr);
                gap: 11px;
                align-items: center;
                min-height: 82px;
                border: 1px solid rgba(205,187,159,.70);
                border-radius: 12px;
                background: rgba(255,255,255,.56);
                padding: 12px;
                text-align: left;
                color: #765944;
                cursor: pointer;
                transition: .14s ease;
            }

            .master-vault-v5-category:hover {
                transform: translateY(-1px);
                border-color: rgba(140,98,57,.44);
                background: rgba(255,255,255,.82);
            }

            .master-vault-v5-category.is-active {
                border-color: rgba(107,29,20,.46);
                background: rgba(107,29,20,.065);
                box-shadow: 0 8px 20px rgba(83,21,15,.07);
            }

            .master-vault-v5-category-icon {
                display: flex;
                width: 44px;
                height: 44px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 11px;
                background: rgba(255,253,248,.80);
                color: #8c6239;
            }

            .master-vault-v5-category-icon svg {
                width: 23px;
                height: 23px;
            }

            .master-vault-v5-category.is-active .master-vault-v5-category-icon {
                border-color: rgba(107,29,20,.26);
                background: rgba(107,29,20,.08);
                color: #6b1d14;
            }

            .master-vault-v5-category strong {
                display: block;
                color: #53150f;
                font-size: 13px;
                font-weight: 900;
            }

            .master-vault-v5-category span span {
                display: block;
                margin-top: 3px;
                font-size: 10px;
                line-height: 1.4;
            }

            /* Habilidades */
            .master-vault-v5-abilities {
                margin-top: 30px;
                overflow: hidden;
                border: 1px solid rgba(107,29,20,.18);
                border-radius: 17px;
                background: linear-gradient(
                    145deg,
                    rgba(255,253,248,.92),
                    rgba(247,241,232,.84)
                );
                box-shadow: 0 10px 28px rgba(83,21,15,.045);
            }

            .master-vault-v5-abilities-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                border-bottom: 1px solid rgba(107,29,20,.13);
                background: linear-gradient(
                    90deg,
                    rgba(107,29,20,.07),
                    rgba(239,233,220,.45)
                );
                padding: 17px 19px;
            }

            .master-vault-v5-abilities-head-main {
                display: flex;
                align-items: center;
                gap: 13px;
                min-width: 0;
            }

            .master-vault-v5-abilities-mark {
                display: flex;
                width: 43px;
                height: 43px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(107,29,20,.18);
                border-radius: 11px;
                background: rgba(255,253,248,.75);
                color: #6b1d14;
            }

            .master-vault-v5-abilities-mark svg {
                width: 22px;
                height: 22px;
            }

            .master-vault-v5-abilities-head span {
                color: #8c6239;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .13em;
                text-transform: uppercase;
            }

            .master-vault-v5-abilities-head h3 {
                margin: 2px 0 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 22px;
                font-weight: 900;
            }

            .master-vault-v5-abilities-head p {
                margin: 3px 0 0;
                color: #765944;
                font-size: 11px;
            }

            .master-vault-v5-add-ability {
                display: inline-flex;
                min-height: 42px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                gap: 7px;
                border: 0;
                border-radius: 10px;
                background: #6b1d14;
                padding: 0 14px;
                color: #f8f3e9;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v5-abilities-body {
                display: grid;
                gap: 10px;
                padding: 14px;
            }

            .master-vault-v5-abilities-empty {
                border: 1px dashed rgba(176,140,98,.44);
                border-radius: 12px;
                background: rgba(255,255,255,.44);
                padding: 25px 18px;
                text-align: center;
                color: #80634e;
                font-size: 12px;
                line-height: 1.5;
            }

            .master-vault-v5-ability-drawer {
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.67);
                border-radius: 13px;
                background: rgba(255,255,255,.57);
            }

            .master-vault-v5-ability-drawer.is-open {
                border-color: rgba(107,29,20,.28);
                box-shadow: 0 8px 20px rgba(83,21,15,.045);
            }

            .master-vault-v5-ability-bar {
                display: grid;
                grid-template-columns: 34px minmax(0,1fr) auto;
                gap: 11px;
                align-items: center;
                width: 100%;
                min-height: 48px;
                border: 0;
                background: linear-gradient(
                    90deg,
                    rgba(239,233,220,.58),
                    rgba(255,255,255,.48)
                );
                padding: 9px 10px;
                text-align: left;
            }

            .master-vault-v5-ability-number {
                display: flex;
                width: 32px;
                height: 32px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.65);
                border-radius: 9px;
                background: rgba(255,253,248,.80);
                color: #8c6239;
                font-family: Georgia, serif;
                font-size: 14px;
                font-weight: 900;
            }

            .master-vault-v5-ability-title {
                min-width: 0;
                border: 0;
                background: transparent;
                padding: 0;
                text-align: left;
                cursor: pointer;
            }

            .master-vault-v5-ability-title strong {
                display: block;
                overflow: hidden;
                color: #53150f;
                font-size: 13px;
                font-weight: 900;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v5-ability-title span {
                display: block;
                margin-top: 2px;
                color: #80634e;
                font-size: 10px;
            }

            .master-vault-v5-ability-tools {
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .master-vault-v5-ability-tool {
                display: inline-flex;
                width: 34px;
                height: 34px;
                align-items: center;
                justify-content: center;
                border: 1px solid transparent;
                border-radius: 8px;
                background: transparent;
                color: #80634e;
                cursor: pointer;
            }

            .master-vault-v5-ability-tool:hover:not(:disabled) {
                border-color: rgba(205,187,159,.58);
                background: rgba(255,253,248,.72);
                color: #6b1d14;
            }

            .master-vault-v5-ability-tool:disabled {
                cursor: default;
                opacity: .28;
            }

            .master-vault-v5-ability-tool.is-danger:hover:not(:disabled) {
                color: #a3322a;
            }

            .master-vault-v5-ability-tool svg {
                width: 17px;
                height: 17px;
            }

            .master-vault-v5-ability-chevron {
                transition: transform .16s ease;
            }

            .master-vault-v5-ability-chevron.is-open {
                transform: rotate(180deg);
            }

            .master-vault-v5-ability-content {
                border-top: 1px solid rgba(205,187,159,.52);
                padding: 15px;
            }

            .master-vault-v5-ability-content-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.65fr) minmax(300px, .85fr);
                gap: 16px;
            }

            .master-vault-v5-ability-side {
                border: 1px solid rgba(216,199,171,.55);
                border-radius: 11px;
                background: rgba(247,241,232,.55);
                padding: 12px;
            }

            .master-vault-v5-ability-side-title {
                margin: 0;
                color: #53150f;
                font-size: 11px;
                font-weight: 900;
            }

            .master-vault-v5-ability-side-copy {
                margin: 3px 0 0;
                color: #80634e;
                font-size: 10px;
                line-height: 1.45;
            }

            @media (max-width: 1050px) {
                .master-vault-v5-category-grid {
                    grid-template-columns: repeat(2, minmax(0,1fr));
                }

                .master-vault-v5-ability-content-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 760px) {
                .master-vault-v4-modal {
                    width: 99vw;
                    max-height: 98dvh;
                    border-radius: 14px;
                }

                .master-vault-v4-head {
                    padding: 14px 17px;
                }

                .master-vault-v4-head h2 { font-size: 23px; }

                .master-vault-v4-scroll {
                    padding: 18px 16px 24px;
                }

                .master-vault-v4-identity {
                    grid-template-columns: 1fr;
                }

                .master-vault-v4-photo {
                    width: min(220px, 100%);
                }

                .master-vault-v5-abilities-head {
                    align-items: stretch;
                    flex-direction: column;
                }

                .master-vault-v5-add-ability {
                    width: 100%;
                }
            }

            @media (max-width: 540px) {
                .master-vault-v5-category-grid {
                    grid-template-columns: 1fr;
                }

                .master-vault-v5-ability-bar {
                    grid-template-columns: 30px minmax(0,1fr);
                }

                .master-vault-v5-ability-tools {
                    grid-column: 1 / -1;
                    justify-content: flex-end;
                    border-top: 1px solid rgba(205,187,159,.42);
                    padding-top: 7px;
                }
            }

        

            /* ==========================================================
               VAULT V6 — PROPRIEDADES / MAESTRIAS SELECIONADAS
            ========================================================== */

            .master-vault-v6-selected {
                display: grid;
                gap: 9px;
                margin-top: 14px;
                border-top: 1px solid rgba(216,199,171,.50);
                padding-top: 13px;
            }

            .master-vault-v6-selected-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .master-vault-v6-selected-head strong {
                color: #53150f;
                font-size: 11px;
                font-weight: 900;
            }

            .master-vault-v6-selected-head span {
                color: #8c6239;
                font-size: 10px;
                font-weight: 800;
            }

            .master-vault-v6-selected-list {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 9px;
            }

            .master-vault-v6-selected-card {
                display: grid;
                grid-template-columns: 34px minmax(0,1fr) auto;
                gap: 10px;
                align-items: start;
                min-height: 92px;
                border: 1px solid rgba(107,29,20,.18);
                border-radius: 12px;
                background:
                    linear-gradient(
                        145deg,
                        rgba(107,29,20,.035),
                        rgba(255,253,248,.74)
                    );
                padding: 11px;
            }

            .master-vault-v6-selected-mark {
                display: flex;
                width: 32px;
                height: 32px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(107,29,20,.18);
                border-radius: 9px;
                background: rgba(107,29,20,.065);
                color: #6b1d14;
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
            }

            .master-vault-v6-selected-copy {
                min-width: 0;
            }

            .master-vault-v6-selected-copy h5 {
                margin: 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 14px;
                font-weight: 900;
                line-height: 1.2;
            }

            .master-vault-v6-selected-copy p {
                margin: 5px 0 0;
                color: #684f3d;
                font-size: 11px;
                font-weight: 600;
                line-height: 1.58;
            }

            .master-vault-v6-selected-remove {
                display: inline-flex;
                width: 30px;
                height: 30px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 8px;
                background: transparent;
                color: #9b4b3b;
                cursor: pointer;
            }

            .master-vault-v6-selected-remove:hover {
                background: rgba(163,50,42,.07);
                color: #8b2c25;
            }

            .master-vault-v6-selected-remove svg {
                width: 15px;
                height: 15px;
            }

            @media (max-width: 960px) {
                .master-vault-v6-selected-list {
                    grid-template-columns: 1fr;
                }
            }

        

            /* ==========================================================
               VAULT V7 — DANO DA ARMA
            ========================================================== */

            .master-vault-v7-damage-types {
                margin-top: 14px;
                border-top: 1px solid rgba(216,199,171,.48);
                padding-top: 13px;
            }

            .master-vault-v7-damage-types-head,
            .master-vault-v7-extra-head {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 14px;
            }

            .master-vault-v7-damage-types-head strong,
            .master-vault-v7-extra-head strong {
                display: block;
                color: #53150f;
                font-size: 12px;
                font-weight: 900;
            }

            .master-vault-v7-damage-types-head span,
            .master-vault-v7-extra-head span {
                display: block;
                margin-top: 2px;
                color: #80634e;
                font-size: 10px;
                line-height: 1.45;
            }

            .master-vault-v7-damage-type-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 7px;
                margin-top: 9px;
            }

            .master-vault-v7-damage-type {
                min-height: 36px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 9px;
                background: rgba(255,255,255,.58);
                padding: 0 11px;
                color: #775a44;
                font-size: 10px;
                font-weight: 900;
                cursor: pointer;
                transition:
                    border-color .14s ease,
                    background .14s ease,
                    color .14s ease,
                    transform .14s ease;
            }

            .master-vault-v7-damage-type:hover {
                transform: translateY(-1px);
                border-color: rgba(140,98,57,.48);
                background: rgba(255,253,248,.92);
                color: #53150f;
            }

            .master-vault-v7-damage-type.is-active {
                border-color: rgba(107,29,20,.46);
                background: #6b1d14;
                color: #fffaf4;
            }

            .master-vault-v7-damage-type.is-custom.is-active {
                border-color: rgba(81,66,117,.46);
                background: #514275;
                color: #fff;
            }

            .master-vault-v7-extra {
                margin-top: 18px;
                border-top: 1px solid rgba(216,199,171,.48);
                padding-top: 15px;
            }

            .master-vault-v7-extra-empty {
                margin-top: 10px;
                border: 1px dashed rgba(176,140,98,.38);
                border-radius: 10px;
                background: rgba(255,255,255,.35);
                padding: 12px;
                color: #8a6d56;
                font-size: 11px;
                text-align: center;
            }

            .master-vault-v7-extra-list {
                display: grid;
                gap: 10px;
                margin-top: 11px;
            }

            .master-vault-v7-extra-card {
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 12px;
                background: rgba(255,253,248,.60);
                padding: 12px;
            }

            .master-vault-v7-extra-card-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 10px;
            }

            .master-vault-v7-extra-card-head strong {
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
            }

            .master-vault-v7-extra-fields {
                display: grid;
                grid-template-columns: 120px 140px;
                gap: 10px;
            }

            .master-vault-v7-extra-types {
                margin-top: 11px;
            }

            .master-vault-v7-extra-type-label {
                display: block;
                color: #53150f;
                font-size: 11px;
                font-weight: 900;
            }

            .master-vault-v7-complete-example {
                display: grid;
                grid-template-columns: 44px minmax(0,1fr);
                gap: 12px;
                align-items: center;
                margin-top: 18px;
                border: 1px solid rgba(107,29,20,.20);
                border-radius: 13px;
                background:
                    linear-gradient(
                        135deg,
                        rgba(107,29,20,.065),
                        rgba(255,253,248,.72)
                    );
                padding: 13px 14px;
            }

            .master-vault-v7-complete-example-mark {
                display: flex;
                width: 42px;
                height: 42px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(107,29,20,.18);
                border-radius: 11px;
                background: rgba(255,253,248,.78);
                color: #6b1d14;
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
            }

            .master-vault-v7-complete-example span {
                display: block;
                color: #8c6239;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .master-vault-v7-complete-example strong {
                display: block;
                margin-top: 3px;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 18px;
                font-weight: 900;
                line-height: 1.3;
            }

            @media (max-width: 640px) {
                .master-vault-v7-damage-types-head,
                .master-vault-v7-extra-head {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .master-vault-v7-extra-fields {
                    grid-template-columns: 1fr 1fr;
                }

                .master-vault-v7-complete-example {
                    grid-template-columns: 1fr;
                }
            }

        

            /* ==========================================================
               VAULT V8 — BIBLIOTECA LIMPA
               Menos informação, mais hierarquia e leitura.
            ========================================================== */

            .master-vault-v8-shell {
                width: min(1540px, calc(100% - 40px));
                margin: 0 auto;
                padding: 30px 0 46px;
            }

            .master-vault-v8-hero {
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 20px;
                background:
                    linear-gradient(
                        125deg,
                        rgba(255,253,248,.93) 0%,
                        rgba(246,240,230,.80) 58%,
                        rgba(239,233,220,.72) 100%
                    );
                box-shadow:
                    0 14px 34px rgba(83,21,15,.045);
                padding: 25px 27px;
            }

            .master-vault-v8-hero::after {
                content: '';
                position: absolute;
                right: -90px;
                top: -120px;
                width: 310px;
                height: 310px;
                border: 1px solid rgba(107,29,20,.055);
                border-radius: 50%;
                box-shadow:
                    0 0 0 34px rgba(107,29,20,.018),
                    0 0 0 70px rgba(107,29,20,.012);
                pointer-events: none;
            }

            .master-vault-v8-hero-inner {
                position: relative;
                z-index: 1;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 28px;
            }

            .master-vault-v8-kicker {
                margin: 0 0 6px;
                color: #956a43;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .16em;
                text-transform: uppercase;
            }

            .master-vault-v8-title {
                margin: 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: clamp(34px, 3.2vw, 48px);
                font-weight: 900;
                line-height: .98;
                letter-spacing: -.02em;
            }

            .master-vault-v8-copy {
                max-width: 690px;
                margin: 10px 0 0;
                color: #725842;
                font-size: 12px;
                line-height: 1.58;
            }

            .master-vault-v8-hero-actions {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                gap: 13px;
            }

            .master-vault-v8-count {
                min-width: 92px;
                border-right: 1px solid rgba(176,140,98,.28);
                padding-right: 14px;
                text-align: right;
            }

            .master-vault-v8-count strong {
                display: block;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 25px;
                line-height: 1;
            }

            .master-vault-v8-count span {
                display: block;
                margin-top: 4px;
                color: #916d50;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .master-vault-v8-create {
                display: inline-flex;
                min-height: 46px;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border: 0;
                border-radius: 12px;
                background: #6b1d14;
                padding: 0 17px;
                color: #fff9f1;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
                cursor: pointer;
                box-shadow:
                    0 7px 18px rgba(107,29,20,.12);
                transition:
                    transform .14s ease,
                    background .14s ease,
                    box-shadow .14s ease;
            }

            .master-vault-v8-create:hover {
                transform: translateY(-1px);
                background: #53150f;
                box-shadow:
                    0 10px 24px rgba(107,29,20,.16);
            }

            .master-vault-v8-toolbar {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 230px auto;
                gap: 10px;
                align-items: center;
                margin-top: 18px;
                border: 1px solid rgba(205,187,159,.54);
                border-radius: 16px;
                background: rgba(255,253,248,.72);
                padding: 9px;
                box-shadow:
                    0 6px 18px rgba(83,21,15,.025);
            }

            .master-vault-v8-search {
                position: relative;
                min-width: 0;
            }

            .master-vault-v8-search svg {
                position: absolute;
                left: 13px;
                top: 50%;
                width: 17px;
                height: 17px;
                transform: translateY(-50%);
                color: #9a795e;
                pointer-events: none;
            }

            .master-vault-v8-search input {
                width: 100%;
                height: 42px;
                border: 0;
                border-radius: 10px;
                background: rgba(255,255,255,.62);
                padding: 0 13px 0 41px;
                color: #53150f;
                font-size: 12px;
                font-weight: 700;
                outline: none;
                transition:
                    background .14s ease,
                    box-shadow .14s ease;
            }

            .master-vault-v8-search input::placeholder {
                color: #9e8774;
                font-weight: 600;
            }

            .master-vault-v8-search input:focus {
                background: #fff;
                box-shadow:
                    inset 0 0 0 1px rgba(107,29,20,.24),
                    0 0 0 3px rgba(107,29,20,.045);
            }

            .master-vault-v8-filter-wrap {
                position: relative;
            }

            .master-vault-v8-filter-wrap svg {
                position: absolute;
                left: 12px;
                top: 50%;
                width: 16px;
                height: 16px;
                transform: translateY(-50%);
                color: #8c6239;
                pointer-events: none;
            }

            .master-vault-v8-filter {
                width: 100%;
                height: 42px;
                appearance: none;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 10px;
                background:
                    linear-gradient(
                        180deg,
                        rgba(255,255,255,.82),
                        rgba(248,244,236,.82)
                    );
                padding: 0 35px 0 37px;
                color: #684733;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                outline: none;
                cursor: pointer;
            }

            .master-vault-v8-filter-wrap::after {
                content: '';
                position: absolute;
                right: 13px;
                top: 50%;
                width: 7px;
                height: 7px;
                border-right: 1.5px solid #8c6239;
                border-bottom: 1.5px solid #8c6239;
                transform:
                    translateY(-65%)
                    rotate(45deg);
                pointer-events: none;
            }

            .master-vault-v8-clear {
                min-height: 42px;
                border: 0;
                border-radius: 10px;
                background: transparent;
                padding: 0 11px;
                color: #8c6239;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v8-clear:hover {
                background: rgba(107,29,20,.05);
                color: #6b1d14;
            }

            .master-vault-v8-results {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin: 22px 2px 10px;
            }

            .master-vault-v8-results-title {
                display: flex;
                align-items: baseline;
                gap: 8px;
            }

            .master-vault-v8-results-title strong {
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 18px;
                font-weight: 900;
            }

            .master-vault-v8-results-title span {
                color: #94745b;
                font-size: 10px;
                font-weight: 800;
            }

            .master-vault-v8-grid {
                display: grid;
                grid-template-columns: repeat(
                    auto-fill,
                    minmax(310px, 1fr)
                );
                gap: 14px;
            }

            .master-vault-v8-card {
                position: relative;
                min-width: 0;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 16px;
                background:
                    linear-gradient(
                        145deg,
                        rgba(255,253,248,.96),
                        rgba(248,243,234,.92)
                    );
                box-shadow:
                    0 5px 16px rgba(83,21,15,.035);
                transition:
                    transform .15s ease,
                    box-shadow .15s ease,
                    border-color .15s ease;
            }

            .master-vault-v8-card:hover {
                transform: translateY(-2px);
                border-color: rgba(140,98,57,.42);
                box-shadow:
                    0 14px 30px rgba(83,21,15,.07);
            }

            .master-vault-v8-card::before {
                content: '';
                position: absolute;
                inset: 0 auto 0 0;
                width: 3px;
                background: #9b7b62;
            }

            .master-vault-v8-card[data-nature="wonderful"]::before {
                background: #6b1d14;
            }

            .master-vault-v8-card[data-nature="technological"]::before {
                background: #486a83;
            }

            .master-vault-v8-card-top {
                display: grid;
                grid-template-columns: 88px minmax(0, 1fr);
                gap: 14px;
                padding: 15px 15px 13px 18px;
            }

            .master-vault-v8-thumb {
                display: flex;
                width: 88px;
                height: 88px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 13px;
                background:
                    radial-gradient(
                        circle at 50% 38%,
                        rgba(205,187,159,.24),
                        rgba(239,233,220,.54)
                    );
                color: #987a61;
            }

            .master-vault-v8-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .master-vault-v8-card-copy {
                min-width: 0;
                padding-right: 31px;
            }

            .master-vault-v8-type {
                display: block;
                margin-bottom: 4px;
                color: #976f50;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .10em;
                text-transform: uppercase;
            }

            .master-vault-v8-card h2 {
                overflow: hidden;
                margin: 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 19px;
                font-weight: 900;
                line-height: 1.15;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v8-meta {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 5px 8px;
                margin-top: 6px;
                color: #846650;
                font-size: 9px;
                font-weight: 800;
            }

            .master-vault-v8-meta-separator {
                width: 3px;
                height: 3px;
                border-radius: 50%;
                background: rgba(140,98,57,.46);
            }

            .master-vault-v8-description {
                display: -webkit-box;
                overflow: hidden;
                margin: 8px 0 0;
                color: #745b48;
                font-size: 11px;
                line-height: 1.48;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
            }

            .master-vault-v8-favorite {
                position: absolute;
                right: 11px;
                top: 11px;
                display: inline-flex;
                width: 32px;
                height: 32px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 9px;
                background: rgba(255,253,248,.78);
                color: #a4876e;
                cursor: pointer;
                transition:
                    background .14s ease,
                    color .14s ease,
                    transform .14s ease;
            }

            .master-vault-v8-favorite:hover {
                transform: scale(1.04);
                background: #fffdf8;
                color: #7c5129;
            }

            .master-vault-v8-favorite.is-active {
                background: rgba(181,130,68,.11);
                color: #8a571f;
            }

            .master-vault-v8-card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                border-top: 1px solid rgba(205,187,159,.46);
                padding: 9px 12px 10px 18px;
            }

            .master-vault-v8-edit {
                display: inline-flex;
                min-height: 33px;
                align-items: center;
                gap: 6px;
                border: 0;
                border-radius: 8px;
                background: transparent;
                padding: 0 8px;
                color: #6b1d14;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v8-edit:hover {
                background: rgba(107,29,20,.055);
            }

            .master-vault-v8-edit svg {
                width: 14px;
                height: 14px;
            }

            .master-vault-v8-card-secondary {
                display: inline-flex;
                width: 32px;
                height: 32px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 8px;
                background: transparent;
                color: #8e6c52;
                cursor: pointer;
            }

            .master-vault-v8-card-secondary:hover {
                background: rgba(107,29,20,.05);
                color: #6b1d14;
            }

            .master-vault-v8-card-secondary svg {
                width: 15px;
                height: 15px;
            }

            .master-vault-v8-empty {
                margin-top: 18px;
                border: 1px dashed rgba(176,140,98,.45);
                border-radius: 18px;
                background: rgba(255,253,248,.46);
                padding: 70px 24px;
                text-align: center;
            }

            .master-vault-v8-empty-mark {
                display: flex;
                width: 52px;
                height: 52px;
                margin: 0 auto 13px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 14px;
                background: rgba(255,253,248,.72);
                color: #8c6239;
            }

            .master-vault-v8-empty-mark svg {
                width: 25px;
                height: 25px;
            }

            .master-vault-v8-empty h3 {
                margin: 0;
                font-family: Georgia, serif;
                color: #53150f;
                font-size: 21px;
                font-weight: 900;
            }

            .master-vault-v8-empty p {
                max-width: 480px;
                margin: 7px auto 0;
                color: #80634e;
                font-size: 11px;
                line-height: 1.55;
            }

            @media (max-width: 900px) {
                .master-vault-v8-shell {
                    width: min(100% - 24px, 1540px);
                }

                .master-vault-v8-hero-inner {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .master-vault-v8-hero-actions {
                    width: 100%;
                    justify-content: space-between;
                }

                .master-vault-v8-count {
                    border-right: 0;
                    border-left: 1px solid rgba(176,140,98,.28);
                    padding-right: 0;
                    padding-left: 14px;
                    text-align: left;
                }

                .master-vault-v8-toolbar {
                    grid-template-columns: minmax(0,1fr) 205px;
                }

                .master-vault-v8-clear {
                    grid-column: 1 / -1;
                    min-height: 34px;
                }
            }

            @media (max-width: 620px) {
                .master-vault-v8-shell {
                    width: min(100% - 16px, 1540px);
                    padding-top: 14px;
                }

                .master-vault-v8-hero {
                    border-radius: 15px;
                    padding: 20px 17px;
                }

                .master-vault-v8-title {
                    font-size: 34px;
                }

                .master-vault-v8-copy {
                    font-size: 11px;
                }

                .master-vault-v8-hero-actions {
                    align-items: stretch;
                    flex-direction: column-reverse;
                }

                .master-vault-v8-count {
                    display: flex;
                    min-width: 0;
                    align-items: baseline;
                    gap: 7px;
                    border-left: 0;
                    padding-left: 0;
                    text-align: left;
                }

                .master-vault-v8-count strong {
                    font-size: 20px;
                }

                .master-vault-v8-create {
                    width: 100%;
                }

                .master-vault-v8-toolbar {
                    grid-template-columns: 1fr;
                }

                .master-vault-v8-clear {
                    grid-column: auto;
                }

                .master-vault-v8-grid {
                    grid-template-columns: 1fr;
                }
            }

        

            /* ==========================================================
               VAULT V9 — BIBLIOTECA COMPACTA + VISUALIZAÇÃO
            ========================================================== */

            .master-vault-v9-shell {
                width: min(1580px, calc(100% - 32px));
                margin: 0 auto;
                padding: 16px 0 38px;
            }

            .master-vault-v9-commandbar {
                position: sticky;
                top: 0;
                z-index: 28;
                display: grid;
                grid-template-columns:
                    auto
                    minmax(320px, 1fr)
                    190px
                    auto;
                gap: 10px;
                align-items: center;
                border: 1px solid rgba(205,187,159,.56);
                border-radius: 16px;
                background: rgba(239,233,220,.96);
                box-shadow: 0 6px 18px rgba(83,21,15,.045);
                padding: 9px 10px;
                backdrop-filter: blur(12px);
            }

            .master-vault-v9-title {
                display: flex;
                min-width: 208px;
                align-items: center;
                gap: 10px;
                padding: 0 7px 0 0;
            }

            .master-vault-v12-brand-icon {
                display: inline-flex;
                width: 38px;
                height: 38px;
                flex: 0 0 38px;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                background: #6b1d14;
                color: #f4f1e8;
                box-shadow: 0 3px 9px rgba(83,21,15,.12);
            }

            .master-vault-v12-brand-icon svg {
                width: 20px;
                height: 20px;
            }

            .master-vault-v12-brand-copy {
                min-width: 0;
                line-height: 1.08;
            }

            .master-vault-v12-brand-copy h1 {
                margin: 0;
                color: #6b1d14;
                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: 900;
                white-space: nowrap;
            }

            .master-vault-v12-brand-copy p {
                margin: 4px 0 0;
                color: rgba(140,98,57,.76);
                font-size: 9px;
                font-style: italic;
                white-space: nowrap;
            }

            .master-vault-v9-title-count {
                display: none;
            }

            .master-vault-v9-search {
                position: relative;
                min-width: 0;
            }

            .master-vault-v9-search svg {
                position: absolute;
                left: 12px;
                top: 50%;
                width: 16px;
                height: 16px;
                transform: translateY(-50%);
                color: #9b7a61;
                pointer-events: none;
            }

            .master-vault-v9-search input {
                width: 100%;
                height: 40px;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 12px;
                background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(252,248,241,.94));
                padding: 0 14px 0 41px;
                color: #53150f;
                font-size: 12px;
                font-weight: 700;
                outline: none;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
            }

            .master-vault-v9-search input:focus {
                border-color: rgba(107,29,20,.30);
                background: #fff;
                box-shadow: 0 0 0 3px rgba(107,29,20,.045);
            }

            .master-vault-v9-filter {
                position: relative;
            }

            .master-vault-v9-filter select {
                width: 100%;
                height: 40px;
                appearance: none;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 12px;
                background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(252,248,241,.94));
                padding: 0 38px 0 14px;
                color: #6b4a32;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
                outline: none;
                cursor: pointer;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
            }

            .master-vault-v9-filter::after {
                content: '';
                position: absolute;
                right: 16px;
                top: 50%;
                width: 8px;
                height: 8px;
                border-right: 1.7px solid #8c6239;
                border-bottom: 1.7px solid #8c6239;
                transform: translateY(-65%) rotate(45deg);
                pointer-events: none;
            }

            .master-vault-v9-create {
                display: inline-flex;
                height: 42px;
                align-items: center;
                justify-content: center;
                gap: 7px;
                border: 0;
                border-radius: 10px;
                background: #6b1d14;
                padding: 0 15px;
                color: #fff9f1;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                white-space: nowrap;
                cursor: pointer;
                box-shadow: 0 6px 16px rgba(107,29,20,.11);
                transition:
                    transform .14s ease,
                    background .14s ease;
            }

            .master-vault-v9-create:hover {
                transform: translateY(-1px);
                background: #53150f;
            }

            .master-vault-v9-results {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-top: 8px;
                padding: 6px 2px 2px;
            }

            .master-vault-v9-results > div {
                display: flex;
                align-items: baseline;
                gap: 10px;
                flex-wrap: wrap;
            }

            .master-vault-v9-results strong {
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 28px;
                font-weight: 900;
                line-height: 1.05;
            }

            .master-vault-v9-results span {
                display: inline-flex;
                min-height: 30px;
                align-items: center;
                justify-content: center;
                margin-left: 0;
                border: 1px solid rgba(205,187,159,.8);
                border-radius: 999px;
                background: rgba(255,249,241,.9);
                padding: 0 12px;
                color: #94745b;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            .master-vault-v9-clear {
                display: inline-flex;
                min-height: 34px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 999px;
                background: rgba(255,251,244,.88);
                padding: 0 12px;
                color: #8c6239;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v9-clear:hover {
                color: #6b1d14;
                background: rgba(107,29,20,.05);
                text-decoration: none;
            }

            .master-vault-v9-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
                gap: 16px;
            }

            .master-vault-v9-card {
                position: relative;
                min-width: 0;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.56);
                border-radius: 14px;
                background:
                    linear-gradient(
                        145deg,
                        rgba(255,253,248,.96),
                        rgba(248,243,234,.91)
                    );
                box-shadow: 0 4px 12px rgba(83,21,15,.028);
                cursor: pointer;
                transition:
                    transform .15s ease,
                    box-shadow .15s ease,
                    border-color .15s ease;
            }

            .master-vault-v9-card:hover,
            .master-vault-v9-card:focus-visible {
                transform: translateY(-2px);
                border-color: rgba(107,29,20,.30);
                box-shadow: 0 12px 25px rgba(83,21,15,.065);
                outline: none;
            }

            .master-vault-v9-card::before {
                content: '';
                position: absolute;
                inset: 0 auto 0 0;
                width: 3px;
                background: #9b7b62;
            }

            .master-vault-v9-card[data-nature="wonderful"]::before {
                background: #6b1d14;
            }

            .master-vault-v9-card[data-nature="technological"]::before {
                background: #486a83;
            }

            .master-vault-v9-card-top {
                display: grid;
                grid-template-columns: 74px minmax(0, 1fr);
                gap: 12px;
                min-height: 104px;
                padding: 12px 12px 10px 15px;
            }

            .master-vault-v9-thumb {
                display: flex;
                width: 74px;
                height: 74px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 11px;
                background:
                    radial-gradient(
                        circle at 50% 38%,
                        rgba(205,187,159,.22),
                        rgba(239,233,220,.52)
                    );
                color: #987a61;
            }

            .master-vault-v9-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .master-vault-v9-card-copy {
                min-width: 0;
                padding-right: 27px;
            }

            .master-vault-v9-type {
                display: block;
                margin-bottom: 3px;
                color: #976f50;
                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .09em;
                text-transform: uppercase;
            }

            .master-vault-v9-card h2 {
                overflow: hidden;
                margin: 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
                line-height: 1.15;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v9-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 4px 7px;
                margin-top: 5px;
                color: #846650;
                font-size: 8px;
                font-weight: 800;
            }

            .master-vault-v9-description {
                overflow: hidden;
                margin: 7px 0 0;
                color: #745b48;
                font-size: 10px;
                line-height: 1.42;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v9-favorite {
                position: absolute;
                right: 9px;
                top: 9px;
                display: inline-flex;
                width: 29px;
                height: 29px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 8px;
                background: rgba(255,253,248,.82);
                color: #a4876e;
                cursor: pointer;
                z-index: 2;
            }

            .master-vault-v9-favorite:hover {
                background: #fffdf8;
                color: #7c5129;
            }

            .master-vault-v9-favorite.is-active {
                color: #8a571f;
                background: rgba(181,130,68,.11);
            }

            .master-vault-v9-card-footer {
                display: flex;
                min-height: 39px;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                border-top: 1px solid rgba(205,187,159,.43);
                padding: 5px 8px 6px 15px;
            }

            .master-vault-v9-view {
                display: inline-flex;
                min-height: 29px;
                align-items: center;
                gap: 6px;
                border: 0;
                border-radius: 8px;
                background: transparent;
                padding: 0 7px;
                color: #6b1d14;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v9-view:hover {
                background: rgba(107,29,20,.055);
            }

            .master-vault-v9-view svg {
                width: 15px;
                height: 15px;
            }

            .master-vault-v9-card-actions {
                display: flex;
                align-items: center;
                gap: 2px;
            }

            .master-vault-v9-icon-action {
                display: inline-flex;
                width: 30px;
                height: 30px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 8px;
                background: transparent;
                color: #8b6a50;
                cursor: pointer;
            }

            .master-vault-v9-icon-action:hover {
                background: rgba(107,29,20,.05);
                color: #6b1d14;
            }

            .master-vault-v9-icon-action svg {
                width: 15px;
                height: 15px;
            }

            /* ----------------------------------------------------------
               Preview do item
            ---------------------------------------------------------- */

            .master-vault-v9-preview-layer {
                position: fixed;
                inset: 0;
                z-index: 230;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }

            .master-vault-v9-preview-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(18,12,9,.70);
                backdrop-filter: blur(7px);
            }

            .master-vault-v9-preview {
                position: relative;
                z-index: 1;
                display: flex;
                width: min(1180px, 96vw);
                max-height: 94dvh;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 18px;
                background:
                    linear-gradient(
                        160deg,
                        #fbf8f1 0%,
                        #f6f0e5 100%
                    );
                box-shadow: 0 30px 85px rgba(23,13,9,.34);
            }

            .master-vault-v9-preview-head {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                border-bottom: 1px solid rgba(205,187,159,.56);
                background: rgba(239,233,220,.62);
                padding: 13px 16px;
            }

            .master-vault-v9-preview-head-copy {
                min-width: 0;
            }

            .master-vault-v9-preview-kicker {
                color: #956b48;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .master-vault-v9-preview-head h2 {
                overflow: hidden;
                margin: 2px 0 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 23px;
                font-weight: 900;
                line-height: 1.05;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v9-preview-head-actions {
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .master-vault-v9-preview-nav,
            .master-vault-v9-preview-close {
                display: inline-flex;
                width: 36px;
                height: 36px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 9px;
                background: transparent;
                color: #80634e;
                cursor: pointer;
            }

            .master-vault-v9-preview-nav:hover:not(:disabled),
            .master-vault-v9-preview-close:hover {
                background: rgba(107,29,20,.06);
                color: #6b1d14;
            }

            .master-vault-v9-preview-nav:disabled {
                opacity: .26;
                cursor: default;
            }

            .master-vault-v9-preview-nav svg,
            .master-vault-v9-preview-close svg {
                width: 18px;
                height: 18px;
            }

            .master-vault-v9-preview-body {
                min-height: 0;
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 20px;
            }

            .master-vault-v9-preview-hero {
                display: grid;
                grid-template-columns: 190px minmax(0, 1fr);
                gap: 24px;
            }

            .master-vault-v9-preview-image {
                display: flex;
                width: 190px;
                height: 190px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 15px;
                background:
                    radial-gradient(
                        circle at 50% 40%,
                        rgba(205,187,159,.24),
                        rgba(239,233,220,.56)
                    );
                color: #95765d;
            }

            .master-vault-v9-preview-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .master-vault-v9-preview-summary {
                min-width: 0;
            }

            .master-vault-v9-preview-titleline {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 7px;
            }

            .master-vault-v9-preview-badge {
                display: inline-flex;
                min-height: 24px;
                align-items: center;
                border: 1px solid rgba(205,187,159,.64);
                border-radius: 999px;
                background: rgba(255,255,255,.55);
                padding: 0 8px;
                color: #806149;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
            }

            .master-vault-v9-preview-name {
                margin: 12px 0 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: clamp(28px, 3vw, 39px);
                font-weight: 900;
                line-height: 1;
            }

            .master-vault-v9-preview-description {
                max-width: 760px;
                margin: 13px 0 0;
                color: #674f3d;
                font-size: 13px;
                line-height: 1.7;
                white-space: pre-line;
            }

            .master-vault-v9-preview-statrow {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 15px;
            }

            .master-vault-v9-preview-stat {
                min-width: 116px;
                border: 1px solid rgba(205,187,159,.54);
                border-radius: 10px;
                background: rgba(255,253,248,.58);
                padding: 8px 10px;
            }

            .master-vault-v9-preview-stat span {
                display: block;
                color: #97765d;
                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .master-vault-v9-preview-stat strong {
                display: block;
                margin-top: 2px;
                color: #53150f;
                font-size: 12px;
                font-weight: 900;
            }

            .master-vault-v9-preview-section {
                margin-top: 20px;
                border-top: 1px solid rgba(205,187,159,.50);
                padding-top: 16px;
            }

            .master-vault-v9-preview-section-head {
                display: flex;
                align-items: baseline;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 10px;
            }

            .master-vault-v9-preview-section h3 {
                margin: 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
            }

            .master-vault-v9-preview-section-head span {
                color: #95755d;
                font-size: 9px;
                font-weight: 800;
            }

            .master-vault-v9-preview-grid {
                display: grid;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
                gap: 9px;
            }

            .master-vault-v9-preview-rule {
                border: 1px solid rgba(205,187,159,.54);
                border-radius: 11px;
                background: rgba(255,253,248,.54);
                padding: 11px;
            }

            .master-vault-v9-preview-rule strong {
                display: block;
                color: #53150f;
                font-size: 11px;
                font-weight: 900;
            }

            .master-vault-v9-preview-rule p {
                margin: 5px 0 0;
                color: #6f5745;
                font-size: 10.5px;
                line-height: 1.55;
                white-space: pre-line;
            }

            .master-vault-v9-preview-feature {
                border: 1px solid rgba(205,187,159,.56);
                border-radius: 12px;
                background: rgba(255,253,248,.57);
                padding: 12px;
            }

            .master-vault-v9-preview-feature + .master-vault-v9-preview-feature {
                margin-top: 8px;
            }

            .master-vault-v9-preview-feature-head {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            .master-vault-v9-preview-feature h4 {
                margin: 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 14px;
                font-weight: 900;
            }

            .master-vault-v9-preview-feature p {
                margin: 6px 0 0;
                color: #654e3d;
                font-size: 11px;
                line-height: 1.65;
                white-space: pre-line;
            }

            .master-vault-v9-preview-usage {
                display: inline-flex;
                min-height: 22px;
                align-items: center;
                border-radius: 999px;
                background: rgba(107,29,20,.07);
                padding: 0 8px;
                color: #6b1d14;
                font-size: 8px;
                font-weight: 900;
            }

            .master-vault-v9-preview-secret {
                margin-top: 20px;
                border: 1px solid rgba(126,69,49,.23);
                border-radius: 12px;
                background: rgba(107,29,20,.035);
                padding: 12px;
            }

            .master-vault-v9-preview-secret-label {
                color: #984a39;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .10em;
                text-transform: uppercase;
            }

            .master-vault-v9-preview-secret h3 {
                margin: 2px 0 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
            }

            .master-vault-v9-preview-secret p {
                margin: 7px 0 0;
                color: #654e3d;
                font-size: 11px;
                line-height: 1.6;
                white-space: pre-line;
            }

            .master-vault-v9-preview-foot {
                display: flex;
                flex: 0 0 auto;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border-top: 1px solid rgba(205,187,159,.56);
                background: rgba(239,233,220,.62);
                padding: 10px 14px;
            }

            .master-vault-v9-preview-position {
                color: #8d7059;
                font-size: 9px;
                font-weight: 800;
            }

            .master-vault-v9-preview-foot-actions {
                display: flex;
                align-items: center;
                gap: 7px;
            }

            .master-vault-v9-preview-edit {
                display: inline-flex;
                min-height: 38px;
                align-items: center;
                justify-content: center;
                gap: 7px;
                border: 0;
                border-radius: 9px;
                background: #6b1d14;
                padding: 0 13px;
                color: #fff9f1;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v9-preview-dismiss {
                display: inline-flex;
                min-height: 38px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.66);
                border-radius: 9px;
                background: rgba(255,255,255,.58);
                padding: 0 12px;
                color: #735640;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            @media (max-width: 1000px) {
                .master-vault-v9-commandbar {
                    grid-template-columns:
                        auto
                        minmax(230px, 1fr)
                        auto;
                }

                .master-vault-v9-filter {
                    grid-column: 2;
                    grid-row: 2;
                }

                .master-vault-v9-create {
                    grid-column: 3;
                    grid-row: 1 / span 2;
                    align-self: stretch;
                    height: auto;
                }
            }

            @media (max-width: 720px) {
                .master-vault-v9-shell {
                    width: min(100% - 16px, 1580px);
                    padding-top: 8px;
                }

                .master-vault-v9-commandbar {
                    grid-template-columns: 1fr;
                }

                .master-vault-v9-title,
                .master-vault-v9-search,
                .master-vault-v9-filter,
                .master-vault-v9-create {
                    grid-column: auto;
                    grid-row: auto;
                }

                .master-vault-v9-create {
                    height: 42px;
                }

                .master-vault-v9-preview-hero {
                    grid-template-columns: 1fr;
                }

                .master-vault-v9-preview-image {
                    width: min(190px, 100%);
                    height: auto;
                    aspect-ratio: 1 / 1;
                }

                .master-vault-v9-preview-grid {
                    grid-template-columns: 1fr;
                }
            }

        

            /* ==========================================================
