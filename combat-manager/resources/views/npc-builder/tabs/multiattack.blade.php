<div class="space-y-3">
    <template x-for="(multiAttack, index) in multiAttacks" :key="multiAttack.id">
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

                    {{-- Nome do Multiataque --}}
                    <input
                        type="text"
                        x-model="multiAttack.title"
                        placeholder="Nome do multiataque..."
                        class="w-full border-0 bg-transparent text-sm font-black text-[#6b1d14] placeholder:text-[#8a2519]/40 focus:ring-0 p-0 outline-none"
                    >
                </div>

                {{-- Controles do Cabeçalho --}}
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#8c6239] hidden sm:inline" x-text="multiAttack.mode === 'automatic' ? 'Automático' : 'Texto Livre'"></span>
                    
                    <div class="w-px h-5 bg-[#cdbb9f]/40 hidden sm:block"></div>

                    <button
                        type="button"
                        @click="removeMultiAttack(index)"
                        title="Remover Multiataque"
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
                            <input type="radio" value="automatic" x-model="multiAttack.mode" class="peer sr-only">
                            <div class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-[#8c6239] peer-checked:bg-white peer-checked:text-[#6b1d14] peer-checked:shadow-xs transition-all">
                                Automático
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" value="custom" x-model="multiAttack.mode" class="peer sr-only">
                            <div class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-[#8c6239] peer-checked:bg-white peer-checked:text-[#6b1d14] peer-checked:shadow-xs transition-all">
                                Texto Livre
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Seção: Automático --}}
                <div x-show="multiAttack.mode === 'automatic'" class="p-4 sm:p-5 space-y-4 bg-[#fcfaf7]">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black uppercase tracking-widest text-[#6b1d14] flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Sequência de Ações
                        </h4>
                        <button
                            type="button"
                            @click="addMultiAttackEntry(multiAttack)"
                            class="px-3 py-1.5 rounded-lg bg-[#ece6d7] text-[#6b1d14] text-[10px] font-black uppercase tracking-widest border border-[#cdbb9f]/60 hover:bg-[#cdbb9f]/30 transition-colors cursor-pointer shadow-xs"
                        >
                            + Adicionar Ação
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="(entry, entryIndex) in multiAttack.entries" :key="entry.id || entryIndex">
                            <div class="bg-white rounded-xl border border-[#cdbb9f]/50 p-3.5 shadow-2xs space-y-3 relative group">
                                
                                <div class="absolute top-2.5 right-2.5 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        type="button"
                                        @click="removeMultiAttackEntry(multiAttack, entryIndex)"
                                        title="Remover Ação"
                                        class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors cursor-pointer shadow-xs"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end pr-6 md:pr-0">
                                    <div class="md:col-span-9 space-y-1">
                                        <label class="block text-[9px] font-black uppercase text-[#8c6239]">Ataque / Ação</label>
                                        <select
                                            class="w-full py-2 px-3 rounded-lg border border-[#cdbb9f]/60 bg-white text-xs font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519] outline-none cursor-pointer"
                                            :value="JSON.stringify({ type: entry.source, id: entry.sourceId })"
                                            @change="
                                                const selected = JSON.parse($event.target.value);
                                                entry.source = selected.type;
                                                entry.sourceId = selected.id;
                                            "
                                        >
                                            <option value="">Selecione...</option>
                                            <template x-for="attack in attacks" :key="'att-' + attack.id">
                                                <option
                                                    :value="JSON.stringify({ type: 'attack', id: attack.id })"
                                                    x-text="'⚔️ ' + (attack.title || 'Ataque sem nome')"
                                                ></option>
                                            </template>
                                            <template x-for="actionOption in actions" :key="'act-' + actionOption.id">
                                                <option
                                                    :value="JSON.stringify({ type: 'action', id: actionOption.id })"
                                                    x-text="'⚡ ' + (actionOption.title || 'Ação sem nome')"
                                                ></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="md:col-span-3 space-y-1">
                                        <label class="block text-[9px] font-black uppercase text-[#8c6239]">Qtd</label>
                                        <input type="number" min="1" x-model.number="entry.quantity" class="w-full py-2 px-2 rounded-lg border border-[#cdbb9f]/60 text-xs font-bold text-center text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519]">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!multiAttack.entries || multiAttack.entries.length === 0">
                            <div class="flex flex-col items-center justify-center py-6 px-4 text-center border border-dashed border-[#cdbb9f]/60 rounded-xl bg-white/50 cursor-pointer hover:bg-[#ece6d7]/30 transition-colors" @click="addMultiAttackEntry(multiAttack)">
                                <span class="text-xs font-bold text-[#8c6239] mb-1">Nenhuma ação adicionada neste multiataque.</span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-[#8a2519] hover:underline">Clique para adicionar.</span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Seção: Personalizado (Texto Livre) --}}
                <div x-show="multiAttack.mode === 'custom'" class="p-4 sm:p-5 bg-[#fcfaf7]">
                    <div class="relative rounded-xl border border-[#cdbb9f]/60 bg-[#fbf9f4] shadow-inner focus-within:ring-1 focus-within:ring-[#8a2519] transition-all">
                        <textarea
                            x-model="multiAttack.customText"
                            rows="4"
                            placeholder="Ex.: O dragão realiza dois ataques de Garra e um de Mordida."
                            class="w-full border-0 bg-transparent p-4 text-sm text-[#53150f] resize-y focus:ring-0 outline-none placeholder:text-[#8a2519]/40"
                        ></textarea>
                    </div>
                </div>

            </div>
        </div>
    </template>
</div>