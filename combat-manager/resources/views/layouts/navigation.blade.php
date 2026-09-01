<aside class="h-full w-16 flex flex-col flex-shrink-0 bg-[#f4f1e8] border-r border-[#6b1d14]/15 select-none z-40 relative">

    {{-- 1. Topo da Sidebar (Logo Compacta) --}}
    <div class="py-3.5 border-b border-[#6b1d14]/15 bg-[#efe9dc] flex-shrink-0 flex flex-col items-center justify-center">
        <div class="w-8 h-8 rounded-lg bg-[#6b1d14] text-[#f4f1e8] flex items-center justify-center font-serif font-black text-lg shadow-sm cursor-default">
            S
        </div>
    </div>

    {{-- 2. Links de Navegação --}}
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

            {{-- Item: Construtor de NPC --}}
            @php $active = request()->routeIs('npc-builder.*'); @endphp
            <a href="{{ route('npc-builder.index') }}"
               class="group relative flex items-center justify-center w-full h-10 rounded-xl transition-all duration-200 {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Construtor
                </span>
            </a>

            {{-- Item: Cofre / NPCs --}}
            @php $active = request()->routeIs('npcs.*'); @endphp
            <a href="{{ route('npcs.index') }}"
               class="group relative flex items-center justify-center w-full h-10 rounded-xl transition-all duration-200 {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Cofre
                </span>
            </a>

            {{-- Item: Combates --}}
            @php $active = request()->routeIs('combats.*'); @endphp
            <a href="{{ route('combats.index') }}"
               class="group relative flex items-center justify-center w-full h-10 rounded-xl transition-all duration-200 {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Combates
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

            {{-- Item: Configurações --}}
            @php $active = request()->routeIs('settings.*'); @endphp
            <a href="#"
               class="group relative flex items-center justify-center w-full h-10 rounded-xl transition-all duration-200 {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Configurações
                </span>
            </a>

        </nav>
    </div>

    {{-- 3. Rodapé --}}
    <div class="py-4 border-t border-[#6b1d14]/15 bg-[#efe9dc] flex-shrink-0 flex flex-col items-center justify-center gap-3">

        {{-- Trocar para Player --}}
        <form method="POST" action="{{ route('dashboard.mode') }}" class="m-0 w-full px-2">
            @csrf

            <input type="hidden" name="mode" value="player">

            <button
                type="submit"
                class="group relative flex items-center justify-center w-full h-10 text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 rounded-xl transition-colors focus:outline-none"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>

                <span class="absolute left-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#efe9dc] border border-[#6b1d14]/15 text-[#6b1d14] font-serif text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-md whitespace-nowrap pointer-events-none z-50">
                    Modo Player
                </span>
            </button>
        </form>

        {{-- Logout --}}
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