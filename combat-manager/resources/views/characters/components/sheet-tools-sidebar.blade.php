{{-- V66.2.168 — ferramentas exclusivas da ficha, integradas visualmente ao navegador esquerdo. --}}
<style>
            .sb-sheet-tools-sidebar {
                position:fixed;left:7px;top:min(var(--sb-sheet-tools-top,174px),calc(100dvh - 153px));
                z-index:91;width:40px;display:flex;flex-direction:column;align-items:center;gap:6px;
                padding-top:10px;border-top:1px solid rgba(105,55,35,.14);
            }
            .sb-sheet-tools-sidebar .sb-nav83__link {
                width:40px;height:40px;min-height:40px;flex:none;display:grid;place-items:center;
                border:0;border-radius:12px;background:transparent;color:#987959;
                cursor:pointer;position:relative;transition:background .18s ease,color .18s ease;
            }
            .sb-sheet-tools-sidebar .sb-nav83__link:hover,
            .sb-sheet-tools-sidebar .sb-nav83__link:focus-visible {background:rgba(107,33,25,.07);color:#6b2119}
            .sb-sheet-tools-sidebar .sb-nav83__link.is-open {background:rgba(107,33,25,.11);color:#6b2119}
            .sb-sheet-tools-sidebar .sb-nav83__link.is-return-target {box-shadow:0 0 0 2px rgba(140,75,58,.28)}
            .sb-sheet-tools-sidebar .sb-nav83__link:focus-visible {outline:2px solid #b08a61;outline-offset:1px}
            .sb-sheet-tools-sidebar .sb-nav83__link svg {width:22px;height:22px;stroke-width:1.85;fill:none;stroke:currentColor}
            .sb-sheet-tools-sidebar__dice-icon {
                width:29px;height:29px;display:block;background-color:currentColor;
                -webkit-mask:url('{{ asset('images/navigation/d20-top-view-icon.png') }}') center/contain no-repeat;
                mask:url('{{ asset('images/navigation/d20-top-view-icon.png') }}') center/contain no-repeat;
            }
            .sb-sheet-tools-sidebar .sb-nav83__tooltip {
                position:absolute;left:51px;top:50%;transform:translate(-3px,-50%);
                white-space:nowrap;opacity:0;pointer-events:none;padding:7px 10px;
                border:1px solid #e1d1bd;border-radius:8px;background:#fffaf2;
                box-shadow:0 7px 20px rgba(48,27,18,.12);color:#6b2119;
                font:700 11px/1.2 Georgia,serif;transition:opacity .14s ease,transform .14s ease;
            }
            .sb-sheet-tools-sidebar .sb-nav83__link:hover .sb-nav83__tooltip,
            .sb-sheet-tools-sidebar .sb-nav83__link:focus-visible .sb-nav83__tooltip {
                opacity:1;transform:translate(0,-50%);
            }
            @media(max-height:460px){.sb-sheet-tools-sidebar{gap:2px;padding-top:4px}.sb-sheet-tools-sidebar .sb-nav83__link{height:34px;min-height:34px}}
            /* V66.2.171: os ícones acompanham o tom ativo da ficha, não a paleta vinho fixa. */
            .sb-sheet-tools-sidebar .sb-nav83__link{
                color:color-mix(in srgb,var(--sb-tools-accent,#896650) 75%,#849086 25%);
                background:transparent;
            }
            .sb-sheet-tools-sidebar .sb-nav83__link:hover,
            .sb-sheet-tools-sidebar .sb-nav83__link:focus-visible,
            .sb-sheet-tools-sidebar .sb-nav83__link.is-open{
                color:var(--sb-tools-accent,#896650);
                background:color-mix(in srgb,var(--sb-tools-accent,#896650) 11%,transparent);
            }
            .sb-sheet-tools-sidebar .sb-nav83__link.is-return-target{
                box-shadow:0 0 0 2px color-mix(in srgb,var(--sb-tools-accent,#896650) 31%,transparent);
            }
            .sb-sheet-tools-sidebar .sb-nav83__link:focus-visible{
                outline-color:color-mix(in srgb,var(--sb-tools-accent,#896650) 65%,white 35%);
            }
            .sb-sheet-tools-sidebar .sb-nav83__tooltip{
                color:var(--sb-tools-accent,#896650);
                border-color:color-mix(in srgb,var(--sb-tools-accent,#896650) 22%,#e3dcd2 78%);
            }
            .sb-nav83.sb-nav83--sheet-tools-theme .sb-nav83__link{
                color:color-mix(in srgb,var(--sb-tools-accent,#896650) 70%,#899188 30%);
            }
            .sb-nav83.sb-nav83--sheet-tools-theme .sb-nav83__link:hover,
            .sb-nav83.sb-nav83--sheet-tools-theme .sb-nav83__link:focus-visible,
            .sb-nav83.sb-nav83--sheet-tools-theme .sb-nav83__link--active{
                color:var(--sb-tools-accent,#896650)!important;
                background:color-mix(in srgb,var(--sb-tools-accent,#896650) 10%,transparent)!important;
            }
            .sb-nav83.sb-nav83--sheet-tools-theme .sb-nav83__link--active::before{
                background:var(--sb-tools-accent,#896650);
            }
</style>
@if(request()->routeIs('characters.show'))
    <div class="sb-sheet-tools-sidebar" style="--sb-sheet-tools-top:{{ session('dashboard_mode') === 'master' ? '337px' : '174px' }}"
         x-data="{
             diceOpen:false,calculatorOpen:false,returnHover:false,
             init(){
                 this.$nextTick(()=>{
                     const sheet=document.querySelector('.character-sheet-theme[data-theme]');
                     const nav=document.querySelector('.sb-nav83');
                     const source=sheet ? getComputedStyle(sheet) : null;
                     const logo=nav?.querySelector('.sb-nav83__logo');
                     const logoColor=logo ? getComputedStyle(logo).backgroundColor : '';
                     const accent=source?.getPropertyValue('--theme-accent-strong')?.trim()
                         || source?.getPropertyValue('--theme-accent')?.trim() || logoColor || '#896650';
                     this.$el.style.setProperty('--sb-tools-accent',accent);
                     if(nav){
                         nav.style.setProperty('--sb-tools-accent',accent);
                         nav.classList.add('sb-nav83--sheet-tools-theme');
                     }
                 });
             }
         }"
         x-on:spellbound:tray-state.window="diceOpen=!!$event.detail.open"
         x-on:spellbound:calc-state.window="calculatorOpen=!!$event.detail.open"
         x-on:spellbound:tray-return-hover.window="returnHover=!!$event.detail.hover"
         aria-label="Ferramentas da ficha" role="group">
        <button type="button" class="sb-nav83__link" data-sb-tool="dice"
                x-bind:class="{'is-open':diceOpen,'is-return-target':returnHover}"
                x-on:click="window.dispatchEvent(new CustomEvent('spellbound:toggle-dice-tray'))"
                x-bind:aria-expanded="diceOpen?'true':'false"
                aria-label="Abrir ou fechar bandeja de dados" title="Bandeja de dados">
            <span class="sb-sheet-tools-sidebar__dice-icon" aria-hidden="true"></span>
            <span class="sb-nav83__tooltip">Bandeja de dados</span>
        </button>
        <button type="button" class="sb-nav83__link" data-sb-tool="calculator"
                x-bind:class="{'is-open':calculatorOpen}"
                x-on:click="window.dispatchEvent(new CustomEvent('spellbound:toggle-calculator'))"
                x-bind:aria-expanded="calculatorOpen?'true':'false"
                aria-label="Abrir ou fechar calculadora" title="Calculadora de dados">
            <svg viewBox="0 0 24 24" aria-hidden="true" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2.5" width="14" height="19" rx="2.3"/><rect x="8" y="5.5" width="8" height="4" rx=".8"/><path d="M8.5 13h1m4.5 0h1m-6.5 3.5h1m4.5 0h1M8.5 20h1m4.5 0h1"/></svg>
            <span class="sb-nav83__tooltip">Calculadora de dados</span>
        </button>
    </div>
@endif
