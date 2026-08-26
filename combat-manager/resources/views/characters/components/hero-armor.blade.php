@props(['character'])

@php

    $abilities = $character->abilities;

    $abilityScores = [

        'str' => (int) ($abilities?->strength ?? 10),

        'dex' => (int) ($abilities?->dexterity ?? 10),

        'con' => (int) ($abilities?->constitution ?? 10),

        'int' => (int) ($abilities?->intelligence ?? 10),

        'wis' => (int) ($abilities?->wisdom ?? 10),

        'cha' => (int) ($abilities?->charisma ?? 10),

    ];

    $abilityModifiers = collect($abilityScores)

        ->mapWithKeys(

            fn ($score, $key) => [

                $key => (int) floor(($score - 10) / 2),

            ]

        )

        ->all();

@endphp

<div class="relative flex justify-center">

    {{-- ============================================================

         CA

    ============================================================= --}}

    <div class="relative translate-y-7">

        <button

            type="button"

            @click="openArmor()"

            class="group relative h-24 w-20 focus:outline-none"

            title="Configurar Classe de Armadura"

        >

            {{-- FUNDO E BORDA DO ESCUDO --}}

            <svg

                class="absolute inset-0 h-full w-full"

                viewBox="0 0 80 96"

                preserveAspectRatio="none"

                fill="none"

            >

                {{-- ESCUDO EXTERNO --}}

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

                    :fill="shieldEnabled ? '#f0f9ff' : '#faf8f2'"

                    :stroke="shieldEnabled ? '#7dd3fc' : '#cdbb9f'"

                    stroke-width="2"

                />

                {{-- BORDA INTERNA --}}

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

                    :stroke="shieldEnabled ? '#38bdf8' : '#8c6239'"

                    stroke-width="1"

                    opacity=".28"

                />

            </svg>



            {{-- CONTEÚDO DO ESCUDO --}}

            <div

                class="

                    relative

                    z-10

                    flex

                    h-full

                    flex-col

                    items-center

                    justify-center

                    pt-0.5

                "

            >

                {{-- TÍTULO --}}

                <span

                    class="

                        -mt-1

                        text-[7px]

                        font-black

                        uppercase

                        leading-none

                        tracking-[0.16em]

                    "

                    :class="shieldEnabled

                        ? 'text-sky-700'

                        : 'text-[#8c6239]'"

                >

                    Classe de

                </span>

                <span

                    class="

                        mt-0.5

                        text-[7px]

                        font-black

                        uppercase

                        leading-none

                        tracking-[0.16em]

                    "

                    :class="shieldEnabled

                        ? 'text-sky-700'

                        : 'text-[#8c6239]'"

                >

                    Armadura

                </span>



                {{-- CA --}}

<span

    x-text="totalAc"

    class="

        relative

        z-10

        -mt-0.5

        font-serif

        text-2xl

        font-black

        leading-none

        text-[#53150f]

    "

></span>

            </div>

        </button>



        {{-- INDICADOR DE ESCUDO EQUIPADO --}}

        <span

            x-show="shieldEnabled"

            x-cloak

            class="

                pointer-events-none

                absolute

                -right-1

                top-1

                z-20

                flex

                h-6

                w-6

                items-center

                justify-center

                rounded-full

                border

                border-sky-300

                bg-sky-50

                text-sky-700

            "

            title="Escudo equipado"

        >

            <svg

                class="h-3.5 w-3.5"

                viewBox="0 0 24 24"

                fill="none"

                stroke="currentColor"

                stroke-width="1.8"

                stroke-linecap="round"

                stroke-linejoin="round"

            >

                <path

                    d="

                        M12 3.5

                        19 6

                        V11.2

                        C19 15.5 16.3 18.9 12 20.5

                        C7.7 18.9 5 15.5 5 11.2

                        V6

                        Z

                    "

                />

            </svg>

        </span>

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

        {{-- BACKDROP --}}

        <div

            x-show="armorOpen"

            x-transition.opacity

            @click="armorOpen = false"

            class="

                absolute

                inset-0

                bg-[#2b1d17]/60

                backdrop-blur-sm

            "

        ></div>



        {{-- MODAL --}}

        <div

            x-show="armorOpen"

            x-transition:enter="transition ease-out duration-200"

            x-transition:enter-start="opacity-0 translate-y-2 scale-95"

            x-transition:enter-end="opacity-100 translate-y-0 scale-100"

            x-transition:leave="transition ease-in duration-150"

            x-transition:leave-start="opacity-100 translate-y-0 scale-100"

            x-transition:leave-end="opacity-0 translate-y-2 scale-95"

            @click.stop

            class="

                relative

                z-10

                w-full

                max-w-sm

                overflow-hidden

                rounded-2xl

                border

                border-[#cdbb9f]

                bg-[#f4f1e8]

                shadow-xl

            "

        >

            {{-- HEADER --}}

            <div

                class="

                    flex

                    items-center

                    justify-between

                    border-b

                    border-[#cdbb9f]/60

                    bg-[#efe9dc]/70

                    px-4

                    py-3

                "

            >

                <div>

                    <p

                        class="

                            text-[8px]

                            font-black

                            uppercase

                            tracking-[0.22em]

                            text-[#8c6239]

                        "

                    >

                        Defesa

                    </p>

                    <h2

                        class="

                            mt-0.5

                            font-serif

                            text-lg

                            font-black

                            text-[#53150f]

                        "

                    >

                        Armadura

                    </h2>

                </div>

                <div class="flex items-center gap-2">

                    {{-- CA --}}

                    <div

                        class="

                            flex

                            h-10

                            min-w-10

                            items-center

                            justify-center

                            rounded-lg

                            border

                            border-[#cdbb9f]

                            bg-[#faf8f2]

                            px-2

                        "

                    >

                        <span

                            x-text="totalAc"

                            class="

                                font-serif

                                text-xl

                                font-black

                                text-[#53150f]

                            "

                        ></span>

                    </div>

                    {{-- FECHAR --}}

                    <button

                        type="button"

                        @click="armorOpen = false"

                        class="

                            flex

                            h-8

                            w-8

                            items-center

                            justify-center

                            rounded-lg

                            text-[#8c6239]

                            transition

                            hover:bg-[#e8dfd1]

                            hover:text-[#53150f]

                        "

                        title="Fechar"

                    >

                        ×

                    </button>

                </div>

            </div>



            <div class="space-y-4 p-4">

                {{-- ==================================================

                     ESCUDO

                =================================================== --}}

                <div

                    class="

                        rounded-xl

                        border

                        border-[#d8c7ab]

                        bg-[#faf8f2]

                        p-3

                    "

                >

                    <div class="flex items-center gap-3">

                        {{-- EMBLEMA DO ESCUDO --}}

                        <div

                            class="

                                flex

                                h-10

                                w-10

                                shrink-0

                                items-center

                                justify-center

                                rounded-lg

                                border

                            "

                            :class="shieldEnabled

                                ? 'border-sky-300 bg-sky-50 text-sky-700'

                                : 'border-[#d8c7ab] bg-[#f5efe6] text-[#8c6239]'"

                        >

                            <svg

                                class="h-5 w-5"

                                viewBox="0 0 24 24"

                                fill="none"

                                stroke="currentColor"

                                stroke-width="1.6"

                                stroke-linecap="round"

                                stroke-linejoin="round"

                            >

                                <path

                                    d="

                                        M12 3.5

                                        19 6

                                        V11.2

                                        C19 15.5 16.3 18.9 12 20.5

                                        C7.7 18.9 5 15.5 5 11.2

                                        V6

                                        Z

                                    "

                                />

                                <path

                                    d="

                                        m9.2 12

                                        1.8 1.8

                                        3.8-4

                                    "

                                />

                            </svg>

                        </div>



                        <div class="min-w-0 flex-1">

                            <div class="flex items-center justify-between gap-3">

                                <div>

                                    <p

                                        class="

                                            text-[8px]

                                            font-black

                                            uppercase

                                            tracking-widest

                                            text-[#53150f]

                                        "

                                    >

                                        Escudo

                                    </p>

                                    <p

                                        class="mt-0.5 text-[9px]"

                                        :class="shieldEnabled

                                            ? 'text-sky-700'

                                            : 'text-[#8c6239]'"

                                        x-text="

                                            shieldEnabled

                                                ? 'Equipado'

                                                : 'Não equipado'

                                        "

                                    ></p>

                                </div>



                                {{-- TOGGLE --}}

                                <label

                                    class="

                                        relative

                                        inline-flex

                                        cursor-pointer

                                        items-center

                                    "

                                >

                                    <input

                                        type="checkbox"

                                        x-model="shieldEnabled"

                                        class="peer sr-only"

                                    >

                                    <span

                                        class="

                                            h-5

                                            w-9

                                            rounded-full

                                            bg-[#d8c7ab]

                                            transition-colors

                                            peer-checked:bg-sky-600

                                        "

                                    ></span>

                                    <span

                                        class="

                                            absolute

                                            left-0.5

                                            top-0.5

                                            h-4

                                            w-4

                                            rounded-full

                                            bg-white

                                            transition-transform

                                            peer-checked:translate-x-4

                                        "

                                    ></span>

                                </label>

                            </div>



                            {{-- BÔNUS --}}

                            <div class="mt-2 flex items-center gap-2">

                                <span

                                    class="

                                        text-[8px]

                                        font-bold

                                        uppercase

                                        tracking-wide

                                        text-[#8c6239]

                                    "

                                >

                                    Bônus

                                </span>

                                <input

                                    type="number"

                                    min="0"

                                    x-model.number="shieldBonus"

                                    :disabled="!shieldEnabled"

                                    class="

                                        h-7

                                        w-12

                                        rounded-md

                                        border

                                        border-[#d8c7ab]

                                        bg-white

                                        text-center

                                        text-xs

                                        font-black

                                        text-[#53150f]

                                        outline-none

                                        focus:border-sky-400

                                        disabled:cursor-not-allowed

                                        disabled:bg-[#efe9dc]

                                        disabled:opacity-40

                                    "

                                >

                                <span

                                    class="

                                        text-[8px]

                                        text-[#8c6239]/70

                                    "

                                >

                                    para a CA

                                </span>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- ==================================================

                     BÔNUS DE ARMADURA

                =================================================== --}}

                <div>

                    <div class="mb-2 flex items-center justify-between">

                        <span

                            class="

                                text-[8px]

                                font-black

                                uppercase

                                tracking-widest

                                text-[#8c6239]

                            "

                        >

                            Bônus de Armadura

                        </span>

                        <button

                            type="button"

                            @click="addArmorBonus()"

                            class="

                                rounded-md

                                border

                                border-[#cdbb9f]/50

                                bg-[#efe9dc]

                                px-2

                                py-1

                                text-[8px]

                                font-black

                                text-[#6b1d14]

                                transition

                                hover:bg-[#6b1d14]

                                hover:text-[#f4f1e8]

                            "

                        >

                            + Adicionar

                        </button>

                    </div>



                    <div class="space-y-1.5">

                        <template

                            x-for="(bonus, index) in armorBonuses"

                            :key="index"

                        >

                            <div class="flex items-center gap-1.5">

                                <input

                                    type="text"

                                    x-model="bonus.name"

                                    placeholder="Nome"

                                    class="

                                        min-w-0

                                        flex-1

                                        rounded-md

                                        border

                                        border-[#cdbb9f]/50

                                        bg-white

                                        px-2

                                        py-1.5

                                        text-[9px]

                                        text-[#53150f]

                                        outline-none

                                        focus:border-[#6b1d14]

                                    "

                                >

                                <input

                                    type="number"

                                    x-model.number="bonus.value"

                                    class="

                                        w-12

                                        rounded-md

                                        border

                                        border-[#cdbb9f]/50

                                        bg-white

                                        px-1

                                        py-1.5

                                        text-center

                                        text-[9px]

                                        font-black

                                        text-[#53150f]

                                        outline-none

                                        focus:border-[#6b1d14]

                                    "

                                >

                                <button

                                    type="button"

                                    @click="removeArmorBonus(index)"

                                    class="

                                        flex

                                        h-7

                                        w-7

                                        items-center

                                        justify-center

                                        rounded-md

                                        text-red-700

                                        transition

                                        hover:bg-red-50

                                    "

                                    title="Remover"

                                >

                                    ×

                                </button>

                            </div>

                        </template>



                        <div

                            x-show="armorBonuses.length === 0"

                            class="

                                rounded-lg

                                border

                                border-dashed

                                border-[#cdbb9f]/50

                                px-3

                                py-2

                                text-center

                                text-[9px]

                                text-[#8c6239]

                            "

                        >

                            Nenhum bônus.

                        </div>

                    </div>

                </div>



                {{-- ==================================================

                     BASE

                =================================================== --}}

                <div

                    class="

                        border-t

                        border-[#cdbb9f]/40

                        pt-3

                    "

                >

                    <div class="mb-2 flex items-center justify-between">

                        <div>

                            <span

                                class="

                                    block

                                    text-[8px]

                                    font-black

                                    uppercase

                                    tracking-widest

                                    text-[#8c6239]

                                "

                            >

                                Base

                            </span>

                            <span

                                class="

                                    text-[9px]

                                    text-[#8c6239]/70

                                "

                            >

                                10 + atributos selecionados

                            </span>

                        </div>

                        <span

                            class="

                                font-serif

                                text-sm

                                font-black

                                text-[#53150f]

                            "

                            x-text="

                                '10' +

                                (

                                    armorBaseBonus > 0

                                        ? '+' + armorBaseBonus

                                        : armorBaseBonus < 0

                                            ? armorBaseBonus

                                            : ''

                                )

                            "

                        ></span>

                    </div>



                    {{-- ATRIBUTOS --}}

                    <div class="grid grid-cols-2 gap-1.5">

                        @foreach ([

                            'str' => ['label' => 'Força', 'short' => 'FOR'],

                            'dex' => ['label' => 'Destreza', 'short' => 'DES'],

                            'con' => ['label' => 'Constituição', 'short' => 'CON'],

                            'int' => ['label' => 'Inteligência', 'short' => 'INT'],

                            'wis' => ['label' => 'Sabedoria', 'short' => 'SAB'],

                            'cha' => ['label' => 'Carisma', 'short' => 'CAR'],

                        ] as $ability => $info)

                            <label

                                class="

                                    flex

                                    cursor-pointer

                                    items-center

                                    justify-between

                                    rounded-lg

                                    border

                                    px-3

                                    py-2

                                    transition

                                "

                                :class="

                                    armorMode.includes('{{ $ability }}')

                                        ? 'border-[#6b1d14] bg-[#efe9dc]'

                                        : 'border-[#cdbb9f]/50 bg-white hover:bg-[#efe9dc]/50'

                                "

                            >

                                <div class="flex items-center gap-2">

                                    <input

                                        type="checkbox"

                                        value="{{ $ability }}"

                                        :checked="armorMode.includes('{{ $ability }}')"

                                        @change="

                                            if ($event.target.checked) {

                                                if (!armorMode.includes('{{ $ability }}')) {

                                                    armorMode.push('{{ $ability }}');

                                                }

                                            } else {

                                                armorMode = armorMode.filter(

                                                    value => value !== '{{ $ability }}'

                                                );

                                            }

                                        "

                                        class="

                                            rounded

                                            border-[#cdbb9f]

                                            text-[#6b1d14]

                                            focus:ring-[#6b1d14]/20

                                        "

                                    >

                                    <span

                                        class="

                                            text-[9px]

                                            font-black

                                            uppercase

                                            tracking-widest

                                            text-[#53150f]

                                        "

                                    >

                                        {{ $info['label'] }}

                                    </span>

                                </div>

                                <span

                                    class="

                                        text-[10px]

                                        font-black

                                        text-[#8c6239]

                                    "

                                >

                                    {{ $abilityModifiers[$ability] >= 0 ? '+' : '' }}{{ $abilityModifiers[$ability] }}

                                </span>

                            </label>

                        @endforeach

                    </div>



                    {{-- NENHUM --}}

                    <button

                        type="button"

                        @click="armorMode = []"

                        :class="

                            armorMode.length === 0

                                ? 'border-[#6b1d14] bg-[#efe9dc] text-[#53150f]'

                                : 'border-[#cdbb9f]/50 bg-white text-[#8c6239]'

                        "

                        class="

                            mt-1.5

                            w-full

                            rounded-lg

                            border

                            px-3

                            py-2

                            text-[8px]

                            font-black

                            uppercase

                            tracking-widest

                            transition

                        "

                    >

                        Nenhum

                    </button>

                </div>



                {{-- ==================================================

                     AÇÕES

                =================================================== --}}

                <div

                    class="

                        flex

                        justify-end

                        gap-2

                        border-t

                        border-[#cdbb9f]/40

                        pt-3

                    "

                >

                    <button

                        type="button"

                        @click="armorOpen = false"

                        class="

                            rounded-lg

                            px-3

                            py-2

                            text-[8px]

                            font-black

                            uppercase

                            tracking-widest

                            text-[#8c6239]

                            transition

                            hover:bg-[#efe9dc]

                        "

                    >

                        Cancelar

                    </button>

                    <button

                        type="button"

                        @click="saveArmor()"

                        :disabled="savingArmor"

                        class="

                            rounded-lg

                            bg-[#6b1d14]

                            px-4

                            py-2

                            text-[8px]

                            font-black

                            uppercase

                            tracking-widest

                            text-[#f4f1e8]

                            transition

                            hover:bg-[#53150f]

                            disabled:cursor-wait

                            disabled:opacity-50

                        "

                    >

                        <span x-show="!savingArmor">

                            Salvar

                        </span>

                        <span

                            x-show="savingArmor"

                            x-cloak

                        >

                            Salvando...

                        </span>

                    </button>

                </div>

            </div>

        </div>

    </div>

    </template>

</div>