<x-app-layout>

    <style>
        [x-cloak] { display: none !important; }

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
            display: none !important; /* Esconde o header padrão do Laravel se houver */
        }

        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cdbb9f; border-radius: 999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #8c6239; }
        .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cdbb9f transparent; }
    </style>

    <div
        class="combats-show-page-scope h-screen w-full flex overflow-hidden bg-[#eee8dc] font-serif selection:bg-[#6b1d14] selection:text-[#f4f1e8]"
        x-data="{ openNpcModal: false, openPlayerModal: false }"
        @keydown.escape.window="openNpcModal = false; openPlayerModal = false"
    >
        <div id="combat-panels-wrapper" class="flex flex-1 overflow-hidden w-full">

            {{-- Área Principal (Fichas e Conteúdo) --}}
            <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar p-6">
                @include('combats.components.npc.npc-panel')
            </main>

            {{-- Painel Lateral Unificado (Header + Dados + Iniciativa) --}}
            <aside class="w-[300px] lg:w-[300px] shrink-0 border-l border-[#d8cebe] bg-[#f9f6f0] flex flex-col h-full shadow-inner">
                @include('combats.components.inciative.initiative-panel')
            </aside>

        </div>

        @include('combats.components.npc.npc-modal')
        @include('combats.components.player.player-modal')
    </div>

</x-app-layout>