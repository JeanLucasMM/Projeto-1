<x-app-layout>
    <div
        class="
            min-h-screen
            bg-[#eee7dc]
            px-4
            py-6
            text-[#3b2a21]
            sm:px-6
            lg:px-8
        "
        x-data="{
            section: 'general',
            search: '',
            allowAll: {{ $campaign->allow_all_homebrew_spells ? 'true' : 'false' }},
            experienceEnabled: {{ $campaign->experience_enabled ? 'true' : 'false' }},

            matches(text) {
                return !this.search
                    || String(text)
                        .toLowerCase()
                        .includes(
                            this.search.toLowerCase()
                        );
            },

            selectVisible(value) {
                document
                    .querySelectorAll(
                        '[data-homebrew-source]'
                    )
                    .forEach((row) => {
                        if (
                            row.style.display
                            ===
                            'none'
                        ) {
                            return;
                        }

                        const checkbox =
                            row.querySelector(
                                'input[type=checkbox]'
                            );

                        if (
                            checkbox
                            &&
                            !checkbox.disabled
                        ) {
                            checkbox.checked =
                                value;
                        }
                    });
            }
        }"
    >
        <div class="mx-auto max-w-6xl">

            {{-- ============================================================
                 CABEÇALHO
            ============================================================= --}}
            <header
                class="
                    mb-4
                    overflow-hidden
                    rounded-[18px]
                    border
                    border-[#cdbb9f]/35
                    bg-[#fffdf8]
                    shadow-[0_14px_45px_rgba(0,0,0,.22)]
                "
            >
                <div
                    class="
                        flex
                        flex-col
                        gap-4
                        bg-[linear-gradient(135deg,#fffdf8,#f3e4d2)]
                        px-5
                        py-5
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        sm:px-6
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
                        <a
                            href="{{ route('campaigns.show', $campaign) }}"
                            class="
                                flex
                                h-10
                                w-10
                                shrink-0
                                items-center
                                justify-center
                                rounded-xl
                                border
                                border-[#cdbb9f]/35
                                bg-[#fffdf8]
                                text-lg
                                font-black
                                text-[#6b1d14]
                                transition
                                hover:-translate-x-0.5
                                hover:bg-white
                            "
                            aria-label="Voltar para a campanha"
                            title="Voltar"
                        >
                            ←
                        </a>

                        <div class="min-w-0">
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.22em]
                                    text-[#8c6239]
                                "
                            >
                                Configurações da campanha
                            </p>

                            <h1
                                class="
                                    mt-0.5
                                    truncate
                                    font-serif
                                    text-2xl
                                    font-black
                                    leading-tight
                                    text-[#53150f]
                                "
                            >
                                {{ $campaign->name }}
                            </h1>

                            <p
                                class="
                                    mt-1
                                    text-xs
                                    text-[#76553f]
                                "
                            >
                                Regras, conteúdo opcional e administração da mesa.
                            </p>
                        </div>
                    </div>

                    <div
                        class="
                            inline-flex
                            w-fit
                            items-center
                            gap-2
                            rounded-full
                            border
                            border-emerald-800/20
                            bg-emerald-900/10
                            px-3
                            py-1.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-[0.12em]
                            text-emerald-800
                        "
                    >
                        <span
                            class="
                                h-1.5
                                w-1.5
                                rounded-full
                                bg-emerald-600
                            "
                        ></span>

                        Campanha ativa
                    </div>
                </div>

                {{-- NAVEGAÇÃO COMPACTA --}}
                <nav
                    class="
                        flex
                        gap-1
                        overflow-x-auto
                        border-t
                        border-[#cdbb9f]/20
                        bg-[#f7f0e6]
                        p-2
                    "
                >
                    <button
                        type="button"
                        @click="section = 'general'"
                        class="
                            shrink-0
                            rounded-lg
                            px-4
                            py-2.5
                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.09em]
                            transition
                        "
                        :class="
                            section === 'general'
                                ? 'bg-[#6b1d14] text-[#fffaf2] shadow-sm'
                                : 'text-[#76553f] hover:bg-[#eadbc8]'
                        "
                    >
                        Geral
                    </button>

                    <button
                        type="button"
                        @click="section = 'table'"
                        class="
                            shrink-0
                            rounded-lg
                            px-4
                            py-2.5
                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.09em]
                            transition
                        "
                        :class="
                            section === 'table'
                                ? 'bg-[#6b1d14] text-[#fffaf2] shadow-sm'
                                : 'text-[#76553f] hover:bg-[#eadbc8]'
                        "
                    >
                        Regras da mesa
                    </button>

                    <button
                        type="button"
                        @click="section = 'optional'"
                        class="
                            shrink-0
                            rounded-lg
                            px-4
                            py-2.5
                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.09em]
                            transition
                        "
                        :class="
                            section === 'optional'
                                ? 'bg-[#6b1d14] text-[#fffaf2] shadow-sm'
                                : 'text-[#76553f] hover:bg-[#eadbc8]'
                        "
                    >
                        Regras opcionais
                    </button>

                    <button
                        type="button"
                        @click="section = 'danger'"
                        class="
                            shrink-0
                            rounded-lg
                            px-4
                            py-2.5
                            text-[10px]
                            font-black
                            uppercase
                            tracking-[0.09em]
                            transition
                        "
                        :class="
                            section === 'danger'
                                ? 'bg-[#82251d] text-white shadow-sm'
                                : 'text-[#873d34] hover:bg-red-100/50'
                        "
                    >
                        Zona de perigo
                    </button>
                </nav>
            </header>


            {{-- ============================================================
                 FEEDBACK
            ============================================================= --}}
            @if (session('success'))
                <div
                    class="
                        mb-4
                        rounded-xl
                        border
                        border-emerald-700/20
                        bg-emerald-50
                        px-4
                        py-3
                        text-sm
                        font-semibold
                        text-emerald-800
                    "
                >
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="
                        mb-4
                        rounded-xl
                        border
                        border-red-700/20
                        bg-red-50
                        px-4
                        py-3
                        text-sm
                        text-red-800
                    "
                >
                    <strong>
                        Não foi possível salvar.
                    </strong>

                    <ul
                        class="
                            mt-2
                            list-disc
                            space-y-1
                            pl-5
                        "
                    >
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- ============================================================
                 CONTEÚDO
            ============================================================= --}}
            <main
                class="
                    overflow-hidden
                    rounded-[18px]
                    border
                    border-[#cdbb9f]/35
                    bg-[#efe2c7]
                    shadow-[0_18px_55px_rgba(0,0,0,.24)]
                "
            >

                {{-- ========================================================
                     GERAL
                ========================================================= --}}
                <section
                    x-show="section === 'general'"
                    x-cloak
                >
                    <div
                        class="
                            border-b
                            border-[#cdbb9f]/25
                            bg-[#f3e4d2]
                            px-5
                            py-4
                            sm:px-6
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
                            Informações gerais
                        </h2>

                        <p
                            class="
                                mt-1
                                text-sm
                                text-[#76553f]
                            "
                        >
                            Nome e descrição exibidos para os participantes.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('campaigns.master.settings.update', $campaign) }}"
                        class="p-5 sm:p-6"
                    >
                        @csrf
                        @method('PATCH')

                        <div
                            class="
                                grid
                                gap-5
                                lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]
                            "
                        >
                            <div>
                                <label
                                    for="campaign-name"
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.12em]
                                        text-[#76553f]
                                    "
                                >
                                    Nome da campanha
                                </label>

                                <input
                                    id="campaign-name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $campaign->name) }}"
                                    maxlength="120"
                                    required
                                    class="
                                        mt-2
                                        w-full
                                        rounded-xl
                                        border-[#cdbb9f]/45
                                        bg-[#fffdf8]
                                        text-sm
                                        text-[#3b2a21]
                                        focus:border-[#6b1d14]
                                        focus:ring-[#6b1d14]
                                    "
                                >
                            </div>

                            <div>
                                <label
                                    for="campaign-description"
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.12em]
                                        text-[#76553f]
                                    "
                                >
                                    Descrição
                                </label>

                                <textarea
                                    id="campaign-description"
                                    name="description"
                                    rows="4"
                                    maxlength="2000"
                                    class="
                                        mt-2
                                        w-full
                                        resize-y
                                        rounded-xl
                                        border-[#cdbb9f]/45
                                        bg-[#fffdf8]
                                        text-sm
                                        text-[#3b2a21]
                                        focus:border-[#6b1d14]
                                        focus:ring-[#6b1d14]
                                    "
                                >{{ old('description', $campaign->description) }}</textarea>
                            </div>
                        </div>

                        <div
                            class="
                                mt-5
                                flex
                                justify-end
                            "
                        >
                            <button
                                type="submit"
                                class="
                                    rounded-xl
                                    bg-[#6b1d14]
                                    px-5
                                    py-2.5
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]
                                    text-[#fffaf2]
                                    transition
                                    hover:bg-[#53150f]
                                "
                            >
                                Salvar alterações
                            </button>
                        </div>
                    </form>
                </section>


                                {{-- ========================================================
                     REGRAS DA MESA
                ========================================================= --}}
                <section
                    x-show="section === 'table'"
                    x-cloak
                >
                    <div
                        class="
                            border-b border-[#cdbb9f]/45 bg-[#f3e4d2]
                            px-5 py-4 sm:px-6
                        "
                    >
                        <p
                            class="
                                text-[8px] font-black uppercase
                                tracking-[0.18em] text-[#8c6239]
                            "
                        >Sistema da mesa</p>
                        <h2 class="mt-1 font-serif text-xl font-black text-[#53150f]">
                            Regras da mesa
                        </h2>
                        <p class="mt-1 text-sm text-[#76553f]">
                            Regras que valem para todos os personagens vinculados.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('campaigns.master.experience.settings', $campaign) }}"
                        class="p-5 sm:p-6"
                    >
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="experience_enabled" value="0">

                        <label
                            class="
                                flex cursor-pointer items-start justify-between gap-5
                                rounded-2xl border border-[#cdbb9f]/70
                                bg-[#fbf8f1] p-5 transition hover:bg-[#fffdf8]
                            "
                        >
                            <span class="flex min-w-0 items-start gap-4">
                                <span
                                    class="
                                        flex h-10 w-10 shrink-0 items-center justify-center
                                        rounded-xl bg-[#eadbc8] font-serif text-sm
                                        font-black text-[#6b1d14]
                                    "
                                >XP</span>

                                <span class="min-w-0">
                                    <span class="flex flex-wrap items-center gap-2">
                                        <strong class="font-serif text-base text-[#53150f]">
                                            Progressão por experiência
                                        </strong>
                                        <span
                                            x-show="experienceEnabled"
                                            x-cloak
                                            class="
                                                rounded-full bg-emerald-100 px-2 py-1
                                                text-[7px] font-black uppercase
                                                tracking-[0.09em] text-emerald-800
                                            "
                                        >Ativa</span>
                                    </span>

                                    <span class="mt-1 block max-w-2xl text-xs leading-5 text-[#76553f]">
                                        O Mestre controla o XP de cada ficha. Players apenas
                                        visualizam a experiência desta campanha na própria ficha.
                                    </span>

                                    <span class="mt-2 block text-[9px] font-semibold text-[#8c6239]">
                                        XP e nível são independentes: atingir uma quantidade de XP
                                        não sobe o personagem automaticamente.
                                    </span>
                                </span>
                            </span>

                            <span
                                class="relative mt-1 h-6 w-11 shrink-0 rounded-full transition"
                                :class="experienceEnabled ? 'bg-[#6b1d14]' : 'bg-[#d8c7ab]'"
                            >
                                <input
                                    type="checkbox"
                                    name="experience_enabled"
                                    value="1"
                                    x-model="experienceEnabled"
                                    class="sr-only"
                                >
                                <span
                                    class="
                                        absolute top-0.5 h-5 w-5 rounded-full
                                        bg-[#fffdf8] shadow-sm transition
                                    "
                                    :class="experienceEnabled ? 'translate-x-[22px]' : 'translate-x-0.5'"
                                ></span>
                            </span>
                        </label>

                        <div class="mt-4 flex justify-end">
                            <button
                                type="submit"
                                class="
                                    rounded-xl bg-[#6b1d14] px-5 py-2.5
                                    text-[9px] font-black uppercase tracking-[0.10em]
                                    text-[#fffaf2] transition hover:bg-[#53150f]
                                "
                            >Salvar regra</button>
                        </div>
                    </form>
                </section>


{{-- ========================================================
                     REGRAS OPCIONAIS
                ========================================================= --}}
                <section
                    x-show="section === 'optional'"
                    x-cloak
                >
                    <div
                        class="
                            border-b
                            border-[#cdbb9f]/25
                            bg-[#f3e4d2]
                            px-5
                            py-4
                            sm:px-6
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
                            Conteúdo adicional
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
                            Regras opcionais
                        </h2>

                        <p
                            class="
                                mt-1
                                text-sm
                                text-[#76553f]
                            "
                        >
                            O Mestre decide quais conteúdos adicionais entram na campanha.
                        </p>
                    </div>

                    <div
                        class="
                            space-y-4
                            p-5
                            sm:p-6
                        "
                    >

                        {{-- CATÁLOGO PADRÃO --}}
                        <article
                            class="
                                overflow-hidden
                                rounded-2xl
                                border
                                border-emerald-900/15
                                bg-[#fbf8f1]
                            "
                        >
                            <div
                                class="
                                    flex
                                    flex-col
                                    gap-4
                                    p-5
                                    sm:flex-row
                                    sm:items-center
                                    sm:justify-between
                                "
                            >
                                <div
                                    class="
                                        flex
                                        min-w-0
                                        items-start
                                        gap-4
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            h-10
                                            w-10
                                            shrink-0
                                            items-center
                                            justify-center
                                            rounded-xl
                                            bg-emerald-800/10
                                            text-lg
                                            text-emerald-800
                                        "
                                    >
                                        ✓
                                    </div>

                                    <div class="min-w-0">
                                        <div
                                            class="
                                                flex
                                                flex-wrap
                                                items-center
                                                gap-2
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
                                                Catálogo padrão de feitiços
                                            </h3>

                                            <span
                                                class="
                                                    rounded-full
                                                    bg-emerald-800/10
                                                    px-2
                                                    py-1
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.1em]
                                                    text-emerald-800
                                                "
                                            >
                                                Sempre ativo
                                            </span>
                                        </div>

                                        <p
                                            class="
                                                mt-1
                                                max-w-2xl
                                                text-sm
                                                leading-5
                                                text-[#6c5543]
                                            "
                                        >
                                            PHB, XPHB e demais fontes do catálogo principal
                                            ficam disponíveis por padrão para todos os players.
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="
                                        shrink-0
                                        rounded-xl
                                        border
                                        border-[#cdbb9f]/25
                                        bg-[#fffdf8]
                                        px-5
                                        py-3
                                        text-center
                                    "
                                >
                                    <strong
                                        class="
                                            block
                                            font-serif
                                            text-2xl
                                            leading-none
                                            text-[#53150f]
                                        "
                                    >
                                        {{ number_format($officialSpellCount, 0, ',', '.') }}
                                    </strong>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]
                                            text-[#8c6239]
                                        "
                                    >
                                        feitiços
                                    </span>
                                </div>
                            </div>
                        </article>


                        {{-- HOMEBREW --}}
                        <article
                            class="
                                overflow-hidden
                                rounded-2xl
                                border
                                border-[#b08c62]/30
                                bg-[#fbf8f1]
                            "
                        >
                            <header
                                class="
                                    border-b
                                    border-[#b08c62]/20
                                    bg-[#eadbc8]/60
                                    p-5
                                "
                            >
                                <div
                                    class="
                                        flex
                                        flex-col
                                        gap-4
                                        lg:flex-row
                                        lg:items-center
                                        lg:justify-between
                                    "
                                >
                                    <div>
                                        <p
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.16em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Grimório · Third-party
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
                                            Fontes homebrew
                                        </h3>

                                        <p
                                            class="
                                                mt-1
                                                max-w-2xl
                                                text-sm
                                                leading-5
                                                text-[#76553f]
                                            "
                                        >
                                            Libere somente os livros e suplementos
                                            aprovados para esta campanha.
                                        </p>
                                    </div>

                                    <div
                                        class="
                                            flex
                                            gap-2
                                        "
                                    >
                                        <div
                                            class="
                                                min-w-[92px]
                                                rounded-xl
                                                border
                                                border-[#cdbb9f]/25
                                                bg-[#fffdf8]
                                                px-4
                                                py-2.5
                                                text-center
                                            "
                                        >
                                            <strong
                                                class="
                                                    block
                                                    font-serif
                                                    text-xl
                                                    leading-none
                                                    text-[#53150f]
                                                "
                                            >
                                                {{ number_format($homebrewSpellCount, 0, ',', '.') }}
                                            </strong>

                                            <span
                                                class="
                                                    mt-1
                                                    block
                                                    text-[7px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.1em]
                                                    text-[#8c6239]
                                                "
                                            >
                                                disponíveis
                                            </span>
                                        </div>

                                        <div
                                            class="
                                                min-w-[92px]
                                                rounded-xl
                                                border
                                                border-[#6b3b23]/20
                                                bg-[#6b2b20]/8
                                                px-4
                                                py-2.5
                                                text-center
                                            "
                                        >
                                            <strong
                                                class="
                                                    block
                                                    font-serif
                                                    text-xl
                                                    leading-none
                                                    text-[#53150f]
                                                "
                                            >
                                                {{ number_format($enabledHomebrewSpellCount, 0, ',', '.') }}
                                            </strong>

                                            <span
                                                class="
                                                    mt-1
                                                    block
                                                    text-[7px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.1em]
                                                    text-[#8c6239]
                                                "
                                            >
                                                liberados
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </header>

                            <form
                                method="POST"
                                action="{{ route('master.spells.sources.update', $campaign) }}"
                                class="p-5"
                            >
                                @csrf
                                @method('PUT')

                                {{-- TOGGLE GLOBAL --}}
                                <label
                                    class="
                                        flex
                                        cursor-pointer
                                        items-start
                                        gap-4
                                        rounded-xl
                                        border
                                        border-[#b08c62]/30
                                        bg-[#fffdf8]
                                        p-4
                                        transition
                                        hover:bg-[#fbf8f1]
                                    "
                                >
                                    <input
                                        type="checkbox"
                                        name="allow_all_homebrew_spells"
                                        value="1"
                                        x-model="allowAll"
                                        class="
                                            mt-0.5
                                            h-5
                                            w-5
                                            rounded
                                            border-[#b08c62]
                                            text-[#6b1d14]
                                            focus:ring-[#8d4134]
                                        "
                                    >

                                    <span
                                        class="
                                            min-w-0
                                            flex-1
                                        "
                                    >
                                        <span
                                            class="
                                                flex
                                                flex-wrap
                                                items-center
                                                gap-2
                                            "
                                        >
                                            <strong
                                                class="
                                                    font-serif
                                                    text-base
                                                    text-[#53150f]
                                                "
                                            >
                                                Permitir todos os homebrews
                                            </strong>

                                            <span
                                                x-show="allowAll"
                                                x-cloak
                                                class="
                                                    rounded-full
                                                    bg-[#6b1d14]/10
                                                    px-2
                                                    py-1
                                                    text-[7px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.1em]
                                                    text-[#6b1d14]
                                                "
                                            >
                                                ativo
                                            </span>
                                        </span>

                                        <span
                                            class="
                                                mt-1
                                                block
                                                text-xs
                                                leading-5
                                                text-[#76553f]
                                            "
                                        >
                                            Inclui automaticamente todas as fontes
                                            atuais e futuras do catálogo third-party.
                                        </span>
                                    </span>
                                </label>


                                {{-- FILTRO --}}
                                <div
                                    class="
                                        mt-5
                                    "
                                    :class="
                                        allowAll
                                            ? 'opacity-40'
                                            : ''
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            flex-col
                                            gap-3
                                            sm:flex-row
                                            sm:items-end
                                            sm:justify-between
                                        "
                                    >
                                        <div>
                                            <h4
                                                class="
                                                    font-serif
                                                    text-base
                                                    font-black
                                                    text-[#53150f]
                                                "
                                            >
                                                Seleção por fonte
                                            </h4>

                                            <p
                                                class="
                                                    mt-1
                                                    text-xs
                                                    text-[#76553f]
                                                "
                                            >
                                                Selecione apenas o conteúdo autorizado pelo Mestre.
                                            </p>
                                        </div>

                                        <div
                                            class="
                                                flex
                                                flex-col
                                                gap-2
                                                sm:flex-row
                                            "
                                        >
                                            <input
                                                type="search"
                                                x-model="search"
                                                placeholder="Buscar fonte, código ou autor..."
                                                class="
                                                    w-full
                                                    rounded-xl
                                                    border-[#cdbb9f]/45
                                                    bg-[#fffdf8]
                                                    text-sm
                                                    text-[#3b2a21]
                                                    placeholder:text-[#9b8878]
                                                    focus:border-[#6b1d14]
                                                    focus:ring-[#6b1d14]
                                                    sm:w-72
                                                "
                                            >

                                            <div class="flex gap-1">
                                                <button
                                                    type="button"
                                                    @click="selectVisible(true)"
                                                    :disabled="allowAll"
                                                    class="
                                                        rounded-lg
                                                        border
                                                        border-[#b08c62]/30
                                                        bg-[#fffdf8]
                                                        px-3
                                                        py-2
                                                        text-[8px]
                                                        font-black
                                                        uppercase
                                                        tracking-wide
                                                        text-[#6b1d14]
                                                        transition
                                                        hover:bg-white
                                                        disabled:cursor-not-allowed
                                                    "
                                                >
                                                    Marcar
                                                </button>

                                                <button
                                                    type="button"
                                                    @click="selectVisible(false)"
                                                    :disabled="allowAll"
                                                    class="
                                                        rounded-lg
                                                        border
                                                        border-[#b08c62]/30
                                                        bg-[#fffdf8]
                                                        px-3
                                                        py-2
                                                        text-[8px]
                                                        font-black
                                                        uppercase
                                                        tracking-wide
                                                        text-[#6b1d14]
                                                        transition
                                                        hover:bg-white
                                                        disabled:cursor-not-allowed
                                                    "
                                                >
                                                    Limpar
                                                </button>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- LISTA --}}
                                    <div
                                        class="
                                            mt-4
                                            max-h-[460px]
                                            overflow-y-auto
                                            rounded-xl
                                            border
                                            border-[#b08c62]/20
                                            bg-[#f4e5c8]
                                            p-2
                                        "
                                    >
                                        <div
                                            class="
                                                grid
                                                gap-1.5
                                                lg:grid-cols-2
                                            "
                                        >
                                            @forelse ($sources as $source)
                                                @php
                                                    $displayName =
                                                        $source->name
                                                        ?: $source->source_code;

                                                    $authors =
                                                        collect(
                                                            $source->authors
                                                            ?? []
                                                        )
                                                            ->filter()
                                                            ->join(', ');

                                                    $searchText =
                                                        mb_strtolower(
                                                            $source->source_code
                                                            .' '
                                                            .$displayName
                                                            .' '
                                                            .$authors,
                                                            'UTF-8'
                                                        );
                                                @endphp

                                                <label
                                                    data-homebrew-source
                                                    x-show="matches(@js($searchText))"
                                                    x-cloak
                                                    class="
                                                        flex
                                                        cursor-pointer
                                                        items-start
                                                        gap-3
                                                        rounded-lg
                                                        border
                                                        border-transparent
                                                        bg-[#fff8e9]/65
                                                        px-3
                                                        py-2.5
                                                        transition
                                                        hover:border-[#8c6042]/25
                                                        hover:bg-[#fffaf0]
                                                    "
                                                >
                                                    <input
                                                        type="checkbox"
                                                        name="source_ids[]"
                                                        value="{{ $source->id }}"
                                                        @checked(
                                                            in_array(
                                                                $source->id,
                                                                $enabledSourceIds,
                                                                true
                                                            )
                                                        )
                                                        :disabled="allowAll"
                                                        class="
                                                            mt-1
                                                            h-4
                                                            w-4
                                                            rounded
                                                            border-[#8e6f50]
                                                            text-[#702b20]
                                                            focus:ring-[#8b4437]
                                                        "
                                                    >

                                                    <span
                                                        class="
                                                            min-w-0
                                                            flex-1
                                                        "
                                                    >
                                                        <span
                                                            class="
                                                                flex
                                                                min-w-0
                                                                items-start
                                                                justify-between
                                                                gap-3
                                                            "
                                                        >
                                                            <span class="min-w-0">
                                                                <strong
                                                                    class="
                                                                        block
                                                                        truncate
                                                                        text-[12px]
                                                                        font-black
                                                                        leading-4
                                                                        text-[#4f1c16]
                                                                    "
                                                                >
                                                                    {{ $displayName }}
                                                                </strong>

                                                                <span
                                                                    class="
                                                                        mt-0.5
                                                                        block
                                                                        truncate
                                                                        font-mono
                                                                        text-[8px]
                                                                        font-bold
                                                                        uppercase
                                                                        tracking-[0.06em]
                                                                        text-[#9a7050]
                                                                    "
                                                                >
                                                                    {{ $source->source_code }}
                                                                </span>
                                                            </span>

                                                            <span
                                                                class="
                                                                    shrink-0
                                                                    rounded-full
                                                                    bg-[#6c281c]/8
                                                                    px-2
                                                                    py-1
                                                                    text-[8px]
                                                                    font-black
                                                                    text-[#70483a]
                                                                "
                                                            >
                                                                {{ number_format($source->spell_count, 0, ',', '.') }}
                                                            </span>
                                                        </span>

                                                        @if ($authors !== '')
                                                            <span
                                                                class="
                                                                    mt-1
                                                                    block
                                                                    truncate
                                                                    text-[9px]
                                                                    text-[#846c57]
                                                                "
                                                            >
                                                                {{ $authors }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                </label>
                                            @empty
                                                <div
                                                    class="
                                                        col-span-full
                                                        px-5
                                                        py-8
                                                        text-center
                                                        text-sm
                                                        text-[#725b49]
                                                    "
                                                >
                                                    Nenhuma fonte homebrew cadastrada.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="
                                        mt-5
                                        flex
                                        flex-col
                                        gap-2
                                        border-t
                                        border-[#b08c62]/20
                                        pt-4
                                        sm:flex-row
                                        sm:items-center
                                        sm:justify-between
                                    "
                                >
                                    <p
                                        class="
                                            text-[10px]
                                            leading-4
                                            text-[#806751]
                                        "
                                    >
                                        O catálogo padrão nunca é desativado.
                                        Esta configuração afeta somente conteúdo homebrew.
                                    </p>

                                    <button
                                        type="submit"
                                        class="
                                            shrink-0
                                            rounded-xl
                                            bg-[#6b1d14]
                                            px-5
                                            py-2.5
                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]
                                            text-[#fffaf2]
                                            transition
                                            hover:bg-[#53150f]
                                        "
                                    >
                                        Salvar regras
                                    </button>
                                </div>
                            </form>
                        </article>
                    </div>
                </section>


                {{-- ========================================================
                     ZONA DE PERIGO
                ========================================================= --}}
                <section
                    x-show="section === 'danger'"
                    x-cloak
                >
                    <div
                        class="
                            border-b
                            border-red-900/20
                            bg-[#e7c6ae]
                            px-5
                            py-4
                            sm:px-6
                        "
                    >
                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.18em]
                                text-[#9b3a31]
                            "
                        >
                            Administração
                        </p>

                        <h2
                            class="
                                mt-1
                                font-serif
                                text-xl
                                font-black
                                text-[#681c16]
                            "
                        >
                            Zona de perigo
                        </h2>

                        <p
                            class="
                                mt-1
                                text-sm
                                text-[#765047]
                            "
                        >
                            Ações sensíveis ou irreversíveis da campanha.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div
                            class="
                                flex
                                flex-col
                                gap-4
                                rounded-2xl
                                border
                                border-red-800/25
                                bg-red-50/40
                                p-5
                                sm:flex-row
                                sm:items-center
                                sm:justify-between
                            "
                        >
                            <div>
                                <h3
                                    class="
                                        font-serif
                                        text-lg
                                        font-black
                                        text-[#681c16]
                                    "
                                >
                                    Excluir campanha
                                </h3>

                                <p
                                    class="
                                        mt-1
                                        max-w-xl
                                        text-sm
                                        leading-5
                                        text-[#775049]
                                    "
                                >
                                    Abre a etapa dedicada de confirmação antes
                                    de qualquer remoção.
                                </p>
                            </div>

                            <a
                                href="{{ route('campaigns.destroy.confirm', $campaign) }}"
                                class="
                                    inline-flex
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    border
                                    border-red-800/30
                                    bg-[#81261f]
                                    px-4
                                    py-2.5
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.11em]
                                    text-white
                                    transition
                                    hover:bg-[#982e25]
                                "
                            >
                                Excluir campanha
                            </a>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</x-app-layout>