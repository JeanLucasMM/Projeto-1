<div
    id="combat-header"
    x-data="{
        diceOpen: false,
        expression: '',
        result: null,
        error: null,
        isLoading: false,
        
        async rollDice() {
            if (this.expression.trim() === '') return;
            
            this.isLoading = true;
            this.result = null;
            this.error = null;

            try {
                const response = await fetch('/api/roll', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ expression: this.expression })
                });

                const json = await response.json();

                if (json.success) {
                    this.result = json.formatted;
                } else {
                    this.error = json.message;
                }
            } catch (err) {
                this.error = 'Erro de comunicação com o servidor.';
            } finally {
                this.isLoading = false;
            }
        }
    }"
    class="sticky top-0 z-40 bg-[#efe9dc]/95 backdrop-blur border-b border-[#cdbb9f]/40 shadow-sm"
>

    {{-- HEADER (Mantido igual ao seu original) --}}
    <div class="h-[60px] px-6 flex items-center justify-between">
        {{-- Esquerda --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="leading-tight">
                <h1 class="text-base font-serif font-bold text-[#6b1d14] tracking-wide truncate">
                    {{ $combat->name }}
                </h1>
                <p class="text-[9px] uppercase tracking-[0.2em] text-[#8c6239]/80 mt-0.5 truncate">
                    Gerenciador de Combate
                </p>
            </div>
        </div>

        {{-- Direita --}}
        <div class="flex items-center gap-3 shrink-0">
            {{-- Botão Dice --}}
            <button
                @click="diceOpen = !diceOpen"
                class="group w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 hover:bg-white/60"
            >
                <svg class="w-4 h-4 text-[#8c6239]/40 group-hover:text-[#6b1d14] group-hover:rotate-180 transition-all duration-300"
                    :class="{ 'rotate-180 text-[#6b1d14]' : diceOpen }"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            @if($combat->is_active)
                <div class="flex items-center gap-2 bg-white/90 border border-[#cdbb9f]/40 rounded-lg p-1 shadow-inner lining-nums">
                    <span class="pl-2.5 pr-1 text-[10px] uppercase font-bold tracking-wider text-[#8c6239]">
                        Rodada
                    </span>
                    <div class="w-7 h-7 rounded-md bg-[#6b1d14] text-white flex items-center justify-center font-serif text-xs font-bold shadow-sm">
                        {{ $combat->current_round }}
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2 bg-amber-50/90 border border-amber-500/30 rounded-lg px-2.5 py-1.5 shadow-sm">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-[9px] uppercase font-bold tracking-wider text-amber-800">
                        Preparação
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- PAINEL DE ROLAGEM --}}
    <div
        x-show="diceOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border-t border-[#d8c7ab] bg-[#faf8f2]"
        style="display:none;"
    >
        <div class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="text-lg">🎲</div>
                
                {{-- Input atualizado para rolar também com a tecla Enter --}}
                <input
                    x-model="expression"
                    @keydown.enter="rollDice"
                    type="text"
                    placeholder="Ex.: 1d20+5"
                    class="flex-1 rounded-lg border border-[#cdbb9f]/50 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#8c6239]/30 uppercase font-mono placeholder:normal-case placeholder:font-sans"
                    :disabled="isLoading"
                >

                {{-- Botão vinculado ao evento click --}}
                <button
                    @click="rollDice"
                    :disabled="isLoading"
                    class="px-4 py-2 rounded-lg bg-[#6b1d14] text-white text-sm font-semibold hover:bg-[#5a1811] transition disabled:opacity-50 flex items-center gap-2"
                >
                    <span x-text="isLoading ? 'Rolando...' : 'Rolar'"></span>
                </button>
            </div>

            {{-- Exibição de Sucesso --}}
            <div
                x-show="result"
                class="mt-3 p-2 rounded bg-[#efe9dc]/50 border border-[#cdbb9f]/30"
                style="display:none;"
            >
                <p class="font-mono text-sm font-bold text-[#6b1d14] tracking-wide" x-text="result"></p>
            </div>

            {{-- Exibição de Erro --}}
            <div
                x-show="error"
                class="mt-3 text-sm font-bold text-red-600 bg-red-50 p-2 rounded border border-red-200"
                style="display:none;"
                x-text="error"
            ></div>
        </div>
    </div>
</div>