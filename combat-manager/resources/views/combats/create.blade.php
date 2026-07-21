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
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    {{-- Wrapper de Escopo da Página --}}
    <div class="combat-create-page-scope min-h-screen bg-[#efe9dc]/30 font-serif pb-12 selection:bg-[#6b1d14] selection:text-[#f4f1e8]">
        
        {{-- Cabeçalho Fixo no Topo (Sticky Header) - Altura Padronizada de 60px alinhada com o Drawer --}}
        <div class="sticky top-0 z-40 bg-[#efe9dc] border-b border-[#cdbb9f]/40 shadow-sm transition-polished h-[60px] flex items-center">
            <div class="w-full mx-auto px-6 sm:px-8">
                <div class="flex items-center justify-between gap-4">
                    
                    {{-- Título Alinhado com Subtítulo Compacto --}}
                    <div class="flex flex-col shrink-0 justify-center min-w-0 leading-tight">
                        <h1 class="text-lg lg:text-xl font-serif font-bold text-[#6b1d14] tracking-wide truncate">
                            Novo Combate
                        </h1>
                        <p class="text-[9px] font-serif font-bold uppercase tracking-wider text-[#8c6239]/80 truncate">
                            Inicie um novo encontro tático para sua mesa
                        </p>
                    </div>
                    
                </div>
            </div>
        </div>

        {{-- Área de Conteúdo Centralizada --}}
        <div class="px-4 py-12 sm:px-6 lg:px-8 flex justify-center items-start">
            
            {{-- Card do Formulário --}}
            <div class="w-full max-w-lg bg-[#f4f1e8] rounded-2xl border border-[#cdbb9f]/40 shadow-sm hover:shadow-md transition-polished p-6 sm:p-8">
                
                <form action="{{ route('combats.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Campo: Nome do Combate --}}
                    <div class="space-y-2.5">
                        <label for="name" class="block text-[10px] font-serif font-bold uppercase tracking-widest text-[#8c6239]/90">
                            Nome do Encontro / Combate
                        </label>
                        
                        <input 
                            type="text" 
                            id="name"
                            name="name" 
                            required
                            placeholder="Ex.: Emboscada na Estrada de Phandalin" 
                            class="w-full bg-[#efe9dc]/50 border border-[#cdbb9f]/60 rounded-xl px-4 py-3.5 text-[#2b1d17] placeholder-[#8c6239]/40 focus:outline-none focus:ring-2 focus:ring-[#6b1d14]/20 focus:border-[#6b1d14] transition-polished shadow-inner text-sm font-serif"
                        >
                        
                        <p class="text-[11px] text-[#8c6239]/80 italic leading-relaxed">
                            Dica: Use nomes sugestivos e memoráveis para organizar melhor seu diário de campanha.
                        </p>
                    </div>

                    {{-- Divisor Rúnico Sutil --}}
                    <hr class="border-[#cdbb9f]/30 my-6">

                    {{-- Ações do Formulário --}}
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-2">
                        
                        {{-- Botão Cancelar --}}
                        <a href="{{ route('combats.index') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center bg-transparent hover:bg-[#6b1d14]/5 text-[#6b1d14] border border-[#6b1d14]/20 hover:border-[#6b1d14]/60 text-xs font-serif font-bold uppercase tracking-widest px-6 py-3.5 rounded-xl transition-polished text-center">
                            Cancelar
                        </a>

                        {{-- Botão Criar Combate --}}
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] text-xs font-serif font-bold uppercase tracking-widest px-6 py-3.5 rounded-xl shadow-sm hover:shadow-md transition-polished border border-[#7A1F17] text-center transform hover:-translate-y-0.5 active:translate-y-0 focus:outline-none">
                            Criar Combate
                        </button>
                        
                    </div>
                </form>

            </div>

        </div>
    </div>

</x-app-layout>