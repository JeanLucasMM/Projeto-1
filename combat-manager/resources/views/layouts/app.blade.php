<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SpellBound') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Estilo essencial para o x-cloak funcionar e impedir o flash visual --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- AQUI: Adicione esta linha para receber os estilos dos componentes --}}
    @stack('styles')

    
</head>
<body class="font-sans antialiased h-full overflow-hidden bg-[#ebe6dc] text-[#2b1d17]">

    <div class="h-screen w-screen flex overflow-hidden" x-data="{ isExpanded: false, loaded: false }" x-init="setTimeout(() => loaded = true, 50)">

        {{-- Sidebar combinando o funcionamento de Drawer com o design clássico e rústico --}}
        <aside 
            class="h-full flex flex-col flex-shrink-0 bg-[#f4f1e8] border-r border-[#6b1d14]/15 select-none w-16"
            :class="{
                'w-52 shadow-[4px_0_24px_rgba(43,29,23,0.05)]': isExpanded, 
                'w-16': !isExpanded,
                'transition-all duration-300 ease-in-out': loaded
            }"
        >

            {{-- Topo da Sidebar (Estilo Antigo Integrado ao Drawer) --}}
            <div 
                class="py-4 border-b border-[#6b1d14]/15 bg-[#efe9dc] flex-shrink-0 overflow-hidden flex flex-col items-center justify-center"
                :class="{
                    'px-4': isExpanded, 
                    'px-0': !isExpanded,
                    'transition-all duration-300 ease-in-out': loaded
                }"
            >
                <div class="flex items-center gap-2.5 w-full" :class="isExpanded ? 'justify-start' : 'justify-center'">
                    
                    <button 
                        @click="isExpanded = !isExpanded" 
                        class="w-7 h-7 rounded-lg bg-[#6b1d14] text-[#f4f1e8] flex items-center justify-center shrink-0 shadow-sm hover:bg-[#53150f] transition-colors duration-200 focus:outline-none"
                        :title="isExpanded ? 'Recolher menu' : 'Expandir menu'"
                    >
                        <svg x-show="!isExpanded" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        
                        <svg x-show="isExpanded" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div x-show="isExpanded" x-cloak x-transition.opacity.duration.200ms class="truncate">
                        <h1 class="text-xl font-serif font-black text-[#53150f] tracking-wider drop-shadow-[0_1.5px_2px_rgba(0,0,0,0.14)] leading-none">
                            SpellBound
                        </h1>
                    </div>

                </div>

                {{-- Subtítulo e Divisor visíveis apenas quando expandido --}}
                <div x-show="isExpanded" x-cloak x-transition.opacity.duration.200ms class="w-full text-center mt-2">
                    <p class="text-[8.5px] text-[#8c6239] uppercase tracking-[0.2em] font-bold italic drop-shadow-[0_1px_1px_rgba(255,255,255,0.5)]">
                        Grimório de Campanha
                    </p>
                    
                    <div class="flex items-center justify-center gap-1.5 mt-2.5 w-full">
                        <div class="h-[1px] flex-1 bg-gradient-to-r from-transparent to-[#6b1d14]/20"></div>
                        <span class="text-[#6b1d14]/40 text-[7px] select-none">✦</span>
                        <div class="h-[1px] flex-1 bg-gradient-to-l from-transparent to-[#6b1d14]/20"></div>
                    </div>
                </div>
            </div>

            {{-- Links de Navegação --}}
            <div class="flex-1 overflow-y-auto overflow-x-hidden py-4 flex flex-col justify-center">
                @include('layouts.navigation')
            </div>

            {{-- Rodapé --}}
            <div class="p-3 border-t border-[#6b1d14]/15 bg-[#efe9dc] flex-shrink-0 flex flex-col items-center justify-center">
                
                {{-- Conteúdo Completo (Visível quando expandido) --}}
                <div x-show="isExpanded" x-cloak x-transition.opacity.duration.200ms class="w-full text-center">
                    
                    <div class="flex items-center justify-center gap-1.5 mb-2.5 w-full">
                        <div class="h-[1px] flex-1 bg-gradient-to-r from-transparent to-[#6b1d14]/20"></div>
                        <span class="text-[#6b1d14]/40 text-[7px] select-none">✦</span>
                        <div class="h-[1px] flex-1 bg-gradient-to-l from-transparent to-[#6b1d14]/20"></div>
                    </div>

                    <p class="text-[8px] text-[#8c6239]/70 uppercase tracking-[0.22em] font-bold italic mb-0.5">
                        Bem Vindo (a)
                    </p>
                    
                    <p class="text-xs font-serif font-black text-[#53150f] truncate mb-3 capitalize drop-shadow-[0_1px_0px_rgba(255,255,255,0.8)]">
                        {{ Auth::user()->name ?? 'Convidado' }}
                    </p>

                    <form method="POST" action="{{ route('logout') }}" class="m-0 w-full">
                        @csrf
                        <button type="submit" class="w-full text-center py-1.5 px-3 border border-[#6b1d14]/25 hover:border-[#6b1d14]/75 text-[#6b1d14] hover:bg-[#6b1d14]/5 rounded-md text-[9px] font-serif font-bold tracking-widest uppercase transition-all duration-200 shadow-[0_1px_3px_rgba(107,29,20,0.02)] hover:shadow-[0_2.5px_6px_rgba(107,29,20,0.08)] focus:outline-none">
                            Sair
                        </button>
                    </form>
                </div>

                {{-- Botão de Sair Compacto (Visível quando recolhido) --}}
                <div x-show="!isExpanded" class="w-full flex justify-center">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="p-2 text-[#8c6239]/60 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5 rounded-lg transition-colors duration-200 focus:outline-none" title="Sair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>

            </div>

        </aside>

        {{-- Tela Principal --}}
        <div class="flex-1 h-full flex flex-col overflow-hidden">
            <main class="flex-1 overflow-y-auto bg-[#ebe6dc]">
                {{ $slot }}
            </main>
        </div>

    </div>

</body>
</html>