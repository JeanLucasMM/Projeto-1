@once
<style>
/* V57 | Spellbound: geometria flat inspirada no exemplo de seis dados. */
.sb-poly-die {
    /* Deriva a base da paleta ativa da ficha. Variáveis de cada tema vêm do CSS central. */
    /* Mesma base cromatica do d6 aprovado, para todos os poliedros. */
    --sb-die-base: color-mix(in srgb, var(--theme-accent, #9e7457) 85%, #b69279);
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
/* V66.2.128: TODOS os dados partilham a mesma base do d6 aprovado.
   Face principal lisa para o numero, luz por cima, sombra apenas fora da face. */
.sb-poly-die {
    --sb-die-front: color-mix(in srgb, var(--sb-die-base) 88%, white 12%);
    --sb-die-top: color-mix(in srgb, var(--sb-die-base) 73%, white 27%);
    --sb-die-left: color-mix(in srgb, var(--sb-die-base) 72%, black 28%);
    --sb-die-side: color-mix(in srgb, var(--sb-die-base) 68%, black 32%);
    --sb-die-bottom: color-mix(in srgb, var(--sb-die-base) 64%, black 36%);
    --sb-die-edge: color-mix(in srgb, var(--sb-die-base) 75%, black 25%);
    --sb-die-outline: color-mix(in srgb, var(--sb-die-base) 56%, black 44%);
}
/* V66.2.130 | d4: profundidade geométrica externa, seguindo o d6.
   Nenhum sombreamento ou divisão atravessa a face frontal. */
.sb-poly-die[data-sides="4"] {
    --sb-d4-main: color-mix(in srgb, var(--sb-die-base) 87%, white 13%);
    --sb-d4-back: color-mix(in srgb, var(--sb-die-base) 64%, black 36%);
    --sb-d4-side: color-mix(in srgb, var(--sb-die-base) 69%, black 31%);
    --sb-d4-bottom: color-mix(in srgb, var(--sb-die-base) 59%, black 41%);
    --sb-d4-outline: color-mix(in srgb, var(--sb-die-base) 56%, black 44%);
}
/* V66.2.130 | A sombra de contato do d4 acompanha a extrusão. */
.sb-poly-die[data-sides="4"] { filter: drop-shadow(0 2px 1.5px rgba(0,0,0,.18)); }
/* V66.2.127 | d6: numeracao no centro da face frontal; brilho e sombra so fora dela. */

/* V66.2.132 | d8: volumes externos amplos e frente lisa como no d6 aprovado.
   A face do numero utiliza a mesma cor principal do d6. */
.sb-poly-die[data-sides="8"] {
    --sb-d8-shell: color-mix(in srgb, var(--sb-die-base) 75%, black 25%);
    --sb-d8-front: var(--sb-die-front);
    --sb-d8-left: var(--sb-die-left);
    --sb-d8-right: var(--sb-die-top);
    --sb-d8-bottom: var(--sb-die-bottom);
    --sb-d8-outline: var(--sb-die-outline);
}
/* V66.2.134 | d10: forma bicônica / trapezoédrica, conforme referência enviada.
   Frente lisa, laterais superiores discretas e base dividida em duas faces externas. */
.sb-poly-die[data-sides="10"] {
    --sb-d10-shell: var(--sb-die-edge);
    --sb-d10-front: var(--sb-die-front);
    --sb-d10-left: var(--sb-die-left);
    --sb-d10-right: var(--sb-die-top);
    --sb-d10-bottom-left: color-mix(in srgb, var(--sb-die-base) 78%, black 22%);
    --sb-d10-bottom-right: var(--sb-die-side);
    --sb-d10-outline: var(--sb-die-outline);
}

/* V66.2.137 | d12: pentagono frontal maior, profundidade equilibrada ao redor.
   Somente as facetas externas recebem diferentes tons da paleta ativa. */
.sb-poly-die[data-sides="12"] {
    --sb-d12-front: var(--sb-die-front);
    --sb-d12-upper-left: color-mix(in srgb, var(--sb-die-base) 69%, white 31%);
    --sb-d12-upper-right: color-mix(in srgb, var(--sb-die-base) 80%, white 20%);
    --sb-d12-left: color-mix(in srgb, var(--sb-die-base) 75%, black 25%);
    --sb-d12-right: color-mix(in srgb, var(--sb-die-base) 72%, black 28%);
    --sb-d12-bottom: color-mix(in srgb, var(--sb-die-base) 67%, black 33%);
    --sb-d12-outline: var(--sb-die-outline);
    --sb-d12-inner-rim: color-mix(in srgb, var(--sb-die-base) 72%, black 28%);
    --sb-d12-highlight: color-mix(in srgb, var(--sb-die-base) 48%, white 52%);
}
.sb-poly-die[data-sides="6"] {
    --sb-d6-front: color-mix(in srgb, var(--sb-die-base) 88%, white 12%);
    --sb-d6-top: color-mix(in srgb, var(--sb-die-base) 73%, white 27%);
    --sb-d6-side: color-mix(in srgb, var(--sb-die-base) 68%, black 32%);
    --sb-d6-outline: color-mix(in srgb, var(--sb-die-base) 56%, black 44%);
}
/* V66.2.138 | D100: par de d10 completos, sem alterar a geometria aprovada.
   O elemento-fonte recebe as mudanças de face do Alpine e nunca ocupa espaço. */
.sb-poly-die[data-sides="100"] {
    --sb-d10-shell: var(--sb-die-edge);
    --sb-d10-front: var(--sb-die-front);
    --sb-d10-left: var(--sb-die-left);
    --sb-d10-right: var(--sb-die-top);
    --sb-d10-bottom-left: color-mix(in srgb, var(--sb-die-base) 78%, black 22%);
    --sb-d10-bottom-right: var(--sb-die-side);
    --sb-d10-outline: var(--sb-die-outline);
    gap: 0;
    justify-content: space-between;
    filter: none;
}
.sb-poly-die[data-sides="100"] .sb-d100-die {
    display: inline-block;
    position: relative;
    flex: 0 0 auto;
    filter: drop-shadow(0 2px 1.5px rgba(0,0,0,.13));
}
.sb-poly-die[data-sides="100"] .sb-d100-source {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: 0 !important;
    overflow: hidden !important;
    clip-path: inset(50%) !important;
    white-space: nowrap !important;
    pointer-events: none !important;
}
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
.sb-poly-die[data-sides="4"] .sb-poly-number { transform: translateY(0); }
.sb-poly-die[data-sides="6"] .sb-poly-number { transform: translate(-5.5%, 7%); }
.sb-poly-die[data-sides="8"] .sb-poly-number { transform: translateY(0); }
.sb-poly-die[data-sides="12"] .sb-poly-number { transform: translateY(3%); }
.sb-poly-die[data-sides="10"] .sb-poly-number,
.sb-poly-die[data-sides="20"] .sb-poly-number,
.sb-poly-die[data-sides="100"] .sb-poly-number { transform: translateY(0); }
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
