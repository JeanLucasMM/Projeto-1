<x-app-layout>
    <style>
        .builder-scope {
            background-color: #EEE8DC;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(140, 98, 57, 0.4);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(140, 98, 57, 0.7);
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(138, 37, 25, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(138, 37, 25, 0);
            }
        }

        .btn-glow {
            animation: pulse-glow 2s infinite;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @php
        $initialNpcData = null;

        if (isset($draft) && $draft) {
            $initialNpcData = $draft instanceof \App\Models\NpcBuilderDraft
                ? $draft->json_data
                : $draft;
        } elseif (isset($npc)) {
            $initialNpcData = $npc instanceof \App\Models\Npc
                ? $npc->json_data
                : $npc;
        }
    @endphp

    <div
        class="builder-scope h-screen overflow-hidden flex flex-col font-serif selection:bg-[#6b1d14] selection:text-[#f4f1e8] relative"
        x-data="npcBuilder(
            @js($initialNpcData),
            {
                sizes: @js(\App\Support\Dictionaries\NpcSizes::options()),
                types: @js(\App\Support\Dictionaries\NpcTypes::options()),
                alignments: @js(\App\Support\Dictionaries\Alignments::options()),
                languages: @js(\App\Support\Dictionaries\Languages::options()),
                senses: @js(\App\Support\Dictionaries\Senses::options()),
                damageTypes: @js(\App\Support\Dictionaries\DamageTypes::options()),
                conditions: @js(\App\Support\Dictionaries\Conditions::options())
            },
            {
                draftSaveUrl: '{{ route('npc-builder.draft.store') }}',
                draftDeleteUrl: '{{ route('npc-builder.draft.destroy') }}'
            }
        )"
    >

        {{-- IMPORTAÇÃO DE JSON (ARQUIVO EXTERNO PARA O BUILDER) --}}
        <input
            x-ref="npcImport"
            type="file"
            accept=".json,application/json"
            class="hidden"
            @change="
                if ($event.target.files.length) {
                    loadFile($event.target.files[0])
                        .catch(error => {
                            console.error(error);
                            alert(error.message);
                        })
                        .finally(() => {
                            $event.target.value = '';
                        });
                }
            "
        >

        {{-- ============================================================
             GAVETA DE OPÇÕES
        ============================================================= --}}
        <div class="fixed bottom-4 right-6 z-50" x-data="{ openDrawer: false }">
            <div class="relative flex flex-col items-end">
                
                <div
                    x-show="openDrawer"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute bottom-full right-0 mb-2 flex flex-col gap-1 p-1 bg-[#f4f1e8]/95 backdrop-blur-md border border-[#cdbb9f]/60 rounded-xl shadow-lg min-w-[130px]"
                    x-cloak
                >
                    {{-- IMPORTAR --}}
                    <button
                        type="button"
                        @click="$refs.npcImport.click(); openDrawer = false;"
                        class="w-full text-left px-2 py-1 rounded-lg bg-[#8c6239] text-white font-bold text-[10px] hover:bg-[#704e2f] transition uppercase tracking-wider cursor-pointer"
                    >
                        Importar
                    </button>

                    {{-- LIMPAR --}}
                    <button
                        type="button"
                        @click="
                            clearDraft()
                                .then(() => { openDrawer = false; })
                                .catch(error => { console.error(error); alert(error.message); });
                        "
                        class="w-full text-left px-2 py-1 rounded-lg bg-[#6b1d14] text-white font-bold text-[10px] hover:bg-[#53150f] transition uppercase tracking-wider cursor-pointer"
                    >
                        Limpar
                    </button>

                    {{-- ENVIAR PARA O COFRE (DISPARA O MODAL) --}}
                    <button
                        type="button"
                        @click="$dispatch('open-vault-modal'); openDrawer = false;"
                        class="w-full text-left px-2 py-1 rounded-lg bg-[#6b1d14] text-white font-bold text-[10px] hover:bg-[#53150f] transition uppercase tracking-wider cursor-pointer"
                    >
                        Salvar no Cofre
                    </button>

                    {{-- EXPORTAR --}}
                    <button
                        type="button"
                        @click="typeof downloadNpc === 'function' ? downloadNpc() : exportNpc(); openDrawer = false;"
                        class="w-full text-left px-2 py-1 rounded-lg bg-[#8c6239] text-white font-bold text-[10px] hover:bg-[#704e2f] transition uppercase tracking-wider cursor-pointer"
                    >
                        Exportar
                    </button>
                </div>

                <button
                    type="button"
                    @click="openDrawer = !openDrawer"
                    class="flex items-center gap-1.5 px-3 py-1 bg-[#f4f1e8]/80 hover:bg-[#f4f1e8] backdrop-blur-sm border border-[#cdbb9f]/50 rounded-full shadow-sm text-[#6b1d14] text-[10px] font-bold uppercase tracking-wider transition cursor-pointer"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-[#6b1d14]"></span>
                    <span x-text="openDrawer ? 'Fechar' : 'Opções'">Opções</span>
                    <svg
                        :class="{ 'rotate-180': openDrawer }"
                        class="w-3 h-3 transition-transform duration-200"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- ============================================================
             MODAL DE SALVAR NO COFRE (PADRÃO IDÊNTICO AO DO COFRE)
        ============================================================= --}}
        <div
            x-data="{
                openVaultModal: false,
                imagePreview: null,
                hasImage: false,
                isSubmitting: false,
                handleImageChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.hasImage = true;
                        const reader = new FileReader();
                        reader.onload = e => { this.imagePreview = e.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this.clearImageInput();
                    }
                },
                clearImageInput() {
                    this.imagePreview = null;
                    this.hasImage = false;
                    const input = document.getElementById('npc_image');
                    if (input) input.value = '';
                },
                resetModal() {
                    this.clearImageInput();
                    this.openVaultModal = false;
                },
                async confirmVaultSave() {
                    this.isSubmitting = true;
                    try {
                        await sendToVault('{{ route('npc.import') }}');
                        this.resetModal();
                        alert('Criatura salva no cofre com sucesso!');
                    } catch (error) {
                        console.error(error);
                        alert(error.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }"
            @open-vault-modal.window="openVaultModal = true"
            x-cloak
        >
            <div
                x-show="openVaultModal"
                class="fixed inset-0 z-50 flex items-center justify-center transition-all duration-300"
            >
                <div @click="if (!isSubmitting) resetModal()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

                <div class="relative w-full max-w-md mx-4 bg-[#f4f1e8] border border-[#6b1d14]/20 rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh] z-10">
                    <div class="p-5 border-b border-[#cdbb9f]/30 bg-[#efe9dc] text-center relative">
                        <h3 class="text-base font-serif font-bold text-[#6b1d14] tracking-wide">
                            Salvar Ficha no Cofre
                        </h3>
                        <p class="text-[9px] text-[#8c6239]/80 uppercase tracking-wider font-bold italic mt-0.5">
                            Adicione um retrato ilustrado opcional para a sua criatura atual
                        </p>
                        
                        <button 
                            type="button"
                            @click="if (!isSubmitting) resetModal()"
                            class="absolute top-4 right-4 text-[#6b1d14]/60 hover:text-[#6b1d14] transition-colors focus:outline-none cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="m-0 flex flex-col overflow-y-auto">
                        <div class="p-6 space-y-5">
                            {{-- Aviso de que o JSON está pronto --}}
                            <div class="p-3 bg-[#efe9dc]/60 rounded-xl border border-[#cdbb9f]/40 text-xs text-[#5c4a3d] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                <span>A ficha gerada no construtor está pronta para ser enviada.</span>
                            </div>

                            {{-- Input da Imagem e Preview --}}
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-serif font-bold text-[#6b1d14] uppercase tracking-wider">
                                    Retrato Ilustrado (Opcional)
                                </label>
                                <input 
                                    type="file" 
                                    id="npc_image"
                                    name="npc_image" 
                                    accept="image/*"
                                    @change="handleImageChange(event)"
                                    class="w-full text-xs text-[#2b1d17] file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-serif file:font-bold file:bg-[#8c6239] file:text-[#f4f1e8] hover:file:bg-[#9c7249] file:cursor-pointer bg-[#efe9dc]/60 p-1.5 rounded-xl border border-[#cdbb9f]/40 focus:outline-none"
                                >
                                
                                {{-- Container de Preview da Imagem --}}
                                <div x-show="hasImage" class="relative mt-3 mx-auto w-32 h-32 rounded-xl border-2 border-dashed border-[#cdbb9f]/60 overflow-hidden bg-[#efe9dc]/40" x-cloak>
                                    <img :src="imagePreview" class="w-full h-full object-cover" alt="Preview da Imagem">
                                    <button
                                        type="button"
                                        @click="clearImageInput()"
                                        class="absolute top-1.5 right-1.5 bg-[#f4f1e8]/90 hover:bg-red-100 text-red-600 rounded-full p-1.5 transition-colors shadow-sm focus:outline-none backdrop-blur-sm cursor-pointer"
                                        title="Remover imagem"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 flex justify-end gap-2 bg-[#efe9dc] border-t border-[#cdbb9f]/30">
                            <button
                                type="button"
                                @click="resetModal()"
                                :disabled="isSubmitting"
                                class="px-4 py-2 border border-[#cdbb9f] text-[#8c6239] rounded-xl text-xs font-serif font-bold uppercase tracking-wider hover:bg-[#e8dcc6] transition-colors cursor-pointer"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                @click="confirmVaultSave()"
                                :disabled="isSubmitting"
                                class="px-5 py-2 rounded-xl bg-[#6b1d14] text-white text-xs font-serif font-bold uppercase tracking-wider hover:bg-[#53150f] transition-colors shadow-sm flex items-center gap-2 cursor-pointer"
                            >
                                <span x-show="isSubmitting" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin" x-cloak></span>
                                <span x-text="isSubmitting ? 'Salvando...' : 'Salvar no Cofre'">Salvar no Cofre</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             CORPO PRINCIPAL
        ============================================================= --}}
        <div class="flex-1 max-w-[1900px] w-full mx-auto px-4 lg:px-6 py-5 overflow-hidden">
            <form
                id="npc-form"
                method="POST"
                action="#"
                class="h-full flex flex-col lg:flex-row gap-5"
                @submit.prevent
            >
                @csrf

                {{-- BUILDER --}}
                <div id="builder-container" class="w-full lg:w-[60%] h-full flex flex-col overflow-hidden">
                    @include('npc-builder.builder')
                </div>

                {{-- PREVIEW --}}
                <div id="preview-container" class="w-full lg:w-[40%] h-full flex flex-col overflow-hidden">
                    @include('npc-builder.preview')
                </div>
            </form>
        </div>

    </div>
</x-app-layout>