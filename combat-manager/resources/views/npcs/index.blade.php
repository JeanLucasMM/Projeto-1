<x-app-layout>

    <style>
        /* Remove o limite de largura e os paddings padrões apenas do container pai desta página */
        main:has(.npc-page-scope),
        .py-12:has(.npc-page-scope),
        .max-w-7xl:has(.npc-page-scope),
        .sm\:px-6:has(.npc-page-scope),
        .lg\:px-8:has(.npc-page-scope) {
            padding: 0 !important;
            max-width: 100% !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        /* Impede qualquer transbordo horizontal indesejado no layout */
        aside, .sidebar, body {
            overflow-x: hidden !important;
        }
        
        /* Transições de estado ultra-suaves */
        .transition-polished {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    {{-- Wrapper de Escopo da Página --}}
    <div class="npc-page-scope">

        {{-- Notificação Toast Flutuante --}}
        @if(session('success'))
            <div 
                id="toast-success" 
                class="fixed top-6 right-6 z-50 transform translate-y-0 opacity-100 transition-all duration-500 ease-out flex items-center p-4 rounded-xl bg-emerald-50 border border-emerald-200/60 shadow-lg text-emerald-800 max-w-sm"
            >
                <svg class="w-5 h-5 mr-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-serif text-xs font-semibold pr-4 leading-relaxed">{{ session('success') }}</span>
                <button onclick="dismissToast()" class="text-emerald-400 hover:text-emerald-600 transition-colors focus:outline-none ml-auto shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <script>
                setTimeout(() => { dismissToast(); }, 4000);
                function dismissToast() {
                    const toast = document.getElementById('toast-success');
                    if (toast) {
                        toast.classList.add('opacity-0', '-translate-y-2');
                        setTimeout(() => toast.remove(), 500);
                    }
                }
            </script>
        @endif

        {{-- Barra de Pesquisa e Ações (Altura casada com h-16 do app.blade) --}}
<header class="sticky top-0 z-30 bg-[#efe9dc] border-b border-[#cdbb9f]/40 h-[60px] flex items-center px-6 sm:px-8 w-full shadow-sm">
    <div class="flex items-center justify-between gap-4 w-full">
        
        {{-- Título da Seção --}}
        <div class="flex items-center gap-3 shrink-0">
            <h1 class="text-lg lg:text-xl font-serif font-bold text-[#6b1d14] tracking-wide whitespace-nowrap">
                Biblioteca de NPCs
            </h1>
        </div>

        {{-- Formulário de Busca e Ordenação - Alinhamento Horizontal Compacto --}}
        <form
            method="GET"
            action="{{ request()->url() }}"
            class="flex flex-1 items-center gap-2 max-w-md lg:max-w-2xl min-w-0"
        >
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Pesquisar..."
                class="w-full rounded-xl border-[#cdbb9f]/50 bg-[#efe9dc]/40 text-[#2b1d17] placeholder-[#8c6239]/40 text-xs focus:border-[#6b1d14] focus:ring-[#6b1d14]/10 h-9 transition-polished min-w-[100px]"
            >

            <div class="flex items-center gap-2 shrink-0">
                <select
                    name="sort"
                    class="hidden sm:block rounded-xl border-[#cdbb9f]/50 bg-[#efe9dc]/40 text-[#2b1d17] text-xs focus:border-[#6b1d14] focus:ring-[#6b1d14]/10 h-9 pr-8 transition-polished"
                >
                    <option value="name_asc" {{ request('sort')=='name_asc' || !request('sort') ? 'selected' : '' }}>
                        Nome (A-Z)
                    </option>
                    <option value="name_desc" {{ request('sort')=='name_desc' ? 'selected' : '' }}>
                        Nome (Z-A)
                    </option>
                    <option value="cr_desc" {{ request('sort')=='cr_desc' ? 'selected' : '' }}>
                        CR Maior
                    </option>
                    <option value="cr_asc" {{ request('sort')=='cr_asc' ? 'selected' : '' }}>
                        CR Menor
                    </option>
                </select>

                <button class="px-4 h-9 rounded-xl bg-[#8c6239] hover:bg-[#74502d] text-white font-serif font-bold text-xs uppercase tracking-wider transition-polished shadow-sm">
                    Buscar
                </button>
            </div>
        </form>

        {{-- Botão Adicionar Compacto --}}
        <button 
            onclick="openImportModal()"
            class="px-4 h-9 rounded-xl bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-widest transition-polished shadow-sm flex items-center justify-center gap-1.5 shrink-0 transform hover:-translate-y-0.5 active:translate-y-0 focus:outline-none"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span class="hidden sm:inline">Adicionar</span>
        </button>

    </div>
</header>

        {{-- Conteúdo dos Cards --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

            {{-- Lista de NPCs --}}
            <div class="grid gap-4">
                @forelse($npcs as $npc)
                    <div class="bg-[#f4f1e8] border border-[#cdbb9f]/40 hover:border-[#6b1d14]/30 rounded-2xl shadow-sm hover:shadow-md transition-polished overflow-hidden">
                        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                            {{-- Lado Esquerdo: Avatar, Nome e Atributos Ficha --}}
                            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 flex-1 w-full">
                                
                                {{-- Avatar Circular --}}
                                <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-[#6b1d14] shadow-md flex items-center justify-center bg-[#6b1d14]/10 shrink-0">
                                    @if($npc->image_path)
                                        <img
                                            src="{{ asset('storage/'.$npc->image_path) }}"
                                            alt="{{ $npc->name }}"
                                            class="w-full h-full object-cover"
                                        >
                                    @else
                                        <span class="text-xl font-serif font-bold text-[#6b1d14] uppercase">
                                            {{ str($npc->name)->substr(0,2)->upper() }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Informações do Personagem --}}
                                <div class="flex-1 space-y-2.5 text-center sm:text-left w-full">
                                    <div>
                                        <h2 class="text-xl font-serif font-bold text-[#6b1d14] leading-tight">
                                            {{ $npc->name }}
                                        </h2>
                                        <p class="italic text-[10px] text-[#8c6239]/80 font-semibold uppercase tracking-wider mt-0.5">
                                            {{ $npc->size }} {{ $npc->creature_type }}
                                        </p>
                                    </div>

                                    {{-- Divisor Sutil --}}
                                    <div class="h-[1px] bg-gradient-to-r from-[#6b1d14]/20 via-[#cdbb9f]/30 to-transparent w-full"></div>

                                    {{-- Painel de Atributos Unificado --}}
                                    <div class="inline-flex items-center gap-3.5 bg-[#efe9dc]/50 border border-[#cdbb9f]/30 px-3 py-1 rounded-xl shadow-inner">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[9px] font-serif font-bold text-red-800 uppercase tracking-wider">Vida</span>
                                            <span class="text-xs font-mono font-bold text-red-700 leading-none">{{ $npc->max_hp }} HP</span>
                                        </div>
                                        
                                        <div class="w-[1px] h-3.5 bg-[#cdbb9f]/40"></div>

                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[9px] font-serif font-bold text-blue-800 uppercase tracking-wider">Armadura</span>
                                            <span class="text-xs font-mono font-bold text-blue-700 leading-none">{{ $npc->armor_class }} CA</span>
                                        </div>

                                        <div class="w-[1px] h-3.5 bg-[#cdbb9f]/40"></div>

                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[9px] font-serif font-bold text-amber-800 uppercase tracking-wider">Desafio</span>
                                            <span class="text-xs font-mono font-bold text-amber-800 leading-none">ND {{ $npc->challenge_rating }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Lado Direito: Ações --}}
                            <div class="flex sm:flex-row lg:flex-col items-center justify-center gap-2 shrink-0 w-full lg:w-auto self-stretch lg:self-center">
                                <a
                                    href="{{ route('npcs.show', $npc->id) }}"
                                    class="flex-1 lg:flex-none px-5 py-2 rounded-xl bg-[#6b1d14] text-[#f4f1e8] text-center font-serif font-bold text-xs uppercase tracking-wider hover:bg-[#53150f] transition-polished shadow-sm"
                                >
                                    Ver Ficha
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('npcs.destroy', $npc->id) }}"
                                    onsubmit="return confirm('Deseja mesmo banir este NPC do seu grimório?')"
                                    class="m-0 flex-1 lg:flex-none"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full px-4 py-2 rounded-xl border border-red-200/60 text-red-700 hover:bg-red-50 text-xs font-serif font-bold uppercase tracking-wider transition-polished">
                                        Excluir
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @empty
                    {{-- Estado Vazio --}}
                    <div class="bg-[#efe9dc]/20 border border-dashed border-[#cdbb9f]/60 rounded-2xl p-12 text-center">
                        <h2 class="text-lg font-serif font-bold text-[#6b1d14]">
                            Nenhum NPC encontrado
                        </h2>
                        <p class="text-xs text-[#8c6239]/80 italic mt-1">
                            Insira um arquivo JSON para iniciar seu grimório de campanha.
                        </p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>

    {{-- Modal Popup de Importação --}}
    <div 
        id="importModal" 
        class="fixed inset-0 z-50 hidden flex items-center justify-center transition-all duration-300"
    >
        <div onclick="closeImportModal()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-4 bg-[#f4f1e8] border border-[#6b1d14]/20 rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-5 border-b border-[#cdbb9f]/30 bg-[#efe9dc] text-center relative">
                <h3 class="text-base font-serif font-bold text-[#6b1d14] tracking-wide">
                    Adicionar Nova Criatura
                </h3>
                <p class="text-[9px] text-[#8c6239]/80 uppercase tracking-wider font-bold italic mt-0.5">
                    Adicione a ficha rúnica e seu retrato ilustrado
                </p>
                
                <button 
                    type="button"
                    onclick="closeImportModal()"
                    class="absolute top-4 right-4 text-[#6b1d14]/60 hover:text-[#6b1d14] transition-colors focus:outline-none"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form 
                id="importForm"
                action="{{ route('npc.import') }}" 
                method="POST" 
                enctype="multipart/form-data" 
                class="m-0 flex flex-col overflow-y-auto"
            >
                @csrf
                
                <div class="p-6 space-y-5">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-serif font-bold text-[#6b1d14] uppercase tracking-wider">
                            Arquivo da Ficha (.json) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            name="npc_file" 
                            accept=".json"
                            required
                            class="w-full text-xs text-[#2b1d17] file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-serif file:font-bold file:bg-[#6b1d14] file:text-[#f4f1e8] hover:file:bg-[#7a1f17] file:cursor-pointer bg-[#efe9dc]/60 p-1.5 rounded-xl border border-[#cdbb9f]/40 focus:outline-none"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-serif font-bold text-[#6b1d14] uppercase tracking-wider">
                            Retrato Ilustrado (Opcional)
                        </label>
                        <input 
                            type="file" 
                            id="npc_image"
                            name="npc_image" 
                            accept="image/*"
                            class="w-full text-xs text-[#2b1d17] file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-serif file:font-bold file:bg-[#8c6239] file:text-[#f4f1e8] hover:file:bg-[#9c7249] file:cursor-pointer bg-[#efe9dc]/60 p-1.5 rounded-xl border border-[#cdbb9f]/40 focus:outline-none"
                        >
                    </div>

                    <input type="hidden" name="image_crop" id="image_crop" value="">

                    <div id="preview-wrapper" class="hidden flex flex-col items-center justify-center space-y-2 pt-3 border-t border-[#cdbb9f]/30">
                        <span class="block text-[9px] font-serif font-bold text-[#6b1d14] uppercase tracking-wider">
                            Visualização do Retrato
                        </span>
                        <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-[#6b1d14] shadow-sm bg-[#efe9dc]/45 flex items-center justify-center">
                            <img id="image-preview" src="" alt="Pré-visualização" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-[#efe9dc]/50 border-t border-[#cdbb9f]/30 flex items-center justify-end gap-2">
                    <button 
                        type="button"
                        onclick="closeImportModal()"
                        class="px-4 py-2 border border-[#6b1d14]/30 text-[#6b1d14] hover:bg-[#6b1d14]/5 rounded-xl text-[10px] font-serif font-bold uppercase tracking-wider transition-polished focus:outline-none"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit"
                        class="px-5 py-2 bg-[#6b1d14] hover:bg-[#7a1f17] text-[#f4f1e8] rounded-xl text-[10px] font-serif font-bold uppercase tracking-wider transition-polished shadow-sm focus:outline-none"
                    >
                        Adicionar
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- Script de Controle --}}
    <script>
        const imageInput = document.getElementById('npc_image');
        const previewWrapper = document.getElementById('preview-wrapper');
        const previewImg = document.getElementById('image-preview');

        imageInput.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();

                reader.onload = function (event) {
                    previewImg.src = event.target.result;
                    previewWrapper.classList.remove('hidden');
                };

                reader.readAsDataURL(file);
            } else {
                clearPreview();
            }
        });

        function openImportModal() {
            const modal = document.getElementById('importModal');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            
            document.getElementById('importForm').reset();
            clearPreview();
        }

        function clearPreview() {
            previewImg.src = '';
            previewWrapper.classList.add('hidden');
        }
    </script>

</x-app-layout>