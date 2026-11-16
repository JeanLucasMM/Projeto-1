<x-app-layout>
    <style>
        .sb-archive-page{min-height:100vh;background:var(--theme-page-background,#eee8dc);padding:34px 24px 80px;color:var(--theme-text-primary,#60473b);font-family:Georgia,'Times New Roman',serif}
        .sb-archive-wrap{max-width:1200px;margin:auto}
        .sb-archive-nav{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:26px;padding-bottom:18px;border-bottom:1px solid var(--theme-border,#d8cebe)}
        .sb-archive-nav h1{font-size:28px;font-weight:700;line-height:1.2;color:var(--theme-text-strong,#622018)}
        .sb-archive-muted{font:12px/1.5 system-ui,sans-serif;color:var(--theme-text-secondary,#91735d)}
        .sb-archive-back,.sb-archive-action{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid var(--theme-border,#d8cebe);border-radius:10px;padding:10px 13px;background:var(--theme-surface-card,#fffaf3);font:700 12px/1.3 system-ui,sans-serif;color:var(--theme-text-strong,#63231c);text-decoration:none;cursor:pointer}
        .sb-archive-action--primary{background:var(--theme-button-background,#6b1d14);color:var(--theme-button-text,#fffbf7);border-color:var(--theme-button-background,#6b1d14)}
        .sb-archive-list{display:grid;gap:13px}
        .sb-archive-item{background:var(--theme-surface-card,#fffaf7);border:1px solid var(--theme-border,#d8c6b3);border-radius:17px;overflow:hidden;box-shadow:0 6px 21px rgba(64,32,22,.06)}
        .sb-archive-summary{cursor:pointer;display:grid;grid-template-columns:72px minmax(0,1fr) auto;align-items:center;gap:15px;padding:15px 19px;list-style:none}
        .sb-archive-summary::-webkit-details-marker{display:none}
        .sb-archive-summary:focus-visible{outline:2px solid var(--theme-accent-strong,#6b1d14);outline-offset:-3px}
        .sb-archive-thumb{width:72px;height:67px;border-radius:11px;background:var(--theme-surface-inset,#eee2d5);border:1px solid var(--theme-border-soft,#e5d5c4);overflow:hidden;object-fit:cover}
        .sb-archive-summary-main{min-width:0}
        .sb-archive-title{font:bold 19px/1.2 Georgia,serif;color:var(--theme-text-strong,#622018);overflow-wrap:anywhere}
        .sb-archive-small{margin-top:6px;display:flex;gap:8px 14px;flex-wrap:wrap;color:var(--theme-text-secondary,#876c52);font:11px/1.4 system-ui,sans-serif}
        .sb-archive-collapse-icon{width:35px;height:35px;border-radius:9px;border:1px solid var(--theme-border,#d7c2aa);display:grid;place-items:center;color:var(--theme-text-strong,#63231c);background:var(--theme-surface-inset,#f7ecdf);font:700 20px system-ui,sans-serif;transition:transform .2s}
        .sb-archive-item[open] .sb-archive-collapse-icon{transform:rotate(180deg)}
        .sb-archive-body{border-top:1px solid var(--theme-border-soft,#ead9c8);padding:19px;display:grid;gap:17px}
        .sb-archive-preview{width:100%;display:block;border-radius:13px;aspect-ratio:1280/420;max-height:360px;object-fit:contain;background:var(--theme-surface-inset,#f2e8df);border:1px solid var(--theme-border-soft,#e7d5be)}
        .sb-archive-chips{display:flex;flex-wrap:wrap;gap:8px}
        .sb-archive-chip{border:1px solid var(--theme-border-soft,#e5d3bd);border-radius:10px;background:var(--theme-surface-inset,#f7f0e7);padding:10px 13px;flex:1 1 125px;min-width:0}
        .sb-archive-chip small{display:block;margin-bottom:5px;font:800 9px/1.4 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.08em;color:var(--theme-label,#987454)}
        .sb-archive-chip strong{font:700 20px/1.2 Georgia,serif;color:var(--theme-text-strong,#622018)}
        .sb-archive-group-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
        .sb-archive-group{padding:13px;border:1px solid var(--theme-border-soft,#e4d4c1);border-radius:12px;background:var(--theme-surface-inset,#f9f1e9)}
        .sb-archive-group h3{font-size:14px;color:var(--theme-text-strong,#633029);font-weight:700;margin-bottom:10px}
        .sb-archive-group--ally{border-top:3px solid #38866e}.sb-archive-group--enemy{border-top:3px solid #b96555}.sb-archive-group--player{border-top:3px solid #aa825d}
        .sb-archive-group-stats{display:flex;justify-content:space-between;gap:10px;font:12px/1.5 system-ui,sans-serif}
        .sb-archive-group-stats strong{font-weight:800;color:var(--theme-text-strong,#622018)}
        .sb-archive-drawer{border:1px solid var(--theme-border-soft,#dccbb7);border-radius:11px;background:var(--theme-surface-card,#fffbf7);overflow:hidden}
        .sb-archive-drawer>summary{cursor:pointer;list-style:none;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 16px;color:var(--theme-text-strong,#672820);font:700 13px/1.3 system-ui,sans-serif;background:var(--theme-surface-inset,#f9f1e6)}
        .sb-archive-drawer>summary::-webkit-details-marker{display:none}
        .sb-archive-drawer>summary::after{content:'+';font-size:20px;line-height:1}
        .sb-archive-drawer[open]>summary::after{content:'−'}
        .sb-archive-drawer-content{padding:14px 16px;display:grid;gap:10px}
        .sb-archive-person{display:grid;grid-template-columns:46px minmax(0,1fr) auto;align-items:center;gap:12px;border-bottom:1px solid var(--theme-border-soft,#e9ded0);padding-bottom:9px}
        .sb-archive-person:last-child{border-bottom:none;padding-bottom:0}
        .sb-archive-face{height:50px;width:46px;border-radius:8px;object-fit:cover;object-position:center top;border:1px solid var(--theme-border,#d9c5b1);background:var(--theme-surface-inset,#edddcb);display:grid;place-items:center;color:var(--theme-text-strong,#683124);font-size:19px}
        .sb-archive-person-name{font:700 13px/1.3 Georgia,serif;color:var(--theme-text-strong,#64231c)}
        .sb-archive-person-meta{font:10px/1.4 system-ui,sans-serif;color:var(--theme-text-secondary,#98765d);margin-top:4px}
        .sb-archive-person-values{text-align:right;font:11px/1.6 system-ui,sans-serif;white-space:nowrap}
        .sb-archive-person-values b{font-size:14px;color:var(--theme-text-strong,#63241b)}
        .sb-archive-damage{color:#a34739}.sb-archive-heal{color:#2c8168}
        .sb-archive-event{border-bottom:1px solid var(--theme-border-soft,#e9ded1);padding:9px 0;display:flex;gap:10px;align-items:center;font:12px/1.5 system-ui,sans-serif}
        .sb-archive-event:last-child{border:none}
        .sb-archive-event small{margin-left:auto;color:var(--theme-text-secondary,#92745f);white-space:nowrap}
        .sb-archive-pill{display:inline-flex;align-items:center;border-radius:6px;background:var(--theme-surface-inset,#eee5d8);border:1px solid var(--theme-border-soft,#e4d5c2);padding:4px 7px;font:800 9px system-ui,sans-serif;color:var(--theme-text-strong,#633227)}
        .sb-archive-note{font:11px/1.5 system-ui,sans-serif;color:var(--theme-text-secondary,#866854)}
        .sb-archive-foot{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px;padding-top:12px;border-top:1px solid var(--theme-border-soft,#e4d5c0)}
        @media(max-width:800px){.sb-archive-group-grid{grid-template-columns:1fr}.sb-archive-page{padding:18px 12px 45px}.sb-archive-body{padding:12px}}
        @media(max-width:480px){.sb-archive-summary{padding:10px;gap:9px;grid-template-columns:54px minmax(0,1fr) 28px}.sb-archive-thumb{width:54px;height:54px}.sb-archive-title{font-size:15px}.sb-archive-collapse-icon{width:28px;height:28px}.sb-archive-person{grid-template-columns:36px minmax(0,1fr);gap:8px}.sb-archive-face{width:36px;height:42px}.sb-archive-person-values{grid-column:2;text-align:left;display:flex;gap:12px}.sb-archive-chip{flex-basis:110px}}

        /* V66.0.51 — arquivo de batalhas: gavetas e cartões com hierarquia visual */
        .sb-archive-page {background:var(--theme-page-background,#eee8dc);padding-block:40px 100px;}
        .sb-archive-wrap{max-width:1180px}
        .sb-archive-nav {padding:0 2px 23px;margin-bottom:25px;border-color:var(--theme-border,#d9c8b5)}
        .sb-archive-nav h1 {font-size:30px;letter-spacing:-.015em}
        .sb-archive-nav .sb-archive-muted {margin-top:5px}
        .sb-archive-back {transition:background .18s,border-color .18s,transform .18s;box-shadow:0 2px 8px rgba(64,28,18,.035)}
        .sb-archive-back:hover {background:var(--theme-card-hover,#f6ecdf);border-color:var(--theme-border-strong,#bb9e83);transform:translateY(-1px)}
        .sb-archive-list{gap:16px}
        .sb-archive-item {border-radius:20px;border-color:var(--theme-border,#d2baa4);box-shadow:0 10px 28px rgba(69,39,24,.075);transition:border-color .18s,box-shadow .18s}
        .sb-archive-item:hover {border-color:var(--theme-border-strong,#ba9b7d);box-shadow:0 14px 35px rgba(69,39,24,.10)}
        .sb-archive-item[open]{border-color:var(--theme-border-strong,#bda085)}
        .sb-archive-summary {position:relative;grid-template-columns:95px minmax(0,1fr) auto 39px;gap:17px;min-height:110px;padding:15px 19px;background:linear-gradient(110deg,var(--theme-surface-card,#fffaf5),var(--theme-surface-inset,#f7f0e8));transition:background .18s}
        .sb-archive-summary:hover{background:var(--theme-card-hover,#f8eee4)}
        .sb-archive-thumb {width:95px;height:77px;object-fit:cover;object-position:center;border-radius:11px;box-shadow:0 3px 8px rgba(53,30,18,.12)}
        .sb-archive-title {font-size:22px;letter-spacing:-.012em}
        .sb-archive-small {gap:6px 15px;font-size:11px;margin-top:9px}
        .sb-archive-small span:not(:first-child):before {content:'·';opacity:.53;margin-right:8px}
        .sb-v51-open-label {font:800 10px/1.3 system-ui,sans-serif;letter-spacing:.04em;color:var(--theme-label,#84634e);white-space:nowrap}
        .sb-archive-collapse-icon {width:37px;height:37px;border-radius:11px;transition:transform .2s,background .2s;box-shadow:inset 0 1px rgba(255,255,255,.4)}
        .sb-archive-item[open] .sb-archive-collapse-icon {background:var(--theme-button-background,#6b1d14);color:var(--theme-button-text,#fff9f2)}
        .sb-archive-body {padding:20px 21px 23px;gap:17px;background:var(--theme-surface-card,#fffaf6)}
        .sb-archive-preview {border-radius:14px;padding:6px;background:var(--theme-surface-inset,#eee6dc);box-shadow:0 5px 18px rgba(58,35,26,.055);max-height:340px;}
        .sb-archive-chips {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px}
        .sb-archive-chip {border-radius:12px;min-width:0;padding:15px 17px;background:linear-gradient(130deg,var(--theme-surface-inset,#f5ede2),var(--theme-surface-card,#fffbf7));box-shadow:inset 0 1px 0 rgba(255,255,255,.27)}
        .sb-archive-chip small{font-size:9px;letter-spacing:.10em}
        .sb-archive-chip strong{font-size:23px}
        .sb-v51-clock {border-color:var(--theme-border-strong,#c5a586);background:linear-gradient(120deg,var(--theme-surface-inset,#f7e9d7),var(--theme-surface-card,#fff9f1))}
        .sb-v51-clock strong{font-size:21px}
        .sb-archive-group {border-radius:13px;padding:16px 17px;box-shadow:0 2px 8px rgba(59,33,22,.025)}
        .sb-archive-group h3{font-size:16px;margin-bottom:12px}
        .sb-archive-group-stats{font-size:12px;padding:4px 0}
        .sb-archive-drawer {border-radius:13px;border-color:var(--theme-border,#decbba);box-shadow:0 2px 8px rgba(53,31,21,.027)}
        .sb-archive-drawer>summary {min-height:65px;display:flex;justify-content:flex-start;gap:11px;padding:12px 16px;background:linear-gradient(110deg,var(--theme-surface-inset,#f7efe6),var(--theme-surface-card,#fffaf5));transition:background .18s;}
        .sb-archive-drawer>summary:hover {background:var(--theme-card-hover,#f3e6d8)}
        .sb-archive-drawer[open]>summary {border-bottom:1px solid var(--theme-border-soft,#ead7c1);background:var(--theme-surface-inset,#f4e9dc)}
        .sb-archive-drawer>summary::after {content:'';width:10px;height:10px;border-right:2px solid currentColor;border-bottom:2px solid currentColor;transform:rotate(45deg);margin:0 6px 5px auto;transition:transform .18s}
        .sb-archive-drawer[open]>summary::after {content:'';transform:rotate(225deg);margin-top:8px;margin-bottom:0}
        .sb-v51-drawer-label {display:flex;align-items:center;gap:11px;font:800 13px/1.2 system-ui,sans-serif;flex-wrap:wrap;color:var(--theme-text-strong,#69281e)}
        .sb-v51-drawer-label>svg {flex-shrink:0;color:var(--theme-accent-strong,#713226)}
        .sb-v51-drawer-label small {font:500 11px/1.2 system-ui,sans-serif;color:var(--theme-text-secondary,#96765d);}
        .sb-archive-pill {border-radius:7px;padding:5px 8px;margin-left:auto;font-size:10px}
        .sb-archive-drawer-content {padding:16px 20px;gap:12px}
        .sb-archive-person {padding:9px 2px 12px;gap:13px;grid-template-columns:52px minmax(0,1fr) auto}
        .sb-archive-face {width:50px;height:59px;border-radius:10px;object-position:top center}
        .sb-archive-person-name {font-size:15px}
        .sb-archive-person-values {display:flex;gap:13px;align-items:center}
        .sb-archive-person-values span{display:flex;flex-direction:column;gap:2px;text-align:center;min-width:54px}
        .sb-archive-person-values b {font-size:18px}
        .sb-archive-event {padding:12px 2px}
        .sb-archive-foot {gap:16px;margin-top:2px;padding:15px 2px 0}
        .sb-v51-archive-delete {color:#a04437;border-color:rgba(160,68,55,.32);background:var(--theme-surface-card,#fff8f5)}
        .sb-v51-archive-delete:hover {background:rgba(182,58,43,.08);border-color:#aa5545}
        .sb-archive-action:focus-visible,.sb-archive-summary:focus-visible,.sb-archive-drawer>summary:focus-visible{outline:2px solid var(--theme-accent-strong,#6b1d14);outline-offset:2px}
        @media(max-width:850px){.sb-archive-chips{grid-template-columns:repeat(2,minmax(0,1fr))}.sb-archive-summary{grid-template-columns:70px minmax(0,1fr) 33px;gap:10px;min-height:90px;padding:12px}.sb-archive-thumb{width:70px;height:65px}.sb-v51-open-label{display:none}.sb-archive-group-grid{grid-template-columns:1fr}.sb-archive-body{padding:12px}}
        @media(max-width:510px){.sb-archive-page{padding:17px 11px 50px}.sb-archive-nav h1{font-size:24px}.sb-archive-title{font-size:17px}.sb-archive-summary{grid-template-columns:55px minmax(0,1fr) 30px;gap:8px}.sb-archive-thumb{width:55px;height:55px}.sb-archive-chips{gap:7px}.sb-archive-chip{padding:11px}.sb-archive-chip strong{font-size:18px}.sb-v51-clock strong{font-size:17px}.sb-archive-drawer-content{padding:10px}.sb-archive-person{grid-template-columns:40px minmax(0,1fr)}.sb-archive-person-values{grid-column:2;justify-content:flex-start}.sb-archive-face{width:40px;height:47px}.sb-v51-drawer-label{gap:7px;font-size:12px}.sb-v51-drawer-label small{display:none}.sb-archive-pill{padding:4px 5px;font-size:9px}}
        @media(prefers-reduced-motion:reduce){.sb-archive-item,.sb-archive-summary,.sb-archive-drawer>summary,.sb-archive-collapse-icon{transition:none}}

        .sb-v51-archive-toolbar {display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:17px}
        .sb-v51-search-form {display:flex;align-items:center;gap:10px;flex:1 1 300px;max-width:570px;padding:5px 6px 5px 13px;border:1px solid var(--theme-border,#d9c8b5);border-radius:12px;background:var(--theme-surface-card,#fffaf5);color:var(--theme-label,#967355);box-shadow:0 2px 8px rgba(45,27,18,.035)}
        .sb-v51-search-form:focus-within {outline:2px solid var(--theme-accent-strong,#6b1d14);outline-offset:1px}
        .sb-v51-search-form input {flex:1;min-width:0;background:transparent;color:var(--theme-text-strong,#5a2119);font:500 13px system-ui,sans-serif;border:0;padding:9px 2px;outline:none;box-shadow:none}
        .sb-v51-search-form button {background:var(--theme-button-background,#6b1d14);color:var(--theme-button-text,#fffbf7);font:800 11px system-ui,sans-serif;border-radius:8px;padding:10px 17px;cursor:pointer}
        .sb-v51-archive-toolbar>.sb-archive-muted {margin-left:auto}
        @media(max-width:520px){.sb-v51-search-form{flex-basis:100%;max-width:none}.sb-v51-search-form button{padding:10px 11px}}

        /* V66.0.52 — cabeçalho e relatórios com hierarquia visual mais clara. */
        .sb-archive-page {padding-top:34px;padding-bottom:85px;}
        .sb-archive-wrap {max-width:1180px;}
        .sb-v52-archive-nav {align-items:center;margin-bottom:22px;padding:0 0 21px;}
        .sb-v52-archive-identity {display:flex;align-items:center;gap:14px;min-width:0;}
        .sb-v52-archive-mark {width:47px;height:47px;flex:none;display:grid;place-items:center;border-radius:13px;background:var(--theme-surface-inset,#ece0d1);border:1px solid var(--theme-border,#dac7ae);color:var(--theme-accent-strong,#69251d);}
        .sb-v52-archive-nav h1 {font:bold clamp(23px,2.6vw,31px)/1.12 Georgia,'Times New Roman',serif;color:var(--theme-text-strong,#65251c);letter-spacing:-.015em;}
        .sb-v52-archive-nav .sb-archive-muted {margin-top:6px;font-size:12px;}
        .sb-v52-archive-return {min-height:40px;gap:8px;white-space:nowrap;}
        .sb-v52-archive-return:hover {border-color:var(--theme-border-strong,#b79d7d);}
        .sb-v52-archive-count {display:inline-flex;align-items:center;justify-content:center;margin-left:auto;padding:8px 12px;border:1px solid var(--theme-border,#d9c8b5);border-radius:999px;background:var(--theme-surface-inset,#f4ecdf);font:800 11px system-ui,sans-serif;white-space:nowrap;color:var(--theme-text-strong,#66362c);}
        .sb-v51-archive-toolbar {padding:0 2px;margin-bottom:21px;gap:12px;}
        .sb-v51-search-form {max-width:560px;min-height:46px;box-shadow:0 4px 11px rgba(53,32,18,.04);}
        .sb-archive-list {gap:14px;}
        .sb-archive-item {border-radius:17px;box-shadow:0 8px 23px rgba(53,31,20,.065);}
        .sb-archive-item:hover {box-shadow:0 12px 27px rgba(53,31,20,.085);}
        .sb-archive-summary {grid-template-columns:106px minmax(0,1fr) auto auto 35px;gap:16px;padding:14px 17px;min-height:104px;}
        .sb-archive-thumb {width:106px;height:77px;border-radius:10px;box-shadow:0 4px 10px rgba(54,33,20,.10);object-fit:cover;}
        .sb-archive-title {font:bold clamp(17px,1.7vw,22px)/1.18 Georgia,serif;letter-spacing:-.012em;}
        .sb-archive-small {margin-top:7px;font-size:10px;gap:4px 12px;}
        .sb-v52-archive-duration {display:flex;flex-direction:column;gap:5px;min-width:125px;text-align:right;}
        .sb-v52-archive-duration small {font:800 9px/1 system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase;color:var(--theme-label,#937358);}
        .sb-v52-archive-duration strong {font:bold 14px/1.2 Georgia,serif;color:var(--theme-text-strong,#642820);}
        .sb-v51-open-label {font-size:11px;}
        .sb-archive-collapse-icon {width:34px;height:34px;border-radius:9px;}
        .sb-archive-item[open] .sb-archive-summary {border-bottom-color:var(--theme-border-soft,#e4d5c4);}
        .sb-archive-body {gap:14px;padding:18px 20px 21px;}
        .sb-v52-report-hero {display:grid;grid-template-columns:minmax(0,1.5fr) minmax(250px, .85fr);align-items:stretch;gap:14px;min-width:0;}
        .sb-v52-report-art {display:flex;align-items:center;min-width:0;padding:8px;border-radius:13px;border:1px solid var(--theme-border-soft,#e4d4c3);background:var(--theme-surface-inset,#f1e9df);}
        .sb-v52-report-art .sb-archive-preview {width:100%;max-height:none;aspect-ratio:1280/420;object-fit:contain;padding:0;border:none;border-radius:8px;box-shadow:none;background:transparent;}
        .sb-v52-report-hero .sb-archive-chips {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px;align-content:stretch;}
        .sb-v52-report-hero .sb-archive-chip {display:flex;flex-direction:column;justify-content:center;min-width:0;padding:13px 14px;border-radius:11px;}
        .sb-v52-report-hero .sb-archive-chip strong {font-size:clamp(16px,1.6vw,22px);overflow-wrap:anywhere;}
        .sb-archive-note {padding:5px 1px;color:var(--theme-text-secondary,#917359);font-size:11px;}
        .sb-archive-group-grid {gap:12px;}
        .sb-archive-group {padding:15px 16px;border-radius:12px;}
        .sb-archive-drawer {border-radius:12px;}
        .sb-archive-drawer>summary {min-height:61px;padding:11px 15px;}
        .sb-archive-drawer-content {padding:15px 19px;}
        @media(max-width:1000px) {.sb-v52-report-hero {grid-template-columns:1fr;}.sb-v52-report-hero .sb-archive-chips {grid-template-columns:repeat(4,minmax(0,1fr));}.sb-archive-summary {grid-template-columns:85px minmax(0,1fr) auto 32px;}.sb-archive-thumb {width:85px;height:68px;}.sb-v51-open-label {display:none;}}
        @media(max-width:670px) {.sb-archive-page {padding:20px 12px 60px}.sb-v52-archive-nav {gap:13px;}.sb-v52-archive-identity {gap:10px}.sb-v52-archive-mark {width:40px;height:40px}.sb-v52-archive-return {padding:9px 11px}.sb-archive-summary {grid-template-columns:60px minmax(0,1fr) 32px;gap:10px;padding:12px;}.sb-archive-thumb {width:60px;height:62px}.sb-v52-archive-duration {display:none}.sb-archive-body {padding:11px}.sb-v52-report-hero .sb-archive-chips {grid-template-columns:repeat(2,minmax(0,1fr))}.sb-v52-report-hero .sb-archive-chip {min-height:85px;padding:11px}.sb-archive-group-grid {grid-template-columns:1fr;}.sb-v52-archive-count {padding:7px 10px}.sb-v51-archive-toolbar {gap:10px}}
        @media(max-width:430px) {.sb-v52-archive-nav h1 {font-size:21px}.sb-v52-archive-nav .sb-archive-muted {font-size:10px}.sb-archive-back {font-size:10px}.sb-archive-summary {grid-template-columns:52px minmax(0,1fr) 29px;gap:8px}.sb-archive-thumb {width:52px;height:55px}.sb-archive-title {font-size:16px}.sb-archive-small {font-size:10px}.sb-archive-collapse-icon {width:29px;height:29px}}

    </style>
    <div class="sb-archive-page">
        <div class="sb-archive-wrap">
            <div class="sb-archive-nav sb-v52-archive-nav">
                <div class="sb-v52-archive-identity">
                    <span class="sb-v52-archive-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="17" rx="3"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                    </span>
                    <div>
                        <h1>Histórico de Batalhas</h1>
                        <p class="sb-archive-muted">Resultados e registros dos encontros encerrados</p>
                    </div>
                </div>
                <a class="sb-archive-back sb-v52-archive-return" href="{{ route('combats.index') }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    Voltar aos combates
                </a>
            </div>
            @if(session('success'))<p style="margin-bottom:14px;color:#18795b">{{ session('success') }}</p>@endif
            @if(session('error'))<p style="margin-bottom:14px;color:#a23a2d">{{ session('error') }}</p>@endif
            <div class="sb-v51-archive-toolbar">
                <form action="{{ route('master.combat.archives.index') }}" method="GET" role="search" class="sb-v51-search-form">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                    <input type="search" name="q" value="{{ $search ?? '' }}" maxlength="80" placeholder="Buscar batalha pelo nome…" aria-label="Buscar batalhas arquivadas">
                    <button type="submit">Buscar</button>
                </form>
                @if(!empty($search))<a class="sb-archive-action" href="{{ route('master.combat.archives.index') }}">Limpar busca</a>@endif
                <span class="sb-v52-archive-count">{{ $archives->total() }} {{ $archives->total() === 1 ? 'batalha' : 'batalhas' }}</span>
            </div>
            <div class="sb-archive-list">
                @forelse($archives as $archive)
                    @php
                        $run = $archive->run;
                        $stats = $archive->statistics ?? [];
                        $metrics = $stats['metrics'] ?? [];
                        $groups = [
                            'player'=>['title'=>'Jogadores','class'=>'player'],
                            'ally'=>['title'=>'NPCs aliados','class'=>'ally'],
                            'enemy'=>['title'=>'Inimigos','class'=>'enemy'],
                        ];
                        $people = $stats['people'] ?? [];
                        $snapshot = $archive->snapshot ?? [];
                        $portraits = collect(array_merge($snapshot['allies'] ?? [], $snapshot['enemies'] ?? []))->keyBy(fn ($e) => ($e['participant_type'] ?? $e['type'] ?? 'npc').':'.($e['participant_id'] ?? 0));
                        $seconds = $stats['duration_seconds'] ?? null;
                        $durationLabel = $seconds === null ? 'Não registrado' : ((floor($seconds / 3600) ? floor($seconds / 3600).'h ' : '').floor(($seconds % 3600) / 60).'min '.($seconds % 60).'s');
                        $events = $stats['events'] ?? [];
                        $hasMissingPortrait = collect(array_merge($snapshot['allies'] ?? [], $snapshot['enemies'] ?? []))
                            ->contains(fn ($entry) => empty($entry['portrait_data']));
                    @endphp
                    <details class="sb-archive-item">
                        <summary class="sb-archive-summary">
                            <img class="sb-archive-thumb" src="{{ route('master.combat.archives.image', $archive) }}" alt="Prévia" loading="lazy">
                            <div class="sb-archive-summary-main">
                                <div class="sb-archive-title">{{ $snapshot['name'] ?? $run->combat->name }}</div>
                                <div class="sb-archive-small">
                                    <span>{{ $snapshot['campaign'] ?? 'Encontro' }}</span>
                                    <span>{{ $run->ended_at?->format('d/m/Y') ?? 'Data desconhecida' }}</span>
                                    <span>{{ $stats['rounds'] ?? '—' }} rodadas</span>
                                </div>
                            </div>
                            <span class="sb-v52-archive-duration"><small>Tempo em combate</small><strong>{{ $durationLabel }}</strong></span>
                            <span class="sb-v51-open-label">{{ __('Detalhes') }}</span>
                            <span class="sb-archive-collapse-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </span>
                        </summary>
                        <div class="sb-archive-body">
                            <div class="sb-v52-report-hero">
                                <div class="sb-v52-report-art">
                                    <img class="sb-archive-preview" src="{{ route('master.combat.archives.image', $archive) }}" alt="Registro congelado do resultado" loading="lazy">
                                </div>
                                <div class="sb-archive-chips">
                                <div class="sb-archive-chip"><small>Rodadas</small><strong>{{ $stats['rounds'] ?? '—' }}</strong></div>
                                <div class="sb-archive-chip"><small>Turnos registrados</small><strong>{{ $stats['turns_advanced'] ?? '—' }}</strong></div>
                                <div class="sb-archive-chip sb-v51-clock"><small>Tempo total de combate</small><strong>{{ $durationLabel }}</strong></div>
                                <div class="sb-archive-chip"><small>Entraram depois</small><strong>{{ $stats['late_count'] ?? '—' }}</strong></div>
                                </div>
                            </div>
                            <p class="sb-archive-note">{{ ($stats['duration_complete'] ?? false) ? 'Tempo ativo somado apenas nos períodos sem pausa.' : 'Tempo ativo parcial ou indisponível em combates anteriores à atualização.' }} {{ ($stats['complete'] ?? false) ? 'Dano e cura representam alterações de PV observadas, sem PV temporários.' : 'O registro de dano e cura deste encontro pode estar incompleto.' }}</p>
                            <div class="sb-archive-group-grid">
                                @foreach($groups as $groupKey => $meta)
                                    <div class="sb-archive-group sb-archive-group--{{ $meta['class'] }}">
                                        <h3>{{ $meta['title'] }}</h3>
                                        <div class="sb-archive-group-stats"><span>Dano recebido</span><strong>{{ $metrics[$groupKey]['damage'] ?? 0 }} PV</strong></div>
                                        <div class="sb-archive-group-stats"><span>PV recuperados</span><strong>{{ $metrics[$groupKey]['healing'] ?? 0 }} PV</strong></div>
                                    </div>
                                @endforeach
                            </div>
                            <details class="sb-archive-drawer">
                                <summary><span class="sb-v51-drawer-label"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20v-2a4 4 0 0 0-4-4H4a4 4 0 0 0-4 4v2M8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 20v-2a4 4 0 0 0-3-3.87M16 2.13a4 4 0 0 1 0 7.75" transform="translate(2 1) scale(.85)"/></svg>Participantes <small>Dano e cura por personagem</small></span><span class="sb-archive-pill">{{ count($people) ?: 'Legado' }}</span></summary>
                                <div class="sb-archive-drawer-content">
                                    @foreach($groups as $groupKey => $meta)
                                        @php $members = collect($people)->where('group',$groupKey)->values(); @endphp
                                        @if($members->isNotEmpty())
                                            <strong style="color:var(--theme-text-strong,#63251c);font-size:14px">{{ $meta['title'] }}</strong>
                                            @foreach($members as $member)
                                                @php $photo = $portraits->get($member['key']); $src = $photo['portrait_data'] ?? (isset($photo['image']) && $photo['image'] ? asset('storage/'.ltrim((string)$photo['image'],'/')) : null); @endphp
                                                <div class="sb-archive-person">
                                                    @if($src)<img class="sb-archive-face" src="{{ $src }}" alt="" loading="lazy">@else<span class="sb-archive-face">{{ mb_strtoupper(mb_substr($member['name'],0,1)) }}</span>@endif
                                                    <div>
                                                        <div class="sb-archive-person-name">{{ $member['name'] }}</div>
                                                        <div class="sb-archive-person-meta">{{ empty($member['joined_known']) ? 'Entrada não registrada' : (!empty($member['joined_late']) ? 'Entrou na rodada '.($member['joined_round'] ?? '—') : 'Participante desde o início') }}{{ !empty($member['left_at']) ? ' · Saiu antes do término' : '' }}</div>
                                                    </div>
                                                    <div class="sb-archive-person-values">
                                                        <span>Dano <b class="sb-archive-damage">{{ $member['damage'] }}</b></span>
                                                        <span>Cura <b class="sb-archive-heal">{{ $member['healing'] }}</b></span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    @if(!count($people))<p class="sb-archive-muted">Esta batalha foi arquivada antes do registro individual por participante. Os totais históricos foram preservados, sem atribuir dano por nome.</p>@endif
                                </div>
                            </details>
                            <details class="sb-archive-drawer">
                                <summary><span class="sb-v51-drawer-label"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8l-5-5H8Zm7 0v6h6M8 13h8M8 17h8"/></svg>Registro de PV <small>Danos e recuperações</small></span><span class="sb-archive-pill">{{ count($events) }} alterações</span></summary>
                                <div class="sb-archive-drawer-content" style="max-height:440px;overflow:auto">
                                    @forelse($events as $event)
                                        <div class="sb-archive-event">
                                            <span class="sb-archive-pill">{{ $event['kind'] === 'damage' ? 'Dano' : 'Cura' }}</span>
                                            <span><strong>{{ $event['name'] }}</strong> · {{ $event['group'] === 'player' ? 'Jogador' : ($event['group'] === 'ally' ? 'NPC aliado' : 'Inimigo') }} · <b class="{{ $event['kind'] === 'damage' ? 'sb-archive-damage' : 'sb-archive-heal' }}">{{ $event['amount'] }} PV</b></span>
                                            <small>Rodada {{ $event['round'] ?? '—' }}</small>
                                        </div>
                                    @empty
                                        <p class="sb-archive-muted">Nenhuma alteração de PV registrada.</p>
                                    @endforelse
                                </div>
                            </details>
                            <div class="sb-archive-foot">
                                <div class="sb-archive-muted">Início: {{ $run->started_at?->format('d/m/Y H:i') ?? 'Não registrado' }} · Término: {{ $run->ended_at?->format('d/m/Y H:i') ?? 'Não registrado' }}</div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                                    @if($hasMissingPortrait)
                                        <form method="POST" action="{{ route('master.combat.archives.restore-portraits', $archive) }}">
                                            @csrf
                                            <button type="submit" class="sb-archive-action" title="Apenas para arquivos antigos; recupera fotos que ainda existam no armazenamento">Recuperar fotos antigas</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('master.combat.archives.destroy', $archive) }}" onsubmit="return confirm('Excluir permanentemente este registro do Histórico de Batalhas? O combate original e seus dados não serão apagados.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-archive-action sb-v51-archive-delete" title="Excluir somente este relatório arquivado">
                                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M9 6V4h6v2m4 0-1 14H6L5 6m5 4v7m4-7v7"/></svg>
                                            Excluir registro
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="sb-archive-item" style="padding:40px;text-align:center">
                        <strong class="sb-archive-title">{{ !empty($search) ? 'Nenhuma batalha corresponde à busca' : 'Nenhuma batalha arquivada' }}</strong>
                        <p class="sb-archive-muted">Salve o resultado de um encontro encerrado para iniciar o histórico.</p>
                    </div>
                @endforelse
            </div>
            <div style="margin-top:22px">{{ $archives->links() }}</div>
        </div>
    </div>
</x-app-layout>
