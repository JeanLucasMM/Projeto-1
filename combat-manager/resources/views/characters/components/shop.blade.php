@props([
    'character',
    'characterTheme' => 'spellbound',
])

@once
    <style>
        [x-cloak] {
            display: none !important;
        }

        .player-storefront-layer {
            position: fixed;
            inset: 0;
            z-index: 285;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
        }

        .player-storefront-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(8, 7, 6, .74);
            backdrop-filter: blur(6px);
        }

        .player-storefront {
            --shop-bg:
                var(--theme-canvas-background, #f3eadc);
            --shop-surface:
                var(--theme-card-background, #fffaf2);
            --shop-surface-alt:
                var(--theme-card-alt-background, #f5eadc);
            --shop-raised:
                var(--theme-surface-raised, var(--shop-surface));
            --shop-inset:
                var(--theme-inset-background, rgba(255,255,255,.05));
            --shop-border:
                var(--theme-border, rgba(205,187,159,.48));
            --shop-border-strong:
                var(--theme-border-strong, rgba(176,140,98,.62));
            --shop-title:
                var(--theme-title, #53150f);
            --shop-text:
                var(--theme-text, #6f5542);
            --shop-text-strong:
                var(--theme-text-strong, var(--shop-title));
            --shop-muted:
                var(--theme-muted, #8c6e57);
            --shop-label:
                var(--theme-label, #9a795b);
            --shop-accent:
                var(--theme-accent, #6b1d14);
            --shop-accent-strong:
                var(--theme-accent-strong, var(--shop-accent));
            --shop-hover:
                var(--theme-hover, rgba(107,29,20,.07));

            position: relative;
            z-index: 1;
            display: flex;
            width: min(1240px, 100%);
            height: min(820px, calc(100dvh - 28px));
            min-height: 0;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--shop-border-strong);
            border-radius: 20px;
            background: var(--shop-bg);
            color: var(--shop-text);
            box-shadow: 0 28px 80px rgba(0,0,0,.36);
        }

        .player-storefront.is-overlay-open {
            border-color: transparent;
            box-shadow: none;
        }

        .player-storefront button,
        .player-storefront input,
        .player-storefront select {
            font: inherit;
        }

        .player-storefront-state {
            display: flex;
            min-height: 420px;
            flex: 1 1 auto;
            align-items: center;
            justify-content: center;
            padding: 28px;
            text-align: center;
        }

        .player-storefront-state__spinner {
            width: 38px;
            height: 38px;
            margin: 0 auto;
            border: 2px solid var(--shop-border);
            border-top-color: var(--shop-accent);
            border-radius: 999px;
            animation: playerShopSpin .8s linear infinite;
        }

        @keyframes playerShopSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .player-storefront-state h3 {
            margin: 14px 0 0;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 22px;
            font-weight: 900;
        }

        .player-storefront-state p {
            max-width: 460px;
            margin: 7px auto 0;
            color: var(--shop-muted);
            font-size: 12px;
            line-height: 1.55;
        }

        .player-storefront-state button {
            min-height: 38px;
            margin-top: 16px;
            border: 1px solid var(--shop-border-strong);
            border-radius: 10px;
            background: var(--shop-surface);
            padding: 0 14px;
            color: var(--shop-title);
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
        }

        .player-storefront-shell {
            display: flex;
            min-height: 0;
            flex: 1 1 auto;
            flex-direction: column;
        }

        .player-storefront-head {
            position: relative;
            flex: 0 0 auto;
            overflow: hidden;
            border-bottom: 1px solid var(--shop-border);
            background: var(--shop-surface);
        }

        .player-storefront-head__background {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .player-storefront-head__background img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: .16;
            filter: saturate(.82);
        }

        .player-storefront-head__shade {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(
                    90deg,
                    var(--shop-surface) 0%,
                    color-mix(in srgb, var(--shop-surface) 92%, transparent) 52%,
                    color-mix(in srgb, var(--shop-surface) 78%, transparent) 100%
                );
        }

        .player-storefront-head__content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 20px;
            align-items: center;
            min-height: 116px;
            padding: 14px 18px;
        }

        .player-storefront-identity {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 14px;
        }

        .player-storefront-shopkeeper {
            display: flex;
            width: 76px;
            height: 76px;
            flex: 0 0 auto;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            border: 1px solid var(--shop-border-strong);
            border-radius: 15px;
            background:
                linear-gradient(
                    145deg,
                    var(--shop-inset),
                    var(--shop-surface-alt)
                );
        }

        .player-storefront-shopkeeper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center bottom;
        }

        .player-storefront-shopkeeper__empty {
            color: var(--shop-muted);
            font-family: Georgia, serif;
            font-size: 28px;
        }

        .player-storefront-copy {
            min-width: 0;
        }

        .player-storefront-kicker {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            margin: 0 0 4px;
            color: var(--shop-label);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .player-storefront-open-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #10b981;
            box-shadow: 0 0 0 4px rgba(16,185,129,.10);
        }

        .player-storefront-name {
            margin: 0;
            overflow: hidden;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: clamp(24px, 2.7vw, 34px);
            font-weight: 900;
            line-height: 1.02;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .player-storefront-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 5px 12px;
            margin-top: 6px;
            color: var(--shop-muted);
            font-size: 10px;
            font-weight: 700;
        }

        .player-storefront-description {
            display: -webkit-box;
            overflow: hidden;
            max-width: 760px;
            margin: 6px 0 0;
            color: var(--shop-text);
            font-size: 11px;
            line-height: 1.45;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .player-storefront-head-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .player-storefront-wallet {
            min-width: 180px;
            max-width: 320px;
            padding: 9px 11px;
            border: 1px solid var(--shop-border);
            border-radius: 11px;
            background: color-mix(in srgb, var(--shop-raised) 92%, transparent);
            text-align: right;
        }

        .player-storefront-wallet span {
            display: block;
            color: var(--shop-label);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .player-storefront-wallet strong {
            display: block;
            overflow: hidden;
            margin-top: 3px;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 12px;
            font-weight: 900;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .player-storefront-icon-action {
            display: grid;
            width: 38px;
            height: 38px;
            flex: 0 0 auto;
            place-items: center;
            border: 1px solid var(--shop-border);
            border-radius: 11px;
            background: var(--shop-raised);
            color: var(--shop-muted);
            cursor: pointer;
            transition:
                border-color .15s ease,
                background .15s ease,
                color .15s ease,
                transform .15s ease;
        }

        .player-storefront-icon-action:hover {
            transform: translateY(-1px);
            border-color: var(--shop-border-strong);
            background: var(--shop-hover);
            color: var(--shop-accent);
        }

        .player-storefront-icon-action svg {
            width: 16px;
            height: 16px;
        }

        .player-storefront-shop-tabs {
            display: flex;
            flex: 0 0 auto;
            gap: 6px;
            overflow-x: auto;
            border-bottom: 1px solid var(--shop-border);
            background: var(--shop-surface-alt);
            padding: 8px 16px;
            scrollbar-width: thin;
        }

        .player-storefront-shop-tab {
            min-height: 32px;
            flex: 0 0 auto;
            border: 1px solid var(--shop-border);
            border-radius: 9px;
            background: var(--shop-raised);
            padding: 0 10px;
            color: var(--shop-muted);
            font-size: 9px;
            font-weight: 900;
            cursor: pointer;
            transition:
                border-color .15s ease,
                background .15s ease,
                color .15s ease;
        }

        .player-storefront-shop-tab:hover {
            background: var(--shop-hover);
            color: var(--shop-title);
        }

        .player-storefront-shop-tab.is-active {
            border-color:
                color-mix(
                    in srgb,
                    var(--shop-accent) 55%,
                    var(--shop-border)
                );
            background:
                color-mix(
                    in srgb,
                    var(--shop-accent) 16%,
                    var(--shop-raised)
                );
            color: var(--shop-accent-strong);
        }

        .player-storefront-toolbar {
            display: none;
        }

        .player-storefront-sort {
            width: 158px;
            height: 32px;
            border: 1px solid var(--shop-border);
            border-radius: 10px;
            outline: none;
            background: var(--shop-raised);
            color: var(--shop-text-strong);
            font-size: 10px;
            font-weight: 700;
            transition:
                border-color .15s ease,
                box-shadow .15s ease,
                background .15s ease;
        }

        .player-storefront-search input {
            padding: 0 12px 0 36px;
        }

        .player-storefront-sort {
            padding: 0 10px;
        }

        .player-storefront-search input::placeholder {
            color: var(--shop-muted);
        }

        .player-storefront-search input:focus,
        .player-storefront-sort:focus {
            border-color: var(--shop-border-strong);
            background: var(--shop-surface);
            box-shadow:
                0 0 0 3px
                color-mix(
                    in srgb,
                    var(--shop-accent) 9%,
                    transparent
                );
        }

        .player-storefront-filters {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 3px;
            border: 1px solid var(--shop-border);
            border-radius: 10px;
            background: var(--shop-inset);
        }

        .player-storefront-filter {
            min-height: 30px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            padding: 0 9px;
            color: var(--shop-muted);
            font-size: 8px;
            font-weight: 900;
            cursor: pointer;
            transition:
                background .15s ease,
                color .15s ease;
        }

        .player-storefront-filter:hover {
            color: var(--shop-title);
        }

        .player-storefront-filter.is-active {
            background: var(--shop-raised);
            color: var(--shop-accent-strong);
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
        }

        .player-storefront-content {
            min-height: 0;
            flex: 1 1 auto;
            overflow-y: auto;
            background:
                radial-gradient(
                    circle at 82% 0%,
                    color-mix(in srgb, var(--shop-accent) 4%, transparent),
                    transparent 30%
                ),
                linear-gradient(
                    180deg,
                    color-mix(in srgb, var(--shop-surface-alt) 32%, var(--shop-bg)) 0%,
                    var(--shop-bg) 100%
                );
            padding: 10px 14px 18px;
            scrollbar-width: thin;
            scrollbar-color:
                color-mix(
                    in srgb,
                    var(--shop-muted) 45%,
                    transparent
                )
                transparent;
        }

        .player-storefront-content::-webkit-scrollbar {
            width: 7px;
        }

        .player-storefront-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .player-storefront-content::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background:
                color-mix(
                    in srgb,
                    var(--shop-muted) 35%,
                    transparent
                );
        }

        .player-storefront-empty {
            display: grid;
            min-height: 260px;
            place-items: center;
            border: 1px dashed var(--shop-border-strong);
            border-radius: 14px;
            background:
                color-mix(
                    in srgb,
                    var(--shop-surface) 55%,
                    transparent
                );
            padding: 28px;
            text-align: center;
        }

        .player-storefront-empty strong {
            display: block;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 18px;
            font-weight: 900;
        }

        .player-storefront-empty p {
            max-width: 420px;
            margin: 6px auto 0;
            color: var(--shop-muted);
            font-size: 11px;
            line-height: 1.55;
        }

        .player-storefront-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(220px, 1fr)
                );
            gap: 11px;
            align-items: stretch;
        }

        .player-storefront-category-list {
            display: grid;
            gap: 8px;
        }

        .player-storefront-category {
            position: relative;
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--shop-border);
            border-radius: 15px;
            background:
                linear-gradient(
                    180deg,
                    color-mix(in srgb, var(--shop-surface) 78%, transparent) 0%,
                    color-mix(in srgb, var(--shop-surface-alt) 58%, transparent) 100%
                );
            box-shadow:
                0 7px 18px rgba(0,0,0,.04),
                inset 0 1px 0 rgba(255,255,255,.38);
            transition:
                border-color .16s ease,
                box-shadow .16s ease,
                background .16s ease;
        }

        .player-storefront-category.is-open {
            border-color:
                color-mix(
                    in srgb,
                    var(--shop-accent) 26%,
                    var(--shop-border)
                );
            box-shadow:
                0 10px 25px rgba(0,0,0,.055),
                inset 0 1px 0 rgba(255,255,255,.46);
        }

        .player-storefront-category__head {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 11px;
            border: 0;
            background: transparent;
            padding: 10px 12px;
            color: inherit;
            text-align: left;
            cursor: pointer;
            transition:
                background .15s ease;
        }

        .player-storefront-category__head:hover {
            background:
                color-mix(
                    in srgb,
                    var(--shop-hover) 72%,
                    transparent
                );
        }

        .player-storefront-category.is-open
            .player-storefront-category__head {
            border-bottom:
                1px solid
                color-mix(
                    in srgb,
                    var(--shop-border) 82%,
                    transparent
                );
            background:
                color-mix(
                    in srgb,
                    var(--shop-surface) 44%,
                    transparent
                );
        }

        .player-storefront-category__icon {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            place-items: center;
            border: 1px solid var(--shop-border);
            border-radius: 10px;
            background: var(--shop-surface);
            color: var(--shop-accent);
            box-shadow: 0 3px 10px rgba(0,0,0,.045);
        }

        .player-storefront-category__icon svg {
            width: 17px;
            height: 17px;
        }

        .player-storefront-category__copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .player-storefront-category__copy h4 {
            margin: 0;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.08;
        }

        .player-storefront-category__copy p {
            margin: 3px 0 0;
            color: var(--shop-muted);
            font-size: 8.8px;
            line-height: 1.36;
        }

        .player-storefront-category__meta {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 7px;
        }

        .player-storefront-category__count {
            min-width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--shop-border);
            border-radius: 999px;
            background: var(--shop-surface);
            padding: 0 7px;
            color: var(--shop-label);
            font-size: 8px;
            font-weight: 900;
        }

        .player-storefront-category__chevron {
            display: grid;
            width: 27px;
            height: 27px;
            place-items: center;
            border: 1px solid var(--shop-border);
            border-radius: 8px;
            background: var(--shop-surface);
            color: var(--shop-muted);
            transition:
                transform .18s ease,
                color .15s ease,
                border-color .15s ease;
        }

        .player-storefront-category__chevron svg {
            width: 13px;
            height: 13px;
        }

        .player-storefront-category.is-open
            .player-storefront-category__chevron {
            transform: rotate(180deg);
            border-color:
                color-mix(
                    in srgb,
                    var(--shop-accent) 28%,
                    var(--shop-border)
                );
            color: var(--shop-accent);
        }

        .player-storefront-category__body {
            padding: 11px 12px 15px;
        }

        .player-storefront-category__body-inner {
            position: relative;
        }

        .player-storefront-category__body-inner::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: -8px;
            left: 0;
            height: 4px;
            border-radius: 999px;
            background:
                linear-gradient(
                    180deg,
                    color-mix(in srgb, var(--shop-title) 13%, transparent),
                    color-mix(in srgb, var(--shop-title) 3%, transparent)
                );
            opacity: .38;
            pointer-events: none;
        }

        .player-storefront-category[data-category="consumables"]
            .player-storefront-category__icon {
            border-color:
                color-mix(
                    in srgb,
                    #7f8d50 36%,
                    var(--shop-border)
                );
            background:
                color-mix(
                    in srgb,
                    #a9b979 13%,
                    var(--shop-surface)
                );
            color: #6d793f;
        }

        .player-storefront-category[data-category="magical"]
            .player-storefront-category__icon {
            border-color:
                color-mix(
                    in srgb,
                    var(--shop-accent) 38%,
                    var(--shop-border)
                );
            background:
                color-mix(
                    in srgb,
                    var(--shop-accent) 9%,
                    var(--shop-surface)
                );
            color: var(--shop-accent);
        }

        .player-storefront-category[data-category="mundane"]
            .player-storefront-category__icon {
            border-color:
                color-mix(
                    in srgb,
                    #8b755f 32%,
                    var(--shop-border)
                );
            background:
                color-mix(
                    in srgb,
                    #8b755f 8%,
                    var(--shop-surface)
                );
            color: #7d6854;
        }

        .player-storefront-item {
            display: flex;
            min-width: 0;
            min-height: 228px;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--shop-border);
            border-radius: 15px;
            background: var(--shop-surface);
            box-shadow:
                0 7px 18px rgba(0,0,0,.07),
                inset 0 1px 0 rgba(255,255,255,.55);
            transition:
                transform .15s ease,
                border-color .15s ease,
                box-shadow .15s ease;
        }

        .player-storefront-item:hover {
            transform: translateY(-3px);
            border-color: var(--shop-border-strong);
            box-shadow: 0 8px 20px rgba(0,0,0,.10);
        }

        .player-storefront-item.is-sold-out {
            opacity: .68;
        }

        .player-storefront-item__main {
            display: block;
            padding: 10px 10px 9px;
        }

        .player-storefront-item__image {
            position: relative;
            display: flex;
            width: 100%;
            height: 108px;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid var(--shop-border);
            border-radius: 12px;
            background:
                radial-gradient(
                    circle at 50% 38%,
                    color-mix(in srgb, var(--shop-surface) 82%, white),
                    var(--shop-inset) 78%
                );
            padding: 8px;
        }

        .player-storefront-item__image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 7px 7px rgba(0,0,0,.12));
        }

        .player-storefront-item__image-empty {
            color: var(--shop-muted);
            font-family: Georgia, serif;
            font-size: 24px;
        }

        .player-storefront-stock {
            position: absolute;
            right: 4px;
            bottom: 4px;
            min-height: 18px;
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--shop-border);
            border-radius: 999px;
            background: var(--shop-raised);
            padding: 0 6px;
            color: var(--shop-text-strong);
            font-size: 7px;
            font-weight: 900;
        }

        .player-storefront-stock.is-empty {
            color: var(--shop-muted);
        }

        .player-storefront-item__copy {
            min-width: 0;
            padding: 9px 2px 0;
        }

        .player-storefront-item__kind {
            overflow: hidden;
            margin: 1px 0 4px;
            color: var(--shop-label);
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .08em;
            text-overflow: ellipsis;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .player-storefront-item h4 {
            display: -webkit-box;
            overflow: hidden;
            margin: 0;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.12;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .player-storefront-item__description {
            display: -webkit-box;
            overflow: hidden;
            margin: 6px 0 0;
            color: var(--shop-text);
            font-size: 9px;
            line-height: 1.42;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .player-storefront-item__inspect {
            margin-top: 7px;
            color: var(--shop-accent);
            font-size: 8px;
            font-weight: 900;
        }

        .player-storefront-item__footer {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: auto;
            border-top: 1px solid var(--shop-border);
            background:
                linear-gradient(
                    180deg,
                    color-mix(in srgb, var(--shop-surface-alt) 82%, var(--shop-surface)) 0%,
                    var(--shop-surface-alt) 100%
                );
            padding: 9px 10px 10px;
        }

        .player-storefront-price-row {
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 8px;
        }

        .player-storefront-price,
        .player-storefront-total-badge {
            min-width: 0;
            flex: 1 1 0;
            border: 1px solid var(--shop-border);
            border-radius: 11px;
            padding: 8px 10px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.48);
        }

        .player-storefront-price {
            text-align: center;
            background:
                linear-gradient(
                    180deg,
                    color-mix(in srgb, var(--shop-accent) 8%, var(--shop-surface)) 0%,
                    color-mix(in srgb, #d9b98d 24%, var(--shop-surface-alt)) 100%
                );
            border-color:
                color-mix(
                    in srgb,
                    var(--shop-accent) 30%,
                    var(--shop-border)
                );
        }

        .player-storefront-total-badge {
            background:
                linear-gradient(
                    180deg,
                    color-mix(in srgb, #7d6854 8%, var(--shop-surface)) 0%,
                    color-mix(in srgb, #b9a28d 18%, var(--shop-surface-alt)) 100%
                );
            text-align: center;
        }

        .player-storefront-price span,
        .player-storefront-total-badge span {
            display: block;
            color: var(--shop-label);
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .player-storefront-price strong,
        .player-storefront-total-badge strong {
            display: block;
            margin-top: 3px;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.05;
            overflow-wrap: anywhere;
        }

        .player-storefront-buy {
            display: grid;
            width: 100%;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 7px;
        }

        .player-storefront-buy-stack {
            display: flex;
            width: 100%;
            min-width: 0;
            flex-direction: column;
            align-items: stretch;
        }

        .player-storefront-qty {
            display: grid;
            width: 100%;
            grid-template-columns: 28px minmax(24px, 1fr) 28px;
            align-items: center;
            overflow: hidden;
            border: 1px solid var(--shop-border);
            border-radius: 9px;
            background: var(--shop-raised);
        }

        .player-storefront-qty button {
            width: 28px;
            height: 32px;
            border: 0;
            background: transparent;
            color: var(--shop-muted);
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
        }

        .player-storefront-qty button:hover {
            background: var(--shop-hover);
            color: var(--shop-accent);
        }

        .player-storefront-qty span {
            min-width: 0;
            color: var(--shop-text-strong);
            font-size: 9px;
            font-weight: 900;
            text-align: center;
        }

        .player-storefront-buy-button {
            min-width: 104px;
            height: 32px;
            border: 1px solid
                color-mix(
                    in srgb,
                    var(--shop-accent) 70%,
                    var(--shop-border)
                );
            border-radius: 9px;
            background: var(--shop-accent);
            padding: 0 11px;
            color: #fff;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
            cursor: pointer;
            transition:
                transform .15s ease,
                filter .15s ease,
                opacity .15s ease;
        }

        .player-storefront-buy-button:hover {
            transform: translateY(-1px);
            filter: brightness(.94);
        }

        .player-storefront-buy-button:disabled {
            cursor: wait;
            opacity: .55;
            transform: none;
        }

        .player-storefront-total {
            display: none;
        }

        .player-storefront-notice {
            pointer-events: none;
            position: absolute;
            bottom: 14px;
            left: 50%;
            z-index: 50;
            max-width: min(520px, calc(100% - 28px));
            transform: translateX(-50%);
            border: 1px solid var(--shop-border-strong);
            border-radius: 11px;
            background: var(--shop-raised);
            padding: 10px 13px;
            color: var(--shop-text-strong);
            box-shadow: 0 15px 38px rgba(0,0,0,.24);
            font-size: 10px;
            font-weight: 900;
        }

        .player-storefront-notice.is-error {
            border-color: rgba(185,65,55,.44);
            color: #c5554b;
        }


        /* ==========================================================
           SHOP V11 — MODAL MAIS HORIZONTAL + TIPOGRAFIA +2PX
           ========================================================== */

        .player-storefront-purchase-v8__head h3 {
            font-size: 26px;
        }

        .player-storefront-purchase-v8__head .player-storefront-purchase__eyebrow,
        .player-storefront-purchase-v8__classification,
        .player-storefront-purchase-v8__section-label {
            font-size: 11px;
        }

        .player-storefront-purchase-v8__product-copy h4 {
            font-size: 30px;
        }

        .player-storefront-purchase-v8__product-copy p {
            font-size: 12px;
            line-height: 1.45;
        }

        .player-storefront-purchase-v8__product-meta small {
            font-size: 9px;
        }

        .player-storefront-purchase-v8__product-meta strong {
            font-size: 19px;
        }

        .player-storefront-purchase-v8__deal-top h4 {
            font-size: 25px;
        }

        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__head strong {
            font-size: 13px;
        }

        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__head span,
        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__badge {
            font-size: 10px;
        }

        .player-storefront-payment-coin-v8__stepper input {
            font-size: 16px;
        }

        .player-storefront-payment-settlement__label {
            font-size: 10px;
        }

        .player-storefront-payment-settlement strong {
            font-size: 18px;
        }

        .player-storefront-payment-settlement small {
            font-size: 11px;
        }

        .player-storefront-purchase-v8__actions .player-storefront-payment-action {
            font-size: 9px;
        }

        @media (max-width: 900px) {
            .player-storefront {
                height: calc(100dvh - 20px);
            }

            .player-storefront-head__content {
                grid-template-columns: 1fr;
                gap: 10px;
                min-height: 0;
            }

            .player-storefront-head-actions {
                justify-content: space-between;
            }

            .player-storefront-wallet {
                flex: 1 1 auto;
                max-width: none;
                text-align: left;
            }

            .player-storefront-toolbar {
                justify-content: flex-end;
            }
        }

        @media (max-width: 760px) {
            .player-storefront-price-row {
                gap: 6px;
            }

            .player-storefront-price strong,
            .player-storefront-total-badge strong {
                font-size: 16px;
            }

            .player-storefront-grid {
                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(190px, 1fr)
                    );
            }
        }

        @media (max-width: 640px) {
            .player-storefront-content {
                padding: 8px 9px 14px;
            }

            .player-storefront-category__head {
                padding: 9px 10px;
            }

            .player-storefront-category__copy p {
                display: none;
            }

            .player-storefront-category__body {
                padding: 9px 9px 13px;
            }

            .player-storefront-layer {
                padding: 0;
            }

            .player-storefront {
                width: 100%;
                height: 100dvh;
                border: 0;
                border-radius: 0;
            }

            .player-storefront-head__content {
                padding: 12px;
            }

            .player-storefront-shopkeeper {
                width: 60px;
                height: 60px;
            }

            .player-storefront-name {
                font-size: 22px;
            }

            .player-storefront-description {
                -webkit-line-clamp: 1;
            }

            .player-storefront-head-actions {
                gap: 6px;
            }

            .player-storefront-wallet {
                min-width: 0;
            }

            .player-storefront-wallet strong {
                font-size: 11px;
            }

            .player-storefront-toolbar {
                justify-content: stretch;
                padding: 6px 10px;
            }

            .player-storefront-sort {
                width: 100%;
            }

            .player-storefront-content {
                padding: 10px;
            }

            .player-storefront-grid {
                grid-template-columns: 1fr;
            }
        }


        /* ==========================================================
           V3 — CENA CONFIGURADA PELO MESTRE + FICHA DO ITEM
           ========================================================== */

        .player-storefront-scene {
            position: relative;
            flex: 0 0 auto;
            height: 200px;
            overflow: hidden;
            border-bottom: 1px solid var(--shop-border);
            background: linear-gradient(180deg, #6f4a33 0%, #523221 100%);
            isolation: isolate;
        }

        .player-storefront-scene::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 32px;
            background: linear-gradient(180deg, rgba(40, 19, 10, 0) 0%, rgba(40, 19, 10, .38) 55%, rgba(26, 12, 7, .74) 100%);
            pointer-events: none;
            z-index: 2;
        }

        .player-storefront-scene__background {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
        }

        .player-storefront-scene__background img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .2s ease;
        }

        .player-storefront-scene__shade {
            position: absolute;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(90deg, rgba(18,10,7,.48), rgba(18,10,7,.08) 42%, rgba(18,10,7,.14)),
                linear-gradient(180deg, rgba(10,6,4,.12), transparent 38%, rgba(10,6,4,.28));
            pointer-events: none;
        }

        .player-storefront-scene__title {
            position: absolute;
            top: 12px;
            left: 15px;
            z-index: 6;
            width: min(350px, calc(100% - 340px));
            min-width: 235px;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 14px;
            background: rgba(20,12,8,.40);
            padding: 9px 12px;
            color: #fff8ef;
            box-shadow: 0 12px 32px rgba(0,0,0,.18);
            backdrop-filter: blur(9px);
        }

        .player-storefront-scene__status {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            color: rgba(255,248,239,.72);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .player-storefront-scene__title h2 {
            overflow: hidden;
            margin: 3px 0 0;
            color: #fff;
            font-family: Georgia, serif;
            font-size: clamp(24px, 2.7vw, 34px);
            font-weight: 900;
            line-height: 1.02;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-shadow: 0 3px 12px rgba(0,0,0,.32);
        }

        .player-storefront-scene__title > p:not(.player-storefront-scene__description) {
            margin: 4px 0 0;
            color: rgba(255,248,239,.82);
            font-size: 9px;
        }

        .player-storefront-scene__description {
            display: none;
        }

        .player-storefront-scene__actions {
            position: absolute;
            top: 12px;
            right: 14px;
            z-index: 8;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .player-storefront-scene .player-storefront-wallet {
            border-color: rgba(255,255,255,.25);
            background: rgba(20,12,8,.42);
            color: #fff;
            backdrop-filter: blur(8px);
        }

        .player-storefront-scene .player-storefront-wallet span {
            color: rgba(255,248,239,.60);
        }

        .player-storefront-scene .player-storefront-wallet strong {
            color: #fff;
        }

        .player-storefront-scene-action {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            border: 1px solid rgba(255,255,255,.26);
            border-radius: 11px;
            background: rgba(20,12,8,.40);
            color: #fff;
            cursor: pointer;
            backdrop-filter: blur(8px);
            transition: transform .15s ease, background .15s ease;
        }

        .player-storefront-scene-action:hover {
            transform: translateY(-1px);
            background: rgba(20,12,8,.62);
        }

        .player-storefront-scene-action svg {
            width: 17px;
            height: 17px;
        }

        .player-storefront-scene__keeper {
            position: absolute;
            top: 6px;
            right: 34%;
            bottom: 21px;
            left: 34%;
            z-index: 4;
            overflow: hidden;
            pointer-events: none;
        }

        .player-storefront-scene__keeper.is-counterless {
            bottom: 0;
        }

        .player-storefront-scene__keeper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center bottom;
            filter: drop-shadow(0 12px 16px rgba(0,0,0,.34));
            transition: transform .2s ease;
        }

        .player-storefront-counter-top {
            position: absolute;
            right: 2.8%;
            bottom: 19px;
            left: 2.8%;
            z-index: 6;
            height: 10px;
            border: 1px solid;
            border-radius: 14px;
            box-shadow: 0 -10px 20px rgba(20,9,5,.18), 0 10px 16px rgba(20,9,5,.18);
        }

        .player-storefront-counter-top > div {
            position: absolute;
            top: 2px;
            right: 6%;
            left: 6%;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--counter-color) 62%, white), transparent);
            opacity: .75;
        }

        .player-storefront-counter-front {
            position: absolute;
            right: 4.8%;
            bottom: 0;
            left: 4.8%;
            z-index: 5;
            height: 22px;
            overflow: hidden;
            border: 1px solid;
            border-radius: 14px 14px 0 0;
            box-shadow: 0 12px 22px rgba(20,9,5,.18), inset 0 1px 0 rgba(255,255,255,.08);
        }

        .player-storefront-counter-front::before {
            content: '';
            position: absolute;
            top: 5px;
            right: 4%;
            left: 4%;
            height: 1px;
            background: rgba(255,255,255,.08);
        }

        .player-storefront-counter-front::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(180deg, rgba(0,0,0,0), rgba(0,0,0,.18));
        }

        .player-storefront-counter-panel {
            position: absolute;
            top: 4px;
            bottom: 4px;
            width: 30%;
            border: 1px solid color-mix(in srgb, var(--counter-color) 58%, black);
            border-radius: 8px;
            background: linear-gradient(180deg, color-mix(in srgb, var(--counter-color) 88%, white) 0%, color-mix(in srgb, var(--counter-color) 88%, black) 100%);
            opacity: .56;
        }

        .player-storefront-counter-panel.is-left { left: 9%; }
        .player-storefront-counter-panel.is-right { right: 9%; }

        .player-storefront-counter-lock {
            display: none;
        }

        .player-storefront-counter-lock span {
            display: none;
        }

        .player-storefront-item {
            cursor: pointer;
            outline: none;
        }

        .player-storefront-item:focus-visible {
            border-color: var(--shop-accent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--shop-accent) 18%, transparent);
        }

        .player-storefront-item__inspect {
            margin-top: 7px;
            color: var(--shop-accent);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .02em;
        }

        .player-storefront-detail-layer {
            position: absolute;
            inset: -1px;
            z-index: 90;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: inherit;
            padding: 16px;
        }

        .player-storefront-detail-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(8,7,6,.88);
            backdrop-filter: blur(9px);
        }

        .player-storefront-detail {
            position: relative;
            z-index: 1;
            display: flex;
            width: min(930px, 100%);
            max-height: calc(100% - 8px);
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--shop-border-strong);
            border-radius: 18px;
            background: var(--shop-bg);
            box-shadow: 0 28px 70px rgba(0,0,0,.42);
        }

        .player-storefront-detail__topbar {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid var(--shop-border);
            background: var(--shop-surface-alt);
            padding: 10px 13px 10px 16px;
        }

        .player-storefront-detail__topbar div span,
        .player-storefront-detail__topbar div strong {
            display: block;
        }

        .player-storefront-detail__topbar div span {
            color: var(--shop-label);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .player-storefront-detail__topbar div strong {
            margin-top: 2px;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 14px;
        }

        .player-storefront-detail__topbar > button {
            width: 34px;
            height: 34px;
            border: 1px solid var(--shop-border);
            border-radius: 10px;
            background: var(--shop-raised);
            color: var(--shop-muted);
            font-size: 20px;
            cursor: pointer;
        }

        .player-storefront-detail__scroll {
            min-height: 0;
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 18px;
            scrollbar-width: thin;
            scrollbar-color: var(--shop-border-strong) transparent;
        }

        .player-storefront-detail__hero {
            display: grid;
            grid-template-columns: 150px minmax(0, 1fr) 180px;
            gap: 18px;
            align-items: start;
        }

        .player-storefront-detail__image {
            display: grid;
            width: 150px;
            height: 150px;
            place-items: center;
            overflow: hidden;
            border: 1px solid var(--shop-border);
            border-radius: 15px;
            background: linear-gradient(180deg, color-mix(in srgb, var(--shop-surface-alt) 74%, white) 0%, color-mix(in srgb, var(--shop-inset) 76%, white) 100%);
            padding: 12px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        }

        .player-storefront-detail__image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center center;
        }

        .player-storefront-detail__image > span {
            color: var(--shop-muted);
            font-family: Georgia, serif;
            font-size: 36px;
        }

        .player-storefront-detail__identity {
            min-width: 0;
        }

        .player-storefront-detail__kind {
            margin: 2px 0 5px;
            color: var(--shop-label);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .player-storefront-detail__identity h2 {
            margin: 0;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: clamp(26px, 3vw, 34px);
            font-weight: 900;
            line-height: 1.04;
        }

        .player-storefront-detail__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .player-storefront-detail__chips > span {
            min-height: 23px;
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--shop-border);
            border-radius: 999px;
            background: var(--shop-inset);
            padding: 0 8px;
            color: var(--shop-text-strong);
            font-size: 8px;
            font-weight: 900;
        }

        .player-storefront-detail__lead {
            margin: 12px 0 0;
            color: var(--shop-text);
            font-size: 12px;
            line-height: 1.65;
            white-space: pre-line;
        }

        .player-storefront-detail__commerce {
            border: 1px solid var(--shop-border);
            border-radius: 13px;
            background: var(--shop-surface);
            padding: 12px;
        }

        .player-storefront-detail__commerce span,
        .player-storefront-detail__commerce strong,
        .player-storefront-detail__commerce small {
            display: block;
        }

        .player-storefront-detail__commerce span {
            color: var(--shop-label);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .player-storefront-detail__commerce strong {
            margin-top: 4px;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 18px;
        }

        .player-storefront-detail__commerce small {
            margin-top: 7px;
            color: var(--shop-muted);
            font-size: 9px;
            line-height: 1.4;
        }

        .player-storefront-detail-section {
            margin-top: 18px;
            border-top: 1px solid var(--shop-border);
            padding-top: 16px;
        }

        .player-storefront-detail-section__title {
            margin-bottom: 10px;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 19px;
            font-weight: 900;
        }

        .player-storefront-detail-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .player-storefront-detail-stat {
            min-width: 0;
            border: 1px solid var(--shop-border);
            border-radius: 11px;
            background: var(--shop-surface);
            padding: 9px 10px;
        }

        .player-storefront-detail-stat span {
            display: block;
            color: var(--shop-label);
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .player-storefront-detail-stat strong {
            display: block;
            margin-top: 3px;
            color: var(--shop-text-strong);
            font-size: 11px;
            line-height: 1.3;
        }

        .player-storefront-detail-property-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }

        .player-storefront-detail-property,
        .player-storefront-detail-feature,
        .player-storefront-detail-rule {
            border: 1px solid var(--shop-border);
            border-radius: 12px;
            background: var(--shop-surface);
            padding: 11px 12px;
        }

        .player-storefront-detail-property > div,
        .player-storefront-detail-feature__head {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }

        .player-storefront-detail-property h3,
        .player-storefront-detail-feature h3,
        .player-storefront-detail-rule h3 {
            margin: 0;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 14px;
            font-weight: 900;
        }

        .player-storefront-detail-property strong,
        .player-storefront-detail-feature__head span {
            color: var(--shop-accent);
            font-size: 9px;
            font-weight: 900;
        }

        .player-storefront-detail-property p,
        .player-storefront-detail-feature p,
        .player-storefront-detail-rule p,
        .player-storefront-detail-notes {
            margin: 6px 0 0;
            color: var(--shop-text);
            font-size: 11px;
            line-height: 1.6;
            white-space: pre-line;
        }

        .player-storefront-detail-features,
        .player-storefront-detail-rule-list {
            display: grid;
            gap: 8px;
        }

        .player-storefront-detail-feature {
            position: relative;
            overflow: hidden;
        }

        .player-storefront-detail-feature::before {
            content: '';
            position: absolute;
            top: 9px;
            bottom: 9px;
            left: 0;
            width: 3px;
            border-radius: 0 999px 999px 0;
            background: var(--shop-accent);
        }

        .player-storefront-detail-rule.is-mastery {
            background: var(--shop-surface-alt);
        }

        .player-storefront-detail__footer {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            border-top: 1px solid var(--shop-border);
            background: var(--shop-surface-alt);
            padding: 11px 14px;
        }

        .player-storefront-detail__footer-price span,
        .player-storefront-detail__footer-price strong {
            display: block;
        }

        .player-storefront-detail__footer-price span {
            color: var(--shop-label);
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .player-storefront-detail__footer-price strong {
            margin-top: 2px;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 16px;
        }

        .player-storefront-detail__buy {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .player-storefront-detail__total {
            color: var(--shop-muted);
            font-size: 8px;
            text-align: right;
        }

        .player-storefront-detail__total strong {
            display: block;
            margin-top: 1px;
            color: var(--shop-title);
            font-family: Georgia, serif;
            font-size: 11px;
        }

        .player-storefront-buy-button.is-detail {
            height: 38px;
            padding: 0 15px;
            font-size: 9px;
        }

        .player-storefront-detail__soldout {
            border: 1px solid var(--shop-border);
            border-radius: 10px;
            background: var(--shop-inset);
            padding: 9px 12px;
            color: var(--shop-muted);
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
        }

        @media (max-width: 900px) {
            .player-storefront-scene {
                height: 184px;
            }

            .player-storefront-scene__title {
                width: min(410px, calc(100% - 170px));
            }

            .player-storefront-scene__keeper {
                top: 7px;
                right: 28%;
                bottom: 20px;
                left: 28%;
            }

            .player-storefront-counter-top {
                bottom: 17px;
                height: 9px;
            }

            .player-storefront-counter-front {
                height: 20px;
            }

            .player-storefront-detail__hero {
                grid-template-columns: 120px minmax(0, 1fr);
            }

            .player-storefront-detail__image {
                width: 120px;
                height: 120px;
            }

            .player-storefront-detail__commerce {
                grid-column: 1 / -1;
            }

            .player-storefront-detail-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .player-storefront-scene {
                height: 160px;
            }

            .player-storefront-scene__title {
                top: 10px;
                left: 10px;
                width: calc(100% - 105px);
                min-width: 0;
                padding: 9px 10px;
            }

            .player-storefront-scene__title h2 {
                font-size: 21px;
            }

            .player-storefront-scene__description,
            .player-storefront-scene__title > p:not(.player-storefront-scene__description) {
                display: none;
            }

            .player-storefront-scene__actions {
                top: 9px;
                right: 9px;
                gap: 5px;
            }

            .player-storefront-scene .player-storefront-wallet {
                display: none;
            }

            .player-storefront-scene-action {
                width: 36px;
                height: 36px;
            }

            .player-storefront-scene__keeper {
                top: 25px;
                right: 23%;
                bottom: 18px;
                left: 23%;
            }

            .player-storefront-counter-top {
                bottom: 15px;
                height: 9px;
            }

            .player-storefront-counter-front {
                bottom: 0;
                height: 19px;
            }

            .player-storefront-detail-layer {
                padding: 0;
            }

            .player-storefront-detail {
                width: 100%;
                height: 100%;
                max-height: none;
                border: 0;
                border-radius: 0;
            }

            .player-storefront-detail__scroll {
                padding: 13px;
            }

            .player-storefront-detail__hero {
                grid-template-columns: 90px minmax(0, 1fr);
                gap: 12px;
            }

            .player-storefront-detail__image {
                width: 90px;
                height: 90px;
            }

            .player-storefront-detail__identity h2 {
                font-size: 22px;
            }

            .player-storefront-detail__commerce {
                grid-column: 1 / -1;
            }

            .player-storefront-detail-stats,
            .player-storefront-detail-property-grid {
                grid-template-columns: 1fr;
            }

            .player-storefront-detail__footer {
                align-items: stretch;
                flex-direction: column;
            }

            .player-storefront-detail__buy {
                width: 100%;
                justify-content: space-between;
            }
        }


        /* ==========================================================
           LOJA DO PLAYER — FICHA EDITORIAL DO ITEM
           Espelha a linguagem visual do modal do Inventário.
           ========================================================== */

        .player-storefront-detail-layer {
            padding: 18px;
        }

        .player-storefront-detail-backdrop {
            background: rgba(11, 8, 6, .76);
            backdrop-filter: blur(7px);
        }

        .player-storefront-detail {
            width: min(860px, 100%);
            max-height: calc(100% - 8px);
            border: 1px solid rgba(176,140,98,.58);
            border-radius: 16px;
            background:
                linear-gradient(
                    180deg,
                    #fbf8f1 0%,
                    #f8f2e8 100%
                );
            color: #4f3427;
            box-shadow:
                0 28px 80px rgba(43,29,23,.30),
                inset 0 1px 0 rgba(255,255,255,.82);
        }

        .player-storefront-detail__topbar {
            min-height: 52px;
            border-bottom: 1px solid rgba(160,119,77,.30);
            background:
                linear-gradient(
                    180deg,
                    #f1e5d6 0%,
                    #eadbc8 100%
                );
            padding: 9px 13px 9px 16px;
        }

        .player-storefront-detail__topbar > span {
            color: #7d604d;
            font-size: 8.5px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .player-storefront-detail__topbar > button {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 9px;
            background: rgba(255,253,248,.52);
            color: #7d604d;
            font-size: 21px;
            line-height: 1;
        }

        .player-storefront-detail__topbar > button:hover {
            background: rgba(107,29,20,.07);
            color: #6b1d14;
        }

        .player-storefront-detail__scroll {
            padding: 24px 28px 28px;
            scrollbar-color: rgba(140,98,57,.42) transparent;
        }

        .player-storefront-detail__hero {
            display: grid;
            grid-template-columns: 158px minmax(0, 1fr);
            gap: 20px;
            align-items: center;
        }

        .player-storefront-detail__image {
            display: flex;
            width: 158px;
            height: 158px;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            overflow: hidden;
            border: 1px solid rgba(176,140,98,.42);
            border-radius: 12px;
            background:
                linear-gradient(
                    180deg,
                    #fffdf8 0%,
                    #f2eadf 100%
                );
            padding: 12px;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.76),
                0 5px 18px rgba(83,21,15,.05);
        }

        .player-storefront-detail__image img {
            display: block;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            object-position: center center;
        }

        .player-storefront-detail__identity {
            align-self: stretch;
        }

        .player-storefront-detail__title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .player-storefront-detail__kicker {
            display: block;
            margin-bottom: 4px;
            color: rgba(140,98,57,.78);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .player-storefront-detail__identity h2 {
            margin: 0;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 30px;
            font-weight: 900;
            line-height: 1.02;
        }

        .player-storefront-detail__stock-status {
            display: inline-flex;
            min-height: 25px;
            flex: 0 0 auto;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(107,29,20,.16);
            border-radius: 999px;
            background: rgba(107,29,20,.055);
            padding: 0 8px;
            color: #6b1d14;
            font-size: 7.5px;
            font-weight: 900;
            letter-spacing: .055em;
            text-transform: uppercase;
        }

        .player-storefront-detail__stock-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #6b1d14;
        }

        .player-storefront-detail__classification {
            display: block;
            margin-top: 7px;
            color: #6b5548;
            font-size: 13px;
            font-weight: 700;
            font-style: italic;
            line-height: 1.4;
        }

        .player-storefront-editorial-mechanics {
            margin-top: 10px;
            max-width: 100%;
            color: #5f4636;
        }

        .player-storefront-weapon-line {
            font-family: Georgia, serif;
            font-size: 12.5px;
            font-weight: 700;
            font-style: italic;
            line-height: 1.45;
            color: #6b5548;
        }

        .player-storefront-mechanic-line {
            margin-top: 4px;
            color: #6b5548;
            font-size: 10.5px;
            line-height: 1.5;
        }

        .player-storefront-mechanic-line > strong {
            margin-right: 4px;
            color: #8c6239;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .065em;
            text-transform: uppercase;
        }

        .player-storefront-inline-tooltip {
            position: relative;
            display: inline;
            cursor: help;
        }

        .player-storefront-inline-term {
            display: inline;
            padding: 0;
            border: 0;
            background: transparent;
            color: inherit;
            font: inherit;
            line-height: inherit;
            text-decoration: underline dotted rgba(140,98,57,.38);
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
            outline: none;
            cursor: help;
        }

        .player-storefront-inline-term:focus-visible {
            border-radius: 2px;
            outline: 1px solid rgba(140,98,57,.38);
            outline-offset: 2px;
        }

        .player-storefront-inline-tooltip-panel {
            position: absolute;
            left: 0;
            bottom: calc(100% + 8px);
            z-index: 100;
            display: block;
            width: min(290px, 72vw);
            max-width: 290px;
            visibility: hidden;
            border: 1px solid rgba(176,140,98,.58);
            border-radius: 8px;
            background: #fbf8f1;
            padding: 9px 10px;
            opacity: 0;
            box-shadow:
                0 10px 28px rgba(43,29,23,.18),
                inset 0 1px 0 rgba(255,255,255,.74);
            pointer-events: none;
            transform: translateY(3px);
            transition:
                opacity .12s ease,
                transform .12s ease,
                visibility .12s ease;
            white-space: normal;
            text-align: left;
        }

        .player-storefront-inline-tooltip:hover
        .player-storefront-inline-tooltip-panel,
        .player-storefront-inline-tooltip:focus-within
        .player-storefront-inline-tooltip-panel {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .player-storefront-inline-tooltip-panel::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 14px;
            width: 8px;
            height: 8px;
            transform: translateY(-4px) rotate(45deg);
            border-right: 1px solid rgba(176,140,98,.58);
            border-bottom: 1px solid rgba(176,140,98,.58);
            background: #fbf8f1;
        }

        .player-storefront-inline-tooltip-title {
            display: block;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 13px;
            font-weight: 900;
            line-height: 1.3;
        }

        .player-storefront-inline-tooltip-text {
            display: block;
            margin-top: 4px;
            color: #5f4636;
            font-size: 11px;
            line-height: 1.55;
        }

        .player-storefront-detail-section {
            margin-top: 24px;
            border-top: 0;
            padding-top: 0;
        }

        .player-storefront-detail-section__title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #8c6239;
            font-family: inherit;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .095em;
            text-transform: uppercase;
        }

        .player-storefront-detail-section__title::after {
            content: '';
            height: 1px;
            flex: 1 1 auto;
            background: rgba(176,140,98,.34);
        }

        .player-storefront-detail-description,
        .player-storefront-detail-notes {
            margin: 0;
            color: #4f3427;
            font-size: 14px;
            line-height: 1.78;
            white-space: pre-line;
        }

        .player-storefront-detail-custom-list,
        .player-storefront-detail-modifier-list {
            display: grid;
            gap: 10px;
        }

        .player-storefront-detail-custom-row,
        .player-storefront-detail-modifier-row {
            color: #5f3a27;
            font-size: 12px;
            line-height: 1.65;
        }

        .player-storefront-detail-custom-row__head {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 7px;
        }

        .player-storefront-detail-custom-row h3,
        .player-storefront-detail-modifier-row h3 {
            margin: 0;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 15px;
            font-weight: 900;
        }

        .player-storefront-detail-custom-row strong {
            color: #8c6239;
            font-size: 11px;
        }

        .player-storefront-detail-custom-row p,
        .player-storefront-detail-modifier-row p {
            margin: 2px 0 0;
            white-space: pre-line;
        }

        .player-storefront-detail-features {
            display: grid;
            gap: 11px;
        }

        .player-storefront-detail-feature {
            overflow: hidden;
            border: 1px solid rgba(176,140,98,.34);
            border-radius: 11px;
            background: #fffdf8;
            padding: 0;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.68);
        }

        .player-storefront-detail-feature::before {
            display: none;
        }

        .player-storefront-detail-feature__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px solid rgba(188,154,111,.28);
            background: #f0e4d5;
            padding: 9px 11px;
        }

        .player-storefront-detail-feature h3 {
            margin: 0;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.25;
        }

        .player-storefront-detail-feature__head span {
            color: #8c6239;
            font-size: 9px;
            font-weight: 900;
        }

        .player-storefront-detail-feature p {
            margin: 0;
            padding: 10px 12px 12px;
            color: #4f3427;
            font-size: 13px;
            line-height: 1.72;
            white-space: pre-line;
        }

        .player-storefront-detail__footer {
            min-height: 62px;
            border-top: 1px solid rgba(160,119,77,.30);
            background:
                linear-gradient(
                    180deg,
                    #f1e5d6 0%,
                    #eadbc8 100%
                );
            padding: 9px 14px;
        }

        .player-storefront-detail__footer-price span {
            color: #7d604d;
        }

        .player-storefront-detail__footer-price strong {
            color: #53150f;
            font-size: 18px;
        }

        .player-storefront-detail__total {
            color: #7d604d;
        }

        .player-storefront-detail__total strong {
            color: #53150f;
        }

        .player-storefront-detail__soldout {
            border-color: rgba(176,140,98,.42);
            background: rgba(255,253,248,.54);
            color: #7d604d;
        }

        @media (max-width: 720px) {
            .player-storefront-detail-layer {
                padding: 0;
            }

            .player-storefront-detail {
                width: 100%;
                height: 100%;
                max-height: none;
                border: 0;
                border-radius: 0;
            }

            .player-storefront-detail__scroll {
                padding: 16px;
            }

            .player-storefront-detail__hero {
                grid-template-columns: 105px minmax(0, 1fr);
                gap: 13px;
            }

            .player-storefront-detail__image {
                width: 105px;
                height: 105px;
            }

            .player-storefront-detail__identity h2 {
                font-size: 23px;
            }

            .player-storefront-detail__stock-status {
                display: none;
            }

            .player-storefront-detail__footer {
                align-items: stretch;
                flex-direction: column;
            }

            .player-storefront-detail__buy {
                width: 100%;
                justify-content: space-between;
            }
        }


        /* ==========================================================
           CONFIRMAÇÃO DE COMPRA / PAGAMENTO EM MOEDAS
           ========================================================== */

        .player-storefront-purchase-layer {
            position: fixed;
            inset: -1px;
            z-index: 420;
            display: grid;
            place-items: center;
            overflow: hidden;
            border-radius: inherit;
            padding: 18px;
        }

        .player-storefront-purchase-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(11, 8, 6, .88);
            backdrop-filter: blur(9px);
        }

        .player-storefront-purchase {
            position: relative;
            z-index: 1;
            width: min(720px, 100%);
            max-height: min(760px, calc(100vh - 40px));
            overflow-y: auto;
            border: 1px solid rgba(176,140,98,.58);
            border-radius: 18px;
            background:
                linear-gradient(
                    180deg,
                    #fbf8f1 0%,
                    #f8f2e8 100%
                );
            color: #4f3427;
            box-shadow:
                0 30px 90px rgba(20,12,8,.38),
                inset 0 1px 0 rgba(255,255,255,.82);
        }

        .player-storefront-purchase__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid rgba(176,140,98,.28);
            padding: 18px 20px 16px;
        }

        .player-storefront-purchase__eyebrow {
            margin: 0 0 4px;
            color: #8c6239;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .player-storefront-purchase__head h3 {
            margin: 0;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.08;
        }

        .player-storefront-purchase__head p {
            margin: 6px 0 0;
            color: #6b5548;
            font-size: 12px;
            line-height: 1.55;
        }

        .player-storefront-purchase__close {
            display: grid;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            place-items: center;
            border: 1px solid rgba(176,140,98,.38);
            border-radius: 10px;
            background: #fffdf8;
            color: #7d604d;
            font-size: 20px;
            line-height: 1;
        }

        .player-storefront-purchase__summary {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 16px;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(176,140,98,.24);
            background: rgba(239,228,213,.42);
        }

        .player-storefront-purchase__summary small,
        .player-storefront-payment-status small {
            display: block;
            margin-bottom: 3px;
            color: #9a795b;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .player-storefront-purchase__summary strong {
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 17px;
        }

        .player-storefront-purchase__price {
            text-align: right;
        }

        .player-storefront-purchase__body {
            padding: 18px 20px 20px;
        }

        .player-storefront-purchase__wallet {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            border: 1px solid rgba(176,140,98,.24);
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255,255,255,.68) 0%, rgba(248,241,229,.88) 100%);
            padding: 12px 14px;
            color: #6b5548;
            font-size: 11px;
        }

        .player-storefront-purchase__wallet strong {
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 14px;
        }

        .player-storefront-payment-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 12px;
        }

        .player-storefront-payment-action {
            min-height: 32px;
            border: 1px solid rgba(176,140,98,.42);
            border-radius: 9px;
            background: #fffdf8;
            padding: 0 10px;
            color: #6b1d14;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .player-storefront-payment-action:hover {
            background: #f1e5d6;
        }

        .player-storefront-payment-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }

        .player-storefront-payment-coin {
            min-width: 0;
            border: 1px solid rgba(176,140,98,.34);
            border-radius: 14px;
            background: linear-gradient(180deg, #fffefb 0%, #f7efe2 100%);
            padding: 11px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.78);
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .player-storefront-payment-coin.is-active {
            border-color: rgba(107, 29, 20, .42);
            box-shadow: 0 8px 18px rgba(107,29,20,.08), inset 0 1px 0 rgba(255,255,255,.82);
            transform: translateY(-1px);
        }

        .player-storefront-payment-coin.is-unavailable {
            border-color: rgba(163,61,53,.34);
            background: linear-gradient(180deg, #fff6f5 0%, #f7ece9 100%);
        }

        .player-storefront-payment-coin__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .player-storefront-payment-coin__identity {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .player-storefront-payment-coin__meta {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 1px;
        }

        .player-storefront-payment-coin__head strong {
            color: #53150f;
            font-size: 11px;
            font-weight: 900;
            line-height: 1;
        }

        .player-storefront-payment-coin__head span {
            color: #9a795b;
            font-size: 8px;
            font-weight: 800;
            white-space: nowrap;
        }

        .player-storefront-payment-coin__badge {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            border: 1px solid rgba(176,140,98,.24);
            border-radius: 999px;
            background: rgba(255,255,255,.68);
            padding: 0 8px;
            color: #8c6239;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .02em;
        }

        .player-storefront-payment-coin.is-unavailable .player-storefront-payment-coin__badge,
        .player-storefront-payment-coin.is-unavailable .player-storefront-payment-coin__head span,
        .player-storefront-payment-coin.is-unavailable small {
            color: #a33d35;
        }

        .player-storefront-payment-coin small {
            display: block;
            margin-top: 7px;
            color: #9a795b;
            font-size: 8px;
        }

        .player-storefront-payment-coin__icon {
            display: flex;
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            align-items: center;
            justify-content: center;
            border: 1px solid currentColor;
            border-radius: 999px;
        }

        .player-storefront-payment-coin__icon svg {
            width: 22px;
            height: 22px;
        }

        .player-storefront-payment-coin__icon circle,
        .player-storefront-payment-coin__icon path {
            stroke: currentColor;
            stroke-width: 1.25;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .player-storefront-payment-coin__icon.coin-copper {
            background: #f3ddcf;
            color: #a4512d;
        }

        .player-storefront-payment-coin__icon.coin-silver {
            background: #ececea;
            color: #777a7d;
        }

        .player-storefront-payment-coin__icon.coin-electrum {
            background: #eee7c8;
            color: #8f7e38;
        }

        .player-storefront-payment-coin__icon.coin-gold {
            background: #f5e3ad;
            color: #b17812;
        }

        .player-storefront-payment-coin__icon.coin-platinum {
            background: #edf0ed;
            color: #7b8582;
        }

        .player-storefront-payment-coin input {
            width: 100%;
            height: 40px;
            margin-top: 9px;
            border: 1px solid rgba(176,140,98,.44);
            border-radius: 10px;
            background: #fbf8f1;
            padding: 0 10px;
            text-align: center;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 16px;
            font-weight: 900;
            outline: none;
            box-shadow: inset 0 1px 3px rgba(69,39,22,.06);
        }

        .player-storefront-payment-coin.is-unavailable input {
            border-color: rgba(163,61,53,.34);
            background: #fff8f7;
            color: #a33d35;
        }

        .player-storefront-payment-coin input:focus {
            border-color: #8c6239;
            box-shadow: 0 0 0 3px rgba(140,98,57,.08);
        }

        .player-storefront-payment-status {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 9px;
            margin-top: 10px;
        }

        .player-storefront-payment-status > div {
            min-width: 0;
            border-top: 1px solid rgba(176,140,98,.28);
            padding-top: 10px;
        }

        .player-storefront-payment-status strong {
            display: block;
            overflow-wrap: anywhere;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 13px;
            line-height: 1.35;
        }

        .player-storefront-payment-status .is-missing strong {
            color: #a33d35;
        }

        .player-storefront-payment-status .is-change strong {
            color: #39705b;
        }

        .player-storefront-payment-preview {
            margin-top: 13px;
            border-radius: 10px;
            background: rgba(239,228,213,.48);
            padding: 10px 12px;
            color: #6b5548;
            font-size: 10px;
            line-height: 1.5;
        }

        .player-storefront-payment-preview strong {
            color: #53150f;
        }

        .player-storefront-payment-error {
            margin-top: 12px;
            border: 1px solid rgba(173,65,55,.24);
            border-radius: 10px;
            background: rgba(173,65,55,.06);
            padding: 9px 11px;
            color: #943d35;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.45;
        }

        .player-storefront-purchase__footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            border-top: 1px solid rgba(176,140,98,.28);
            padding: 13px 20px;
            background: #f0e4d5;
        }

        .player-storefront-purchase__cancel,
        .player-storefront-purchase__confirm {
            min-height: 39px;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .player-storefront-purchase__cancel {
            border: 1px solid rgba(176,140,98,.46);
            background: #fffdf8;
            color: #7d604d;
        }

        .player-storefront-purchase__confirm {
            border: 1px solid #6b1d14;
            background: #6b1d14;
            color: #fff;
        }

        .player-storefront-purchase__confirm:disabled {
            cursor: not-allowed;
            opacity: .42;
        }

        @media (max-width: 720px) {
            .player-storefront-purchase-layer {
                padding: 10px;
            }

            .player-storefront-purchase {
                max-height: calc(100vh - 20px);
            }

            .player-storefront-payment-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .player-storefront-payment-status {
                grid-template-columns: 1fr;
            }

            .player-storefront-purchase__summary {
                grid-template-columns: 1fr;
            }

            .player-storefront-purchase__price {
                text-align: left;
            }
        }

    

        /* ==========================================================
           SHOP V8 — NEGOCIAÇÃO EM DUAS COLUNAS
           ========================================================== */

        .player-storefront-purchase-v8 {
            display: flex;
            width: min(1064px, 100%);
            height: auto;
            min-height: 0;
            max-height: min(560px, calc(100dvh - 34px));
            flex-direction: column;
            overflow: hidden;
        }

        .player-storefront-purchase-v8__head {
            align-items: center;
            flex: 0 0 auto;
            padding: 16px 18px 14px 20px;
        }

        .player-storefront-purchase-v8__head .player-storefront-purchase__eyebrow {
            margin-bottom: 2px;
        }

        .player-storefront-purchase-v8__head h3 {
            font-size: 24px;
        }

        .player-storefront-purchase-v8__layout {
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            min-height: 0;
            flex: 0 1 auto;
        }

        .player-storefront-purchase-v8__product {
            position: relative;
            display: flex;
            min-width: 0;
            flex-direction: column;
            border-right: 1px solid rgba(176,140,98,.28);
            background:
                radial-gradient(circle at 50% 8%, rgba(255,255,255,.85), transparent 35%),
                linear-gradient(180deg, #f6eadb 0%, #ead7bf 100%);
            padding: 18px 22px 18px;
        }

        .player-storefront-purchase-v8__product::after {
            content: '';
            position: absolute;
            right: 18px;
            bottom: 63px;
            left: 18px;
            height: 1px;
            background: rgba(140,98,57,.18);
        }

        .player-storefront-purchase-v8__product-image {
            display: grid;
            width: 182px;
            height: 182px;
            place-items: center;
            align-self: center;
            overflow: hidden;
            border: 1px solid rgba(176,140,98,.38);
            border-radius: 22px;
            background: rgba(255,253,249,.76);
            padding: 18px;
            box-shadow:
                0 14px 28px rgba(83,21,15,.07),
                inset 0 1px 0 rgba(255,255,255,.9);
        }

        .player-storefront-purchase-v8__product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center center;
        }

        .player-storefront-purchase-v8__product-image > span {
            color: #9a795b;
            font-family: Georgia, serif;
            font-size: 42px;
        }

        .player-storefront-purchase-v8__product-copy {
            margin-top: 13px;
            text-align: center;
        }

        .player-storefront-purchase-v8__classification,
        .player-storefront-purchase-v8__section-label {
            display: block;
            color: #9a795b;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .player-storefront-purchase-v8__product-copy h4 {
            margin: 5px 0 0;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.05;
        }

        .player-storefront-purchase-v8__product-copy p {
            display: -webkit-box;
            overflow: hidden;
            margin: 8px 0 0;
            color: #6b5548;
            font-size: 10px;
            line-height: 1.5;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        .player-storefront-purchase-v8__product-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 22px;
            padding-top: 15px;
        }

        .player-storefront-purchase-v8__product-meta > div {
            min-width: 0;
        }

        .player-storefront-purchase-v8__product-meta small {
            display: block;
            color: #9a795b;
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .player-storefront-purchase-v8__product-meta strong {
            display: block;
            margin-top: 3px;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 17px;
            font-weight: 900;
        }

        .player-storefront-purchase-v8__product-meta .is-price {
            text-align: right;
        }

        .player-storefront-purchase-v8__deal {
            display: flex;
            min-width: 0;
            min-height: 0;
            height: 100%;
            align-self: stretch;
            flex-direction: column;
            overflow-y: auto;
            background:
                radial-gradient(circle at 100% 0%, rgba(140,98,57,.055), transparent 34%),
                #fbf8f1;
            padding: 17px 22px 0;
        }

        .player-storefront-purchase-v8__deal-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .player-storefront-purchase-v8__deal-top h4 {
            margin: 3px 0 0;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 23px;
            font-weight: 900;
        }

        .player-storefront-purchase-v8__wallet-pill {
            min-width: 210px;
            border: 1px solid rgba(176,140,98,.26);
            border-radius: 12px;
            background: rgba(255,255,255,.64);
            padding: 9px 11px;
            text-align: right;
        }

        .player-storefront-purchase-v8__wallet-pill span,
        .player-storefront-purchase-v8__wallet-pill strong {
            display: block;
        }

        .player-storefront-purchase-v8__wallet-pill span {
            color: #9a795b;
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .player-storefront-purchase-v8__wallet-pill strong {
            margin-top: 2px;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 13px;
        }

        .player-storefront-purchase-v8__actions {
            margin: 14px 0 10px;
        }

        .player-storefront-payment-action.is-primary {
            border-color: rgba(107,29,20,.22);
            background: rgba(107,29,20,.08);
        }

        .player-storefront-payment-grid-v8 {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
        }

        .player-storefront-payment-coin-v8 {
            padding: 9px;
            border-radius: 13px;
        }

        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__head {
            align-items: flex-start;
        }

        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__identity {
            gap: 6px;
        }

        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__icon {
            width: 29px;
            height: 29px;
            flex-basis: 29px;
        }

        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__icon svg {
            width: 20px;
            height: 20px;
        }

        .player-storefront-payment-coin-v8 .player-storefront-payment-coin__badge {
            min-height: 22px;
            padding: 0 7px;
            font-size: 8px;
        }

        .player-storefront-payment-coin-v8__stepper {
            display: grid;
            grid-template-columns: 29px minmax(0, 1fr) 29px;
            gap: 4px;
            margin-top: 9px;
        }

        .player-storefront-payment-coin-v8__stepper button,
        .player-storefront-payment-coin-v8__stepper input {
            height: 34px;
            border: 1px solid rgba(176,140,98,.38);
            background: #fffdf8;
            color: #53150f;
        }

        .player-storefront-payment-coin-v8__stepper button {
            border-radius: 9px;
            font-size: 16px;
            font-weight: 900;
            cursor: pointer;
        }

        .player-storefront-payment-coin-v8__stepper button:disabled {
            cursor: not-allowed;
            opacity: .32;
        }

        .player-storefront-payment-coin-v8__stepper input {
            width: 100%;
            min-width: 0;
            margin: 0;
            border-radius: 9px;
            padding: 0 3px;
            text-align: center;
            font-family: Georgia, serif;
            font-size: 14px;
            font-weight: 900;
            outline: none;
        }

        .player-storefront-payment-coin-v8 {
            position: relative;
            overflow: hidden;
        }

        .player-storefront-payment-coin-v8.is-unavailable {
            opacity: 1;
            border-color: rgba(173, 65, 55, .34);
            background:
                linear-gradient(
                    180deg,
                    rgba(173, 65, 55, .09) 0%,
                    rgba(173, 65, 55, .055) 100%
                ),
                #fffaf7;
        }

        .player-storefront-payment-coin-v8.is-unavailable::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 3;
            background: rgba(173, 65, 55, .035);
            pointer-events: none;
        }

        .player-storefront-payment-coin-v8.is-unavailable::after {
            content: 'SEM MOEDAS';
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 5;
            transform: translate(-50%, -50%);
            display: inline-flex;
            min-height: 25px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(173, 65, 55, .34);
            border-radius: 999px;
            background: rgba(255, 246, 244, .94);
            padding: 0 9px;
            color: #a33d35;
            box-shadow: 0 5px 14px rgba(123, 43, 36, .09);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .075em;
            line-height: 1;
            white-space: nowrap;
            pointer-events: none;
        }

        .player-storefront-payment-coin-v8.is-unavailable .player-storefront-payment-coin__identity,
        .player-storefront-payment-coin-v8.is-unavailable .player-storefront-payment-coin-v8__stepper {
            opacity: .30;
        }

        .player-storefront-payment-coin-v8.is-unavailable .player-storefront-payment-coin__badge {
            visibility: hidden;
            opacity: 0;
        }

        .player-storefront-payment-status-v8 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 13px;
        }

        .player-storefront-payment-status-v8 > div {
            border: 1px solid rgba(176,140,98,.22);
            border-radius: 11px;
            background: rgba(255,255,255,.46);
            padding: 9px 10px;
        }

        .player-storefront-payment-status-v8 > div.is-missing {
            border-color: rgba(163,61,53,.24);
            background: rgba(163,61,53,.045);
        }

        .player-storefront-payment-status-v8 > div.is-change {
            border-color: rgba(57,112,91,.24);
            background: rgba(57,112,91,.045);
        }

        .player-storefront-payment-preview-v8 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
            border: 1px solid rgba(176,140,98,.18);
            background: rgba(239,228,213,.40);
        }

        .player-storefront-payment-preview-v8 > span {
            color: #9a795b;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .player-storefront-purchase-v8__footer {
            position: relative;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 4;
            flex: 0 0 auto;
            margin: auto -22px 0;
            border-top: 1px solid rgba(176,140,98,.28);
            background:
                linear-gradient(
                    180deg,
                    #f1e5d5 0%,
                    #ead8c2 100%
                );
            padding: 12px 22px;
            box-shadow:
                0 -1px 0 rgba(255,255,255,.62),
                0 -8px 18px rgba(82,45,26,.04);
        }

        .player-storefront-purchase-v8__footer .player-storefront-purchase__confirm {
            min-width: 170px;
            min-height: 42px;
            font-size: 10px;
        }

        .player-storefront-purchase-v8__footer .player-storefront-purchase__cancel {
            min-height: 42px;
            font-size: 10px;
        }


        /* ==========================================================
           SHOP V9 — NEGOCIAÇÃO MAIS LIMPA
           ========================================================== */

        .player-storefront-purchase-v8__deal-top {
            align-items: center;
            gap: 12px;
        }

        .player-storefront-purchase-v8__deal-top h4 {
            font-size: 23px;
        }

        .player-storefront-purchase-v8__wallet-pill {
            display: none;
        }

        .player-storefront-purchase-v8__actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
        }

        .player-storefront-purchase-v8__actions .player-storefront-payment-action {
            min-height: 31px;
            border-radius: 8px;
            padding: 0 10px;
            font-size: 8px;
            letter-spacing: .055em;
        }

        .player-storefront-purchase-v8__actions .player-storefront-payment-action.is-primary {
            border-color: rgba(107,29,20,.20);
            background: rgba(107,29,20,.075);
        }

        .player-storefront-purchase-v8__actions .player-storefront-payment-action:hover {
            transform: translateY(-1px);
        }

        .player-storefront-payment-grid-v8 {
            margin-top: 10px;
        }

        .player-storefront-payment-status-v9 {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr);
            gap: 9px;
            margin-top: 10px;
        }

        .player-storefront-payment-settlement {
            position: relative;
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(176,140,98,.24);
            border-radius: 13px;
            background: linear-gradient(180deg, rgba(255,255,255,.72), rgba(247,239,227,.74));
            padding: 11px 13px 12px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        }

        .player-storefront-payment-settlement::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 3px;
            background: rgba(140,98,57,.55);
        }

        .player-storefront-payment-settlement.is-change {
            border-color: rgba(57,112,91,.26);
            background: linear-gradient(180deg, rgba(247,252,249,.9), rgba(235,245,239,.78));
        }

        .player-storefront-payment-settlement.is-change::before {
            background: #39705b;
        }

        .player-storefront-payment-settlement.is-missing {
            border-color: rgba(163,61,53,.26);
            background: linear-gradient(180deg, rgba(255,248,247,.92), rgba(248,235,233,.78));
        }

        .player-storefront-payment-settlement.is-missing::before {
            background: #a33d35;
        }

        .player-storefront-payment-settlement.is-even::before {
            background: #7f765f;
        }

        .player-storefront-payment-settlement__label {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #9a795b;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .player-storefront-payment-settlement__label svg {
            width: 13px;
            height: 13px;
            flex: 0 0 13px;
            stroke: currentColor;
        }

        .player-storefront-payment-settlement strong {
            display: block;
            margin-top: 5px;
            overflow-wrap: anywhere;
            color: #53150f;
            font-family: Georgia, serif;
            font-size: 16px;
            font-weight: 900;
            line-height: 1.15;
        }

        .player-storefront-payment-settlement.is-change strong {
            color: #39705b;
        }

        .player-storefront-payment-settlement.is-missing strong {
            color: #a33d35;
        }

        .player-storefront-payment-settlement small {
            display: block;
            margin-top: 3px;
            color: #8c6e57;
            font-size: 9px;
            line-height: 1.4;
        }

        .player-storefront-payment-preview-v8 {
            margin-top: 9px;
            border-radius: 12px;
            padding: 9px 12px;
        }

        @media (max-width: 900px) {
            .player-storefront-purchase-v8__layout {
                grid-template-columns: 245px minmax(0, 1fr);
            }

            .player-storefront-purchase-v8__product-image {
                width: 155px;
                height: 155px;
            }

            .player-storefront-payment-grid-v8 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .player-storefront-purchase-v8 {
                width: 100%;
                height: 100%;
                min-height: 0;
                max-height: none;
                border: 0;
                border-radius: 0;
            }

            .player-storefront-purchase-v8__layout {
                grid-template-columns: 1fr;
                overflow-y: auto;
            }

            .player-storefront-purchase-v8__product {
                border-right: 0;
                border-bottom: 1px solid rgba(176,140,98,.28);
                padding: 16px;
            }

            .player-storefront-purchase-v8__product-image {
                width: 120px;
                height: 120px;
            }

            .player-storefront-purchase-v8__product-copy {
                margin-top: 10px;
            }

            .player-storefront-purchase-v8__product-copy p {
                display: none;
            }

            .player-storefront-purchase-v8__product-meta {
                margin-top: 12px;
            }

            .player-storefront-purchase-v8__deal {
                overflow: visible;
                padding: 16px 14px 0;
            }

            .player-storefront-purchase-v8__deal-top {
                flex-direction: column;
            }

            .player-storefront-purchase-v8__wallet-pill {
                width: 100%;
                min-width: 0;
                text-align: left;
            }

            .player-storefront-payment-grid-v8 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .player-storefront-purchase-v8__footer {
                margin-right: -14px;
                margin-left: -14px;
                padding-right: 14px;
                padding-left: 14px;
            }
        }
</style>

    <script>
        window.characterShopStorefrontV2 = function (config) {
            return {
                open: false,
                loading: false,
                loaded: false,
                error: null,

                shops: [],
                activeShopId: null,

                quantities: {},
                busyItemId: null,

                itemSearch: '',
                stockFilter: 'available',
                sortMode: 'price_asc',

                detailOpen: false,
                detailItem: null,

                categoryDrawerKey: null,

                purchaseOpen: false,
                purchaseItem: null,
                purchaseQuantity: 1,
                paymentError: null,
                payment: {
                    copper: 0,
                    silver: 0,
                    electrum: 0,
                    gold: 0,
                    platinum: 0,
                },

                notice: null,
                noticeError: false,
                noticeTimer: null,

                wallet: {
                    copper: 0,
                    silver: 0,
                    electrum: 0,
                    gold: 0,
                    platinum: 0,
                },

                get activeShop() {
                    return this.shops.find(
                        shop =>
                            parseInt(shop?.id)
                            ===
                            parseInt(this.activeShopId)
                    ) ?? this.shops[0] ?? null;
                },

                get activeItems() {
                    return Array.isArray(
                        this.activeShop?.items
                    )
                        ? this.activeShop.items
                        : [];
                },

                get visibleItems() {
                    const normalize =
                        value =>
                            String(value ?? '')
                                .normalize('NFD')
                                .replace(
                                    /[\u0300-\u036f]/g,
                                    ''
                                )
                                .toLocaleLowerCase(
                                    'pt-BR'
                                )
                                .trim();

                    const query =
                        normalize(
                            this.itemSearch
                        );

                    const filtered =
                        this.activeItems
                            .filter(item => {
                                if (!query) {
                                    return true;
                                }

                                return normalize(
                                    [
                                        item?.name,
                                        item?.description,
                                        item?.type,
                                        item?.rarity_label,
                                    ]
                                        .filter(Boolean)
                                        .join(' ')
                                ).includes(query);
                            });

                    return [...filtered]
                        .sort((a, b) => {
                            if (
                                this.sortMode
                                ===
                                'price_desc'
                            ) {
                                return (
                                    this.priceCopperValue(b)
                                    -
                                    this.priceCopperValue(a)
                                );
                            }

                            if (
                                this.sortMode
                                ===
                                'name'
                            ) {
                                return String(
                                    a?.name ?? ''
                                ).localeCompare(
                                    String(
                                        b?.name ?? ''
                                    ),
                                    'pt-BR'
                                );
                            }

                            return (
                                this.priceCopperValue(a)
                                -
                                this.priceCopperValue(b)
                            );
                        });
                },

                isConsumableItem(item) {
                    const properties =
                        this.detailProperties(item);

                    const itemUsage =
                        properties?.usage
                        &&
                        typeof properties.usage === 'object'
                            ? properties.usage
                            : {};

                    if (
                        String(
                            itemUsage?.mode
                            ?? ''
                        ).toLowerCase()
                        ===
                        'consumable'
                    ) {
                        return true;
                    }

                    const rawType =
                        String(
                            item?.type
                            ?? ''
                        )
                            .toLowerCase()
                            .trim();

                    if (
                        [
                            'potion',
                            'scroll',
                            'consumable',
                        ].includes(rawType)
                    ) {
                        return true;
                    }

                    return this.detailFeatures(item)
                        .some(feature => {
                            const usage =
                                feature?.usage
                                &&
                                typeof feature.usage === 'object'
                                    ? feature.usage
                                    : {};

                            const rawEnabled =
                                usage?.enabled;

                            const enabled =
                                rawEnabled === true
                                ||
                                rawEnabled === 1
                                ||
                                [
                                    '1',
                                    'true',
                                    'on',
                                    'yes',
                                ].includes(
                                    String(
                                        rawEnabled
                                        ?? ''
                                    )
                                        .toLowerCase()
                                        .trim()
                                );

                            return (
                                enabled
                                &&
                                String(
                                    usage?.recovery
                                    ?? ''
                                )
                                    .toLowerCase()
                                    .trim()
                                ===
                                'single_use'
                            );
                        });
                },

                itemStoreCategory(item) {
                    if (
                        this.isConsumableItem(
                            item
                        )
                    ) {
                        return 'consumables';
                    }

                    if (
                        !!item?.is_magical
                        ||
                        String(
                            item?.type
                            ?? ''
                        )
                            .toLowerCase()
                            .trim()
                        ===
                        'wonderful'
                    ) {
                        return 'magical';
                    }

                    return 'mundane';
                },

                get categorySections() {
                    const groups = {
                        consumables: [],
                        magical: [],
                        mundane: [],
                    };

                    for (
                        const item
                        of this.visibleItems
                    ) {
                        const key =
                            this.itemStoreCategory(
                                item
                            );

                        groups[key].push(item);
                    }

                    return [
                        {
                            key: 'consumables',
                            title: 'Consumíveis',
                            description:
                                'Poções, pergaminhos e itens de uso único.',
                            items: groups.consumables,
                        },
                        {
                            key: 'magical',
                            title: 'Itens Mágicos',
                            description:
                                'Artefatos, equipamentos e objetos encantados.',
                            items: groups.magical,
                        },
                        {
                            key: 'mundane',
                            title: 'Itens Mundanos',
                            description:
                                'Equipamentos, ferramentas e objetos comuns.',
                            items: groups.mundane,
                        },
                    ];
                },

                firstAvailableCategoryKey() {
                    return (
                        this.categorySections
                            .find(
                                section =>
                                    section.items.length > 0
                            )
                            ?.key
                        ?? null
                    );
                },

                ensureCategoryDrawer() {
                    const currentStillExists =
                        this.categorySections
                            .some(
                                section =>
                                    section.key
                                    ===
                                    this.categoryDrawerKey
                                    &&
                                    section.items.length > 0
                            );

                    if (!currentStillExists) {
                        this.categoryDrawerKey =
                            this.firstAvailableCategoryKey();
                    }
                },

                isCategoryDrawerOpen(key) {
                    return (
                        this.categoryDrawerKey
                        ===
                        key
                    );
                },

                toggleCategoryDrawer(key) {
                    this.categoryDrawerKey =
                        this.categoryDrawerKey === key
                            ? null
                            : key;
                },

                openItemDetail(item) {
                    if (!item?.id) {
                        return;
                    }

                    this.detailItem = item;
                    this.detailOpen = true;
                },

                closeItemDetail() {
                    this.detailOpen = false;
                    this.detailItem = null;
                },

                detailProperties(item) {
                    return item?.properties
                        && typeof item.properties === 'object'
                            ? item.properties
                            : {};
                },

                detailFeatures(item) {
                    const properties = this.detailProperties(item);

                    return Array.isArray(properties?.features)
                        ? properties.features.filter(
                            feature =>
                                String(feature?.title ?? '').trim()
                                || String(feature?.description ?? '').trim()
                        )
                        : [];
                },

                detailCustomProperties(item) {
                    const properties = this.detailProperties(item);

                    return Array.isArray(properties?.custom_properties)
                        ? properties.custom_properties.filter(
                            entry =>
                                String(entry?.title ?? '').trim()
                                || String(entry?.value ?? '').trim()
                                || String(entry?.description ?? '').trim()
                        )
                        : [];
                },

                detailWeapon(item) {
                    const value = this.detailProperties(item)?.weapon;

                    return value && typeof value === 'object'
                        ? value
                        : null;
                },

                detailArmor(item) {
                    const value = this.detailProperties(item)?.armor;

                    return value && typeof value === 'object'
                        ? value
                        : null;
                },

                detailShield(item) {
                    const value = this.detailProperties(item)?.shield;

                    return value && typeof value === 'object'
                        ? value
                        : null;
                },

                detailWeaponTraits(item) {
                    const weapon = this.detailWeapon(item);

                    return Array.isArray(weapon?.traits)
                        ? weapon.traits
                        : [];
                },

                detailWeaponMasteries(item) {
                    const weapon = this.detailWeapon(item);

                    return Array.isArray(weapon?.masteries)
                        ? weapon.masteries
                        : [];
                },

                detailModifiers(item) {
                    return Array.isArray(item?.modifiers)
                        ? item.modifiers
                        : [];
                },

                detailAbilityBonuses(item) {
                    const source =
                        item?.ability_bonuses
                        && typeof item.ability_bonuses === 'object'
                            ? item.ability_bonuses
                            : {};

                    const labels = {
                        strength: 'Força',
                        dexterity: 'Destreza',
                        constitution: 'Constituição',
                        intelligence: 'Inteligência',
                        wisdom: 'Sabedoria',
                        charisma: 'Carisma',
                    };

                    return Object.entries(source)
                        .map(([key, value]) => ({
                            label: labels[key] ?? String(key).toUpperCase(),
                            value: Number(value),
                        }))
                        .filter(entry =>
                            Number.isFinite(entry.value)
                            && entry.value !== 0
                        );
                },

                signedNumber(value) {
                    if (
                        value === null
                        || value === undefined
                        || value === ''
                    ) {
                        return '';
                    }

                    const number = Number(value);

                    if (!Number.isFinite(number)) {
                        return String(value);
                    }

                    return number > 0
                        ? `+${number}`
                        : String(number);
                },

                featureRecoveryLabel(usage) {
                    if (!usage || typeof usage !== 'object') {
                        return '';
                    }

                    const mode = String(
                        usage?.recovery
                        ?? usage?.recovery_type
                        ?? ''
                    );

                    const label = {
                        day: 'por dia',
                        short_rest: 'descanso curto',
                        long_rest: 'descanso longo',
                        dawn: 'ao amanhecer',
                        single_use: 'uso único',
                        custom: String(
                            usage?.recovery_custom
                            ?? usage?.custom_recovery
                            ?? 'recuperação especial'
                        ),
                    }[mode];

                    return label ?? '';
                },

                featureUsageLabel(feature) {
                    const usage = feature?.usage;

                    const enabledValue =
                        usage?.enabled;

                    const enabled =
                        enabledValue === true
                        || enabledValue === 1
                        || (
                            typeof enabledValue === 'string'
                            && [
                                '1',
                                'true',
                                'on',
                                'yes',
                            ].includes(
                                enabledValue
                                    .trim()
                                    .toLowerCase()
                            )
                        );

                    if (!enabled) {
                        return 'Passiva';
                    }

                    const max = Math.max(
                        0,
                        parseInt(usage?.max) || 0
                    );

                    const mode =
                        usage?.counter_mode === 'build'
                            ? 'Acúmulo'
                            : 'Usos';

                    const recovery =
                        this.featureRecoveryLabel(usage);

                    return [
                        max > 0
                            ? `${mode}: ${max}`
                            : mode,
                        recovery,
                    ]
                        .filter(Boolean)
                        .join(' · ');
                },

                detailClassificationLine(item) {
                    const parts = [
                        this.typeLabel(item),
                        item?.rarity_label,
                    ].filter(Boolean);

                    let text = parts.join(', ');

                    if (item?.requires_attunement) {
                        text += text
                            ? ' (Sintonizável)'
                            : 'Sintonizável';
                    }

                    return text || 'Item';
                },

                detailWeaponLine(item) {
                    const weapon = this.detailWeapon(item);

                    if (!weapon) {
                        return '';
                    }

                    const category = {
                        simple: 'Arma Simples',
                        martial: 'Arma Marcial',
                        custom: 'Arma',
                    }[String(weapon?.category ?? '').toLowerCase()]
                        ?? 'Arma';

                    const weaponType = String(
                        weapon?.weapon_type
                        ?? weapon?.category_custom
                        ?? ''
                    ).trim();

                    const damage = weapon?.damage;
                    let damageText = '';

                    if (
                        damage
                        && typeof damage === 'object'
                        && damage?.die
                    ) {
                        const dice = `${Math.max(1, parseInt(damage?.count) || 1)}${damage.die}`;
                        damageText = [
                            dice,
                            damage?.type,
                        ]
                            .filter(Boolean)
                            .join(' ');
                    }

                    const main = [
                        category,
                        weaponType,
                    ]
                        .filter(Boolean)
                        .join(' - ');

                    return damageText
                        ? `${main} (${damageText})`
                        : main;
                },

                detailEditorialStats(item) {
                    const hidden = new Set([
                        'Tipo de arma',
                        'Dano base',
                        'Dano',
                        'Bônus de ataque',
                        'Bônus de dano',
                        'Bônus mágico da arma',
                    ]);

                    return this.detailStats(item)
                        .filter(row => !hidden.has(row.label));
                },

                detailTooltipText(entry) {
                    if (!entry || typeof entry !== 'object') {
                        return '';
                    }

                    return String(
                        entry?.description
                        ?? entry?.text
                        ?? entry?.value
                        ?? ''
                    ).trim();
                },

                detailStats(item) {
                    const rows = [];
                    const add = (label, value) => {
                        if (
                            value === null
                            || value === undefined
                            || value === ''
                            || value === false
                        ) {
                            return;
                        }

                        rows.push({
                            label,
                            value: String(value),
                        });
                    };

                    const weight = Number(item?.weight ?? 0);

                    if (Number.isFinite(weight) && weight > 0) {
                        add(
                            'Peso',
                            `${Number.isInteger(weight) ? weight : weight.toFixed(2)} kg`
                        );
                    }

                    add('Classe de Armadura', item?.armor_class);

                    const weapon = this.detailWeapon(item);
                    if (weapon) {
                        add(
                            'Tipo de arma',
                            weapon?.weapon_type
                            || weapon?.category
                        );

                    }

                    const armor = this.detailArmor(item);
                    if (armor) {
                        add(
                            'Tipo de armadura',
                            armor?.armor_type
                            || armor?.category
                        );
                        add('CA base', armor?.base_ac);

                        const magicBonus = this.signedNumber(armor?.magic_bonus);
                        if (magicBonus && magicBonus !== '0') {
                            add('Bônus mágico da armadura', magicBonus);
                        }
                    }

                    const shield = this.detailShield(item);
                    if (shield) {
                        const total =
                            Number(shield?.base_bonus ?? shield?.ac_bonus ?? 0)
                            + Number(shield?.magic_bonus ?? 0);

                        add(
                            'Escudo',
                            shield?.label || 'Escudo'
                        );

                        if (Number.isFinite(total) && total !== 0) {
                            add('Bônus do escudo', `${this.signedNumber(total)} CA`);
                        }
                    }

                    for (const entry of this.detailAbilityBonuses(item)) {
                        add(
                            `Bônus de ${entry.label}`,
                            this.signedNumber(entry.value)
                        );
                    }

                    return rows;
                },

                itemFeatureCount(item) {
                    return this.detailFeatures(item).length;
                },

                async openStorefront() {
                    this.open = true;
                    document.body
                        .classList
                        .add('overflow-hidden');

                    await this.loadShops();
                },

                closeStorefront() {
                    if (this.purchaseOpen) {
                        this.closePurchase();
                        return;
                    }

                    if (this.detailOpen) {
                        this.closeItemDetail();
                        return;
                    }

                    this.open = false;
                    this.closePurchase();
                    this.closeItemDetail();

                    document.body
                        .classList
                        .remove('overflow-hidden');
                },

                async loadShops(
                    silent = false
                ) {
                    if (this.loading) {
                        return;
                    }

                    this.loading = true;

                    if (!silent) {
                        this.error = null;
                    }

                    try {
                        const response =
                            await fetch(
                                config.indexUrl,
                                {
                                    method: 'GET',

                                    headers: {
                                        'Accept':
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest',
                                    },

                                    cache:
                                        'no-store',
                                }
                            );

                        const data =
                            await response
                                .json()
                                .catch(
                                    () => ({})
                                );

                        if (!response.ok) {
                            const errors =
                                data?.errors
                                    ? Object.values(
                                        data.errors
                                    )
                                        .flat()
                                        .filter(Boolean)
                                    : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (
                                        data?.message
                                        ??
                                        'Não foi possível abrir a loja.'
                                    )
                            );
                        }

                        this.shops =
                            Array.isArray(
                                data?.shops
                            )
                                ? data.shops
                                    .map(shop => ({
                                        ...shop,

                                        id:
                                            parseInt(
                                                shop?.id
                                            ) || 0,

                                        shop_has_counter:
                                            !(
                                                shop?.shop_has_counter === false
                                                || shop?.shop_has_counter === 0
                                                || String(shop?.shop_has_counter ?? '1') === '0'
                                            ),

                                        shop_counter_color:
                                            /^#[0-9A-Fa-f]{6}$/.test(
                                                String(
                                                    shop?.shop_counter_color
                                                    ?? ''
                                                )
                                            )
                                                ? String(
                                                    shop.shop_counter_color
                                                ).toUpperCase()
                                                : '#6B3A24',

                                        shop_background_color:
                                            /^#[0-9A-Fa-f]{6}$/.test(
                                                String(
                                                    shop?.shop_background_color
                                                    ?? ''
                                                )
                                            )
                                                ? String(
                                                    shop.shop_background_color
                                                ).toUpperCase()
                                                : '#6F4A33',

                                        shop_image_zoom:
                                            Math.max(
                                                70,
                                                Math.min(
                                                    180,
                                                    parseInt(
                                                        shop?.shop_image_zoom
                                                    ) || 100
                                                )
                                            ),

                                        shop_image_position:
                                            [
                                                'top',
                                                'center',
                                                'bottom',
                                            ].includes(
                                                String(
                                                    shop?.shop_image_position
                                                    ?? ''
                                                )
                                            )
                                                ? String(
                                                    shop.shop_image_position
                                                )
                                                : 'center',

                                        shop_background_zoom:
                                            Math.max(
                                                100,
                                                Math.min(
                                                    180,
                                                    parseInt(
                                                        shop?.shop_background_zoom
                                                    ) || 100
                                                )
                                            ),

                                        shop_background_position:
                                            [
                                                'top',
                                                'center',
                                                'bottom',
                                            ].includes(
                                                String(
                                                    shop?.shop_background_position
                                                    ?? ''
                                                )
                                            )
                                                ? String(
                                                    shop.shop_background_position
                                                )
                                                : 'center',

                                        items:
                                            Array.isArray(
                                                shop?.items
                                            )
                                                ? shop.items
                                                    .map(
                                                        item => ({
                                                            ...item,

                                                            id:
                                                                parseInt(
                                                                    item?.id
                                                                ) || 0,

                                                            quantity_remaining:
                                                                Math.max(
                                                                    0,
                                                                    parseInt(
                                                                        item?.quantity_remaining
                                                                    ) || 0
                                                                ),
                                                        })
                                                    )
                                                    .filter(
                                                        item =>
                                                            item.id > 0
                                                    )
                                                : [],
                                    }))
                                    .filter(
                                        shop =>
                                            shop.id > 0
                                    )
                                : [];

                        this.wallet = {
                            copper:
                                Math.max(
                                    0,
                                    parseInt(
                                        data?.wallet?.copper
                                    ) || 0
                                ),

                            silver:
                                Math.max(
                                    0,
                                    parseInt(
                                        data?.wallet?.silver
                                    ) || 0
                                ),

                            electrum:
                                Math.max(
                                    0,
                                    parseInt(
                                        data?.wallet?.electrum
                                    ) || 0
                                ),

                            gold:
                                Math.max(
                                    0,
                                    parseInt(
                                        data?.wallet?.gold
                                    ) || 0
                                ),

                            platinum:
                                Math.max(
                                    0,
                                    parseInt(
                                        data?.wallet?.platinum
                                    ) || 0
                                ),
                        };

                        if (
                            !this.activeShopId
                            ||
                            !this.shops.some(
                                shop =>
                                    parseInt(
                                        shop.id
                                    )
                                    ===
                                    parseInt(
                                        this.activeShopId
                                    )
                            )
                        ) {
                            this.activeShopId =
                                this.shops[0]?.id
                                ?? null;
                        }

                        for (
                            const shop
                            of
                            this.shops
                        ) {
                            for (
                                const item
                                of
                                shop.items
                            ) {
                                if (
                                    !this.quantities[
                                        item.id
                                    ]
                                ) {
                                    this.quantities[
                                        item.id
                                    ] = 1;
                                }
                            }
                        }

                        this.ensureCategoryDrawer();

                        this.loaded = true;
                        this.error = null;
                    } catch (error) {
                        this.error =
                            error?.message
                            ??
                            'Não foi possível abrir a loja.';
                    } finally {
                        this.loading = false;
                    }
                },

                selectShop(shop) {
                    this.activeShopId =
                        parseInt(
                            shop?.id
                        ) || null;

                    this.itemSearch = '';
                    this.sortMode =
                        'price_asc';

                    this.categoryDrawerKey =
                        this.firstAvailableCategoryKey();

                    this.closeItemDetail();
                },

                coinDefinitions() {
                    return [
                        {
                            key: 'copper',
                            abbr: 'PC',
                            label: 'Cobre',
                            value: 1,
                        },
                        {
                            key: 'silver',
                            abbr: 'PP',
                            label: 'Prata',
                            value: 10,
                        },
                        {
                            key: 'electrum',
                            abbr: 'PE',
                            label: 'Electro',
                            value: 50,
                        },
                        {
                            key: 'gold',
                            abbr: 'PO',
                            label: 'Ouro',
                            value: 100,
                        },
                        {
                            key: 'platinum',
                            abbr: 'PL',
                            label: 'Platina',
                            value: 1000,
                        },
                    ];
                },

                priceCopperValue(item) {
                    const price =
                        item?.price
                        ?? {};

                    return this.coinDefinitions()
                        .reduce(
                            (
                                total,
                                coin
                            ) =>
                                total
                                +
                                (
                                    Math.max(
                                        0,
                                        parseInt(
                                            price?.[
                                                coin.key
                                            ]
                                        ) || 0
                                    )
                                    *
                                    coin.value
                                ),
                            0
                        );
                },

                priceInline(
                    item,
                    quantity = 1
                ) {
                    const price =
                        item?.price
                        ?? {};

                    const multiplier =
                        Math.max(
                            1,
                            parseInt(
                                quantity
                            ) || 1
                        );

                    const parts =
                        this.coinDefinitions()
                            .map(
                                coin => ({
                                    ...coin,

                                    amount:
                                        Math.max(
                                            0,
                                            parseInt(
                                                price?.[
                                                    coin.key
                                                ]
                                            ) || 0
                                        )
                                        *
                                        multiplier,
                                })
                            )
                            .filter(
                                coin =>
                                    coin.amount > 0
                            );

                    return parts.length
                        ? parts
                            .map(
                                coin =>
                                    `${coin.amount} ${coin.abbr}`
                            )
                            .join(' · ')
                        : 'Sem preço';
                },

                itemQuantity(item) {
                    const stock =
                        Math.max(
                            0,
                            parseInt(
                                item?.quantity_remaining
                            ) || 0
                        );

                    if (stock <= 0) {
                        return 0;
                    }

                    return Math.max(
                        1,
                        Math.min(
                            stock,
                            parseInt(
                                this.quantities?.[
                                    item?.id
                                ]
                            ) || 1
                        )
                    );
                },

                setQuantity(
                    item,
                    value
                ) {
                    const stock =
                        Math.max(
                            0,
                            parseInt(
                                item?.quantity_remaining
                            ) || 0
                        );

                    if (stock <= 0) {
                        this.quantities[
                            item.id
                        ] = 0;

                        return;
                    }

                    this.quantities[
                        item.id
                    ] =
                        Math.max(
                            1,
                            Math.min(
                                stock,
                                parseInt(
                                    value
                                ) || 1
                            )
                        );
                },

                decrementQuantity(item) {
                    this.setQuantity(
                        item,
                        this.itemQuantity(item) - 1
                    );
                },

                incrementQuantity(item) {
                    this.setQuantity(
                        item,
                        this.itemQuantity(item) + 1
                    );
                },

                totalInline(item) {
                    return this.priceInline(
                        item,
                        this.itemQuantity(item)
                        || 1
                    );
                },

                typeLabel(item) {
                    const type =
                        String(
                            item?.type
                            ?? ''
                        ).trim();

                    return {
                        mundane:
                            'Item mundano',

                        wonderful:
                            'Item maravilhoso',

                        technological:
                            'Tecnológico',

                        weapon:
                            'Arma',

                        armor:
                            'Armadura',

                        shield:
                            'Escudo',

                        potion:
                            'Poção',

                        scroll:
                            'Pergaminho',
                    }[type] ?? (
                        type
                        || 'Item'
                    );
                },

                walletInline() {
                    const wallet =
                        this.wallet
                        ?? {};

                    const parts =
                        this.coinDefinitions()
                            .map(
                                coin => ({
                                    ...coin,

                                    amount:
                                        Math.max(
                                            0,
                                            parseInt(
                                                wallet?.[
                                                    coin.key
                                                ]
                                            ) || 0
                                        ),
                                })
                            )
                            .filter(
                                coin =>
                                    coin.amount > 0
                            );

                    return parts.length
                        ? parts
                            .map(
                                coin =>
                                    `${coin.amount} ${coin.abbr}`
                            )
                            .join(' · ')
                        : 'Bolsa vazia';
                },

                emptyPayment() {
                    return {
                        copper: 0,
                        silver: 0,
                        electrum: 0,
                        gold: 0,
                        platinum: 0,
                    };
                },

                walletValueCopper() {
                    return this.coinDefinitions()
                        .reduce(
                            (total, coin) =>
                                total
                                +
                                (
                                    Math.max(
                                        0,
                                        parseInt(
                                            this.wallet?.[
                                                coin.key
                                            ]
                                        ) || 0
                                    )
                                    *
                                    coin.value
                                ),
                            0
                        );
                },

                copperValueInline(value) {
                    let remaining =
                        Math.max(
                            0,
                            parseInt(value) || 0
                        );

                    if (remaining <= 0) {
                        return '0 PC';
                    }

                    const parts = [];

                    for (
                        const coin
                        of [...this.coinDefinitions()]
                            .sort(
                                (a, b) =>
                                    b.value - a.value
                            )
                    ) {
                        const amount =
                            Math.floor(
                                remaining
                                /
                                coin.value
                            );

                        if (amount > 0) {
                            parts.push(
                                `${amount} ${coin.abbr}`
                            );

                            remaining -=
                                amount
                                *
                                coin.value;
                        }
                    }

                    return parts.length
                        ? parts.join(' · ')
                        : '0 PC';
                },

                paymentValueCopper() {
                    return this.coinDefinitions()
                        .reduce(
                            (total, coin) =>
                                total
                                +
                                (
                                    Math.max(
                                        0,
                                        Math.min(
                                            parseInt(
                                                this.wallet?.[
                                                    coin.key
                                                ]
                                            ) || 0,
                                            parseInt(
                                                this.payment?.[
                                                    coin.key
                                                ]
                                            ) || 0
                                        )
                                    )
                                    *
                                    coin.value
                                ),
                            0
                        );
                },

                paymentInline() {
                    const parts =
                        this.coinDefinitions()
                            .map(
                                coin => ({
                                    ...coin,
                                    amount:
                                        Math.max(
                                            0,
                                            parseInt(
                                                this.payment?.[
                                                    coin.key
                                                ]
                                            ) || 0
                                        ),
                                })
                            )
                            .filter(
                                coin =>
                                    coin.amount > 0
                            );

                    return parts.length
                        ? parts
                            .map(
                                coin =>
                                    `${coin.amount} ${coin.abbr}`
                            )
                            .join(' · ')
                        : 'Nenhuma moeda selecionada';
                },

                purchaseCostCopper() {
                    if (!this.purchaseItem) {
                        return 0;
                    }

                    return (
                        this.priceCopperValue(
                            this.purchaseItem
                        )
                        *
                        Math.max(
                            1,
                            parseInt(
                                this.purchaseQuantity
                            ) || 1
                        )
                    );
                },

                paymentRemainingCopper() {
                    return Math.max(
                        0,
                        this.purchaseCostCopper()
                        -
                        this.paymentValueCopper()
                    );
                },

                paymentChangeCopper() {
                    return Math.max(
                        0,
                        this.paymentValueCopper()
                        -
                        this.purchaseCostCopper()
                    );
                },

                get purchaseCanConfirm() {
                    return (
                        !!this.purchaseItem
                        &&
                        this.busyItemId === null
                        &&
                        this.purchaseQuantity > 0
                        &&
                        this.paymentValueCopper()
                            >=
                            this.purchaseCostCopper()
                        &&
                        this.purchaseCostCopper() > 0
                    );
                },

                clampPaymentCoin(key) {
                    const max =
                        Math.max(
                            0,
                            parseInt(
                                this.wallet?.[key]
                            ) || 0
                        );

                    this.payment[key] =
                        Math.max(
                            0,
                            Math.min(
                                max,
                                parseInt(
                                    this.payment?.[key]
                                ) || 0
                            )
                        );

                    this.paymentError = null;
                },

                adjustPaymentCoin(key, delta) {
                    const max = Math.max(
                        0,
                        parseInt(this.wallet?.[key]) || 0
                    );

                    const current = Math.max(
                        0,
                        parseInt(this.payment?.[key]) || 0
                    );

                    this.payment[key] = Math.max(
                        0,
                        Math.min(max, current + delta)
                    );

                    this.paymentError = null;
                },

                clearPayment() {
                    this.payment =
                        this.emptyPayment();

                    this.paymentError = null;
                },

                suggestPayment() {
                    const target =
                        this.purchaseCostCopper();

                    const result =
                        this.emptyPayment();

                    let remaining =
                        target;

                    const descending =
                        [...this.coinDefinitions()]
                            .sort(
                                (a, b) =>
                                    b.value - a.value
                            );

                    for (
                        const coin
                        of descending
                    ) {
                        const available =
                            Math.max(
                                0,
                                parseInt(
                                    this.wallet?.[
                                        coin.key
                                    ]
                                ) || 0
                            );

                        if (
                            available <= 0
                            ||
                            remaining <= 0
                        ) {
                            continue;
                        }

                        const take =
                            Math.min(
                                available,
                                Math.floor(
                                    remaining
                                    /
                                    coin.value
                                )
                            );

                        if (take > 0) {
                            result[
                                coin.key
                            ] += take;

                            remaining -=
                                take
                                *
                                coin.value;
                        }
                    }

                    if (remaining > 0) {
                        const ascending =
                            [...this.coinDefinitions()]
                                .sort(
                                    (a, b) =>
                                        a.value - b.value
                                );

                        for (
                            const coin
                            of ascending
                        ) {
                            const available =
                                Math.max(
                                    0,
                                    parseInt(
                                        this.wallet?.[
                                            coin.key
                                        ]
                                    ) || 0
                                )
                                -
                                result[
                                    coin.key
                                ];

                            if (
                                available > 0
                                &&
                                coin.value
                                    >=
                                    remaining
                            ) {
                                result[
                                    coin.key
                                ] += 1;

                                remaining = 0;
                                break;
                            }
                        }
                    }

                    this.payment =
                        result;

                    this.paymentError =
                        remaining > 0
                            ? 'Sua bolsa não consegue cobrir este valor com as moedas disponíveis.'
                            : null;
                },

                walletAfterPayment() {
                    const result = {};

                    for (
                        const coin
                        of this.coinDefinitions()
                    ) {
                        result[
                            coin.key
                        ] =
                            Math.max(
                                0,
                                (
                                    parseInt(
                                        this.wallet?.[
                                            coin.key
                                        ]
                                    ) || 0
                                )
                                -
                                (
                                    parseInt(
                                        this.payment?.[
                                            coin.key
                                        ]
                                    ) || 0
                                )
                            );
                    }

                    let change =
                        this.paymentChangeCopper();

                    for (
                        const coin
                        of [...this.coinDefinitions()]
                            .sort(
                                (a, b) =>
                                    b.value - a.value
                            )
                    ) {
                        const amount =
                            Math.floor(
                                change
                                /
                                coin.value
                            );

                        if (amount > 0) {
                            result[
                                coin.key
                            ] += amount;

                            change -=
                                amount
                                *
                                coin.value;
                        }
                    }

                    return result;
                },

                walletObjectInline(wallet) {
                    const parts =
                        this.coinDefinitions()
                            .map(
                                coin => ({
                                    ...coin,
                                    amount:
                                        Math.max(
                                            0,
                                            parseInt(
                                                wallet?.[
                                                    coin.key
                                                ]
                                            ) || 0
                                        ),
                                })
                            )
                            .filter(
                                coin =>
                                    coin.amount > 0
                            );

                    return parts.length
                        ? parts
                            .map(
                                coin =>
                                    `${coin.amount} ${coin.abbr}`
                            )
                            .join(' · ')
                        : 'Bolsa vazia';
                },

                openPurchase(item) {
                    const itemId =
                        parseInt(
                            item?.id
                        ) || 0;

                    const quantity =
                        this.itemQuantity(
                            item
                        );

                    if (
                        !itemId
                        ||
                        quantity <= 0
                        ||
                        parseInt(
                            item?.quantity_remaining
                        ) <= 0
                    ) {
                        return;
                    }

                    this.purchaseItem =
                        item;

                    this.purchaseQuantity =
                        quantity;

                    this.paymentError =
                        null;

                    this.payment =
                        this.emptyPayment();

                    this.purchaseOpen =
                        true;

                    this.suggestPayment();
                },

                closePurchase() {
                    if (
                        this.busyItemId
                        !==
                        null
                    ) {
                        return;
                    }

                    this.purchaseOpen =
                        false;

                    this.purchaseItem =
                        null;

                    this.purchaseQuantity =
                        1;

                    this.paymentError =
                        null;

                    this.payment =
                        this.emptyPayment();
                },

                async confirmPurchase() {
                    const item =
                        this.purchaseItem;

                    const itemId =
                        parseInt(
                            item?.id
                        ) || 0;

                    const quantity =
                        Math.max(
                            1,
                            parseInt(
                                this.purchaseQuantity
                            ) || 1
                        );

                    if (
                        !itemId
                        ||
                        !this.purchaseCanConfirm
                    ) {
                        this.paymentError =
                            this.walletValueCopper()
                            <
                            this.purchaseCostCopper()
                                ? 'Você não possui moedas suficientes para esta compra.'
                                : (
                                    this.paymentRemainingCopper() > 0
                                        ? `Ainda faltam ${this.copperValueInline(this.paymentRemainingCopper())}.`
                                        : 'Revise o pagamento antes de confirmar.'
                                );

                        return;
                    }

                    const selectedPayment =
                        this.coinDefinitions()
                            .reduce(
                                (
                                    result,
                                    coin
                                ) => {
                                    result[
                                        coin.key
                                    ] =
                                        Math.max(
                                            0,
                                            parseInt(
                                                this.payment?.[
                                                    coin.key
                                                ]
                                            ) || 0
                                        );

                                    return result;
                                },
                                {}
                            );

                    this.busyItemId =
                        itemId;

                    this.paymentError =
                        null;

                    try {
                        const response =
                            await fetch(
                                config
                                    .purchaseUrl
                                    .replace(
                                        '__LOOT_ITEM__',
                                        itemId
                                    ),
                                {
                                    method:
                                        'POST',

                                    headers: {
                                        'Accept':
                                            'application/json',

                                        'Content-Type':
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest',

                                        'X-CSRF-TOKEN':
                                            config.csrf,
                                    },

                                    body:
                                        JSON.stringify({
                                            quantity,
                                            payment:
                                                selectedPayment,
                                        }),
                                }
                            );

                        const data =
                            await response
                                .json()
                                .catch(
                                    () => ({})
                                );

                        if (!response.ok) {
                            const errors =
                                data?.errors
                                    ? Object.values(
                                        data.errors
                                    )
                                        .flat()
                                        .filter(Boolean)
                                    : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (
                                        data?.message
                                        ??
                                        'Não foi possível concluir a compra.'
                                    )
                            );
                        }

                        const current =
                            this.activeItems
                                .find(
                                    currentItem =>
                                        parseInt(
                                            currentItem.id
                                        )
                                        ===
                                        itemId
                                );

                        if (current) {
                            current
                                .quantity_remaining =
                                    Math.max(
                                        0,
                                        parseInt(
                                            data?.quantity_remaining
                                        ) || 0
                                    );
                        }

                        if (data?.wallet) {
                            this.wallet = {
                                copper:
                                    Math.max(
                                        0,
                                        parseInt(
                                            data.wallet.copper
                                        ) || 0
                                    ),

                                silver:
                                    Math.max(
                                        0,
                                        parseInt(
                                            data.wallet.silver
                                        ) || 0
                                    ),

                                electrum:
                                    Math.max(
                                        0,
                                        parseInt(
                                            data.wallet.electrum
                                        ) || 0
                                    ),

                                gold:
                                    Math.max(
                                        0,
                                        parseInt(
                                            data.wallet.gold
                                        ) || 0
                                    ),

                                platinum:
                                    Math.max(
                                        0,
                                        parseInt(
                                            data.wallet.platinum
                                        ) || 0
                                    ),
                            };
                        }

                        this.quantities[
                            itemId
                        ] = 1;

                        this.purchaseOpen =
                            false;

                        this.purchaseItem =
                            null;

                        this.purchaseQuantity =
                            1;

                        this.payment =
                            this.emptyPayment();

                        window.dispatchEvent(
                            new CustomEvent(
                                'shop-purchase-completed',
                                {
                                    detail: {
                                        item:
                                            data?.item
                                            ?? null,

                                        wallet:
                                            data?.wallet
                                            ?? null,

                                        payment:
                                            data?.payment
                                            ?? null,

                                        change:
                                            data?.change
                                            ?? null,
                                    },
                                }
                            )
                        );

                        this.showNotice(
                            data?.message
                            ??
                            'Compra concluída.'
                        );
                    } catch (error) {
                        this.paymentError =
                            error?.message
                            ??
                            'Não foi possível concluir a compra.';

                        await this
                            .loadShops(
                                true
                            );
                    } finally {
                        this.busyItemId =
                            null;
                    }
                },


                showNotice(
                    message,
                    error = false
                ) {
                    this.notice =
                        message;

                    this.noticeError =
                        !!error;

                    clearTimeout(
                        this.noticeTimer
                    );

                    this.noticeTimer =
                        setTimeout(
                            () => {
                                this.notice =
                                    null;
                            },
                            4200
                        );
                },
            };
        };
    </script>
@endonce


<div
    x-data="characterShopStorefrontV2({
        indexUrl: @js(
            route(
                'characters.shops.index',
                [
                    'character' =>
                        $character
                ]
            )
        ),

        purchaseUrl: @js(
            route(
                'characters.shops.items.purchase',
                [
                    'character' =>
                        $character,

                    'lootItem' =>
                        '__LOOT_ITEM__',
                ]
            )
        ),

        csrf: @js(csrf_token()),
    })"
    @open-character-shop.window="openStorefront()"
    @keydown.escape.window="
        if (purchaseOpen) {
            closePurchase()
        } else if (detailOpen) {
            closeItemDetail()
        } else if (open) {
            closeStorefront()
        }
    "
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition.opacity
            class="player-storefront-layer"
            role="dialog"
            aria-modal="true"
            aria-label="Loja"
        >
            <div
                class="player-storefront-backdrop"
                @click="closeStorefront()"
            ></div>

            <section
                class="
                    character-sheet-theme
                    player-storefront
                "
                :class="{
                    'is-overlay-open':
                        detailOpen
                        ||
                        purchaseOpen
                }"
                data-theme="{{ $characterTheme }}"
                @click.stop
            >
                {{-- =====================================================
                    LOADING
                ====================================================== --}}
                <div
                    x-show="loading && !loaded"
                    x-cloak
                    class="player-storefront-state"
                >
                    <div>
                        <div
                            class="
                                player-storefront-state__spinner
                            "
                        ></div>

                        <h3>
                            Abrindo a loja...
                        </h3>

                        <p>
                            Preparando a vitrine e conferindo
                            o estoque disponível.
                        </p>
                    </div>
                </div>


                {{-- =====================================================
                    ERRO
                ====================================================== --}}
                <div
                    x-show="error && !loading"
                    x-cloak
                    class="player-storefront-state"
                >
                    <div>
                        <h3>
                            A porta não abriu.
                        </h3>

                        <p
                            x-text="error"
                        ></p>

                        <button
                            type="button"
                            @click="loadShops()"
                        >
                            Tentar novamente
                        </button>
                    </div>
                </div>


                {{-- =====================================================
                    SEM LOJAS
                ====================================================== --}}
                <div
                    x-show="
                        loaded
                        &&
                        !loading
                        &&
                        !error
                        &&
                        shops.length === 0
                    "
                    x-cloak
                    class="player-storefront-state"
                >
                    <div>
                        <h3>
                            Nenhuma loja aberta
                        </h3>

                        <p>
                            O Mestre não está mantendo nenhuma
                            loja disponível para a Party neste
                            momento.
                        </p>

                        <button
                            type="button"
                            @click="closeStorefront()"
                        >
                            Voltar
                        </button>
                    </div>
                </div>


                {{-- =====================================================
                    LOJA
                ====================================================== --}}
                <template
                    x-if="
                        activeShop
                        &&
                        !loading
                        &&
                        !error
                    "
                >
                    <div class="player-storefront-shell">

                        {{-- =====================================================
                            CENA DA LOJA — FUNDO + LOJISTA + BALCÃO
                            Replica a apresentação configurada pelo Mestre sem
                            empurrar a vitrine para fora da tela.
                        ====================================================== --}}
                        <header class="player-storefront-scene">
                            <div
                                class="player-storefront-scene__background"
                                :style="{
                                    backgroundColor:
                                        activeShop.shop_background_color
                                        || '#6F4A33'
                                }"
                            >
                                <template x-if="activeShop.background_image_url">
                                    <img
                                        :src="activeShop.background_image_url"
                                        :alt="activeShop.name"
                                        :style="{
                                            transform:
                                                'scale('
                                                + ((activeShop.shop_background_zoom || 100) / 100)
                                                + ')',
                                            transformOrigin:
                                                activeShop.shop_background_position === 'top'
                                                    ? 'center top'
                                                    : (
                                                        activeShop.shop_background_position === 'bottom'
                                                            ? 'center bottom'
                                                            : 'center center'
                                                    ),
                                            objectPosition:
                                                activeShop.shop_background_position === 'top'
                                                    ? 'center top'
                                                    : (
                                                        activeShop.shop_background_position === 'bottom'
                                                            ? 'center bottom'
                                                            : 'center center'
                                                    )
                                        }"
                                    >
                                </template>
                            </div>

                            <div class="player-storefront-scene__shade"></div>

                            <div class="player-storefront-scene__title">
                                <div class="player-storefront-scene__status">
                                    <span class="player-storefront-open-dot"></span>
                                    Loja aberta
                                    <template x-if="activeShop.campaign_name">
                                        <span>
                                            · <span x-text="activeShop.campaign_name"></span>
                                        </span>
                                    </template>
                                </div>

                                <h2 x-text="activeShop.name"></h2>

                                <p x-show="activeShop.shopkeeper_name">
                                    Atendido por
                                    <strong x-text="activeShop.shopkeeper_name"></strong>
                                </p>

                                <p
                                    x-show="activeShop.description"
                                    class="player-storefront-scene__description"
                                    x-text="activeShop.description"
                                ></p>
                            </div>

                            <div class="player-storefront-scene__actions">
                                <div class="player-storefront-wallet">
                                    <span>Sua bolsa</span>
                                    <strong x-text="walletInline()"></strong>
                                </div>

                                <button
                                    type="button"
                                    class="player-storefront-scene-action"
                                    title="Atualizar estoque"
                                    aria-label="Atualizar estoque"
                                    :disabled="loading"
                                    @click="loadShops(true)"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M20 6v5h-5"></path>
                                        <path d="M4 18v-5h5"></path>
                                        <path d="M6.1 9A7 7 0 0 1 18.2 6.3L20 11"></path>
                                        <path d="M17.9 15A7 7 0 0 1 5.8 17.7L4 13"></path>
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    class="player-storefront-scene-action"
                                    title="Fechar loja"
                                    aria-label="Fechar loja"
                                    @click="closeStorefront()"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M6 6 18 18"></path>
                                        <path d="M18 6 6 18"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- Lojista: posição e zoom vêm da configuração do Mestre. --}}
                            <div class="player-storefront-scene__keeper" :class="{ 'is-counterless': activeShop.shop_has_counter === false }">
                                <template x-if="activeShop.presentation_image_url">
                                    <img
                                        :src="activeShop.presentation_image_url"
                                        :alt="activeShop.shopkeeper_name || activeShop.name"
                                        :style="{
                                            transform:
                                                'scale('
                                                + ((activeShop.shop_image_zoom || 100) / 100)
                                                + ')',
                                            transformOrigin:
                                                activeShop.shop_image_position === 'top'
                                                    ? 'center top'
                                                    : (
                                                        activeShop.shop_image_position === 'bottom'
                                                            ? 'center bottom'
                                                            : 'center center'
                                                    ),
                                            objectPosition:
                                                activeShop.shop_image_position === 'top'
                                                    ? 'center top'
                                                    : (
                                                        activeShop.shop_image_position === 'bottom'
                                                            ? 'center bottom'
                                                            : 'center center'
                                                    )
                                        }"
                                    >
                                </template>
                            </div>

                            {{-- Balcão superior --}}
                            <div
                                x-show="activeShop.shop_has_counter !== false"
                                x-cloak
                                class="player-storefront-counter-top"
                                :style="{
                                    '--counter-color': activeShop.shop_counter_color || '#6B3A24',
                                    background:
                                        'linear-gradient(180deg, '
                                        + 'color-mix(in srgb, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 68%, white) 0%, '
                                        + 'color-mix(in srgb, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 88%, white) 20%, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 58%, '
                                        + 'color-mix(in srgb, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 72%, black) 100%)',
                                    borderColor:
                                        'color-mix(in srgb, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 60%, black)'
                                }"
                            >
                                <div></div>
                            </div>

                            {{-- Frente do balcão --}}
                            <div
                                x-show="activeShop.shop_has_counter !== false"
                                x-cloak
                                class="player-storefront-counter-front"
                                :style="{
                                    '--counter-color': activeShop.shop_counter_color || '#6B3A24',
                                    background:
                                        'linear-gradient(180deg, '
                                        + 'color-mix(in srgb, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 82%, white) 0%, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 26%, '
                                        + 'color-mix(in srgb, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 82%, black) 100%)',
                                    borderColor:
                                        'color-mix(in srgb, '
                                        + (activeShop.shop_counter_color || '#6B3A24')
                                        + ' 58%, black)'
                                }"
                            >
                                <div class="player-storefront-counter-panel is-left"></div>
                                <div class="player-storefront-counter-panel is-right"></div>
                                <div class="player-storefront-counter-lock">
                                    <span></span>
                                </div>
                            </div>
                        </header>

                        {{-- OUTRAS LOJAS ABERTAS --}}
                        <div
                            x-show="shops.length > 1"
                            x-cloak
                            class="
                                player-storefront-shop-tabs
                            "
                        >
                            <template
                                x-for="
                                    shop
                                    in
                                    shops
                                "
                                :key="
                                    'player-shop-tab-'
                                    +
                                    shop.id
                                "
                            >
                                <button
                                    type="button"
                                    class="
                                        player-storefront-shop-tab
                                    "
                                    :class="{
                                        'is-active':
                                            parseInt(
                                                activeShopId
                                            )
                                            ===
                                            parseInt(
                                                shop.id
                                            )
                                    }"
                                    @click="
                                        selectShop(shop)
                                    "
                                    x-text="
                                        shop.name
                                    "
                                ></button>
                            </template>
                        </div>


                        {{-- ESTOQUE --}}
                        <main
                            class="
                                player-storefront-content
                            "
                        >
                            <div
                                x-show="
                                    activeItems.length === 0
                                "
                                x-cloak
                                class="
                                    player-storefront-empty
                                "
                            >
                                <div>
                                    <strong>
                                        As prateleiras estão vazias.
                                    </strong>

                                    <p>
                                        A loja continua aberta.
                                        O Mestre pode repor o
                                        estoque a qualquer momento.
                                    </p>
                                </div>
                            </div>


                            <div
                                x-show="
                                    activeItems.length > 0
                                    &&
                                    visibleItems.length === 0
                                "
                                x-cloak
                                class="
                                    player-storefront-empty
                                "
                            >
                                <div>
                                    <strong>
                                        Nenhum produto encontrado
                                    </strong>

                                    <p>
                                        Não há produtos nesta vitrine.
                                    </p>
                                </div>
                            </div>


                            <div
                                x-show="
                                    visibleItems.length > 0
                                "
                                class="player-storefront-category-list"
                            >
                                <template
                                    x-for="section in categorySections"
                                    :key="'storefront-category-' + section.key"
                                >
                                    <section
                                        x-show="section.items.length > 0"
                                        x-cloak
                                        class="player-storefront-category"
                                        :class="{
                                            'is-open':
                                                isCategoryDrawerOpen(
                                                    section.key
                                                )
                                        }"
                                        :data-category="section.key"
                                    >
                                        <button
                                            type="button"
                                            class="player-storefront-category__head"
                                            :aria-expanded="
                                                isCategoryDrawerOpen(
                                                    section.key
                                                )
                                                    ? 'true'
                                                    : 'false'
                                            "
                                            @click="
                                                toggleCategoryDrawer(
                                                    section.key
                                                )
                                            "
                                        >
                                            <span class="player-storefront-category__icon" aria-hidden="true">
                                                <template x-if="section.key === 'consumables'">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                        <path d="M9 3h6M10 3v4l-3 5v6.5A2.5 2.5 0 0 0 9.5 21h5a2.5 2.5 0 0 0 2.5-2.5V12l-3-5V3"/>
                                                        <path d="M8 13h8"/>
                                                    </svg>
                                                </template>

                                                <template x-if="section.key === 'magical'">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                        <path d="m12 3 1.35 4.15L17.5 8.5l-4.15 1.35L12 14l-1.35-4.15L6.5 8.5l4.15-1.35L12 3Z"/>
                                                        <path d="m18.5 14 .7 2.1 2.1.7-2.1.7-.7 2.1-.7-2.1-2.1-.7 2.1-.7.7-2.1Z"/>
                                                    </svg>
                                                </template>

                                                <template x-if="section.key === 'mundane'">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                        <path d="M5 7.5 12 4l7 3.5v9L12 20l-7-3.5v-9Z"/>
                                                        <path d="m5 7.5 7 3.5 7-3.5M12 11v9"/>
                                                    </svg>
                                                </template>
                                            </span>

                                            <div class="player-storefront-category__copy">
                                                <h4 x-text="section.title"></h4>
                                                <p x-text="section.description"></p>
                                            </div>

                                            <span class="player-storefront-category__meta">
                                                <span
                                                    class="player-storefront-category__count"
                                                    x-text="section.items.length"
                                                ></span>

                                                <span
                                                    class="player-storefront-category__chevron"
                                                    aria-hidden="true"
                                                >
                                                    <svg
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="1.9"
                                                    >
                                                        <path d="m7 9 5 5 5-5"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </button>

                                        <div
                                            x-show="
                                                isCategoryDrawerOpen(
                                                    section.key
                                                )
                                            "
                                            x-cloak
                                            x-transition.opacity.duration.140ms
                                            class="player-storefront-category__body"
                                        >
                                            <div class="player-storefront-category__body-inner">
                                                <div class="player-storefront-grid">
                                            <template
                                                x-for="item in section.items"
                                                :key="'storefront-item-' + item.id"
                                            >
                                    <article
                                        class="
                                            player-storefront-item
                                        "
                                        role="button"
                                        tabindex="0"
                                        @click="openItemDetail(item)"
                                        @keydown.enter.prevent="openItemDetail(item)"
                                        @keydown.space.prevent="openItemDetail(item)"
                                        :class="{
                                            'is-sold-out':
                                                parseInt(
                                                    item
                                                        .quantity_remaining
                                                )
                                                <=
                                                0
                                        }"
                                    >
                                        <div
                                            class="
                                                player-storefront-item__main
                                            "
                                        >
                                            <div
                                                class="
                                                    player-storefront-item__image
                                                "
                                            >
                                                <template
                                                    x-if="
                                                        item.image_url
                                                    "
                                                >
                                                    <img
                                                        :src="
                                                            item
                                                                .image_url
                                                        "
                                                        :alt="
                                                            item.name
                                                        "
                                                    >
                                                </template>

                                                <template
                                                    x-if="
                                                        !item
                                                            .image_url
                                                    "
                                                >
                                                    <span
                                                        class="
                                                            player-storefront-item__image-empty
                                                        "
                                                    >
                                                        ◆
                                                    </span>
                                                </template>

                                                <span
                                                    class="
                                                        player-storefront-stock
                                                    "
                                                    :class="{
                                                        'is-empty':
                                                            parseInt(
                                                                item
                                                                    .quantity_remaining
                                                            )
                                                            <=
                                                            0
                                                    }"
                                                    x-text="
                                                        parseInt(
                                                            item
                                                                .quantity_remaining
                                                        )
                                                        >
                                                        0
                                                            ? (
                                                                item
                                                                    .quantity_remaining
                                                                +
                                                                (
                                                                    parseInt(
                                                                        item
                                                                            .quantity_remaining
                                                                    )
                                                                    ===
                                                                    1
                                                                        ? ' disponível'
                                                                        : ' disponíveis'
                                                                )
                                                            )
                                                            : 'Esgotado'
                                                    "
                                                ></span>
                                            </div>


                                            <div
                                                class="
                                                    player-storefront-item__copy
                                                "
                                            >
                                                <p
                                                    class="
                                                        player-storefront-item__kind
                                                    "
                                                >
                                                    <span
                                                        x-text="
                                                            typeLabel(
                                                                item
                                                            )
                                                        "
                                                    ></span>

                                                    <template
                                                        x-if="
                                                            item
                                                                .rarity_label
                                                        "
                                                    >
                                                        <span>
                                                            ·
                                                            <span
                                                                x-text="
                                                                    item
                                                                        .rarity_label
                                                                "
                                                            ></span>
                                                        </span>
                                                    </template>
                                                </p>

                                                <h4
                                                    x-text="
                                                        item.name
                                                    "
                                                ></h4>

                                                <p
                                                    x-show="
                                                        item
                                                            .description
                                                    "
                                                    class="
                                                        player-storefront-item__description
                                                    "
                                                    x-text="
                                                        item
                                                            .description
                                                    "
                                                ></p>

                                                <div class="player-storefront-item__inspect">
                                                    <span x-show="itemFeatureCount(item) > 0">
                                                        <span x-text="itemFeatureCount(item)"></span>
                                                        habilidade<span x-show="itemFeatureCount(item) !== 1">s</span>
                                                        ·
                                                    </span>
                                                    Ver item completo →
                                                </div>
                                            </div>
                                        </div>


                                        <footer
                                            class="
                                                player-storefront-item__footer
                                            "
                                        >
                                            <div
                                                class="
                                                    player-storefront-price-row
                                                "
                                            >
                                                <div
                                                    class="
                                                        player-storefront-price
                                                    "
                                                >
                                                    <span>
                                                        Preço
                                                    </span>

                                                    <strong
                                                        x-text="
                                                            priceInline(
                                                                item
                                                            )
                                                        "
                                                    ></strong>
                                                </div>

                                                <div
                                                    x-show="
                                                        itemQuantity(
                                                            item
                                                        )
                                                        >
                                                        1
                                                    "
                                                    class="
                                                        player-storefront-total-badge
                                                    "
                                                >
                                                    <span>
                                                        Valor total
                                                    </span>

                                                    <strong
                                                        x-text="
                                                            totalInline(
                                                                item
                                                            )
                                                        "
                                                    ></strong>
                                                </div>
                                            </div>

                                            <div
                                                x-show="
                                                    parseInt(
                                                        item
                                                            .quantity_remaining
                                                    )
                                                    >
                                                    0
                                                "
                                                class="
                                                    player-storefront-buy-stack
                                                "
                                            >
                                                <div
                                                    class="
                                                        player-storefront-buy
                                                    "
                                                >
                                                    <div
                                                        class="
                                                            player-storefront-qty
                                                        "
                                                        aria-label="Quantidade"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click.stop="
                                                                decrementQuantity(
                                                                    item
                                                                )
                                                            "
                                                            :disabled="
                                                                itemQuantity(
                                                                    item
                                                                )
                                                                <=
                                                                1
                                                            "
                                                        >
                                                            −
                                                        </button>

                                                        <span
                                                            x-text="
                                                                itemQuantity(
                                                                    item
                                                                )
                                                            "
                                                        ></span>

                                                        <button
                                                            type="button"
                                                            @click.stop="
                                                                incrementQuantity(
                                                                    item
                                                                )
                                                            "
                                                            :disabled="
                                                                itemQuantity(
                                                                    item
                                                                )
                                                                >=
                                                                parseInt(
                                                                    item
                                                                        .quantity_remaining
                                                                )
                                                            "
                                                        >
                                                            +
                                                        </button>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="
                                                            player-storefront-buy-button
                                                        "
                                                        :disabled="
                                                            busyItemId
                                                            !==
                                                            null
                                                        "
                                                        @click.stop="
                                                            openPurchase(
                                                                item
                                                            )
                                                        "
                                                    >
                                                        <span
                                                            x-text="
                                                                parseInt(
                                                                    busyItemId
                                                                )
                                                                ===
                                                                parseInt(
                                                                    item.id
                                                                )
                                                                    ? 'Comprando...'
                                                                    : 'Comprar'
                                                            "
                                                        ></span>
                                                    </button>
                                                </div>

                                            </div>
                                        </footer>
                                    </article>

                                            </template>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </template>
                            </div>
                        </main>
                    </div>
                </template>


                {{-- =====================================================
                    FICHA COMPLETA DO PRODUTO
                    Mesma linguagem editorial do Inventário do Player.
                ====================================================== --}}
                <div
                    x-show="detailOpen && detailItem"
                    x-cloak
                    x-transition.opacity
                    class="player-storefront-detail-layer"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Detalhes do produto"
                >
                    <div
                        class="player-storefront-detail-backdrop"
                        @click="closeItemDetail()"
                    ></div>

                    <section
                        x-show="detailItem"
                        x-transition
                        class="player-storefront-detail"
                    >
                        <header class="player-storefront-detail__topbar">
                            <span>Detalhes do item</span>

                            <button
                                type="button"
                                @click="closeItemDetail()"
                                aria-label="Fechar detalhes"
                            >×</button>
                        </header>

                        <div class="player-storefront-detail__scroll">
                            <div class="player-storefront-detail__hero">
                                <div class="player-storefront-detail__image">
                                    <template x-if="detailItem?.image_url">
                                        <img
                                            :src="detailItem.image_url"
                                            :alt="detailItem.name"
                                        >
                                    </template>

                                    <template x-if="!detailItem?.image_url">
                                        <span>◆</span>
                                    </template>
                                </div>

                                <div class="player-storefront-detail__identity">
                                    <div class="player-storefront-detail__title-row">
                                        <div class="min-w-0">
                                            <span class="player-storefront-detail__kicker">
                                                Item da loja
                                            </span>

                                            <h2 x-text="detailItem?.name"></h2>
                                        </div>

                                        <span class="player-storefront-detail__stock-status">
                                            <span class="player-storefront-detail__stock-dot"></span>
                                            <span
                                                x-text="
                                                    parseInt(detailItem?.quantity_remaining) > 0
                                                        ? detailItem.quantity_remaining + ' em estoque'
                                                        : 'Esgotado'
                                                "
                                            ></span>
                                        </span>
                                    </div>

                                    <em
                                        class="player-storefront-detail__classification"
                                        x-text="detailClassificationLine(detailItem)"
                                    ></em>

                                    <div class="player-storefront-editorial-mechanics">
                                        <p
                                            x-show="detailWeapon(detailItem)"
                                            x-cloak
                                            class="player-storefront-weapon-line"
                                            x-text="detailWeaponLine(detailItem)"
                                        ></p>

                                        <p
                                            x-show="detailWeaponTraits(detailItem).length > 0"
                                            x-cloak
                                            class="player-storefront-mechanic-line"
                                        >
                                            <strong>Propriedades:</strong>

                                            <template
                                                x-for="(trait, index) in detailWeaponTraits(detailItem)"
                                                :key="'shop-editorial-trait-' + index"
                                            >
                                                <span>
                                                    <span class="player-storefront-inline-tooltip">
                                                        <button
                                                            type="button"
                                                            class="player-storefront-inline-term"
                                                            x-text="trait?.name || trait?.label || trait?.key || 'Propriedade'"
                                                        ></button>

                                                        <span
                                                            x-show="detailTooltipText(trait)"
                                                            x-cloak
                                                            class="player-storefront-inline-tooltip-panel"
                                                            role="tooltip"
                                                        >
                                                            <span
                                                                class="player-storefront-inline-tooltip-title"
                                                                x-text="trait?.name || trait?.label || trait?.key || 'Propriedade'"
                                                            ></span>

                                                            <span
                                                                class="player-storefront-inline-tooltip-text"
                                                                x-text="detailTooltipText(trait)"
                                                            ></span>
                                                        </span>
                                                    </span><span
                                                        x-show="index < detailWeaponTraits(detailItem).length - 1"
                                                        aria-hidden="true"
                                                    >, </span>
                                                </span>
                                            </template>
                                        </p>

                                        <p
                                            x-show="detailWeaponMasteries(detailItem).length > 0"
                                            x-cloak
                                            class="player-storefront-mechanic-line"
                                        >
                                            <strong>Maestrias:</strong>

                                            <template
                                                x-for="(mastery, index) in detailWeaponMasteries(detailItem)"
                                                :key="'shop-editorial-mastery-' + index"
                                            >
                                                <span>
                                                    <span class="player-storefront-inline-tooltip">
                                                        <button
                                                            type="button"
                                                            class="player-storefront-inline-term"
                                                            x-text="mastery?.name || mastery?.label || mastery?.key || 'Maestria'"
                                                        ></button>

                                                        <span
                                                            x-show="detailTooltipText(mastery)"
                                                            x-cloak
                                                            class="player-storefront-inline-tooltip-panel"
                                                            role="tooltip"
                                                        >
                                                            <span
                                                                class="player-storefront-inline-tooltip-title"
                                                                x-text="mastery?.name || mastery?.label || mastery?.key || 'Maestria'"
                                                            ></span>

                                                            <span
                                                                class="player-storefront-inline-tooltip-text"
                                                                x-text="detailTooltipText(mastery)"
                                                            ></span>
                                                        </span>
                                                    </span><span
                                                        x-show="index < detailWeaponMasteries(detailItem).length - 1"
                                                        aria-hidden="true"
                                                    >, </span>
                                                </span>
                                            </template>
                                        </p>

                                        <template
                                            x-for="(stat, index) in detailEditorialStats(detailItem)"
                                            :key="'shop-editorial-stat-' + index"
                                        >
                                            <p class="player-storefront-mechanic-line">
                                                <strong x-text="stat.label + ':'"></strong>
                                                <span x-text="stat.value"></span>
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <section
                                x-show="detailItem?.description"
                                x-cloak
                                class="player-storefront-detail-section"
                            >
                                <div class="player-storefront-detail-section__title">
                                    Descrição
                                </div>

                                <p
                                    class="player-storefront-detail-description"
                                    x-text="detailItem?.description"
                                ></p>
                            </section>

                            <section
                                x-show="detailCustomProperties(detailItem).length > 0"
                                x-cloak
                                class="player-storefront-detail-section"
                            >
                                <div class="player-storefront-detail-section__title">
                                    Propriedades
                                </div>

                                <div class="player-storefront-detail-custom-list">
                                    <template
                                        x-for="(entry, index) in detailCustomProperties(detailItem)"
                                        :key="'shop-detail-property-' + index"
                                    >
                                        <div class="player-storefront-detail-custom-row">
                                            <div class="player-storefront-detail-custom-row__head">
                                                <h3 x-text="entry?.title || 'Propriedade'"></h3>
                                                <strong
                                                    x-show="entry?.value"
                                                    x-text="entry?.value"
                                                ></strong>
                                            </div>

                                            <p
                                                x-show="entry?.description"
                                                x-text="entry?.description"
                                            ></p>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            <section
                                x-show="detailFeatures(detailItem).length > 0"
                                x-cloak
                                class="player-storefront-detail-section"
                            >
                                <div class="player-storefront-detail-section__title">
                                    Habilidades
                                </div>

                                <div class="player-storefront-detail-features">
                                    <template
                                        x-for="(feature, index) in detailFeatures(detailItem)"
                                        :key="'shop-detail-feature-' + index"
                                    >
                                        <article class="player-storefront-detail-feature">
                                            <div class="player-storefront-detail-feature__head">
                                                <h3 x-text="feature?.title || 'Habilidade'"></h3>
                                                <span x-text="featureUsageLabel(feature)"></span>
                                            </div>

                                            <p
                                                x-show="feature?.description"
                                                x-text="feature?.description"
                                            ></p>
                                        </article>
                                    </template>
                                </div>
                            </section>

                            <section
                                x-show="detailModifiers(detailItem).length > 0"
                                x-cloak
                                class="player-storefront-detail-section"
                            >
                                <div class="player-storefront-detail-section__title">
                                    Modificadores
                                </div>

                                <div class="player-storefront-detail-modifier-list">
                                    <template
                                        x-for="(modifier, index) in detailModifiers(detailItem)"
                                        :key="'shop-detail-modifier-' + index"
                                    >
                                        <div class="player-storefront-detail-modifier-row">
                                            <template x-if="typeof modifier === 'string'">
                                                <p x-text="modifier"></p>
                                            </template>

                                            <template x-if="modifier && typeof modifier === 'object'">
                                                <div>
                                                    <h3 x-text="modifier?.name || modifier?.label || 'Modificador'"></h3>
                                                    <p
                                                        x-show="modifier?.description || modifier?.text || modifier?.value"
                                                        x-text="modifier?.description || modifier?.text || modifier?.value"
                                                    ></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            <section
                                x-show="detailItem?.notes"
                                x-cloak
                                class="player-storefront-detail-section"
                            >
                                <div class="player-storefront-detail-section__title">
                                    Observações
                                </div>

                                <p
                                    class="player-storefront-detail-notes"
                                    x-text="detailItem?.notes"
                                ></p>
                            </section>
                        </div>

                        <footer class="player-storefront-detail__footer">
                            <div class="player-storefront-detail__footer-price">
                                <span>Preço unitário</span>
                                <strong x-text="priceInline(detailItem)"></strong>
                            </div>

                            <div
                                x-show="parseInt(detailItem?.quantity_remaining) > 0"
                                class="player-storefront-detail__buy"
                            >
                                <div class="player-storefront-qty">
                                    <button
                                        type="button"
                                        @click="decrementQuantity(detailItem)"
                                        :disabled="itemQuantity(detailItem) <= 1"
                                    >−</button>

                                    <span x-text="itemQuantity(detailItem)"></span>

                                    <button
                                        type="button"
                                        @click="incrementQuantity(detailItem)"
                                        :disabled="itemQuantity(detailItem) >= parseInt(detailItem?.quantity_remaining)"
                                    >+</button>
                                </div>

                                <div
                                    x-show="itemQuantity(detailItem) > 1"
                                    class="player-storefront-detail__total"
                                >
                                    Total
                                    <strong x-text="totalInline(detailItem)"></strong>
                                </div>

                                <button
                                    type="button"
                                    class="player-storefront-buy-button is-detail"
                                    :disabled="busyItemId !== null"
                                    @click="openPurchase(detailItem)"
                                >
                                    <span
                                        x-text="
                                            parseInt(busyItemId) === parseInt(detailItem?.id)
                                                ? 'Comprando...'
                                                : 'Comprar item'
                                        "
                                    ></span>
                                </button>
                            </div>

                            <div
                                x-show="parseInt(detailItem?.quantity_remaining) <= 0"
                                class="player-storefront-detail__soldout"
                            >
                                Produto esgotado
                            </div>
                        </footer>
                    </section>
                </div>


                {{-- =====================================================
                    NEGOCIAÇÃO DE COMPRA
                    Produto à esquerda; moedas e fechamento à direita.
                ====================================================== --}}
                <div
                    x-show="purchaseOpen && purchaseItem"
                    x-cloak
                    x-transition.opacity
                    class="player-storefront-purchase-layer"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Finalizar compra"
                >
                    <div
                        class="player-storefront-purchase-backdrop"
                        @click="closePurchase()"
                    ></div>

                    <section
                        x-show="purchaseItem"
                        x-transition
                        class="player-storefront-purchase player-storefront-purchase-v8"
                        @click.stop
                    >
                        <header class="player-storefront-purchase__head player-storefront-purchase-v8__head">
                            <div>
                                <p class="player-storefront-purchase__eyebrow">Negociação</p>
                                <h3>Finalizar compra</h3>
                            </div>

                            <button
                                type="button"
                                class="player-storefront-purchase__close"
                                @click="closePurchase()"
                                :disabled="busyItemId !== null"
                                aria-label="Fechar"
                            >×</button>
                        </header>

                        <div class="player-storefront-purchase-v8__layout">
                            {{-- PRODUTO --}}
                            <aside class="player-storefront-purchase-v8__product">
                                <div class="player-storefront-purchase-v8__product-image">
                                    <template x-if="purchaseItem?.image_url">
                                        <img
                                            :src="purchaseItem.image_url"
                                            :alt="purchaseItem.name"
                                        >
                                    </template>

                                    <template x-if="!purchaseItem?.image_url">
                                        <span>◇</span>
                                    </template>
                                </div>

                                <div class="player-storefront-purchase-v8__product-copy">
                                    <span
                                        class="player-storefront-purchase-v8__classification"
                                        x-text="detailClassificationLine(purchaseItem)"
                                    ></span>

                                    <h4 x-text="purchaseItem?.name"></h4>

                                    <p
                                        x-show="purchaseItem?.description"
                                        x-text="purchaseItem?.description"
                                    ></p>
                                </div>

                                <div class="player-storefront-purchase-v8__product-meta">
                                    <div>
                                        <small>Quantidade</small>
                                        <strong>
                                            <span x-text="purchaseQuantity"></span>×
                                        </strong>
                                    </div>

                                    <div class="is-price">
                                        <small>Total</small>
                                        <strong
                                            x-text="priceInline(
                                                purchaseItem,
                                                purchaseQuantity
                                            )"
                                        ></strong>
                                    </div>
                                </div>
                            </aside>

                            {{-- NEGOCIAÇÃO --}}
                            <main class="player-storefront-purchase-v8__deal">
                                <div class="player-storefront-purchase-v8__deal-top">
                                    <div>
                                        <span class="player-storefront-purchase-v8__section-label">Pagamento</span>
                                        <h4>Escolha as moedas</h4>
                                    </div>

                                    <div class="player-storefront-payment-actions player-storefront-purchase-v8__actions">
                                        <button
                                            type="button"
                                            class="player-storefront-payment-action is-primary"
                                            @click="suggestPayment()"
                                        >
                                            Sugerir
                                        </button>

                                        <button
                                            type="button"
                                            class="player-storefront-payment-action"
                                            @click="clearPayment()"
                                        >
                                            Limpar
                                        </button>
                                    </div>
                                </div>

                                <div class="player-storefront-payment-grid player-storefront-payment-grid-v8">
                                    <template
                                        x-for="coin in coinDefinitions()"
                                        :key="'shop-payment-' + coin.key"
                                    >
                                        <div
                                            class="player-storefront-payment-coin player-storefront-payment-coin-v8"
                                            :class="{
                                                'is-unavailable': (parseInt(wallet?.[coin.key]) || 0) <= 0,
                                                'is-active': (parseInt(payment?.[coin.key]) || 0) > 0
                                            }"
                                        >
                                            <div class="player-storefront-payment-coin__head">
                                                <span class="player-storefront-payment-coin__identity">
                                                    <span
                                                        class="player-storefront-payment-coin__icon"
                                                        :class="'coin-' + coin.key"
                                                        aria-hidden="true"
                                                    >
                                                        <template x-if="coin.key === 'copper'">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="8.25"/>
                                                                <path d="M12 7.3v9.4M8.8 9.5h6.4M8.8 14.5h6.4"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="coin.key === 'silver'">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="8.25"/>
                                                                <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="coin.key === 'electrum'">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="8.25"/>
                                                                <path d="M8.3 12h7.4M12 8.3v7.4"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="coin.key === 'gold'">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="8.25"/>
                                                                <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="coin.key === 'platinum'">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="8.25"/>
                                                                <path d="M8.5 8.5h7v7h-7zM10.5 6.5h3M10.5 17.5h3"/>
                                                            </svg>
                                                        </template>
                                                    </span>

                                                    <span class="player-storefront-payment-coin__meta">
                                                        <strong x-text="coin.abbr"></strong>
                                                        <span x-text="coin.label"></span>
                                                    </span>
                                                </span>

                                                <span
                                                    class="player-storefront-payment-coin__badge"
                                                    x-text="
                                                        (parseInt(wallet?.[coin.key]) || 0) > 0
                                                            ? (parseInt(wallet?.[coin.key]) || 0) + ' disp.'
                                                            : 'sem moeda'
                                                    "
                                                ></span>
                                            </div>

                                            <div class="player-storefront-payment-coin-v8__stepper">
                                                <button
                                                    type="button"
                                                    @click="adjustPaymentCoin(coin.key, -1)"
                                                    :disabled="busyItemId !== null || (parseInt(payment?.[coin.key]) || 0) <= 0"
                                                    aria-label="Diminuir"
                                                >−</button>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    :max="wallet?.[coin.key] ?? 0"
                                                    step="1"
                                                    x-model.number="payment[coin.key]"
                                                    @input="clampPaymentCoin(coin.key)"
                                                    :disabled="busyItemId !== null || (parseInt(wallet?.[coin.key]) || 0) <= 0"
                                                >

                                                <button
                                                    type="button"
                                                    @click="adjustPaymentCoin(coin.key, 1)"
                                                    :disabled="
                                                        busyItemId !== null
                                                        || (parseInt(payment?.[coin.key]) || 0) >= (parseInt(wallet?.[coin.key]) || 0)
                                                    "
                                                    aria-label="Aumentar"
                                                >+</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="player-storefront-payment-status-v9">
                                    <div class="player-storefront-payment-settlement">
                                        <span class="player-storefront-payment-settlement__label">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" aria-hidden="true">
                                                <path d="M4 12h13"></path>
                                                <path d="m13 8 4 4-4 4"></path>
                                                <circle cx="5" cy="12" r="2.5"></circle>
                                            </svg>
                                            Você entrega
                                        </span>
                                        <strong x-text="paymentInline()"></strong>
                                        <small>Moedas colocadas no balcão</small>
                                    </div>

                                    <div
                                        class="player-storefront-payment-settlement"
                                        :class="{
                                            'is-missing': paymentRemainingCopper() > 0,
                                            'is-change': paymentRemainingCopper() <= 0 && paymentChangeCopper() > 0,
                                            'is-even': paymentRemainingCopper() <= 0 && paymentChangeCopper() <= 0
                                        }"
                                    >
                                        <span class="player-storefront-payment-settlement__label">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" aria-hidden="true">
                                                <path d="M20 12H7"></path>
                                                <path d="m11 8-4 4 4 4"></path>
                                                <circle cx="19" cy="12" r="2.5"></circle>
                                            </svg>
                                            <span
                                                x-text="
                                                    paymentRemainingCopper() > 0
                                                        ? 'Ainda falta'
                                                        : (
                                                            paymentChangeCopper() > 0
                                                                ? 'Troco'
                                                                : 'Pagamento exato'
                                                        )
                                                "
                                            ></span>
                                        </span>

                                        <strong
                                            x-text="
                                                paymentRemainingCopper() > 0
                                                    ? copperValueInline(paymentRemainingCopper())
                                                    : (
                                                        paymentChangeCopper() > 0
                                                            ? copperValueInline(paymentChangeCopper())
                                                            : 'Sem troco'
                                                    )
                                            "
                                        ></strong>

                                        <small
                                            x-text="
                                                paymentRemainingCopper() > 0
                                                    ? 'Complete o valor para fechar o negócio'
                                                    : (
                                                        paymentChangeCopper() > 0
                                                            ? 'Valor devolvido pelo lojista'
                                                            : 'O valor entregue fecha a conta'
                                                    )
                                            "
                                        ></small>
                                    </div>
                                </div>

                                <div
                                    x-show="walletValueCopper() < purchaseCostCopper()"
                                    x-cloak
                                    class="player-storefront-payment-error"
                                >
                                    Sua bolsa não possui valor suficiente para esta compra.
                                </div>

                                <div
                                    x-show="paymentError"
                                    x-cloak
                                    class="player-storefront-payment-error"
                                    x-text="paymentError"
                                ></div>

                                <footer class="player-storefront-purchase__footer player-storefront-purchase-v8__footer">
                                    <button
                                        type="button"
                                        class="player-storefront-purchase__cancel"
                                        @click="closePurchase()"
                                        :disabled="busyItemId !== null"
                                    >
                                        Voltar
                                    </button>

                                    <button
                                        type="button"
                                        class="player-storefront-purchase__confirm"
                                        @click="confirmPurchase()"
                                        :disabled="!purchaseCanConfirm"
                                    >
                                        <span
                                            x-text="
                                                busyItemId !== null
                                                    ? 'Concluindo...'
                                                    : 'Fechar negócio'
                                            "
                                        ></span>
                                    </button>
                                </footer>
                            </main>
                        </div>
                    </section>
                </div>

                {{-- TOAST --}}
                <div
                    x-show="notice"
                    x-cloak
                    x-transition
                    class="
                        player-storefront-notice
                    "
                    :class="{
                        'is-error':
                            noticeError
                    }"
                    x-text="notice"
                ></div>
            </section>
        </div>
    </template>
</div>