<x-app-layout>
    @php
        // Mesma coleção de pastas usada no Cofre: o menu "Enviar para pasta"
        // continua disponível também dentro de uma pasta.
        $availableFolders = $folder->newQuery()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    @endphp

    <style>
        /* V66.0.65 — Visual interno das pastas: composição editorial, sem banner. */
        html:has(.npc-folder-page-scope) {
            scrollbar-width: thin;
            scrollbar-color: #a88b6a #efe9dc;
        }
        html:has(.npc-folder-page-scope)::-webkit-scrollbar { width: 11px; }
        html:has(.npc-folder-page-scope)::-webkit-scrollbar-track { background: #efe9dc; }
        html:has(.npc-folder-page-scope)::-webkit-scrollbar-thumb {
            background: #ac8d6e;
            border: 3px solid #efe9dc;
            border-radius: 999px;
        }
        html:has(.npc-folder-page-scope)::-webkit-scrollbar-thumb:hover { background: #875736; }

        .npc-folder-page-scope {
            --folder-ink: #562119;
            --folder-muted: #8c6239;
            max-width: 1450px;
            margin-inline: auto;
            padding: 29px 26px 54px;
            min-height: 60vh;
        }

        /* O caminho de volta faz parte do cabeçalho; não é outro "card". */
        .npc-folder-path {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
            color: #947658;
            font: 600 12px/1.25 ui-sans-serif, system-ui, sans-serif;
        }
        .npc-folder-path__back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 0;
            color: #795238;
            text-decoration: none;
            transition: color .18s ease;
        }
        .npc-folder-path__back:hover,
        .npc-folder-path__back:focus-visible {
            color: #6b1d14;
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        .npc-folder-path__back svg { transition: transform .18s ease; }
        .npc-folder-path__back:hover svg { transform: translateX(-3px); }
        .npc-folder-path__divider { color: #c9bba7; user-select: none; }
        .npc-folder-path__current {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #aa8563;
        }

        /* Título principal: sem retângulo atrás, sem retratos repetidos. */
        .npc-folder-masthead {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
            padding: 30px 0 27px;
        }
        .npc-folder-masthead__identity {
            position: relative;
            min-width: 0;
            flex: 1;
            padding-left: 22px;
        }
        .npc-folder-masthead__identity::before {
            content: '';
            position: absolute;
            top: 2px;
            bottom: 2px;
            left: 0;
            width: 4px;
            min-height: 62px;
            border-radius: 999px;
            background: var(--folder-accent, #8c6239);
            box-shadow: 0 0 0 4px rgba(255,255,255,.17);
        }
        .npc-folder-masthead__overline {
            margin: 0 0 8px;
            color: #a78260;
            font: 700 10px/1.3 ui-sans-serif, system-ui, sans-serif;
            letter-spacing: .19em;
            text-transform: uppercase;
        }
        .npc-folder-masthead__title {
            margin: 0;
            color: var(--folder-ink);
            font: 700 clamp(30px, 3vw, 43px)/1.07 Georgia, 'Times New Roman', serif;
            letter-spacing: -.027em;
            overflow-wrap: anywhere;
        }
        .npc-folder-masthead__subtitle {
            margin: 11px 0 0;
            max-width: 830px;
            color: #8d6746;
            font: italic 14px/1.6 ui-sans-serif, system-ui, sans-serif;
            overflow-wrap: anywhere;
        }
        .npc-folder-masthead__count {
            display: flex;
            align-items: baseline;
            gap: 8px;
            flex-shrink: 0;
            padding: 8px 0 8px 25px;
            border-left: 1px solid #d9cbb7;
            white-space: nowrap;
        }
        .npc-folder-masthead__count strong {
            color: #6b1d14;
            font: 700 33px/1 Georgia, 'Times New Roman', serif;
            font-variant-numeric: tabular-nums;
        }
        .npc-folder-masthead__count span {
            color: #8c6239;
            font: 700 11px/1 ui-sans-serif, system-ui, sans-serif;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        /* Uma linha de acento, não uma segunda seção de metadados. */
        .npc-folder-rule {
            position: relative;
            height: 1px;
            margin-bottom: 24px;
            background: #d9c9b3;
        }
        .npc-folder-rule::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: min(112px, 22%);
            height: 3px;
            border-radius: 999px;
            background: var(--folder-accent, #8c6239);
        }

        /* Mesmo componente ilustrado compartilhado com a Biblioteca. */
        .npc-folder-page-scope .npc-folder-card-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 21px;
            align-items: stretch;
        }

        .npc-folder-empty {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 13px;
            min-height: 270px;
            padding: 40px 22px;
            border: 1px dashed #cdbb9f;
            border-radius: 20px;
            background: rgba(255, 251, 244, .47);
            text-align: center;
        }
        .npc-folder-empty__icon {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 17px;
            background: rgba(140,98,57,.09);
            color: #8c6239;
        }
        .npc-folder-empty h2 {
            margin: 0;
            color: #6b1d14;
            font: 700 20px/1.25 Georgia, serif;
        }
        .npc-folder-empty p {
            margin: 0;
            color: #967454;
            font: 13px/1.5 ui-sans-serif, system-ui, sans-serif;
        }

        @media (max-width: 1180px) {
            .npc-folder-page-scope .npc-folder-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 680px) {
            .npc-folder-page-scope { padding: 20px 15px 38px; }
            .npc-folder-masthead { align-items: flex-start; gap: 12px; padding: 22px 0 22px; }
            .npc-folder-masthead__identity { padding-left: 16px; }
            .npc-folder-masthead__identity::before { min-height: 46px; }
            .npc-folder-masthead__title { font-size: clamp(26px, 6vw, 32px); }
            .npc-folder-masthead__subtitle { font-size: 12px; margin-top: 8px; }
            .npc-folder-masthead__count { gap: 4px; padding: 8px 0 8px 12px; }
            .npc-folder-masthead__count strong { font-size: 26px; }
            .npc-folder-masthead__count span { font-size: 9px; }
            .npc-folder-rule { margin-bottom: 17px; }
            .npc-folder-page-scope .npc-folder-card-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 16px;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .npc-folder-path__back,
            .npc-folder-path__back svg { transition: none; }
        }
    </style>

    <div class="npc-folder-page-scope" style="--folder-accent: {{ $folder->color }};">
        <nav class="npc-folder-path" aria-label="Navegação do Cofre">
            <a href="{{ route('npcs.index') }}" class="npc-folder-path__back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Cofre de NPCs
            </a>
            <span class="npc-folder-path__divider" aria-hidden="true">/</span>
            <span class="npc-folder-path__current" aria-current="page">{{ $folder->name }}</span>
        </nav>

        <header class="npc-folder-masthead">
            <div class="npc-folder-masthead__identity">
                <p class="npc-folder-masthead__overline">Pasta</p>
                <h1 class="npc-folder-masthead__title">{{ $folder->name }}</h1>
                @if($folder->subtitle)
                    <p class="npc-folder-masthead__subtitle">{{ $folder->subtitle }}</p>
                @endif
            </div>
            <div class="npc-folder-masthead__count" aria-label="{{ $folder->npcs->count() }} NPCs nesta pasta">
                <strong>{{ $folder->npcs->count() }}</strong>
                <span>NPCs</span>
            </div>
        </header>

        <div class="npc-folder-rule" aria-hidden="true"></div>

        <section class="npc-folder-card-grid" aria-label="NPCs da pasta {{ $folder->name }}">
            @forelse($folder->npcs as $npc)
                @include('npcs.npc-card', ['npc' => $npc])
            @empty
                <div class="npc-folder-empty">
                    <div class="npc-folder-empty__icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-7l-2-3H5a2 2 0 0 0-2 2v1Z" />
                            <path d="M12 11v6m-3-3h6" />
                        </svg>
                    </div>
                    <h2>Nenhum NPC nesta pasta</h2>
                    <p>Use “Enviar para pasta” no menu de um NPC para adicioná-lo aqui.</p>
                </div>
            @endforelse
        </section>
    </div>

    @include('npcs.partials.move-folder-modal', ['availableFolders' => $availableFolders])
    @include('npcs.partials.edit-npc-modal')
</x-app-layout>
