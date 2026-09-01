<x-app-layout>

    <style>
        [x-cloak] {
            display: none !important;
        }

        main:has(.combats-show-page-scope),
        .py-12:has(.combats-show-page-scope),
        .max-w-7xl:has(.combats-show-page-scope),
        .sm\:px-6:has(.combats-show-page-scope),
        .lg\:px-8:has(.combats-show-page-scope) {
            padding: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
            height: 100vh;
            overflow: hidden;
        }

        header {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cdbb9f;
            border-radius: 999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #8c6239;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cdbb9f transparent;
        }
    </style>

    <div
        class="
            combats-show-page-scope
            flex
            h-screen
            w-full
            overflow-hidden
            bg-[#eee8dc]
            font-serif
            selection:bg-[#6b1d14]
            selection:text-[#f4f1e8]
        "
        x-data="{
            openNpcModal: false,
            openPlayerModal: @js(request()->boolean('player_modal'))
        }"
        @keydown.escape.window="
            openNpcModal = false;
            openPlayerModal = false;
        "
    >
        <div
            id="combat-panels-wrapper"
            class="flex w-full flex-1 overflow-hidden"
        >

            {{-- ============================================================
                 ÁREA PRINCIPAL
            ============================================================= --}}

            <main
                id="combat-npc-panel-host"
                class="
                    custom-scrollbar
                    min-w-0
                    flex-1
                    overflow-y-auto
                    p-6
                "
            >
                @include(
                    'combats.components.npc.npc-panel'
                )
            </main>


            {{-- ============================================================
                 PAINEL LATERAL — INICIATIVA
            ============================================================= --}}

            <aside
                class="
                    flex
                    h-full
                    w-[300px]
                    shrink-0
                    flex-col
                    border-l
                    border-[#d8cebe]
                    bg-[#f9f6f0]
                    shadow-inner
                    lg:w-[300px]
                "
            >
                @include(
                    'combats.components.inciative.initiative-panel'
                )
            </aside>


            {{--
            |--------------------------------------------------------------------------
            | Modal de Player dentro do wrapper
            |--------------------------------------------------------------------------
            |
            | O AJAX dos painéis substitui #combat-panels-wrapper.
            | Mantendo este modal aqui, a lista de Characters disponíveis
            | também é atualizada depois que uma ficha entra no combate.
            |
            --}}

            @include(
                'combats.components.player.player-modal'
            )

        </div>


        @include(
            'combats.components.npc.npc-modal'
        )

    </div>

</x-app-layout>