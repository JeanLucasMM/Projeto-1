<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SpellBound') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/book.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased h-full overflow-hidden bg-[#ebe6dc] text-[#2b1d17]">

    @php
        $dashboardMode = session('dashboard_mode');
    @endphp

    <div
        class="h-screen w-screen flex overflow-hidden"
        x-data="{
            isExpanded: localStorage.getItem('spellbound-sidebar-expanded') === 'true',
            loaded: false,

            toggleSidebar() {
                this.isExpanded = !this.isExpanded;

                localStorage.setItem(
                    'spellbound-sidebar-expanded',
                    this.isExpanded
                );
            }
        }"
        x-init="setTimeout(() => loaded = true, 50)"
    >

        {{-- ========================================================= --}}
        {{-- NAVEGAÇÃO --}}
        {{-- ========================================================= --}}

        @if ($dashboardMode === 'master')

            @include('layouts.navigation')

        @elseif ($dashboardMode === 'player')

            @include('layouts.player-navigation')

        @endif


        {{-- ========================================================= --}}
        {{-- CONTEÚDO --}}
        {{-- ========================================================= --}}

        <div class="flex-1 h-full flex flex-col overflow-hidden">

            <main class="flex-1 overflow-y-auto bg-[#ebe6dc]">

                {{ $slot }}

            </main>

        </div>

    </div>

</body>
</html>