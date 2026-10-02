<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spellbound') }}</title>

        <link
            rel="icon"
            type="image/png"
            href="{{ asset('images/book.png') }}"
        >

        <link
            rel="preconnect"
            href="https://fonts.bunny.net"
        >

        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap"
            rel="stylesheet"
        >

        @vite([
            'resources/css/app.css',
            'resources/js/app.js'
        ])
    </head>

    <body
        class="
            min-h-screen
            bg-[#120d0b]
            font-sans
            text-[#34231b]
            antialiased
        "
    >
        <div
            class="
                relative
                flex
                min-h-screen
                items-center
                justify-center
                overflow-hidden
                px-4
                py-8
                sm:px-6
            "
        >
            <div
                class="
                    pointer-events-none
                    absolute
                    inset-0
                    bg-[radial-gradient(circle_at_50%_18%,rgba(107,29,20,0.24),transparent_34%),linear-gradient(180deg,#17100d_0%,#100c0a_100%)]
                "
            ></div>

            <div
                class="
                    pointer-events-none
                    absolute
                    inset-x-0
                    top-0
                    h-px
                    bg-gradient-to-r
                    from-transparent
                    via-[#a0523d]/55
                    to-transparent
                "
            ></div>

            <main
                class="
                    relative
                    z-10
                    w-full
                    max-w-md
                "
            >
                <div
                    class="
                        mb-6
                        flex
                        justify-center
                    "
                >
                    <a
                        href="/"
                        class="
                            group
                            inline-flex
                            items-center
                            gap-3
                            rounded-xl
                            outline-none
                            transition
                            focus-visible:ring-2
                            focus-visible:ring-[#a0523d]/55
                            focus-visible:ring-offset-4
                            focus-visible:ring-offset-[#120d0b]
                        "
                    >
                        <span
                            class="
                                flex
                                h-12
                                w-12
                                items-center
                                justify-center
                                rounded-xl
                                border
                                border-[#9b382b]/55
                                bg-gradient-to-b
                                from-[#7f261d]
                                to-[#53150f]
                                text-[23px]
                                shadow-[0_10px_28px_rgba(0,0,0,.26)]
                                transition
                                group-hover:-translate-y-0.5
                                group-hover:border-[#b45e48]/70
                            "
                            aria-hidden="true"
                        >
                            ⚔️
                        </span>

                        <span
                            class="
                                font-serif
                                text-[24px]
                                font-black
                                uppercase
                                tracking-[0.10em]
                                text-[#fffaf2]
                                drop-shadow-sm
                            "
                        >
                            {{ config('app.name', 'Spellbound') }}
                        </span>
                    </a>
                </div>

                <section
                    class="
                        relative
                        overflow-hidden
                        rounded-2xl
                        border
                        border-[#a06d45]/45
                        bg-[#fffaf2]
                        shadow-[0_28px_80px_rgba(0,0,0,.38)]
                    "
                >
                    <div
                        class="
                            absolute
                            inset-x-0
                            top-0
                            h-[3px]
                            bg-gradient-to-r
                            from-transparent
                            via-[#7f261d]
                            to-transparent
                        "
                    ></div>

                    <div
                        class="
                            px-6
                            py-7
                            sm:px-7
                            sm:py-8
                        "
                    >
                        {{ $slot }}
                    </div>
                </section>

                <p
                    class="
                        mt-4
                        text-center
                        text-[10px]
                        font-semibold
                        tracking-[0.04em]
                        text-[#cdbba7]/60
                    "
                >
                    Spellbound · Combat Manager
                </p>
            </main>
        </div>
    </body>
</html>