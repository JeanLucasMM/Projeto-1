<x-app-layout>

    <style>
        /* Remove o limite de largura e os paddings padrões apenas do container pai desta página */
        main:has(.combats-index-page-scope),
        .py-12:has(.combats-index-page-scope),
        .max-w-7xl:has(.combats-index-page-scope),
        .sm\:px-6:has(.combats-index-page-scope),
        .lg\:px-8:has(.combats-index-page-scope) {
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
    <div class="combats-index-page-scope min-h-screen bg-[#efe9dc]/30 font-serif pb-12 selection:bg-[#6b1d14] selection:text-[#f4f1e8]">
        
        {{-- Cabeçalho Fixo no Topo (Sticky Header) - Compacto e Idêntico à Biblioteca de NPCs --}}
        <div class="sticky top-0 z-40 bg-[#efe9dc] border-b border-[#cdbb9f]/40 shadow-sm transition-polished">
            <div class="w-full mx-auto px-6 py-3 sm:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    {{-- Título Alinhado --}}
                    <div class="flex items-center gap-3 shrink-0 self-start sm:self-auto">
                        <h1 class="text-lg lg:text-xl font-serif font-bold text-[#6b1d14] tracking-wide">
                            Biblioteca de Combates
                        </h1>
                    </div>

                    {{-- Botão Adicionar Compacto --}}
                    <a href="{{ route('combats.create') }}"
                       class="w-full sm:w-auto px-4 h-9 rounded-xl bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-widest transition-polished shadow-sm flex items-center justify-center gap-1.5 shrink-0 transform hover:-translate-y-0.5 active:translate-y-0 focus:outline-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Adicionar
                    </a>
                    
                </div>
            </div>
        </div>

        {{-- Conteúdo Principal --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">

            {{-- Alerta de Sucesso Estilizado --}}
            @if(session('success'))
                <div class="mb-8 flex items-center p-4 rounded-xl bg-emerald-50 border border-emerald-200/60 shadow-md text-emerald-800">
                    <svg class="w-5 h-5 mr-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-serif text-xs font-semibold leading-relaxed">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Listagem de Combates --}}
            <div class="grid gap-4">
                @forelse($combats as $combat)

                    {{-- Card de Combate --}}
                    <div class="bg-[#f4f1e8] border border-[#cdbb9f]/40 hover:border-[#6b1d14]/30 rounded-2xl shadow-sm hover:shadow-md transition-polished overflow-hidden">
                        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            
                            {{-- Lado Esquerdo: Informações e Atributos Ficha --}}
                            <div class="flex-1 space-y-2.5 text-center sm:text-left w-full">
                                <div>
                                    <h2 class="text-xl font-serif font-bold text-[#6b1d14] leading-tight capitalize">
                                        {{ $combat->name }}
                                    </h2>
                                    <p class="italic text-[10px] text-[#8c6239]/80 font-semibold uppercase tracking-wider mt-0.5">
                                        Encontro de Campanha
                                    </p>
                                </div>

                                {{-- Divisor Rúnico Sutil --}}
                                <div class="h-[1px] bg-gradient-to-r from-[#6b1d14]/20 via-[#cdbb9f]/30 to-transparent w-full"></div>

                                {{-- Painel de Atributos Unificado e Ultra-Sutil --}}
                                <div class="inline-flex items-center gap-3.5 bg-[#efe9dc]/50 border border-[#cdbb9f]/30 px-3 py-1 rounded-xl shadow-inner">
                                    
                                    {{-- Rodada --}}
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] font-serif font-bold text-amber-800 uppercase tracking-wider">Rodada</span>
                                        <span class="text-xs font-mono font-bold text-amber-800 leading-none">{{ $combat->round ?? 1 }}</span>
                                    </div>

                                    {{-- Divisor --}}
                                    <div class="w-[1px] h-3.5 bg-[#cdbb9f]/40"></div>

                                    {{-- Status --}}
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] font-serif font-bold text-[#8c6239]/80 uppercase tracking-wider">Status</span>
                                        @if($combat->started)
                                            <span class="text-xs font-serif font-bold text-emerald-700 leading-none">Em Andamento</span>
                                        @else
                                            <span class="text-xs font-serif font-bold text-[#b45309] leading-none">Não Iniciado</span>
                                        @endif
                                    </div>

                                    {{-- Divisor --}}
                                    <div class="w-[1px] h-3.5 bg-[#cdbb9f]/40"></div>

                                    {{-- Data de Criação --}}
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] font-serif font-bold text-blue-800 uppercase tracking-wider">Criado em</span>
                                        <span class="text-xs font-mono font-bold text-blue-700 leading-none">{{ $combat->created_at->format('d/m/Y') }}</span>
                                    </div>

                                </div>
                            </div>

                            {{-- Lado Direito: Ações --}}
                            <div class="flex sm:flex-row lg:flex-col items-center justify-center gap-2 shrink-0 w-full lg:w-auto self-stretch lg:self-center">
                                
                                <a href="{{ route('combats.show', $combat) }}"
                                   class="flex-1 lg:flex-none px-5 py-2 rounded-xl bg-[#6b1d14] text-[#f4f1e8] text-center font-serif font-bold text-xs uppercase tracking-wider hover:bg-[#53150f] transition-polished shadow-sm">
                                    Entrar
                                </a>

                                <form action="{{ route('combats.destroy', $combat) }}"
                                      method="POST"
                                      class="m-0 flex-1 lg:flex-none w-full"
                                      onsubmit="return confirm('Excluir este combate?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-4 py-2 rounded-xl border border-red-200/60 text-red-700 hover:bg-red-50 text-xs font-serif font-bold uppercase tracking-wider transition-polished">
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
                            Nenhum combate criado
                        </h2>
                        <p class="text-xs text-[#8c6239]/80 italic mt-1 mb-6">
                            Crie seu primeiro combate para iniciar o controle das suas rodadas e iniciativas.
                        </p>
                        <a href="{{ route('combats.create') }}"
                           class="inline-flex items-center gap-1.5 bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-widest px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-polished text-center justify-center border border-[#7A1F17]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Criar Combate
                        </a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</x-app-layout>