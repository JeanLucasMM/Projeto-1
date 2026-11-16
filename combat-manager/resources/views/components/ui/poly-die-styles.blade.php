@once
<style>
/* V57 | Spellbound: geometria flat inspirada no exemplo de seis dados. */
.sb-poly-die {
    /* Deriva a base da paleta ativa da ficha. Variáveis de cada tema vêm do CSS central. */
    --sb-die-base: var(--theme-accent-strong, #813d35);
    --sb-die-main: var(--sb-die-base);
    --sb-die-mid: color-mix(in srgb, var(--sb-die-base) 91%, white 9%);
    --sb-die-shadow: color-mix(in srgb, var(--sb-die-base) 88%, black 12%);
    --sb-die-deep: color-mix(in srgb, var(--sb-die-base) 70%, black 30%);
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    vertical-align: middle;
    isolation: isolate;
    filter: drop-shadow(0 2px 1.5px rgba(0,0,0,.13));
}
/* Variações discretas, mantendo toda a coleção coerente com o tema. */
.sb-poly-die[data-sides="4"] { --sb-die-base: var(--theme-accent-strong, #813d35); }
.sb-poly-die[data-sides="6"] { --sb-die-base: color-mix(in srgb, var(--theme-accent, #9e7457) 85%, #b69279); }
.sb-poly-die[data-sides="8"] { --sb-die-base: color-mix(in srgb, var(--theme-accent-strong, #813d35) 85%, #916184); }
.sb-poly-die[data-sides="10"],
.sb-poly-die[data-sides="100"] { --sb-die-base: color-mix(in srgb, var(--theme-accent, #9e7457) 80%, #c09743); }
.sb-poly-die[data-sides="12"] { --sb-die-base: color-mix(in srgb, var(--theme-accent-strong, #813d35) 85%, #c15d64); }
.sb-poly-die[data-sides="20"] { --sb-die-base: color-mix(in srgb, var(--theme-accent, #9e7457) 76%, var(--theme-accent-strong, #813d35)); }
.sb-poly-die svg { display: block; width: 100%; height: 100%; overflow: visible; }
.sb-poly-die .sb-poly-face { transform-origin: 50% 50%; }
.sb-poly-die .sb-poly-number {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-family: system-ui,-apple-system,"Segoe UI",sans-serif;
    font-weight: 750;
    font-variant-numeric: tabular-nums;
    line-height: 1;
    letter-spacing: -.035em;
    text-align: center;
    pointer-events: none;
    text-shadow: 0 1px 1px rgba(0,0,0,.1);
}
.sb-poly-die[data-sides="4"] .sb-poly-number { transform: translateY(10%); }
.sb-poly-die[data-sides="8"] .sb-poly-number,
.sb-poly-die[data-sides="20"] .sb-poly-number { transform: translateY(-4%); }
.sb-poly-die[data-sides="10"] .sb-poly-number,
.sb-poly-die[data-sides="100"] .sb-poly-number { transform: translateY(5%); }
.sb-poly-die.is-rolling .sb-poly-face { animation: sb-poly-rock .22s ease-in-out infinite alternate; }
.sb-poly-die.is-rolling .sb-poly-number { animation: sb-poly-flash .1s ease-in-out infinite alternate; }
.sb-poly-die.is-landed .sb-poly-face { animation: sb-poly-land .25s cubic-bezier(.2,.85,.24,1) both; }
@keyframes sb-poly-rock { from { transform: rotate(-5deg) scale(.98); } to { transform: rotate(5deg) scale(1.02); } }
@keyframes sb-poly-flash { from { opacity:.75; } to { opacity:1; } }
@keyframes sb-poly-land { from { transform: rotate(3deg) scale(1.04); } to { transform: rotate(0) scale(1); } }
@media (prefers-reduced-motion: reduce) {
    .sb-poly-die.is-rolling .sb-poly-face,
    .sb-poly-die.is-rolling .sb-poly-number,
    .sb-poly-die.is-landed .sb-poly-face { animation: none !important; }
}
</style>
@endonce
