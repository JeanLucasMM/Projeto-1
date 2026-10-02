<x-app-layout>

    @php
        $currentUserId =
            (int) auth()->id();

        $campaignCombats =
            $campaignCombats
            ?? collect();

        $pendingInvitations =
            $campaign->invitations
                ->where(
                    'status',
                    \App\Models\CampaignInvitation::STATUS_PENDING
                )
                ->values();

        $activeCharacters =
            $campaign->characters
                ->filter(
                    fn ($character) =>
                        (bool) $character->pivot->is_active
                )
                ->values();

        $restingCharacters =
            $campaign->characters
                ->reject(
                    fn ($character) =>
                        (bool) $character->pivot->is_active
                )
                ->values();

        $myCharacters =
            $campaign->characters
                ->filter(
                    fn ($character) =>
                        (int) $character->user_id
                        ===
                        $currentUserId
                )
                ->values();

        $myActiveCharacters =
            $myCharacters
                ->filter(
                    fn ($character) =>
                        (bool) $character->pivot->is_active
                )
                ->values();

        $partyCharacters =
            $activeCharacters
                ->filter(
                    fn ($character) =>
                        (int) $character->user_id
                        !==
                        $currentUserId
                )
                ->values();

        $charactersForMember =
            function ($member) use ($campaign) {
                return $campaign->characters
                    ->filter(
                        fn ($character) =>
                            (int) $character->user_id
                            ===
                            (int) $member->user_id
                    )
                    ->sortByDesc(
                        fn ($character) =>
                            (bool) $character->pivot->is_active
                    )
                    ->values();
            };

        $activeCharacterForMember =
            function ($member) use ($charactersForMember) {
                return $charactersForMember(
                    $member
                )
                    ->first(
                        fn ($character) =>
                            (bool) $character->pivot->is_active
                    );
            };

        $characterHpPercent =
            static function ($character): int {
                $combat =
                    $character->combat;

                $max =
                    max(
                        1,
                        (int) (
                            $combat?->max_hp
                            ?? 1
                        )
                        +
                        (int) (
                            $combat?->temporary_max_hp
                            ?? 0
                        )
                    );

                $current =
                    max(
                        0,
                        (int) (
                            $combat?->current_hp
                            ?? $max
                        )
                    );

                return max(
                    0,
                    min(
                        100,
                        (int) round(
                            ($current / $max)
                            * 100
                        )
                    )
                );
            };

        $characterHpLabel =
            static function ($character): string {
                $combat =
                    $character->combat;

                $max =
                    max(
                        1,
                        (int) (
                            $combat?->max_hp
                            ?? 1
                        )
                        +
                        (int) (
                            $combat?->temporary_max_hp
                            ?? 0
                        )
                    );

                $current =
                    max(
                        0,
                        (int) (
                            $combat?->current_hp
                            ?? $max
                        )
                    );

                return $current . '/' . $max;
            };
    @endphp

    <style>
        /* ============================================================
           SCROLLBARS — PALETA GLOBAL DA CAMPANHA
           ============================================================ */
        html,
        body,
        body * {
            scrollbar-width: thin;
            scrollbar-color: #a98569 #ece2d5;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        body *::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        html::-webkit-scrollbar-track,
        body::-webkit-scrollbar-track,
        body *::-webkit-scrollbar-track {
            border-radius: 999px;
            background: #ece2d5;
            box-shadow: inset 0 0 0 1px rgba(205,187,159,.35);
        }

        html::-webkit-scrollbar-thumb,
        body::-webkit-scrollbar-thumb,
        body *::-webkit-scrollbar-thumb {
            min-height: 38px;
            border: 2px solid #ece2d5;
            border-radius: 999px;
            background: linear-gradient(180deg,#b48d70 0%,#987056 100%);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
        }

        html::-webkit-scrollbar-thumb:hover,
        body::-webkit-scrollbar-thumb:hover,
        body *::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg,#9b6f53 0%,#7e513d 100%);
        }

        html::-webkit-scrollbar-corner,
        body::-webkit-scrollbar-corner,
        body *::-webkit-scrollbar-corner {
            background: #ece2d5;
        }
    </style>


    <div
        x-data="{
            inviteOpen:
                false,

            optionsOpen:
                false,

            masterTab:
                'overview',

            playerTab:
                'overview',
        }"
        class="
            sb-campaign-scroll-theme
            bg-[#eee7dc]
            px-3
            sm:px-5
            lg:px-6

            {{
                $isOwner
                    ? 'h-[100dvh] overflow-hidden py-2'
                    : 'min-h-screen py-3'
            }}
        "
    >
        <div
            class="
                mx-auto
                max-w-[1480px]

                {{
                    $isOwner
                        ? 'flex h-full min-h-0 flex-col'
                        : ''
                }}
            "
        >
            @if(session('success'))
                <div
                    class="
                        mb-2
                        rounded-xl
                        border
                        border-[#cdbb9f]/70
                        bg-[#fffdf8]
                        px-4
                        py-3
                        text-sm
                        font-semibold
                        text-[#53150f]
                    "
                >
                    {{ session('success') }}
                </div>
            @endif


            @if($errors->any())
                <div
                    class="
                        mb-2
                        rounded-xl
                        border
                        border-[#b46a62]/45
                        bg-[#fff8f5]
                        px-4
                        py-3
                        text-sm
                        text-[#6b1d14]
                    "
                >
                    {{ $errors->first() }}
                </div>
            @endif


            {{-- ============================================================
                 CAMPANHA — BARRA MÍNIMA
            ============================================================= --}}
            <section
                class="
                    flex
                    h-10
                    shrink-0
                    items-center
                    gap-2
                    border-b
                    border-[#cdbb9f]/45
                    px-0.5
                "
            >
                <a
                    href="{{ route('campaigns.index') }}"
                    class="
                        flex
                        h-7
                        w-7
                        shrink-0
                        items-center
                        justify-center
                        rounded-lg
                        border
                        border-[#cdbb9f]/55
                        bg-[#fffdf8]
                        text-[11px]
                        font-black
                        text-[#6b1d14]
                        transition
                        hover:bg-[#eadbc8]
                    "
                    title="Voltar para Campanhas"
                >
                    ←
                </a>

                <h1
                    class="
                        min-w-0
                        flex-1
                        truncate
                        font-serif
                        text-[15px]
                        font-black
                        leading-none
                        text-[#53150f]
                    "
                >
                    {{ $campaign->name }}
                </h1>

                @if($isOwner)
                    <button
                        type="button"
                        x-on:click="
                            inviteOpen =
                                true
                        "
                        class="
                            inline-flex
                            h-7
                            shrink-0
                            items-center
                            rounded-lg
                            bg-[#6b1d14]
                            px-2.5
                            text-[7px]
                            font-black
                            uppercase
                            tracking-[0.07em]
                            text-[#fffaf2]
                            transition
                            hover:bg-[#53150f]
                        "
                    >
                        + Convidar
                    </button>
                @endif
            </section>


            {{-- ============================================================
                 TELA DO MESTRE
            ============================================================= --}}
            @if($isOwner)

                @include(
                    'campaigns.components.master-shield',
                    [
                        'campaign' => $campaign,
                        'campaignCombats' => $campaignCombats,
                        'masterShieldInitial' => $masterShieldInitial,
                    ]
                )


            {{-- ============================================================
                 TELA DO PLAYER
            ============================================================= --}}
            @else

                <nav
                    class="
                        mt-6
                        flex
                        gap-1
                        overflow-x-auto
                        rounded-2xl
                        border
                        border-[#cdbb9f]/55
                        bg-[#fffdf8]
                        p-1.5
                        shadow-sm
                    "
                >
                    <button
                        type="button"
                        class="
                            shrink-0
                            rounded-xl
                            bg-[#6b1d14]
                            px-4
                            py-2.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-[0.10em]
                            text-[#fffaf2]
                        "
                    >
                        Visão geral
                    </button>

                    <button
                        type="button"
                        disabled
                        class="
                            shrink-0
                            cursor-not-allowed
                            rounded-xl
                            px-4
                            py-2.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-[0.10em]
                            text-[#a89483]
                        "
                    >
                        Mensagens
                        <span class="ml-1 text-[7px]">
                            · em breve
                        </span>
                    </button>
                </nav>

                <div
                    class="
                        mt-6
                        grid
                        gap-6
                        xl:grid-cols-[minmax(0,1fr)_340px]
                    "
                >
                    <main
                        class="
                            min-w-0
                            space-y-6
                        "
                    >
                        {{-- MINHA PERSONAGEM --}}
                        <section
                            class="
                                rounded-2xl
                                border
                                border-[#b08c62]/35
                                bg-[#fffdf8]
                                p-5
                                shadow-sm
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#8c6239]
                                    "
                                >
                                    Na Campanha
                                </p>

                                <h2
                                    class="
                                        mt-1
                                        font-serif
                                        text-2xl
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    Meus personagens
                                </h2>
                            </div>

                            @if($myCharacters->isNotEmpty())
                                <div
                                    class="
                                        mt-5
                                        grid
                                        gap-3
                                        md:grid-cols-2
                                    "
                                >
                                    @foreach($myCharacters as $character)
                                        <article
                                            class="
                                                rounded-2xl
                                                border
                                                p-4
                                                transition

                                                {{
                                                    $character->pivot->is_active
                                                        ? 'border-[#b08c62]/60 bg-[linear-gradient(145deg,#fffdf8_0%,#f3e4d2_100%)] shadow-sm'
                                                        : 'border-[#d8cec0]/60 bg-[#f3efe9] opacity-60 grayscale-[35%]'
                                                }}
                                            "
                                        >
                                            <div
                                                class="
                                                    flex
                                                    items-center
                                                    gap-4
                                                "
                                            >
                                                <div
                                                    class="
                                                        flex
                                                        h-20
                                                        w-20
                                                        shrink-0
                                                        items-center
                                                        justify-center
                                                        overflow-hidden
                                                        rounded-2xl
                                                        border
                                                        border-[#cdbb9f]
                                                        bg-[#eadbc8]
                                                        font-serif
                                                        text-2xl
                                                        font-black
                                                        text-[#53150f]
                                                    "
                                                >
                                                    @if($character->image_path)
                                                        <img
                                                            src="{{ asset('storage/' . $character->image_path) }}"
                                                            alt=""
                                                            class="
                                                                h-full
                                                                w-full
                                                                object-cover
                                                                object-top
                                                            "
                                                        >
                                                    @else
                                                        {{
                                                            mb_strtoupper(
                                                                mb_substr(
                                                                    $character->name,
                                                                    0,
                                                                    1
                                                                )
                                                            )
                                                        }}
                                                    @endif
                                                </div>

                                                <div
                                                    class="
                                                        min-w-0
                                                        flex-1
                                                    "
                                                >
                                                    <div
                                                        class="
                                                            flex
                                                            items-start
                                                            justify-between
                                                            gap-2
                                                        "
                                                    >
                                                        <div
                                                            class="
                                                                min-w-0
                                                            "
                                                        >
                                                            <h3
                                                                class="
                                                                    truncate
                                                                    font-serif
                                                                    text-xl
                                                                    font-black
                                                                    text-[#53150f]
                                                                "
                                                            >
                                                                {{ $character->name }}
                                                            </h3>

                                                            <p
                                                                class="
                                                                    mt-1
                                                                    truncate
                                                                    text-[9px]
                                                                    font-bold
                                                                    uppercase
                                                                    tracking-wider
                                                                    text-[#8c6239]
                                                                "
                                                            >
                                                                {{ $character->class_label }}
                                                            </p>
                                                        </div>

                                                        <span
                                                            class="
                                                                shrink-0
                                                                rounded-full
                                                                px-2
                                                                py-1
                                                                text-[8px]
                                                                font-black
                                                                uppercase
                                                                tracking-wider
                                                                {{
                                                                    $character->pivot->is_active
                                                                        ? 'bg-[#e8efe7] text-emerald-800'
                                                                        : 'bg-[#eee9e1] text-[#7d604d]'
                                                                }}
                                                            "
                                                        >
                                                            {{
                                                                $character->pivot->is_active
                                                                    ? 'Jogando agora'
                                                                    : 'Descansando'
                                                            }}
                                                        </span>
                                                    </div>

                                                    <div
                                                        class="
                                                            mt-3
                                                            flex
                                                            items-center
                                                            justify-between
                                                            gap-3
                                                            text-[9px]
                                                            font-black
                                                            text-[#7d604d]
                                                        "
                                                    >
                                                        <span>
                                                            PV
                                                        </span>

                                                        <span>
                                                            {{ $characterHpLabel($character) }}
                                                        </span>
                                                    </div>

                                                    <div
                                                        class="
                                                            mt-1.5
                                                            h-2
                                                            overflow-hidden
                                                            rounded-full
                                                            bg-[#ded6c9]
                                                        "
                                                    >
                                                        <div
                                                            class="
                                                                h-full
                                                                rounded-full
                                                                bg-emerald-600
                                                            "
                                                            style="
                                                                width:
                                                                    {{ $characterHpPercent($character) }}%;
                                                            "
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="
                                                    mt-4
                                                    flex
                                                    flex-wrap
                                                    items-center
                                                    gap-2
                                                "
                                            >
                                                <a
                                                    href="{{ route('characters.show', $character) }}"
                                                    class="
                                                        rounded-lg
                                                        bg-[#6b1d14]
                                                        px-4
                                                        py-2.5
                                                        text-[9px]
                                                        font-black
                                                        uppercase
                                                        tracking-wider
                                                        text-[#fffaf2]
                                                        transition
                                                        hover:bg-[#53150f]
                                                    "
                                                >
                                                    Abrir ficha
                                                </a>

                                                @if(!$character->pivot->is_active)
                                                    <form
                                                        method="POST"
                                                        action="{{ route('campaigns.characters.update', [$campaign, $character]) }}"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <input
                                                            type="hidden"
                                                            name="is_active"
                                                            value="1"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="
                                                                rounded-lg
                                                                border
                                                                border-[#b08c62]/60
                                                                bg-[#eadbc8]
                                                                px-3
                                                                py-2
                                                                text-[8px]
                                                                font-black
                                                                uppercase
                                                                tracking-wider
                                                                text-[#6b1d14]
                                                                transition
                                                                hover:bg-[#dcc7ae]
                                                            "
                                                        >
                                                            Jogar agora
                                                        </button>
                                                    </form>
                                                @else
                                                    <span
                                                        class="
                                                            rounded-full
                                                            bg-emerald-100
                                                            px-2.5
                                                            py-1
                                                            text-[7px]
                                                            font-black
                                                            uppercase
                                                            tracking-wider
                                                            text-emerald-800
                                                        "
                                                    >
                                                        Na Party
                                                    </span>
                                                @endif

                                                <form
                                                    method="POST"
                                                    action="{{ route('campaigns.characters.destroy', [$campaign, $character]) }}"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="
                                                            text-[9px]
                                                            font-black
                                                            text-[#8d5148]
                                                            hover:text-red-700
                                                        "
                                                    >
                                                        Remover
                                                    </button>
                                                </form>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="
                                        mt-5
                                        rounded-2xl
                                        border
                                        border-dashed
                                        border-[#cdbb9f]
                                        bg-[#fbf8f1]
                                        px-5
                                        py-8
                                    "
                                >
                                    <h3
                                        class="
                                            font-serif
                                            text-lg
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Escolha quem entra na campanha
                                    </h3>

                                    <p
                                        class="
                                            mt-1
                                            max-w-xl
                                            text-[11px]
                                            leading-5
                                            text-[#76553f]
                                        "
                                    >
                                        Nenhuma ficha sua está compartilhada aqui. A escolha é explícita e não libera seus outros personagens.
                                    </p>

                                    @if($availableCharacters->isNotEmpty())
                                        <form
                                            method="POST"
                                            action="{{ route('campaigns.characters.store', $campaign) }}"
                                            class="
                                                mt-4
                                                flex
                                                flex-col
                                                gap-2
                                                sm:flex-row
                                            "
                                        >
                                            @csrf

                                            <select
                                                name="character_id"
                                                required
                                                class="
                                                    min-w-0
                                                    flex-1
                                                    rounded-xl
                                                    border
                                                    border-[#cdbb9f]
                                                    bg-[#fffdf8]
                                                    px-3
                                                    py-2.5
                                                    text-sm
                                                    text-[#432c21]
                                                    focus:border-[#6b1d14]
                                                    focus:ring-[#6b1d14]/15
                                                "
                                            >
                                                <option value="">
                                                    Escolha um personagem
                                                </option>

                                                @foreach($availableCharacters as $character)
                                                    <option value="{{ $character->id }}">
                                                        {{ $character->name }} — {{ $character->class_label }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button
                                                type="submit"
                                                class="
                                                    rounded-xl
                                                    bg-[#6b1d14]
                                                    px-5
                                                    py-2.5
                                                    text-[9px]
                                                    font-black
                                                    uppercase
                                                    tracking-wider
                                                    text-[#fffaf2]
                                                    transition
                                                    hover:bg-[#53150f]
                                                "
                                            >
                                                Compartilhar
                                            </button>
                                        </form>
                                    @else
                                        <p
                                            class="
                                                mt-4
                                                text-[10px]
                                                font-bold
                                                text-[#8c6239]
                                            "
                                        >
                                            Você não possui outra ficha disponível para compartilhar.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </section>


                        {{-- PARTY --}}
                        <section
                            class="
                                rounded-2xl
                                border
                                border-[#b08c62]/35
                                bg-[#fffdf8]
                                p-5
                                shadow-sm
                            "
                        >
                            <div
                                class="
                                    flex
                                    items-end
                                    justify-between
                                    gap-4
                                "
                            >
                                <div>
                                    <p
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.15em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Companheiros
                                    </p>

                                    <h2
                                        class="
                                            mt-1
                                            font-serif
                                            text-2xl
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Party
                                    </h2>
                                </div>

                                <span
                                    class="
                                        rounded-full
                                        bg-[#eadbc8]
                                        px-2.5
                                        py-1
                                        text-[9px]
                                        font-black
                                        text-[#6b1d14]
                                    "
                                >
                                    {{ $partyCharacters->count() }}
                                </span>
                            </div>

                            <div
                                class="
                                    mt-6
                                    flex
                                    flex-wrap
                                    gap-x-9
                                    gap-y-10
                                "
                            >
                                @forelse($partyCharacters as $character)
                                    <article
                                        class="
                                            w-[160px]
                                            text-center
                                        "
                                    >
                                        <div
                                            class="
                                                mx-auto
                                                flex
                                                h-24
                                                w-24
                                                items-center
                                                justify-center
                                                overflow-hidden
                                                rounded-full
                                                border-[3px]
                                                border-[#cdbb9f]
                                                bg-[#eadbc8]
                                                font-serif
                                                text-2xl
                                                font-black
                                                text-[#53150f]
                                            "
                                        >
                                            @if($character->image_path)
                                                <img
                                                    src="{{ asset('storage/' . $character->image_path) }}"
                                                    alt=""
                                                    class="
                                                        h-full
                                                        w-full
                                                        object-cover
                                                        object-top
                                                    "
                                                >
                                            @else
                                                {{
                                                    mb_strtoupper(
                                                        mb_substr(
                                                            $character->name,
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}
                                            @endif
                                        </div>

                                        <h3
                                            class="
                                                mt-3
                                                truncate
                                                font-serif
                                                text-sm
                                                font-black
                                                text-[#53150f]
                                            "
                                        >
                                            {{ $character->name }}
                                        </h3>

                                        <div
                                            class="
                                                mx-auto
                                                mt-2.5
                                                h-2
                                                w-[130px]
                                                overflow-hidden
                                                rounded-full
                                                bg-[#ded6c9]
                                            "
                                        >
                                            <div
                                                class="
                                                    h-full
                                                    rounded-full
                                                    bg-emerald-600
                                                "
                                                style="
                                                    width:
                                                        {{ $characterHpPercent($character) }}%;
                                                "
                                            ></div>
                                        </div>

                                        <p
                                            class="
                                                mt-1.5
                                                text-[8px]
                                                font-black
                                                text-[#8c6239]
                                            "
                                        >
                                            {{ $characterHpLabel($character) }} PV
                                        </p>
                                    </article>
                                @empty
                                    <div
                                        class="
                                            w-full
                                            rounded-xl
                                            border
                                            border-dashed
                                            border-[#cdbb9f]
                                            px-5
                                            py-10
                                            text-center
                                            text-xs
                                            text-[#7d604d]
                                        "
                                    >
                                        Nenhum outro personagem ativo na Party ainda.
                                    </div>
                                @endforelse
                            </div>
                        </section>


                        {{-- COMPARTILHAR OUTRA FICHA --}}
                        @if($myCharacters->isNotEmpty() && $availableCharacters->isNotEmpty())
                            <section
                                class="
                                    rounded-2xl
                                    border
                                    border-[#cdbb9f]/55
                                    bg-[#f7f0e6]
                                    p-5
                                "
                            >
                                <h2
                                    class="
                                        font-serif
                                        text-lg
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    Compartilhar outra ficha
                                </h2>

                                <form
                                    method="POST"
                                    action="{{ route('campaigns.characters.store', $campaign) }}"
                                    class="
                                        mt-4
                                        flex
                                        flex-col
                                        gap-2
                                        sm:flex-row
                                    "
                                >
                                    @csrf

                                    <select
                                        name="character_id"
                                        required
                                        class="
                                            min-w-0
                                            flex-1
                                            rounded-xl
                                            border
                                            border-[#cdbb9f]
                                            bg-[#fffdf8]
                                            px-3
                                            py-2.5
                                            text-sm
                                            text-[#432c21]
                                            focus:border-[#6b1d14]
                                            focus:ring-[#6b1d14]/15
                                        "
                                    >
                                        <option value="">
                                            Escolha um personagem
                                        </option>

                                        @foreach($availableCharacters as $character)
                                            <option value="{{ $character->id }}">
                                                {{ $character->name }} — {{ $character->class_label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button
                                        type="submit"
                                        class="
                                            rounded-xl
                                            bg-[#6b1d14]
                                            px-5
                                            py-2.5
                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#fffaf2]
                                            transition
                                            hover:bg-[#53150f]
                                        "
                                    >
                                        Compartilhar
                                    </button>
                                </form>
                            </section>
                        @endif
                    </main>


                    <aside
                        class="
                            space-y-6
                        "
                    >
                        <section
                            class="
                                rounded-2xl
                                border
                                border-[#b08c62]/35
                                bg-[#fffdf8]
                                p-5
                                shadow-sm
                            "
                        >
                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.15em]
                                    text-[#8c6239]
                                "
                            >
                                Mestre
                            </p>

                            <div
                                class="
                                    mt-3
                                    flex
                                    items-center
                                    gap-3
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-12
                                        w-12
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-[#eadbc8]
                                        font-serif
                                        text-lg
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    {{
                                        mb_strtoupper(
                                            mb_substr(
                                                $campaign->owner->name,
                                                0,
                                                1
                                            )
                                        )
                                    }}
                                </div>

                                <div
                                    class="
                                        min-w-0
                                    "
                                >
                                    <h2
                                        class="
                                            truncate
                                            font-serif
                                            text-base
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        {{ $campaign->owner->name }}
                                    </h2>

                                    <p
                                        class="
                                            mt-0.5
                                            text-[9px]
                                            text-[#8c6239]
                                        "
                                    >
                                        Mestre da campanha
                                    </p>
                                </div>
                            </div>
                        </section>


                        <section
                            class="
                                rounded-2xl
                                border
                                border-[#cdbb9f]/55
                                bg-[#f7f0e6]
                                p-5
                            "
                        >
                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.15em]
                                    text-[#8c6239]
                                "
                            >
                                Party na ficha
                            </p>

                            <p
                                class="
                                    mt-2
                                    text-[11px]
                                    leading-5
                                    text-[#76553f]
                                "
                            >
                                As interações detalhadas com os companheiros, Diário, Cutucar e Entregar Item ficam na gaveta Party da sua ficha.
                            </p>

                            @if($myActiveCharacters->isNotEmpty())
                                <a
                                    href="{{ route('characters.show', $myActiveCharacters->first()) }}"
                                    class="
                                        mt-4
                                        inline-flex
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#6b1d14]
                                        hover:underline
                                    "
                                >
                                    Abrir minha ficha →
                                </a>
                            @endif
                        </section>
                    </aside>
                </div>

            @endif
        </div>


        {{-- ============================================================
             MODAL — CONVIDAR JOGADOR
        ============================================================= --}}
        @if($isOwner)
            <div
                x-show="
                    inviteOpen
                "
                x-cloak
                x-transition.opacity
                class="
                    fixed
                    inset-0
                    z-[180]
                    flex
                    items-center
                    justify-center
                    p-4
                "
            >
                <div
                    class="
                        absolute
                        inset-0
                        bg-black/90
                        backdrop-blur-[2px]
                    "
                    style="
                        background-color:
                            rgba(8, 6, 5, 0.88);
                    "
                    x-on:click="
                        inviteOpen = false
                    "
                ></div>

                <section
                    x-on:click.stop
                    class="
                        relative
                        z-10
                        w-full
                        max-w-md
                        overflow-hidden
                        rounded-2xl
                        border
                        border-[#cdbb9f]
                        bg-[#f4f1e8]
                        shadow-2xl
                    "
                >
                    <header
                        class="
                            border-b
                            border-[#cdbb9f]/65
                            bg-[#eadbc8]
                            px-5
                            py-4
                        "
                    >
                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.18em]
                                text-[#8c6239]
                            "
                        >
                            Campanha
                        </p>

                        <h2
                            class="
                                mt-1
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            Convidar jogador
                        </h2>
                    </header>

                    <form
                        method="POST"
                        action="{{ route('campaigns.invitations.store', $campaign) }}"
                        class="
                            p-5
                        "
                    >
                        @csrf

                        <p
                            class="
                                text-[10px]
                                leading-5
                                text-[#76553f]
                            "
                        >
                            O convite adiciona o jogador à campanha, mas não libera nenhuma ficha automaticamente.
                        </p>

                        <label
                            class="
                                mt-4
                                block
                            "
                        >
                            <span
                                class="
                                    mb-1.5
                                    block
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#8c6239]
                                "
                            >
                                E-mail do usuário
                            </span>

                            <input
                                type="email"
                                name="email"
                                required
                                autofocus
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]
                                    bg-[#fffdf8]
                                    px-3
                                    py-2.5
                                    text-sm
                                    text-[#432c21]
                                    focus:border-[#6b1d14]
                                    focus:ring-[#6b1d14]/15
                                "
                            >
                        </label>

                        <button
                            type="submit"
                            class="
                                mt-4
                                w-full
                                rounded-xl
                                bg-[#6b1d14]
                                px-4
                                py-3
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-[#fffaf2]
                                transition
                                hover:bg-[#53150f]
                            "
                        >
                            Enviar convite
                        </button>
                    </form>
                </section>
            </div>
        @endif

        @if($isOwner)
            <section
                class="
                    mt-10
                    border-t
                    border-[#cdbb9f]/45
                    pt-5
                    text-right
                "
            >
                <a
                    href="{{ route('campaigns.destroy.confirm', $campaign) }}"
                    class="
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.12em]
                        text-red-700
                        transition
                        hover:text-red-900
                        hover:underline
                    "
                >
                    Excluir campanha
                </a>
            </section>
        @endif
    </div>

</x-app-layout>