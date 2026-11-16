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
                @click="openCreate()"
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
                                            x-on:click.stop="toggleEquipped(item)"
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
                                            x-on:mouseenter="showInventorySkillTooltip(entry.feature, $event)"
                                            x-on:mouseleave="inventorySkillTooltip = null"
                                        >
                                            <span
                                                class="inventory-v48-skill-name"
                                                :title="entry.feature.description ? '' : (entry.feature.title || 'Habilidade')"
                                                x-text="entry.feature.title || 'Habilidade'"
                                            ></span>

                                            <template x-if="entry.feature?.usage?.enabled && !entry.vestige">
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
                                            <template x-if="!entry.feature?.usage?.enabled || entry.vestige">
                                                <span
                                                    class="inventory-v48-passive"
                                                    x-text="entry.vestige ? vestigeFeatureUsageLabel(entry.feature) : 'Passiva'"
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

        {{-- Um único tooltip no body: não é recortado pelos cards ou pela gaveta. --}}
        <template x-teleport="body">
            <div
                x-show="inventorySkillTooltip !== null"
                x-cloak
                data-theme="{{ $characterTheme }}"
                class="character-sheet-theme inventory-v49-skill-tooltip"
                :style="inventorySkillTooltip ? {
                    left: inventorySkillTooltip.left + 'px',
                    top: inventorySkillTooltip.top + 'px',
                    transform: inventorySkillTooltip.above ? 'translateY(-100%)' : 'none'
                } : {}"
                role="tooltip"
                aria-live="off"
            >
                <strong x-text="inventorySkillTooltip?.name || ''"></strong>
                <p x-html="formatItemRichText(inventorySkillTooltip?.description || '')"></p>
                <small>Abra o item para consultar a habilidade completa.</small>
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
