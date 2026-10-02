@props([
    'sides' => 20,
    'size' => 48,
])

@php
    $sides =
        in_array(
            (int) $sides,
            [4, 6, 8, 10, 12, 20],
            true
        )
            ? (int) $sides
            : 20;

    $size = max(20, (int) $size);
@endphp

<span
    {{ $attributes->merge([
        'class' => 'relative inline-flex shrink-0 items-center justify-center',
    ]) }}
    style="
        width: {{ $size }}px;
        height: {{ $size }}px;
    "
>
    <svg
        viewBox="0 0 100 100"
        class="
            absolute
            inset-0
            h-full
            w-full
            overflow-visible
            drop-shadow-[0_3px_5px_rgba(83,21,15,.24)]
        "
        aria-hidden="true"
    >
        @switch($sides)

            {{-- =========================================================
                 D4 — VETOR PLANO
            ========================================================== --}}
            @case(4)
                <polygon
                    points="50,12 92,86 8,86"
                    fill="#7f261d"
                />
                @break


            {{-- =========================================================
                 D6 — CUBO PLANO
            ========================================================== --}}
            @case(6)
                <rect
                    x="12"
                    y="12"
                    width="76"
                    height="76"
                    rx="14"
                    fill="#7f261d"
                />
                @break


            {{-- =========================================================
                 D8 — OCTAEDRO
            ========================================================== --}}
            @case(8)
                <polygon
                    points="50,15 15,70 5,35"
                    fill="#a0523d"
                />
                <polygon
                    points="50,15 85,70 95,35"
                    fill="#7f261d"
                />
                <polygon
                    points="15,70 85,70 50,95"
                    fill="#53150f"
                />
                <polygon
                    points="50,15 85,70 15,70"
                    fill="#6b1d14"
                />
                @break


            {{-- =========================================================
                 D10 — BIPIRÂMIDE PENTAGONAL
            ========================================================== --}}
            @case(10)
                <polygon
                    points="50,4 91,35 84,77 50,96 16,77 9,35"
                    fill="#53150f"
                />
                <polygon
                    points="50,4 50,51 9,35"
                    fill="#a0523d"
                />
                <polygon
                    points="50,4 91,35 50,51"
                    fill="#8c3b2d"
                />
                <polygon
                    points="9,35 50,51 16,77"
                    fill="#7f261d"
                />
                <polygon
                    points="91,35 84,77 50,51"
                    fill="#6b1d14"
                />
                <polygon
                    points="16,77 50,51 50,96"
                    fill="#642018"
                />
                <polygon
                    points="50,51 84,77 50,96"
                    fill="#5a1a13"
                />
                @break


            {{-- =========================================================
                 D12 — DODECAEDRO
            ========================================================== --}}
            @case(12)
                <polygon
                    points="23,41 5,35 20,10 50,5 50,22"
                    fill="#a0523d"
                />
                <polygon
                    points="50,5 80,10 95,35 77,41 50,22"
                    fill="#8c3b2d"
                />
                <polygon
                    points="95,35 98,65 85,90 67,73 77,41"
                    fill="#6b1d14"
                />
                <polygon
                    points="67,73 85,90 50,98 20,90 33,73"
                    fill="#53150f"
                />
                <polygon
                    points="33,73 20,90 2,65 5,35 23,41"
                    fill="#642018"
                />
                <polygon
                    points="50,22 77,41 67,73 33,73 23,41"
                    fill="#7f261d"
                />
                @break


            {{-- =========================================================
                 D20 — ICOSAEDRO
            ========================================================== --}}
            @default
                <polygon
                    points="50,5 50,28 5,28"
                    fill="#a0523d"
                />
                <polygon
                    points="50,5 95,28 50,28"
                    fill="#8c3b2d"
                />
                <polygon
                    points="5,28 50,28 25,65"
                    fill="#7f261d"
                />
                <polygon
                    points="95,28 75,65 50,28"
                    fill="#6b1d14"
                />
                <polygon
                    points="5,28 25,65 5,72"
                    fill="#8c3b2d"
                />
                <polygon
                    points="95,28 95,72 75,65"
                    fill="#5a1a13"
                />
                <polygon
                    points="5,72 25,65 50,95"
                    fill="#6b1d14"
                />
                <polygon
                    points="25,65 75,65 50,95"
                    fill="#642018"
                />
                <polygon
                    points="75,65 95,72 50,95"
                    fill="#53150f"
                />
                <polygon
                    points="50,28 75,65 25,65"
                    fill="#7f261d"
                />
        @endswitch
    </svg>

    <span
        class="
            relative
            z-10
            flex
            items-center
            justify-center
            font-serif
            font-black
            leading-none
            text-[#fffaf2]
            drop-shadow-[0_2px_2px_rgba(83,21,15,.50)]
        "
        style="
            font-size: {{ max(8, (int) round($size * .35)) }}px;
        "
    >
        {{ $slot }}
    </span>
</span>