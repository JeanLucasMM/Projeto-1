<style>
    .sb-nav83 { width: 56px; min-width: 56px; background: #f7f3eb; border-right: 1px solid rgba(105,55,35,.12); z-index:40; }
    .sb-nav83 * { box-sizing: border-box; }
    .sb-nav83__top { flex:0 0 auto; padding: 11px 8px; display:flex; justify-content:center; border-bottom:1px solid rgba(105,55,35,.10); }
    .sb-nav83__logo { width:32px; height:32px; border-radius:10px; display:grid;place-items:center; background:#6b2119; color:#fff8ef; font:700 18px/1 Georgia,serif; text-decoration:none; }
    .sb-nav83__logo:hover { background:#85362b; }
    .sb-nav83__logo--active { box-shadow:inset 0 0 0 2px #d9bfa4,0 2px 8px rgba(107,33,25,.12); }
    .sb-nav83__menu { flex:1 1 auto; min-height:0; overflow:visible; padding:14px 7px; display:flex; flex-direction:column; align-items:center; gap:6px; scrollbar-width:none; }
    .sb-nav83__menu::-webkit-scrollbar { display:none; }
    .sb-nav83__link { width:40px; height:40px; min-height:40px; flex:0 0 40px; margin:0; padding:0; position:relative; border:0; border-radius:12px; background:transparent; display:flex; justify-content:center; align-items:center; color:#987959; cursor:pointer; text-decoration:none; transition:color .18s ease,background .18s ease,transform .18s ease; }
    .sb-nav83__link svg { width:21px; height:21px; flex:0 0 21px; stroke-width:2.05; transition:transform .18s ease; }
    .sb-nav83__link:hover svg { transform:scale(1.055); }
    /* Ícones PNG padronizados em dimensões, traço e proporção visual. A máscara acompanha currentColor. */
    .sb-nav83__image-icon {
        --sb-icon-shift-y: 0px;
        --sb-icon-scale: 1;
        display: block;
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
        background-color: currentColor;
        -webkit-mask-image: var(--sb-nav-img);
        mask-image: var(--sb-nav-img);
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-position: center;
        mask-position: center;
        -webkit-mask-size: contain;
        mask-size: contain;
        transform: translateY(var(--sb-icon-shift-y)) scale(var(--sb-icon-scale));
        transition: transform .18s ease;
    }
    .sb-nav83__link:hover .sb-nav83__image-icon {
        transform: translateY(var(--sb-icon-shift-y)) scale(calc(var(--sb-icon-scale) * 1.065));
    }
    .sb-nav83__image-icon--construtor {
        --sb-nav-img: url('{{ asset('images/navigation/construtor.png') }}?v=91');
    }
    .sb-nav83__image-icon--npcs { --sb-nav-img: url('{{ asset('images/navigation/npcs.png') }}?v=88'); }
    .sb-nav83__image-icon--vault { --sb-nav-img: url('{{ asset('images/navigation/vault.png') }}?v=88'); }
    .sb-nav83__image-icon--combates { --sb-nav-img: url('{{ asset('images/navigation/combates.png') }}?v=88'); }
    .sb-nav83__image-icon--campanhas { --sb-nav-img: url('{{ asset('images/navigation/campanhas.png') }}?v=88'); }

    .sb-nav83__link:hover,.sb-nav83__link:focus-visible { color:#6b2119; background:rgba(107,33,25,.07); }
    .sb-nav83__link--active { color:#6b2119 !important; background:rgba(107,33,25,.11) !important; }
    .sb-nav83__link--active::before { content:''; position:absolute; left:-7px; top:11px; bottom:11px; width:3px; border-radius:0 4px 4px 0; background:#8c4b3a; }
    .sb-nav83__link:focus-visible { outline:2px solid #b08a61; outline-offset:1px; }
    .sb-nav83__tooltip { opacity:0; pointer-events:none; position:absolute; z-index:100; left:52px; top:50%; transform:translateY(-50%) translateX(-3px); background:#fffaf2; border:1px solid #e1d1bd; border-radius:9px; padding:8px 11px; white-space:nowrap; box-shadow:0 7px 20px rgba(48,27,18,.11); color:#6b2119; font:700 11px/1.2 Georgia,serif; transition:opacity .16s ease,transform .16s ease; }
    .sb-nav83__link:hover .sb-nav83__tooltip,.sb-nav83__link:focus-visible .sb-nav83__tooltip { opacity:1;transform:translateY(-50%) translateX(0); }
    .sb-nav83__footer { border-top:1px solid rgba(105,55,35,.10); flex:0 0 auto; padding:9px 7px; display:flex; flex-direction:column; gap:5px; align-items:center; }
    .sb-nav83__footer form { display:flex; justify-content:center; }
    @media (max-height:480px) { .sb-nav83 { overflow-y:auto; scrollbar-width:thin; } .sb-nav83__menu { flex:0 0 auto; } }
    @media (prefers-reduced-motion:reduce) { .sb-nav83__link,.sb-nav83__tooltip { transition:none; } }
</style>
<aside class="sb-nav83 player-sidebar h-full flex flex-col flex-shrink-0 select-none relative"
    aria-label="Navegação do jogador"
    data-sb-user="{{ (string) auth()->id() }}"
    data-sb-mode="player"
    data-sb-route="{{ request()->route()?->getName() ?? '' }}">
    {{-- Navegação lateral leve: os recursos têm a mesma rota e permissões anteriores. --}}
    <div class="sb-nav83__top player-sidebar__top">
        <a href="{{ route('dashboard') }}" class="sb-nav83__logo {{ request()->routeIs('dashboard') ? 'sb-nav83__logo--active' : '' }} player-sidebar__logo" title="Dashboard" aria-label="Ir para o Dashboard" @if(request()->routeIs('dashboard')) aria-current="page" @endif>S</a>
    </div>
    <nav class="sb-nav83__menu" aria-label="Seções">
            @php $sbActive = request()->routeIs('characters.*'); @endphp
            <a href="{{ route('characters.index') }}"
               aria-label="Personagens" title="Personagens"
               class="sb-nav83__link player-sidebar__link {{ $sbActive ? 'sb-nav83__link--active player-sidebar__link--active' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"><circle cx="10" cy="7" r="4"/><path d="M3 21a7 7 0 0 1 14 0M20 11v8m-4-4h8"/></svg>
                <span class="sb-nav83__tooltip">Personagens</span>
            </a>
            @php $sbActive = request()->routeIs('campaigns.*') || request()->routeIs('campaign-invitations.*'); @endphp
            <a href="{{ route('campaigns.index') }}"
               aria-label="Campanhas" title="Campanhas"
               class="sb-nav83__link player-sidebar__link {{ $sbActive ? 'sb-nav83__link--active player-sidebar__link--active' : '' }}">
                <span class="sb-nav83__image-icon sb-nav83__image-icon--campanhas" aria-hidden="true"></span>
                <span class="sb-nav83__tooltip">Campanhas</span>
            </a>
    </nav>
    <div class="sb-nav83__footer player-sidebar__footer">
        {{-- Trocar de modo é uma escolha explícita no hub, não um POST invisível. --}}
        <a href="{{ route('dashboard') }}" aria-label="Escolher modo Mestre ou Player" title="Escolher modo"
           class="sb-nav83__link player-sidebar__link">
            <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"><path d="M20 7H4m0 0 4-4M4 7l4 4M4 17h16m0 0-4-4m4 4-4 4"/></svg>
            <span class="sb-nav83__tooltip">Escolher modo</span>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="m-0 w-full">
            @csrf
            <button class="sb-nav83__link player-sidebar__link" type="submit" title="Sair" aria-label="Sair da conta">
                <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"><path d="M15 17l5-5-5-5M20 12H9"/><path d="M13 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7"/></svg>
                <span class="sb-nav83__tooltip">Sair</span>
            </button>
        </form>
    </div>
</aside>
<script src="{{ asset('js/spellbound-navigation-history.js') }}" defer></script>
