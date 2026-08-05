<div class="space-y-3">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <template x-for="attr in [
            { key: 'str', label: 'FOR' },
            { key: 'dex', label: 'DES' },
            { key: 'int', label: 'INT' },
            { key: 'wis', label: 'SAB' },
            { key: 'cha', label: 'CAR' }
        ]" :key="attr.key">

            {{-- Renderiza apenas se houver perícias vinculadas --}}
            <template x-if="skills.filter(s => s.ability.toLowerCase() === attr.key).length > 0">
                <div class="bg-gradient-to-b from-[#fbf9f4] to-[#f4f1e8] border border-[#cdbb9f]/80 rounded-xl p-3 shadow-sm flex items-stretch gap-3">
                    
                    {{-- Pequeno Quadrado do Atributo à Esquerda (Com mais tom - Vinho Suave e Elegante) --}}
                    <div class="w-12 shrink-0 bg-gradient-to-b from-[#7a3227] to-[#59211a] border border-[#d6a56c]/60 rounded-xl flex flex-col items-center justify-center p-1.5 shadow-sm text-white">
                        <span class="text-[9px] font-black uppercase tracking-widest text-[#f5ebd6]" x-text="attr.label"></span>
                        <span class="text-xs font-black text-white mt-0.5 leading-none" x-text="formatModifier(getModifier(abilities[attr.key]))"></span>
                    </div>

                    {{-- Retângulos Horizontais das Skills ao lado --}}
                    <div class="flex-1 flex flex-col justify-center space-y-2">
                        <template x-for="skill in skills.filter(s => s.ability.toLowerCase() === attr.key)" :key="skill.key">
                            <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg border transition-all duration-200"
                                 :class="skill.proficient ? 'bg-white border-[#d6a56c] shadow-xs ring-1 ring-[#d6a56c]/20' : 'bg-white/60 border-[#cdbb9f]/60 hover:bg-white hover:border-[#cdbb9f]'">
                                
                                {{-- Nome da Perícia (Letra levemente maior) --}}
                                <span class="text-xs font-bold tracking-wide truncate pr-2"
                                      :class="skill.proficient ? 'text-[#6b1d14] font-black' : 'text-[#53150f]'"
                                      x-text="skill.label"></span>

                                {{-- Controles Compactos (P, E, Bônus, Total) --}}
                                <div class="flex items-center gap-1.5 shrink-0">
                                    {{-- Proficiência (P) --}}
                                    <button type="button"
                                            @click="skill.proficient = !skill.proficient; if(!skill.proficient) skill.expertise = false"
                                            class="w-6 h-6 rounded-md border flex items-center justify-center transition-all cursor-pointer font-black text-[10px]"
                                            :class="skill.proficient ? 'bg-[#8a2519] border-[#6b1d14] text-[#f4f1e8] shadow-inner' : 'bg-white border-[#cdbb9f] text-[#a89f91] hover:text-[#8c6239]'"
                                            title="Proficiência">
                                        P
                                    </button>

                                    {{-- Expertise (E) --}}
                                    <button type="button"
                                            @click="if(skill.proficient) { skill.expertise = !skill.expertise; }"
                                            :disabled="!skill.proficient"
                                            class="w-6 h-6 rounded-md border flex items-center justify-center transition-all cursor-pointer font-black text-[10px] disabled:opacity-30 disabled:cursor-not-allowed"
                                            :class="skill.expertise ? 'bg-[#8c6239] border-[#5a3f25] text-white shadow-inner' : 'bg-white border-[#cdbb9f] text-[#a89f91] hover:text-[#8c6239]'"
                                            title="Expertise">
                                        E
                                    </button>

                                    {{-- Bônus Extra --}}
                                    <input type="number" 
                                           x-model.number="skill.bonus" 
                                           class="w-8 h-6 rounded-md border border-[#cdbb9f] bg-white px-0.5 text-center text-xs font-bold text-[#53150f] focus:ring-1 focus:ring-[#8a2519] shadow-inner"
                                           placeholder="+0">

                                    {{-- Resultado Final (Total) --}}
                                    <div class="w-7 text-right font-black text-xs"
                                         :class="skill.proficient ? 'text-[#8a2519]' : 'text-[#8c6239]'"
                                         x-text="formatModifier(getSkillValue(skill))">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
            </template>
        </template>
    </div>
</div>