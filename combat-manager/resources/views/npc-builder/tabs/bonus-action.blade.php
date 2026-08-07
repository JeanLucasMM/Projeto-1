<div class="space-y-4">

    {{-- Lista de Ações Bônus com Gaveta (Acordeão Fechado por Padrão) --}}
    <div class="space-y-3">
        <template x-for="(action, index) in bonusActions" :key="action.id">
            <div x-data="{ open: false }" class="rounded-xl border border-[#cdbb9f]/80 bg-white shadow-xs overflow-hidden transition-all hover:shadow-md">

                {{-- CABEÇALHO DA GAVETA --}}
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-gradient-to-r from-[#fbf9f4] via-[#f4f1e8] to-[#ece6d7]/70 border-b border-[#cdbb9f]/50">
                    
                    <div class="flex items-center gap-2.5 flex-1 min-w-[200px]">
                        {{-- Botão de Fechar/Abrir Gaveta --}}
                        <button type="button" @click="open = !open" class="text-[#8c6239] hover:text-[#6b1d14] transition-colors cursor-pointer p-1 rounded-lg hover:bg-[#cdbb9f]/20 shrink-0">
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Nome da Ação Bônus --}}
                        <input
                            type="text"
                            x-model="action.title"
                            placeholder="Nome da ação bônus..."
                            class="w-full border-0 bg-transparent text-xs font-black text-[#6b1d14] placeholder:text-[#8a2519]/50 focus:ring-0 p-0"
                        >
                    </div>

                    {{-- Controles do Cabeçalho --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        
                        {{-- Tipo --}}
                        <select
                            x-model="action.type"
                            class="py-1 px-2 rounded-lg border border-[#cdbb9f] bg-white text-[10px] font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] cursor-pointer shadow-2xs"
                        >
                            <option value="normal">Ação Bônus</option>
                            <option value="spellcasting">Conjuração</option>
                        </select>

                        {{-- Rastreador Agrupado --}}
                        <div class="flex items-center gap-1.5 bg-white/60 px-2 py-1 rounded-lg border border-[#cdbb9f]/50 shadow-2xs">
                            <label class="flex items-center gap-1.5 cursor-pointer group select-none">
                                <input
                                    type="checkbox"
                                    x-model="action.tracker.enabled"
                                    class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] bg-[#fbf9f4] w-3.5 h-3.5 cursor-pointer"
                                >
                                <span 
                                    class="text-[10px] font-black uppercase tracking-widest text-[#8c6239] group-hover:text-[#6b1d14] transition-colors"
                                    x-text="action.tracker.reset === 'recharge' ? 'Recarga' : 'Usos'"
                                ></span>
                            </label>

                            <div
                                x-show="action.tracker && action.tracker.enabled"
                                x-transition
                                class="flex items-center gap-1.5 pl-2 ml-1.5 border-l border-[#cdbb9f]/50"
                            >
                                <template x-if="action.tracker.reset !== 'recharge'">
                                    <input
                                        type="number" min="0"
                                        x-model.number="action.tracker.uses"
                                        placeholder="Qtd."
                                        class="w-12 py-1 px-1.5 rounded-lg border border-[#cdbb9f] bg-white text-xs font-bold text-center text-[#6b1d14] shadow-inner focus:ring-1 focus:ring-[#8a2519]"
                                    >
                                </template>

                                <template x-if="action.tracker.reset === 'recharge'">
                                    <div class="flex items-center bg-white rounded-lg border border-[#cdbb9f] overflow-hidden shadow-inner">
                                        <input type="number" min="1" x-model.number="action.tracker.min" class="w-8 py-1 px-0.5 border-0 bg-transparent text-[10px] font-bold text-center text-[#6b1d14] focus:ring-0">
                                        <span class="text-[#8c6239] text-[10px] font-black">/</span>
                                        <input type="number" min="1" x-model.number="action.tracker.max" class="w-8 py-1 px-0.5 border-0 bg-transparent text-[10px] font-bold text-center text-[#6b1d14] focus:ring-0">
                                    </div>
                                </template>

                                <select
                                    x-model="action.tracker.reset"
                                    class="py-1 px-2 rounded-lg border border-[#cdbb9f] bg-white text-[10px] font-semibold text-[#6b1d14] shadow-inner focus:ring-1 focus:ring-[#8a2519] cursor-pointer"
                                >
                                    <option value="">Nenhum</option>
                                    <option value="day">Dia</option>
                                    <option value="short_rest">Desc. Curto</option>
                                    <option value="long_rest">Desc. Longo</option>
                                    <option value="turn">Turno</option>
                                    <option value="recharge">Recarga</option>
                                    <option value="custom">Outro</option>
                                </select>

                                <template x-if="action.tracker.reset === 'custom'">
                                    <input
                                        type="text" x-model="action.tracker.customReset" placeholder="Ex: Semana"
                                        class="w-20 py-1 px-2 rounded-lg border border-[#cdbb9f] bg-white text-[10px] font-bold text-[#6b1d14] shadow-inner focus:ring-1 focus:ring-[#8a2519]"
                                    >
                                </template>
                            </div>
                        </div>

                        {{-- Remover --}}
                        <button
                            type="button"
                            @click="removeBonusAction(index)"
                            title="Remover Ação Bônus"
                            class="text-[#8c6239] hover:text-red-700 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-1 border-l border-[#cdbb9f]/60 pl-3 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="hidden sm:inline">Remover</span>
                        </button>
                    </div>
                </div>

                {{-- CORPO DA GAVETA (Painel de Conjuração + Editor) --}}
                <div x-show="open" x-transition.opacity.duration.200ms class="border-t border-[#cdbb9f]/30">

                    {{-- PAINEL DE CONJURAÇÃO (Caso aplicável) --}}
                    <div
                        x-show="action.type === 'spellcasting'"
                        x-collapse
                        class="p-4 bg-[#f8f5ee] border-b border-[#cdbb9f]/50 space-y-4"
                    >
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Nível do Conjurador</label>
                                <input type="number" min="1" x-model.number="action.spellcasting.casterLevel" class="w-full py-1.5 px-2.5 rounded-lg border border-[#cdbb9f] bg-white text-xs font-bold text-[#6b1d14] focus:border-[#8a2519] focus:ring-1 focus:ring-[#8a2519] shadow-inner">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Atributo Chave</label>
                                <select x-model="action.spellcasting.ability" class="w-full py-1.5 px-2.5 rounded-lg border border-[#cdbb9f] bg-white text-xs font-bold text-[#6b1d14] focus:border-[#8a2519] focus:ring-1 focus:ring-[#8a2519] shadow-inner cursor-pointer">
                                    <option value="int">Inteligência</option>
                                    <option value="wis">Sabedoria</option>
                                    <option value="cha">Carisma</option>
                                    <option value="con">Constituição</option>
                                    <option value="dex">Destreza</option>
                                    <option value="str">Força</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Bônus de Acerto</label>
                                <input type="number" x-model.number="action.spellcasting.attackBonusExtra" placeholder="+0" class="w-full py-1.5 px-2.5 rounded-lg border border-[#cdbb9f] bg-white text-xs font-bold text-[#6b1d14] focus:border-[#8a2519] focus:ring-1 focus:ring-[#8a2519] shadow-inner">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black uppercase text-[#8c6239]">Bônus de CD</label>
                                <input type="number" x-model.number="action.spellcasting.saveDCExtra" placeholder="+0" class="w-full py-1.5 px-2.5 rounded-lg border border-[#cdbb9f] bg-white text-xs font-bold text-[#6b1d14] focus:border-[#8a2519] focus:ring-1 focus:ring-[#8a2519] shadow-inner">
                            </div>
                        </div>

                        {{-- CÍRCULOS DE MAGIA --}}
                        <div class="pt-4 border-t border-[#cdbb9f]/40 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] font-black uppercase tracking-widest text-[#6b1d14] flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    Círculos de Magia
                                </h4>
                                <button
                                    type="button"
                                    @click="addSpellSlot(action)"
                                    class="px-2.5 py-1 rounded-lg bg-[#ece6d7] text-[#6b1d14] text-[10px] font-black uppercase tracking-widest border border-[#cdbb9f] hover:bg-[#cdbb9f]/30 transition-colors cursor-pointer shadow-2xs"
                                >
                                    + Adicionar Círculo
                                </button>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(slot, slotIndex) in action.spellcasting.slots" :key="slot.id || slotIndex">
                                    <div class="bg-white rounded-lg border border-[#cdbb9f]/70 p-3 shadow-2xs space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                            <div class="md:col-span-2 space-y-1">
                                                <label class="block text-[9px] font-black uppercase text-[#8c6239]">Nível</label>
                                                <input type="number" min="0" max="9" x-model.number="slot.level" class="w-full py-1 px-2 rounded border border-[#cdbb9f] text-xs font-bold text-center focus:ring-1 focus:ring-[#8a2519]">
                                            </div>

                                            <div class="md:col-span-2 space-y-1">
                                                <label class="block text-[9px] font-black uppercase text-[#8c6239]">Usos</label>
                                                <input type="number" min="0" x-model.number="slot.uses" class="w-full py-1 px-2 rounded border border-[#cdbb9f] text-xs font-bold text-center focus:ring-1 focus:ring-[#8a2519]">
                                            </div>

                                            <div class="md:col-span-3 space-y-1">
                                                <label class="block text-[9px] font-black uppercase text-[#8c6239]">Recuperação</label>
                                                <select x-model="slot.reset" class="w-full py-1 px-2 rounded border border-[#cdbb9f] text-xs font-bold focus:ring-1 focus:ring-[#8a2519] cursor-pointer">
                                                    <option value="day">Dia</option>
                                                    <option value="short_rest">Desc. Curto</option>
                                                    <option value="long_rest">Desc. Longo</option>
                                                    <option value="turn">Turno</option>
                                                    <option value="custom">Personalizado</option>
                                                </select>
                                            </div>

                                            <div class="md:col-span-4 space-y-1">
                                                <label class="block text-[9px] font-black uppercase text-[#8c6239]">Magias Conhecidas</label>
                                                <input type="text" x-model="slot.spells" placeholder="Ex: Míssil Mágico..." class="w-full py-1 px-2 rounded border border-[#cdbb9f] text-xs text-[#53150f] focus:ring-1 focus:ring-[#8a2519]">
                                            </div>

                                            <div class="md:col-span-1 flex justify-end">
                                                <button
                                                    type="button"
                                                    @click="removeSpellSlot(action, slotIndex)"
                                                    title="Remover Círculo"
                                                    class="p-1.5 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!action.spellcasting.slots || action.spellcasting.slots.length === 0">
                                    <div class="flex flex-col items-center justify-center py-6 px-4 text-center border border-dashed border-[#cdbb9f]/60 rounded-xl bg-white/50">
                                        <span class="text-xs font-bold text-[#8c6239] mb-2">Nenhum círculo de magia cadastrado.</span>
                                        <button type="button" @click="addSpellSlot(action)" class="text-[10px] font-black uppercase tracking-widest text-[#8a2519] hover:underline cursor-pointer">
                                            Adicionar o primeiro círculo
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- EDITOR DE DESCRIÇÃO (Tiptap) --}}
                    <div class="p-4 bg-white">
                        <div class="relative rounded-xl border border-[#cdbb9f]/60 bg-[#fbf9f4] shadow-inner focus-within:ring-1 focus-within:ring-[#8a2519] transition-all">
                            <div wire:ignore>
                                <div
                                    :id="'bonusActions-editor-' + action.id"
                                    class="min-h-[120px] p-3 prose prose-sm max-w-none prose-p:m-0 prose-p:leading-relaxed text-[#53150f] outline-none [&>.tiptap]:outline-none text-xs"
                                ></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </template>
    </div>

</div>