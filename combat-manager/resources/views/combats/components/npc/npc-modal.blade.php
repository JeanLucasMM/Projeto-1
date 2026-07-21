<div
    x-show="openNpcModal"
    x-cloak
    x-transition
    x-data="{ search: '' }"
    {{-- Alinhado lá em cima com um recuo elegante do topo --}}
    class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-[6vh] bg-black/40 backdrop-blur-sm overflow-y-auto"
>
    <div
        @click.outside="openNpcModal = false"
        class="bg-[#f4f1e8] border border-[#cdbb9f]/50 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden"
    >
        {{-- Cabeçalho --}}
        <div class="px-6 py-4 border-b border-[#cdbb9f]/30 bg-[#efe9dc] flex items-center justify-between">
            <div>
                <h2 class="text-lg font-serif font-bold text-[#6b1d14]">Adicionar ao Grimório</h2>
                <p class="text-[10px] text-[#8c6239] uppercase tracking-wider font-bold mt-0.5">
                    Selecione uma criatura para este combate
                </p>
            </div>
            <button
                @click="openNpcModal = false"
                class="text-[#6b1d14]/60 hover:text-[#6b1d14] transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Busca Integrada --}}
        <div class="p-3 border-b border-[#cdbb9f]/20 bg-[#faf8f2]/60">
            <input
                id="npc-search"
                type="text"
                x-model="search"
                placeholder="Pesquisar pelo nome..."
                class="w-full rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 px-4 py-2 text-xs font-serif text-[#6b1d14] placeholder-[#8c6239]/50 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14]"
            >
        </div>

        {{-- Lista de Criaturas Compactada --}}
        <div class="p-5 overflow-y-auto flex-1">
            @if($npcs->isEmpty())
                <div class="text-center py-12 text-[#8c6239]/60 font-serif italic">
                    Nenhum NPC encontrado em seus registros.
                </div>
            @else
                {{-- Mudança para até 4 colunas bem distribuídas --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
                    @foreach($npcs as $npc)
                        <div 
                            x-show="search === '' || '{{ strtolower($npc->name) }}'.includes(search.toLowerCase())"
                            class="bg-[#efe9dc]/20 border border-[#cdbb9f]/40 rounded-xl p-3 hover:border-[#6b1d14]/40 hover:bg-white transition-all flex flex-col justify-between min-h-[135px]"
                        >
                            <div>
                                <h3 class="font-serif font-bold text-xs text-[#6b1d14] truncate mb-1.5" title="{{ $npc->name }}">
                                    {{ $npc->name }}
                                </h3>
                                
                                {{-- Atributos mini em grid compacto --}}
                                <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-[9px] font-bold text-[#8c6239]/90 uppercase tracking-wide">
                                    <span>CR {{ $npc->challenge_rating }}</span>
                                    <span>CA {{ $npc->armor_class }}</span>
                                    <span>HP {{ $npc->max_hp }}</span>
                                    <span class="truncate">{{ $npc->creature_type }}</span>
                                </div>
                            </div>

                            <form action="{{ route('combats.npcs.store', $combat) }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="npc_id" value="{{ $npc->id }}">
                                <button class="w-full rounded-lg bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] text-[9px] font-serif font-bold uppercase py-1.5 transition-all shadow-sm tracking-wider">
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
<div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-[#f4f1e8] border border-[#6b1d14]/20 rounded-2xl p-6 shadow-2xl w-full max-w-sm text-center">
        <div class="w-12 h-12 bg-[#6b1d14]/10 rounded-full flex items-center justify-center mx-auto mb-4">
             <svg class="w-6 h-6 text-[#6b1d14]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
             </svg>
        </div>
        
        <h2 class="text-lg font-serif font-bold text-[#6b1d14]">NPC já em combate</h2>
        <p class="mt-2 text-xs text-[#8c6239] leading-relaxed">
            O NPC <strong>{{ session('duplicateNpc.name') }}</strong> já foi convocado. Deseja inserir uma cópia?
        </p>

        <div class="flex flex-col gap-2 mt-6">
            <form action="{{ route('combats.npcs.store', $combat) }}" method="POST">
                @csrf
                <input type="hidden" name="npc_id" value="{{ session('duplicateNpc.npc') }}">
                <input type="hidden" name="force" value="1">
                <button class="w-full py-2.5 rounded-xl bg-[#6b1d14] text-[#f4f1e8] text-[10px] font-serif font-bold uppercase tracking-wider hover:bg-[#53150f]">
                    Adicionar cópia
                </button>
            </form>
            
            <a href="{{ route('combats.show', $combat) }}" class="w-full py-2.5 rounded-xl border border-[#cdbb9f]/50 text-[#6b1d14] text-[10px] font-serif font-bold uppercase tracking-wider hover:bg-[#efe9dc] flex items-center justify-center">
                Cancelar
            </a>
        </div>
    </div>
</div>
@endif