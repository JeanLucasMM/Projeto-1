<x-guest-layout>
    <div>
        <div class="mb-6">
            <p
                class="
                    text-[9px]
                    font-black
                    uppercase
                    tracking-[0.15em]
                    text-[#93623d]
                "
            >
                Bem-vindo de volta
            </p>

            <h1
                class="
                    mt-1
                    font-serif
                    text-[27px]
                    font-black
                    leading-none
                    text-[#53150f]
                "
            >
                Entrar no Spellbound
            </h1>

            <p
                class="
                    mt-2
                    text-[12px]
                    leading-relaxed
                    text-[#755847]
                "
            >
                Acesse sua conta para continuar sua aventura.
            </p>
        </div>

        @if (session('status'))
            <div
                class="
                    mb-5
                    rounded-lg
                    border
                    border-[#7d9a76]/35
                    bg-[#edf4e9]
                    px-3
                    py-2.5
                    text-[11px]
                    font-semibold
                    text-[#496043]
                "
            >
                {{ session('status') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('login') }}"
            class="space-y-4"
        >
            @csrf

            <div>
                <label
                    for="email"
                    class="
                        block
                        text-[10px]
                        font-black
                        uppercase
                        tracking-[0.07em]
                        text-[#704326]
                    "
                >
                    Email
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="
                        mt-1.5
                        block
                        h-11
                        w-full
                        rounded-lg
                        border
                        border-[#cdbb9f]
                        bg-[#fffdf8]
                        px-3
                        text-[13px]
                        font-semibold
                        text-[#34231b]
                        caret-[#6b1d14]
                        placeholder:text-[#a88c77]
                        shadow-[inset_0_1px_2px_rgba(70,40,20,.05)]
                        outline-none
                        transition
                        hover:border-[#b48a64]
                        focus:border-[#93623d]
                        focus:ring-2
                        focus:ring-[#93623d]/15
                    "
                >

                @error('email')
                    <p
                        class="
                            mt-1.5
                            text-[10px]
                            font-bold
                            text-[#9a2f24]
                        "
                    >
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="password"
                    class="
                        block
                        text-[10px]
                        font-black
                        uppercase
                        tracking-[0.07em]
                        text-[#704326]
                    "
                >
                    Senha
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="
                        mt-1.5
                        block
                        h-11
                        w-full
                        rounded-lg
                        border
                        border-[#cdbb9f]
                        bg-[#fffdf8]
                        px-3
                        text-[13px]
                        font-semibold
                        text-[#34231b]
                        caret-[#6b1d14]
                        shadow-[inset_0_1px_2px_rgba(70,40,20,.05)]
                        outline-none
                        transition
                        hover:border-[#b48a64]
                        focus:border-[#93623d]
                        focus:ring-2
                        focus:ring-[#93623d]/15
                    "
                >

                @error('password')
                    <p
                        class="
                            mt-1.5
                            text-[10px]
                            font-bold
                            text-[#9a2f24]
                        "
                    >
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div
                class="
                    flex
                    flex-wrap
                    items-center
                    justify-between
                    gap-3
                    pt-0.5
                "
            >
                <label
                    for="remember_me"
                    class="
                        inline-flex
                        cursor-pointer
                        items-center
                        gap-2
                        text-[11px]
                        font-semibold
                        text-[#755847]
                    "
                >
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="
                            h-4
                            w-4
                            rounded
                            border-[#b99a7b]
                            bg-[#fffdf8]
                            text-[#6b1d14]
                            focus:ring-[#93623d]/25
                        "
                    >

                    <span>
                        Lembrar de mim
                    </span>
                </label>

                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="
                            rounded-md
                            text-[11px]
                            font-bold
                            text-[#93623d]
                            underline
                            decoration-[#93623d]/35
                            underline-offset-2
                            outline-none
                            transition
                            hover:text-[#53150f]
                            hover:decoration-[#53150f]/50
                            focus-visible:ring-2
                            focus-visible:ring-[#93623d]/20
                        "
                    >
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <button
                type="submit"
                class="
                    mt-1
                    inline-flex
                    h-11
                    w-full
                    items-center
                    justify-center
                    rounded-lg
                    border
                    border-[#53150f]/35
                    bg-[#6b1d14]
                    px-4
                    text-[10px]
                    font-black
                    uppercase
                    tracking-[0.10em]
                    text-[#fffaf2]
                    shadow-[0_8px_22px_rgba(83,21,15,.18)]
                    transition
                    hover:-translate-y-0.5
                    hover:bg-[#53150f]
                    focus-visible:outline-none
                    focus-visible:ring-2
                    focus-visible:ring-[#93623d]/35
                    focus-visible:ring-offset-2
                    focus-visible:ring-offset-[#fffaf2]
                "
            >
                Entrar
            </button>

            @if (Route::has('register'))
                <p
                    class="
                        pt-1
                        text-center
                        text-[11px]
                        text-[#755847]
                    "
                >
                    Ainda não possui uma conta?

                    <a
                        href="{{ route('register') }}"
                        class="
                            ml-1
                            font-black
                            text-[#6b1d14]
                            underline
                            decoration-[#6b1d14]/30
                            underline-offset-2
                            transition
                            hover:text-[#53150f]
                            hover:decoration-[#53150f]/55
                        "
                    >
                        Criar conta
                    </a>
                </p>
            @endif
        </form>
    </div>
</x-guest-layout>