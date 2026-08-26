@props(['character', 'maxHp'])

<div class="flex min-w-0 translate- flex-1 flex-col gap-1.5">

    {{-- CABEÇALHO --}}

    <div class="flex items-center gap-2 px-0.5">

        {{-- TÍTULO + EDIÇÃO DE VIDA --}}
        <button
            type="button"
            @click.stop="openHpSettings()"
            class="group inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest text-[#8c6239] transition-colors hover:text-[#6b1d14] focus:outline-none"
            title="Configurar Pontos de Vida"
        >
            <span>Pontos de Vida</span>


        </button>

        {{-- VIDA MÁXIMA EXTRA --}}
        <template x-if="temporaryMaxHp > 0">
            <span class="rounded border border-[#d4b36b]/40 bg-[#f4ead0] px-1.5 py-0.5 text-[8px] font-black uppercase tracking-wide text-[#9a6f16]">
                +<span x-text="temporaryMaxHp"></span>
            </span>
        </template>

    </div>

    {{-- BARRA DE VIDA --}}

    <div

        :class="{

            'animate-damage-red': shakingRed,

            'animate-damage-red': shakingBlue,

            'animate-heal-green': flashingGreen,

            'ring-1 ring-sky-400/40': temporaryHp > 0

        }"

        class="relative flex h-8 w-full items-center justify-between overflow-hidden rounded-md border border-black/40 bg-[#1f1b1b] shadow-inner"

    >

        {{-- GHOST --}}

        <div

            class="absolute inset-y-0 left-0 bg-red-950/80 transition-all duration-1000 ease-out"

            :style="`width:${ghostHpPercent}%`"

        ></div>

        {{-- VIDA NORMAL --}}

        <div

            class="absolute inset-y-0 left-0 z-[5] transition-all duration-700 ease-out"

            :class="

                hpPercent > 50

                    ? 'bg-emerald-700'

                    : (

                        hpPercent > 25

                            ? 'bg-amber-600'

                            : 'bg-red-700'

                    )

            "

            :style="`width:${normalHpPercent}%`"

        ></div>

        {{-- VIDA EXTRA --}}

        <template x-if="bonusHpPercent > 0">

            <div

                class="bonus-hp-active absolute inset-y-0 z-[8] overflow-hidden border-l border-[#f4d58a]/70 bg-gradient-to-r from-[#9a6f16] via-[#d9a441] to-[#efd27a] transition-all duration-700 ease-out"

                :style="`left:${normalHpPercent}%;width:${bonusHpPercent}%`"

            >

                <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/15 via-transparent to-black/10"></div>

            </div>

        </template>

        {{-- VIDA TEMPORÁRIA --}}

        <template x-if="temporaryHp > 0">

            <div

                class="pointer-events-none absolute inset-y-0 left-0 z-[15] overflow-hidden transition-all duration-500 ease-out"

                :style="`width:${tempHpPercent}%`"

            >

                <div class="temp-hp-active absolute inset-0 border-y border-sky-200/50 bg-gradient-to-b from-sky-300/25 via-sky-400/20 to-sky-600/30"></div>

                <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/15 to-transparent"></div>

                <div class="absolute inset-0 opacity-20 bg-[repeating-linear-gradient(-45deg,transparent,transparent_4px,rgba(186,230,253,.45)_4px,rgba(186,230,253,.45)_5px)]"></div>

            </div>

        </template>

        {{-- DANO --}}

        <div class="relative z-30 flex h-full items-center px-1">

            <button

                type="button"

                title="-5"

                @click="changeHp(-5)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                «

            </button>

            <button

                type="button"

                title="-1"

                @click="changeHp(-1)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                ‹

            </button>

        </div>

        {{-- VALORES --}}

        <div class="relative z-30 flex min-w-0 flex-1 items-center justify-center gap-2">

            <span

                x-show="!editingHp"

                @click="

                    directHp = currentHp;

                    editingHp = true;

                    $nextTick(() => {

                        $refs.hpInput.focus();

                        $refs.hpInput.select();

                    });

                "

                class="cursor-pointer select-none text-xs font-bold tracking-wider text-white drop-shadow-[0_1px_2px_rgba(0,0,0,.8)]"

            >

                <span x-text="currentHp"></span>

                <span class="mx-0.5 text-white/50">/</span>

                <span x-text="effectiveMaxHp"></span>

            </span>

            <input

                x-ref="hpInput"

                x-show="editingHp"

                x-cloak

                type="number"

                min="0"

                :max="effectiveMaxHp"

                x-model.number="directHp"

                @keydown.enter="$el.blur()"

                @keydown.escape="

                    editingHp = false;

                    directHp = currentHp;

                "

                @blur="handleDirectHp()"

                class="h-6 w-16 rounded bg-black/80 text-center text-xs font-bold text-white focus:outline-none focus:ring-1 focus:ring-amber-500"

            >

            {{-- VIDA TEMPORÁRIA --}}
            <template x-if="!editingTempHp">
                <button
                    type="button"
                    @click="
                        directTempHp = temporaryHp;
                        editingTempHp = true;

                        $nextTick(() => {
                            $refs.tempHp.focus();
                            $refs.tempHp.select();
                        });
                    "
                    :class="
                        temporaryHp > 0
                            ? 'border-sky-300/70 bg-sky-100/90 text-sky-800 hover:bg-sky-100'
                            : 'border-white/15 bg-black/20 text-white/55 hover:bg-black/30 hover:text-white/75'
                    "
                    class="inline-flex items-center gap-1 rounded-md border px-1.5 py-0.5 text-[10px] font-bold transition-colors"
                    title="Vida Temporária"
                >
                    <svg
                        class="h-3 w-3 shrink-0"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z" />
                    </svg>

                    <span x-text="temporaryHp"></span>
                </button>
            </template>

            <input

                x-ref="tempHp"

                x-show="editingTempHp"

                x-cloak

                type="number"

                min="0"

                x-model.number="directTempHp"

                @keydown.enter="$el.blur()"

                @keydown.escape="

                    editingTempHp = false;

                    directTempHp = temporaryHp;

                "

                @blur="handleTemporaryHp()"

                class="h-6 w-14 rounded border border-sky-400/50 bg-sky-900 text-center text-xs font-bold text-white focus:outline-none focus:ring-1 focus:ring-sky-300"

            >

        </div>

        {{-- CURA --}}

        <div class="relative z-30 flex h-full items-center px-1">

            <button

                type="button"

                title="+1"

                @click="changeHp(1)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                ›

            </button>

            <button

                type="button"

                title="+5"

                @click="changeHp(5)"

                class="h-full px-2 font-bold text-white/70 transition hover:bg-white/10 hover:text-white"

            >

                »

            </button>

        </div>

    </div>

    {{-- MODAL DE CONFIGURAÇÃO --}}

    <div

        x-show="hpSettingsOpen"

        x-cloak

        @keydown.escape.window="closeHpSettings()"

        class="fixed inset-0 z-[200] flex items-center justify-center p-4"

    >

        {{-- BACKDROP --}}

        <div

            class="absolute inset-0 bg-[#2b1d17]/60 backdrop-blur-sm"

            @click="closeHpSettings()"

        ></div>

        {{-- PAINEL --}}

        <div

            x-show="hpSettingsOpen"

            x-transition:enter="transition duration-200 ease-out"

            x-transition:enter-start="opacity-0 scale-95 translate-y-2"

            x-transition:enter-end="opacity-100 scale-100 translate-y-0"

            x-transition:leave="transition duration-150 ease-in"

            x-transition:leave-start="opacity-100 scale-100 translate-y-0"

            x-transition:leave-end="opacity-0 scale-95 translate-y-2"

            @click.stop

            class="relative z-10 w-full max-w-md overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#f4f1e8] shadow-2xl"

        >

            {{-- CABEÇALHO --}}

            <div class="flex items-center justify-between border-b border-[#cdbb9f]/60 bg-[#efe9dc]/70 px-4 py-3">

                <div>

                    <p class="text-[8px] font-black uppercase tracking-[0.25em] text-[#8c6239]">

                        Configuração de Combate

                    </p>

                    <h2 class="mt-1 font-serif text-lg font-black text-[#53150f]">

                        Valores de Vida

                    </h2>

                </div>

                <button

                    type="button"

                    @click="closeHpSettings()"

                    class="flex h-7 w-7 items-center justify-center rounded-lg text-[#8c6239] transition hover:bg-[#6b1d14]/10 hover:text-[#6b1d14]"

                >

                    ×

                </button>

            </div>

            {{-- CONFIRMAÇÃO --}}

            <template x-if="hpSettingsStep === 'confirm'">

                <div class="p-4">

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">

                        <p class="text-sm font-black text-amber-900">

                            Alteração permanente

                        </p>

                        <p class="mt-1 text-xs leading-4 text-amber-800/80">

                            A Vida Máxima pertence à ficha do personagem.

                            A Vida Máxima Extra aumenta temporariamente o limite

                            e concede os PV correspondentes.

                        </p>

                    </div>

                    <div class="mt-3 rounded-xl border border-[#cdbb9f]/50 bg-white/60 p-3">

                        <div class="flex items-center justify-between">

                            <span class="text-[8px] font-black uppercase tracking-widest text-[#8c6239]">

                                Vida Atual

                            </span>

                            <span class="font-serif text-lg font-black text-[#53150f]">

                                <span x-text="currentHp"></span>/<span x-text="effectiveMaxHp"></span>

                            </span>

                        </div>

                        <div class="mt-2 grid grid-cols-2 gap-2">

                            <div class="rounded-lg bg-[#efe9dc] p-2 text-center">

                                <span class="block text-[7px] font-black uppercase tracking-widest text-[#8c6239]">

                                    Máxima

                                </span>

                                <span

                                    x-text="maxHp"

                                    class="mt-0.5 block font-serif text-base font-black text-[#53150f]"

                                ></span>

                            </div>

                            <div

                                x-show="temporaryMaxHp > 0"

                                class="rounded-lg bg-[#f4ead0] p-2 text-center"

                            >

                                <span class="block text-[7px] font-black uppercase tracking-widest text-[#9a6f16]">

                                    Extra

                                </span>

                                <span class="mt-0.5 block font-serif text-base font-black text-[#9a6f16]">

                                    +<span x-text="temporaryMaxHp"></span>

                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="mt-4 flex justify-end gap-2">

                        <button

                            type="button"

                            @click="closeHpSettings()"

                            class="rounded-lg px-3 py-2 text-[8px] font-black uppercase tracking-widest text-[#8c6239] hover:bg-[#efe9dc]"

                        >

                            Cancelar

                        </button>

                        <button

                            type="button"

                            @click="beginHpSettings()"

                            class="rounded-lg bg-[#6b1d14] px-4 py-2 text-[8px] font-black uppercase tracking-widest text-[#f4f1e8] hover:bg-[#53150f]"

                        >

                            Continuar

                        </button>

                    </div>

                </div>

            </template>

            {{-- EDIÇÃO --}}

            <template x-if="hpSettingsStep === 'edit'">

                <div class="p-4">

                    <label class="block">

                        <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">

                            Vida Máxima Permanente

                        </span>

                        <span class="mt-1 block text-[9px] leading-4 text-[#8c6239]/80">

                            Limite normal de Pontos de Vida.

                        </span>

                        <input

                            x-ref="maxHpSettingsInput"

                            type="number"

                            min="1"

                            x-model.number="directMaxHp"

                            class="mt-2 w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2.5 font-serif text-lg font-black text-[#53150f] outline-none focus:border-[#6b1d14] focus:ring-1 focus:ring-[#6b1d14]/10"

                        >

                    </label>

                    <label class="mt-3 block rounded-xl border border-[#d4b36b]/50 bg-[#f8efd9]/70 p-3">

                        <span class="block text-[8px] font-black uppercase tracking-widest text-[#9a6f16]">

                            Vida Máxima Extra

                        </span>

                        <span class="mt-1 block text-[9px] leading-4 text-[#9a6f16]/80">

                            Aumenta o limite e cura a mesma quantidade adicionada.

                        </span>

                        <input

                            type="number"

                            min="0"

                            x-model.number="directTemporaryMaxHp"

                            class="mt-2 w-full rounded-lg border border-[#d4b36b]/60 bg-white px-3 py-2.5 font-serif text-lg font-black text-[#9a6f16] outline-none focus:border-[#b88920] focus:ring-1 focus:ring-[#d4b36b]/20"

                        >

                    </label>

                    {{-- PREVIEW --}}

                    <div class="mt-3 rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40 p-3">

                        <div class="flex items-center justify-between">

                            <span class="text-[7px] font-black uppercase tracking-widest text-[#8c6239]">

                                Novo limite

                            </span>

                            <span class="font-serif text-base font-black text-[#53150f]">

                                <span x-text="Math.max(1, parseInt(directMaxHp) || maxHp)"></span>

                                <span

                                    x-show="(parseInt(directTemporaryMaxHp) || 0) > 0"

                                    class="text-[#9a6f16]"

                                >

                                    +

                                    <span x-text="Math.max(0, parseInt(directTemporaryMaxHp) || 0)"></span>

                                </span>

                            </span>

                        </div>

                        <div class="relative mt-2 h-3 overflow-hidden rounded-full bg-[#d8c7ab]">

                            <div

                                class="absolute inset-y-0 left-0 bg-emerald-600"

                                :style="`

                                    width:${

                                        (

                                            Math.max(

                                                1,

                                                parseInt(directMaxHp) || maxHp

                                            ) /

                                            (

                                                Math.max(

                                                    1,

                                                    parseInt(directMaxHp) || maxHp

                                                ) +

                                                Math.max(

                                                    0,

                                                    parseInt(directTemporaryMaxHp) || 0

                                                )

                                            )

                                        ) * 100

                                    }%;

                                `"

                            ></div>

                            <div

                                class="absolute inset-y-0 right-0 bg-[#d9a441]"

                                :style="`

                                    width:${

                                        (

                                            Math.max(

                                                0,

                                                parseInt(directTemporaryMaxHp) || 0

                                            ) /

                                            (

                                                Math.max(

                                                    1,

                                                    parseInt(directMaxHp) || maxHp

                                                ) +

                                                Math.max(

                                                    0,

                                                    parseInt(directTemporaryMaxHp) || 0

                                                )

                                            )

                                        ) * 100

                                    }%;

                                `"

                            ></div>

                        </div>

                        <div

                            x-show="Math.max(0, (parseInt(directTemporaryMaxHp) || 0) - temporaryMaxHp) > 0"

                            class="mt-2 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-2"

                        >

                            <p class="text-[8px] font-black uppercase tracking-widest text-emerald-800">

                                Cura automática

                            </p>

                            <p class="mt-0.5 text-[10px] text-emerald-800/80">

                                +

                                <span x-text="Math.max(0, (parseInt(directTemporaryMaxHp) || 0) - temporaryMaxHp)"></span>

                                PV

                            </p>

                        </div>

                    </div>

                    {{-- AÇÕES --}}

                    <div class="mt-4 flex justify-end gap-2 border-t border-[#cdbb9f]/40 pt-3">

                        <button

                            type="button"

                            @click="closeHpSettings()"

                            class="rounded-lg px-3 py-2 text-[8px] font-black uppercase tracking-widest text-[#8c6239] transition hover:bg-[#efe9dc]"

                        >

                            Cancelar

                        </button>

                        <button

                            type="button"

                            @click="saveHpSettings()"

                            class="rounded-lg bg-[#6b1d14] px-4 py-2 text-[8px] font-black uppercase tracking-widest text-[#f4f1e8] hover:bg-[#53150f]"

                        >

                            Salvar

                        </button>

                    </div>

                </div>

            </template>

        </div>

    </div>

</div>