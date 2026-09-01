<x-app-layout>

    @php
        $dashboardMode =
            session(
                'dashboard_mode',
                'player'
            );

        $isMasterMode =
            $dashboardMode
            ===
            'master';

        $isPlayerMode =
            !$isMasterMode;
    @endphp

    <div
        class="
            min-h-screen
            bg-[#eee7dc]
            px-4
            py-8
            sm:px-6
        "
        x-data="{ createOpen: false }"
    >
        <div class="mx-auto max-w-6xl">

            <div
                class="
                    mb-6
                    flex
                    flex-col
                    gap-4
                    sm:flex-row
                    sm:items-end
                    sm:justify-between
                "
            >
                <div>
                    <p
                        class="
                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.18em]
                            text-[#8c6239]
                        "
                    >
                        Spellbound
                    </p>

                    <h1
                        class="
                            mt-1
                            font-serif
                            text-3xl
                            font-black
                            text-[#53150f]
                        "
                    >
                        Campanhas
                    </h1>

                    <p
                        class="
                            mt-1
                            max-w-2xl
                            text-sm
                            text-[#76553f]
                        "
                    >
                        {{
                            $isMasterMode
                                ? 'Crie e gerencie suas mesas, jogadores e personagens compartilhados.'
                                : 'Acompanhe somente as campanhas em que você participa e seus convites.'
                        }}
                    </p>
                </div>

                @if($isMasterMode)
                    <button
                        type="button"
                        @click="createOpen = true"

                        class="
                            rounded-xl
                            bg-[#6b1d14]
                            px-4
                            py-2.5

                            font-serif
                            text-xs
                            font-black
                            uppercase
                            tracking-[0.12em]
                            text-[#fffaf2]

                            shadow-sm
                            transition
                            hover:bg-[#53150f]
                        "
                    >
                        Nova Campanha
                    </button>
                @endif
            </div>


            @if(session('success'))
                <div
                    class="
                        mb-5
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


            {{-- CONVITES --}}

            @if($isPlayerMode)
                <section class="mb-8">

                    <div
                        class="
                            mb-3
                            flex
                            items-center
                            justify-between

                            border-b
                            border-[#b08c62]/35

                            pb-2
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
                            Convites
                        </h2>

                        <span
                            class="
                                rounded-full
                                bg-[#eadbc8]
                                px-2.5
                                py-1
                                text-[10px]
                                font-black
                                text-[#6b1d14]
                            "
                        >
                            {{ $pendingInvitations->count() }}
                        </span>
                    </div>

                    @if($pendingInvitations->isEmpty())
                        <div
                            class="
                                rounded-2xl
                                border
                                border-dashed
                                border-[#cdbb9f]
                                bg-[#fbf8f1]/65
                                px-5
                                py-7
                                text-center
                            "
                        >
                            <p
                                class="
                                    text-xs
                                    font-semibold
                                    text-[#7d604d]
                                "
                            >
                                Nenhum convite pendente.
                            </p>
                        </div>
                    @else
                        <div class="grid gap-3 md:grid-cols-2">

                            @foreach($pendingInvitations as $invitation)
                            <article
                                class="
                                    rounded-2xl
                                    border
                                    border-[#b08c62]/35
                                    bg-[#fffdf8]
                                    p-4

                                    shadow-[inset_0_1px_0_rgba(255,255,255,.8),0_3px_10px_rgba(83,21,15,.04)]
                                "
                            >
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.14em]
                                        text-[#8c6239]
                                    "
                                >
                                    Convite de campanha
                                </p>

                                <h3
                                    class="
                                        mt-1
                                        font-serif
                                        text-lg
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    {{ $invitation->campaign->name }}
                                </h3>

                                <p
                                    class="
                                        mt-1
                                        text-xs
                                        text-[#76553f]
                                    "
                                >
                                    Mestre:
                                    <strong>
                                        {{ $invitation->campaign->owner->name }}
                                    </strong>
                                </p>

                                <div
                                    class="
                                        mt-4
                                        flex
                                        gap-2
                                    "
                                >
                                    <form
                                        method="POST"
                                        action="{{ route('campaign-invitations.accept', $invitation) }}"
                                        class="flex-1"
                                    >
                                        @csrf

                                        <button
                                            class="
                                                w-full
                                                rounded-lg
                                                bg-[#6b1d14]
                                                px-3
                                                py-2
                                                text-xs
                                                font-black
                                                text-[#fffaf2]
                                                transition
                                                hover:bg-[#53150f]
                                            "
                                        >
                                            Aceitar
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('campaign-invitations.decline', $invitation) }}"
                                        class="flex-1"
                                    >
                                        @csrf

                                        <button
                                            class="
                                                w-full
                                                rounded-lg
                                                border
                                                border-[#cdbb9f]
                                                bg-[#f7f0e6]
                                                px-3
                                                py-2
                                                text-xs
                                                font-black
                                                text-[#6b1d14]
                                                transition
                                                hover:bg-[#eadbc8]
                                            "
                                        >
                                            Recusar
                                        </button>
                                    </form>
                                </div>
                            </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif


            {{-- CAMPANHAS DO MESTRE --}}

            @if($isMasterMode)
                <section class="mb-8">

                <div
                    class="
                        mb-3
                        border-b
                        border-[#b08c62]/35
                        pb-2
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
                        Minhas campanhas
                    </h2>
                </div>

                @if($ownedCampaigns->isEmpty())
                    <div
                        class="
                            rounded-2xl
                            border
                            border-dashed
                            border-[#cdbb9f]
                            bg-[#fbf8f1]/65
                            p-6
                            text-center
                            text-sm
                            text-[#7d604d]
                        "
                    >
                        Você ainda não criou nenhuma campanha.
                    </div>
                @else
                    <div
                        class="
                            grid
                            gap-4
                            md:grid-cols-2
                            xl:grid-cols-3
                        "
                    >
                        @foreach($ownedCampaigns as $campaign)
                            <a
                                href="{{ route('campaigns.show', $campaign) }}"

                                class="
                                    group
                                    rounded-2xl
                                    border
                                    border-[#b08c62]/35
                                    bg-[linear-gradient(180deg,#fffdf8_0%,#f7f0e6_100%)]
                                    p-5

                                    shadow-sm
                                    transition
                                    hover:-translate-y-0.5
                                    hover:border-[#6b1d14]/35
                                    hover:shadow-md
                                "
                            >
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.14em]
                                        text-[#8c6239]
                                    "
                                >
                                    Mestre
                                </p>

                                <h3
                                    class="
                                        mt-1
                                        font-serif
                                        text-xl
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    {{ $campaign->name }}
                                </h3>

                                <p
                                    class="
                                        mt-2
                                        line-clamp-2
                                        min-h-[36px]
                                        text-xs
                                        leading-relaxed
                                        text-[#76553f]
                                    "
                                >
                                    {{ $campaign->description ?: 'Sem descrição.' }}
                                </p>

                                <div
                                    class="
                                        mt-4
                                        flex
                                        gap-2
                                        border-t
                                        border-[#cdbb9f]/50
                                        pt-3

                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.08em]
                                        text-[#8c6239]
                                    "
                                >
                                    <span>
                                        {{ $campaign->members_count }}
                                        jogadores
                                    </span>

                                    <span>·</span>

                                    <span>
                                        {{ $campaign->characters_count }}
                                        fichas
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
            @endif



            {{-- CAMPANHAS DO PLAYER --}}

            @if($isPlayerMode)
                <section>

                <div
                    class="
                        mb-3
                        border-b
                        border-[#b08c62]/35
                        pb-2
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
                        Minhas campanhas
                    </h2>
                </div>

                @if($joinedCampaigns->isEmpty())
                    <div
                        class="
                            rounded-2xl
                            border
                            border-dashed
                            border-[#cdbb9f]
                            bg-[#fbf8f1]/65
                            p-6
                            text-center
                            text-sm
                            text-[#7d604d]
                        "
                    >
                        Você ainda não participa de nenhuma campanha.
                    </div>
                @else
                    <div
                        class="
                            grid
                            gap-4
                            md:grid-cols-2
                            xl:grid-cols-3
                        "
                    >
                        @foreach($joinedCampaigns as $campaign)
                            <a
                                href="{{ route('campaigns.show', $campaign) }}"

                                class="
                                    rounded-2xl
                                    border
                                    border-[#b08c62]/35
                                    bg-[#fffdf8]
                                    p-5
                                    shadow-sm
                                    transition

                                    hover:-translate-y-0.5
                                    hover:border-[#6b1d14]/35
                                    hover:shadow-md
                                "
                            >
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.14em]
                                        text-[#8c6239]
                                    "
                                >
                                    Jogador
                                </p>

                                <h3
                                    class="
                                        mt-1
                                        font-serif
                                        text-xl
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    {{ $campaign->name }}
                                </h3>

                                <p
                                    class="
                                        mt-2
                                        text-xs
                                        text-[#76553f]
                                    "
                                >
                                    Mestre:
                                    <strong>
                                        {{ $campaign->owner->name }}
                                    </strong>
                                </p>
                            </a>
                        @endforeach
                    </div>
                @endif
                </section>
            @endif
        </div>


        {{-- MODAL CRIAR CAMPANHA --}}

        @if($isMasterMode)
            <template x-teleport="body">
            <div
                x-show="createOpen"
                x-cloak

                @keydown.escape.window="createOpen = false"

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
                    @click="createOpen = false"
                ></div>

                <form
                    method="POST"
                    action="{{ route('campaigns.store') }}"

                    class="
                        relative
                        z-10

                        w-full
                        max-w-lg
                        overflow-hidden

                        rounded-2xl
                        border
                        border-[#b98e63]/70

                        bg-[#faf8f2]

                        shadow-[0_26px_80px_rgba(0,0,0,.38)]
                    "
                >
                    @csrf

                    <div
                        class="
                            border-b
                            border-[#a0774d]/30
                            bg-[#eadbc8]

                            px-5
                            py-4

                            shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                        "
                    >
                        <h2
                            class="
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            Nova Campanha
                        </h2>

                        <p
                            class="
                                mt-1
                                text-xs
                                text-[#76553f]
                            "
                        >
                            Crie a mesa e depois convide os jogadores.
                        </p>
                    </div>

                    <div class="space-y-4 p-5">

                        <div>
                            <label
                                class="
                                    mb-1.5
                                    block
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]
                                    text-[#6f472f]
                                "
                            >
                                Nome
                            </label>

                            <input
                                type="text"
                                name="name"
                                required
                                maxlength="120"
                                value="{{ old('name') }}"

                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]
                                    bg-[#fffdf8]
                                    px-3.5
                                    py-2.5
                                    text-sm
                                    text-[#432c21]

                                    focus:border-[#6b1d14]
                                    focus:ring-[#6b1d14]/15
                                "
                            >
                        </div>

                        <div>
                            <label
                                class="
                                    mb-1.5
                                    block
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]
                                    text-[#6f472f]
                                "
                            >
                                Descrição
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                maxlength="2000"

                                class="
                                    w-full
                                    resize-none
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]
                                    bg-[#fffdf8]
                                    px-3.5
                                    py-2.5
                                    text-sm
                                    text-[#432c21]

                                    focus:border-[#6b1d14]
                                    focus:ring-[#6b1d14]/15
                                "
                            >{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div
                        class="
                            flex
                            justify-end
                            gap-2

                            border-t
                            border-[#a0774d]/25
                            bg-[#eadbc8]

                            px-5
                            py-3
                        "
                    >
                        <button
                            type="button"
                            @click="createOpen = false"

                            class="
                                rounded-lg
                                border
                                border-[#cdbb9f]
                                bg-[#fffdf8]/75
                                px-4
                                py-2
                                text-xs
                                font-black
                                text-[#6b1d14]
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"

                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-4
                                py-2
                                text-xs
                                font-black
                                text-[#fffaf2]

                                hover:bg-[#53150f]
                            "
                        >
                            Criar
                        </button>
                    </div>
                </form>
            </div>
            </template>
        @endif

    </div>

</x-app-layout>