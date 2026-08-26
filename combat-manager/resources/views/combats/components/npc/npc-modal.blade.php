{{-- Modal Principal de Busca de NPCs --}}
<div
    x-show="openNpcModal"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-data="{ search: '' }"
    class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-[8vh] bg-stone-900/60 backdrop-blur-sm overflow-y-auto"
>
    <div
        @click.outside="openNpcModal = false"
        class="bg-[#f9f6f0] border border-[#d8cebe] rounded-xl shadow-2xl w-full max-w-5xl max-h-[80vh] flex flex-col overflow-hidden ring-1 ring-white/50"
    >
        {{-- Cabeçalho --}}
        <div class="px-6 py-5 border-b border-[#d8cebe] bg-[#eee8dc]/90 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-serif font-bold text-[#6b1d14] drop-shadow-sm">Adicionar ao Combate          !(EM CONSTRUÇÃO)! </h2>
                <p class="text-xs text-[#8c6239] uppercase tracking-wider font-bold mt-1">
                    Selecione criaturas para o combate
                </p>
            </div>
            <button
                @click="openNpcModal = false"
                class="w-8 h-8 rounded-md hover:bg-white hover:shadow-sm border border-transparent hover:border-[#d8cebe] text-[#6b1d14]/60 hover:text-[#6b1d14] flex items-center justify-center transition-all"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Busca Integrada --}}
        <div class="p-4 border-b border-[#d8cebe]/60 bg-[#f9f6f0]">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-[#8c6239]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    id="npc-search"
                    type="text"
                    x-model="search"
                    placeholder="Pesquisar pelo nome da criatura..."
                    class="w-full rounded-lg border border-[#cdbb9f] bg-white pl-10 pr-4 py-2.5 text-sm text-[#6b1d14] placeholder-stone-400 focus:ring-2 focus:ring-[#6b1d14]/30 focus:border-[#6b1d14]/50 outline-none transition-all shadow-inner"
                >
            </div>
        </div>

        {{-- Lista de Criaturas --}}
        <div class="p-5 overflow-y-auto flex-1 custom-scrollbar">
            @if($npcs->isEmpty())
                <div class="flex flex-col items-center justify-center h-full py-12 text-[#8c6239]/60">
                    <span class="text-4xl mb-3 opacity-50">📖</span>
                    <p class="font-serif italic text-lg">Nenhum NPC encontrado em seus registros.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($npcs as $npc)
                        <div 
                            x-show="search === '' || '{{ strtolower($npc->name) }}'.includes(search.toLowerCase())"
                            class="bg-white border border-[#d8cebe] rounded-xl p-4 hover:border-[#6b1d14]/40 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-col group"
                        >
                            <div class="flex-1">
                                <h3 class="font-serif font-bold text-[15px] text-[#6b1d14] mb-3 line-clamp-2 leading-tight" title="{{ $npc->name }}">
                                    {{ $npc->name }}
                                </h3>
                                
                                {{-- Badges de Atributos --}}
                                <div class="flex flex-wrap gap-1.5 mb-2">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded border border-[#d8cebe] bg-[#eee8dc]/50 text-[10px] font-bold text-[#8c6239] uppercase tracking-wide">
                                        CR {{ $npc->challenge_rating }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded border border-[#d8cebe] bg-[#eee8dc]/50 text-[10px] font-bold text-[#8c6239] uppercase tracking-wide">
                                        CA {{ $npc->armor_class }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded border border-red-200 bg-red-50 text-[10px] font-bold text-red-700 uppercase tracking-wide">
                                        HP {{ $npc->max_hp }}
                                    </span>
                                </div>
                                
                                <p class="text-[11px] font-medium text-stone-500 truncate" title="{{ $npc->creature_type }}">
                                    {{ $npc->creature_type }}
                                </p>
                            </div>

                            <form action="{{ route('combats.npcs.store', $combat) }}" method="POST" class="mt-4 pt-4 border-t border-[#d8cebe]/40">
                                @csrf
                                <input type="hidden" name="npc_id" value="{{ $npc->id }}">
                                <button class="w-full rounded-md bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] text-xs font-bold uppercase py-2 transition-all shadow-sm active:scale-[0.98] tracking-widest">
                                    Adicionar
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal de Duplicidade --}}
@if(session('duplicateNpc'))
<div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm transition-opacity">
    <div class="bg-[#f9f6f0] border border-[#d8cebe] rounded-xl shadow-2xl w-full max-w-sm overflow-hidden ring-1 ring-white/50 animate-in zoom-in-95 duration-200">
        
        <div class="p-6 text-center">
            <div class="w-14 h-14 bg-amber-100 border border-amber-200 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                 <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                 </svg>
            </div>
            
            <h2 class="text-xl font-serif font-bold text-[#6b1d14]">NPC já em combate</h2>
            <p class="mt-3 text-sm text-[#8c6239] leading-relaxed">
                A criatura <strong class="text-[#6b1d14]">{{ session('duplicateNpc.name') }}</strong> já foi convocada para este confronto. 
                <span class="block mt-1">Deseja inserir uma cópia?</span>
            </p>
        </div>

        <div class="p-4 bg-[#eee8dc]/50 border-t border-[#d8cebe] flex flex-col gap-2.5">
            <form action="{{ route('combats.npcs.store', $combat) }}" method="POST">
                @csrf
                <input type="hidden" name="npc_id" value="{{ session('duplicateNpc.npc') }}">
                <input type="hidden" name="force" value="1">
                <button class="w-full py-2.5 rounded-md bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] text-xs font-bold uppercase tracking-widest transition-all shadow-sm active:scale-[0.98]">
                    Adicionar Cópia
                </button>
            </form>
            
            <a href="{{ route('combats.show', $combat) }}" class="w-full py-2.5 rounded-md border border-[#cdbb9f] bg-white text-[#6b1d14] hover:bg-stone-50 text-xs font-bold uppercase tracking-widest transition-all text-center shadow-sm active:scale-[0.98]">
                Cancelar
            </a>
        </div>
    </div>
</div>
@endif