{{-- ================================================================
     ITENS E MOEDAS RÁPIDAS
     Entregas operacionais sem criar um Espólio completo.
================================================================ --}}
<div
    x-show="lootQuickOpen"
    x-cloak
    class="fixed inset-0 z-[285] flex items-center justify-center p-3 sm:p-5"
    @keydown.escape.window="lootQuickOpen && closeQuickLoot()"
>
    <div
        class="absolute inset-0 bg-[#160d09]/70 backdrop-blur-[3px]"
        @click="closeQuickLoot()"
    ></div>

    <section
        x-transition.opacity.scale.95
        class="relative z-10 flex h-[min(760px,calc(100dvh-30px))] w-full max-w-[1080px] flex-col overflow-hidden rounded-[22px] border border-[#cdbb9f] bg-[#f7f2e9] shadow-[0_28px_90px_rgba(20,12,8,.36)]"
        role="dialog"
        aria-modal="true"
        aria-label="Itens e moedas rápidas"
    >
        <header class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b border-[#d8c7ab]/75 bg-[#fffdf8] px-4 py-3.5 sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#d6b88e] bg-[#f3e5d2] text-[18px] text-[#7a301e]">⚡</span>

                <div class="min-w-0">
                    <p class="text-[7px] font-black uppercase tracking-[0.16em] text-[#a17e62]">Acesso rápido</p>
                    <div class="mt-0.5 flex flex-wrap items-center gap-2">
                        <h3 class="font-serif text-[22px] font-black leading-none text-[#53150f]">Itens e moedas rápidas</h3>
                        <span
                            x-show="lootQuickActiveOfferCount > 0"
                            x-cloak
                            class="rounded-full bg-emerald-100 px-2 py-1 text-[7px] font-black text-emerald-800"
                            x-text="lootQuickActiveOfferCount + ' ativo(s)'"
                        ></span>
                    </div>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <button
                    type="button"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-[#cdbb9f] bg-white px-3.5 text-[8px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-[#f2e8dc]"
                    @click="openQuickLootComposer('item')"
                >
                    <span class="text-[13px] leading-none">+</span>
                    Item
                </button>

                <button
                    type="button"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-[#6b1d14] px-3.5 text-[8px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f]"
                    @click="openQuickLootComposer('currency')"
                >
                    <span class="text-[13px] leading-none">+</span>
                    Moedas
                </button>

                <button
                    type="button"
                    class="grid h-9 w-9 place-items-center rounded-lg text-[18px] font-black text-[#8c6239] transition hover:bg-[#efe4d5] hover:text-[#53150f]"
                    @click="closeQuickLoot()"
                    aria-label="Fechar"
                >×</button>
            </div>
        </header>

        <div class="flex min-h-0 flex-1 flex-col">
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-[#e3d5c2] bg-[#f2eadf] px-4 py-2.5 sm:px-5">
                <div class="inline-grid grid-cols-2 rounded-xl border border-[#d6c2a7] bg-[#e8ddcf] p-1">
                    <button
                        type="button"
                        class="min-w-[118px] rounded-lg px-3 py-2 text-[8px] font-black uppercase tracking-wider transition"
                        :class="lootQuickTab === 'available' ? 'bg-[#fffdf8] text-[#53150f] shadow-sm' : 'text-[#886b55] hover:bg-white/55'"
                        @click="lootQuickTab = 'available'; lootQuickExpandedItemId = null; lootQuickExpandedCurrencyId = null; lootQuickItemMenuId = null"
                    >
                        Disponíveis
                        <span
                            x-show="lootQuickActiveOfferCount > 0"
                            x-cloak
                            class="ml-1 rounded-full bg-[#eadbc8] px-1.5 py-0.5 text-[7px]"
                            x-text="lootQuickActiveOfferCount"
                        ></span>
                    </button>

                    <button
                        type="button"
                        class="min-w-[118px] rounded-lg px-3 py-2 text-[8px] font-black uppercase tracking-wider transition"
                        :class="lootQuickTab === 'history' ? 'bg-[#fffdf8] text-[#53150f] shadow-sm' : 'text-[#886b55] hover:bg-white/55'"
                        @click="lootQuickTab = 'history'; lootQuickExpandedItemId = null; lootQuickExpandedCurrencyId = null; lootQuickItemMenuId = null"
                    >Histórico</button>
                </div>

                <span class="hidden text-[8px] font-semibold text-[#957762] sm:block">
                    Recolher remove apenas o que ainda está disponível.
                </span>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3.5 sm:px-5">
                <div
                    x-show="lootQuickError"
                    x-cloak
                    class="mb-3 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2.5 text-[9px] font-bold text-red-800"
                    x-text="lootQuickError"
                ></div>

                <div
                    x-show="lootQuickNotice"
                    x-cloak
                    class="mb-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-[9px] font-bold text-emerald-800"
                    x-text="lootQuickNotice"
                ></div>

                {{-- ========================================================
                     DISPONÍVEIS
                ========================================================= --}}
                <section x-show="lootQuickTab === 'available'" x-cloak class="space-y-4">
                    {{-- ITENS --}}
                    <div x-show="lootQuickAvailableItems.length > 0" x-cloak>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="text-[7px] font-black uppercase tracking-[0.16em] text-[#a17e62]">Itens</span>
                            <span class="h-px flex-1 bg-[#e3d6c5]"></span>
                            <span class="text-[7px] font-black text-[#8c725e]" x-text="lootQuickAvailableItems.length"></span>
                        </div>

                        <div class="space-y-2">
                            <template x-for="item in lootQuickAvailableItems" :key="'quick-live-' + item.id">
                                <article class="overflow-visible rounded-2xl border border-[#dac8ae] bg-[#fffdf8] shadow-[0_5px_16px_rgba(83,21,15,.035)]">
                                    <div class="flex items-center gap-3 p-3">
                                        <div class="grid h-14 w-14 shrink-0 place-items-center overflow-hidden rounded-xl border border-[#d7c2a5] bg-[#efe6da]">
                                            <template x-if="lootItemImageUrl(lootQuickDrop, item)">
                                                <img :src="lootItemImageUrl(lootQuickDrop, item)" :alt="item.name" class="h-full w-full object-contain p-1.5">
                                            </template>
                                            <template x-if="!lootItemImageUrl(lootQuickDrop, item)">
                                                <span class="font-serif text-lg font-black text-[#aa8b70]">◇</span>
                                            </template>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="truncate font-serif text-[15px] font-black text-[#53150f]" x-text="item.name"></h4>
                                                <span class="rounded-full bg-emerald-100 px-2 py-1 text-[6px] font-black uppercase tracking-wider text-emerald-800">Disponível</span>
                                            </div>

                                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-semibold text-[#876b55]">
                                                <span>
                                                    <strong class="text-[#53150f]" x-text="item.quantity_remaining ?? 0"></strong>
                                                    de
                                                    <span x-text="item.quantity_total ?? 0"></span>
                                                    restante(s)
                                                </span>
                                                <span x-text="lootAudienceLabel(item)"></span>
                                            </div>

                                            <div x-show="lootQuickPickedQuantity(item) > 0" x-cloak class="mt-2 flex items-center gap-2">
                                                <span class="text-[7px] font-black uppercase tracking-wider text-[#a17e62]">Pegaram</span>

                                                <div class="flex -space-x-1.5">
                                                    <template x-for="claim in lootQuickClaimRows(item).filter(row => !row?.reversed_at).slice(0, 5)" :key="'quick-avatar-' + claim.id">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center overflow-hidden rounded-full border-2 border-[#fffdf8] bg-[#eadbc8] text-[7px] font-black text-[#6b1d14]"
                                                            :title="lootClaimDestinationLabel(claim)"
                                                        >
                                                            <template x-if="lootQuickClaimCharacter(claim)?.image_url">
                                                                <img :src="lootQuickClaimCharacter(claim).image_url" :alt="lootQuickClaimCharacter(claim)?.name ?? ''" class="h-full w-full object-cover">
                                                            </template>
                                                            <template x-if="!lootQuickClaimCharacter(claim)?.image_url">
                                                                <span x-text="String(lootQuickClaimCharacter(claim)?.name ?? '?').trim().slice(0,1).toUpperCase()"></span>
                                                            </template>
                                                        </span>
                                                    </template>
                                                </div>

                                                <button
                                                    type="button"
                                                    class="text-[7px] font-black text-[#6b1d14] hover:underline"
                                                    @click="toggleQuickHistoryDetails(item.id)"
                                                >Quem pegou</button>
                                            </div>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-1.5">
                                            <button
                                                x-show="lootQuickPickedQuantity(item) > 0"
                                                x-cloak
                                                type="button"
                                                class="rounded-lg border border-[#d6c2a7] bg-white px-3 py-2 text-[6px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#f1e8db]"
                                                @click="toggleQuickHistoryDetails(item.id)"
                                            >Detalhes</button>

                                            <button
                                                type="button"
                                                class="rounded-lg border border-[#d6c2a7] bg-[#f7efe5] px-3 py-2 text-[6px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#eee0d1] disabled:opacity-40"
                                                :disabled="lootActingItemId !== null"
                                                @click="withdrawQuickLootItem(item)"
                                            >Recolher</button>
                                        </div>
                                    </div>

                                    <div
                                        x-show="parseInt(lootQuickExpandedItemId) === parseInt(item.id)"
                                        x-cloak
                                        x-collapse
                                        class="border-t border-[#e5d9c8] bg-[#faf6ef] px-3 py-2.5"
                                    >
                                        <div x-show="lootQuickClaimRows(item).length > 0" class="space-y-1.5">
                                            <template x-for="claim in lootQuickClaimRows(item)" :key="'quick-live-claim-' + claim.id">
                                                <div class="flex items-center justify-between gap-3 rounded-xl bg-white px-3 py-2">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-[8px] font-black text-[#53150f]" x-text="lootClaimDestinationLabel(claim)"></p>
                                                        <p class="mt-0.5 text-[7px] text-[#9b806b]">
                                                            <span x-text="(claim.quantity ?? 1) + '×'"></span>
                                                            <span x-show="lootClaimDate(claim.claimed_at)"> · </span>
                                                            <span x-text="lootClaimDate(claim.claimed_at)"></span>
                                                            <span x-show="claim?.reversed_at" class="ml-1 font-black text-[#8c725e]">· recolhido</span>
                                                        </p>
                                                    </div>

                                                    <button
                                                        x-show="!claim?.reversed_at"
                                                        x-cloak
                                                        type="button"
                                                        class="shrink-0 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-[6px] font-black uppercase tracking-wider text-red-700 hover:bg-red-100 disabled:opacity-40"
                                                        :disabled="lootReversingClaimId !== null"
                                                        @click="reverseQuickLootClaim(item, claim)"
                                                    >Recolher do personagem</button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    {{-- MOEDAS --}}
                    <div x-show="lootQuickAvailableCurrencies.length > 0" x-cloak>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="text-[7px] font-black uppercase tracking-[0.16em] text-[#a17e62]">Moedas</span>
                            <span class="h-px flex-1 bg-[#e3d6c5]"></span>
                            <span class="text-[7px] font-black text-[#8c725e]" x-text="lootQuickAvailableCurrencies.length"></span>
                        </div>

                        <div class="space-y-2">
                            <template x-for="entry in lootQuickAvailableCurrencies" :key="'quick-live-currency-' + entry.id">
                                <article class="overflow-hidden rounded-2xl border border-[#d8c49e] bg-[#fffdf8] shadow-[0_5px_16px_rgba(83,21,15,.035)]">
                                    <div class="flex items-center gap-3 p-3">
                                        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl border border-[#d9bd7f] bg-[#f5ead1]">
                                            <span class="font-serif text-[22px] font-black text-[#8a5d19]">¤</span>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="truncate font-serif text-[15px] font-black text-[#53150f]" x-text="entry?.label || 'Moedas rápidas'"></h4>
                                                <span class="rounded-full bg-emerald-100 px-2 py-1 text-[6px] font-black uppercase tracking-wider text-emerald-800">Disponível</span>
                                            </div>

                                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-semibold text-[#876b55]">
                                                <strong class="text-[#6b1d14]" x-text="lootCurrencyInline(entry, true)"></strong>
                                                <span x-text="lootQuickCurrencyDistributionLabel(entry)"></span>
                                            </div>

                                            <div x-show="lootQuickCurrencyClaimRows(entry).length > 0" x-cloak class="mt-2 flex items-center gap-2">
                                                <span class="text-[7px] font-black uppercase tracking-wider text-[#a17e62]">Pegaram</span>

                                                <div class="flex -space-x-1.5">
                                                    <template x-for="claim in lootQuickCurrencyClaimRows(entry).slice(0, 5)" :key="'quick-currency-avatar-' + claim.id">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center overflow-hidden rounded-full border-2 border-[#fffdf8] bg-[#eadbc8] text-[7px] font-black text-[#6b1d14]"
                                                            :title="lootQuickCurrencyClaimCharacter(claim)?.name ?? 'Personagem'"
                                                        >
                                                            <template x-if="lootQuickCurrencyClaimCharacter(claim)?.image_url">
                                                                <img :src="lootQuickCurrencyClaimCharacter(claim).image_url" :alt="lootQuickCurrencyClaimCharacter(claim)?.name ?? ''" class="h-full w-full object-cover">
                                                            </template>
                                                            <template x-if="!lootQuickCurrencyClaimCharacter(claim)?.image_url">
                                                                <span x-text="String(lootQuickCurrencyClaimCharacter(claim)?.name ?? '?').trim().slice(0,1).toUpperCase()"></span>
                                                            </template>
                                                        </span>
                                                    </template>
                                                </div>

                                                <button
                                                    type="button"
                                                    class="text-[7px] font-black text-[#6b1d14] hover:underline"
                                                    @click="toggleQuickCurrencyHistoryDetails(entry.id)"
                                                >Quem pegou</button>
                                            </div>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-1.5">
                                            <button
                                                x-show="lootQuickCurrencyClaimRows(entry).length > 0"
                                                x-cloak
                                                type="button"
                                                class="rounded-lg border border-[#d6c2a7] bg-white px-3 py-2 text-[6px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#f1e8db]"
                                                @click="toggleQuickCurrencyHistoryDetails(entry.id)"
                                            >Detalhes</button>

                                            <button
                                                type="button"
                                                class="rounded-lg border border-[#d6c2a7] bg-[#f7efe5] px-3 py-2 text-[6px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#eee0d1] disabled:opacity-40"
                                                :disabled="lootQuickActingCurrencyId !== null"
                                                @click="withdrawQuickLootCurrency(entry)"
                                            >Recolher</button>
                                        </div>
                                    </div>

                                    <div
                                        x-show="parseInt(lootQuickExpandedCurrencyId) === parseInt(entry.id)"
                                        x-cloak
                                        x-collapse
                                        class="border-t border-[#e5d9c8] bg-[#faf6ef] px-3 py-2.5"
                                    >
                                        <div x-show="lootQuickCurrencyClaimRows(entry).length > 0" class="space-y-1.5">
                                            <template x-for="claim in lootQuickCurrencyClaimRows(entry)" :key="'quick-live-currency-claim-' + claim.id">
                                                <div class="flex items-center justify-between gap-3 rounded-xl bg-white px-3 py-2">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-[8px] font-black text-[#53150f]" x-text="lootQuickCurrencyClaimCharacter(claim)?.name ?? 'Personagem'"></p>
                                                        <p class="mt-0.5 text-[7px] text-[#9b806b]">
                                                            <strong class="text-[#6b1d14]" x-text="lootCurrencyClaimInline(claim)"></strong>
                                                            <span x-show="lootClaimDate(claim.claimed_at)"> · </span>
                                                            <span x-text="lootClaimDate(claim.claimed_at)"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <p x-show="lootQuickCurrencyClaimRows(entry).length === 0" x-cloak class="py-2 text-center text-[8px] text-[#9a806d]">Ninguém retirou estas moedas ainda.</p>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    <div
                        x-show="lootQuickAvailableItems.length === 0 && lootQuickAvailableCurrencies.length === 0"
                        x-cloak
                        class="grid min-h-[280px] place-items-center rounded-2xl border border-dashed border-[#d2bea2] bg-[#fffdf8]/75 px-6 text-center"
                    >
                        <div>
                            <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-[#efe3d4] text-xl">⚡</span>
                            <h4 class="mt-3 font-serif text-[18px] font-black text-[#53150f]">Nada disponível agora</h4>
                            <div class="mt-3 flex flex-wrap justify-center gap-2">
                                <button type="button" class="rounded-lg border border-[#cdbb9f] bg-white px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-[#6b1d14]" @click="openQuickLootComposer('item')">+ Item</button>
                                <button type="button" class="rounded-lg bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-white" @click="openQuickLootComposer('currency')">+ Moedas</button>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ========================================================
                     HISTÓRICO
                ========================================================= --}}
                <section x-show="lootQuickTab === 'history'" x-cloak class="space-y-4">
                    {{-- ITENS --}}
                    <div x-show="lootQuickHistoryItems.length > 0" x-cloak>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="text-[7px] font-black uppercase tracking-[0.16em] text-[#a17e62]">Itens</span>
                            <span class="h-px flex-1 bg-[#e3d6c5]"></span>
                        </div>

                        <div class="space-y-2">
                            <template x-for="item in lootQuickHistoryItems" :key="'quick-history-' + item.id">
                                <article class="rounded-2xl border border-[#d8c7ab]/75 bg-[#fffdf8]">
                                    <div class="flex items-center gap-3 p-3">
                                        <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl border border-[#d7c2a5] bg-[#efe6da]">
                                            <template x-if="lootItemImageUrl(lootQuickDrop, item)">
                                                <img :src="lootItemImageUrl(lootQuickDrop, item)" :alt="item.name" class="h-full w-full object-contain p-1">
                                            </template>
                                            <template x-if="!lootItemImageUrl(lootQuickDrop, item)"><span class="font-serif text-base font-black text-[#aa8b70]">◇</span></template>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="truncate font-serif text-[14px] font-black text-[#53150f]" x-text="item.name"></h4>
                                                <span
                                                    class="rounded-full border px-2 py-1 text-[6px] font-black uppercase tracking-wider"
                                                    :class="{
                                                        'border-emerald-200 bg-emerald-50 text-emerald-800': lootQuickStatusTone(item) === 'is-live',
                                                        'border-[#d9c9b6] bg-[#f3ece3] text-[#806650]': lootQuickStatusTone(item) === 'is-withdrawn',
                                                        'border-[#d4c6b5] bg-[#eee8df] text-[#75665b]': lootQuickStatusTone(item) === 'is-depleted',
                                                        'border-amber-200 bg-amber-50 text-amber-800': lootQuickStatusTone(item) === 'is-hidden'
                                                    }"
                                                    x-text="lootQuickStatusLabel(item)"
                                                ></span>
                                            </div>
                                            <p class="mt-1 text-[8px] text-[#8b6e58]">
                                                <span x-text="(item.quantity_remaining ?? 0) + '/' + (item.quantity_total ?? 0) + ' restante(s)'"></span>
                                                <span> · </span>
                                                <span x-text="lootQuickPickedQuantity(item) + ' retirado(s)'"></span>
                                            </p>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-1.5">
                                            <button
                                                x-show="item.release_status !== 'released' && parseInt(item.quantity_remaining ?? 0) > 0"
                                                x-cloak
                                                type="button"
                                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-[6px] font-black uppercase tracking-wider text-emerald-800 hover:bg-emerald-100 disabled:opacity-40"
                                                :disabled="lootActingItemId !== null"
                                                @click="releaseQuickExistingItem(item)"
                                            >Disponibilizar</button>

                                            <button
                                                type="button"
                                                class="rounded-lg border border-[#d6c2a7] bg-white px-3 py-2 text-[6px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#f1e8db]"
                                                @click="toggleQuickHistoryDetails(item.id)"
                                            >Quem pegou</button>

                                            <button
                                                x-show="lootCanDeleteItem(item)"
                                                x-cloak
                                                type="button"
                                                class="grid h-8 w-8 place-items-center rounded-lg text-[13px] text-red-700 hover:bg-red-50"
                                                @click="deleteQuickLootItem(item)"
                                                title="Excluir item sem histórico"
                                            >×</button>
                                        </div>
                                    </div>

                                    <div
                                        x-show="parseInt(lootQuickExpandedItemId) === parseInt(item.id)"
                                        x-cloak
                                        x-collapse
                                        class="border-t border-[#e5d9c8] bg-[#faf6ef] px-3 py-2.5"
                                    >
                                        <div x-show="lootQuickClaimRows(item).length > 0" class="space-y-1.5">
                                            <template x-for="claim in lootQuickClaimRows(item)" :key="'quick-history-claim-' + claim.id">
                                                <div class="flex items-center justify-between gap-3 rounded-xl bg-white px-3 py-2">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-[8px] font-black text-[#53150f]" x-text="lootClaimDestinationLabel(claim)"></p>
                                                        <p class="mt-0.5 text-[7px] text-[#9b806b]">
                                                            <span x-text="(claim.quantity ?? 1) + '×'"></span>
                                                            <span x-show="lootClaimDate(claim.claimed_at)"> · </span>
                                                            <span x-text="lootClaimDate(claim.claimed_at)"></span>
                                                            <span x-show="claim?.reversed_at" class="ml-1 font-black text-[#8c725e]">· recolhido</span>
                                                        </p>
                                                    </div>
                                                    <button
                                                        x-show="!claim?.reversed_at"
                                                        x-cloak
                                                        type="button"
                                                        class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-[6px] font-black uppercase tracking-wider text-red-700 hover:bg-red-100 disabled:opacity-40"
                                                        :disabled="lootReversingClaimId !== null"
                                                        @click="reverseQuickLootClaim(item, claim)"
                                                    >Recolher</button>
                                                </div>
                                            </template>
                                        </div>
                                        <p x-show="lootQuickClaimRows(item).length === 0" x-cloak class="py-2 text-center text-[8px] text-[#9a806d]">Nenhuma retirada registrada.</p>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    {{-- MOEDAS --}}
                    <div x-show="lootQuickHistoryCurrencies.length > 0" x-cloak>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="text-[7px] font-black uppercase tracking-[0.16em] text-[#a17e62]">Moedas</span>
                            <span class="h-px flex-1 bg-[#e3d6c5]"></span>
                        </div>

                        <div class="space-y-2">
                            <template x-for="entry in lootQuickHistoryCurrencies" :key="'quick-history-currency-' + entry.id">
                                <article class="rounded-2xl border border-[#d8c49e] bg-[#fffdf8]">
                                    <div class="flex items-center gap-3 p-3">
                                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl border border-[#d9bd7f] bg-[#f5ead1]">
                                            <span class="font-serif text-[19px] font-black text-[#8a5d19]">¤</span>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="truncate font-serif text-[14px] font-black text-[#53150f]" x-text="entry?.label || 'Moedas rápidas'"></h4>
                                                <span
                                                    class="rounded-full border px-2 py-1 text-[6px] font-black uppercase tracking-wider"
                                                    :class="{
                                                        'border-emerald-200 bg-emerald-50 text-emerald-800': lootQuickCurrencyStatusTone(entry) === 'is-live',
                                                        'border-[#d9c9b6] bg-[#f3ece3] text-[#806650]': lootQuickCurrencyStatusTone(entry) === 'is-withdrawn',
                                                        'border-[#d4c6b5] bg-[#eee8df] text-[#75665b]': lootQuickCurrencyStatusTone(entry) === 'is-depleted',
                                                        'border-amber-200 bg-amber-50 text-amber-800': lootQuickCurrencyStatusTone(entry) === 'is-hidden'
                                                    }"
                                                    x-text="lootQuickCurrencyStatusLabel(entry)"
                                                ></span>
                                            </div>

                                            <p class="mt-1 text-[8px] text-[#8b6e58]">
                                                <strong class="text-[#6b1d14]" x-text="lootCurrencyInline(entry, true)"></strong>
                                                <span> restante · </span>
                                                <span x-text="lootQuickCurrencyDistributionLabel(entry)"></span>
                                                <span x-show="lootQuickCurrencyClaimRows(entry).length > 0"> · </span>
                                                <span x-show="lootQuickCurrencyClaimRows(entry).length > 0" x-text="lootQuickCurrencyClaimRows(entry).length + ' retirada(s)'"></span>
                                            </p>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-1.5">
                                            <button
                                                x-show="entry.release_status !== 'released' && lootQuickCurrencyHasRemaining(entry)"
                                                x-cloak
                                                type="button"
                                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-[6px] font-black uppercase tracking-wider text-emerald-800 hover:bg-emerald-100 disabled:opacity-40"
                                                :disabled="lootQuickActingCurrencyId !== null"
                                                @click="releaseQuickExistingCurrency(entry)"
                                            >Disponibilizar</button>

                                            <button
                                                type="button"
                                                class="rounded-lg border border-[#d6c2a7] bg-white px-3 py-2 text-[6px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#f1e8db]"
                                                @click="toggleQuickCurrencyHistoryDetails(entry.id)"
                                            >Quem pegou</button>
                                        </div>
                                    </div>

                                    <div
                                        x-show="parseInt(lootQuickExpandedCurrencyId) === parseInt(entry.id)"
                                        x-cloak
                                        x-collapse
                                        class="border-t border-[#e5d9c8] bg-[#faf6ef] px-3 py-2.5"
                                    >
                                        <div x-show="lootQuickCurrencyClaimRows(entry).length > 0" class="space-y-1.5">
                                            <template x-for="claim in lootQuickCurrencyClaimRows(entry)" :key="'quick-history-currency-claim-' + claim.id">
                                                <div class="flex items-center justify-between gap-3 rounded-xl bg-white px-3 py-2">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-[8px] font-black text-[#53150f]" x-text="lootQuickCurrencyClaimCharacter(claim)?.name ?? 'Personagem'"></p>
                                                        <p class="mt-0.5 text-[7px] text-[#9b806b]">
                                                            <strong class="text-[#6b1d14]" x-text="lootCurrencyClaimInline(claim)"></strong>
                                                            <span x-show="lootClaimDate(claim.claimed_at)"> · </span>
                                                            <span x-text="lootClaimDate(claim.claimed_at)"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <p x-show="lootQuickCurrencyClaimRows(entry).length === 0" x-cloak class="py-2 text-center text-[8px] text-[#9a806d]">Nenhuma retirada registrada.</p>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    <div
                        x-show="lootQuickHistoryItems.length === 0 && lootQuickHistoryCurrencies.length === 0"
                        x-cloak
                        class="grid min-h-[280px] place-items-center rounded-2xl border border-dashed border-[#d2bea2] bg-[#fffdf8]/75 px-6 text-center"
                    >
                        <div>
                            <h4 class="font-serif text-[18px] font-black text-[#53150f]">Histórico vazio</h4>
                            <p class="mt-1 text-[9px] text-[#92755f]">Itens e moedas rápidas aparecerão aqui.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- ============================================================
             COMPOSER — ITEM / MOEDAS
        ============================================================= --}}
        <div
            x-show="lootQuickComposerOpen"
            x-cloak
            class="absolute inset-0 z-40 flex items-center justify-center bg-[#160d09]/45 p-3 backdrop-blur-[2px]"
            @click.self="closeQuickLootComposer()"
        >
            <section x-transition.opacity.scale.95 class="flex max-h-[90%] w-full max-w-[900px] flex-col overflow-hidden rounded-[20px] border border-[#cdbb9f] bg-[#f8f2e9] shadow-2xl">
                <header class="flex shrink-0 items-center justify-between gap-3 border-b border-[#d8c7ab] bg-[#fffdf8] px-4 py-3">
                    <div>
                        <p class="text-[7px] font-black uppercase tracking-[0.14em] text-[#a17e62]">Disponibilizar agora</p>
                        <h4
                            class="mt-0.5 font-serif text-[18px] font-black text-[#53150f]"
                            x-text="lootQuickComposerMode === 'currency' ? 'Moedas rápidas' : 'Novo item rápido'"
                        ></h4>
                    </div>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-lg text-[17px] font-black text-[#8c6239] hover:bg-[#efe4d5]" @click="closeQuickLootComposer()">×</button>
                </header>

                {{-- COMPOSER DE ITEM --}}
                <div
                    x-show="lootQuickComposerMode === 'item'"
                    x-cloak
                    class="grid min-h-0 flex-1 md:grid-cols-[minmax(0,1.1fr)_minmax(310px,.9fr)]"
                >
                    <section class="flex min-h-0 flex-col border-r border-[#ded0bd] bg-[#f4ede3]">
                        <div class="shrink-0 p-3">
                            <input
                                type="search"
                                x-model.debounce.180ms="lootQuickSearch"
                                placeholder="Buscar no Vault..."
                                class="h-10 w-full rounded-xl border border-[#d1bda0] bg-white px-3 text-[10px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                            >
                        </div>

                        <div class="min-h-0 flex-1 space-y-1.5 overflow-y-auto px-3 pb-3">
                            <template x-for="item in lootFilteredQuickVaultItems" :key="'quick-vault-item-' + item.id">
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-3 rounded-xl border p-2 text-left transition"
                                    :class="parseInt(lootQuickSelectedItem?.id) === parseInt(item.id) ? 'border-[#8a4b20] bg-[#fffdf8] shadow-sm' : 'border-[#dfd0bd] bg-white/70 hover:bg-white'"
                                    @click="selectQuickVaultItem(item)"
                                >
                                    <span class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-lg border border-[#ddcbb2] bg-[#efe6da]">
                                        <template x-if="item?.image_url"><img :src="item.image_url" :alt="item.name" class="h-full w-full object-contain p-1"></template>
                                        <template x-if="!item?.image_url"><span class="font-serif font-black text-[#aa8b70]">◇</span></template>
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <strong class="block truncate font-serif text-[12px] text-[#53150f]" x-text="item.name"></strong>
                                        <small class="mt-0.5 block truncate text-[7px] font-semibold text-[#93745e]" x-text="lootNatureLabel(item.type) + (item?.rarity ? ' · ' + lootRarityLabel(item.rarity) : '')"></small>
                                    </span>
                                </button>
                            </template>

                            <div x-show="lootFilteredQuickVaultItems.length === 0" x-cloak class="rounded-xl border border-dashed border-[#d6c2a7] px-4 py-8 text-center text-[8px] text-[#977b65]">Nenhum item encontrado.</div>
                        </div>
                    </section>

                    <aside class="min-h-0 overflow-y-auto bg-[#fffdf8] p-4">
                        <template x-if="lootQuickSelectedItem">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-14 w-14 shrink-0 place-items-center overflow-hidden rounded-xl border border-[#d8c6ad] bg-[#f0e6d9]">
                                        <template x-if="lootQuickSelectedItem?.image_url"><img :src="lootQuickSelectedItem.image_url" :alt="lootQuickSelectedItem.name" class="h-full w-full object-contain p-1.5"></template>
                                        <template x-if="!lootQuickSelectedItem?.image_url"><span class="font-serif text-lg font-black text-[#aa8b70]">◇</span></template>
                                    </span>
                                    <div class="min-w-0">
                                        <h5 class="truncate font-serif text-[16px] font-black text-[#53150f]" x-text="lootQuickSelectedItem.name"></h5>
                                        <p class="mt-1 text-[8px] font-semibold text-[#8b6e58]" x-text="lootNatureLabel(lootQuickSelectedItem.type) + (lootQuickSelectedItem?.rarity ? ' · ' + lootRarityLabel(lootQuickSelectedItem.rarity) : '')"></p>
                                    </div>
                                </div>

                                <label class="block">
                                    <span class="text-[7px] font-black uppercase tracking-wider text-[#9b7b63]">Quantidade</span>
                                    <input type="number" min="1" max="999999" x-model.number="lootQuickQuantity" class="mt-1.5 h-10 w-full rounded-xl border border-[#d1bda0] bg-white px-3 text-center font-serif text-[15px] font-black text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10">
                                </label>

                                <div>
                                    <span class="text-[7px] font-black uppercase tracking-wider text-[#9b7b63]">Entrega</span>
                                    <div class="mt-1.5 grid grid-cols-2 gap-1.5 rounded-xl border border-[#d7c4a8] bg-[#f1e8dc] p-1">
                                        <button type="button" class="rounded-lg px-2 py-2 text-[7px] font-black uppercase tracking-wider" :class="lootQuickVisibility === 'party' ? 'bg-white text-[#53150f] shadow-sm' : 'text-[#876b55]'" @click="lootQuickVisibility = 'party'; lootQuickCharacterIds = []">Party</button>
                                        <button type="button" class="rounded-lg px-2 py-2 text-[7px] font-black uppercase tracking-wider" :class="lootQuickVisibility === 'restricted' ? 'bg-white text-[#53150f] shadow-sm' : 'text-[#876b55]'" @click="lootQuickVisibility = 'restricted'">Personagens</button>
                                    </div>
                                </div>

                                <div x-show="lootQuickVisibility === 'restricted'" x-cloak>
                                    <div class="max-h-[180px] space-y-1.5 overflow-y-auto pr-1">
                                        <template x-for="character in lootCharacters" :key="'quick-recipient-' + character.id">
                                            <button type="button" class="flex w-full items-center gap-2 rounded-xl border px-2.5 py-2 text-left transition" :class="quickLootRecipientSelected(character.id) ? 'border-[#8a4b20] bg-[#f7eadb]' : 'border-[#ded0bd] bg-white'" @click="toggleQuickLootRecipient(character.id)">
                                                <span class="grid h-7 w-7 shrink-0 place-items-center overflow-hidden rounded-full bg-[#eadbc8] text-[7px] font-black text-[#6b1d14]">
                                                    <template x-if="character?.image_url"><img :src="character.image_url" :alt="character.name" class="h-full w-full object-cover"></template>
                                                    <template x-if="!character?.image_url"><span x-text="String(character.name ?? '?').slice(0,1).toUpperCase()"></span></template>
                                                </span>
                                                <span class="min-w-0 flex-1 truncate text-[8px] font-black text-[#53150f]" x-text="character.name"></span>
                                                <span class="grid h-5 w-5 place-items-center rounded-full border text-[8px]" :class="quickLootRecipientSelected(character.id) ? 'border-[#8a4b20] bg-[#8a4b20] text-white' : 'border-[#cdbb9f] text-transparent'">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="lootQuickError" x-cloak class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-[8px] font-bold text-red-800" x-text="lootQuickError"></div>

                                <button
                                    type="button"
                                    class="flex h-11 w-full items-center justify-center rounded-xl bg-[#6b1d14] text-[8px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f] disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="lootQuickReleasing"
                                    @click="releaseQuickLootItem()"
                                >
                                    <span x-show="!lootQuickReleasing">Disponibilizar item</span>
                                    <span x-show="lootQuickReleasing" x-cloak>Liberando...</span>
                                </button>
                            </div>
                        </template>

                        <div x-show="!lootQuickSelectedItem" x-cloak class="grid min-h-[260px] place-items-center text-center">
                            <div>
                                <span class="mx-auto grid h-11 w-11 place-items-center rounded-full bg-[#efe3d4] text-lg">◇</span>
                                <h5 class="mt-3 font-serif text-[16px] font-black text-[#53150f]">Escolha um item</h5>
                            </div>
                        </div>
                    </aside>
                </div>

                {{-- COMPOSER DE MOEDAS --}}
                <div
                    x-show="lootQuickComposerMode === 'currency'"
                    x-cloak
                    class="min-h-0 flex-1 overflow-y-auto bg-[#fffdf8] p-4 sm:p-5"
                >
                    <div class="mx-auto max-w-[780px] space-y-4">
                        <label class="block">
                            <span class="text-[7px] font-black uppercase tracking-wider text-[#9b7b63]">Nome opcional</span>
                            <input
                                type="text"
                                maxlength="120"
                                x-model="lootQuickCurrencyLabel"
                                placeholder="Ex.: Bolsa do mercenário"
                                class="mt-1.5 h-10 w-full rounded-xl border border-[#d1bda0] bg-white px-3 text-[10px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                            >
                        </label>

                        <div>
                            <span class="text-[7px] font-black uppercase tracking-wider text-[#9b7b63]">Moedas</span>
                            <div class="mt-1.5 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                <template x-for="coin in lootCoinDefinitions" :key="'quick-coin-' + coin.key">
                                    <label
                                        class="rounded-xl border bg-[#faf6ef] p-2.5 transition"
                                        :class="(parseInt(lootQuickCurrencyAmounts?.[coin.key]) || 0) > 0 ? 'border-[#b98b4d] bg-[#fff8e8]' : 'border-[#ddcdb7]'"
                                    >
                                        <span class="flex items-center justify-between gap-2">
                                            <strong class="text-[9px] font-black text-[#6b1d14]" x-text="coin.abbr"></strong>
                                            <small class="truncate text-[6px] font-bold uppercase tracking-wider text-[#9a7d65]" x-text="coin.label"></small>
                                        </span>
                                        <input
                                            type="number"
                                            min="0"
                                            max="999999999"
                                            x-model.number="lootQuickCurrencyAmounts[coin.key]"
                                            class="mt-2 h-10 w-full rounded-lg border border-[#d7c4a8] bg-white px-2 text-center font-serif text-[14px] font-black text-[#53150f] focus:border-[#8a4b20] focus:ring-[#8a4b20]/10"
                                        >
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div>
                            <span class="text-[7px] font-black uppercase tracking-wider text-[#9b7b63]">Entrega</span>
                            <div class="mt-1.5 grid grid-cols-3 gap-1.5 rounded-xl border border-[#d7c4a8] bg-[#f1e8dc] p-1">
                                <button
                                    type="button"
                                    class="rounded-lg px-2 py-2.5 text-[7px] font-black uppercase tracking-wider"
                                    :class="lootQuickCurrencyDistribution === 'open' ? 'bg-white text-[#53150f] shadow-sm' : 'text-[#876b55]'"
                                    @click="setQuickCurrencyDistribution('open')"
                                >Party</button>
                                <button
                                    type="button"
                                    class="rounded-lg px-2 py-2.5 text-[7px] font-black uppercase tracking-wider"
                                    :class="lootQuickCurrencyDistribution === 'split' ? 'bg-white text-[#53150f] shadow-sm' : 'text-[#876b55]'"
                                    @click="setQuickCurrencyDistribution('split')"
                                >Dividir</button>
                                <button
                                    type="button"
                                    class="rounded-lg px-2 py-2.5 text-[7px] font-black uppercase tracking-wider"
                                    :class="lootQuickCurrencyDistribution === 'first_claim' ? 'bg-white text-[#53150f] shadow-sm' : 'text-[#876b55]'"
                                    @click="setQuickCurrencyDistribution('first_claim')"
                                >Personagem</button>
                            </div>
                        </div>

                        <div x-show="lootQuickCurrencyDistribution !== 'open'" x-cloak>
                            <div class="grid max-h-[210px] gap-1.5 overflow-y-auto pr-1 sm:grid-cols-2">
                                <template x-for="character in lootCharacters" :key="'quick-currency-recipient-' + character.id">
                                    <button
                                        type="button"
                                        class="flex items-center gap-2 rounded-xl border px-2.5 py-2 text-left transition"
                                        :class="quickCurrencyRecipientSelected(character.id) ? 'border-[#8a4b20] bg-[#f7eadb]' : 'border-[#ded0bd] bg-white'"
                                        @click="toggleQuickCurrencyRecipient(character.id)"
                                    >
                                        <span class="grid h-7 w-7 shrink-0 place-items-center overflow-hidden rounded-full bg-[#eadbc8] text-[7px] font-black text-[#6b1d14]">
                                            <template x-if="character?.image_url"><img :src="character.image_url" :alt="character.name" class="h-full w-full object-cover"></template>
                                            <template x-if="!character?.image_url"><span x-text="String(character.name ?? '?').slice(0,1).toUpperCase()"></span></template>
                                        </span>
                                        <span class="min-w-0 flex-1 truncate text-[8px] font-black text-[#53150f]" x-text="character.name"></span>
                                        <span class="grid h-5 w-5 place-items-center rounded-full border text-[8px]" :class="quickCurrencyRecipientSelected(character.id) ? 'border-[#8a4b20] bg-[#8a4b20] text-white' : 'border-[#cdbb9f] text-transparent'">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div x-show="lootQuickError" x-cloak class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-[8px] font-bold text-red-800" x-text="lootQuickError"></div>

                        <div class="flex justify-end">
                            <button
                                type="button"
                                class="flex h-11 min-w-[210px] items-center justify-center rounded-xl bg-[#6b1d14] px-5 text-[8px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f] disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="lootQuickCurrencyReleasing"
                                @click="releaseQuickLootCurrency()"
                            >
                                <span x-show="!lootQuickCurrencyReleasing">Disponibilizar moedas</span>
                                <span x-show="lootQuickCurrencyReleasing" x-cloak>Liberando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
</div>