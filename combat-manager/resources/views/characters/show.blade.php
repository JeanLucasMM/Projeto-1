<x-app-layout>

    <style>
        /*
        |--------------------------------------------------------------------------
        | FOLHA ÚNICA — V2 MAIS LIMPA
        |--------------------------------------------------------------------------
        |
        | Mantemos a ficha como uma peça única, mas retiramos molduras e
        | divisões redundantes no miolo.
        |
        */

        .character-sheet-hero > * {
            margin-bottom: 0 !important;

            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;

            border-bottom-color: transparent !important;

            box-shadow: none !important;
        }

        .character-sheet-body {
            position: relative;

            margin-top: -1px;

            overflow: hidden;

            border-right: 1px solid rgba(205, 187, 159, .50);
            border-bottom: 1px solid rgba(205, 187, 159, .50);
            border-left: 1px solid rgba(205, 187, 159, .50);

            border-radius:
                0
                0
                16px
                16px;

            background:
                #f8f5ee;

            box-shadow:
                0 7px 16px rgba(83, 21, 15, .05);
        }

        /*
        |--------------------------------------------------------------------------
        | ATRIBUTOS + COLUNA PRINCIPAL
        |--------------------------------------------------------------------------
        |
        | A coluna de atributos fica mais estreita sem alterar a tipografia
        | interna do componente.
        |
        | Ataques permanece compacto no topo da coluna principal e o restante
        | da ficha sobe logo abaixo. Isso elimina o grande vazio à direita.
        |
        */

        .character-sheet-main-grid {
            position: relative;

            z-index: 10;

            display: grid;

            grid-template-columns:
                minmax(0, 1fr);

            align-items: start;
        }

        .character-sheet-abilities,
        .character-sheet-main-column,
        .character-sheet-attacks {
            min-width: 0;
        }

        .character-sheet-main-column {
            display: flex;

            min-height: 0;

            flex-direction: column;
        }

        /*
        | Os componentes já possuem divisões internas suficientes.
        | Removemos apenas a moldura externa duplicada.
        */

        .character-sheet-abilities > *,
        .character-sheet-attacks > * {
            width: 100%;

            border: 0 !important;
            border-radius: 0 !important;

            box-shadow: none !important;
        }

        .character-sheet-attacks {
            align-self: start;
        }

        .character-sheet-attacks > * {
            min-height: 0 !important;
            height: auto !important;
        }

        /*
        |--------------------------------------------------------------------------
        | CONTEÚDO INFERIOR
        |--------------------------------------------------------------------------
        */

        .character-sheet-lower {
            position: relative;

            min-width: 0;

            margin-top: 10px;

            border-top: 0;
        }

        @media (min-width: 1024px) {
            .character-sheet-main-grid {
                grid-template-columns:
                    minmax(340px, 365px)
                    minmax(0, 1fr);

                column-gap: 12px;
            }

            .character-sheet-abilities {
                align-self: start;
            }

            .character-sheet-main-column {
                padding-left: 2px;
            }
        }

        @media (min-width: 1280px) {
            .character-sheet-main-grid {
                grid-template-columns:
                    370px
                    minmax(0, 1fr);

                column-gap: 14px;
            }
        }

        @media (max-width: 1023px) {
            .character-sheet-main-column {
                border-top:
                    1px solid
                    rgba(205, 187, 159, .34);
            }

            .character-sheet-lower {
                margin-top: 12px;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BOTÕES LATERAIS
        |--------------------------------------------------------------------------
        */

        .character-side-tab {
            min-width: 42px;

            border:
                1px solid
                rgba(205, 187, 159, .88);

            border-right: 0;

            background:
                #53150f;

            color:
                #f4f1e8;

            box-shadow:
                0 7px 18px
                rgba(83, 21, 15, .16);

            transition:
                background .16s ease,
                padding .16s ease,
                transform .16s ease;
        }

        .character-side-tab:hover,
        .character-side-tab.active {
            background:
                #6b1d14;
        }

        .character-side-tab:hover {
            padding-right:
                1rem;
        }

        /*
        |--------------------------------------------------------------------------
        | DRAWER COMPARTILHADO
        |--------------------------------------------------------------------------
        */

        .character-drawer {
            background:
                linear-gradient(
                    180deg,
                    #f7f3ea 0%,
                    #f4f1e8 100%
                );
        }
    </style>


    <div
        x-data="{
            drawer: null,

            openDrawer(name) {
                this.drawer = name;
            },

            closeDrawer() {
                this.drawer = null;
            }
        }"

        x-effect="
            document.body.classList.toggle(
                'overflow-hidden',
                drawer !== null
            )
        "

        @keydown.escape.window="
            closeDrawer()
        "

        class="
            min-h-full
            px-3
            py-5
            sm:px-6
            md:px-8
        "
    >

        <div
            class="
                mx-auto
                max-w-[1400px]
            "
        >

            {{-- ============================================================
                 FOLHA PRINCIPAL
            ============================================================= --}}

            <div
                class="
                    relative
                    w-full
                "
            >

                {{-- ========================================================
                     HERO
                ========================================================= --}}

                <div
                    class="
                        character-sheet-hero
                        relative
                        z-20
                        w-full
                    "
                >
                    @include(
                        'characters.components.hero-header',
                        [
                            'character' =>
                                $character,
                        ]
                    )
                </div>


                {{-- ========================================================
                     CORPO
                ========================================================= --}}

                <div
                    class="
                        character-sheet-body
                        relative
                        z-10
                    "
                >

                    {{-- ====================================================
                         CONTEÚDO PRINCIPAL
                    ===================================================== --}}
                    <div
                        class="
                            character-sheet-main-grid
                        "
                    >
                        {{-- =================================================
                             ATRIBUTOS — COLUNA MAIS ESTREITA
                        ================================================== --}}
                        <div
                            class="
                                character-sheet-abilities
                            "
                        >
                            @include(
                                'characters.components.character-abilities',
                                [
                                    'character' =>
                                        $character,
                                ]
                            )
                        </div>


                        {{-- =================================================
                             COLUNA PRINCIPAL
                        ================================================== --}}
                        <div
                            class="
                                character-sheet-main-column
                            "
                        >
                            {{-- ATAQUES --}}
                            <div
                                class="
                                    character-sheet-attacks
                                "
                            >
                                @include(
                                    'characters.components.attacks',
                                    [
                                        'character' =>
                                            $character,
                                    ]
                                )
                            </div>


                            {{-- =============================================
                                 RESTANTE DA FICHA
                            ============================================== --}}
                            <div
                                class="
                                    character-sheet-lower

                                    space-y-4

                                    px-3
                                    pb-5
                                    pt-2

                                    sm:px-4
                                "
                            >
                                @include(
                                    'characters.components.features',
                                    [
                                        'character' =>
                                            $character,
                                    ]
                                )


                                <div
                                    class="
                                        grid
                                        grid-cols-1
                                        gap-4

                                        md:grid-cols-2
                                    "
                                >
                                    @include(
                                        'characters.components.resources',
                                        [
                                            'character' =>
                                                $character,
                                        ]
                                    )

                                    @include(
                                        'characters.components.classes',
                                        [
                                            'character' =>
                                                $character,
                                        ]
                                    )
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- ================================================================
             ABAS LATERAIS
        ================================================================= --}}

        <div
            class="
                fixed
                right-0
                top-1/2
                z-40

                flex
                -translate-y-1/2
                flex-col
                gap-2
            "
        >

            {{-- INVENTÁRIO --}}

            <button
                type="button"

                @click="
                    drawer === 'inventory'
                        ? closeDrawer()
                        : openDrawer('inventory')
                "

                class="
                    character-side-tab

                    rounded-l-2xl

                    px-3
                    py-4
                "

                :class="{
                    'active':
                        drawer ===
                            'inventory'
                }"

                title="Abrir Inventário"
            >
                <div
                    class="
                        flex
                        flex-col
                        items-center
                        gap-2
                    "
                >
                    <svg
                        class="
                            h-5
                            w-5
                        "

                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M5 8.5h14v11H5v-11z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M8 8.5V6a4 4 0 018 0v2.5"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M9 13h6"
                        />
                    </svg>

                    <span
                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-[0.22em]
                        "

                        style="
                            writing-mode:
                                vertical-rl;
                        "
                    >
                        Inventário
                    </span>
                </div>
            </button>


            {{-- GRIMÓRIO --}}

            <button
                type="button"

                @click="
                    drawer === 'spellbook'
                        ? closeDrawer()
                        : openDrawer('spellbook')
                "

                class="
                    character-side-tab

                    rounded-l-2xl

                    px-3
                    py-4
                "

                :class="{
                    'active':
                        drawer ===
                            'spellbook'
                }"

                title="Abrir Grimório"
            >
                <div
                    class="
                        flex
                        flex-col
                        items-center
                        gap-2
                    "
                >
                    <svg
                        class="
                            h-5
                            w-5
                        "

                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M4 5.5A2.5 2.5 0 016.5 3H20v16H6.5A2.5 2.5 0 014 16.5v-11z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M4 16.5A2.5 2.5 0 016.5 14H20"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M8 7h7M8 10h5"
                        />
                    </svg>

                    <span
                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-[0.25em]
                        "

                        style="
                            writing-mode:
                                vertical-rl;
                        "
                    >
                        Grimório
                    </span>
                </div>
            </button>

        </div>


        {{-- ================================================================
             DRAWER COMPARTILHADO
        ================================================================= --}}

        <div
            x-show="
                drawer !== null
            "

            x-cloak

            class="
                fixed
                inset-0
                z-50
            "
        >

            {{-- BACKDROP --}}

            <div
                x-show="
                    drawer !== null
                "

                x-transition:enter="
                    transition-opacity
                    duration-200
                "

                x-transition:enter-start="
                    opacity-0
                "

                x-transition:enter-end="
                    opacity-100
                "

                x-transition:leave="
                    transition-opacity
                    duration-150
                "

                x-transition:leave-start="
                    opacity-100
                "

                x-transition:leave-end="
                    opacity-0
                "

                @click="
                    closeDrawer()
                "

                class="
                    absolute
                    inset-0
                    bg-[#2a1712]/60
                    backdrop-blur-[2px]
                "
            ></div>


            {{-- DRAWER --}}

            <aside
                x-show="
                    drawer !== null
                "

                x-transition:enter="
                    transform
                    transition
                    ease-out
                    duration-300
                "

                x-transition:enter-start="
                    translate-x-full
                "

                x-transition:enter-end="
                    translate-x-0
                "

                x-transition:leave="
                    transform
                    transition
                    ease-in
                    duration-200
                "

                x-transition:leave-start="
                    translate-x-0
                "

                x-transition:leave-end="
                    translate-x-full
                "

                class="
                    character-drawer

                    absolute
                    inset-y-0
                    right-0

                    flex
                    w-full
                    max-w-3xl
                    flex-col

                    border-l
                    border-[#cdbb9f]

                    shadow-2xl
                "
            >

                {{-- ========================================================
                     HEADER — GRIMÓRIO
                ========================================================= --}}

                <div
                    x-show="
                        drawer ===
                            'spellbook'
                    "

                    x-cloak

                    class="
                        shrink-0

                        border-b
                        border-[#cdbb9f]/60

                        bg-[#53150f]

                        px-5
                        py-5

                        text-[#f4f1e8]
                    "
                >
                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-4
                        "
                    >
                        <div>
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.3em]
                                    text-[#cdbb9f]
                                "
                            >
                                Grimório de Aventureiro
                            </p>

                            <h2
                                class="
                                    mt-1
                                    font-serif
                                    text-2xl
                                    font-black
                                "
                            >
                                Magias
                            </h2>

                            <p
                                class="
                                    mt-1
                                    text-xs
                                    text-[#eadfce]/70
                                "
                            >
                                {{ $character->name }}
                            </p>
                        </div>


                        <button
                            type="button"

                            @click="
                                closeDrawer()
                            "

                            class="
                                flex
                                h-9
                                w-9
                                shrink-0
                                items-center
                                justify-center

                                rounded-lg
                                border
                                border-[#cdbb9f]/30

                                bg-[#f4f1e8]/10

                                text-[#f4f1e8]

                                transition

                                hover:bg-[#f4f1e8]/20
                            "

                            title="Fechar Grimório"
                        >
                            <svg
                                class="
                                    h-5
                                    w-5
                                "

                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M6 6l12 12M18 6L6 18"
                                />
                            </svg>
                        </button>
                    </div>
                </div>


                {{-- ========================================================
                     INVENTÁRIO
                ========================================================= --}}

                <div
                    x-show="
                        drawer ===
                            'inventory'
                    "

                    x-cloak

                    class="
                        min-h-0
                        flex-1
                        overflow-y-auto

                        p-4
                        sm:p-5
                    "
                >
                    @include(
                        'characters.components.inventory',
                        [
                            'character' =>
                                $character,
                        ]
                    )
                </div>


                {{-- ========================================================
                     GRIMÓRIO
                ========================================================= --}}

                <div
                    x-show="
                        drawer ===
                            'spellbook'
                    "

                    x-cloak

                    class="
                        min-h-0
                        flex-1
                        overflow-y-auto
                        p-4
                        sm:p-6
                    "
                >
                    @include(
                        'characters.components.spells',
                        [
                            'character' =>
                                $character,
                        ]
                    )
                </div>

            </aside>

        </div>

    </div>

</x-app-layout>