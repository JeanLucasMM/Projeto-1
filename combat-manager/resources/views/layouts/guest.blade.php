<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Grimoire D&D') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/book.png') }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#f4f1e8] bg-[#121110] antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 px-4">
            {{-- Logo / Ícone da Aplicação --}}
            <div>
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 bg-[#5a1810] rounded-lg flex items-center justify-center font-bold text-white text-2xl shadow-lg border border-[#7a2016] group-hover:bg-[#7a2016] transition">
                        ⚔️
                    </div>
                    <span class="font-extrabold text-2xl tracking-wider text-[#f4f1e8] uppercase">
                        {{ config('app.name', 'Grimoire D&D') }}
                    </span>
                </a>
            </div>

            {{-- Card do Formulário --}}
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-[#1a1816] border border-[#5a1810]/40 shadow-2xl overflow-hidden sm:rounded-xl relative">
                {{-- Detalhe estético no topo do card --}}
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#5a1810] to-transparent"></div>

                {{ $slot }}
            </div>
        </div>
    </body>
</html>