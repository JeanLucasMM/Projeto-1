<template x-teleport="body">
    <div
        x-show="loadoutOpen"
        x-cloak
        class="character-spellbook-v4 sb-modal-backdrop fixed inset-0 z-[145] flex items-center justify-center p-3 backdrop-blur-sm sm:p-4"
        :style="spellbookThemeStyle"
        @click.self="closeLoadouts()"
    >
        <section
            class="
                sb-modal-shell
                flex max-h-[94vh] w-full max-w-5xl flex-col overflow-hidden
                rounded-[24px] border
            "
        >
            {{-- =========================================================
                 CABEÇALHO
            ========================================================== --}}
            <header
                class="
                    flex shrink-0 items-center justify-between gap-4
                    sb-modal-header
                    border-t-[4px]
                    border-b
                    px-5 py-3.5 sm:px-6
                "
                style="border-top-color: var(--spellbook-accent);"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="
                            flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-xl border border-[#ccb08e]/55 bg-[#f0dfc7]
                            text-[#6b1d14]
                        "
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M8 6h11M8 12h11M8 18h11"></path>
                            <path d="M4 6h.01M4 12h.01M4 18h.01"></path>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="truncate font-serif text-[24px] font-black leading-tight text-[#571a13]">Loadouts de feitiços</h3>

                            <div class="relative">
                                <button
                                    type="button"
                                    @click="loadoutHelpOpen = !loadoutHelpOpen"
                                    class="
                                        flex h-6 w-6 items-center justify-center rounded-full
                                        border border-[#c8aa88]/60 bg-[#fbf3e6]
                                        text-[10px] font-black text-[#7a4f35]
                                        hover:bg-[#f1e3cf]
                                    "
                                    aria-label="Como funcionam os loadouts"
                                    title="Como funcionam os loadouts"
                                >
                                    ?
                                </button>

                                <div
                                    x-show="loadoutHelpOpen"
                                    x-cloak
                                    @click.outside="loadoutHelpOpen = false"
                                    class="
                                        absolute left-0 top-8 z-20 w-64 rounded-xl
                                        border border-[#cbb08f]/65 bg-[#fffaf3]
                                        p-3 text-[9px] leading-4 text-[#6f5748]
                                        shadow-[0_12px_28px_rgba(65,39,25,.18)]
                                    "
                                >
                                    <strong class="block text-[#5d231a]">Como funciona</strong>
                                    <p class="mt-1">Escolha feitiços de nível 1+ e salve o conjunto. Truques e feitiços sempre preparados ficam fora do loadout e não são alterados ao aplicá-lo.</p>
                                </div>
                            </div>
                        </div>

                        <p class="mt-0.5 text-[8px] text-[#806956]">Escolha, salve e reaplique suas preparações favoritas.</p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="closeLoadouts()"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-[#b99d83]/45 bg-white text-base text-[#6b1d14] hover:bg-[#f2e6d8]"
                    aria-label="Fechar"
                >
                    ×
                </button>
            </header>

            {{-- =========================================================
                 CONSTRUTOR
            ========================================================== --}}
            <div class="grid min-h-0 flex-1 lg:grid-cols-[minmax(0,1fr)_330px]">
                {{-- LISTA DE FEITIÇOS --}}
                <div class="flex min-h-0 flex-col border-b border-[#dcc9b1] lg:border-b-0 lg:border-r">
                    <div class="shrink-0 px-4 pb-3 pt-4 sm:px-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-[8px] font-black uppercase tracking-[0.13em] text-[#80604b]">Escolher feitiços</p>
                                <p class="mt-0.5 text-[9px] text-[#8a715e]">Somente feitiços de nível 1+ aparecem aqui.</p>
                            </div>

                            <button
                                type="button"
                                @click="loadoutUseCurrentPrepared()"
                                class="
                                    rounded-lg border border-[#c6aa88]/55 bg-[#fbf4e9]
                                    px-3 py-2 text-[7px] font-black uppercase
                                    tracking-[0.07em] text-[#6b1d14]
                                    hover:bg-[#f1e3cf]
                                "
                            >
                                Usar preparados atuais
                            </button>
                        </div>

                        <div class="mt-3 grid gap-2 sm:grid-cols-[minmax(0,1fr)_170px]">
                            <label class="relative block">
                                <svg
                                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b7b62]"
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
                                    x-model="loadoutSearch"
                                    placeholder="Buscar feitiços..."
                                    class="
                                        w-full rounded-xl border-[#c3a584] bg-white
                                        py-2.5 pl-9 pr-3 text-[10px] font-medium
                                        text-[#38251d] placeholder:text-[#9b8370]
                                        focus:border-[#6b1d14] focus:ring-[#6b1d14]
                                    "
                                >
                            </label>

                            <label>
                                <select
                                    x-model="loadoutForm.spellcasting_id"
                                    @change="loadoutProfileChanged()"
                                    class="
                                        w-full rounded-xl border-[#c3a584] bg-white
                                        px-3 py-2.5 text-[10px] font-bold text-[#38251d]
                                        focus:border-[#6b1d14] focus:ring-[#6b1d14]
                                    "
                                >
                                    <option value="">Todos os perfis</option>
                                    <template x-for="profile in profiles" :key="profile.id">
                                        <option :value="String(profile.id)" x-text="`${profile.class} · Nv. ${profile.caster_level}`"></option>
                                    </template>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto px-4 pb-4 sm:px-5">
                        <div class="overflow-hidden rounded-2xl border border-[#d6c0a5]/65 bg-white">
                            <template x-for="(spell, index) in loadoutAvailableSpells()" :key="spell.id">
                                <button
                                    type="button"
                                    @click="toggleLoadoutSpell(spell)"
                                    class="
                                        grid w-full grid-cols-[32px_42px_minmax(0,1fr)_auto]
                                        items-center gap-2.5 px-3 py-2.5 text-left transition
                                        hover:bg-[#fbf4e9]
                                    "
                                    :class="[
                                        index > 0 ? 'border-t border-[#ead9c6]' : '',
                                        loadoutSpellSelected(spell.id) ? 'bg-[#faf0e3]' : ''
                                    ]"
                                >
                                    <span
                                        class="
                                            flex h-5 w-5 items-center justify-center rounded-[6px]
                                            border text-[10px] font-black transition
                                        "
                                        :class="loadoutSpellSelected(spell.id)
                                            ? 'border-[#7b2a20] bg-[#7b2a20] text-white'
                                            : 'border-[#c9ae8c] bg-white text-transparent'"
                                    >
                                        ✓
                                    </span>

                                    <span
                                        class="
                                            relative flex h-9 w-9 items-center justify-center rounded-[11px]
                                            border border-[#d0b696]/65 bg-[#f0dfc7]
                                            font-serif text-[11px] font-black text-[#6b1d14]
                                        "
                                    >
                                        <span class="absolute inset-[4px] rounded-[8px] border border-white/80"></span>
                                        <span class="relative" x-text="`${spell.level}º`"></span>
                                    </span>

                                    <div class="min-w-0">
                                        <strong class="block truncate font-serif text-[13px] font-black text-[#411811]" x-text="spell.name"></strong>
                                        <p class="mt-0.5 truncate text-[8px] font-semibold text-[#846955]" x-text="spell.subtitle_pt || spell.subtitle"></p>
                                    </div>

                                    <span
                                        class="
                                            flex h-7 w-7 items-center justify-center rounded-lg
                                            border border-[#c8aa88]/55 bg-[#fffaf3]
                                            text-sm text-[#6b1d14]
                                        "
                                        x-text="loadoutSpellSelected(spell.id) ? '−' : '+'"
                                    ></span>
                                </button>
                            </template>

                            <div
                                x-show="loadoutAvailableSpells().length === 0"
                                class="px-4 py-10 text-center text-[9px] text-[#806956]"
                            >
                                Nenhum feitiço encontrado.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LOADOUT ATUAL --}}
                <aside class="flex min-h-0 flex-col bg-[#f7eddf]">
                    <div class="shrink-0 border-b border-[#ddc8af] px-4 py-4 sm:px-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[8px] font-black uppercase tracking-[0.12em] text-[#80604b]" x-text="loadoutEditingId === null ? 'Novo loadout' : 'Editar loadout'"></p>
                                <h4 class="mt-0.5 font-serif text-[18px] font-black text-[#53150f]" x-text="loadoutEditingId === null ? 'Montar preparação' : 'Atualizar preparação'"></h4>
                            </div>

                            <div
                                class="
                                    rounded-full bg-[#6b1d14] px-3 py-1.5
                                    text-center text-white shadow-[0_4px_10px_rgba(107,29,20,.14)]
                                "
                                :class="loadoutSelectionExceedsLimit() ? 'bg-amber-700' : 'bg-[#6b1d14]'"
                            >
                                <strong class="text-[14px] leading-none" x-text="loadoutSelectedIds.length"></strong>
                                <span class="ml-1 text-[7px] font-black uppercase tracking-[0.07em]">feitiços</span>
                            </div>
                        </div>

                        <label class="mt-3 block">
                            <span class="block text-[7px] font-black uppercase tracking-[0.09em] text-[#795844]">Nome</span>
                            <input
                                type="text"
                                maxlength="80"
                                x-model="loadoutForm.name"
                                placeholder="Ex.: Combate, Cura, Exploração..."
                                class="
                                    mt-1 w-full rounded-xl border-[#c3a584] bg-white
                                    px-3 py-2.5 text-[10px] font-bold text-[#38251d]
                                    focus:border-[#6b1d14] focus:ring-[#6b1d14]
                                "
                            >
                        </label>

                        <p
                            x-show="loadoutPreparedLimit() !== null"
                            class="mt-2 text-[8px] font-semibold"
                            :class="loadoutSelectionExceedsLimit() ? 'text-amber-800' : 'text-[#806956]'"
                        >
                            Limite informado:
                            <strong x-text="loadoutPreparedLimit()"></strong>
                            <span x-show="loadoutSelectionExceedsLimit()">· acima do limite</span>
                        </p>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3 sm:px-5">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[8px] font-black uppercase tracking-[0.12em] text-[#80604b]">Selecionados</p>
                            <button
                                x-show="loadoutSelectedIds.length > 0"
                                type="button"
                                @click="loadoutSelectedIds = []"
                                class="text-[7px] font-black uppercase tracking-[0.06em] text-[#9b5d46] hover:underline"
                            >
                                Limpar
                            </button>
                        </div>

                        <div class="mt-2 space-y-1.5">
                            <template x-for="spell in loadoutSelectedSpells()" :key="spell.id">
                                <div
                                    class="
                                        flex items-center gap-2.5 rounded-xl
                                        border border-[#d6c0a5]/65 bg-[#fffaf3]
                                        px-2.5 py-2 shadow-[0_2px_7px_rgba(91,61,41,.035)]
                                    "
                                >
                                    <span
                                        class="
                                            flex h-8 w-8 shrink-0 items-center justify-center
                                            rounded-[10px] bg-[#efddc1]
                                            font-serif text-[10px] font-black text-[#6b1d14]
                                        "
                                        x-text="`${spell.level}º`"
                                    ></span>

                                    <div class="min-w-0 flex-1">
                                        <strong class="block truncate text-[10px] font-black text-[#4b1a13]" x-text="spell.name"></strong>
                                        <span class="mt-0.5 block truncate text-[7px] text-[#8a6e59]" x-text="spell.school_pt || spell.school"></span>
                                    </div>

                                    <button
                                        type="button"
                                        @click="toggleLoadoutSpell(spell)"
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-base text-[#8b5b45] hover:bg-[#f3e4d1] hover:text-[#6b1d14]"
                                        aria-label="Remover do loadout"
                                    >
                                        ×
                                    </button>
                                </div>
                            </template>

                            <div
                                x-show="loadoutSelectedIds.length === 0"
                                class="rounded-xl border border-dashed border-[#c9af8e]/60 bg-[#fffaf3]/65 px-3 py-8 text-center"
                            >
                                <svg class="mx-auto h-5 w-5 text-[#9a765c]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M8 6h11M8 12h11M8 18h11"></path>
                                    <path d="M4 6h.01M4 12h.01M4 18h.01"></path>
                                </svg>
                                <p class="mt-2 text-[9px] font-bold text-[#745947]">Escolha os feitiços ao lado.</p>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 border-t border-[#ddc8af] px-4 py-3 sm:px-5">
                        <button
                            type="button"
                            @click="saveLoadout()"
                            :disabled="busy || loadoutSelectedIds.length === 0"
                            class="
                                w-full rounded-xl bg-[#762119] px-4 py-3
                                text-[8px] font-black uppercase tracking-[0.09em] text-white
                                shadow-[0_7px_16px_rgba(107,29,20,.16)]
                                transition hover:bg-[#84291f] disabled:opacity-40
                            "
                            x-text="busy ? 'Salvando...' : (loadoutEditingId === null ? 'Salvar loadout' : 'Salvar alterações')"
                        ></button>

                        <button
                            x-show="loadoutEditingId !== null"
                            type="button"
                            @click="newLoadout()"
                            class="mt-2 w-full text-[7px] font-black uppercase tracking-[0.07em] text-[#7a5845] hover:underline"
                        >
                            Cancelar edição
                        </button>
                    </div>
                </aside>
            </div>

            {{-- =========================================================
                 LOADOUTS SALVOS
            ========================================================== --}}
            <footer class="shrink-0 border-t border-[#dccab5] bg-[#fffaf3] px-4 py-3 sm:px-5">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <p class="text-[8px] font-black uppercase tracking-[0.12em] text-[#80604b]">Loadouts salvos</p>
                    <button
                        type="button"
                        @click="newLoadout()"
                        class="text-[7px] font-black uppercase tracking-[0.07em] text-[#6b1d14] hover:underline"
                    >
                        + Novo loadout
                    </button>
                </div>

                <div class="flex gap-2 overflow-x-auto pb-0.5">
                    <template x-for="loadout in loadouts" :key="loadout.id">
                        <article
                            class="
                                min-w-[190px] rounded-xl border border-[#d5c0a7]/65
                                bg-[#fbf5eb] px-3 py-2.5
                                shadow-[0_3px_9px_rgba(91,61,41,.04)]
                            "
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <strong class="block truncate font-serif text-[12px] font-black text-[#4b1812]" x-text="loadout.name"></strong>
                                    <p class="mt-0.5 text-[8px] text-[#816754]">
                                        <span x-text="`${loadout.spell_count} feitiço(s)`"></span>
                                        <span x-show="loadout.profile_name"> · <span x-text="loadout.profile_name"></span></span>
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="applyLoadout(loadout)"
                                    :disabled="busy"
                                    class="rounded-lg bg-[#762119] px-2.5 py-1.5 text-[6px] font-black uppercase tracking-[0.06em] text-white hover:bg-[#84291f] disabled:opacity-40"
                                >
                                    Aplicar
                                </button>
                            </div>

                            <div class="mt-2 flex items-center gap-2 border-t border-[#e3d3c0] pt-2">
                                <button type="button" @click="editLoadout(loadout)" class="text-[6px] font-black uppercase tracking-[0.06em] text-[#6b1d14] hover:underline">Editar</button>
                                <span class="text-[#c9ad91]">·</span>
                                <button type="button" @click="deleteLoadout(loadout)" :disabled="busy" class="text-[6px] font-black uppercase tracking-[0.06em] text-red-700 hover:underline disabled:opacity-40">Excluir</button>
                            </div>
                        </article>
                    </template>

                    <button
                        x-show="loadouts.length === 0"
                        type="button"
                        @click="newLoadout()"
                        class="min-w-[190px] rounded-xl border border-dashed border-[#c2a584]/60 bg-[#fbf5eb]/65 px-4 py-4 text-[8px] font-bold text-[#806956]"
                    >
                        Nenhum loadout salvo · criar o primeiro
                    </button>
                </div>
            </footer>
        </section>
    </div>
</template>
