<div class="space-y-6">
    <div class="rounded-2xl border border-[#cdbb9f]/70 bg-gradient-to-b from-[#fbf9f4] to-[#f4f1e8] p-4 sm:p-5 shadow-sm space-y-5">
    

        {{-- Resistências a Dano --}}
        <div x-data="{ open: false, customItem: '', dict: @js(\App\Support\Dictionaries\DamageTypes::options()) }" 
             class="relative"
             :class="{'z-50': open}">
            
            <label class="block mb-1.5 text-[10px] font-bold uppercase tracking-wider text-[#8c6239] pl-1">
                Resistências a Dano (Damage Resistances)
            </label>
            
            <div @click="open = !open" 
                class="min-h-[38px] w-full rounded-xl border border-[#cdbb9f]/80 bg-white flex items-center justify-between px-2.5 py-1.5 shadow-inner cursor-pointer hover:border-[#8c6239] transition-all duration-200"
                :class="{'ring-1 ring-[#8a2519] border-[#8a2519] bg-[#fbf9f4]': open}">
                <div class="flex flex-wrap gap-1.5 flex-1 items-center">
                    <span x-show="!combat.resistances.length" class="text-xs text-[#8c6239]/50 italic">Selecionar resistências...</span>
                    <template x-for="(item, index) in combat.resistances" :key="index">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#ece6d7] border border-[#d6a56c]/70 px-2 py-0.5 text-[10px] font-bold text-[#53150f] uppercase tracking-wide shadow-xs">
                            <span x-text="dict[item] || item"></span>
                            <button type="button" @click.stop="combat.resistances.splice(index,1)" class="text-[#53150f]/60 hover:text-red-700 focus:outline-none text-xs font-black">&times;</button>
                        </span>
                    </template>
                </div>
                <div class="px-1 shrink-0">
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-[#8c6239] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms style="display: none;" class="absolute bottom-full mb-2 left-0 w-full rounded-xl border border-[#cdbb9f] bg-white p-3.5 shadow-2xl z-50">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 max-h-40 overflow-y-auto custom-scrollbar pr-1 pb-2">
                    <template x-for="(label, value) in dict" :key="value">
                        <label class="flex items-center gap-2 p-1.5 rounded-lg cursor-pointer hover:bg-[#f4f1e8] transition-colors text-xs select-none">
                            <input type="checkbox" :value="value" x-model="combat.resistances" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-3.5 h-3.5">
                            <span class="text-[#53150f] font-semibold" x-text="label"></span>
                        </label>
                    </template>
                </div>
                <div class="flex gap-2 pt-2.5 border-t border-[#cdbb9f]/40" @click.stop>
                    <input type="text" x-model="customItem" @keydown.enter.prevent="if(customItem.trim()) { combat.resistances.push(customItem.trim()); customItem = ''; }" placeholder="Resistência customizada..." 
                        class="h-8 py-1 w-full rounded-lg border border-[#cdbb9f] bg-[#fbf9f4] px-3 text-xs font-bold text-[#2b1d17] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#6b1d14]">
                    <button type="button" @click="if(customItem.trim()) { combat.resistances.push(customItem.trim()); customItem = ''; }" class="h-8 rounded-lg bg-[#8a2519] px-4 text-[10px] font-black uppercase text-[#f4f1e8] hover:bg-[#53150f] transition-colors shadow-sm cursor-pointer">Add</button>
                </div>
            </div>
        </div>

        <div class="border-t border-[#cdbb9f]/40"></div>

        {{-- Imunidades a Dano --}}
        <div x-data="{ open: false, customItem: '', dict: @js(\App\Support\Dictionaries\DamageTypes::options()) }" 
             class="relative"
             :class="{'z-50': open}">
            
            <label class="block mb-1.5 text-[10px] font-bold uppercase tracking-wider text-[#8c6239] pl-1">
                Imunidades a Dano (Damage Immunities)
            </label>
            
            <div @click="open = !open" 
                class="min-h-[38px] w-full rounded-xl border border-[#cdbb9f]/80 bg-white flex items-center justify-between px-2.5 py-1.5 shadow-inner cursor-pointer hover:border-[#8c6239] transition-all duration-200"
                :class="{'ring-1 ring-[#8a2519] border-[#8a2519] bg-[#fbf9f4]': open}">
                <div class="flex flex-wrap gap-1.5 flex-1 items-center">
                    <span x-show="!combat.immunities.length" class="text-xs text-[#8c6239]/50 italic">Selecionar imunidades...</span>
                    <template x-for="(item, index) in combat.immunities" :key="index">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#ece6d7] border border-[#d6a56c]/70 px-2 py-0.5 text-[10px] font-bold text-[#53150f] uppercase tracking-wide shadow-xs">
                            <span x-text="dict[item] || item"></span>
                            <button type="button" @click.stop="combat.immunities.splice(index,1)" class="text-[#53150f]/60 hover:text-red-700 focus:outline-none text-xs font-black">&times;</button>
                        </span>
                    </template>
                </div>
                <div class="px-1 shrink-0">
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-[#8c6239] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms style="display: none;" class="absolute bottom-full mb-2 left-0 w-full rounded-xl border border-[#cdbb9f] bg-white p-3.5 shadow-2xl z-50">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 max-h-40 overflow-y-auto custom-scrollbar pr-1 pb-2">
                    <template x-for="(label, value) in dict" :key="value">
                        <label class="flex items-center gap-2 p-1.5 rounded-lg cursor-pointer hover:bg-[#f4f1e8] transition-colors text-xs select-none">
                            <input type="checkbox" :value="value" x-model="combat.immunities" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-3.5 h-3.5">
                            <span class="text-[#53150f] font-semibold" x-text="label"></span>
                        </label>
                    </template>
                </div>
                <div class="flex gap-2 pt-2.5 border-t border-[#cdbb9f]/40" @click.stop>
                    <input type="text" x-model="customItem" @keydown.enter.prevent="if(customItem.trim()) { combat.immunities.push(customItem.trim()); customItem = ''; }" placeholder="Imunidade customizada..." 
                        class="h-8 py-1 w-full rounded-lg border border-[#cdbb9f] bg-[#fbf9f4] px-3 text-xs font-bold text-[#2b1d17] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#6b1d14]">
                    <button type="button" @click="if(customItem.trim()) { combat.immunities.push(customItem.trim()); customItem = ''; }" class="h-8 rounded-lg bg-[#8a2519] px-4 text-[10px] font-black uppercase text-[#f4f1e8] hover:bg-[#53150f] transition-colors shadow-sm cursor-pointer">Add</button>
                </div>
            </div>
        </div>

        <div class="border-t border-[#cdbb9f]/40"></div>

        {{-- Vulnerabilidades a Dano --}}
        <div x-data="{ open: false, customItem: '', dict: @js(\App\Support\Dictionaries\DamageTypes::options()) }" 
             class="relative"
             :class="{'z-50': open}">
            
            <label class="block mb-1.5 text-[10px] font-bold uppercase tracking-wider text-[#8c6239] pl-1">
                Vulnerabilidades a Dano (Damage Vulnerabilities)
            </label>
            
            <div @click="open = !open" 
                class="min-h-[38px] w-full rounded-xl border border-[#cdbb9f]/80 bg-white flex items-center justify-between px-2.5 py-1.5 shadow-inner cursor-pointer hover:border-[#8c6239] transition-all duration-200"
                :class="{'ring-1 ring-[#8a2519] border-[#8a2519] bg-[#fbf9f4]': open}">
                <div class="flex flex-wrap gap-1.5 flex-1 items-center">
                    <span x-show="!combat.vulnerabilities.length" class="text-xs text-[#8c6239]/50 italic">Selecionar vulnerabilidades...</span>
                    <template x-for="(item, index) in combat.vulnerabilities" :key="index">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#ece6d7] border border-[#d6a56c]/70 px-2 py-0.5 text-[10px] font-bold text-[#53150f] uppercase tracking-wide shadow-xs">
                            <span x-text="dict[item] || item"></span>
                            <button type="button" @click.stop="combat.vulnerabilities.splice(index,1)" class="text-[#53150f]/60 hover:text-red-700 focus:outline-none text-xs font-black">&times;</button>
                        </span>
                    </template>
                </div>
                <div class="px-1 shrink-0">
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-[#8c6239] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms style="display: none;" class="absolute bottom-full mb-2 left-0 w-full rounded-xl border border-[#cdbb9f] bg-white p-3.5 shadow-2xl z-50">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 max-h-40 overflow-y-auto custom-scrollbar pr-1 pb-2">
                    <template x-for="(label, value) in dict" :key="value">
                        <label class="flex items-center gap-2 p-1.5 rounded-lg cursor-pointer hover:bg-[#f4f1e8] transition-colors text-xs select-none">
                            <input type="checkbox" :value="value" x-model="combat.vulnerabilities" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-3.5 h-3.5">
                            <span class="text-[#53150f] font-semibold" x-text="label"></span>
                        </label>
                    </template>
                </div>
                <div class="flex gap-2 pt-2.5 border-t border-[#cdbb9f]/40" @click.stop>
                    <input type="text" x-model="customItem" @keydown.enter.prevent="if(customItem.trim()) { combat.vulnerabilities.push(customItem.trim()); customItem = ''; }" placeholder="Vulnerabilidade customizada..." 
                        class="h-8 py-1 w-full rounded-lg border border-[#cdbb9f] bg-[#fbf9f4] px-3 text-xs font-bold text-[#2b1d17] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#6b1d14]">
                    <button type="button" @click="if(customItem.trim()) { combat.vulnerabilities.push(customItem.trim()); customItem = ''; }" class="h-8 rounded-lg bg-[#8a2519] px-4 text-[10px] font-black uppercase text-[#f4f1e8] hover:bg-[#53150f] transition-colors shadow-sm cursor-pointer">Add</button>
                </div>
            </div>
        </div>

        <div class="border-t border-[#cdbb9f]/40"></div>

        {{-- Imunidades a Condição --}}
        <div x-data="{ open: false, customItem: '', dict: @js(\App\Support\Dictionaries\Conditions::options()) }" 
             class="relative"
             :class="{'z-50': open}">
            
            <label class="block mb-1.5 text-[10px] font-bold uppercase tracking-wider text-[#8c6239] pl-1">
                Imunidades a Condição (Condition Immunities)
            </label>
            
            <div @click="open = !open" 
                class="min-h-[38px] w-full rounded-xl border border-[#cdbb9f]/80 bg-white flex items-center justify-between px-2.5 py-1.5 shadow-inner cursor-pointer hover:border-[#8c6239] transition-all duration-200"
                :class="{'ring-1 ring-[#8a2519] border-[#8a2519] bg-[#fbf9f4]': open}">
                <div class="flex flex-wrap gap-1.5 flex-1 items-center">
                    <span x-show="!combat.conditionImmunities.length" class="text-xs text-[#8c6239]/50 italic">Selecionar condições...</span>
                    <template x-for="(item, index) in combat.conditionImmunities" :key="index">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#ece6d7] border border-[#d6a56c]/70 px-2 py-0.5 text-[10px] font-bold text-[#53150f] uppercase tracking-wide shadow-xs">
                            <span x-text="dict[item] || item"></span>
                            <button type="button" @click.stop="combat.conditionImmunities.splice(index,1)" class="text-[#53150f]/60 hover:text-red-700 focus:outline-none text-xs font-black">&times;</button>
                        </span>
                    </template>
                </div>
                <div class="px-1 shrink-0">
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-[#8c6239] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms style="display: none;" class="absolute bottom-full mb-2 left-0 w-full rounded-xl border border-[#cdbb9f] bg-white p-3.5 shadow-2xl z-50">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 max-h-40 overflow-y-auto custom-scrollbar pr-1 pb-2">
                    <template x-for="(label, value) in dict" :key="value">
                        <label class="flex items-center gap-2 p-1.5 rounded-lg cursor-pointer hover:bg-[#f4f1e8] transition-colors text-xs select-none">
                            <input type="checkbox" :value="value" x-model="combat.conditionImmunities" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-3.5 h-3.5">
                            <span class="text-[#53150f] font-semibold" x-text="label"></span>
                        </label>
                    </template>
                </div>
                <div class="flex gap-2 pt-2.5 border-t border-[#cdbb9f]/40" @click.stop>
                    <input type="text" x-model="customItem" @keydown.enter.prevent="if(customItem.trim()) { combat.conditionImmunities.push(customItem.trim()); customItem = ''; }" placeholder="Condição customizada..." 
                        class="h-8 py-1 w-full rounded-lg border border-[#cdbb9f] bg-[#fbf9f4] px-3 text-xs font-bold text-[#2b1d17] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519]">
                    <button type="button" @click="if(customItem.trim()) { combat.conditionImmunities.push(customItem.trim()); customItem = ''; }" class="h-8 rounded-lg bg-[#8a2519] px-4 text-[10px] font-black uppercase text-[#f4f1e8] hover:bg-[#53150f] transition-colors shadow-sm cursor-pointer">Add</button>
                </div>
            </div>
        </div>

    </div>
</div>