<aside class="h-full w-16 flex flex-col flex-shrink-0 bg-[#f4f1e8] border-r border-[#6b1d14]/15 select-none z-40 relative">

    {{-- Topo da Sidebar (Logo Compacta) --}}
    <div class="py-3.5 border-b border-[#6b1d14]/15 bg-[#efe9dc] flex-shrink-0 flex flex-col items-center justify-center">
        <div class="w-8 h-8 rounded-lg bg-[#6b1d14] text-[#f4f1e8] flex items-center justify-center font-serif font-black text-lg shadow-sm cursor-default">
            S
        </div>
    </div>

    {{-- Links de Navegação --}}
    <div class="flex-1 py-4 flex flex-col items-center w-full">
        <nav class="space-y-3 w-full px-2">

            {{-- Item: Dashboard --}}
            @php $active = request()->routeIs('dashboard'); @endphp
            <a href="{{ route('dashboard') }}"
               class="group relative flex items-center justify-center w-full h-10 rounded-xl transition-all duration-200 {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>

                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Dashboard
                </span>
            </a>

            {{-- Item: Personagens --}}
            @php $active = request()->routeIs('characters.*'); @endphp
            <a href="{{ route('characters.index') }}"
               class="group relative flex items-center justify-center w-full h-10 rounded-xl transition-all duration-200 {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 20a6 6 0 00-12 0M9 10a4 4 0 100-8 4 4 0 000 8zM17 13a5 5 0 015 5v2" />
                </svg>

                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Personagens
                </span>
            </a>

            {{-- Item: Campanhas --}}
            @php $active = request()->routeIs('campaigns.*') || request()->routeIs('campaign-invitations.*'); @endphp
            <a href="{{ route('campaigns.index') }}"
               class="group relative flex items-center justify-center w-full h-10 rounded-xl transition-all duration-200 {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 016.5 3H20v17H6.5A2.5 2.5 0 014 17.5v-12z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5" />
                </svg>

                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Campanhas
                </span>
            </a>

        </nav>
    </div>

    {{-- Rodapé --}}
    <div class="py-4 border-t border-[#6b1d14]/15 bg-[#efe9dc] flex-shrink-0 flex flex-col items-center justify-center gap-3">

        {{-- Trocar para Mestre --}}
        <form method="POST" action="{{ route('dashboard.mode') }}" class="m-0 w-full px-2">
            @csrf

            <input type="hidden" name="mode" value="master">

            <button
                type="submit"
                class="group relative flex items-center justify-center w-full h-10 text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 rounded-xl transition-colors focus:outline-none"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>

                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Modo Mestre
                </span>
            </button>
        </form>

        {{-- Sair --}}
        <form method="POST" action="{{ route('logout') }}" class="m-0 w-full px-2">
            @csrf

            <button
                type="submit"
                class="group relative flex items-center justify-center w-full h-10 text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 rounded-xl transition-colors focus:outline-none"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>

                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Sair
                </span>
            </button>
        </form>

    </div>
</aside>