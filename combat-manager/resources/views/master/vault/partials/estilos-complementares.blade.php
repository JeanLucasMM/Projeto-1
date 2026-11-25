               VAULT V10 — CARD PADRONIZADO + MENU DE AÇÕES
            ========================================================== */

            .master-vault-v9-grid {
                grid-template-columns:
                    repeat(auto-fill, minmax(360px, 1fr));
                gap: 13px;
            }

            .master-vault-v10-card {
                position: relative;
                min-width: 0;
                overflow: visible;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 20px;
                background: linear-gradient(180deg, rgba(255,253,248,.98), rgba(250,246,238,.95));
                box-shadow: 0 10px 26px rgba(83,21,15,.045);
                cursor: pointer;
                transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            }

            .master-vault-v10-card:hover,
            .master-vault-v10-card:focus-visible {
                transform: translateY(-3px);
                border-color: rgba(107,29,20,.34);
                box-shadow: 0 18px 34px rgba(83,21,15,.08);
                outline: none;
            }

            .master-vault-v10-card::before {
                content: '';
                position: absolute;
                z-index: 1;
                inset: 0 auto 0 0;
                width: 5px;
                border-radius: 20px 0 0 20px;
                background: #9b7b62;
            }

            .master-vault-v10-card[data-nature="wonderful"]::before {
                background: #6b1d14;
            }

            .master-vault-v10-card[data-nature="technological"]::before {
                background: #486a83;
            }

            .master-vault-v10-card-main {
                display: grid;
                grid-template-columns: 92px minmax(0,1fr) auto;
                gap: 15px;
                align-items: start;
                padding: 16px 16px 13px 19px;
            }

            .master-vault-v10-thumb {
                display: flex;
                width: 92px;
                height: 92px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.64);
                border-radius: 16px;
                background: linear-gradient(180deg, rgba(251,247,240,.95), rgba(243,236,225,.95));
                color: #987a61;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
            }

            .master-vault-v10-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .master-vault-v10-identity {
                min-width: 0;
            }

            .master-vault-v10-name {
                overflow: hidden;
                margin: 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
                line-height: 1.2;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v10-nature {
                margin: 6px 0 0;
                color: #775b46;
                font-size: 10.5px;
                font-weight: 800;
                line-height: 1.35;
            }

            .master-vault-v10-category {
                margin: 4px 0 0;
                color: #936b4d;
                font-size: 10px;
                font-weight: 900;
                line-height: 1.35;
            }

            .master-vault-v10-top-actions {
                display: flex;
                align-items: center;
                gap: 2px;
            }

            .master-vault-v10-top-button {
                display: inline-flex;
                width: 34px;
                height: 34px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 10px;
                background: transparent;
                color: #9b7b62;
                cursor: pointer;
            }

            .master-vault-v10-top-button:hover {
                background: rgba(107,29,20,.055);
                color: #6b1d14;
            }

            .master-vault-v10-top-button.is-active {
                color: #8a571f;
                background: rgba(181,130,68,.10);
            }

            .master-vault-v10-top-button svg {
                width: 17px;
                height: 17px;
            }

            .master-vault-v10-menu-wrap {
                position: relative;
            }

            .master-vault-v10-menu {
                position: absolute;
                z-index: 35;
                right: 0;
                top: calc(100% + 7px);
                width: 174px;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 11px;
                background: #fffdf8;
                box-shadow: 0 15px 32px rgba(50,25,13,.16);
                padding: 5px;
                cursor: default;
            }

            .master-vault-v10-menu button {
                display: flex;
                width: 100%;
                min-height: 36px;
                align-items: center;
                gap: 9px;
                border: 0;
                border-radius: 8px;
                background: transparent;
                padding: 0 9px;
                color: #654b38;
                font-size: 9px;
                font-weight: 900;
                text-align: left;
                cursor: pointer;
            }

            .master-vault-v10-menu button:hover {
                background: rgba(107,29,20,.055);
                color: #53150f;
            }

            .master-vault-v10-menu button.is-danger {
                color: #a13b32;
            }

            .master-vault-v10-menu button.is-danger:hover {
                background: rgba(161,59,50,.07);
                color: #8f2c25;
            }

            .master-vault-v10-menu svg {
                width: 15px;
                height: 15px;
                flex: 0 0 auto;
            }

            .master-vault-v10-section {
                border-top: 1px solid rgba(205,187,159,.43);
                padding: 12px 16px 13px 19px;
            }

            .master-vault-v10-section-label {
                display: block;
                margin-bottom: 6px;
                color: #99714f;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .11em;
                text-transform: uppercase;
            }

            .master-vault-v10-description {
                display: -webkit-box;
                overflow: hidden;
                margin: 0;
                color: #66503f;
                font-size: 11px;
                font-weight: 600;
                line-height: 1.6;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;
            }

            .master-vault-v10-empty-copy {
                margin: 0;
                color: #9a806d;
                font-size: 10px;
                font-style: italic;
                line-height: 1.45;
            }

            .master-vault-v10-abilities {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .master-vault-v10-ability {
                display: inline-flex;
                min-height: 25px;
                align-items: center;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 999px;
                background: rgba(255,255,255,.62);
                padding: 0 9px;
                color: #684d38;
                font-size: 8.8px;
                font-weight: 900;
            }

            .master-vault-v10-ability-more {
                background: rgba(107,29,20,.05);
                color: #6b1d14;
            }


            /* V12 — cards internos mais simples */
            .master-vault-v10-top-view {
                display: none !important;
            }

            .master-vault-v10-card-main {
                grid-template-columns: 78px minmax(0,1fr) auto;
                gap: 13px;
                padding: 14px 14px 10px 18px;
            }

            .master-vault-v10-thumb {
                width: 78px;
                height: 78px;
                border-radius: 14px;
                background: rgba(248,243,234,.78);
            }

            .master-vault-v10-thumb img {
                object-fit: contain;
                padding: 4px;
            }

            .master-vault-v10-card > .master-vault-v10-section {
                border-top: 0;
                padding: 0 16px 12px 18px;
            }

            .master-vault-v10-section-label {
                display: none;
            }

            .master-vault-v10-description {
                color: #705744;
                font-size: 10.5px;
                line-height: 1.5;
                -webkit-line-clamp: 2;
            }

            .master-vault-v10-card > .master-vault-v10-section:last-of-type {
                border-top: 1px solid rgba(205,187,159,.32);
                padding-top: 10px;
            }

            .master-vault-v10-abilities {
                gap: 6px;
            }

            .master-vault-v10-ability {
                min-height: 23px;
                padding: 0 8px;
                background: rgba(255,255,255,.50);
                font-size: 8.2px;
            }

            /* ----------------------------------------------------------
               Confirmação para Copiar / Excluir
            ---------------------------------------------------------- */

            .master-vault-v10-confirm-layer {
                position: fixed;
                inset: 0;
                z-index: 280;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }

            .master-vault-v10-confirm-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(18,12,9,.67);
                backdrop-filter: blur(6px);
            }

            .master-vault-v10-confirm {
                position: relative;
                z-index: 1;
                width: min(440px, 94vw);
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.74);
                border-radius: 16px;
                background:
                    linear-gradient(
                        160deg,
                        #fffdf8,
                        #f6efe4
                    );
                box-shadow: 0 28px 75px rgba(30,16,10,.30);
            }

            .master-vault-v10-confirm-body {
                padding: 21px;
            }

            .master-vault-v10-confirm-mark {
                display: flex;
                width: 42px;
                height: 42px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(107,29,20,.16);
                border-radius: 11px;
                background: rgba(107,29,20,.055);
                color: #6b1d14;
            }

            .master-vault-v10-confirm-mark svg {
                width: 21px;
                height: 21px;
            }

            .master-vault-v10-confirm h3 {
                margin: 13px 0 0;
                color: #53150f;
                font-family: Georgia, serif;
                font-size: 21px;
                font-weight: 900;
            }

            .master-vault-v10-confirm p {
                margin: 7px 0 0;
                color: #705743;
                font-size: 11px;
                line-height: 1.6;
            }

            .master-vault-v10-confirm-foot {
                display: flex;
                justify-content: flex-end;
                gap: 7px;
                border-top: 1px solid rgba(205,187,159,.52);
                background: rgba(239,233,220,.58);
                padding: 10px 12px;
            }

            .master-vault-v10-confirm-cancel,
            .master-vault-v10-confirm-ok {
                min-height: 38px;
                border-radius: 9px;
                padding: 0 13px;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v10-confirm-cancel {
                border: 1px solid rgba(205,187,159,.64);
                background: rgba(255,255,255,.58);
                color: #715540;
            }

            .master-vault-v10-confirm-ok {
                border: 0;
                background: #6b1d14;
                color: #fff9f1;
            }

            .master-vault-v10-confirm-ok.is-danger {
                background: #8f2f28;
            }

            .master-vault-v10-confirm-cancel:disabled,
            .master-vault-v10-confirm-ok:disabled {
                cursor: wait;
                opacity: .56;
            }

            @media (max-width: 720px) {
                .master-vault-v9-grid {
                    grid-template-columns: 1fr;
                }

                .master-vault-v10-card-main {
                    grid-template-columns: 74px minmax(0,1fr) auto;
                }

                .master-vault-v10-thumb {
                    width: 74px;
                    height: 74px;
                }
            }

        

            /* ==========================================================
               VAULT V10.1 — AÇÕES SUPERIORES COMPACTAS
            ========================================================== */

            .master-vault-v10-top-view {
                border: 1px solid rgba(107,29,20,.10);
                background: rgba(107,29,20,.035);
                color: #6b1d14;
            }

            .master-vault-v10-top-view:hover {
                border-color: rgba(107,29,20,.22);
                background: rgba(107,29,20,.075);
            }

            /* Sem footer inferior: Habilidades encerra o card. */
            .master-vault-v10-card > .master-vault-v10-section:last-of-type {
                padding-bottom: 13px;
            }

        

            /* ==========================================================
               VAULT V10.2 — SEM RODAPÉ NOS CARDS
               Remove definitivamente o antigo "Visualizar item / Maravilhoso".
            ========================================================== */

            .master-vault-v8-card-footer,
            .master-vault-v9-card-footer,
            .master-vault-v10-footer {
                display: none !important;
            }

            .master-vault-v10-card {
                overflow: visible;
            }

            .master-vault-v10-card > .master-vault-v10-section:last-of-type {
                border-radius: 0 0 14px 14px;
                padding-bottom: 14px;
            }

        

            /* ==========================================================
               VAULT V10.4 — REMOÇÃO VISUAL SEGURA DO OLHO
               Mantém integralmente a lógica/markup da V10.2.
            ========================================================== */

            .master-vault-v10-top-view {
                display: none !important;
            }

        

            /* ==========================================================
               VAULT V10.5 — DISTÂNCIA / ALCANCE DA ARMA
            ========================================================== */

            .master-vault-v10-5-range-panel {
                margin-top: 13px;
                border: 1px solid rgba(107,29,20,.16);
                border-radius: 12px;
                background:
                    linear-gradient(
                        145deg,
                        rgba(107,29,20,.035),
                        rgba(255,253,248,.66)
                    );
                padding: 12px;
            }

            .master-vault-v10-5-range-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
            }

            .master-vault-v10-5-range-head strong {
                display: block;
                color: #53150f;
                font-size: 12px;
                font-weight: 900;
            }

            .master-vault-v10-5-range-head span {
                display: block;
                margin-top: 2px;
                color: #7c604a;
                font-size: 10px;
                line-height: 1.45;
            }

            .master-vault-v10-5-range-preview {
                flex: 0 0 auto;
                min-height: 27px;
                border: 1px solid rgba(107,29,20,.17);
                border-radius: 999px;
                background: rgba(107,29,20,.065);
                padding: 5px 10px;
                color: #6b1d14 !important;
                font-family: Georgia, serif;
                font-size: 11px !important;
                font-weight: 900;
                white-space: nowrap;
            }

            .master-vault-v10-5-range-grid {
                display: grid;
                grid-template-columns:
                    minmax(140px, 1fr)
                    minmax(140px, 1fr)
                    150px;
                gap: 10px;
                margin-top: 11px;
            }

            .master-vault-v10-5-range-grid.is-two {
                grid-template-columns:
                    repeat(2, minmax(160px, 1fr));
                max-width: 620px;
            }

            .master-vault-v10-5-range-grid.is-reach {
                grid-template-columns:
                    minmax(180px, 360px);
                max-width: 360px;
            }

            @media (max-width: 680px) {
                .master-vault-v10-5-range-head {
                    flex-direction: column;
                }

                .master-vault-v10-5-range-grid,
                .master-vault-v10-5-range-grid.is-reach {
                    grid-template-columns: 1fr;
                    max-width: none;
                }
            }

        

            /* ==========================================================
               VAULT V10.8 — GAVETAS DE PROPRIEDADES / MAESTRIAS
            ========================================================== */

            .master-vault-v10-8-selected {
                display: grid;
                gap: 8px;
                margin-top: 13px;
                border-top: 1px solid rgba(216,199,171,.50);
                padding-top: 12px;
            }

            .master-vault-v10-8-selected-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .master-vault-v10-8-selected-head strong {
                color: #53150f;
                font-size: 11px;
                font-weight: 900;
            }

            .master-vault-v10-8-selected-head span {
                color: #94755d;
                font-size: 9px;
                font-weight: 800;
            }

            .master-vault-v10-8-drawer-list {
                display: grid;
                gap: 6px;
            }

            .master-vault-v10-8-drawer {
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 10px;
                background: rgba(255,253,248,.56);
            }

            .master-vault-v10-8-drawer-head {
                display: grid;
                grid-template-columns: minmax(0,1fr) 34px;
                align-items: stretch;
            }

            .master-vault-v10-8-drawer-toggle {
                display: flex;
                min-width: 0;
                min-height: 40px;
                align-items: center;
                gap: 8px;
                border: 0;
                background: transparent;
                padding: 7px 9px;
                color: #53150f;
                text-align: left;
                cursor: pointer;
            }

            .master-vault-v10-8-drawer-toggle:hover {
                background: rgba(107,29,20,.035);
            }

            .master-vault-v10-8-chevron {
                width: 14px;
                height: 14px;
                flex: 0 0 auto;
                color: #8c6239;
                transition: transform .14s ease;
            }

            .master-vault-v10-8-chevron.is-open {
                transform: rotate(90deg);
            }

            .master-vault-v10-8-drawer-title {
                display: flex;
                min-width: 0;
                align-items: baseline;
                gap: 8px;
            }

            .master-vault-v10-8-drawer-title strong {
                overflow: hidden;
                color: #53150f;
                font-size: 10.5px;
                font-weight: 900;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v10-8-drawer-title small {
                flex: 0 0 auto;
                color: #956d4e;
                font-size: 8.5px;
                font-weight: 900;
            }

            .master-vault-v10-8-drawer-remove {
                display: flex;
                align-items: center;
                justify-content: center;
                border: 0;
                border-left: 1px solid rgba(205,187,159,.40);
                background: transparent;
                color: #a28670;
                cursor: pointer;
            }

            .master-vault-v10-8-drawer-remove:hover {
                background: rgba(161,59,50,.06);
                color: #96372f;
            }

            .master-vault-v10-8-drawer-remove svg {
                width: 14px;
                height: 14px;
            }

            .master-vault-v10-8-drawer-body {
                border-top: 1px solid rgba(205,187,159,.42);
                background: rgba(246,240,230,.48);
                padding: 10px 12px 11px 31px;
            }

            .master-vault-v10-8-rule-description {
                margin: 0;
                color: #674f3d;
                font-size: 10px;
                line-height: 1.58;
            }

            .master-vault-v10-8-measure {
                margin-top: 10px;
                border-top: 1px dashed rgba(176,140,98,.42);
                padding-top: 9px;
            }

            .master-vault-v10-8-measure-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 8px;
            }

            .master-vault-v10-8-measure-head strong {
                color: #53150f;
                font-size: 9.5px;
                font-weight: 900;
            }

            .master-vault-v10-8-measure-head span {
                color: #6b1d14;
                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;
            }

            .master-vault-v10-8-measure-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0,1fr));
                gap: 9px;
                max-width: 620px;
            }

            .master-vault-v10-8-measure-grid.is-single {
                grid-template-columns: minmax(180px, 320px);
                max-width: 320px;
            }

            @media (max-width: 640px) {
                .master-vault-v10-8-drawer-body {
                    padding-left: 11px;
                }

                .master-vault-v10-8-measure-grid,
                .master-vault-v10-8-measure-grid.is-single {
                    grid-template-columns: 1fr;
                    max-width: none;
                }

                .master-vault-v10-8-drawer-title {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 1px;
                }
            }



            .master-vault-v11-single-use-note {
                margin-top: 10px;
                border-left: 2px solid rgba(107,29,20,.44);
                padding: 7px 0 7px 10px;
                color: rgba(83,21,15,.72);
                font-size: 10px;
                line-height: 1.5;
            }

            .master-vault-v4-choice:disabled,
            .master-vault-v4-input:disabled {
                cursor: not-allowed;
                opacity: .48;
            }



            /* ================================================================
               V11 — PASTAS DO COFRE
               ================================================================ */
            .master-vault-v11-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .master-vault-v11-folder-button {
                display: inline-flex;
                min-height: 42px;
                align-items: center;
                justify-content: center;
                gap: 7px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 11px;
                background: rgba(255,253,248,.92);
                padding: 0 14px;
                color: var(--vault-accent);
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
                cursor: pointer;
                transition: .15s ease;
            }

            .master-vault-v11-folder-button:hover {
                border-color: rgba(107,29,20,.35);
                background: #fffdf8;
                transform: translateY(-1px);
            }

            /* Visão geral das pastas. NÃO compartilhar layout com active-folder. */
            .master-vault-v11-folder-section {
                display: block;
                margin-top: 22px;
                padding: 0;
                border: 0;
                background: transparent;
                box-shadow: none;
            }

            .master-vault-v11-folder-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                padding: 0 2px 18px;
                border-bottom: 1px solid rgba(205,187,159,.48);
            }

            .master-vault-v11-folder-headline {
                display: flex;
                align-items: center;
                gap: 13px;
                min-width: 0;
            }

            .master-vault-v11-folder-headicon,
            .master-vault-v11-folder-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
                color: var(--vault-accent);
            }

            .master-vault-v11-folder-headicon {
                width: 42px;
                height: 42px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 13px;
                background: rgba(255,249,242,.92);
            }

            .master-vault-v11-folder-headicon svg {
                display: block !important;
                width: 20px !important;
                height: 20px !important;
                min-width: 20px !important;
                min-height: 20px !important;
                max-width: 20px !important;
                max-height: 20px !important;
                flex: none !important;
            }

            .master-vault-v11-folder-head h2 {
                margin: 0;
                color: var(--vault-ink);
                font-family: Georgia, serif;
                font-size: 26px;
                font-weight: 900;
                line-height: 1.05;
            }

            .master-vault-v11-folder-head p {
                margin: 5px 0 0;
                color: #a1724a;
                font-size: 12px;
                line-height: 1.4;
                font-style: italic;
            }

            .master-vault-v11-folder-badge,
            .master-vault-v11-folder-count {
                display: inline-flex;
                min-height: 30px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.82);
                border-radius: 999px;
                background: rgba(255,249,241,.92);
                padding: 0 12px;
                color: #9a6d3c;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .master-vault-v11-folder-grid {
                display: grid !important;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 22px;
                align-items: stretch;
                margin-top: 24px;
            }

            .master-vault-v11-folder-card,
            .master-vault-v11-folder-new {
                min-width: 0;
                min-height: 220px;
                border: 1px solid rgba(205,187,159,.74);
                border-radius: 20px;
                background: linear-gradient(180deg, rgba(255,253,248,.99), rgba(250,246,238,.94));
                box-shadow: 0 8px 24px rgba(83,21,15,.045);
            }

            .master-vault-v11-folder-card {
                --folder-accent: #8A4B20;
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                overflow: visible;
                padding: 20px 20px 17px;
                cursor: pointer;
                box-shadow: inset 0 7px 0 0 var(--folder-accent), 0 8px 24px rgba(83,21,15,.045);
                transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
            }

            .master-vault-v11-folder-card:hover,
            .master-vault-v11-folder-card.is-drag-target {
                transform: translateY(-2px);
                border-color: rgba(107,29,20,.30);
                box-shadow: inset 0 7px 0 0 var(--folder-accent), 0 14px 30px rgba(83,21,15,.07);
            }

            .master-vault-v11-folder-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .master-vault-v11-folder-title {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                min-width: 0;
                flex: 1;
            }

            .master-vault-v11-folder-icon {
                width: 40px;
                height: 40px;
                flex: 0 0 40px;
                border-radius: 12px;
                background: rgba(140,98,57,.09);
            }

            .master-vault-v11-folder-icon svg {
                display: block !important;
                width: 20px !important;
                height: 20px !important;
                min-width: 20px !important;
                min-height: 20px !important;
                max-width: 20px !important;
                max-height: 20px !important;
                flex: none !important;
            }

            .master-vault-v11-folder-card h3 {
                display: -webkit-box;
                overflow: hidden;
                margin: 2px 0 0;
                color: var(--vault-ink);
                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: 900;
                line-height: 1.2;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
            }

            .master-vault-v11-folder-description {
                display: -webkit-box;
                overflow: hidden;
                min-height: 38px;
                margin: 13px 0 0;
                color: #a07048;
                font-size: 11px;
                line-height: 1.55;
                font-style: italic;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
            }

            .master-vault-v11-folder-menu-wrap {
                position: relative;
                flex: 0 0 auto;
            }

            .master-vault-v11-folder-menu-button {
                display: inline-flex;
                width: 32px;
                height: 32px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 9px;
                background: transparent;
                color: #c09872;
                font-size: 18px;
                line-height: 1;
                cursor: pointer;
            }

            .master-vault-v11-folder-menu-button:hover {
                background: rgba(107,29,20,.06);
                color: var(--vault-accent);
            }

            .master-vault-v11-folder-menu-panel {
                position: absolute;
                top: calc(100% + 8px);
                right: 0;
                z-index: 80;
                display: grid;
                gap: 4px;
                min-width: 165px;
                border: 1px solid rgba(205,187,159,.86);
                border-radius: 13px;
                background: #fffdf8;
                padding: 7px;
                box-shadow: 0 16px 34px rgba(31,18,12,.14);
            }

            .master-vault-v11-folder-menu-panel button {
                width: 100%;
                border: 0;
                border-radius: 9px;
                background: transparent;
                padding: 9px 10px;
                color: #76553f;
                text-align: left;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v11-folder-menu-panel button:hover {
                background: rgba(107,29,20,.06);
                color: #6b1d14;
            }

            .master-vault-v11-folder-menu-panel button.is-danger:hover {
                background: #fff0ed;
                color: #a33a2f;
            }

            .master-vault-v11-folder-foot {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 12px;
                margin-top: 20px;
            }

            .master-vault-v11-folder-preview {
                display: flex;
                align-items: flex-end;
                gap: 3px;
                min-width: 0;
                min-height: 38px;
                overflow: hidden;
            }

            .master-vault-v11-folder-avatar {
                display: inline-flex;
                width: 34px;
                height: 34px;
                flex: 0 0 34px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 0;
                border-radius: 8px;
                background: transparent;
                box-shadow: none;
            }

            .master-vault-v11-folder-avatar img {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: contain;
                filter: drop-shadow(0 2px 4px rgba(83,21,15,.10));
            }

            .master-vault-v11-folder-avatar.is-text {
                border: 1px solid rgba(205,187,159,.55);
                background: rgba(140,98,57,.08);
                color: #8c6239;
                font-size: 10px;
                font-weight: 900;
            }

            .master-vault-v11-folder-avatar.is-empty {
                width: auto;
                height: auto;
                flex: 0 1 auto;
                border: 0;
                border-radius: 0;
                color: #b58d69;
                font-size: 10px;
                font-style: italic;
                white-space: nowrap;
            }

            .master-vault-v11-folder-new {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                border-style: dashed;
                padding: 20px;
                color: #8c6239;
                text-align: center;
                cursor: pointer;
            }

            .master-vault-v11-folder-new:hover {
                border-color: rgba(107,29,20,.30);
                background: #fffaf3;
            }

            .master-vault-v11-folder-new .master-vault-v11-folder-icon {
                width: 48px;
                height: 48px;
                flex-basis: 48px;
                border-radius: 15px;
                font-size: 27px;
                font-weight: 500;
            }

            .master-vault-v11-folder-new strong {
                margin-top: 13px;
                color: var(--vault-ink);
                font-family: Georgia, serif;
                font-size: 20px;
                line-height: 1.1;
            }

            .master-vault-v11-folder-new small {
                max-width: 220px;
                margin-top: 8px;
                color: #9b7758;
                font-size: 10px;
                line-height: 1.5;
            }

            /* Pasta aberta: bloco separado da visão geral. */
            .master-vault-v11-active-folder {
                --folder-accent: #8A4B20;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                margin-top: 22px;
                padding: 18px 20px;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 18px;
                background: rgba(255,252,246,.90);
                box-shadow: inset 4px 0 0 var(--folder-accent), 0 7px 20px rgba(83,21,15,.04);
            }

            .master-vault-v11-active-main {
                min-width: 0;
                display: flex;
                align-items: center;
                gap: 12px;
                flex: 1;
            }

            .master-vault-v11-active-back {
                display: inline-flex;
                width: 36px;
                height: 36px;
                flex: 0 0 36px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 11px;
                background: rgba(255,250,244,.84);
                color: #8c6239;
                cursor: pointer;
            }

            .master-vault-v11-active-back svg {
                display: block !important;
                width: 16px !important;
                height: 16px !important;
                max-width: 16px !important;
                max-height: 16px !important;
            }

            .master-vault-v11-active-icon {
                display: inline-flex;
                width: 44px;
                height: 44px;
                flex: 0 0 44px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 13px;
                background: rgba(140,98,57,.08);
                color: var(--folder-accent);
            }

            .master-vault-v11-active-icon svg {
                display: block !important;
                width: 21px !important;
                height: 21px !important;
                max-width: 21px !important;
                max-height: 21px !important;
                flex: none !important;
            }

            .master-vault-v11-active-copy { min-width: 0; }

            .master-vault-v11-active-folder h2 {
                margin: 0;
                color: var(--vault-ink);
                font-family: Georgia, serif;
                font-size: 25px;
                font-weight: 900;
                line-height: 1.1;
            }

            .master-vault-v11-active-folder p {
                display: -webkit-box;
                overflow: hidden;
                max-width: 720px;
                margin: 4px 0 0;
                color: #9a6c48;
                font-size: 11px;
                line-height: 1.45;
                font-style: italic;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
            }

            .master-vault-v11-active-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .master-vault-v11-active-count {
                display: inline-flex;
                min-height: 34px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.80);
                border-radius: 999px;
                background: rgba(255,250,244,.92);
                padding: 0 12px;
                color: #9a6d3c;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .master-vault-v11-modal-layer {
                position: fixed;
                inset: 0;
                z-index: 170;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background: rgba(20,12,9,.68);
                backdrop-filter: blur(7px);
            }

            .master-vault-v11-modal {
                width: min(680px, 100%);
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 20px;
                background: #f8f5ee;
                box-shadow: 0 24px 60px rgba(31,18,12,.26);
            }

            .master-vault-v11-modal-head,
            .master-vault-v11-modal-foot {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                background: #efe9dc;
                padding: 14px 22px;
            }

            .master-vault-v11-modal-head { border-bottom: 1px solid rgba(205,187,159,.62); }
            .master-vault-v11-modal-foot { justify-content: flex-end; border-top: 1px solid rgba(205,187,159,.62); }

            .master-vault-v11-modal-headline { display: flex; align-items: center; gap: 12px; }

            .master-vault-v11-modal-headicon {
                display: inline-flex;
                width: 36px;
                height: 36px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 12px;
                background: rgba(255,248,241,.94);
                color: var(--vault-accent);
            }

            .master-vault-v11-modal-headicon svg { width: 18px; height: 18px; }

            .master-vault-v11-modal-head small {
                display: block;
                color: #8c6239;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .14em;
                text-transform: uppercase;
            }

            .master-vault-v11-modal-head h2 {
                margin: 2px 0 0;
                color: var(--vault-ink);
                font-family: Georgia, serif;
                font-size: 19px;
                font-weight: 900;
                line-height: 1.1;
            }

            .master-vault-v11-modal-head p {
                margin: 3px 0 0;
                color: #a1724a;
                font-size: 12px;
                line-height: 1.35;
                font-style: italic;
            }

            .master-vault-v11-modal-body { display: grid; gap: 16px; padding: 18px 22px; }

            .master-vault-v11-modal-stack { display: grid; gap: 14px; }

            .master-vault-v11-label {
                display: block;
                margin-bottom: 10px;
                color: var(--vault-ink);
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                letter-spacing: .03em;
                text-transform: uppercase;
            }

            .master-vault-v11-label small {
                color: #9a7450;
                font-size: 10px;
                font-family: inherit;
                font-weight: 700;
                text-transform: none;
                letter-spacing: 0;
            }

            .master-vault-v11-color-grid {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 12px 14px;
                padding: 14px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 15px;
                background: rgba(255,251,244,.90);
            }

            .master-vault-v11-color-option {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 6px;
                border: 0;
                background: transparent;
                padding: 0;
                color: #8c6239;
                font-size: 10px;
                cursor: pointer;
            }

            .master-vault-v11-color-swatch {
                display: inline-flex;
                width: 30px;
                height: 30px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: var(--swatch-color, #8A4B20);
                box-shadow: inset 0 0 0 1px rgba(255,255,255,.32);
            }

            .master-vault-v11-color-option.is-active .master-vault-v11-color-swatch {
                box-shadow: 0 0 0 3px #fff7ef, 0 0 0 5px var(--swatch-color, #8A4B20);
            }

            .master-vault-v11-color-option strong {
                color: #9b724d;
                font-size: 10px;
                font-weight: 700;
            }

            .master-vault-v11-curse-add {
                width: 100%;
                min-height: 42px;
                border: 1px dashed rgba(107,29,20,.30);
                border-radius: 11px;
                background: rgba(107,29,20,.035);
                color: var(--vault-accent);
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v11-curse-panel {
                display: grid;
                gap: 11px;
                border: 1px solid rgba(107,29,20,.18);
                border-radius: 12px;
                background: rgba(107,29,20,.035);
                padding: 12px;
            }

            .master-vault-v11-curse-panel-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .master-vault-v11-curse-panel-head strong { font-size: 11px; }
            .master-vault-v11-curse-panel-head button {
                border: 0;
                background: transparent;
                color: #a33a2f;
                font-size: 8px;
                font-weight: 900;
                text-transform: uppercase;
                cursor: pointer;
            }

            .master-vault-v11-modal .master-vault-v4-textarea {
                min-height: 90px;
                max-height: 120px;
                resize: vertical;
            }

            .master-vault-shell,
            .master-vault-v4-scroll,
            .master-vault-v9-preview-body,
            .master-vault-v11-modal-body,
            html,
            body {
                scrollbar-width: thin;
                scrollbar-color: #a9794d transparent;
            }

            .master-vault-shell::-webkit-scrollbar,
            .master-vault-v4-scroll::-webkit-scrollbar,
            .master-vault-v9-preview-body::-webkit-scrollbar,
            .master-vault-v11-modal-body::-webkit-scrollbar,
            html::-webkit-scrollbar,
            body::-webkit-scrollbar {
                width: 7px;
                height: 7px;
            }

            .master-vault-shell::-webkit-scrollbar-track,
            .master-vault-v4-scroll::-webkit-scrollbar-track,
            .master-vault-v9-preview-body::-webkit-scrollbar-track,
            .master-vault-v11-modal-body::-webkit-scrollbar-track,
            html::-webkit-scrollbar-track,
            body::-webkit-scrollbar-track {
                background: transparent;
            }

            .master-vault-shell::-webkit-scrollbar-thumb,
            .master-vault-v4-scroll::-webkit-scrollbar-thumb,
            .master-vault-v9-preview-body::-webkit-scrollbar-thumb,
            .master-vault-v11-modal-body::-webkit-scrollbar-thumb,
            html::-webkit-scrollbar-thumb,
            body::-webkit-scrollbar-thumb {
                border: 2px solid transparent;
                border-radius: 999px;
                background: #a9794d;
                background-clip: padding-box;
            }

            .master-vault-shell::-webkit-scrollbar-thumb:hover,
            .master-vault-v4-scroll::-webkit-scrollbar-thumb:hover,
            .master-vault-v9-preview-body::-webkit-scrollbar-thumb:hover,
            .master-vault-v11-modal-body::-webkit-scrollbar-thumb:hover,
            html::-webkit-scrollbar-thumb:hover,
            body::-webkit-scrollbar-thumb:hover {
                background: #8a4b20;
                background-clip: padding-box;
            }

            @media (max-width: 900px) {
                .master-vault-v11-folder-grid {
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                }

                .master-vault-v11-color-grid {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }

            @media (max-width: 760px) {
                .master-vault-v11-folder-head {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .master-vault-v11-folder-headline {
                    width: 100%;
                }

                .master-vault-v11-active-folder {
                    align-items: flex-start;
                    flex-direction: column;
                    padding: 16px;
                }

                .master-vault-v11-active-main,
                .master-vault-v11-active-actions {
                    width: 100%;
                }

                .master-vault-v11-active-actions {
                    justify-content: flex-start;
                }

                .master-vault-v11-actions {
                    width: 100%;
                }

                .master-vault-v11-folder-button,
                .master-vault-v9-create {
                    flex: 1;
                }

                .master-vault-v11-modal-head,
                .master-vault-v11-modal-foot,
                .master-vault-v11-modal-body {
                    padding-left: 18px;
                    padding-right: 18px;
                }

                .master-vault-v11-color-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }

                .master-vault-v9-grid {
                    grid-template-columns: 1fr;
                }
            }



            /* ================================================================
               V14 — HEADER ÚNICO + PASTAS NO PADRÃO DO COFRE DE NPCs
               ================================================================ */
            .master-vault-v9-shell {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 18px 42px !important;
            }

            .master-vault-v14-topbar {
                position: sticky !important;
                top: 0 !important;
                z-index: 45 !important;
                grid-template-columns: minmax(245px, auto) minmax(300px, 1fr) 185px auto !important;
                gap: 10px !important;
                margin: 0 -18px 0 !important;
                border-top: 0 !important;
                border-right: 0 !important;
                border-left: 0 !important;
                border-radius: 0 !important;
                background: rgba(239,233,220,.97) !important;
                box-shadow: 0 1px 0 rgba(205,187,159,.55), 0 7px 18px rgba(83,21,15,.035) !important;
                padding: 10px 24px !important;
                backdrop-filter: blur(14px) !important;
            }

            .master-vault-v14-context {
                min-width: 0;
            }

            .master-vault-v14-context-home,
            .master-vault-v14-context-folder {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 10px;
            }

            .master-vault-v14-context-folder {
                gap: 9px;
            }

            .master-vault-v14-back {
                display: inline-flex;
                width: 34px;
                height: 34px;
                flex: 0 0 34px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 10px;
                background: rgba(255,250,243,.88);
                color: #8c6239;
                cursor: pointer;
                transition: .15s ease;
            }

            .master-vault-v14-back:hover {
                border-color: rgba(107,29,20,.25);
                background: #fffaf3;
                color: #6b1d14;
            }

            .master-vault-v14-back svg {
                width: 16px !important;
                height: 16px !important;
            }

            .master-vault-v14-folder-mark {
                display: inline-flex;
                width: 38px;
                height: 38px;
                flex: 0 0 38px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.60);
                border-radius: 11px;
            }

            .master-vault-v14-folder-mark svg {
                width: 19px !important;
                height: 19px !important;
            }

            .master-vault-v14-folder-copy {
                min-width: 0;
                max-width: 285px;
            }

            .master-vault-v14-folder-copy h1 {
                overflow: hidden;
                margin: 0;
                color: #6b1d14;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
                line-height: 1.15;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .master-vault-v14-folder-copy p {
                overflow: hidden;
                margin: 4px 0 0;
                color: rgba(140,98,57,.80);
                font-size: 9px;
                font-style: italic;
                line-height: 1.15;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* A tela deixa de parecer formada por caixas flutuantes. */
            .master-vault-v11-folder-section {
                margin-top: 30px !important;
            }

            .master-vault-v11-folder-head {
                padding: 0 2px 16px !important;
                border-bottom: 1px solid rgba(205,187,159,.48) !important;
            }

            /* Mesmo ritmo dos cards de pasta do Cofre de NPCs. */
            .master-vault-v11-folder-grid {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 24px !important;
                margin-top: 24px !important;
            }

            .master-vault-v11-folder-card {
                min-height: 238px !important;
                border-radius: 16px !important;
                padding: 20px 20px 16px !important;
                background: linear-gradient(180deg, rgba(255,253,248,.99), rgba(249,245,237,.97)) !important;
                box-shadow: inset 0 8px 0 0 var(--folder-accent), 0 5px 14px rgba(83,21,15,.045) !important;
            }

            .master-vault-v11-folder-card:hover {
                transform: translateY(-2px) !important;
                box-shadow: inset 0 8px 0 0 var(--folder-accent), 0 11px 23px rgba(83,21,15,.075) !important;
            }

            .master-vault-v11-folder-title {
                gap: 11px !important;
            }

            .master-vault-v11-folder-icon {
                width: 36px !important;
                height: 36px !important;
                flex-basis: 36px !important;
                border-radius: 10px !important;
            }

            .master-vault-v11-folder-icon svg {
                width: 18px !important;
                height: 18px !important;
                min-width: 18px !important;
                min-height: 18px !important;
                max-width: 18px !important;
                max-height: 18px !important;
            }

            .master-vault-v11-folder-card h3 {
                margin-top: 4px !important;
                font-size: 16px !important;
                line-height: 1.16 !important;
            }

            .master-vault-v11-folder-description {
                min-height: 46px !important;
                margin-top: 13px !important;
                font-size: 10.5px !important;
                line-height: 1.5 !important;
                -webkit-line-clamp: 3 !important;
            }

            .master-vault-v11-folder-foot {
                margin-top: auto !important;
                border-top: 1px solid rgba(205,187,159,.48) !important;
                padding-top: 14px !important;
            }

            .master-vault-v11-folder-preview {
                gap: 3px !important;
            }

            .master-vault-v11-folder-avatar {
                width: 31px !important;
                height: 31px !important;
                margin-left: 0 !important;
                border-radius: 7px !important;
            }

            .master-vault-v11-folder-avatar img {
                object-fit: contain !important;
            }

            .master-vault-v11-folder-count {
                min-height: 29px !important;
                padding: 0 11px !important;
                font-size: 9px !important;
            }

            .master-vault-v11-folder-menu-button {
                color: #b98963 !important;
            }

            /* Conteúdo da pasta: sem segundo hero/header. */
            .master-vault-v9-results.is-inside-folder {
                margin-top: 30px !important;
                padding: 0 2px 12px !important;
                border-bottom: 1px solid rgba(205,187,159,.46);
            }

            .master-vault-v9-results.is-inside-folder strong {
                font-size: 24px !important;
            }

            /* Cards internos simples: uma peça, sem várias caixas empilhadas. */
            .master-vault-v9-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 16px !important;
                margin-top: 16px !important;
            }

            .master-vault-v10-card {
                overflow: hidden !important;
                border-radius: 17px !important;
                box-shadow: 0 6px 18px rgba(83,21,15,.04) !important;
            }

            .master-vault-v10-card::before {
                display: none !important;
            }

            .master-vault-v10-card-main {
                grid-template-columns: 78px minmax(0,1fr) auto !important;
                gap: 13px !important;
                padding: 14px !important;
            }

            .master-vault-v10-thumb {
                width: 78px !important;
                height: 78px !important;
                border-radius: 13px !important;
            }

            .master-vault-v10-name {
                font-size: 16px !important;
            }

            .master-vault-v10-nature,
            .master-vault-v10-category {
                font-size: 9.5px !important;
            }

            .master-vault-v10-section {
                border-top: 0 !important;
                padding: 0 14px 12px 105px !important;
            }

            .master-vault-v10-description {
                font-size: 10.5px !important;
                line-height: 1.5 !important;
                -webkit-line-clamp: 2 !important;
            }

            .master-vault-v10-abilities {
                gap: 5px !important;
            }

            .master-vault-v10-ability {
                min-height: 23px !important;
                padding: 0 8px !important;
                font-size: 8px !important;
            }

            @media (max-width: 1260px) {
                .master-vault-v14-topbar {
                    grid-template-columns: minmax(220px, auto) minmax(250px, 1fr) 165px auto !important;
                }

                .master-vault-v11-folder-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                }

                .master-vault-v9-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
            }

            @media (max-width: 980px) {
                .master-vault-v14-topbar {
                    grid-template-columns: minmax(0, 1fr) auto !important;
                    grid-template-areas:
                        "context actions"
                        "search filter" !important;
                }

                .master-vault-v14-context { grid-area: context; }
                .master-vault-v9-search { grid-area: search; }
                .master-vault-v9-filter { grid-area: filter; }
                .master-vault-v11-actions { grid-area: actions; }

                .master-vault-v11-folder-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
            }

            @media (max-width: 720px) {
                .master-vault-v9-shell {
                    padding-right: 10px !important;
                    padding-left: 10px !important;
                }

                .master-vault-v14-topbar {
                    margin-right: -10px !important;
                    margin-left: -10px !important;
                    grid-template-columns: 1fr !important;
                    grid-template-areas:
                        "context"
                        "search"
                        "filter"
                        "actions" !important;
                    padding: 10px !important;
                }

                .master-vault-v11-folder-grid,
                .master-vault-v9-grid {
                    grid-template-columns: 1fr !important;
                }

                .master-vault-v10-section {
                    padding-left: 14px !important;
                }
            }

            /* ================================================================
               V15 — ALINHAMENTO EXATO COM A SIDEBAR / COFRE DE NPCs
               ================================================================ */

            /*
             * O Cofre de NPCs remove TODO o padding herdado dos wrappers
             * do x-app-layout. Aqui fazemos exatamente a mesma coisa.
             */
            main:has(.master-vault-page),
            .py-12:has(.master-vault-page),
            .max-w-7xl:has(.master-vault-page),
            .sm\:px-6:has(.master-vault-page),
            .lg\:px-8:has(.master-vault-page) {
                padding: 0 !important;
                max-width: 100% !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
            }

            aside,
            .sidebar,
            body {
                overflow-x: hidden !important;
            }

            .master-vault-page {
                width: 100% !important;
                min-width: 0 !important;
            }

            /*
             * O shell não cria mais uma margem artificial ao redor do header.
             * O respiro passa a existir somente no conteúdo abaixo.
             */
            .master-vault-v9-shell {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 0 42px !important;
            }

            /*
             * Mesma faixa superior do navegador:
             * - sidebar: py-3.5 + logo 32px = aproximadamente 60px
             * - header: py-2.5 + controles 40px = 60px
             *
             * Assim a linha inferior da sidebar e a linha inferior do
             * header terminam exatamente na mesma altura.
             */
            .master-vault-v14-topbar {
                position: sticky !important;
                top: 0 !important;
                z-index: 30 !important;
                width: 100% !important;
                min-height: 61px !important;
                margin: 0 !important;
                border: 0 !important;
                border-bottom: 1px solid rgba(107,29,20,.15) !important;
                border-radius: 0 !important;
                background: #efe9dc !important;
                box-shadow: 0 1px 2px rgba(83,21,15,.055) !important;
                padding: 10px 24px !important;
                backdrop-filter: blur(12px) !important;
            }

            .master-vault-v14-topbar .master-vault-v12-brand-icon {
                width: 36px !important;
                height: 36px !important;
                flex-basis: 36px !important;
                border-radius: 12px !important;
            }

            .master-vault-v14-topbar .master-vault-v12-brand-copy h1 {
                font-size: 14px !important;
            }

            .master-vault-v14-topbar .master-vault-v12-brand-copy p {
                margin-top: 2px !important;
                font-size: 10px !important;
            }

            .master-vault-v14-topbar .master-vault-v14-back,
            .master-vault-v14-topbar .master-vault-v14-folder-mark {
                width: 36px !important;
                height: 36px !important;
                flex-basis: 36px !important;
            }

            .master-vault-v14-topbar .master-vault-v9-search input,
            .master-vault-v14-topbar .master-vault-v9-filter select,
            .master-vault-v14-topbar .master-vault-v11-folder-button,
            .master-vault-v14-topbar .master-vault-v9-create {
                height: 40px !important;
                min-height: 40px !important;
            }

            .master-vault-v14-topbar .master-vault-v11-actions {
                flex-wrap: nowrap !important;
            }

            /*
             * Somente o conteúdo recebe margem interna.
             * O header continua 100% colado à sidebar e ao topo.
             */
            .master-vault-v9-shell > .master-vault-v11-folder-section,
            .master-vault-v9-shell > .master-vault-v9-results,
            .master-vault-v9-shell > .master-vault-v9-grid,
            .master-vault-v9-shell > .master-vault-v8-empty {
                margin-left: 18px !important;
                margin-right: 18px !important;
            }

            @media (max-width: 980px) {
                .master-vault-v14-topbar {
                    min-height: 0 !important;
                    padding: 10px 16px !important;
                }
            }

            @media (max-width: 720px) {
                .master-vault-v9-shell {
                    padding-right: 0 !important;
                    padding-left: 0 !important;
                }

                .master-vault-v14-topbar {
                    margin: 0 !important;
                    padding: 10px !important;
                }

                .master-vault-v9-shell > .master-vault-v11-folder-section,
                .master-vault-v9-shell > .master-vault-v9-results,
                .master-vault-v9-shell > .master-vault-v9-grid,
                .master-vault-v9-shell > .master-vault-v8-empty {
                    margin-left: 10px !important;
                    margin-right: 10px !important;
                }
            }


            /* ================================================================
               V15 — BUSCA SEMÂNTICA + PREVIEW COMPACTO
               ================================================================ */

            .master-vault-v15-taxonomy {
                display: grid;
                gap: 3px;
                margin-top: 8px;
            }

            .master-vault-v15-taxonomy-primary {
                margin: 0 !important;
                color: #6b1d14;
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.35;
            }

            .master-vault-v15-taxonomy-secondary {
                margin: 0 !important;
                color: #8c6239;
                font-size: 11px;
                font-weight: 800;
                line-height: 1.35;
            }

            .master-vault-v15-weapon-section {
                overflow: visible;
            }

            .master-vault-v15-rule-group + .master-vault-v15-rule-group {
                margin-top: 14px;
            }

            .master-vault-v15-rule-label {
                margin: 0 0 8px;
                color: #76553f;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
            }

            .master-vault-v15-rule-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                overflow: visible;
            }

            .master-vault-v15-rule-chip {
                position: relative;
                display: inline-flex;
                min-height: 32px;
                align-items: center;
                gap: 6px;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 999px;
                background: rgba(255,252,247,.95);
                padding: 0 11px;
                color: #684832;
                font-family: inherit;
                font-size: 10px;
                font-weight: 900;
                cursor: help;
                transition:
                    border-color .15s ease,
                    background .15s ease,
                    transform .15s ease;
            }

            .master-vault-v15-rule-chip:hover,
            .master-vault-v15-rule-chip:focus-visible {
                z-index: 20;
                border-color: rgba(107,29,20,.42);
                background: #fffdf8;
                outline: none;
                transform: translateY(-1px);
            }

            .master-vault-v15-rule-chip.is-mastery {
                border-color: rgba(107,29,20,.22);
                background: rgba(107,29,20,.045);
                color: #6b1d14;
            }

            .master-vault-v15-rule-chip small {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                background: rgba(140,98,57,.10);
                padding: 3px 6px;
                color: #8c6239;
                font-size: 8px;
                font-weight: 900;
                line-height: 1;
            }

            .master-vault-v15-rule-chip::after {
                content: attr(data-tooltip);
                position: absolute;
                left: 50%;
                bottom: calc(100% + 10px);
                z-index: 90;
                width: max-content;
                max-width: 340px;
                padding: 10px 12px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 11px;
                background: #2f211a;
                box-shadow: 0 12px 28px rgba(29,17,11,.20);
                color: #fff8ef;
                font-size: 10px;
                font-weight: 600;
                line-height: 1.55;
                text-align: left;
                text-transform: none;
                letter-spacing: 0;
                white-space: normal;
                pointer-events: none;
                opacity: 0;
                transform: translate(-50%, 5px);
                transition:
                    opacity .13s ease,
                    transform .13s ease;
            }

            .master-vault-v15-rule-chip:hover::after,
            .master-vault-v15-rule-chip:focus-visible::after {
                opacity: 1;
                transform: translate(-50%, 0);
            }

            .master-vault-v15-hover-hint {
                margin: 7px 0 0;
                color: #a08067;
                font-size: 8.5px;
                font-style: italic;
            }

            /*
             * Criação / edição: substitui a coleção de badges por duas
             * linhas tipográficas, igual à leitura final do item.
             */
            .master-vault-v15-editor-taxonomy {
                display: grid !important;
                gap: 2px !important;
                margin-top: 7px !important;
            }

            .master-vault-v15-editor-taxonomy-primary {
                color: #76513a;
                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;
                line-height: 1.35;
            }

            .master-vault-v15-editor-taxonomy-secondary {
                color: #9a7458;
                font-size: 9px;
                font-weight: 800;
                line-height: 1.35;
            }

            /*
             * A visualização deixa de parecer uma ficha gigante:
             * hero mais compacto e seções com menos altura.
             */
            .master-vault-v9-preview-hero {
                align-items: start;
                gap: 22px;
            }

            .master-vault-v9-preview-summary {
                padding-top: 2px;
            }

            .master-vault-v9-preview-description {
                max-width: 760px;
                margin-top: 13px;
                font-size: 12px;
                line-height: 1.65;
            }

            .master-vault-v9-preview-statrow {
                margin-top: 14px;
                gap: 8px;
            }

            .master-vault-v9-preview-stat {
                min-height: 44px;
                padding: 8px 11px;
            }

            .master-vault-v9-preview-section {
                padding-top: 15px;
                padding-bottom: 15px;
            }

            @media (max-width: 720px) {
                .master-vault-v15-rule-chip::after {
                    display: none;
                }

                .master-vault-v15-hover-hint {
                    display: none;
                }
            }


            /* ================================================================
               V16 — VISUALIZAÇÃO NO PADRÃO DO INVENTÁRIO
               ================================================================ */

            .master-vault-v9-preview-head {
                min-height: 50px;
                padding: 12px 16px !important;
            }

            .master-vault-v9-preview-head-copy {
                display: flex;
                align-items: center;
            }

            .master-vault-v9-preview-head h2 {
                display: none !important;
            }

            .master-vault-v16-preview-hero {
                grid-template-columns: 158px minmax(0, 1fr) !important;
                gap: 22px !important;
                align-items: start !important;
            }

            .master-vault-v16-preview-image {
                width: 158px !important;
                height: 158px !important;
                border-radius: 14px !important;
                background: #fbf7ef !important;
            }

            .master-vault-v16-preview-image img {
                object-fit: contain !important;
                padding: 8px;
            }

            .master-vault-v16-preview-summary {
                padding-top: 4px !important;
            }

            .master-vault-v16-eyebrow {
                display: block;
                color: #9a7458;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .master-vault-v16-preview-name {
                margin: 5px 0 0 !important;
                font-size: clamp(28px, 3.2vw, 38px) !important;
                line-height: .98 !important;
            }

            .master-vault-v16-classification {
                display: grid;
                gap: 5px;
                margin-top: 9px;
            }

            .master-vault-v16-nature,
            .master-vault-v16-category {
                margin: 0;
                font-family: Georgia, serif;
                font-style: italic;
                font-weight: 700;
                line-height: 1.35;
            }

            .master-vault-v16-nature {
                color: #765b48;
                font-size: 12px;
            }

            .master-vault-v16-category {
                color: #684832;
                font-size: 13px;
            }

            .master-vault-v16-inline-rules {
                display: flex;
                align-items: baseline;
                gap: 7px;
                margin-top: 7px;
                color: #76543e;
                font-size: 9px;
                line-height: 1.45;
            }

            .master-vault-v16-inline-rules > strong {
                flex: 0 0 auto;
                color: #6b1d14;
                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .master-vault-v16-inline-list {
                display: flex;
                flex-wrap: wrap;
                align-items: baseline;
                gap: 2px;
                min-width: 0;
            }

            .master-vault-v16-inline-entry {
                position: relative;
                display: inline-flex;
                align-items: baseline;
            }

            .master-vault-v16-hover-link {
                position: relative;
                border: 0;
                border-bottom: 1px dotted rgba(107,29,20,.42);
                background: transparent;
                padding: 0;
                color: #7d604b;
                font-family: inherit;
                font-size: 9px;
                font-weight: 700;
                cursor: help;
            }

            .master-vault-v16-hover-link:hover,
            .master-vault-v16-hover-link:focus-visible {
                z-index: 30;
                color: #6b1d14;
                outline: none;
            }

            .master-vault-v16-hover-link small {
                font-size: inherit;
                font-weight: inherit;
            }

            .master-vault-v16-hover-link::after {
                content: attr(data-tooltip);
                position: absolute;
                left: 50%;
                bottom: calc(100% + 9px);
                z-index: 100;
                width: max-content;
                max-width: 340px;
                padding: 10px 12px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 10px;
                background: #2f211a;
                box-shadow: 0 12px 28px rgba(29,17,11,.20);
                color: #fff8ef;
                font-size: 10px;
                font-weight: 600;
                line-height: 1.55;
                text-align: left;
                white-space: normal;
                pointer-events: none;
                opacity: 0;
                transform: translate(-50%, 5px);
                transition: opacity .13s ease, transform .13s ease;
            }

            .master-vault-v16-hover-link:hover::after,
            .master-vault-v16-hover-link:focus-visible::after {
                opacity: 1;
                transform: translate(-50%, 0);
            }

            .master-vault-v16-separator {
                color: #a88b73;
                margin-right: 2px;
            }

            .master-vault-v16-small-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 9px;
                color: #8c6a50;
                font-size: 8.5px;
            }

            .master-vault-v16-small-meta strong {
                color: #6b1d14;
                font-weight: 900;
            }

            .master-vault-v16-content-section {
                margin-top: 20px;
                padding-top: 0;
            }

            .master-vault-v16-section-title {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr);
                align-items: center;
                gap: 10px;
                margin-bottom: 12px;
            }

            .master-vault-v16-section-title > span {
                color: #9a704d;
                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .master-vault-v16-section-title > i {
                display: block;
                height: 1px;
                background: rgba(205,187,159,.65);
            }

            .master-vault-v16-description {
                margin: 0;
                color: #654e3d;
                font-size: 12px;
                line-height: 1.7;
                white-space: pre-line;
            }

            .master-vault-v9-preview-section:has(.master-vault-v16-section-title) {
                border-top: 0 !important;
                padding-top: 0 !important;
            }

            .master-vault-v9-preview-feature {
                padding: 0 !important;
                overflow: hidden;
                border-radius: 13px !important;
                background: #fbf7ef !important;
            }

            .master-vault-v9-preview-feature-head {
                border-bottom: 1px solid rgba(205,187,159,.55);
                padding: 11px 13px;
            }

            .master-vault-v9-preview-feature > p {
                margin: 0 !important;
                padding: 12px 13px 13px;
            }

            .master-vault-v10-category:empty,
            .master-vault-v15-taxonomy-secondary:empty,
            .master-vault-v15-editor-taxonomy-secondary:empty {
                display: none !important;
            }

            @media (max-width: 720px) {
                .master-vault-v16-preview-hero {
                    grid-template-columns: 1fr !important;
                }

                .master-vault-v16-preview-image {
                    width: 132px !important;
                    height: 132px !important;
                }

                .master-vault-v16-hover-link::after {
                    display: none;
                }
            }


            /* =============================================================
               V66.1.12 — COFRE DE ITENS: PADRÃO VISUAL COFRE DE NPCs
               Estética da página e das pastas apenas. Modais e backend intactos.
               ============================================================= */
            .master-vault-page {
                --vault-ink: #63231c;
                --vault-accent: #72271f;
                --vault-muted: #926849;
                --vault-border: rgba(178, 145, 108, .30);
                --vault-border-soft: rgba(206, 184, 155, .57);
            }
            .master-vault-page .master-vault-v9-shell {
                width: min(1530px, 100%) !important;
                max-width: 1530px !important;
                margin: 0 auto !important;
                padding: clamp(18px, 2vw, 30px) clamp(16px, 2.7vw, 38px) 52px !important;
            }
            /* Sem header fixo, barra de navegação interna nem caixa retangular. */
            .master-vault-page .master-vault-v14-topbar {
                position: relative !important;
                top: auto !important;
                z-index: 12 !important;
                display: grid !important;
                width: 100% !important;
                min-height: 0 !important;
                grid-template-columns: minmax(0, 1fr) minmax(0, auto) !important;
                grid-template-areas: "context actions" "search filter" !important;
                align-items: center !important;
                column-gap: 14px !important;
                row-gap: clamp(13px, 1.6vw, 20px) !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
                backdrop-filter: none !important;
            }
            .master-vault-page .master-vault-v14-context {
                grid-area: context !important;
                min-width: 0;
            }
            .master-vault-page .master-vault-v14-context-home,
            .master-vault-page .master-vault-v14-context-folder {
                align-items: center;
                min-width: 0;
                gap: 12px;
            }
            .master-vault-page .master-vault-v12-brand-copy h1,
            .master-vault-page .master-vault-v14-folder-copy h1 {
                margin: 0 !important;
                font-family: Georgia, "Times New Roman", serif !important;
                font-weight: 700 !important;
                font-size: clamp(27px, 2.85vw, 39px) !important;
                letter-spacing: -.038em;
                line-height: 1.14 !important;
                white-space: normal;
                color: #69251c !important;
            }
            .master-vault-page .master-vault-v12-brand-copy::after,
            .master-vault-page .master-vault-v14-folder-copy::after {
                content: "";
                display: block;
                width: 45px;
                height: 2px;
                border-radius: 99px;
                margin-top: 8px;
                background: linear-gradient(90deg, #8a4c38, #c8b291 83%, transparent);
            }
            .master-vault-page .master-vault-v12-brand-copy p,
            .master-vault-page .master-vault-v14-folder-copy p {
                margin: 3px 0 0 !important;
                color: #97714f !important;
                font-size: 11px !important;
                line-height: 1.35 !important;
                letter-spacing: 0;
                font-style: italic;
                white-space: normal;
            }
            .master-vault-page .master-vault-v14-folder-copy { max-width: min(750px, 70vw); }
            .master-vault-page .master-vault-v14-topbar .master-vault-v14-back {
                width: 35px !important;
                height: 35px !important;
                border-radius: 12px;
                flex-basis: 35px !important;
            }
            .master-vault-page .master-vault-v11-actions {
                grid-area: actions !important;
                display: flex;
                justify-content: flex-end;
                width: auto !important;
                gap: 9px;
                flex-wrap: wrap !important;
            }
            .master-vault-page .master-vault-v11-folder-button,
            .master-vault-page .master-vault-v9-create {
                display: inline-flex;
                min-height: 38px !important;
                height: 38px !important;
                flex: 0 0 auto;
                border-radius: 12px;
                padding: 0 15px;
                font: 700 12px/1.2 Georgia, "Times New Roman", serif;
                text-transform: none;
                letter-spacing: 0;
                white-space: nowrap;
                box-shadow: 0 2px 8px rgba(60,33,18,.045);
                transition: transform .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease;
            }
            .master-vault-page .master-vault-v11-folder-button {
                color: #702a20;
                border: 1px solid #dec9af;
                background: #fffaf3;
            }
            .master-vault-page .master-vault-v9-create {
                background: #74281f;
                color: #fff9f2;
                border: 1px solid #74281f;
            }
            .master-vault-page .master-vault-v11-folder-button:hover,
            .master-vault-page .master-vault-v9-create:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 14px rgba(83,21,15,.10);
            }
            .master-vault-page .master-vault-v9-create:hover { background: #5b1b15; }
            .master-vault-page .master-vault-v9-search {
                grid-area: search !important;
                display: block;
                min-width: 0;
            }
            .master-vault-page .master-vault-v9-search input {
                width: 100%;
                height: 43px !important;
                min-height: 43px !important;
                border: 1px solid #dac5a9 !important;
                border-radius: 14px !important;
                background: rgba(255, 252, 247, .80) !important;
                color: #573e30;
                padding: 0 18px 0 44px !important;
                font-size: 13px;
                font-weight: 400;
                line-height: 1.4;
                box-shadow: 0 3px 10px rgba(77,43,22,.035) !important;
            }
            .master-vault-page .master-vault-v9-search input::placeholder { color: #b19b81; }
            .master-vault-page .master-vault-v9-search svg {
                left: 16px;
                width: 17px;
                height: 17px;
            }
            .master-vault-page .master-vault-v9-search input:focus {
                outline: none;
                background: #fffdf8 !important;
                border-color: #ac8b68 !important;
                box-shadow: 0 0 0 3px rgba(135,90,55,.07) !important;
            }
            .master-vault-page .master-vault-v9-filter {
                grid-area: filter !important;
                display: block;
                width: clamp(135px, 13vw, 190px);
            }
            .master-vault-page .master-vault-v9-filter select {
                height: 43px !important;
                min-height: 43px !important;
                border: 1px solid #e1d1ba;
                border-radius: 13px;
                background: rgba(255,252,247,.62);
                color: #77533e;
                font-size: 11px;
                font-weight: 500;
                letter-spacing: 0;
                text-transform: none;
            }
            .master-vault-page .master-vault-v9-filter select:focus-visible,
            .master-vault-page .master-vault-v11-folder-button:focus-visible,
            .master-vault-page .master-vault-v9-create:focus-visible {
                outline: 2px solid #b28d66;
                outline-offset: 2px;
            }
            /* Seção igual à do Cofre de NPCs: título livre e contador discreto. */
            .master-vault-page .master-vault-v9-shell > .master-vault-v11-folder-section,
            .master-vault-page .master-vault-v9-shell > .master-vault-v9-results,
            .master-vault-page .master-vault-v9-shell > .master-vault-v9-grid,
            .master-vault-page .master-vault-v9-shell > .master-vault-v8-empty {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .master-vault-page .master-vault-v11-folder-section { margin-top: 29px !important; }
            .master-vault-page .master-vault-v11-folder-head {
                gap: 12px;
                border-bottom: 1px solid rgba(173,139,101,.22) !important;
                padding: 0 0 12px !important;
            }
            .master-vault-page .master-vault-v11-folder-headline { gap: 0; }
            .master-vault-page .master-vault-v11-folder-head h2 {
                font: 700 clamp(23px, 2.1vw, 29px)/1.13 Georgia, "Times New Roman", serif !important;
                letter-spacing: -.028em;
                color: #6b251b;
            }
            .master-vault-page .master-vault-v11-folder-head p {
                margin-top: 2px;
                font-size: 11px;
                color: #99724f;
            }
            .master-vault-page .master-vault-v11-folder-badge {
                min-height: 24px;
                padding: 0 11px;
                color: #8b5c3c;
                font: 700 11px Georgia, serif;
                letter-spacing: 0;
                text-transform: none;
            }
            /* Cards de pastas: proporção compacta, sem painéis vazios. */
            .master-vault-page .master-vault-v11-folder-grid {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0,1fr)) !important;
                gap: 17px !important;
                margin-top: 19px !important;
                align-items: stretch;
            }
            .master-vault-page .master-vault-v11-folder-card {
                min-height: 174px !important;
                padding: 17px 15px 13px !important;
                border: 1px solid #ddc9b0 !important;
                border-radius: 17px !important;
                background: linear-gradient(155deg, #fffcf7 0%, #f8f2e9 100%) !important;
                box-shadow: inset 0 3px 0 0 var(--folder-accent), 0 5px 14px rgba(77,41,22,.045) !important;
                transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s, border-color .25s;
            }
            .master-vault-page .master-vault-v11-folder-card:hover,
            .master-vault-page .master-vault-v11-folder-card.is-drag-target {
                transform: translateY(-3px) !important;
                border-color: color-mix(in srgb, var(--folder-accent) 36%, #dcc9b0) !important;
                box-shadow: inset 0 3px 0 0 var(--folder-accent), 0 10px 24px rgba(77,41,22,.09) !important;
            }
            .master-vault-page .master-vault-v11-folder-top { gap: 7px; }
            .master-vault-page .master-vault-v11-folder-title {
                gap: 10px !important;
                align-items: flex-start;
                min-width: 0;
            }
            .master-vault-page .master-vault-v11-folder-icon {
                width: 32px !important;
                height: 32px !important;
                flex: 0 0 32px !important;
                border-radius: 9px !important;
                border: 1px solid rgba(149,118,82,.10);
            }
            .master-vault-page .master-vault-v11-folder-icon svg {
                width: 16px !important;
                height: 16px !important;
                min-width: 16px !important;
                min-height: 16px !important;
                max-width: 16px !important;
                max-height: 16px !important;
            }
            .master-vault-page .master-vault-v11-folder-card h3 {
                margin: 1px 0 0 !important;
                font: 700 clamp(15px, 1.15vw, 18px)/1.18 Georgia, serif !important;
                color: #4e332b;
                letter-spacing: -.018em;
                -webkit-line-clamp: 2;
            }
            .master-vault-page .master-vault-v11-folder-description {
                display: -webkit-box;
                margin: 7px 0 0 !important;
                min-height: 0 !important;
                max-height: 36px;
                color: #946d4e;
                font-size: 10.5px !important;
                font-weight: 400;
                font-style: italic;
                line-height: 1.45 !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .master-vault-page .master-vault-v11-folder-menu-button {
                width: 25px;
                height: 25px;
                border-radius: 9px;
                color: #9a7658 !important;
            }
            .master-vault-page .master-vault-v11-folder-foot {
                align-items: center;
                gap: 8px;
                margin-top: 12px !important;
                border-top: 1px solid rgba(183,149,111,.24) !important;
                padding-top: 9px !important;
            }
            .master-vault-page .master-vault-v11-folder-preview {
                display: flex;
                min-width: 0;
                min-height: 37px;
                gap: 1px !important;
                overflow: hidden;
                align-items: center;
            }
            .master-vault-page .master-vault-v11-folder-avatar {
                width: 38px !important;
                height: 38px !important;
                flex: 0 0 38px !important;
                margin-left: -4px !important;
                overflow: hidden;
                border: 0 !important;
                border-radius: 8px !important;
                background: transparent !important;
                box-shadow: none;
            }
            .master-vault-page .master-vault-v11-folder-avatar:first-child { margin-left: 0 !important; }
            .master-vault-page .master-vault-v11-folder-avatar img {
                object-fit: contain !important;
                width: 100% !important;
                height: 100% !important;
                mix-blend-mode: multiply;
                filter: drop-shadow(0 2px 2px rgba(60,32,20,.13));
                transition: transform .22s ease;
            }
            .master-vault-page .master-vault-v11-folder-avatar:hover img { transform: translateY(-3px) scale(1.08); }
            .master-vault-page .master-vault-v11-folder-avatar.is-empty {
                width: auto !important;
                height: auto !important;
                flex: 0 1 auto !important;
                margin: 0 !important;
                font-size: 10px;
            }
            .master-vault-page .master-vault-v11-folder-count {
                min-height: 24px !important;
                padding: 0 10px !important;
                font: 700 10px Georgia, serif !important;
                letter-spacing: 0;
                text-transform: none;
                color: #8e6140;
                background: #fffaf3;
            }
            .master-vault-page .master-vault-v9-grid { margin-top: 15px !important; }
            .master-vault-page .master-vault-v9-results.is-inside-folder { margin-top: 24px !important; }
            @media (max-width: 1220px) {
                .master-vault-page .master-vault-v11-folder-grid { grid-template-columns: repeat(3, minmax(0,1fr)) !important; }
            }
            @media (max-width: 990px) {
                .master-vault-page .master-vault-v14-topbar {
                    grid-template-columns: minmax(0,1fr) minmax(130px, 175px) !important;
                    grid-template-areas: "context actions" "search filter" !important;
                }
                .master-vault-page .master-vault-v11-folder-grid { grid-template-columns: repeat(2, minmax(0,1fr)) !important; }
            }
            @media (max-width: 720px) {
                .master-vault-page .master-vault-v9-shell { padding: 17px 12px 34px !important; }
                .master-vault-page .master-vault-v14-topbar {
                    grid-template-columns: minmax(0,1fr) !important;
                    grid-template-areas: "context" "actions" "search" "filter" !important;
                    row-gap: 11px !important;
                }
                .master-vault-page .master-vault-v11-actions { justify-content: flex-start; }
                .master-vault-page .master-vault-v11-folder-button,
                .master-vault-page .master-vault-v9-create { flex: 1 1 0; }
                .master-vault-page .master-vault-v9-filter { width: 100%; }
                .master-vault-page .master-vault-v11-folder-grid { grid-template-columns: minmax(0,1fr) !important; gap: 13px !important; }
                .master-vault-page .master-vault-v11-folder-section { margin-top: 21px !important; }
                .master-vault-page .master-vault-v11-folder-head { align-items: center; flex-direction: row; }
                .master-vault-page .master-vault-v11-folder-head h2 { font-size: 22px !important; }
                .master-vault-page .master-vault-v11-folder-badge { font-size: 10px; }
                .master-vault-page .master-vault-v11-folder-card { min-height: 164px !important; }
            }
            @media (max-width: 385px) {
                .master-vault-page .master-vault-v12-brand-copy h1,
                .master-vault-page .master-vault-v14-folder-copy h1 { font-size: 25px !important; }
                .master-vault-page .master-vault-v11-folder-head h2 { font-size: 20px !important; }
                .master-vault-page .master-vault-v11-folder-avatar { width: 34px !important; height: 34px !important; flex-basis: 34px !important; }
            }
            @media (prefers-reduced-motion: reduce) {
                .master-vault-page .master-vault-v11-folder-card,
                .master-vault-page .master-vault-v11-folder-avatar img,
                .master-vault-page .master-vault-v11-folder-button,
                .master-vault-page .master-vault-v9-create { transition: none !important; }
                .master-vault-page .master-vault-v11-folder-card:hover { transform: none !important; }
            }


            .master-vault-page .master-vault-view-controls {
                grid-area: filter;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 9px;
                min-width: 0;
            }
            .master-vault-page .master-vault-view-controls .master-vault-v9-filter {
                grid-area: auto !important;
                flex: 0 0 auto;
                width: clamp(120px, 11vw, 165px);
            }
            .master-vault-page .master-vault-view-controls .master-vault-display-toggle {
                min-height: 43px;
            }
            @media(max-width: 990px) and (min-width: 721px) {
                .master-vault-page .master-vault-v14-topbar {
                    grid-template-columns: minmax(0,1fr) auto !important;
                }
            }
            @media(max-width: 720px) {
                .master-vault-page .master-vault-view-controls { justify-content: space-between; }
                .master-vault-page .master-vault-view-controls .master-vault-v9-filter { flex: 1 1 auto; width: auto; }
            }
            /* Uma bandeja de destino evita perder o arrastar quando pastas e itens estão em abas diferentes. */
            .master-vault-page .master-vault-drag-destinations {
                position: fixed;
                z-index: 105;
                top: 50%;
                right: clamp(12px, 2vw, 30px);
                transform: translateY(-50%);
                width: min(325px, calc(100vw - 24px));
                max-height: min(74vh, 620px);
                overflow: auto;
                border: 1px solid #d5bca0;
                border-radius: 21px;
                background: #fbf7ef;
                box-shadow: 0 16px 52px rgba(49, 27, 14, .19);
                padding: 17px;
                scrollbar-width: thin;
                scrollbar-color: #c3a27e transparent;
            }
            .master-vault-page .master-vault-drag-destinations h3 {
                margin: 0;
                color: #6b231a;
                font: 700 21px/1.15 Georgia, serif;
            }
            .master-vault-page .master-vault-drag-destinations p {
                margin: 5px 0 13px;
                color: #96704e;
                font-size: 11px;
            }
            .master-vault-page .master-vault-drop-target {
                display: flex;
                align-items: center;
                gap: 10px;
                width: 100%;
                min-height: 48px;
                margin-top: 7px;
                padding: 9px 12px;
                border: 1px solid rgba(192,157,119,.42);
                border-radius: 12px;
                background: rgba(255,255,255,.65);
                color: #5c3428;
                font: 700 12px Georgia, serif;
                text-align: left;
                transition: background .15s ease, border-color .15s ease, transform .15s ease;
            }
            .master-vault-page .master-vault-drop-target:hover,
            .master-vault-page .master-vault-drop-target.is-over {
                background: #f0e7db;
                border-color: #ae7e5e;
                transform: translateX(-2px);
            }
            .master-vault-page .master-vault-drop-target svg { width: 18px; height: 18px; flex: none; }
            .master-vault-page .master-vault-drop-target span { min-width: 0; overflow: hidden; text-overflow: ellipsis; }
            .master-vault-page .master-vault-drop-target--library { border-style: dashed; }
            @media(prefers-reduced-motion: reduce) {
                .master-vault-page .master-vault-drop-target { transition: none; transform: none !important; }
            }

            /* Spellbound V66.1.13 — visualização conjunta e separada (mesma linguagem do Cofre de NPCs). */
            .master-vault-page .master-vault-display-row {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 12px;
                min-height: 48px;
                margin-top: 12px;
                padding: 0 0 9px;
                border-bottom: 1px solid rgba(174, 137, 97, .22);
            }
            .master-vault-page .master-vault-display-row.is-combined {
                justify-content: flex-end;
                min-height: 41px;
            }
            .master-vault-page .master-vault-display-tabs {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 22px;
                min-width: 0;
            }
            .master-vault-page .master-vault-display-tab {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                margin-bottom: -10px;
                padding: 10px 2px 12px;
                background: none;
                border: 0;
                border-bottom: 2px solid transparent;
                color: #997659;
                font-family: inherit;
                font-weight: 650;
                font-size: 12px;
                cursor: pointer;
                transition: color .18s ease, border-color .18s ease;
            }
            .master-vault-page .master-vault-display-tab:hover { color: #74281f; }
            .master-vault-page .master-vault-display-tab.is-active {
                color: #6b2119;
                border-bottom-color: #792e26;
            }
            .master-vault-page .master-vault-display-tab span {
                min-width: 21px;
                padding: 3px 6px;
                border-radius: 8px;
                border: 1px solid rgba(183,146,109,.23);
                background: #f3eae0;
                color: #9b6f4a;
                font: 700 10px/1.2 Georgia, serif;
                text-align: center;
            }
            .master-vault-page .master-vault-display-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 38px;
                border: 1px solid #d6b99e;
                border-radius: 12px;
                padding: 0 12px;
                color: #773226;
                background: rgba(255,251,245,.8);
                font: 700 12px Georgia, serif;
                white-space: nowrap;
                cursor: pointer;
                transition: background .2s ease, border-color .2s ease, transform .2s ease;
            }
            .master-vault-page .master-vault-display-toggle svg {
                width: 15px;
                height: 15px;
                flex: none;
            }
            .master-vault-page .master-vault-display-toggle:hover {
                border-color: #b58e6d;
                background: #fffdfa;
                transform: translateY(-1px);
            }
            .master-vault-page .master-vault-display-toggle:focus-visible,
            .master-vault-page .master-vault-display-tab:focus-visible {
                outline: 2px solid #94674a;
                outline-offset: 4px;
            }
            .master-vault-page .master-vault-display-row + .master-vault-v9-results {
                margin-top: 22px !important;
            }
            .master-vault-page .master-vault-v9-results.vault-overview-items {
                margin-top: 19px !important;
            }
            .master-vault-page .master-vault-v11-folder-section.vault-overview-folders {
                margin-top: 26px !important;
            }
            .master-vault-page .master-vault-v11-folder-section.vault-overview-folders.is-separated {
                margin-top: 18px !important;
            }
            .master-vault-page .master-vault-v9-grid.vault-unassigned-grid {
                margin-top: 14px !important;
            }
            .master-vault-page .master-vault-v11-folder-head.is-separated { display: none !important; }
            .master-vault-page .master-vault-v11-folder-grid.is-separated { margin-top: 0 !important; }
            @media(max-width: 700px) {
                .master-vault-page .master-vault-display-row { align-items: center; gap: 7px; }
                .master-vault-page .master-vault-display-tabs { gap: 12px; }
                .master-vault-page .master-vault-display-tab { font-size: 11px; padding: 8px 0 11px; }
                .master-vault-page .master-vault-display-toggle { min-height: 34px; padding: 0 9px; font-size: 11px; }
            }
            @media(max-width: 400px) {
                .master-vault-page .master-vault-display-row { flex-direction: column-reverse; align-items: stretch; }
                .master-vault-page .master-vault-display-toggle { align-self: flex-end; }
            }
            @media(prefers-reduced-motion: reduce) {
                .master-vault-page .master-vault-display-tab,
                .master-vault-page .master-vault-display-toggle { transition: none; }
            }


            /* V66.1.14 — alinhamento com o Cofre de NPCs e cartas com imagens maiores */
            .master-vault-page .master-vault-v12-brand-copy p { display: none !important; }
            .master-vault-page .master-vault-v14-topbar {
                row-gap: 12px !important;
                column-gap: 14px !important;
            }
            .master-vault-page .master-vault-v12-brand-copy h1 { font-size: clamp(29px, 3vw, 38px) !important; }
            .master-vault-page .master-vault-v12-brand-copy::after { margin-top: 9px !important; }
            .master-vault-page .master-vault-v9-search input {
                border-radius: 15px !important;
                background: #fffcf7 !important;
                box-shadow: 0 5px 13px rgba(65,43,30,.035);
            }
            .master-vault-page .master-vault-v11-folder-card { border-radius: 17px !important; }
            .master-vault-page .master-vault-v9-grid {
                grid-template-columns: repeat(auto-fill, minmax(min(400px,100%), 1fr)) !important;
                gap: 17px !important;
                align-items: stretch;
            }
            .master-vault-page .master-vault-v10-card {
                display: grid;
                grid-template-columns: 144px minmax(0,1fr);
                grid-template-rows: auto minmax(0,1fr) auto;
                min-height: 198px;
                isolation: isolate;
                border: 1px solid #dfc9af !important;
                border-radius: 19px !important;
                background: #fffaf3 !important;
                box-shadow: 0 6px 16px rgba(68,39,24,.05) !important;
                transition: transform .26s cubic-bezier(.22,1,.36,1), box-shadow .26s ease, border-color .26s ease;
            }
            .master-vault-page .master-vault-v10-card.is-dragging { opacity: .5; transform: scale(.985) rotate(-.4deg); }
            .master-vault-page .master-vault-v10-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 29px rgba(75,41,24,.11) !important;
            }
            .master-vault-page .master-vault-v10-card::before { display:none; }
            .master-vault-page .master-vault-v10-card-main { display: contents; }
            .master-vault-page .master-vault-v10-thumb {
                width: 100% !important;
                height: 100% !important;
                min-height: 194px !important;
                grid-column: 1;
                grid-row: 1 / 4;
                margin: 0 !important;
                padding: 8px !important;
                border: 0 !important;
                border-right: 1px solid #e1cdb7 !important;
                border-radius: 18px 0 0 18px !important;
                background: radial-gradient(ellipse at 50% 39%, #f7ecdf, #e9d8c4) !important;
                box-shadow: inset 0 0 0 5px rgba(255,255,255,.14);
                overflow: hidden;
            }
            .master-vault-page .master-vault-v10-thumb img {
                width: 100%; height: 100%; object-fit: contain !important;
                filter: drop-shadow(0 6px 6px rgba(57,35,24,.14));
                transition: transform .38s cubic-bezier(.2,.8,.2,1);
            }
            .master-vault-page .master-vault-v10-card:hover .master-vault-v10-thumb img { transform: scale(1.065) translateY(-2px); }
            .master-vault-page .master-vault-v10-identity {
                grid-column: 2; grid-row: 1;
                padding: 17px 60px 8px 17px;
                min-height: 95px;
                align-self: start;
            }
            .master-vault-page .master-vault-v10-name {
                white-space: normal !important;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                font-size: 17px !important;
                line-height: 1.22 !important;
                letter-spacing: -.013em;
            }
            .master-vault-page .master-vault-v10-nature { margin-top: 7px; }
            .master-vault-page .master-vault-v10-top-actions {
                position: absolute; right: 9px; top: 9px; z-index: 12;
            }
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section {
                grid-column: 2;
                min-width: 0;
                margin: 0 14px 0 16px !important;
                padding: 9px 0 12px !important;
                border-top: 1px solid rgba(181,147,110,.23) !important;
                background: transparent !important;
            }
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section:last-of-type {
                margin-bottom: 4px !important;
            }
            .master-vault-page .master-vault-v10-description {
                display: -webkit-box !important;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3 !important;
                overflow: hidden !important;
                max-height: none !important;
                color: #6e5140; font-size: 11px; line-height: 1.53;
            }
            .master-vault-page .master-vault-v10-abilities { gap: 4px; }
            .master-vault-page .master-vault-v10-ability { min-height: 22px; font-size: 9px; }
            /* Vestígios — brilho discreto e aura dourada animada sem ocultar menus. */
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"]) {
                border-color: #c6a369 !important;
                box-shadow: 0 0 0 1px rgba(196,156,83,.27), 0 8px 27px rgba(138,105,47,.16) !important;
            }
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"])::after {
                content: ''; pointer-events: none; position: absolute; inset: -3px; z-index: -1;
                border-radius: 22px;
                background: conic-gradient(from var(--vestige-angle,0deg), transparent 0deg, rgba(212,170,87,.72) 55deg, transparent 95deg, rgba(230,193,111,.4) 190deg, transparent 265deg, rgba(212,170,87,.55) 330deg, transparent 360deg);
                opacity: .6; filter: blur(5px); animation: vault-vestige-aura 7s linear infinite;
            }
            @property --vestige-angle { syntax:'<angle>'; inherits:false; initial-value:0deg; }
            @keyframes vault-vestige-aura { to { --vestige-angle: 360deg; } }
            .master-vault-page .vault-vestige-badge {
                display: inline-flex; align-items: center; gap: 4px;
                margin-bottom: 5px; padding: 3px 7px; border: 1px solid #e0c48d;
                border-radius: 20px; color: #875d22; background: #fff6df;
                font: 800 9px Georgia,serif;
            }
            /* V66.1.16 — Vestígio integrado ao editor de habilidades existente. */
            .master-vault-page .vault-v16-mode-switch {
                display:flex; align-items:center; justify-content:space-between; gap:16px;
                border:1px solid rgba(185,148,105,.39); border-radius:14px;
                background:linear-gradient(112deg,rgba(251,246,238,.96),rgba(242,232,215,.62));
                padding:13px 16px; margin:4px 0 14px;
            }
            .master-vault-page .vault-v16-mode-switch__text {min-width:0;}
            .master-vault-page .vault-v16-mode-switch__eyebrow {
                display:block; margin-bottom:3px; font:800 9px system-ui,sans-serif;
                letter-spacing:.1em; text-transform:uppercase; color:#997854;
            }
            .master-vault-page .vault-v16-mode-switch strong {
                display:block; font:700 18px/1.2 Georgia,serif; color:#6a291d;
            }
            .master-vault-page .vault-v16-mode-switch p {
                margin:4px 0 0; font:400 11px/1.4 system-ui,sans-serif; color:#84654d;
            }
            .master-vault-page .vault-v16-mode-switch .master-vault-v4-switch {flex:0 0 auto;}
            .master-vault-page .vault-v16-stages {
                margin:0 13px 4px; border:1px solid rgba(198,165,119,.38);
                border-radius:14px; overflow:hidden;
                background:linear-gradient(160deg,rgba(255,251,244,.99),rgba(247,238,225,.68));
            }
            .master-vault-page .vault-v16-stages__tabs {
                display:grid; grid-template-columns:repeat(3,minmax(0,1fr));
                gap:0; background:rgba(237,226,210,.36);
                border-bottom:1px solid rgba(198,165,119,.35);
            }
            .master-vault-page .vault-v16-stages__tab {
                position:relative; display:flex; flex-wrap:wrap; align-items:center;
                justify-content:center; gap:4px 8px; min-width:0; min-height:60px;
                padding:10px 8px; cursor:pointer; border:0;
                border-right:1px solid rgba(198,165,119,.3);
                color:#87664c; background:transparent;
                transition:background .23s ease,color .23s ease;
            }
            .master-vault-page .vault-v16-stages__tab:last-child {border-right:0;}
            .master-vault-page .vault-v16-stages__tab strong {font:700 15px/1.2 Georgia,serif;}
            .master-vault-page .vault-v16-stages__tab small {font:600 9px/1.2 system-ui,sans-serif; opacity:.75;}
            .master-vault-page .vault-v16-stages__tab-count {
                display:inline-flex; align-items:center; justify-content:center;
                min-width:20px; height:20px; border:1px solid rgba(174,132,79,.3);
                border-radius:999px; font:700 10px system-ui,sans-serif;
                background:rgba(255,255,255,.6);
            }
            .master-vault-page .vault-v16-stages__tab.is-active {
                background:rgba(255,252,247,.96); color:#6b2119;
                box-shadow:inset 0 -3px 0 #b8935c;
            }
            .master-vault-page .vault-v16-stages__tab:hover:not(.is-active) {background:rgba(255,255,255,.42);}
            .master-vault-page .vault-v16-stages__details {padding:15px 16px 13px;}
            .master-vault-page .vault-v16-stages__edit-intro {
                display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:12px;
            }
            .master-vault-page .vault-v16-stages__edit-intro > div {display:flex; flex-direction:column; gap:2px;}
            .master-vault-page .vault-v16-stages__edit-intro strong {
                color:#642a1f; font:700 15px Georgia,serif;
            }
            .master-vault-page .vault-v16-stages__edit-intro small {
                color:#9a795d; font:600 10px system-ui,sans-serif;
            }
            .master-vault-page .vault-v16-stages__master-select {display:flex; align-items:center; gap:9px;}
            .master-vault-page .vault-v16-stages__master-select > span {
                color:#8d6d53; font:700 10px system-ui,sans-serif; white-space:nowrap;
            }
            .master-vault-page .vault-v16-stages__master-select select {width:150px; min-height:34px; padding:5px 7px;}
            .master-vault-page .vault-v16-stages__lore {display:block;}
            .master-vault-page .vault-v16-stages__lore textarea {min-height:74px; max-height:170px; resize:vertical;}
            .master-vault-page .vault-v16-stages__hint {
                margin:8px 0 0; color:#94735b; font:500 10px/1.45 system-ui,sans-serif;
            }
            @media(max-width:650px) {
                .master-vault-page .vault-v16-stages {margin:0 8px 3px;}
                .master-vault-page .vault-v16-stages__tabs {grid-template-columns:repeat(3,minmax(0,1fr));}
                .master-vault-page .vault-v16-stages__tab {min-height:66px; gap:4px; padding:7px 4px; flex-direction:column;}
                .master-vault-page .vault-v16-stages__tab strong {font-size:12px;}
                .master-vault-page .vault-v16-stages__tab small {font-size:8px;}
                .master-vault-page .vault-v16-stages__details {padding:12px;}
                .master-vault-page .vault-v16-stages__edit-intro {align-items:stretch; flex-direction:column; gap:10px;}
                .master-vault-page .vault-v16-stages__master-select {justify-content:space-between;}
                .master-vault-page .vault-v16-mode-switch {padding:11px 12px;}
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .vault-v16-stages__tab {transition:none;}
            }
            /* A gaveta de arraste usa a mesma linguagem e easing do Cofre de NPCs. */
            .master-vault-page .master-vault-drag-destinations {
                top: 14px !important; bottom: 14px !important; right: 14px !important;
                transform: translateX(0) !important; width: min(340px, calc(100vw - 50px));
                max-height: none !important; height: auto !important; padding: 0 !important;
                display:flex; flex-direction:column; overflow:hidden;
                border:1px solid #dbc7ae; border-radius:19px;
                background:#fbf7ef; box-shadow:0 24px 50px rgba(45,26,13,.2);
            }
            .master-vault-page .master-vault-drag-destinations[x-cloak] {display:none !important;}
            .master-vault-page .vault-drawer-transition { transition: transform .29s cubic-bezier(.22,.7,.23,1), opacity .23s ease !important; }
            .master-vault-page .vault-drawer-hidden { transform: translateX(calc(100% + 24px)) !important; opacity: 0 !important; }
            .master-vault-page .vault-drawer-visible { transform: translateX(0) !important; opacity: 1 !important; }
            .master-vault-page .master-vault-drag-destinations h3 {font:700 24px/1.15 Georgia,serif;color:#6b2119;}
            .master-vault-page .vault-drop-drawer__head {padding:23px 19px 15px;border-bottom:1px solid #e5d7c5;flex:0 0 auto;}
            .master-vault-page .vault-drop-drawer__eyebrow {display:block;margin-bottom:8px;color:#a17b54;font:700 9px system-ui,sans-serif;letter-spacing:.19em;}
            .master-vault-page .vault-drop-drawer__selected {display:flex;align-items:center;gap:9px;margin-top:15px;padding:11px 12px;border:1px solid #e6d7c4;border-radius:11px;background:#fffaf2;}
            .master-vault-page .vault-drop-drawer__selected span {font-size:19px;color:#b48d5c;}
            .master-vault-page .vault-drop-drawer__selected strong {min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#5c3028;font:700 13px Georgia,serif;}
            .master-vault-page .vault-drop-drawer__destinations {flex:1;min-height:0;overflow-y:auto;padding:12px 11px;scrollbar-width:thin;scrollbar-color:#bea489 transparent;}
            .master-vault-page .master-vault-drop-target {min-height:57px;padding:11px 12px;border-radius:12px;background:#fffcf6;transition:background .16s,border-color .16s,transform .16s,box-shadow .16s;}
            .master-vault-page .master-vault-drop-target:hover, .master-vault-page .master-vault-drop-target.is-over {transform:translateX(-3px);border-color:#a57b57;background:#f2e5d3;box-shadow:0 4px 12px rgba(56,34,22,.1);}
            .master-vault-page .vault-drop-drawer__foot {padding:14px;border-top:1px solid #e1d2bd;text-align:center;background:#f3e8db;color:#906f52;font:600 11px system-ui,sans-serif;}
            @media(max-width:700px) {
                .master-vault-page .master-vault-v9-grid {grid-template-columns:minmax(0,1fr)!important;}
                .master-vault-page .master-vault-v10-card {grid-template-columns:126px minmax(0,1fr);min-height:174px;}
                .master-vault-page .master-vault-v10-thumb {min-height:174px!important;}
                .master-vault-page .master-vault-v10-identity {padding:14px 60px 6px 12px;min-height:85px;}
                .master-vault-page .master-vault-v10-name {font-size:15px!important;}
                .master-vault-page .master-vault-drag-destinations {top:7px!important;bottom:7px!important;right:7px!important;width:min(300px,calc(100vw - 58px));}
            }
            @media(max-width:410px) {
                .master-vault-page .master-vault-v10-card {grid-template-columns:100px minmax(0,1fr);min-height:170px;}
                .master-vault-page .master-vault-v10-thumb {min-height:170px!important;padding:3px!important;}
                .master-vault-page .master-vault-v10-identity {padding-right:52px;min-height:100px;}
                .master-vault-page .master-vault-v10-card > .master-vault-v10-section {margin-left:10px!important;margin-right:9px!important;}
            }
            @media(prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card, .master-vault-page .master-vault-v10-thumb img, .master-vault-page .master-vault-drop-target, .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"])::after, .master-vault-page .vault-drawer-transition {transition:none!important;animation:none!important;}
            }

            /* ==========================================================
               SPELLBOUND V66.1.18 — EDITOR DE ITENS / VISUAL ORGANIZADO
               Regras escopadas para a modal; nenhuma alteração na biblioteca.
               ========================================================== */
            .vault-v18-workspace {
                display: grid;
                grid-template-columns: 238px minmax(0, 1fr);
                align-items: start;
                gap: 21px;
            }
            .vault-v18-sidebar {position:sticky;top:0;display:grid;gap:13px;min-width:0;}
            .vault-v18-sidebar-card {
                border:1px solid #ddcdb9;border-radius:16px;
                background:linear-gradient(150deg,#f7efe3,#f1e7d8);
                padding:14px;box-shadow:0 3px 10px rgba(88,51,29,.04);
            }
            .vault-v18-photo-heading {display:flex;justify-content:space-between;align-items:center;gap:6px;margin-bottom:11px;}
            .vault-v18-overline {
                display:block;color:#966d47;font:800 9px/1.3 system-ui,sans-serif;
                letter-spacing:.12em;text-transform:uppercase;
            }
            .vault-v18-photo-note {color:#aa8c70;font:700 8px system-ui,sans-serif;white-space:nowrap;}
            .vault-v18-photo {width:100%;}
            .vault-v18-photo .master-vault-v4-photo-frame {
                border:1px dashed #c9a781;border-radius:12px;
                background:radial-gradient(circle at 48% 34%,#faf7ef,#eae0d1);
            }
            .vault-v18-photo .master-vault-v4-photo-frame:hover {box-shadow:0 0 0 3px rgba(111,37,25,.05);}
            .vault-v18-photo .master-vault-v4-photo-remove {margin-top:9px;}
            .vault-v18-sidebar-preview {min-width:0;margin-top:14px;padding:12px 2px 0;border-top:1px solid #dccbb5;}
            .vault-v18-sidebar-preview strong {
                display:block;margin-top:6px;overflow:hidden;text-overflow:ellipsis;
                color:#63271d;font:700 16px/1.3 Georgia,serif;overflow-wrap:anywhere;
            }
            .vault-v18-sidebar-preview small {display:block;margin-top:4px;color:#91745a;font:650 10px/1.5 system-ui,sans-serif;}
            .vault-v18-nav {display:grid;gap:2px;border:1px solid #e4d5c3;border-radius:14px;background:#fcf8f1;padding:13px 9px;}
            .vault-v18-nav > p {padding:0 9px;margin:1px 0 7px;}
            .vault-v18-nav button {
                display:flex;align-items:center;gap:10px;min-height:34px;
                padding:6px 10px;border:0;border-radius:8px;background:transparent;
                color:#70523f;text-align:left;font:750 11px/1.3 system-ui,sans-serif;
                cursor:pointer;transition:color .16s,background .16s;
            }
            .vault-v18-nav button span {min-width:18px;color:#ac8866;font:800 9px system-ui,sans-serif;}
            .vault-v18-nav button:hover,.vault-v18-nav button:focus-visible {background:#f0e4d5;color:#6b1d14;outline:none;}
            .vault-v18-form {display:grid;gap:15px;min-width:0;}
            .vault-v18-card {
                min-width:0;border:1px solid #e1cfb9;border-radius:16px;
                background:linear-gradient(145deg,#fffdf9 0%,#faf6ef 100%);
                padding:20px 22px 22px;box-shadow:0 4px 15px rgba(80,45,23,.045);
                scroll-margin-top:12px;
            }
            .vault-v18-card-heading {display:flex;align-items:flex-start;gap:12px;margin-bottom:19px;padding-bottom:15px;border-bottom:1px solid #eadbca;}
            .vault-v18-step {
                display:inline-flex;flex:0 0 auto;align-items:center;justify-content:center;
                width:32px;height:32px;border:1px solid #d8bfa4;border-radius:9px;
                background:#f2e6d7;color:#7d3427;font:800 11px system-ui,sans-serif;
            }
            .vault-v18-card-heading h3 {margin:0;color:#63251b;font:700 19px/1.22 Georgia,serif;}
            .vault-v18-card-heading p {margin:4px 0 0;color:#92745b;font:500 10.5px/1.5 system-ui,sans-serif;}
            .vault-v18-fields {display:grid;gap:14px;min-width:0;}
            .vault-v18-fields--identity {grid-template-columns:minmax(0,1.8fr) minmax(200px,1fr);}
            .vault-v18-group {margin:0 0 20px;}
            .vault-v18-nature-options {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;}
            .vault-v18-nature-option {
                display:flex;align-items:center;gap:10px;min-height:69px;
                padding:11px 12px;border:1px solid #e1d0bb;border-radius:11px;
                background:#fcf9f4;color:#7d5c46;text-align:left;
                cursor:pointer;transition:border-color .16s,background .16s,box-shadow .16s;
            }
            .vault-v18-nature-option:hover {border-color:#bda082;background:#f8f0e6;}
            .vault-v18-nature-option.is-active {border-color:#a66e5f;background:#f0e3da;box-shadow:inset 0 0 0 1px rgba(107,29,20,.11);}
            .vault-v18-nature-mark {
                display:flex;align-items:center;justify-content:center;flex:0 0 33px;
                width:33px;height:33px;border:1px solid #e2d0bd;border-radius:9px;
                background:#f4e9dd;color:#916b48;font:20px Georgia,serif;
            }
            .vault-v18-nature-option.is-active .vault-v18-nature-mark {border-color:#c99e8e;background:#e6d1c6;color:#762c22;}
            .vault-v18-nature-option strong {display:block;color:#683026;font:800 11px/1.35 system-ui,sans-serif;}
            .vault-v18-nature-option small {display:block;margin-top:3px;color:#96765f;font:500 9px/1.35 system-ui,sans-serif;}
            .vault-v18-classification-fields {grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;align-items:start;}
            .vault-v18-classification-fields > * {min-width:0;}
            .vault-v18-yes-no {gap:5px;}
            .vault-v18-yes-no .master-vault-v4-choice {min-height:45px;}
            .vault-v18-field-group {min-width:0;}
            .vault-v18-field-help {display:block;margin-top:7px;color:#9a7a62;font:500 10px/1.5 system-ui,sans-serif;}
            .vault-v18-classification-fields .master-vault-v4-toggle-card,
            .vault-v18-inventory-fields .master-vault-v4-toggle-card {min-height:46px;}
            .vault-v18-vestige {
                display:flex;align-items:center;justify-content:space-between;gap:15px;
                margin:18px 0 0;padding:13px 15px;border:1px solid #d7be9e;
                border-radius:12px;background:linear-gradient(110deg,#faf1de,#f3e5d2);
            }
            .vault-v18-vestige .vault-v16-mode-switch__eyebrow {
                display:block;margin:0 0 3px;color:#986b39;
                font:800 9px/1.3 system-ui,sans-serif;letter-spacing:.1em;text-transform:uppercase;
            }
            .vault-v18-vestige .vault-v16-mode-switch__text strong {display:block;color:#692d20;font:700 15px Georgia,serif;}
            .vault-v18-vestige .vault-v16-mode-switch__text p {margin:3px 0 0;color:#917352;font:500 10px/1.45 system-ui,sans-serif;}
            .vault-v18-vestige .master-vault-v4-switch {flex:0 0 auto;}
            .vault-v18-inventory-fields {grid-template-columns:repeat(3,minmax(0,1fr));align-items:start;}
            .vault-v18-card--description .vault-v18-description-field textarea {min-height:154px;}
            .vault-v18-tags-field {display:block;margin-top:16px;}
            .vault-v18-details-intro {
                margin:27px 0 16px;padding:17px 21px;
                border:1px solid #dfcab2;border-left:4px solid #8c4838;
                border-radius:13px;background:linear-gradient(100deg,#f7ebdd,#fbf7f0);
            }
            .vault-v18-details-intro h3 {margin:4px 0 0;color:#64271e;font:700 21px/1.25 Georgia,serif;}
            .vault-v18-details-intro p {margin:5px 0 0;color:#907058;font:500 11px/1.5 system-ui,sans-serif;}
            .vault-v18-mechanics,.master-vault-v5-abilities[x-ref="vaultEditorAbilities"],
            .master-vault-v4-secret[x-ref="vaultEditorSecrets"] {scroll-margin-top:12px;}
            @media (max-width:1140px) {
                .vault-v18-workspace {grid-template-columns:205px minmax(0,1fr);gap:14px;}
                .vault-v18-card {padding:17px 17px 19px;}
                .vault-v18-nature-option {padding:10px 8px;gap:7px;}
                .vault-v18-nature-mark {flex-basis:28px;width:28px;height:28px;}
                .vault-v18-classification-fields {grid-template-columns:repeat(2,minmax(0,1fr));}
            }
            @media (max-width:860px) {
                .vault-v18-workspace {grid-template-columns:minmax(0,1fr);}
                .vault-v18-sidebar {position:static;grid-template-columns:140px minmax(0,1fr);}
                .vault-v18-sidebar-card {display:flex;align-items:center;gap:14px;padding:10px;}
                .vault-v18-photo-heading,.vault-v18-sidebar-preview {display:none;}
                .vault-v18-photo {width:90px;flex:0 0 90px;}
                .vault-v18-nav {align-content:center;grid-template-columns:repeat(3,minmax(0,1fr));gap:3px;}
                .vault-v18-nav > p {grid-column:1/-1;margin:0 0 4px;}
                .vault-v18-nav button {min-height:29px;padding:4px 7px;font-size:10px;}
            }
            @media (max-width:620px) {
                .vault-v18-sidebar {grid-template-columns:1fr;}
                .vault-v18-sidebar-card {justify-content:center;}
                .vault-v18-nav {grid-template-columns:repeat(2,minmax(0,1fr));}
                .vault-v18-fields--identity,.vault-v18-classification-fields,.vault-v18-inventory-fields {grid-template-columns:1fr;}
                .vault-v18-nature-options {grid-template-columns:1fr;gap:6px;}
                .vault-v18-nature-option {min-height:51px;}
                .vault-v18-nature-option small {font-size:9px;}
                .vault-v18-card {padding:14px;}
                .vault-v18-card-heading {margin-bottom:14px;padding-bottom:12px;}
                .vault-v18-card-heading h3 {font-size:17px;}
                .vault-v18-details-intro {padding:14px;}
            }
            @media (prefers-reduced-motion:reduce) {
                .vault-v18-nav button,.vault-v18-nature-option {transition:none;}
            }


            /* ========================================================== 
               V66.1.19 — COFRE DE ITENS | REFINAMENTO E GAVETAS
               Somente estilos do editor. Paleta do Spellbound preservada.
            ========================================================== */
            .vault-v18-sidebar {position:sticky;top:0;align-self:start;gap:0;}
            .vault-v18-sidebar-card {
                background:transparent; border:0; border-radius:0; padding:0;
                box-shadow:none;
            }
            .vault-v18-photo .master-vault-v4-photo-frame {
                aspect-ratio:1/1; border-radius:15px; border:1px dashed #bd9e7d;
                box-shadow:0 5px 14px rgba(70,39,23,.05);
            }
            .vault-v18-photo .master-vault-v4-photo-frame:focus-within {outline:2px solid #8a473c;outline-offset:3px;}
            .vault-v18-workspace {grid-template-columns:205px minmax(0,1fr);gap:19px;}
            .vault-v18-form {gap:13px;}
            .vault-v19-mechanics-heading {
                display:flex;gap:14px;align-items:center;margin:27px 0 17px;
                padding:17px 20px;border:1px solid #b9c9bf;border-left:4px solid #527565;
                border-radius:14px;background:linear-gradient(110deg,#e5efe9 0%,#f2f5eb 60%,#faf8ef 100%);
                box-shadow:0 3px 12px rgba(60,92,76,.07);
            }
            .vault-v19-mechanics-heading h3 {margin:0;color:#365c4d;font:700 21px/1.2 Georgia,serif;}
            .vault-v19-mechanics-heading p {color:#637f70;margin-top:5px;}
            .vault-v19-mechanics-emblem {
                display:flex;align-items:center;justify-content:center;flex:0 0 42px;
                height:42px;border:1px solid #b6cbbc;border-radius:12px;
                background:#f8fbf5;color:#527565;
            }
            .vault-v19-mechanics-emblem svg {width:23px;height:23px;}

            /* Categoria geral: opções compactas, legíveis e com estado de seleção. */
            .vault-v19-category-grid {gap:9px;}
            .vault-v19-category-grid .master-vault-v5-category {
                position:relative;align-items:center;min-height:88px;border:1px solid #d8c6ae;
                border-radius:12px;background:#fffcf7;
                box-shadow:0 2px 7px rgba(73,38,23,.03);
                transition:border-color .15s,background .15s,box-shadow .15s,transform .15s;
            }
            .vault-v19-category-grid .master-vault-v5-category:hover {
                transform:translateY(-1px);border-color:#b9956c;background:#fff8ed;
                box-shadow:0 6px 13px rgba(71,37,19,.065);
            }
            .vault-v19-category-grid .master-vault-v5-category.is-active {
                border-color:#975f49;background:linear-gradient(135deg,#f8eee4,#f0e2d8);
                box-shadow:inset 0 0 0 1px rgba(124,59,42,.12),0 5px 13px rgba(98,48,24,.07);
            }
            .vault-v19-category-grid .master-vault-v5-category.is-active:after {
                content:'✓';position:absolute;right:11px;top:9px;display:grid;place-items:center;
                width:17px;height:17px;border-radius:50%;background:#6b1d14;color:#fff;font:800 11px system-ui,sans-serif;
            }
            .vault-v19-category-grid .master-vault-v5-category-icon {
                flex-shrink:0;border:1px solid #dfc7ae;border-radius:11px;
                background:#f7eddf;color:#916b46;
            }
            .vault-v19-category-grid .master-vault-v5-category.is-active .master-vault-v5-category-icon {
                border-color:#d1ab98;background:#f0d9c9;color:#7f3327;
            }
            .vault-v19-equipment {
                overflow:hidden;border:1px solid #dec9ad;border-radius:16px;
                background:linear-gradient(155deg,#fffefa,#faf4e9);
                box-shadow:0 4px 16px rgba(83,46,18,.045);
            }
            .vault-v19-equipment .master-vault-v4-module-head {
                background:linear-gradient(100deg,#f2e7d8,#faf6ed);
                border-bottom:1px solid #e3d1b8;padding:14px 18px;
            }
            .vault-v19-equipment .master-vault-v4-module-head h3 {color:#612619;}
            .vault-v19-equipment .master-vault-v4-module-body {display:grid;gap:12px;padding:15px 18px;}
            .vault-v19-equipment .master-vault-v4-rule {
                margin:0;padding:15px 17px;border:1px solid #e4d4bc;border-radius:12px;
                background:rgba(255,254,250,.86);
            }
            .vault-v19-options {gap:8px;}
            .vault-v19-options .vault-v19-option {
                display:flex;min-width:0;min-height:66px;align-items:flex-start;
                justify-content:center;flex-direction:column;gap:3px;text-align:left;
                padding:10px 14px 10px 36px;position:relative;
                border:1px solid #ddc8b1;border-radius:11px;background:#fdfaf5;
                box-shadow:0 2px 6px rgba(75,44,27,.025);
                color:#76553e;transition:border-color .14s,background .14s,box-shadow .14s;
            }
            .vault-v19-options .vault-v19-option:before {
                content:'';position:absolute;left:12px;top:18px;width:14px;height:14px;
                border:1.5px solid #bda48c;border-radius:50%;background:#fffaf3;
            }
            .vault-v19-options .vault-v19-option strong {
                display:block;font:850 11px/1.25 system-ui,sans-serif;color:#6d3627;
            }
            .vault-v19-options .vault-v19-option small {
                display:block;margin:0;color:#997d66;font:500 9px/1.35 system-ui,sans-serif;
            }
            .vault-v19-options .vault-v19-option:hover {
                border-color:#b58a6a;background:#f9eee2;
            }
            .vault-v19-options .vault-v19-option.is-active {
                border-color:#9d6a55;background:linear-gradient(145deg,#f4e7de,#f9f0e7);
                box-shadow:inset 0 0 0 1px rgba(107,29,20,.1);
            }
            .vault-v19-options .vault-v19-option.is-active:before {
                border-color:#793025;background:#793025;box-shadow:inset 0 0 0 3px #f4e7de;
            }
            .vault-v19-options .vault-v19-option.is-active strong {color:#6b1d14;}
            .vault-v19-bonus-help {
                display:block;margin:2px 0 7px;color:#9a795d;
                font:500 9px/1.4 system-ui,sans-serif;
            }

            /* Três gavetas de Vestígio, cada uma com poderes e descrição próprios. */
            .vault-v19-abilities {border-radius:17px;overflow:hidden;}
            .vault-v19-current-stage {
                display:flex;align-items:center;justify-content:space-between;gap:15px;
                margin:14px 17px 15px;padding:12px 15px;border:1px solid #e2cfb3;
                border-radius:12px;background:linear-gradient(105deg,#f8f0e4,#fffaf3);
            }
            .vault-v19-current-stage strong {display:block;color:#683124;font:800 11px system-ui,sans-serif;}
            .vault-v19-current-stage small {display:block;margin-top:3px;color:#96775d;font:500 9px/1.4 system-ui,sans-serif;}
            .vault-v19-current-stage select {width:160px;flex-shrink:0;min-height:37px;padding:6px 10px;}
            .vault-v19-stage-groups {display:grid;gap:12px;padding:0 17px 18px;}
            .vault-v19-stage-group.is-vestige {
                overflow:hidden;border:1px solid #dfcdb6;border-radius:13px;
                background:#fffcf8;box-shadow:0 3px 10px rgba(72,47,24,.035);
            }
            .vault-v19-stage-group.is-vestige.is-open {border-color:#bd9a73;box-shadow:0 8px 19px rgba(80,49,24,.06);}
            .vault-v19-stage-group.is-vestige.is-current .vault-v19-stage-bar {
                background:linear-gradient(100deg,#f8eddd,#f9f4e9);
            }
            .vault-v19-stage-bar {
                display:flex;gap:9px;align-items:center;justify-content:space-between;
                min-height:75px;padding:8px 13px;border-bottom:1px solid transparent;
                background:linear-gradient(100deg,#f8f5f0,#fffcf7);
            }
            .vault-v19-stage-group.is-open .vault-v19-stage-bar {border-bottom-color:#ead9c6;}
            .vault-v19-stage-toggle {
                display:flex;min-width:0;flex:1;align-items:center;gap:11px;
                border:0;background:transparent;color:#64271e;text-align:left;
                cursor:pointer;padding:5px 3px;
            }
            .vault-v19-stage-symbol {
                flex:0 0 37px;display:grid;place-items:center;width:37px;height:37px;
                border:1px solid #d6b899;border-radius:10px;background:#f1e6d8;
                color:#845537;font:700 17px Georgia,serif;
            }
            .vault-v19-stage-group.is-current .vault-v19-stage-symbol {
                border-color:#a46c53;background:#eddbca;color:#6b271b;
            }
            .vault-v19-stage-text {display:flex;flex:0 1 auto;flex-direction:column;gap:4px;}
            .vault-v19-stage-text strong {font:700 17px/1.1 Georgia,serif;color:#66271c;}
            .vault-v19-stage-text small {font:600 9px/1.25 system-ui,sans-serif;color:#97755b;}
            .vault-v19-stage-count {
                margin-left:auto;display:inline-flex;gap:4px;align-items:center;
                color:#967455;font:700 10px system-ui,sans-serif;white-space:nowrap;
            }
            .vault-v19-stage-count span:first-child {
                display:grid;place-items:center;min-width:23px;height:23px;
                border:1px solid #e2cfb9;border-radius:7px;background:#fffaf4;color:#783526;
            }
            .vault-v19-current-tag {
                padding:4px 7px;border:1px solid #d2b896;border-radius:999px;
                background:#f6e8d4;color:#865326;font:800 8px system-ui,sans-serif;text-transform:uppercase;
            }
            .vault-v19-stage-chevron {flex:0 0 17px;width:17px;height:17px;color:#996e53;transition:transform .2s;}
            .vault-v19-stage-chevron.is-open {transform:rotate(180deg);}
            .vault-v19-stage-add {
                display:flex;align-items:center;gap:6px;flex:0 0 auto;
                min-height:35px;padding:0 11px;border:1px solid #b18168;
                border-radius:9px;background:#6b1d14;color:#fff9f0;
                font:800 9px system-ui,sans-serif;cursor:pointer;
            }
            .vault-v19-stage-add:hover {background:#822a1f;}
            .vault-v19-stage-add span {font-size:16px;line-height:1;}
            .vault-v19-stage-content {min-width:0;padding:14px;}
            .vault-v19-stage-lore {
                padding:0 0 14px;margin-bottom:12px;border-bottom:1px solid #e9daca;
            }
            .vault-v19-stage-lore label {display:block;}
            .vault-v19-stage-lore textarea {min-height:85px;resize:vertical;}
            .vault-v19-stage-content .master-vault-v5-abilities-body {padding:0;}
            .vault-v19-stage-group:not(.is-vestige) .vault-v19-stage-content {padding:0;}
            .vault-v19-stage-group:not(.is-vestige) .master-vault-v5-abilities-body {padding:13px 17px 18px;}
            @media(max-width:1140px){
                .vault-v18-workspace {grid-template-columns:170px minmax(0,1fr);gap:14px;}
                .vault-v19-options .vault-v19-option {padding-left:30px;}
                .vault-v19-options .vault-v19-option:before {left:10px;}
            }
            @media(max-width:860px){
                .vault-v18-workspace {grid-template-columns:minmax(0,1fr);}
                .vault-v18-sidebar {position:static;display:flex;justify-content:center;}
                .vault-v18-sidebar-card {width:min(175px,100%);display:block;}
                .vault-v18-photo {width:100%;flex:auto;}
            }
            @media(max-width:620px){
                .vault-v19-options {grid-template-columns:1fr;}
                .vault-v19-category-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
                .vault-v19-category-grid .master-vault-v5-category {min-height:110px;padding:9px;}
                .vault-v19-mechanics-heading {padding:14px;}
                .vault-v19-mechanics-heading h3 {font-size:18px;}
                .vault-v19-current-stage {align-items:stretch;flex-direction:column;}
                .vault-v19-current-stage select {width:100%;}
                .vault-v19-stage-bar {flex-wrap:wrap;padding:9px;gap:6px;}
                .vault-v19-stage-toggle {flex-basis:100%;}
                .vault-v19-stage-add {width:100%;justify-content:center;}
                .vault-v19-stage-count {font-size:9px;}
                .vault-v19-current-tag {display:none;}
            }
            @media(prefers-reduced-motion:reduce){
                .vault-v19-stage-chevron,
                .vault-v19-options .vault-v19-option {transition:none;}
            }


            /* =============================================================
               V66.1.23 — Visualização do Cofre: Vestígio dourado e 3 etapas
               Não depende do wrapper .master-vault-page: modal usa teleport.
               ============================================================= */
            .master-vault-v9-preview .vault-v23-inline-description {
                max-width: 850px;
                margin-top: 16px;
                border-top: 1px solid rgba(205,187,159,.49);
                padding-top: 12px;
            }
            .master-vault-v9-preview .vault-v23-inline-description-label {
                display:block;margin-bottom:6px;color:#9a704d;
                font:900 9px/1.3 system-ui,sans-serif;
                text-transform:uppercase;letter-spacing:.09em;
            }
            .master-vault-v9-preview .vault-v23-inline-description p {
                margin:0;color:#654e3d;font:400 12px/1.75 system-ui,sans-serif;
                white-space:pre-line;overflow-wrap:anywhere;
            }
            .master-vault-v9-preview .vault-v23-attunable {
                display:inline;margin-left:5px;font-style:italic;
                color:#98754a;font-size:11px;
            }
            .master-vault-v9-preview .vault-v23-vestige-section {margin-top:20px;}
            .master-vault-v9-preview .vault-v23-vestige-overview {
                display:flex;align-items:center;gap:14px;
                margin-bottom:12px;padding:15px 17px;
                border:1px solid #d8bb7e;border-radius:13px;
                background:linear-gradient(115deg,#f5e9cf,#fff8e9);
            }
            .master-vault-v9-preview .vault-v23-vestige-star {
                display:grid;place-items:center;flex:0 0 44px;width:44px;height:44px;
                border:1px solid #d2ad69;border-radius:12px;background:#fbf3df;
                color:#9a6b22;font-size:25px;
            }
            .master-vault-v9-preview .vault-v23-vestige-overline {
                color:#926c32;font:900 8px/1.4 system-ui,sans-serif;
                text-transform:uppercase;letter-spacing:.12em;
            }
            .master-vault-v9-preview .vault-v23-vestige-overview h3 {
                margin:3px 0 0;color:#70471f;font:700 19px/1.18 Georgia,serif;
            }
            .master-vault-v9-preview .vault-v23-vestige-overview p {
                margin:4px 0 0;color:#896a41;font:500 10px/1.5 system-ui,sans-serif;
            }
            .master-vault-v9-preview .vault-v23-vestige-stage {
                overflow:hidden;margin-top:11px;border:1px solid #d9c49b;
                border-radius:13px;background:#fffbf2;
                box-shadow:0 3px 12px rgba(100,69,24,.035);
            }
            .master-vault-v9-preview .vault-v23-stage-header {
                display:flex;align-items:center;gap:12px;padding:12px 15px;
                border-bottom:1px solid #e4cfaa;
                background:linear-gradient(105deg,#f2e3c3,#f9f0dc);
            }
            .master-vault-v9-preview .vault-v23-stage-order {
                display:grid;place-items:center;flex:0 0 36px;width:36px;height:36px;
                border:1px solid #ceb178;border-radius:9px;
                background:#fff7e4;color:#866025;font:800 14px Georgia,serif;
            }
            .master-vault-v9-preview .vault-v23-stage-identity {min-width:0;flex:1;}
            .master-vault-v9-preview .vault-v23-stage-identity h4 {
                margin:0;color:#68401b;font:700 17px/1.2 Georgia,serif;
            }
            .master-vault-v9-preview .vault-v23-stage-identity small {
                display:block;margin-top:3px;color:#8e6b35;font:700 9px/1.3 system-ui,sans-serif;
            }
            .master-vault-v9-preview .vault-v23-stage-count {
                flex:0 0 auto;border:1px solid #d4bb86;border-radius:999px;
                background:#fff8e7;padding:5px 9px;color:#80602e;
                font:800 9px/1.2 system-ui,sans-serif;
            }
            .master-vault-v9-preview .vault-v23-stage-lore {
                margin:0;border-bottom:1px solid #e9dcc2;padding:11px 15px;
                background:#fcf5e8;color:#73542e;
                font:400 11px/1.65 system-ui,sans-serif;
                white-space:pre-line;overflow-wrap:anywhere;
            }
            .master-vault-v9-preview .vault-v23-stage-features {
                display:grid;gap:8px;padding:11px 12px;
            }
            .master-vault-v9-preview .vault-v23-vestige-feature {
                margin:0!important;border-color:#e4d2ad!important;
                background:#fffdf8!important;
            }
            .master-vault-v9-preview .vault-v23-vestige-feature .master-vault-v9-preview-feature-head {
                border-bottom-color:#e9d8b9;
            }
            .master-vault-v9-preview .vault-v23-vestige-feature h4 {color:#69411f;}
            .master-vault-v9-preview .vault-v23-stage-empty {
                margin:0;padding:10px 5px;color:#9c8261;
                font:500 10px/1.5 system-ui,sans-serif;
            }
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) {
                border:1px solid #bd9750;
                background:linear-gradient(155deg,#fffaf0 0%,#faf2de 100%);
                box-shadow:0 30px 85px rgba(31,21,8,.38),0 0 0 1px rgba(231,197,120,.28);
            }
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v9-preview-head,
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v9-preview-foot {
                border-color:#ddc391;
                background:linear-gradient(100deg,#f1dfb9,#f8edd6);
            }
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v9-preview-kicker,
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v16-eyebrow {color:#9b712c;}
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v16-preview-image {
                border:1px solid #c8a365;
                background:radial-gradient(circle at 50% 35%,#fff9e9,#f0e1c3)!important;
                box-shadow:0 0 0 4px rgba(217,180,106,.13);
            }
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v16-preview-name {color:#6c461e;}
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v16-nature,
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v16-category {color:#93672c;}
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .vault-v23-attunable {color:#a07736;}
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v9-preview-edit {
                background:#8a602b;color:#fffaf0;
            }
            .master-vault-v9-preview:is(.vault-v23-preview-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .master-vault-v9-preview-edit:hover {background:#70491e;}
            @media(max-width:720px){
                .master-vault-v9-preview .vault-v23-vestige-overview {padding:12px;}
                .master-vault-v9-preview .vault-v23-stage-header {flex-wrap:wrap;padding:10px;}
                .master-vault-v9-preview .vault-v23-stage-count {margin-left:48px;}
                .master-vault-v9-preview .vault-v23-inline-description {margin-top:12px;}
            }


            /* ===============================================================
               SPELLBOUND V66.1.25 — PASTAS / CARDS DO COFRE
               Apenas apresentação; preserva menus, filtros, drag-and-drop,
               miniaturas, favorito, descrição e habilidades.
               =============================================================== */
            .master-vault-page .master-vault-v11-folder-card.vault-v25-folder {
                min-height: 185px !important;
                padding: 16px 15px 13px !important;
                border: 1px solid color-mix(in srgb, var(--folder-accent) 20%, #ddcbb5) !important;
                border-radius: 18px !important;
                background: linear-gradient(140deg, #fffcf7 4%, #fbf5eb 72%,
                    color-mix(in srgb, var(--folder-accent) 5%, #f8f0e5) 100%) !important;
                box-shadow: 0 5px 17px rgba(75, 43, 25, .055),
                    inset 0 1px 0 rgba(255,255,255,.8) !important;
                isolation: isolate;
            }
            .master-vault-page .vault-v25-folder::before {
                content: '';
                position: absolute;
                top: 0;
                left: 17px;
                width: 84px;
                height: 4px;
                border-radius: 0 0 9px 9px;
                background: var(--folder-accent);
                opacity: .87;
                pointer-events: none;
            }
            .master-vault-page .vault-v25-folder:hover,
            .master-vault-page .vault-v25-folder:focus-visible,
            .master-vault-page .vault-v25-folder.is-drag-target {
                transform: translateY(-3px) !important;
                border-color: color-mix(in srgb, var(--folder-accent) 46%, #d6c2a7) !important;
                box-shadow: 0 14px 30px rgba(75, 43, 25, .11),
                    inset 0 1px 0 rgba(255,255,255,.8) !important;
                outline: none;
            }
            .master-vault-page .vault-v25-folder:focus-visible {
                outline: 2px solid var(--folder-accent);
                outline-offset: 3px;
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-title {
                gap: 11px !important;
                align-items: flex-start;
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-icon {
                width: 41px !important;
                height: 41px !important;
                flex: 0 0 41px !important;
                border: 1px solid color-mix(in srgb, var(--folder-accent) 22%, #e4d5c2);
                border-radius: 12px !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.72);
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-icon svg {
                width: 20px !important;
                height: 20px !important;
                min-width: 20px !important;
                min-height: 20px !important;
                max-width: 20px !important;
                max-height: 20px !important;
            }
            .master-vault-page .vault-v25-folder-eyebrow {
                display: block;
                margin: 1px 0 5px;
                color: #a08367;
                font: 800 8px/1.1 system-ui, sans-serif;
                text-transform: uppercase;
                letter-spacing: .11em;
            }
            .master-vault-page .vault-v25-folder h3 {
                margin: 0 !important;
                font-size: clamp(16px, 1.1vw, 19px) !important;
                line-height: 1.17 !important;
                color: #593328;
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-description {
                margin: 6px 0 0 !important;
                min-height: 0 !important;
                max-height: 31px;
                font-size: 10.4px !important;
                line-height: 1.42 !important;
                color: #8e6d54;
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-menu-button {
                width: 28px;
                height: 28px;
                border-radius: 9px;
            }
            .master-vault-page .vault-v25-folder .vault-v25-folder-shelf {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                min-height: 64px;
                margin-top: 15px !important;
                padding: 9px 10px !important;
                border: 1px solid rgba(181, 148, 108, .23) !important;
                border-radius: 11px;
                background: color-mix(in srgb, var(--folder-accent) 5%, #f7f0e5);
            }
            .master-vault-page .vault-v25-folder-shelf-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-width: 0;
                gap: 5px;
            }
            .master-vault-page .vault-v25-folder-shelf-label {
                color: #93765b;
                font: 800 8px/1 system-ui, sans-serif;
                letter-spacing: .065em;
                text-transform: uppercase;
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-preview {
                min-height: 30px;
                align-items: center;
                gap: 2px !important;
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-avatar {
                width: 32px !important;
                height: 32px !important;
                flex: 0 0 32px !important;
                margin-left: 0 !important;
                border-radius: 6px !important;
            }
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-avatar.is-empty {
                height: auto !important;
                width: auto !important;
            }
            .master-vault-page .vault-v25-folder-open {
                display: inline-flex;
                align-items: center;
                justify-content: flex-end;
                gap: 5px;
                flex: 0 0 auto;
                min-width: 0;
            }
            .master-vault-page .vault-v25-folder-open .master-vault-v11-folder-count {
                flex: 0 0 auto;
                border-color: rgba(180, 147, 106, .4);
                background: rgba(255,253,247,.78);
                color: #80583f;
            }
            .master-vault-page .vault-v25-folder-open svg {
                width: 15px;
                height: 15px;
                flex: 0 0 15px;
                color: var(--folder-accent);
                transition: transform .2s ease;
            }
            .master-vault-page .vault-v25-folder:hover .vault-v25-folder-open svg {
                transform: translateX(3px);
            }

            /* Um único corpo visual: não há mais duas linhas separando
               descrição e habilidades nos cards de itens. */
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section,
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section:last-of-type {
                border-top: none !important;
            }
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section:not(:has(.master-vault-v10-abilities)) {
                padding-top: 2px !important;
                padding-bottom: 7px !important;
            }
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section:has(.master-vault-v10-abilities) {
                margin: 0 13px 11px 16px !important;
                padding: 6px 8px !important;
                border-radius: 9px !important;
                background: #f4ede3 !important;
                align-self: end;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-abilities {
                gap: 5px !important;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-ability {
                min-height: 23px !important;
                padding: 3px 8px !important;
                border: 1px solid rgba(168,132,93,.28) !important;
                border-radius: 7px !important;
                background: #fffbf5;
                color: #795438;
                font-size: 9px !important;
                line-height: 1.3;
                font-weight: 700;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-ability-more {
                color: #6b1d14;
                background: #f1e1d5;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-identity {
                padding-bottom: 4px;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-description {
                color: #755c49;
                line-height: 1.53;
            }
            @media (max-width: 720px) {
                .master-vault-page .vault-v25-folder .vault-v25-folder-shelf {
                    margin-top: 12px !important;
                }
                .master-vault-page .master-vault-v10-card > .master-vault-v10-section:has(.master-vault-v10-abilities) {
                    margin-right: 9px !important;
                    margin-left: 10px !important;
                }
            }
            @media (prefers-reduced-motion: reduce) {
                .master-vault-page .vault-v25-folder,
                .master-vault-page .vault-v25-folder-open svg {
                    transition: none !important;
                }
            }


            /* V66.1.30 — Formatação de texto sem editor pesado. */
            .vault-v30-rich-field .vault-v30-rich-toolbar {
                display:flex;align-items:center;justify-content:space-between;
                flex-wrap:wrap;gap:8px;margin-bottom:7px;
            }
            .vault-v30-rich-field .vault-v30-rich-toolbar > span {
                color:#53150f;font-size:11px;font-weight:900;
            }
            .vault-v30-bold-button {
                display:inline-flex;align-items:center;justify-content:center;gap:6px;
                min-height:30px;padding:4px 10px;border:1px solid rgba(181,145,99,.5);
                border-radius:8px;background:#fff9ef;color:#79512d;
                font-size:10px;font-weight:800;cursor:pointer;
                transition:background .15s,border-color .15s;
            }
            .vault-v30-bold-button:hover,
            .vault-v30-bold-button:focus-visible {background:#f4e7d0;border-color:#aa8150;}
            .vault-v30-bold-button strong {font-family:Georgia,serif;font-size:14px;font-weight:900;}
            .vault-v30-rich-hint {display:block;margin-top:6px;color:#9b7d60;font-size:10px;}
            .master-vault-v9-preview .vault-v30-rich-text strong {font-weight:800;color:inherit;}
            .master-vault-v9-preview .vault-v23-inline-description {margin-top:14px;}

            /* V66.1.31 — barra Tiptap compacta, leitura de pergaminho preservada. */
            .vault-v31-rich-field { min-width:0; }
            .vault-v31-rich-toolbar { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:8px 10px;border:1px solid rgba(193,164,125,.65);border-bottom:0;border-radius:12px 12px 0 0;background:#f1e9db; }
            .vault-v31-rich-label { color:#65311f;font-size:11px;font-weight:900; }
            .vault-v31-rich-actions { display:flex;align-items:center;flex-wrap:wrap;gap:3px; }
            .vault-v31-rich-actions button { min-width:27px;height:27px;padding:0 6px;border:1px solid transparent;border-radius:6px;background:transparent;color:#76563f;font-size:11px;font-weight:850;cursor:pointer;transition:background .12s,color .12s; }
            .vault-v31-rich-actions button:hover,.vault-v31-rich-actions button:focus-visible { background:#fff9ed;border-color:rgba(174,137,89,.36); }
            .vault-v31-rich-actions button.is-active { color:#662016;background:#e3d1b7;border-color:rgba(139,88,42,.28); }
            .vault-v31-rich-separator { width:1px;height:17px;background:#cfbca2;margin:0 4px; }
            .vault-v31-rich-canvas { padding:2px;min-height:120px;border:1px solid rgba(193,164,125,.65);border-radius:0 0 10px 10px;background:#fffdfa;transition:border-color .14s; }
            /* DOM do Tiptap isolado do Alpine; alternativa editável durante o carregamento. */
            .vault-v31-rich-surface { min-height:114px; cursor:text; }
            .vault-v31-fallback-input {
                display:block; width:100%; min-height:114px; max-height:390px;
                padding:12px 14px; border:0; outline:none; resize:vertical;
                background:transparent; color:#563926; font-size:12px; line-height:1.65;
            }
            .vault-v31-prosemirror { cursor:text; }
            /* Barra em linha única; não empurra nem desorganiza os blocos do formulário. */
            .vault-v31-rich-toolbar { flex-wrap:nowrap; min-width:0; }
            .vault-v31-rich-label { flex:0 0 auto; }
            .vault-v31-rich-actions { flex:1 1 auto; min-width:0; flex-wrap:nowrap; overflow-x:auto; justify-content:flex-end; scrollbar-width:thin; }
            .vault-v31-rich-actions button, .vault-v31-rich-separator { flex-shrink:0; }
            @media (max-width:680px) {
                .vault-v31-rich-toolbar { flex-wrap:wrap; }
                .vault-v31-rich-actions { flex:1 0 100%; justify-content:flex-start; }
            }

            .vault-v31-rich-canvas:focus-within { border-color:#a87a48;box-shadow:0 0 0 3px rgba(166,112,61,.08); }
            .vault-v31-prosemirror { min-height:114px;max-height:390px;overflow:auto;padding:12px 14px;outline:none;color:#563926;font-size:12px;line-height:1.65;white-space:normal;overflow-wrap:anywhere; }
            .vault-v31-prosemirror p { margin:0; }
            .vault-v31-prosemirror p + p { margin-top:.7em; }
            .vault-v31-prosemirror h3 { font-family:Georgia,serif;font-size:17px;font-weight:900;color:#622018;margin:.7em 0 .25em; }
            .vault-v31-prosemirror :is(strong,b) { font-weight:900;color:#4f231a; }
            .vault-v31-prosemirror ul { list-style:disc;padding-left:1.4em;margin:.55em 0; }
            .vault-v31-prosemirror ol { list-style:decimal;padding-left:1.4em;margin:.55em 0; }
            .vault-v31-prosemirror li { margin:.15em 0; }
            .vault-v31-prosemirror blockquote { border-left:3px solid #bf9961;padding-left:11px;margin:.7em 0;color:#76563f;font-style:italic; }
            .vault-v31-prosemirror .ProseMirror-selectednode {outline:2px solid #be9d69;}
            .vault-v31-rich-foot { display:flex;justify-content:space-between;gap:8px;margin-top:5px;color:#977456;font-size:9px;line-height:1.3; }
            .vault-v31-rich-foot .is-over { color:#af2727;font-weight:900; }
            .master-vault-v9-preview .vault-v30-rich-text p + p { margin-top:.65em; }
            .master-vault-v9-preview .vault-v30-rich-text :is(h3,h4) { font-family:Georgia,serif;font-weight:900;font-size:1.12em;margin:.75em 0 .35em; }
            .master-vault-v9-preview .vault-v30-rich-text ul { list-style:disc;padding-left:1.35em;margin:.5em 0; }
            .master-vault-v9-preview .vault-v30-rich-text ol { list-style:decimal;padding-left:1.35em;margin:.5em 0; }
            .master-vault-v9-preview .vault-v30-rich-text li { margin:.16em 0; }
            .master-vault-v9-preview .vault-v30-rich-text blockquote { border-left:3px solid #bd9860;padding-left:1em;margin:.6em 0; }
            .master-vault-v9-preview .vault-v30-rich-text s { text-decoration:line-through; }
            @media (max-width:560px) { .vault-v31-rich-toolbar { align-items:flex-start; } .vault-v31-rich-actions { width:100%; } .vault-v31-rich-prosemirror { min-height:130px; } }


            /* V66.1.33 — digitação e leitura em escala consistente com o Cofre. */
            .vault-v31-rich-field .vault-v31-rich-toolbar { padding:6px 9px;min-height:38px;gap:7px;background:#f3ecdf; }
            .vault-v31-rich-field .vault-v31-rich-actions button { min-width:29px;height:27px;font-size:11px; }
            .vault-v31-rich-field .vault-v31-rich-canvas { min-height:98px; }
            .vault-v31-rich-field .vault-v31-fallback-input,
            .vault-v31-rich-field .vault-v31-prosemirror {
                display:block;width:100%;min-height:96px;max-height:370px;overflow-y:auto;
                padding:11px 13px;background:transparent;outline:none;
                font-family:inherit;font-size:12px;line-height:1.65;font-weight:500;
                color:#614936;text-align:left;white-space:normal;overflow-wrap:anywhere;
            }
            .vault-v31-rich-field .vault-v31-fallback-input:empty::before {
                content:attr(data-placeholder);color:#a89076;pointer-events:none;
            }
            .vault-v31-rich-field .vault-v31-fallback-input:focus,
            .vault-v31-rich-field .vault-v31-prosemirror:focus {outline:none;}
            .vault-v31-rich-field .vault-v31-fallback-input :is(p,div),
            .vault-v31-rich-field .vault-v31-prosemirror p {margin:0;}
            .vault-v31-rich-field .vault-v31-fallback-input :is(p,div) + :is(p,div),
            .vault-v31-rich-field .vault-v31-prosemirror p + p {margin-top:.46em;}
            .vault-v31-rich-field :is(.vault-v31-fallback-input,.vault-v31-prosemirror) h3 {
                font-family:Georgia,serif;font-size:15px;line-height:1.3;
                font-weight:800;color:#683f25;margin:.65em 0 .25em;
            }
            .vault-v31-rich-field :is(.vault-v31-fallback-input,.vault-v31-prosemirror) :is(strong,b) {
                font-weight:800;color:#57311f;
            }
            .vault-v31-rich-field :is(.vault-v31-fallback-input,.vault-v31-prosemirror) :is(ul,ol) {
                margin:.45em 0;padding-left:1.4em;
            }
            .vault-v31-rich-field :is(.vault-v31-fallback-input,.vault-v31-prosemirror) ul {list-style:disc;}
            .vault-v31-rich-field :is(.vault-v31-fallback-input,.vault-v31-prosemirror) ol {list-style:decimal;}
            .vault-v31-rich-field :is(.vault-v31-fallback-input,.vault-v31-prosemirror) blockquote {
                margin:.5em 0;padding-left:.8em;border-left:2px solid #c9a579;
                color:#82654a;font-style:italic;
            }
            .master-vault-v9-preview .vault-v30-rich-text {
                font-family:inherit;font-size:11.5px;line-height:1.67;font-weight:500;
                color:inherit;letter-spacing:normal;white-space:normal;overflow-wrap:anywhere;
            }
            .master-vault-v9-preview .vault-v30-rich-text :is(p,ul,ol,blockquote) {margin:0;}
            .master-vault-v9-preview .vault-v30-rich-text p + p {margin-top:.48em;}
            .master-vault-v9-preview .vault-v30-rich-text :is(strong,b) {font-weight:800;color:inherit;}
            .master-vault-v9-preview .vault-v30-rich-text :is(em,i) {font-style:italic;}
            .master-vault-v9-preview .vault-v30-rich-text :is(h3,h4) {
                font-family:Georgia,serif;font-size:13px;line-height:1.35;font-weight:800;
                margin:.7em 0 .22em;color:inherit;
            }
            .master-vault-v9-preview .vault-v30-rich-text ul {list-style:disc;padding-left:1.4em;}
            .master-vault-v9-preview .vault-v30-rich-text ol {list-style:decimal;padding-left:1.4em;}
            .master-vault-v9-preview .vault-v30-rich-text li {margin:.14em 0;}
            .master-vault-v9-preview .vault-v30-rich-text blockquote {
                border-left:2px solid #c6a16b;padding-left:.85em;margin:.5em 0;font-style:italic;
            }
            .master-vault-v9-preview .master-vault-v9-preview-feature > .vault-v30-rich-text,
            .master-vault-v9-preview .vault-v23-vestige-feature > .vault-v30-rich-text {padding:10px 13px 12px;}
            .master-vault-v9-preview .vault-v23-stage-lore > .vault-v30-rich-text {font-size:11px;}
            .master-vault-v9-preview .vault-v23-inline-description > .vault-v30-rich-text {font-size:11.5px;}


            /* ===========================================================
               V66.1.34 — Separadores Tiptap e leitura rica nos cards
               =========================================================== */
            .vault-v31-rich-field :is(.vault-v31-fallback-input, .vault-v31-prosemirror) hr,
            .master-vault-v9-preview .vault-v30-rich-text hr {
                display:block !important;
                width:100%;
                height:0;
                border:0;
                border-top:1px solid rgba(158,115,59,.48);
                margin:13px 0;
                opacity:1;
            }
            .vault-v31-rich-field :is(.vault-v31-fallback-input, .vault-v31-prosemirror) hr {
                border-top-color:rgba(129,86,49,.54);
            }
            .vault-v34-card-rich-excerpt.master-vault-v10-description {
                display:block !important;
                max-height:calc(1.5em * 3) !important;
                overflow:hidden;
                -webkit-line-clamp:unset !important;
                white-space:normal;
                overflow-wrap:anywhere;
                line-height:1.5 !important;
            }
            .vault-v34-card-rich-excerpt :is(p,ul,ol,blockquote,h3,h4) {margin:0; padding:0;}
            .vault-v34-card-rich-excerpt p + p {margin-top:5px;}
            .vault-v34-card-rich-excerpt :is(h3,h4) {font-family:Georgia,serif;font-size:1.05em;font-weight:800;line-height:1.35;}
            .vault-v34-card-rich-excerpt :is(strong,b) {font-weight:850;color:inherit;}
            .vault-v34-card-rich-excerpt :is(em,i) {font-style:italic;}
            .vault-v34-card-rich-excerpt :is(ul,ol) {padding-left:16px;}
            .vault-v34-card-rich-excerpt ul {list-style:disc;}
            .vault-v34-card-rich-excerpt ol {list-style:decimal;}
            .vault-v34-card-rich-excerpt li {margin:0;}
            .vault-v34-card-rich-excerpt hr {
                display:block;height:0;margin:5px 0;
                border:0;border-top:1px solid rgba(158,115,59,.34);
            }

            /* ===============================================================
               SPELLBOUND V66.1.35 — COFRE NO PADRÃO DE CARDS DE NPCS
               Identidade da miniatura, ritmo de leitura e menus consistentes.
               =============================================================== */
            .master-vault-page .vault-unassigned-grid { align-items: stretch; }
            .master-vault-page .master-vault-v10-card {
                grid-template-rows: auto minmax(0,1fr) auto;
                overflow: visible !important;
                border-radius: 18px !important;
                background: #fffcf7 !important;
                transition: transform .22s cubic-bezier(.22,1,.36,1), border-color .22s ease,
                    box-shadow .22s ease, opacity .2s ease !important;
            }
            .master-vault-page .master-vault-v10-card.is-menu-open {
                z-index: 65 !important;
            }
            .master-vault-page .master-vault-v10-card:hover { border-color:#c9a986 !important; }
            .master-vault-page .master-vault-v10-card.is-moving {
                opacity: .52; transform: translateY(-9px) scale(.975) !important;
                pointer-events: none;
            }
            .master-vault-page .master-vault-v10-card.is-arriving {
                animation: vault-v35-item-arrive .45s cubic-bezier(.18,.8,.24,1) both;
            }
            .master-vault-page .master-vault-v10-thumb.vault-v35-portrait {
                position: relative; z-index: 1; isolation: isolate;
                padding: 12px !important;
                background: linear-gradient(155deg,#efdfcb 0%,#e3ccb3 65%,#d8bda1 100%) !important;
                box-shadow: inset -6px 0 12px rgba(93,59,36,.035), inset 0 0 0 6px rgba(255,255,255,.09);
            }
            .master-vault-page .vault-v35-portrait::before {
                content:''; position:absolute; z-index:-1; inset:10px;
                border:1px solid rgba(255,252,245,.60);
                border-radius:12px; pointer-events:none;
            }
            .master-vault-page .vault-v35-portrait::after {
                content:''; position:absolute; z-index:-1; inset:13px;
                border-radius:10px; pointer-events:none;
                background:radial-gradient(circle at 50% 44%,rgba(255,255,255,.36),transparent 68%);
            }
            .master-vault-page .vault-v35-portrait img {
                position:relative; z-index:1;
                object-fit:contain !important;
                filter:drop-shadow(0 7px 7px rgba(60,40,24,.17));
            }
            .master-vault-page .vault-v35-portrait-seal {
                position:absolute; z-index:4; left:50%; bottom:12px;
                transform:translateX(-50%);
                display:inline-flex; align-items:center; justify-content:center;
                padding:5px 9px; border:1px solid rgba(191,147,61,.62);
                border-radius:999px; background:rgba(255,246,218,.95);
                color:#815820; font:800 8px/1 Georgia,serif;
                white-space:nowrap; letter-spacing:.075em;
                box-shadow:0 2px 8px rgba(89,63,30,.13);
                pointer-events:none;
            }
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .vault-v35-portrait {
                background:linear-gradient(150deg,#f4e7cf,#e6cf9e 65%,#cfae76) !important;
                border-right-color:#d3b57d !important;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-identity {
                padding:14px 49px 7px 17px !important;
                min-height:0 !important;
            }
            .master-vault-page .vault-v35-item-mark {
                display:block; height:12px; margin-bottom:5px;
                font:700 16px/1 Georgia,serif; color:#ad8257;
            }
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .vault-v35-item-mark {color:#be9142;}
            .master-vault-page .master-vault-v10-card .master-vault-v10-name {
                margin:0; font-family:Georgia,serif;
                font-size:18px !important; line-height:1.16 !important;
                font-weight:800;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-nature {
                margin-top:8px !important; color:#86674e; font-size:9px !important;
                font-weight:650 !important; line-height:1.38;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-category {
                margin-top:5px !important; font-size:9px !important; font-weight:680 !important;
                color:#8a6449;
            }
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section:not(.vault-v35-features-section) {
                grid-column:2; grid-row:2;
                align-self: start;
                margin:0 15px 10px 17px !important;
                padding:10px 0 0 !important;
                border-top:1px solid rgba(189,160,122,.26) !important;
            }
            .master-vault-page .master-vault-v10-card .vault-v34-card-rich-excerpt.master-vault-v10-description {
                font-size:11px !important; line-height:1.5 !important;
                max-height:4.5em !important; overflow:hidden;
                margin:0 !important; color:#795e4a;
                font-weight:500;
                display:-webkit-box !important; -webkit-line-clamp:3 !important;
                -webkit-box-orient:vertical !important;
            }
            .master-vault-page .master-vault-v10-card .vault-v34-card-rich-excerpt p+p {margin-top:4px;}
            .master-vault-page .master-vault-v10-card > .vault-v35-features-section {
                grid-column:2; grid-row:3; align-self:end;
                margin:0 13px 13px 16px !important; padding:0 !important;
                border:0 !important; background:transparent !important;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-abilities {
                display:flex; flex-wrap:wrap; align-items:center; gap:5px;
                padding:7px 8px; border:1px solid rgba(178,148,114,.14);
                border-radius:10px; background:#f3ece2;
            }
            .master-vault-page .vault-v35-vestige-summary {
                display:flex; align-items:center; gap:9px;
                width:100%; min-height:48px; padding:8px 10px;
                border:1px solid rgba(188,149,75,.39);
                border-radius:11px;
                background:linear-gradient(105deg,#f9ebcc,#f7f0df);
                color:#765427;
            }
            .master-vault-page .vault-v35-vestige-summary-icon {
                display:grid;place-items:center;flex:0 0 30px;width:30px;height:30px;
                border:1px solid rgba(185,144,77,.33);border-radius:9px;
                background:#fff7e4;font-size:18px;
            }
            .master-vault-page .vault-v35-vestige-summary > span:nth-child(2) {display:grid;gap:3px;min-width:0;flex:1;}
            .master-vault-page .vault-v35-vestige-summary strong {font:800 11px/1.15 Georgia,serif;}
            .master-vault-page .vault-v35-vestige-summary small {font:650 9px/1.25 system-ui,sans-serif;color:#927346;}
            .master-vault-page .vault-v35-vestige-summary-arrow {font:500 20px/1 Georgia,serif;color:#9e7a3a;}
            .master-vault-page .master-vault-v10-top-actions {right:10px !important; top:10px !important;}
            .master-vault-page .vault-v35-menu-trigger {
                display:grid;place-items:center;width:30px;height:30px;
                border:1px solid rgba(187,159,126,.18);border-radius:50%;
                background:rgba(255,252,245,.92);color:#6b1d14;
                box-shadow:0 1px 4px rgba(91,55,33,.05);
            }
            .master-vault-page .vault-v35-menu-trigger:hover,
            .master-vault-page .vault-v35-menu-trigger[aria-expanded="true"] {
                border-color:rgba(157,114,79,.36);background:#f4e9dc;
            }
            .master-vault-page .vault-v35-menu-trigger svg {width:16px;height:16px;}
            .master-vault-page .master-vault-v10-menu.vault-v35-action-menu {
                width:224px;top:calc(100% + 7px);right:0;z-index:200;
                padding:5px; overflow:visible !important;
                border:1px solid #dfcbb7;border-radius:12px;
                box-shadow:0 16px 34px rgba(53,31,22,.20);
                background:#fffcf7;
            }
            .master-vault-page .vault-v35-action-menu > button {
                display:flex;align-items:center;gap:11px;
                width:100%;min-height:38px;padding:0 11px;
                border:0;border-radius:7px;
                color:#5b3028;font-size:11.5px;font-weight:700;
                text-transform:none;
            }
            .master-vault-page .vault-v35-action-menu > button svg {width:16px;height:16px;stroke:currentColor;}
            .master-vault-page .vault-v35-action-menu > button:nth-last-child(2) {color:#31465e;}
            .master-vault-page .vault-v35-action-menu > button.is-danger {color:#bc3227;}
            .master-vault-page .vault-v35-action-menu > button+button,
            .master-vault-page .vault-v35-action-menu > .vault-v35-move-list+button {border-top:1px solid rgba(216,196,174,.43);}
            .master-vault-page .vault-v35-menu-chevron {margin-left:auto;color:#997b61;font-size:15px;}
            .master-vault-page .vault-v35-move-list {
                display:grid;gap:2px;max-height:175px;overflow-y:auto;
                margin:3px 2px 6px;padding:5px;border:1px solid #e4d2bc;
                border-radius:8px;background:#f8f0e5;
                scrollbar-width:thin;
            }
            .master-vault-page .vault-v35-move-list button {
                display:flex;gap:8px;align-items:center;width:100%;min-height:29px;
                padding:5px 8px;text-align:left;border:0;border-radius:6px;
                font-size:10px;font-weight:600;color:#6f4b32;background:transparent;
            }
            .master-vault-page .vault-v35-move-list button:not(:disabled):hover {background:#fffaf2;color:#5b3028;}
            .master-vault-page .vault-v35-move-list button:disabled {opacity:.5;cursor:default;}
            .master-vault-page .vault-v35-move-dot {flex:0 0 8px;width:8px;height:8px;border-radius:50%;}
            .vault-v35-menu-enter {transition:opacity .16s ease,transform .16s cubic-bezier(.2,.8,.2,1);transform-origin:top right;}
            .vault-v35-menu-from {opacity:0;transform:translateY(-6px) scale(.975);}
            .vault-v35-menu-to {opacity:1;transform:translateY(0) scale(1);}
            .master-vault-page .vault-v35-view-enter .master-vault-v10-card,
            .master-vault-page .vault-v35-view-enter .master-vault-v11-folder-card {
                animation: vault-v35-panel-enter .34s cubic-bezier(.21,.8,.28,1) both;
            }
            .master-vault-page .vault-v35-view-enter .master-vault-v10-card:nth-child(3n + 2),
            .master-vault-page .vault-v35-view-enter .master-vault-v11-folder-card:nth-child(3n + 2) {animation-delay:35ms;}
            .master-vault-page .vault-v35-view-enter .master-vault-v10-card:nth-child(3n),
            .master-vault-page .vault-v35-view-enter .master-vault-v11-folder-card:nth-child(3n) {animation-delay:65ms;}
            .master-vault-page .vault-v35-folder-received {
                animation:vault-v35-folder-receive .6s cubic-bezier(.18,.78,.25,1) both;
            }
            .master-vault-page .vault-v35-folder-received .master-vault-v11-folder-avatar {
                animation:vault-v35-mini-arrive .42s cubic-bezier(.18,.8,.27,1) both;
            }
            @keyframes vault-v35-item-arrive {from{opacity:.45;transform:translateY(12px) scale(.975)}to{opacity:1;transform:translateY(0) scale(1)}}
            @keyframes vault-v35-panel-enter {from{opacity:.35;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
            @keyframes vault-v35-folder-receive {0%{transform:scale(.985);filter:brightness(1)}45%{transform:scale(1.015);filter:brightness(1.06)}100%{transform:scale(1);filter:brightness(1)}}
            @keyframes vault-v35-mini-arrive {from{opacity:.4;transform:scale(.82) translateY(4px)}to{opacity:1;transform:scale(1) translateY(0)}}
            @media(max-width:700px) {
                .master-vault-page .master-vault-v10-card .master-vault-v10-identity {padding:12px 41px 7px 12px !important;}
                .master-vault-page .master-vault-v10-card > .master-vault-v10-section:not(.vault-v35-features-section) {margin-left:12px !important;margin-right:10px !important;}
                .master-vault-page .master-vault-v10-card > .vault-v35-features-section {margin-left:10px !important;margin-right:9px !important;}
                .master-vault-page .master-vault-v10-card .master-vault-v10-name {font-size:15px !important;}
                .master-vault-page .vault-v35-portrait-seal {font-size:7px;padding:4px 6px;}
                .master-vault-page .master-vault-v10-menu.vault-v35-action-menu {width:210px;}
            }
            @media(prefers-reduced-motion:reduce) {
                .master-vault-page :is(.vault-v35-view-enter .master-vault-v10-card,.vault-v35-view-enter .master-vault-v11-folder-card,.vault-v35-folder-received,.vault-v35-folder-received .master-vault-v11-folder-avatar,.master-vault-v10-card.is-arriving) {animation:none!important;}
                .vault-v35-menu-enter {transition:none!important;}
            }

            /* V66.1.35 — Inventário do editor sem coluna vazia após remover Favorito. */
            .vault-v18-inventory-fields {grid-template-columns:repeat(2,minmax(0,1fr));}
            @media(max-width:760px){.vault-v18-inventory-fields{grid-template-columns:1fr;}}
            /* Menu de pasta com a mesma hierarquia visual do menu de NPCs. */
            .master-vault-page .vault-v25-folder .master-vault-v11-folder-menu-button {
                border:1px solid rgba(189,157,123,.22);background:#fffaf3;
                display:grid;place-items:center;border-radius:50%;color:#765445;
                font-size:18px;line-height:1;
            }
            .master-vault-page .master-vault-v11-folder-menu-panel {
                min-width:186px;padding:5px;
                border:1px solid #decab5;border-radius:12px;
                background:#fffcf7;box-shadow:0 13px 30px rgba(53,31,22,.17);
            }
            .master-vault-page .master-vault-v11-folder-menu-panel button {
                display:flex;align-items:center;gap:8px;width:100%;
                min-height:37px;padding:0 9px;border-radius:7px;
                color:#5b3028;font-size:11px;font-weight:700;
            }
            .master-vault-page .master-vault-v11-folder-menu-panel button+button {
                border-top:1px solid rgba(216,196,174,.43);
            }
            .master-vault-page .master-vault-v11-folder-menu-panel button svg {
                width:15px;height:15px;flex:0 0 15px;
            }
            .master-vault-page .master-vault-v11-folder-menu-panel button.is-danger {color:#bd3329;}

            /* ===============================================================
               SPELLBOUND V66.1.36 — IDENTIDADE VISUAL POR NATUREZA DO ITEM
               Magia = estrela já aprovada; tecnologia = símbolo fornecido;
               mundano = bolsa de aventureiro. Ícones sem função de favorito.
               =============================================================== */
            .master-vault-page .vault-v35-item-mark.vault-v36-nature-mark {
                display:flex;
                align-items:center;
                justify-content:flex-start;
                width:22px;
                height:21px;
                margin:0 0 5px;
                line-height:1;
            }
            .master-vault-page .vault-v36-nature-icon {
                display:block;
                flex:0 0 auto;
                width:18px;
                height:18px;
            }
            .master-vault-page .vault-v36-nature-icon--magical {
                width:20px;
                height:20px;
                font:700 20px/20px Georgia,serif;
                text-align:center;
                color:#b68a4c;
                text-shadow:0 1px 0 rgba(255,250,233,.75);
            }
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .vault-v36-nature-icon--magical {
                color:#c39437;
            }
            .master-vault-page .vault-v36-nature-icon--technology,
            .master-vault-page .vault-v36-nature-icon--mundane {
                width:18px;
                height:18px;
                background-color:#557286;
                -webkit-mask-repeat:no-repeat;
                mask-repeat:no-repeat;
                -webkit-mask-size:contain;
                mask-size:contain;
                -webkit-mask-position:center;
                mask-position:center;
            }
            .master-vault-page .vault-v36-nature-icon--technology {
                -webkit-mask-image:url('{{ asset('images/vault/icons/technological.png') }}');
                mask-image:url('{{ asset('images/vault/icons/technological.png') }}');
            }
            .master-vault-page .vault-v36-nature-icon--mundane {
                background-color:#9b8068;
                -webkit-mask-image:url('{{ asset('images/vault/icons/mundane.png') }}');
                mask-image:url('{{ asset('images/vault/icons/mundane.png') }}');
            }

            /* ===========================================================
               SPELLBOUND V66.1.38 — IMAGEM, CLASSIFICAÇÃO E RARIDADE
               Mágicos: ouro | Tecnológicos: azul elétrico
               Mundanos: aço ou obsidiana (nunca ouro)
               =========================================================== */

            /* O comando de trocar imagem continua usando o mesmo input/file
               e o mesmo handleImage existente: não altera armazenamento. */
            .vault-v18-photo { position: relative; }
            .vault-v18-photo .vault-v38-photo-edit {
                position:absolute; left:50%; bottom:39px; z-index:5;
                transform:translateX(-50%);
                display:inline-flex; align-items:center; justify-content:center; gap:7px;
                min-width:128px; min-height:35px; padding:7px 12px;
                background:rgba(62,41,31,.91); color:#fffaf1;
                border:1px solid rgba(255,252,246,.45); border-radius:999px;
                box-shadow:0 5px 17px rgba(36,24,15,.17);
                font:700 10px/1.2 system-ui,sans-serif;
                white-space:nowrap; opacity:.87;
                transition:opacity .2s ease,background .2s ease,box-shadow .2s ease;
                cursor:pointer;
            }
            .vault-v18-photo .vault-v38-photo-edit svg { width:15px; height:15px; flex:0 0 15px; }
            .vault-v18-photo .vault-v38-photo-edit:hover,
            .vault-v18-photo .vault-v38-photo-edit:focus-visible {
                opacity:1; background:#6b241a;
                box-shadow:0 6px 18px rgba(36,24,15,.28);
                outline:2px solid #d8ac73; outline-offset:2px;
            }
            .vault-v18-photo .master-vault-v4-photo-remove { display:block; margin:11px auto 0; }

            .vault-v18-nature-options { gap:11px; }
            .vault-v18-nature-option {
                position:relative; min-width:0; min-height:77px; border-radius:13px;
                background:linear-gradient(145deg,#fffaf4,#f6efe6);
                transition:background .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease;
            }
            .vault-v18-nature-option:hover { transform:translateY(-1px); box-shadow:0 6px 14px rgba(83,51,27,.07); }
            .vault-v18-nature-option.is-active {
                background:linear-gradient(140deg,#f7eae0,#efe1d5);
                box-shadow:inset 0 0 0 1px rgba(127,65,49,.15),0 3px 12px rgba(83,51,27,.04);
            }
            .vault-v18-nature-option .vault-v38-nature-mark {
                flex:0 0 39px; width:39px; height:39px; border-radius:11px;
                display:grid;place-items:center;
            }
            .vault-v18-nature-option .vault-v38-nature-mark .vault-v36-nature-icon {
                display:block; flex:0 0 auto; width:25px; height:25px;
            }
            .vault-v18-nature-option .vault-v38-nature-mark--magical .vault-v36-nature-icon {
                width:27px; height:27px; color:#bb8d4f;
                font:700 27px/27px Georgia,serif;text-align:center;
            }
            .vault-v18-nature-option .vault-v38-nature-mark--technology .vault-v36-nature-icon,
            .vault-v18-nature-option .vault-v38-nature-mark--mundane .vault-v36-nature-icon {
                -webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;
                -webkit-mask-size:contain;mask-size:contain;
                -webkit-mask-position:center;mask-position:center;
            }
            .vault-v18-nature-option .vault-v38-nature-mark--technology .vault-v36-nature-icon {
                background-color:#496e86;
                -webkit-mask-image:url('{{ asset('images/vault/icons/technological.png') }}');
                mask-image:url('{{ asset('images/vault/icons/technological.png') }}');
            }
            .vault-v18-nature-option .vault-v38-nature-mark--mundane .vault-v36-nature-icon {
                background-color:#8c735f;
                -webkit-mask-image:url('{{ asset('images/vault/icons/mundane.png') }}');
                mask-image:url('{{ asset('images/vault/icons/mundane.png') }}');
            }
            .vault-v18-nature-option:has(.vault-v38-nature-mark--technology).is-active {
                border-color:#7c9baf; background:linear-gradient(140deg,#e9f0f4,#e1eaf2);
                box-shadow:inset 0 0 0 1px rgba(64,110,143,.14);
            }
            .vault-v18-nature-option:has(.vault-v38-nature-mark--mundane).is-active {
                border-color:#a29ba4; background:linear-gradient(140deg,#f0eff3,#e8e5eb);
                box-shadow:inset 0 0 0 1px rgba(92,91,108,.12);
            }

            .vault-v38-rarity-field {order:-1;--v38-rarity-color:#ccb89e;}
            .vault-v38-rarity-field > label {display:block;margin-bottom:8px;color:#632b22;font-size:11px;font-weight:750;}
            .vault-v38-rarity-hint {
                margin-top:8px;display:flex;align-items:center;gap:7px;
                font:600 10px/1.4 system-ui,sans-serif;color:#9c7745;
            }
            .vault-v38-rarity-glyph {font:900 19px/1 Georgia,serif;}
            .vault-v38-rarity-field[data-nature="technological"] .vault-v38-rarity-hint {color:#4b7896;}
            .vault-v38-rarity-field[data-nature="mundane"] .vault-v38-rarity-hint {color:#6e6779;}
            .vault-v38-rarity-field[data-rarity="legendary"] .master-vault-v4-select,
            .vault-v38-rarity-field[data-rarity="artifact"] .master-vault-v4-select {
                border-color:var(--v38-rarity-color);
            }
            .vault-v38-rarity-field[data-nature="wonderful"][data-rarity="legendary"],
            .vault-v38-rarity-field[data-nature="wonderful"][data-rarity="artifact"] {--v38-rarity-color:#b18b45;color:#b18b45;}
            .vault-v38-rarity-field[data-nature="technological"][data-rarity="legendary"],
            .vault-v38-rarity-field[data-nature="technological"][data-rarity="artifact"] {--v38-rarity-color:#5d93ac;color:#5d93ac;}
            .vault-v38-rarity-field[data-nature="mundane"][data-rarity="legendary"] {--v38-rarity-color:#8694a4;color:#8694a4;}
            .vault-v38-rarity-field[data-nature="mundane"][data-rarity="artifact"] {--v38-rarity-color:#7d708e;color:#7d708e;}
            .vault-v38-rarity-field .master-vault-v4-select {color:#5d3728;}

            /* Paleta completa, diferenciando natureza e potência. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="legendary"] {
                --v38-edge:#c59d58;--v38-shade:#f1dfbc;--v38-pale:#fff7e7;
                --v38-ink:#956a29;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] {
                --v38-edge:#b88632;--v38-shade:#e9ce91;--v38-pale:#fff2d5;
                --v38-ink:#875b17;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] {
                --v38-edge:#5598ba;--v38-shade:#7caec4;--v38-pale:#edf8fb;
                --v38-ink:#336e91;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] {
                --v38-edge:#4e79bf;--v38-shade:#7799cf;--v38-pale:#ebf0ff;
                --v38-ink:#425eac;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] {
                --v38-edge:#8e9fad;--v38-shade:#c5d0d9;--v38-pale:#f5f8fa;
                --v38-ink:#657787;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] {
                --v38-edge:#84728f;--v38-shade:#bbb0c4;--v38-pale:#f6f3f8;
                --v38-ink:#665873;
            }
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"],
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"] {
                border-color:var(--v38-edge) !important;
                background:linear-gradient(125deg,#fffdfa 35%,var(--v38-pale)) !important;
                box-shadow:0 0 0 1px color-mix(in srgb,var(--v38-edge) 19%,transparent),
                           0 8px 21px color-mix(in srgb,var(--v38-edge) 14%,transparent) !important;
            }
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"]:hover,
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"]:hover {
                border-color:var(--v38-ink) !important;
                box-shadow:0 0 0 1px color-mix(in srgb,var(--v38-edge) 25%,transparent),
                           0 14px 31px color-mix(in srgb,var(--v38-edge) 25%,transparent) !important;
            }
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"] .vault-v35-portrait,
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"] .vault-v35-portrait {
                background:linear-gradient(155deg,var(--v38-pale),var(--v38-shade)) !important;
                border-right-color:var(--v38-edge) !important;
            }
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"] .master-vault-v10-name,
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"] .master-vault-v10-name {
                color:color-mix(in srgb,var(--v38-ink) 34%,#422820);
            }
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"] .vault-v36-nature-icon--magical,
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"] .vault-v36-nature-icon--magical {
                color:var(--v38-ink);
            }
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"] .vault-v36-nature-icon--technology,
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"] .vault-v36-nature-icon--technology,
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"] .vault-v36-nature-icon--mundane,
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"] .vault-v36-nature-icon--mundane {
                background-color:var(--v38-ink);
            }
            .master-vault-page .vault-v38-rarity-badge {
                display:inline-flex;align-items:center;gap:4px;
                width:max-content;max-width:100%;margin-top:7px;
                border:1px solid color-mix(in srgb,var(--v38-edge,#b99c70) 48%,transparent);
                border-radius:999px;background:color-mix(in srgb,var(--v38-pale,#f8ecd8) 84%,white);
                color:var(--v38-ink,#916b43);padding:3px 8px;
                font:800 9px/1.3 Georgia,serif;
            }
            /* Vestígios continuam com o acabamento mágico de Artefato. */
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .vault-v35-portrait {
                background:linear-gradient(145deg,#faf1d7,#e9ce91) !important;
            }

            /* Circuitos e fagulhas se movem discretamente, sem imagens externas. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                isolation:isolate;
                background:radial-gradient(ellipse at 50% 36%,#c7edfb,#376183 78%,#243e64) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 50% 35%,#d6eaff,#415c9a 76%,#253555) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait::before,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::before {
                z-index:0;inset:9px;
                background:repeating-linear-gradient(90deg,transparent 0 16px,rgba(178,233,255,.14) 17px 18px,transparent 19px 31px),
                           repeating-linear-gradient(0deg,transparent 0 25px,rgba(199,232,255,.09) 26px 27px,transparent 28px 44px);
                border-color:rgba(176,227,255,.36);
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait::after,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {
                z-index:2;inset:0;pointer-events:none;opacity:.62;
                background:radial-gradient(circle,#b8f5ff 0 1.4px,transparent 2.2px) 17% 85%/54px 71px,
                           radial-gradient(circle,#ecffff 0 1px,transparent 2px) 75% 45%/42px 98px,
                           radial-gradient(circle,#8bd6ff 0 1.5px,transparent 2.4px) 48% 90%/71px 85px;
                border-radius:inherit;
                animation:vault-v38-circuits-rise 6.5s linear infinite;
            }
            @keyframes vault-v38-circuits-rise {
                0% {background-position:17% 85%,75% 45%,48% 90%;opacity:.12;}
                18% {opacity:.55;}
                84% {opacity:.57;}
                100% {background-position:17% -105%,75% -110%,48% -100%;opacity:.10;}
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait img,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait img {
                filter:drop-shadow(0 5px 9px rgba(0,25,46,.42));
            }

            /* Mundano lendário: aço gravado; Artefato: obsidiana e violeta.
               Nenhum acabamento dourado é aplicado aos Mundanos. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v35-portrait {
                background:linear-gradient(138deg,#f4f7fa,#c5cdd7 51%,#8a9cae) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 50% 35%,#e4dce8,#978ba4 62%,#514857) !important;
            }

            /* Visualização do Mestre: também é teleportada para o body. */
            .master-vault-v9-preview[data-rarity="legendary"],
            .master-vault-v9-preview[data-rarity="artifact"] {
                --v38-modal-edge:#c7a46d;--v38-modal-tint:#f7e7c9;--v38-modal-ink:#785323;
                border-color:var(--v38-modal-edge) !important;
                box-shadow:0 24px 65px rgba(35,25,17,.33),0 0 0 2px color-mix(in srgb,var(--v38-modal-edge) 22%,transparent);
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="legendary"] {--v38-modal-edge:#c5a067;--v38-modal-tint:#f7e9cb;--v38-modal-ink:#8f642e;}
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] {--v38-modal-edge:#b58836;--v38-modal-tint:#f2dfb8;--v38-modal-ink:#79521c;}
            .master-vault-v9-preview[data-nature="technological"][data-rarity="legendary"] {--v38-modal-edge:#5694b4;--v38-modal-tint:#daedf7;--v38-modal-ink:#386e90;}
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] {--v38-modal-edge:#647dc1;--v38-modal-tint:#e0e8fc;--v38-modal-ink:#4c60a1;}
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="legendary"] {--v38-modal-edge:#8798aa;--v38-modal-tint:#e3e9f0;--v38-modal-ink:#5f7285;}
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] {--v38-modal-edge:#81708d;--v38-modal-tint:#e9e1ef;--v38-modal-ink:#675775;}
            .master-vault-v9-preview[data-rarity="legendary"] .master-vault-v9-preview-head,
            .master-vault-v9-preview[data-rarity="legendary"] .master-vault-v9-preview-foot,
            .master-vault-v9-preview[data-rarity="artifact"] .master-vault-v9-preview-head,
            .master-vault-v9-preview[data-rarity="artifact"] .master-vault-v9-preview-foot {
                background:linear-gradient(110deg,var(--v38-modal-tint),#fbf8f1);
                border-color:var(--v38-modal-edge);
            }
            .master-vault-v9-preview[data-rarity="legendary"] .master-vault-v16-preview-image,
            .master-vault-v9-preview[data-rarity="artifact"] .master-vault-v16-preview-image {
                background:radial-gradient(circle at 50% 35%,#fff,var(--v38-modal-tint)) !important;
                border-color:var(--v38-modal-edge);
            }
            .master-vault-v9-preview[data-rarity="legendary"] .master-vault-v16-preview-name,
            .master-vault-v9-preview[data-rarity="artifact"] .master-vault-v16-preview-name {
                color:var(--v38-modal-ink);
            }
            /* Modais de Artefato mundano ou tecnológico nunca herdam o dourado de Vestígio. */
            .master-vault-v9-preview:not([data-nature="wonderful"]).vault-v23-preview-vestige {
                border-color:var(--v38-modal-edge);
            }
            @media (max-width:860px) {
                .vault-v18-photo .vault-v38-photo-edit {bottom:0;left:auto;right:-2px;transform:none;min-width:35px;width:35px;padding:7px;}
                .vault-v18-photo .vault-v38-photo-edit span {display:none;}
                .vault-v18-photo .master-vault-v4-photo-remove {font-size:9px;}
                .vault-v18-nature-option .vault-v38-nature-mark {width:34px;height:34px;flex-basis:34px;}
            }
            @media (max-width:640px) {
                .vault-v18-photo .vault-v38-photo-edit {right:-12px;}
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"] .vault-v35-portrait::after,
                .vault-v18-nature-option {animation:none!important;transition:none!important;}
                .vault-v18-nature-option:hover {transform:none;}
            }

            /* ===========================================================
               V66.1.39 — editor compacto, recorte de foto e cards refinados
               =========================================================== */
            .master-vault-v4-modal { width:min(1450px,95vw); }
            .master-vault-v4-scroll { padding:20px 22px 26px; }
            .vault-v18-workspace { grid-template-columns:154px minmax(0,1fr); gap:17px; }
            .vault-v18-sidebar { top:8px; }
            .vault-v18-sidebar-card { width:154px;max-width:100%;margin:0 auto; }
            .vault-v18-photo .master-vault-v4-photo-frame { border-radius:15px; }
            .vault-v18-photo .vault-v38-photo-edit {
                left:50%;right:auto;bottom:33px;width:calc(100% - 16px);min-width:0;
                min-height:30px;transform:translateX(-50%);padding:5px 8px;
                font-size:9px;opacity:0;transition:opacity .18s ease,background .18s ease;
            }
            .vault-v18-photo:hover .vault-v38-photo-edit,
            .vault-v18-photo:focus-within .vault-v38-photo-edit {opacity:1;}
            .vault-v18-photo .master-vault-v4-photo-remove {font-size:9px;}
            .vault-v18-card { padding:17px 19px; }
            .vault-v18-card-heading { margin-bottom:15px;padding-bottom:12px; }
            .vault-v18-nature-options {gap:9px;}
            .vault-v18-nature-option {
                min-height:65px;gap:11px;padding:10px 11px;
                border:1px solid #ded0c2;border-radius:12px;
                background:#fbf7f1 !important;box-shadow:none !important;
                transition:border-color .18s ease,background .18s ease,transform .18s ease;
            }
            .vault-v18-nature-option:hover {transform:translateY(-1px);background:#fffdfa !important;}
            .vault-v18-nature-option.is-active {background:#fffaf4 !important;border-color:#a78d73;box-shadow:inset 0 0 0 1px #d9c5aa !important;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--magical).is-active {border-color:#c8a269;box-shadow:inset 0 0 0 1px rgba(181,139,77,.16) !important;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--technology).is-active {border-color:#829eaf;box-shadow:inset 0 0 0 1px rgba(65,103,132,.13) !important;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--mundane).is-active {border-color:#a99c91;box-shadow:inset 0 0 0 1px rgba(120,104,89,.10) !important;}
            .vault-v18-nature-option .vault-v38-nature-mark,
            .vault-v18-nature-option.is-active .vault-v38-nature-mark {
                width:42px;height:42px;flex-basis:42px;border-radius:11px;
                background:#f5ece0;border-color:#e4d3be;
            }
            .vault-v18-nature-option .vault-v38-nature-mark--technology {background:#edf2f6 !important;border-color:#cfdce6 !important;}
            .vault-v18-nature-option .vault-v38-nature-mark--mundane {background:#f1e9df !important;}
            .vault-v18-nature-option .vault-v38-nature-mark--magical {background:#fbf0dc !important;border-color:#ebd4a7 !important;}
            .vault-v18-nature-option .vault-v38-nature-mark--magical .vault-v36-nature-icon {color:#b88a3e !important;font-size:31px !important;}
            .vault-v18-nature-option .vault-v39-nature-image {display:block;max-width:none;width:25px;height:25px;object-fit:contain;opacity:.78;}
            .vault-v18-nature-option .vault-v39-nature-image--tech {filter:invert(35%) sepia(16%) saturate(1644%) hue-rotate(161deg) brightness(91%);}
            .vault-v18-nature-option .vault-v39-nature-image--mundane {filter:invert(45%) sepia(11%) saturate(843%) hue-rotate(345deg) brightness(97%);}
            .vault-v18-nature-option strong {color:#5f3027;font:750 11px/1.3 system-ui,sans-serif;}
            .vault-v18-nature-option small {font-size:9px;color:#97806b;}
            .vault-v39-nature-selected {
                margin-left:auto;align-self:flex-start;width:17px;height:17px;flex:0 0 17px;
                display:grid;place-items:center;border-radius:100%;background:#e9dfd2;color:#74513c;
                font:800 10px/1 system-ui,sans-serif;opacity:0;transition:opacity .16s ease;
            }
            .vault-v18-nature-option.is-active .vault-v39-nature-selected {opacity:1;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--technology).is-active .vault-v39-nature-selected {background:#dbe8f0;color:#3f7895;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--magical).is-active .vault-v39-nature-selected {background:#f5e4c4;color:#a57b31;}

            /* Editor de imagem incorporado: o recorte é gerado no navegador. */
            .vault-v39-crop-overlay {
                position:fixed;inset:0;z-index:900;display:grid;place-items:center;
                padding:18px;isolation:isolate;
            }
            .vault-v39-crop-backdrop {position:absolute;inset:0;background:rgba(25,17,14,.76);backdrop-filter:blur(5px);}
            .vault-v39-crop-panel {
                position:relative;z-index:1;width:min(680px,100%);max-height:min(92dvh,760px);overflow:auto;
                border:1px solid #d8bf9b;border-radius:17px;background:#faf6ed;
                box-shadow:0 25px 75px rgba(24,16,11,.35);color:#573329;
            }
            .vault-v39-crop-header {display:flex;justify-content:space-between;gap:16px;padding:19px 22px 15px;border-bottom:1px solid #e2d2bc;}
            .vault-v39-crop-header small {font:800 9px/1.3 system-ui,sans-serif;letter-spacing:.12em;color:#947150;}
            .vault-v39-crop-header h3 {margin:4px 0 0;font:700 23px/1.2 Georgia,serif;color:#69271c;}
            .vault-v39-crop-header p {margin:5px 0 0;font:500 11px/1.5 system-ui,sans-serif;color:#93735a;}
            .vault-v39-crop-header>button {align-self:flex-start;width:29px;height:29px;border-radius:8px;font-size:23px;color:#8e6f57;}
            .vault-v39-crop-header>button:hover {background:#eee3d3;}
            .vault-v39-crop-layout {display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:23px;padding:22px;}
            .vault-v39-crop-stage {
                width:280px;height:280px;flex:0 0 280px;max-width:100%;position:relative;
                overflow:hidden;border-radius:14px;border:1px solid #c5a57e;
                background-color:#eee6d9;
                background-image:linear-gradient(45deg,#e1d8c9 25%,transparent 25%),linear-gradient(-45deg,#e1d8c9 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#e1d8c9 75%),linear-gradient(-45deg,transparent 75%,#e1d8c9 75%);
                background-size:24px 24px;background-position:0 0,0 12px,12px -12px,-12px 0;
                cursor:grab;touch-action:none;user-select:none;
            }
            .vault-v39-crop-stage:active {cursor:grabbing;}
            .vault-v39-crop-stage img {position:absolute;top:50%;left:50%;max-width:none;max-height:none;pointer-events:none;user-select:none;transform-origin:center center;}
            .vault-v39-crop-guides {position:absolute;inset:12px;border:1px solid rgba(255,255,255,.67);border-radius:10px;pointer-events:none;box-shadow:0 0 0 1px rgba(88,58,34,.17);}
            .vault-v39-crop-tools {flex:1 1 180px;max-width:235px;display:flex;flex-direction:column;gap:11px;}
            .vault-v39-crop-tools label {display:flex;justify-content:space-between;font:750 11px/1.3 system-ui,sans-serif;}
            .vault-v39-crop-tools input[type="range"] {width:100%;accent-color:#833e2d;}
            .vault-v39-crop-tools p {margin:2px 0 4px;color:#8b6b54;font:500 11px/1.5 system-ui,sans-serif;}
            .vault-v39-crop-tools button {border:1px solid #d6c4ac;border-radius:9px;padding:9px 12px;text-align:left;background:#fffaf2;color:#6f4635;font:700 11px/1.3 system-ui,sans-serif;}
            .vault-v39-crop-tools button:hover {background:#efe4d5;}
            .vault-v39-crop-footer {display:flex;justify-content:flex-end;gap:9px;padding:14px 20px;border-top:1px solid #e2d2bc;}
            .vault-v39-crop-footer button {min-height:37px;padding:0 13px;border-radius:9px;border:1px solid #d7c3a9;background:#fffaf4;color:#794e3d;font:750 10px/1 system-ui,sans-serif;}
            .vault-v39-crop-footer .vault-v39-crop-apply {border-color:#6f2419;background:#6f2419;color:#fff9ef;}
            .vault-v39-crop-footer .vault-v39-crop-apply:disabled {opacity:.5;cursor:not-allowed;}
            @media (max-width:860px) {
                .vault-v18-workspace {grid-template-columns:minmax(0,1fr);}
                .vault-v18-sidebar {position:static;}
                .vault-v18-sidebar-card {width:132px;}
                .vault-v18-photo .vault-v38-photo-edit {opacity:1;bottom:29px;right:auto;left:50%;width:calc(100% - 13px);height:28px;min-height:28px;transform:translateX(-50%);}
                .vault-v18-photo .vault-v38-photo-edit span {display:inline;}
            }
            @media (max-width:640px) {
                .master-vault-v4-modal {width:100%;}
                .master-vault-v4-scroll {padding:13px 11px 20px;}
                .vault-v18-nature-options {grid-template-columns:1fr;}
                .vault-v18-nature-option {min-height:53px;}
                .vault-v39-crop-layout {padding:14px;gap:16px;}
                .vault-v39-crop-footer {padding:12px;}
            }

            /* Cards: protagonista é a imagem e o nome, não a caixa das habilidades. */
            .master-vault-page .master-vault-v10-card {min-height:211px;grid-template-rows:auto minmax(0,1fr) auto;}
            .master-vault-page .master-vault-v10-card .master-vault-v10-identity {padding:14px 43px 3px 15px !important;}
            .master-vault-page .master-vault-v10-card .vault-v35-item-mark {margin-bottom:2px;height:14px;}
            .master-vault-page .master-vault-v10-card .master-vault-v10-name {font-size:17px !important;line-height:1.2 !important;color:#4c2e29;}
            .master-vault-page .master-vault-v10-card .master-vault-v10-nature {margin-top:6px !important;line-height:1.4 !important;}
            .master-vault-page .master-vault-v10-card .master-vault-v10-category {margin-top:4px !important;}
            .master-vault-page .master-vault-v10-card > .master-vault-v10-section:not(.vault-v35-features-section) {
                border-top:0 !important;margin:0 15px 5px 15px !important;padding:4px 0 !important;
            }
            .master-vault-page .master-vault-v10-card .vault-v34-card-rich-excerpt.master-vault-v10-description {
                color:#755b49;font-size:11.2px !important;line-height:1.48 !important;
                max-height:4.45em !important;
            }
            .master-vault-page .master-vault-v10-card .vault-v34-card-rich-excerpt p+p {margin-top:3px;}
            .master-vault-page .master-vault-v10-card > .vault-v35-features-section {
                margin:0 13px 11px 15px !important;padding:8px 0 0 !important;
                border-top:1px solid rgba(171,139,98,.22) !important;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-abilities {
                padding:0 !important;border:0 !important;border-radius:0 !important;
                background:transparent !important;gap:5px !important;
            }
            .master-vault-page .master-vault-v10-card .master-vault-v10-ability {
                background:rgba(255,252,247,.78) !important;border:1px solid rgba(194,164,125,.32) !important;
                min-height:21px !important;padding:3px 8px !important;font-size:9px !important;color:#78543c;
            }
            .master-vault-page .vault-v35-vestige-summary {
                display:flex;align-items:center;gap:8px;padding:0 !important;
                min-height:27px;width:100%;border:0 !important;border-radius:0 !important;
                background:transparent !important;color:#815f31;
            }
            .master-vault-page .vault-v35-vestige-summary-icon {
                flex:0 0 19px;width:19px;height:19px;border:0 !important;
                border-radius:50% !important;background:rgba(184,145,80,.14) !important;
                font-size:12px;color:#a0793f;
            }
            .master-vault-page .vault-v39-vestige-copy {display:flex !important;flex-wrap:wrap;gap:3px 7px;align-items:baseline;}
            .master-vault-page .vault-v35-vestige-summary strong {font:700 10.5px/1.2 Georgia,serif;color:#795829;}
            .master-vault-page .vault-v35-vestige-summary small {font:550 9px/1.3 system-ui,sans-serif;color:#9c7e53;}
            .master-vault-page .vault-v35-vestige-summary-arrow {margin-left:auto;color:#ad8951;font-size:17px;}
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"]) .vault-v35-portrait {
                background:linear-gradient(150deg,#f7ebcf,#e8ce9d) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 52% 39%,#e9f1f9,#91b3ca 54%,#577c9b) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 50% 38%,#e8effa,#91a9cb 55%,#617aaf) !important;
            }
            .master-vault-page .master-vault-v10-card .vault-v35-portrait::before {border-radius:14px;inset:10px;}
            .master-vault-page .master-vault-v10-card .vault-v35-portrait img {filter:drop-shadow(0 5px 8px rgba(47,33,25,.21));}
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card .vault-v35-portrait img,
                .vault-v18-nature-option,.vault-v18-photo .vault-v38-photo-edit {animation:none!important;transition:none!important;}
            }


            /* ===========================================================
               V66.1.40 — ícones confiáveis, editor de imagem discreto
               e raridades especiais com acabamento refinado.
               =========================================================== */
            :root { --vault-tech-icon-mask:url("data:image/png;base64,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"); }

            /* O símbolo usa os dados da arte original incorporados ao CSS.
               Não depende do caminho public/ após o modal ser teleportado. */
            .vault-v40-tech-symbol,
            .master-vault-page .vault-v36-nature-icon--technology {
                /* Não usar !important: o x-show do Alpine deve poder ocultar
                   este símbolo quando a natureza não for tecnológica. */
                display:block;
                width:24px;height:24px;flex:0 0 24px;
                background-color:#4d7893 !important;
                -webkit-mask:var(--vault-tech-icon-mask) center/contain no-repeat !important;
                mask:var(--vault-tech-icon-mask) center/contain no-repeat !important;
                opacity:1 !important;
            }
            .master-vault-page .vault-v36-nature-icon--technology {
                width:20px;height:20px;flex-basis:20px;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v36-nature-icon--technology,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v36-nature-icon--technology {background-color:#527e9e !important;}

            /* Classificação: opções objetivas, sem caixas ou legendas redundantes. */
            .vault-v18-nature-options {gap:9px !important;grid-template-columns:repeat(3,minmax(0,1fr)) !important;}
            .vault-v18-nature-option {
                display:flex !important;flex-direction:row !important;align-items:center !important;
                min-height:57px !important;padding:9px 13px !important;gap:10px !important;
                border:1px solid #e1d3c2 !important;border-radius:12px !important;
                background:#fffdf8 !important;box-shadow:none !important;
                transform:none !important;transition:border-color .18s ease,background .18s ease,box-shadow .18s ease !important;
            }
            .vault-v18-nature-option:hover {background:#f9f3e9 !important;border-color:#cbb49a !important;}
            .vault-v18-nature-option .vault-v38-nature-mark {
                order:0 !important;display:grid !important;place-items:center !important;
                width:35px !important;height:35px !important;flex:0 0 35px !important;
                border:0 !important;border-radius:10px !important;
                background:#f4eee5 !important;
            }
            .vault-v18-nature-option .vault-v38-nature-mark--technology {background:#e8f1f5 !important;}
            .vault-v18-nature-option .vault-v38-nature-mark--magical {background:#f7ebd3 !important;}
            .vault-v18-nature-option .vault-v38-nature-mark--mundane {background:#ede7df !important;}
            .vault-v18-nature-option .vault-v38-nature-mark--magical .vault-v36-nature-icon {font-size:26px !important;width:26px !important;height:26px !important;line-height:26px !important;}
            .vault-v18-nature-option .vault-v39-nature-image--mundane {width:23px !important;height:23px !important;opacity:.9;}
            .vault-v18-nature-option > span:last-child {order:1 !important;min-width:0;}
            .vault-v18-nature-option strong {font:750 12px/1.2 system-ui,sans-serif !important;color:#553128 !important;}
            .vault-v18-nature-option .vault-v39-nature-selected {order:2 !important;margin-left:auto !important;align-self:center !important;width:19px;height:19px;flex-basis:19px;}
            .vault-v18-nature-option.is-active {background:#f9f3e9 !important;border-color:#a98b6c !important;box-shadow:inset 0 0 0 1px rgba(143,100,69,.12) !important;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--technology).is-active {background:#edf5f8 !important;border-color:#7598ad !important;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--magical).is-active {background:#fbf3e4 !important;border-color:#b99a66 !important;}
            .vault-v18-nature-option:has(.vault-v38-nature-mark--mundane).is-active {background:#f3efea !important;border-color:#aa9b8c !important;}
            .vault-v18-classification-fields {row-gap:10px !important;}
            .vault-v38-rarity-field {min-width:0;}
            .vault-v38-rarity-field .master-vault-v4-select {min-height:43px;}

            /* Ajuste de foto: menos instruções, controles diretos. */
            .vault-v39-crop-panel {width:min(605px,100%) !important;max-height:90dvh;border-radius:15px;}
            .vault-v39-crop-header {padding:14px 19px 12px !important;align-items:center;}
            .vault-v39-crop-header h3 {margin:0;font-size:20px !important;}
            .vault-v39-crop-header small {display:block;margin-top:3px;letter-spacing:0;text-transform:none;font-size:10px;}
            .vault-v39-crop-layout {gap:20px !important;padding:17px 20px 20px !important;align-items:center;}
            .vault-v39-crop-stage {width:248px !important;height:248px !important;flex:0 0 248px !important;border-radius:12px;}
            .vault-v39-crop-stage img {will-change:transform;}
            .vault-v39-crop-guides {inset:9px;}
            .vault-v40-crop-controls {flex:1 1 250px !important;max-width:none !important;gap:15px !important;}
            .vault-v40-crop-control label {margin-bottom:6px;display:flex;align-items:center;justify-content:space-between;font-size:11px !important;}
            .vault-v40-crop-control output {color:#99694a;font-variant-numeric:tabular-nums;font-weight:700;}
            .vault-v40-crop-control input[type=range] {display:block;width:100%;height:18px;cursor:pointer;}
            .vault-v40-align-row {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px;margin-top:8px;}
            .vault-v40-align-row button {min-width:0;padding:7px 3px !important;text-align:center !important;border-radius:7px !important;background:#f6eee3 !important;font-size:10px !important;}
            .vault-v40-align-row button:hover {background:#e8d8c5 !important;}
            .vault-v40-crop-file-actions {display:flex;align-items:center;gap:7px;}
            .vault-v40-crop-file-actions button {flex:1;text-align:center !important;padding:8px 5px !important;font-size:10px !important;}
            .vault-v39-crop-footer {padding:11px 17px !important;}

            /* Raridades: o acabamento pertence à imagem e às bordas,
               sem comprometer a leitura com um bloco saturado. */
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"],
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"] {
                background:linear-gradient(112deg,#fffdf9 42%,color-mix(in srgb,var(--v38-pale) 49%,#fffdf9)) !important;
                box-shadow:0 2px 11px rgba(69,46,32,.045),inset 0 1px 0 rgba(255,255,255,.55) !important;
                border-color:color-mix(in srgb,var(--v38-edge) 74%,#e6d7c4) !important;
            }
            .master-vault-page .master-vault-v10-card[data-rarity="legendary"]:hover,
            .master-vault-page .master-vault-v10-card[data-rarity="artifact"]:hover {
                box-shadow:0 10px 26px color-mix(in srgb,var(--v38-edge) 16%,transparent),inset 0 1px 0 rgba(255,255,255,.65) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] {
                --v38-edge:#659ab4;--v38-shade:#adcdda;--v38-pale:#eff7fa;--v38-ink:#476e84;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] {
                --v38-edge:#7c8fbf;--v38-shade:#aebad9;--v38-pale:#f0f2fa;--v38-ink:#526595;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] {
                --v38-edge:#9ca6b1;--v38-shade:#cbd2d9;--v38-pale:#f6f8fa;--v38-ink:#6d7885;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] {
                --v38-edge:#8f839d;--v38-shade:#bfb6ca;--v38-pale:#f7f3f8;--v38-ink:#6b6079;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 50% 33%,#f6fbfd 4%,#c5dce8 60%,#92b6ca) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 50% 31%,#fafbff 4%,#ccd7ec 61%,#98aacb) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait::before,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::before {
                background:repeating-linear-gradient(90deg,transparent 0 23px,rgba(80,137,170,.09) 24px,transparent 25px 40px),
                           repeating-linear-gradient(0deg,transparent 0 26px,rgba(80,137,170,.07) 27px,transparent 28px 52px) !important;
                border-color:rgba(99,151,181,.25) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait::after,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {
                background:radial-gradient(circle,rgba(97,176,210,.7) 0 1px,transparent 2px) 20% 84%/53px 79px,
                           radial-gradient(circle,rgba(138,198,220,.65) 0 1px,transparent 2px) 80% 40%/71px 104px !important;
                opacity:.23 !important;
                animation:vault-v40-drift 12s linear infinite !important;
            }
            @keyframes vault-v40-drift {
                0%{background-position:20% 84%,80% 40%;opacity:.12;}
                50%{opacity:.30;}
                100%{background-position:20% -60%,80% -75%;opacity:.12;}
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"] .vault-v35-portrait img {
                filter:drop-shadow(0 5px 7px rgba(41,67,82,.17)) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="legendary"] .vault-v35-portrait,
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .vault-v35-portrait {
                background:linear-gradient(148deg,#fff8e7,#edddb8) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v35-portrait {
                background:linear-gradient(141deg,#f9fafb,#d4dae0) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait {
                background:linear-gradient(145deg,#f0eaf2,#b2a9bd) !important;
            }
            /* A aura dos Vestígios acompanha o pergaminho, sem disputar com a leitura. */
            .master-vault-page .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"])::after {
                opacity:.32 !important;filter:blur(4px) !important;animation-duration:12s !important;
            }
            /* Artefatos mundanos: detalhe metálico/violeta, sem brilho dourado. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v35-portrait::after,
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait::after {
                opacity:.35;background:linear-gradient(118deg,transparent 25%,rgba(255,255,255,.24) 47%,transparent 68%) !important;
            }
            @media (max-width:720px) {
                .vault-v18-nature-options {grid-template-columns:1fr !important;gap:6px !important;}
                .vault-v18-nature-option {min-height:49px !important;}
                .vault-v39-crop-layout {flex-direction:column;gap:13px !important;}
                .vault-v40-crop-controls {width:100%;}
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait::after,
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {animation:none !important;}
            }


            /* ===============================================================
               SPELLBOUND V66.1.41 — MUNDANO LENDÁRIO / ARTEFATO
               Aço polido com reflexo solar; prata perolada para Artefatos.
               O filete luminoso percorre a borda sem cobrir ações ou conteúdo.
               =============================================================== */
            @property --vault-v41-metal-angle {
                syntax: '<angle>';
                inherits: false;
                initial-value: 0deg;
            }

            /* Lendário: aço claro polido, com reflexo frio. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] {
                --v38-edge:#9fb4c4;
                --v38-shade:#dce7ef;
                --v38-pale:#f5faff;
                --v38-ink:#5b7080;
                --vault-v41-metal-edge:#91a7b9;
                --vault-v41-metal-flash:#f9fdff;
                --vault-v41-metal-period:9s;
                background:linear-gradient(114deg,#fffefa 29%,#f1f6fa 75%,#f9fbfd) !important;
                border-color:#a8bdcc !important;
                box-shadow:0 3px 13px rgba(76,101,122,.075),inset 0 1px 0 rgba(255,255,255,.93) !important;
            }

            /* Artefato: prata luminosa (não cinza-escuro, violeta ou ouro). */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] {
                --v38-edge:#bdcad8;
                --v38-shade:#ecf3f8;
                --v38-pale:#fcfeff;
                --v38-ink:#647c91;
                --vault-v41-metal-edge:#a4bbcc;
                --vault-v41-metal-flash:#ffffff;
                --vault-v41-metal-period:6.4s;
                background:linear-gradient(118deg,#ffffff 25%,#f2f8fc 64%,#fffefe) !important;
                border-color:#b8ccda !important;
                box-shadow:0 4px 18px rgba(119,152,178,.13),inset 0 1px 0 #fff !important;
            }

            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]):hover,
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]):focus-within {
                box-shadow:0 11px 29px rgba(111,142,167,.18),0 0 0 1px rgba(240,249,255,.88) !important;
                border-color:var(--vault-v41-metal-edge) !important;
            }

            /* Máscara: somente a borda recebe o reflexo que circula no card.
               O interior continua livre para textos, imagem e menu. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                content:'';
                display:block !important;
                position:absolute;
                z-index:5;
                inset:-2px;
                width:auto;
                height:auto;
                padding:2px;
                border:0;
                border-radius:20px;
                pointer-events:none;
                background:conic-gradient(
                    from var(--vault-v41-metal-angle),
                    var(--vault-v41-metal-edge) 0deg,
                    #c4d4df 56deg,
                    #eef6fb 72deg,
                    var(--vault-v41-metal-flash) 83deg,
                    #ffffff 88deg,
                    #d7e6f0 99deg,
                    var(--vault-v41-metal-edge) 118deg,
                    #9fb5c6 220deg,
                    #dae5ec 300deg,
                    var(--vault-v41-metal-edge) 360deg
                );
                -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                -webkit-mask-composite:xor;
                mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                mask-composite:exclude;
                animation:vault-v41-metal-sun-pass var(--vault-v41-metal-period) linear infinite;
            }
            @keyframes vault-v41-metal-sun-pass {
                to { --vault-v41-metal-angle:360deg; }
            }

            /* A foto permanece legível: o feixe de luz atravessa apenas por
               instantes, com reflexo perolado menos intenso no aço. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v35-portrait {
                background:linear-gradient(144deg,#fff 0%,#e8f1f7 38%,#c4d2de 83%,#f4f9fd 100%) !important;
                border-right-color:#b7c7d4 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 46% 33%,#ffffff 0%,#f3f9fd 37%,#d8e5ee 83%,#f8fcff 100%) !important;
                border-right-color:#cadce8 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait::before {
                border-color:rgba(255,255,255,.80) !important;
                box-shadow:0 0 8px rgba(249,253,255,.35);
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait::after {
                z-index:2;
                inset:0;
                border-radius:inherit;
                background:linear-gradient(110deg,
                    transparent 34%,rgba(255,255,255,.02) 42%,
                    rgba(255,255,255,.72) 49%,rgba(255,255,255,.10) 56%,
                    transparent 64%) 115% 0 / 260% 100% no-repeat !important;
                opacity:0;
                pointer-events:none;
                animation:vault-v41-photo-reflection 9s ease-in-out infinite;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait::after {
                animation-duration:6.8s;
            }
            @keyframes vault-v41-photo-reflection {
                0%,22% { background-position:115% 0;opacity:0; }
                36% { opacity:.57; }
                54%,100% { background-position:-115% 0;opacity:0; }
            }

            /* Visualização detalhada do Mestre (modal teleportado ao body). */
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="legendary"] {
                --v38-modal-edge:#a5bbcc;
                --v38-modal-tint:#eaf3f9;
                --v38-modal-ink:#5d7384;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] {
                --v38-modal-edge:#bed0dd;
                --v38-modal-tint:#f2f9fd;
                --v38-modal-ink:#617f95;
                box-shadow:0 24px 65px rgba(35,25,17,.30),0 0 0 2px rgba(244,251,255,.86),0 0 21px rgba(202,224,239,.32) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image {
                background:linear-gradient(140deg,#fff,#e8f2f9) !important;
                border-color:var(--v38-modal-edge) !important;
            }

            /* Transição acessível: luz estática caso animações estejam reduzidas. */
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait::after {
                    animation:none !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait::after {
                    opacity:0 !important;
                }
            }

            /* Spellbound V66.1.43 — Natureza como etiqueta sobre a imagem.
               A miniatura carrega a classificação; o nome tem leitura livre. */
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag {
                position:absolute;
                z-index:6;
                top:14px;
                left:14px;
                display:flex;
                align-items:center;
                justify-content:center;
                width:30px;
                height:30px;
                min-width:30px;
                min-height:30px;
                padding:0;
                border:1px solid rgba(176,143,106,.45);
                border-radius:10px;
                background:rgba(255,251,243,.94);
                box-shadow:0 2px 9px rgba(60,43,29,.105),inset 0 1px 0 rgba(255,255,255,.72);
                color:#8d6848;
                pointer-events:none;
            }
            .master-vault-page .vault-v43-nature-tag .vault-v36-nature-icon {
                display:block;
                width:15px;
                height:15px;
                flex:0 0 15px;
                margin:0;
            }
            .master-vault-page .vault-v43-nature-tag .vault-v36-nature-icon--magical {
                font:700 16px/15px Georgia,serif;
                color:#ae7a2c;
            }
            .master-vault-page .vault-v43-nature-tag .vault-v36-nature-icon--technology {
                width:15px;
                height:15px;
                flex-basis:15px;
                background-color:#4e7997 !important;
            }
            .master-vault-page .vault-v43-nature-tag .vault-v36-nature-icon--mundane {
                width:15px;
                height:15px;
                flex-basis:15px;
                background-color:#927761;
            }
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag[data-nature="wonderful"] {
                background:rgba(255,249,232,.96);
                border-color:rgba(198,163,98,.57);
                color:#835b24;
            }
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag[data-nature="technological"] {
                background:rgba(239,247,252,.96);
                border-color:rgba(113,158,185,.51);
                color:#416d87;
            }
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag[data-nature="mundane"] {
                background:rgba(255,250,243,.94);
                border-color:rgba(158,137,112,.42);
                color:#7e6651;
            }
            /* Sem o ícone acima do nome, o título usa a primeira linha do card. */
            .master-vault-page .master-vault-v10-card .master-vault-v10-identity {
                padding-top:12px !important;
            }
            @media(max-width:700px) {
                .master-vault-page .master-vault-v10-card .vault-v43-nature-tag {
                    top:11px;
                    left:11px;
                    width:28px;
                    height:28px;
                    min-width:28px;
                    min-height:28px;
                }
                .master-vault-page .master-vault-v10-card .master-vault-v10-identity {padding-top:11px !important;}
            }


            /* ===============================================================
               SPELLBOUND V66.1.45 — MUNDANO LENDÁRIO / ARTEFATO
               Aço luminoso contido vs. prata singular e radiante.
               Sem qualquer interferência nas naturezas mágica/tecnológica.
               =============================================================== */

            /* Ícones de natureza: selo sem moldura, flutuando na miniatura. */
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag,
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag[data-nature] {
                top: 12px;
                left: 13px;
                width: 25px;
                height: 25px;
                min-width: 25px;
                min-height: 25px;
                padding: 0;
                border: 0 !important;
                border-radius: 0;
                background: transparent !important;
                box-shadow: none !important;
                filter: drop-shadow(0 2px 2px rgba(40, 33, 27, .27));
                pointer-events: none;
            }
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag .vault-v36-nature-icon {
                width: 21px;
                height: 21px;
                flex-basis: 21px;
                margin: 0;
            }
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag .vault-v36-nature-icon--magical {
                font: 700 24px/21px Georgia, serif;
                text-shadow: 0 1px 2px rgba(91,60,19,.12);
            }
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag .vault-v36-nature-icon--technology,
            .master-vault-page .master-vault-v10-card .vault-v43-nature-tag .vault-v36-nature-icon--mundane {
                width: 21px;
                height: 21px;
                flex-basis: 21px;
            }
            @media(max-width:700px) {
                .master-vault-page .master-vault-v10-card .vault-v43-nature-tag,
                .master-vault-page .master-vault-v10-card .vault-v43-nature-tag[data-nature] {
                    top: 10px; left: 11px;
                    width: 24px; height: 24px;
                    min-width: 24px; min-height: 24px;
                }
            }

            /* Passe do mouse: um movimento quase imperceptível, como uma carta. */
            .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging) {
                transform-origin: 50% 62%;
                transition: transform .42s cubic-bezier(.2,.8,.22,1),
                    box-shadow .42s ease, border-color .3s ease, opacity .2s ease !important;
            }
            .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging):hover {
                transform: translateY(-3px) rotate(-.18deg) scale(1.004) !important;
            }
            .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging):focus-visible {
                outline: 2px solid rgba(115,93,72,.38);
                outline-offset: 3px;
            }
            .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging):hover .vault-v35-portrait img {
                transform: translateY(-2px) scale(1.038) !important;
            }

            /* LENDÁRIO MUNDANO — metal polido, prata acinzentada neutra.
               O brilho pertence ao filete da borda, nunca ao miolo. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] {
                --v38-edge: #a4a9af;
                --v38-shade: #d8dade;
                --v38-pale: #f7f7f8;
                --v38-ink: #555b63;
                --vault-v41-metal-edge: #a1a7ad;
                --vault-v41-metal-flash: #ffffff;
                --vault-v41-metal-period: 10s;
                border-color: #a7adb3 !important;
                background: linear-gradient(124deg,#fafafa 0%,#f0f1f2 45%,#fafbfc 100%) !important;
                box-shadow: 0 2px 10px rgba(55,59,66,.08),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"]::before {
                padding: 1.5px;
                inset: -1.5px;
                border-radius: 19px;
                background: conic-gradient(from var(--vault-v41-metal-angle),
                    #979da4 0deg,#c4c8cd 50deg,#e9ebee 74deg,
                    #fff 86deg,#bec4c9 108deg,#989fa5 150deg,
                    #d5d9dc 260deg,#a3a8ae 360deg);
                opacity: .95;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v35-portrait {
                background: linear-gradient(137deg,#f9f9f9 0%,#e7e9eb 42%,#bfc5cb 88%,#eeeef0) !important;
                border-right-color: #b6bcc2 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .master-vault-v10-name {
                color: #464a50 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v36-nature-icon--mundane {
                background-color: #59616a !important;
            }

            /* ARTEFATO MUNDANO — prata viva, quase joalheria.
               Fundo, imagem, texto, moldura e rodapé participam do efeito. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] {
                --v38-edge: #bcc2ca;
                --v38-shade: #e6e6e9;
                --v38-pale: #fdfdfd;
                --v38-ink: #464b58;
                --vault-v41-metal-edge: #9fa6b0;
                --vault-v41-metal-flash: #fff;
                --vault-v41-metal-period: 5.6s;
                border: 2px solid #bcc0c8 !important;
                border-radius: 20px !important;
                background:
                    radial-gradient(ellipse 95% 150% at 91% 20%,rgba(255,255,255,.96) 0%,rgba(255,255,255,.0) 68%),
                    radial-gradient(ellipse 78% 140% at 9% 80%,rgba(178,180,186,.56) 0%,transparent 76%),
                    linear-gradient(113deg,#cbced4 0%,#fdfdfd 32%,#fff 47%,#c8cbd2 80%,#fdfdfd 100%) !important;
                box-shadow: 0 0 0 2px rgba(255,255,255,.90),
                    0 9px 30px rgba(93,98,119,.20),
                    inset 0 0 25px rgba(255,255,255,.94) !important;
                animation: vault-v45-silver-inner-light 8s ease-in-out infinite;
            }
            /* Coroa de luz rotativa contornando somente o artefato. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::before {
                inset: -3px;
                padding: 3px;
                border-radius: 23px;
                opacity: 1;
                background: conic-gradient(from var(--vault-v41-metal-angle),
                    #8e939d 0deg,#d8dbe2 30deg,#fff 67deg,
                    #f6f8fc 86deg,#aeb2bc 105deg,#fff 155deg,
                    #959aa5 217deg,#f4f5f9 270deg,#a9aeb7 330deg,#8e939d 360deg);
                filter: drop-shadow(0 0 4px rgba(238,241,252,.80));
                animation: vault-v41-metal-sun-pass 5.6s linear infinite;
            }
            /* Camada prateada interna, bem atrás das informações. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after {
                content: '';
                display: block;
                position: absolute;
                inset: 0;
                z-index: 0;
                border-radius: 18px;
                pointer-events: none;
                background:
                    radial-gradient(ellipse 65% 95% at 72% 24%,rgba(255,255,255,.97),transparent 75%),
                    linear-gradient(112deg,transparent 30%,rgba(255,255,255,.38) 48%,transparent 66%) 100% 0 / 240% 100% no-repeat;
                opacity: .45;
                filter: none;
                animation: vault-v45-silver-veil 7.5s ease-in-out infinite;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait {
                z-index: 1;
                background:
                    radial-gradient(circle at 44% 35%,#fff 2%,#eeeff1 36%,#aeb3bd 78%,#d9dbe0 100%) !important;
                border-right: 1px solid rgba(138,145,159,.66) !important;
                box-shadow: inset 0 0 28px rgba(255,255,255,.85),inset 0 0 0 1px rgba(255,255,255,.72) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait::before {
                border-color: rgba(255,255,255,.94) !important;
                box-shadow: 0 0 0 1px rgba(139,145,157,.23),0 0 18px rgba(255,255,255,.72);
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-portrait::after {
                opacity: .46;
                animation-duration: 6.5s;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .master-vault-v10-identity,
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] > .master-vault-v10-section {
                position: relative;
                z-index: 1;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .master-vault-v10-name {
                color: #393d49 !important;
                text-shadow: 0 1px 0 rgba(255,255,255,.96),0 0 15px rgba(255,255,255,.80);
                font-size: 18px !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .master-vault-v10-nature {
                color: #656976 !important;
                font-weight: 750;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .master-vault-v10-description {
                color: #5a5962 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] > .master-vault-v10-section {
                border-top-color: rgba(135,143,158,.23) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-features-section {
                background: linear-gradient(120deg,rgba(242,244,248,.82),rgba(255,255,255,.80)) !important;
                border: 1px solid rgba(185,188,200,.34) !important;
                border-radius: 10px;
                padding: 5px 8px !important;
                margin-bottom: 7px !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v36-nature-icon--mundane {
                background-color: #545966 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]:hover {
                box-shadow: 0 0 0 2px rgba(255,255,255,.98),
                    0 15px 37px rgba(99,105,122,.27),
                    0 0 23px rgba(219,224,235,.80),
                    inset 0 0 30px rgba(255,255,255,.98) !important;
                border-color: #989da9 !important;
            }
            @keyframes vault-v45-silver-inner-light {
                0%,100% { background-position: 0 0,0 0,0 0; }
                50% { background-position: 8px -5px,-10px 6px,0 0; }
            }
            @keyframes vault-v45-silver-veil {
                0%,18% {opacity:.22;background-position:0 0,115% 0;}
                42% {opacity:.62;}
                70%,100% {opacity:.35;background-position:0 0,-115% 0;}
            }

            /* A janela do Mestre reflete a raridade, sem aplicar cores mágicas. */
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="legendary"] {
                --v38-modal-edge:#a4a9af;
                --v38-modal-tint:#eff0f2;
                --v38-modal-ink:#545962;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] {
                --v38-modal-edge:#999fac;
                --v38-modal-tint:#f0f1f5;
                --v38-modal-ink:#414653;
                background:linear-gradient(115deg,#f8f9fb,#fff,#eff0f4) !important;
                border:2px solid #b5b9c3 !important;
                box-shadow:0 28px 76px rgba(35,25,17,.3),
                    0 0 0 3px rgba(251,252,255,.94),
                    0 0 36px rgba(227,229,241,.76) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body {
                background:radial-gradient(ellipse at 87% 12%,rgba(255,255,255,.91),transparent 58%),
                    linear-gradient(125deg,#fff,#f0f0f2) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v16-preview-image {
                background:radial-gradient(ellipse at 46% 32%,#fff,#dee2e9) !important;
                border-color:#aeb2bd !important;
                box-shadow:0 0 0 3px rgba(255,255,255,.94),0 0 15px rgba(185,189,209,.42);
            }

            @media (prefers-reduced-motion: reduce) {
                .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging),
                .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging):hover,
                .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging):hover .vault-v35-portrait img {
                    transition: none !important;
                    transform: none !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"],
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after,
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::before {
                    animation: none !important;
                }
            }

            /* ===============================================================
               SPELLBOUND V66.1.46 — REFLEXO INTERNO + MENU METÁLICO
               Aplica-se exclusivamente a Mundanos Lendários/Artefatos.
               Mantém o reflexo da borda e não interfere com o menu aberto.
               =============================================================== */

            /* Botão de ações integrado à paleta de cada raridade. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v35-menu-trigger {
                border: 1px solid rgba(140,148,158,.48) !important;
                background: linear-gradient(145deg,rgba(255,255,255,.92),rgba(224,228,233,.91)) !important;
                color: #59616c !important;
                box-shadow: 0 2px 6px rgba(74,82,94,.10), inset 0 1px 0 rgba(255,255,255,.96) !important;
                transition: color .20s ease, background .20s ease, border-color .20s ease, box-shadow .20s ease, transform .20s ease !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"]) {
                border-color: rgba(109,120,132,.7) !important;
                background: linear-gradient(145deg,#f8fafc,#cfd5db) !important;
                color: #353c45 !important;
                box-shadow: 0 3px 10px rgba(75,83,96,.16), inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-menu-trigger {
                border: 1px solid rgba(156,164,179,.60) !important;
                background: linear-gradient(140deg,#fff,rgba(230,233,239,.96) 64%,rgba(213,219,230,.94)) !important;
                color: #4e5767 !important;
                box-shadow: 0 2px 9px rgba(97,105,123,.15),inset 0 1px 0 #fff !important;
                transition: color .20s ease, background .20s ease, border-color .20s ease, box-shadow .20s ease, transform .20s ease !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"]) {
                border-color: #929caa !important;
                background: linear-gradient(140deg,#fff,#dce2eb 65%,#cdd4df) !important;
                color: #303847 !important;
                box-shadow: 0 4px 12px rgba(91,100,122,.23), 0 0 8px rgba(255,255,255,.70),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-menu-trigger:focus-visible {
                outline: 2px solid #808d9d;
                outline-offset: 2px;
            }

            /* Reflexo no corpo (não apenas na imagem ou na borda).
               A camada fica atrás do texto e não captura nenhum clique. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"]::after {
                content: '';
                position: absolute;
                display: block;
                inset: 1px;
                z-index: 0;
                border-radius: 17px;
                pointer-events: none;
                background:
                    radial-gradient(ellipse 70% 95% at 81% 16%,rgba(255,255,255,.62),transparent 73%),
                    linear-gradient(112deg,transparent 36%,rgba(255,255,255,.45) 49%,transparent 62%) 115% 0 / 240% 100% no-repeat;
                opacity: .20;
                animation: vault-v46-soft-interior-glint 13s ease-in-out infinite;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after {
                /* Luz interna prateada visível em todo o card, lembrando um
                   brilho de limpeza cruzando a superfície inteira. */
                inset: 2px;
                border-radius: 16px;
                background:
                    radial-gradient(ellipse 72% 108% at 52% 48%,rgba(255,255,255,.28),transparent 72%),
                    radial-gradient(ellipse 52% 72% at 78% 22%,rgba(255,255,255,.40),transparent 70%),
                    linear-gradient(108deg,
                        transparent 0%,
                        transparent 34%,
                        rgba(255,255,255,.10) 41%,
                        rgba(255,255,255,.58) 48%,
                        rgba(255,255,255,.92) 50%,
                        rgba(255,255,255,.58) 52%,
                        rgba(255,255,255,.10) 59%,
                        transparent 66%,
                        transparent 100%) 132% 0 / 230% 100% no-repeat;
                opacity: .44;
                filter: saturate(1.04);
                animation: vault-v61-silver-artifact-glint 6.6s ease-in-out infinite;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) > .master-vault-v10-identity,
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) > .master-vault-v10-section,
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) > .vault-v35-portrait {
                position: relative;
                z-index: 1;
            }
            @keyframes vault-v46-soft-interior-glint {
                0%, 21% { background-position: 0 0, 115% 0; opacity: .13; }
                37% { opacity: .30; }
                55% { background-position: 0 0, -115% 0; opacity: .23; }
                66%, 100% { background-position: 0 0, -115% 0; opacity: .13; }
            }
            @keyframes vault-v61-silver-artifact-glint {
                0%, 18% {
                    background-position: 0 0,0 0,132% 0;
                    opacity: .24;
                }
                30% {
                    background-position: 0 0,0 0,74% 0;
                    opacity: .40;
                }
                46% {
                    background-position: 0 0,0 0,18% 0;
                    opacity: .56;
                }
                58% {
                    background-position: 0 0,0 0,-18% 0;
                    opacity: .48;
                }
                76% {
                    background-position: 0 0,0 0,-92% 0;
                    opacity: .28;
                }
                100% {
                    background-position: 0 0,0 0,-132% 0;
                    opacity: .24;
                }
            }
            @media (prefers-reduced-motion: reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::after {
                    animation: none !important;
                    opacity: .18 !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after {
                    opacity: .26 !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-menu-trigger {
                    transition: none !important;
                }
            }

            /* ===============================================================
               SPELLBOUND V66.1.47 — TECNOLOGIA / NITIDEZ DO HOVER
               Aço eletrônico e ciano para Lendários; núcleo noturno para
               Artefatos. Circuitos angulares em vez de um fundo quadriculado.
               Somente CSS: preserva menu, Alpine, Tiptap e dados existentes.
               =============================================================== */

            /* A escala e a rotação fracionárias reamostravam a miniatura,
               parecendo um filtro opaco. Mantemos o movimento apenas no card. */
            .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging):hover {
                transform:translateY(-3px) !important;
            }
            .master-vault-page .master-vault-v10-card .vault-v35-portrait img,
            .master-vault-page .master-vault-v10-card:hover .vault-v35-portrait img,
            .master-vault-page .master-vault-v10-card:focus-within .vault-v35-portrait img {
                transform:none !important;
                opacity:1 !important;
                filter:drop-shadow(0 5px 7px rgba(34,43,50,.18)) !important;
                transition:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"] .vault-v35-portrait img,
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:hover .vault-v35-portrait img {
                position:relative;
                z-index:3;
                transform:none !important;
                opacity:1 !important;
                filter:drop-shadow(0 6px 9px rgba(8,25,42,.27)) !important;
            }


            /* A regra original do hover tem :not() adicionais e maior
               especificidade. Neutralizamos a escala no mesmo nível. */
            .master-vault-page .master-vault-v10-card:not(.is-moving):not(.is-dragging):hover .vault-v35-portrait img {
                transform:none !important;
                opacity:1 !important;
            }

            @property --vault-v47-tech-angle {
                syntax:'<angle>';
                inherits:false;
                initial-value:0deg;
            }
            @keyframes vault-v47-tech-border {
                to { --vault-v47-tech-angle:360deg; }
            }
            @keyframes vault-v47-tech-sparks {
                0%,100% { background-position:10% 110%,92% 115%,70% 105%;opacity:.16; }
                48% { opacity:.48; }
                70% { background-position:10% 12%,92% 21%,70% 5%;opacity:.34; }
            }
            @keyframes vault-v47-tech-pulse {
                0%,100% { opacity:.26; background-position:0 0,110% 0; }
                45% { opacity:.54; background-position:0 0,0% 0; }
                72% { opacity:.38; background-position:0 0,-110% 0; }
            }

            /* A base cria uma hierarquia coerente sem afetar os Mágicos/Mundanos. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                position:relative;
                isolation:isolate;
                overflow:visible !important;
                --vault-v47-trace:#51b3ce;
                --vault-v47-light:#c9ffff;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                content:'';
                display:block !important;
                position:absolute;
                z-index:5;
                inset:-2px;
                padding:2px;
                border:0;
                border-radius:20px;
                pointer-events:none;
                background:conic-gradient(from var(--vault-v47-tech-angle),
                    var(--vault-v47-trace) 0deg,
                    transparent 52deg,
                    var(--vault-v47-trace) 75deg,
                    var(--vault-v47-light) 87deg,
                    var(--vault-v47-trace) 99deg,
                    transparent 117deg,
                    var(--vault-v47-trace) 215deg,
                    var(--vault-v47-light) 257deg,
                    var(--vault-v47-trace) 285deg,
                    var(--vault-v47-trace) 360deg);
                -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                -webkit-mask-composite:xor;
                mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                mask-composite:exclude;
                animation:vault-v47-tech-border 10s linear infinite;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::after {
                content:'';
                display:block;
                position:absolute;
                z-index:0;
                inset:1px;
                border-radius:17px;
                pointer-events:none;
                opacity:.28;
                background:
                    radial-gradient(ellipse 55% 65% at 90% 24%,rgba(159,233,246,.35),transparent 77%),
                    linear-gradient(110deg,transparent 40%,rgba(127,230,243,.15) 50%,transparent 61%) 110% 0/250% 100% no-repeat;
                animation:vault-v47-tech-pulse 11s ease-in-out infinite;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v10-identity,
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) > .master-vault-v10-section,
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait {
                position:relative;
                z-index:1;
            }

            /* Lendário: metal grafite-azulado claro, circuitos em ciano. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] {
                --v38-edge:#4888a3;
                --v38-pale:#eaf5fa;
                --v38-ink:#27536a;
                --vault-v47-trace:#4f91aa;
                --vault-v47-light:#ecffff;
                border-color:#699cb3 !important;
                background:linear-gradient(119deg,#edf8fb 0%,#fbfeff 42%,#dcebf2 100%) !important;
                box-shadow:0 5px 16px rgba(36,93,118,.14),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"]:is(:hover,:focus-within) {
                border-color:#3d89a7 !important;
                box-shadow:0 13px 30px rgba(25,104,138,.20),0 0 0 1px rgba(183,246,255,.55) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .master-vault-v10-name {
                color:#244f67 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .master-vault-v10-nature,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .master-vault-v10-category {
                color:#607f91 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v34-card-rich-excerpt {
                color:#466577 !important;
            }

            /* Artefato: a própria superfície vira um núcleo cibernético único. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] {
                --v38-edge:#40c7dc;
                --v38-pale:#11263f;
                --v38-ink:#b6f5ff;
                --vault-v47-trace:#20b6d6;
                --vault-v47-light:#fbffff;
                border:2px solid #309cbf !important;
                border-radius:20px !important;
                background:
                    radial-gradient(ellipse 74% 94% at 95% 12%,rgba(30,143,185,.36),transparent 75%),
                    radial-gradient(ellipse 65% 85% at 12% 100%,rgba(93,71,182,.18),transparent 75%),
                    linear-gradient(124deg,#152b42 0%,#0d182e 53%,#1c3150 100%) !important;
                box-shadow:0 0 0 1px rgba(125,217,241,.35),0 8px 26px rgba(6,28,61,.22),inset 0 0 18px rgba(59,160,204,.10) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::before {
                inset:-3px;
                padding:3px;
                border-radius:23px;
                animation-duration:6.8s;
                filter:drop-shadow(0 0 4px rgba(71,216,255,.39));
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {
                opacity:.48;
                background:
                    radial-gradient(ellipse 60% 100% at 90% 22%,rgba(55,191,235,.28),transparent 77%),
                    linear-gradient(110deg,transparent 37%,rgba(127,245,255,.18) 50%,transparent 63%) 110% 0/240% 100% no-repeat;
                animation-duration:8s;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]:is(:hover,:focus-within) {
                border-color:#62deed !important;
                box-shadow:0 0 0 1px rgba(130,232,255,.73),0 14px 32px rgba(6,36,73,.38),0 0 22px rgba(70,197,233,.24),inset 0 0 27px rgba(54,162,219,.13) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-name {
                color:#e9fcff !important;
                text-shadow:0 0 12px rgba(71,197,234,.16);
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-nature,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-category {
                color:#a1c4d5 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v34-card-rich-excerpt {
                color:#c6dce7 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] > .master-vault-v10-section {
                border-top-color:rgba(130,222,245,.20) !important;
            }

            /* Miniatura: traços de circuito angulares, sem grade de quadrados.
               O circuito e as fagulhas ficam ATRÁS da foto para não opacá-la. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 50% 41%,#daeff4 2%,#77a9bb 68%,#386079 100%) !important;
                border-right:1px solid #72a6ba !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 49% 39%,#3c7a9b 0%,#183e65 54%,#0b1a35 100%) !important;
                border-right:1px solid #2faccd !important;
                box-shadow:inset 0 0 23px rgba(20,181,224,.24) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait::before {
                z-index:0 !important;
                inset:10px !important;
                border-radius:13px !important;
                border:1px solid rgba(154,239,254,.26) !important;
                background:
                    linear-gradient(90deg,rgba(148,237,249,.42),rgba(148,237,249,.42)) 0 22%/42% 1px no-repeat,
                    linear-gradient(135deg,transparent 49%,rgba(148,237,249,.44) 49.5% 50.5%,transparent 51%) 54% 22%/48px 45px no-repeat,
                    linear-gradient(90deg,rgba(148,237,249,.36),rgba(148,237,249,.36)) 100% 75%/42% 1px no-repeat,
                    linear-gradient(135deg,transparent 49%,rgba(148,237,249,.36) 49.5% 50.5%,transparent 51%) 75% 67%/46px 37px no-repeat,
                    radial-gradient(circle,#7ce7f8 0 2px,transparent 2.7px) 16% 20%/11px 11px no-repeat,
                    radial-gradient(circle,#7ce7f8 0 2px,transparent 2.7px) 85% 77%/11px 11px no-repeat !important;
                box-shadow:inset 0 0 16px rgba(77,196,221,.13);
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait::after {
                z-index:1 !important;
                inset:0 !important;
                border-radius:inherit !important;
                pointer-events:none;
                background:
                    radial-gradient(circle,rgba(164,251,255,.92) 0 1.3px,transparent 2px) 10% 110%/60px 80px no-repeat,
                    radial-gradient(circle,rgba(180,229,255,.78) 0 1px,transparent 1.8px) 92% 115%/49px 88px no-repeat,
                    radial-gradient(circle,rgba(146,253,255,.70) 0 1.4px,transparent 2.3px) 70% 105%/71px 100px no-repeat !important;
                opacity:.25 !important;
                animation:vault-v47-tech-sparks 9s ease-in-out infinite !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {
                animation-duration:6.8s !important;
            }
            /* Ícone da tecnologia ganha 2 px, mantendo o formato flutuante. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"] .vault-v43-nature-tag .vault-v36-nature-icon--technology {
                width:23px !important;
                height:23px !important;
                flex-basis:23px !important;
                background-color:#367b98 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v43-nature-tag .vault-v36-nature-icon--technology {
                background-color:#98f3ff !important;
                filter:drop-shadow(0 0 5px rgba(93,226,244,.38));
            }

            /* Controles e habilidades acompanham a raridade, sem caixa bege. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-menu-trigger {
                border-radius:50%;
                transition:color .2s ease,border-color .2s ease,box-shadow .2s ease,background .2s ease !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-menu-trigger {
                color:#356c86 !important;
                border:1px solid rgba(81,146,173,.44) !important;
                background:linear-gradient(145deg,#fff,#d6edf6) !important;
                box-shadow:0 2px 7px rgba(39,107,135,.11),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"]) {
                color:#163d57 !important;
                border-color:#5a9fba !important;
                background:linear-gradient(140deg,#f8feff,#b6dde9) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger {
                color:#b6faff !important;
                border:1px solid rgba(86,201,225,.48) !important;
                background:linear-gradient(145deg,#25445e,#102439) !important;
                box-shadow:0 2px 9px rgba(0,0,0,.21),inset 0 1px 0 rgba(181,246,255,.11) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"]) {
                color:#fff !important;
                border-color:#7befff !important;
                background:linear-gradient(145deg,#32627d,#163850) !important;
                box-shadow:0 0 12px rgba(59,211,240,.34) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v10-abilities {
                min-height:28px;
                gap:7px !important;
                padding:6px 0 0 !important;
                border:0 !important;
                border-radius:0 !important;
                border-top:1px solid rgba(52,150,178,.25) !important;
                background:transparent !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v10-ability {
                min-height:20px !important;
                border-radius:6px !important;
                padding:3px 7px !important;
                font-size:9px !important;
                background:rgba(225,249,253,.64) !important;
                color:#376c83 !important;
                border:1px solid rgba(78,156,177,.32) !important;
                box-shadow:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-abilities {
                border-top-color:rgba(110,220,246,.26) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-ability {
                background:rgba(31,79,103,.50) !important;
                color:#c4f6ff !important;
                border:1px solid rgba(103,210,237,.35) !important;
            }
            /* Menu suspenso segue a mesma paleta e preserva a ação de excluir. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .vault-v35-action-menu {
                background:#f3fbfe !important;
                border-color:#a4cfdf !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu {
                background:#152a40 !important;
                border-color:#488eac !important;
                box-shadow:0 18px 38px rgba(6,18,39,.38) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu > button:not(.is-danger) {
                color:#c8eaf5 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu > button:is(:hover,:focus-visible) {
                background:rgba(77,159,195,.16) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu > button.is-danger {
                color:#ffabae !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-move-list {
                background:#1d3a50 !important;
                border-color:#3c7291 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-move-list button {
                color:#c4e8f4 !important;
            }

            /* O modal teleportado recebe a distinção entre patamares. */
            .master-vault-v9-preview[data-nature="technological"][data-rarity="legendary"] {
                --v38-modal-edge:#639bb4;
                --v38-modal-tint:#deeff5;
                --v38-modal-ink:#30647a;
            }
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] {
                --v38-modal-edge:#289cc4;
                --v38-modal-tint:#d0edf5;
                --v38-modal-ink:#276985;
                border:2px solid #319dbd !important;
                box-shadow:0 0 0 2px rgba(182,242,255,.50),0 25px 67px rgba(7,30,59,.36),0 0 28px rgba(78,177,218,.27) !important;
            }

            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]),
                .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::after,
                .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v35-portrait::after {
                    animation:none !important;
                }
            }

            /* ================================================================
               SPELLBOUND V66.1.48 — TECNOLÓGICOS NA MESMA LINGUAGEM DOS MUNDANOS
               Lendário: acabamento delicado; Artefato: evolução luminosa do
               MESMO card. Retira o filete lateral do aro animado da V66.1.47.
               ================================================================ */

            /* O conic-gradient anterior criava uma barra ciano na lateral.
               A borda passa a ser desenhada pelo próprio card, como nos Mundanos. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                content:none !important;
                display:none !important;
                animation:none !important;
            }

            /* Mantém o Lendário já aprovado, mas com contorno uniforme e
               brilho suave, sem traços verticais fora da moldura. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] {
                border:1px solid #83b2c6 !important;
                box-shadow:0 5px 16px rgba(45,105,133,.11),inset 0 1px 0 rgba(255,255,255,.96) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"]:is(:hover,:focus-within) {
                border-color:#5a9db9 !important;
                box-shadow:0 12px 26px rgba(36,109,144,.17),0 0 0 1px rgba(211,251,255,.64),inset 0 1px 0 #fff !important;
            }

            /* Artefato: mesma base azul-perolada do Lendário, porém com
               um núcleo ciano interno. Nada de card preto/azul-noturno. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] {
                --v38-edge:#4aadc7;
                --v38-pale:#e5f8ff;
                --v38-ink:#245e77;
                --vault-v47-trace:#56bfd7;
                --vault-v47-light:#ecffff;
                border:2px solid #69bbd1 !important;
                border-radius:20px !important;
                background:
                    radial-gradient(ellipse 75% 112% at 15% 7%,rgba(234,253,255,.91),transparent 76%),
                    radial-gradient(ellipse 85% 115% at 100% 93%,rgba(155,216,240,.56),transparent 73%),
                    linear-gradient(115deg,#fbfeff 0%,#edf9fd 44%,#d6edf7 100%) !important;
                box-shadow:0 0 0 1px rgba(150,220,235,.49),0 9px 26px rgba(47,130,170,.19),inset 0 1px 0 #fff,inset 0 0 22px rgba(90,194,226,.13) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]:is(:hover,:focus-within) {
                border-color:#46aac9 !important;
                box-shadow:0 0 0 1px rgba(188,246,255,.82),0 14px 30px rgba(42,126,164,.25),0 0 17px rgba(113,211,235,.17),inset 0 0 26px rgba(117,209,232,.19) !important;
            }
            /* Reflexo interno, bem atrás do conteúdo, sem esconder a foto. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {
                z-index:0 !important;
                opacity:.25;
                background:
                    radial-gradient(ellipse 67% 93% at 88% 12%,rgba(135,225,249,.51),transparent 80%),
                    linear-gradient(112deg,transparent 37%,rgba(255,255,255,.75) 49%,transparent 63%) 115% 0/250% 100% no-repeat !important;
                animation:vault-v46-soft-interior-glint 10s ease-in-out infinite !important;
            }
            /* Cores editoriais escuras e legíveis, como nos Mundanos. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-name {
                color:#204e64 !important;
                text-shadow:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-nature,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-category {
                color:#5c7c8d !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v34-card-rich-excerpt {
                color:#436878 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] > .master-vault-v10-section {
                border-top-color:rgba(81,159,190,.21) !important;
            }
            /* Fotografia clara e nítida; circuitos finos e partículas da
               V66.1.47 permanecem, porém sem fundo escuro no Artefato. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 51% 37%,#e8fbff 0%,#acd6e8 57%,#659eb7 100%) !important;
                border-right:1px solid #85c1d7 !important;
                box-shadow:inset 0 0 20px rgba(94,195,223,.16) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::before {
                border-color:rgba(218,249,255,.65) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v43-nature-tag .vault-v36-nature-icon--technology {
                background-color:#318aa8 !important;
                filter:drop-shadow(0 1px 3px rgba(228,255,255,.95)) !important;
            }
            /* Botão e menu continuam como elementos do mesmo metal azulado;
               nenhuma peça azul-marinho isolada do resto do card. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger {
                color:#337892 !important;
                border:1px solid rgba(80,164,192,.51) !important;
                background:linear-gradient(145deg,#fff,#d7f0f9) !important;
                box-shadow:0 2px 8px rgba(55,133,165,.13),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"]) {
                color:#18526b !important;
                border-color:#5bb4cf !important;
                background:linear-gradient(145deg,#fbffff,#bde3ef) !important;
                box-shadow:0 3px 11px rgba(61,156,190,.20) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu {
                background:#f3fbff !important;
                border-color:#8bc4d7 !important;
                box-shadow:0 14px 32px rgba(36,94,119,.19) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu > button:not(.is-danger),
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-move-list button {
                color:#315f77 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu > button:is(:hover,:focus-visible) {
                background:rgba(87,186,215,.13) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-action-menu > button.is-danger {
                color:#b54848 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-move-list {
                background:#f0faff !important;
                border-color:#a1cadb !important;
            }
            /* Etiquetas simples, sem fundo bege ou pílula neon isolada. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-abilities {
                border-top-color:rgba(77,171,197,.21) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-ability {
                color:#367b94 !important;
                background:rgba(235,251,255,.80) !important;
                border-color:rgba(89,173,200,.37) !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {
                    animation:none !important;
                    opacity:.16 !important;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.49 — ACABAMENTO TECNOLÓGICO / NÚCLEO VIVO
               Mantém a linguagem azul clara da V66.1.48. O Artefato ganha
               presença na superfície, reflexo INTERNO e rastro NA BORDA;
               não há barra vertical ou filtro aplicado sobre a fotografia.
               ================================================================ */

            @property --vault-v49-tech-trail-angle {
                syntax:'<angle>';
                inherits:false;
                initial-value:0deg;
            }
            @keyframes vault-v49-tech-trail {
                to { --vault-v49-tech-trail-angle:360deg; }
            }
            @keyframes vault-v49-tech-core {
                0%,100% {background-position:6% 8%,95% 96%,0% 50%,center,center;}
                50% {background-position:96% 68%,7% 17%,100% 50%,center,center;}
            }
            @keyframes vault-v49-tech-inside {
                0%,100% {opacity:.46; background-position:9% 10%,0% 50%,12% 82%;}
                45% {opacity:.77; background-position:88% 65%,100% 50%,86% 22%;}
                70% {opacity:.60; background-position:72% 19%,60% 50%,55% 66%;}
            }

            /* Lendário: a faixa das habilidades pertence ao mesmo material
               azul acetinado do card. Nada do fundo bege do estilo geral. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] {
                background:
                    radial-gradient(ellipse 75% 100% at 94% 93%,rgba(169,224,241,.34),transparent 77%),
                    linear-gradient(116deg,#f2fbfd 0%,#fcfeff 44%,#dceef6 100%) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] > .vault-v35-features-section {
                padding:6px 8px !important;
                margin-bottom:10px !important;
                border:1px solid rgba(100,170,197,.29) !important;
                border-radius:10px !important;
                background:linear-gradient(115deg,rgba(207,236,247,.69),rgba(233,250,254,.70)) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.69) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .master-vault-v10-abilities {
                min-height:23px !important;
                padding:0 !important;
                border:0 !important;
                border-radius:0 !important;
                background:transparent !important;
                gap:5px !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="legendary"] .master-vault-v10-ability {
                color:#326c88 !important;
                background:rgba(247,254,255,.82) !important;
                border-color:rgba(92,164,190,.31) !important;
                box-shadow:none !important;
            }

            /* Artefato: a composição inteira pulsa, sem escurecer o card.
               Gradientes coloridos são parte do próprio fundo, não um filtro
               que passe por cima da imagem e reduza sua nitidez. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] {
                --v38-edge:#39a7c5;
                --v38-pale:#d9f5ff;
                --v38-ink:#205771;
                --vault-v49-tech-trail-angle:0deg;
                border:2px solid #51b8d3 !important;
                background:
                    radial-gradient(ellipse 70% 115% at 14% 16%,rgba(112,215,242,.61),transparent 73%),
                    radial-gradient(ellipse 82% 115% at 85% 84%,rgba(91,178,224,.53),transparent 74%),
                    linear-gradient(109deg,rgba(225,251,255,.76),rgba(255,255,255,.36) 49%,rgba(156,222,246,.65)),
                    radial-gradient(ellipse 110% 145% at 65% 12%,#f8feff 10%,#d5effa 67%,#a9d9ee 100%),
                    linear-gradient(120deg,#e9faff,#c5eaf6) !important;
                background-size:145% 145%,150% 150%,210% 100%,100% 100%,100% 100% !important;
                background-position:6% 8%,95% 96%,0% 50%,center,center;
                animation:vault-v49-tech-core 13s ease-in-out infinite;
                box-shadow:
                    0 0 0 1px rgba(231,253,255,.94),
                    0 10px 27px rgba(40,131,173,.22),
                    0 0 19px rgba(100,220,244,.17),
                    inset 0 1px 0 #fff,
                    inset 0 0 24px rgba(56,174,222,.17) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]:is(:hover,:focus-within) {
                border-color:#36aacb !important;
                box-shadow:
                    0 0 0 1px rgba(227,255,255,.98),
                    0 15px 31px rgba(33,123,167,.27),
                    0 0 22px rgba(67,207,235,.22),
                    inset 0 0 32px rgba(82,198,236,.25) !important;
            }

            /* Uma única faísca se desloca PERIMETRALMENTE na borda de 2px.
               A máscara impede qualquer feixe vertical no corpo do card. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::before {
                content:'' !important;
                display:block !important;
                position:absolute !important;
                inset:-1px !important;
                z-index:4 !important;
                padding:2px !important;
                border:0 !important;
                border-radius:20px !important;
                pointer-events:none !important;
                background:conic-gradient(
                    from var(--vault-v49-tech-trail-angle),
                    rgba(40,150,188,.30) 0deg,
                    rgba(56,177,213,.28) 146deg,
                    rgba(233,255,255,.95) 166deg,
                    #77efff 176deg,
                    rgba(67,198,233,.75) 185deg,
                    rgba(55,154,191,.28) 201deg,
                    rgba(40,150,188,.30) 360deg
                ) !important;
                -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                -webkit-mask-composite:xor !important;
                mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                mask-composite:exclude !important;
                filter:drop-shadow(0 0 3px rgba(82,226,249,.38));
                animation:vault-v49-tech-trail 7.8s linear infinite !important;
            }
            /* Reflexo ciano e pequenos pontos de energia no plano do CARD.
               O retrato e os textos ficam numa camada superior. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {
                content:'' !important;
                display:block !important;
                position:absolute !important;
                z-index:0 !important;
                inset:2px !important;
                opacity:.58 !important;
                border-radius:17px !important;
                pointer-events:none !important;
                background:
                    radial-gradient(ellipse 64% 100% at 15% 25%,rgba(206,252,255,.46),transparent 78%),
                    linear-gradient(108deg,transparent 35%,rgba(255,255,255,.46) 49%,transparent 64%),
                    radial-gradient(circle at 18% 72%,rgba(58,183,221,.30) 0 1.4px,transparent 2.4px) !important;
                background-size:155% 155%,250% 100%,100% 100% !important;
                animation:vault-v49-tech-inside 9.5s ease-in-out infinite !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:radial-gradient(ellipse at 49% 37%,#e5faff 0%,#9fd5e8 57%,#4a91b0 100%) !important;
                border-right:1px solid rgba(57,153,193,.66) !important;
                box-shadow:inset 0 0 19px rgba(73,189,222,.23) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-name {
                color:#1d5771 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v34-card-rich-excerpt {
                color:#365f75 !important;
            }

            /* Habilidades no material azul do Artefato, sem painel bege. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] > .vault-v35-features-section {
                padding:6px 8px !important;
                margin-bottom:10px !important;
                border:1px solid rgba(60,167,200,.36) !important;
                border-radius:10px !important;
                background:linear-gradient(112deg,rgba(122,202,232,.36),rgba(218,247,255,.54)) !important;
                box-shadow:inset 0 1px 0 rgba(247,255,255,.67) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-abilities {
                min-height:23px !important;
                padding:0 !important;
                border:0 !important;
                background:transparent !important;
                gap:5px !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-ability {
                color:#1c6d8e !important;
                background:rgba(241,253,255,.76) !important;
                border:1px solid rgba(64,164,201,.36) !important;
                box-shadow:none !important;
            }

            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"],
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::before,
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {
                    animation:none !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {opacity:.30 !important;}
            }


            /* ================================================================
               SPELLBOUND V66.1.50 — ARTEFATO TECNOLÓGICO, SEM BARRA / SEM AZUL EXCESSIVO
               O LENDÁRIO permanece rigorosamente inalterado.
               Card de artefato em porcelana metálica/prata fria; energia só nos
               acentos. Ondas de trilhas eletrônicas passam ATRÁS da foto.
               ================================================================ */
            @keyframes vault-v50-circuit-waves {
                0% { transform:translate3d(-19%,0,0); }
                50% { transform:translate3d(2%,0,0); }
                100% { transform:translate3d(19%,0,0); }
            }
            @keyframes vault-v50-pearl-light {
                0%,100% { opacity:.30; background-position: 140% 0; }
                50% { opacity:.52; background-position: -40% 0; }
            }
            @keyframes vault-v50-edge-breathe {
                0%,100% { box-shadow:0 5px 21px rgba(48,85,96,.15),0 0 0 1px rgba(255,255,255,.94),inset 0 1px 0 #fff,inset 0 0 22px rgba(255,255,255,.50); }
                50% { box-shadow:0 9px 25px rgba(45,94,112,.20),0 0 0 1px rgba(199,224,229,.90),inset 0 1px 0 #fff,inset 0 0 25px rgba(246,255,255,.78); }
            }

            /* SEM pseudo aro: a barra ciano nasce do ::before com máscara. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::before {
                content:none !important;
                display:none !important;
                background:none !important;
                border:0 !important;
                box-shadow:none !important;
                filter:none !important;
                animation:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] {
                --v38-edge:#85abb4;
                --v38-pale:#f0f3f2;
                --v38-ink:#3c5e69;
                border:1px solid #91b3bd !important;
                border-radius:19px !important;
                background:
                    radial-gradient(ellipse 65% 95% at 91% 14%,rgba(255,255,255,.92),transparent 75%),
                    radial-gradient(ellipse 60% 130% at 16% 95%,rgba(190,209,213,.32),transparent 70%),
                    linear-gradient(120deg,#ffffff 0%,#f7f9f9 47%,#e8eef0 100%) !important;
                background-size:100% 100% !important;
                background-position:center !important;
                animation:vault-v50-edge-breathe 10s ease-in-out infinite !important;
                box-shadow:0 8px 23px rgba(48,85,96,.15),0 0 0 1px rgba(255,255,255,.94),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]:is(:hover,:focus-within) {
                border-color:#6d9eaa !important;
                box-shadow:0 14px 29px rgba(48,85,96,.22),0 0 0 1px rgba(245,255,255,.95),inset 0 1px 0 #fff !important;
            }
            /* Luz de superfície prata/porcelana, nunca uma película azul na foto. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {
                content:'' !important;
                display:block !important;
                position:absolute !important;
                inset:1px !important;
                z-index:0 !important;
                border-radius:18px !important;
                pointer-events:none !important;
                background:linear-gradient(106deg,transparent 37%,rgba(255,255,255,.68) 49%,transparent 61%) 140% 0 / 260% 100% no-repeat !important;
                opacity:.34 !important;
                animation:vault-v50-pearl-light 13s ease-in-out infinite !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                background:linear-gradient(152deg,#c9dde1 0%,#adcbd3 54%,#86aebc 100%) !important;
                border-right:1px solid rgba(85,138,156,.42) !important;
                box-shadow:inset 0 0 16px rgba(247,255,255,.40) !important;
            }
            /* Remove os circuitos PARADOS da V47: apenas a moldura limpa fica. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::before {
                content:'' !important;
                display:block !important;
                background:none !important;
                border:1px solid rgba(238,251,253,.64) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.30) !important;
            }
            /* Circuitos em ondas horizontais, sem grade nem textura quadrada.
               A foto fica acima da animação, na sua resolução e opacidade. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {
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
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait img {
                position:relative !important;
                z-index:2 !important;
                opacity:1 !important;
                transform:none !important;
                filter:drop-shadow(0 7px 8px rgba(35,56,66,.22)) !important;
            }
            /* Materiais frios e tipografia legível, sem saturação azul no painel. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-name {
                color:#344b56 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-nature,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-category {
                color:#697983 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v34-card-rich-excerpt {
                color:#526772 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v43-nature-tag .vault-v36-nature-icon--technology {
                background-color:#3f7183 !important;
                filter:drop-shadow(0 1px 2px rgba(248,255,255,.8)) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger {
                color:#426676 !important;
                border:1px solid rgba(94,143,161,.44) !important;
                background:linear-gradient(150deg,#fff,#e4eff1) !important;
                box-shadow:0 2px 7px rgba(61,110,129,.11),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"]) {
                color:#315365 !important;
                border-color:#7daebd !important;
                background:linear-gradient(150deg,#fff,#d5e9ed) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] > .vault-v35-features-section {
                border-color:rgba(122,161,175,.36) !important;
                background:linear-gradient(115deg,rgba(219,232,236,.83),rgba(243,249,249,.89)) !important;
                box-shadow:inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-ability {
                color:#526e7b !important;
                background:rgba(255,255,255,.87) !important;
                border-color:rgba(124,164,178,.40) !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"],
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after,
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {
                    animation:none !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {opacity:.24 !important;}
            }

            /* ================================================================
               SPELLBOUND V66.1.51 — ARTEFATO TECNOLÓGICO, NÚCLEO EXPANDIDO
               A base do Lendário foi REUTILIZADA sem mudar sua paleta:
               mesmo fundo do card, mesmo gradiente da foto, mesmos circuitos.
               A raridade Artefato acrescenta pulso/partículas ASSÍNCRONOS.
               Não modifica nenhum seletor de Lendário.
               ================================================================ */
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
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] {
                /* Copia EXATA das variáveis e do fundo do Lendário. */
                --v38-edge:#4888a3;
                --v38-pale:#eaf5fa;
                --v38-ink:#27536a;
                --vault-v47-trace:#4f91aa;
                --vault-v47-light:#ecffff;
                border:1px solid #83b2c6 !important;
                border-radius:20px !important;
                background:
                    radial-gradient(ellipse 75% 100% at 94% 93%,rgba(169,224,241,.34),transparent 77%),
                    linear-gradient(116deg,#f2fbfd 0%,#fcfeff 44%,#dceef6 100%) !important;
                background-size:100% 100% !important;
                background-position:center !important;
                animation:vault-v51-artifact-halo 11.7s ease-in-out infinite !important;
                box-shadow:0 5px 16px rgba(45,105,133,.11),inset 0 1px 0 rgba(255,255,255,.96) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]:is(:hover,:focus-within) {
                border-color:#3d89a7 !important;
                box-shadow:0 13px 30px rgba(25,104,138,.20),0 0 0 1px rgba(183,246,255,.55),0 0 15px rgba(106,197,215,.19) !important;
            }
            /* Jamais restaurar o aro conic-gradient: ele causava a barra lateral. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::before {
                content:none !important;display:none !important;animation:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after {
                content:'' !important;display:block !important;
                inset:1px !important;z-index:0 !important;border-radius:18px !important;
                pointer-events:none !important;
                background:
                    radial-gradient(ellipse 62% 85% at 78% 24%,rgba(255,255,255,.50),transparent 73%),
                    linear-gradient(115deg,transparent 36%,rgba(191,236,248,.19) 49%,transparent 62%) 120% 50%/240% 100% no-repeat !important;
                opacity:.12 !important;
                animation:vault-v51-artifact-wash 15s ease-in-out infinite !important;
            }
            /* Reuso do MESMO FUNDO da foto do Lendário, sem uma tonalidade nova. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait {
                position:relative !important;isolation:isolate !important;
                background:radial-gradient(ellipse at 50% 41%,#daeff4 2%,#77a9bb 68%,#386079 100%) !important;
                border-right:1px solid #72a6ba !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.28) !important;
                overflow:hidden !important;
            }
            /* Mesmos 4 segmentos + 2 nós que já existem no Lendário. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::before {
                content:'' !important;display:block !important;
                z-index:0 !important;inset:10px !important;border-radius:13px !important;
                border:1px solid rgba(154,239,254,.26) !important;
                background:
                    linear-gradient(90deg,rgba(148,237,249,.42),rgba(148,237,249,.42)) 0 22%/42% 1px no-repeat,
                    linear-gradient(135deg,transparent 49%,rgba(148,237,249,.44) 49.5% 50.5%,transparent 51%) 54% 22%/48px 45px no-repeat,
                    linear-gradient(90deg,rgba(148,237,249,.36),rgba(148,237,249,.36)) 100% 75%/42% 1px no-repeat,
                    linear-gradient(135deg,transparent 49%,rgba(148,237,249,.36) 49.5% 50.5%,transparent 51%) 75% 67%/46px 37px no-repeat,
                    radial-gradient(circle,#7ce7f8 0 2px,transparent 2.7px) 16% 20%/11px 11px no-repeat,
                    radial-gradient(circle,#7ce7f8 0 2px,transparent 2.7px) 85% 77%/11px 11px no-repeat !important;
                box-shadow:inset 0 0 16px rgba(77,196,221,.13) !important;
            }
            /* Os pontos originais do Lendário permanecem; os extras estão no
               overlay separado, que não altera nem nubla o retrato. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after {
                content:'' !important;display:block !important;
                z-index:1 !important;inset:0 !important;
                border-radius:inherit !important;pointer-events:none !important;
                background:
                    radial-gradient(circle,rgba(164,251,255,.92) 0 1.3px,transparent 2px) 10% 110%/60px 80px no-repeat,
                    radial-gradient(circle,rgba(180,229,255,.78) 0 1px,transparent 1.8px) 92% 115%/49px 88px no-repeat,
                    radial-gradient(circle,rgba(146,253,255,.70) 0 1.4px,transparent 2.3px) 70% 105%/71px 100px no-repeat !important;
                opacity:.25 !important;
                animation:vault-v47-tech-sparks 9s ease-in-out infinite !important;
            }
            /* Aumenta o detalhamento dos MESMOS circuitos com pulso em vários
               sentidos, mantendo uma única camada isolada atrás da foto. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-energy {
                position:absolute;inset:0;z-index:2;display:block;
                overflow:hidden;pointer-events:none;border-radius:inherit;
                isolation:isolate;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-circuits {
                position:absolute;inset:8px 3px;width:calc(100% - 6px);height:calc(100% - 16px);
                overflow:visible;fill:none;stroke:#a5f4fc;stroke-width:1.5;
                stroke-linecap:round;stroke-linejoin:round;
                filter:drop-shadow(0 0 3px rgba(186,255,255,.44));
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
            .master-vault-page .vault-v51-tech-circuits .vault-v51-trace {
                stroke-dasharray:18 142;stroke-dashoffset:155;
                animation:vault-v51-trace-forward 7.2s linear infinite;
            }
            .master-vault-page .vault-v51-tech-circuits .vault-v51-trace:nth-child(2) {
                stroke-dasharray:12 126;animation:vault-v51-trace-reverse 9.1s linear -3.4s infinite;
            }
            .master-vault-page .vault-v51-tech-circuits .vault-v51-trace:nth-child(3) {
                stroke-dasharray:15 102;animation:vault-v51-trace-branch 6.7s ease-in-out -1.1s infinite;
            }
            .master-vault-page .vault-v51-tech-circuits .vault-v51-trace:nth-child(4) {
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
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark {
                --size:3px;position:absolute;
                width:var(--size);height:var(--size);border-radius:100%;
                background:#d0fbff;box-shadow:0 0 5px #baf9ff,0 0 12px rgba(110,225,245,.62);
                left:var(--x);top:var(--y);
                will-change:transform,opacity;
                animation:var(--motion) var(--duration) ease-in-out var(--delay) infinite;
            }
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(1){--x:15%;--y:22%;--size:3px;--motion:vault-v51-orbit-a;--duration:7.7s;--delay:-1.4s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(2){--x:73%;--y:14%;--size:2px;--motion:vault-v51-orbit-b;--duration:11.2s;--delay:-6.1s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(3){--x:34%;--y:75%;--size:4px;--motion:vault-v51-orbit-c;--duration:8.5s;--delay:-2.9s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(4){--x:84%;--y:56%;--size:2px;--motion:vault-v51-orbit-d;--duration:9.3s;--delay:-4.2s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(5){--x:23%;--y:48%;--size:2px;--motion:vault-v51-orbit-b;--duration:6.9s;--delay:-3.7s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(6){--x:67%;--y:87%;--size:3px;--motion:vault-v51-orbit-a;--duration:10.6s;--delay:-8.2s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(7){--x:47%;--y:11%;--size:2px;--motion:vault-v51-orbit-d;--duration:8.8s;--delay:-5.4s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(8){--x:88%;--y:83%;--size:3px;--motion:vault-v51-orbit-c;--duration:12.4s;--delay:-7.1s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(9){--x:12%;--y:90%;--size:2px;--motion:vault-v51-orbit-b;--duration:9.9s;--delay:-3.0s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(10){--x:78%;--y:37%;--size:3px;--motion:vault-v51-orbit-c;--duration:7.9s;--delay:-6.3s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(11){--x:29%;--y:15%;--size:2px;--motion:vault-v51-orbit-d;--duration:13.1s;--delay:-2.5s;}
            .master-vault-page .vault-v51-tech-energy .vault-v51-spark:nth-of-type(12){--x:55%;--y:65%;--size:3px;--motion:vault-v51-orbit-a;--duration:10.1s;--delay:-4.7s;}
            /* Exatamente a paleta textual do Lendário, sem tons cinzentos extras. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-name {color:#244f67 !important;text-shadow:none !important;}
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-nature,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-category {color:#607f91 !important;}
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v34-card-rich-excerpt {color:#466577 !important;}
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v43-nature-tag .vault-v36-nature-icon--technology {background-color:#367b98 !important;filter:drop-shadow(0 1px 2px rgba(255,255,255,.60)) !important;}
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] > .vault-v35-features-section {
                border:1px solid rgba(100,170,197,.29) !important;
                background:linear-gradient(115deg,rgba(207,236,247,.69),rgba(233,250,254,.70)) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.69) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .master-vault-v10-ability {
                color:#326c88 !important;
                background:rgba(247,254,255,.82) !important;
                border-color:rgba(92,164,190,.31) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger {
                color:#326c88 !important;
                border:1px solid rgba(104,169,195,.40) !important;
                background:linear-gradient(150deg,#fff,#e2f4fa) !important;
                box-shadow:0 2px 6px rgba(38,104,135,.12),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"]) {
                color:#244f67 !important;border-color:#75acc4 !important;
                background:linear-gradient(150deg,#fff,#d1edf6) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait img,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]:hover .vault-v35-portrait img {
                position:relative !important;z-index:3 !important;
                opacity:1 !important;transform:none !important;
                filter:drop-shadow(0 6px 9px rgba(8,25,42,.27)) !important;
            }
            /* Ícone/circuitos/partículas não devem cobrir a foto nem o menu. */
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v43-nature-tag {z-index:6 !important;}
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] > .master-vault-v10-card-main,
            .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] > .vault-v35-features-section {position:relative;z-index:1;}
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"],
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::after,
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v35-portrait::after,
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-circuits .vault-v51-trace,
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-energy .vault-v51-spark {
                    animation:none !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-circuits .vault-v51-trace {opacity:.22 !important;stroke-dasharray:none !important;}
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"] .vault-v51-tech-energy .vault-v51-spark {opacity:.20 !important;transform:none !important;}
            }


            /* ================================================================
               SPELLBOUND V66.1.52 — FIO DE LUZ NAS BORDAS TECNOLÓGICAS
               Mesmo princípio do reflexo metálico mundano: a máscara mostra
               SOMENTE a borda. Nenhuma barra à esquerda ou película na foto.
               Não altera base, circuitos ou partículas aprovados na V66.1.51.
               ================================================================ */
            @property --vault-v52-edge-angle {
                syntax:'<angle>';
                inherits:false;
                initial-value:0deg;
            }
            @keyframes vault-v52-edge-orbit {
                to { --vault-v52-edge-angle:360deg; }
            }
            /* A película mascarada tem um segmento luminoso CURTO que gira
               pelo perímetro, em vez de pintar uma das laterais de ciano. */
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                    content:'' !important;
                    display:block !important;
                    visibility:visible !important;
                    position:absolute !important;
                    inset:-1.5px !important;
                    width:auto !important;
                    height:auto !important;
                    box-sizing:border-box !important;
                    z-index:8 !important;
                    padding:1.5px !important;
                    border:0 !important;
                    border-radius:20px !important;
                    background:conic-gradient(
                        from var(--vault-v52-edge-angle),
                        transparent 0deg 306deg,
                        rgba(139,209,227,.08) 315deg,
                        rgba(188,235,245,.42) 325deg,
                        rgba(255,255,255,.95) 335deg,
                        rgba(155,219,237,.63) 342deg,
                        transparent 355deg 360deg
                    ) !important;
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    -webkit-mask-composite:xor !important;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0) !important;
                    mask-composite:exclude !important;
                    filter:none !important;
                    box-shadow:none !important;
                    opacity:.78 !important;
                    pointer-events:none !important;
                    animation:vault-v52-edge-orbit 14s linear infinite !important;
                }
                /* Artefato: o MESMO rastro, com faixa e halo mais presentes. */
                .master-vault-page .master-vault-v10-card[data-nature="technological"][data-rarity="artifact"]::before {
                    inset:-2px !important;
                    padding:2.2px !important;
                    border-radius:21px !important;
                    background:conic-gradient(
                        from var(--vault-v52-edge-angle),
                        transparent 0deg 298deg,
                        rgba(100,193,223,.06) 306deg,
                        rgba(100,202,230,.30) 314deg,
                        rgba(153,234,252,.87) 325deg,
                        #faffff 335deg,
                        rgba(125,223,247,.96) 343deg,
                        rgba(84,194,229,.34) 351deg,
                        transparent 359deg 360deg
                    ) !important;
                    filter:drop-shadow(0 0 2.5px rgba(96,207,238,.62)) !important;
                    opacity:1 !important;
                    animation:vault-v52-edge-orbit 8.7s linear infinite !important;
                }
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                    animation:none !important;
                    --vault-v52-edge-angle:0deg;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.53 — VISUALIZAÇÃO TECNOLÓGICA DO MESTRE
               O modal é teleportado diretamente para <body>; não usar o
               ancestral .master-vault-page. Mantém a base aprovada do Lendário
               no Artefato e acrescenta apenas luz/circuitos, sem barra lateral.
               Exclusivo a Tecnológicos Lendários/Artefatos.
               ================================================================ */
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                --vault-v53-edge:#76a9c1;
                --vault-v53-deep:#26566d;
                --vault-v53-ink:#42677c;
                --vault-v53-soft:#dceff6;
                isolation:isolate;
                border:1px solid var(--vault-v53-edge) !important;
                background:linear-gradient(119deg,#edf8fb 0%,#fbfeff 42%,#dcebf2 100%) !important;
                box-shadow:0 28px 76px rgba(23,53,70,.33),inset 0 1px 0 #fff !important;
            }
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] {
                /* Mesma cor/base do Lendário, acabamento de patamar superior. */
                border-color:#67a2bc !important;
                box-shadow:0 27px 78px rgba(24,59,78,.37),0 0 0 2px rgba(142,215,236,.18),0 0 35px rgba(116,199,221,.20),inset 0 1px 0 #fff !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-head,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-foot {
                position:relative;
                z-index:1;
                border-color:rgba(100,157,181,.42) !important;
                background:linear-gradient(115deg,rgba(205,233,244,.87),rgba(249,253,255,.93)) !important;
            }
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v9-preview-head,
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v9-preview-foot {
                background:linear-gradient(111deg,rgba(185,222,235,.90),rgba(239,250,253,.95),rgba(207,235,244,.88)) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-body {
                position:relative;
                z-index:1;
                background:
                    radial-gradient(ellipse 48% 66% at 92% 4%,rgba(172,222,239,.20),transparent 80%),
                    linear-gradient(119deg,rgba(255,255,255,.64),rgba(233,246,250,.42));
            }
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v9-preview-body {
                background:
                    radial-gradient(ellipse 66% 74% at 91% 8%,rgba(173,225,239,.37),transparent 78%),
                    radial-gradient(ellipse 45% 53% at 6% 85%,rgba(209,237,248,.25),transparent 83%),
                    linear-gradient(119deg,rgba(255,255,255,.73),rgba(229,247,251,.60));
                background-size:100% 100%;
                animation:vault-v53-artifact-glow 13s ease-in-out infinite;
            }
            @keyframes vault-v53-artifact-glow {
                0%,100% {background-position:0 0,0 0,0 0;}
                50% {background-position:22px -12px,-20px 11px,0 0;}
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-kicker,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-position {
                color:#43758b !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-nav,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-close {
                color:#4a7890 !important;
                transition:background .18s ease,color .18s ease,transform .18s ease;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.master-vault-v9-preview-nav:not(:disabled),.master-vault-v9-preview-close):hover {
                color:#245671 !important;
                background:rgba(117,185,207,.17) !important;
            }
            /* Foto do modal: exatamente a família cromática da miniatura.
               Camadas decorativas atrás do PNG: jamais reduzir a nitidez. */
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image {
                position:relative !important;
                isolation:isolate !important;
                overflow:hidden !important;
                border:1px solid rgba(101,161,185,.76) !important;
                background:radial-gradient(ellipse at 50% 41%,#daeff4 2%,#77a9bb 68%,#386079 100%) !important;
                box-shadow:inset 0 0 0 1px rgba(255,255,255,.31),0 4px 15px rgba(31,92,119,.12) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image::before {
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
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image::after {
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
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v16-preview-image::after {
                opacity:.68;
                animation-duration:6.6s;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image > img {
                position:relative !important;
                z-index:3 !important;
                opacity:1 !important;
                transform:none !important;
                filter:drop-shadow(0 5px 7px rgba(11,40,57,.22)) !important;
                object-fit:contain !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image > svg {
                position:relative;z-index:3;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-name {
                color:#26576f !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-nature,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-category {
                color:#4e7489 !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v23-inline-description {
                border-top-color:rgba(112,171,191,.40) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v23-inline-description .vault-v30-rich-text,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-inline-rules {
                color:#466879 !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-inline-rules > strong,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-hover-link {
                color:#34657f !important;
            }
            /* Habilidades não são mais painéis de pergaminho bege: base clara
               azul translúcida, na mesma paleta do card do Cofre. */
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-section-title,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-section-title > span {
                color:#3f6d84 !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-section-title > i {
                background:linear-gradient(90deg,rgba(100,170,196,.55),transparent) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature {
                border:1px solid rgba(105,161,187,.45) !important;
                border-radius:13px !important;
                background:linear-gradient(125deg,rgba(247,253,255,.95),rgba(217,240,248,.70)) !important;
                box-shadow:0 3px 12px rgba(33,98,126,.055),inset 0 1px 0 rgba(255,255,255,.85) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature-head {
                border-bottom:1px solid rgba(118,177,200,.34) !important;
                background:linear-gradient(100deg,rgba(206,234,244,.55),rgba(244,252,255,.65)) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature h4 {
                color:#28576e !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature > .vault-v30-rich-text,
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature > p {
                color:#3e6173 !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-usage {
                border:1px solid rgba(99,159,183,.34) !important;
                background:rgba(210,235,244,.76) !important;
                color:#356c84 !important;
            }
            /* Também harmoniza os outros blocos mecânicos do mesmo item. */
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-rule {
                border-color:rgba(112,171,191,.43) !important;
                background:rgba(231,245,250,.62) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-edit {
                border:1px solid #437a94 !important;
                background:linear-gradient(135deg,#376b86,#24566d) !important;
                color:#f7fdff !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-edit:hover {
                background:linear-gradient(135deg,#285c76,#1d475b) !important;
            }
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-dismiss {
                border-color:rgba(106,162,186,.48) !important;
                color:#366981 !important;
                background:rgba(251,255,255,.84) !important;
            }
            /* Artefato: mesma base e texturas do Lendário, com trilha de luz
               muito mais visível ao redor de TODO o perímetro. Sem barra fixa. */
            @property --vault-v53-preview-angle {
                syntax:'<angle>';
                inherits:false;
                initial-value:0deg;
            }
            @keyframes vault-v53-preview-orbit {
                to {--vault-v53-preview-angle:360deg;}
            }
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"]::before {
                    content:'';
                    position:absolute;
                    inset:0;
                    z-index:9;
                    padding:2px;
                    border-radius:inherit;
                    pointer-events:none;
                    background:conic-gradient(from var(--vault-v53-preview-angle),transparent 0deg 302deg,rgba(115,203,229,.14) 314deg,rgba(157,239,252,.78) 329deg,#fff 338deg,rgba(131,221,243,.81) 348deg,transparent 360deg);
                    -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                    -webkit-mask-composite:xor;
                    mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                    mask-composite:exclude;
                    animation:vault-v53-preview-orbit 10.5s linear infinite;
                }
            }
            @media (max-width:720px) {
                .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image {
                    width:132px !important;height:132px !important;
                }
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image::after,
                .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v9-preview-body,
                .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"]::before {
                    animation:none !important;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.54 — ENERGIA E BORDA VIVAS NO MODAL
               O modal está teleportado ao body. O Lendário compartilha a
               linguagem do card; Artefato acrescenta pulsos e mais partículas.
               Camadas sempre atrás da foto e sem bloquear controles.
               ================================================================ */
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                position:relative !important;
                isolation:isolate;
                --vault-v54-edge-angle:0deg;
            }
            /* Reflexo curto ao redor de TODO o perímetro, não um filete lateral. */
            @property --vault-v54-edge-angle {
                syntax:'<angle>';
                inherits:false;
                initial-value:0deg;
            }
            @keyframes vault-v54-edge-orbit {
                to { --vault-v54-edge-angle:360deg; }
            }
            @supports ((-webkit-mask-composite:xor) or (mask-composite:exclude)) {
                .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
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
                .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"]::before {
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
            .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image {
                isolation:isolate !important;
            }
            /* Circuitos e partículas são elementos reais; os pseudo-elementos
               de fundo da V66.1.53 ficam preservados embaixo. */
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-energy {
                display:block;
                position:absolute;
                inset:0;
                z-index:2;
                border-radius:inherit;
                overflow:hidden;
                pointer-events:none;
            }
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-circuits {
                position:absolute;
                inset:6px 3px;
                width:calc(100% - 6px);
                height:calc(100% - 12px);
                fill:none;
                stroke:#b1f6ff;
                stroke-width:1.35px;
                stroke-linecap:round;
                stroke-linejoin:round;
                filter:drop-shadow(0 0 2px rgba(136,232,250,.40));
            }
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-trace {
                opacity:.32;
                stroke-dasharray:18 142;
                stroke-dashoffset:155;
                animation:vault-v51-trace-forward 9.5s linear -1.3s infinite;
            }
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-trace:nth-child(2) {
                stroke-dasharray:12 126;
                animation:vault-v51-trace-reverse 11.1s linear -3.4s infinite;
            }
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-trace:nth-child(3) {
                stroke-dasharray:15 102;
                animation:vault-v51-trace-branch 8.7s ease-in-out -1.1s infinite;
            }
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-trace:nth-child(4) {
                stroke-dasharray:9 111;
                animation:vault-v51-trace-reverse 12.3s linear -4.9s infinite;
            }
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace {
                stroke:#bafaff;
                stroke-width:1.75px;
                filter:drop-shadow(0 0 3px rgba(110,244,255,.62));
                animation-duration:6.8s;
            }
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace:nth-child(2) {animation-duration:8.9s;}
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace:nth-child(3) {animation-duration:6.5s;}
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .vault-v54-preview-trace:nth-child(4) {animation-duration:10.1s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark {
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
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(1){--v54-x:15%;--v54-y:22%;--v54-size:3px;--v54-motion:vault-v51-orbit-a;--v54-duration:7.7s;--v54-delay:-1.4s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(2){--v54-x:73%;--v54-y:14%;--v54-size:2px;--v54-motion:vault-v51-orbit-b;--v54-duration:11.2s;--v54-delay:-6.1s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(3){--v54-x:34%;--v54-y:75%;--v54-size:3px;--v54-motion:vault-v51-orbit-c;--v54-duration:8.5s;--v54-delay:-2.9s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(4){--v54-x:84%;--v54-y:56%;--v54-size:2px;--v54-motion:vault-v51-orbit-d;--v54-duration:9.3s;--v54-delay:-4.2s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(5){--v54-x:23%;--v54-y:48%;--v54-size:2px;--v54-motion:vault-v51-orbit-b;--v54-duration:6.9s;--v54-delay:-3.7s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(6){--v54-x:67%;--v54-y:87%;--v54-size:3px;--v54-motion:vault-v51-orbit-a;--v54-duration:10.6s;--v54-delay:-8.2s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(7){--v54-x:47%;--v54-y:11%;--v54-size:2px;--v54-motion:vault-v51-orbit-d;--v54-duration:8.8s;--v54-delay:-5.4s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(8){--v54-x:88%;--v54-y:83%;--v54-size:3px;--v54-motion:vault-v51-orbit-c;--v54-duration:12.4s;--v54-delay:-7.1s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(9){--v54-x:12%;--v54-y:90%;--v54-size:2px;--v54-motion:vault-v51-orbit-b;--v54-duration:9.9s;--v54-delay:-3.0s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(10){--v54-x:78%;--v54-y:37%;--v54-size:3px;--v54-motion:vault-v51-orbit-c;--v54-duration:7.9s;--v54-delay:-6.3s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(11){--v54-x:29%;--v54-y:15%;--v54-size:2px;--v54-motion:vault-v51-orbit-d;--v54-duration:13.1s;--v54-delay:-2.5s;}
            .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark:nth-of-type(12){--v54-x:55%;--v54-y:65%;--v54-size:3px;--v54-motion:vault-v51-orbit-a;--v54-duration:10.1s;--v54-delay:-4.7s;}
            /* Patamar Lendário: atividade contida; Artefato: 12 partículas. */
            .master-vault-v9-preview[data-nature="technological"][data-rarity="legendary"] .vault-v54-preview-spark:nth-of-type(n+6) {display:none;}
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v16-preview-image {
                box-shadow:inset 0 0 0 1px rgba(255,255,255,.42),0 4px 18px rgba(31,92,119,.19),0 0 0 1px rgba(108,207,231,.28) !important;
                animation:vault-v54-photo-aura 7.6s ease-in-out infinite;
            }
            @keyframes vault-v54-photo-aura {
                0%,100% {box-shadow:inset 0 0 0 1px rgba(255,255,255,.42),0 4px 18px rgba(31,92,119,.19),0 0 0 1px rgba(108,207,231,.28);}
                50% {box-shadow:inset 0 0 0 1px rgba(255,255,255,.52),0 5px 18px rgba(31,92,119,.21),0 0 0 2px rgba(129,218,244,.49),0 0 20px rgba(94,213,244,.19);}
            }
            /* O antigo background animado estava com tamanho 100%, logo sua
               posição quase não variava. Dá espaço para o reflexo se mover. */
            .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v9-preview-body {
                background-size:125% 135%,120% 125%,100% 100% !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-v9-preview[data-nature="technological"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-trace,
                .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark,
                .master-vault-v9-preview[data-nature="technological"][data-rarity="artifact"] .master-vault-v16-preview-image {
                    animation:none !important;
                }
                .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-spark {opacity:.24 !important;transform:none !important;}
                .master-vault-v9-preview[data-nature="technological"] .vault-v54-preview-trace {opacity:.23 !important;stroke-dashoffset:0 !important;}
            }

            /* ===============================================================
               SPELLBOUND V66.1.55 — HABILIDADES DOS MUNDANOS ESPECIAIS
               As habilidades seguem o material do card: aço para Lendário,
               prata luminosa para Artefato. Não afeta Mágicos/Tecnológicos.
               =============================================================== */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) > .vault-v35-features-section {
                box-sizing:border-box;
                padding:6px 8px !important;
                margin:0 13px 11px 15px !important;
                border-radius:10px !important;
                border-top:1px solid rgba(143,151,162,.32) !important;
                border-right:1px solid rgba(152,159,168,.25) !important;
                border-bottom:1px solid rgba(152,159,168,.27) !important;
                border-left:1px solid rgba(152,159,168,.25) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.80) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"] > .vault-v35-features-section {
                background:linear-gradient(105deg,rgba(216,221,227,.78),rgba(239,241,243,.91) 58%,rgba(225,229,233,.72)) !important;
                border-color:rgba(149,157,166,.34) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] > .vault-v35-features-section {
                background:linear-gradient(110deg,rgba(221,225,233,.90),rgba(252,253,255,.92) 44%,rgba(211,218,228,.83)) !important;
                border-color:rgba(164,174,190,.47) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.98),0 1px 6px rgba(104,116,137,.07) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v10-abilities {
                min-height:22px;
                display:flex;
                flex-wrap:wrap;
                gap:5px !important;
                padding:0 !important;
                border:0 !important;
                border-radius:0 !important;
                background:transparent !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v10-ability {
                min-height:21px !important;
                padding:3px 8px !important;
                border-radius:7px !important;
                background:rgba(252,253,255,.87) !important;
                border:1px solid rgba(149,158,170,.42) !important;
                color:#4f5965 !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.92) !important;
                font-size:9px !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] .master-vault-v10-ability {
                background:linear-gradient(125deg,rgba(255,255,255,.99),rgba(235,240,247,.96)) !important;
                color:#495261 !important;
                border-color:rgba(163,173,190,.55) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v10-ability-more {
                background:rgba(209,216,225,.80) !important;
                color:#4c5968 !important;
            }

            /* ===============================================================
               SPELLBOUND V66.1.56 — VISUALIZAÇÃO MUNDANA METÁLICA
               Aço neutro no Lendário; prata viva no Artefato. O modal
               teleportado usa o mesmo material da biblioteca, incluindo
               todas as áreas internas. Não altera Mágicos/Tecnológicos.
               =============================================================== */
            @property --vault-v56-silver-angle {
                syntax:'<angle>';
                initial-value:0deg;
                inherits:false;
            }
            @keyframes vault-v56-silver-orbit {
                to {--vault-v56-silver-angle:360deg;}
            }
            @keyframes vault-v56-silver-breathe {
                0%,100% {background-position:0% 40%,50% 40%,0% 0%;}
                50% {background-position:100% 60%,45% 55%,0% 0%;}
            }
            @keyframes vault-v56-photo-sheen {
                0%,20% {background-position:145% center;opacity:0;}
                38% {opacity:.46;}
                62%,100% {background-position:-145% center;opacity:0;}
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                --vault-v56-edge:#a5acb4;
                --vault-v56-ink:#484f59;
                --vault-v56-muted:#6d747e;
                --vault-v56-rule:rgba(150,159,170,.40);
                --vault-v56-feature:linear-gradient(128deg,#f9fafb 4%,#e8eaed 100%);
                --vault-v56-feature-head:linear-gradient(110deg,#dce0e5,#f2f3f4);
                position:relative;
                isolation:isolate;
                border:1px solid var(--vault-v56-edge) !important;
                background:linear-gradient(124deg,#f5f6f8,#f9fafb 49%,#e7e9ec) !important;
                box-shadow:0 24px 65px rgba(43,48,57,.25),0 0 0 2px rgba(246,248,251,.55),inset 0 1px 0 #fff !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] {
                --vault-v56-edge:#989fab;
                --vault-v56-ink:#39414d;
                --vault-v56-muted:#606a79;
                --vault-v56-rule:rgba(143,154,172,.49);
                --vault-v56-feature:linear-gradient(115deg,#fefeff,#e7ebf1 58%,#f8faff);
                --vault-v56-feature-head:linear-gradient(105deg,#d4d9e3,#f8faff 57%,#e4e8f0);
                border:2px solid #adb4c1 !important;
                background:linear-gradient(120deg,#e3e7ed 0%,#fff 37%,#eff2f6 72%,#dfe5ed 100%) !important;
                box-shadow:0 28px 72px rgba(51,59,75,.30),0 0 0 3px rgba(250,252,255,.83),0 0 31px rgba(209,216,230,.55),inset 0 1px 0 #fff !important;
            }
            /* Feixe curto apenas na linha do contorno, nunca uma barra lateral. */
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before {
                content:'';
                position:absolute;
                display:block;
                pointer-events:none;
                z-index:6;
                inset:-2px;
                padding:2px;
                border-radius:inherit;
                background:conic-gradient(from var(--vault-v56-silver-angle),transparent 0deg 270deg,rgba(222,229,237,.12) 296deg,rgba(255,255,255,.94) 319deg,rgba(182,193,209,.55) 337deg,transparent 360deg);
                -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                -webkit-mask-composite:xor;
                mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                mask-composite:exclude;
                animation:vault-v56-silver-orbit 14s linear infinite;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"]::before {
                inset:-3px;
                padding:2.5px;
                background:conic-gradient(from var(--vault-v56-silver-angle),transparent 0deg 256deg,rgba(225,234,245,.24) 277deg,#fff 299deg,#d7e4f6 319deg,rgba(255,255,255,.95) 334deg,transparent 354deg);
                filter:drop-shadow(0 0 3px rgba(242,247,255,.70));
                animation-duration:7.2s;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.master-vault-v9-preview-head,.master-vault-v9-preview-foot) {
                position:relative;
                z-index:1;
                background:linear-gradient(110deg,#dfe2e6,#f5f6f8 68%,#e9ecef) !important;
                border-color:var(--vault-v56-rule) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] :is(.master-vault-v9-preview-head,.master-vault-v9-preview-foot) {
                background:linear-gradient(108deg,#cfd4de 0%,#fdfdff 42%,#e1e6ef 100%) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-body {
                position:relative;
                z-index:1;
                background:linear-gradient(123deg,rgba(255,255,255,.92),rgba(232,235,240,.82)) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body {
                background:
                    radial-gradient(ellipse 58% 110% at 90% 15%,rgba(255,255,255,.92),transparent 77%),
                    radial-gradient(ellipse 67% 110% at 12% 86%,rgba(190,199,214,.36),transparent 79%),
                    linear-gradient(118deg,#f8f9fc,#e7ebf2 60%,#fff) !important;
                background-size:140% 130%,140% 130%,100% 100%;
                animation:vault-v56-silver-breathe 12s ease-in-out infinite;
            }
            /* V66.1.61 — a descrição pertence ao pergaminho prateado do
               Artefato, sem uma segunda caixa com fundo e brilho passando
               atrás das letras. A luz geral do corpo do modal é preservada. */
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .vault-v23-inline-description {
                position:relative;
                overflow:visible;
                border-radius:0;
                background:transparent !important;
                background-image:none !important;
                box-shadow:none !important;
                padding:12px 0 0;
                margin-top:13px;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .vault-v23-inline-description::after {
                content:none !important;
                display:none !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .vault-v23-inline-description > .vault-v30-rich-text {
                position:relative;
                z-index:1;
                padding:0 !important;
                background:transparent !important;
                background-image:none !important;
                color:#47515f !important;
                font-size:12px;
                line-height:1.7;
                font-weight:500;
                text-shadow:none;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .vault-v23-inline-description .vault-v30-rich-text :is(p,div,span) {
                background:transparent !important;
                background-image:none !important;
                box-shadow:none !important;
                color:inherit !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .vault-v23-inline-description .vault-v30-rich-text p {
                font:inherit !important;
                line-height:inherit !important;
                white-space:normal !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.master-vault-v9-preview-kicker,.master-vault-v9-preview-position,.master-vault-v16-nature,.master-vault-v16-category) {
                color:var(--vault-v56-muted) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.master-vault-v16-preview-name,.master-vault-v9-preview-feature h4) {
                color:var(--vault-v56-ink) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.master-vault-v9-preview-nav,.master-vault-v9-preview-close) {
                color:#6b7480 !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.master-vault-v9-preview-nav:not(:disabled),.master-vault-v9-preview-close):hover {
                background:rgba(159,168,180,.17) !important;
                color:#39424d !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image {
                position:relative !important;
                isolation:isolate;
                border:1px solid #aeb7c3 !important;
                background:radial-gradient(circle at 46% 34%,#fcfdff 0%,#e7ebf0 54%,#c6ccd5 100%) !important;
                box-shadow:inset 0 0 0 1px rgba(255,255,255,.90),0 4px 14px rgba(84,93,110,.10) !important;
                overflow:hidden;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v16-preview-image {
                border-color:#9daab9 !important;
                background:radial-gradient(circle at 44% 29%,#fff 0%,#eff3f8 38%,#c1cbd9 100%) !important;
                box-shadow:inset 0 0 0 1px #fff,0 0 0 2px rgba(255,255,255,.78),0 4px 17px rgba(115,128,151,.25) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v16-preview-image::after {
                content:'';
                position:absolute;
                z-index:2;
                inset:0;
                pointer-events:none;
                border-radius:inherit;
                background:linear-gradient(112deg,transparent 36%,rgba(255,255,255,.04) 44%,rgba(255,255,255,.54) 50%,transparent 60%) 145% center/260% 100% no-repeat;
                opacity:0;
                animation:vault-v56-photo-sheen 8.3s ease-in-out infinite;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-preview-image > img {
                position:relative;
                z-index:3;
                opacity:1 !important;
                transform:none !important;
                filter:drop-shadow(0 4px 7px rgba(62,70,85,.17));
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .vault-v23-inline-description {
                border-top-color:var(--vault-v56-rule) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.vault-v23-inline-description .vault-v30-rich-text,.master-vault-v16-inline-rules,.master-vault-v9-preview-feature > .vault-v30-rich-text,.master-vault-v9-preview-feature > p) {
                color:#555c67 !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) :is(.master-vault-v16-inline-rules > strong,.master-vault-v16-hover-link,.master-vault-v16-section-title) {
                color:#586271 !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-section-title > i {
                background:linear-gradient(90deg,var(--vault-v56-rule),transparent) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature {
                border:1px solid var(--vault-v56-rule) !important;
                background:var(--vault-v56-feature) !important;
                box-shadow:0 2px 11px rgba(86,98,115,.055),inset 0 1px 0 rgba(255,255,255,.9) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature-head {
                border-bottom:1px solid var(--vault-v56-rule) !important;
                background:var(--vault-v56-feature-head) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-usage {
                border:1px solid rgba(148,160,177,.4) !important;
                color:#596474 !important;
                background:linear-gradient(110deg,#eef0f4,#dfe4eb) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-rule {
                border-color:var(--vault-v56-rule) !important;
                background:linear-gradient(120deg,#f8f9fb,#e9edf1) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-dismiss {
                border-color:rgba(145,156,170,.48) !important;
                background:linear-gradient(130deg,#fff,#e7ebf0) !important;
                color:#586272 !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-edit {
                border:1px solid #687482 !important;
                background:linear-gradient(125deg,#687482,#4d5866) !important;
                color:#fff !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-edit {
                background:linear-gradient(135deg,#606b7c,#364352) !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])::before,
                .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body,
                .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v16-preview-image::after {
                    animation:none !important;
                }
            }


            /* ===============================================================
               SPELLBOUND V66.1.59 — AJUSTE PONTUAL, SEM AUMENTAR OS CARDS
               Reverte o crescimento indevido da V66.1.58. Prévia compacta
               com reticências; texto completo acessível na visualização e
               no tooltip. Aço/prata sólido nas habilidades, não degradê.
               =============================================================== */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                /* Preserva exatamente a grade/altura da V66.1.57. */
                grid-template-rows:auto minmax(0,1fr) auto !important;
                min-height:211px;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .vault-v34-card-rich-excerpt.master-vault-v10-description {
                display:-webkit-box !important;
                -webkit-box-orient:vertical !important;
                -webkit-line-clamp:3 !important;
                max-height:4.45em !important;
                overflow:hidden !important;
                text-overflow:ellipsis;
                overflow-wrap:anywhere;
            }
            /* Um fundo único até a extremidade: nenhum degradê branco no início. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            > .vault-v35-features-section {
                position:relative;
                z-index:2;
                background-image:none !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.20) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="legendary"]
            > .vault-v35-features-section {
                background:#cfd6df !important;
                border-color:rgba(116,128,144,.44) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]
            > .vault-v35-features-section {
                background:#d6dde6 !important;
                border-color:rgba(128,141,161,.48) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v10-abilities {
                background:transparent !important;
                background-image:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v10-ability {
                background:#e7ebf1 !important;
                background-image:none !important;
                border-color:rgba(121,133,150,.45) !important;
                color:#444d5b !important;
                box-shadow:none !important;
            }
            /* A luz fica atrás do texto e repete com maior frequência. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"] {
                border-width:1.5px !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::before {
                inset:-2px !important;
                padding:2px !important;
                animation-duration:4.7s !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after {
                animation-duration:6s !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]:is(:hover,:focus-within)::after {
                animation-duration:4.5s !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::before,
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after {
                    animation:none !important;
                }
            }


            /* ===============================================================
               SPELLBOUND V66.1.60 — MUNDANOS ESPECIAIS
               Sem caixa na descrição do card; habilidade do modal com
               preenchimento metálico uniforme; aura prateada do Artefato.
               Não altera a altura nem os outros tipos de item.
               =============================================================== */
            /* A área narrativa é parte do mesmo card, não um painel encaixado. */
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v10-identity,
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            > .master-vault-v10-section:not(.vault-v35-features-section) {
                background:transparent !important;
                background-image:none !important;
                border:0 !important;
                border-radius:0 !important;
                box-shadow:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v10-description,
            .master-vault-page .master-vault-v10-card[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v10-description > :is(p,div) {
                background:transparent !important;
                background-image:none !important;
                box-shadow:none !important;
                border:0 !important;
                padding:0 !important;
            }
            /* O corpo da habilidade não deve voltar ao branco/bege do Cofre. */
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v9-preview-feature {
                background:#e5e8ed !important;
                background-image:none !important;
                border-color:rgba(137,149,166,.44) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.36),0 3px 10px rgba(64,74,88,.055) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"]
            .master-vault-v9-preview-feature {
                background:#e0e5ed !important;
                border-color:rgba(129,143,162,.48) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v9-preview-feature-head {
                background:#d8dde5 !important;
                background-image:none !important;
                border-bottom-color:rgba(127,141,160,.34) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"]
            .master-vault-v9-preview-feature-head {
                background:#d2d9e4 !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"])
            .master-vault-v9-preview-feature > :is(.vault-v30-rich-text,p) {
                background:transparent !important;
                background-image:none !important;
                border:0 !important;
                box-shadow:none !important;
                color:#4d5866 !important;
            }
            /* Reflexo prateado pulsante no CARD INTEIRO, não só na borda. */
            @keyframes vault-v60-mundane-silver-aura {
                0%,100% {
                    box-shadow:0 0 0 1px rgba(248,250,255,.32),0 0 10px rgba(194,204,223,.10);
                }
                36% {
                    box-shadow:0 0 0 2px rgba(255,255,255,.93),
                               0 0 18px 5px rgba(222,230,245,.66),
                               0 0 32px 9px rgba(167,179,202,.28);
                }
                59% {
                    box-shadow:0 0 0 1px rgba(245,249,255,.60),
                               0 0 15px 3px rgba(219,226,239,.37);
                }
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after {
                /* Brilho interno visível atravessando TODO o card, junto da aura prateada. */
                animation:vault-v61-silver-artifact-glint 6.2s ease-in-out infinite,
                          vault-v60-mundane-silver-aura 5.4s ease-in-out infinite !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]:is(:hover,:focus-within)::after {
                animation:vault-v61-silver-artifact-glint 4.8s ease-in-out infinite,
                          vault-v60-mundane-silver-aura 3.9s ease-in-out infinite !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]::after,
                .master-vault-page .master-vault-v10-card[data-nature="mundane"][data-rarity="artifact"]:is(:hover,:focus-within)::after {
                    animation:none !important;
                    box-shadow:0 0 0 1px rgba(245,249,255,.50),0 0 12px rgba(204,211,225,.23);
                }
            }


            /* ===============================================================
               SPELLBOUND V66.1.63 — VISUALIZAÇÃO DO MESTRE, MUNDANOS
               Ajusta o título "HABILIDADES" para a paleta metálica correta,
               adiciona brilho interno animado no Artefato também no modal e
               reposiciona levemente as luzes internas para um prata mais nobre.
               =============================================================== */
            @keyframes vault-v63-mundane-preview-sheen {
                0%, 16% {
                    background-position: 0 0, 0 0, 138% 0;
                    opacity: .18;
                }
                30% {
                    background-position: 0 0, 0 0, 92% 0;
                    opacity: .26;
                }
                47% {
                    background-position: 0 0, 0 0, 22% 0;
                    opacity: .42;
                }
                60% {
                    background-position: 0 0, 0 0, -24% 0;
                    opacity: .36;
                }
                82% {
                    background-position: 0 0, 0 0, -96% 0;
                    opacity: .22;
                }
                100% {
                    background-position: 0 0, 0 0, -138% 0;
                    opacity: .18;
                }
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) {
                --vault-v63-section-ink:#707b89;
                --vault-v63-section-line:rgba(157,167,182,.44);
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-section-title > span {
                color:var(--vault-v63-section-ink) !important;
                text-shadow:none !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v16-section-title > i {
                background:linear-gradient(90deg,var(--vault-v63-section-line),rgba(171,180,193,.16) 56%,transparent 100%) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] {
                --vault-v63-section-ink:#647081;
                --vault-v63-section-line:rgba(174,184,199,.52);
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body {
                background:
                    radial-gradient(ellipse 60% 95% at 84% 14%,rgba(255,255,255,.74),transparent 74%),
                    radial-gradient(ellipse 58% 88% at 18% 88%,rgba(195,204,219,.26),transparent 78%),
                    linear-gradient(118deg,#f7f9fc 0%,#e4e9f1 54%,#fbfcfe 100%) !important;
                background-size:138% 124%,136% 124%,100% 100% !important;
                background-position:0 0,0 0,0 0 !important;
                animation:vault-v56-silver-breathe 11.4s ease-in-out infinite;
                overflow:hidden;
                isolation:isolate;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body::after {
                content:'';
                position:absolute;
                inset:0;
                z-index:0;
                pointer-events:none;
                background:
                    radial-gradient(ellipse 42% 70% at 10% 84%,rgba(255,255,255,.16),transparent 74%),
                    radial-gradient(ellipse 36% 62% at 88% 20%,rgba(255,255,255,.19),transparent 70%),
                    linear-gradient(108deg,
                        transparent 0%,
                        transparent 34%,
                        rgba(255,255,255,.03) 41%,
                        rgba(255,255,255,.18) 45%,
                        rgba(255,255,255,.48) 49%,
                        rgba(255,255,255,.82) 50%,
                        rgba(255,255,255,.48) 51%,
                        rgba(255,255,255,.18) 55%,
                        rgba(255,255,255,.03) 59%,
                        transparent 66%,
                        transparent 100%) 138% 0 / 230% 100% no-repeat;
                opacity:.34;
                animation:vault-v63-mundane-preview-sheen 6.4s ease-in-out infinite;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body > * {
                position:relative;
                z-index:1;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature {
                background:linear-gradient(180deg,#e6eaf0 0%,#dde3ea 100%) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"]:is([data-rarity="legendary"],[data-rarity="artifact"]) .master-vault-v9-preview-feature-head {
                background:linear-gradient(180deg,#d5dbe4 0%,#ced5df 100%) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-feature {
                background:linear-gradient(180deg,#e2e8f0 0%,#d9e0ea 100%) !important;
                border-color:rgba(137,149,167,.52) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.42),0 4px 14px rgba(74,84,100,.07) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-feature-head {
                background:linear-gradient(180deg,#d6dde8 0%,#cfd8e4 100%) !important;
                border-bottom-color:rgba(132,145,164,.38) !important;
            }
            .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-feature > :is(.vault-v30-rich-text,p) {
                color:#4b5563 !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body,
                .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body::after {
                    animation:none !important;
                }
                .master-vault-v9-preview[data-nature="mundane"][data-rarity="artifact"] .master-vault-v9-preview-body::after {
                    opacity:.18 !important;
                    background-position:0 0,0 0,0 0 !important;
                }
            }


            /* ===============================================================
               SPELLBOUND V66.1.65 — ARTEFATO MÁGICO = VESTÍGIO VISUAL
               O mesmo conjunto de regras atende Vestígios e Artefatos mágicos.
               O selo Vestígio e suas etapas continuam condicionados pelo
               isVestige(item) no Blade; estilos não alteram essa lógica.
               Lendários nunca entram nesta seleção.
               =============================================================== */
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
            /* O mesmo pergaminho dourado para Vestígios e demais Artefatos. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] {
                position:relative;
                isolation:isolate;
                overflow:visible !important;
                border:1px solid #c79d51 !important;
                background:linear-gradient(120deg,#fffaf0 0%,#fbf0d9 50%,#fff8eb 100%) !important;
                box-shadow:0 0 0 1px rgba(204,164,81,.31),0 8px 24px rgba(139,101,39,.14),0 0 16px rgba(224,178,91,.13) !important;
                transition:transform .22s cubic-bezier(.22,1,.36,1),box-shadow .25s ease,border-color .24s ease !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]:hover {
                border-color:#b98939 !important;
                box-shadow:0 0 0 1px rgba(226,186,107,.55),0 11px 27px rgba(135,98,39,.18),0 0 24px rgba(240,195,111,.23) !important;
            }
            /* Feixe curto que percorre SOMENTE a borda; sem barra lateral fixa.
               V66.1.66: neutraliza width:5px do ::before base. Com width:auto,
               o inset preenche TODO o perímetro e o gradiente pode orbitar. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]::before {
                content:'';
                display:block !important;
                position:absolute;
                inset:-2px;
                width:auto !important;
                height:auto !important;
                z-index:4;
                padding:2px;
                border-radius:inherit;
                pointer-events:none;
                background:conic-gradient(from var(--vestige-angle),transparent 0deg 276deg,
                    rgba(234,190,95,.12) 291deg,rgba(255,241,180,.62) 310deg,
                    #fff9db 324deg,rgba(255,220,123,.95) 335deg,
                    rgba(208,147,53,.56) 351deg,transparent 360deg);
                -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                -webkit-mask-composite:xor;
                mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                mask-composite:exclude;
                animation:vault-vestige-aura 8.5s linear infinite;
            }
            /* Luz atravessando o interior do card e aura externa delicada. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]::after {
                content:'';
                display:block !important;
                position:absolute;
                inset:1px;
                z-index:0;
                border-radius:16px;
                pointer-events:none;
                background:
                    radial-gradient(ellipse 68% 95% at 72% 18%,rgba(255,249,224,.46),transparent 76%),
                    linear-gradient(110deg,transparent 31%,rgba(255,246,209,.06) 41%,
                        rgba(255,236,172,.55) 48%,rgba(255,253,230,.84) 51%,
                        rgba(255,236,172,.38) 55%,transparent 68%) 144% 0 / 236% 100% no-repeat,
                    linear-gradient(120deg,rgba(249,229,183,.22),transparent 72%);
                filter:none !important;
                opacity:.44 !important;
                animation:vault-v65-golden-interior 8.5s ease-in-out infinite !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]:is(:hover,:focus-within)::after {
                animation-duration:6.7s !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > :is(.vault-v35-portrait,.master-vault-v10-identity,.master-vault-v10-section) {
                position:relative;
                z-index:1;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .vault-v35-portrait {
                background:linear-gradient(150deg,#f8edcf,#e8d19d) !important;
                border-right-color:#cfa963 !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v10-name {
                color:#74491f !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > .vault-v35-features-section {
                border-color:rgba(193,150,70,.35) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v10-abilities {
                background:rgba(248,232,197,.58) !important;
                border-color:rgba(194,152,76,.25) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v10-ability {
                background:rgba(255,249,233,.92) !important;
                border-color:rgba(194,154,82,.45) !important;
                color:#7d592c !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .vault-v35-menu-trigger {
                background:linear-gradient(140deg,#fff7e5,#f4e3bf) !important;
                border:1px solid rgba(194,151,74,.51) !important;
                color:#8c5c21 !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.86),0 2px 6px rgba(143,100,38,.11) !important;
                transition:background .2s ease,border-color .2s ease,box-shadow .2s ease,transform .2s ease;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .vault-v35-menu-trigger:is(:hover,[aria-expanded="true"],:focus-visible) {
                background:linear-gradient(145deg,#fff9e9,#e9cd8b) !important;
                border-color:#bd8734 !important;
                color:#72451a !important;
                box-shadow:0 2px 10px rgba(155,103,34,.20),inset 0 1px 0 #fff !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .vault-v35-action-menu {
                background:#fff9ef !important;
                border-color:#dfc18a !important;
            }
            /* Mesma identidade no modal do Mestre, Vestígio ou Artefato. */
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] {
                position:relative;
                isolation:isolate;
                border:1px solid #bd9652 !important;
                background:linear-gradient(155deg,#fffaf0,#faf2de) !important;
                box-shadow:0 30px 85px rgba(31,21,8,.38),0 0 0 1px rgba(231,197,120,.28),0 0 25px rgba(230,183,99,.16) !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"]::before {
                content:'';
                position:absolute;
                z-index:7;
                display:block;
                inset:-2px;
                padding:2px;
                border-radius:inherit;
                pointer-events:none;
                background:conic-gradient(from var(--vestige-angle),transparent 0deg 284deg,rgba(230,185,95,.20) 301deg,#ffefbf 321deg,#fff9df 331deg,rgba(207,160,68,.59) 349deg,transparent 360deg);
                -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                -webkit-mask-composite:xor;
                mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
                mask-composite:exclude;
                animation:vault-vestige-aura 9.3s linear infinite;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body {
                position:relative;
                isolation:isolate;
                overflow:hidden;
                background:linear-gradient(125deg,#fffaf0,#f8edd7 55%,#fff8e9) !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body::after {
                content:'';
                display:block;
                position:absolute;
                inset:0;
                z-index:0;
                pointer-events:none;
                background:
                    radial-gradient(ellipse 65% 90% at 85% 12%,rgba(255,246,210,.24),transparent 76%),
                    linear-gradient(112deg,transparent 30%,rgba(255,246,211,.04) 42%,rgba(255,234,169,.38) 49%,rgba(255,250,222,.63) 52%,transparent 68%) 145% 0/235% 100% no-repeat,
                    linear-gradient(125deg,rgba(244,212,137,.08),transparent 65%);
                opacity:.40;
                animation:vault-v65-golden-interior 10s ease-in-out infinite;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body > * {
                position:relative;
                z-index:1;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] :is(.master-vault-v9-preview-head,.master-vault-v9-preview-foot) {
                background:linear-gradient(100deg,#f1dfb9,#f8edd6) !important;
                border-color:#ddc391 !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v16-preview-image {
                border:1px solid #c8a365 !important;
                background:radial-gradient(circle at 50% 35%,#fff9e9,#f0e1c3) !important;
                box-shadow:0 0 0 3px rgba(217,180,106,.14) !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v16-preview-name {color:#6c461e !important;}
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-feature {
                background:#fff9ed !important;
                border-color:#e2c992 !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-feature-head {
                background:#f8ebd1 !important;
                border-bottom-color:#e3c99b !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]::before,
                .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]::after,
                .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"]::before,
                .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body::after {
                    animation:none !important;
                }
                .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]::after {
                    opacity:.27 !important;
                    box-shadow:none !important;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.67 — HABILIDADES DE ARTEFATOS MÁGICOS / VESTÍGIOS
               Um único acabamento dourado para ambos no card do Cofre.
               - sem barra bege/cinza estranha na base;
               - sem contador de poderes ou etapas no Vestígio;
               - sem alteração dos poderes reais ou da progressão;
               - sem interferir no feixe perimetral aprovado na V66.1.66.
               ================================================================ */

            .master-vault-page
            .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"])
            > .vault-v35-features-section {
                box-sizing:border-box;
                position:relative;
                z-index:2;
                display:flex;
                align-items:center;
                min-height:37px;
                margin:0 13px 11px 15px !important;
                padding:5px 10px !important;
                border:1px solid rgba(193,153,81,.43) !important;
                border-radius:10px !important;
                background:linear-gradient(113deg,rgba(242,222,179,.72) 0%,rgba(255,249,230,.85) 53%,rgba(243,226,186,.67) 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.83),0 1px 4px rgba(136,97,36,.045) !important;
            }

            /* Vestígio: uma única legenda, sem contadores visuais. */
            .master-vault-page .master-vault-v10-card.is-vestige .vault-v35-vestige-summary {
                display:flex !important;
                align-items:center;
                gap:8px;
                flex:1 1 auto;
                min-height:24px !important;
                padding:0 !important;
                margin:0 !important;
                border:0 !important;
                background:none !important;
                box-shadow:none !important;
            }
            .master-vault-page .master-vault-v10-card.is-vestige .vault-v39-vestige-copy {
                display:flex !important;
                align-items:center;
                flex:1 1 auto;
                min-width:0;
                gap:0 !important;
            }
            .master-vault-page .master-vault-v10-card.is-vestige .vault-v35-vestige-summary-icon {
                display:grid;
                place-items:center;
                flex:0 0 23px !important;
                width:23px !important;
                height:23px !important;
                border:1px solid rgba(185,137,59,.28) !important;
                border-radius:50% !important;
                background:rgba(255,250,234,.81) !important;
                color:#b07c2d !important;
                font-size:13px !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.72);
            }
            .master-vault-page .master-vault-v10-card.is-vestige .vault-v35-vestige-summary strong {
                color:#785020 !important;
                font:700 11px/1.3 Georgia,serif !important;
            }
            .master-vault-page .master-vault-v10-card.is-vestige .vault-v35-vestige-summary-arrow {
                margin-left:auto;
                color:#a7752e !important;
                font:700 18px/1 Georgia,serif !important;
            }

            /* Artefato mágico comum: habilidades como pequenas plaquetas de ouro
               integradas à mesma superfície usada pelo Vestígio. */
            .master-vault-page
            .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]:not(.is-vestige)
            .master-vault-v10-abilities {
                display:flex;
                flex:1 1 auto;
                align-items:center;
                flex-wrap:wrap;
                gap:6px !important;
                min-height:24px;
                margin:0 !important;
                padding:0 !important;
                border:0 !important;
                border-radius:0 !important;
                background:none !important;
                box-shadow:none !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]
            .vault-v67-ability-sigil {
                display:grid;
                place-items:center;
                flex:0 0 23px;
                width:23px;
                height:23px;
                border:1px solid rgba(185,137,59,.28);
                border-radius:50%;
                background:rgba(255,250,234,.81);
                color:#b07c2d;
                font:700 13px/1 Georgia,serif;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.72);
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]
            .vault-v67-ability-sigil[x-cloak] {
                display:none !important;
            }
            .master-vault-page
            .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]:not(.is-vestige)
            .master-vault-v10-ability {
                min-height:22px !important;
                padding:3px 9px !important;
                border:1px solid rgba(192,144,61,.40) !important;
                border-radius:7px !important;
                background:rgba(255,251,238,.85) !important;
                color:#7e5523 !important;
                font-size:9px !important;
                font-weight:700 !important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.85) !important;
            }
            .master-vault-page
            .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]:not(.is-vestige)
            .master-vault-v10-ability-more {
                color:#865c23 !important;
                background:rgba(234,209,158,.48) !important;
                border-color:rgba(188,142,63,.35) !important;
            }
            @media (max-width:720px) {
                .master-vault-page
                .master-vault-v10-card:is(.is-vestige,[data-nature="wonderful"][data-rarity="artifact"])
                > .vault-v35-features-section {
                    margin-left:10px !important;
                    margin-right:9px !important;
                    padding-left:8px !important;
                    padding-right:8px !important;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.68 — PREVIEW DE ARTEFATOS + SCROLL DO MESTRE
               - destrava a rolagem do modal de visualização;
               - mantém/realça a borda animada no preview dos Artefatos;
               - remove o sigilo lateral dos Artefatos comuns (fica só no Vestígio).
               ================================================================ */

            /* Algumas versões especiais da prévia acabavam zerando o scroll ao
               trocar overflow-y:auto por overflow:hidden. O modal volta a rolar. */
            .master-vault-v9-preview[data-rarity="artifact"] .master-vault-v9-preview-body,
            .master-vault-v9-preview.vault-v23-preview-vestige .master-vault-v9-preview-body {
                min-height:0 !important;
                overflow-y:auto !important;
                overflow-x:hidden !important;
                -webkit-overflow-scrolling:touch;
                overscroll-behavior:contain;
            }

            /* Garante que o conteúdo real fique acima dos brilhos internos. */
            .master-vault-v9-preview[data-rarity="artifact"] .master-vault-v9-preview-body > * {
                position:relative;
                z-index:1;
            }

            /* O artefato na visualização do Mestre também recebe a moldura viva.
               O movimento acompanha o card, sem virar barra lateral fixa. */
            .master-vault-v9-preview[data-rarity="artifact"] {
                position:relative;
                isolation:isolate;
            }
            .master-vault-v9-preview[data-rarity="artifact"]::before {
                pointer-events:none;
                opacity:1 !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"]::before {
                animation-duration:8.5s !important;
            }

            /* O detalhe com estrela volta a ser exclusivo do resumo do Vestígio. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .vault-v67-ability-sigil {
                display:none !important;
            }


            /* ================================================================
               SPELLBOUND V66.1.69 — HABILIDADES DOS LENDÁRIOS MARAVILHOSOS
               Apenas a área de habilidades dos cards Lendários mágicos:
               ouro acetinado e plaquetas em tom de pergaminho dourado.
               Artefatos, Vestígios, Mundanos e Tecnológicos permanecem iguais.
               ================================================================ */
            .master-vault-page
            .master-vault-v10-card[data-nature="wonderful"][data-rarity="legendary"]
            > .vault-v35-features-section {
                box-sizing:border-box;
                position:relative;
                z-index:2;
                margin:0 13px 11px 15px !important;
                padding:7px 9px !important;
                border:1px solid rgba(190,145,68,.47) !important;
                border-radius:10px !important;
                background:linear-gradient(112deg,
                    rgba(232,204,151,.74) 0%,
                    rgba(248,231,193,.90) 52%,
                    rgba(238,212,161,.79) 100%) !important;
                box-shadow:inset 0 1px 0 rgba(255,252,234,.81),
                           0 2px 7px rgba(138,99,40,.075) !important;
            }
            .master-vault-page
            .master-vault-v10-card[data-nature="wonderful"][data-rarity="legendary"]
            .master-vault-v10-abilities {
                display:flex;
                align-items:center;
                flex-wrap:wrap;
                min-height:22px;
                gap:6px !important;
                padding:0 !important;
                border:0 !important;
                border-radius:0 !important;
                background:transparent !important;
                box-shadow:none !important;
            }
            .master-vault-page
            .master-vault-v10-card[data-nature="wonderful"][data-rarity="legendary"]
            .master-vault-v10-ability {
                min-height:22px !important;
                padding:3px 9px !important;
                border:1px solid rgba(187,139,58,.45) !important;
                border-radius:7px !important;
                background:linear-gradient(135deg,
                    rgba(255,250,228,.97),
                    rgba(250,235,199,.94)) !important;
                color:#805520 !important;
                font-size:9px !important;
                font-weight:750 !important;
                box-shadow:inset 0 1px 0 rgba(255,255,246,.90) !important;
            }
            .master-vault-page
            .master-vault-v10-card[data-nature="wonderful"][data-rarity="legendary"]
            .master-vault-v10-ability-more {
                border-color:rgba(175,124,43,.44) !important;
                background:rgba(218,180,111,.38) !important;
                color:#754919 !important;
            }
            @media (max-width:720px) {
                .master-vault-page
                .master-vault-v10-card[data-nature="wonderful"][data-rarity="legendary"]
                > .vault-v35-features-section {
                    margin-left:10px !important;
                    margin-right:9px !important;
                    padding:6px 8px !important;
                }
            }


            /* ================================================================
               SPELLBOUND V66.1.79 — OURO LÍQUIDO INTERNO, SEM AURA EXTERNA
               Base aprovada 07/11/2026. Desfaz totalmente os overlays V77/V78.
               Artefatos Maravilhosos e Vestígios apenas. Mantém o feixe orbital
               original na moldura; não altera Mundanos/Tecnológicos/Lendários.
               ================================================================ */

            @keyframes vault-v79-liquid-pass {
                0%,9%   {background-position:50% 50%,140% 50%;opacity:.56;}
                43%     {background-position:50% 50%,53% 50%;opacity:.88;}
                76%     {background-position:50% 50%,-22% 50%;opacity:.68;}
                100%    {background-position:50% 50%,-52% 50%;opacity:.55;}
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] {
                /* Sem halo exterior; sombra com a mesma escala dos outros cards. */
                border-color:#c4a16b !important;
                background:linear-gradient(118deg,#fffaf0 0%,#fdf8ed 51%,#f9f0dd 100%) !important;
                box-shadow:0 0 0 1px rgba(193,155,96,.17),0 8px 22px rgba(105,77,38,.12) !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]:hover {
                border-color:#b99256 !important;
                box-shadow:0 0 0 1px rgba(194,150,79,.25),0 13px 27px rgba(105,76,38,.16) !important;
            }
            /* A camada brilhante está dentro dos limites do papel, nunca fora. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > .vault-v79-liquid-gold {
                position:absolute;
                display:block;
                inset:2px 2px 2px 31%;
                z-index:0;
                border-radius:0 16px 16px 0;
                overflow:hidden;
                pointer-events:none;
                background:
                    radial-gradient(ellipse 74% 65% at 51% 53%,
                        rgba(229,176,71,.17) 0%,rgba(245,214,143,.10) 53%,transparent 83%),
                    linear-gradient(105deg,
                        transparent 29%,
                        rgba(225,172,75,.025) 37%,
                        rgba(234,186,91,.13) 43%,
                        rgba(251,225,159,.38) 48%,
                        rgba(255,251,229,.61) 51%,
                        rgba(225,175,77,.22) 57%,
                        transparent 71%) 140% 50% / 225% 100% no-repeat;
                animation:vault-v79-liquid-pass 10.2s ease-in-out infinite;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > .vault-v79-liquid-gold[x-cloak] {display:none !important;}
            /* Conteúdo acima da luz; nenhuma camada intercepta os cliques. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > .master-vault-v10-card-main,
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > .vault-v35-features-section {
                position:relative;
                z-index:1;
            }
            /* Controla o reflexo antigo para evitar duas faixas de luz somadas. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"]::after {
                opacity:.20 !important;
                filter:none !important;
            }
            /* O retrato usa ouro fosco, não um halo de luz por trás do objeto. */
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] .vault-v35-portrait {
                background:linear-gradient(150deg,#fbf3df,#f1e3c2 64%,#e8d29f) !important;
                border-right-color:#c9a66c !important;
            }
            .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > .vault-v35-features-section {
                border-color:rgba(188,150,81,.35) !important;
                background:linear-gradient(112deg,rgba(247,231,198,.68),rgba(255,250,238,.81) 58%,rgba(246,235,212,.72)) !important;
            }

            /* Mesma película acetinada no alto da prévia do Mestre. */
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] {
                border-color:#c2a171 !important;
                background:linear-gradient(155deg,#fffaf0,#fbf5e8) !important;
                box-shadow:0 30px 85px rgba(31,21,8,.35),0 0 0 1px rgba(195,161,105,.17) !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body {
                position:relative;
                isolation:isolate;
                overflow-y:auto !important;
                overflow-x:hidden !important;
                background:
                    radial-gradient(ellipse 72% 88% at 68% 34%, rgba(232,184,85,.10), transparent 74%),
                    radial-gradient(ellipse 62% 92% at 38% 68%, rgba(244,214,150,.08), transparent 78%),
                    linear-gradient(106deg,
                       transparent 26%,
                       rgba(230,177,75,.018) 38%,
                       rgba(246,212,145,.12) 45%,
                       rgba(255,250,222,.27) 50%,
                       rgba(227,179,82,.12) 56%,
                       transparent 69%) var(--vault-v81-liquid-x) 50% / 235% 100% no-repeat,
                    linear-gradient(120deg,#fffaf2,#fcf6e9 60%,#fffaf2) !important;
                /* V66.1.81: anima apenas o reflexo no fundo; a animação
                   v79 também mudava opacity e apagava TODO o conteúdo. */
                opacity:1 !important;
                animation:vault-v81-modal-gold-travel 11.5s ease-in-out infinite !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body > .vault-v79-liquid-gold--preview {
                display:block;
                position:absolute;
                inset:0;
                z-index:0 !important;
                pointer-events:none;
                opacity:.001;
                background:none !important;
                animation:none !important;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body > .vault-v79-liquid-gold--preview[x-cloak] {display:none !important;}
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body > :not(.vault-v79-liquid-gold) {
                position:relative;
                z-index:1;
            }
            .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body::after {
                opacity:.18 !important;
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-page .master-vault-v10-card[data-nature="wonderful"][data-rarity="artifact"] > .vault-v79-liquid-gold,
                .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .vault-v79-liquid-gold--preview {
                    animation:none !important;
                    opacity:.66 !important;
                }
            }


            /* SPELLBOUND V66.1.80 — reflexo dourado do modal expandido para
               todo o fundo da visualização do Mestre (Artefato Maravilhoso/Vestígio). */

            /* ================================================================
               SPELLBOUND V66.1.81 — CONTRASTE RESTAURADO NO MODAL
               Na V66.1.80, vault-v79-liquid-pass alterava opacity do BODY
               rolável (0.55–0.88). A opacidade afetava textos, imagem e
               habilidades. Agora só o gradiente do fundo se movimenta.
               ================================================================ */
            @property --vault-v81-liquid-x {
                syntax:'<percentage>';
                inherits:false;
                initial-value:140%;
            }
            @keyframes vault-v81-modal-gold-travel {
                0%,100% { --vault-v81-liquid-x:140%; }
                50% { --vault-v81-liquid-x:-25%; }
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-v9-preview[data-nature="wonderful"][data-rarity="artifact"] .master-vault-v9-preview-body {
                    animation:none !important;
                    opacity:1 !important;
                }
            }

            /* ================================================================
               SPELLBOUND V66.2.44 — EDITOR COERENTE E COMPACTO
               Somente a modal de criação/edição. Cofre, cards e show intactos.
               ================================================================ */
            .master-vault-v4-modal .vault-v82-editor-head {
                min-height:40px; padding:6px 15px 6px 21px !important;
                display:flex;align-items:center;justify-content:space-between;
                background:linear-gradient(90deg,#f0e7da,#f6efe5);
                border-bottom:1px solid #e3d2be;
            }
            .master-vault-v4-modal .vault-v82-editor-head-rule {
                display:block;width:57px;height:2px;border-radius:4px;
                background:linear-gradient(90deg,rgba(134,67,40,.52),rgba(194,154,97,.22),transparent);
            }
            .master-vault-v4-modal .vault-v82-editor-head .master-vault-v4-close {
                display:grid;place-items:center;width:28px;height:28px;
                padding:0;border-radius:8px;
                font-size:0;line-height:1;color:#89684c;
                background:rgba(255,252,247,.62);
            }
            .master-vault-v4-modal .vault-v82-editor-head .master-vault-v4-close svg {width:15px;height:15px;}
            .master-vault-v4-modal .master-vault-v4-scroll {padding-top:15px;}
            .master-vault-v4-modal .vault-v18-form {gap:11px;}
            .master-vault-v4-modal .vault-v18-card {padding:15px 18px 16px;}
            .master-vault-v4-modal .vault-v18-card-heading {align-items:center;gap:10px;margin:0 0 13px;padding:0 0 11px;}
            .master-vault-v4-modal .vault-v18-step {width:27px;height:27px;border-radius:8px;font-size:10px;}
            .master-vault-v4-modal .vault-v18-card-heading h3 {font-size:17px;line-height:1.2;}
            .master-vault-v4-modal .vault-v18-card-heading p {margin-top:2px;font-size:9px;line-height:1.4;}
            .master-vault-v4-modal .vault-v18-fields--identity {
                grid-template-columns:minmax(0,1.7fr) minmax(170px,.8fr);
                gap:12px;align-items:end;
            }
            .master-vault-v4-modal .vault-v18-fields--identity .master-vault-v4-input,
            .master-vault-v4-modal .vault-v18-fields--identity .master-vault-v4-select {min-height:39px;}
            .master-vault-v4-modal .vault-v82-classification {
                margin-top:15px;padding-top:13px;border-top:1px solid #eadfce;
            }
            .master-vault-v4-modal .vault-v82-classification-main {
                display:grid;grid-template-columns:minmax(0,1fr) 176px;gap:13px;
                align-items:end;
            }
            .master-vault-v4-modal .vault-v82-classification .vault-v18-group {min-width:0;margin:0;}
            .master-vault-v4-modal .vault-v82-classification .master-vault-v4-label {display:block;margin-bottom:6px;}
            .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-options {gap:7px;}
            .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-option {
                min-height:47px;gap:7px;padding:5px 9px;border-radius:10px;
            }
            .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-option .vault-v38-nature-mark,
            .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-option.is-active .vault-v38-nature-mark {
                width:30px;height:30px;flex-basis:30px;border-radius:8px;
            }
            .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-option .vault-v38-nature-mark--magical .vault-v36-nature-icon {
                font-size:23px !important;
            }
            .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-option .vault-v39-nature-image {width:20px;height:20px;}
            .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-option strong {font-size:10px;white-space:nowrap;}
            .master-vault-v4-modal .vault-v82-equippable {width:100%;}
            .master-vault-v4-modal .vault-v82-equippable .vault-v18-yes-no {display:grid;grid-template-columns:1fr 1fr;gap:5px;}
            .master-vault-v4-modal .vault-v82-equippable .vault-v18-yes-no .master-vault-v4-choice {
                min-height:46px;padding:6px;border-radius:9px;font-size:10px;
            }
            .master-vault-v4-modal .vault-v18-classification-fields {
                display:grid;grid-template-columns:minmax(0,1fr) 224px;align-items:end;
                gap:12px;margin-top:14px;
            }
            .master-vault-v4-modal .vault-v82-rarity-field > .master-vault-v4-label {margin-bottom:6px;}
            .master-vault-v4-modal .vault-v82-rarity-list {
                display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:5px;
            }
            .master-vault-v4-modal .vault-v82-rarity-choice {
                --rarity-accent:#b8a18a;
                position:relative;display:flex;min-width:0;align-items:center;justify-content:center;
                gap:5px;min-height:43px;padding:5px 6px;
                border:1px solid #e0d1c0;border-radius:9px;
                background:linear-gradient(180deg,#fffdf9,#f8f2ea);
                color:#83654f;font:750 9.5px/1.25 system-ui,sans-serif;
                cursor:pointer;transition:border-color .16s,background .16s,box-shadow .16s;
            }
            .master-vault-v4-modal .vault-v82-rarity-choice[data-rarity="uncommon"] {--rarity-accent:#6b9677;}
            .master-vault-v4-modal .vault-v82-rarity-choice[data-rarity="rare"] {--rarity-accent:#648eaf;}
            .master-vault-v4-modal .vault-v82-rarity-choice[data-rarity="very_rare"] {--rarity-accent:#9d78b1;}
            .master-vault-v4-modal .vault-v82-rarity-choice[data-rarity="legendary"] {--rarity-accent:#be8844;}
            .master-vault-v4-modal .vault-v82-rarity-choice[data-rarity="artifact"] {--rarity-accent:#c59b42;}
            .master-vault-v4-modal .vault-v82-rarity-dot {display:block;flex:0 0 6px;width:6px;height:6px;border-radius:50%;background:var(--rarity-accent);}
            .master-vault-v4-modal .vault-v82-rarity-choice:hover {background:#fffaf3;border-color:var(--rarity-accent);}
            .master-vault-v4-modal .vault-v82-rarity-choice.is-selected {
                border-color:var(--rarity-accent);
                background:linear-gradient(145deg,#fffdf8,#f4eadd);
                color:#593726;box-shadow:inset 0 0 0 1px color-mix(in srgb,var(--rarity-accent) 35%,transparent);
            }
            .master-vault-v4-modal .vault-v82-rarity-choice.is-selected .vault-v82-rarity-dot {box-shadow:0 0 0 3px color-mix(in srgb,var(--rarity-accent) 16%,transparent);}
            .master-vault-v4-modal .vault-v18-classification-fields .master-vault-v4-toggle-card {
                min-height:43px;padding:7px 10px;border-radius:9px;
            }
            .master-vault-v4-modal .vault-v18-classification-fields .master-vault-v4-toggle-card strong {font-size:10px;}
            .master-vault-v4-modal .vault-v18-classification-fields .master-vault-v4-toggle-card small {font-size:8.6px;}
            .master-vault-v4-modal .vault-v18-vestige {margin-top:12px;padding:10px 13px;border-radius:10px;}
            .master-vault-v4-modal .vault-v82-inventory {
                display:grid;grid-template-columns:180px minmax(0,1fr);align-items:center;gap:18px;
                padding:12px 17px;
            }
            .master-vault-v4-modal .vault-v82-inventory .vault-v18-card-heading {
                margin:0;padding:0;border:0;
            }
            .master-vault-v4-modal .vault-v82-inventory .vault-v18-card-heading h3 {font-size:15px;}
            .master-vault-v4-modal .vault-v82-inventory .vault-v18-card-heading p {font-size:8.5px;}
            .master-vault-v4-modal .vault-v82-inventory .vault-v18-inventory-fields {
                display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:12px;
            }
            .master-vault-v4-modal .vault-v82-inventory .master-vault-v4-input {min-height:39px;}
            .master-vault-v4-modal .vault-v18-card--description {margin-top:0;}
            .master-vault-v4-modal .master-vault-v5-category-shell {margin-top:14px;}
            .master-vault-v4-modal .master-vault-v5-category-head {padding:12px 16px 9px;}
            .master-vault-v4-modal .master-vault-v5-category-head > strong {font-size:18px;}
            .master-vault-v4-modal .master-vault-v5-category-head > p {margin:3px 0 0;font-size:9px;}
            .master-vault-v4-modal .vault-v19-category-grid {gap:7px;}
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category {
                min-height:63px;padding:8px 9px;gap:8px;border-radius:10px;
            }
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category-icon {
                display:grid;place-items:center;width:37px;height:37px;flex:0 0 37px;
                padding:7px;border-radius:9px;
            }
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category-icon svg {width:22px;height:22px;}
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category strong {font-size:11px;}
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category span span {font-size:8.5px;}
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category.is-active:after {top:6px;right:6px;width:14px;height:14px;font-size:9px;}
            .master-vault-v4-modal .vault-v19-abilities {margin-top:14px;}
            .master-vault-v4-modal .master-vault-v5-abilities-head {padding:12px 15px;}
            .master-vault-v4-modal .master-vault-v5-abilities-head-main {gap:9px;}
            .master-vault-v4-modal .master-vault-v5-abilities-mark {width:34px;height:34px;flex-basis:34px;}
            .master-vault-v4-modal .master-vault-v5-abilities-mark svg {width:19px;height:19px;}
            .master-vault-v4-modal .master-vault-v5-abilities-head h3 {font-size:18px;}
            .master-vault-v4-modal .master-vault-v5-abilities-head p {font-size:9px;}
            .master-vault-v4-modal .master-vault-v5-add-ability {min-height:34px;padding:0 11px;font-size:9px;}
            .master-vault-v4-modal .master-vault-v5-abilities-body {padding:10px;gap:8px;}
            .master-vault-v4-modal .master-vault-v5-ability-drawer {border-radius:10px;}
            .master-vault-v4-modal .master-vault-v5-ability-bar {min-height:42px;padding:5px 9px;gap:8px;grid-template-columns:28px minmax(0,1fr) auto;}
            .master-vault-v4-modal .master-vault-v5-ability-number {width:27px;height:27px;font-size:12px;border-radius:7px;}
            .master-vault-v4-modal .master-vault-v5-ability-title strong {font-size:12px;}
            .master-vault-v4-modal .master-vault-v5-ability-title span {font-size:9px;}
            .master-vault-v4-modal .master-vault-v5-ability-tools {gap:1px;}
            .master-vault-v4-modal .master-vault-v5-ability-tool {width:26px;height:26px;padding:4px;}
            .master-vault-v4-modal .master-vault-v5-ability-tool svg {width:15px;height:15px;}
            .master-vault-v4-modal .master-vault-v5-ability-content {padding:11px;}
            .master-vault-v4-modal .master-vault-v5-ability-content-grid {
                grid-template-columns:minmax(0,1fr) minmax(205px,.42fr);gap:11px;align-items:start;
            }
            .master-vault-v4-modal .master-vault-v5-ability-side {
                padding:8px;border-radius:9px;
                background:linear-gradient(145deg,#f9f5ed,#fffaf4);
            }
            .master-vault-v4-modal .vault-v82-tracker-toggle {margin:0;padding:7px 9px;min-height:44px;}
            .master-vault-v4-modal .vault-v82-tracker-toggle strong {font-size:10px;}
            .master-vault-v4-modal .vault-v82-tracker-toggle small {font-size:8.5px;}
            .master-vault-v4-modal .master-vault-v5-ability-side > .mt-3 {margin-top:9px !important;}
            .master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-rich-field {margin-top:9px;}
            .master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-rich-canvas {min-height:85px;}
            .master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-rich-surface,
            .master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-fallback-input {
                min-height:82px !important;
            }
            .master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-rich-foot {padding:4px 8px;}
            .master-vault-v4-modal :is(.vault-v82-rarity-choice,.vault-v18-nature-option,.master-vault-v5-category):focus-visible {outline:2px solid #8b5241;outline-offset:2px;}
            @media(max-width:1240px) {
                .master-vault-v4-modal .vault-v82-classification-main {grid-template-columns:1fr;}
                .master-vault-v4-modal .vault-v18-classification-fields {grid-template-columns:1fr;}
                .master-vault-v4-modal .vault-v82-rarity-list {grid-template-columns:repeat(3,minmax(0,1fr));}
                .master-vault-v4-modal .vault-v82-equippable {max-width:260px;}
            }
            @media(max-width:960px) {
                .master-vault-v4-modal .vault-v82-inventory {grid-template-columns:1fr;gap:12px;}
                .master-vault-v4-modal .vault-v82-inventory .vault-v18-card-heading {padding-bottom:10px;border-bottom:1px solid #eadfce;}
                .master-vault-v4-modal .master-vault-v5-ability-content-grid {grid-template-columns:1fr;}
            }
            @media(max-width:620px) {
                .master-vault-v4-modal .vault-v18-fields--identity {grid-template-columns:1fr;}
                .master-vault-v4-modal .vault-v82-classification .vault-v18-nature-options {grid-template-columns:1fr;}
                .master-vault-v4-modal .vault-v82-rarity-list {grid-template-columns:repeat(2,minmax(0,1fr));}
                .master-vault-v4-modal .vault-v82-inventory .vault-v18-inventory-fields {grid-template-columns:1fr 1fr;}
                .master-vault-v4-modal .vault-v19-category-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
                .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category {min-height:64px;}
            }

            /* ===========================================================
               V66.2.45 — Editor de item: descrição sob fotografia,
               seleção de raridade recolhível e metadados compactos.
               Sem alterações nas animações/cards da biblioteca.
               =========================================================== */
            .master-vault-v4-modal .vault-v18-workspace {
                grid-template-columns:220px minmax(0,1fr);gap:16px 20px;
            }
            .master-vault-v4-modal .vault-v18-sidebar {
                position:relative;top:auto;align-self:start;
            }
            .master-vault-v4-modal .vault-v18-sidebar-card {
                width:220px;max-width:100%;
            }
            .master-vault-v4-modal .vault-v18-photo .master-vault-v4-photo-frame {
                width:100%;aspect-ratio:1;border-radius:17px;
                background:radial-gradient(circle at 45% 35%,#fffaf1,#efe1cb);
            }
            .master-vault-v4-modal .vault-v18-form {gap:10px;}
            .master-vault-v4-modal .vault-v18-fields--identity {display:block;}
            .master-vault-v4-modal .vault-v18-fields--identity .vault-v18-field--name {width:100%;}
            .master-vault-v4-modal .vault-v45-description {
                grid-column:1 / -1;min-width:0;margin-top:0;
            }
            .master-vault-v4-modal .vault-v45-description .vault-v31-rich-canvas {min-height:150px;}
            .master-vault-v4-modal .vault-v45-description .vault-v31-rich-surface,
            .master-vault-v4-modal .vault-v45-description .vault-v31-fallback-input {min-height:144px;}
            .master-vault-v4-modal .vault-v18-card-heading {gap:8px;}
            .master-vault-v4-modal .vault-v18-card-heading h3 {font-size:17px;}
            .master-vault-v4-modal .vault-v82-rarity-field {
                --rarity-accent:#b8a18a;
            }
            .master-vault-v4-modal .vault-v82-rarity-field[data-rarity="uncommon"] {--rarity-accent:#6b9677;}
            .master-vault-v4-modal .vault-v82-rarity-field[data-rarity="rare"] {--rarity-accent:#648eaf;}
            .master-vault-v4-modal .vault-v82-rarity-field[data-rarity="very_rare"] {--rarity-accent:#9d78b1;}
            .master-vault-v4-modal .vault-v82-rarity-field[data-rarity="legendary"] {--rarity-accent:#be8844;}
            .master-vault-v4-modal .vault-v82-rarity-field[data-rarity="artifact"] {--rarity-accent:#c59b42;}
            .master-vault-v4-modal .vault-v45-rarity-picker {position:relative;min-width:0;}
            .master-vault-v4-modal .vault-v45-rarity-trigger {
                display:flex;align-items:center;justify-content:space-between;gap:12px;
                min-height:43px;border:1px solid #ddcbb5;border-radius:10px;
                background:linear-gradient(130deg,#fffefa,#f6efe6);
                padding:8px 12px;color:#603c2c;cursor:pointer;list-style:none;
                transition:border-color .16s,box-shadow .16s;
            }
            .master-vault-v4-modal .vault-v45-rarity-trigger::-webkit-details-marker {display:none;}
            .master-vault-v4-modal .vault-v45-rarity-trigger::marker {content:'';}
            .master-vault-v4-modal .vault-v45-rarity-trigger:hover,
            .master-vault-v4-modal .vault-v45-rarity-picker[open] .vault-v45-rarity-trigger {
                border-color:var(--rarity-accent);box-shadow:0 0 0 2px color-mix(in srgb,var(--rarity-accent) 12%,transparent);
            }
            .master-vault-v4-modal .vault-v45-rarity-current {display:flex;align-items:center;gap:10px;min-width:0;}
            .master-vault-v4-modal .vault-v45-rarity-current strong {font:750 11px/1.3 system-ui,sans-serif;}
            .master-vault-v4-modal .vault-v45-rarity-current .vault-v82-rarity-dot {background:var(--rarity-accent);width:8px;height:8px;flex-basis:8px;}
            .master-vault-v4-modal .vault-v45-rarity-trigger > svg {width:16px;height:16px;flex:0 0 16px;transition:transform .2s;}
            .master-vault-v4-modal .vault-v45-rarity-picker[open] .vault-v45-rarity-trigger > svg {transform:rotate(180deg);}
            .master-vault-v4-modal .vault-v45-rarity-options {
                display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px;
                margin-top:7px;padding:9px;border:1px solid #e3d2be;
                border-radius:11px;background:#fffaf3;box-shadow:0 6px 16px rgba(79,37,18,.06);
            }
            .master-vault-v4-modal .vault-v45-rarity-options .vault-v82-rarity-choice {min-height:39px;}
            .master-vault-v4-modal .vault-v82-equippable .vault-v45-equip-choices {
                display:flex;gap:6px;
            }
            .master-vault-v4-modal .vault-v45-equip-option {
                display:flex;gap:8px;align-items:center;justify-content:center;
                flex:1;min-width:0;min-height:43px;padding:6px 8px;
                border:1px solid #dbc9b4;border-radius:10px;
                background:#fffcf8;color:#8e745e;cursor:pointer;
                font:750 9px/1.25 system-ui,sans-serif;
            }
            .master-vault-v4-modal .vault-v45-equip-option.is-active {
                border-color:#ab6c45;background:linear-gradient(140deg,#fff5e7,#f1e1ce);color:#64351f;
                box-shadow:inset 0 0 0 1px rgba(171,108,69,.12);
            }
            .master-vault-v4-modal .vault-v45-hand-icon {
                position:relative;display:block;width:21px;height:21px;flex:0 0 21px;opacity:.83;
            }
            .master-vault-v4-modal .vault-v45-hand-icon::before,
            .master-vault-v4-modal .vault-v45-hand-icon::after {
                position:absolute;inset:0;content:'';display:block;
                -webkit-mask-size:contain;mask-size:contain;
                -webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;
                -webkit-mask-position:center;mask-position:center;
            }
            .master-vault-v4-modal .vault-v45-hand-icon::before {
                background:#fff9f1;-webkit-mask-image:var(--inventory-hand-fill);mask-image:var(--inventory-hand-fill);
            }
            .master-vault-v4-modal .vault-v45-hand-icon::after {
                background:currentColor;-webkit-mask-image:var(--inventory-hand-line);mask-image:var(--inventory-hand-line);
            }
            .master-vault-v4-modal .vault-v45-inventory-details {
                border:1px solid #e0ccb3;border-radius:12px;
                background:linear-gradient(125deg,#fffdf9,#f8f2e9);
                overflow:hidden;
            }
            .master-vault-v4-modal .vault-v45-inventory-trigger {
                display:flex;align-items:center;gap:10px;min-height:44px;
                padding:8px 12px;cursor:pointer;list-style:none;color:#75533b;
            }
            .master-vault-v4-modal .vault-v45-inventory-trigger::-webkit-details-marker {display:none;}
            .master-vault-v4-modal .vault-v45-inventory-trigger::marker {content:'';}
            .master-vault-v4-modal .vault-v45-inventory-summary-title {display:flex;align-items:center;gap:8px;white-space:nowrap;}
            .master-vault-v4-modal .vault-v45-inventory-summary-title > svg {width:18px;height:18px;flex:0 0 18px;color:#ac7950;}
            .master-vault-v4-modal .vault-v45-inventory-summary-title strong {font:750 11px system-ui,sans-serif;}
            .master-vault-v4-modal .vault-v45-inventory-summary-value {font:600 10px system-ui,sans-serif;margin-left:auto;color:#947b65;white-space:nowrap;}
            .master-vault-v4-modal .vault-v45-inventory-chevron {width:16px;height:16px;flex:0 0 16px;transition:transform .2s;}
            .master-vault-v4-modal .vault-v45-inventory-details[open] .vault-v45-inventory-chevron {transform:rotate(180deg);}
            .master-vault-v4-modal .vault-v45-inventory-fields {display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:12px;border-top:1px solid #ebdecc;}
            .master-vault-v4-modal .vault-v45-inventory-fields .master-vault-v4-input {min-height:39px;}
            .master-vault-v4-modal .vault-v45-weight-label {display:inline-flex;gap:6px;align-items:center;}
            .master-vault-v4-modal .vault-v45-weight-label svg {width:15px;height:15px;}
            .master-vault-v4-modal :is(.vault-v45-rarity-trigger,.vault-v45-equip-option,.vault-v45-inventory-trigger):focus-visible {outline:2px solid #8b5241;outline-offset:2px;}
            @media(max-width:1080px) {
                .master-vault-v4-modal .vault-v18-workspace {grid-template-columns:180px minmax(0,1fr);gap:13px;}
                .master-vault-v4-modal .vault-v18-sidebar-card {width:180px;}
                .master-vault-v4-modal .vault-v82-classification-main {grid-template-columns:1fr;}
                .master-vault-v4-modal .vault-v82-equippable {max-width:320px;}
            }
            @media(max-width:760px) {
                .master-vault-v4-modal .vault-v18-workspace {grid-template-columns:1fr;}
                .master-vault-v4-modal .vault-v18-sidebar {display:flex;justify-content:center;}
                .master-vault-v4-modal .vault-v18-sidebar-card {width:min(220px,100%);}
                .master-vault-v4-modal .vault-v45-description {grid-column:1;}
                .master-vault-v4-modal .vault-v45-rarity-options {grid-template-columns:repeat(2,minmax(0,1fr));}
            }
            @media(max-width:490px) {
                .master-vault-v4-modal .vault-v45-inventory-trigger {flex-wrap:wrap;}
                .master-vault-v4-modal .vault-v45-inventory-summary-value {order:3;width:100%;margin:0 0 0 26px;}
                .master-vault-v4-modal .vault-v45-inventory-fields {grid-template-columns:1fr;}
            }
            .master-vault-v4-modal .vault-v45-hand-icon.is-equipped {
                --inventory-hand-fill:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACgCAYAAACLz2ctAAAL1klEQVR42u2deaxcVR3HPzPzurfYKtgQikSK0NKGyhoIBZS0KgSaaEBJSBRcwZAomkCUhviHCxIX0ERJNGKClVCNVBSxaA0plQgoEIVipbUu1YDdaG3fo29m7vjH+R3nvNuZ9+7M3Ll3lu83OZl5y8zcOfdzf9s595wCUjOVgGrw8+nARcClwDLgDcAC+9t+YC/wAvAb4HFg6yTvJUlTwgdQANYAG4DDQC1hO2yvWWPvEb6nJDVVIQBmJbAlBlYVqNhjFPw+iv0tfM0W4MIG7y9JE1QMnn85AKxiLWrBAkbB6/zPdzT5LEn6v1WaBzwYs3a1DltoFR+0z0CWUIq73ZnAYwbKeIsWL4lFHLfnjwEz5I4HF6SitSQnuGDJQQFYH8BX61Lz770+9tlpfy8pw7htJEGGOdLkZPvX3ZoBfHEIb2mSHXswRxJk6iP9Hk/265VUClxkCOPrgdfZiTkIHABGG7zWv64GLAH+AEwPLEw3Fbrks4BtwWcWOLpeOBs4xlrVvte+2P8Vm7xWAHbB4tUCgJYBq4FLgKXAcXbCSgbeKPAP4GlccfhXuKIxBtw48DBwuZ28rGp1/rMeBq4IjgVccXu1lYHOBU6y7zTLwB0F9gDPAo8Cm4Htsew6knPsjtXzutI6v9yi+9sF3G2wApxhMFQzcL3xpMR/7hl2LEvt2Ha1+F5jwEPAxU36SkqxRncmsDF2AspMLA7HWzX4n3CkYi2wLiiV1DJu/jN/ANzGxJGWSnDMzb5TpcFxP4QbMhSEKYYIHr6bgNdovzjsLU85B9iStnKb3ymsNb4KXBtAqIy5A/j8VXxXA8vRqQtsB+BuueO0jiW8uO4UhJ3B58sQ36I7xeFBbaGV/4LccWcJx9oM63ODCuGnBWF78F0auFxZvs5c+zmCsLWkY7YVaGs5lEgGqfl4+ekMC+1tlTd6yfpFwMeAU60DNXWps/6smAX8iPVtsdcsTq8dy1zc1PZFdvUKwM4UWd9us6J3xX5fkwU8+mqtAZcBJ/bi1dqn8sOXS4BVDS7qIvWJDSXqs27ivy8MOoD+iryaieO9UjpWsAZ8IFbmKnL07QSNRlx8HF5KO5EZ6ZEO8jM55gAXMHEERErH0BRws29m4kaUvBbb78/GTXyYj5v4MAa8ArwE/B54BtgdnK8CAzTpwcO2Qplr10oyNeAI8EaD8Abc7KCxhO+xG/gh8I5Y2DQQoyzeEr+H/CYHDEv7PvB8g3KNn/wQtrK1eClsExNn3xQHBcAPc/SYplr3aoStFPnjkx5qwNesvtg2hL1G7jyFa12XTzRKLbrQQpAl+/e42dz4wnarFr1YiJa638fFlN6jbK54E3B8OxAq05Q60TRzy8uAn+EGEWglMRGAUhrxe9nKOPe0agV7DUAVn/vbEl4LvI8WbvCSBZTSkh/yu9OSySiJKxaAUpoARsCbgA9SH7oTgFLmYdSN5parAlDKUn5G02m4Mf0praAAlNKWn6DwTnssCEApS3ngzo8BKQClTAFcjBsnnjQbFoBStwCcj9tJAAEo5aEZuAnGfZUFawmJwdEICWbc9xKARepr5ElDol4B0N9fsEinRADmYarLwHXAJ6lPlpSGxE/n/fkV4Hrge9RnwygWlAXMHL5I8AnArDStAXza/0IAZh7zCT4BmIvbvQ64V/BJxRzgu17wSVkDOKKYT8oLQCUcUm4A+oRD8EmZAxgmHIJPyhRAJRxSbgAq4ZByA1DwSbkB6OF7v+CTsgbQw3dFEPMh+KQsAPSboVwIPBBAp/tNpK4D6FfLfAuwAbfFljaXkTIB0Md303G7fh9rMAo+qaXYrVPX+1XgPHs+oi6VsrCAHr41wCfsue7jkDIBsGBx3jzgm0HMp4xXygRAvxDhbbjFCLWpoJQZgH4/3+XUb6EUfFKmFrAGfA639keidYAlKQ0AS7gyyzJLPiJlvVIeFvBTuFnOkbpPygpAP+JxEnANCVdAl6Q0AQS4CjfcVlXsJyXIFVID0AP3XvtZ8EmZWUC/A84K3H5gcr9S5gACXBlkwpKUGYA+210p9ytlDaBfuXQ+cFaLcaMkdQyg//tbcfP9arKAUtYWEEtAUPwn5REDApyirpLyANAnIIuVgEhZA+gTkCLwZgEo5eWCZ1oWLAClXACcjbvzTZIyd8EewBnqKikvCzhLFlDKE0DFfVKuAI7j7vuVpFwALKMRECkHAGsCUOoFC3gQGFNXSXkB+JpBGFpFScrMBUfAAXWVlIcF9H/fIwso5QngPwWglFcMCPB3dZWUJ4A71FVSHgBGMQuo+4GlTAH0Md924FXqq6NKUqYueC+wU4mIlIcFLNnjn2NuWZIysYB+OtZT6i4pDwC9y31SiYjUgiISTGJpZW2YrcB/qN8tJ0mTaZwEk1iSWsAibjz4WSUiUgsAjk7FS6srpP5aAEoJdSQAkE4B9C53kz1XHChNlTOM4iYzpwrgC7hhOcWB0lQ6HCQhHbtgcHuCjAOPxKCUpEYWcE8SxoptvPGP23itNHwA7kgbQL8t15PAS9Q3LZSkRtrWSnablOySueGfyA1LTeRHzv4yVfzXjhv1wN1nGY6yYamRkYqAv3ULwKJlw4/b73TPsBTXPhLextHuhtUA30XrxkiNPeTzuJGzYjcA9Nt2/RQ3R1DJiBTPgDcn5avdDatLuEr3t9EsaelonjYncb904EK9aT0WN1F1AfVJC9LwWr8CrgB9KrA/iXFqFxifjOwG7kFDc1L9/D9j8E0Z/3UCoP/AAnA37p6Rklyx4j/qM6YSsdVpHW8E+C9uGd+3B5ZRGj74SriFrG4yC9jVGDD++vmWeh+vWHAoVTUANwLvooXKSDEF8otG/O3KiIde61rlKo1CciFovwPOCa4IaXiy372W/e5rxRAVUzyAKnCzsuGhdL8APzf4WkpGSyleBSXcGjLHAefjVtZXLDgcioAPAS+TsPzSDRXsw4/BzYTw94XW1Aa2VexxQ7seNU0L5V3xQeBGVJweBvlY744Uc4qONWKPd9nBlWUpBtr6beyCMev4qijhtnl9LnawaoMH4CUp5xOpyB/McuCQxYKKBwcPvgc6ha9b1Pqs+BXgr8DV1OcRahJr/2e8BdwQ7LvpcA+ZbprNmsWDfwTmABcZhCrN9D+AJeCzwC+p3wPSs1lSyR43KCnp++bDqOeAacG57flUvYDbef0pQdi3LbLYrwqs7MXEI0m98QSLCZUZ91/zRmNtv8EXjzdPA/4tCPsy630kVuulXyFcgZvOLwj7I+6LgH8BC6kPudLvEJ5rZRpB2PtxXw1Y1a+ud7LhuhXALiUmPQufPycf73fXOxmES3CrbQnC3mrj9nj7IMIXd8cnAL8VhD0H3zcC+AZ2BMtDOBv4USzwFQz5wbcuOD8DP3waZlWfb5D+q2Vb67vPwCsyRGP3YXp/Fe7+Arnk7LPdr8dGsIZOPtg9HdgSdJCmc3W3zheOchQZ8llLHsJpwBdjLkKxYfoutwrcMEwxX6tx4SrgT4oNu1Lj2wG8bZBLLZ3Ghb5T5gBfCrK0itxyR+O6Ndxi8wsFX/JSDcB5wKMxNyIQW0s0xoFbmvSvNIk1DDvqGuDF2JUtEKe2epuBC4IwRzPU24gNfZA8F7ccyE6B2NTi+aTtZer3a8vqpeyW5wO34lZkCMsLlSHMmqOYxYuA79hwZ7zeKqWYpADMAz6KWyY2Xm4YdBirsaJ9Gbgft04PSjSyjQ9LwOW4+1YPDTCMUYOQYwy4Fzgz1h+q7eUAIsDJlvE90aB+WAlOYNRHlq7S4LvsAL4CLI2BJ3ebI4jxzl8OfAY3xDfaJFss9xCUUQBcozHx/cB63A3ic/sRvGEwy77U4N1UaBkvBlbjVnU9ZZKTVm3QZ4UU+rAWe/TPJ0sUduK2zN0E/AJ3g1cY40X00apkwxYXNINxxIA822Kn5QbkItwOAHnpAO5moK1Wv3vCno81qAZE9OH63MMcmHoYazTe8XOWAXgycKI9LsbtDrXA2jzchInp1FcKSKIIOBxrh3DrLL+I231qu1m73cCRBiWoQr9Zu0b6H3TySrOuM3kcAAAAAElFTkSuQmCC');
                --inventory-hand-line:url('data:image/png;base64,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');
            }
            .master-vault-v4-modal .vault-v45-hand-icon.is-unequipped {
                --inventory-hand-fill:url('data:image/png;base64,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');
                --inventory-hand-line:url('data:image/png;base64,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');
            }


            /* ===========================================================
               V66.2.46 — Ajustes do editor: raridade sobreposta, mão única,
               sintonia discreta, peso em kg e foto centralizada.
               Somente modal; não altera cards, show nem efeitos dos itens.
               =========================================================== */
            .master-vault-v4-modal .vault-v18-sidebar {
                position:relative;top:auto;align-self:stretch;
                display:flex;align-items:center;justify-content:center;
                min-height:230px;
            }
            .master-vault-v4-modal .vault-v18-sidebar-card {
                margin:auto;
            }
            .master-vault-v4-modal .vault-v82-equippable {
                display:flex;flex-direction:column;gap:5px;max-width:176px;
            }
            .master-vault-v4-modal .vault-v46-equip-toggle {
                display:flex;align-items:center;justify-content:center;gap:7px;
                width:100%;min-height:43px;padding:7px 10px;
                border:1px solid #dccbb7;border-radius:10px;
                background:linear-gradient(145deg,#fffdf9,#f8f3ec);
                color:#86705e;font:750 10px/1.2 system-ui,sans-serif;
                cursor:pointer;transition:background .18s,border-color .18s,box-shadow .18s,color .18s;
            }
            .master-vault-v4-modal .vault-v46-equip-toggle .vault-v45-hand-icon {
                width:22px;height:22px;flex:0 0 22px;opacity:.8;
            }
            .master-vault-v4-modal .vault-v46-equip-toggle:hover {
                border-color:#bf9670;background:#fff8ed;
            }
            .master-vault-v4-modal .vault-v46-equip-toggle.is-active {
                border-color:#aa7750;color:#713820;
                background:linear-gradient(130deg,#fff3df,#f3e3cf);
                box-shadow:inset 0 0 0 1px rgba(172,121,65,.1);
            }
            .master-vault-v4-modal .vault-v46-equip-indicator {
                width:7px;height:7px;flex:0 0 7px;border-radius:50%;
                background:#cbbcac;transition:background .18s,box-shadow .18s;
            }
            .master-vault-v4-modal .vault-v46-equip-toggle.is-active .vault-v46-equip-indicator {
                background:#bd8c4d;box-shadow:0 0 0 3px rgba(189,140,77,.15);
            }
            .master-vault-v4-modal .vault-v46-equip-toggle:focus-visible,
            .master-vault-v4-modal .vault-v46-attunement-toggle:focus-visible {
                outline:2px solid #8b5241;outline-offset:2px;
            }
            /* Sobrepõe o formulário em vez de empurrar o conteúdo. */
            .master-vault-v4-modal .vault-v82-classification,
            .master-vault-v4-modal .vault-v18-classification-fields,
            .master-vault-v4-modal .vault-v82-rarity-field {overflow:visible;}
            .master-vault-v4-modal .vault-v82-rarity-field {position:relative;z-index:3;}
            .master-vault-v4-modal .vault-v82-rarity-field:has(.vault-v45-rarity-picker[open]) {z-index:31;}
            .master-vault-v4-modal .vault-v45-rarity-picker[open] {z-index:32;}
            .master-vault-v4-modal .vault-v45-rarity-options {
                position:absolute;left:0;right:0;bottom:calc(100% + 9px);z-index:33;
                margin-top:0;padding:10px;gap:6px;
                background:linear-gradient(155deg,#fffdf9,#f8efe3);
                border:1px solid #d7b98e;border-radius:12px;
                box-shadow:0 14px 35px rgba(72,38,18,.18),0 2px 6px rgba(72,38,18,.08);
            }
            .master-vault-v4-modal .vault-v45-rarity-options::after {
                content:'';position:absolute;bottom:-5px;left:23px;
                width:9px;height:9px;background:#faf3e8;
                border-right:1px solid #d7b98e;border-bottom:1px solid #d7b98e;
                transform:rotate(45deg);
            }
            .master-vault-v4-modal .vault-v46-attunement-field {min-width:0;}
            .master-vault-v4-modal .vault-v46-attunement-toggle {
                display:flex;align-items:center;gap:8px;width:100%;min-height:43px;
                padding:8px 11px;border:1px solid #ddcbb7;border-radius:10px;
                background:linear-gradient(145deg,#fffdf9,#f9f3ed);
                color:#94806d;font:750 10px/1.25 system-ui,sans-serif;
                text-align:left;cursor:pointer;
                transition:background .18s,border-color .18s,color .18s;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle svg {
                width:19px;height:19px;flex:0 0 19px;color:#b4a394;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle.is-on {
                border-color:#cbad72;background:linear-gradient(135deg,#fff8e8,#f4e6cf);
                color:#7a5223;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle.is-on svg {color:#bb8d43;}
            .master-vault-v4-modal .vault-v46-attunement-mark {
                width:9px;height:9px;flex:0 0 9px;border-radius:50%;
                margin-left:auto;background:#ddd0bf;
                box-shadow:inset 0 0 0 1px #cbb9a3;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle.is-on .vault-v46-attunement-mark {
                background:#c59b42;border:2px solid #fff5d6;box-shadow:0 0 0 1px #c59b42,0 0 7px rgba(197,155,66,.28);
            }
            .master-vault-v4-modal .vault-v46-weight-control {position:relative;min-width:0;}
            .master-vault-v4-modal .vault-v46-weight-control .master-vault-v4-input {padding-right:43px;}
            .master-vault-v4-modal .vault-v46-weight-unit {
                position:absolute;right:10px;top:50%;transform:translateY(-50%);
                color:#89694c;font:800 11px/1 system-ui,sans-serif;
                pointer-events:none;
            }
            @media(max-width:1080px) {
                .master-vault-v4-modal .vault-v82-equippable {max-width:180px;}
            }
            @media(max-width:760px) {
                .master-vault-v4-modal .vault-v18-sidebar {min-height:0;align-self:center;}
                .master-vault-v4-modal .vault-v18-sidebar-card {margin:0 auto;}
                .master-vault-v4-modal .vault-v45-rarity-options {grid-template-columns:repeat(2,minmax(0,1fr));}
            }


            /* ==========================================================
               V66.2.47 — Editor compacto: cristal, gramas, pergaminhos,
               seletor inferior de raridade e gaveta da descrição.
               ========================================================== */
            .master-vault-v4-modal .vault-v45-rarity-options {
                top:calc(100% + 8px) !important;bottom:auto !important;
                box-shadow:0 16px 34px rgba(72,38,18,.18) !important;
            }
            .master-vault-v4-modal .vault-v45-rarity-options::after {
                top:-5px !important;bottom:auto !important;
                border:0 !important;border-left:1px solid #d7b98e !important;
                border-top:1px solid #d7b98e !important;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle .vault-v247-crystal {
                width:22px;height:22px;flex:0 0 22px;object-fit:contain;
                opacity:.38;filter:grayscale(1) brightness(.75);
                transition:filter .22s ease,opacity .22s ease,transform .22s ease;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle.is-on .vault-v247-crystal {
                opacity:1;filter:brightness(0) saturate(100%) invert(66%) sepia(41%) saturate(632%) hue-rotate(350deg) brightness(101%) contrast(92%) drop-shadow(0 0 3px rgba(226,172,71,.82));
                transform:scale(1.07);
            }
            .master-vault-v4-modal .vault-v46-attunement-mark {display:none !important;}
            .master-vault-v4-modal .vault-v46-attunement-toggle {justify-content:flex-start;gap:9px;}
            .master-vault-v4-modal .vault-v247-weight-converted {
                display:block;margin-top:6px;color:#8c6a4c;font:650 10px/1.35 system-ui,sans-serif;
            }
            .master-vault-v4-modal .vault-v247-description-details > summary {
                display:flex;align-items:center;justify-content:space-between;list-style:none;
                cursor:pointer;user-select:none;
            }
            .master-vault-v4-modal .vault-v247-description-details > summary::-webkit-details-marker{display:none;}
            .master-vault-v4-modal .vault-v247-description-details > summary::marker{content:'';}
            .master-vault-v4-modal .vault-v247-description-summary svg {
                width:18px;height:18px;color:#a4754a;transition:transform .2s ease;
            }
            .master-vault-v4-modal .vault-v247-description-details[open] .vault-v247-description-summary svg {transform:rotate(180deg);}
            .master-vault-v4-modal .vault-v247-description-details:not([open]) {padding-bottom:0;}
            .master-vault-v4-modal .vault-v247-description-details:not([open]) .vault-v247-description-summary {border-bottom:0;}
            .master-vault-v4-modal .vault-v19-category-grid {
                grid-template-columns:repeat(5,minmax(0,1fr));gap:8px;
            }
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category {
                min-width:0;min-height:70px;padding:8px 10px;gap:8px;
            }
            .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category-icon {
                background:linear-gradient(150deg,#fff9f0,#eadac5) !important;
                border:1px solid #ddc0a0;color:#8d5b36;
            }
            .master-vault-v4-modal .vault-v247-category-image {
                display:block;width:25px;height:25px;object-fit:contain;
                filter:none;
            }
            .master-vault-v4-modal .vault-v247-scroll-editor {
                margin-top:13px;padding:15px;border:1px solid #dfc4a5;border-radius:13px;
                background:linear-gradient(125deg,#fffcf8,#f8f0e4);
            }
            .master-vault-v4-modal .vault-v247-scroll-heading {display:flex;align-items:center;gap:11px;margin-bottom:12px;}
            .master-vault-v4-modal .vault-v247-scroll-heading img {width:28px;height:28px;object-fit:contain;}
            .master-vault-v4-modal .vault-v247-scroll-heading h3 {margin:0;color:#642e20;font:800 16px/1.3 Georgia,serif;}
            .master-vault-v4-modal .vault-v247-scroll-heading p {margin:4px 0 0;font:500 10px/1.4 system-ui,sans-serif;color:#97725a;}
            .master-vault-v4-modal .vault-v247-spell-results {
                display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;
                max-height:200px;overflow:auto;margin:8px 0;padding:4px;
            }
            .master-vault-v4-modal .vault-v247-spell-choice {
                display:flex;justify-content:space-between;align-items:center;gap:10px;
                border:1px solid #dfc9b4;border-radius:9px;background:#fffdf9;
                padding:10px;color:#6d432c;text-align:left;cursor:pointer;
            }
            .master-vault-v4-modal .vault-v247-spell-choice strong {font:700 11px/1.3 system-ui,sans-serif;}
            .master-vault-v4-modal .vault-v247-spell-choice span {font:600 9px/1.3 system-ui,sans-serif;white-space:nowrap;color:#99806c;}
            .master-vault-v4-modal .vault-v247-spell-choice:hover,.master-vault-v4-modal .vault-v247-spell-choice.is-selected {
                border-color:#bb8856;background:#fff3df;
            }
            .master-vault-v4-modal .vault-v247-spell-empty {font:500 11px/1.5 system-ui,sans-serif;color:#846b57;margin:10px 0;}
            .master-vault-v4-modal .vault-v247-spell-manual {display:grid;grid-template-columns:2fr 1fr;gap:10px;margin-top:12px;}
            .master-vault-v4-modal .vault-v247-spell-linked {font:600 10px/1.5 system-ui,sans-serif;color:#87705b;}
            @media (max-width:1180px) {
                .master-vault-v4-modal .vault-v19-category-grid {grid-template-columns:repeat(3,minmax(0,1fr));}
            }
            @media (max-width:760px) {
                .master-vault-v4-modal .vault-v19-category-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
                .master-vault-v4-modal .vault-v247-spell-results,.master-vault-v4-modal .vault-v247-spell-manual {grid-template-columns:1fr;}
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-v4-modal .vault-v46-attunement-toggle .vault-v247-crystal {transition:none;}
            }

            .master-vault-v4-modal .vault-v247-description-details > .vault-v31-rich-field .vault-v31-rich-toolbar {justify-content:flex-end;}
            .master-vault-v4-modal .vault-v247-description-details > .vault-v31-rich-field .vault-v31-rich-actions {margin-left:auto;}

            /* ===========================================================
               V66.2.48 — Peso com máscara local, cristal vivo, toolbar central
               =========================================================== */
            .master-vault-v4-modal .vault-v46-weight-control .vault-v248-weight-input {
                padding-right:59px;
                font-variant-numeric:tabular-nums;
                letter-spacing:.015em;
            }
            .master-vault-v4-modal .vault-v46-weight-unit {
                right:12px;
                padding-left:9px;
                border-left:1px solid rgba(154,111,74,.25);
                font-size:11px;
                color:#825b3c;
            }
            /* O campo mostra o resultado em kg; não duplicar texto embaixo. */
            .master-vault-v4-modal .vault-v247-weight-converted {display:none !important;}

            .master-vault-v4-modal .vault-v46-attunement-toggle {
                min-height:51px;
                gap:11px;
                padding:7px 12px;
                position:relative;
                overflow:hidden;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle .vault-v247-crystal {
                width:38px;
                height:38px;
                flex:0 0 38px;
                opacity:.52;
                transform:none;
                filter:grayscale(1) brightness(.72);
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle.is-on .vault-v247-crystal {
                transform:none;
                filter:brightness(0) saturate(100%) invert(70%) sepia(55%) saturate(718%) hue-rotate(353deg) brightness(105%) contrast(95%) drop-shadow(0 0 5px rgba(241,182,66,.68));
                animation:vault-v248-crystal-pulse 3.1s ease-in-out infinite;
                will-change:transform,opacity;
            }
            .master-vault-v4-modal .vault-v46-attunement-toggle.is-on::after {
                content:'';
                position:absolute;
                width:51px;height:39px;
                top:50%;left:4px;
                transform:translateY(-50%);
                border-radius:50%;pointer-events:none;
                background:radial-gradient(ellipse,rgba(239,179,75,.20),transparent 72%);
                animation:vault-v248-crystal-halo 3.1s ease-in-out infinite;
            }
            @keyframes vault-v248-crystal-pulse {
                0%,100% {transform:translateY(0) scale(.97);opacity:.85;}
                28% {transform:translateY(-3px) scale(1.05);opacity:1;}
                57% {transform:translateY(-1px) scale(1.01);opacity:.92;}
                76% {transform:translateY(-2px) scale(1.08);opacity:1;}
            }
            @keyframes vault-v248-crystal-halo {
                0%,100% {opacity:.30;}
                35% {opacity:.88;}
                68% {opacity:.42;}
                80% {opacity:.75;}
            }
            @media (prefers-reduced-motion:reduce) {
                .master-vault-v4-modal .vault-v46-attunement-toggle.is-on .vault-v247-crystal,
                .master-vault-v4-modal .vault-v46-attunement-toggle.is-on::after {
                    animation:none !important;
                    will-change:auto;
                }
            }

            .master-vault-v4-modal .vault-v31-rich-field .vault-v31-rich-toolbar {
                justify-content:center !important;
            }
            .master-vault-v4-modal .vault-v31-rich-field .vault-v31-rich-actions {
                flex:0 1 auto !important;
                min-width:0;
                width:max-content;
                max-width:100%;
                margin-left:auto !important;
                margin-right:auto !important;
                justify-content:center !important;
            }
            @media (max-width:650px) {
                .master-vault-v4-modal .vault-v31-rich-field .vault-v31-rich-actions {
                    width:100%;justify-content:flex-start !important;
                    overflow-x:auto;
                }
            }

            /* V66.2.49 — Espada com a mesma presença visual dos demais ícones.
               Ajuste exclusivo do botão Arma, sem alterar os outros ícones. */
            .master-vault-v4-modal .vault-v19-category-grid
            .vault-v249-weapon-icon {
                padding:2px;
            }
            .master-vault-v4-modal .vault-v19-category-grid .vault-v249-weapon-image {
                width:32px;
                height:32px;
                max-width:none;
                object-fit:contain;
            }

            /* ============================================================
               SPELLBOUND V66.2.50 — Editor de Armas
               Propriedades e maestrias claras; danos múltiplos no mesmo
               contrato (damage + extra_damage) já lido pelo Inventário.
               ============================================================ */
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-module-head {
                padding:12px 18px;
                background:linear-gradient(100deg,#f4e9db 0%,#fcf9f4 100%);
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-module-head h3 {
                font-size:19px;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-module-body {
                gap:11px;
                padding:14px 16px 18px;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-rule {
                padding:15px;
                border-color:#e6d5c0;
                background:linear-gradient(150deg,rgba(255,254,250,.98),rgba(251,246,238,.72));
            }
            .master-vault-v4-modal .vault-v250-weapon .vault-v19-options--weapon .vault-v19-option {
                min-height:54px;
                border-radius:10px;
            }
            .master-vault-v4-modal .vault-v250-model {
                display:block;
                margin-top:12px;
            }
            .master-vault-v4-modal .vault-v250-model .master-vault-v4-input {
                max-width:none;
            }
            .master-vault-v4-modal .vault-v250-rule-heading,
            .master-vault-v4-modal .vault-v250-damage-header {
                display:flex;
                flex-wrap:wrap;
                align-items:center;
                justify-content:space-between;
                gap:10px 18px;
                margin-bottom:12px;
            }
            .master-vault-v4-modal .vault-v250-rule-heading .master-vault-v4-rule-title,
            .master-vault-v4-modal .vault-v250-damage-header .master-vault-v4-rule-title {
                margin:0 0 4px;
                color:#5e261c;
                font:700 16px/1.25 Georgia,serif;
            }
            .master-vault-v4-modal .vault-v250-rule-heading .master-vault-v4-rule-copy,
            .master-vault-v4-modal .vault-v250-damage-header .master-vault-v4-rule-copy {
                margin:0;
                color:#907159;
                font-size:10px;
                line-height:1.45;
            }
            .master-vault-v4-modal .vault-v250-count {
                flex:0 0 auto;
                border:1px solid #e3d2ba;
                border-radius:999px;
                background:#faf4eb;
                padding:5px 10px;
                color:#8b674d;
                font:700 9px/1.2 system-ui,sans-serif;
                white-space:nowrap;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-chip-row {
                display:flex;
                flex-wrap:wrap;
                gap:7px;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-chip-button.vault-v250-choice {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                gap:7px;
                min-height:34px;
                padding:6px 12px 6px 8px;
                border:1px solid #dbc8b1;
                border-radius:9px;
                background:#fffdf9;
                color:#785743;
                box-shadow:0 1px 2px rgba(78,43,20,.025);
                font:750 10px/1.2 system-ui,sans-serif;
                letter-spacing:0;
                transition:background .16s,border-color .16s,color .16s,box-shadow .16s;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-chip-button.vault-v250-choice:hover {
                border-color:#bc9b79;
                background:#fbf3e8;
                color:#682a1f;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-chip-button.vault-v250-choice.is-active {
                border-color:#b38468;
                background:linear-gradient(112deg,#f0e0d3,#f9efe6);
                color:#61291f;
                box-shadow:inset 0 0 0 1px rgba(122,63,42,.06);
            }
            .master-vault-v4-modal .vault-v250-choice-indicator {
                display:inline-flex;
                width:17px;
                height:17px;
                flex:0 0 17px;
                align-items:center;
                justify-content:center;
                border:1px solid #d9c8b3;
                border-radius:5px;
                background:#f9f4ec;
                color:transparent;
                font-size:10px;
                line-height:1;
            }
            .master-vault-v4-modal .vault-v250-choice.is-active .vault-v250-choice-indicator {
                border-color:#98583d;
                background:#8e4635;
                color:#fff9ed;
            }
            .master-vault-v4-modal .vault-v250-choice--custom {
                border-style:dashed !important;
                color:#8c603f !important;
                padding-left:12px !important;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-selected {
                margin-top:12px;
                padding:10px 11px 11px;
                border:1px solid #e6d7c3;
                border-radius:11px;
                background:rgba(247,241,232,.56);
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-selected-head {
                margin:0 0 8px;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-selected-head strong {
                color:#744330;
                font-size:10px;
                font-weight:750;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-selected-head span {
                color:#a18a75;
                font-weight:600;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-list {
                display:grid;
                grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
                align-items:start;
                gap:7px;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer {
                border-radius:9px;
                background:#fffefa;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-toggle {
                min-height:34px;
                padding:6px 8px;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-body {
                padding:11px;
            }
            .master-vault-v4-modal .vault-v250-weapon .master-vault-v4-repeat-row {
                border-color:#ead8c6;
                background:#fffdf9;
                border-radius:10px;
            }

            /* Construtor de dano: compacto, uma linha para cada tipo. */
            .master-vault-v4-modal .vault-v250-damage-header {
                margin-bottom:14px;
            }
            .master-vault-v4-modal .vault-v250-damage-summary {
                display:flex;
                flex:0 1 auto;
                min-width:min(255px,100%);
                max-width:100%;
                flex-direction:column;
                gap:3px;
                padding:9px 13px;
                border:1px solid #ddc6a7;
                border-radius:10px;
                background:linear-gradient(110deg,#f7ede0,#fffaf1 66%,#f4e8d4);
                text-align:right;
            }
            .master-vault-v4-modal .vault-v250-damage-summary span {
                color:#967354;
                font:800 8px/1.2 system-ui,sans-serif;
                letter-spacing:.07em;
                text-transform:uppercase;
            }
            .master-vault-v4-modal .vault-v250-damage-summary strong {
                color:#612b20;
                font:800 15px/1.35 Georgia,serif;
                overflow-wrap:anywhere;
            }
            .master-vault-v4-modal .vault-v250-damage-list {
                display:grid;
                gap:9px;
            }
            .master-vault-v4-modal .vault-v250-damage-part {
                min-width:0;
                padding:12px 13px 13px;
                border:1px solid #e2d0bc;
                border-radius:11px;
                background:rgba(255,253,249,.91);
            }
            .master-vault-v4-modal .vault-v250-damage-part.is-extra {
                background:linear-gradient(120deg,#fbf7ef,#fffdfa);
            }
            .master-vault-v4-modal .vault-v250-damage-part-header {
                display:flex;
                gap:8px;
                align-items:center;
                margin-bottom:11px;
                color:#6a3528;
                font:750 11px/1.3 system-ui,sans-serif;
            }
            .master-vault-v4-modal .vault-v250-damage-part-number {
                display:grid;
                flex:0 0 25px;
                width:25px;
                height:25px;
                place-items:center;
                border:1px solid #e6d5bf;
                border-radius:8px;
                background:#f8efe3;
                color:#9b7658;
                font:750 9px/1 system-ui,sans-serif;
            }
            .master-vault-v4-modal .vault-v250-damage-fields {
                display:grid;
                grid-template-columns:minmax(74px,.4fr) minmax(100px,.6fr) minmax(148px,1.4fr) minmax(155px,1.1fr);
                gap:10px;
            }
            .master-vault-v4-modal .vault-v250-damage-fields.is-extra {
                grid-template-columns:minmax(74px,.4fr) minmax(100px,.6fr) minmax(148px,2.5fr);
            }
            .master-vault-v4-modal .vault-v250-damage-fields .master-vault-v4-field {
                min-width:0;
                display:block;
            }
            .master-vault-v4-modal .vault-v250-damage-fields .master-vault-v4-field > span {
                display:block;
                margin:0 0 5px;
                font-size:10px;
                font-weight:750;
            }
            .master-vault-v4-modal .vault-v250-damage-fields :is(input,select) {
                width:100%;
                min-width:0;
                min-height:39px;
                padding:7px 10px;
                font-size:11px;
            }
            .master-vault-v4-modal .vault-v250-custom-fields {
                display:flex;
                flex-wrap:wrap;
                gap:10px;
                margin-top:9px;
            }
            .master-vault-v4-modal .vault-v250-custom-fields > input {
                min-width:170px;
                max-width:400px;
                flex:1 1 210px;
            }
            .master-vault-v4-modal .vault-v250-extra-custom {
                width:100%;
                max-width:400px;
                margin-top:9px;
            }
            .master-vault-v4-modal .vault-v250-remove-damage {
                display:inline-flex;
                margin-left:auto;
                align-items:center;
                gap:5px;
                border:1px solid transparent;
                border-radius:7px;
                background:transparent;
                padding:5px 8px;
                color:#925d4e;
                font:750 9px/1.2 system-ui,sans-serif;
                cursor:pointer;
            }
            .master-vault-v4-modal .vault-v250-remove-damage svg {
                width:13px;height:13px;
            }
            .master-vault-v4-modal .vault-v250-remove-damage:hover {
                border-color:#e6c5b9;
                background:#faeee8;
                color:#882d26;
            }
            .master-vault-v4-modal .vault-v250-add-damage {
                display:inline-flex;
                min-height:38px;
                align-items:center;
                justify-content:center;
                gap:8px;
                margin-top:11px;
                padding:8px 13px;
                border:1px dashed #c69c78;
                border-radius:9px;
                background:#fcf7ef;
                color:#78412d;
                font:800 10px/1.3 system-ui,sans-serif;
                cursor:pointer;
            }
            .master-vault-v4-modal .vault-v250-add-damage svg {
                width:15px;height:15px;
            }
            .master-vault-v4-modal .vault-v250-add-damage:hover {
                border-style:solid;
                background:#f6e9da;
                color:#67291f;
            }
            @media(max-width:990px) {
                .master-vault-v4-modal .vault-v250-damage-fields,
                .master-vault-v4-modal .vault-v250-damage-fields.is-extra {
                    grid-template-columns:repeat(2,minmax(0,1fr));
                }
            }
            @media(max-width:610px) {
                .master-vault-v4-modal .vault-v250-rule-heading,
                .master-vault-v4-modal .vault-v250-damage-header {
                    align-items:stretch;
                }
                .master-vault-v4-modal .vault-v250-damage-summary {
                    flex:1 1 100%;
                    text-align:left;
                }
                .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-list {
                    grid-template-columns:1fr;
                }
                .master-vault-v4-modal .vault-v250-damage-fields,
                .master-vault-v4-modal .vault-v250-damage-fields.is-extra {
                    grid-template-columns:repeat(2,minmax(0,1fr));
                    gap:8px;
                }
                .master-vault-v4-modal .vault-v250-damage-type {
                    grid-column:span 2;
                }
                .master-vault-v4-modal .vault-v250-damage-ability {
                    grid-column:span 2;
                }
            }

/* ================================================================
   V66.2.51 — Armas: atributos, encantamento e dano compacto.
   ================================================================ */
.master-vault-v4-modal .vault-v251-attributes,
.master-vault-v4-modal .vault-v251-enchant {
    background:linear-gradient(125deg,rgba(255,253,247,.82),rgba(246,238,225,.55));
}
.master-vault-v4-modal .vault-v251-ability-row,
.master-vault-v4-modal .vault-v251-damage-type-list {
    display:flex;flex-wrap:wrap;gap:7px;margin-top:11px;
}
.master-vault-v4-modal .vault-v251-option,
.master-vault-v4-modal .vault-v251-type-chip {
    display:inline-flex;align-items:center;justify-content:center;gap:7px;
    min-height:34px;border:1px solid rgba(173,128,78,.33);border-radius:9px;
    padding:6px 12px;background:rgba(255,253,250,.73);color:#77513e;
    font-size:11px;font-weight:700;cursor:pointer;transition:background .15s,border-color .15s,box-shadow .15s;
}
.master-vault-v4-modal .vault-v251-option:hover,
.master-vault-v4-modal .vault-v251-type-chip:hover {border-color:rgba(161,100,40,.65);background:#fff9ed}
.master-vault-v4-modal .vault-v251-option.is-selected,
.master-vault-v4-modal .vault-v251-type-chip.is-selected {
    background:linear-gradient(125deg,#f4e4c5,#fff8ed);border-color:#bf8d4b;
    color:#70401d;box-shadow:inset 0 0 0 1px rgba(255,255,255,.8);
}
.master-vault-v4-modal .vault-v251-option-dot {width:7px;height:7px;border:1px solid #c5a78b;border-radius:50%}
.master-vault-v4-modal .vault-v251-option.is-selected .vault-v251-option-dot {background:#b87c2c;border-color:#b87c2c;box-shadow:0 0 0 3px rgba(193,142,67,.16)}
.master-vault-v4-modal .vault-v251-quiet {font-size:10px;line-height:1.5;color:#947660;margin:10px 0 0}
.master-vault-v4-modal .vault-v251-enchant-top {display:flex;align-items:center;justify-content:space-between;gap:18px}
.master-vault-v4-modal .vault-v251-switch {display:flex;align-items:center;gap:9px;min-height:36px;border-radius:18px;border:1px solid #d6c2ad;padding:4px 12px 4px 5px;background:#efe8dd;color:#806755;cursor:pointer}
.master-vault-v4-modal .vault-v251-switch.is-on {background:#faeccb;border-color:#cba561;color:#70441f}
.master-vault-v4-modal .vault-v251-switch-knob {display:block;width:24px;height:24px;border-radius:50%;background:#fff;border:1px solid #cfc3b5;box-shadow:0 1px 3px rgba(50,30,10,.12)}
.master-vault-v4-modal .vault-v251-switch.is-on .vault-v251-switch-knob {background:#e4ad47;border-color:#c18e40;box-shadow:0 0 0 3px rgba(226,172,64,.15)}
.master-vault-v4-modal .vault-v251-switch strong {font-size:10px}
.master-vault-v4-modal .vault-v251-enchant-body {display:grid;grid-template-columns:140px minmax(0,1fr);gap:12px;margin-top:13px}
.master-vault-v4-modal .vault-v251-feature-preview {display:flex;flex-direction:column;justify-content:center;gap:3px;border:1px solid #e4ceb0;border-radius:10px;background:rgba(250,238,212,.55);padding:10px 12px}
.master-vault-v4-modal .vault-v251-feature-preview strong {color:#785021;font-family:Georgia,serif;font-size:13px}
.master-vault-v4-modal .vault-v251-feature-preview span {color:#756452;font-size:11px;line-height:1.5}
.master-vault-v4-modal .vault-v251-damage-list {display:grid;gap:10px;margin:13px 0}
.master-vault-v4-modal .vault-v251-damage-line {padding:13px;border:1px solid rgba(201,164,119,.45);border-radius:12px;background:rgba(255,253,250,.75)}
.master-vault-v4-modal .vault-v251-damage-line-head {display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;color:#783e2e;font-size:12px}
.master-vault-v4-modal .vault-v251-damage-grid {display:grid;grid-template-columns:minmax(160px, .65fr) minmax(0,1.8fr);gap:12px;align-items:start}
.master-vault-v4-modal .vault-v251-damage-types>span {font-size:10px;color:#754d3e;font-weight:750}
.master-vault-v4-modal .vault-v251-damage-type-list {gap:5px;margin-top:7px}
.master-vault-v4-modal .vault-v251-type-chip {padding:4px 8px;min-height:28px;font-size:10px}
.master-vault-v4-modal .vault-v251-custom-type {margin-top:10px;max-width:280px}
.master-vault-v4-modal .vault-v251-damage .vault-v250-damage-summary {max-width:40%;min-width:185px}
@media(max-width:740px) {
    .master-vault-v4-modal .vault-v251-damage-grid,
    .master-vault-v4-modal .vault-v251-enchant-body {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v251-damage .vault-v250-damage-summary {max-width:none}
}

.master-vault-v4-modal .vault-v251-attack-choice {max-width:295px;margin-top:13px}

.master-vault-v4-modal .vault-v251-error {display:block;margin-top:5px;color:#a53e2b;font-size:10px}


/* V66.2.52 — Configuração precisa do dano e habilidade automática do encantamento. */
.master-vault-v4-modal .vault-v252-dice-controls {min-width:0}
.master-vault-v4-modal .vault-v252-field-label,
.master-vault-v4-modal .vault-v252-die-field>span {display:block;font-size:10px;font-weight:800;color:#73503e;margin:0 0 6px}
.master-vault-v4-modal .vault-v252-dice-selectors {display:flex;align-items:end;flex-wrap:wrap;gap:8px}
.master-vault-v4-modal .vault-v252-quantity {display:grid;grid-template-columns:34px minmax(36px,auto) 34px;align-items:center;min-height:39px;border:1px solid #d6bfa3;border-radius:9px;background:#fffdfa;overflow:hidden}
.master-vault-v4-modal .vault-v252-quantity button {height:39px;border:0;background:#f5ecdf;color:#804f32;font-size:18px;font-weight:800;cursor:pointer}
.master-vault-v4-modal .vault-v252-quantity button:hover:not(:disabled) {background:#eedbc5}
.master-vault-v4-modal .vault-v252-quantity button:disabled {opacity:.34;cursor:default}
.master-vault-v4-modal .vault-v252-quantity strong {text-align:center;font-size:13px;color:#5a291e;padding:0 8px;font-variant-numeric:tabular-nums}
.master-vault-v4-modal .vault-v252-die-field {flex:1 1 72px;min-width:72px}
.master-vault-v4-modal .vault-v252-die-field select {height:41px;width:100%;border:1px solid #d6bfa3;border-radius:9px;background:#fffdfa;color:#5e392b;padding:0 9px;font-size:12px;font-weight:700;cursor:pointer;outline:none}
.master-vault-v4-modal .vault-v252-die-field select:focus-visible,.master-vault-v4-modal .vault-v252-quantity:focus-within {border-color:#b37e4a;box-shadow:0 0 0 3px rgba(191,141,75,.14)}
.master-vault-v4-modal .vault-v252-modifier-field {flex:1.4 1 112px}
.master-vault-v4-modal .vault-v251-damage-grid {grid-template-columns:minmax(320px,.9fr) minmax(0,1.4fr);gap:18px}
.master-vault-v4-modal .vault-v252-custom-damage {margin-top:6px}
.master-vault-v4-modal .vault-v252-custom-toggle {display:inline-flex;align-items:center;gap:8px;border:0;background:transparent;padding:6px 1px;color:#936647;font-size:10px;font-weight:800;cursor:pointer}
.master-vault-v4-modal .vault-v252-custom-toggle:hover {color:#6c3020}
.master-vault-v4-modal .vault-v252-custom-toggle small {font-weight:500;color:#998273;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.master-vault-v4-modal .vault-v252-custom-input {max-width:320px;margin:6px 0 0}
.master-vault-v4-modal .vault-v252-enchant-note {display:flex;align-items:center;gap:9px;border:1px solid rgba(192,153,93,.38);border-radius:10px;background:rgba(252,243,221,.6);padding:7px 11px;min-width:0}
.master-vault-v4-modal .vault-v252-enchant-note>span:last-child {display:flex;flex-direction:column;gap:3px;min-width:0}
.master-vault-v4-modal .vault-v252-enchant-note strong {font-size:11px;color:#80532a}
.master-vault-v4-modal .vault-v252-enchant-note small {font-size:10px;color:#91735b;line-height:1.4}
.master-vault-v4-modal .vault-v252-enchant-status {color:#be8435;font-size:20px}
.master-vault-v4-modal .vault-v252-auto-ability {margin:12px 16px 0;border:1px solid rgba(191,137,59,.46);border-radius:12px;background:linear-gradient(120deg,#fffaf0,#f6e9ce 100%);padding:12px 15px;box-shadow:inset 0 1px 0 rgba(255,255,255,.85)}
.master-vault-v4-modal .vault-v252-auto-header {display:flex;align-items:center;gap:8px}
.master-vault-v4-modal .vault-v252-auto-header strong {font:800 15px Georgia,serif;color:#77421d}
.master-vault-v4-modal .vault-v252-auto-mark {color:#bd8740;font-size:18px}
.master-vault-v4-modal .vault-v252-auto-tag {margin-left:auto;font-size:9px;font-weight:800;border:1px solid #dfbf82;border-radius:999px;padding:4px 8px;color:#96612c;background:rgba(255,255,255,.6)}
.master-vault-v4-modal .vault-v252-auto-ability p {margin:7px 0 5px;font-size:12px;line-height:1.6;color:#664738}
.master-vault-v4-modal .vault-v252-auto-ability>small {font-size:10px;line-height:1.5;color:#92765d}
@media(max-width:870px){.master-vault-v4-modal .vault-v251-damage-grid {grid-template-columns:1fr;gap:12px}}
@media(max-width:490px){.master-vault-v4-modal .vault-v252-auto-tag {font-size:8px}.master-vault-v4-modal .vault-v252-dice-selectors {gap:6px}}


/* =================================================================
   SPELLBOUND V66.2.53 — Encantamento no editor de habilidades.
   O estado ativo é configurado e pré-visualizado no mesmo cartão.
   ================================================================= */
.master-vault-v4-modal .vault-v253-enchant-ability {
    margin: 12px 16px 4px;
    padding: 12px 14px;
    border: 1px solid rgba(162,128,91,.30);
    border-radius: 13px;
    background: linear-gradient(130deg,rgba(251,248,243,.92),rgba(245,238,225,.62));
    box-shadow: inset 0 1px 0 rgba(255,255,255,.76);
    transition: border-color .18s,background .18s,box-shadow .18s;
}
.master-vault-v4-modal .vault-v253-enchant-ability.is-enchanted {
    border-color:rgba(189,140,65,.64);
    background:linear-gradient(126deg,#fffbf1 0%,#faf0d8 56%,#fdf9ec 100%);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.85),0 4px 17px rgba(128,79,18,.07);
}
.master-vault-v4-modal .vault-v253-enchant-heading {display:flex;align-items:center;gap:12px;min-width:0}
.master-vault-v4-modal .vault-v253-enchant-emblem {
    display:grid;place-items:center;flex:0 0 42px;width:42px;height:42px;
    border:1px solid rgba(167,133,99,.3);border-radius:12px;
    background:rgba(255,255,255,.62);color:#a59a89;
    transition:color .18s,border-color .18s,background .18s;
}
.master-vault-v4-modal .vault-v253-enchant-emblem svg {width:25px;height:25px}
.master-vault-v4-modal .is-enchanted .vault-v253-enchant-emblem {
    color:#aa782b;border-color:rgba(205,159,82,.65);
    background:linear-gradient(135deg,#fffdf6,#f4e1b9);
}
.master-vault-v4-modal .vault-v253-enchant-title {display:flex;flex-direction:column;gap:3px;min-width:0;flex:1}
.master-vault-v4-modal .vault-v253-enchant-overline {
    font-size:9px;line-height:1.25;font-weight:800;letter-spacing:.09em;
    text-transform:uppercase;color:#a18b75;
}
.master-vault-v4-modal .is-enchanted .vault-v253-enchant-overline {color:#aa7a3b}
.master-vault-v4-modal .vault-v253-enchant-title>strong {
    font:700 17px/1.2 Georgia,serif;color:#744836;
}
.master-vault-v4-modal .is-enchanted .vault-v253-enchant-title>strong {color:#815521}
.master-vault-v4-modal .vault-v253-enchant-title>small {
    font-size:10px;line-height:1.35;color:#927c6c;
}
.master-vault-v4-modal .vault-v253-enchant-toggle {
    border:1px solid #c7b6a5;background:rgba(255,255,255,.77);
    color:#77604d;border-radius:99px;padding:5px 11px 5px 6px;
    min-height:34px;display:inline-flex;align-items:center;gap:8px;
    font-size:10px;font-weight:800;cursor:pointer;white-space:nowrap;
}
.master-vault-v4-modal .vault-v253-enchant-toggle:hover {border-color:#b58349}
.master-vault-v4-modal .vault-v253-enchant-toggle.is-on {
    color:#79501f;background:rgba(255,248,227,.9);border-color:#c89c58;
}
.master-vault-v4-modal .vault-v253-enchant-toggle-track {
    display:flex;align-items:center;width:32px;height:19px;padding:2px;
    background:#d2c7bb;border-radius:99px;transition:background .18s;
}
.master-vault-v4-modal .vault-v253-enchant-toggle-track i {
    display:block;width:15px;height:15px;border-radius:50%;background:#fff;
    box-shadow:0 1px 2px rgba(70,39,13,.17);transition:transform .18s;
}
.master-vault-v4-modal .vault-v253-enchant-toggle.is-on .vault-v253-enchant-toggle-track {background:#c4923d}
.master-vault-v4-modal .vault-v253-enchant-toggle.is-on .vault-v253-enchant-toggle-track i {transform:translateX(13px)}
.master-vault-v4-modal .vault-v253-enchant-detail {
    margin-top:12px;padding-top:12px;border-top:1px solid rgba(187,152,100,.25);
    display:grid;grid-template-columns:minmax(195px, 225px) minmax(0,1fr);
    column-gap:14px;row-gap:8px;align-items:start;
}
.master-vault-v4-modal .vault-v253-enchant-bonuses-title {
    display:block;margin-bottom:8px;font-size:10px;font-weight:800;color:#805f37;
}
.master-vault-v4-modal .vault-v253-enchant-levels {display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:5px}
.master-vault-v4-modal .vault-v253-enchant-levels button {
    min-height:35px;padding:0 2px;border:1px solid #d4b68c;border-radius:8px;
    background:rgba(255,254,251,.85);color:#835f3d;font-size:12px;font-weight:800;
    cursor:pointer;transition:background .16s,border-color .16s,box-shadow .16s;
}
.master-vault-v4-modal .vault-v253-enchant-levels button:hover {border-color:#b58849;background:#fff9eb}
.master-vault-v4-modal .vault-v253-enchant-levels button.is-selected {
    border-color:#ad7f37;background:linear-gradient(135deg,#d5a451,#f4dca1);
    color:#59370f;box-shadow:inset 0 1px 0 rgba(255,255,255,.5);
}
.master-vault-v4-modal .vault-v253-enchant-preview {
    border:1px solid rgba(183,141,75,.3);border-radius:10px;
    padding:9px 12px;background:rgba(255,255,255,.48);
}
.master-vault-v4-modal .vault-v253-enchant-preview-heading {
    display:flex;align-items:center;flex-wrap:wrap;justify-content:space-between;gap:8px;
}
.master-vault-v4-modal .vault-v253-enchant-preview-heading strong {font:700 13px Georgia,serif;color:#785124}
.master-vault-v4-modal .vault-v253-enchant-preview-heading span {
    color:#986b38;font-size:9px;font-weight:750;
}
.master-vault-v4-modal .vault-v253-enchant-preview p {
    color:#6d523d;font-size:11px;line-height:1.55;margin:6px 0 0;
}
.master-vault-v4-modal .vault-v253-enchant-hint {
    grid-column:1/-1;margin:0;font-size:10px;line-height:1.45;color:#9b7d60;
}
/* Quantidade, dado e modificador: controles estritamente fechados. */
.master-vault-v4-modal .vault-v253-count-field {flex:0 0 85px;min-width:80px}
.master-vault-v4-modal .vault-v253-roll-preview {
    margin-left:auto;color:#8d6341;font-size:11px;font-weight:800;
    font-variant-numeric:tabular-nums;
}
.master-vault-v4-modal .vault-v252-dice-selectors {flex-wrap:nowrap;align-items:end}
.master-vault-v4-modal .vault-v252-die-field {min-width:65px}
.master-vault-v4-modal .vault-v252-modifier-field {flex:1.5 1 130px;min-width:105px}
.master-vault-v4-modal .vault-v252-custom-damage {
    display:flex;align-items:center;gap:7px;flex-wrap:wrap;
    margin-top:7px;
}
.master-vault-v4-modal .vault-v252-custom-toggle {
    border:1px dashed rgba(175,134,90,.42);border-radius:7px;
    padding:5px 9px;background:rgba(255,255,255,.36);color:#947250;
    font-size:10px;line-height:1.25;
}
.master-vault-v4-modal .vault-v252-custom-toggle:hover {background:rgba(251,239,222,.56)}
.master-vault-v4-modal .vault-v252-custom-input {margin:4px 0 0;flex:0 1 320px}
@media(max-width:820px) {
    .master-vault-v4-modal .vault-v253-enchant-detail {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v253-enchant-hint {grid-column:1}
}
@media(max-width:510px) {
    .master-vault-v4-modal .vault-v253-enchant-ability {margin:9px 10px 3px;padding:11px}
    .master-vault-v4-modal .vault-v253-enchant-heading {gap:8px;flex-wrap:wrap}
    .master-vault-v4-modal .vault-v253-enchant-emblem {flex-basis:37px;width:37px;height:37px}
    .master-vault-v4-modal .vault-v253-enchant-toggle {margin-left:auto}
    .master-vault-v4-modal .vault-v252-dice-selectors {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v253-count-field {flex:1 1 72px}
}


/* ================================================================
   V66.2.54 — atributos após maestrias, rolagem ágil e habilidades arejadas.
   ================================================================ */
.master-vault-v4-modal .vault-v254-attributes {padding:16px 17px;background:linear-gradient(120deg,rgba(255,253,248,.88),rgba(247,239,229,.55))}
.master-vault-v4-modal .vault-v254-ability-row {gap:9px;margin-top:15px}
.master-vault-v4-modal .vault-v254-ability-chip {min-height:39px;padding:7px 12px;gap:8px}
.master-vault-v4-modal .vault-v254-ability-index {display:grid;place-items:center;width:21px;height:21px;border-radius:7px;background:#d7ae6d;border:1px solid #c28e50;color:#57321e;font-size:11px;font-weight:900}
.master-vault-v4-modal .vault-v254-ability-chip.is-locked {box-shadow:inset 0 0 0 1px rgba(203,153,80,.24)}
.master-vault-v4-modal .vault-v254-custom-ability {margin-top:12px;max-width:300px}
.master-vault-v4-modal .vault-v254-ability-foot {display:flex;align-items:center;gap:9px;margin-top:14px;padding:9px 12px;border-radius:9px;background:rgba(232,219,196,.28);border:1px solid rgba(200,169,130,.3)}
.master-vault-v4-modal .vault-v254-ability-foot p {margin:0;color:#765744;font-size:10.5px;line-height:1.5}
.master-vault-v4-modal .vault-v254-ability-mark {flex:none;display:grid;place-items:center;width:20px;height:20px;border-radius:6px;background:#e1c28a;color:#573a22;font-size:11px;font-weight:900}
.master-vault-v4-modal .vault-v254-finesse-hint {margin-top:8px}
.master-vault-v4-modal .vault-v254-damage .vault-v251-damage-list {gap:14px}
.master-vault-v4-modal .vault-v254-damage-line {padding:15px 16px;border-radius:13px;background:linear-gradient(120deg,rgba(255,253,250,.87),rgba(250,245,238,.6))}
.master-vault-v4-modal .vault-v254-damage-line .vault-v251-damage-line-head {gap:10px;padding-bottom:11px;margin-bottom:13px;border-bottom:1px solid rgba(173,128,81,.18)}
.master-vault-v4-modal .vault-v254-damage-content {display:grid;grid-template-columns:minmax(250px,.78fr) minmax(0,1.5fr);align-items:start;gap:19px}
.master-vault-v4-modal .vault-v254-dice-panel {min-width:0}
.master-vault-v4-modal .vault-v254-dice-composer {display:flex;align-items:center;gap:8px;margin-top:10px}
.master-vault-v4-modal .vault-v254-stepper {display:flex;align-items:center;border:1px solid #cdb497;background:#fffefa;border-radius:10px;min-width:0;height:44px;overflow:hidden;box-shadow:0 2px 5px rgba(70,33,12,.035)}
.master-vault-v4-modal .vault-v254-stepper button {border:0;background:#f6eee2;width:32px;min-width:30px;height:100%;cursor:pointer;color:#925d32;font-size:23px;line-height:1;transition:background .15s}
.master-vault-v4-modal .vault-v254-stepper button:hover:not(:disabled) {background:#ead7bc;color:#623b21}
.master-vault-v4-modal .vault-v254-stepper button:disabled {opacity:.35;cursor:default}
.master-vault-v4-modal .vault-v254-stepper input {width:40px;min-width:35px;height:42px;border:0;outline:0;background:transparent;text-align:center;color:#61382a;font:800 15px Georgia,serif;font-variant-numeric:tabular-nums;padding:0 2px}
.master-vault-v4-modal .vault-v254-stepper:focus-within {border-color:#b08042;box-shadow:0 0 0 3px rgba(189,144,74,.12)}
.master-vault-v4-modal .vault-v254-dice-d {color:#915b39;font:800 19px Georgia,serif}
.master-vault-v4-modal .vault-v254-dice-help {display:block;margin-top:9px;color:#9b7d65;font-size:9.5px}
.master-vault-v4-modal .vault-v254-type-panel {min-width:0}
.master-vault-v4-modal .vault-v254-type-panel>span {font-size:10px;font-weight:800;color:#754d3e}
.master-vault-v4-modal .vault-v254-type-panel .vault-v251-damage-type-list {margin-top:9px;gap:6px}
.master-vault-v4-modal .vault-v254-custom-chip {border-style:dashed;background:rgba(242,229,208,.28)}
.master-vault-v4-modal .vault-v254-custom-type-input {margin-top:12px;max-width:350px}
.master-vault-v4-modal .vault-v254-modifier-row {display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:15px;padding-top:13px;border-top:1px solid rgba(175,129,79,.14)}
.master-vault-v4-modal .vault-v254-ability-modifier {display:flex;align-items:center;gap:9px;padding:5px 10px 5px 6px;border:1px solid rgba(176,145,110,.38);border-radius:9px;background:rgba(255,254,251,.7);color:#836a59;cursor:pointer;text-align:left}
.master-vault-v4-modal .vault-v254-ability-modifier.is-on {background:#faf2e4;border-color:#c79b60;color:#6e4025}
.master-vault-v4-modal .vault-v254-ability-modifier>span:last-child {display:flex;flex-direction:column;gap:3px}
.master-vault-v4-modal .vault-v254-ability-modifier strong {font-size:10px}
.master-vault-v4-modal .vault-v254-ability-modifier small {font-size:9px;color:#947a65}
.master-vault-v4-modal .vault-v254-mod-check {display:grid;place-items:center;width:23px;height:23px;border-radius:7px;background:#ece6dc;color:transparent;border:1px solid #c8b9a5;font-size:13px}
.master-vault-v4-modal .vault-v254-ability-modifier.is-on .vault-v254-mod-check {background:#c99552;color:#fff;border-color:#bf873d}
.master-vault-v4-modal .vault-v254-extra-toggle {border:1px dashed #d8b992;border-radius:8px;background:rgba(255,250,242,.5);padding:9px 12px;cursor:pointer;font-size:10px;font-weight:800;color:#865b37;display:inline-flex;gap:7px}
.master-vault-v4-modal .vault-v254-extra-toggle:hover {background:#f8eedc}
.master-vault-v4-modal .vault-v254-extra-toggle small {font-weight:800;color:#9b5a2c}
.master-vault-v4-modal .vault-v254-bonus-detail {display:flex;align-items:end;gap:12px;flex-wrap:wrap;background:rgba(250,240,225,.42);border:1px solid rgba(191,153,110,.32);border-radius:10px;margin-top:11px;padding:12px}
.master-vault-v4-modal .vault-v254-bonus-number {flex:0 0 120px}
.master-vault-v4-modal .vault-v254-bonus-condition {flex:1 1 270px}
.master-vault-v4-modal .vault-v254-bonus-detail .master-vault-v4-input {height:40px}
.master-vault-v4-modal .vault-v254-feature-heading {margin:18px 18px 4px;padding:14px 16px 13px;display:flex;justify-content:space-between;align-items:center;gap:14px;border-bottom:1px solid rgba(177,140,99,.31)}
.master-vault-v4-modal .vault-v254-feature-heading>div {display:flex;flex-direction:column;gap:4px}
.master-vault-v4-modal .vault-v254-feature-heading strong {font:700 15px Georgia,serif;color:#704233}
.master-vault-v4-modal .vault-v254-feature-heading span {font-size:10px;color:#947d6a}
.master-vault-v4-modal .vault-v254-feature-total {white-space:nowrap;border:1px solid rgba(189,147,103,.35);padding:5px 9px;border-radius:999px;background:#f7efdf;font-size:10px!important;color:#805739!important}
.master-vault-v4-modal .vault-v19-stage-group:not(.is-vestige) .master-vault-v5-abilities-body {display:grid;gap:12px;padding:10px 0 4px}
.master-vault-v4-modal .vault-v19-stage-groups {gap:15px;padding:8px 17px 22px}
.master-vault-v4-modal .master-vault-v5-ability-drawer {border-radius:13px!important;border:1px solid rgba(194,155,118,.43);background:rgba(255,253,249,.85);box-shadow:0 3px 10px rgba(83,44,28,.055);overflow:hidden}
.master-vault-v4-modal .master-vault-v5-ability-drawer.is-open {border-color:rgba(168,116,80,.56);box-shadow:0 7px 22px rgba(84,48,32,.07)}
.master-vault-v4-modal .master-vault-v5-ability-bar {min-height:62px;padding:9px 14px;gap:12px;grid-template-columns:36px minmax(0,1fr) auto;background:linear-gradient(100deg,rgba(247,240,229,.85),rgba(254,253,250,.7))}
.master-vault-v4-modal .master-vault-v5-ability-drawer.is-open .master-vault-v5-ability-bar {border-bottom:1px solid rgba(189,143,104,.27);background:linear-gradient(90deg,#f8eee1,#fffdf9)}
.master-vault-v4-modal .master-vault-v5-ability-number {width:33px;height:33px;border-radius:9px;font-size:12px}
.master-vault-v4-modal .master-vault-v5-ability-title strong {font-size:13px}
.master-vault-v4-modal .master-vault-v5-ability-title span {font-size:10px;line-height:1.5}
.master-vault-v4-modal .master-vault-v5-ability-tools {gap:4px}
.master-vault-v4-modal .master-vault-v5-ability-tool {width:29px;height:29px;padding:5px}
.master-vault-v4-modal .master-vault-v5-ability-content {padding:20px}
.master-vault-v4-modal .master-vault-v5-ability-content-grid {grid-template-columns:minmax(0,1fr) minmax(215px,.35fr);gap:20px}
.master-vault-v4-modal .master-vault-v5-ability-side {padding:12px;border:1px solid rgba(181,151,117,.28);border-radius:11px;background:linear-gradient(135deg,#faf5ed,#fffdfa)}
.master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-rich-field {margin-top:15px}
.master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-rich-canvas {min-height:115px}
.master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-rich-surface,
.master-vault-v4-modal .master-vault-v5-ability-content .vault-v31-fallback-input {min-height:112px!important}
@media(max-width:930px) {
    .master-vault-v4-modal .vault-v254-damage-content {grid-template-columns:1fr;gap:16px}
    .master-vault-v4-modal .master-vault-v5-ability-content-grid {grid-template-columns:1fr}
}
@media(max-width:590px) {
    .master-vault-v4-modal .vault-v254-feature-heading {flex-wrap:wrap;margin:10px 10px 4px}
    .master-vault-v4-modal .vault-v254-dice-composer {gap:4px}
    .master-vault-v4-modal .vault-v254-stepper button {min-width:27px;width:27px}
    .master-vault-v4-modal .vault-v254-stepper input {width:31px}
    .master-vault-v4-modal .vault-v254-damage-line {padding:12px}
    .master-vault-v4-modal .master-vault-v5-ability-content {padding:13px}
    .master-vault-v4-modal .master-vault-v5-ability-bar {gap:7px;padding:8px;grid-template-columns:32px minmax(0,1fr) auto}
}

/* ================================================================
   V66.2.55 — dano mais enxuto e capacidade das habilidades por origem.
   ================================================================ */
.master-vault-v4-modal .vault-v255-damage {padding:18px 17px 17px;background:linear-gradient(140deg,rgba(255,254,251,.92),rgba(251,245,236,.75))}
.master-vault-v4-modal .vault-v255-damage .vault-v250-damage-header {align-items:center;gap:14px;margin-bottom:17px}
.master-vault-v4-modal .vault-v255-damage .vault-v250-damage-summary {padding:10px 15px;text-align:center;background:linear-gradient(125deg,#faf0e1,#fffaf2);box-shadow:0 2px 10px rgba(123,82,47,.04)}
.master-vault-v4-modal .vault-v255-damage .vault-v254-damage-line {padding:17px 18px 18px;border:1px solid rgba(193,151,111,.46);background:linear-gradient(125deg,rgba(255,254,252,.96),rgba(250,245,237,.78));box-shadow:0 4px 15px rgba(93,52,28,.035)}
.master-vault-v4-modal .vault-v255-damage .vault-v251-damage-line-head {align-items:center}
.master-vault-v4-modal .vault-v255-damage .vault-v254-damage-content {grid-template-columns:minmax(280px,.86fr) minmax(0,1.65fr);align-items:stretch;gap:18px}
.master-vault-v4-modal .vault-v255-damage .vault-v254-dice-panel {min-width:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;min-height:132px;padding:16px 10px;border:1px solid rgba(204,169,127,.40);border-radius:13px;background:radial-gradient(ellipse at 50% 20%,rgba(252,239,214,.7),rgba(253,249,242,.64) 66%)}
.master-vault-v4-modal .vault-v255-damage .vault-v252-field-label {text-align:center;font-size:10px;font-weight:800;letter-spacing:.025em;color:#80543c}
.master-vault-v4-modal .vault-v255-damage .vault-v254-dice-composer {margin:0;justify-content:center;gap:13px}
.master-vault-v4-modal .vault-v255-damage .vault-v254-stepper {height:52px;border-radius:12px;background:#fffdfa;border-color:#d5b58c;box-shadow:0 2px 7px rgba(96,53,25,.06)}
.master-vault-v4-modal .vault-v255-damage .vault-v254-stepper button {min-width:37px;width:37px;font-size:27px;background:#f8eddb}
.master-vault-v4-modal .vault-v255-damage .vault-v254-stepper input {height:50px;width:48px;font:800 20px/1 Georgia,serif}
.master-vault-v4-modal .vault-v255-damage .vault-v254-dice-d {color:#905222;font:800 34px/1 Georgia,serif;transform:translateY(-1px)}
.master-vault-v4-modal .vault-v255-damage .vault-v254-type-panel {display:flex;flex-direction:column;justify-content:center;padding:5px 0}
.master-vault-v4-modal .vault-v255-damage .vault-v254-type-panel .vault-v251-damage-type-list {gap:7px;align-content:center}
.master-vault-v4-modal .vault-v255-damage .vault-v251-type-chip {min-height:29px;padding:6px 10px;border-radius:8px}
.master-vault-v4-modal .vault-v255-damage .vault-v254-modifier-row {gap:10px;padding-top:16px;margin-top:17px}
.master-vault-v4-modal .vault-v255-damage .vault-v254-ability-modifier {min-height:37px;padding:6px 12px 6px 6px;transition:background .16s,border-color .16s}
.master-vault-v4-modal .vault-v255-damage .vault-v254-ability-modifier strong {font-size:11px}
.master-vault-v4-modal .vault-v255-damage .vault-v254-ability-modifier:disabled {opacity:.54;cursor:not-allowed}
.master-vault-v4-modal .vault-v255-damage .vault-v254-extra-toggle {min-height:37px;align-items:center}
.master-vault-v4-modal .vault-v254-ability-chip:disabled {opacity:.55;cursor:not-allowed}
.master-vault-v4-modal .vault-v254-ability-chip.is-selected:disabled {opacity:1;cursor:default}

.master-vault-v4-modal .vault-v255-counter-mode {gap:7px;margin-top:15px}
.master-vault-v4-modal .vault-v255-usage-max {display:block;margin-top:15px;padding:12px;border-radius:10px;border:1px solid rgba(195,150,98,.38);background:#fffaf0}
.master-vault-v4-modal .vault-v255-usage-max > span {display:block;margin-bottom:7px;font-size:10px;font-weight:800;color:#70402b}
.master-vault-v4-modal .vault-v255-usage-max input {height:45px;font:800 20px Georgia,serif;text-align:center;background:#fffefd}
.master-vault-v4-modal .vault-v255-usage-start {margin-top:15px}
.master-vault-v4-modal .vault-v255-usage-label {display:block;margin:0 0 9px;font-weight:800;font-size:10px;color:#744b3a}
.master-vault-v4-modal .vault-v255-start-choices {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px}
.master-vault-v4-modal .vault-v255-start-choices button,.master-vault-v4-modal .vault-v255-roll-trigger button {min-width:0;padding:10px 5px;border:1px solid rgba(197,167,138,.72);border-radius:8px;background:rgba(255,253,249,.92);color:#80644f;font-size:10px;font-weight:800;cursor:pointer;transition:background .15s,border-color .15s}
.master-vault-v4-modal .vault-v255-start-choices button:hover,.master-vault-v4-modal .vault-v255-roll-trigger button:hover {background:#f5e9d7}
.master-vault-v4-modal .vault-v255-start-choices button.is-active,.master-vault-v4-modal .vault-v255-roll-trigger button.is-active {border-color:#b97c4f;background:#f4e4cc;color:#682e20;box-shadow:inset 0 0 0 1px rgba(184,126,73,.13)}
.master-vault-v4-modal .vault-v255-usage-fixed {display:block;margin-top:11px}
.master-vault-v4-modal .vault-v255-usage-fixed input {margin-top:7px}
.master-vault-v4-modal .vault-v255-usage-roll {margin-top:12px;padding:12px;border:1px solid rgba(192,155,113,.32);border-radius:10px;background:rgba(255,253,247,.82)}
.master-vault-v4-modal .vault-v255-usage-roll > label {display:block}
.master-vault-v4-modal .vault-v255-usage-roll input {margin-top:6px;text-align:center;font-size:16px;font-weight:800;letter-spacing:.025em}
.master-vault-v4-modal .vault-v255-roll-feedback {display:block;margin-top:6px;color:#aa4231;font-size:10px}
.master-vault-v4-modal .vault-v255-roll-trigger {display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:11px}
.master-vault-v4-modal .vault-v255-roll-preview-row {display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:11px}
.master-vault-v4-modal .vault-v255-roll-preview-row span {font:800 15px Georgia,serif;color:#80481e}
.master-vault-v4-modal .vault-v255-roll-preview-button {min-height:34px;padding:7px 11px;border:1px dashed #c59c69;border-radius:8px;background:#fdf6ea;color:#76422f;font-weight:800;font-size:10px;cursor:pointer}
.master-vault-v4-modal .vault-v255-roll-preview-button:disabled {opacity:.5;cursor:not-allowed}
.master-vault-v4-modal .vault-v255-usage-recovery {display:block;margin-top:15px}
.master-vault-v4-modal .vault-v255-usage-recovery > span {display:block;margin-bottom:8px}
.master-vault-v4-modal .vault-v255-single-use-note {font-size:10px;line-height:1.55;margin-top:9px}
@media (max-width:1000px) {
    .master-vault-v4-modal .vault-v255-damage .vault-v254-damage-content {grid-template-columns:minmax(220px,.85fr) minmax(0,1fr)}
}
@media (max-width:800px) {
    .master-vault-v4-modal .vault-v255-damage .vault-v254-damage-content {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-dice-panel {min-height:118px}
}
@media (max-width:510px) {
    .master-vault-v4-modal .vault-v255-damage {padding:11px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-damage-line {padding:12px 10px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-dice-composer {gap:7px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-stepper button {width:29px;min-width:29px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-stepper input {width:35px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-dice-d {font-size:30px}
}

/* ================================================================
   V66.2.56 — rastreador compacto: modos distintos e ajuda contextual.
   ================================================================ */
.master-vault-v4-modal .vault-v256-ability-side {
    min-width:0;padding:13px;border:1px solid rgba(183,143,103,.37);border-radius:13px;
    background:linear-gradient(155deg,rgba(252,246,236,.92),rgba(255,253,249,.98) 65%);
    box-shadow:0 5px 18px rgba(95,57,32,.045);
}
.master-vault-v4-modal .vault-v256-tracker-head {
    padding:10px 11px;min-height:47px;border:1px solid rgba(195,151,105,.33);
    border-radius:10px;background:rgba(255,255,254,.72);
}
.master-vault-v4-modal .vault-v256-tracker-head strong {font-size:11px;letter-spacing:.01em;color:#642c21}
.master-vault-v4-modal .vault-v256-tracker-head small {font-size:9px;color:#9a785d}
.master-vault-v4-modal .vault-v256-tracker {min-width:0;padding-top:12px}
.master-vault-v4-modal .vault-v256-mode-select {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
.master-vault-v4-modal .vault-v256-mode-select button {
    display:flex;justify-content:center;align-items:center;gap:8px;min-width:0;min-height:52px;
    padding:8px;border:1px solid rgba(191,158,123,.47);border-radius:10px;
    background:linear-gradient(160deg,#fffdfa,#fcf8f2);color:#86614b;cursor:pointer;
    transition:border-color .18s,background .18s,box-shadow .18s,transform .18s;
}
.master-vault-v4-modal .vault-v256-mode-select button:hover:not(:disabled) {border-color:#c28c5d;background:#fcf1e2}
.master-vault-v4-modal .vault-v256-mode-select button.is-active {
    border-color:#a86a4b;background:linear-gradient(130deg,#f7e8d5,#fff5e9);
    box-shadow:inset 0 0 0 1px rgba(168,106,75,.14),0 2px 7px rgba(140,85,37,.06);color:#682d20;
}
.master-vault-v4-modal .vault-v256-mode-select button:disabled {opacity:.45;cursor:not-allowed}
.master-vault-v4-modal .vault-v256-mode-mark {
    display:grid;place-items:center;flex:none;width:27px;height:27px;border-radius:8px;
    border:1px solid rgba(185,148,111,.55);background:#fffaf3;color:#a07545;
    font:700 20px/1 Georgia,serif;
}
.master-vault-v4-modal .vault-v256-mode-select button.is-active .vault-v256-mode-mark {background:#e9cb9c;color:#6f391e;border-color:#cf9a5b}
.master-vault-v4-modal .vault-v256-mode-select button span:last-child {display:flex;flex-direction:column;align-items:start;gap:3px;text-align:left}
.master-vault-v4-modal .vault-v256-mode-select strong {font-size:11px;line-height:1.1;font-weight:800}
.master-vault-v4-modal .vault-v256-mode-select small {font-size:9px;line-height:1.1;font-weight:500;white-space:nowrap;color:#a17e67}
.master-vault-v4-modal .vault-v256-config-grid {display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:8px;margin-top:11px}
.master-vault-v4-modal .vault-v256-config-field {
    display:flex;flex-direction:column;justify-content:space-between;min-width:0;gap:8px;
    padding:10px;background:rgba(255,251,244,.92);
    border:1px solid rgba(201,167,128,.39);border-radius:10px;
}
.master-vault-v4-modal .vault-v256-config-field > span {font-size:10px;font-weight:800;color:#70402b;line-height:1.3}
.master-vault-v4-modal .vault-v256-config-field input,
.master-vault-v4-modal .vault-v256-config-field select {
    width:100%;min-width:0;margin:0;height:40px;padding:6px 9px;
    border:1px solid rgba(194,156,119,.64);border-radius:8px;background:#fffefd;
    color:#652f22;
}
.master-vault-v4-modal .vault-v256-capacity input {font:800 21px/1 Georgia,serif;text-align:center;font-variant-numeric:tabular-nums}
.master-vault-v4-modal .vault-v256-recovery select {font-size:11px;font-weight:700;cursor:pointer}
.master-vault-v4-modal .vault-v256-custom-recovery {display:block;margin-top:9px}
.master-vault-v4-modal .vault-v256-custom-recovery > span {font-size:10px;font-weight:800}
.master-vault-v4-modal .vault-v256-custom-recovery input {margin-top:7px}
.master-vault-v4-modal .vault-v256-single-note {margin:9px 0 0;color:#9b6349}
.master-vault-v4-modal .vault-v256-spend,
.master-vault-v4-modal .vault-v256-build {
    margin-top:12px;padding:12px;border-radius:11px;
    border:1px solid rgba(197,158,118,.34);background:linear-gradient(155deg,rgba(255,255,254,.88),rgba(251,244,234,.65));
}
.master-vault-v4-modal .vault-v256-section-top,
.master-vault-v4-modal .vault-v256-build-header {display:flex;align-items:center;justify-content:space-between;gap:10px}
.master-vault-v4-modal .vault-v256-section-name {font-size:11px;font-weight:800;color:#733b29}
.master-vault-v4-modal .vault-v256-help {position:relative;z-index:12;flex:none}
.master-vault-v4-modal .vault-v256-help summary {
    display:grid;place-items:center;width:21px;height:21px;border:1px solid #d2ae80;
    border-radius:50%;background:linear-gradient(145deg,#fff7e8,#f6e8d1);
    color:#98683a;font:800 12px/1 Georgia,serif;cursor:pointer;list-style:none;
}
.master-vault-v4-modal .vault-v256-help summary::-webkit-details-marker {display:none}
.master-vault-v4-modal .vault-v256-help[open] summary {background:#deb985;color:#6b3424;border-color:#b87c4f}
.master-vault-v4-modal .vault-v256-help-content {
    position:absolute;right:0;top:calc(100% + 9px);width:min(285px,80vw);
    padding:13px 14px;border:1px solid rgba(177,129,87,.57);border-radius:10px;
    background:#fffaf2;box-shadow:0 12px 28px rgba(75,43,22,.19);color:#704634;
}
.master-vault-v4-modal .vault-v256-help-content::before {
    content:"";position:absolute;right:5px;top:-6px;width:10px;height:10px;
    transform:rotate(45deg);border-top:1px solid rgba(177,129,87,.57);border-left:1px solid rgba(177,129,87,.57);background:#fffaf2;
}
.master-vault-v4-modal .vault-v256-help-content strong {display:block;margin-bottom:6px;font-size:11px;color:#692d20}
.master-vault-v4-modal .vault-v256-help-content p {margin:0 0 7px;font-size:10px;line-height:1.6;color:#83634d}
.master-vault-v4-modal .vault-v256-help-content p:last-child {margin-bottom:0}
.master-vault-v4-modal .vault-v256-start-choices {margin-top:10px;gap:6px}
.master-vault-v4-modal .vault-v256-start-choices button {padding:10px 4px;min-height:39px;font-size:10px}
.master-vault-v4-modal .vault-v256-complete,
.master-vault-v4-modal .vault-v256-fixed,
.master-vault-v4-modal .vault-v256-roll {margin-top:11px}
.master-vault-v4-modal .vault-v256-complete {
    display:flex;align-items:center;gap:8px;padding:8px 10px;border:1px solid rgba(188,154,118,.30);
    border-radius:8px;background:#fff9ed;color:#83634f;font-size:10px;
}
.master-vault-v4-modal .vault-v256-complete > span:first-child {display:grid;place-items:center;width:18px;height:18px;border-radius:50%;background:#dfbf90;color:#6d3c26;font-weight:800}
.master-vault-v4-modal .vault-v256-complete strong {color:#633124}
.master-vault-v4-modal .vault-v256-fixed {display:block}
.master-vault-v4-modal .vault-v256-fixed > span {font-size:10px;font-weight:800}
.master-vault-v4-modal .vault-v256-fixed input {height:41px;margin-top:7px;text-align:center;font:800 17px/1 Georgia,serif}
.master-vault-v4-modal .vault-v256-roll {padding:11px;border:1px solid rgba(201,168,128,.32);border-radius:9px;background:#fffaf4}
.master-vault-v4-modal .vault-v256-roll-row {display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:end;gap:8px}
.master-vault-v4-modal .vault-v256-roll-row label {display:block;min-width:0}
.master-vault-v4-modal .vault-v256-roll-row label > span {display:block;font-size:10px;font-weight:800;color:#72402d;margin-bottom:7px}
.master-vault-v4-modal .vault-v256-roll-row input {min-width:0;width:100%;height:41px;padding:7px;text-align:center;font:800 17px/1 Georgia,serif;letter-spacing:.015em}
.master-vault-v4-modal .vault-v256-roll-test {
    display:flex;align-items:center;justify-content:center;gap:6px;min-height:41px;padding:6px 11px;
    border:1px solid rgba(177,124,78,.59);border-radius:8px;background:#f6e7d4;
    color:#754127;font-size:10px;font-weight:800;cursor:pointer;
}
.master-vault-v4-modal .vault-v256-roll-test span {font-size:16px;line-height:1}
.master-vault-v4-modal .vault-v256-roll-test:hover:not(:disabled) {background:#edd5b5}
.master-vault-v4-modal .vault-v256-roll-test:disabled {opacity:.45;cursor:not-allowed}
.master-vault-v4-modal .vault-v256-roll-result {display:block;margin-top:8px;font-size:10px;color:#78452c}
.master-vault-v4-modal .vault-v256-roll-result strong {font-size:12px;color:#653024}
.master-vault-v4-modal .vault-v256-roll-description {margin:9px 0 0;font-size:9px;line-height:1.5;color:#a07a5b}
.master-vault-v4-modal .vault-v256-build {background:linear-gradient(145deg,#faf4e9,#fffdf8)}
.master-vault-v4-modal .vault-v256-build-header strong {font:800 19px/1 Georgia,serif;color:#804e2d;font-variant-numeric:tabular-nums}
.master-vault-v4-modal .vault-v256-build-header strong span {font:700 9px/1 Arial,sans-serif;color:#997e67}
.master-vault-v4-modal .vault-v256-progress {height:8px;margin:13px 0 9px;border-radius:99px;background:#e9daca;box-shadow:inset 0 1px 3px rgba(117,73,40,.12);overflow:hidden}
.master-vault-v4-modal .vault-v256-progress span {display:block;width:0;height:100%;background:#b9834f}
.master-vault-v4-modal .vault-v256-build p {margin:0;color:#9a785f;font-size:10px;line-height:1.55}
.master-vault-v4-modal .vault-v256-ability-side :is(button,summary,input,select):focus-visible {outline:2px solid #a16f43;outline-offset:2px}
@media (max-width:420px) {
    .master-vault-v4-modal .vault-v256-ability-side {padding:9px}
    .master-vault-v4-modal .vault-v256-config-grid {grid-template-columns:minmax(0,1fr)}
}


/* ================================================================
   V66.2.57 — recuperação opcional no Acúmulo + disposição refinada.
   ================================================================ */
.master-vault-v4-modal .vault-v256-ability-side {
    padding:12px;border-radius:14px;border-color:rgba(174,132,94,.4);
    background:linear-gradient(153deg,#fcf7f0 0%,#fffdfa 64%,#faf3e9 100%);
    box-shadow:0 3px 13px rgba(98,59,39,.045),inset 0 1px 0 rgba(255,255,255,.9);
}
.master-vault-v4-modal .vault-v256-tracker-head {padding:11px 12px;border-radius:11px;background:#fffdfa}
.master-vault-v4-modal .vault-v256-tracker-head strong {font-size:12px}
.master-vault-v4-modal .vault-v256-tracker-head small {margin-top:4px;font-size:9.5px}
.master-vault-v4-modal .vault-v256-mode-select {gap:7px}
.master-vault-v4-modal .vault-v256-mode-select button {min-height:58px;border-radius:10px;padding:9px 7px}
.master-vault-v4-modal .vault-v256-mode-mark {width:28px;height:28px;border-radius:8px}
.master-vault-v4-modal .vault-v256-mode-select button.is-active {box-shadow:inset 0 0 0 1px rgba(174,109,66,.12),0 3px 9px rgba(132,81,46,.065)}
.master-vault-v4-modal .vault-v257-config-grid {gap:8px;margin-top:11px;align-items:stretch}
.master-vault-v4-modal .vault-v257-config-grid .vault-v256-config-field {padding:11px;gap:8px;background:#fffaf3}
.master-vault-v4-modal .vault-v257-config-grid .vault-v256-config-field > span {font-size:10px;letter-spacing:.005em}
.master-vault-v4-modal .vault-v257-config-grid .vault-v256-config-field input,
.master-vault-v4-modal .vault-v257-config-grid .vault-v256-config-field select {height:42px}
.master-vault-v4-modal .vault-v257-build-recovery {
    display:flex;flex-direction:column;justify-content:center;min-width:0;gap:9px;
    padding:10px 9px;border:1px solid rgba(190,151,114,.38);
    border-radius:10px;background:linear-gradient(150deg,#fffdfa,#faf5ed);
    transition:border-color .16s,background .16s;
}
.master-vault-v4-modal .vault-v257-build-recovery.is-on {border-color:rgba(177,124,79,.66);background:#fff8ec}
.master-vault-v4-modal .vault-v257-recovery-row {display:flex;align-items:center;justify-content:space-between;gap:5px;min-height:34px}
.master-vault-v4-modal .vault-v257-recovery-copy {display:flex;flex-direction:column;gap:5px;min-width:0}
.master-vault-v4-modal .vault-v257-recovery-copy strong {color:#71412f;font-size:10px;font-weight:800;line-height:1.2}
.master-vault-v4-modal .vault-v257-recovery-copy small {color:#997b61;font-size:9px;line-height:1.15}
.master-vault-v4-modal .vault-v257-recovery-switch {flex:none;transform:scale(.84);transform-origin:right center}
.master-vault-v4-modal .vault-v257-build-recovery-options select {
    width:100%;height:36px;padding:5px 8px;min-width:0;border-radius:8px;
    border:1px solid rgba(186,142,101,.55);background:#fffdf8;color:#723d29;
    font-size:10px;font-weight:700;
}
.master-vault-v4-modal .vault-v257-custom-recovery {padding:11px 12px;margin-top:9px;border:1px solid rgba(195,158,118,.33);border-radius:10px;background:#fffaf2}
.master-vault-v4-modal .vault-v257-custom-recovery input {height:39px}
.master-vault-v4-modal .vault-v256-spend,
.master-vault-v4-modal .vault-v256-build {margin-top:11px;padding:12px;border-radius:11px;background:rgba(255,252,247,.8);border-color:rgba(191,147,109,.35)}
.master-vault-v4-modal .vault-v256-build {background:linear-gradient(155deg,#fbf2e5,#fffdfa)}
.master-vault-v4-modal .vault-v256-build-header strong {display:inline-flex;align-items:baseline;gap:4px;font-size:21px}
.master-vault-v4-modal .vault-v256-build-header strong span {font:700 11px/1 Georgia,serif;color:#9c785c}
.master-vault-v4-modal .vault-v256-progress {margin:11px 0 9px;height:7px;background:#e8d7c3}
.master-vault-v4-modal .vault-v256-build p {font-size:9.5px;line-height:1.5}
.master-vault-v4-modal .vault-v256-start-choices {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:7px;align-items:stretch;margin-top:12px}
.master-vault-v4-modal .vault-v256-start-choices > button,
.master-vault-v4-modal .vault-v257-random-option {height:42px;min-height:42px;border-radius:9px}
.master-vault-v4-modal .vault-v256-start-choices > button {padding:9px 6px}
.master-vault-v4-modal .vault-v257-random-option {
    position:relative;display:flex;align-items:stretch;min-width:0;
    border:1px solid rgba(193,156,119,.64);background:#fffdfa;
    transition:border-color .15s,background .15s,box-shadow .15s;
}
.master-vault-v4-modal .vault-v257-random-option:hover {border-color:#ba875f;background:#fff5e8}
.master-vault-v4-modal .vault-v257-random-option.is-active {border-color:#b67b4c;background:linear-gradient(135deg,#f5dfc4,#f7e6d0);box-shadow:inset 0 0 0 1px rgba(165,106,61,.14)}
.master-vault-v4-modal .vault-v257-random-option .vault-v257-random-select {
    flex:1;min-width:0;height:100%;padding:6px 25px 6px 7px;border:0;border-radius:8px;
    background:transparent;color:#79513b;font-size:10px;font-weight:700;cursor:pointer;
}
.master-vault-v4-modal .vault-v257-random-option.is-active .vault-v257-random-select {color:#642e20;font-weight:800}
.master-vault-v4-modal .vault-v257-random-help {position:absolute;right:5px;top:50%;transform:translateY(-50%);z-index:15}
.master-vault-v4-modal .vault-v257-random-help summary {width:18px;height:18px;font-size:10px;border-color:#d7b68a;background:#fff8e9;box-shadow:0 1px 3px rgba(89,52,27,.04)}
.master-vault-v4-modal .vault-v257-random-help .vault-v256-help-content {right:-5px;top:calc(100% + 11px);width:min(295px,78vw);z-index:20}
.master-vault-v4-modal .vault-v256-ability-side :is(.vault-v257-random-select, .vault-v257-recovery-switch, .vault-v257-random-help summary):focus-visible {outline:2px solid #9a693d;outline-offset:2px}
/* Habilidades — ritmo visual leve e gavetas com áreas clicáveis mais claras */
.master-vault-v4-modal .vault-v254-feature-heading {margin:18px 17px 5px;padding:14px 16px;border-radius:10px;border:1px solid rgba(193,150,113,.29);background:linear-gradient(105deg,rgba(247,238,224,.65),rgba(255,252,247,.8));}
.master-vault-v4-modal .vault-v254-feature-heading strong {font-size:16px}
.master-vault-v4-modal .vault-v254-feature-heading>div {gap:5px}
.master-vault-v4-modal .vault-v254-feature-total {background:#fff8ec;padding:6px 10px}
.master-vault-v4-modal .vault-v19-stage-groups {padding:12px 17px 24px}
.master-vault-v4-modal .master-vault-v5-ability-drawer {border-color:rgba(188,145,103,.42);box-shadow:0 3px 11px rgba(83,44,28,.045)}
.master-vault-v4-modal .master-vault-v5-ability-drawer.is-open {box-shadow:0 8px 23px rgba(83,44,28,.075)}
.master-vault-v4-modal .master-vault-v5-ability-bar {transition:background .18s}
.master-vault-v4-modal .master-vault-v5-ability-drawer:not(.is-open) .master-vault-v5-ability-bar:hover {background:linear-gradient(100deg,#f5e9d9,#fffdfa)}
.master-vault-v4-modal .master-vault-v5-ability-content-grid {gap:22px}
@media (max-width:930px) {
    .master-vault-v4-modal .master-vault-v5-ability-content-grid {gap:15px}
}
@media (max-width:420px) {
    .master-vault-v4-modal .vault-v257-config-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
    .master-vault-v4-modal .vault-v257-config-grid .vault-v256-config-field {padding:8px}
    .master-vault-v4-modal .vault-v257-build-recovery {padding:9px 7px}
    .master-vault-v4-modal .vault-v257-recovery-copy strong {font-size:9px}
    .master-vault-v4-modal .vault-v257-random-option .vault-v257-random-select {font-size:9px;padding-left:3px}
    .master-vault-v4-modal .vault-v256-start-choices {gap:5px}
}

/* ================================================================
   SPELLBOUND V66.2.58 — dano legível, atributos cumulativos e oficina
   de cálculo do Mestre; habilidades sem faixa redundante.
   ================================================================ */
.master-vault-v4-modal .vault-v254-feature-heading {display:none!important}
.master-vault-v4-modal .master-vault-v5-abilities-head {padding-bottom:13px!important}
.master-vault-v4-modal .vault-v19-stage-groups {padding-top:10px!important;gap:10px}
.master-vault-v4-modal .vault-v19-stage-group:not(.is-vestige) .master-vault-v5-abilities-body {padding-top:2px;gap:9px}
.master-vault-v4-modal .vault-v253-enchant-ability {margin-bottom:0!important}
.master-vault-v4-modal .vault-v253-enchant-title>small {font-size:10px;line-height:1.35}
.master-vault-v4-modal .vault-v253-enchant-detail {gap:16px}
.master-vault-v4-modal .vault-v258-enchant-help {position:relative;flex:none;align-self:center}
.master-vault-v4-modal .vault-v258-enchant-help summary {
    list-style:none;cursor:pointer;display:grid;place-items:center;width:23px;height:23px;
    border-radius:50%;border:1px solid #d6b78e;background:#fffbf4;
    color:#93643d;font:800 11px/1 Arial,sans-serif;transition:background .2s,border .2s
}
.master-vault-v4-modal .vault-v258-enchant-help summary::-webkit-details-marker {display:none}
.master-vault-v4-modal .vault-v258-enchant-help summary:hover {background:#f4e4cb;border-color:#b7864c}
.master-vault-v4-modal .vault-v258-enchant-help-content {
    position:absolute;right:-40px;top:calc(100% + 12px);z-index:50;width:min(315px,75vw);
    padding:13px 15px;border-radius:11px;border:1px solid #d4b690;background:#fffaf0;
    color:#694731;box-shadow:0 12px 28px rgba(68,38,17,.16);
    font:500 11px/1.6 Arial,sans-serif
}
.master-vault-v4-modal .vault-v258-enchant-edit-prompt {
    display:flex;align-items:center;justify-content:space-between;gap:16px;
    min-width:0;padding:10px 13px;border:1px solid rgba(183,137,74,.28);
    border-radius:10px;background:rgba(255,250,240,.7)
}
.master-vault-v4-modal .vault-v258-enchant-edit-prompt>div {display:flex;flex-direction:column;gap:4px;min-width:0}
.master-vault-v4-modal .vault-v258-enchant-edit-prompt strong {font:700 14px/1.3 Georgia,serif;color:#784723}
.master-vault-v4-modal .vault-v258-enchant-edit-prompt small {font:500 10px/1.3 Arial,sans-serif;color:#967456}
.master-vault-v4-modal .vault-v258-enchant-edit-prompt button {
    flex:none;display:flex;align-items:center;gap:8px;border:1px solid #d4ab78;
    border-radius:8px;padding:8px 11px;background:#fff9ed;color:#7c4e2b;
    font:750 10px/1.2 Arial,sans-serif;cursor:pointer
}
.master-vault-v4-modal .vault-v258-enchant-edit-prompt button:hover {background:#f2dfc5}
.master-vault-v4-modal .master-vault-v5-ability-drawer.is-weapon-enchantment {border-color:#d0ab71;background:linear-gradient(105deg,#fffaf1,#fffdfa)}
.master-vault-v4-modal .master-vault-v5-ability-drawer.is-weapon-enchantment .master-vault-v5-ability-number {background:#f1ddb9;color:#8b5d26}
.master-vault-v4-modal .master-vault-v5-ability-drawer.is-weapon-enchantment .master-vault-v5-ability-content-grid {grid-template-columns:minmax(0,1fr)!important}
.master-vault-v4-modal .master-vault-v5-ability-drawer.is-weapon-enchantment .master-vault-v5-ability-content {padding:20px 24px}
.master-vault-v4-modal .vault-v258-damage-actions {display:flex;align-items:center;justify-content:flex-end;gap:10px;max-width:min(740px,70%);min-width:0}
.master-vault-v4-modal .vault-v258-damage-summary {
    min-width:0!important;max-width:min(380px,100%)!important;flex:1 1 210px!important;
    padding:9px 14px!important;text-align:left!important;border-color:#e0c7a6!important
}
.master-vault-v4-modal .vault-v258-damage-summary strong {
    overflow:hidden!important;white-space:nowrap;text-overflow:ellipsis;display:block;
    font:700 15px/1.3 Georgia,serif!important
}
.master-vault-v4-modal .vault-v258-modifier-row {margin-top:12px;padding-top:12px;gap:8px}
.master-vault-v4-modal .vault-v258-modifier-trigger {
    display:flex;align-items:center;gap:9px;min-height:39px;min-width:0;max-width:100%;
    padding:5px 12px 5px 6px;border:1px solid #d9b98e;border-radius:9px;
    color:#75503a;background:#fffaf4;font:750 11px/1.2 Arial,sans-serif;
    cursor:pointer;transition:background .16s,border .16s
}
.master-vault-v4-modal .vault-v258-modifier-trigger:hover {background:#f9eddb;border-color:#be8a4e}
.master-vault-v4-modal .vault-v258-modifier-trigger.is-on {border-color:#c89962;background:#faeeda;color:#744125}
.master-vault-v4-modal .vault-v258-modifier-symbol {
    width:26px;height:26px;flex:none;display:grid;place-items:center;
    border:1px solid #dec9a9;border-radius:7px;background:#fffaf1;
    color:#9b6531;font-size:17px;font-weight:500
}
.master-vault-v4-modal .vault-v258-modifier-trigger.is-on .vault-v258-modifier-symbol {background:#d4a15a;color:#fff;border-color:#c58b3d}
.master-vault-v4-modal .vault-v258-modifier-label {white-space:normal;overflow-wrap:anywhere;text-align:left}
.master-vault-v4-modal .vault-v258-modifier-chevron {font-size:15px;color:#ac815d;margin-left:auto}
.master-vault-v4-modal .vault-v258-modifier-picker {
    display:flex;flex-direction:column;gap:10px;margin-top:10px;padding:12px;
    background:linear-gradient(120deg,#fbf3e8,#fffdfa);border:1px solid #e0c8a9;border-radius:11px
}
.master-vault-v4-modal .vault-v258-modifier-picker p {margin:0;color:#77432f;font:800 10px/1.3 Arial,sans-serif}
.master-vault-v4-modal .vault-v258-modifier-picker small {font:500 9px/1.5 Arial,sans-serif;color:#9a775c}
.master-vault-v4-modal .vault-v258-modifier-chips {display:flex;flex-wrap:wrap;gap:7px}
.master-vault-v4-modal .vault-v258-mod-chip {
    display:inline-flex;gap:6px;align-items:center;justify-content:center;
    min-height:33px;padding:6px 10px;border-radius:8px;border:1px solid #e2cdb2;
    background:#fffdfa;color:#735745;font:700 10px/1.2 Arial,sans-serif;cursor:pointer
}
.master-vault-v4-modal .vault-v258-mod-chip>span:first-child {display:grid;place-items:center;width:14px;height:14px;border:1px solid #ceb797;border-radius:4px;font-size:10px;color:#b49778}
.master-vault-v4-modal .vault-v258-mod-chip.is-active {background:#f0dbc0;border-color:#bd874a;color:#6b341e}
.master-vault-v4-modal .vault-v258-mod-chip.is-active>span:first-child {background:#bc884d;color:white;border-color:#bc884d}
.master-vault-v4-modal .vault-v258-mod-chip:disabled {opacity:.44;cursor:not-allowed}
.master-vault-v4-modal :is(.vault-v258-enchant-help summary,.vault-v258-modifier-trigger,.vault-v258-mod-chip):focus-visible {outline:2px solid #b9834f;outline-offset:2px}
@media(max-width:930px) {
    .master-vault-v4-modal .vault-v258-damage-actions {max-width:none;flex-wrap:wrap}
}
@media(max-width:640px) {
    .master-vault-v4-modal .vault-v258-damage-actions {width:100%;justify-content:stretch}
    .master-vault-v4-modal .vault-v258-enchant-edit-prompt {flex-wrap:wrap}
}

/* ================================================================
   V66.2.59 — Dano compacto e seleção visual de modificadores.
   ================================================================ */
.master-vault-v4-modal .vault-v255-damage .vault-v254-damage-content {
    grid-template-columns:minmax(220px,290px) minmax(0,1fr);
    align-items:center;gap:18px
}
.master-vault-v4-modal .vault-v255-damage .vault-v254-dice-panel {
    width:100%;max-width:290px;min-height:96px;gap:8px;
    padding:12px 14px;border-radius:11px;
    background:linear-gradient(145deg,rgba(251,241,225,.62),rgba(255,252,247,.82));
    border-color:rgba(204,169,127,.32);box-shadow:none
}
.master-vault-v4-modal .vault-v255-damage .vault-v252-field-label {
    font-size:10px;letter-spacing:.015em;font-weight:700
}
.master-vault-v4-modal .vault-v255-damage .vault-v254-dice-composer {gap:8px}
.master-vault-v4-modal .vault-v255-damage .vault-v254-stepper {
    height:39px;border-radius:9px;border-color:#d7b992;
    box-shadow:0 2px 5px rgba(96,53,25,.035)
}
.master-vault-v4-modal .vault-v255-damage .vault-v254-stepper button {
    width:27px;min-width:27px;font-size:21px;background:#f8efe3;
    transition:background .16s,color .16s
}
.master-vault-v4-modal .vault-v255-damage .vault-v254-stepper input {
    height:37px;width:33px;min-width:33px;font:750 17px/1 Georgia,serif
}
.master-vault-v4-modal .vault-v255-damage .vault-v254-dice-d {
    font:750 25px/1 Georgia,serif;color:#985d31;transform:none
}
.master-vault-v4-modal .vault-v255-damage .vault-v254-damage-line {padding:14px 16px 15px}
.master-vault-v4-modal .vault-v258-modifier-trigger {
    min-height:37px;transition:background .2s,border-color .2s,box-shadow .2s
}
.master-vault-v4-modal .vault-v258-modifier-trigger.is-open {
    border-color:#b77b44;background:#f5e6ce;box-shadow:0 0 0 2px rgba(185,126,66,.09)
}
.master-vault-v4-modal .vault-v259-chevron {
    width:22px;height:22px;display:grid;place-items:center;flex:none;
    border-radius:6px;background:rgba(188,144,90,.10);color:#91623f;
    transition:transform .22s ease,background .2s,color .2s
}
.master-vault-v4-modal .vault-v258-modifier-trigger.is-open .vault-v259-chevron {
    transform:rotate(180deg);background:#ead0ad;color:#754224
}
.master-vault-v4-modal .vault-v259-modifier-picker {
    position:relative;gap:10px;margin-top:7px;padding:13px 14px 14px;
    border:1px solid #dcc1a0;border-top:2px solid #c89a62;
    border-radius:10px;background:linear-gradient(155deg,#fffaf3 0%,#fbf3e8 100%);
    box-shadow:0 5px 15px rgba(85,52,27,.04);transform-origin:top center
}
.master-vault-v4-modal .vault-v259-picker-heading {
    display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px 12px
}
.master-vault-v4-modal .vault-v259-picker-heading strong {font:750 11px/1.35 Georgia,serif;color:#73422f}
.master-vault-v4-modal .vault-v259-picker-key {display:inline-flex;align-items:center;gap:5px;color:#9a704a;font:600 9px/1.3 Arial,sans-serif}
.master-vault-v4-modal .vault-v259-picker-key>span {color:#b17a38;font-size:9px}
.master-vault-v4-modal .vault-v259-modifier-chips {gap:7px}
.master-vault-v4-modal .vault-v259-mod-chip {
    min-height:33px;padding:6px 9px;border-radius:8px;
    border:1px solid #e1cbb0;background:rgba(255,254,251,.86);
    gap:6px;transition:background .15s,border-color .15s,box-shadow .15s
}
.master-vault-v4-modal .vault-v259-mod-chip:hover {border-color:#c69a69;background:#f9ebd8}
.master-vault-v4-modal .vault-v259-mod-chip.is-active {
    background:#f0ddc3;border-color:#c18e52;box-shadow:inset 0 0 0 1px rgba(191,137,70,.06)
}
.master-vault-v4-modal .vault-v259-mod-chip.is-attack {border-style:solid;border-color:#d3b280}
.master-vault-v4-modal .vault-v259-mod-chip.is-attack.is-active {background:#efdab7;border-color:#ba8343}
.master-vault-v4-modal .vault-v259-mod-chip .vault-v259-chip-state {
    width:15px;height:15px;border-radius:5px;flex:none;font-size:10px
}
.master-vault-v4-modal .vault-v259-mod-chip.is-active .vault-v259-chip-state {
    background:#bb8749;color:#fff;border-color:#bb8749
}
.master-vault-v4-modal .vault-v259-attack-icon {
    display:inline-grid;place-items:center;width:16px;height:16px;flex:none;
    border-radius:5px;background:#f3e3c8;color:#ae742d;font-size:9px;
    margin-left:1px
}
.master-vault-v4-modal .vault-v259-mod-chip.is-active .vault-v259-attack-icon {
    background:#d9b782;color:#673b21
}
@media(max-width:800px) {
    .master-vault-v4-modal .vault-v255-damage .vault-v254-damage-content {grid-template-columns:1fr;gap:12px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-dice-panel {max-width:100%}
}
@media(max-width:510px) {
    .master-vault-v4-modal .vault-v255-damage .vault-v254-damage-line {padding:12px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-dice-composer {gap:6px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-stepper button {width:26px;min-width:26px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-stepper input {width:31px;min-width:31px}
    .master-vault-v4-modal .vault-v255-damage .vault-v254-dice-d {font-size:24px}
    .master-vault-v4-modal .vault-v259-picker-heading {align-items:flex-start}
}


/* ================================================================
   SPELLBOUND V66.2.60 — Armadura e Escudo: editor defensivo uniforme
   ================================================================ */
.master-vault-v4-modal .vault-v260-defense {border-color:#dfc6a8;border-radius:15px;background:linear-gradient(150deg,#fffdf9,#fbf6ee)}
.master-vault-v4-modal .vault-v260-defense .vault-v260-defense-header {
    padding:14px 18px;background:linear-gradient(105deg,#f2e7d9,#fcf8f2);
    display:flex;gap:12px;align-items:center;justify-content:space-between
}
.master-vault-v4-modal .vault-v260-eyebrow {display:block;margin:0 0 4px;color:#a0714e;font:700 9px/1.3 Arial,sans-serif;letter-spacing:.11em;text-transform:uppercase}
.master-vault-v4-modal .vault-v260-defense .vault-v260-defense-header h3 {font:700 19px/1.3 Georgia,serif;color:#652a1e}
.master-vault-v4-modal .vault-v260-defense .master-vault-v4-module-body {gap:10px;padding:14px 16px 17px}
.master-vault-v4-modal .vault-v260-defense .vault-v260-rule {padding:13px 15px;background:linear-gradient(155deg,#fffdf9,#fbf7f1);border-color:#e6d3b9}
.master-vault-v4-modal .vault-v260-row-heading {display:flex;align-items:baseline;justify-content:space-between;gap:8px;margin:0 0 10px}
.master-vault-v4-modal .vault-v260-row-heading strong {font:700 14px/1.25 Georgia,serif;color:#743626}
.master-vault-v4-modal .vault-v260-row-heading small {font:500 10px/1.35 Arial,sans-serif;color:#9f7a5d}
.master-vault-v4-modal .vault-v260-defense .vault-v260-armor-choices {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px}
.master-vault-v4-modal .vault-v260-defense .vault-v260-armor-choices .vault-v19-option,
.master-vault-v4-modal .vault-v260-defense .vault-v260-dex-choices .vault-v19-option {min-height:52px;border-radius:9px;padding:8px 11px 8px 31px}
.master-vault-v4-modal .vault-v260-defense .vault-v260-dex-choices {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}
.master-vault-v4-modal .vault-v260-defense .vault-v19-option:before {top:17px;left:10px;width:12px;height:12px}
.master-vault-v4-modal .vault-v260-custom-category {display:block;margin-top:10px;width:100%}
.master-vault-v4-modal .vault-v260-fields {display:grid;grid-template-columns:minmax(0,2fr) minmax(150px,.8fr);gap:12px;align-items:end}
.master-vault-v4-modal .vault-v260-number .master-vault-v4-input {font:700 19px Georgia,serif;text-align:center;color:#643224}
.master-vault-v4-modal .vault-v260-dex-cap {display:block;margin-top:11px;max-width:195px}
.master-vault-v4-modal .vault-v260-dex-cap .master-vault-v4-input {text-align:center}
.master-vault-v4-modal .vault-v260-ability-chips {gap:7px}
.master-vault-v4-modal .vault-v260-ability-chips .master-vault-v4-chip-button {border-radius:8px;padding:8px 13px}
.master-vault-v4-modal .vault-v260-defense-result {
    display:flex;align-items:center;gap:12px;min-width:0;padding:13px 16px;
    border:1px solid #dcc6a8;border-radius:11px;
    background:linear-gradient(110deg,#f3e9db,#fcf8f1);
}
.master-vault-v4-modal .vault-v260-result-symbol {display:grid;place-items:center;flex:none;width:33px;height:33px;border:1px solid #dfc49f;border-radius:9px;background:#fff8ed;color:#ad7d44;font-size:17px}
.master-vault-v4-modal .vault-v260-defense-result>div {flex:1;min-width:0;display:grid;gap:5px}
.master-vault-v4-modal .vault-v260-defense-result>div>span {color:#997355;font:700 9px/1.2 Arial,sans-serif;letter-spacing:.05em;text-transform:uppercase}
.master-vault-v4-modal .vault-v260-defense-result strong {display:block;color:#632a1c;font:700 16px/1.35 Georgia,serif;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v260-magic-tag {flex:none;padding:6px 10px;border:1px solid #dcb77d;border-radius:999px;background:#f8ecd7;color:#945d22;font:700 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v260-defense-enchant {border-color:#e2d0b8}
.master-vault-v4-modal .vault-v260-defense-enchant.is-enchanted {border-color:#d4aa70}
@media(max-width:690px) {
    .master-vault-v4-modal .vault-v260-defense .vault-v260-armor-choices {grid-template-columns:repeat(2,minmax(0,1fr))}
    .master-vault-v4-modal .vault-v260-defense .vault-v260-dex-choices {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v260-fields {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v260-defense-result {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v260-magic-tag {margin-left:auto}
}
@media(prefers-reduced-motion:reduce) {
    .master-vault-v4-modal .vault-v260-defense * {transition-duration:.01ms!important}
}


.master-vault-v4-modal .vault-v261-category-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
.master-vault-v4-modal .vault-v261-category-card {display:flex;align-items:center;gap:12px;min-height:72px;padding:12px 14px;border:1px solid #d9bea1;border-radius:13px;background:linear-gradient(155deg,#fffdf9,#fbf7f0);box-shadow:inset 0 1px 0 rgba(255,255,255,.82);text-align:left;transition:.18s ease}
.master-vault-v4-modal .vault-v261-category-card:hover {transform:translateY(-1px);border-color:#cba57b;box-shadow:0 8px 18px rgba(85,40,18,.07)}
.master-vault-v4-modal .vault-v261-category-card.is-active {border-color:#9c5842;background:linear-gradient(160deg,#fffaf3,#f7ece2);box-shadow:0 0 0 1px rgba(156,88,66,.12),0 12px 22px rgba(96,44,28,.08)}
.master-vault-v4-modal .vault-v261-category-card.is-muted {opacity:.82}
.master-vault-v4-modal .vault-v261-category-icon {display:grid;place-items:center;flex:none;width:46px;height:46px;border:1px solid #dcc4a7;border-radius:12px;background:linear-gradient(180deg,#fff8ed,#f5ebdc)}
.master-vault-v4-modal .vault-v261-category-icon img {width:30px;height:30px;object-fit:contain}
.master-vault-v4-modal .vault-v261-category-text {display:grid;gap:3px;min-width:0}
.master-vault-v4-modal .vault-v261-category-text strong {font:700 16px/1.2 Georgia,serif;color:#6a2c1f}
.master-vault-v4-modal .vault-v261-category-text small {font:600 10px/1.2 Arial,sans-serif;color:#a07c5b;text-transform:uppercase;letter-spacing:.04em}
.master-vault-v4-modal .vault-v261-identity-grid {display:grid;grid-template-columns:minmax(0,2fr) minmax(150px,.75fr);gap:12px;align-items:end}
.master-vault-v4-modal .vault-v261-properties-grid {display:grid;grid-template-columns:1.35fr .85fr .8fr;gap:12px}
.master-vault-v4-modal .vault-v261-property-card {padding:12px;border:1px solid #e2d0ba;border-radius:12px;background:linear-gradient(160deg,#fffefb,#fbf7ef)}
.master-vault-v4-modal .vault-v261-property-head {display:grid;gap:4px;margin:0 0 10px}
.master-vault-v4-modal .vault-v261-property-head strong {font:700 14px/1.2 Georgia,serif;color:#733424}
.master-vault-v4-modal .vault-v261-property-head small {font:500 10px/1.35 Arial,sans-serif;color:#9b7557}
.master-vault-v4-modal .vault-v261-inline-toggle-row {display:flex;gap:8px;flex-wrap:wrap}
.master-vault-v4-modal .vault-v261-toggle-pill {padding:10px 12px;border:1px solid #dbc09f;border-radius:999px;background:#fffaf2;color:#875634;font:700 12px/1.1 Arial,sans-serif;transition:.16s ease}
.master-vault-v4-modal .vault-v261-toggle-pill.is-active {border-color:#b47447;background:linear-gradient(180deg,#f6e6cd,#eed39f);color:#6d3519;box-shadow:inset 0 1px 0 rgba(255,255,255,.6)}
.master-vault-v4-modal .vault-v261-strength-input .master-vault-v4-input {text-align:center;font:700 18px/1.2 Georgia,serif;color:#6a2a1d}
.master-vault-v4-modal .vault-v261-armor-result {align-items:flex-start}
.master-vault-v4-modal .vault-v261-result-tags {display:flex;flex-wrap:wrap;gap:7px;margin-top:3px}
.master-vault-v4-modal .vault-v261-result-tag {padding:5px 9px;border:1px solid #e1c8a9;border-radius:999px;background:#fff8ee;color:#8e6345;font:700 10px/1.2 Arial,sans-serif}
@media (max-width: 980px){
  .master-vault-v4-modal .vault-v261-category-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
  .master-vault-v4-modal .vault-v261-properties-grid,.master-vault-v4-modal .vault-v261-identity-grid {grid-template-columns:1fr}
}
@media (max-width: 680px){
  .master-vault-v4-modal .vault-v261-category-grid {grid-template-columns:1fr}
}


/* V66.2.62 — armaduras sem catálogo: mais espaço para as escolhas do criador. */
.master-vault-v4-modal .vault-v261-armor-editor .master-vault-v4-module-body {gap:9px;padding:13px 16px 16px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-rule {padding:13px 15px;border-color:#e6d3bd;background:linear-gradient(160deg,#fffdfa 0%,#fcf8f1 100%)}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-row-heading {margin-bottom:9px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card {min-height:65px;padding:10px 12px;border-radius:11px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon {width:43px;height:43px;border-radius:11px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon img {width:29px;height:29px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-text strong {font-size:15px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card.is-muted:not(.is-active) {opacity:.65;border-style:dashed}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card.is-muted.is-active {opacity:1}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card:focus-visible,
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-toggle-pill:focus-visible {outline:2px solid #ad7650;outline-offset:3px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-card {background:linear-gradient(155deg,#fffdf9,#fbf7f0);border-color:#e5d2b9;border-radius:11px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-head {margin-bottom:9px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-head small {line-height:1.45}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-toggle-pill {border-radius:9px;min-height:36px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-inline-toggle-row {gap:7px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-identity-grid .master-vault-v4-field>span {margin-bottom:7px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-armor-result {border-radius:11px}
@media (max-width:1200px) and (min-width:981px){
  .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-properties-grid {grid-template-columns:minmax(0,1.2fr) minmax(0,.9fr)}
  .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-strength {grid-column:1 / -1}
  .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-strength .vault-v261-strength-input {max-width:270px}
}
@media (max-width:980px){
  .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-properties-grid {grid-template-columns:1fr}
}
@media(prefers-reduced-motion:reduce){
  .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card {transition:none}
}

/* Spellbound V66.2.63 — propriedades defensivas: foco no atributo, controles pequenos e fórmula inline. */
.master-vault-v4-modal .vault-v263-properties {padding:15px 16px 13px!important;overflow:visible}
.master-vault-v4-modal .vault-v263-properties > .vault-v260-row-heading {margin-bottom:13px!important}
.master-vault-v4-modal .vault-v263-property-layout {display:grid;grid-template-columns:minmax(0,1.35fr) minmax(285px,.72fr);gap:12px;align-items:stretch}
.master-vault-v4-modal .vault-v263-main-ability {padding:14px 15px;background:linear-gradient(145deg,#fffdfa,#fcf6ed);border-color:#e1c9aa}
.master-vault-v4-modal .vault-v263-main-ability .vault-v261-property-head {margin-bottom:12px}
.master-vault-v4-modal .vault-v263-ability-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:7px}
.master-vault-v4-modal .vault-v263-ability-choice {display:grid;place-items:center;min-width:0;min-height:37px;padding:8px 9px;color:#78513c;background:linear-gradient(#fffefb,#fdfaf6);border:1px solid #ddc8b0;border-radius:9px;font:600 11px/1.2 Arial,sans-serif;transition:background .16s,border-color .16s,box-shadow .16s,transform .16s}
.master-vault-v4-modal .vault-v263-ability-choice.is-none {color:#9b7b65}
.master-vault-v4-modal .vault-v263-ability-choice:hover {transform:translateY(-1px);border-color:#b98d69}
.master-vault-v4-modal .vault-v263-ability-choice.is-active {border-color:#9f664c;background:linear-gradient(145deg,#f9ebe0,#f4dfca);color:#723323;box-shadow:inset 0 0 0 1px rgba(159,102,76,.12)}
.master-vault-v4-modal .vault-v263-mode-panel {display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:14px;padding-top:12px;border-top:1px solid #ead8c2}
.master-vault-v4-modal .vault-v263-field-caption {color:#89634b;font:700 10px/1.3 Arial,sans-serif}
.master-vault-v4-modal .vault-v263-mode-row {display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.master-vault-v4-modal .vault-v263-mode-button {min-width:70px;padding:8px 12px;border:1px solid #dfc5a7;border-radius:8px;background:#fffaf4;color:#83583e;font:700 11px Arial,sans-serif;transition:.16s}
.master-vault-v4-modal .vault-v263-mode-button.is-active {border-color:#aa7450;background:#f6e3cd;color:#773c23}
.master-vault-v4-modal .vault-v263-cap {display:flex;gap:3px;align-items:center;margin-left:4px;color:#956e4e;font:600 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v263-cap > span {margin-right:3px}
.master-vault-v4-modal .vault-v263-cap button,.master-vault-v4-modal .vault-v263-strength-stepper button {display:grid;place-items:center;flex:none;border:1px solid #ddbd97;background:#fbf0e1;color:#8b552f;font:700 18px/1 Arial,sans-serif;transition:.15s}
.master-vault-v4-modal .vault-v263-cap button {width:30px;height:30px;border-radius:7px}
.master-vault-v4-modal .vault-v263-cap input {width:37px;height:30px;border:1px solid #e0cbb1;border-radius:7px;background:#fffefb;text-align:center;color:#653221;font:700 15px Georgia,serif}
.master-vault-v4-modal .vault-v263-secondary-properties {display:grid;grid-template-rows:auto 1fr;gap:10px;min-width:0}
.master-vault-v4-modal .vault-v263-stealth-card,.master-vault-v4-modal .vault-v263-strength-card {display:flex;flex-direction:column;gap:10px;padding:13px 14px}
.master-vault-v4-modal .vault-v263-compact-heading strong,.master-vault-v4-modal .vault-v263-strength-heading>strong {font:700 14px/1.2 Georgia,serif;color:#723626}
.master-vault-v4-modal .vault-v263-stealth-switch {display:flex;align-items:center;gap:9px;width:100%;max-width:235px;min-height:38px;padding:7px 10px;border:1px solid #dcbf9e;border-radius:9px;background:#fff9f0;text-align:left;color:#855639;font:700 12px Arial,sans-serif;transition:.16s}
.master-vault-v4-modal .vault-v263-stealth-switch:hover {border-color:#ad805f;background:#fcf0e1}
.master-vault-v4-modal .vault-v263-stealth-switch.is-disadvantage {background:#f7e3d5;border-color:#b5785c;color:#773725}
.master-vault-v4-modal .vault-v263-stealth-indicator {width:8px;height:8px;border-radius:50%;background:#c5a886;box-shadow:0 0 0 3px rgba(197,168,134,.14)}
.master-vault-v4-modal .vault-v263-stealth-switch.is-disadvantage .vault-v263-stealth-indicator {background:#a7553a;box-shadow:0 0 0 3px rgba(167,85,58,.13)}
.master-vault-v4-modal .vault-v263-stealth-switch svg {width:15px;height:15px;margin-left:auto;opacity:.8;transition:transform .16s}
.master-vault-v4-modal .vault-v263-stealth-switch.is-disadvantage svg {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v263-strength-heading {display:flex;align-items:center;justify-content:space-between;gap:10px}
.master-vault-v4-modal .vault-v263-enable-switch {display:flex;align-items:center;gap:7px;padding:3px 0;border:0;background:none;color:#987453;font:600 10px/1.2 Arial,sans-serif}
.master-vault-v4-modal .vault-v263-enable-track {display:flex;align-items:center;width:30px;height:17px;padding:2px;border-radius:99px;background:#cfc0b1;transition:background .17s}
.master-vault-v4-modal .vault-v263-enable-track i {display:block;width:13px;height:13px;background:#fff;border-radius:50%;box-shadow:0 1px 2px #5b39283d;transition:transform .17s}
.master-vault-v4-modal .vault-v263-enable-switch.is-on .vault-v263-enable-track {background:#a76d40}
.master-vault-v4-modal .vault-v263-enable-switch.is-on .vault-v263-enable-track i {transform:translateX(13px)}
.master-vault-v4-modal .vault-v263-strength-stepper {display:flex;width:min(100%,202px);height:39px;border:1px solid #d9bd9b;border-radius:9px;overflow:hidden;background:#fffdf9}
.master-vault-v4-modal .vault-v263-strength-stepper button {width:40px;border:0;border-radius:0;background:#f6ecde}
.master-vault-v4-modal .vault-v263-strength-stepper button:first-child {border-right:1px solid #e4ceb1}
.master-vault-v4-modal .vault-v263-strength-stepper button:last-child {border-left:1px solid #e4ceb1}
.master-vault-v4-modal .vault-v263-strength-stepper button:hover,.master-vault-v4-modal .vault-v263-cap button:hover {background:#eed4b3}
.master-vault-v4-modal .vault-v263-strength-stepper input {flex:1;min-width:0;width:50px;border:0;background:transparent;text-align:center;font:700 19px Georgia,serif;color:#633122;outline:0}
.master-vault-v4-modal .vault-v263-strength-empty {padding:8px 2px;color:#a2876d;font:500 11px/1.2 Arial,sans-serif}
.master-vault-v4-modal .vault-v263-extras {margin-top:12px;border:1px solid #e7d4bc;border-radius:10px;background:#fcf8f2;overflow:hidden}
.master-vault-v4-modal .vault-v263-extras summary {display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 13px;cursor:pointer;list-style:none;color:#7e5036;user-select:none}
.master-vault-v4-modal .vault-v263-extras summary::-webkit-details-marker {display:none}
.master-vault-v4-modal .vault-v263-extras-label {display:flex;align-items:center;gap:9px;font:700 12px/1.3 Georgia,serif}
.master-vault-v4-modal .vault-v263-extras-label small {font:600 10px Arial,sans-serif;color:#ab8b6a}
.master-vault-v4-modal .vault-v263-extras-chevron {font:700 17px Arial,sans-serif;transition:transform .2s}
.master-vault-v4-modal .vault-v263-extras[open] .vault-v263-extras-chevron {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v263-extras-options {display:flex;flex-wrap:wrap;gap:7px;padding:0 13px 12px}
.master-vault-v4-modal .vault-v263-extra-chip {padding:7px 12px;border:1px solid #dcc4a7;border-radius:8px;background:#fffefb;color:#8a6249;font:700 11px Arial,sans-serif}
.master-vault-v4-modal .vault-v263-extra-chip.is-active {border-color:#b47c5a;background:#f4e3d8;color:#723622}
.master-vault-v4-modal .vault-v263-formula {margin-top:12px;padding:11px 13px!important;border-radius:10px!important;align-items:center!important}
.master-vault-v4-modal .vault-v263-formula .vault-v261-result-tags {margin-top:0}
.master-vault-v4-modal .vault-v263-formula strong {font-size:15px!important}
.master-vault-v4-modal .vault-v263-properties button:focus-visible,.master-vault-v4-modal .vault-v263-properties input:focus-visible,.master-vault-v4-modal .vault-v263-extras summary:focus-visible {outline:2px solid #a8754f;outline-offset:2px}
@media(max-width:1170px){
  .master-vault-v4-modal .vault-v263-property-layout {grid-template-columns:1fr}
  .master-vault-v4-modal .vault-v263-secondary-properties {grid-template-columns:repeat(2,minmax(0,1fr));grid-template-rows:auto}
}
@media(max-width:690px){
  .master-vault-v4-modal .vault-v263-ability-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
  .master-vault-v4-modal .vault-v263-secondary-properties {grid-template-columns:1fr}
  .master-vault-v4-modal .vault-v263-mode-panel {align-items:flex-start;flex-direction:column;gap:8px}
}
@media(prefers-reduced-motion:reduce){
 .master-vault-v4-modal .vault-v263-properties * {transition:none!important}
}


/* Spellbound V66.2.64 — acabamento de Armaduras, sem alterações mecânicas. */
/* Escopo restrito à armadura para não interferir no Escudo, Armas ou Habilidades. */
.master-vault-v4-modal .vault-v261-armor-editor {
    --v264-ink:#653126;--v264-muted:#9c765c;--v264-border:#e5d0b5;
    --v264-accent:#a56c49;--v264-cream:#fcf7ef;--v264-highlight:#f5e7d6;
    border:1px solid #dbc09f;border-radius:16px;
    background:linear-gradient(155deg,#fffdf9 0%,#fdf9f2 72%,#faf4eb 100%);
    box-shadow:0 10px 26px rgba(85,47,24,.045),inset 0 1px rgba(255,255,255,.9);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-defense-header {
    padding:15px 20px;border-bottom:1px solid #e4ccb0;
    background:linear-gradient(112deg,#f1e4d4,#f8f0e6 65%,#fcf9f3);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-defense-header h3 {
    font:700 21px/1.12 Georgia,serif;letter-spacing:-.018em;color:var(--v264-ink);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-eyebrow {margin-bottom:6px;letter-spacing:.13em}
.master-vault-v4-modal .vault-v261-armor-editor .master-vault-v4-module-body {gap:11px;padding:16px 17px 18px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-rule {
    border:1px solid var(--v264-border);border-radius:13px;
    padding:14px 15px;background:linear-gradient(135deg,rgba(255,255,255,.86),rgba(251,246,237,.6));
    box-shadow:inset 0 1px rgba(255,255,255,.95);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-row-heading {margin-bottom:12px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v260-row-heading strong {font:700 15px/1.2 Georgia,serif;color:var(--v264-ink)}

/* Categories — actual card selection, not just a thin generic outline. */
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-grid {gap:10px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card {
    position:relative;gap:12px;min-height:67px;border-radius:12px;
    padding:10px 13px;text-align:left;
    background:linear-gradient(135deg,#fffefa,#fcf8f2);
    border:1px solid #dec8ad;box-shadow:inset 0 1px rgba(255,255,255,.95);
    transition:background .18s,border-color .18s,box-shadow .18s,transform .18s;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card:hover {
    border-color:#b9916e;background:#fffaf3;box-shadow:0 6px 15px rgba(92,48,27,.075);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card.is-active {
    border-color:#ae7250;background:linear-gradient(125deg,#f9eee4,#f6e7d8 88%);
    box-shadow:inset 0 1px rgba(255,255,255,.8),0 0 0 1px rgba(158,89,55,.1),0 5px 15px rgba(95,47,29,.07);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon {
    width:46px;height:46px;flex:none;border-radius:12px;
    border:1px solid #dfc4a4;background:linear-gradient(#fdf7ed,#f1e5d5);
    box-shadow:inset 0 1px rgba(255,255,255,.7);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon img {width:32px;height:32px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card.is-active .vault-v261-category-icon {
    border-color:#ca9c72;background:linear-gradient(#fff7e9,#efddc6);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-text strong {font:700 16px Georgia,serif;color:var(--v264-ink)}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card.is-muted:not(.is-active) {opacity:.73}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v264-category-check {
    display:grid;place-items:center;position:absolute;top:7px;right:7px;width:19px;height:19px;
    border-radius:50%;background:#95603e;color:#fffaf2;
    opacity:0;transform:scale(.7);transition:opacity .16s,transform .16s;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v264-category-check svg {width:12px;height:12px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card.is-active .vault-v264-category-check {opacity:1;transform:scale(1)}

/* Compact identity row: base AC is a distinct numeric control, not another text field. */
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-identity-grid {grid-template-columns:minmax(0,2fr) minmax(145px,.54fr);gap:14px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-identity-grid .master-vault-v4-field>span {
    font-weight:700;color:#71402f;margin-bottom:8px;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-identity-grid .master-vault-v4-input {
    min-height:46px;border-radius:10px;border-color:#dfc8aa;background:#fffdfb;
    box-shadow:inset 0 1px 2px rgba(83,49,28,.035);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-identity-grid .vault-v260-number input {
    font:700 22px Georgia,serif;letter-spacing:.01em;background:#faf1e6;color:#633021;
}

/* Visual hierarchy inside properties — clear primary choice, quiet secondary rules. */
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-properties {padding:16px 16px 14px!important}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-property-layout {grid-template-columns:minmax(0,1.42fr) minmax(280px,.75fr);gap:12px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-main-ability {
    padding:15px 16px;border-color:#ddc4a5;border-radius:12px;
    background:linear-gradient(143deg,#fff9f0,#f9f0e4 100%);
    box-shadow:inset 0 1px rgba(255,255,255,.95);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-head {gap:5px;margin-bottom:13px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-head strong {font:700 15px/1.2 Georgia,serif;color:#713625}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-property-head small {font-size:10px;color:#967052}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-ability-grid {gap:7px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-ability-choice {
    display:flex;justify-content:center;gap:5px;min-height:39px;padding:7px 9px;
    border-radius:9px;border:1px solid #ddc8b1;background:#fffdfa;
    color:#77523f;font:600 11px/1.25 Arial,sans-serif;white-space:nowrap;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-ability-choice:hover {border-color:#b58561;background:#fdf3e9;transform:none}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-ability-choice.is-active {
    color:#763722;border-color:#ae7654;
    background:linear-gradient(180deg,#f2ddc6,#f8eadd);box-shadow:inset 0 1px rgba(255,255,255,.65);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v264-ability-indicator {
    display:none;flex:none;width:6px;height:6px;border-radius:50%;background:#a86841;
    box-shadow:0 0 0 2px rgba(168,104,65,.14);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-ability-choice.is-active .vault-v264-ability-indicator {display:block}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-ability-choice.is-none {color:#a0856e}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-mode-panel {
    gap:10px;align-items:center;justify-content:space-between;
    margin-top:15px;padding-top:13px;border-top:1px solid #e3ccb1;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-field-caption {font-size:10px;color:#886047;letter-spacing:.01em}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-mode-row {gap:6px;flex-wrap:wrap}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-mode-button {
    min-height:32px;min-width:76px;border-radius:8px;background:#fffaf4;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-mode-button.is-active {
    background:#f0dcc6;border-color:#b47f59;box-shadow:inset 0 1px rgba(255,255,255,.7);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-cap {gap:4px;margin-left:6px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-cap input {height:32px;width:40px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-cap button {width:31px;height:32px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-secondary-properties {gap:11px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-card,
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-card {
    border-radius:12px;border:1px solid #e2ccb0;padding:14px 15px;
    background:linear-gradient(145deg,#fffdf9,#fbf6ee);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-compact-heading strong,
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading>strong {font-size:15px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-switch {
    width:100%;max-width:none;min-height:39px;padding:8px 11px;margin-top:2px;
    border-radius:9px;background:#fcf7f1;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-switch.is-disadvantage {
    background:#f5e2d3;color:#833d2c;border-color:#b8775b;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-indicator {
    display:grid;place-items:center;width:21px;height:21px;flex:none;
    border-radius:7px;background:#e9d9c4;color:#9c7956;box-shadow:none;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-indicator svg {width:13px;height:13px;margin:0;opacity:.65}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-switch.is-disadvantage .vault-v263-stealth-indicator {
    background:#ae6547;color:#fff8f0;box-shadow:none;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-switch.is-disadvantage .vault-v263-stealth-indicator svg {opacity:1}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-empty {padding:8px 1px;color:#9f8167}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-enable-switch {font-size:10px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-stepper {margin-top:1px;height:40px;border-radius:9px}

/* Extra modifiers + formula are members of the same properties section. */
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras {
    margin-top:12px;border-radius:10px;border:1px solid #e6ceb0;
    background:linear-gradient(90deg,#faf3e8,#fdfaf5);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras summary {min-height:45px;padding:10px 14px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras summary:hover {background:rgba(235,211,181,.17)}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras-label {font:700 13px/1.25 Georgia,serif;gap:11px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras-label small {
    font:600 10px Arial,sans-serif;border:1px solid #e3ceb4;
    border-radius:99px;padding:4px 8px;color:#9e7d5c;background:#fffaf2;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras-chevron {display:grid;place-items:center;width:26px;height:26px;border-radius:8px;border:1px solid #e4cbae;background:#fff9f1}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras-chevron svg {width:16px;height:16px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras-options {padding:3px 14px 13px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extra-chip {border-radius:8px;min-height:34px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-formula {
    margin-top:11px!important;min-height:72px;padding:13px 15px!important;
    border:1px solid #d8b992!important;border-radius:11px!important;
    background:linear-gradient(115deg,#f2e3ce 0%,#f8eee0 45%,#fcf8f0 100%)!important;
    box-shadow:inset 0 1px rgba(255,255,255,.7);
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-formula .vault-v260-result-symbol {
    width:39px;height:39px;border-radius:11px;
    background:linear-gradient(#fffaf2,#f7e8d2);border-color:#d8b487;
    font-size:20px;color:#ab7547;
}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-formula>div {gap:4px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-formula strong {font:700 19px/1.26 Georgia,serif!important;color:#653021}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-result-tags {gap:6px}
.master-vault-v4-modal .vault-v261-armor-editor .vault-v261-result-tag {
    border:1px solid #dfc6a8;background:#fff9ef;
    color:#8a5b3c;border-radius:99px;padding:4px 9px;
}

@media (max-width:1170px) {
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-property-layout {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-secondary-properties {grid-template-columns:repeat(2,minmax(0,1fr));grid-template-rows:auto}
}
@media (max-width:980px) {
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-identity-grid {grid-template-columns:1fr minmax(120px,.4fr)}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width:690px) {
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-secondary-properties {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-mode-panel {align-items:flex-start}
}
@media (max-width:500px) {
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-card {gap:8px;padding:8px;min-height:60px}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon {width:37px;height:37px}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon img {width:27px;height:27px}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-text strong {font-size:14px}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-identity-grid {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-ability-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-mode-panel {justify-content:flex-start}
}
@media (prefers-reduced-motion:reduce) {
    .master-vault-v4-modal .vault-v261-armor-editor .vault-v264-category-check {transition:none}
}


/* ================================================================
   V66.2.65 — Unificação visual entre Arma, Armadura e Escudo.
   Apenas refinamento estético: mesma paleta pergaminho e mesma
   hierarquia de cartões para os três blocos.
   ================================================================ */
.master-vault-v4-modal .vault-v250-weapon,
.master-vault-v4-modal .vault-v260-shield {
    --v265-ink:#653126;
    --v265-muted:#9c765c;
    --v265-border:#e5d0b5;
    --v265-accent:#a56c49;
    --v265-cream:#fcf7ef;
    --v265-highlight:#f5e7d6;
    border:1px solid #dbc09f;
    border-radius:16px;
    background:linear-gradient(155deg,#fffdf9 0%,#fdf9f2 72%,#faf4eb 100%);
    box-shadow:0 10px 26px rgba(85,47,24,.045),inset 0 1px rgba(255,255,255,.9);
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-module-head,
.master-vault-v4-modal .vault-v260-shield .vault-v260-defense-header {
    padding:15px 20px;
    border-bottom:1px solid #e4ccb0;
    background:linear-gradient(112deg,#f1e4d4,#f8f0e6 65%,#fcf9f3);
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-module-head h3,
.master-vault-v4-modal .vault-v260-shield .vault-v260-defense-header h3 {
    font:700 21px/1.12 Georgia,serif;
    letter-spacing:-.018em;
    color:var(--v265-ink);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v260-eyebrow,
.master-vault-v4-modal .vault-v260-shield .vault-v260-eyebrow {margin-bottom:6px;letter-spacing:.13em}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-module-head p {
    margin:4px 0 0;
    color:#9a785d;
    font:500 11px/1.45 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-module-body,
.master-vault-v4-modal .vault-v260-shield .master-vault-v4-module-body {
    gap:11px;
    padding:16px 17px 18px;
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-rule,
.master-vault-v4-modal .vault-v260-shield .vault-v260-rule {
    border:1px solid var(--v265-border);
    border-radius:13px;
    padding:14px 15px;
    background:linear-gradient(135deg,rgba(255,255,255,.86),rgba(251,246,237,.6));
    box-shadow:inset 0 1px rgba(255,255,255,.95);
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-rule-title,
.master-vault-v4-modal .vault-v260-shield .master-vault-v4-field>span,
.master-vault-v4-modal .vault-v260-shield .vault-v260-row-heading strong {
    color:#71402f;
}
.master-vault-v4-modal .vault-v250-weapon .vault-v19-options--weapon .vault-v19-option {
    min-height:56px;
    border-radius:11px;
    border-color:#dec8ad;
    background:linear-gradient(135deg,#fffefa,#fcf8f2);
    box-shadow:inset 0 1px rgba(255,255,255,.95);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v19-options--weapon .vault-v19-option:hover {
    border-color:#b9916e;
    background:#fffaf3;
    box-shadow:0 6px 15px rgba(92,48,27,.075);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v19-options--weapon .vault-v19-option.is-active {
    border-color:#ae7250;
    background:linear-gradient(125deg,#f9eee4,#f6e7d8 88%);
    box-shadow:inset 0 1px rgba(255,255,255,.8),0 0 0 1px rgba(158,89,55,.1),0 5px 15px rgba(95,47,29,.07);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v250-count {
    border-color:#e3ceb4;
    background:#fffaf2;
    color:#9e7d5c;
    padding:4px 9px;
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-chip-button.vault-v250-choice {
    min-height:35px;
    border-color:#ddc8b1;
    border-radius:9px;
    background:#fffdfa;
    color:#77523f;
    box-shadow:inset 0 1px rgba(255,255,255,.85);
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-chip-button.vault-v250-choice:hover {
    border-color:#b58561;
    background:#fdf3e9;
    color:#6f2f24;
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-chip-button.vault-v250-choice.is-active {
    border-color:#ae7654;
    background:linear-gradient(180deg,#f2ddc6,#f8eadd);
    color:#763722;
    box-shadow:inset 0 1px rgba(255,255,255,.65);
}
.master-vault-v4-modal .vault-v250-choice-indicator {
    border-color:#dcc7b0;
    border-radius:6px;
    background:#f8efe3;
}
.master-vault-v4-modal .vault-v250-choice.is-active .vault-v250-choice-indicator {
    border-color:#9a6242;
    background:#9a6242;
    color:#fff9ed;
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-selected {
    border-color:#e6ceb0;
    border-radius:10px;
    background:linear-gradient(90deg,#faf3e8,#fdfaf5);
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer {
    border:1px solid #e5cfb4;
    border-radius:10px;
    background:#fffdf9;
}
.master-vault-v4-modal .vault-v250-weapon .master-vault-v4-repeat-row,
.master-vault-v4-modal .vault-v250-weapon .vault-v250-damage-part {
    border-color:#e2ccb0;
    border-radius:12px;
    background:linear-gradient(145deg,#fffdf9,#fbf6ee);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v250-damage-summary {
    border:1px solid #d8b992;
    border-radius:11px;
    background:linear-gradient(115deg,#f2e3ce 0%,#f8eee0 45%,#fcf8f0 100%);
    box-shadow:inset 0 1px rgba(255,255,255,.7);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v250-damage-summary span {color:#997355}
.master-vault-v4-modal .vault-v250-weapon .vault-v250-damage-summary strong {color:#653021;font:700 18px/1.26 Georgia,serif}
.master-vault-v4-modal .vault-v250-weapon .vault-v251-attributes,
.master-vault-v4-modal .vault-v250-weapon .vault-v251-enchant {
    border-color:#e2ccb0;
    background:linear-gradient(145deg,#fffdf9,#fbf6ee);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v251-option,
.master-vault-v4-modal .vault-v250-weapon .vault-v251-type-chip,
.master-vault-v4-modal .vault-v250-weapon .vault-v258-modifier-trigger,
.master-vault-v4-modal .vault-v250-weapon .vault-v259-mod-chip {
    border-color:#ddc8b1;
    background:#fffdfa;
    color:#77523f;
}
.master-vault-v4-modal .vault-v250-weapon .vault-v251-option.is-selected,
.master-vault-v4-modal .vault-v250-weapon .vault-v251-type-chip.is-selected,
.master-vault-v4-modal .vault-v250-weapon .vault-v258-modifier-trigger.is-open,
.master-vault-v4-modal .vault-v250-weapon .vault-v259-mod-chip.is-active {
    border-color:#ae7654;
    background:linear-gradient(180deg,#f2ddc6,#f8eadd);
    color:#763722;
}
.master-vault-v4-modal .vault-v250-weapon .vault-v252-stepper,
.master-vault-v4-modal .vault-v250-weapon .vault-v255-stepper {
    border-color:#dcc2a0;
    background:linear-gradient(180deg,#fff9f1,#f7ecdf);
}
.master-vault-v4-modal .vault-v250-weapon .vault-v252-stepper-center,
.master-vault-v4-modal .vault-v250-weapon .vault-v255-stepper-center {
    background:#fffdfa;
    color:#653021;
}
.master-vault-v4-modal .vault-v250-weapon .vault-v250-add-damage {
    border-color:#c69c78;
    border-radius:10px;
    background:#fcf7ef;
    color:#78412d;
}
.master-vault-v4-modal .vault-v250-weapon .vault-v250-add-damage:hover {
    background:#f6e9da;
    color:#67291f;
}
.master-vault-v4-modal .vault-v260-shield .vault-v260-fields {
    grid-template-columns:minmax(0,2fr) minmax(145px,.54fr);
    gap:14px;
}
.master-vault-v4-modal .vault-v260-shield .master-vault-v4-field>span {font-weight:700;color:#71402f;margin-bottom:8px}
.master-vault-v4-modal .vault-v260-shield .master-vault-v4-input {
    min-height:46px;
    border-radius:10px;
    border-color:#dfc8aa;
    background:#fffdfb;
    box-shadow:inset 0 1px 2px rgba(83,49,28,.035);
}
.master-vault-v4-modal .vault-v260-shield .vault-v260-number input {
    font:700 22px Georgia,serif;
    letter-spacing:.01em;
    background:#faf1e6;
    color:#633021;
    text-align:center;
}
.master-vault-v4-modal .vault-v260-shield .vault-v260-defense-result {
    min-height:72px;
    padding:13px 15px;
    border:1px solid #d8b992;
    border-radius:11px;
    background:linear-gradient(115deg,#f2e3ce 0%,#f8eee0 45%,#fcf8f0 100%);
    box-shadow:inset 0 1px rgba(255,255,255,.7);
}
.master-vault-v4-modal .vault-v260-shield .vault-v260-result-symbol {
    width:39px;height:39px;border-radius:11px;
    background:linear-gradient(#fffaf2,#f7e8d2);
    border-color:#d8b487;
    font-size:20px;
    color:#ab7547;
}
.master-vault-v4-modal .vault-v260-shield .vault-v260-defense-result>div>span {color:#997355;letter-spacing:.06em}
.master-vault-v4-modal .vault-v260-shield .vault-v260-defense-result strong {
    color:#653021;
    font:700 19px/1.26 Georgia,serif;
}
.master-vault-v4-modal .vault-v260-shield .vault-v260-magic-tag {
    border-color:#dcb77d;
    background:#f8ecd7;
    color:#945d22;
}
@media (max-width:980px) {
    .master-vault-v4-modal .vault-v260-shield .vault-v260-fields {grid-template-columns:1fr minmax(120px,.4fr)}
}
@media (max-width:680px) {
    .master-vault-v4-modal .vault-v260-shield .vault-v260-fields {grid-template-columns:1fr}
}


/* ================================================================
   Spellbound V66.2.66 — Seleção de feitiços no pergaminho.
   Pergaminho claro, seleção por fonte opt-in e visual de grimório.
   ================================================================ */
.master-vault-v4-modal .vault-v266-scroll-editor {
    border:1px solid #dbc09f;border-radius:16px;overflow:hidden;
    background:linear-gradient(155deg,#fffdf9 0%,#fdf9f2 72%,#faf4eb 100%);
    box-shadow:0 10px 26px rgba(85,47,24,.045),inset 0 1px rgba(255,255,255,.9);
}
.master-vault-v4-modal .vault-v266-scroll-head {padding:15px 20px;border-bottom:1px solid #e4ccb0;background:linear-gradient(112deg,#f1e4d4,#f8f0e6 65%,#fcf9f3)}
.master-vault-v4-modal .vault-v266-scroll-head h3 {font:700 21px/1.2 Georgia,serif;color:#653126;letter-spacing:-.018em}
.master-vault-v4-modal .vault-v266-scroll-body {display:grid;gap:12px;padding:16px 17px 18px}
.master-vault-v4-modal .vault-v266-catalog {padding:14px;border:1px solid #e5d0b5;border-radius:13px;background:linear-gradient(135deg,rgba(255,255,255,.86),rgba(251,246,237,.6));box-shadow:inset 0 1px rgba(255,255,255,.95)}
.master-vault-v4-modal .vault-v266-catalog-top {display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px}
.master-vault-v4-modal .vault-v266-catalog-caption {display:flex;align-items:center;gap:10px;min-width:0}
.master-vault-v4-modal .vault-v266-book-crest {width:41px;height:41px;display:grid;place-items:center;border:1px solid #dfc4a4;border-radius:11px;background:linear-gradient(#fff8ec,#f2e4d3)}
.master-vault-v4-modal .vault-v266-book-crest img {width:26px;height:26px;object-fit:contain}
.master-vault-v4-modal .vault-v266-catalog-caption>div {display:grid;gap:3px}
.master-vault-v4-modal .vault-v266-catalog-caption strong {font:700 16px/1.2 Georgia,serif;color:#653126}
.master-vault-v4-modal .vault-v266-catalog-caption small {font:500 10px/1.35 Arial,sans-serif;color:#997456}
.master-vault-v4-modal .vault-v266-catalog-actions {display:flex;align-items:center;gap:9px;flex-wrap:wrap}
.master-vault-v4-modal .vault-v266-count {border:1px solid #e3ceb4;border-radius:99px;background:#fffaf2;color:#946b50;padding:7px 10px;font:700 10px/1.2 Arial,sans-serif}
.master-vault-v4-modal .vault-v266-books-toggle {display:inline-flex;align-items:center;gap:8px;min-height:37px;border:1px solid #c89975;border-radius:9px;background:#fffaf4;color:#79422f;padding:7px 12px;font:700 11px/1.1 Arial,sans-serif;transition:background .15s,border-color .15s}
.master-vault-v4-modal .vault-v266-books-toggle:hover,.master-vault-v4-modal .vault-v266-books-toggle.is-active {background:#f2e2d0;border-color:#af7754}
.master-vault-v4-modal .vault-v266-books-toggle svg {width:17px;height:17px;flex:none}
.master-vault-v4-modal .vault-v266-chevron {transition:transform .2s}
.master-vault-v4-modal .vault-v266-chevron.is-open {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v266-book-count {background:#a46b49;color:#fff7ef;border-radius:99px;min-width:19px;text-align:center;padding:3px 6px}
.master-vault-v4-modal .vault-v266-book-drawer {padding:12px;margin-bottom:12px;border:1px solid #dac09f;border-radius:12px;background:linear-gradient(135deg,#f5e8d8,#fbf5ed)}
.master-vault-v4-modal .vault-v266-book-drawer-head {display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:10px}
.master-vault-v4-modal .vault-v266-book-drawer-head>div {display:grid;gap:3px}
.master-vault-v4-modal .vault-v266-book-drawer-head strong {font:700 14px Georgia,serif;color:#713726}
.master-vault-v4-modal .vault-v266-book-drawer-head small {font-size:10px;color:#967358}
.master-vault-v4-modal .vault-v266-clear-books {border:0;background:transparent;color:#974b37;text-decoration:underline;text-underline-offset:3px;font:700 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-book-search,.master-vault-v4-modal .vault-v266-spell-search {display:flex;align-items:center;gap:9px;min-width:0;border:1px solid #dec7aa;border-radius:9px;background:#fffdfb;padding:0 11px;color:#997554}
.master-vault-v4-modal :is(.vault-v266-book-search,.vault-v266-spell-search) svg {width:17px;height:17px;flex:none}
.master-vault-v4-modal :is(.vault-v266-book-search,.vault-v266-spell-search) input {width:100%;min-width:0;height:40px;border:0;outline:0;box-shadow:none;background:transparent;color:#693321;font:500 12px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-book-search {max-width:400px;margin-bottom:10px}
.master-vault-v4-modal .vault-v266-book-grid {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:7px;max-height:190px;overflow-y:auto;padding:2px}
.master-vault-v4-modal .vault-v266-book-chip {display:flex;align-items:center;gap:8px;min-width:0;border:1px solid #e0c8aa;border-radius:9px;background:#fffdf9;padding:8px;text-align:left;color:#74472d;transition:background .15s,border-color .15s}
.master-vault-v4-modal .vault-v266-book-chip:hover {border-color:#ba916a;background:#fff9ed}
.master-vault-v4-modal .vault-v266-book-chip.is-selected {border-color:#ae7654;background:linear-gradient(180deg,#f2ddc6,#f8eadd)}
.master-vault-v4-modal .vault-v266-book-check {width:21px;height:21px;flex:none;display:grid;place-items:center;border:1px solid #dbba96;border-radius:6px;background:#faf1e5;font:700 12px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-book-chip.is-selected .vault-v266-book-check {border-color:#a36b48;background:#a36b48;color:white}
.master-vault-v4-modal .vault-v266-book-name {display:grid;gap:3px;min-width:0;flex:1}
.master-vault-v4-modal .vault-v266-book-name strong {font:700 11px/1.25 Arial,sans-serif;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v266-book-name small {font:600 9px Arial,sans-serif;color:#a07e65}
.master-vault-v4-modal .vault-v266-book-total {font:700 10px Arial,sans-serif;color:#9a7255}
.master-vault-v4-modal .vault-v266-filters {display:grid;grid-template-columns:minmax(160px,1fr) minmax(125px,.3fr) minmax(145px,.35fr);gap:8px;margin-bottom:11px}
.master-vault-v4-modal .vault-v266-filter-select {width:100%;min-width:0;height:42px;border:1px solid #dec7aa;border-radius:9px;background:#fffdfb;color:#74442e;padding:0 10px;font:600 11px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-browser {display:grid;grid-template-columns:minmax(0,1.2fr) minmax(250px,.8fr);gap:12px;align-items:stretch}
.master-vault-v4-modal .vault-v266-spell-panel,.master-vault-v4-modal .vault-v266-detail-panel {min-width:0;border:1px solid #e2ccb0;border-radius:11px;background:#fffdfa;overflow:hidden}
.master-vault-v4-modal .vault-v266-list-heading {display:flex;justify-content:space-between;gap:10px;padding:10px 12px;border-bottom:1px solid #e9d7c2;background:linear-gradient(105deg,#f5e9db,#fdf9f3)}
.master-vault-v4-modal .vault-v266-list-heading strong,.master-vault-v4-modal .vault-v266-list-heading span {font:700 10px/1.2 Arial,sans-serif;color:#906749}
.master-vault-v4-modal .vault-v266-spell-list {height:286px;overflow-y:auto;overscroll-behavior:contain}
.master-vault-v4-modal .vault-v266-spell-row {width:100%;display:flex;align-items:center;justify-content:space-between;gap:10px;text-align:left;padding:9px 12px;border:0;border-bottom:1px solid #efe3d7;background:#fffdfa;color:#70402d;transition:background .12s}
.master-vault-v4-modal .vault-v266-spell-row:hover {background:#fbf0e1}
.master-vault-v4-modal .vault-v266-spell-row.is-preview {background:#f3e3ce;box-shadow:inset 3px 0 #a56a44}
.master-vault-v4-modal .vault-v266-spell-row.is-linked .vault-v266-spell-row-main strong:after {content:' ✓';color:#a46a40}
.master-vault-v4-modal .vault-v266-spell-row-main {display:grid;gap:3px;min-width:0}
.master-vault-v4-modal .vault-v266-spell-row-main strong {font:700 12px/1.24 Georgia,serif;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v266-spell-row-main small {font:500 10px/1.2 Arial,sans-serif;color:#9a765b}
.master-vault-v4-modal .vault-v266-spell-row-meta {display:grid;justify-items:end;gap:3px;flex:none;font:600 10px/1.2 Arial,sans-serif;color:#966d4e}
.master-vault-v4-modal .vault-v266-spell-row-meta b {font:700 9px Arial,sans-serif;color:#aa794b}
.master-vault-v4-modal .vault-v266-list-empty {padding:22px 14px;color:#98775b;font:500 12px/1.6 Arial,sans-serif;text-align:center}
.master-vault-v4-modal .vault-v266-more-results {display:block;width:100%;padding:10px;border:0;border-top:1px solid #e9d7c2;background:#f9f1e7;color:#895737;font:700 11px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-more-results:hover {background:#f1e1cc}
.master-vault-v4-modal .vault-v266-detail-panel {display:flex;flex-direction:column;min-height:330px;background:linear-gradient(150deg,#fffaf3,#f7ecdc)}
.master-vault-v4-modal .vault-v266-spell-detail {display:flex;flex-direction:column;gap:11px;padding:20px 18px;height:100%}
.master-vault-v4-modal .vault-v266-detail-eyebrow {color:#a27a56;font:700 9px Arial,sans-serif;letter-spacing:.11em;text-transform:uppercase}
.master-vault-v4-modal .vault-v266-spell-detail h4 {font:700 22px/1.2 Georgia,serif;color:#683120;margin:0;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v266-detail-tags {display:flex;gap:6px;flex-wrap:wrap}
.master-vault-v4-modal .vault-v266-detail-tags span {border:1px solid #e0c3a0;border-radius:99px;padding:5px 8px;background:#fffaf3;color:#92613f;font:700 9px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-detail-metadata {display:grid;gap:8px;margin:0}
.master-vault-v4-modal .vault-v266-detail-metadata>div {display:grid;grid-template-columns:83px minmax(0,1fr);gap:10px;font:11px/1.35 Arial,sans-serif}
.master-vault-v4-modal .vault-v266-detail-metadata dt {color:#9a7356;font-weight:700}
.master-vault-v4-modal .vault-v266-detail-metadata dd {margin:0;color:#653a29;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v266-detail-flags {display:flex;flex-wrap:wrap;gap:6px}
.master-vault-v4-modal .vault-v266-detail-flags span {padding:5px 8px;background:#f2e3cf;border-radius:7px;color:#825132;font:700 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-detail-hint {color:#9a755b;font:500 10px/1.5 Arial,sans-serif;margin:auto 0 0}
.master-vault-v4-modal .vault-v266-link-button {width:100%;min-height:40px;padding:9px 12px;border:1px solid #7c3425;border-radius:9px;background:#742d21;color:#fffaf3;font:700 11px Arial,sans-serif}
.master-vault-v4-modal .vault-v266-link-button.is-linked {border-color:#b08459;background:#f0dcc5;color:#75452c}
.master-vault-v4-modal .vault-v266-detail-empty {display:flex;flex-direction:column;align-items:center;justify-content:center;gap:9px;padding:22px;height:100%;text-align:center;color:#967157}
.master-vault-v4-modal .vault-v266-detail-empty img {height:38px;width:38px;object-fit:contain;opacity:.72}
.master-vault-v4-modal .vault-v266-detail-empty strong {font:700 15px Georgia,serif;color:#74402d}
.master-vault-v4-modal .vault-v266-detail-empty span {font:500 11px/1.55 Arial,sans-serif}
.master-vault-v4-modal .vault-v266-linked {display:flex;align-items:center;gap:10px;padding:11px 13px;border:1px solid #d6b58e;border-radius:11px;background:linear-gradient(110deg,#f2e3ce,#fcf8ef)}
.master-vault-v4-modal .vault-v266-linked-mark {width:24px;height:24px;display:grid;place-items:center;flex:none;background:#a1744a;color:white;border-radius:50%;font-weight:700}
.master-vault-v4-modal .vault-v266-linked>span:nth-child(2) {flex:1;display:grid;gap:3px;min-width:0}
.master-vault-v4-modal .vault-v266-linked small {font:600 9px Arial,sans-serif;color:#a07853;text-transform:uppercase;letter-spacing:.04em}
.master-vault-v4-modal .vault-v266-linked strong {font:700 13px Georgia,serif;color:#68311e;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v266-linked-level {font:700 10px Arial,sans-serif;color:#85563a}
.master-vault-v4-modal .vault-v266-manual {border:1px solid #e4d0b7;border-radius:10px;background:#fffaf4}
.master-vault-v4-modal .vault-v266-manual summary {display:flex;align-items:center;justify-content:space-between;gap:10px;cursor:pointer;padding:12px 14px;color:#86573b;font:700 11px Arial,sans-serif;list-style:none}
.master-vault-v4-modal .vault-v266-manual summary::-webkit-details-marker {display:none}
.master-vault-v4-modal .vault-v266-manual summary svg {width:17px;height:17px;transition:transform .2s}
.master-vault-v4-modal .vault-v266-manual[open] summary svg {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v266-manual .vault-v247-spell-manual {margin:0;padding:0 14px 14px}
@media(max-width:1050px){
    .master-vault-v4-modal .vault-v266-browser {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v266-detail-panel {min-height:200px}
    .master-vault-v4-modal .vault-v266-book-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:650px){
    .master-vault-v4-modal .vault-v266-filters {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v266-book-grid {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v266-catalog-actions {width:100%;justify-content:space-between}
    .master-vault-v4-modal .vault-v266-scroll-body {padding:12px}
    .master-vault-v4-modal .vault-v266-spell-detail {padding:14px}
}
@media(prefers-reduced-motion:reduce){
    .master-vault-v4-modal .vault-v266-scroll-editor * {transition:none!important}
}
/* ==============================================================
   V66.2.67 | Pergaminho: seleção rápida + biblioteca arcana modal.
   CSS do portal é independente dos ancestrais do editor.
   ============================================================== */
.master-vault-v4-modal .vault-v267-scroll-body {gap:10px;padding:16px 17px 18px}
.master-vault-v4-modal .vault-v267-quick-card {
    padding:17px;border:1px solid #ddc4a8;border-radius:13px;
    background:linear-gradient(125deg,#fffdf9,#f8efe3 100%);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.92)
}
.master-vault-v4-modal .vault-v267-quick-title {display:flex;align-items:center;gap:11px;margin-bottom:15px}
.master-vault-v4-modal .vault-v267-quick-emblem {display:grid;place-items:center;width:45px;height:45px;flex:none;border-radius:12px;border:1px solid #ddc09a;background:linear-gradient(#fff8ee,#f0dfcb)}
.master-vault-v4-modal .vault-v267-quick-emblem img {width:28px;height:28px;object-fit:contain}
.master-vault-v4-modal .vault-v267-quick-title>div {display:grid;gap:4px;flex:1;min-width:0}
.master-vault-v4-modal .vault-v267-quick-title strong {color:#6c3426;font:700 16px/1.2 Georgia,serif}
.master-vault-v4-modal .vault-v267-quick-title small {color:#9c775a;font:500 11px/1.4 Arial,sans-serif}
.master-vault-v4-modal .vault-v267-base-tag {flex:none;border:1px solid #d9b995;border-radius:999px;padding:6px 10px;background:#f4e8d6;color:#94623d;font:700 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v267-quick-controls {display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:stretch}
.master-vault-v4-modal .vault-v267-quick-search-wrap {position:relative;min-width:0;z-index:15}
.master-vault-v4-modal .vault-v267-quick-search {height:48px;display:flex;align-items:center;gap:11px;padding:0 14px;border:1px solid #d7b895;border-radius:11px;background:#fffdfb;color:#9d795c;box-shadow:0 2px 8px rgba(94,50,28,.035)}
.master-vault-v4-modal .vault-v267-quick-search:focus-within {border-color:#ad764f;box-shadow:0 0 0 3px rgba(173,118,79,.12)}
.master-vault-v4-modal .vault-v267-quick-search svg {height:19px;width:19px;flex:none}
.master-vault-v4-modal .vault-v267-quick-search input {height:100%;min-width:0;flex:1;border:0;background:transparent;outline:0;box-shadow:none;font:500 13px Arial,sans-serif;color:#6b3428}
.master-vault-v4-modal .vault-v267-quick-search input::placeholder {color:#a28b7b}
.master-vault-v4-modal .vault-v267-open-book {display:flex;align-items:center;justify-content:center;gap:10px;min-width:175px;height:48px;padding:10px 15px;border:1px solid #aa704c;border-radius:11px;background:linear-gradient(135deg,#f3e2ce,#ead2b7);color:#78432c;font:700 12px Arial,sans-serif;box-shadow:inset 0 1px 0 rgba(255,255,255,.7);transition:background .15s,transform .15s}
.master-vault-v4-modal .vault-v267-open-book:hover {background:#e8c9a6;transform:translateY(-1px)}
.master-vault-v4-modal .vault-v267-open-book svg {width:20px;height:20px;flex:none}
.master-vault-v4-modal .vault-v267-suggestions {position:absolute;top:calc(100% + 5px);left:0;right:0;z-index:16;max-height:330px;overflow-y:auto;border:1px solid #d6b58e;border-radius:10px;background:#fffcf7;box-shadow:0 16px 32px rgba(63,32,19,.2)}
.master-vault-v4-modal .vault-v267-suggestion-row {display:flex;justify-content:space-between;align-items:center;gap:9px;width:100%;text-align:left;padding:10px 13px;border:0;border-bottom:1px solid #f0e0d0;background:transparent;color:#6d3b2b}
.master-vault-v4-modal .vault-v267-suggestion-row:hover,.master-vault-v4-modal .vault-v267-suggestion-row:focus {background:#f4e4cf}
.master-vault-v4-modal .vault-v267-suggestion-row>span:first-child {display:grid;gap:4px;min-width:0}
.master-vault-v4-modal .vault-v267-suggestion-row strong {font:700 13px Georgia,serif}
.master-vault-v4-modal .vault-v267-suggestion-row small {font:500 10px Arial,sans-serif;color:#977455}
.master-vault-v4-modal .vault-v267-suggestion-meta {flex:none;display:grid;justify-items:end;gap:4px;color:#916141;font:600 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v267-suggestion-empty {padding:16px;color:#977253;font:11px/1.6 Arial,sans-serif}
.master-vault-v4-modal .vault-v267-quick-empty {margin:12px 0 0;padding:9px 12px;border:1px dashed #e2cbb0;border-radius:9px;color:#987458;background:#fffaf3;font:11px/1.5 Arial,sans-serif}
.master-vault-v4-modal .vault-v267-quick-empty span {color:#ad7651}
.master-vault-v4-modal .vault-v267-selection {display:flex;align-items:center;gap:11px;flex-wrap:wrap;margin-top:13px;padding:12px;border:1px solid #d6b58e;border-radius:10px;background:linear-gradient(90deg,#f2e3ce,#fcf8ef)}
.master-vault-v4-modal .vault-v267-selection-mark {width:28px;height:28px;display:grid;place-items:center;flex:none;border-radius:9px;background:#95613e;color:white;font-size:13px}
.master-vault-v4-modal .vault-v267-selection-text {display:grid;gap:4px;flex:1;min-width:150px}
.master-vault-v4-modal .vault-v267-selection-text small {font:700 9px Arial,sans-serif;text-transform:uppercase;letter-spacing:.07em;color:#977253}
.master-vault-v4-modal .vault-v267-selection-text strong {font:700 15px/1.2 Georgia,serif;color:#683426}
.master-vault-v4-modal .vault-v267-selection-level {border:1px solid #e0c5a3;border-radius:99px;padding:5px 9px;color:#8e5f3f;font:700 10px Arial,sans-serif;background:#fffaf3}
.master-vault-v4-modal .vault-v267-change-link {border:1px solid #c6a27f;border-radius:8px;background:#fffaf2;padding:7px 11px;color:#80503a;font:700 11px Arial,sans-serif}
.master-vault-v4-modal .vault-v267-change-link:hover {background:#f5e4cf}
.master-vault-v4-modal .vault-v267-manual {background:linear-gradient(90deg,#fffcf8,#f9f1e6)}
.master-vault-v4-modal .vault-v267-manual summary {min-height:43px}

/* Teleportado para body: overlay sempre sobre o modal do Cofre. */
.vault-v267-library-layer {position:fixed;inset:0;z-index:2147480000;display:grid;place-items:center;padding:18px;color:#663526;font-family:Arial,sans-serif}
.vault-v267-library-layer[x-cloak] {display:none!important}
.vault-v267-library-backdrop {position:absolute;inset:0;background:rgba(43,30,24,.76);backdrop-filter:blur(3px)}
.vault-v267-library-dialog {position:relative;display:flex;flex-direction:column;width:min(1070px,100%);height:min(790px,calc(100vh - 36px));min-height:0;overflow:hidden;border:1px solid #cfae88;border-radius:18px;background:#fdf9f2;box-shadow:0 28px 65px rgba(28,17,10,.38),inset 0 1px rgba(255,255,255,.9)}
.vault-v267-library-head {display:flex;align-items:center;justify-content:space-between;gap:12px;padding:15px 21px;border-bottom:1px solid #e1c8aa;background:linear-gradient(110deg,#f0e1d0,#fcf7f0)}
.vault-v267-library-head-title {display:flex;gap:13px;align-items:center;min-width:0}
.vault-v267-library-emblem {display:grid;place-items:center;flex:none;width:46px;height:46px;border:1px solid #d9b993;border-radius:12px;background:#fff6e8;color:#975f3f}
.vault-v267-library-emblem svg {width:25px;height:25px}
.vault-v267-library-head-title>div {display:grid;gap:3px;min-width:0}
.vault-v267-library-head-title small {font:800 9px Arial,sans-serif;letter-spacing:.12em;color:#aa754e}
.vault-v267-library-head-title h3 {font:700 23px/1.15 Georgia,serif;color:#683224;margin:0}
.vault-v267-library-head-title p {font:500 10px/1.4 Arial,sans-serif;color:#987258;margin:0}
.vault-v267-library-close {display:grid;place-items:center;flex:none;width:34px;height:34px;border:1px solid #e4cdb4;border-radius:9px;background:#fffaf3;color:#7f5037}
.vault-v267-library-close:hover {background:#f3dfc9}
.vault-v267-library-close svg {width:19px;height:19px}
.vault-v267-library-toolbar {display:flex;align-items:center;gap:9px;flex-wrap:wrap;padding:10px 19px;background:#fcf7ef;border-bottom:1px solid #ecdbc7}
.vault-v267-library-source {display:flex;align-items:center;gap:9px;padding:7px 10px;border:1px solid #debea0;border-radius:9px;background:#fff9f0}
.vault-v267-library-source strong {font:700 11px Arial,sans-serif;color:#7c482b}
.vault-v267-library-source small {font:600 10px Arial,sans-serif;color:#a27e61}
.vault-v267-library-count {font:700 10px Arial,sans-serif;color:#977356;margin-left:auto}
.vault-v267-more-books {display:flex;align-items:center;gap:7px;min-height:35px;padding:7px 12px;border:1px solid #b7865e;border-radius:9px;background:#f6e8d6;color:#78432c;font:700 11px Arial,sans-serif}
.vault-v267-more-books:hover,.vault-v267-more-books.is-active {background:#edd7bc}
.vault-v267-more-books svg {width:17px;height:17px;flex:none}
.vault-v267-more-books svg:last-child {width:14px;height:14px;transition:transform .17s}
.vault-v267-more-books svg.is-flipped {transform:rotate(180deg)}
.vault-v267-more-books b {padding:2px 6px;border-radius:100px;background:#985f40;color:white;font:700 10px Arial,sans-serif}
.vault-v267-library-books {flex:none;max-height:230px;overflow-y:auto;padding:12px 19px;border-bottom:1px solid #e3cbb0;background:linear-gradient(105deg,#f5e7d6,#fcf6ed)}
.vault-v267-books-top {display:flex;justify-content:space-between;gap:10px;margin-bottom:9px;align-items:center}
.vault-v267-books-top strong {font:700 14px Georgia,serif;color:#753d28}
.vault-v267-books-top button {background:transparent;border:0;text-decoration:underline;color:#a16b49;font:700 10px Arial,sans-serif}
.vault-v267-book-search {display:flex;align-items:center;gap:8px;border:1px solid #d9bb99;border-radius:8px;background:#fffdfa;padding:0 10px;max-width:365px;height:34px;margin-bottom:10px}
.vault-v267-book-search svg {width:16px;height:16px;color:#a17a59}
.vault-v267-book-search input {flex:1;min-width:0;height:100%;border:0;outline:0;background:transparent;font:11px Arial,sans-serif;color:#693521}
.vault-v267-books-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:7px}
.vault-v267-book-option {display:flex;align-items:center;gap:7px;text-align:left;min-width:0;min-height:42px;border:1px solid #dfc8aa;border-radius:9px;padding:7px 8px;background:#fffdf9;color:#754b34}
.vault-v267-book-option:hover {border-color:#b7865e;background:#fff7e9}
.vault-v267-book-option.is-active {background:#f0dcc6;border-color:#ab724c}
.vault-v267-book-check {display:grid;place-items:center;flex:none;width:20px;height:20px;border:1px solid #d9b995;border-radius:6px;font:700 12px Arial,sans-serif}
.vault-v267-book-option.is-active .vault-v267-book-check {background:#996340;color:#fffaf3}
.vault-v267-book-info {display:grid;gap:3px;flex:1;min-width:0}
.vault-v267-book-info strong {font:700 10px Arial,sans-serif;overflow-wrap:anywhere}
.vault-v267-book-info small {font:500 9px Arial,sans-serif;color:#9f7b5b}
.vault-v267-book-option>small {color:#9e7355;font-size:10px}
.vault-v267-books-empty {margin:0;padding:12px;color:#97745b;font:11px Arial,sans-serif}
.vault-v267-library-filters {display:grid;grid-template-columns:minmax(0,1fr) minmax(140px,.24fr) minmax(160px,.27fr);gap:9px;padding:12px 19px;border-bottom:1px solid #ead9c4}
.vault-v267-library-search {height:40px;display:flex;align-items:center;gap:9px;padding:0 11px;border:1px solid #dabd9d;border-radius:9px;background:#fffdfb;color:#967050}
.vault-v267-library-search svg {width:17px;height:17px;flex:none}
.vault-v267-library-search input {min-width:0;width:100%;height:100%;border:0;background:transparent;outline:0;font:12px Arial,sans-serif;color:#683221}
.vault-v267-filter {height:40px;min-width:0;width:100%;padding:0 10px;border:1px solid #dabd9d;border-radius:9px;background:#fffdfb;color:#6e432e;font:600 11px Arial,sans-serif}
.vault-v267-library-content {display:grid;grid-template-columns:minmax(0,1.3fr) minmax(285px,.8fr);gap:12px;padding:12px 19px;min-height:0;flex:1}
.vault-v267-library-list-pane,.vault-v267-library-preview {display:flex;flex-direction:column;min-width:0;min-height:0;border:1px solid #e1c7a9;border-radius:11px;overflow:hidden;background:#fffdfa}
.vault-v267-list-title {display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border-bottom:1px solid #e7d4bc;background:linear-gradient(90deg,#f3e6d6,#fdf9f3)}
.vault-v267-list-title strong,.vault-v267-list-title span {font:700 10px Arial,sans-serif;color:#8f674b}
.vault-v267-library-list {overflow-y:auto;min-height:0;flex:1;overscroll-behavior:contain}
.vault-v267-library-spell {display:flex;align-items:center;justify-content:space-between;gap:10px;width:100%;padding:10px 13px;border:0;border-bottom:1px solid #efe0d0;background:#fffdfa;text-align:left}
.vault-v267-library-spell:hover {background:#f9eee0}
.vault-v267-library-spell.is-active {background:#f1dfc7;box-shadow:inset 3px 0 #a56d49}
.vault-v267-library-spell.is-linked .vault-v267-spell-name strong:after {content:' ✓';color:#9a643f}
.vault-v267-spell-name {display:grid;gap:4px;min-width:0}
.vault-v267-spell-name strong {font:700 12px Georgia,serif;color:#713927}
.vault-v267-spell-name small {font:500 10px Arial,sans-serif;color:#977355}
.vault-v267-spell-meta {display:grid;gap:3px;justify-items:end;flex:none}
.vault-v267-spell-meta b {font:700 10px Arial,sans-serif;color:#805137}
.vault-v267-spell-meta small {font:700 9px Arial,sans-serif;color:#b08054}
.vault-v267-library-empty {padding:26px 14px;margin:0;text-align:center;color:#98775b;font:11px/1.6 Arial,sans-serif}
.vault-v267-load-more {padding:10px;width:100%;border:0;border-top:1px solid #e6ceb3;background:#f7eadb;color:#855436;font:700 11px Arial,sans-serif}
.vault-v267-library-preview {background:linear-gradient(155deg,#fffbf5,#f6e9d8)}
.vault-v267-preview-inner {display:flex;flex-direction:column;gap:12px;min-height:0;overflow-y:auto;padding:20px 18px;height:100%}
.vault-v267-preview-inner>small {font:800 9px Arial,sans-serif;letter-spacing:.1em;color:#a17a54}
.vault-v267-preview-inner h4 {margin:0;color:#693326;font:700 22px/1.2 Georgia,serif;overflow-wrap:anywhere}
.vault-v267-preview-tags {display:flex;flex-wrap:wrap;gap:6px}
.vault-v267-preview-tags span {border:1px solid #e0c2a0;border-radius:999px;padding:5px 8px;color:#895638;background:#fffaf3;font:700 9px Arial,sans-serif}
.vault-v267-preview-inner dl {display:grid;gap:9px;margin:0}
.vault-v267-preview-inner dl>div {display:grid;grid-template-columns:86px minmax(0,1fr);gap:8px;font:11px/1.35 Arial,sans-serif}
.vault-v267-preview-inner dt {font-weight:700;color:#997254}
.vault-v267-preview-inner dd {margin:0;color:#653a29;overflow-wrap:anywhere}
.vault-v267-preview-inner p {margin:auto 0 0;color:#99775d;font:500 11px/1.5 Arial,sans-serif}
.vault-v267-link-spell {min-height:42px;padding:10px;border:1px solid #713122;border-radius:9px;background:#743122;color:#fff9ef;font:700 11px Arial,sans-serif}
.vault-v267-link-spell:hover {background:#8b422b}
.vault-v267-preview-empty {flex:1;display:grid;align-content:center;justify-items:center;gap:10px;padding:24px;text-align:center;color:#997554}
.vault-v267-preview-empty strong {font:700 17px Georgia,serif;color:#713925}
.vault-v267-preview-empty span {font:11px/1.5 Arial,sans-serif}
.vault-v267-library-footer {display:flex;justify-content:space-between;align-items:center;gap:12px;padding:10px 18px;border-top:1px solid #dec4a6;background:#f7eee2}
.vault-v267-library-footer span {color:#9b7657;font:500 10px Arial,sans-serif}
.vault-v267-library-footer button {padding:9px 14px;border:1px solid #713021;border-radius:9px;background:#743122;color:#fff9ef;font:700 11px Arial,sans-serif}
@media(max-width:840px) {
 .vault-v267-library-content {grid-template-columns:minmax(0,1fr) minmax(220px,.85fr)}
 .vault-v267-books-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
 .vault-v267-library-head-title h3 {font-size:19px}
}
@media(max-width:650px) {
 .master-vault-v4-modal .vault-v267-quick-controls {grid-template-columns:1fr}
 .master-vault-v4-modal .vault-v267-open-book {min-width:0;width:100%}
 .master-vault-v4-modal .vault-v267-base-tag {display:none}
 .vault-v267-library-layer {padding:6px}
 .vault-v267-library-dialog {height:calc(100dvh - 12px);border-radius:12px}
 .vault-v267-library-head {padding:11px}
 .vault-v267-library-head-title p {display:none}
 .vault-v267-library-emblem {width:36px;height:36px}
 .vault-v267-library-toolbar {padding:8px 11px}
 .vault-v267-library-filters {grid-template-columns:repeat(2,minmax(0,1fr));padding:9px 11px}
 .vault-v267-library-search {grid-column:span 2}
 .vault-v267-library-content {grid-template-columns:1fr;grid-template-rows:minmax(140px,1fr) minmax(130px,.75fr);padding:9px 11px}
 .vault-v267-preview-inner {padding:13px}
 .vault-v267-preview-inner h4 {font-size:18px}
 .vault-v267-library-footer span {display:none}
 .vault-v267-library-footer {justify-content:flex-end;padding:8px}
}
@media(prefers-reduced-motion:reduce){.vault-v267-library-layer *{transition:none!important}}


/* Spellbound V66.2.69 — busca e fontes da biblioteca por campanhas. */
.vault-v267-library-books.vault-v269-library-books {max-height:350px;overflow-y:auto;overscroll-behavior:contain;padding:12px 18px 15px;background:linear-gradient(160deg,#f8efe3,#fcf8f2);}
.vault-v269-section-title,.vault-v269-books-top {display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px}
.vault-v269-section-title>div,.vault-v269-books-top>div {display:grid;gap:4px}
.vault-v269-section-title strong,.vault-v269-books-top strong {font:700 14px/1.25 Georgia,serif;color:#723322}
.vault-v269-section-title small,.vault-v269-books-top small {font:500 10px/1.4 Arial,sans-serif;color:#92735c}
.vault-v269-section-title>span {font:700 10px/1.3 Arial,sans-serif;color:#9a7458}
.vault-v269-campaign-section {padding:12px 14px 13px;margin-bottom:14px;border:1px solid #dfc7aa;border-radius:12px;background:linear-gradient(125deg,#fffdf9,#f7ecde)}
.vault-v269-campaign-list {display:flex;align-items:stretch;gap:8px;flex-wrap:wrap}
.vault-v269-campaign-choice {display:grid;gap:4px;align-content:center;min-height:38px;max-width:240px;padding:8px 12px;border:1px solid #dfc8ad;border-radius:9px;background:#fffdfa;color:#74442f;font:700 11px/1.3 Arial,sans-serif;text-align:left;transition:.15s}
.vault-v269-campaign-choice:hover {border-color:#b8916e;background:#fcf2e6}
.vault-v269-campaign-choice.is-active {border-color:#a86b49;background:#f2ddc7;box-shadow:inset 0 0 0 1px rgba(178,111,68,.1)}
.vault-v269-campaign-choice small {font:500 9px/1.3 Arial,sans-serif;color:#9b7556}
.vault-v269-campaign-empty,.vault-v269-campaign-note {margin:8px 0 0;color:#926e50;font:500 10px/1.45 Arial,sans-serif}
.vault-v269-books-top>button {flex:none;color:#955e40;font:700 10px Arial,sans-serif}
.vault-v269-book-search-row {display:flex;align-items:center;gap:8px;margin-bottom:12px}
.vault-v267-book-search.vault-v269-book-search {flex:1 1 auto;max-width:580px;height:39px;margin:0;border-radius:9px;background:#fffdfa;border-color:#d6b998}
.vault-v267-book-search.vault-v269-book-search input {font-size:12px}
.vault-v269-book-search-row>span {margin-left:auto;white-space:nowrap;font:700 10px Arial,sans-serif;color:#967154}
.vault-v269-search-clear {flex:none;display:grid;place-items:center;width:27px;height:27px;border:1px solid #e1ccb2;border-radius:7px;background:#fff9f0;color:#845339;font-size:16px}
.vault-v267-books-grid.vault-v269-books-grid {grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}
.vault-v269-book-card {min-width:0;border:1px solid #dec5aa;border-radius:10px;background:#fffdf9;box-shadow:inset 0 1px rgba(255,255,255,.8);overflow:hidden}
.vault-v269-book-card.is-active {border-color:#aa754f;background:#fbf2e7}
.vault-v269-book-toggle {display:flex;width:100%;min-height:40px;align-items:center;gap:8px;padding:9px 11px 5px;text-align:left;color:#663320}
.vault-v269-book-toggle .vault-v267-book-check {flex:none}
.vault-v269-book-name {min-width:0;font:700 11px/1.35 Arial,sans-serif;overflow-wrap:anywhere}
.vault-v269-book-footer {display:flex;align-items:center;justify-content:space-between;gap:8px;padding:2px 11px 9px}
.vault-v269-book-footer small {white-space:nowrap;color:#a1846c;font:500 9px Arial,sans-serif}
.vault-v269-book-footer button {color:#995b36;text-decoration:underline;text-underline-offset:2px;font:700 10px/1.3 Arial,sans-serif;white-space:nowrap}
.vault-v269-book-inspector {margin-top:12px;padding:13px 14px;border:1px solid #d8b894;border-radius:12px;background:#fff9ef}
.vault-v269-inspector-head {display:flex;align-items:center;justify-content:space-between;gap:12px}
.vault-v269-inspector-head>div {display:grid;gap:4px}
.vault-v269-inspector-head small {font:700 9px Arial,sans-serif;letter-spacing:.1em;color:#ad8058}
.vault-v269-inspector-head strong {font:700 15px/1.25 Georgia,serif;color:#713526;overflow-wrap:anywhere}
.vault-v269-inspector-head>button {width:26px;height:26px;border:1px solid #dfc6a8;border-radius:7px;background:#fffdfa;color:#85593d;font-size:18px}
.vault-v269-book-inspector>p {margin:8px 0 10px;font:500 10px/1.4 Arial,sans-serif;color:#967456}
.vault-v269-inspector-spells {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px}
.vault-v269-inspector-spells>button {display:flex;align-items:center;justify-content:space-between;gap:9px;min-height:35px;text-align:left;padding:7px 9px;border:1px solid #e3ccb1;border-radius:7px;background:#fffdfa;color:#713a29;font:600 11px/1.25 Arial,sans-serif}
.vault-v269-inspector-spells>button:hover {border-color:#b58358;background:#f8ebda}
.vault-v269-inspector-spells>button>span {overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vault-v269-inspector-spells>button>small {flex:none;color:#a17b5c;font:500 9px Arial,sans-serif}
.vault-v269-inspector-more {display:block;margin-top:9px;color:#925d3c;text-decoration:underline;font:700 10px Arial,sans-serif}
.vault-v269-filter-search-wrap {position:relative;min-width:0}
.vault-v269-filter-search-wrap .vault-v267-library-search {width:100%}
.vault-v269-filter-search-wrap .vault-v267-library-search input {padding-right:33px}
.vault-v269-clear-spell-search {position:absolute;right:8px;top:50%;transform:translateY(-50%);display:grid;place-items:center;width:25px;height:25px;border:1px solid #e4ccb3;border-radius:7px;background:#fff8ee;color:#875b3f;font-size:17px}
@media (max-width:950px) { .vault-v267-books-grid.vault-v269-books-grid,.vault-v269-inspector-spells {grid-template-columns:repeat(2,minmax(0,1fr))} }
@media (max-width:620px) { .vault-v267-books-grid.vault-v269-books-grid,.vault-v269-inspector-spells {grid-template-columns:1fr} .vault-v269-section-title {align-items:flex-start;flex-direction:column} .vault-v269-book-search-row>span {display:none} }


/* ================================================================
   Spellbound V66.2.70 — Biblioteca editorial em abas, sem empilhamento.
   Os verbetes são exibidos a partir do texto existente no Grimório.
   ================================================================ */
.vault-v270-library-dialog {width:min(1190px,calc(100vw - 28px));height:min(860px,calc(100dvh - 28px));background:#fdf9f2}
.vault-v270-head {flex:none;padding:17px 23px;background:linear-gradient(105deg,#f2e5d5,#fcf7ef);border-bottom:1px solid #dfc5a4}
.vault-v270-tabs {display:flex;align-items:stretch;gap:5px;flex:none;min-height:50px;padding:7px 20px 0;background:#faf3e9;border-bottom:1px solid #ddc3a3}
.vault-v270-tabs>button {display:inline-flex;align-items:center;gap:9px;min-width:160px;padding:10px 16px 11px;border:1px solid transparent;border-bottom:0;border-radius:10px 10px 0 0;color:#8c654c;background:transparent;font:700 12px/1.25 Arial,sans-serif;text-align:left}
.vault-v270-tabs>button:hover {background:#f4e6d4;color:#713a26}
.vault-v270-tabs>button.is-current {background:#fffbf5;border-color:#ddc3a3;color:#6f3525;box-shadow:inset 0 3px #a86b48}
.vault-v270-tabs>button svg {width:17px;height:17px;flex:none}
.vault-v270-tabs>button small {margin-left:4px;color:#97775e;font:600 10px Arial,sans-serif;white-space:nowrap}
.vault-v270-tab-note {margin:auto 0 auto auto;color:#9a785d;font:600 10px Arial,sans-serif;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:220px}
.vault-v270-spell-tab,.vault-v270-books-tab {display:flex;flex:1;min-height:0;flex-direction:column;overflow:hidden}
.vault-v270-filters {flex:none;grid-template-columns:minmax(0,1fr) 155px 170px;gap:10px;padding:13px 19px;background:#fffcf7}
.vault-v270-filters .vault-v267-library-search,.vault-v270-filters .vault-v267-filter {height:42px;border-radius:9px}
.vault-v270-spell-content {grid-template-columns:minmax(310px,.78fr) minmax(0,1.22fr);min-height:0;gap:12px;padding:13px 18px 16px;background:#fcf7ee}
.vault-v270-list-pane {border-color:#ddc5a6;background:#fffdf9}
.vault-v270-list-pane .vault-v267-list-title {padding:12px 14px;background:#f4e7d7}
.vault-v270-spell-row {min-height:61px;padding:10px 14px}
.vault-v270-spell-row .vault-v267-spell-name strong {font:700 13px/1.2 Georgia,serif}
.vault-v270-spell-row .vault-v267-spell-name small {font-size:10px}
.vault-v270-preview-pane {border-color:#d5b995;background:#fffaf0}
.vault-v270-spell-detail {display:flex;flex:1;flex-direction:column;min-height:0}
.vault-v270-spell-paper {flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain;padding:25px 30px 22px;background:linear-gradient(130deg,#fffaf1,#fff9ed 65%,#f8edda)}
.vault-v270-book-folio {display:flex;justify-content:space-between;gap:12px;margin:0 0 19px;padding-bottom:10px;border-bottom:1px solid #dbc5a4;color:#a17a59;font:700 9px/1.3 Arial,sans-serif;letter-spacing:.13em}
.vault-v270-spell-paper h4 {margin:0 0 5px;font:700 25px/1.16 Georgia,'Times New Roman',serif;font-variant:small-caps;color:#652d20;overflow-wrap:anywhere}
.vault-v270-spell-subtitle {margin-bottom:19px;color:#683a2b;font:italic 600 14px/1.4 Georgia,serif}
.vault-v270-spell-facts {display:grid;gap:6px;margin:0 0 17px}
.vault-v270-spell-facts>div {display:flex;align-items:baseline;gap:5px;color:#513529;font:13px/1.45 Georgia,serif}
.vault-v270-spell-facts dt {flex:none;font-weight:700}
.vault-v270-spell-facts dd {margin:0;overflow-wrap:anywhere}
.vault-v270-spell-flags {display:flex;gap:7px;margin:-5px 0 15px}
.vault-v270-spell-flags span {padding:4px 8px;border:1px solid #dec19d;border-radius:999px;background:#faeddb;color:#835537;font:700 10px Arial,sans-serif}
.vault-v270-spell-description {font:14px/1.62 Georgia,'Times New Roman',serif;color:#3c3028;overflow-wrap:anywhere}
.vault-v270-spell-description p {margin:0 0 12px;font:inherit;color:inherit}
.vault-v270-spell-description p + p {text-indent:1.1em}
.vault-v270-spell-description strong,.vault-v270-spell-description b {font-weight:700;color:#392c25}
.vault-v270-spell-description :is(ul,ol) {margin:8px 0 13px 20px;padding:0;list-style:disc}
.vault-v270-spell-description h3,.vault-v270-spell-description h4 {font:700 15px/1.35 Georgia,serif;color:#653021;margin:12px 0 6px}
.vault-v270-spell-description blockquote {margin:9px 0;border-left:2px solid #d1ae82;padding-left:11px;color:#6b4d36}
.vault-v270-no-description {font:italic 12px/1.5 Georgia,serif;color:#977353}
.vault-v270-higher {margin-top:18px;padding-top:13px;border-top:1px solid #dcc8a9}
.vault-v270-higher h5 {font:700 14px/1.3 Georgia,serif;color:#6e3627;margin:0 0 7px}
.vault-v270-preview-action {display:flex;flex-direction:column;gap:9px;flex:none;padding:12px 17px;background:#f7eada;border-top:1px solid #d8bf9c}
.vault-v270-preview-action small {font:500 10px/1.4 Arial,sans-serif;color:#937254}
.vault-v270-preview-action .vault-v267-link-spell {min-height:42px;font-size:12px}
.vault-v270-preview-empty {background:linear-gradient(140deg,#fffaf1,#f7ead8)}
.vault-v270-footer {flex:none;padding:10px 18px;min-height:56px}
.vault-v270-footer span {font:600 10px Arial,sans-serif}
/* Books screen: one library browser and one inspector, not stacked catalogues. */
.vault-v270-campaign-bar {flex:none;padding:14px 19px 12px;background:linear-gradient(110deg,#f7ecdd,#fcf8f2);border-bottom:1px solid #dfc6a8}
.vault-v270-section-label {display:flex;align-items:baseline;gap:10px;flex-wrap:wrap;margin-bottom:10px}
.vault-v270-section-label strong {color:#693223;font:700 15px Georgia,serif}
.vault-v270-section-label small,.vault-v270-campaign-bar>small {font:500 10px/1.4 Arial,sans-serif;color:#947459}
.vault-v270-campaign-choices {display:flex;gap:7px;overflow-x:auto;overscroll-behavior:contain;padding-bottom:2px}
.vault-v270-campaign-choices button {display:grid;gap:3px;align-content:center;min-width:110px;max-width:230px;min-height:43px;flex:none;padding:8px 12px;border:1px solid #dbc3a7;border-radius:9px;background:#fffaf3;color:#77452e;font:700 11px/1.3 Arial,sans-serif;text-align:left}
.vault-v270-campaign-choices button:hover {border-color:#b38660}
.vault-v270-campaign-choices button.is-active {border-color:#aa6e4c;background:#eed8c0;box-shadow:inset 0 1px rgba(255,255,255,.7)}
.vault-v270-campaign-choices button span {overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vault-v270-campaign-choices button small {color:#9b765a;font:500 9px Arial,sans-serif}
.vault-v270-books-workspace {display:grid;grid-template-columns:minmax(270px,.85fr) minmax(0,1.15fr);gap:12px;flex:1;min-height:0;padding:13px 18px 16px;background:#fcf7ee}
.vault-v270-book-browser,.vault-v270-book-detail {display:flex;flex-direction:column;min-width:0;min-height:0;border:1px solid #dec4a5;border-radius:11px;background:#fffdf9;overflow:hidden}
.vault-v270-book-browser-head {display:flex;align-items:center;justify-content:space-between;gap:7px;padding:12px 14px;border-bottom:1px solid #ead7c0;background:#f6e9d8}
.vault-v270-book-browser-head strong {font:700 13px Georgia,serif;color:#6b3525}
.vault-v270-book-browser-head span {font:600 10px Arial,sans-serif;color:#987251}
.vault-v270-book-search {display:flex;align-items:center;gap:8px;flex:none;margin:10px 12px;padding:0 11px;height:39px;border:1px solid #dac0a1;border-radius:8px;color:#a17b59;background:#fffaf4}
.vault-v270-book-search svg {width:16px;height:16px;flex:none}
.vault-v270-book-search input,.vault-v270-inspector-search input {width:100%;min-width:0;border:0;background:transparent;outline:0;color:#673a2a;font:11px Arial,sans-serif}
.vault-v270-book-results {flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain}
.vault-v270-book-line {display:flex;align-items:center;gap:8px;min-height:54px;border-bottom:1px solid #eee0d0;background:#fffdf9;padding:5px 9px 5px 12px}
.vault-v270-book-line:hover {background:#f9f0e4}
.vault-v270-book-line.is-selected {background:#f3e3d1;box-shadow:inset 3px 0 #a76c48}
.vault-v270-book-line.is-enabled .vault-v270-book-explore strong:after {content:' · Ativo';font:600 9px Arial,sans-serif;color:#9b6a46}
.vault-v270-book-explore {display:grid;flex:1;min-width:0;gap:4px;text-align:left}
.vault-v270-book-explore strong {font:700 12px/1.25 Georgia,serif;color:#713c2a;overflow-wrap:anywhere}
.vault-v270-book-explore small {font:500 10px Arial,sans-serif;color:#927259}
.vault-v270-book-enable {display:grid;place-items:center;flex:none;width:29px;height:29px;border:1px solid #d8b997;border-radius:7px;color:#9c613d;background:#fff9ee;font-size:16px}
.vault-v270-book-enable[aria-pressed='true'] {background:#9b6341;color:#fffdf5;border-color:#9b6341}
.vault-v270-clear-books {flex:none;padding:10px;text-align:center;color:#8f593b;background:#faf0e3;border-top:1px solid #e2cab0;font:700 10px Arial,sans-serif}
.vault-v270-book-detail {background:linear-gradient(145deg,#fffaf1,#fdf5ea)}
.vault-v270-inspector-layout {display:flex;flex-direction:column;flex:1;min-height:0}
.vault-v270-inspector-title {display:grid;grid-template-columns:minmax(0,1fr) auto;gap:4px 10px;align-items:center;padding:17px 18px 12px;border-bottom:1px solid #e5d0b5}
.vault-v270-inspector-title>small {grid-column:1 / -1;color:#ac7d55;font:700 9px Arial,sans-serif;letter-spacing:.12em}
.vault-v270-inspector-title h4 {font:700 20px/1.2 Georgia,serif;color:#6d3324;overflow-wrap:anywhere;margin:0}
.vault-v270-inspector-title>span {color:#967052;font:600 10px Arial,sans-serif}
.vault-v270-inspector-title>button {grid-column:1 / -1;justify-self:start;padding:8px 13px;border:1px solid #b78a64;border-radius:8px;color:#7b462f;font:700 11px Arial,sans-serif;background:#f7e8d4}
.vault-v270-inspector-title>button.is-active {background:#9d6342;color:#fffaf2;border-color:#9d6342}
.vault-v270-inspector-search {display:flex;align-items:center;height:38px;margin:11px 15px;padding:0 11px;border:1px solid #dec5a8;border-radius:8px;background:#fffdfa}
.vault-v270-inspector-list {flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain}
.vault-v270-inspector-list>button {display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;min-height:48px;border-bottom:1px solid #ecddca;padding:9px 16px;background:#fffaf3;text-align:left;color:#713b28}
.vault-v270-inspector-list>button:hover {background:#f1dfc7}
.vault-v270-inspector-list>button>span:first-child {display:grid;min-width:0;gap:4px}
.vault-v270-inspector-list>button strong {font:700 12px Georgia,serif;overflow-wrap:anywhere}
.vault-v270-inspector-list>button small,.vault-v270-inspector-list>button>span:last-child {color:#9b7657;font:600 10px Arial,sans-serif;white-space:nowrap}
.vault-v270-inspector-empty {display:grid;place-content:center;justify-items:center;gap:9px;flex:1;padding:28px;text-align:center}
.vault-v270-inspector-empty strong {color:#6f3525;font:700 19px Georgia,serif}
.vault-v270-inspector-empty span {color:#98765b;font:11px/1.5 Arial,sans-serif;max-width:270px}
@media (max-width:890px){
 .vault-v270-spell-content {grid-template-columns:minmax(245px,.8fr) minmax(0,1.2fr)}
 .vault-v270-spell-paper {padding:18px 19px}
 .vault-v270-tabs>button {min-width:0;padding:10px 12px}
 .vault-v270-tab-note {display:none}
}
@media (max-width:680px){
 .vault-v270-library-dialog {width:calc(100vw - 12px);height:calc(100dvh - 12px)}
 .vault-v270-head {padding:11px 13px}
 .vault-v270-tabs {padding:5px 9px 0}
 .vault-v270-tabs>button {flex:1;font-size:11px;gap:5px}
 .vault-v270-tabs>button small {font-size:9px}
 .vault-v270-spell-content {grid-template-columns:1fr;grid-template-rows:minmax(120px,.36fr) minmax(180px,.64fr);padding:8px;gap:8px}
 .vault-v270-filters {grid-template-columns:1fr 1fr;padding:9px}
 .vault-v270-filters .vault-v269-filter-search-wrap {grid-column:1/-1}
 .vault-v270-filters .vault-v267-library-search {grid-column:auto}
 .vault-v270-spell-paper {padding:15px 17px}
 .vault-v270-spell-paper h4 {font-size:21px}
 .vault-v270-book-folio {margin-bottom:10px}
 .vault-v270-books-workspace {grid-template-columns:1fr;grid-template-rows:minmax(110px,.4fr) minmax(140px,.6fr);padding:8px;gap:8px}
 .vault-v270-campaign-bar {padding:9px}
 .vault-v270-section-label small {display:none}
 .vault-v270-campaign-choices button {min-height:34px;padding:6px 9px}
 .vault-v270-footer {min-height:45px;padding:7px 10px}
}
@media (prefers-reduced-motion:reduce) {.vault-v270-library-layer * {scroll-behavior:auto!important}}


/* V66.2.72 — Prévia usa a mesma apresentação do Grimório do Player.
   As regras de scaledamage/HTML são tratadas pelo SpellDisplayService. */
.vault-v272-reader {
    --spellbook-ink:#2f211b;--spellbook-title:#5b1b14;
    --spellbook-label:#704326;--spellbook-accent:#70291f;
    --spellbook-muted:#8b6951;--spellbook-border-strong:#d7ba98;
    --spellbook-paper-strong:#fffaf2;
    --spellbook-shadow-strong:0 14px 30px rgba(68,32,17,.17);
    color:var(--spellbook-ink);
}
.vault-v272-reader .vault-v272-identity {margin:0 0 15px}
.vault-v272-reader .vault-v272-title-line {display:flex;align-items:baseline;justify-content:space-between;gap:15px}
.vault-v270-spell-paper .vault-v272-reader h4 {
    margin:0;font:800 27px/1.1 Georgia,'Times New Roman',serif;
    font-variant:normal;color:#5b1b14;letter-spacing:-.015em;
}
.vault-v272-reader .vault-v272-source {flex:none;color:#94735d;font:800 9px/1.3 Arial,sans-serif;letter-spacing:.07em;text-transform:uppercase}
.vault-v272-reader .vault-v272-subtitle {display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin:5px 0 0;font:italic 600 13px/1.45 Georgia,serif;color:#4b362c}
.vault-v272-reader .vault-v272-ritual {display:inline-flex;border-radius:999px;padding:3px 7px;background:#e2edf0;color:#365e69;font:800 9px/1 Arial,sans-serif;font-style:normal;text-transform:uppercase}
.vault-v272-reader .vault-v272-facts {display:grid;gap:3px;margin:0 0 16px;color:#2f211b;font:14px/1.55 Georgia,'Times New Roman',serif}
.vault-v272-reader .vault-v272-facts>div {display:block}
.vault-v272-reader .vault-v272-facts dt {display:inline;font-weight:800;color:#3f1712}
.vault-v272-reader .vault-v272-facts dd {display:inline;margin-left:5px;overflow-wrap:anywhere}
.vault-v272-reader .vault-v272-rich-copy {
    color:var(--spellbook-ink);font:15px/1.72 Georgia,'Times New Roman',serif;
    overflow-wrap:anywhere;
}
.vault-v272-reader .spell-reader-copy > p {margin:0!important}
.vault-v272-reader .spell-reader-copy > p + p {margin-top:.8rem!important}
.vault-v272-reader .spell-reader-copy > ul,.vault-v272-reader .spell-reader-copy > ol {margin:.65rem 0 0 1.25rem!important}
.vault-v272-reader .spell-reader-copy li + li {margin-top:.25rem!important}
.vault-v272-reader .spell-reader-copy strong {color:var(--spellbook-title);font-weight:800}
.vault-v272-reader .vault-v272-higher {margin-top:16px}
.vault-v272-reader .vault-v272-higher-line {margin:0;color:#33241d;font:15px/1.72 Georgia,'Times New Roman',serif}
.vault-v272-reader .vault-v272-higher-line>strong {color:#4f1812;font-weight:800}
.vault-v272-reader .vault-v272-higher-line .vault-v272-rich-copy {margin-left:5px;display:inline}
.vault-v272-reader .vault-v272-higher-line .vault-v272-rich-copy > p {display:inline}
.vault-v272-reader .vault-v272-scaling {margin-top:10px;color:#725947;font:11px/1.8 Arial,sans-serif}
.vault-v272-reader .vault-v272-scaling p {margin:0 0 3px}
.vault-v272-reader .vault-v272-scaling strong {margin-right:7px;color:#5d1d15}
@media(max-width:640px) {
    .vault-v270-spell-paper .vault-v272-reader h4 {font-size:22px}
    .vault-v272-reader .vault-v272-facts {font-size:13px}
    .vault-v272-reader .vault-v272-rich-copy,.vault-v272-reader .vault-v272-higher-line {font-size:14px}
}

/* Estilos de marcações e tooltips iguais aos do Grimório */
    .vault-v272-reader .spell-rich-copy {
        color: var(--spellbook-ink);
        font-size: 0.92rem;
        line-height: 1.85;
    }

    .vault-v272-reader .spell-rich-copy br + br {
        display: block;
        content: '';
        margin-top: .8rem;
    }

    .vault-v272-reader .spell-roll-value {
        color: var(--spellbook-accent);
        font-weight: 900;
        white-space: nowrap;
    }

    .vault-v272-reader .spell-damage-type {
        color: color-mix(in srgb, var(--spellbook-accent) 78%, var(--spellbook-label) 22%);
        font-weight: 900;
    }

    .vault-v272-reader .spell-rich-text {
        color: var(--spellbook-ink);
        font-size: 0.86rem;
        line-height: 1.75;
    }

    .vault-v272-reader .spell-rich-text br + br {
        display: block;
        content: '';
        margin-top: .65rem;
    }

    .vault-v272-reader .spell-rule-term {
        position: relative;
        display: inline;
        color: var(--spellbook-accent);
        font-weight: 800;
        text-decoration-line: underline;
        text-decoration-style: dotted;
        text-decoration-thickness: 1px;
        text-underline-offset: 2px;
        cursor: help;
        outline: none;
    }

    .vault-v272-reader .spell-rule-term::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -1px;
        height: 2px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--spellbook-accent) 18%, transparent);
    }

    .vault-v272-reader .spell-rule-tooltip {
        position: absolute;
        z-index: 180;
        left: 50%;
        bottom: calc(100% + 9px);
        width: min(300px, 72vw);
        transform: translate(-50%, 5px);
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        border: 1px solid var(--spellbook-border-strong);
        border-radius: 11px;
        background: var(--spellbook-paper-strong);
        padding: 10px 11px;
        box-shadow: var(--spellbook-shadow-strong);
        color: var(--spellbook-ink);
        text-align: left;
        white-space: normal;
        transition: opacity .14s ease, transform .14s ease, visibility .14s ease;
    }

    .vault-v272-reader .spell-rule-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        width: 9px;
        height: 9px;
        transform: translate(-50%, -5px) rotate(45deg);
        border-right: 1px solid var(--spellbook-border-strong);
        border-bottom: 1px solid var(--spellbook-border-strong);
        background: var(--spellbook-paper-strong);
    }

    .vault-v272-reader .spell-rule-term:hover .spell-rule-tooltip,
    .vault-v272-reader .spell-rule-term:focus .spell-rule-tooltip,
    .vault-v272-reader .spell-rule-term:focus-visible .spell-rule-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, 0);
    }

    .vault-v272-reader .spell-rule-tooltip-kind {
        display: block;
        color: var(--spellbook-label);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .vault-v272-reader .spell-rule-tooltip-title {
        display: block;
        margin-top: 2px;
        color: var(--spellbook-title);
        font-family: var(--theme-font-display, Georgia, 'Times New Roman', serif);
        font-size: 13px;
        line-height: 1.25;
    }

    .vault-v272-reader .spell-rule-tooltip-copy {
        display: block;
        margin-top: 5px;
        color: var(--spellbook-muted);
        font-size: 10px;
        font-weight: 500;
        line-height: 1.55;
    }



/* V66.2.73 — Tooltips de regras limitados à coluna do verbete. */
.vault-v270-library-dialog .vault-v272-reader .spell-rule-tooltip {
    width: min(262px, 70vw);
    max-height: min(250px, 60vh);
    overflow: hidden;
    box-sizing: border-box;
}
.vault-v270-library-dialog .vault-v272-reader .spell-rule-tooltip.vault-v273-tip-below {
    bottom: auto;
    top: calc(100% + 9px);
}
.vault-v270-library-dialog .vault-v272-reader .spell-rule-tooltip.vault-v273-tip-below::after {
    top: auto;
    bottom: 100%;
    transform: translate(-50%, 5px) rotate(225deg);
}
/* Resumo da habilidade automática do pergaminho (sem mais um card enorme). */
.master-vault-v4-modal .vault-v273-casting-note {
    display:flex;align-items:center;gap:10px;margin-top:11px;padding:11px 13px;
    border:1px solid #e0c7a7;border-radius:10px;
    background:linear-gradient(115deg,#f8f0e4,#fffbf5);color:#703b29;
}
.master-vault-v4-modal .vault-v273-casting-icon {
    flex:none;display:grid;place-items:center;width:30px;height:30px;
    border:1px solid #d7b28c;border-radius:8px;background:#fff9ec;color:#af7d49;
}
.master-vault-v4-modal .vault-v273-casting-note>div {flex:1;min-width:0;display:grid;gap:3px}
.master-vault-v4-modal .vault-v273-casting-note strong {font:700 13px/1.2 Georgia,serif}
.master-vault-v4-modal .vault-v273-casting-note small {font:500 10px/1.4 Arial,sans-serif;color:#967456}
.master-vault-v4-modal .vault-v273-casting-note button {
    flex:none;border:1px solid #c99f77;border-radius:8px;padding:8px 11px;
    background:#fff8ed;color:#80472d;font:700 10px/1.2 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v273-casting-note button:hover {background:#f2dfca}
@media(max-width:620px) {
    .master-vault-v4-modal .vault-v273-casting-note {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v273-casting-note button {margin-left:auto}
}


/* V66.2.74 — Pergaminhos no formato de duas habilidades históricas. */
.master-vault-v4-modal .vault-v274-scroll-abilities > div:first-of-type {flex:1;min-width:0}
.master-vault-v4-modal .vault-v274-scroll-abilities .vault-v274-spell-line {
    display:block;margin-top:3px;font:700 11px/1.45 Georgia,serif;color:#74452f;
}
.master-vault-v4-modal .vault-v274-scroll-abilities > .vault-v274-scroll-actions {display:flex;flex-wrap:wrap;gap:7px;flex:none}
.master-vault-v4-modal .vault-v274-scroll-actions button {white-space:nowrap}
.master-vault-v4-modal .vault-v274-scroll-stats {
    border:1px solid #e3ccb0;border-radius:10px;background:#fffbf6;margin-top:8px;
}
.master-vault-v4-modal .vault-v274-scroll-stats summary {
    cursor:pointer;list-style:none;padding:11px 13px;font:700 11px/1.4 Arial,sans-serif;color:#7c4c36;
}
.master-vault-v4-modal .vault-v274-scroll-stats summary::-webkit-details-marker {display:none}
.master-vault-v4-modal .vault-v274-scroll-stats summary::before {content:'▸';margin-right:8px;color:#ad7d50}
.master-vault-v4-modal .vault-v274-scroll-stats[open] summary::before {content:'▾'}
.master-vault-v4-modal .vault-v274-scroll-stats summary span {font-weight:500;color:#a28065;margin-left:5px}
.master-vault-v4-modal .vault-v274-scroll-stats-grid {
    display:flex;align-items:end;flex-wrap:wrap;gap:9px;padding:0 13px 13px;border-top:1px solid #f0dfca;
}
.master-vault-v4-modal .vault-v274-scroll-stats-grid label {display:grid;gap:5px;margin-top:11px;font:700 10px/1.2 Arial,sans-serif;color:#76513a}
.master-vault-v4-modal .vault-v274-scroll-stats-grid input {
    width:112px;min-height:37px;padding:8px 10px;border-radius:8px;background:#fffdfa;
    border:1px solid #d9bfa0;color:#673b28;font:700 15px Georgia,serif;
}
.master-vault-v4-modal .vault-v274-scroll-stats-grid button {
    border:1px solid #dec5a4;border-radius:8px;padding:10px 12px;
    background:#fff9f1;color:#83543b;font:700 10px Arial,sans-serif;
}
@media(max-width:760px) {
    .master-vault-v4-modal .vault-v274-scroll-abilities {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v274-scroll-actions {width:100%}
}


/* V66.2.75 — sugestões de raridade para pergaminhos, sem afetar outros itens. */
.master-vault-v4-modal .vault-v275-spell-defaults {display:flex;align-items:center;flex-wrap:wrap;gap:8px 13px;margin:10px 0 11px;padding:10px 13px;border:1px solid #e0c6a4;border-radius:11px;background:linear-gradient(110deg,#faf0e2,#fffaf3);color:#8f674c;font-size:11px}
.master-vault-v4-modal .vault-v275-spell-defaults strong {color:#673026}
.master-vault-v4-modal .vault-v275-spell-defaults>span:nth-child(2){padding-left:11px;border-left:1px solid #ddc3a5;font-weight:700;color:#77432d}
.master-vault-v4-modal .vault-v275-spell-defaults button {margin-left:auto;padding:6px 9px;border:1px solid #c99e77;border-radius:8px;background:#fffaf2;color:#7e3f2c;font-weight:700;cursor:pointer}
.master-vault-v4-modal .vault-v275-spell-defaults button:hover {background:#f3e0ca}
@media(max-width:640px){.master-vault-v4-modal .vault-v275-spell-defaults>span:nth-child(2){border:0;padding-left:0}.master-vault-v4-modal .vault-v275-spell-defaults button{margin-left:0}}

/* V66.2.76 — Pergaminho: três passos claros, sem cartões redundantes. */
.master-vault-v4-modal .vault-v276-scroll-builder {
    display:grid;gap:0;padding:0;overflow:visible;
    border:1px solid #ddc2a1;border-radius:15px;
    background:linear-gradient(145deg,#fffdf9,#faf4ea 92%);
    box-shadow:inset 0 1px 0 #fff;
}
.master-vault-v4-modal .vault-v276-step-title {
    display:flex;align-items:center;gap:11px;min-width:0;padding:15px 17px 11px;
}
.master-vault-v4-modal .vault-v276-step-number {
    display:grid;place-items:center;flex:none;width:33px;height:33px;
    border:1px solid #d4b28b;border-radius:10px;
    background:linear-gradient(145deg,#f6e7d2,#fffaf2);
    color:#a2754d;font:700 11px Georgia,serif;letter-spacing:.04em;
}
.master-vault-v4-modal .vault-v276-step-copy {display:grid;gap:3px;min-width:0;flex:1}
.master-vault-v4-modal .vault-v276-step-copy strong {color:#673126;font:700 16px/1.18 Georgia,serif}
.master-vault-v4-modal .vault-v276-step-copy small {color:#9b7559;font:500 10px/1.4 Arial,sans-serif}
.master-vault-v4-modal .vault-v276-collection-status {
    flex:none;border:1px solid #e4ceb2;border-radius:999px;
    padding:5px 10px;background:#fffaf2;color:#977052;
    font:700 10px/1.25 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v276-search-controls {padding:0 17px 14px}
.master-vault-v4-modal .vault-v276-search-controls .vault-v267-quick-search,
.master-vault-v4-modal .vault-v276-search-controls .vault-v267-open-book {height:43px}
.master-vault-v4-modal .vault-v276-search-controls .vault-v267-open-book {
    min-width:138px;border-radius:10px;background:linear-gradient(145deg,#f7ebdc,#eddbc6);
}
.master-vault-v4-modal .vault-v276-selected {
    display:flex;align-items:center;gap:12px;margin:0 17px 15px;padding:10px 12px;
    border:1px solid #dcc09d;border-radius:11px;
    background:linear-gradient(105deg,#f4e7d7,#fcf7ef);
}
.master-vault-v4-modal .vault-v276-selected-check {
    display:grid;place-items:center;flex:none;width:30px;height:30px;
    border-radius:9px;background:#956343;color:#fffaf5;font:700 13px Arial,sans-serif;
}
.master-vault-v4-modal .vault-v276-selected-details {display:grid;flex:1;gap:4px;min-width:0}
.master-vault-v4-modal .vault-v276-selected-details strong {font:700 16px/1.18 Georgia,serif;color:#613025;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v276-selected-details small {color:#8d6a50;font:600 10px/1.35 Arial,sans-serif}
.master-vault-v4-modal .vault-v276-selected>button {
    flex:none;padding:7px 12px;border-radius:8px;
    border:1px solid #d1ad85;background:#fffaf2;color:#76452f;
    font:700 11px Arial,sans-serif;transition:background .15s,border-color .15s;
}
.master-vault-v4-modal .vault-v276-selected>button:hover {background:#f2ddc6;border-color:#ae7a51}
.master-vault-v4-modal .vault-v276-configuration,
.master-vault-v4-modal .vault-v276-features {border-top:1px solid #e6d0b5;padding:0 17px 15px}
.master-vault-v4-modal .vault-v276-subheading {padding:15px 0 12px}
.master-vault-v4-modal .vault-v276-subheading .vault-v276-step-number {
    width:30px;height:30px;border-color:#dfc8ad;background:#fff9ef;color:#9b7456;
}
.master-vault-v4-modal .vault-v276-subheading .vault-v276-step-copy strong {font-size:14px}
.master-vault-v4-modal .vault-v276-stat-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px}
.master-vault-v4-modal .vault-v276-stat {
    min-width:0;display:grid;align-content:center;gap:6px;
    min-height:64px;padding:10px 12px;border:1px solid #e1c9aa;
    border-radius:10px;background:#fffdf9;box-shadow:inset 0 1px 0 #fff;
}
.master-vault-v4-modal .vault-v276-stat>span {text-transform:uppercase;letter-spacing:.055em;color:#9b785d;font:700 9px/1.2 Arial,sans-serif}
.master-vault-v4-modal .vault-v276-stat>strong {color:#69352a;font:700 16px/1.2 Georgia,serif;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v276-stat:last-child>strong {font:700 11px/1.4 Arial,sans-serif}
.master-vault-v4-modal .vault-v276-advanced {margin-top:9px;background:transparent;border-color:#e5d2bc}
.master-vault-v4-modal .vault-v276-advanced summary {display:flex;align-items:center;justify-content:space-between;gap:8px;padding:10px 12px;font-size:10px}
.master-vault-v4-modal .vault-v276-advanced summary::before {display:none}
.master-vault-v4-modal .vault-v276-advanced summary svg {height:14px;width:14px;transition:transform .2s;flex:none}
.master-vault-v4-modal .vault-v276-advanced[open] summary svg {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v276-feature-list {
    overflow:hidden;border:1px solid #dec5a5;border-radius:11px;background:#fffdf9;
}
.master-vault-v4-modal .vault-v276-feature-row {
    display:flex;align-items:center;gap:12px;padding:12px 13px;
}
.master-vault-v4-modal .vault-v276-feature-row+.vault-v276-feature-row {border-top:1px solid #e9d8c3}
.master-vault-v4-modal .vault-v276-feature-index {
    display:grid;place-items:center;flex:none;width:31px;height:31px;
    border-radius:9px;background:#f5e9d9;border:1px solid #ddc1a0;
    font:700 11px Georgia,serif;color:#a26f43;
}
.master-vault-v4-modal .vault-v276-feature-content {flex:1;min-width:0;display:grid;gap:5px}
.master-vault-v4-modal .vault-v276-feature-content strong {color:#643126;font:700 12px/1.3 Georgia,serif;overflow-wrap:anywhere}
.master-vault-v4-modal .vault-v276-feature-content small {font:500 10px/1.4 Arial,sans-serif;color:#977356}
.master-vault-v4-modal .vault-v276-feature-row button {
    flex:none;display:inline-flex;align-items:center;gap:10px;min-height:32px;
    border:1px solid #d9b994;border-radius:8px;padding:7px 10px;
    background:#fff9ef;font:700 10px Arial,sans-serif;color:#7a452d;
}
.master-vault-v4-modal .vault-v276-feature-row button:hover {background:#f2e1cb;border-color:#b78b66}
.master-vault-v4-modal .vault-v276-feature-row button span {font-size:17px;line-height:0}
.master-vault-v4-modal .vault-v276-scroll-builder>.vault-v267-quick-empty {margin:0 17px 16px}
@media(max-width:850px){
    .master-vault-v4-modal .vault-v276-stat-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:580px){
    .master-vault-v4-modal .vault-v276-step-title {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v276-collection-status {margin-left:43px}
    .master-vault-v4-modal .vault-v276-search-controls {grid-template-columns:minmax(0,1fr)}
    .master-vault-v4-modal .vault-v276-feature-row {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v276-feature-row button {margin-left:43px}
    .master-vault-v4-modal .vault-v276-selected {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v276-selected>button {margin-left:auto}
}
@media(prefers-reduced-motion:reduce){
    .master-vault-v4-modal .vault-v276-scroll-builder *{transition:none!important}
}

/* V66.2.78 — Editor de pergaminhos: consulta e parâmetros em duas zonas,
   com habilidades em sequência. Mantém a mecânica Alpine existente. */
.master-vault-v4-modal .vault-v278-builder {
    display:block;overflow:visible;padding:0;border:1px solid #dfc7a9;
    border-radius:15px;background:linear-gradient(145deg,#fffdf9,#fcf8f2);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.9);
}
.master-vault-v4-modal .vault-v278-columns {
    display:grid;grid-template-columns:minmax(0,1.55fr) minmax(280px,.85fr);
    min-width:0;gap:0;
}
.master-vault-v4-modal .vault-v278-selection {padding:20px 20px 19px;min-width:0}
.master-vault-v4-modal .vault-v278-section-head {display:flex;align-items:center;gap:11px;margin-bottom:17px;min-width:0}
.master-vault-v4-modal .vault-v278-section-icon {
    display:grid;place-items:center;flex:none;width:39px;height:39px;border:1px solid #dbc2a4;
    border-radius:11px;background:linear-gradient(140deg,#fffaf1,#f3e5d2);color:#a16c42;
}
.master-vault-v4-modal .vault-v278-section-icon svg {width:21px;height:21px}
.master-vault-v4-modal .vault-v278-section-head>div {display:grid;gap:3px;min-width:0;flex:1}
.master-vault-v4-modal .vault-v278-section-head strong {
    font:700 18px/1.12 Georgia,'Times New Roman',serif;color:#6a3225;
}
.master-vault-v4-modal .vault-v278-section-head small {font:500 11px/1.35 Arial,sans-serif;color:#956f53}
.master-vault-v4-modal .vault-v278-count {
    flex:none;border-radius:999px;border:1px solid #e4d1b9;padding:5px 9px;
    background:#fffaf3;color:#927354;font:700 10px/1.3 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v278-search-row {grid-template-columns:minmax(0,1fr) auto;gap:8px}
.master-vault-v4-modal .vault-v278-search-row .vault-v267-quick-search {height:44px;border-radius:10px}
.master-vault-v4-modal .vault-v278-search-row .vault-v267-open-book {
    height:44px;min-width:116px;gap:8px;border-radius:10px;border:1px solid #bd8b65;
    background:linear-gradient(115deg,#f0dfcb,#f8ecdc);color:#754329;
    font:700 11px/1 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v278-search-row .vault-v267-open-book:hover {
    background:#e9cfb2;border-color:#a36d4b;
}
.master-vault-v4-modal .vault-v278-selected {
    display:flex;align-items:center;gap:11px;min-width:0;margin-top:14px;
    padding:12px 14px;border:1px solid #dcc2a1;border-radius:11px;
    background:linear-gradient(105deg,#f6ead8,#fcf8f1);
}
.master-vault-v4-modal .vault-v278-selected-symbol {
    display:grid;place-items:center;flex:none;width:34px;height:34px;border-radius:10px;
    border:1px solid #d8b88f;background:#fff7eb;color:#b47a42;font-size:19px;
}
.master-vault-v4-modal .vault-v278-selected-copy {display:grid;gap:4px;flex:1;min-width:0}
.master-vault-v4-modal .vault-v278-selected-copy>small {
    color:#9d7958;font:800 9px/1.2 Arial,sans-serif;letter-spacing:.095em;
}
.master-vault-v4-modal .vault-v278-selected-copy>strong {
    font:700 17px/1.22 Georgia,serif;color:#653022;overflow-wrap:anywhere;
}
.master-vault-v4-modal .vault-v278-selected-copy>span {
    font:500 11px/1.25 Arial,sans-serif;color:#8e674d;
}
.master-vault-v4-modal .vault-v278-selected button {
    flex:none;border:1px solid #d4b18a;border-radius:8px;background:#fffaf3;
    color:#804e33;padding:8px 12px;font:700 11px/1 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v278-selected button:hover {background:#f3e2cd}
.master-vault-v4-modal .vault-v278-empty {
    border:1px dashed #e4d0b5;border-radius:10px;background:#fffcf8;
    margin-top:14px;padding:15px;font:500 11px/1.55 Arial,sans-serif;color:#9a7759;
}
.master-vault-v4-modal .vault-v278-parameters {
    padding:20px 18px 18px;min-width:0;border-left:1px solid #e4cfb4;
    background:linear-gradient(125deg,#f8efe2,#fcf6ed 75%);
    border-top-right-radius:14px;
}
.master-vault-v4-modal .vault-v278-side-head {display:grid;gap:4px;margin-bottom:15px}
.master-vault-v4-modal .vault-v278-side-head strong {
    font:700 17px/1.1 Georgia,serif;color:#673126;
}
.master-vault-v4-modal .vault-v278-side-head small {
    color:#9b7658;font:500 11px/1.3 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v278-metric-grid {
    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;
}
.master-vault-v4-modal .vault-v278-metric-grid>div {
    min-width:0;min-height:65px;display:grid;align-content:center;gap:6px;
    border:1px solid #e0c9aa;border-radius:10px;background:#fffdf9;padding:11px 12px;
}
.master-vault-v4-modal .vault-v278-metric-grid>div>span {
    color:#a17c5d;font:800 9px/1.2 Arial,sans-serif;text-transform:uppercase;letter-spacing:.055em;
}
.master-vault-v4-modal .vault-v278-metric-grid>div>strong {
    font:700 19px/1.05 Georgia,serif;color:#6a3429;overflow-wrap:anywhere;
}
.master-vault-v4-modal .vault-v278-metric-grid>div:last-child>strong {font:700 12px/1.28 Arial,sans-serif}
.master-vault-v4-modal .vault-v278-adjust {
    margin-top:11px;border:1px solid #dec5a5;border-radius:10px;background:#fffaf2;
}
.master-vault-v4-modal .vault-v278-adjust>summary {
    display:flex;align-items:center;justify-content:space-between;gap:10px;
    padding:11px 12px;color:#854e34;font:700 11px/1.2 Arial,sans-serif;cursor:pointer;
}
.master-vault-v4-modal .vault-v278-adjust>summary::before {display:none}
.master-vault-v4-modal .vault-v278-adjust>summary svg {width:16px;height:16px;transition:transform .2s}
.master-vault-v4-modal .vault-v278-adjust[open]>summary svg {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v278-adjust .vault-v274-scroll-stats-grid {padding:4px 11px 11px}
.master-vault-v4-modal .vault-v278-features {
    border-top:1px solid #e5d0b5;padding:17px 20px 20px;
    background:linear-gradient(120deg,#fffdf9,#fcf8f2);
    border-radius:0 0 14px 14px;
}
.master-vault-v4-modal .vault-v278-features-heading {
    display:flex;align-items:center;gap:10px;justify-content:space-between;margin-bottom:11px;
}
.master-vault-v4-modal .vault-v278-features-heading>div {display:grid;gap:4px;min-width:0}
.master-vault-v4-modal .vault-v278-features-heading strong {font:700 15px/1.1 Georgia,serif;color:#6a3429}
.master-vault-v4-modal .vault-v278-features-heading small {font:500 10px/1.3 Arial,sans-serif;color:#99765a}
.master-vault-v4-modal .vault-v278-features-heading>span {
    flex:none;color:#a27a56;font:700 9px/1.3 Arial,sans-serif;letter-spacing:.06em;
}
.master-vault-v4-modal .vault-v278-feature-list {
    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;
}
.master-vault-v4-modal .vault-v278-feature {
    min-width:0;display:flex;gap:10px;align-items:center;padding:13px;
    border:1px solid #e0c8a9;border-radius:11px;background:#fffdfa;
}
.master-vault-v4-modal .vault-v278-feature-mark {
    display:grid;place-items:center;flex:none;width:33px;height:33px;border-radius:10px;
    border:1px solid #dec3a0;background:linear-gradient(130deg,#faf0e1,#fff9ef);
    color:#a37148;font:700 14px/1 Georgia,serif;
}
.master-vault-v4-modal .vault-v278-feature-copy {display:grid;gap:5px;flex:1;min-width:0}
.master-vault-v4-modal .vault-v278-feature-copy strong {
    font:700 13px/1.2 Georgia,serif;color:#603024;overflow-wrap:anywhere;
}
.master-vault-v4-modal .vault-v278-feature-copy small {
    font:500 10px/1.25 Arial,sans-serif;color:#9c785b;
}
.master-vault-v4-modal .vault-v278-feature button {
    flex:none;padding:9px 10px;border-radius:8px;border:1px solid #d2ad85;
    background:#f9efe3;color:#78442d;font:700 10px/1 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v278-feature button:hover {background:#f2dcc2}
@media(max-width:1120px) {
    .master-vault-v4-modal .vault-v278-columns {grid-template-columns:minmax(0,1fr)}
    .master-vault-v4-modal .vault-v278-parameters {
        border-left:0;border-top:1px solid #e4cfb4;border-radius:0;
    }
    .master-vault-v4-modal .vault-v278-metric-grid {grid-template-columns:repeat(4,minmax(0,1fr))}
}
@media(max-width:790px) {
    .master-vault-v4-modal .vault-v278-feature-list {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v278-metric-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:520px) {
    .master-vault-v4-modal .vault-v278-selection,
    .master-vault-v4-modal .vault-v278-parameters,
    .master-vault-v4-modal .vault-v278-features {padding:14px}
    .master-vault-v4-modal .vault-v278-count {display:none}
    .master-vault-v4-modal .vault-v278-search-row {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v278-selected {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v278-selected button {margin-left:auto}
    .master-vault-v4-modal .vault-v278-feature {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v278-feature button {margin-left:auto}
}
@media(prefers-reduced-motion:reduce) {
    .master-vault-v4-modal .vault-v278-builder * {transition:none!important}
}


/* V66.2.80 — Verbete do Grimório integrado à Parte II e à Visualização do Mestre */
.master-vault-v4-modal .vault-v280-reader,
.master-vault-v9-preview .vault-v280-reader {
    min-width:0;overflow:hidden;
    border:1px solid #dfc5a4;border-radius:12px;
    background:linear-gradient(160deg,#fffdf8,#fbf4e8);
    box-shadow:inset 0 1px rgba(255,255,255,.8);
    color:#553627;
}
.master-vault-v4-modal .vault-v280-reader {margin-top:13px}
.master-vault-v4-modal .vault-v280-reader-top,
.master-vault-v9-preview .vault-v280-reader-top {
    display:flex;align-items:center;justify-content:space-between;gap:15px;
    padding:15px 17px;border-bottom:1px solid #e8d7bd;
    background:linear-gradient(110deg,#f5e8d7,#fcf8f0);
}
.master-vault-v4-modal .vault-v280-reader-top>div,
.master-vault-v9-preview .vault-v280-reader-top>div {display:grid;gap:5px;min-width:0}
.master-vault-v4-modal .vault-v280-eyebrow,
.master-vault-v9-preview .vault-v280-eyebrow {color:#a2754e;font:800 9px/1.2 Arial,sans-serif;letter-spacing:.11em;text-transform:uppercase}
.master-vault-v4-modal .vault-v280-reader-top h4 {margin:0;font:700 19px/1.2 Georgia,serif;color:#632c1e}
.master-vault-v4-modal .vault-v280-reader-top em,
.master-vault-v9-preview .vault-v280-reader-top em {font:italic 13px/1.3 Georgia,serif;color:#87583f}
.master-vault-v4-modal .vault-v280-reader-action,
.master-vault-v9-preview .vault-v280-reader-fallback button {
    flex:none;border:1px solid #cda47c;border-radius:9px;
    color:#793b27;background:#fff8ee;padding:9px 12px;
    font:700 11px/1.2 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v280-reader-action:hover,
.master-vault-v9-preview .vault-v280-reader-fallback button:hover {background:#f3e4d2}
.master-vault-v4-modal .vault-v280-facts,
.master-vault-v9-preview .vault-v280-facts {
    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));
    gap:9px 18px;padding:14px 17px;
}
.master-vault-v4-modal .vault-v280-facts>div,
.master-vault-v9-preview .vault-v280-facts>div {display:flex;align-items:baseline;gap:8px;min-width:0;font:12px/1.5 Arial,sans-serif}
.master-vault-v4-modal .vault-v280-facts strong,
.master-vault-v9-preview .vault-v280-facts strong {flex:none;font-weight:700;color:#78452f}
.master-vault-v4-modal .vault-v280-facts span,
.master-vault-v9-preview .vault-v280-facts span {overflow-wrap:anywhere;color:#6f5542}
.master-vault-v4-modal .vault-v280-reader-badges,
.master-vault-v9-preview .vault-v280-reader-badges {display:flex;gap:7px;flex-wrap:wrap;padding:0 17px 11px}
.master-vault-v4-modal .vault-v280-reader-badges span,
.master-vault-v9-preview .vault-v280-reader-badges span {border:1px solid #dfc4a3;border-radius:7px;padding:4px 8px;background:#f5e5d4;color:#8d5838;font:700 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v280-scroll-body,
.master-vault-v9-preview .vault-v280-scroll-body {border-top:1px solid #e8d7bd;padding:18px 19px 20px}
.master-vault-v4-modal .vault-v280-spell-prose,
.master-vault-v9-preview .vault-v280-spell-prose {font:14px/1.76 Georgia,'Times New Roman',serif;color:#4c3529;overflow-wrap:anywhere;white-space:normal}
.master-vault-v4-modal .vault-v280-spell-prose p,
.master-vault-v9-preview .vault-v280-spell-prose p {margin:0 0 12px}
.master-vault-v4-modal .vault-v280-spell-prose p:last-child,
.master-vault-v9-preview .vault-v280-spell-prose p:last-child {margin-bottom:0}
.master-vault-v4-modal .vault-v280-spell-prose ul,
.master-vault-v9-preview .vault-v280-spell-prose ul {margin:8px 0 13px 21px;list-style:disc}
.master-vault-v4-modal .vault-v280-spell-prose ol,
.master-vault-v9-preview .vault-v280-spell-prose ol {margin:8px 0 13px 21px;list-style:decimal}
.master-vault-v4-modal .vault-v280-spell-prose li,
.master-vault-v9-preview .vault-v280-spell-prose li {margin-bottom:7px}
.master-vault-v4-modal .vault-v280-spell-prose strong,
.master-vault-v9-preview .vault-v280-spell-prose strong {color:#5a2b1f}
.master-vault-v4-modal .vault-v280-higher,
.master-vault-v9-preview .vault-v280-higher {margin-top:19px;padding-top:16px;border-top:1px solid #dfc5a4}
.master-vault-v4-modal .vault-v280-higher h5,
.master-vault-v9-preview .vault-v280-higher h5 {margin:0 0 10px;color:#693023;font:700 15px/1.3 Georgia,serif}
.master-vault-v4-modal .vault-v280-reader-notice,
.master-vault-v9-preview .vault-v280-reader-notice {color:#997254;font:12px/1.5 Arial,sans-serif}
.master-vault-v4-modal .vault-v280-reader-fallback,
.master-vault-v9-preview .vault-v280-reader-fallback {display:flex;align-items:center;flex-wrap:wrap;gap:10px;color:#9b795e;font:12px/1.4 Arial,sans-serif}
.master-vault-v9-preview .vault-v280-preview-source {font:800 10px Arial,sans-serif;color:#a27650;letter-spacing:.04em}
.master-vault-v9-preview .vault-v280-reader {margin-top:10px}
@media(max-width:650px) {
    .master-vault-v4-modal .vault-v280-facts,
    .master-vault-v9-preview .vault-v280-facts {grid-template-columns:1fr}
    .master-vault-v4-modal .vault-v280-reader-top,
    .master-vault-v9-preview .vault-v280-reader-top {flex-wrap:wrap}
}

/* ================================================================
   Spellbound V66.2.81 — Pergaminhos: seleção limpa e leitor editorial.
   O verbo "Ler" abre um único modal, em vez de reproduzir o conteúdo
   completo dentro dos cards e gavetas do editor.
   ================================================================ */
.master-vault-v4-modal .vault-v281-scroll-builder {
    padding:20px 22px 17px;
    border:1px solid #e2c8a8;
    border-radius:14px;
    background:linear-gradient(150deg,#fffdf9 0%,#fcf7ef 100%);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.95);
}
.master-vault-v4-modal .vault-v281-topline,
.master-vault-v4-modal .vault-v281-topline-title,
.master-vault-v4-modal .vault-v281-current,
.master-vault-v4-modal .vault-v281-rules-link {
    display:flex;align-items:center;min-width:0;
}
.master-vault-v4-modal .vault-v281-topline {justify-content:space-between;gap:14px;margin-bottom:14px}
.master-vault-v4-modal .vault-v281-topline-title {gap:11px}
.master-vault-v4-modal .vault-v281-book-emblem {
    display:grid;place-items:center;flex:none;width:42px;height:42px;
    border:1px solid #e1c4a2;border-radius:12px;
    background:linear-gradient(150deg,#f8e9d6,#fffaf2);color:#9f6844;
}
.master-vault-v4-modal .vault-v281-book-emblem svg {width:22px;height:22px}
.master-vault-v4-modal .vault-v281-topline-title h4 {
    margin:0;color:#632c21;font:700 20px/1.16 Georgia,'Times New Roman',serif;
}
.master-vault-v4-modal .vault-v281-topline-title>div>span {
    display:block;margin-top:4px;color:#937154;font:500 11px/1.35 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v281-library-action {
    display:inline-flex;align-items:center;justify-content:center;gap:8px;flex:none;
    border:1px solid #ceaa84;border-radius:10px;padding:10px 14px;
    background:#fff9ef;color:#7e4930;font:700 11px/1 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v281-library-action:hover {background:#f6e8d7;border-color:#a77752}
.master-vault-v4-modal .vault-v281-library-action svg {width:17px;height:17px}
.master-vault-v4-modal .vault-v281-quick-choice {position:relative;z-index:2}
.master-vault-v4-modal .vault-v281-search {height:47px;border-radius:10px}
.master-vault-v4-modal .vault-v281-current {gap:10px;align-items:stretch}
.master-vault-v4-modal .vault-v281-current-spell {
    display:flex;align-items:center;gap:14px;flex:1;min-width:0;text-align:left;
    padding:15px 16px;border:1px solid #dbc1a0;border-radius:12px;
    background:linear-gradient(110deg,#f8eddf,#fff9f1);
    box-shadow:inset 0 1px rgba(255,255,255,.75);
    transition:border-color .16s ease,background .16s ease,transform .16s ease;
}
.master-vault-v4-modal .vault-v281-current-spell:hover,
.master-vault-v4-modal .vault-v281-feature-open:hover {
    border-color:#b58159;background:#f7e7d1;transform:translateY(-1px);
}
.master-vault-v4-modal .vault-v281-current-spell:focus-visible,
.master-vault-v4-modal .vault-v281-feature-open:focus-visible,
.master-vault-v4-modal .vault-v281-library-action:focus-visible {
    outline:2px solid #a76f48;outline-offset:3px;
}
.master-vault-v4-modal .vault-v281-current-mark,
.master-vault-v4-modal .vault-v281-feature-open-symbol {
    display:grid;place-items:center;flex:none;width:38px;height:38px;
    border:1px solid #d9b990;border-radius:11px;background:#fff9ef;
    color:#a57645;font:700 22px Georgia,serif;
}
.master-vault-v4-modal .vault-v281-current-copy,
.master-vault-v4-modal .vault-v281-feature-open-copy {display:grid;gap:5px;min-width:0;flex:1}
.master-vault-v4-modal .vault-v281-current-copy strong,
.master-vault-v4-modal .vault-v281-feature-open-copy strong {
    font:700 17px/1.16 Georgia,serif;color:#642f24;overflow-wrap:anywhere;
}
.master-vault-v4-modal .vault-v281-current-copy small,
.master-vault-v4-modal .vault-v281-feature-open-copy small {
    font:italic 12px/1.3 Georgia,serif;color:#926c50;
}
.master-vault-v4-modal .vault-v281-read-call,
.master-vault-v4-modal .vault-v281-feature-open-action {
    display:flex;align-items:center;gap:5px;white-space:nowrap;
    font:700 11px/1.2 Arial,sans-serif;color:#8c5b37;
}
.master-vault-v4-modal .vault-v281-read-call svg,
.master-vault-v4-modal .vault-v281-feature-open-action svg {width:17px;height:17px}
.master-vault-v4-modal .vault-v281-change-spell {
    flex:none;align-self:stretch;padding:0 17px;border-radius:10px;
    border:1px solid #e2cbb0;background:#fffdf9;color:#795038;
    font:700 11px Arial,sans-serif;
}
.master-vault-v4-modal .vault-v281-change-spell:hover {border-color:#b3855e;background:#f9efe3}
.master-vault-v4-modal .vault-v281-settings {
    margin-top:11px;border:1px solid #e5d2ba;border-radius:11px;
    background:rgba(255,254,251,.95);overflow:hidden;
}
.master-vault-v4-modal .vault-v281-settings>summary {
    display:flex;gap:14px;align-items:center;min-height:46px;
    padding:10px 13px;cursor:pointer;list-style:none;
}
.master-vault-v4-modal .vault-v281-settings>summary::-webkit-details-marker {display:none}
.master-vault-v4-modal .vault-v281-settings>summary:hover {background:#fcf5eb}
.master-vault-v4-modal .vault-v281-settings-label {
    display:flex;align-items:center;gap:8px;flex:none;
    font:700 11px/1.2 Arial,sans-serif;color:#713d2c;
}
.master-vault-v4-modal .vault-v281-settings-label svg {width:17px;height:17px;color:#a87650}
.master-vault-v4-modal .vault-v281-settings-values {
    display:flex;align-items:center;gap:8px;flex:1;justify-content:flex-end;min-width:0;
    flex-wrap:wrap;
}
.master-vault-v4-modal .vault-v281-settings-values span {
    display:inline-flex;align-items:center;line-height:1.1;
    border-left:1px solid #dfc9af;padding-left:8px;
    color:#8c6c52;font:600 10px/1.25 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v281-settings-values span:first-child {border-left:0}
.master-vault-v4-modal .vault-v281-settings-arrow {width:16px;height:16px;color:#a27451;transition:transform .18s}
.master-vault-v4-modal .vault-v281-settings[open] .vault-v281-settings-arrow {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v281-settings-content {
    padding:14px;border-top:1px solid #ebdac5;background:#fdf9f3;
}
.master-vault-v4-modal .vault-v281-settings-grid {
    display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;
}
.master-vault-v4-modal .vault-v281-settings-grid>div {
    min-width:0;display:grid;gap:6px;padding:12px;
    border:1px solid #e3cdb0;border-radius:10px;background:#fffdfa;
}
.master-vault-v4-modal .vault-v281-settings-grid small {
    text-transform:uppercase;letter-spacing:.075em;font:700 9px Arial,sans-serif;color:#9a7658;
}
.master-vault-v4-modal .vault-v281-settings-grid strong {
    font:700 17px/1.3 Georgia,serif;color:#683126;overflow-wrap:anywhere;
}
.master-vault-v4-modal .vault-v281-settings-grid>div:last-child strong {font:700 11px/1.5 Arial,sans-serif}
.master-vault-v4-modal .vault-v281-custom-fields {display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:13px}
.master-vault-v4-modal .vault-v281-custom-fields label {display:grid;gap:5px}
.master-vault-v4-modal .vault-v281-custom-fields label span {font:700 10px Arial,sans-serif;color:#70432e}
.master-vault-v4-modal .vault-v281-custom-fields input {
    width:100%;min-height:39px;border:1px solid #dec4a5;border-radius:8px;
    padding:7px 10px;background:#fffdf9;color:#5c2e22;font:700 15px Georgia,serif;
}
.master-vault-v4-modal .vault-v281-settings-actions {display:flex;flex-wrap:wrap;gap:9px;margin-top:11px}
.master-vault-v4-modal .vault-v281-settings-actions button {
    padding:8px 10px;border:1px solid #ddc6ab;border-radius:8px;
    background:#fff9f0;color:#795037;font:700 10px Arial,sans-serif;
}
.master-vault-v4-modal .vault-v281-settings-actions button:hover {background:#f4e4d0}
.master-vault-v4-modal .vault-v281-rules-link {
    gap:12px;justify-content:space-between;margin-top:11px;padding:9px 2px 0;
    border-top:1px solid #eddcc8;
}
.master-vault-v4-modal .vault-v281-rules-link>span {display:flex;align-items:center;gap:10px;min-width:0;flex-wrap:wrap}
.master-vault-v4-modal .vault-v281-rules-link strong {font:700 11px Georgia,serif;color:#70422d}
.master-vault-v4-modal .vault-v281-rules-link small {color:#9b7d63;font:500 10px Arial,sans-serif}
.master-vault-v4-modal .vault-v281-rules-link button {
    white-space:nowrap;border:0;background:transparent;color:#975d38;
    font:700 11px Arial,sans-serif;text-decoration:underline;text-underline-offset:3px;
}
.master-vault-v4-modal .vault-v281-feature-reader {display:grid;gap:10px}
.master-vault-v4-modal .vault-v281-feature-open {
    display:flex;align-items:center;gap:13px;width:100%;min-width:0;
    padding:13px 15px;border:1px solid #dfc8aa;border-radius:12px;
    background:linear-gradient(135deg,#fcf5ea,#fffdf9);text-align:left;
    transition:transform .16s,border-color .16s,background .16s;
}
.master-vault-v9-preview .vault-v281-preview-spell {
    display:flex;align-items:center;justify-content:space-between;gap:14px;
    padding:13px 15px;background:linear-gradient(125deg,#fcf5e9,#fdfaf4);
    border-top:1px solid #e8d6bf;
}
.master-vault-v9-preview .vault-v281-preview-spell-info {min-width:0;flex:1}
.master-vault-v9-preview .vault-v281-preview-spell-info em {
    color:#875539;font:italic 12px Georgia,serif;
}
.master-vault-v9-preview .vault-v281-preview-facts {display:flex;flex-wrap:wrap;gap:6px 12px;margin-top:7px}
.master-vault-v9-preview .vault-v281-preview-facts span {
    color:#8b674d;font:500 10px/1.4 Arial,sans-serif;
}
.master-vault-v9-preview .vault-v281-preview-spell button {
    display:flex;align-items:center;gap:6px;flex:none;padding:9px 12px;
    border:1px solid #cba47e;border-radius:9px;background:#f8e9d7;
    color:#75422e;font:700 11px Arial,sans-serif;
}
.master-vault-v9-preview .vault-v281-preview-spell button:hover {background:#f2dbbd}
.master-vault-v9-preview .vault-v281-preview-spell button svg {width:15px;height:15px}

/* Leitor flutuante, inspirado na hierarquia editorial do Grimório do Player. */
.vault-v281-reader-layer {
    position:fixed;inset:0;z-index:2147481200;
    display:flex;align-items:center;justify-content:center;padding:20px;
    color:#4f3328;font-family:Arial,sans-serif;
}
.vault-v281-reader-layer[x-cloak] {display:none!important}
.vault-v281-reader-backdrop {position:absolute;inset:0;background:rgba(25,16,12,.69);backdrop-filter:blur(3px)}
.vault-v281-reader-dialog {
    position:relative;z-index:1;width:min(830px,100%);max-height:min(91dvh,970px);
    display:flex;flex-direction:column;overflow:hidden;
    border:1px solid #b99570;border-radius:17px;
    background:#fbf7ef;box-shadow:0 24px 80px rgba(0,0,0,.36),inset 0 1px rgba(255,255,255,.9);
}
.vault-v281-reader-head {
    flex:none;display:flex;align-items:center;justify-content:space-between;gap:14px;
    min-height:54px;padding:10px 19px;border-bottom:1px solid #e5ceb1;
    background:linear-gradient(120deg,#f2e6d7,#fcf8f1);
}
.vault-v281-reader-brand {display:flex;gap:10px;align-items:center;color:#915f41;font:800 10px Arial,sans-serif;text-transform:uppercase;letter-spacing:.1em}
.vault-v281-reader-brand-icon {font:700 22px Georgia,serif;color:#a97744}
.vault-v281-reader-close {display:grid;place-items:center;width:34px;height:34px;border:1px solid #dfc5a4;border-radius:9px;background:#fffaf3;color:#8c5f47}
.vault-v281-reader-close:hover {background:#f0ddc5}
.vault-v281-reader-close svg {width:18px;height:18px}
.vault-v281-reader-scroll {flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain}
.vault-v281-reader-article {max-width:755px;margin:0 auto;padding:30px 28px 38px}
.vault-v281-reader-title-row {display:flex;align-items:flex-start;gap:18px;justify-content:space-between;margin-bottom:22px}
.vault-v281-reader-title-row h3 {margin:0 0 8px;font:700 clamp(27px,3.6vw,35px)/1.14 Georgia,serif;color:#57291f;letter-spacing:-.018em}
.vault-v281-reader-title-row em {color:#885839;font:italic 15px/1.2 Georgia,serif}
.vault-v281-reader-title-row>small {padding-top:7px;color:#a17959;font:800 10px/1.3 Arial,sans-serif;letter-spacing:.04em;white-space:nowrap}
.vault-v281-reader-statlines {display:grid;gap:7px;margin:0 0 23px}
.vault-v281-reader-statlines div {display:flex;align-items:baseline;gap:9px;min-width:0}
.vault-v281-reader-statlines dt {flex:none;font:700 13px/1.45 Georgia,serif;color:#613024}
.vault-v281-reader-statlines dd {min-width:0;margin:0;font:13px/1.5 Arial,sans-serif;color:#604736;overflow-wrap:anywhere}
.vault-v281-reader-flags {display:flex;gap:7px;flex-wrap:wrap;margin:-7px 0 21px}
.vault-v281-reader-flags span {padding:5px 8px;border:1px solid #dec6a8;border-radius:7px;background:#f7ecde;color:#96633c;font:700 10px Arial,sans-serif}
.vault-v281-reader-prose {
    font:15px/1.7 Georgia,'Times New Roman',serif;color:#45372f;
    overflow-wrap:anywhere;white-space:normal;
}
.vault-v281-reader-prose p {margin:0 0 13px}
.vault-v281-reader-prose p:last-child {margin-bottom:0}
.vault-v281-reader-prose h3,.vault-v281-reader-prose h4 {margin:16px 0 8px;font:700 16px Georgia,serif;color:#6e3729}
.vault-v281-reader-prose ul {list-style:disc;margin:9px 0 14px 22px}
.vault-v281-reader-prose ol {list-style:decimal;margin:9px 0 14px 22px}
.vault-v281-reader-prose li {margin:0 0 7px}
.vault-v281-reader-prose strong {font-weight:700;color:#653025}
.vault-v281-reader-prose br+br {display:none}
.vault-v281-reader-higher {margin-top:20px;border-top:1px solid #dec7aa;padding-top:17px}
.vault-v281-reader-higher>h4 {margin:0 0 10px;color:#693325;font:700 17px Georgia,serif}
.vault-v281-reader-loading,.vault-v281-reader-unavailable {color:#906c51;font:12px/1.5 Arial,sans-serif}
.vault-v281-reader-unavailable {display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.vault-v281-reader-unavailable button {text-decoration:underline;color:#78402d}
.vault-v281-reader-foot {
    flex:none;display:flex;align-items:center;justify-content:space-between;gap:14px;
    padding:12px 20px;border-top:1px solid #e4ceb1;background:#f5ebdf;
}
.vault-v281-reader-foot>span {font:500 10px/1.4 Arial,sans-serif;color:#9a785b}
.vault-v281-reader-foot>div {display:flex;gap:7px;flex:none}
.vault-v281-reader-change,.vault-v281-reader-done {padding:10px 13px;border:1px solid #d7b995;border-radius:9px;background:#fff9f1;color:#794732;font:700 11px Arial,sans-serif}
.vault-v281-reader-done {border-color:#6d3022;background:#713324;color:#fff9f1}
.vault-v281-reader-change:hover {background:#f3e1c7}
.vault-v281-reader-done:hover {background:#59261a}
@media(max-width:750px) {
    .master-vault-v4-modal .vault-v281-settings-values span:nth-child(3),
    .master-vault-v4-modal .vault-v281-settings-values span:nth-child(4) {display:none}
    .master-vault-v4-modal .vault-v281-settings-grid {grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:520px) {
    .master-vault-v4-modal .vault-v281-scroll-builder {padding:15px 14px}
    .master-vault-v4-modal .vault-v281-current {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v281-current-spell {flex-basis:100%}
    .master-vault-v4-modal .vault-v281-current-spell,.master-vault-v4-modal .vault-v281-feature-open {flex-wrap:wrap}
    .master-vault-v4-modal .vault-v281-read-call,.master-vault-v4-modal .vault-v281-feature-open-action {margin-left:auto}
    .master-vault-v4-modal .vault-v281-change-spell {min-height:34px;margin-left:auto}
    .master-vault-v4-modal .vault-v281-topline-title h4 {font-size:17px}
    .master-vault-v4-modal .vault-v281-settings-values span:nth-child(n+2) {display:none}
    .master-vault-v4-modal .vault-v281-custom-fields {grid-template-columns:1fr}
    .vault-v281-reader-layer {padding:7px}
    .vault-v281-reader-dialog {max-height:96dvh;border-radius:11px}
    .vault-v281-reader-article {padding:21px 18px}
    .vault-v281-reader-foot {flex-wrap:wrap}
    .vault-v281-reader-foot>span {flex:1 1 100%}
    .master-vault-v9-preview .vault-v281-preview-spell {flex-wrap:wrap}
    .master-vault-v9-preview .vault-v281-preview-spell button {margin-left:auto}
}
@media(prefers-reduced-motion:reduce) {
    .vault-v281-reader-layer *, .vault-v281-current-spell,.vault-v281-feature-open {transition:none!important}
}

.master-vault-v4-modal .master-vault-v5-ability-content-grid.vault-v281-feature-grid {grid-template-columns:minmax(0,1fr);gap:0}


/* Spellbound V66.2.82 — resumo do feitiço e edição de título. */
.master-vault-v9-preview .vault-v282-preview-action {
    padding:12px 14px;
    border-top:1px solid #e6d4bc;
    background:linear-gradient(105deg,#fffcf7,#faf2e7);
}
.master-vault-v9-preview .vault-v282-preview-read {
    width:100%;min-height:54px;display:flex;align-items:center;gap:13px;
    padding:9px 13px;border:1px solid #cda783;border-radius:11px;
    background:linear-gradient(115deg,#f4e2c9,#fbf1e4);
    text-align:left;color:#713b29;
    box-shadow:inset 0 1px rgba(255,255,255,.8);
    transition:border-color .18s,background .18s,box-shadow .18s,transform .18s;
}
.master-vault-v9-preview .vault-v282-preview-read:hover {
    border-color:#9c694a;background:linear-gradient(115deg,#eed2b0,#f8e6d0);
    box-shadow:0 5px 17px rgba(85,45,24,.09);transform:translateY(-1px);
}
.master-vault-v9-preview .vault-v282-preview-read:focus-visible {
    outline:2px solid #a57753;outline-offset:3px;
}
.master-vault-v9-preview .vault-v282-preview-read strong {
    font:700 14px/1.2 Georgia,'Times New Roman',serif;color:#623024;
}
.master-vault-v9-preview .vault-v282-preview-book {
    flex:none;display:grid;place-items:center;width:34px;height:34px;
    border:1px solid #dab991;border-radius:9px;background:#fff9f0;color:#9b6846;
}
.master-vault-v9-preview .vault-v282-preview-book svg {width:19px;height:19px}
.master-vault-v9-preview .vault-v282-preview-chevron {width:19px;height:19px;margin-left:auto;color:#9c6b49}

.master-vault-v4-modal .vault-v282-title-panel {
    min-width:0;border:1px solid #e4c9a8;border-radius:12px;
    background:linear-gradient(135deg,#fffdf9,#fcf5eb);
    box-shadow:inset 0 1px rgba(255,255,255,.85);overflow:hidden;
}
.master-vault-v4-modal .vault-v282-title-panel>summary {
    display:flex;align-items:center;gap:12px;min-height:65px;
    padding:11px 15px;list-style:none;cursor:pointer;
    transition:background .17s;
}
.master-vault-v4-modal .vault-v282-title-panel>summary::-webkit-details-marker {display:none}
.master-vault-v4-modal .vault-v282-title-panel>summary:hover {background:#f9ecdc}
.master-vault-v4-modal .vault-v282-title-panel>summary:focus-visible {outline:2px solid #a67650;outline-offset:-3px}
.master-vault-v4-modal .vault-v282-title-icon {
    display:grid;place-items:center;flex:none;width:38px;height:38px;
    border-radius:10px;border:1px solid #e0c2a1;background:linear-gradient(#fffaf3,#f6e8d4);color:#996545;
}
.master-vault-v4-modal .vault-v282-title-icon svg {width:19px;height:19px}
.master-vault-v4-modal .vault-v282-title-copy {display:grid;gap:4px;flex:1;min-width:0}
.master-vault-v4-modal .vault-v282-title-copy strong {color:#663327;font:700 14px/1.2 Georgia,serif}
.master-vault-v4-modal .vault-v282-title-copy small {color:#977253;font:500 10px/1.3 Arial,sans-serif}
.master-vault-v4-modal .vault-v282-title-tag {
    padding:5px 8px;border:1px solid #e3ccb1;border-radius:99px;
    background:#fff8ef;color:#986e52;font:700 10px/1 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v282-title-chevron {width:17px;height:17px;flex:none;color:#996c50;transition:transform .18s}
.master-vault-v4-modal .vault-v282-title-panel[open] .vault-v282-title-chevron {transform:rotate(180deg)}
.master-vault-v4-modal .vault-v282-title-panel[open]>summary {border-bottom:1px solid #ead6bc}
.master-vault-v4-modal .vault-v282-title-body {
    display:flex;align-items:end;gap:10px;padding:16px;background:#fffaf4;
}
.master-vault-v4-modal .vault-v282-title-label {display:grid;gap:7px;flex:1;min-width:0}
.master-vault-v4-modal .vault-v282-title-label>span {
    font:700 11px/1.2 Arial,sans-serif;color:#80513a;
}
.master-vault-v4-modal .vault-v282-title-label>input {
    display:block;width:100%;min-width:0;min-height:44px;
    padding:10px 12px;border:1px solid #dec2a0;border-radius:9px;
    background:#fffefe;color:#653226;font:500 13px/1.4 Arial,sans-serif;
    transition:border-color .15s,box-shadow .15s;
}
.master-vault-v4-modal .vault-v282-title-label>input:focus {
    outline:none;border-color:#ad7955;box-shadow:0 0 0 3px rgba(174,119,82,.14);
}
.master-vault-v4-modal .vault-v282-title-reset {
    flex:none;min-height:44px;padding:10px 14px;
    border:1px solid #d9b996;border-radius:9px;
    background:#f7e9d8;color:#7d482e;font:700 11px/1.2 Arial,sans-serif;
}
.master-vault-v4-modal .vault-v282-title-reset:hover {background:#efd8bc;border-color:#cba17a}
@media(max-width:580px) {
    .master-vault-v4-modal .vault-v282-title-body {flex-direction:column;align-items:stretch}
    .master-vault-v4-modal .vault-v282-title-reset {align-self:flex-end}
    .master-vault-v4-modal .vault-v282-title-panel>summary {gap:9px;padding:10px}
}
@media(prefers-reduced-motion:reduce) {
    .master-vault-v9-preview .vault-v282-preview-read,
    .master-vault-v4-modal .vault-v282-title-panel * {transition:none!important}
}



/* Spellbound V66.2.83 — Pergaminhos: ações discretas, habilidades no editor principal. */
.master-vault-v9-preview .vault-v282-preview-action {
    display:flex;
    justify-content:flex-end;
    align-items:center;
    padding:9px 14px 11px;
    border-top:1px solid #eadbc8;
    background:transparent;
}
.master-vault-v9-preview .vault-v282-preview-read {
    width:auto;
    min-height:36px;
    display:inline-flex;
    flex:none;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:5px 11px;
    border:1px solid #ddc5a9;
    border-radius:8px;
    background:#fffaf3;
    color:#78513e;
    box-shadow:none;
    text-align:center;
    transition:border-color .16s,background .16s,color .16s;
}
.master-vault-v9-preview .vault-v282-preview-read:hover {
    border-color:#b99471;
    background:#f7ecde;
    box-shadow:none;
    transform:none;
}
.master-vault-v9-preview .vault-v282-preview-read strong {
    color:inherit;
    font:700 11px/1.2 Arial,sans-serif;
}
.master-vault-v9-preview .vault-v282-preview-book {
    width:23px;height:23px;
    border:0;border-radius:5px;
    background:transparent;color:#996e50;
}
.master-vault-v9-preview .vault-v282-preview-book svg {width:16px;height:16px}
.master-vault-v9-preview .vault-v282-preview-chevron {
    width:14px;height:14px;margin-left:2px;color:#996e50;
}
.master-vault-v4-modal .vault-v283-scroll-abilities {
    margin-top:12px;
    border-color:#e2c7a8;
    background:linear-gradient(130deg,#fffdf9,#faf3e9);
}
.master-vault-v4-modal .vault-v283-scroll-abilities .master-vault-v5-abilities-head {
    background:linear-gradient(110deg,#f4e9dc,#fcf8f3);
    border-bottom:1px solid #e8d1b5;
}
.master-vault-v4-modal .vault-v283-scroll-abilities .master-vault-v5-ability-drawer {
    border-color:#dfc6a8;
    background:#fffcf8;
}
.master-vault-v4-modal .vault-v283-scroll-abilities .master-vault-v5-ability-title strong {
    color:#683426;
}
@media(max-width:650px) {
    .master-vault-v9-preview .vault-v282-preview-action {padding:8px 10px 10px}
    .master-vault-v9-preview .vault-v282-preview-read {min-height:35px}
}


/* V66.2.84 — No pergaminho, a habilidade de feitiço é uma só linha.
   O verbete completo continua acessível no mesmo leitor da V66.2.83. */
.master-vault-v9-preview .master-vault-v9-preview-feature.vault-v284-scroll-compact {
    overflow:hidden;
}
.master-vault-v9-preview .vault-v284-scroll-compact > .master-vault-v9-preview-feature-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:10px 16px;
    padding:10px 13px;
    border-bottom:0;
    min-height:51px;
}
.master-vault-v9-preview .vault-v284-scroll-compact .master-vault-v9-preview-feature-head h4 {
    margin:0;
    flex:1 1 240px;
    min-width:0;
    overflow-wrap:anywhere;
}
.master-vault-v9-preview .vault-v284-preview-read {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:34px;
    padding:7px 11px;
    flex:none;
    border:1px solid #d9bfa0;
    border-radius:8px;
    background:#fff9f2;
    color:#7b4b32;
    font:700 11px/1.15 Arial,sans-serif;
    cursor:pointer;
    transition:background .16s ease,border-color .16s ease,color .16s ease;
}
.master-vault-v9-preview .vault-v284-preview-read svg {
    width:15px;height:15px;flex:none;
}
.master-vault-v9-preview .vault-v284-preview-read svg:last-child {
    width:12px;height:12px;color:#a37557;
}
.master-vault-v9-preview .vault-v284-preview-read:hover {
    border-color:#b78d66;
    background:#f5e8d7;
    color:#672f22;
}
.master-vault-v9-preview .vault-v284-preview-read:focus-visible {
    outline:2px solid #a87853;
    outline-offset:2px;
}
@media (max-width:530px) {
    .master-vault-v9-preview .vault-v284-scroll-compact > .master-vault-v9-preview-feature-head {
        gap:8px;
    }
    .master-vault-v9-preview .vault-v284-scroll-compact .master-vault-v9-preview-feature-head h4 {
        flex-basis:100%;
    }
    .master-vault-v9-preview .vault-v284-preview-read {
        margin-left:auto;
    }
}
@media (prefers-reduced-motion:reduce) {
    .master-vault-v9-preview .vault-v284-preview-read {transition:none}
}
