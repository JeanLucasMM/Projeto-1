<div class="rounded-xl border border-[#cdbb9f]/40 bg-white/90 p-4 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
    
    {{-- Topo: Nome e Tipo --}}
    <div class="flex justify-between items-start gap-2">
        <div>
            <h3 class="font-serif text-base font-bold text-[#6b1d14] tracking-wide leading-tight">
                {{ $player->name }}
            </h3>
            <p class="text-[9px] font-bold uppercase tracking-wider text-[#8c6239]/80 mt-0.5">
                Personagem
            </p>
        </div>
        <span class="text-[9px] bg-stone-200 text-stone-700 px-2 py-0.5 rounded font-bold uppercase tracking-widest shrink-0">
            PC
        </span>
    </div>

    {{-- Centro: Ajuste de Iniciativa --}}
    <div class="mt-4 pt-3 border-t border-[#cdbb9f]/20">
        <div class="text-[9px] font-bold uppercase tracking-widest text-[#8c6239] mb-1.5">
            Modificar Iniciativa
        </div>
        
        <form method="POST" action="{{ route('combats.players.initiative', [$combat, $player]) }}" 
              class="flex items-center gap-2">
            @csrf
            @method('PATCH')

            <input type="number" name="initiative" value="{{ $player->initiative }}"
                class="w-16 h-8 bg-[#fcfbf9] border border-[#cdbb9f]/60 rounded text-center font-serif font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] outline-none text-sm transition-all">

            <button class="h-8 px-3 bg-[#8c6239] hover:bg-[#724b24] text-[#f4f1e8] text-xs font-bold rounded transition shadow-sm">
                ✓
            </button>
        </form>
    </div>

    {{-- Base: Ação de Remoção Fina --}}
    <div class="mt-3.5">
        <form method="POST" action="{{ route('combats.players.destroy', [$combat, $player]) }}"
              onsubmit="return confirm('Remover {{ $player->name }} do combate?')">
            @csrf
            @method('DELETE')

            <button class="w-full border border-red-200/80 hover:bg-red-50 text-red-700 hover:text-red-800 text-[9px] font-bold uppercase tracking-widest py-1.5 rounded transition">
                Remover da Mesa
            </button>
        </form>
    </div>

</div>