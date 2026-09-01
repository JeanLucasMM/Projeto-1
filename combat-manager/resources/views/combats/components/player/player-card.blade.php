@php
    $character =
        $player->character;

    $characterCombat =
        $character?->combat;

    $isLinked =
        $character !== null;

    $displayName =
        $character?->name
        ?? $player->name
        ?? 'Personagem';

    $effectiveMaxHp =
        $characterCombat
            ? max(
                1,
                (int) $characterCombat->max_hp
                +
                (int) $characterCombat->temporary_max_hp
            )
            : null;

    $currentHp =
        $characterCombat
            ? max(
                0,
                (int) $characterCombat->current_hp
            )
            : null;

    $temporaryHp =
        $characterCombat
            ? max(
                0,
                (int) $characterCombat->temporary_hp
            )
            : 0;

    $hpPercent =
        (
            $effectiveMaxHp !== null
            &&
            $effectiveMaxHp > 0
        )
            ? max(
                0,
                min(
                    100,
                    (
                        $currentHp
                        /
                        $effectiveMaxHp
                    )
                    * 100
                )
            )
            : 0;

    $hpColor =
        $hpPercent > 50
            ? 'bg-emerald-600'
            : (
                $hpPercent > 25
                    ? 'bg-amber-500'
                    : 'bg-red-600'
            );
@endphp

<article
    class="
        flex
        flex-col
        justify-between
        overflow-hidden
        rounded-xl
        border
        border-[#cdbb9f]/45
        bg-white/90
        shadow-sm
        transition-all
        hover:shadow-md
    "
>
    {{-- ============================================================
         IDENTIDADE
    ============================================================= --}}

    <div class="p-4">
        <div
            class="
                flex
                items-start
                justify-between
                gap-3
            "
        >
            <div
                class="
                    flex
                    min-w-0
                    items-center
                    gap-3
                "
            >
                @if($isLinked)
                    <div
                        class="
                            flex
                            h-11
                            w-11
                            shrink-0
                            items-center
                            justify-center
                            overflow-hidden
                            rounded-xl
                            border
                            border-[#cdbb9f]/65
                            bg-[#efe9dc]
                            shadow-inner
                        "
                    >
                        @if($character->image_path)
                            <img
                                src="{{ asset('storage/' . $character->image_path) }}"
                                alt="{{ $displayName }}"
                                class="
                                    h-full
                                    w-full
                                    object-cover
                                    object-top
                                "
                            >
                        @else
                            <span
                                class="
                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#6b1d14]
                                "
                            >
                                {{ strtoupper(substr($displayName, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                @endif

                <div class="min-w-0">
                    <h3
                        class="
                            truncate
                            font-serif
                            text-base
                            font-bold
                            leading-tight
                            tracking-wide
                            text-[#6b1d14]
                        "
                        title="{{ $displayName }}"
                    >
                        {{ $displayName }}
                    </h3>

                    @if($isLinked)
                        <p
                            class="
                                mt-0.5
                                truncate
                                text-[9px]
                                font-bold
                                uppercase
                                tracking-wider
                                text-[#8c6239]/85
                            "
                        >
                            {{ $character->class_label ?: 'Personagem' }}
                            @if($character->level)
                                · Nv. {{ $character->level }}
                            @endif
                        </p>

                        @if($character->user)
                            <p
                                class="
                                    mt-0.5
                                    truncate
                                    text-[9px]
                                    text-[#8c6239]/65
                                "
                            >
                                {{ $character->user->name }}
                            </p>
                        @endif
                    @else
                        <p
                            class="
                                mt-0.5
                                text-[9px]
                                font-bold
                                uppercase
                                tracking-wider
                                text-[#8c6239]/80
                            "
                        >
                            Participante manual
                        </p>
                    @endif
                </div>
            </div>

            <span
                class="
                    shrink-0
                    rounded
                    px-2
                    py-0.5
                    text-[8px]
                    font-black
                    uppercase
                    tracking-widest
                "
                @class([
                    'border border-[#bda780] bg-[#f8efd9] text-[#7f5c18]' =>
                        $isLinked,

                    'bg-stone-200 text-stone-700' =>
                        !$isLinked,
                ])
            >
                {{ $isLinked ? 'Ficha' : 'Manual' }}
            </span>
        </div>


        {{-- ========================================================
             VIDA REAL DA CHARACTER
        ========================================================= --}}

        @if($isLinked && $characterCombat)
            <div
                class="
                    mt-4
                    rounded-lg
                    border
                    border-[#cdbb9f]/35
                    bg-[#faf8f2]/75
                    p-3
                "
            >
                <div
                    class="
                        mb-1.5
                        flex
                        items-center
                        justify-between
                        gap-2
                    "
                >
                    <span
                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-widest
                            text-[#8c6239]
                        "
                    >
                        Pontos de Vida
                    </span>

                    <span
                        class="
                            font-mono
                            text-[10px]
                            font-black
                            text-[#53150f]
                        "
                    >
                        {{ $currentHp }}/{{ $effectiveMaxHp }}
                    </span>
                </div>

                <div
                    class="
                        h-2
                        overflow-hidden
                        rounded-full
                        bg-[#e6ded2]
                        shadow-inner
                    "
                >
                    <div
                        class="
                            {{ $hpColor }}
                            h-full
                            transition-all
                            duration-500
                        "
                        style="
                            width:
                                {{ $hpPercent }}%;
                        "
                    ></div>
                </div>

                @if($temporaryHp > 0)
                    <p
                        class="
                            mt-1.5
                            text-right
                            text-[8px]
                            font-bold
                            uppercase
                            tracking-wide
                            text-[#8c6239]
                        "
                    >
                        +{{ $temporaryHp }} PV temporários
                    </p>
                @endif
            </div>
        @elseif($isLinked)
            <div
                class="
                    mt-4
                    rounded-lg
                    border
                    border-dashed
                    border-[#cdbb9f]/55
                    bg-[#faf8f2]/60
                    px-3
                    py-2.5
                    text-[9px]
                    font-medium
                    text-[#8c6239]
                "
            >
                A ficha ainda não possui dados de combate.
            </div>
        @endif


        {{-- ========================================================
             INICIATIVA
        ========================================================= --}}

        <div
            class="
                mt-4
                border-t
                border-[#cdbb9f]/20
                pt-3
            "
        >
            <div
                class="
                    mb-1.5
                    text-[9px]
                    font-bold
                    uppercase
                    tracking-widest
                    text-[#8c6239]
                "
            >
                Iniciativa
            </div>

            <form
                method="POST"
                action="{{ route('combats.players.initiative', [$combat, $player]) }}"
                class="
                    flex
                    items-center
                    gap-2
                "
            >
                @csrf
                @method('PATCH')

                <input
                    type="number"
                    name="initiative"
                    value="{{ $player->initiative }}"
                    class="
                        h-8
                        w-16
                        rounded
                        border
                        border-[#cdbb9f]/60
                        bg-[#fcfbf9]
                        text-center
                        font-serif
                        text-sm
                        font-bold
                        text-[#6b1d14]
                        outline-none
                        transition-all
                        focus:border-[#6b1d14]
                        focus:ring-1
                        focus:ring-[#6b1d14]
                    "
                >

                <button
                    type="submit"
                    class="
                        h-8
                        rounded
                        bg-[#8c6239]
                        px-3
                        text-xs
                        font-bold
                        text-[#f4f1e8]
                        shadow-sm
                        transition
                        hover:bg-[#724b24]
                    "
                    title="Salvar iniciativa"
                >
                    ✓
                </button>

                @if($isLinked)
                    <a
                        href="{{ route('characters.show', $character) }}"
                        class="
                            ml-auto
                            text-[8px]
                            font-black
                            uppercase
                            tracking-wider
                            text-[#8c6239]
                            transition
                            hover:text-[#53150f]
                            hover:underline
                        "
                    >
                        Abrir ficha
                    </a>
                @endif
            </form>
        </div>
    </div>


    {{-- ============================================================
         REMOVER APENAS DO COMBATE
    ============================================================= --}}

    <div
        class="
            border-t
            border-[#cdbb9f]/20
            bg-[#faf8f2]/55
            px-4
            py-3
        "
    >
        <form
            method="POST"
            action="{{ route('combats.players.destroy', [$combat, $player]) }}"
            onsubmit="
                return confirm(
                    'Remover este participante do combate?'
                )
            "
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="
                    w-full
                    rounded
                    border
                    border-red-200/80
                    py-1.5
                    text-[8px]
                    font-bold
                    uppercase
                    tracking-widest
                    text-red-700
                    transition
                    hover:bg-red-50
                    hover:text-red-800
                "
            >
                Remover da Mesa
            </button>
        </form>
    </div>
</article>