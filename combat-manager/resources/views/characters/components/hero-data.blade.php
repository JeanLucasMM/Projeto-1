@props([
    'character',
    'imageUrl' => null,
    'className' => 'Classe não definida',
    'subclassName' => null,
    'proficiencyBonus' => 2,
    'classes' => collect(),
    'hasMulticlass' => false,
])

@php
    /*
    |--------------------------------------------------------------------------
    | CLASSES
    |--------------------------------------------------------------------------
    */
    $sortedClasses = collect($classes)
        ->sortByDesc(function ($class) {
            return [
                (int) ($class->is_primary ?? false),
                (int) $class->level,
                -((int) ($class->sort_order ?? 0)),
            ];
        })
        ->values();

    $primaryClass = $sortedClasses->first();

    $primarySubclass =
        $primaryClass?->subclass
        ?? $subclassName;

    if ($sortedClasses->isNotEmpty()) {
        $formattedClasses = $sortedClasses
            ->map(function ($class) {
                return $class->class . ' ' . $class->level . 'º';
            })
            ->join(', ');
    } else {
        $formattedClasses =
            $primaryClass?->class
            ?? $className
            ?? 'Classe não definida';
    }

    /*
    |--------------------------------------------------------------------------
    | EXPERIÊNCIA
    |--------------------------------------------------------------------------
    */
    $xpEnabled = (bool) (
        $character->experience_enabled
        ?? $character->xp_enabled
        ?? false
    );

    $experience =
        $character->experience
        ?? $character->experience_points
        ?? $character->xp
        ?? null;
@endphp

<div
    class="
        flex
        min-w-0
        flex-col
        items-start
        gap-3
        select-none
        sm:flex-row
        sm:items-start
    "
>

    {{-- ============================================================
         RETRATO / NÍVEL / ESTADO / PROFICIÊNCIA
    ============================================================= --}}
    <div
        class="
            relative
            flex
            w-[152px]
            shrink-0
            flex-col
        "
    >

        {{-- FOTO --}}
        <div
            :class="{
                'animate-damage-photo': shakingRed,
                'animate-damage-red': shakingBlue,
                'animate-heal-green': flashingGreen
            }"
            class="
                relative
                h-[152px]
                w-[152px]
                overflow-hidden
                rounded-xl
                border
                border-[#cdbb9f]/75
                bg-[#efe9dc]
                p-1
                shadow-[0_2px_6px_rgba(83,21,15,.07)]
                transition-all
            "
        >

            <div
                class="
                    relative
                    h-full
                    w-full
                    overflow-hidden
                    rounded-lg
                    bg-[#f4f1e8]
                "
            >

                {{-- MOLDURA INTERNA --}}
                <div
                    class="
                        pointer-events-none
                        absolute
                        inset-[2px]
                        z-10
                        rounded-md
                        border
                        border-[#cdbb9f]/55
                    "
                ></div>

                @if ($imageUrl)

                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $character->name }}"
                        :class="{
                            'grayscale opacity-60 contrast-125': isDowned
                        }"
                        class="
                            h-full
                            w-full
                            object-cover
                            object-top
                            transition-all
                            duration-500
                        "
                    >

                @else

                    <div
                        :class="{
                            'grayscale opacity-60': isDowned
                        }"
                        class="
                            flex
                            h-full
                            w-full
                            items-center
                            justify-center
                            font-serif
                            text-3xl
                            font-black
                            text-[#53150f]
                        "
                    >
                        {{ strtoupper(mb_substr($character->name, 0, 2)) }}
                    </div>

                @endif

            </div>

        </div>


        {{-- ========================================================
             NÍVEL
        ========================================================= --}}
        <div
            class="
                absolute
                -right-[9px]
                -top-[8px]
                z-30
                flex
                h-[58px]
                w-[58px]
                items-center
                justify-center
                rounded-full
                border
                border-[#cdbb9f]
                bg-[#faf8f2]
                shadow-[0_4px_10px_rgba(83,21,15,.11)]
            "
        >

            {{-- BORDA INTERNA --}}
            <div
                class="
                    pointer-events-none
                    absolute
                    inset-[3px]
                    rounded-full
                    border
                    border-[#d8c7ab]/70
                "
            ></div>

            <div
                class="
                    relative
                    z-10
                    flex
                    flex-col
                    items-center
                    justify-center
                "
            >

                <span
                    class="
                        text-[5.5px]
                        font-black
                        uppercase
                        tracking-[0.18em]
                        text-[#8c6239]
                    "
                >
                    Nível
                </span>

                <span
                    class="
                        mt-[2px]
                        font-serif
                        text-[22px]
                        font-black
                        leading-none
                        text-[#6b1d14]
                        drop-shadow-[0_1px_1px_rgba(83,21,15,.14)]
                    "
                >
                    {{ $character->level }}
                </span>

            </div>

        </div>


        {{-- ========================================================
             XP OPCIONAL
        ========================================================= --}}
        @if ($xpEnabled)

            <div
                class="
                    absolute
                    right-[-8px]
                    top-[47px]
                    z-20
                    flex
                    min-w-[54px]
                    items-center
                    justify-center
                    gap-1
                    rounded-md
                    border
                    border-[#d8c7ab]/70
                    bg-[#faf8f2]
                    px-1.5
                    py-1
                    shadow-[0_2px_5px_rgba(83,21,15,.07)]
                "
            >

                <span
                    class="
                        text-[5px]
                        font-black
                        uppercase
                        tracking-[0.16em]
                        text-[#8c6239]
                    "
                >
                    XP
                </span>

                @if ($experience !== null)
                    <span
                        class="
                            font-serif
                            text-[10px]
                            font-black
                            leading-none
                            text-[#53150f]
                        "
                    >
                        {{ $experience }}
                    </span>
                @endif

            </div>

        @endif


        {{-- ========================================================
             ESTADO + PROFICIÊNCIA
        ========================================================= --}}
        <div
            class="
                relative
                z-20
                -mt-[11px]
                flex
                h-[25px]
                w-full
                items-center
                overflow-hidden
                rounded-b-lg
                border
                border-[#cdbb9f]/70
                bg-[#faf8f2]
                shadow-[0_2px_5px_rgba(83,21,15,.07)]
            "
        >

            {{-- ESTADO --}}
            <div
                class="
                    flex
                    min-w-0
                    flex-1
                    items-center
                    px-2
                "
            >

                <span
                    class="
                        mr-1.5
                        h-1.5
                        w-1.5
                        shrink-0
                        rounded-full
                        bg-[#8c6239]
                    "
                    :class="{
                        'bg-red-700':
                            isDead ||
                            isCritical,

                        'bg-amber-600':
                            isBloodied,

                        'bg-emerald-700':
                            isStable ||
                            (
                                !isDowned &&
                                !isCritical &&
                                !isBloodied
                            )
                    }"
                ></span>

                <span
                    x-text="statusLabel"
                    :class="statusClass"
                    class="
                        truncate
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.15em]
                    "
                ></span>

            </div>


            {{-- DIVISOR --}}
            <div
                class="
                    h-4
                    w-px
                    shrink-0
                    bg-[#d8c7ab]/70
                "
            ></div>


            {{-- PROFICIÊNCIA --}}
            <div
                class="
                    flex
                    h-full
                    shrink-0
                    items-center
                    gap-1
                    px-2
                "
            >

                <span
                    class="
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.13em]
                        text-[#8c6239]
                    "
                >
                    Prof.
                </span>

                <span
                    class="
                        font-serif
                        text-[17px]
                        font-black
                        
                        leading-none
                        text-[#6b1d14]
                        drop-shadow-[0_1px_1px_rgba(83,21,15,.12)]
                    "
                >
                    {{ $proficiencyBonus >= 0 ? '+' : '' }}{{ $proficiencyBonus }}
                </span>

            </div>

        </div>

    </div>


    {{-- ============================================================
         FICHA DE IDENTIDADE
    ============================================================= --}}
    <div
        class="
            relative
            flex
            min-w-0
            flex-1
            items-stretch
        "
    >

        <div
            class="
                relative
                flex
                h-[176px]
                min-w-0
                flex-1
                flex-col
                justify-center
                overflow-visible
                border
                border-r-0
                border-[#cdbb9f]/70
                bg-[#faf8f2]
                px-3
                py-2
                shadow-[0_2px_6px_rgba(83,21,15,.05)]
            "
            style="
                border-top-left-radius:12px;
                border-bottom-left-radius:4px;
            "
        >

            {{-- ====================================================
                 MOLDURA INTERNA
            ===================================================== --}}
            <div
                class="
                    pointer-events-none
                    absolute
                    inset-[3px]
                    border
                    border-r-0
                    border-[#d8c7ab]/60
                "
                style="
                    border-top-left-radius:9px;
                    border-bottom-left-radius:2px;
                "
            ></div>


            {{-- ====================================================
                 DETALHE DE JUNÇÃO
            ===================================================== --}}
            <div
                class="
                    absolute
                    -left-[5px]
                    -top-[5px]
                    z-20
                    h-3
                    w-3
                    rounded-full
                    bg-[#d8c7ab]
                    ring-2
                    ring-[#faf8f2]
                "
            ></div>


            {{-- ====================================================
                 CONTEÚDO
            ===================================================== --}}
            <div
                class="
                    relative
                    z-10
                    flex
                    min-h-0
                    w-full
                    flex-1
                    flex-col
                    justify-center
                    gap-1.5
                "
            >

                {{-- =================================================
                     NOME
                ================================================== --}}
                <div class="min-w-0">

                    <div
                        class="
                            flex
                            h-[31px]
                            w-full
                            items-center
                            border
                            border-[#d8c7ab]/55
                            bg-[#f4f1e8]
                            px-2
                            shadow-[inset_0_1px_2px_rgba(83,21,15,.04)]
                        "
                    >

                        <h1
                            class="
                                truncate
                                font-serif
                                text-[19px]
                                font-black
                                leading-none
                                text-[#53150f]
                                sm:text-[21px]
                            "
                            title="{{ $character->name }}"
                        >
                            {{ $character->name }}
                        </h1>

                    </div>

                    <p
                        class="
                            mt-[2px]
                            px-2
                            text-[6px]
                            font-extrabold
                            uppercase
                            tracking-[0.18em]
                            text-[#8c6239]
                        "
                    >
                        Nome do Personagem
                    </p>

                </div>


                {{-- =================================================
                     GRID DE INFORMAÇÕES
                ================================================== --}}
                <div
                    class="
                        grid
                        min-h-0
                        grid-cols-2
                        gap-x-3
                        gap-y-1
                    "
                >

                    {{-- ANTECEDENTE --}}
                    <div class="min-w-0">

                        <div
                            class="
                                flex
                                h-[23px]
                                items-center
                                border
                                border-[#d8c7ab]/45
                                bg-[#f4f1e8]
                                px-2
                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]
                            "
                        >

                            <p
                                class="
                                    truncate
                                    font-serif
                                    text-[11px]
                                    font-bold
                                    leading-none
                                    text-[#53150f]
                                    sm:text-xs
                                "
                                title="{{ $character->background ?? '—' }}"
                            >
                                {{ $character->background ?? '—' }}
                            </p>

                        </div>

                        <p
                            class="
                                mt-[2px]
                                px-2
                                text-[5.5px]
                                font-extrabold
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            Antecedente
                        </p>

                    </div>


                    {{-- CLASSE --}}
                    <div class="min-w-0">

                        <div
                            class="
                                flex
                                h-[23px]
                                items-center
                                border
                                border-[#d8c7ab]/45
                                bg-[#f4f1e8]
                                px-2
                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]
                            "
                        >

                            <p
                                class="
                                    truncate
                                    font-serif
                                    text-[11px]
                                    font-bold
                                    leading-none
                                    text-[#53150f]
                                    sm:text-xs
                                "
                                title="{{ $formattedClasses }}"
                            >
                                {{ $formattedClasses }}
                            </p>

                        </div>

                        <p
                            class="
                                mt-[2px]
                                px-2
                                text-[5.5px]
                                font-extrabold
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            {{ $hasMulticlass || $sortedClasses->count() > 1 ? 'Classes' : 'Classe' }}
                        </p>

                    </div>


                    {{-- ESPÉCIE --}}
                    <div class="min-w-0">

                        <div
                            class="
                                flex
                                h-[23px]
                                items-center
                                border
                                border-[#d8c7ab]/45
                                bg-[#f4f1e8]
                                px-2
                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]
                            "
                        >

                            <p
                                class="
                                    truncate
                                    font-serif
                                    text-[11px]
                                    font-bold
                                    leading-none
                                    text-[#53150f]
                                    sm:text-xs
                                "
                                title="{{ $character->species ?? '—' }}"
                            >
                                {{ $character->species ?? '—' }}
                            </p>

                        </div>

                        <p
                            class="
                                mt-[2px]
                                px-2
                                text-[5.5px]
                                font-extrabold
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            Espécie
                        </p>

                    </div>


                    {{-- SUBCLASSE --}}
                    <div class="min-w-0">

                        <div
                            class="
                                flex
                                h-[23px]
                                items-center
                                border
                                border-[#d8c7ab]/45
                                bg-[#f4f1e8]
                                px-2
                                shadow-[inset_0_1px_2px_rgba(83,21,15,.025)]
                            "
                        >

                            <p
                                class="
                                    truncate
                                    font-serif
                                    text-[11px]
                                    font-bold
                                    leading-none
                                    text-[#53150f]
                                    sm:text-xs
                                "
                                title="{{ $primarySubclass ?? '—' }}"
                            >
                                {{ $primarySubclass ?? '—' }}
                            </p>

                        </div>

                        <p
                            class="
                                mt-[2px]
                                px-2
                                text-[5.5px]
                                font-extrabold
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            Subclasse
                        </p>

                    </div>

                </div>

            </div>


            {{-- ====================================================
                 LINHAS DECORATIVAS SUPERIOR / INFERIOR
                 Servem também para continuar no Hero Header.
            ===================================================== --}}
            <div
                class="
                    pointer-events-none
                    absolute
                    left-2
                    right-0
                    top-[8px]
                    h-px
                    bg-[#d8c7ab]/55
                "
            ></div>

            <div
                class="
                    pointer-events-none
                    absolute
                    bottom-[8px]
                    left-2
                    right-0
                    h-px
                    bg-[#d8c7ab]/55
                "
            ></div>

        </div>

    </div>

</div>