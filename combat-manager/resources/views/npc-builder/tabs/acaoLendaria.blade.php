<div class="space-y-5">

    {{-- CONFIGURAÇÃO DAS AÇÕES LENDÁRIAS --}}
    <div class="rounded-xl border border-[#cdbb9f]/80 bg-white shadow-sm overflow-hidden">

        <div class="px-4 py-3 border-b border-[#cdbb9f]/50 bg-gradient-to-r from-[#fbf9f4] via-[#f4f1e8] to-[#ece6d7]/70">

            <h3 class="text-xs font-black uppercase tracking-widest text-[#6b1d14]">
                Configuração das Ações Lendárias
            </h3>

        </div>

        <div class="p-4 space-y-4">

            <div class="grid md:grid-cols-2 gap-4">

                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                        Quantidade de ações
                    </label>

                    <input
                        type="number"
                        min="1"
                        max="20"
                        x-model.number="legendaryActions[0].legendary.totalActions"
                        class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519]"
                    >
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                        Texto introdutório (Editavel)
                    </label>

                    <input
                        type="text"
                        x-model="legendaryActions[0].legendary.intro"
                        class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm font-bold text-[#6b1d14] focus:ring-1 focus:ring-[#8a2519]"
                    >
                </div>

            </div>

            

        </div>

    </div>



    {{-- LISTA DE AÇÕES LENDÁRIAS --}}
    <div class="space-y-3">

        <template
            x-for="(action,index) in legendaryActions"
            :key="action.id"
        >

            <div
                class="rounded-xl border border-[#cdbb9f]/80 bg-white shadow-xs overflow-hidden hover:shadow-md"
                x-data="{ open:false }"
            >

                {{-- Cabeçalho --}}
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-gradient-to-r from-[#fbf9f4] via-[#f4f1e8] to-[#ece6d7]/70 border-b border-[#cdbb9f]/50">

                    <div class="flex items-center gap-2 flex-1">

                        <button
                            type="button"
                            @click="open=!open"
                            class="cursor-pointer"
                        >
                            <svg
                                class="w-4 h-4 transition-transform"
                                :class="{ 'rotate-180':open }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <input
                            type="text"
                            x-model="action.title"
                            placeholder="Nome da ação lendária..."
                            class="w-full border-0 bg-transparent text-xs font-black text-[#6b1d14] placeholder:text-[#8a2519]/50 focus:ring-0 p-0"
                        >

                    </div>

                    <div class="flex items-center gap-2">



                        <button
                            type="button"
                            @click="removeLegendaryAction(index)"
                            class="text-red-700 text-[10px] font-black uppercase cursor-pointer"
                        >
                            Remover
                        </button>

                    </div>

                </div>

                {{-- Corpo --}}
                <div
                    x-show="open"
                    x-transition
                    class="p-4 bg-white"
                >

                    <div class="rounded-xl border border-[#cdbb9f]/60 bg-[#fbf9f4] shadow-inner">

                        <div wire:ignore>

                            <div
                                :id="'legendaryActions-editor-'+action.id"
                                class="min-h-[120px] p-3 prose prose-sm max-w-none text-xs text-[#53150f]"
                            ></div>

                        </div>

                    </div>

                </div>

            </div>

        </template>

    </div>

</div>