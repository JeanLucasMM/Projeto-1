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

        {{-- Barra de Pesquisa e Ações --}}
        <header class="sticky top-0 z-30 bg-[#efe9dc] border-b border-[#cdbb9f]/40 h-[60px] flex items-center px-6 sm:px-8 w-full shadow-sm">
            <div class="flex items-center justify-between gap-4 w-full">
                
                {{-- Título da Seção --}}
                <div class="flex items-center gap-3 shrink-0">
                    <h1 class="text-lg lg:text-xl font-serif font-bold text-[#6b1d14] tracking-wide whitespace-nowrap">
                        Biblioteca de NPCs
                    </h1>
                </div>

                {{-- Formulário de Busca e Ordenação --}}
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

                {{-- Botões de Ação --}}
                <div class="flex items-center gap-2 shrink-0"> 
                    <button
                        type="button"
                        onclick="openFolderModal()"
                        class="px-4 h-9 rounded-xl bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-widest transition-polished shadow-sm flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h5l2 2h11v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>
                        </svg>
                        <span class="hidden sm:inline">
                            Nova Pasta
                        </span>
                    </button>

                    <button
                        type="button"
                        onclick="openImportModal()"
                        class="px-4 h-9 rounded-xl bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-widest transition-polished shadow-sm flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="hidden sm:inline">
                            Adicionar
                        </span>
                    </button>
                </div>

            </div>
        </header>

        {{-- Conteúdo Principal --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

            {{-- NPCs sem pasta --}}
            @if($npcs->whereNull('folder_id')->count())
                <div class="mb-12">
                    <h2 class="text-2xl font-serif font-bold text-[#6b1d14] mb-5">
                        Biblioteca
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 items-start mt-6">
                        @foreach($npcs->whereNull('folder_id') as $npc)
                            @include('npcs.npc-card', ['npc' => $npc])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Seção de Pastas --}}
            @if($folders->count())
                <div class="mt-14">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-[#6b1d14]">
                            Pastas
                        </h2>
                        <span class="text-xs uppercase tracking-widest text-[#8c6239]">
                            {{ $folders->count() }} Pastas
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($folders as $folder)
                            <div
                                class="folder-dropzone transition-all duration-200 group relative block h-56 rounded-2xl overflow-hidden bg-[#f4f1e8] border border-[#d8c7aa] shadow-sm hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between cursor-pointer"
                                data-folder="{{ $folder->id }}"
                                data-url="{{ route('folders.show', $folder) }}"
                                data-name="{{ $folder->name }}"
                                data-subtitle="{{ $folder->subtitle }}"
                                data-color="{{ $folder->color }}"
                                data-update-url="{{ route('folders.update', $folder) }}"
                            >
                                {{-- Faixa Superior Colorida --}}
                                <div
                                    class="w-full h-8 shrink-0"
                                    style="background: {{ $folder->color }}"
                                ></div>

                                {{-- Corpo da Pasta --}}
                                <div class="flex-1 p-5 flex flex-col justify-between">
                                    {{-- Cabeçalho da Pasta com Menu Dropdown --}}
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <svg
                                                    class="w-6 h-6 shrink-0"
                                                    fill="{{ $folder->color }}"
                                                    viewBox="0 0 24 24">
                                                    <path d="M10 4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h6z"/>
                                                </svg>

                                                <h3 class="font-serif font-bold text-lg text-[#2b1d17] truncate max-w-[140px]">
                                                    {{ $folder->name }}
                                                </h3>
                                            </div>

                                            @if($folder->subtitle)
                                                <p class="mt-1 text-xs italic text-[#8c6239] line-clamp-1 leading-relaxed">
                                                    {{ $folder->subtitle }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="relative" onclick="event.stopPropagation();">
                                            <button
                                                type="button"
                                                onclick="toggleFolderMenu({{ $folder->id }})"
                                                class="rounded-lg p-1.5 hover:bg-[#e8dcc6] text-[#2b1d17] font-bold"
                                            >
                                                ⋮
                                            </button>
                                            <div
                                                id="folder-menu-{{ $folder->id }}"
                                                class="hidden absolute right-0 mt-2 w-48 rounded-xl bg-white border border-[#cdbb9f]/40 shadow-lg z-50 text-left overflow-hidden"
                                            >
                                                <button
                                                    type="button"
                                                    onclick="editFolder({{ $folder->id }})"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-100 text-xs font-serif text-[#2b1d17] transition-colors"
                                                >
                                                    Editar
                                                </button>
                                                <form
                                                    action="{{ route('folders.destroy', $folder) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Excluir esta pasta? Os NPCs voltarão para Sem Pasta.')"
                                                    class="w-full m-0"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="w-full text-left px-4 py-2.5 hover:bg-red-50 text-xs font-serif text-red-700 transition-colors"
                                                    >
                                                         Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Miniaturas e Contador --}}
                                    <div class="flex items-center justify-between pt-2">
                                        <div class="folder-avatars mt-6 flex -space-x-3">
                                            @foreach($folder->npcs->take(4) as $npc)
                                                <div class="avatar-preview w-14 h-14 rounded-full overflow-hidden border-2 border-white bg-[#efe9dc]">
                                                    @if($npc->image_path)
                                                        <img
                                                            src="{{ asset('storage/'.$npc->image_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-xs font-bold bg-[#6b1d14]/10 text-[#6b1d14]">
                                                            {{ str($npc->name)->substr(0,2)->upper() }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="text-xs font-serif text-[#8c6239]">
                                            <span class="folder-count">
                                                {{ $folder->npcs->count() }}
                                            </span>
                                            NPCs
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Estado Vazio Geral --}}
            @if($npcs->isEmpty() && $folders->isEmpty())
                <div class="bg-[#efe9dc]/20 border border-dashed border-[#cdbb9f]/60 rounded-2xl p-12 text-center mt-8">
                    <h2 class="text-lg font-serif font-bold text-[#6b1d14]">
                        Nenhum NPC encontrado
                    </h2>
                    <p class="text-xs text-[#8c6239]/80 italic mt-1">
                        Insira um arquivo JSON para iniciar seu grimório de campanha.
                    </p>
                </div>
            @endif

        </div>

    </div>

    {{-- Modal de Nova Pasta --}}
    <div id="folderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div onclick="closeFolderModal()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md bg-[#f4f1e8] rounded-2xl shadow-xl overflow-hidden z-10">
            <div class="p-5 border-b bg-[#efe9dc]">
                <h3 class="text-base font-serif font-bold text-[#6b1d14]">
                    Nova Pasta
                </h3>
                <p class="text-[10px] italic text-[#8c6239]">
                    Organize seus NPCs
                </p>
            </div>

            <form action="{{ route('folders.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold mb-1">
                            Nome
                        </label>
                        <input
                            name="name"
                            required
                            class="w-full rounded-xl border-[#cdbb9f]"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">
                            Subtítulo
                        </label>
                        <textarea
                            name="subtitle"
                            rows="2"
                            class="w-full rounded-xl border-[#cdbb9f] text-xs resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-2">
                            Cor
                        </label>
                        <select
                            name="color"
                            class="w-full rounded-xl border-[#cdbb9f]"
                        >
                            <option value="#8c6239">Marrom</option>
                            <option value="#6b1d14">Vermelho</option>
                            <option value="#2f5e3e">Verde</option>
                            <option value="#2c4a6f">Azul</option>
                            <option value="#5b3265">Roxo</option>
                            <option value="#595959">Cinza</option>
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 flex justify-end gap-2 bg-[#efe9dc]">
                    <button
                        type="button"
                        onclick="closeFolderModal()"
                        class="px-4 py-2 border rounded-xl"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-[#6b1d14] text-white"
                    >
                        Criar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de Edição de Pasta --}}
    <div id="editFolderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div onclick="closeEditFolderModal()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md bg-[#f4f1e8] rounded-2xl shadow-xl overflow-hidden z-10">
            <div class="p-5 border-b bg-[#efe9dc]">
                <h3 class="text-base font-serif font-bold text-[#6b1d14]">
                    Editar Pasta
                </h3>
            </div>

            <form id="editFolderForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold mb-1">Nome</label>
                        <input
                            id="edit_name"
                            name="name"
                            required
                            class="w-full rounded-xl border-[#cdbb9f]"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Subtítulo</label>
                        <textarea
                            id="edit_subtitle"
                            name="subtitle"
                            rows="2"
                            class="w-full rounded-xl border-[#cdbb9f] text-xs resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-2">Cor</label>
                        <select
                            id="edit_color"
                            name="color"
                            class="w-full rounded-xl border-[#cdbb9f]"
                        >
                            <option value="#8c6239">Marrom</option>
                            <option value="#6b1d14">Vermelho</option>
                            <option value="#2f5e3e">Verde</option>
                            <option value="#2c4a6f">Azul</option>
                            <option value="#5b3265">Roxo</option>
                            <option value="#595959">Cinza</option>
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 flex justify-end gap-2 bg-[#efe9dc]">
                    <button
                        type="button"
                        onclick="closeEditFolderModal()"
                        class="px-4 py-2 border rounded-xl"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-[#6b1d14] text-white"
                    >
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Popup de Importação --}}
    <div id="importModal" class="fixed inset-0 z-50 hidden flex items-center justify-center transition-all duration-300">
        <div onclick="closeImportModal()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-4 bg-[#f4f1e8] border border-[#6b1d14]/20 rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh] z-10">
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
                </div>

                <div class="px-6 py-4 flex justify-end gap-2 bg-[#efe9dc] border-t border-[#cdbb9f]/30">
                    <button
                        type="button"
                        onclick="closeImportModal()"
                        class="px-4 py-2 border border-[#cdbb9f] text-[#8c6239] rounded-xl text-xs font-serif font-bold uppercase tracking-wider hover:bg-[#e8dcc6] transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-[#6b1d14] text-white text-xs font-serif font-bold uppercase tracking-wider hover:bg-[#53150f] transition-colors shadow-sm"
                    >
                        Importar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts de Controle dos Modais, Menus e Navegação --}}
    <script>
        function openFolderModal() {
            document.getElementById('folderModal').classList.remove('hidden');
        }

        function closeFolderModal() {
            document.getElementById('folderModal').classList.add('hidden');
        }

        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        function toggleFolderMenu(id) {
            document.querySelectorAll('[id^="folder-menu-"]').forEach(menu => {
                if (menu.id !== `folder-menu-${id}`) {
                    menu.classList.add('hidden');
                }
            });
            const targetMenu = document.getElementById(`folder-menu-${id}`);
            if (targetMenu) {
                targetMenu.classList.toggle('hidden');
            }
        }

        function editFolder(id) {
            const folderCard = document.querySelector(`[data-folder="${id}"]`);
            if (!folderCard) return;

            const name = folderCard.dataset.name || '';
            const subtitle = folderCard.dataset.subtitle || '';
            const color = folderCard.dataset.color || '#8c6239';
            const updateUrl = folderCard.dataset.updateUrl || '';

            document.getElementById('editFolderForm').action = updateUrl;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_subtitle').value = subtitle;
            document.getElementById('edit_color').value = color;

            document.getElementById('editFolderModal').classList.remove('hidden');

            const menu = document.getElementById(`folder-menu-${id}`);
            if (menu) menu.classList.add('hidden');
        }

        function closeEditFolderModal() {
            document.getElementById('editFolderModal').classList.add('hidden');
        }

        // Navegação ao clicar no card da pasta (ignorando botões, forms e menus)
        document.querySelectorAll('.folder-dropzone').forEach(folder => {
            folder.addEventListener('click', function(e) {
                if (
                    e.target.closest('button') ||
                    e.target.closest('form') ||
                    e.target.closest('[id^="folder-menu-"]')
                ) {
                    return;
                }

                if (folder.dataset.url) {
                    window.location = folder.dataset.url;
                }
            });
        });

        // Fecha menus abertos ao clicar fora
        document.addEventListener('click', function () {
            document.querySelectorAll('[id^="folder-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            let draggedNpc = null;

            document.querySelectorAll('.npc-card').forEach(card => {
                card.addEventListener('dragstart', function (e) {
                    draggedNpc = this.dataset.npc;
                    this.classList.add('opacity-40');
                    e.dataTransfer.effectAllowed = 'move';
                });

                card.addEventListener('dragend', function () {
                    this.classList.remove('opacity-40');
                });
            });

            document.querySelectorAll('.folder-dropzone').forEach(folder => {
                folder.addEventListener('dragover', function(e){
                    e.preventDefault();
                    this.classList.add(
                        'ring-4',
                        'ring-[#8c6239]',
                        'scale-[1.02]'
                    );
                });

                folder.addEventListener('dragleave', function(){
                    this.classList.remove(
                        'ring-4',
                        'ring-[#8c6239]',
                        'scale-[1.02]'
                    );
                });

                folder.addEventListener('drop', async function(e){
                    e.preventDefault();
                    this.classList.remove(
                        'ring-4',
                        'ring-[#8c6239]',
                        'scale-[1.02]'
                    );

                    if(!draggedNpc) return;

                    const response = await fetch(`/npcs/${draggedNpc}/move-folder`,{
                        method:'PATCH',
                        headers:{
                            'Content-Type':'application/json',
                            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':'application/json'
                        },
                        body:JSON.stringify({
                            folder_id:this.dataset.folder
                        })
                    });

                    if(response.ok){
                        const cardElement = document.querySelector(`.npc-card[data-npc="${draggedNpc}"]`);
                        
                        if(cardElement) {
                            // Atualiza dinamicamente as miniaturas na pasta destino
                            const avatarsContainer = this.querySelector('.folder-avatars');
                            if(avatarsContainer && avatarsContainer.children.length < 4) {
                                const npcImg = cardElement.querySelector('img');
                                const npcInitials = cardElement.querySelector('.npc-initials')?.textContent || '??';
                                
                                const avatarDiv = document.createElement('div');
                                avatarDiv.className = 'avatar-preview w-14 h-14 rounded-full overflow-hidden border-2 border-white bg-[#efe9dc]';
                                
                                if(npcImg) {
                                    avatarDiv.innerHTML = `<img src="${npcImg.src}" class="w-full h-full object-cover">`;
                                } else {
                                    avatarDiv.innerHTML = `<div class="w-full h-full flex items-center justify-center text-xs font-bold bg-[#6b1d14]/10 text-[#6b1d14]">${npcInitials.trim()}</div>`;
                                }
                                avatarsContainer.appendChild(avatarDiv);
                            }

                            cardElement.remove();
                        }

                        // Atualiza o contador de NPCs da pasta de destino
                        const countSpan = this.querySelector('.folder-count');
                        if(countSpan) {
                            countSpan.textContent = parseInt(countSpan.textContent.trim()) + 1;
                        }

                        draggedNpc = null;
                    } else {
                        alert("Erro ao mover NPC.");
                    }
                });
            });
        });

        document.addEventListener('click', function(event) {
    // Verifica se clicou no botão de 3 pontinhos
    const button = event.target.closest('.npc-menu-btn');
    const allMenus = document.querySelectorAll('.npc-dropdown-menu');

    if (button) {
        event.preventDefault();
        event.stopPropagation();
        
        const targetId = button.getAttribute('data-target');
        const targetMenu = document.getElementById(targetId);

        // Fecha todos os outros menus que estiverem abertos
        allMenus.forEach(menu => {
            if (menu.id !== targetId) {
                menu.classList.add('hidden');
            }
        });

        // Abre/Fecha o menu que você clicou
        if (targetMenu) {
            targetMenu.classList.toggle('hidden');
        }
    } else {
        // Se clicar em qualquer outro lugar da tela, fecha todos os menus
        allMenus.forEach(menu => menu.classList.add('hidden'));
    }
});
    </script>
</x-app-layout>