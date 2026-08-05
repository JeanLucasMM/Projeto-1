@php
    $isDeceased = !is_null($npc->deceased_at);
    $deceasedDate = $npc->deceased_at ? \Carbon\Carbon::parse($npc->deceased_at)->format('d/m/Y') : '';
@endphp

<div
    id="npc-card-{{ $npc->id }}"
    class="npc-card group relative bg-gradient-to-br from-[#fcfaf5] to-[#f4f1e8] border border-[#cdbb9f]/60 hover:border-[#6b1d14]/50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between p-4 max-w-[240px] w-full"
    draggable="true"
    data-npc="{{ $npc->id }}"
>
    {{-- Overlay "Falecido" --}}
    <div 
        id="falecido-overlay-{{ $npc->id }}" 
        class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-gradient-to-t from-black/95 via-black/80 to-black/60 backdrop-grayscale-[0.3] backdrop-blur-[2px] transition-all duration-700 {{ $isDeceased ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}"
    >
        <svg 
            id="tomb-icon-{{ $npc->id }}"
            xmlns="http://www.w3.org/2000/svg" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" 
            stroke-width="1.5" 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            class="w-12 h-12 text-gray-400 drop-shadow-[0_0_5px_rgba(0,0,0,0.8)] transform transition-transform duration-700 ease-out {{ $isDeceased ? 'translate-y-0' : 'translate-y-6' }}"
        >
            <path d="M7 22V10a5 5 0 0 1 10 0v12"/>
            <path d="M5 22h14"/>
            <path d="M12 7v5"/>
            <path d="M10 9h4"/>
        </svg>

        <span 
            id="tomb-text-{{ $npc->id }}"
            class="mt-3 text-lg font-serif font-black text-red-600 uppercase tracking-[0.25em] drop-shadow-[0_0_8px_rgba(220,38,38,0.5)] transform transition-all duration-700 delay-150 {{ $isDeceased ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0' }}"
        >
            Falecido
        </span>

        <div 
            id="tomb-details-{{ $npc->id }}"
            class="text-center transform transition-all duration-700 delay-200 {{ $isDeceased ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0' }}"
        >
            <a 
                href="{{ route('npcs.show', $npc->id) }}"
                class="block text-sm font-serif font-bold text-gray-200 mt-2 line-clamp-1 px-4 hover:text-white hover:underline hover:scale-105 transition-all cursor-pointer drop-shadow-md" 
                title="Abrir ficha de {{ $npc->name }}"
            >
                {{ $npc->name }}
            </a>
            <p id="tomb-date-{{ $npc->id }}" class="text-[11px] font-mono text-gray-400 mt-1">
                {{ $deceasedDate }}
            </p>
        </div>
    </div>

    {{-- Menu de 3 Pontinhos --}}
    <div class="absolute top-3 right-3 z-20">
        <button
            type="button"
            class="npc-menu-btn w-7 h-7 flex items-center justify-center rounded-full hover:bg-[#cdbb9f]/40 text-[#6b1d14] transition-all bg-white/60 backdrop-blur-md shadow-sm hover:shadow"
            data-target="npc-menu-{{ $npc->id }}"
        >
            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
        </button>

        {{-- Dropdown do Menu --}}
        <div
            id="npc-menu-{{ $npc->id }}"
            class="npc-dropdown-menu hidden absolute right-0 mt-2 w-36 rounded-xl bg-[#fdfbf7] border border-[#cdbb9f]/60 shadow-xl z-30 overflow-hidden"
        >
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
                        class="w-full text-left px-3 py-2.5 hover:bg-amber-50/80 text-xs font-serif font-bold text-amber-700 transition-colors flex items-center gap-2"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        Tirar da Pasta
                    </button>
                </form>
            @endif

            {{-- Alternar Falecido --}}
            <button
                type="button"
                onclick="toggleFalecido('{{ $npc->id }}')"
                class="w-full text-left px-3 py-2.5 hover:bg-slate-100/80 text-xs font-serif font-bold text-slate-700 transition-colors flex items-center gap-2 border-b border-[#cdbb9f]/20"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m-4-4h8"></path>
                </svg>
                Falecido
            </button>

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
                    class="w-full text-left px-3 py-2.5 hover:bg-red-50 text-xs font-serif font-bold text-red-700 transition-colors flex items-center gap-2"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    {{-- Conteúdo do Card --}}
    <div class="flex flex-col items-center text-center pt-1 relative z-0">
        <div class="w-20 h-20 rounded-full overflow-hidden border-[3px] border-[#efe9dc] outline outline-1 outline-[#6b1d14]/40 shadow-md flex items-center justify-center bg-gradient-to-br from-[#cdbb9f]/30 to-[#cdbb9f]/10 mb-4 group-hover:scale-105 transition-transform duration-500">
            @if($npc->image_path)
                <img src="{{ asset('storage/'.$npc->image_path) }}" alt="{{ $npc->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-xl font-serif font-bold text-[#6b1d14] uppercase">{{ str($npc->name)->substr(0,2)->upper() }}</span>
            @endif
        </div>

        <h2 class="text-[16px] font-serif font-bold text-[#53150f] leading-tight line-clamp-2 w-full px-1 min-h-[40px] flex items-center justify-center drop-shadow-sm" title="{{ $npc->name }}">
            {{ $npc->name }}
        </h2>
        <p class="italic text-[10px] text-[#8c6239] font-bold uppercase tracking-widest mt-1.5 truncate w-full px-1">
            {{ $npc->size }} {{ $npc->creature_type }}
        </p>
    </div>

    {{-- Bloco de Status --}}
    <div class="my-4 flex items-center justify-between bg-gradient-to-b from-[#f4f1e8] to-[#e8e2d2] border border-[#cdbb9f]/50 px-3 py-2 rounded-xl text-center shadow-[inset_0_1px_3px_rgba(0,0,0,0.06)] relative z-0">
        <div class="flex flex-col flex-1">
            <span class="text-[9px] font-serif font-bold text-red-900/80 uppercase tracking-wider">Vida</span>
            <span class="text-xs font-mono font-bold text-red-700 leading-none mt-1">{{ $npc->max_hp }}</span>
        </div>
        <div class="w-[1px] h-5 bg-[#cdbb9f]/50 mx-1"></div>
        <div class="flex flex-col flex-1">
            <span class="text-[9px] font-serif font-bold text-blue-900/80 uppercase tracking-wider">CA</span>
            <span class="text-xs font-mono font-bold text-blue-700 leading-none mt-1">{{ $npc->armor_class }}</span>
        </div>
        <div class="w-[1px] h-5 bg-[#cdbb9f]/50 mx-1"></div>
        <div class="flex flex-col flex-1">
            <span class="text-[9px] font-serif font-bold text-amber-900/80 uppercase tracking-wider">ND</span>
            <span class="text-xs font-mono font-bold text-amber-800 leading-none mt-1">{{ rtrim(rtrim(number_format($npc->challenge_rating, 2, '.', ''), '0'), '.') }}</span>
        </div>
    </div>

    <a href="{{ route('npcs.show', $npc->id) }}" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-center font-serif font-bold text-[11px] uppercase tracking-[0.15em] hover:from-[#53150f] hover:to-[#6b1d14] transition-all shadow-md hover:shadow-lg  block mt-auto relative z-0">
        Ver Ficha
    </a>

    <input type="hidden" class="npc-avatar" value="{{ $npc->image_path ? asset('storage/'.$npc->image_path) : '' }}">
</div>

<script>
async function toggleFalecido(id) {
    const overlay = document.getElementById(`falecido-overlay-${id}`);
    const tombIcon = document.getElementById(`tomb-icon-${id}`);
    const tombText = document.getElementById(`tomb-text-${id}`);
    const tombDetails = document.getElementById(`tomb-details-${id}`);
    const tombDate = document.getElementById(`tomb-date-${id}`);
    const menu = document.getElementById(`npc-menu-${id}`);
    
    if (menu) menu.classList.add('hidden');

    try {
        const response = await fetch(`/npcs/${id}/toggle-deceased`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Erro ao salvar no banco');

        const data = await response.json();

        if (!data.is_deceased) {
            tombIcon.classList.add('translate-y-6');
            tombIcon.classList.remove('translate-y-0');
            
            tombText.classList.add('translate-y-4', 'opacity-0');
            tombText.classList.remove('translate-y-0', 'opacity-100');

            tombDetails.classList.add('translate-y-4', 'opacity-0');
            tombDetails.classList.remove('translate-y-0', 'opacity-100');

            setTimeout(() => {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100', 'pointer-events-auto');
            }, 300);
        } else {
            if (tombDate) tombDate.textContent = data.deceased_at;

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            
            setTimeout(() => {
                tombIcon.classList.remove('translate-y-6');
                tombIcon.classList.add('translate-y-0');
                
                tombText.classList.remove('translate-y-4', 'opacity-0');
                tombText.classList.add('translate-y-0', 'opacity-100');

                tombDetails.classList.remove('translate-y-4', 'opacity-0');
                tombDetails.classList.add('translate-y-0', 'opacity-100');
            }, 50);
        }
    } catch (error) {
        alert('Não foi possível atualizar o status de falecimento.');
    }
}
</script>