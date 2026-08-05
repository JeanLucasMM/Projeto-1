<div class="space-y-6">

    {{-- SENTIDOS ESPECIAIS (BLINDSIGHT, DARKVISION, ETC.) --}}
    <div class="rounded-2xl border border-[#cdbb9f]/70 bg-gradient-to-b from-[#fbf9f4] to-[#f4f1e8] p-4 sm:p-5 shadow-sm space-y-5">
    
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            
            {{-- Blindsight --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">
                    Visão Cega (Blindsight)
                </label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="combat.senses.blindsight"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-white py-1.5 pl-3 pr-8 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

            {{-- Darkvision --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">
                    Visão no Escuro (Darkvision)
                </label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="combat.senses.darkvision"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-white py-1.5 pl-3 pr-8 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

            {{-- Tremorsense --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">
                    Sentido Sísmico (Tremorsense)
                </label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="combat.senses.tremorsense"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-white py-1.5 pl-3 pr-8 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

            {{-- Truesight --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">
                    Visão Verdadeira (Truesight)
                </label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="combat.senses.truesight"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-white py-1.5 pl-3 pr-8 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

        </div>

        {{-- SEÇÃO DE SENTIDOS PERSONALIZADOS --}}
        <div class="border-t border-[#cdbb9f]/50 pt-5">

            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                    Sentidos Personalizados
                </span>

                <button type="button"
                    @click="combat.customSenses.push({ name: '', distance: 30 })"
                    class="rounded-lg border border-[#cdbb9f] bg-[#ece6d7] px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-[#53150f] hover:bg-[#e5dcc8] transition-colors cursor-pointer shadow-xs">
                    + Adicionar
                </button>
            </div>

            <div class="space-y-2">
                <template x-for="(sense, index) in combat.customSenses" :key="index">
                    <div class="flex items-center gap-2 bg-white/60 border border-[#cdbb9f]/50 p-2 rounded-xl">
                        
                        <input type="text"
                            x-model="sense.name"
                            placeholder="Nome do sentido (ex: Ecolocalização)"
                            class="flex-1 rounded-lg border border-[#cdbb9f]/80 bg-white px-2.5 py-1.5 text-xs font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] shadow-inner">

                        <div class="relative w-24 shrink-0">
                            <input type="number" min="0" step="5"
                                x-model.number="sense.distance"
                                class="w-full rounded-lg border border-[#cdbb9f]/80 bg-white py-1.5 pl-2.5 pr-7 text-xs font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] shadow-inner">
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                        </div>

                        <button type="button"
                            @click="combat.customSenses.splice(index, 1)"
                            class="w-7 h-7 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors cursor-pointer font-black text-xs shrink-0"
                            title="Remover sentido">
                            ×
                        </button>

                    </div>
                </template>

                <div x-show="combat.customSenses.length === 0"
                    class="text-xs italic text-[#8c6239]/60 text-center py-2">
                    Nenhum sentido personalizado adicionado.
                </div>
            </div>

        </div>

    </div>

    {{-- PERCEPÇÃO PASSIVA --}}
    <div class="rounded-2xl border border-[#cdbb9f]/70 bg-gradient-to-r from-[#f4f1e8] to-[#ece6d7] p-4 sm:p-5 shadow-sm flex items-center justify-between">
        <div class="flex flex-col pr-3">
            <span class="text-[10px] font-black uppercase tracking-widest text-[#8c6239] mb-0.5">
                Percepção Passiva (Passive Perception)
            </span>
            <span class="text-[#53150f] text-xs italic">
                Calculado automaticamente: 10 + bônus de Percepção + bônus adicional.
            </span>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <div class="flex flex-col items-end">
                <span class="text-[9px] font-bold uppercase tracking-wider text-[#8c6239] mb-1">Bônus Adicional</span>
                <input type="number"
                    x-model.number="combat.passivePerceptionBonus"
                    class="w-14 h-8 rounded-lg border border-[#cdbb9f] bg-white text-center text-xs font-bold text-[#2b1d17] shadow-inner focus:ring-1 focus:ring-[#6b1d14]">
            </div>

            <div class="w-12 h-12 rounded-xl bg-gradient-to-b from-[#8a4238] to-[#642d25] border border-[#d6a56c]/40 flex flex-col items-center justify-center shadow-sm text-white shrink-0">
                <span class="text-[8px] font-black uppercase text-[#f0dec9] leading-none">Total</span>
                <span class="text-base font-black text-white leading-none mt-0.5" x-text="passivePerception"></span>
            </div>
        </div>
    </div>

</div>