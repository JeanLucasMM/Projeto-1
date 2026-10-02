<aside
    class="
        player-sidebar
        relative
        z-40
        flex
        h-full
        w-16
        flex-shrink-0
        select-none
        flex-col
        border-r
    "
>
    {{-- Topo da Sidebar (Logo Compacta) --}}
    <div
        class="
            player-sidebar__top
            flex
            flex-shrink-0
            flex-col
            items-center
            justify-center
            border-b
            py-3.5
        "
    >
        <div
            class="
                player-sidebar__logo
                flex
                h-8
                w-8
                cursor-default
                items-center
                justify-center
                rounded-lg
                font-serif
                text-lg
                font-black
            "
        >
            S
        </div>
    </div>

    {{-- Links de Navegação --}}
    <div class="flex w-full flex-1 flex-col items-center py-4">
        <nav class="w-full space-y-3 px-2">

            {{-- Item: Dashboard --}}
            @php $active = request()->routeIs('dashboard'); @endphp
            <a
                href="{{ route('dashboard') }}"
                class="
                    player-sidebar__link
                    {{ $active ? 'player-sidebar__link--active' : '' }}
                    group
                    relative
                    flex
                    h-10
                    w-full
                    items-center
                    justify-center
                    rounded-xl
                    transition-all
                    duration-200
                "
            >
                <svg
                    class="h-5 w-5 transition-transform duration-200 group-hover:scale-110"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"
                    />
                </svg>

                <span
                    class="
                        player-sidebar__tooltip
                        pointer-events-none
                        absolute
                        left-14
                        z-50
                        whitespace-nowrap
                        rounded-md
                        border
                        px-3
                        py-1.5
                        font-serif
                        text-[11px]
                        font-bold
                        uppercase
                        tracking-widest
                        opacity-0
                        shadow-md
                        transition-all
                        duration-200
                        group-hover:opacity-100
                    "
                >
                    Dashboard
                </span>
            </a>

            {{-- Item: Personagens --}}
            @php $active = request()->routeIs('characters.*'); @endphp
            <a
                href="{{ route('characters.index') }}"
                class="
                    player-sidebar__link
                    {{ $active ? 'player-sidebar__link--active' : '' }}
                    group
                    relative
                    flex
                    h-10
                    w-full
                    items-center
                    justify-center
                    rounded-xl
                    transition-all
                    duration-200
                "
            >
                <svg
                    class="h-5 w-5 transition-transform duration-200 group-hover:scale-110"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 20a6 6 0 00-12 0M9 10a4 4 0 100-8 4 4 0 000 8zM17 13a5 5 0 015 5v2"
                    />
                </svg>

                <span
                    class="
                        player-sidebar__tooltip
                        pointer-events-none
                        absolute
                        left-14
                        z-50
                        whitespace-nowrap
                        rounded-md
                        border
                        px-3
                        py-1.5
                        font-serif
                        text-[11px]
                        font-bold
                        uppercase
                        tracking-widest
                        opacity-0
                        shadow-md
                        transition-all
                        duration-200
                        group-hover:opacity-100
                    "
                >
                    Personagens
                </span>
            </a>

            {{-- Item: Campanhas --}}
            @php
                $active = request()->routeIs('campaigns.*')
                    || request()->routeIs('campaign-invitations.*');
            @endphp
            <a
                href="{{ route('campaigns.index') }}"
                class="
                    player-sidebar__link
                    {{ $active ? 'player-sidebar__link--active' : '' }}
                    group
                    relative
                    flex
                    h-10
                    w-full
                    items-center
                    justify-center
                    rounded-xl
                    transition-all
                    duration-200
                "
            >
                <svg
                    class="h-5 w-5 transition-transform duration-200 group-hover:scale-110"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 5.5A2.5 2.5 0 016.5 3H20v17H6.5A2.5 2.5 0 014 17.5v-12z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M8 7h8M8 11h8M8 15h5"
                    />
                </svg>

                <span
                    class="
                        player-sidebar__tooltip
                        pointer-events-none
                        absolute
                        left-14
                        z-50
                        whitespace-nowrap
                        rounded-md
                        border
                        px-3
                        py-1.5
                        font-serif
                        text-[11px]
                        font-bold
                        uppercase
                        tracking-widest
                        opacity-0
                        shadow-md
                        transition-all
                        duration-200
                        group-hover:opacity-100
                    "
                >
                    Campanhas
                </span>
            </a>

        </nav>
    </div>

    {{-- Rodapé --}}
    <div
        class="
            player-sidebar__footer
            flex
            flex-shrink-0
            flex-col
            items-center
            justify-center
            gap-3
            border-t
            py-4
        "
    >
        {{-- Trocar para Mestre --}}
        <form
            method="POST"
            action="{{ route('dashboard.mode') }}"
            class="m-0 w-full px-2"
        >
            @csrf

            <input type="hidden" name="mode" value="master">

            <button
                type="submit"
                class="
                    player-sidebar__link
                    group
                    relative
                    flex
                    h-10
                    w-full
                    items-center
                    justify-center
                    rounded-xl
                    transition-colors
                    focus:outline-none
                "
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"
                    />
                </svg>

                <span
                    class="
                        player-sidebar__tooltip
                        pointer-events-none
                        absolute
                        left-14
                        z-50
                        whitespace-nowrap
                        rounded-md
                        border
                        px-3
                        py-1.5
                        font-serif
                        text-[11px]
                        font-bold
                        uppercase
                        tracking-widest
                        opacity-0
                        shadow-md
                        transition-all
                        duration-200
                        group-hover:opacity-100
                    "
                >
                    Modo Mestre
                </span>
            </button>
        </form>

        {{-- Sair --}}
        <form
            method="POST"
            action="{{ route('logout') }}"
            class="m-0 w-full px-2"
        >
            @csrf

            <button
                type="submit"
                class="
                    player-sidebar__link
                    group
                    relative
                    flex
                    h-10
                    w-full
                    items-center
                    justify-center
                    rounded-xl
                    transition-colors
                    focus:outline-none
                "
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>

                <span
                    class="
                        player-sidebar__tooltip
                        pointer-events-none
                        absolute
                        left-14
                        z-50
                        whitespace-nowrap
                        rounded-md
                        border
                        px-3
                        py-1.5
                        font-serif
                        text-[11px]
                        font-bold
                        uppercase
                        tracking-widest
                        opacity-0
                        shadow-md
                        transition-all
                        duration-200
                        group-hover:opacity-100
                    "
                >
                    Sair
                </span>
            </button>
        </form>
    </div>
</aside>