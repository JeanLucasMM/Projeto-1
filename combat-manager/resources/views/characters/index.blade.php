<x-app-layout>
    {{-- Usa exatamente a geometria e a paleta dos dados de Ataques. --}}
    @include('components.ui.poly-die-styles')

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
            $relationsToLoad = [
                'abilities',
                'items',
                'classes',
                'combat',
            ];

            $firstRosterCharacter = $characters->first();

            if ($firstRosterCharacter && method_exists($firstRosterCharacter, 'spellSlots')) {
                $relationsToLoad[] = 'spellSlots';
            }

            $characters->loadMissing($relationsToLoad);
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

        /*
        |--------------------------------------------------------------------------
        | GRIMÓRIO COMPACTO — preparados, truques e mídias pessoais
        |--------------------------------------------------------------------------
        | Trazemos as informações leves do grimório em lote. Os filtros de
        | preparados/truques são aplicados por personagem mais abaixo.
        */
        $preparedSpellsByCharacter = collect();

        if (
            $characterIds->isNotEmpty()
            && \Illuminate\Support\Facades\Schema::hasTable('character_spells')
            && \Illuminate\Support\Facades\Schema::hasTable('spells')
            && \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'character_id')
            && \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'spell_id')
        ) {
            $hasKnown = \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'known');
            $hasPrepared = \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'prepared');
            $hasAlwaysPrepared = \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'always_prepared');
            $hasPersonalMedia = \Illuminate\Support\Facades\Schema::hasColumn('character_spells', 'personal_media_path');

            $spellColumns = [
                'character_spells.character_id',
                'character_spells.id',
                'character_spells.spell_id',
                'spells.name',
                'spells.level',
                'spells.school',
            ];

            $spellColumns[] = $hasKnown
                ? 'character_spells.known'
                : \Illuminate\Support\Facades\DB::raw('0 as known');

            $spellColumns[] = $hasPrepared
                ? 'character_spells.prepared'
                : \Illuminate\Support\Facades\DB::raw('0 as prepared');

            $spellColumns[] = $hasAlwaysPrepared
                ? 'character_spells.always_prepared'
                : \Illuminate\Support\Facades\DB::raw('0 as always_prepared');

            $spellColumns[] = $hasPersonalMedia
                ? 'character_spells.personal_media_path'
                : \Illuminate\Support\Facades\DB::raw('NULL as personal_media_path');

            $preparedSpellsByCharacter = \Illuminate\Support\Facades\DB::table('character_spells')
                ->join('spells', 'spells.id', '=', 'character_spells.spell_id')
                ->whereIn('character_spells.character_id', $characterIds)
                ->select($spellColumns)
                ->orderBy('spells.level')
                ->orderBy('spells.name')
                ->get()
                ->groupBy('character_id');
        }

        /*
        |--------------------------------------------------------------------------
        | DIÁRIO — tenta usar uma fonte real quando existir, senão mostra vazio
        |--------------------------------------------------------------------------
        */
        $diaryEntriesByCharacter = collect();

        if ($characterIds->isNotEmpty()) {
            $diarySources = [
                ['table' => 'character_diary_entries', 'title' => ['title', 'name'], 'body' => ['content', 'body', 'entry', 'text', 'notes']],
                ['table' => 'character_diaries', 'title' => ['title', 'name'], 'body' => ['content', 'body', 'entry', 'text', 'notes']],
                ['table' => 'character_journal_entries', 'title' => ['title', 'name'], 'body' => ['content', 'body', 'entry', 'text', 'notes']],
                ['table' => 'character_journals', 'title' => ['title', 'name'], 'body' => ['content', 'body', 'entry', 'text', 'notes']],
            ];

            foreach ($diarySources as $source) {
                if (!\Illuminate\Support\Facades\Schema::hasTable($source['table']) || !\Illuminate\Support\Facades\Schema::hasColumn($source['table'], 'character_id')) {
                    continue;
                }

                $titleColumn = collect($source['title'])->first(fn ($column) => \Illuminate\Support\Facades\Schema::hasColumn($source['table'], $column));
                $bodyColumn = collect($source['body'])->first(fn ($column) => \Illuminate\Support\Facades\Schema::hasColumn($source['table'], $column));
                $updatedColumn = collect(['updated_at', 'created_at'])->first(fn ($column) => \Illuminate\Support\Facades\Schema::hasColumn($source['table'], $column));

                if (!$titleColumn && !$bodyColumn) {
                    continue;
                }

                $selectColumns = ['character_id'];
                $selectColumns[] = $titleColumn
                    ? \Illuminate\Support\Facades\DB::raw($titleColumn . ' as diary_title')
                    : \Illuminate\Support\Facades\DB::raw('NULL as diary_title');
                $selectColumns[] = $bodyColumn
                    ? \Illuminate\Support\Facades\DB::raw($bodyColumn . ' as diary_body')
                    : \Illuminate\Support\Facades\DB::raw('NULL as diary_body');
                $selectColumns[] = $updatedColumn
                    ? \Illuminate\Support\Facades\DB::raw($updatedColumn . ' as diary_updated_at')
                    : \Illuminate\Support\Facades\DB::raw('NULL as diary_updated_at');

                $diaryRows = \Illuminate\Support\Facades\DB::table($source['table'])
                    ->whereIn('character_id', $characterIds)
                    ->when($updatedColumn, fn ($query) => $query->orderByDesc($updatedColumn))
                    ->select($selectColumns)
                    ->get()
                    ->groupBy('character_id')
                    ->map(fn ($entries) => $entries->take(4)->values());

                if ($diaryRows->isNotEmpty()) {
                    $diaryEntriesByCharacter = $diaryRows;
                    break;
                }
            }
        }

    @endphp

    <style>
        /* SPELLBOUND V66.2.227 — Dossiê revisado: duas abas externas, sem sobreposição.
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

        /* Header editorial: geometria/tipografia/paleta do Cofre de NPCs. */
        .characters-index-v2 .character-vault-head{
            max-width:1280px;margin:0 auto clamp(24px,2.8vw,36px);
            padding:1px 0 22px;border-bottom:1px solid rgba(142,105,72,.19)
        }
        .characters-index-v2 .character-vault-head__intro{
            display:flex;align-items:center;justify-content:space-between;
            gap:20px;min-width:0;margin-bottom:0
        }
        .characters-index-v2 .character-vault-head__identity{min-width:0}
        .characters-index-v2 .character-vault-head__title{
            margin:0;color:#6b2119;font:700 clamp(27px,3.05vw,39px)/1.12 Georgia,"Times New Roman",serif;
            letter-spacing:-.042em
        }
        .characters-index-v2 .character-vault-head__stroke{
            display:block;width:72px;height:3px;margin-top:13px;border-radius:3px;
            background:linear-gradient(90deg,#79352a 0%,#c8ab88 82%,transparent 100%)
        }
        .characters-index-v2 .character-vault-head__actions{
            display:flex;align-items:center;gap:9px;flex-shrink:0
        }
        .characters-index-v2 .character-vault-action{
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            min-height:41px;padding:0 16px;border:1px solid #d8c4aa;border-radius:12px;
            background:#fbf7f0;color:#6b2119;
            font:700 12px/1.2 Georgia,"Times New Roman",serif;
            white-space:nowrap;cursor:pointer;box-shadow:0 2px 5px rgba(65,40,26,.04);
            transition:background .18s ease,border-color .18s ease,box-shadow .18s ease,transform .18s ease
        }
        .characters-index-v2 .character-vault-action svg{width:16px;height:16px;flex:0 0 16px}
        .characters-index-v2 .character-vault-action--primary{
            background:#72261c;color:#fff8f0;border-color:#72261c
        }
        .characters-index-v2 .character-vault-action:hover{
            transform:translateY(-1px);background:#f1e6d7;border-color:#c4aa89;
            box-shadow:0 5px 13px rgba(65,40,26,.09)
        }
        .characters-index-v2 .character-vault-action--primary:hover{
            background:#5e1c15;border-color:#5e1c15;color:#fff8f0
        }
        .characters-index-v2 .character-vault-action:focus-visible{
            outline:2px solid #a8815e;outline-offset:3px
        }
        @media(max-width:640px){
            .characters-index-v2 .character-vault-head{padding-bottom:18px;margin-bottom:30px}
            .characters-index-v2 .character-vault-head__intro{align-items:flex-start;gap:12px;margin-bottom:0}
            .characters-index-v2 .character-vault-head__title{font-size:clamp(24px,6vw,31px)}
            .characters-index-v2 .character-vault-head__stroke{margin-top:9px}
            .characters-index-v2 .character-vault-action{
                width:39px;min-width:39px;min-height:39px;height:39px;padding:0;border-radius:11px
            }
            .characters-index-v2 .character-vault-action span{display:none}
        }
        @media(max-width:385px){
            .characters-index-v2 .character-vault-head__title{font-size:23px}
        }
        @media(prefers-reduced-motion:reduce){
            .characters-index-v2 .character-vault-action{transition:none}
            .characters-index-v2 .character-vault-action:hover{transform:none}
        }
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
        /* Nível: plaqueta original em pergaminho, sem ornamentos que interferem no número. */
        .character-roster-level{position:absolute;right:14px;top:14px;z-index:6;display:flex;
            width:48px;height:54px;flex-direction:column;align-items:center;justify-content:center;
            border:1px solid rgba(161,114,72,.32);border-radius:12px;
            background:linear-gradient(180deg,#fffcf5,#f7ecdc);
            box-shadow:0 5px 12px rgba(55,32,19,.12),inset 0 0 0 3px rgba(238,224,207,.45);
            color:var(--index-wine);pointer-events:none}
        .character-roster-level small{font:850 8px/1.1 system-ui,sans-serif;letter-spacing:.11em;text-transform:uppercase;color:#966f4e}
        .character-roster-level strong{margin-top:3px;font:750 23px/1 Georgia,serif}
        /* Estado integrado à base da moldura, fora da imagem. */
        .character-roster-statebar{
            position:absolute;left:8px;right:8px;bottom:8px;z-index:10;
            display:flex;align-items:center;justify-content:space-between;gap:10px;
            height:29px;min-width:0;padding:0 12px;
            border:1px solid rgba(119,76,54,.30);border-top-color:rgba(132,87,62,.40);
            border-radius:0 0 8px 8px;
            background:linear-gradient(180deg,#e9d3b7 0%,#d6b694 100%);
            color:#604031;box-shadow:inset 0 1px rgba(255,247,235,.60);
            pointer-events:none
        }
        .character-roster-statebar-label{
            font:800 8px/1 system-ui,sans-serif;letter-spacing:.13em;text-transform:uppercase;
            color:#8b6045
        }
        .character-roster-statebar-value{display:inline-flex;align-items:center;gap:7px;min-width:0;
            color:#653a2b;font:850 9px/1 system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase;
            white-space:nowrap
        }
        .character-roster-statebar.critical .character-roster-statebar-value,
        .character-roster-statebar.down .character-roster-statebar-value{color:#7b282a}
        .character-roster-statebar.dead .character-roster-statebar-value{color:#59433c}
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
        /* ================================================================
           SPELLBOUND V66.2.227 — DOSSIE DE PERSONAGEM
           Moldura fora da arte, paginas integradas e transicao de folio.
           As regras de vida/CA permanecem calculadas pelo Blade original.
           ================================================================ */
        .characters-index-v2{
            --index-paper:var(--theme-paper-strong,#fffdf8);
            --index-paper-alt:var(--theme-paper-alt,#f8efe4);
            --index-wine:var(--theme-accent-strong,#651f18);
            --index-gold:#ad865d;
            --index-text:var(--theme-text-primary,#3d2a22);
            --index-muted:var(--theme-text-secondary,#80634f);
        }
        .character-roster-grid{gap:37px}
        .character-roster-card{
            grid-template-columns:268px minmax(0,1fr);max-width:994px;min-height:417px;
            isolation:isolate;border:1px solid rgba(146,104,72,.37);border-radius:19px;
            background:linear-gradient(125deg,#fffaf3 0%,#fffdf8 43%,#f6eadb 100%);
            box-shadow:0 21px 42px rgba(82,48,28,.12),0 4px 12px rgba(82,48,28,.055),inset 0 1px 0 rgba(255,255,255,.95);
            transition:transform .28s ease,box-shadow .28s ease,border-color .28s ease;
        }
        .character-roster-card:hover{
            transform:translateY(-2px);border-color:rgba(138,92,60,.51);
            box-shadow:0 23px 44px rgba(82,48,28,.14),0 5px 14px rgba(82,48,28,.06),inset 0 1px 0 rgba(255,255,255,.95)
        }
        .character-roster-card::after{
            content:'';position:absolute;pointer-events:none;z-index:1;inset:7px 7px 7px 274px;
            border:1px solid rgba(182,142,102,.13);border-radius:0 12px 12px 0
        }
        /* A borda e montada FORA da fotografia, que nao recebe filetes brancos. */
        .character-roster-portrait{
            position:relative;overflow:hidden;min-height:100%;border-radius:18px 0 0 18px;
            border-right:1px solid rgba(107,58,39,.36);
            background:linear-gradient(135deg,#ac795f,#e5c5a7 12%,#7d5142 88%,#c89a7a);
            box-shadow:inset -4px 0 10px rgba(66,30,21,.14)
        }
        .character-roster-portrait-art{
            position:absolute;z-index:1;inset:8px 8px 36px;overflow:hidden;
            border:1px solid rgba(120,71,49,.38);border-radius:12px 4px 0 0;
            background:radial-gradient(ellipse at 45% 25%,#f0dfcd,#caa88e 80%);
            box-shadow:0 0 0 1px rgba(255,231,205,.28),0 7px 16px rgba(62,30,22,.22)
        }
        .character-roster-portrait-art::after{
            content:'';position:absolute;z-index:2;pointer-events:none;inset:0;
            background:linear-gradient(180deg,transparent 52%,rgba(31,19,17,.02) 65%,rgba(29,16,14,.36))
        }
        .character-roster-portrait::after{content:none}
        .character-roster-portrait-frame{display:none!important}
        .character-roster-image{inset:0;width:100%;height:100%;object-fit:cover;object-position:center top}
        .character-roster-level{
            top:15px;right:17px;width:49px;height:54px;border:1px solid rgba(148,101,69,.36);border-radius:11px;
            background:linear-gradient(140deg,#fffaf0,#f3e3ce);
            box-shadow:0 5px 12px rgba(40,20,15,.2),inset 0 0 0 3px rgba(255,252,246,.48)
        }
        .character-roster-photo-caption{left:17px;right:17px;bottom:18px}
        .character-roster-photo-caption-stack>span{background:rgba(56,35,28,.83);border-color:rgba(246,224,207,.21)}
        .character-roster-body{
            position:relative;z-index:3;padding:18px 24px 15px;
            background:radial-gradient(ellipse at 95% 4%,rgba(220,185,140,.10),transparent 45%);
        }
        .character-roster-body::before{
            top:10px;right:10px;width:17px;height:17px;border-color:rgba(155,106,70,.3)
        }
        .character-roster-heading{padding-bottom:11px;border-bottom:1px solid rgba(155,110,74,.19)}
        .character-roster-name{font-size:clamp(27px,2.15vw,34px);letter-spacing:-.022em;line-height:1.13}
        .character-roster-summary{margin-top:5px;color:#71503e;font-size:12px}
        .character-roster-traits{margin-top:7px;gap:4px 8px;color:#a27d5b;font-size:9px}
        .character-roster-panel-shell{position:relative;display:flex;flex-direction:column;min-height:0;flex:1;margin-top:3px}
        .character-roster-tabrail{
            position:relative;z-index:7;top:auto;right:auto;display:flex;align-items:center;justify-content:space-between;
            min-height:38px;margin:3px 0 0;gap:12px;padding-bottom:7px;
            border-bottom:1px solid rgba(161,116,77,.14)
        }
        .character-roster-section-title{
            display:inline-flex;align-items:center;gap:8px;min-width:0;
            font:850 9px/1.2 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.145em;color:#9c795b
        }
        .character-roster-section-title::before{
            content:'';width:6px;height:6px;flex:none;border:1px solid #b89570;transform:rotate(45deg);background:#f7e8d5
        }
        .character-roster-tab-actions{display:flex;gap:6px;align-items:center;margin-left:auto}
        .character-roster-tab{
            position:relative;width:auto;height:29px;display:inline-flex;flex-direction:row;align-items:center;justify-content:center;
            gap:6px;padding:0 10px;border:1px solid rgba(155,112,79,.20);border-radius:8px;
            background:linear-gradient(180deg,rgba(255,252,247,.85),rgba(241,229,215,.57));
            color:#865b43;font:750 10px/1 system-ui,sans-serif;letter-spacing:.015em;text-transform:none;
            writing-mode:horizontal-tb;box-shadow:0 1px 2px rgba(103,62,34,.035),inset 0 1px rgba(255,255,255,.72);
            transition:border-color .2s ease,background .2s ease,color .2s ease,transform .2s ease,box-shadow .2s ease;
        }
        .character-roster-tab::before{display:none}
        .character-roster-tab svg{width:14px;height:14px;flex:none}
        .character-roster-tab::after{
            content:'';position:absolute;left:11px;right:11px;bottom:-8px;height:2px;background:#9c6950;
            border-radius:2px;transform:scaleX(0);opacity:0;transition:transform .24s ease,opacity .24s ease
        }
        .character-roster-tab:hover,.character-roster-tab:focus-visible{
            color:#63281e;transform:translateY(-1px);outline-offset:2px;
            background:#fff8ed;border-color:rgba(149,98,59,.43)
        }
        .character-roster-tab.is-active{
            color:#65251c;background:linear-gradient(180deg,#f9ead9,#f4e0cb);font-weight:850;
            border-color:rgba(145,96,62,.43);box-shadow:inset 0 1px rgba(255,251,244,.92),0 1px 4px rgba(106,58,39,.065)
        }
        .character-roster-tab.is-active::after{opacity:1;transform:scaleX(1)}
        /* As páginas principais ocupam a mesma superfície: sem saltos de altura. */
        .character-roster-panel-stage{
            position:relative;z-index:5;display:grid;grid-template-columns:minmax(0,1fr);
            min-height:207px;height:207px;max-height:207px;overflow:hidden;perspective:1100px;
        }
        .character-roster-panel-stage>.character-roster-panel{
            grid-area:1/1;min-width:0;min-height:0;max-height:100%;padding-top:7px;
            transform-origin:50% 50%;backface-visibility:hidden
        }
        .roster-page-enter{z-index:2;transition:opacity .35s ease,transform .40s cubic-bezier(.19,.82,.24,1)}
        .roster-page-enter-start{opacity:0;transform:translateX(25px) rotateY(-5deg) scale(.985)}
        .roster-page-enter-end{opacity:1;transform:translateX(0) rotateY(0) scale(1)}
        .roster-page-leave{z-index:1;transition:opacity .19s ease,transform .32s ease}
        .roster-page-leave-start{opacity:1;transform:translateX(0) rotateY(0)}
        .roster-page-leave-end{opacity:0;transform:translateX(-19px) rotateY(4deg) scale(.99)}
        .character-roster-panel-stage.is-returning .roster-page-enter-start{transform:translateX(-23px) rotateY(5deg) scale(.985)}
        .character-roster-panel-stage.is-returning .roster-page-leave-end{transform:translateX(19px) rotateY(-4deg) scale(.99)}
        /* Campanha em tarja estreita, em vez de um grande retangulo administrativo. */
        .character-roster-campaign{
            min-height:48px;gap:9px;margin-top:2px;padding:7px 11px;border-radius:10px;
            border:1px solid rgba(164,114,76,.19);border-left:3px solid rgba(165,113,76,.48);
            background:linear-gradient(105deg,rgba(242,228,210,.78),rgba(255,251,244,.50));
            box-shadow:inset 0 1px rgba(255,255,255,.74)
        }
        .character-roster-campaign-icon{width:29px;height:29px;background:rgba(255,251,246,.65);color:#895337}
        .character-roster-campaign-icon svg{width:16px;height:16px}
        .character-roster-campaign-copy small{font-size:8px;letter-spacing:.115em}
        .character-roster-campaign-copy strong{font-size:13px;margin-top:3px}
        .character-roster-combat{
            display:grid;grid-template-columns:82px minmax(0,1.65fr) minmax(0,.96fr) minmax(0,1fr);
            gap:8px;min-height:116px;margin-top:10px;padding:0;
            border:0;border-radius:0;background:none;box-shadow:none;overflow:visible
        }
        .character-roster-stat{
            position:relative;min-width:0;min-height:116px;gap:5px;padding:10px 6px;
            border:1px solid rgba(162,115,77,.20);border-radius:11px;
            background:linear-gradient(160deg,rgba(255,253,248,.92),rgba(243,231,215,.67));
            box-shadow:inset 0 1px rgba(255,255,255,.85),0 2px 4px rgba(94,59,35,.025)
        }
        .character-roster-stat+.character-roster-stat{border-left:1px solid rgba(162,115,77,.20)}
        .character-roster-stat>span{font-size:8px;letter-spacing:.09em;color:#997250}
        .character-roster-stat>strong{font-size:23px;color:#65271d}
        .character-roster-stat>small{font:650 8px/1.2 system-ui,sans-serif;color:#a58a75;white-space:nowrap}
        .character-roster-stat-ac{
            background:linear-gradient(160deg,rgba(255,253,248,.95),rgba(245,234,220,.78));
            border-color:rgba(162,115,77,.20);padding:7px 4px
        }
        .character-roster-ac-shield{width:59px;height:76px;filter:drop-shadow(0 2px 2px rgba(86,53,33,.065))}
        .character-roster-ac-value{top:24%;gap:4px}
        .character-roster-ac-value small{color:#92735a;font-size:8px;letter-spacing:.1em}
        .character-roster-ac-value strong{font-size:24px;color:#672b21}
        .character-roster-stat-pv{padding:13px 8px 8px;background:linear-gradient(160deg,#fffaf2,#f6e9dc)}
        .character-roster-pv-main{font-size:26px;gap:6px;letter-spacing:-.015em}
        .character-roster-hp-track{
            position:relative;width:80%;height:7px;overflow:hidden;border-radius:999px;margin:6px auto 0;
            border:1px solid rgba(164,103,77,.16);background:linear-gradient(180deg,rgba(105,58,42,.15),rgba(160,114,82,.10));
            box-shadow:inset 0 1px 2px rgba(72,37,22,.08),0 0 0 1px rgba(255,255,255,.22)
        }
        .character-roster-hp-fill,
        .character-roster-hp-bonus-zone,
        .character-roster-hp-bonus-fill,
        .character-roster-hp-temp-zone,
        .character-roster-hp-reduction-zone{
            position:absolute;top:0;bottom:0;display:block
        }
        .character-roster-hp-fill{
            left:0;z-index:2;border-radius:inherit;
            background:linear-gradient(90deg,#a45043,#cf8860);box-shadow:0 0 8px rgba(172,70,53,.22)
        }
        .character-roster-hp-fill.healthy{background:linear-gradient(90deg,#977d4f,#c5a475)}
        .character-roster-hp-fill.critical,.character-roster-hp-fill.down{background:linear-gradient(90deg,#822d2b,#bb4b41)}
        .character-roster-hp-bonus-zone{
            z-index:1;border-radius:0 999px 999px 0;
            background:linear-gradient(90deg,rgba(189,149,67,.22),rgba(227,194,111,.55));
            box-shadow:inset 0 0 0 1px rgba(255,239,183,.22),0 0 7px rgba(197,154,60,.12)
        }
        .character-roster-hp-bonus-fill{
            z-index:3;border-radius:0 999px 999px 0;
            background:linear-gradient(90deg,#b58a33,#e0b656 52%,#f0d88a);
            box-shadow:0 0 9px rgba(211,169,75,.28), inset 0 0 0 1px rgba(255,241,187,.28)
        }
        .character-roster-hp-temp-zone{
            z-index:4;border-radius:999px;
            background:linear-gradient(90deg,rgba(110,208,255,.70),rgba(58,151,242,.92));
            box-shadow:inset 0 0 0 1px rgba(222,245,255,.55),0 0 9px rgba(58,151,242,.28)
        }
        .character-roster-hp-temp-zone::after{
            content:'';position:absolute;inset:1px;border-radius:inherit;
            background:linear-gradient(180deg,rgba(255,255,255,.48),rgba(255,255,255,0));opacity:.75
        }
        .character-roster-hp-reduction-zone{
            right:0;z-index:5;border-radius:999px 0 0 999px;
            background:linear-gradient(90deg,rgba(93,63,120,.85),rgba(56,34,73,.96));
            box-shadow:inset 0 0 0 1px rgba(203,171,233,.16)
        }
        .character-roster-hp-track.has-temp{box-shadow:inset 0 1px 2px rgba(72,37,22,.08),0 0 0 1px rgba(255,255,255,.22),0 0 11px rgba(83,165,239,.16)}
        .character-roster-pv-modifiers{margin-top:2px;gap:2px 8px}
        .character-roster-pv-mod{font-size:9px}
        .character-roster-pv-mod strong{font-size:11px}
        .character-roster-pv-mod small{font-size:8px}
        .character-roster-pv-mod svg{width:12px;height:13px}
        /* Catalogos visuais com todos os registros, rolados dentro do folio. */
        .character-roster-catalog{display:flex;flex-direction:column}
        .character-roster-catalog-header{
            position:relative;flex:none;display:flex;align-items:center;justify-content:space-between;
            gap:12px;min-height:39px;margin-bottom:7px;border-bottom:1px solid rgba(171,128,91,.19)
        }
        .character-roster-catalog-title{display:flex;align-items:baseline;min-width:0;gap:9px}
        .character-roster-catalog-title strong{font:750 18px/1.12 Georgia,serif;color:#682b21}
        .character-roster-catalog-title span{font:750 9px/1.15 system-ui,sans-serif;color:#9b7759;white-space:nowrap}
        .character-roster-section-label{
            grid-column:1/-1;display:flex;align-items:center;gap:8px;margin:1px 0 -1px;
            font:800 9px/1 system-ui,sans-serif;letter-spacing:.16em;text-transform:uppercase;color:#8c674f
        }
        .character-roster-section-label::before{
            content:'';display:block;flex:none;width:18px;height:1px;background:rgba(155,113,77,.42)
        }
        .character-roster-slots-box{position:relative;display:flex;align-items:center}
        .character-roster-slots-toggle{
            display:inline-flex;align-items:center;gap:6px;min-height:30px;padding:0 10px;border-radius:999px;
            border:1px solid rgba(163,120,83,.28);background:linear-gradient(180deg,#fffaf1,#f3e4d0);color:#8c633f;
            font:800 9px/1 system-ui,sans-serif;letter-spacing:.12em;text-transform:uppercase;box-shadow:inset 0 1px 0 rgba(255,255,255,.72)
        }
        .character-roster-slots-toggle svg{width:14px;height:14px;flex:none}
        .character-roster-slots-toggle.is-open{color:#5f281f;border-color:rgba(133,75,46,.42)}
        .character-roster-slots-popover{
            position:absolute;top:calc(100% + 8px);right:0;z-index:14;min-width:152px;padding:9px 10px;border-radius:12px;
            border:1px solid rgba(162,115,78,.24);background:linear-gradient(180deg,#fffaf3,#f4e6d4);box-shadow:0 10px 18px rgba(71,38,20,.14)
        }
        .character-roster-slot-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:4px 0;color:#815c41}
        .character-roster-slot-row strong{font:800 11px/1 Georgia,serif;color:#64261d}
        .character-roster-diary-list{
            display:flex;flex-direction:column;gap:9px;max-height:160px;overflow-y:auto;padding:0 4px 5px 0;scrollbar-width:thin;scrollbar-color:#c6a587 transparent
        }
        .character-roster-diary-list::-webkit-scrollbar{width:5px}
        .character-roster-diary-list::-webkit-scrollbar-thumb{background:#c6a587;border-radius:10px}
        .character-roster-diary-entry{
            display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1px solid rgba(168,124,90,.18);border-radius:12px;
            background:linear-gradient(145deg,rgba(255,252,246,.96),rgba(245,232,214,.82));box-shadow:inset 0 1px 0 rgba(255,255,255,.75)
        }
        .character-roster-diary-mark{flex:none;margin-top:2px;color:#a87a4d;font-size:13px;line-height:1}
        .character-roster-diary-copy{min-width:0;flex:1}
        .character-roster-diary-copy header{display:flex;align-items:baseline;justify-content:space-between;gap:8px}
        .character-roster-diary-copy strong{font:750 12px/1.2 Georgia,serif;color:#673126}
        .character-roster-diary-copy span{font:750 8px/1 system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase;color:#a17b60;white-space:nowrap}
        .character-roster-diary-copy p{margin-top:6px;color:#7c5d48;font-size:10px;line-height:1.45}
        .character-roster-diary-empty{
            display:flex;flex-direction:column;justify-content:center;gap:7px;min-height:160px;padding:0 10px;text-align:center;color:#866751
        }
        .character-roster-diary-empty strong{font:750 14px/1.2 Georgia,serif;color:#673126}
        .character-roster-diary-empty p{font-size:10px;line-height:1.55}
        .character-roster-spellbook-list{align-content:start}
        .character-roster-spell-thumb.cantrip{background:radial-gradient(ellipse at 35% 20%,#ecf2ea,#b7d0c1);color:#5f8d6f}
        .character-roster-back{
            z-index:8;flex:none;padding:6px 2px;background:none;color:#93694a;
            font:750 11px/1.2 Georgia,serif;cursor:pointer
        }
        .character-roster-back:hover,.character-roster-back:focus-visible{color:#60261d;text-underline-offset:3px}
        .character-roster-catalog-list{
            flex:1;min-height:0;max-height:158px;overflow-y:auto;overscroll-behavior:contain;
            display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-content:start;
            gap:7px;padding:0 5px 7px 0;scrollbar-width:thin;scrollbar-color:#c6a587 transparent
        }
        .character-roster-catalog-list::-webkit-scrollbar{width:5px}
        .character-roster-catalog-list::-webkit-scrollbar-thumb{background:#c6a587;border-radius:10px}
        .character-roster-list-entry{
            display:flex;align-items:center;min-width:0;min-height:61px;gap:9px;padding:5px 7px 5px 5px;
            border:1px solid rgba(168,124,90,.18);border-radius:10px;
            background:linear-gradient(130deg,rgba(255,253,249,.94),rgba(247,236,221,.83));
            box-shadow:inset 0 1px 0 rgba(255,255,255,.79)
        }
        .character-roster-thumb{
            flex:none;display:grid;place-items:center;width:49px;height:49px;overflow:hidden;
            border:1px solid rgba(163,123,88,.24);border-radius:7px;
            background:radial-gradient(ellipse at 35% 20%,#f9e9d2,#ddc5a8);color:#997654;
            box-shadow:0 2px 4px rgba(80,48,34,.055)
        }
        .character-roster-thumb img{display:block;width:100%;height:100%;object-fit:cover}
        .character-roster-thumb svg{width:24px;height:24px;opacity:.7}
        .character-roster-entry-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:5px}
        .character-roster-entry-info strong{
            display:-webkit-box;overflow:hidden;-webkit-line-clamp:2;-webkit-box-orient:vertical;
            font:750 11px/1.19 Georgia,serif;color:#67402f;overflow-wrap:anywhere
        }
        .character-roster-rarity,.character-roster-spell-level{
            font:800 8px/1.12 system-ui,sans-serif;letter-spacing:.035em;text-transform:uppercase;color:#9a765b
        }
        .character-roster-list-entry[data-rarity="uncommon"] .character-roster-rarity{color:#4e8263}
        .character-roster-list-entry[data-rarity="rare"] .character-roster-rarity{color:#4b78a5}
        .character-roster-list-entry[data-rarity="very_rare"] .character-roster-rarity{color:#88559e}
        .character-roster-list-entry[data-rarity="legendary"] .character-roster-rarity{color:#a77b29}
        .character-roster-list-entry[data-rarity="artifact"] .character-roster-rarity{color:#aa7832}
        .character-roster-equipped{
            flex:none;display:inline-flex;align-items:center;justify-content:center;gap:3px;
            min-height:21px;padding:0 5px;border-radius:7px;
            border:1px solid rgba(100,131,101,.25);background:#eaf2e7;color:#527552;
            font:800 8px/1 system-ui,sans-serif
        }
        .character-roster-equipped svg{width:12px;height:12px}
        .character-roster-spell-thumb{background:radial-gradient(ellipse at 35% 20%,#efe7f2,#c3aece);color:#80638d}
        .character-roster-spell-entry[data-school="evocation"] .character-roster-spell-thumb{background:linear-gradient(135deg,#eccbb6,#c79879);color:#944b34}
        .character-roster-spell-entry[data-school="necromancy"] .character-roster-spell-thumb{background:linear-gradient(135deg,#e4dce4,#b7a2bc);color:#74537d}
        .character-roster-spell-entry[data-school="conjuration"] .character-roster-spell-thumb{background:linear-gradient(135deg,#d9e7e0,#acccbb);color:#537b71}
        .character-roster-spell-entry[data-school="abjuration"] .character-roster-spell-thumb{background:linear-gradient(135deg,#dbe5f0,#b1c9db);color:#4e7095}
        .character-roster-spell-entry[data-school="illusion"] .character-roster-spell-thumb{background:linear-gradient(135deg,#e7dcf1,#c6afe2);color:#7651a2}
        .character-roster-panel-empty{padding:15px 10px;color:#8e715a;text-align:center;font-size:11px}
        .character-roster-footer{position:relative;z-index:6;margin-top:auto;padding-top:9px;min-height:44px}
        .character-roster-open::before{z-index:2}
        .character-roster-xp{margin-top:5px;padding-top:7px}
        @media(max-width:1080px){
            .character-roster-card{grid-template-columns:238px minmax(0,1fr);min-height:412px}
            .character-roster-card::after{left:245px}
            .character-roster-body{padding:16px 18px 13px}
            .character-roster-combat{grid-template-columns:75px minmax(0,1.6fr) minmax(0,.9fr) minmax(0,.95fr);gap:6px}
            .character-roster-stat{padding-inline:5px}
            .character-roster-ac-shield{width:61px;height:75px}
            .character-roster-pv-main{font-size:23px}
        }
        @media(max-width:820px){
            .character-roster-card{grid-template-columns:198px minmax(0,1fr);min-height:450px}
            .character-roster-card::after{left:205px}
            .character-roster-body{padding:15px 15px 12px}
            .character-roster-name{font-size:26px}
            .character-roster-combat{grid-template-columns:76px minmax(0,1fr);gap:6px;margin-top:8px}
            .character-roster-stat{min-height:74px;padding:6px 5px}
            .character-roster-stat:nth-child(3),.character-roster-stat:nth-child(4){border:1px solid rgba(162,115,77,.20)}
            .character-roster-stat-ac{grid-row:1;min-height:80px}
            .character-roster-stat-pv{min-height:80px}
            .character-roster-ac-shield{width:59px;height:72px}
            .character-roster-stat>small{display:none}
            .character-roster-panel-stage{height:271px;min-height:271px;max-height:271px}
            .character-roster-catalog-list{max-height:218px;grid-template-columns:minmax(0,1fr)}
            .character-roster-list-entry{min-height:56px}
            .character-roster-thumb{width:43px;height:43px}
        }
        @media(max-width:600px){
            .character-roster-card{grid-template-columns:minmax(0,1fr);min-height:0}
            .character-roster-card::after{content:none}
            .character-roster-portrait{height:284px;min-height:284px;border-radius:18px 18px 0 0;border-right:0;border-bottom:1px solid rgba(125,76,49,.32)}
            .character-roster-portrait-art{inset:8px 8px 36px;border-radius:11px 11px 0 0}
            .character-roster-image{object-position:center 14%}
            .character-roster-body{padding:16px 16px 12px}
            .character-roster-panel-stage{height:242px;min-height:242px;max-height:242px}
            .character-roster-combat{grid-template-columns:85px minmax(0,1fr);gap:7px}
            .character-roster-catalog-list{grid-template-columns:repeat(2,minmax(0,1fr));max-height:189px}
            .character-roster-tabrail{position:relative;flex-direction:row;gap:7px;top:auto;right:auto;margin:5px 0 0}
            .character-roster-tab{width:auto;height:30px;padding:0 9px;border-radius:8px;font-size:10px}
            .character-roster-tab::after{display:block}
        }
        @media(max-width:420px){
            .character-roster-section-title{font-size:8px;letter-spacing:.07em}
            .character-roster-tab-actions{gap:5px}
            .character-roster-tab{font-size:9px;padding:0 7px;gap:4px}
            .character-roster-tab svg{width:12px;height:12px}
            .character-roster-catalog-list{grid-template-columns:minmax(0,1fr)}
            .character-roster-catalog-title{gap:6px}
            .character-roster-catalog-title span{font-size:8px}
        }
        @media(prefers-reduced-motion:reduce){
            .roster-page-enter,.roster-page-leave{transition:none!important}
            .roster-page-enter-start,.roster-page-leave-end{transform:none!important}
            .character-roster-tab,.character-roster-tab::after{transition:none!important}
        }
        /* ================================================================
           SPELLBOUND V66.2.228 — MARCADORES DE FOLIO E FERIMENTOS
           A navegação pertence à borda do dossiê, não à área de leitura.
           ================================================================ */
        .character-roster-card{min-height:409px}
        .character-roster-panel-shell{position:static;margin-top:14px;min-height:0;display:flex;flex-direction:column;flex:1}
        .character-roster-panel-shell.is-detail{padding-top:0;min-height:0;flex:1}
        /*
         * SPELLBOUND V66.2.234 — etiquetas guardadas sob a borda da ficha.
         * O trilho comeca 60px DENTRO do card; o recorte so permite
         * que o ultimo trecho de cada etiqueta apareca para fora.
         * Assim não existem mais quatro blocos inteiros flutuando ao lado.
         */
        .character-roster-tabrail{
            position:absolute;top:50%;right:-52px;z-index:12;
            transform:translateY(-50%);
            display:flex;flex-direction:column;justify-content:flex-start;
            align-items:flex-start;gap:0;width:112px;margin:0;padding:0;
            border:0;min-height:0;overflow:visible;
            -webkit-clip-path:inset(0 0 0 60px);
            clip-path:inset(0 0 0 60px)
        }
        .character-roster-tabrail::before{content:none}
        .character-roster-tab-actions{
            display:flex;flex-direction:column;align-items:flex-start;gap:5px;margin:0
        }
        .character-roster-tab{
            position:relative;z-index:1;box-sizing:border-box;
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            flex:none;width:90px;height:79px;min-width:0;
            margin:0;padding:7px 4px 7px 63px;gap:5px;
            border:1px solid rgba(147,101,72,.32);border-left:0;
            border-radius:0 11px 11px 0;
            -webkit-clip-path:none;clip-path:none;
            background:linear-gradient(90deg,#d6b89a 0%,#e7cfb1 49%,#f6e7d4 72%,#fff8e9 100%);
            color:#784b32;font:800 8px/1 system-ui,sans-serif;
            letter-spacing:.065em;text-transform:uppercase;
            writing-mode:horizontal-tb;
            box-shadow:inset -1px 0 rgba(144,94,59,.09),0 3px 6px rgba(76,42,25,.07);
            transition:transform .27s cubic-bezier(.2,.8,.2,1),background .27s ease,
                       color .27s ease,border-color .27s ease,box-shadow .27s ease
        }
        .character-roster-tab:nth-child(1),
        .character-roster-tab:nth-child(2),
        .character-roster-tab:nth-child(3),
        .character-roster-tab:nth-child(4){width:90px}
        .character-roster-tab::before{content:none}
        .character-roster-tab::after{
            content:'';display:block;position:absolute;top:10px;bottom:10px;right:3px;
            left:auto;width:1px;height:auto;transform:none;
            border-radius:1px;background:rgba(150,103,68,.14)
        }
        .character-roster-tab svg{
            display:block;flex:none;width:12px;height:12px;
            stroke-width:1.55;opacity:.9
        }
        .character-roster-tab-label{
            display:block;writing-mode:vertical-rl;text-orientation:mixed;white-space:nowrap
        }
        .character-roster-tab:hover,.character-roster-tab:focus-visible{
            transform:translateX(12px);outline-offset:3px;color:#622d22;
            border-color:rgba(132,77,46,.52);
            background:linear-gradient(90deg,#dfc1a1 0%,#f2d9b9 49%,#fff3df 78%,#fffdf6 100%);
            box-shadow:0 5px 12px rgba(76,42,25,.14)
        }
        .character-roster-tab.is-active{
            transform:translateX(6px);color:#652b20;
            border-color:rgba(130,76,44,.51);
            background:linear-gradient(90deg,#ddba91 0%,#eed2ac 47%,#fff1d8 77%,#fffcf1 100%);
            box-shadow:0 4px 10px rgba(76,42,25,.12)
        }
        .character-roster-tab.is-active:hover,
        .character-roster-tab.is-active:focus-visible{transform:translateX(13px)}
        /* Resumo compacto; detalhes ganham mais área útil para conteúdo rolável. */
        .character-roster-panel-stage{min-height:214px;height:214px;max-height:214px;overflow:hidden}
        .character-roster-panel-shell.is-detail .character-roster-panel-stage{
            display:flex;flex-direction:column;min-height:320px;height:320px;max-height:none
        }
        .character-roster-panel-stage>.character-roster-panel{padding-top:0}
        .character-roster-panel-shell.is-detail .character-roster-panel,
        .character-roster-panel-shell.is-detail .character-roster-catalog{display:flex;flex-direction:column;flex:1;min-height:0}
        .character-roster-panel-shell.is-detail .character-roster-catalog-list{flex:1;min-height:0;max-height:none;padding-bottom:4px}
        .character-roster-panel-shell.is-detail .character-roster-diary-list{flex:1;min-height:0;max-height:none;padding-bottom:4px}
        .character-roster-panel-shell.is-detail .character-roster-diary-empty{flex:1;min-height:0}
        .character-roster-campaign{margin-top:2px}
        .character-roster-combat{margin-top:12px}
        .character-roster-footer{margin-top:auto}
        /* A mudança mantém o movimento do fólio; os marcadores respondem à seleção. */
        .roster-page-enter{
            z-index:2;transform-origin:left center;
            transition:opacity .44s ease,transform .58s cubic-bezier(.18,.9,.22,1),filter .34s ease,box-shadow .44s ease
        }
        .roster-page-enter-start{opacity:.08;transform:translateX(26px) perspective(1200px) rotateY(-16deg) scale(.992);filter:blur(.45px);box-shadow:-22px 0 28px rgba(77,48,30,.14)}
        .roster-page-enter-end{opacity:1;transform:translateX(0) perspective(1200px) rotateY(0) scale(1);filter:blur(0);box-shadow:0 0 0 rgba(0,0,0,0)}
        .roster-page-leave{
            z-index:1;transform-origin:right center;
            transition:opacity .24s ease,transform .42s cubic-bezier(.45,0,.72,.38),filter .2s ease,box-shadow .24s ease
        }
        .roster-page-leave-start{opacity:1;transform:translateX(0) perspective(1200px) rotateY(0) scale(1);filter:blur(0);box-shadow:0 0 0 rgba(0,0,0,0)}
        .roster-page-leave-end{opacity:0;transform:translateX(-22px) perspective(1200px) rotateY(12deg) scale(.99);filter:blur(.28px);box-shadow:18px 0 24px rgba(77,48,30,.12)}
        .character-roster-panel-stage.is-returning .roster-page-enter{transform-origin:right center}
        .character-roster-panel-stage.is-returning .roster-page-leave{transform-origin:left center}
        .character-roster-panel-stage.is-returning .roster-page-enter-start{transform:translateX(-26px) perspective(1200px) rotateY(16deg) scale(.992)}
        .character-roster-panel-stage.is-returning .roster-page-leave-end{transform:translateX(22px) perspective(1200px) rotateY(-12deg) scale(.99)}

        /* Ferimentos: manchas orgânicas em vermelho e roxo, sem riscos sobre o personagem. */
        .roster-wound-aura,.roster-wound-fissures,.roster-wound-sparks{
            position:absolute;inset:0;pointer-events:none
        }
        .roster-wound-aura{
            z-index:3;opacity:0;
            background:
                radial-gradient(ellipse 27% 42% at 9% 34%,rgba(151,34,49,.56),rgba(126,21,38,.20) 53%,transparent 100%),
                radial-gradient(ellipse 30% 46% at 88% 61%,rgba(158,28,44,.54),rgba(117,20,35,.18) 54%,transparent 100%),
                radial-gradient(ellipse 64% 15% at 49% 100%,rgba(135,33,58,.28),transparent 92%),
                radial-gradient(ellipse 58% 12% at 52% 0%,rgba(111,26,52,.16),transparent 94%);
            filter:blur(7px) saturate(1.08);mix-blend-mode:multiply;
            -webkit-mask-image:radial-gradient(ellipse 95% 84% at 50% 50%,transparent 37%,#000 92%);
            mask-image:radial-gradient(ellipse 95% 84% at 50% 50%,transparent 37%,#000 92%);
        }
        .roster-wound-fissures{
            z-index:4;opacity:0;
            background:
                radial-gradient(ellipse 18% 14% at 12% 28%,rgba(106,31,78,.42),rgba(106,31,78,.10) 58%,transparent 100%),
                radial-gradient(ellipse 24% 17% at 83% 56%,rgba(127,39,88,.36),rgba(127,39,88,.08) 59%,transparent 100%),
                radial-gradient(ellipse 23% 12% at 16% 76%,rgba(145,45,67,.35),rgba(145,45,67,.08) 58%,transparent 100%),
                radial-gradient(ellipse 20% 12% at 85% 82%,rgba(102,30,72,.34),rgba(102,30,72,.08) 60%,transparent 100%);
            filter:blur(3px) saturate(1.14);mix-blend-mode:multiply;
            -webkit-mask-image:radial-gradient(ellipse 95% 84% at 50% 50%,transparent 44%,#000 93%);
            mask-image:radial-gradient(ellipse 95% 84% at 50% 50%,transparent 44%,#000 93%);
        }
        .roster-wound-sparks{display:none}
        .roster-wound-sparks i{display:none}
        .roster-wound-sparks i:nth-child(1),.roster-wound-sparks i:nth-child(2),.roster-wound-sparks i:nth-child(3),.roster-wound-sparks i:nth-child(4){display:none}
        .character-roster-card.is-wounded .roster-wound-aura{
            animation:rosterWoundBreath 3.8s ease-in-out infinite
        }
        .character-roster-card.is-wounded .roster-wound-fissures{opacity:.34}
                .character-roster-card.is-critical .roster-wound-aura{
            animation:rosterWoundHeartbeat 2.6s ease-in-out infinite
        }
        .character-roster-card.is-critical .roster-wound-fissures{
            animation:rosterFracturePulse 2.6s ease-in-out infinite
        }
                .character-roster-card.is-down:not(.is-dead) .roster-wound-aura{
            filter:blur(8px) saturate(.65);animation:rosterDownFade 4.8s ease-in-out infinite
        }
        .character-roster-card.is-down:not(.is-dead) .roster-wound-fissures{opacity:.20;filter:saturate(.65)}
        .character-roster-card.is-wounded .character-roster-hp-track{border-color:rgba(171,85,66,.28)}
        .character-roster-card.is-critical .character-roster-hp-track{
            border-color:rgba(170,55,51,.35);animation:rosterHealthBeat 2.6s ease-in-out infinite
        }
        .character-roster-card.is-critical .character-roster-stat-pv{border-color:rgba(169,63,54,.36)}
        @keyframes rosterWoundBreath{
            0%,100%{opacity:.30}48%{opacity:.68}65%{opacity:.46}
        }
        @keyframes rosterWoundHeartbeat{
            0%,10%,38%,61%,100%{opacity:.38}
            18%{opacity:.93}27%{opacity:.50}45%{opacity:.81}55%{opacity:.41}
        }
        @keyframes rosterFracturePulse{
            0%,10%,37%,61%,100%{opacity:.18}
            18%{opacity:.63}45%{opacity:.51}
        }
        @keyframes rosterSparkFaint{0%,100%{opacity:.03}50%{opacity:.24}}
        @keyframes rosterSparkHeartbeat{
            0%,10%,38%,61%,100%{opacity:.06}
            18%{opacity:.66}45%{opacity:.45}
        }
        @keyframes rosterDownFade{0%,100%{opacity:.12}50%{opacity:.32}}
        @keyframes rosterHealthBeat{
            0%,10%,38%,61%,100%{box-shadow:inset 0 1px 2px rgba(72,37,22,.06)}
            18%,45%{box-shadow:inset 0 1px 2px rgba(72,37,22,.06),0 0 8px rgba(174,45,43,.22)}
        }
        @media(max-width:1080px){
            .character-roster-card{min-height:424px}
            .character-roster-tabrail{top:50%;right:-49px;width:108px;
                -webkit-clip-path:inset(0 0 0 59px);clip-path:inset(0 0 0 59px)}
            .character-roster-tab{height:79px}
            .character-roster-panel-shell.is-detail .character-roster-panel-stage{min-height:310px;height:310px}
        }
        @media(max-width:820px){
            .character-roster-card{min-height:464px}
            .character-roster-tabrail{top:50%;right:-46px;width:104px;
                -webkit-clip-path:inset(0 0 0 58px);clip-path:inset(0 0 0 58px)}
            .character-roster-tab-actions{gap:5px}
            .character-roster-tab{height:76px;padding-left:63px;gap:4px;font-size:7.5px;margin:0}
            .character-roster-tab svg{width:12px;height:12px}
            .character-roster-panel-stage{height:276px;min-height:276px;max-height:276px}
            .character-roster-panel-shell.is-detail .character-roster-panel-stage{min-height:330px;height:330px}
        }
        @media(max-width:600px){
            .character-roster-card{min-height:0}
            .character-roster-panel-shell{position:relative;margin-top:9px}
            /* Em celulares a faixa volta para dentro da pagina, sem recortes. */
            .character-roster-tabrail{
                position:relative;top:auto;right:auto;width:100%;transform:none;
                flex-direction:row;align-items:center;min-height:37px;margin:0 0 11px;padding:0;
                border:0;border-bottom:1px solid rgba(161,116,77,.15);
                -webkit-clip-path:none;clip-path:none;overflow:visible
            }
            .character-roster-tabrail::before{display:none}
            .character-roster-tab-actions{flex-direction:row;align-items:center;gap:7px;flex-wrap:wrap}
            .character-roster-tab,
            .character-roster-tab:nth-child(1),.character-roster-tab:nth-child(2),
            .character-roster-tab:nth-child(3),.character-roster-tab:nth-child(4){
                width:auto;height:32px;min-width:0;padding:0 11px;gap:7px;flex-direction:row;margin:0;
                border:1px solid rgba(147,91,56,.30);border-radius:7px 7px 3px 3px;
                -webkit-clip-path:none;clip-path:none;
                font-size:9px;box-shadow:0 2px 4px rgba(71,38,20,.06)
            }
            .character-roster-tab::before{content:none}
            .character-roster-tab::after{display:none}
            .character-roster-tab-label{writing-mode:horizontal-tb}
            .character-roster-tab:hover,.character-roster-tab:focus-visible{transform:translateY(-2px)}
            .character-roster-tab.is-active{transform:translateY(0)}
            .character-roster-tab.is-active:hover{transform:translateY(-1px)}
            .character-roster-panel-shell.is-detail .character-roster-panel-stage{min-height:312px;height:312px}
            .character-roster-panel-stage{height:242px;min-height:242px;max-height:242px}
        }
        @media(prefers-reduced-motion:reduce){
            .character-roster-tab,.roster-page-enter,.roster-page-leave{transition:none!important}
            .character-roster-tab:hover,.character-roster-tab.is-active{transform:none!important}
            .roster-wound-aura,.roster-wound-fissures,.roster-wound-sparks,
            .character-roster-hp-track{animation:none!important}
            .character-roster-card.is-wounded .roster-wound-aura{opacity:.43}
            .character-roster-card.is-critical .roster-wound-aura{opacity:.70}
            .character-roster-card.is-critical .roster-wound-fissures{opacity:.40}
            .character-roster-card.is-down:not(.is-dead) .roster-wound-aura{opacity:.22}
        }


        /* SPELLBOUND V66.2.238 — mudança de fólio em duas fases.
           A folha cobre a página anterior ANTES de trocar conteúdo (350 ms).
           Não há duas seções visíveis concorrendo pelo mesmo espaço. */
        .character-roster-turn-sheet,
        .character-roster-turn-crease{
            position:absolute;pointer-events:none;opacity:0;visibility:hidden;
            z-index:9;top:11px;bottom:58px;left:11px;right:10px
        }
        .character-roster-turn-sheet{
            border-radius:6px 8px 4px 5px;
            background:
                radial-gradient(ellipse 12% 95% at 0 50%,rgba(122,76,48,.19),transparent 100%),
                linear-gradient(97deg,#e8d2b9 0%,#fff8ec 12%,#fffdf7 57%,#f1e2cf 100%);
            box-shadow:inset 0 1px rgba(255,255,255,.82),0 0 16px rgba(85,49,29,.09);
            backface-visibility:hidden;
        }
        .character-roster-turn-crease{
            z-index:10;right:auto;left:100%;width:29px;border-radius:35% 12% 16% 35%;
            background:linear-gradient(90deg,transparent,rgba(103,67,45,.16) 24%,rgba(255,255,250,.91) 51%,rgba(188,143,99,.16) 72%,transparent);
            filter:blur(1px)
        }
        .character-roster-body.is-leaf-turning .character-roster-turn-sheet{
            visibility:visible;animation:rosterTurnForward .72s ease-in-out both
        }
        .character-roster-body.is-leaf-turning .character-roster-turn-crease{
            visibility:visible;animation:rosterTurnFoldForward .72s ease-in-out both
        }
        .character-roster-body.is-leaf-turning.is-leaf-reverse .character-roster-turn-sheet{
            animation-name:rosterTurnBack
        }
        .character-roster-body.is-leaf-turning.is-leaf-reverse .character-roster-turn-crease{
            animation-name:rosterTurnFoldBack
        }
        @keyframes rosterTurnForward{
            0%{opacity:0;clip-path:polygon(100% 0,100% 0,100% 100%,100% 100%);transform:perspective(1000px) rotateY(-9deg)}
            5%{opacity:1}
            22%{clip-path:polygon(60% 0,100% 0,100% 100%,71% 100%)}
            43%,59%{opacity:1;clip-path:polygon(0 0,100% 0,100% 100%,0 100%);transform:perspective(1000px) rotateY(0)}
            78%{clip-path:polygon(0 0,45% 0,56% 100%,0 100%)}
            96%{opacity:1;clip-path:polygon(0 0,0 0,0 100%,0 100%);transform:perspective(1000px) rotateY(9deg)}
            100%{opacity:0;clip-path:polygon(0 0,0 0,0 100%,0 100%)}
        }
        @keyframes rosterTurnBack{
            0%{opacity:0;clip-path:polygon(0 0,0 0,0 100%,0 100%);transform:perspective(1000px) rotateY(9deg)}
            5%{opacity:1}
            22%{clip-path:polygon(0 0,40% 0,29% 100%,0 100%)}
            43%,59%{opacity:1;clip-path:polygon(0 0,100% 0,100% 100%,0 100%);transform:perspective(1000px) rotateY(0)}
            78%{clip-path:polygon(55% 0,100% 0,100% 100%,44% 100%)}
            96%{opacity:1;clip-path:polygon(100% 0,100% 0,100% 100%,100% 100%);transform:perspective(1000px) rotateY(-9deg)}
            100%{opacity:0;clip-path:polygon(100% 0,100% 0,100% 100%,100% 100%)}
        }
        @keyframes rosterTurnFoldForward{
            0%{opacity:0;left:100%}
            12%{opacity:.75}
            50%{opacity:.8;left:1%}
            82%{opacity:.47;left:-2%}
            100%{opacity:0;left:-4%}
        }
        @keyframes rosterTurnFoldBack{
            0%{opacity:0;left:0%}
            12%{opacity:.75}
            50%{opacity:.8;left:96%}
            82%{opacity:.47;left:99%}
            100%{opacity:0;left:100%}
        }
        @media(prefers-reduced-motion:reduce){
            .character-roster-turn-sheet,.character-roster-turn-crease{display:none!important}
        }

        /* SPELLBOUND V66.2.235 — cabeçalho do Grimório no mesmo ritmo do Inventário. */
        .character-roster-spellbook{isolation:isolate;gap:0;min-height:0;min-width:0}
        .character-roster-spellbook .character-roster-spellbook-header{
            display:flex;align-items:center;justify-content:space-between;
            min-height:40px;margin:0 0 8px;padding:0 1px 6px;
            gap:12px;border-bottom:1px solid rgba(147,99,64,.19)
        }
        .character-roster-spellbook .character-roster-catalog-title strong{
            font:750 18px/1.12 Georgia,serif;color:#682b21
        }
        .character-roster-spellbook-tools{display:flex;align-items:center;justify-content:flex-end;gap:13px;min-width:0}
        .character-roster-spellbook .character-roster-slots-box{z-index:30;flex:none}
        .character-roster-spellbook .character-roster-slots-toggle{
            display:inline-flex;justify-content:center;align-items:center;gap:5px;
            min-height:28px;border-radius:7px;padding:0 8px;
            background:linear-gradient(180deg,#fffaf1,#f5e8d4);
            text-transform:none;letter-spacing:0;font:750 9px/1 system-ui,sans-serif;color:#85583c
        }
        .character-roster-spellbook .character-roster-slots-toggle svg{width:13px;height:13px}
        .character-roster-spellbook .character-roster-slots-toggle:hover{border-color:rgba(142,88,53,.42);color:#652820}
        .character-roster-spellbook-switch{
            position:relative;z-index:2;flex:none;display:flex;align-items:center;gap:16px;
            min-height:31px;margin:0;border:0
        }
        .character-roster-spellbook-switch-tab{
            position:relative;display:inline-flex;align-items:center;gap:5px;justify-content:center;
            min-height:31px;border:0;padding:0 2px;background:none;box-shadow:none;cursor:pointer;
            font:800 9px/1 system-ui,sans-serif;letter-spacing:.045em;text-transform:uppercase;
            color:#9f8066;white-space:nowrap;transition:color .22s ease
        }
        .character-roster-spellbook-switch-tab::after{
            content:'';position:absolute;bottom:-5px;left:0;right:0;height:2px;border-radius:3px;
            transform:scaleX(.25);opacity:0;transform-origin:center;
            background:linear-gradient(90deg,#9e6648,#c69a65);
            transition:transform .32s cubic-bezier(.16,1,.3,1),opacity .22s ease
        }
        .character-roster-spellbook-switch-tab:hover,.character-roster-spellbook-switch-tab:focus-visible{color:#692d23;outline-offset:2px}
        .character-roster-spellbook-switch-tab.is-active{color:#64261d}
        .character-roster-spellbook-switch-tab.is-active::after{transform:scaleX(1);opacity:1}
        .character-roster-spellbook-switch-tab em{
            display:grid;place-items:center;min-width:18px;height:17px;padding:0 5px;
            border-radius:5px;background:rgba(168,120,76,.095);color:#936a4a;
            font:800 10px/1 Georgia,serif;font-style:normal;letter-spacing:0
        }
        .character-roster-spellbook-switch-tab.is-active em{background:rgba(162,96,61,.14);color:#6b2a20}
        .character-roster-spellbook-pages{
            position:relative;min-height:0;flex:1;overflow:hidden;
        }
        .character-roster-panel-shell.is-detail .character-roster-spellbook-pages > .character-roster-spellbook-list,
        .character-roster-spellbook-pages > .character-roster-spellbook-list{
            position:absolute;inset:0;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));
            grid-auto-rows:minmax(66px,max-content);align-content:start;gap:8px;
            min-height:0;max-height:none;height:100%;overflow-y:auto;
            padding:2px 6px 8px 0;overscroll-behavior:contain;
        }
        .character-roster-spellbook-list .character-roster-spell-entry{
            position:relative;min-height:66px;padding:6px 9px 6px 6px;gap:11px;
            border-radius:9px;border:1px solid rgba(160,112,76,.23);
            background:linear-gradient(124deg,rgba(255,251,244,.98),rgba(245,231,212,.86));
            box-shadow:inset 0 1px 0 rgba(255,253,249,.9),0 1px 3px rgba(75,43,27,.035);
            transition:border-color .2s ease,background .2s ease,box-shadow .2s ease;
        }
        .character-roster-spellbook-list .character-roster-spell-entry:hover{
            border-color:rgba(146,98,64,.44);background:linear-gradient(124deg,#fffaf1,#f2e0c8);
            box-shadow:0 2px 7px rgba(77,47,32,.085),inset 0 1px 0 rgba(255,255,255,.78);
        }
        .character-roster-spellbook-list .character-roster-spell-thumb{
            width:53px;height:53px;border-radius:7px;border-color:rgba(145,104,82,.26);
        }
        .character-roster-spellbook-list .character-roster-entry-info{gap:7px;justify-content:center}
        .character-roster-spellbook-list .character-roster-entry-info strong{
            font:700 13px/1.22 Georgia,serif;color:#673a29;letter-spacing:-.01em;
            -webkit-line-clamp:2;
        }
        .character-roster-spellbook-list .character-roster-spell-level{
            font:800 9px/1.1 system-ui,sans-serif;letter-spacing:.065em;
            color:#9a7454;text-transform:uppercase;
        }
        .character-roster-spellbook-list .character-roster-spell-thumb.cantrip{
            background:radial-gradient(ellipse at 35% 20%,#e7f0ea,#bfd4c5);color:#597f6b;
        }
        .roster-spell-enter{z-index:2;transition:opacity .26s ease,transform .35s cubic-bezier(.16,1,.3,1)}
        .roster-spell-enter-start{opacity:0;transform:translateX(14px) scale(.99)}
        .roster-spell-enter-end{opacity:1;transform:translateX(0) scale(1)}
        .roster-spell-leave{z-index:1;transition:opacity .13s ease,transform .23s ease}
        .roster-spell-leave-start{opacity:1;transform:translateX(0) scale(1)}
        .roster-spell-leave-end{opacity:0;transform:translateX(-10px) scale(.995)}
        .character-roster-spellbook-pages.is-returning .roster-spell-enter-start{transform:translateX(-14px) scale(.99)}
        .character-roster-spellbook-pages.is-returning .roster-spell-leave-end{transform:translateX(10px) scale(.995)}
        @media(max-width:820px){
            .character-roster-spellbook-pages > .character-roster-spellbook-list,
            .character-roster-panel-shell.is-detail .character-roster-spellbook-pages > .character-roster-spellbook-list{grid-template-columns:minmax(0,1fr)}
            .character-roster-spellbook .character-roster-spellbook-header{gap:8px}
            .character-roster-spellbook-switch{gap:11px}
        }
        @media(max-width:600px){
            .character-roster-spellbook-switch{gap:12px}
            .character-roster-spellbook .character-roster-spellbook-header{flex-wrap:wrap;row-gap:4px}
            .character-roster-spellbook-tools{gap:10px}
            .character-roster-spellbook-pages > .character-roster-spellbook-list,
            .character-roster-panel-shell.is-detail .character-roster-spellbook-pages > .character-roster-spellbook-list{grid-template-columns:repeat(2,minmax(0,1fr))}
        }
        @media(max-width:420px){
            .character-roster-spellbook-pages > .character-roster-spellbook-list,
            .character-roster-panel-shell.is-detail .character-roster-spellbook-pages > .character-roster-spellbook-list{grid-template-columns:minmax(0,1fr)}
            .character-roster-spellbook-list .character-roster-spell-entry{min-height:59px}
            .character-roster-spellbook-list .character-roster-spell-thumb{width:45px;height:45px}
        }
        @media(prefers-reduced-motion:reduce){
            .roster-spell-enter,.roster-spell-leave{transition:none!important}
            .roster-page-enter-start,.roster-page-enter-end,.roster-page-leave-start,.roster-page-leave-end,
            .roster-spell-enter-start,.roster-spell-enter-end,.roster-spell-leave-start,.roster-spell-leave-end{
                transform:none!important;filter:none!important
            }
            .roster-spell-enter-start,.roster-spell-leave-end{transform:none!important}
        }

        /* SPELLBOUND V66.2.239 — acabamento da CA, abas e altura fixa.
           Cada uma das quatro páginas usa a MESMA caixa externa; apenas
           a área interna de leitura e suas listas podem rolar. */
        .character-roster-ac-value{top:27%;gap:3px}
        .character-roster-ac-value small{font-size:7.5px;letter-spacing:.08em}
        .character-roster-ac-value strong{font-size:19px;line-height:1.05;font-weight:750}
        .character-roster-ac-shield.is-triple .character-roster-ac-value strong{font-size:15px}

        /* Etiquetas de pasta quase totalmente guardadas atrás da moldura. */
        .character-roster-tabrail{
            width:101px;right:-35px;top:50%;transform:translateY(-50%);
            -webkit-clip-path:inset(0 0 0 66px);clip-path:inset(0 0 0 66px)
        }
        .character-roster-tab-actions{gap:4px}
        .character-roster-tab,
        .character-roster-tab:nth-child(1),.character-roster-tab:nth-child(2),
        .character-roster-tab:nth-child(3),.character-roster-tab:nth-child(4){
            box-sizing:border-box;width:90px;height:90px;min-height:0;
            padding:7px 4px 7px 65px;gap:5px;
            border-radius:0 9px 9px 0;
            font-size:10px;letter-spacing:.008em;line-height:1;font-weight:800;
            background:linear-gradient(90deg,#d6b89a 0%,#e9d5bc 63%,#fcf3e4 100%);
            box-shadow:inset -1px 0 rgba(144,94,59,.09),0 2px 5px rgba(76,42,25,.07)
        }
        .character-roster-tab svg{width:13px;height:13px;flex:none}
        .character-roster-tab-label{font-size:inherit;letter-spacing:.012em}
        .character-roster-tab:hover,.character-roster-tab:focus-visible{transform:translateX(12px)}
        .character-roster-tab.is-active{transform:translateX(6px)}
        .character-roster-tab.is-active:hover,.character-roster-tab.is-active:focus-visible{transform:translateX(13px)}

        /* Altura externa idêntica no Resumo, Inventário, Diário e Grimório.
           O grid não pode crescer por causa de algum painel com mais itens. */
        .character-roster-card{
            height:410px;min-height:410px;grid-template-rows:minmax(0,1fr)
        }
        .character-roster-portrait{height:100%;min-height:0}
        .character-roster-body{height:100%;min-height:0}
        .character-roster-heading{flex-shrink:0}
        .character-roster-panel-shell{flex:1 1 0;min-height:0}
        .character-roster-panel-shell .character-roster-panel-stage,
        .character-roster-panel-shell.is-detail .character-roster-panel-stage{
            display:grid;grid-template-columns:minmax(0,1fr);grid-template-rows:minmax(0,1fr);
            flex:1 1 0;min-height:0;height:auto;max-height:none;overflow:hidden
        }
        .character-roster-panel-stage>.character-roster-panel{min-height:0;max-height:100%}
        .character-roster-panel-shell.is-detail .character-roster-catalog-list,
        .character-roster-panel-shell.is-detail .character-roster-diary-list{
            flex:1 1 auto;min-height:0;max-height:none;overflow-y:auto;overscroll-behavior:contain
        }
        .character-roster-footer{flex-shrink:0}
        .character-roster-xp{flex-shrink:0}

        @media(max-width:1080px){
            .character-roster-card{height:424px;min-height:424px}
            .character-roster-tabrail{
                width:99px;right:-34px;
                -webkit-clip-path:inset(0 0 0 65px);clip-path:inset(0 0 0 65px)
            }
            .character-roster-tab,.character-roster-tab:nth-child(1),
            .character-roster-tab:nth-child(2),.character-roster-tab:nth-child(3),
            .character-roster-tab:nth-child(4){width:89px;height:88px;padding-left:64px;font-size:9.8px}
        }
        @media(max-width:820px){
            .character-roster-card{height:464px;min-height:464px}
            .character-roster-tabrail{
                width:96px;right:-33px;
                -webkit-clip-path:inset(0 0 0 63px);clip-path:inset(0 0 0 63px)
            }
            .character-roster-tab,.character-roster-tab:nth-child(1),
            .character-roster-tab:nth-child(2),.character-roster-tab:nth-child(3),
            .character-roster-tab:nth-child(4){width:87px;height:84px;padding-left:62px;font-size:9.4px;gap:4px}
        }
        @media(max-width:600px){
            .character-roster-card{
                height:730px;min-height:730px;
                grid-template-rows:284px minmax(0,1fr)
            }
            .character-roster-portrait{height:284px;min-height:284px}
            .character-roster-body{min-height:0;height:100%}
            .character-roster-tabrail{
                position:relative;top:auto;right:auto;width:100%;transform:none;
                -webkit-clip-path:none;clip-path:none
            }
            .character-roster-tab,.character-roster-tab:nth-child(1),
            .character-roster-tab:nth-child(2),.character-roster-tab:nth-child(3),
            .character-roster-tab:nth-child(4){
                width:auto;height:32px;min-height:0;padding:0 11px;gap:7px;
                border-radius:7px 7px 3px 3px;font-size:10px;letter-spacing:.02em;
                background:linear-gradient(180deg,#fff9ef,#f1e3d0)
            }
            .character-roster-tab-label{writing-mode:horizontal-tb}
            .character-roster-tab:hover,.character-roster-tab:focus-visible{transform:translateY(-2px)}
            .character-roster-tab.is-active{transform:translateY(0)}
            .character-roster-tab.is-active:hover,.character-roster-tab.is-active:focus-visible{transform:translateY(-1px)}
        }
        @media(max-width:420px){
            .character-roster-card{height:775px;min-height:775px}
            .character-roster-tab,.character-roster-tab:nth-child(1),
            .character-roster-tab:nth-child(2),.character-roster-tab:nth-child(3),
            .character-roster-tab:nth-child(4){font-size:9px;padding:0 8px;gap:5px}
        }

        /* ================================================================
           SPELLBOUND V66.2.242 — Criação de personagens em tela ampla
           Mantém o índice e os cards existentes inalterados.
        ================================================================= */
        .sb-create-overlay{position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:14px;color:#532b21}
        .sb-create-backdrop{position:absolute;inset:0;background:rgba(37,24,18,.72);backdrop-filter:blur(6px)}
        .sb-create-window{position:relative;z-index:1;display:flex;flex-direction:column;overflow:hidden;width:min(1760px,calc(100vw - 28px));height:min(1050px,calc(100dvh - 28px));border:1px solid #c7a988;border-radius:19px;background:linear-gradient(135deg,#fdfaf4,#f3e9db 90%);box-shadow:0 24px 75px rgba(27,13,8,.38),inset 0 0 0 3px rgba(255,255,255,.44)}
        .sb-create-head{display:flex;flex:none;align-items:center;justify-content:space-between;gap:12px;min-height:52px;padding:7px clamp(16px,2.3vw,30px);border-bottom:1px solid rgba(170,125,89,.25);background:linear-gradient(90deg,#fffaf2,#f4e8d8)}
        .sb-create-heading{display:flex;align-items:baseline;gap:18px;min-width:0}
        .sb-create-heading h2{margin:0;font:750 clamp(21px,2vw,28px)/1.15 Georgia,serif;color:#601f18;white-space:nowrap}
        .sb-create-heading>span{font:750 9px/1.2 system-ui,sans-serif;color:#a17a5c;letter-spacing:.15em;text-transform:uppercase}
        .sb-create-close{display:flex;flex:none;align-items:center;justify-content:center;width:34px;height:34px;border:1px solid rgba(150,104,70,.25);border-radius:9px;background:rgba(255,253,249,.65);color:#875b43;transition:background .18s,color .18s}
        .sb-create-close:hover,.sb-create-close:focus-visible{background:#f0d9c3;color:#62231b}
        .sb-create-close svg{width:17px;height:17px}
        .sb-create-tabs{display:flex;flex:none;gap:4px;align-items:center;overflow-x:auto;scrollbar-width:none;white-space:nowrap;padding:4px clamp(16px,2.3vw,30px) 0;border-bottom:1px solid rgba(166,119,81,.25);background:#f8f1e6}
        .sb-create-tabs::-webkit-scrollbar{display:none}
        .sb-create-tabs button{flex:none}
        .sb-create-tabs button{display:flex;align-items:center;gap:8px;min-height:36px;padding:0 14px 6px;border:0;border-bottom:2px solid transparent;background:transparent;font:800 10px/1 system-ui,sans-serif;letter-spacing:.04em;color:#977357;transition:color .2s,border-color .2s,background .2s}
        .sb-create-tabs button:hover{color:#64271c;background:rgba(155,104,62,.04)}
        .sb-create-tabs button.is-active{color:#64251c;border-bottom-color:#a66b42}
        .sb-create-step{display:flex;width:22px;height:22px;align-items:center;justify-content:center;border:1px solid rgba(159,116,78,.3);border-radius:50%;font:800 8px/1 system-ui,sans-serif;background:rgba(255,252,244,.78);color:inherit}
        .sb-create-tabs button.is-active .sb-create-step{border-color:#9b6748;background:#f5e1ca}
        .sb-create-form{display:flex;flex-direction:column;flex:1;min-height:0}
        .sb-create-content{flex:1;min-height:0;overflow-y:auto;padding:clamp(16px,2vw,27px) clamp(16px,2.3vw,30px);scrollbar-width:thin;scrollbar-color:#c2a183 transparent}
        .sb-create-view{max-width:1500px;margin:0 auto;animation:sb-create-page .26s cubic-bezier(.2,.8,.2,1) both}
        @keyframes sb-create-page{from{opacity:0;transform:translateY(7px)}to{opacity:1;transform:translateY(0)}}
        .sb-create-section-head{display:flex;justify-content:space-between;align-items:flex-start;gap:22px;margin:0 0 17px;padding-bottom:10px;border-bottom:1px solid rgba(160,115,76,.25)}
        .sb-create-section-head small{font:850 9px/1 system-ui,sans-serif;color:#ac7c56;letter-spacing:.2em}
        .sb-create-section-head h3{margin:5px 0 4px;font:750 clamp(23px,2vw,30px)/1.1 Georgia,serif;color:#64261d}
        .sb-create-section-head p{font:500 12px/1.5 system-ui,sans-serif;color:#97735b}
        .sb-create-section-folio{font:700 12px/1 Georgia,serif;color:#b29675;white-space:nowrap;margin-top:12px}
        .sb-create-identity-grid{display:grid;grid-template-columns:minmax(230px,310px) minmax(0,1fr);align-items:start;gap:clamp(18px,3vw,38px)}
        .sb-create-portrait-pane{min-width:0}
        .sb-create-portrait-preview{position:relative;overflow:hidden;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%;height:min(48vh,460px);min-height:290px;border:7px solid #b48468;border-radius:16px;background:radial-gradient(circle at 48% 25%,#e5c7b5,#b88a70 90%);box-shadow:0 10px 25px rgba(91,54,35,.15),inset 0 0 0 1px rgba(255,250,237,.44)}
        .sb-create-portrait-preview>img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center top}
        .sb-create-empty-portrait{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:11px;color:#815744}
        .sb-create-empty-portrait span{font:750 9px/1.2 system-ui,sans-serif;letter-spacing:.11em;text-transform:uppercase}
        .sb-create-portrait-name{position:absolute;z-index:1;bottom:0;left:0;right:0;display:flex;flex-direction:column;gap:3px;padding:26px 16px 15px;background:linear-gradient(transparent,rgba(51,30,23,.88));color:#fffaf0}
        .sb-create-portrait-name strong{font:750 21px/1.2 Georgia,serif;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .sb-create-portrait-name small{font:700 10px/1.2 system-ui,sans-serif;color:#f4dfca;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .sb-create-photo-button{display:flex;align-items:center;justify-content:center;gap:9px;cursor:pointer;margin-top:14px;min-height:40px;border:1px solid #d3b89c;border-radius:10px;background:#fff9f0;font:800 10px/1 system-ui,sans-serif;letter-spacing:.03em;color:#6d2e22;transition:background .2s}
        .sb-create-photo-button:hover{background:#f0ddc7}
        .sb-create-photo-button svg{width:16px;height:16px}
        .sb-create-photo-button input{position:absolute;width:1px;height:1px;opacity:0;overflow:hidden}
        .sb-create-photo-help{text-align:center;margin:9px 6px;color:#a1856f;font:500 10px/1.5 system-ui,sans-serif}
        .sb-create-identity-fields{min-width:0}
        .sb-create-subsection{margin-bottom:15px}
        .sb-create-subsection h4{font:750 20px/1.2 Georgia,serif;color:#6e3224}
        .sb-create-subsection p{margin-top:3px;font:500 11px/1.4 system-ui,sans-serif;color:#9e7b61}
        .sb-create-field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 17px}
        .sb-create-field{display:flex;flex-direction:column;gap:6px;min-width:0;color:#744830}
        .sb-create-field>span{font:850 9px/1.15 system-ui,sans-serif;letter-spacing:.1em;text-transform:uppercase}
        .sb-create-field>span b{color:#bd7253}
        .sb-create-field input,.sb-create-field textarea{display:block;width:100%;max-width:100%;min-width:0;min-height:43px;padding:10px 13px;border:1px solid #d8c2ac;border-radius:9px;background:#fffdf9;color:#532b21;font:600 13px/1.45 system-ui,sans-serif;box-shadow:inset 0 1px 0 #fff,0 2px 4px rgba(93,54,34,.03);transition:border-color .18s,box-shadow .18s}
        .sb-create-field textarea{resize:vertical;min-height:74px}
        .sb-create-field input::placeholder,.sb-create-field textarea::placeholder{color:#ad9584;font-weight:400}
        .sb-create-field input:focus,.sb-create-field textarea:focus{outline:none;border-color:#a16c4d;box-shadow:0 0 0 3px rgba(161,108,77,.13)}
        .sb-create-span-2{grid-column:span 2}
        .sb-create-optional-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-top:21px}
        .sb-create-optional-card{min-width:0;align-self:start;overflow:hidden;border:1px solid #e0ccb4;border-radius:12px;background:linear-gradient(160deg,#fffaf2,#f5e9d9);padding:16px}
        .sb-create-optional-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
        .sb-create-optional-kicker{display:block;font:800 8px/1.2 system-ui,sans-serif;letter-spacing:.11em;color:#ae805f}
        .sb-create-optional-card h4{margin-top:4px;font:750 16px/1.2 Georgia,serif;color:#6b3022}
        .sb-create-option-toggle{flex:none;border:1px solid #d9bfa6;border-radius:999px;padding:8px 10px;background:#fffbf5;font:800 9px/1 system-ui,sans-serif;color:#814b34;white-space:nowrap}
        .sb-create-option-toggle:hover{border-color:#aa7b55;background:#f3e2ce}
        .sb-create-optional-hint{margin-top:10px;font:500 10.5px/1.55 system-ui,sans-serif;color:#9c785e}
        .sb-create-optional-inner{display:flex;flex-direction:column;gap:11px;margin-top:15px;padding-top:13px;border-top:1px solid #e6d2bd}
        .sb-create-trait-editor{display:flex;flex-direction:column;gap:9px;padding:12px;border:1px solid #e3ceba;border-radius:10px;background:#fffaf4}
        .sb-create-trait-title{display:flex;justify-content:space-between;gap:8px;align-items:center;font:800 9px/1 system-ui,sans-serif;letter-spacing:.07em;color:#91694e;text-transform:uppercase}
        .sb-create-trait-title button{border:0;background:transparent;color:#9d4939;font-size:9px}
        .sb-create-trait-title button:hover{text-decoration:underline}
        .sb-create-add-trait{align-self:flex-start;font:800 10px/1.3 system-ui,sans-serif;color:#6f3527;padding:5px 0;border:0;background:transparent}
        .sb-create-add-trait:hover{text-decoration:underline}
        .sb-create-link-note{margin-top:14px;color:#9a785c;font:500 10px/1.5 system-ui,sans-serif}
        .sb-create-link-note strong{font-weight:800;color:#784730}
        .sb-create-class-summary{display:flex;align-items:center;gap:18px;max-width:700px;margin-bottom:22px;padding:17px;border:1px solid #e1c9ad;border-radius:12px;background:#f6e9d8}
        .sb-create-class-summary>div{display:flex;flex-direction:column;gap:4px;padding-right:19px;border-right:1px solid #d4b497}
        .sb-create-class-summary small{font:800 9px/1 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.1em;color:#93694e}
        .sb-create-class-summary strong{font:750 27px/1 Georgia,serif;color:#64261e}
        .sb-create-class-summary p{font:500 11px/1.55 system-ui,sans-serif;color:#936f53}
        .sb-create-class-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px;max-width:1120px}
        .sb-create-class-card{padding:17px;border:1px solid #dfc6ad;border-radius:12px;background:#fffaf3}
        .sb-create-class-top{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:17px;color:#70412d;font:800 10px/1 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.1em}
        .sb-create-class-top button{border:0;background:transparent;color:#a14c38;font:800 9px/1 system-ui,sans-serif}
        .sb-create-class-fields{display:grid;grid-template-columns:1.1fr 1fr 85px;gap:11px}
        .sb-create-add-class{margin-top:18px;padding:10px 14px;border:1px solid #d4b89b;border-radius:9px;background:#f8eedf;color:#73372a;font:800 10px/1 system-ui,sans-serif}
        .sb-create-add-class:hover{background:#ecd9c2}
        .sb-create-settings-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px;max-width:1120px}
        .sb-create-setting{padding:21px;border:1px solid #e0c9ae;border-radius:13px;background:#fff9f1}
        .sb-create-setting-main{display:flex;align-items:flex-start;gap:12px;cursor:pointer}
        .sb-create-setting-main input{margin-top:2px;accent-color:#6b1d14;width:17px;height:17px}
        .sb-create-setting-main span{display:flex;flex-direction:column;gap:6px}
        .sb-create-setting-main strong{font:750 16px/1.2 Georgia,serif;color:#663026}
        .sb-create-setting-main small{font:500 11px/1.45 system-ui,sans-serif;color:#96735a}
        .sb-create-setting-extra{margin-top:18px;border-top:1px solid #e5d3c0;padding-top:16px}
        .sb-create-finish-note{max-width:1120px;margin-top:25px;border-left:3px solid #b68d60;padding:10px 16px;background:#f3e5d2;border-radius:0 10px 10px 0}
        .sb-create-finish-note strong{font:750 16px/1.2 Georgia,serif;color:#683027}
        .sb-create-finish-note p{margin-top:4px;font:500 11px/1.5 system-ui,sans-serif;color:#8f6b52}
        .sb-create-errors{margin:22px auto 0;max-width:1200px;border:1px solid #c98e81;background:#fff1e9;border-radius:10px;padding:13px 17px;color:#8c3425;font:600 12px/1.6 system-ui,sans-serif}
        .sb-create-errors strong{font-weight:800}
        .sb-create-errors ul{padding-left:20px;list-style:disc}
        .sb-create-back,.sb-create-next,.sb-create-submit{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:36px;padding:0 14px;border-radius:8px;font:850 10px/1 system-ui,sans-serif;letter-spacing:.035em;white-space:nowrap;transition:background .18s,transform .18s}
        .sb-create-back{border:1px solid #d1b79e;background:#fffaf3;color:#70412d}
        .sb-create-next,.sb-create-submit{border:1px solid #722c22;background:#6b251c;color:#fffaf2}
        .sb-create-back:hover{background:#ecdbc5}
        .sb-create-next:hover,.sb-create-submit:hover{background:#501b15;transform:translateY(-1px)}
        @media(max-width:1080px){
            .sb-create-identity-grid{grid-template-columns:minmax(180px,260px) minmax(0,1fr);gap:18px}
            .sb-create-optional-grid{grid-template-columns:1fr}
            .sb-create-portrait-preview{height:400px}
        }
        @media(max-width:760px){
            .sb-create-overlay{padding:0}
            .sb-create-window{width:100vw;height:100dvh;border-radius:0;border:0}
            .sb-create-head{min-height:48px;padding:6px 14px}
            .sb-create-heading>span{display:none}
            .sb-create-tabs{padding:3px 10px 0;gap:0;justify-content:space-between}
            .sb-create-tabs button{min-height:35px;padding:0 11px 6px;gap:6px;font-size:10px}
            .sb-create-step{width:22px;height:22px}
            .sb-create-content{padding:15px 15px}
            .sb-create-section-head{margin-bottom:14px}
            .sb-create-identity-grid{grid-template-columns:1fr}
            .sb-create-portrait-pane{display:grid;grid-template-columns:130px 1fr;gap:10px;align-items:center}
            .sb-create-portrait-preview{grid-row:span 2;min-height:0;height:167px;border-width:4px;border-radius:10px}
            .sb-create-portrait-name{padding:15px 9px 9px}
            .sb-create-portrait-name strong{font-size:12px}
            .sb-create-photo-button{margin-top:0}
            .sb-create-photo-help{text-align:left}
            .sb-create-optional-grid{grid-template-columns:1fr}
            .sb-create-class-list,.sb-create-settings-grid{grid-template-columns:1fr}
        }
        @media(max-width:440px){
            .sb-create-field-grid{grid-template-columns:1fr}
            .sb-create-span-2{grid-column:auto}
            .sb-create-tabs button{font-size:9px;padding-inline:5px}
            .sb-create-class-fields{grid-template-columns:1fr 1fr}
            .sb-create-class-fields .sb-create-field:first-child{grid-column:span 2}
            .sb-create-portrait-pane{grid-template-columns:110px 1fr}
            .sb-create-portrait-preview{height:146px}
        }
        /* Notebooks com pouca altura: reservar a maior parte para o formulário, não as barras. */
        @media (max-height:740px) and (min-width:761px){
            .sb-create-head{min-height:48px;padding-block:5px}
            .sb-create-tabs{padding-top:2px}
            .sb-create-tabs button{min-height:34px;padding-bottom:5px}
            .sb-create-content{padding-top:17px;padding-bottom:17px}
            .sb-create-section-head{margin-bottom:14px;padding-bottom:9px}
            .sb-create-back,.sb-create-next,.sb-create-submit{min-height:34px}
        }

        /* ================================================================
           V66.2.245 — Criar personagem: hierarquia e proporções adaptáveis.
           As alterações não afetam a ficha nem as rotas de salvamento.
        ================================================================ */
        .sb-create-overlay{padding:9px}
        .sb-create-window{
            width:min(1120px,calc(100vw - 38px));height:min(860px,calc(100dvh - 38px));
            border-radius:16px
        }
        .sb-create-content{
            padding:clamp(14px,1.55vw,22px) clamp(16px,2.15vw,29px);
            overscroll-behavior:contain
        }
        .sb-create-view{max-width:1040px}
        .sb-create-identity-heading{
            align-items:center;margin-bottom:13px;padding-bottom:9px;gap:12px
        }
        .sb-create-identity-heading h3{margin:0;font-size:clamp(22px,1.75vw,26px);line-height:1.15}
        .sb-create-identity-heading p{margin-top:4px;font-size:11px;line-height:1.3}
        .sb-create-identity-grid{
            grid-template-columns:clamp(186px,20vw,236px) minmax(0,1fr);
            align-items:start;gap:clamp(17px,2.15vw,28px)
        }
        .sb-create-portrait-preview{
            height:clamp(182px,34dvh,284px);min-height:0;border-width:5px;border-radius:13px;
            background:radial-gradient(ellipse at 44% 23%,#e8cdbb 0%,#c89c84 80%,#b88972 100%);
            box-shadow:0 7px 16px rgba(91,54,35,.12),inset 0 0 0 1px rgba(255,250,237,.40)
        }
        .sb-create-empty-portrait{gap:5px;padding:0 14px 28px;text-align:center}
        .sb-create-empty-portrait .sb-create-empty-portrait-title{
            font:700 clamp(13px,1.25vw,16px)/1.2 Georgia,serif;
            color:rgba(102,57,43,.90);letter-spacing:0;text-transform:none
        }
        .sb-create-empty-portrait .sb-create-empty-portrait-subtitle{
            font:700 9px/1.3 system-ui,sans-serif;color:rgba(115,74,58,.7);
            letter-spacing:.08em;text-transform:uppercase
        }
        .sb-create-portrait-name{padding:20px 11px 9px;gap:1px}
        .sb-create-portrait-name strong{font-size:clamp(14px,1.35vw,17px)}
        .sb-create-portrait-name small{font-size:9px}
        .sb-create-photo-button{margin-top:9px;min-height:37px;border-radius:9px;font-size:10px}
        .sb-create-photo-help{margin:6px 2px 0;font-size:9px;line-height:1.4}
        .sb-create-field-grid{gap:11px 15px}
        .sb-create-field{gap:5px}
        .sb-create-field>span{font-size:11px;letter-spacing:.06em}
        .sb-create-field input,.sb-create-field textarea{
            min-height:41px;padding:9px 12px;font-size:13px
        }
        .sb-create-optional-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:16px}
        .sb-create-optional-card{padding:12px 13px 13px;border-radius:11px}
        .sb-create-optional-kicker{font-size:9px;letter-spacing:.07em}
        .sb-create-optional-card h4{font-size:16px;margin-top:4px}
        .sb-create-option-toggle{padding:8px 11px;font-size:10px;min-height:32px}
        .sb-create-optional-hint{margin-top:7px;line-height:1.43;font-size:11px}
        .sb-create-optional-inner{margin-top:11px;padding-top:10px}
        .sb-create-link-note{margin-top:9px;font-size:10px;line-height:1.4}

        /* Telas com pouca altura: priorizar a leitura dos campos e a foto inteira. */
        @media (max-height:700px) and (min-width:761px){
            .sb-create-head{min-height:48px}
            .sb-create-content{padding-top:12px;padding-bottom:12px}
            .sb-create-identity-heading{margin-bottom:11px;padding-bottom:8px}
            .sb-create-identity-heading h3{font-size:22px}
            .sb-create-identity-grid{grid-template-columns:clamp(178px,19vw,226px) minmax(0,1fr);gap:17px}
            .sb-create-portrait-preview{height:clamp(175px,34dvh,220px)}
            .sb-create-photo-button{min-height:34px;margin-top:7px}
            .sb-create-field-grid{gap:9px 13px}
            .sb-create-field input{min-height:38px;padding-block:7px}
            .sb-create-optional-grid{margin-top:12px;gap:10px}
            .sb-create-optional-card{padding:10px 11px}
            .sb-create-optional-card h4{font-size:14px}
            .sb-create-optional-hint{margin-top:5px;font-size:11px}
        }
        /* Notebooks intermediários: evitar empilhar caixas desnecessariamente. */
        @media (min-width:761px) and (max-width:1080px){
            .sb-create-identity-grid{grid-template-columns:clamp(172px,22vw,225px) minmax(0,1fr);gap:16px}
            .sb-create-optional-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
            .sb-create-option-toggle{padding-inline:8px;font-size:9px}
            .sb-create-optional-card h4{font-size:14px}
        }
        @media(max-width:760px){
            .sb-create-overlay{padding:0}
            .sb-create-window{width:100vw;height:100dvh;border:0;border-radius:0}
            .sb-create-content{padding:15px 15px 18px}
            .sb-create-identity-heading{margin-bottom:13px;padding-bottom:9px}
            .sb-create-identity-heading h3{font-size:23px}
            .sb-create-identity-heading p{font-size:11px}
            .sb-create-identity-grid{grid-template-columns:1fr;gap:17px}
            .sb-create-portrait-pane{
                display:grid;grid-template-columns:126px minmax(0,1fr);
                grid-template-rows:auto auto;align-items:center;gap:6px 12px
            }
            .sb-create-portrait-preview{
                grid-column:1;grid-row:1/3;height:145px;min-height:0;border-width:4px;border-radius:11px
            }
            .sb-create-empty-portrait{gap:3px;padding:0 5px 24px}
            .sb-create-empty-portrait .sb-create-empty-portrait-title{font-size:13px}
            .sb-create-empty-portrait .sb-create-empty-portrait-subtitle{font-size:8px}
            /* Na miniatura móvel, o nome está nos campos: não sobrepor texto à foto. */
            .sb-create-portrait-name{display:none}
            .sb-create-empty-portrait .sb-create-empty-portrait-subtitle{display:none}
            .sb-create-photo-button{grid-column:2;grid-row:1;min-height:39px;margin:0;padding:0 10px}
            .sb-create-photo-help{grid-column:2;grid-row:2;margin:0;font-size:10px;line-height:1.45;text-align:left}
            .sb-create-field-grid{gap:11px 12px}
            .sb-create-optional-grid{grid-template-columns:1fr;gap:10px;margin-top:16px}
            .sb-create-optional-card{padding:12px 13px}
        }
        @media (max-width:440px){
            .sb-create-portrait-pane{grid-template-columns:112px minmax(0,1fr);gap:6px 10px}
            .sb-create-portrait-preview{height:133px}
            .sb-create-empty-portrait .sb-create-empty-portrait-title{font-size:12px}
            .sb-create-photo-button{font-size:9px}
            .sb-create-optional-card h4{font-size:15px}
        }
        @media (max-width:370px){
            .sb-create-portrait-pane{grid-template-columns:102px minmax(0,1fr)}
            .sb-create-portrait-preview{height:128px}
            .sb-create-identity-heading p{font-size:10px}
        }

        @media(prefers-reduced-motion:reduce){.sb-create-view{animation:none}.sb-create-back,.sb-create-next,.sb-create-submit{transition:none}}

        /* V66.2.246 — modal proporcional, sem rodapé fixo; navegação no cabeçalho. */
        .sb-create-head-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex:none;min-width:0}
        .sb-create-head-actions .sb-create-back,
        .sb-create-head-actions .sb-create-next,
        .sb-create-head-actions .sb-create-submit{
            min-height:33px;padding:0 12px;border-radius:8px;font-size:10px;line-height:1;
            letter-spacing:.01em;box-shadow:none
        }
        .sb-create-head-actions .sb-create-back{background:#fffcf7}
        .sb-create-head-actions .sb-create-next,
        .sb-create-head-actions .sb-create-submit{background:#70291f;color:#fffaf2}
        .sb-create-action-symbol{font-size:14px;line-height:1}
        .sb-create-head-back .sb-create-action-symbol{display:none}
        .sb-create-head-submit .sb-create-action-symbol{display:none}
        .sb-create-form{min-height:0;flex:1}
        .sb-create-content{padding-bottom:clamp(16px,2vw,26px)}
        @media(min-width:761px) and (max-width:940px){
            .sb-create-optional-grid{grid-template-columns:1fr}
            .sb-create-window{width:calc(100vw - 28px)}
        }
        @media(max-width:760px){
            .sb-create-overlay{padding:0}
            .sb-create-window{width:100vw;height:100dvh;border-radius:0}
            .sb-create-head-actions{gap:6px}
            .sb-create-head-actions .sb-create-back,
            .sb-create-head-actions .sb-create-next,
            .sb-create-head-actions .sb-create-submit{min-height:32px;padding:0 10px}
        }
        @media(max-width:440px){
            .sb-create-head{gap:7px;padding-inline:12px}
            .sb-create-heading h2{font-size:clamp(19px,5.4vw,23px)}
            .sb-create-head-actions{gap:5px}
            .sb-create-head-actions .sb-create-back,
            .sb-create-head-actions .sb-create-next,
            .sb-create-head-actions .sb-create-submit{width:33px;min-width:33px;min-height:33px;padding:0}
            .sb-create-head-actions .sb-create-action-label{display:none}
            .sb-create-head-actions .sb-create-action-symbol{display:block;font-size:17px}
            .sb-create-head-actions .sb-create-close{width:33px;height:33px}
        }

        /* ================================================================
           V66.2.247 — Identidade centralizada e origens progressivas.
           Não altera o estilo dos cards do índice.
        ================================================================ */
        .sb-create-identity-grid{
            display:flex;flex-direction:column;align-items:stretch;gap:15px;
            width:100%;max-width:950px;margin:0 auto
        }
        .sb-create-portrait-pane{
            width:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;
            gap:0;margin:0 auto;min-width:0
        }
        .sb-create-portrait-preview{
            flex:none;width:154px;height:170px;min-height:0;max-width:100%;
            border-width:4px;border-radius:11px;
            box-shadow:0 5px 14px rgba(91,54,35,.13),inset 0 0 0 1px rgba(255,250,237,.46)
        }
        .sb-create-portrait-name{padding:17px 9px 8px}
        .sb-create-portrait-name strong{font-size:13px}
        .sb-create-portrait-name small{font-size:8px}
        .sb-create-empty-portrait{padding:0 9px 11px}
        .sb-create-empty-portrait .sb-create-empty-portrait-title{font-size:13px}
        .sb-create-photo-button{flex:none;width:190px;max-width:100%;min-height:31px;margin-top:7px}
        .sb-create-photo-help{margin:4px 0 0;text-align:center;font-size:9px}
        .sb-create-identity-fields{width:100%;max-width:950px;margin:0 auto}
        .sb-create-field-grid{gap:11px 14px}
        .sb-create-optional-grid{grid-template-columns:repeat(2,minmax(0,1fr));margin-top:15px;gap:12px}
        .sb-create-optional-card{padding:12px 14px}
        .sb-create-proficiencies-card{margin-top:12px}
        .sb-create-proficiency-count{display:inline-flex;margin-top:8px;font:750 9px/1.2 system-ui,sans-serif;color:#8c6346}
        .sb-create-proficiency-editor{gap:8px}
        .sb-create-proficiency-group{border:1px solid #e4ceba;border-radius:8px;background:rgba(255,253,248,.73);overflow:hidden}
        .sb-create-proficiency-group summary{cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:10px;
            padding:9px 11px;color:#6d3327;font:750 11px/1.25 Georgia,serif;list-style:none}
        .sb-create-proficiency-group summary::-webkit-details-marker{display:none}
        .sb-create-proficiency-group summary::after{content:'+';font:750 15px/1 system-ui,sans-serif;color:#9a6e50}
        .sb-create-proficiency-group[open] summary::after{content:'−'}
        .sb-create-proficiency-group summary small{margin-left:auto;font:600 9px/1.3 system-ui,sans-serif;color:#a1846d}
        .sb-create-proficiency-group[open] summary{background:#f4e9dc;border-bottom:1px solid #e4ceba}
        .sb-create-proficiency-options{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:7px;padding:10px}
        .sb-create-proficiency-check{display:flex;min-width:0;align-items:center;gap:8px;cursor:pointer;padding:7px 9px;
            border:1px solid #e5d2c0;border-radius:7px;background:#fffaf3;color:#77503a;
            font:700 10px/1.3 system-ui,sans-serif;transition:background .15s,border-color .15s}
        .sb-create-proficiency-check:hover{background:#f1e2d1;border-color:#c59a78}
        .sb-create-proficiency-check:has(input:checked){background:#e9d7c2;border-color:#ae8060;color:#5d271f}
        .sb-create-proficiency-check input{margin:0;flex:none;width:13px;height:13px;accent-color:#712d22}
        .sb-create-proficiency-check span{min-width:0}
        .sb-create-custom-training{padding:12px 3px 2px;border-top:1px solid #e6d2bd}
        .sb-create-custom-training>strong{display:block;color:#6b3022;font:750 12px/1.2 Georgia,serif}
        .sb-create-custom-training>p{margin:4px 0 9px;color:#99795f;font:500 10px/1.4 system-ui,sans-serif}
        .sb-create-custom-training-row{display:grid;grid-template-columns:180px minmax(0,1fr) 28px;align-items:center;gap:7px;margin-bottom:7px}
        .sb-create-custom-training-row input,.sb-create-custom-training-row select{min-width:0;width:100%;height:33px;
            border:1px solid #d8c2ac;border-radius:7px;background:#fffdf9;padding:0 8px;color:#66402e;font:600 11px/1.3 system-ui,sans-serif}
        .sb-create-custom-training-row button{height:29px;border-radius:7px;background:#f6e7d8;color:#8f4e3a;font-size:18px}
        @media(max-width:940px){
            .sb-create-optional-grid{grid-template-columns:1fr}
            .sb-create-proficiency-options{grid-template-columns:repeat(2,minmax(0,1fr))}
        }
        @media(max-height:700px) and (min-width:761px){
            .sb-create-identity-grid{gap:11px}
            .sb-create-portrait-preview{height:136px;width:124px}
            .sb-create-photo-button{min-height:29px;margin-top:5px}
            .sb-create-photo-help{margin-top:2px}
            .sb-create-optional-grid{margin-top:11px}
        }
        @media(max-width:760px){
            .sb-create-identity-grid{display:flex;flex-direction:column;gap:13px}
            .sb-create-portrait-pane{display:flex;flex-direction:column;align-items:center;gap:0}
            .sb-create-portrait-preview{display:flex;grid-column:auto;grid-row:auto;width:134px;height:145px;min-height:0;border-width:4px}
            .sb-create-portrait-name{display:flex}
            .sb-create-photo-button{grid-column:auto;grid-row:auto;width:186px;margin-top:6px}
            .sb-create-photo-help{grid-column:auto;grid-row:auto;text-align:center}
            .sb-create-optional-grid{grid-template-columns:1fr}
            .sb-create-proficiency-options{grid-template-columns:repeat(2,minmax(0,1fr))}
        }
        @media(max-width:440px){
            .sb-create-portrait-pane{display:flex;flex-direction:column;align-items:center}
            .sb-create-portrait-preview{width:126px;height:137px}
            .sb-create-field-grid{grid-template-columns:1fr}
            .sb-create-span-2{grid-column:auto}
            .sb-create-proficiency-options{grid-template-columns:1fr 1fr;gap:5px;padding:7px}
            .sb-create-proficiency-check{padding:7px 6px;font-size:9px}
            .sb-create-proficiency-group summary small{display:none}
            .sb-create-custom-training-row{grid-template-columns:1fr 28px}
            .sb-create-custom-training-row select{grid-column:1/-1}
        }
        @media(max-width:360px){
            .sb-create-proficiency-options{grid-template-columns:1fr}
        }

        /* V66.2.248 — Fotografia central, campos com ações inline e modais compactos. */
        .sb-create-identity-composition{
            display:grid;grid-template-columns:minmax(0,1fr) 180px minmax(0,1fr);
            gap:clamp(12px,2.4vw,24px);align-items:center;max-width:1040px;
            min-height:330px;margin:4px auto 0;padding:10px 0 12px
        }
        .sb-create-identity-side{min-width:0;display:flex;flex-direction:column;justify-content:center;gap:19px}
        .sb-create-identity-left{padding-left:3px}.sb-create-identity-right{padding-right:3px}
        .sb-create-identity-composition .sb-create-portrait-pane{display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%;min-width:0}
        .sb-create-identity-composition .sb-create-portrait-preview{width:166px;height:199px;min-height:0;max-width:100%;border-width:4px}
        .sb-create-identity-composition .sb-create-photo-button{width:166px;max-width:100%;min-height:34px;margin-top:9px}
        .sb-create-identity-composition .sb-create-photo-help{max-width:185px;margin-top:7px;font-size:9px;text-align:center;line-height:1.35}
        .sb-create-identity-composition .sb-create-field{gap:7px}
        .sb-create-field-labelrow{display:flex;justify-content:space-between;align-items:center;gap:6px;min-height:25px}
        .sb-create-field-labelrow>span:first-child{font:850 9px/1.15 system-ui,sans-serif;letter-spacing:.1em;text-transform:uppercase;color:#744830}
        .sb-create-field-actions{display:inline-flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;gap:5px;min-width:0}
        .sb-create-field-action{display:inline-flex;align-items:center;gap:5px;min-height:25px;padding:0 8px;border:1px solid #d8bc9f;border-radius:6px;
            background:#fffaf2;color:#793d2d;font:800 9px/1 system-ui,sans-serif;white-space:nowrap;transition:background .15s,border-color .15s}
        .sb-create-field-action:hover,.sb-create-field-action:focus-visible{border-color:#a87d5c;background:#f0dfca}
        .sb-create-field-action em{display:inline-flex;min-width:16px;height:16px;align-items:center;justify-content:center;border-radius:999px;
            background:#ebd9c3;font:800 9px/1 system-ui,sans-serif;color:#703425;font-style:normal}
        .sb-create-info-wrap{display:inline-flex;align-items:center;justify-content:center;flex:none;width:19px;height:19px;border:1px solid #d4b79b;
            border-radius:50%;background:#f7ead8;color:#88543a;font:800 11px/1 system-ui,sans-serif;cursor:help}
        /* V66.2.249 — A segunda janela deve ter foco; não deixar a borda externa brilhando. */
        .sb-create-window.sb-create-child-dialog-open{
            border-color:transparent;box-shadow:none;background:transparent
        }
        .sb-create-feature-overlay{position:fixed;inset:0;z-index:350}
        .sb-create-feature-backdrop{background:rgba(39,25,19,.77);backdrop-filter:blur(5px)}
        .sb-create-feature-dialog{border-color:#c1a68c;box-shadow:0 18px 42px rgba(30,16,10,.23)}
        .sb-create-info-wrap{position:relative;z-index:2;appearance:none;padding:0;line-height:1;outline-offset:3px}
        .sb-create-info-wrap:hover,.sb-create-info-wrap:focus-visible{background:#f1ddc4;border-color:#a67450;color:#652a20}
        .sb-create-info-wrap::after{
            content:attr(data-tooltip);position:absolute;z-index:60;bottom:calc(100% + 10px);right:-10px;
            width:245px;max-width:min(75vw,270px);padding:10px 12px;border:1px solid #cba98c;
            border-radius:9px;background:#fffaf1;color:#704632;
            box-shadow:0 8px 24px rgba(48,27,17,.18);font:600 11px/1.5 system-ui,sans-serif;
            letter-spacing:0;text-align:left;text-transform:none;white-space:normal;
            visibility:hidden;opacity:0;pointer-events:none;transform:translateY(4px);
            transition:opacity .16s ease,transform .16s ease,visibility .16s
        }
        .sb-create-info-wrap:is(:hover,:focus-visible)::after{visibility:visible;opacity:1;transform:translateY(0)}
        .sb-create-feature-footer .sb-create-info-wrap{z-index:5}
        .sb-create-feature-footer .sb-create-info-wrap::after{left:0;right:auto}
        @media(max-width:525px){
            .sb-create-info-wrap::after{right:-4px;width:205px;font-size:10px}
        }
        .sb-create-identity-footnote{max-width:1040px;margin:4px auto 0;font:600 10px/1.4 system-ui,sans-serif;color:#a17d62;text-align:center}
        .sb-create-hidden-input{position:absolute!important;width:1px!important;height:1px!important;opacity:0!important;pointer-events:none!important}
        .sb-create-feature-overlay{position:absolute;inset:0;z-index:35;display:flex;align-items:center;justify-content:center;padding:16px}
        .sb-create-feature-backdrop{position:absolute;inset:0;background:rgba(41,24,17,.65);backdrop-filter:blur(3px)}
        .sb-create-feature-dialog{position:relative;z-index:1;display:flex;flex-direction:column;overflow:hidden;width:min(680px,100%);
            max-height:min(88dvh,660px);border:1px solid #cdae8b;border-radius:15px;background:#fdf8ee;box-shadow:0 20px 55px rgba(26,13,7,.28)}
        .sb-create-feature-header{display:flex;align-items:center;justify-content:space-between;gap:12px;flex:none;padding:13px 19px;
            border-bottom:1px solid #e3cdb5;background:linear-gradient(90deg,#fdf9f1,#f2e4d2)}
        .sb-create-feature-header small{font:800 9px/1.2 system-ui,sans-serif;letter-spacing:.11em;text-transform:uppercase;color:#a17b60}
        .sb-create-feature-header h3{margin-top:3px;font:750 20px/1.2 Georgia,serif;color:#61291e}
        .sb-create-feature-header>button{width:29px;height:29px;border:1px solid #ddc7b0;border-radius:7px;background:#fffaf3;color:#81533a;font-size:23px;line-height:1}
        .sb-create-feature-body{flex:1;min-height:0;overflow:auto;padding:16px 19px;scrollbar-width:thin;scrollbar-color:#c6a587 transparent}
        .sb-create-feature-body [data-create-feature='origin']>.sb-create-field + .sb-create-field{margin-top:12px}
        .sb-create-feature-body [data-create-feature='species']{display:flex;flex-direction:column;gap:10px}
        .sb-create-feature-body .sb-create-trait-editor{gap:10px}
        .sb-create-feature-body .sb-create-proficiency-editor{display:flex;flex-direction:column;gap:8px}
        .sb-create-feature-footer{display:flex;justify-content:space-between;align-items:center;gap:10px;flex:none;padding:10px 19px;
            border-top:1px solid #e4d0b9;background:#f7ecde;color:#98765b;font:700 10px/1.25 system-ui,sans-serif}
        .sb-create-feature-footer>span{display:flex;align-items:center;gap:8px}
        .sb-create-feature-footer>button,.sb-create-crop-apply{min-height:34px;padding:0 14px;border-radius:8px;background:#70291f!important;
            border:1px solid #70291f!important;color:#fffaf2!important;font:800 10px/1 system-ui,sans-serif}
        .sb-create-crop-dialog{width:min(620px,100%)}
        .sb-create-crop-layout{display:flex;align-items:center;justify-content:center;gap:22px;padding:17px 21px;overflow:auto}
        .sb-create-crop-stage{position:relative;flex:0 0 250px;width:250px;height:300px;overflow:hidden;border:5px solid #b58a6e;
            border-radius:12px;background:#c69f86;touch-action:none;cursor:grab;box-shadow:inset 0 0 0 1px #ffead7}
        .sb-create-crop-stage:active{cursor:grabbing}
        .sb-create-crop-stage img{position:absolute;top:50%;left:50%;max-width:none;max-height:none;pointer-events:none;user-select:none;transform-origin:center center}
        .sb-create-crop-guides{position:absolute;inset:9px;border:1px solid rgba(255,255,255,.70);border-radius:7px;pointer-events:none}
        .sb-create-crop-tools{display:flex;flex-direction:column;gap:9px;flex:1;min-width:0;max-width:245px;color:#875e45}
        .sb-create-crop-tools p{font:600 11px/1.45 system-ui,sans-serif;color:#93735a;margin-bottom:3px}
        .sb-create-crop-tools label{display:flex;justify-content:space-between;align-items:center;font:800 10px/1.3 system-ui,sans-serif;color:#6b3a2c}
        .sb-create-crop-tools output{font-weight:750;color:#9d7255}
        .sb-create-crop-tools input[type='range']{width:100%;margin:0 0 4px;accent-color:#7b3929}
        .sb-create-crop-buttons{display:flex;gap:7px;flex-wrap:wrap}
        .sb-create-crop-buttons>button{padding:8px 10px;border:1px solid #d8bd9d;border-radius:7px;background:#fffaf2;color:#714332;
            font:800 10px/1.2 system-ui,sans-serif}
        .sb-create-crop-buttons>button:hover{background:#f0dfc9}
        .sb-create-crop-buttons .sb-create-crop-apply:disabled{opacity:.5;cursor:not-allowed}
        .sb-create-crop-error{margin:0 19px 12px;padding:9px;border-radius:7px;background:#f7dbd2;color:#842b20;font:700 10px/1.35 system-ui,sans-serif}
        @media(max-width:950px){
            .sb-create-identity-composition{grid-template-columns:minmax(0,1fr) 160px minmax(0,1fr);gap:13px;min-height:290px}
            .sb-create-identity-composition .sb-create-portrait-preview{width:142px;height:170px}
            .sb-create-identity-composition .sb-create-photo-button{width:152px;font-size:9px}
            .sb-create-identity-side{gap:13px}
            .sb-create-field-actions{gap:3px}
            .sb-create-field-action{padding:0 6px;font-size:8px}
        }
        @media(max-height:680px) and (min-width:761px){
            .sb-create-identity-composition{min-height:265px;padding-block:4px}
            .sb-create-identity-composition .sb-create-portrait-preview{width:134px;height:160px}
            .sb-create-identity-composition .sb-create-photo-button{width:158px;margin-top:6px}
        }
        @media(max-width:760px){
            .sb-create-identity-composition{display:grid;grid-template-columns:1fr 1fr;gap:16px 12px;max-width:570px;min-height:0;padding-top:6px}
            .sb-create-identity-composition .sb-create-portrait-pane{grid-column:1/-1;grid-row:1;display:flex;flex-direction:column}
            .sb-create-identity-composition .sb-create-portrait-preview{width:132px;height:158px}
            .sb-create-identity-left{grid-column:1;grid-row:2}.sb-create-identity-right{grid-column:2;grid-row:2}
            .sb-create-field-actions{gap:4px}
            .sb-create-feature-dialog{max-height:calc(100dvh - 30px)}
        }
        @media(max-width:525px){
            .sb-create-identity-composition{grid-template-columns:1fr;gap:14px}
            .sb-create-identity-composition .sb-create-portrait-pane{grid-column:1;grid-row:1}
            .sb-create-identity-left,.sb-create-identity-right{grid-column:1;grid-row:auto}
            .sb-create-identity-side{gap:13px}
            .sb-create-crop-layout{flex-direction:column;gap:12px;overflow:auto;padding:12px}
            .sb-create-crop-stage{width:200px;height:240px;flex:0 0 240px}
            .sb-create-crop-tools{max-width:100%;width:100%;gap:5px}
            .sb-create-feature-overlay{padding:7px}
            .sb-create-feature-header{padding:10px 12px}
            .sb-create-feature-header h3{font-size:18px}
            .sb-create-feature-body{padding:12px}
            .sb-create-feature-footer{padding:9px 12px}
            .sb-create-feature-footer>span{font-size:9px}
        }

        /* V66.2.250 — vida calculada e conjuração vinculada às classes. */
        .sb-create-class-card{padding:13px 15px}
        .sb-create-class-top{margin-bottom:12px}
        .sb-create-class-top-actions{display:flex;align-items:center;flex-wrap:wrap;gap:12px}
        .sb-create-class-top-actions button:first-child{color:#7e5537;text-decoration:underline;text-underline-offset:3px}
        .sb-create-class-fields{grid-template-columns:minmax(130px,1.2fr) minmax(130px,1fr) 72px;gap:9px}
        .sb-create-class-options{display:flex;align-items:end;gap:18px;margin-top:12px;padding-top:10px;border-top:1px solid #ead9c6}
        .sb-create-class-options .sb-create-field{width:112px}
        .sb-create-class-options select,.sb-create-class-caster select{
            display:block;width:100%;min-height:36px;border:1px solid #d8c2ac;border-radius:8px;background:#fffdf9;
            padding:6px 10px;font:700 11px/1.4 system-ui,sans-serif;color:#63372a}
        .sb-create-caster-choice{display:flex;align-items:center;gap:8px;min-height:36px;font:750 11px/1.35 system-ui,sans-serif;color:#73452d;cursor:pointer}
        .sb-create-caster-choice input{width:16px;height:16px;accent-color:#752d22}
        .sb-create-class-caster{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px 12px;margin-top:11px;padding:10px;border-radius:9px;background:#f8edde}
        .sb-create-class-caster>p{grid-column:1/-1;font:500 10px/1.4 system-ui,sans-serif;color:#927258}
        .sb-create-life-grid{display:grid;grid-template-columns:minmax(0,.86fr) minmax(0,1.14fr);gap:14px;max-width:1050px;margin-inline:auto}
        .sb-create-life-panel,.sb-create-life-tray{min-width:0;padding:17px;border:1px solid #e3cdb4;border-radius:13px;background:#fffbf5}
        .sb-create-life-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-bottom:15px}
        .sb-create-life-stats>div{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;min-height:72px;border-radius:9px;background:#f5e8d6;color:#722e23}
        .sb-create-life-stats small{font:800 8px/1 system-ui,sans-serif;letter-spacing:.08em;text-align:center;color:#9c7353}
        .sb-create-life-stats strong{font:800 24px/1 Georgia,serif}
        .sb-create-life-con{max-width:240px}
        .sb-create-life-modes{display:flex;flex-wrap:wrap;gap:7px;margin:14px 0}
        .sb-create-life-modes label{display:flex;align-items:center;gap:6px;cursor:pointer;padding:9px 11px;border:1px solid #e3cdb4;border-radius:8px;background:#fdf8f0;color:#74442f;font:750 10px/1 system-ui,sans-serif}
        .sb-create-life-modes label.is-selected{background:#f1dfc9;border-color:#b78d65;color:#652820}
        .sb-create-life-modes input{accent-color:#69281f}
        .sb-create-life-help{margin-top:13px;color:#92745b;font:500 10px/1.55 system-ui,sans-serif}
        .sb-create-life-tray-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}
        .sb-create-life-tray-head>div{display:flex;flex-direction:column;gap:4px}
        .sb-create-life-tray-head strong{font:800 17px/1 Georgia,serif;color:#633024}
        .sb-create-life-tray-head span{font:600 10px/1.2 system-ui,sans-serif;color:#97745b}
        .sb-create-life-roll-button{padding:9px 11px;border:1px solid #722c22;border-radius:8px;background:#722c22;color:#fffaf1;white-space:nowrap;font:800 10px/1 system-ui,sans-serif}
        .sb-create-life-roll-button:disabled{opacity:.55;cursor:default}
        .sb-create-life-dice{display:flex;align-content:start;flex-wrap:wrap;gap:9px;padding:10px;max-height:235px;min-height:115px;overflow-y:auto;border:1px solid #e5d1bb;border-radius:10px;background:radial-gradient(circle at center,#fcf6ea,#f1e2ce)}
        .sb-create-life-dice>p{align-self:center;width:100%;text-align:center;color:#98755b;font:650 11px/1.5 system-ui,sans-serif}
        .sb-create-life-die{display:flex;align-items:center;flex-direction:column;gap:1px;min-width:53px}
        .sb-create-life-die .sb-poly-die{flex:none;transition:transform .35s ease}
        .sb-create-life-die.is-rolling .sb-poly-die{animation:sb-forge-life-roll .47s cubic-bezier(.2,.72,.43,1) infinite}
        .sb-create-life-die small{font:800 9px/1 system-ui,sans-serif;color:#8d6448}
        .sb-create-life-dice-caption{margin-top:9px;font:500 10px/1.5 system-ui,sans-serif;color:#977459}
        .sb-create-life-error{margin-top:6px;color:#aa342a;font:700 10px/1.4 system-ui,sans-serif}
        @keyframes sb-forge-life-roll{0%{transform:translateY(2px) rotate(-22deg) scale(.94)}50%{transform:translateY(-8px) rotate(19deg) scale(1.04)}100%{transform:translateY(2px) rotate(-22deg) scale(.94)}}
        @media(max-width:880px){.sb-create-life-grid{grid-template-columns:1fr}.sb-create-class-list{grid-template-columns:1fr}}
        @media(max-width:530px){.sb-create-class-fields{grid-template-columns:minmax(0,1fr) 77px}.sb-create-class-fields label:nth-child(2){grid-column:1/-1;grid-row:2}.sb-create-class-caster{grid-template-columns:1fr}.sb-create-tabs button{padding-inline:5px;font-size:9px}.sb-create-tabs{gap:0}.sb-create-step{width:19px;height:19px}}
        @media(prefers-reduced-motion:reduce){.sb-create-life-die.is-rolling .sb-poly-die{animation:none}}
        /* V66.2.252 — Atributos: seis valores e rolagem com os dados existentes. */
        .sb-create-abilities-layout{display:grid;grid-template-columns:minmax(0,1.56fr) minmax(0,.9fr);gap:13px;align-items:start;max-width:1050px;margin:0 auto}
        .sb-create-abilities-primary,.sb-create-abilities-tray{min-width:0;padding:15px;border:1px solid #e3cdb4;border-radius:13px;background:#fffbf5}
        .sb-create-abilities-top{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:13px}
        .sb-create-abilities-top>div:first-child{display:flex;flex-direction:column;gap:4px}
        .sb-create-abilities-top strong,.sb-create-abilities-tray-head strong{font:750 17px/1.1 Georgia,serif;color:#682c23}
        .sb-create-abilities-top span{font:600 10px/1.4 system-ui,sans-serif;color:#94745b}
        .sb-create-abilities-actions{display:flex;align-items:center;gap:7px;flex-wrap:wrap;justify-content:flex-end}
        .sb-create-abilities-actions button{min-height:34px;padding:0 10px;border-radius:8px;font:800 10px/1 system-ui,sans-serif;white-space:nowrap;cursor:pointer}
        .sb-create-abilities-actions button:disabled,.sb-create-ability-roll-one:disabled,.sb-create-abilities-history-list button:disabled{opacity:.55;cursor:wait}
        .sb-create-abilities-secondary{border:1px solid #ddc7b0;background:#faf2e6;color:#7d4e37}
        .sb-create-abilities-primary-button{border:1px solid #742e23;background:#742e23;color:#fff9f0}
        .sb-create-abilities-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}
        .sb-create-ability-card{display:flex;flex-direction:column;gap:11px;min-width:0;padding:12px;border:1px solid #ead7c5;border-radius:11px;background:linear-gradient(150deg,#fffdf9,#f5e9d9);transition:border-color .16s,box-shadow .16s}
        .sb-create-ability-card.is-picked{border-color:#c79c7c;box-shadow:inset 0 0 0 1px rgba(192,137,94,.15)}
        .sb-create-ability-top{display:flex;justify-content:space-between;align-items:center;gap:5px}
        .sb-create-ability-top label{font:800 10px/1.2 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.05em;color:#734431}
        .sb-create-ability-mod{display:inline-flex;align-items:center;justify-content:center;min-width:39px;padding:5px 7px;border-radius:7px;background:#ead9c4;color:#703127;font:750 16px/1 Georgia,serif}
        .sb-create-ability-bottom{display:flex;align-items:center;gap:7px}
        .sb-create-ability-bottom input{width:100%;min-width:0;height:42px;padding:4px 8px;text-align:center;border:1px solid #d9bca3;border-radius:8px;background:#fffdf9;color:#62251d;font:750 22px/1 Georgia,serif;box-shadow:inset 0 1px 2px rgba(87,46,26,.04)}
        .sb-create-ability-bottom input:focus-visible{outline:2px solid rgba(140,79,51,.45);outline-offset:1px}
        .sb-create-ability-roll-one{display:flex;align-items:center;justify-content:center;gap:4px;flex:none;min-height:36px;padding:0 8px;border:1px solid #dfc8b0;border-radius:8px;background:#fbf2e6;color:#744331;font:750 9px/1 system-ui,sans-serif;cursor:pointer}
        .sb-create-ability-roll-one svg{width:14px;height:14px}
        .sb-create-ability-roll-one:hover:not(:disabled),.sb-create-abilities-secondary:hover:not(:disabled){background:#f1dec8}
        .sb-create-abilities-primary-button:hover:not(:disabled){background:#5d251e}
        .sb-create-abilities-help{margin-top:12px;font:500 10px/1.5 system-ui,sans-serif;color:#96745a}
        .sb-create-abilities-tray-head{display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:12px}
        .sb-create-abilities-tray-head>div{display:flex;flex-direction:column;gap:6px}
        .sb-create-abilities-tray-head small,.sb-create-abilities-history>small{font:850 8px/1.1 system-ui,sans-serif;letter-spacing:.14em;color:#a77b58}
        .sb-create-abilities-tray-head>span{font:800 9px/1.2 system-ui,sans-serif;color:#986e51;white-space:nowrap}
        .sb-create-abilities-dice{display:flex;align-items:center;justify-content:space-evenly;gap:3px;min-height:100px;padding:13px 6px;border-radius:10px;border:1px solid #e5d1bb;background:radial-gradient(ellipse at center,#fff9ef,#f0e0cb)}
        .sb-create-abilities-die{display:flex;justify-content:center;align-items:center;min-width:0;transition:opacity .25s,filter .25s}
        .sb-create-abilities-die.is-discarded{opacity:.35;filter:grayscale(.75)}
        .sb-create-abilities-die .sb-poly-die{transition:transform .2s}
        .sb-create-abilities-die .sb-poly-die.is-rolling{animation:sb-forge-life-roll .48s ease-in-out infinite}
        .sb-create-abilities-result{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:10px;padding:9px 10px;border:1px solid #e9d7c3;border-radius:8px;background:#fbf2e6}
        .sb-create-abilities-result>div{display:flex;align-items:center;gap:10px}
        .sb-create-abilities-result small{color:#78503c;font:750 10px/1.2 system-ui,sans-serif}
        .sb-create-abilities-result strong{font:750 23px/1 Georgia,serif;color:#6a2a20}
        .sb-create-abilities-result>span{max-width:110px;text-align:right;font:600 9px/1.3 system-ui,sans-serif;color:#94735d}
        .sb-create-abilities-empty{margin-top:10px;text-align:center;color:#937057;font:600 10px/1.5 system-ui,sans-serif}
        .sb-create-abilities-empty strong{color:#6b3026}
        .sb-create-abilities-history{margin-top:14px;padding-top:11px;border-top:1px solid #ead7c5}
        .sb-create-abilities-history-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px;margin-top:9px}
        .sb-create-abilities-history-list button{display:flex;align-items:center;justify-content:space-between;gap:5px;min-width:0;padding:7px 9px;border:1px solid #e5d1bc;border-radius:8px;background:#faf4ea;color:#795641;text-align:left;font:700 10px/1.2 system-ui,sans-serif;cursor:pointer}
        .sb-create-abilities-history-list button.is-selected{border-color:#bb9275;background:#f3e2ce;color:#632c23}
        .sb-create-abilities-history-list strong{font:800 14px/1 Georgia,serif;color:#6b2b21}
        .sb-create-life-constitution{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border:1px solid #e5cfb7;border-radius:9px;background:#faf2e7;font:700 11px/1.2 system-ui,sans-serif;color:#714937}
        .sb-create-life-constitution strong{margin-left:7px;color:#63281e;font:800 14px/1 Georgia,serif}
        .sb-create-life-constitution button{padding:6px 9px;border:1px solid #d9bca0;border-radius:7px;background:#fffaf2;color:#713c2c;font:800 9px/1 system-ui,sans-serif;white-space:nowrap}
        @media(max-height:700px) and (min-width:761px){
            .sb-create-abilities-primary,.sb-create-abilities-tray{padding:11px 12px}
            .sb-create-abilities-top{margin-bottom:9px}
            .sb-create-ability-card{gap:7px;padding:9px 10px}
            .sb-create-ability-bottom input{height:38px}
            .sb-create-abilities-help{margin-top:8px}
            .sb-create-abilities-tray-head{margin-bottom:8px}
            .sb-create-abilities-dice{min-height:82px;padding:8px 6px}
            .sb-create-abilities-result{margin-top:7px;padding:6px 9px}
            .sb-create-abilities-history{margin-top:8px;padding-top:8px}
            .sb-create-abilities-history-list{margin-top:6px;gap:5px}
            .sb-create-abilities-history-list button{padding:6px 7px}
        }
        @media(max-width:950px){.sb-create-abilities-layout{grid-template-columns:minmax(0,1.2fr) minmax(0,.82fr)}.sb-create-abilities-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:690px){.sb-create-abilities-layout{grid-template-columns:minmax(0,1fr)}.sb-create-abilities-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.sb-create-abilities-tray{max-width:none}}
        @media(max-width:440px){.sb-create-abilities-history-list{grid-template-columns:repeat(2,minmax(0,1fr))}.sb-create-abilities-primary,.sb-create-abilities-tray{padding:12px}.sb-create-abilities-top{align-items:flex-start;flex-direction:column}.sb-create-abilities-actions{width:100%;justify-content:flex-start}.sb-create-ability-card{padding:9px}.sb-create-ability-bottom{flex-wrap:wrap}.sb-create-ability-bottom input{width:100%}.sb-create-ability-roll-one{width:100%;min-height:30px}}
        @media(prefers-reduced-motion:reduce){.sb-create-abilities-die .sb-poly-die.is-rolling{animation:none}}

        /* V66.2.253 — distribuição D&D 2024: Proficiências e conjunto padrão único. */
        .sb-create-proficiency-stage{max-width:1030px;margin:0 auto;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,.37fr);gap:13px;align-items:start}
        .sb-create-proficiency-main,.sb-create-proficiency-overview{min-width:0;border:1px solid #e4cdb6;border-radius:12px;background:#fffbf6;padding:14px}
        .sb-create-proficiency-overview{background:linear-gradient(155deg,#fbf3e7,#f2e4d2)}
        .sb-create-proficiency-overview h4{font:750 17px/1.2 Georgia,serif;color:#65291e}
        .sb-create-proficiency-overview p,.sb-create-proficiency-hint{font:600 10px/1.5 system-ui,sans-serif;color:#927054;margin:6px 0}
        .sb-create-proficiency-overview strong{color:#6a3024}
        .sb-create-proficiency-sources{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:11px}
        .sb-create-proficiency-sources button{padding:9px 12px;border:1px solid #dfc8b3;border-radius:9px;color:#815940;background:#faf3e9;font:800 10px/1 system-ui,sans-serif;cursor:pointer}
        .sb-create-proficiency-sources button.is-active{border-color:#b98666;color:#682820;background:#eedac4;box-shadow:inset 0 0 0 1px rgba(155,90,51,.12)}
        .sb-create-proficiency-main .sb-create-proficiency-editor{max-height:295px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:#c3a185 transparent}
        .sb-create-proficiency-main .sb-create-proficiency-group{margin-bottom:7px}
        .sb-create-proficiency-overview button{border:1px solid #d1ac8c;background:#fffbf6;border-radius:8px;padding:9px 12px;font:800 10px/1 system-ui,sans-serif;color:#743a2d;cursor:pointer;margin-top:10px}
        .sb-create-abilities-layout.is-single{grid-template-columns:minmax(0,1fr)}
        .sb-create-abilities-layout.is-single .sb-create-abilities-primary{width:100%;max-width:950px;margin-inline:auto}
        .sb-create-ability-modes{display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin:0 0 11px}
        .sb-create-ability-modes button{padding:7px 10px;border-radius:8px;border:1px solid #ddc7b3;color:#815139;background:#faf2e6;cursor:pointer;font:800 10px/1 system-ui,sans-serif}
        .sb-create-ability-modes button.is-active{background:#e9d4bd;border-color:#b38564;color:#632b23}
        .sb-create-ability-mode-hint{font:600 10px/1.45 system-ui,sans-serif;color:#946d50;margin:0 0 8px}
        .sb-create-standard-pool{display:flex;align-items:center;flex-wrap:wrap;gap:5px;margin:1px 0 9px}
        .sb-create-standard-pool span{font:800 9px/1 system-ui,sans-serif;color:#907052;margin-right:4px}
        .sb-create-standard-pool strong{display:inline-flex;min-width:26px;justify-content:center;padding:5px 6px;border:1px solid #cba483;border-radius:6px;background:#f6e4cf;font:800 11px/1 Georgia,serif;color:#653126}
        .sb-create-standard-pool em{font:600 10px/1.3 system-ui,sans-serif;color:#9c775d}
        .sb-create-ability-standard-select{width:100%;min-width:0;height:42px;border:1px solid #d9bca3;border-radius:8px;background:#fffdf9;color:#652820;text-align:center;font:800 16px Georgia,serif}
        .sb-create-ability-release{flex:none;border:1px solid #ddc2a8;border-radius:8px;background:#fbf2e5;color:#81513a;min-height:35px;padding:0 6px;font-size:10px;cursor:pointer}
        .sb-create-abilities-dice{position:relative;min-height:133px;overflow:hidden;perspective:700px;background:radial-gradient(ellipse at 35% 29%,#f8e8cf,#d7b68f);box-shadow:inset 0 3px 8px rgba(103,66,35,.16)}
        .sb-create-abilities-die{transform-style:preserve-3d}
        .sb-create-abilities-die .sb-poly-die.is-rolling{animation:sb-create-d6-toss 1.02s cubic-bezier(.2,.65,.27,1) both !important;animation-delay:calc(var(--sb-die-order,0) * 50ms)}
        @keyframes sb-create-d6-toss{
            0%{transform:translate(-24px,-72px) rotateX(-25deg) rotateY(45deg) rotateZ(-65deg) scale(.86);filter:drop-shadow(0 24px 9px rgba(81,48,25,.13))}
            35%{transform:translate(17px,15px) rotateX(270deg) rotateY(190deg) rotateZ(130deg) scale(1.06);filter:drop-shadow(0 2px 2px rgba(81,48,25,.35))}
            55%{transform:translate(8px,-33px) rotateX(410deg) rotateY(335deg) rotateZ(210deg) scale(.97);filter:drop-shadow(0 17px 8px rgba(81,48,25,.2))}
            76%{transform:translate(-4px,6px) rotateX(590deg) rotateY(450deg) rotateZ(330deg) scale(1);filter:drop-shadow(0 2px 2px rgba(81,48,25,.3))}
            90%{transform:translate(2px,-9px) rotateX(690deg) rotateY(575deg) rotateZ(352deg) scale(1);filter:drop-shadow(0 6px 4px rgba(81,48,25,.22))}
            100%{transform:translate(0,0) rotateX(720deg) rotateY(720deg) rotateZ(360deg) scale(1);filter:drop-shadow(0 3px 3px rgba(81,48,25,.25))}
        }
        @media(max-width:780px){.sb-create-proficiency-stage{grid-template-columns:1fr}.sb-create-proficiency-main .sb-create-proficiency-editor{max-height:270px}}
        @media(max-height:700px) and (min-width:781px){.sb-create-proficiency-main .sb-create-proficiency-editor{max-height:248px}.sb-create-abilities-dice{min-height:104px}}
        @media(max-width:530px){.sb-create-proficiency-main,.sb-create-proficiency-overview{padding:10px}.sb-create-ability-modes button{padding:7px 8px}.sb-create-standard-pool{gap:4px}}
        @media(prefers-reduced-motion:reduce){.sb-create-abilities-die .sb-poly-die.is-rolling{animation:none!important}}
        /* V66.2.254 — sem cubos decorativos: bandeja e física aprovadas na ficha. */
        .sb-create-abilities-physical-hint{display:flex;align-items:center;gap:13px;min-height:140px;padding:15px 12px;
            border:1px solid #ddc1a2;border-radius:12px;
            background:linear-gradient(150deg,#f5e5cf,#e7d0b1)}
        .sb-create-abilities-physical-copy{min-width:0;color:#85573d}
        .sb-create-abilities-physical-copy strong{display:block;margin-bottom:7px;font:800 14px/1.1 Georgia,serif;color:#682b20}
        .sb-create-abilities-physical-copy p{font:650 10px/1.55 system-ui,sans-serif}
        .sb-create-abilities-physical-copy kbd{display:inline;padding:2px 4px;border-radius:4px;
            border:1px solid #caa684;background:#fff8eb;font:800 9px system-ui,sans-serif;color:#743829}
        .sb-create-abilities-open-tray{margin-top:7px;padding:6px 8px;border:1px solid #ceb091;
            border-radius:8px;background:#fff9ee;color:#6d3125;font:800 10px system-ui,sans-serif}
        @media(max-width:530px){.sb-create-abilities-physical-hint{min-height:105px;padding:10px;gap:8px}
        }

        /* V66.2.255 — Atributos organizados em tabela, bandeja independente e fixa. */
        .sb-create-abilities-layout{display:grid;grid-template-columns:minmax(0,1fr) 255px;max-width:1040px;gap:14px;align-items:start;margin:auto}
        .sb-create-abilities-layout.is-single{grid-template-columns:minmax(0,1fr)}
        .sb-create-abilities-primary{padding:14px 16px;min-width:0;background:#fffbf6}
        .sb-create-abilities-guide{padding:14px 13px;background:linear-gradient(145deg,#fffaf3,#f5e9da)}
        .sb-create-abilities-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:13px;flex-wrap:wrap}
        .sb-create-abilities-heading>div:first-child{display:flex;flex-direction:column;gap:4px}
        .sb-create-abilities-heading strong{color:#682c23;font:750 17px/1.2 Georgia,serif}
        .sb-create-abilities-heading span{color:#99765d;font:600 10px/1.3 system-ui,sans-serif}
        .sb-create-ability-modes{display:flex;gap:6px;flex-wrap:wrap}
        .sb-create-ability-modes button{border:1px solid #dfc6ac;border-radius:8px;background:#fbf0e3;padding:9px 10px;white-space:nowrap;color:#784333;font:800 10px/1 system-ui,sans-serif;cursor:pointer}
        .sb-create-ability-modes button.is-active{background:#eee0cd;border-color:#be8f6a;color:#69281d}
        .sb-create-ability-modes button:hover{background:#f3dfc9}
        .sb-create-ability-pool{display:flex;align-items:center;gap:6px;margin:0 0 12px;padding:7px 9px;border:1px solid #e8d6c2;border-radius:8px;background:#faf1e6}
        .sb-create-ability-pool small{font:800 9px system-ui,sans-serif;color:#926748;letter-spacing:.07em;margin-right:5px}
        .sb-create-ability-pool strong{padding:4px 9px;border-radius:6px;background:#fffaf4;border:1px solid #e5cbb0;color:#692d24;font:800 14px/1 Georgia,serif}
        .sb-create-ability-pool em{font:600 10px system-ui,sans-serif;color:#92715a}
        .sb-create-ability-table-wrap{border:1px solid #e9d5bf;border-radius:11px;overflow:hidden}
        .sb-create-ability-table{width:100%;border-collapse:collapse;table-layout:fixed;background:#fffaf4}
        .sb-create-ability-table th:nth-child(1){width:34%}.sb-create-ability-table th:nth-child(2){width:25%}.sb-create-ability-table th:nth-child(3){width:20%}.sb-create-ability-table th:nth-child(4){width:21%}
        .sb-create-ability-table thead th{padding:10px 9px;background:#f2e3d0;border-bottom:1px solid #e3cbb3;text-align:left;color:#7b4c37;font:850 9px/1 system-ui,sans-serif;letter-spacing:.09em;text-transform:uppercase}
        .sb-create-ability-table thead th:not(:first-child){text-align:center}
        .sb-create-ability-table tbody tr{border-bottom:1px solid #eee0d0;transition:background .17s ease}
        .sb-create-ability-table tbody tr:last-child{border-bottom:0}
        .sb-create-ability-table tbody tr:nth-child(even){background:#f9f0e5}
        .sb-create-ability-table tbody tr.is-selected{background:#f4e3ce}
        .sb-create-ability-table tbody th{padding:7px 10px;text-align:left}
        .sb-create-ability-table tbody th label{color:#673324;font:800 12px/1.15 system-ui,sans-serif}
        .sb-create-ability-table td{padding:6px 8px;text-align:center}
        .sb-create-ability-table select,.sb-create-ability-table input{width:100%;min-width:0;max-width:114px;height:34px;border:1px solid #d8b89c;border-radius:7px;background:#fffdf8;text-align:center;color:#6b2d25;font:800 17px/1 Georgia,serif}
        .sb-create-ability-table select{font:750 12px system-ui,sans-serif}
        .sb-create-ability-table-mod{display:inline-grid;place-items:center;min-width:42px;padding:7px 6px;background:#ebdbc7;border-radius:7px;font:800 15px/1 Georgia,serif;color:#683226}
        .sb-create-ability-table-action{padding:8px 9px;border:1px solid #dac0a6;border-radius:7px;background:#fff8ee;color:#723d2d;font:800 10px/1 system-ui,sans-serif;cursor:pointer}
        .sb-create-ability-table-action:hover:not(:disabled){background:#f5e1c7}
        .sb-create-ability-table-action:disabled{opacity:.6;cursor:default}
        .sb-create-ability-table-readonly{font:650 9px system-ui,sans-serif;color:#a17c60}
        .sb-create-abilities-guide-text{margin:0;color:#76543e;font:600 11px/1.55 system-ui,sans-serif}
        .sb-create-abilities-guide-text strong{color:#662a21}
        .sb-create-abilities-guide-text kbd{border:1px solid #d8bba0;border-radius:4px;background:#fffdf7;padding:1px 3px;font:850 10px system-ui,sans-serif}
        .sb-create-abilities-progress{display:flex;flex-direction:column;gap:3px;padding:9px;margin-top:12px;border-radius:8px;border:1px solid #e2ccb5;background:#fbf1e5}
        .sb-create-abilities-progress strong{font:800 13px Georgia,serif;color:#692e24}
        .sb-create-abilities-progress span{font:600 10px/1.4 system-ui,sans-serif;color:#866249}
        .sb-create-abilities-facedice{display:flex;justify-content:center;gap:7px;margin-top:10px}
        .sb-create-abilities-facedice span{display:grid;place-items:center;width:32px;height:34px;border:1px solid #d5b79c;border-radius:7px;background:#fffaf0;color:#6b3028;font:800 19px/1 Georgia,serif}
        .sb-create-abilities-facedice span.is-discarded{opacity:.45;text-decoration:line-through}
        .sb-create-abilities-guide-actions{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
        .sb-create-abilities-guide-actions button{padding:7px 8px;border:1px solid #d9bca3;border-radius:7px;background:#fbf4e9;color:#763d2d;font:750 9px system-ui,sans-serif}
        .sb-create-abilities-guide .sb-create-abilities-tray-head{margin-bottom:9px}
        @media(max-width:980px){.sb-create-abilities-layout{grid-template-columns:minmax(0,1fr) 226px}.sb-create-abilities-heading{gap:9px}}
        @media(max-width:740px){.sb-create-abilities-layout{grid-template-columns:1fr}.sb-create-abilities-guide{max-width:none}.sb-create-ability-table tbody th label{font-size:11px}.sb-create-abilities-heading{flex-direction:column}}
        @media(max-width:420px){.sb-create-abilities-primary{padding:10px 9px}.sb-create-ability-table thead th{padding:8px 4px;font-size:8px}.sb-create-ability-table tbody th{padding:6px 5px}.sb-create-ability-table td{padding:5px 3px}.sb-create-ability-table-action{font-size:9px;padding:6px 5px}.sb-create-ability-table-mod{min-width:34px}.sb-create-ability-table tbody th label{font-size:10px}}
        /* V66.2.256: só a tabela e uma ajuda discreta; sem painel duplicado. */
        .sb-create-abilities-layout,.sb-create-abilities-layout.is-single{grid-template-columns:minmax(0,1fr);max-width:1000px}
        .sb-create-abilities-primary{width:100%;padding:17px 19px}
        .sb-create-abilities-heading{align-items:center;gap:10px;margin-bottom:12px}
        .sb-create-abilities-heading>div:first-child{flex-direction:row;align-items:center;gap:9px;flex-wrap:wrap}
        .sb-create-abilities-heading>div:first-child>span{width:100%;font-size:10px}
        .sb-create-abilities-heading strong{font-size:18px}
        .sb-create-ability-info{position:relative;display:inline-flex;align-items:center;flex:none}
        .sb-create-ability-info-trigger{display:grid;place-items:center;width:21px;height:21px;padding:0;
            border:1px solid #cfae91;border-radius:50%;background:#f9ecdd;color:#743f2b;
            font:800 12px/1 Georgia,serif;cursor:help}
        .sb-create-ability-info-trigger:hover,.sb-create-ability-info-trigger:focus-visible{
            background:#ebd6bc;border-color:#a97b5e;outline-offset:2px}
        .sb-create-ability-info-panel{position:absolute;z-index:25;top:calc(100% + 8px);left:-14px;
            width:min(350px,calc(100vw - 50px));padding:13px 14px;border:1px solid #d8bca1;
            border-radius:10px;background:#fffbf3;color:#6d4633;font:600 11px/1.55 system-ui,sans-serif;
            box-shadow:0 15px 34px rgba(68,32,17,.18)}
        .sb-create-ability-info-panel strong{font:800 13px/1.2 Georgia,serif;color:#6e3024}
        .sb-create-ability-info-panel p{margin:7px 0 0}
        .sb-create-ability-info-panel em{font-style:normal;color:#936448}
        .sb-create-ability-progress-inline{display:inline-flex;align-items:center;gap:7px;max-width:100%;
            border:1px solid #e6ceb7;border-radius:999px;padding:6px 9px;background:#f8ecde;
            font:700 10px/1.2 system-ui,sans-serif;color:#86513b}
        .sb-create-ability-progress-inline b{font-weight:850;color:#693225}
        .sb-create-ability-progress-inline button{border:0;background:transparent;color:#893c2b;
            text-decoration:underline;text-underline-offset:2px;font:800 10px/1.2 system-ui,sans-serif;cursor:pointer}
        .sb-create-ability-progress-inline button:hover{color:#581f18}
        .sb-create-abilities-status-row{display:flex;align-items:center;justify-content:space-between;
            flex-wrap:wrap;gap:6px;margin:-1px 0 9px;min-height:0}
        .sb-create-abilities-status-row:empty{display:none}
        .sb-create-abilities-status-row .sb-create-life-error{margin:0;font-size:10px}
        .sb-create-abilities-help{margin-top:8px;font-size:10px}
        @media(max-width:760px){.sb-create-abilities-primary{padding:12px}}
        @media(max-width:530px){.sb-create-abilities-heading{align-items:flex-start;flex-direction:column}
            .sb-create-abilities-heading>div:first-child>span{font-size:9.5px}
            .sb-create-ability-info-panel{left:-112px;width:min(320px,calc(100vw - 44px))}}
        /* O estojo ocupa a coluna à direita, sem encobrir a tabela em notebooks. */
        .sb-create-abilities-layout:not(.is-single){grid-template-columns:minmax(0,1fr) 280px}
        .sb-create-abilities-dice-anchor{min-width:0;min-height:186px;pointer-events:none}
        @media(max-width:920px){.sb-create-abilities-layout:not(.is-single){grid-template-columns:minmax(0,1fr) 254px}}
        @media(max-width:780px){.sb-create-abilities-layout:not(.is-single){grid-template-columns:minmax(0,1fr)}
            .sb-create-abilities-dice-anchor{display:none!important}}


        /* V66.2.257 — Vida segue a mesma tabela limpa de Atributos. */
        .sb-create-life-modern{display:grid;grid-template-columns:minmax(0,1fr) minmax(260px,290px);gap:14px;max-width:1050px;margin-inline:auto;align-items:start}
        .sb-create-life-main,.sb-create-life-aside{min-width:0;border:1px solid #e3cdb4;border-radius:13px;background:#fffbf5;padding:15px}
        .sb-create-life-main .sb-create-life-stats{margin-bottom:10px}
        .sb-create-life-main .sb-create-life-stats>div{min-height:62px;gap:5px}
        .sb-create-life-main .sb-create-life-stats strong{font-size:21px}
        .sb-create-life-modes{margin:10px 0;gap:6px}
        .sb-create-life-modes button{padding:8px 11px;border:1px solid #e3cdb4;border-radius:8px;background:#fdf8f0;color:#74442f;font:750 10px system-ui,sans-serif;cursor:pointer}
        .sb-create-life-modes button.is-selected{background:#f1dfc9;border-color:#b78d65;color:#652820}
        .sb-create-life-list-title{display:flex;align-items:center;gap:7px;margin:13px 0 6px;color:#683027}
        .sb-create-life-list-title>strong{font:800 16px/1 Georgia,serif}
        .sb-create-life-list-title>span{margin-left:auto;font:700 10px system-ui,sans-serif;color:#98715b}
        .sb-create-life-table-wrap{border:1px solid #e3cdb4;border-radius:9px;overflow:auto;max-height:min(37vh,300px)}
        .sb-create-life-table{width:100%;border-collapse:collapse;text-align:left;color:#6c392b;font:750 11px system-ui,sans-serif}
        .sb-create-life-table thead{position:sticky;top:0;background:#f1dfca;z-index:2}
        .sb-create-life-table th{font:800 9px system-ui,sans-serif;letter-spacing:.08em;color:#88583e;padding:10px}
        .sb-create-life-table td{padding:8px 10px;border-top:1px solid #ebdac8}
        .sb-create-life-table tbody tr:nth-child(even){background:#f9f0e5}
        .sb-create-life-table td strong,.sb-create-life-fixed{font:800 15px Georgia,serif;color:#6b2b22}
        .sb-create-life-table td strong.is-pending{opacity:.5}
        .sb-create-life-table td small{display:inline;color:#98725a;font:700 9px system-ui,sans-serif}
        .sb-create-life-main .sb-create-life-help{font-size:9.5px;margin-top:10px}
        .sb-create-life-aside{min-height:165px;position:relative}
        .sb-create-life-dice-dock-area{height:174px;margin-top:12px;pointer-events:none}
        .sb-create-life-aside-top{display:flex;justify-content:space-between;gap:8px;align-items:center;color:#683027}
        .sb-create-life-aside-top strong{font:800 17px Georgia,serif}
        .sb-create-life-aside-top span{font:700 9px system-ui,sans-serif;color:#967159}
        .sb-create-life-aside-hint{margin-top:15px;color:#8c654d;font:600 11px/1.55 system-ui,sans-serif}
        .sb-create-life-aside-progress{display:flex;flex-direction:column;gap:10px;margin-top:12px;color:#7d523c}
        .sb-create-life-aside-progress>strong{font:800 15px Georgia,serif;color:#6e3328}
        .sb-create-life-aside-progress>span{font:650 10px/1.4 system-ui,sans-serif}
        .sb-create-life-aside-progress button{align-self:flex-start}
        @media(max-width:840px){.sb-create-life-modern{grid-template-columns:1fr}.sb-create-life-aside{min-height:0}}
        @media(max-width:520px){.sb-create-life-main,.sb-create-life-aside{padding:10px}.sb-create-life-table th,.sb-create-life-table td{padding:7px 5px}.sb-create-life-main .sb-create-life-stats strong{font-size:17px}}
        /* V66.2.258: painel de vida sem duplicações e edição manual inline. */
        .sb-create-life-modern{grid-template-columns:minmax(0,1fr);max-width:1000px}
        .sb-create-life-modern.is-rolling{grid-template-columns:minmax(0,1fr) 258px}
        .sb-create-life-main{padding:16px 18px}
        .sb-create-life-summary{display:flex;align-items:stretch;justify-content:space-between;gap:12px}
        .sb-create-life-total{display:flex;flex:1;flex-direction:column;align-items:flex-start;justify-content:center;gap:4px;
            padding:14px 17px;border:1px solid #e8d3bd;border-radius:11px;background:linear-gradient(150deg,#f6e8d7,#f9f2e8)}
        .sb-create-life-total>span{color:#987052;font:850 9px/1.3 system-ui,sans-serif;letter-spacing:.09em}
        .sb-create-life-total>strong{color:#6c2c22;font:850 37px/1.15 Georgia,serif}
        .sb-create-life-total input{width:146px;max-width:100%;height:43px;padding:4px 9px;border:1px solid #be9676;
            border-radius:8px;background:#fffaf3;color:#692b22;font:850 30px Georgia,serif}
        .sb-create-life-total>small{color:#927055;font:650 10px/1.4 system-ui,sans-serif}
        .sb-create-life-con-info{display:flex;flex-direction:column;justify-content:center;align-items:flex-start;gap:6px;
            min-width:160px;padding:12px;border:1px solid #ead6c1;border-radius:10px;background:#fffdf8}
        .sb-create-life-con-info>span{color:#946f52;font:850 9px/1 system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase}
        .sb-create-life-con-info strong{color:#713226;font:800 21px/1 Georgia,serif}
        .sb-create-life-con-info button{padding:5px 0;border:0;background:none;color:#8b573c;font:750 10px/1.2 system-ui,sans-serif;
            text-decoration:underline;text-underline-offset:3px;cursor:pointer}
        .sb-create-life-modes{margin:12px 0 8px}
        .sb-create-life-aside{min-height:0;padding:14px;position:relative}
        .sb-create-life-aside-top strong{font-size:15px}
        .sb-create-life-aside-note{margin:12px 0 10px;font:650 10.5px/1.5 system-ui,sans-serif;color:#8b654e}
        .sb-create-life-aside-buttons{display:flex;flex-wrap:wrap;gap:7px}
        .sb-create-life-aside-buttons button{padding:8px 9px;border-radius:8px;font:800 10px/1 system-ui,sans-serif;cursor:pointer}
        .sb-create-life-roll-subtle{border:1px solid #e0cab4;background:#fcf4e9;color:#7c422e}
        .sb-create-life-roll-subtle:disabled{opacity:.5}
        .sb-create-life-dice-dock-area{height:172px;margin-top:6px}
        .sb-create-ability-table-actions{display:flex;align-items:center;justify-content:center;gap:4px;flex-wrap:wrap}
        .sb-create-ability-table-actions button{font-size:9px;padding:7px 6px}
        @media(max-width:920px){.sb-create-life-modern.is-rolling{grid-template-columns:minmax(0,1fr) 236px}}
        @media(max-width:780px){.sb-create-life-modern.is-rolling{grid-template-columns:1fr}.sb-create-life-dice-dock-area{display:none!important}}
        @media(max-width:480px){.sb-create-life-summary{flex-wrap:wrap}.sb-create-life-con-info{min-width:0;width:100%;flex-direction:row;align-items:center;justify-content:space-between}.sb-create-life-total{padding:12px}.sb-create-ability-table-actions button{font-size:8px;padding:6px 5px}}

        /* V66.2.260 — classes em ordem: escolha primeiro, total depois. */
        .sb-create-class-card:only-child{grid-column:1/-1;max-width:705px;width:100%}
        .sb-create-class-summary{display:flex;align-items:baseline;gap:9px;max-width:705px;margin:14px 0 0;padding:12px 15px;
            border:1px solid #e4ceb7;border-radius:10px;background:linear-gradient(90deg,#f8eee1,#fcf6ed)}
        .sb-create-class-summary>span:first-child{color:#80533b;font:850 10px system-ui,sans-serif;text-transform:uppercase;letter-spacing:.05em}
        .sb-create-class-summary>strong{color:#63271e;font:800 25px Georgia,serif}
        .sb-create-class-summary-limit{font:800 12px Georgia,serif;color:#a07857}
        .sb-create-class-summary>small{margin-left:auto;color:#926e56;font:650 10px/1.3 system-ui,sans-serif}
        /* Bandeja de vida tem uma coluna real, sem fugir para o rodapé da janela. */
        .sb-create-life-modern.is-rolling{grid-template-columns:minmax(0,1fr) 318px;max-width:1070px}
        .sb-create-life-aside{min-height:338px;padding:14px 12px}
        .sb-create-life-dice-dock-area{display:block;height:226px;min-height:226px;width:100%;margin-top:10px;pointer-events:none}
        .sb-create-life-aside-note{margin:7px 0 9px;font-size:10px;line-height:1.42}
        .sb-create-life-aside-buttons{gap:5px}
        .sb-create-life-aside-buttons button{padding:7px 9px}
        .sb-create-life-result-cell{min-width:130px}
        .sb-create-life-result-button{display:inline-flex;align-items:center;gap:7px;padding:5px 7px;border:1px solid transparent;border-radius:7px;
            background:transparent;color:#6b3027;font:800 12px Georgia,serif;cursor:pointer;text-align:left;transition:background .15s,border-color .15s}
        .sb-create-life-result-button:not(:disabled):hover,.sb-create-life-result-button:not(:disabled):focus-visible{background:#f6e4d0;border-color:#d9b794}
        .sb-create-life-result-button:disabled{cursor:default;opacity:1}
        .sb-create-life-result-button.is-pending{opacity:.57}
        .sb-create-life-result-button strong{font:800 16px Georgia,serif}
        .sb-create-life-result-button small{font:700 9px/1.15 system-ui,sans-serif;color:#9a6c4a}
        .sb-create-life-inline-edit{display:inline-flex;align-items:center;gap:5px;max-width:100%}
        .sb-create-life-inline-edit input{width:54px;height:30px;padding:3px 5px;border:1px solid #b98d71;border-radius:6px;
            background:#fffaf3;color:#693228;font:800 14px Georgia,serif;text-align:center}
        .sb-create-life-inline-edit button{width:29px;height:29px;border:1px solid #dfc4a9;border-radius:6px;background:#faf0e4;
            color:#6b3328;font:800 14px system-ui,sans-serif;cursor:pointer}
        .sb-create-life-inline-edit button:hover{background:#eedbc8}
        .sb-create-life-inline-error{display:block;color:#a73b2b;font-size:9px;margin-top:3px}
        @media(max-width:1020px){.sb-create-life-modern.is-rolling{grid-template-columns:minmax(0,1fr) 305px}}
        @media(max-width:820px){.sb-create-life-modern.is-rolling{grid-template-columns:1fr}.sb-create-life-aside{min-height:305px}.sb-create-life-dice-dock-area{height:205px}}
        @media(max-width:520px){.sb-create-class-summary{flex-wrap:wrap}.sb-create-class-summary>small{flex-basis:100%;margin-left:0}.sb-create-life-result-cell{min-width:103px}}

        /* V66.2.261 — bandeja harmonizada com o painel de Rolagem de vida. */
        .sb-create-life-modern.is-rolling{align-items:start}
        .sb-create-life-modern.is-rolling .sb-create-life-aside{display:flex;flex-direction:column;min-height:0}
        .sb-create-life-modern.is-rolling .sb-create-life-dice-dock-area{flex:0 0 auto;height:296px;min-height:296px;margin-top:12px;width:100%}
        .sb-create-life-edit-warning{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;
            margin:9px 0 4px;padding:11px 12px;border:1px solid #d2a383;border-radius:10px;
            background:linear-gradient(100deg,#fff5e8,#f4e5d2);box-shadow:0 2px 8px rgba(89,46,28,.06)}
        .sb-create-life-edit-warning-copy{flex:1 1 200px;min-width:0}
        .sb-create-life-edit-warning-copy strong{font:800 12px/1.25 Georgia,serif;color:#64291f}
        .sb-create-life-edit-warning-copy p{margin:4px 0 0;font:600 10px/1.45 system-ui,sans-serif;color:#88573e}
        .sb-create-life-edit-warning-actions{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
        .sb-create-life-edit-warning-actions button{min-height:30px;border:1px solid #dbbd9e;border-radius:7px;padding:6px 9px;
            background:#fffaf3;color:#704233;cursor:pointer;font:800 10px/1 system-ui,sans-serif}
        .sb-create-life-edit-warning-actions button.is-confirm{background:#742c22;color:#fff7eb;border-color:#742c22}
        .sb-create-life-edit-warning-actions button:hover{filter:brightness(.97)}
        @media(max-width:820px){.sb-create-life-modern.is-rolling .sb-create-life-aside{min-height:0}
            .sb-create-life-modern.is-rolling .sb-create-life-dice-dock-area{height:234px;min-height:234px}}


        /* V66.2.262 — criação consolidada: proficiências e origem 2024 */
        .sb-create-proficiency-stage--unified{grid-template-columns:minmax(0,1fr) minmax(205px,.33fr)}
        .sb-create-skill-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px;padding:10px}
        .sb-create-skill-line{display:flex;min-width:0;align-items:center;justify-content:space-between;gap:8px;padding:6px 8px;border:1px solid #e6d0ba;border-radius:8px;background:#fffaf4;font:700 10px/1.2 system-ui,sans-serif;color:#6b3a2c}
        .sb-create-skill-line select{min-width:107px;max-width:125px;padding:6px;border:1px solid #dfc4ab;border-radius:6px;background:#fffdf9;color:#6b3125;font:700 10px system-ui,sans-serif}
        .sb-create-origin-methods{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0 12px}
        .sb-create-origin-methods label{display:flex;align-items:center;gap:6px;border:1px solid #e5cdb6;border-radius:8px;padding:9px;background:#fbf3e7;font:750 11px system-ui,sans-serif;color:#713b29}
        .sb-create-origin-methods input{accent-color:#712d22}
        .sb-create-origin-three{display:flex;gap:10px;margin:10px 0;flex-wrap:wrap}
        .sb-create-origin-three label{display:flex;flex:1;flex-direction:column;gap:6px;font:750 10px system-ui,sans-serif;color:#743c2d}
        .sb-create-origin-three select{min-width:130px;flex:1;border:1px solid #dcbda4;border-radius:8px;background:#fffcf8;padding:9px;color:#663023;font:700 12px system-ui,sans-serif}
        .sb-create-modal-summary{font:600 11px/1.5 system-ui,sans-serif;color:#86614d;margin:6px 0}
        .sb-create-ability-total{font:800 13px Georgia,serif;color:#722c24}
        .sb-create-ability-total.is-over-limit{color:#c72c2c}
        .sb-create-attribute-breakdown{display:flex;align-items:center;flex-wrap:wrap;gap:9px;padding:8px 10px;margin:7px 0;border:1px solid #e6cdb7;background:#f9efe3;border-radius:9px;font:650 10px system-ui,sans-serif;color:#82523b}
        .sb-create-attribute-breakdown button,.sb-create-ability-extra-link{border:1px solid #dec3aa;border-radius:7px;background:#fff8f0;padding:5px 8px;color:#77382a;font:800 10px system-ui,sans-serif;cursor:pointer}
        .sb-create-ability-extra-link--header{margin-left:10px}
        .sb-create-ability-validation{color:#a72c22;font:700 11px system-ui,sans-serif;margin:5px 0}
        .sb-create-ability-table-wrap{overflow-x:auto;scrollbar-width:thin}
        .sb-create-ability-table{min-width:660px}
        @media(max-width:780px){.sb-create-proficiency-stage--unified{grid-template-columns:1fr}.sb-create-skill-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:520px){.sb-create-skill-grid{grid-template-columns:1fr}.sb-create-origin-three{flex-direction:column}}

        .sb-create-ability-table{table-layout:fixed;min-width:730px}
        .sb-create-ability-table th:nth-child(1){width:16%}
        .sb-create-ability-table th:nth-child(2){width:14%}
        .sb-create-ability-table th:nth-child(3){width:11%}
        .sb-create-ability-table th:nth-child(4){width:11%}
        .sb-create-ability-table th:nth-child(5){width:10%}
        .sb-create-ability-table th:nth-child(6){width:10%}
        .sb-create-ability-table th:nth-child(7){width:28%}
        .sb-create-ability-table td,.sb-create-ability-table th{overflow-wrap:normal;white-space:nowrap}
        .sb-create-ability-table input,.sb-create-ability-table select{max-width:104px}
        .sb-create-ability-table tbody th label{font-size:11px}
        .sb-create-ability-table .sb-create-ability-table-action{padding:6px 7px}
        .sb-create-proficiency-main .sb-create-proficiency-editor--unified{max-height:345px;overflow-y:auto}
        .sb-create-field-actions{flex-wrap:wrap;justify-content:flex-end}
        @media(max-width:530px){.sb-create-ability-table-wrap{overflow-x:auto}.sb-create-ability-table{min-width:730px}.sb-create-skill-grid{grid-template-columns:1fr}}

        /* V66.2.263 — sem modal concorrendo com a bandeja de dados. Os resultados são preservados. */
        body:has(.sb-create-window.sb-create-child-dialog-open) .sb-dice-dock,
        body:has(.sb-create-window.sb-create-child-dialog-open) .sb-free-staged-field,
        body:has(.sb-create-window.sb-create-child-dialog-open) .sb-forge-dice-stage {visibility:hidden!important;pointer-events:none!important}
        /* Classes — fluxo de leitura limpo, sem cartões isolados em uma metade da janela. */
        .sb-create-view[data-create-tab="classes"] .sb-create-class-list{width:100%;max-width:1030px;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
        .sb-create-class-card,.sb-create-class-card:only-child{width:100%;max-width:none;padding:0;border:1px solid #dcc1a7;border-radius:13px;background:#fffbf6;overflow:hidden;box-shadow:0 4px 16px rgba(92,45,30,.045)}
        .sb-create-class-top{margin:0;padding:12px 16px;gap:8px;background:#f4e8d9;border-bottom:1px solid #e6cbb1;letter-spacing:0;text-transform:none}
        .sb-create-class-identity{display:flex;align-items:center;gap:10px;min-width:0}
        .sb-create-class-identity>div{display:flex;flex-direction:column;gap:4px;min-width:0}
        .sb-create-class-identity strong{color:#61291e;font:800 16px/1.1 Georgia,serif}
        .sb-create-class-identity small{color:#9c7456;font:650 10px/1.2 system-ui,sans-serif;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-transform:none;letter-spacing:0}
        .sb-create-class-seal{display:grid;place-items:center;flex:none;width:31px;height:31px;border:1px solid #d6b292;border-radius:9px;background:#fff9f0;font:800 17px Georgia,serif;color:#713025}
        .sb-create-class-top-actions{gap:10px;justify-content:flex-end}
        .sb-create-class-top-actions button:first-child{color:#80513a;font-size:10px}
        .sb-create-class-fields{grid-template-columns:minmax(0,1.3fr) minmax(0,1fr) 80px;padding:15px 16px 14px;gap:9px}
        .sb-create-class-options{margin:0 16px;padding:12px 0;align-items:center;justify-content:space-between;gap:12px}
        .sb-create-class-options .sb-create-field{width:110px}
        .sb-create-caster-choice{border:1px solid #e5ccba;border-radius:8px;padding:7px 10px;background:#faf1e5;gap:7px}
        .sb-create-caster-choice.is-on{background:#ead6be;border-color:#cba17d;color:#64281e}
        .sb-create-class-caster{margin:0 16px 15px;padding:12px;background:#f6e9da;border:1px solid #ead3bc}
        .sb-create-class-summary{max-width:1030px;margin:14px 0 0;padding:10px 14px;gap:8px}
        .sb-create-class-summary>strong{font-size:23px}
        .sb-create-settings-grid:has(> .sb-create-setting:only-child){grid-template-columns:minmax(0,1fr);max-width:550px}
        .sb-create-class-proficiency{display:flex;align-items:center;flex-wrap:wrap;gap:12px;max-width:1030px;margin-top:10px;padding:11px 14px;border:1px solid #e0c7ae;border-radius:10px;background:#fff9f1}
        .sb-create-class-proficiency-copy{display:flex;align-items:center;gap:10px;flex:1;min-width:230px;flex-wrap:wrap}
        .sb-create-class-proficiency-copy>span{color:#81543d;font:850 9px system-ui,sans-serif;letter-spacing:.07em}
        .sb-create-class-proficiency-copy>strong{font:800 21px Georgia,serif;color:#60261d}
        .sb-create-class-proficiency-copy>small{color:#99775e;font:600 10px system-ui,sans-serif}
        .sb-create-prof-custom-switch{display:flex;align-items:center;gap:6px;cursor:pointer;font:750 10px system-ui,sans-serif;color:#724532}
        .sb-create-prof-custom-switch input{accent-color:#712a1e}
        .sb-create-prof-custom-field{flex-direction:row;align-items:center;gap:8px}
        .sb-create-prof-custom-field input{width:72px;min-height:32px;padding:5px 8px}
        /* Atributos — cabe em uma tabela sem barra horizontal no notebook. */
        .sb-create-ability-table{width:100%;min-width:565px;table-layout:fixed}
        .sb-create-ability-table th:nth-child(1){width:22%}
        .sb-create-ability-table th:nth-child(2){width:18%}
        .sb-create-ability-table th:nth-child(3){width:17%}
        .sb-create-ability-table th:nth-child(4){width:13%}
        .sb-create-ability-table th:nth-child(5){width:12%}
        .sb-create-ability-table th:nth-child(6){width:18%}
        .sb-create-ability-table input,.sb-create-ability-table select{max-width:92px}
        .sb-create-ability-table-actions{gap:3px}
        .sb-create-ability-table-actions button{padding:6px 5px;font-size:8.5px}
        .sb-create-abilities-heading{gap:10px}
        .sb-create-attribute-breakdown{padding:7px 9px;gap:7px}
        /* Treinamentos adicionais exatamente na categoria que os define. */
        .sb-create-training-inline{border-top:1px solid #ecd9c7;padding:9px 10px 11px;background:#fcf7f1}
        .sb-create-training-inline-title{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:6px;color:#825a42;font:750 10px system-ui,sans-serif}
        .sb-create-training-inline-title button{padding:6px 10px;border:1px solid #dec3a8;border-radius:7px;background:#fffbf6;color:#7b382a;font:800 10px system-ui,sans-serif;cursor:pointer}
        .sb-create-training-inline-title button:disabled{opacity:.5;cursor:default}
        .sb-create-training-inline-row{display:flex;align-items:center;gap:6px;margin-top:6px}
        .sb-create-training-inline-row input,.sb-create-training-inline-row select{min-width:0;min-height:34px;border:1px solid #e0c5ac;border-radius:7px;padding:6px 8px;background:#fffefa;color:#603427;font:650 11px system-ui,sans-serif}
        .sb-create-training-inline-row input{flex:1}
        .sb-create-training-inline-row select{width:150px;max-width:36%}
        .sb-create-training-inline-row button{flex:none;display:grid;place-items:center;width:30px;height:30px;border-radius:7px;color:#9b5946;background:#f5e5d5;font-size:18px}
        @media(max-width:870px){.sb-create-view[data-create-tab="classes"] .sb-create-class-list{grid-template-columns:1fr}.sb-create-class-card{max-width:740px}.sb-create-class-proficiency{max-width:740px}.sb-create-class-summary{max-width:740px}}
        @media(max-width:600px){.sb-create-ability-table{min-width:565px}.sb-create-class-fields{grid-template-columns:minmax(0,1fr) 82px}.sb-create-class-fields label:nth-child(2){grid-column:1/-1;grid-row:2}.sb-create-class-top{padding:10px}.sb-create-class-fields{padding:12px}.sb-create-class-options{margin-inline:12px}.sb-create-training-inline-row{flex-wrap:wrap}.sb-create-training-inline-row select{width:100%;max-width:none;flex:1}.sb-create-class-proficiency-copy{min-width:0}}
    </style>


    @php
        $forgeOldClasses = is_array(old('classes')) && count(old('classes')) > 0
            ? array_values(old('classes'))
            : [[
                'class' => '', 'subclass' => '', 'level' => 1,
                'hit_die' => 8, 'spellcaster' => false,
                'spell_ability' => 'wisdom', 'caster_type' => 'full',
            ]];
        $forgeOldSpeciesTraits = is_array(old('species_traits'))
            ? array_values(old('species_traits')) : [];
        $forgeOldOriginFeat = is_array(old('origin_feat'))
            ? old('origin_feat') : ['name' => '', 'description' => ''];
        $forgeBackgroundSkillOptions = [
            'acrobatics' => 'Acrobacia',
            'animal_handling' => 'Adestrar Animais',
            'arcana' => 'Arcanismo',
            'athletics' => 'Atletismo',
            'deception' => 'Enganação',
            'history' => 'História',
            'insight' => 'Intuição',
            'intimidation' => 'Intimidação',
            'investigation' => 'Investigação',
            'medicine' => 'Medicina',
            'nature' => 'Natureza',
            'perception' => 'Percepção',
            'performance' => 'Atuação',
            'persuasion' => 'Persuasão',
            'religion' => 'Religião',
            'sleight_of_hand' => 'Prestidigitação',
            'stealth' => 'Furtividade',
            'survival' => 'Sobrevivência',
        ];
        $forgeBackgroundTrainingOptions = [
            'armor_training' => [
                'title' => 'Armaduras e escudos',
                'options' => ['Armaduras Leves', 'Armaduras Médias', 'Armaduras Pesadas', 'Escudos'],
            ],
            'weapon_training' => [
                'title' => 'Armas',
                'options' => ['Armas Simples', 'Armas Marciais', 'Todas as Armas'],
            ],
            'tool_proficiency' => [
                'title' => 'Ferramentas e kits',
                'options' => [
                    'Ferramentas de Ladrão', 'Kit de Herbalismo', 'Kit de Disfarce',
                    'Kit de Falsificação', 'Kit de Veneno', 'Ferramentas de Navegador',
                    'Suprimentos de Alquimista', 'Ferramentas de Ferreiro',
                    'Ferramentas de Carpinteiro', 'Ferramentas de Artesão',
                ],
            ],
            'language' => [
                'title' => 'Idiomas',
                'options' => [
                    'Comum', 'Anão', 'Élfico', 'Gigante', 'Gnômico', 'Goblin',
                    'Halfling', 'Orc', 'Abissal', 'Celestial', 'Dracônico',
                    'Dialeto Profundo', 'Infernal', 'Primordial', 'Silvestre', 'Subcomum',
                ],
            ],
            'vehicle_proficiency' => [
                'title' => 'Veículos',
                'options' => ['Veículos Terrestres', 'Veículos Aquáticos', 'Veículos Aéreos'],
            ],
        ];
        $forgeOldBackgroundTrainings = array_replace(
            array_fill_keys(array_keys($forgeBackgroundTrainingOptions), []),
            is_array(old('background_trainings')) ? old('background_trainings') : []
        );
        $forgeOldBackgroundSkills = is_array(old('background_skills'))
            ? array_values(old('background_skills')) : [];
        $forgeOldBackgroundCustom = is_array(old('background_custom_trainings'))
            ? array_values(old('background_custom_trainings')) : [];
        $forgeOldClassSkills = is_array(old('class_skills'))
            ? array_values(old('class_skills')) : [];
        $forgeOldClassTrainings = array_replace(
            array_fill_keys(array_keys($forgeBackgroundTrainingOptions), []),
            is_array(old('class_trainings')) ? old('class_trainings') : []
        );
        $forgeOldClassCustom = is_array(old('class_custom_trainings'))
            ? array_values(old('class_custom_trainings')) : [];
        $forgeHasBackgroundTraining = $forgeOldBackgroundSkills !== []
            || collect($forgeOldBackgroundTrainings)->contains(fn ($values) => is_array($values) && $values !== [])
            || $forgeOldBackgroundCustom !== [];
        $forgeInitialErrorKeys = collect(array_keys($errors->getBag('default')->getMessages()));
        $forgeInitialTab = $forgeInitialErrorKeys->contains(fn ($key) => str_starts_with($key, 'classes'))
            ? 'classes'
            : ($forgeInitialErrorKeys->contains(fn ($key) => str_starts_with($key, 'class_skills')
                || str_starts_with($key, 'class_trainings') || str_starts_with($key, 'class_custom_trainings')
                || str_starts_with($key, 'background_skills') || str_starts_with($key, 'background_trainings')
                || str_starts_with($key, 'background_custom_trainings') || str_starts_with($key, 'skill_expertise'))
                ? 'proficiencies'
                : ($forgeInitialErrorKeys->contains(fn ($key) => $key === 'abilities'
                    || $key === 'ability_generation_method' || str_starts_with($key, 'abilities.')
                    || str_starts_with($key, 'base_abilities'))
                    ? 'abilities'
                    : ($forgeInitialErrorKeys->contains(fn ($key) => in_array($key, [
                        'hit_points_method', 'hit_points_manual', 'hit_points_rolls',
                    ], true)) ? 'vitality' : 'identity')));
        $forgeAbilityNames = [
            'strength' => 'Força',
            'dexterity' => 'Destreza',
            'constitution' => 'Constituição',
            'intelligence' => 'Inteligência',
            'wisdom' => 'Sabedoria',
            'charisma' => 'Carisma',
        ];
        $forgeAbilityDefaults = [
            'strength' => 10, 'dexterity' => 10, 'constitution' => 10,
            'intelligence' => 10, 'wisdom' => 10, 'charisma' => 10,
        ];
        $forgeAbilityOld = array_replace(
            $forgeAbilityDefaults,
            is_array(old('base_abilities')) ? old('base_abilities')
                : (is_array(old('abilities')) ? old('abilities') : [])
        );
        $forgeAbilityMethod = old('ability_generation_method', 'manual');
        $forgeStandardArray = [15, 14, 13, 12, 10, 8];
        $forgeOldStandardAssignments = [];
        foreach (array_keys($forgeAbilityNames) as $forgeAbilityKey) {
            $forgeSavedScore = $forgeAbilityOld[$forgeAbilityKey] ?? null;
            $forgeOldStandardAssignments[$forgeAbilityKey] = $forgeAbilityMethod === 'standard'
                && in_array((int) $forgeSavedScore, $forgeStandardArray, true)
                ? (int) $forgeSavedScore : null;
        }
    @endphp

    <div

        x-data="{

            createOpen: {{ $errors->any() ? 'true' : 'false' }},

            forgeName: @js(old('name', '')),
            forgeSpecies: @js(old('species', '')),
            forgeBackground: @js(old('background', '')),
            createTab: @js($forgeInitialTab),
            xpEnabled: {{ old('xp_enabled') ? 'true' : 'false' }},
            customProfEnabled: {{ old('custom_prof_enabled') ? 'true' : 'false' }},
            customProfValue: @js(old('proficiency_bonus', '')),
            abilityScores: @js($forgeAbilityOld),
            originAbilityMode: @js(old('background_ability_method', 'none')),
            originAbilityOptions: @js(old('background_ability_options', ['', '', ''])),
            originAbilityPrimary: @js(old('background_ability_primary', '')),
            originAbilitySecondary: @js(old('background_ability_secondary', '')),
            expertiseSkills: @js(is_array(old('skill_expertise')) ? old('skill_expertise') : []),
            abilityMethod: @js($forgeAbilityMethod),
            abilityStandardSelections: @js($forgeOldStandardAssignments),
            standardArray: [15, 14, 13, 12, 10, 8],
            abilityRollProgress: 0,
            abilityRollBusy: false,
            abilityRollError: '',
            abilityRollHistory: [],
            abilitySelected: 'strength',
            abilityDiceFaces: [null, null, null, null],
            abilityDiscardIndex: -1,
            forgePendingDice: [],
            forgePendingFaces: [],
            lifeRollBusy: false,
            forgeDiceContext: 'ability',
            forgeRoundId: 0,
            forgeDiceInFlight: false,
            forgeTarget: 'strength',
            forgeBatch: false,
            hpMethod: @js(old('hit_points_method', 'standard')),
            hpManual: @js(old('hit_points_manual', '')),
            lifeRollResults: @js(json_decode((string) old('hit_points_rolls', '{}'), true) ?: (object) []),
            lifeRolling: false,
            lifeRollError: '',
            lifeEditKey: null,
            lifeEditConfirmKey: null,
            lifeEditValue: null,
            lifeEditError: '',
            diceRollEndpoint: @js(url('/api/roll')),
            imagePreview: null,
            featureModal: null,
            photoCropOpen: false,
            photoCropSource: null,
            photoCropTempUrl: null,
            photoPreviewUrl: null,
            photoCropReady: false,
            photoCropSaving: false,
            photoCropError: '',
            photoCropZoom: 1,
            photoCropFrameW: 250,
            photoCropFrameH: 300,
            photoCropBaseW: 250,
            photoCropBaseH: 300,
            photoCropOffsetX: 0,
            photoCropOffsetY: 0,
            photoCropPointer: null,
            speciesTraitsOpen: @js(count($forgeOldSpeciesTraits) > 0),
            originFeatOpen: @js(old('origin_feat') !== null),
            speciesTraits: @js($forgeOldSpeciesTraits),
            originFeat: @js($forgeOldOriginFeat),
            backgroundProficienciesOpen: @js($forgeHasBackgroundTraining),
            backgroundSkills: @js($forgeOldBackgroundSkills),
            backgroundTrainings: @js($forgeOldBackgroundTrainings),
            backgroundCustomTrainings: @js($forgeOldBackgroundCustom),
            proficiencyOrigin: 'class',
            classSkills: @js($forgeOldClassSkills),
            classTrainings: @js($forgeOldClassTrainings),
            classCustomTrainings: @js($forgeOldClassCustom),
            get classProficiencyCount() {
                return this.classSkills.length
                    + Object.values(this.classTrainings).reduce((sum, entries) => sum + (Array.isArray(entries) ? entries.length : 0), 0)
                    + this.classCustomTrainings.filter(entry => (entry.name || '').trim()).length;
            },
            addClassTraining(type='tool_proficiency') {
                if (!['armor_training','weapon_training','tool_proficiency','language','vehicle_proficiency'].includes(type)) return;
                if (this.classCustomTrainings.length < 30) {
                    this.classCustomTrainings.push({type, name:'', roll_ability:''});
                }
            },
            get backgroundProficiencyCount() {
                return this.backgroundSkills.length
                    + Object.values(this.backgroundTrainings).reduce((sum, entries) => sum + (Array.isArray(entries) ? entries.length : 0), 0)
                    + this.backgroundCustomTrainings.filter(entry => (entry.name || '').trim()).length;
            },
            addBackgroundTraining(type='tool_proficiency') {
                if (!['armor_training','weapon_training','tool_proficiency','language','vehicle_proficiency'].includes(type)) return;
                if (this.backgroundCustomTrainings.length < 30) {
                    this.backgroundProficienciesOpen = true;
                    this.backgroundCustomTrainings.push({type, name:'', roll_ability:''});
                }
            },
            classes: @js($forgeOldClasses),
            classSuggestion(item) {
                const name = String(item?.class || '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').trim().toLowerCase();
                const defaults = {
                    'barbaro':{hit_die:12,spellcaster:false},
                    'bardo':{hit_die:8,spellcaster:true,spell_ability:'charisma',caster_type:'full'},
                    'bruxo':{hit_die:8,spellcaster:true,spell_ability:'charisma',caster_type:'pact'},
                    'clerigo':{hit_die:8,spellcaster:true,spell_ability:'wisdom',caster_type:'full'},
                    'druida':{hit_die:8,spellcaster:true,spell_ability:'wisdom',caster_type:'full'},
                    'feiticeiro':{hit_die:6,spellcaster:true,spell_ability:'charisma',caster_type:'full'},
                    'guerreiro':{hit_die:10,spellcaster:false},
                    'ladino':{hit_die:8,spellcaster:false},
                    'mago':{hit_die:6,spellcaster:true,spell_ability:'intelligence',caster_type:'full'},
                    'monge':{hit_die:8,spellcaster:false},
                    'paladino':{hit_die:10,spellcaster:true,spell_ability:'charisma',caster_type:'half_up'},
                    'patrulheiro':{hit_die:10,spellcaster:true,spell_ability:'wisdom',caster_type:'half_up'},
                    'artifice':{hit_die:8,spellcaster:true,spell_ability:'intelligence',caster_type:'half_up'},
                };
                return defaults[name] || null;
            },
            applyClassSuggestion(item) {
                const suggestion = this.classSuggestion(item);
                if (suggestion) Object.assign(item, suggestion);
            },
            get normalizedClasses() {
                return this.classes.map(row => ({
                    ...row,
                    level: Math.max(1, Math.min(20, Number(row.level) || 1)),
                    hit_die: [6,8,10,12].includes(Number(row.hit_die)) ? Number(row.hit_die) : 8,
                    spellcaster: row.spellcaster === true || row.spellcaster === '1' || row.spellcaster === 1,
                }));
            },
            get lifeDiceList() {
                const rows = [];
                this.normalizedClasses.forEach((item, index) => {
                    const amount = item.level - (index === 0 ? 1 : 0);
                    for (let number = 0; number < amount; number++) {
                        rows.push({key: index + '-' + number, index, sides: item.hit_die,
                            classLevel: number + (index===0?2:1),
                            className: (item.class || 'Classe ' + (index + 1))});
                    }
                });
                return rows;
            },
            get standardProficiency() {
                const level = this.totalLevel;
                return Math.min(6, 2 + Math.floor((Math.max(1,level) - 1) / 4));
            },
            get effectiveProficiency() {
                const raw = String(this.customProfValue ?? '').trim();
                const number = Number(raw);
                return this.customProfEnabled && raw !== '' && Number.isInteger(number)
                    ? number : this.standardProficiency;
            },
            get lifeDefaultTotal() {
                const first = this.normalizedClasses[0]?.hit_die || 8;
                return first + this.lifeDiceList.reduce((sum, entry) => sum + Math.floor(entry.sides/2) + 1, 0);
            },
            get lifeRollComplete() {
                return this.lifeDiceList.every(entry => {
                    const raw=this.lifeRollResults[entry.key];
                    const number=Number(raw);
                    return raw!==undefined && raw!==null && Number.isInteger(number) && number>=1 && number<=entry.sides;
                });
            },
            get lifeRollCount() {
                return this.lifeDiceList.filter(entry => Object.prototype.hasOwnProperty.call(this.lifeRollResults,entry.key)).length;
            },
            get lifeDiceTotal() {
                const first = this.normalizedClasses[0]?.hit_die || 8;
                const extra = this.lifeDiceList.reduce((sum, entry) => {
                    const value = Number(this.lifeRollResults[entry.key]);
                    return sum + (Number.isInteger(value) && value >= 1 && value <= entry.sides ? value : Math.floor(entry.sides/2) + 1);
                }, 0);
                return first + extra;
            },
            get lifeCalculatedMax() {
                const base = this.hpMethod === 'rolled' ? this.lifeDiceTotal : this.lifeDefaultTotal;
                const con = Math.max(1, Math.min(30, Number(this.abilityScores.constitution) || 10));
                return Math.max(1, base + Math.floor((con-10)/2) * this.totalLevel);
            },
            get lifeMaxHp() {
                const manual = Number(this.hpManual);
                return this.hpMethod === 'manual' && Number.isInteger(manual) && manual >= 1 ? manual : this.lifeCalculatedMax;
            },
            setLifeMethod(method) {
                if (!['standard','rolled','manual'].includes(method)) return;
                const previousMax=this.lifeMaxHp;
                if (this.lifeRollBusy && method !== 'rolled') this.stopForgeDice();
                this.hpMethod=method;
                this.cancelLifeDieEdit();
                // O ajuste manual acontece no mesmo mostrador dos PV, sem campo extra.
                if(method==='manual' && (!Number.isInteger(Number(this.hpManual)) || Number(this.hpManual)<1))
                    this.hpManual=previousMax;
                if(method==='rolled' && this.lifeDiceList.length>0){
                    if(this.lifeRollCount>0)this.showForgeLifeDice();
                    else if(!this.lifeRollBusy)this.beginForgeLifeRoll();
                }
            },
            showForgeLifeDice() {
                if(!this.lifeDiceList.length||this.hpMethod!=='rolled')return;
                if(this.lifeRollBusy && this.forgeDiceContext==='life'){
                    window.dispatchEvent(new CustomEvent('spellbound:forge-show-tray'));return;
                }
                this.forgeDiceContext='life';
                this.forgeRoundId++;
                this.forgeTarget='life';
                this.lifeRollBusy=!this.lifeRollComplete;
                window.dispatchEvent(new CustomEvent('spellbound:forge-prepare-dice',{
                    detail:{mode:'life',round:this.forgeRoundId,target:'life',restore:true,
                        dice:this.lifeDiceList.map(entry=>({key:entry.key,sides:entry.sides,
                            label:entry.className+' · '+entry.classLevel+'º nível · d'+entry.sides,badge:entry.classLevel+'º'}))}
                }));
            },
            beginForgeLifeRoll() {
                if (this.abilityRollBusy || this.lifeRolling) return;
                this.cancelLifeDieEdit();
                this.hpMethod='rolled';
                if (!this.lifeDiceList.length) return;
                // Só o botão Rerolar reinicia uma rodada concluída.
                if (this.lifeRollComplete && !this.lifeRollBusy) return;
                this.lifeRollResults={};
                this.lifeRollBusy=true;
                this.forgeDiceContext='life';
                this.forgeRoundId++;
                this.lifeRollError='';
                window.dispatchEvent(new CustomEvent('spellbound:forge-prepare-dice', {
                    detail:{mode:'life',round:this.forgeRoundId,target:'life',reset:true,dice:this.lifeDiceList.map(entry=>({
                        key:entry.key,sides:entry.sides,
                        label:entry.className+' · '+entry.classLevel+'º nível · d'+entry.sides,
                        badge:entry.classLevel+'º',
                    }))}
                }));
            },
            restartForgeLifeRoll() {
                if (this.abilityRollBusy || this.lifeRolling || this.forgeDiceInFlight) return;
                this.cancelLifeDieEdit();
                this.lifeRollResults={};
                this.lifeRollBusy=false;
                this.beginForgeLifeRoll();
            },
            lifeDieIsRolled(entry) {
                const value=this.lifeRollResults[entry.key];
                return value!==null&&value!==undefined&&Number.isInteger(Number(value))
                    && Number(value)>=1 && Number(value)<=entry.sides;
            },
            lifeDieFace(entry) {
                return this.lifeDieIsRolled(entry) ? Number(this.lifeRollResults[entry.key]) : null;
            },
            editLifeDieResult(entry) {
                if (this.hpMethod!=='rolled' || !this.lifeDieIsRolled(entry) || this.forgeDiceInFlight) return;
                // A primeira interação apenas avisa. Nenhuma face é alterada sem confirmação.
                this.lifeEditKey=null;
                this.lifeEditValue=null;
                this.lifeEditConfirmKey=entry.key;
                this.lifeEditError='';
            },
            confirmLifeDieEdit() {
                const entry=this.lifeDiceList.find(row=>row.key===this.lifeEditConfirmKey);
                if (!entry || !this.lifeDieIsRolled(entry) || this.forgeDiceInFlight) {
                    this.lifeEditConfirmKey=null;
                    return;
                }
                this.lifeEditKey=entry.key;
                this.lifeEditValue=this.lifeDieFace(entry);
                this.lifeEditConfirmKey=null;
                this.lifeEditError='';
            },
            dismissLifeDieWarning() {
                this.lifeEditConfirmKey=null;
            },
            saveLifeDieResult(entry) {
                if (this.lifeEditKey!==entry.key || this.forgeDiceInFlight) return;
                const value=Number(this.lifeEditValue);
                if (!Number.isInteger(value) || value<1 || value>entry.sides) {
                    this.lifeEditError='Informe um valor entre 1 e '+entry.sides+'.';
                    return;
                }
                // A edição muda apenas o resultado desse nível, sem criar outro dado.
                this.lifeRollResults={...this.lifeRollResults,[entry.key]:value};
                this.lifeRollBusy=!this.lifeRollComplete;
                this.lifeEditKey=null;
                this.lifeEditValue=null;
                this.lifeEditError='';
                window.dispatchEvent(new CustomEvent('spellbound:forge-set-die-face', {
                    detail:{mode:'life',key:entry.key,face:value}
                }));
            },
            cancelLifeDieEdit() {
                this.lifeEditKey=null;
                this.lifeEditConfirmKey=null;
                this.lifeEditValue=null;
                this.lifeEditError='';
            },
            abilityModifier(key) {
                const value = this.effectiveAbilityScore(key);
                return value===null ? 0 : Math.floor((value - 10) / 2);
            },
            abilityModifierLabel(key) {
                if (this.effectiveAbilityScore(key) === null) return '—';
                const modifier = this.abilityModifier(key);
                return modifier >= 0 ? '+' + modifier : String(modifier);
            },
            abilityName(key) {
                return ({strength:'Força',dexterity:'Destreza',constitution:'Constituição',
                    intelligence:'Inteligência',wisdom:'Sabedoria',charisma:'Carisma'})[key] || key;
            },
            get abilityKeys() {
                return ['strength','dexterity','constitution','intelligence','wisdom','charisma'];
            },
            get originEligible() {
                return this.originAbilityOptions.filter((v,i,a) => !!v && a.indexOf(v)===i);
            },
            originBonus(key) {
                if (!this.forgeBackground.trim() || this.originEligible.length!==3) return 0;
                if (this.originAbilityMode==='three') return this.originEligible.includes(key) ? 1 : 0;
                if (this.originAbilityMode==='two' && this.originAbilityPrimary!==this.originAbilitySecondary) {
                    if (!this.originEligible.includes(this.originAbilityPrimary) || !this.originEligible.includes(this.originAbilitySecondary)) return 0;
                    return key===this.originAbilityPrimary ? 2 : (key===this.originAbilitySecondary ? 1 : 0);
                }
                return 0;
            },
            effectiveAbilityScore(key) {
                const raw=this.abilityScores[key];
                if (raw==='' || raw===null || raw===undefined) return null;
                const value=Number(raw);
                return Number.isFinite(value) ? value+this.originBonus(key) : null;
            },
            abilityOverLimit(key) { const score=this.effectiveAbilityScore(key); return score!==null && score>20; },
            get abilityBonusError() {
                if (this.originAbilityMode!=='none' && (this.originEligible.length!==3
                    || (this.originAbilityMode==='two' && (!this.originAbilityPrimary || !this.originAbilitySecondary || this.originAbilityPrimary===this.originAbilitySecondary
                        || !this.originEligible.includes(this.originAbilityPrimary) || !this.originEligible.includes(this.originAbilitySecondary)))))
                    return 'Defina três atributos elegíveis e a distribuição do antecedente.';
                if (this.abilityKeys.some(key=>this.abilityOverLimit(key))) return 'O atributo final ultrapassou 20. Revise a base ou o bônus do antecedente.';
                return '';
            },
            skillTrainingLevel(key) {
                if (this.expertiseSkills.includes(key)) return 'expertise';
                if (this.classSkills.includes(key) || this.backgroundSkills.includes(key)) return 'proficient';
                return 'none';
            },
            setSkillTrainingLevel(key,mode) {
                if (!this.abilityKeys) return;
                this.expertiseSkills=this.expertiseSkills.filter(v=>v!==key);
                if (mode==='none') {
                    this.classSkills=this.classSkills.filter(v=>v!==key);
                    this.backgroundSkills=this.backgroundSkills.filter(v=>v!==key);
                } else if (mode==='expertise') {
                    if (!this.classSkills.includes(key) && !this.backgroundSkills.includes(key)) this.classSkills.push(key);
                    this.expertiseSkills.push(key);
                } else if (!this.classSkills.includes(key) && !this.backgroundSkills.includes(key)) this.classSkills.push(key);
            },
            trainingChecked(type,name) {
                return (this.classTrainings[type]||[]).includes(name) || (this.backgroundTrainings[type]||[]).includes(name);
            },
            toggleTraining(type,name,checked) {
                if (checked) {
                    if (!this.trainingChecked(type,name)) this.classTrainings[type].push(name);
                } else {
                    this.classTrainings[type]=this.classTrainings[type].filter(v=>v!==name);
                    this.backgroundTrainings[type]=this.backgroundTrainings[type].filter(v=>v!==name);
                }
            },
            syncExpertiseSkills() {
                const trained=new Set([...this.classSkills,...this.backgroundSkills]);
                this.expertiseSkills=this.expertiseSkills.filter(key=>trained.has(key));
            },
            get uniqueSkillCount() {return new Set([...this.classSkills,...this.backgroundSkills]).size;},
            get expertiseCount() {return this.expertiseSkills.length;},
            get selectedAbilityHistory() {
                return this.abilityRollHistory.find(item => item.key === this.abilitySelected) || null;
            },
            get standardRemaining() {
                const taken = Object.values(this.abilityStandardSelections).filter(v => v !== null && v !== '');
                return this.standardArray.filter(value => !taken.includes(value));
            },
            availableStandardScores(key) {
                const own = this.abilityStandardSelections[key];
                return this.standardArray.filter(value => value === own || this.standardRemaining.includes(value));
            },
            assignStandardAbility(key, raw) {
                if (!this.abilityKeys.includes(key) || this.abilityMethod !== 'standard') return;
                const value = raw === '' ? null : Number(raw);
                if (value !== null && (!this.standardArray.includes(value)
                    || this.abilityKeys.some(other => other !== key && this.abilityStandardSelections[other] === value))) {
                    return;
                }
                this.abilityStandardSelections[key] = value;
                this.abilityScores[key] = value === null ? '' : value;
            },
            setAbilityMethod(method) {
                if (this.abilityRollBusy || !['manual','standard','rolled'].includes(method)) return;
                if (this.abilityMethod === 'rolled' && method !== 'rolled') this.stopForgeDice();
                if (method === 'standard') {
                    this.abilityMethod = 'standard';
                    this.abilityKeys.forEach(key => {
                        this.abilityStandardSelections[key] = null;
                        this.abilityScores[key] = '';
                    });
                } else if (method === 'rolled' && this.abilityMethod !== 'rolled') {
                    // Cada atributo deve receber seus quatro dados reais.
                    // Os antigos 10 manuais não podem contar como resultados rolados.
                    this.abilityMethod = 'rolled';
                    this.abilityRollHistory = [];
                    this.abilityKeys.forEach(key => {this.abilityScores[key] = '';});
                } else {
                    this.abilityMethod = method;
                    if (method === 'manual') this.abilityKeys.forEach(key => {
                        const value = Number(this.abilityScores[key]);
                        if (!Number.isInteger(value) || value < 1 || value > 20) this.abilityScores[key] = 10;
                    });
                }
                this.abilityRollError = '';
                this.abilityRollProgress = 0;
            },
            useStandardAbilities() {
                if (this.abilityMethod === 'standard' || this.abilityRollBusy) return;
                this.setAbilityMethod('standard');
                this.abilityRollHistory = [];
                this.abilityDiceFaces = [null,null,null,null];
                this.abilityDiscardIndex = -1;
            },
            sleepForRoll(ms) { return new Promise(resolve => setTimeout(resolve, ms)); },
            async requestAbilityRoll(count) {
                const response = await fetch(this.diceRollEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({expression: count + 'd6'}),
                });
                const json = await response.json();
                const rolls = json?.data?.rolls;
                if (!response.ok || !json?.success || !Array.isArray(rolls) || rolls.length !== count ||
                    !rolls.every(value => Number.isInteger(Number(value)) && Number(value) >= 1 && Number(value) <= 6)) {
                    throw new Error(json?.message || 'Não foi possível rolar os dados.');
                }
                return rolls.map(Number);
            },
            abilityRollRecord(key, faces) {
                const dropped = faces.indexOf(Math.min(...faces));
                return {key, faces:[...faces], discarded:dropped,
                    total:faces.reduce((sum,number,index) => sum + (index === dropped ? 0 : number), 0)};
            },
            showAbilityRoll(key) {
                if (this.abilityRollBusy || this.abilityMethod!=='rolled') return;
                const record=this.abilityRollHistory.find(item=>item.key===key);
                if(!record)return;
                this.abilitySelected=key;
                this.abilityDiceFaces=[...record.faces];
                this.abilityDiscardIndex=record.discarded;
                this.forgeTarget=key;
                this.forgeDiceContext='ability';
                this.forgeRoundId++;
                this.abilityRollError='';
                window.dispatchEvent(new CustomEvent('spellbound:forge-prepare-dice',{
                    detail:{mode:'ability',round:this.forgeRoundId,target:key,restore:true}
                }));
            },
            beginForgeAbilityRoll(key) {
                if (!this.abilityKeys.includes(key)) return;
                if (this.abilityRollBusy) {
                    if (key === this.forgeTarget) window.dispatchEvent(new CustomEvent('spellbound:forge-show-tray'));
                    return;
                }
                if (this.abilityMethod !== 'rolled') this.setAbilityMethod('rolled');
                this.abilityRollBusy = true;
                this.forgeDiceContext = 'ability';
                this.forgeRoundId++;
                this.forgeBatch = false;
                this.abilityScores[key] = '';
                this.abilityRollHistory = this.abilityRollHistory.filter(item => item.key !== key);
                this.forgeTarget = key;
                this.forgePendingFaces = [];
                this.forgePendingDice = [];
                this.abilitySelected = key;
                this.abilityRollProgress = 1;
                this.abilityRollError = '';
                this.abilityDiceFaces = [null,null,null,null];
                this.abilityDiscardIndex = -1;
                window.dispatchEvent(new CustomEvent('spellbound:forge-prepare-dice', {
                    detail: {mode:'ability',round:this.forgeRoundId,target:key, all:false,reset:true}
                }));
            },
            rollAbilityScore(key) { this.beginForgeAbilityRoll(key); },
            openAbilityDiceTray() {
                if (this.abilityRollBusy) {
                    window.dispatchEvent(new CustomEvent('spellbound:forge-show-tray'));
                    return;
                }
                const current=this.abilitySelected || 'strength';
                if(this.abilityRollHistory.some(item=>item.key===current)){
                    this.showAbilityRoll(current);
                    return;
                }
                this.beginForgeAbilityRoll(current);
            },
            resetForgeFour() {
                if (!this.abilityRollBusy || this.forgeBatch) return;
                this.forgePendingFaces=[];
                this.forgePendingDice=[];
                this.abilityRollError='';
                this.abilityDiceFaces=[null,null,null,null];
                this.abilityDiscardIndex=-1;
                window.dispatchEvent(new CustomEvent('spellbound:forge-prepare-dice', {
                    detail:{mode:'ability',round:++this.forgeRoundId,target:this.forgeTarget,all:false,reset:true}
                }));
            },
            rollAllAbilityScores() {
                this.openAbilityDiceTray();
            },
            restoreForgeDiceResults(event) {
                const detail=event?.detail||{};
                if(Number(detail.round)!==this.forgeRoundId)return;
                const dice=Array.isArray(detail.dice)?detail.dice:[];
                if(detail.mode==='ability' && detail.target===this.forgeTarget){
                    this.forgePendingDice=dice.map(row=>({id:Number(row.id),face:Number(row.face)}))
                        .filter(row=>Number.isInteger(row.id)&&Number.isInteger(row.face)&&row.face>=1&&row.face<=6);
                    this.forgePendingFaces=this.forgePendingDice.map(row=>row.face);
                }
            },
            requestForgeSingleReroll(event) {
                const detail=event?.detail||{};
                if(Number(detail.round)!==this.forgeRoundId || this.forgeDiceInFlight)return;
                if(detail.mode==='life') {
                    if(this.createTab!=='vitality'||this.hpMethod!=='rolled')return;
                    const entry=this.lifeDiceList.find(row=>row.key===String(detail.key));
                    if(!entry||!this.lifeDieIsRolled(entry))return;
                    const updated={...this.lifeRollResults};
                    delete updated[entry.key];
                    this.lifeRollResults=updated;
                    this.lifeRollBusy=true;
                    this.lifeRollError='';
                    detail.accepted=true;
                    return;
                }
                if(detail.mode!=='ability'||this.createTab!=='abilities'
                    ||this.abilityMethod!=='rolled'||detail.target!==this.forgeTarget)return;
                const remaining=Array.isArray(detail.remaining)?detail.remaining:[];
                const validated=remaining.map(row=>({id:Number(row.id),face:Number(row.face)}))
                    .filter(row=>Number.isInteger(row.id)&&Number.isInteger(row.face)&&row.face>=1&&row.face<=6);
                if(validated.length>3 || !this.abilityKeys.includes(detail.target))return;
                this.abilityRollBusy=true;
                this.abilitySelected=detail.target;
                this.abilityScores[detail.target]='';
                this.forgePendingDice=validated;
                this.forgePendingFaces=validated.map(row=>row.face);
                this.abilityDiceFaces=[null,null,null,null];
                this.abilityDiscardIndex=-1;
                this.abilityRollHistory=this.abilityRollHistory.filter(row=>row.key!==detail.target);
                this.abilityRollError='';
                detail.accepted=true;
            },
            collectForgeDice(event) {
                if (!this.createOpen) return;
                const detail=event?.detail||{};
                if(Number(detail.round)!==this.forgeRoundId)return;
                const incoming=Array.isArray(detail.dice)?detail.dice:[];
                if (!incoming.length)return;
                if (detail.mode==='life') {
                    if(this.createTab!=='vitality'||this.hpMethod!=='rolled'||!this.lifeRollBusy)return;
                    const valid=new Map(this.lifeDiceList.map(entry=>[entry.key,entry]));
                    const next={...this.lifeRollResults};
                    incoming.forEach(row=>{
                        const key=String(row?.key??'');
                        const entry=valid.get(key),face=Number(row?.face);
                        // Faces já registradas são imutáveis até Rerolar todos.
                        if(!entry||Object.prototype.hasOwnProperty.call(next,key)||!Number.isInteger(face)
                            ||face<1||face>entry.sides)return;
                        next[key]=face;
                    });
                    this.lifeRollResults=next;
                    if(this.lifeRollComplete){this.lifeRollBusy=false;}
                    return;
                }
                if (this.createTab !== 'abilities' || this.abilityMethod !== 'rolled' || !this.abilityRollBusy) return;
                const target=detail.target;
                if(!this.abilityKeys.includes(target)||target!==this.forgeTarget)return;
                incoming.forEach(row=>{
                    const id=Number(row?.id),face=Number(row?.face);
                    if(!Number.isInteger(id)||!Number.isInteger(face)||face<1||face>6)return;
                    // Cada posição só pontua uma vez, mesmo com eventos repetidos.
                    if(this.forgePendingDice.some(die=>die.id===id)||this.forgePendingDice.length>=4)return;
                    this.forgePendingDice.push({id,face});
                });
                this.forgePendingFaces=this.forgePendingDice.map(die=>die.face);
                if(this.forgePendingFaces.length<4)return;
                const record=this.abilityRollRecord(target,this.forgePendingFaces);
                this.abilityScores[target]=record.total;
                this.abilityRollHistory=[record,...this.abilityRollHistory.filter(item=>item.key!==target)];
                this.abilityDiceFaces=[...record.faces];
                this.abilityDiscardIndex=record.discarded;
                this.abilityRollBusy=false;
                this.abilityRollProgress=0;
                this.forgeBatch=false;
            },
            handleForgeDiceError(event) {
                if(this.forgeDiceContext==='life'){
                    this.lifeRollError=event?.detail?.message||'Falha ao lançar os Dados de Vida.';
                    this.lifeRollBusy=false;
                    return;
                }
                this.abilityRollError=event?.detail?.message||'Falha na rolagem dos dados.';
                this.abilityRollBusy=false;
                this.forgeBatch=false;
                this.forgePendingFaces=[];
                this.forgePendingDice=[];
                this.abilityRollProgress=0;
            },
            stopForgeDice() {
                this.abilityRollBusy=false;
                this.lifeRollBusy=false;
                this.forgePendingDice=[];
                this.forgeBatch=false;
                this.forgePendingFaces=[];
                this.abilityRollProgress=0;
                window.dispatchEvent(new CustomEvent('spellbound:forge-close-dice'));
            },
            addSpeciesTrait() {
                if (this.speciesTraits.length < 12) {
                    this.speciesTraitsOpen = true;
                    this.speciesTraits.push({ name: '', description: '' });
                }
            },
            removeSpeciesTrait(index) {
                this.speciesTraits.splice(index, 1);
                if (!this.speciesTraits.length) this.speciesTraitsOpen = false;
            },
            toggleSpeciesTraits() {
                if (this.speciesTraitsOpen) {
                    this.speciesTraitsOpen = false;
                } else {
                    this.speciesTraitsOpen = true;
                    if (!this.speciesTraits.length) this.addSpeciesTrait();
                }
            },
            openFeatureModal(type) {
                if (type === 'species' && !this.forgeSpecies.trim()) return;
                if (['origin', 'proficiencies', 'background_abilities'].includes(type) && !this.forgeBackground.trim()) return;
                if (!['species', 'origin', 'proficiencies', 'background_abilities'].includes(type)) return;
                if (type === 'species') {
                    this.speciesTraitsOpen = true;
                    if (!this.speciesTraits.length) this.addSpeciesTrait();
                }
                if (type === 'origin') this.originFeatOpen = true;
                if (type === 'proficiencies') this.backgroundProficienciesOpen = true;
                this.featureModal = type;
            },
            closeFeatureModal() {
                this.featureModal = null;
            },
            get featureModalTitle() {
                return ({ species:'Habilidades de espécie',origin:'Talento de origem',
                    proficiencies:'Proficiências do antecedente',
                    background_abilities:'Atributos do antecedente' })[this.featureModal] || 'Configuração';
            },
            createTabs: ['identity', 'classes', 'proficiencies', 'abilities', 'vitality', 'settings'],
            goCreateTab(target) {
                if (!this.createTabs.includes(target)) return;
                if ((this.createTab === 'abilities' || this.createTab === 'vitality')
                    && target !== this.createTab) this.stopForgeDice();
                this.createTab = target;
                if(target==='abilities' && this.abilityMethod==='rolled'
                    && this.abilityRollHistory.some(row=>row.key===this.abilitySelected))
                    this.$nextTick(()=>this.showAbilityRoll(this.abilitySelected));
                if(target==='vitality' && this.hpMethod==='rolled' && this.lifeRollCount>0)
                    this.$nextTick(()=>this.showForgeLifeDice());
            },
            reportCreateInvalid(field) {
                const section = field.closest('[data-create-tab]');
                if (section) this.createTab = section.dataset.createTab;
                const feature = field.closest('[data-create-feature]');
                if (feature) this.featureModal = feature.dataset.createFeature;
                this.$nextTick(() => {
                    field.focus();
                    field.reportValidity();
                });
            },
            nextCreateTab() {
                const current = this.$refs.createForm?.querySelector('[data-create-tab=' + this.createTab + ']');
                const invalid = [...(current?.querySelectorAll('input,select,textarea') || [])]
                    .find(field => field.willValidate && !field.checkValidity());
                if (invalid) { this.reportCreateInvalid(invalid); return; }
                if ((this.createTab==='abilities' || this.createTab==='identity') && this.abilityBonusError) {
                    if (this.createTab==='identity') {
                        if (this.abilityBonusError.includes('20')) this.createTab='abilities';
                        else this.featureModal='background_abilities';
                    }
                    return;
                }
                if (this.createTab === 'vitality' && this.hpMethod === 'rolled' && !this.lifeRollComplete) {
                    this.lifeRollError='Conclua todos os Dados de Vida ou escolha Valor padrão/Manual.';
                    return;
                }
                const modalInvalid = [...(this.$refs.createForm?.querySelectorAll('[data-create-feature] input, [data-create-feature] textarea') || [])]
                    .find(field => field.willValidate && !field.checkValidity());
                if (modalInvalid) { this.reportCreateInvalid(modalInvalid); return; }
                this.featureModal = null;
                const index = this.createTabs.indexOf(this.createTab);
                const nextTab = this.createTabs[Math.min(index + 1, this.createTabs.length - 1)];
                if ((this.createTab === 'abilities' || this.createTab === 'vitality')
                    && nextTab !== this.createTab) this.stopForgeDice();
                this.createTab = nextTab;
            },
            submitCreate(event) {
                if (this.abilityBonusError) {
                    event.preventDefault();
                    this.createTab='abilities';
                    return;
                }
                if (this.hpMethod === 'rolled' && !this.lifeRollComplete) {
                    event.preventDefault();
                    this.createTab='vitality';
                    this.lifeRollError='Conclua os Dados de Vida antes de criar o personagem.';
                    return;
                }
                const invalid = [...event.target.elements]
                    .find(field => field.willValidate && !field.checkValidity());
                if (!invalid) return;
                event.preventDefault();
                this.reportCreateInvalid(invalid);
            },

            addClass() {

                if (this.classes.length < 6) {

                    this.multiClass = true;

                    this.classes.push({
                        class: '', subclass: '', level: 1,
                        hit_die: 8, spellcaster: false,
                        spell_ability: 'wisdom', caster_type: 'full'
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

            openPhotoEditor() {
                if (!this.imagePreview) {
                    this.$refs.forgeCropInput?.click();
                    return;
                }
                this.photoCropSource = this.imagePreview;
                this.photoCropOpen = true;
                this.photoCropReady = false;
                this.photoCropError = '';
                this.photoCropZoom = 1;
                this.photoCropOffsetX = 0;
                this.photoCropOffsetY = 0;
            },
            startPhotoCrop(event) {
                const file = event.target.files?.[0];
                event.target.value = '';
                if (!file) return;
                if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
                    this.photoCropOpen = true;
                    this.photoCropReady = false;
                    this.photoCropError = 'Use uma imagem JPG, PNG ou WebP com até 5 MB.';
                    return;
                }
                if (this.photoCropTempUrl) URL.revokeObjectURL(this.photoCropTempUrl);
                this.photoCropTempUrl = URL.createObjectURL(file);
                this.photoCropSource = this.photoCropTempUrl;
                this.photoCropZoom = 1;
                this.photoCropOffsetX = 0;
                this.photoCropOffsetY = 0;
                this.photoCropReady = false;
                this.photoCropError = '';
                this.photoCropOpen = true;
            },
            photoCropImageLoaded(event) {
                const image = event.target;
                if (!image.naturalWidth || !image.naturalHeight) return;
                const frame = this.$refs.forgePhotoCropStage;
                this.photoCropFrameW = Math.max(1, frame?.clientWidth || 250);
                this.photoCropFrameH = Math.max(1, frame?.clientHeight || 300);
                const cover = Math.max(this.photoCropFrameW / image.naturalWidth, this.photoCropFrameH / image.naturalHeight);
                this.photoCropBaseW = image.naturalWidth * cover;
                this.photoCropBaseH = image.naturalHeight * cover;
                this.photoCropOffsetX = 0;
                this.photoCropOffsetY = 0;
                this.photoCropReady = true;
                this.clampPhotoCrop();
            },
            photoCropImageStyle() {
                return {
                    width: this.photoCropBaseW + 'px', height: this.photoCropBaseH + 'px',
                    transform: 'translate(calc(-50% + ' + this.photoCropOffsetX + 'px), calc(-50% + ' + this.photoCropOffsetY + 'px)) scale(' + this.photoCropZoom + ')'
                };
            },
            photoCropAllowance(axis) {
                const base = axis === 'x' ? this.photoCropBaseW : this.photoCropBaseH;
                const frame = axis === 'x' ? this.photoCropFrameW : this.photoCropFrameH;
                return Math.max(0, (base * this.photoCropZoom - frame) / 2);
            },
            photoCropPercent(axis) {
                const max = this.photoCropAllowance(axis);
                const offset = axis === 'x' ? this.photoCropOffsetX : this.photoCropOffsetY;
                return max > 0 ? Math.round(50 + offset / max * 50) : 50;
            },
            setPhotoCropPercent(axis, value) {
                const amount = ((Number(value) || 0) / 50 - 1) * this.photoCropAllowance(axis);
                if (axis === 'x') this.photoCropOffsetX = amount;
                else this.photoCropOffsetY = amount;
                this.clampPhotoCrop();
            },
            clampPhotoCrop() {
                const x = this.photoCropAllowance('x');
                const y = this.photoCropAllowance('y');
                this.photoCropOffsetX = Math.max(-x, Math.min(x, this.photoCropOffsetX));
                this.photoCropOffsetY = Math.max(-y, Math.min(y, this.photoCropOffsetY));
            },
            resetPhotoCrop() {
                this.photoCropZoom = 1;
                this.photoCropOffsetX = 0;
                this.photoCropOffsetY = 0;
                this.clampPhotoCrop();
            },
            startPhotoCropDrag(event) {
                if (!this.photoCropReady || event.button !== 0) return;
                const frame = this.$refs.forgePhotoCropStage;
                const bounds = frame.getBoundingClientRect();
                this.photoCropPointer = {
                    id:event.pointerId, x:event.clientX, y:event.clientY,
                    dx:this.photoCropOffsetX, dy:this.photoCropOffsetY,
                    scale:this.photoCropFrameW / Math.max(1,bounds.width)
                };
                frame.setPointerCapture?.(event.pointerId);
            },
            movePhotoCropDrag(event) {
                const drag = this.photoCropPointer;
                if (!drag || drag.id !== event.pointerId) return;
                this.photoCropOffsetX = drag.dx + (event.clientX - drag.x) * drag.scale;
                this.photoCropOffsetY = drag.dy + (event.clientY - drag.y) * drag.scale;
                this.clampPhotoCrop();
            },
            stopPhotoCropDrag(event) {
                if (this.photoCropPointer?.id === event.pointerId) this.photoCropPointer = null;
            },
            async applyPhotoCrop() {
                const image = this.$refs.forgePhotoCropImage;
                if (!this.photoCropReady || !image?.complete || this.photoCropSaving) return;
                this.photoCropSaving = true;
                this.photoCropError = '';
                try {
                    const width = 640, height = 768;
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const context = canvas.getContext('2d');
                    if (!context) throw new Error('Não foi possível preparar o recorte.');
                    const scale = width / this.photoCropFrameW;
                    const drawW = this.photoCropBaseW * this.photoCropZoom * scale;
                    const drawH = this.photoCropBaseH * this.photoCropZoom * scale;
                    context.drawImage(image,
                        (width - drawW) / 2 + this.photoCropOffsetX * scale,
                        (height - drawH) / 2 + this.photoCropOffsetY * scale,
                        drawW, drawH);
                    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                    if (!blob) throw new Error('Não foi possível salvar o recorte.');
                    if (blob.size > 5 * 1024 * 1024) throw new Error('O recorte ultrapassou 5 MB. Escolha outra foto.');
                    const file = new File([blob], 'retrato-personagem.png', {type:'image/png'});
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    this.$refs.forgeImageInput.files = transfer.files;
                    if (this.photoPreviewUrl) URL.revokeObjectURL(this.photoPreviewUrl);
                    this.photoPreviewUrl = URL.createObjectURL(file);
                    this.imagePreview = this.photoPreviewUrl;
                    this.closePhotoCrop();
                } catch (error) {
                    this.photoCropError = error?.message || 'Não foi possível aplicar a imagem.';
                } finally {
                    this.photoCropSaving = false;
                }
            },
            closePhotoCrop() {
                this.photoCropOpen = false;
                this.photoCropReady = false;
                this.photoCropPointer = null;
                this.photoCropSource = null;
                if (this.photoCropTempUrl) URL.revokeObjectURL(this.photoCropTempUrl);
                this.photoCropTempUrl = null;
            },
            closeCreate() {
                if (this.photoCropOpen) { this.closePhotoCrop(); return; }
                if (this.featureModal) { this.closeFeatureModal(); return; }
                this.stopForgeDice();
                this.createOpen = false;
            }

        }"

        class="characters-index-v2 min-h-full px-5 py-7 md:px-8"

        @keydown.escape.window="closeCreate()"
        @spellbound:forge-dice-result.window="collectForgeDice($event)"
        @spellbound:forge-dice-restored.window="restoreForgeDiceResults($event)"
        @spellbound:forge-request-die-reroll.window="requestForgeSingleReroll($event)"
        @spellbound:forge-roll-error.window="handleForgeDiceError($event)"
        @spellbound:forge-inflight.window="forgeDiceInFlight=!!$event.detail.busy"

    >

        <div class="mx-auto max-w-[1940px]">

            {{-- ================================================================
                 CABEÇALHO — MESMA LINGUAGEM VISUAL DO COFRE DE NPCS
                 Apenas título e ação de criar personagem, sem controles adicionais.
            ================================================================= --}}

            <header class="character-vault-head" aria-labelledby="character-vault-title">
                <div class="character-vault-head__intro">
                    <div class="character-vault-head__identity">
                        <h1 id="character-vault-title" class="character-vault-head__title">Personagens</h1>
                        <span class="character-vault-head__stroke" aria-hidden="true"></span>
                    </div>

                    <div class="character-vault-head__actions">
                        <button type="button" class="character-vault-action character-vault-action--primary"
                                x-on:click="createOpen = true" aria-label="Forjar personagem" title="Forjar personagem">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span>Forjar personagem</span>
                        </button>
                    </div>
                </div>

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

                            $trackTotalHp =
                                max(
                                    1,
                                    $effectiveMaxHp
                                );

                            $baseMaxPercent =
                                $trackTotalHp > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            round(
                                                (
                                                    $baseMaxHp
                                                    /
                                                    $trackTotalHp
                                                )
                                                * 100,
                                                3
                                            )
                                        )
                                    )
                                    : 0;

                            $bonusMaxPercent =
                                $trackTotalHp > 0 && $bonusMaxHp > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            round(
                                                (
                                                    $bonusMaxHp
                                                    /
                                                    $trackTotalHp
                                                )
                                                * 100,
                                                3
                                            )
                                        )
                                    )
                                    : 0;

                            $maxReductionPercent =
                                $trackTotalHp > 0 && $maxHpReduction > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            round(
                                                (
                                                    $maxHpReduction
                                                    /
                                                    $trackTotalHp
                                                )
                                                * 100,
                                                3
                                            )
                                        )
                                    )
                                    : 0;

                            $normalFillAmount =
                                min(
                                    $displayHpCurrent,
                                    $baseMaxHp
                                );

                            $bonusFilledAmount =
                                max(
                                    0,
                                    min(
                                        $displayHpCurrent - $baseMaxHp,
                                        max(
                                            0,
                                            $usableMaxHp - $baseMaxHp
                                        )
                                    )
                                );

                            $normalFillPercent =
                                $trackTotalHp > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            round(
                                                (
                                                    $normalFillAmount
                                                    /
                                                    $trackTotalHp
                                                )
                                                * 100,
                                                3
                                            )
                                        )
                                    )
                                    : 0;

                            $bonusFilledPercent =
                                $trackTotalHp > 0 && $bonusFilledAmount > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            round(
                                                (
                                                    $bonusFilledAmount
                                                    /
                                                    $trackTotalHp
                                                )
                                                * 100,
                                                3
                                            )
                                        )
                                    )
                                    : 0;

                            $totalCurrentFillPercent =
                                max(
                                    0,
                                    min(
                                        100,
                                        $normalFillPercent + $bonusFilledPercent
                                    )
                                );

                            $tempVisualAmount =
                                min(
                                    $temporaryHp,
                                    max(
                                        0,
                                        $trackTotalHp - $displayHpCurrent
                                    )
                                );

                            $temporaryFillPercent =
                                $trackTotalHp > 0 && $tempVisualAmount > 0
                                    ? max(
                                        0,
                                        min(
                                            100,
                                            round(
                                                (
                                                    $tempVisualAmount
                                                    /
                                                    $trackTotalHp
                                                )
                                                * 100,
                                                3
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
                                'Normal';

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
                                    'Inconsciente';

                                $statusKey =
                                    'down';

                            } elseif ($currentHp <= 0) {
                                $status =
                                    'Inconsciente';

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

                            $allSpellRows = $preparedSpellsByCharacter->get(
                                $character->id,
                                collect()
                            );

                            $preparedSpells = $allSpellRows
                                ->filter(fn ($spell) => (int) ($spell->level ?? 0) > 0 && ((bool) ($spell->prepared ?? false) || (bool) ($spell->always_prepared ?? false)))
                                ->values();

                            $cantripSpells = $allSpellRows
                                ->filter(fn ($spell) => (int) ($spell->level ?? 0) === 0 && ((bool) ($spell->known ?? false) || (bool) ($spell->prepared ?? false) || (bool) ($spell->always_prepared ?? false)))
                                ->values();

                            $preparedSpellCount = $preparedSpells->count();
                            $cantripSpellCount = $cantripSpells->count();

                            $rawSpellSlots = $character->spellSlots instanceof \Illuminate\Support\Collection
                                ? $character->spellSlots
                                : collect($character->spellSlots ?? []);

                            $spellSlots = $rawSpellSlots
                                ->map(function ($slot) {
                                    $level = (int) (data_get($slot, 'level') ?? data_get($slot, 'spell_level') ?? data_get($slot, 'slot_level') ?? 0);
                                    $max = data_get($slot, 'max_slots');
                                    if ($max === null) {
                                        $max = data_get($slot, 'total_slots', data_get($slot, 'total', data_get($slot, 'slots_max', data_get($slot, 'max', 0))));
                                    }
                                    $current = data_get($slot, 'current_slots');
                                    if ($current === null) {
                                        $current = data_get($slot, 'remaining_slots', data_get($slot, 'available_slots', data_get($slot, 'remaining', data_get($slot, 'current', null))));
                                    }
                                    if ($current === null) {
                                        $used = (int) data_get($slot, 'used_slots', data_get($slot, 'used', 0));
                                        $current = max(0, (int) $max - $used);
                                    }
                                    return [
                                        'level' => $level,
                                        'current' => max(0, (int) $current),
                                        'max' => max(0, (int) $max),
                                    ];
                                })
                                ->filter(fn ($slot) => $slot['level'] > 0 && $slot['max'] > 0)
                                ->sortBy('level')
                                ->values();

                            $spellPanelEnabled = $preparedSpellCount > 0 || $cantripSpellCount > 0 || $spellSlots->isNotEmpty();

                            $diaryEntries = $diaryEntriesByCharacter->get(
                                $character->id,
                                collect()
                            );
                        @endphp


                        <article
                            x-data="{
                                rosterPanel: 'overview',
                                rosterReturning: false,
                                rosterTurning: false,
                                rosterSpellView: '{{ $preparedSpellCount > 0 ? 'prepared' : 'cantrips' }}',
                                rosterSpellReturning: false,
                                showSpellSlots: false,
                                switchRoster(target) {
                                    if (target === this.rosterPanel || this.rosterTurning) return;
                                    const folios = ['overview', 'inventory', 'diary', 'spells'];
                                    this.rosterReturning = folios.indexOf(target) < folios.indexOf(this.rosterPanel);
                                    this.showSpellSlots = false;
                                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                                        this.rosterPanel = target;
                                        return;
                                    }
                                    this.rosterTurning = true;
                                    window.setTimeout(() => { this.rosterPanel = target; }, 350);
                                    window.setTimeout(() => { this.rosterTurning = false; }, 730);
                                },
                                switchSpellView(target) {
                                    if (target === this.rosterSpellView) return;
                                    this.rosterSpellReturning = target === 'prepared';
                                    this.showSpellSlots = false;
                                    this.rosterSpellView = target;
                                }
                            }"
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
                                <div class="character-roster-portrait-art">

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
                                    @if (!$isDead && in_array($statusKey, ['wounded', 'critical', 'down'], true))
                                        <span class="roster-wound-aura" aria-hidden="true"></span>
                                        <span class="roster-wound-fissures" aria-hidden="true"></span>
                                        <span class="roster-wound-sparks" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
                                    @endif
                                </div>

                                <div class="character-roster-level">
                                    <small>Nível</small>
                                    <strong>{{ $character->level }}</strong>
                                </div>

                                <div class="character-roster-statebar {{ $statusKey }}" aria-label="Estado do personagem: {{ $status }}">
                                    <span class="character-roster-statebar-label">Estado</span>
                                    <span class="character-roster-statebar-value">
                                        <i class="character-roster-status-dot {{ $statusKey }}" aria-hidden="true"></i>
                                        {{ $status }}
                                    </span>
                                </div>

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
                            <div class="character-roster-body"
                                 x-bind:class="{ 'is-leaf-turning': rosterTurning, 'is-leaf-reverse': rosterReturning }"
                                 x-bind:aria-busy="rosterTurning ? 'true' : 'false'">

                                {{-- Uma folha de pergaminho percorre esta página durante a troca; o retrato e os marcadores ficam estáveis. --}}
                                <span class="character-roster-turn-sheet" aria-hidden="true"></span>
                                <span class="character-roster-turn-crease" aria-hidden="true"></span>

                                {{-- A identidade começa pelo nome; as demais informações acompanham a leitura. --}}
                                <div class="character-roster-heading" x-show="rosterPanel === 'overview'" x-cloak>
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


                                {{-- Marcadores de páginas encaixados na borda do dossiê. --}}
                                <div class="character-roster-panel-shell" x-bind:class="{ 'is-detail': rosterPanel !== 'overview' }">
                                    <nav class="character-roster-tabrail" aria-label="Seções do dossiê de {{ $character->name }}">

                                        <div class="character-roster-tab-actions">
                                            <button type="button" class="character-roster-tab"
                                                    x-bind:class="{ 'is-active': rosterPanel === 'overview' }"
                                                    x-bind:aria-pressed="rosterPanel === 'overview' ? 'true' : 'false'"
                                                    x-on:click="switchRoster('overview')"
                                                    title="Voltar para o resumo do personagem">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 4v16M5 9.5 12 3l7 6.5M7 20.5h10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span class="character-roster-tab-label">Resumo</span>
                                            </button>
                                            <button type="button" class="character-roster-tab"
                                                    x-bind:class="{ 'is-active': rosterPanel === 'inventory' }"
                                                    x-bind:aria-pressed="rosterPanel === 'inventory' ? 'true' : 'false'"
                                                    x-on:click="switchRoster('inventory')"
                                                    title="{{ $inventoryItems->count() }} itens no inventário">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 8h16v11H4zM8 8V5.5A2.5 2.5 0 0 1 10.5 3h3A2.5 2.5 0 0 1 16 5.5V8M4 12h16M12 12v3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span class="character-roster-tab-label">Inventário</span>
                                            </button>
                                            <button type="button" class="character-roster-tab"
                                                    x-bind:class="{ 'is-active': rosterPanel === 'diary' }"
                                                    x-bind:aria-pressed="rosterPanel === 'diary' ? 'true' : 'false'"
                                                    x-on:click="switchRoster('diary')"
                                                    title="Abrir diário do personagem">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M6 4.5h9.5A2.5 2.5 0 0 1 18 7v12.5a31.7 31.7 0 0 0-6-1c-2.6 0-4.6.4-6 1V7A2.5 2.5 0 0 1 8.5 4.5H18M9 8h5M9 11h6M9 14h4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span class="character-roster-tab-label">Diário</span>
                                            </button>
                                            @if ($spellPanelEnabled)
                                                <button type="button" class="character-roster-tab"
                                                        x-bind:class="{ 'is-active': rosterPanel === 'spells' }"
                                                        x-bind:aria-pressed="rosterPanel === 'spells' ? 'true' : 'false'"
                                                        x-on:click="switchRoster('spells')"
                                                        title="{{ $preparedSpellCount }} {{ $preparedSpellCount === 1 ? 'feitiço preparado' : 'feitiços preparados' }}{{ $cantripSpellCount > 0 ? ' e ' . $cantripSpellCount . ' truques' : '' }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 20.5c-2.5-1.7-5-2.1-8-2.1V5.5c3.2-.3 5.8.4 8 2.2 2.2-1.8 4.8-2.5 8-2.2v12.9c-3 0-5.5.4-8 2.1ZM12 7.7v12.8M6.8 9c1.2 0 2.2.2 3.2.7M14 9.7c1-.5 2-.7 3.2-.7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    <span class="character-roster-tab-label">Feitiços</span>
                                                </button>
                                            @endif
                                        </div>
                                    </nav>

                                    <div class="character-roster-panel-stage" x-bind:class="{ 'is-returning': rosterReturning }">
                                        <section class="character-roster-panel character-roster-overview" x-show="rosterPanel === 'overview'">
                                            <div class="character-roster-campaign">
                                                <div class="character-roster-campaign-icon" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 19V6.5A1.5 1.5 0 015.5 5H18l2 3-2 3H5.5" />
                                                    </svg>
                                                </div>
                                                <div class="character-roster-campaign-copy">
                                                    <small>{{ $activeCampaign ? 'Campanha atual' : ($displayCampaign ? 'Campanha vinculada' : 'Campanha') }}</small>
                                                    <strong>{{ $displayCampaign?->name ?? 'Nenhuma campanha vinculada' }}</strong>
                                                </div>
                                                @if ($campaignCount > 1)
                                                    <span class="character-roster-campaign-more">+{{ $campaignCount - 1 }}</span>
                                                @endif
                                            </div>

                                            <div class="character-roster-combat">
                                                <div class="character-roster-stat character-roster-stat-ac">
                                                    <div class="character-roster-ac-shield {{ $armorClass >= 100 ? 'is-triple' : '' }}" role="img" aria-label="Classe de Armadura: {{ $armorClass }}">
                                                        <svg viewBox="0 0 80 100" fill="none" aria-hidden="true">
                                                            <path d="M40 7c9 5 18 8 28 10v28c0 21-12 36-28 47C24 81 12 66 12 45V17c10-2 19-5 28-10Z"
                                                                  fill="#fbf5ec" stroke="#bfa083" stroke-width="1.7" stroke-linejoin="round"/>
                                                            <path d="M40 14c8 4 14 6 21 8v23c0 16-9 29-21 38C28 74 19 61 19 45V22c7-2 13-4 21-8Z"
                                                                  stroke="#dfc6ac" stroke-width="1.05" stroke-linejoin="round"/>
                                                            <path d="M31 24q9-4 18 0M31 70q9 7 18 0" stroke="#c7a786" stroke-width=".85" opacity=".64" stroke-linecap="round"/>
                                                        </svg>
                                                        <div class="character-roster-ac-value"><small>CA</small><strong>{{ $armorClass }}</strong></div>
                                                    </div>
                                                </div>

                                                <div class="character-roster-stat character-roster-stat-pv" aria-label="Pontos de vida: {{ $displayHpCurrent }} de {{ $usableMaxHp }}{{ $temporaryHp > 0 ? ', ' . $temporaryHp . ' temporários' : '' }}{{ $bonusMaxHp > 0 ? ', bônus de ' . $bonusMaxHp . ' na vida máxima' : '' }}{{ $maxHpReduction > 0 ? ', redução de ' . $maxHpReduction . ' na vida máxima' : '' }}">
                                                    <span>Pontos de vida</span>
                                                    <div class="character-roster-pv-main">
                                                        <span class="pv-current">{{ $displayHpCurrent }}</span>
                                                        <span class="pv-slash">/</span>
                                                        <span class="pv-max {{ $maxHpReduction > 0 ? 'has-reduction' : ($bonusMaxHp > 0 ? 'has-bonus' : '') }}">{{ $usableMaxHp }}</span>
                                                    </div>
                                                    <div class="character-roster-hp-track {{ $temporaryHp > 0 ? 'has-temp' : '' }} {{ $bonusMaxHp > 0 ? 'has-bonus' : '' }} {{ $maxHpReduction > 0 ? 'has-reduction' : '' }}" role="meter" aria-label="Vida restante" aria-valuemin="0" aria-valuemax="{{ $usableMaxHp }}" aria-valuenow="{{ $displayHpCurrent }}">
                                                        @if ($bonusMaxPercent > 0)
                                                            <span class="character-roster-hp-bonus-zone" style="left:{{ $baseMaxPercent }}%;width:{{ $bonusMaxPercent }}%"></span>
                                                        @endif
                                                        <span class="character-roster-hp-fill {{ $statusKey }}" style="width:{{ $normalFillPercent }}%"></span>
                                                        @if ($bonusFilledPercent > 0)
                                                            <span class="character-roster-hp-bonus-fill" style="left:{{ $baseMaxPercent }}%;width:{{ $bonusFilledPercent }}%"></span>
                                                        @endif
                                                        @if ($temporaryFillPercent > 0)
                                                            <span class="character-roster-hp-temp-zone" style="left:{{ $totalCurrentFillPercent }}%;width:{{ $temporaryFillPercent }}%"></span>
                                                        @endif
                                                        @if ($maxReductionPercent > 0)
                                                            <span class="character-roster-hp-reduction-zone" style="width:{{ $maxReductionPercent }}%"></span>
                                                        @endif
                                                    </div>
                                                    @if ($temporaryHp > 0 || $bonusMaxHp > 0 || $maxHpReduction > 0)
                                                        <div class="character-roster-pv-modifiers">
                                                            @if ($temporaryHp > 0)
                                                                <span class="character-roster-pv-mod temp" title="{{ $temporaryHp }} pontos de vida temporários">
                                                                    <svg viewBox="0 0 24 26" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 2 21 6v7c0 5-3.6 8.4-9 11C6.6 21.4 3 18 3 13V6l9-4Z"/><path d="m7.5 12 3 3 6-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                    <strong>+{{ $temporaryHp }}</strong><small>temp.</small>
                                                                </span>
                                                            @endif
                                                            @if ($bonusMaxHp > 0)
                                                                <span class="character-roster-pv-mod bonus" title="Bônus de {{ $bonusMaxHp }} pontos na vida máxima"><strong>+{{ $bonusMaxHp }}</strong><small>máx.</small></span>
                                                            @endif
                                                            @if ($maxHpReduction > 0)
                                                                <span class="character-roster-pv-mod reduced" title="Redução de {{ $maxHpReduction }} pontos na vida máxima"><strong>−{{ $maxHpReduction }}</strong><small>máx.</small></span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="character-roster-stat character-roster-stat-initiative">
                                                    <span>Iniciativa</span>
                                                    <strong>{{ $initiative >= 0 ? '+' : '' }}{{ $initiative }}</strong>
                                                    
                                                </div>
                                                <div class="character-roster-stat character-roster-stat-speed">
                                                    <span>Movimento</span>
                                                    <strong>{{ $speed > 0 ? $speed . ' ft.' : '—' }}</strong>
                                                    
                                                </div>
                                            </div>
                                        </section>

                                                                                <section class="character-roster-panel character-roster-catalog" x-show="rosterPanel === 'inventory'" x-cloak>
                                            <div class="character-roster-catalog-header">
                                                <div class="character-roster-catalog-title"><strong>Inventário</strong><span>{{ $inventoryItems->count() }} {{ $inventoryItems->count() === 1 ? 'item' : 'itens' }} registrados</span></div>
                                            </div>
                                            @if ($inventoryItems->isNotEmpty())
                                                <div class="character-roster-catalog-list" aria-label="Itens de {{ $character->name }}">
                                                    @foreach ($inventoryItems as $inventoryItem)
                                                        <div class="character-roster-list-entry" data-rarity="{{ $inventoryItem->rarity ?? 'common' }}" title="{{ $inventoryItem->name ?? 'Item' }}">
                                                            <div class="character-roster-thumb">
                                                                @if ($inventoryItem->image_path)
                                                                    <img src="{{ route('characters.items.image', [
                                                                        'character' => $character->id,
                                                                        'item' => $inventoryItem->id,
                                                                        'v' => substr(sha1($inventoryItem->image_path), 0, 12),
                                                                    ], false) }}" alt="" loading="lazy" decoding="async">
                                                                @else
                                                                    <svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="m16 3 11 7v12l-11 7L5 22V10L16 3Z" stroke="currentColor" stroke-width="1.2"/><path d="m16 9 5.5 7L16 23l-5.5-7L16 9Z" stroke="currentColor" stroke-width="1.2"/></svg>
                                                                @endif
                                                            </div>
                                                            <div class="character-roster-entry-info">
                                                                <strong>{{ $inventoryItem->name ?? 'Item sem nome' }}</strong>
                                                                <span class="character-roster-rarity">{{ $inventoryItem->rarity_label ?? 'Comum' }}</span>
                                                            </div>
                                                            @if ($inventoryItem->equipped)
                                                                <span class="character-roster-equipped" title="Item equipado" aria-label="Equipado">
                                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 11V5a2 2 0 0 1 4 0v4-2a2 2 0 0 1 4 0v3a2 2 0 0 1 3 1.7v4.1c0 3-2 5.2-5.6 5.2h-2.8c-2.2 0-3.5-.9-4.6-2.2L4.3 15a1.7 1.7 0 0 1 2.6-2.1L9 15v-4Z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                    <span>Equipado</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="character-roster-panel-empty">Este personagem ainda não possui itens.</p>
                                            @endif
                                        </section>

                                        <section class="character-roster-panel character-roster-catalog" x-show="rosterPanel === 'diary'" x-cloak>
                                            <div class="character-roster-catalog-header">
                                                <div class="character-roster-catalog-title"><strong>Diário</strong><span>{{ $diaryEntries->count() > 0 ? $diaryEntries->count() . ' entradas recentes' : 'Páginas recentes' }}</span></div>
                                            </div>
                                            @if ($diaryEntries->isNotEmpty())
                                                <div class="character-roster-diary-list" aria-label="Diário de {{ $character->name }}">
                                                    @foreach ($diaryEntries as $diaryEntry)
                                                        @php
                                                            $diaryTitle = trim((string) ($diaryEntry->diary_title ?? ''));
                                                            $diaryBody = trim(strip_tags((string) ($diaryEntry->diary_body ?? '')));
                                                            $diaryPreview = \Illuminate\Support\Str::limit($diaryBody !== '' ? $diaryBody : 'Sem conteúdo disponível.', 180);
                                                            $diaryStamp = !empty($diaryEntry->diary_updated_at)
                                                                ? \Illuminate\Support\Carbon::parse($diaryEntry->diary_updated_at)->format('d/m/Y')
                                                                : null;
                                                        @endphp
                                                        <article class="character-roster-diary-entry">
                                                            <div class="character-roster-diary-mark" aria-hidden="true">✦</div>
                                                            <div class="character-roster-diary-copy">
                                                                <header>
                                                                    <strong>{{ $diaryTitle !== '' ? $diaryTitle : 'Entrada do diário' }}</strong>
                                                                    @if ($diaryStamp)
                                                                        <span>{{ $diaryStamp }}</span>
                                                                    @endif
                                                                </header>
                                                                <p>{{ $diaryPreview }}</p>
                                                            </div>
                                                        </article>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="character-roster-diary-empty">
                                                    <strong>O diário está silencioso por enquanto.</strong>
                                                    <p>Quando houver páginas registradas, elas aparecerão aqui em formato resumido. A ficha completa continua sendo o melhor lugar para escrever e ler o diário inteiro.</p>
                                                </div>
                                            @endif
                                        </section>

                                        @if ($spellPanelEnabled)
                                            <section class="character-roster-panel character-roster-catalog character-roster-spellbook" x-show="rosterPanel === 'spells'" x-cloak>
                                                <header class="character-roster-catalog-header character-roster-spellbook-header">
                                                    <div class="character-roster-catalog-title">
                                                        <strong>Grimório</strong>
                                                    </div>
                                                    <div class="character-roster-spellbook-tools">
                                                        <div class="character-roster-spellbook-switch" role="tablist" aria-label="Tipo de magia">
                                                            @if ($preparedSpellCount > 0)
                                                                <button type="button" role="tab" class="character-roster-spellbook-switch-tab"
                                                                        x-bind:class="{ 'is-active': rosterSpellView === 'prepared' }"
                                                                        x-bind:aria-selected="rosterSpellView === 'prepared' ? 'true' : 'false'"
                                                                        x-on:click="switchSpellView('prepared')">
                                                                    <span>Preparados</span><em>{{ $preparedSpellCount }}</em>
                                                                </button>
                                                            @endif
                                                            @if ($cantripSpellCount > 0)
                                                                <button type="button" role="tab" class="character-roster-spellbook-switch-tab"
                                                                        x-bind:class="{ 'is-active': rosterSpellView === 'cantrips' }"
                                                                        x-bind:aria-selected="rosterSpellView === 'cantrips' ? 'true' : 'false'"
                                                                        x-on:click="switchSpellView('cantrips')">
                                                                    <span>Truques</span><em>{{ $cantripSpellCount }}</em>
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @if ($spellSlots->isNotEmpty())
                                                            <div class="character-roster-slots-box">
                                                                <button type="button" class="character-roster-slots-toggle" x-bind:class="{ 'is-open': showSpellSlots }"
                                                                        x-on:click="showSpellSlots = !showSpellSlots" x-bind:aria-expanded="showSpellSlots ? 'true' : 'false'"
                                                                        title="Espaços de magia disponíveis" aria-label="Consultar espaços de magia disponíveis">
                                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 2.5 4.5 7v5c0 4.6 3 7.5 7.5 9 4.5-1.5 7.5-4.4 7.5-9V7L12 2.5Z" stroke-width="1.5"/><path d="M12 7v9M8.5 11.5h7" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                                    <span>Slots</span>
                                                                </button>
                                                                <div class="character-roster-slots-popover" x-show="showSpellSlots" x-cloak x-transition.opacity>
                                                                    @foreach ($spellSlots as $slot)
                                                                        <div class="character-roster-slot-row">
                                                                            <span>{{ $slot['level'] }}º círculo</span>
                                                                            <strong>{{ $slot['current'] }}/{{ $slot['max'] }}</strong>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </header>

                                                <div class="character-roster-spellbook-pages" x-bind:class="{ 'is-returning': rosterSpellReturning }">
                                                    @if ($preparedSpells->isNotEmpty())
                                                        <div class="character-roster-catalog-list character-roster-spellbook-list" role="tabpanel"
                                                             aria-label="Feitiços preparados de {{ $character->name }}"
                                                             x-show="rosterSpellView === 'prepared'" x-cloak
                                                             x-transition:enter="roster-spell-enter" x-transition:enter-start="roster-spell-enter-start" x-transition:enter-end="roster-spell-enter-end"
                                                             x-transition:leave="roster-spell-leave" x-transition:leave-start="roster-spell-leave-start" x-transition:leave-end="roster-spell-leave-end">
                                                            @foreach ($preparedSpells as $preparedSpell)
                                                                <div class="character-roster-list-entry character-roster-spell-entry" data-school="{{ strtolower($preparedSpell->school ?? '') }}" title="{{ $preparedSpell->name }}">
                                                                    <div class="character-roster-thumb character-roster-spell-thumb">
                                                                        @if (!empty($preparedSpell->personal_media_path))
                                                                            <img src="{{ route('characters.spellbook.spells.media.show', [
                                                                                'character' => $character->id,
                                                                                'spell' => $preparedSpell->spell_id,
                                                                            ], false) }}" alt="" loading="lazy" decoding="async">
                                                                        @else
                                                                            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="m16 2 3.1 10.9L30 16l-10.9 3.1L16 30l-3.1-10.9L2 16l10.9-3.1L16 2Z" stroke="currentColor" stroke-width="1.15"/><circle cx="16" cy="16" r="4" stroke="currentColor" stroke-width="1.3"/><path d="M5.5 5.5 9 9M26.5 5.5 23 9M5.5 26.5 9 23M26.5 26.5 23 23" stroke="currentColor" stroke-width="1.1"/></svg>
                                                                        @endif
                                                                    </div>
                                                                    <div class="character-roster-entry-info">
                                                                        <strong>{{ $preparedSpell->name }}</strong>
                                                                        <span class="character-roster-spell-level">{{ $preparedSpell->level }}º círculo</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if ($cantripSpells->isNotEmpty())
                                                        <div class="character-roster-catalog-list character-roster-spellbook-list character-roster-cantrip-list" role="tabpanel"
                                                             aria-label="Truques de {{ $character->name }}"
                                                             x-show="rosterSpellView === 'cantrips'" x-cloak
                                                             x-transition:enter="roster-spell-enter" x-transition:enter-start="roster-spell-enter-start" x-transition:enter-end="roster-spell-enter-end"
                                                             x-transition:leave="roster-spell-leave" x-transition:leave-start="roster-spell-leave-start" x-transition:leave-end="roster-spell-leave-end">
                                                            @foreach ($cantripSpells as $cantripSpell)
                                                                <div class="character-roster-list-entry character-roster-spell-entry" data-school="{{ strtolower($cantripSpell->school ?? '') }}" title="{{ $cantripSpell->name }}">
                                                                    <div class="character-roster-thumb character-roster-spell-thumb cantrip">
                                                                        @if (!empty($cantripSpell->personal_media_path))
                                                                            <img src="{{ route('characters.spellbook.spells.media.show', [
                                                                                'character' => $character->id,
                                                                                'spell' => $cantripSpell->spell_id,
                                                                            ], false) }}" alt="" loading="lazy" decoding="async">
                                                                        @else
                                                                            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M16 4.5c3.5 0 6.3 2.8 6.3 6.3 0 5-4.3 7-6.3 14.2-2-7.2-6.3-9.2-6.3-14.2 0-3.5 2.8-6.3 6.3-6.3Z" stroke="currentColor" stroke-width="1.2"/><path d="M13 12.7c.9-.8 1.8-1.2 3-1.2 1.1 0 2 .4 3 1.2" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/></svg>
                                                                        @endif
                                                                    </div>
                                                                    <div class="character-roster-entry-info">
                                                                        <strong>{{ $cantripSpell->name }}</strong>
                                                                        <span class="character-roster-spell-level">Truque</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if ($preparedSpells->isEmpty() && $cantripSpells->isEmpty())
                                                        <p class="character-roster-panel-empty">Nenhuma magia disponível nesta seleção.</p>
                                                    @endif
                                                </div>
                                            </section>
                                        @endif
                                    </div>
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

        {{-- ================================================================
             CRIAÇÃO DE PERSONAGEM — V66.2.242
             Tela ampla, etapas separadas e integração com character_features.
             As demais páginas do índice não são modificadas.
        ================================================================= --}}
        <div
            x-show="createOpen"
            x-cloak
            class="sb-create-overlay"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sb-create-title"
            @keydown.escape.stop="closeCreate()"
        >
            <div class="sb-create-backdrop"></div>
            <section class="sb-create-window" :class="{ 'sb-create-child-dialog-open': featureModal !== null || photoCropOpen }" x-show="createOpen" x-transition.opacity.duration.180ms>
                <header class="sb-create-head">
                    <div class="sb-create-heading">
                        <h2 id="sb-create-title">Novo Personagem</h2>
                    </div>
                    <div class="sb-create-head-actions" aria-label="Navegação da criação">
                        <button type="button" class="sb-create-back sb-create-head-back" x-show="createTab !== 'identity'" x-cloak
                                @click="goCreateTab(createTabs[Math.max(createTabs.indexOf(createTab) - 1, 0)])"
                                title="Voltar à etapa anterior" aria-label="Voltar à etapa anterior">
                            <span class="sb-create-action-label">Voltar</span><span class="sb-create-action-symbol" aria-hidden="true">←</span>
                        </button>
                        <button type="button" class="sb-create-next sb-create-head-next" x-show="createTab !== 'settings'" x-cloak
                                @click="nextCreateTab()" title="Avançar para a próxima etapa" aria-label="Avançar para a próxima etapa">
                            <span class="sb-create-action-label">Avançar</span><span class="sb-create-action-symbol" aria-hidden="true">→</span>
                        </button>
                        <button type="submit" form="sb-create-character-form" class="sb-create-submit sb-create-head-submit"
                                x-show="createTab === 'settings'" x-cloak title="Criar personagem" aria-label="Criar personagem">
                            <span class="sb-create-action-label">Criar personagem</span><span class="sb-create-action-symbol" aria-hidden="true">✓</span>
                        </button>
                        <button type="button" class="sb-create-close" @click="closeCreate()" title="Fechar criação" aria-label="Fechar criação">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 5 19 19M19 5 5 19" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                </header>

                <nav class="sb-create-tabs" aria-label="Etapas do personagem">
                    <button type="button" :class="{'is-active':createTab === 'identity'}" :aria-current="createTab === 'identity' ? 'step' : null" @click="goCreateTab('identity')">
                        <span class="sb-create-step">01</span><span>Identidade</span>
                    </button>
                    <button type="button" :class="{'is-active':createTab === 'classes'}" :aria-current="createTab === 'classes' ? 'step' : null" @click="goCreateTab('classes')">
                        <span class="sb-create-step">02</span><span>Classes</span>
                    </button>
                    <button type="button" :class="{'is-active':createTab === 'proficiencies'}" :aria-current="createTab === 'proficiencies' ? 'step' : null" @click="goCreateTab('proficiencies')">
                        <span class="sb-create-step">03</span><span>Proficiências</span>
                    </button>
                    <button type="button" :class="{'is-active':createTab === 'abilities'}" :aria-current="createTab === 'abilities' ? 'step' : null" @click="goCreateTab('abilities')">
                        <span class="sb-create-step">04</span><span>Atributos</span>
                    </button>
                    <button type="button" :class="{'is-active':createTab === 'vitality'}" :aria-current="createTab === 'vitality' ? 'step' : null" @click="goCreateTab('vitality')">
                        <span class="sb-create-step">05</span><span>Vida e dados</span>
                    </button>
                    <button type="button" :class="{'is-active':createTab === 'settings'}" :aria-current="createTab === 'settings' ? 'step' : null" @click="goCreateTab('settings')">
                        <span class="sb-create-step">06</span><span>Configurações</span>
                    </button>
                </nav>

                <form
                    id="sb-create-character-form"
                    method="POST"
                    action="{{ route('characters.store') }}"
                    enctype="multipart/form-data"
                    class="sb-create-form"
                    x-ref="createForm"
                    @submit="submitCreate($event)"
                    novalidate
                >
                    @csrf
                    <input type="hidden" name="multi_class_enabled" :value="classes.length > 1 ? 1 : 0">
                    <input type="hidden" name="level" :value="totalLevel">

                    <div class="sb-create-content">
                        {{-- 01 — IDENTIDADE --}}
                        <section class="sb-create-view" data-create-tab="identity" x-show="createTab === 'identity'" x-cloak>
                            <div class="sb-create-section-head sb-create-identity-heading">
                                <div><h3>Identidade</h3><p>Retrato, origens e informações iniciais.</p></div>
                            </div>
                            <div class="sb-create-identity-composition">
                                <div class="sb-create-identity-side sb-create-identity-left">
                                    <label class="sb-create-field">
                                        <span>Nome</span>
                                        <input type="text" name="name" x-model="forgeName" maxlength="120" required placeholder="Nome do personagem">
                                    </label>
                                    <div class="sb-create-field">
                                        <div class="sb-create-field-labelrow">
                                            <span>Espécie</span>
                                            <span class="sb-create-field-actions" x-show="!!forgeSpecies.trim()" x-cloak>
                                                <button type="button" class="sb-create-field-action" @click="openFeatureModal('species')">
                                                    Traços <em x-show="speciesTraits.filter(t => t.name.trim()).length > 0" x-text="speciesTraits.filter(t => t.name.trim()).length"></em>
                                                </button>
                                                <button type="button" class="sb-create-info-wrap" data-tooltip="Os traços de espécie são opcionais agora. Você poderá adicioná-los, detalhá-los ou editá-los depois em Traços &amp; Talentos, na ficha." title="Traços opcionais: você pode completar depois na ficha." aria-label="Ajuda sobre os traços de espécie: você poderá acrescentá-los e editá-los depois na ficha.">?</button>
                                            </span>
                                        </div>
                                        <input type="text" name="species" x-model="forgeSpecies" maxlength="80" required placeholder="Ex.: Elfa (Drow)">
                                    </div>
                                </div>
                                <aside class="sb-create-portrait-pane" aria-label="Retrato do personagem">
                                    <div class="sb-create-portrait-preview">
                                        <template x-if="imagePreview"><img :src="imagePreview" alt="Prévia da fotografia do personagem"></template>
                                        <div class="sb-create-empty-portrait" x-show="!imagePreview" x-cloak>
                                            <span class="sb-create-empty-portrait-title">Seu retrato</span>
                                            <span class="sb-create-empty-portrait-subtitle">Opcional</span>
                                        </div>
                                        <div class="sb-create-portrait-name" x-show="!!forgeName.trim()" x-cloak>
                                            <strong x-text="forgeName.trim()"></strong>
                                            <small x-show="!!forgeSpecies.trim()" x-text="forgeSpecies.trim()"></small>
                                        </div>
                                    </div>
                                    <button type="button" class="sb-create-photo-button" @click="openPhotoEditor()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 7h4l2-3h6l2 3h4v13H3zM12 17a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke-width="1.5" stroke-linejoin="round"/></svg>
                                        <span x-text="imagePreview ? 'Ajustar fotografia' : 'Escolher fotografia'"></span>
                                    </button>
                                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp" x-ref="forgeImageInput" class="sb-create-hidden-input" tabindex="-1">
                                    <input type="file" accept="image/png,image/jpeg,image/webp" x-ref="forgeCropInput" class="sb-create-hidden-input" tabindex="-1" @change="startPhotoCrop($event)">
                                    <p class="sb-create-photo-help">Arraste, ajuste o zoom e aplique o recorte</p>
                                </aside>
                                <div class="sb-create-identity-side sb-create-identity-right">
                                    <div class="sb-create-field">
                                        <div class="sb-create-field-labelrow">
                                            <span>Antecedente</span>
                                            <span class="sb-create-field-actions" x-show="!!forgeBackground.trim()" x-cloak>
                                                <button type="button" class="sb-create-field-action" @click="openFeatureModal('origin')">Talento</button>
                                                <button type="button" class="sb-create-field-action" @click="openFeatureModal('background_abilities')">Atributos <em x-show="originAbilityMode!=='none'">+3</em></button>
                                                <button type="button" class="sb-create-field-action" @click="openFeatureModal('proficiencies')">
                                                    Proficiências <em x-show="backgroundProficiencyCount > 0" x-text="backgroundProficiencyCount"></em>
                                                </button>
                                                <button type="button" class="sb-create-info-wrap" data-tooltip="Talento de origem e proficiências são opcionais neste momento. Poderão ser cadastrados ou alterados depois, nas áreas de Traços &amp; Talentos e Treinamentos &amp; Proficiências." title="Talentos e proficiências poderão ser completados depois na ficha." aria-label="Ajuda sobre antecedente: talentos e proficiências podem ser completados depois na ficha.">?</button>
                                            </span>
                                        </div>
                                        <input type="text" name="background" x-model="forgeBackground" maxlength="120" required placeholder="Ex.: Eremita">
                                    </div>
                                    <label class="sb-create-field">
                                        <span>Alinhamento</span>
                                        <input type="text" name="alignment" value="{{ old('alignment') }}" maxlength="50" required placeholder="Ex.: Caótica Neutra">
                                    </label>
                                </div>
                            </div>
                            <p class="sb-create-identity-footnote">Os detalhes opcionais de espécie e antecedente também podem ser preenchidos depois na ficha.</p>
                        </section>

                        {{-- 02 — CLASSES --}}
                        <section class="sb-create-view" data-create-tab="classes" x-show="createTab === 'classes'" x-cloak>
                            <div class="sb-create-section-head">
                                <div><small>SEGUNDA ETAPA</small><h3>Classes e nível</h3><p>Escolha a classe inicial e, se necessário, adicione multiclasses.</p></div>
                                <span class="sb-create-section-folio">II / VI</span>
                            </div>
                            <div class="sb-create-class-list">
                                <template x-for="(item,index) in classes" :key="index">
                                    <article class="sb-create-class-card">
                                        <header class="sb-create-class-top">
                                            <div class="sb-create-class-identity">
                                                <span class="sb-create-class-seal" x-text="index + 1"></span>
                                                <div><strong x-text="index===0 ? 'Classe inicial' : 'Multiclasse '+(index+1)"></strong>
                                                    <small x-text="item.class?.trim() || 'Defina uma classe'"></small></div>
                                            </div>
                                            <div class="sb-create-class-top-actions">
                                                <button type="button" x-show="classSuggestion(item) !== null" @click="applyClassSuggestion(item)" title="Aplicar apenas dado de vida e conjuração sugeridos">Usar sugestão</button>
                                                <button type="button" x-show="classes.length > 1" @click="removeClass(index)" class="sb-create-class-remove">Remover</button>
                                            </div>
                                        </header>
                                        <div class="sb-create-class-fields">
                                            <label class="sb-create-field"><span>Classe</span><input type="text" :name="`classes[${index}][class]`" x-model="item.class" required maxlength="80" placeholder="Ex.: Guerreiro"></label>
                                            <label class="sb-create-field"><span>Subclasse</span><input type="text" :name="`classes[${index}][subclass]`" x-model="item.subclass" maxlength="80" placeholder="Se houver"></label>
                                            <label class="sb-create-field sb-create-class-level"><span>Nível</span><input type="number" :name="`classes[${index}][level]`" x-model.number="item.level" required min="1" max="20"></label>
                                        </div>
                                        <div class="sb-create-class-options">
                                            <label class="sb-create-field"><span>Dado de Vida</span>
                                                <select :name="`classes[${index}][hit_die]`" x-model.number="item.hit_die">
                                                    <option value="6">d6</option><option value="8">d8</option><option value="10">d10</option><option value="12">d12</option>
                                                </select>
                                            </label>
                                            <label class="sb-create-caster-choice" :class="{'is-on':item.spellcaster}">
                                                <input type="checkbox" :name="`classes[${index}][spellcaster]`" value="1" x-model="item.spellcaster">
                                                <span>Classe conjuradora</span>
                                            </label>
                                        </div>
                                        <div class="sb-create-class-caster" x-show="item.spellcaster" x-cloak>
                                            <label class="sb-create-field"><span>Atributo de conjuração</span>
                                                <select :name="`classes[${index}][spell_ability]`" x-model="item.spell_ability" :disabled="!item.spellcaster">
                                                    <option value="intelligence">Inteligência</option><option value="wisdom">Sabedoria</option><option value="charisma">Carisma</option>
                                                    <option value="strength">Força</option><option value="dexterity">Destreza</option><option value="constitution">Constituição</option>
                                                </select>
                                            </label>
                                            <label class="sb-create-field"><span>Progressão de espaços</span>
                                                <select :name="`classes[${index}][caster_type]`" x-model="item.caster_type" :disabled="!item.spellcaster">
                                                    <option value="full">Completa</option><option value="half">Metade</option><option value="half_up">Metade (arredonda para cima)</option>
                                                    <option value="third">Um terço</option><option value="pact">Pacto</option><option value="custom">Personalizada</option>
                                                </select>
                                            </label>
                                            <p>As configurações detalhadas de conjuração poderão ser ajustadas no Grimório da ficha.</p>
                                        </div>
                                    </article>
                                </template>
                            </div>
                            <button type="button" class="sb-create-add-class" @click="addClass()" x-show="classes.length < 6">+ Adicionar multiclasse</button>
                            <div class="sb-create-class-summary" aria-live="polite">
                                <span>Nível total</span>
                                <strong x-text="totalLevel"></strong>
                                <span class="sb-create-class-summary-limit">/ 20</span>
                                <small>Soma das classes cadastradas</small>
                            </div>
                            <div class="sb-create-class-proficiency">
                                <div class="sb-create-class-proficiency-copy">
                                    <span>BÔNUS DE PROFICIÊNCIA</span>
                                    <strong x-text="'+' + effectiveProficiency"></strong>
                                    <small>Calculado pelo nível total. Personalize somente se necessário.</small>
                                </div>
                                <label class="sb-create-prof-custom-switch"><input type="checkbox" name="custom_prof_enabled" value="1" x-model="customProfEnabled"><span>Personalizar</span></label>
                                <label class="sb-create-field sb-create-prof-custom-field" x-show="customProfEnabled" x-cloak>
                                    <span>Bônus</span><input type="number" name="proficiency_bonus" min="0" max="20" step="1" x-model.number="customProfValue" :disabled="!customProfEnabled" :required="customProfEnabled" aria-label="Bônus de proficiência personalizado">
                                </label>
                            </div>
                        </section>

                        {{-- 03 — PROFICIÊNCIAS: classe + antecedente, sem duplicar registros. --}}
                        <section class="sb-create-view" data-create-tab="proficiencies" x-show="createTab === 'proficiencies'" x-cloak>
                            <div class="sb-create-section-head sb-create-compact-heading">
                                <div><h3>Treinamentos & Proficiências</h3><p>Registre o que sua classe e seu antecedente concedem. Você poderá editar tudo na ficha.</p></div>
                                <span class="sb-create-section-folio">III / VI</span>
                            </div>
                            <div class="sb-create-proficiency-stage sb-create-proficiency-stage--unified">
                                <div class="sb-create-proficiency-main">
                                    <p class="sb-create-proficiency-hint">Configure perícias e treinamentos em um único lugar. É possível adicionar nomes personalizados e definir o atributo das ferramentas e veículos.</p>
                                    <div class="sb-create-proficiency-editor sb-create-proficiency-editor--unified">
                                        <details class="sb-create-proficiency-group" open>
                                            <summary>Perícias e expertise <small>Escolha o nível de treinamento</small></summary>
                                            <div class="sb-create-skill-grid">
                                                @foreach ($forgeBackgroundSkillOptions as $skillKey => $skillLabel)
                                                    <label class="sb-create-skill-line">
                                                        <span>{{ $skillLabel }}</span>
                                                        <select :value="skillTrainingLevel('{{ $skillKey }}')" @change="setSkillTrainingLevel('{{ $skillKey }}',$event.target.value)" aria-label="Treinamento em {{ $skillLabel }}">
                                                            <option value="none">Nenhum</option>
                                                            <option value="proficient">Proficiência</option>
                                                            <option value="expertise">Expertise</option>
                                                        </select>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </details>
                                        @foreach ($forgeBackgroundTrainingOptions as $trainingType => $definition)
                                            <details class="sb-create-proficiency-group">
                                                <summary>{{ $definition['title'] }} <small>Selecione ou escreva um nome</small></summary>
                                                <div class="sb-create-proficiency-options">
                                                    @foreach ($definition['options'] as $option)
                                                        <label class="sb-create-proficiency-check">
                                                            <input type="checkbox" :checked="trainingChecked('{{ $trainingType }}', @js($option))" @change="toggleTraining('{{ $trainingType }}', @js($option), $event.target.checked)">
                                                            <span>{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <div class="sb-create-training-inline">
                                                    <div class="sb-create-training-inline-title"><strong>Opções personalizadas</strong><button type="button" @click="addClassTraining('{{ $trainingType }}')" :disabled="classCustomTrainings.length >= 30">+ Adicionar</button></div>
                                                    <template x-for="(entry,index) in classCustomTrainings" :key="index">
                                                        <template x-if="entry.type === '{{ $trainingType }}'">
                                                            <div class="sb-create-training-inline-row">
                                                                <input type="hidden" :name="`class_custom_trainings[${index}][type]`" :value="entry.type">
                                                                <input type="text" :name="`class_custom_trainings[${index}][name]`" x-model="entry.name" maxlength="120" placeholder="Escreva o nome" aria-label="Nome do treinamento personalizado">
                                                                @if (in_array($trainingType, ['tool_proficiency','vehicle_proficiency'], true))
                                                                    <select :name="`class_custom_trainings[${index}][roll_ability]`" x-model="entry.roll_ability" aria-label="Atributo de rolagem">
                                                                        <option value="">Atributo automático</option>
                                                                        @foreach ($forgeAbilityNames as $abilityKey => $abilityLabel)
                                                                            <option value="{{ $abilityKey }}">{{ $abilityLabel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                @endif
                                                                <button type="button" @click="classCustomTrainings.splice(index,1)" title="Remover treinamento" aria-label="Remover treinamento personalizado">×</button>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>
                                            </details>
                                        @endforeach
                                    </div>
                                    <template x-for="key in classSkills" :key="key"><input type="hidden" name="class_skills[]" :value="key"></template>
                                    <template x-for="key in expertiseSkills" :key="key"><input type="hidden" name="skill_expertise[]" :value="key"></template>
                                    @foreach ($forgeBackgroundTrainingOptions as $trainingType => $definition)
                                        <template x-for="name in classTrainings.{{ $trainingType }}" :key="name"><input type="hidden" name="class_trainings[{{ $trainingType }}][]" :value="name"></template>
                                    @endforeach
                                </div>
                                <aside class="sb-create-proficiency-overview sb-create-proficiency-overview--unified">
                                    <h4>Sua formação</h4>
                                    <p><strong x-text="uniqueSkillCount"></strong> perícias treinadas</p>
                                    <p><strong x-text="expertiseCount"></strong> com expertise</p>
                                    <p>As escolhas do antecedente feitas em Identidade aparecem aqui automaticamente.</p>
                                    <button type="button" @click="openFeatureModal('proficiencies')" :disabled="!forgeBackground.trim()">Editar proficiências do antecedente</button>
                                    <p>Personagens de nível alto podem receber expertise conforme suas características. Você pode completar os detalhes na ficha.</p>
                                </aside>
                            </div>
                        </section>

                        {{-- 04 — ATRIBUTOS: conjunto padrão, rolagem ou valores manuais. --}}
                        <section class="sb-create-view" data-create-tab="abilities" x-show="createTab === 'abilities'" x-cloak>
                            <div class="sb-create-section-head sb-create-compact-heading">
                                <div><h3>Atributos</h3><p>Escolha os seis valores ou role 4d6, descartando o menor dado.</p></div>
                                <span class="sb-create-section-folio">IV / VI</span>
                            </div>
                            <div class="sb-create-abilities-layout" :class="{'is-single':abilityMethod !== 'rolled'}">
                                <div class="sb-create-abilities-primary">
                                    <div class="sb-create-abilities-heading">
                                        <div>
                                            <strong>Distribuição de atributos</strong>
                                            <div class="sb-create-ability-info" x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" @click.outside="open=false">
                                                <button type="button" class="sb-create-ability-info-trigger" aria-label="Como funcionam as rolagens de atributos" :aria-expanded="open ? 'true' : 'false'" @click.stop="open=true" @focus="open=true" @blur="open=false">?</button>
                                                <div class="sb-create-ability-info-panel" x-show="open" x-cloak role="tooltip">
                                                    <strong>Rolando atributos — 4d6</strong>
                                                    <p>Role <b>quatro dados de seis faces</b>, descarte o <b>menor resultado</b> e some os três restantes. A soma é o valor do atributo.</p>
                                                    <p><em>Alguns mestres permitem rerolar um dado ou o conjunto inteiro. Consulte seu mestre a respeito.</em></p>
                                                </div>
                                            </div>
                                            <span>Defina os valores iniciais; o bônus do antecedente aparece separado.</span>
                                        </div>
                                        <div class="sb-create-ability-modes" role="group" aria-label="Método de geração dos atributos">
                                            <button type="button" :class="{'is-active':abilityMethod === 'standard'}" @click="useStandardAbilities()" :disabled="abilityRollBusy">Padrão</button>
                                            <button type="button" :class="{'is-active':abilityMethod === 'rolled'}" @click="openAbilityDiceTray()" :disabled="abilityRollBusy && abilityMethod !== 'rolled'">Rolar 4d6</button>
                                            <button type="button" :class="{'is-active':abilityMethod === 'manual'}" @click="setAbilityMethod('manual')" :disabled="abilityRollBusy">Manual</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="ability_generation_method" :value="abilityMethod">
                                    @foreach ($forgeAbilityNames as $abilityKey => $abilityLabel)
                                        <input type="hidden" name="abilities[{{ $abilityKey }}]" :value="effectiveAbilityScore('{{ $abilityKey }}') ?? ''">
                                    @endforeach
                                    <div class="sb-create-standard-pool sb-create-ability-pool" x-show="abilityMethod === 'standard'" x-cloak>
                                        <small>VALORES DISPONÍVEIS</small>
                                        <template x-for="value in standardRemaining" :key="value"><strong x-text="value"></strong></template>
                                        <em x-show="standardRemaining.length === 0">Todos utilizados. Libere algum para trocar.</em>
                                    </div>
                                    <div class="sb-create-abilities-status-row" x-show="abilityMethod === 'rolled' && (abilityRollBusy || abilityRollError)" x-cloak>
                                        <div class="sb-create-ability-progress-inline" x-show="abilityRollBusy" x-cloak>
                                            <span>Rolando <b x-text="abilityName(forgeTarget)"></b> · <b x-text="forgePendingFaces.length + '/4'"></b></span>
                                            <button type="button" @click="resetForgeFour()" :disabled="forgeDiceInFlight">Recomeçar</button>
                                            <button type="button" @click="stopForgeDice()" aria-label="Cancelar rolagem">Cancelar</button>
                                        </div>
                                        <p class="sb-create-life-error" role="alert" x-show="abilityRollError" x-text="abilityRollError" x-cloak></p>
                                    </div>
                                    <div class="sb-create-attribute-breakdown" x-show="forgeBackground.trim()" x-cloak>
                                        <span>Antecedente: <b x-text="forgeBackground"></b></span>
                                        <button type="button" @click="openFeatureModal('background_abilities')">Configurar bônus</button>
                                        <span x-show="originAbilityMode !== 'none'" x-text="originAbilityMode==='three' ? '+1 / +1 / +1' : '+2 / +1'" x-cloak></span>
                                    </div>
                                    <p class="sb-create-ability-validation" x-show="abilityBonusError" x-text="abilityBonusError" role="alert" x-cloak></p>
                                    <div class="sb-create-ability-table-wrap">
                                        <table class="sb-create-ability-table">
                                            <thead><tr><th scope="col">Atributo</th><th scope="col">Base</th><th scope="col">Antecedente</th><th scope="col">Total</th><th scope="col">Mod.</th><th scope="col">Ação</th></tr></thead>
                                            <tbody>
                                            @foreach ($forgeAbilityNames as $abilityKey => $abilityLabel)
                                                <tr :class="{'is-selected':abilityMethod === 'rolled' && abilitySelected === '{{ $abilityKey }}'}">
                                                    <th scope="row"><label for="sb-create-score-{{ $abilityKey }}">{{ $abilityLabel }}</label></th>
                                                    <td>
                                                        <template x-if="abilityMethod === 'standard'">
                                                            <select id="sb-create-score-{{ $abilityKey }}" name="base_abilities[{{ $abilityKey }}]" required
                                                                    x-model.number="abilityStandardSelections.{{ $abilityKey }}"
                                                                    @change="assignStandardAbility('{{ $abilityKey }}', $event.target.value)" aria-label="Valor padrão de {{ $abilityLabel }}">
                                                                <option value="">Escolher</option>
                                                                <template x-for="value in availableStandardScores('{{ $abilityKey }}')" :key="value"><option :value="value" x-text="value"></option></template>
                                                            </select>
                                                        </template>
                                                        <template x-if="abilityMethod !== 'standard'">
                                                            <input id="sb-create-score-{{ $abilityKey }}" type="number" name="base_abilities[{{ $abilityKey }}]" min="1" max="20" step="1" required
                                                                   inputmode="numeric" x-model.number="abilityScores.{{ $abilityKey }}" :readonly="abilityMethod === 'rolled'" aria-label="Valor de {{ $abilityLabel }}">
                                                        </template>
                                                    </td>
                                                    <td><span class="sb-create-ability-origin-value" x-text="originBonus('{{ $abilityKey }}') ? '+'+originBonus('{{ $abilityKey }}') : '—'"></span></td>
                                                    <td><strong class="sb-create-ability-total" :class="{'is-over-limit':abilityOverLimit('{{ $abilityKey }}')}" x-text="effectiveAbilityScore('{{ $abilityKey }}') ?? '—'"></strong></td>
                                                    <td><span class="sb-create-ability-table-mod" x-text="abilityModifierLabel('{{ $abilityKey }}')">+0</span></td>
                                                    <td>
                                                        <div class="sb-create-ability-table-actions">
                                                        <button type="button" class="sb-create-ability-table-action" x-show="abilityMethod === 'rolled' && abilityRollHistory.some(item=>item.key==='{{ $abilityKey }}')" x-cloak
                                                                @click="showAbilityRoll('{{ $abilityKey }}')"
                                                                :disabled="abilityRollBusy" title="Rever as quatro faces e suas posições">Ver dados</button>
                                                        <button type="button" class="sb-create-ability-table-action" x-show="abilityMethod === 'standard' && abilityStandardSelections.{{ $abilityKey }} !== null" x-cloak
                                                                @click="assignStandardAbility('{{ $abilityKey }}','')" title="Devolver ao conjunto padrão">Liberar</button>
                                                        <button type="button" class="sb-create-ability-table-action" x-show="abilityMethod === 'rolled'" x-cloak
                                                                @click="rollAbilityScore('{{ $abilityKey }}')"
                                                                :disabled="abilityRollBusy && abilitySelected !== '{{ $abilityKey }}'"
                                                                x-text="abilityRollBusy && abilitySelected === '{{ $abilityKey }}' ? 'Na mesa' : (abilityRollHistory.some(item => item.key === '{{ $abilityKey }}') ? 'Rerolar' : 'Rolar')"
                                                                :title="'Abrir a bandeja de 4d6 para {{ $abilityLabel }}'"></button>
                                                        <span class="sb-create-ability-table-readonly" x-show="abilityMethod === 'manual'" x-cloak>Editar valor</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="sb-create-abilities-help">No conjunto padrão, cada número só pode ser usado uma vez. Constituição modifica os PV máximos na próxima etapa.</p>
                                </div>
                                <div id="sb-create-abilities-dice-anchor" class="sb-create-abilities-dice-anchor"
                                     x-show="abilityMethod === 'rolled'" x-cloak aria-hidden="true"></div>
                            </div>
                        </section>

                        {{-- 05 — VIDA E DADOS: usa Constituição da etapa Atributos. --}}
                        <section class="sb-create-view" data-create-tab="vitality" x-show="createTab === 'vitality'" x-cloak>
                            <div class="sb-create-section-head sb-create-compact-heading">
                                <div><h3>Vida e dados</h3><p>Defina seus dados de vida por classe e calcule os PV máximos.</p></div>
                                <span class="sb-create-section-folio">V / VI</span>
                            </div>
                            <div class="sb-create-life-modern" :class="{'is-rolling':hpMethod==='rolled'&&lifeDiceList.length>0}">
                                <div class="sb-create-life-main">
                                    <div class="sb-create-life-summary">
                                        <div class="sb-create-life-total">
                                            <span>SEUS PONTOS DE VIDA MÁXIMOS</span>
                                            <strong x-show="hpMethod !== 'manual'" x-text="lifeMaxHp"></strong>
                                            <input x-show="hpMethod === 'manual'" x-cloak type="number" name="hit_points_manual"
                                                   x-model.number="hpManual" :disabled="hpMethod !== 'manual'"
                                                   :required="hpMethod==='manual'" min="1" max="9999"
                                                   aria-label="Alterar pontos de vida máximos manualmente">
                                            <small x-text="hpMethod==='rolled'&&!lifeRollComplete ? 'Prévia: finalize as rolagens para confirmar.' : 'O personagem começa com a vida completa.'">O personagem começa com a vida completa.</small>
                                        </div>
                                        <div class="sb-create-life-con-info">
                                            <span>Constituição</span>
                                            <strong x-text="(effectiveAbilityScore('constitution') ?? '—') + ' (' + abilityModifierLabel('constitution') + ')'">10 (+0)</strong>
                                            <button type="button" @click="goCreateTab('abilities')">Editar atributo</button>
                                        </div>
                                    </div>
                                    <div class="sb-create-life-modes" role="group" aria-label="Método de definição dos PV máximos">
                                        <button type="button" :class="{'is-selected':hpMethod==='standard'}" @click="setLifeMethod('standard')">Valor padrão</button>
                                        <button type="button" :class="{'is-selected':hpMethod==='rolled'}" @click="setLifeMethod('rolled')">Rolar dados</button>
                                        <button type="button" :class="{'is-selected':hpMethod==='manual'}" @click="setLifeMethod('manual')">Manual</button>
                                        <input type="hidden" name="hit_points_method" :value="hpMethod">
                                        <input type="hidden" name="hit_points_rolls" :value="JSON.stringify(lifeRollResults)">
                                    </div>
                                    <div class="sb-create-life-list-title">
                                        <strong>Dados por nível</strong>
                                        <div class="sb-create-ability-info" x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" @click.outside="open=false">
                                            <button type="button" class="sb-create-ability-info-trigger" aria-label="Como calcular os Pontos de Vida" @click.stop="open=!open" @focus="open=true" :aria-expanded="open ? 'true':'false'">?</button>
                                            <div class="sb-create-ability-info-panel" x-show="open" x-cloak role="tooltip">
                                                <strong>Dados de Vida</strong>
                                                <p>O primeiro nível começa com o <b>valor máximo</b> do dado da classe inicial. Para cada nível seguinte, use o valor padrão ou role o dado daquela classe.</p>
                                                <p>O modificador de Constituição é somado uma vez por nível. Ao criar, seus PV atuais começam iguais aos máximos. O modo Manual permite mudar o valor acima diretamente.</p>
                                            </div>
                                        </div>
                                        <span x-text="totalLevel + (totalLevel===1?' nível':' níveis')"></span>
                                    </div>
                                    <div class="sb-create-life-edit-warning" x-show="lifeEditConfirmKey!==null" x-cloak
                                         role="alertdialog" aria-label="Confirmar alteração de dado de vida"
                                         @keydown.escape.stop="dismissLifeDieWarning()">
                                        <div class="sb-create-life-edit-warning-copy">
                                            <strong>Alterar resultado deste dado?</strong>
                                            <p>O valor <b x-text="lifeRollResults[lifeEditConfirmKey] ?? '—'"></b> será substituído ao salvar a edição. O dado continua registrado e pode ser movido; cancelar não altera nenhum resultado.</p>
                                        </div>
                                        <div class="sb-create-life-edit-warning-actions">
                                            <button type="button" @click="dismissLifeDieWarning()">Cancelar</button>
                                            <button type="button" class="is-confirm" @click="confirmLifeDieEdit()">Continuar e editar</button>
                                        </div>
                                    </div>
                                    <div class="sb-create-life-table-wrap">
                                        <table class="sb-create-life-table">
                                            <thead><tr><th>CLASSE</th><th>NÍVEL</th><th>DADO</th><th>RESULTADO</th></tr></thead>
                                            <tbody>
                                                <tr><td x-text="normalizedClasses[0]?.class || 'Classe inicial'"></td>
                                                    <td>1º</td><td x-text="'d' + (normalizedClasses[0]?.hit_die || 8)"></td>
                                                    <td><span class="sb-create-life-fixed" x-text="normalizedClasses[0]?.hit_die || 8"></span> <small>máximo</small></td></tr>
                                                <template x-for="(entry,index) in lifeDiceList" :key="entry.key">
                                                    <tr><td x-text="entry.className"></td><td x-text="(entry.classLevel) + 'º'"></td>
                                                        <td x-text="'d' + entry.sides"></td>
                                                        <td class="sb-create-life-result-cell">
                                                            <template x-if="hpMethod==='rolled' && lifeEditKey===entry.key">
                                                                <div class="sb-create-life-inline-edit">
                                                                    <input type="number" inputmode="numeric" min="1" :max="entry.sides" step="1"
                                                                           x-model.number="lifeEditValue" :aria-label="'Resultado do dado d'+entry.sides+' de '+entry.className+' nível '+entry.classLevel"
                                                                           @keydown.enter.prevent="saveLifeDieResult(entry)" @keydown.escape.prevent="cancelLifeDieEdit()">
                                                                    <button type="button" @click="saveLifeDieResult(entry)" title="Salvar resultado deste nível" :disabled="forgeDiceInFlight">✓</button>
                                                                    <button type="button" @click="cancelLifeDieEdit()" title="Cancelar edição">×</button>
                                                                </div>
                                                            </template>
                                                            <template x-if="!(hpMethod==='rolled' && lifeEditKey===entry.key)">
                                                                <button type="button" class="sb-create-life-result-button"
                                                                        :class="{'is-pending':hpMethod==='rolled'&&!lifeDieIsRolled(entry)}"
                                                                        :disabled="hpMethod!=='rolled'||!lifeDieIsRolled(entry)||forgeDiceInFlight"
                                                                        :title="hpMethod==='rolled'&&lifeDieIsRolled(entry) ? 'Editar este resultado (solicitar confirmação antes de substituir)' : 'Selecione Rolar dados para usar dados individuais'"
                                                                        @click="editLifeDieResult(entry)">
                                                                    <strong x-text="hpMethod==='rolled' ? (lifeDieIsRolled(entry) ? lifeDieFace(entry) : '—') : (Math.floor(entry.sides/2)+1)"></strong>
                                                                    <small x-show="hpMethod==='standard'">padrão</small>
                                                                    <small x-show="hpMethod==='rolled'&&lifeDieIsRolled(entry)">editar · <span x-text="entry.classLevel+'º'"></span></small>
                                                                </button>
                                                            </template>
                                                            <small class="sb-create-life-inline-error" x-show="lifeEditKey===entry.key && lifeEditError" x-text="lifeEditError" x-cloak role="alert"></small>
                                                        </td></tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="sb-create-life-help" x-show="hpMethod==='rolled'" x-cloak>
                                        Os dados lançados ficam registrados. Para rever ou reposicionar, abra a bandeja; use ↻ sobre um dado para rerolá-lo.
                                    </p>
                                </div>
                                <aside class="sb-create-life-aside" x-show="hpMethod==='rolled'&&lifeDiceList.length>0" x-cloak aria-label="Acompanhamento dos Dados de Vida">
                                    <div class="sb-create-life-aside-top">
                                        <strong>Rolagem de vida</strong>
                                        <span x-text="lifeRollCount+' de '+lifeDiceList.length"></span>
                                    </div>
                                    <p class="sb-create-life-aside-note" x-text="lifeRollComplete ? 'Os resultados estão salvos. Clique em um valor para solicitar a edição.' : 'Retire e lance cada dado disponível, conforme a classe e o nível.'"></p>
                                    <div class="sb-create-life-aside-buttons">
                                        <button type="button" class="sb-create-life-roll-button" @click="showForgeLifeDice()"
                                                :disabled="forgeDiceInFlight">Ver dados</button>
                                        <button type="button" class="sb-create-life-roll-subtle" @click="restartForgeLifeRoll()"
                                                :disabled="lifeRolling || abilityRollBusy || forgeDiceInFlight">Rerolar todos</button>
                                    </div>
                                    <p class="sb-create-life-error" x-show="lifeRollError" x-text="lifeRollError" role="alert" x-cloak></p>
                                    <div id="sb-create-life-dice-anchor" class="sb-create-life-dice-dock-area" aria-hidden="true"></div>
                                </aside>
                            </div>
                        </section>

                        {{-- 06 — CONFIGURAÇÕES --}}
                        <section class="sb-create-view" data-create-tab="settings" x-show="createTab === 'settings'" x-cloak>
                            <div class="sb-create-section-head">
                                <div><small>ETAPA FINAL</small><h3>Configurações</h3><p>Preferências opcionais. A maioria das informações poderá ser ajustada diretamente na ficha.</p></div>
                                <span class="sb-create-section-folio">VI / VI</span>
                            </div>
                            <div class="sb-create-settings-grid">
                                <section class="sb-create-setting">
                                    <label class="sb-create-setting-main"><input type="checkbox" name="xp_enabled" value="1" x-model="xpEnabled"><span><strong>Usar experiência</strong><small>Ative quando a campanha utilizar pontos de experiência.</small></span></label>
                                    <div x-show="xpEnabled" x-cloak class="sb-create-setting-extra"><label class="sb-create-field"><span>Experiência inicial</span><input type="number" min="0" name="experience_points" value="{{ old('experience_points', 0) }}" :disabled="!xpEnabled" placeholder="0"></label></div>
                                </section>

                            </div>
                            <div class="sb-create-finish-note"><strong>Pronto para começar?</strong><p>A ficha receberá os seis atributos definidos, classes, dados de vida e as habilidades opcionais escolhidas. Os PV atuais começarão iguais aos PV máximos.</p></div>
                        </section>

                        @if ($errors->any())
                            <div class="sb-create-errors" role="alert">
                                <strong>Verifique as informações antes de criar:</strong>
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                    </div>
                    <!-- Edição opcional: um modal por tipo; todos os dados permanecem no formulário. -->
                    <div class="sb-create-feature-overlay" x-show="featureModal !== null" x-cloak
                         @keydown.escape.stop.prevent="closeFeatureModal()">
                        <div class="sb-create-feature-backdrop" @click="closeFeatureModal()"></div>
                        <section class="sb-create-feature-dialog" role="dialog" aria-modal="true" aria-label="Informações opcionais do personagem" @click.stop>
                            <header class="sb-create-feature-header">
                                <div><small x-text="featureModal === 'species' ? forgeSpecies : forgeBackground"></small><h3 x-text="featureModalTitle"></h3></div>
                                <button type="button" @click="closeFeatureModal()" aria-label="Fechar janela">×</button>
                            </header>
                            <div class="sb-create-feature-body">
                                <div x-show="featureModal === 'species'" data-create-feature="species" x-cloak>
                                    <template x-for="(trait,index) in speciesTraits" :key="index">
                                        <div class="sb-create-trait-editor">
                                            <div class="sb-create-trait-title"><strong x-text="'Traço ' + (index + 1)"></strong><button type="button" @click="removeSpeciesTrait(index)">Remover</button></div>
                                            <label class="sb-create-field"><span>Nome</span><input type="text" :name="`species_traits[${index}][name]`" x-model="trait.name" :disabled="!forgeSpecies.trim() || (featureModal !== 'species' && !trait.name.trim())" :required="featureModal === 'species'" maxlength="120" placeholder="Ex.: Visão no Escuro"></label>
                                            <label class="sb-create-field"><span>Descrição (opcional)</span><textarea :name="`species_traits[${index}][description]`" x-model="trait.description" :disabled="!forgeSpecies.trim() || (featureModal !== 'species' && !trait.name.trim())" maxlength="30000" rows="3" placeholder="O que essa habilidade concede?"></textarea></label>
                                        </div>
                                    </template>
                                    <button type="button" class="sb-create-add-trait" x-show="speciesTraits.length < 12" @click="addSpeciesTrait()">+ Adicionar outro traço</button>
                                </div>
                                <div x-show="featureModal === 'origin'" data-create-feature="origin" x-cloak>
                                    <label class="sb-create-field"><span>Nome do talento</span><input type="text" name="origin_feat[name]" x-model="originFeat.name" :disabled="!forgeBackground.trim() || (featureModal !== 'origin' && !originFeat.name.trim())" maxlength="120" placeholder="Ex.: Sortudo"></label>
                                    <label class="sb-create-field"><span>Descrição (opcional)</span><textarea name="origin_feat[description]" x-model="originFeat.description" :disabled="!forgeBackground.trim() || (featureModal !== 'origin' && !originFeat.name.trim())" maxlength="30000" rows="3" placeholder="Benefício concedido pelo antecedente"></textarea></label>
                                </div>
                                <div x-show="featureModal === 'background_abilities'" data-create-feature="background_abilities" x-cloak>
                                    <p class="sb-create-modal-summary">Escolha os três atributos contemplados por este antecedente personalizado e como distribuir os três pontos. Limite final: 20.</p>
                                    <div class="sb-create-origin-methods">
                                        <label><input type="radio" name="background_ability_method" value="none" x-model="originAbilityMode"> Sem bônus</label>
                                        <label><input type="radio" name="background_ability_method" value="two" x-model="originAbilityMode"> +2 e +1</label>
                                        <label><input type="radio" name="background_ability_method" value="three" x-model="originAbilityMode"> +1 em três</label>
                                    </div>
                                    <div x-show="originAbilityMode !== 'none'" x-cloak>
                                        <p class="sb-create-modal-summary">Três atributos elegíveis do antecedente:</p>
                                        <div class="sb-create-origin-three">
                                            <template x-for="(chosen,i) in originAbilityOptions" :key="i">
                                                <select :name="`background_ability_options[${i}]`" x-model="originAbilityOptions[i]" aria-label="Atributo elegível do antecedente">
                                                    <option value="">Selecione</option>
                                                    @foreach ($forgeAbilityNames as $abilityKey => $abilityLabel)
                                                        <option value="{{ $abilityKey }}" :disabled="originAbilityOptions.some((v,j)=>j!==i && v==='{{ $abilityKey }}')">{{ $abilityLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </template>
                                        </div>
                                        <div class="sb-create-origin-three" x-show="originAbilityMode === 'two'" x-cloak>
                                            <label>Recebe +2
                                                <select name="background_ability_primary" x-model="originAbilityPrimary">
                                                    <option value="">Escolha</option>
                                                    <template x-for="key in originEligible" :key="key"><option :value="key" x-text="abilityName(key)"></option></template>
                                                </select>
                                            </label>
                                            <label>Recebe +1
                                                <select name="background_ability_secondary" x-model="originAbilitySecondary">
                                                    <option value="">Escolha</option>
                                                    <template x-for="key in originEligible" :key="key"><option :value="key" x-text="abilityName(key)" :disabled="key===originAbilityPrimary"></option></template>
                                                </select>
                                            </label>
                                        </div>
                                        <p class="sb-create-modal-summary" x-show="originAbilityMode==='three'">Cada um dos três atributos receberá +1.</p>
                                    </div>
                                    <p class="sb-create-ability-validation" x-show="abilityBonusError" x-text="abilityBonusError" x-cloak></p>
                                </div>
                                <div x-show="featureModal === 'proficiencies'" data-create-feature="proficiencies" x-cloak>
                                    <div class="sb-create-proficiency-editor">
                                            <details class="sb-create-proficiency-group" open>
                                                <summary>Perícias <small>Selecione as concedidas pelo antecedente</small></summary>
                                                <div class="sb-create-proficiency-options">
                                                    @foreach ($forgeBackgroundSkillOptions as $skillKey => $skillLabel)
                                                        <label class="sb-create-proficiency-check">
                                                            <input type="checkbox" name="background_skills[]" value="{{ $skillKey }}" x-model="backgroundSkills" @change="syncExpertiseSkills()" :disabled="!forgeBackground.trim()">
                                                            <span>{{ $skillLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </details>
                                            @foreach ($forgeBackgroundTrainingOptions as $trainingType => $definition)
                                                <details class="sb-create-proficiency-group">
                                                    <summary>{{ $definition['title'] }}</summary>
                                                    <div class="sb-create-proficiency-options">
                                                        @foreach ($definition['options'] as $option)
                                                            <label class="sb-create-proficiency-check">
                                                                <input type="checkbox" name="background_trainings[{{ $trainingType }}][]" value="{{ $option }}" x-model="backgroundTrainings.{{ $trainingType }}" :disabled="!forgeBackground.trim()">
                                                                <span>{{ $option }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    <div class="sb-create-training-inline">
                                                        <div class="sb-create-training-inline-title"><strong>Opções personalizadas</strong>
                                                            <button type="button" @click="addBackgroundTraining('{{ $trainingType }}')" :disabled="!forgeBackground.trim() || backgroundCustomTrainings.length >= 30">+ Adicionar</button>
                                                        </div>
                                                        <template x-for="(entry,index) in backgroundCustomTrainings" :key="index">
                                                            <template x-if="entry.type === '{{ $trainingType }}'">
                                                                <div class="sb-create-training-inline-row">
                                                                    <input type="hidden" :name="`background_custom_trainings[${index}][type]`" :value="entry.type" :disabled="!forgeBackground.trim()">
                                                                    <input type="text" :name="`background_custom_trainings[${index}][name]`" x-model="entry.name" maxlength="120" :disabled="!forgeBackground.trim()" placeholder="Escreva o nome" aria-label="Nome do treinamento do antecedente">
                                                                    @if (in_array($trainingType, ['tool_proficiency','vehicle_proficiency'], true))
                                                                        <select :name="`background_custom_trainings[${index}][roll_ability]`" x-model="entry.roll_ability" :disabled="!forgeBackground.trim()" aria-label="Atributo de rolagem">
                                                                            <option value="">Atributo automático</option>
                                                                            @foreach ($forgeAbilityNames as $abilityKey => $abilityLabel)
                                                                                <option value="{{ $abilityKey }}">{{ $abilityLabel }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    @endif
                                                                    <button type="button" @click="backgroundCustomTrainings.splice(index,1)" title="Remover treinamento" aria-label="Remover treinamento do antecedente">×</button>
                                                                </div>
                                                            </template>
                                                        </template>
                                                    </div>
                                                </details>
                                            @endforeach
                                    </div>
                                </div>
                            </div>
                            <footer class="sb-create-feature-footer">
                                <span><button type="button" class="sb-create-info-wrap" data-tooltip="Você poderá voltar e corrigir, acrescentar ou remover estas informações depois da criação, diretamente na ficha." title="Você poderá editar tudo depois, na ficha." aria-label="Ajuda: estas informações podem ser editadas posteriormente na ficha.">?</button> Editável depois na ficha</span>
                                <button type="button" @click="closeFeatureModal()">Concluir</button>
                            </footer>
                        </section>
                    </div>
                    <!-- Editor de recorte com o mesmo comportamento do Cofre de Itens. -->
                    <div class="sb-create-feature-overlay" x-show="photoCropOpen" x-cloak @keydown.escape.stop.prevent="closePhotoCrop()">
                        <div class="sb-create-feature-backdrop" @click="closePhotoCrop()"></div>
                        <section class="sb-create-feature-dialog sb-create-crop-dialog" role="dialog" aria-modal="true" aria-label="Enquadrar fotografia" @click.stop>
                            <header class="sb-create-feature-header">
                                <div><small>Fotografia do personagem</small><h3>Enquadrar foto</h3></div>
                                <button type="button" @click="closePhotoCrop()" aria-label="Fechar ajuste de imagem">×</button>
                            </header>
                            <div class="sb-create-crop-layout">
                                <div class="sb-create-crop-stage" x-ref="forgePhotoCropStage"
                                     @pointerdown.prevent="startPhotoCropDrag($event)" @pointermove="movePhotoCropDrag($event)"
                                     @pointerup="stopPhotoCropDrag($event)" @pointercancel="stopPhotoCropDrag($event)">
                                    <img x-ref="forgePhotoCropImage" :src="photoCropSource" :style="photoCropImageStyle()"
                                         @load="photoCropImageLoaded($event)" alt="Prévia para enquadramento" draggable="false">
                                    <span class="sb-create-crop-guides" aria-hidden="true"></span>
                                </div>
                                <div class="sb-create-crop-tools">
                                    <p>Arraste a imagem para escolher o enquadramento.</p>
                                    <label for="sb-create-crop-zoom">Zoom <output x-text="Math.round(photoCropZoom * 100) + '%'"></output></label>
                                    <input id="sb-create-crop-zoom" type="range" min="1" max="3" step="0.05" x-model.number="photoCropZoom" @input="clampPhotoCrop()" :disabled="!photoCropReady">
                                    <label for="sb-create-crop-x">Horizontal <output x-text="photoCropPercent('x') + '%'"></output></label>
                                    <input id="sb-create-crop-x" type="range" min="0" max="100" step="1" :value="photoCropPercent('x')" @input="setPhotoCropPercent('x', $event.target.value)" :disabled="!photoCropReady">
                                    <label for="sb-create-crop-y">Vertical <output x-text="photoCropPercent('y') + '%'"></output></label>
                                    <input id="sb-create-crop-y" type="range" min="0" max="100" step="1" :value="photoCropPercent('y')" @input="setPhotoCropPercent('y', $event.target.value)" :disabled="!photoCropReady">
                                    <div class="sb-create-crop-buttons">
                                        <button type="button" @click="resetPhotoCrop()">Centralizar</button>
                                        <button type="button" @click="$refs.forgeCropInput.click()">Trocar foto</button>
                                    </div>
                                </div>
                            </div>
                            <p class="sb-create-crop-error" x-show="photoCropError" x-text="photoCropError" role="alert" x-cloak></p>
                            <footer class="sb-create-feature-footer">
                                <span>Recorte vertical · PNG 640 × 768</span>
                                <div class="sb-create-crop-buttons">
                                    <button type="button" @click="closePhotoCrop()">Cancelar</button>
                                    <button type="button" class="sb-create-crop-apply" :disabled="!photoCropReady || photoCropSaving" @click="applyPhotoCrop()" x-text="photoCropSaving ? 'Aplicando…' : 'Aplicar foto'"></button>
                                </div>
                            </footer>
                        </section>
                    </div>
                </form>
            </section>
        </div>

    </div>

    {{-- Bandeja física original e palco de animação, montados no contexto da janela. --}}
    @include('characters.components.forge-dice-stage')
    @include('characters.components.forge-dice-tray', ['characterTheme' => 'spellbound'])
</x-app-layout>
