<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Scrollbar personalizada fina e temática */
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
            transition: background 0.2s ease;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #8c6239;
        }

        /* Suporte para Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cdbb9f transparent;
        }

        /* Destrava os limites de largura padrão do layout do Laravel */
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
    </style>

    <div
        class="combats-show-page-scope h-full w-full flex flex-col overflow-hidden bg-[#efe9dc]/30 font-serif"
        x-data="{
            openNpcModal: false,
            openPlayerModal: false
        }"
    >

        {{-- Header - Cobertura total no topo do espaço disponível --}}
        @include('combats.components.header')

        {{-- CONTAINER DO ENCONTRO --}}
        <div id="combat-panels-wrapper" class="flex flex-1 overflow-hidden">

            {{-- Área principal --}}
            <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar px-8 py-6">
                @include('combats.components.npc.npc-panel')
            </main>

            {{-- Iniciativa --}}
            <aside class="w-[280px] shrink-0 border-l border-[#d9ccb8] bg-[#f7f3ea] overflow-y-auto custom-scrollbar">
                @include('combats.components.inciative.initiative-panel')
            </aside>

        </div>

        {{-- Modais de Inclusão --}}
        @include('combats.components.npc.npc-modal')
        @include('combats.components.player.player-modal')

    </div>
</x-app-layout>