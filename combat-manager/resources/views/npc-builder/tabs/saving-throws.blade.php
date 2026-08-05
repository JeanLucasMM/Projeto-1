<div class="space-y-2">

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
        <template
            x-for="ability in [
                { key:'str', label:'FOR' },
                { key:'dex', label:'DES' },
                { key:'con', label:'CON' },
                { key:'int', label:'INT' },
                { key:'wis', label:'SAB' },
                { key:'cha', label:'CAR' }
            ]"
            :key="ability.key">

            {{-- Card Compacto de Salvaguarda --}}
            <div class="bg-gradient-to-b from-[#fbf9f4] to-[#f4f1e8] border border-[#cdbb9f] rounded-xl p-2 text-center shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-[#8c6239] transition-all duration-200 group relative overflow-hidden flex flex-col justify-between h-[86px]">
                
                {{-- Linha Superior: Checkbox e Label (Alinhamento Simétrico por Grid) --}}
                <div class="grid grid-cols-[20px_1fr_20px] items-center w-full">
                    <div class="flex justify-start">
                        <input 
                            type="checkbox" 
                            x-model="savingThrows[ability.key].proficient"
                            class="w-3.5 h-3.5 rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30 bg-white cursor-pointer transition-colors"
                            title="Possui Proficiência"
                        >
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#53150f] text-center" x-text="ability.label"></span>
                    <div></div> {{-- Elemento invisível de balanceamento para simetria perfeita --}}
                </div>

                {{-- Valor Central (Resultado Final) --}}
                <div class="text-xl font-black text-[#6b1d14] leading-none my-auto" 
                     x-text="formatModifier(getSavingThrowValue(ability.key))">
                </div>

                {{-- Linha Inferior: Bônus Extra --}}
                <div class="flex items-center justify-center gap-1.5 border-t border-[#cdbb9f]/40 pt-1.5 mt-auto">
                    <span class="text-[8px] uppercase font-bold text-[#8c6239] tracking-wider">Extra</span>
                    <input 
                        type="number" 
                        x-model.number="savingThrows[ability.key].bonus"
                        class="h-5 w-10 rounded border border-[#cdbb9f]/60 bg-white px-1 text-center text-[9px] font-bold text-[#2b1d17] focus:outline-none focus:ring-1 focus:ring-[#6b1d14] transition-colors shadow-inner"
                    >
                </div>

            </div>
        </template>
    </div>
</div>