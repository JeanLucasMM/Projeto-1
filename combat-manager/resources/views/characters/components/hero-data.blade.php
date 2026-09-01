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

    $sheetSettings =
        is_array(
            $character->sheet_settings
            ?? null
        )
            ? $character->sheet_settings
            : [];

    $exhaustionRuleActive =
        (bool) data_get(
            $sheetSettings,
            'optional_rules.exhaustion',
            false
        );
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
            w-[166px]
            shrink-0
            flex-col
        "
    >

        {{-- FOTO / CONFIGURAÇÕES --}}
        <button
            type="button"

            @click="
                window.dispatchEvent(
                    new CustomEvent(
                        'open-character-customization',
                        {
                            detail: {
                                tab: 'identity'
                            }
                        }
                    )
                )
            "

            :class="{
                'animate-damage-photo': shakingRed,
                'animate-damage-red': shakingBlue,
                'animate-heal-green': flashingGreen
            }"

            class="
                group
                relative

                block
                h-[170px]
                w-[166px]

                overflow-hidden

                rounded-xl
                border
                border-[#cdbb9f]/75

                bg-[#efe9dc]

                p-1

                text-left

                shadow-[0_2px_6px_rgba(83,21,15,.07)]

                transition-all
                duration-150

                hover:border-[#a0774d]/60
                hover:shadow-[0_3px_8px_rgba(83,21,15,.09)]

                focus:outline-none
                focus-visible:ring-2
                focus-visible:ring-[#8c6239]/25
                focus-visible:ring-offset-1
                focus-visible:ring-offset-[#faf8f2]
            "

            title="Abrir configurações do personagem"
            aria-label="Abrir configurações do personagem"
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
                            'grayscale opacity-60 contrast-125': isDead || (isDowned && !isMorquenConsciousAtZero)
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
                            'grayscale opacity-60': isDead || (isDowned && !isMorquenConsciousAtZero)
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

        </button>


        {{-- ========================================================
             EXAUSTÃO
        ========================================================= --}}

        @if ($exhaustionRuleActive)
            <button
                type="button"

                @click.stop="
                    openExhaustionEditor()
                "

                class="
                    group
                    absolute
                    left-[-6px]
                    -top-[7px]
                    z-30

                    flex
                    h-[48px]
                    w-[48px]
                    items-center
                    justify-center

                    rounded-full
                    border
                    border-[#cdbb9f]/90

                    bg-[radial-gradient(circle_at_35%_25%,#fffdf8_0%,#f7f0e6_58%,#efe4d5_100%)]

                    shadow-[0_4px_10px_rgba(83,21,15,.09),inset_0_1px_0_rgba(255,255,255,.80)]

                    transition
                    duration-150

                    hover:-translate-y-[1px]
                    hover:border-[#a0774d]/60
                    hover:shadow-[0_5px_12px_rgba(83,21,15,.12),inset_0_1px_0_rgba(255,255,255,.84)]

                    focus:outline-none
                    focus-visible:ring-2
                    focus-visible:ring-[#8c6239]/25
                "

                :class="{
                    'border-[#9f5c52]/55 bg-[radial-gradient(circle_at_35%_25%,#fffdf8_0%,#f5ece7_58%,#eadbd4_100%)]':
                        exhaustionLevel >= 6
                }"

                :title="
                    'Exaustão: '
                    + exhaustionLevel
                "

                aria-label="Editar exaustão"
            >
                {{-- ARO INTERNO --}}
                <span
                    class="
                        pointer-events-none
                        absolute
                        inset-[3px]

                        rounded-full
                        border
                        border-[#d8c7ab]/72

                        shadow-[inset_0_0_0_1px_rgba(255,255,255,.42)]
                    "
                ></span>

                {{-- DISCO CENTRAL --}}
                <span
                    class="
                        pointer-events-none
                        absolute
                        inset-[7px]

                        rounded-full

                        bg-[#fffdf8]/48
                    "
                ></span>

                <span
                    class="
                        relative
                        z-10

                        flex
                        flex-col
                        items-center
                        justify-center
                    "
                >
                    <img
                        src="{{ asset('images/exaustao.png') }}"
                        alt=""

                        class="
                            h-[11px]
                            w-[11px]
                            object-contain
                            opacity-65
                        "
                    >

                    <span
                        class="
                            mt-[1px]

                            font-serif
                            text-[17px]
                            font-black
                            leading-none

                            text-[#6b1d14]

                            drop-shadow-[0_1px_0_rgba(255,255,255,.65)]
                        "

                        :class="{
                            'text-[#8b1e16]':
                                exhaustionLevel >= 6
                        }"

                        x-text="
                            exhaustionLevel
                        "
                    ></span>
                </span>

                {{-- PEQUENO MARCADOR INFERIOR --}}
                <span
                    class="
                        pointer-events-none
                        absolute
                        bottom-[3px]
                        h-[2px]
                        w-[10px]
                        rounded-full
                        bg-[#b29161]/38
                    "

                    :class="{
                        'bg-[#8b1e16]/45':
                            exhaustionLevel >= 6
                    }"
                ></span>
            </button>
        @endif


        {{-- ========================================================
             NÍVEL
        ========================================================= --}}

        <div
            class="
                absolute
                -right-[5px]
                -top-[7px]
                z-30

                flex
                h-[48px]
                w-[48px]
                items-center
                justify-center

                rounded-full
                border
                border-[#cdbb9f]/90

                bg-[radial-gradient(circle_at_35%_25%,#fffdf8_0%,#f7f0e6_58%,#efe4d5_100%)]

                shadow-[0_4px_10px_rgba(83,21,15,.09),inset_0_1px_0_rgba(255,255,255,.80)]
            "
        >
            {{-- ARO INTERNO --}}
            <span
                class="
                    pointer-events-none
                    absolute
                    inset-[3px]

                    rounded-full
                    border
                    border-[#d8c7ab]/72

                    shadow-[inset_0_0_0_1px_rgba(255,255,255,.42)]
                "
            ></span>

            {{-- DISCO CENTRAL --}}
            <span
                class="
                    pointer-events-none
                    absolute
                    inset-[7px]

                    rounded-full

                    bg-[#fffdf8]/48
                "
            ></span>

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
                        leading-none
                        tracking-[0.16em]

                        text-[#7d604d]
                    "
                >
                    Nível
                </span>

                <span
                    class="
                        mt-[2px]

                        font-serif
                        text-[18px]
                        font-black
                        leading-none

                        text-[#6b1d14]

                        drop-shadow-[0_1px_0_rgba(255,255,255,.65)]
                    "
                >
                    {{ $character->level }}
                </span>
            </div>

            {{-- PEQUENO MARCADOR INFERIOR --}}
            <span
                class="
                    pointer-events-none
                    absolute
                    bottom-[3px]
                    h-[2px]
                    w-[10px]
                    rounded-full
                    bg-[#b29161]/38
                "
            ></span>
        </div>


        {{-- ========================================================
             XP OPCIONAL
        ========================================================= --}}
        @if ($xpEnabled)

            <div
                class="
                    absolute
                    right-[-8px]
                    top-[39px]
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
                -mt-[18px]
                flex
                h-[24px]
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
                            isMorquenConsciousAtZero ||
                            isBloodied,

                        'bg-emerald-700':
                            (
                                isStable &&
                                !isMorquenConsciousAtZero
                            ) ||
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

@if ($exhaustionRuleActive)
{{-- ================================================================
     EXAUSTÃO — EDITOR
================================================================= --}}

<template x-teleport="body">
    <div
        x-show="exhaustionOpen"
        x-cloak

        @keydown.escape.window="
            cancelExhaustionEditor()
        "

        class="
            fixed
            inset-0
            z-[320]

            flex
            items-center
            justify-center

            p-4
        "

        role="dialog"
        aria-modal="true"
        aria-label="Editar exaustão"
    >
        <div
            class="
                absolute
                inset-0

                bg-black/70
                backdrop-blur-[2px]
            "

            style="
                background-color:
                    rgba(12, 8, 7, 0.72);
            "

            @click="
                cancelExhaustionEditor()
            "
        ></div>

        <div
            @click.stop

            class="
                relative
                z-10

                w-full
                max-w-[540px]

                overflow-hidden

                rounded-2xl
                border
                border-[#b98e63]/70

                bg-[#faf8f2]

                shadow-[0_26px_80px_rgba(42,23,18,.34)]
            "
        >
            {{-- HEADER --}}
            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-4

                    border-b
                    border-[#a0774d]/30

                    bg-[#eadbc8]

                    px-5
                    py-4

                    shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                "
            >
                <div class="min-w-0">
                    <p
                        class="
                            text-[9px]
                            font-black
                            uppercase
                            tracking-[0.18em]
                            text-[#8c6239]
                        "
                    >
                        Condição acumulativa
                    </p>

                    <h3
                        class="
                            mt-0.5
                            font-serif
                            text-[22px]
                            font-black
                            leading-none
                            text-[#53150f]
                        "
                    >
                        Exaustão
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[11px]
                            leading-relaxed
                            text-[#76553f]
                        "
                    >
                        Cada nível piora as rolagens e reduz o deslocamento.
                    </p>
                </div>

                <button
                    type="button"

                    @click="
                        cancelExhaustionEditor()
                    "

                    :disabled="
                        savingExhaustion
                    "

                    class="
                        flex
                        h-9
                        w-9
                        shrink-0
                        items-center
                        justify-center

                        rounded-lg

                        text-[22px]
                        leading-none
                        text-[#8c6239]

                        transition

                        hover:bg-[#fffdf8]/55
                        hover:text-[#53150f]

                        disabled:opacity-50
                    "

                    aria-label="Fechar"
                >
                    ×
                </button>
            </div>

            {{-- BODY --}}
            <div class="space-y-4 px-5 py-5">

                <div>
                    <p
                        class="
                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.12em]
                            text-[#8c6239]
                        "
                    >
                        Nível de exaustão
                    </p>

                    <div
                        class="
                            mt-2
                            grid
                            grid-cols-7
                            gap-2
                        "
                    >
                        <template
                            x-for="
                                level in [0, 1, 2, 3, 4, 5, 6]
                            "
                            :key="
                                'exhaustion-level-' + level
                            "
                        >
                            <button
                                type="button"

                                @click="
                                    setExhaustionDraft(level)
                                "

                                class="
                                    flex
                                    h-11
                                    items-center
                                    justify-center

                                    rounded-lg
                                    border

                                    font-serif
                                    text-[16px]
                                    font-black

                                    transition
                                "

                                :class="
                                    exhaustionDraft === level
                                        ? (
                                            level >= 6
                                                ? 'border-[#8b1e16] bg-[#8b1e16] text-white'
                                                : 'border-[#6b1d14] bg-[#6b1d14] text-[#fffaf2]'
                                        )
                                        : 'border-[#cdbb9f] bg-[#fffdf8] text-[#53150f] hover:bg-[#f4e8d8]'
                                "

                                x-text="
                                    level
                                "
                            ></button>
                        </template>
                    </div>
                </div>


                <div
                    class="
                        grid
                        grid-cols-3
                        gap-2
                    "
                >
                    <div
                        class="
                            rounded-xl
                            border
                            border-[#d8c7ab]/70
                            bg-[#fffdf8]
                            px-3
                            py-3
                            text-center
                        "
                    >
                        <p
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.10em]
                                text-[#8c6239]
                            "
                        >
                            Rolagens
                        </p>

                        <p
                            class="
                                mt-1
                                font-serif
                                text-[20px]
                                font-black
                                text-[#6b1d14]
                            "

                            x-text="
                                exhaustionDraft > 0
                                    ? '-' + (exhaustionDraft * 2)
                                    : '0'
                            "
                        ></p>
                    </div>

                    <div
                        class="
                            rounded-xl
                            border
                            border-[#d8c7ab]/70
                            bg-[#fffdf8]
                            px-3
                            py-3
                            text-center
                        "
                    >
                        <p
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.10em]
                                text-[#8c6239]
                            "
                        >
                            Deslocamento
                        </p>

                        <p
                            class="
                                mt-1
                                font-serif
                                text-[20px]
                                font-black
                                text-[#6b1d14]
                            "
                        >
                            <span
                                x-text="
                                    exhaustionDraft > 0
                                        ? '-' + (exhaustionDraft * 5)
                                        : '0'
                                "
                            ></span>

                            <span
                                class="
                                    text-[10px]
                                    text-[#8c6239]
                                "
                            >
                                ft
                            </span>
                        </p>
                    </div>

                    <div
                        class="
                            rounded-xl
                            border
                            border-[#d8c7ab]/70
                            bg-[#fffdf8]
                            px-3
                            py-3
                            text-center
                        "

                        :class="{
                            'border-red-300 bg-red-50':
                                exhaustionDraft >= 6
                        }"
                    >
                        <p
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.10em]
                                text-[#8c6239]
                            "
                        >
                            Estado
                        </p>

                        <p
                            class="
                                mt-1
                                font-serif
                                text-[15px]
                                font-black
                                text-[#53150f]
                            "

                            :class="{
                                'text-red-800':
                                    exhaustionDraft >= 6
                            }"

                            x-text="
                                exhaustionDraft >= 6
                                    ? 'Morte'
                                    : (
                                        exhaustionDraft > 0
                                            ? 'Exausto'
                                            : 'Normal'
                                    )
                            "
                        ></p>
                    </div>
                </div>


                <div
                    class="
                        rounded-xl
                        border
                        border-[#d8c7ab]/60
                        bg-[#f4f1e8]/70
                        px-4
                        py-3
                    "
                >
                    <p
                        class="
                            text-[11px]
                            leading-relaxed
                            text-[#76553f]
                        "
                    >
                        Os efeitos são cumulativos:
                        cada nível aplica
                        <strong class="text-[#53150f]">−2 em todas as rolagens</strong>
                        e
                        <strong class="text-[#53150f]">−5 ft de deslocamento</strong>.
                        Ao atingir
                        <strong class="text-red-800">6 níveis</strong>,
                        o personagem morre.
                    </p>
                </div>


                <div
                    x-show="
                        exhaustionError
                    "

                    x-cloak

                    class="
                        rounded-lg
                        border
                        border-red-200
                        bg-red-50
                        px-3
                        py-2.5
                        text-[11px]
                        font-bold
                        text-red-700
                    "

                    x-text="
                        exhaustionError
                    "
                ></div>

            </div>

            {{-- FOOTER --}}
            <div
                class="
                    flex
                    items-center
                    justify-end
                    gap-2

                    border-t
                    border-[#d8c7ab]/60

                    bg-[#f4f1e8]/70

                    px-5
                    py-3
                "
            >
                <button
                    type="button"

                    @click="
                        cancelExhaustionEditor()
                    "

                    :disabled="
                        savingExhaustion
                    "

                    class="
                        rounded-lg
                        px-4
                        py-2.5

                        text-[10px]
                        font-black
                        uppercase
                        tracking-wider
                        text-[#8c6239]

                        transition

                        hover:bg-[#eadbc8]/55
                        hover:text-[#53150f]

                        disabled:opacity-50
                    "
                >
                    Cancelar
                </button>

                <button
                    type="button"

                    @click="
                        saveExhaustion()
                    "

                    :disabled="
                        savingExhaustion
                    "

                    class="
                        min-w-[132px]

                        rounded-lg

                        bg-[#6b1d14]

                        px-5
                        py-2.5

                        text-[10px]
                        font-black
                        uppercase
                        tracking-wider
                        text-[#fffaf2]

                        transition

                        hover:bg-[#53150f]

                        disabled:cursor-wait
                        disabled:opacity-60
                    "
                >
                    <span
                        x-show="
                            !savingExhaustion
                        "
                    >
                        Salvar
                    </span>

                    <span
                        x-show="
                            savingExhaustion
                        "

                        x-cloak
                    >
                        Salvando...
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>
@endif


{{-- ================================================================
     CONFIGURAÇÕES DO PERSONAGEM
================================================================= --}}

@include(
    'characters.components.character-customization-modal',
    [
        'character' =>
            $character,

        'classes' =>
            $classes,
    ]
)
