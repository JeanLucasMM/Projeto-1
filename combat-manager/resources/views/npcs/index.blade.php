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
                        NPCs
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
                            Importar
                        </span>
                    </button>
                </div>

            </div>
        </header>

        {{-- Conteúdo Principal --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

         {{-- NPCs sem pasta (Biblioteca) --}}
    @php
        $unassignedNpcs = $npcs->whereNull('folder_id');
    @endphp

    @if($unassignedNpcs->count())
        <div class="mb-12">
            {{-- Cabeçalho da Seção --}}
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-[#cdbb9f]/40">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-[#6b1d14]/10 text-[#6b1d14] border border-[#6b1d14]/20 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-serif font-bold text-[#6b1d14] leading-tight">
                            Biblioteca
                        </h2>
                        <p class="text-xs italic text-[#8c6239]">
                            NPCs sem pastas atribuidas
                        </p>
                    </div>
                </div>

                <span class="px-3 py-1 rounded-full bg-[#efe9dc] border border-[#cdbb9f] text-xs font-serif font-bold text-[#8c6239] shadow-sm">
                    {{ $unassignedNpcs->count() }} {{ $unassignedNpcs->count() === 1 ? 'NPC' : 'NPCs' }}
                </span>
            </div>

            {{-- Grade de Cards de NPCs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 items-start mt-6">
                @foreach($unassignedNpcs as $npc)
                    @include('npcs.npc-card', ['npc' => $npc])
                @endforeach
            </div>
        </div>
    @endif

            {{-- Seção de Pastas --}}
           @if($folders->count())
            <div class="mt-14">
            {{-- Cabeçalho da Seção --}}
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-[#cdbb9f]/40">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-xl bg-[#6b1d14]/10 text-[#6b1d14] border border-[#6b1d14]/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-serif font-bold text-[#6b1d14] leading-tight">
                        Pastas de Campanhas
                    </h2>
                    <p class="text-xs italic text-[#8c6239]">
                        Arraste seus NPCs para organizalos em pastas
                    </p>
                </div>
            </div>

            <span class="px-3 py-1 rounded-full bg-[#efe9dc] border border-[#cdbb9f] text-xs font-serif font-bold text-[#8c6239] shadow-sm">
                {{ $folders->count() }} {{ $folders->count() === 1 ? 'Pasta' : 'Pastas' }}
            </span>
        </div>

        {{-- Grade de Pastas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($folders as $folder)
                <div
                    class="folder-dropzone group relative flex flex-col justify-between h-60 rounded-2xl bg-[#f4f1e8] border border-[#cdbb9f]/70 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden"
                    data-folder="{{ $folder->id }}"
                    data-url="{{ route('folders.show', $folder) }}"
                    data-name="{{ $folder->name }}"
                    data-subtitle="{{ $folder->subtitle }}"
                    data-color="{{ $folder->color }}"
                    data-update-url="{{ route('folders.update', $folder) }}"
                >
                    {{-- Faixa de Destaque Superior com Cor Personalizada --}}
                    <div class="relative w-full h-2.5 shrink-0" style="background-color: {{ $folder->color }}">
                        <div class="absolute inset-0 bg-black/10"></div>
                    </div>

                    {{-- Corpo Principal da Pasta --}}
                    <div class="flex-1 p-5 flex flex-col justify-between">
                        
                        {{-- Topo do Card: Ícone, Nome, Subtítulo e Menu --}}
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    {{-- Ícone da Pasta na Cor do Destaque --}}
                                    <div class="p-1.5 rounded-lg shrink-0" style="background-color: {{ $folder->color }}15">
                                        <svg class="w-5 h-5 shrink-0" style="color: {{ $folder->color }}" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h6z"/>
                                        </svg>
                                    </div>

                                    <h3 class="font-serif font-bold text-base text-[#2b1d17] group-hover:text-[#6b1d14] transition-colors truncate">
                                        {{ $folder->name }}
                                    </h3>
                                </div>

                                {{-- Botão do Menu Dropdown --}}
                                <div class="relative shrink-0" onclick="event.stopPropagation();">
                                    <button
                                        type="button"
                                        onclick="toggleFolderMenu({{ $folder->id }})"
                                        class="p-1.5 rounded-lg text-[#8c6239] hover:text-[#6b1d14] hover:bg-[#efe9dc] transition-colors"
                                        title="Opções"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>

                                    {{-- Menu Dropdown Estilizado --}}
                                    <div
                                        id="folder-menu-{{ $folder->id }}"
                                        class="hidden absolute right-0 mt-2 w-40 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] shadow-xl z-50 text-left overflow-hidden py-1"
                                    >
                                        {{-- Editar --}}
                                        <button
                                            type="button" 
                                            onclick="editFolder(this)"
                                            data-id="{{ $folder->id }}"
                                            data-name="{{ $folder->name }}"
                                            data-subtitle="{{ $folder->subtitle }}"
                                            data-color="{{ $folder->color }}"
                                            class="w-full flex items-center gap-2 px-3.5 py-2 hover:bg-[#efe9dc] text-xs font-serif font-bold text-[#4a3b32] hover:text-[#6b1d14] transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-[#8c6239]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </button>

                                        <div class="my-1 border-t border-[#cdbb9f]/30"></div>

                                        {{-- Excluir --}}
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
                                                class="w-full flex items-center gap-2 px-3.5 py-2 hover:bg-red-50 text-xs font-serif font-bold text-red-700 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Subtítulo da Pasta --}}
                            @if($folder->subtitle)
                                <p class="mt-2 text-xs italic text-[#8c6239] line-clamp-2 leading-relaxed">
                                    "{{ $folder->subtitle }}"
                                </p>
                            @else
                                <p class="mt-2 text-xs italic text-[#8c6239]/40">
                                    Sem descrição adicional...
                                </p>
                            @endif
                        </div>

                        {{-- Rodapé do Card: Avatares Sobrepostos & Contador de NPCs --}}
                        <div class="pt-3 border-t border-[#cdbb9f]/40 flex items-center justify-between">
                            {{-- Miniaturas de NPCs --}}
                            <div class="folder-avatars flex items-center -space-x-2.5 overflow-hidden">
                                @forelse($folder->npcs->take(4) as $npc)
                                    <div class="avatar-preview relative w-8 h-8 rounded-full overflow-hidden border-2 border-[#f4f1e8] bg-[#efe9dc] shadow-sm">
                                        @if($npc->image_path)
                                            <img
                                                src="{{ asset('storage/'.$npc->image_path) }}"
                                                alt="{{ $npc->name }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[10px] font-serif font-bold bg-[#6b1d14]/10 text-[#6b1d14]">
                                                {{ str($npc->name)->substr(0, 2)->upper() }}
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <span class="text-[11px] italic text-[#8c6239]/60">Vazia</span>
                                @endforelse

                                {{-- Pill indicador se houver mais de 4 NPCs --}}
                                @if($folder->npcs->count() > 4)
                                    <div class="w-8 h-8 rounded-full bg-[#efe9dc] border-2 border-[#f4f1e8] flex items-center justify-center text-[10px] font-serif font-bold text-[#6b1d14] shadow-sm">
                                        +{{ $folder->npcs->count() - 4 }}
                                    </div>
                                @endif
                            </div>

                            {{-- Contador Total de NPCs --}}
                            <div class="px-2.5 py-1 rounded-lg bg-[#efe9dc]/80 border border-[#cdbb9f]/50 flex items-center gap-1.5 shadow-inner">
                                <span class="folder-count text-xs font-serif font-bold text-[#6b1d14]">
                                    {{ $folder->npcs->count() }}
                                </span>
                                <span class="text-[10px] font-serif uppercase tracking-wider text-[#8c6239]">
                                    NPCs
                                </span>
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
<div id="folderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop com Blur --}}
    <div onclick="closeFolderModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        {{-- Card do Modal --}}
        <div class="relative w-full max-w-md bg-[#f4f1e8] rounded-2xl shadow-2xl border border-[#cdbb9f]/60 overflow-hidden z-10">
        
        {{-- Cabeçalho --}}
        <div class="px-6 py-4 border-b border-[#cdbb9f]/50 bg-[#efe9dc] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-lg bg-[#6b1d14]/10 text-[#6b1d14]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-serif font-bold text-[#6b1d14] leading-tight">
                        Nova Pasta
                    </h3>
                    <p class="text-[11px] italic text-[#8c6239]">
                        Organize seus NPCs e conteúdos
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeFolderModal()" class="p-1.5 rounded-lg text-[#8c6239]/70 hover:text-[#6b1d14] hover:bg-[#6b1d14]/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Formulário --}}
        <form action="{{ route('folders.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                
                {{-- Nome --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Nome da Pasta
                    </label>
                    <input
                        type="text"
                        name="name"
                        required
                        placeholder="Ex: Taverna do Dragão, Vilões..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-sm text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all shadow-inner"
                    >
                </div>

                {{-- Subtítulo --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Subtítulo <span class="text-[10px] text-[#8c6239]/70 normal-case font-normal">(opcional)</span>
                    </label>
                    <textarea
                        name="subtitle"
                        rows="2"
                        placeholder="Breve descrição sobre esta pasta..."
                        class="w-full px-3.5 py-2 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-xs text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all resize-none shadow-inner"
                    ></textarea>
                </div>

                {{-- Seletor de Cores --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-2.5">
                        Cor de Destaque
                    </label>
                    
                    <div class="grid grid-cols-5 gap-3 p-3 rounded-xl bg-[#efe9dc]/60 border border-[#cdbb9f]/40">
                        
                        {{-- Couro --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8c6239" class="peer sr-only" checked>
                            <span class="w-7 h-7 rounded-full bg-[#8c6239] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Couro</span>
                        </label>

                        {{-- Carmesim --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#6b1d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#6b1d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Carmesim</span>
                        </label>

                        {{-- Âmbar --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#a65d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#a65d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#a65d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#a65d14]">Âmbar</span>
                        </label>

                        {{-- Floresta --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2f5e3e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2f5e3e] shadow-md ring-2 ring-transparent peer-checked:ring-[#2f5e3e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2f5e3e]">Floresta</span>
                        </label>

                        {{-- Arcano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#1b5e5e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#1b5e5e] shadow-md ring-2 ring-transparent peer-checked:ring-[#1b5e5e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#1b5e5e]">Arcano</span>
                        </label>

                        {{-- Oceano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2c4a6f" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2c4a6f] shadow-md ring-2 ring-transparent peer-checked:ring-[#2c4a6f] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2c4a6f]">Oceano</span>
                        </label>

                        {{-- Místico --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#5b3265" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#5b3265] shadow-md ring-2 ring-transparent peer-checked:ring-[#5b3265] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#5b3265]">Místico</span>
                        </label>

                        {{-- Magia --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8a3052" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#8a3052] shadow-md ring-2 ring-transparent peer-checked:ring-[#8a3052] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#8a3052]">Magia</span>
                        </label>

                        {{-- Sombra --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#595959" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#595959] shadow-md ring-2 ring-transparent peer-checked:ring-[#595959] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#595959]">Sombra</span>
                        </label>

                        {{-- Obsidian --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2a2a2a" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2a2a2a] shadow-md ring-2 ring-transparent peer-checked:ring-[#2a2a2a] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2a2a2a]">Obsidian</span>
                        </label>

                    </div>
                </div>
            </div>

            {{-- Rodapé --}}
            <div class="px-6 py-4 border-t border-[#cdbb9f]/50 flex justify-end items-center gap-2.5 bg-[#efe9dc]">
                <button
                    type="button"
                    onclick="closeFolderModal()"
                    class="px-4 py-2 rounded-xl border border-[#cdbb9f] font-serif text-xs font-bold text-[#8c6239] hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 transition-all"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-[#6b1d14] hover:bg-[#802319] active:bg-[#54160f] text-[#f4f1e8] font-serif text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-lg transition-all"
                >
                    Criar Pasta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal de Edição de Pasta --}}
<div id="editFolderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop com Blur --}}
    <div onclick="closeEditFolderModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

    {{-- Card do Modal --}}
    <div class="relative w-full max-w-md bg-[#f4f1e8] rounded-2xl shadow-2xl border border-[#cdbb9f]/60 overflow-hidden z-10">
        
        {{-- Cabeçalho --}}
        <div class="px-6 py-4 border-b border-[#cdbb9f]/50 bg-[#efe9dc] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-lg bg-[#6b1d14]/10 text-[#6b1d14]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-serif font-bold text-[#6b1d14] leading-tight">
                        Editar Pasta
                    </h3>
                    <p class="text-[11px] italic text-[#8c6239]">
                        Altere os detalhes ou a cor da pasta
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeEditFolderModal()" class="p-1.5 rounded-lg text-[#8c6239]/70 hover:text-[#6b1d14] hover:bg-[#6b1d14]/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Formulário --}}
        <form id="editFolderForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-5">
                
                {{-- Nome --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Nome da Pasta
                    </label>
                    <input
                        type="text"
                        id="edit_name"
                        name="name"
                        required
                        placeholder="Ex: Taverna do Dragão, Vilões..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-sm text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all shadow-inner"
                    >
                </div>

                {{-- Subtítulo --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-1.5">
                        Subtítulo <span class="text-[10px] text-[#8c6239]/70 normal-case font-normal">(opcional)</span>
                    </label>
                    <textarea
                        id="edit_subtitle"
                        name="subtitle"
                        rows="2"
                        placeholder="Breve descrição sobre esta pasta..."
                        class="w-full px-3.5 py-2 rounded-xl bg-[#fefcf8] border border-[#cdbb9f] text-xs text-[#4a3b32] placeholder-[#8c6239]/40 focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] outline-none transition-all resize-none shadow-inner"
                    ></textarea>
                </div>

                {{-- Seletor de Cores --}}
                <div>
                    <label class="block font-serif text-xs font-bold uppercase tracking-wider text-[#6b1d14] mb-2.5">
                        Cor de Destaque
                    </label>
                    
                    <div class="grid grid-cols-5 gap-3 p-3 rounded-xl bg-[#efe9dc]/60 border border-[#cdbb9f]/40">
                        
                        {{-- Couro --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8c6239" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#8c6239] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Couro</span>
                        </label>

                        {{-- Carmesim --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#6b1d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#6b1d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#6b1d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#6b1d14]">Carmesim</span>
                        </label>

                        {{-- Âmbar --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#a65d14" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#a65d14] shadow-md ring-2 ring-transparent peer-checked:ring-[#a65d14] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#a65d14]">Âmbar</span>
                        </label>

                        {{-- Floresta --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2f5e3e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2f5e3e] shadow-md ring-2 ring-transparent peer-checked:ring-[#2f5e3e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2f5e3e]">Floresta</span>
                        </label>

                        {{-- Arcano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#1b5e5e" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#1b5e5e] shadow-md ring-2 ring-transparent peer-checked:ring-[#1b5e5e] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#1b5e5e]">Arcano</span>
                        </label>

                        {{-- Oceano --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2c4a6f" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2c4a6f] shadow-md ring-2 ring-transparent peer-checked:ring-[#2c4a6f] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2c4a6f]">Oceano</span>
                        </label>

                        {{-- Místico --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#5b3265" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#5b3265] shadow-md ring-2 ring-transparent peer-checked:ring-[#5b3265] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#5b3265]">Místico</span>
                        </label>

                        {{-- Magia --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#8a3052" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#8a3052] shadow-md ring-2 ring-transparent peer-checked:ring-[#8a3052] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#8a3052]">Magia</span>
                        </label>

                        {{-- Sombra --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#595959" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#595959] shadow-md ring-2 ring-transparent peer-checked:ring-[#595959] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#595959]">Sombra</span>
                        </label>

                        {{-- Obsidian --}}
                        <label class="flex flex-col items-center gap-1 cursor-pointer group">
                            <input type="radio" name="color" value="#2a2a2a" class="peer sr-only">
                            <span class="w-7 h-7 rounded-full bg-[#2a2a2a] shadow-md ring-2 ring-transparent peer-checked:ring-[#2a2a2a] peer-checked:ring-offset-2 peer-checked:ring-offset-[#f4f1e8] transition-all transform group-hover:scale-105"></span>
                            <span class="text-[9px] font-medium text-[#8c6239] peer-checked:font-bold peer-checked:text-[#2a2a2a]">Obsidian</span>
                        </label>

                    </div>
                </div>
            </div>

            {{-- Rodapé --}}
            <div class="px-6 py-4 border-t border-[#cdbb9f]/50 flex justify-end items-center gap-2.5 bg-[#efe9dc]">
                <button
                    type="button"
                    onclick="closeEditFolderModal()"
                    class="px-4 py-2 rounded-xl border border-[#cdbb9f] font-serif text-xs font-bold text-[#8c6239] hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 transition-all"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-[#6b1d14] hover:bg-[#802319] active:bg-[#54160f] text-[#f4f1e8] font-serif text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-lg transition-all"
                >
                    Salvar Alterações
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
                    {{-- Input da Ficha JSON --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-serif font-bold text-[#6b1d14] uppercase tracking-wider">
                            Arquivo da Ficha (.json) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="file" 
                                id="npc_file"
                                name="npc_file" 
                                accept=".json"
                                required
                                onchange="handleFileChange(event)"
                                class="w-full text-xs text-[#2b1d17] file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-serif file:font-bold file:bg-[#6b1d14] file:text-[#f4f1e8] hover:file:bg-[#7a1f17] file:cursor-pointer bg-[#efe9dc]/60 p-1.5 rounded-xl border border-[#cdbb9f]/40 focus:outline-none"
                            >
                            <button 
                                type="button" 
                                id="clear_file_btn"
                                onclick="clearFileInput()"
                                class="hidden shrink-0 p-2 rounded-xl bg-[#efe9dc]/60 border border-[#cdbb9f]/40 text-[#6b1d14]/60 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-colors focus:outline-none"
                                title="Remover ficha selecionada"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
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
                            onchange="handleImageChange(event)"
                            class="w-full text-xs text-[#2b1d17] file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-serif file:font-bold file:bg-[#8c6239] file:text-[#f4f1e8] hover:file:bg-[#9c7249] file:cursor-pointer bg-[#efe9dc]/60 p-1.5 rounded-xl border border-[#cdbb9f]/40 focus:outline-none"
                        >
                        
                        {{-- Container de Preview da Imagem --}}
                        <div id="image_preview_wrapper" class="hidden relative mt-3 mx-auto w-32 h-32 rounded-xl border-2 border-dashed border-[#cdbb9f]/60 overflow-hidden bg-[#efe9dc]/40">
    <img id="image_preview_el" class="w-full h-full object-cover" src="" alt="Preview da Imagem">
    <button
        type="button"
        onclick="clearImageInput()"
        class="absolute top-1.5 right-1.5 bg-[#f4f1e8]/90 hover:bg-red-100 text-red-600 rounded-full p-1.5 transition-colors shadow-sm focus:outline-none backdrop-blur-sm"
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

function editFolder(button) {
    // 1. Extrai os dados dos atributos data-* do botão
    const id = button.dataset.id;
    const name = button.dataset.name || '';
    const subtitle = button.dataset.subtitle || '';
    const color = (button.dataset.color || '#8c6239').toLowerCase();

    // 2. Preenche a action e os campos de texto
    document.getElementById('editFolderForm').action = `/folders/${id}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_subtitle').value = subtitle;

    // 3. Seleciona o radio button da cor correspondente
    const radios = document.querySelectorAll('#editFolderModal input[name="color"]');
    
    // Procura o radio que possui exatamente a mesma cor (ignorando maiúsculas/minúsculas)
    let matchedRadio = Array.from(radios).find(r => r.value.toLowerCase() === color);

    // Caso a cor antiga não exista nas opções, usa a primeira como padrão para não enviar nulo
    if (!matchedRadio) {
        matchedRadio = radios[0];
    }
    
    matchedRadio.checked = true;

    // 4. Abre o modal
    document.getElementById('editFolderModal').classList.remove('hidden');
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

        // ==========================================
        // NOVAS FUNÇÕES PARA PREVIEW E LIMPEZA DE INPUTS
        // ==========================================

        function handleFileChange(event) {
            const input = event.target;
            const clearBtn = document.getElementById('clear_file_btn');
            
            if (input.files && input.files.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        function clearFileInput() {
            const input = document.getElementById('npc_file');
            input.value = '';
            document.getElementById('clear_file_btn').classList.add('hidden');
        }

        function handleImageChange(event) {
            const input = event.target;
            const previewWrapper = document.getElementById('image_preview_wrapper');
            const previewEl = document.getElementById('image_preview_el');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewEl.src = e.target.result;
                    previewWrapper.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                clearImageInput();
            }
        }

        function clearImageInput() {
            const input = document.getElementById('npc_image');
            const previewWrapper = document.getElementById('image_preview_wrapper');
            const previewEl = document.getElementById('image_preview_el');
            
            input.value = '';
            previewEl.src = '';
            previewWrapper.classList.add('hidden');
        }

        function resetImportForm() {
            clearFileInput();
            clearImageInput();
            document.getElementById('importForm').reset();
        }
    </script>
</x-app-layout>