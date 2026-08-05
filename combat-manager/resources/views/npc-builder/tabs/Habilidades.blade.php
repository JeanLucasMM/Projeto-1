<div class="space-y-4">

    {{-- Lista de Habilidades com Gaveta (Acordeão Fechado por Padrão) --}}
    <div class="space-y-3">
        <template x-for="(feature, index) in features" :key="feature.id">

            <div class="rounded-xl border border-[#cdbb9f]/80 bg-white shadow-xs overflow-hidden transition-all hover:shadow-md" x-data="{ open: false }">

                <!-- Cabeçalho da Gaveta -->
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-gradient-to-r from-[#fbf9f4] via-[#f4f1e8] to-[#ece6d7]/70 border-b border-[#cdbb9f]/50">

                    <div class="flex items-center gap-2.5 flex-1 min-w-[200px]">
                        <!-- Botão de Fechar/Abrir Gaveta -->
                        <button type="button" @click="open = !open" class="text-[#8c6239] hover:text-[#6b1d14] transition-colors cursor-pointer p-1 rounded-lg hover:bg-[#cdbb9f]/20 shrink-0">
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Nome da Habilidade (Vinho Tinto #6b1d14 com Placeholder Corretamente Estilizado) -->
                        <input
                            type="text"
                            x-model="feature.title"
                            placeholder="Nome da habilidade..."
                            class="w-full border-0 bg-transparent text-xs font-black text-[#6b1d14] placeholder:text-[#8a2519]/50 focus:ring-0 p-0"
                        >
                    </div>

                    <!-- Rastreador Completo e Ações -->
                    <div class="flex flex-wrap items-center gap-2.5">

                        <div class="flex items-center gap-1.5 bg-white/60 px-2 py-1 rounded-lg border border-[#cdbb9f]/50 shadow-2xs">
                            <label class="flex items-center gap-1.5 cursor-pointer group select-none">
                                <input
                                    type="checkbox"
                                    x-model="feature.tracker.enabled"
                                    class="rounded border-[#cdbb9f] text-[#8a2519] focus:ring-[#8a2519] bg-[#fbf9f4] w-3.5 h-3.5 cursor-pointer"
                                >
                                <span
                                    x-text="feature.tracker.reset === 'recharge' ? 'Recarga' : 'Usos'"
                                    class="text-[10px] font-black uppercase tracking-widest text-[#8c6239] group-hover:text-[#6b1d14] transition-colors"
                                ></span>
                            </label>

                            <div
                                x-show="feature.tracker && feature.tracker.enabled"
                                x-transition
                                class="flex items-center gap-1.5 pl-2 ml-1.5 border-l border-[#cdbb9f]/50"
                            >
                                <template x-if="feature.tracker.reset !== 'recharge'">
                                    <input
                                        type="number"
                                        min="1"
                                        placeholder="Qtd."
                                        x-model.number="feature.tracker.uses"
                                        class="w-12 py-1 px-1.5 rounded-lg border border-[#cdbb9f] bg-white text-xs font-bold text-center text-[#6b1d14] shadow-inner focus:ring-1 focus:ring-[#8a2519]"
                                    >
                                </template>

                                <template x-if="feature.tracker.reset === 'recharge'">
                                    <div class="flex items-center bg-white rounded-lg border border-[#cdbb9f] overflow-hidden shadow-inner">
                                        <input
                                            type="number"
                                            min="1"
                                            max="20"
                                            placeholder="4"
                                            x-model.number="feature.tracker.min"
                                            class="w-8 py-1 px-0.5 border-0 bg-transparent text-[10px] font-bold text-center text-[#6b1d14] focus:ring-0"
                                        >
                                        <span class="text-xs font-black text-[#8c6239]">/</span>
                                        <input
                                            type="number"
                                            min="1"
                                            max="20"
                                            placeholder="6"
                                            x-model.number="feature.tracker.max"
                                            class="w-8 py-1 px-0.5 border-0 bg-transparent text-[10px] font-bold text-center text-[#6b1d14] focus:ring-0"
                                        >
                                    </div>
                                </template>

                                <select
                                    x-model="feature.tracker.reset"
                                    class="py-1 px-2 rounded-lg border border-[#cdbb9f] bg-white text-[10px] font-semibold text-[#6b1d14] shadow-inner focus:ring-1 focus:ring-[#8a2519] cursor-pointer"
                                >
                                    <option value="">Nenhum</option>
                                    <option value="day">Dia</option>
                                    <option value="short_rest">Desc. Curto</option>
                                    <option value="long_rest">Desc. Longo</option>
                                    <option value="turn">Turno</option>
                                    <option value="custom">Criar</option>
                                    <option value="recharge">Recarga</option>
                                </select>

                                <template x-if="feature.tracker.reset === 'custom'">
                                    <input
                                        type="text"
                                        x-model="feature.tracker.customReset"
                                        placeholder="Ex.: Semana..."
                                        class="w-20 py-1 px-2 rounded-lg border border-[#cdbb9f] bg-white text-[10px] font-bold text-[#6b1d14] shadow-inner focus:ring-1 focus:ring-[#8a2519]"
                                    >
                                </template>
                            </div>
                        </div>

                        {{-- Botão Remover --}}
                        <button
                            type="button"
                            @click="removeFeature(index)"
                            class="text-[#8c6239] hover:text-red-700 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-1 border-l border-[#cdbb9f]/60 pl-3 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span class="hidden sm:inline">Remover</span>
                        </button>

                    </div>

                </div>

                {{-- Conteúdo Retrátil (Editor Tiptap dentro da Gaveta) --}}
                <div x-show="open" x-transition.opacity.duration.200ms class="p-4 bg-white border-t border-[#cdbb9f]/30">
                    <div class="relative rounded-xl border border-[#cdbb9f]/60 bg-[#fbf9f4] shadow-inner focus-within:ring-1 focus-within:ring-[#8a2519] focus-within:border-[#8a2519] transition-all">
                        <div wire:ignore>
                            <div
                                :id="'features-editor-' + feature.id"
                                class="min-h-[120px] p-3 prose prose-sm max-w-none prose-p:m-0 prose-p:leading-relaxed text-[#53150f] outline-none focus:outline-none [&>.tiptap]:outline-none text-xs"
                            ></div>
                        </div>
                    </div>
                </div>

            </div>

        </template>
    </div>
</div>