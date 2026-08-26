<x-app-layout>

    <style>
        /* Remove espaçamentos vazios para alinhar ao header */
        main:has(.combats-index-page-scope),
        .py-12:has(.combats-index-page-scope),
        .max-w-7xl:has(.combats-index-page-scope) {
            padding: 0 !important;
            max-width: 100% !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        header {
            background-color: transparent !important;
            border-bottom: none !important;
            box-shadow: none !important;
        }

        aside, .sidebar, body {
            overflow-x: hidden !important;
        }
        
        .transition-polished {
            transition: all 0.25s ease-in-out;
        }
    </style>

    {{-- Gerenciamento de Estado do Modal via Alpine.js --}}
    <div x-data="{ createModalOpen: false }" @keydown.escape.window="createModalOpen = false" class="combats-index-page-scope min-h-screen bg-[#eee8dc] font-serif pb-16 selection:bg-[#6b1d14] selection:text-[#f4f1e8]">

        {{-- Header da página --}}
        <x-slot name="header">
            <div class="flex items-center justify-between gap-4 w-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#6b1d14] flex items-center justify-center text-[#f4f1e8] shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-[#4a100a] leading-tight font-serif">
                            Gerenciador de Combates
                        </h1>
                        <p class="text-[11px] text-[#8c6239] italic font-serif">Gerencie seus encontros e sessões ativas</p>
                    </div>
                </div>

                {{-- Botão Principal do Topo --}}
                <button @click="createModalOpen = true"
                        type="button"
                        class="px-6 py-2.5 rounded-full bg-[#53150f] hover:bg-[#3d0f0a] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-wider transition-polished shadow-md flex items-center gap-2 shrink-0 cursor-pointer">
                    <span class="text-sm font-extrabold leading-none">+</span>
                    <span>Novo Combate</span>
                </button>
            </div>
        </x-slot>

        <main class="max-w-7xl mx-auto px-6 pt-8">

            @if(session('success'))
                <div class="mb-6 flex items-center p-3.5 rounded-xl bg-emerald-100/80 border border-emerald-300 text-emerald-900 shadow-sm">
                    <svg class="w-5 h-5 mr-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-serif text-xs font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex items-center justify-between border-b border-[#d8cebe] pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#e5decb] border border-[#d8cebe] flex items-center justify-center text-[#53150f]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-serif font-bold text-[#4a100a]">Combates Registrados</h2>
                        <p class="text-xs text-[#8c6239] italic">Sessões prontas e em andamento</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 rounded-full text-xs font-serif font-bold text-[#8c6239] bg-[#e5decb] border border-[#d8cebe]">
                        {{ $combats->count() }} {{ $combats->count() === 1 ? 'Combate' : 'Combates' }}
                    </span>
                    <button @click="createModalOpen = true"
                            type="button"
                            class="px-4 py-1.5 rounded-xl bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-wider transition-polished shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <span class="text-sm font-extrabold leading-none">+</span>
                        <span>Criar</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-5">
                @forelse($combats as $combat)

                    {{-- CARD COMPARTIMENTADO --}}
                    <div class="bg-[#fcfaf7] border border-[#d8cebe] rounded-2xl overflow-hidden flex flex-col lg:flex-row items-stretch shadow-sm hover:shadow-md transition-polished">
                        
                        {{-- 1. Info e Título --}}
                        <div class="p-5 flex-1 flex flex-col justify-center min-w-0">
                            <div class="flex items-center gap-3 mb-1.5">
                                <h3 class="text-xl font-serif font-bold text-[#4a100a] truncate">{{ $combat->name }}</h3>
                                @if($combat->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-[#e6f4ea] text-[#1e4620] border border-[#a8d0b2] shrink-0">Iniciado</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-[#fdf2e3] text-[#7a4b13] border border-[#e5ceaf] shrink-0">Não Iniciado</span>
                                @endif
                            </div>
                            <p class="text-[10px] font-serif text-[#8c6239] uppercase tracking-widest font-semibold">
                                Criado em: {{ $combat->created_at->format('d/m/Y') }}
                            </p>
                        </div>

                        {{-- 2. Estatísticas --}}
                        <div class="border-t lg:border-t-0 lg:border-l border-[#d8cebe] bg-[#f9f6f0] p-4 lg:p-5 flex items-center justify-center gap-6 min-w-[180px]">
                            <div class="text-center">
                                <span class="block text-[9px] font-serif font-bold text-[#8c6239] uppercase tracking-widest mb-1">Rodada</span>
                                <span class="text-2xl font-bold text-[#6b1d14] font-mono leading-none">{{ $combat->current_round ?? 0 }}</span>
                            </div>
                            <div class="w-px h-10 bg-[#d8cebe]"></div>
                            <div class="text-center">
                                <span class="block text-[9px] font-serif font-bold text-[#8c6239] uppercase tracking-widest mb-1">NPCs</span>
                                <span class="text-2xl font-bold text-[#6b1d14] font-mono leading-none">{{ $combat->npcs ? $combat->npcs->count() : 0 }}</span>
                            </div>
                        </div>

                        {{-- 3. Combatentes em Campo --}}
                        <div class="border-t lg:border-t-0 lg:border-l border-[#d8cebe] p-4 lg:p-5 flex flex-col justify-center items-center lg:items-start min-w-[220px]">
                            <span class="text-[9px] font-serif font-bold text-[#8c6239] uppercase tracking-widest mb-2">Combatentes</span>
                            
                            @php
                                $participants = $combat->npcs ?? collect();
                            @endphp

                            @if($participants->count() > 0)
                                <div class="flex -space-x-3 overflow-hidden p-1">
                                    @foreach($participants->take(5) as $combatNpc)
                                        @php $npc = $combatNpc->npc; @endphp
                                        @if($npc)
                                            <div class="inline-block h-10 w-10 rounded-full border-2 border-[#fcfaf7] bg-gradient-to-br from-[#8c6239] to-[#53150f] shadow-sm flex items-center justify-center overflow-hidden" title="{{ $npc->name }}">
                                                @if($npc->image_path)
                                                    <img src="{{ asset('storage/' . $npc->image_path) }}" alt="{{ $npc->name }}" class="w-full h-full object-cover object-top">
                                                @else
                                                    <span class="text-[#f4f1e8] font-bold text-[10px]">{{ strtoupper(substr($npc->name, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach

                                    @if($participants->count() > 5)
                                        <div class="inline-flex items-center justify-center h-10 w-10 rounded-full border-2 border-[#fcfaf7] bg-[#e5decb] text-[#53150f] font-bold text-[10px] shadow-sm z-10">
                                            +{{ $participants->count() - 5 }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="h-10 flex items-center text-[10px] text-[#8c6239] italic">Nenhum inserido</div>
                            @endif
                        </div>

                        {{-- 4. Botões de Ação --}}
                        <div class="border-t lg:border-t-0 lg:border-l border-[#d8cebe] p-4 lg:p-5 flex items-center justify-center gap-2 bg-[#f9f6f0] shrink-0">
                            <a href="{{ route('combats.show', $combat) }}"
                               class="px-5 py-2.5 rounded-lg bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-[10px] uppercase tracking-widest transition-polished shadow-sm text-center w-full lg:w-auto">
                                Abrir Combate
                            </a>
                            <form action="{{ route('combats.destroy', $combat) }}" method="POST" class="m-0" onsubmit="return confirm('Deseja apagar este combate?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 rounded-lg bg-white border border-[#d8cebe] text-[#8c6239] hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition-colors shadow-sm" title="Excluir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>

                    </div>

                @empty
                    
                    <div class="bg-[#fcfaf7] border-2 border-dashed border-[#d8cebe] rounded-2xl p-12 text-center">
                        <div class="w-12 h-12 bg-[#eee8dc] rounded-full flex items-center justify-center mx-auto mb-3 text-[#8c6239]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-serif font-bold text-[#4a100a] mb-1">Nenhum combate encontrado</h3>
                        <p class="text-xs text-[#8c6239] italic mb-4">Crie um novo encontro para começar a organizar sua sessão.</p>
                        <button @click="createModalOpen = true"
                                type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-[#6b1d14] hover:bg-[#53150f] transition-colors text-[#f4f1e8] rounded-xl text-xs font-serif font-bold uppercase tracking-wider cursor-pointer">
                            Novo Combate
                        </button>
                    </div>

                @endforelse
            </div>

        </main>

        {{-- MODAL DE CRIAÇÃO DE COMBATE --}}
        <div x-show="createModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-[#2b1d17]/60 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">

            {{-- Container do Modal --}}
            <div @click.away="createModalOpen = false"
                 x-show="createModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-gradient-to-br from-[#f4f1e8] to-[#efe9dc] rounded-2xl border border-[#cdbb9f]/80 shadow-2xl p-6 sm:p-8 overflow-hidden font-serif">
                
                {{-- Detalhes Decorativos RPG nas cantoneiras --}}
                <div class="absolute top-0 left-0 w-10 h-10 border-t-2 border-l-2 border-[#8c6239]/20 rounded-tl-2xl m-3 pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 w-10 h-10 border-b-2 border-r-2 border-[#8c6239]/20 rounded-br-2xl m-3 pointer-events-none"></div>

                {{-- Cabeçalho do Modal --}}
                <div class="flex items-center justify-between pb-4 border-b border-[#cdbb9f]/60 mb-6 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#6b1d14] flex items-center justify-center text-[#f4f1e8] shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-[#6b1d14] leading-tight">Novo Combate</h3>
                            <p class="text-[10px] uppercase font-bold text-[#8c6239]/80 tracking-widest">Inicie um novo encontro tático</p>
                        </div>
                    </div>
                    <button @click="createModalOpen = false" class="text-[#8c6239] hover:text-[#6b1d14] transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Formulário --}}
                <form action="{{ route('combats.store') }}" method="POST" class="space-y-6 relative z-10">
                    @csrf

                    <div class="space-y-2">
                        <label for="name" class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#8c6239]">
                            <svg class="w-3.5 h-3.5 text-[#6b1d14]/70" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Nome do Encontro
                        </label>
                        
                        <input 
                            type="text" 
                            id="name"
                            name="name" 
                            required
                            placeholder="Ex.: Emboscada No Amanhecer Prateado" 
                            class="w-full bg-[#f4f1e8] border border-[#cdbb9f] rounded-xl px-4 py-3 text-[#2b1d17] placeholder-[#8c6239]/40 focus:outline-none focus:ring-2 focus:ring-[#6b1d14]/30 focus:border-[#6b1d14] transition-polished shadow-inner text-sm font-serif"
                        >
                        
                        <p class="text-[11px] text-[#8c6239]/80 italic pl-1">
                            Dica: Use nomes sugestivos para organizar o diário da campanha.
                        </p>
                    </div>

                    <div class="flex items-center justify-center gap-4 py-1">
                        <div class="h-px bg-gradient-to-r from-transparent via-[#cdbb9f]/60 to-transparent w-full"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-[#cdbb9f]/80 rotate-45"></div>
                        <div class="h-px bg-gradient-to-r from-transparent via-[#cdbb9f]/60 to-transparent w-full"></div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-1">
                        <button type="button"
                                @click="createModalOpen = false"
                                class="w-full sm:w-auto inline-flex items-center justify-center bg-transparent hover:bg-[#8c6239]/10 text-[#8c6239] border border-[#8c6239]/40 hover:text-[#6b1d14] text-xs font-bold uppercase tracking-widest px-5 py-3 rounded-xl transition-polished cursor-pointer">
                            Cancelar
                        </button>

                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-b from-[#6b1d14] to-[#53150f] hover:from-[#7A1F17] hover:to-[#6b1d14] text-[#f4f1e8] text-xs font-bold uppercase tracking-widest px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition-polished border border-[#4a110a] cursor-pointer">
                            <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Criar Combate
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</x-app-layout>