    {{-- V66.2.41 — Ajuste enxuto dos espaços; configuração mantida por personagem. --}}
    <template x-teleport="body">
        <div x-show="attunementSettingsOpen" x-cloak
             x-transition.opacity.duration.150ms
             x-on:keydown.escape.window="attunementSettingsOpen = false"
             class="character-sheet-theme character-theme-modal-layer inventory-v35-modal-layer inventory-v240-modal-layer"
             data-theme="{{ $characterTheme }}"
             id="inventory-v240-attunement-modal"
             role="dialog" aria-modal="true" aria-labelledby="inventory-v240-modal-title">
            <div class="character-theme-modal-backdrop inventory-v240-backdrop"
                 @click="attunementSettingsOpen = false" aria-hidden="true"></div>
            <div class="character-theme-modal inventory-v240-modal inventory-v241-modal" @click.stop>
                <header class="inventory-v240-modal-header">
                    <div class="inventory-v240-modal-heading">
                        <h3 id="inventory-v240-modal-title">Sintonização</h3>
                        <small>Configurar espaços</small>
                    </div>
                    <button type="button" class="inventory-v240-modal-close"
                            @click="attunementSettingsOpen = false" aria-label="Fechar configuração">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </header>
                <div class="inventory-v240-modal-content">
                    <div class="inventory-v240-space-control">
                        <button type="button" @click="setAttunementSlotLimit(attunementSlotLimit - 1)"
                                :disabled="attunementSlotLimit <= 0" aria-label="Diminuir um espaço">−</button>
                        <div class="inventory-v240-current-limit" aria-live="polite">
                            <strong x-text="attunementSlotLimit"></strong>
                            <span x-text="attunementSlotLimit === 1 ? 'espaço' : 'espaços'"></span>
                        </div>
                        <button type="button" @click="setAttunementSlotLimit(attunementSlotLimit + 1)"
                                :disabled="attunementSlotLimit >= 12" aria-label="Adicionar um espaço">+</button>
                    </div>
                    <p class="inventory-v240-modal-stats">
                        <span><strong x-text="attunedItems.length"></strong> sintonizados</span>
                        <span aria-hidden="true">·</span>
                        <span>Limite: 0–12</span>
                    </p>
                    <div x-show="attunedItems.length > attunementSlotLimit" x-cloak
                         class="inventory-v240-overflow-warning" role="status">
                        <strong>Vínculos acima do limite</strong>
                        <span>Os itens excedentes permanecem sintonizados.</span>
                    </div>
                </div>
                <footer class="inventory-v240-modal-footer">
                    <button type="button" class="inventory-v240-restore" @click="setAttunementSlotLimit(3)">Padrão: 3</button>
                    <button type="button" class="inventory-v240-done" @click="attunementSettingsOpen = false">Concluir</button>
                </footer>
            </div>
        </div>
    </template>

    {{-- =========================================================
         SELETOR DE SINTONIZAÇÃO
    ========================================================== --}}
    <template x-teleport="body">
        <div
            x-show="attunementPickerOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Escolher item para sintonização"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[245]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="attunementPickerOpen = false"
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-picker-modal

                    relative
                    z-10

                    w-full
                    max-w-[560px]
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
            >
                <div class="flex items-center justify-between border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-4 py-3">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Sintonização</p>
                        <h3 class="font-serif text-[19px] font-black text-[#53150f]">Escolher Item</h3>
                    </div>
                    <button type="button" @click="attunementPickerOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-xl text-[#8c6239] hover:bg-[#e8dfd1]">×</button>
                </div>

                <div class="max-h-[62vh] overflow-y-auto p-2">
                    <template x-for="item in eligibleAttunementItems" :key="'eligible-' + item.id">
                        <button
                            type="button"
                            @click="requestAttunement(item); if (!attunementWarningOpen) attunementPickerOpen = false"
                            class="flex w-full items-center gap-3 border-b border-[#d8c7ab]/45 px-2.5 py-3 text-left last:border-b-0 hover:bg-[#efe9dc]/45"
                        >
                            <div class="inventory-v10-thumb flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-[#d8c7ab]/55">
                                <template x-if="item.image_url"><img :src="item.image_url" :alt="item.name"></template>
                                <template x-if="!item.image_url"><span class="font-serif text-[17px] text-[#8c6239]/40">◆</span></template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate font-serif text-[15px] font-black text-[#53150f]" x-text="item.name"></strong>
                                <span class="mt-0.5 block truncate text-[10px] italic text-[#8c6239]" x-text="classificationLine(item)"></span>
                            </div>
                        </button>
                    </template>

                    <div x-show="eligibleAttunementItems.length === 0" x-cloak class="px-4 py-9 text-center">
                        <p class="font-serif text-[16px] font-black text-[#53150f]">Nenhum item disponível</p>
                        <p class="mt-1 text-[10px] text-[#8c6239]">Apenas Itens Maravilhosos sintonizáveis ainda não sintonizados aparecem aqui.</p>
                    </div>
                </div>
            </div>
        </div>
    </template>


    {{-- LIMITE DE 3 --}}
    <template x-teleport="body">
        <div
            x-show="attunementWarningOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Limite de sintonização"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[250]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="
                    attunementWarningOpen = false;
                    pendingAttunementItem = null
                "
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-warning-modal

                    relative
                    z-10

                    w-full
                    max-w-[500px]
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
            >
                <div class="border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-4 py-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Limite Padrão</p>
                    <h3 class="mt-0.5 font-serif text-[19px] font-black text-[#53150f]">Limite de sintonia alcançado</h3>
                </div>

                <div class="px-4 py-4 text-[12px] leading-relaxed text-[#6b5548]">
                    Você já possui <strong x-text="attunedItems.length"></strong> itens sintonizados. Seu limite configurado é de <strong x-text="attunementSlotLimit"></strong> espaços.
                    <p class="mt-2">Você pode ajustar o limite na engrenagem do painel ou, se o Mestre permitir, continuar excepcionalmente.</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/60 px-4 py-3">
                    <button type="button" @click="attunementWarningOpen = false; pendingAttunementItem = null" class="rounded-lg px-3 py-2 text-[9px] font-black uppercase text-[#8c6239]">Cancelar</button>
                    <button type="button" @click="confirmExtraAttunement()" class="rounded-lg bg-[#6b1d14] px-3 py-2 text-[9px] font-black uppercase text-[#f4f1e8]">Sintonizar Mesmo Assim</button>
                </div>
            </div>
        </div>
    </template>


    {{-- ITENS EQUIPADOS — LISTA SIMPLES --}}
    <template x-teleport="body">
        <div
            x-show="equippedDrawerOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Itens equipados"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[238]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="equippedDrawerOpen = false"
            ></div>

            <section
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-equipped-modal
                    inventory-v29-equipped-modal
                "
            >
                <header class="inventory-v29-equipped-header">
                    <div>
                        <span class="inventory-v29-equipped-kicker">
                            Em uso
                        </span>

                        <div class="inventory-v29-equipped-title-row">
                            <h3>Itens Equipados</h3>

                            <span
                                class="inventory-v29-equipped-count"
                                x-text="equippedItems.length"
                            ></span>
                        </div>
                    </div>

                    <span class="inventory-v29-equipped-hint">
                        Clique em um item para abrir
                    </span>
                </header>

                <div class="inventory-v29-equipped-list-wrap">
                    <template x-if="equippedItems.length === 0">
                        <div class="inventory-v29-equipped-empty">
                            <span>◇</span>
                            <strong>Nenhum item equipado</strong>
                            <p>Itens marcados como equipados aparecerão aqui.</p>
                        </div>
                    </template>

                    <div
                        x-show="equippedItems.length > 0"
                        x-cloak
                        class="inventory-v29-equipped-list"
                    >
                        <template
                            x-for="item in equippedItems"
                            :key="'equipped-simple-' + item.id"
                        >
                            <button
                                type="button"
                                class="inventory-v29-equipped-row inventory-v238-equipped-row"
                                :data-rarity="item.rarity || 'common'"
                                :data-nature="itemNature(item)"
                                @click="
                                    equippedDrawerOpen = false;
                                    openDetail(item)
                                "
                            >
                                <div class="inventory-v29-equipped-thumb">
                                    <template x-if="item.image_url">
                                        <img
                                            :src="item.image_url"
                                            :alt="item.name"
                                        >
                                    </template>

                                    <template x-if="!item.image_url">
                                        <span>◆</span>
                                    </template>
                                </div>

                                <div class="inventory-v29-equipped-copy">
                                    <div class="inventory-v238-equipped-name">
                                        <span class="inventory-v238-equipped-nature" :data-nature="itemNature(item)" aria-hidden="true">
                                            <span x-show="itemNature(item) === 'wonderful'">✦</span>
                                            <span x-show="itemNature(item) === 'technological'">⌁</span>
                                            <span x-show="itemNature(item) === 'mundane'">◇</span>
                                        </span>
                                        <strong x-text="item.name"></strong>
                                        <span x-show="item.rarity === 'artifact'" class="inventory-v238-equipped-rarity">Artefato</span>
                                        <span x-show="item.rarity === 'legendary'" class="inventory-v238-equipped-rarity">Lendário</span>
                                    </div>
                                    <span x-text="cardClassificationLine(item)"></span>
                                </div>

                                <span
                                    class="inventory-v29-equipped-chevron"
                                    aria-hidden="true"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="m9 6 6 6-6 6"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
            </section>
        </div>
    </template>


    {{-- ITENS ENCONTRADOS --}}
    <template x-teleport="body">
        <div
            x-show="foundItemsOpen"
            x-cloak
            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"
            data-theme="{{ $characterTheme }}"
            role="dialog"
            aria-modal="true"
            aria-label="Itens encontrados"
            class="character-sheet-theme character-theme-modal-layer inventory-v35-modal-layer fixed inset-0 z-[235] flex items-center justify-center p-4"
        >
            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="foundItemsOpen = false"
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"
                class="character-theme-modal inventory-v35-modal inventory-v35-found-modal relative z-10 w-full max-w-[680px] overflow-hidden rounded-2xl border shadow-2xl"
            >
                <div class="flex items-center justify-between gap-3 border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-4 py-3">
                    <div class="min-w-0">
                        <div class="inventory-v242-found-header-title">
                            <h3>Itens encontrados</h3>
                            <span x-show="foundLootLoaded" x-cloak class="inventory-v242-found-total" x-text="foundLootCount"></span>
                        </div>
                        <p class="inventory-v242-found-subtitle">Espólios liberados pelo Mestre</p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <button
                            type="button"
                            @click="loadFoundLoot()"
                            :disabled="foundLootLoading"
                            class="inventory-v242-found-refresh"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 11a8 8 0 0 0-14.4-4.9L4 8m0-5v5h5M4 13a8 8 0 0 0 14.4 4.9L20 16m0 5v-5h-5"/>
                            </svg>
                            <span x-text="foundLootLoading ? 'Atualizando...' : 'Atualizar'"></span>
                        </button>
                        <button
                            type="button"
                            @click="foundItemsOpen = false"
                            class="inventory-v242-found-close"
                            aria-label="Fechar itens encontrados"
                        ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                    </div>
                </div>

                <div class="max-h-[72vh] overflow-y-auto px-4 py-4">
                    <div
                        x-show="foundLootLoading && !foundLootLoaded"
                        x-cloak
                        class="px-5 py-12 text-center"
                    >
                        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-[#cdbb9f] border-t-[#6b1d14]"></div>
                        <p class="mt-3 text-[11px] font-bold text-[#8c6239]">Procurando espólios disponíveis...</p>
                    </div>

                    <div
                        x-show="foundLootError"
                        x-cloak
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 text-center"
                    >
                        <p class="text-[11px] font-bold text-red-700" x-text="foundLootError"></p>
                        <button
                            type="button"
                            class="mt-3 rounded-lg border border-red-200 bg-white px-3 py-2 text-[9px] font-black uppercase tracking-[0.05em] text-red-700"
                            @click="loadFoundLoot()"
                        >Tentar novamente</button>
                    </div>

                    <div
                        x-show="foundLootLoaded && !foundLootLoading && !foundLootError && !hasFoundLoot"
                        x-cloak
                        class="px-5 py-12 text-center"
                    >
                        <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full border border-[#cdbb9f]/65 bg-[#efe9dc]/55 font-serif text-[17px] text-[#8c6239]">?</div>
                        <p class="mt-3 font-serif text-[17px] font-black text-[#53150f]">Nenhum item disponível</p>
                        <p class="mx-auto mt-1.5 max-w-sm text-[11px] leading-relaxed text-[#8c6239]">
                            Quando o Mestre liberar um Espólio para este personagem, os itens e moedas aparecerão aqui.
                        </p>
                    </div>

                    <div
                        x-show="foundLootLoaded && !foundLootError && foundLootItems.length > 0"
                        x-cloak
                    >
                        <div class="inventory-v242-found-section-title">
                            <strong>Itens disponíveis</strong>
                            <span x-text="foundLootItems.length"></span>
                        </div>

                        <div class="inventory-v242-found-list">
                            <template x-for="item in foundLootItems" :key="'found-loot-item-' + item.id">
                                <article
                                    class="inventory-v50-found-item inventory-v242-found-card"
                                    :data-nature="item.type || (item.is_magical ? 'wonderful' : 'mundane')"
                                    :data-rarity="item.rarity || 'common'"
                                    :class="{ 'inventory-v50-found-artifact': isArtifact(item), 'inventory-v50-found-vestige': isVestige(item) }"
                                >
                                    <div class="inventory-v242-found-main">
                                        <div class="inventory-v242-found-thumb">
                                            <template x-if="item.image_url">
                                                <img :src="item.image_url" :alt="item.name" loading="lazy">
                                            </template>
                                            <template x-if="!item.image_url">
                                                <span class="inventory-v242-found-fallback" aria-hidden="true">✧</span>
                                            </template>
                                        </div>
                                        <div class="inventory-v242-found-copy">
                                            <div class="inventory-v242-found-topline">
                                                <div class="inventory-v242-found-heading">
                                                    <h4 class="inventory-v50-found-name inventory-v242-found-name" x-text="item.name"></h4>
                                                    <p class="inventory-v242-found-classification">
                                                        <span x-text="foundLootNatureLabel(item)"></span>
                                                        <span x-show="item.rarity_label"> · <span x-text="item.rarity_label"></span></span>
                                                        <span x-show="item.requires_attunement" x-cloak> (Sintonizável)</span>
                                                    </p>
                                                </div>
                                                <span class="inventory-v242-found-quantity" :aria-label="'Disponível: ' + item.quantity_remaining">
                                                    <span>Disponível</span>
                                                    <strong x-text="item.quantity_remaining"></strong>
                                                </span>
                                            </div>
                                            <div
                                                x-show="item.description" x-cloak
                                                class="inventory-v238-rich-text inventory-v242-found-description"
                                                x-html="formatItemRichText(item.description)"
                                            ></div>
                                        </div>
                                    </div>
                                    <div class="inventory-v242-found-footer">
                                        <div class="inventory-v242-found-meta">
                                            <span x-show="isVestige(item)" x-cloak class="inventory-v242-found-vestige">✦ Vestígio</span>
                                            <span x-show="item.campaign_name || item.drop_name" x-cloak class="inventory-v242-found-source">
                                                <span x-show="item.campaign_name" x-text="item.campaign_name"></span>
                                                <span x-show="item.campaign_name && item.drop_name"> · </span>
                                                <span x-show="item.drop_name" x-text="item.drop_name"></span>
                                            </span>
                                            <span x-show="item.weight !== null && item.weight > 0" x-cloak class="inventory-v242-found-weight">
                                                Peso: <span x-text="item.weight"></span>
                                            </span>
                                        </div>
                                        <button
                                            type="button"
                                            @click="claimFoundLootItem(item)"
                                            :disabled="foundLootBusyItemId !== null || foundLootBusyCurrencyId !== null"
                                            class="inventory-v50-found-claim inventory-v242-found-claim"
                                        >
                                            <span x-text="parseInt(foundLootBusyItemId) === parseInt(item.id) ? 'Adicionando...' : 'Adicionar ao inventário'"></span>
                                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14m-6-6 6 6-6 6"/>
                                            </svg>
                                        </button>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    <div
                        x-show="foundLootLoaded && !foundLootError && foundLootCurrencies.length > 0"
                        x-cloak
                        :class="{ 'mt-5': foundLootItems.length > 0 }"
                    >
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">Moedas</p>
                                <p class="mt-0.5 text-[10px] text-[#9a795b]">As moedas resgatadas entram diretamente na bolsa.</p>
                            </div>
                            <span class="rounded-full bg-[#efe9dc] px-2 py-1 font-serif text-[11px] font-black text-[#8c6239]" x-text="foundLootCurrencies.length"></span>
                        </div>

                        <div class="grid gap-2.5">
                            <template x-for="entry in foundLootCurrencies" :key="'found-loot-currency-' + entry.id">
                                <article class="rounded-2xl border border-[#cdbb9f]/65 bg-[#faf8f2] p-4">
                                    <div class="flex flex-col gap-4">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="font-serif text-[16px] font-black text-[#53150f]" x-text="entry.label || 'Moedas'"></h4>
                                                    <span
                                                        class="rounded-full border border-[#cdbb9f] bg-white px-2.5 py-1 text-[9px] font-black text-[#76553f]"
                                                        x-text="foundLootCurrencyModeLabel(entry)"
                                                    ></span>
                                                </div>

                                                <p
                                                    x-show="entry.campaign_name || entry.drop_name"
                                                    x-cloak
                                                    class="mt-1 text-[10px] text-[#9a795b]"
                                                >
                                                    <span x-show="entry.campaign_name" x-text="entry.campaign_name"></span>
                                                    <span x-show="entry.campaign_name && entry.drop_name"> · </span>
                                                    <span x-show="entry.drop_name" x-text="entry.drop_name"></span>
                                                </p>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="mb-2 text-[9px] font-black uppercase tracking-[0.12em] text-[#8c6239]">
                                                <span x-text="entry.distribution_mode === 'open' ? 'Disponível na caixa' : 'Valor disponível para você'"></span>
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="line in foundLootCoinLines(entry)" :key="entry.id + '-' + line.coin">
                                                    <span class="rounded-xl border border-[#d8c7ab]/70 bg-[#fffdf9] px-3 py-2 text-[10px] font-black text-[#6b5548]">
                                                        <span x-text="line.label"></span>
                                                        <span class="ml-1" x-text="formatMoney(line.amount)"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        <div
                                            x-show="entry.distribution_mode === 'open'"
                                            x-cloak
                                            class="rounded-xl border border-[#d8c7ab]/70 bg-white p-3"
                                        >
                                            <p class="text-[10px] font-bold leading-5 text-[#76553f]">
                                                Combine a divisão com a Party e informe apenas o que você vai retirar agora.
                                            </p>

                                            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                                <template x-for="line in foundLootCoinLines(entry)" :key="'claim-' + entry.id + '-' + line.coin">
                                                    <label>
                                                        <span class="mb-1 block text-[8px] font-black uppercase tracking-wider text-[#8c6239]" x-text="line.label"></span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            :max="line.amount"
                                                            x-model.number="foundLootCurrencyDrafts[entry.id][line.coin]"
                                                            class="h-10 w-full rounded-lg border border-[#cdbb9f] bg-[#fffdf9] px-2 text-[11px] font-black text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                                        >
                                                    </label>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="flex justify-end">
                                            <button
                                                type="button"
                                                @click="claimFoundLootCurrency(entry)"
                                                :disabled="foundLootBusyCurrencyId !== null || foundLootBusyItemId !== null"
                                                class="inline-flex min-h-[40px] items-center justify-center rounded-xl bg-[#6b1d14] px-4 text-[9px] font-black uppercase tracking-[0.05em] text-white transition hover:bg-[#53150f] disabled:cursor-wait disabled:opacity-60"
                                            >
                                                <span
                                                    x-text="parseInt(foundLootBusyCurrencyId) === parseInt(entry.id)
                                                        ? 'Adicionando...'
                                                        : (entry.distribution_mode === 'open' ? 'Retirar da caixa' : 'Adicionar à bolsa')"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </template>
