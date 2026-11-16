<x-app-layout>
    @php
        $user = auth()->user();
        $activeMode = in_array($mode ?? null, ['master', 'player'], true) ? $mode : null;
        $requestedModes = array_values(array_intersect((array) ($intendedModes ?? []), ['master', 'player']));
        $accountName = $user?->name ?: 'Aventureiro';
        $accountEmail = $user?->email ?: '';
        $userInitial = mb_strtoupper(mb_substr($accountName, 0, 1));
        $profilePhoto = data_get($user, 'profile_photo_url')
            ?: data_get($user, 'avatar_url')
            ?: data_get($user, 'photo_url');
        if (! $profilePhoto && data_get($user, 'profile_photo_path')) {
            $profilePhoto = \Illuminate\Support\Facades\Storage::disk('public')->url(data_get($user, 'profile_photo_path'));
        }
    @endphp

    <style>
        /* Spellbound V66.0.83 — hub com escolha de modo explícita e retorno opcional. */
        main:has(.sb-hub82), .py-12:has(.sb-hub82),
        .max-w-7xl:has(.sb-hub82), .sm\:px-6:has(.sb-hub82),
        .lg\:px-8:has(.sb-hub82) {
            max-width: none !important; width: 100% !important;
            padding: 0 !important; margin: 0 !important; min-height: 0 !important;
        }
        html:has(.sb-hub82), body:has(.sb-hub82) {
            scrollbar-color: #b89b77 #eee7dc; scrollbar-width: thin;
        }
        html:has(.sb-hub82)::-webkit-scrollbar,
        body:has(.sb-hub82)::-webkit-scrollbar { width: 10px; }
        html:has(.sb-hub82)::-webkit-scrollbar-track,
        body:has(.sb-hub82)::-webkit-scrollbar-track { background: #eee7dc; }
        html:has(.sb-hub82)::-webkit-scrollbar-thumb,
        body:has(.sb-hub82)::-webkit-scrollbar-thumb {
            background: #b89b77; border-radius: 20px; border: 2px solid #eee7dc;
        }
        .sb-hub82 {
            --sb-ink: #4c261c; --sb-wine: #6b2218; --sb-muted: #987455;
            --sb-gold: #a97a39; --sb-gold-light: #dbbd83; --sb-line: #ddcfbc;
            box-sizing: border-box; width: 100%; min-height: 100dvh;
            display: flex; justify-content: center; align-items: center;
            padding: clamp(18px, 3.5vw, 48px);
            background: radial-gradient(ellipse at 15% 12%, rgba(132,83,48,.075), transparent 36%),
                        radial-gradient(ellipse at 92% 90%, rgba(149,111,73,.06), transparent 36%), #eee8dd;
            color: var(--sb-ink);
        }
        .sb-hub82 *, .sb-hub82 *::before, .sb-hub82 *::after { box-sizing: border-box; }
        .sb-hub82__inner { width: min(100%, 1400px); margin: auto; }
        .sb-hub82__header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 22px; margin-bottom: clamp(18px, 3vh, 32px);
            padding: 0 4px clamp(18px, 2.4vh, 25px);
            border-bottom: 1px solid #d9c8b4;
        }
        .sb-hub82__brand { display: flex; align-items: center; gap: 16px; min-width: 0; }
        .sb-hub82__seal {
            display: grid; place-items: center; width: 60px; height: 60px;
            flex: 0 0 60px; border-radius: 17px;
            color: #fff8ee; background: linear-gradient(150deg, #7e3026, #551910);
            box-shadow: 0 8px 20px rgba(76,31,21,.13);
            font: 700 30px/1 Georgia, serif;
        }
        .sb-hub82__brand h1 { color: var(--sb-wine); font: 700 clamp(29px, 3vw, 42px)/1.05 Georgia, serif; letter-spacing: -.035em; }
        .sb-hub82__brand p { margin-top: 6px; color: var(--sb-muted); font-size: 12px; }
        .sb-hub82__account { position: relative; flex-shrink: 0; max-width: 360px; z-index: 30; }
        .sb-hub82__account > summary {
            display: flex; align-items: center; gap: 13px;
            padding: 8px 12px 8px 8px; border: 1px solid #dfd1be;
            border-radius: 18px; background: rgba(255,251,244,.88);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
            cursor: pointer; list-style: none;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        .sb-hub82__account > summary::-webkit-details-marker { display: none; }
        .sb-hub82__account > summary:hover,
        .sb-hub82__account > summary:focus-visible {
            border-color: #c5a77d; box-shadow: 0 6px 16px rgba(79,46,28,.07);
        }
        .sb-hub82__avatar {
            position: relative; display: grid; place-items: center;
            width: 51px; height: 51px; flex: 0 0 51px; overflow: hidden;
            border-radius: 13px; background: #6b2218; color: #fff8ee;
            font: 700 23px/1 Georgia,serif;
        }
        .sb-hub82__avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .sb-hub82__identity { min-width: 0; text-align: left; }
        .sb-hub82__identity strong { display: block; color: #60251b; font: 700 18px/1.14 Georgia,serif; overflow-wrap:anywhere; }
        .sb-hub82__identity small { display: block; max-width: 225px; margin-top: 4px; color: #9a7959; font-size: 11px; overflow-wrap: anywhere; }
        .sb-hub82__chevron { width: 17px; height: 17px; color: #947355; flex-shrink: 0; transition: transform .18s ease; }
        .sb-hub82__account[open] .sb-hub82__chevron { transform: rotate(180deg); }
        .sb-hub82__account-menu {
            position: absolute; top: calc(100% + 9px); right: 0; width: 235px;
            padding: 8px; border: 1px solid #d6c4aa; border-radius: 16px;
            background: #fffaf3; box-shadow: 0 16px 34px rgba(63,36,20,.15);
        }
        .sb-hub82__account-menu a {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; padding: 12px 13px; border-radius: 10px;
            color: #6b2218; font-size: 13px; font-weight: 700; text-decoration: none;
        }
        .sb-hub82__account-menu a:hover, .sb-hub82__account-menu a:focus-visible { background: #f2e7d7; }
        .sb-hub82__notice {
            margin: -5px 0 18px; padding: 11px 15px; border: 1px solid #dac7ab;
            border-radius: 14px; background: #fff8ed; color: #80583d; font-size: 12px;
        }
        .sb-hub82__modes { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: clamp(16px,2vw,26px); }
        .sb-hub82__modes form { display: flex; min-width: 0; margin: 0; }
        .sb-hub82__mode {
            position: relative; isolation: isolate; display: flex; flex-direction: column; text-align: left;
            width: 100%; min-width: 0; min-height: clamp(300px, 39vh, 388px);
            padding: clamp(24px,2.65vw,38px); overflow: hidden; cursor: pointer;
            border: 1px solid #dbcab4; border-radius: 27px;
            background: linear-gradient(145deg, #fffdf8 0%, #f5eddf 100%);
            box-shadow: 0 15px 35px rgba(72,44,26,.075), inset 0 1px 0 #fffefa;
            color: var(--sb-ink);
            transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease;
        }
        .sb-hub82__mode::before {
            content:""; position:absolute; top:0; left:23px; right:23px; height:3px;
            background:#a27e59; border-radius:0 0 5px 5px; opacity:.5;
        }
        .sb-hub82__mode.is-active {
            border-color: #bd995d;
            background: linear-gradient(145deg, #fffdf7 0%, #f5ede0 78%, #eee2ce 100%);
            animation: sb-hub82-active 4s ease-in-out infinite;
        }
        .sb-hub82__mode.is-active::before { height: 4px; opacity: 1; background: linear-gradient(90deg, #a57534, #e1c992, #a57534); }
        @keyframes sb-hub82-active {
            0%,100% { box-shadow: 0 0 0 1px rgba(178,132,58,.2),0 17px 36px rgba(72,44,26,.09); }
            50% { box-shadow: 0 0 0 3px rgba(178,132,58,.12),0 21px 40px rgba(72,44,26,.12); }
        }
        .sb-hub82__mode:hover, .sb-hub82__mode:focus-visible {
            transform: translateY(-4px); border-color: #a98555;
            box-shadow: 0 22px 42px rgba(72,44,26,.13), inset 0 1px 0 #fffefa;
        }
        .sb-hub82__mode.is-active:hover, .sb-hub82__mode.is-active:focus-visible { animation-play-state: paused; }
        .sb-hub82__mode:focus-visible { outline: 3px solid #c4a36e; outline-offset: 4px; }
        .sb-hub82__mode-head { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .sb-hub82__eyebrow { color: #957250; font-size: 10px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        .sb-hub82__active-badge {
            display: inline-flex; align-items: center; gap: 9px; border-radius: 999px;
            border: 1px solid #d9b77d; padding: 8px 12px; background: #fff5e2;
            color: #79501f; font: 700 11px/1 ui-sans-serif,system-ui,sans-serif;
        }
        .sb-hub82__active-dot { display:block; width:8px; height:8px; border-radius:50%; background:#af7c2f; animation:sb-hub82-dot 2.4s ease-out infinite; }
        @keyframes sb-hub82-dot { 0%,100% { box-shadow:0 0 0 0 rgba(174,126,52,.25); } 50% { box-shadow:0 0 0 6px rgba(174,126,52,0); } }
        .sb-hub82__mode-art {
            position: absolute; width: clamp(130px, 14vw, 230px); height: clamp(130px, 14vw, 230px);
            top: 20%; right: clamp(14px,2.5vw,30px); z-index: 1;
            opacity: .15; pointer-events: none; object-fit: contain; filter: grayscale(1);
            transition: opacity .4s ease, transform .6s cubic-bezier(.2,.8,.2,1);
            transform-origin: center;
        }
        .sb-hub82__mode.is-active .sb-hub82__mode-art { opacity: .18; }
        .sb-hub82__mode:hover .sb-hub82__mode-art, .sb-hub82__mode:focus-visible .sb-hub82__mode-art { opacity: .25; }
        .sb-hub82__mode--player:hover .sb-hub82__mode-art, .sb-hub82__mode--player:focus-visible .sb-hub82__mode-art { transform: rotate(14deg) scale(1.045); }
        .sb-hub82__mode--master:hover .sb-hub82__mode-art, .sb-hub82__mode--master:focus-visible .sb-hub82__mode-art { transform: translateY(-6px) rotate(-2deg) scale(1.045); }
        .sb-hub82__mode-body { display: block; margin-top: auto; max-width: 69%; position: relative; z-index: 2; }
        .sb-hub82__mode-title { display:block; font: 700 clamp(44px,4.3vw,61px)/1.02 Georgia,serif; letter-spacing:-.045em; color:var(--sb-wine); }
        .sb-hub82__mode-desc { display:block; margin-top:12px; color:#8d6c4e; font-size:clamp(12px,1vw,14px); line-height:1.58; }
        .sb-hub82__mode-footer {
            position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 12px;
            width: 100%; margin-top: clamp(20px,3.6vh,32px); padding-top: 17px; border-top: 1px solid #e0d0bb;
            color:var(--sb-wine); font: 700 14px/1.2 Georgia,serif;
        }
        .sb-hub82__arrow { display:grid; place-items:center; width:36px; height:36px; border-radius:11px; background:#f0e3d3; transition:transform .2s ease,background .2s ease; flex-shrink:0; }
        .sb-hub82__arrow svg { width:18px; height:18px; }
        .sb-hub82__mode:hover .sb-hub82__arrow { transform:translateX(4px); background:#ead8c3; }
        .sb-hub82__account-menu a.sb-hub83__recent {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 12px 13px;
            border-top: 1px solid #ebdfce;
            color: #6b2218;
            font-size: 12px;
            line-height: 1.35;
        }
        .sb-hub82__account-menu a.sb-hub83__recent.is-ready { display: flex; }
        .sb-hub83__recent-label { display: block; font-weight: 750; }
        .sb-hub83__recent-sub { display: block; margin-top: 3px; font-size: 10px; color: #987455; }
        .sb-hub82__account-menu a.sb-hub83__recent:hover { background:#f1e7d8; }
        @media (prefers-reduced-motion: reduce) {
            .sb-hub82__active-dot, .sb-hub82__mode.is-active { animation: none !important; }
        }
        @media(min-width:1600px) {
            .sb-hub82__inner { width:min(100%,1540px); }
            .sb-hub82__mode { min-height:clamp(335px,36vh,425px); }
            .sb-hub82__mode-art { width:clamp(200px,14vw,245px); height:clamp(200px,14vw,245px); }
        }
        @media(max-width:920px) {
            .sb-hub82__mode-art { width:140px; height:140px; top:23%; right:12px; }
            .sb-hub82__mode-body { max-width:62%; }
            .sb-hub82__mode-title { font-size:42px; }
        }
        @media(max-width:740px) {
            .sb-hub82 { align-items:flex-start; padding:18px 14px 28px; }
            .sb-hub82__header { align-items:flex-start; gap:14px; flex-direction:column; margin-bottom:18px; }
            .sb-hub82__account { max-width:100%; width:100%; }
            .sb-hub82__account > summary { width:100%; }
            .sb-hub82__identity { flex:1; }
            .sb-hub82__identity small { max-width:100%; }
            .sb-hub82__account-menu { left:0; right:auto; }
            .sb-hub82__modes { grid-template-columns:minmax(0,1fr); gap:14px; }
            .sb-hub82__mode { min-height:224px; padding:20px; border-radius:22px; }
            .sb-hub82__mode-art { width:134px; height:134px; top:14%; right:15px; }
            .sb-hub82__mode-title { font-size:39px; }
            .sb-hub82__mode-body { max-width:66%; }
            .sb-hub82__mode-desc { font-size:12px; margin-top:8px; }
            .sb-hub82__mode-footer { margin-top:14px; padding-top:12px; }
        }
        @media(max-width:390px) {
            .sb-hub82 { padding:12px 10px 20px; }
            .sb-hub82__brand { gap:10px; }
            .sb-hub82__seal { width:48px; height:48px; flex-basis:48px; border-radius:13px; font-size:24px; }
            .sb-hub82__brand h1 { font-size:27px; }
            .sb-hub82__account > summary { padding:7px; }
            .sb-hub82__mode { padding:17px; min-height:215px; }
            .sb-hub82__mode-title { font-size:35px; }
            .sb-hub82__mode-art { width:103px; height:103px; top:19%; right:8px; }
            .sb-hub82__mode-desc { font-size:11px; }
            .sb-hub82__mode-footer { font-size:12px; }
        }
        @media(max-height:690px) and (min-width:741px) {
            .sb-hub82 { padding-block:12px; }
            .sb-hub82__header { margin-bottom:15px; padding-bottom:12px; }
            .sb-hub82__mode { min-height:245px; }
        }
        @media(prefers-reduced-motion:reduce) {
            .sb-hub82__mode, .sb-hub82__mode-art, .sb-hub82__arrow,
            .sb-hub82__active-dot { animation:none !important; transition:none !important; }
            .sb-hub82__mode:hover, .sb-hub82__mode-art { transform:none !important; }
        }
    </style>

    <div class="sb-hub82" data-spellbound-hub="true" data-spellbound-user="{{ (string) $user?->getAuthIdentifier() }}" data-spellbound-mode="{{ $activeMode ?: '' }}">
        <div class="sb-hub82__inner">
            <header class="sb-hub82__header">
                <div class="sb-hub82__brand">
                    <div class="sb-hub82__seal" aria-hidden="true">S</div>
                    <div>
                        <h1>Spellbound</h1>
                        <p>Escolha o seu espaço de trabalho.</p>
                    </div>
                </div>
                <details class="sb-hub82__account">
                    <summary aria-label="Abrir opções da conta">
                        <span class="sb-hub82__avatar">
                            <span aria-hidden="true">{{ $userInitial }}</span>
                            @if($profilePhoto)
                                <img src="{{ $profilePhoto }}" alt="" x-on:error="$el.style.display='none'">
                            @endif
                        </span>
                        <span class="sb-hub82__identity">
                            <strong>{{ $accountName }}</strong>
                            @if($accountEmail)<small>{{ $accountEmail }}</small>@endif
                        </span>
                        <svg class="sb-hub82__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="sb-hub82__account-menu">
                        <a href="{{ route('profile.edit') }}">Meu perfil</a>
                        <a href="#" class="sb-hub83__recent" data-spellbound-resume hidden>
                            <svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 12a9 9 0 1 0 2-5.7L3 8m0-5v5h5"/><path d="M12 7v5l3 2"/></svg>
                            <span><span class="sb-hub83__recent-label">Retomar última página</span><span class="sb-hub83__recent-sub" data-spellbound-resume-label></span></span>
                        </a>
                    </div>
                </details>
            </header>

            @if(session('mode_required'))
                <div class="sb-hub82__notice" role="status">{{ session('mode_required') }}</div>
            @endif

            <section class="sb-hub82__modes" aria-label="Selecionar Mestre ou Player">
                <form method="POST" action="{{ route('dashboard.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="master">
                    <button type="submit" class="sb-hub82__mode sb-hub82__mode--master {{ $activeMode === 'master' ? 'is-active' : '' }}" aria-label="{{ $activeMode === 'master' ? 'Continuar como Mestre' : 'Entrar como Mestre' }}">
                        <span class="sb-hub82__mode-head">
                            <span class="sb-hub82__eyebrow">Modo Mestre</span>
                            @if($activeMode === 'master')
                                <span class="sb-hub82__active-badge"><span class="sb-hub82__active-dot" aria-hidden="true"></span>Em uso</span>
                            @elseif(in_array('master', $requestedModes, true) && ! in_array('player', $requestedModes, true))
                                <span class="sb-hub82__eyebrow">Necessário</span>
                            @endif
                        </span>
                        <img class="sb-hub82__mode-art" src="{{ asset('images/dashboard/mestre.png') }}" alt="" aria-hidden="true" width="768" height="768" decoding="async">
                        <span class="sb-hub82__mode-body">
                            <span class="sb-hub82__mode-title">Mestre</span>
                            <span class="sb-hub82__mode-desc">Campanhas, combates e ferramentas da mesa.</span>
                        </span>
                        <span class="sb-hub82__mode-footer">
                            {{ $activeMode === 'master' ? 'Continuar como Mestre' : ($activeMode === 'player' ? 'Trocar para Mestre' : 'Entrar como Mestre') }}
                            <span class="sb-hub82__arrow" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
                        </span>
                    </button>
                </form>

                <form method="POST" action="{{ route('dashboard.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="player">
                    <button type="submit" class="sb-hub82__mode sb-hub82__mode--player {{ $activeMode === 'player' ? 'is-active' : '' }}" aria-label="{{ $activeMode === 'player' ? 'Continuar como Player' : 'Entrar como Player' }}">
                        <span class="sb-hub82__mode-head">
                            <span class="sb-hub82__eyebrow">Modo Player</span>
                            @if($activeMode === 'player')
                                <span class="sb-hub82__active-badge"><span class="sb-hub82__active-dot" aria-hidden="true"></span>Em uso</span>
                            @elseif(in_array('player', $requestedModes, true) && ! in_array('master', $requestedModes, true))
                                <span class="sb-hub82__eyebrow">Necessário</span>
                            @endif
                        </span>
                        <img class="sb-hub82__mode-art" src="{{ asset('images/dashboard/player.png') }}" alt="" aria-hidden="true" width="768" height="768" decoding="async">
                        <span class="sb-hub82__mode-body">
                            <span class="sb-hub82__mode-title">Player</span>
                            <span class="sb-hub82__mode-desc">Suas fichas, personagens e campanhas.</span>
                        </span>
                        <span class="sb-hub82__mode-footer">
                            {{ $activeMode === 'player' ? 'Continuar como Player' : ($activeMode === 'master' ? 'Trocar para Player' : 'Entrar como Player') }}
                            <span class="sb-hub82__arrow" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
                        </span>
                    </button>
                </form>
            </section>


        </div>
    </div>
    <script src="{{ asset('js/spellbound-navigation-history.js') }}" defer></script>
</x-app-layout>
