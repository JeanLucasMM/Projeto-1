<x-app-layout>
    <style>
        .builder-scope {
            background-color: #efe9dc;
            background-image: radial-gradient(circle at center, rgba(239, 233, 220, 0.8) 0%, rgba(220, 210, 190, 0.9) 100%);
        }

        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(140, 98, 57, 0.4);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(140, 98, 57, 0.7); }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(138, 37, 25, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(138, 37, 25, 0); }
        }
        .btn-glow { animation: pulse-glow 2s infinite; }
    </style>

    <div
        class="builder-scope h-screen overflow-hidden flex flex-col font-serif selection:bg-[#6b1d14] selection:text-[#f4f1e8] relative"
        x-data="npcBuilder(@js($npc ?? null), {
            sizes: @js(\App\Support\Dictionaries\NpcSizes::options()),
            types: @js(\App\Support\Dictionaries\NpcTypes::options()),
            alignments: @js(\App\Support\Dictionaries\Alignments::options()),
            languages: @js(\App\Support\Dictionaries\Languages::options()),
            senses: @js(\App\Support\Dictionaries\Senses::options()),
            damageTypes: @js(\App\Support\Dictionaries\DamageTypes::options()),
            conditions: @js(\App\Support\Dictionaries\Conditions::options()),
        })"
    >
        {{-- Botão Flutuante de Salvar --}}
        <div class="fixed bottom-6 right-6 z-50">
            <button
                type="submit"
                form="npc-form"
                class="btn-glow px-6 py-3 rounded-xl bg-gradient-to-r from-[#6b1d14] via-[#8a2519] to-[#6b1d14] hover:from-[#53150f] hover:to-[#6b1d14] text-[#f4f1e8] font-black text-[11px] uppercase tracking-widest shadow-xl transition-all transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 border border-[#d6a56c]/50 cursor-pointer"
            >
                <svg class="w-4 h-4 text-[#d6a56c]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Salvar Ficha
            </button>
        </div>

        <div class="flex-1 max-w-[1800px] w-full mx-auto px-4 lg:px-6 py-5 overflow-hidden">
            <form id="npc-form" method="POST" action="{{-- route('npcs.store') --}}" class="h-full flex flex-col lg:flex-row gap-5">
                @csrf

                {{-- COLUNA DA ESQUERDA: FORMULÁRIO --}}
                @include('npc-builder.builder')

                {{-- COLUNA DA DIREITA: PREVIEW DA FICHA DO NPC --}}
                @include('npc-builder.preview')

            </form>
        </div>
    </div>
</x-app-layout>