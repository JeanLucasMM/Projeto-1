<x-app-layout>

    <style>
        /* Remove o limite de largura e os paddings padrões apenas do container pai desta página */
        main:has(.npc-page-scope),
        .py-12:has(.npc-page-scope),
        .max-w-7xl:has(.npc-page-scope),
        .sm\:px-6:has(.npc-page-scope),
        .lg\:px-8:has(.npc-page-scope) {
            padding: 0 !important;
            max-width: 100% !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        /* Impede qualquer transbordo horizontal indesejado no layout */
        aside, .sidebar, body {
            overflow-x: hidden !important;
        }
        
        /* Acabamento editorial do Cofre, sem alterar as demais páginas. */
        html:has(.npc-page-scope) { scrollbar-width: thin; scrollbar-color: #a88b6a #efe9dc; }
        html:has(.npc-page-scope)::-webkit-scrollbar { width: 11px; }
        html:has(.npc-page-scope)::-webkit-scrollbar-track { background: #efe9dc; }
        html:has(.npc-page-scope)::-webkit-scrollbar-thumb { background: #ac8d6e; border: 3px solid #efe9dc; border-radius: 999px; }
        html:has(.npc-page-scope)::-webkit-scrollbar-thumb:hover { background: #875736; }
        .npc-page-scope { min-height: 100vh; background: radial-gradient(circle at 50% 12%, rgba(255,255,255,.22), transparent 48%); }
        .npc-page-scope .npc-vault-content { max-width: 1450px; }
        /* Três cartas ilustradas por linha, com espaço real para retratos e nomes. */
        .npc-page-scope .npc-vault-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; align-items: stretch; }
        @media (max-width: 1180px) { .npc-page-scope .npc-vault-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 680px) { .npc-page-scope .npc-vault-grid { grid-template-columns: minmax(0, 1fr); gap: 15px; } }
        .npc-page-scope .npc-vault-section-heading { color: #611e17; }
        .npc-page-scope .npc-vault-section-rule { border-color: rgba(140,98,57,.23); }

        /* V66.0.62 — Pastas compactas. Um único cartão, sem módulos aninhados. */
        .npc-page-scope .npc-folder-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            align-items: stretch;
        }
        .npc-page-scope .npc-folder-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 202px;
            padding: 21px 18px 17px;
            border: 1px solid #d9ccb7;
            border-radius: 17px;
            background: linear-gradient(150deg, #fffcf6 0%, #f5efe5 100%);
            box-shadow: 0 3px 11px rgba(64, 43, 28, .055), inset 0 1px 0 rgba(255,255,255,.72);
            cursor: pointer;
            isolation: isolate;
            /* Sem overflow:hidden: o menu de três pontos não pode ser cortado. */
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }
        .npc-page-scope .npc-folder-card::before {
            content: "";
            position: absolute;
            height: 4px;
            top: -1px;
            left: 17px;
            right: 17px;
            border-radius: 0 0 5px 5px;
            background: var(--npc-folder-accent, #8c6239);
            opacity: .9;
            pointer-events: none;
        }
        .npc-page-scope .npc-folder-card:hover,
        .npc-page-scope .npc-folder-card:focus-within {
            z-index: 4;
            transform: none;
            border-color: color-mix(in srgb, var(--npc-folder-accent, #8c6239) 43%, #d9ccb7);
            box-shadow: 0 9px 25px rgba(72, 44, 29, .105), inset 0 1px 0 rgba(255,255,255,.76);
        }
        .npc-page-scope .npc-folder-card.is-drag-over {
            transform: translateY(-4px) scale(1.01);
            border-color: #8c6239;
            box-shadow: 0 14px 30px rgba(107, 29, 20, .16), 0 0 0 3px rgba(140, 98, 57, .18);
        }
        .npc-page-scope .npc-folder-card__head {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 0;
        }
        .npc-page-scope .npc-folder-card__symbol {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: grid;
            place-items: center;
            border: 1px solid color-mix(in srgb, var(--npc-folder-accent, #8c6239) 17%, white);
            border-radius: 10px;
            color: var(--npc-folder-accent, #8c6239);
            background: color-mix(in srgb, var(--npc-folder-accent, #8c6239) 10%, #fffaf2);
        }
        .npc-page-scope .npc-folder-card__title {
            margin: 1px 0 0;
            font: 700 18px/1.15 Georgia, "Times New Roman", serif;
            letter-spacing: -.015em;
            color: #402b24;
            overflow-wrap: anywhere;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .npc-page-scope .npc-folder-card__subtitle {
            margin: 8px 0 0;
            color: #8d6b4a;
            font: italic 11.5px/1.45 ui-sans-serif, system-ui, sans-serif;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .npc-page-scope .npc-folder-card__menu-wrap {
            position: relative;
            flex-shrink: 0;
            z-index: 8;
            margin: -4px -5px 0 0;
        }
        .npc-page-scope .npc-folder-card__menu {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #9b7c5c;
            transition: color .15s ease, background .15s ease;
        }
        .npc-page-scope .npc-folder-card__menu:hover,
        .npc-page-scope .npc-folder-card__menu:focus-visible {
            outline: none;
            color: #6b1d14;
            background: #ece1d1;
        }
        .npc-page-scope .npc-folder-card__dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 25;
            width: 155px;
            overflow: hidden;
            border: 1px solid #d7c3aa;
            border-radius: 11px;
            background: #fffaf3;
            box-shadow: 0 17px 37px rgba(54, 29, 19, .18);
        }
        .npc-page-scope .npc-folder-card__people {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #e4d9ca;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 10px;
            min-width: 0;
        }
        /* A seta fica fora da faixa deslizante: nunca participa da ordenação. */
        .npc-page-scope .npc-folder-card__gallery {
            display:flex;
            align-items:center;
            flex:0 1 auto;
            min-width:0;
            gap:6px;
        }
        .npc-page-scope .npc-folder-card__viewport {
            --npc-thumb-size:54px;
            --npc-thumb-step:44px;
            position:relative;
            box-sizing:content-box;
            flex:0 0 auto;
            width:var(--npc-thumb-size); /* JS mede a largura disponível e configura até 5 retratos. */
            height:var(--npc-thumb-size);
            min-width:0;
            padding:11px 0 5px;
            margin-top:-11px;
            overflow:hidden;
            border-radius:10px;
            outline-offset:2px;
            cursor:pointer;
        }
        .npc-page-scope .npc-folder-card__viewport:focus-visible { outline:2px solid #8c6239; }
        .npc-page-scope .npc-folder-card__portraits {
            display:flex;
            align-items:center;
            width:max-content;
            height:var(--npc-thumb-size);
            min-width:0;
            will-change:transform;
            isolation:isolate;
        }
        .npc-page-scope .npc-folder-card__portrait[hidden] { display:none !important; }
        .npc-page-scope .npc-folder-card__next[hidden] { display:none !important; }
        .npc-page-scope .npc-folder-card__next {
            display:grid;
            place-items:center;
            flex:0 0 28px;
            width:28px;
            height:28px;
            padding:0;
            border:1px solid rgba(140,98,57,.24);
            border-radius:50%;
            background:rgba(255,251,244,.92);
            color:#855f3d;
            box-shadow:0 2px 7px rgba(67,42,24,.06);
            cursor:pointer;
            transition:background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
        }
        .npc-page-scope .npc-folder-card__next:hover,
        .npc-page-scope .npc-folder-card__next:focus-visible {
            outline:2px solid rgba(140,98,57,.34);
            outline-offset:2px;
            color:#6b1d14;
            background:#f2e6d6;
            border-color:rgba(107,29,20,.36);
            transform:translateX(2px);
        }
        .npc-page-scope .npc-folder-card__portrait {
            position: relative;
            flex: 0 0 54px;
            width: 54px;
            height: 54px;
            margin-left: -10px;
            /* Retrato sem moldura bege: transparência revela o mesmo fundo da pasta. */
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            transition: transform .22s ease, filter .22s ease;
            isolation: isolate;
        }
        .npc-page-scope .npc-folder-card__portrait--initials {
            display: grid;
            place-items: center;
            background: rgba(140, 98, 57, .09);
            border-radius: 12px;
        }
        /* A primeira posição da janela não muda mesmo após uma rotação. */
        .npc-page-scope .npc-folder-card__portrait[data-visible-slot="1"] { margin-left:0; }
        .npc-page-scope .npc-folder-card__portrait[data-visible-slot="6"] { z-index: 0; }
        .npc-page-scope .npc-folder-card__portrait[data-visible-slot="1"] { z-index: 1; }
        .npc-page-scope .npc-folder-card__portrait[data-visible-slot="2"] { z-index: 2; }
        .npc-page-scope .npc-folder-card__portrait[data-visible-slot="3"] { z-index: 3; }
        .npc-page-scope .npc-folder-card__portrait[data-visible-slot="4"] { z-index: 4; }
        .npc-page-scope .npc-folder-card__portrait[data-visible-slot="5"] { z-index: 5; }
        .npc-page-scope .npc-folder-card__portrait:hover {
            z-index: 12 !important;
            transform: translateY(-4px) scale(1.045);
            filter: saturate(1.05);
        }
        .npc-page-scope .npc-folder-card__portrait img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center bottom;
            filter: drop-shadow(0 5px 5px rgba(58, 39, 27, .13));
            transition: transform .22s ease, filter .22s ease;
        }
        .npc-page-scope .npc-folder-card__portrait:hover img {
            transform: scale(1.035);
            filter: drop-shadow(0 9px 8px rgba(58, 39, 27, .20));
        }
        .npc-page-scope .npc-folder-card__empty {
            font: italic 11px/1.4 ui-sans-serif, system-ui, sans-serif;
            color: #a18a72;
            padding: 8px 0;
        }
        .npc-page-scope .npc-folder-card__count {
            flex-shrink: 0;
            text-align: right;
            padding-bottom: 3px;
            line-height: 1.05;
        }
        .npc-page-scope .npc-folder-card__count strong {
            display: block;
            color: #6b1d14;
            font: 700 23px/1 Georgia, serif;
            font-variant-numeric: tabular-nums;
        }
        .npc-page-scope .npc-folder-card__count small {
            display: block;
            color: #9d7955;
            font: 700 9px/1.3 ui-sans-serif, system-ui, sans-serif;
            text-transform: uppercase;
            letter-spacing: .12em;
            margin-top: 3px;
        }
        @media (min-width: 1211px) {
            .npc-page-scope .npc-folder-card__viewport { --npc-thumb-size:49px; --npc-thumb-step:39px; }
            .npc-page-scope .npc-folder-card__portrait { flex-basis:49px; width:49px; height:49px; }
            .npc-page-scope .npc-folder-card__next { flex-basis:27px; width:27px; height:27px; }
        }
        @media (max-width: 1210px) {
            .npc-page-scope .npc-folder-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 925px) {
            .npc-page-scope .npc-folder-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 600px) {
            .npc-page-scope .npc-folder-grid { grid-template-columns: minmax(0, 1fr); gap: 13px; }
            .npc-page-scope .npc-folder-card { min-height: 177px; }
        }
        @media (max-width: 360px) {
            .npc-page-scope .npc-folder-card__viewport { --npc-thumb-size:43px; --npc-thumb-step:33px; }
            .npc-page-scope .npc-folder-card__portrait { flex-basis:43px; width:43px; height:43px; margin-left:-10px; }
            .npc-page-scope .npc-folder-card__next { flex-basis:25px; width:25px; height:25px; }
            .npc-page-scope .npc-folder-card { padding-left: 15px; padding-right: 15px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .npc-page-scope .npc-folder-card,
            .npc-page-scope .npc-folder-card__portrait {
                transition: none !important;
            }
        }


        /* V66.0.94 — topo editorial, integrado à página: sem header fixo ou caixa externa. */
        .npc-page-scope .npc-workspace-head {
            margin: 0 0 clamp(32px, 4vw, 48px);
            padding: 1px 0 25px;
            border-bottom: 1px solid rgba(142, 105, 72, .19);
        }
        .npc-page-scope .npc-workspace-head__intro {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            min-width: 0;
            margin-bottom: clamp(18px, 2.3vw, 24px);
        }
        .npc-page-scope .npc-workspace-head__identity { min-width: 0; }
        .npc-page-scope .npc-workspace-head__title {
            margin: 0;
            color: #6b2119;
            font: 700 clamp(27px, 3.05vw, 39px)/1.12 Georgia, "Times New Roman", serif;
            letter-spacing: -.042em;
        }
        .npc-page-scope .npc-workspace-head__stroke {
            display: block;
            width: 72px;
            height: 3px;
            margin-top: 13px;
            border-radius: 3px;
            background: linear-gradient(90deg, #79352a 0%, #c8ab88 82%, transparent 100%);
        }
        .npc-page-scope .npc-workspace-head__actions {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-shrink: 0;
        }
        .npc-page-scope .npc-workspace-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 41px;
            padding: 0 16px;
            border: 1px solid #d8c4aa;
            border-radius: 12px;
            background: #fbf7f0;
            color: #6b2119;
            font: 700 12px/1.2 Georgia, "Times New Roman", serif;
            white-space: nowrap;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(65, 40, 26, .04);
            transition: background .18s ease, border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }
        .npc-page-scope .npc-workspace-action svg { width: 16px; height: 16px; flex: 0 0 16px; }
        .npc-page-scope .npc-workspace-action--primary {
            background: #72261c;
            color: #fff8f0;
            border-color: #72261c;
        }
        .npc-page-scope .npc-workspace-action:hover {
            transform: translateY(-1px);
            background: #f1e6d7;
            border-color: #c4aa89;
            box-shadow: 0 5px 13px rgba(65, 40, 26, .09);
        }
        .npc-page-scope .npc-workspace-action--primary:hover {
            background: #5e1c15;
            border-color: #5e1c15;
        }
        .npc-page-scope .npc-workspace-action:focus-visible,
        .npc-page-scope .npc-workspace-search-button:focus-visible,
        .npc-page-scope .npc-workspace-search:focus-within,
        .npc-page-scope .npc-workspace-select:focus-within {
            outline: 2px solid #aa8258;
            outline-offset: 2px;
        }
        .npc-page-scope .npc-workspace-tools {
            display: grid;
            grid-template-columns: minmax(250px, 1fr) auto;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .npc-page-scope .npc-workspace-tools__search-line {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }
        .npc-page-scope .npc-workspace-search {
            display: flex;
            align-items: center;
            gap: 11px;
            flex: 1 1 0%;
            min-width: 0;
            min-height: 44px;
            border: 1px solid #d9c8b2;
            border-radius: 13px;
            background: rgba(255,252,246,.68);
            padding: 0 15px;
            color: #9d8060;
            transition: border-color .18s, background .18s;
        }
        .npc-page-scope .npc-workspace-search:focus-within { background: #fffdf8; border-color: #ad8965; }
        .npc-page-scope .npc-workspace-search svg { flex: 0 0 17px; width: 17px; height: 17px; }
        .npc-page-scope .npc-workspace-search input {
            display: block;
            flex: 1;
            width: 100%;
            min-width: 0;
            height: 42px;
            padding: 0;
            border: 0;
            outline: 0;
            box-shadow: none;
            background: transparent;
            color: #48332a;
            font-size: 13px;
            font-weight: 400;
        }
        .npc-page-scope .npc-workspace-search input:focus { border: 0; outline: none; box-shadow: none; --tw-ring-shadow: 0 0 #0000; }
        .npc-page-scope .npc-workspace-search input::placeholder { color: #ab947b; }
        .npc-page-scope .npc-workspace-search-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 15px;
            border: 1px solid #c4aa89;
            border-radius: 12px;
            background: #ede2d1;
            color: #704a2e;
            font: 700 12px/1 Georgia, serif;
            cursor: pointer;
            transition: background .18s, border-color .18s;
        }
        .npc-page-scope .npc-workspace-search-button:hover { background: #e4d4bf; border-color: #b4946b; }
        .npc-page-scope .npc-workspace-search-button svg { width: 15px; height: 15px; }
        .npc-page-scope .npc-workspace-tools__filters {
            display: grid;
            grid-template-columns: minmax(140px, 175px) minmax(123px, 150px);
            gap: 9px;
            align-items: center;
        }
        .npc-page-scope .npc-workspace-select {
            position: relative;
            display: flex;
            align-items: center;
            min-width: 0;
            height: 44px;
            border: 1px solid #e0d1bd;
            border-radius: 12px;
            background: rgba(255,252,247,.40);
            color: #634d3e;
            transition: border-color .18s, background .18s;
        }
        .npc-page-scope .npc-workspace-select:hover { background: rgba(255,252,247,.80); border-color: #c9b296; }
        .npc-page-scope .npc-workspace-select select {
            display: block;
            width: 100%;
            min-width: 0;
            height: 100%;
            padding: 0 29px 0 13px;
            appearance: none;
            -webkit-appearance: none;
            background: transparent;
            border: 0;
            border-radius: inherit;
            box-shadow: none;
            outline: none;
            color: #604635;
            font-size: 12px;
            line-height: 1.2;
            cursor: pointer;
            text-overflow: ellipsis;
        }
        .npc-page-scope .npc-workspace-select select:focus { border: 0; box-shadow: none; outline: 0; --tw-ring-shadow: 0 0 #0000; }
        .npc-page-scope .npc-workspace-select > svg {
            position: absolute;
            right: 10px;
            width: 13px;
            height: 13px;
            pointer-events: none;
            color: #8a674d;
        }
        @media (max-width: 970px) {
            .npc-page-scope .npc-workspace-tools { grid-template-columns: minmax(0, 1fr); gap: 10px; }
            .npc-page-scope .npc-workspace-tools__filters {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .npc-page-scope .npc-workspace-head { padding-bottom: 18px; margin-bottom: 30px; }
            .npc-page-scope .npc-workspace-head__intro { gap: 12px; align-items: flex-start; }
            .npc-page-scope .npc-workspace-head__title { font-size: clamp(24px, 6vw, 31px); }
            .npc-page-scope .npc-workspace-head__actions { gap: 6px; }
            .npc-page-scope .npc-workspace-action { width: 39px; min-width: 39px; height: 39px; min-height: 39px; padding: 0; border-radius: 11px; }
            .npc-page-scope .npc-workspace-action span { display: none; }
            .npc-page-scope .npc-workspace-head__stroke { margin-top: 9px; }
            .npc-page-scope .npc-workspace-search-button { padding: 0 12px; }
        }
        @media (max-width: 385px) {
            .npc-page-scope .npc-workspace-head__title { font-size: 23px; }
            .npc-page-scope .npc-workspace-search-button { min-width: 42px; width: 42px; padding: 0; }
            .npc-page-scope .npc-workspace-search-button span { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .npc-page-scope .npc-workspace-action,
            .npc-page-scope .npc-workspace-search-button,
            .npc-page-scope .npc-workspace-search,
            .npc-page-scope .npc-workspace-select { transition: none; }
            .npc-page-scope .npc-workspace-action:hover { transform: none; }
        }


        /* V66.0.95 — refinamento editorial do Cofre (sem modificar lógica/JS). */
        .npc-page-scope .npc-vault-content {
            width: 100%;
            max-width: 1510px;
            padding-top: clamp(16px, 1.8vw, 24px);
            padding-bottom: 52px;
        }
        .npc-page-scope .npc-workspace-head {
            margin-bottom: clamp(18px, 2vw, 24px);
            padding-bottom: 0;
            border-bottom: 0;
        }
        .npc-page-scope .npc-workspace-head__intro {
            margin-bottom: clamp(14px, 1.6vw, 18px);
        }
        .npc-page-scope .npc-workspace-head__title {
            color: #65221b;
            font-size: clamp(30px, 3vw, 42px);
            line-height: 1.08;
            letter-spacing: -.047em;
        }
        .npc-page-scope .npc-workspace-head__stroke {
            width: 46px;
            height: 3px;
            margin-top: 10px;
            background: linear-gradient(90deg, #7b3327, #ba986d 78%, transparent);
            opacity: .78;
        }
        .npc-page-scope .npc-workspace-head__actions { gap: 10px; }
        .npc-page-scope .npc-workspace-action {
            border-radius: 11px;
            min-height: 40px;
            padding-inline: 15px;
            background: #fbf6ee;
            box-shadow: 0 1px 3px rgba(79, 47, 32, .035);
        }
        .npc-page-scope .npc-workspace-action--primary {
            background: #72261c;
            border-color: #72261c;
            box-shadow: 0 3px 9px rgba(93, 30, 22, .10);
        }
        /* Search + submit are a single understated toolbar control. */
        .npc-page-scope .npc-workspace-tools {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: clamp(10px, 1.4vw, 17px);
        }
        .npc-page-scope .npc-workspace-tools__search-line {
            gap: 0;
            min-width: 0;
            min-height: 46px;
            border: 1px solid #d8c6ad;
            background: rgba(255, 252, 246, .78);
            border-radius: 13px;
            box-shadow: 0 2px 7px rgba(60, 37, 23, .035);
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .npc-page-scope .npc-workspace-tools__search-line:focus-within {
            background: #fffaf3;
            border-color: #b59671;
            box-shadow: 0 0 0 3px rgba(143, 105, 67, .08);
        }
        .npc-page-scope .npc-workspace-search,
        .npc-page-scope .npc-workspace-search:focus-within {
            border: 0;
            background: transparent;
            box-shadow: none;
            outline: none;
            min-height: 44px;
            border-radius: 13px 0 0 13px;
        }
        .npc-page-scope .npc-workspace-search input { height: 44px; font-size: 13px; }
        .npc-page-scope .npc-workspace-search-button {
            min-height: 31px;
            height: 31px;
            padding: 0 14px;
            margin-right: 7px;
            background: #eee3d3;
            border: 1px solid #e2d1ba;
            border-radius: 9px;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11px;
            font-weight: 650;
            color: #744e32;
            transition: background .18s, color .18s;
        }
        .npc-page-scope .npc-workspace-search-button:hover { background: #e5d2ba; border-color: #d6b99b; }
        .npc-page-scope .npc-workspace-tools__filters {
            grid-template-columns: minmax(140px, 176px) minmax(126px, 154px);
            gap: 10px;
        }
        .npc-page-scope .npc-workspace-select {
            min-height: 46px;
            height: 46px;
            background: rgba(255, 252, 246, .36);
            border-color: rgba(168, 136, 96, .23);
            border-radius: 12px;
        }
        .npc-page-scope .npc-workspace-select:hover,
        .npc-page-scope .npc-workspace-select:focus-within { background: rgba(255,252,246,.75); }
        .npc-page-scope .npc-workspace-select select { font-size: 11.5px; }

        /* Section headings are lighter, readable, and visually independent from cards. */
        .npc-page-scope .npc-vault-section-bar {
            position: relative;
            gap: 12px;
            padding-bottom: 11px;
            margin-bottom: 16px;
            border-bottom: 1px solid rgba(134, 97, 65, .16);
        }
        .npc-page-scope .npc-vault-section-bar > div:first-child {
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        
        
        .npc-page-scope .npc-vault-section-bar h2 {
            color: #67251e;
            font-size: clamp(23px, 2.1vw, 28px);
            margin: 0;
            line-height: 1.15;
            letter-spacing: -.02em;
        }
        .npc-page-scope .npc-vault-section-bar p {
            font-size: 11.5px;
            line-height: 1.45;
            color: #916d4e;
            margin: 4px 0 0;
        }
        
        .npc-page-scope .npc-vault-library {
            position: relative;
            margin-bottom: clamp(22px, 2.2vw, 28px);
        }
        .npc-page-scope .npc-vault-library::before {
            content: "";
            position: absolute;
            inset: -12px -12px auto;
            height: 150px;
            pointer-events: none;
            background: radial-gradient(ellipse at 8% 55%, rgba(255,255,255,.22), transparent 65%);
            opacity: .58;
        }
        .npc-page-scope .npc-vault-library > * { position: relative; }
        .npc-page-scope .npc-vault-folders { margin-top: 0; }
        .npc-page-scope .npc-vault-library .npc-vault-grid { margin-top: 0; }
        .npc-page-scope .npc-vault-folders .npc-folder-grid { margin-top: 0; }

        /* NPC cards: only subtle surface/border polish; never touch portrait crop/zoom logic. */
        .npc-page-scope .npc-vault-grid { gap: clamp(15px, 1.55vw, 22px); }
        .npc-page-scope .npc-vault-grid .npc-vault-card {
            border-color: #ddcbb5;
            box-shadow: 0 5px 15px rgba(65, 38, 26, .065);
            transition: border-color .22s ease, box-shadow .22s ease, transform .22s ease;
        }
        .npc-page-scope .npc-vault-grid .npc-vault-card:hover,
        .npc-page-scope .npc-vault-grid .npc-vault-card:focus-within {
            border-color: #c7a98a;
            box-shadow: 0 12px 24px rgba(65, 38, 26, .115);
        }

        /* Folder cards: consistent hover, softer surface and legible supporting text. */
        .npc-page-scope .npc-folder-grid { gap: clamp(14px, 1.55vw, 22px); }
        .npc-page-scope .npc-folder-card {
            min-height: 193px;
            padding: 19px 17px 16px;
            border-color: #d9c8b0;
            background: linear-gradient(145deg, #fffcf7, #f7f1e6);
            border-radius: 16px;
            box-shadow: 0 4px 13px rgba(65, 39, 25, .055), inset 0 1px 0 rgba(255,255,255,.76);
            transition: transform .19s ease, border-color .19s ease, box-shadow .19s ease;
        }
        .npc-page-scope .npc-folder-card:hover,
        .npc-page-scope .npc-folder-card:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 11px 24px rgba(65, 39, 25, .105), inset 0 1px 0 rgba(255,255,255,.8);
        }
        .npc-page-scope .npc-folder-card.is-drag-over { transform: translateY(-4px) scale(1.01); }
        .npc-page-scope .npc-folder-card::before { height: 3px; opacity: .95; }
        .npc-page-scope .npc-folder-card__title { color: #462e24; font-size: 18px; }
        .npc-page-scope .npc-folder-card__subtitle { color: #856648; font-size: 11.5px; }
        .npc-page-scope .npc-folder-card__count strong { font-size: 22px; }
        .npc-page-scope .npc-folder-card__count small { font-size: 9px; letter-spacing: .095em; }
        .npc-page-scope .npc-folder-card__people { border-top-color: rgba(135, 104, 74, .18); }

        @media (min-width: 1720px) {
            .npc-page-scope .npc-vault-content { max-width: 1540px; }
        }
        @media (max-width: 1050px) {
            .npc-page-scope .npc-workspace-tools { grid-template-columns: minmax(0, 1fr); gap: 11px; }
            .npc-page-scope .npc-workspace-tools__filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .npc-page-scope .npc-vault-content { padding-top: 22px; }
            .npc-page-scope .npc-workspace-head { margin-bottom: 30px; }
            .npc-page-scope .npc-workspace-head__title { font-size: clamp(24px, 6vw, 30px); }
            .npc-page-scope .npc-workspace-action { width: 39px; min-width: 39px; height: 39px; padding: 0; }
            .npc-page-scope .npc-workspace-search-button { margin-right: 6px; }
            .npc-page-scope .npc-vault-section-bar { padding-bottom: 12px; gap: 8px; }
            .npc-page-scope .npc-vault-section-bar > div:first-child { gap: 9px; }
            
            .npc-page-scope .npc-vault-section-bar h2 { font-size: 21px; }
            .npc-page-scope .npc-vault-section-bar p { font-size: 10.5px; }
            .npc-page-scope .npc-folder-card { min-height: 177px; }
        }
        @media (max-width: 375px) {
            .npc-page-scope .npc-vault-section-bar h2 { font-size: 19px; }
            
            .npc-page-scope .npc-workspace-search-button { padding-inline: 7px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .npc-page-scope .npc-vault-grid .npc-vault-card,
            .npc-page-scope .npc-folder-card,
            .npc-page-scope .npc-workspace-tools__search-line { transition: none !important; }
            .npc-page-scope .npc-folder-card:hover,
            .npc-page-scope .npc-folder-card:focus-within { transform: none; }
        }


        /* V66.1.01 — retratos das pastas: transparência integrada ao pergaminho. */
        .npc-page-scope .npc-folder-card__portrait { background:transparent; border:0; box-shadow:none; }
        .npc-page-scope .npc-folder-card__portrait img { background:transparent; }
        .npc-page-scope .npc-folder-card__portrait img.npc-thumb-white-bg { filter:none; }
        .npc-page-scope .npc-folder-card__portrait:hover img.npc-thumb-white-bg { filter:none; }

        /* V66.1.03 — composição de contornos suaves; nenhuma mudança nos cards ou na lógica. */
        .npc-page-scope .npc-workspace-head__stroke {
            width: 48px;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg, #8d4a37, #cbaa7b 82%, transparent);
        }
        /* A busca deixa de parecer mais uma caixa retangular na página. */
        .npc-page-scope .npc-workspace-tools__search-line {
            min-height: 49px;
            border-radius: 999px;
            border-color: rgba(180, 145, 108, .39);
            background: rgba(255,252,246,.84);
            box-shadow: 0 5px 18px rgba(75,45,29,.045), inset 0 1px 0 rgba(255,255,255,.58);
        }
        .npc-page-scope .npc-workspace-tools__search-line:focus-within {
            background: #fffdf8;
            border-color: #af8b68;
            box-shadow: 0 0 0 3px rgba(143,105,67,.075), 0 6px 19px rgba(75,45,29,.055);
        }
        .npc-page-scope .npc-workspace-search,
        .npc-page-scope .npc-workspace-search:focus-within {
            padding-left: 20px;
            padding-right: 8px;
            min-height: 47px;
            border-radius: 999px 0 0 999px;
        }
        .npc-page-scope .npc-workspace-search input { height: 47px; }
        .npc-page-scope .npc-workspace-search-button {
            display: grid;
            place-items: center;
            flex: 0 0 37px;
            width: 37px;
            min-width: 37px;
            height: 37px;
            min-height: 37px;
            margin-right: 6px;
            padding: 0;
            border: 1px solid rgba(160,120,78,.20);
            border-radius: 50%;
            background: #ecddca;
            color: #754630;
            box-shadow: 0 1px 3px rgba(95,61,39,.045);
            transition: background .18s ease, color .18s ease, transform .18s ease;
        }
        .npc-page-scope .npc-workspace-search-button svg { width: 17px; height: 17px; }
        .npc-page-scope .npc-workspace-search-button:hover {
            background: #6b2119;
            border-color: #6b2119;
            color: #fff8f0;
            transform: scale(1.035);
        }
        .npc-page-scope .npc-workspace-select {
            height: 49px;
            min-height: 49px;
            border-radius: 999px;
            border-color: rgba(174, 143, 110, .29);
            background: rgba(255,251,245,.37);
            box-shadow: none;
        }
        .npc-page-scope .npc-workspace-select:hover { background: rgba(255,251,245,.83); }
        .npc-page-scope .npc-workspace-select select {
            height: 100%;
            border-radius: 999px;
            padding-left: 17px;
            padding-right: 33px;
        }
        .npc-page-scope .npc-workspace-select > svg { right: 14px; }
        .npc-page-scope .npc-workspace-action {
            min-height: 40px;
            padding-inline: 18px;
            border-radius: 999px;
            box-shadow: 0 2px 9px rgba(75,43,30,.055);
        }
        .npc-page-scope .npc-workspace-action--primary { box-shadow: 0 3px 12px rgba(100,38,29,.12); }
        /* Gavetas menos geométricas: curvas amplas, borda menos presente e acento oval. */
        
        
        
        
        
        
        
        
        @media (max-width: 640px) {
            .npc-page-scope .npc-workspace-action { width: 39px; min-width: 39px; height: 39px; padding: 0; }
            .npc-page-scope .npc-workspace-tools__search-line,
            .npc-page-scope .npc-workspace-select { min-height: 45px; height: 45px; }
            .npc-page-scope .npc-workspace-search,
            .npc-page-scope .npc-workspace-search:focus-within { min-height: 43px; padding-left: 15px; }
            .npc-page-scope .npc-workspace-search input { height: 43px; }
            .npc-page-scope .npc-workspace-search-button {
                width: 34px; min-width: 34px; height: 34px; min-height: 34px;
                flex-basis: 34px; margin-right: 5px;
            }
            
            
            
        }
        @media (prefers-reduced-motion: reduce) {
            .npc-page-scope .npc-workspace-search-button { transition: none; }
            .npc-page-scope .npc-workspace-search-button:hover { transform: none; }
        }

        /* V66.1.04 — compactação óptica de gavetas e ferramentas (apenas CSS).
           Mantém abertura/fechamento, estado salvo, cards e arraste da V66.1.03. */
        .npc-page-scope .npc-vault-content {
            padding-top: clamp(14px, 1.6vw, 22px);
        }
        .npc-page-scope .npc-workspace-head {
            margin-bottom: clamp(17px, 1.8vw, 22px);
        }
        .npc-page-scope .npc-workspace-head__intro {
            margin-bottom: clamp(11px, 1.35vw, 16px);
        }
        .npc-page-scope .npc-workspace-head__title {
            font-size: clamp(29px, 2.4vw, 37px);
            line-height: 1.12;
        }
        .npc-page-scope .npc-workspace-head__stroke {
            width: 40px;
            height: 2px;
            margin-top: 8px;
            opacity: .85;
        }
        .npc-page-scope .npc-workspace-action {
            min-height: 36px;
            padding-inline: 15px;
            font-size: 11px;
            box-shadow: 0 2px 7px rgba(75,43,30,.04);
        }
        .npc-page-scope .npc-workspace-tools { gap: 12px; }
        .npc-page-scope .npc-workspace-tools__search-line {
            min-height: 41px;
            border-color: rgba(174,142,107,.31);
            background: rgba(255,252,248,.76);
            box-shadow: 0 2px 10px rgba(75,45,29,.025);
        }
        .npc-page-scope .npc-workspace-search,
        .npc-page-scope .npc-workspace-search:focus-within {
            min-height: 39px;
            padding-left: 17px;
            padding-right: 8px;
        }
        .npc-page-scope .npc-workspace-search input {
            height: 39px;
            font-size: 12.5px;
        }
        .npc-page-scope .npc-workspace-search-button {
            width: 30px;
            min-width: 30px;
            height: 30px;
            min-height: 30px;
            flex-basis: 30px;
            margin-right: 5px;
            background: rgba(231,213,190,.70);
        }
        .npc-page-scope .npc-workspace-search-button svg { width: 15px; height: 15px; }
        .npc-page-scope .npc-workspace-select {
            height: 41px;
            min-height: 41px;
            border-color: rgba(174,143,110,.23);
        }
        .npc-page-scope .npc-workspace-select select {
            font-size: 11px;
            padding-left: 15px;
        }
        .npc-page-scope .npc-workspace-tools__filters { gap: 8px; }
        
        
        
        
        
        
        
        
        
        @media (max-width: 640px) {
            .npc-page-scope .npc-vault-content { padding-top: 13px; }
            .npc-page-scope .npc-workspace-head { margin-bottom: 17px; }
            .npc-page-scope .npc-workspace-head__intro { margin-bottom: 10px; }
            .npc-page-scope .npc-workspace-head__title { font-size: clamp(24px, 6vw, 29px); }
            .npc-page-scope .npc-workspace-action {
                width: 35px; min-width: 35px; height: 35px; min-height: 35px;
                padding: 0;
            }
            .npc-page-scope .npc-workspace-tools__search-line,
            .npc-page-scope .npc-workspace-select { height: 40px; min-height: 40px; }
            .npc-page-scope .npc-workspace-search,
            .npc-page-scope .npc-workspace-search:focus-within { min-height: 38px; padding-left: 14px; }
            .npc-page-scope .npc-workspace-search input { height: 38px; }
            .npc-page-scope .npc-workspace-search-button {
                width: 29px; height: 29px; min-width: 29px; min-height: 29px; flex-basis: 29px;
            }
            
            
            
            
            
            
            
        }


        /* V66.1.05 — topo e gavetas menos rígidos, com casulos visuais. */
        .npc-page-scope .npc-workspace-head {
            gap: 12px;
        }
        .npc-page-scope .npc-workspace-head__intro {
            align-items: end;
            margin-bottom: 12px;
        }
        .npc-page-scope .npc-workspace-head__actions {
            gap: 9px;
        }
        .npc-page-scope .npc-workspace-tools {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 12px;
            padding: 9px 10px 9px 12px;
            border: 1px solid rgba(177, 141, 104, .18);
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(255,252,247,.92), rgba(247,238,226,.58));
            box-shadow: 0 8px 24px rgba(78,48,31,.04), inset 0 1px 0 rgba(255,255,255,.55);
        }
        .npc-page-scope .npc-workspace-tools__search-line {
            min-height: 44px;
            padding-left: 3px;
            border-color: rgba(174,142,107,.23);
            background: rgba(255,255,255,.72);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.72);
        }
        .npc-page-scope .npc-workspace-search,
        .npc-page-scope .npc-workspace-search:focus-within {
            min-height: 42px;
        }
        .npc-page-scope .npc-workspace-search input {
            height: 42px;
        }
        .npc-page-scope .npc-workspace-tools__filters {
            gap: 9px;
        }
        .npc-page-scope .npc-workspace-select {
            min-width: 160px;
            background: rgba(255,250,244,.9);
        }
        .npc-page-scope .npc-workspace-select--sort {
            min-width: 146px;
        }

        
        
        
        
        
        
        

        @media (max-width: 960px) {
            .npc-page-scope .npc-workspace-tools {
                grid-template-columns: minmax(0, 1fr);
                padding: 10px;
                border-radius: 24px;
            }
            .npc-page-scope .npc-workspace-tools__filters {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .npc-page-scope .npc-workspace-head__intro {
                align-items: flex-start;
            }
            .npc-page-scope .npc-workspace-tools {
                padding: 8px;
                border-radius: 20px;
                gap: 9px;
            }
            .npc-page-scope .npc-workspace-tools__search-line,
            .npc-page-scope .npc-workspace-select {
                min-height: 40px;
                height: 40px;
            }
            .npc-page-scope .npc-workspace-search,
            .npc-page-scope .npc-workspace-search:focus-within {
                min-height: 38px;
            }
            .npc-page-scope .npc-workspace-search input {
                height: 38px;
            }
            .npc-page-scope .npc-workspace-select {
                min-width: 0;
            }
            
            
            
        }


        /* V66.1.06 — Biblioteca e Pastas novamente abertas, sem gavetas ou caixas externas. */
        .npc-page-scope .npc-vault-open-section {
            display: block;
            margin: 0 0 clamp(27px, 3vw, 38px);
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            overflow: visible;
        }
        .npc-page-scope .npc-vault-folders.npc-vault-open-section { margin-top: 0; }
        .npc-page-scope .npc-vault-open-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
            gap: 14px;
            margin: 0 0 15px;
            padding: 0 1px;
            border: 0;
            background: none;
            box-shadow: none;
        }
        .npc-page-scope .npc-vault-open-heading > div:first-child { min-width: 0; }
        .npc-page-scope .npc-vault-open-heading h2 {
            margin: 0;
            color: #67251e;
            font: 700 clamp(22px, 2vw, 26px)/1.15 Georgia, "Times New Roman", serif;
            letter-spacing: -.024em;
        }
        .npc-page-scope .npc-vault-open-heading p {
            margin: 4px 0 0;
            color: #8d694a;
            font: italic 11px/1.4 ui-sans-serif, system-ui, sans-serif;
        }
        .npc-page-scope .npc-vault-open-count {
            flex: 0 0 auto;
            padding: 5px 11px;
            background: rgba(255,251,244,.58);
            border: 1px solid rgba(181,147,106,.31);
            border-radius: 999px;
            color: #7b5137;
            font: 700 10.5px/1.1 Georgia, "Times New Roman", serif;
            white-space: nowrap;
            box-shadow: none;
            font-variant-numeric: tabular-nums;
        }
        .npc-page-scope .npc-vault-open-empty {
            padding: 20px 22px;
            border: 1px dashed rgba(148,111,79,.30);
            border-radius: 16px;
            background: rgba(255,251,244,.34);
            color: #8f7056;
            font-size: 12px;
        }
        .npc-page-scope .npc-vault-open-empty button {
            color: #6b2119;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        @media (max-width: 640px) {
            .npc-page-scope .npc-vault-open-section { margin-bottom: 25px; }
            .npc-page-scope .npc-vault-open-heading { margin-bottom: 13px; gap: 8px; }
            .npc-page-scope .npc-vault-open-heading h2 { font-size: clamp(19px, 5vw, 23px); }
            .npc-page-scope .npc-vault-open-heading p { font-size: 10px; }
            .npc-page-scope .npc-vault-open-count { padding: 5px 8px; font-size: 10px; }
        }

        /* V66.1.07 — duas formas de visualizar, sem duplicar cards nem criar gavetas. */
        .npc-page-scope [data-npc-view-section][hidden],
        .npc-page-scope .npc-vault-view-tabs[hidden] { display: none !important; }
        .npc-page-scope .npc-workspace-action--view {
            color: #795539;
            background: rgba(255,252,247,.52);
            border-color: rgba(162,125,90,.34);
            box-shadow: none;
        }
        .npc-page-scope .npc-workspace-action--view:hover {
            color: #6b2119;
            border-color: #c9a987;
            background: #faf3e9;
        }
        .npc-page-scope .npc-workspace-action--view[aria-pressed="true"] {
            color: #6b2119;
            background: #f3e6d7;
            border-color: #c5aa89;
        }
        .npc-page-scope .npc-vault-view-tabs {
            display: flex;
            align-items: center;
            gap: clamp(12px, 1.8vw, 25px);
            margin: -2px 0 20px;
            border-bottom: 1px solid rgba(146,109,72,.17);
            overflow-x: auto;
            scrollbar-width: thin;
        }
        .npc-page-scope .npc-vault-view-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            min-height: 39px;
            padding: 2px 3px 10px;
            border: 0;
            border-bottom: 2px solid transparent;
            background: transparent;
            color: #8e6b50;
            font-size: 12px;
            font-weight: 650;
            cursor: pointer;
            transition: color .18s ease, border-color .18s ease;
        }
        .npc-page-scope .npc-vault-view-tab:hover { color: #6b2119; }
        .npc-page-scope .npc-vault-view-tab[aria-selected="true"] {
            color: #6b2119;
            border-bottom-color: #7c2d22;
        }
        .npc-page-scope .npc-vault-view-tab span {
            display: grid;
            place-items: center;
            min-width: 22px;
            min-height: 20px;
            padding: 0 6px;
            border: 1px solid rgba(163,124,91,.17);
            border-radius: 8px;
            background: rgba(244,235,224,.75);
            color: #845e41;
            font-size: 10px;
            font-variant-numeric: tabular-nums;
        }
        .npc-page-scope .npc-vault-view-tab:focus-visible,
        .npc-page-scope .npc-workspace-action--view:focus-visible {
            outline: 2px solid #a8815e;
            outline-offset: 3px;
        }
        .npc-page-scope[data-npc-view-mode="separate"] .npc-vault-open-section {
            margin-bottom: 18px;
            animation: npc-vault-section-in .24s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes npc-vault-section-in {
            from { opacity: .35; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 640px) {
            .npc-page-scope .npc-workspace-action--view { min-width: 39px; }
            .npc-page-scope .npc-vault-view-tabs { margin-bottom: 16px; gap: 16px; }
            .npc-page-scope .npc-vault-view-tab { font-size: 11px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .npc-page-scope[data-npc-view-mode="separate"] .npc-vault-open-section { animation: none; }
            .npc-page-scope .npc-vault-view-tab { transition: none; }
        }


        /* V66.1.08 — pesquisa em superfície única, sem cápsulas aninhadas.
           Os filtros ficam em um único grupo leve; campos e rotas GET permanecem os mesmos. */
        .npc-page-scope .npc-workspace-head { margin-bottom: clamp(19px, 2vw, 25px); }
        .npc-page-scope .npc-workspace-tools {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto;
            align-items: center;
            column-gap: 14px;
            row-gap: 10px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: none;
            box-shadow: none;
        }
        .npc-page-scope .npc-workspace-tools__search-line {
            display: flex;
            align-items: center;
            gap: 4px;
            min-width: 0;
            min-height: 44px;
            padding: 3px 5px 3px 0;
            border: 1px solid #d7c3a7;
            border-radius: 15px;
            background: rgba(255, 252, 246, .90);
            box-shadow: 0 3px 11px rgba(80, 45, 26, .043), inset 0 1px 0 rgba(255,255,255,.68);
            transition: border-color .22s ease, box-shadow .22s ease, background .22s ease;
        }
        .npc-page-scope .npc-workspace-tools__search-line:hover {
            border-color: #c3a784;
        }
        .npc-page-scope .npc-workspace-tools__search-line:focus-within {
            border-color: #a77e59;
            background: #fffcf7;
            box-shadow: 0 0 0 3px rgba(132, 87, 55, .09), 0 4px 13px rgba(80,45,26,.05);
        }
        .npc-page-scope .npc-workspace-search,
        .npc-page-scope .npc-workspace-search:focus-within {
            display: flex;
            align-items: center;
            gap: 11px;
            flex: 1 1 auto;
            min-width: 0;
            min-height: 42px;
            padding: 0 5px 0 17px;
            border: 0;
            border-radius: 15px;
            background: none;
            box-shadow: none;
            outline: 0;
        }
        .npc-page-scope .npc-workspace-search__icon {
            display: block;
            width: 17px;
            height: 17px;
            flex: 0 0 17px;
            color: #a8845f;
            transition: color .18s ease;
        }
        .npc-page-scope .npc-workspace-tools__search-line:focus-within .npc-workspace-search__icon {
            color: #793429;
        }
        .npc-page-scope .npc-workspace-search input,
        .npc-page-scope .npc-workspace-search input:focus {
            display: block;
            flex: 1 1 auto;
            width: 100%;
            min-width: 0;
            height: 42px;
            padding: 0;
            margin: 0;
            background: transparent;
            border: 0;
            border-radius: 0;
            outline: 0;
            box-shadow: none;
            font: 400 12.5px/1.25 ui-sans-serif, system-ui, sans-serif;
            color: #4b3228;
        }
        .npc-page-scope .npc-workspace-search input::placeholder { color: #ac9177; opacity: 1; }
        .npc-page-scope .npc-workspace-search-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex: 0 0 auto;
            width: auto;
            min-width: 87px;
            height: 35px;
            min-height: 35px;
            margin: 0;
            padding: 0 12px;
            border: 1px solid rgba(121, 50, 36, .15);
            border-radius: 10px;
            background: #f0e3d2;
            color: #76382b;
            box-shadow: none;
            font: 700 11.5px/1.1 Georgia, "Times New Roman", serif;
            cursor: pointer;
            transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
        }
        .npc-page-scope .npc-workspace-search-button svg { width: 14px; height: 14px; }
        .npc-page-scope .npc-workspace-search-button:hover,
        .npc-page-scope .npc-workspace-search-button:focus-visible {
            background: #76291f;
            border-color: #76291f;
            color: #fffaf3;
            transform: none;
        }
        .npc-page-scope .npc-workspace-tools__filters {
            display: grid;
            grid-template-columns: 177px 152px;
            align-items: center;
            gap: 0;
            min-height: 45px;
            padding: 3px;
            background: rgba(255,250,243,.50);
            border: 1px solid rgba(174, 137, 102, .26);
            border-radius: 15px;
            box-shadow: 0 2px 6px rgba(72,45,29,.022);
        }
        .npc-page-scope .npc-workspace-select,
        .npc-page-scope .npc-workspace-select--sort {
            position: relative;
            min-width: 0;
            min-height: 37px;
            height: 37px;
            padding: 0;
            background: transparent;
            border: 0;
            border-radius: 11px;
            box-shadow: none;
            transition: background .18s ease;
        }
        .npc-page-scope .npc-workspace-select:hover,
        .npc-page-scope .npc-workspace-select:focus-within { background: rgba(247,235,219,.73); }
        .npc-page-scope .npc-workspace-select:first-child::after {
            content: '';
            position: absolute;
            right: 0;
            top: 9px;
            bottom: 9px;
            width: 1px;
            background: rgba(164,121,85,.24);
            pointer-events: none;
        }
        .npc-page-scope .npc-workspace-select select {
            height: 100%;
            padding-left: 12px;
            padding-right: 29px;
            font-size: 11.5px;
            font-weight: 500;
            color: #77523b;
            border: 0;
            background: transparent;
            box-shadow: none;
        }
        .npc-page-scope .npc-workspace-select > svg { right: 10px; width: 12px; height: 12px; }
        @media (max-width: 930px) {
            .npc-page-scope .npc-workspace-tools { grid-template-columns: minmax(0, 1fr); }
            .npc-page-scope .npc-workspace-tools__filters {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
            }
        }
        @media (max-width: 640px) {
            .npc-page-scope .npc-workspace-tools { gap: 8px; }
            .npc-page-scope .npc-workspace-tools__search-line { min-height: 43px; }
            .npc-page-scope .npc-workspace-search,
            .npc-page-scope .npc-workspace-search:focus-within { min-height: 39px; padding-left: 13px; }
            .npc-page-scope .npc-workspace-search input { height: 39px; }
            .npc-page-scope .npc-workspace-search-button { min-width: 79px; height: 34px; min-height: 34px; }
            .npc-page-scope .npc-workspace-tools__filters { min-height: 42px; }
            .npc-page-scope .npc-workspace-select,
            .npc-page-scope .npc-workspace-select--sort { height: 34px; min-height: 34px; }
        }
        @media (max-width: 385px) {
            .npc-page-scope .npc-workspace-search-button span { display: inline; }
            .npc-page-scope .npc-workspace-search-button { width: auto; min-width: 72px; padding-inline: 10px; }
        }
        @media (max-width: 360px) {
            .npc-page-scope .npc-workspace-search-button { min-width: 64px; padding-inline: 9px; gap: 5px; }
            .npc-page-scope .npc-workspace-search-button svg { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .npc-page-scope .npc-workspace-search-button,
            .npc-page-scope .npc-workspace-tools__search-line,
            .npc-page-scope .npc-workspace-search__icon { transition: none; }
        }


        /* V66.1.09 — proposta C: topo editorial aberto, ferramentas contextuais. */
        .npc-page-scope .npc-workspace-head--editorial {
            margin-bottom: clamp(17px, 2vw, 24px);
            padding: 0;
            border: 0;
            background: transparent;
        }
        .npc-page-scope .npc-workspace-head--editorial .npc-workspace-head__intro {
            margin-bottom: clamp(16px, 2vw, 21px);
        }
        .npc-page-scope .npc-workspace-head--editorial .npc-workspace-tools--editorial {
            display: block;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-tools__search-line {
            display: flex;
            width: min(82%, 860px);
            max-width: 100%;
            min-height: 45px;
            padding: 3px 5px 3px 0;
            border: 1px solid rgba(170, 126, 87, .37);
            border-radius: 999px;
            background: #fffbf5;
            box-shadow: 0 3px 12px rgba(71, 43, 29, .045);
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-tools__search-line:focus-within {
            border-color: #a77e59;
            box-shadow: 0 0 0 3px rgba(132, 87, 55, .095);
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search,
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search:focus-within {
            min-height: 39px;
            padding: 0 8px 0 17px;
            border: none;
            background: transparent;
            box-shadow: none;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search input {
            height: 39px;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search-button {
            display: grid;
            place-items: center;
            width: 34px;
            min-width: 34px;
            height: 34px;
            min-height: 34px;
            flex: 0 0 34px;
            padding: 0;
            margin-right: 3px;
            border-radius: 50%;
            border: 1px solid rgba(160, 120, 78, .19);
            background: #efe3d3;
            color: #713f30;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search-button:hover {
            background: #72291e;
            border-color: #72291e;
            color: #fff9f2;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search-button svg {
            width: 16px;
            height: 16px;
        }
        .npc-page-scope .npc-workspace-tools__utility-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px 22px;
            margin-top: 13px;
            min-height: 27px;
        }
        .npc-page-scope .npc-workspace-tools__context-controls {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
        }
        .npc-page-scope .npc-context-menu {
            position: relative;
            isolation: isolate;
        }
        .npc-page-scope .npc-context-menu[open] {
            z-index: 80;
        }
        .npc-page-scope .npc-context-menu__trigger,
        .npc-page-scope .npc-context-view-switch {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            min-height: 30px;
            padding: 4px 5px;
            border: 0;
            border-radius: 8px;
            color: #87654b;
            background: transparent;
            box-shadow: none;
            font: 600 12px/1.2 ui-sans-serif, system-ui, sans-serif;
            cursor: pointer;
            transition: color .18s ease, background .18s ease;
            white-space: nowrap;
        }
        .npc-page-scope .npc-context-menu__trigger { list-style: none; }
        .npc-page-scope .npc-context-menu__trigger::-webkit-details-marker { display: none; }
        .npc-page-scope .npc-context-menu__trigger::marker { content: ''; }
        .npc-page-scope .npc-context-menu__trigger:hover,
        .npc-page-scope .npc-context-view-switch:hover,
        .npc-page-scope .npc-context-menu[open] > .npc-context-menu__trigger {
            background: rgba(255,250,244,.65);
            color: #6b2119;
        }
        .npc-page-scope .npc-context-menu__trigger:focus-visible,
        .npc-page-scope .npc-context-view-switch:focus-visible {
            outline: 2px solid #a8815e;
            outline-offset: 3px;
        }
        .npc-page-scope .npc-context-menu__trigger > svg,
        .npc-page-scope .npc-context-view-switch > svg {
            width: 15px;
            height: 15px;
            flex: 0 0 15px;
        }
        .npc-page-scope .npc-context-menu__trigger .npc-context-menu__caret,
        .npc-page-scope .npc-context-view-switch__caret {
            width: 12px;
            height: 12px;
            opacity: .65;
            transition: transform .2s ease;
        }
        .npc-page-scope .npc-context-menu[open] .npc-context-menu__caret {
            transform: rotate(180deg);
        }
        .npc-page-scope .npc-context-menu__active-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #9f633e;
        }
        .npc-page-scope .npc-context-menu__panel {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: min(255px, calc(100vw - 96px));
            padding: 15px;
            border: 1px solid #ddc7ad;
            border-radius: 17px;
            background: #fffbf5;
            box-shadow: 0 14px 34px rgba(64, 36, 21, .14);
        }
        .npc-page-scope .npc-context-menu__label {
            display: block;
            margin-bottom: 9px;
            font: 700 12px/1.2 Georgia, 'Times New Roman', serif;
            color: #6b2119;
        }
        .npc-page-scope .npc-context-menu__field select {
            display: block;
            width: 100%;
            min-width: 0;
            height: 40px;
            border: 1px solid #ddcbb7;
            border-radius: 10px;
            background: #fffefa;
            color: #674633;
            padding: 0 9px;
            font-size: 12px;
            outline-offset: 2px;
        }
        .npc-page-scope .npc-context-menu__field select:focus-visible {
            outline: 2px solid #a8815e;
        }
        .npc-page-scope .npc-context-menu__apply {
            display: block;
            width: 100%;
            margin-top: 12px;
            min-height: 36px;
            border: 0;
            border-radius: 10px;
            background: #6b2119;
            color: #fffaf2;
            font: 700 12px/1.2 Georgia, serif;
            cursor: pointer;
        }
        .npc-page-scope .npc-context-menu__apply:hover { background: #843226; }
        .npc-page-scope .npc-context-view-switch {
            margin-left: auto;
            color: #704b3b;
        }
        .npc-page-scope .npc-context-view-switch__heading { color: #9d8067; }
        .npc-page-scope .npc-context-view-switch[aria-pressed='true'] {
            color: #702a21;
            background: rgba(155, 93, 55, .055);
        }
        @media(max-width: 780px) {
            .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-tools__search-line { width: 100%; }
        }
        @media(max-width: 640px) {
            .npc-page-scope .npc-workspace-tools__utility-row { margin-top: 10px; }
            .npc-page-scope .npc-workspace-tools__context-controls { gap: 12px; }
            .npc-page-scope .npc-context-menu__trigger,
            .npc-page-scope .npc-context-view-switch { font-size: 11px; }
            .npc-page-scope .npc-context-view-switch__heading { display: none; }
            .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-tools__search-line { min-height: 43px; }
        }
        @media(max-width: 380px) {
            .npc-page-scope .npc-context-menu__panel { width: min(230px, calc(100vw - 102px)); }
            .npc-page-scope .npc-context-menu__trigger,
            .npc-page-scope .npc-context-view-switch { gap: 4px; }
            .npc-page-scope .npc-workspace-tools__context-controls { gap: 7px; }
        }
        @media(prefers-reduced-motion:reduce) {
            .npc-page-scope .npc-context-menu__trigger,
            .npc-page-scope .npc-context-view-switch,
            .npc-page-scope .npc-context-menu__trigger .npc-context-menu__caret { transition: none; }
        }

        /* V66.1.10 — ferramentas na mesma linha: composição equilibrada, sem linhas vazias. */
        .npc-page-scope .npc-workspace-head--editorial {
            margin-bottom: clamp(16px, 1.7vw, 23px);
        }
        .npc-page-scope .npc-workspace-head--editorial .npc-workspace-head__intro {
            margin-bottom: 16px;
        }
        .npc-page-scope .npc-workspace-head--editorial .npc-workspace-tools--editorial {
            display: grid;
            grid-template-columns: minmax(280px, 1fr) auto;
            align-items: center;
            gap: 12px;
            width: 100%;
            min-width: 0;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-tools__search-line {
            width: 100%;
            max-width: none;
            min-width: 0;
            min-height: 44px;
            border-radius: 15px;
            background: rgba(255, 252, 246, .93);
            border-color: rgba(161, 119, 84, .32);
            box-shadow: 0 3px 10px rgba(71, 43, 29, .035);
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search,
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search:focus-within {
            padding-left: 14px;
        }
        .npc-page-scope .npc-workspace-tools--editorial .npc-workspace-search-button {
            border-radius: 10px;
            background: #eee1d1;
            border-color: rgba(159, 114, 79, .19);
            margin-right: 2px;
        }
        .npc-page-scope .npc-workspace-tools__utility-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            gap: 9px;
            min-height: 44px;
            margin-top: 0;
        }
        .npc-page-scope .npc-workspace-tools__context-controls {
            gap: 8px;
            flex-wrap: nowrap;
        }
        .npc-page-scope .npc-context-menu__trigger,
        .npc-page-scope .npc-context-view-switch {
            min-height: 42px;
            padding: 0 12px;
            border-radius: 13px;
            border: 1px solid rgba(166, 130, 96, .28);
            background: rgba(255, 251, 245, .64);
            color: #77553d;
            font-size: 12px;
            box-shadow: none;
        }
        .npc-page-scope .npc-context-menu__trigger:hover,
        .npc-page-scope .npc-context-menu[open] > .npc-context-menu__trigger,
        .npc-page-scope .npc-context-view-switch:hover {
            background: #fffaf2;
            border-color: rgba(151, 105, 70, .48);
            color: #6b2119;
        }
        .npc-page-scope .npc-context-view-switch {
            margin-left: 3px;
            border-color: rgba(107, 33, 25, .23);
            color: #6b2119;
            background: rgba(252,245,237,.73);
            font-family: Georgia, 'Times New Roman', serif;
            font-weight: 700;
        }
        .npc-page-scope .npc-context-view-switch[aria-pressed='true'] {
            background: #f1e3d5;
            border-color: rgba(126, 67, 42, .36);
        }
        .npc-page-scope .npc-context-view-switch__heading,
        .npc-page-scope .npc-context-view-switch__caret { display: none; }
        /* O seletor em abas já diz qual seção está aberta; não duplica Biblioteca/Pastas. */
        .npc-page-scope[data-npc-view-mode='separate'] .npc-vault-open-heading {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            clip-path: inset(50%) !important;
            padding: 0 !important;
            margin: 0 !important;
            border: 0 !important;
        }
        .npc-page-scope[data-npc-view-mode='separate'] .npc-vault-view-tabs {
            margin: 0 0 16px;
        }
        @media (max-width: 1060px) {
            .npc-page-scope .npc-workspace-head--editorial .npc-workspace-tools--editorial {
                grid-template-columns: minmax(0,1fr);
                gap: 9px;
            }
            .npc-page-scope .npc-workspace-tools__utility-row {
                justify-content: space-between;
            }
            .npc-page-scope .npc-context-view-switch { margin-left: auto; }
        }
        @media (max-width: 640px) {
            .npc-page-scope .npc-workspace-head--editorial .npc-workspace-head__intro { margin-bottom: 12px; }
            .npc-page-scope .npc-workspace-tools__utility-row { gap: 7px; }
            .npc-page-scope .npc-workspace-tools__context-controls { gap: 6px; }
            .npc-page-scope .npc-context-menu__trigger,
            .npc-page-scope .npc-context-view-switch {
                min-height: 36px;
                border-radius: 11px;
                padding: 0 9px;
                font-size: 11px;
            }
            .npc-page-scope .npc-context-view-switch { margin-left: auto; }
            .npc-page-scope .npc-vault-view-tabs { margin-bottom: 13px; }
        }
        @media (max-width: 360px) {
            .npc-page-scope .npc-context-view-switch { margin-left: 0; }
        }

        /* Transições de estado ultra-suaves */
        .transition-polished {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    {{-- Wrapper de Escopo da Página --}}
    <div class="npc-page-scope" data-npc-owner="{{ (string) auth()->id() }}">

        {{-- Notificação Toast Flutuante --}}
        @if(session('success'))
            <div 
                id="toast-success" 
                class="fixed top-6 right-6 z-50 transform translate-y-0 opacity-100 transition-all duration-500 ease-out flex items-center p-4 rounded-xl bg-emerald-50 border border-emerald-200/60 shadow-lg text-emerald-800 max-w-sm"
            >
                <svg class="w-5 h-5 mr-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-serif text-xs font-semibold pr-4 leading-relaxed">{{ session('success') }}</span>
                <button onclick="dismissToast()" class="text-emerald-400 hover:text-emerald-600 transition-colors focus:outline-none ml-auto shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <script>
                setTimeout(() => { dismissToast(); }, 4000);
                function dismissToast() {
                    const toast = document.getElementById('toast-success');
                    if (toast) {
                        toast.classList.add('opacity-0', '-translate-y-2');
                        setTimeout(() => toast.remove(), 500);
                    }
                }
            </script>
        @endif

        {{-- Conteúdo principal: o topo do Cofre faz parte do fluxo da página, sem barra fixa. --}}
        <div class="npc-vault-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

            <section class="npc-workspace-head npc-workspace-head--editorial" aria-labelledby="npc-workspace-title">
                <div class="npc-workspace-head__intro">
                    <div class="npc-workspace-head__identity">
                        <h1 id="npc-workspace-title" class="npc-workspace-head__title">Cofre de NPCs</h1>
                        <span class="npc-workspace-head__stroke" aria-hidden="true"></span>
                    </div>
                    <div class="npc-workspace-head__actions">
                        <button type="button" onclick="openFolderModal()"
                                class="npc-workspace-action npc-workspace-action--secondary"
                                aria-label="Criar nova pasta" title="Nova pasta">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>
                                <path d="M12 11v6m-3-3h6"/>
                            </svg>
                            <span>Nova pasta</span>
                        </button>
                        <button type="button" onclick="openImportModal()"
                                class="npc-workspace-action npc-workspace-action--primary"
                                aria-label="Importar NPC" title="Importar NPC">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span>Importar NPC</span>
                        </button>
                    </div>
                </div>

                {{-- Campo principal livre; filtros e modo de exibição permanecem contextuais. --}}
                <form method="GET" action="{{ request()->url() }}" class="npc-workspace-tools npc-workspace-tools--editorial" role="search">
                    <div class="npc-workspace-tools__search-line">
                        <label class="npc-workspace-search">
                            <span class="sr-only">Pesquisar fichas de NPCs</span>
                            <svg class="npc-workspace-search__icon" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <circle cx="10.8" cy="10.8" r="7"/><path d="m16 16 5 5"/>
                            </svg>
                            <input type="search" name="search" value="{{ request('search') }}"
                                   autocomplete="off" placeholder="Pesquisar nome ou criatura..."
                                   aria-label="Pesquisar criaturas">
                        </label>
                        <button type="submit" class="npc-workspace-search-button" aria-label="Buscar NPCs" title="Buscar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14m-6-6 6 6-6 6"/>
                            </svg>
                        </button>
                    </div>

                    <div class="npc-workspace-tools__utility-row">
                        <div class="npc-workspace-tools__context-controls">
                            <details class="npc-context-menu" data-npc-context-menu>
                                <summary class="npc-context-menu__trigger" aria-label="Abrir filtros de pasta">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M4 7h16M7 12h10M10 17h4"/>
                                    </svg>
                                    <span>Filtros</span>
                                    @if(filled(request('folder')))<span class="npc-context-menu__active-dot" aria-label="Filtro ativo"></span>@endif
                                    <svg class="npc-context-menu__caret" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"/>
                                    </svg>
                                </summary>
                                <div class="npc-context-menu__panel">
                                    <label for="npc-folder-filter" class="npc-context-menu__label">Filtrar por pasta</label>
                                    <div class="npc-context-menu__field">
                                        <select id="npc-folder-filter" name="folder" aria-label="Filtrar por pasta">
                                <option value="">Todas as pastas</option>
                                @if(isset($folders))
                                    @foreach($folders as $folder)
                                        <option value="{{ $folder->id }}"
                                            {{ (string) request('folder') === (string) $folder->id ? 'selected' : '' }}>
                                            {{ $folder->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                                    </div>
                                    <button class="npc-context-menu__apply" type="submit">Aplicar filtro</button>
                                </div>
                            </details>

                            <details class="npc-context-menu" data-npc-context-menu>
                                <summary class="npc-context-menu__trigger" aria-label="Abrir ordenação">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 5v14m-3-3 3 3 3-3M13 7h8m-8 5h6m-6 5h4"/>
                                    </svg>
                                    <span>Ordenar</span>
                                    @if(request('sort', 'name_asc') !== 'name_asc')<span class="npc-context-menu__active-dot" aria-label="Ordenação alterada"></span>@endif
                                    <svg class="npc-context-menu__caret" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"/>
                                    </svg>
                                </summary>
                                <div class="npc-context-menu__panel">
                                    <label for="npc-sort-filter" class="npc-context-menu__label">Ordenar fichas</label>
                                    <div class="npc-context-menu__field">
                                        <select id="npc-sort-filter" name="sort" aria-label="Ordenar NPCs">
                                <option value="name_asc" {{ request('sort', 'name_asc') === 'name_asc' ? 'selected' : '' }}>Nome A-Z</option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nome Z-A</option>
                                <option value="cr_desc" {{ request('sort') === 'cr_desc' ? 'selected' : '' }}>CR maior</option>
                                <option value="cr_asc" {{ request('sort') === 'cr_asc' ? 'selected' : '' }}>CR menor</option>
                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mais recentes</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Mais antigas</option>
                            </select>
                                    </div>
                                    <button class="npc-context-menu__apply" type="submit">Aplicar ordenação</button>
                                </div>
                            </details>
                        </div>

                        @if(!filled(request('search')) && !filled(request('folder')))
                            <button type="button" id="npc-view-switch"
                                    class="npc-context-view-switch" aria-pressed="false"
                                    aria-label="Separar Biblioteca e Pastas em abas"
                                    title="Alternar entre visualização conjunta e separada">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 4h8v16H3zM13 4h8v16h-8z" stroke-linejoin="round"/>
                                </svg>
                                <span class="npc-context-view-switch__heading">Visualização</span>
                                <span data-npc-view-switch-label>Separar em abas</span>
                                <svg class="npc-context-view-switch__caret" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="m7 10 5 5 5-5"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </form>
            </section>

    @php
        $hasSearch = filled(request('search'));
        $hasFolder = filled(request('folder'));
        $hasFilters = $hasSearch || $hasFolder;

        $unassignedNpcs = $npcs->whereNull('folder_id');
    @endphp


    {{-- ============================================================
         MODO DE BUSCA / FILTRO
         ============================================================ --}}
    @if($hasFilters)

        <section class="mb-12">

            <div class="npc-vault-section-bar flex items-center justify-between mb-6 pb-4 border-b border-[#cdbb9f]/40">

                <div class="flex items-center gap-3">

                    <div class="p-2 rounded-xl
                                bg-[#6b1d14]/10
                                text-[#6b1d14]
                                border border-[#6b1d14]/20
                                shadow-sm">

                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-2xl font-serif font-bold text-[#6b1d14]">
                            Resultados
                        </h2>

                        <p class="text-xs italic text-[#8c6239]">

                            @if($hasSearch)
                                Pesquisa por
                                <span class="font-bold">
                                    "{{ request('search') }}"
                                </span>
                            @elseif($hasFolder)
                                NPCs da pasta selecionada
                            @endif

                        </p>

                    </div>

                </div>

                <span class="px-3 py-1 rounded-full
                             bg-[#efe9dc]
                             border border-[#cdbb9f]
                             text-xs font-serif font-bold
                             text-[#8c6239]
                             shadow-sm">

                    {{ $npcs->count() }}
                    {{ $npcs->count() === 1 ? 'NPC' : 'NPCs' }}

                </span>

            </div>


            @if($npcs->count())

                <div class="npc-vault-grid">

                    @foreach($npcs as $npc)

                        @include('npcs.npc-card', [
                            'npc' => $npc
                        ])

                    @endforeach

                </div>

            @else

                <div class="bg-[#efe9dc]/20
                            border border-dashed
                            border-[#cdbb9f]/60
                            rounded-2xl
                            p-12
                            text-center">

                    <div class="mx-auto mb-4 w-12 h-12 rounded-full
                                bg-[#6b1d14]/10
                                text-[#6b1d14]
                                flex items-center justify-center">

                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                    </div>

                    <h2 class="text-lg font-serif font-bold text-[#6b1d14]">
                        Nenhum NPC encontrado
                    </h2>

                    <p class="text-xs text-[#8c6239]/80 italic mt-1">
                        Nenhuma ficha corresponde aos filtros atuais.
                    </p>

                    <a
                        href="{{ request()->url() }}"
                        class="inline-flex mt-5 px-4 py-2
                               rounded-xl
                               bg-[#6b1d14]
                               hover:bg-[#53150f]
                               text-white
                               text-xs
                               font-serif
                               font-bold
                               uppercase
                               tracking-wider
                               transition-polished
                               shadow-sm"
                    >
                        Limpar filtros
                    </a>

                </div>

            @endif

        </section>


    {{-- ============================================================
         MODO NORMAL
         ============================================================ --}}
    @else

        {{-- Visualização opcional por abas. Na visualização conjunta, esta barra permanece oculta. --}}
        <nav class="npc-vault-view-tabs" id="npc-vault-view-tabs" role="tablist"
             aria-label="Visualizar o Cofre por seção" hidden>
            <button type="button" class="npc-vault-view-tab is-active" id="npc-view-tab-library"
                    data-npc-view-tab="library" role="tab" aria-selected="true"
                    aria-controls="npc-vault-library-panel" tabindex="0">
                Personagens soltos <span>{{ $unassignedNpcs->count() }}</span>
            </button>
            <button type="button" class="npc-vault-view-tab" id="npc-view-tab-folders"
                    data-npc-view-tab="folders" role="tab" aria-selected="false"
                    aria-controls="npc-vault-folders-panel" tabindex="-1">
                Pastas <span>{{ $folders->count() }}</span>
            </button>
        </nav>

        {{-- ========================================================
             BIBLIOTECA
             ======================================================== --}}
            <section class="npc-vault-library npc-vault-open-section" id="npc-vault-library-panel"
                     data-npc-view-section="library" aria-labelledby="npc-vault-library-heading">

                <div class="npc-vault-open-heading flex items-center justify-between">

                    <div class="flex items-center gap-3">
                        <div>

                            <h2 id="npc-vault-library-heading" class="text-2xl font-serif font-bold text-[#6b1d14]">
                                Biblioteca
                            </h2>

                            <p class="text-xs italic text-[#8c6239]">
                                NPCs sem pastas atribuídas
                            </p>

                        </div>

                    </div>

                    <span class="npc-vault-open-count px-3 py-1 rounded-full
                                 bg-[#efe9dc]
                                 border border-[#cdbb9f]
                                 text-xs font-serif font-bold
                                 text-[#8c6239]
                                 shadow-sm">

                        {{ $unassignedNpcs->count() }}
                        {{ $unassignedNpcs->count() === 1 ? 'NPC' : 'NPCs' }}

                    </span>
                </div>

                    @if($unassignedNpcs->count())


                <div class="npc-vault-grid">

                    @foreach($unassignedNpcs as $npc)

                        @include('npcs.npc-card', [
                            'npc' => $npc
                        ])

                    @endforeach

                </div>
                    @else
                        <p class="npc-vault-open-empty">Nenhum NPC solto no momento. Seus personagens organizados continuam nas pastas abaixo.</p>
                    @endif
            </section>


        {{-- ========================================================
             PASTAS
             ======================================================== --}}
            <section class="npc-vault-folders npc-vault-open-section" id="npc-vault-folders-panel"
                     data-npc-view-section="folders" aria-labelledby="npc-vault-folders-heading">

                <div class="npc-vault-open-heading flex items-center justify-between">

                    <div class="flex items-center gap-3">
                        <div>

                            <h2 id="npc-vault-folders-heading" class="text-2xl font-serif font-bold text-[#6b1d14]">
                                Pastas de Campanhas
                            </h2>

                            <p class="text-xs italic text-[#8c6239]">
                                Arraste seus NPCs para organizá-los em pastas
                            </p>

                        </div>

                    </div>

                    <span class="npc-vault-open-count px-3 py-1 rounded-full
                                 bg-[#efe9dc]
                                 border border-[#cdbb9f]
                                 text-xs font-serif font-bold
                                 text-[#8c6239]
                                 shadow-sm">

                        {{ $folders->count() }}
                        {{ $folders->count() === 1 ? 'Pasta' : 'Pastas' }}

                    </span>
                </div>

                    @if($folders->count())


                <div class="npc-folder-grid">

                    @foreach($folders as $folder)
                        <div
                            class="folder-dropzone npc-vault-folder npc-folder-card group"
                            style="--npc-folder-accent: {{ $folder->color }};"
                            data-folder="{{ $folder->id }}"
                            data-url="{{ route('folders.show', $folder) }}"
                            data-name="{{ $folder->name }}"
                            data-subtitle="{{ $folder->subtitle }}"
                            data-color="{{ $folder->color }}"
                            data-update-url="{{ route('folders.update', $folder) }}"
                        >
                            {{-- O overlay de arrastar continua sendo controlado pelo JS existente. --}}
                            <div class="npc-folder-drop-hint" aria-hidden="true">
                                <span class="npc-folder-drop-hint__icon">↳</span>
                                <strong>Guardar nesta pasta</strong>
                                <small>Solte para organizar</small>
                            </div>

                            <div class="npc-folder-card__head">
                                <div class="npc-folder-card__symbol" aria-hidden="true">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M10 4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h6z"/>
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="npc-folder-card__title" title="{{ $folder->name }}">
                                        {{ $folder->name }}
                                    </h3>
                                    @if($folder->subtitle)
                                        <p class="npc-folder-card__subtitle" title="{{ $folder->subtitle }}">
                                            {{ $folder->subtitle }}
                                        </p>
                                    @endif
                                </div>

                                <div class="npc-folder-card__menu-wrap" onclick="event.stopPropagation();">
                                    <button
                                        type="button"
                                        class="npc-folder-card__menu"
                                        onclick="toggleFolderMenu({{ $folder->id }})"
                                        aria-label="Opções da pasta {{ $folder->name }}"
                                        title="Opções da pasta"
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                            <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                                        </svg>
                                    </button>
                                    <div
                                        id="folder-menu-{{ $folder->id }}"
                                        class="npc-folder-card__dropdown hidden"
                                    >
                                        <button
                                            type="button"
                                            onclick="editFolder(this)"
                                            data-id="{{ $folder->id }}"
                                            data-name="{{ $folder->name }}"
                                            data-subtitle="{{ $folder->subtitle }}"
                                            data-color="{{ $folder->color }}"
                                            class="w-full text-left px-3.5 py-2.5 text-xs font-serif font-bold text-[#4a3b32] hover:bg-[#efe9dc]"
                                        >
                                            Editar pasta
                                        </button>
                                        <form
                                            action="{{ route('folders.destroy', $folder) }}"
                                            method="POST"
                                            onsubmit="return confirm('Excluir esta pasta? Os NPCs voltarão para Sem Pasta.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="w-full text-left px-3.5 py-2.5 border-t border-[#e4d7c4] text-xs font-serif font-bold text-red-700 hover:bg-red-50"
                                            >
                                                Excluir pasta
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="npc-folder-card__people">
                                <div class="npc-folder-card__gallery">
                                    <div
                                        class="npc-folder-card__viewport"
                                        @if($folder->npcs->count() > 1)
                                            data-folder-preview
                                            data-preview-index="0"
                                            role="button"
                                            tabindex="0"
                                            aria-label="Ver próximos NPCs da pasta {{ $folder->name }}"
                                            title="Clique para ver os próximos personagens"
                                        @endif
                                    >
                                        <div class="folder-avatars npc-folder-card__portraits">
                                            @forelse($folder->npcs as $npc)
                                                <div
                                                    class="avatar-preview npc-folder-card__portrait{{ $npc->image_path ? '' : ' npc-folder-card__portrait--initials' }}"
                                                    title="{{ $npc->name }}"
                                                    @if($loop->index < 5) data-visible-slot="{{ $loop->index + 1 }}" @else hidden @endif
                                                >
                                                    @if($npc->image_path)
                                                        <img src="{{ asset('storage/'.$npc->image_path) }}" alt="{{ $npc->name }}" loading="lazy">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-[12px] font-serif font-bold text-[#6b1d14]">
                                                            {{ str($npc->name)->substr(0, 2)->upper() }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="npc-folder-card__empty">Nenhum NPC nesta pasta</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    @if($folder->npcs->count() > 1)
                                        <button
                                            type="button"
                                            class="npc-folder-card__next"
                                            aria-label="Próximos personagens da pasta {{ $folder->name }}"
                                            title="Próximos personagens"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="m9 5 7 7-7 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                <div class="npc-folder-card__count" aria-label="{{ $folder->npcs->count() }} NPCs">
                                    <strong class="folder-count">{{ $folder->npcs->count() }}</strong>
                                    <small>NPCs</small>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                    @else
                        <div class="npc-vault-open-empty">Nenhuma pasta criada. <button type="button" onclick="openFolderModal()">Criar primeira pasta</button></div>
                    @endif
            </section>

    @endif



</div>
@include('npcs.partials.move-folder-modal', ['availableFolders' => $folders])
@include('npcs.partials.edit-npc-modal')
{{-- Modal de Nova Pasta --}}
<div id="folderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop com Blur --}}
    <div onclick="closeFolderModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        {{-- Card do Modal --}}
        <div class="relative w-full max-w-md bg-[#f4f1e8] rounded-2xl shadow-2xl border border-[#cdbb9f]/60 overflow-hidden z-10">
        
        {{-- Cabeçalho --}}
        <div class="px-6 py-4 border-b border-[#cdbb9f]/50 bg-[#efe9dc] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-lg bg-[#6b1d14]/10 text-[#6b1d14]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-serif font-bold text-[#6b1d14] leading-tight">
                        Nova Pasta
                    </h3>
                    <p class="text-[11px] italic text-[#8c6239]">
                        Organize seus NPCs e conteúdos
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeFolderModal()" class="p-1.5 rounded-lg text-[#8c6239]/70 hover:text-[#6b1d14] hover:bg-[#6b1d14]/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Formulário --}}
        <form action="{{ route('folders.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                
                {{-- Nome --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Nome da Pasta
                    </label>
                    <input
                        type="text"
                        name="name"
                        required
                        placeholder="Ex: Taverna do Dragão, Vilões..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-sm text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all shadow-inner"
                    >
                </div>

                {{-- Subtítulo --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Subtítulo <span class="text-[10px] text-[#8c6239]/70 normal-case font-normal">(opcional)</span>
                    </label>
                    <textarea
                        name="subtitle"
                        rows="2"
                        placeholder="Breve descrição sobre esta pasta..."
                        class="w-full px-3.5 py-2 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-xs text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all resize-none shadow-inner"
                    ></textarea>
                </div>

                {{-- Seletor de Cores --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-2.5">
                        Cor de Destaque
                    </label>
                    
                                        <div class="grid grid-cols-6 gap-3 p-3 rounded-xl bg-[#efe9dc]/60 border border-[#cdbb9f]/40">
                        {{-- Couro --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8c6239" class="peer sr-only" checked>
                            <span class="w-7 h-7 rounded-full bg-[#8c6239] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Couro</span>
                        </label>
                        {{-- Carmesim --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#6b1d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#6b1d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Carmesim</span>
                        </label>
                        {{-- Âmbar --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#a65d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#a65d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#a65d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#a65d14]">Âmbar</span>
                        </label>
                        {{-- Dourado --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#b48a2c" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#b48a2c] shadow-md ring-2 ring-transparent peer-checked:ring-[#b48a2c] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#b48a2c]">Dourado</span>
                        </label>
                        {{-- Floresta --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2f5e3e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2f5e3e] shadow-md ring-2 ring-transparent peer-checked:ring-[#2f5e3e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2f5e3e]">Floresta</span>
                        </label>
                        {{-- Jade --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2f7d67" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2f7d67] shadow-md ring-2 ring-transparent peer-checked:ring-[#2f7d67] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2f7d67]">Jade</span>
                        </label>
                        {{-- Arcano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#1b5e5e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#1b5e5e] shadow-md ring-2 ring-transparent peer-checked:ring-[#1b5e5e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#1b5e5e]">Arcano</span>
                        </label>
                        {{-- Oceano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2c4a6f" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2c4a6f] shadow-md ring-2 ring-transparent peer-checked:ring-[#2c4a6f] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2c4a6f]">Oceano</span>
                        </label>
                        {{-- Lavanda --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#6a5da8" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#6a5da8] shadow-md ring-2 ring-transparent peer-checked:ring-[#6a5da8] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6a5da8]">Lavanda</span>
                        </label>
                        {{-- Místico --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#5b3265" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#5b3265] shadow-md ring-2 ring-transparent peer-checked:ring-[#5b3265] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#5b3265]">Místico</span>
                        </label>
                        {{-- Magia --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8a3052" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#8a3052] shadow-md ring-2 ring-transparent peer-checked:ring-[#8a3052] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#8a3052]">Magia</span>
                        </label>
                        {{-- Rubi --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#9d2d2d" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#9d2d2d] shadow-md ring-2 ring-transparent peer-checked:ring-[#9d2d2d] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#9d2d2d]">Rubi</span>
                        </label>
                        {{-- Rosé --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#b16478" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#b16478] shadow-md ring-2 ring-transparent peer-checked:ring-[#b16478] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#b16478]">Rosé</span>
                        </label>
                        {{-- Sombra --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#595959" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#595959] shadow-md ring-2 ring-transparent peer-checked:ring-[#595959] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#595959]">Sombra</span>
                        </label>
                        {{-- Obsidian --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2a2a2a" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2a2a2a] shadow-md ring-2 ring-transparent peer-checked:ring-[#2a2a2a] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2a2a2a]">Obsidian</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Rodapé --}}
            <div class="px-6 py-4 border-t border-[#cdbb9f]/50 flex justify-end items-center gap-2.5 bg-[#efe9dc]">
                <button
                    type="button"
                    onclick="closeFolderModal()"
                    class="px-4 py-2 rounded-xl border border-[#cdbb9f] font-serif text-xs font-bold text-[#8c6239] hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 transition-all"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-[#6b1d14] hover:bg-[#802319] active:bg-[#54160f] text-[#f4f1e8] font-serif text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-lg transition-all"
                >
                    Criar Pasta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal de Edição de Pasta --}}
<div id="editFolderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop com Blur --}}
    <div onclick="closeEditFolderModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

    {{-- Card do Modal --}}
    <div class="relative w-full max-w-md bg-[#f4f1e8] rounded-2xl shadow-2xl border border-[#cdbb9f]/60 overflow-hidden z-10">
        
        {{-- Cabeçalho --}}
        <div class="px-6 py-4 border-b border-[#cdbb9f]/50 bg-[#efe9dc] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-lg bg-[#6b1d14]/10 text-[#6b1d14]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-serif font-bold text-[#6b1d14] leading-tight">
                        Editar Pasta
                    </h3>
                    <p class="text-[11px] italic text-[#8c6239]">
                        Altere os detalhes ou a cor da pasta
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeEditFolderModal()" class="p-1.5 rounded-lg text-[#8c6239]/70 hover:text-[#6b1d14] hover:bg-[#6b1d14]/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Formulário --}}
        <form id="editFolderForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-5">
                
                {{-- Nome --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Nome da Pasta
                    </label>
                    <input
                        type="text"
                        id="edit_name"
                        name="name"
                        required
                        placeholder="Ex: Taverna do Dragão, Vilões..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-sm text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all shadow-inner"
                    >
                </div>

                {{-- Subtítulo --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Subtítulo <span class="text-[10px] text-[#8c6239]/70 normal-case font-normal">(opcional)</span>
                    </label>
                    <textarea
                        id="edit_subtitle"
                        name="subtitle"
                        rows="2"
                        placeholder="Breve descrição sobre esta pasta..."
                        class="w-full px-3.5 py-2 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-xs text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all resize-none shadow-inner"
                    ></textarea>
                </div>

                {{-- Seletor de Cores --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-2.5">
                        Cor de Destaque
                    </label>
                    
                                        <div class="grid grid-cols-6 gap-3 p-3 rounded-xl bg-[#efe9dc]/60 border border-[#cdbb9f]/40">
                        {{-- Couro --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8c6239" class="peer sr-only" >
                            <span class="w-7 h-7 rounded-full bg-[#8c6239] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Couro</span>
                        </label>
                        {{-- Carmesim --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#6b1d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#6b1d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Carmesim</span>
                        </label>
                        {{-- Âmbar --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#a65d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#a65d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#a65d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#a65d14]">Âmbar</span>
                        </label>
                        {{-- Dourado --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#b48a2c" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#b48a2c] shadow-md ring-2 ring-transparent peer-checked:ring-[#b48a2c] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#b48a2c]">Dourado</span>
                        </label>
                        {{-- Floresta --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2f5e3e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2f5e3e] shadow-md ring-2 ring-transparent peer-checked:ring-[#2f5e3e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2f5e3e]">Floresta</span>
                        </label>
                        {{-- Jade --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2f7d67" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2f7d67] shadow-md ring-2 ring-transparent peer-checked:ring-[#2f7d67] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2f7d67]">Jade</span>
                        </label>
                        {{-- Arcano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#1b5e5e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#1b5e5e] shadow-md ring-2 ring-transparent peer-checked:ring-[#1b5e5e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#1b5e5e]">Arcano</span>
                        </label>
                        {{-- Oceano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2c4a6f" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2c4a6f] shadow-md ring-2 ring-transparent peer-checked:ring-[#2c4a6f] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2c4a6f]">Oceano</span>
                        </label>
                        {{-- Lavanda --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#6a5da8" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#6a5da8] shadow-md ring-2 ring-transparent peer-checked:ring-[#6a5da8] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6a5da8]">Lavanda</span>
                        </label>
                        {{-- Místico --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#5b3265" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#5b3265] shadow-md ring-2 ring-transparent peer-checked:ring-[#5b3265] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#5b3265]">Místico</span>
                        </label>
                        {{-- Magia --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8a3052" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#8a3052] shadow-md ring-2 ring-transparent peer-checked:ring-[#8a3052] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#8a3052]">Magia</span>
                        </label>
                        {{-- Rubi --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#9d2d2d" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#9d2d2d] shadow-md ring-2 ring-transparent peer-checked:ring-[#9d2d2d] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#9d2d2d]">Rubi</span>
                        </label>
                        {{-- Rosé --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#b16478" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#b16478] shadow-md ring-2 ring-transparent peer-checked:ring-[#b16478] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#b16478]">Rosé</span>
                        </label>
                        {{-- Sombra --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#595959" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#595959] shadow-md ring-2 ring-transparent peer-checked:ring-[#595959] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#595959]">Sombra</span>
                        </label>
                        {{-- Obsidian --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2a2a2a" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2a2a2a] shadow-md ring-2 ring-transparent peer-checked:ring-[#2a2a2a] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2a2a2a]">Obsidian</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Rodapé --}}
            <div class="px-6 py-4 border-t border-[#cdbb9f]/50 flex justify-end items-center gap-2.5 bg-[#efe9dc]">
                <button
                    type="button"
                    onclick="closeEditFolderModal()"
                    class="px-4 py-2 rounded-xl border border-[#cdbb9f] font-serif text-xs font-bold text-[#8c6239] hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 transition-all"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-[#6b1d14] hover:bg-[#802319] active:bg-[#54160f] text-[#f4f1e8] font-serif text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-lg transition-all"
                >
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

    {{-- V66.0.58 — Importação com enquadramento do retrato. --}}
    @include('npcs.partials.import-npc-modal')

    {{-- Scripts de Controle dos Modais, Menus e Navegação --}}
    <script>

        /* V66.1.09 — fecha apenas menus contextuais ao sair deles. */
        (function initNpcContextMenus() {
            const menus = Array.from(document.querySelectorAll('[data-npc-context-menu]'));
            menus.forEach(menu => menu.addEventListener('toggle', () => {
                if (menu.open) menus.forEach(other => { if (other !== menu) other.open = false; });
            }));
            document.addEventListener('pointerdown', event => {
                if (!menus.some(menu => menu.contains(event.target))) {
                    menus.forEach(menu => { menu.open = false; });
                }
            });
            document.addEventListener('keydown', event => {
                if (event.key !== 'Escape') return;
                const openMenu = menus.find(menu => menu.open);
                if (!openMenu) return;
                openMenu.open = false;
                openMenu.querySelector('summary')?.focus();
            });
        })();

        /* V66.1.07 — troca entre seções juntas e abas; usa os mesmos cards e pastas. */
        (function initNpcDisplayMode() {
            const scope = document.querySelector('.npc-page-scope');
            const switchButton = document.getElementById('npc-view-switch');
            const tabs = document.getElementById('npc-vault-view-tabs');
            if (!scope || !switchButton || !tabs) return; // Modo de busca já usa Resultados.

            const sections = {
                library: scope.querySelector('[data-npc-view-section="library"]'),
                folders: scope.querySelector('[data-npc-view-section="folders"]')
            };
            if (!sections.library || !sections.folders) return;
            const tabButtons = Array.from(tabs.querySelectorAll('[data-npc-view-tab]'));
            const label = switchButton.querySelector('[data-npc-view-switch-label]');
            const userId = scope.dataset.npcOwner || 'guest';
            // Chave estável entre versões, por conta. Persiste após recarregar e fechar o navegador.
            const storageKey = 'spellbound:npcs:preferred-display:' + userId;
            const previousStorageKey = 'spellbound:npcs:view-mode:v107:' + userId;
            let mode = 'combined';
            let activeTab = 'library';

            function restorePreference(raw) {
                if (!raw) return false;
                try {
                    const saved = JSON.parse(raw);
                    if (!saved || !['combined', 'separate'].includes(saved.mode)) return false;
                    mode = saved.mode;
                    activeTab = saved.tab === 'folders' ? 'folders' : 'library';
                    return true;
                } catch (_) { return false; }
            }
            try {
                const current = localStorage.getItem(storageKey);
                // Migra a preferência da V66.1.07, sem obrigar uma nova escolha.
                if (!restorePreference(current) && restorePreference(localStorage.getItem(previousStorageKey))) {
                    localStorage.setItem(storageKey, JSON.stringify({ mode, tab: activeTab }));
                }
            } catch (_) { /* Permite uso normal quando o armazenamento estiver indisponível. */ }

            const save = () => {
                try { localStorage.setItem(storageKey, JSON.stringify({ mode, tab: activeTab })); }
                catch (_) { /* Ignora bloqueio de storage sem impedir a navegação. */ }
            };
            function render() {
                const separate = mode === 'separate';
                scope.dataset.npcViewMode = mode;
                tabs.hidden = !separate;
                switchButton.setAttribute('aria-pressed', String(separate));
                switchButton.setAttribute('aria-label', separate
                    ? 'Mostrar Biblioteca e Pastas juntas' : 'Separar Biblioteca e Pastas em abas');
                if (label) label.textContent = separate ? 'Mostrar tudo' : 'Separar em abas';
                Object.entries(sections).forEach(([name, section]) => {
                    section.hidden = separate && name !== activeTab;
                    section.setAttribute('role', separate ? 'tabpanel' : 'region');
                    section.setAttribute('aria-labelledby', separate
                        ? (name === 'library' ? 'npc-view-tab-library' : 'npc-view-tab-folders')
                        : (name === 'library' ? 'npc-vault-library-heading' : 'npc-vault-folders-heading'));
                });
                tabButtons.forEach(button => {
                    const selected = button.dataset.npcViewTab === activeTab;
                    button.setAttribute('aria-selected', String(selected));
                    button.tabIndex = selected ? 0 : -1;
                    button.classList.toggle('is-active', selected);
                });
            }
            switchButton.addEventListener('click', () => {
                mode = mode === 'combined' ? 'separate' : 'combined';
                render();
                save();
            });
            tabButtons.forEach((button, index) => {
                button.addEventListener('click', () => {
                    activeTab = button.dataset.npcViewTab;
                    render();
                    save();
                });
                button.addEventListener('keydown', event => {
                    let next = null;
                    if (event.key === 'ArrowRight') next = (index + 1) % tabButtons.length;
                    if (event.key === 'ArrowLeft') next = (index - 1 + tabButtons.length) % tabButtons.length;
                    if (event.key === 'Home') next = 0;
                    if (event.key === 'End') next = tabButtons.length - 1;
                    if (next === null) return;
                    event.preventDefault();
                    tabButtons[next].click();
                    tabButtons[next].focus();
                });
            });
            window.addEventListener('storage', event => {
                if (event.key === storageKey && restorePreference(event.newValue)) render();
            });
            render();
        })();

        /* Galeria de pastas: o viewport desliza 1 retrato, sem mover a seta.
           O sexto retrato fica fora da janela até a animação trazê-lo para dentro. */
        document.querySelectorAll('.npc-folder-card__viewport[data-folder-preview]').forEach(function (viewport) {
            const strip = viewport.querySelector('.npc-folder-card__portraits');
            const portraits = Array.from(strip.querySelectorAll('.npc-folder-card__portrait'));
            const arrow = viewport.closest('.npc-folder-card__gallery')?.querySelector('.npc-folder-card__next');
            if (!strip || !portraits.length) return;
            const gallery = viewport.closest('.npc-folder-card__gallery');
            const people = viewport.closest('.npc-folder-card__people');
            const counter = people?.querySelector('.npc-folder-card__count');
            let visibleCount = Math.min(5, portraits.length);
            let canAdvance = false;

            let offset = 0;
            let animating = false;
            let pending = 0;
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

            // O número de retratos depende da largura REAL do card, não da tela.
            // A seta e o contador reservam espaço antes de calcular quantos cabem.
            function fitToCard() {
                if (!people || !gallery || !counter) return;
                const css = getComputedStyle(viewport);
                const thumb = parseFloat(css.getPropertyValue('--npc-thumb-size')) || 54;
                const step = parseFloat(css.getPropertyValue('--npc-thumb-step')) || 44;
                const peopleGap = parseFloat(getComputedStyle(people).columnGap) || 10;
                const galleryGap = parseFloat(getComputedStyle(gallery).columnGap) || 6;
                const counterWidth = counter.getBoundingClientRect().width;
                const available = Math.max(thumb, people.clientWidth - counterWidth - peopleGap);
                const capacity = width => Math.max(1, Math.min(5, 1 + Math.floor((width - thumb + 0.5) / step)));
                const withoutArrow = capacity(available);
                const needsArrow = portraits.length > withoutArrow || portraits.length > 5;
                const arrowWidth = needsArrow ? (arrow?.offsetWidth || 27) + galleryGap : 0;
                visibleCount = Math.min(portraits.length, capacity(available - arrowWidth));
                canAdvance = portraits.length > visibleCount;
                if (arrow) arrow.hidden = !canAdvance;
                viewport.style.width = `${thumb + (visibleCount - 1) * step}px`;
                viewport.style.flexBasis = viewport.style.width;
                viewport.style.cursor = canAdvance ? 'pointer' : 'default';
                viewport.tabIndex = canAdvance ? 0 : -1;
                if (canAdvance) viewport.setAttribute('role', 'button');
                else viewport.removeAttribute('role');
                paint();
            }

            function paint() {
                portraits.forEach(function (portrait) {
                    portrait.hidden = true;
                    portrait.style.removeProperty('order');
                    portrait.removeAttribute('data-visible-slot');
                });
                const shown = [];
                const windowCount = Math.min(visibleCount + 1, portraits.length);
                for (let slot = 0; slot < windowCount; slot++) {
                    const portrait = portraits[(offset + slot) % portraits.length];
                    portrait.style.order = String(slot);
                    portrait.dataset.visibleSlot = String(slot + 1);
                    portrait.hidden = false;
                    if (slot < visibleCount) shown.push(portrait.title || 'NPC');
                }
                viewport.setAttribute('aria-label', 'NPCs visíveis: ' + shown.join(', ') + (canAdvance ? '. Clique para avançar.' : '.'));
            }

            async function advance() {
                if (!canAdvance) return;
                if (animating) { pending = Math.min(pending + 1, 2); return; }
                animating = true;
                try {
                    // Sem animação, o conteúdo muda imediatamente, inclusive em acessibilidade.
                    if (!reducedMotion.matches && strip.animate) {
                        // Passo fixo do CSS: não muda mesmo se um retrato estiver em hover.
                        const step = parseFloat(getComputedStyle(viewport).getPropertyValue('--npc-thumb-step')) || 44;
                        const animation = strip.animate([
                            { transform: 'translateX(0)' },
                            { transform: `translateX(-${step}px)` },
                        ], { duration: 370, easing: 'cubic-bezier(.22,.61,.36,1)' });
                        await animation.finished.catch(() => {});
                    }
                    offset = (offset + 1) % portraits.length;
                    viewport.dataset.previewIndex = String(offset);
                    paint(); // reposiciona a faixa após a saída do retrato, sem salto visual
                } finally {
                    animating = false;
                    if (pending > 0) { pending--; advance(); }
                }
            }

            function onAdvance(event) {
                if (!canAdvance) return; // Sem rolagem, clique normal continua abrindo a pasta.
                event.preventDefault();
                event.stopPropagation();
                advance();
            }
            viewport.addEventListener('click', onAdvance);
            arrow?.addEventListener('click', onAdvance);
            viewport.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                onAdvance(event);
            });
            // ResizeObserver também detecta quando a largura muda por sidebar/zoom.
            let fitFrame = 0;
            const scheduleFit = () => {
                window.cancelAnimationFrame(fitFrame);
                fitFrame = window.requestAnimationFrame(() => {
                    if (animating) { scheduleFit(); return; }
                    fitToCard();
                });
            };
            fitToCard();
            if ('ResizeObserver' in window) new ResizeObserver(scheduleFit).observe(people);
            else window.addEventListener('resize', scheduleFit, { passive: true });
        });

        function openFolderModal() {
            document.getElementById('folderModal').classList.remove('hidden');
        }

        function closeFolderModal() {
            document.getElementById('folderModal').classList.add('hidden');
        }


        function toggleFolderMenu(id) {
            document.querySelectorAll('[id^="folder-menu-"]').forEach(menu => {
                if (menu.id !== `folder-menu-${id}`) {
                    menu.classList.add('hidden');
                }
            });
            const targetMenu = document.getElementById(`folder-menu-${id}`);
            if (targetMenu) {
                targetMenu.classList.toggle('hidden');
            }
        }

function editFolder(button) {
    // 1. Extrai os dados dos atributos data-* do botão
    const id = button.dataset.id;
    const name = button.dataset.name || '';
    const subtitle = button.dataset.subtitle || '';
    const color = (button.dataset.color || '#8c6239').toLowerCase();

    // 2. Preenche a action e os campos de texto
    document.getElementById('editFolderForm').action = `/folders/${id}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_subtitle').value = subtitle;

    // 3. Seleciona o radio button da cor correspondente
    const radios = document.querySelectorAll('#editFolderModal input[name="color"]');
    
    // Procura o radio que possui exatamente a mesma cor (ignorando maiúsculas/minúsculas)
    let matchedRadio = Array.from(radios).find(r => r.value.toLowerCase() === color);

    // Caso a cor antiga não exista nas opções, usa a primeira como padrão para não enviar nulo
    if (!matchedRadio) {
        matchedRadio = radios[0];
    }
    
    matchedRadio.checked = true;

    // 4. Abre o modal
    document.getElementById('editFolderModal').classList.remove('hidden');
}

function closeEditFolderModal() {
    document.getElementById('editFolderModal').classList.add('hidden');
}

        /* Preview-only: remove o branco conectado às bordas de imagens opacas.
           Não altera os arquivos armazenados nem o enquadramento salvo do NPC. */
        (function cleanFolderThumbnailBackgrounds() {
            const maxSide = 160;
            document.querySelectorAll('.npc-folder-card__portrait img').forEach(img => {
                const prepare = () => {
                    if (img.dataset.npcThumbChecked === '1') return;
                    img.dataset.npcThumbChecked = '1';
                    if (!img.naturalWidth || !img.naturalHeight) return;
                    try {
                        const ratio = Math.min(1, maxSide / Math.max(img.naturalWidth, img.naturalHeight));
                        const w = Math.max(2, Math.round(img.naturalWidth * ratio));
                        const h = Math.max(2, Math.round(img.naturalHeight * ratio));
                        const canvas = document.createElement('canvas');
                        canvas.width = w; canvas.height = h;
                        const ctx = canvas.getContext('2d', { willReadFrequently: true });
                        if (!ctx) return;
                        ctx.drawImage(img, 0, 0, w, h);
                        const pixels = ctx.getImageData(0, 0, w, h);
                        const data = pixels.data;
                        const corner = [0, w-1, (h-1)*w, w*h-1];
                        const white = p => {
                            const n = p*4;
                            return data[n+3] > 242 && data[n] > 229 && data[n+1] > 229 && data[n+2] > 225
                                && Math.max(data[n], data[n+1], data[n+2]) - Math.min(data[n], data[n+1], data[n+2]) < 34;
                        };
                        if (corner.filter(white).length < 3) return;
                        const visited = new Uint8Array(w*h);
                        const queue = new Int32Array(w*h);
                        let head = 0, tail = 0;
                        const enqueue = p => { if (!visited[p] && white(p)) { visited[p]=1; queue[tail++]=p; } };
                        for (let x=0; x<w; x++) { enqueue(x); enqueue((h-1)*w+x); }
                        for (let y=0; y<h; y++) { enqueue(y*w); enqueue(y*w+w-1); }
                        let removed = 0;
                        while (head < tail) {
                            const p = queue[head++], n = p*4;
                            data[n+3] = 0; removed++;
                            const x=p%w, y=Math.floor(p/w);
                            if (x>0) enqueue(p-1);
                            if (x<w-1) enqueue(p+1);
                            if (y>0) enqueue(p-w);
                            if (y<h-1) enqueue(p+w);
                        }
                        if (removed < Math.round(w*h*.03) || removed > Math.round(w*h*.94)) return;
                        ctx.putImageData(pixels, 0, 0);
                        img.classList.add('npc-thumb-white-bg');
                        img.src = canvas.toDataURL('image/png');
                    } catch (_) {
                        // Falhas de canvas/CORS preservam a imagem original, sem quebrar a galeria.
                    }
                };
                if (img.complete) prepare();
                else img.addEventListener('load', prepare, { once: true });
            });
        })();

        // Navegação ao clicar no card da pasta (ignorando botões, forms e menus)
        document.querySelectorAll('.folder-dropzone').forEach(folder => {
            folder.addEventListener('click', function(e) {
                if (
                    e.target.closest('button') ||
                    e.target.closest('form') ||
                    e.target.closest('[id^="folder-menu-"]')
                ) {
                    return;
                }

                if (folder.dataset.url) {
                    window.location = folder.dataset.url;
                }
            });
        });

        // Fecha menus abertos ao clicar fora
        document.addEventListener('click', function () {
            document.querySelectorAll('[id^="folder-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        });


    </script>
</x-app-layout>