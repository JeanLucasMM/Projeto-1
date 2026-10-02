<x-app-layout>
    @php
        $rarityLabels = [
            'common' => 'Comum',
            'uncommon' => 'Incomum',
            'rare' => 'Raro',
            'very_rare' => 'Muito Raro',
            'legendary' => 'Lendário',
            'artifact' => 'Artefato',
        ];

        $abilityLabels = [
            'strength' => 'Força',
            'dexterity' => 'Destreza',
            'constitution' => 'Constituição',
            'intelligence' => 'Inteligência',
            'wisdom' => 'Sabedoria',
            'charisma' => 'Carisma',
            'none' => 'Sem atributo',
            'custom' => 'Custom',
        ];

        $recoveryLabels = [
            'day' => 'Dia',
            'short_rest' => 'Descanso Curto',
            'long_rest' => 'Descanso Longo',
            'dawn' => 'Amanhecer',
            'single_use' => 'Uso Único',
            'custom' => 'Custom',
        ];

        $damageTypeDefinitions = [
            'Cortante',
            'Impacto',
            'Perfurante',
            'Ácido',
            'Frio',
            'Fogo',
            'Elétrico',
            'Energia',
            'Necrótico',
            'Veneno',
            'Psíquico',
            'Radiante',
            'Trovão',
        ];

        $weaponTraitDefinitions = [
            [
                'key' => 'ammunition',
                'name' => 'Munição',
                'description' => 'Você só pode usar uma arma com a propriedade Munição para realizar um ataque à distância se tiver munição disponível. O tipo de munição necessário é especificado junto com o alcance da arma. Cada ataque consome uma peça de munição. Sacar a munição faz parte do ataque, e você precisa de uma mão livre para carregar uma arma de uma mão. Após um combate, você pode gastar 1 minuto para recuperar metade da munição usada, arredondando para baixo; o restante é perdido.',
            ],
            [
                'key' => 'finesse',
                'name' => 'Acuidade',
                'description' => 'Ao realizar um ataque com uma arma que possui Acuidade, você pode usar o modificador de Força ou de Destreza, à sua escolha, para as jogadas de ataque e dano. Você deve usar o mesmo modificador para ambas as jogadas.',
            ],
            [
                'key' => 'heavy',
                'name' => 'Pesada',
                'description' => 'Você tem Desvantagem nas jogadas de ataque com uma arma Pesada se ela for uma arma corpo a corpo e sua Força for menor que 13, ou se ela for uma arma à distância e sua Destreza for menor que 13.',
            ],
            [
                'key' => 'light',
                'name' => 'Leve',
                'description' => 'Quando você usa a ação Atacar no seu turno e ataca com uma arma Leve, pode fazer um ataque extra como uma Ação Bônus mais tarde no mesmo turno. Esse ataque extra deve ser feito com uma arma Leve diferente, e você não adiciona seu modificador de habilidade ao dano do ataque extra, a menos que esse modificador seja negativo.',
            ],
            [
                'key' => 'loading',
                'name' => 'Carregamento',
                'description' => 'Você só pode disparar uma munição de uma arma com Carregamento quando usa uma ação, uma Ação Bônus ou uma Reação para dispará-la, independentemente do número de ataques que normalmente poderia realizar.',
            ],
            [
                'key' => 'range',
                'name' => 'Distância',
                'description' => 'Uma arma de longo alcance apresenta dois valores de alcance. O primeiro é o alcance normal da arma em Ft e o segundo é o alcance máximo. Ao atacar um alvo além do alcance normal, você tem Desvantagem na jogada de ataque. Você não pode atacar um alvo além do alcance máximo.',
            ],
            [
                'key' => 'reach',
                'name' => 'Alcance',
                'description' => 'Uma arma com Alcance adiciona 5 Ft ao seu alcance quando você ataca com ela e também ao determinar seu alcance para Ataques de Oportunidade realizados com essa arma.',
            ],
            [
                'key' => 'thrown',
                'name' => 'Arremesso',
                'description' => 'Se uma arma possuir a propriedade Arremesso, você pode arremessá-la para realizar um ataque à distância e pode sacá-la como parte desse ataque. Se for uma arma corpo a corpo, use o mesmo modificador de habilidade para as jogadas de ataque e dano que usaria em um ataque corpo a corpo com essa arma.',
            ],
            [
                'key' => 'two_handed',
                'name' => 'Duas Mãos',
                'description' => 'Uma arma de Duas Mãos exige o uso das duas mãos para atacar com ela.',
            ],
            [
                'key' => 'versatile',
                'name' => 'Versátil',
                'description' => 'Uma arma Versátil pode ser usada com uma ou duas mãos. Um valor de dano alternativo aparece associado à propriedade. A arma causa esse dano quando usada com as duas mãos para realizar um ataque corpo a corpo.',
            ],
        ];

        $masteryDefinitions = [
            [
                'key' => 'cleave',
                'name' => 'Cleave',
                'description' => 'Se você acertar uma criatura com uma jogada de ataque corpo a corpo usando esta arma, pode realizar uma jogada de ataque corpo a corpo com a arma contra uma segunda criatura que esteja a até 5 Ft da primeira e também dentro do seu alcance. Se acertar, a segunda criatura sofre o dano da arma, mas você não adiciona seu modificador de habilidade a esse dano, a menos que o modificador seja negativo. Você só pode realizar esse ataque extra uma vez por turno.',
            ],
            [
                'key' => 'graze',
                'name' => 'Graze',
                'description' => 'Se sua jogada de ataque com esta arma errar uma criatura, você pode causar a ela dano igual ao modificador de habilidade usado para realizar a jogada de ataque. Esse dano é do mesmo tipo causado pela arma e só pode ser aumentado aumentando esse modificador de habilidade.',
            ],
            [
                'key' => 'nick',
                'name' => 'Nick',
                'description' => 'Quando você realiza o ataque extra concedido pela propriedade Leve, pode fazê-lo como parte da ação Atacar em vez de usar uma Ação Bônus. Você só pode realizar esse ataque extra uma vez por turno.',
            ],
            [
                'key' => 'push',
                'name' => 'Push',
                'description' => 'Se você acertar uma criatura com esta arma, pode empurrá-la até 10 Ft em linha reta para longe de você, desde que ela seja de tamanho Grande ou menor.',
            ],
            [
                'key' => 'sap',
                'name' => 'Sap',
                'description' => 'Se você acertar uma criatura com esta arma, ela terá Desvantagem na próxima jogada de ataque que realizar antes do início do seu próximo turno.',
            ],
            [
                'key' => 'slow',
                'name' => 'Slow',
                'description' => 'Se você acertar uma criatura com esta arma e causar dano a ela, pode reduzir o Deslocamento dela em 10 Ft até o início do seu próximo turno. Se a criatura for atingida mais de uma vez por armas com esta maestria, a redução de Deslocamento não ultrapassa 10 Ft.',
            ],
            [
                'key' => 'topple',
                'name' => 'Topple',
                'description' => 'Se você acertar uma criatura com esta arma, pode forçá-la a realizar um teste de resistência de Constituição. A CD é igual a 8 + o modificador de habilidade usado na jogada de ataque + seu Bônus de Proficiência. Em caso de falha, a criatura fica Caída.',
            ],
            [
                'key' => 'vex',
                'name' => 'Vex',
                'description' => 'Se você acertar uma criatura com esta arma e causar dano a ela, terá Vantagem na sua próxima jogada de ataque contra essa criatura antes do fim do seu próximo turno.',
            ],
        ];
    @endphp

    @push('styles')
        <style>
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

        </style>
    @endpush

    @once
    <script>
        window.masterVaultPage = (
            initialItems,
            initialFolders,
            weaponTraitDefinitions,
            masteryDefinitions,
            abilityLabels,
            recoveryLabels,
            damageTypeDefinitions,
            urls,
            folderConfig
        ) => ({
                    items: Array.isArray(initialItems) ? initialItems : [],
                    folders: Array.isArray(initialFolders) ? initialFolders : [],
                    weaponTraitDefinitions: Array.isArray(weaponTraitDefinitions) ? weaponTraitDefinitions : [],
                    masteryDefinitions: Array.isArray(masteryDefinitions) ? masteryDefinitions : [],
                    abilityLabels: abilityLabels ?? {},
                    recoveryLabels: recoveryLabels ?? {},
                    damageTypeDefinitions: Array.isArray(damageTypeDefinitions) ? damageTypeDefinitions : [],
                    urls,
                    filter: 'all',
                    search: '',
                    activeFolderId: null,
                    formFolderId: null,
                    folderSystemReady: !!folderConfig?.ready,
                    folderScopeId: parseInt(folderConfig?.scopeId) || null,
                    folderModalOpen: false,
                    folderEditingId: null,
                    folderForm: { name: '', description: '', color: '#8A4B20' },
                    folderPalette: [
                        { key: 'couro', label: 'Couro', color: '#8A4B20' },
                        { key: 'carmesim', label: 'Carmesim', color: '#8A2314' },
                        { key: 'ambar', label: 'Âmbar', color: '#B56A14' },
                        { key: 'floresta', label: 'Floresta', color: '#2E6B41' },
                        { key: 'arcano', label: 'Arcano', color: '#1F6F73' },
                        { key: 'oceano', label: 'Oceano', color: '#355C8A' },
                        { key: 'mistico', label: 'Místico', color: '#6C3A7F' },
                        { key: 'magia', label: 'Magia', color: '#983B69' },
                        { key: 'sombra', label: 'Sombra', color: '#5E5B58' },
                        { key: 'obsidian', label: 'Obsidian', color: '#2E2E31' },
                    ],
                    folderSaving: false,
                    folderError: null,
                    folderMenuId: null,
                    draggedItemId: null,

                    previewOpen: false,
                    previewItem: null,

                    actionMenuId: null,

                    confirmOpen: false,
                    confirmMode: null,
                    confirmItem: null,
                    confirmBusy: false,

                    editorOpen: false,
                    editorTab: 'general',
                    editingId: null,
                    form: null,
                    tagsText: '',
                    imageFile: null,
                    imagePreview: null,
                    saving: false,
                    saveError: null,
                    busyId: null,
                    toastOpen: false,
                    toastMessage: '',
                    toastError: false,
                    toastTimer: null,

                    filterOptions: [
                        { key: 'all', label: 'Todos' },
                        { key: 'favorite', label: 'Favoritos' },
                        { key: 'wonderful', label: 'Maravilhosos' },
                        { key: 'technological', label: 'Tecnológicos' },
                        { key: 'mundane', label: 'Mundanos' },
                    ],

                    init() {
                        this.items = this.items.map(item => this.normalizeItem(item));
                        this.folders = this.folders.map(folder => this.normalizeFolder(folder));
                    },

                    get favoriteCount() {
                        return this.items.filter(item => !!item.favorite).length;
                    },

                    get activeFolder() {
                        if (this.activeFolderId === null) return null;
                        return this.folders.find(
                            folder => parseInt(folder.id) === parseInt(this.activeFolderId)
                        ) ?? null;
                    },

                    get unassignedCount() {
                        return this.items.filter(item => !item.folder_id).length;
                    },

                    get hasActiveSearch() {
                        return this.filter !== 'all' || this.normalizeSearch(this.search) !== '';
                    },

                    get showFolderOverview() {
                        return this.activeFolderId === null && !this.hasActiveSearch;
                    },

                    get showItemResultsSection() {
                        if (this.activeFolderId !== null || this.hasActiveSearch) return true;
                        return this.unassignedCount > 0 || this.folders.length === 0;
                    },

                    get resultHeading() {
                        if (this.hasActiveSearch) return 'Resultados';
                        if (this.activeFolder) return 'Itens';
                        return 'Itens sem pasta';
                    },

                    get filteredItems() {
                        const query = this.normalizeSearch(this.search);
                        const queryTerms = query
                            .split(/\s+/)
                            .map(term => term.trim())
                            .filter(Boolean);

                        return [...this.items]
                            .filter(item => {
                                if (this.activeFolderId !== null) {
                                    return parseInt(item.folder_id) === parseInt(this.activeFolderId);
                                }

                                if (!this.hasActiveSearch) {
                                    return !item.folder_id;
                                }

                                return true;
                            })
                            .filter(item => {
                                if (this.filter === 'favorite') return !!item.favorite;
                                if (['wonderful', 'technological', 'mundane'].includes(this.filter)) return item.type === this.filter;
                                return true;
                            })
                            .filter(item => {
                                if (!queryTerms.length) return true;

                                const haystack = this.itemSearchHaystack(item);

                                /*
                                 * Cada palavra digitada precisa existir em algum
                                 * ponto do item, mas não precisa estar adjacente.
                                 *
                                 * Ex.: "arma raro" encontra uma arma rara mesmo
                                 * que "Arma" esteja na categoria e "Raro" na raridade.
                                 */
                                return queryTerms.every(
                                    term => haystack.includes(term)
                                );
                            })
                            .sort((a, b) => {
                                if (!!a.favorite !== !!b.favorite) return a.favorite ? -1 : 1;
                                return String(a.name ?? '').localeCompare(String(b.name ?? ''), 'pt-BR');
                            });
                    },

                    get previewIndex() {
                        if (!this.previewItem) {
                            return -1;
                        }

                        return this.filteredItems.findIndex(
                            item =>
                                parseInt(item.id)
                                === parseInt(this.previewItem.id)
                        );
                    },

                    get hasPreviousPreview() {
                        return this.previewIndex > 0;
                    },

                    get hasNextPreview() {
                        return (
                            this.previewIndex >= 0
                            && this.previewIndex < this.filteredItems.length - 1
                        );
                    },

                    normalizeFolder(raw) {
                        return {
                            id: parseInt(raw?.id),
                            name: raw?.name ?? '',
                            description: raw?.description ?? '',
                            color: raw?.color ?? '#8A4B20',
                            text_color: raw?.text_color ?? null,
                            image_url: raw?.image_url ?? null,
                            position: parseInt(raw?.position) || 0,
                            items_count: parseInt(raw?.items_count) || 0,
                        };
                    },

                    rgbaFromHex(hex, alpha = 1) {
                        const normalized = String(hex ?? '').replace('#', '').trim();
                        if (!/^[0-9A-Fa-f]{6}$/.test(normalized)) {
                            return `rgba(138, 75, 32, ${alpha})`;
                        }

                        const r = parseInt(normalized.slice(0, 2), 16);
                        const g = parseInt(normalized.slice(2, 4), 16);
                        const b = parseInt(normalized.slice(4, 6), 16);
                        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                    },

                    folderAccent(folder) {
                        return folder?.color || '#8A4B20';
                    },

                    folderCardStyle(folder) {
                        const accent = this.folderAccent(folder);
                        return {
                            '--folder-accent': accent,
                        };
                    },

                    folderIconStyle(folder) {
                        const accent = this.folderAccent(folder);
                        return {
                            color: accent,
                            background: this.rgbaFromHex(accent, .10),
                        };
                    },

                    folderPreviewItems(folderId) {
                        return this.items
                            .filter(item => parseInt(item.folder_id) === parseInt(folderId))
                            .slice(0, 7);
                    },

                    folderPlaceholderLetter(folder) {
                        return String(folder?.name ?? '?').trim().charAt(0).toUpperCase() || '?';
                    },

                    folderItemBadge(folderId) {
                        const count = this.folderItemCount(folderId);
                        return `${count} ${count === 1 ? 'item' : 'itens'}`;
                    },

                    toggleFolderMenu(folderId) {
                        const normalizedId = parseInt(folderId);
                        this.folderMenuId = this.folderMenuId === normalizedId ? null : normalizedId;
                    },

                    closeFolderMenu() {
                        this.folderMenuId = null;
                    },

                    folderItemCount(folderId) {
                        return this.items.filter(
                            item => parseInt(item.folder_id) === parseInt(folderId)
                        ).length;
                    },

                    openFolder(folder) {
                        this.activeFolderId = parseInt(folder.id);
                        this.search = '';
                        this.filter = 'all';
                        this.closeActionMenu();
                        this.closeFolderMenu();
                    },

                    closeFolder() {
                        this.activeFolderId = null;
                        this.search = '';
                        this.filter = 'all';
                        this.closeFolderMenu();
                    },

                    openFolderCreate() {
                        if (!this.folderSystemReady) {
                            this.showToast('O sistema de pastas ainda não foi migrado. Rode php artisan migrate.', true);
                            return;
                        }

                        this.folderEditingId = null;
                        this.folderForm = {
                            name: '',
                            description: '',
                            color: this.folderPalette?.[0]?.color ?? '#8A4B20',
                        };
                        this.folderError = null;
                        this.closeFolderMenu();
                        this.folderModalOpen = true;
                    },

                    openFolderEdit(folder) {
                        this.folderEditingId = parseInt(folder.id);
                        this.folderForm = {
                            name: folder.name ?? '',
                            description: folder.description ?? '',
                            color: folder.color ?? (this.folderPalette?.[0]?.color ?? '#8A4B20'),
                        };
                        this.folderError = null;
                        this.closeFolderMenu();
                        this.folderModalOpen = true;
                    },

                    closeFolderModal() {
                        if (this.folderSaving) return;
                        this.folderModalOpen = false;
                        this.folderEditingId = null;
                        this.folderError = null;
                        this.folderForm = {
                            name: '',
                            description: '',
                            color: this.folderPalette?.[0]?.color ?? '#8A4B20',
                        };
                    },

                    async saveFolder() {
                        if (this.folderSaving) return;

                        const name = String(this.folderForm?.name ?? '').trim();
                        if (!name) {
                            this.folderError = 'Informe um nome para a pasta.';
                            return;
                        }

                        this.folderSaving = true;
                        this.folderError = null;

                        const editing = this.folderEditingId !== null;
                        const url = editing
                            ? this.urls.folderUpdate.replace('__FOLDER__', this.folderEditingId)
                            : this.urls.folderStore;

                        const formData = new FormData();
                        if (!editing) {
                            formData.append('scope_type', 'master.vault');
                            formData.append('scope_id', String(this.folderScopeId));
                        }
                        formData.append('name', name);
                        formData.append('description', String(this.folderForm?.description ?? '').trim());
                        formData.append('color', String(this.folderForm?.color ?? (this.folderPalette?.[0]?.color ?? '#8A4B20')));

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: this.headers(),
                                body: formData,
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) {
                                const errors = data?.errors ? Object.values(data.errors).flat().filter(Boolean) : [];
                                throw new Error(errors.length ? errors.join(' ') : (data?.message ?? 'Não foi possível salvar a pasta.'));
                            }

                            const folder = this.normalizeFolder(data.folder);
                            const index = this.folders.findIndex(current => parseInt(current.id) === parseInt(folder.id));
                            if (index >= 0) this.folders.splice(index, 1, folder);
                            else this.folders.push(folder);

                            this.folders.sort((a, b) => (a.position - b.position) || String(a.name).localeCompare(String(b.name), 'pt-BR'));
                            this.folderSaving = false;
                            this.closeFolderModal();
                            this.showToast(editing ? 'Pasta atualizada.' : 'Pasta criada.');
                        } catch (error) {
                            this.folderError = error?.message ?? 'Não foi possível salvar a pasta.';
                            this.folderSaving = false;
                        }
                    },

                    async deleteFolder(folder) {
                        if (!folder || this.folderSaving) return;
                        this.closeFolderMenu();
                        if (!window.confirm(`Excluir a pasta "${folder.name}"? Os itens continuarão no Cofre e serão movidos para Sem pasta.`)) return;

                        this.folderSaving = true;
                        try {
                            const response = await fetch(
                                this.urls.folderDestroy.replace('__FOLDER__', folder.id),
                                { method: 'DELETE', headers: this.headers() }
                            );
                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível excluir a pasta.');

                            this.items.forEach(item => {
                                if (parseInt(item.folder_id) === parseInt(folder.id)) item.folder_id = null;
                            });
                            this.folders = this.folders.filter(current => parseInt(current.id) !== parseInt(folder.id));
                            if (parseInt(this.activeFolderId) === parseInt(folder.id)) this.activeFolderId = null;
                            this.showToast('Pasta excluída. Os itens foram movidos para Sem pasta.');
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível excluir a pasta.', true);
                        } finally {
                            this.folderSaving = false;
                        }
                    },

                    startItemDrag(item, event) {
                        this.draggedItemId = parseInt(item.id);
                        if (event?.dataTransfer) {
                            event.dataTransfer.effectAllowed = 'move';
                            event.dataTransfer.setData('text/plain', String(item.id));
                        }
                    },

                    async dropItemIntoFolder(folder) {
                        const itemId = parseInt(this.draggedItemId);
                        if (!itemId || !folder) return;
                        this.draggedItemId = null;

                        try {
                            await this.moveVaultItem(itemId, folder.id);
                            this.showToast(`Item movido para ${folder.name}.`);
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível mover o item.', true);
                        }
                    },

                    async moveVaultItem(itemId, folderId, updateForm = false) {
                        if (!this.folderSystemReady) return null;

                        const response = await fetch(this.urls.moveFolder, {
                            method: 'PATCH',
                            headers: {
                                ...this.headers(),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                scope_type: 'master.vault',
                                scope_id: this.folderScopeId,
                                item_type: 'vault_item',
                                item_id: parseInt(itemId),
                                folder_id: folderId ? parseInt(folderId) : null,
                            }),
                        });

                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) throw new Error(data?.message ?? 'Não foi possível mover o item.');

                        const item = this.items.find(current => parseInt(current.id) === parseInt(itemId));
                        if (item) item.folder_id = data.folder_id ? parseInt(data.folder_id) : null;
                        if (updateForm) this.formFolderId = data.folder_id ? parseInt(data.folder_id) : null;
                        return data;
                    },

                    natureCount(nature) {
                        return this.items.filter(item => item.type === nature).length;
                    },

                    natureLabel(nature) {
                        return {
                            wonderful: 'Maravilhoso',
                            technological: 'Tecnológico',
                            mundane: 'Mundano',
                        }[nature] ?? 'Item';
                    },

                    rarityLabel(rarity) {
                        return {
                            common: 'Comum',
                            uncommon: 'Incomum',
                            rare: 'Raro',
                            very_rare: 'Muito Raro',
                            legendary: 'Lendário',
                            artifact: 'Artefato',
                        }[rarity] ?? rarity ?? '';
                    },

                    recoveryLabel(recovery) {
                        return this.recoveryLabels?.[recovery]
                            ?? recovery
                            ?? '';
                    },

                    weaponCategoryLabel(category) {
                        return {
                            simple: 'Arma Simples',
                            martial: 'Arma Marcial',
                            custom: 'Arma Custom',
                        }[category] ?? 'Arma';
                    },

                    armorCategoryLabel(category) {
                        return {
                            light: 'Armadura Leve',
                            medium: 'Armadura Média',
                            heavy: 'Armadura Pesada',
                            custom: 'Armadura Especial',
                        }[category] ?? 'Armadura';
                    },

                    previewCategoryLabel(item) {
                        if (item?.properties?.weapon) {
                            return this.weaponCategoryLabel(
                                item.properties.weapon.category
                            );
                        }

                        if (item?.properties?.armor) {
                            return this.armorCategoryLabel(
                                item.properties.armor.category
                            );
                        }

                        if (item?.properties?.shield) {
                            return 'Escudo';
                        }

                        return 'Outro Item';
                    },

                    cardNatureLine(item) {
                        if (!item) return '';

                        const parts = [
                            `Item ${this.natureLabel(item.type)}`,
                        ];

                        if (item.rarity) {
                            parts.push(
                                this.rarityLabel(item.rarity)
                            );
                        }

                        let line =
                            parts.join(', ');

                        if (
                            this.toBoolean(
                                item.requires_attunement
                            )
                        ) {
                            line += ' (Sintonizável)';
                        }

                        return line;
                    },

                    cardCategoryLine(item) {
                        if (!item) return '';

                        if (item?.properties?.weapon) {
                            const weapon =
                                item.properties.weapon;

                            const category =
                                this.weaponCategoryLabel(
                                    weapon.category
                                );

                            const type =
                                String(
                                    weapon.weapon_type
                                    ?? ''
                                ).trim();

                            const damage =
                                this.weaponBaseDamageDice(
                                    weapon
                                );

                            const damageType =
                                String(
                                    weapon?.damage?.type
                                    ?? ''
                                ).trim();

                            const base =
                                type
                                    ? `${category} - ${type}`
                                    : category;

                            const damageLabel =
                                [damage, damageType]
                                    .filter(Boolean)
                                    .join(' ');

                            return damageLabel
                                ? `${base} (${damageLabel})`
                                : base;
                        }

                        if (item?.properties?.armor) {
                            const armor =
                                item.properties.armor;

                            const category =
                                this.armorCategoryLabel(
                                    armor.category
                                );

                            const type =
                                String(
                                    armor.armor_type
                                    ?? ''
                                ).trim();

                            return type
                                ? `${category} - ${type}`
                                : category;
                        }

                        if (item?.properties?.shield) {
                            const label =
                                String(
                                    item.properties.shield.label
                                    ?? ''
                                ).trim();

                            return label
                                ? `Escudo - ${label}`
                                : 'Escudo';
                        }

                        return '';
                    },

                    cardFeatures(item) {
                        return Array.isArray(
                            item?.properties?.features
                        )
                            ? item.properties.features
                            : [];
                    },

                    openPreview(item) {
                        if (!item) return;

                        this.previewItem =
                            this.toPlainData(
                                this.normalizeItem(item)
                            );

                        this.previewOpen = true;
                    },

                    closePreview() {
                        this.previewOpen = false;
                        this.previewItem = null;
                    },

                    previewAdjacent(direction) {
                        const index =
                            this.previewIndex;

                        if (
                            index < 0
                            || ![-1, 1].includes(direction)
                        ) {
                            return;
                        }

                        const nextIndex =
                            index + direction;

                        if (
                            nextIndex < 0
                            || nextIndex >= this.filteredItems.length
                        ) {
                            return;
                        }

                        this.openPreview(
                            this.filteredItems[nextIndex]
                        );
                    },

                    editFromPreview() {
                        if (!this.previewItem) return;

                        const item =
                            this.toPlainData(
                                this.previewItem
                            );

                        this.closePreview();
                        this.openEdit(item);
                    },

                    toggleActionMenu(item) {
                        const id =
                            parseInt(item?.id);

                        if (!Number.isFinite(id)) {
                            return;
                        }

                        this.actionMenuId =
                            this.actionMenuId === id
                                ? null
                                : id;
                    },

                    closeActionMenu() {
                        this.actionMenuId = null;
                    },

                    editFromMenu(item) {
                        this.closeActionMenu();
                        this.openEdit(item);
                    },

                    requestDuplicate(item) {
                        if (!item) return;

                        this.closeActionMenu();

                        this.confirmMode =
                            'duplicate';

                        this.confirmItem =
                            this.toPlainData(
                                this.normalizeItem(item)
                            );

                        this.confirmOpen =
                            true;
                    },

                    requestDelete(item) {
                        if (!item) return;

                        this.closeActionMenu();

                        this.confirmMode =
                            'delete';

                        this.confirmItem =
                            this.toPlainData(
                                this.normalizeItem(item)
                            );

                        this.confirmOpen =
                            true;
                    },

                    closeConfirm() {
                        if (this.confirmBusy) {
                            return;
                        }

                        this.confirmOpen =
                            false;

                        this.confirmMode =
                            null;

                        this.confirmItem =
                            null;
                    },

                    confirmTitle() {
                        if (this.confirmMode === 'duplicate') {
                            return 'Criar uma cópia?';
                        }

                        if (this.confirmMode === 'delete') {
                            return 'Excluir item do Vault?';
                        }

                        return 'Confirmar ação';
                    },

                    confirmMessage() {
                        const name =
                            this.confirmItem?.name
                            || 'este item';

                        if (this.confirmMode === 'duplicate') {
                            return `Uma nova cópia de "${name}" será criada no Vault. O original não será alterado.`;
                        }

                        if (this.confirmMode === 'delete') {
                            return `"${name}" será removido do Vault. Espólios que já receberam um snapshot não serão alterados.`;
                        }

                        return '';
                    },

                    async confirmVaultAction() {
                        if (
                            this.confirmBusy
                            || !this.confirmItem
                        ) {
                            return;
                        }

                        this.confirmBusy =
                            true;

                        const mode =
                            this.confirmMode;

                        const item =
                            this.toPlainData(
                                this.confirmItem
                            );

                        try {
                            if (mode === 'duplicate') {
                                await this.duplicateItem(
                                    item
                                );
                            }

                            if (mode === 'delete') {
                                await this.deleteVaultItem(
                                    item
                                );
                            }

                            this.confirmOpen =
                                false;

                            this.confirmMode =
                                null;

                            this.confirmItem =
                                null;
                        } finally {
                            this.confirmBusy =
                                false;
                        }
                    },

                    normalizeSearch(value) {
                        return String(value ?? '')
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .toLocaleLowerCase('pt-BR')
                            .trim();
                    },

                    itemSearchHaystack(item) {
                        if (!item) return '';

                        const properties =
                            item?.properties
                            && typeof item.properties === 'object'
                                ? item.properties
                                : {};

                        const weapon = properties?.weapon ?? null;
                        const armor = properties?.armor ?? null;
                        const shield = properties?.shield ?? null;

                        const features = Array.isArray(properties?.features)
                            ? properties.features
                            : [];

                        const customProperties = Array.isArray(properties?.custom_properties)
                            ? properties.custom_properties
                            : [];

                        const weaponTraits = Array.isArray(weapon?.traits)
                            ? weapon.traits
                            : [];

                        const weaponMasteries = Array.isArray(weapon?.masteries)
                            ? weapon.masteries
                            : [];

                        const extraDamage = Array.isArray(weapon?.extra_damage)
                            ? weapon.extra_damage
                            : [];

                        const terms = [
                            item.name,
                            item.description,

                            // Valores crus do banco.
                            item.type,
                            item.rarity,

                            // Labels que o Mestre realmente enxerga.
                            this.natureLabel(item.type),
                            this.rarityLabel(item.rarity),
                            this.cardNatureLine(item),
                            this.cardCategoryLine(item),
                            this.previewCategoryLabel(item),

                            // Tags explícitas do Vault.
                            ...(Array.isArray(item.tags) ? item.tags : []),

                            // Termos semânticos úteis.
                            weapon ? 'arma' : '',
                            armor ? 'armadura' : '',
                            shield ? 'escudo' : '',
                            item.favorite ? 'favorito favorita' : '',
                            item.requires_attunement
                                ? 'sintonizacao sintonia sintonizavel'
                                : '',
                            item.is_magical
                                ? 'magico magica magia'
                                : '',

                            // Arma.
                            weapon?.category,
                            weapon ? this.weaponCategoryLabel(weapon.category) : '',
                            weapon?.weapon_type,
                            weapon ? this.weaponBaseDamageDice(weapon) : '',
                            weapon ? this.weaponDamagePreview(weapon) : '',
                            weapon?.damage?.type,
                            weapon?.loading_capacity,

                            ...weaponTraits.flatMap(trait => [
                                trait?.key,
                                trait?.name,
                                trait?.description,
                                this.weaponRuleCompactValue(trait, weapon),
                            ]),

                            ...weaponMasteries.flatMap(mastery => [
                                mastery?.key,
                                mastery?.name,
                                mastery?.description,
                            ]),

                            ...extraDamage.flatMap(damage => [
                                damage?.name,
                                damage?.type,
                                damage?.description,
                            ]),

                            // Armadura / escudo.
                            armor?.category,
                            armor ? this.armorCategoryLabel(armor.category) : '',
                            armor?.armor_type,
                            shield?.label,

                            // Habilidades e propriedades personalizadas.
                            ...features.flatMap(feature => [
                                feature?.title,
                                feature?.description,
                            ]),

                            ...customProperties.flatMap(property => [
                                property?.title,
                                property?.value,
                                property?.description,
                            ]),

                            // Fallback para estruturas futuras.
                            JSON.stringify(properties),
                        ];

                        return this.normalizeSearch(
                            terms
                                .filter(value =>
                                    value !== null
                                    && value !== undefined
                                    && String(value).trim() !== ''
                                )
                                .join(' ')
                        );
                    },

                    blankProperties() {
                        return {
                            nature: 'mundane',
                            inventory: { equippable: false },
                            custom_properties: [],
                            features: [],
                            optional_rules: {},
                        };
                    },

                    blankWeapon() {
                        return {
                            category: 'simple',
                            category_custom: '',
                            weapon_type: '',
                            magic_bonus: 0,
                            traits: [],
                            damage: {
                                count: 1,
                                die: 'd4',
                                ability: 'dexterity',
                                ability_custom: '',
                                type: 'Perfurante',
                                type_custom_mode: false,
                            },

                            /*
                            |--------------------------------------------------------------------------
                            | Distância e Alcance
                            |--------------------------------------------------------------------------
                            |
                            | Mantemos os nomes já compreendidos pelas integrações
                            | do Inventário/Ataques:
                            |
                            | range_normal_ft
                            | range_long_ft
                            | reach_ft
                            |
                            */
                            range_normal_ft: null,
                            range_long_ft: null,
                            reach_ft: 10,
                            loading_capacity: null,

                            extra_damage: [],
                            masteries: [],
                        };
                    },

                    blankArmor() {
                        return {
                            category: 'light',
                            category_custom: '',
                            armor_type: '',
                            base_ac: 11,
                            magic_bonus: 0,
                            dexterity_mode: 'full',
                            dexterity_cap: 2,
                            ability_modifiers: [],
                        };
                    },

                    blankShield() {
                        return {
                            label: 'Escudo',
                            ac_bonus: 2,
                            magic_bonus: 0,
                        };
                    },

                    blankFeature() {
                        return {
                            title: '',
                            description: '',
                            editor_open: true,
                            usage: {
                                enabled: false,
                                counter_mode: 'spend',
                                current: 1,
                                max: 1,
                                recovery: 'day',
                                recovery_custom: '',
                            },
                        };
                    },

                    toBoolean(value) {
                        if (typeof value === 'boolean') {
                            return value;
                        }

                        if (typeof value === 'number') {
                            return value !== 0;
                        }

                        if (typeof value === 'string') {
                            const normalized =
                                value
                                    .trim()
                                    .toLowerCase();

                            if (
                                [
                                    '',
                                    '0',
                                    'false',
                                    'off',
                                    'no',
                                    'null',
                                    'undefined',
                                ].includes(normalized)
                            ) {
                                return false;
                            }

                            if (
                                [
                                    '1',
                                    'true',
                                    'on',
                                    'yes',
                                ].includes(normalized)
                            ) {
                                return true;
                            }
                        }

                        return !!value;
                    },

                    blankForm() {
                        return {
                            name: '',
                            type: 'mundane',
                            description: '',
                            image_url: null,
                            remove_image: false,
                            default_quantity: 1,
                            weight: null,
                            is_magical: false,
                            rarity: 'common',
                            requires_attunement: false,
                            is_cursed: false,
                            curse_name: '',
                            curse_description: '',
                            armor_class: null,
                            damage: '',
                            attack_bonus: null,
                            damage_bonus: null,
                            ability_bonuses: {},
                            properties: this.blankProperties(),
                            modifiers: {},
                            notes: '',
                            tags: [],
                            favorite: false,
                        };
                    },

                    normalizeFeatureUsage(raw) {
                        const enabled =
                            this.toBoolean(
                                raw?.enabled
                            );

                        if (!raw || !enabled) {
                            return {
                                enabled: false,
                                counter_mode: 'spend',
                                current: 1,
                                max: 1,
                                recovery: 'day',
                                recovery_custom: '',
                            };
                        }

                        const max = Math.max(1, parseInt(raw.max) || 1);
                        const counterMode = ['spend', 'build'].includes(raw.counter_mode)
                            ? raw.counter_mode
                            : 'spend';

                        const parsedCurrent = parseInt(raw.current);
                        const defaultCurrent = counterMode === 'build' ? 0 : max;
                        const current = Math.max(
                            0,
                            Math.min(
                                max,
                                Number.isFinite(parsedCurrent)
                                    ? parsedCurrent
                                    : defaultCurrent
                            )
                        );

                        const recovery = Object.prototype.hasOwnProperty.call(
                            this.recoveryLabels,
                            raw.recovery
                        )
                            ? raw.recovery
                            : 'day';

                        return {
                            enabled: true,
                            counter_mode: counterMode,
                            current,
                            max,
                            recovery,
                            recovery_custom: raw.recovery_custom ?? '',
                        };
                    },

                    normalizeWeapon(raw) {
                        const source = raw && typeof raw === 'object' && !Array.isArray(raw)
                            ? raw
                            : {};

                        const weapon = {
                            ...this.blankWeapon(),
                            ...source,
                        };

                        weapon.damage = {
                            ...this.blankWeapon().damage,
                            ...(source.damage && typeof source.damage === 'object'
                                ? source.damage
                                : {}),
                        };

                        weapon.damage.type_custom_mode =
                            !!weapon.damage.type
                            && !this.damageTypeIsStandard(
                                weapon.damage.type
                            );

                        /*
                        | Compatibilidade com a V10.5:
                        | se existir o formato temporário aninhado, migramos
                        | automaticamente para os campos flat usados pelo sistema.
                        */
                        const legacyRange =
                            source.range
                            && typeof source.range === 'object'
                            && !Array.isArray(source.range)
                                ? source.range
                                : null;

                        const legacyReach =
                            source.reach
                            && typeof source.reach === 'object'
                            && !Array.isArray(source.reach)
                                ? source.reach
                                : null;

                        weapon.range_normal_ft =
                            this.nullableNumber(
                                source.range_normal_ft
                                ?? (
                                    legacyRange?.unit === 'm'
                                        ? (
                                            legacyRange?.normal !== null
                                            && legacyRange?.normal !== undefined
                                                ? Math.round(
                                                    Number(legacyRange.normal) * 3.28084
                                                )
                                                : null
                                        )
                                        : legacyRange?.normal
                                ),
                                true
                            );

                        weapon.range_long_ft =
                            this.nullableNumber(
                                source.range_long_ft
                                ?? (
                                    legacyRange?.unit === 'm'
                                        ? (
                                            legacyRange?.max !== null
                                            && legacyRange?.max !== undefined
                                                ? Math.round(
                                                    Number(legacyRange.max) * 3.28084
                                                )
                                                : null
                                        )
                                        : legacyRange?.max
                                ),
                                true
                            );

                        weapon.reach_ft =
                            this.nullableNumber(
                                source.reach_ft
                                ?? (
                                    legacyReach?.unit === 'm'
                                        ? (
                                            legacyReach?.distance !== null
                                            && legacyReach?.distance !== undefined
                                                ? Math.round(
                                                    Number(legacyReach.distance) * 3.28084
                                                )
                                                : 10
                                        )
                                        : (
                                            legacyReach?.distance
                                            ?? 10
                                        )
                                ),
                                true
                            );

                        weapon.loading_capacity =
                            this.nullableNumber(
                                source.loading_capacity,
                                false
                            );

                        delete weapon.range;
                        delete weapon.reach;

                        if (!['simple', 'martial', 'custom'].includes(weapon.category)) {
                            weapon.category = 'custom';
                            weapon.category_custom = source.category ?? '';
                        }

                        weapon.traits = Array.isArray(source.traits)
                            ? source.traits.map(entry => {
                                const key = entry?.key ?? '';
                                const custom = this.toBoolean(entry?.custom);
                                const definition = this.weaponTraitDefinitions.find(
                                    current => current.key === key
                                );

                                return {
                                    key,
                                    name: custom
                                        ? (entry?.name ?? '')
                                        : (definition?.name ?? entry?.name ?? ''),
                                    description: custom
                                        ? (entry?.description ?? '')
                                        : (definition?.description ?? entry?.description ?? ''),
                                    custom,
                                };
                            })
                            : [];

                        weapon.masteries = Array.isArray(source.masteries)
                            ? source.masteries.map(entry => {
                                const key = entry?.key ?? '';
                                const custom = this.toBoolean(entry?.custom);
                                const definition = this.masteryDefinitions.find(
                                    current => current.key === key
                                );

                                return {
                                    key,
                                    name: custom
                                        ? (entry?.name ?? '')
                                        : (definition?.name ?? entry?.name ?? ''),
                                    description: custom
                                        ? (entry?.description ?? '')
                                        : (definition?.description ?? entry?.description ?? ''),
                                    custom,
                                };
                            })
                            : [];

                        weapon.extra_damage = Array.isArray(source.extra_damage)
                            ? source.extra_damage.map(part => ({
                                count: Math.max(1, parseInt(part?.count) || 1),
                                die: part?.die ?? 'd6',
                                type: part?.type ?? '',
                                type_custom_mode:
                                    !!part?.type
                                    && !this.damageTypeIsStandard(part.type),
                            }))
                            : [];

                        return weapon;
                    },

                    armorDexterityDefaults(category) {
                        if (category === 'light') {
                            return { mode: 'full', cap: 2 };
                        }

                        if (category === 'medium') {
                            return { mode: 'capped', cap: 2 };
                        }

                        return { mode: 'none', cap: 2 };
                    },

                    normalizeArmor(raw) {
                        const source = raw && typeof raw === 'object' && !Array.isArray(raw)
                            ? raw
                            : {};

                        const armor = {
                            ...this.blankArmor(),
                            ...source,
                        };

                        const category = ['light', 'medium', 'heavy', 'custom'].includes(armor.category)
                            ? armor.category
                            : 'custom';

                        const defaults = this.armorDexterityDefaults(category);
                        const dexterityMode = ['none', 'full', 'capped'].includes(armor.dexterity_mode)
                            ? armor.dexterity_mode
                            : defaults.mode;

                        const abilities = Array.isArray(armor.ability_modifiers)
                            ? [...new Set(
                                armor.ability_modifiers.filter(
                                    ability => [
                                        'strength',
                                        'constitution',
                                        'intelligence',
                                        'wisdom',
                                        'charisma',
                                    ].includes(ability)
                                )
                            )]
                            : [];

                        return {
                            ...armor,
                            category,
                            category_custom: category === 'custom'
                                ? (armor.category_custom ?? '')
                                : '',
                            armor_type: armor.armor_type ?? '',
                            base_ac: Math.max(0, parseInt(armor.base_ac) || 0),
                            magic_bonus: parseInt(armor.magic_bonus) || 0,
                            dexterity_mode: dexterityMode,
                            dexterity_cap: Math.max(
                                0,
                                parseInt(armor.dexterity_cap) || defaults.cap
                            ),
                            ability_modifiers: abilities,
                        };
                    },

                    toPlainData(value) {
                        if (
                            value === null
                            || typeof value !== 'object'
                        ) {
                            return value;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Alpine usa Proxy para dados reativos
                        |--------------------------------------------------------------------------
                        |
                        | A API nativa de clonagem profunda do navegador não aceita
                        | Proxy. Alpine.raw() remove a camada reativa do nível atual
                        | e a recursão converte toda a árvore para dados simples.
                        |
                        */
                        const rawValue =
                            window.Alpine
                            && typeof window.Alpine.raw === 'function'
                                ? window.Alpine.raw(value)
                                : value;

                        if (Array.isArray(rawValue)) {
                            return rawValue.map(
                                entry => this.toPlainData(entry)
                            );
                        }

                        const plain = {};

                        Object.keys(rawValue).forEach(
                            key => {
                                const child = rawValue[key];

                                if (
                                    typeof child === 'function'
                                    || typeof child === 'undefined'
                                ) {
                                    return;
                                }

                                plain[key] =
                                    this.toPlainData(child);
                            }
                        );

                        return plain;
                    },

                    normalizeProperties(raw, rawItem = null) {
                        const properties = raw && typeof raw === 'object' && !Array.isArray(raw)
                            ? this.toPlainData(raw)
                            : {};

                        let nature = properties.nature;

                        if (!['wonderful', 'technological', 'mundane'].includes(nature)) {
                            nature = ['wonderful', 'technological', 'mundane'].includes(rawItem?.type)
                                ? rawItem.type
                                : (rawItem?.is_magical ? 'wonderful' : 'mundane');
                        }

                        properties.nature = nature;
                        properties.inventory = {
                            equippable: !!properties?.inventory?.equippable,
                        };

                        if (properties.weapon && typeof properties.weapon === 'object') {
                            properties.weapon = this.normalizeWeapon(properties.weapon);
                        } else {
                            delete properties.weapon;
                        }

                        if (properties.armor && typeof properties.armor === 'object') {
                            properties.armor = this.normalizeArmor(properties.armor);
                        } else {
                            delete properties.armor;
                        }

                        if (properties.shield && typeof properties.shield === 'object') {
                            properties.shield = {
                                ...this.blankShield(),
                                ...properties.shield,
                            };
                        } else {
                            delete properties.shield;
                        }

                        if (properties.armor || properties.shield) {
                            properties.inventory.equippable = true;
                        }

                        properties.custom_properties = Array.isArray(properties.custom_properties)
                            ? properties.custom_properties.map(entry => ({
                                title: entry?.title ?? '',
                                value: entry?.value ?? '',
                                description: entry?.description ?? '',
                            }))
                            : [];

                        properties.features = Array.isArray(properties.features)
                            ? properties.features.map(feature => ({
                                title: feature?.title ?? '',
                                description: feature?.description ?? '',
                                editor_open: !!feature?.editor_open,
                                usage: this.normalizeFeatureUsage(feature?.usage),
                            }))
                            : [];

                        properties.optional_rules =
                            properties.optional_rules
                            && typeof properties.optional_rules === 'object'
                                ? properties.optional_rules
                                : {};

                        return properties;
                    },

                    normalizeItem(raw) {
                        const properties = this.normalizeProperties(
                            raw?.properties,
                            raw
                        );

                        return {
                            id: parseInt(raw?.id),
                            name: raw?.name ?? '',
                            type: properties.nature,
                            description: raw?.description ?? '',
                            image_url: raw?.image_url ?? null,
                            default_quantity: Math.max(
                                1,
                                parseInt(raw?.default_quantity) || 1
                            ),
                            weight: raw?.weight ?? null,
                            is_magical: properties.nature === 'wonderful',
                            rarity: raw?.rarity ?? 'common',
                            requires_attunement: this.toBoolean(raw?.requires_attunement),
                            is_cursed: this.toBoolean(raw?.is_cursed),
                            curse_name: raw?.curse_name ?? '',
                            curse_description: raw?.curse_description ?? '',
                            armor_class: raw?.armor_class ?? null,
                            damage: raw?.damage ?? '',
                            attack_bonus: raw?.attack_bonus ?? null,
                            damage_bonus: raw?.damage_bonus ?? null,
                            ability_bonuses: raw?.ability_bonuses ?? {},
                            properties,
                            modifiers: raw?.modifiers ?? {},
                            notes: raw?.notes ?? '',
                            tags: Array.isArray(raw?.tags) ? raw.tags : [],
                            favorite: this.toBoolean(raw?.favorite),
                            folder_id: raw?.folder_id ? parseInt(raw.folder_id) : null,
                        };
                    },

                    openCreate() {
                        this.editingId = null;
                        this.form = this.blankForm();
                        this.formFolderId = this.activeFolderId;
                        this.tagsText = '';
                        this.editorTab = 'general';
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.saveError = null;
                        this.editorOpen = true;
                    },

                    openEdit(item) {
                        const normalized = this.normalizeItem(item);

                        this.editingId = normalized.id;
                        this.form = this.toPlainData(normalized);
                        this.formFolderId = normalized.folder_id;
                        this.tagsText = (normalized.tags ?? []).join(', ');
                        this.editorTab = 'general';
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.saveError = null;
                        this.editorOpen = true;
                    },

                    closeEditor() {
                        if (this.saving) return;

                        this.editorOpen = false;
                        this.editingId = null;
                        this.form = null;
                        this.formFolderId = null;
                        this.tagsText = '';
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.saveError = null;
                    },

                    setNature(nature) {
                        if (!['wonderful', 'technological', 'mundane'].includes(nature)) {
                            return;
                        }

                        this.form.type = nature;
                        this.form.properties.nature = nature;
                        this.form.is_magical = nature === 'wonderful';

                        if (nature !== 'wonderful') {
                            this.form.requires_attunement = false;
                        }
                    },

                    setEquippable(value) {
                        if (!this.form) return;
                        this.form.properties.inventory.equippable = !!value;
                    },

                    hasModule(module) {
                        return !!this.form?.properties?.[module];
                    },

                    itemCategory() {
                        if (this.form?.properties?.weapon) return 'weapon';
                        if (this.form?.properties?.armor) return 'armor';
                        if (this.form?.properties?.shield) return 'shield';
                        return 'other';
                    },

                    setItemCategory(category) {
                        if (
                            !this.form
                            || !['weapon', 'armor', 'shield', 'other'].includes(category)
                        ) {
                            return;
                        }

                        const properties = this.form.properties;

                        if (category === 'weapon') {
                            properties.weapon =
                                properties.weapon ?? this.blankWeapon();

                            delete properties.armor;
                            delete properties.shield;

                            properties.inventory.equippable = true;
                            return;
                        }

                        if (category === 'armor') {
                            properties.armor =
                                properties.armor ?? this.blankArmor();

                            delete properties.weapon;
                            delete properties.shield;

                            properties.inventory.equippable = true;
                            return;
                        }

                        if (category === 'shield') {
                            properties.shield =
                                properties.shield ?? this.blankShield();

                            delete properties.weapon;
                            delete properties.armor;

                            properties.inventory.equippable = true;
                            return;
                        }

                        delete properties.weapon;
                        delete properties.armor;
                        delete properties.shield;
                    },

                    addModule(module) {
                        if (!this.form) return;

                        if (module === 'weapon' && !this.form.properties.weapon) {
                            this.form.properties.weapon = this.blankWeapon();
                        }

                        if (module === 'armor' && !this.form.properties.armor) {
                            this.form.properties.armor = this.blankArmor();
                            this.form.properties.inventory.equippable = true;
                        }

                        if (module === 'shield' && !this.form.properties.shield) {
                            this.form.properties.shield = this.blankShield();
                            this.form.properties.inventory.equippable = true;
                        }
                    },

                    removeModule(module) {
                        if (!this.form || !['weapon', 'armor', 'shield'].includes(module)) {
                            return;
                        }

                        delete this.form.properties[module];
                    },

                    traitSelected(key) {
                        return this.form?.properties?.weapon?.traits?.some(
                            trait => !trait.custom && trait.key === key
                        ) ?? false;
                    },

                    toggleTrait(definition) {
                        const traits = this.form.properties.weapon.traits;
                        const index = traits.findIndex(
                            trait => !trait.custom && trait.key === definition.key
                        );

                        if (index >= 0) {
                            traits.splice(index, 1);
                            return;
                        }

                        traits.push({
                            key: definition.key,
                            name: definition.name,
                            description: definition.description,
                            custom: false,
                        });

                        /*
                        | Se a propriedade exige medidas adicionais,
                        | inicializa a configuração sem forçar um valor
                        | específico para Distância.
                        */
                        if (
                            definition.key === 'range'
                            && !this.form.properties.weapon.range
                        ) {
                            this.form.properties.weapon.range =
                                this.blankWeapon().range;
                        }

                        if (
                            definition.key === 'reach'
                            && !this.form.properties.weapon.reach
                        ) {
                            this.form.properties.weapon.reach =
                                this.blankWeapon().reach;
                        }

                        if (
                            definition.key === 'loading'
                            && !this.form.properties.weapon.loading_capacity
                        ) {
                            this.form.properties.weapon.loading_capacity = null;
                        }
                    },

                    addCustomTrait() {
                        this.form.properties.weapon.traits.push({
                            key: `custom-trait-${Date.now()}`,
                            name: '',
                            description: '',
                            custom: true,
                        });
                    },

                    removeTrait(index) {
                        this.form.properties.weapon.traits.splice(index, 1);
                    },

                    masterySelected(key) {
                        return this.form?.properties?.weapon?.masteries?.some(
                            mastery => !mastery.custom && mastery.key === key
                        ) ?? false;
                    },

                    toggleMastery(definition) {
                        const masteries = this.form.properties.weapon.masteries;
                        const index = masteries.findIndex(
                            mastery => !mastery.custom && mastery.key === definition.key
                        );

                        if (index >= 0) {
                            masteries.splice(index, 1);
                            return;
                        }

                        masteries.push({
                            key: definition.key,
                            name: definition.name,
                            description: definition.description,
                            custom: false,
                        });
                    },

                    addCustomMastery() {
                        this.form.properties.weapon.masteries.push({
                            key: `custom-mastery-${Date.now()}`,
                            name: '',
                            description: '',
                            custom: true,
                        });
                    },

                    removeMastery(index) {
                        this.form.properties.weapon.masteries.splice(index, 1);
                    },

                    weaponHasTrait(weapon, key) {
                        return Array.isArray(weapon?.traits)
                            && weapon.traits.some(
                                trait =>
                                    String(trait?.key ?? '')
                                    === String(key ?? '')
                            );
                    },

                    weaponRuleCompactValue(trait, weapon) {
                        const key =
                            String(trait?.key ?? '');

                        if (key === 'range') {
                            return this.weaponRangeText(weapon);
                        }

                        if (key === 'reach') {
                            return this.weaponReachText(weapon);
                        }

                        if (key === 'loading') {
                            const capacity = this.nullableNumber(weapon?.loading_capacity, false);
                            return capacity && capacity > 0
                                ? `${capacity} ${capacity === 1 ? 'munição' : 'munições'}`
                                : '';
                        }

                        return '';
                    },

                    weaponRangeText(weapon) {
                        if (!weapon) {
                            return '';
                        }

                        const normal =
                            this.nullableNumber(
                                weapon.range_normal_ft,
                                true
                            );

                        const max =
                            this.nullableNumber(
                                weapon.range_long_ft,
                                true
                            );

                        if (
                            normal === null
                            && max === null
                        ) {
                            return '';
                        }

                        if (
                            normal !== null
                            && max !== null
                        ) {
                            return `${normal}/${max} Ft`;
                        }

                        if (normal !== null) {
                            return `${normal} Ft`;
                        }

                        return `máx. ${max} Ft`;
                    },

                    weaponReachText(weapon) {
                        if (!weapon) {
                            return '';
                        }

                        const reach =
                            this.nullableNumber(
                                weapon.reach_ft,
                                true
                            );

                        return reach === null
                            ? ''
                            : `${reach} Ft`;
                    },

                    damageTypeIsStandard(value) {
                        return this.damageTypeDefinitions.includes(
                            String(value ?? '').trim()
                        );
                    },

                    setDamageType(part, value) {
                        if (!part) return;

                        part.type = String(value ?? '').trim();
                        part.type_custom_mode = false;
                    },

                    setCustomDamageType(part) {
                        if (!part) return;

                        if (this.damageTypeIsStandard(part.type)) {
                            part.type = '';
                        }

                        part.type_custom_mode = true;
                    },

                    addExtraDamage() {
                        this.form.properties.weapon.extra_damage.push({
                            count: 1,
                            die: 'd6',
                            type: 'Elétrico',
                            type_custom_mode: false,
                        });
                    },

                    removeExtraDamage(index) {
                        this.form.properties.weapon.extra_damage.splice(index, 1);
                    },

                    setArmorCategory(category) {
                        const armor = this.form?.properties?.armor;
                        if (!armor) return;

                        armor.category = category;

                        const defaults = this.armorDexterityDefaults(category);
                        armor.dexterity_mode = defaults.mode;
                        armor.dexterity_cap = defaults.cap;
                    },

                    setArmorDexterityMode(mode) {
                        if (
                            !['none', 'full', 'capped'].includes(mode)
                            || !this.form?.properties?.armor
                        ) {
                            return;
                        }

                        this.form.properties.armor.dexterity_mode = mode;
                    },

                    toggleArmorAbilityModifier(ability) {
                        const armor = this.form?.properties?.armor;
                        if (!armor) return;

                        const current = Array.isArray(armor.ability_modifiers)
                            ? [...armor.ability_modifiers]
                            : [];

                        if (current.includes(ability)) {
                            armor.ability_modifiers = current.filter(
                                entry => entry !== ability
                            );
                            return;
                        }

                        armor.ability_modifiers = [...current, ability];
                    },

                    abilityShortLabel(ability) {
                        return {
                            strength: 'FOR',
                            constitution: 'CON',
                            intelligence: 'INT',
                            wisdom: 'SAB',
                            charisma: 'CAR',
                        }[ability] ?? String(ability ?? '').toUpperCase();
                    },

                    armorFormulaText(armor) {
                        if (!armor) return '';

                        const parts = [
                            `CA ${Math.max(0, parseInt(armor.base_ac) || 0)}`,
                        ];

                        if (armor.dexterity_mode === 'full') {
                            parts.push('+ DES');
                        }

                        if (armor.dexterity_mode === 'capped') {
                            parts.push(
                                '+ DES (máx. +'
                                + Math.max(0, parseInt(armor.dexterity_cap) || 0)
                                + ')'
                            );
                        }

                        (armor.ability_modifiers ?? []).forEach(
                            ability => parts.push('+ ' + this.abilityShortLabel(ability))
                        );

                        const magic = parseInt(armor.magic_bonus) || 0;

                        if (magic !== 0) {
                            parts.push(
                                (magic > 0 ? '+' : '')
                                + magic
                                + ' mágico'
                            );
                        }

                        return parts.join(' ');
                    },

                    shieldTotalBonus(shield) {
                        if (!shield) return 0;

                        return (
                            (parseInt(shield.ac_bonus) || 0)
                            + (parseInt(shield.magic_bonus) || 0)
                        );
                    },

                    signedNumber(value) {
                        const parsed = parseInt(value) || 0;
                        return parsed > 0 ? `+${parsed}` : String(parsed);
                    },

                    weaponBaseDamageDice(weapon) {
                        if (!weapon?.damage) return '';

                        const count =
                            Math.max(
                                1,
                                parseInt(weapon.damage.count) || 1
                            );

                        let die =
                            String(
                                weapon.damage.die
                                ?? ''
                            ).trim();

                        if (!die) return '';

                        if (!die.toLowerCase().startsWith('d')) {
                            die = `d${die}`;
                        }

                        return `${count}${die}`;
                    },

                    weaponDamagePreview(weapon) {
                        if (!weapon?.damage) return '';

                        const baseCount =
                            Math.max(
                                1,
                                parseInt(weapon.damage.count) || 1
                            );

                        const baseDie =
                            String(
                                weapon.damage.die
                                ?? ''
                            ).trim();

                        const baseType =
                            String(
                                weapon.damage.type
                                ?? ''
                            ).trim();

                        const magic =
                            parseInt(
                                weapon.magic_bonus
                            ) || 0;

                        let base =
                            baseDie
                                ? `${baseCount}${baseDie}`
                                : '';

                        if (
                            base
                            && magic !== 0
                        ) {
                            base +=
                                magic > 0
                                    ? `+${magic}`
                                    : `${magic}`;
                        }

                        if (baseType) {
                            base +=
                                `${base ? ' ' : ''}${baseType}`;
                        }

                        const extras =
                            (
                                weapon.extra_damage
                                ?? []
                            )
                                .map(
                                    part => {
                                        const count =
                                            Math.max(
                                                1,
                                                parseInt(part?.count) || 1
                                            );

                                        const die =
                                            String(
                                                part?.die
                                                ?? ''
                                            ).trim();

                                        const type =
                                            String(
                                                part?.type
                                                ?? ''
                                            ).trim();

                                        return [
                                            die
                                                ? `${count}${die}`
                                                : '',
                                            type,
                                        ]
                                            .filter(Boolean)
                                            .join(' ');
                                    }
                                )
                                .filter(Boolean);

                        return [
                            base,
                            ...extras,
                        ]
                            .filter(Boolean)
                            .join(' + ');
                    },

                    addCustomProperty() {
                        this.form.properties.custom_properties.push({
                            title: '',
                            value: '',
                            description: '',
                        });
                    },

                    removeCustomProperty(index) {
                        this.form.properties.custom_properties.splice(index, 1);
                    },

                    addFeature() {
                        const features =
                            this.form?.properties?.features;

                        if (!Array.isArray(features)) {
                            return;
                        }

                        features.forEach(
                            feature => {
                                feature.editor_open = false;
                            }
                        );

                        features.push(
                            this.blankFeature()
                        );
                    },

                    toggleFeatureDrawer(feature) {
                        if (!feature) return;

                        feature.editor_open =
                            !feature.editor_open;
                    },

                    moveFeature(index, direction) {
                        const features =
                            this.form?.properties?.features;

                        if (
                            !Array.isArray(features)
                            || ![-1, 1].includes(direction)
                        ) {
                            return;
                        }

                        const target =
                            index + direction;

                        if (
                            target < 0
                            || target >= features.length
                        ) {
                            return;
                        }

                        const [moved] =
                            features.splice(index, 1);

                        features.splice(
                            target,
                            0,
                            moved
                        );
                    },

                    removeFeature(index) {
                        this.form.properties.features.splice(index, 1);
                    },

                    setFeatureUsage(feature, enabled) {
                        feature.usage = this.normalizeFeatureUsage({
                            ...feature.usage,
                            enabled: !!enabled,
                        });

                        if (enabled) {
                            feature.usage.current =
                                feature.usage.counter_mode === 'build'
                                    ? 0
                                    : feature.usage.max;
                        }
                    },

                    setFeatureCounterMode(feature, mode) {
                        if (!['spend', 'build'].includes(mode)) return;

                        feature.usage.counter_mode = mode;
                        feature.usage.max = Math.max(
                            1,
                            parseInt(feature.usage.max) || 1
                        );

                        feature.usage.current =
                            mode === 'build'
                                ? 0
                                : feature.usage.max;
                    },

                    clampFeatureUsage(feature) {
                        feature.usage.max = Math.max(
                            1,
                            parseInt(feature.usage.max) || 1
                        );

                        feature.usage.current = Math.max(
                            0,
                            Math.min(
                                feature.usage.max,
                                parseInt(feature.usage.current) || 0
                            )
                        );
                    },

                    handleFeatureRecoveryChange(feature) {
                        if (!feature?.usage) return;

                        if (feature.usage.recovery === 'single_use') {
                            /*
                            | Uso Único = cargas permanentes, não "sempre 1/1".
                            | O Mestre define quantas cargas o item possui.
                            */
                            const wasBuild =
                                feature.usage.counter_mode
                                === 'build';

                            feature.usage.counter_mode =
                                'spend';

                            feature.usage.max =
                                Math.max(
                                    1,
                                    parseInt(
                                        feature.usage.max
                                    ) || 1
                                );

                            if (wasBuild) {
                                feature.usage.current =
                                    feature.usage.max;
                            }

                            feature.usage.recovery_custom =
                                '';

                            this.clampFeatureUsage(feature);
                            return;
                        }

                        this.clampFeatureUsage(feature);
                    },

                    cleanProperties() {
                        const properties = this.normalizeProperties(
                            this.form.properties,
                            this.form
                        );

                        properties.custom_properties = properties.custom_properties
                            .map(entry => ({
                                title: String(entry.title ?? '').trim(),
                                value: String(entry.value ?? '').trim(),
                                description: String(entry.description ?? '').trim(),
                            }))
                            .filter(entry => entry.title || entry.value || entry.description);

                        properties.features = properties.features
                            .map(feature => {
                                const usage =
                                    this.normalizeFeatureUsage(
                                        feature.usage
                                    );

                                return {
                                    title:
                                        String(
                                            feature.title
                                            ?? ''
                                        ).trim(),

                                    description:
                                        String(
                                            feature.description
                                            ?? ''
                                        ).trim(),

                                    /*
                                    | Passiva não possui contador. Mantemos
                                    | somente o booleano e não carregamos
                                    | 1/1 / Dia como se fosse uma regra real.
                                    */
                                    usage:
                                        usage.enabled
                                            ? usage
                                            : {
                                                enabled: false,
                                            },
                                };
                            })
                            .filter(
                                feature =>
                                    feature.title
                                    || feature.description
                            );

                        if (properties.weapon) {
                            if (properties.weapon.damage) {
                                delete properties.weapon.damage.type_custom_mode;
                            }

                            properties.weapon.traits = properties.weapon.traits
                                .map(trait => ({
                                    key: String(trait.key ?? '').trim(),
                                    name: String(trait.name ?? '').trim(),
                                    description: String(trait.description ?? '').trim(),
                                    custom: !!trait.custom,
                                }))
                                .filter(trait => trait.name);

                            properties.weapon.masteries = properties.weapon.masteries
                                .map(mastery => ({
                                    key: String(mastery.key ?? '').trim(),
                                    name: String(mastery.name ?? '').trim(),
                                    description: String(mastery.description ?? '').trim(),
                                    custom: !!mastery.custom,
                                }))
                                .filter(mastery => mastery.name);

                            properties.weapon.extra_damage = properties.weapon.extra_damage
                                .map(part => ({
                                    count: Math.max(1, parseInt(part.count) || 1),
                                    die: String(part.die ?? '').trim(),
                                    type: String(part.type ?? '').trim(),
                                }))
                                .filter(part => part.die || part.type);

                            const hasRange =
                                properties.weapon.traits.some(
                                    trait =>
                                        !trait.custom
                                        && trait.key === 'range'
                                );

                            const hasReach =
                                properties.weapon.traits.some(
                                    trait =>
                                        !trait.custom
                                        && trait.key === 'reach'
                                );

                            const hasLoading =
                                properties.weapon.traits.some(
                                    trait =>
                                        !trait.custom
                                        && trait.key === 'loading'
                                );

                            if (hasRange) {
                                properties.weapon.range_normal_ft =
                                    this.nullableNumber(
                                        properties.weapon.range_normal_ft,
                                        true
                                    );

                                properties.weapon.range_long_ft =
                                    this.nullableNumber(
                                        properties.weapon.range_long_ft,
                                        true
                                    );
                            } else {
                                delete properties.weapon.range_normal_ft;
                                delete properties.weapon.range_long_ft;
                            }

                            if (hasReach) {
                                properties.weapon.reach_ft =
                                    this.nullableNumber(
                                        properties.weapon.reach_ft,
                                        true
                                    ) ?? 10;
                            } else {
                                delete properties.weapon.reach_ft;
                            }

                            if (hasLoading) {
                                const capacity = this.nullableNumber(
                                    properties.weapon.loading_capacity,
                                    false
                                );

                                properties.weapon.loading_capacity =
                                    capacity !== null
                                        ? Math.max(1, Math.min(999999, capacity))
                                        : null;
                            } else {
                                delete properties.weapon.loading_capacity;
                            }

                            /*
                            | Garante que o formato temporário da V10.5 nunca
                            | seja persistido novamente.
                            */
                            delete properties.weapon.range;
                            delete properties.weapon.reach;
                        }

                        return properties;
                    },

                    deriveLegacyFields(properties) {
                        const result = {
                            armor_class: null,
                            damage: null,
                            attack_bonus: null,
                            damage_bonus: null,
                        };

                        const weapon = properties?.weapon;

                        if (weapon) {
                            const magic = parseInt(weapon.magic_bonus) || 0;
                            const count = Math.max(
                                1,
                                parseInt(weapon?.damage?.count) || 1
                            );
                            const die = String(
                                weapon?.damage?.die ?? ''
                            ).trim();

                            result.damage = die
                                ? `${count}${die}`
                                : null;

                            result.attack_bonus = magic !== 0
                                ? magic
                                : null;

                            result.damage_bonus = magic !== 0
                                ? magic
                                : null;
                        }

                        const armor = properties?.armor;

                        if (armor) {
                            const base = this.nullableNumber(armor.base_ac);
                            const magic = parseInt(armor.magic_bonus) || 0;

                            result.armor_class = base !== null
                                ? base + magic
                                : null;
                        }

                        return result;
                    },

                    handleImage(event) {
                        const file = event?.target?.files?.[0];
                        if (!file) return;

                        if (!String(file.type ?? '').startsWith('image/')) {
                            this.showToast('Selecione um arquivo de imagem.', true);
                            event.target.value = '';
                            return;
                        }

                        this.imageFile = file;
                        this.form.remove_image = false;
                        this.clearImagePreview();
                        this.imagePreview = URL.createObjectURL(file);
                    },

                    removeImage() {
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.form.remove_image = true;
                        this.form.image_url = null;
                    },

                    clearImagePreview() {
                        if (this.imagePreview) {
                            URL.revokeObjectURL(this.imagePreview);
                            this.imagePreview = null;
                        }
                    },

                    parsedTags() {
                        return String(this.tagsText ?? '')
                            .split(',')
                            .map(tag => tag.trim())
                            .filter(Boolean)
                            .filter((tag, index, list) => list.indexOf(tag) === index)
                            .slice(0, 30);
                    },

                    nullableNumber(value, decimal = false) {
                        if (value === null || value === '' || typeof value === 'undefined') return null;
                        const parsed = decimal ? parseFloat(value) : parseInt(value);
                        return Number.isNaN(parsed) ? null : parsed;
                    },

                    buildPayload() {
                        const properties = this.cleanProperties();
                        properties.nature = this.form.type;

                        const legacy = this.deriveLegacyFields(properties);
                        const wonderful = properties.nature === 'wonderful';

                        return {
                            name: String(this.form.name ?? '').trim(),
                            type: properties.nature,
                            description:
                                String(this.form.description ?? '').trim()
                                || null,
                            default_quantity: Math.max(
                                1,
                                parseInt(this.form.default_quantity) || 1
                            ),
                            weight: this.nullableNumber(
                                this.form.weight,
                                true
                            ),
                            is_magical: wonderful,
                            rarity: wonderful
                                ? (this.form.rarity || 'common')
                                : null,
                            requires_attunement: wonderful
                                ? !!this.form.requires_attunement
                                : false,
                            is_cursed: !!this.form.is_cursed,
                            curse_name: this.form.is_cursed
                                ? (String(this.form.curse_name ?? '').trim() || null)
                                : null,
                            curse_description: this.form.is_cursed
                                ? (this.form.curse_description ?? '')
                                : null,
                            armor_class: legacy.armor_class,
                            damage: legacy.damage,
                            attack_bonus: legacy.attack_bonus,
                            damage_bonus: legacy.damage_bonus,
                            ability_bonuses: this.form.ability_bonuses ?? {},
                            properties,
                            modifiers: this.form.modifiers ?? {},
                            notes: this.form.notes ?? '',
                            tags: this.parsedTags(),
                            favorite: !!this.form.favorite,
                        };
                    },

                    appendFormValue(formData, key, value) {
                        if (Array.isArray(value)) {
                            value.forEach((entry, index) => this.appendFormValue(formData, `${key}[${index}]`, entry));
                            return;
                        }

                        if (value && typeof value === 'object' && !(value instanceof File)) {
                            Object.entries(value).forEach(([childKey, childValue]) => {
                                this.appendFormValue(formData, `${key}[${childKey}]`, childValue);
                            });
                            return;
                        }

                        if (typeof value === 'boolean') {
                            formData.append(key, value ? '1' : '0');
                            return;
                        }

                        formData.append(key, value === null ? '' : String(value));
                    },

                    buildFormData(payload) {
                        const formData = new FormData();
                        Object.entries(payload).forEach(([key, value]) => this.appendFormValue(formData, key, value));
                        if (this.imageFile) formData.append('image', this.imageFile);
                        if (this.form?.remove_image) formData.append('remove_image', '1');
                        return formData;
                    },

                    headers() {
                        return {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                            'X-Requested-With': 'XMLHttpRequest',
                        };
                    },

                    async saveItem() {
                        if (this.saving || !this.form) return;

                        const payload = this.buildPayload();
                        if (!payload.name) {
                            this.saveError = 'Informe um nome para o item.';
                            this.editorTab = 'general';
                            return;
                        }

                        this.saving = true;
                        this.saveError = null;
                        const editing = this.editingId !== null;
                        const url = editing
                            ? this.urls.update.replace('__ITEM__', this.editingId)
                            : this.urls.store;

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: this.headers(),
                                body: this.buildFormData(payload),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) {
                                const errors = data?.errors ? Object.values(data.errors).flat().filter(Boolean) : [];
                                throw new Error(errors.length ? errors.join(' ') : (data?.message ?? 'Não foi possível salvar o item.'));
                            }

                            const savedItem = this.upsertItem(data.item);

                            if (this.folderSystemReady) {
                                const targetFolderId = this.formFolderId
                                    ? parseInt(this.formFolderId)
                                    : null;
                                const currentFolderId = savedItem.folder_id
                                    ? parseInt(savedItem.folder_id)
                                    : null;

                                if (targetFolderId !== currentFolderId) {
                                    await this.moveVaultItem(savedItem.id, targetFolderId, true);
                                }
                            }

                            this.saving = false;
                            this.closeEditor();
                            this.showToast(editing ? 'Item atualizado no Vault.' : 'Item criado no Vault.');
                        } catch (error) {
                            this.saveError = error?.message ?? 'Não foi possível salvar o item.';
                            this.saving = false;
                        }
                    },

                    payloadForExisting(item) {
                        const normalized = this.normalizeItem(item);
                        const properties = this.normalizeProperties(
                            normalized.properties,
                            normalized
                        );

                        const legacy = this.deriveLegacyFields(properties);
                        const wonderful = properties.nature === 'wonderful';

                        return {
                            name: normalized.name,
                            type: properties.nature,
                            description: normalized.description || null,
                            default_quantity: normalized.default_quantity,
                            weight: normalized.weight,
                            is_magical: wonderful,
                            rarity: wonderful
                                ? (normalized.rarity || 'common')
                                : null,
                            requires_attunement: wonderful
                                ? !!normalized.requires_attunement
                                : false,
                            is_cursed: !!normalized.is_cursed,
                            curse_name: normalized.is_cursed
                                ? (String(normalized.curse_name ?? '').trim() || null)
                                : null,
                            curse_description: normalized.is_cursed
                                ? normalized.curse_description
                                : null,
                            armor_class: legacy.armor_class,
                            damage: legacy.damage,
                            attack_bonus: legacy.attack_bonus,
                            damage_bonus: legacy.damage_bonus,
                            ability_bonuses: normalized.ability_bonuses ?? {},
                            properties,
                            modifiers: normalized.modifiers ?? {},
                            notes: normalized.notes ?? '',
                            tags: normalized.tags ?? [],
                            favorite: !!normalized.favorite,
                        };
                    },

                    async toggleFavorite(item) {
                        if (this.busyId !== null) return;
                        this.busyId = item.id;
                        const previous = !!item.favorite;
                        item.favorite = !previous;

                        try {
                            const payload = this.payloadForExisting(item);
                            const response = await fetch(this.urls.update.replace('__ITEM__', item.id), {
                                method: 'POST',
                                headers: this.headers(),
                                body: this.buildStandaloneFormData(payload),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível atualizar o favorito.');
                            this.upsertItem(data.item);
                        } catch (error) {
                            item.favorite = previous;
                            this.showToast(error?.message ?? 'Não foi possível atualizar o favorito.', true);
                        } finally {
                            this.busyId = null;
                        }
                    },

                    buildStandaloneFormData(payload) {
                        const formData = new FormData();
                        Object.entries(payload).forEach(([key, value]) => this.appendFormValue(formData, key, value));
                        return formData;
                    },

                    async duplicateItem(item) {
                        if (this.busyId !== null) return;
                        this.busyId = item.id;

                        try {
                            const response = await fetch(this.urls.duplicate.replace('__ITEM__', item.id), {
                                method: 'POST',
                                headers: this.headers(),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível duplicar o item.');
                            this.upsertItem(data.item);
                            this.showToast('Cópia criada no Vault.');
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível duplicar o item.', true);
                        } finally {
                            this.busyId = null;
                        }
                    },

                    async deleteVaultItem(item) {
                        if (
                            this.busyId !== null
                            || !item?.id
                        ) {
                            return false;
                        }

                        this.busyId =
                            item.id;

                        try {
                            const response =
                                await fetch(
                                    this.urls.destroy.replace(
                                        '__ITEM__',
                                        item.id
                                    ),
                                    {
                                        method: 'DELETE',
                                        headers: this.headers(),
                                    }
                                );

                            const data =
                                await response
                                    .json()
                                    .catch(
                                        () => ({})
                                    );

                            if (!response.ok) {
                                throw new Error(
                                    data?.message
                                    ?? 'Não foi possível excluir o item.'
                                );
                            }

                            const id =
                                parseInt(item.id);

                            this.items =
                                this.items.filter(
                                    current =>
                                        parseInt(current.id)
                                        !== id
                                );

                            if (
                                this.previewItem
                                && parseInt(this.previewItem.id)
                                    === id
                            ) {
                                this.closePreview();
                            }

                            if (
                                parseInt(this.editingId)
                                === id
                            ) {
                                this.closeEditor();
                            }

                            this.showToast(
                                'Item removido do Vault.'
                            );

                            return true;
                        } catch (error) {
                            this.showToast(
                                error?.message
                                ?? 'Não foi possível excluir o item.',
                                true
                            );

                            return false;
                        } finally {
                            this.busyId =
                                null;
                        }
                    },

                    async deleteItem() {
                        if (!this.editingId || this.saving) return;
                        if (!window.confirm('Remover este item do Vault? Espólios que já receberam um snapshot não serão alterados.')) return;

                        this.saving = true;

                        try {
                            const response = await fetch(this.urls.destroy.replace('__ITEM__', this.editingId), {
                                method: 'DELETE',
                                headers: this.headers(),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível excluir o item.');

                            const id = parseInt(this.editingId);
                            this.items = this.items.filter(item => parseInt(item.id) !== id);
                            this.saving = false;
                            this.closeEditor();
                            this.showToast('Item removido do Vault.');
                        } catch (error) {
                            this.saveError = error?.message ?? 'Não foi possível excluir o item.';
                            this.saving = false;
                        }
                    },

                    upsertItem(raw) {
                        const item = this.normalizeItem(raw);
                        const index = this.items.findIndex(current => parseInt(current.id) === parseInt(item.id));

                        if (index >= 0) {
                            if (raw?.folder_id === undefined || raw?.folder_id === null) {
                                item.folder_id = this.items[index].folder_id ?? null;
                            }
                            this.items.splice(index, 1, item);
                        } else {
                            this.items.push(item);
                        }

                        return item;
                    },

                    showToast(message, error = false) {
                        this.toastMessage = message;
                        this.toastError = !!error;
                        this.toastOpen = true;
                        clearTimeout(this.toastTimer);
                        this.toastTimer = setTimeout(() => this.toastOpen = false, 3600);
                    },
        });
    </script>
    @endonce

    <div
        class="master-vault-page"
        x-data="masterVaultPage(
            @js($vaultItems),
            @js($vaultFolders ?? []),
            @js($weaponTraitDefinitions),
            @js($masteryDefinitions),
            @js($abilityLabels),
            @js($recoveryLabels),
            @js($damageTypeDefinitions),
            {
                store: @js(route('master.vault.items.store')),
                update: @js(route('master.vault.items.update', ['vaultItem' => '__ITEM__'])),
                duplicate: @js(route('master.vault.items.duplicate', ['vaultItem' => '__ITEM__'])),
                destroy: @js(route('master.vault.items.destroy', ['vaultItem' => '__ITEM__'])),
                folderStore: @js(route('workspace-folders.store')),
                folderUpdate: @js(route('workspace-folders.update', ['workspaceFolder' => '__FOLDER__'])),
                folderDestroy: @js(route('workspace-folders.destroy', ['workspaceFolder' => '__FOLDER__'])),
                moveFolder: @js(route('workspace-folder-items.move')),
            },
            {
                ready: @js($vaultFolderSystemReady ?? false),
                scopeId: @js($vaultScopeId ?? auth()->id()),
            }
        )"
        x-init="
            if (typeof openCreate !== 'function') {
                console.error('[Vault] Alpine não inicializou masterVaultPage corretamente.');
            }
        "
        @keydown.escape.window="folderModalOpen ? closeFolderModal() : (actionMenuId ? closeActionMenu() : (confirmOpen ? closeConfirm() : (previewOpen ? closePreview() : (editorOpen ? closeEditor() : null))))"
    >
        <div class="master-vault-v9-shell">
            <header class="master-vault-v9-commandbar master-vault-v14-topbar">
                <div class="master-vault-v14-context">
                    <template x-if="!activeFolder">
                        <div class="master-vault-v14-context-home">
                            <span class="master-vault-v12-brand-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M5 5.5h14v13H5z" stroke-width="1.7" stroke-linejoin="round"/>
                                    <path d="M8 3.5h8M8 8.5h8M8 12h8M8 15.5h5" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </span>

                            <div class="master-vault-v12-brand-copy">
                                <h1>Cofre de Itens</h1>
                                <p>Gerencie sua biblioteca de itens</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="activeFolder">
                        <div class="master-vault-v14-context-folder">
                            <button
                                type="button"
                                class="master-vault-v14-back"
                                @click="closeFolder()"
                                title="Voltar ao Cofre"
                                aria-label="Voltar ao Cofre"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M15 18l-6-6 6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <span
                                class="master-vault-v14-folder-mark"
                                :style="folderIconStyle(activeFolder)"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <div class="master-vault-v14-folder-copy">
                                <h1 x-text="activeFolder?.name"></h1>
                                <p x-text="activeFolder?.description || 'Itens organizados nesta pasta.'"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <label class="master-vault-v9-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="6.5" stroke-width="1.7"/>
                        <path d="m16 16 4 4" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>

                    <input
                        type="search"
                        x-model.debounce.180ms="search"
                        :placeholder="activeFolder ? 'Buscar nesta pasta...' : 'Buscar item por nome, descrição ou propriedade...'"
                    >
                </label>

                <label class="master-vault-v9-filter">
                    <select x-model="filter" aria-label="Filtrar itens do Cofre">
                        <template x-for="option in filterOptions" :key="option.key">
                            <option :value="option.key" x-text="option.label"></option>
                        </template>
                    </select>
                </label>

                <div class="master-vault-v11-actions">
                    <button
                        x-show="!activeFolder && folderSystemReady"
                        x-cloak
                        type="button"
                        class="master-vault-v11-folder-button"
                        @click="openFolderCreate()"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4">
                            <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="M12 12v4M10 14h4" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        Pasta
                    </button>

                    <button
                        x-show="activeFolder"
                        x-cloak
                        type="button"
                        class="master-vault-v11-folder-button"
                        @click="openFolderEdit(activeFolder)"
                    >
                        Editar pasta
                    </button>

                    <button type="button" class="master-vault-v9-create" @click="openCreate()">
                        <span class="text-base leading-none">+</span>
                        Novo Item
                    </button>
                </div>
            </header>

            <section
                x-show="showFolderOverview && folderSystemReady"
                x-cloak
                class="master-vault-v11-folder-section"
            >
                <div class="master-vault-v11-folder-head">
                    <div class="master-vault-v11-folder-headline">
                        <span class="master-vault-v11-folder-headicon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </span>

                        <div>
                            <h2>Pastas do Cofre</h2>
                            <p>Arraste seus itens para organizá-los em pastas.</p>
                        </div>
                    </div>

                    <span class="master-vault-v11-folder-badge" x-text="folders.length + ' ' + (folders.length === 1 ? 'Pasta' : 'Pastas')"></span>
                </div>

                <div class="master-vault-v11-folder-grid">
                    <template x-for="folder in folders" :key="'vault-folder-' + folder.id">
                        <article
                            class="master-vault-v11-folder-card"
                            :style="folderCardStyle(folder)"
                            @click="openFolder(folder)"
                            @dragover.prevent
                            @drop.prevent.stop="dropItemIntoFolder(folder)"
                        >
                            <div>
                                <div class="master-vault-v11-folder-top">
                                    <div class="master-vault-v11-folder-title">
                                        <span class="master-vault-v11-folder-icon" :style="folderIconStyle(folder)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                                            </svg>
                                        </span>

                                        <div class="min-w-0">
                                            <h3 x-text="folder.name"></h3>
                                        </div>
                                    </div>

                                    <div class="master-vault-v11-folder-menu-wrap" @click.stop>
                                        <button type="button" class="master-vault-v11-folder-menu-button" @click="toggleFolderMenu(folder.id)">⋮</button>

                                        <div
                                            x-show="folderMenuId === folder.id"
                                            x-cloak
                                            x-transition.opacity.duration.100ms
                                            class="master-vault-v11-folder-menu-panel"
                                            @click.outside="closeFolderMenu()"
                                        >
                                            <button type="button" @click="openFolder(folder)">Abrir pasta</button>
                                            <button type="button" @click="openFolderEdit(folder)">Editar pasta</button>
                                            <button type="button" class="is-danger" @click="deleteFolder(folder)">Excluir pasta</button>
                                        </div>
                                    </div>
                                </div>

                                <p class="master-vault-v11-folder-description" x-text="folder.description || ''"></p>
                            </div>

                            <footer class="master-vault-v11-folder-foot">
                                <div class="master-vault-v11-folder-preview">
                                    <template x-if="folderPreviewItems(folder.id).length === 0">
                                        <span class="master-vault-v11-folder-avatar is-empty">Nenhum item ainda</span>
                                    </template>

                                    <template x-for="item in folderPreviewItems(folder.id)" :key="'folder-preview-' + folder.id + '-' + item.id">
                                        <span class="master-vault-v11-folder-avatar" :class="{ 'is-text': !item.image_url }">
                                            <template x-if="item.image_url">
                                                <img :src="item.image_url" :alt="item.name">
                                            </template>
                                            <template x-if="!item.image_url">
                                                <span x-text="String(item.name ?? '?').trim().charAt(0).toUpperCase() || '?' "></span>
                                            </template>
                                        </span>
                                    </template>
                                </div>

                                <span class="master-vault-v11-folder-count" x-text="folderItemBadge(folder.id)"></span>
                            </footer>
                        </article>
                    </template>
                </div>
            </section>

            <div
                x-show="showItemResultsSection"
                x-cloak
                class="master-vault-v9-results"
                :class="{ 'is-inside-folder': activeFolder }"
            >
                <div>
                    <strong x-text="resultHeading"></strong>

                    <span
                        x-text="
                            filteredItems.length
                            + (
                                filteredItems.length === 1
                                    ? ' resultado'
                                    : ' resultados'
                            )
                        "
                    ></span>
                </div>

                <button
                    x-show="filter !== 'all' || search"
                    x-cloak
                    type="button"
                    class="master-vault-v9-clear"
                    @click="
                        filter = 'all';
                        search = '';
                    "
                >
                    Limpar filtros
                </button>
            </div>

            <div
                x-show="showItemResultsSection && filteredItems.length > 0"
                class="master-vault-v9-grid"
            >
                <template
                    x-for="item in filteredItems"
                    :key="item.id"
                >
                    <article
                        class="master-vault-v10-card"
                        :data-nature="item.type"
                        draggable="true"
                        @dragstart.stop="startItemDrag(item, $event)"
                        @dragend="draggedItemId = null"
                        tabindex="0"
                        role="button"
                        :aria-label="'Visualizar ' + item.name"
                        @click="openPreview(item)"
                        @keydown.enter.prevent="openPreview(item)"
                    >
                        <div class="master-vault-v10-card-main">
                            <div class="master-vault-v10-thumb">
                                <template x-if="item.image_url">
                                    <img
                                        :src="item.image_url"
                                        :alt="item.name"
                                    >
                                </template>

                                <template x-if="!item.image_url">
                                    <svg
                                        class="h-8 w-8"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M5 6.5h14v11H5z"
                                            stroke-width="1.5"
                                        />
                                        <path
                                            d="m7.5 15 3.2-3.2 2.3 2.3 1.7-1.7 2.8 2.6"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </template>
                            </div>

                            <div class="master-vault-v10-identity">
                                <h2
                                    class="master-vault-v10-name"
                                    x-text="item.name"
                                ></h2>

                                <p
                                    class="master-vault-v10-nature"
                                    x-text="cardNatureLine(item)"
                                ></p>

                                <p
                                    x-show="cardCategoryLine(item)"
                                    x-cloak
                                    class="master-vault-v10-category"
                                    x-text="cardCategoryLine(item)"
                                ></p>
                            </div>

                            <div
                                class="master-vault-v10-top-actions"
                                @click.stop
                            >
                                <button
                                    type="button"
                                    class="master-vault-v10-top-button"
                                    :class="{ 'is-active': item.favorite }"
                                    @click="toggleFavorite(item)"
                                    :disabled="busyId === item.id"
                                    :title="
                                        item.favorite
                                            ? 'Remover dos favoritos'
                                            : 'Adicionar aos favoritos'
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        :fill="item.favorite ? 'currentColor' : 'none'"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"
                                            stroke-width="1.45"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="master-vault-v10-menu-wrap"
                                    @click.outside="
                                        actionMenuId === parseInt(item.id)
                                            ? closeActionMenu()
                                            : null
                                    "
                                >
                                    <button
                                        type="button"
                                        class="master-vault-v10-top-button"
                                        @click="toggleActionMenu(item)"
                                        :aria-expanded="
                                            actionMenuId === parseInt(item.id)
                                        "
                                        title="Ações do item"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                        >
                                            <circle cx="5" cy="12" r="1.6"/>
                                            <circle cx="12" cy="12" r="1.6"/>
                                            <circle cx="19" cy="12" r="1.6"/>
                                        </svg>
                                    </button>

                                    <div
                                        x-show="
                                            actionMenuId === parseInt(item.id)
                                        "
                                        x-cloak
                                        x-transition.opacity.duration.120ms
                                        class="master-vault-v10-menu"
                                    >
                                        <button
                                            type="button"
                                            @click="editFromMenu(item)"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    d="m4 20 4.2-1 9.9-9.9-3.2-3.2L5 15.8 4 20ZM13.8 7l3.2 3.2"
                                                    stroke-width="1.6"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>

                                            Editar
                                        </button>

                                        <button
                                            type="button"
                                            @click="requestDuplicate(item)"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <rect
                                                    x="8"
                                                    y="8"
                                                    width="10"
                                                    height="10"
                                                    rx="2"
                                                    stroke-width="1.6"
                                                />
                                                <path
                                                    d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"
                                                    stroke-width="1.6"
                                                    stroke-linecap="round"
                                                />
                                            </svg>

                                            Criar cópia
                                        </button>

                                        <button
                                            type="button"
                                            class="is-danger"
                                            @click="requestDelete(item)"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    d="M5 7h14M9 7V4h6v3M8 10v8M12 10v8M16 10v8M7 7l1 14h8l1-14"
                                                    stroke-width="1.55"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>

                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <section
                            x-show="item.description"
                            class="master-vault-v10-section"
                        >
                            <p
                                class="master-vault-v10-description"
                                x-text="item.description"
                            ></p>
                        </section>

                        <section
                            x-show="cardFeatures(item).length > 0"
                            class="master-vault-v10-section"
                        >
                            <div class="master-vault-v10-abilities">
                                <template
                                    x-for="
                                        (feature, featureIndex) in
                                        cardFeatures(item).slice(0, 3)
                                    "
                                    :key="
                                        'card-feature-'
                                        + item.id
                                        + '-'
                                        + featureIndex
                                    "
                                >
                                    <span
                                        class="master-vault-v10-ability"
                                        x-text="
                                            feature.title
                                            || ('Habilidade ' + (featureIndex + 1))
                                        "
                                    ></span>
                                </template>

                                <span
                                    x-show="cardFeatures(item).length > 3"
                                    class="master-vault-v10-ability master-vault-v10-ability-more"
                                    x-text="
                                        '+'
                                        + (
                                            cardFeatures(item).length - 3
                                        )
                                    "
                                ></span>
                            </div>

                        </section>

                    </article>
                </template>
            </div>

            <section
                x-show="showItemResultsSection && filteredItems.length === 0"
                x-cloak
                class="master-vault-v8-empty"
            >
                <span class="master-vault-v8-empty-mark">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            d="M5 7h14v12H5zM8 4h8v3"
                            stroke-width="1.6"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M9 12h6"
                            stroke-width="1.6"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <h3
                    x-text="
                        items.length
                            ? 'Nenhum item encontrado'
                            : 'Seu Vault está vazio'
                    "
                ></h3>

                <p
                    x-text="
                        items.length
                            ? 'Ajuste a busca ou o filtro para encontrar outros itens.'
                            : 'Crie o primeiro item da sua biblioteca permanente do Mestre.'
                    "
                ></p>

                <button
                    x-show="items.length === 0"
                    type="button"
                    class="master-vault-v9-create mt-5"
                    @click="openCreate()"
                >
                    + Criar primeiro item
                </button>
            </section>
        </div>

        {{-- =========================================================
             CONFIRMAÇÃO DE AÇÃO
        ========================================================== --}}
        <template x-teleport="body">
            <div
                x-show="confirmOpen && confirmItem"
                x-cloak
                class="master-vault-v10-confirm-layer"
                x-transition.opacity.duration.120ms
                role="dialog"
                aria-modal="true"
                aria-label="Confirmar ação do Vault"
            >
                <div
                    class="master-vault-v10-confirm-backdrop"
                    @click="closeConfirm()"
                ></div>

                <section
                    class="master-vault-v10-confirm"
                    x-transition:enter="transition ease-out duration-160"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-[.985]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                >
                    <div class="master-vault-v10-confirm-body">
                        <span class="master-vault-v10-confirm-mark">
                            <svg
                                x-show="confirmMode === 'duplicate'"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <rect
                                    x="8"
                                    y="8"
                                    width="10"
                                    height="10"
                                    rx="2"
                                    stroke-width="1.6"
                                />
                                <path
                                    d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"
                                    stroke-width="1.6"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <svg
                                x-show="confirmMode === 'delete'"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    d="M5 7h14M9 7V4h6v3M8 10v8M12 10v8M16 10v8M7 7l1 14h8l1-14"
                                    stroke-width="1.55"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>

                        <h3 x-text="confirmTitle()"></h3>

                        <p x-text="confirmMessage()"></p>
                    </div>

                    <footer class="master-vault-v10-confirm-foot">
                        <button
                            type="button"
                            class="master-vault-v10-confirm-cancel"
                            @click="closeConfirm()"
                            :disabled="confirmBusy"
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            class="master-vault-v10-confirm-ok"
                            :class="{
                                'is-danger':
                                    confirmMode === 'delete'
                            }"
                            @click="confirmVaultAction()"
                            :disabled="confirmBusy"
                        >
                            <span
                                x-show="!confirmBusy"
                                x-text="
                                    confirmMode === 'delete'
                                        ? 'Excluir Item'
                                        : 'Criar Cópia'
                                "
                            ></span>

                            <span
                                x-show="confirmBusy"
                                x-cloak
                            >
                                Processando...
                            </span>
                        </button>
                    </footer>
                </section>
            </div>
        </template>

        {{-- =========================================================
             VISUALIZAÇÃO RÁPIDA DO ITEM
        ========================================================== --}}
        <template x-teleport="body">
            <div
                x-show="previewOpen && previewItem"
                x-cloak
                class="master-vault-v9-preview-layer"
                x-transition.opacity.duration.130ms
                role="dialog"
                aria-modal="true"
                aria-label="Visualização do item"
            >
                <div
                    class="master-vault-v9-preview-backdrop"
                    @click="closePreview()"
                ></div>

                <section
                    class="master-vault-v9-preview"
                    x-transition:enter="transition ease-out duration-170"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-[.985]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                >
                    <header class="master-vault-v9-preview-head">
                        <div class="master-vault-v9-preview-head-copy">
                            <span class="master-vault-v9-preview-kicker">
                                Visualização do Mestre
                            </span>
                        </div>

                        <div class="master-vault-v9-preview-head-actions">
                            <button
                                type="button"
                                class="master-vault-v9-preview-nav"
                                :disabled="!hasPreviousPreview"
                                @click="previewAdjacent(-1)"
                                title="Item anterior"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path
                                        d="m15 6-6 6 6 6"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="master-vault-v9-preview-nav"
                                :disabled="!hasNextPreview"
                                @click="previewAdjacent(1)"
                                title="Próximo item"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path
                                        d="m9 6 6 6-6 6"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="master-vault-v9-preview-close"
                                @click="closePreview()"
                                title="Fechar"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path
                                        d="M6 6l12 12M18 6 6 18"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </div>
                    </header>

                    <div class="master-vault-v9-preview-body">
                        <section class="master-vault-v9-preview-hero master-vault-v16-preview-hero">
                            <div class="master-vault-v9-preview-image master-vault-v16-preview-image">
                                <template x-if="previewItem?.image_url">
                                    <img
                                        :src="previewItem.image_url"
                                        :alt="previewItem.name"
                                    >
                                </template>

                                <template x-if="!previewItem?.image_url">
                                    <svg
                                        class="h-12 w-12"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path d="M5 6h14v12H5z" stroke-width="1.5"/>
                                        <path
                                            d="m7 15 3.2-3.2 2.5 2.5 2-2 2.8 2.7"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </template>
                            </div>

                            <div class="master-vault-v9-preview-summary master-vault-v16-preview-summary">
                                <span class="master-vault-v16-eyebrow">
                                    Item do Inventário
                                </span>

                                <h1
                                    class="master-vault-v9-preview-name master-vault-v16-preview-name"
                                    x-text="previewItem?.name"
                                ></h1>

                                <div class="master-vault-v16-classification">
                                    <p
                                        class="master-vault-v16-nature"
                                        x-text="cardNatureLine(previewItem)"
                                    ></p>

                                    <p
                                        x-show="cardCategoryLine(previewItem)"
                                        x-cloak
                                        class="master-vault-v16-category"
                                        x-text="cardCategoryLine(previewItem)"
                                    ></p>
                                </div>

                                <div
                                    x-show="(previewItem?.properties?.weapon?.traits ?? []).length > 0"
                                    x-cloak
                                    class="master-vault-v16-inline-rules"
                                >
                                    <strong>Propriedades:</strong>

                                    <div class="master-vault-v16-inline-list">
                                        <template
                                            x-for="(trait, index) in (previewItem?.properties?.weapon?.traits ?? [])"
                                            :key="'preview-inline-trait-' + trait.key"
                                        >
                                            <span class="master-vault-v16-inline-entry">
                                                <button
                                                    type="button"
                                                    class="master-vault-v16-hover-link"
                                                    :data-tooltip="trait.description || 'Sem descrição.'"
                                                    :title="trait.description || trait.name"
                                                >
                                                    <span x-text="trait.name"></span>
                                                    <small
                                                        x-show="weaponRuleCompactValue(trait, previewItem?.properties?.weapon)"
                                                        x-text="' (' + weaponRuleCompactValue(trait, previewItem?.properties?.weapon) + ')'"
                                                    ></small>
                                                </button>
                                                <span
                                                    x-show="index < (previewItem?.properties?.weapon?.traits ?? []).length - 1"
                                                    class="master-vault-v16-separator"
                                                >,</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div
                                    x-show="(previewItem?.properties?.weapon?.masteries ?? []).length > 0"
                                    x-cloak
                                    class="master-vault-v16-inline-rules"
                                >
                                    <strong>Maestrias:</strong>

                                    <div class="master-vault-v16-inline-list">
                                        <template
                                            x-for="(mastery, index) in (previewItem?.properties?.weapon?.masteries ?? [])"
                                            :key="'preview-inline-mastery-' + mastery.key"
                                        >
                                            <span class="master-vault-v16-inline-entry">
                                                <button
                                                    type="button"
                                                    class="master-vault-v16-hover-link"
                                                    :data-tooltip="mastery.description || 'Sem descrição.'"
                                                    :title="mastery.description || mastery.name"
                                                >
                                                    <span x-text="'(' + mastery.name + ')'"></span>
                                                </button>
                                                <span
                                                    x-show="index < (previewItem?.properties?.weapon?.masteries ?? []).length - 1"
                                                    class="master-vault-v16-separator"
                                                >,</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div
                                    x-show="
                                        (previewItem?.weight !== null && previewItem?.weight !== '')
                                        || (previewItem?.default_quantity ?? 1) !== 1
                                    "
                                    x-cloak
                                    class="master-vault-v16-small-meta"
                                >
                                    <span
                                        x-show="previewItem?.weight !== null && previewItem?.weight !== ''"
                                        x-cloak
                                    >
                                        <strong>Peso:</strong>
                                        <span x-text="previewItem?.weight"></span>
                                    </span>

                                    <span
                                        x-show="(previewItem?.default_quantity ?? 1) !== 1"
                                        x-cloak
                                    >
                                        <strong>Quantidade:</strong>
                                        <span x-text="previewItem?.default_quantity ?? 1"></span>
                                    </span>
                                </div>
                            </div>
                        </section>

                        <section
                            x-show="previewItem?.description"
                            x-cloak
                            class="master-vault-v16-content-section"
                        >
                            <div class="master-vault-v16-section-title">
                                <span>Descrição</span>
                                <i></i>
                            </div>

                            <p
                                class="master-vault-v16-description"
                                x-text="previewItem?.description"
                            ></p>
                        </section>

                        {{-- Armadura --}}
                        <section
                            x-show="previewItem?.properties?.armor"
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v9-preview-section-head">
                                <h3>Armadura</h3>

                                <span
                                    x-text="
                                        previewItem?.properties?.armor?.armor_type
                                        || armorCategoryLabel(
                                            previewItem?.properties?.armor?.category
                                        )
                                    "
                                ></span>
                            </div>

                            <article class="master-vault-v9-preview-rule">
                                <strong>Fórmula de CA</strong>

                                <p
                                    x-text="
                                        armorFormulaText(
                                            previewItem?.properties?.armor
                                        )
                                    "
                                ></p>
                            </article>
                        </section>

                        {{-- Escudo --}}
                        <section
                            x-show="previewItem?.properties?.shield"
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v9-preview-section-head">
                                <h3>Escudo</h3>

                                <span
                                    x-text="
                                        previewItem?.properties?.shield?.label
                                        || 'Escudo'
                                    "
                                ></span>
                            </div>

                            <article class="master-vault-v9-preview-rule">
                                <strong>Bônus defensivo</strong>

                                <p
                                    x-text="
                                        '+' + shieldTotalBonus(
                                            previewItem?.properties?.shield
                                        ) + ' CA'
                                    "
                                ></p>
                            </article>
                        </section>

                        {{-- Propriedades custom --}}
                        <section
                            x-show="
                                (previewItem?.properties?.custom_properties ?? []).length > 0
                            "
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v9-preview-section-head">
                                <h3>Propriedades</h3>
                            </div>

                            <div class="master-vault-v9-preview-grid">
                                <template
                                    x-for="
                                        (property, index) in
                                        (previewItem?.properties?.custom_properties ?? [])
                                    "
                                    :key="'preview-property-' + index"
                                >
                                    <article class="master-vault-v9-preview-rule">
                                        <strong x-text="property.title || 'Propriedade'"></strong>

                                        <p
                                            x-text="
                                                [property.value, property.description]
                                                    .filter(Boolean)
                                                    .join(' — ')
                                            "
                                        ></p>
                                    </article>
                                </template>
                            </div>
                        </section>

                        {{-- Habilidades --}}
                        <section
                            x-show="
                                (previewItem?.properties?.features ?? []).length > 0
                            "
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v16-section-title">
                                <span>Habilidades</span>
                                <i></i>
                            </div>

                            <template
                                x-for="
                                    (feature, index) in
                                    (previewItem?.properties?.features ?? [])
                                "
                                :key="'preview-feature-' + index"
                            >
                                <article class="master-vault-v9-preview-feature">
                                    <div class="master-vault-v9-preview-feature-head">
                                        <h4
                                            x-text="
                                                feature.title
                                                || ('Habilidade ' + (index + 1))
                                            "
                                        ></h4>

                                        <span
                                            x-show="feature?.usage?.enabled"
                                            class="master-vault-v9-preview-usage"
                                            x-text="
                                                feature.usage.counter_mode === 'build'
                                                    ? (
                                                        'Acúmulo '
                                                        + feature.usage.current
                                                        + '/'
                                                        + feature.usage.max
                                                    )
                                                    : (
                                                        feature.usage.current
                                                        + '/'
                                                        + feature.usage.max
                                                        + ' usos'
                                                    )
                                                + (
                                                    feature.usage.recovery
                                                        ? (
                                                            ' · '
                                                            + recoveryLabel(
                                                                feature.usage.recovery
                                                            )
                                                        )
                                                        : ''
                                                )
                                            "
                                        ></span>
                                    </div>

                                    <p x-text="feature.description"></p>
                                </article>
                            </template>
                        </section>

                        {{-- Segredos do Mestre --}}
                        <section
                            x-show="
                                previewItem?.is_cursed
                                || previewItem?.notes
                            "
                            class="master-vault-v9-preview-secret"
                        >
                            <span class="master-vault-v9-preview-secret-label">
                                Somente o Mestre
                            </span>

                            <h3>Informações privadas</h3>

                            <div x-show="previewItem?.is_cursed">
                                <p>
                                    <strong x-text="previewItem?.curse_name || 'Maldição'"></strong>
                                    <span
                                        x-text="
                                            previewItem?.curse_description
                                            || 'Item marcado como amaldiçoado.'
                                        "
                                    ></span>
                                </p>
                            </div>

                            <div x-show="previewItem?.notes">
                                <p>
                                    <strong>Notas:</strong>
                                    <span x-text="previewItem?.notes"></span>
                                </p>
                            </div>
                        </section>
                    </div>

                    <footer class="master-vault-v9-preview-foot">
                        <span
                            class="master-vault-v9-preview-position"
                            x-text="
                                previewIndex >= 0
                                    ? (
                                        (previewIndex + 1)
                                        + ' de '
                                        + filteredItems.length
                                    )
                                    : ''
                            "
                        ></span>

                        <div class="master-vault-v9-preview-foot-actions">
                            <button
                                type="button"
                                class="master-vault-v9-preview-dismiss"
                                @click="closePreview()"
                            >
                                Fechar
                            </button>

                            <button
                                type="button"
                                class="master-vault-v9-preview-edit"
                                @click="editFromPreview()"
                            >
                                Editar Item
                            </button>
                        </div>
                    </footer>
                </section>
            </div>
        </template>

        <template x-teleport="body">
            <div
                x-show="editorOpen"
                x-cloak
                class="master-vault-v4-layer"
                x-transition.opacity.duration.140ms
                role="dialog"
                aria-modal="true"
                aria-label="Editor de item do Vault"
            >
                <div
                    class="master-vault-v4-backdrop"
                    @click="closeEditor()"
                ></div>

                <section
                    class="master-vault-v4-modal"
                    x-transition:enter="transition ease-out duration-180"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-[.985]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-130"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-[.99]"
                >
                    <header class="master-vault-v4-head">
                        <div class="min-w-0">
                            <p class="master-vault-v4-head-kicker">
                                Vault Permanente do Mestre
                            </p>

                            <h2 x-text="editingId ? 'Editar Item' : 'Novo Item'"></h2>

                            <div
                                x-show="form"
                                class="master-vault-v4-head-meta master-vault-v15-editor-taxonomy"
                            >
                                <span
                                    class="master-vault-v15-editor-taxonomy-primary"
                                    x-text="form ? cardNatureLine(form) : ''"
                                ></span>

                                <span
                                    x-show="form && cardCategoryLine(form)"
                                    x-cloak
                                    class="master-vault-v15-editor-taxonomy-secondary"
                                    x-text="form ? cardCategoryLine(form) : ''"
                                ></span>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="master-vault-v4-close"
                            @click="closeEditor()"
                            :disabled="saving"
                            aria-label="Fechar editor"
                        >
                            ×
                        </button>
                    </header>

                    <template x-if="form">
                        <div class="master-vault-v4-scroll">
                            {{-- =====================================================
                                 IDENTIDADE — MESMA LÓGICA VISUAL DO INVENTÁRIO
                            ====================================================== --}}
                            <section class="master-vault-v4-identity">
                                <aside class="master-vault-v4-photo">
                                    <label class="master-vault-v4-photo-frame">
                                        <template x-if="imagePreview || form.image_url">
                                            <img
                                                :src="imagePreview || form.image_url"
                                                alt="Prévia do item"
                                            >
                                        </template>

                                        <template x-if="!(imagePreview || form.image_url)">
                                            <div class="master-vault-v4-photo-empty">
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        d="M4 5h16v14H4zM8 13l2.5-2.5L14 14l2-2 4 4M8 9h.01"
                                                        stroke-width="1.6"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>

                                                <span>Adicionar Foto</span>
                                            </div>
                                        </template>

                                        <input
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                            @click="$event.target.value = null"
                                            @change="handleImage($event)"
                                        >
                                    </label>

                                    <button
                                        x-show="imagePreview || form.image_url"
                                        x-cloak
                                        type="button"
                                        class="master-vault-v4-photo-remove"
                                        @click="removeImage()"
                                    >
                                        Remover Foto
                                    </button>
                                </aside>

                                <div class="master-vault-v4-stack">
                                    <label class="master-vault-v4-field">
                                        <span>Nome</span>

                                        <input
                                            type="text"
                                            maxlength="120"
                                            x-model="form.name"
                                            placeholder="Ex.: Machado Lampejo Real"
                                            class="master-vault-v4-input"
                                        >
                                    </label>

                                    <label
                                        x-show="folderSystemReady"
                                        x-cloak
                                        class="master-vault-v4-field"
                                    >
                                        <span>Pasta</span>

                                        <select
                                            x-model="formFolderId"
                                            class="master-vault-v4-select"
                                        >
                                            <option value="">Sem pasta</option>
                                            <template x-for="folder in folders" :key="'editor-folder-' + folder.id">
                                                <option :value="folder.id" x-text="folder.name"></option>
                                            </template>
                                        </select>
                                    </label>

                                    <div>
                                        <div class="flex items-end justify-between gap-3">
                                            <span class="master-vault-v4-label">
                                                É equipável?
                                            </span>

                                            <span class="master-vault-v4-help">
                                                Permite Equipar / Guardar quando chegar ao personagem.
                                            </span>
                                        </div>

                                        <div class="master-vault-v4-choice-grid mt-1.5 sm:max-w-xs">
                                            <button
                                                type="button"
                                                class="master-vault-v4-choice"
                                                :class="{ 'is-active': form.properties.inventory.equippable }"
                                                @click="setEquippable(true)"
                                            >
                                                Sim
                                            </button>

                                            <button
                                                type="button"
                                                class="master-vault-v4-choice"
                                                :class="{ 'is-active': !form.properties.inventory.equippable }"
                                                @click="setEquippable(false)"
                                            >
                                                Não
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="master-vault-v4-label">
                                            Natureza do Item
                                        </span>

                                        <div class="master-vault-v4-choice-grid is-3 mt-1.5">
                                            <button
                                                type="button"
                                                class="master-vault-v4-choice"
                                                :class="{ 'is-active': form.type === 'wonderful' }"
                                                @click="setNature('wonderful')"
                                            >
                                                Maravilhoso
                                            </button>

                                            <button
                                                type="button"
                                                class="master-vault-v4-choice"
                                                :class="{ 'is-active': form.type === 'technological' }"
                                                @click="setNature('technological')"
                                            >
                                                Tecnológico
                                            </button>

                                            <button
                                                type="button"
                                                class="master-vault-v4-choice"
                                                :class="{ 'is-active': form.type === 'mundane' }"
                                                @click="setNature('mundane')"
                                            >
                                                Mundano
                                            </button>
                                        </div>
                                    </div>

                                    <div class="master-vault-v4-grid-3">
                                        <label class="master-vault-v4-field">
                                            <span>Quantidade Padrão</span>

                                            <input
                                                type="number"
                                                min="1"
                                                max="999999"
                                                x-model.number="form.default_quantity"
                                                class="master-vault-v4-input"
                                            >
                                        </label>

                                        <label class="master-vault-v4-field">
                                            <span>Peso</span>

                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                x-model="form.weight"
                                                placeholder="Opcional"
                                                class="master-vault-v4-input"
                                            >
                                        </label>

                                        <div>
                                            <span class="master-vault-v4-label">
                                                Favorito
                                            </span>

                                            <div class="master-vault-v4-toggle-card">
                                                <div>
                                                    <strong>Destacar</strong>
                                                    <small>
                                                        Mantém no topo do Vault.
                                                    </small>
                                                </div>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-switch"
                                                    :class="{ 'is-on': form.favorite }"
                                                    @click="form.favorite = !form.favorite"
                                                ></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        x-show="form.type === 'wonderful'"
                                        x-cloak
                                        class="master-vault-v4-grid-2"
                                    >
                                        <label class="master-vault-v4-field">
                                            <span>Raridade</span>

                                            <select
                                                x-model="form.rarity"
                                                class="master-vault-v4-select"
                                            >
                                                @foreach($rarityLabels as $key => $label)
                                                    <option value="{{ $key }}">
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>

                                        <div>
                                            <span class="master-vault-v4-label">
                                                Sintonização
                                            </span>

                                            <div class="master-vault-v4-toggle-card">
                                                <div>
                                                    <strong>Sintonizável</strong>
                                                    <small>
                                                        Chega ao inventário ainda não sintonizado.
                                                    </small>
                                                </div>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-switch"
                                                    :class="{ 'is-on': form.requires_attunement }"
                                                    @click="form.requires_attunement = !form.requires_attunement"
                                                ></button>
                                            </div>
                                        </div>
                                    </div>

                                    <label class="master-vault-v4-field">
                                        <span>Descrição</span>

                                        <textarea
                                            maxlength="30000"
                                            x-model="form.description"
                                            placeholder="Descrição pública que acompanhará o item quando for encontrado..."
                                            class="master-vault-v4-textarea"
                                        ></textarea>
                                    </label>

                                    <label class="master-vault-v4-field">
                                        <span>Tags do Vault</span>

                                        <input
                                            type="text"
                                            x-model="tagsText"
                                            placeholder="Ex.: tesouro, laboratório, investigação"
                                            class="master-vault-v4-input"
                                        >
                                    </label>
                                </div>
                            </section>

                            <div class="master-vault-v4-divider"></div>

                            {{-- =====================================================
                                 CATEGORIA DO ITEM
                            ====================================================== --}}
                            <section>
                                <div class="master-vault-v5-category-shell">
                                    <header class="master-vault-v5-category-head">
                                        <span class="master-vault-v4-section-kicker">
                                            Classificação Mecânica
                                        </span>

                                        <strong>Categoria do Item</strong>

                                        <p>
                                            Arma, Armadura e Escudo são categorias principais.
                                            Habilidades são independentes e ficam sempre disponíveis
                                            em uma seção própria abaixo.
                                        </p>
                                    </header>

                                    <div class="master-vault-v5-category-grid">
                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'weapon' }"
                                            @click="setItemCategory('weapon')"
                                        >
                                            <span class="master-vault-v5-category-icon">
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M15.5 3.5 21 2l-1.5 5.5-9.1 9.1-3-3 8.1-10.1Z"
                                                        stroke-width="1.7"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="m8.4 12.6 3 3M6.4 14.6l3 3M7.4 17.6 4 21"
                                                        stroke-width="1.7"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </span>

                                            <span>
                                                <strong>Arma</strong>
                                                <span>Dano, propriedades, maestrias e bônus.</span>
                                            </span>
                                        </button>

                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'armor' }"
                                            @click="setItemCategory('armor')"
                                        >
                                            <span class="master-vault-v5-category-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path d="M7 4.5 12 3l5 1.5 2 4.5-2 11H7L5 9l2-4.5Z"
                                                          stroke-width="1.6"
                                                          stroke-linejoin="round"/>
                                                    <path d="M9 5.2 12 8l3-2.8"
                                                          stroke-width="1.6"
                                                          stroke-linecap="round"/>
                                                </svg>
                                            </span>

                                            <span>
                                                <strong>Armadura</strong>
                                                <span>CA base, Destreza e fórmula defensiva.</span>
                                            </span>
                                        </button>

                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'shield' }"
                                            @click="setItemCategory('shield')"
                                        >
                                            <span class="master-vault-v5-category-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path d="M12 3 19 6v5c0 4.6-2.8 8-7 10-4.2-2-7-5.4-7-10V6l7-3Z"
                                                          stroke-width="1.6"
                                                          stroke-linejoin="round"/>
                                                </svg>
                                            </span>

                                            <span>
                                                <strong>Escudo</strong>
                                                <span>Bônus defensivo base e bônus mágico.</span>
                                            </span>
                                        </button>

                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'other' }"
                                            @click="setItemCategory('other')"
                                        >
                                            <span class="master-vault-v5-category-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path d="M5 7.5 12 4l7 3.5v9L12 20l-7-3.5v-9Z"
                                                          stroke-width="1.6"
                                                          stroke-linejoin="round"/>
                                                    <path d="m5 7.5 7 3.5 7-3.5M12 11v9"
                                                          stroke-width="1.6"
                                                          stroke-linejoin="round"/>
                                                </svg>
                                            </span>

                                            <span>
                                                <strong>Outro Item</strong>
                                                <span>Consumível, ferramenta, acessório ou tesouro.</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                {{-- ARMA --}}
                                <article
                                    x-show="itemCategory() === 'weapon'"
                                    x-cloak
                                    class="master-vault-v4-module"
                                >
                                    <header class="master-vault-v4-module-head">
                                        <div>
                                            <h3>Arma</h3>

                                            <p>
                                                Tipo, bônus, propriedades, dano, danos extras e maestrias.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            class="master-vault-v4-remove"
                                            @click="setItemCategory('other')"
                                        >
                                            Limpar Categoria
                                        </button>
                                    </header>

                                    <div class="master-vault-v4-module-body">
                                        <section class="master-vault-v4-rule">
                                            <p class="master-vault-v4-rule-title">
                                                Tipo de Arma
                                            </p>

                                            <div class="master-vault-v4-choice-grid is-3 mt-2 sm:max-w-lg">
                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.weapon.category === 'simple' }"
                                                    @click="form.properties.weapon.category = 'simple'"
                                                >
                                                    Simples
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.weapon.category === 'martial' }"
                                                    @click="form.properties.weapon.category = 'martial'"
                                                >
                                                    Marcial
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.weapon.category === 'custom' }"
                                                    @click="form.properties.weapon.category = 'custom'"
                                                >
                                                    Custom
                                                </button>
                                            </div>

                                            <input
                                                x-show="form.properties.weapon.category === 'custom'"
                                                x-cloak
                                                type="text"
                                                maxlength="120"
                                                x-model="form.properties.weapon.category_custom"
                                                placeholder="Ex.: Arma Exótica"
                                                class="master-vault-v4-input mt-2 sm:max-w-lg"
                                            >

                                            <div class="master-vault-v4-grid-2 mt-3">
                                                <label class="master-vault-v4-field">
                                                    <span>Modelo / Tipo</span>

                                                    <input
                                                        type="text"
                                                        maxlength="120"
                                                        x-model="form.properties.weapon.weapon_type"
                                                        placeholder="Ex.: Faca, Machado, Cajado..."
                                                        class="master-vault-v4-input"
                                                    >
                                                </label>

                                                <label class="master-vault-v4-field">
                                                    <span>Bônus</span>

                                                    <input
                                                        type="number"
                                                        min="-99"
                                                        max="99"
                                                        x-model.number="form.properties.weapon.magic_bonus"
                                                        class="master-vault-v4-input"
                                                    >
                                                </label>
                                            </div>
                                        </section>

                                        <section class="master-vault-v4-rule">
                                            <p class="master-vault-v4-rule-title">
                                                Propriedades da Arma
                                            </p>

                                            <p class="master-vault-v4-rule-copy">
                                                Selecione as propriedades da arma. As propriedades oficiais ficam em
                                                gavetas compactas e não podem ser alteradas; apenas propriedades Custom são editáveis.
                                            </p>

                                            <div class="master-vault-v4-chip-row">
                                                <template
                                                    x-for="definition in weaponTraitDefinitions"
                                                    :key="definition.key"
                                                >
                                                    <button
                                                        type="button"
                                                        class="master-vault-v4-chip-button"
                                                        :class="{ 'is-active': traitSelected(definition.key) }"
                                                        :title="definition.description"
                                                        @click="toggleTrait(definition)"
                                                        x-text="definition.name"
                                                    ></button>
                                                </template>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-chip-button"
                                                    @click="addCustomTrait()"
                                                >
                                                    + Custom
                                                </button>
                                            </div>

                                            <div
                                                x-show="form.properties.weapon.traits.some(trait => !trait.custom)"
                                                x-cloak
                                                class="master-vault-v10-8-selected"
                                            >
                                                <div class="master-vault-v10-8-selected-head">
                                                    <strong>Propriedades Selecionadas</strong>

                                                    <span
                                                        x-text="
                                                            form.properties.weapon.traits
                                                                .filter(trait => !trait.custom)
                                                                .length
                                                            + (
                                                                form.properties.weapon.traits
                                                                    .filter(trait => !trait.custom)
                                                                    .length === 1
                                                                    ? ' propriedade'
                                                                    : ' propriedades'
                                                            )
                                                        "
                                                    ></span>
                                                </div>

                                                <div class="master-vault-v10-8-drawer-list">
                                                    <template
                                                        x-for="
                                                            trait in
                                                            form.properties.weapon.traits.filter(
                                                                current => !current.custom
                                                            )
                                                        "
                                                        :key="'standard-trait-' + trait.key"
                                                    >
                                                        <article
                                                            x-data="{ open: false }"
                                                            class="master-vault-v10-8-drawer"
                                                        >
                                                            <header class="master-vault-v10-8-drawer-head">
                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-toggle"
                                                                    @click="open = !open"
                                                                    :aria-expanded="open"
                                                                >
                                                                    <svg
                                                                        class="master-vault-v10-8-chevron"
                                                                        :class="{ 'is-open': open }"
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="m9 6 6 6-6 6"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                        />
                                                                    </svg>

                                                                    <span class="master-vault-v10-8-drawer-title">
                                                                        <strong x-text="trait.name"></strong>

                                                                        <small
                                                                            x-show="
                                                                                weaponRuleCompactValue(
                                                                                    trait,
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                            x-text="
                                                                                weaponRuleCompactValue(
                                                                                    trait,
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                        ></small>
                                                                    </span>
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-remove"
                                                                    @click="toggleTrait(trait)"
                                                                    :title="'Remover ' + trait.name"
                                                                    :aria-label="'Remover ' + trait.name"
                                                                >
                                                                    <svg
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="M6 6l12 12M18 6 6 18"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                            </header>

                                                            <div
                                                                x-show="open"
                                                                x-cloak
                                                                x-transition.opacity.duration.120ms
                                                                class="master-vault-v10-8-drawer-body"
                                                            >
                                                                <p
                                                                    class="master-vault-v10-8-rule-description"
                                                                    x-text="
                                                                        trait.description
                                                                        || 'Sem descrição cadastrada.'
                                                                    "
                                                                ></p>

                                                                <div
                                                                    x-show="trait.key === 'range'"
                                                                    x-cloak
                                                                    class="master-vault-v10-8-measure"
                                                                >
                                                                    <div class="master-vault-v10-8-measure-head">
                                                                        <strong>Distância da Arma</strong>

                                                                        <span
                                                                            x-show="
                                                                                weaponRangeText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                            x-text="
                                                                                weaponRangeText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                        ></span>
                                                                    </div>

                                                                    <div class="master-vault-v10-8-measure-grid">
                                                                        <label class="master-vault-v4-field">
                                                                            <span>Alcance Normal (Ft)</span>

                                                                            <input
                                                                                type="number"
                                                                                min="0"
                                                                                step="1"
                                                                                x-model.number="
                                                                                    form.properties.weapon.range_normal_ft
                                                                                "
                                                                                placeholder="Ex.: 80"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>

                                                                        <label class="master-vault-v4-field">
                                                                            <span>Alcance Máximo (Ft)</span>

                                                                            <input
                                                                                type="number"
                                                                                min="0"
                                                                                step="1"
                                                                                x-model.number="
                                                                                    form.properties.weapon.range_long_ft
                                                                                "
                                                                                placeholder="Ex.: 320"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    x-show="trait.key === 'loading'"
                                                                    x-cloak
                                                                    class="master-vault-v10-8-measure"
                                                                >
                                                                    <div class="master-vault-v10-8-measure-head">
                                                                        <strong>Capacidade antes de recarregar</strong>

                                                                        <span
                                                                            x-show="form.properties.weapon.loading_capacity"
                                                                            x-text="weaponRuleCompactValue(trait, form.properties.weapon)"
                                                                        ></span>
                                                                    </div>

                                                                    <div class="master-vault-v10-8-measure-grid is-single">
                                                                        <label class="master-vault-v4-field">
                                                                            <span>Munições no carregador</span>

                                                                            <input
                                                                                type="number"
                                                                                min="1"
                                                                                max="999999"
                                                                                step="1"
                                                                                x-model.number="form.properties.weapon.loading_capacity"
                                                                                placeholder="Ex.: 6"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    x-show="trait.key === 'reach'"
                                                                    x-cloak
                                                                    class="master-vault-v10-8-measure"
                                                                >
                                                                    <div class="master-vault-v10-8-measure-head">
                                                                        <strong>Alcance Corpo a Corpo</strong>

                                                                        <span
                                                                            x-show="
                                                                                weaponReachText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                            x-text="
                                                                                weaponReachText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                        ></span>
                                                                    </div>

                                                                    <div class="master-vault-v10-8-measure-grid is-single">
                                                                        <label class="master-vault-v4-field">
                                                                            <span>Alcance (Ft)</span>

                                                                            <input
                                                                                type="number"
                                                                                min="0"
                                                                                step="1"
                                                                                x-model.number="
                                                                                    form.properties.weapon.reach_ft
                                                                                "
                                                                                placeholder="Ex.: 10"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </article>
                                                    </template>
                                                </div>
                                            </div>

                                            <div class="master-vault-v4-repeat">
                                                <template
                                                    x-for="(trait, index) in form.properties.weapon.traits"
                                                    :key="trait.key"
                                                >
                                                    <div
                                                        x-show="trait.custom"
                                                        x-cloak
                                                        class="master-vault-v4-repeat-row"
                                                    >
                                                        <input
                                                            type="text"
                                                            maxlength="120"
                                                            x-model="trait.name"
                                                            placeholder="Nome da propriedade"
                                                            class="master-vault-v4-input"
                                                        >

                                                        <input
                                                            type="text"
                                                            maxlength="2000"
                                                            x-model="trait.description"
                                                            placeholder="O que esta propriedade faz?"
                                                            class="master-vault-v4-input"
                                                        >

                                                        <button
                                                            type="button"
                                                            class="master-vault-v4-remove"
                                                            @click="removeTrait(index)"
                                                        >
                                                            Remover
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </section>

<section class="master-vault-v4-rule">
                                            <p class="master-vault-v4-rule-title">
                                                Maestrias
                                            </p>

                                            <p class="master-vault-v4-rule-copy">
                                                Selecione uma ou mais maestrias. As maestrias oficiais ficam em
                                                gavetas compactas e preservam exatamente a regra cadastrada.
                                            </p>

                                            <div class="master-vault-v4-chip-row">
                                                <template
                                                    x-for="definition in masteryDefinitions"
                                                    :key="definition.key"
                                                >
                                                    <button
                                                        type="button"
                                                        class="master-vault-v4-chip-button"
                                                        :class="{ 'is-active': masterySelected(definition.key) }"
                                                        :title="definition.description"
                                                        @click="toggleMastery(definition)"
                                                        x-text="definition.name"
                                                    ></button>
                                                </template>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-chip-button"
                                                    @click="addCustomMastery()"
                                                >
                                                    + Custom
                                                </button>
                                            </div>

                                            <div
                                                x-show="
                                                    form.properties.weapon.masteries.some(
                                                        mastery => !mastery.custom
                                                    )
                                                "
                                                x-cloak
                                                class="master-vault-v10-8-selected"
                                            >
                                                <div class="master-vault-v10-8-selected-head">
                                                    <strong>Maestrias Selecionadas</strong>

                                                    <span
                                                        x-text="
                                                            form.properties.weapon.masteries
                                                                .filter(mastery => !mastery.custom)
                                                                .length
                                                            + (
                                                                form.properties.weapon.masteries
                                                                    .filter(mastery => !mastery.custom)
                                                                    .length === 1
                                                                    ? ' maestria'
                                                                    : ' maestrias'
                                                            )
                                                        "
                                                    ></span>
                                                </div>

                                                <div class="master-vault-v10-8-drawer-list">
                                                    <template
                                                        x-for="
                                                            mastery in
                                                            form.properties.weapon.masteries.filter(
                                                                current => !current.custom
                                                            )
                                                        "
                                                        :key="'standard-mastery-' + mastery.key"
                                                    >
                                                        <article
                                                            x-data="{ open: false }"
                                                            class="master-vault-v10-8-drawer"
                                                        >
                                                            <header class="master-vault-v10-8-drawer-head">
                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-toggle"
                                                                    @click="open = !open"
                                                                    :aria-expanded="open"
                                                                >
                                                                    <svg
                                                                        class="master-vault-v10-8-chevron"
                                                                        :class="{ 'is-open': open }"
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="m9 6 6 6-6 6"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                        />
                                                                    </svg>

                                                                    <span class="master-vault-v10-8-drawer-title">
                                                                        <strong x-text="mastery.name"></strong>
                                                                    </span>
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-remove"
                                                                    @click="toggleMastery(mastery)"
                                                                    :title="'Remover ' + mastery.name"
                                                                    :aria-label="'Remover ' + mastery.name"
                                                                >
                                                                    <svg
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="M6 6l12 12M18 6 6 18"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                            </header>

                                                            <div
                                                                x-show="open"
                                                                x-cloak
                                                                x-transition.opacity.duration.120ms
                                                                class="master-vault-v10-8-drawer-body"
                                                            >
                                                                <p
                                                                    class="master-vault-v10-8-rule-description"
                                                                    x-text="
                                                                        mastery.description
                                                                        || 'Sem descrição cadastrada.'
                                                                    "
                                                                ></p>
                                                            </div>
                                                        </article>
                                                    </template>
                                                </div>
                                            </div>

                                            <div class="master-vault-v4-repeat">
                                                <template
                                                    x-for="(mastery, index) in form.properties.weapon.masteries"
                                                    :key="mastery.key"
                                                >
                                                    <div
                                                        x-show="mastery.custom"
                                                        x-cloak
                                                        class="master-vault-v4-repeat-row"
                                                    >
                                                        <input
                                                            type="text"
                                                            maxlength="120"
                                                            x-model="mastery.name"
                                                            placeholder="Nome da maestria"
                                                            class="master-vault-v4-input"
                                                        >

                                                        <textarea
                                                            rows="2"
                                                            maxlength="5000"
                                                            x-model="mastery.description"
                                                            placeholder="O que esta maestria faz?"
                                                            class="master-vault-v4-textarea"
                                                        ></textarea>

                                                        <button
                                                            type="button"
                                                            class="master-vault-v4-remove"
                                                            @click="removeMastery(index)"
                                                        >
                                                            Remover
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </section>

                                        <section class="master-vault-v4-rule">
                                            <div>
                                                <p class="master-vault-v4-rule-title">
                                                    Dano da Arma
                                                </p>

                                                <p class="master-vault-v4-rule-copy">
                                                    Configure o dano principal e os danos extras. O exemplo final
                                                    mostra a expressão completa do item.
                                                </p>
                                            </div>

                                            <div class="master-vault-v4-damage-grid mt-3">
                                                <label class="master-vault-v4-field">
                                                    <span>Dados</span>

                                                    <input
                                                        type="number"
                                                        min="1"
                                                        max="99"
                                                        x-model.number="form.properties.weapon.damage.count"
                                                        class="master-vault-v4-input"
                                                    >
                                                </label>

                                                <label class="master-vault-v4-field">
                                                    <span>Dado</span>

                                                    <input
                                                        type="text"
                                                        maxlength="20"
                                                        x-model="form.properties.weapon.damage.die"
                                                        placeholder="d8"
                                                        class="master-vault-v4-input"
                                                    >
                                                </label>

                                                <label class="master-vault-v4-field">
                                                    <span>Atributo</span>

                                                    <select
                                                        x-model="form.properties.weapon.damage.ability"
                                                        class="master-vault-v4-select"
                                                    >
                                                        @foreach($abilityLabels as $key => $label)
                                                            <option value="{{ $key }}">
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </label>

                                                <div></div>
                                            </div>

                                            <input
                                                x-show="form.properties.weapon.damage.ability === 'custom'"
                                                x-cloak
                                                type="text"
                                                maxlength="80"
                                                x-model="form.properties.weapon.damage.ability_custom"
                                                placeholder="Atributo ou modificador custom"
                                                class="master-vault-v4-input mt-2 sm:max-w-md"
                                            >

                                            <div class="master-vault-v7-damage-types">
                                                <div class="master-vault-v7-damage-types-head">
                                                    <div>
                                                        <strong>Tipo de Dano</strong>

                                                        <span>
                                                            Selecione um dos tipos abaixo ou crie um tipo Custom.
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="master-vault-v7-damage-type-grid">
                                                    <template
                                                        x-for="damageType in damageTypeDefinitions"
                                                        :key="'base-damage-type-' + damageType"
                                                    >
                                                        <button
                                                            type="button"
                                                            class="master-vault-v7-damage-type"
                                                            :class="{
                                                                'is-active':
                                                                    !form.properties.weapon.damage.type_custom_mode
                                                                    && form.properties.weapon.damage.type === damageType
                                                            }"
                                                            @click="
                                                                setDamageType(
                                                                    form.properties.weapon.damage,
                                                                    damageType
                                                                )
                                                            "
                                                            x-text="damageType"
                                                        ></button>
                                                    </template>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v7-damage-type is-custom"
                                                        :class="{
                                                            'is-active':
                                                                form.properties.weapon.damage.type_custom_mode
                                                        }"
                                                        @click="
                                                            setCustomDamageType(
                                                                form.properties.weapon.damage
                                                            )
                                                        "
                                                    >
                                                        + Custom
                                                    </button>
                                                </div>

                                                <input
                                                    x-show="form.properties.weapon.damage.type_custom_mode"
                                                    x-cloak
                                                    type="text"
                                                    maxlength="80"
                                                    x-model="form.properties.weapon.damage.type"
                                                    placeholder="Ex.: Plasma, Temporal, Sagrado..."
                                                    class="master-vault-v4-input mt-3"
                                                >
                                            </div>

                                            <div class="master-vault-v7-extra">
                                                <div class="master-vault-v7-extra-head">
                                                    <div>
                                                        <strong>Dano Extra</strong>

                                                        <span>
                                                            Ex.: 2d6 Elétrico ou 1d4 Veneno além do dano principal.
                                                        </span>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v4-link"
                                                        @click="addExtraDamage()"
                                                    >
                                                        + Adicionar Dano Extra
                                                    </button>
                                                </div>

                                                <div
                                                    x-show="form.properties.weapon.extra_damage.length === 0"
                                                    x-cloak
                                                    class="master-vault-v7-extra-empty"
                                                >
                                                    Nenhum dano extra configurado.
                                                </div>

                                                <div class="master-vault-v7-extra-list">
                                                    <template
                                                        x-for="(part, index) in form.properties.weapon.extra_damage"
                                                        :key="'extra-' + index"
                                                    >
                                                        <article class="master-vault-v7-extra-card">
                                                            <div class="master-vault-v7-extra-card-head">
                                                                <strong
                                                                    x-text="'Dano Extra ' + (index + 1)"
                                                                ></strong>

                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v4-remove"
                                                                    @click="removeExtraDamage(index)"
                                                                >
                                                                    Remover
                                                                </button>
                                                            </div>

                                                            <div class="master-vault-v7-extra-fields">
                                                                <label class="master-vault-v4-field">
                                                                    <span>Dados</span>

                                                                    <input
                                                                        type="number"
                                                                        min="1"
                                                                        max="99"
                                                                        x-model.number="part.count"
                                                                        class="master-vault-v4-input"
                                                                    >
                                                                </label>

                                                                <label class="master-vault-v4-field">
                                                                    <span>Dado</span>

                                                                    <input
                                                                        type="text"
                                                                        maxlength="20"
                                                                        x-model="part.die"
                                                                        placeholder="d6"
                                                                        class="master-vault-v4-input"
                                                                    >
                                                                </label>
                                                            </div>

                                                            <div class="master-vault-v7-extra-types">
                                                                <span class="master-vault-v7-extra-type-label">
                                                                    Tipo de Dano
                                                                </span>

                                                                <div class="master-vault-v7-damage-type-grid">
                                                                    <template
                                                                        x-for="damageType in damageTypeDefinitions"
                                                                        :key="
                                                                            'extra-'
                                                                            + index
                                                                            + '-damage-type-'
                                                                            + damageType
                                                                        "
                                                                    >
                                                                        <button
                                                                            type="button"
                                                                            class="master-vault-v7-damage-type"
                                                                            :class="{
                                                                                'is-active':
                                                                                    !part.type_custom_mode
                                                                                    && part.type === damageType
                                                                            }"
                                                                            @click="
                                                                                setDamageType(
                                                                                    part,
                                                                                    damageType
                                                                                )
                                                                            "
                                                                            x-text="damageType"
                                                                        ></button>
                                                                    </template>

                                                                    <button
                                                                        type="button"
                                                                        class="master-vault-v7-damage-type is-custom"
                                                                        :class="{
                                                                            'is-active':
                                                                                part.type_custom_mode
                                                                        }"
                                                                        @click="setCustomDamageType(part)"
                                                                    >
                                                                        + Custom
                                                                    </button>
                                                                </div>

                                                                <input
                                                                    x-show="part.type_custom_mode"
                                                                    x-cloak
                                                                    type="text"
                                                                    maxlength="80"
                                                                    x-model="part.type"
                                                                    placeholder="Tipo de dano custom"
                                                                    class="master-vault-v4-input mt-2"
                                                                >
                                                            </div>
                                                        </article>
                                                    </template>
                                                </div>
                                            </div>

                                            <div class="master-vault-v7-complete-example">
                                                <div class="master-vault-v7-complete-example-mark">
                                                    Σ
                                                </div>

                                                <div>
                                                    <span>Exemplo Completo do Dano</span>

                                                    <strong
                                                        x-text="
                                                            weaponDamagePreview(
                                                                form.properties.weapon
                                                            )
                                                            || 'Configure o dano acima'
                                                        "
                                                    ></strong>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </article>

                                {{-- ARMADURA --}}
                                <article
                                    x-show="itemCategory() === 'armor'"
                                    x-cloak
                                    class="master-vault-v4-module"
                                >
                                    <header class="master-vault-v4-module-head">
                                        <div>
                                            <h3>Armadura</h3>

                                            <p>
                                                Fórmula defensiva pronta para ser usada quando o item estiver equipado.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            class="master-vault-v4-remove"
                                            @click="setItemCategory('other')"
                                        >
                                            Limpar Categoria
                                        </button>
                                    </header>

                                    <div class="master-vault-v4-module-body">
                                        <section class="master-vault-v4-rule">
                                            <p class="master-vault-v4-rule-title">
                                                Categoria
                                            </p>

                                            <div class="master-vault-v4-choice-grid mt-2">
                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.armor.category === 'light' }"
                                                    @click="setArmorCategory('light')"
                                                >
                                                    Leve
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.armor.category === 'medium' }"
                                                    @click="setArmorCategory('medium')"
                                                >
                                                    Média
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.armor.category === 'heavy' }"
                                                    @click="setArmorCategory('heavy')"
                                                >
                                                    Pesada
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.armor.category === 'custom' }"
                                                    @click="setArmorCategory('custom')"
                                                >
                                                    Especial
                                                </button>
                                            </div>

                                            <input
                                                x-show="form.properties.armor.category === 'custom'"
                                                x-cloak
                                                type="text"
                                                maxlength="120"
                                                x-model="form.properties.armor.category_custom"
                                                placeholder="Ex.: Armadura Astral"
                                                class="master-vault-v4-input mt-2"
                                            >
                                        </section>

                                        <section class="master-vault-v4-rule">
                                            <div class="master-vault-v4-grid-3">
                                                <label class="master-vault-v4-field">
                                                    <span>Tipo / Modelo</span>

                                                    <input
                                                        type="text"
                                                        maxlength="120"
                                                        x-model="form.properties.armor.armor_type"
                                                        placeholder="Ex.: Meia Armadura"
                                                        class="master-vault-v4-input"
                                                    >
                                                </label>

                                                <label class="master-vault-v4-field">
                                                    <span>CA Base</span>

                                                    <input
                                                        type="number"
                                                        min="0"
                                                        max="99"
                                                        x-model.number="form.properties.armor.base_ac"
                                                        class="master-vault-v4-input"
                                                    >
                                                </label>

                                                <label class="master-vault-v4-field">
                                                    <span>Bônus Mágico</span>

                                                    <input
                                                        type="number"
                                                        min="-99"
                                                        max="99"
                                                        x-model.number="form.properties.armor.magic_bonus"
                                                        class="master-vault-v4-input"
                                                    >
                                                </label>
                                            </div>
                                        </section>

                                        <section class="master-vault-v4-rule">
                                            <p class="master-vault-v4-rule-title">
                                                Destreza na CA
                                            </p>

                                            <div class="master-vault-v4-choice-grid is-3 mt-2">
                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.armor.dexterity_mode === 'none' }"
                                                    @click="setArmorDexterityMode('none')"
                                                >
                                                    Sem DES
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.armor.dexterity_mode === 'full' }"
                                                    @click="setArmorDexterityMode('full')"
                                                >
                                                    DES Completa
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice"
                                                    :class="{ 'is-active': form.properties.armor.dexterity_mode === 'capped' }"
                                                    @click="setArmorDexterityMode('capped')"
                                                >
                                                    DES Limitada
                                                </button>
                                            </div>

                                            <label
                                                x-show="form.properties.armor.dexterity_mode === 'capped'"
                                                x-cloak
                                                class="master-vault-v4-field mt-3 block"
                                            >
                                                <span>Limite de DES</span>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="99"
                                                    x-model.number="form.properties.armor.dexterity_cap"
                                                    class="master-vault-v4-input sm:max-w-[180px]"
                                                >
                                            </label>
                                        </section>

                                        <section class="master-vault-v4-rule">
                                            <p class="master-vault-v4-rule-title">
                                                Atributos Adicionais
                                            </p>

                                            <p class="master-vault-v4-rule-copy">
                                                Para armaduras especiais que somem outro modificador à CA.
                                            </p>

                                            <div class="master-vault-v4-chip-row">
                                                <template
                                                    x-for="ability in ['strength','constitution','intelligence','wisdom','charisma']"
                                                    :key="'armor-ability-' + ability"
                                                >
                                                    <button
                                                        type="button"
                                                        class="master-vault-v4-chip-button"
                                                        :class="{ 'is-active': form.properties.armor.ability_modifiers.includes(ability) }"
                                                        @click="toggleArmorAbilityModifier(ability)"
                                                        x-text="abilityShortLabel(ability)"
                                                    ></button>
                                                </template>
                                            </div>

                                            <div class="master-vault-v4-formula">
                                                <div>
                                                    <span>Fórmula da Armadura</span>

                                                    <strong
                                                        x-text="armorFormulaText(form.properties.armor)"
                                                    ></strong>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </article>

                                {{-- ESCUDO --}}
                                <article
                                    x-show="itemCategory() === 'shield'"
                                    x-cloak
                                    class="master-vault-v4-module"
                                >
                                    <header class="master-vault-v4-module-head">
                                        <div>
                                            <h3>Escudo</h3>

                                            <p>
                                                Bônus base e bônus mágico separados.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            class="master-vault-v4-remove"
                                            @click="setItemCategory('other')"
                                        >
                                            Limpar Categoria
                                        </button>
                                    </header>

                                    <div class="master-vault-v4-module-body">
                                        <div class="master-vault-v4-grid-3">
                                            <label class="master-vault-v4-field">
                                                <span>Nome / Modelo</span>

                                                <input
                                                    type="text"
                                                    maxlength="120"
                                                    x-model="form.properties.shield.label"
                                                    placeholder="Escudo"
                                                    class="master-vault-v4-input"
                                                >
                                            </label>

                                            <label class="master-vault-v4-field">
                                                <span>Bônus de CA</span>

                                                <input
                                                    type="number"
                                                    min="-99"
                                                    max="99"
                                                    x-model.number="form.properties.shield.ac_bonus"
                                                    class="master-vault-v4-input"
                                                >
                                            </label>

                                            <label class="master-vault-v4-field">
                                                <span>Bônus Mágico</span>

                                                <input
                                                    type="number"
                                                    min="-99"
                                                    max="99"
                                                    x-model.number="form.properties.shield.magic_bonus"
                                                    class="master-vault-v4-input"
                                                >
                                            </label>
                                        </div>

                                        <div class="master-vault-v4-formula">
                                            <div>
                                                <span>Bônus Total do Escudo</span>

                                                <strong
                                                    x-text="'+' + shieldTotalBonus(form.properties.shield)"
                                                ></strong>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </section>

                            {{-- =====================================================
                                 PROPRIEDADES CUSTOM
                            ====================================================== --}}
                            <section class="master-vault-v4-subsection">
                                <div class="master-vault-v4-subsection-head">
                                    <div>
                                        <h3>Propriedades Custom</h3>

                                        <p>
                                            Informações livres que acompanham o item até o inventário.
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        class="master-vault-v4-link"
                                        @click="addCustomProperty()"
                                    >
                                        + Propriedade
                                    </button>
                                </div>

                                <div class="master-vault-v4-repeat">
                                    <template
                                        x-for="(property, index) in form.properties.custom_properties"
                                        :key="'property-' + index"
                                    >
                                        <div class="master-vault-v4-feature">
                                            <div class="master-vault-v4-feature-head">
                                                <input
                                                    type="text"
                                                    maxlength="120"
                                                    x-model="property.title"
                                                    placeholder="Título"
                                                    class="master-vault-v4-input"
                                                >

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-remove"
                                                    @click="removeCustomProperty(index)"
                                                >
                                                    Remover
                                                </button>
                                            </div>

                                            <div class="master-vault-v4-grid-2 mt-2">
                                                <input
                                                    type="text"
                                                    maxlength="500"
                                                    x-model="property.value"
                                                    placeholder="Valor / resumo"
                                                    class="master-vault-v4-input"
                                                >

                                                <input
                                                    type="text"
                                                    maxlength="2000"
                                                    x-model="property.description"
                                                    placeholder="Descrição"
                                                    class="master-vault-v4-input"
                                                >
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- =====================================================
                                 HABILIDADES — SEÇÃO PERMANENTE
                            ====================================================== --}}
                            <section class="master-vault-v5-abilities">
                                <header class="master-vault-v5-abilities-head">
                                    <div class="master-vault-v5-abilities-head-main">
                                        <span class="master-vault-v5-abilities-mark">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8"
                                                      stroke-width="1.6"
                                                      stroke-linecap="round"/>
                                                <circle cx="12" cy="12" r="3.2" stroke-width="1.6"/>
                                            </svg>
                                        </span>

                                        <div>
                                            <span>Seção permanente</span>
                                            <h3>Habilidades do Item</h3>
                                            <p>
                                                Cada habilidade é uma gaveta. Use as setas para definir
                                                a ordem em que elas serão exibidas.
                                            </p>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="master-vault-v5-add-ability"
                                        @click="addFeature()"
                                    >
                                        <span class="text-base leading-none">+</span>
                                        Adicionar Habilidade
                                    </button>
                                </header>

                                <div class="master-vault-v5-abilities-body">
                                    <div
                                        x-show="form.properties.features.length === 0"
                                        x-cloak
                                        class="master-vault-v5-abilities-empty"
                                    >
                                        Nenhuma habilidade cadastrada. Esta seção existe para qualquer
                                        categoria de item e não depende de Arma, Armadura ou Escudo.
                                    </div>

                                    <template
                                        x-for="(feature, index) in form.properties.features"
                                        :key="'feature-drawer-' + index"
                                    >
                                        <article
                                            class="master-vault-v5-ability-drawer"
                                            :class="{ 'is-open': feature.editor_open }"
                                        >
                                            <div class="master-vault-v5-ability-bar">
                                                <span
                                                    class="master-vault-v5-ability-number"
                                                    x-text="index + 1"
                                                ></span>

                                                <button
                                                    type="button"
                                                    class="master-vault-v5-ability-title"
                                                    @click="toggleFeatureDrawer(feature)"
                                                >
                                                    <strong
                                                        x-text="feature.title?.trim() || ('Habilidade ' + (index + 1))"
                                                    ></strong>

                                                    <span
                                                        x-text="
                                                            feature.usage.enabled
                                                                ? (
                                                                    feature.usage.counter_mode === 'build'
                                                                        ? 'Tracker de acúmulo'
                                                                        : 'Tracker de consumo'
                                                                )
                                                                : 'Habilidade passiva'
                                                        "
                                                    ></span>
                                                </button>

                                                <div class="master-vault-v5-ability-tools">
                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool"
                                                        :disabled="index === 0"
                                                        @click="moveFeature(index, -1)"
                                                        title="Mover para cima"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="m6 14 6-6 6 6"
                                                                  stroke-width="1.8"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool"
                                                        :disabled="index === form.properties.features.length - 1"
                                                        @click="moveFeature(index, 1)"
                                                        title="Mover para baixo"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="m6 10 6 6 6-6"
                                                                  stroke-width="1.8"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool is-danger"
                                                        @click="removeFeature(index)"
                                                        title="Remover habilidade"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M5 7h14M9 7V4h6v3M8 10v7M12 10v7M16 10v7M7 7l1 13h8l1-13"
                                                                  stroke-width="1.55"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool"
                                                        @click="toggleFeatureDrawer(feature)"
                                                        :title="feature.editor_open ? 'Fechar gaveta' : 'Abrir gaveta'"
                                                    >
                                                        <svg
                                                            class="master-vault-v5-ability-chevron"
                                                            :class="{ 'is-open': feature.editor_open }"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                        >
                                                            <path d="m6 9 6 6 6-6"
                                                                  stroke-width="1.8"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div
                                                x-show="feature.editor_open"
                                                x-collapse
                                                class="master-vault-v5-ability-content"
                                            >
                                                <div class="master-vault-v5-ability-content-grid">
                                                    <div>
                                                        <label class="master-vault-v4-field">
                                                            <span>Nome da Habilidade</span>

                                                            <input
                                                                type="text"
                                                                maxlength="120"
                                                                x-model="feature.title"
                                                                placeholder="Ex.: Descarga Arcana"
                                                                class="master-vault-v4-input"
                                                            >
                                                        </label>

                                                        <label class="master-vault-v4-field mt-3 block">
                                                            <span>Descrição</span>

                                                            <textarea
                                                                rows="5"
                                                                maxlength="10000"
                                                                x-model="feature.description"
                                                                placeholder="Descreva efeito, condição, alcance, CD, duração e qualquer regra relevante..."
                                                                class="master-vault-v4-textarea"
                                                            ></textarea>
                                                        </label>
                                                    </div>

                                                    <aside class="master-vault-v5-ability-side">
                                                        <p class="master-vault-v5-ability-side-title">
                                                            Tracker / Contador
                                                        </p>

                                                        <p class="master-vault-v5-ability-side-copy">
                                                            Ative quando a habilidade possuir usos, cargas
                                                            ou um recurso acumulável.
                                                        </p>

                                                        <div class="master-vault-v4-toggle-card mt-3">
                                                            <div>
                                                                <strong>Usar Tracker</strong>
                                                                <small>O contador acompanha o item para o Player.</small>
                                                            </div>

                                                            <button
                                                                type="button"
                                                                class="master-vault-v4-switch"
                                                                :class="{ 'is-on': feature.usage.enabled }"
                                                                @click="setFeatureUsage(feature, !feature.usage.enabled)"
                                                            ></button>
                                                        </div>

                                                        <div
                                                            x-show="feature.usage.enabled"
                                                            x-cloak
                                                            class="mt-3"
                                                        >
                                                            <div class="master-vault-v4-choice-grid">
                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v4-choice"
                                                                    :class="{ 'is-active': feature.usage.counter_mode === 'spend' }"
                                                                    :disabled="feature.usage.recovery === 'single_use'"
                                                                    @click="setFeatureCounterMode(feature, 'spend')"
                                                                >
                                                                    Consumo
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v4-choice"
                                                                    :class="{ 'is-active': feature.usage.counter_mode === 'build' }"
                                                                    :disabled="feature.usage.recovery === 'single_use'"
                                                                    @click="setFeatureCounterMode(feature, 'build')"
                                                                >
                                                                    Acúmulo
                                                                </button>
                                                            </div>

                                                            <div class="master-vault-v4-grid-2 mt-3">
                                                                <label class="master-vault-v4-field">
                                                                    <span>Atual</span>
                                                                    <input
                                                                        type="number"
                                                                        min="0"
                                                                        x-model.number="feature.usage.current"
                                                                        @change="clampFeatureUsage(feature)"
                                                                        class="master-vault-v4-input"
                                                                    >
                                                                </label>

                                                                <label class="master-vault-v4-field">
                                                                    <span>Máximo</span>
                                                                    <input
                                                                        type="number"
                                                                        min="1"
                                                                        x-model.number="feature.usage.max"
                                                                        @change="clampFeatureUsage(feature)"
                                                                        class="master-vault-v4-input"
                                                                    >
                                                                </label>
                                                            </div>

                                                            <label class="master-vault-v4-field mt-3 block">
                                                                <span>Recuperação</span>
                                                                <select
                                                                    x-model="feature.usage.recovery"
                                                                    @change="handleFeatureRecoveryChange(feature)"
                                                                    class="master-vault-v4-select"
                                                                >
                                                                    @foreach($recoveryLabels as $key => $label)
                                                                        <option value="{{ $key }}">
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </label>

                                                            <p
                                                                x-show="feature.usage.recovery === 'single_use'"
                                                                x-cloak
                                                                class="master-vault-v11-single-use-note"
                                                            >
                                                                Uso Único não recupera cargas. Defina <strong>Atual</strong> e <strong>Máximo</strong> normalmente; cada uso confirmado pelo Player reduz 1 carga de forma permanente. O item só fica <strong>Usado</strong> ao chegar a 0.
                                                            </p>

                                                            <label
                                                                x-show="feature.usage.recovery === 'custom'"
                                                                x-cloak
                                                                class="master-vault-v4-field mt-3 block"
                                                            >
                                                                <span>Recuperação Custom</span>
                                                                <input
                                                                    type="text"
                                                                    maxlength="120"
                                                                    x-model="feature.usage.recovery_custom"
                                                                    placeholder="Ex.: Depois de recalibrar o núcleo"
                                                                    class="master-vault-v4-input"
                                                                >
                                                            </label>
                                                        </div>
                                                    </aside>
                                                </div>
                                            </div>
                                        </article>
                                    </template>
                                </div>
                            </section>

                            {{-- =====================================================
                                 CAMADA PRIVADA DO MESTRE
                            ====================================================== --}}
                            <section class="master-vault-v4-secret">
                                <header class="master-vault-v4-secret-head">
                                    <span>Informações Privadas</span>

                                    <h3>Camada do Mestre</h3>
                                </header>

                                <div class="master-vault-v4-secret-body">
                                    <button
                                        x-show="!form.is_cursed"
                                        x-cloak
                                        type="button"
                                        class="master-vault-v11-curse-add"
                                        @click="form.is_cursed = true"
                                    >
                                        + Adicionar maldição
                                    </button>

                                    <div
                                        x-show="form.is_cursed"
                                        x-cloak
                                        class="master-vault-v11-curse-panel"
                                    >
                                        <div class="master-vault-v11-curse-panel-head">
                                            <div>
                                                <strong>Maldição do item</strong>
                                                <small class="block mt-1 text-[9px] text-[#876a53]">Oculta do Player até a revelação.</small>
                                            </div>

                                            <button
                                                type="button"
                                                @click="form.is_cursed = false; form.curse_name = ''; form.curse_description = ''"
                                            >
                                                Remover maldição
                                            </button>
                                        </div>

                                        <label class="master-vault-v4-field">
                                            <span>Nome da Maldição</span>
                                            <input
                                                type="text"
                                                maxlength="160"
                                                x-model="form.curse_name"
                                                placeholder="Ex.: Fome Eterna"
                                                class="master-vault-v4-input"
                                            >
                                        </label>

                                        <label class="master-vault-v4-field">
                                            <span>Descrição Secreta da Maldição</span>
                                            <textarea
                                                maxlength="30000"
                                                x-model="form.curse_description"
                                                placeholder="Somente o Mestre conhece esta descrição até a revelação."
                                                class="master-vault-v4-textarea"
                                            ></textarea>
                                        </label>
                                    </div>

                                    <label class="master-vault-v4-field">
                                        <span>Notas Privadas do Mestre</span>

                                        <textarea
                                            maxlength="30000"
                                            x-model="form.notes"
                                            placeholder="Origem, pistas, CD sugerida, localização, contexto narrativo..."
                                            class="master-vault-v4-textarea"
                                        ></textarea>
                                    </label>
                                </div>
                            </section>

                            <p
                                x-show="saveError"
                                x-cloak
                                class="master-vault-error mt-4"
                                x-text="saveError"
                            ></p>
                        </div>
                    </template>

                    <footer class="master-vault-v4-foot">
                        <button
                            x-show="editingId"
                            x-cloak
                            type="button"
                            class="master-vault-v4-danger"
                            @click="deleteItem()"
                            :disabled="saving"
                        >
                            Excluir do Vault
                        </button>

                        <span x-show="!editingId"></span>

                        <div class="master-vault-v4-foot-actions">
                            <button
                                type="button"
                                class="master-vault-v4-secondary"
                                @click="closeEditor()"
                                :disabled="saving"
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                class="master-vault-v4-primary"
                                @click="saveItem()"
                                :disabled="saving"
                            >
                                <span
                                    x-show="!saving"
                                    x-text="editingId ? 'Salvar Alterações' : 'Criar no Vault'"
                                ></span>

                                <span
                                    x-show="saving"
                                    x-cloak
                                >
                                    Salvando...
                                </span>
                            </button>
                        </div>
                    </footer>
                </section>
            </div>
        </template>

        <template x-if="folderModalOpen">
            <div class="master-vault-v11-modal-layer" @click.self="closeFolderModal()">
                <section class="master-vault-v11-modal" @click.stop>
                    <header class="master-vault-v11-modal-head">
                        <div class="master-vault-v11-modal-headline">
                            <span class="master-vault-v11-modal-headicon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <div>
                                <h2 x-text="folderEditingId ? 'Editar pasta' : 'Nova pasta'"></h2>
                                <p>Organize seus itens e conteúdos</p>
                            </div>
                        </div>

                        <button type="button" class="master-vault-close" @click="closeFolderModal()" :disabled="folderSaving">×</button>
                    </header>

                    <div class="master-vault-v11-modal-body">
                        <div class="master-vault-v11-modal-stack">
                            <label class="master-vault-v4-field">
                                <span class="master-vault-v11-label">Nome da Pasta</span>
                                <input
                                    type="text"
                                    maxlength="120"
                                    x-model="folderForm.name"
                                    placeholder="Ex: Taverna do Dragão, Vilões..."
                                    class="master-vault-v4-input"
                                >
                            </label>

                            <label class="master-vault-v4-field">
                                <span class="master-vault-v11-label">Subtítulo <small>(opcional)</small></span>
                                <textarea
                                    maxlength="10000"
                                    x-model="folderForm.description"
                                    placeholder="Breve descrição sobre esta pasta..."
                                    class="master-vault-v4-textarea"
                                ></textarea>
                            </label>

                            <div>
                                <span class="master-vault-v11-label">Cor de Destaque</span>
                                <div class="master-vault-v11-color-grid">
                                    <template x-for="colorOption in folderPalette" :key="'vault-folder-color-' + colorOption.key">
                                        <button
                                            type="button"
                                            class="master-vault-v11-color-option"
                                            :class="{ 'is-active': String(folderForm.color).toUpperCase() === String(colorOption.color).toUpperCase() }"
                                            :style="{ '--swatch-color': colorOption.color }"
                                            @click="folderForm.color = colorOption.color"
                                        >
                                            <span class="master-vault-v11-color-swatch"></span>
                                            <strong x-text="colorOption.label"></strong>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <p x-show="folderError" x-cloak class="master-vault-error" x-text="folderError"></p>
                    </div>

                    <footer class="master-vault-v11-modal-foot">
                        <button type="button" class="master-vault-v4-secondary" @click="closeFolderModal()" :disabled="folderSaving">Cancelar</button>

                        <button type="button" class="master-vault-v4-primary" @click="saveFolder()" :disabled="folderSaving" x-text="folderSaving ? 'Salvando...' : (folderEditingId ? 'Salvar pasta' : 'Criar pasta')"></button>
                    </footer>
                </section>
            </div>
        </template>

        <div x-show="toastOpen" x-cloak class="master-vault-toast" :class="{ 'is-error': toastError }" x-transition.opacity.duration.120ms x-text="toastMessage"></div>
    </div>

</x-app-layout>