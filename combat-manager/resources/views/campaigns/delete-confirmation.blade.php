<x-app-layout>
    <div
        class="
            mx-auto
            w-full
            max-w-2xl
            px-4
            py-8
            sm:px-6
            lg:px-8
        "
    >
        <a
            href="{{ route('campaigns.show', $campaign) }}"
            class="
                inline-flex
                items-center
                gap-2
                text-xs
                font-black
                uppercase
                tracking-[0.12em]
                text-[#7d604d]
                transition
                hover:text-[#53150f]
            "
        >
            <span aria-hidden="true">←</span>
            Voltar para a campanha
        </a>

        <section
            class="
                mt-6
                overflow-hidden
                rounded-[26px]
                border
                border-red-200
                bg-[#fffdf8]
                shadow-[0_18px_50px_rgba(83,21,15,.08)]
            "
        >
            <header
                class="
                    border-b
                    border-red-200
                    bg-red-50
                    px-6
                    py-5
                "
            >
                <p
                    class="
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.18em]
                        text-red-700
                    "
                >
                    Zona de perigo
                </p>

                <h1
                    class="
                        mt-1
                        font-serif
                        text-2xl
                        font-black
                        text-[#53150f]
                    "
                >
                    Excluir {{ $campaign->name }}
                </h1>
            </header>

            <div class="p-6">
                <div
                    class="
                        rounded-2xl
                        border
                        border-[#cdbb9f]/60
                        bg-[#f7f0e6]
                        p-4
                        text-sm
                        leading-6
                        text-[#654938]
                    "
                >
                    <p class="font-bold text-[#53150f]">
                        Esta ação agora é protegida por uma lixeira.
                    </p>

                    <p class="mt-1">
                        A campanha deixará de aparecer normalmente, mas os dados vinculados serão preservados no banco.
                    </p>

                    <p class="mt-3">
                        Para continuar, confirme conscientemente digitando a frase abaixo exatamente como aparece.
                    </p>
                </div>

                <div
                    class="
                        mt-5
                        rounded-xl
                        border
                        border-dashed
                        border-red-300
                        bg-red-50/70
                        px-4
                        py-3
                        font-mono
                        text-sm
                        font-black
                        text-red-800
                    "
                >
                    EXCLUIR {{ $campaign->name }}
                </div>

                <form
                    method="POST"
                    action="{{ route('campaigns.destroy', $campaign) }}"
                    class="mt-5"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="delete_intent"
                        value="archive_campaign"
                    >

                    <label class="block">
                        <span
                            class="
                                mb-1.5
                                block
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            Confirmação
                        </span>

                        <input
                            type="text"
                            name="confirmation_text"
                            value="{{ old('confirmation_text') }}"
                            autocomplete="off"
                            spellcheck="false"
                            required
                            class="
                                w-full
                                rounded-xl
                                border
                                border-[#cdbb9f]
                                bg-[#fffdf8]
                                px-3
                                py-3
                                text-sm
                                text-[#432c21]
                                focus:border-red-500
                                focus:ring-red-500/15
                            "
                        >

                        @error('confirmation_text')
                            <p
                                class="
                                    mt-2
                                    text-xs
                                    font-bold
                                    text-red-700
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </label>

                    <label
                        class="
                            mt-4
                            flex
                            items-start
                            gap-3
                            rounded-xl
                            border
                            border-[#cdbb9f]/60
                            bg-[#fbf8f1]
                            p-4
                        "
                    >
                        <input
                            type="checkbox"
                            name="understand"
                            value="1"
                            required
                            class="
                                mt-0.5
                                rounded
                                border-[#b08c62]
                                text-red-700
                                focus:ring-red-500/20
                            "
                        >

                        <span
                            class="
                                text-xs
                                leading-5
                                text-[#654938]
                            "
                        >
                            Eu entendo que a campanha será removida da visualização normal e enviada para a lixeira.
                        </span>
                    </label>

                    @error('understand')
                        <p
                            class="
                                mt-2
                                text-xs
                                font-bold
                                text-red-700
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                    <div
                        class="
                            mt-6
                            flex
                            flex-col-reverse
                            gap-3
                            sm:flex-row
                            sm:justify-end
                        "
                    >
                        <a
                            href="{{ route('campaigns.show', $campaign) }}"
                            class="
                                inline-flex
                                items-center
                                justify-center
                                rounded-xl
                                border
                                border-[#cdbb9f]
                                bg-[#fffdf8]
                                px-5
                                py-3
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-[#654938]
                                transition
                                hover:bg-[#f7f0e6]
                            "
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="
                                inline-flex
                                items-center
                                justify-center
                                rounded-xl
                                bg-red-700
                                px-5
                                py-3
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-white
                                transition
                                hover:bg-red-800
                            "
                        >
                            Mover campanha para a lixeira
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-app-layout>