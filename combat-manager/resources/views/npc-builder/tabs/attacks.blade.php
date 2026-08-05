<div class="space-y-3">
    <template x-for="(attack, index) in attacks" :key="attack.id">
        <div x-data="{ open: false }" class="rounded-xl border border-[#cdbb9f]/60 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
            
            {{-- CABEÇALHO DA GAVETA --}}
            <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-3 bg-[#fbf9f4] border-b border-[#cdbb9f]/30">
                <div class="flex items-center gap-3 flex-1 min-w-[220px]">
                    {{-- Botão de Fechar/Abrir Gaveta --}}
                    <button type="button" @click="open = !open" class="text-[#8c6239] hover:text-[#6b1d14] transition-colors cursor-pointer p-1.5 rounded-lg hover:bg-[#cdbb9f]/20 shrink-0 outline-none">
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Nome do Ataque --}}
                    <input
                        type="text"
                        x-model="attack.title"
                        placeholder="Nome do ataque..."
                        class="w-full border-0 bg-transparent text-sm font-black text-[#6b1d14] placeholder:text-[#8a2519]/40 focus:ring-0 p-0 outline-none"
                    >
                </div>

                {{-- Controles do Cabeçalho --}}
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#8c6239] hidden sm:inline" x-text="attack.mode === 'builder' ? 'Estruturado' : 'Texto Livre'"></span>
                    
                    <div class="w-px h-5 bg-[#cdbb9f]/40 hidden sm:block"></div>

                    <button
                        type="button"
                        @click="removeAttack(index)"
                        title="Remover Ataque"
                        class="text-[#8c6239] hover:text-red-600 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-1.5 cursor-pointer outline-none"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="hidden sm:inline">Remover</span>
                    </button>
                </div>
            </div>

            {{-- CORPO DA GAVETA --}}
            <div x-show="open" x-collapse class="border-t border-[#cdbb9f]/30 bg-white">
                
                {{-- Controle de Abas (Modo de Construção) --}}
                <div class="flex justify-center p-3 bg-[#fcfaf7] border-b border-[#cdbb9f]/30">
                    <div class="inline-flex bg-[#ece6d7]/60 p-1 rounded-xl shadow-2xs gap-1">
                        <label class="cursor-pointer relative">
                            <input type="radio" value="builder" x-model="attack.mode" class="peer sr-only">
                            <div class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-[#8c6239] peer-checked:bg-white peer-checked:text-[#6b1d14] peer-checked:shadow-xs transition-all">
                                Estruturado
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" value="text" x-model="attack.mode" class="peer sr-only">
                            <div class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-[#8c6239] peer-checked:bg-white peer-checked:text-[#6b1d14] peer-checked:shadow-xs transition-all">
                                Texto Livre
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Seção: Texto Livre --}}
                <div x-show="attack.mode === 'text'" class="p-4 sm:p-5">
                    <div class="relative rounded-xl border border-[#cdbb9f]/60 bg-[#fbf9f4] shadow-inner focus-within:ring-1 focus-within:ring-[#8a2519] transition-all">
                        <div wire:ignore>
                            <div
                                :id="'attacks-editor-'+attack.id"
                                class="min-h-[140px] p-4 prose prose-sm max-w-none prose-p:m-0 prose-p:leading-relaxed text-[#53150f] outline-none [&>.tiptap]:outline-none text-sm"
                            ></div>
                        </div>
                    </div>
                </div>

                {{-- Seção: Builder (Estruturado) --}}
                <div x-show="attack.mode === 'builder'" class="p-4 sm:p-5 space-y-5 bg-[#fcfaf7]">
                    
                    {{-- Parâmetros Principais --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-widest text-[#6b1d14] flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Parâmetros do Ataque
                        </h4>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Tipo</label>
                                <select x-model="attack.builder.attackType" class="w-full py-2 px-3 rounded-lg border border-[#cdbb9f]/60 bg-white text-sm font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] outline-none cursor-pointer">
                                    <option value="weapon">Arma</option>
                                    <option value="spell">Magia</option>
                                    <option value="feature">Desarmado</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Alcance</label>
                                <select x-model="attack.builder.range" class="w-full py-2 px-3 rounded-lg border border-[#cdbb9f]/60 bg-white text-sm font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] outline-none cursor-pointer">
                                    <option value="melee">Corpo a Corpo</option>
                                    <option value="ranged">À Distância</option>
                                    <option value="both">Ambos</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Atributo Chave</label>
                                <select x-model="attack.builder.attackAbility" class="w-full py-2 px-3 rounded-lg border border-[#cdbb9f]/60 bg-white text-sm font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] outline-none cursor-pointer">
                                    <option value="str">Força (STR)</option>
                                    <option value="dex">Destreza (DEX)</option>
                                    <option value="con">Constituição (CON)</option>
                                    <option value="int">Inteligência (INT)</option>
                                    <option value="wis">Sabedoria (WIS)</option>
                                    <option value="cha">Carisma (CHA)</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Proficiência</label>
                                <label class="h-[38px] px-3 rounded-lg border border-[#cdbb9f]/60 flex items-center justify-center gap-2 cursor-pointer bg-white hover:bg-[#ece6d7]/30 transition-colors">
                                    <input type="checkbox" x-model="attack.builder.proficiency" class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] w-4 h-4">
                                    <span class="text-xs font-bold text-[#6b1d14]">Adicionar</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Alvo(s)</label>
                                <input type="text" x-model="attack.builder.targets" placeholder="Ex: Uma criatura..." class="w-full py-2 px-3 rounded-lg border border-[#cdbb9f]/60 bg-white text-sm text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Distância (ft)</label>
                                <input type="number" x-model.number="attack.builder.reach" placeholder="5" class="w-full py-2 px-3 rounded-lg border border-[#cdbb9f]/60 bg-white text-sm font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Bônus Extra (+)</label>
                                <input type="number" x-model.number="attack.builder.extraHitBonus" placeholder="0" class="w-full py-2 px-3 rounded-lg border border-[#cdbb9f]/60 bg-white text-sm font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] outline-none">
                            </div>
                        </div>
                    </div>

                    {{-- Seção de Danos --}}
                    <div class="pt-5 border-t border-[#cdbb9f]/30 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-widest text-[#6b1d14] flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Rolagens de Dano
                            </h4>
                        </div>

                        <div class="space-y-2.5">
                            <template x-for="(damage, dmgIndex) in attack.builder.damages" :key="damage.id || dmgIndex">
                                <div class="bg-white rounded-xl border border-[#cdbb9f]/50 p-3.5 shadow-2xs space-y-3 relative group">
                                    
                                    <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button
                                            type="button"
                                            @click="attack.builder.damages.splice(dmgIndex,1)"
                                            class="p-1 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-12 gap-3 items-end pr-6 md:pr-0">
                                        <div class="md:col-span-2 space-y-1">
                                            <label class="block text-[9px] font-black uppercase text-[#8c6239]">Qtd</label>
                                            <input type="number" x-model.number="damage.count" class="w-full py-1.5 px-2 rounded-lg border border-[#cdbb9f]/60 text-xs font-bold text-center focus:ring-1 focus:ring-[#8a2519]">
                                        </div>

                                        <div class="md:col-span-2 space-y-1">
                                            <label class="block text-[9px] font-black uppercase text-[#8c6239]">Dado</label>
                                            <select x-model="damage.die" class="w-full py-1.5 px-2 rounded-lg border border-[#cdbb9f]/60 text-xs font-bold focus:ring-1 focus:ring-[#8a2519]">
                                                <option value="d4">d4</option>
                                                <option value="d6">d6</option>
                                                <option value="d8">d8</option>
                                                <option value="d10">d10</option>
                                                <option value="d12">d12</option>
                                                <option value="d20">d20</option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-4 space-y-1">
                                            <label class="block text-[9px] font-black uppercase text-[#8c6239]">Tipo de Dano</label>
                                            <select x-model="damage.type" class="w-full py-1.5 px-2 rounded-lg border border-[#cdbb9f]/60 text-xs font-bold focus:ring-1 focus:ring-[#8a2519]">
                                                @foreach(\App\Support\Dictionaries\DamageTypes::options() as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="md:col-span-2 space-y-1">
                                            <label class="block text-[9px] font-black uppercase text-[#8c6239]">Atributo</label>
                                            <select x-model="damage.ability" class="w-full py-1.5 px-2 rounded-lg border border-[#cdbb9f]/60 text-xs font-bold focus:ring-1 focus:ring-[#8a2519]">
                                                <option value="none">Nenhum</option>
                                                <option value="str">STR</option>
                                                <option value="dex">DEX</option>
                                                <option value="con">CON</option>
                                                <option value="int">INT</option>
                                                <option value="wis">WIS</option>
                                                <option value="cha">CHA</option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-2 space-y-1">
                                            <label class="block text-[9px] font-black uppercase text-[#8c6239]">Extra (+)</label>
                                            <input type="number" x-model.number="damage.extra" placeholder="0" class="w-full py-1.5 px-2 rounded-lg border border-[#cdbb9f]/60 text-xs font-bold text-center focus:ring-1 focus:ring-[#8a2519]">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="flex flex-col items-center justify-center py-6 px-4 text-center border border-dashed border-[#cdbb9f]/60 rounded-xl bg-white/50 cursor-pointer hover:bg-[#ece6d7]/30 transition-colors" @click="addAttackDamage(attack)">
                                <template x-if="!attack.builder.damages || attack.builder.damages.length === 0">
                                    <span class="text-xs font-bold text-[#8c6239] mb-1">Nenhum dano configurado.</span>
                                </template>
                                <span class="text-[10px] font-black uppercase tracking-widest text-[#8a2519] hover:underline" x-text="attack.builder.damages.length === 0 ? 'Clique para adicionar.' : '+ Adicionar Outro Dano'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Seção de Efeitos Adicionais --}}
                    <div class="pt-5 border-t border-[#cdbb9f]/30 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-widest text-[#6b1d14] flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                Efeitos Adicionais
                            </h4>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(effect, effectIndex) in attack.builder.effects" :key="effect.id">
                                <div class="bg-white rounded-xl border border-[#cdbb9f]/50 shadow-2xs relative overflow-hidden">
                                    <div class="absolute top-2.5 right-2.5 z-10">
                                        <button
                                            type="button"
                                            @click="attack.builder.effects.splice(effectIndex,1)"
                                            class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors cursor-pointer shadow-xs"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    <div wire:ignore>
                                        <div
                                            :id="'attacks-effect-' + attack.id + '-' + effect.id"
                                            class="min-h-[100px] p-4 pt-8 prose prose-sm max-w-none text-[#53150f] outline-none [&>.tiptap]:outline-none text-xs"
                                        ></div>
                                    </div>
                                </div>
                            </template>

                            <div class="flex flex-col items-center justify-center py-6 px-4 text-center border border-dashed border-[#cdbb9f]/60 rounded-xl bg-white/50 cursor-pointer hover:bg-[#ece6d7]/30 transition-colors" @click="addAttackEffect(attack)">
                                <template x-if="!attack.builder.effects || attack.builder.effects.length === 0">
                                    <span class="text-xs font-bold text-[#8c6239] mb-1">Sem efeitos adicionais.</span>
                                </template>
                                <span class="text-[10px] font-black uppercase tracking-widest text-[#8a2519] hover:underline" x-text="attack.builder.effects.length === 0 ? 'Clique para adicionar.' : '+ Adicionar Outro Efeito'"></span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </template>
</div>