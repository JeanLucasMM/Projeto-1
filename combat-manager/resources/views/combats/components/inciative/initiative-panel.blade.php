<div
    x-data="{
        loading: false,
        // Mantém uma cópia local para iluminar o card selecionado na hora
        selectedNpc: sessionStorage.getItem('combat_{{ $combat->id }}_selected') ? parseInt(sessionStorage.getItem('combat_{{ $combat->id }}_selected')) : null,
        
        async handleInitiativeAction(e) {
            this.loading = true;
            const form = e.target;
            const wrapper = document.getElementById('combat-panels-wrapper');
            const header = document.getElementById('combat-header');

            try {
                await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const res = await fetch(window.location.href, { cache: 'no-store' });
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                
                const newContent = doc.getElementById('combat-panels-wrapper');
                const newHeader = doc.getElementById('combat-header');

                if (newContent) {
                    wrapper.innerHTML = newContent.innerHTML;
                    if (window.Alpine) Alpine.initTree(wrapper);
                    
                    setTimeout(() => {
                        const activeCard = wrapper.querySelector('.js-active-turn');
                        if (activeCard) {
                            activeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 50);
                }

                if (header && newHeader) {
                    header.innerHTML = newHeader.innerHTML;
                    if (window.Alpine) Alpine.initTree(header);
                }
            } catch (err) {
                console.error('Erro ao atualizar:', err);
            } finally {
                this.loading = false;
            }
        }
    }"
    class="h-full flex flex-col bg-[#f4f1e8] transition-opacity"
    :class="loading ? 'opacity-50 pointer-events-none' : 'opacity-100'"
>
    <style>
        @keyframes sutil-bounce {
            0%, 100% { transform: translateY(0); animation-timing-function: cubic-bezier(0.8,0,1,1); }
            50% { transform: translateY(-3px); animation-timing-function: cubic-bezier(0,0,0.2,1); }
        }
    </style>

    <div class="px-3.5 py-2.5 border-b border-[#cdbb9f]/40 bg-[#efe9dc]">
        <h2 class="font-serif text-base font-bold text-[#6b1d14] tracking-wide">Ordem de Iniciativa</h2>
        
        @if($combat->is_active)
            <p class="mt-0.5 text-[10px] uppercase tracking-wider text-amber-700 font-bold">Combate em andamento</p>
        @else
            <p class="mt-0.5 text-[10px] uppercase tracking-wider text-[#8c6239]/80 font-medium">Combate não iniciado</p>
        @endif

        <div class="flex flex-wrap gap-1.5 mt-2.5">
            @if(!$combat->is_active)
                {{-- Botão Jogador corrigido para alcançar o escopo global --}}
                <button type="button" @click="
    const mainEl = document.querySelector('[x-data*=\'openPlayerModal\']') || document.querySelector('[x-data*=\'selectedNpc\']');
    if (mainEl && window.Alpine) { Alpine.$data(mainEl).openPlayerModal = true; } else { openPlayerModal = true; }
" class="flex-1 min-w-[75px] px-2.5 py-1.5 rounded-md bg-[#8c6239] hover:bg-[#724b24] text-[#f4f1e8] text-xs uppercase tracking-wider font-bold transition text-center shadow-sm"> + Jogador </button>

                {{-- Botão NPC corrigido para alcançar o escopo global --}}
                <button type="button" 
                    @click="
                        const mainEl = document.querySelector('[x-data*=\'openNpcModal\']') || document.querySelector('[x-data*=\'selectedNpc\']');
                        if (mainEl && window.Alpine) { Alpine.$data(mainEl).openNpcModal = true; } else { openNpcModal = true; }
                    " 
                    class="flex-1 min-w-[75px] px-2.5 py-1.5 rounded-md bg-[#8c6239] hover:bg-[#724b24] text-[#f4f1e8] text-xs uppercase tracking-wider font-bold transition text-center shadow-sm"
                >
                    + NPC
                </button>

                <form action="{{ route('combats.start', $combat->id) }}" method="POST" @submit.prevent="handleInitiativeAction($event)" class="flex-1 min-w-[90px]">
                    @csrf
                    <button class="w-full px-2.5 py-1.5 rounded-md bg-[#6b1d14] hover:bg-[#53150f] text-white font-bold uppercase tracking-wider text-xs transition text-center shadow-sm">
                        ⚔ Iniciar
                    </button>
                </form>
            @else
                <form action="{{ route('combats.next', $combat->id) }}" method="POST" @submit.prevent="handleInitiativeAction($event)" class="w-full">
                    @csrf
                    <button class="w-full px-4 py-2.5 rounded-md bg-[#6b1d14] hover:bg-[#53150f] text-white font-extrabold uppercase tracking-widest text-sm transition-all text-center shadow-md active:scale-[0.99]">
                        Próximo Turno →
                    </button>
                </form>
                <form action="{{ route('combats.reset', $combat->id) }}" method="POST" @submit.prevent="if(confirm('Deseja realmente encerrar este combate?')) handleInitiativeAction($event)" class="w-full">
                    @csrf
                    <button class="w-full px-3 py-1 rounded-md border border-[#cdbb9f]/40 text-[#6b1d14]/60 hover:text-[#6b1d14] hover:bg-[#efe9dc] hover:border-[#cdbb9f] font-bold uppercase tracking-wider text-[10px] transition-all text-center mt-1.5 bg-white/30">
                        Terminar Combate
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div id="combat-panels-wrapper" class="flex-1 overflow-y-auto p-3 space-y-2 scrollbar-thin">
        @forelse($initiative as $participant)
            @php
                $isAnArray = is_array($participant);
                $isNpc = $isAnArray ? (($participant['type']??'') === 'npc') : $participant->isNpc();
                $name = $isAnArray ? $participant['name'] : $participant->name;
                $id = $isAnArray ? $participant['id'] : $participant->id;
                $initiativeVal = $isAnArray ? ($participant['initiative']??0) : $participant->initiative;
                $isCurrentTurn = $combat->is_active && ($loop->index === $combat->current_turn);
                
                $updateRoute = $isNpc ? route('combats.npcs.initiative', [$combat->id, $id]) : route('combats.players.initiative', [$combat->id, $id]);
                $destroyRoute = $isNpc ? route('combats.npcs.destroy', [$combat->id, $id]) : route('combats.players.destroy', [$combat->id, $id]);

                // --- ALTERAÇÃO 1: Mover lógica de HP para cá ---
                $isDeadNpc = false;
                $npcHPData = null;

                if ($isNpc) {
                    $npcModel = $combatNpcs->firstWhere('id', $id);
                    if ($npcModel) {
                        $isDeadNpc = $npcModel->current_hp <= 0;
                        
                        // Prepara dados para usar depois na barra de HP sem recalcular
                        $percent = $npcModel->max_hp > 0 ? ($npcModel->current_hp / $npcModel->max_hp) * 100 : 0;
                        $npcHPData = [
                            'current' => $npcModel->current_hp,
                            'max' => $npcModel->max_hp,
                            'percent' => $percent,
                            'color' => $percent > 60 ? 'bg-emerald-600' : ($percent > 30 ? 'bg-amber-500' : 'bg-red-600')
                        ];
                    }
                }
                // -------------------------------------------------
            @endphp

            <div 
                class="relative rounded-lg border transition-all duration-200"
                {{-- ALTERAÇÃO 2: Adicionar grayscale se for NPC morto (Blade expression dentro do array de classes do Alpine) --}}
                :class="[
                    {{ $isCurrentTurn ? "'border-[#6b1d14] bg-[#6b1d14]/[0.02] shadow-md js-active-turn'" : "'border-[#cdbb9f]/40 bg-white hover:border-[#cdbb9f]'" }},
                    (selectedNpc == {{ $id }} && {{ $isNpc ? 'true' : 'false' }}) ? 'ring-2 ring-amber-500/50 border-amber-600/40 bg-amber-50/[0.02]' : '',
                    {{ $isDeadNpc ? "'grayscale opacity-80 border-stone-300 bg-stone-50'" : "''" }}
                ]"
            >
                @if($isCurrentTurn)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#6b1d14] rounded-l-lg z-10"></div>
                    <div class="absolute -inset-px rounded-lg border-2 border-[#6b1d14] shadow-[0_0_12px_rgba(107,29,20,0.25)] animate-pulse pointer-events-none z-10"></div>
                @endif

                <div class="py-2 px-2.5 flex items-center justify-between gap-2 relative z-20">
                    
                    {{-- Bloco Esquerdo Clicável corrigido para forçar a atualização da ficha técnica --}}
                    <div 
                        @if($isNpc) 
                            @click="
                                selectedNpc = {{ $id }};
                                sessionStorage.setItem('combat_{{ $combat->id }}_selected', {{ $id }});
                                const mainSheetEl = document.querySelector('[x-data*=\'selectedNpc\']');
                                if (mainSheetEl && window.Alpine) {
                                    Alpine.$data(mainSheetEl).selectedNpc = {{ $id }};
                                }
                            " 
                        @endif
                        class="flex items-center gap-2 min-w-0 flex-1 select-none {{ $isNpc ? 'cursor-pointer group' : '' }}"
                    >
                        {{-- Número da iniciativa muda de cor se estiver morto para contrastar com o fundo cinza --}}
                        <div class="w-7 h-7 rounded-md flex-shrink-0 flex items-center justify-center font-serif text-sm font-bold lining-nums {{ $isCurrentTurn ? 'bg-[#6b1d14] text-white shadow-sm' : ($isDeadNpc ? 'bg-stone-200 text-stone-500' : 'bg-[#efe9dc] text-[#8c6239]') }}">
                            <span @if($isCurrentTurn) style="animation: sutil-bounce 1.2s infinite;" @endif>
                                {{ $loop->iteration }}
                            </span>
                        </div>

                        <div class="min-w-0 flex-1">
                            {{-- Nome riscado se estiver morto --}}
                            <h3 class="font-serif text-sm font-bold text-[#6b1d14] truncate leading-tight {{ $isNpc ? 'group-hover:text-amber-800 group-hover:underline decoration-amber-500/40' : '' }} {{ $isDeadNpc ? 'line-through text-stone-500' : '' }}" title="{{ $name }}">
                                {{ $name }}
                            </h3>
                            
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($isNpc)
                                    <span class="inline-block px-1 py-0.2 rounded text-[9px] uppercase font-bold tracking-wider bg-red-50 text-red-700 border border-red-200/40 shrink-0">NPC</span>
                                    
                                    {{-- Reutiliza os dados calculados no topo --}}
                                    @if($npcHPData)
                                        <div class="flex flex-col flex-1 min-w-0 max-w-[120px]">
                                            <span class="font-mono text-[9px] font-bold text-stone-600 lining-nums leading-none mb-1">
                                                @if($isDeadNpc)
                                                    <span class="text-red-700 bg-red-50 px-1 rounded border border-red-200/50 text-[8px] uppercase tracking-wide font-serif font-bold">Derrotado</span>
                                                @else
                                                    {{ $npcHPData['current'] }}<span class="text-stone-400">/{{ $npcHPData['max'] }}</span>
                                                @endif
                                            </span>
                                            <div class="h-2 w-full rounded-full bg-stone-100 border border-stone-200/30 overflow-hidden shadow-inner">
                                                <div class="{{ $npcHPData['color'] }} h-full transition-all duration-300" style="width: {{ $npcHPData['percent'] }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-block px-1 py-0.2 rounded text-[9px] uppercase font-bold tracking-wider bg-stone-100 text-[#8c6239] border border-[#cdbb9f]/30">Jogador</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 flex-shrink-0" @click.stop>
                        <form action="{{ $updateRoute }}" method="POST" @submit.prevent="handleInitiativeAction($event)" class="flex items-center">
                            @csrf @method('PATCH')
                            {{-- Input desabilitado se estiver morto --}}
                            <input type="number" name="initiative" value="{{ $initiativeVal }}" @change="$el.form.requestSubmit()" {{ $isDeadNpc ? 'disabled' : '' }}
                                class="w-10 h-7 rounded-md bg-white border border-[#cdbb9f]/70 text-center font-serif text-sm font-bold text-[#6b1d14] shadow-sm focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] outline-none transition-all lining-nums py-0 px-0 {{ $isDeadNpc ? 'text-stone-400 border-stone-200 bg-stone-50' : '' }}">
                        </form>

                        <form action="{{ $destroyRoute }}" method="POST" @submit.prevent="if(confirm('Remover {{ $name }} do combate?')) handleInitiativeAction($event)" class="flex items-center">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 rounded-md border border-red-100 hover:bg-red-50 text-red-600 flex items-center justify-center transition-all bg-white shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <div class="rounded-lg border border-dashed border-[#cdbb9f]/60 bg-[#faf8f2]/50 py-8 px-4 text-center">
                <div class="text-xl mb-1">⚔️</div>
                <h3 class="text-sm font-serif font-bold text-[#6b1d14]">Nenhum combatente</h3>
                <p class="text-xs text-[#8c6239]/80 mt-0.5">Adicione jogadores ou NPCs para começar.</p>
            </div>
        @endforelse
    </div>
</div>