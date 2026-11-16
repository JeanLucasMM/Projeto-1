@props([
    'sides' => 20,
    'size' => 60,
    'rolling' => false,
])

@php
    $sides = in_array((int) $sides, [4, 6, 8, 10, 12, 20, 100], true) ? (int) $sides : 20;
    $size = min(144, max(24, (int) $size));
@endphp

{{-- V57: polígonos planos, correspondentes ao protótipo. Não modificar a lógica do slot. --}}
<span
    {{ $attributes->class(['sb-poly-die', 'is-rolling' => (bool) $rolling]) }}
    data-sides="{{ $sides }}"
    style="width: {{ $size }}px; height: {{ $size }}px;"
    role="img"
    aria-label="D{{ $sides }}"
>
    <svg viewBox="0 0 100 100" focusable="false" aria-hidden="true">
        <g class="sb-poly-face">
            @switch($sides)
                @case(4)
                    <polygon points="50,8 95,91 5,91" fill="var(--sb-die-main)"/>
                    <polygon points="5,91 50,85 95,91" fill="var(--sb-die-shadow)"/>
                    @break
                @case(6)
                    <rect x="12" y="12" width="76" height="76" fill="var(--sb-die-main)"/>
                    @break
                @case(8)
                    <polygon points="50,4 91,30 91,72 50,97 9,72 9,30" fill="var(--sb-die-mid)"/>
                    <polygon points="50,4 9,30 9,72" fill="var(--sb-die-shadow)"/>
                    <polygon points="50,4 91,30 91,72" fill="var(--sb-die-mid)"/>
                    <polygon points="50,4 91,72 9,72" fill="var(--sb-die-main)"/>
                    <polygon points="9,72 91,72 50,97" fill="var(--sb-die-deep)"/>
                    @break
                @case(10)
                    <polygon points="50,5 93,43 93,72 50,97 7,72 7,43" fill="var(--sb-die-mid)"/>
                    <polygon points="50,5 7,43 7,72 24,70" fill="var(--sb-die-shadow)"/>
                    <polygon points="50,5 93,43 93,72 76,70" fill="var(--sb-die-mid)"/>
                    <polygon points="50,5 76,70 50,84 24,70" fill="var(--sb-die-main)"/>
                    <polygon points="7,72 24,70 50,84 76,70 93,72 50,97" fill="var(--sb-die-deep)"/>
                    @break
                @case(12)
                    <polygon points="50,4 79,13 96,42 96,69 78,91 50,98 22,91 4,69 4,42 21,13" fill="var(--sb-die-mid)"/>
                    <polygon points="50,4 79,13 75,34 50,26 25,34 21,13" fill="var(--sb-die-shadow)"/>
                    <polygon points="21,13 25,34 20,72 4,69 4,42" fill="var(--sb-die-shadow)"/>
                    <polygon points="79,13 96,42 96,69 80,72 75,34" fill="var(--sb-die-mid)"/>
                    <polygon points="50,26 75,34 80,72 50,87 20,72 25,34" fill="var(--sb-die-main)"/>
                    <polygon points="4,69 20,72 50,87 80,72 96,69 78,91 50,98 22,91" fill="var(--sb-die-deep)"/>
                    @break
                @case(20)
                    <polygon points="50,4 90,27 90,72 50,97 10,72 10,27" fill="var(--sb-die-mid)"/>
                    <polygon points="50,4 10,27 50,27" fill="var(--sb-die-shadow)"/>
                    <polygon points="50,4 90,27 50,27" fill="var(--sb-die-mid)"/>
                    <polygon points="10,27 50,27 21,73 10,72" fill="var(--sb-die-shadow)"/>
                    <polygon points="90,27 90,72 79,73 50,27" fill="var(--sb-die-mid)"/>
                    <polygon points="50,27 79,73 21,73" fill="var(--sb-die-main)"/>
                    <polygon points="10,72 21,73 79,73 90,72 50,97" fill="var(--sb-die-deep)"/>
                    @break
                @case(100)
                    <polygon points="50,5 93,43 93,72 50,97 7,72 7,43" fill="var(--sb-die-mid)"/>
                    <polygon points="50,5 7,43 7,72 24,70" fill="var(--sb-die-shadow)"/>
                    <polygon points="50,5 93,43 93,72 76,70" fill="var(--sb-die-mid)"/>
                    <polygon points="50,5 76,70 50,84 24,70" fill="var(--sb-die-main)"/>
                    <polygon points="7,72 24,70 50,84 76,70 93,72 50,97" fill="var(--sb-die-deep)"/>
                    @break
            @endswitch
        </g>
    </svg>
    <span class="sb-poly-number" style="font-size: {{ (int) round($size * ($sides === 100 ? .31 : ($sides >= 10 ? .37 : .47))) }}px;">
        {{ $slot->isEmpty() ? '—' : $slot }}
    </span>
</span>
