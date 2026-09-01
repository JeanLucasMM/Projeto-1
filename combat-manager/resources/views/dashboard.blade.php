<x-app-layout>

    <div
        class="
            min-h-[calc(100vh-4rem)]
            bg-[#f4f1e8]

            flex
            items-center
            justify-center

            px-6
            py-10
        "
    >
        <div class="w-full max-w-4xl">

            <div class="text-center mb-8">

                <p
                    class="
                        text-[10px]
                        uppercase
                        tracking-[0.35em]
                        font-bold
                        text-[#8c6239]
                        mb-3
                    "
                >
                    Grimório de Campanha
                </p>

                <h1
                    class="
                        text-4xl
                        md:text-5xl
                        font-serif
                        font-black
                        text-[#53150f]
                    "
                >
                    Bem-vindo ao SpellBound
                </h1>

                <p
                    class="
                        mt-4
                        text-sm
                        text-[#8c6239]
                    "
                >
                    Escolha como deseja utilizar o sistema.
                </p>

                @if($mode)
                    <div
                        class="
                            mt-4
                            inline-flex
                            items-center
                            gap-2

                            rounded-full
                            border
                            border-[#cdbb9f]/70
                            bg-[#fffdf8]

                            px-3
                            py-1.5

                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.12em]
                            text-[#6b1d14]
                        "
                    >
                        Modo atual:
                        {{ $mode === 'master' ? 'Mestre' : 'Player' }}


                    </div>
                @endif

            </div>


            @if(session('mode_required'))
                <div
                    class="
                        mb-6
                        rounded-xl
                        border
                        border-[#b08c62]/45

                        bg-[#fffdf8]

                        px-4
                        py-3

                        text-center
                        text-xs
                        font-semibold
                        text-[#6b1d14]

                        shadow-sm
                    "
                >
                    {{ session('mode_required') }}
                </div>
            @endif


            <div
                class="
                    grid
                    grid-cols-1
                    md:grid-cols-2
                    gap-6
                "
            >

                {{-- MESTRE --}}

                <form
                    method="POST"
                    action="{{ route('dashboard.mode') }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="mode"
                        value="master"
                    >

                    <button
                        type="submit"

                        class="
                            group
                            w-full
                            h-full
                            text-left

                            bg-[#efe9dc]

                            border
                            border-[#6b1d14]/20

                            rounded-2xl
                            p-8

                            shadow-[0_8px_30px_rgba(43,29,23,0.06)]

                            hover:border-[#6b1d14]/50
                            hover:shadow-[0_12px_35px_rgba(43,29,23,0.10)]

                            transition-all
                            duration-200
                        "
                    >
                        <div
                            class="
                                w-14
                                h-14

                                rounded-xl

                                bg-[#6b1d14]
                                text-[#f4f1e8]

                                flex
                                items-center
                                justify-center

                                mb-6

                                group-hover:scale-105
                                transition-transform
                            "
                        >
                            <svg
                                class="w-7 h-7"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12l2 2 4-4"
                                />
                            </svg>
                        </div>

                        <h2
                            class="
                                text-xl
                                font-serif
                                font-black
                                text-[#53150f]
                            "
                        >
                            Mestre
                        </h2>

                        <p
                            class="
                                mt-2
                                text-sm
                                leading-6
                                text-[#8c6239]
                            "
                        >
                            Gerencie campanhas, NPCs, combates, criaturas e as ferramentas da mesa.
                        </p>

                        <div
                            class="
                                mt-6
                                text-[10px]
                                uppercase
                                tracking-widest
                                font-bold
                                text-[#6b1d14]
                            "
                        >
                            Entrar como Mestre →
                        </div>
                    </button>
                </form>


                {{-- PLAYER --}}

                <form
                    method="POST"
                    action="{{ route('dashboard.mode') }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="mode"
                        value="player"
                    >

                    <button
                        type="submit"

                        class="
                            group
                            w-full
                            h-full
                            text-left

                            bg-[#efe9dc]

                            border
                            border-[#6b1d14]/20

                            rounded-2xl
                            p-8

                            shadow-[0_8px_30px_rgba(43,29,23,0.06)]

                            hover:border-[#6b1d14]/50
                            hover:shadow-[0_12px_35px_rgba(43,29,23,0.10)]

                            transition-all
                            duration-200
                        "
                    >
                        <div
                            class="
                                w-14
                                h-14

                                rounded-xl

                                bg-[#8c6239]
                                text-[#f4f1e8]

                                flex
                                items-center
                                justify-center

                                mb-6

                                group-hover:scale-105
                                transition-transform
                            "
                        >
                            <svg
                                class="w-7 h-7"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 2l2.2 6.2L20 11l-5.8 2.8L12 20l-2.2-6.2L4 11l5.8-2.8L12 2z"
                                />
                            </svg>
                        </div>

                        <h2
                            class="
                                text-xl
                                font-serif
                                font-black
                                text-[#53150f]
                            "
                        >
                            Player
                        </h2>

                        <p
                            class="
                                mt-2
                                text-sm
                                leading-6
                                text-[#8c6239]
                            "
                        >
                            Acesse campanhas, fichas, personagens, recursos e informações liberadas pelo Mestre.
                        </p>

                        <div
                            class="
                                mt-6
                                text-[10px]
                                uppercase
                                tracking-widest
                                font-bold
                                text-[#6b1d14]
                            "
                        >
                            Entrar como Player →
                        </div>
                    </button>
                </form>

            </div>

        </div>
    </div>

</x-app-layout>