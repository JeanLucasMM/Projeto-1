@props(['character'])

<section class="overflow-hidden rounded-2xl border border-[#cdbb9f]/60 bg-[#f4f1e8] shadow-sm">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between border-b border-[#cdbb9f]/50 bg-[#efe9dc]/50 px-5 py-4">

        <div>
            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">
                Recursos
            </p>

            <h2 class="mt-1 font-serif text-xl font-black text-[#53150f]">
                Recursos Ativos
            </h2>
        </div>

        <span class="rounded-lg border border-[#cdbb9f]/50 bg-[#f4f1e8] px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
            {{ $character->resources->count() }}
        </span>

    </div>


    {{-- Lista --}}
    <div class="space-y-3 p-5">

        @forelse ($character->resources as $resource)

            @php
                $current = (int) ($resource->current ?? 0);
                $maximum = (int) ($resource->maximum ?? 0);

                $percentage = $maximum > 0
                    ? min(100, max(0, ($current / $maximum) * 100))
                    : 0;
            @endphp

            <div
                class="group rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 p-4 transition-all duration-200 hover:border-[#6b1d14]/30 hover:bg-[#efe9dc]/80"
            >

                {{-- Nome + contador --}}
                <div class="flex items-start justify-between gap-4">

                    <div class="min-w-0">

                        <p class="font-serif text-sm font-black text-[#53150f]">
                            {{ $resource->name }}
                        </p>

                        @if ($resource->type || $resource->source)

                            <p class="mt-0.5 text-[9px] font-black uppercase tracking-widest text-[#8c6239]">

                                {{ $resource->type }}

                                @if ($resource->type && $resource->source)
                                    ·
                                @endif

                                {{ $resource->source }}

                            </p>

                        @endif

                    </div>


                    {{-- Contador --}}
                    <div class="shrink-0 text-right">

                        <span class="font-serif text-lg font-black text-[#53150f]">
                            {{ $current }}
                        </span>

                        <span class="text-xs font-bold text-[#8c6239]">
                            / {{ $maximum }}
                        </span>

                    </div>

                </div>


                {{-- Barra de recurso --}}
                <div class="mt-3">

                    <div class="h-2 overflow-hidden rounded-full bg-[#cdbb9f]/30">

                        <div
                            class="h-full rounded-full bg-[#6b1d14] transition-all duration-300"
                            style="width: {{ $percentage }}%"
                        ></div>

                    </div>

                </div>


                {{-- Recuperação --}}
                @if ($resource->recovery)

                    <div class="mt-3 flex items-center gap-2">

                        <span class="text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                            Recuperação
                        </span>

                        <span class="text-[10px] font-semibold text-[#53150f]">
                            {{ $resource->recovery }}
                        </span>

                    </div>

                @endif


                {{-- Ações --}}
                <div class="mt-3 flex items-center justify-between border-t border-[#cdbb9f]/30 pt-3">

                    <div class="flex items-center gap-1.5">

                        {{-- Gastar --}}
                        <form
                            method="POST"
                            action="{{ route('characters.resources.decrease', [$character, $resource]) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                @disabled($current <= 0)
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] text-sm font-black text-[#6b1d14] transition hover:border-[#6b1d14]/40 hover:bg-[#efe9dc] disabled:cursor-not-allowed disabled:opacity-30"
                                title="Gastar recurso"
                            >
                                −
                            </button>
                        </form>


                        {{-- Recuperar --}}
                        <form
                            method="POST"
                            action="{{ route('characters.resources.increase', [$character, $resource]) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                @disabled($current >= $maximum)
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] text-sm font-black text-[#6b1d14] transition hover:border-[#6b1d14]/40 hover:bg-[#efe9dc] disabled:cursor-not-allowed disabled:opacity-30"
                                title="Recuperar recurso"
                            >
                                +
                            </button>
                        </form>

                    </div>


                    {{-- Configuração --}}
                    <button
                        type="button"
                        x-data
                        @click="$dispatch('open-resource-editor', { id: {{ $resource->id }} })"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8] text-[#8c6239] transition hover:border-[#6b1d14]/40 hover:text-[#6b1d14]"
                        title="Configurar recurso"
                    >
                        <svg
                            class="h-3.5 w-3.5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.4 15a1.7 1.7 0 00.34 1.88l.06.06-1.9 1.9-.06-.06a1.7 1.7 0 00-1.88-.34 1.7 1.7 0 00-1.03 1.55V20h-2.7v-.09a1.7 1.7 0 00-1.03-1.55 1.7 1.7 0 00-1.88.34l-.06.06-1.9-1.9.06-.06A1.7 1.7 0 007.76 15a1.7 1.7 0 00-1.55-1.03H6v-2.7h.21A1.7 1.7 0 007.76 10a1.7 1.7 0 00-.34-1.88l-.06-.06 1.9-1.9.06.06a1.7 1.7 0 001.88.34A1.7 1.7 0 0012.23 5V4h2.7v1a1.7 1.7 0 001.03 1.55 1.7 1.7 0 001.88-.34l.06-.06 1.9 1.9-.06.06A1.7 1.7 0 0019.4 10a1.7 1.7 0 001.55 1.03H21v2.7h-.05A1.7 1.7 0 0019.4 15z"
                            />
                        </svg>
                    </button>

                </div>

            </div>

        @empty

            <div class="rounded-xl border border-dashed border-[#cdbb9f] bg-[#efe9dc]/40 p-8 text-center">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full border border-[#cdbb9f]/60 bg-[#f4f1e8]">

                    <svg
                        class="h-5 w-5 text-[#8c6239]/60"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7m16 0l-8 4m-8-4l8 4m0 0v10"
                        />
                    </svg>

                </div>

                <p class="mt-3 font-serif text-base font-black text-[#53150f]">
                    Nenhum recurso
                </p>

                <p class="mt-1 text-xs text-[#8c6239]">
                    Recursos como cargas, usos por descanso e habilidades
                    podem ser adicionados à ficha.
                </p>

            </div>

        @endforelse

    </div>

</section>