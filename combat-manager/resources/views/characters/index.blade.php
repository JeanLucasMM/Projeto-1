<x-app-layout>

    @php
        /*
        |--------------------------------------------------------------------------
        | CAMPANHAS DOS PERSONAGENS
        |--------------------------------------------------------------------------
        |
        | O Index precisa mostrar a campanha sem exigir mudança no controller.
        | Fazemos uma consulta única para todos os Characters da tela e agrupamos
        | por character_id, evitando uma consulta por card.
        |
        */

        $characterIds = $characters
            ->pluck('id')
            ->filter()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RELAÇÕES USADAS PELO RESUMO
        |--------------------------------------------------------------------------
        |
        | A CA real do Spellbound não é apenas character_combat.armor_class.
        | Ela depende dos atributos efetivos, configuração de defesa base e
        | equipamentos do inventário. Carregamos tudo em lote para evitar N+1.
        |
        */

        if (method_exists($characters, 'loadMissing')) {
            $characters->loadMissing([
                'abilities',
                'items',
                'classes',
                'combat',
            ]);
        }

        $campaignsByCharacter = collect();

        if ($characterIds->isNotEmpty()) {
            $campaignsByCharacter =
                \Illuminate\Support\Facades\DB::table('campaign_characters')
                    ->join(
                        'campaigns',
                        'campaigns.id',
                        '=',
                        'campaign_characters.campaign_id'
                    )
                    ->whereIn(
                        'campaign_characters.character_id',
                        $characterIds
                    )
                    ->whereNull('campaigns.deleted_at')
                    ->select([
                        'campaign_characters.character_id',
                        'campaign_characters.is_active',
                        'campaign_characters.experience_points',
                        'campaigns.experience_enabled',
                        'campaigns.id',
                        'campaigns.name',
                    ])
                    ->orderByDesc('campaign_characters.is_active')
                    ->orderBy('campaigns.name')
                    ->get()
                    ->groupBy('character_id');
        }

        $spellSummaryByCharacter = collect();
        $spellSummaryAvailable = false;
        $spellPreparedAvailable = false;

        if (
            $characterIds->isNotEmpty()
            && \Illuminate\Support\Facades\Schema::hasTable('character_spells')
            && \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'character_id')
        ) {
            $spellSummaryAvailable = true;
            $spellPreparedAvailable =
                \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'is_prepared');

            $spellSelects = [
                'character_id',
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'),
            ];

            if ($spellPreparedAvailable) {
                $spellSelects[] = \Illuminate\Support\Facades\DB::raw(
                    'SUM(CASE WHEN is_prepared = 1 THEN 1 ELSE 0 END) as prepared'
                );
            }

            $spellSummaryByCharacter =
                \Illuminate\Support\Facades\DB::table('character_spells')
                    ->whereIn('character_id', $characterIds)
                    ->select($spellSelects)
                    ->groupBy('character_id')
                    ->get()
                    ->keyBy('character_id');
        }
    @endphp

    <style>
        /* SPELLBOUND V66.2.226 — Dossiê revisado: duas abas externas, sem sobreposição.
           Resumos compactos de inventário e grimório, retrato e rodapé harmonizados. */
        .characters-index-v2 {
            --index-paper:#fffaf3;
            --index-paper-alt:#f7efe4;
            --index-line:rgba(157,113,73,.26);
            --index-wine:#651f18;
            --index-gold:#a07854;
            --index-text:#3d2a22;
            --index-muted:#80634f;
        }
        .characters-index-v2 [x-cloak]{display:none!important}
        .character-roster-card{
            position:relative;display:grid;grid-template-columns:259px minmax(0,1fr);
            min-width:0;width:100%;max-width:960px;min-height:395px;overflow:visible;
            border:1px solid rgba(154,109,72,.34);border-radius:19px;
            background:linear-gradient(135deg,#fffdf8 0%,#fbf7f0 57%,#f4e9dc 100%);
            box-shadow:inset 0 1px 0 rgba(255,255,255,.96), 0 16px 32px rgba(68,40,23,.10), 0 3px 9px rgba(68,40,23,.04);
            transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease;
        }
        .character-roster-card:hover{transform:translateY(-3px);border-color:rgba(136,88,55,.47);
            box-shadow:inset 0 1px rgba(255,255,255,.92),0 13px 28px rgba(71,41,23,.108)}
        .character-roster-card.is-dead{border-color:rgba(92,69,64,.40)}
        /* Aos 0 PV, o cartão perde saturação sem desaparecer e continua acessível. */
        .character-roster-card.is-zero:not(.is-dead){
            border-color:rgba(119,110,104,.42);
            background:linear-gradient(135deg,#f6f2ec 0%,#f0ece6 100%);
            box-shadow:inset 0 1px 0 rgba(255,255,255,.85),0 9px 24px rgba(43,39,38,.08)}
        .character-roster-card.is-zero:not(.is-dead) .character-roster-body{
            filter:saturate(.64)}
        .character-roster-card.is-zero:not(.is-dead) .character-roster-image{
            filter:grayscale(.82) saturate(.28) brightness(.79)}
        .character-roster-card.is-zero:not(.is-dead) .character-roster-portrait{
            background:radial-gradient(ellipse at 50% 25%,#d8d1ca,#a9a09b 84%)}
        .character-roster-card.is-zero:not(.is-dead) .character-roster-portrait::after{
            background:linear-gradient(180deg,rgba(83,75,70,.1),rgba(42,38,36,.08) 43%,rgba(27,25,24,.52))}
        .character-roster-card:focus-within{outline:none}
        .character-roster-card:has(.character-roster-open:focus-visible){outline:2px solid rgba(135,91,62,.55);outline-offset:3px}
        .character-roster-card:has(.character-roster-delete:hover){transform:none}
        .character-roster-portrait{
            position:relative;min-width:0;min-height:100%;overflow:hidden;border-radius:18px 0 0 18px;
            border-right:1px solid rgba(131,91,67,.30);
            background:radial-gradient(ellipse at 51% 23%,#f5e9da,#dfc5ad 74%,#b99b85);
        }
        .character-roster-portrait-frame{position:absolute;z-index:4;pointer-events:none;inset:12px;
            border:1px solid rgba(250,243,233,.82);border-radius:12px 0 0 12px;
            box-shadow:0 0 0 1px rgba(147,102,71,.11), inset 0 0 0 1px rgba(255,255,255,.22), inset 0 -22px 30px rgba(52,28,18,.04)}
        .character-roster-portrait-frame::before,.character-roster-portrait-frame::after{content:none}
        .character-roster-portrait::after{content:'';position:absolute;z-index:2;pointer-events:none;inset:45% 0 0;
            background:linear-gradient(180deg,transparent,rgba(37,22,18,.02) 48%,rgba(29,16,12,.35))}
        .character-roster-image{position:absolute;inset:0;display:block;width:100%;height:100%;object-fit:cover;
            object-position:center top;transition:transform .38s ease}
        .character-roster-card:hover .character-roster-image{transform:scale(1.035)}
        .character-roster-card.is-dead .character-roster-image{filter:grayscale(.88) saturate(.2) brightness(.72)}
        /* Vigneta de ferimento: sombras abertas nas bordas, sem círculos ou manchas definidas. */
        .character-roster-portrait::before{
            content:'';position:absolute;z-index:3;pointer-events:none;inset:0;opacity:0;
            background:
                radial-gradient(ellipse 74% 62% at -13% 46%,rgba(161,32,34,.42),rgba(139,27,29,.14) 58%,transparent 100%),
                radial-gradient(ellipse 70% 76% at 116% 71%,rgba(157,28,31,.39),rgba(135,24,27,.12) 57%,transparent 100%),
                radial-gradient(ellipse 100% 37% at 48% 114%,rgba(112,19,24,.30),transparent 92%),
                linear-gradient(90deg,rgba(167,32,31,.06),transparent 36%,transparent 70%,rgba(165,33,35,.06));
            filter:blur(5px);transition:opacity .35s ease}
        .character-roster-card.is-wounded .character-roster-portrait::before{
            opacity:.55;animation:rosterWoundedVeil 3.3s ease-in-out infinite}
        .character-roster-card.is-critical .character-roster-portrait::before{
            opacity:.72;animation:rosterCriticalVeil 2.2s ease-in-out infinite}
        .character-roster-card.is-down:not(.is-dead) .character-roster-portrait::before{
            opacity:.24;animation:rosterDownVeil 4.2s ease-in-out infinite;
            filter:blur(9px) grayscale(.25)}
        /* O centro do retrato permanece limpo; a borda pulsa vermelho conforme o ferimento. */
        .character-roster-card.is-wounded .character-roster-portrait{
            animation:rosterWoundedEdge 3.3s ease-in-out infinite}
        .character-roster-card.is-critical .character-roster-portrait{
            animation:rosterCriticalEdge 2.2s ease-in-out infinite}
        .character-roster-card.is-down:not(.is-dead) .character-roster-portrait{
            animation:rosterDownEdge 4.2s ease-in-out infinite}
        .character-roster-level{position:absolute;right:14px;top:14px;z-index:5;display:flex;
            width:48px;height:54px;flex-direction:column;align-items:center;justify-content:center;
            border:1px solid rgba(161,114,72,.32);border-radius:12px;
            background:linear-gradient(180deg,#fffcf5,#f7ecdc);
            box-shadow:0 5px 12px rgba(55,32,19,.12),inset 0 0 0 3px rgba(238,224,207,.45);
            color:var(--index-wine)}
        .character-roster-level{pointer-events:none}
        .character-roster-level small{font:850 8px/1.1 system-ui,sans-serif;letter-spacing:.11em;text-transform:uppercase;color:#966f4e}
        .character-roster-level strong{margin-top:3px;font:750 23px/1 Georgia,serif}
        .character-roster-photo-caption{position:absolute;z-index:5;left:12px;bottom:12px;right:12px}
        .character-roster-photo-caption{pointer-events:none}
        .character-roster-photo-caption-stack > span{display:inline-flex;align-items:center;gap:7px;max-width:100%;
            padding:6px 10px;border:1px solid rgba(255,255,255,.21);border-radius:9px;
            background:rgba(40,26,22,.78);color:#fff9f0;font:750 10px/1.1 system-ui,sans-serif;
            box-shadow:0 3px 10px rgba(19,9,6,.12);backdrop-filter:blur(4px)}
        .character-roster-photo-caption-stack{display:flex;flex-direction:column;align-items:flex-start;gap:6px}
        .character-roster-status-dot{display:block;flex:none;width:7px;height:7px;border-radius:50%;background:#c9a776}
        .character-roster-status-dot.healthy{background:#72bd93}
        .character-roster-status-dot.wounded{background:#e4b363}
        .character-roster-status-dot.critical,.character-roster-status-dot.down{background:#e17a68}
        .character-roster-status-dot.dead{background:#e5c7c0}
        .character-roster-status-dot.draft{background:#b4a08b}
        .character-roster-body{position:relative;display:flex;flex-direction:column;min-width:0;
            padding:21px 25px 17px;border-radius:0 18px 18px 0;background:
            radial-gradient(ellipse at 100% 0%,rgba(194,150,103,.07),transparent 54%),
            linear-gradient(180deg,rgba(255,255,255,.15),transparent 18%, transparent 86%, rgba(208,181,148,.06));} 
        .character-roster-body::before{content:'';position:absolute;pointer-events:none;right:11px;top:11px;
            width:17px;height:17px;border-top:1px solid rgba(167,117,77,.24);border-right:1px solid rgba(167,117,77,.24);border-radius:0 6px 0 0}
        /* Identidade: o nome abre a leitura; classe e espécie são detalhes subordinados. */
        .character-roster-heading{min-width:0;padding-bottom:13px;border-bottom:1px solid rgba(160,120,86,.16)}
        .character-roster-name{display:block;max-width:100%;margin:0;color:#562019;
            font:750 clamp(27px,1.9vw,33px)/1.12 Georgia,serif;letter-spacing:-.018em;
            overflow-wrap:anywhere}
        .character-roster-name a{color:inherit;text-decoration:none}
        .character-roster-name a:hover{text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:4px}
        .character-roster-summary{margin:7px 0 0;color:#6e4a39;font:700 12.5px/1.38 system-ui,sans-serif;
            display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden}
        /* Espécie, antecedente e alinhamento compartilham uma única hierarquia. */
        .character-roster-traits{display:flex;align-items:center;flex-wrap:wrap;column-gap:10px;row-gap:4px;
            max-width:100%;margin-top:9px;color:#9c7657;font:800 10px/1.4 system-ui,sans-serif;
            letter-spacing:.09em;text-transform:uppercase}
        .character-roster-trait{display:inline-flex;align-items:center;min-width:0;overflow-wrap:anywhere}
        .character-roster-trait:first-child::before{content:'';display:block;flex:none;width:12px;height:1px;
            margin-right:6px;background:#b99c80}
        .character-roster-trait:not(:first-child)::before{content:'·';display:inline-block;flex:none;
            margin-right:10px;color:#b99c80;font-weight:800}
        .character-roster-campaign{display:flex;min-width:0;align-items:center;gap:9px;margin-top:14px;
            border:1px solid rgba(166,122,83,.22);border-radius:12px;
            background:linear-gradient(100deg,rgba(246,237,225,.78),rgba(255,251,245,.55));padding:9px 11px;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.68), 0 3px 8px rgba(101,66,40,.035)}
        .character-roster-campaign-icon{flex:none;display:grid;place-items:center;width:30px;height:30px;
            border:1px solid rgba(168,123,88,.10);border-radius:8px;background:rgba(255,252,247,.75);color:#8e5237}
        .character-roster-campaign-copy{flex:1;min-width:0}
        .character-roster-campaign-copy small{display:block;color:#9b765a;
            font:800 9px/1.1 system-ui,sans-serif;letter-spacing:.09em;text-transform:uppercase}
        .character-roster-campaign-copy strong{display:block;margin-top:4px;overflow:hidden;
            text-overflow:ellipsis;white-space:nowrap;color:#622c23;font:750 14px/1.1 Georgia,serif}
        .character-roster-campaign-more{flex:none;color:#936b4d;font:800 9px system-ui,sans-serif}
        /* Uma única faixa de combate, com escudo verdadeiro e PV como destaque central. */
        .character-roster-combat{
            display:grid;grid-template-columns:96px minmax(180px,1.75fr) minmax(82px,.86fr) minmax(94px,.9fr);
            overflow:hidden;align-items:stretch;min-height:108px;
            margin-top:13px;border:1px solid rgba(166,124,82,.23);border-radius:13px;
            background:linear-gradient(180deg,rgba(255,253,248,.93),rgba(246,235,222,.81));
            box-shadow:inset 0 1px 0 rgba(255,255,255,.82),0 2px 6px rgba(122,79,47,.03)
        }
        .character-roster-stat{display:flex;flex-direction:column;align-items:center;justify-content:center;
            min-width:0;gap:6px;padding:11px 9px;text-align:center}
        .character-roster-stat+.character-roster-stat{border-left:1px solid rgba(166,124,82,.18)}
        .character-roster-stat > span{display:block;color:#9d7757;font:800 9px/1.15 system-ui,sans-serif;
            letter-spacing:.10em;text-transform:uppercase}
        .character-roster-stat > strong{display:block;white-space:nowrap;color:#67271c;
            font:750 25px/1.05 Georgia,serif;font-variant-numeric:tabular-nums}
        .character-roster-stat-note{margin-top:1px;color:#a3856f;font:700 9px/1.15 system-ui,sans-serif;letter-spacing:.02em}
        .character-roster-stat-ac{padding:8px 5px}
        .character-roster-ac-shield{position:relative;display:block;flex:none;width:65px;height:80px;
            color:#b18a69;filter:drop-shadow(0 2px 2px rgba(87,47,27,.10))}
        .character-roster-ac-shield svg{position:absolute;inset:0;width:100%;height:100%}
        /* CA e número ancorados no centro útil, acima da ponta estreita do escudo. */
        .character-roster-ac-value{position:absolute;z-index:1;top:22%;left:8%;right:8%;
            display:flex;flex-direction:column;align-items:center;justify-content:flex-start;gap:3px;
            margin:0;pointer-events:none;text-align:center}
        .character-roster-ac-value small{color:#946c51;font:850 8px/1 system-ui,sans-serif;letter-spacing:.12em}
        .character-roster-ac-value strong{display:block;max-width:100%;color:#62251c;
            font:750 23px/1.05 Georgia,serif;white-space:nowrap;text-shadow:0 1px rgba(255,255,255,.8)}
        .character-roster-ac-shield.is-triple .character-roster-ac-value strong{font-size:18px}
        .character-roster-stat-pv{gap:5px;padding:10px 9px 9px;
            background:linear-gradient(180deg,rgba(255,254,250,.20),rgba(247,235,221,.23))}
        .character-roster-pv-main{display:flex;align-items:baseline;justify-content:center;gap:7px;
            white-space:nowrap;font:800 27px/1.1 Georgia,serif;font-variant-numeric:tabular-nums}
        .character-roster-pv-main .pv-current{color:#65291e}
        .character-roster-pv-main .pv-slash{color:#b79a85;font-size:19px;font-weight:500}
        .character-roster-pv-main .pv-max{color:#65291e}
        .character-roster-pv-main .pv-max.has-bonus{color:#ac7924;text-shadow:0 0 9px rgba(215,172,65,.24)}
        .character-roster-pv-main .pv-max.has-reduction{color:#7c4ca6;text-shadow:0 0 9px rgba(128,82,188,.18)}
        .character-roster-pv-modifiers{display:flex;align-items:center;justify-content:center;gap:6px 12px;
            flex-wrap:wrap;margin-top:4px;max-width:100%}
        /* Modificadores sem caixinhas: cor, tipografia e um pouco de brilho. */
        .character-roster-pv-mod{display:inline-flex;align-items:center;justify-content:center;gap:3px;min-width:0;
            font:800 11px/1.2 system-ui,sans-serif;white-space:nowrap;letter-spacing:.01em}
        .character-roster-pv-mod strong{font:850 13px/1 system-ui,sans-serif;font-variant-numeric:tabular-nums}
        .character-roster-pv-mod small{font:750 9px/1 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.04em}
        .character-roster-pv-mod svg{width:15px;height:16px;flex:none;stroke-width:1.8}
        .character-roster-pv-mod.temp{color:#2f9cdb;text-shadow:0 0 7px rgba(67,176,234,.62)}
        .character-roster-pv-mod.temp svg{filter:drop-shadow(0 0 4px rgba(67,180,239,.76))}
        .character-roster-pv-mod.bonus{color:#b48532;text-shadow:0 0 7px rgba(224,174,65,.40)}
        .character-roster-pv-mod.reduced{color:#8451b4;text-shadow:0 0 7px rgba(154,97,196,.35)}
        @keyframes rosterShieldGlow{0%,100%{filter:drop-shadow(0 0 3px rgba(73,184,247,.58))}50%{filter:drop-shadow(0 0 6px rgba(73,184,247,.94))}}
        .character-roster-pv-mod.temp svg{animation:rosterShieldGlow 3.2s ease-in-out infinite}
        /* Duas folhas pequenas PRESAS À BORDA, não botões pousados sobre o conteúdo. */
        .character-roster-panel-shell{position:static;display:flex;flex-direction:column;min-width:0;margin-top:0}
        .character-roster-tabrail{position:absolute;top:108px;right:-29px;z-index:12;
            display:flex;flex-direction:column;gap:10px}
        .character-roster-tab{position:relative;display:flex;align-items:center;justify-content:center;
            width:35px;height:95px;padding:9px 8px 9px 9px;cursor:pointer;
            border:1px solid rgba(151,103,69,.31);border-left:0;border-radius:0 11px 11px 0;
            background:linear-gradient(90deg,#e9d3b6 0%,#f7e8d4 14%,#fff5e6 80%,#f1dfc8 100%);
            color:#7d4b33;font:800 9px/1.1 system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase;
            writing-mode:vertical-rl;text-orientation:mixed;
            box-shadow:4px 6px 12px rgba(78,47,29,.11),inset 1px 0 0 rgba(110,72,46,.10);
            transition:color .18s ease,transform .18s ease,background .18s ease}
        .character-roster-tab::before{content:'';position:absolute;top:8px;bottom:8px;left:3px;
            width:1px;background:rgba(169,121,83,.21)}
        .character-roster-tab:hover,.character-roster-tab:focus-visible{color:#5f251d;
            background:linear-gradient(90deg,#ead1af,#fff8ec 72%,#f5e1c6);transform:translateX(2px);outline-offset:3px}
        .character-roster-tab.is-active{color:#65251d;background:linear-gradient(90deg,#ecd5b9,#fffaf0 72%,#f1dac0);font-weight:900}
        .character-roster-panel{min-width:0}
        .character-roster-panel-heading{display:flex;align-items:center;justify-content:space-between;gap:10px;
            padding:2px 0 12px;margin-top:13px;border-bottom:1px solid rgba(160,120,86,.17)}
        .character-roster-panel-heading strong{color:#642b22;font:750 20px/1.2 Georgia,serif}
        .character-roster-panel-heading small{display:block;color:#a07a5a;margin-top:3px;
            font:800 9px/1.25 system-ui,sans-serif;letter-spacing:.1em;text-transform:uppercase}
        .character-roster-back{position:relative;z-index:15;border:0;background:transparent;
            color:#9a704f;font:750 11px/1.2 Georgia,serif;padding:7px;cursor:pointer}
        .character-roster-back:hover,.character-roster-back:focus-visible{color:#64271d;text-decoration:underline;text-underline-offset:4px}
        .character-roster-panel-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));
            margin-top:15px;border:1px solid rgba(166,124,82,.21);border-radius:13px;
            overflow:hidden;background:linear-gradient(180deg,rgba(255,253,248,.9),rgba(245,235,222,.83))}
        .character-roster-panel-metric{display:flex;flex-direction:column;justify-content:center;align-items:center;
            min-height:90px;gap:5px;padding:12px 10px;text-align:center}
        .character-roster-panel-metric + .character-roster-panel-metric{border-left:1px solid rgba(166,124,82,.20)}
        .character-roster-panel-metric small{color:#967356;font:800 9px/1.2 system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase}
        .character-roster-panel-metric strong{color:#64271e;font:750 26px/1 Georgia,serif}
        .character-roster-panel-entries{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-top:12px;min-height:31px}
        .character-roster-panel-entry{display:inline-block;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
            padding:7px 10px;border:1px solid rgba(165,120,84,.18);border-radius:8px;
            background:rgba(255,251,245,.75);color:#79523f;font:700 11px/1.1 Georgia,serif}
        .character-roster-panel-empty{margin-top:13px;color:#8e715a;font:650 12px/1.5 system-ui,sans-serif}
        .character-roster-xp{margin-top:13px;padding-top:12px;border-top:1px solid rgba(159,113,73,.13)}
        .character-roster-xp-head{display:flex;justify-content:space-between;gap:8px;align-items:center;
            color:#8e6d54;font:750 9px/1.1 system-ui,sans-serif}
        .character-roster-xp-head strong{color:#70412f}
        .character-roster-xp-track{height:3px;margin-top:6px;overflow:hidden;border-radius:99px;background:#e5d7c7}
        .character-roster-xp-track span{display:block;height:100%;border-radius:inherit;background:#b28a57}
        /* Card inteiro acessível: a área do link expande sem interceptar excluir. */
        .character-roster-footer{display:flex;justify-content:flex-end;align-items:center;gap:8px;
            margin-top:auto;padding-top:11px;border-top:1px solid rgba(160,121,85,.15)}
        .character-roster-open{display:inline-flex;align-items:center;justify-content:center;gap:10px;
            height:36px;padding:0 3px 0 7px;border:0;background:transparent;
            color:#682b20;text-decoration:none;font:750 12px/1.1 Georgia,serif;
            white-space:nowrap;cursor:pointer;transition:color .2s ease}
        .character-roster-open::before{content:'';position:absolute;inset:0;z-index:2;pointer-events:auto}
        .character-roster-open .character-roster-entry-arrow{display:grid;place-items:center;
            width:32px;height:32px;flex:0 0 32px;border-radius:50%;
            border:1px solid rgba(158,115,80,.35);background:#f5e9da;color:#743e2c;
            box-shadow:inset 0 1px rgba(255,255,255,.75);
            transition:transform .23s ease,background .23s ease,border-color .23s ease,box-shadow .23s ease}
        .character-roster-entry-arrow svg{width:16px;height:16px}
        .character-roster-card:hover .character-roster-open,
        .character-roster-open:focus-visible{color:#4d1a13}
        .character-roster-card:hover .character-roster-entry-arrow,
        .character-roster-open:focus-visible .character-roster-entry-arrow{
            background:#e8d4bc;border-color:rgba(137,91,60,.55);transform:translateX(4px);
            box-shadow:0 3px 9px rgba(88,50,28,.12)}
        .character-roster-delete{position:relative;z-index:6;display:grid;place-items:center;
            flex:none;width:32px;height:32px;border:1px solid rgba(150,106,72,.20);
            border-radius:9px;color:#9a7561;background:rgba(255,251,245,.45);
            transition:background .18s,color .18s,border-color .18s}
        .character-roster-delete:hover,.character-roster-delete:focus-visible{
            border-color:rgba(141,50,40,.45);background:#8d3228;color:white}
        .character-roster-memorial{position:absolute;z-index:9;inset:0;display:flex;
            align-items:center;justify-content:center;flex-direction:column;text-align:center;padding:17px;
            background:radial-gradient(ellipse at 50% 38%,rgba(77,41,32,.55),rgba(26,18,16,.9) 76%);
            color:#fff9f1;backdrop-filter:blur(1.5px)}
        .character-roster-memorial{pointer-events:none}
        .character-roster-memorial-mark{display:grid;place-items:center;width:44px;height:44px;
            border:1px solid rgba(255,250,239,.45);border-radius:50%;background:rgba(95,34,26,.35)}
        .character-roster-memorial small{margin-top:12px;color:#e0c5af;font:850 9px system-ui,sans-serif;
            letter-spacing:.18em;text-transform:uppercase}
        .character-roster-memorial h3{margin:5px 0 0;font:800 20px/1.1 Georgia,serif}
        .character-roster-memorial p{max-width:190px;margin:9px 0 0;color:#eadbc8;
            font:600 10px/1.55 system-ui,sans-serif}
        .character-roster-memorial-rule{width:55px;height:1px;margin-top:12px;background:rgba(255,250,239,.42)}
        .character-index-create{display:inline-flex;align-items:center;justify-content:center;gap:8px;
            height:36px;border:1px solid #6b2119;border-radius:9px;padding:0 13px;background:#6b2119;
            color:#fff9f1;font:800 10px/1.1 system-ui,sans-serif;letter-spacing:.04em;
            transition:background .18s ease,transform .18s ease}
        .character-index-create:hover{background:#531711;transform:translateY(-1px)}
        /* A coleção dá espaço aos dossiês; uma ficha centraliza sem esticar indefinidamente. */
        .character-roster-grid{display:grid;grid-template-columns:minmax(0,1fr);justify-items:center;gap:52px}
        .character-roster-grid > .character-roster-card{width:100%}
        @media(min-width:2240px){
            .character-roster-grid{grid-template-columns:repeat(2,minmax(0,1fr));align-items:start}
            .character-roster-grid > .character-roster-card{max-width:none}
        }
        @media(max-width:1080px){
            .character-roster-card{grid-template-columns:235px minmax(0,1fr);min-height:380px}
            .character-roster-body{padding:19px 20px 15px}
            .character-roster-combat{grid-template-columns:98px minmax(160px,1.45fr) minmax(82px,.8fr) minmax(86px,.8fr)}
            .character-roster-stat{padding:10px 7px}
            .character-roster-stat > strong{font-size:22px}
            .character-roster-ac-shield{width:61px;height:74px}
            .character-roster-pv-main{font-size:24px}
            .character-roster-tabrail{right:-27px;top:101px}
            .character-roster-tab{width:33px;height:88px}
        }
        @media(max-width:820px){
            .character-roster-card{grid-template-columns:205px minmax(0,1fr);min-height:362px}
            .character-roster-body{padding:17px 17px 13px}
            .character-roster-name{font-size:26px}
            .character-roster-summary{font-size:11px}
            .character-roster-heading{padding-bottom:10px}
            .character-roster-traits{font-size:8.5px;gap:4px 7px}
            .character-roster-campaign{margin-top:11px;padding:7px 9px}
            .character-roster-combat{grid-template-columns:92px minmax(0,1fr);margin-top:11px;min-height:0}
            .character-roster-stat{min-height:78px;padding:8px}
            .character-roster-stat:nth-child(3){border-left:0;border-top:1px solid rgba(166,124,82,.18)}
            .character-roster-stat:nth-child(4){border-top:1px solid rgba(166,124,82,.18)}
            .character-roster-stat-pv{padding:10px 6px}
            .character-roster-pv-main{font-size:23px}
            .character-roster-stat-ac{grid-row:1}
            .character-roster-ac-shield{width:58px;height:71px}
            .character-roster-stat > strong{font-size:22px}
            .character-roster-pv-modifiers{gap:3px 8px}
            .character-roster-pv-mod strong{font-size:11px}
            .character-roster-tabrail{right:-23px;top:96px;gap:7px}
            .character-roster-tab{width:30px;height:76px;font-size:8px}
            .character-roster-panel-metric{min-height:75px}
            .character-roster-panel-heading strong{font-size:18px}
            .character-roster-stat-note{display:none}
        }
        @media(max-width:520px){
            .characters-index-v2{padding-inline:14px}
            .character-roster-card{grid-template-columns:minmax(0,1fr);min-height:0}
            .character-roster-portrait{height:278px;min-height:278px;border-radius:18px 18px 0 0;border-right:0;border-bottom:1px solid var(--index-line)}
            .character-roster-image{object-fit:cover;object-position:center 16%}
            .character-roster-body{padding:18px 17px 14px;border-radius:0 0 18px 18px}
            .character-roster-name{font-size:27px}
            .character-roster-combat{grid-template-columns:92px minmax(0,1fr)}
            .character-roster-stat{min-height:74px}
            .character-roster-pv-main{font-size:23px}
            .character-roster-ac-shield{width:58px;height:71px}
            .character-roster-footer{margin-top:13px}
            .character-roster-tabrail{position:static;flex-direction:row;gap:8px;margin:10px 0 2px}
            .character-roster-tab{writing-mode:horizontal-tb;width:auto;height:36px;padding:0 14px;border-radius:9px;border-left:1px solid rgba(165,120,84,.24);font-size:10px}
            .character-roster-tab::before{display:none}
            .character-roster-panel-metric{min-height:70px;padding:10px 5px}
            .character-roster-panel-metric strong{font-size:23px}
        }
        @keyframes rosterWoundedVeil{
            0%,100%{opacity:.34} 42%{opacity:.70} 60%{opacity:.44}
        }
        @keyframes rosterCriticalVeil{
            0%,13%,100%{opacity:.40} 20%{opacity:.91} 30%{opacity:.47} 40%{opacity:.84} 54%,82%{opacity:.43}
        }
        @keyframes rosterDownVeil{
            0%,100%{opacity:.16}50%{opacity:.29}
        }
        @keyframes rosterWoundedEdge{
            0%,100%{box-shadow:inset 0 0 12px rgba(154,44,41,.12)}
            42%{box-shadow:inset 0 0 27px rgba(168,42,39,.33)}
        }
        @keyframes rosterCriticalEdge{
            0%,13%,100%{box-shadow:inset 0 0 14px rgba(143,27,26,.18)}
            20%{box-shadow:inset 0 0 38px rgba(185,37,38,.51)}
            30%{box-shadow:inset 0 0 17px rgba(147,31,32,.23)}
            40%{box-shadow:inset 0 0 32px rgba(174,34,36,.43)}
            55%,82%{box-shadow:inset 0 0 15px rgba(154,32,34,.2)}
        }
        @keyframes rosterDownEdge{
            0%,100%{box-shadow:inset 0 0 14px rgba(112,69,65,.18)}
            50%{box-shadow:inset 0 0 25px rgba(116,59,59,.29)}
        }
        @media(prefers-reduced-motion:reduce){
            .character-roster-card,.character-roster-image,.character-roster-open,
            .character-roster-entry-arrow,.character-roster-delete,.character-roster-portrait::before{transition:none!important}
            .character-roster-portrait,.character-roster-portrait::before,
            .character-roster-pv-mod svg{animation:none!important}
            .character-roster-tab{transition:none!important}
            .character-roster-card:hover .character-roster-entry-arrow{transform:none}
        }
    </style>


    <div

        x-data="{

            createOpen: {{ $errors->any() ? 'true' : 'false' }},

            forgeName: @js(old('name', '')),
            forgeSpecies: @js(old('species', '')),

            advancedOpen: false,

            xpEnabled: {{ old('xp_enabled') ? 'true' : 'false' }},

            customProfEnabled: {{ old('custom_prof_enabled') ? 'true' : 'false' }},

            multiClass: {{ old('multi_class_enabled') ? 'true' : 'false' }},

            imagePreview: null,

            classes: [

                {

                    class: @js(old('classes.0.class', '')),

                    subclass: @js(old('classes.0.subclass', '')),

                    level: {{ (int) old('classes.0.level', 1) }}

                }

            ],

            addClass() {

                if (this.classes.length < 6) {

                    this.multiClass = true;

                    this.classes.push({

                        class: '',

                        subclass: '',

                        level: 1

                    });

                }

            },

            removeClass(index) {

                if (this.classes.length <= 1) return;

                this.classes.splice(index, 1);

                this.multiClass = this.classes.length > 1;

            },

            get totalLevel() {

                return this.classes.reduce(

                    (total, item) => total + (parseInt(item.level) || 0),

                    0

                );

            },

            updatePreview(event) {

                const file = event.target.files[0];

                if (!file) {

                    this.imagePreview = null;

                    return;

                }

                const reader = new FileReader();

                reader.onload = e => this.imagePreview = e.target.result;

                reader.readAsDataURL(file);

            },

            closeCreate() {

                this.createOpen = false;

            }

        }"

        class="characters-index-v2 min-h-full px-5 py-7 md:px-8"

        @keydown.escape.window="closeCreate()"

    >

        <div class="mx-auto max-w-[1940px]">

            {{-- ================================================================
                 CABEÇALHO
            ================================================================= --}}

            <header
                class="
                    mb-7
                    flex
                    flex-col
                    gap-5
                    border-b
                    border-[#cdbb9f]/55
                    pb-5
                    sm:flex-row
                    sm:items-end
                    sm:justify-between
                "
            >
                <div>
                    <p
                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-[0.23em]
                            text-[#8c6239]
                        "
                    >
                        Grimório de Aventureiro
                    </p>

                    <div class="mt-1 flex items-center gap-3">
                        <h1
                            class="
                                font-serif
                                text-[30px]
                                font-black
                                leading-none
                                text-[#53150f]
                            "
                        >
                            Personagens
                        </h1>

                        @if ($characters->isNotEmpty())
                            <span
                                class="
                                    inline-flex
                                    h-7
                                    min-w-7
                                    items-center
                                    justify-center
                                    rounded-full
                                    bg-[#eadbc8]
                                    px-2
                                    font-serif
                                    text-[11px]
                                    font-black
                                    text-[#6b1d14]
                                "
                            >
                                {{ $characters->count() }}
                            </span>
                        @endif
                    </div>


                </div>

                <button
                    type="button"
                    @click="createOpen = true"
                    class="character-index-create"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 5v14M5 12h14"
                        />
                    </svg>

                    Forjar personagem
                </button>
            </header>


            @if (session('success'))
                <div
                    class="
                        mb-6
                        flex
                        items-center
                        gap-2
                        rounded-xl
                        border
                        border-[#8c6239]/20
                        bg-[#eadbc8]/45
                        px-4
                        py-3
                        text-[11px]
                        font-semibold
                        text-[#53150f]
                    "
                >
                    <span class="font-black">✓</span>
                    {{ session('success') }}
                </div>
            @endif


            {{-- ================================================================
                 LISTA
            ================================================================= --}}

            @if ($characters->isEmpty())

                <div
                    class="
                        rounded-2xl
                        border
                        border-dashed
                        border-[#cdbb9f]
                        bg-[#fffdf8]/75
                        px-6
                        py-16
                        text-center
                    "
                >
                    <div
                        class="
                            mx-auto
                            mb-4
                            flex
                            h-14
                            w-14
                            items-center
                            justify-center
                            rounded-full
                            border
                            border-[#cdbb9f]/60
                            bg-[#eadbc8]/55
                            text-[#6b1d14]
                        "
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.7"
                                d="M12 5v14M5 12h14"
                            />
                        </svg>
                    </div>

                    <h2
                        class="
                            font-serif
                            text-xl
                            font-black
                            text-[#53150f]
                        "
                    >
                        Nenhuma história começou ainda
                    </h2>

                    <p
                        class="
                            mx-auto
                            mt-2
                            max-w-md
                            text-[11px]
                            leading-relaxed
                            text-[#755847]
                        "
                    >
                        Forje seu primeiro personagem e deixe o Spellbound registrar o restante da jornada.
                    </p>

                    <button
                        type="button"
                        @click="createOpen = true"
                        class="character-index-create mt-5"
                    >
                        Forjar primeiro personagem
                    </button>
                </div>

            @else

                <div
                    class="
                        character-roster-grid
                    "
                >
                    @foreach ($characters as $character)

                        @php
                            $primaryClass =
                                $character->primary_class;

                            $combat =
                                $character->combat;

                            $currentHp =
                                max(
                                    0,
                                    (int) (
                                        $combat?->current_hp
                                        ?? 0
                                    )
                                );

                            $baseMaxHp =
                                max(
                                    0,
                                    (int) (
                                        $combat?->max_hp
                                        ?? 0
                                    )
                                );

                            $bonusMaxHp =
                                (int) (
                                    $combat?->temporary_max_hp
                                    ?? 0
                                );

                            $effectiveMaxHp =
                                max(
                                    0,
                                    $baseMaxHp
                                    +
                                    $bonusMaxHp
                                );

                            $temporaryHp =
                                max(
                                    0,
                                    (int) (
                                        $combat?->temporary_hp
                                        ?? 0
                                    )
                                );

                            $combatOverrides =
                                $combat?->overrides ?? [];

                            if (is_string($combatOverrides)) {
                                $decodedOverrides = json_decode($combatOverrides, true);
                                $combatOverrides = is_array($decodedOverrides) ? $decodedOverrides : [];
                            }

                            if ($combatOverrides instanceof \Illuminate\Contracts\Support\Arrayable) {
                                $combatOverrides =
                                    $combatOverrides->toArray();
                            } elseif (is_object($combatOverrides)) {
                                $combatOverrides =
                                    (array) $combatOverrides;
                            }

                            if (!is_array($combatOverrides)) {
                                $combatOverrides = [];
                            }

                            $maxHpReduction =
                                min(
                                    max(
                                        0,
                                        $effectiveMaxHp - 1
                                    ),
                                    max(
                                        0,
                                        (int) data_get(
                                            $combatOverrides,
                                            'hp.max_reduction',
                                            0
                                        )
                                    )
                                );

                            $usableMaxHp =
                                max(
                                    1,
                                    $effectiveMaxHp - $maxHpReduction
                                );

                            $displayHpCurrent =
                                min(
                                    $currentHp,
                                    $usableMaxHp
                                );

                            $hpPercent =
                                $usableMaxHp > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            (int) round(
                                                (
                                                    $displayHpCurrent
                                                    /
                                                    $usableMaxHp
                                                )
                                                * 100
                                            )
                                        )
                                    )
                                    : 0;

                            $deathSuccesses =
                                max(
                                    0,
                                    (int) (
                                        $combat?->death_save_successes
                                        ?? 0
                                    )
                                );

                            $deathFailures =
                                max(
                                    0,
                                    (int) (
                                        $combat?->death_save_failures
                                        ?? 0
                                    )
                                );

                            $morquenEnabled =
                                (bool) data_get(
                                    $character->sheet_settings,
                                    'optional_rules.morquen',
                                    false
                                );

                            $deathFailureLimit =
                                $morquenEnabled
                                    ? 6
                                    : 3;

                            /*
                             * Zero PV não significa morte automaticamente.
                             * A morte acontece quando as falhas de death save
                             * atingem o limite da regra ativa.
                             */
                            $isDead =
                                $combat !== null
                                &&
                                $currentHp <= 0
                                &&
                                $deathFailures
                                    >=
                                    $deathFailureLimit;

                            $status =
                                'Estável';

                            $statusKey =
                                'healthy';

                            if ($isDead) {
                                $status =
                                    'Morto';

                                $statusKey =
                                    'dead';

                            } elseif (
                                $currentHp <= 0
                                &&
                                $deathSuccesses >= 3
                            ) {
                                $status =
                                    'Estabilizado';

                                $statusKey =
                                    'down';

                            } elseif ($currentHp <= 0) {
                                $status =
                                    'Caído';

                                $statusKey =
                                    'down';

                            } elseif ($hpPercent <= 25) {
                                $status =
                                    'Crítico';

                                $statusKey =
                                    'critical';

                            } elseif ($hpPercent <= 50) {
                                $status =
                                    'Ferido';

                                $statusKey =
                                    'wounded';
                            }

                            $campaignLinks =
                                $campaignsByCharacter->get(
                                    $character->id,
                                    collect()
                                );

                            $activeCampaign =
                                $campaignLinks->first(
                                    fn ($campaign) =>
                                        (bool) $campaign->is_active
                                );

                            $displayCampaign =
                                $activeCampaign
                                ??
                                $campaignLinks->first();

                            $campaignCount =
                                $campaignLinks->count();

                            // A campanha ativa determina a experiência quando a usa.
                            $xpCampaign = $campaignLinks->first(
                                fn ($link) => (bool) ($link->experience_enabled ?? false)
                            );
                            $usesExperience = $xpCampaign !== null
                                || (bool) data_get($combat?->overrides, 'uses_experience', false);
                            $xpSource = $xpCampaign !== null
                                ? (int) ($xpCampaign->experience_points ?? 0)
                                : (int) ($combat?->experience_points ?? 0);
                            $xpProgress = $usesExperience
                                ? \App\Support\CharacterExperience::progress(
                                    (int) $character->level,
                                    $xpSource
                                )
                                : null;

                            $classSummary =
                                $character->classes
                                    ->map(
                                        function ($class) {
                                            $name =
                                                trim(
                                                    (string) (
                                                        $class->class
                                                        ?? ''
                                                    )
                                                );

                                            $level =
                                                (int) (
                                                    $class->level
                                                    ?? 0
                                                );

                                            $subclass =
                                                trim(
                                                    (string) (
                                                        $class->subclass
                                                        ?? ''
                                                    )
                                                );

                                            $label =
                                                $name !== ''
                                                    ? $name
                                                    : 'Classe';

                                            if ($level > 0) {
                                                $label .= ' ' . $level;
                                            }

                                            if ($subclass !== '') {
                                                $label .= ' · ' . $subclass;
                                            }

                                            return $label;
                                        }
                                    )
                                    ->filter()
                                    ->implode(' / ');

                            if ($classSummary === '') {
                                $classSummary =
                                    $primaryClass?->class
                                    ?? 'Classe não definida';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | CLASSE DE ARMADURA CALCULADA
                            |--------------------------------------------------------------------------
                            |
                            | O campo character_combat.armor_class é apenas um valor persistido
                            | legado/base e não representa necessariamente a CA que aparece na
                            | ficha. O Show calcula:
                            |
                            |   corpo = melhor armadura equipada OU defesa base
                            |   total = corpo + escudos equipados + bônus extras
                            |
                            | Abaixo reproduzimos a mesma regra em PHP para o Index.
                            |
                            */

                            $abilities =
                                $character->abilities;

                            $abilityTemporaryBonuses =
                                $abilities?->temporary_bonuses
                                ?? [];

                            if (is_string($abilityTemporaryBonuses)) {
                                $decoded =
                                    json_decode(
                                        $abilityTemporaryBonuses,
                                        true
                                    );

                                $abilityTemporaryBonuses =
                                    is_array($decoded)
                                        ? $decoded
                                        : [];
                            }

                            if (!is_array($abilityTemporaryBonuses)) {
                                $abilityTemporaryBonuses = [];
                            }

                            $abilityOverrides =
                                $abilities?->overrides
                                ?? [];

                            if (is_string($abilityOverrides)) {
                                $decoded =
                                    json_decode(
                                        $abilityOverrides,
                                        true
                                    );

                                $abilityOverrides =
                                    is_array($decoded)
                                        ? $decoded
                                        : [];
                            }

                            if (!is_array($abilityOverrides)) {
                                $abilityOverrides = [];
                            }

                            $abilityKeyMap = [
                                'str' => 'strength',
                                'dex' => 'dexterity',
                                'con' => 'constitution',
                                'int' => 'intelligence',
                                'wis' => 'wisdom',
                                'cha' => 'charisma',
                            ];

                            $abilityModifiers = [];

                            foreach (
                                $abilityKeyMap
                                as $shortKey => $fullKey
                            ) {
                                $baseScore =
                                    (int) (
                                        $abilities?->{$fullKey}
                                        ?? 10
                                    );

                                $temporaryBonus =
                                    (int) (
                                        $abilityTemporaryBonuses[$fullKey]
                                        ?? $abilityTemporaryBonuses[$shortKey]
                                        ?? 0
                                    );

                                $hasFullOverride =
                                    array_key_exists(
                                        $fullKey,
                                        $abilityOverrides
                                    )
                                    &&
                                    $abilityOverrides[$fullKey] !== null
                                    &&
                                    $abilityOverrides[$fullKey] !== '';

                                $hasShortOverride =
                                    array_key_exists(
                                        $shortKey,
                                        $abilityOverrides
                                    )
                                    &&
                                    $abilityOverrides[$shortKey] !== null
                                    &&
                                    $abilityOverrides[$shortKey] !== '';

                                if ($hasFullOverride) {
                                    $effectiveScore =
                                        (int) $abilityOverrides[$fullKey];

                                } elseif ($hasShortOverride) {
                                    $effectiveScore =
                                        (int) $abilityOverrides[$shortKey];

                                } else {
                                    $effectiveScore =
                                        $baseScore
                                        +
                                        $temporaryBonus;
                                }

                                $effectiveScore =
                                    max(
                                        1,
                                        $effectiveScore
                                    );

                                $abilityModifiers[$shortKey] =
                                    (int) floor(
                                        ($effectiveScore - 10)
                                        / 2
                                    );
                            }


                            /*
                             * Configuração da Defesa Base:
                             * overrides.armor.mode + overrides.armor.bonuses
                             */
                            $combatOverrides =
                                $combat?->overrides
                                ?? [];

                            if (is_string($combatOverrides)) {
                                $decoded =
                                    json_decode(
                                        $combatOverrides,
                                        true
                                    );

                                $combatOverrides =
                                    is_array($decoded)
                                        ? $decoded
                                        : [];
                            }

                            if (!is_array($combatOverrides)) {
                                $combatOverrides = [];
                            }

                            $armorConfig =
                                data_get(
                                    $combatOverrides,
                                    'armor',
                                    []
                                );

                            if (!is_array($armorConfig)) {
                                $armorConfig = [];
                            }

                            $storedArmorMode =
                                $armorConfig['mode']
                                ?? ['dex'];

                            $armorMode =
                                is_array($storedArmorMode)
                                    ? array_values(
                                        $storedArmorMode
                                    )
                                    : [
                                        $storedArmorMode,
                                    ];

                            if (
                                empty($armorMode)
                                &&
                                !array_key_exists(
                                    'mode',
                                    $armorConfig
                                )
                            ) {
                                $armorMode = [
                                    'dex',
                                ];
                            }

                            $baseDefenseBodyAc =
                                10
                                +
                                collect($armorMode)
                                    ->sum(
                                        fn ($ability) =>
                                            (int) (
                                                $abilityModifiers[$ability]
                                                ?? 0
                                            )
                                    );

                            $armorNamedBonusTotal =
                                collect(
                                    $armorConfig['bonuses']
                                    ?? []
                                )
                                    ->filter(
                                        fn ($bonus) =>
                                            trim(
                                                (string) (
                                                    $bonus['name']
                                                    ?? ''
                                                )
                                            ) !== ''
                                    )
                                    ->sum(
                                        fn ($bonus) =>
                                            (int) (
                                                $bonus['value']
                                                ?? 0
                                            )
                                    );


                            /*
                             * Normalizador de properties dos itens.
                             */
                            $itemProperties =
                                static function ($item): array {
                                    $properties =
                                        $item->properties
                                        ?? [];

                                    if (is_string($properties)) {
                                        $decoded =
                                            json_decode(
                                                $properties,
                                                true
                                            );

                                        $properties =
                                            is_array($decoded)
                                                ? $decoded
                                                : [];
                                    }

                                    return is_array($properties)
                                        ? $properties
                                        : [];
                                };

                            $armorAbilityMap = [
                                'strength' => 'str',
                                'constitution' => 'con',
                                'intelligence' => 'int',
                                'wisdom' => 'wis',
                                'charisma' => 'cha',

                                // Compatibilidade defensiva com registros antigos.
                                'str' => 'str',
                                'con' => 'con',
                                'int' => 'int',
                                'wis' => 'wis',
                                'cha' => 'cha',
                            ];

                            $armorBodyFromItem =
                                function (
                                    $item
                                ) use (
                                    $itemProperties,
                                    $abilityModifiers,
                                    $armorAbilityMap
                                ): ?int {
                                    $properties =
                                        $itemProperties(
                                            $item
                                        );

                                    $armor =
                                        data_get(
                                            $properties,
                                            'armor'
                                        );

                                    if (!is_array($armor)) {
                                        return null;
                                    }

                                    $baseAc =
                                        (int) (
                                            $armor['base_ac']
                                            ?? 10
                                        );

                                    $magicBonus =
                                        (int) (
                                            $armor['magic_bonus']
                                            ?? 0
                                        );

                                    $dexterityMode =
                                        $armor['dexterity_mode']
                                        ?? match (
                                            $armor['category']
                                            ?? null
                                        ) {
                                            'light' =>
                                                'full',

                                            'medium' =>
                                                'capped',

                                            default =>
                                                'none',
                                        };

                                    $dexterity =
                                        (int) (
                                            $abilityModifiers['dex']
                                            ?? 0
                                        );

                                    $dexterityContribution =
                                        match ($dexterityMode) {
                                            'full' =>
                                                $dexterity,

                                            'capped' =>
                                                min(
                                                    $dexterity,
                                                    max(
                                                        0,
                                                        (int) (
                                                            $armor['dexterity_cap']
                                                            ?? 2
                                                        )
                                                    )
                                                ),

                                            default =>
                                                0,
                                        };

                                    $extraAbilityBonus =
                                        collect(
                                            is_array(
                                                $armor['ability_modifiers']
                                                ?? null
                                            )
                                                ? $armor['ability_modifiers']
                                                : []
                                        )
                                            ->sum(
                                                function (
                                                    $ability
                                                ) use (
                                                    $armorAbilityMap,
                                                    $abilityModifiers
                                                ) {
                                                    $key =
                                                        $armorAbilityMap[$ability]
                                                        ?? $ability;

                                                    return (int) (
                                                        $abilityModifiers[$key]
                                                        ?? 0
                                                    );
                                                }
                                            );

                                    return (
                                        $baseAc
                                        +
                                        $dexterityContribution
                                        +
                                        $extraAbilityBonus
                                        +
                                        $magicBonus
                                    );
                                };


                            /*
                             * Se mais de uma armadura tiver sido marcada como equipada por
                             * algum dado antigo, o Show escolhe a que produzir a maior CA.
                             */
                            $equippedArmorBodyAc =
                                $character->items
                                    ->filter(
                                        fn ($item) =>
                                            (bool) $item->equipped
                                    )
                                    ->map(
                                        fn ($item) =>
                                            $armorBodyFromItem(
                                                $item
                                            )
                                    )
                                    ->filter(
                                        fn ($value) =>
                                            $value !== null
                                    )
                                    ->max();

                            $armorBodyAc =
                                $equippedArmorBodyAc !== null
                                    ? (int) $equippedArmorBodyAc
                                    : $baseDefenseBodyAc;


                            /*
                             * Escudos acumulam ac_bonus + magic_bonus, exatamente como
                             * no cálculo usado pelo Show.
                             */
                            $equippedShieldBonus =
                                $character->items
                                    ->filter(
                                        fn ($item) =>
                                            (bool) $item->equipped
                                    )
                                    ->sum(
                                        function (
                                            $item
                                        ) use (
                                            $itemProperties
                                        ): int {
                                            $properties =
                                                $itemProperties(
                                                    $item
                                                );

                                            $shield =
                                                data_get(
                                                    $properties,
                                                    'shield'
                                                );

                                            if (!is_array($shield)) {
                                                return 0;
                                            }

                                            return (
                                                (int) (
                                                    $shield['ac_bonus']
                                                    ?? 0
                                                )
                                                +
                                                (int) (
                                                    $shield['magic_bonus']
                                                    ?? 0
                                                )
                                            );
                                        }
                                    );

                            $armorClass =
                                $armorBodyAc
                                +
                                $equippedShieldBonus
                                +
                                $armorNamedBonusTotal;

                            $initiative =
                                (int) (
                                    $combat?->initiative_bonus
                                    ?? 0
                                );

                            $speed =
                                (int) (
                                    $combat?->speed
                                    ?? 0
                                );

                            $exhaustion =
                                max(
                                    0,
                                    (int) (
                                        $combat?->exhaustion_level
                                        ?? 0
                                    )
                                );

                            $inventoryItems =
                                $character->items instanceof \Illuminate\Support\Collection
                                    ? $character->items
                                    : collect($character->items ?? []);

                            $equippedCount =
                                $inventoryItems->filter(
                                    function ($item) {
                                        return (bool) (
                                            data_get($item, 'pivot.is_equipped')
                                            ?? data_get($item, 'is_equipped')
                                            ?? data_get($item, 'equipped')
                                            ?? false
                                        );
                                    }
                                )->count();

                            $attunedCount =
                                $inventoryItems->filter(
                                    function ($item) {
                                        return (bool) (
                                            data_get($item, 'pivot.is_attuned')
                                            ?? data_get($item, 'is_attuned')
                                            ?? data_get($item, 'attuned')
                                            ?? false
                                        );
                                    }
                                )->count();

                            $inventoryPreview = $inventoryItems->take(4);

                            $spellSummary =
                                $spellSummaryByCharacter->get($character->id);

                            $spellTotal = (int) ($spellSummary->total ?? 0);
                            $spellPrepared = (int) ($spellSummary->prepared ?? 0);
                        @endphp


                        <article
                            x-data="{ rosterPanel: 'overview' }"
                            class="
                                character-roster-card
                                {{ $isDead ? 'is-dead' : '' }}
                                {{ !$isDead && $currentHp === 0 ? 'is-zero is-down' : '' }}
                                {{ $statusKey === 'wounded' ? 'is-wounded' : '' }}
                                {{ $statusKey === 'critical' ? 'is-critical' : '' }}
                            "
                        >

                            {{-- RETRATO --}}
                            <div class="character-roster-portrait">

                                @if ($character->image_path)
                                    <img
                                        src="{{ Storage::url($character->image_path) }}"
                                        alt="{{ $character->name }}"
                                        class="character-roster-image"
                                    >
                                @else
                                    <div
                                        class="
                                            absolute
                                            inset-0
                                            flex
                                            items-center
                                            justify-center
                                            text-[#8c6239]/30
                                        "
                                    >
                                        <svg
                                            class="h-20 w-20"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 21a7 7 0 0114 0"
                                            />
                                        </svg>
                                    </div>
                                @endif


                                <span class="character-roster-portrait-frame" aria-hidden="true"></span>

                                <div class="character-roster-level">
                                    <small>Nível</small>
                                    <strong>{{ $character->level }}</strong>
                                </div>


                                @if (!$isDead && $statusKey !== 'healthy')
                                    <div class="character-roster-photo-caption">
                                        <div class="character-roster-photo-caption-stack">
                                            <span>
                                                <i
                                                    class="
                                                        character-roster-status-dot
                                                        {{ $statusKey }}
                                                    "
                                                ></i>

                                                {{ $status }}
                                            </span>
                                        </div>
                                    </div>
                                @endif


                                @if ($isDead)
                                    <div class="character-roster-memorial">
                                        <div class="character-roster-memorial-mark">
                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M12 3v18M7 8h10"
                                                />
                                            </svg>
                                        </div>

                                        <small>Em memória</small>

                                        <h3>{{ $character->name }}</h3>

                                        <div class="character-roster-memorial-rule"></div>

                                        <p>
                                            A jornada terminou
                                            @if ($displayCampaign)
                                                em
                                                <strong>{{ $displayCampaign->name }}</strong>.
                                            @else
                                                em combate.
                                            @endif

                                            A ficha permanece como registro da aventura.
                                        </p>
                                    </div>
                                @endif

                            </div>


                            {{-- DOSSIÊ --}}
                            <div class="character-roster-body">

                                {{-- A identidade começa pelo nome; as demais informações acompanham a leitura. --}}
                                <div class="character-roster-heading">
                                    <h2 class="character-roster-name">
                                        <a href="{{ route('characters.show', $character) }}">{{ $character->name }}</a>
                                    </h2>

                                    <p class="character-roster-summary">{{ $classSummary }}</p>

                                    <div class="character-roster-traits" aria-label="Identidade do personagem">
                                        <span class="character-roster-trait">{{ $character->species ?? 'Espécie não definida' }}</span>
                                        <span class="character-roster-trait">{{ $character->background ?? 'Antecedente não definido' }}</span>
                                        <span class="character-roster-trait">{{ $character->alignment ?? 'Alinhamento não definido' }}</span>
                                        @if ($exhaustion > 0)
                                            <span class="character-roster-trait">Exaustão {{ $exhaustion }}</span>
                                        @endif
                                    </div>
                                </div>


                                <div class="character-roster-panel-shell">
                                    <nav class="character-roster-tabrail" aria-label="Páginas do dossiê de {{ $character->name }}">
                                        <button type="button" class="character-roster-tab"
                                                x-bind:class="{ 'is-active': rosterPanel === 'inventory' }"
                                                x-bind:aria-pressed="rosterPanel === 'inventory' ? 'true' : 'false'"
                                                x-on:click="rosterPanel = rosterPanel === 'inventory' ? 'overview' : 'inventory'"
                                                title="Mostrar inventário ou voltar ao resumo">Inventário</button>
                                        <button type="button" class="character-roster-tab"
                                                x-bind:class="{ 'is-active': rosterPanel === 'spells' }"
                                                x-bind:aria-pressed="rosterPanel === 'spells' ? 'true' : 'false'"
                                                x-on:click="rosterPanel = rosterPanel === 'spells' ? 'overview' : 'spells'"
                                                title="Mostrar feitiços ou voltar ao resumo">Feitiços</button>
                                    </nav>

                                    <div class="character-roster-panel" x-show="rosterPanel === 'overview'" x-transition.opacity.duration.180ms>
                                        <div class="character-roster-campaign">
                                            <div class="character-roster-campaign-icon">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 19V6.5A1.5 1.5 0 015.5 5H18l2 3-2 3H5.5" />
                                                </svg>
                                            </div>

                                            <div class="character-roster-campaign-copy">
                                                <small>
                                                    {{ $activeCampaign
                                                        ? 'Campanha atual'
                                                        : (
                                                            $displayCampaign
                                                                ? 'Campanha vinculada'
                                                                : 'Campanha'
                                                        )
                                                    }}
                                                </small>
                                                <strong>
                                                    {{ $displayCampaign?->name
                                                        ?? 'Nenhuma campanha vinculada'
                                                    }}
                                                </strong>
                                            </div>

                                            @if ($campaignCount > 1)
                                                <span class="character-roster-campaign-more">+{{ $campaignCount - 1 }}</span>
                                            @endif
                                        </div>

                                        <div class="character-roster-combat">
                                            <div class="character-roster-stat character-roster-stat-ac">
                                                <div class="character-roster-ac-shield {{ $armorClass >= 100 ? 'is-triple' : '' }}" role="img" aria-label="Classe de Armadura: {{ $armorClass }}">
                                                    <svg viewBox="0 0 80 100" fill="none" aria-hidden="true">
                                                        <path d="M40 4 71 16v30c0 21-12 37-31 50C21 83 9 67 9 46V16L40 4Z" fill="#f6e6d2" stroke="#a77a53" stroke-width="2.1"/>
                                                        <path d="M40 11 64 20v26c0 17-9 30-24 40-15-10-24-23-24-40V20l24-9Z" fill="#fffaf2" stroke="#c49b74" stroke-width="1.1"/>
                                                        <path d="M27 23 40 18l13 5M30 73l10 8 10-8" stroke="#ba916c" stroke-width="1" opacity=".72"/>
                                                    </svg>
                                                    <div class="character-roster-ac-value">
                                                        <small>CA</small>
                                                        <strong>{{ $armorClass }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="character-roster-stat character-roster-stat-pv" aria-label="Pontos de vida: {{ $displayHpCurrent }} de {{ $usableMaxHp }}{{ $temporaryHp > 0 ? ', ' . $temporaryHp . ' temporários' : '' }}{{ $bonusMaxHp > 0 ? ', bônus de ' . $bonusMaxHp . ' na vida máxima' : '' }}{{ $maxHpReduction > 0 ? ', redução de ' . $maxHpReduction . ' na vida máxima' : '' }}">
                                                <span>Pontos de vida</span>
                                                <div class="character-roster-pv-main">
                                                    <span class="pv-current">{{ $displayHpCurrent }}</span>
                                                    <span class="pv-slash">/</span>
                                                    <span class="pv-max {{ $maxHpReduction > 0 ? 'has-reduction' : ($bonusMaxHp > 0 ? 'has-bonus' : '') }}">{{ $usableMaxHp }}</span>
                                                </div>
                                                @if ($temporaryHp > 0 || $bonusMaxHp > 0 || $maxHpReduction > 0)
                                                    <div class="character-roster-pv-modifiers">
                                                        @if ($temporaryHp > 0)
                                                            <span class="character-roster-pv-mod temp" title="{{ $temporaryHp }} pontos de vida temporários">
                                                                <svg viewBox="0 0 24 26" fill="none" stroke="currentColor" aria-hidden="true">
                                                                    <path d="M12 2 21 6v7c0 5-3.6 8.4-9 11C6.6 21.4 3 18 3 13V6l9-4Z"/>
                                                                    <path d="m7.5 12 3 3 6-7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                                <strong>+{{ $temporaryHp }}</strong><small>temp.</small>
                                                            </span>
                                                        @endif
                                                        @if ($bonusMaxHp > 0)
                                                            <span class="character-roster-pv-mod bonus" title="Bônus de {{ $bonusMaxHp }} pontos na vida máxima">
                                                                <strong>+{{ $bonusMaxHp }}</strong><small>máx.</small>
                                                            </span>
                                                        @endif
                                                        @if ($maxHpReduction > 0)
                                                            <span class="character-roster-pv-mod reduced" title="Redução de {{ $maxHpReduction }} pontos na vida máxima">
                                                                <strong>−{{ $maxHpReduction }}</strong><small>máx.</small>
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="character-roster-stat">
                                                <span>Iniciativa</span>
                                                <strong>{{ $initiative >= 0 ? '+' : '' }}{{ $initiative }}</strong>
                                            </div>

                                            <div class="character-roster-stat">
                                                <span>Movimento</span>
                                                <strong>{{ $speed > 0 ? $speed . ' ft.' : '—' }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <section class="character-roster-panel" x-show="rosterPanel === 'inventory'" x-transition.opacity.duration.180ms x-cloak>
                                        <div class="character-roster-panel-heading">
                                            <div><strong>Inventário</strong><small>Registro de pertences</small></div>
                                            <button type="button" class="character-roster-back" x-on:click="rosterPanel = 'overview'">← Resumo</button>
                                        </div>
                                        <div class="character-roster-panel-metrics">
                                            <div class="character-roster-panel-metric"><small>Itens</small><strong>{{ $inventoryItems->count() }}</strong></div>
                                            <div class="character-roster-panel-metric"><small>Equipados</small><strong>{{ $equippedCount }}</strong></div>
                                            <div class="character-roster-panel-metric"><small>Sintonizados</small><strong>{{ $attunedCount }}</strong></div>
                                        </div>
                                        @if ($inventoryPreview->isNotEmpty())
                                            <div class="character-roster-panel-entries" aria-label="Alguns itens do inventário">
                                                @foreach ($inventoryPreview as $inventoryPreviewItem)
                                                    <span class="character-roster-panel-entry" title="{{ data_get($inventoryPreviewItem, 'name') ?? 'Item' }}">
                                                        {{ data_get($inventoryPreviewItem, 'name') ?? 'Item sem nome' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="character-roster-panel-empty">Nenhum item registrado neste inventário.</p>
                                        @endif
                                    </section>

                                    <section class="character-roster-panel" x-show="rosterPanel === 'spells'" x-transition.opacity.duration.180ms x-cloak>
                                        <div class="character-roster-panel-heading">
                                            <div><strong>Grimório</strong><small>Registro de feitiços</small></div>
                                            <button type="button" class="character-roster-back" x-on:click="rosterPanel = 'overview'">← Resumo</button>
                                        </div>
                                        @if ($spellSummaryAvailable)
                                            <div class="character-roster-panel-metrics" style="grid-template-columns:repeat(2,minmax(0,1fr))">
                                                <div class="character-roster-panel-metric"><small>Feitiços registrados</small><strong>{{ $spellTotal }}</strong></div>
                                                <div class="character-roster-panel-metric">
                                                    <small>{{ $spellPreparedAvailable ? 'Preparados' : 'Preparação' }}</small>
                                                    <strong>{{ $spellPreparedAvailable ? $spellPrepared : '—' }}</strong>
                                                </div>
                                            </div>
                                            <p class="character-roster-panel-empty">Consulte a ficha para os efeitos e detalhes da conjuração.</p>
                                        @else
                                            <p class="character-roster-panel-empty">O resumo do grimório ainda não está disponível nesta listagem. Os feitiços continuam acessíveis na ficha.</p>
                                        @endif
                                    </section>
                                </div>

                                @if ($usesExperience && $xpProgress)
                                    <div class="character-roster-xp">
                                        <div class="character-roster-xp-head">
                                            <span>Experiência</span>
                                            <strong>{{ number_format($xpProgress['experience'], 0, ',', '.') }} XP</strong>
                                        </div>
                                        @if (!$xpProgress['max_level'])
                                            <div class="character-roster-xp-track">
                                                <span style="width: {{ $xpProgress['percent'] }}%"></span>
                                            </div>
                                        @endif
                                    </div>
                                @endif


                                {{-- AÇÕES --}}
                                <div class="character-roster-footer">

                                    <a
                                        href="{{ route('characters.show', $character) }}"
                                        class="character-roster-open"
                                        aria-label="{{ $isDead ? 'Abrir memorial de ' : 'Abrir ficha de ' }}{{ $character->name }}"
                                        title="{{ $isDead ? 'Abrir memorial' : 'Abrir ficha' }}"
                                    >
                                        <span>{{ $isDead ? 'Ver memorial' : 'Abrir ficha' }}</span>
                                        <span class="character-roster-entry-arrow" aria-hidden="true">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h14m-5.5-5.5L18.5 12 13 17.5"/>
                                            </svg>
                                        </span>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('characters.destroy', $character) }}"
                                        onsubmit="return confirm('Tem certeza que deseja remover este personagem? Esta ação não pode ser desfeita.');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Excluir personagem"
                                            class="character-roster-delete"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 011 1h-4a1 1 0 01-1-1V3m-6 4h16"
                                                />
                                            </svg>
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </article>

                    @endforeach
                </div>

            @endif

        </div>

        {{-- Modal --}}

        <div

            x-show="createOpen"

            x-cloak

            x-transition.opacity

            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#2b1d17]/60 p-4 backdrop-blur-sm"

        >

            <div

                x-show="createOpen"

                x-transition

                @click.outside="closeCreate()"

                class="relative flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#f4f1e8] shadow-2xl"

            >

                <div class="flex shrink-0 items-center justify-between border-b border-[#cdbb9f]/70 bg-[#efe9dc]/60 px-5 py-4">

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">

                            Novo Personagem

                        </p>

                        <h2 class="mt-1 font-serif text-xl font-black text-[#53150f]">

                            Forjar Aventureiro

                        </h2>

                    </div>

                    <button

                        type="button"

                        @click="closeCreate()"

                        class="flex h-9 w-9 items-center justify-center rounded-lg text-[#8c6239] hover:bg-[#6b1d14]/10 hover:text-[#6b1d14]"

                    >

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18"/>

                        </svg>

                    </button>

                </div>

                <form

                    method="POST"

                    action="{{ route('characters.store') }}"

                    enctype="multipart/form-data"

                    class="min-h-0 overflow-y-auto"

                >

                    @csrf

                    <input type="hidden" name="multi_class_enabled" :value="classes.length > 1 ? 1 : 0">

                    <input type="hidden" name="level" :value="totalLevel">

                    <div class="space-y-5 p-5 sm:p-6">
                        <div class="grid gap-3 md:grid-cols-[minmax(0,1.35fr)_minmax(220px,1fr)]">
                            <div class="sb-forge-intro">
                                <div class="sb-forge-intro-mark" aria-hidden="true">✦</div>
                                <div>
                                    <strong>O primeiro passo da sua jornada</strong>
                                    <p>Defina o essencial. Atributos, inventário, perícias, magia e habilidades serão configurados depois, dentro da ficha.</p>
                                </div>
                            </div>
                            <div class="sb-forge-preview" aria-live="polite">
                                <div class="sb-forge-preview-portrait">
                                    <img x-show="imagePreview" x-cloak :src="imagePreview" alt="Prévia do retrato">
                                    <svg x-show="!imagePreview" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-5 3.5-8 8-8s8 3 8 8"></path></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <small class="character-roster-kicker">Seu aventureiro</small>
                                    <p class="sb-forge-preview-name" x-text="forgeName.trim() || 'Nome a definir'"></p>
                                    <p class="sb-forge-preview-sub" x-text="(forgeSpecies.trim() || 'Espécie') + ' · Nível ' + totalLevel"></p>
                                    <p class="sb-forge-preview-sub" x-text="classes.filter(c => c.class.trim()).map(c => c.class.trim()).join(' / ') || 'Classe a definir'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Identidade --}}

                        <div>

                            <div class="mb-4">

                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">

                                    Identidade

                                </p>

                                <p class="mt-1 text-xs text-[#8c6239]/70">

                                    Informações básicas do aventureiro.

                                </p>

                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                                <div class="sm:col-span-2">

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Nome

                                    </label>

                                    <input

                                        type="text"

                                        name="name"
                                        x-model="forgeName"

                                        value="{{ old('name') }}"

                                        required

                                        maxlength="120"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                                <div>

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Espécie

                                    </label>

                                    <input

                                        type="text"

                                        name="species"
                                        x-model="forgeSpecies"

                                        value="{{ old('species') }}"

                                        required

                                        maxlength="80"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                                <div>

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Antecedente

                                    </label>

                                    <input

                                        type="text"

                                        name="background"

                                        value="{{ old('background') }}"

                                        required

                                        maxlength="120"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                                <div>

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Alinhamento

                                    </label>

                                    <input

                                        type="text"

                                        name="alignment"

                                        value="{{ old('alignment') }}"

                                        required

                                        maxlength="50"

                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                    >

                                </div>

                            </div>

                        </div>

                        {{-- Classes --}}

                        <div class="border-t border-[#cdbb9f]/40 pt-5">

                            <div class="mb-4 flex items-end justify-between gap-3">

                                <div>

                                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">

                                        Classes

                                    </p>

                                    <p class="mt-1 text-xs text-[#8c6239]/70">

                                        Defina a composição inicial do personagem.

                                    </p>

                                </div>

                                <div class="rounded-lg border border-[#cdbb9f]/50 bg-[#efe9dc] px-3 py-1.5 text-center">

                                    <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Nível Total

                                    </span>

                                    <span class="block font-serif text-lg font-black text-[#53150f]" x-text="totalLevel"></span>

                                </div>

                            </div>

                            <div class="space-y-3">

                                <template x-for="(item, index) in classes" :key="index">

                                    <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40 p-3">

                                        <div class="mb-3 flex items-center justify-between gap-2">

                                            <span class="text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                Classe <span x-text="index + 1"></span>

                                            </span>

                                            <button

                                                x-show="classes.length > 1"

                                                type="button"

                                                @click="removeClass(index)"

                                                class="text-[9px] font-black uppercase tracking-widest text-red-700 hover:text-red-900"

                                            >

                                                Remover

                                            </button>

                                        </div>

                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">

                                            <div class="sm:col-span-5">

                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                    Classe

                                                </label>

                                                <input

                                                    type="text"

                                                    :name="`classes[${index}][class]`"

                                                    x-model="item.class"

                                                    required

                                                    maxlength="80"

                                                    placeholder="Ex.: Clérigo"

                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                                >

                                            </div>

                                            <div class="sm:col-span-5">

                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                    Subclasse

                                                </label>

                                                <input

                                                    type="text"

                                                    :name="`classes[${index}][subclass]`"

                                                    x-model="item.subclass"

                                                    maxlength="80"

                                                    placeholder="Opcional"

                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                                >

                                            </div>

                                            <div class="sm:col-span-2">

                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                                    Nível

                                                </label>

                                                <input

                                                    type="number"

                                                    :name="`classes[${index}][level]`"

                                                    x-model.number="item.level"

                                                    min="1"

                                                    max="20"

                                                    required

                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                                >

                                            </div>

                                        </div>

                                    </div>

                                </template>

                            </div>

                            <button

                                type="button"

                                x-show="classes.length === 1"

                                @click="addClass()"

                                class="mt-3 inline-flex items-center gap-2 rounded-lg border border-[#cdbb9f] bg-[#f4f1e8] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#6b1d14] transition hover:border-[#6b1d14]/30 hover:bg-[#efe9dc]"

                            >

                                <span class="text-sm">+</span>

                                Adicionar multiclasse

                            </button>

                            <button

                                type="button"

                                x-show="classes.length > 1"

                                @click="addClass()"

                                class="mt-3 text-[9px] font-black uppercase tracking-widest text-[#6b1d14] hover:text-[#53150f]"

                                x-show="classes.length < 6"

                            >

                                + Adicionar outra classe

                            </button>

                        </div>

                        {{-- Avançado --}}

                        <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40">

                            <button

                                type="button"

                                @click="advancedOpen = !advancedOpen"

                                class="flex w-full items-center justify-between px-4 py-3 text-left"

                            >

                                <div>

                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-[#8c6239]">

                                        Configurações avançadas

                                    </span>

                                    <p class="mt-0.5 text-xs text-[#8c6239]/70">

                                        XP e regras personalizadas.

                                    </p>

                                </div>

                                <svg

                                    class="h-4 w-4 text-[#8c6239] transition-transform"

                                    :class="{ 'rotate-180': advancedOpen }"

                                    fill="none"

                                    viewBox="0 0 24 24"

                                    stroke="currentColor"

                                >

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 9l-7 7-7-7"/>

                                </svg>

                            </button>

                            <div x-show="advancedOpen" x-transition class="border-t border-[#cdbb9f]/40 px-4 pb-4">

                                <div class="space-y-3 pt-4">

                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] p-3">

                                        <label class="flex cursor-pointer items-center gap-3">

                                            <input

                                                type="checkbox"

                                                name="xp_enabled"

                                                value="1"

                                                x-model="xpEnabled"

                                                class="rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30"

                                            >

                                            <span>

                                                <span class="block text-[10px] font-black uppercase tracking-widest text-[#53150f]">

                                                    Usar experiência

                                                </span>

                                                <span class="text-[11px] text-[#8c6239]">

                                                    Ative somente se a campanha usar XP.

                                                </span>

                                            </span>

                                        </label>

                                        <div x-show="xpEnabled" x-transition class="mt-3">

                                            <input

                                                type="number"

                                                name="experience_points"

                                                min="0"

                                                value="{{ old('experience_points', 0) }}"

                                                :disabled="!xpEnabled"

                                                placeholder="XP inicial"

                                                class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                            >

                                        </div>

                                    </div>

                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] p-3">

                                        <label class="flex cursor-pointer items-center gap-3">

                                            <input

                                                type="checkbox"

                                                name="custom_prof_enabled"

                                                value="1"

                                                x-model="customProfEnabled"

                                                class="rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30"

                                            >

                                            <span>

                                                <span class="block text-[10px] font-black uppercase tracking-widest text-[#53150f]">

                                                    Proficiência personalizada

                                                </span>

                                                <span class="text-[11px] text-[#8c6239]">

                                                    Sobrescreva o valor padrão.

                                                </span>

                                            </span>

                                        </label>

                                        <div x-show="customProfEnabled" x-transition class="mt-3">

                                            <input

                                                type="number"

                                                name="proficiency_bonus"

                                                value="{{ old('proficiency_bonus') }}"

                                                :disabled="!customProfEnabled"

                                                placeholder="Bônus de proficiência"

                                                class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"

                                            >

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- Foto --}}

                        <div class="border-t border-[#cdbb9f]/40 pt-5">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                                <div class="relative flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#efe9dc]">

                                    <svg class="h-7 w-7 text-[#8c6239]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>

                                    </svg>

                                    <img

                                        x-show="imagePreview"

                                        x-cloak

                                        :src="imagePreview"

                                        alt="Pré-visualização"

                                        class="absolute inset-0 h-full w-full object-cover object-top"

                                    >

                                </div>

                                <div class="flex-1">

                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">

                                        Foto de Perfil

                                    </label>

                                    <input

                                        type="file"

                                        name="image"

                                        accept="image/*"

                                        @change="updatePreview"

                                        class="block w-full text-sm text-[#8c6239] file:mr-4 file:cursor-pointer file:rounded-xl file:border-0 file:bg-[#efe9dc] file:px-4 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-[#6b1d14] hover:file:bg-[#6b1d14] hover:file:text-[#f4f1e8]"

                                    >

                                </div>

                            </div>

                        </div>

                        @if ($errors->any())

                            <div class="rounded-xl border border-red-800/20 bg-red-50/50 p-4 text-sm text-red-900">

                                <p class="mb-2 text-[9px] font-black uppercase tracking-widest text-red-800">

                                    Corrija os campos abaixo

                                </p>

                                <ul class="space-y-1">

                                    @foreach ($errors->all() as $error)

                                        <li class="flex gap-2">

                                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-red-600"></span>

                                            <span>{{ $error }}</span>

                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif

                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-[#cdbb9f]/50 bg-[#efe9dc]/40 px-5 py-4 sm:flex-row sm:justify-end">

                        <button

                            type="button"

                            @click="closeCreate()"

                            class="rounded-xl px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#8c6239] hover:bg-[#8c6239]/10"

                        >

                            Cancelar

                        </button>

                        <button

                            type="submit"

                            class="rounded-xl bg-[#6b1d14] px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#f4f1e8] transition hover:bg-[#53150f]"

                        >

                            Confirmar Criação

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>