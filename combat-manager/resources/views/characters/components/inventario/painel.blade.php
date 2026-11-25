    {{-- V66.2.116 — O botão Equipar deve ficar acima da foto e dos efeitos. --}}
    <style>
        .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art > img,
        .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art::before,
        .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art::after,
        .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art .vault-v51-tech-energy,
        .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art .vault-v51-tech-circuits {
            pointer-events: none !important;
        }
        .character-sheet-theme[data-theme] .inventory-v35 .inventory-v48-art > .inventory-v49-equip {
            position: absolute !important;
            z-index: 50 !important;
            pointer-events: auto !important;
            cursor: pointer;
            isolation: isolate;
        }
    </style>
    {{-- V66.1.29 — aviso dourado de evolução, fora do drawer (teleport) --}}
    <template x-teleport="body">
        <div
            x-show="vestigeNotice !== null"
            x-cloak
            x-transition.opacity.duration.250ms
            class="inventory-v51-notice-layer"
            role="status"
            aria-live="polite"
            aria-atomic="true"
        >
            <div class="inventory-v51-notice" :key="vestigeNotice?.id">
                <div class="inventory-v51-notice-crest" aria-hidden="true">✦</div>
                <div class="inventory-v51-notice-copy">
                    <span>O poder do Vestígio evoluiu</span>
                    <strong x-text="vestigeNotice?.title || ''"></strong>
                    <small x-text="'Novo patamar: ' + (vestigeNotice?.stage || '')"></small>
                </div>
                <button
                    type="button"
                    class="inventory-v51-notice-dismiss"
                    @click="dismissVestigeNotice()"
                    aria-label="Fechar aviso de evolução"
                >×</button>
            </div>
        </div>
    </template>

    {{-- ============================================================
         TOPO — V14 SIMPLIFICADO
    ============================================================= --}}
    <div
        class="
            inventory-v14-toolbar
            inventory-v23-main-topbar
            inventory-v38-toolbar
        "
    >
        <div
            class="
                inventory-v14-actions
                inventory-v38-actions
            "
        >
            <button
                type="button"
                x-show="canCreateInventoryItems"
                x-cloak
                @click="$dispatch('inventory-vault-new-item')"
                class="inventory-v14-primary inventory-v38-primary-action"
            >
                <span class="inventory-v14-plus">+</span>
                <span>Novo Item</span>
            </button>

            <button
                type="button"
                @click="equippedDrawerOpen = true"
                class="inventory-v14-action inventory-v38-secondary-action"
            >
                <span>Equipados</span>
                <span class="inventory-v14-badge" x-text="equippedItems.length"></span>
            </button>

            <button
                type="button"
                @click="openFoundItems()"
                class="inventory-v14-action inventory-v14-action-muted inventory-v38-secondary-action"
            >
                <span>Encontrados</span>

                <span
                    x-show="foundLootLoaded && foundLootCount > 0"
                    x-cloak
                    class="inventory-v14-badge"
                    x-text="foundLootCount"
                ></span>
            </button>
        </div>

        <div class="inventory-v238-overview-wallet inventory-v239-toolbar-wallet">
            {{-- Bolsa compacta --}}
            <div
                class="inventory-v14-wallet-wrap inventory-v38-wallet-wrap"
                @click.outside="walletOpen = false"
            >
                <button
                    type="button"
                    @click="walletOpen = !walletOpen"
                    class="inventory-v14-wallet-trigger inventory-v38-wallet-trigger"
                    :aria-expanded="walletOpen"
                    aria-label="Abrir bolsa de moedas"
                >
                    <span class="inventory-v14-pouch" aria-hidden="true">
                        <svg viewBox="0 0 32 32" fill="none">
                            <path d="M11 5.5h10l-2.2 4.1c4.5 2.1 7.2 6.1 7.2 10.2 0 5.1-4.2 7.7-10 7.7S6 24.9 6 19.8c0-4.1 2.7-8.1 7.2-10.2L11 5.5Z" />
                            <path d="M10.2 10.2h11.6M9.2 13.1c4.3 1 9.3 1 13.6 0" />
                            <circle cx="13" cy="18" r="2.25" />
                            <circle cx="18.5" cy="18.8" r="2.25" />
                            <circle cx="15.8" cy="22.1" r="2.25" />
                        </svg>
                    </span>

                    <span class="inventory-v14-wallet-copy">
                        <span class="inventory-v14-wallet-label">Bolsa de moedas</span>

                        <span class="inventory-v38-wallet-total-line">
                            <strong x-text="formatMoney(walletTotalCoins)"></strong>
                            <small>moedas</small>
                        </span>
                    </span>

                    <svg
                        class="inventory-v14-wallet-chevron"
                        :class="{ 'is-open': walletOpen }"
                        viewBox="0 0 20 20"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path d="M5 7.5 10 12.5 15 7.5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div
                    x-show="walletOpen"
                    x-cloak
                    x-transition.opacity.duration.120ms
                    class="inventory-v14-wallet-panel inventory-v38-wallet-panel"
                >
                    <div
                        class="
                            inventory-v14-wallet-panel-head
                            inventory-v38-wallet-panel-head
                        "
                    >
                        <div>


                            <strong>Bolsa de moedas</strong>

                        </div>

                        <span
                            x-show="walletSaving"
                            x-cloak
                            class="
                                inventory-v14-saving
                                inventory-v38-wallet-saving
                            "
                        >
                            Salvando…
                        </span>
                    </div>

                    <div class="inventory-v38-wallet-summary">
                        <div>
                            <span>Total na bolsa</span>

                            <strong
                                x-text="
                                    formatMoney(
                                        walletTotalCoins
                                    )
                                "
                            ></strong>
                        </div>

                        <span class="inventory-v38-wallet-summary-note">
                            moedas
                        </span>
                    </div>

                    <div
                        class="
                            inventory-v14-coins
                            inventory-v38-coins
                        "
                    >
                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-copper" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="M12 7.3v9.4M8.8 9.5h6.4M8.8 14.5h6.4"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PC</b><small>Cobre</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.copper)"
                                @input="handleMoneyInput('copper', $event)"
                                @blur="$event.target.value = formatMoney(wallet.copper)"
                                aria-label="Peças de Cobre"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-silver" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PP</b><small>Prata</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.silver)"
                                @input="handleMoneyInput('silver', $event)"
                                @blur="$event.target.value = formatMoney(wallet.silver)"
                                aria-label="Peças de Prata"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-electrum" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="M8.3 12h7.4M12 8.3v7.4"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PE</b><small>Electro</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.electrum)"
                                @input="handleMoneyInput('electrum', $event)"
                                @blur="$event.target.value = formatMoney(wallet.electrum)"
                                aria-label="Peças de Electro"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card">
                            <span class="inventory-v14-coin-icon coin-gold" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PO</b><small>Ouro</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.gold)"
                                @input="handleMoneyInput('gold', $event)"
                                @blur="$event.target.value = formatMoney(wallet.gold)"
                                aria-label="Peças de Ouro"
                            >
                        </label>

                        <label class="inventory-v14-coin-row inventory-v38-coin-card inventory-v38-coin-card--platinum">
                            <span class="inventory-v14-coin-icon coin-platinum" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25"/>
                                    <path d="M8.5 8.5h7v7h-7zM10.5 6.5h3M10.5 17.5h3"/>
                                </svg>
                            </span>
                            <span class="inventory-v14-coin-name"><b>PL</b><small>Platina</small></span>
                            <input
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                :value="formatMoney(wallet.platinum)"
                                @input="handleMoneyInput('platinum', $event)"
                                @blur="$event.target.value = formatMoney(wallet.platinum)"
                                aria-label="Peças de Platina"
                            >
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================
         SINTONIZAÇÃO — V14
    ============================================================= --}}
    <section
        class="
            inventory-v14-attunement
            inventory-v23-attunement-panel
            inventory-v36-attunement-shell
        "
    >
        <div
            class="
                inventory-v14-section-head
                inventory-v36-attunement-head
            "
        >
            <div
                class="
                    inventory-v14-section-title
                    inventory-v36-attunement-title
                "
            >
                <span
                    class="
                        inventory-v14-diamond
                        inventory-v36-attunement-icon
                    "
                >
                    ✦
                </span>

                <div class="inventory-v36-attunement-copy">
                    <div
                        class="
                            inventory-v14-title-line
                            inventory-v36-attunement-line
                        "
                    >
                        <button type="button"
                                class="inventory-v239-attune-title-button"
                                @click="attunementSettingsOpen = true"
                                :aria-expanded="String(attunementSettingsOpen)"
                                aria-haspopup="dialog"
                                aria-controls="inventory-v240-attunement-modal"
                                aria-label="Sintonização — configurar quantidade de espaços"
                                title="Configurar espaços de sintonização">
                            <span>Sintonização</span>
                            <span class="inventory-v240-gear" aria-hidden="true">
                                {{-- Ícone clássico de engrenagem, traçado no padrão Lucide. --}}
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.09a2 2 0 0 1 1 1.73v.51a2 2 0 0 1-1 1.73l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.73v-.51a2 2 0 0 1 1-1.73l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </span>
                        </button>

                    </div>

                    
                </div>
            </div>

            <div class="inventory-v238-attune-actions">
                <button type="button" @click="attunementPickerOpen = true"
                        class="inventory-v14-text-action inventory-v36-attunement-action">Sintonizar</button>
            </div>
        </div>

        <div
            class="
                inventory-v14-slots
                inventory-v36-attunement-slots
            "
        >
            <template
                x-for="(slot, index) in primaryAttunementSlots"
                :key="'slot-' + index"
            >
                <button
                    type="button"
                    @click="
                        slot
                            ? openDetail(slot)
                            : attunementPickerOpen = true
                    "
                    class="
                        inventory-v14-slot
                        inventory-v36-attunement-slot
                    "
                    :class="{ 'is-filled': !!slot, 'is-empty': !slot, 'inventory-v238-slot-vestige': slot && isVestige(slot) }"
                    :data-rarity="slot?.rarity || 'empty'"
                    :data-nature="slot ? itemNature(slot) : 'none'"
                >
                    <template x-if="slot">
                        <div
                            class="
                                inventory-v14-slot-content
                                inventory-v36-slot-content
                            "
                        >
                            <div
                                class="
                                    inventory-v10-thumb
                                    inventory-v14-slot-image
                                    inventory-v36-slot-image
                                "
                            >
                                <template x-if="slot.image_url">
                                    <img :src="slot.image_url" :alt="slot.name">
                                </template>

                                <template x-if="!slot.image_url">
                                    <span>◆</span>
                                </template>

                            </div>

                            <div
                                class="
                                    inventory-v14-slot-copy
                                    inventory-v36-slot-copy
                                "
                            >
                                <div class="inventory-v36-slot-meta">
                                    <small>
                                        Espaço
                                        <span x-text="index + 1"></span>
                                    </small>

                                </div>

                                <strong x-text="slot.name"></strong>

                                <span class="inventory-v241-slot-kind" :data-rarity="slot.rarity" :data-nature="itemNature(slot)">
                                    <span x-show="slot.rarity === 'artifact'" aria-hidden="true" class="inventory-v241-kind-spark">✦</span>
                                    <span x-text="rarityLabel(slot)"></span>
                                </span>
                            </div>
                        </div>
                    </template>

                    <template x-if="!slot">
                        <div
                            class="
                                inventory-v14-slot-empty
                                inventory-v36-slot-empty
                            "
                        >
                            <span class="inventory-v14-slot-plus">+</span>

                            <div class="inventory-v36-slot-empty-copy">
                                <strong>Espaço livre</strong>
                                <small>Sintonizar item</small>
                            </div>
                        </div>
                    </template>
                </button>
            </template>
        </div>

        <div x-show="attunementSlotLimit === 0" x-cloak class="inventory-v240-no-slots">
            Nenhum espaço disponível. Clique em <strong>Sintonização</strong> para configurar.
        </div>

        <div
            x-show="extraAttunedItems.length > 0"
            x-cloak
            class="
                inventory-v14-extra-attuned
                inventory-v36-extra-attuned
            "
        >
            <span>Vínculos acima do limite</span>
            <small>Estes itens permanecem sintonizados, mas excedem os espaços disponíveis.</small>

            <template x-for="item in extraAttunedItems" :key="'extra-' + item.id">
                <button
                    type="button"
                    @click="openDetail(item)"
                    x-text="item.name"
                ></button>
            </template>
        </div>
    </section>

    {{-- LISTA --}}
    <div class="inventory-v23-list">
        <template x-for="group in inventoryGroups" :key="group.key">
            <section
                class="
                    inventory-v23-group-panel
                    inventory-v24-group-drawer
                "
                :class="{
                    'is-open':
                        isInventoryGroupExpanded(
                            group.key
                        ),

                    'is-collapsed':
                        !isInventoryGroupExpanded(
                            group.key
                        )
                }"
            >
                <button
                    type="button"
                    @click="
                        toggleInventoryGroup(
                            group.key
                        )
                    "
                    class="
                        inventory-v14-group-head
                        inventory-v24-group-trigger
                    "
                    :aria-expanded="
                        isInventoryGroupExpanded(
                            group.key
                        )
                            ? 'true'
                            : 'false'
                    "
                >
                    <div class="inventory-v24-group-copy inventory-v238-group-copy">
                        <span class="inventory-v238-nature-icon" :data-nature="group.key" aria-hidden="true">
                            <svg x-show="group.key === 'wonderful'" x-cloak viewBox="0 0 24 24" fill="none"><path d="m12 1.5 2.7 7.8 7.8 2.7-7.8 2.7-2.7 7.8-2.7-7.8-7.8-2.7 7.8-2.7z" fill="currentColor"/><path d="m19.8 2 .7 2.3L23 5l-2.5.7-.7 2.3-.7-2.3L16.6 5l2.5-.7z" fill="currentColor"/></svg>
                            <img x-show="group.key === 'technological'" x-cloak
                                 src="{{ asset('images/vault/icons/technological.png') }}"
                                 alt="" class="inventory-v239-technological-icon" loading="lazy" decoding="async">
                            <svg x-show="group.key === 'mundane'" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="14" height="17" rx="4"/><path d="M9 5V4a3 3 0 0 1 6 0v1M5 10H3v9h2M19 10h2v9h-2M8 13h8v6H8z"/></svg>
                        </span>
                        <div class="inventory-v238-group-title">
                            <h3 x-text="group.title"></h3>
                            <p x-text="group.subtitle"></p>
                        </div>
                    </div>

                    <div class="inventory-v24-group-meta">
                        <span
                            class="inventory-v14-group-count"
                            x-text="
                                groupSearchActive(
                                    group.key
                                )
                                    ? (
                                        group.items.length
                                        + '/'
                                        + group.total
                                    )
                                    : group.total
                            "
                        ></span>

                        <span
                            class="inventory-v24-group-chevron"
                            :class="{
                                'is-open':
                                    isInventoryGroupOpen(
                                        group.key
                                    )
                            }"
                            aria-hidden="true"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    d="m8 10 4 4 4-4"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>
                    </div>
                </button>

                <div
                    x-show="
                        isInventoryGroupExpanded(
                            group.key
                        )
                    "
                    x-transition.opacity.duration.120ms
                    class="inventory-v24-group-content"
                >
                    <div class="inventory-v26-group-tools">
                        <div class="inventory-v26-group-search">
                            <span
                                class="inventory-v26-group-search-icon"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <circle
                                        cx="11"
                                        cy="11"
                                        r="6.5"
                                        stroke-width="1.7"
                                    />

                                    <path
                                        d="m16 16 4 4"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <input
                                type="search"
                                x-model.debounce.120ms="
                                    inventoryGroupSearch[
                                        group.key
                                    ]
                                "
                                class="inventory-v26-group-search-input"
                                :placeholder="
                                    'Pesquisar em '
                                    + group.title.toLocaleLowerCase(
                                        'pt-BR'
                                    )
                                    + '...'
                                "
                                :aria-label="
                                    'Pesquisar em '
                                    + group.title
                                "
                            >

                            <button
                                x-show="
                                    inventoryGroupSearch[
                                        group.key
                                    ]
                                "
                                x-cloak
                                type="button"
                                @click="
                                    inventoryGroupSearch[
                                        group.key
                                    ] = ''
                                "
                                class="inventory-v26-group-search-clear"
                                title="Limpar pesquisa"
                            >
                                ×
                            </button>
                        </div>

                    </div>

                    <div
                        x-show="group.items.length === 0"
                        x-cloak
                        class="inventory-v26-group-empty"
                    >
                        <span>◇</span>

                        <strong>
                            Nenhum item encontrado
                        </strong>

                        <button
                            type="button"
                            @click="
                                inventoryGroupSearch[
                                    group.key
                                ] = ''
                            "
                        >
                            Limpar pesquisa
                        </button>
                    </div>

                    <div
                        x-show="group.items.length > 0"
                        x-cloak
                        class="
                            inventory-v16-items-grid
                            inventory-v26-items-grid
                            inventory-v39-items-grid
                        "
                    >
                    <template x-for="item in group.items" :key="group.key + '-' + item.id">
                        <article
                            class="inventory-v48-item-card cursor-pointer"
                            :data-nature="item.type || 'mundane'"
                            :data-rarity="item.rarity || 'common'"
                            :class="{
                                'inventory-v43-item-used': singleUseItemUsed(item),
                                'inventory-v105-discard-packed': singleUseItemUsed(item) && cardVisibleFeatureEntries(item).length > 1,
                                'inventory-v50-artifact': isArtifact(item),
                                'inventory-v50-vestige': isVestige(item),
                                'inventory-v51-ascending': !!vestigeGlowingItemIds[String(item.id)]
                            }"
                            role="group"
                            tabindex="0"
                            :aria-label="'Ver detalhes de ' + (item.name || 'item')"
                            @click="openDetail(item)"
                            @keydown.enter.self.prevent="openDetail(item)"
                            @keydown.space.self.prevent="openDetail(item)"
                        >
                            {{-- Reflexo V79 original do Cofre, só para Artefatos Maravilhosos. --}}
                            <span x-show="item.type === 'wonderful' && item.rarity === 'artifact' && !singleUseItemUsed(item)"
                                  x-cloak class="inventory-v203-liquid-gold" aria-hidden="true"></span>
                            {{-- V66.2.27: energia nasce na borda, alcança uma junção,
                                 acende um nó e se divide em dois trajetos discretos.
                                 O circuito do retrato continua independente. --}}
                            <template x-if="item.type === 'technological' && item.rarity === 'artifact' && !singleUseItemUsed(item)">
                                <span class="inventory-v217-tech-wave" x-data="window.SpellboundTechCircuits()" x-init="start($el)" aria-hidden="true">
                                    <svg viewBox="0 0 320 240" preserveAspectRatio="none" focusable="false" aria-hidden="true">
                                        {{-- Bifurcação superior: a luz vem da direita e se reparte. --}}
                                        <g data-tech-branch="upper">
                                        <path class="inventory-v227-trace inventory-v227-stem" pathLength="100" style="--inv227-cycle:16.8s;--inv227-delay:.55s;--inv227-peak:.51" d="M336 36 H294 L277 49 H258 L239 64 H220"/>
                                        <path class="inventory-v227-head inventory-v227-stem" pathLength="100" style="--inv227-cycle:16.8s;--inv227-delay:.55s" d="M336 36 H294 L277 49 H258 L239 64 H220"/>
                                        <path class="inventory-v227-trace inventory-v227-arm" pathLength="100" style="--inv227-cycle:16.8s;--inv227-delay:.55s;--inv227-peak:.44" d="M220 64 H202 L184 80 H160"/>
                                        <path class="inventory-v227-trace inventory-v227-arm inventory-v227-quiet" pathLength="100" style="--inv227-cycle:16.8s;--inv227-delay:.55s;--inv227-peak:.35" d="M220 64 H241 L259 80 H282"/>
                                        <circle class="inventory-v227-node" style="--inv227-cycle:16.8s;--inv227-delay:.55s" cx="220" cy="64" r="1.6"/>
                                        </g>

                                        {{-- Bifurcação inferior: duas saídas após um tronco comum. --}}
                                        <g data-tech-branch="lower">
                                        <path class="inventory-v227-trace inventory-v227-stem" pathLength="100" style="--inv227-cycle:18.6s;--inv227-delay:2.5s;--inv227-peak:.49" d="M-12 158 H28 L47 175 H81 L98 190 H122"/>
                                        <path class="inventory-v227-head inventory-v227-stem" pathLength="100" style="--inv227-cycle:18.6s;--inv227-delay:2.5s" d="M-12 158 H28 L47 175 H81 L98 190 H122"/>
                                        <path class="inventory-v227-trace inventory-v227-arm" pathLength="100" style="--inv227-cycle:18.6s;--inv227-delay:2.5s;--inv227-peak:.43" d="M122 190 H144 L163 176 H185"/>
                                        <path class="inventory-v227-trace inventory-v227-arm inventory-v227-quiet" pathLength="100" style="--inv227-cycle:18.6s;--inv227-delay:2.5s;--inv227-peak:.34" d="M122 190 H145 L162 207 H190"/>
                                        <circle class="inventory-v227-node" style="--inv227-cycle:18.6s;--inv227-delay:2.5s" cx="122" cy="190" r="1.6"/>
                                        </g>

                                        {{-- V66.2.28: terceira bifurcação, mais discreta, que nasce
                                             na lateral direita e se divide perto do centro. --}}
                                        <g data-tech-branch="middle">
                                        <path class="inventory-v227-trace inventory-v227-stem" pathLength="100" style="--inv227-cycle:20.1s;--inv227-delay:4.15s;--inv227-peak:.43" d="M337 156 H303 L287 169 H262 L244 154 H222"/>
                                        <path class="inventory-v227-head inventory-v227-stem" pathLength="100" style="--inv227-cycle:20.1s;--inv227-delay:4.15s" d="M337 156 H303 L287 169 H262 L244 154 H222"/>
                                        <path class="inventory-v227-trace inventory-v227-arm" pathLength="100" style="--inv227-cycle:20.1s;--inv227-delay:4.15s;--inv227-peak:.36" d="M222 154 H202 L186 141 H163"/>
                                        <path class="inventory-v227-trace inventory-v227-arm inventory-v227-quiet" pathLength="100" style="--inv227-cycle:20.1s;--inv227-delay:4.15s;--inv227-peak:.29" d="M222 154 H203 L186 174 H161"/>
                                        <circle class="inventory-v227-node" style="--inv227-cycle:20.1s;--inv227-delay:4.15s" cx="222" cy="154" r="1.45"/>
                                        </g>

                                        {{-- Trilhas avulsas com tempos distintos e baixa intensidade. --}}
                                        <path class="inventory-v227-trace inventory-v227-roam" pathLength="100" style="--inv227-cycle:17.4s;--inv227-delay:1.9s;--inv227-peak:.37" d="M335 110 H303 L286 124 H264 L245 137 H220 L202 122 H183"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-quiet" pathLength="100" style="--inv227-cycle:19.3s;--inv227-delay:3.65s;--inv227-peak:.35" d="M-12 132 H32 L49 146 H79 L97 158 H130"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-offset" pathLength="100" style="--inv227-cycle:15.7s;--inv227-delay:4.4s;--inv227-peak:.40" d="M334 215 H303 L285 199 H262 L245 183 H218"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-offset inventory-v227-quiet" pathLength="100" style="--inv227-cycle:20.2s;--inv227-delay:1.15s;--inv227-peak:.33" d="M112 114 H147 L166 128 H200 L221 110 H251"/>
                                        {{-- Duas passagens curtas extras, desencontradas e quase transparentes. --}}
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-quiet" pathLength="100" style="--inv227-cycle:21.3s;--inv227-delay:5.15s;--inv227-peak:.29" d="M110 95 H143 L159 108 H182 L201 92 H227"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-offset inventory-v227-quiet" pathLength="100" style="--inv227-cycle:18.2s;--inv227-delay:6.05s;--inv227-peak:.31" d="M-12 223 H32 L49 207 H74 L94 222 H125"/>
                                        {{-- Duas trilhas adicionais, bem suaves, para dar um pouco mais de vida sem poluir. --}}
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-quiet" pathLength="100" style="--inv227-cycle:16.2s;--inv227-delay:2.85s;--inv227-peak:.30" d="M335 86 H305 L288 98 H268 L249 112 H228"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-offset inventory-v227-quiet" pathLength="100" style="--inv227-cycle:22.4s;--inv227-delay:7.1s;--inv227-peak:.28" d="M98 206 H132 L149 192 H178 L196 206 H222 L241 189 H266"/>
                                        {{-- V66.2.30: mais variação e mais presença, mantendo elegância. --}}
                                        <g data-tech-branch="fourth">
                                        <path class="inventory-v227-trace inventory-v227-stem inventory-v227-quiet" pathLength="100" style="--inv227-cycle:23.2s;--inv227-delay:6.45s;--inv227-peak:.34" d="M336 126 H309 L291 140 H270 L250 154 H232"/>
                                        <path class="inventory-v227-head inventory-v227-stem" pathLength="100" style="--inv227-cycle:23.2s;--inv227-delay:6.45s" d="M336 126 H309 L291 140 H270 L250 154 H232"/>
                                        <path class="inventory-v227-trace inventory-v227-arm inventory-v227-quiet" pathLength="100" style="--inv227-cycle:23.2s;--inv227-delay:6.45s;--inv227-peak:.31" d="M232 154 H212 L194 167 H173"/>
                                        <path class="inventory-v227-trace inventory-v227-arm inventory-v227-quiet" pathLength="100" style="--inv227-cycle:23.2s;--inv227-delay:6.45s;--inv227-peak:.27" d="M232 154 H251 L270 170 H291"/>
                                        <circle class="inventory-v227-node" style="--inv227-cycle:23.2s;--inv227-delay:6.45s" cx="232" cy="154" r="1.35"/>
                                        </g>

                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-quiet" pathLength="100" style="--inv227-cycle:14.9s;--inv227-delay:.95s;--inv227-peak:.33" d="M334 58 H314 L298 71 H281 L264 85 H245"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-offset inventory-v227-quiet" pathLength="100" style="--inv227-cycle:18.9s;--inv227-delay:2.2s;--inv227-peak:.30" d="M-12 177 H18 L37 191 H62 L79 205 H102"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-quiet" pathLength="100" style="--inv227-cycle:16.7s;--inv227-delay:3.3s;--inv227-peak:.34" d="M132 146 H157 L175 160 H201 L218 146 H242 L260 132 H283"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-offset inventory-v227-quiet" pathLength="100" style="--inv227-cycle:24.1s;--inv227-delay:4.95s;--inv227-peak:.27" d="M334 194 H309 L292 181 H271 L251 196 H229 L211 212 H191"/>
                                        <path class="inventory-v227-trace inventory-v227-roam inventory-v227-quiet" pathLength="100" style="--inv227-cycle:19.8s;--inv227-delay:5.9s;--inv227-peak:.29" d="M86 223 H113 L131 208 H154 L172 223 H197"/>

                                        {{-- V66.2.31: duas novas bifurcações ocasionais, com destinos distintos. --}}
                                        <g data-tech-branch="fifth">
                                            <path class="inventory-v227-trace inventory-v227-stem" pathLength="100" d="M335 96 H306 L290 109 H264 L248 121 H227"/>
                                            <path class="inventory-v227-head inventory-v227-stem" pathLength="100" d="M335 96 H306 L290 109 H264 L248 121 H227"/>
                                            <path class="inventory-v227-trace inventory-v227-arm" pathLength="100" d="M227 121 H208 L191 108 H171"/>
                                            <path class="inventory-v227-trace inventory-v227-arm inventory-v227-quiet" pathLength="100" d="M227 121 H208 L192 139 H170"/>
                                            <circle class="inventory-v227-node" cx="227" cy="121" r="1.5"/>
                                        </g>
                                        <g data-tech-branch="sixth">
                                            <path class="inventory-v227-trace inventory-v227-stem inventory-v227-quiet" pathLength="100" d="M-12 217 H28 L44 202 H72 L89 186 H112"/>
                                            <path class="inventory-v227-head inventory-v227-stem" pathLength="100" d="M-12 217 H28 L44 202 H72 L89 186 H112"/>
                                            <path class="inventory-v227-trace inventory-v227-arm" pathLength="100" d="M112 186 H134 L150 172 H172"/>
                                            <path class="inventory-v227-trace inventory-v227-arm inventory-v227-quiet" pathLength="100" d="M112 186 H133 L151 200 H176"/>
                                            <circle class="inventory-v227-node" cx="112" cy="186" r="1.4"/>
                                        </g>
                                        <g class="inventory-v231-sparks" aria-hidden="true"></g>
                                    </svg>
                                </span>
                            </template>
                            <div class="inventory-v48-hero"
                                 :class="{ 'inventory-v89-hero-complex': !!cardTechnicalLine(item) }">
                                <div class="inventory-v48-art-col">
                                    <div class="inventory-v48-art">
                                        <template x-if="item.type === 'technological' && item.rarity === 'artifact'">
                                    <span class="vault-v51-tech-energy" aria-hidden="true">
                                        <svg class="vault-v51-tech-circuits" viewBox="0 0 160 230" preserveAspectRatio="none">
                                            <path class="vault-v51-trace" d="M-8 48 H45 L65 68 H108 L122 51 H170" />
                                            <path class="vault-v51-trace" d="M169 177 H114 L98 156 H49 L33 180 H-8" />
                                            <path class="vault-v51-trace" d="M-8 110 H27 L44 94 H97 L112 111 H170" />
                                            <path class="vault-v51-trace" d="M160 20 H129 L108 39 H86 M1 202 H38 L58 188 H91" />
                                        </svg>
                                        <i class="vault-v51-spark"></i><i class="vault-v51-spark"></i><i class="vault-v51-spark"></i><i class="vault-v51-spark"></i>
                                        <i class="vault-v51-spark"></i><i class="vault-v51-spark"></i><i class="vault-v51-spark"></i><i class="vault-v51-spark"></i>
                                        <i class="vault-v51-spark"></i><i class="vault-v51-spark"></i><i class="vault-v51-spark"></i><i class="vault-v51-spark"></i>
                                    </span>
                                </template>
                                        <span
                                            x-show="singleUseItemUsed(item)"
                                            x-cloak
                                            class="inventory-v43-used-badge"
                                        >Usado</span>
                                        <template x-if="item.image_url">
                                            <img :src="item.image_url" :alt="item.name" loading="lazy">
                                        </template>
                                        <template x-if="!item.image_url">
                                            <span class="font-serif text-2xl" style="color:var(--theme-text-secondary)">◆</span>
                                        </template>
                                        {{-- O status de equipamento permanece acessível sem criar outra linha. --}}
                                        <button
                                            x-show="item?.properties?.inventory?.equippable"
                                            x-cloak
                                            type="button"
                                            class="inventory-v49-equip"
                                            :class="{ 'is-equipped': item.equipped }"
                                            :disabled="busyItemId !== null"
                                            :aria-busy="Number(busyItemId) === Number(item.id) ? 'true' : 'false'"
                                            :aria-pressed="item.equipped ? 'true' : 'false'"
                                            :aria-label="item.equipped ? 'Equipado. Guardar ' + item.name : 'Equipar ' + item.name"
                                            :title="item.equipped ? 'Equipado · clique para guardar' : 'Clique para equipar'"
                                            x-on:pointerdown.stop
                                            x-on:mousedown.stop
                                            x-on:touchstart.stop
                                            x-on:click.stop.prevent="
                                                !item.equipped && ['simple','martial','custom'].includes(item?.properties?.weapon?.category)
                                                ? window.dispatchEvent(new CustomEvent('spellbound-weapon-equip-request', {
                                                    detail: {item}
                                                  }))
                                                : toggleEquipped(item)
                                            "
                                        >
                                            <span
                                                aria-hidden="true"
                                                class="inventory-v49-equip-icon"
                                                :class="item.equipped ? 'is-equipped' : 'is-unequipped'"
                                            ></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="inventory-v48-identity">
                                    <h4 class="inventory-v48-name" :title="item.name" x-text="item.name"></h4>
                                    <p
                                        class="inventory-v48-classification"
                                        :title="classificationLine(item)"
                                        x-text="classificationLine(item)"
                                    ></p>
                                    <p
                                        x-show="cardTechnicalLine(item)"
                                        x-cloak
                                        class="inventory-v48-technical"
                                        :title="cardTechnicalLine(item)"
                                        x-text="cardTechnicalLine(item)"
                                    ></p>
                                </div>
                            </div>

                            <div
                                class="inventory-v49-description inventory-v238-rich-description"
                                :class="{ 'inventory-v92-vestige-description': isVestige(item) }"
                                :title="cardItemDescription(item)"
                            ><span class="inventory-v92-description-copy" x-html="formatItemRichText(item.description, true)"></span></div>

                            <div class="inventory-v48-abilities">
                                <div class="inventory-v48-abilities-header">
                                    <span>Habilidades</span>
                                    <span x-show="!isVestige(item) && !singleUseItemUsed(item)"
                                          x-text="cardFeatureEntries(item).length"></span>
                                </div>

                                {{-- O card do Vestígio informa apenas a progressão. Os poderes são
                                     consultados ao abrir os detalhes, preservando a ficha completa. --}}
                                <div
                                    x-show="isVestige(item)"
                                    x-cloak
                                    class="inventory-v92-vestige-stages"
                                    aria-label="Etapas do Vestígio"
                                >
                                    <template x-for="stage in vestigeCardStages(item)" :key="'v92-stage-' + item.id + '-' + stage.key">
                                        <span
                                            class="inventory-v92-vestige-stage"
                                            :class="{ 'is-current': stage.current, 'is-reached': stage.reached }"
                                            :aria-label="stage.label + (stage.current ? ', etapa atual' : (stage.reached ? ', alcançada' : ', futura'))"
                                            :title="stage.label + (stage.current ? ' · Etapa atual' : (stage.reached ? ' · Alcançada' : ' · Ainda não alcançada'))"
                                        ><span class="inventory-v92-stage-marker" aria-hidden="true"></span><span x-text="stage.label"></span></span>
                                    </template>
                                </div>

                                <template x-if="!isVestige(item)">
                                <div class="inventory-v48-skill-list">
                                    <template
                                        x-for="entry in cardVisibleFeatureEntries(item)"
                                        :key="'v48-' + item.id + '-' + entry.index"
                                    >
                                        <div
                                            class="inventory-v48-skill inventory-v49-skill"
                                            :class="{ 'inventory-v85-consumed-skill': singleUseFeatureUsed(entry.feature) }"
                                            x-on:mouseenter="
                                                if (window.spellboundIsScrollCast(item, entry.feature)) {
                                                    const anchor = $el;
                                                    const rect = anchor.getBoundingClientRect();
                                                    const width = Math.min(430, window.innerWidth - 24);
                                                    const expectedHeight = Math.min(340, window.innerHeight - 24);
                                                    const spaceBelow = window.innerHeight - rect.bottom;
                                                    const showBelow = spaceBelow >= expectedHeight + 14 || spaceBelow >= rect.top;
                                                    const left = Math.max(12, Math.min(rect.left, window.innerWidth - width - 12));
                                                    const top = Math.max(12, Math.min(
                                                        showBelow ? rect.bottom + 10 : rect.top - expectedHeight - 10,
                                                        window.innerHeight - expectedHeight - 12
                                                    ));
                                                    inventorySkillTooltip = {
                                                        name: '', description: '', left, top, above:false,
                                                        spell: window.spellboundScrollTooltipModel(item, entry.feature)
                                                    };
                                                    window.spellboundScrollResolve(item.properties.spell_scroll,
                                                        @js(route('characters.spellbook.catalog', $character)))
                                                        .then(info => {
                                                            if (anchor?.matches(':hover') && inventorySkillTooltip?.spell?.itemId === item.id) {
                                                                inventorySkillTooltip.spell = window.spellboundScrollTooltipModel(item, entry.feature, info);
                                                            }
                                                        });
                                                } else {
                                                    showInventorySkillTooltip(entry.feature, $event);
                                                }
                                            "
                                            x-on:mouseleave="inventorySkillTooltip = null"
                                        >
                                            <span
                                                class="inventory-v48-skill-name"
                                                :title="entry.feature.description ? '' : (entry.feature.title || 'Habilidade')"
                                                x-text="entry.feature.title || 'Habilidade'"
                                            ></span>

                                            {{-- Conjuração do pergaminho usa sua CD e ataque; não consome slot de magia. --}}
                                            <template x-if="window.spellboundIsScrollRules(item, entry.feature)">
                                                <div class="inventory-v101-cast-row">
                                                    <button type="button" class="inventory-v101-cast-button"
                                                            :disabled="busyFeatureKey !== null || !!item?.properties?.spell_scroll?.consumed || (entry.feature?.usage?.enabled && Number(entry.feature.usage.current) < 1)"
                                                            :title="'Conjurar ' + (item?.properties?.spell_scroll?.spell_name || 'feitiço do pergaminho')"
                                                            @click.stop="window.dispatchEvent(new CustomEvent('spellbound-scroll-request', {detail: {item: item, index: entry.index}}))">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m12 2 2.8 7.2L22 12l-7.2 2.8L12 22l-2.8-7.2L2 12l7.2-2.8L12 2Z" stroke-width="1.6" stroke-linejoin="round"/></svg>
                                                        <span x-text="!!item?.properties?.spell_scroll?.consumed || (entry.feature?.usage?.enabled && Number(entry.feature.usage.current) < 1) ? 'Conjurado' : 'Conjurar'"></span>
                                                        <small x-show="entry.feature?.usage?.enabled" x-text="(entry.feature?.usage?.current ?? 0) + '/' + (entry.feature?.usage?.max ?? 1)"></small>
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="entry.feature?.usage?.enabled && !entry.vestige && !window.spellboundIsScrollRules(item, entry.feature)">
                                                <div class="inventory-v48-tracker">
                                                    <button
                                                        x-show="entry.feature.usage.recovery === 'single_use' && !singleUseFeatureUsed(entry.feature)"
                                                        x-cloak
                                                        type="button"
                                                        class="inventory-v48-use"
                                                        :disabled="busyFeatureKey !== null"
                                                        :title="'Usar ' + (entry.feature.title || 'habilidade')"
                                                        @click.stop="requestSingleUse(item, entry.index)"
                                                        x-text="'Usar · ' + entry.feature.usage.current + '/' + entry.feature.usage.max"
                                                    ></button>
                                                    <span
                                                        x-show="singleUseFeatureUsed(entry.feature)"
                                                        x-cloak
                                                        class="inventory-v48-passive"
                                                    >Usado</span>
                                                    <div
                                                        x-show="entry.feature.usage.recovery !== 'single_use'"
                                                        x-cloak
                                                        class="inventory-v11-mini-tracker"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click.stop="changeFeatureUses(item, entry.index, -1)"
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="entry.feature.usage.counter_mode === 'build' ? 'Reduzir carga acumulada' : 'Gastar carga'"
                                                        >−</button>
                                                        <strong x-text="entry.feature.usage.current + '/' + entry.feature.usage.max"></strong>
                                                        <button
                                                            type="button"
                                                            @click.stop="changeFeatureUses(item, entry.index, 1)"
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="entry.feature.usage.counter_mode === 'build' ? 'Acumular carga' : 'Recuperar carga'"
                                                        >+</button>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="(!entry.feature?.usage?.enabled || entry.vestige) && !window.spellboundIsScrollRules(item, entry.feature)">
                                                <span
                                                    class="inventory-v48-passive"
                                                    x-text="entry.vestige ? vestigeFeatureUsageLabel(entry.feature) : (window.spellboundIsScrollCast(item,entry.feature) ? 'Feitiço' : 'Passiva')"
                                                ></span>
                                            </template>
                                        </div>
                                    </template>
                                    <span
                                        x-show="!isVestige(item) && cardFeatureEntries(item).length === 0"
                                        x-cloak
                                        class="inventory-v48-footer-note"
                                    >Sem habilidades adicionais</span>
                                </div>
                                </template>

                                {{-- V66.1.88: descarte centralizado, independente do hover da habilidade. --}}
                                <div
                                    x-show="singleUseItemUsed(item)"
                                    x-cloak
                                    class="inventory-v88-discard-footer"
                                    @mouseenter="inventorySkillTooltip = null"
                                >
                                    <button
                                        type="button"
                                        class="inventory-v88-discard-action"
                                        :disabled="busyItemId !== null"
                                        :aria-label="'Descartar ' + item.name"
                                        title="Descartar item consumido"
                                        @focus="inventorySkillTooltip = null"
                                        @click.stop="requestDiscardUsedItem(item)"
                                    >Descartar</button>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>
                </div>
            </section>
        </template>

        {{-- Um único tooltip no body: o pergaminho usa um verbete resumido,
             as outras habilidades mantêm o hover convencional. --}}
        <template x-teleport="body">
            <div
                x-show="inventorySkillTooltip !== null"
                x-cloak
                data-theme="{{ $characterTheme }}"
                class="character-sheet-theme inventory-v49-skill-tooltip"
                :class="{ 'inventory-v104-scroll-tooltip': !!inventorySkillTooltip?.spell }"
                :style="inventorySkillTooltip ? {
                    left: inventorySkillTooltip.left + 'px',
                    top: inventorySkillTooltip.top + 'px',
                    transform: inventorySkillTooltip.above ? 'translateY(-100%)' : 'none'
                } : {}"
                role="tooltip"
                aria-live="off"
            >
                <template x-if="inventorySkillTooltip?.spell">
                    <div class="inventory-v104-tooltip-layout">
                        <header class="inventory-v104-tooltip-heading">
                            <strong x-text="inventorySkillTooltip.spell.name"></strong>
                            <em x-text="inventorySkillTooltip.spell.subtitle"></em>
                        </header>
                        <dl class="inventory-v104-tooltip-facts">
                            <template x-for="fact in inventorySkillTooltip.spell.facts" :key="fact.label">
                                <div>
                                    <dt x-text="fact.label + ':'"></dt>
                                    <dd x-text="fact.value"></dd>
                                </div>
                            </template>
                        </dl>
                        <p class="inventory-v104-tooltip-excerpt"
                           x-show="!!inventorySkillTooltip.spell.excerpt"
                           x-text="inventorySkillTooltip.spell.excerpt"></p>
                        <footer>Abra o item e escolha <b>Ler feitiço</b> para ver o verbete completo.</footer>
                    </div>
                </template>
                <template x-if="!inventorySkillTooltip?.spell">
                    <div class="inventory-v104-tooltip-default">
                        <strong x-text="inventorySkillTooltip?.name || ''"></strong>
                        <p x-html="formatItemRichText(inventorySkillTooltip?.description || '')"></p>
                        <small>Abra o item para consultar a habilidade completa.</small>
                    </div>
                </template>
            </div>
        </template>

        <div x-show="totalCount === 0" x-cloak class="py-16 text-center">
            <p class="font-serif text-[19px] font-black text-[#53150f]">
                Nenhum item ainda
            </p>

            <p class="mt-1 text-[12px] text-[#8c6239]">
                Crie um item ou escolha algo liberado pelo Mestre.
            </p>
        </div>
    </div>

    {{-- V66.2.31 — Ativação imprevisível dos circuitos do card.
         CSS puro gerava ciclos iguais. Agora apenas 1–2 eventos surgem
         em intervalos sorteados; bifurcações e micropartículas são eventuais.
         Sem efeitos no retrato, sem loops constantes de filtros. --}}
    @once
        <script>
        (() => {
            'use strict';
            if (window.SpellboundTechCircuits) return;

            window.SpellboundTechCircuits = () => ({
                _stopTech: null,
                start(overlay) {
                    if (!overlay || this._stopTech || typeof overlay.animate !== 'function') return;
                    const svg = overlay.querySelector('svg');
                    if (!svg) return;

                    const branches = Array.from(svg.querySelectorAll('[data-tech-branch]'));
                    const roam = Array.from(svg.querySelectorAll('.inventory-v227-roam'));
                    const sparks = svg.querySelector('.inventory-v231-sparks');
                    const media = window.matchMedia('(prefers-reduced-motion: reduce)');
                    const anims = new Set();
                    const timers = new Set();
                    let running = true;
                    let visible = true;
                    let concurrent = 0;
                    const maxConcurrent = 4;
                    let lastBranch = -1;
                    let lastRoam = -1;
                    let observer = null;
                    let nextTickTimer = null;

                    const random = (a,b) => a + Math.random() * (b-a);
                    const int = (a,b) => Math.floor(random(a,b+1));
                    const queue = (fn, ms) => {
                        const id = window.setTimeout(() => {
                            timers.delete(id);
                            if (running && overlay.isConnected) fn();
                        }, ms);
                        timers.add(id);
                    };
                    // Garante um único agendamento ativo mesmo após abrir/fechar
                    // a gaveta e entrar/sair da viewport (evita loops acumulados).
                    const scheduleTick = (ms) => {
                        if (nextTickTimer !== null || !running) return;
                        const id = window.setTimeout(() => {
                            timers.delete(id);
                            nextTickTimer = null;
                            if (running && overlay.isConnected) tick();
                        }, ms);
                        nextTickTimer = id;
                        timers.add(id);
                    };
                    const play = (el, frames, options) => {
                        if (!el || !running) return null;
                        const animation = el.animate(frames, {fill:'none', ...options});
                        anims.add(animation);
                        animation.finished.then(() => anims.delete(animation)).catch(() => anims.delete(animation));
                        return animation;
                    };
                    const move = (path, duration, intensity, delay=0, isHead=false) => {
                        if (!path) return;
                        const head = isHead ? 1 : intensity;
                        play(path, [
                            {strokeDashoffset:'18', opacity:0, offset:0},
                            {strokeDashoffset:'7', opacity:head*.15, offset:.15},
                            {strokeDashoffset:'-38', opacity:head, offset:.53},
                            {strokeDashoffset:'-101', opacity:head*.36, offset:.89},
                            {strokeDashoffset:'-126', opacity:0, offset:1},
                        ], {duration, delay, easing:'linear'});
                    };
                    const burst = (cx,cy,delay=0,spreadScale=1) => {
                        if (!sparks || !running) return;
                        const count = int(3,7);
                        for (let k=0;k<count;k++) {
                            const dot = document.createElementNS('http://www.w3.org/2000/svg','circle');
                            dot.setAttribute('cx',cx);
                            dot.setAttribute('cy',cy);
                            dot.setAttribute('r',random(.40,1.02).toFixed(2));
                            dot.setAttribute('class','inventory-v231-spark');
                            sparks.appendChild(dot);
                            const theta = random(0, Math.PI*2);
                            const radius = random(5.5,15.5) * spreadScale;
                            const dx = Math.cos(theta)*radius;
                            const dy = Math.sin(theta)*radius;
                            const animation = play(dot,[
                                {transform:'translate(0px,0px) scale(.38)',opacity:0,offset:0},
                                {transform:'translate(0px,0px) scale(1.06)',opacity:random(.52,.84),offset:.18},
                                {transform:`translate(${dx.toFixed(1)}px,${dy.toFixed(1)}px) scale(.22)`,opacity:0,offset:1}
                            ],{duration:random(430,820),delay:delay+random(0,120),easing:'ease-out'});
                            if (animation) animation.finished.finally(() => dot.remove()).catch(()=>{});
                            else dot.remove();
                        }
                    };
                    const pathPoint = (path, atEnd=true) => {
                        if (!path || typeof path.getTotalLength !== 'function') return null;
                        try {
                            const len = path.getTotalLength();
                            if (!Number.isFinite(len) || len <= 0) return null;
                            const point = path.getPointAtLength(atEnd ? Math.max(.01, len - .01) : .01);
                            return point ? {x: point.x, y: point.y} : null;
                        } catch (error) {
                            return null;
                        }
                    };
                    const chooseDifferent = (arr,last) => {
                        if (arr.length < 2) return 0;
                        let index = int(0,arr.length-1);
                        if (index===last) index=(index+int(1,arr.length-1))%arr.length;
                        return index;
                    };
                    // V66.2.34: trajetos ~20% mais lentos; sorteios e número de eventos preservados.
                    const branchEvent = () => {
                        const index = chooseDifferent(branches,lastBranch);
                        lastBranch=index;
                        const g=branches[index];
                        if (!g) return 0;
                        const trunk=g.querySelector('.inventory-v227-trace.inventory-v227-stem');
                        const head=g.querySelector('.inventory-v227-head');
                        const arms=Array.from(g.querySelectorAll('.inventory-v227-arm'));
                        const node=g.querySelector('.inventory-v227-node');
                        const speed=random(720,1180);
                        const peak=random(.34,.66);
                        move(trunk,speed,peak);
                        move(head,speed,1);
                        const splitAt=speed*random(.56,.79);
                        const activeArms=[];
                        arms.forEach((arm,j) => {
                            if (j > 1 && Math.random() < .18) return;
                            if (j > 0 && Math.random() < .08) return;
                            activeArms.push(arm);
                            move(arm,random(550,970),peak*random(.64,.98),splitAt+random(0,145));
                        });
                        if (node) {
                            play(node,[
                                {opacity:0,transform:'scale(.4)',offset:0},
                                {opacity:random(.54,.88),transform:'scale(1.3)',offset:.42},
                                {opacity:0,transform:'scale(.62)',offset:1}
                            ],{duration:random(390,650),delay:splitAt-70,easing:'ease-out'});
                            if (Math.random() < .92) burst(node.getAttribute('cx'),node.getAttribute('cy'),splitAt,1.02);
                        }
                        activeArms.forEach((arm, armIndex) => {
                            const point = pathPoint(arm, true);
                            if (point && Math.random() < .76) {
                                burst(point.x, point.y, splitAt + random(120,270) + armIndex * 36, .82);
                            }
                        });
                        if (activeArms.length > 1 && Math.random() < .58) {
                            const extraArm = activeArms[int(0, activeArms.length - 1)];
                            move(extraArm, random(440,740), peak * random(.42,.68), splitAt + random(150,270));
                        }
                        return Math.round(splitAt+1160);
                    };
                    const roamEvent = () => {
                        const index=chooseDifferent(roam,lastRoam);
                        lastRoam=index;
                        const path = roam[index];
                        const duration = random(680,1390);
                        move(path,duration,random(.28,.52));
                        const point = pathPoint(path, true);
                        if (point && Math.random() < .44) burst(point.x, point.y, duration * random(.44,.84), .56);
                        return Math.round(duration + 760);
                    };
                    const launchEvent = () => {
                        concurrent++;
                        const lifetime = Math.random() < .78 ? branchEvent() : roamEvent();
                        queue(() => { concurrent = Math.max(0, concurrent - 1); }, lifetime);
                        return lifetime;
                    };
                    const tick = () => {
                        if (!running || !overlay.isConnected) return;
                        if (!media.matches && !document.hidden && visible && concurrent < maxConcurrent) {
                            const capacity = Math.max(0, maxConcurrent - concurrent);
                            let launches = 1;
                            if (capacity > 1 && Math.random() < .74) launches++;
                            if (capacity > 2 && Math.random() < .44) launches++;
                            if (capacity > 3 && Math.random() < .18) launches++;
                            for (let i = 0; i < launches; i++) {
                                const delay = i === 0 ? 0 : random(80,240) + ((i - 1) * random(28,72));
                                queue(() => {
                                    if (!running || !overlay.isConnected || media.matches || document.hidden || !visible || concurrent >= maxConcurrent) return;
                                    launchEvent();
                                }, delay);
                            }
                        }
                        // O próximo evento é sorteado a cada passagem, sem período fixo.
                        scheduleTick(random(340,1420));
                    };
                    const onVisibility = () => {if (!document.hidden && visible && running) scheduleTick(random(260,800));};
                    document.addEventListener('visibilitychange',onVisibility);
                    if (typeof IntersectionObserver!=='undefined') {
                        observer = new IntersectionObserver(entries => {
                            visible=!!entries[0]?.isIntersecting;
                            if (visible && running) scheduleTick(random(240,900));
                        },{threshold:.06});
                        observer.observe(overlay);
                    }
                    scheduleTick(random(350,1650));
                    this._stopTech=() => {
                        if (!running) return;
                        running=false;
                        observer?.disconnect();
                        document.removeEventListener('visibilitychange',onVisibility);
                        timers.forEach(id=>clearTimeout(id));
                        timers.clear();
                        anims.forEach(a=>a.cancel());
                        anims.clear();
                        if (sparks) sparks.replaceChildren();
                    };
                },
                destroy() {
                    this._stopTech?.();
                    this._stopTech=null;
                }
            });
        })();
        </script>
    @endonce

        {{-- V66.2.101 — camada de conjuração de pergaminhos, independente dos slots da classe. --}}
        <template x-teleport="body">
            <div class="character-sheet-theme inventory-v101-layer"
                 data-theme="{{ $characterTheme }}"
                 x-data="window.SpellboundInventoryScrollCast({
                    castUrl: @js(route('characters.items.features.uses.update', ['character' => $character, 'item' => '__ITEM__', 'feature' => '__FEATURE__'])),
                    syncUrl: @js(route('characters.items.sync', $character)),
                    concentrationUrl: @js(route('characters.spellbook.state', $character)),
                    catalogUrl: @js(route('characters.spellbook.catalog', $character))
                 })"
                 @spellbound-scroll-request.window="openDialog($event.detail)"
                 @keydown.escape.window="if (modalOpen && !busy) closeDialog()"
                 x-show="modalOpen" x-cloak
                 role="dialog" aria-modal="true" aria-label="Conjurar feitiço do pergaminho">
                <div class="inventory-v101-backdrop" @click="if (!busy) closeDialog()"></div>
                <section class="inventory-v101-panel inventory-v104-cast-panel"
                         @keydown.escape.stop="if (!busy) closeDialog()">
                    <header class="inventory-v104-cast-nav">
                        <div class="inventory-v104-cast-tabs" aria-label="Modo de conjuração">
                            <span class="is-active">FEITIÇO</span>
                            <span>PERGAMINHO · CONJURAÇÃO</span>
                        </div>
                        <span class="inventory-v104-cast-resource">Sem gasto de espaços de magia</span>
                        <button type="button" class="inventory-v101-close" aria-label="Fechar conjuração"
                                :disabled="busy" @click="closeDialog()">×</button>
                    </header>
                    <div class="inventory-v101-content inventory-v104-cast-content">
                        {{-- Estatísticas do pergaminho, separadas da descrição do feitiço. --}}
                        <div class="inventory-v104-cast-stats" role="group" aria-label="Dados do pergaminho">
                            <div><small>CD do pergaminho</small><strong x-text="scrollStats().dc"></strong></div>
                            <div><small>Ataque</small><strong x-text="scrollStats().attack"></strong></div>
                            <div><small>Círculo</small><strong x-text="Number(scroll?.spell_level) === 0 ? 'Truque' : ((scroll?.spell_level ?? '?') + 'º')"></strong></div>
                            <div><small>Usos restantes</small><strong x-text="remainingUses()"></strong></div>
                        </div>
                        <article class="inventory-v104-cast-article" aria-label="Feitiço vinculado ao pergaminho">
                            <div class="inventory-v104-cast-title-row">
                                <div>
                                    <h2 x-text="scroll?.spell_name || 'Feitiço do pergaminho'"></h2>
                                    <em x-text="window.spellboundScrollSubtitle(scroll, enrichedInfo)"></em>
                                </div>
                                <small x-text="enrichedInfo.source_label || scroll?.spell_source || ''"></small>
                            </div>
                            <dl class="inventory-v104-cast-facts">
                                <div><dt>Conjuração:</dt><dd x-text="enrichedInfo.casting_time || 'Não informada'"></dd></div>
                                <div><dt>Alcance:</dt><dd x-text="enrichedInfo.range || 'Não informado'"></dd></div>
                                <div><dt>Componentes:</dt><dd x-text="enrichedInfo.components || 'Não informados'"></dd></div>
                                <div><dt>Duração:</dt><dd><span x-text="enrichedInfo.duration || 'Não informada'"></span><span x-show="enrichedInfo.concentration && !/concentra/i.test(enrichedInfo.duration || '')" x-cloak> (Concentração)</span></dd></div>
                            </dl>
                            <div class="inventory-v104-cast-description"
                                 x-show="enrichedInfo.description_html || enrichedInfo.description_text" x-cloak
                                 x-html="window.spellboundScrollSafeRich(enrichedInfo.description_html || enrichedInfo.description_text, 30000)"></div>
                            <section class="inventory-v104-cast-higher"
                                     x-show="enrichedInfo.higher_level_text || enrichedInfo.higher_level_html" x-cloak>
                                <h3 x-text="enrichedInfo.higher_level_label || 'Em Níveis Superiores'"></h3>
                                <div x-html="window.spellboundScrollSafeRich(enrichedInfo.higher_level_html || enrichedInfo.higher_level_text, 15000)"></div>
                            </section>
                            <p class="inventory-v104-cast-loading" x-show="infoLoading" x-cloak>Consultando o verbete no Grimório…</p>
                            <p class="inventory-v104-cast-loading" x-show="!infoLoading && !enrichedInfo.description_text && !enrichedInfo.description_html" x-cloak>
                                Não há descrição completa disponível para este feitiço.
                            </p>
                        </article>
                        <label class="inventory-v101-conflict inventory-v104-cast-conflict"
                               x-show="!!enrichedInfo.concentration && concentration?.active" x-cloak>
                            <input type="checkbox" x-model="confirmReplace">
                            <span>Substituir a concentração atual em <strong x-text="concentration?.effect_name || concentration?.spell_name || 'outro efeito'"></strong>.</span>
                        </label>
                        <p class="inventory-v101-error" x-show="!!error" x-text="error" role="alert"></p>
                    </div>
                    <footer class="inventory-v101-foot inventory-v104-cast-foot">
                        <button type="button" class="inventory-v101-cancel" :disabled="busy" @click="closeDialog()">Cancelar</button>
                        <button type="button" class="inventory-v101-confirm"
                                :disabled="busy || infoLoading || !item || remainingUses() < 1 || (!!enrichedInfo.concentration && concentration?.active && !confirmReplace)"
                                @click="cast()" x-text="busy ? 'Conjurando...' : 'Conjurar Feitiço'"></button>
                    </footer>
                </section>
            </div>
        </template>

        {{-- Contador real de efeitos temporários de pergaminhos sem concentração.
             A concentração continua no HUD de Rodadas da própria ficha. --}}
        <template x-teleport="body">
            <div x-data="window.SpellboundInventoryScrollTimers(@js(route('characters.items.sync', $character)))"
                 @spellbound-scroll-cast-done.window="load()"
                 x-init="start()"
                 class="character-sheet-theme inventory-v101-duration-hud" data-theme="{{ $characterTheme }}"
                 x-show="timers.length > 0" x-cloak>
                <button type="button" @click="expanded = !expanded" class="inventory-v101-timers-trigger"
                        :aria-expanded="expanded" title="Duração dos feitiços conjurados com pergaminhos">
                    <span>◷</span> Pergaminhos <b x-text="timers.length"></b>
                </button>
                <div x-show="expanded" x-cloak class="inventory-v101-timers-list">
                    <strong>Efeitos em andamento</strong>
                    <template x-for="timer in timers" :key="timer.id">
                        <div class="inventory-v101-timer-entry">
                            <span x-text="timer.spell_name"></span>
                            <b x-text="timer.remaining_rounds + ' rodadas'"></b>
                        </div>
                    </template>
                    <small>O contador acompanha as rodadas da iniciativa.</small>
                </div>
            </div>
        </template>

        {{-- V66.2.102 — leitura independente do feitiço, também disponível no modal de conjuração. --}}
        <template x-teleport="body">
            <div class="character-sheet-theme inventory-v102-reader-layer" data-theme="{{ $characterTheme }}"
                 x-data="window.SpellboundInventoryScrollReader(@js(route('characters.spellbook.catalog', $character)))"
                 @spellbound-scroll-read.window="openReader($event.detail)"
                 x-show="opened" x-cloak role="dialog" aria-modal="true" aria-label="Ler feitiço do pergaminho">
                <div class="inventory-v102-reader-backdrop" @click="closeReader()"></div>
                <section class="inventory-v102-reader-panel" @keydown.escape.stop.prevent="closeReader()">
                    <header class="inventory-v102-reader-nav">
                        <span class="inventory-v102-reader-tab">FEITIÇO</span>
                        <span>INFORMAÇÕES DO PERGAMINHO</span>
                        <button type="button" @click="closeReader()" aria-label="Fechar leitura do feitiço">×</button>
                    </header>
                    <div class="inventory-v102-reader-scroll">
                        <div class="inventory-v102-reader-title">
                            <div>
                                <h2 x-text="scroll?.spell_name || 'Feitiço'"></h2>
                                <em x-text="window.spellboundScrollSubtitle(scroll,info)"></em>
                            </div>
                            <small x-text="info.source_label || scroll?.spell_source || ''"></small>
                        </div>
                        <div class="inventory-v102-reader-facts">
                            <p><b>Conjuração:</b> <span x-text="info.casting_time || 'Não informada'"></span></p>
                            <p><b>Alcance:</b> <span x-text="info.range || 'Não informado'"></span></p>
                            <p><b>Componentes:</b> <span x-text="info.components || 'Não informados'"></span></p>
                            <p><b>Duração:</b> <span x-text="info.duration || 'Não informada'"></span><span x-show="info.concentration" x-cloak> (Concentração)</span></p>
                        </div>
                        <p x-show="loading" x-cloak class="inventory-v102-reader-loading">Consultando os dados do Grimório…</p>
                        <div class="inventory-v102-reader-description" x-show="!!info.description_html || !!info.description_text" x-cloak
                             x-html="window.spellboundScrollSafeRich(info.description_html || info.description_text)"></div>
                        <div class="inventory-v102-reader-extra" x-show="!!info.higher_level_text || !!info.higher_level_html" x-cloak>
                            <h3 x-text="info.higher_level_label || 'Em Níveis Superiores'"></h3>
                            <div x-html="window.spellboundScrollSafeRich(info.higher_level_html || info.higher_level_text)"></div>
                        </div>
                        <p x-show="!loading && !info.description_html && !info.description_text" x-cloak class="inventory-v102-reader-loading">
                            Este pergaminho não possui descrição completa disponível no catálogo. Verifique os dados do feitiço vinculado no editor.
                        </p>
                        <div class="inventory-v102-reader-source">Origem: <span x-text="info.source_label || scroll?.spell_source || 'Pergaminho'"></span></div>
                    </div>
                </section>
            </div>
        </template>

        @push('styles')
        <style>
        .inventory-v101-layer {position:fixed;inset:0;z-index:2147482500;display:grid;place-items:center;padding:16px;color:var(--theme-modal-text,var(--theme-text-primary));}
        .inventory-v101-layer[x-cloak],.inventory-v101-duration-hud[x-cloak]{display:none!important;}
        .inventory-v101-backdrop {position:absolute;inset:0;background:var(--theme-modal-backdrop,rgba(0,0,0,.8));backdrop-filter:blur(5px);}
        .inventory-v101-panel {position:relative;width:min(780px,100%);max-height:min(90dvh,940px);display:flex;flex-direction:column;min-height:0;overflow:hidden;border:1px solid var(--theme-modal-border,var(--theme-border));background:var(--theme-modal-surface,var(--theme-paper));border-radius:15px;box-shadow:var(--theme-modal-shadow,0 20px 80px #0005);}
        .inventory-v101-head {display:flex;align-items:center;gap:13px;padding:18px 20px;background:var(--theme-modal-header-background,var(--theme-panel));border-bottom:1px solid var(--theme-modal-border,var(--theme-border));}
        .inventory-v101-emblem {display:grid;place-items:center;flex:0 0 41px;width:41px;height:41px;font-size:25px;color:var(--theme-accent);border:1px solid var(--theme-border-strong);border-radius:10px;}
        .inventory-v101-head>div{flex:1;min-width:0;}.inventory-v101-head small{font-size:9px;font-weight:800;letter-spacing:.12em;color:var(--theme-text-secondary);}
        .inventory-v101-head h3{margin:3px 0;color:var(--theme-modal-title,var(--theme-text-strong));font:bold 21px/1.2 Georgia,serif;overflow-wrap:anywhere;}
        .inventory-v101-head p{margin:4px 0 0;font-size:11px;color:var(--theme-modal-muted,var(--theme-text-secondary));}
        .inventory-v101-close{align-self:flex-start;border:1px solid var(--theme-border);border-radius:8px;width:30px;height:30px;font-size:21px;color:var(--theme-text-primary);}
        .inventory-v101-close:hover{background:var(--theme-hover);}
        .inventory-v101-content{padding:20px 24px;display:grid;gap:17px;overflow:auto;min-height:0;flex:1;overscroll-behavior:contain;}
        .inventory-v101-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;}
        .inventory-v101-stats>div{display:grid;gap:6px;padding:12px 10px;border:1px solid var(--theme-border);background:var(--theme-surface-card-alt,var(--theme-paper-alt));border-radius:9px;min-width:0;}
        .inventory-v101-stats small{font-size:10px;color:var(--theme-text-secondary);}.inventory-v101-stats strong{font-size:18px;color:var(--theme-text-strong);}
        .inventory-v101-meta{display:flex;gap:8px;flex-wrap:wrap}.inventory-v101-meta span{border:1px solid var(--theme-border-strong);color:var(--theme-text-primary);background:var(--theme-accent-softer,var(--theme-panel-soft));padding:5px 8px;font-size:10px;border-radius:7px;}
        .inventory-v101-note{max-height:122px;overflow:auto;font:12px/1.6 Georgia,serif;white-space:pre-line;color:var(--theme-text-primary);padding:10px;border-left:2px solid var(--theme-accent);background:var(--theme-surface-inset,var(--theme-paper-alt));}
        .inventory-v101-conflict{display:flex;align-items:center;gap:10px;padding:12px;border:1px solid var(--theme-border-strong);border-radius:9px;color:var(--theme-text-primary);background:var(--theme-surface-card-alt);font-size:12px;cursor:pointer;}
        .inventory-v101-conflict input{accent-color:var(--theme-accent-strong);}.inventory-v101-error{color:var(--theme-state-damage,#bd584c);font-size:12px;}
        .inventory-v101-foot{display:flex;justify-content:flex-end;gap:9px;padding:13px 20px;background:var(--theme-modal-header-background);border-top:1px solid var(--theme-border);}
        .inventory-v101-foot button{padding:10px 14px;border-radius:8px;font:800 11px system-ui,sans-serif;}
        .inventory-v101-cancel{border:1px solid var(--theme-border-strong);color:var(--theme-text-primary);background:var(--theme-surface-card);}
        .inventory-v101-confirm{background:var(--theme-button-background,var(--theme-accent));color:var(--theme-button-text,#fff);border:1px solid var(--theme-border-strong);}
        .inventory-v101-confirm:hover:not(:disabled){background:var(--theme-button-hover-background,var(--theme-accent-strong));}
        .inventory-v101-confirm:disabled{opacity:.45;cursor:not-allowed;}
        .inventory-v101-cast-row{display:flex;align-items:center;flex:0 0 auto;}
        .inventory-v48-skill .inventory-v101-cast-button{display:inline-flex;gap:4px;align-items:center;justify-content:center;padding:4px 7px;min-height:23px;border:1px solid var(--theme-border-strong);border-radius:7px;background:var(--theme-accent-soft,var(--theme-card-alt-background));color:var(--theme-text-strong);font-weight:800;font-size:9px;white-space:nowrap;cursor:pointer;}
        .inventory-v48-skill .inventory-v101-cast-button:hover:not(:disabled){background:var(--theme-hover);border-color:var(--theme-accent);}
        .inventory-v48-skill .inventory-v101-cast-button svg{height:13px;width:13px;color:var(--theme-accent);}
        .inventory-v48-skill .inventory-v101-cast-button small{font-size:8px;opacity:.75;}
        .inventory-v48-skill .inventory-v101-cast-button:disabled{opacity:.5;cursor:not-allowed;}
        .inventory-v101-duration-hud{position:fixed;right:20px;bottom:72px;z-index:700;color:var(--theme-text-primary);font-size:11px;}
        .inventory-v101-timers-trigger{display:flex;align-items:center;gap:9px;padding:9px 13px;border:1px solid var(--theme-border-strong);border-radius:10px;background:var(--theme-modal-surface,var(--theme-paper));box-shadow:var(--theme-shadow);color:var(--theme-text-primary);font-weight:700;}
        .inventory-v101-timers-trigger>b{color:var(--theme-accent);}
        .inventory-v101-timers-list{position:absolute;right:0;bottom:calc(100% + 8px);width:min(300px,calc(100vw - 24px));padding:12px;background:var(--theme-modal-surface,var(--theme-paper));border:1px solid var(--theme-border-strong);border-radius:10px;box-shadow:var(--theme-shadow-strong);}
        .inventory-v101-timers-list>strong{display:block;margin-bottom:8px;color:var(--theme-text-strong);}
        .inventory-v101-timer-entry{display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-top:1px solid var(--theme-border-soft);}
        .inventory-v101-timer-entry>b{flex-shrink:0;color:var(--theme-accent);}.inventory-v101-timers-list>small{display:block;margin-top:9px;color:var(--theme-text-secondary);}
        @media(max-width:520px){.inventory-v101-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.inventory-v101-head{padding:14px}.inventory-v101-content{padding:14px}.inventory-v101-foot{padding:12px}.inventory-v101-duration-hud{right:10px;bottom:58px}}
        /* V66.2.102 — painel de leitura do Grimório e detalhes da conjuração. */
        .inventory-v102-cast-actions{display:flex;align-items:center;gap:8px;align-self:flex-start;}
        .inventory-v102-cast-read,.inventory-v102-cast-detail-link{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--theme-border-strong);border-radius:8px;padding:8px 10px;background:var(--theme-surface-card-alt,var(--theme-paper-alt));color:var(--theme-text-primary);font:800 10px system-ui,sans-serif;cursor:pointer;}
        .inventory-v102-cast-read:hover,.inventory-v102-cast-detail-link:hover{background:var(--theme-hover);border-color:var(--theme-accent);}
        .inventory-v102-cast-spellinfo{border-top:1px solid var(--theme-border-soft);padding:15px 0 2px;display:grid;gap:9px;}
        .inventory-v102-cast-section-caption{font:800 9px system-ui,sans-serif;letter-spacing:.13em;color:var(--theme-text-secondary);}
        .inventory-v102-cast-spelltitle{display:flex;align-items:baseline;gap:12px;flex-wrap:wrap;}
        .inventory-v102-cast-spelltitle strong{font:800 18px/1.3 Georgia,serif;color:var(--theme-text-strong);}
        .inventory-v102-cast-spelltitle span{font:italic 11px Georgia,serif;color:var(--theme-text-secondary);}
        .inventory-v102-cast-spellfacts{display:grid;gap:4px;font-size:11px;color:var(--theme-text-primary);}
        .inventory-v102-cast-spellfacts p{margin:0;}.inventory-v102-cast-spellfacts b{color:var(--theme-text-strong);}
        .inventory-v102-cast-excerpt{font:11px/1.65 system-ui,sans-serif;color:var(--theme-text-primary);border-top:1px solid var(--theme-border-soft);padding-top:10px;max-height:120px;overflow:hidden;}
        .inventory-v102-cast-excerpt p{margin:0 0 6px;}.inventory-v102-cast-detail-link{width:max-content;}
        .inventory-v102-reader-layer{position:fixed;inset:0;z-index:2147482900;display:grid;place-items:center;padding:17px;color:var(--theme-modal-text,var(--theme-text-primary));}
        .inventory-v102-reader-layer[x-cloak]{display:none!important;}
        .inventory-v102-reader-backdrop{position:absolute;inset:0;background:var(--theme-modal-backdrop,rgba(0,0,0,.85));backdrop-filter:blur(5px);}
        .inventory-v102-reader-panel{position:relative;width:min(790px,100%);max-height:90dvh;display:flex;flex-direction:column;overflow:hidden;border:1px solid var(--theme-modal-border,var(--theme-border));border-radius:14px;background:var(--theme-modal-surface,var(--theme-paper));box-shadow:var(--theme-modal-shadow,0 22px 90px #0007);}
        .inventory-v102-reader-nav{display:flex;align-items:center;gap:18px;padding:12px 17px;background:var(--theme-modal-header-background,var(--theme-paper-strong));border-bottom:1px solid var(--theme-border-soft);font:800 9px system-ui,sans-serif;letter-spacing:.07em;color:var(--theme-text-secondary);}
        .inventory-v102-reader-tab{color:var(--theme-accent);position:relative;padding:5px 0;}.inventory-v102-reader-tab:after{position:absolute;content:"";left:0;bottom:-13px;height:2px;width:100%;background:var(--theme-accent);}
        .inventory-v102-reader-nav button{margin-left:auto;width:28px;height:28px;border:1px solid var(--theme-border-soft);border-radius:7px;color:var(--theme-text-primary);background:var(--theme-surface-card);font-size:20px;line-height:1;cursor:pointer;}
        .inventory-v102-reader-nav button:hover{background:var(--theme-hover);}
        .inventory-v102-reader-scroll{overflow:auto;min-height:0;padding:26px clamp(19px,5%,45px) 28px;}
        .inventory-v102-reader-title{display:flex;gap:15px;align-items:flex-start;justify-content:space-between;}
        .inventory-v102-reader-title h2{margin:0;color:var(--theme-text-strong);font:bold clamp(25px,4vw,33px)/1.2 Georgia,serif;overflow-wrap:anywhere;}
        .inventory-v102-reader-title em{display:block;margin:8px 0 0;font:italic 13px Georgia,serif;color:var(--theme-text-secondary);}
        .inventory-v102-reader-title>small{flex:0 0 auto;color:var(--theme-text-secondary);font:700 9px system-ui,sans-serif;}
        .inventory-v102-reader-facts{display:grid;gap:8px;margin-top:24px;color:var(--theme-text-primary);font:13px/1.5 system-ui,sans-serif;}
        .inventory-v102-reader-facts p{margin:0;}.inventory-v102-reader-facts b{font-weight:800;color:var(--theme-text-strong);}
        .inventory-v102-reader-description,.inventory-v102-reader-extra{margin-top:21px;padding-top:18px;border-top:1px solid var(--theme-border-soft);color:var(--theme-text-primary);font:13px/1.8 system-ui,sans-serif;overflow-wrap:anywhere;}
        .inventory-v102-reader-description p,.inventory-v102-reader-extra p{margin:0 0 12px;}.inventory-v102-reader-description strong,.inventory-v102-reader-extra strong{color:inherit;font-weight:800;}
        .inventory-v102-reader-description ul,.inventory-v102-reader-extra ul,.inventory-v102-reader-description ol,.inventory-v102-reader-extra ol{padding-left:25px;margin:8px 0;}
        .inventory-v102-reader-extra h3{font:800 16px Georgia,serif;color:var(--theme-text-strong);margin:0 0 10px;}
        .inventory-v102-reader-loading{font-size:12px;color:var(--theme-text-secondary);margin-top:18px;}
        .inventory-v102-reader-source{margin-top:22px;padding-top:12px;border-top:1px solid var(--theme-border-soft);font:italic 11px Georgia,serif;color:var(--theme-text-secondary);}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip{position:fixed!important;z-index:2147482850!important;pointer-events:none!important;width:min(370px,calc(100vw - 24px))!important;max-height:min(420px,65vh);overflow:hidden;border:1px solid var(--theme-border-strong)!important;background:var(--theme-modal-surface,var(--theme-paper))!important;color:var(--theme-text-primary)!important;box-shadow:0 15px 45px #0005!important;}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip p{max-height:335px;overflow:hidden;}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip strong,.character-sheet-theme[data-theme].inventory-v49-skill-tooltip b{color:var(--theme-text-strong)!important;}
        /* V66.2.103 — a conjuração abre diretamente a descrição completa, sem segundo clique. */
        .inventory-v103-cast-spellinfo{gap:12px;padding-top:20px;min-height:0;}
        .inventory-v103-cast-spellinfo .inventory-v102-cast-spelltitle strong{font-size:clamp(19px,3.5vw,24px);}
        .inventory-v103-cast-spellinfo .inventory-v102-cast-spellfacts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px 20px;}
        .inventory-v103-cast-spellinfo .inventory-v102-cast-spellfacts p{overflow-wrap:anywhere;}
        .inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext{max-height:none!important;height:auto!important;overflow:visible!important;white-space:normal;line-height:1.75;margin-top:5px;padding-top:17px;border-top:1px solid var(--theme-border-soft);}
        .inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext p{margin:0 0 13px;}
        .inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext strong{font-weight:800;color:inherit;}
        .inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext h2,.inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext h3{font-family:Georgia,serif;font-weight:800;color:var(--theme-text-strong);}
        .inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext ul,.inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext ol{padding-left:22px;margin-bottom:12px;}
        .inventory-v103-cast-spellinfo .inventory-v103-cast-fulltext li{margin-bottom:5px;}
        .inventory-v103-cast-higher{border-top:1px solid var(--theme-border-soft);padding-top:13px;line-height:1.7;color:var(--theme-text-primary);}
        .inventory-v103-cast-higher h4{font:800 15px Georgia,serif;color:var(--theme-text-strong);margin:0 0 8px;}
        .inventory-v103-cast-higher p{margin:0 0 11px;}
        .inventory-v103-spell-source{font:italic 10px system-ui,sans-serif;color:var(--theme-text-secondary);margin:-4px 0 0;}
        .inventory-v103-cast-loading{font-size:11px;color:var(--theme-text-secondary);}
        .inventory-v101-foot{flex-shrink:0;}
        .inventory-v101-head{flex-shrink:0;}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip{max-height:min(320px,60vh)!important;overflow:hidden!important;}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip p{max-height:226px!important;overflow:hidden!important;font-size:11px;line-height:1.6;white-space:normal;overflow-wrap:anywhere;}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip p strong{font-size:11px;}
        @media(max-width:530px){.inventory-v101-head{flex-wrap:wrap}.inventory-v102-cast-actions{margin-left:auto;}.inventory-v102-reader-nav{gap:9px}.inventory-v102-reader-scroll{padding:17px}.inventory-v102-reader-title{flex-wrap:wrap}.inventory-v103-cast-spellinfo .inventory-v102-cast-spellfacts{grid-template-columns:1fr;}}
        /* V66.2.104 — modal de conjuração com tipografia e hierarquia do Grimório. */
        .inventory-v101-panel.inventory-v104-cast-panel{width:min(800px,100%);max-height:min(92dvh,940px);border-radius:14px;}
        .inventory-v104-cast-nav{display:flex;align-items:center;gap:16px;flex-shrink:0;padding:10px 15px;background:var(--theme-modal-header-background,var(--theme-paper-strong));border-bottom:1px solid var(--theme-border-soft);}
        .inventory-v104-cast-tabs{display:flex;gap:17px;align-items:center;min-width:0;flex:1;font:800 9px system-ui,sans-serif;letter-spacing:.08em;color:var(--theme-text-secondary);}
        .inventory-v104-cast-tabs span{position:relative;white-space:nowrap;}
        .inventory-v104-cast-tabs .is-active{color:var(--theme-accent);}
        .inventory-v104-cast-tabs .is-active:after{content:"";position:absolute;left:0;right:0;bottom:-15px;height:2px;background:var(--theme-accent);}
        .inventory-v104-cast-resource{font:600 10px system-ui,sans-serif;color:var(--theme-text-secondary);}
        .inventory-v104-cast-nav>.inventory-v101-close{align-self:center;flex:0 0 30px;background:var(--theme-surface-card);}
        .inventory-v101-content.inventory-v104-cast-content{display:block;padding:26px clamp(20px,5vw,48px) 30px;scrollbar-gutter:stable;}
        .inventory-v104-cast-stats{display:flex;align-items:center;flex-wrap:wrap;gap:8px 18px;padding:0 0 16px;margin:0 0 23px;border-bottom:1px solid var(--theme-border-soft);}
        .inventory-v104-cast-stats>div{display:flex;gap:7px;align-items:baseline;white-space:nowrap;}
        .inventory-v104-cast-stats small{font:700 10px system-ui,sans-serif;color:var(--theme-text-secondary);}
        .inventory-v104-cast-stats strong{font:800 13px system-ui,sans-serif;color:var(--theme-text-strong);}
        .inventory-v104-cast-stats>div:not(:last-child):after{content:"";display:inline-block;align-self:center;height:13px;width:1px;background:var(--theme-border-soft);margin-left:9px;}
        .inventory-v104-cast-article{color:var(--theme-text-primary);}
        .inventory-v104-cast-title-row{display:flex;align-items:start;justify-content:space-between;gap:15px;}
        .inventory-v104-cast-title-row h2{font:800 clamp(24px,4vw,31px)/1.18 Georgia,serif;color:var(--theme-text-strong);margin:0;overflow-wrap:anywhere;}
        .inventory-v104-cast-title-row em{display:block;font:italic 13px Georgia,serif;color:var(--theme-text-secondary);margin-top:5px;}
        .inventory-v104-cast-title-row>small{font:700 10px system-ui,sans-serif;color:var(--theme-text-secondary);flex-shrink:0;padding-top:8px;}
        .inventory-v104-cast-facts{display:grid;gap:6px;margin:23px 0 0;font:13px/1.55 system-ui,sans-serif;}
        .inventory-v104-cast-facts>div{display:flex;align-items:baseline;gap:5px;min-width:0;}
        .inventory-v104-cast-facts dt{font-weight:800;color:var(--theme-text-strong);flex:0 0 auto;}
        .inventory-v104-cast-facts dd{margin:0;color:var(--theme-text-primary);overflow-wrap:anywhere;}
        .inventory-v104-cast-description,.inventory-v104-cast-higher{border-top:1px solid var(--theme-border-soft);margin-top:24px;padding-top:18px;font:13px/1.85 system-ui,sans-serif;color:var(--theme-text-primary);overflow-wrap:anywhere;}
        .inventory-v104-cast-description p,.inventory-v104-cast-higher p{margin:0 0 16px;}
        .inventory-v104-cast-description strong,.inventory-v104-cast-higher strong{font-weight:800;color:inherit;}
        .inventory-v104-cast-description em,.inventory-v104-cast-higher em{font-style:italic;}
        .inventory-v104-cast-description ul,.inventory-v104-cast-description ol,.inventory-v104-cast-higher ul,.inventory-v104-cast-higher ol{padding-left:23px;margin:10px 0 17px;}
        .inventory-v104-cast-description li,.inventory-v104-cast-higher li{margin-bottom:7px;}
        .inventory-v104-cast-description h2,.inventory-v104-cast-description h3,.inventory-v104-cast-description h4,.inventory-v104-cast-higher h3{font:800 16px Georgia,serif;color:var(--theme-text-strong);margin:0 0 12px;}
        .inventory-v104-cast-higher>h3{font-size:18px;}
        .inventory-v104-cast-loading{margin:20px 0;font-size:12px;color:var(--theme-text-secondary);}
        .inventory-v104-cast-conflict{margin-top:24px;}
        .inventory-v104-cast-foot{flex-shrink:0;}
        /* Tooltip do feitiço: título, informações e texto em blocos distintos. */
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip.inventory-v104-scroll-tooltip{width:min(430px,calc(100vw - 24px))!important;max-height:min(410px,calc(100dvh - 24px))!important;overflow:hidden!important;padding:17px 18px!important;background:var(--theme-modal-surface,var(--theme-paper))!important;border:1px solid var(--theme-border-strong)!important;border-radius:12px!important;color:var(--theme-text-primary)!important;box-shadow:0 18px 52px rgba(0,0,0,.3)!important;}
        .inventory-v104-tooltip-layout{display:grid;gap:13px;min-width:0;}
        .inventory-v104-tooltip-heading{display:grid;gap:5px;padding-bottom:11px;border-bottom:1px solid var(--theme-border-soft);}
        .character-sheet-theme[data-theme] .inventory-v104-tooltip-heading strong{font:800 17px/1.28 Georgia,serif;color:var(--theme-text-strong)!important;overflow-wrap:anywhere;}
        .inventory-v104-tooltip-heading em{font:italic 11px Georgia,serif;color:var(--theme-text-secondary);}
        .inventory-v104-tooltip-facts{display:grid;gap:7px;margin:0;font:11px/1.5 system-ui,sans-serif;}
        .inventory-v104-tooltip-facts>div{display:grid;grid-template-columns:85px minmax(0,1fr);gap:12px;align-items:start;}
        .inventory-v104-tooltip-facts dt{color:var(--theme-text-strong);font-weight:800;}
        .inventory-v104-tooltip-facts dd{color:var(--theme-text-primary);margin:0;overflow-wrap:anywhere;}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip .inventory-v104-tooltip-excerpt{display:block!important;margin:0!important;padding:11px 0 0!important;border-top:1px solid var(--theme-border-soft);color:var(--theme-text-primary)!important;font:11px/1.75 system-ui,sans-serif!important;max-height:116px!important;overflow:hidden!important;}
        .character-sheet-theme[data-theme].inventory-v49-skill-tooltip .inventory-v104-tooltip-layout footer{display:block;margin:0;padding-top:10px;border-top:1px solid var(--theme-border-soft);font:10px/1.5 system-ui,sans-serif;color:var(--theme-text-secondary)!important;}
        .inventory-v104-tooltip-layout footer b{color:var(--theme-text-strong);}
        @media(max-width:580px){
            .inventory-v101-content.inventory-v104-cast-content{padding:20px 17px 26px;}
            .inventory-v104-cast-resource{display:none;}
            .inventory-v104-cast-stats{gap:8px 12px;}
            .inventory-v104-cast-stats>div:not(:last-child):after{display:none;}
            .inventory-v104-cast-stats>div{padding:5px 8px;border:1px solid var(--theme-border-soft);border-radius:6px;}
            .inventory-v104-cast-facts>div{display:block;}
            .inventory-v104-cast-facts dt,.inventory-v104-cast-facts dd{display:inline;}
            .inventory-v104-tooltip-facts>div{grid-template-columns:78px minmax(0,1fr);gap:8px;}
        }

        /* SPELLBOUND V66.2.106 — Leitura semântica de magias (5etools).
           A estética de texto é compartilhada por Inventário e Grimório;
           apenas as cores são herdadas dos oito temas. Não altera o Cofre. */
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher > div,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra > div,
            .character-spellbook-v4 .spell-reader-copy) {
            font-family: system-ui, -apple-system, 'Segoe UI', sans-serif !important;
            font-size: 14px !important;
            line-height: 1.75 !important;
            letter-spacing: 0 !important;
            color: inherit;
            overflow-wrap: anywhere;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) :is(p, li, blockquote) {
            line-height: 1.75 !important;
            white-space: normal;
            color: inherit;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher > div,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra > div,
            .character-spellbook-v4 .spell-reader-copy) :is(p, blockquote) {
            margin: 0 0 1.05em !important;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) :is(h4, .sb-spell-subheading) {
            display: block;
            margin: 1.28em 0 .50em !important;
            padding: 0;
            color: var(--theme-text-strong, inherit) !important;
            font: 800 14.5px/1.35 Georgia, serif !important;
            text-transform: none;
            letter-spacing: .005em;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) :is(h4, .sb-spell-subheading) + p {
            margin-top: 0 !important;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) :is(ul, ol) {
            margin: .5em 0 1.20em !important;
            padding-inline-start: 1.5em !important;
            list-style: disc outside;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) li { margin-bottom: .50em !important; }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) :is(strong, b) {
            font-weight: 800;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) blockquote {
            border-inline-start: 2px solid var(--theme-border-strong);
            padding-inline-start: 14px;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) table {
            width:100%; border-collapse:collapse;
            margin: 14px 0 19px;
            font-size:12px; line-height:1.55;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) :is(th,td) {
            padding:8px 10px; border-bottom:1px solid var(--theme-border-soft);
            text-align:left; vertical-align:top;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) th {
            color:var(--theme-text-strong); font-weight:800;
        }
        /* Mesma moldura discreta de leitura do Grimório; paleta local intacta. */
        .inventory-v102-reader-panel { position: relative; }
        .inventory-v102-reader-panel::before,
        .inventory-v102-reader-panel::after {
            content: '';
            position: absolute;
            z-index: 3;
            width: 15px;
            height: 15px;
            border-color: var(--theme-border-strong, var(--theme-border));
            opacity: .58;
            pointer-events: none;
        }
        .inventory-v102-reader-panel::before {
            top: 8px;
            left: 8px;
            border-top: 1px solid;
            border-left: 1px solid;
        }
        .inventory-v102-reader-panel::after {
            right: 8px;
            bottom: 8px;
            border-right: 1px solid;
            border-bottom: 1px solid;
        }
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra,
            .character-spellbook-v4 .spell-reader-copy) li > p {
            display: inline;
            margin: 0 !important;
        }
        .character-spellbook-v4 .spell-reader-copy > :last-child,
        :is(.inventory-v104-cast-description,
            .inventory-v104-cast-higher,
            .inventory-v102-reader-description,
            .inventory-v102-reader-extra) > :last-child { margin-bottom:0 !important; }

        /* V66.2.105 — Consumíveis esgotados com duas habilidades.
           O card padrão continua com 240px. Apenas o card cheio ganha
           espaço para as duas linhas e o botão Descartar sem cortes.
           Não altera as paletas dos oito temas. */
        .character-sheet-theme[data-theme] .inventory-v35
        .inventory-v48-item-card.inventory-v105-discard-packed {
            height: auto !important;
            min-height: 276px !important;
            max-height: none !important;
        }
        .character-sheet-theme[data-theme] .inventory-v35
        .inventory-v48-item-card.inventory-v105-discard-packed .inventory-v48-abilities {
            flex: 1 0 auto !important;
            min-height: 124px !important;
        }
        </style>
        @endpush

        <script>
        // Catálogo existente do Grimório: consulta apenas o feitiço selecionado,
        // sem adicionar a magia à lista do personagem ou consumir slots.
        window.__spellboundScrollDetailsCache ??= new Map();
        window.spellboundScrollResolve = async (scroll, catalogUrl) => {
            const snapshot = {...(scroll?.spell_info || {})};
            if (!scroll?.spell_name) return snapshot;
            // V66.2.106: consultar a estrutura atual do Grimório mesmo quando
            // o pergaminho antigo guardou a descrição achatada no snapshot.
            // O Map abaixo evita múltiplas consultas pelo mesmo feitiço.
            if (!catalogUrl) return snapshot;
            const key = catalogUrl + '|' + String(scroll?.spell_id || '') + '|' + String(scroll.spell_name);
            const cache = window.__spellboundScrollDetailsCache;
            if (!cache.has(key)) {
                cache.set(key, (async () => {
                    try {
                        const url = new URL(catalogUrl, window.location.origin);
                        url.searchParams.set('q', String(scroll.spell_name).trim());
                        url.searchParams.set('per_page', '60');
                        const response = await fetch(url, {headers:{Accept:'application/json'},cache:'no-store'});
                        if (!response.ok) return null;
                        const json = await response.json();
                        const entries = Array.isArray(json.data) ? json.data : [];
                        return entries.find(x => Number(x.id) === Number(scroll.spell_id)) ||
                            entries.find(x => String(x.name).toLocaleLowerCase() === String(scroll.spell_name).toLocaleLowerCase()
                                && Number(x.level) === Number(scroll.spell_level)) || null;
                    } catch (_) { return null; }
                })());
            }
            const spell = await cache.get(key);
            if (!spell) return snapshot;
            return {
                ...snapshot,
                school: snapshot.school || spell.school_pt || spell.school || '',
                casting_time: snapshot.casting_time || spell.casting_time_display || spell.casting_time || '',
                range: snapshot.range || spell.range_display || spell.range || '',
                components: snapshot.components || spell.components || '',
                duration: snapshot.duration || spell.duration_display || spell.duration || '',
                concentration: snapshot.concentration === true || spell.concentration === true,
                ritual: snapshot.ritual === true || spell.ritual === true,
                description_text: snapshot.description_text || spell.description || '',
                // O HTML reconstituído do catálogo preserva os blocos
                // originais do 5etools; texto salvo permanece como fallback.
                description_html: spell.description_html || snapshot.description_html || '',
                higher_level_text: snapshot.higher_level_text || spell.higher_level_text || '',
                higher_level_html: spell.higher_level_html || snapshot.higher_level_html || '',
                higher_level_label: snapshot.higher_level_label || spell.higher_level_label || '',
                source_label: snapshot.source_label || spell.source_label || '',
            };
        };
        window.spellboundScrollSubtitle = (scroll, info) => {
            const level = Number(scroll?.spell_level || 0);
            const label = level === 0 ? 'Truque' : level + 'º círculo';
            const rawSchool = info?.school || '';
            const schools = {Abjuration:'Abjuração',Conjuration:'Conjuração',Divination:'Adivinhação',Enchantment:'Encantamento',Evocation:'Evocação',Illusion:'Ilusão',Necromancy:'Necromancia',Transmutation:'Transmutação'};
            const school = schools[rawSchool] || rawSchool;
            return label + (school ? ' (' + school + ')' : '');
        };
        // Renderização com lista de tags permitidas, sem atributos executáveis.
        window.spellboundScrollSafeRich = (value, max = 30000) => {
            const raw = String(value ?? '').slice(0, max).trim();
            if (!raw) return '';
            const esc = v => String(v).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            if (!/<[a-z][\s\S]*?>/i.test(raw)) {
                return raw.split(/\n\s*\n/).map(s => '<p>' + esc(s).replace(/\n/g,'<br>') + '</p>').join('');
            }
            const doc = new DOMParser().parseFromString(raw,'text/html');
            const allowed = new Set(['p','strong','em','b','i','u','s','ul','ol','li','blockquote','h2','h3','h4','br','hr','table','thead','tbody','tr','th','td','caption']);
            const blocked = new Set(['script','style','iframe','object','embed','svg','math','template','form','textarea','img','video','audio']);
            const clean = node => {
                if (node.nodeType === 3) return esc(node.textContent);
                if (node.nodeType !== 1) return '';
                const tag = node.tagName.toLowerCase();
                if (blocked.has(tag)) return '';
                // As definições dos termos do Grimório são popovers próprios;
                // não podem cair no corpo do texto quando removemos os spans.
                if (node.classList?.contains('spell-rule-tooltip')) return '';
                const body = Array.from(node.childNodes).map(clean).join('');
                if (!allowed.has(tag)) return body;
                if (tag === 'br' || tag === 'hr') return '<'+tag+'>';
                const attrs = tag === 'th' ? ' scope="col"' : (tag === 'p' && node.classList?.contains('sb-spell-subheading') ? ' class="sb-spell-subheading"' : '');
                return '<'+tag+attrs+'>'+body+'</'+tag+'>';
            };
            return Array.from(doc.body.childNodes).map(clean).join('');
        };
        window.SpellboundInventoryScrollDetail = (item, catalogUrl) => ({
            item, scroll:item.properties.spell_scroll,
            info:{...(item.properties.spell_scroll?.spell_info || {})},loading:false,catalogUrl,
            async hydrate(){
                this.loading=true;
                try {this.info = await window.spellboundScrollResolve(this.scroll,this.catalogUrl);}
                finally {this.loading=false;}
            },
            rulesIndex(){return (this.item?.properties?.features||[]).findIndex(f=>window.spellboundIsScrollRules(this.item,f));},
            isConsumed(){return !!this.scroll?.consumed || this.remainingUses()<1;},
            remainingUses(){
                if (this.scroll?.consumed)return 0;
                const feature = this.item.properties?.features?.[this.rulesIndex()];
                return feature?.usage?.enabled ? Math.max(0,Number(feature.usage.current)||0) : (this.rulesIndex()>=0?1:0);
            },
            stats(){
                const defaults=[[13,5],[13,5],[13,5],[15,7],[15,7],[17,9],[17,9],[18,10],[18,10],[19,11]];
                const level=Math.max(0,Math.min(9,Number(this.scroll?.spell_level)||0));
                const n=(v,f)=>v===undefined||v===null||v===''?f:(Number.isFinite(Number(v))?Number(v):f);
                const attack=n(this.scroll?.spell_attack_bonus,defaults[level][1]);
                return {dc:n(this.scroll?.spell_dc,defaults[level][0]),attack:(attack>=0?'+':'')+attack};
            },
            levelLabel(){const level=Number(this.scroll?.spell_level||0);return level===0?'Truque':level+'º';},
            subtitle(){return window.spellboundScrollSubtitle(this.scroll,this.info);},
            tooltipHtml(){
                return window.spellboundScrollSkillTooltip(this.item,{system_key:'spell_scroll_cast',title:this.scroll?.spell_name},this.info).description;
            },
            requestCast(){
                const index=this.rulesIndex();
                if(index>=0 && !this.isConsumed())window.dispatchEvent(new CustomEvent('spellbound-scroll-request',{detail:{item:this.item,index}}));
            },
            readSpell(){window.dispatchEvent(new CustomEvent('spellbound-scroll-read',{detail:{item:this.item,info:this.info}}));},
        });
        window.SpellboundInventoryScrollReader = (catalogUrl) => ({
            opened:false,scroll:null,info:{},loading:false,catalogUrl,
            async openReader(payload){
                if(!payload?.item?.properties?.spell_scroll)return;
                this.scroll=payload.item.properties.spell_scroll;
                this.info={...this.scroll.spell_info,...(payload.info||{})};
                this.opened=true;this.loading=true;
                const scroll=this.scroll;
                try {const data=await window.spellboundScrollResolve(scroll,this.catalogUrl);
                    if(this.opened && this.scroll===scroll)this.info={...data,...(payload.info||{})};
                }finally{this.loading=false;}
            },
            closeReader(){this.opened=false;this.scroll=null;this.info={};this.loading=false;},
        });
        window.spellboundIsScrollRules = (item, feature) => {
            const scroll = item?.properties?.spell_scroll;
            if (!scroll || !String(scroll.spell_name || '').trim()) return false;
            return feature?.system_key === 'spell_scroll_rules'
                || String(feature?.title || '').trim() === 'Uso e Conjuração de Pergaminhos';
        };
        window.spellboundIsScrollCast = (item, feature) => {
            const scroll = item?.properties?.spell_scroll;
            if (!scroll?.spell_name) return false;
            return feature?.system_key === 'spell_scroll_cast'
                || String(feature?.title || '').startsWith(String(scroll.spell_name));
        };
        // O tooltip do card é deliberadamente curto. O verbete completo está no leitor.
        // Não passamos HTML pré-formatado ao formatItemRichText() novamente.
        window.spellboundScrollSkillTooltip = (item, feature, infoOverride = null) => {
            if (!window.spellboundIsScrollCast(item, feature)) return feature;
            const scroll = item?.properties?.spell_scroll || {};
            const info = infoOverride || scroll.spell_info || {};
            const level = Number(scroll.spell_level);
            const circle = Number.isFinite(level) && level > 0 ? `${level}º círculo` : 'Truque';
            const facts = [
                ['Conjuração', info.casting_time],
                ['Alcance', info.range],
                ['Componentes', info.components],
                ['Duração', info.duration ? (info.duration + (info.concentration && !/concentra/i.test(info.duration) ? ' · Concentração' : '')) : (info.concentration ? 'Concentração' : '')],
            ].filter(([,value]) => String(value ?? '').trim()).map(([label,value]) => `${label}: ${String(value).trim()}`);
            let text = String(info.description_text || '').trim();
            if (!text && info.description_html) {
                const doc = new DOMParser().parseFromString(String(info.description_html), 'text/html');
                doc.body.querySelectorAll('script,style,iframe,object,svg').forEach(node=>node.remove());
                text = doc.body.textContent || '';
            }
            text = text.replace(/\s+/g, ' ').trim();
            const excerpt = text ? `${text.slice(0, 220)}${text.length > 220 ? '…' : ''}` : '';
            return {
                ...feature,
                title: `${String(scroll.spell_name || feature?.title || 'Feitiço')} · ${circle}`,
                description: [facts.join('\n'),excerpt].filter(Boolean).join('\n\n') || 'Abra o item para ler as informações do feitiço.',
            };
        };

        // Verbete resumido e espaçado para o hover. Sem HTML misturado aos rótulos.
        window.spellboundScrollTooltipModel = (item, feature, infoOverride = null) => {
            const scroll = item?.properties?.spell_scroll || {};
            const info = infoOverride || scroll.spell_info || {};
            const facts = [
                {label:'Conjuração', value:info.casting_time},
                {label:'Alcance', value:info.range},
                {label:'Componentes', value:info.components},
                {label:'Duração', value: info.duration
                    ? (info.duration + (info.concentration && !/concentra/i.test(info.duration) ? ' (Concentração)' : ''))
                    : (info.concentration ? 'Concentração' : '')},
            ].filter(row => String(row.value ?? '').trim()).map(row => ({label:row.label, value:String(row.value).trim()}));
            let raw = String(info.description_text || '').trim();
            if (!raw && info.description_html) {
                const doc = new DOMParser().parseFromString(String(info.description_html),'text/html');
                doc.body.querySelectorAll('script,style,iframe,object,embed,svg').forEach(node=>node.remove());
                raw = doc.body.textContent || '';
            }
            const plain = raw.replace(/<[^>]+>/g,' ').replace(/\s+/g,' ').trim();
            const short = plain.slice(0,290);
            const excerpt = short.length < plain.length
                ? short.replace(/\s+\S*$/,'').trimEnd() + '…' : short;
            return {
                itemId: item?.id,
                name: String(scroll.spell_name || feature?.title || 'Feitiço'),
                subtitle: window.spellboundScrollSubtitle(scroll,info),
                facts,
                excerpt,
            };
        };

        window.SpellboundInventoryScrollCast = (urls) => ({
            modalOpen:false,busy:false,error:'',item:null,featureIndex:0,scroll:null,
            confirmReplace:false,concentration:{active:false},urls,
            enrichedInfo:{},infoLoading:false,
            async openDialog(detail) {
                if (!detail?.item) return;
                this.item = detail.item;
                this.featureIndex = Number(detail.index) || 0;
                this.scroll = this.item?.properties?.spell_scroll || null;
                this.enrichedInfo = {...(this.scroll?.spell_info || {})};
                this.infoLoading = true;
                window.spellboundScrollResolve(this.scroll, this.urls.catalogUrl)
                    .then(info => { if (this.modalOpen && this.item?.id === detail.item.id) this.enrichedInfo = info; })
                    .finally(() => this.infoLoading = false);
                this.error = '';
                this.busy = false;
                this.confirmReplace = false;
                this.concentration = {active:false};
                this.modalOpen = true;
                this.$nextTick(() => {
                    const content = this.$el?.querySelector('.inventory-v101-content');
                    if (content) content.scrollTop = 0;
                });
                try {
                    const url=this.urls.concentrationUrl+(this.urls.concentrationUrl.includes('?')?'&':'?')+'scope=concentration';
                    const response=await fetch(url,{headers:{Accept:'application/json'},cache:'no-store'});
                    if (!response.ok) throw new Error('Não foi possível consultar a concentração.');
                    const data=await response.json();
                    if (!this.modalOpen) return;
                    this.concentration = data?.concentration || {active:false};
                } catch(e) { this.error=e.message || 'Não foi possível consultar a concentração.'; }
            },
            closeDialog() { if (this.busy) return;this.modalOpen=false;this.item=null;this.scroll=null;this.error=''; },
            remainingUses() {
                if (!this.item || this.scroll?.consumed) return 0;
                const usage=this.item?.properties?.features?.[this.featureIndex]?.usage;
                return usage?.enabled ? Math.max(0, Number(usage.current)||0) : 1;
            },
            scrollStats() {
                const defaults=[[13,5],[13,5],[13,5],[15,7],[15,7],[17,9],[17,9],[18,10],[18,10],[19,11]];
                const level=Math.max(0,Math.min(9,Number(this.scroll?.spell_level)||0));
                const dcValue=this.scroll?.spell_dc;
                const attackValue=this.scroll?.spell_attack_bonus;
                const dc=dcValue===null||dcValue===''||dcValue===undefined?defaults[level][0]:Number(dcValue);
                const attack=attackValue===null||attackValue===''||attackValue===undefined?defaults[level][1]:Number(attackValue);
                return {dc:Number.isFinite(dc)?dc:defaults[level][0],attack:(attack>=0?'+':'')+String(Number.isFinite(attack)?attack:defaults[level][1])};
            },
            async cast() {
                if (!this.item || this.busy || this.remainingUses()<1) return;
                if (this.infoLoading || (!!this.enrichedInfo?.concentration && !!this.concentration?.active && !this.confirmReplace)) return;
                if (!this.scroll?.spell_id) {this.error='Este pergaminho precisa de um feitiço vinculado do Grimório.';return;}
                this.busy=true;this.error='';
                try {
                    const path=this.urls.castUrl.replace('__ITEM__',String(this.item.id)).replace('__FEATURE__',String(this.featureIndex));
                    const response=await fetch(path,{
                        method:'PATCH',headers:{'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || ''},
                        body:JSON.stringify({scroll_cast:true,confirm_replace_concentration:!!this.confirmReplace}),
                    });
                    const data=await response.json().catch(()=>({}));
                    if (!response.ok) {
                        const errors=data?.errors?Object.values(data.errors).flat().filter(Boolean):[];
                        throw new Error(errors.join(' ') || data?.message || 'Não foi possível conjurar.');
                    }
                    const inventoryRoot=document.querySelector('.inventory-v10[x-data]');
                    const inventory=inventoryRoot?window.Alpine?.$data?.(inventoryRoot):null;
                    if (data.item) inventory?.upsertItem?.(data.item);
                    if (inventory) inventory.inventorySkillTooltip=null;
                    inventory?.showToast?.(data.message || 'Pergaminho conjurado.');
                    window.dispatchEvent(new CustomEvent('character-equipment-updated',{detail:{item:data.item}}));
                    window.dispatchEvent(new CustomEvent('spellbound-spell-cast',{detail:{spell_id:data.cast?.spell_id,cast_level:data.cast?.cast_level,source:'scroll',item_id:this.item.id,spell_save_dc:data.cast?.spell_save_dc,spell_attack_bonus:data.cast?.spell_attack_bonus}}));
                    window.dispatchEvent(new CustomEvent('character-concentration-changed'));
                    window.dispatchEvent(new CustomEvent('spellbound-scroll-cast-done',{detail:{timers:data.scroll_timers||[]}}));
                    this.busy=false;this.closeDialog();
                }catch(e){this.error=e.message || 'Não foi possível conjurar.';}finally{this.busy=false;}
            }
        });
        window.SpellboundInventoryScrollTimers = (syncUrl) => ({
            timers:[],expanded:false,interval:null,syncUrl,
            start() {this.load();this.interval=setInterval(()=>this.load(),5000);},
            destroy() {if(this.interval!==null) clearInterval(this.interval);this.interval=null;},
            async load() {
                if(document.hidden)return;
                try {
                    const url=this.syncUrl+(this.syncUrl.includes('?')?'&':'?')+'scroll_timers_only=1';
                    const response=await fetch(url,{headers:{Accept:'application/json'},cache:'no-store'});
                    if(!response.ok)return;
                    const data=await response.json();
                    if(data?.success)this.timers=Array.isArray(data.scroll_timers)?data.scroll_timers:[];
                    if(!this.timers.length)this.expanded=false;
                }catch(e){}
            }
        });
        </script>
