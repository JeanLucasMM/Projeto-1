{{-- V66 — Condições de combate sincronizadas com a iniciativa do Mestre. --}}
@once
    @push('styles')
        <style>
            /* V66.0.36 — Tokens dos oito temas da ficha; valores de fallback Spellbound. */
            .sb-condition-bar {
                --sb-condition-surface: var(--theme-paper-strong, #fffaf2);
                --sb-condition-surface-alt: var(--theme-paper-alt, #f8f0e6);
                --sb-condition-hover: var(--theme-hover, #f5ebdd);
                --sb-condition-ink: var(--theme-text, #4d2922);
                --sb-condition-muted: var(--theme-muted, #916b45);
                --sb-condition-title: var(--theme-title, #681b15);
                --sb-condition-accent: var(--theme-accent, #93623d);
                --sb-condition-border: var(--theme-border, #d8c7ab);
                --sb-condition-border-strong: var(--theme-border-strong, #b79165);
                --sb-condition-danger: var(--theme-state-damage, #a22222);
                position: relative;
                z-index: 48;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 7px;
                margin: 0 0 10px;
                isolation: isolate;
                color: var(--sb-condition-ink);
                font-family: var(--theme-font-body, Figtree, sans-serif);
            }
            .sb-condition-token {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-height: 30px;
                padding: 5px 10px;
                border: 1px solid var(--sb-condition-border);
                border-radius: 9px;
                background: linear-gradient(155deg, var(--sb-condition-surface), var(--sb-condition-surface-alt));
                color: var(--sb-condition-ink);
                font-size: 10px;
                font-weight: 750;
                letter-spacing: .01em;
                box-shadow: var(--theme-module-shadow, 0 2px 5px rgba(45,26,18,.09));
                cursor: pointer;
                transition: background .24s ease, border-color .24s ease, box-shadow .24s ease, transform .22s ease;
                animation: sb-condition-token-in .35s cubic-bezier(.2,.8,.2,1) both;
            }
            .sb-condition-token:hover, .sb-condition-token[aria-expanded='true'] {
                background: var(--sb-condition-hover);
                border-color: var(--sb-condition-border-strong);
                box-shadow: 0 3px 12px color-mix(in srgb, var(--sb-condition-accent) 14%, transparent);
                transform: translateY(-1px);
            }
            .sb-condition-token:focus-visible, .sb-condition-detail__close:focus-visible {
                outline: 2px solid var(--sb-condition-accent);
                outline-offset: 3px;
            }
            .sb-condition-token.is-leaving { pointer-events: none; animation: sb-condition-token-out .38s ease-in both; }
            .sb-condition-token--blinded {
                border-color: color-mix(in srgb, var(--sb-condition-danger) 45%, var(--sb-condition-border));
                color: var(--sb-condition-danger);
                background: color-mix(in srgb, var(--sb-condition-surface) 91%, var(--sb-condition-danger) 9%);
            }
            .sb-condition-token__symbol { width: 15px; height: 15px; display: inline-flex; flex: 0 0 auto; justify-content: center; align-items: center; font-size: 12px; color: currentColor; }
            .sb-condition-token__mask { display: inline-block; width: 15px; height: 15px; background: currentColor; -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center; }
            .sb-condition-token__mask--large { width: 20px; height: 20px; }
            .sb-condition-detail {
                position: absolute;
                top: calc(100% + 8px);
                left: 0;
                width: min(540px, calc(100vw - 210px));
                max-width: calc(100vw - 42px);
                padding: 15px 17px 14px;
                border: 1px solid var(--sb-condition-border-strong);
                border-radius: 12px;
                background: linear-gradient(165deg, var(--sb-condition-surface), var(--sb-condition-surface-alt));
                color: var(--sb-condition-ink);
                box-shadow: var(--theme-shadow-strong, 0 14px 32px rgba(43,25,16,.18));
                z-index: 90;
                font-family: var(--theme-font-body, Figtree, sans-serif);
                overflow: visible;
            }
            .sb-condition-detail::before {
                content: '';
                position: absolute;
                top: 0;
                left: 15px;
                right: 15px;
                height: 2px;
                border-radius: 0 0 6px 6px;
                background: linear-gradient(90deg, transparent, var(--sb-condition-accent), transparent);
                opacity: .65;
                pointer-events: none;
            }
            .sb-condition-self-action { display:flex; flex-wrap:wrap; align-items:center; gap:8px; margin-top:12px; padding-top:10px; border-top:1px solid var(--sb-condition-border, #ddcab8); }
            .sb-condition-self-action__button { padding:7px 11px; border-radius:8px; border:1px solid var(--sb-condition-border, #ddcab8); background:var(--sb-condition-surface, #fffcf5); color:var(--sb-condition-title, #681b15); font-size:11px; font-weight:800; transition:background .18s, transform .18s; }
            .sb-condition-self-action__button:hover:not(:disabled) { background:var(--sb-condition-hover, #f5ebdd); transform:translateY(-1px); }
            .sb-condition-self-action__button:disabled { opacity:.5; cursor:wait; }
            .sb-condition-self-action__hint { font-size:10px; color:var(--sb-condition-muted, #916b45); }
            .sb-condition-self-action__error { font-size:11px; color:var(--sb-condition-danger, #a22222); }
            .sb-condition-detail h3 { margin: 0; font-family: var(--theme-font-display, Georgia, serif); font-size: 16px; font-weight: 750; color: var(--sb-condition-title); }
            .sb-condition-detail p { margin-top: 8px; font-size: 12px; line-height: 1.65; color: var(--sb-condition-ink); }
            .sb-condition-detail small { display: block; margin-top: 9px; font-size: 10px; line-height: 1.5; color: var(--sb-condition-muted); }
            .sb-condition-detail__close {
                display: grid; place-items: center; flex: 0 0 24px;
                width: 24px; height: 24px; border: 1px solid transparent; border-radius: 7px;
                background: transparent; color: var(--sb-condition-muted);
                font-size: 19px; line-height: 1; cursor: pointer;
                transition: background .2s, color .2s, border-color .2s;
            }
            .sb-condition-detail__close:hover { background: var(--sb-condition-hover); color: var(--sb-condition-title); border-color: var(--sb-condition-border); }
            .sb-condition-turn-warning { color: var(--sb-condition-danger) !important; font-weight: 750; }
            .sb-condition-inline-ref { position: relative; display: inline-flex; align-items: center; color: var(--sb-condition-accent); font-weight: 750; border-bottom: 1px dotted var(--sb-condition-accent); cursor: help; }
            .sb-condition-inline-ref__tip {
                position: absolute; left: 50%; bottom: calc(100% + 8px);
                transform: translateX(-50%) translateY(5px);
                min-width: 185px; max-width: 240px; padding: 9px 11px;
                border-radius: 9px; border: 1px solid var(--sb-condition-border-strong);
                background: var(--sb-condition-surface); color: var(--sb-condition-ink);
                font-size: 11px; line-height: 1.48;
                box-shadow: var(--theme-shadow-strong, 0 8px 18px rgba(44,24,12,.2));
                opacity: 0; pointer-events: none;
                transition: opacity .18s ease, transform .18s ease; z-index: 100;
            }
            .sb-condition-inline-ref:hover .sb-condition-inline-ref__tip,
            .sb-condition-inline-ref:focus-visible .sb-condition-inline-ref__tip { opacity: 1; transform: translateX(-50%) translateY(0); }
            .sb-condition-inline-ref__tip strong { display: block; margin-bottom: 3px; color: var(--sb-condition-title); font-size: 11px; }
            .sb-detail-appear, .sb-detail-disappear { transition: opacity .2s ease, transform .2s cubic-bezier(.2,.75,.2,1); }
            .sb-detail-appear-start, .sb-detail-disappear-end { opacity: 0; transform: translateY(-6px) scale(.985); }
            .sb-detail-appear-end, .sb-detail-disappear-start { opacity: 1; transform: translateY(0) scale(1); }
            @keyframes sb-condition-token-in { from { opacity: 0; transform: translateY(6px) scale(.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
            @keyframes sb-condition-token-out { from { opacity: 1; transform: translateY(0) scale(1); } to { opacity: 0; transform: translateY(-4px) scale(.95); } }
            .hero-header-v22 .hero-data-portrait-media { overflow: hidden; }
            .hero-header-v22 .sb-condition-heart-layer,
            .hero-header-v22 .sb-condition-portrait-overlay { position: absolute; inset: 0; pointer-events: none; }
            .hero-header-v22 .sb-condition-heart-layer { z-index: 4; overflow: hidden; }
            .hero-header-v22 .sb-condition-heart { position: absolute; bottom: -10%; color: #d78aa4; font-size: 14px; font-weight: 700; line-height: 1; opacity: 0; text-shadow: 0 0 4px #ffffffa6; animation: sb-charm-heart-float var(--sb-heart-duration, 4.8s) linear infinite; animation-delay: var(--sb-heart-delay, 0s); transform: translateY(0) scale(var(--sb-heart-scale, 1)); }
            .hero-header-v22[data-sb-conditions~='blinded'] .hero-data-portrait-image { filter: blur(3.2px) saturate(.6) contrast(.94) !important; }
            .hero-header-v22[data-sb-conditions~='blinded'] .hero-data-portrait-media { box-shadow: inset 0 0 16px #78787855; }
            .hero-header-v22[data-sb-conditions~='charmed'] .hero-data-portrait-image { animation: sb-charm-glow 3.8s ease-in-out infinite; }
            .hero-header-v22[data-sb-conditions~='charmed'] .hero-data-portrait-media { box-shadow: inset 0 0 0 1px #e1aac133, inset 0 0 22px #e49ab943, 0 0 10px #d88ba626; }
            .hero-header-v22[data-sb-conditions~='frightened'] .hero-data-portrait-image { transform-origin: 50% 56%; animation: sb-fear-breath 2.8s ease-in-out infinite; }
            .hero-header-v22[data-sb-conditions~='frightened'] .hero-data-portrait-media { box-shadow: inset 0 0 28px rgba(61, 20, 98, .58), inset 0 0 56px rgba(99, 47, 145, .18), 0 0 16px rgba(83, 28, 128, .28); }
            .hero-header-v22[data-sb-conditions~='frightened'] .hero-data-portrait-media::before,
            .hero-header-v22[data-sb-conditions~='frightened'] .hero-data-portrait-media::after { content: ''; position: absolute; left: -12%; right: -12%; pointer-events: none; }
            .hero-header-v22[data-sb-conditions~='frightened'] .hero-data-portrait-media::before { bottom: -10%; height: 88%; border-radius: 54% 54% 18% 18%; background: radial-gradient(ellipse at 18% 100%, rgba(104, 40, 161, .54) 0 13%, transparent 44%), radial-gradient(ellipse at 50% 100%, rgba(84, 32, 132, .58) 0 14%, transparent 46%), radial-gradient(ellipse at 82% 100%, rgba(121, 59, 183, .48) 0 12%, transparent 42%); filter: blur(11px); opacity: .9; animation: sb-fear-smoke 4.8s ease-in-out infinite; }
            .hero-header-v22[data-sb-conditions~='frightened'] .hero-data-portrait-media::after { inset: 0; background: radial-gradient(circle at 50% 52%, transparent 0 44%, rgba(53, 17, 87, .10) 72%, rgba(39, 12, 65, .32) 100%), linear-gradient(180deg, rgba(100, 52, 152, .05) 0%, rgba(41, 16, 68, .28) 100%); mix-blend-mode: screen; opacity: .86; animation: sb-fear-flicker 2.1s ease-in-out infinite; }
            /* Agarrado: braços do usuário em camada própria, abraçando a moldura sem cortar. */
            .hero-header-v22[data-sb-conditions~='grappled'] .hero-data-portrait-media {
                outline: 2px solid rgba(200, 154, 87, .75);
                outline-offset: -2px;
                box-shadow: inset 0 0 12px rgba(79, 52, 23, .16), 0 0 11px rgba(208, 167, 101, .18);
            }
            .hero-header-v22 .sb-condition-extra--grappled { z-index: 8; overflow: visible; }
            .hero-header-v22 .sb-condition-grappled-hand {
                position: absolute;
                width: 144px;
                height: 92px;
                background-position: center;
                background-size: contain;
                background-repeat: no-repeat;
                pointer-events: none;
                filter:
                    drop-shadow(0 1px 2px rgba(31, 16, 7, .58))
                    drop-shadow(0 0 1px rgba(255, 232, 168, .92))
                    drop-shadow(0 0 6px rgba(213, 162, 56, .42))
                    brightness(1.18)
                    saturate(1.14)
                    contrast(1.08);
                will-change: transform, opacity, filter;
                opacity: 0;
            }
            .hero-header-v22 .sb-condition-grappled-hand--left {
                left: -45px;
                top: 46%;
                background-image: url('{{ asset('images/conditions/grappled-hand-left.png') }}');
            }
            .hero-header-v22 .sb-condition-grappled-hand--right {
                right: -60px;
                top: 66%;
                background-image: url('{{ asset('images/conditions/grappled-hand-right.png') }}');
            }
            .hero-header-v22 .is-entering .sb-condition-grappled-hand--left { animation: sb-grappled-enter-left .52s cubic-bezier(.24,.74,.24,1) both; }
            .hero-header-v22 .is-entering .sb-condition-grappled-hand--right { animation: sb-grappled-enter-right .52s cubic-bezier(.24,.74,.24,1) both; }
            .hero-header-v22 .is-idle .sb-condition-grappled-hand--left { opacity: 1; filter: drop-shadow(0 1px 2px rgba(31, 16, 7, .62)) drop-shadow(0 0 2px rgba(255, 231, 165, .96)) drop-shadow(0 0 7px rgba(214, 165, 57, .48)) brightness(1.2) saturate(1.15) contrast(1.1); animation: sb-grappled-idle-left 2.8s ease-in-out infinite; }
            .hero-header-v22 .is-idle .sb-condition-grappled-hand--right { opacity: 1; filter: drop-shadow(0 1px 2px rgba(31, 16, 7, .62)) drop-shadow(0 0 2px rgba(255, 231, 165, .96)) drop-shadow(0 0 7px rgba(214, 165, 57, .48)) brightness(1.2) saturate(1.15) contrast(1.1); animation: sb-grappled-idle-right 2.8s ease-in-out infinite; }
            .hero-header-v22 .is-exiting .sb-condition-grappled-hand--left { opacity: 1; animation: sb-grappled-exit-left .42s ease-in both; }
            .hero-header-v22 .is-exiting .sb-condition-grappled-hand--right { opacity: 1; animation: sb-grappled-exit-right .42s ease-in both; }
            @keyframes sb-grappled-enter-left { from { opacity: 0; transform: translateX(-42px) translateY(8px) rotate(-10deg); } to { opacity: 1; transform: translateX(0) translateY(0) rotate(0deg); } }
            @keyframes sb-grappled-enter-right { from { opacity: 0; transform: translateX(42px) translateY(8px) rotate(10deg); } to { opacity: 1; transform: translateX(0) translateY(0) rotate(0deg); } }
            @keyframes sb-grappled-idle-left { 0%,100% { transform: translateX(0) translateY(0) rotate(0deg); } 50% { transform: translateX(-2px) translateY(1px) rotate(-2deg); } }
            @keyframes sb-grappled-idle-right { 0%,100% { transform: translateX(0) translateY(0) rotate(0deg); } 50% { transform: translateX(2px) translateY(1px) rotate(2deg); } }
            @keyframes sb-grappled-exit-left { from { opacity: 1; transform: translateX(0) translateY(0); } to { opacity: 0; transform: translateX(-32px) translateY(6px); } }
            @keyframes sb-grappled-exit-right { from { opacity: 1; transform: translateX(0) translateY(0); } to { opacity: 0; transform: translateX(32px) translateY(6px); } }
            .hero-header-v22[data-sb-conditions~='incapacitated'] .hero-data-portrait-image { filter: brightness(.53) saturate(.42); }
            /* Paralisado — energia somente nas bordas do retrato, sem atravessar o rosto. */
            .hero-header-v22[data-sb-conditions~='paralyzed'] .hero-data-portrait-image {
                filter: saturate(.92) brightness(.96);
            }
            .hero-header-v22[data-sb-conditions~='paralyzed'] .hero-data-portrait-media {
                box-shadow: inset 0 0 19px rgba(119, 71, 210, .32), 0 0 13px rgba(121, 78, 206, .25);
            }
            .hero-header-v22 .sb-condition-paralysis-layer {
                position: absolute;
                inset: 0;
                z-index: 4;
                border-radius: inherit;
                overflow: hidden;
                pointer-events: none;
                opacity: 0;
                background:
                    radial-gradient(ellipse at 0% 65%, rgba(116, 67, 196, .22), transparent 46%),
                    radial-gradient(ellipse at 100% 45%, rgba(131, 72, 212, .20), transparent 48%),
                    linear-gradient(180deg, rgba(88, 48, 147, .17), rgba(76, 35, 137, .065) 48%, rgba(107, 59, 173, .24));
            }
            .hero-header-v22 .sb-condition-paralysis-particle {
                position: absolute;
                left: var(--sb-particle-x);
                bottom: var(--sb-particle-bottom);
                width: var(--sb-particle-size);
                height: var(--sb-particle-size);
                border-radius: 50%;
                background: rgba(223, 203, 255, .93);
                box-shadow: 0 0 4px 1px rgba(178, 126, 250, .58), 0 0 8px 2px rgba(121, 67, 223, .32);
                opacity: 0;
                will-change: transform, opacity;
            }
            .hero-header-v22 .sb-condition-paralysis-bolt {
                position: absolute;
                top: 2%;
                width: 40%;
                height: 96%;
                pointer-events: none;
                z-index: 5;
                opacity: 0;
                background-repeat: no-repeat;
                background-size: 500% 100%;
                filter: drop-shadow(0 0 5px rgba(171, 128, 255, .52));
            }
            .hero-header-v22 .sb-condition-paralysis-bolt--left { left: -5%; }
            .hero-header-v22 .sb-condition-paralysis-bolt--right { right: -5%; transform: scaleX(-1); }
            .hero-header-v22 .sb-condition-paralysis-layer.is-entering { animation: sb-paralysis-mist-enter .55s ease-out forwards; }
            .hero-header-v22 .sb-condition-paralysis-layer.is-idle { opacity: .95; animation: sb-paralysis-mist 6.5s ease-in-out infinite; }
            .hero-header-v22 .sb-condition-paralysis-layer.is-exiting { animation: sb-paralysis-mist-exit .45s ease-in forwards; }
            .hero-header-v22 .sb-condition-paralysis-layer.is-entering .sb-condition-paralysis-bolt {
                opacity: .98;
                background-image: url('{{ asset('images/conditions/paralyzed-bolt-enter-sprite.png') }}');
                animation: sb-paralysis-bolt-enter .6s steps(5) forwards;
            }
            .hero-header-v22 .sb-condition-paralysis-layer.is-exiting .sb-condition-paralysis-bolt {
                opacity: .98;
                background-image: url('{{ asset('images/conditions/paralyzed-bolt-exit-sprite.png') }}');
                animation: sb-paralysis-bolt-exit .5s steps(5) forwards;
            }
            .hero-header-v22 .sb-condition-paralysis-layer.is-entering .sb-condition-paralysis-particle,
            .hero-header-v22 .sb-condition-paralysis-layer.is-idle .sb-condition-paralysis-particle {
                animation: sb-paralysis-rise var(--sb-particle-duration) linear infinite;
                animation-delay: var(--sb-particle-delay);
            }
            .hero-header-v22 .sb-condition-paralysis-layer.is-exiting .sb-condition-paralysis-particle {
                animation: sb-paralysis-particle-exit .4s ease-in forwards !important;
            }
            @keyframes sb-paralysis-mist { 0%, 100% { opacity: .76; } 50% { opacity: 1; } }
            @keyframes sb-paralysis-mist-enter { from { opacity: 0; } to { opacity: .95; } }
            @keyframes sb-paralysis-mist-exit { from { opacity: .92; } to { opacity: 0; } }
            @keyframes sb-paralysis-rise {
                0% { transform: translate3d(0, 6px, 0) scale(.72); opacity: 0; }
                16% { opacity: .72; }
                68% { opacity: .57; }
                100% { transform: translate3d(var(--sb-particle-drift), -110px, 0) scale(1.12); opacity: 0; }
            }
            @keyframes sb-paralysis-particle-exit { from { opacity: .62; transform: translateY(0) scale(1); } to { opacity: 0; transform: translateY(-16px) scale(.92); } }
            .hero-header-v22[data-sb-conditions~='paralyzed'] .hero-data-portrait-media::before {
                content: '';
                position: absolute;
                inset: 1px;
                border: 1px solid rgba(173, 133, 250, .69);
                border-radius: inherit;
                box-shadow: inset 0 0 11px rgba(134, 87, 231, .20), 0 0 8px rgba(144, 89, 245, .31);
                pointer-events: none;
                z-index: 5;
                opacity: 0;
            }
            .hero-header-v22[data-sb-conditions~='paralyzed'] .hero-data-portrait-media::after {
                content: '';
                position: absolute;
                inset: 0;
                pointer-events: none;
                z-index: 6;
                background: url('{{ asset('images/conditions/paralyzed-arcane-threads.svg') }}') center center / 100% 100% no-repeat;
                opacity: 0;
                filter: drop-shadow(0 0 3px rgba(135, 83, 227, .48));
            }
            .hero-header-v22[data-sb-paralyzed-phase='enter'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media,
            .hero-header-v22[data-sb-paralyzed-phase='idle'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media { animation: sb-paralysis-pulse 4.2s ease-in-out infinite; }
            .hero-header-v22[data-sb-paralyzed-phase='enter'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media::before { animation: sb-paralysis-frame-enter .55s ease-out forwards; }
            .hero-header-v22[data-sb-paralyzed-phase='enter'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media::after { animation: sb-paralysis-threads-enter .55s ease-out forwards; }
            .hero-header-v22[data-sb-paralyzed-phase='idle'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media::before { opacity: .82; animation: sb-paralysis-frame 4.2s ease-in-out infinite; }
            .hero-header-v22[data-sb-paralyzed-phase='idle'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media::after { opacity: .88; animation: sb-paralysis-threads 5.2s ease-in-out infinite; }
            .hero-header-v22[data-sb-paralyzed-phase='exit'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media::before { opacity: .82; animation: sb-paralysis-frame-exit .42s ease-in forwards; }
            .hero-header-v22[data-sb-paralyzed-phase='exit'][data-sb-conditions~='paralyzed'] .hero-data-portrait-media::after { opacity: .88; animation: sb-paralysis-threads-exit .42s ease-in forwards; }
            .hero-header-v22[data-sb-conditions~='petrified'] .hero-data-portrait-image { filter: grayscale(1) saturate(.06) brightness(.76) contrast(1.08) sepia(.04); }
            .hero-header-v22[data-sb-conditions~='petrified'] .hero-data-portrait-media { box-shadow: inset 0 0 0 999px rgba(129, 125, 119, .18), inset 0 0 16px rgba(233, 230, 224, .08); }
            .hero-header-v22[data-sb-conditions~='petrified'] .hero-data-portrait-media::before,
            .hero-header-v22[data-sb-conditions~='petrified'] .hero-data-portrait-media::after { content: none !important; background: none !important; }
            .hero-header-v22[data-sb-conditions~='poisoned'] .hero-data-portrait-image { animation: sb-poison-pulse 2.8s infinite; }
            .hero-header-v22[data-sb-conditions~='prone'] .hero-data-portrait-image { transform-origin: center center; }
            .hero-header-v22[data-sb-conditions~='restrained'] .hero-data-portrait-media { outline: 2px solid rgba(116, 121, 129, .78); outline-offset: -3px; box-shadow: inset 0 0 18px rgba(42, 45, 52, .28), 0 0 12px rgba(17, 21, 29, .18); }
            .hero-header-v22[data-sb-conditions~='stunned'] .hero-data-portrait-image { filter: grayscale(.5); }
            .hero-header-v22[data-sb-conditions~='stunned'] .hero-data-portrait-media::after { content: none !important; }
            .hero-header-v22[data-sb-conditions~='unconscious'] .hero-data-portrait-image { filter: grayscale(1) brightness(.66) !important; opacity: .8 !important; }
            @keyframes sb-charm-glow { 0%,100% { filter: brightness(1) saturate(1); } 50% { filter: brightness(1.03) saturate(1.04) drop-shadow(0 0 6px #f0adc466); } }
            @keyframes sb-charm-heart-float { 0% { transform: translate3d(0, 8px, 0) scale(var(--sb-heart-scale, 1)); opacity: 0; } 15% { opacity: .75; } 85% { opacity: .38; } 100% { transform: translate3d(7px, -88px, 0) scale(calc(var(--sb-heart-scale, 1) + .12)); opacity: 0; } }
            @keyframes sb-fear-breath { 0%, 100% { transform: scale(1); filter: saturate(.94) brightness(.96); } 25% { transform: scale(1.015); filter: saturate(1); } 55% { transform: scale(1.03); filter: saturate(.9) brightness(.92); } 78% { transform: scale(1.017); filter: saturate(.96) brightness(.95); } }
            @keyframes sb-fear-smoke { 0% { transform: translateY(16px) scale(.94); opacity: .14; } 36% { opacity: .76; } 100% { transform: translateY(-20px) scale(1.11); opacity: 0; } }
            @keyframes sb-fear-flicker { 0%,100% { opacity: .66; } 50% { opacity: .92; } }
            @keyframes sb-paralysis-pulse {
                0%, 100% { box-shadow: inset 0 0 13px rgba(111, 65, 197, .21), 0 0 8px rgba(117, 71, 205, .16); }
                50% { box-shadow: inset 0 0 23px rgba(140, 89, 235, .37), 0 0 15px rgba(130, 85, 221, .29); }
            }
            @keyframes sb-paralysis-frame { 0%, 100% { opacity: .63; } 50% { opacity: .98; } }
            @keyframes sb-paralysis-frame-enter { from { opacity: 0; } to { opacity: .86; } }
            @keyframes sb-paralysis-frame-exit { from { opacity: .82; } to { opacity: 0; } }
            @keyframes sb-paralysis-threads { 0%, 100% { opacity: .66; transform: translateY(0); } 30% { opacity: .91; transform: translateY(-1px); } 65% { opacity: .76; transform: translateY(1px); } }
            @keyframes sb-paralysis-threads-enter { from { opacity: 0; transform: translateY(2px); } to { opacity: .88; transform: translateY(0); } }
            @keyframes sb-paralysis-threads-exit { from { opacity: .88; transform: translateY(0); } to { opacity: 0; transform: translateY(-2px); } }
            @keyframes sb-paralysis-bolt-enter { from { background-position: 0% 0; } to { background-position: 100% 0; } }
            @keyframes sb-paralysis-bolt-exit { from { background-position: 0% 0; } to { background-position: 100% 0; } }
            @keyframes sb-grappled-enter-left { from { opacity: 0; transform: translateY(-50%) translateX(-18px) rotate(-13deg) scale(.92); } to { opacity: 1; transform: translateY(-50%) translateX(0) rotate(-7deg) scale(1); } }
            @keyframes sb-grappled-enter-right { from { opacity: 0; transform: translateY(-50%) translateX(18px) rotate(13deg) scale(.92); } to { opacity: 1; transform: translateY(-50%) translateX(0) rotate(7deg) scale(1); } }
            @keyframes sb-grappled-idle-left { 0%, 100% { opacity: 1; transform: translateY(-50%) translateX(0) rotate(-7deg); } 50% { opacity: 1; transform: translateY(-50%) translateX(2px) rotate(-5deg); } }
            @keyframes sb-grappled-idle-right { 0%, 100% { opacity: 1; transform: translateY(-50%) translateX(0) rotate(7deg); } 50% { opacity: 1; transform: translateY(-50%) translateX(-2px) rotate(5deg); } }
            @keyframes sb-grappled-exit-left { from { opacity: 1; transform: translateY(-50%) translateX(0) rotate(-7deg) scale(1); } to { opacity: 0; transform: translateY(-50%) translateX(-16px) rotate(-14deg) scale(.92); } }
            @keyframes sb-grappled-exit-right { from { opacity: 1; transform: translateY(-50%) translateX(0) rotate(7deg) scale(1); } to { opacity: 0; transform: translateY(-50%) translateX(16px) rotate(14deg) scale(.92); } }
            @keyframes sb-poison-pulse { 0%,100% { filter: sepia(.2) hue-rotate(55deg) saturate(1); } 50% { filter: sepia(.65) hue-rotate(60deg) saturate(2) brightness(.84); } }
                        @media (max-width: 960px) {
                .sb-condition-detail { left: 0; width: min(92vw, 560px); }
            }
            @media (prefers-reduced-motion: reduce) {
                .hero-header-v22 .hero-data-portrait-image,
                .hero-header-v22 .hero-data-portrait-media,
                .hero-header-v22 .hero-data-portrait-media::after,
                .hero-header-v22 .hero-data-portrait-media::before,
                .hero-header-v22 .sb-condition-heart,
                .hero-header-v22 .sb-condition-paralysis-layer,
                .hero-header-v22 .sb-condition-paralysis-particle,
                .hero-header-v22 .sb-condition-paralysis-bolt { animation: none !important; }
                .hero-header-v22 .sb-condition-paralysis-particle { opacity: .32; }
            }

            /* V66.0.20: efeitos adicionais sem sobrescrever a base funcional. */
            /* V66.0.17 — fases controladas por dados de condição: entrar, manter, dissipar. */
            .hero-header-v22 .sb-condition-extra-layer { position:absolute; inset:0; z-index:5; pointer-events:none; overflow:hidden; border-radius:inherit; }
            /* Petrificado: cimento cinza real, rachaduras extraídas dos seis quadros enviados. */
            .hero-header-v22[data-sb-petrified-phase='enter'] .hero-data-portrait-image { animation:sb-petrify-image 1.55s ease-out both; }
            .hero-header-v22[data-sb-petrified-phase='idle'] .hero-data-portrait-image { filter:grayscale(1) saturate(.08) brightness(.72) contrast(1.15); }
            .hero-header-v22[data-sb-petrified-phase='exit'] .hero-data-portrait-image { animation:sb-unpetrify-image .8s ease-in-out both; }
            .hero-header-v22 .sb-condition-extra--petrified { background:linear-gradient(180deg,rgba(173,170,165,.3),rgba(76,76,76,.28)),radial-gradient(circle at 19% 33%,rgba(199,197,189,.3),transparent 43%),radial-gradient(circle at 82% 67%,rgba(61,62,63,.26),transparent 40%); mix-blend-mode:normal; }
            .hero-header-v22 .sb-condition-extra--petrified.is-entering { animation:sb-stone-layer-in 1.6s ease-out both; }
            .hero-header-v22 .sb-condition-extra--petrified.is-idle { opacity:1; }
            .hero-header-v22 .sb-condition-extra--petrified.is-exiting { animation:sb-overlay-exit .8s ease-in both; }
            .hero-header-v22 .sb-condition-crack { position:absolute; display:block; aspect-ratio:1.25; height:auto; background-repeat:no-repeat; background-size:100% 100%; opacity:0; transform-origin:50% 50%; filter:drop-shadow(0 0 1px rgba(12,15,17,.65)); }
            .hero-header-v22 .is-entering .sb-condition-crack { animation:sb-crack-appear .65s ease-out var(--sb-crack-delay) both; }
            .hero-header-v22 .is-idle .sb-condition-crack { opacity:.87; transform:scale(1) rotate(var(--sb-crack-rotation)); }
            .hero-header-v22 .is-exiting .sb-condition-crack { opacity:.87; animation:sb-crack-dissolve .6s ease-in both; }
            @keyframes sb-petrify-image { from { filter:none; } to { filter:grayscale(1) saturate(.08) brightness(.72) contrast(1.15); } }
            @keyframes sb-unpetrify-image { from { filter:grayscale(1) saturate(.08) brightness(.72) contrast(1.15); } to { filter:none; } }
            @keyframes sb-stone-layer-in { 0% { opacity:0; } 100% { opacity:1; } }
            @keyframes sb-crack-appear { 0% { opacity:0; transform:scale(.1) rotate(var(--sb-crack-rotation)); } 65% { opacity:.9; } 100% { opacity:.87; transform:scale(1) rotate(var(--sb-crack-rotation)); } }
            @keyframes sb-crack-dissolve { to { opacity:0; transform:scale(1.1) rotate(var(--sb-crack-rotation)); filter:blur(2px); } }
            /* Envenenado: fundo verde escuro, vertigem lateral e vapores em espiral. */
            .hero-header-v22 .sb-condition-extra--poisoned { background:radial-gradient(ellipse at 6% 56%,rgba(24,91,42,.55),transparent 60%),radial-gradient(ellipse at 95% 72%,rgba(20,95,35,.48),transparent 59%),linear-gradient(180deg,rgba(20,56,29,.18),rgba(36,81,37,.26)); box-shadow:inset 0 0 20px rgba(21,93,32,.42); }
            .hero-header-v22 .sb-condition-extra--poisoned.is-entering { animation:sb-poison-aura-in .75s ease-out both; }
            .hero-header-v22 .sb-condition-extra--poisoned.is-idle { opacity:1; animation:sb-poison-aura-idle 4.8s ease-in-out infinite; }
            .hero-header-v22 .sb-condition-extra--poisoned.is-exiting { animation:sb-overlay-exit .55s ease-in both; }
            .hero-header-v22[data-sb-poisoned-phase='enter'] .hero-data-portrait-image { animation:sb-poison-image-in .75s ease-out both; }
            .hero-header-v22[data-sb-poisoned-phase='idle'] .hero-data-portrait-image { animation:sb-poison-vertigo 4.1s ease-in-out infinite; }
            .hero-header-v22[data-sb-poisoned-phase='exit'] .hero-data-portrait-image { animation:sb-poison-image-out .58s ease-in both; }
            .hero-header-v22 .sb-condition-poison-mote { position:absolute; left:var(--sb-poison-x); bottom:var(--sb-poison-bottom); width:7px; height:7px; border-radius:58% 42% 67% 33%; border:1px solid rgba(135,218,93,.85); background:radial-gradient(circle,rgba(154,215,92,.46),transparent 70%); box-shadow:0 0 6px rgba(111,198,61,.65); opacity:0; }
            .hero-header-v22 .is-idle .sb-condition-poison-mote, .hero-header-v22 .is-entering .sb-condition-poison-mote { animation:sb-poison-mote-rise var(--sb-poison-duration) ease-in-out var(--sb-poison-delay) infinite; }
            .hero-header-v22 .is-exiting .sb-condition-poison-mote { animation:sb-overlay-exit .4s ease-in both; }
            @keyframes sb-poison-aura-in { from { opacity:0; filter:blur(6px); } to { opacity:1; filter:blur(0); } }
            @keyframes sb-poison-aura-idle { 0%,100% { opacity:.75; } 50% { opacity:1; } }
            @keyframes sb-poison-image-in { from { filter:none; transform:rotate(0deg); } to { filter:saturate(.83) sepia(.08) brightness(.93); transform:rotate(-1.7deg); } }
            @keyframes sb-poison-vertigo { 0%,100% { transform:rotate(-2.3deg) translateX(-2px); filter:saturate(.82) sepia(.11) brightness(.92); } 50% { transform:rotate(2.3deg) translateX(2px); filter:saturate(.84) sepia(.1) brightness(.94); } }
            @keyframes sb-poison-image-out { from { transform:rotate(-1.7deg); filter:saturate(.83) sepia(.08) brightness(.93); } to { transform:rotate(0); filter:none; } }
            @keyframes sb-poison-mote-rise { 0% { transform:translate(0,8px) rotate(0) scale(.6); opacity:0; } 16% { opacity:.7; } 45% { transform:translate(var(--sb-poison-shift),-22px) rotate(90deg) scale(1.1); opacity:.8; } 73% { transform:translate(calc(var(--sb-poison-shift) * -0.5),-48px) rotate(220deg); opacity:.5; } 100% { transform:translate(4px,-83px) rotate(360deg) scale(.6); opacity:0; } }            /* Em Chamas: fogo mais afiado e detalhado, com entrada, loop e dissipação. */
            .hero-header-v22 .sb-condition-extra--burning {
                z-index: 8;
                background:
                    radial-gradient(circle at 50% 88%, rgba(255, 166, 74, .20) 0 18%, rgba(255, 104, 28, .18) 34%, rgba(255, 43, 0, .10) 56%, transparent 76%),
                    linear-gradient(180deg, rgba(255, 150, 44, .04) 0%, rgba(255, 116, 28, .09) 45%, rgba(255, 72, 18, .13) 100%);
                mix-blend-mode: screen;
            }
            .hero-header-v22 .sb-condition-extra--burning.is-entering { animation: sb-burning-layer-in .78s ease-out both; }
            .hero-header-v22 .sb-condition-extra--burning.is-idle { opacity: 1; animation: sb-burning-layer-idle 2.8s ease-in-out infinite; }
            .hero-header-v22 .sb-condition-extra--burning.is-exiting { animation: sb-burning-layer-out .62s ease-in both; }
            .hero-header-v22 .sb-condition-burning-flames {
                position: absolute;
                inset: -7% -9% -5% -9%;
                background-position: center bottom;
                background-repeat: no-repeat;
                background-size: 600% 100%;
                opacity: .98;
                mix-blend-mode: screen;
                filter: drop-shadow(0 0 8px rgba(255, 126, 34, .55)) drop-shadow(0 -1px 14px rgba(255, 190, 84, .22));
                will-change: background-position, opacity, transform;
            }
            .hero-header-v22 .is-entering .sb-condition-burning-flames {
                background-image: url('{{ asset('images/conditions/burning-flames-enter-sprite.png') }}');
                animation: sb-burning-enter .78s steps(6) forwards;
            }
            .hero-header-v22 .is-idle .sb-condition-burning-flames {
                background-image: url('{{ asset('images/conditions/burning-flames-idle-sprite.png') }}');
                animation: sb-burning-idle .95s steps(6) infinite;
            }
            .hero-header-v22 .is-exiting .sb-condition-burning-flames {
                background-image: url('{{ asset('images/conditions/burning-flames-exit-sprite.png') }}');
                animation: sb-burning-exit .58s steps(6) forwards;
            }
            .hero-header-v22 .sb-condition-burning-ember {
                position: absolute;
                left: var(--sb-ember-x);
                bottom: var(--sb-ember-bottom);
                width: var(--sb-ember-size);
                height: calc(var(--sb-ember-size) * 1.2);
                border-radius: 50% 50% 40% 40%;
                background: radial-gradient(circle at 50% 30%, rgba(255,246,201,.98) 0 18%, rgba(255,198,97,.95) 19% 46%, rgba(255,107,30,.88) 47% 70%, transparent 71%);
                box-shadow: 0 0 7px rgba(255, 142, 42, .64);
                opacity: 0;
            }
            .hero-header-v22 .is-entering .sb-condition-burning-ember,
            .hero-header-v22 .is-idle .sb-condition-burning-ember {
                animation: sb-burning-ember-rise var(--sb-ember-duration) ease-out infinite;
                animation-delay: var(--sb-ember-delay);
            }
            .hero-header-v22 .is-exiting .sb-condition-burning-ember { animation: sb-burning-ember-out .36s ease-in forwards; }
            .hero-header-v22[data-sb-burning-phase='enter'] .hero-data-portrait-media,
            .hero-header-v22[data-sb-burning-phase='idle'] .hero-data-portrait-media {
                box-shadow: inset 0 0 24px rgba(255, 116, 34, .24), inset 0 -18px 26px rgba(255, 95, 16, .21), 0 0 16px rgba(255, 126, 42, .12);
            }
            .hero-header-v22[data-sb-burning-phase='idle'] .hero-data-portrait-image { animation: sb-burning-heat 2.8s ease-in-out infinite; }
            .hero-header-v22[data-sb-burning-phase='exit'] .hero-data-portrait-image { animation: sb-burning-cooldown .55s ease-in both; }
            @keyframes sb-burning-layer-in { from { opacity: 0; filter: blur(8px); } to { opacity: 1; filter: blur(0); } }
            @keyframes sb-burning-layer-idle { 0%,100% { opacity: .9; } 50% { opacity: 1; } }
            @keyframes sb-burning-layer-out { from { opacity: 1; } to { opacity: 0; filter: blur(5px); } }
            @keyframes sb-burning-enter { from { background-position: 0% 0; transform: translateY(7px) scaleY(.88); opacity: .4; } to { background-position: 100% 0; transform: translateY(0) scaleY(1); opacity: 1; } }
            @keyframes sb-burning-idle { from { background-position: 0% 0; } to { background-position: 100% 0; } }
            @keyframes sb-burning-exit { from { background-position: 0% 0; transform: translateY(0) scaleY(1); opacity: 1; } to { background-position: 100% 0; transform: translateY(9px) scaleY(.84); opacity: 0; } }
            @keyframes sb-burning-ember-rise {
                0% { transform: translate3d(0, 9px, 0) scale(.48) rotate(0deg); opacity: 0; }
                12% { opacity: .92; }
                62% { opacity: .56; }
                100% { transform: translate3d(var(--sb-ember-drift), -96px, 0) scale(1.06) rotate(18deg); opacity: 0; }
            }
            @keyframes sb-burning-ember-out { from { opacity: .62; transform: scale(1); } to { opacity: 0; transform: translateY(-12px) scale(.72); } }
            @keyframes sb-burning-heat {
                0%,100% { transform: translateY(0) scale(1); filter: saturate(1.02) brightness(1.01); }
                50% { transform: translateY(-.5px) scale(1.008); filter: saturate(1.1) brightness(1.06); }
            }
            @keyframes sb-burning-cooldown { from { filter: saturate(1.08) brightness(1.03); } to { filter: none; } }

            /* Caído: a BASE que tocava o rodapé passa a tocar a BORDA DIREITA.
               O pivô fica no centro para que a cabeça não fuja pela esquerda.
               O dimensionamento conserva o aspecto e só reduz quando necessário
               para caber inteiramente na moldura, inclusive em layouts responsivos. */
            .hero-header-v22 .sb-condition-extra--prone { background:linear-gradient(0deg,rgba(25,20,15,.52) 0%,transparent 28%); }
            .hero-header-v22 .sb-condition-extra--prone.is-entering { animation:sb-prone-aura-in .85s ease-out both; }
            .hero-header-v22 .sb-condition-extra--prone.is-idle { opacity:1; }
            .hero-header-v22 .sb-condition-extra--prone.is-exiting { animation:sb-overlay-exit .6s ease-out both; }
            .hero-header-v22[data-sb-prone-phase='enter'] .hero-data-portrait-image,
            .hero-header-v22[data-sb-prone-phase='idle'] .hero-data-portrait-image,
            .hero-header-v22[data-sb-prone-phase='exit'] .hero-data-portrait-image { transform-origin:50% 50%; }
            .hero-header-v22[data-sb-prone-phase='enter'] .hero-data-portrait-image { animation:sb-prone-fall .62s cubic-bezier(.22,.78,.24,1) both; }
            .hero-header-v22[data-sb-prone-phase='idle'] .hero-data-portrait-image {
                transform:translate(var(--sb-prone-x, 0px), var(--sb-prone-y, 0px)) rotate(-90deg) scale(var(--sb-prone-scale, 1));
            }
            .hero-header-v22[data-sb-prone-phase='exit'] .hero-data-portrait-image { animation:sb-prone-rise .58s ease-in-out both; }
            @keyframes sb-prone-fall {
                0% { transform:none; }
                100% { transform:translate(var(--sb-prone-x, 0px), var(--sb-prone-y, 0px)) rotate(-90deg) scale(var(--sb-prone-scale, 1)); }
            }
            @keyframes sb-prone-rise {
                0% { transform:translate(var(--sb-prone-x, 0px), var(--sb-prone-y, 0px)) rotate(-90deg) scale(var(--sb-prone-scale, 1)); }
                100% { transform:none; }
            }
            @keyframes sb-prone-aura-in { from { opacity:0; } to { opacity:1; } }
            /* Contido/Restringido: sprite central maior, escondido pelas bordas da caixa para não nascer do nada. */
            .hero-header-v22 .sb-condition-extra--restrained { z-index: 7; overflow: hidden; }
            .hero-header-v22 .sb-condition-chain-frame {
                position: absolute;
                inset: 0;
                overflow: hidden;
                border-radius: inherit;
                pointer-events: none;
            }
            .hero-header-v22 .sb-condition-chain-sprite {
                position: absolute;
                left: 50%;
                top: 50%;
                width: 142%;
                height: 142%;
                transform: translate(-50%, -50%);
                background: url('{{ asset('images/conditions/restrained-chain-sprite.png') }}') center center / contain no-repeat;
                opacity: .86;
                filter: brightness(.52) saturate(.72) contrast(1.05) drop-shadow(0 1px 2px rgba(0,0,0,.55));
                mix-blend-mode: screen;
                will-change: transform, opacity;
            }
            .hero-header-v22 .is-entering .sb-condition-chain-sprite { animation: sb-chain-enter .88s cubic-bezier(.22,.72,.18,1) both; }
            .hero-header-v22 .is-idle .sb-condition-chain-sprite { animation: sb-chain-idle 4.8s ease-in-out infinite; }
            .hero-header-v22 .is-exiting .sb-condition-chain-sprite { animation: sb-chain-exit .55s ease-in both; }
            @keyframes sb-chain-enter {
                0% { opacity: 0; transform: translate(-50%, -50%) scale(1.07); }
                55% { opacity: .9; }
                100% { opacity: .86; transform: translate(-50%, -50%) scale(1); }
            }
            @keyframes sb-chain-idle {
                0%,100% { opacity: .86; transform: translate(-50%, -50%) translate3d(0,0,0) scale(1); }
                25% { transform: translate(-50%, -50%) translate3d(-1.2px, .8px, 0) scale(1.005); }
                50% { opacity: .9; transform: translate(-50%, -50%) translate3d(.8px, -.8px, 0) scale(1.01); }
                75% { transform: translate(-50%, -50%) translate3d(-.8px, -.6px, 0) scale(1.004); }
            }
            @keyframes sb-chain-exit {
                from { opacity: .86; transform: translate(-50%, -50%) scale(1); }
                to { opacity: 0; transform: translate(-50%, -50%) scale(1.05); }
            }
            /* Atordoado: aureola horizontal. Cada estrela percorre a elipse individualmente,
               mantendo sua forma vertical e simulando profundidade por tamanho, brilho e opacidade. */
            .hero-header-v22 .sb-condition-extra--stunned { z-index:7; overflow:visible; }
            .hero-header-v22 .sb-condition-stun-orbit {
                position:absolute;
                left:15%; top:2%; width:70%; height:24%;
                display:block;
                pointer-events:none;
                opacity:1;
            }
            .hero-header-v22 .sb-condition-stun-star {
                position:absolute;
                left:50%; top:50%;
                width:16px; height:16px;
                display:flex; align-items:center; justify-content:center;
                font-size:15px; line-height:1;
                color:#f1cf79;
                text-shadow:0 0 3px rgba(251,228,148,.7);
                opacity:0;
                transform-origin:center center;
                /* Atrasos negativos deixam as estrelas igualmente distribuídas na órbita.
                   A animação continua entre entrada/idle, sem reiniciar no polling. */
                animation:sb-stun-star-revolve 5.4s linear infinite;
                animation-delay:var(--sb-star-delay,0s);
                will-change:transform, opacity;
            }
            .hero-header-v22 .is-entering .sb-condition-stun-orbit { animation:sb-stun-orbit-enter .72s ease-out both; }
            .hero-header-v22 .is-idle .sb-condition-stun-orbit { opacity:1; }
            .hero-header-v22 .is-exiting .sb-condition-stun-orbit { animation:sb-stun-orbit-exit .48s ease-in both; }
            .hero-header-v22[data-sb-stunned-phase='enter'] .hero-data-portrait-image { animation:sb-stun-image-enter .7s ease-out both; }
            .hero-header-v22[data-sb-stunned-phase='idle'] .hero-data-portrait-image { animation:sb-stun-image-idle 4.9s ease-in-out infinite; }
            .hero-header-v22[data-sb-stunned-phase='exit'] .hero-data-portrait-image { animation:sb-stun-image-exit .48s ease-out both; }
            /* Trajetória vista de frente: ao passar atrás, fica pequena e discreta;
               ao passar à frente, aproxima-se, ganha brilho e nunca deforma a estrela. */
            @keyframes sb-stun-star-revolve {
                0%, 100% { transform:translate(-50%,-50%) translate(-35px,0) scale(.88); opacity:.77; filter:brightness(.85); z-index:3; }
                12.5% { transform:translate(-50%,-50%) translate(-25px,-8px) scale(.71); opacity:.53; filter:brightness(.69); z-index:2; }
                25% { transform:translate(-50%,-50%) translate(0,-11px) scale(.58); opacity:.32; filter:brightness(.58); z-index:1; }
                37.5% { transform:translate(-50%,-50%) translate(25px,-8px) scale(.71); opacity:.53; filter:brightness(.69); z-index:2; }
                50% { transform:translate(-50%,-50%) translate(35px,0) scale(.88); opacity:.77; filter:brightness(.85); z-index:3; }
                62.5% { transform:translate(-50%,-50%) translate(25px,9px) scale(1.07); opacity:.94; filter:brightness(1.08) drop-shadow(0 0 3px rgba(255,220,110,.55)); z-index:5; }
                75% { transform:translate(-50%,-50%) translate(0,12px) scale(1.28); opacity:1; filter:brightness(1.2) drop-shadow(0 0 5px rgba(255,222,115,.67)); z-index:7; }
                87.5% { transform:translate(-50%,-50%) translate(-25px,9px) scale(1.07); opacity:.94; filter:brightness(1.08) drop-shadow(0 0 3px rgba(255,220,110,.55)); z-index:5; }
            }
            @keyframes sb-stun-orbit-enter { from { opacity:0; transform:scale(.74); } to { opacity:1; transform:scale(1); } }
            @keyframes sb-stun-orbit-exit { from { opacity:1; transform:scale(1); } to { opacity:0; transform:scale(.74); } }
            @keyframes sb-stun-image-enter { from { filter:none; transform:rotate(0); } to { filter:grayscale(.3); transform:rotate(-1deg); } }
            @keyframes sb-stun-image-idle { 0%,100% { filter:grayscale(.34); transform:rotate(-1.6deg) translateX(-1px); } 50% { filter:grayscale(.32); transform:rotate(1.6deg) translateX(1px); } }
            @keyframes sb-stun-image-exit { from { filter:grayscale(.32); transform:rotate(1deg); } to { filter:none; transform:rotate(0); } }
            @keyframes sb-overlay-exit { from { opacity:1; } to { opacity:0; } }

            /* V66.0.36 — Entradas e saídas sem alterar as animações de permanência. */
            .hero-header-v22 .hero-data-portrait-image {
                transition: filter .52s cubic-bezier(.2,.75,.25,1), opacity .52s cubic-bezier(.2,.75,.25,1);
            }
            .hero-header-v22 .hero-data-portrait-media {
                transition: box-shadow .48s ease, outline-color .48s ease;
            }
            .hero-header-v22 .sb-condition-extra-layer::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: inherit;
                pointer-events: none;
                background: radial-gradient(ellipse at 50% 60%, transparent 22%, var(--sb-condition-phase-glow, rgba(170,153,123,.24)) 100%);
                opacity: 0;
                z-index: 0;
            }
            .hero-header-v22 .sb-condition-extra-layer.is-entering::after {
                animation: sb-condition-phase-in .52s ease-out both;
            }
            .hero-header-v22 .sb-condition-extra-layer.is-exiting::after {
                animation: sb-condition-phase-out .5s ease-in both;
            }
            .hero-header-v22 .sb-condition-extra--blinded { --sb-condition-phase-glow: rgba(169,169,170,.24); }
            .hero-header-v22 .sb-condition-extra--charmed { --sb-condition-phase-glow: rgba(235,117,166,.25); }
            .hero-header-v22 .sb-condition-extra--deafened { --sb-condition-phase-glow: rgba(151,158,177,.19); }
            .hero-header-v22 .sb-condition-extra--exhaustion { --sb-condition-phase-glow: rgba(159,131,91,.24); }
            .hero-header-v22 .sb-condition-extra--frightened { --sb-condition-phase-glow: rgba(109,57,163,.29); }
            .hero-header-v22 .sb-condition-extra--incapacitated { --sb-condition-phase-glow: rgba(91,92,108,.23); }
            .hero-header-v22 .sb-condition-extra--unconscious { --sb-condition-phase-glow: rgba(83,90,101,.26); }
            .hero-header-v22 .sb-condition-extra--paralyzed { --sb-condition-phase-glow: rgba(133,81,205,.2); }
            .hero-header-v22 .sb-condition-extra--petrified { --sb-condition-phase-glow: rgba(166,160,151,.2); }
            .hero-header-v22 .sb-condition-extra--poisoned { --sb-condition-phase-glow: rgba(60,155,68,.16); }
            .hero-header-v22 .sb-condition-extra--prone { --sb-condition-phase-glow: rgba(131,112,95,.15); }
            .hero-header-v22 .sb-condition-extra--restrained { --sb-condition-phase-glow: rgba(137,143,156,.21); }
            .hero-header-v22 .sb-condition-extra--stunned { --sb-condition-phase-glow: rgba(225,185,105,.17); }
            .hero-header-v22 .sb-condition-extra--burning { --sb-condition-phase-glow: rgba(255,120,48,.23); }
            @keyframes sb-condition-phase-in {
                0% { opacity: 0; transform: scale(1.035); }
                38% { opacity: .94; }
                100% { opacity: 0; transform: scale(1); }
            }
            @keyframes sb-condition-phase-out {
                0% { opacity: 0; transform: scale(1); }
                40% { opacity: .62; }
                100% { opacity: 0; transform: scale(1.028); }
            }
            .hero-header-v22 .sb-condition-heart-layer.is-entering { animation: sb-condition-heart-in .55s ease-out both; }
            .hero-header-v22 .sb-condition-heart-layer.is-exiting { animation: sb-condition-heart-out .53s ease-in both; }
            @keyframes sb-condition-heart-in { from { opacity: 0; } to { opacity: 1; } }
            @keyframes sb-condition-heart-out { from { opacity: 1; } to { opacity: 0; } }
            /* O efeito de ofegar e os corações existentes continuam idênticos em idle. */
            .hero-header-v22[data-sb-frightened-phase='enter'] .hero-data-portrait-image { animation: sb-fear-breath-in .67s ease-out both; }
            .hero-header-v22[data-sb-frightened-phase='exit'] .hero-data-portrait-image { animation: sb-fear-breath-out .62s ease-in-out both; }
            @keyframes sb-fear-breath-in { from { transform: scale(.985); filter: saturate(1); } to { transform: scale(1.015); filter: saturate(.94) brightness(.96); } }
            @keyframes sb-fear-breath-out { from { transform: scale(1.015); filter: saturate(.92) brightness(.95); } to { transform: scale(1); filter: none; } }
            .hero-header-v22[data-sb-charmed-phase='enter'] .hero-data-portrait-image { animation: sb-charm-entry .56s ease-out both; }
            .hero-header-v22[data-sb-charmed-phase='exit'] .hero-data-portrait-image { animation: sb-charm-leave .56s ease-in both; }
            @keyframes sb-charm-entry { from { filter: brightness(1) saturate(1); } to { filter: brightness(1.03) saturate(1.04) drop-shadow(0 0 6px #f0adc466); } }
            @keyframes sb-charm-leave { from { filter: brightness(1.03) saturate(1.04) drop-shadow(0 0 6px #f0adc466); } to { filter: none; } }
            @media (prefers-reduced-motion: reduce) {
                .sb-condition-token, .sb-condition-token.is-leaving { animation: none !important; }
                .hero-header-v22 .sb-condition-extra-layer::after { animation: none !important; }
                .sb-condition-detail, .sb-condition-inline-ref__tip { transition-duration: .01ms !important; }
            }
        </style>
    @endpush
@endonce
{{-- V66.0.3: não colocar objetos JS dentro de atributos HTML. --}}
@once
<script>
    window.sbCombatConditions = function () {
        return {
            conditions: [],
            leavingConditions: [],
            tokenTimers: {},
            activeKey: null,
            removingKey: null,
            removalError: '',
            removeUrl: '',
            refreshing: false,
            interval: null,
            stateUrl: '',
            combatId: null,
            round: null,
            turn: null,
            isCurrentTurn: false,
            lastBurningAlert: null,
            appliedPortraitSignature: null,
            appliedPortraitMedia: null,
            resizeHandler: null,
            visualStates: {},
            visualTimers: {},
            visualDurations: {
                grappled: {enter: 560, exit: 430},
                paralyzed: {enter: 620, exit: 500},
                petrified: {enter: 1860, exit: 800},
                poisoned: {enter: 780, exit: 580},
                prone: {enter: 850, exit: 620},
                restrained: {enter: 960, exit: 670},
                stunned: {enter: 720, exit: 500},
                burning: {enter: 720, exit: 560},
                blinded: {enter: 510, exit: 520},
                charmed: {enter: 560, exit: 550},
                deafened: {enter: 450, exit: 500},
                exhaustion: {enter: 520, exit: 520},
                frightened: {enter: 670, exit: 630},
                incapacitated: {enter: 540, exit: 530},
                unconscious: {enter: 610, exit: 640}
            },
            relatedConditions: {
                blinded: {name: 'Cego', summary: 'Falha em testes que exigem visão; seus ataques têm desvantagem e ataques contra você têm vantagem.'},
                charmed: {name: 'Enfeitiçado', summary: 'Não pode atacar o encantador e ele recebe vantagem em interações sociais apropriadas.'},
                deafened: {name: 'Surdo', summary: 'Falha em testes que dependem da audição.'},
                exhaustion: {name: 'Exaustão', summary: 'Penalidades por nível em testes de d20 e deslocamento; nível 6 é fatal.'},
                frightened: {name: 'Amedrontado', summary: 'Desvantagem em ataques e testes enquanto vê a fonte do medo; não pode se aproximar voluntariamente.'},
                grappled: {name: 'Agarrado', summary: 'Seu deslocamento é 0 e tem desvantagem para atacar outros alvos além do agarrador.'},
                incapacitated: {name: 'Incapacitado', summary: 'Não pode realizar ações, ações bônus ou reações.'},
                paralyzed: {name: 'Paralisado', summary: 'Também fica Incapacitado, com deslocamento 0; falha em salvaguardas de Força e Destreza.'},
                petrified: {name: 'Petrificado', summary: 'É transformado em pedra, fica Incapacitado e recebe resistência a danos.'},
                poisoned: {name: 'Envenenado', summary: 'Desvantagem nos ataques e testes de habilidade.'},
                prone: {name: 'Caído', summary: 'Ataques a até 5 ft contra você têm vantagem, enquanto ataques de longe têm desvantagem.'},
                restrained: {name: 'Contido', summary: 'Deslocamento 0; ataques contra você têm vantagem, seus ataques e salvaguardas de Destreza têm desvantagem.'},
                stunned: {name: 'Atordoado', summary: 'Também fica Incapacitado, falha em salvaguardas de Força e Destreza e ataques contra você têm vantagem.'},
                unconscious: {name: 'Inconsciente', summary: 'Fica Incapacitado e Caído e não percebe o ambiente.'},
                burning: {name: 'Em Chamas', summary: 'No seu turno, confirme com o Mestre o dano de fogo a receber.'}
            },
            init() {
                this.stateUrl = this.$el.dataset.stateUrl || '';
                this.removeUrl = this.$el.dataset.removeUrl || '';
                this.conditionAssetsUrl = this.$el.dataset.conditionsAssetsUrl || '/images/conditions/';
                this.resizeHandler = () => { this.positionBadges(); this.alignPronePortrait(); };
                window.addEventListener('resize', this.resizeHandler, {passive: true});
                this.refresh();
                this.interval = window.setInterval(() => this.refresh(), 3500);
                this.$nextTick(() => this.positionBadges());
            },
            destroy() {
                if (this.interval) window.clearInterval(this.interval);
                if (this.resizeHandler) window.removeEventListener('resize', this.resizeHandler);
                Object.values(this.visualTimers).forEach(timer => window.clearTimeout(timer));
                Object.values(this.tokenTimers).forEach(timer => window.clearTimeout(timer));
                this.visualTimers = {};
                this.tokenTimers = {};
                this.leavingConditions = [];
                this.visualStates = {};
                this.updatePortrait(true);
            },
            managedVisualKeys() {
                return ['blinded', 'charmed', 'deafened', 'exhaustion', 'frightened', 'grappled', 'incapacitated', 'paralyzed', 'petrified', 'poisoned', 'prone', 'restrained', 'stunned', 'unconscious', 'burning'];
            },
            getVisualPhase(key) {
                return this.visualStates[key]?.phase || '';
            },
            setVisualPhase(key, phase) {
                if (this.visualTimers[key]) {
                    window.clearTimeout(this.visualTimers[key]);
                    delete this.visualTimers[key];
                }
                this.visualStates[key] = {phase};
                if (phase === 'enter') {
                    this.visualTimers[key] = window.setTimeout(() => {
                        if (this.visualStates[key]?.phase === 'enter') {
                            this.visualStates[key] = {phase: 'idle'};
                            this.updatePortrait();
                        }
                    }, this.visualDurations[key]?.enter || 500);
                } else if (phase === 'exit') {
                    this.visualTimers[key] = window.setTimeout(() => {
                        if (this.visualStates[key]?.phase === 'exit') {
                            delete this.visualStates[key];
                            this.updatePortrait();
                        }
                    }, this.visualDurations[key]?.exit || 400);
                }
            },
            syncVisualStates(activeKeys) {
                const next = new Set(activeKeys);
                this.managedVisualKeys().forEach((key) => {
                    const currentPhase = this.getVisualPhase(key);
                    if (next.has(key)) {
                        if (!currentPhase || currentPhase === 'exit') {
                            this.setVisualPhase(key, 'enter');
                        }
                    } else if (currentPhase && currentPhase !== 'exit') {
                        this.setVisualPhase(key, 'exit');
                    }
                });
            },
            renderedPortraitKeys(activeKeys) {
                const set = new Set(activeKeys);
                Object.keys(this.visualStates).forEach((key) => set.add(key));
                return Array.from(set).sort();
            },
            positionBadges() {
                const hero = this.$el.closest('.character-sheet-hero');
                if (!hero) return;
                const label = Array.from(hero.querySelectorAll('p')).find(
                    p => p.textContent.trim().toLocaleLowerCase('pt-BR') === 'espécie'
                );
                if (!label || hero.clientWidth < 800) {
                    this.$el.style.position = 'relative';
                    this.$el.style.left = '';
                    this.$el.style.top = '';
                    this.$el.style.maxWidth = '';
                    return;
                }
                const heroBounds = hero.getBoundingClientRect();
                const labelBounds = label.getBoundingClientRect();
                const left = Math.max(0, labelBounds.left - heroBounds.left);
                this.$el.style.position = 'absolute';
                this.$el.style.left = left + 'px';
                this.$el.style.top = (labelBounds.bottom - heroBounds.top + 6) + 'px';
                this.$el.style.maxWidth = Math.max(180, heroBounds.width - left - 16) + 'px';
            },
            async removeOwnCondition(condition) {
                if (!condition || condition._sbLeaving || this.removingKey || !this.removeUrl || !this.combatId) return;
                // Exaustão é tratada no módulo próprio e invisibilidade não é aplicada aqui.
                if (condition.key === 'exhaustion' || condition.key === 'invisible') return;
                if (!window.confirm('Encerrar a condição ' + condition.name + ' na sua ficha?')) return;
                this.removingKey = condition.key;
                this.removalError = '';
                try {
                    const response = await fetch(this.removeUrl, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            remove_combat_condition: condition.key,
                            condition_combat_id: this.combatId
                        })
                    });
                    if (!response.ok) {
                        throw new Error(response.status === 419
                            ? 'Sessão expirada. Atualize a página.'
                            : 'Não foi possível remover a condição (' + response.status + ').');
                    }
                    const result = await response.json();
                    if (!result.success) throw new Error('O servidor não confirmou a remoção.');
                    this.activeKey = null;
                    await this.refresh();
                    window.dispatchEvent(new CustomEvent('spellbound:conditions-changed'));
                } catch (error) {
                    this.removalError = error.message || 'Não foi possível remover a condição.';
                } finally {
                    this.removingKey = null;
                }
            },
            async refresh() {
                if (this.refreshing || !this.stateUrl || document.hidden) return;
                this.refreshing = true;
                try {
                    const response = await fetch(this.stateUrl, {
                        headers: {'Accept': 'application/json'},
                        credentials: 'same-origin'
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    const incoming = Array.isArray(data.conditions) ? data.conditions : [];
                    const incomingKeys = new Set(incoming.map(condition => condition.key));
                    // O servidor remove a condição imediatamente; o chip só permanece por 400ms para dissipar.
                    this.conditions.filter(condition => condition.key !== 'invisible' && !incomingKeys.has(condition.key))
                        .forEach(condition => {
                            if (this.tokenTimers[condition.key]) return;
                            this.leavingConditions = [...this.leavingConditions, {...condition, _sbLeaving: true}];
                            this.tokenTimers[condition.key] = window.setTimeout(() => {
                                this.leavingConditions = this.leavingConditions.filter(item => item.key !== condition.key);
                                delete this.tokenTimers[condition.key];
                            }, 400);
                        });
                    incomingKeys.forEach(key => {
                        if (this.tokenTimers[key]) {
                            window.clearTimeout(this.tokenTimers[key]);
                            delete this.tokenTimers[key];
                        }
                    });
                    this.leavingConditions = this.leavingConditions.filter(condition => !incomingKeys.has(condition.key));
                    this.conditions = incoming;
                    this.combatId = data.combat_id;
                    this.round = data.round;
                    this.turn = data.turn;
                    this.isCurrentTurn = !!data.is_current_turn;
                    this.syncVisualStates(this.visibleConditions().map(c => c.key));
                    this.updatePortrait();
                    this.$nextTick(() => this.positionBadges());
                    const turnToken = [this.combatId, this.round, this.turn].join(':');
                    if (this.isCurrentTurn && this.conditions.some(c => c.key === 'burning') && turnToken !== this.lastBurningAlert) {
                        this.lastBurningAlert = turnToken;
                        this.activeKey = 'burning';
                    }
                    if (this.activeKey && !this.visibleConditions().some(c => c.key === this.activeKey)) {
                        this.activeKey = null;
                    }
                } catch (_error) {
                    // Falhas de rede não apagam as condições já exibidas.
                } finally {
                    this.refreshing = false;
                }
            },
            createParalysisLayer() {
                const layer = document.createElement('div');
                layer.className = 'sb-condition-paralysis-layer';
                layer.setAttribute('aria-hidden', 'true');
                const leftBolt = document.createElement('span');
                leftBolt.className = 'sb-condition-paralysis-bolt sb-condition-paralysis-bolt--left';
                layer.appendChild(leftBolt);
                const rightBolt = document.createElement('span');
                rightBolt.className = 'sb-condition-paralysis-bolt sb-condition-paralysis-bolt--right';
                layer.appendChild(rightBolt);
                for (let index = 0; index < 11; index++) {
                    const particle = document.createElement('span');
                    particle.className = 'sb-condition-paralysis-particle';
                    const leftSide = index % 2 === 0;
                    const x = leftSide ? 4 + Math.random() * 20 : 76 + Math.random() * 20;
                    particle.style.setProperty('--sb-particle-x', x.toFixed(1) + '%');
                    particle.style.setProperty('--sb-particle-bottom', (3 + Math.random() * 39).toFixed(1) + '%');
                    particle.style.setProperty('--sb-particle-size', (1.9 + Math.random() * 1.8).toFixed(1) + 'px');
                    particle.style.setProperty('--sb-particle-drift', ((Math.random() - .5) * 15).toFixed(1) + 'px');
                    particle.style.setProperty('--sb-particle-duration', (4.4 + Math.random() * 2.5).toFixed(2) + 's');
                    particle.style.setProperty('--sb-particle-delay', '-' + (Math.random() * 6.5).toFixed(2) + 's');
                    layer.appendChild(particle);
                }
                return layer;
            },
            createHeartLayer() {
                const layer = document.createElement('div');
                layer.className = 'sb-condition-heart-layer';
                const count = 4 + Math.floor(Math.random() * 3);
                for (let i = 0; i < count; i++) {
                    const heart = document.createElement('span');
                    heart.className = 'sb-condition-heart';
                    heart.textContent = '♡';
                    heart.style.left = (12 + Math.random() * 72).toFixed(1) + '%';
                    heart.style.setProperty('--sb-heart-delay', '-' + (Math.random() * 4).toFixed(2) + 's');
                    heart.style.setProperty('--sb-heart-duration', (4.2 + Math.random() * 2.0).toFixed(2) + 's');
                    heart.style.setProperty('--sb-heart-scale', (0.85 + Math.random() * 0.4).toFixed(2));
                    layer.appendChild(heart);
                }
                return layer;
            },
            createExtraVisualLayer(key) {
                const layer = document.createElement('div');
                layer.className = 'sb-condition-extra-layer sb-condition-extra--' + key;
                layer.dataset.sbVisualKey = key;
                layer.setAttribute('aria-hidden', 'true');
                if (key === 'grappled') {
                    const leftHand = document.createElement('span');
                    leftHand.className = 'sb-condition-grappled-hand sb-condition-grappled-hand--left';
                    layer.appendChild(leftHand);
                    const rightHand = document.createElement('span');
                    rightHand.className = 'sb-condition-grappled-hand sb-condition-grappled-hand--right';
                    layer.appendChild(rightHand);
                }

                if (key === 'petrified') {
                    const positions = [
                        [-7, -4, 46, -18], [53, 2, 37, 10], [-9, 55, 52, 8], [56, 58, 40, -12]
                    ];
                    positions.forEach(([left, top, width, rotate], i) => {
                        const crack = document.createElement('span');
                        crack.className = 'sb-condition-crack';
                        crack.style.left = left + '%';
                        crack.style.top = top + '%';
                        crack.style.width = width + '%';
                        crack.style.setProperty('--sb-crack-rotation', rotate + 'deg');
                        crack.style.setProperty('--sb-crack-delay', (i * .19).toFixed(2) + 's');
                        crack.style.backgroundImage = `url("${this.conditionAssetsUrl}petrified-crack-${String(i+1).padStart(2, '0')}.png")`;
                        layer.appendChild(crack);
                    });
                }
                if (key === 'poisoned') {
                    for (let i = 0; i < 9; i++) {
                        const mote = document.createElement('span');
                        mote.className = 'sb-condition-poison-mote';
                        mote.style.setProperty('--sb-poison-x', (20 + (i*19)%62) + '%');
                        mote.style.setProperty('--sb-poison-bottom', (5 + (i*11)%43) + '%');
                        mote.style.setProperty('--sb-poison-delay', (-i*.53).toFixed(2) + 's');
                        mote.style.setProperty('--sb-poison-duration', (4.3 + (i%4)*.55).toFixed(2) + 's');
                        mote.style.setProperty('--sb-poison-shift', ((i%2 ? 1 : -1)*(14+i*2)) + 'px');
                        layer.appendChild(mote);
                    }
                }
                if (key === 'restrained') {
                    const chains = document.createElement('span');
                    chains.className = 'sb-condition-chain-frame';
                    const sprite = document.createElement('span');
                    sprite.className = 'sb-condition-chain-sprite';
                    chains.appendChild(sprite);
                    layer.appendChild(chains);
                }
                if (key === 'stunned') {
                    const orbit = document.createElement('span');
                    orbit.className = 'sb-condition-stun-orbit';
                    // Seis estrelas independentes no mesmo percurso elíptico,
                    // cada uma iniciando em uma fase diferente. Nenhuma gira sobre si.
                    const starCount = 6;
                    const orbitDuration = 5.4;
                    for (let i = 0; i < starCount; i++) {
                        const star = document.createElement('span');
                        star.className = 'sb-condition-stun-star';
                        star.textContent = i % 3 === 0 ? '✦' : '✧';
                        star.style.setProperty('--sb-star-delay',
                            (-i * orbitDuration / starCount).toFixed(2) + 's');
                        orbit.appendChild(star);
                    }
                    layer.appendChild(orbit);
                }
                if (key === 'burning') {
                    const flames = document.createElement('span');
                    flames.className = 'sb-condition-burning-flames';
                    layer.appendChild(flames);
                    for (let i = 0; i < 14; i++) {
                        const ember = document.createElement('span');
                        ember.className = 'sb-condition-burning-ember';
                        ember.style.setProperty('--sb-ember-x', (6 + (i * 7.1) % 88).toFixed(1) + '%');
                        ember.style.setProperty('--sb-ember-bottom', (4 + (i * 3.4) % 18).toFixed(1) + '%');
                        ember.style.setProperty('--sb-ember-size', (4.2 + (i % 4) * 1.15).toFixed(1) + 'px');
                        ember.style.setProperty('--sb-ember-duration', (2.4 + (i % 5) * .28).toFixed(2) + 's');
                        ember.style.setProperty('--sb-ember-delay', (-i * .23).toFixed(2) + 's');
                        ember.style.setProperty('--sb-ember-drift', (((i % 2 ? 1 : -1) * (8 + (i % 4) * 4))).toFixed(1) + 'px');
                        layer.appendChild(ember);
                    }
                }
                return layer;
            },
            updateExtraVisualLayers(media, renderKeys) {
                this.managedVisualKeys().filter(key => key !== 'paralyzed').forEach(key => {
                    let layer = media.querySelector(`.sb-condition-extra--${key}`);
                    if (renderKeys.includes(key)) {
                        if (!layer) {
                            layer = this.createExtraVisualLayer(key);
                            media.appendChild(layer);
                        }
                        const phase = this.getVisualPhase(key) || 'idle';
                        layer.className = `sb-condition-extra-layer sb-condition-extra--${key} ${phase === 'enter' ? 'is-entering' : phase === 'exit' ? 'is-exiting' : 'is-idle'}`;
                    } else if (layer) {
                        layer.remove();
                    }
                });
            },
            alignPronePortrait(media = null) {
                if (!this.getVisualPhase('prone')) return;
                if (!media) {
                    const hero = this.$el.closest('.character-sheet-hero')?.querySelector('.hero-header-v22');
                    media = hero?.querySelector('.hero-data-portrait-media');
                }
                const photo = media?.querySelector('.hero-data-portrait-image');
                if (!photo) return;

                // Ler as medidas sem a rotação/animação atual. Restaurar logo
                // depois: não modifica o layout normal nem as outras condições.
                const previousAnimation = [photo.style.getPropertyValue('animation'), photo.style.getPropertyPriority('animation')];
                const previousTransform = [photo.style.getPropertyValue('transform'), photo.style.getPropertyPriority('transform')];
                let rectangle, frame;
                try {
                    photo.style.setProperty('animation', 'none', 'important');
                    photo.style.setProperty('transform', 'none', 'important');
                    rectangle = photo.getBoundingClientRect();
                    frame = media.getBoundingClientRect();
                } finally {
                    if (previousAnimation[0]) photo.style.setProperty('animation', previousAnimation[0], previousAnimation[1]);
                    else photo.style.removeProperty('animation');
                    if (previousTransform[0]) photo.style.setProperty('transform', previousTransform[0], previousTransform[1]);
                    else photo.style.removeProperty('transform');
                }
                if (!rectangle.width || !rectangle.height || !frame.width || !frame.height) return;

                const scale = Math.min(1, (frame.width - 2) / rectangle.height, (frame.height - 2) / rectangle.width);
                const left = rectangle.left - frame.left;
                const top = rectangle.top - frame.top;
                const shiftX = frame.width - (left + rectangle.width / 2 + rectangle.height * scale / 2);
                const shiftY = frame.height / 2 - (top + rectangle.height / 2) + 10;
                photo.style.setProperty('--sb-prone-x', shiftX.toFixed(2) + 'px');
                photo.style.setProperty('--sb-prone-y', shiftY.toFixed(2) + 'px');
                photo.style.setProperty('--sb-prone-scale', scale.toFixed(5));
            },
            updatePortrait(clear = false) {
                const hero = this.$el.closest('.character-sheet-hero')?.querySelector('.hero-header-v22');
                if (!hero) return;
                const activeKeys = clear ? [] : this.conditions.filter(c => c.key !== 'invisible').map(c => c.key).sort();
                const renderKeys = clear ? [] : this.renderedPortraitKeys(activeKeys);
                const signature = clear
                    ? 'clear'
                    : renderKeys.join(' ') + '|' + this.managedVisualKeys()
                        .map(key => key + ':' + this.getVisualPhase(key)).join('|');
                hero.dataset.sbConditions = renderKeys.join(' ');
                hero.dataset.sbGrappledPhase = this.getVisualPhase('grappled');
                hero.dataset.sbParalyzedPhase = this.getVisualPhase('paralyzed');
                this.managedVisualKeys().forEach(key => {
                    hero.dataset['sb' + key.charAt(0).toUpperCase() + key.slice(1) + 'Phase'] = this.getVisualPhase(key);
                });
                const media = hero.querySelector('.hero-data-portrait-media');
                if (!media) return;
                const photo = media.querySelector('.hero-data-portrait-image');
                if (renderKeys.includes('prone')) {
                    // Mede só quando a condição entra (ou quando a mídia muda), evitando a sensação de “cair duas vezes”.
                    const hasProneTransform = !!photo?.style.getPropertyValue('--sb-prone-x');
                    const needsProneAlignment = media !== this.appliedPortraitMedia || !hasProneTransform || !this.proneWasActive;
                    if (needsProneAlignment) this.alignPronePortrait(media);
                    this.proneWasActive = true;
                } else if (photo) {
                    ['--sb-prone-x', '--sb-prone-y', '--sb-prone-scale'].forEach(name => photo.style.removeProperty(name));
                    this.proneWasActive = false;
                }
                if (!clear && signature === this.appliedPortraitSignature && media === this.appliedPortraitMedia) return;
                this.appliedPortraitSignature = signature;
                this.appliedPortraitMedia = media;

                let paralysisLayer = media.querySelector('.sb-condition-paralysis-layer');
                if (renderKeys.includes('paralyzed')) {
                    if (!paralysisLayer) {
                        paralysisLayer = this.createParalysisLayer();
                        media.appendChild(paralysisLayer);
                    }
                    const phase = this.getVisualPhase('paralyzed') || 'idle';
                    paralysisLayer.className = 'sb-condition-paralysis-layer ' + (phase === 'enter' ? 'is-entering' : phase === 'exit' ? 'is-exiting' : 'is-idle');
                } else if (paralysisLayer) {
                    paralysisLayer.remove();
                }

                let heartLayer = media.querySelector('.sb-condition-heart-layer');
                if (renderKeys.includes('charmed')) {
                    if (!heartLayer) {
                        heartLayer = this.createHeartLayer();
                        media.appendChild(heartLayer);
                    }
                    const heartPhase = this.getVisualPhase('charmed') || 'idle';
                    heartLayer.className = 'sb-condition-heart-layer ' + (heartPhase === 'enter' ? 'is-entering' : heartPhase === 'exit' ? 'is-exiting' : 'is-idle');
                } else if (heartLayer) {
                    heartLayer.remove();
                }
                // Extra visuals are optional; the established effects above still work.
                this.updateExtraVisualLayers(media, renderKeys);
            },
            symbol(key) {
                return ({blinded: '◉', charmed: '♡', deafened: '', exhaustion: '−', frightened: '◆',
                    grappled: '', incapacitated: '⊘', invisible: '', paralyzed: '',
                    petrified: '▤', poisoned: '☣', prone: '↘', restrained: '',
                    stunned: '✦', unconscious: '☾', burning: '♨'})[key] || '•';
            },
            visibleConditions() {
                return this.conditions.filter(condition => condition.key !== 'invisible');
            },
            shownConditions() {
                const active = this.visibleConditions();
                const activeKeys = new Set(active.map(condition => condition.key));
                return [...active, ...this.leavingConditions.filter(condition => !activeKeys.has(condition.key))];
            },
            escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, char => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[char] || char));
            },
            renderEffect(condition) {
                const effect = String(condition?.effect || '');
                const entries = Object.entries(this.relatedConditions).filter(([key]) => key !== condition?.key);
                const byName = new Map(entries.map(([, meta]) => [meta.name.toLocaleLowerCase('pt-BR'), meta]));
                const names = entries.map(([, meta]) => meta.name);
                if (!names.length) return this.escapeHtml(effect);
                const regex = new RegExp('(^|[^\p{L}])(' + names.join('|') + ')(?=[^\p{L}]|$)', 'giu');
                let output = '';
                let cursor = 0;
                for (const match of effect.matchAll(regex)) {
                    const at = match.index;
                    const before = match[1];
                    const word = match[2];
                    output += this.escapeHtml(effect.slice(cursor, at));
                    output += this.escapeHtml(before);
                    const meta = byName.get(word.toLocaleLowerCase('pt-BR'));
                    if (meta) {
                        output += '<span tabindex=\'0\' class=\'sb-condition-inline-ref\'>'
                            + this.escapeHtml(word)
                            + '<span class=\'sb-condition-inline-ref__tip\'><strong>'
                            + this.escapeHtml(meta.name)
                            + '</strong>' + this.escapeHtml(meta.summary) + '</span></span>';
                    } else {
                        output += this.escapeHtml(word);
                    }
                    cursor = at + before.length + word.length;
                }
                return output + this.escapeHtml(effect.slice(cursor));
            }
        };
    };
</script>
@endonce
<div x-data="sbCombatConditions()" data-state-url="{{ route('characters.combat-conditions.show', ['character' => $character->id]) }}"
    data-remove-url="{{ route('characters.combat.update', ['character' => $character->id]) }}"
    data-conditions-assets-url="{{ asset('images/conditions') }}/"
    x-show="shownConditions().length > 0" x-cloak
    class="sb-condition-bar" aria-label="Condições ativas no personagem">
    <template x-for="condition in shownConditions()" :key="condition.key">
        <button type="button" class="sb-condition-token"
            :class="{'sb-condition-token--blinded': condition.key === 'blinded', 'is-leaving': !!condition._sbLeaving}"
            x-on:click="!condition._sbLeaving && (activeKey = activeKey === condition.key ? null : condition.key)"
            :aria-expanded="activeKey === condition.key"
            :title="condition.name + ' — ver efeitos e duração'">
            <span class="sb-condition-token__symbol" aria-hidden="true">
                <template x-if="condition.key === 'blinded'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M10.7 5.1A11 11 0 0 1 12 5c5 0 8.5 5 9.5 7a14 14 0 0 1-2.4 3.1" />
                        <path d="M6.2 6.2A14 14 0 0 0 2.5 12c1 2 4.5 7 9.5 7 2.2 0 4.1-.9 5.9-2" />
                        <path d="M10 10a3 3 0 0 0 4 4" />
                        <path d="M3 3 21 21" />
                    </svg>
                </template>
                <template x-if="condition.key === 'deafened'">
                    <span class="sb-condition-token__mask" style="-webkit-mask-image: url('{{ asset('images/conditions/deafened-ear-mask.png') }}'); mask-image: url('{{ asset('images/conditions/deafened-ear-mask.png') }}');"></span>
                </template>
                <template x-if="condition.key === 'grappled'">
                    <span class="sb-condition-token__mask sb-condition-token__mask--large" style="-webkit-mask-image: url('{{ asset('images/conditions/grappled-hand-token-mask.png') }}'); mask-image: url('{{ asset('images/conditions/grappled-hand-token-mask.png') }}');"></span>
                </template>
                                <template x-if="condition.key === 'paralyzed'">
                    <span class="sb-condition-token__mask sb-condition-token__mask--large" style="-webkit-mask-image: url('{{ asset('images/conditions/paralyzed-figure-mask.png') }}'); mask-image: url('{{ asset('images/conditions/paralyzed-figure-mask.png') }}');"></span>
                </template>
                <template x-if="condition.key === 'prone'">
                    <span class="sb-condition-token__mask" style="-webkit-mask-image: url('{{ asset('images/conditions/prone-fallen-mask.png') }}'); mask-image: url('{{ asset('images/conditions/prone-fallen-mask.png') }}');"></span>
                </template>
                <template x-if="condition.key === 'restrained'">
                    <span class="sb-condition-token__mask sb-condition-token__mask--large" style="-webkit-mask-image: url('{{ asset('images/conditions/restrained-chain-token-mask.png') }}'); mask-image: url('{{ asset('images/conditions/restrained-chain-token-mask.png') }}');"></span>
                </template>
                <template x-if="condition.key !== 'blinded' && condition.key !== 'deafened' && condition.key !== 'grappled' && condition.key !== 'paralyzed' && condition.key !== 'prone' && condition.key !== 'restrained'"><span x-text="symbol(condition.key)"></span></template>
            </span>
            <span x-text="condition.name"></span>
            <span x-show="condition.key === 'exhaustion'" x-text="condition.level" class="opacity-70"></span>
        </button>
    </template>
    <template x-for="condition in visibleConditions()" :key="'detail-' + condition.key">
        <section x-show="activeKey === condition.key" x-cloak
            x-transition:enter="sb-detail-appear"
            x-transition:enter-start="sb-detail-appear-start"
            x-transition:enter-end="sb-detail-appear-end"
            x-transition:leave="sb-detail-disappear"
            x-transition:leave-start="sb-detail-disappear-start"
            x-transition:leave-end="sb-detail-disappear-end"
            class="sb-condition-detail" aria-live="polite">
            <div class="flex items-center justify-between gap-2">
                <h3 x-text="condition.name"></h3>
                <button type="button" class="sb-condition-detail__close" x-on:click="activeKey = null" aria-label="Fechar detalhes">×</button>
            </div>
            <p x-html="renderEffect(condition)"></p>
            <small x-show="condition.key !== 'exhaustion'" x-text="condition.duration_turns ? (condition.elapsed_turns + ' de ' + condition.duration_turns + ' turnos próprios transcorridos') : (condition.elapsed_turns + ' turnos próprios transcorridos · duração indefinida')"></small>
            <small x-show="condition.source" x-text="'Origem: ' + condition.source"></small>
            <p x-show="condition.key === 'burning' && isCurrentTurn" class="sb-condition-turn-warning">É seu turno: confirme com o Mestre quanto dano de fogo receber.</p>
            <div x-show="condition.key !== 'exhaustion' && condition.key !== 'invisible'" class="sb-condition-self-action">
                <button type="button" class="sb-condition-self-action__button"
                    :disabled="!!removingKey" x-on:click="removeOwnCondition(condition)">
                    <span x-text="removingKey === condition.key ? 'Removendo...' : 'Encerrar condição'"></span>
                </button>
                <span class="sb-condition-self-action__hint">Você também pode encerrar essa condição na sua ficha.</span>
            </div>
            <p x-show="removalError" x-text="removalError" x-cloak role="alert" class="sb-condition-self-action__error"></p>
        </section>
    </template>
</div>
