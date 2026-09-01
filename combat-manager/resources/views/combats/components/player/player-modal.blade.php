<div
    x-show="
        openPlayerModal
    "
    x-cloak
    x-data="{
        loading: false,

        activeTab:
            @js(
                $combat->campaign_id
                && $availableCharacters->isNotEmpty()
                    ? 'campaign'
                    : 'manual'
            ),

        error: null,

        async submitCombatant(event) {
            if (this.loading) {
                return;
            }

            this.loading =
                true;

            this.error =
                null;

            const form =
                event.target;

            try {
                const response =
                    await fetch(
                        form.action,
                        {
                            method:
                                'POST',

                            body:
                                new FormData(
                                    form
                                ),

                            headers: {
                                'Accept':
                                    'text/html,application/xhtml+xml',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    );

                if (!response.ok) {
                    throw new Error(
                        response.status === 422
                            ? 'Confira os dados informados.'
                            : 'Não foi possível adicionar o participante.'
                    );
                }

                const pageResponse =
                    await fetch(
                        window.location.href,
                        {
                            cache:
                                'no-store',
                        }
                    );

                if (!pageResponse.ok) {
                    throw new Error(
                        'O participante foi salvo, mas a tela não pôde ser atualizada.'
                    );
                }

                const html =
                    await pageResponse.text();

                const documentUpdated =
                    new DOMParser()
                        .parseFromString(
                            html,
                            'text/html'
                        );

                const currentWrapper =
                    document.getElementById(
                        'combat-panels-wrapper'
                    );

                const updatedWrapper =
                    documentUpdated
                        .getElementById(
                            'combat-panels-wrapper'
                        );

                /*
                | Fecha no escopo pai antes de trocar o conteúdo do wrapper.
                */
                openPlayerModal =
                    false;

                if (
                    currentWrapper
                    &&
                    updatedWrapper
                ) {
                    currentWrapper.innerHTML =
                        updatedWrapper.innerHTML;

                    if (window.Alpine) {
                        Alpine.initTree(
                            currentWrapper
                        );
                    }
                }

            } catch (error) {
                console.error(
                    'Erro ao adicionar participante:',
                    error
                );

                this.error =
                    error?.message
                    ?? 'Não foi possível adicionar o participante.';

            } finally {
                this.loading =
                    false;
            }
        },
    }"
    class="
        fixed
        inset-0
        z-[190]
        flex
        items-center
        justify-center
        p-4
    "
    :class="
        loading
            ? 'pointer-events-none'
            : ''
    "
>
    {{-- Backdrop padrão do site --}}

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
        @click="
            if (!loading) {
                openPlayerModal = false
            }
        "
    ></div>


    <section
        class="
            relative
            z-10
            w-full
            max-w-xl
            overflow-hidden
            rounded-2xl
            border
            border-[#cdbb9f]/65
            bg-[#f4f1e8]
            shadow-2xl
        "
    >
        {{-- ============================================================
             HEADER
        ============================================================= --}}

        <header
            class="
                flex
                items-start
                justify-between
                gap-4
                border-b
                border-[#cdbb9f]/45
                bg-[#eadbc8]
                px-6
                py-5
            "
        >
            <div>
                <p
                    class="
                        text-[9px]
                        font-black
                        uppercase
                        tracking-[0.18em]
                        text-[#8c6239]
                    "
                >
                    Combate
                </p>

                <h2
                    class="
                        mt-0.5
                        font-serif
                        text-xl
                        font-black
                        text-[#53150f]
                    "
                >
                    Adicionar participante
                </h2>

                @if($combat->campaign)
                    <p
                        class="
                            mt-1
                            text-[11px]
                            font-medium
                            text-[#76553f]
                        "
                    >
                        Campanha:
                        <strong>
                            {{ $combat->campaign->name }}
                        </strong>
                    </p>
                @endif
            </div>

            <button
                type="button"
                @click="
                    if (!loading) {
                        openPlayerModal = false
                    }
                "
                :disabled="
                    loading
                "
                class="
                    flex
                    h-9
                    w-9
                    items-center
                    justify-center
                    rounded-lg
                    text-xl
                    text-[#8c6239]
                    transition
                    hover:bg-[#f4f1e8]/65
                    hover:text-[#53150f]
                    disabled:opacity-40
                "
            >
                ×
            </button>
        </header>


        {{-- ============================================================
             ABAS
        ============================================================= --}}

        <div
            class="
                border-b
                border-[#cdbb9f]/45
                bg-[#fbf8f1]
                px-6
                pt-4
            "
        >
            <div
                class="
                    grid
                    grid-cols-2
                    gap-1
                    rounded-xl
                    border
                    border-[#cdbb9f]/55
                    bg-[#efe9dc]/65
                    p-1
                "
            >
                <button
                    type="button"
                    @click="
                        activeTab = 'campaign';
                        error = null;
                    "
                    @if(!$combat->campaign)
                        disabled
                    @endif
                    class="
                        rounded-lg
                        px-3
                        py-2
                        text-[9px]
                        font-black
                        uppercase
                        tracking-[0.10em]
                        transition
                        disabled:cursor-not-allowed
                        disabled:opacity-35
                    "
                    :class="
                        activeTab === 'campaign'
                            ? 'bg-[#6b1d14] text-[#fffaf2] shadow-sm'
                            : 'text-[#8c6239] hover:bg-white/70'
                    "
                >
                    Ficha da Campanha
                </button>

                <button
                    type="button"
                    @click="
                        activeTab = 'manual';
                        error = null;
                    "
                    class="
                        rounded-lg
                        px-3
                        py-2
                        text-[9px]
                        font-black
                        uppercase
                        tracking-[0.10em]
                        transition
                    "
                    :class="
                        activeTab === 'manual'
                            ? 'bg-[#6b1d14] text-[#fffaf2] shadow-sm'
                            : 'text-[#8c6239] hover:bg-white/70'
                    "
                >
                    Participante Manual
                </button>
            </div>
        </div>


        <div
            class="
                max-h-[70vh]
                overflow-y-auto
                px-6
                py-5
            "
        >
            {{-- ========================================================
                 CHARACTER DA CAMPANHA
            ========================================================= --}}

            <div
                x-show="
                    activeTab === 'campaign'
                "
                x-cloak
            >
                @if(!$combat->campaign)

                    <div
                        class="
                            rounded-xl
                            border
                            border-dashed
                            border-[#cdbb9f]
                            bg-[#fbf8f1]
                            px-4
                            py-6
                            text-center
                        "
                    >
                        <p
                            class="
                                text-sm
                                font-bold
                                text-[#53150f]
                            "
                        >
                            Combate independente
                        </p>

                        <p
                            class="
                                mt-1
                                text-[11px]
                                leading-relaxed
                                text-[#8c6239]
                            "
                        >
                            Para inserir fichas compartilhadas, crie o combate vinculado a uma campanha.
                        </p>
                    </div>

                @elseif($availableCharacters->isEmpty())

                    <div
                        class="
                            rounded-xl
                            border
                            border-dashed
                            border-[#cdbb9f]
                            bg-[#fbf8f1]
                            px-4
                            py-6
                            text-center
                        "
                    >
                        <p
                            class="
                                text-sm
                                font-bold
                                text-[#53150f]
                            "
                        >
                            Nenhuma ficha disponível
                        </p>

                        <p
                            class="
                                mt-1
                                text-[11px]
                                leading-relaxed
                                text-[#8c6239]
                            "
                        >
                            Todas as fichas ativas da campanha já estão neste combate ou ainda não foram compartilhadas pelos Players.
                        </p>
                    </div>

                @else

                    <form
                        method="POST"
                        action="{{ route('combats.characters.store', $combat) }}"
                        @submit.prevent="
                            submitCombatant(
                                $event
                            )
                        "
                    >
                        @csrf

                        <label class="block">
                            <span
                                class="
                                    mb-2
                                    block
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-wider
                                    text-[#8c6239]
                                "
                            >
                                Ficha compartilhada
                            </span>

                            <select
                                name="character_id"
                                required
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]
                                    bg-[#fffdf8]
                                    px-3
                                    py-3
                                    text-sm
                                    font-bold
                                    text-[#53150f]
                                    focus:border-[#6b1d14]
                                    focus:ring-1
                                    focus:ring-[#6b1d14]/15
                                "
                            >
                                <option value="">
                                    Escolha um personagem
                                </option>

                                @foreach($availableCharacters as $character)
                                    <option value="{{ $character->id }}">
                                        {{ $character->name }}
                                        @if($character->class_label)
                                            — {{ $character->class_label }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label
                            class="
                                mt-4
                                block
                            "
                        >
                            <span
                                class="
                                    mb-2
                                    block
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-wider
                                    text-[#8c6239]
                                "
                            >
                                Iniciativa inicial
                            </span>

                            <input
                                type="number"
                                name="initiative"
                                value="0"
                                min="-20"
                                max="99"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]
                                    bg-[#fffdf8]
                                    px-3
                                    py-3
                                    text-sm
                                    font-bold
                                    text-[#53150f]
                                    focus:border-[#6b1d14]
                                    focus:ring-1
                                    focus:ring-[#6b1d14]/15
                                "
                            >
                        </label>

                        <p
                            class="
                                mt-3
                                rounded-lg
                                border
                                border-[#d4b36b]/40
                                bg-[#f8efd9]/60
                                px-3
                                py-2.5
                                text-[10px]
                                leading-relaxed
                                text-[#8a6418]
                            "
                        >
                            A vida não será copiada para o combate. O card usará diretamente os PV salvos na ficha do personagem.
                        </p>

                        <button
                            type="submit"
                            :disabled="
                                loading
                            "
                            class="
                                mt-5
                                w-full
                                rounded-xl
                                bg-[#6b1d14]
                                px-4
                                py-3
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#fffaf2]
                                transition
                                hover:bg-[#53150f]
                                disabled:cursor-wait
                                disabled:opacity-55
                            "
                        >
                            <span
                                x-show="
                                    !loading
                                "
                            >
                                Adicionar ficha ao combate
                            </span>

                            <span
                                x-show="
                                    loading
                                "
                                x-cloak
                            >
                                Adicionando...
                            </span>
                        </button>
                    </form>

                @endif
            </div>


            {{-- ========================================================
                 PARTICIPANTE MANUAL
            ========================================================= --}}

            <div
                x-show="
                    activeTab === 'manual'
                "
                x-cloak
            >
                <form
                    method="POST"
                    action="{{ route('combats.players.store', $combat) }}"
                    @submit.prevent="
                        submitCombatant(
                            $event
                        )
                    "
                >
                    @csrf

                    <label class="block">
                        <span
                            class="
                                mb-2
                                block
                                text-[10px]
                                font-black
                                uppercase
                                tracking-wider
                                text-[#8c6239]
                            "
                        >
                            Nome do participante
                        </span>

                        <input
                            type="text"
                            name="name"
                            required
                            class="
                                w-full
                                rounded-xl
                                border
                                border-[#cdbb9f]
                                bg-[#fffdf8]
                                px-3
                                py-3
                                text-sm
                                font-bold
                                text-[#53150f]
                                focus:border-[#6b1d14]
                                focus:ring-1
                                focus:ring-[#6b1d14]/15
                            "
                        >
                    </label>

                    <label
                        class="
                            mt-4
                            block
                        "
                    >
                        <span
                            class="
                                mb-2
                                block
                                text-[10px]
                                font-black
                                uppercase
                                tracking-wider
                                text-[#8c6239]
                            "
                        >
                            Iniciativa
                        </span>

                        <input
                            type="number"
                            name="initiative"
                            value="0"
                            min="-20"
                            max="99"
                            class="
                                w-full
                                rounded-xl
                                border
                                border-[#cdbb9f]
                                bg-[#fffdf8]
                                px-3
                                py-3
                                text-sm
                                font-bold
                                text-[#53150f]
                                focus:border-[#6b1d14]
                                focus:ring-1
                                focus:ring-[#6b1d14]/15
                            "
                        >
                    </label>

                    <p
                        class="
                            mt-3
                            text-[10px]
                            leading-relaxed
                            text-[#8c6239]/80
                        "
                    >
                        Participantes manuais continuam disponíveis para aliados, convidados ou personagens sem ficha no Spellbound.
                    </p>

                    <button
                        type="submit"
                        :disabled="
                            loading
                        "
                        class="
                            mt-5
                            w-full
                            rounded-xl
                            bg-[#8c6239]
                            px-4
                            py-3
                            text-[10px]
                            font-black
                            uppercase
                            tracking-widest
                            text-[#fffaf2]
                            transition
                            hover:bg-[#724b24]
                            disabled:cursor-wait
                            disabled:opacity-55
                        "
                    >
                        <span
                            x-show="
                                !loading
                            "
                        >
                            Adicionar participante manual
                        </span>

                        <span
                            x-show="
                                loading
                            "
                            x-cloak
                        >
                            Adicionando...
                        </span>
                    </button>
                </form>
            </div>


            <p
                x-show="
                    error
                "
                x-cloak
                x-text="
                    error
                "
                class="
                    mt-4
                    rounded-lg
                    border
                    border-red-200
                    bg-red-50
                    px-3
                    py-2.5
                    text-[10px]
                    font-bold
                    text-red-700
                "
            ></p>
        </div>
    </section>
</div>