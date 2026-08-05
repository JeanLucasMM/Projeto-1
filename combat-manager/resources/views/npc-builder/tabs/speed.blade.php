<div class="space-y-6">

    {{-- MOVIMENTAÇÃO (GRID COMPACTO) --}}
    <div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
            
            {{-- Caminhada --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">Caminhada</label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="speed.walk"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-[#fbf9f4] py-1.5 pl-3 pr-7 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

            {{-- Escalada --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">Escalada</label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="speed.climb"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-[#fbf9f4] py-1.5 pl-3 pr-7 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

            {{-- Natação --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">Natação</label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="speed.swim"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-[#fbf9f4] py-1.5 pl-3 pr-7 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

            {{-- Escavação --}}
            <div class="flex flex-col">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">Escavação</label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="speed.burrow"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-[#fbf9f4] py-1.5 pl-3 pr-7 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

            {{-- Voo --}}
            <div class="flex flex-col col-span-2 sm:col-span-1">
                <label class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] mb-1 pl-1">Voo</label>
                <div class="relative">
                    <input type="number" min="0" step="5" x-model.number="speed.fly"
                        class="w-full rounded-lg border border-[#cdbb9f]/80 bg-[#fbf9f4] py-1.5 pl-3 pr-7 text-sm font-bold text-[#2b1d17] focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all shadow-inner">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-[#8c6239]/60 pointer-events-none">ft.</span>
                </div>
            </div>

        </div>

        {{-- Checkbox Pairar (Só faz sentido se tiver voo) --}}
        <div class="mt-2.5 flex justify-end">
            <label class="inline-flex items-center gap-2 cursor-pointer group" :class="{ 'opacity-50 cursor-not-allowed': speed.fly <= 0 }">
                <input type="checkbox" x-model="speed.hover" :disabled="speed.fly <= 0"
                    class="w-3.5 h-3.5 rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30 bg-white transition-colors">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#8c6239] group-hover:text-[#6b1d14] transition-colors select-none">
                    Pode Pairar (Hover)
                </span>
            </label>
        </div>
    </div>

    {{-- SALTOS (GAVETA OPCIONAL) --}}
    <div class="border-t border-[#cdbb9f]/40 pt-4">
        
        {{-- Botão de Toggle --}}
        <button type="button" @click="speed.hasJumps = !speed.hasJumps" 
            class="flex items-center gap-2 w-full text-left group">
            <svg :class="{'rotate-90': speed.hasJumps}" class="w-4 h-4 text-[#8c6239] group-hover:text-[#6b1d14] transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-[10px] font-black uppercase tracking-widest text-[#8c6239] group-hover:text-[#6b1d14] transition-colors">
                Configurar Saltos Específicos (Opcional)
            </span>
        </button>

        {{-- Área Expansível --}}
        <div x-show="speed.hasJumps" x-collapse>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 pb-1">

                {{-- Salto Horizontal --}}
                <div class="bg-[#f4f1e8]/50 border border-[#cdbb9f]/50 rounded-xl p-3.5">
                    <label class="block mb-2.5 text-[10px] font-black uppercase tracking-wider text-[#6b1d14]">
                        Salto Horizontal
                    </label>
                    <div class="flex items-center gap-2 text-center">
                        <div class="flex-1">
                            <div class="text-[8px] uppercase font-bold text-[#8c6239] mb-1">Base (FOR)</div>
                            <div class="bg-[#ece6d7]/80 rounded border border-[#cdbb9f]/40 py-1.5 text-xs font-bold text-[#53150f]" x-text="abilities.str || 0"></div>
                        </div>
                        <div class="text-[#8c6239] font-black pt-3">+</div>
                        <div class="flex-1">
                            <div class="text-[8px] uppercase font-bold text-[#8c6239] mb-1">Bônus</div>
                            <input type="number" x-model.number="speed.jumpHorizontalBonus" class="w-full text-center rounded border border-[#cdbb9f]/80 bg-white py-1.5 text-xs font-bold text-[#2b1d17] shadow-inner focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14]">
                        </div>
                        <div class="text-[#8c6239] font-black pt-3">=</div>
                        <div class="flex-1">
                            <div class="text-[8px] uppercase font-bold text-[#8c6239] mb-1">Total (ft.)</div>
                            <div class="bg-[#ece6d7] rounded border border-[#8c6239]/40 py-1.5 text-xs font-black text-[#6b1d14] shadow-sm" x-text="horizontalJump || 0"></div>
                        </div>
                    </div>
                </div>

                {{-- Salto Vertical --}}
                <div class="bg-[#f4f1e8]/50 border border-[#cdbb9f]/50 rounded-xl p-3.5">
                    <label class="block mb-2.5 text-[10px] font-black uppercase tracking-wider text-[#6b1d14]">
                        Salto Vertical
                    </label>
                    <div class="flex items-center gap-2 text-center">
                        <div class="flex-1">
                            <div class="text-[8px] uppercase font-bold text-[#8c6239] mb-1">Base (3+Mod)</div>
                            <div class="bg-[#ece6d7]/80 rounded border border-[#cdbb9f]/40 py-1.5 text-xs font-bold text-[#53150f]" x-text="3 + getModifier(abilities.str)"></div>
                        </div>
                        <div class="text-[#8c6239] font-black pt-3">+</div>
                        <div class="flex-1">
                            <div class="text-[8px] uppercase font-bold text-[#8c6239] mb-1">Bônus</div>
                            <input type="number" x-model.number="speed.jumpVerticalBonus" class="w-full text-center rounded border border-[#cdbb9f]/80 bg-white py-1.5 text-xs font-bold text-[#2b1d17] shadow-inner focus:ring-1 focus:ring-[#6b1d14] focus:border-[#6b1d14]">
                        </div>
                        <div class="text-[#8c6239] font-black pt-3">=</div>
                        <div class="flex-1">
                            <div class="text-[8px] uppercase font-bold text-[#8c6239] mb-1">Total (ft.)</div>
                            <div class="bg-[#ece6d7] rounded border border-[#8c6239]/40 py-1.5 text-xs font-black text-[#6b1d14] shadow-sm" x-text="verticalJump || 0"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>