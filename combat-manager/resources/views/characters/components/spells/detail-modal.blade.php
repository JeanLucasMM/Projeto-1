<template x-teleport="body">

    <div

        x-show="detailOpen"

        x-cloak

        class="

            character-spellbook-v4

            fixed inset-0 z-[125]

            flex items-center justify-center

            sb-modal-backdrop

            p-2 backdrop-blur-sm

            sm:p-4

        "

        :style="spellbookThemeStyle"

        @click.self="closeSpell()"

    >

        <style>

            .spell-reader-copy > p {

                margin: 0 !important;

            }

            .spell-reader-copy > p + p {

                margin-top: .8rem !important;

            }

            .spell-reader-copy > ul,

            .spell-reader-copy > ol {

                margin: .65rem 0 0 1.25rem !important;

            }

            .spell-reader-copy li + li {

                margin-top: .25rem !important;

            }

            .spell-reader-copy strong {

                color: var(--spellbook-title);

                font-weight: 800;

            }

            .spell-detail-shell {
                background:
                    radial-gradient(
                        circle at 14% 0%,
                        color-mix(in srgb, var(--spellbook-paper-strong) 78%, transparent),
                        transparent 24%
                    ),
                    linear-gradient(
                        180deg,
                        var(--spellbook-paper-strong) 0%,
                        var(--spellbook-paper) 100%
                    ) !important;
                box-shadow: var(--spellbook-modal-shadow) !important;
            }

            .spell-detail-shell::before {
                content: '';
                position: absolute;
                inset: 7px;
                z-index: 1;
                pointer-events: none;
                border: 1px solid color-mix(in srgb, var(--spellbook-accent) 20%, transparent);
                border-radius: 12px;
            }

            .spell-detail-corner {
                position: absolute;
                z-index: 40;
                width: 30px;
                height: 30px;
                pointer-events: none;
                color: color-mix(in srgb, var(--spellbook-accent) 55%, transparent);
            }

            .spell-detail-corner::after {
                content: '';
                position: absolute;
                width: 5px;
                height: 5px;
                border: 1px solid currentColor;
                transform: rotate(45deg);
                background: var(--spellbook-paper-strong);
            }

            .spell-detail-corner--tl {
                left: 11px;
                top: 11px;
                border-left: 1px solid currentColor;
                border-top: 1px solid currentColor;
            }

            .spell-detail-corner--tl::after {
                left: -3px;
                top: -3px;
            }

            .spell-detail-corner--tr {
                right: 11px;
                top: 11px;
                border-right: 1px solid currentColor;
                border-top: 1px solid currentColor;
            }

            .spell-detail-corner--tr::after {
                right: -3px;
                top: -3px;
            }

            .spell-detail-corner--bl {
                left: 11px;
                bottom: 11px;
                border-left: 1px solid currentColor;
                border-bottom: 1px solid currentColor;
            }

            .spell-detail-corner--bl::after {
                left: -3px;
                bottom: -3px;
            }

            .spell-detail-corner--br {
                right: 11px;
                bottom: 11px;
                border-right: 1px solid currentColor;
                border-bottom: 1px solid currentColor;
            }

            .spell-detail-corner--br::after {
                right: -3px;
                bottom: -3px;
            }

        </style>

        <div

            class="

                spell-detail-shell sb-modal-shell relative

                flex max-h-[94vh] w-full max-w-[860px] flex-col

                overflow-hidden rounded-[18px]

                border

            "

        >

            <span class="spell-detail-corner spell-detail-corner--tl"></span>
            <span class="spell-detail-corner spell-detail-corner--tr"></span>
            <span class="spell-detail-corner spell-detail-corner--bl"></span>
            <span class="spell-detail-corner spell-detail-corner--br"></span>

            {{-- =========================================================

                 BARRA DE NAVEGAÇÃO / AÇÕES

            ========================================================== --}}

            <header

                class="

                    sb-modal-header shrink-0

                    border-t-[4px]

                    border-b

                    px-5 py-2.5

                    sm:px-7

                "

                style="border-top-color: var(--spellbook-accent);"

            >

                <div class="flex items-center justify-between gap-4">

                    <nav class="flex items-center gap-5">

                        <button

                            type="button"

                            @click="detailTab = 'spell'"

                            class="

                                relative py-1.5

                                text-[8px]

                                font-black

                                uppercase

                                tracking-[0.1em]

                                transition

                            "

                            :class="

                                detailTab === 'spell'

                                    ? 'text-[#6b1d14]'

                                    : 'text-[#9a7d68] hover:text-[#6b1d14]'

                            "

                        >

                            Feitiço

                            <span

                                class="

                                    absolute inset-x-0 bottom-0

                                    h-[2px]

                                    bg-[#762119]

                                "

                                :class="

                                    detailTab === 'spell'

                                        ? 'opacity-100'

                                        : 'opacity-0'

                                "

                            ></span>

                        </button>

                        <button

                            type="button"

                            @click="detailTab = 'origin'"

                            class="

                                relative py-1.5

                                text-[8px]

                                font-black

                                uppercase

                                tracking-[0.1em]

                                transition

                            "

                            :class="

                                detailTab === 'origin'

                                    ? 'text-[#6b1d14]'

                                    : 'text-[#9a7d68] hover:text-[#6b1d14]'

                            "

                        >

                            Origem & usos

                            <span

                                class="

                                    absolute inset-x-0 bottom-0

                                    h-[2px]

                                    bg-[#762119]

                                "

                                :class="

                                    detailTab === 'origin'

                                        ? 'opacity-100'

                                        : 'opacity-0'

                                "

                            ></span>

                        </button>

                        {{-- V15: personalização separada da leitura e da preparação. --}}
                        <button
                            type="button"
                            @click="detailTab = 'image'"
                            class="relative inline-flex items-center gap-1.5 py-1.5 text-[8px] font-black uppercase tracking-[0.1em] transition"
                            :class="detailTab === 'image' ? 'text-[#6b1d14]' : 'text-[#9a7d68] hover:text-[#6b1d14]'"
                            :aria-current="detailTab === 'image' ? 'page' : null"
                        >
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="3" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <path d="m21 16-5.5-5.5L5 21" />
                            </svg>
                            Imagem
                            <span class="absolute inset-x-0 bottom-0 h-[2px] bg-[#762119]"
                                  :class="detailTab === 'image' ? 'opacity-100' : 'opacity-0'"></span>
                        </button>

                    </nav>

                    <div class="flex shrink-0 items-center gap-2">

                        <button

                            x-show="detailTab === 'spell'"

                            type="button"

                            @click="openCastConfirm(detailSpell)"

                            class="

                                rounded-lg

                                bg-[#762119]

                                px-3.5 py-2

                                text-[8px]

                                font-black

                                uppercase

                                tracking-[0.08em]

                                text-white

                                transition

                                hover:bg-[#862a20]

                            "

                        >

                            Conjurar

                        </button>

                        <button

                            type="button"

                            @click="

                                castPanelOpen = false;

                                closeSpell()

                            "

                            class="

                                flex h-8 w-8

                                items-center justify-center

                                rounded-lg

                                border border-[#b99d83]/45

                                bg-white

                                text-base

                                text-[#6b1d14]

                                transition

                                hover:bg-[#f2e6d8]

                            "

                            aria-label="Fechar feitiço"

                        >

                            ×

                        </button>

                    </div>

                </div>

            </header>

            {{-- =========================================================

                 CONTEÚDO

            ========================================================== --}}

            <div class="min-h-0 flex-1 overflow-y-auto">

                {{-- =====================================================

                     FEITIÇO

                ====================================================== --}}

                <article

                    x-show="detailTab === 'spell'"

                    x-cloak

                    class="

                        mx-auto

                        w-full

                        max-w-[760px]

                        px-5 py-6

                        sm:px-7 sm:py-7

                    "

                >

                    {{-- =====================================================

                         IDENTIDADE DO FEITIÇO — PARTE DO CONTEÚDO

                    ====================================================== --}}

                    <div class="mb-4">

                        <div

                            class="

                                flex items-baseline

                                justify-between gap-4

                            "

                        >

                            <h3

                                class="

                                    min-w-0

                                    font-serif

                                    text-[25px]

                                    font-black

                                    leading-[1.08]

                                    text-[#5b1b14]

                                    sm:text-[28px]

                                "

                                x-text="detailSpell?.name"

                            ></h3>

                            <span

                                class="

                                    shrink-0

                                    text-[8px]

                                    font-black

                                    uppercase

                                    tracking-[0.07em]

                                    text-[#94735d]

                                "

                                x-text="detailSpell?.source_label"

                            ></span>

                        </div>

                        <p

                            class="

                                mt-1

                                text-[12px]

                                font-semibold

                                italic

                                leading-5

                                text-[#4b362c]

                                sm:text-[13px]

                            "

                        >

                            <span

                                x-text="

                                    detailSpell?.subtitle_pt

                                    || detailSpell?.subtitle

                                "

                            ></span>

                            <span

                                x-show="detailSpell?.ritual"

                                class="ml-2 inline-flex rounded-full bg-[#e2edf0] px-2 py-0.5 not-italic text-[7px] font-black uppercase tracking-[0.06em] text-[#365e69]"

                            >

                                Ritual

                            </span>

                        </p>

                    </div>

                    {{-- =====================================================

                         DADOS DO FEITIÇO — MESMA LEITURA DO LIVRO

                    ====================================================== --}}

                    <dl

                        class="

                            space-y-0.5

                            text-[13px]

                            leading-[1.55]

                            text-[#2f211b]

                            sm:text-[14px]

                        "

                    >

                        <div>

                            <dt

                                class="

                                    inline

                                    font-black

                                    text-[#3f1712]

                                "

                            >

                                Conjuração:

                            </dt>

                            <dd

                                class="ml-1 inline"

                                x-text="

                                    detailSpell?.casting_time_display

                                    || detailSpell?.casting_time

                                    || '—'

                                "

                            ></dd>

                        </div>

                        <div>

                            <dt

                                class="

                                    inline

                                    font-black

                                    text-[#3f1712]

                                "

                            >

                                Alcance:

                            </dt>

                            <dd

                                class="ml-1 inline"

                                x-text="

                                    detailSpell?.range_display

                                    || detailSpell?.range

                                    || '—'

                                "

                            ></dd>

                        </div>

                        <div>

                            <dt

                                class="

                                    inline

                                    font-black

                                    text-[#3f1712]

                                "

                            >

                                Componentes:

                            </dt>

                            <dd

                                class="ml-1 inline"

                                x-text="

                                    detailSpell?.components

                                    || '—'

                                "

                            ></dd>

                        </div>

                        <div>

                            <dt

                                class="

                                    inline

                                    font-black

                                    text-[#3f1712]

                                "

                            >

                                Duração:

                            </dt>

                            <dd class="ml-1 inline">

                                <span

                                    x-text="

                                        detailSpell?.duration_display

                                        || detailSpell?.duration

                                        || '—'

                                    "

                                ></span>

                                <span

                                    x-show="detailSpell?.concentration"

                                >

                                    (Concentração)

                                </span>

                            </dd>

                        </div>

                    </dl>

                    {{-- =====================================================

                         TEXTO DO FEITIÇO

                    ====================================================== --}}

                    <div

                        class="

                            spell-reader-copy

                            spell-rich-copy

                            mt-4

                            text-[14px]

                            leading-[1.72]

                            text-[#2f211b]

                            sm:text-[15px]

                        "

                        x-html="

                            detailSpell?.description_html

                            || ''

                        "

                    ></div>

{{-- =====================================================

                         EM NÍVEIS SUPERIORES

                    ====================================================== --}}

                    <section

                        x-show="

                            detailSpell?.higher_level_html

                            ||

                            (

                                detailSpell

                                    ?.scaling_level_dice

                                    ?.length

                                ?? 0

                            )

                            > 0

                        "

                        class="mt-4"

                    >

                        <p

                            x-show="

                                detailSpell

                                    ?.higher_level_html

                            "

                            class="

                                text-[14px]

                                leading-[1.72]

                                text-[#33241d]

                                sm:text-[15px]

                            "

                        >

                            <strong

                                class="

                                    font-black

                                    text-[#4f1812]

                                "

                                x-text="

                                    (

                                        detailSpell

                                            ?.higher_level_label

                                        ||

                                        'Em Níveis Superiores'

                                    )

                                    + ':'

                                "

                            ></strong>

                            <span

                                class="

                                    spell-reader-copy

                                    spell-rich-copy

                                    ml-1

                                "

                                x-html="

                                    detailSpell

                                        ?.higher_level_html

                                    || ''

                                "

                            ></span>

                        </p>

                        <template

                            x-if="

                                Array.isArray(

                                    detailSpell

                                        ?.scaling_level_dice

                                )

                                &&

                                detailSpell

                                    .scaling_level_dice

                                    .length

                                > 0

                            "

                        >

                            <div

                                class="

                                    mt-2

                                    space-y-1

                                    text-[10px]

                                    leading-5

                                    text-[#725947]

                                "

                            >

                                <template

                                    x-for="

                                        progression

                                        in

                                        detailSpell

                                            .scaling_level_dice

                                    "

                                    :key="

                                        progression.label

                                    "

                                >

                                    <p>

                                        <strong

                                            class="

                                                mr-1.5

                                                text-[#5d1d15]

                                            "

                                            x-text="

                                                progression.label

                                            "

                                        ></strong>

                                        <template

                                            x-for="

                                                (

                                                    row,

                                                    index

                                                )

                                                in

                                                progression.levels

                                            "

                                            :key="

                                                row.level

                                            "

                                        >

                                            <span>

                                                <span

                                                    x-show="index > 0"

                                                    class="

                                                        mx-1

                                                        text-[#b69a82]

                                                    "

                                                >

                                                    ·

                                                </span>

                                                <span

                                                    :class="

                                                        Number(

                                                            row.level

                                                        )

                                                        <=

                                                        Number(

                                                            characterContext

                                                                .level

                                                        )

                                                            ? 'font-black text-[#6b1d14]'

                                                            : ''

                                                    "

                                                >

                                                    <span

                                                        x-text="

                                                            `Nv. ${row.level}`

                                                        "

                                                    ></span>

                                                    <span> — </span>

                                                    <span

                                                        x-text="

                                                            row.value

                                                        "

                                                    ></span>

                                                </span>

                                            </span>

                                        </template>

                                    </p>

                                </template>

                            </div>

                        </template>

                    </section>

                    {{-- =====================================================

                         LISTAS — RODAPÉ DO VERBETE

                    ====================================================== --}}

                    <p

                        x-show="

                            spellListLabels(

                                detailSpell

                            ).length

                            > 0

                        "

                        class="

                            mt-5

                            border-t border-[#dfd0bd]

                            pt-2.5

                            text-[10px]

                            italic

                            leading-5

                            text-[#806653]

                        "

                    >

                        <strong

                            class="

                                not-italic

                                text-[#5d1d15]

                            "

                        >

                            Listas:

                        </strong>

                        <span

                            x-text="

                                spellListLabels(

                                    detailSpell

                                ).join(', ')

                            "

                        ></span>

                    </p>

                </article>

                {{-- =====================================================

                     ORIGEM & USOS

                ====================================================== --}}

                <div

                    x-show="detailTab === 'origin'"

                    x-cloak

                    class="mx-auto w-full max-w-[820px] px-5 py-6 sm:px-7"

                >

                    {{-- ORIGEM / PREPARAÇÃO --}}

                    <section>

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-[8px] font-black uppercase tracking-[0.12em] text-[#8a6852]">No seu Grimório</p>

                                <h4 class="mt-1 font-serif text-[19px] font-black text-[#53150f]">Origem e preparação</h4>

                            </div>

                            <button type="button" @click="removeSpell(detailSpell)" :disabled="busy" class="text-[8px] font-black uppercase tracking-[0.08em] text-red-700 hover:underline disabled:opacity-40">Remover</button>

                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(280px,.9fr)]">

                            <label>

                                <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Perfil / origem</span>

                                <select

                                    :value="detailSpell?.spellcasting_id ?? ''"

                                    @change="changeSpellProfile(detailSpell, $event.target.value)"

                                    class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019] focus:border-[#6b1d14] focus:ring-[#6b1d14]"

                                >

                                    <option value="">Sem perfil / outra origem</option>

                                    <template x-for="profile in profiles" :key="profile.id">

                                        <option :value="profile.id" x-text="`${profile.class} · Nv. ${profile.caster_level}`"></option>

                                    </template>

                                </select>

                            </label>

                            <section class="rounded-xl border border-[#d8c6ae] bg-white/75 p-3 md:col-span-2">
                                <p class="text-[9px] font-black text-[#571a13]">Origens desta magia</p>
                                <p class="mb-2 text-[8px] text-[#7b624e]">Marque somente as classes pelas quais a personagem realmente conhece ou preparou a magia. Uma única cópia permanece no Grimório.</p>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <template x-for="profile in profiles.filter(row => row.is_active)" :key="profile.id">
                                        <div class="rounded-lg border border-[#deceb7] bg-[#fffaf5] px-3 py-2">
                                            <label class="flex cursor-pointer items-center gap-2 text-[10px] font-bold text-[#4e2a1e]">
                                                <input type="checkbox" class="rounded border-[#c6a780] text-[#762119]" :checked="(detailSpell?.casting_profiles || []).some(row => Number(row.id) === Number(profile.id))"
                                                    @change="updateSpellCastingProfiles(detailSpell, profile.id, $event.target.checked)">
                                                <span x-text="profile.class"></span>
                                            </label>
                                            <label x-show="(detailSpell?.casting_profiles || []).some(row => Number(row.id) === Number(profile.id)) && Number(detailSpell?.level ?? 0) > 0"
                                                class="mt-2 flex cursor-pointer items-center gap-2 text-[8px] text-[#725845]">
                                                <input type="checkbox" class="rounded border-[#c6a780] text-[#762119]" :checked="!!(detailSpell?.casting_profiles || []).find(row => Number(row.id) === Number(profile.id))?.prepared"
                                                    @change="updateSpellOriginPreparation(detailSpell, profile.id, $event.target.checked)">
                                                Preparada por esta classe
                                            </label>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            <template x-if="Number(detailSpell?.level ?? 0) > 0">

                                <div class="grid grid-cols-2 gap-2 self-end">

                                    <button

                                        type="button"

                                        @click="togglePrepared(detailSpell)"

                                        :disabled="busy || detailSpell?.always_prepared"

                                        class="rounded-xl border px-3 py-2.5 text-[8px] font-black uppercase tracking-[0.07em] transition disabled:opacity-40"

                                        :class="detailSpell?.prepared_manual ? 'border-emerald-800/20 bg-emerald-100 text-emerald-900' : 'border-[#bea384] bg-white text-[#6b1d14]'"

                                        x-text="detailSpell?.prepared_manual ? 'Preparado' : 'Preparar'"

                                    ></button>

                                    <button

                                        type="button"

                                        @click="toggleAlwaysPrepared(detailSpell)"

                                        :disabled="busy"

                                        class="rounded-xl border px-3 py-2.5 text-[8px] font-black uppercase tracking-[0.07em] transition"

                                        :class="detailSpell?.always_prepared ? 'border-[#8d5a25]/25 bg-[#f4e4bd] text-[#6a3c13]' : 'border-[#bea384] bg-white text-[#6b1d14]'"

                                    >

                                        Sempre preparado

                                    </button>

                                </div>

                            </template>

                            <template x-if="Number(detailSpell?.level ?? 0) === 0">

                                <div class="self-end rounded-xl border border-[#d6c0a5]/65 bg-[#f8efe2] px-3 py-2.5 text-[9px] leading-4 text-[#725946]">

                                    <strong class="text-[#5a1e16]">Truque:</strong> não entra no contador de feitiços preparados.

                                </div>

                            </template>

                        </div>

                    </section>

                    {{-- CONJURAÇÃO SEM SLOT --}}

                    <section class="mt-7 border-t border-[#dfd0bd] pt-6">

                        <p class="text-[8px] font-black uppercase tracking-[0.12em] text-[#8a6852]">Conjuração</p>

                        <h4 class="mt-1 font-serif text-[19px] font-black text-[#53150f]">Como este feitiço consome recursos</h4>

                        <p class="mt-1 text-[10px] leading-5 text-[#765d4a]">Configure exceções concedidas por classe, item, talento, origem ou regra da mesa.</p>

                        <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-2xl border px-4 py-3 transition" :class="detailSpell?.cast_without_slot_input ? 'border-[#8e633f]/30 bg-[#f2e3c9]' : 'border-[#cfb89b]/60 bg-white hover:bg-[#fbf5eb]'">

                            <input type="checkbox" x-model="detailSpell.cast_without_slot_input" class="mt-0.5 rounded border-[#9c795d] text-[#6b1d14] focus:ring-[#6b1d14]">

                            <span>

                                <strong class="block text-[10px] text-[#4e1c15]">Conjurar sem gastar slot</strong>

                                <span class="mt-0.5 block text-[9px] leading-4 text-[#7d6451]">Quando ativo, a conjuração normal deste feitiço não consome espaço de magia.</span>

                            </span>

                        </label>

                    </section>

                    {{-- USOS GRATUITOS --}}

                    <section x-show="!detailSpell?.cast_without_slot_input" class="mt-5 rounded-2xl border border-[#d5bea0]/65 bg-[#fbf4e8] p-4">

                        <div>

                            <p class="text-[8px] font-black uppercase tracking-[0.12em] text-[#8a6852]">Recurso próprio</p>

                            <h4 class="mt-1 font-serif text-[18px] font-black text-[#53150f]">Conjurações gratuitas</h4>

                            <p class="mt-1 text-[9px] leading-4 text-[#765d4a]">Para usos limitados sem gastar slot. O máximo vem primeiro; depois você controla quantos ainda restam.</p>

                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">

                            <label>

                                <span class="block text-[8px] font-black uppercase text-[#795741]">Máximo</span>

                                <input type="number" min="0" max="999" x-model="detailSpell.free_casts_max_input" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">

                            </label>

                            <label>

                                <span class="block text-[8px] font-black uppercase text-[#795741]">Atual</span>

                                <input type="number" min="0" max="999" x-model="detailSpell.free_casts_current_input" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">

                            </label>

                            <label>

                                <span class="block text-[8px] font-black uppercase text-[#795741]">Recuperação</span>

                                <select x-model="detailSpell.free_casts_recovery_input" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">

                                    <option value="">Nenhuma</option>

                                    <option value="short_rest">Descanso curto</option>

                                    <option value="long_rest">Descanso longo</option>

                                    <option value="day">Por dia / descanso longo</option>

                                    <option value="manual">Manual</option>

                                </select>

                            </label>

                        </div>

                    </section>

                    <div class="mt-5 flex justify-end">

                        <button

                            type="button"

                            @click="saveFreeCasts(detailSpell)"

                            :disabled="busy"

                            class="rounded-xl bg-[#762119] px-5 py-2.5 text-[8px] font-black uppercase tracking-[0.08em] text-white shadow-[0_5px_12px_rgba(107,29,20,.14)] transition hover:bg-[#862a20] disabled:opacity-50"

                        >

                            Salvar conjuração

                        </button>

                    </div>

                </div>

                {{-- =====================================================
                     V15 — IMAGEM DO FEITIÇO (OPCIONAL)
                     A imagem é pessoal; a ficha exibe a miniatura no selo.
                ====================================================== --}}
                <section
                    x-show="detailTab === 'image'"
                    x-cloak
                    class="mx-auto w-full max-w-[640px] px-4 py-5 sm:px-6 sm:py-6"
                >
                    <div class="mb-6 text-center">
                        <p class="text-[8px] font-black uppercase tracking-[.15em] text-[var(--spellbook-accent)]/80">Personalização</p>
                        <h4 class="mt-1.5 font-serif text-[23px] font-black text-[var(--spellbook-ink)]">Imagem do feitiço</h4>
                        <p class="mt-1 text-[10px] text-[var(--spellbook-muted)]">Sua imagem aparecerá no selo desta magia no Grimório.</p>
                    </div>

                    <div class="mx-auto w-full max-w-[360px] overflow-hidden rounded-[22px] border border-[#b88e63]/35 bg-[var(--spellbook-paper-alt)] shadow-[0_12px_28px_rgba(20,14,10,.16)]">
                        <div class="relative flex min-h-[220px] items-center justify-center overflow-hidden bg-[var(--spellbook-paper)] p-4 sm:min-h-[250px]">
                            <div class="pointer-events-none absolute inset-3 rounded-[15px] border border-[#b88e63]/35"></div>

                            <img
                                x-show="detailSpell?.personal_media_url && !detailSpell?._mediaError"
                                :src="detailSpell?.personal_media_url || null"
                                x-on:error="if (detailSpell) detailSpell._mediaError = true"
                                x-on:load="if (detailSpell) detailSpell._mediaError = false"
                                :alt="`Imagem personalizada de ${detailSpell?.name || 'feitiço'}`"
                                class="relative z-10 max-h-[220px] max-w-full rounded-[12px] object-contain shadow-[0_10px_28px_rgba(0,0,0,.22)]"
                            >

                            <div x-show="!detailSpell?.personal_media_url || detailSpell?._mediaError"
                                 class="relative z-10 flex flex-col items-center gap-3 text-center">
                                <div class="flex h-[92px] w-[92px] items-center justify-center rounded-[24px] border border-[#b88e63]/35 bg-[var(--spellbook-paper-strong)] shadow-[0_7px_18px_rgba(0,0,0,.18)]">
                                    <svg class="h-10 w-10 text-[var(--spellbook-accent)]/75" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                                        <rect x="2.5" y="3" width="19" height="18" rx="3" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <path d="m3 18 5.8-5.8a2 2 0 0 1 2.8 0l2.2 2.2 2.7-3a2 2 0 0 1 3 0L21.5 14" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold text-[var(--spellbook-muted)]"
                                      x-text="detailSpell?._mediaError ? 'Imagem indisponível. Escolha outra para substituir.' : 'Nenhuma imagem adicionada'"></span>
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-3 border-t border-[#b88e63]/30 bg-[var(--spellbook-paper-strong)]/90 px-4 py-4">
                            <div class="flex w-full flex-wrap items-center justify-center gap-2.5">
                                <label
                                    class="inline-flex min-h-[39px] cursor-pointer items-center justify-center gap-2 rounded-xl bg-[var(--spellbook-button-bg)] px-5 py-2.5 text-[9px] font-black uppercase tracking-[.05em] text-[var(--spellbook-button-text)] transition hover:brightness-110"
                                    :class="mediaSaving ? 'pointer-events-none opacity-50' : ''"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M12 16V4m-4 4 4-4 4 4M4 17v3h16v-3" />
                                    </svg>
                                    <span x-text="mediaSaving ? 'Enviando...' : (detailSpell?.personal_media_url ? 'Trocar imagem' : 'Escolher imagem')"></span>
                                    <input type="file" class="sr-only" accept="image/jpeg,image/png,image/gif,image/webp"
                                           :disabled="mediaSaving || !detailSpell" @change="uploadSpellMedia(detailSpell, $event)">
                                </label>
                                <button
                                    x-show="detailSpell?.personal_media_url"
                                    type="button"
                                    @click="removeSpellMedia(detailSpell)"
                                    :disabled="mediaSaving"
                                    class="inline-flex min-h-[39px] items-center justify-center rounded-xl border border-[#b88e63]/35 bg-black/20 px-4 py-2.5 text-[9px] font-bold text-[var(--spellbook-ink)] transition hover:bg-black/30 disabled:opacity-50"
                                >
                                    Remover
                                </button>
                            </div>
                            <span class="text-center text-[9px] text-[var(--spellbook-muted)]">PNG, JPG, WebP ou GIF · até 10 MB</span>
                            <p x-show="detailSpell?._mediaStatus" x-text="detailSpell?._mediaStatus"
                               role="status" aria-live="polite"
                               class="text-center text-[9px] font-semibold text-[#7db396]"></p>
                        </div>
                    </div>
                </section>

            </div>

        </div>

    </div>

</template>