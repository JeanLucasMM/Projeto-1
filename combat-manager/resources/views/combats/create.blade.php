<x-app-layout>

    <style>
        /* Remove o limite de largura e os paddings padrões apenas do container pai desta página */
        main:has(.combat-create-page-scope),
        .py-12:has(.combat-create-page-scope),
        .max-w-7xl:has(.combat-create-page-scope),
        .sm\:px-6:has(.combat-create-page-scope),
        .lg\:px-8:has(.combat-create-page-scope) {
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    {{-- Wrapper de Escopo da Página --}}
    <div class="combat-create-page-scope min-h-screen bg-[#efe9dc]/40 font-serif pb-12 selection:bg-[#6b1d14] selection:text-[#f4f1e8]">
        
        {{-- Cabeçalho Fixo no Topo (Sticky Header) - Padrão 60px --}}
        <div class="sticky top-0 z-40 bg-[#efe9dc]/95 backdrop-blur-sm border-b border-[#cdbb9f]/60 shadow-sm transition-polished h-[60px] flex items-center">
            <div class="w-full mx-auto px-6 sm:px-8">
                <div class="flex items-center gap-3">
                    
                    {{-- Ícone do Cabeçalho --}}
                    <div class="flex-shrink-0 text-[#6b1d14]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>

                    {{-- Título Alinhado com Subtítulo Compacto --}}
                    <div class="flex flex-col shrink-0 justify-center min-w-0 leading-none mt-0.5">
                        <h1 class="text-lg font-serif font-bold text-[#6b1d14] tracking-wide truncate">
                            Novo Combate
                        </h1>
                        <p class="text-[9px] font-serif font-bold uppercase tracking-widest text-[#8c6239]/80 truncate mt-0.5">
                            Inicie um novo encontro tático
                        </p>
                    </div>
                    
                </div>
            </div>
        </div>

        {{-- Área de Conteúdo Centralizada --}}
        <div class="px-4 py-12 sm:px-6 lg:px-8 flex justify-center items-start">
            
            {{-- Card do Formulário --}}
            <div class="relative w-full max-w-lg bg-gradient-to-br from-[#f4f1e8] to-[#efe9dc] rounded-2xl border border-[#cdbb9f]/60 shadow-lg shadow-[#8c6239]/10 hover:shadow-xl transition-polished p-8 sm:p-10 overflow-hidden">
                
                {{-- Detalhes Decorativos (Cantoneiras RPG) --}}
                <div class="absolute top-0 left-0 w-12 h-12 border-t-2 border-l-2 border-[#8c6239]/10 rounded-tl-2xl m-3 pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 w-12 h-12 border-b-2 border-r-2 border-[#8c6239]/10 rounded-br-2xl m-3 pointer-events-none"></div>

                <form action="{{ route('combats.store') }}" method="POST" class="space-y-7 relative z-10">
                    @csrf

                    {{-- Campo: Nome do Combate --}}
                    <div class="space-y-3">
                        <label for="name" class="flex items-center gap-2 text-[11px] font-serif font-bold uppercase tracking-widest text-[#8c6239]">
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
                            class="w-full bg-[#f4f1e8]/80 border border-[#cdbb9f]/80 rounded-xl px-4 py-3.5 text-[#2b1d17] placeholder-[#8c6239]/40 focus:outline-none focus:ring-2 focus:ring-[#6b1d14]/30 focus:border-[#6b1d14] transition-polished shadow-inner text-sm font-serif hover:border-[#8c6239]"
                        >
                        
                        <p class="text-[11.5px] text-[#8c6239]/80 italic leading-relaxed pl-1">
                            Dica: Use nomes sugestivos para organizar o diário da campanha.
                        </p>
                    </div>

                    {{-- Divisor Rúnico Sutil --}}
                    <div class="flex items-center justify-center gap-4 py-2">
                        <div class="h-px bg-gradient-to-r from-transparent via-[#cdbb9f]/50 to-transparent w-full"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-[#cdbb9f]/60 rotate-45"></div>
                        <div class="h-px bg-gradient-to-r from-transparent via-[#cdbb9f]/50 to-transparent w-full"></div>
                    </div>

                    {{-- Ações do Formulário --}}
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-4 pt-2">
                        
                        {{-- Botão Cancelar --}}
                        <a href="{{ route('combats.index') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-transparent hover:bg-[#8c6239]/5 text-[#8c6239] border border-[#8c6239]/30 hover:border-[#8c6239]/80 hover:text-[#6b1d14] text-xs font-serif font-bold uppercase tracking-widest px-6 py-3.5 rounded-xl transition-polished text-center focus:ring-2 focus:ring-[#cdbb9f]">
                            Cancelar
                        </a>

                        {{-- Botão Criar Combate --}}
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-b from-[#6b1d14] to-[#53150f] hover:from-[#7A1F17] hover:to-[#6b1d14] text-[#f4f1e8] text-xs font-serif font-bold uppercase tracking-widest px-6 py-3.5 rounded-xl shadow-md hover:shadow-lg transition-polished border border-[#4a110a] text-center transform hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6b1d14] focus:ring-offset-[#efe9dc]">
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