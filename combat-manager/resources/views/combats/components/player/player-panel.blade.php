<div
    class="
        overflow-hidden
        rounded-xl
        border
        border-[#cdbb9f]/40
        bg-white/90
        shadow-sm
        transition-all
    "
>
    {{-- ============================================================
         CABEÇALHO
    ============================================================= --}}

    <div
        class="
            flex
            flex-wrap
            items-center
            justify-between
            gap-3
            border-b
            border-[#cdbb9f]/20
            bg-[#efe9dc]/30
            px-6
            py-4
        "
    >
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h2
                    class="
                        font-serif
                        text-lg
                        font-bold
                        tracking-wide
                        text-[#6b1d14]
                    "
                >
                    Heróis na Mesa
                </h2>

                @if($combat->campaign)
                    <span
                        class="
                            rounded-full
                            border
                            border-[#cdbb9f]
                            bg-[#f4f1e8]
                            px-2
                            py-0.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-wider
                            text-[#8c6239]
                        "
                    >
                        {{ $combat->campaign->name }}
                    </span>
                @else
                    <span
                        class="
                            rounded-full
                            border
                            border-[#d8cebe]
                            bg-white
                            px-2
                            py-0.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-wider
                            text-[#8c6239]/75
                        "
                    >
                        Independente
                    </span>
                @endif
            </div>

            <p
                class="
                    mt-1
                    text-[10px]
                    font-medium
                    text-[#8c6239]/80
                "
            >
                @if($combat->campaign)
                    Fichas compartilhadas e participantes manuais deste encontro.
                @else
                    Participantes manuais deste encontro.
                @endif
            </p>
        </div>

        <button
            type="button"
            @click="
                openPlayerModal = true
            "
            class="
                rounded-lg
                bg-[#8c6239]
                px-4
                py-2
                text-xs
                font-bold
                uppercase
                tracking-wider
                text-[#f4f1e8]
                shadow-sm
                transition
                hover:bg-[#724b24]
            "
        >
            + Participante
        </button>
    </div>


    {{-- ============================================================
         CARDS
    ============================================================= --}}

    <div class="p-6">

        @if($combatPlayers->isEmpty())

            <div
                class="
                    rounded-xl
                    border
                    border-dashed
                    border-[#cdbb9f]/60
                    bg-[#faf8f2]/50
                    px-4
                    py-10
                    text-center
                "
            >
                <div class="mb-2 text-2xl">
                    👥
                </div>

                <h3
                    class="
                        font-serif
                        text-sm
                        font-bold
                        text-[#6b1d14]
                    "
                >
                    Nenhum herói na mesa
                </h3>

                <p
                    class="
                        mt-1
                        text-[11px]
                        text-[#8c6239]
                    "
                >
                    @if($combat->campaign)
                        Adicione uma ficha compartilhada da campanha ou registre um participante manual.
                    @else
                        Registre manualmente os integrantes deste encontro.
                    @endif
                </p>
            </div>

        @else

            <div
                class="
                    grid
                    grid-cols-1
                    gap-4
                    sm:grid-cols-2
                    xl:grid-cols-3
                "
            >
                @foreach($combatPlayers as $player)

                    @include(
                        'combats.components.player.player-card',
                        [
                            'player' =>
                                $player,

                            'participant' =>
                                $player,

                            'iteration' =>
                                $loop->iteration,

                            'position' =>
                                $loop->iteration,
                        ]
                    )

                @endforeach
            </div>

        @endif

    </div>
</div>