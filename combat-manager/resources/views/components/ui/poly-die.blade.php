@props([
    'sides' => 20,
    'size' => 60,
    'rolling' => false,
])

@php
    $sides = in_array((int) $sides, [4, 6, 8, 10, 12, 20, 100], true) ? (int) $sides : 20;
    $size = min(144, max(24, (int) $size));
@endphp

{{-- V66.2.138: d100 exibido como dois d10; demais dados aprovados preservados. --}}
@php
    // d100: dois d10 completos, cada qual com o tamanho solicitado pelo chamador.
    $d100Gap = max(4, (int) round($size * .10));
    $d100Width = $size * 2 + $d100Gap;
    $d100Text = trim(strip_tags((string) $slot));
    $d100Initial = ctype_digit($d100Text) && (int) $d100Text <= 100
        ? (int) $d100Text
        : null;
@endphp
<span
    {{ $attributes->class(['sb-poly-die', 'is-rolling' => (bool) $rolling]) }}
    data-sides="{{ $sides }}"
    style="width: {{ $sides === 100 ? $d100Width : $size }}px; height: {{ $size }}px;"
    role="img"
    aria-label="D{{ $sides }}"
    @if($sides === 100)
        x-data="{
            d100Number: {{ $d100Initial === null ? 'null' : $d100Initial }},
            syncD100(source) {
                const value = (source?.textContent || '').trim();
                const number = Number(value);
                this.d100Number = /^\d{1,3}$/.test(value) && Number.isInteger(number) && number <= 100
                    ? number : null;
            }
        }"
        x-init="
            const source = $el.querySelector('.sb-d100-source');
            syncD100(source);
            const observer = new MutationObserver(() => syncD100(source));
            observer.observe(source, { childList: true, characterData: true, subtree: true });
        "
    @endif
>
    @if($sides === 100)
        {{-- A fonte permanece no DOM para receber x-text de Alpine; nunca é exibida. --}}
        <span class="sb-d100-source" x-ref="d100Source" aria-hidden="true">{{ $slot }}</span>
        @for($d100Part = 0; $d100Part < 2; $d100Part++)
            <span class="sb-d100-die" style="width: {{ $size }}px; height: {{ $size }}px;">
                <svg viewBox="0 0 100 100" focusable="false" aria-hidden="true">
                    <g class="sb-poly-face">
                        {{-- Geometria do d10 aprovada na V66.2.134, repetida sem modificações. --}}
                        <polygon points="50,7 7,53 7,62 50,95 93,62 93,53" fill="var(--sb-d10-shell)"/>
                        <polygon points="50,7 7,53 22,53" fill="var(--sb-d10-left)"/>
                        <polygon points="50,7 78,53 93,53" fill="var(--sb-d10-right)"/>
                        <polygon points="7,53 7,62 50,95 50,68 22,53" fill="var(--sb-d10-bottom-left)"/>
                        <polygon points="50,68 50,95 93,62 93,53 78,53" fill="var(--sb-d10-bottom-right)"/>
                        <polygon points="50,7 78,53 50,68 22,53" fill="var(--sb-d10-front)"/>
                        <path d="M50 7 L93 53 L93 62 L50 95 L7 62 L7 53 Z"
                              fill="none" stroke="var(--sb-d10-outline)" stroke-width="1.25" stroke-linejoin="round"/>
                    </g>
                </svg>
                <span class="sb-poly-number" style="font-size: {{ (int) round($size * ($d100Part === 0 ? .32 : .39)) }}px;"
                      x-text="d100Number === null ? '—' : {{ $d100Part === 0 ? "String(Math.floor((d100Number % 100) / 10) * 10).padStart(2, '0')" : "String(d100Number % 10)" }}">
                    {{ $d100Initial === null ? '—' : ($d100Part === 0 ? str_pad((string) (intdiv($d100Initial % 100, 10) * 10), 2, '0', STR_PAD_LEFT) : ($d100Initial % 10)) }}
                </span>
            </span>
        @endfor
    @else
    <svg viewBox="0 0 100 100" focusable="false" aria-hidden="true">
        <g class="sb-poly-face">
            @switch($sides)
                @case(4)
                    {{-- V66.2.130: um prisma triangular com faces externas reais.
                         O centro fica completamente liso para a leitura do número. --}}
                    {{-- Base traseira: 10 unidades abaixo e à direita da face frontal. --}}
                    <polygon points="57,20 97,90 17,90" fill="var(--sb-d4-back)"/>
                    {{-- Lateral escurecida à direita: volume visível fora do triângulo. --}}
                    <polygon points="47,10 87,80 97,90 57,20" fill="var(--sb-d4-side)"/>
                    {{-- Base inferior: a profundidade aparece abaixo, sem invadir o número. --}}
                    <polygon points="7,80 87,80 97,90 17,90" fill="var(--sb-d4-bottom)"/>
                    {{-- Superfície frontal totalmente lisa. --}}
                    <polygon points="47,10 87,80 7,80" fill="var(--sb-d4-main)"/>
                    <path d="M47 10 L87 80 L97 90 L17 90 L7 80 Z"
                          fill="none" stroke="var(--sb-d4-outline)"
                          stroke-width="1.2" stroke-linejoin="round"/>
                    @break
                @case(6)
                    {{-- V66.2.127: cubo minimalista com face frontal lisa, como o d4 aprovado. --}}
                    {{-- A sombra e o brilho ficam somente nas duas faces externas ao quadrado. --}}
                    <polygon points="14,26 27,13 88,13 75,26" fill="var(--sb-d6-top)"/>
                    <polygon points="75,26 88,13 88,75 75,88" fill="var(--sb-d6-side)"/>
                    <rect x="14" y="26" width="61" height="62" fill="var(--sb-d6-front)"/>
                    <path d="M27 13 H88 V75 L75 88 H14 V26 Z" fill="none"
                          stroke="var(--sb-d6-outline)" stroke-width="1.35" stroke-linejoin="round"/>
                    @break
                @case(8)
                    {{-- V66.2.132: octaedro com face frontal ampla e lisa.
                         Faces de volume somente por fora da area do numero. --}}
                    <polygon points="50,6 92,43 83,74 50,97 17,74 8,43" fill="var(--sb-d8-shell)"/>
                    <polygon points="50,6 8,43 17,74 21,70 50,12" fill="var(--sb-d8-left)"/>
                    <polygon points="50,6 92,43 83,74 79,70 50,12" fill="var(--sb-d8-right)"/>
                    <polygon points="17,74 50,97 83,74 79,70 21,70" fill="var(--sb-d8-bottom)"/>
                    <polygon points="50,12 79,70 21,70" fill="var(--sb-d8-front)"/>
                    <path d="M50 6 L92 43 L83 74 L50 97 L17 74 L8 43 Z"
                          fill="none" stroke="var(--sb-d8-outline)" stroke-width="1.25" stroke-linejoin="round"/>
                    @break
                @case(10)
                    {{-- V66.2.134: d10 de silhueta bicônica, conforme a referência.
                         Centro da face superior liso; outras faces apenas fora da região do número. --}}
                    {{-- Face superior principal com quatro vértices: topo, esquerda, centro inferior, direita. --}}
                    <polygon points="50,7 7,53 7,62 50,95 93,62 93,53" fill="var(--sb-d10-shell)"/>
                    {{-- Faces laterais superiores: laterais estreitas, não invadem o número. --}}
                    <polygon points="50,7 7,53 22,53" fill="var(--sb-d10-left)"/>
                    <polygon points="50,7 78,53 93,53" fill="var(--sb-d10-right)"/>
                    {{-- Faces inferiores com a aresta central descendente, dando profundidade real. --}}
                    <polygon points="7,53 7,62 50,95 50,68 22,53" fill="var(--sb-d10-bottom-left)"/>
                    <polygon points="50,68 50,95 93,62 93,53 78,53" fill="var(--sb-d10-bottom-right)"/>
                    {{-- Face principal, inteiramente limpa, com espaço para o número de 1 a 10. --}}
                    <polygon points="50,7 78,53 50,68 22,53" fill="var(--sb-d10-front)"/>
                    <path d="M50 7 L93 53 L93 62 L50 95 L7 62 L7 53 Z"
                          fill="none" stroke="var(--sb-d10-outline)" stroke-width="1.25" stroke-linejoin="round"/>
                    @break
                @case(12)
                    {{-- V66.2.137: cinco faces pentagonais externas, com a face frontal
                         mais ampla e regular. O numero permanece sobre uma superficie lisa. --}}
                    <polygon points="50,18 17,41 4,39 16,15 50,5" fill="var(--sb-d12-upper-left)"/>
                    <polygon points="50,18 83,41 96,39 84,15 50,5" fill="var(--sb-d12-upper-right)"/>
                    <polygon points="17,41 30,78 25,92 8,72 4,39" fill="var(--sb-d12-left)"/>
                    <polygon points="83,41 70,78 75,92 92,72 96,39" fill="var(--sb-d12-right)"/>
                    <polygon points="30,78 70,78 75,92 50,98 25,92" fill="var(--sb-d12-bottom)"/>
                    {{-- Pentagono central: sem gradientes, linhas ou facetas internas. --}}
                    <polygon points="50,18 83,41 70,78 30,78 17,41" fill="var(--sb-d12-front)"/>
                    {{-- Contornos finos somente nas arestas externas da face central. --}}
                    <path d="M50 18 L83 41 L70 78 L30 78 L17 41 Z" fill="none"
                          stroke="var(--sb-d12-inner-rim)" stroke-opacity=".68"
                          stroke-width=".9" stroke-linejoin="round"/>
                    {{-- Reflexo restrito à aresta superior, sem interferir no número. --}}
                    <path d="M17 41 L50 18 L83 41" fill="none"
                          stroke="var(--sb-d12-highlight)" stroke-opacity=".48"
                          stroke-width=".85" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M50 5 L84 15 L96 39 L92 72 L75 92 L50 98 L25 92 L8 72 L4 39 L16 15 Z"
                          fill="none" stroke="var(--sb-d12-outline)" stroke-width="1.1" stroke-linejoin="round"/>
                    @break
                @case(20)
                    {{-- Icosaedro: triangulo frontal amplo e faces externas ao numero. --}}
                    <polygon points="50,5 90,28 90,73 50,97 10,73 10,28" fill="var(--sb-die-edge)"/>
                    <polygon points="50,5 10,28 18,74 50,19" fill="var(--sb-die-left)"/>
                    <polygon points="50,5 90,28 82,74 50,19" fill="var(--sb-die-top)"/>
                    <polygon points="10,28 10,73 18,74" fill="var(--sb-die-bottom)"/>
                    <polygon points="90,28 90,73 82,74" fill="var(--sb-die-side)"/>
                    <polygon points="18,74 82,74 50,97" fill="var(--sb-die-bottom)"/>
                    <polygon points="50,19 82,74 18,74" fill="var(--sb-die-front)"/>
                    <path d="M50 5 L90 28 L90 73 L50 97 L10 73 L10 28 Z" fill="none" stroke="var(--sb-die-outline)" stroke-width="1.35" stroke-linejoin="round"/>
                    @break
            @endswitch
        </g>
    </svg>
    <span class="sb-poly-number" style="font-size: {{ (int) round($size * ($sides === 4 ? .37 : ($sides === 6 || $sides === 8 ? .40 : ($sides === 100 ? .31 : ($sides >= 10 ? .37 : .47))))) }}px;">
        {{ $slot->isEmpty() ? '—' : $slot }}
    </span>
    @endif
</span>
