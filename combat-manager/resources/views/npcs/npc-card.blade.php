<div
    class="npc-card group relative bg-[#f4f1e8] border border-[#cdbb9f]/40 hover:border-[#6b1d14]/30 rounded-2xl shadow-sm hover:shadow-md transition-polished overflow-hidden flex flex-col justify-between p-4 max-w-[240px] w-full"
    draggable="true"
    data-npc="{{ $npc->id }}"
>
    {{-- Menu de 3 Pontinhos --}}
    <div class="absolute top-2 right-2 z-20">
        <button
            type="button"
            class="npc-menu-btn w-7 h-7 flex items-center justify-center rounded-lg hover:bg-[#cdbb9f]/50 text-[#6b1d14] transition-colors"
            data-target="npc-menu-{{ $npc->id }}"
        >
            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
        </button>

        {{-- Dropdown do Menu --}}
        <div
            id="npc-menu-{{ $npc->id }}"
            class="npc-dropdown-menu hidden absolute right-0 mt-1 w-36 rounded-lg bg-white border border-[#cdbb9f]/60 shadow-lg z-30 overflow-hidden"
        >
            {{-- Remover da Pasta (Aparece apenas se estiver em uma pasta) --}}
            @if($npc->folder_id)
                <form
                    method="POST"
                    action="{{ route('npcs.remove-folder', $npc) }}"
                    class="m-0 border-b border-[#cdbb9f]/20"
                >
                    @csrf
                    @method('PATCH')
                    <button
                        type="submit"
                        class="w-full text-left px-3 py-2 hover:bg-amber-50 text-[11px] font-serif font-bold text-amber-700 transition-colors flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        Tirar da Pasta
                    </button>
                </form>
            @endif

            {{-- Excluir NPC --}}
            <form
                method="POST"
                action="{{ route('npcs.destroy', $npc->id) }}"
                onsubmit="return confirm('Deseja mesmo banir este NPC do seu grimório?')"
                class="m-0"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="w-full text-left px-3 py-2 hover:bg-red-50 text-[11px] font-serif font-bold text-red-700 transition-colors flex items-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    {{-- Topo: Avatar + Nome + Tipo --}}
    <div class="flex flex-col items-center text-center pt-2">
        <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-[#6b1d14] shadow-sm flex items-center justify-center bg-[#6b1d14]/10 mb-3">
            @if($npc->image_path)
                <img src="{{ asset('storage/'.$npc->image_path) }}" alt="{{ $npc->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-lg font-serif font-bold text-[#6b1d14] uppercase">{{ str($npc->name)->substr(0,2)->upper() }}</span>
            @endif
        </div>

        <h2 class="text-[15px] font-serif font-bold text-[#6b1d14] leading-tight line-clamp-2 w-full px-1 min-h-[36px] flex items-center justify-center" title="{{ $npc->name }}">
            {{ $npc->name }}
        </h2>
        <p class="italic text-[10px] text-[#8c6239]/80 font-semibold uppercase tracking-wider mt-1 truncate w-full px-1">
            {{ $npc->size }} {{ $npc->creature_type }}
        </p>
    </div>

    {{-- Painel Central de Atributos --}}
    <div class="my-3 flex items-center justify-between bg-[#efe9dc]/60 border border-[#cdbb9f]/30 px-2 py-1.5 rounded-lg text-center shadow-inner">
        <div class="flex flex-col flex-1">
            <span class="text-[8px] font-serif font-bold text-red-800 uppercase">Vida</span>
            <span class="text-[11px] font-mono font-bold text-red-700 leading-none mt-0.5">{{ $npc->max_hp }}</span>
        </div>
        <div class="w-[1px] h-4 bg-[#cdbb9f]/40 mx-1"></div>
        <div class="flex flex-col flex-1">
            <span class="text-[8px] font-serif font-bold text-blue-800 uppercase">CA</span>
            <span class="text-[11px] font-mono font-bold text-blue-700 leading-none mt-0.5">{{ $npc->armor_class }}</span>
        </div>
        <div class="w-[1px] h-4 bg-[#cdbb9f]/40 mx-1"></div>
        <div class="flex flex-col flex-1">
            <span class="text-[8px] font-serif font-bold text-amber-800 uppercase">ND</span>
            <span class="text-[11px] font-mono font-bold text-amber-800 leading-none mt-0.5">{{ rtrim(rtrim(number_format($npc->challenge_rating, 2, '.', ''), '0'), '.') }}</span>
        </div>
    </div>

    {{-- Botão "Ver Ficha" --}}
    <a href="{{ route('npcs.show', $npc->id) }}" class="w-full py-2 rounded-lg bg-[#6b1d14] text-[#f4f1e8] text-center font-serif font-bold text-[11px] uppercase tracking-wider hover:bg-[#53150f] transition-polished shadow-sm block mt-auto">
        Ver Ficha
    </a>

    <input type="hidden" class="npc-avatar" value="{{ $npc->image_path ? asset('storage/'.$npc->image_path) : '' }}">
</div>