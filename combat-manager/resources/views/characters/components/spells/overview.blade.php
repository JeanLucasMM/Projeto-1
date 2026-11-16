{{-- ================================================================

     GRIMÓRIO — VISÃO PRINCIPAL V12

     Cabeçalho compacto, slots destacados e gavetas focadas nos feitiços.

================================================================= --}}

<div class="min-h-full bg-[var(--spellbook-paper)] text-[var(--spellbook-ink)]">

    <div class="space-y-2.5 px-4 pb-4 pt-0 sm:px-5 sm:pb-5 sm:pt-0">

        {{-- =============================================================

             CABEÇALHO UNIFICADO DO GRIMÓRIO

             Busca + ações + personagem + CD/ATQ + contadores + slots

        ============================================================== --}}

        <section

            class="

                sticky top-0 z-30

                overflow-hidden

                rounded-b-[24px]

                border-x border-b border-[#cbb18f]/65

                bg-[#fffaf3]/97

                shadow-[0_10px_28px_rgba(91,61,41,.09)]

                backdrop-blur

            "

        >

            {{-- LINHA 1 — GRIMÓRIO / BUSCA / AÇÕES --}}

            <div

                class="

                    flex flex-col gap-3

                    border-b border-[#dfccb5]/75

                    px-4 py-1.5

                    xl:flex-row xl:items-center

                "

            >

                <div

                    class="

                        flex shrink-0 items-center gap-2.5

                        xl:min-w-[185px]

                    "

                >

                    <span

                        class="

                            flex h-10 w-10

                            items-center justify-center

                            rounded-[14px]

                            border border-[#d6bea0]/65

                            bg-[#f4e7d3]

                            shadow-[0_3px_10px_rgba(91,61,41,.05)]

                        "

                    >

                        <img

                            src="{{ asset('images/book.png') }}"

                            alt=""

                            class="

                                h-8 w-8

                                object-contain

                            "

                        >

                    </span>

                    <h3

                        class="

                            truncate

                            font-serif

                            text-[18px]

                            font-black

                            leading-tight

                            text-[#4c1812]

                        "

                    >

                        Meu Grimório

                    </h3>

                </div>

                <label class="relative min-w-0 flex-1">

                    <svg

                        class="

                            pointer-events-none

                            absolute left-3.5 top-1/2

                            h-3.5 w-3.5

                            -translate-y-1/2

                            text-[#9a7b61]

                        "

                        viewBox="0 0 24 24"

                        fill="none"

                        stroke="currentColor"

                        stroke-width="1.8"

                    >

                        <circle cx="11" cy="11" r="7"></circle>

                        <path d="m20 20-3.5-3.5"></path>

                    </svg>

                    <input

                        type="search"

                        x-model="spellSearch"

                        placeholder="Buscar no meu Grimório..."

                        class="

                            w-full

                            rounded-2xl

                            border-[#c4a98a]

                            bg-white

                            h-[34px] py-0 pl-10 pr-3

                            text-[11px]

                            font-medium

                            text-[#35231b]

                            placeholder:text-[#9d8673]

                            shadow-[0_2px_10px_rgba(91,61,41,.035)]

                            focus:border-[#6b1d14]

                            focus:ring-[#6b1d14]

                        "

                    >

                </label>

                <div

                    class="

                        flex shrink-0

                        flex-wrap items-center gap-2

                    "

                >

                    <button

                        type="button"

                        @click="preparedOnly = !preparedOnly"

                        class="

                            inline-flex h-[34px] items-center gap-2

                            rounded-xl border px-3.5

                            text-[8px] font-black uppercase

                            tracking-[0.08em] transition

                        "

                        :class="

                            preparedOnly

                                ? 'border-[#6b1d14]/30 bg-[var(--spellbook-button-bg)] text-white shadow-[0_6px_14px_rgba(107,29,20,.14)]'

                                : 'border-[#b99d7c]/50 bg-white text-[var(--spellbook-accent)] hover:bg-[#f7ede0]'

                        "

                    >

                        <svg

                            class="h-3.5 w-3.5"

                            viewBox="0 0 24 24"

                            fill="none"

                            stroke="currentColor"

                            stroke-width="1.8"

                        >

                            <path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z"></path>

                        </svg>

                        Preparados

                    </button>

                    <button

                        type="button"

                        @click="openLoadouts()"

                        class="

                            inline-flex h-[34px] items-center gap-2

                            rounded-xl

                            border border-[#b99d7c]/50

                            bg-white px-3.5

                            text-[8px] font-black uppercase

                            tracking-[0.08em]

                            text-[var(--spellbook-accent)]

                            transition hover:bg-[#f7ede0]

                        "

                    >

                        <svg

                            class="h-3.5 w-3.5"

                            viewBox="0 0 24 24"

                            fill="none"

                            stroke="currentColor"

                            stroke-width="1.8"

                        >

                            <path d="M7 7h13"></path>

                            <path d="M7 12h13"></path>

                            <path d="M7 17h13"></path>

                            <circle cx="4" cy="7" r="1"></circle>

                            <circle cx="4" cy="12" r="1"></circle>

                            <circle cx="4" cy="17" r="1"></circle>

                        </svg>

                        Loadouts

                    </button>

                    <button

                        type="button"

                        @click="openSettings()"

                        class="

                            flex h-[34px] w-[34px]

                            items-center justify-center

                            rounded-xl

                            border border-[#b99d7c]/50

                            bg-white

                            text-[var(--spellbook-accent)]

                            transition hover:bg-[#f7ede0]

                        "

                        title="Configurar"

                        aria-label="Configurar"

                    >

                        <svg

                            class="h-4 w-4"

                            viewBox="0 0 24 24"

                            fill="none"

                            stroke="currentColor"

                            stroke-width="1.8"

                        >

                            <circle cx="12" cy="12" r="3"></circle>

                            <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21h-4v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H3v-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.3 7 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6V3h4v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1v4H21a1.7 1.7 0 0 0-1.6 1Z"></path>

                        </svg>

                    </button>

                    <button

                        type="button"

                        @click="openCatalog()"

                        class="

                            inline-flex h-[34px] items-center gap-2

                            rounded-xl

                            bg-[var(--spellbook-button-bg)]

                            px-4

                            text-[8px] font-black uppercase

                            tracking-[0.08em]

                            text-white

                            shadow-[0_7px_16px_rgba(107,29,20,.16)]

                            transition hover:bg-[#7a281f]

                        "

                    >

                        <span class="text-[13px] leading-none">+</span>

                        Feitiço

                    </button>

                </div>

            </div>

            {{-- LINHA 2 — PERSONAGEM / CD + ATAQUE / CONTADORES --}}

            <div

                class="

                    grid gap-3

                    px-4 py-2

                    xl:grid-cols-[minmax(265px,1fr)_auto_minmax(335px,1fr)]

                    xl:items-center

                "

            >

                {{-- IDENTIDADE --}}

                <div

                    class="

                        flex min-w-0 items-center gap-3

                    "

                >

                    <span

                        class="

                            flex h-11 min-w-11 shrink-0

                            items-center justify-center

                            rounded-[14px]

                            bg-[var(--spellbook-button-bg)]

                            px-2

                            font-serif

                            text-[13px]

                            font-black

                            text-white

                            shadow-[0_7px_16px_rgba(107,29,20,.18)]

                        "

                        x-text="`Nv. ${characterContext.level}`"

                    ></span>

                    <div class="min-w-0 flex-1">

                        <strong

                            class="

                                block truncate

                                font-serif

                                text-[19px]

                                font-black

                                leading-tight

                                text-[var(--spellbook-title)]

                            "

                        >

                            {{ $character->name }}

                        </strong>

                        <p

                            class="

                                mt-0.5 truncate

                                text-[9px]

                                font-semibold

                                text-[var(--spellbook-muted)]

                            "

                            x-text="classSummary()"

                        ></p>

                        <p

                            x-show="primaryProfile()"

                            class="

                                mt-1 truncate

                                text-[7px]

                                font-black uppercase

                                tracking-[0.11em]

                                text-[var(--spellbook-accent)]

                            "

                            x-text="primaryProfile()?.class"

                        ></p>

                    </div>

                </div>

                {{-- CD / ATAQUE — FOCO CENTRAL --}}

                <template x-if="primaryProfile()">

                    <div

                        class="

                            relative mx-auto

                            grid grid-cols-2

                            overflow-hidden

                            rounded-[20px]

                            border border-[var(--spellbook-border-strong)]

                            bg-[var(--spellbook-paper-strong)]

                            shadow-[0_12px_26px_rgba(83,45,30,.14)]

                        "

                    >

                        <span

                            class="

                                pointer-events-none

                                absolute inset-x-5 top-0

                                h-px

                            "

                            style="
                                background:
                                    linear-gradient(
                                        90deg,
                                        transparent,
                                        var(--spellbook-divider-strong),
                                        transparent
                                    );
                            "

                        ></span>

                        <div

                            class="

                                min-w-[116px]

                                px-5 py-2.5

                                text-center

                            "

                        >

                            <span

                                class="

                                    block

                                    text-[7px]

                                    font-black uppercase

                                    tracking-[0.14em]

                                    text-[var(--spellbook-label)]

                                "

                            >

                                CD

                            </span>

                            <strong

                                class="

                                    mt-0.5 block

                                    font-serif

                                    text-[27px]

                                    font-black

                                    leading-none

                                    text-[var(--spellbook-title)]


                                "

                                x-text="primaryProfile()?.spell_save_dc"

                            ></strong>

                            <span

                                class="

                                    mt-1 block

                                    text-[7px]

                                    font-semibold

                                    text-[var(--spellbook-muted)]

                                "

                            >

                                Dificuldade

                            </span>

                        </div>

                        <div

                            class="

                                min-w-[116px]

                                border-l border-[var(--spellbook-border-soft)]

                                px-5 py-2.5

                                text-center

                            "

                        >

                            <span

                                class="

                                    block

                                    text-[7px]

                                    font-black uppercase

                                    tracking-[0.14em]

                                    text-[var(--spellbook-label)]

                                "

                            >

                                Ataque

                            </span>

                            <strong

                                class="

                                    mt-0.5 block

                                    font-serif

                                    text-[27px]

                                    font-black

                                    leading-none

                                    text-[var(--spellbook-title)]


                                "

                                x-text="

                                    formatBonus(

                                        primaryProfile()

                                            ?.spell_attack_bonus

                                    )

                                "

                            ></strong>

                            <span

                                class="

                                    mt-1 block

                                    text-[7px]

                                    font-semibold

                                    text-[var(--spellbook-muted)]

                                "

                            >

                                Bônus mágico

                            </span>

                        </div>

                    </div>

                </template>

                {{-- CONTADORES --}}

                <div

                    class="

                        flex min-w-0 flex-1

                        flex-wrap items-center

                        justify-start gap-2

                        xl:justify-end

                    "

                >

                    {{-- TRUQUES --}}

                    <div

                        class="

                            grid min-w-[108px]

                            grid-cols-[30px_1fr]

                            items-center gap-2

                            rounded-2xl

                            border border-[var(--spellbook-border-soft)]

                            bg-[var(--spellbook-panel-soft)]

                            px-2.5 py-2

                            shadow-[0_4px_12px_rgba(91,61,41,.055)]

                        "

                    >

                        <span

                            class="

                                flex h-8 w-8

                                items-center justify-center

                                rounded-xl

                                shadow-[inset_0_0_0_1px_rgba(141,98,72,.12)]

                            "

                            style="background: var(--spellbook-accent-soft); color: var(--spellbook-accent);"

                        >

                            <svg

                                class="h-3.5 w-3.5"

                                viewBox="0 0 24 24"

                                fill="none"

                                stroke="currentColor"

                                stroke-width="1.7"

                            >

                                <path d="m12 3 1.4 4.6L18 9l-4.6 1.4L12 15l-1.4-4.6L6 9l4.6-1.4L12 3Z"></path>

                                <path d="m18.5 15 .8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8.8-2.2Z"></path>

                            </svg>

                        </span>

                        <div>

                            <span

                                class="

                                    block

                                    text-[7px]

                                    font-black uppercase

                                    tracking-[0.11em]

                                    text-[#92725b]

                                "

                            >

                                Truques

                            </span>

                            <strong

                                class="

                                    mt-0.5 block

                                    font-serif

                                    text-[21px]

                                    font-black

                                    leading-none

                                    text-[#551a13]

                                "

                                x-text="cantripCount()"

                            ></strong>

                        </div>

                    </div>

                    {{-- FEITIÇOS --}}

                    <div

                        class="

                            grid min-w-[118px]

                            grid-cols-[30px_1fr]

                            items-center gap-2

                            rounded-2xl

                            border border-[var(--spellbook-border-soft)]

                            bg-[var(--spellbook-panel-soft)]

                            px-2.5 py-2

                            shadow-[0_4px_12px_rgba(91,61,41,.055)]

                        "

                        :class="

                            isSpellLimitExceeded()

                                ? 'ring-1 ring-amber-600/40'

                                : ''

                        "

                    >

                        <span

                            class="

                                flex h-8 w-8

                                items-center justify-center

                                rounded-xl

                                shadow-[inset_0_0_0_1px_rgba(141,98,72,.12)]

                            "

                            style="background: var(--spellbook-accent-soft); color: var(--spellbook-accent);"

                        >

                            <svg

                                class="h-3.5 w-3.5"

                                viewBox="0 0 24 24"

                                fill="none"

                                stroke="currentColor"

                                stroke-width="1.7"

                            >

                                <path d="M6 4h10a2 2 0 0 1 2 2v14H8a2 2 0 0 1-2-2V4Z"></path>

                                <path d="M9 8h6M9 12h6"></path>

                            </svg>

                        </span>

                        <div>

                            <span

                                class="

                                    block

                                    text-[7px]

                                    font-black uppercase

                                    tracking-[0.11em]

                                    text-[#92725b]

                                "

                            >

                                Feitiços

                            </span>

                            <strong

                                class="

                                    mt-0.5 block

                                    font-serif

                                    text-[21px]

                                    font-black

                                    leading-none

                                "

                                :class="

                                    isSpellLimitExceeded()

                                        ? 'text-amber-800'

                                        : 'text-[#551a13]'

                                "

                                x-text="

                                    primaryProfile()?.spell_limit

                                    !== null

                                    &&

                                    primaryProfile()?.spell_limit

                                    !== undefined

                                        ? `${leveledSpellCount(primaryProfile()?.id)} / ${primaryProfile()?.spell_limit}`

                                        : `${leveledSpellCount()}`

                                "

                            ></strong>

                        </div>

                    </div>

                    {{-- PREPARADOS --}}

                    <div

                        class="

                            grid min-w-[132px]

                            grid-cols-[30px_1fr]

                            items-center gap-2

                            rounded-2xl

                            border border-[var(--spellbook-border-soft)]

                            bg-[var(--spellbook-panel-soft)]

                            px-2.5 py-2

                            shadow-[0_4px_12px_rgba(91,61,41,.055)]

                        "

                        :class="

                            isPreparedLimitExceeded()

                                ? 'ring-1 ring-amber-600/40'

                                : ''

                        "

                    >

                        <span

                            class="

                                flex h-8 w-8

                                items-center justify-center

                                rounded-xl

                                shadow-[inset_0_0_0_1px_rgba(141,98,72,.12)]

                            "

                            style="background: var(--spellbook-accent-soft); color: var(--spellbook-accent);"

                        >

                            <svg

                                class="h-3.5 w-3.5"

                                viewBox="0 0 24 24"

                                fill="none"

                                stroke="currentColor"

                                stroke-width="1.7"

                            >

                                <path d="M7 4h10v16l-5-3-5 3V4Z"></path>

                                <path d="m9.5 10.5 1.7 1.7 3.6-3.7"></path>

                            </svg>

                        </span>

                        <div>

                            <span

                                class="

                                    block

                                    text-[7px]

                                    font-black uppercase

                                    tracking-[0.11em]

                                    text-[#92725b]

                                "

                            >

                                Preparados

                            </span>

                            <strong

                                class="

                                    mt-0.5 block

                                    font-serif

                                    text-[21px]

                                    font-black

                                    leading-none

                                "

                                :class="

                                    isPreparedLimitExceeded()

                                        ? 'text-amber-800'

                                        : 'text-[#551a13]'

                                "

                                x-text="

                                    primaryProfile()?.prepared_limit

                                    !== null

                                    &&

                                    primaryProfile()?.prepared_limit

                                    !== undefined

                                        ? `${preparedSpellCount(primaryProfile()?.id)} / ${primaryProfile()?.prepared_limit}`

                                        : `${preparedSpellCount()}`

                                "

                            ></strong>

                        </div>

                    </div>

                </div>

            </div>

            {{-- LINHA 3 — SLOTS CENTRALIZADOS --}}

            <div

                class="

                    border-t border-[#dfccb5]/75

                    bg-[var(--spellbook-paper-alt)]

                    px-4 py-2

                "

            >

                <div

                    x-show="displaySlots().some(slot => slot.slot_type === 'spell')"

                    class="

                        flex w-full

                        flex-wrap

                        items-center justify-center

                        gap-2

                    "

                >

                    <template

                        x-for="slot in displaySlots().filter(row => row.slot_type === 'spell')"

                        :key="slot.id"

                    >

                        <div

                            class="

                                inline-flex h-9

                                min-w-[103px]

                                items-center gap-2

                                rounded-2xl

                                border border-[#c9af8e]/65

                                bg-[var(--spellbook-paper-strong)]

                                px-2.5

                                shadow-[0_4px_11px_rgba(91,61,41,.055)]

                            "

                            :title="

                                `${slotLabel(slot)} · ${

                                    slotRecoveryLabel(

                                        slot.recovery

                                    )

                                }`

                            "

                        >

                            <span

                                class="

                                    flex h-7 min-w-7 shrink-0

                                    items-center justify-center

                                    rounded-lg

                                    bg-[var(--spellbook-panel)]

                                    px-1.5

                                    text-[9px] font-black

                                    text-[var(--spellbook-accent)]

                                "

                                x-text="slotLabel(slot)"

                            ></span>

                            <span

                                class="flex items-center gap-1"

                                title="Clique para gastar ou restaurar slots"

                            >

                                <template

                                    x-for="pip in Number(slot.maximum || 0)"

                                    :key="pip"

                                >

                                    <button

                                        type="button"

                                        @click.stop="toggleSlotPip(slot, pip)"

                                        class="

                                            h-3 w-3

                                            rounded-[4px]

                                            border border-[#87594a]/45

                                            transition hover:scale-110

                                        "

                                        :class="

                                            pip <= Number(slot.current || 0)

                                                ? 'bg-[#81291f] shadow-[0_2px_5px_rgba(107,29,20,.18)]'

                                                : 'bg-[#eadcc9] hover:bg-[#dbc6ac]'

                                        "

                                    ></button>

                                </template>

                            </span>

                            <strong

                                class="

                                    ml-auto shrink-0

                                    text-[9px] text-[var(--spellbook-muted)]

                                "

                                x-text="`${slot.current}/${slot.maximum}`"

                            ></strong>

                        </div>

                    </template>

                </div>
                <div

                    x-show="displaySlots().some(slot => String(slot.slot_type).startsWith('pact:'))"

                    class="sb-pact-slots-row mt-4 flex w-full flex-wrap items-center justify-center gap-2.5 pt-3.5"

                >

                    <template

                        x-for="slot in displaySlots().filter(row => String(row.slot_type).startsWith('pact:'))"

                        :key="slot.id"

                    >

                        <div

                            class="sb-pact-slot inline-flex h-9 min-w-[112px] items-center gap-2.5 rounded-2xl border px-3 transition"

                            :title="

                                `${slotLabel(slot)} · ${

                                    slotRecoveryLabel(

                                        slot.recovery

                                    )

                                }`

                            "

                        >

                            <span

                                class="sb-pact-slot__label flex h-7 min-w-7 shrink-0 items-center justify-center rounded-lg px-1.5 text-[9px] font-black"

                                x-text="slotLabel(slot)"

                            ></span>

                            <span

                                class="flex items-center gap-1"

                                title="Clique para gastar ou restaurar slots"

                            >

                                <template

                                    x-for="pip in Number(slot.maximum || 0)"

                                    :key="pip"

                                >

                                    <button

                                        type="button"

                                        @click.stop="toggleSlotPip(slot, pip)"

                                        class="sb-pact-slot__pip h-3 w-3 rounded-[4px] border transition hover:scale-110"

                                        :class="pip <= Number(slot.current || 0) ? 'is-filled' : 'is-empty'"

                                    ></button>

                                </template>

                            </span>

                            <strong

                                class="

                                    sb-pact-slot__count ml-auto shrink-0

                                    text-[9px] text-[var(--spellbook-muted)]

                                "

                                x-text="`${slot.current}/${slot.maximum}`"

                            ></strong>

                        </div>

                    </template>

                </div>

                <button

                    x-show="displaySlots().length === 0"

                    type="button"

                    @click="openSettings()"

                    class="

                        mx-auto block

                        rounded-xl

                        border border-dashed

                        border-[#b79b7c]/55

                        bg-[#fffaf2]/75

                        px-4 py-2.5

                        text-[9px] font-bold

                        text-[#725b49]

                    "

                >

                    Nenhum slot configurado · configurar

                </button>

            </div>

        </section>


        {{-- =============================================================

             VAZIO

        ============================================================== --}}

        <div

            x-show="filteredSpells().length === 0"

            class="

                rounded-[22px] border border-dashed border-[#b89a78]/50

                bg-[#fffaf2] px-6 py-12 text-center

                shadow-[0_6px_18px_rgba(91,61,41,.05)]

            "

        >

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--spellbook-button-bg)]/8 text-2xl text-[var(--spellbook-accent)]">✦</div>

            <h4 class="mt-3 font-serif text-xl font-black text-[#53150f]">Nenhum feitiço aqui</h4>

            <p class="mx-auto mt-1 max-w-md text-[12px] leading-5 text-[#755d49]">Adicione feitiços pelo catálogo ou altere os filtros.</p>

        </div>

        {{-- =============================================================

             GAVETAS — TRUQUES / CÍRCULOS

        ============================================================== --}}

        <div x-show="filteredSpells().length > 0" class="space-y-3">

            <template x-for="level in spellLevels()" :key="level">

                <section

                    x-data="{ open: Number(level) === 0 }"

                    class="

                        relative overflow-visible rounded-[22px]

                        border border-[#d8c3a6]/60 bg-[#fffaf3]

                        shadow-[0_6px_18px_rgba(91,61,41,.045)]

                    "

                >

                    <button

                        type="button"

                        @click="open = !open"

                        class="group flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-[#fbf4e9]"

                    >

                        <div

                            class="

                                relative h-11 w-11 shrink-0

                            "

                        >

                            <span

                                class="

                                    absolute inset-[4px]

                                    rotate-45

                                    rounded-[11px]

                                    border border-[#c59f75]/55

                                    bg-[#ead1ab]

                                    shadow-[0_4px_10px_rgba(91,61,41,.08)]

                                "

                            ></span>

                            <span

                                class="

                                    absolute inset-[5px]

                                    flex items-center justify-center

                                    rounded-full

                                    border border-[#fff8ec]

                                    bg-[#f9edda]

                                    shadow-[inset_0_0_0_1px_rgba(139,94,64,.12)]

                                "

                            >

                                <strong

                                    class="

                                        font-serif

                                        text-[14px]

                                        font-black

                                        text-[var(--spellbook-accent)]

                                    "

                                    x-text="Number(level) === 0 ? 'T' : `${level}º`"

                                ></strong>

                            </span>

                        </div>

                        <div class="min-w-0">

                            <h4

                                class="text-[10px] font-black uppercase tracking-[0.16em] text-[#6e4936]"

                                x-text="levelTitle(level)"

                            ></h4>

                            <p class="mt-0.5 text-[9px] text-[#8b6f5b]"><span x-text="spellsAtLevel(level).length"></span> feitiço(s)</p>

                        </div>

                        <span class="ml-1 h-px flex-1 bg-[#d5bea0]"></span>

                        <svg

                            class="h-4 w-4 shrink-0 text-[#87654e] transition-transform group-hover:text-[var(--spellbook-accent)]"

                            :class="open ? 'rotate-180' : ''"

                            viewBox="0 0 20 20"

                            fill="none"

                            stroke="currentColor"

                            stroke-width="2"

                        >

                            <path d="m5 7 5 5 5-5"></path>

                        </svg>

                    </button>

                    <div x-show="open" x-cloak x-transition.opacity class="border-t border-[#e5d4c0] bg-white/55 p-2.5">

                        <div class="relative overflow-visible rounded-[17px] border border-[#d5bfa3]/55 bg-white">

                            <template x-for="(spell, index) in spellsAtLevel(level)" :key="spell.id">

                                <div

                                    x-data="{

                                        rangeText: String((spell.range_display || spell.range || '')).toLowerCase(),

                                        areaTags: Array.isArray(spell.area_tags) ? spell.area_tags : [],

                                        menuOpen: false,

                                        noteOpen: false,

                                        isSelf() {

                                            return this.rangeText.includes('self') || this.rangeText.includes('pessoal');

                                        },

                                        isTouch() {

                                            return this.rangeText.includes('touch') || this.rangeText.includes('toque');

                                        },

                                        isArea() {

                                            return this.areaTags.length > 0

                                                || this.rangeText.includes('radius')

                                                || this.rangeText.includes('raio')

                                                || this.rangeText.includes('cone')

                                                || this.rangeText.includes('cube')

                                                || this.rangeText.includes('cubo')

                                                || this.rangeText.includes('sphere')

                                                || this.rangeText.includes('esfera')

                                                || this.rangeText.includes('cylinder')

                                                || this.rangeText.includes('cilindro')

                                                || this.rangeText.includes('line')

                                                || this.rangeText.includes('linha');

                                        }

                                    }"

                                    class="group grid grid-cols-[56px_minmax(0,1fr)_auto] items-center gap-3 px-3 py-3 transition hover:bg-[var(--spellbook-paper-alt)]"

                                    :class="index > 0 ? 'border-t border-[#ebdccb]' : ''"

                                >

                                    {{-- Selo do círculo / truque --}}

                                    <button

                                        type="button"

                                        @click="openSpell(spell)"

                                        class="

                                            relative h-12 w-12 shrink-0

                                            transition

                                            hover:scale-[1.03]

                                        "

                                        :title="spell.level_label_pt || spell.level_label"

                                    >

                                        <span

                                            class="

                                                absolute inset-[4px]

                                                rotate-45

                                                rounded-[12px]

                                                border border-[#c59f75]/60

                                                bg-[#e9cca4]

                                                shadow-[0_5px_12px_rgba(91,61,41,.1)]

                                                transition

                                                group-hover:bg-[#dfbd8f]

                                            "

                                        ></span>

                                        <span

                                            class="

                                                absolute inset-[5px]

                                                flex items-center justify-center

                                                rounded-full

                                                border border-[#fff8ec]

                                                bg-[#fff5e6]

                                                shadow-[inset_0_0_0_1px_rgba(139,94,64,.14)]

                                            "

                                        >

                                            {{-- V15: a arte opcional substitui o selo; sem arte, mantém T ou círculo. --}}
                                            <img
                                                x-show="spell.personal_media_url && !spell._mediaError"
                                                :src="spell.personal_media_url || null"
                                                x-on:error="spell._mediaError = true"
                                                x-on:load="spell._mediaError = false"
                                                :alt="`Arte de ${spell.name}`"
                                                class="absolute inset-0 h-full w-full rounded-full object-cover"
                                                loading="lazy"
                                            >
                                            <strong
                                                x-show="!spell.personal_media_url || spell._mediaError"
                                                class="font-serif text-[15px] font-black text-[#671d15]"
                                                x-text="Number(spell.level) === 0 ? 'T' : `${spell.level}º`"
                                            ></strong>

                                        </span>

                                        <span

                                            class="

                                                absolute -bottom-0.5 left-1/2

                                                h-1 w-3

                                                -translate-x-1/2

                                                rounded-full

                                                bg-[#7b281d]/70

                                            "

                                        ></span>

                                    </button>

                                    <div class="min-w-0">

                                        <div class="flex min-w-0 flex-wrap items-center gap-1.5">

                                            <button type="button" @click="openSpell(spell)" class="min-w-0 text-left">

                                                <strong class="block truncate font-serif text-[16px] font-black text-[#301611]" x-text="spell.name"></strong>

                                            </button>

                                        <button type="button" @click.stop="noteOpen = !noteOpen; if (noteOpen) $nextTick(() => $refs.quickSpellNote?.focus())"
                                            :title="noteOpen ? 'Fechar anotação' : 'Anotar neste feitiço'"
                                            :aria-expanded="noteOpen.toString()"
                                            class="sb-quick-note-toggle ml-1 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md border transition"
                                            :class="noteOpen ? 'is-active' : ''">
                                            <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L9 17l-4 1 1-4L16.5 3.5Z"/></svg>
                                        </button>

                                            {{-- Preparar fica junto do nome, não perdido à direita. --}}

                                            <button

                                                x-show="Number(spell.level ?? 0) > 0 && !spell.always_prepared"

                                                type="button"

                                                @click.stop="togglePrepared(spell)"

                                                :disabled="busy"

                                                class="

                                                    inline-flex items-center gap-1 rounded-full border

                                                    px-2 py-0.5 text-[6px] font-black uppercase

                                                    tracking-[0.05em] transition disabled:opacity-40

                                                "

                                                :class="spell.prepared_manual

                                                    ? 'border-emerald-800/20 bg-emerald-50 text-emerald-800 hover:bg-emerald-100'

                                                    : 'border-[#cdb79c]/65 bg-[#fbf4e9] text-[#8a664d] hover:border-[#9b6a4e]/50 hover:text-[var(--spellbook-accent)]'"

                                                :title="spell.prepared_manual ? 'Despreparar' : 'Preparar'"

                                            >

                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                                    <path d="M7 4h10v16l-5-3-5 3V4Z"></path>

                                                    <path x-show="spell.prepared_manual" d="m9 10 2 2 4-4"></path>

                                                </svg>

                                                <span x-text="spell.prepared_manual ? 'Preparado' : 'Preparar'"></span>

                                            </button>

                                            <span

                                                x-show="Number(spell.level ?? 0) > 0 && spell.always_prepared"

                                                class="rounded-full bg-[#ece0ca] px-2 py-0.5 text-[6px] font-black uppercase tracking-[0.04em] text-[#78523a]"

                                            >

                                                Sempre preparado

                                            </span>

                                            <span x-show="spell.concentration" class="rounded-full bg-[#eee4f2] px-2 py-0.5 text-[6px] font-black uppercase tracking-[0.04em] text-[#6d477f]">Concentração</span>

                                            <span x-show="spell.ritual" class="rounded-full bg-[#e2edf0] px-2 py-0.5 text-[6px] font-black uppercase tracking-[0.04em] text-[#365e69]">Ritual</span>

                                            <span x-show="spell.cast_without_slot && Number(spell.level ?? 0) > 0" class="rounded-full bg-[#f1e5c9] px-2 py-0.5 text-[6px] font-black uppercase tracking-[0.04em] text-[#79521f]">Sem slot</span>

                                        </div>

                                        <button type="button" @click="openSpell(spell)" class="mt-0.5 block max-w-full text-left">

                                            <p class="truncate text-[10.5px] font-semibold text-[#806754]" x-text="spell.subtitle_pt || spell.subtitle"></p>

                                        </button>

                                        <div class="mt-1 flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1 text-[9.5px] font-semibold text-[#6f5748]">

                                            <span class="inline-flex items-center gap-1" title="Tempo de conjuração">

                                                <svg class="h-3.5 w-3.5 text-[#936c4e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                                    <circle cx="12" cy="12" r="8"></circle>

                                                    <path d="M12 7v5l3 2"></path>

                                                </svg>

                                                <span x-text="spell.casting_time_display || spell.casting_time"></span>

                                            </span>

                                            <span class="inline-flex items-center gap-1" title="Alcance">

                                                <template x-if="isSelf()">

                                                    <svg class="h-3.5 w-3.5 text-[#936c4e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                                        <circle cx="12" cy="7" r="3"></circle>

                                                        <path d="M6.5 20c.7-3.2 2.8-5 5.5-5s4.8 1.8 5.5 5"></path>

                                                        <path d="M8.8 12.3 7 15M15.2 12.3 17 15"></path>

                                                    </svg>

                                                </template>

                                                <template x-if="!isSelf() && isTouch()">

                                                    <svg class="h-3.5 w-3.5 sb-label" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">

                                                        <path d="M8.5 11V6.5a1.5 1.5 0 0 1 3 0V10"></path>

                                                        <path d="M11.5 10V5.5a1.5 1.5 0 0 1 3 0V10"></path>

                                                        <path d="M14.5 10V7a1.5 1.5 0 0 1 3 0v5"></path>

                                                        <path d="M8.5 9.5 7.2 8.2a1.5 1.5 0 0 0-2.1 2.1l3.4 4.4c1 1.4 2.6 2.3 4.4 2.3h1.6c3 0 5.5-2.5 5.5-5.5V10a1.5 1.5 0 0 0-3 0"></path>

                                                    </svg>

                                                </template>

                                                <template x-if="!isSelf() && !isTouch() && isArea()">

                                                    <svg class="h-3.5 w-3.5 text-[#936c4e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                                        <circle cx="12" cy="12" r="7"></circle>

                                                        <circle cx="12" cy="12" r="2"></circle>

                                                    </svg>

                                                </template>

                                                <template x-if="!isSelf() && !isTouch() && !isArea()">

                                                    <svg class="h-3.5 w-3.5 text-[#936c4e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                                        <path d="M4 12h14"></path>

                                                        <path d="m14 7 5 5-5 5"></path>

                                                    </svg>

                                                </template>

                                                <span x-text="spell.range_display || spell.range"></span>

                                            </span>

                                        </div>

                                        {{-- V15: nota pessoal editável na lista; imagens só na aba Imagem do detalhe. --}}
                                        <button
                                            type="button"
                                            x-show="!noteOpen && String(spell.personal_notes || '').trim()"
                                            @click.stop="noteOpen = true; $nextTick(() => $refs.quickSpellNote?.focus())"
                                            class="sb-quick-note-preview mt-1.5 block max-w-full truncate border-l-2 pl-2 text-left text-[9px] italic transition"
                                            x-text="spell.personal_notes"
                                            title="Editar minha anotação"
                                        ></button>

                                        <div
                                            x-show="noteOpen"
                                            x-cloak
                                            x-transition.opacity.duration.120ms
                                            @click.outside="noteOpen = false"
                                            @keydown.escape.stop.prevent="noteOpen = false"
                                            class="sb-quick-note-editor mt-2 w-full max-w-[480px] rounded-xl border p-2.5"
                                        >
                                            <textarea
                                                x-ref="quickSpellNote"
                                                x-model="spell.personal_notes"
                                                @input.debounce.700ms="saveSpellNotes(spell)"
                                                @blur="saveSpellNotes(spell)"
                                                rows="2"
                                                maxlength="8000"
                                                placeholder="Uma observação para lembrar depois..."
                                                aria-label="Anotação pessoal do feitiço"
                                                class="sb-quick-note-textarea block w-full resize-y rounded-lg border-0 p-1.5 text-[11px] leading-5 focus:ring-0"
                                            ></textarea>
                                            <div class="sb-quick-note-status mt-1 flex items-center gap-1.5 px-1">
                                                <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                                <span
                                                    role="status"
                                                    aria-live="polite"
                                                    class="text-[9px]"
                                                    x-text="spell._noteStatus || 'Salva automaticamente'"
                                                ></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="flex shrink-0 items-center gap-1.5">

                                        <button

                                            type="button"

                                            @click.stop="openCastConfirm(spell)"

                                            class="

                                                inline-flex items-center gap-1.5 rounded-xl

                                                border border-[#7c382c]/25 bg-[#f3dfd6]

                                                px-3 py-2 text-[7px] font-black uppercase

                                                tracking-[0.07em] text-[var(--spellbook-accent)]

                                                shadow-[0_3px_9px_rgba(107,29,20,.07)]

                                                transition hover:bg-[#ebd2c7]

                                            "

                                        >

                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                                <path d="M12 3c2 3 4 5.2 4 8.2A4 4 0 0 1 8 11c0-2 1-3.8 2.3-5.6-.1 2.2.6 3.6 1.7 4.6.8-1.7.8-4.2 0-7Z"></path>

                                                <path d="M9.2 15.4A4 4 0 0 0 16 18c0-1.3-.5-2.5-1.3-3.5.1 1.2-.3 2.1-1.1 2.7-.3-1.1-1.1-2-2.3-2.6-.1 1.5-.7 2.3-2.1 2.8Z"></path>

                                            </svg>

                                            Conjurar

                                        </button>

                                        <div class="relative">

                                            <button

                                                type="button"

                                                @click.stop="menuOpen = !menuOpen"

                                                class="sb-button-soft flex h-8 w-8 items-center justify-center rounded-lg border text-[15px]"

                                                aria-label="Mais ações"

                                                title="Mais ações"

                                            >

                                                ⋯

                                            </button>

                                            <div

                                                x-show="menuOpen"

                                                x-cloak

                                                @click.outside="menuOpen = false"

                                                class="sb-paper sb-shadow absolute right-0 z-50 min-w-[165px] overflow-hidden rounded-xl border sb-border"

                                                :class="index >= spellsAtLevel(level).length - 2 ? 'bottom-9' : 'top-9'"

                                            >

                                                <button

                                                    type="button"

                                                    @click.stop="

                                                        menuOpen = false;

                                                        openSpell(spell)

                                                    "

                                                    class="sb-muted flex w-full items-center gap-2 px-3 py-2 text-left text-[9px] font-bold hover:bg-[var(--spellbook-hover)]"

                                                >

                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">

                                                        <path d="M12 5c5 0 8 7 8 7s-3 7-8 7-8-7-8-7 3-7 8-7Z"></path>

                                                        <circle cx="12" cy="12" r="2.5"></circle>

                                                    </svg>

                                                    Ver feitiço

                                                </button>

                                                <button

                                                    type="button"

                                                    @click.stop="

                                                        menuOpen = false;

                                                        removeSpell(spell)

                                                    "

                                                    class="flex w-full items-center gap-2 border-t border-red-900/10 px-3 py-2 text-left text-[9px] font-black text-red-700 hover:bg-red-50"

                                                >

                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">

                                                        <path d="M5 7h14"></path>

                                                        <path d="M9 7V4h6v3"></path>

                                                        <path d="m8 10 .5 8h7l.5-8"></path>

                                                    </svg>

                                                    Remover do Grimório

                                                </button>

                                            </div>

                                        </div>

                                        <button

                                            type="button"

                                            @click="openSpell(spell)"

                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-lg text-[#9b7a60] transition hover:bg-[var(--spellbook-hover)] hover:text-[var(--spellbook-accent)]"

                                            aria-label="Abrir feitiço"

                                        >

                                            ›

                                        </button>

                                    </div>

                                </div>

                            </template>

                        </div>

                    </div>

                </section>

            </template>

        </div>

    </div>

</div>