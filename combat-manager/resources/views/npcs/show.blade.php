<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $npc->header->name }}
        </h2>
    </x-slot>

    <div class="statblock">

        {{-- A div de fluxo foi movida para cá, abraçando todo o conteúdo --}}
        <div class="content-flow">

            <x-statblock.header :npc="$npc"/>

            <x-statblock.abilities :npc="$npc"/>

            <x-statblock.details :npc="$npc"/>

            <hr class="stat-divider">

            @foreach($npc->sections as $section)

                <x-statblock.section :section="$section"/>

            @endforeach

        </div> {{-- Fechamento da div content-flow modificado para cá --}}

    </div>

</x-app-layout>