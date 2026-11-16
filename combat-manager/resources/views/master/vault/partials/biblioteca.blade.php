        <div class="master-vault-v9-shell">
            <header class="master-vault-v9-commandbar master-vault-v14-topbar">
                <div class="master-vault-v14-context">
                    <template x-if="!activeFolder">
                        <div class="master-vault-v14-context-home">
                            <div class="master-vault-v12-brand-copy">
                                <h1>Cofre de Itens</h1>

                            </div>
                        </div>
                    </template>

                    <template x-if="activeFolder">
                        <div class="master-vault-v14-context-folder">
                            <button
                                type="button"
                                class="master-vault-v14-back"
                                @click="closeFolder()"
                                title="Voltar ao Cofre"
                                aria-label="Voltar ao Cofre"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M15 18l-6-6 6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <div class="master-vault-v14-folder-copy">
                                <h1 x-text="activeFolder?.name"></h1>
                                <p x-text="activeFolder?.description || 'Itens organizados nesta pasta.'"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="master-vault-v11-actions">
                    <button
                        x-show="!activeFolder && folderSystemReady"
                        x-cloak
                        type="button"
                        class="master-vault-v11-folder-button"
                        @click="openFolderCreate()"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4">
                            <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="M12 12v4M10 14h4" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        Pasta
                    </button>

                    <button
                        x-show="activeFolder"
                        x-cloak
                        type="button"
                        class="master-vault-v11-folder-button"
                        @click="openFolderEdit(activeFolder)"
                    >
                        Editar pasta
                    </button>

                    <button type="button" class="master-vault-v9-create" @click="openCreate()">
                        <span class="text-base leading-none">+</span>
                        Novo Item
                    </button>
                </div>

                <label class="master-vault-v9-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="6.5" stroke-width="1.7"/>
                        <path d="m16 16 4 4" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>

                    <input
                        type="search"
                        x-model.debounce.180ms="search"
                        :placeholder="activeFolder ? 'Buscar nesta pasta...' : 'Buscar item por nome, descrição ou propriedade...'"
                    >
                </label>

                <div class="master-vault-view-controls">
                <label class="master-vault-v9-filter">
                    <select x-model="filter" aria-label="Filtrar itens do Cofre">
                        <template x-for="option in filterOptions" :key="option.key">
                            <option :value="option.key" x-text="option.label"></option>
                        </template>
                    </select>
                </label>
                <button type="button" class="master-vault-display-toggle" x-show="showVaultTabs" x-cloak
                        @click="toggleDisplayMode()"
                        :aria-pressed="displayMode === 'separate'"
                        :aria-label="displayMode === 'separate' ? 'Mostrar itens e pastas juntos' : 'Separar itens e pastas em abas'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="7" height="16" rx="1.5"/>
                        <rect x="14" y="4" width="7" height="16" rx="1.5"/>
                    </svg>
                    <span x-text="displayMode === 'separate' ? 'Mostrar tudo' : 'Separar em abas'"></span>
                </button>
                </div>

            </header>

            {{-- Mesmo modelo do Cofre de NPCs: visualização conjunta ou abas com preferência por conta. --}}
            <nav class="master-vault-display-row"
                 :class="{ 'is-combined': displayMode === 'combined' }"
                 x-show="showVaultTabs && displayMode === 'separate'" x-cloak
                 aria-label="Visualização do Cofre de Itens">
                <div class="master-vault-display-tabs" role="tablist"
                     aria-label="Seções do Cofre"
                     x-show="displayMode === 'separate'" x-cloak>
                    <button type="button" id="vault-tab-items" role="tab" x-ref="vaultItemsTab"
                            class="master-vault-display-tab"
                            :class="{ 'is-active': activeVaultTab === 'items' }"
                            :aria-selected="activeVaultTab === 'items'"
                            aria-controls="vault-items-panel"
                            :tabindex="activeVaultTab === 'items' ? 0 : -1"
                            @click="selectVaultTab('items')"
                            x-on:keydown.arrow-right.prevent="selectVaultTab('folders'); $nextTick(() => $refs.vaultFoldersTab.focus())">
                        Itens soltos <span x-text="unassignedCount"></span>
                    </button>
                    <button type="button" id="vault-tab-folders" role="tab"
                            class="master-vault-display-tab" x-ref="vaultFoldersTab"
                            :class="{ 'is-active': activeVaultTab === 'folders' }"
                            :aria-selected="activeVaultTab === 'folders'"
                            aria-controls="vault-folders-panel"
                            :tabindex="activeVaultTab === 'folders' ? 0 : -1"
                            @click="selectVaultTab('folders')"
                            x-on:keydown.arrow-left.prevent="selectVaultTab('items'); $nextTick(() => $refs.vaultItemsTab.focus())">
                        Pastas <span x-text="folders.length"></span>
                    </button>
                </div>

            </nav>

            <div
                x-show="showItemResultsSection && !(showVaultTabs && displayMode === 'separate')"
                x-cloak
                class="master-vault-v9-results vault-overview-items"
                :class="{ 'is-inside-folder': activeFolder }"
            >
                <div>
                    <strong x-text="resultHeading"></strong>

                    <span
                        x-text="
                            filteredItems.length
                            + (
                                filteredItems.length === 1
                                    ? ' resultado'
                                    : ' resultados'
                            )
                        "
                    ></span>
                </div>

                <button
                    x-show="filter !== 'all' || search"
                    x-cloak
                    type="button"
                    class="master-vault-v9-clear"
                    @click="
                        filter = 'all';
                        search = '';
                    "
                >
                    Limpar filtros
                </button>
            </div>

            <div
                x-show="showItemResultsSection && filteredItems.length > 0"
                id="vault-items-panel"
                role="region"
                aria-label="Itens do Cofre"
                class="master-vault-v9-grid vault-unassigned-grid vault-v35-animated-grid"
                :class="{ 'vault-v35-view-enter': viewAnimating }"
                x-transition.opacity.duration.220ms
            >
                <template
                    x-for="item in filteredItems"
                    :key="item.id"
                >
                    <article
                        class="master-vault-v10-card"
                        :data-nature="item.type"
                        :data-rarity="item.rarity || 'common'"
                        :class="{ 'is-vestige': isVestige(item), 'is-dragging': draggedItemId === parseInt(item.id), 'is-moving': movingItemId === parseInt(item.id), 'is-arriving': arrivedItemId === parseInt(item.id), 'is-menu-open': actionMenuId === parseInt(item.id) }"
                        draggable="true"
                        @dragstart.stop="startItemDrag(item, $event)"
                        @dragend="draggedItemId = null; dragOverFolderId = null"
                        tabindex="0"
                        role="button"
                        :aria-label="'Visualizar ' + item.name"
                        @click="openPreview(item)"
                        @keydown.enter.prevent="openPreview(item)"
                    >
                        {{-- V66.1.79: reflexo de ouro acetinado SOMENTE no interior. --}}
                        <span x-show="item.type === 'wonderful' && item.rarity === 'artifact'"
                              x-cloak class="vault-v79-liquid-gold" aria-hidden="true"></span>
                        <div class="master-vault-v10-card-main">
                            <div class="master-vault-v10-thumb vault-v35-portrait">
                                <span class="vault-v35-portrait-corner" aria-hidden="true"></span>
                                <span class="vault-v43-nature-tag"
                                      :data-nature="item.type"
                                      :title="'Natureza: ' + natureLabel(item.type)"
                                      role="img"
                                      :aria-label="'Natureza: ' + natureLabel(item.type)">
                                    <span x-show="item.type === 'wonderful'" x-cloak aria-hidden="true"
                                          class="vault-v36-nature-icon vault-v36-nature-icon--magical">✦</span>
                                    <span x-show="item.type === 'technological'" x-cloak aria-hidden="true"
                                          class="vault-v36-nature-icon vault-v36-nature-icon--technology"></span>
                                    <span x-show="item.type === 'mundane'" x-cloak aria-hidden="true"
                                          class="vault-v36-nature-icon vault-v36-nature-icon--mundane"></span>
                                </span>
                                <!-- Somente Artefatos Tecnológicos: a base visual permanece idêntica ao Lendário. -->
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
                                <span x-show="isVestige(item)" x-cloak class="vault-v35-portrait-seal">✦ VESTÍGIO</span>
                                <template x-if="item.image_url">
                                    <img
                                        :src="item.image_url"
                                        :alt="item.name"
                                    >
                                </template>

                                <template x-if="!item.image_url">
                                    <svg
                                        class="h-8 w-8"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M5 6.5h14v11H5z"
                                            stroke-width="1.5"
                                        />
                                        <path
                                            d="m7.5 15 3.2-3.2 2.3 2.3 1.7-1.7 2.8 2.6"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </template>
                            </div>

                            <div class="master-vault-v10-identity">
                                <h2
                                    class="master-vault-v10-name"
                                    x-text="item.name"
                                ></h2>

                                <p
                                    class="master-vault-v10-nature"
                                    x-text="cardNatureLine(item)"
                                ></p>

                                <p
                                    x-show="cardCategoryLine(item)"
                                    x-cloak
                                    class="master-vault-v10-category"
                                    x-text="cardCategoryLine(item)"
                                ></p>
                            </div>

                            <div class="master-vault-v10-top-actions" @click.stop>
                                <div class="master-vault-v10-menu-wrap"
                                     @click.outside="actionMenuId === parseInt(item.id) ? closeActionMenu() : null">
                                    <button type="button"
                                            class="master-vault-v10-top-button vault-v35-menu-trigger"
                                            @click="toggleActionMenu(item)"
                                            :aria-expanded="actionMenuId === parseInt(item.id)"
                                            :aria-label="'Opções de ' + item.name"
                                            title="Opções do item">
                                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <circle cx="12" cy="5" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="12" cy="19" r="1.7"/>
                                        </svg>
                                    </button>

                                    <div x-show="actionMenuId === parseInt(item.id)" x-cloak
                                         x-transition:enter="vault-v35-menu-enter"
                                         x-transition:enter-start="vault-v35-menu-from"
                                         x-transition:enter-end="vault-v35-menu-to"
                                         x-transition:leave="vault-v35-menu-enter"
                                         x-transition:leave-start="vault-v35-menu-to"
                                         x-transition:leave-end="vault-v35-menu-from"
                                         class="master-vault-v10-menu vault-v35-action-menu">
                                        <button type="button" x-show="folderSystemReady" x-cloak
                                                @click="toggleMovePicker(item)"
                                                :aria-expanded="movePickerItemId === parseInt(item.id)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true"><path d="M3 7h7l2 2h9v10H3z" stroke-linejoin="round"/><path d="M12 12v5m-2.5-2.5h5" stroke-linecap="round"/></svg>
                                            Enviar para pasta
                                            <span class="vault-v35-menu-chevron" aria-hidden="true">⌄</span>
                                        </button>
                                        <div x-show="movePickerItemId === parseInt(item.id)" x-cloak
                                             class="vault-v35-move-list" aria-label="Escolher pasta">
                                            <button type="button" @click="moveItemFromMenu(item, null)" :disabled="!item.folder_id">Itens soltos</button>
                                            <template x-for="folder in folders" :key="'move-to-'+item.id+'-'+folder.id">
                                                <button type="button" @click="moveItemFromMenu(item, folder.id)"
                                                        :disabled="parseInt(item.folder_id) === parseInt(folder.id)">
                                                    <span class="vault-v35-move-dot" :style="{ background: folder.color || '#8a4b20' }"></span>
                                                    <span x-text="folder.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <button type="button" @click="editFromMenu(item)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true"><path d="m4 20 4-1 10-10-3-3L5 16zM14 7l3 3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Editar item
                                        </button>
                                        <button type="button" @click="downloadItemJson(item)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M5 17v3h14v-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Baixar JSON
                                        </button>
                                        <button type="button" @click="requestDuplicate(item)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true"><path d="M8 8h11v11H8zM5 16H4V4h12v1" stroke-linejoin="round"/></svg>
                                            Criar cópia
                                        </button>
                                        <button type="button" class="is-danger" @click="requestDelete(item)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v6m4-6v6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <section
                            x-show="item.description"
                            class="master-vault-v10-section"
                        >
                            <div
                                class="master-vault-v10-description vault-v34-card-rich-excerpt"
                                :title="String(item.description || '').replace(/<[^>]*>/g, ' ').replace(/&nbsp;/gi, ' ').replace(/\s+/g, ' ').trim()"
                                x-html="formatVaultText(item.description)"
                            ></div>
                        </section>

                        <section
                            x-show="isVestige(item) || cardFeatures(item).length > 0"
                            class="master-vault-v10-section vault-v35-features-section"
                        >
                            <div x-show="isVestige(item)" x-cloak class="vault-v35-vestige-summary">
                                <span class="vault-v35-vestige-summary-icon" aria-hidden="true">✦</span>
                                <span class="vault-v39-vestige-copy"><strong>Habilidades do Vestígio</strong></span>
                                <span class="vault-v35-vestige-summary-arrow" aria-hidden="true">›</span>
                            </div>
                            <div x-show="!isVestige(item)" class="master-vault-v10-abilities">
                                <template
                                    x-for="
                                        (feature, featureIndex) in
                                        cardFeatures(item).slice(0, 2)
                                    "
                                    :key="
                                        'card-feature-'
                                        + item.id
                                        + '-'
                                        + featureIndex
                                    "
                                >
                                    <span
                                        class="master-vault-v10-ability"
                                        x-text="
                                            feature.title
                                            || ('Habilidade ' + (featureIndex + 1))
                                        "
                                    ></span>
                                </template>

                                <span
                                    x-show="cardFeatures(item).length > 2"
                                    class="master-vault-v10-ability master-vault-v10-ability-more"
                                    x-text="
                                        '+'
                                        + (
                                            cardFeatures(item).length - 2
                                        )
                                    "
                                ></span>
                            </div>

                        </section>

                    </article>
                </template>
            </div>

            <section
                x-show="showItemResultsSection && filteredItems.length === 0"
                x-cloak
                class="master-vault-v8-empty"
            >
                <span class="master-vault-v8-empty-mark">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            d="M5 7h14v12H5zM8 4h8v3"
                            stroke-width="1.6"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M9 12h6"
                            stroke-width="1.6"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <h3 x-text="emptyItemsHeading"></h3>
                <p x-text="emptyItemsDescription"></p>

                <button
                    x-show="items.length === 0"
                    type="button"
                    class="master-vault-v9-create mt-5"
                    @click="openCreate()"
                >
                    + Criar primeiro item
                </button>
            </section>

            <section
                x-show="showFolderOverview && folderSystemReady"
                x-cloak
                x-transition.opacity.duration.200ms
                id="vault-folders-panel"
                role="region"
                aria-label="Pastas do Cofre"
                class="master-vault-v11-folder-section vault-overview-folders"
                :class="{ 'is-separated': displayMode === 'separate', 'vault-v35-view-enter': viewAnimating }"
            >
                <div class="master-vault-v11-folder-head" :class="{ 'is-separated': displayMode === 'separate' && showVaultTabs }">
                    <div class="master-vault-v11-folder-headline">
                        <div>
                            <h2>Pastas do Cofre</h2>
                            <p>Arraste seus itens para organizá-los em pastas.</p>
                        </div>
                    </div>

                    <span class="master-vault-v11-folder-badge" x-text="folders.length + ' ' + (folders.length === 1 ? 'Pasta' : 'Pastas')"></span>
                </div>

                <div class="master-vault-v11-folder-grid" :class="{ 'is-separated': displayMode === 'separate' && showVaultTabs }">
                    <template x-for="folder in folders" :key="'vault-folder-' + folder.id">
                        <article
                            class="master-vault-v11-folder-card vault-v25-folder"
                            :class="{ 'is-drag-target': dragOverFolderId === parseInt(folder.id), 'vault-v35-folder-received': receivedFolderId === parseInt(folder.id) }"
                            :style="folderCardStyle(folder)"
                            role="button"
                            tabindex="0"
                            :aria-label="'Abrir pasta ' + folder.name"
                            @click="openFolder(folder)"
                            @keydown.enter.self.prevent="openFolder(folder)"
                            @keydown.space.self.prevent="openFolder(folder)"
                            @dragover.prevent="dragOverFolderId = parseInt(folder.id)"
                            @dragleave="dragOverFolderId = null"
                            @drop.prevent.stop="dropItemIntoFolder(folder)"
                        >
                            <div>
                                <div class="master-vault-v11-folder-top">
                                    <div class="master-vault-v11-folder-title">
                                        <span class="master-vault-v11-folder-icon" :style="folderIconStyle(folder)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                                            </svg>
                                        </span>

                                        <div class="min-w-0">
                                            <span class="vault-v25-folder-eyebrow">Pasta do Cofre</span>
                                            <h3 x-text="folder.name"></h3>
                                            <p class="master-vault-v11-folder-description" x-text="folder.description || ''"></p>
                                        </div>
                                    </div>

                                    <div class="master-vault-v11-folder-menu-wrap" @click.stop>
                                        <button type="button" class="master-vault-v11-folder-menu-button"
                                                @click="toggleFolderMenu(folder.id)"
                                                :aria-expanded="folderMenuId === parseInt(folder.id)"
                                                :aria-label="'Opções da pasta ' + folder.name">⋮</button>

                                        <div
                                            x-show="folderMenuId === folder.id"
                                            x-cloak
                                            x-transition.opacity.duration.100ms
                                            class="master-vault-v11-folder-menu-panel"
                                            @click.outside="closeFolderMenu()"
                                        >
                                            <button type="button" @click="openFolder(folder)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 7h7l2 2h9v11H3z" stroke-linejoin="round"/></svg>Abrir pasta</button>
                                            <button type="button" @click="openFolderEdit(folder)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m4 20 4-1 10-10-3-3L5 16zM14 7l3 3"/></svg>Editar pasta</button>
                                            <button type="button" class="is-danger" @click="deleteFolder(folder)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 7h14M9 7V4h6v3M7 7l1 13h8l1-13"/></svg>Excluir pasta</button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <footer class="master-vault-v11-folder-foot vault-v25-folder-shelf">
                                <div class="vault-v25-folder-shelf-content">
                                    <span class="vault-v25-folder-shelf-label">Itens guardados</span>
                                    <div class="master-vault-v11-folder-preview">
                                        <template x-if="folderPreviewItems(folder.id).length === 0">
                                            <span class="master-vault-v11-folder-avatar is-empty">Nenhum item ainda</span>
                                        </template>

                                        <template x-for="item in folderPreviewItems(folder.id)" :key="'folder-preview-' + folder.id + '-' + item.id">
                                            <span class="master-vault-v11-folder-avatar" :class="{ 'is-text': !item.image_url }">
                                                <template x-if="item.image_url">
                                                    <img :src="item.image_url" :alt="item.name">
                                                </template>
                                                <template x-if="!item.image_url">
                                                    <span x-text="String(item.name ?? '?').trim().charAt(0).toUpperCase() || '?' "></span>
                                                </template>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                                <div class="vault-v25-folder-open">
                                    <span class="master-vault-v11-folder-count" x-text="folderItemBadge(folder.id)"></span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                        <path d="M5 12h13m-5-5 5 5-5 5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </footer>
                        </article>
                    </template>
                </div>
            </section>

        </div>

        {{-- Destinos para arrastar itens mesmo quando a visualização está separada em abas. --}}
        <aside x-show="draggedItemId !== null && folderSystemReady && displayMode === 'separate'"
               x-cloak
               x-transition:enter="vault-drawer-transition"
               x-transition:enter-start="vault-drawer-hidden"
               x-transition:enter-end="vault-drawer-visible"
               x-transition:leave="vault-drawer-transition"
               x-transition:leave-start="vault-drawer-visible"
               x-transition:leave-end="vault-drawer-hidden"
               class="master-vault-drag-destinations"
               aria-label="Mover item para uma pasta">
            <header class="vault-drop-drawer__head">
                <span class="vault-drop-drawer__eyebrow">COFRE DE ITENS</span>
                <h3>Guardar em pasta</h3>
                <p>Arraste o item até o destino desejado.</p>
                <div class="vault-drop-drawer__selected">
                    <span>✦</span>
                    <strong x-text="items.find(item => parseInt(item.id) === parseInt(draggedItemId))?.name || 'Item selecionado'"></strong>
                </div>
            </header>
            <div class="master-vault-drop-target master-vault-drop-target--library"
                 :class="{ 'is-over': dragOverFolderId === 'library' }"
                 @dragover.prevent="dragOverFolderId = 'library'"
                 @dragleave="dragOverFolderId = null"
                 @drop.prevent.stop="dropDraggedItemToLibrary()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path d="M4 5h16v14H4zM7 9h10M7 13h7" stroke-linecap="round"/>
                </svg>
                <span>Itens soltos (sem pasta)</span>
            </div>
            <div class="vault-drop-drawer__destinations">
            <template x-for="folder in folders" :key="'vault-drag-destination-' + folder.id">
                <div class="master-vault-drop-target"
                     :class="{ 'is-over': dragOverFolderId === folder.id }"
                     @dragover.prevent="dragOverFolderId = folder.id"
                     @dragleave="dragOverFolderId = null"
                     @drop.prevent.stop="dropItemIntoFolder(folder)">
                    <svg viewBox="0 0 24 24" fill="none" :stroke="folder.color || '#926849'" stroke-width="1.8" aria-hidden="true">
                        <path d="M3 7h6l2 2h10v10H3z" stroke-linejoin="round"/>
                    </svg>
                    <span x-text="folder.name"></span>
                </div>
            </template>
            </div>
            <footer class="vault-drop-drawer__foot">Solte sobre a pasta desejada</footer>
        </aside>

        {{-- =========================================================
             CONFIRMAÇÃO DE AÇÃO
        ========================================================== --}}
        <template x-teleport="body">
            <div
                x-show="confirmOpen && confirmItem"
                x-cloak
                class="master-vault-v10-confirm-layer"
                x-transition.opacity.duration.120ms
                role="dialog"
                aria-modal="true"
                aria-label="Confirmar ação do Vault"
            >
                <div
                    class="master-vault-v10-confirm-backdrop"
                    @click="closeConfirm()"
                ></div>

                <section
                    class="master-vault-v10-confirm"
                    x-transition:enter="transition ease-out duration-160"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-[.985]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                >
                    <div class="master-vault-v10-confirm-body">
                        <span class="master-vault-v10-confirm-mark">
                            <svg
                                x-show="confirmMode === 'duplicate'"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <rect
                                    x="8"
                                    y="8"
                                    width="10"
                                    height="10"
                                    rx="2"
                                    stroke-width="1.6"
                                />
                                <path
                                    d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"
                                    stroke-width="1.6"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <svg
                                x-show="confirmMode === 'delete'"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    d="M5 7h14M9 7V4h6v3M8 10v8M12 10v8M16 10v8M7 7l1 14h8l1-14"
                                    stroke-width="1.55"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>

                        <h3 x-text="confirmTitle()"></h3>

                        <p x-text="confirmMessage()"></p>
                    </div>

                    <footer class="master-vault-v10-confirm-foot">
                        <button
                            type="button"
                            class="master-vault-v10-confirm-cancel"
                            @click="closeConfirm()"
                            :disabled="confirmBusy"
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            class="master-vault-v10-confirm-ok"
                            :class="{
                                'is-danger':
                                    confirmMode === 'delete'
                            }"
                            @click="confirmVaultAction()"
                            :disabled="confirmBusy"
                        >
                            <span
                                x-show="!confirmBusy"
                                x-text="
                                    confirmMode === 'delete'
                                        ? 'Excluir Item'
                                        : 'Criar Cópia'
                                "
                            ></span>

                            <span
                                x-show="confirmBusy"
                                x-cloak
                            >
                                Processando...
                            </span>
                        </button>
                    </footer>
                </section>
            </div>
        </template>

