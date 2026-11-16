<x-app-layout>

    <style>

        /* Remove espaçamentos vazios para alinhar ao header */

        main:has(.combats-index-page-scope),

        .py-12:has(.combats-index-page-scope),

        .max-w-7xl:has(.combats-index-page-scope) {

            padding: 0 !important;

            max-width: 100% !important;

            margin-top: 0 !important;

            margin-bottom: 0 !important;

        }

        header {

            background-color: transparent !important;

            border-bottom: none !important;

            box-shadow: none !important;

        }

        aside, .sidebar, body {

            overflow-x: hidden !important;

        }

        .transition-polished {

            transition: all 0.25s ease-in-out;

        }


        /* V66.0.47 — encontros mais visuais, sem alterar as rotas ou os dados. */
        .sb-encounters-list { display:flex; flex-direction:column; gap:24px; }
        .sb-encounter-card {
            position:relative; isolation:isolate;
            background:var(--theme-surface-card,#fcfaf7);
            border:1px solid var(--theme-border,#d5bfa7);
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 8px 24px rgba(56,34,18,.075),0 1px 3px rgba(56,34,18,.035);
            transition:border-color .2s ease,box-shadow .2s ease,transform .2s ease;
        }
        .sb-encounter-card:hover {
            border-color:var(--theme-border-strong,#bb9872);
            box-shadow:0 14px 34px rgba(56,34,18,.105),0 2px 4px rgba(56,34,18,.035);
            transform:translateY(-2px);
        }
        .sb-encounter-card__top {
            display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
            padding:19px 23px 15px;
        }
        .sb-encounter-card__name {
            font:bold clamp(20px,2.15vw,26px)/1.14 Georgia,'Times New Roman',serif;
            letter-spacing:.005em;color:var(--theme-text-strong,#631f16);
            overflow-wrap:anywhere;margin:0;
        }
        .sb-encounter-card__context {
            display:flex;align-items:center;flex-wrap:wrap;gap:6px 11px;
            margin-top:8px;color:var(--theme-text-secondary,#917256);
            font:500 11px/1.45 Georgia,serif;
        }
        .sb-encounter-card__context span { display:inline-flex;align-items:center;gap:6px; }
        .sb-encounter-card__context span+span:before { content:'·';font-size:13px;opacity:.65;margin-right:2px; }
        .sb-encounter-card__status {
            flex:none;display:inline-flex;align-items:center;gap:7px;padding:7px 11px;
            border-radius:8px;border:1px solid #d9c39e;
            color:#926126;background:#fbf1db;
            font:800 10px/1.2 ui-sans-serif,system-ui,sans-serif;
            letter-spacing:.065em;text-transform:uppercase;
        }
        .sb-encounter-card__status:before { content:'';width:6px;height:6px;border-radius:50%;background:currentColor; }
        .sb-encounter-card__status--active { color:#167457;border-color:#b1d9c1;background:#e9f6ef; }
        .sb-encounter-card__status--ended { color:#755951;border-color:#d8c3b9;background:#f4ebe5; }

        /* Uma faixa de confronto ilustrada, com os personagens como protagonistas. */
        .sb-encounter-card__versus {
            position:relative;isolation:isolate;
            display:grid;grid-template-columns:minmax(0,1fr) 100px minmax(0,1fr);
            align-items:center;gap:6px;min-height:176px;
            margin:0 17px 16px;padding:21px 28px;
            border:1px solid var(--theme-border-soft,#e4d7c4);
            border-radius:15px;
            background:
                radial-gradient(ellipse at 15% 92%,rgba(37,135,95,.13),transparent 63%),
                radial-gradient(ellipse at 87% 10%,rgba(160,59,47,.11),transparent 58%),
                linear-gradient(110deg,var(--theme-surface-inset,#f7f3eb),var(--theme-surface-card,#fffaf6) 53%,var(--theme-surface-inset,#f7f1e9));
            overflow:hidden;
        }
        .sb-encounter-card__versus:before {
            content:'';position:absolute;inset:9px;
            border:1px solid rgba(148,112,75,.10);border-radius:11px;
            pointer-events:none;
        }
        .sb-encounter-team {position:relative;z-index:1;min-width:0;}
        .sb-encounter-team--enemy {text-align:right;}
        .sb-encounter-team__heading {
            display:inline-flex;align-items:center;gap:7px;margin-bottom:12px;
            color:var(--theme-text-secondary,#82694d);
            font:900 10px/1.2 ui-sans-serif,system-ui,sans-serif;
            letter-spacing:.13em;text-transform:uppercase;
        }
        .sb-encounter-team__heading:before {content:'';width:7px;height:7px;background:currentColor;transform:rotate(45deg);opacity:.68;}
        .sb-encounter-team--ally .sb-encounter-team__heading {color:#237e5f;}
        .sb-encounter-team--enemy .sb-encounter-team__heading {color:#a04437;}
        .sb-encounter-team__portraits {display:flex;align-items:center;min-height:91px;min-width:0;}
        .sb-encounter-team--enemy .sb-encounter-team__portraits {justify-content:flex-end;}
        .sb-encounter-portrait {
            position:relative;display:flex;align-items:center;justify-content:center;
            height:86px;width:69px;flex:0 0 69px;margin-right:-12px;
            overflow:hidden;border:2px solid var(--theme-surface-card,#fcfaf7);border-radius:12px;
            background:#d3dacb;color:#376650;
            box-shadow:0 5px 11px rgba(25,20,15,.19),0 0 0 1px rgba(85,74,48,.09);
            font:bold 25px/1 Georgia,serif;
            transition:transform .2s,box-shadow .2s;
        }
        .sb-encounter-team--enemy .sb-encounter-portrait {margin-right:0;margin-left:-12px;background:#e6c8bb;color:#823e32;}
        .sb-encounter-portrait:hover {z-index:12!important;transform:translateY(-5px) scale(1.035);box-shadow:0 9px 18px rgba(25,20,15,.27);}
        .sb-encounter-portrait img {display:block;width:100%;height:100%;object-fit:cover;object-position:top center;}
        .sb-encounter-overflow {
            display:inline-flex;align-items:center;justify-content:center;
            flex:none;min-width:36px;height:34px;margin:0 5px;padding:0 6px;
            border-radius:9px;border:1px solid var(--theme-border,#d9c5af);
            background:var(--theme-surface-inset,#f1e5d7);
            color:var(--theme-text-strong,#674c3a);
            font:800 11px/1 ui-sans-serif,system-ui,sans-serif;
        }
        .sb-encounter-team__empty {color:var(--theme-text-secondary,#917f6e);font-size:12px;font-style:italic;}
        .sb-encounter-vs {
            position:relative;z-index:2;display:grid;place-items:center;
            justify-self:center;width:76px;height:76px;
            border:1px solid rgba(163,120,70,.34);border-radius:50%;
            background:var(--theme-surface-card,#fffaf3);
            box-shadow:0 0 0 6px rgba(255,249,240,.40),0 5px 18px rgba(80,44,20,.10);
            color:var(--theme-accent-strong,#7c3024);
            font:bold italic 26px/1 Georgia,serif;letter-spacing:-.06em;
        }
        .sb-encounter-vs:before,.sb-encounter-vs:after {
            content:'';position:absolute;left:50%;width:18px;height:1px;
            transform:translateX(-50%);background:var(--theme-border-strong,#b6966b);
        }
        .sb-encounter-vs:before {top:12px;}.sb-encounter-vs:after {bottom:12px;}

        /* Rodapé: dados em blocos leves, o principal continua sendo o confronto. */
        .sb-encounter-card__bottom {
            display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;
            padding:15px 22px;
            border-top:1px solid var(--theme-border-soft,#e8d9c4);
            background:var(--theme-surface-inset,#f7f0e6);
        }
        .sb-encounter-card__timeline {display:flex;flex-wrap:wrap;gap:9px 22px;align-items:center;min-width:0;}
        .sb-encounter-time {display:flex;flex-direction:column;gap:4px;}
        .sb-encounter-time__label {font:900 9px/1 ui-sans-serif,system-ui,sans-serif;letter-spacing:.1em;text-transform:uppercase;color:var(--theme-label,#967450);}
        .sb-encounter-time__value {font:700 12px/1.2 Georgia,serif;color:var(--theme-text-strong,#633f33);}
        .sb-encounter-actions {display:flex;align-items:center;gap:9px;margin-left:auto;}
        .sb-encounter-primary {display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:10px 17px;white-space:nowrap;border-radius:9px;background:var(--theme-button-background,#6b1d14);color:var(--theme-button-text,#fff8ef);font:800 11px/1 ui-sans-serif,system-ui,sans-serif;text-decoration:none;transition:background .2s,transform .2s;}
        .sb-encounter-primary:hover {background:var(--theme-button-hover-background,#83291c);transform:translateY(-1px);}
        .sb-encounter-delete {display:grid;place-items:center;width:38px;height:38px;border:1px solid var(--theme-border,#dccab5);border-radius:9px;background:var(--theme-surface-card,#fffaf5);color:var(--theme-text-secondary,#86644b);transition:color .2s,border-color .2s;}
        .sb-encounter-delete:hover {color:#ac3532;border-color:#daaea8;}
        @media(max-width:850px) {
            .sb-encounter-card__versus {grid-template-columns:minmax(0,1fr) 64px minmax(0,1fr);padding:17px 13px;min-height:151px;gap:3px;}
            .sb-encounter-portrait {height:70px;width:57px;flex-basis:57px;margin-right:-12px;}
            .sb-encounter-team--enemy .sb-encounter-portrait {margin-left:-12px;}
            .sb-encounter-vs {width:54px;height:54px;font-size:20px;}
            .sb-encounter-vs:before {top:7px;}.sb-encounter-vs:after {bottom:7px;}
        }
        @media(max-width:600px) {
            .sb-encounter-card {border-radius:16px;}
            .sb-encounter-card__top {padding:15px 15px 11px;}
            .sb-encounter-card__name {font-size:20px;}
            .sb-encounter-card__context {font-size:10px;}
            .sb-encounter-card__versus {grid-template-columns:minmax(0,1fr) 43px minmax(0,1fr);margin:0 9px 10px;padding:13px 7px;min-height:124px;}
            .sb-encounter-team__heading {font-size:8px;letter-spacing:.08em;margin-bottom:9px;}
            .sb-encounter-portrait {height:56px;width:42px;flex-basis:42px;margin-right:-13px;border-radius:8px;}
            .sb-encounter-team--enemy .sb-encounter-portrait {margin-left:-13px;}
            .sb-encounter-vs {width:37px;height:37px;font-size:15px;box-shadow:none;}
            .sb-encounter-vs:before,.sb-encounter-vs:after {width:8px;}
            .sb-encounter-team__portraits {min-height:60px;}
            .sb-encounter-overflow {height:25px;min-width:25px;font-size:9px;margin:0 2px;}
            .sb-encounter-card__bottom {padding:13px 14px;gap:13px;}
            .sb-encounter-card__timeline {gap:10px 15px;}
            .sb-encounter-actions {width:100%;}
            .sb-encounter-primary {flex:1;}
        }
        @media(prefers-reduced-motion:reduce){.sb-encounter-card,.sb-encounter-portrait,.sb-encounter-primary {transition:none;}}


        /* V66.0.49 — retratos um pouco maiores, sempre enquadrados pelo alto. */
        .sb-encounter-portrait img { object-position: 50% 0 !important; }
        @media(min-width:821px) {
            .sb-encounter-portrait,.sb-encounter-team--enemy .sb-encounter-portrait {
                width:82px;height:100px;flex-basis:82px;
            }
        }
        .sb-result-save {
            min-height:38px; padding:10px 16px; border-radius:9px;
            background:var(--theme-surface-card,#fffaf4);
            border:1px solid var(--theme-border-strong,#c7ac8e);
            color:var(--theme-text-strong,#6b1d14);
            font:800 11px/1 system-ui,sans-serif;
            cursor:pointer;white-space:nowrap;
        }
        .sb-result-save:hover {background:var(--theme-card-hover,#f7eee0)}
        .sb-archive-shortcut {
            display:inline-flex;align-items:center;justify-content:center;
            min-height:32px;padding:7px 12px;
            background:var(--theme-surface-card,#fffaf3);
            border:1px solid var(--theme-border,#dbc9b6);border-radius:9px;
            color:var(--theme-text-strong,#6b1d14);
            font:800 11px/1 system-ui,sans-serif;text-decoration:none;
        }
        @media(max-width:600px){.sb-result-save{flex:1}}

        /* V66.0.48 — formação visual compacta: retratos próximos do centro.
           Preserva cartões, fontes, ações e paletas já aprovados. */
        .sb-encounter-card__versus {
            grid-template-columns:minmax(0,1fr) 74px minmax(0,1fr);
            gap:10px;
            min-height:168px;
            padding:17px clamp(14px,3vw,38px);
        }
        .sb-encounter-team {display:flex; flex-direction:column; justify-content:center; min-width:0;}
        .sb-encounter-team--ally {align-items:flex-end;text-align:right;}
        .sb-encounter-team--enemy {align-items:flex-start;text-align:left;}
        .sb-encounter-team__heading {margin:0 0 13px;white-space:nowrap;}
        .sb-encounter-team__heading span {letter-spacing:0;opacity:.78;}
        .sb-encounter-team__portraits {min-height:94px;display:flex;align-items:center;justify-content:flex-start;max-width:100%;}
        .sb-encounter-team--ally .sb-encounter-team__portraits {justify-content:flex-end;flex-direction:row;}
        .sb-encounter-team--enemy .sb-encounter-team__portraits {justify-content:flex-start;flex-direction:row;}
        .sb-encounter-portrait,
        .sb-encounter-team--enemy .sb-encounter-portrait {
            width:76px;height:92px;flex-basis:76px;margin-right:0;margin-left:0;
            border-radius:11px;border:2px solid var(--theme-surface-card,#fcfaf7);
            box-shadow:0 5px 11px rgba(37,27,21,.14);
            z-index:calc(10 + var(--portrait-order,0));
        }
        .sb-encounter-team--ally .sb-encounter-portrait + .sb-encounter-portrait,
        .sb-encounter-team--enemy .sb-encounter-portrait + .sb-encounter-portrait {margin-left:-19px;}
        .sb-encounter-team__portraits .sb-encounter-portrait:first-child {margin-left:0;}
        .sb-encounter-portrait img {object-position:center 17%;}
        .sb-encounter-portrait:hover {z-index:22!important;transform:translateY(-5px) scale(1.045);}
        .sb-encounter-vs {width:61px;height:61px;font-size:23px;box-shadow:0 0 0 5px rgba(255,248,236,.24);}
        .sb-encounter-vs:before {top:8px;}.sb-encounter-vs:after {bottom:8px;}
        .sb-encounter-card__versus--solo {display:flex; flex-direction:column; gap:8px;align-items:center;justify-content:center;min-height:168px;}
        .sb-encounter-card__versus--solo .sb-encounter-team {align-items:center;text-align:center;}
        .sb-encounter-card__versus--solo .sb-encounter-team__portraits {justify-content:center;}
        .sb-encounter-solo-label {font:800 9px ui-sans-serif,system-ui,sans-serif;letter-spacing:.18em;color:var(--theme-label,#957357);opacity:.86;order:-1;}
        .sb-encounter-card__versus--draft {background:linear-gradient(120deg,var(--theme-surface-inset,#f3ede3),var(--theme-surface-card,#fcf8f1) 52%,var(--theme-surface-inset,#f2e9dc));}
        .sb-encounter-empty {
            min-height:137px;margin:0 17px 16px;padding:17px;
            display:flex;flex-direction:column;justify-content:center;align-items:center;gap:5px;
            border:1px solid var(--theme-border-soft,#e1d1b9);border-radius:14px;
            background:linear-gradient(125deg,var(--theme-surface-inset,#f3ede3),var(--theme-surface-card,#fbf7f0));text-align:center;
        }
        .sb-encounter-empty__icon {width:39px;height:39px;display:grid;place-items:center;color:var(--theme-accent-strong,#86553a);opacity:.76;}
        .sb-encounter-empty__icon svg {width:30px;height:30px;}
        .sb-encounter-empty__title {font:700 16px Georgia,serif;color:var(--theme-text-strong,#6b1d14);}
        .sb-encounter-empty__description {font:500 11px Georgia,serif;color:var(--theme-text-secondary,#927861);}
        @media(max-width:820px){
            .sb-encounter-portrait,.sb-encounter-team--enemy .sb-encounter-portrait {width:59px;height:76px;flex-basis:59px;}
            .sb-encounter-team--ally .sb-encounter-portrait + .sb-encounter-portrait,
            .sb-encounter-team--enemy .sb-encounter-portrait + .sb-encounter-portrait {margin-left:-21px;}
            .sb-encounter-card__versus {grid-template-columns:minmax(0,1fr) 54px minmax(0,1fr);padding:14px;min-height:145px;gap:4px;}
            .sb-encounter-vs {width:46px;height:46px;font-size:17px;}
            .sb-encounter-team__heading {font-size:9px;letter-spacing:.07em;}
        }
        @media(max-width:520px){
            .sb-encounter-card__versus {grid-template-columns:minmax(0,1fr) 33px minmax(0,1fr);margin:0 9px 11px;padding:14px 8px;gap:3px;}
            .sb-encounter-portrait,.sb-encounter-team--enemy .sb-encounter-portrait {width:40px;height:59px;flex-basis:40px;border-radius:7px;}
            .sb-encounter-team--ally .sb-encounter-portrait + .sb-encounter-portrait,
            .sb-encounter-team--enemy .sb-encounter-portrait + .sb-encounter-portrait {margin-left:-14px;}
            .sb-encounter-team__portraits .sb-encounter-portrait:nth-of-type(n+5) {display:none;}
            .sb-encounter-team__heading {font-size:8px;letter-spacing:.015em;gap:4px;margin-bottom:7px;}
            .sb-encounter-team__heading:before {width:5px;height:5px;}
            .sb-encounter-vs {width:30px;height:30px;font-size:13px;}
            .sb-encounter-vs:before,.sb-encounter-vs:after {display:none;}
            .sb-encounter-card__versus--solo {min-height:137px;}
            .sb-encounter-empty {margin:0 9px 11px;}
        }


        /* V66.0.51 — acabamento dos encontros e edição contextual */
        .sb-encounter-card {border-radius:23px;box-shadow:0 12px 30px rgba(63,37,23,.085);}
        .sb-encounter-card__top {padding:22px 24px 17px;}
        .sb-encounter-card__versus {
            min-height:176px;border-radius:17px;
            background:radial-gradient(ellipse at 23% 70%,rgba(40,139,109,.14),transparent 65%),
                       radial-gradient(ellipse at 83% 35%,rgba(150,57,46,.12),transparent 64%),
                       linear-gradient(110deg,var(--theme-surface-card,#faf6ee),var(--theme-surface-inset,#f4eadf),var(--theme-surface-card,#faf6ee));
            box-shadow:inset 0 1px 0 rgba(255,255,255,.38);
        }
        .sb-encounter-card__versus::after {
            content:'';pointer-events:none;position:absolute;inset:0;
            background:linear-gradient(90deg,rgba(40,128,92,.14) 0 3px,transparent 3px calc(100% - 3px),rgba(154,66,49,.13) calc(100% - 3px));
            z-index:0;
        }
        .sb-encounter-card__versus--solo::after {display:none;}
        .sb-encounter-team__heading {padding:4px 11px;border-radius:999px;background:var(--theme-surface-card,#fffcf7);border:1px solid var(--theme-border-soft,#e3d5c7);}
        .sb-encounter-portrait,.sb-encounter-team--enemy .sb-encounter-portrait {
            border-width:2px;box-shadow:0 7px 14px rgba(35,25,20,.17),0 0 0 1px rgba(113,91,72,.13);
        }
        .sb-encounter-vs {
            position:relative;width:73px;height:73px;border:1px solid var(--theme-border-strong,#b79a75);
            background:linear-gradient(145deg,var(--theme-surface-card,#fffaf4),var(--theme-surface-inset,#f0e5d8));
            box-shadow:0 0 0 5px rgba(244,230,210,.50),0 0 0 7px rgba(143,109,79,.11),0 9px 21px rgba(93,46,28,.11);
            display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0;
            letter-spacing:0;
        }
        .sb-encounter-vs:before,.sb-encounter-vs:after {display:none;}
        .sb-encounter-vs svg {width:19px;height:19px;color:var(--theme-label,#a78460);opacity:.82;}
        .sb-encounter-vs span {font:italic 700 22px/1 Georgia,serif;color:var(--theme-accent-strong,#6b1d14);}
        .sb-v51-top-actions {display:flex;align-items:center;gap:8px;flex-shrink:0;}
        .sb-v51-rename {position:relative;}
        .sb-v51-rename>summary {display:grid;place-items:center;width:33px;height:33px;border:1px solid var(--theme-border,#d9c6b1);border-radius:9px;cursor:pointer;color:var(--theme-accent-strong,#69221b);background:var(--theme-surface-card,#fffaf4);list-style:none;transition:background .15s,border-color .15s;}
        .sb-v51-rename>summary::-webkit-details-marker{display:none}
        .sb-v51-rename>summary:hover {background:var(--theme-card-hover,#f5eadf);border-color:var(--theme-border-strong,#b89d82);}
        .sb-v51-rename[open]>summary {border-color:var(--theme-accent-strong,#6b1d14)}
        .sb-v51-rename-form {position:absolute;right:0;top:calc(100% + 9px);z-index:30;width:min(290px,82vw);padding:15px;display:grid;gap:9px;background:var(--theme-surface-raised,var(--theme-surface-card,#fffaf4));border:1px solid var(--theme-border-strong,#c4a788);border-radius:13px;box-shadow:0 13px 29px rgba(44,24,15,.16);}
        .sb-v51-rename-form label {font:800 10px system-ui,sans-serif;letter-spacing:.09em;text-transform:uppercase;color:var(--theme-label,#937050);}
        .sb-v51-rename-form input {width:100%;min-width:0;background:var(--theme-surface-inset,#fffaf4);color:var(--theme-text-strong,#5c201b);border:1px solid var(--theme-border,#dac7b3);border-radius:8px;padding:10px 11px;font:700 13px Georgia,serif;}
        .sb-v51-rename-form button {padding:11px;border:0;border-radius:8px;color:var(--theme-button-text,#fff);background:var(--theme-button-background,#6b1d14);font:800 11px system-ui,sans-serif;cursor:pointer;}
        .sb-encounter-card__bottom {background:linear-gradient(180deg,var(--theme-surface-inset,#f7eee5),var(--theme-surface-card,#fffaf6));}
        @media(max-width:820px){.sb-encounter-vs{width:49px;height:49px}.sb-encounter-vs svg{width:15px;height:15px}.sb-encounter-vs span{font-size:17px}}
        @media(max-width:520px){.sb-encounter-card__top{padding:16px 14px}.sb-encounter-vs{width:33px;height:39px;box-shadow:0 0 0 2px rgba(244,230,210,.45)}.sb-encounter-vs svg{width:12px;height:12px}.sb-encounter-vs span{font-size:12px}.sb-v51-top-actions{gap:5px}.sb-v51-rename>summary{width:29px;height:29px}}


        /* V66.0.52 — menus contextuais, acabamento de cards e VS limpo. */
        .sb-encounters-list {gap:19px;}
        .sb-encounter-card {overflow:visible;border-radius:19px;box-shadow:0 7px 24px rgba(71,44,26,.065);transform:none;}
        .sb-encounter-card:hover {transform:none;box-shadow:0 10px 30px rgba(71,44,26,.085);}
        .sb-encounter-card:has(.sb-v52-menu[open]) {z-index:20;}
        .sb-encounter-card__top {padding:20px 23px 16px;gap:12px;}
        .sb-encounter-card__versus {border-radius:14px;min-height:158px;}
        .sb-encounter-card__versus--solo {min-height:148px;}
        .sb-encounter-card__bottom {border-radius:0 0 18px 18px;padding:13px 21px;}
        .sb-encounter-vs {width:65px;height:65px;display:grid;place-items:center;border-color:var(--theme-border-strong,#bda181);box-shadow:0 0 0 4px rgba(255,247,238,.50),0 7px 17px rgba(61,36,26,.10);}
        .sb-encounter-vs span {font:italic 800 25px/1 Georgia,'Times New Roman',serif;letter-spacing:-.045em;}
        .sb-encounter-vs svg {display:none!important;}
        .sb-v51-top-actions {align-items:center;gap:10px;}
        .sb-v52-menu {position:relative;flex:none;}
        .sb-v52-menu>summary {list-style:none;width:34px;height:34px;display:grid;place-items:center;cursor:pointer;border:1px solid var(--theme-border,#d9c5b1);border-radius:10px;background:var(--theme-surface-card,#fffaf5);color:var(--theme-text-strong,#682d23);transition:background .16s,border-color .16s,box-shadow .16s;}
        .sb-v52-menu>summary::-webkit-details-marker,.sb-v52-menu__edit>summary::-webkit-details-marker {display:none;}
        .sb-v52-menu>summary:hover,.sb-v52-menu[open]>summary {background:var(--theme-card-hover,#f6eadf);border-color:var(--theme-border-strong,#b99779);box-shadow:0 2px 8px rgba(44,23,13,.075);}
        .sb-v52-menu[open]>summary {color:var(--theme-accent-strong,#6b1d14);}
        .sb-v52-menu__panel {position:absolute;z-index:70;right:0;top:calc(100% + 9px);width:min(277px,calc(100vw - 36px));padding:8px;border:1px solid var(--theme-border-strong,#ceb294);border-radius:13px;background:var(--theme-surface-raised,var(--theme-surface-card,#fffaf6));box-shadow:0 16px 36px rgba(46,24,15,.18);color:var(--theme-text-strong,#683229);}
        .sb-v52-menu__caption {margin:3px 8px 9px;font:800 9px/1.3 system-ui,sans-serif;color:var(--theme-label,#97785d);letter-spacing:.12em;text-transform:uppercase;}
        .sb-v52-menu__option {width:100%;min-height:40px;display:flex;align-items:center;gap:10px;text-align:left;padding:9px 10px;border:0;border-radius:9px;cursor:pointer;background:transparent;color:var(--theme-text-strong,#693025);font:700 12px/1.2 system-ui,sans-serif;}
        .sb-v52-menu__option:hover {background:var(--theme-card-hover,#f6ebdf);}
        .sb-v52-menu__option-arrow {margin-left:auto;transition:transform .16s;}
        .sb-v52-menu__edit[open] .sb-v52-menu__option-arrow {transform:rotate(90deg);}
        .sb-v52-menu__rename-form {display:grid;gap:9px;margin:4px 6px 10px;padding:12px;background:var(--theme-surface-inset,#f5eee4);border:1px solid var(--theme-border-soft,#e9dac9);border-radius:9px;}
        .sb-v52-menu__rename-form label {font:800 10px/1 system-ui,sans-serif;color:var(--theme-label,#937052);letter-spacing:.055em;text-transform:uppercase;}
        .sb-v52-menu__rename-form input {width:100%;min-width:0;border-radius:7px;background:var(--theme-surface-card,#fffaf7);border:1px solid var(--theme-border,#d7c5b4);color:var(--theme-text-strong,#64271e);padding:9px;font:700 12px Georgia,serif;}
        .sb-v52-menu__rename-form button {border-radius:7px;padding:10px;background:var(--theme-button-background,#6b1d14);color:var(--theme-button-text,#fffaf5);font:800 11px system-ui,sans-serif;cursor:pointer;}
        .sb-v52-menu__separator {height:1px;background:var(--theme-border-soft,#e8d9c8);margin:6px 5px;}
        .sb-v52-menu__option--danger {color:#a54839;}
        .sb-v52-menu__option--danger:hover {background:rgba(164,67,52,.075);}
        .sb-v52-menu>summary:focus-visible,.sb-v52-menu__option:focus-visible,.sb-v52-menu__rename-form input:focus-visible {outline:2px solid var(--theme-accent-strong,#6b1d14);outline-offset:2px;}
        @media(max-width:820px){.sb-encounter-vs {width:47px;height:47px}.sb-encounter-vs span {font-size:18px}.sb-encounter-card__top {padding:16px}}
        @media(max-width:520px){.sb-encounter-vs {width:34px;height:34px;box-shadow:0 0 0 2px rgba(255,247,238,.5)}.sb-encounter-vs span {font-size:13px}.sb-encounter-card__versus {min-height:132px}.sb-encounter-card__bottom {padding:12px 14px}.sb-v52-menu>summary{width:32px;height:32px}.sb-encounter-card__name {font-size:20px}}

    </style>

    {{-- Gerenciamento de Estado do Modal via Alpine.js --}}

    <div x-data="{ createModalOpen: false }" @keydown.escape.window="createModalOpen = false" class="combats-index-page-scope min-h-screen bg-[#eee8dc] font-serif pb-16 selection:bg-[#6b1d14] selection:text-[#f4f1e8]">

        {{-- Header da página --}}

        <x-slot name="header">

            <div class="flex items-center justify-between gap-4 w-full">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-xl bg-[#6b1d14] flex items-center justify-center text-[#f4f1e8] shadow-sm">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>

                        </svg>

                    </div>

                    <div>

                        <h1 class="text-xl font-bold text-[#4a100a] leading-tight font-serif">

                            Gerenciador de Combates

                        </h1>

                        <p class="text-[11px] text-[#8c6239] italic font-serif">Gerencie seus encontros e sessões ativas</p>

                    </div>

                </div>

                {{-- Botão Principal do Topo --}}

                <button @click="createModalOpen =  true"

                        type="button"

                        class="px-6 py-2.5 rounded-full bg-[#53150f] hover:bg-[#3d0f0a] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-wider transition-polished shadow-md flex items-center gap-2 shrink-0 cursor-pointer">

                    <span class="text-sm font-extrabold leading-none">+</span>

                    <span>Novo Combate</span>

                </button>

            </div>

        </x-slot>

        <main class="max-w-[1500px] mx-auto px-5 sm:px-7 pt-8">
            @php
                // Encontros já arquivados saem da lista principal; execuções novas continuam visíveis.
                $combats->loadMissing(['campaign', 'npcs.npc', 'players.character', 'runs.archive']);
                $combats = $combats->filter(function ($combat) {
                    if ($combat->is_active) return true;
                    $run = $combat->runs->sortByDesc('id')->first();
                    return !($run && $run->ended_at && $run->archive);
                })->values();
            @endphp


            @if(session('success'))

                <div class="mb-6 flex items-center p-3.5 rounded-xl bg-emerald-100/80 border border-emerald-300 text-emerald-900 shadow-sm">

                    <svg class="w-5 h-5 mr-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                    <span class="font-serif text-xs font-semibold">{{ session('success') }}</span>

                </div>

            @endif

            <div class="flex items-center justify-between border-b border-[#d8cebe] pb-4 mb-6">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-xl bg-[#e5decb] border border-[#d8cebe] flex items-center justify-center text-[#53150f]">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>

                        </svg>

                    </div>

                    <div>

                        <h2 class="text-2xl font-serif font-bold text-[#4a100a]">Combates Registrados</h2>

                        <p class="text-xs text-[#8c6239] italic">Seus encontros e confrontos</p>

                    </div>

                </div>

                <div class="flex items-center gap-3 flex-wrap justify-end">

                    <a class="sb-archive-shortcut" href="{{ route('master.combat.archives.index') }}">Histórico de batalhas</a>

                    <span class="px-3 py-1.5 rounded-full text-xs font-serif font-bold text-[#8c6239] bg-[#e5decb] border border-[#d8cebe]">

                        {{ $combats->count() }} {{ $combats->count() === 1 ? 'Combate' : 'Combates' }}

                    </span>

                    <button @click="createModalOpen =  true"

                            type="button"

                            class="px-4 py-1.5 rounded-xl bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-wider transition-polished shadow-sm flex items-center gap-1.5 cursor-pointer">

                        <span class="text-sm font-extrabold leading-none">+</span>

                        <span>Criar</span>

                    </button>

                </div>

            </div>

            <div class="sb-encounters-list">


                @forelse($combats as $combat)
                    @php
                        $lastRun = $combat->runs->sortByDesc('id')->first();
                        // Com um encontro em andamento, nunca mostre os horários de uma execução anterior.
                        $displayRun = $combat->is_active
                            ? $combat->runs->whereNull('ended_at')->sortByDesc('id')->first()
                            : $lastRun;
                        $startedAt = $displayRun?->started_at;
                        $endedAt = $combat->is_active ? null : $displayRun?->ended_at;
                        $status = $combat->is_active ? 'active' : ($endedAt ? 'ended' : 'draft');
                        $allies = collect();
                        $enemies = collect();

                        foreach ($combat->players as $participant) {
                            $character = $participant->character;
                            $name = $character?->name ?: ($participant->name ?: 'Jogador');
                            $allies->push(['name' => $name, 'image' => $character?->image_path]);
                        }

                        foreach ($combat->npcs as $participant) {
                            $npc = $participant->npc;
                            if (!$npc) continue;
                            $item = ['name' => $npc->name, 'image' => $npc->image_path];
                            if (($participant->side ?? 'enemy') === 'ally') $allies->push($item);
                            else $enemies->push($item);
                        }
                    @endphp

                    <article class="sb-encounter-card" aria-label="Encontro {{ $combat->name }}">
                        <div class="sb-encounter-card__top">
                            <div class="min-w-0 flex-1">
                                <h3 class="sb-encounter-card__name">{{ $combat->name }}</h3>
                                <div class="sb-encounter-card__context">
                                    @if($combat->campaign)
                                        <span>Campanha: {{ $combat->campaign->name }}</span>
                                    @else
                                        <span>Encontro independente (legado)</span>
                                    @endif
                                    <span>Criado em {{ $combat->created_at?->format('d/m/Y') ?? '—' }}</span>
                                    <span>Rodada {{ (int) ($combat->current_round ?? 0) }}</span>
                                </div>
                            </div>
                            <div class="sb-v51-top-actions">
                            <span class="sb-encounter-card__status{{ $status === 'active' ? ' sb-encounter-card__status--active' : ($status === 'ended' ? ' sb-encounter-card__status--ended' : '') }}">
                                {{ $status === 'active' ? 'Em andamento' : ($status === 'ended' ? 'Encerrado' : 'Preparação') }}
                            </span>
                            {{-- Ações secundárias reunidas num único menu. --}}
                            <details class="sb-v52-menu" x-on:click.outside="$el.open = false" x-on:keydown.escape.window="$el.open = false">
                                <summary title="Opções do combate" aria-label="Opções do combate {{ $combat->name }}">
                                    <svg viewBox="0 0 24 24" width="19" height="19" fill="currentColor" aria-hidden="true">
                                        <circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/>
                                    </svg>
                                </summary>
                                <div class="sb-v52-menu__panel">
                                    <p class="sb-v52-menu__caption">Opções do encontro</p>
                                    <details class="sb-v52-menu__edit">
                                        <summary class="sb-v52-menu__option">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5Z"/></svg>
                                            <span>Editar nome</span>
                                            <svg class="sb-v52-menu__option-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                                        </summary>
                                        <form action="{{ route('master.combats.rename', $combat) }}" method="POST" class="sb-v52-menu__rename-form">
                                            @csrf
                                            <label for="sb-rename-{{ $combat->id }}">Nome do combate</label>
                                            <input id="sb-rename-{{ $combat->id }}" type="text" name="name" value="{{ $combat->name }}" maxlength="150" minlength="2" required autocomplete="off">
                                            <button type="submit">Salvar alteração</button>
                                        </form>
                                    </details>
                                    <div class="sb-v52-menu__separator"></div>
                                    <form action="{{ route('combats.destroy', $combat) }}" method="POST" onsubmit="return confirm('Excluir permanentemente este combate?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-v52-menu__option sb-v52-menu__option--danger">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m5 4v7m4-7v7"/></svg>
                                            <span>Excluir combate</span>
                                        </button>
                                    </form>
                                </div>
                            </details>
                        </div>
                        </div>

                        @if($allies->isEmpty() && $enemies->isEmpty())
                            <div class="sb-encounter-empty" aria-label="Encontro aguardando participantes">
                                <div class="sb-encounter-empty__icon" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m12 9 27 27M36 9 9 36M9 6l7 7M32 32l8 8M39 6l-7 7M16 32l-8 8"/>
                                        <path d="m8 6 1 8 7-1M40 6l-1 8-7-1M8 42l1-8 7 1M40 42l-1-8-7 1"/>
                                    </svg>
                                </div>
                                <span class="sb-encounter-empty__title">{{ $status === 'draft' ? 'Preparando o encontro' : 'Sem participantes no momento' }}</span>
                                <span class="sb-encounter-empty__description">Adicione criaturas ou jogadores para montar o confronto.</span>
                            </div>
                        @else
                            <div class="sb-encounter-card__versus{{ $allies->isEmpty() || $enemies->isEmpty() ? ' sb-encounter-card__versus--solo' : '' }}{{ $status === 'draft' ? ' sb-encounter-card__versus--draft' : '' }}" aria-label="Composição do confronto">
                                @if($allies->isNotEmpty())
                                    <div class="sb-encounter-team sb-encounter-team--ally">
                                        <div class="sb-encounter-team__heading">Aliados <span>{{ $allies->count() }}</span></div>
                                        <div class="sb-encounter-team__portraits">
                                            @foreach($allies->take(7) as $index => $ally)
                                                <div class="sb-encounter-portrait" style="--portrait-order: {{ $index }}" title="{{ $ally['name'] }}" aria-label="Aliado {{ $ally['name'] }}">
                                                    @if($ally['image'])
                                                        <img src="{{ asset('storage/' . ltrim((string) $ally['image'], '/')) }}" loading="lazy" alt="{{ $ally['name'] }}">
                                                    @else
                                                        <span aria-hidden="true">{{ mb_strtoupper(mb_substr($ally['name'], 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($allies->count() > 7)
                                                <span class="sb-encounter-overflow" title="{{ $allies->count() - 7 }} aliados adicionais">+{{ $allies->count() - 7 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if($allies->isNotEmpty() && $enemies->isNotEmpty())
                                    <div class="sb-encounter-vs" aria-hidden="true"><span>VS</span></div>
                                @elseif($status === 'draft')
                                    <div class="sb-encounter-solo-label">PREPARAÇÃO</div>
                                @endif
                                @if($enemies->isNotEmpty())
                                    <div class="sb-encounter-team sb-encounter-team--enemy">
                                        <div class="sb-encounter-team__heading">Inimigos <span>{{ $enemies->count() }}</span></div>
                                        <div class="sb-encounter-team__portraits">
                                            @foreach($enemies->take(7) as $index => $enemy)
                                                <div class="sb-encounter-portrait" style="--portrait-order: {{ $index }}" title="{{ $enemy['name'] }}" aria-label="Inimigo {{ $enemy['name'] }}">
                                                    @if($enemy['image'])
                                                        <img src="{{ asset('storage/' . ltrim((string) $enemy['image'], '/')) }}" loading="lazy" alt="{{ $enemy['name'] }}">
                                                    @else
                                                        <span aria-hidden="true">{{ mb_strtoupper(mb_substr($enemy['name'], 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($enemies->count() > 7)
                                                <span class="sb-encounter-overflow" title="{{ $enemies->count() - 7 }} inimigos adicionais">+{{ $enemies->count() - 7 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="sb-encounter-card__bottom">
                            <div class="sb-encounter-card__timeline">
                                <div class="sb-encounter-time">
                                    <span class="sb-encounter-time__label">Início</span>
                                    <span class="sb-encounter-time__value">{{ $startedAt?->format('d/m/Y · H:i') ?? 'Não registrado' }}</span>
                                </div>
                                <div class="sb-encounter-time">
                                    <span class="sb-encounter-time__label">Término</span>
                                    <span class="sb-encounter-time__value">{{ $endedAt?->format('d/m/Y · H:i') ?? ($combat->is_active ? 'Em andamento' : '—') }}</span>
                                </div>
                                <div class="sb-encounter-time">
                                    <span class="sb-encounter-time__label">Participantes</span>
                                    <span class="sb-encounter-time__value">{{ $allies->count() + $enemies->count() }}</span>
                                </div>
                            </div>
                            <div class="sb-encounter-actions">
                                @if($status === 'ended' && $lastRun && !$lastRun->archive)
                                    <form action="{{ route('master.combat.archives.save', $combat) }}" method="POST" style="display:flex;gap:7px;align-items:center;flex-wrap:wrap" onsubmit="return confirm('Salvar a imagem e o resultado final desta batalha no histórico?');">
                                        @csrf
                                        <button type="submit" class="sb-result-save">Salvar resultado</button>
                                    </form>
                                @endif
                                <a class="sb-encounter-primary" href="{{ route('combats.show', $combat) }}">{{ $combat->is_active ? 'Continuar combate' : 'Abrir combate' }}</a>

                            </div>
                        </div>
                    </article>

                @empty

                    <div class="bg-[#fcfaf7] border-2 border-dashed border-[#d8cebe] rounded-2xl p-12 text-center">

                        <div class="w-12 h-12 bg-[#eee8dc] rounded-full flex items-center justify-center mx-auto mb-3 text-[#8c6239]">

                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>

                            </svg>

                        </div>

                        <h3 class="text-lg font-serif font-bold text-[#4a100a] mb-1">Nenhum combate encontrado</h3>

                        <p class="text-xs text-[#8c6239] italic mb-4">Crie um novo encontro para começar a organizar sua sessão.</p>

                        <button @click="createModalOpen =  true"

                                type="button"

                                class="inline-flex items-center gap-2 px-4 py-2 bg-[#6b1d14] hover:bg-[#53150f] transition-colors text-[#f4f1e8] rounded-xl text-xs font-serif font-bold uppercase tracking-wider cursor-pointer">

                            Novo Combate

                        </button>

                    </div>

                @endforelse

            </div>

        </main>

        {{-- MODAL DE CRIAÇÃO DE COMBATE --}}

        <div x-show="createModalOpen"

             x-transition:enter="transition ease-out duration-300"

             x-transition:enter-start="opacity-0"

             x-transition:enter-end="opacity-100"

             x-transition:leave="transition ease-in duration-200"

             x-transition:leave-start="opacity-100"

             x-transition:leave-end="opacity-0"

             class="fixed inset-0 z-50 overflow-y-auto bg-[#2b1d17]/60 backdrop-blur-sm flex items-center justify-center p-4"

             style="display: none;">

            {{-- Container do Modal --}}

            <div @click.away="createModalOpen = false"

                 x-show="createModalOpen"

                 x-transition:enter="transition ease-out duration-300"

                 x-transition:enter-start="opacity-0 scale-95"

                 x-transition:enter-end="opacity-100 scale-100"

                 x-transition:leave="transition ease-in duration-200"

                 x-transition:leave-start="opacity-100 scale-100"

                 x-transition:leave-end="opacity-0 scale-95"

                 class="relative w-full max-w-lg bg-gradient-to-br from-[#f4f1e8] to-[#efe9dc] rounded-2xl border border-[#cdbb9f]/80 shadow-2xl p-6 sm:p-8 overflow-hidden font-serif">

                {{-- Detalhes Decorativos RPG nas cantoneiras --}}

                <div class="absolute top-0 left-0 w-10 h-10 border-t-2 border-l-2 border-[#8c6239]/20 rounded-tl-2xl m-3 pointer-events-none"></div>

                <div class="absolute bottom-0 right-0 w-10 h-10 border-b-2 border-r-2 border-[#8c6239]/20 rounded-br-2xl m-3 pointer-events-none"></div>

                {{-- Cabeçalho do Modal --}}

                <div class="flex items-center justify-between pb-4 border-b border-[#cdbb9f]/60 mb-6 relative z-10">

                    <div class="flex items-center gap-3">

                        <div class="w-8 h-8 rounded-lg bg-[#6b1d14] flex items-center justify-center text-[#f4f1e8] shadow-sm">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>

                            </svg>

                        </div>

                        <div>

                            <h3 class="text-lg font-bold text-[#6b1d14] leading-tight">Novo Combate</h3>

                            <p class="text-[10px] uppercase font-bold text-[#8c6239]/80 tracking-widest">Inicie um novo encontro tático</p>

                        </div>

                    </div>

                    <button @click="createModalOpen =  false" class="text-[#8c6239] hover:text-[#6b1d14] transition-colors p-1">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>

                        </svg>

                    </button>

                </div>

                {{-- Formulário --}}

                <form action="{{ route('combats.store') }}" method="POST" class="space-y-6 relative z-10">

                    @csrf

                    <div class="space-y-2">

                        <label for="name" class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#8c6239]">

                            <svg class="w-3.5 h-3.5 text-[#6b1d14]/70" fill="currentColor" viewBox="0 0 20 20">

                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>

                            </svg>

                            Nome do Encontro

                        </label>

                        <input

                            type="text"

                            id="name"

                            name="name"

                            required

                            placeholder="Ex.: Emboscada No Amanhecer Prateado"

                            class="w-full bg-[#f4f1e8] border border-[#cdbb9f] rounded-xl px-4 py-3 text-[#2b1d17] placeholder-[#8c6239]/40 focus:outline-none focus:ring-2 focus:ring-[#6b1d14]/30 focus:border-[#6b1d14] transition-polished shadow-inner text-sm font-serif"

                        >

                        <p class="text-[11px] text-[#8c6239]/80 italic pl-1">

                            Dica: Use nomes sugestivos para organizar o diário da campanha.

                        </p>

                    </div>

                    <div class="space-y-2">

                        <label for="campaign_id" class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#8c6239]">

                            <svg class="h-3.5 w-3.5 text-[#6b1d14]/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M4 6.5h6l2 2h8v9H4z" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>

                            Campanha

                        </label>

                        <select
                            id="campaign_id"
                            name="campaign_id"
                            class="w-full rounded-xl border border-[#cdbb9f] bg-[#f4f1e8] px-4 py-3 text-sm font-serif text-[#2b1d17] shadow-inner transition-polished focus:border-[#6b1d14] focus:outline-none focus:ring-2 focus:ring-[#6b1d14]/30"
                        >
                            <option value="">
                                Combate independente
                            </option>

                            @foreach($campaigns as $campaign)
                                <option
                                    value="{{ $campaign->id }}"
                                    @selected((string) old('campaign_id') === (string) $campaign->id)
                                >
                                    {{ $campaign->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="text-[11px] italic text-[#8c6239]/80 pl-1">
                            Vincule o encontro a uma campanha para adicionar as fichas compartilhadas pelos Players.
                        </p>

                    </div>

                    <div class="flex items-center justify-center gap-4 py-1">

                        <div class="h-px bg-gradient-to-r from-transparent via-[#cdbb9f]/60 to-transparent w-full"></div>

                        <div class="w-1.5 h-1.5 rounded-full bg-[#cdbb9f]/80 rotate-45"></div>

                        <div class="h-px bg-gradient-to-r from-transparent via-[#cdbb9f]/60 to-transparent w-full"></div>

                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-1">

                        <button type="button"

                                @click="createModalOpen =  false"

                                class="w-full sm:w-auto inline-flex items-center justify-center bg-transparent hover:bg-[#8c6239]/10 text-[#8c6239] border border-[#8c6239]/40 hover:text-[#6b1d14] text-xs font-bold uppercase tracking-widest px-5 py-3 rounded-xl transition-polished cursor-pointer">

                            Cancelar

                        </button>

                        <button type="submit"

                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-b from-[#6b1d14] to-[#53150f] hover:from-[#7A1F17] hover:to-[#6b1d14] text-[#f4f1e8] text-xs font-bold uppercase tracking-widest px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition-polished border border-[#4a110a] cursor-pointer">

                            <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>

                            </svg>

                            Criar Combate

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
