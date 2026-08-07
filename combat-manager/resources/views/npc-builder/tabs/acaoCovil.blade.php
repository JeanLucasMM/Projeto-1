<div class="space-y-5">

    {{-- CONFIGURAÇÃO DAS AÇÕES DE COVIL --}}
    <div class="rounded-xl border border-[#cdbb9f]/80 bg-white shadow-sm overflow-hidden" x-show="lairActions.length > 0">
        <div class="px-4 py-3 border-b border-[#cdbb9f]/50 bg-gradient-to-r from-[#fbf9f4] via-[#f4f1e8] to-[#ece6d7]/70">
            <h3 class="text-xs font-black uppercase tracking-widest text-[#6b1d14]">
                Configuração das Ações de Covil
            </h3>
        </div>

        <div class="p-4 space-y-4">
            <div class="space-y-1">
                <label class="block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                    Texto introdutório (Editável)
                </label>

                <input
                    type="text"
                    x-model="lairActions[0].lair.intro"
                    placeholder="Deixe em branco para usar o automático..."
                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm font-bold text-[#6b1d14] placeholder:text-[#8a2519]/40 focus:ring-1 focus:ring-[#8a2519]"
                >
            </div>

           
        </div>
    </div>

    {{-- LISTA DE AÇÕES DE COVIL --}}
    <div class="space-y-3">
        <template x-for="(action, index) in lairActions" :key="action.id">
            <div class="rounded-xl border border-[#cdbb9f]/80 bg-white shadow-xs overflow-hidden hover:shadow-md transition-shadow" x-data="{ open: false }">
                
                {{-- Cabeçalho --}}
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-gradient-to-r from-[#fbf9f4] via-[#f4f1e8] to-[#ece6d7]/70 border-b border-[#cdbb9f]/50">
                    <div class="flex items-center gap-2.5 flex-1 min-w-[200px]">
                        <button type="button" @click="open = !open" class="cursor-pointer text-[#8c6239] hover:text-[#6b1d14] transition-colors p-1 rounded-md hover:bg-[#cdbb9f]/20">
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <input type="text" x-model="action.title" placeholder="Nome da ação de covil..." class="w-full border-0 bg-transparent text-xs font-black text-[#6b1d14] placeholder:text-[#8a2519]/50 focus:ring-0 p-0 outline-none">
                    </div>
                    
                    <button type="button" @click="removeLairAction(index)" class="text-red-700 text-[10px] font-black uppercase cursor-pointer hover:bg-red-50 p-1.5 rounded-md transition-colors">
                        Remover
                    </button>
                </div>

                {{-- Corpo --}}
                <div x-show="open" class="p-4 bg-white border-t border-[#cdbb9f]/30">
                    <div class="rounded-xl border border-[#cdbb9f]/60 bg-[#fbf9f4] shadow-inner">
                        <div wire:ignore>
                            <div :id="'lairActions-editor-'+action.id" class="min-h-[120px] p-3 prose prose-sm max-w-none text-xs text-[#53150f] outline-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>