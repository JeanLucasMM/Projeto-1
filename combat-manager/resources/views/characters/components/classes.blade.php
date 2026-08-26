@props(['character'])

@php
    $classes = $character->classes ?? collect();

    $totalClassLevels = (int) $classes->sum('level');

    /*
    |--------------------------------------------------------------------------
    | Nível exibido
    |--------------------------------------------------------------------------
    |
    | O nível armazenado em characters continua sendo o nível oficial.
    | Entretanto, a soma das classes serve como referência para conferir
    | se a progressão está coerente.
    |
    */

    $levelMismatch = $totalClassLevels !== (int) $character->level;
@endphp


<section
    x-data="{
        configuring: false,

        toggleConfiguration() {
            this.configuring = !this.configuring;
        }
    }"
    class="overflow-hidden rounded-2xl border border-[#cdbb9f]/60 bg-[#f4f1e8] shadow-sm"
>

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between border-b border-[#cdbb9f]/50 bg-[#efe9dc]/60 px-5 py-3.5">

        <div>

            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">
                Progressão
            </p>

            <h2 class="mt-0.5 font-serif text-xl font-black text-[#53150f]">
                Classes & Subclasses
            </h2>

        </div>


        <div class="flex items-center gap-2">

            {{-- Nível total --}}
            <div class="flex items-center gap-1.5 rounded-lg border border-[#cdbb9f]/50 bg-[#f4f1e8] px-2.5 py-1">

                <span class="text-[9px] font-black uppercase tracking-wider text-[#8c6239]">
                    Nível
                </span>

                <span class="font-serif text-sm font-black text-[#53150f]">
                    {{ $character->level }}
                </span>

            </div>


            {{-- Configuração --}}
            <button
                type="button"
                @click="toggleConfiguration()"
                class="inline-flex items-center gap-2 rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#8c6239] transition hover:border-[#6b1d14]/40 hover:text-[#6b1d14]"
            >

                <svg
                    class="h-3.5 w-3.5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M11.49 3.17c.24-.9 1.52-.9 1.76 0l.12.45a1.8 1.8 0 002.2 1.27l.45-.12c.9-.24 1.54.8.89 1.46l-.33.33a1.8 1.8 0 001.27 3.07h.47c.93 0 .93 1.32 0 1.32h-.47a1.8 1.8 0 00-1.27 3.07l.33.33c.65.65.01 1.7-.89 1.46l-.45-.12a1.8 1.8 0 00-2.2 1.27l-.12.45c-.24.9-1.52.9-1.76 0l-.12-.45a1.8 1.8 0 00-2.2-1.27l-.45.12c-.9.24-1.54-.8-.89-1.46l.33-.33a1.8 1.8 0 001.27-3.07h-.47c-.93 0-.93-1.32 0-1.32h.47a1.8 1.8 0 001.27-3.07l-.33-.33c-.65-.65-.01-1.7.89-1.46l.45.12a1.8 1.8 0 002.2-1.27l.12-.45zM12.37 8a2 2 0 100 4 2 2 0 000-4z"
                        clip-rule="evenodd"
                    />
                </svg>

                Configurar

            </button>

        </div>

    </div>


    {{-- Informação de progressão --}}
    <div class="border-b border-[#cdbb9f]/40 bg-[#efe9dc]/30 px-5 py-3">

        <div class="flex flex-wrap items-center justify-between gap-2">

            <div>

                <span class="text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                    Níveis de classe
                </span>

                <p class="mt-0.5 text-xs text-[#53150f]">
                    {{ $totalClassLevels }}
                    {{ $totalClassLevels === 1 ? 'nível' : 'níveis' }}
                    distribuídos entre
                    {{ $classes->count() }}
                    {{ $classes->count() === 1 ? 'classe' : 'classes' }}
                </p>

            </div>


            @if ($levelMismatch)

                <span class="rounded-lg border border-[#6b1d14]/20 bg-[#6b1d14]/5 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-[#6b1d14]">
                    Nível total divergente
                </span>

            @endif

        </div>

    </div>


    {{-- Configuração --}}
    <div
        x-show="configuring"
        x-cloak
        x-transition
        class="border-b border-[#cdbb9f]/50 bg-[#efe9dc]/40 px-5 py-4"
    >

        <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#f4f1e8] p-4">

            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#8c6239]">
                Progressão de personagem
            </p>

            <p class="mt-1 text-xs leading-relaxed text-[#8c6239]">
                Cada classe possui seu próprio nível. Você pode distribuir
                livremente os níveis entre as classes para representar
                personagens multiclasses.
            </p>

        </div>

    </div>


    {{-- Lista de classes --}}
    <div class="space-y-2.5 p-4">

        @forelse ($classes as $class)

            <div
                class="group rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/40 transition-all duration-200 hover:border-[#6b1d14]/30 hover:bg-[#efe9dc]/80"
            >

                {{-- Visualização --}}
                <div class="flex items-center justify-between px-4 py-3">

                    {{-- Classe --}}
                    <div class="min-w-0 flex-1 pr-3">

                        <div class="flex items-center gap-2">

                            <p class="truncate font-serif text-base font-black text-[#53150f]">
                                {{ $class->class }}
                            </p>

                            @if ($class->level > 0)

                                <span class="shrink-0 rounded-md bg-[#f4f1e8] px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-[#8c6239]">
                                    Nível {{ $class->level }}
                                </span>

                            @endif

                        </div>


                        @if ($class->subclass)

                            <p class="mt-0.5 truncate text-xs font-medium text-[#8c6239]">

                                <span class="text-[9px] font-black uppercase tracking-wider text-[#8c6239]/70">
                                    Subclasse:
                                </span>

                                {{ $class->subclass }}

                            </p>

                        @else

                            <p class="mt-0.5 text-xs italic text-[#8c6239]/50">
                                Sem subclasse selecionada
                            </p>

                        @endif

                    </div>


                    {{-- Nível --}}
                    <div class="flex shrink-0 items-center justify-center rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] px-3 py-1.5 shadow-sm">

                        <span class="text-[10px] font-black uppercase tracking-wider text-[#6b1d14]">
                            Nvl {{ $class->level }}
                        </span>

                    </div>

                </div>


                {{-- Editor individual --}}
                <div
                    x-show="configuring"
                    x-cloak
                    x-transition
                    class="border-t border-[#cdbb9f]/40 px-4 py-4"
                >

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                        {{-- Classe --}}
                        <div>

                            <label
                                for="class_name_{{ $class->id }}"
                                class="mb-1 block text-[8px] font-black uppercase tracking-widest text-[#8c6239]"
                            >
                                Classe
                            </label>

                            <input
                                id="class_name_{{ $class->id }}"
                                type="text"
                                value="{{ $class->class }}"
                                class="w-full rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] px-3 py-2 text-xs font-black text-[#53150f] outline-none transition focus:border-[#6b1d14] focus:ring-1 focus:ring-[#6b1d14]/20"
                            >

                        </div>


                        {{-- Subclasse --}}
                        <div>

                            <label
                                for="subclass_{{ $class->id }}"
                                class="mb-1 block text-[8px] font-black uppercase tracking-widest text-[#8c6239]"
                            >
                                Subclasse
                            </label>

                            <input
                                id="subclass_{{ $class->id }}"
                                type="text"
                                value="{{ $class->subclass }}"
                                placeholder="Opcional"
                                class="w-full rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] px-3 py-2 text-xs font-black text-[#53150f] outline-none transition focus:border-[#6b1d14] focus:ring-1 focus:ring-[#6b1d14]/20"
                            >

                        </div>


                        {{-- Nível --}}
                        <div>

                            <label
                                for="class_level_{{ $class->id }}"
                                class="mb-1 block text-[8px] font-black uppercase tracking-widest text-[#8c6239]"
                            >
                                Nível
                            </label>

                            <input
                                id="class_level_{{ $class->id }}"
                                type="number"
                                value="{{ $class->level }}"
                                min="0"
                                class="w-full rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] px-3 py-2 text-center text-xs font-black text-[#53150f] outline-none transition focus:border-[#6b1d14] focus:ring-1 focus:ring-[#6b1d14]/20"
                            >

                        </div>

                    </div>


                    <div class="mt-3 flex items-center justify-between gap-2">

                        <button
                            type="button"
                            class="rounded-lg border border-[#6b1d14]/20 px-3 py-2 text-[8px] font-black uppercase tracking-widest text-[#6b1d14] transition hover:bg-[#6b1d14]/5"
                        >
                            Remover Classe
                        </button>


                        <button
                            type="button"
                            class="rounded-lg bg-[#6b1d14] px-4 py-2 text-[8px] font-black uppercase tracking-widest text-[#f4f1e8] transition hover:bg-[#53150f]"
                        >
                            Salvar Alterações
                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="rounded-xl border border-dashed border-[#cdbb9f] bg-[#efe9dc]/30 p-5 text-center">

                <p class="text-xs font-medium text-[#8c6239]">
                    Nenhuma classe associada a este personagem.
                </p>

            </div>

        @endforelse


        {{-- Adicionar classe --}}
        <div
            x-show="configuring"
            x-cloak
            x-transition
            class="pt-1"
        >

            <button
                type="button"
                class="flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-[#cdbb9f] bg-[#efe9dc]/20 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-[#8c6239] transition hover:border-[#6b1d14]/40 hover:bg-[#efe9dc]/60 hover:text-[#6b1d14]"
            >

                <span class="text-base leading-none">
                    +
                </span>

                Adicionar Multiclasse

            </button>

        </div>

    </div>

</section>