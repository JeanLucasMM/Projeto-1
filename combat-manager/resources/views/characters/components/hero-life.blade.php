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

</div>