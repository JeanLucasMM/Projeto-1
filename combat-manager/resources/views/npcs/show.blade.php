<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $npc->header->name }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 py-4">

        {{-- Botão Voltar --}}
        <a
            href="{{ route('npcs.index') }}"
            class="inline-flex items-center gap-2 mb-6 text-xs font-serif font-bold uppercase tracking-wider text-[#6b1d14] hover:text-[#53150f] bg-white/60 hover:bg-white border border-[#cdbb9f]/40 px-3.5 py-2 rounded-xl shadow-sm transition-all"
        >
            <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2.5"
                    d="M15 19l-7-7 7-7"
                />
            </svg>

            Voltar
        </a>

        @if($npc instanceof \App\ViewModels\NativeNpcViewModel)

            @include('statblock.native.sheet', [
                'npc' => $npc
            ])

        @else

            @include('statblock.sheet', [
                'npc' => $npc
            ])

        @endif

    </div>

</x-app-layout>