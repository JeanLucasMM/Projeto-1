@props(['character'])

@php
    $armorAbilityOptions = [
        'str' => 'FOR',
        'dex' => 'DES',
        'con' => 'CON',
        'int' => 'INT',
        'wis' => 'SAB',
        'cha' => 'CAR',
    ];
@endphp

<div
    x-data="{
        baseDefenseDrawerOpen: false,
        armorDrawerOpen: false,
        shieldDrawerOpen: false,
    }"
    class="relative flex justify-center"
>
    {{-- ============================================================
         ESCUDO DE CA
    ============================================================= --}}
    <div class="relative translate-y-7">
        <button
            type="button"
            @click="openArmor()"
            class="group relative h-24 w-20 focus:outline-none"
            title="Configurar Classe de Armadura"
        >
            <svg
                class="absolute inset-0 h-full w-full"
                viewBox="0 0 80 96"
                preserveAspectRatio="none"
                fill="none"
            >
                <path
                    d="
                        M40 2
                        L73 13
                        V43
                        C73 63 60 79 40 91
                        C20 79 7 63 7 43
                        V13
                        Z
                    "
                    :fill="
                        activeShieldItem
                            ? '#f7f0e6'
                            : '#faf8f2'
                    "
                    :stroke="
                        activeShieldItem
                            ? '#b08c62'
                            : '#cdbb9f'
                    "
                    stroke-width="2"
                />

                <path
                    d="
                        M40 8
                        L66 17
                        V42
                        C66 58 56 72 40 82
                        C24 72 14 58 14 42
                        V17
                        Z
                    "
                    :stroke="activeShieldItem ? '#8c6239' : '#8c6239'"
                    stroke-width="1"
                    opacity=".24"
                />
            </svg>

            <div class="relative z-10 flex h-full flex-col items-center justify-center pt-0.5">
                <span class="-mt-1 text-[7px] font-black uppercase leading-none tracking-[0.16em] text-[#8c6239]">
                    Classe de
                </span>

                <span class="mt-0.5 text-[7px] font-black uppercase leading-none tracking-[0.16em] text-[#8c6239]">
                    Armadura
                </span>

                <span
                    x-text="totalAc"
                    class="relative z-10 -mt-0.5 font-serif text-2xl font-black leading-none text-[#53150f]"
                ></span>

            </div>
        </button>
    </div>

    {{-- ============================================================
         MODAL
    ============================================================= --}}
    <template x-teleport="body">
        <div
            x-show="armorOpen"
            x-cloak
            class="fixed inset-0 z-[110] flex items-center justify-center p-4"
        >
            <div
                x-show="armorOpen"
                x-transition.opacity
                @click="armorOpen = false"
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
            ></div>

            <div
                x-show="armorOpen"
                x-transition:enter="transition ease-out duration-180"
                x-transition:enter-start="opacity-0 translate-y-2 scale-[.985]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-120"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-[.985]"
                @click.stop
                class="
                    relative
                    z-10
                    flex
                    max-h-[88vh]
                    w-full
                    max-w-[720px]
                    flex-col
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#b9966c]/75
                    bg-[#fbf8f1]
                    shadow-[0_30px_90px_rgba(27,18,14,.38)]
                "
            >
                {{-- HEADER --}}
                <header
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-4
                        border-b
                        border-[#a0774d]/30
                        bg-[#eadbc8]
                        px-5
                        py-3.5
                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                    "
                >
                    <div class="min-w-0 flex-1">

                        <div
                            class="
                                flex
                                min-w-0
                                items-baseline
                                gap-2.5
                            "
                        >
                            <h2
                                class="
                                    truncate

                                    font-serif
                                    text-xl
                                    font-black

                                    text-[#53150f]
                                "
                            >
                                Classe de Armadura
                            </h2>

                            <span
                                class="
                                    shrink-0

                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.10em]

                                    text-[#8c6239]/75
                                "

                                x-text="armorDefenseLabel"
                            ></span>
                        </div>


                        <div
                            class="
                                mt-2

                                flex
                                w-fit
                                max-w-full
                                min-w-0
                                flex-wrap
                                items-center
                                gap-x-1.5
                                gap-y-1

                                border-0

                                bg-transparent

                                px-0
                                py-0

                                shadow-none
                            "
                        >

                            <span
                                class="
                                    text-[11px]
                                    font-semibold
                                    leading-relaxed

                                    text-[#6f4f38]
                                "

                                x-text="
                                    activeArmorItem
                                        ? armorFormulaLabel(activeArmorItem)
                                        : baseDefenseFormulaLabel
                                "
                            ></span>


                            <span
                                x-show="
                                    equippedShieldBonus !== 0
                                "
                                x-cloak

                                class="
                                    text-[11px]
                                    font-semibold

                                    text-[#6f4f38]
                                "
                            >
                                ·
                                Escudo
                                <strong
                                    class="
                                        font-black
                                        text-[#53150f]
                                    "

                                    x-text="
                                        equippedShieldBonus > 0
                                            ? '+' + equippedShieldBonus
                                            : equippedShieldBonus
                                    "
                                ></strong>
                            </span>


                            <span
                                x-show="
                                    armorNamedBonusTotal !== 0
                                "
                                x-cloak

                                class="
                                    text-[11px]
                                    font-semibold

                                    text-[#6f4f38]
                                "
                            >
                                ·
                                Extras
                                <strong
                                    class="
                                        font-black
                                        text-[#53150f]
                                    "

                                    x-text="
                                        armorNamedBonusTotal > 0
                                            ? '+' + armorNamedBonusTotal
                                            : armorNamedBonusTotal
                                    "
                                ></strong>
                            </span>

                        </div>

                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <div
                            class="
                                flex
                                min-w-[54px]
                                flex-col
                                items-center
                                justify-center

                                pl-3
                                pr-1
                            "
                        >
                            <span
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    leading-none
                                    tracking-[0.14em]

                                    text-[#7d604d]/75
                                "
                            >
                                CA
                            </span>

                            <span
                                x-text="totalAc"

                                class="
                                    mt-1

                                    font-serif
                                    text-[26px]
                                    font-black
                                    leading-[0.86]

                                    text-[#53150f]
                                "
                            ></span>
                        </div>

                        <button
                            type="button"
                            @click="armorOpen = false"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-transparent text-[#8c6239] transition hover:border-[#a0774d]/30 hover:bg-[#fffdf8]/55 hover:text-[#53150f]"
                            title="Fechar"
                        >
                            ×
                        </button>
                    </div>
                </header>

                {{-- BODY --}}
                <div
                    class="
                        min-h-0
                        flex-1
                        space-y-3
                        overflow-y-auto
                        overflow-x-hidden
                        px-5
                        pt-[12px]
                        pb-[12px]
                        [scrollbar-width:none]
                        [&::-webkit-scrollbar]:hidden
                    "
                >
                    <div
                        x-show="armorEquipmentError"
                        x-cloak
                        class="rounded-lg border border-red-300/70 bg-red-50 px-3 py-2 text-[12px] font-bold text-red-800"
                        x-text="armorEquipmentError"
                    ></div>

                    {{-- DEFESA BASE --}}

                    <section
                        class="
                            overflow-hidden

                            rounded-xl
                            border
                            border-[#d6c09f]/85

                            bg-[linear-gradient(180deg,#fffdfa_0%,#fbf5ec_100%)]

                            shadow-[0_2px_8px_rgba(83,61,42,.055)]
                        "
                    >

                        {{-- CABEÇALHO / RESUMO DO CÁLCULO --}}

                        <button
                            type="button"

                            @click="
                                baseDefenseDrawerOpen =
                                    !baseDefenseDrawerOpen
                            "

                            class="
                                flex
                                w-full
                                items-center
                                justify-between
                                gap-4

                                px-3.5
                                py-3

                                text-left

                                transition

                                hover:bg-[#faf3e9]
                            "

                            :class="
                                baseDefenseDrawerOpen
                                    ? 'bg-[#faf3e9]'
                                    : ''
                            "

                            :aria-expanded="
                                baseDefenseDrawerOpen
                                    ? 'true'
                                    : 'false'
                            "
                        >

                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <span
                                        class="
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Defesa Base
                                    </span>

                                    <span
                                        class="
                                            text-[9px]
                                            font-semibold

                                            text-[#8c6239]/60
                                        "
                                    >
                                        cálculo sem armadura
                                    </span>
                                </div>


                                {{-- EQUAÇÃO VISÍVEL MESMO COM A GAVETA FECHADA --}}

                                <div
                                    class="
                                        mt-2

                                        flex
                                        min-w-0
                                        flex-wrap
                                        items-center
                                        gap-1.5
                                    "
                                >

                                    <span
                                        class="
                                            inline-flex
                                            h-7
                                            items-center

                                            rounded-md
                                            border
                                            border-[#d8c7ab]/75

                                            bg-[#fffaf2]

                                            px-2.5

                                            font-serif
                                            text-[13px]
                                            font-black

                                            text-[#53150f]
                                        "
                                    >
                                        10
                                    </span>


                                    @foreach ($armorAbilityOptions as $ability => $short)

                                        <template
                                            x-if="
                                                armorMode.includes(
                                                    '{{ $ability }}'
                                                )
                                            "
                                        >
                                            <div
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1.5
                                                "
                                            >

                                                <span
                                                    class="
                                                        font-serif
                                                        text-[15px]
                                                        font-bold

                                                        text-[#b08c62]
                                                    "
                                                >
                                                    +
                                                </span>


                                                <span
                                                    class="
                                                        inline-flex
                                                        h-7
                                                        items-center
                                                        gap-1

                                                        rounded-md
                                                        border
                                                        border-[#b9966c]/55

                                                        bg-[#f7ece0]

                                                        px-2.5

                                                        text-[10px]
                                                        font-black

                                                        text-[#6b1d14]
                                                    "
                                                >
                                                    {{ $short }}

                                                    <span
                                                        x-text="
                                                            abilityModifier('{{ $ability }}') >= 0
                                                                ? '+' + abilityModifier('{{ $ability }}')
                                                                : abilityModifier('{{ $ability }}')
                                                        "
                                                    ></span>
                                                </span>

                                            </div>
                                        </template>

                                    @endforeach


                                    <span
                                        class="
                                            mx-0.5

                                            font-serif
                                            text-[15px]
                                            font-bold

                                            text-[#b08c62]
                                        "
                                    >
                                        =
                                    </span>


                                    <strong
                                        class="
                                            inline-flex
                                            h-7
                                            min-w-8
                                            items-center
                                            justify-center

                                            rounded-md

                                            bg-[#6b1d14]

                                            px-2.5

                                            font-serif
                                            text-[14px]
                                            font-black

                                            text-[#fffaf2]

                                            shadow-[0_2px_5px_rgba(83,21,15,.12)]
                                        "

                                        x-text="
                                            baseDefenseBodyAc
                                        "
                                    ></strong>


                                    <template
                                        x-if="
                                            armorNamedBonusTotal !== 0
                                        "
                                    >
                                        <div
                                            class="
                                                inline-flex
                                                items-center
                                                gap-1.5
                                            "
                                        >

                                            <span
                                                class="
                                                    ml-1

                                                    text-[9px]
                                                    font-bold

                                                    text-[#8c6239]/70
                                                "
                                            >
                                                com extras
                                            </span>


                                            <span
                                                class="
                                                    font-serif
                                                    text-[14px]
                                                    font-bold

                                                    text-[#b08c62]
                                                "
                                            >
                                                →
                                            </span>


                                            <strong
                                                class="
                                                    font-serif
                                                    text-[15px]
                                                    font-black

                                                    text-[#6b1d14]
                                                "

                                                x-text="
                                                    baseDefenseAc
                                                "
                                            ></strong>

                                        </div>
                                    </template>

                                </div>

                            </div>


                            {{-- CHEVRON --}}

                            <span
                                class="
                                    flex
                                    h-8
                                    w-8
                                    shrink-0
                                    items-center
                                    justify-center

                                    rounded-lg
                                    border
                                    border-[#c9ae8a]/70

                                    bg-[#fffaf2]

                                    text-[#8c6239]

                                    shadow-[0_1px_3px_rgba(83,61,42,.06)]

                                    transition
                                "

                                :class="
                                    baseDefenseDrawerOpen
                                        ? 'rotate-180 bg-[#f0e2d2] text-[#6b1d14]'
                                        : ''
                                "

                                aria-hidden="true"
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        d="m7 10 5 5 5-5"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>

                        </button>


                        {{-- CONTEÚDO DA GAVETA --}}

                        <div
                            x-show="
                                baseDefenseDrawerOpen
                            "
                            x-cloak

                            x-transition.opacity.duration.140ms

                            class="
                                border-t
                                border-[#d8c7ab]/60

                                bg-[#fffdfa]/80

                                px-3.5
                                pb-3.5
                                pt-3
                            "
                        >

                            <div
                                class="
                                    flex
                                    items-center
                                    justify-between
                                    gap-3
                                "
                            >

                                <div>

                                    <span
                                        class="
                                            block

                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]

                                            text-[#8c6239]/75
                                        "
                                    >
                                        Atributos da Defesa Base
                                    </span>

                                    <p
                                        class="
                                            mt-0.5

                                            text-[10px]
                                            leading-relaxed

                                            text-[#7d604d]
                                        "
                                    >
                                        Selecione os modificadores que entram no cálculo acima.
                                    </p>

                                </div>


                                <div
                                    class="
                                        shrink-0
                                        text-right
                                    "
                                >

                                    <span
                                        class="
                                            block

                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]

                                            text-[#8c6239]/55
                                        "
                                    >
                                        Resultado
                                    </span>

                                    <strong
                                        class="
                                            font-serif
                                            text-[18px]
                                            font-black

                                            text-[#53150f]
                                        "

                                        x-text="
                                            baseDefenseAc
                                        "
                                    ></strong>

                                </div>

                            </div>


                            <div
                                class="
                                    mt-3

                                    grid
                                    grid-cols-3
                                    gap-1.5

                                    sm:grid-cols-6
                                "
                            >

                                @foreach ($armorAbilityOptions as $ability => $short)

                                    <button
                                        type="button"

                                        @click="
                                            armorMode =
                                                armorMode.includes('{{ $ability }}')
                                                    ? armorMode.filter(
                                                        entry =>
                                                            entry !== '{{ $ability }}'
                                                    )
                                                    : [
                                                        ...armorMode,
                                                        '{{ $ability }}'
                                                    ]
                                        "

                                        class="
                                            flex
                                            min-h-9
                                            items-center
                                            justify-center
                                            gap-1

                                            rounded-lg
                                            border

                                            px-2

                                            text-[10px]
                                            font-black

                                            transition
                                        "

                                        :class="
                                            armorMode.includes('{{ $ability }}')
                                                ? 'border-[#9f7154]/65 bg-[#f0e2d2] text-[#6b1d14] shadow-[inset_0_0_0_1px_rgba(255,255,255,.38)]'
                                                : 'border-[#d8c7ab]/85 bg-[#fffaf6] text-[#8c6239] hover:border-[#c9ae8a] hover:bg-[#f3eadf]'
                                        "
                                    >

                                        <span>
                                            {{ $short }}
                                        </span>

                                        <span
                                            x-text="
                                                abilityModifier('{{ $ability }}') >= 0
                                                    ? '+' + abilityModifier('{{ $ability }}')
                                                    : abilityModifier('{{ $ability }}')
                                            "
                                        ></span>

                                    </button>

                                @endforeach

                            </div>

                        </div>

                    </section>


                    {{-- EQUIPAMENTOS --}}

                    <div
                        class="
                            grid
                            grid-cols-1
                            gap-3

                            lg:grid-cols-2
                        "
                    >

                    {{-- ARMADURAS --}}
                    <section
                        class="
                            overflow-hidden
                            rounded-xl
                            border
                            border-[#d6c09f]/85
                            bg-[#fffdfa]
                            shadow-[0_2px_7px_rgba(83,61,42,.05)]
                        "
                    >
                        <button
                            type="button"
                            @click="armorDrawerOpen = !armorDrawerOpen"
                            class="
                                flex
                                w-full
                                items-center
                                justify-between
                                gap-3
                                bg-[linear-gradient(180deg,#fffaf3_0%,#f7eee2_100%)]
                                px-3.5
                                py-3
                                text-left
                                transition
                                hover:bg-[#f2e6d7]
                            "
                            :class="
                                armorDrawerOpen
                                    ? 'border-b border-[#c9ae8a]/70 bg-[#f7eee2]'
                                    : ''
                            "
                            :aria-expanded="armorDrawerOpen ? 'true' : 'false'"
                        >
                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <strong
                                        class="
                                            font-serif
                                            text-[16px]
                                            leading-none

                                            text-[#53150f]
                                        "
                                    >
                                        Armaduras
                                    </strong>

                                    <span
                                        x-show="activeArmorItem"
                                        x-cloak

                                        class="
                                            inline-flex
                                            items-center
                                            gap-1

                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]

                                            text-[#8c6239]/75
                                        "
                                    >
                                        <span
                                            class="
                                                h-1.5
                                                w-1.5
                                                rounded-full
                                                bg-[#9b7655]
                                            "
                                        ></span>

                                        Equipado
                                    </span>
                                </div>


                                <span
                                    x-show="activeArmorItem"
                                    x-cloak

                                    class="
                                        mt-1
                                        block
                                        max-w-[240px]
                                        truncate

                                        text-[11px]
                                        font-semibold
                                        leading-none

                                        text-[#7d604d]
                                    "

                                    x-text="
                                        activeArmorItem
                                            ? activeArmorItem.name
                                            : ''
                                    "
                                ></span>

                            </div>

                            <div
                                class="
                                    flex
                                    shrink-0
                                    items-center
                                    gap-1.5
                                "
                            >
                                <span
                                    class="
                                        min-w-6

                                        text-center
                                        text-[9px]
                                        font-black

                                        text-[#8c6239]/70
                                    "

                                    x-text="armorItems.length"
                                ></span>

                                <span
                                    class="
                                        flex
                                        h-8
                                        w-8
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border
                                        border-[#c9ae8a]/70

                                        bg-[#fffaf2]

                                        text-[#8c6239]

                                        shadow-[0_1px_3px_rgba(83,61,42,.06)]

                                        transition
                                    "

                                    :class="
                                        armorDrawerOpen
                                            ? 'rotate-180 bg-[#f0e2d2] text-[#6b1d14]'
                                            : ''
                                    "

                                    aria-hidden="true"
                                >
                                    <svg
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="m7 10 5 5 5-5"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </button>

                        <div
                            x-show="armorDrawerOpen"
                            x-cloak
                            x-transition.opacity.duration.120ms
                        >

                        <div
                            x-show="armorItems.length === 0"
                            x-cloak
                            class="px-3 py-5 text-center text-[12px] text-[#8c6239]"
                        >
                            Nenhuma armadura criada no inventário.
                        </div>

                        <div
                            x-show="armorItems.length > 0"
                            x-cloak
                            class="divide-y divide-[#d8c7ab]/45"
                        >
                            <template
                                x-for="item in armorItems"
                                :key="'armor-item-' + item.id"
                            >
                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-3
                                        px-3.5
                                        py-3
                                        transition
                                        hover:bg-[#faf4eb]
                                    "
                                    :class="
                                        item.equipped
                                            ? 'bg-[#f8efe3]'
                                            : ''
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            h-11
                                            w-11
                                            shrink-0
                                            items-center
                                            justify-center
                                            overflow-hidden
                                            rounded-lg
                                            border
                                            border-[#d8c7ab]/85
                                            bg-[#f5efe6]
                                        "
                                    >
                                        <template x-if="item.image_url">
                                            <img
                                                :src="item.image_url"
                                                :alt="item.name"
                                                class="h-full w-full object-cover"
                                            >
                                        </template>

                                        <template x-if="!item.image_url">
                                            <span class="font-serif text-[16px] text-[#8c6239]/45">
                                                ◇
                                            </span>
                                        </template>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <strong
                                                class="block min-w-0 flex-1 truncate font-serif text-[13px] text-[#53150f]"
                                                x-text="item.name"
                                            ></strong>
                                        </div>

                                        <p
                                            class="mt-0.5 truncate text-[11px] text-[#8c6239]"
                                            x-text="armorFormulaLabel(item)"
                                        ></p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <strong
                                            class="block font-serif text-[16px] text-[#53150f]"
                                            x-text="armorItemBodyAc(item)"
                                        ></strong>

                                        <button
                                            type="button"
                                            @click="toggleArmorItem(item)"
                                            :disabled="armorEquipmentBusyId !== null"
                                            class="
                                                mt-1
                                                rounded-md
                                                border
                                                min-w-[60px]
                                                px-2.5
                                                py-1.5
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                transition
                                                disabled:cursor-wait
                                                disabled:opacity-40
                                            "
                                            :class="
                                                item.equipped
                                                    ? 'border-[#cdbb9f] bg-[#fffaf2] text-[#8c6239] hover:bg-[#f3eadf]'
                                                    : 'border-[#8c6239] bg-[#8c6239] text-white hover:bg-[#755237]'
                                            "
                                            x-text="
                                                item.equipped
                                                    ? 'Guardar'
                                                    : 'Equipar'
                                            "
                                        ></button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        </div>
                    </section>


                    {{-- ESCUDOS --}}
                    <section
                        class="
                            overflow-hidden
                            rounded-xl
                            border
                            border-[#d6c09f]/85
                            bg-[#fffdfa]
                            shadow-[0_2px_7px_rgba(83,61,42,.05)]
                        "
                    >
                        <button
                            type="button"
                            @click="shieldDrawerOpen = !shieldDrawerOpen"
                            class="
                                flex
                                w-full
                                items-center
                                justify-between
                                gap-3
                                bg-[linear-gradient(180deg,#fffaf3_0%,#f7eee2_100%)]
                                px-3.5
                                py-3
                                text-left
                                transition
                                hover:bg-[#f2e6d7]
                            "
                            :class="
                                shieldDrawerOpen
                                    ? 'border-b border-[#c9ae8a]/70 bg-[#f7eee2]'
                                    : ''
                            "
                            :aria-expanded="shieldDrawerOpen ? 'true' : 'false'"
                        >
                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <strong
                                        class="
                                            font-serif
                                            text-[16px]
                                            leading-none

                                            text-[#53150f]
                                        "
                                    >
                                        Escudos
                                    </strong>

                                    <span
                                        x-show="activeShieldItem"
                                        x-cloak

                                        class="
                                            inline-flex
                                            items-center
                                            gap-1

                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]

                                            text-[#8c6239]/75
                                        "
                                    >
                                        <span
                                            class="
                                                h-1.5
                                                w-1.5
                                                rounded-full
                                                bg-[#9b7655]
                                            "
                                        ></span>

                                        Equipado
                                    </span>
                                </div>


                                <span
                                    x-show="activeShieldItem"
                                    x-cloak

                                    class="
                                        mt-1
                                        block
                                        max-w-[240px]
                                        truncate

                                        text-[11px]
                                        font-semibold
                                        leading-none

                                        text-[#7d604d]
                                    "

                                    x-text="
                                        activeShieldItem
                                            ? activeShieldItem.name
                                            : ''
                                    "
                                ></span>

                            </div>

                            <div
                                class="
                                    flex
                                    shrink-0
                                    items-center
                                    gap-1.5
                                "
                            >
                                <span
                                    class="
                                        min-w-6

                                        text-center
                                        text-[9px]
                                        font-black

                                        text-[#8c6239]/70
                                    "

                                    x-text="shieldItems.length"
                                ></span>

                                <span
                                    class="
                                        flex
                                        h-8
                                        w-8
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border
                                        border-[#c9ae8a]/70

                                        bg-[#fffaf2]

                                        text-[#8c6239]

                                        shadow-[0_1px_3px_rgba(83,61,42,.06)]

                                        transition
                                    "

                                    :class="
                                        shieldDrawerOpen
                                            ? 'rotate-180 bg-[#f0e2d2] text-[#6b1d14]'
                                            : ''
                                    "

                                    aria-hidden="true"
                                >
                                    <svg
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="m7 10 5 5 5-5"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </button>

                        <div
                            x-show="shieldDrawerOpen"
                            x-cloak
                            x-transition.opacity.duration.120ms
                        >

                        <div
                            x-show="shieldItems.length === 0"
                            x-cloak
                            class="px-3 py-5 text-center text-[12px] text-[#8c6239]"
                        >
                            Nenhum escudo criado no inventário.
                        </div>

                        <div
                            x-show="shieldItems.length > 0"
                            x-cloak
                            class="divide-y divide-[#d8c7ab]/45"
                        >
                            <template
                                x-for="item in shieldItems"
                                :key="'shield-item-' + item.id"
                            >
                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-3
                                        px-3.5
                                        py-3
                                        transition
                                        hover:bg-[#faf4eb]
                                    "
                                    :class="
                                        item.equipped
                                            ? 'bg-[#f8efe3]'
                                            : ''
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            h-11
                                            w-11
                                            shrink-0
                                            items-center
                                            justify-center
                                            overflow-hidden
                                            rounded-lg
                                            border
                                            border-[#d8c7ab]/85
                                            bg-[#f5efe6]
                                        "
                                    >
                                        <template x-if="item.image_url">
                                            <img
                                                :src="item.image_url"
                                                :alt="item.name"
                                                class="h-full w-full object-cover"
                                            >
                                        </template>

                                        <template x-if="!item.image_url">
                                            <span class="font-serif text-[16px] text-[#8c6239]/45">
                                                ◇
                                            </span>
                                        </template>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <strong
                                                class="block min-w-0 flex-1 truncate font-serif text-[13px] text-[#53150f]"
                                                x-text="item.name"
                                            ></strong>
                                        </div>

                                        <p
                                            class="mt-0.5 truncate text-[11px] text-[#8c6239]"
                                            x-text="shieldFormulaLabel(item)"
                                        ></p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <strong
                                            class="block font-serif text-[16px] text-[#53150f]"
                                            x-text="'+' + shieldItemBonus(item)"
                                        ></strong>

                                        <button
                                            type="button"
                                            @click="toggleShieldItem(item)"
                                            :disabled="armorEquipmentBusyId !== null"
                                            class="
                                                mt-1
                                                rounded-md
                                                border
                                                min-w-[60px]
                                                px-2.5
                                                py-1.5
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                transition
                                                disabled:cursor-wait
                                                disabled:opacity-40
                                            "
                                            :class="
                                                item.equipped
                                                    ? 'border-[#cdbb9f] bg-[#fffaf2] text-[#8c6239] hover:bg-[#f3eadf]'
                                                    : 'border-[#8c6239] bg-[#8c6239] text-white hover:bg-[#755237]'
                                            "
                                            x-text="
                                                item.equipped
                                                    ? 'Guardar'
                                                    : 'Equipar'
                                            "
                                        ></button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        </div>
                    </section>


                    </div>

                    {{-- BÔNUS EXTRAS --}}
                    <section
                        class="
                            rounded-xl
                            border
                            border-[#d6c09f]/85
                            bg-[linear-gradient(180deg,#fffdfa_0%,#fbf5ec_100%)]
                            p-3.5
                            shadow-[0_2px_8px_rgba(83,61,42,.05)]
                        "
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <strong class="font-serif text-[15px] text-[#53150f]">
                                    Bônus Extras
                                </strong>

                                <p class="mt-0.5 text-[11px] text-[#8c6239]">
                                    Efeitos temporários, habilidades ou ajustes que não pertencem ao item.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="addArmorBonus()"
                                class="rounded-md border border-[#c5a783] bg-[#fffaf2] px-2.5 py-1.5 text-[10px] font-black text-[#6b1d14] transition hover:bg-[#eadbc8]"
                            >
                                + Adicionar
                            </button>
                        </div>

                        <div class="mt-2 space-y-1.5">
                            <template
                                x-for="(bonus, index) in armorBonuses"
                                :key="'armor-extra-' + index"
                            >
                                <div class="flex items-center gap-1.5">
                                    <input
                                        type="text"
                                        x-model="bonus.name"
                                        placeholder="Ex.: Defesa sem Armadura"
                                        class="min-w-0 flex-1 rounded-md border border-[#cdbb9f]/70 bg-[#fffdfa] px-2.5 py-2 text-[11px] text-[#53150f] outline-none transition focus:border-[#9b7655] focus:ring-2 focus:ring-[#b08c62]/10"
                                    >

                                    <input
                                        type="number"
                                        x-model.number="bonus.value"
                                        class="w-14 rounded-md border border-[#cdbb9f]/70 bg-[#fffdfa] px-2 py-2 text-center text-[11px] font-black text-[#53150f] outline-none transition focus:border-[#9b7655] focus:ring-2 focus:ring-[#b08c62]/10"
                                    >

                                    <button
                                        type="button"
                                        @click="removeArmorBonus(index)"
                                        class="flex h-7 w-7 items-center justify-center rounded-md text-red-700 transition hover:bg-red-50"
                                        title="Remover"
                                    >
                                        ×
                                    </button>
                                </div>
                            </template>

                            <div
                                x-show="armorBonuses.length === 0"
                                x-cloak
                                class="rounded-lg border border-dashed border-[#d8c7ab]/85 px-3 py-2 text-center text-[11px] text-[#8c6239]"
                            >
                                Nenhum bônus extra.
                            </div>
                        </div>
                    </section>
                </div>

                {{-- FOOTER --}}
                <footer
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-4
                        border-t
                        border-[#c9ae8a]/65
                        bg-[linear-gradient(180deg,#f3eadf_0%,#eee2d4_100%)]
                        px-5
                        py-3.5
                    "
                >
                    <span
                        class="
                            text-[10px]
                            font-semibold

                            text-[#8c6239]/75
                        "
                    >
                        Equipamentos são aplicados na hora. Base e bônus precisam ser salvos.
                    </span>

                    <button
                        type="button"

                        @click="saveArmor()"

                        :disabled="savingArmor"

                        class="
                            inline-flex
                            min-w-[132px]
                            items-center
                            justify-center

                            rounded-xl
                            border
                            border-[#5b1812]/30

                            bg-[#6b1d14]

                            px-4
                            py-2.5

                            text-[11px]
                            font-black
                            leading-none

                            text-[#fffaf2]

                            shadow-[0_3px_8px_rgba(83,21,15,.15)]

                            transition

                            hover:-translate-y-px
                            hover:bg-[#53150f]
                            hover:shadow-[0_5px_12px_rgba(83,21,15,.20)]

                            disabled:cursor-wait
                            disabled:translate-y-0
                            disabled:opacity-50
                        "
                    >

                        <span x-show="!savingArmor">
                            Salvar ajustes
                        </span>


                        <span
                            x-show="savingArmor"
                            x-cloak
                        >
                            Salvando...
                        </span>

                    </button>
                </footer>
            </div>
        </div>
    </template>
</div>