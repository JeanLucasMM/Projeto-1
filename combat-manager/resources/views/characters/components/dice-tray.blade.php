@props(['characterTheme' => 'spellbound'])

{{-- V66.2.167 — acesso pela navegação esquerda; bandeja arrastável, feltro e física preservados. --}}
@once
    @push('styles')
        <style>
            .sb-dice-dock{position:fixed;right:clamp(12px,2vw,24px);bottom:clamp(90px,15vh,158px);z-index:295;pointer-events:none;color:var(--theme-text-primary);font-family:system-ui,-apple-system,"Segoe UI",sans-serif}
            .sb-dice-launcher{position:relative;display:grid;place-items:center;width:48px;height:48px;margin-left:auto;pointer-events:auto;border:1px solid var(--theme-border-strong,var(--theme-border));border-radius:16px;background:var(--theme-modal-surface,var(--theme-surface-card));box-shadow:0 12px 30px rgba(0,0,0,.2),inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 35%,transparent);color:var(--theme-text-strong);cursor:pointer;transition:background .18s ease,border-color .18s ease,box-shadow .18s ease,transform .2s cubic-bezier(.2,.8,.2,1)}
            .sb-dice-launcher .sb-poly-die{pointer-events:none}
            .sb-dice-launcher:hover,.sb-dice-launcher:focus-visible{background:var(--theme-card-hover);border-color:var(--theme-accent);box-shadow:0 10px 28px rgba(0,0,0,.25),0 0 0 3px color-mix(in srgb,var(--theme-accent) 17%,transparent);transform:translateY(-2px)}
            .sb-dice-launcher.is-return-target{border-color:var(--theme-accent-strong,var(--theme-accent));background:var(--theme-card-hover);box-shadow:0 0 0 5px color-mix(in srgb,var(--theme-accent) 20%,transparent),0 0 25px color-mix(in srgb,var(--theme-accent) 31%,transparent);transform:scale(1.08)}
            .sb-dice-launcher:focus-visible,.sb-dice-choice:focus-visible,.sb-dice-dock-close:focus-visible{outline:2px solid var(--theme-input-focus,var(--theme-accent));outline-offset:2px}
            .sb-dice-dock-count{position:absolute;top:-6px;right:-6px;display:grid;place-items:center;min-width:19px;height:19px;padding:0 4px;border:1px solid var(--theme-modal-border,var(--theme-border));border-radius:999px;background:var(--theme-accent);color:var(--theme-button-text,#fff);font:800 10px/1 system-ui,sans-serif}
            .sb-dice-dock-panel{position:absolute;bottom:60px;right:0;width:min(386px,calc(100vw - 28px));pointer-events:auto;overflow:hidden;border:1px solid color-mix(in srgb,var(--theme-modal-border,var(--theme-border)) 88%,var(--theme-accent) 12%);border-radius:18px;background:linear-gradient(180deg,color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 94%,white 6%),color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 98%,black 2%));box-shadow:0 22px 54px rgba(0,0,0,.28),0 3px 10px rgba(0,0,0,.12),inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 22%,transparent),inset 0 -1px 0 rgba(0,0,0,.08);backdrop-filter:blur(15px);animation:sb-dice-panel-in .22s cubic-bezier(.15,.85,.25,1) both}
            @keyframes sb-dice-panel-in{from{opacity:0;transform:translateY(9px) scale(.975)}to{opacity:1;transform:translateY(0) scale(1)}}
            .sb-dice-dock-panel.is-return-target{border-color:var(--theme-accent-strong,var(--theme-accent));box-shadow:0 0 0 3px color-mix(in srgb,var(--theme-accent) 24%,transparent),0 22px 54px rgba(0,0,0,.28)}
            .sb-dice-dock-head{display:flex;align-items:center;gap:9px;padding:12px 13px 10px;border-bottom:1px solid var(--theme-border-soft)}
            .sb-dice-dock-head-copy{min-width:0;flex:1}
            .sb-dice-dock-kicker{display:block;font:800 8px/1.2 system-ui,sans-serif;letter-spacing:.13em;text-transform:uppercase;color:var(--theme-label)}
            .sb-dice-dock-title{display:block;margin-top:2px;font:900 16px/1.1 Georgia,serif;color:var(--theme-text-strong)}
            .sb-dice-dock-close{width:27px;height:27px;border:1px solid var(--theme-border-soft);border-radius:8px;background:var(--theme-surface-raised);color:var(--theme-text-secondary);cursor:pointer}
            .sb-dice-dock-close:hover{background:var(--theme-card-hover);color:var(--theme-text-strong)}
            /* Estojo com borda elevada e interior tipo feltro, em todos os temas. */
            .sb-dice-dock-head{padding:12px 14px;border-bottom:0}
            .sb-dice-dock-title{font-size:17px}
            .sb-dice-help-trigger{display:grid;place-items:center;flex:none;width:23px;height:23px;border:1px solid var(--theme-border-soft);border-radius:50%;background:var(--theme-surface-raised);font:900 11px/1 Georgia,serif;color:var(--theme-text-secondary);cursor:help;transition:background .17s,color .17s,border-color .17s}
            .sb-dice-help-trigger:hover,.sb-dice-help-trigger:focus-visible{background:var(--theme-card-hover);border-color:var(--theme-accent);color:var(--theme-text-strong)}
            .sb-dice-help-panel{position:absolute;right:13px;top:47px;z-index:5;width:min(317px,calc(100% - 26px));padding:12px 13px;border-radius:11px;border:1px solid var(--theme-border-strong,var(--theme-border));background:var(--theme-modal-surface,var(--theme-surface-card));color:var(--theme-text-primary);box-shadow:0 13px 30px rgba(0,0,0,.3);font:600 10px/1.5 system-ui,sans-serif}
            .sb-dice-help-panel p+p{margin-top:6px}
            .sb-dice-case{position:relative;margin:1px 12px 11px;padding:7px;border-radius:18px;border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 76%,black 24%);background:linear-gradient(145deg,color-mix(in srgb,var(--theme-surface-raised,var(--theme-surface-card)) 76%,black 24%),color-mix(in srgb,var(--theme-surface-raised,var(--theme-surface-card)) 86%,var(--theme-accent) 14%));box-shadow:0 3px 8px rgba(0,0,0,.12),inset 0 2px 1px rgba(255,255,255,.08),inset 0 -2px 4px rgba(0,0,0,.23)}
            .sb-dice-dock-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:3px 5px;position:relative;margin:0;padding:10px;border:1px solid color-mix(in srgb,var(--theme-accent-strong,var(--theme-accent)) 25%,black 75%);border-radius:13px;isolation:isolate;background:radial-gradient(ellipse at 40% 25%,color-mix(in srgb,var(--theme-surface-raised,var(--theme-surface-card)) 82%,var(--theme-accent) 18%),transparent 100%),linear-gradient(125deg,color-mix(in srgb,var(--theme-surface-card) 75%,black 25%),color-mix(in srgb,var(--theme-surface-card) 79%,var(--theme-accent-strong) 21%));box-shadow:inset 0 5px 10px rgba(0,0,0,.22),inset 0 0 0 1px rgba(255,255,255,.035)}
            .sb-dice-dock-grid::before{content:"";position:absolute;inset:0;border-radius:inherit;pointer-events:none;opacity:.09;background:repeating-linear-gradient(33deg,transparent 0 3px,currentColor 3px 3.5px,transparent 3.5px 8px);z-index:-1}
            .sb-dice-choice{position:relative;display:flex;min-width:0;height:66px;align-items:center;justify-content:center;border:1px solid transparent;border-radius:13px;background:transparent;color:var(--theme-text-strong);cursor:grab;touch-action:none;user-select:none;transition:filter .18s ease,transform .18s cubic-bezier(.2,.8,.2,1),border-color .18s ease}
            .sb-dice-choice::before{content:"";position:absolute;inset:6px 8px 5px;border-radius:48%;background:radial-gradient(ellipse at center,rgba(0,0,0,.32),rgba(0,0,0,.15) 58%,transparent 74%);transform:translateY(9px) scale(1.04);pointer-events:none}
            .sb-dice-choice .sb-poly-die{pointer-events:none;position:relative;z-index:1;filter:drop-shadow(0 4px 2px rgba(0,0,0,.3));transition:transform .18s cubic-bezier(.2,.85,.25,1),filter .18s ease}
            .sb-dice-choice.is-percentile{grid-column:span 2}
            .sb-dice-choice-percentile{display:flex;position:relative;z-index:1;gap:1px;align-items:center;justify-content:center;pointer-events:none}
            .sb-dice-choice-label{position:absolute;bottom:1px;left:50%;transform:translateX(-50%);font:800 8px/1 system-ui,sans-serif;letter-spacing:.04em;color:var(--theme-text-secondary);pointer-events:none;opacity:.82}
            .sb-dice-choice:hover,.sb-dice-choice:focus-visible{border-color:color-mix(in srgb,var(--theme-accent) 35%,transparent);filter:brightness(1.12);transform:translateY(-1px)}
            .sb-dice-choice:hover .sb-poly-die{transform:translateY(-3px) scale(1.065);filter:drop-shadow(0 7px 5px rgba(0,0,0,.3)) drop-shadow(0 0 6px color-mix(in srgb,var(--theme-accent) 27%,transparent))}
            .sb-dice-choice:active{cursor:grabbing;transform:translateY(1px)}
            .sb-dice-choice:disabled{cursor:not-allowed;opacity:.35;filter:grayscale(.5)}
            .sb-dice-case-hint{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:6px;color:var(--theme-text-secondary);font:750 8px/1 system-ui,sans-serif;letter-spacing:.09em;text-transform:uppercase;opacity:.75;pointer-events:none}
            .sb-dice-dock-foot{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:3px 13px 12px;color:var(--theme-text-secondary);font:700 10px/1.2 system-ui,sans-serif}
            .sb-dice-dock-roll{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:29px;padding:6px 11px;border:1px solid var(--theme-accent-strong,var(--theme-accent));border-radius:8px;background:var(--theme-button-background,var(--theme-accent));color:var(--theme-button-text,#fff);font:850 10px/1.15 system-ui,sans-serif;cursor:pointer;white-space:nowrap;transition:filter .16s ease,transform .16s ease,box-shadow .16s ease}
            .sb-dice-dock-roll:hover:not(:disabled){filter:brightness(1.1);transform:translateY(-1px);box-shadow:0 4px 12px color-mix(in srgb,var(--theme-accent) 24%,transparent)}
            .sb-dice-dock-roll:disabled{opacity:.42;cursor:not-allowed}
            .sb-dice-dock-actions{display:flex;align-items:center;gap:5px;flex-wrap:wrap;justify-content:flex-end}
            .sb-dice-quick-roll{position:absolute;right:0;bottom:58px;pointer-events:auto;min-height:34px;box-shadow:0 7px 20px rgba(0,0,0,.22)}
            .sb-dice-dock-clean{padding:5px 9px;border:1px solid var(--theme-border-soft);border-radius:7px;background:var(--theme-surface-raised);color:var(--theme-text-strong);font:800 10px system-ui,sans-serif;cursor:pointer}
            .sb-dice-dock-clean:hover{background:var(--theme-card-hover)}
            .sb-dice-dock-clean.is-select-toggle{border-color:color-mix(in srgb,var(--theme-accent) 36%,var(--theme-border-soft));}
            .sb-free-staged-die:focus-visible{outline:2px solid var(--theme-input-focus,var(--theme-accent));outline-offset:2px;border-radius:14px}
            .sb-dice-dock-clean:disabled{opacity:.4;cursor:default}
            .sb-dice-error{padding:5px 13px 11px;color:var(--theme-state-damage,#d45b59);font:750 10px/1.45 system-ui,sans-serif}
            .sb-free-staged-field{position:fixed;inset:0;z-index:302;pointer-events:none;overflow:hidden}
            .sb-free-staged-die{position:absolute;display:flex;align-items:center;justify-content:center;width:75px;height:76px;transform:translate(-50%,-50%);pointer-events:auto;cursor:grab;touch-action:none;user-select:none;opacity:1;filter:drop-shadow(0 9px 13px rgba(0,0,0,.27));transition:opacity .12s ease-in,filter .18s ease;animation:sb-dice-staged-enter .3s cubic-bezier(.15,.84,.2,1.2) both}
            @keyframes sb-dice-staged-enter{from{opacity:.1;scale:.74}to{opacity:1;scale:1}}
            /* Ao voltar do palco, preserva o último quadro sem novo fade ou zoom. */
            .sb-free-staged-die.is-returned{animation:none!important}
            .sb-free-staged-die.is-placed .sb-poly-die{transition:none!important}
            .sb-free-staged-die.is-dragging{animation:none!important}
            .sb-free-staged-die.is-returned .sb-poly-die{transition:none!important}
            .sb-free-staged-die.is-storing{animation:none!important;z-index:5;pointer-events:none}
            .sb-free-staged-die[data-sides="100"]{width:144px}
            /* As metades do d100 permanecem nos dois pontos onde caíram. */
            .sb-free-percentile-pair{position:absolute;inset:0;pointer-events:none}
            .sb-free-percentile-half{position:absolute;left:50%;top:50%;display:inline-flex;align-items:center;justify-content:center;translate:-50% -50%;pointer-events:auto;touch-action:none}
            .sb-free-percentile-half .sb-poly-die{flex:none}
            .sb-free-staged-die[data-sides="100"]{pointer-events:none}
            .sb-free-staged-die.is-dragging .sb-free-percentile-half:first-child{scale:1.015}
            .sb-free-staged-die.is-dragging .sb-free-percentile-half:last-child{scale:.99}
            .sb-free-staged-die:hover{filter:drop-shadow(0 14px 18px rgba(0,0,0,.36)) drop-shadow(0 0 8px color-mix(in srgb,var(--theme-accent) 35%,transparent))}
            .sb-free-staged-die.is-dragging{z-index:3;cursor:grabbing;filter:drop-shadow(0 18px 20px rgba(0,0,0,.4))}
            /* Só o poliedro é visível. A luz do próprio dado indica seleção. */
            .sb-free-staged-die .sb-poly-die{pointer-events:none;transform:translateY(var(--sb-grab-lift,0px)) rotate(var(--sb-grab-tilt,0deg)) scale(var(--sb-grab-scale,1)) scaleX(var(--sb-grab-squash,1));transition:transform .16s cubic-bezier(.15,.75,.3,1),opacity .18s ease,filter .18s ease}
            .sb-free-staged-die .sb-poly-number{rotate:var(--sb-number-counter-angle,0deg)}
            .sb-free-percentile-half .sb-poly-die{transform:translateY(var(--sb-grab-lift,0px)) rotate(calc(var(--sb-grab-tilt,0deg) + var(--sb-piece-angle,0deg))) scale(var(--sb-grab-scale,1)) scaleX(var(--sb-grab-squash,1))}
            .sb-free-percentile-half .sb-poly-number{rotate:var(--sb-piece-counter-angle,0deg)}
            .sb-free-staged-die:not(.is-selected):not(.is-dragging) .sb-poly-die{opacity:.67;filter:saturate(.76)}
            .sb-free-staged-die.is-selected .sb-poly-die{opacity:1;filter:drop-shadow(0 0 6px color-mix(in srgb,var(--theme-accent) 46%,transparent))}
            .sb-free-staged-die.is-dragging .sb-poly-die{transition:none;filter:drop-shadow(0 4px 7px color-mix(in srgb,var(--theme-accent) 24%,transparent))}
            /* Handoff: o dado preparado só desaparece após a animação real iniciar. */
            .sb-free-staged-die.is-launching{animation:none!important;opacity:0!important;pointer-events:none;filter:none}
            .sb-free-staged-die.is-launching .sb-poly-die{transition:none!important}
            @media(max-width:600px){.sb-dice-dock{right:12px;bottom:75px}.sb-dice-launcher{width:43px;height:43px;border-radius:13px}.sb-dice-dock-panel{bottom:53px}.sb-dice-dock-grid{gap:2px 3px;padding:6px}.sb-dice-choice{height:59px}.sb-free-staged-die{width:66px;height:68px}}
            @media(max-width:600px){.sb-dice-dock-actions{gap:4px;width:100%;justify-content:space-between}.sb-dice-dock-foot{gap:7px;align-items:stretch;flex-direction:column}.sb-dice-dock-roll{font-size:9px;padding:6px 8px;flex:1}}
            @media(prefers-reduced-motion:reduce){.sb-dice-dock-panel,.sb-free-staged-die{animation:none!important}.sb-dice-launcher,.sb-dice-choice,.sb-free-staged-die .sb-poly-die{transition:none!important}}

            /* V66.2.164 — Bandeja verdadeira: moldura, feltro e dados sem cartões. */
            .sb-dice-dock-panel{
                width:min(408px,calc(100vw - 24px));border-radius:19px;
                border-color:var(--theme-modal-border,var(--theme-border));
                background:var(--theme-modal-surface,var(--theme-surface-card));
                box-shadow:0 24px 60px rgba(0,0,0,.28),0 5px 15px rgba(0,0,0,.12),
                    inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 25%,transparent);
            }
            .sb-dice-dock-head{padding:13px 16px 12px;gap:10px;}
            .sb-dice-dock-title{font-size:18px;letter-spacing:-.015em;}
            .sb-dice-dock-kicker{font-size:7.5px;letter-spacing:.17em;opacity:.78;}
            .sb-dice-help-trigger,.sb-dice-dock-close{width:26px;height:26px;background:var(--theme-surface-raised);border-radius:9px;}
            .sb-dice-help-trigger{border-radius:50%;}
            .sb-dice-case{
                position:relative;margin:1px 14px 7px;padding:8px;border-radius:21px;
                border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 72%,#3b2726 28%);
                background:linear-gradient(155deg,
                    color-mix(in srgb,var(--theme-accent-strong,#75473d) 24%,#7e6655 76%),
                    color-mix(in srgb,var(--theme-accent-strong,#75473d) 20%,#47352e 80%));
                box-shadow:0 5px 12px rgba(0,0,0,.15),
                    inset 0 2px 1px rgba(255,255,255,.24),inset 0 -3px 3px rgba(0,0,0,.28),
                    0 0 0 1px color-mix(in srgb,var(--theme-paper-strong) 25%,transparent);
            }
            .sb-dice-case::before,.sb-dice-case::after{
                content:"";position:absolute;top:12px;width:4px;height:4px;border-radius:50%;z-index:2;
                background:color-mix(in srgb,var(--theme-accent) 38%,#f2c991 62%);
                box-shadow:inset 0 1px 1px rgba(255,255,255,.38),0 1px 2px rgba(0,0,0,.55);
                pointer-events:none;
            }
            .sb-dice-case::before{left:12px}.sb-dice-case::after{right:12px}
            .sb-dice-dock-grid{
                display:grid;grid-template-columns:repeat(4,minmax(0,1fr));align-items:center;
                min-height:156px;gap:3px 4px;margin:0;padding:11px 12px 10px;
                border:1px solid rgba(0,0,0,.43);border-radius:15px;
                background:
                    radial-gradient(ellipse at 45% 22%,color-mix(in srgb,var(--theme-accent-strong,#674343) 22%,#48515a 78%),transparent 90%),
                    linear-gradient(160deg,
                        color-mix(in srgb,var(--theme-accent-strong,#674343) 29%,#252f38 71%),
                        color-mix(in srgb,var(--theme-accent-strong,#674343) 19%,#18252d 81%));
                box-shadow:inset 0 4px 12px rgba(0,0,0,.42),inset 0 0 0 1px rgba(255,255,255,.045),
                    inset 0 -2px 7px rgba(0,0,0,.18);
            }
            .sb-dice-dock-grid::before{display:none!important;}
            .sb-dice-choice{
                min-width:0;height:68px;position:relative;display:flex;align-items:center;justify-content:center;
                border:0!important;border-radius:14px;background:transparent!important;
                box-shadow:none!important;outline-offset:1px;
                cursor:grab;transform:none;
                transition:transform .2s cubic-bezier(.23,.86,.25,1),filter .2s ease;
            }
            .sb-dice-choice::before{
                content:"";position:absolute;inset:auto 12px 3px;height:17px;border-radius:50%;
                background:radial-gradient(ellipse at center,rgba(0,0,0,.53),rgba(0,0,0,.2) 51%,transparent 76%);
                transform:none;filter:blur(3px);opacity:.82;pointer-events:none;
            }
            .sb-dice-choice:focus-visible{outline:1px solid color-mix(in srgb,var(--theme-accent) 85%,white 15%);}
            .sb-dice-choice:hover,.sb-dice-choice:focus-visible{filter:brightness(1.14);transform:translateY(-3px)}
            .sb-dice-choice:hover .sb-poly-die,.sb-dice-choice:focus-visible .sb-poly-die{
                transform:translateY(-3px) scale(1.045);
                filter:drop-shadow(0 7px 4px rgba(0,0,0,.48)) drop-shadow(0 0 6px color-mix(in srgb,var(--theme-accent) 24%,transparent));
            }
            .sb-dice-choice .sb-poly-die{filter:drop-shadow(0 4px 3px rgba(0,0,0,.35));}
            .sb-dice-choice.is-percentile{grid-column:span 2;}
            .sb-dice-choice-percentile{gap:5px}
            .sb-dice-choice-label{display:none!important}
            .sb-dice-choice:active{transform:translateY(1px) scale(.985)}
            .sb-dice-dock-foot{display:flex;flex-direction:column;align-items:stretch;gap:8px;padding:10px 15px 15px;}
            .sb-dice-dock-foot>span{font:750 9px/1.15 system-ui,sans-serif;letter-spacing:.025em;opacity:.82;}
            .sb-dice-dock-actions{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,.8fr) minmax(0,1.8fr);gap:7px;width:100%;}
            .sb-dice-dock-clean{min-height:32px;white-space:nowrap;border-radius:9px;}
            .sb-dice-dock-roll{min-height:32px;border-radius:9px;}
            .sb-dice-quick-roll{right:0;bottom:58px;}
            @media(max-width:600px){
                .sb-dice-dock-panel{width:min(390px,calc(100vw - 18px))}
                .sb-dice-case{margin-inline:9px;padding:6px;border-radius:17px;}
                .sb-dice-dock-grid{min-height:139px;padding:7px 8px;gap:1px 3px;}
                .sb-dice-choice{height:61px;}
                .sb-dice-dock-foot{padding:8px 10px 12px;gap:7px;}
                .sb-dice-dock-actions{width:100%;gap:5px;}
                .sb-dice-dock-roll{flex:initial;}
            }
            @media(prefers-reduced-motion:reduce){
                .sb-dice-choice,.sb-dice-choice .sb-poly-die{transition:none!important;}
            }

            /* V66.2.166 — Aba de mesa inferior, independente das abas de navegação.
               O interior do estojo e os modelos dos dados não são alterados. */
            .sb-dice-dock {
                position:fixed;left:calc(50% + 24px);right:auto;top:auto;bottom:0;
                width:112px;min-width:112px;z-index:295;pointer-events:none;
                translate:-50% 0;
            }
            .sb-dice-launcher {
                position:relative;display:flex;align-items:center;justify-content:center;gap:9px;
                width:112px;height:43px;margin:0;padding:6px 12px 7px;
                border:1px solid color-mix(in srgb,var(--theme-accent-strong,var(--theme-accent)) 79%,black 21%);
                border-bottom:0;border-radius:13px 13px 0 0;
                background:var(--theme-accent-strong,var(--theme-accent));
                color:var(--theme-button-text,#fff);
                box-shadow:0 -4px 16px rgba(0,0,0,.17),inset 0 1px 0 rgba(255,255,255,.19);
                cursor:pointer;
                transition:background .18s ease,box-shadow .18s ease,filter .18s ease;
            }
            .sb-dice-launcher .sb-poly-die {
                pointer-events:none;flex:none;filter:drop-shadow(0 2px 2px rgba(0,0,0,.18));
            }
            .sb-dice-launcher-label {
                font:850 10px/1 system-ui,sans-serif;letter-spacing:.085em;
                color:var(--theme-button-text,#fff);text-transform:uppercase;
            }
            .sb-dice-launcher:hover,.sb-dice-launcher:focus-visible,.sb-dice-launcher.is-active {
                background:var(--theme-accent-strong,var(--theme-accent));
                border-color:var(--theme-accent-strong,var(--theme-accent));
                filter:brightness(1.12);transform:none;
                box-shadow:0 -7px 23px rgba(0,0,0,.19),0 -2px 13px color-mix(in srgb,var(--theme-accent) 20%,transparent),inset 0 1px 0 rgba(255,255,255,.2);
            }
            .sb-dice-launcher.is-return-target {
                transform:none;filter:brightness(1.18);
                box-shadow:0 0 0 4px color-mix(in srgb,var(--theme-accent) 18%,transparent),0 -5px 23px rgba(0,0,0,.18);
            }
            .sb-dice-dock-count {
                top:-8px;right:-8px;left:auto;min-width:19px;height:19px;
                border-radius:999px;font-size:9px;
            }
            .sb-dice-dock-panel,.sb-dice-dock.is-low .sb-dice-dock-panel {
                position:absolute;left:50%;right:auto;top:auto;bottom:calc(100% + 10px);
                translate:-50% 0;width:min(402px,calc(100vw - 24px));
                max-height:calc(100dvh - 63px);max-height:calc(100vh - 63px);
                overflow-x:hidden;overflow-y:auto;
                border:1px solid var(--theme-modal-border,var(--theme-border));
                border-radius:17px;
                background:var(--theme-modal-surface,var(--theme-surface-card));
                box-shadow:0 20px 48px rgba(0,0,0,.25),0 4px 13px rgba(0,0,0,.1),inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 23%,transparent);
                transform-origin:bottom center;
            }
            @supports (height:1dvh) {
                .sb-dice-dock-panel,.sb-dice-dock.is-low .sb-dice-dock-panel {max-height:calc(100dvh - 63px)}
            }
            @keyframes sb-dice-panel-in {
                from{opacity:0;transform:translateY(11px) scale(.986)}
                to{opacity:1;transform:translateY(0) scale(1)}
            }
            .sb-dice-dock-head {
                display:flex;align-items:center;gap:9px;padding:11px 13px 10px;
                border-bottom:1px solid var(--theme-border-soft);
                background:color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 96%,var(--theme-surface-raised) 4%);
            }
            .sb-dice-dock-head-icon {
                width:32px;height:32px;flex:none;display:grid;place-items:center;
                border:1px solid color-mix(in srgb,var(--theme-border-soft) 75%,var(--theme-accent) 25%);
                border-radius:9px;background:var(--theme-surface-raised,var(--theme-surface-card));
                box-shadow:inset 0 1px 0 color-mix(in srgb,var(--theme-paper-strong) 27%,transparent);
            }
            .sb-dice-dock-head-icon .sb-poly-die {pointer-events:none}
            .sb-dice-dock-head-copy {min-width:0;flex:1}
            .sb-dice-dock-kicker {font-size:7.5px;letter-spacing:.15em;opacity:.8}
            .sb-dice-dock-title {margin-top:3px;font-size:16px;line-height:1.05;letter-spacing:-.015em}
            .sb-dice-help-trigger,.sb-dice-dock-close {
                width:26px;height:26px;flex:none;display:grid;place-items:center;
                border:1px solid var(--theme-border-soft);border-radius:8px;
                background:var(--theme-surface-raised);color:var(--theme-text-secondary);
                transition:background .17s ease,border-color .17s ease,color .17s ease;
            }
            .sb-dice-help-trigger {border-radius:50%}
            .sb-dice-help-trigger:hover,.sb-dice-help-trigger:focus-visible,
            .sb-dice-dock-close:hover,.sb-dice-dock-close:focus-visible {
                color:var(--theme-text-strong);background:var(--theme-card-hover);border-color:var(--theme-accent);
            }
            /* Cabeçalho e ações são organizados sem mexer no feltro interno. */
            .sb-dice-dock-foot {
                display:flex;flex-direction:row;align-items:center;justify-content:space-between;
                gap:8px;padding:10px 12px 12px;margin-top:2px;
                border-top:1px solid var(--theme-border-soft);
                background:color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 97%,var(--theme-surface-raised) 3%);
            }
            .sb-dice-foot-stats {
                display:flex;flex:0 0 auto;flex-direction:column;gap:2px;white-space:nowrap;
            }
            .sb-dice-foot-stats strong {
                font:850 12px/1.05 Georgia,serif;color:var(--theme-text-strong);font-variant-numeric:tabular-nums;
            }
            .sb-dice-foot-stats span {font:700 8px/1.2 system-ui,sans-serif;color:var(--theme-text-secondary)}
            .sb-dice-dock-actions {
                display:flex;align-items:center;justify-content:flex-end;flex-wrap:nowrap;
                flex:0 1 auto;gap:5px;width:auto;min-width:0;
            }
            .sb-dice-dock-clean {
                min-height:31px;padding:6px 7px;white-space:nowrap;
                border:1px solid var(--theme-border-soft);border-radius:8px;
                background:var(--theme-surface-raised);color:var(--theme-text-strong);font-size:9px;
            }
            .sb-dice-dock-roll {min-height:32px;padding:6px 10px;border-radius:8px;font-size:9px}
            .sb-dice-dock-actions .sb-dice-dock-roll {flex:none}
            .sb-dice-help-panel {top:51px}
            /* O segundo botão faz parte do mesmo dock, sem surgir no meio da ficha. */
            .sb-dice-quick-roll {
                position:absolute;left:calc(100% + 7px);right:auto;top:auto;bottom:5px;
                max-width:94px;min-height:31px;padding:5px 9px;
                border-radius:8px;transform:none;box-shadow:0 -3px 12px rgba(0,0,0,.13);
                pointer-events:auto;
            }
            .sb-dice-quick-roll:hover:not(:disabled) {transform:none;filter:brightness(1.12)}
            @media(max-width:680px) {
                .sb-dice-dock {top:auto;right:auto;left:50%;bottom:0;width:112px;min-width:112px}
                .sb-dice-launcher {width:112px;height:41px;border:1px solid var(--theme-accent-strong,var(--theme-accent));border-bottom:0;border-radius:12px 12px 0 0}
                .sb-dice-launcher-label {display:block}
                .sb-dice-dock-panel,.sb-dice-dock.is-low .sb-dice-dock-panel {
                    left:50%;right:auto;top:auto;bottom:calc(100% + 9px);
                    width:min(402px,calc(100vw - 18px));max-height:calc(100vh - 58px);
                    transform-origin:bottom center;
                }
                .sb-dice-dock-foot {padding:9px 10px 11px;gap:7px;flex-direction:column;align-items:stretch}
                .sb-dice-foot-stats {flex-direction:row;align-items:baseline;gap:6px}
                .sb-dice-dock-actions {display:grid;grid-template-columns:1fr .85fr 1.3fr;width:100%;gap:5px}
                .sb-dice-dock-actions .sb-dice-dock-roll {width:100%}
                .sb-dice-quick-roll {left:calc(100% + 6px);right:auto;bottom:5px;max-width:90px}
            }
            @media(max-width:350px) {.sb-dice-quick-roll {max-width:79px;padding-inline:6px;font-size:8px}}
            /* V66.2.167 — A janela flutua independentemente do navegador lateral.
               Não alteramos nenhum seletor da superfície de feltro ou dos poliedros. */
            .sb-dice-dock {position:fixed!important;inset:0 auto auto 0!important;width:0!important;min-width:0!important;height:0!important;translate:none!important;pointer-events:none!important;z-index:318!important}
            .sb-dice-dock-panel,.sb-dice-dock.is-low .sb-dice-dock-panel {
                position:fixed!important;bottom:auto!important;right:auto!important;
                left:var(--sb-tray-x,84px)!important;top:var(--sb-tray-y,108px)!important;
                translate:none!important;transform-origin:top left!important;
                animation:none!important;
                width:min(408px,calc(100vw - 18px))!important;
                max-height:calc(100dvh - 16px)!important;
                pointer-events:auto!important;
            }
            .sb-dice-dock-panel.is-dragging-panel {animation:none!important}
            .sb-dice-dock-head {cursor:grab;touch-action:none;user-select:none}
            .sb-dice-dock-panel.is-dragging-panel .sb-dice-dock-head {cursor:grabbing}
            .sb-dice-dock-head button {cursor:pointer;touch-action:auto}
            .sb-dice-launcher,.sb-dice-quick-roll {display:none!important}
            @media(max-width:600px) {.sb-dice-dock-panel,.sb-dice-dock.is-low .sb-dice-dock-panel {width:min(390px,calc(100vw - 14px))!important}}
            @media(prefers-reduced-motion:reduce) {.sb-dice-dock-panel{animation:none!important}}

            /* V66.2.169 — painel simplificado: estojo em destaque e apenas ações essenciais. */
            .sb-dice-dock-panel{overflow:visible!important;width:min(392px,calc(100vw - 20px))!important;border-radius:18px!important;background:linear-gradient(180deg,color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 98%,white 2%),color-mix(in srgb,var(--theme-modal-surface,var(--theme-surface-card)) 99%,black 1%))!important;box-shadow:0 24px 56px rgba(0,0,0,.22),0 6px 18px rgba(0,0,0,.10),inset 0 1px 0 rgba(255,255,255,.38)!important}
            .sb-dice-dock-head{justify-content:flex-end!important;gap:8px!important;padding:8px 10px 4px!important;border-bottom:0!important;background:transparent!important;cursor:grab}
            .sb-dice-dock-head-icon,.sb-dice-dock-head-copy{display:none!important}
            .sb-dice-help-trigger,.sb-dice-dock-close{width:28px!important;height:28px!important;background:color-mix(in srgb,var(--theme-surface-raised) 82%,white 18%)!important;border-color:color-mix(in srgb,var(--theme-border-soft) 78%,white 22%)!important;box-shadow:0 1px 3px rgba(0,0,0,.06),inset 0 1px 0 rgba(255,255,255,.42)!important}
            .sb-dice-case{margin:0 12px 10px!important;padding:8px!important;border-radius:20px!important;border:1px solid color-mix(in srgb,var(--theme-border-strong,var(--theme-border)) 66%,black 34%)!important;background:linear-gradient(145deg,color-mix(in srgb,var(--theme-surface-raised,var(--theme-surface-card)) 72%,black 28%),color-mix(in srgb,var(--theme-surface-raised,var(--theme-surface-card)) 84%,var(--theme-accent) 16%))!important;box-shadow:0 5px 14px rgba(0,0,0,.16),inset 0 2px 1px rgba(255,255,255,.10),inset 0 -3px 5px rgba(0,0,0,.28)!important}
            .sb-dice-dock-grid{min-height:164px!important;padding:13px 14px 12px!important;gap:6px 7px!important;border-radius:15px!important;background:radial-gradient(ellipse at 50% 24%,color-mix(in srgb,var(--theme-accent-strong,#674343) 20%,#4f5761 80%),transparent 88%),linear-gradient(160deg,color-mix(in srgb,var(--theme-accent-strong,#674343) 25%,#2b3540 75%),color-mix(in srgb,var(--theme-accent-strong,#674343) 16%,#18252d 84%))!important;box-shadow:inset 0 7px 16px rgba(0,0,0,.46),inset 0 0 0 1px rgba(255,255,255,.05),inset 0 -2px 10px rgba(0,0,0,.24)!important}
            .sb-dice-choice{height:70px!important;transition:transform .24s cubic-bezier(.22,.86,.23,1),filter .22s ease!important}
            .sb-dice-choice::before{inset:auto 10px 2px!important;height:18px!important;background:radial-gradient(ellipse at center,rgba(0,0,0,.58),rgba(0,0,0,.22) 52%,transparent 78%)!important;filter:blur(3.5px)!important;opacity:.92!important}
            .sb-dice-choice .sb-poly-die{filter:drop-shadow(0 6px 6px rgba(0,0,0,.38)) drop-shadow(0 1px 0 rgba(255,255,255,.06))!important;transition:transform .24s cubic-bezier(.2,.85,.25,1),filter .24s ease!important}
            .sb-dice-choice:hover,.sb-dice-choice:focus-visible{transform:translateY(-4px)!important;filter:brightness(1.15)!important}
            .sb-dice-choice:hover .sb-poly-die,.sb-dice-choice:focus-visible .sb-poly-die{transform:translateY(-4px) scale(1.055)!important;filter:drop-shadow(0 11px 8px rgba(0,0,0,.40)) drop-shadow(0 0 10px color-mix(in srgb,var(--theme-accent) 18%,transparent))!important}
            .sb-dice-dock-foot{display:flex!important;align-items:center!important;justify-content:flex-end!important;padding:4px 12px 12px!important;gap:10px!important}
            .sb-dice-foot-stats,.is-select-toggle{display:none!important}
            .sb-dice-dock-actions{display:flex!important;justify-content:flex-end!important;gap:8px!important;width:100%!important;grid-template-columns:none!important}
            .sb-dice-dock-clean,.sb-dice-dock-roll{min-height:34px!important;padding:7px 13px!important;border-radius:10px!important;font:850 10px/1 system-ui,sans-serif!important}
            .sb-dice-error{padding:0 14px 12px!important}
            .sb-free-staged-die{animation:sb-dice-staged-enter .42s cubic-bezier(.16,.88,.22,1.2) both!important;filter:drop-shadow(0 10px 15px rgba(0,0,0,.27))!important;transition:opacity .18s ease,filter .22s ease!important}
            @keyframes sb-dice-staged-enter{0%{opacity:0;transform:translate(-50%,-50%) scale(.72);filter:drop-shadow(0 2px 2px rgba(0,0,0,.08))}55%{opacity:1;transform:translate(-50%,-50%) scale(1.08)}100%{opacity:1;transform:translate(-50%,-50%) scale(1)}}
            .sb-free-staged-die.is-selected .sb-poly-die{filter:drop-shadow(0 0 8px color-mix(in srgb,var(--theme-accent) 42%,transparent)) drop-shadow(0 8px 8px rgba(0,0,0,.24))!important}
            .sb-free-staged-die.is-storing{animation:sb-dice-store-out .26s cubic-bezier(.3,.08,.62,1) both!important}
            @keyframes sb-dice-store-out{from{opacity:1;transform:translate(-50%,-50%) scale(1)}to{opacity:0;transform:translate(-50%,-50%) scale(.58)}}
            @media(max-width:600px){.sb-dice-case{margin:0 9px 9px!important}.sb-dice-dock-grid{min-height:146px!important;padding:9px 10px 8px!important;gap:3px 4px!important}.sb-dice-choice{height:62px!important}.sb-dice-dock-foot{padding:4px 10px 11px!important}}
            /* V66.2.170 — estojo praticamente sozinho, com controles flutuantes e melhor acabamento. */
            .sb-dice-dock{z-index:298!important}
            .sb-dice-dock-panel{background:transparent!important;border:0!important;box-shadow:none!important;overflow:visible!important;padding:0 0 46px 0!important}
            .sb-dice-dock-head{position:absolute!important;top:6px!important;right:10px!important;z-index:4!important;padding:0!important;gap:7px!important;cursor:grab!important}
            .sb-dice-help-trigger,.sb-dice-dock-close{width:30px!important;height:30px!important;border-radius:999px!important;background:linear-gradient(180deg,rgba(255,255,255,.92),rgba(244,241,237,.95))!important;border:1px solid rgba(111,92,83,.18)!important;box-shadow:0 6px 12px rgba(0,0,0,.08),inset 0 1px 0 rgba(255,255,255,.72)!important;backdrop-filter:blur(4px)}
            .sb-dice-help-trigger:hover,.sb-dice-help-trigger:focus-visible,.sb-dice-dock-close:hover,.sb-dice-dock-close:focus-visible{background:linear-gradient(180deg,#fff,#f6f2ee)!important;box-shadow:0 8px 16px rgba(0,0,0,.12),0 0 0 3px color-mix(in srgb,var(--theme-accent) 10%,transparent)!important}
            .sb-dice-case{position:relative!important;margin:0 0 8px 0!important;padding:10px!important;border-radius:22px!important;border:1px solid rgba(82,59,49,.34)!important;background:linear-gradient(155deg,#9d8577 0%,#6e564e 18%,#4a382f 52%,#7e6758 100%)!important;box-shadow:0 18px 28px rgba(42,25,20,.18),0 5px 12px rgba(0,0,0,.10),inset 0 1px 0 rgba(255,255,255,.25),inset 0 -4px 8px rgba(0,0,0,.28)!important}
            .sb-dice-case::before{left:13px!important}.sb-dice-case::after{right:13px!important}
            .sb-dice-case::before,.sb-dice-case::after{top:13px!important;width:5px!important;height:5px!important;background:linear-gradient(180deg,#f0cf9a,#b88d58)!important;box-shadow:inset 0 1px 1px rgba(255,255,255,.38),0 1px 3px rgba(0,0,0,.48)!important}
            .sb-dice-dock-grid{min-height:174px!important;padding:16px 16px 14px!important;gap:8px 8px!important;border-radius:16px!important;border:1px solid rgba(13,21,25,.72)!important;background:radial-gradient(ellipse at 50% 22%,rgba(78,104,112,.86),rgba(36,55,62,.35) 36%,transparent 70%),linear-gradient(180deg,#31444d 0%,#20343c 46%,#14262d 100%)!important;box-shadow:inset 0 9px 18px rgba(0,0,0,.42),inset 0 -3px 14px rgba(0,0,0,.28),inset 0 0 0 1px rgba(255,255,255,.05),0 2px 3px rgba(255,255,255,.05)!important}
            .sb-dice-choice{height:72px!important}
            .sb-dice-choice::before{inset:auto 9px 1px!important;height:19px!important;opacity:1!important;background:radial-gradient(ellipse at center,rgba(0,0,0,.62),rgba(0,0,0,.24) 52%,transparent 80%)!important}
            .sb-dice-choice .sb-poly-die{filter:drop-shadow(0 8px 10px rgba(0,0,0,.42)) drop-shadow(0 1px 0 rgba(255,255,255,.06))!important}
            .sb-dice-choice-percentile{gap:7px!important}
            .sb-dice-dock-foot{position:absolute!important;right:0!important;bottom:0!important;padding:0!important;background:transparent!important}
            .sb-dice-dock-actions{gap:10px!important}
            .sb-dice-dock-clean,.sb-dice-dock-roll{min-height:36px!important;padding:8px 15px!important;border-radius:11px!important;box-shadow:0 7px 14px rgba(0,0,0,.08),inset 0 1px 0 rgba(255,255,255,.34)!important}
            .sb-dice-dock-clean{background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(240,236,231,.94))!important;border-color:rgba(111,92,83,.18)!important}
            .sb-dice-dock-roll{background:linear-gradient(180deg,color-mix(in srgb,var(--theme-button-background,var(--theme-accent)) 92%,white 8%),var(--theme-button-background,var(--theme-accent)))!important}
            .sb-dice-help-panel{top:42px!important;right:8px!important}
            .sb-free-staged-field{z-index:320!important;pointer-events:none}
            .sb-free-staged-field .sb-free-staged-die{pointer-events:auto}
            .sb-free-staged-die{filter:drop-shadow(0 12px 16px rgba(0,0,0,.30))!important}
            .sb-free-staged-die.is-dragging{z-index:325!important;filter:drop-shadow(0 20px 22px rgba(0,0,0,.40)) drop-shadow(0 0 12px color-mix(in srgb,var(--theme-accent) 12%,transparent))!important}
            .sb-free-staged-die.is-storing{z-index:330!important}
            @keyframes sb-dice-staged-enter{0%{opacity:0;transform:translate(-50%,-50%) scale(.66)}45%{opacity:1;transform:translate(-50%,-50%) scale(1.06)}100%{opacity:1;transform:translate(-50%,-50%) scale(1)}}
            @keyframes sb-dice-store-out{0%{opacity:1;transform:translate(-50%,-50%) scale(1)}100%{opacity:0;transform:translate(-50%,-50%) scale(.48)}}
            @media(max-width:600px){.sb-dice-dock-panel{padding-bottom:44px!important}.sb-dice-case{border-radius:20px!important;padding:8px!important}.sb-dice-dock-grid{min-height:150px!important;padding:11px 12px 10px!important;gap:4px 5px!important}.sb-dice-choice{height:62px!important}.sb-dice-dock-clean,.sb-dice-dock-roll{min-height:34px!important;padding:7px 12px!important}}


            /* V66.2.171 — bandeja única, comandos encaixados e arraste pela superfície livre. */
            .sb-dice-dock-panel,
            .sb-dice-dock.is-low .sb-dice-dock-panel {
                width:min(390px,calc(100vw - 18px))!important;
                padding:0!important;
                border:0!important;border-radius:25px!important;
                background:transparent!important;box-shadow:none!important;
                overflow:visible!important;cursor:grab;touch-action:none;
            }
            .sb-dice-dock-panel.is-dragging-panel{cursor:grabbing!important}
            .sb-dice-dock-panel.is-dragging-panel .sb-dice-case,
            .sb-dice-dock-panel.is-dragging-panel .sb-dice-dock-grid{cursor:grabbing!important}
            .sb-dice-case{
                position:relative!important;margin:0!important;padding:10px 10px 6px!important;
                border:1px solid color-mix(in srgb,var(--theme-accent-strong,#73554b) 44%,#382f2b 56%)!important;
                border-radius:25px!important;
                background:linear-gradient(156deg,
                    color-mix(in srgb,var(--theme-accent-strong,#73554b) 26%,#a18d80 74%) 0%,
                    color-mix(in srgb,var(--theme-accent-strong,#73554b) 30%,#5a4a42 70%) 30%,
                    color-mix(in srgb,var(--theme-accent-strong,#73554b) 18%,#3b302c 82%) 74%,
                    color-mix(in srgb,var(--theme-accent-strong,#73554b) 22%,#776355 78%) 100%)!important;
                box-shadow:0 18px 35px rgba(15,14,17,.20),0 4px 12px rgba(15,14,17,.11),
                    inset 0 1px 1px rgba(255,255,255,.3),inset 0 -3px 5px rgba(0,0,0,.24)!important;
                cursor:grab;touch-action:none;user-select:none;
            }
            .sb-dice-case::before{left:13px!important;top:13px!important}
            .sb-dice-case::after{right:13px!important;top:13px!important}
            .sb-dice-dock-grid{
                min-height:162px!important;padding:13px 15px 10px!important;gap:5px 7px!important;
                background:radial-gradient(ellipse at 50% 24%,
                    color-mix(in srgb,var(--theme-accent-strong,#73554b) 24%,#506671 76%) 0%,transparent 81%),
                    linear-gradient(160deg,
                    color-mix(in srgb,var(--theme-accent-strong,#73554b) 22%,#2d3d46 78%),
                    color-mix(in srgb,var(--theme-accent-strong,#73554b) 15%,#17282f 85%))!important;
                border-radius:18px!important;border:1px solid rgba(5,12,15,.66)!important;
                box-shadow:inset 0 7px 15px rgba(0,0,0,.42),
                    inset 0 -3px 10px rgba(0,0,0,.23),inset 0 0 0 1px rgba(255,255,255,.05)!important;
                cursor:grab;touch-action:none;
            }
            .sb-dice-choice{height:67px!important;cursor:grab}
            .sb-dice-choice:active{cursor:grabbing}
            .sb-dice-dock-foot{
                position:static!important;display:flex!important;align-items:center!important;justify-content:flex-end!important;
                margin:0!important;padding:6px 3px 0!important;min-height:36px!important;
                background:transparent!important;border:0!important;box-shadow:none!important;
            }
            .sb-dice-dock-actions{display:flex!important;justify-content:flex-end!important;gap:6px!important;width:auto!important}
            .sb-dice-dock-clean.sb-dice-icon-action,
            .sb-dice-dock-roll.sb-dice-icon-action{
                position:relative;display:grid!important;place-items:center!important;
                width:29px!important;min-width:29px!important;height:27px!important;min-height:27px!important;
                padding:0!important;margin:0!important;border-radius:8px!important;
                border:1px solid rgba(10,17,18,.23)!important;
                color:#f2ebe4!important;background:linear-gradient(175deg,rgba(21,34,39,.62),rgba(17,27,32,.8))!important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.08),0 1px 2px rgba(0,0,0,.18)!important;
                font-size:0!important;cursor:pointer;opacity:1!important;
                transition:background .18s ease,box-shadow .18s ease,transform .16s ease,opacity .18s ease!important;
            }
            .sb-dice-dock-roll.sb-dice-icon-action:not(:disabled){
                border-color:color-mix(in srgb,var(--theme-accent) 40%,#1c292c 60%)!important;
                background:linear-gradient(180deg,
                    color-mix(in srgb,var(--theme-accent-strong) 70%,#273740 30%),
                    color-mix(in srgb,var(--theme-accent-strong) 65%,#162730 35%))!important;
            }
            .sb-dice-icon-action svg{display:block;width:15px;height:15px;pointer-events:none}
            .sb-dice-icon-action:disabled{opacity:.35!important;cursor:not-allowed}
            .sb-dice-icon-action:hover:not(:disabled),.sb-dice-icon-action:focus-visible:not(:disabled){
                filter:brightness(1.16)!important;transform:translateY(-1px)!important;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.12),0 3px 5px rgba(0,0,0,.2)!important;
            }
            .sb-dice-help-trigger{
                position:absolute!important;z-index:6!important;top:5px!important;right:6px!important;
                display:grid!important;place-items:center!important;
                width:19px!important;min-width:19px!important;height:19px!important;
                padding:0!important;border-radius:50%!important;
                border:1px solid rgba(221,205,184,.4)!important;
                color:rgba(249,241,229,.9)!important;
                background:linear-gradient(180deg,rgba(20,32,34,.60),rgba(7,18,21,.72))!important;
                box-shadow:0 1px 3px rgba(0,0,0,.2)!important;
                font:800 10px/1 Georgia,serif!important;
                cursor:help!important;
            }
            .sb-dice-help-trigger:hover,.sb-dice-help-trigger:focus-visible{
                background:color-mix(in srgb,var(--theme-accent-strong) 58%,#28383f 42%)!important;
                border-color:rgba(245,232,210,.72)!important;
            }
            .sb-dice-help-panel{top:25px!important;right:6px!important;z-index:12!important;touch-action:auto}
            .sb-dice-error{position:absolute;left:0;right:0;bottom:-43px!important;padding:8px 10px!important;
                border-radius:8px;background:var(--theme-modal-surface,var(--theme-surface-card));
                box-shadow:0 5px 13px rgba(0,0,0,.1)}
            @media(max-width:600px){
                .sb-dice-case{padding:8px 8px 5px!important;border-radius:21px!important}
                .sb-dice-dock-grid{min-height:147px!important;padding:10px 12px 8px!important;gap:3px 4px!important}
                .sb-dice-choice{height:61px!important}
                .sb-dice-dock-foot{padding:4px 2px 0!important;min-height:33px!important}
            }
            @media(prefers-reduced-motion:reduce){
                .sb-dice-icon-action{transition:none!important}
            }
        </style>
    @endpush
    <script>
        (() => {
            let registered=false;
            const register = () => {
                if(registered || !window.Alpine)return;
                registered=true;
                window.Alpine.data('spellboundDiceTray', (rollUrl, csrf) => ({
                rollUrl, csrf,
                expanded:false,
                helpOpen:false,
                calcOpen:false,
                trayPull:null,
                panelPosition:{x:82,y:105},
                panelDrag:null,
                busy:false,
                freeDice:[],
                freeDiceCounter:0,
                freeDiceLimit:20,
                freeDieDrag:null,
                returnHover:false,
                dragFrame:null,
                dragFrameTime:0,
                placingFrames:{},
                handoffToken:null,
                handoffIds:[],
                handoffTimer:null,
                error:'',
                init(){
                    // A navegação da esquerda é o acesso; a janela mantém posição própria.
                    try {
                        const saved=JSON.parse(localStorage.getItem('sb-sheet-dice-window-v167')||'null');
                        if(saved&&Number.isFinite(saved.x)&&Number.isFinite(saved.y)){
                            this.panelPosition={x:saved.x,y:saved.y};
                        }else{
                            const anchor=document.querySelector('[data-sb-tool="dice"]');
                            this.panelPosition={x:82,y:Math.max(26,Math.round(anchor?.getBoundingClientRect().top||115))};
                        }
                    }catch(_error){}
                    this.helpOpen=false;
                    this.$nextTick(()=>this.emitTrayState());
                },
                toggleTray(){
                    if(this.expanded){this.closeTray();return;}
                    this.expanded=true;
                    this.helpOpen=false;
                    this.emitTrayState();
                    this.$nextTick(()=>this.clampTrayPanel());
                },
                closeTray(){
                    this.expanded=false;
                    this.helpOpen=false;
                    this.endTrayPanelDrag();
                    this.emitTrayState();
                },
                emitTrayState(){
                    window.dispatchEvent(new CustomEvent('spellbound:tray-state',{detail:{open:!!this.expanded}}));
                },
                trayPanelStyle(){
                    return `--sb-tray-x:${Math.round(this.panelPosition.x)}px;--sb-tray-y:${Math.round(this.panelPosition.y)}px`;
                },
                clampTrayPanel(){
                    const panel=this.$refs?.freeDicePanel;
                    const w=panel?.offsetWidth||Math.min(408,innerWidth-18);
                    const h=panel?.offsetHeight||340;
                    this.panelPosition={
                        x:Math.max(7,Math.min(Math.max(7,innerWidth-w-7),Number(this.panelPosition.x)||7)),
                        y:Math.max(7,Math.min(Math.max(7,innerHeight-h-7),Number(this.panelPosition.y)||7)),
                    };
                },
                beginTrayPanelDrag(event){
                    if(!this.expanded || (event.pointerType==='mouse' && event.button!==0)) return;
                    // Feltro e moldura funcionam como alça. Os dados e comandos permanecem independentes.
                    if(event.target.closest('button, [data-sb-no-tray-drag]') || this.trayPull || this.freeDieDrag) return;
                    event.preventDefault();
                    this.panelDrag={id:event.pointerId,x:event.clientX,y:event.clientY,
                        left:this.panelPosition.x,top:this.panelPosition.y};
                },
                moveTrayPanel(event){
                    const drag=this.panelDrag;
                    if(!drag||drag.id!==event.pointerId)return;
                    this.panelPosition={x:drag.left+event.clientX-drag.x,y:drag.top+event.clientY-drag.y};
                    this.clampTrayPanel();
                },
                endTrayPanelDrag(){
                    if(!this.panelDrag)return;
                    this.panelDrag=null;
                    try{localStorage.setItem('sb-sheet-dice-window-v167',JSON.stringify(this.panelPosition));}catch(_error){}
                },
                destroy(){
                    this.endTrayPanelDrag();
                    this.stopDragPhysics();
                    Object.values(this.placingFrames).forEach(id=>window.cancelAnimationFrame(id));
                    this.placingFrames={};
                    this.trayPull=null;
                    this.returnHover=false;
                    if(this.handoffTimer!==null) window.clearTimeout(this.handoffTimer);
                },
                selectedFreeDice(){return this.freeDice.filter(die=>die.selected&&!die.launching&&!die.storing);},
                selectedFreeDiceCount(){return this.selectedFreeDice().length;},
                // d100 ocupa duas peças físicas, mesmo sendo uma única rolagem.
                physicalFreeDiceCount(){return this.freeDice.reduce((n,die)=>n+(die.sides===100?2:1),0);},
                canAddFreeDie(sides){return !this.busy && this.physicalFreeDiceCount()+(Number(sides)===100?2:1)<=this.freeDiceLimit;},
                allFreeDiceSelected(){return this.freeDice.length>0&&this.selectedFreeDiceCount()===this.freeDice.length;},
                toggleAllFreeDice(){
                    if(this.busy||!this.freeDice.length)return;
                    const select=!this.allFreeDiceSelected();
                    this.freeDice.forEach(die=>{die.selected=select;});
                },
                addFreeDie(sides, options={}){
                    const value=Number(sides);
                    if(![4,6,8,10,12,20,100].includes(value)||this.busy)return null;
                    if(!this.canAddFreeDie(value)){this.error='Limite de 20 dados físicos preparados.';return null;}
                    const n=this.freeDice.length;
                    const angle=n*2.39996323+Math.random()*.5;
                    const radius=28+Math.sqrt(n)*37;
                    const w=window.innerWidth,h=window.innerHeight;
                    const margin=value===100?77:40;
                    const fallbackX=Math.max(margin,Math.min(w-margin,w*.52+Math.cos(angle)*radius));
                    const fallbackY=Math.max(50,Math.min(h-110,h*.38+Math.sin(angle)*radius));
                    const x=Math.max(margin,Math.min(w-margin,Number(options?.x)||fallbackX));
                    const y=Math.max(50,Math.min(h-110,Number(options?.y)||fallbackY));
                    const die={id:++this.freeDiceCounter,sides:value,x,y,selected:options?.selected!==false,launching:false,heldForHandoff:false,
                        tilt:0,angularVelocity:0,vx:0,vy:0,impact:0,
                        face:null,restAngle:0,landedParts:null,justLanded:!!options?.justLanded,storing:false};
                    this.freeDice.push(die);
                    this.error='';
                    return die;
                },
                // Um clique no estojo não derruba o dado. A peça só é criada
                // depois de um arraste deliberado, preservando a bandeja cheia.
                beginTrayPull(sides,event){
                    if(this.busy || !this.canAddFreeDie(sides) || (event.pointerType==='mouse'&&event.button!==0))return;
                    this.trayPull={sides:Number(sides),pointerId:event.pointerId,
                        x:event.clientX,y:event.clientY,time:event.timeStamp||0};
                },
                trackTrayPull(event){
                    const pull=this.trayPull;
                    if(!pull || pull.pointerId!==event.pointerId)return;
                    if(Math.hypot(event.clientX-pull.x,event.clientY-pull.y)<7)return;
                    this.trayPull=null;
                    const die=this.addFreeDie(pull.sides,{x:event.clientX,y:event.clientY,selected:true,justLanded:true});
                    if(!die)return;
                    // O drag começa imediatamente, mesmo se o mouse for solto
                    // antes do próximo quadro renderizado pelo Alpine.
                    this.beginFreeDieDrag(die,{
                        pointerId:event.pointerId,pointerType:event.pointerType||'mouse',
                        button:0,shiftKey:false,clientX:event.clientX,clientY:event.clientY,
                        timeStamp:event.timeStamp||performance.now(),
                    });
                    if(this.freeDieDrag){
                        this.freeDieDrag.fromTray=true;
                        this.freeDieDrag.moved=true;
                        this.freeDieDrag.targetDX=0;
                        this.freeDieDrag.targetDY=0;
                    }
                },
                endTrayPull(event){
                    if(this.trayPull?.pointerId===event.pointerId)this.trayPull=null;
                },
                toggleFreeDieSelection(die, additive=false){
                    if(!die||this.busy||this.freeDieDrag)return;
                    if(additive){ die.selected=!die.selected; return; }
                    // Clique comum escolhe este dado; Shift + clique compõe o grupo.
                    const exclusive=this.selectedFreeDiceCount()===1 && die.selected;
                    this.freeDice.forEach(item=>{item.selected=item.id===die.id ? !exclusive : false;});
                },
                removeFreeDie(id){
                    if(this.freeDieDrag?.id===id)this.cancelFreeDieDrag();
                    this.freeDice=this.freeDice.filter(die=>die.id!==id);
                },
                clearFreeDice(){
                    if(this.busy)return;
                    this.cancelFreeDieDrag();
                    this.freeDice=[];
                    this.error='';
                },
                storeFreeDice(dice) {
                    const ids=new Set((dice||[]).map(item=>item.id));
                    if(!ids.size)return;
                    const selected=this.freeDice.filter(item=>ids.has(item.id));
                    selected.forEach(item=>{item.storing=true;item.selected=false;});
                    const launcher=this.expanded ? this.$refs?.freeDiceWell : document.querySelector('[data-sb-tool="dice"]');
                    const target=launcher?.getBoundingClientRect();
                    this.returnHover=false;
                    this.$nextTick(()=>{
                        const jobs=selected.map(async item=>{
                            const el=document.querySelector(`.sb-free-staged-die[data-free-die-id="${item.id}"]`);
                            if(!target||!el?.animate)return;
                            const rect=el.getBoundingClientRect();
                            const dx=(target.left+target.width/2)-(rect.left+rect.width/2);
                            const dy=(target.top+target.height/2)-(rect.top+rect.height/2);
                            const animation=el.animate([
                                {translate:'0px 0px',scale:'1',opacity:1},
                                {translate:`${dx}px ${dy}px`,scale:'.16',opacity:0},
                            ],{duration:320,easing:'cubic-bezier(.3,.08,.62,1)',fill:'forwards'});
                            try {await animation.finished;}catch(_error){}
                        });
                        Promise.allSettled(jobs).then(()=>{
                            this.freeDice=this.freeDice.filter(item=>!ids.has(item.id));
                        });
                    });
                },
                freeDiePositionStyle(die){
                    const dragging=!!this.freeDieDrag?.ids?.includes(die.id) || !!die.heldForHandoff;
                    const tilt=(Number(die.tilt)||0)+(Number(die.restAngle)||0);
                    const impact=Math.min(1,Math.max(0,Number(die.impact)||0));
                    return `left:${(Number(die.x)||0).toFixed(2)}px;top:${(Number(die.y)||0).toFixed(2)}px;`
                        +`--sb-grab-tilt:${tilt.toFixed(2)}deg;--sb-number-counter-angle:${(-tilt).toFixed(2)}deg;--sb-grab-scale:${dragging?1.055:1};`
                        +`--sb-grab-lift:${dragging?(-5+impact*2):0}px;--sb-grab-squash:${(1-impact*.13).toFixed(3)};`;
                },
                percentileOffsets(die) {
                    const pieces=Array.isArray(die?.landedParts) && die.landedParts.length===2
                        ? die.landedParts : [{dx:-35,dy:-2,rotation:0},{dx:35,dy:2,rotation:0}];
                    return pieces;
                },
                freePercentilePieceStyle(die,index) {
                    const part=this.percentileOffsets(die)[index] || {dx:0,dy:0,rotation:0};
                    const angle=(Number(die.tilt)||0)+(Number(die.restAngle)||0)+(Number(part.rotation)||0);
                    return `transform:translate3d(${(Number(part.dx)||0).toFixed(2)}px,${(Number(part.dy)||0).toFixed(2)}px,0);`
                        +`--sb-piece-angle:${(Number(part.rotation)||0).toFixed(2)}deg;`
                        +`--sb-piece-counter-angle:${(-angle).toFixed(2)}deg;`;
                },
                freeDieBounds(die){
                    const mobile=window.innerWidth<600;
                    const offsets=die.sides===100?this.percentileOffsets(die):[{dx:0,dy:0}];
                    const spreadX=Math.max(...offsets.map(part=>Math.abs(Number(part.dx)||0)));
                    const spreadY=Math.max(...offsets.map(part=>Math.abs(Number(part.dy)||0)));
                    const rX=mobile?35:39, rY=mobile?35:39;
                    const minX=spreadX+rX, minY=spreadY+rY+10;
                    return {minX,maxX:Math.max(minX,window.innerWidth-minX),
                        minY,maxY:Math.max(minY,window.innerHeight-spreadY-rY-22)};
                },
                dieDragNearDock(x,y) {
                    const launcher=document.querySelector('[data-sb-tool="dice"]');
                    const panel=this.expanded ? this.$refs?.freeDicePanel : null;
                    return [launcher,panel].filter(Boolean).some(node=>{
                        const bounds=node.getBoundingClientRect();
                        const p=node===launcher?18:8;
                        return x>=bounds.left-p && x<=bounds.right+p
                            && y>=bounds.top-p && y<=bounds.bottom+p;
                    });
                },
                startDragPhysics(){
                    if(this.dragFrame!==null)return;
                    this.dragFrameTime=0;
                    this.dragFrame=window.requestAnimationFrame(t=>this.dragPhysicsFrame(t));
                },
                stopDragPhysics(){
                    if(this.dragFrame!==null)window.cancelAnimationFrame(this.dragFrame);
                    this.dragFrame=null;
                    this.dragFrameTime=0;
                },
                dragPhysicsFrame(time){
                    this.dragFrame=null;
                    const drag=this.freeDieDrag;
                    if(!drag)return;
                    const dt=this.dragFrameTime ? Math.max(.45,Math.min(1.95,(time-this.dragFrameTime)/16.667)) : 1;
                    this.dragFrameTime=time;
                    const members=drag.members.map(member=>({
                        member,die:this.freeDice.find(d=>d.id===member.id)
                    })).filter(item=>item.die);
                    if(!members.length){this.cancelFreeDieDrag();return;}
                    // Limites comuns: o grupo permanece unido ao chegar na parede.
                    // Cada peça conserva sua mola e ângulo, sem pular para o ponteiro.
                    const minDX=Math.max(...members.map(({member,die})=>this.freeDieBounds(die).minX-member.startX));
                    const maxDX=Math.min(...members.map(({member,die})=>this.freeDieBounds(die).maxX-member.startX));
                    const minDY=Math.max(...members.map(({member,die})=>this.freeDieBounds(die).minY-member.startY));
                    const maxDY=Math.min(...members.map(({member,die})=>this.freeDieBounds(die).maxY-member.startY));
                    const tx=Math.max(minDX,Math.min(maxDX,drag.targetDX));
                    const ty=Math.max(minDY,Math.min(maxDY,drag.targetDY));
                    const edgeX=Math.abs(tx-drag.targetDX)>1.5;
                    const edgeY=Math.abs(ty-drag.targetDY)>1.5;
                    members.forEach(({member,die},i)=>{
                        const goalX=member.startX+tx;
                        const goalY=member.startY+ty;
                        const lag=(i%4)*.011;
                        const k=.205-lag;
                        const damping=Math.pow(.785-lag,dt);
                        die.vx=((Number(die.vx)||0)+(goalX-die.x)*k*dt)*damping;
                        die.vy=((Number(die.vy)||0)+(goalY-die.y)*k*dt)*damping;
                        die.x+=die.vx*dt;
                        die.y+=die.vy*dt;
                        const bounds=this.freeDieBounds(die);
                        let wall=0;
                        if(die.x<bounds.minX||die.x>bounds.maxX){
                            const hit=die.vx;
                            die.x=Math.max(bounds.minX,Math.min(bounds.maxX,die.x));
                            die.vx=-hit*.42;
                            wall=Math.max(wall,Math.min(1,Math.abs(hit)/14));
                            die.angularVelocity=(Number(die.angularVelocity)||0)-Math.sign(hit)*wall*5;
                        }
                        if(die.y<bounds.minY||die.y>bounds.maxY){
                            const hit=die.vy;
                            die.y=Math.max(bounds.minY,Math.min(bounds.maxY,die.y));
                            die.vy=-hit*.44;
                            wall=Math.max(wall,Math.min(1,Math.abs(hit)/14));
                            die.angularVelocity=(Number(die.angularVelocity)||0)+Math.sign(hit)*wall*4;
                        }
                        if(edgeX||edgeY)wall=Math.max(wall,Math.min(.85,Math.hypot(drag.vx,drag.vy)*.27));
                        die.impact=Math.max(wall,(Number(die.impact)||0)*Math.pow(.78,dt));
                        const targetTilt=Math.max(-24,Math.min(24,
                            die.vx*1.3+die.vy*.65+drag.vx*(4.1+i*.13)-drag.vy*1.85));
                        die.angularVelocity=((Number(die.angularVelocity)||0)
                            +(targetTilt-(Number(die.tilt)||0))*.16*dt)*Math.pow(.66,dt);
                        die.tilt=Math.max(-26,Math.min(26,(Number(die.tilt)||0)+die.angularVelocity*dt));
                    });
                    this.dragFrame=window.requestAnimationFrame(t=>this.dragPhysicsFrame(t));
                },
                beginFreeDieDrag(die,event){
                    if(!die||die.storing||this.busy||(event.pointerType==='mouse'&&event.button!==0))return;
                    if(this.freeDieDrag)this.cancelFreeDieDrag();
                    if(this.placingFrames[die.id]){window.cancelAnimationFrame(this.placingFrames[die.id]);delete this.placingFrames[die.id];die.justPlaced=false;}
                    const group=!!event.shiftKey;
                    const initialSelected=!!die.selected;
                    // Shift + arrastar uma peça não selecionada adiciona-a ao grupo.
                    if(group && !die.selected)die.selected=true;
                    const selected=group ? this.selectedFreeDice() : [die];
                    const members=selected.map(item=>({id:item.id,startX:item.x,startY:item.y}));
                    this.freeDieDrag={id:die.id,ids:members.map(item=>item.id),members,
                        shiftKey:group,initialSelected,pointerId:event.pointerId,
                        fromX:event.clientX,fromY:event.clientY,targetDX:0,targetDY:0,
                        moved:false,fromTray:false,lastX:event.clientX,lastY:event.clientY,
                        lastTime:event.timeStamp||performance.now(),releaseSamples:[{x:event.clientX,y:event.clientY,t:event.timeStamp||performance.now()}],vx:0,vy:0};
                    selected.forEach(item=>{
                        item.heldForHandoff=false;
                        item.vx=0;item.vy=0;item.angularVelocity=0;item.impact=0;
                    });
                    this.error='';
                    this.startDragPhysics();
                },
                moveFreeDie(event){
                    const drag=this.freeDieDrag;
                    if(!drag||drag.pointerId!==event.pointerId)return;
                    const dx=event.clientX-drag.fromX,dy=event.clientY-drag.fromY;
                    if(Math.hypot(dx,dy)>9)drag.moved=true;
                    const elapsed=Math.max(8,(event.timeStamp||0)-drag.lastTime);
                    const sx=event.clientX-drag.lastX,sy=event.clientY-drag.lastY;
                    if(Math.hypot(sx,sy)>.28){
                        drag.vx=drag.vx*.38+(sx/elapsed)*.62;
                        drag.vy=drag.vy*.38+(sy/elapsed)*.62;
                        drag.lastX=event.clientX;drag.lastY=event.clientY;
                        drag.lastTime=event.timeStamp || performance.now();
                        drag.releaseSamples.push({x:event.clientX,y:event.clientY,t:drag.lastTime});
                        drag.releaseSamples=drag.releaseSamples.filter(s=>drag.lastTime-s.t<175).slice(-14);
                    }
                    drag.targetDX=dx;drag.targetDY=dy;
                    this.returnHover=drag.moved&&this.dieDragNearDock(event.clientX,event.clientY);
                },
                // O impulso depende dos últimos ~100 ms, não de um único frame.
                // Um pequeno atraso ao levantar o botão do mouse não o apaga.
                sampleThrowImpulse(drag, releasedAt){
                    const samples=Array.isArray(drag?.releaseSamples)?drag.releaseSamples:[];
                    if(samples.length<2)return {vx:0,vy:0,speed:0};
                    const last=samples[samples.length-1];
                    const age=Math.max(0,releasedAt-last.t);
                    if(age>150)return {vx:0,vy:0,speed:0};
                    let first=samples[0];
                    // Janela curta que privilegia a direção do arremesso.
                    for(let i=samples.length-2;i>=0;i--){
                        first=samples[i];
                        if(last.t-first.t>=75)break;
                    }
                    const elapsed=Math.max(12,last.t-first.t);
                    const vx=(last.x-first.x)/elapsed;
                    const vy=(last.y-first.y)/elapsed;
                    const freshness=Math.max(.2,1-Math.max(0,age-35)/155);
                    const averaged=Math.hypot(vx,vy);
                    const smoothSpeed=Math.hypot(drag.vx,drag.vy);
                    const blend=averaged*.72+smoothSpeed*.28;
                    return {vx:vx*freshness,vy:vy*freshness,speed:blend*freshness};
                },
                // Ao apenas colocar o dado, a mola termina de acompanhar a mão
                // e o poliedro assenta em vez de parar na posição de um frame.
                settlePlacedDice(drag,selected){
                    selected.forEach(item=>{
                        const member=drag.members.find(part=>part.id===item.id);
                        if(!member)return;
                        const bounds=this.freeDieBounds(item);
                        const endX=Math.max(bounds.minX,Math.min(bounds.maxX,member.startX+drag.targetDX));
                        const endY=Math.max(bounds.minY,Math.min(bounds.maxY,member.startY+drag.targetDY));
                        if(this.placingFrames[item.id])window.cancelAnimationFrame(this.placingFrames[item.id]);
                        const startX=item.x,startY=item.y,tilt=Number(item.tilt)||0;
                        const impact=Number(item.impact)||0;
                        const duration=235,started=performance.now();
                        item.justPlaced=true;
                        const advance=now=>{
                            if(!this.freeDice.includes(item)||this.freeDieDrag?.ids?.includes(item.id)){
                                delete this.placingFrames[item.id];return;
                            }
                            const t=Math.max(0,Math.min(1,(now-started)/duration));
                            const eased=1-Math.pow(1-t,3);
                            item.x=startX+(endX-startX)*eased;
                            item.y=startY+(endY-startY)*eased;
                            item.tilt=tilt*(1-eased)+Math.sin(t*Math.PI*2)*1.4*(1-t);
                            item.impact=impact*(1-eased);
                            item.angularVelocity=0;
                            if(t<1){this.placingFrames[item.id]=window.requestAnimationFrame(advance);}
                            else{
                                item.x=endX;item.y=endY;item.tilt=0;
                                item.restAngle=0;item.vx=0;item.vy=0;
                                item.angularVelocity=0;item.impact=0;item.justPlaced=false;
                                delete this.placingFrames[item.id];
                            }
                        };
                        this.placingFrames[item.id]=window.requestAnimationFrame(advance);
                    });
                },
                releaseFreeDie(event){
                    const drag=this.freeDieDrag;
                    if(!drag||drag.pointerId!==event.pointerId)return;
                    this.moveFreeDie(event);
                    this.stopDragPhysics();
                    this.freeDieDrag=null;
                    this.returnHover=false;
                    const die=this.freeDice.find(item=>item.id===drag.id);
                    if(!die)return;
                    if(!drag.moved){
                        drag.members.forEach(({id})=>{
                            const item=this.freeDice.find(v=>v.id===id);
                            if(item){item.tilt=0;item.impact=0;item.angularVelocity=0;}
                        });
                        if(drag.shiftKey){
                            // Um Shift + clique desmarca um selecionado, ou
                            // inclui um ainda não escolhido, sem limpar o resto.
                            die.selected=!drag.initialSelected;
                        }else{
                            this.toggleFreeDieSelection(die,false);
                        }
                        return;
                    }
                    const selected=drag.members.map(item=>this.freeDice.find(v=>v.id===item.id)).filter(Boolean);
                    // Soltar sobre o dado da bandeja guarda o grupo inteiro.
                    if(this.dieDragNearDock(event.clientX,event.clientY)) {
                        this.storeFreeDice(selected);
                        this.error='';
                        return;
                    }
                    // Gestos suaves apenas posicionam. Um impulso moderado e recente
                    // já é suficiente para rolar, sem precisar sacudir o dado.
                    const impulse=this.sampleThrowImpulse(drag,event.timeStamp||performance.now());
                    const displacement=Math.hypot(drag.targetDX,drag.targetDY);
                    const walked=(drag.releaseSamples||[]).reduce((sum,point,index,array)=>{
                        if(index===0)return 0;
                        return sum+Math.hypot(point.x-array[index-1].x,point.y-array[index-1].y);
                    },0);
                    const shouldThrow=displacement>=18 && walked>=24 && impulse.speed>=.34;
                    if(!shouldThrow){
                        this.settlePlacedDice(drag,selected);
                        return;
                    }
                    const count=selected.length;
                    const origins=selected.map((item,i)=>{
                        const spread=i-(count-1)/2;
                        item.heldForHandoff=true;
                        return {x:item.x,y:item.y,tilt:(Number(item.tilt)||0)+(Number(item.restAngle)||0),scale:1.055,
                            parts:item.sides===100?this.percentileOffsets(item).map(part=>({
                                x:item.x+(Number(part.dx)||0),y:item.y+(Number(part.dy)||0),
                                tilt:(Number(item.tilt)||0)+(Number(part.rotation)||0)})):null,
                            vx:Math.max(-4.2,Math.min(4.2,
                                impulse.vx+(Number(item.vx)||0)/22+spread*.14)),
                            vy:Math.max(-4.2,Math.min(4.2,
                                impulse.vy+(Number(item.vy)||0)/22+(i%3-1)*.13))};
                    });
                    // Um Shift + arraste lança todos os escolhidos no mesmo gesto.
                    // Sem Shift, a versão aprovada continua lançando somente um.
                    void this.throwFreeDice(selected,origins);
                },
                cancelFreeDieDrag(){
                    if(this.freeDieDrag){
                        this.freeDieDrag.members.forEach(({id})=>{
                            const die=this.freeDice.find(item=>item.id===id);
                            if(die){die.tilt=0;die.impact=0;die.angularVelocity=0;}
                        });
                    }
                    this.freeDieDrag=null;
                    this.returnHover=false;
                    this.stopDragPhysics();
                },
                beginFreeDiceHandoff(token){
                    if(!token||token!==this.handoffToken)return;
                    const ids=new Set(this.handoffIds);
                    this.freeDice.forEach(die=>{if(ids.has(die.id))die.launching=true;});
                    if(this.handoffTimer!==null)window.clearTimeout(this.handoffTimer);
                    this.handoffTimer=window.setTimeout(()=>{
                        if(this.handoffToken!==token)return;
                        this.freeDice=this.freeDice.filter(die=>!ids.has(die.id));
                        this.handoffToken=null;
                        this.handoffIds=[];
                        this.handoffTimer=null;
                    },135);
                },
                completeFreeDiceHandoff(token){
                    if(token!==this.handoffToken)return;
                    if(this.handoffTimer!==null)window.clearTimeout(this.handoffTimer);
                    const ids=new Set(this.handoffIds);
                    this.freeDice=this.freeDice.filter(die=>!ids.has(die.id));
                    this.handoffToken=null;this.handoffIds=[];this.handoffTimer=null;
                },
                cancelFreeDiceHandoff(token){
                    if(token!==this.handoffToken)return;
                    if(this.handoffTimer!==null)window.clearTimeout(this.handoffTimer);
                    const ids=new Set(this.handoffIds);
                    this.freeDice.forEach(die=>{
                        if(ids.has(die.id)){
                            die.launching=false;die.heldForHandoff=false;die.tilt=0;die.impact=0;die.angularVelocity=0;
                        }
                    });
                    this.handoffToken=null;this.handoffIds=[];this.handoffTimer=null;
                },
                async throwSelectedFreeDice(){
                    if(this.busy)return;
                    const selected=this.selectedFreeDice();
                    if(!selected.length){this.error='Selecione os dados para lançar.';return;}
                    await this.throwFreeDice(selected);
                },
                async throwFreeDie(die,origin=null){
                    if(!die)return;
                    await this.throwFreeDice([die],origin?[origin]:null);
                },
                async throwFreeDice(dice,origins=null){
                    if(this.busy||!Array.isArray(dice)||!dice.length)return;
                    const pending=dice.filter(die=>this.freeDice.some(item=>item.id===die.id)).slice(0,this.freeDiceLimit);
                    if(!pending.length)return;
                    const fx=window.SpellboundDiceFX;
                    if(!fx?.start||!fx?.finish){
                        pending.forEach(die=>{die.heldForHandoff=false;die.tilt=0;die.impact=0;});
                        this.error='O palco de rolagem ainda não está disponível.';return;
                    }
                    const shots=pending.map((die,i)=>({
                        die,
                        expression:`1d${die.sides}`,
                        origin:origins?.[i]||{x:die.x,y:die.y,tilt:Number(die.tilt)||0,
                            scale:die.heldForHandoff?1.055:1,
                            parts:die.sides===100?this.percentileOffsets(die).map(part=>({
                                x:die.x+(Number(part.dx)||0),y:die.y+(Number(part.dy)||0),
                                tilt:(Number(die.tilt)||0)+(Number(part.rotation)||0)})):null,
                            vx:(Number(die.vx)||0)/18+(Math.random()-.5)*.9,
                            vy:(Number(die.vy)||0)/18+(Math.random()-.5)*.65},
                    }));
                    const label=shots.length===1?`Dado livre · d${shots[0].die.sides}`:`Dados livres · ${shots.length} dados`;
                    let token=null;
                    token=fx.start({kind:'free',label,parts:shots.map(s=>({label:`d${s.die.sides}`,expression:s.expression,
                            initialFace:s.die.face==null?'?':String(s.die.face),
                            initialTens:s.die.face==null?'00':String(Math.floor((s.die.face%100)/10)*10).padStart(2,'0'),
                            initialUnits:s.die.face==null?'0':String(s.die.face%10)})),
                        origins:shots.map(s=>s.origin),expression:shots[0].expression,
                        onMotionStart:()=>this.beginFreeDiceHandoff(token)});
                    if(!token){
                        pending.forEach(die=>{die.heldForHandoff=false;die.tilt=0;die.impact=0;});
                        this.error='Conclua a rolagem atual antes de lançar outro dado.';return;
                    }
                    this.busy=true;
                    this.handoffToken=token;
                    this.handoffIds=pending.map(die=>die.id);
                    // NÃO remove antes de o palco de animação estar visível.
                    this.error='';
                    try{
                        // Cada dado e resolvido pela API e todas as chamadas partem juntas.
                        // A ordem e preservada para vincular as faces aos poliedros animados.
                        const results=await Promise.all(shots.map(async shot=>{
                            const response=await fetch(this.rollUrl,{
                                method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json',
                                    'X-CSRF-TOKEN':this.csrf,'X-Requested-With':'XMLHttpRequest'},
                                body:JSON.stringify({expression:shot.expression}),
                            });
                            const payload=await response.json().catch(()=>({}));
                            if(!response.ok)throw new Error(payload?.message||'A rolagem não foi concluída.');
                            const roll=payload?.data??payload;
                            const rawTotal=roll?.total??payload?.total??payload?.result;
                            const total=Number(rawTotal);
                            const faces=Array.isArray(roll?.rolls)?roll.rolls.map(Number):[];
                            if(rawTotal==null||!Number.isFinite(total)||!Number.isInteger(faces[0])
                                ||faces[0]<1||faces[0]>shot.die.sides){
                                throw new Error('A API não retornou uma face válida.');
                            }
                            return {total,rolls:faces,modifier:Number(roll?.modifier)||0};
                        }));
                        const total=results.reduce((sum,result)=>sum+result.total,0);
                        const faces=results.flatMap(result=>result.rolls);
                        const ok=await fx.finish(token,{label,expression:shots.length===1?shots[0].expression:null,
                            total,rolls:faces,results,modifier:0,
                            natural:shots.length===1&&shots[0].die.sides===20?faces[0]:null,
                            selectedIndex:null,fromTray:true,
                            parts:shots.map((shot,i)=>({expression:shot.expression,total:results[i].total,
                                type_label:`d${shot.die.sides}`})),
                        });
                        if(ok===false)throw new Error('Não foi possível apresentar a rolagem.');
                        // O palco entrega as posições reais do repouso. Devolvemos os
                        // mesmos dados à camada interativa antes de esconder os animados.
                        this.completeFreeDiceHandoff(token);
                        const landings=Array.isArray(ok?.landings)?ok.landings:[];
                        pending.forEach((die,index)=>{
                            const pieces=landings[index]?.pieces||[];
                            die.launching=false;
                            die.heldForHandoff=false;
                            die.justLanded=true;
                            die.tilt=0;die.impact=0;die.angularVelocity=0;
                            die.vx=0;die.vy=0;
                            die.face=Number(results[index]?.rolls?.[0])||null;
                            if(die.sides===100 && pieces.length===2){
                                const centerX=(pieces[0].x+pieces[1].x)/2;
                                const centerY=(pieces[0].y+pieces[1].y)/2;
                                die.x=centerX;die.y=centerY;die.restAngle=0;
                                die.landedParts=pieces.map(part=>({
                                    dx:part.x-centerX,dy:part.y-centerY,
                                    rotation:0,
                                }));
                            }else if(pieces[0]){
                                die.x=pieces[0].x;die.y=pieces[0].y;
                                die.restAngle=0;
                                die.landedParts=null;
                            }
                        });
                        this.freeDice=[...this.freeDice,...pending];
                        this.$nextTick(()=>window.requestAnimationFrame(()=>{
                            fx.hideLandedFreeDice?.();
                            window.setTimeout(()=>pending.forEach(die=>{die.justLanded=false;}),100);
                        }));
                    }catch(err){
                        window.SpellboundDiceFX?.fail?.(token,err?.message||'Falha na rolagem');
                        this.cancelFreeDiceHandoff(token);
                        const existing=new Set(this.freeDice.map(die=>die.id));
                        const restored=pending.filter(die=>!existing.has(die.id));
                        restored.forEach(die=>{die.launching=false;die.heldForHandoff=false;die.tilt=0;die.impact=0;});
                        this.freeDice=[...this.freeDice,...restored].slice(0,this.freeDiceLimit);
                        this.error=err?.message||'Não foi possível lançar os dados.';
                    }finally{this.busy=false;}
                },
                }));
            };
            document.addEventListener('alpine:init',register,{once:true});
            if(window.Alpine)register();
        })();
    </script>
@endonce

<div x-data="spellboundDiceTray(@js(url('/api/roll')), @js(csrf_token()))" x-cloak
     @pointermove.window="moveTrayPanel($event);trackTrayPull($event);moveFreeDie($event)"
     @pointerup.window="endTrayPanelDrag();endTrayPull($event);releaseFreeDie($event)"
     @pointercancel.window="endTrayPanelDrag();endTrayPull($event);cancelFreeDieDrag()"
     @resize.window="clampTrayPanel()"
     @spellbound:toggle-dice-tray.window="toggleTray()"
     @spellbound:close-dice.window="closeTray()"
     @blur.window="endTrayPanelDrag();cancelFreeDieDrag()"
     x-effect="window.dispatchEvent(new CustomEvent('spellbound:tray-return-hover',{detail:{hover:!!returnHover}}))">
    <template x-teleport="body">
        <div class="character-sheet-theme sb-dice-dock" data-theme="{{ $characterTheme }}" aria-label="Bandeja de dados livres"
>
            <template x-if="expanded">
            <div class="sb-dice-dock-panel" x-ref="freeDicePanel"
                 :style="trayPanelStyle()" :class="{'is-return-target':returnHover,'is-dragging-panel':!!panelDrag}"
                 @pointerdown="beginTrayPanelDrag($event)" @keydown.escape.window="closeTray()">
                <div class="sb-dice-case" x-ref="freeDiceCase" aria-label="Bandeja de dados. Arraste a superfície livre para mover a bandeja.">
                    <button type="button" class="sb-dice-help-trigger"
                            @pointerdown.stop @click.stop="helpOpen=!helpOpen"
                            @mouseenter="helpOpen=true" @mouseleave="helpOpen=false"
                            @focus="helpOpen=true" @blur="helpOpen=false"
                            :aria-expanded="helpOpen ? 'true' : 'false'" aria-label="Como usar os dados" title="Ajuda">?</button>
                    <div class="sb-dice-dock-grid" x-ref="freeDiceWell" role="group" aria-label="Dados disponíveis na bandeja">
                        @foreach([4,6,8,10,12,20,100] as $sides)
                            <button type="button" class="sb-dice-choice {{ $sides === 100 ? 'is-percentile' : '' }}"
                                    @pointerdown.stop.prevent="beginTrayPull({{ $sides }},$event)"
                                    @keydown.enter.prevent="addFreeDie({{ $sides }})" @keydown.space.prevent="addFreeDie({{ $sides }})"
                                    :disabled="!canAddFreeDie({{ $sides }})" title="Arrastar d{{ $sides }} para a mesa" aria-label="Retirar d{{ $sides }} da bandeja">
                                @if($sides === 100)
                                    <span class="sb-dice-choice-percentile">
                                        <x-ui.poly-die :sides="10" size="37"><span>00</span></x-ui.poly-die>
                                        <x-ui.poly-die :sides="10" size="37"><span>0</span></x-ui.poly-die>
                                    </span>
                                @else
                                    <x-ui.poly-die :sides="$sides" size="47"><span>{{ $sides }}</span></x-ui.poly-die>
                                @endif
                                <span class="sb-dice-choice-label">d{{ $sides }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="sb-dice-dock-foot" data-sb-no-tray-drag>
                        <div class="sb-dice-dock-actions">
                            <button type="button" class="sb-dice-dock-clean sb-dice-icon-action"
                                    @pointerdown.stop @click.stop="clearFreeDice()" :disabled="busy || freeDice.length===0"
                                    title="Limpar a mesa" aria-label="Recolher todos os dados da mesa">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 7h16M9 7V4.5h6V7M6.7 7l.8 12.1c.04.7.6 1.2 1.3 1.2h6.4c.7 0 1.25-.5 1.3-1.2L17.3 7M10 10.5v6M14 10.5v6"/>
                                </svg>
                            </button>
                            <button type="button" class="sb-dice-dock-roll sb-dice-icon-action"
                                    @pointerdown.stop @click.stop="throwSelectedFreeDice()"
                                    :disabled="busy || selectedFreeDiceCount()===0"
                                    :title="'Rolar '+selectedFreeDiceCount()+' dados selecionados'"
                                    :aria-label="'Rolar '+selectedFreeDiceCount()+' dados selecionados'">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 2.8 21 8l-9 5.2L3 8 12 2.8Z"/>
                                    <path d="M3 8v8l9 5.2 9-5.2V8M12 13.2v8"/>
                                    <path d="M8 8h.01M16 8h.01M12 5.5h.01" stroke-width="2.7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div x-show="helpOpen" x-cloak class="sb-dice-help-panel" role="tooltip" data-sb-no-tray-drag>
                    <p><strong>Arrastar pela bandeja:</strong> segure o feltro vazio para reposicionar o estojo. Clique no ícone de d20 à esquerda para recolher ou abrir.</p>
                    <p><strong>Dados:</strong> arraste um dado para a mesa; solte devagar para colocar ou com impulso para lançar. Shift + clique seleciona vários e Shift + arrastar lança o grupo. Solte na bandeja para guardar.</p>
                </div>
                <p x-show="!!error" x-cloak class="sb-dice-error" role="alert" x-text="error"></p>
            </div>
            </template>
        </div>
    </template>
    <template x-teleport="body">
        <div x-show="freeDice.length>0" x-cloak class="character-sheet-theme sb-free-staged-field"
             data-theme="{{ $characterTheme }}">
            <template x-for="die in freeDice" :key="die.id">
                <div class="sb-free-staged-die" :data-sides="die.sides" :style="freeDiePositionStyle(die)"
                     :data-free-die-id="die.id"
                     :class="{'is-dragging':freeDieDrag?.ids?.includes(die.id)||die.heldForHandoff,'is-selected':die.selected,'is-launching':die.launching,'is-returned':die.justLanded,'is-storing':die.storing,'is-placed':die.justPlaced}"
                     @pointerdown.stop.prevent="beginFreeDieDrag(die,$event)"
                     @contextmenu.stop.prevent="removeFreeDie(die.id)"
                     @dblclick.stop.prevent="throwFreeDie(die)"
                     @keydown.enter.prevent="throwFreeDie(die)" @keydown.space.prevent="toggleFreeDieSelection(die,$event.shiftKey)"
                     @keydown.delete.prevent="removeFreeDie(die.id)" @keydown.backspace.prevent="removeFreeDie(die.id)"
                     tabindex="0" role="button" :aria-pressed="die.selected?'true':'false'"
                     :title="'d'+die.sides+' · Arraste para rolar · Solte na bandeja para guardar · Shift+arrastar: lançar grupo'"
                     :aria-label="'d'+die.sides+' · '+(die.selected?'Selecionado':'Não selecionado')+' · Clique para escolher; Shift e clique para selecionar vários; Shift e arraste para lançar o grupo'">
                    @foreach([4,6,8,10,12,20] as $sides)
                        <template x-if="die.sides === {{ $sides }}">
                            <x-ui.poly-die :sides="$sides" size="64"><span x-text="die.face==null ? '?' : String(die.face)"></span></x-ui.poly-die>
                        </template>
                    @endforeach
                    <template x-if="die.sides === 100">
                        <span class="sb-free-percentile-pair" aria-hidden="true">
                            <span class="sb-free-percentile-half" :style="freePercentilePieceStyle(die,0)">
                                <x-ui.poly-die :sides="10" size="64"><span x-text="die.face==null ? '00' : String(Math.floor((die.face%100)/10)*10).padStart(2,'0')"></span></x-ui.poly-die>
                            </span>
                            <span class="sb-free-percentile-half" :style="freePercentilePieceStyle(die,1)">
                                <x-ui.poly-die :sides="10" size="64"><span x-text="die.face==null ? '0' : String(die.face%10)"></span></x-ui.poly-die>
                            </span>
                        </span>
                    </template>
                </div>
            </template>
        </div>
    </template>
</div>

@include('characters.components.sheet-calculator', ['characterTheme' => $characterTheme])
