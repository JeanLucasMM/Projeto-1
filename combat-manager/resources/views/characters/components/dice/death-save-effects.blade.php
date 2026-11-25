{{--
    Spellbound V66.2.179 · Partição visual do Salvamento Contra a Morte.
    ESCOPOS:
      - Somente aparência / escala do d20 de morte no palco compartilhado.
      - Efeitos de 20 natural, 1 natural e atmosfera da rolagem.
    NÃO COLOCAR AQUI: física, trajetórias, quicadas, tempos ou colisões.
    A fonte única dessas regras continua em attacks.blade.php / SpellboundDiceFX.
--}}
@once
    @push('styles')
        <style>
            /* Um d20 maior, mas a escala fica somente na arte.
               O contêiner que recebe transform/WAAPI continua idêntico
               ao dos demais d20, sem multiplicar a trajetória. */
            .attack-v139-scene.is-death-save .attack-v139-die.is-physics[data-sides="20"] .sb-poly-die {
                scale:1.50;
                transform-origin:50% 50%;
            }
            .attack-v139-scene.is-death-save .attack-v139-die.is-physics[data-sides="20"] {
                filter:drop-shadow(0 12px 15px rgba(51,19,21,.22));
            }
            /* A ambientação não afeta as posições nem os quadros do dado. */
            .attack-v139-scene.is-death-save::before {
                width:min(75vw,640px);
                height:170px;
                background:radial-gradient(ellipse,rgba(128,35,44,.12),transparent 73%);
                opacity:.65;
            }
            /* O motor já emite is-natural-20/is-natural-1; estas classes
               podem receber efeitos próprios aqui em revisões futuras. */
            .attack-v139-scene.is-death-save .attack-v139-die.is-natural-20::before {
                filter:blur(7px);
            }
            .attack-v139-scene.is-death-save .attack-v139-die.is-natural-1::before {
                filter:blur(7px);
            }
            /* Atmosfera separada da física: véu baixo de luz e poeira suave.
               Nenhum transform dos dados, duração ou trajetória é alterado. */
            .attack-v139-scene.is-death-save::after {
                content:"";
                position:fixed;
                left:50%;top:44%;
                width:min(65vw,520px);height:210px;
                transform:translate(-50%,-50%);
                border-radius:50%;
                pointer-events:none;
                opacity:0;
                background:
                    radial-gradient(circle at 18% 38%,rgba(228,161,123,.19) 0 1px,transparent 2px),
                    radial-gradient(circle at 69% 29%,rgba(240,204,150,.16) 0 1px,transparent 2px),
                    radial-gradient(circle at 81% 69%,rgba(229,137,128,.19) 0 1.5px,transparent 3px),
                    radial-gradient(circle at 35% 76%,rgba(222,181,133,.14) 0 1px,transparent 2px);
                animation:sb-death-motes 3.1s ease-in-out both;
                filter:blur(.3px);
            }
            @keyframes sb-death-motes {
                0% {opacity:0;translate:0 14px}
                23% {opacity:.65}
                68% {opacity:.4;translate:0 -6px}
                100% {opacity:0;translate:0 -16px}
            }
            .attack-v139-scene.is-death-save .attack-v139-die.is-natural-20::before {
                background:radial-gradient(circle,rgba(255,231,150,.4),rgba(245,191,85,.14) 45%,transparent 78%);
                filter:blur(9px);
            }
            .attack-v139-scene.is-death-save .attack-v139-die.is-natural-1::before {
                background:radial-gradient(circle,rgba(244,127,118,.30),rgba(158,38,49,.12) 44%,transparent 77%);
                filter:blur(9px);
            }
            @media(max-width:600px) {
                .attack-v139-scene.is-death-save .attack-v139-die.is-physics[data-sides="20"] .sb-poly-die {
                    scale:1.38;
                }
            }
            @media(prefers-reduced-motion:reduce) {
                .attack-v139-scene.is-death-save::after {animation:none!important;opacity:0!important}
                .attack-v139-scene.is-death-save .attack-v139-die.is-natural-20::before,
                .attack-v139-scene.is-death-save .attack-v139-die.is-natural-1::before {
                    filter:none;
                }
            }
        </style>
    @endpush
@endonce
