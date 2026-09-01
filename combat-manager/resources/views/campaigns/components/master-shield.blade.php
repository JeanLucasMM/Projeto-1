<section
    x-data="{
        liveUrl:
            @js(
                route(
                    'campaigns.master.live',
                    $campaign
                )
            ),

        players:
            [],

        loading:
            false,

        timer:
            null,

        drawer:
            null,

        messageTarget:
            null,

        init() {
            this.refresh();

            this.timer =
                window.setInterval(
                    () => {
                        if (!document.hidden) {
                            this.refresh();
                        }
                    },
                    1200
                );
        },

        destroy() {
            if (this.timer) {
                window.clearInterval(
                    this.timer
                );
            }
        },

        async refresh() {
            if (this.loading) {
                return;
            }

            this.loading =
                true;

            try {
                const response =
                    await fetch(
                        this.liveUrl,
                        {
                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    );

                const data =
                    await response
                        .json()
                        .catch(
                            () => ({})
                        );

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ??
                        'Não foi possível atualizar o Escudo.'
                    );
                }

                this.players =
                    Array.isArray(
                        data?.players
                    )
                        ? data.players
                        : [];
            } catch (error) {
                console.error(
                    error
                );
            } finally {
                this.loading =
                    false;
            }
        },

        openDrawer(name) {
            this.drawer =
                name;
        },

        closeDrawer() {
            this.drawer =
                null;

            this.messageTarget =
                null;
        },

        openMessage(player) {
            this.messageTarget =
                player;

            this.drawer =
                'messages';
        },

        hpStateLabel(character) {
            switch (
                character?.health_state
            ) {
                case 'down':
                    return 'Caído';

                case 'critical':
                    return 'Crítico';

                case 'wounded':
                    return 'Ferido';

                default:
                    return 'Estável';
            }
        },

        initiativeLabel(value) {
            const number =
                Number(value || 0);

            return number >= 0
                ? `+${number}`
                : `${number}`;
        },
    }"
    class="
        mt-5
    "
>
    {{-- ================================================================
         BARRA DO ESCUDO
    ================================================================= --}}
    <header
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
            <p
                class="
                    text-[8px]
                    font-black
                    uppercase
                    tracking-[0.18em]
                    text-[#8c6239]
                "
            >
                Sessão
            </p>

            <div
                class="
                    mt-0.5
                    flex
                    items-center
                    gap-2
                "
            >
                <h2
                    class="
                        font-serif
                        text-2xl
                        font-black
                        text-[#53150f]
                    "
                >
                    Escudo do Mestre
                </h2>

                <span
                    class="
                        h-2
                        w-2
                        rounded-full
                        bg-emerald-600
                    "
                    :class="
                        loading
                            ? 'animate-pulse'
                            : ''
                    "
                    title="Estado sincronizado"
                ></span>
            </div>

            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#7d604d]
                "
            >
                Estado dos personagens que estão na Party neste momento.
            </p>
        </div>


        <div
            class="
                flex
                flex-wrap
                gap-1.5
            "
        >
            <button
                type="button"
                x-on:click="
                    openDrawer(
                        'sheets'
                    )
                "
                class="
                    rounded-xl
                    border
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-3.5
                    py-2.5
                    text-[8px]
                    font-black
                    uppercase
                    tracking-[0.10em]
                    text-[#6b1d14]
                    shadow-sm
                    transition
                    hover:bg-[#eadbc8]
                "
            >
                Fichas
            </button>

            <button
                type="button"
                x-on:click="
                    openDrawer(
                        'combats'
                    )
                "
                class="
                    rounded-xl
                    border
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-3.5
                    py-2.5
                    text-[8px]
                    font-black
                    uppercase
                    tracking-[0.10em]
                    text-[#6b1d14]
                    shadow-sm
                    transition
                    hover:bg-[#eadbc8]
                "
            >
                Combates
            </button>

            <button
                type="button"
                x-on:click="
                    openDrawer(
                        'notes'
                    )
                "
                class="
                    rounded-xl
                    border
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-3.5
                    py-2.5
                    text-[8px]
                    font-black
                    uppercase
                    tracking-[0.10em]
                    text-[#6b1d14]
                    shadow-sm
                    transition
                    hover:bg-[#eadbc8]
                "
            >
                Anotações
            </button>

            <button
                type="button"
                x-on:click="
                    openDrawer(
                        'loot'
                    )
                "
                class="
                    rounded-xl
                    border
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-3.5
                    py-2.5
                    text-[8px]
                    font-black
                    uppercase
                    tracking-[0.10em]
                    text-[#6b1d14]
                    shadow-sm
                    transition
                    hover:bg-[#eadbc8]
                "
            >
                Espólios
            </button>

            <button
                type="button"
                x-on:click="
                    openDrawer(
                        'messages'
                    )
                "
                class="
                    rounded-xl
                    bg-[#6b1d14]
                    px-3.5
                    py-2.5
                    text-[8px]
                    font-black
                    uppercase
                    tracking-[0.10em]
                    text-[#fffaf2]
                    shadow-sm
                    transition
                    hover:bg-[#53150f]
                "
            >
                Mensagens
            </button>
        </div>
    </header>


    {{-- ================================================================
         PAINEL AO VIVO
    ================================================================= --}}
    <div
        class="
            mt-4
            rounded-[24px]
            border
            border-[#b08c62]/38
            bg-[#ded5c9]
            p-3
            shadow-[0_12px_32px_rgba(63,43,31,.08)]
            sm:p-4
        "
    >
        <div
            x-show="
                players.length
                ===
                0
                &&
                loading
            "
            class="
                grid
                gap-3
                md:grid-cols-2
                2xl:grid-cols-3
            "
        >
            <template
                x-for="
                    index
                    in
                    3
                "
                :key="
                    `shield-loading-${index}`
                "
            >
                <div
                    class="
                        h-[350px]
                        animate-pulse
                        rounded-2xl
                        border
                        border-[#cdbb9f]/50
                        bg-[#fffdf8]/55
                    "
                ></div>
            </template>
        </div>


        <div
            x-show="
                players.length
                >
                0
            "
            class="
                grid
                gap-3
                md:grid-cols-2
                2xl:grid-cols-3
            "
        >
            <template
                x-for="
                    player
                    in
                    players
                "
                :key="
                    player.user_id
                "
            >
                <article
                    class="
                        overflow-hidden
                        rounded-2xl
                        border
                        border-[#b69d7e]/58
                        bg-[#fffdf8]
                        shadow-sm
                    "
                >
                    <div
                        class="
                            h-1
                            bg-[#6b1d14]
                        "
                    ></div>


                    <template
                        x-if="
                            player.character
                        "
                    >
                        <div>
                            <header
                                class="
                                    flex
                                    items-start
                                    gap-3
                                    border-b
                                    border-[#d8c7ab]/48
                                    bg-[linear-gradient(135deg,#fffdf8_0%,#f5ecdf_100%)]
                                    px-4
                                    py-4
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-16
                                        w-16
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-xl
                                        border
                                        border-[#cdbb9f]
                                        bg-[#eadbc8]
                                        font-serif
                                        text-2xl
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    <template
                                        x-if="
                                            player
                                                .character
                                                .image_url
                                        "
                                    >
                                        <img
                                            :src="
                                                player
                                                    .character
                                                    .image_url
                                            "
                                            :alt="
                                                player
                                                    .character
                                                    .name
                                            "
                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                                object-top
                                            "
                                        >
                                    </template>

                                    <template
                                        x-if="
                                            !player
                                                .character
                                                .image_url
                                        "
                                    >
                                        <span
                                            x-text="
                                                player
                                                    .character
                                                    .name
                                                    ?.charAt(0)
                                                    ?.toUpperCase()
                                                ?? '?'
                                            "
                                        ></span>
                                    </template>
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
                                            <p
                                                class="
                                                    truncate
                                                    text-[7px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.14em]
                                                    text-[#8c6239]
                                                "
                                                x-text="
                                                    player
                                                        .player_name
                                                "
                                            ></p>

                                            <h3
                                                class="
                                                    mt-0.5
                                                    truncate
                                                    font-serif
                                                    text-lg
                                                    font-black
                                                    text-[#53150f]
                                                "
                                                x-text="
                                                    player
                                                        .character
                                                        .name
                                                "
                                            ></h3>

                                            <p
                                                class="
                                                    mt-0.5
                                                    truncate
                                                    text-[8px]
                                                    font-bold
                                                    text-[#8c6239]
                                                "
                                            >
                                                <span
                                                    x-text="
                                                        player
                                                            .character
                                                            .class_name
                                                    "
                                                ></span>

                                                <span>
                                                    · Nv.
                                                </span>

                                                <span
                                                    x-text="
                                                        player
                                                            .character
                                                            .level
                                                    "
                                                ></span>
                                            </p>
                                        </div>

                                        <span
                                            class="
                                                shrink-0
                                                rounded-full
                                                px-2
                                                py-1
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-wider
                                            "
                                            :class="{
                                                'bg-emerald-100 text-emerald-800':
                                                    player.character.health_state === 'healthy',

                                                'bg-amber-100 text-amber-800':
                                                    player.character.health_state === 'wounded',

                                                'bg-red-100 text-red-800':
                                                    player.character.health_state === 'critical',

                                                'bg-[#53150f] text-[#fffaf2]':
                                                    player.character.health_state === 'down',
                                            }"
                                            x-text="
                                                hpStateLabel(
                                                    player.character
                                                )
                                            "
                                        ></span>
                                    </div>
                                </div>
                            </header>


                            <div
                                class="
                                    px-4
                                    py-4
                                "
                            >
                                {{-- PV / TEMP --}}
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
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-[0.14em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Pontos de Vida
                                        </p>

                                        <div
                                            class="
                                                mt-0.5
                                                flex
                                                items-baseline
                                                gap-1.5
                                            "
                                        >
                                            <strong
                                                class="
                                                    font-serif
                                                    text-3xl
                                                    font-black
                                                    leading-none
                                                    text-[#53150f]
                                                "
                                                x-text="
                                                    player
                                                        .character
                                                        .current_hp
                                                "
                                            ></strong>

                                            <span
                                                class="
                                                    text-[10px]
                                                    font-black
                                                    text-[#9a806d]
                                                "
                                            >
                                                /
                                            </span>

                                            <span
                                                class="
                                                    font-serif
                                                    text-base
                                                    font-black
                                                    text-[#7d604d]
                                                "
                                                x-text="
                                                    player
                                                        .character
                                                        .max_hp
                                                "
                                            ></span>
                                        </div>
                                    </div>

                                    <div
                                        class="
                                            text-right
                                        "
                                    >
                                        <p
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-[0.12em]
                                                text-[#8c6239]
                                            "
                                        >
                                            PV Temporário
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                font-serif
                                                text-xl
                                                font-black
                                            "
                                            :class="
                                                player.character.temporary_hp > 0
                                                    ? 'text-sky-700'
                                                    : 'text-[#b8a698]'
                                            "
                                            x-text="
                                                player
                                                    .character
                                                    .temporary_hp
                                            "
                                        ></p>
                                    </div>
                                </div>

                                <div
                                    class="
                                        mt-2
                                        h-2.5
                                        overflow-hidden
                                        rounded-full
                                        bg-[#ddd4c7]
                                    "
                                >
                                    <div
                                        class="
                                            h-full
                                            rounded-full
                                            transition-all
                                            duration-300
                                        "
                                        :class="{
                                            'bg-emerald-600':
                                                player.character.hp_percent > 50,

                                            'bg-amber-500':
                                                player.character.hp_percent <= 50
                                                &&
                                                player.character.hp_percent > 25,

                                            'bg-red-600':
                                                player.character.hp_percent <= 25,
                                        }"
                                        :style="
                                            `width: ${player.character.hp_percent}%`
                                        "
                                    ></div>
                                </div>


                                {{-- CA / INIT / MOV --}}
                                <div
                                    class="
                                        mt-4
                                        grid
                                        grid-cols-3
                                        divide-x
                                        divide-[#d8c7ab]/45
                                        rounded-xl
                                        border
                                        border-[#d8c7ab]/55
                                        bg-[#faf7f1]
                                    "
                                >
                                    <div
                                        class="
                                            px-2
                                            py-2.5
                                            text-center
                                        "
                                    >
                                        <p
                                            class="
                                                text-[6px]
                                                font-black
                                                uppercase
                                                tracking-wider
                                                text-[#9a806d]
                                            "
                                        >
                                            CA
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                font-serif
                                                text-base
                                                font-black
                                                text-[#53150f]
                                            "
                                            x-text="
                                                player
                                                    .character
                                                    .armor_class
                                            "
                                        ></p>
                                    </div>

                                    <div
                                        class="
                                            px-2
                                            py-2.5
                                            text-center
                                        "
                                    >
                                        <p
                                            class="
                                                text-[6px]
                                                font-black
                                                uppercase
                                                tracking-wider
                                                text-[#9a806d]
                                            "
                                        >
                                            Iniciativa
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                font-serif
                                                text-base
                                                font-black
                                                text-[#53150f]
                                            "
                                            x-text="
                                                initiativeLabel(
                                                    player
                                                        .character
                                                        .initiative_bonus
                                                )
                                            "
                                        ></p>
                                    </div>

                                    <div
                                        class="
                                            px-2
                                            py-2.5
                                            text-center
                                        "
                                    >
                                        <p
                                            class="
                                                text-[6px]
                                                font-black
                                                uppercase
                                                tracking-wider
                                                text-[#9a806d]
                                            "
                                        >
                                            Movimento
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                font-serif
                                                text-base
                                                font-black
                                                text-[#53150f]
                                            "
                                        >
                                            <span
                                                x-text="
                                                    player
                                                        .character
                                                        .speed
                                                "
                                            ></span>

                                            <span
                                                class="
                                                    text-[8px]
                                                "
                                            >
                                                ft
                                            </span>
                                        </p>
                                    </div>
                                </div>


                                {{-- EXAUSTÃO / CONCENTRAÇÃO --}}
                                <div
                                    class="
                                        mt-4
                                        flex
                                        items-center
                                        justify-between
                                        gap-3
                                    "
                                >
                                    <div>
                                        <p
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-[0.12em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Exaustão
                                        </p>

                                        <template
                                            x-if="
                                                player
                                                    .character
                                                    .exhaustion_enabled
                                            "
                                        >
                                            <div
                                                class="
                                                    mt-1.5
                                                    flex
                                                    gap-1
                                                "
                                            >
                                                <template
                                                    x-for="
                                                        level
                                                        in
                                                        6
                                                    "
                                                    :key="
                                                        `ex-${player.user_id}-${level}`
                                                    "
                                                >
                                                    <span
                                                        class="
                                                            h-2.5
                                                            w-2.5
                                                            rounded-full
                                                            border
                                                        "
                                                        :class="
                                                            level
                                                            <=
                                                            player.character.exhaustion
                                                                ? 'border-[#6b1d14] bg-[#6b1d14]'
                                                                : 'border-[#cdbb9f] bg-transparent'
                                                        "
                                                    ></span>
                                                </template>
                                            </div>
                                        </template>

                                        <template
                                            x-if="
                                                !player
                                                    .character
                                                    .exhaustion_enabled
                                            "
                                        >
                                            <p
                                                class="
                                                    mt-1
                                                    text-[8px]
                                                    italic
                                                    text-[#aa9584]
                                                "
                                            >
                                                Regra desativada
                                            </p>
                                        </template>
                                    </div>

                                    <div
                                        x-show="
                                            player
                                                .character
                                                .concentration_active
                                        "
                                        x-cloak
                                        class="
                                            rounded-lg
                                            border
                                            border-[#cdbb9f]/60
                                            bg-[#f3eadf]
                                            px-2
                                            py-1.5
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#6b1d14]
                                        "
                                    >
                                        Concentração
                                    </div>
                                </div>


                                {{-- CONDIÇÕES --}}
                                <div
                                    x-show="
                                        player
                                            .character
                                            .conditions
                                            ?.length
                                        >
                                        0
                                    "
                                    x-cloak
                                    class="
                                        mt-4
                                    "
                                >
                                    <p
                                        class="
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Condições
                                    </p>

                                    <div
                                        class="
                                            mt-1.5
                                            flex
                                            flex-wrap
                                            gap-1
                                        "
                                    >
                                        <template
                                            x-for="
                                                condition
                                                in
                                                player.character.conditions
                                            "
                                            :key="
                                                `${player.user_id}-${condition}`
                                            "
                                        >
                                            <span
                                                class="
                                                    rounded-md
                                                    border
                                                    border-[#d6c1a7]
                                                    bg-[#f2e7d7]
                                                    px-2
                                                    py-1
                                                    text-[7px]
                                                    font-black
                                                    text-[#6b1d14]
                                                "
                                                x-text="
                                                    condition
                                                "
                                            ></span>
                                        </template>
                                    </div>
                                </div>


                                {{-- ROLAMENTOS --}}
                                <div
                                    class="
                                        mt-4
                                        rounded-xl
                                        border
                                        border-dashed
                                        border-[#cdbb9f]/75
                                        bg-[#fbf8f1]
                                        px-3
                                        py-2.5
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            items-center
                                            justify-between
                                            gap-2
                                        "
                                    >
                                        <p
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-[0.12em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Último rolamento
                                        </p>

                                        <span
                                            class="
                                                text-[7px]
                                                text-[#ad9989]
                                            "
                                        >
                                            em breve
                                        </span>
                                    </div>

                                    <p
                                        class="
                                            mt-1
                                            font-serif
                                            text-[12px]
                                            font-black
                                            text-[#9a806d]
                                        "
                                    >
                                        —
                                    </p>
                                </div>
                            </div>


                            <footer
                                class="
                                    flex
                                    gap-2
                                    border-t
                                    border-[#d8c7ab]/48
                                    bg-[#faf7f1]
                                    px-4
                                    py-3
                                "
                            >
                                <a
                                    :href="
                                        player
                                            .character
                                            .sheet_url
                                    "
                                    class="
                                        flex-1
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/65
                                        bg-[#fffdf8]
                                        px-3
                                        py-2
                                        text-center
                                        text-[7px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#6b1d14]
                                        transition
                                        hover:bg-[#eadbc8]
                                    "
                                >
                                    Abrir ficha
                                </a>

                                <button
                                    type="button"
                                    x-on:click="
                                        openMessage(
                                            player
                                        )
                                    "
                                    class="
                                        flex-1
                                        rounded-lg
                                        bg-[#6b1d14]
                                        px-3
                                        py-2
                                        text-[7px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#fffaf2]
                                        transition
                                        hover:bg-[#53150f]
                                    "
                                >
                                    Mensagem secreta
                                </button>
                            </footer>
                        </div>
                    </template>


                    <template
                        x-if="
                            !player.character
                        "
                    >
                        <div
                            class="
                                flex
                                min-h-[300px]
                                flex-col
                                items-center
                                justify-center
                                px-5
                                py-8
                                text-center
                            "
                        >
                            <div
                                class="
                                    flex
                                    h-12
                                    w-12
                                    items-center
                                    justify-center
                                    rounded-full
                                    border
                                    border-[#cdbb9f]
                                    bg-[#eadbc8]/55
                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#8c6239]
                                "
                                x-text="
                                    player
                                        .player_name
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                    ?? '?'
                                "
                            ></div>

                            <h3
                                class="
                                    mt-3
                                    font-serif
                                    text-base
                                    font-black
                                    text-[#53150f]
                                "
                                x-text="
                                    player
                                        .player_name
                                "
                            ></h3>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    text-[#8c6239]
                                "
                            >
                                Nenhum personagem está em jogo.
                            </p>

                            <p
                                x-show="
                                    player.resting_count
                                    >
                                    0
                                "
                                class="
                                    mt-2
                                    text-[7px]
                                    font-black
                                    uppercase
                                    tracking-wider
                                    text-[#a38d7c]
                                "
                            >
                                <span
                                    x-text="
                                        player
                                            .resting_count
                                    "
                                ></span>

                                descansando
                            </p>
                        </div>
                    </template>
                </article>
            </template>
        </div>


        <div
            x-show="
                players.length
                ===
                0
                &&
                !loading
            "
            class="
                rounded-2xl
                border
                border-dashed
                border-[#cdbb9f]
                bg-[#fffdf8]/65
                px-6
                py-12
                text-center
            "
        >
            <p
                class="
                    font-serif
                    text-lg
                    font-black
                    text-[#53150f]
                "
            >
                O Escudo ainda está vazio
            </p>

            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#7d604d]
                "
            >
                Os Players e seus personagens ativos aparecerão aqui.
            </p>
        </div>
    </div>


    {{-- ================================================================
         GAVETAS
    ================================================================= --}}
    <div
        x-show="
            drawer
        "
        x-cloak
        class="
            fixed
            inset-0
            z-[170]
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
                closeDrawer()
            "
        ></div>


        <aside
            x-on:click.stop
            x-transition:enter="
                transition
                ease-out
                duration-200
            "
            x-transition:enter-start="
                translate-x-full
            "
            x-transition:enter-end="
                translate-x-0
            "
            x-transition:leave="
                transition
                ease-in
                duration-150
            "
            x-transition:leave-start="
                translate-x-0
            "
            x-transition:leave-end="
                translate-x-full
            "
            class="
                absolute
                bottom-0
                right-0
                top-0
                z-10
                flex
                w-full
                max-w-2xl
                flex-col
                border-l
                border-[#cdbb9f]
                bg-[#f4f1e8]
                shadow-2xl
            "
        >
            <header
                class="
                    flex
                    shrink-0
                    items-center
                    justify-between
                    gap-4
                    border-b
                    border-[#cdbb9f]/65
                    bg-[#eadbc8]
                    px-5
                    py-4
                "
            >
                <div>
                    <p
                        class="
                            text-[7px]
                            font-black
                            uppercase
                            tracking-[0.16em]
                            text-[#8c6239]
                        "
                    >
                        Escudo do Mestre
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
                        <span
                            x-show="
                                drawer
                                ===
                                'sheets'
                            "
                        >
                            Fichas da Campanha
                        </span>

                        <span
                            x-show="
                                drawer
                                ===
                                'combats'
                            "
                        >
                            Combates
                        </span>

                        <span
                            x-show="
                                drawer
                                ===
                                'notes'
                            "
                        >
                            Anotações Rápidas
                        </span>

                        <span
                            x-show="
                                drawer
                                ===
                                'loot'
                            "
                        >
                            Espólios
                        </span>

                        <span
                            x-show="
                                drawer
                                ===
                                'messages'
                            "
                        >
                            Mensagens Secretas
                        </span>
                    </h2>
                </div>

                <button
                    type="button"
                    x-on:click="
                        closeDrawer()
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
                        hover:bg-[#dbc7ae]
                        hover:text-[#53150f]
                    "
                >
                    ×
                </button>
            </header>


            <div
                class="
                    min-h-0
                    flex-1
                    overflow-y-auto
                    p-5
                "
            >
                {{-- --------------------------------------------------------
                     FICHAS
                --------------------------------------------------------- --}}
                <div
                    x-show="
                        drawer
                        ===
                        'sheets'
                    "
                >
                    <div
                        class="
                            space-y-5
                        "
                    >
                        @foreach($campaign->members as $member)

                            @php
                                $memberCharacters =
                                    $campaign
                                        ->characters
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
                            @endphp

                            <section>
                                <div
                                    class="
                                        mb-2
                                        flex
                                        items-center
                                        gap-2
                                    "
                                >
                                    <h3
                                        class="
                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]
                                            text-[#53150f]
                                        "
                                    >
                                        {{ $member->user->name }}
                                    </h3>

                                    <span
                                        class="
                                            h-px
                                            flex-1
                                            bg-[#cdbb9f]/65
                                        "
                                    ></span>
                                </div>

                                <div
                                    class="
                                        grid
                                        gap-2
                                        sm:grid-cols-2
                                    "
                                >
                                    @forelse($memberCharacters as $character)
                                        <a
                                            href="{{ route('characters.show', $character) }}"
                                            class="
                                                flex
                                                items-center
                                                gap-3
                                                rounded-xl
                                                border
                                                p-3
                                                transition
                                                {{
                                                    $character->pivot->is_active
                                                        ? 'border-[#b08c62]/60 bg-[#fffdf8] shadow-sm'
                                                        : 'border-[#d8cec0]/60 bg-[#eee9e1] opacity-60 grayscale-[40%] hover:opacity-95 hover:grayscale-0'
                                                }}
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
                                                    overflow-hidden
                                                    rounded-lg
                                                    border
                                                    border-[#cdbb9f]
                                                    bg-[#eadbc8]
                                                    font-serif
                                                    text-lg
                                                    font-black
                                                    text-[#53150f]
                                                "
                                            >
                                                @if($character->image_path)
                                                    <img
                                                        src="{{ route('campaigns.master.characters.image', [$campaign, $character]) }}"
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
                                                <p
                                                    class="
                                                        truncate
                                                        font-serif
                                                        text-[13px]
                                                        font-black
                                                        text-[#53150f]
                                                    "
                                                >
                                                    {{ $character->name }}
                                                </p>

                                                <p
                                                    class="
                                                        mt-0.5
                                                        text-[7px]
                                                        font-black
                                                        uppercase
                                                        tracking-wider
                                                        {{
                                                            $character->pivot->is_active
                                                                ? 'text-emerald-800'
                                                                : 'text-[#8c6239]'
                                                        }}
                                                    "
                                                >
                                                    {{
                                                        $character->pivot->is_active
                                                            ? 'Jogando agora'
                                                            : 'Descansando'
                                                    }}
                                                </p>
                                            </div>
                                        </a>
                                    @empty
                                        <p
                                            class="
                                                col-span-full
                                                rounded-xl
                                                border
                                                border-dashed
                                                border-[#cdbb9f]
                                                px-4
                                                py-6
                                                text-center
                                                text-[9px]
                                                text-[#8c6239]
                                            "
                                        >
                                            Nenhuma ficha compartilhada.
                                        </p>
                                    @endforelse
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>


                {{-- --------------------------------------------------------
                     COMBATES
                --------------------------------------------------------- --}}
                <div
                    x-show="
                        drawer
                        ===
                        'combats'
                    "
                >
                    <div
                        class="
                            space-y-2
                        "
                    >
                        @forelse($campaignCombats as $combat)
                            <a
                                href="{{ route('combats.show', $combat) }}"
                                class="
                                    block
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]/60
                                    bg-[#fffdf8]
                                    p-4
                                    transition
                                    hover:border-[#b08c62]
                                    hover:shadow-sm
                                "
                            >
                                <div
                                    class="
                                        flex
                                        items-start
                                        justify-between
                                        gap-3
                                    "
                                >
                                    <div>
                                        <p
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-[0.14em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Encontro
                                        </p>

                                        <h3
                                            class="
                                                mt-1
                                                font-serif
                                                text-base
                                                font-black
                                                text-[#53150f]
                                            "
                                        >
                                            {{ $combat->name }}
                                        </h3>
                                    </div>

                                    <span
                                        class="
                                            rounded-full
                                            px-2
                                            py-1
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            {{
                                                $combat->is_active
                                                    ? 'bg-emerald-100 text-emerald-800'
                                                    : 'bg-[#eee9e1] text-[#7d604d]'
                                            }}
                                        "
                                    >
                                        {{
                                            $combat->is_active
                                                ? 'Em andamento'
                                                : (
                                                    (int) $combat->current_round > 0
                                                        ? 'Pausado'
                                                        : 'Planejado'
                                                )
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="
                                        mt-3
                                        flex
                                        gap-3
                                        text-[8px]
                                        font-black
                                        text-[#8c6239]
                                    "
                                >
                                    <span>
                                        Rodada {{ (int) $combat->current_round }}
                                    </span>

                                    <span>
                                        {{ (int) $combat->players_count }} Players
                                    </span>

                                    <span>
                                        {{ (int) $combat->npcs_count }} NPCs
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-dashed
                                    border-[#cdbb9f]
                                    px-5
                                    py-10
                                    text-center
                                "
                            >
                                <p
                                    class="
                                        font-serif
                                        text-base
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    Nenhum combate planejado
                                </p>

                                <a
                                    href="{{ route('combats.index') }}"
                                    class="
                                        mt-3
                                        inline-flex
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#6b1d14]
                                        hover:underline
                                    "
                                >
                                    Ir para Combates →
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>


                {{-- --------------------------------------------------------
                     ANOTAÇÕES
                --------------------------------------------------------- --}}
                <div
                    x-show="
                        drawer
                        ===
                        'notes'
                    "
                >
                    <div
                        class="
                            rounded-2xl
                            border
                            border-[#cdbb9f]/60
                            bg-[#fffdf8]
                            p-5
                        "
                    >
                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            Bloco privado do Mestre
                        </p>

                        <textarea
                            disabled
                            rows="14"
                            placeholder="A persistência das anotações entra na próxima etapa."
                            class="
                                mt-3
                                w-full
                                resize-none
                                rounded-xl
                                border
                                border-[#cdbb9f]
                                bg-[#fbf8f1]
                                p-4
                                font-serif
                                text-sm
                                leading-6
                                text-[#53150f]
                                placeholder:text-[#9f8a78]
                            "
                        ></textarea>
                    </div>
                </div>


                {{-- --------------------------------------------------------
                     ESPÓLIOS
                --------------------------------------------------------- --}}
                <div
                    x-show="
                        drawer
                        ===
                        'loot'
                    "
                >
                    <div
                        class="
                            space-y-4
                        "
                    >
                        <section
                            class="
                                rounded-2xl
                                border
                                border-[#cdbb9f]/60
                                bg-[#fffdf8]
                                p-5
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
                                    <p
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Descobertas
                                    </p>

                                    <h3
                                        class="
                                            mt-0.5
                                            font-serif
                                            text-lg
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Itens Encontrados
                                    </h3>
                                </div>

                                <button
                                    type="button"
                                    disabled
                                    class="
                                        cursor-not-allowed
                                        rounded-lg
                                        bg-[#6b1d14]/50
                                        px-3
                                        py-2
                                        text-[7px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#fffaf2]
                                    "
                                >
                                    + Criar item
                                </button>
                            </div>

                            <div
                                class="
                                    mt-4
                                    rounded-xl
                                    border
                                    border-dashed
                                    border-[#cdbb9f]
                                    px-5
                                    py-8
                                    text-center
                                "
                            >
                                <p
                                    class="
                                        font-serif
                                        text-base
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    Baú compartilhado da Party
                                </p>

                                <p
                                    class="
                                        mx-auto
                                        mt-1
                                        max-w-md
                                        text-[9px]
                                        leading-5
                                        text-[#8c6239]
                                    "
                                >
                                    Os itens liberados aqui serão os mesmos exibidos no modal “Itens Encontrados” do inventário dos Players.
                                </p>
                            </div>
                        </section>


                        <section
                            class="
                                rounded-2xl
                                border
                                border-[#cdbb9f]/60
                                bg-[#fffdf8]
                                p-5
                            "
                        >
                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#8c6239]
                                "
                            >
                                Tesouro
                            </p>

                            <h3
                                class="
                                    mt-0.5
                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Moedas Encontradas
                            </h3>

                            <div
                                class="
                                    mt-4
                                    grid
                                    grid-cols-5
                                    divide-x
                                    divide-[#cdbb9f]/45
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]/55
                                    bg-[#fbf8f1]
                                "
                            >
                                @foreach(['PC', 'PP', 'PE', 'PO', 'PL'] as $coin)
                                    <div
                                        class="
                                            px-2
                                            py-3
                                            text-center
                                        "
                                    >
                                        <p
                                            class="
                                                text-[7px]
                                                font-black
                                                text-[#8c6239]
                                            "
                                        >
                                            {{ $coin }}
                                        </p>

                                        <p
                                            class="
                                                mt-1
                                                font-serif
                                                text-lg
                                                font-black
                                                text-[#9f8a78]
                                            "
                                        >
                                            0
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            <p
                                class="
                                    mt-3
                                    text-[8px]
                                    leading-4
                                    text-[#8c6239]
                                "
                            >
                                Os Players decidirão livremente como dividir o dinheiro encontrado.
                            </p>
                        </section>
                    </div>
                </div>


                {{-- --------------------------------------------------------
                     MENSAGENS
                --------------------------------------------------------- --}}
                <div
                    x-show="
                        drawer
                        ===
                        'messages'
                    "
                >
                    <div
                        class="
                            rounded-2xl
                            border
                            border-[#cdbb9f]/60
                            bg-[#fffdf8]
                            p-5
                        "
                    >
                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            Conversa privada
                        </p>

                        <h3
                            class="
                                mt-1
                                font-serif
                                text-lg
                                font-black
                                text-[#53150f]
                            "
                            x-text="
                                messageTarget
                                    ? messageTarget.player_name
                                    : 'Escolha um Player'
                            "
                        ></h3>

                        <div
                            class="
                                mt-4
                                rounded-xl
                                border
                                border-dashed
                                border-[#cdbb9f]
                                px-5
                                py-8
                                text-center
                            "
                        >
                            <p
                                class="
                                    text-[9px]
                                    leading-5
                                    text-[#8c6239]
                                "
                            >
                                O histórico e o envio Mestre ↔ Player serão conectados nesta mesma gaveta.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>