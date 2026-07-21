<nav class="space-y-1.5 px-2 w-full">

    {{-- Item: Dashboard --}}
    <x-sidebar.item :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        @php $active = request()->routeIs('dashboard'); @endphp
        <div 
            class="flex items-center w-full h-9 rounded-xl transition-all duration-200 group font-serif text-[11px] font-bold uppercase tracking-widest {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}"
            :class="isExpanded ? 'justify-start px-2.5 gap-2.5' : 'justify-center px-0'"
            title="Dashboard"
        >
            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ $active ? 'text-[#f4f1e8]' : 'text-[#8c6239]/70 group-hover:text-[#6b1d14]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
            </svg>
            <span x-show="isExpanded" x-cloak x-transition.opacity.duration.150ms class="whitespace-nowrap">
                Dashboard
            </span>
        </div>
    </x-sidebar.item>

    {{-- Item: NPCs --}}
    <x-sidebar.item :href="route('npcs.index')" :active="request()->routeIs('npcs.*')">
        @php $active = request()->routeIs('npcs.*'); @endphp
        <div 
            class="flex items-center w-full h-9 rounded-xl transition-all duration-200 group font-serif text-[11px] font-bold uppercase tracking-widest {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}"
            :class="isExpanded ? 'justify-start px-2.5 gap-2.5' : 'justify-center px-0'"
            title="NPCs"
        >
            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ $active ? 'text-[#f4f1e8]' : 'text-[#8c6239]/70 group-hover:text-[#6b1d14]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span x-show="isExpanded" x-cloak x-transition.opacity.duration.150ms class="whitespace-nowrap">
                NPCs
            </span>
        </div>
    </x-sidebar.item>

    {{-- Item: Combates --}}
    <x-sidebar.item :href="route('combats.index')" :active="request()->routeIs('combats.*')">
        @php $active = request()->routeIs('combats.*'); @endphp
        <div 
            class="flex items-center w-full h-9 rounded-xl transition-all duration-200 group font-serif text-[11px] font-bold uppercase tracking-widest {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}"
            :class="isExpanded ? 'justify-start px-2.5 gap-2.5' : 'justify-center px-0'"
            title="Combates"
        >
            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ $active ? 'text-[#f4f1e8]' : 'text-[#8c6239]/70 group-hover:text-[#6b1d14]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span x-show="isExpanded" x-cloak x-transition.opacity.duration.150ms class="whitespace-nowrap">
                Combates
            </span>
        </div>
    </x-sidebar.item>

    {{-- Item: Configurações --}}
    <x-sidebar.item href="#" :active="request()->routeIs('settings.*')">
        @php $active = request()->routeIs('settings.*'); @endphp
        <div 
            class="flex items-center w-full h-9 rounded-xl transition-all duration-200 group font-serif text-[11px] font-bold uppercase tracking-widest {{ $active ? 'bg-[#6b1d14] text-[#f4f1e8] shadow-sm' : 'text-[#8c6239]/80 hover:text-[#6b1d14] hover:bg-[#6b1d14]/5' }}"
            :class="isExpanded ? 'justify-start px-2.5 gap-2.5' : 'justify-center px-0'"
            title="Configurações"
        >
            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 group-hover:scale-105 {{ $active ? 'text-[#f4f1e8]' : 'text-[#8c6239]/70 group-hover:text-[#6b1d14]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span x-show="isExpanded" x-cloak x-transition.opacity.duration.150ms class="whitespace-nowrap">
                Configurações
            </span>
        </div>
    </x-sidebar.item>

</nav>