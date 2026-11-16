    @push('styles')
        <style>
            .inventory-v10 { position: relative; min-height: 100%; color: #53150f; }
            .inventory-v10 [x-cloak] { display: none !important; }

            .inventory-v10-input,
            .inventory-v10-select,
            .inventory-v10-textarea {
                width: 100%;
                border: 1px solid rgba(205,187,159,.86);
                border-radius: 10px;
                background: rgba(255,255,255,.82);
                color: #53150f;
                outline: none;
                transition: border-color .14s ease, box-shadow .14s ease, background .14s ease;
            }

            .inventory-v10-input,
            .inventory-v10-select {
                min-height: 44px;
                padding: 9px 11px;
                font-size: 14px;
                font-weight: 700;
            }

            .inventory-v10-textarea {
                padding: 11px 12px;
                font-size: 14px;
                line-height: 1.65;
                resize: vertical;
            }

            .inventory-v10-input:focus,
            .inventory-v10-select:focus,
            .inventory-v10-textarea:focus {
                border-color: #8c6239;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(140,98,57,.08);
            }

            .inventory-v12-topbar {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid rgba(216,199,171,.55);
                padding-bottom: 14px;
            }

            .inventory-v12-actions {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
            }

            .inventory-v12-money-wrap {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                justify-content: flex-end;
                gap: 10px;
                margin-left: auto;
            }

            .inventory-v12-wallet {
                min-width: min(100%, 430px);
                border: 1px solid rgba(205,187,159,.7);
                border-radius: 16px;
                background: linear-gradient(180deg, rgba(250,248,242,.98), rgba(244,239,230,.9));
                box-shadow: 0 2px 12px rgba(83,21,15,.04);
                padding: 10px 12px;
            }

            .inventory-v12-wallet-head {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px;
            }

            .inventory-v12-wallet-title {
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .14em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v12-wallet-grid {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 8px;
            }

            .inventory-v12-coin {
                display: flex;
                flex-direction: column;
                gap: 3px;
                border: 1px solid rgba(205,187,159,.6);
                border-radius: 12px;
                background: rgba(255,255,255,.52);
                padding: 8px 8px 7px;
            }

            .inventory-v12-coin span {
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v12-coin input {
                width: 100%;
                border: 0;
                background: transparent;
                padding: 0;
                font-family: Georgia, serif;
                font-size: 18px;
                font-weight: 900;
                color: #53150f;
                text-align: right;
                outline: none;
            }

            .inventory-v12-mini-action {
                position: relative;
                display: inline-flex;
                min-height: 40px;
                align-items: center;
                gap: 8px;
                border-radius: 12px;
                border: 1px solid rgba(205,187,159,.7);
                padding: 0 13px;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                transition: background .15s ease, color .15s ease, border-color .15s ease;
            }

            .inventory-v12-mini-action:hover {
                background: rgba(239,233,220,.62);
                color: #53150f;
                border-color: rgba(140,98,57,.45);
            }

            .inventory-v12-counter-badge {
                display: inline-flex;
                min-width: 22px;
                height: 22px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: rgba(107,29,20,.08);
                padding: 0 6px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #6b1d14;
            }

            .inventory-v12-equipped-card {
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 16px;
                background: linear-gradient(180deg, rgba(255,253,249,.98), rgba(248,244,236,.96));
                padding: 12px;
                box-shadow: 0 3px 10px rgba(83,21,15,.04);
            }

            .inventory-v12-equipped-card + .inventory-v12-equipped-card {
                margin-top: 10px;
            }

            @media (max-width: 640px) {
                .inventory-v12-wallet {
                    min-width: 100%;
                }

                .inventory-v12-wallet-grid {
                    grid-template-columns: repeat(5, minmax(52px, 1fr));
                }

                .inventory-v12-coin input {
                    font-size: 16px;
                }
            }

            .inventory-v10-money {
                width: 42px;
                border: 0;
                border-bottom: 1px solid rgba(205,187,159,.82);
                background: transparent;
                padding: 1px 2px 2px;
                text-align: right;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #53150f;
                outline: none;
            }

            .inventory-v10-thumb {
                overflow: hidden;
                background: radial-gradient(circle at 50% 42%, rgba(205,187,159,.30), rgba(239,233,220,.46));
            }
            .inventory-v10-thumb img { width: 100%; height: 100%; object-fit: cover; }
            .inventory-v10-row { transition: background .14s ease; }
            .inventory-v10-row:hover { background: rgba(239,233,220,.40); }

            .inventory-v10-slot {
                min-height: 88px;
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 12px;
                background: rgba(250,248,242,.76);
                transition: border-color .14s ease, background .14s ease, transform .14s ease;
            }
            .inventory-v10-slot:hover {
                border-color: rgba(107,29,20,.34);
                background: rgba(239,233,220,.52);
                transform: translateY(-1px);
            }

            .inventory-v10-choice {
                min-height: 42px;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 10px;
                background: rgba(250,248,242,.82);
                color: #8c6239;
                padding: 8px 11px;
                font-size: 11px;
                font-weight: 900;
                transition: background .14s ease, border-color .14s ease, color .14s ease;
            }
            .inventory-v10-choice:hover { background: rgba(239,233,220,.62); color: #53150f; }
            .inventory-v10-choice.active { border-color: #6b1d14; background: #6b1d14; color: #f4f1e8; }

            .inventory-v10-chip {
                position: relative;
                display: inline-flex;
                align-items: center;
                min-height: 30px;
                border: 1px solid rgba(205,187,159,.78);
                border-radius: 999px;
                background: rgba(250,248,242,.86);
                padding: 5px 10px;
                color: #8c6239;
                font-size: 10px;
                font-weight: 900;
                transition: background .14s ease, border-color .14s ease, color .14s ease;
            }
            .inventory-v10-chip:hover { background: rgba(239,233,220,.70); color: #53150f; }
            .inventory-v10-chip.active { border-color: rgba(107,29,20,.42); background: rgba(107,29,20,.08); color: #53150f; }

            .inventory-v10-tooltip-panel {
                pointer-events: none;
                position: absolute;
                left: 50%;
                bottom: calc(100% + 8px);
                z-index: 80;
                width: 260px;
                transform: translateX(-50%) translateY(4px);
                border: 1px solid rgba(205,187,159,.92);
                border-radius: 10px;
                background: #faf8f2;
                padding: 9px 10px;
                color: #5f3a27;
                box-shadow: 0 12px 28px rgba(43,29,23,.16);
                font-size: 11px;
                font-weight: 600;
                line-height: 1.45;
                opacity: 0;
                visibility: hidden;
                transition: opacity .12s ease, transform .12s ease, visibility .12s ease;
            }
            .inventory-v10-tooltip:hover .inventory-v10-tooltip-panel {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(0);
            }

            .inventory-v10-module {
                border: 1px solid rgba(216,199,171,.72);
                border-radius: 14px;
                background: linear-gradient(180deg, rgba(255,253,249,.96), rgba(247,243,234,.86));
            }

            .inventory-v10-feature-card {
                border: 1px solid rgba(216,199,171,.68);
                border-radius: 13px;
                background: rgba(255,253,249,.86);
            }

            .inventory-v10-detail-paper {
                background:
                    radial-gradient(circle at 16% 0%, rgba(255,255,255,.92), transparent 34%),
                    linear-gradient(180deg, rgba(250,248,242,.995), rgba(247,243,234,.99));
            }

            .inventory-v11-item-card {
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.62);
                border-radius: 16px;
                background:
                    linear-gradient(
                        135deg,
                        rgba(255,253,249,.96),
                        rgba(248,244,235,.90)
                    );
                box-shadow:
                    0 1px 0 rgba(255,255,255,.72) inset,
                    0 4px 14px rgba(83,21,15,.045);
                transition:
                    transform .16s ease,
                    border-color .16s ease,
                    box-shadow .16s ease,
                    background .16s ease;
            }

            .inventory-v11-item-card::before {
                content: '';
                position: absolute;
                left: 0;
                top: 12px;
                bottom: 12px;
                width: 3px;
                border-radius: 0 999px 999px 0;
                background: rgba(140,98,57,.32);
            }

            .inventory-v11-item-card.magical::before {
                background: #6b1d14;
            }

            .inventory-v11-item-card:hover,
            .inventory-v11-item-card:focus-within {
                transform: translateY(-1px);
                border-color: rgba(140,98,57,.52);
                background:
                    linear-gradient(
                        135deg,
                        rgba(255,254,251,.99),
                        rgba(247,241,230,.96)
                    );
                box-shadow:
                    0 1px 0 rgba(255,255,255,.84) inset,
                    0 10px 24px rgba(83,21,15,.075);
            }

            .inventory-v11-item-card:focus {
                outline: none;
                box-shadow:
                    0 0 0 3px rgba(140,98,57,.08),
                    0 10px 24px rgba(83,21,15,.075);
            }

            .inventory-v11-feature-strip {
                border-top: 1px solid rgba(216,199,171,.52);
                background: rgba(239,233,220,.26);
            }

            .inventory-v11-feature-usage {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 8px;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 10px;
                background: rgba(255,253,249,.72);
                padding: 6px 7px 6px 9px;
            }

            .inventory-v11-usage-name {
                max-width: 128px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;
                color: #53150f;
            }

            .inventory-v11-usage-recovery {
                white-space: nowrap;
                font-size: 9px;
                font-weight: 900;
                color: #8c6239;
            }

            .inventory-v11-mini-tracker {
                display: inline-flex;
                flex-shrink: 0;
                align-items: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.76);
                border-radius: 8px;
                background: #faf8f2;
            }

            .inventory-v11-mini-tracker button {
                display: flex;
                width: 27px;
                height: 27px;
                align-items: center;
                justify-content: center;
                color: #8c6239;
                font-size: 14px;
                font-weight: 900;
                transition: background .12s ease, color .12s ease;
            }

            .inventory-v11-mini-tracker button:hover {
                background: rgba(239,233,220,.78);
                color: #53150f;
            }

            .inventory-v11-mini-tracker strong {
                min-width: 43px;
                padding: 0 5px;
                text-align: center;
                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;
                color: #53150f;
            }

            .inventory-v11-detail-mechanics {
                border: 1px solid rgba(205,187,159,.55);
                border-radius: 14px;
                background: rgba(255,253,249,.58);
                padding: 13px 14px;
            }

            .inventory-v11-detail-section-title {
                display: flex;
                align-items: center;
                gap: 9px;
                margin-bottom: 12px;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .10em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v11-detail-section-title::after {
                content: '';
                height: 1px;
                flex: 1;
                background: linear-gradient(
                    90deg,
                    rgba(205,187,159,.75),
                    transparent
                );
            }

            .inventory-v11-ability-card {
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.58);
                border-radius: 14px;
                background: rgba(255,253,249,.76);
                box-shadow: 0 2px 8px rgba(83,21,15,.035);
            }

            .inventory-v11-ability-header {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px 14px;
                border-bottom: 1px solid rgba(216,199,171,.52);
                background: rgba(239,233,220,.38);
                padding: 10px 12px;
            }

            .inventory-v11-ability-body {
                padding: 11px 13px 13px;
            }

            .inventory-v11-recovery-pill {
                display: inline-flex;
                align-items: center;
                min-height: 28px;
                border-radius: 999px;
                background: rgba(107,29,20,.065);
                padding: 0 9px;
                white-space: nowrap;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .045em;
                text-transform: uppercase;
                color: #6b1d14;
            }

            .inventory-v10-tooltip-panel {
                width: min(390px, 82vw);
                max-height: 280px;
                overflow: hidden;
                padding: 12px 13px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                line-height: 1.58;
                text-align: left;
            }

            .inventory-v10-tracker {
                display: inline-flex;
                align-items: center;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.82);
                border-radius: 10px;
                background: rgba(255,253,249,.96);
                box-shadow: 0 1px 0 rgba(255,255,255,.75) inset;
            }
            .inventory-v10-tracker button {
                display: flex;
                width: 30px;
                height: 30px;
                align-items: center;
                justify-content: center;
                color: #8c6239;
                font-size: 15px;
                font-weight: 900;
            }
            .inventory-v10-tracker button:hover { background: rgba(239,233,220,.72); color: #53150f; }
            .inventory-v10-tracker strong {
                min-width: 52px;
                padding: 0 8px;
                text-align: center;
                font-family: Georgia, serif;
                font-size: 12px;
                color: #53150f;
            }

            /* ============================================================
               INVENTÁRIO V14 — frente simplificada
            ============================================================ */

            .inventory-v14 {
                color: #53150f;
            }

            .inventory-v14-toolbar {
                position: relative;
                z-index: 45;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid rgba(205,187,159,.58);
                padding: 0 0 14px;
            }

            .inventory-v14-actions,
            .inventory-v14-toolbar-right {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .inventory-v14-actions {
                flex-wrap: wrap;
            }

            .inventory-v14-toolbar-right {
                margin-left: auto;
            }

            .inventory-v14-primary,
            .inventory-v14-action,
            .inventory-v14-close,
            .inventory-v14-wallet-trigger {
                border: 1px solid rgba(205,187,159,.78);
                background: #faf8f2;
                transition:
                    background .14s ease,
                    border-color .14s ease,
                    color .14s ease;
            }

            .inventory-v14-primary,
            .inventory-v14-action {
                display: inline-flex;
                min-height: 40px;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border-radius: 10px;
                padding: 0 14px;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .035em;
            }

            .inventory-v14-primary {
                border-color: #6b1d14;
                background: #6b1d14;
                color: #faf8f2;
            }

            .inventory-v14-primary:hover {
                border-color: #53150f;
                background: #53150f;
            }

            .inventory-v14-plus {
                margin-top: -1px;
                font-size: 18px;
                font-weight: 500;
                line-height: 1;
            }

            .inventory-v14-action {
                color: #6b1d14;
            }

            .inventory-v14-action-muted {
                color: #8c6239;
            }

            .inventory-v14-action:hover,
            .inventory-v14-close:hover,
            .inventory-v14-wallet-trigger:hover {
                border-color: rgba(140,98,57,.50);
                background: #f1ece2;
            }

            .inventory-v14-badge {
                display: inline-flex;
                min-width: 22px;
                height: 22px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: rgba(107,29,20,.075);
                padding: 0 6px;
                font-family: Georgia, serif;
                font-size: 12px;
                color: #6b1d14;
            }

            .inventory-v14-close {
                display: flex;
                width: 40px;
                height: 40px;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                color: #8c6239;
            }

            .inventory-v14-close svg {
                width: 18px;
                height: 18px;
            }

            .inventory-v14-wallet-wrap {
                position: relative;
            }

            .inventory-v14-wallet-trigger {
                display: flex;
                min-width: 126px;
                height: 44px;
                align-items: center;
                gap: 9px;
                border-radius: 12px;
                padding: 4px 10px 4px 8px;
                text-align: left;
                color: #53150f;
            }

            .inventory-v14-pouch {
                display: flex;
                width: 31px;
                height: 31px;
                flex: 0 0 31px;
                align-items: center;
                justify-content: center;
                border-radius: 9px;
                background: #eee3d1;
                color: #8c6239;
            }

            .inventory-v14-pouch svg {
                width: 25px;
                height: 25px;
            }

            .inventory-v14-pouch path,
            .inventory-v14-pouch circle {
                stroke: currentColor;
                stroke-width: 1.45;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            .inventory-v14-wallet-copy {
                display: flex;
                min-width: 45px;
                flex-direction: column;
                line-height: 1.05;
            }

            .inventory-v14-wallet-label {
                margin-bottom: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v14-wallet-copy strong {
                font-family: Georgia, serif;
                font-size: 18px;
                font-weight: 900;
                color: #53150f;
            }

            .inventory-v14-wallet-chevron {
                width: 15px;
                height: 15px;
                margin-left: auto;
                color: #8c6239;
                transition: transform .14s ease;
            }

            .inventory-v14-wallet-chevron.is-open {
                transform: rotate(180deg);
            }

            .inventory-v14-wallet-panel {
                position: absolute;
                top: calc(100% + 8px);
                right: 0;
                z-index: 90;
                width: 330px;
                overflow: hidden;
                border: 1px solid #cdbb9f;
                border-radius: 14px;
                background: #faf8f2;
            }

            .inventory-v14-wallet-panel-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                border-bottom: 1px solid rgba(205,187,159,.55);
                padding: 12px 13px;
            }

            .inventory-v14-wallet-panel-head > div {
                min-width: 0;
            }

            .inventory-v14-wallet-panel-head strong {
                display: block;
                font-family: Georgia, serif;
                font-size: 15px;
                color: #53150f;
            }

            .inventory-v14-wallet-panel-head span {
                display: block;
                margin-top: 1px;
                font-size: 10px;
                color: #8c6239;
            }

            .inventory-v14-saving {
                flex: 0 0 auto;
                font-size: 10px !important;
                font-weight: 800;
                color: #8c6239 !important;
            }

            .inventory-v14-coins {
                padding: 5px 8px 8px;
            }

            .inventory-v14-coin-row {
                display: grid;
                min-height: 48px;
                grid-template-columns: 34px minmax(0, 1fr) 86px;
                align-items: center;
                gap: 9px;
                border-bottom: 1px solid rgba(216,199,171,.48);
                padding: 5px 4px;
            }

            .inventory-v14-coin-row:last-child {
                border-bottom: 0;
            }

            .inventory-v14-coin-icon {
                display: flex;
                width: 28px;
                height: 28px;
                align-items: center;
                justify-content: center;
                border: 1px solid currentColor;
                border-radius: 50%;
            }

            .inventory-v14-coin-icon svg {
                width: 22px;
                height: 22px;
            }

            .inventory-v14-coin-icon circle,
            .inventory-v14-coin-icon path {
                stroke: currentColor;
                stroke-width: 1.25;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            .inventory-v14-coin-icon.coin-copper {
                background: #f3ddcf;
                color: #a4512d;
            }

            .inventory-v14-coin-icon.coin-silver {
                background: #ececea;
                color: #777a7d;
            }

            .inventory-v14-coin-icon.coin-electrum {
                background: #eee7c8;
                color: #8f7e38;
            }

            .inventory-v14-coin-icon.coin-gold {
                background: #f5e3ad;
                color: #b17812;
            }

            .inventory-v14-coin-icon.coin-platinum {
                background: #edf0ed;
                color: #7b8582;
            }

            .inventory-v14-coin-name {
                display: flex;
                min-width: 0;
                align-items: baseline;
                gap: 7px;
            }

            .inventory-v14-coin-name b {
                width: 23px;
                font-size: 11px;
                color: #53150f;
            }

            .inventory-v14-coin-name small {
                overflow: hidden;
                font-size: 12px;
                color: #6b5548;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v14-coin-row input {
                width: 100%;
                height: 34px;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 8px;
                background: #fffdf9;
                padding: 0 9px;
                text-align: right;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
                color: #53150f;
                outline: none;
            }

            .inventory-v14-coin-row input:focus {
                border-color: #8c6239;
                box-shadow: 0 0 0 2px rgba(140,98,57,.07);
            }

            .inventory-v14-attunement {
                border-bottom: 1px solid rgba(205,187,159,.58);
                padding: 18px 0;
            }

            .inventory-v14-section-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-bottom: 12px;
            }

            .inventory-v14-section-title {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 10px;
            }

            .inventory-v14-diamond {
                display: flex;
                width: 27px;
                height: 27px;
                flex: 0 0 27px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.75);
                border-radius: 8px;
                color: #b29161;
                font-size: 10px;
            }

            .inventory-v14-title-line {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .inventory-v14-title-line h3 {
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
                line-height: 1.1;
                color: #53150f;
            }

            .inventory-v14-count {
                display: inline-flex;
                min-height: 24px;
                align-items: center;
                border-radius: 999px;
                background: #efe9dc;
                padding: 0 8px;
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;
                color: #8c6239;
            }

            .inventory-v14-section-title p {
                margin-top: 3px;
                font-size: 12px;
                color: #8c6239;
            }

            .inventory-v14-text-action {
                flex: 0 0 auto;
                border-bottom: 1px solid transparent;
                padding: 3px 0;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .05em;
                text-transform: uppercase;
                color: #6b1d14;
            }

            .inventory-v14-text-action:hover {
                border-bottom-color: currentColor;
            }

            .inventory-v14-slots {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 9px;
            }

            .inventory-v14-slot {
                min-height: 82px;
                overflow: hidden;
                border: 1px solid rgba(205,187,159,.72);
                border-radius: 12px;
                background: #faf8f2;
                padding: 8px;
                text-align: left;
                transition: border-color .14s ease, background .14s ease;
            }

            .inventory-v14-slot:hover {
                border-color: rgba(140,98,57,.56);
                background: #f5f0e6;
            }

            .inventory-v14-slot-content {
                display: flex;
                height: 100%;
                align-items: center;
                gap: 10px;
            }

            .inventory-v14-slot-image {
                display: flex;
                width: 58px !important;
                height: 58px !important;
                flex: 0 0 58px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(216,199,171,.65);
                border-radius: 9px;
                background: #fffdf9;
            }

            .inventory-v14-slot-image span {
                font-family: Georgia, serif;
                font-size: 20px;
                color: rgba(140,98,57,.34);
            }

            .inventory-v14-slot-copy {
                min-width: 0;
                flex: 1;
            }

            .inventory-v14-slot-copy small {
                display: block;
                margin-bottom: 3px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .065em;
                text-transform: uppercase;
                color: #a07855;
            }

            .inventory-v14-slot-copy strong {
                display: block;
                overflow: hidden;
                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;
                line-height: 1.15;
                color: #53150f;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v14-slot-copy > span {
                display: block;
                margin-top: 3px;
                overflow: hidden;
                font-size: 11px;
                color: #8c6239;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v14-slot-empty {
                display: flex;
                min-height: 64px;
                align-items: center;
                justify-content: center;
                gap: 7px;
                color: #aa8b68;
                font-size: 11px;
                font-weight: 800;
            }

            .inventory-v14-slot-plus {
                font-size: 18px;
                font-weight: 400;
            }

            .inventory-v14-extra-attuned {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px;
                margin-top: 9px;
            }

            .inventory-v14-extra-attuned > span {
                margin-right: 2px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                color: #8c6239;
            }

            .inventory-v14-extra-attuned button {
                border: 1px solid rgba(205,187,159,.68);
                border-radius: 999px;
                background: #faf8f2;
                padding: 4px 9px;
                font-size: 10px;
                font-weight: 800;
                color: #53150f;
            }

            .inventory-v14 > .pt-4 {
                padding-top: 18px !important;
            }

            .inventory-v14-group-head {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 10px;
                border-bottom: 1px solid rgba(205,187,159,.58);
                padding-bottom: 9px;
            }

            .inventory-v14-group-head > div {
                min-width: 0;
            }

            .inventory-v14-group-head h3 {
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
                line-height: 1.12;
                color: #53150f;
            }

            .inventory-v14-group-head p {
                margin-top: 3px;
                font-size: 12px;
                color: #9a795b;
            }

            .inventory-v14-group-count {
                display: inline-flex;
                min-width: 30px;
                height: 28px;
                margin-left: auto;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(205,187,159,.64);
                border-radius: 999px;
                background: #faf8f2;
                padding: 0 8px;
                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                color: #8c6239;
            }

            .inventory-v14 .inventory-v11-item-card {
                border-color: rgba(205,187,159,.68);
                border-radius: 14px;
                background: #faf8f2;
                box-shadow: none;
            }

            .inventory-v14 .inventory-v11-item-card::before {
                top: 10px;
                bottom: 10px;
                width: 3px;
                background: rgba(140,98,57,.36);
            }

            .inventory-v14 .inventory-v11-item-card.magical::before {
                background: #6b1d14;
            }

            .inventory-v14 .inventory-v11-item-card:hover,
            .inventory-v14 .inventory-v11-item-card:focus-within {
                transform: none;
                border-color: rgba(140,98,57,.55);
                background: #f7f2e8;
                box-shadow: none;
            }

            .inventory-v14 .inventory-v11-item-card > div:first-child {
                gap: 14px !important;
                padding: 13px 14px 13px 17px !important;
            }

            .inventory-v14 .inventory-v11-item-card > div:first-child > .inventory-v10-thumb {
                width: 82px !important;
                height: 82px !important;
                border-radius: 11px !important;
                background: #f3eee4;
            }

            .inventory-v14 .inventory-v11-item-card h4 {
                font-size: 18px !important;
                line-height: 1.15 !important;
            }

            .inventory-v14 .inventory-v11-item-card em {
                font-size: 13px !important;
                line-height: 1.35 !important;
            }

            .inventory-v14 .inventory-v11-item-card p {
                font-size: 12px !important;
            }

            .inventory-v14 .inventory-v11-feature-strip {
                border-top-color: rgba(216,199,171,.52);
                background: #f4efe5;
            }

            .inventory-v14 .inventory-v11-feature-usage {
                background: #faf8f2;
            }

            @media (max-width: 760px) {
                .inventory-v14-toolbar {
                    align-items: flex-start;
                }

                .inventory-v14-toolbar-right {
                    position: static;
                }

                .inventory-v14-wallet-panel {
                    right: 0;
                    width: min(330px, calc(100vw - 34px));
                }

                .inventory-v14-slots {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 560px) {
                .inventory-v14-toolbar {
                    flex-direction: column;
                }

                .inventory-v14-toolbar-right {
                    width: 100%;
                    justify-content: flex-end;
                }

                .inventory-v14-actions {
                    width: 100%;
                }

                .inventory-v14-primary,
                .inventory-v14-action {
                    flex: 1;
                }

                .inventory-v14-section-head {
                    align-items: flex-start;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V15 — EQUIPAMENTO MAIS ORGANIZADO
            |--------------------------------------------------------------------------
            */

            /*
            | O topo já possui espaçamento suficiente; removemos o filete longo
            | para não adicionar mais uma divisão visual ao drawer.
            */
            .inventory-v15 .inventory-v14-toolbar {
                border-bottom: 0;

                padding-bottom: 10px;
            }

            /*
            |--------------------------------------------------------------------------
            | ESTADO + AÇÃO NO CARD
            |--------------------------------------------------------------------------
            */

            .inventory-v15-card-actions {
                display: flex;

                align-items: center;
                justify-content: flex-end;

                gap: 7px;

                margin-top: 8px;
            }

            .inventory-v15-equipped-state {
                display: inline-flex;

                min-height: 28px;

                align-items: center;

                gap: 6px;

                border:
                    1px solid
                    rgba(205,187,159,.44);

                border-radius: 999px;

                background:
                    rgba(247,240,230,.72);

                padding:
                    0 8px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                color: #92745c;
            }

            .inventory-v15-equipped-state.is-active {
                border-color:
                    rgba(107,29,20,.18);

                background:
                    rgba(107,29,20,.055);

                color: #6b1d14;
            }

            .inventory-v15-equipped-dot {
                width: 6px;
                height: 6px;

                flex: 0 0 6px;

                border:
                    1px solid
                    rgba(140,98,57,.55);

                border-radius: 50%;

                background: #faf8f2;
            }

            .inventory-v15-equipped-state.is-active
            .inventory-v15-equipped-dot {
                border-color: #6b1d14;
                background: #6b1d14;
            }

            .inventory-v15-equip-toggle,
            .inventory-v15-detail-equip {
                display: inline-flex;

                min-height: 30px;

                align-items: center;
                justify-content: center;

                gap: 6px;

                border:
                    1px solid
                    rgba(205,187,159,.70);

                border-radius: 8px;

                padding:
                    0 9px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                transition:
                    border-color .14s ease,
                    background .14s ease,
                    color .14s ease;
            }

            .inventory-v15-equip-toggle svg,
            .inventory-v15-detail-equip svg {
                width: 13px;
                height: 13px;

                flex: 0 0 13px;
            }

            /*
            | Equipar é ação positiva.
            */
            .inventory-v15-equip-toggle.is-stowed,
            .inventory-v15-detail-equip.is-stowed {
                border-color: #6b1d14;

                background: #6b1d14;

                color: #faf8f2;
            }

            .inventory-v15-equip-toggle.is-stowed:hover,
            .inventory-v15-detail-equip.is-stowed:hover {
                border-color: #53150f;
                background: #53150f;
            }

            /*
            | Guardar apenas muda o estado; não deve parecer destrutivo.
            */
            .inventory-v15-equip-toggle.is-equipped,
            .inventory-v15-detail-equip.is-equipped {
                border-color:
                    rgba(176,140,98,.52);

                background: #f7f0e6;

                color: #8c6239;
            }

            .inventory-v15-equip-toggle.is-equipped:hover,
            .inventory-v15-detail-equip.is-equipped:hover {
                border-color:
                    rgba(140,98,57,.56);

                background: #eadbc8;

                color: #53150f;
            }

            .inventory-v15-equip-toggle:disabled,
            .inventory-v15-detail-equip:disabled {
                cursor: wait;
                opacity: .40;
            }

            /*
            |--------------------------------------------------------------------------
            | DRAWER DE EQUIPADOS
            |--------------------------------------------------------------------------
            */

            .inventory-v15-equipped-drawer-actions {
                display: flex;

                flex-wrap: wrap;

                align-items: center;
                justify-content: flex-end;

                gap: 7px;

                margin-top: 12px;

                border-top:
                    1px solid
                    rgba(216,199,171,.46);

                padding-top: 10px;
            }

            .inventory-v15-drawer-detail {
                display: inline-flex;

                min-height: 30px;

                align-items: center;
                justify-content: center;

                border-radius: 8px;

                padding:
                    0 9px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .04em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v15-drawer-detail:hover {
                background: #efe9dc;
                color: #53150f;
            }

            @media (max-width: 560px) {
                .inventory-v15-card-actions {
                    justify-content: flex-start;
                }

                .inventory-v15-equipped-state {
                    margin-right: auto;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V16 — POLAROIDS + EQUIPADOS
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | TOOLBAR SEM X
            |--------------------------------------------------------------------------
            */

            .inventory-v16 .inventory-v14-toolbar-right {
                min-height: 44px;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKERS MAIS COMPACTOS
            |--------------------------------------------------------------------------
            |
            | Reduzimos o volume dos controles sem reduzir demais o texto.
            |
            */

            .inventory-v16 .inventory-v11-feature-usage {
                gap: 5px;

                border-radius: 8px;

                padding:
                    4px 5px 4px 7px;
            }

            .inventory-v16 .inventory-v11-usage-name {
                max-width: 108px;

                font-size: 10px;
            }

            .inventory-v16 .inventory-v11-usage-recovery {
                font-size: 8px;
            }

            .inventory-v16 .inventory-v11-mini-tracker {
                border-radius: 7px;
            }

            .inventory-v16 .inventory-v11-mini-tracker button {
                width: 22px;
                height: 22px;

                font-size: 12px;
            }

            .inventory-v16 .inventory-v11-mini-tracker strong {
                min-width: 34px;

                padding:
                    0 4px;

                font-size: 10.5px;
            }

            .inventory-v16 .inventory-v10-tracker {
                border-radius: 8px;
            }

            .inventory-v16 .inventory-v10-tracker button {
                width: 25px;
                height: 25px;

                font-size: 13px;
            }

            .inventory-v16 .inventory-v10-tracker strong {
                min-width: 42px;

                padding:
                    0 6px;

                font-size: 11px;
            }

            /*
            |--------------------------------------------------------------------------
            | LISTA PRINCIPAL — CARDS TIPO POLAROID
            |--------------------------------------------------------------------------
            */

            .inventory-v16-items-grid {
                display: grid;

                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(200px, 1fr)
                    );

                gap: 11px;
            }

            .inventory-v16 .inventory-v16-polaroid {
                display: flex;

                min-width: 0;
                min-height: 0;

                flex-direction: column;

                overflow: hidden;

                border-radius: 12px;

                background: #faf8f2;

                box-shadow:
                    0 1px 0 rgba(255,255,255,.78) inset;
            }

            .inventory-v16 .inventory-v16-polaroid::before {
                top: 8px;
                bottom: 8px;

                width: 3px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child {
                display: block !important;

                flex: 1;

                padding:
                    9px 9px 8px 12px !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .inventory-v10-thumb {
                width: 100% !important;
                height: 132px !important;

                border-radius: 8px !important;

                background: #f3eee4;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .inventory-v10-thumb
            img {
                object-fit: contain;

                padding: 4px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                margin-top: 9px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            h4 {
                display: -webkit-box;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 15px !important;
                line-height: 1.15 !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            em {
                display: -webkit-box;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10.5px !important;
                line-height: 1.32 !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            p {
                font-size: 10.5px !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v15-card-actions {
                justify-content: space-between;

                gap: 5px;

                margin-top: 7px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v15-equipped-state {
                min-height: 25px;

                padding:
                    0 7px;

                font-size: 7.5px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v15-equip-toggle {
                min-height: 27px;

                padding:
                    0 8px;

                font-size: 8px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            > div:first-child
            > .hidden.shrink-0 {
                display: none !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v11-feature-strip {
                margin-top: auto;

                padding:
                    7px 8px 8px 11px !important;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v11-feature-strip
            > div {
                gap: 5px;
            }

            .inventory-v16
            .inventory-v16-polaroid
            .inventory-v11-feature-strip
            > div
            > span:first-child {
                width: 100%;

                margin: 0 0 1px;

                font-size: 7px;
            }

            /*
            |--------------------------------------------------------------------------
            | GAVETA DE EQUIPADOS
            |--------------------------------------------------------------------------
            */

            .inventory-v16-equipped-drawer {
                position: absolute;
                inset-block: 0;
                right: 0;
                z-index: 10;

                display: flex;

                width:
                    min(
                        760px,
                        92vw
                    );

                height: 100%;

                flex-direction: column;

                border-left:
                    1px solid
                    #cdbb9f;

                background:
                    linear-gradient(
                        180deg,
                        #faf8f2 0%,
                        #f7f1e8 100%
                    );

                box-shadow:
                    -18px 0 50px
                    rgba(43,29,23,.16);
            }

            .inventory-v16-equipped-header {
                display: flex;

                flex: 0 0 auto;

                align-items: flex-start;
                justify-content: space-between;

                gap: 18px;

                border-bottom:
                    1px solid
                    rgba(205,187,159,.58);

                background:
                    rgba(239,233,220,.58);

                padding:
                    18px 20px 16px;
            }

            .inventory-v16-equipped-header-copy {
                min-width: 0;
            }

            .inventory-v16-equipped-kicker {
                display: block;

                margin-bottom: 4px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .13em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v16-equipped-title-row {
                display: flex;

                align-items: center;

                gap: 9px;
            }

            .inventory-v16-equipped-title-row h3 {
                font-family: Georgia, serif;
                font-size: 24px;
                font-weight: 900;
                line-height: 1;

                color: #53150f;
            }

            .inventory-v16-equipped-count {
                display: inline-flex;

                min-width: 27px;
                height: 27px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.30);

                border-radius: 999px;

                background: #faf8f2;

                padding:
                    0 7px;

                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #6b1d14;
            }

            .inventory-v16-equipped-header p {
                margin-top: 6px;

                font-size: 11px;
                line-height: 1.4;

                color: #8c6239;
            }

            .inventory-v16-close-hint {
                flex: 0 0 auto;

                margin-top: 4px;

                font-size: 8px;
                font-weight: 800;
                letter-spacing: .04em;

                color:
                    rgba(140,98,57,.62);
            }

            .inventory-v16-equipped-body {
                min-height: 0;

                flex: 1;

                overflow-y: auto;

                padding:
                    14px 15px 18px;
            }

            .inventory-v16-equipped-grid {
                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 12px;
            }

            .inventory-v16-equipped-card {
                display: flex;

                min-width: 0;

                flex-direction: column;

                overflow: hidden;

                border:
                    1px solid
                    rgba(205,187,159,.70);

                border-radius: 12px;

                background: #faf8f2;

                box-shadow:
                    0 1px 0 rgba(255,255,255,.86) inset;

                transition:
                    border-color .14s ease,
                    background .14s ease;
            }

            .inventory-v16-equipped-card:hover {
                border-color:
                    rgba(140,98,57,.52);

                background: #fffdf9;
            }

            .inventory-v16-equipped-main {
                display: block;

                width: 100%;

                padding:
                    9px;

                text-align: left;
            }

            .inventory-v16-equipped-photo {
                display: flex;

                width: 100%;
                height: 154px;

                align-items: center;
                justify-content: center;

                overflow: hidden;

                border:
                    1px solid
                    rgba(216,199,171,.60);

                border-radius: 8px;

                background:
                    #f3eee4;
            }

            .inventory-v16-equipped-photo img {
                width: 100%;
                height: 100%;

                object-fit: contain;

                padding: 5px;
            }

            .inventory-v16-equipped-photo > span {
                font-family: Georgia, serif;
                font-size: 28px;

                color:
                    rgba(140,98,57,.28);
            }

            .inventory-v16-equipped-copy {
                padding:
                    9px 2px 1px;
            }

            .inventory-v16-equipped-badges {
                display: flex;

                flex-wrap: wrap;

                gap: 5px;

                margin-bottom: 5px;
            }

            .inventory-v16-equipped-badge {
                display: inline-flex;

                min-height: 20px;

                align-items: center;

                border-radius: 999px;

                background:
                    rgba(107,29,20,.065);

                padding:
                    0 6px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .04em;

                text-transform: uppercase;

                color: #6b1d14;
            }

            .inventory-v16-equipped-badge.is-attuned {
                background: #efe9dc;

                color: #8c6239;
            }

            .inventory-v16-equipped-copy h4 {
                display: -webkit-box;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-family: Georgia, serif;
                font-size: 17px;
                font-weight: 900;
                line-height: 1.12;

                color: #53150f;
            }

            .inventory-v16-equipped-copy em {
                display: -webkit-box;

                margin-top: 4px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10.5px;
                font-weight: 700;
                line-height: 1.35;

                color: #6b5548;
            }

            .inventory-v16-equipped-mechanics {
                display: -webkit-box;

                margin-top: 6px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10px;
                font-weight: 800;
                line-height: 1.35;

                color: #8c6239;
            }

            .inventory-v16-equipped-description {
                display: -webkit-box;

                margin-top: 4px;

                overflow: hidden;

                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;

                font-size: 10px;
                line-height: 1.4;

                color:
                    rgba(107,85,72,.90);
            }

            .inventory-v16-equipped-trackers {
                display: flex;

                flex-wrap: wrap;

                align-items: center;

                gap: 5px;

                margin-top: auto;

                border-top:
                    1px solid
                    rgba(216,199,171,.48);

                background: #f4efe5;

                padding:
                    8px 9px;
            }

            .inventory-v16-tracker-label {
                width: 100%;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .09em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.78);
            }

            .inventory-v16-more-trackers {
                display: inline-flex;

                min-width: 23px;
                height: 23px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.58);

                border-radius: 999px;

                background: #faf8f2;

                padding:
                    0 6px;

                font-size: 8px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v16-equipped-actions {
                display: flex;

                align-items: center;
                justify-content: space-between;

                gap: 7px;

                border-top:
                    1px solid
                    rgba(216,199,171,.48);

                padding:
                    8px 9px;
            }

            .inventory-v16-view-item {
                min-height: 28px;

                border-radius: 7px;

                padding:
                    0 8px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v16-view-item:hover {
                background: #efe9dc;

                color: #53150f;
            }

            .inventory-v16-equipped-empty {
                display: flex;

                min-height: 220px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                border:
                    1px dashed
                    rgba(205,187,159,.68);

                border-radius: 14px;

                background:
                    rgba(255,253,249,.62);

                padding:
                    30px;

                text-align: center;
            }

            .inventory-v16-empty-mark {
                margin-bottom: 8px;

                font-family: Georgia, serif;
                font-size: 30px;

                color:
                    rgba(140,98,57,.35);
            }

            .inventory-v16-equipped-empty strong {
                font-family: Georgia, serif;
                font-size: 18px;

                color: #53150f;
            }

            .inventory-v16-equipped-empty p {
                max-width: 300px;

                margin-top: 4px;

                font-size: 10.5px;
                line-height: 1.45;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 720px) {
                .inventory-v16-items-grid {
                    grid-template-columns:
                        repeat(
                            2,
                            minmax(0, 1fr)
                        );
                }

                .inventory-v16-equipped-drawer {
                    width:
                        min(
                            620px,
                            94vw
                        );
                }
            }

            @media (max-width: 520px) {
                .inventory-v16-items-grid,
                .inventory-v16-equipped-grid {
                    grid-template-columns: 1fr;
                }

                .inventory-v16-equipped-header {
                    padding:
                        15px 14px 13px;
                }

                .inventory-v16-close-hint {
                    display: none;
                }

                .inventory-v16-equipped-body {
                    padding:
                        11px 10px 14px;
                }

                .inventory-v16
                .inventory-v16-polaroid
                > div:first-child
                > .inventory-v10-thumb {
                    height: 160px !important;
                }

                .inventory-v16-equipped-photo {
                    height: 180px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V17 — MODAL EDITORIAL + SINTONIZAÇÃO
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-modal {
                border-color:
                    rgba(176,140,98,.58) !important;

                background:
                    linear-gradient(
                        180deg,
                        #fbf8f1 0%,
                        #f8f2e8 100%
                    ) !important;

                box-shadow:
                    0 28px 80px rgba(43,29,23,.24),
                    0 1px 0 rgba(255,255,255,.86) inset !important;
            }

            /*
            |--------------------------------------------------------------------------
            | TOPBAR
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-topbar {
                min-height: 52px;

                border-bottom-color:
                    rgba(160,119,77,.30) !important;

                background:
                    linear-gradient(
                        180deg,
                        #f1e5d6 0%,
                        #eadbc8 100%
                    ) !important;

                padding:
                    9px 13px 9px 16px !important;
            }

            .inventory-v17-detail-topbar
            > span:first-child {
                font-size: 8.5px !important;
                letter-spacing: .13em !important;

                color: #7d604d !important;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTEÚDO PRINCIPAL
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-scroll {
                scrollbar-color:
                    rgba(140,98,57,.45)
                    transparent;
            }

            .inventory-v17-detail-identity {
                align-self: stretch !important;
            }

            .inventory-v17-title-row {
                display: flex;

                align-items: flex-start;
                justify-content: space-between;

                gap: 12px;
            }

            .inventory-v17-item-kicker {
                display: block;

                margin-bottom: 4px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .12em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.78);
            }

            .inventory-v17-item-title {
                font-family: Georgia, serif;
                font-size: 30px;
                font-weight: 900;
                line-height: 1.02;

                color: #53150f;
            }

            .inventory-v17-status-badge {
                display: inline-flex;

                min-height: 25px;

                flex: 0 0 auto;

                align-items: center;

                gap: 6px;

                border:
                    1px solid
                    rgba(107,29,20,.16);

                border-radius: 999px;

                background:
                    rgba(107,29,20,.055);

                padding:
                    0 8px;

                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .055em;

                text-transform: uppercase;

                color: #6b1d14;
            }

            .inventory-v17-status-dot {
                width: 6px;
                height: 6px;

                border-radius: 50%;

                background: #6b1d14;
            }

            .inventory-v17-classification {
                display: block;

                margin-top: 7px;

                font-size: 13px;
                font-weight: 700;
                line-height: 1.4;

                color: #6b5548;
            }

            /*
            |--------------------------------------------------------------------------
            | SINTONIZAÇÃO
            |--------------------------------------------------------------------------
            |
            | Antes era um pill pequeno junto da classificação.
            | Agora vira um estado de gameplay claro, visível e acionável.
            |
            */

            .inventory-v17-attunement {
                display: grid;

                grid-template-columns:
                    38px
                    minmax(0, 1fr)
                    auto;

                align-items: center;

                gap: 10px;

                margin-top: 14px;

                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.42);

                border-radius: 11px;

                background:
                    linear-gradient(
                        180deg,
                        #f5eadc 0%,
                        #f0e2d1 100%
                    );

                padding:
                    9px 10px;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.62);
            }

            .inventory-v17-attunement.is-attuned {
                border-color:
                    rgba(107,29,20,.30);

                background:
                    linear-gradient(
                        180deg,
                        rgba(107,29,20,.070) 0%,
                        rgba(107,29,20,.040) 100%
                    );
            }

            .inventory-v17-attunement-icon {
                display: flex;

                width: 38px;
                height: 38px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 9px;

                background: #fffdf8;

                font-family: Georgia, serif;
                font-size: 18px;

                color: #8c6239;
            }

            .inventory-v17-attunement.is-attuned
            .inventory-v17-attunement-icon {
                border-color:
                    rgba(107,29,20,.22);

                background:
                    rgba(107,29,20,.055);

                color: #6b1d14;
            }

            .inventory-v17-attunement-copy {
                min-width: 0;
            }

            .inventory-v17-attunement-kicker {
                display: block;

                margin-bottom: 1px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .11em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v17-attunement-copy strong {
                display: block;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v17-attunement-copy p {
                margin-top: 2px;

                max-width: 470px;

                font-size: 9.5px;
                line-height: 1.38;

                color: #7d604d;
            }

            .inventory-v17-attunement-action {
                display: inline-flex;

                min-height: 32px;

                flex: 0 0 auto;

                align-items: center;
                justify-content: center;

                gap: 5px;

                border-radius: 8px;

                padding:
                    0 10px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            .inventory-v17-attunement-action.is-primary {
                border:
                    1px solid #6b1d14;

                background: #6b1d14;

                color: #faf8f2;
            }

            .inventory-v17-attunement-action.is-primary:hover {
                border-color: #53150f;
                background: #53150f;
            }

            .inventory-v17-attunement-action.is-secondary {
                border:
                    1px solid
                    rgba(107,29,20,.22);

                background:
                    rgba(255,253,248,.72);

                color: #6b1d14;
            }

            .inventory-v17-attunement-action.is-secondary:hover {
                background:
                    rgba(107,29,20,.070);
            }

            /*
            |--------------------------------------------------------------------------
            | MECÂNICAS
            |--------------------------------------------------------------------------
            */

            .inventory-v17
            .inventory-v11-detail-mechanics {
                margin-top: 14px !important;

                border-color:
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                padding:
                    11px 12px;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.70);
            }

            .inventory-v17
            .inventory-v11-detail-mechanics
            p {
                font-size: 12.5px !important;
                line-height: 1.5 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | SEÇÕES DO MODAL
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-section {
                margin-top: 24px !important;
            }

            .inventory-v17
            .inventory-v11-detail-section-title {
                margin-bottom: 10px;

                font-size: 9px;
                letter-spacing: .095em;
            }

            .inventory-v17
            .inventory-v11-ability-card {
                border-color:
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.68);
            }

            .inventory-v17
            .inventory-v11-ability-header {
                border-bottom-color:
                    rgba(188,154,111,.28);

                background: #f0e4d5;

                padding:
                    9px 11px;
            }

            .inventory-v17
            .inventory-v11-ability-body {
                padding:
                    10px 12px 12px;
            }

            /*
            |--------------------------------------------------------------------------
            | FOTO
            |--------------------------------------------------------------------------
            */

            .inventory-v17-detail-modal
            .inventory-v10-thumb {
                border-color:
                    rgba(176,140,98,.42) !important;

                border-radius: 12px !important;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf8 0%,
                        #f2eadf 100%
                    ) !important;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.76),
                    0 5px 18px rgba(83,21,15,.05) !important;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 640px) {
                .inventory-v17-attunement {
                    grid-template-columns:
                        36px
                        minmax(0, 1fr);
                }

                .inventory-v17-attunement-action {
                    grid-column:
                        1 / -1;

                    width: 100%;
                }

                .inventory-v17-item-title {
                    font-size: 26px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V18 — SINTONIZAÇÃO NA BARRA DE AÇÕES
            |--------------------------------------------------------------------------
            |
            | A sintonização deixa de interromper o fluxo de leitura do item.
            | Ela passa a ser uma ação compacta ao lado de Equipar/Guardar.
            |
            */

            .inventory-v18-attunement-toggle {
                display: inline-flex;

                min-height: 30px;

                align-items: center;
                justify-content: center;

                gap: 5px;

                border:
                    1px solid
                    rgba(176,140,98,.48);

                border-radius: 8px;

                padding:
                    0 9px;

                font-size: 8.5px;
                font-weight: 900;
                letter-spacing: .035em;

                text-transform: uppercase;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            /*
            | Ainda não sintonizado:
            | ação disponível, mas sem competir com Editar.
            */
            .inventory-v18-attunement-toggle.is-unattuned {
                background: #f7f0e6;

                color: #8c6239;
            }

            .inventory-v18-attunement-toggle.is-unattuned:hover {
                border-color:
                    rgba(107,29,20,.30);

                background: #eadbc8;

                color: #6b1d14;
            }

            /*
            | Já sintonizado:
            | o estado fica visível pelo vinho suave, sem virar um banner.
            */
            .inventory-v18-attunement-toggle.is-attuned {
                border-color:
                    rgba(107,29,20,.22);

                background:
                    rgba(107,29,20,.065);

                color: #6b1d14;
            }

            .inventory-v18-attunement-toggle.is-attuned:hover {
                border-color:
                    rgba(107,29,20,.34);

                background:
                    rgba(107,29,20,.10);

                color: #53150f;
            }

            .inventory-v18-attunement-toggle:disabled {
                cursor: wait;
                opacity: .40;
            }

            .inventory-v18-attunement-mark {
                font-family: Georgia, serif;
                font-size: 10px;
                line-height: 1;
            }

            /*
            | Como o painel grande saiu, a mecânica volta a ficar próxima
            | da classificação e a leitura segue naturalmente para baixo.
            */
            .inventory-v18
            .inventory-v11-detail-mechanics {
                margin-top: 13px !important;
            }

            @media (max-width: 640px) {
                .inventory-v18-attunement-toggle {
                    min-height: 29px;

                    padding:
                        0 8px;

                    font-size: 8px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V19 — MECÂNICAS MAIS LIMPAS
            |--------------------------------------------------------------------------
            */

            .inventory-v19
            .inventory-v19-detail-mechanics {
                padding: 0 !important;

                overflow: visible;

                border: 0 !important;

                background:
                    transparent !important;

                box-shadow:
                    none !important;
            }

            .inventory-v19-weapon-block {
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.72);
            }

            /*
            | Identidade mecânica + dano.
            */
            .inventory-v19-weapon-heading {
                display: flex;

                min-height: 48px;

                align-items: center;
                justify-content: space-between;

                gap: 18px;

                background: #f7f0e6;

                padding:
                    9px 12px;
            }

            .inventory-v19-weapon-heading
            > strong {
                min-width: 0;

                font-family: Georgia, serif;
                font-size: 13.5px;
                font-weight: 900;
                line-height: 1.25;

                color: #53150f;
            }

            .inventory-v19-damage-line {
                display: inline-flex;

                flex: 0 0 auto;

                align-items: baseline;

                gap: 5px;

                color: #7d604d;
            }

            .inventory-v19-damage-line
            > span {
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .07em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v19-damage-line
            > b {
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #6b1d14;
            }

            /*
            | Propriedades / Maestrias em linhas próprias.
            */
            .inventory-v19-mechanic-row {
                display: grid;

                grid-template-columns:
                    88px
                    minmax(0, 1fr);

                align-items: center;

                gap: 9px;

                min-height: 40px;

                border-top:
                    1px solid
                    rgba(188,154,111,.26);

                padding:
                    7px 12px;
            }

            .inventory-v19-mechanic-label {
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .075em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v19-mechanic-values {
                display: flex;

                min-width: 0;

                flex-wrap: wrap;

                gap: 6px;
            }

            .inventory-v19
            .inventory-v19-mechanic-values
            .inventory-v10-chip {
                min-height: 27px;

                padding:
                    0 9px;

                font-size: 9px;
            }

            /*
            | Armadura / escudo / propriedades custom continuam suportados.
            */
            .inventory-v19-other-mechanics {
                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 11px;

                background: #fffdf8;

                padding:
                    10px 12px;

                font-size: 12px;
                font-weight: 700;
                line-height: 1.5;

                color: #5f3a27;
            }

            .inventory-v19-weapon-block
            + .inventory-v19-other-mechanics {
                margin-top: 8px;
            }

            @media (max-width: 560px) {
                .inventory-v19-weapon-heading {
                    align-items: flex-start;

                    flex-direction: column;

                    gap: 4px;
                }

                .inventory-v19-mechanic-row {
                    grid-template-columns: 1fr;

                    gap: 5px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V20 — MECÂNICA EDITORIAL
            |--------------------------------------------------------------------------
            |
            | Sem caixa, sem tabela e sem chips.
            | A informação vira parte natural da leitura do item.
            |
            */

            .inventory-v20-editorial-mechanics {
                margin-top: 10px;

                max-width: 100%;

                color: #5f4636;
            }

            .inventory-v20-weapon-text {
                display: flex;

                flex-direction: column;

                gap: 4px;
            }

            /*
            | Arma Marcial - Katana (1d10 Cortante/Energia)
            */
            .inventory-v20-weapon-line {
                font-family: Georgia, serif;
                font-size: 12.5px;
                font-weight: 700;
                font-style: italic;
                line-height: 1.45;

                color: #6b5548;
            }

            /*
            | Propriedades: Acuidade, Leve
            | Maestrias: (Sap)
            */
            .inventory-v20-detail-line {
                font-size: 10.5px;
                line-height: 1.45;

                color: #6b5548;
            }

            .inventory-v20-detail-line
            strong {
                margin-right: 4px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .065em;

                text-transform: uppercase;

                color: #8c6239;
            }

            /*
            | Remove visualmente qualquer herança da caixa V19.
            */
            .inventory-v20
            .inventory-v19-detail-mechanics {
                border: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            @media (max-width: 560px) {
                .inventory-v20-weapon-line {
                    font-size: 12px;
                }

                .inventory-v20-detail-line {
                    font-size: 10px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V21 — TOOLTIPS EDITORIAIS
            |--------------------------------------------------------------------------
            |
            | Propriedades e Maestrias continuam parecendo texto normal.
            | A descrição só aparece ao passar o mouse ou focar o termo.
            |
            */

            .inventory-v21-inline-tooltip {
                position: relative;

                display: inline;

                cursor: help;
            }

            /*
            | Não vira chip, botão, badge ou link.
            | Mantém exatamente a aparência tipográfica da linha V20.
            */
            .inventory-v21-inline-term {
                display: inline;

                padding: 0;

                border: 0;

                background: transparent;

                color: inherit;

                font: inherit;
                line-height: inherit;

                text-decoration:
                    underline
                    dotted
                    rgba(140,98,57,.34);

                text-decoration-thickness: 1px;
                text-underline-offset: 2px;

                outline: none;
            }

            .inventory-v21-inline-term:focus-visible {
                border-radius: 2px;

                outline:
                    1px solid
                    rgba(140,98,57,.38);

                outline-offset: 2px;
            }

            /*
            |--------------------------------------------------------------------------
            | PAINEL
            |--------------------------------------------------------------------------
            */

            .inventory-v21-tooltip-panel {
                position: absolute;

                left: 0;
                bottom:
                    calc(100% + 8px);

                z-index: 80;

                display: block;

                width:
                    min(
                        280px,
                        72vw
                    );

                max-width: 280px;

                visibility: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.58);

                border-radius: 8px;

                background: #fbf8f1;

                padding:
                    8px 9px;

                opacity: 0;

                box-shadow:
                    0 10px 28px
                    rgba(43,29,23,.16),
                    inset 0 1px 0
                    rgba(255,255,255,.74);

                pointer-events: none;

                transform:
                    translateY(3px);

                transition:
                    opacity .12s ease,
                    transform .12s ease,
                    visibility .12s ease;

                white-space: normal;

                text-align: left;
            }

            .inventory-v21-inline-tooltip:hover
            .inventory-v21-tooltip-panel,
            .inventory-v21-inline-tooltip:focus-within
            .inventory-v21-tooltip-panel {
                visibility: visible;

                opacity: 1;

                transform:
                    translateY(0);
            }

            .inventory-v21-tooltip-panel::after {
                content: '';

                position: absolute;

                top: 100%;
                left: 14px;

                width: 8px;
                height: 8px;

                border-right:
                    1px solid
                    rgba(176,140,98,.58);

                border-bottom:
                    1px solid
                    rgba(176,140,98,.58);

                background: #fbf8f1;

                transform:
                    translateY(-4px)
                    rotate(45deg);
            }

            .inventory-v21-tooltip-title {
                display: block;

                margin-bottom: 3px;

                font-family: Georgia, serif;
                font-size: 11.5px;
                font-weight: 900;
                line-height: 1.2;

                color: #53150f;
            }

            .inventory-v21-tooltip-text {
                display: block;

                font-size: 10px;
                font-weight: 500;
                line-height: 1.45;

                color: #6b5548;
            }

            /*
            | Em telas estreitas, posiciona o tooltip de forma mais segura.
            */
            @media (max-width: 560px) {
                .inventory-v21-tooltip-panel {
                    width:
                        min(
                            240px,
                            76vw
                        );
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V22 — CONTEÚDO DOS POLAROIDS
            |--------------------------------------------------------------------------
            |
            | A aparência externa do card permanece a mesma.
            | Mudanças:
            |
            | - nenhuma ação Equipar/Guardar dentro do polaroid;
            | - apenas o badge Equipado permanece quando aplicável;
            | - estado de sintonização não é repetido no card;
            | - habilidades passivas também são mostradas;
            | - a área de habilidades tem altura controlada para manter os
            |   cards visualmente equilibrados.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | CORPO PRINCIPAL
            |--------------------------------------------------------------------------
            */

            .inventory-v22
            .inventory-v16-polaroid
            > div:first-child {
                display: flex !important;

                flex-direction: column;
            }

            .inventory-v22
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                display: flex;

                min-height: 0;

                flex: 1;
                flex-direction: column;
            }

            /*
            | Sem área de ações, o texto usa melhor o espaço restante.
            */
            .inventory-v22
            .inventory-v16-polaroid
            .inventory-v15-card-actions {
                display: none !important;
            }

            /*
            |--------------------------------------------------------------------------
            | HABILIDADES
            |--------------------------------------------------------------------------
            */

            .inventory-v22
            .inventory-v22-card-feature-strip {
                min-height: 92px;

                margin-top: auto;

                border-top:
                    1px solid
                    rgba(216,199,171,.52);

                background: #f4efe5;

                padding:
                    7px 8px 8px 11px !important;
            }

            .inventory-v22-card-feature-head {
                display: flex;

                min-height: 17px;

                align-items: center;
                justify-content: space-between;

                gap: 6px;

                margin-bottom: 4px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .085em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.78);
            }

            .inventory-v22-card-feature-count {
                display: inline-flex;

                min-width: 18px;
                height: 18px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.54);

                border-radius: 999px;

                background:
                    rgba(255,253,249,.66);

                padding:
                    0 5px;

                font-size: 7px;
                letter-spacing: 0;

                color: #8c6239;
            }

            .inventory-v22-card-feature-list {
                display: flex;

                flex-direction: column;

                gap: 4px;
            }

            .inventory-v22-card-feature-row {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    auto;

                min-height: 27px;

                align-items: center;

                gap: 5px;

                border:
                    1px solid
                    rgba(205,187,159,.52);

                border-radius: 7px;

                background:
                    rgba(255,253,249,.72);

                padding:
                    2px 4px 2px 7px;
            }

            .inventory-v22-card-feature-name {
                min-width: 0;

                overflow: hidden;

                font-family: Georgia, serif;
                font-size: 9.5px;
                font-weight: 900;
                line-height: 1.15;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKER DA HABILIDADE
            |--------------------------------------------------------------------------
            */

            .inventory-v22-card-feature-tracker {
                display: flex;

                align-items: center;

                gap: 3px;
            }

            .inventory-v22
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width: 19px;
                height: 19px;

                font-size: 10px;
            }

            .inventory-v22
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width: 29px;

                padding:
                    0 3px;

                font-size: 9px;
            }

            .inventory-v22-card-feature-recovery {
                max-width: 29px;

                overflow: hidden;

                font-size: 7px;
                font-weight: 800;

                color: #8c6239;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /*
            | Habilidade sem tracker.
            */
            .inventory-v22-card-feature-passive {
                display: inline-flex;

                min-height: 19px;

                align-items: center;

                border-radius: 999px;

                background:
                    rgba(140,98,57,.055);

                padding:
                    0 6px;

                font-size: 6.5px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                color:
                    rgba(140,98,57,.76);
            }

            .inventory-v22-card-feature-more {
                display: flex;

                align-items: center;
                justify-content: space-between;

                gap: 6px;

                min-height: 18px;

                padding:
                    0 3px;

                font-size: 7px;
                font-weight: 800;

                color:
                    rgba(140,98,57,.75);
            }

            .inventory-v22-card-feature-more
            span:last-child {
                color:
                    rgba(107,29,20,.66);
            }

            .inventory-v22-card-feature-empty {
                display: flex;

                min-height: 50px;

                align-items: center;
                justify-content: center;

                font-size: 8px;
                font-style: italic;

                color:
                    rgba(140,98,57,.52);
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 520px) {
                .inventory-v22
                .inventory-v22-card-feature-strip {
                    min-height: 88px;
                }

                .inventory-v22-card-feature-name {
                    font-size: 10px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V23 — TELA PRINCIPAL EDITORIAL
            |--------------------------------------------------------------------------
            |
            | O objetivo é trazer para a tela principal a mesma linguagem
            | visual do modal de visualização:
            |
            | - superfícies de papel claras;
            | - cabeçalhos bege-avermelhados;
            | - vinho apenas para hierarquia e ações;
            | - blocos bem definidos, sem excesso de linhas;
            | - polaroids preservados.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | TOPO
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            .inventory-v23-main-topbar {
                min-height: 58px;

                border:
                    1px solid
                    rgba(176,140,98,.42);

                border-radius: 13px;

                background:
                    linear-gradient(
                        180deg,
                        #f2e7d8 0%,
                        #eadbc8 100%
                    );

                padding:
                    8px 10px;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.72);

                margin-bottom: 12px;
            }

            .inventory-v23
            .inventory-v14-actions {
                gap: 7px;
            }

            .inventory-v23
            .inventory-v14-primary,
            .inventory-v23
            .inventory-v14-action {
                min-height: 36px;

                border-radius: 9px;

                padding:
                    0 12px;

                font-size: 10px;
            }

            .inventory-v23
            .inventory-v14-wallet-trigger {
                height: 38px;

                border-color:
                    rgba(176,140,98,.46);

                border-radius: 9px;

                background:
                    rgba(255,253,248,.78);
            }

            .inventory-v23
            .inventory-v14-pouch {
                width: 28px;
                height: 28px;

                flex-basis: 28px;

                border-radius: 7px;
            }

            .inventory-v23
            .inventory-v14-wallet-copy strong {
                font-size: 16px;
            }

            /*
            |--------------------------------------------------------------------------
            | SINTONIZAÇÃO
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            .inventory-v23-attunement-panel {
                overflow: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 13px;

                background:
                    #fbf8f1;

                padding: 0;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.70);
            }

            .inventory-v23
            .inventory-v23-attunement-panel
            .inventory-v14-section-head {
                min-height: 58px;

                margin: 0;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);

                background:
                    linear-gradient(
                        180deg,
                        #f7efe4 0%,
                        #f0e3d2 100%
                    );

                padding:
                    9px 11px;
            }

            .inventory-v23
            .inventory-v14-section-title {
                gap: 9px;
            }

            .inventory-v23
            .inventory-v14-diamond {
                width: 31px;
                height: 31px;

                flex-basis: 31px;

                border-color:
                    rgba(176,140,98,.36);

                background:
                    #fffdf8;

                color: #8c6239;
            }

            .inventory-v23
            .inventory-v14-title-line h3 {
                font-size: 19px;
            }

            .inventory-v23
            .inventory-v14-count {
                min-height: 22px;

                background:
                    rgba(107,29,20,.055);

                font-size: 10.5px;

                color: #6b1d14;
            }

            .inventory-v23
            .inventory-v14-section-title p {
                margin-top: 2px;

                font-size: 10px;

                color: #92745c;
            }

            .inventory-v23
            .inventory-v14-text-action {
                min-height: 29px;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 7px;

                background:
                    rgba(255,253,248,.72);

                padding:
                    0 8px;

                font-size: 8px;

                color: #6b1d14;
            }

            .inventory-v23
            .inventory-v14-text-action:hover {
                border-bottom-color:
                    rgba(176,140,98,.38);

                background: #eadbc8;
            }

            .inventory-v23
            .inventory-v14-slots {
                gap: 8px;

                padding:
                    10px 11px 11px;
            }

            .inventory-v23
            .inventory-v14-slot {
                min-height: 70px;

                border-color:
                    rgba(205,187,159,.62);

                border-radius: 10px;

                background: #fffdf8;

                padding: 7px;
            }

            .inventory-v23
            .inventory-v14-slot.is-filled {
                background:
                    linear-gradient(
                        180deg,
                        #fffdf9 0%,
                        #faf6ee 100%
                    );
            }

            .inventory-v23
            .inventory-v14-slot-image {
                width: 48px !important;
                height: 48px !important;

                flex-basis: 48px;

                border-radius: 8px;
            }

            .inventory-v23
            .inventory-v14-slot-copy small {
                margin-bottom: 2px;

                font-size: 7px;
            }

            .inventory-v23
            .inventory-v14-slot-copy strong {
                font-size: 13px;
            }

            .inventory-v23
            .inventory-v14-slot-copy > span {
                margin-top: 2px;

                font-size: 9px;
            }

            .inventory-v23
            .inventory-v14-slot-empty {
                min-height: 52px;

                font-size: 9px;
            }

            .inventory-v23
            .inventory-v14-extra-attuned {
                margin: 0;

                border-top:
                    1px solid
                    rgba(188,154,111,.24);

                padding:
                    8px 11px 10px;
            }

            /*
            |--------------------------------------------------------------------------
            | LISTA / GRUPOS
            |--------------------------------------------------------------------------
            */

            .inventory-v23-list {
                display: flex;

                flex-direction: column;

                gap: 12px;

                padding-top: 12px;
            }

            .inventory-v23-group-panel {
                overflow: hidden;

                margin: 0;

                border:
                    1px solid
                    rgba(176,140,98,.34);

                border-radius: 13px;

                background:
                    rgba(251,248,241,.74);

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.72);
            }

            .inventory-v23
            .inventory-v23-group-panel
            .inventory-v14-group-head {
                min-height: 58px;

                margin: 0;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);

                background:
                    linear-gradient(
                        180deg,
                        #f7efe4 0%,
                        #f0e3d2 100%
                    );

                padding:
                    9px 12px;
            }

            .inventory-v23
            .inventory-v14-group-head h3 {
                font-size: 19px;
            }

            .inventory-v23
            .inventory-v14-group-head p {
                margin-top: 2px;

                font-size: 10px;

                color: #92745c;
            }

            .inventory-v23
            .inventory-v14-group-count {
                min-width: 27px;
                height: 25px;

                border-color:
                    rgba(176,140,98,.34);

                background: #fffdf8;

                font-size: 11px;
            }

            .inventory-v23
            .inventory-v23-group-panel
            .inventory-v16-items-grid {
                gap: 10px;

                padding:
                    11px;
            }

            /*
            |--------------------------------------------------------------------------
            | POLAROIDS — MESMA APARÊNCIA, LEITURA MAIS LIMPA
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            .inventory-v16-polaroid {
                border-color:
                    rgba(176,140,98,.42);

                background:
                    linear-gradient(
                        180deg,
                        #fffdf9 0%,
                        #faf6ee 100%
                    );
            }

            .inventory-v23
            .inventory-v16-polaroid:hover,
            .inventory-v23
            .inventory-v16-polaroid:focus-within {
                border-color:
                    rgba(140,98,57,.54);

                background:
                    #fffdf9;

                box-shadow:
                    0 8px 20px
                    rgba(83,21,15,.055);
            }

            .inventory-v23-card-mechanics {
                color: #7d604d !important;
            }

            .inventory-v23
            .inventory-v22-card-feature-strip {
                background:
                    linear-gradient(
                        180deg,
                        #f4efe5 0%,
                        #f0e6d8 100%
                    );
            }

            .inventory-v23
            .inventory-v22-card-feature-row {
                background:
                    rgba(255,253,249,.82);
            }

            /*
            |--------------------------------------------------------------------------
            | EMPTY STATE
            |--------------------------------------------------------------------------
            */

            .inventory-v23
            > .py-16 {
                margin-top: 12px;

                border:
                    1px dashed
                    rgba(176,140,98,.48);

                border-radius: 13px;

                background:
                    rgba(251,248,241,.70);
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 760px) {
                .inventory-v23
                .inventory-v23-main-topbar {
                    align-items: flex-start;
                }

                .inventory-v23
                .inventory-v14-toolbar-right {
                    align-self: flex-start;
                }

                .inventory-v23
                .inventory-v14-slots {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 520px) {
                .inventory-v23
                .inventory-v23-main-topbar {
                    border-radius: 11px;

                    padding:
                        8px;
                }

                .inventory-v23
                .inventory-v14-primary,
                .inventory-v23
                .inventory-v14-action {
                    padding:
                        0 10px;
                }

                .inventory-v23
                .inventory-v14-wallet-trigger {
                    min-width: 112px;
                }

                .inventory-v23
                .inventory-v23-group-panel
                .inventory-v16-items-grid {
                    padding:
                        9px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V24 — CATEGORIAS EM GAVETAS
            |--------------------------------------------------------------------------
            |
            | O cabeçalho permanece sempre visível. Clique nele para esconder
            | ou revelar os polaroids. O estado fica salvo para o personagem.
            |
            */

            .inventory-v24
            .inventory-v24-group-drawer {
                transition:
                    border-color .14s ease,
                    background .14s ease;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-collapsed {
                background:
                    rgba(247,240,230,.72);
            }

            /*
            |--------------------------------------------------------------------------
            | CABEÇALHO CLICÁVEL
            |--------------------------------------------------------------------------
            */

            .inventory-v24
            .inventory-v23-group-panel
            .inventory-v24-group-trigger {
                display: flex;

                width: 100%;

                align-items: center;

                margin: 0;

                border-bottom: 0;

                text-align: left;

                cursor: pointer;

                transition:
                    background .14s ease;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-open
            .inventory-v24-group-trigger {
                border-bottom:
                    1px solid
                    rgba(188,154,111,.28);
            }

            .inventory-v24
            .inventory-v24-group-trigger:hover {
                background:
                    linear-gradient(
                        180deg,
                        #f3e7d8 0%,
                        #ead9c5 100%
                    ) !important;
            }

            .inventory-v24
            .inventory-v24-group-trigger:focus-visible {
                outline:
                    2px solid
                    rgba(140,98,57,.30);

                outline-offset:
                    -2px;
            }

            .inventory-v24-group-copy {
                min-width: 0;

                flex: 1;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTADOR + CHEVRON
            |--------------------------------------------------------------------------
            */

            .inventory-v24-group-meta {
                display: flex;

                flex: 0 0 auto;

                align-items: center;

                gap: 8px;
            }

            .inventory-v24
            .inventory-v24-group-trigger
            .inventory-v14-group-count {
                margin-left: 0;
            }

            .inventory-v24-group-chevron {
                display: flex;

                width: 29px;
                height: 29px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 8px;

                background:
                    rgba(255,253,248,.68);

                color: #8c6239;

                transition:
                    border-color .14s ease,
                    background .14s ease,
                    color .14s ease;
            }

            .inventory-v24-group-chevron svg {
                width: 15px;
                height: 15px;

                transition:
                    transform .16s ease;
            }

            .inventory-v24-group-chevron.is-open svg {
                transform:
                    rotate(180deg);
            }

            .inventory-v24
            .inventory-v24-group-trigger:hover
            .inventory-v24-group-chevron {
                border-color:
                    rgba(140,98,57,.48);

                background: #fffdf8;

                color: #53150f;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTEÚDO RECOLHÍVEL
            |--------------------------------------------------------------------------
            */

            .inventory-v24-group-content {
                overflow: hidden;

                transform-origin: top;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-collapsed
            .inventory-v24-group-trigger {
                min-height: 54px;
            }

            .inventory-v24
            .inventory-v24-group-drawer.is-collapsed
            .inventory-v24-group-copy p {
                opacity: .76;
            }

            @media (max-width: 520px) {
                .inventory-v24-group-meta {
                    gap: 6px;
                }

                .inventory-v24-group-chevron {
                    width: 27px;
                    height: 27px;
                }
            }

        

            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V26 — FILTROS POR CATEGORIA + CARDS COMPACTOS
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | FERRAMENTAS DENTRO DA GAVETA
            |--------------------------------------------------------------------------
            */

            .inventory-v26-group-tools {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    182px;

                gap: 7px;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.22);

                background:
                    rgba(247,240,230,.52);

                padding:
                    7px 10px;
            }

            .inventory-v26-group-search {
                position: relative;

                display: flex;

                min-width: 0;

                align-items: center;
            }

            .inventory-v26-group-search-icon {
                position: absolute;
                left: 9px;

                display: flex;

                width: 14px;
                height: 14px;

                align-items: center;
                justify-content: center;

                color:
                    rgba(140,98,57,.68);

                pointer-events: none;
            }

            .inventory-v26-group-search-icon svg {
                width: 14px;
                height: 14px;
            }

            .inventory-v26-group-search-input {
                width: 100%;
                min-height: 32px;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 8px;

                background: #fffdf8;

                padding:
                    0 30px 0 31px;

                font-size: 10px;
                font-weight: 600;

                color: #53150f;

                outline: none;
            }

            .inventory-v26-group-search-input::placeholder {
                color:
                    rgba(140,98,57,.54);
            }

            .inventory-v26-group-search-input:focus {
                border-color:
                    rgba(107,29,20,.32);

                box-shadow:
                    0 0 0 2px
                    rgba(107,29,20,.04);
            }

            .inventory-v26-group-search-clear {
                position: absolute;
                right: 6px;

                display: flex;

                width: 21px;
                height: 21px;

                align-items: center;
                justify-content: center;

                border-radius: 6px;

                font-size: 13px;

                color: #8c6239;
            }

            .inventory-v26-group-search-clear:hover {
                background: #efe9dc;

                color: #53150f;
            }

            .inventory-v26-group-sort {
                display: grid;

                grid-template-columns:
                    auto
                    minmax(0, 1fr);

                align-items: center;

                gap: 6px;
            }

            .inventory-v26-group-sort > span {
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .065em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v26-group-sort select {
                width: 100%;
                min-height: 32px;

                border:
                    1px solid
                    rgba(176,140,98,.36);

                border-radius: 8px;

                background: #fffdf8;

                padding:
                    0 7px;

                font-size: 9px;
                font-weight: 800;

                color: #6b1d14;

                outline: none;
            }

            /*
            |--------------------------------------------------------------------------
            | SEM RESULTADOS NA CATEGORIA
            |--------------------------------------------------------------------------
            */

            .inventory-v26-group-empty {
                display: flex;

                min-height: 92px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                gap: 3px;

                padding:
                    14px;

                text-align: center;

                color:
                    rgba(140,98,57,.56);
            }

            .inventory-v26-group-empty > span {
                font-family: Georgia, serif;
                font-size: 20px;
            }

            .inventory-v26-group-empty strong {
                font-family: Georgia, serif;
                font-size: 12px;

                color: #53150f;
            }

            .inventory-v26-group-empty button {
                margin-top: 4px;

                font-size: 8px;
                font-weight: 900;

                color: #8c6239;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V39 — CARDS MAIORES E MAIS LEGÍVEIS
            |--------------------------------------------------------------------------
            |
            | A V26 tinha compactado demais os cards do inventário.
            | Aqui voltamos a uma leitura confortável e com mais cara de módulo
            | da ficha: imagem grande, título forte, subtítulo legível e área
            | de habilidades menos apertada.
            |
            */


            /* ==============================================================
               GRID
            ============================================================== */

            .inventory-v39
            .inventory-v39-items-grid {
                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(220px, 1fr)
                    ) !important;

                gap:
                    14px !important;

                padding:
                    12px !important;
            }

            @media (max-width: 760px) {
                .inventory-v39
                .inventory-v39-items-grid {
                    grid-template-columns:
                        repeat(
                            auto-fill,
                            minmax(200px, 1fr)
                        ) !important;
                }
            }

            @media (max-width: 540px) {
                .inventory-v39
                .inventory-v39-items-grid {
                    grid-template-columns:
                        1fr !important;
                }
            }


            /* ==============================================================
               CARD BASE
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card {
                display:
                    flex;

                min-height:
                    338px;

                flex-direction:
                    column;

                overflow:
                    hidden;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    18px;

                background:
                    var(--theme-surface-card) !important;

                box-shadow:
                    0 4px 12px rgba(0,0,0,.04) !important;

                transition:
                    transform .16s ease,
                    border-color .16s ease,
                    box-shadow .16s ease,
                    background .16s ease;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card::before {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-border-strong) 85%,
                        white
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card.magical::before {
                background:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card:hover,
            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card:focus-within {
                transform:
                    translateY(-2px) !important;

                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 10px 24px rgba(0,0,0,.07) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-main {
                display:
                    flex !important;

                min-height:
                    0;

                flex:
                    1;

                flex-direction:
                    column;

                gap:
                    12px;

                padding:
                    12px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-thumb {
                width:
                    100% !important;

                height:
                    152px !important;

                border:
                    1px solid
                    var(--theme-border-soft) !important;

                border-radius:
                    14px !important;

                background:
                    linear-gradient(
                        180deg,
                        var(--theme-surface-inset) 0%,
                        color-mix(
                            in srgb,
                            var(--theme-surface-inset) 88%,
                            var(--theme-band-background)
                        ) 100%
                    ) !important;

                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.45);
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-thumb
            img {
                width:
                    100%;

                height:
                    100%;

                object-fit:
                    contain;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-thumb
            .inventory-v27-equipped-badge {
                top:
                    10px !important;

                right:
                    10px !important;

                min-height:
                    24px;

                padding:
                    0 9px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 16%,
                        var(--theme-border)
                    ) !important;

                border-radius:
                    999px;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-surface-raised) 85%,
                        white
                    ) !important;

                font-size:
                    8px;

                letter-spacing:
                    .08em;

                text-transform:
                    uppercase;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-copy {
                display:
                    flex;

                flex:
                    1;

                flex-direction:
                    column;

                gap:
                    7px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-copy
            h4 {
                overflow:
                    hidden;

                display:
                    -webkit-box;

                min-height:
                    44px;

                -webkit-line-clamp:
                    2;

                -webkit-box-orient:
                    vertical;

                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    17px !important;

                font-weight:
                    900;

                line-height:
                    1.18 !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-copy
            em {
                overflow:
                    hidden;

                display:
                    -webkit-box;

                min-height:
                    30px;

                -webkit-line-clamp:
                    2;

                -webkit-box-orient:
                    vertical;

                margin-top:
                    0 !important;

                font-size:
                    12px !important;

                font-weight:
                    600;

                line-height:
                    1.25 !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-card-main
            > .hidden.shrink-0.self-center.text-\[\#8c6239\]\/35 {
                display:
                    none !important;
            }


            /* ==============================================================
               FEATURE STRIP
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-feature-strip {
                min-height:
                    106px;

                margin-top:
                    auto;

                border-top:
                    1px solid
                    var(--theme-divider) !important;

                background:
                    var(--theme-surface-card-alt) !important;

                padding:
                    9px 10px 10px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-head {
                min-height:
                    22px;

                margin-bottom:
                    7px;

                font-size:
                    8.5px !important;

                letter-spacing:
                    .12em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-count {
                min-width:
                    20px;

                height:
                    20px;

                font-size:
                    8px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-list {
                gap:
                    6px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row {
                min-height:
                    29px;

                gap:
                    8px;

                border:
                    1px solid
                    var(--theme-border-soft) !important;

                border-radius:
                    10px;

                background:
                    var(--theme-surface-card) !important;

                padding:
                    4px 6px 4px 8px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-name {
                font-size:
                    11px !important;

                line-height:
                    1.15 !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-passive {
                min-height:
                    19px;

                padding:
                    0 7px;

                border:
                    1px solid
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                font-size:
                    7px !important;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width:
                    20px;

                height:
                    20px;

                font-size:
                    11px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width:
                    34px;

                font-size:
                    9px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-recovery {
                max-width:
                    none;

                font-size:
                    8px;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-more {
                min-height:
                    18px;

                margin-top:
                    2px;

                font-size:
                    8px !important;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v22-card-feature-more
            span:last-child {
                color:
                    var(--theme-accent-strong) !important;
            }


            /* ==============================================================
               FALLBACK — SEM HABILIDADES
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v39
            .inventory-v39-item-card
            .inventory-v39-feature-strip:empty {
                display:
                    none;
            }

            /*
            |--------------------------------------------------------------------------
            | POLAROIDS MAIS COMPACTOS
            |--------------------------------------------------------------------------
            |
            | Mantém a mesma aparência, apenas reduz densidade e redundância.
            |
            */

            .inventory-v26
            .inventory-v26-items-grid {
                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(168px, 1fr)
                    ) !important;

                gap: 9px !important;

                padding:
                    9px !important;
            }

            .inventory-v26
            .inventory-v16-polaroid {
                min-height: 0;
            }

            /*
            | Foto menor: era 132px.
            */
            .inventory-v26
            .inventory-v16-polaroid
            > div:first-child {
                padding:
                    7px 7px 6px 10px !important;
            }

            .inventory-v26
            .inventory-v16-polaroid
            > div:first-child
            > .inventory-v10-thumb {
                height: 104px !important;

                border-radius: 7px !important;
            }

            .inventory-v26
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                margin-top: 7px;
            }

            /*
            | Título continua claramente legível.
            */
            .inventory-v26
            .inventory-v16-polaroid
            h4 {
                font-size: 14px !important;
                line-height: 1.12 !important;
            }

            /*
            | A categoria já diz "Itens Maravilhosos/Mundanos".
            | No card mostramos apenas raridade + sintonizável.
            */
            .inventory-v26
            .inventory-v16-polaroid
            em {
                margin-top: 2px;

                font-size: 9px !important;
                line-height: 1.25 !important;

                -webkit-line-clamp: 1;
            }

            /*
            | Mecânica continua útil, mas compacta.
            */
            .inventory-v26
            .inventory-v23-card-mechanics {
                margin-top: 5px !important;

                font-size: 9.5px !important;
                line-height: 1.3 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | HABILIDADES MAIS BAIXAS
            |--------------------------------------------------------------------------
            */

            .inventory-v26
            .inventory-v22-card-feature-strip {
                min-height: 68px;

                padding:
                    5px 6px 6px 9px !important;
            }

            .inventory-v26
            .inventory-v22-card-feature-head {
                min-height: 14px;

                margin-bottom: 3px;

                font-size: 6.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-count {
                min-width: 16px;
                height: 16px;

                font-size: 6.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-list {
                gap: 3px;
            }

            .inventory-v26
            .inventory-v22-card-feature-row {
                min-height: 23px;

                gap: 4px;

                padding:
                    1px 3px 1px 6px;
            }

            .inventory-v26
            .inventory-v22-card-feature-name {
                font-size: 8.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-passive {
                min-height: 17px;

                padding:
                    0 5px;

                font-size: 6px;
            }

            .inventory-v26
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width: 17px;
                height: 17px;

                font-size: 9px;
            }

            .inventory-v26
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width: 27px;

                font-size: 8px;
            }

            .inventory-v26
            .inventory-v22-card-feature-recovery {
                max-width: 24px;

                font-size: 6.5px;
            }

            .inventory-v26
            .inventory-v22-card-feature-empty {
                min-height: 36px;

                font-size: 7px;
            }

            .inventory-v26
            .inventory-v22-card-feature-more {
                min-height: 15px;

                font-size: 6.5px;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 660px) {
                .inventory-v26-group-tools {
                    grid-template-columns: 1fr;
                }

                .inventory-v26-group-sort {
                    grid-template-columns:
                        50px
                        minmax(0, 1fr);
                }
            }

            @media (max-width: 520px) {
                .inventory-v26
                .inventory-v26-items-grid {
                    grid-template-columns:
                        repeat(
                            2,
                            minmax(0, 1fr)
                        ) !important;
                }

                .inventory-v26
                .inventory-v16-polaroid
                > div:first-child
                > .inventory-v10-thumb {
                    height: 112px !important;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V27 — CARD SEM FAIXA + SELO EQUIPADO NA FOTO
            |--------------------------------------------------------------------------
            */

            .inventory-v27 .inventory-v11-item-card::before,
            .inventory-v27 .inventory-v16-polaroid::before {
                content: none !important;
                display: none !important;
            }

            .inventory-v27 .inventory-v16-polaroid > div:first-child {
                padding-left: 9px !important;
            }

            .inventory-v27 .inventory-v27-thumb {
                position: relative;
                overflow: hidden;
            }

            .inventory-v27 .inventory-v27-equipped-badge {
                position: absolute;
                top: 7px;
                right: 7px;
                z-index: 2;

                display: inline-flex;
                align-items: center;
                gap: 4px;

                min-height: 18px;
                padding: 0 7px;

                border: 1px solid rgba(205,187,159,.72);
                border-radius: 999px;

                background: rgba(255,248,242,.92);
                box-shadow: 0 1px 4px rgba(83,21,15,.08);

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .055em;
                text-transform: uppercase;
                color: #6b1d14;
                backdrop-filter: blur(2px);
            }

            .inventory-v27 .inventory-v27-equipped-badge::before {
                content: '';
                width: 5px;
                height: 5px;
                border-radius: 999px;
                background: #6b1d14;
                opacity: .9;
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V28 — CONTRASTE DOS CARDS + SCROLLBAR OCULTA
            |--------------------------------------------------------------------------
            */

            .inventory-v28 .inventory-v23-group-panel {
                border-color: rgba(176,140,98,.46);
                background: linear-gradient(180deg, #f4ebde 0%, #efe3d2 100%);
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.78),
                    0 1px 0 rgba(140,98,57,.04);
            }

            .inventory-v28 .inventory-v23 .inventory-v14-group-head,
            .inventory-v28 .inventory-v14-group-head {
                border-bottom-color: rgba(176,140,98,.32);
                background: linear-gradient(180deg, #f2e4d3 0%, #ebdac5 100%);
            }

            .inventory-v28 .inventory-v26-group-tools {
                background: rgba(255,252,247,.48);
                border-bottom: 1px solid rgba(188,154,111,.22);
            }

            .inventory-v28 .inventory-v26-group-search-input,
            .inventory-v28 .inventory-v26-group-sort select {
                background: rgba(255,255,255,.90);
                border-color: rgba(188,154,111,.52);
                color: #5c2018;
                box-shadow: 0 1px 0 rgba(255,255,255,.75) inset;
            }

            .inventory-v28 .inventory-v26-group-search-input::placeholder {
                color: rgba(125,96,77,.78);
            }

            .inventory-v28 .inventory-v16-polaroid {
                border: 1px solid rgba(176,140,98,.58) !important;
                background: linear-gradient(180deg, #fffefa 0%, #fcf8f1 100%) !important;
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.94),
                    0 8px 18px rgba(83,21,15,.035);
            }

            .inventory-v28 .inventory-v16-polaroid:hover,
            .inventory-v28 .inventory-v16-polaroid:focus-within {
                border-color: rgba(140,98,57,.62) !important;
                background: linear-gradient(180deg, #fffefd 0%, #fdf9f2 100%) !important;
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.96),
                    0 10px 24px rgba(83,21,15,.06);
            }

            .inventory-v28 .inventory-v10-thumb,
            .inventory-v28 .inventory-v27-thumb {
                background: linear-gradient(180deg, #fffdfa 0%, #f5ede1 100%) !important;
                border-color: rgba(188,154,111,.72) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.92);
            }

            .inventory-v28 .inventory-v23-card-mechanics {
                color: #8a6542 !important;
            }

            .inventory-v28 .inventory-v22-card-feature-strip {
                border-top: 1px solid rgba(188,154,111,.32);
                background: linear-gradient(180deg, #f5eee3 0%, #f0e4d4 100%) !important;
            }

            .inventory-v28 .inventory-v22-card-feature-row {
                background: rgba(255,251,246,.94);
                border-color: rgba(188,154,111,.34);
            }

            .inventory-v28 .inventory-v22-card-feature-empty,
            .inventory-v28 .inventory-v22-card-feature-more {
                color: #8a6542;
            }

            .inventory-v28 .inventory-v22-card-feature-passive {
                background: rgba(250,242,234,.95);
                border-color: rgba(205,187,159,.72);
            }

            .inventory-v28 .inventory-v22-card-feature-recovery {
                color: #8a6542;
            }

            .inventory-v28 .inventory-v27-equipped-badge {
                border-color: rgba(188,154,111,.72);
                background: rgba(255,250,244,.95);
                color: #6b1d14;
                box-shadow: 0 1px 6px rgba(83,21,15,.08);
            }

            .character-drawer > .min-h-0.flex-1.overflow-y-auto {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .character-drawer > .min-h-0.flex-1.overflow-y-auto::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v28 .overflow-y-auto,
            .inventory-v28 .inventory-v17-detail-scroll,
            .inventory-v28 .inventory-v16-equipped-body {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v28 .overflow-y-auto::-webkit-scrollbar,
            .inventory-v28 .inventory-v17-detail-scroll::-webkit-scrollbar,
            .inventory-v28 .inventory-v16-equipped-body::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V29 — ITENS EQUIPADOS COMO LISTA SIMPLES
            |--------------------------------------------------------------------------
            */

            .inventory-v29-equipped-modal {
                position: relative;
                z-index: 10;

                width: min(520px, 94vw);
                max-height: min(72vh, 620px);

                overflow: hidden;

                border: 1px solid rgba(176,140,98,.58);
                border-radius: 14px;

                background:
                    linear-gradient(
                        180deg,
                        #fbf8f1 0%,
                        #f7f0e6 100%
                    );

                box-shadow:
                    0 28px 80px rgba(43,29,23,.24),
                    inset 0 1px 0 rgba(255,255,255,.86);
            }

            .inventory-v29-equipped-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;

                border-bottom: 1px solid rgba(188,154,111,.30);

                background:
                    linear-gradient(
                        180deg,
                        #f2e6d7 0%,
                        #eadbc8 100%
                    );

                padding: 13px 15px 12px;
            }

            .inventory-v29-equipped-kicker {
                display: block;

                margin-bottom: 3px;

                font-size: 7.5px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v29-equipped-title-row {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .inventory-v29-equipped-title-row h3 {
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: 900;
                line-height: 1.05;

                color: #53150f;
            }

            .inventory-v29-equipped-count {
                display: inline-flex;
                min-width: 24px;
                height: 24px;
                align-items: center;
                justify-content: center;

                border: 1px solid rgba(176,140,98,.32);
                border-radius: 999px;

                background: rgba(255,253,248,.78);

                padding: 0 6px;

                font-family: Georgia, serif;
                font-size: 11px;
                font-weight: 900;

                color: #6b1d14;
            }

            .inventory-v29-equipped-hint {
                flex: 0 0 auto;
                margin-top: 5px;

                font-size: 7.5px;
                font-weight: 700;

                color: rgba(140,98,57,.66);
            }

            .inventory-v29-equipped-list-wrap {
                max-height: calc(min(72vh, 620px) - 66px);
                overflow-y: auto;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v29-equipped-list-wrap::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v29-equipped-list {
                display: flex;
                flex-direction: column;

                padding: 7px;
            }

            .inventory-v29-equipped-row {
                display: grid;
                grid-template-columns: 48px minmax(0, 1fr) 24px;
                align-items: center;
                gap: 10px;

                width: 100%;
                min-height: 60px;

                border: 1px solid transparent;
                border-radius: 9px;

                padding: 6px 7px;

                text-align: left;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    transform .12s ease;
            }

            .inventory-v29-equipped-row + .inventory-v29-equipped-row {
                margin-top: 2px;
            }

            .inventory-v29-equipped-row:hover,
            .inventory-v29-equipped-row:focus-visible {
                border-color: rgba(176,140,98,.34);
                background: #fffdf8;
                outline: none;
            }

            .inventory-v29-equipped-thumb {
                display: flex;
                width: 48px;
                height: 48px;
                align-items: center;
                justify-content: center;

                overflow: hidden;

                border: 1px solid rgba(188,154,111,.52);
                border-radius: 8px;

                background: #f3ece1;
            }

            .inventory-v29-equipped-thumb img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                padding: 2px;
            }

            .inventory-v29-equipped-thumb > span {
                font-family: Georgia, serif;
                font-size: 17px;

                color: rgba(140,98,57,.30);
            }

            .inventory-v29-equipped-copy {
                min-width: 0;
            }

            .inventory-v29-equipped-copy strong {
                display: block;
                overflow: hidden;

                font-family: Georgia, serif;
                font-size: 13.5px;
                font-weight: 900;
                line-height: 1.15;

                color: #53150f;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v29-equipped-copy span {
                display: block;
                overflow: hidden;

                margin-top: 3px;

                font-size: 9px;
                font-style: italic;
                font-weight: 600;

                color: #7d604d;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v29-equipped-chevron {
                display: flex;
                width: 24px;
                height: 24px;
                align-items: center;
                justify-content: center;

                color: rgba(140,98,57,.48);

                transition: color .12s ease, transform .12s ease;
            }

            .inventory-v29-equipped-chevron svg {
                width: 14px;
                height: 14px;
            }

            .inventory-v29-equipped-row:hover
            .inventory-v29-equipped-chevron {
                color: #6b1d14;
                transform: translateX(1px);
            }

            .inventory-v29-equipped-empty {
                display: flex;
                min-height: 160px;
                flex-direction: column;
                align-items: center;
                justify-content: center;

                padding: 24px;
                text-align: center;
            }

            .inventory-v29-equipped-empty > span {
                margin-bottom: 4px;

                font-family: Georgia, serif;
                font-size: 24px;

                color: rgba(140,98,57,.36);
            }

            .inventory-v29-equipped-empty strong {
                font-family: Georgia, serif;
                font-size: 15px;

                color: #53150f;
            }

            .inventory-v29-equipped-empty p {
                margin-top: 3px;

                font-size: 9.5px;

                color: #8c6239;
            }

            @media (max-width: 520px) {
                .inventory-v29-equipped-header {
                    padding: 12px;
                }

                .inventory-v29-equipped-hint {
                    display: none;
                }

                .inventory-v29-equipped-row {
                    grid-template-columns: 44px minmax(0,1fr) 20px;
                    min-height: 56px;
                }

                .inventory-v29-equipped-thumb {
                    width: 44px;
                    height: 44px;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V30 — CARDS MAIS LIMPOS + TRACKERS MENORES
            |--------------------------------------------------------------------------
            |
            | A imagem já comunica o tipo do item. Portanto, os polaroids não
            | repetem mais "Arma Marcial", "Escudo", "Armadura" etc.
            |
            | Os rastreadores continuam funcionais, mas ocupam menos espaço.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | CORPO DO POLAROID
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v16-polaroid
            > div:first-child
            > .min-w-0.flex-1 {
                margin-top: 6px;
            }

            .inventory-v30
            .inventory-v16-polaroid
            h4 {
                margin-bottom: 1px;
            }

            .inventory-v30
            .inventory-v16-polaroid
            em {
                margin-top: 1px !important;
            }

            /*
            |--------------------------------------------------------------------------
            | ÁREA DE HABILIDADES
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v22-card-feature-strip {
                min-height: 58px;

                padding:
                    4px 5px 5px 7px !important;
            }

            .inventory-v30
            .inventory-v22-card-feature-head {
                min-height: 12px;

                margin-bottom: 2px;

                font-size: 6px;
            }

            .inventory-v30
            .inventory-v22-card-feature-count {
                min-width: 15px;
                height: 15px;

                padding:
                    0 4px;

                font-size: 6px;
            }

            .inventory-v30
            .inventory-v22-card-feature-list {
                gap: 2px;
            }

            .inventory-v30
            .inventory-v22-card-feature-row {
                min-height: 20px;

                gap: 3px;

                border-radius: 6px;

                padding:
                    1px 2px 1px 5px;
            }

            .inventory-v30
            .inventory-v22-card-feature-name {
                font-size: 8px;
                line-height: 1.1;
            }

            /*
            |--------------------------------------------------------------------------
            | TRACKER
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v22-card-feature-tracker {
                gap: 2px;
            }

            .inventory-v30
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker {
                border-radius: 6px;
            }

            .inventory-v30
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker button {
                width: 15px;
                height: 15px;

                font-size: 8px;
                line-height: 1;
            }

            .inventory-v30
            .inventory-v22-card-feature-row
            .inventory-v11-mini-tracker strong {
                min-width: 23px;

                padding:
                    0 2px;

                font-size: 7.5px;
                line-height: 1;
            }

            .inventory-v30
            .inventory-v22-card-feature-recovery {
                max-width: 22px;

                font-size: 6px;
                line-height: 1;

                color: #8a6542;
            }

            /*
            |--------------------------------------------------------------------------
            | PASSIVAS
            |--------------------------------------------------------------------------
            */

            .inventory-v30
            .inventory-v22-card-feature-passive {
                min-height: 15px;

                padding:
                    0 4px;

                font-size: 5.5px;
                letter-spacing: .035em;
            }

            .inventory-v30
            .inventory-v22-card-feature-empty {
                min-height: 30px;

                font-size: 6.5px;
            }

            .inventory-v30
            .inventory-v22-card-feature-more {
                min-height: 13px;

                font-size: 6px;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSIVO
            |--------------------------------------------------------------------------
            */

            @media (max-width: 520px) {
                .inventory-v30
                .inventory-v22-card-feature-name {
                    font-size: 8.5px;
                }

                .inventory-v30
                .inventory-v22-card-feature-row
                .inventory-v11-mini-tracker button {
                    width: 16px;
                    height: 16px;
                }

                .inventory-v30
                .inventory-v22-card-feature-row
                .inventory-v11-mini-tracker strong {
                    min-width: 24px;

                    font-size: 8px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V31 — CRIAÇÃO / TOOLTIPS / TRACKERS
            |--------------------------------------------------------------------------
            */

            /*
            | O módulo Arma não pode cortar tooltips das propriedades.
            */
            .inventory-v31
            .inventory-v31-weapon-module {
                overflow: visible !important;
            }

            .inventory-v31
            .inventory-v31-weapon-module
            .inventory-v10-tooltip-panel {
                z-index: 180;
            }

            /*
            | Escolha do comportamento das cargas.
            */
            .inventory-v31-counter-choice {
                display: flex;

                min-height: 52px;

                flex-direction: column;

                align-items: flex-start;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.72);

                border-radius: 9px;

                background:
                    rgba(255,253,249,.72);

                padding:
                    7px 9px;

                text-align: left;

                transition:
                    border-color .12s ease,
                    background .12s ease,
                    color .12s ease;
            }

            .inventory-v31-counter-choice strong {
                font-size: 10px;

                color: #53150f;
            }

            .inventory-v31-counter-choice span {
                margin-top: 2px;

                font-size: 8.5px;
                line-height: 1.3;

                color: #8c6239;
            }

            .inventory-v31-counter-choice:hover {
                background: #f7f0e6;
            }

            .inventory-v31-counter-choice.active {
                border-color:
                    rgba(107,29,20,.42);

                background:
                    rgba(107,29,20,.065);
            }

            .inventory-v31-counter-choice.active strong {
                color: #6b1d14;
            }

            @media (max-width: 520px) {
                .inventory-v31-counter-choice {
                    min-height: 48px;
                }
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V32 — TOOLTIP FLUTUANTE + SCROLL SEM BARRAS
            |--------------------------------------------------------------------------
            */

            /*
            | Continua rolando com mouse, trackpad, teclado e touch.
            | Apenas a barra visual é escondida.
            */
            .inventory-v32-editor-scroll {
                overflow-x: hidden !important;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v32-editor-scroll::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v32-editor-scroll > *,
            .inventory-v32-editor-scroll section,
            .inventory-v32-editor-scroll .inventory-v10-module {
                max-width: 100%;
                min-width: 0;
            }

            /*
            | Tooltip fora do fluxo do editor.
            | Não pode mais criar scrollbar horizontal nem ser cortado.
            */
            .inventory-v32-floating-tooltip {
                position: fixed;

                z-index: 9999;

                width:
                    min(
                        390px,
                        calc(100vw - 24px)
                    );

                max-height:
                    calc(100vh - 24px);

                overflow-y: auto;
                overflow-x: hidden;

                border:
                    1px solid
                    rgba(176,140,98,.72);

                border-radius: 10px;

                background: #fbf8f1;

                padding:
                    10px 11px;

                color: #5f3a27;

                box-shadow:
                    0 14px 32px
                    rgba(43,29,23,.18);

                pointer-events: none;

                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .inventory-v32-floating-tooltip::-webkit-scrollbar {
                width: 0;
                height: 0;
                display: none;
            }

            .inventory-v32-floating-tooltip strong {
                display: block;

                margin-bottom: 4px;

                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v32-floating-tooltip p {
                margin: 0;

                font-size: 11px;
                font-weight: 600;
                line-height: 1.52;

                color: #6b5548;
            }

            /*
            | O módulo pode voltar a controlar o próprio acabamento.
            | O tooltip não está mais dentro dele.
            */
            .inventory-v32
            .inventory-v31-weapon-module {
                overflow: hidden !important;
            }

        
            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V34 — CRIADOR DE ARMADURAS E ESCUDOS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-defense-module {
                border-color:
                    rgba(176,140,98,.46) !important;

                background:
                    linear-gradient(
                        180deg,
                        #fffdf9 0%,
                        #f8f2e8 100%
                    ) !important;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.88);
            }

            .inventory-v34-defense-head {
                display: flex;

                align-items: flex-start;

                gap: 11px;

                border-bottom:
                    1px solid
                    rgba(188,154,111,.30);

                background:
                    linear-gradient(
                        180deg,
                        #f2e5d5 0%,
                        #ead9c5 100%
                    );

                padding:
                    12px 14px;
            }

            .inventory-v34-defense-head-icon {
                display: flex;

                width: 36px;
                height: 36px;

                flex: 0 0 36px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.48);

                border-radius: 10px;

                background:
                    rgba(255,253,249,.80);

                color: #6b1d14;
            }

            .inventory-v34-defense-head-icon svg {
                width: 19px;
                height: 19px;
            }

            .inventory-v34-auto-equip {
                display: inline-flex;

                min-height: 20px;

                align-items: center;

                border:
                    1px solid
                    rgba(107,29,20,.15);

                border-radius: 999px;

                background:
                    rgba(107,29,20,.055);

                padding:
                    0 7px;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .06em;

                text-transform: uppercase;

                color: #6b1d14;
            }

            .inventory-v34-remove {
                flex: 0 0 auto;

                border-radius: 7px;

                padding:
                    5px 7px;

                font-size: 8px;
                font-weight: 900;
                letter-spacing: .04em;

                text-transform: uppercase;

                color: #a03d32;

                transition:
                    background .12s ease,
                    color .12s ease;
            }

            .inventory-v34-remove:hover {
                background:
                    rgba(160,61,50,.07);

                color: #7c211a;
            }

            /*
            |--------------------------------------------------------------------------
            | SEÇÕES
            |--------------------------------------------------------------------------
            */

            .inventory-v34-section-label {
                display: flex;

                align-items: flex-start;

                gap: 9px;
            }

            .inventory-v34-section-label > span {
                display: flex;

                width: 26px;
                height: 26px;

                flex: 0 0 26px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 8px;

                background: #fffdf8;

                font-family: Georgia, serif;
                font-size: 9px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-section-label strong {
                display: block;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.2;

                color: #53150f;
            }

            .inventory-v34-section-label p {
                margin-top: 2px;

                font-size: 9.5px;
                line-height: 1.4;

                color: #8c6239;
            }

            .inventory-v34-rule-panel {
                border-top:
                    1px solid
                    rgba(188,154,111,.24);

                padding-top: 15px;
            }

            .inventory-v34-field-label {
                display: block;

                font-size: 9px;
                font-weight: 900;
                letter-spacing: .045em;

                text-transform: uppercase;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | CATEGORIAS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-armor-category {
                display: flex;

                min-height: 82px;

                flex-direction: column;

                align-items: flex-start;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.68);

                border-radius: 10px;

                background: #fffdf8;

                padding:
                    9px 10px;

                text-align: left;

                transition:
                    border-color .14s ease,
                    background .14s ease,
                    box-shadow .14s ease;
            }

            .inventory-v34-armor-category:hover {
                border-color:
                    rgba(140,98,57,.50);

                background: #f8f2e8;
            }

            .inventory-v34-armor-category.active {
                border-color:
                    rgba(107,29,20,.42);

                background:
                    linear-gradient(
                        180deg,
                        rgba(107,29,20,.075),
                        rgba(107,29,20,.035)
                    );

                box-shadow:
                    inset 0 0 0 1px
                    rgba(107,29,20,.045);
            }

            .inventory-v34-category-mark {
                display: flex;

                width: 23px;
                height: 23px;

                align-items: center;
                justify-content: center;

                margin-bottom: 5px;

                border:
                    1px solid
                    rgba(176,140,98,.38);

                border-radius: 7px;

                background: #f7f0e6;

                font-family: Georgia, serif;
                font-size: 9px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-armor-category.active
            .inventory-v34-category-mark {
                border-color:
                    rgba(107,29,20,.26);

                background:
                    rgba(107,29,20,.08);

                color: #6b1d14;
            }

            .inventory-v34-armor-category strong {
                font-family: Georgia, serif;
                font-size: 12px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v34-armor-category small {
                margin-top: 2px;

                font-size: 8px;
                font-weight: 700;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | CAMPOS NUMÉRICOS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-number-field {
                display: flex;

                min-height: 44px;

                align-items: center;

                border:
                    1px solid
                    rgba(205,187,159,.86);

                border-radius: 10px;

                background:
                    rgba(255,255,255,.82);

                overflow: hidden;
            }

            .inventory-v34-number-field > span {
                padding-left: 10px;

                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-number-field input {
                width: 100%;
                min-width: 0;
                height: 42px;

                border: 0;

                background: transparent;

                padding:
                    0 8px;

                text-align: center;

                font-family: Georgia, serif;
                font-size: 15px;
                font-weight: 900;

                color: #53150f;

                outline: none;
            }

            .inventory-v34-number-field:focus-within {
                border-color: #8c6239;

                box-shadow:
                    0 0 0 3px
                    rgba(140,98,57,.08);
            }

            /*
            |--------------------------------------------------------------------------
            | REGRAS DE DESTREZA
            |--------------------------------------------------------------------------
            */

            .inventory-v34-rule-choice {
                display: flex;

                min-height: 62px;

                flex-direction: column;

                align-items: flex-start;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.66);

                border-radius: 9px;

                background: #fffdf8;

                padding:
                    8px 9px;

                text-align: left;

                transition:
                    border-color .12s ease,
                    background .12s ease;
            }

            .inventory-v34-rule-choice strong {
                font-size: 9.5px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v34-rule-choice span {
                margin-top: 2px;

                font-size: 7.5px;
                line-height: 1.25;

                color: #8c6239;
            }

            .inventory-v34-rule-choice.active {
                border-color:
                    rgba(107,29,20,.42);

                background:
                    rgba(107,29,20,.055);
            }

            .inventory-v34-cap-row {
                display: flex;

                flex-wrap: wrap;

                align-items: center;

                gap: 6px;

                margin-top: 10px;

                border:
                    1px solid
                    rgba(205,187,159,.50);

                border-radius: 9px;

                background:
                    rgba(247,240,230,.66);

                padding:
                    7px 8px;
            }

            .inventory-v34-cap-button {
                display: flex;

                min-width: 42px;
                height: 30px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.74);

                border-radius: 7px;

                background: #fffdf8;

                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #8c6239;
            }

            .inventory-v34-cap-button.active {
                border-color: #6b1d14;

                background: #6b1d14;

                color: #faf8f2;
            }

            .inventory-v34-cap-custom {
                display: flex;

                height: 30px;

                align-items: center;

                gap: 5px;

                border:
                    1px solid
                    rgba(205,187,159,.74);

                border-radius: 7px;

                background: #fffdf8;

                padding:
                    0 7px;
            }

            .inventory-v34-cap-custom span {
                font-size: 7px;
                font-weight: 900;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v34-cap-custom input {
                width: 42px;

                border: 0;

                background: transparent;

                text-align: center;

                font-family: Georgia, serif;
                font-size: 10px;
                font-weight: 900;

                color: #53150f;

                outline: 0;
            }

            /*
            |--------------------------------------------------------------------------
            | ATRIBUTOS EXTRAS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-ability {
                display: flex;

                min-width: 0;
                min-height: 48px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.64);

                border-radius: 9px;

                background: #fffdf8;

                padding:
                    5px 3px;

                transition:
                    border-color .12s ease,
                    background .12s ease;
            }

            .inventory-v34-ability strong {
                font-family: Georgia, serif;
                font-size: 10px;

                color: #53150f;
            }

            .inventory-v34-ability span {
                max-width: 100%;

                margin-top: 1px;

                overflow: hidden;

                font-size: 6.5px;

                color: #8c6239;

                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .inventory-v34-ability.active {
                border-color:
                    rgba(107,29,20,.40);

                background:
                    rgba(107,29,20,.06);
            }

            /*
            |--------------------------------------------------------------------------
            | FÓRMULA / RESUMOS
            |--------------------------------------------------------------------------
            */

            .inventory-v34-formula-card {
                display: flex;

                align-items: center;

                gap: 10px;

                border:
                    1px solid
                    rgba(107,29,20,.20);

                border-radius: 11px;

                background:
                    linear-gradient(
                        180deg,
                        rgba(107,29,20,.055),
                        rgba(107,29,20,.025)
                    );

                padding:
                    10px 11px;
            }

            .inventory-v34-formula-icon {
                display: flex;

                width: 34px;
                height: 34px;

                flex: 0 0 34px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(107,29,20,.18);

                border-radius: 9px;

                background:
                    rgba(255,253,249,.76);

                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: 900;

                color: #6b1d14;
            }

            .inventory-v34-formula-card span {
                display: block;

                font-size: 7px;
                font-weight: 900;
                letter-spacing: .07em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v34-formula-card strong {
                display: block;

                margin-top: 2px;

                font-family: Georgia, serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.3;

                color: #53150f;
            }

            .inventory-v34-formula-card small {
                display: block;

                margin-top: 2px;

                font-size: 8px;
                line-height: 1.35;

                color: #8c6239;
            }

            /*
            |--------------------------------------------------------------------------
            | ESCUDO — SOMA VISUAL
            |--------------------------------------------------------------------------
            */

            .inventory-v34-shield-total {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    auto
                    minmax(0, 1fr)
                    auto
                    minmax(0, 1.15fr);

                align-items: stretch;

                gap: 7px;
            }

            .inventory-v34-shield-total > div {
                display: flex;

                min-height: 58px;

                flex-direction: column;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(205,187,159,.62);

                border-radius: 9px;

                background: #fffdf8;
            }

            .inventory-v34-shield-total > div.is-total {
                border-color:
                    rgba(107,29,20,.28);

                background:
                    rgba(107,29,20,.055);
            }

            .inventory-v34-shield-total span {
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .06em;

                text-transform: uppercase;

                color: #8c6239;
            }

            .inventory-v34-shield-total strong {
                margin-top: 2px;

                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: 900;

                color: #53150f;
            }

            .inventory-v34-shield-total > b {
                display: flex;

                align-items: center;
                justify-content: center;

                font-family: Georgia, serif;
                font-size: 15px;

                color:
                    rgba(140,98,57,.48);
            }

            .inventory-v34-equipment-note {
                display: flex;

                align-items: center;

                gap: 8px;

                border-top:
                    1px solid
                    rgba(188,154,111,.22);

                padding-top: 11px;

                font-size: 8.5px;
                line-height: 1.4;

                color: #8c6239;
            }

            .inventory-v34-equipment-note svg {
                width: 16px;
                height: 16px;

                flex: 0 0 16px;

                color: #6b1d14;
            }

            @media (max-width: 560px) {
                .inventory-v34-defense-head {
                    padding:
                        10px 11px;
                }

                .inventory-v34-shield-total {
                    grid-template-columns:
                        1fr
                        auto
                        1fr;

                    gap: 5px;
                }

                .inventory-v34-shield-total
                > div.is-total {
                    grid-column:
                        1 / -1;
                }

                .inventory-v34-shield-total
                > b:nth-of-type(2) {
                    display: none;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V35 — SISTEMA VISUAL UNIFICADO
            |--------------------------------------------------------------------------
            |
            | Direção:
            | - papel neutro;
            | - cor do tema apenas como acento;
            | - tipografia escura e legível;
            | - modais com o mesmo contrato global;
            | - menos caixas decorativas;
            | - hierarquia editorial;
            | - estado mecânico continua semântico.
            |
            */


            /* ==============================================================
               BASE
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35 {
                color:
                    var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#53150f]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#6b1d14]"] {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#8c6239]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#92745c]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#9a795b]"],
            .character-sheet-theme[data-theme]
            .inventory-v35
            [class*="text-[#6b5548]"] {
                color:
                    var(--theme-text-secondary) !important;
            }


            /* ==============================================================
               TOOLBAR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v23-main-topbar {
                align-items:
                    center;

                min-height:
                    58px;

                border-bottom:
                    1px solid
                    var(--theme-divider) !important;

                padding:
                    0 0 14px !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-primary {
                border-color:
                    var(--theme-accent-strong) !important;

                background:
                    var(--theme-button-background) !important;

                color:
                    var(--theme-button-text) !important;

                box-shadow:
                    0 2px 8px
                    color-mix(
                        in srgb,
                        var(--theme-accent-deep) 12%,
                        transparent
                    );
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-primary:hover {
                border-color:
                    var(--theme-accent-deep) !important;

                background:
                    var(--theme-button-hover-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-action,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-trigger {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-action:hover,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-trigger:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-badge {
                background:
                    var(--theme-accent-soft) !important;

                color:
                    var(--theme-text-strong) !important;
            }


            /* ==============================================================
               BOLSA
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-pouch {
                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-label,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-chevron {
                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-copy
            strong {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-surface-raised) !important;

                box-shadow:
                    0 18px 50px rgba(0,0,0,.18),
                    var(
                        --theme-modal-control-shadow,
                        0 2px 7px rgba(0,0,0,.06)
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel-head {
                border-color:
                    var(--theme-divider) !important;

                background:
                    var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel-head
            strong,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-name
            b {
                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-wallet-panel-head
            span,
            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-name
            small {
                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-row {
                border-color:
                    var(--theme-divider) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-row
            input {
                border-color:
                    var(--theme-input-border) !important;

                background:
                    var(--theme-input-bg) !important;

                color:
                    var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v14-coin-row
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



            /*
            |--------------------------------------------------------------------------
            | INVENTÁRIO V38 — TOOLBAR / BOLSA DE MOEDAS
            |--------------------------------------------------------------------------
            |
            | A área superior deixa de parecer uma faixa de botões isolados.
            | Ela passa a funcionar como uma barra de ações compacta, enquanto
            | a Bolsa ganha hierarquia própria e um editor de moedas mais claro.
            |
            */


            /* ==============================================================
               TOOLBAR
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-toolbar {
                min-height:
                    68px;

                align-items:
                    center;

                gap:
                    14px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    16px;

                background:
                    linear-gradient(
                        180deg,
                        var(--theme-surface-card) 0%,
                        var(--theme-surface-card-alt) 100%
                    ) !important;

                padding:
                    10px 12px !important;

                box-shadow:
                    0 2px 10px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-actions {
                align-items:
                    center;

                gap:
                    8px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-primary-action {
                min-height:
                    42px;

                border-radius:
                    11px;

                padding:
                    0 15px;

                font-size:
                    11px;

                letter-spacing:
                    .01em;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-primary-action
            .inventory-v14-plus {
                font-size:
                    17px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-secondary-action {
                min-height:
                    42px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    11px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    0 13px;

                font-size:
                    11px;

                font-weight:
                    800;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    0 1px 4px rgba(0,0,0,.025);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-secondary-action:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-secondary-action
            .inventory-v14-badge {
                min-width:
                    24px;

                height:
                    24px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 12%,
                        var(--theme-border)
                    );

                background:
                    var(--theme-accent-soft) !important;

                font-size:
                    11px;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-toolbar-right {
                margin-left:
                    auto;
            }


            /* ==============================================================
               WALLET TRIGGER
            ============================================================== */

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-wrap {
                position:
                    relative;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger {
                min-width:
                    180px;

                min-height:
                    46px;

                gap:
                    10px;

                border:
                    1px solid
                    var(--theme-border) !important;

                border-radius:
                    12px;

                background:
                    var(--theme-surface-raised) !important;

                padding:
                    6px 10px 6px 8px;

                color:
                    var(--theme-text-primary) !important;

                box-shadow:
                    0 2px 7px rgba(0,0,0,.035);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger:hover {
                border-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-card-hover) !important;

                box-shadow:
                    0 4px 12px rgba(0,0,0,.05);
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-pouch {
                width:
                    34px;

                height:
                    34px;

                flex-basis:
                    34px;

                border:
                    1px solid
                    color-mix(
                        in srgb,
                        var(--theme-accent-strong) 14%,
                        var(--theme-border)
                    );

                border-radius:
                    10px;

                background:
                    var(--theme-accent-softer) !important;

                color:
                    var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-wallet-copy {
                display:
                    flex;

                min-width:
                    0;

                flex:
                    1;

                flex-direction:
                    column;

                align-items:
                    flex-start;

                gap:
                    2px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-wallet-label {
                font-size:
                    8px;

                line-height:
                    1;

                letter-spacing:
                    .10em;

                color:
                    var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-total-line {
                display:
                    flex;

                align-items:
                    baseline;

                gap:
                    5px;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-total-line
            strong {
                font-family:
                    Georgia,
                    "Times New Roman",
                    serif;

                font-size:
                    15px;

                line-height:
                    1;

                color:
                    var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-total-line
            small {
                font-size:
                    9px;

                color:
                    var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .inventory-v35
            .inventory-v38-wallet-trigger
            .inventory-v14-wallet-chevron {
                width:
                    16px;

                height:
                    16px;

                color:
                    var(--theme-label) !important;
            }


@include('characters.components.inventario.efeitos')        </style>
    @endpush