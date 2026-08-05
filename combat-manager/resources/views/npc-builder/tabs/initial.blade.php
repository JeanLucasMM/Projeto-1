<div class="space-y-4 font-sans text-[11px]">

    <!-- Linha 1: Nome, Desafio (CR), Proficiência e XP -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-6">
            <label class="block mb-1.5 text-[9px] font-black text-[#53150f] uppercase tracking-wider">Nome</label>
            <input type="text" x-model="header.name" placeholder="Nome do NPC..." 
                class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2.5 text-[11px] font-medium text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] focus:border-[#8a2519] transition-all duration-200">
        </div>

        <div class="md:col-span-2">
            <label class="block mb-1.5 text-[9px] font-black text-[#53150f] uppercase tracking-wider">Desafio (CR)</label>
            @php
                $challengeRatings = [
                    '0','1/8','1/4','1/2','1','2','3','4','5','6',
                    '7','8','9','10','11','12','13','14','15','16',
                    '17','18','19','20','21','22','23','24','25',
                    '26','27','28','29','30',
                ];
            @endphp
            <select x-model="header.challengeRating" 
                class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2 text-[11px] font-semibold text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] focus:border-[#8a2519] transition-all duration-200">
                @foreach($challengeRatings as $cr)
                    <option value="{{ $cr }}">{{ $cr }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2 h-9 rounded-md border border-[#cdbb9f] bg-gradient-to-b from-[#f4f1e8] to-[#ece6d7] flex items-center justify-between px-2.5 shadow-sm">
            <span class="text-[8px] uppercase font-bold text-[#8c6239] tracking-wider">Prof.</span>
            <span class="text-xs font-black text-[#8a2519]" x-text="'+' + proficiencyBonus"></span>
        </div>

        <div class="md:col-span-2 h-9 rounded-md border border-[#cdbb9f] bg-gradient-to-b from-[#f4f1e8] to-[#ece6d7] flex items-center justify-between px-2.5 shadow-sm">
            <span class="text-[8px] uppercase font-bold text-[#8c6239] tracking-wider">XP</span>
            <span class="text-[11px] font-black text-[#8a2519] truncate" x-text="xp"></span>
        </div>
    </div>

    <!-- Linha 2: Tamanho, Tipos e Alinhamentos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block mb-1.5 text-[9px] font-black text-[#53150f] uppercase tracking-wider">Tamanho</label>
            <select x-model="header.size" @change="updateSize()" 
                class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2.5 text-[11px] text-[#53150f] font-medium shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] transition-all duration-200">
                @foreach(\App\Support\Dictionaries\NpcSizes::options() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tipos (Seleção Inline) -->
        <div x-data="{ openTypes: false, dictTypes: @js(\App\Support\Dictionaries\NpcTypes::options()) }" class="relative">
            <label class="block mb-1.5 text-[9px] font-black text-[#53150f] uppercase tracking-wider">Tipos</label>
            <div @click="openTypes = !openTypes" 
                class="min-h-[36px] w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] flex items-center justify-between shadow-inner cursor-pointer hover:border-[#8c6239] transition-all duration-200"
                :class="{'ring-1 ring-[#8a2519] border-[#8a2519]': openTypes}">
                <div class="flex flex-wrap gap-1 p-1 flex-1">
                    <span x-show="!header.types.length" class="text-[11px] text-gray-400 px-1.5 py-0.5">Selecionar...</span>
                    <template x-for="(type, index) in header.types" :key="index">
                        <span class="inline-flex items-center gap-1 rounded bg-[#ece6d7] border border-[#d6a56c] px-1.5 py-0.5 text-[9px] font-bold text-[#53150f] uppercase tracking-wide shadow-sm">
                            <span x-text="dictTypes[type] || type"></span>
                            <button type="button" @click.stop="header.types.splice(index, 1)" class="hover:text-red-700 focus:outline-none">&times;</button>
                        </span>
                    </template>
                </div>
                <div class="px-2 shrink-0">
                    <svg :class="{'rotate-180': openTypes}" class="w-3.5 h-3.5 text-[#8c6239] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            
            <div x-show="openTypes" @click.away="openTypes = false" x-transition.opacity.duration.200ms class="absolute top-full mt-1.5 left-0 w-full rounded-lg border border-[#cdbb9f] bg-white p-2 shadow-xl z-50">
                <div class="grid grid-cols-1 gap-0.5 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                    @foreach(\App\Support\Dictionaries\NpcTypes::options() as $value => $label)
                        <label class="flex items-center gap-2 p-1.5 rounded cursor-pointer hover:bg-[#f4f1e8] transition-colors text-[11px]">
                            <input type="checkbox" value="{{ $value }}" x-model="header.types" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-3.5 h-3.5">
                            <span class="text-[#53150f] font-medium">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Alinhamentos (Seleção Inline) -->
        <div x-data="{ openAlignments: false, dictAln: @js(\App\Support\Dictionaries\Alignments::options()) }" class="relative">
            <label class="block mb-1.5 text-[9px] font-black text-[#53150f] uppercase tracking-wider">Alinhamentos</label>
            <div @click="openAlignments = !openAlignments" 
                class="min-h-[36px] w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] flex items-center justify-between shadow-inner cursor-pointer hover:border-[#8c6239] transition-all duration-200"
                :class="{'ring-1 ring-[#8a2519] border-[#8a2519]': openAlignments}">
                <div class="flex flex-wrap gap-1 p-1 flex-1">
                    <span x-show="!header.alignments.length" class="text-[11px] text-gray-400 px-1.5 py-0.5">Selecionar...</span>
                    <template x-for="(aln, index) in header.alignments" :key="index">
                        <span class="inline-flex items-center gap-1 rounded bg-[#ece6d7] border border-[#d6a56c] px-1.5 py-0.5 text-[9px] font-bold text-[#53150f] uppercase tracking-wide shadow-sm">
                            <span x-text="dictAln[aln] || aln"></span>
                            <button type="button" @click.stop="header.alignments.splice(index, 1)" class="hover:text-red-700 focus:outline-none">&times;</button>
                        </span>
                    </template>
                </div>
                <div class="px-2 shrink-0">
                    <svg :class="{'rotate-180': openAlignments}" class="w-3.5 h-3.5 text-[#8c6239] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <div x-show="openAlignments" @click.away="openAlignments = false" x-transition.opacity.duration.200ms class="absolute top-full mt-1.5 left-0 w-full rounded-lg border border-[#cdbb9f] bg-white p-2 shadow-xl z-50">
                <div class="grid grid-cols-1 gap-0.5 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                    @foreach(\App\Support\Dictionaries\Alignments::options() as $value => $label)
                        <label class="flex items-center gap-2 p-1.5 rounded cursor-pointer hover:bg-[#f4f1e8] transition-colors text-[11px]">
                            <input type="checkbox" value="{{ $value }}" x-model="header.alignments" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-3.5 h-3.5">
                            <span class="text-[#53150f] font-medium">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Linha 3: Combate -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div>
            <label class="block mb-1.5 text-[9px] font-black uppercase tracking-wider text-[#53150f]">Armadura</label>
            <input type="number" x-model.number="combat.ac_base" class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2 text-center text-[11px] font-bold text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] transition-all">
        </div>
        <div>
            <label class="block mb-1.5 text-[9px] font-black uppercase tracking-wider text-[#53150f]">Armadura Bônus</label>
            <input type="number" x-model.number="combat.ac_bonus" class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2 text-center text-[11px] font-bold text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] transition-all">
        </div>
        <div>
            <label class="block mb-1.5 text-[9px] font-black uppercase tracking-wider text-[#53150f]">Dados de Vida</label>
            <input type="number" min="1" x-model.number="combat.hit_dice_count" class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2 text-center text-[11px] font-bold text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] transition-all">
        </div>
        <div>
            <label class="block mb-1.5 text-[9px] font-black uppercase tracking-wider text-[#53150f]">Tipo Dado</label>
            <select x-model="combat.hit_die" class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-1.5 text-center text-[11px] font-bold text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] transition-all">
                <option value="d4">d4</option>
                <option value="d6">d6</option>
                <option value="d8">d8</option>
                <option value="d10">d10</option>
                <option value="d12">d12</option>
                <option value="d20">d20</option>
            </select>
        </div>
        <div>
            <label class="block mb-1.5 text-[9px] font-black uppercase tracking-wider text-[#53150f]">PV Mod.</label>
            <input readonly :value="totalHpModifier" class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#ece6d7] px-2 text-center text-[11px] font-black text-[#8a2519] shadow-inner cursor-not-allowed">
        </div>
        <div>
            <label class="block mb-1.5 text-[9px] font-black uppercase tracking-wider text-[#53150f]">PV Extra</label>
            <input type="number" x-model.number="combat.hp_mod_extra" class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2 text-center text-[11px] font-bold text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] transition-all">
        </div>
    </div>

    <!-- Linha 4: Atributos -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <template x-for="ability in [
            {key:'str',label:'FOR'}, {key:'dex',label:'DES'}, {key:'con',label:'CON'},
            {key:'int',label:'INT'}, {key:'wis',label:'SAB'}, {key:'cha',label:'CAR'}
        ]">
            <div class="bg-gradient-to-b from-[#fbf9f4] to-[#f4f1e8] border border-[#cdbb9f] rounded-xl p-2.5 text-center shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-[#8c6239] transition-all duration-300 group">
                <label class="block mb-1.5 text-[10px] font-black uppercase tracking-widest text-[#53150f]" x-text="ability.label"></label>
                <input type="number" min="1" x-model.number="abilities[ability.key]" @input="refreshPreview()" 
                    class="h-9 py-1 w-full rounded border border-[#cdbb9f] bg-white px-1 text-[13px] text-center font-black text-[#53150f] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519] focus:border-[#8a2519] transition-colors">
                <div class="mt-2 text-[10px] text-[#8c6239] font-medium group-hover:text-[#53150f] transition-colors">
                    Mod: <strong class="text-[#8a2519] text-[11px]" x-text="formatModifier(getModifier(abilities[ability.key]))"></strong>
                </div>
            </div>
        </template>
    </div>

    <!-- Linha 5: Idiomas -->
    <div x-data="{ openLanguages: false, dictLang: @js(\App\Support\Dictionaries\Languages::options()) }" class="relative">
        <label class="block mb-1.5 text-[9px] font-black text-[#53150f] uppercase tracking-wider">Idiomas</label>
        <div @click="openLanguages = !openLanguages" 
            class="min-h-[36px] w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] flex items-center justify-between shadow-inner cursor-pointer hover:border-[#8c6239] transition-all duration-200"
            :class="{'ring-1 ring-[#8a2519] border-[#8a2519]': openLanguages}">
            <div class="flex flex-wrap gap-1 p-1 flex-1">
                <span x-show="!header.languages.length" class="text-[11px] text-gray-400 px-1.5 py-0.5">Selecionar idiomas...</span>
                <template x-for="(lang, index) in header.languages" :key="index">
                    <span class="inline-flex items-center gap-1 rounded bg-[#ece6d7] border border-[#d6a56c] px-1.5 py-0.5 text-[9px] font-bold text-[#53150f] uppercase tracking-wide shadow-sm">
                        <span x-text="dictLang[lang] || lang"></span>
                        <button type="button" @click.stop="removeHeaderLanguage(index)" class="hover:text-red-700 focus:outline-none">&times;</button>
                    </span>
                </template>
            </div>
            <div class="px-2 shrink-0">
                <svg :class="{'rotate-180': openLanguages}" class="w-3.5 h-3.5 text-[#8c6239] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>

        <div x-show="openLanguages" @click.away="openLanguages = false" x-transition.opacity.duration.200ms class="absolute bottom-full mb-1.5 left-0 w-full rounded-lg border border-[#cdbb9f] bg-white p-3 shadow-xl z-50">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1 max-h-36 overflow-y-auto custom-scrollbar pr-1 pb-2">
                @foreach(\App\Support\Dictionaries\Languages::options() as $value => $label)
                    <label class="flex items-center gap-2 p-1.5 rounded cursor-pointer hover:bg-[#f4f1e8] transition-colors text-[11px]">
                        <input type="checkbox" value="{{ $value }}" x-model="header.languages" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-3.5 h-3.5">
                        <span class="text-[#53150f] font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div class="flex gap-2 pt-2 border-t border-[#cdbb9f]/40" @click.stop>
                <input type="text" x-model="header.languageCustom" @keydown.enter.prevent="addHeaderLanguage()" placeholder="Idioma customizado..." 
                    class="h-9 py-1 w-full rounded-md border border-[#cdbb9f] bg-[#fbf9f4] px-2.5 text-[11px] shadow-inner focus:outline-none focus:ring-1 focus:ring-[#8a2519]">
                <button type="button" @click="addHeaderLanguage()" class="h-9 rounded-md bg-[#8a2519] px-4 text-[10px] font-black uppercase text-[#f4f1e8] hover:bg-[#53150f] transition-colors shadow-sm">Add</button>
            </div>
        </div>
    </div>

</div>