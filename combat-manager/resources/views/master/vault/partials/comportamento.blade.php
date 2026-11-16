                    items: Array.isArray(initialItems) ? initialItems : [],
                    folders: Array.isArray(initialFolders) ? initialFolders : [],
                    weaponTraitDefinitions: Array.isArray(weaponTraitDefinitions) ? weaponTraitDefinitions : [],
                    masteryDefinitions: Array.isArray(masteryDefinitions) ? masteryDefinitions : [],
                    abilityLabels: abilityLabels ?? {},
                    recoveryLabels: recoveryLabels ?? {},
                    damageTypeDefinitions: Array.isArray(damageTypeDefinitions) ? damageTypeDefinitions : [],
                    urls,
                    filter: 'all',
                    search: '',
                    // Visualização compartilhada com o padrão do Cofre de NPCs.
                    displayMode: 'separate',
                    activeVaultTab: 'items',
                    displayUserId: String(folderConfig?.userId ?? 'guest'),
                    activeFolderId: null,
                    formFolderId: null,
                    folderSystemReady: !!folderConfig?.ready,
                    folderScopeId: parseInt(folderConfig?.scopeId) || null,
                    folderModalOpen: false,
                    folderEditingId: null,
                    folderForm: { name: '', description: '', color: '#8A4B20' },
                    folderPalette: [
                        { key: 'couro', label: 'Couro', color: '#8A4B20' },
                        { key: 'carmesim', label: 'Carmesim', color: '#8A2314' },
                        { key: 'ambar', label: 'Âmbar', color: '#B56A14' },
                        { key: 'floresta', label: 'Floresta', color: '#2E6B41' },
                        { key: 'arcano', label: 'Arcano', color: '#1F6F73' },
                        { key: 'oceano', label: 'Oceano', color: '#355C8A' },
                        { key: 'mistico', label: 'Místico', color: '#6C3A7F' },
                        { key: 'magia', label: 'Magia', color: '#983B69' },
                        { key: 'sombra', label: 'Sombra', color: '#5E5B58' },
                        { key: 'obsidian', label: 'Obsidian', color: '#2E2E31' },
                    ],
                    folderSaving: false,
                    folderError: null,
                    folderMenuId: null,
                    // V66.2.66 — metadados do Grimório local, sem conteúdo editorial copiado.
                    spellCatalog: Array.isArray(initialSpellCatalog) ? initialSpellCatalog.map(spell => ({
                        id: spell.id,
                        name: String(spell.name ?? spell.nome ?? spell.title ?? ''),
                        level: Number(spell.level ?? spell.nivel ?? 0),
                        level_label: Number(spell.level ?? spell.nivel ?? 0) === 0 ? 'Truque' : `${Number(spell.level ?? spell.nivel ?? 0)}º nível`,
                        source: String(spell.source ?? 'PERSONALIZADO').trim().toUpperCase(),
                        provider: String(spell.provider ?? 'local').trim().toLowerCase(),
                        is_official: spell.is_official === true,
                        extra_key: String(spell.provider ?? 'local').trim().toLowerCase() + '|' + String(spell.source ?? 'PERSONALIZADO').trim().toUpperCase(),
                        school: String(spell.school ?? '').trim().toLowerCase(),
                        casting_time: String(spell.casting_time ?? '').trim(),
                        range: String(spell.range ?? '').trim(),
                        duration: String(spell.duration ?? '').trim(),
                        ritual: !!spell.ritual,
                        concentration: !!spell.concentration,
                        description: String(spell.description ?? ''),
                        description_data: spell.description_data ?? null,
                        higher_level_text: String(spell.higher_level_text ?? ''),
                        higher_level_data: spell.higher_level_data ?? null,
                        components: spell.components ?? null,
                        // HTML já processado pelo SpellDisplayService do Grimório.
                        description_html: '',
                        higher_level_html: '',
                        higher_level_label: '',
                        subtitle_pt: '',
                        source_label: '',
                        casting_time_display: '',
                        range_display: '',
                        duration_display: '',
                        components_display: '',
                        scaling_level_dice: [],
                        _detail_loaded: false,
                        _detail_loading: false,
                        _detail_error: false,
                    })).filter(spell => spell.id != null && spell.name
                        && spell.source !== 'PHB') : [],
                    spellCampaigns: Array.isArray(initialSpellCampaigns) ? initialSpellCampaigns.map(campaign => ({
                        id: String(campaign.id),
                        name: String(campaign.name ?? ''),
                        allow_all: !!campaign.allow_all,
                        sources: Array.isArray(campaign.sources) ? campaign.sources.map(String) : [],
                    })).filter(campaign => campaign.id && campaign.name) : [],
                    spellSourceNames: initialSpellSourceNames && typeof initialSpellSourceNames === 'object'
                        ? initialSpellSourceNames : {},
                    scrollCampaignId: '',
                    scrollInspectBookKey: '',
                    scrollInspectLimit: 18,
                    spellSearch: '',
                    scrollQuickSearch: '',
                    scrollQuickFocused: false,
                    scrollBookModalOpen: false,
                    scrollLibraryTab: 'spells',
                    scrollBooksOpen: false,
                    scrollInspectSearch: '',
                    _scrollBooksCache: null,
                    _scrollBookSpellsCache: null,
                    scrollBookSearch: '',
                    scrollExtraBooks: [],
                    scrollLevelFilter: 'all',
                    scrollSchoolFilter: 'all',
                    scrollPreviewId: null,
                    scrollListLimit: 60,
                    weightGramsText: '', 
                    draggedItemId: null,
                    dragOverFolderId: null,

                    previewOpen: false,
                    previewItem: null,

                    actionMenuId: null,
                    movePickerItemId: null,
                    movingItemId: null,
                    arrivedItemId: null,
                    receivedFolderId: null,
                    viewAnimating: false,
                    viewAnimationTimer: null,
                    moveFeedbackTimer: null,

                    confirmOpen: false,
                    confirmMode: null,
                    confirmItem: null,
                    confirmBusy: false,

                    editorOpen: false,
                    editorTab: 'general',
                    vestigeEditorStage: 'dormant',
                    editingId: null,
                    form: null,
                    imageFile: null,
                    imagePreview: null,
                    cropOpen: false,
                    cropSource: null,
                    cropTempUrl: null,
                    cropReady: false,
                    cropSaving: false,
                    cropZoom: 1,
                    cropFrameSize: 248,
                    cropBaseW: 248,
                    cropBaseH: 248,
                    cropOffsetX: 0,
                    cropOffsetY: 0,
                    cropPointer: null,
                    saving: false,
                    saveError: null,
                    featureEditorSequence: 0,
                    busyId: null,
                    toastOpen: false,
                    toastMessage: '',
                    toastError: false,
                    toastTimer: null,

                    filterOptions: [
                        { key: 'all', label: 'Todos' },
                        { key: 'wonderful', label: 'Maravilhosos' },
                        { key: 'technological', label: 'Tecnológicos' },
                        { key: 'mundane', label: 'Mundanos' },
                    ],

                    init() {
                        this.items = this.items.map(item => this.normalizeItem(item));
                        this.folders = this.folders.map(folder => this.normalizeFolder(folder));
                        this.restoreDisplayPreference();
                    },

                    get displayPreferenceKey() {
                        return 'spellbound:items:preferred-display:' + this.displayUserId;
                    },

                    restoreDisplayPreference() {
                        try {
                            const saved = JSON.parse(window.localStorage.getItem(this.displayPreferenceKey) || 'null');
                            if (!saved || !['combined', 'separate'].includes(saved.mode)) return;
                            this.displayMode = saved.mode;
                            this.activeVaultTab = saved.tab === 'folders' ? 'folders' : 'items';
                        } catch (_) {
                            // Armazenamento bloqueado não impede abrir o Cofre.
                        }
                    },

                    saveDisplayPreference() {
                        try {
                            window.localStorage.setItem(this.displayPreferenceKey, JSON.stringify({
                                mode: this.displayMode,
                                tab: this.activeVaultTab,
                            }));
                        } catch (_) {
                            // A preferência permanece funcional durante a sessão.
                        }
                    },

                    pulseVaultView() {
                        // Reinicia a transição mesmo em alternâncias rápidas.
                        this.viewAnimating = false;
                        if (this.viewAnimationTimer) window.clearTimeout(this.viewAnimationTimer);
                        window.setTimeout(() => {
                            this.viewAnimating = true;
                            this.viewAnimationTimer = window.setTimeout(() => {
                                this.viewAnimating = false;
                            }, 420);
                        }, 16);
                    },

                    toggleDisplayMode() {
                        this.displayMode = this.displayMode === 'combined' ? 'separate' : 'combined';
                        this.closeActionMenu();
                        this.pulseVaultView();
                        this.saveDisplayPreference();
                    },

                    selectVaultTab(tab) {
                        if (!['items', 'folders'].includes(tab)) return;
                        if (this.activeVaultTab === tab) return;
                        this.activeVaultTab = tab;
                        this.closeActionMenu();
                        this.pulseVaultView();
                        this.saveDisplayPreference();
                    },

                    get showVaultTabs() {
                        return this.activeFolderId === null && !this.hasActiveSearch && this.folderSystemReady;
                    },

                    get activeFolder() {
                        if (this.activeFolderId === null) return null;
                        return this.folders.find(
                            folder => parseInt(folder.id) === parseInt(this.activeFolderId)
                        ) ?? null;
                    },

                    get unassignedCount() {
                        return this.items.filter(item => !item.folder_id).length;
                    },

                    get hasActiveSearch() {
                        return this.filter !== 'all' || this.normalizeSearch(this.search) !== '';
                    },

                    get showFolderOverview() {
                        if (this.activeFolderId !== null || this.hasActiveSearch) return false;
                        if (!this.folderSystemReady) return false;
                        return this.displayMode === 'combined' || this.activeVaultTab === 'folders';
                    },

                    get showItemResultsSection() {
                        // A pesquisa global e o interior de uma pasta não dependem da aba escolhida.
                        if (this.activeFolderId !== null || this.hasActiveSearch) return true;
                        if (this.folderSystemReady && this.displayMode === 'separate') {
                            return this.activeVaultTab === 'items';
                        }
                        // Na exibição conjunta, Itens soltos continua presente mesmo quando vazio.
                        return true;
                    },

                    get resultHeading() {
                        if (this.hasActiveSearch) return 'Resultados';
                        if (this.activeFolder) return 'Itens';
                        return 'Itens sem pasta';
                    },

                    get filteredItems() {
                        const query = this.normalizeSearch(this.search);
                        const queryTerms = query
                            .split(/\s+/)
                            .map(term => term.trim())
                            .filter(Boolean);

                        return [...this.items]
                            .filter(item => {
                                if (this.activeFolderId !== null) {
                                    return parseInt(item.folder_id) === parseInt(this.activeFolderId);
                                }

                                if (!this.hasActiveSearch) {
                                    return !item.folder_id;
                                }

                                return true;
                            })
                            .filter(item => {
                                if (['wonderful', 'technological', 'mundane'].includes(this.filter)) return item.type === this.filter;
                                return true;
                            })
                            .filter(item => {
                                if (!queryTerms.length) return true;

                                const haystack = this.itemSearchHaystack(item);

                                /*
                                 * Cada palavra digitada precisa existir em algum
                                 * ponto do item, mas não precisa estar adjacente.
                                 *
                                 * Ex.: "arma raro" encontra uma arma rara mesmo
                                 * que "Arma" esteja na categoria e "Raro" na raridade.
                                 */
                                return queryTerms.every(
                                    term => haystack.includes(term)
                                );
                            })
                            .sort((a, b) => {
                                return String(a.name ?? '').localeCompare(String(b.name ?? ''), 'pt-BR');
                            });
                    },

                    get emptyItemsHeading() {
                        if (this.activeFolder) return 'Esta pasta está vazia';
                        if (this.hasActiveSearch) return 'Nenhum item encontrado';
                        return this.items.length ? 'Nenhum item solto' : 'Seu Cofre está vazio';
                    },

                    get emptyItemsDescription() {
                        if (this.activeFolder) return 'Arraste um item até esta pasta ou escolha uma pasta ao editar.';
                        if (this.hasActiveSearch) return 'Ajuste a busca ou o filtro para encontrar outros itens.';
                        return this.items.length
                            ? 'Todos os itens estão organizados. Acesse a aba Pastas para encontrá-los.'
                            : 'Crie o primeiro item para começar sua biblioteca.';
                    },

                    get previewIndex() {
                        if (!this.previewItem) {
                            return -1;
                        }

                        return this.filteredItems.findIndex(
                            item =>
                                parseInt(item.id)
                                === parseInt(this.previewItem.id)
                        );
                    },

                    get hasPreviousPreview() {
                        return this.previewIndex > 0;
                    },

                    get hasNextPreview() {
                        return (
                            this.previewIndex >= 0
                            && this.previewIndex < this.filteredItems.length - 1
                        );
                    },

                    normalizeFolder(raw) {
                        return {
                            id: parseInt(raw?.id),
                            name: raw?.name ?? '',
                            description: raw?.description ?? '',
                            color: raw?.color ?? '#8A4B20',
                            text_color: raw?.text_color ?? null,
                            image_url: raw?.image_url ?? null,
                            position: parseInt(raw?.position) || 0,
                            items_count: parseInt(raw?.items_count) || 0,
                        };
                    },

                    rgbaFromHex(hex, alpha = 1) {
                        const normalized = String(hex ?? '').replace('#', '').trim();
                        if (!/^[0-9A-Fa-f]{6}$/.test(normalized)) {
                            return `rgba(138, 75, 32, ${alpha})`;
                        }

                        const r = parseInt(normalized.slice(0, 2), 16);
                        const g = parseInt(normalized.slice(2, 4), 16);
                        const b = parseInt(normalized.slice(4, 6), 16);
                        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                    },

                    folderAccent(folder) {
                        return folder?.color || '#8A4B20';
                    },

                    folderCardStyle(folder) {
                        const accent = this.folderAccent(folder);
                        return {
                            '--folder-accent': accent,
                        };
                    },

                    folderIconStyle(folder) {
                        const accent = this.folderAccent(folder);
                        return {
                            color: accent,
                            background: this.rgbaFromHex(accent, .10),
                        };
                    },

                    folderPreviewItems(folderId) {
                        return this.items
                            .filter(item => parseInt(item.folder_id) === parseInt(folderId))
                            .slice(0, 5);
                    },

                    folderPlaceholderLetter(folder) {
                        return String(folder?.name ?? '?').trim().charAt(0).toUpperCase() || '?';
                    },

                    folderItemBadge(folderId) {
                        const count = this.folderItemCount(folderId);
                        return `${count} ${count === 1 ? 'item' : 'itens'}`;
                    },

                    toggleFolderMenu(folderId) {
                        const normalizedId = parseInt(folderId);
                        this.folderMenuId = this.folderMenuId === normalizedId ? null : normalizedId;
                    },

                    closeFolderMenu() {
                        this.folderMenuId = null;
                    },

                    folderItemCount(folderId) {
                        return this.items.filter(
                            item => parseInt(item.folder_id) === parseInt(folderId)
                        ).length;
                    },

                    openFolder(folder) {
                        this.activeFolderId = parseInt(folder.id);
                        this.search = '';
                        this.filter = 'all';
                        this.closeActionMenu();
                        this.closeFolderMenu();
                        this.pulseVaultView();
                    },

                    closeFolder() {
                        this.activeFolderId = null;
                        this.search = '';
                        this.filter = 'all';
                        this.closeFolderMenu();
                        this.pulseVaultView();
                    },

                    openFolderCreate() {
                        if (!this.folderSystemReady) {
                            this.showToast('O sistema de pastas ainda não foi migrado. Rode php artisan migrate.', true);
                            return;
                        }

                        this.folderEditingId = null;
                        this.folderForm = {
                            name: '',
                            description: '',
                            color: this.folderPalette?.[0]?.color ?? '#8A4B20',
                        };
                        this.folderError = null;
                        this.closeFolderMenu();
                        this.folderModalOpen = true;
                    },

                    openFolderEdit(folder) {
                        this.folderEditingId = parseInt(folder.id);
                        this.folderForm = {
                            name: folder.name ?? '',
                            description: folder.description ?? '',
                            color: folder.color ?? (this.folderPalette?.[0]?.color ?? '#8A4B20'),
                        };
                        this.folderError = null;
                        this.closeFolderMenu();
                        this.folderModalOpen = true;
                    },

                    closeFolderModal() {
                        if (this.folderSaving) return;
                        this.folderModalOpen = false;
                        this.folderEditingId = null;
                        this.folderError = null;
                        this.folderForm = {
                            name: '',
                            description: '',
                            color: this.folderPalette?.[0]?.color ?? '#8A4B20',
                        };
                    },

                    async saveFolder() {
                        if (this.folderSaving) return;

                        const name = String(this.folderForm?.name ?? '').trim();
                        if (!name) {
                            this.folderError = 'Informe um nome para a pasta.';
                            return;
                        }

                        this.folderSaving = true;
                        this.folderError = null;

                        const editing = this.folderEditingId !== null;
                        const url = editing
                            ? this.urls.folderUpdate.replace('__FOLDER__', this.folderEditingId)
                            : this.urls.folderStore;

                        const formData = new FormData();
                        if (!editing) {
                            formData.append('scope_type', 'master.vault');
                            formData.append('scope_id', String(this.folderScopeId));
                        }
                        formData.append('name', name);
                        formData.append('description', String(this.folderForm?.description ?? '').trim());
                        formData.append('color', String(this.folderForm?.color ?? (this.folderPalette?.[0]?.color ?? '#8A4B20')));

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: this.headers(),
                                body: formData,
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) {
                                const errors = data?.errors ? Object.values(data.errors).flat().filter(Boolean) : [];
                                throw new Error(errors.length ? errors.join(' ') : (data?.message ?? 'Não foi possível salvar a pasta.'));
                            }

                            const folder = this.normalizeFolder(data.folder);
                            const index = this.folders.findIndex(current => parseInt(current.id) === parseInt(folder.id));
                            if (index >= 0) this.folders.splice(index, 1, folder);
                            else this.folders.push(folder);

                            this.folders.sort((a, b) => (a.position - b.position) || String(a.name).localeCompare(String(b.name), 'pt-BR'));
                            this.folderSaving = false;
                            this.closeFolderModal();
                            this.showToast(editing ? 'Pasta atualizada.' : 'Pasta criada.');
                        } catch (error) {
                            this.folderError = error?.message ?? 'Não foi possível salvar a pasta.';
                            this.folderSaving = false;
                        }
                    },

                    async deleteFolder(folder) {
                        if (!folder || this.folderSaving) return;
                        this.closeFolderMenu();
                        if (!window.confirm(`Excluir a pasta "${folder.name}"? Os itens continuarão no Cofre e serão movidos para Sem pasta.`)) return;

                        this.folderSaving = true;
                        try {
                            const response = await fetch(
                                this.urls.folderDestroy.replace('__FOLDER__', folder.id),
                                { method: 'DELETE', headers: this.headers() }
                            );
                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível excluir a pasta.');

                            this.items.forEach(item => {
                                if (parseInt(item.folder_id) === parseInt(folder.id)) item.folder_id = null;
                            });
                            this.folders = this.folders.filter(current => parseInt(current.id) !== parseInt(folder.id));
                            if (parseInt(this.activeFolderId) === parseInt(folder.id)) this.activeFolderId = null;
                            this.showToast('Pasta excluída. Os itens foram movidos para Sem pasta.');
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível excluir a pasta.', true);
                        } finally {
                            this.folderSaving = false;
                        }
                    },

                    startItemDrag(item, event) {
                        this.draggedItemId = parseInt(item.id);
                        if (event?.dataTransfer) {
                            event.dataTransfer.effectAllowed = 'move';
                            event.dataTransfer.setData('text/plain', String(item.id));
                        }
                    },

                    async dropDraggedItemToLibrary() {
                        const itemId = parseInt(this.draggedItemId);
                        if (!itemId) return;
                        this.draggedItemId = null;
                        this.dragOverFolderId = null;
                        try {
                            await this.moveVaultItem(itemId, null);
                            this.showToast('Item movido para itens soltos.');
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível mover o item.', true);
                        }
                    },

                    async dropItemIntoFolder(folder) {
                        const itemId = parseInt(this.draggedItemId);
                        if (!itemId || !folder) return;
                        this.draggedItemId = null;
                        this.dragOverFolderId = null;

                        try {
                            await this.moveVaultItem(itemId, folder.id);
                            this.showToast(`Item movido para ${folder.name}.`);
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível mover o item.', true);
                        }
                    },

                    async moveVaultItem(itemId, folderId, updateForm = false) {
                        if (!this.folderSystemReady) return null;
                        const numericItem = parseInt(itemId);
                        const requestedFolder = folderId ? parseInt(folderId) : null;
                        const item = this.items.find(current => parseInt(current.id) === numericItem);
                        if (!item) return null;
                        if ((item.folder_id ? parseInt(item.folder_id) : null) === requestedFolder) return null;

                        this.movingItemId = numericItem;
                        try {
                            // Um breve deslocamento comunica a saída do card antes da atualização.
                            await new Promise(resolve => window.setTimeout(resolve, 135));
                            const response = await fetch(this.urls.moveFolder, {
                                method: 'PATCH',
                                headers: {
                                    ...this.headers(),
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    scope_type: 'master.vault',
                                    scope_id: this.folderScopeId,
                                    item_type: 'vault_item',
                                    item_id: numericItem,
                                    folder_id: requestedFolder,
                                }),
                            });
                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível mover o item.');

                            const destination = data.folder_id ? parseInt(data.folder_id) : null;
                            item.folder_id = destination;
                            if (updateForm) this.formFolderId = destination;
                            this.arrivedItemId = numericItem;
                            this.receivedFolderId = destination;
                            this.pulseVaultView();
                            if (this.moveFeedbackTimer) window.clearTimeout(this.moveFeedbackTimer);
                            this.moveFeedbackTimer = window.setTimeout(() => {
                                this.arrivedItemId = null;
                                this.receivedFolderId = null;
                            }, 650);
                            return data;
                        } finally {
                            this.movingItemId = null;
                        }
                    },

                    // V66.1.14 — definições de Vestígio no Cofre do Mestre.
                    // A visibilidade do jogador DEVE ser filtrada no backend de inventário.
                    blankVestige() {
                        return {
                            enabled: false,
                            current_stage: 'dormant',
                            stages: {
                                dormant: { lore: '', features: [] },
                                awakened: { lore: '', features: [] },
                                exalted: { lore: '', features: [] },
                            },
                        };
                    },

                    normalizeVestige(raw) {
                        const defaults = this.blankVestige();
                        const source = raw && typeof raw === 'object' && !Array.isArray(raw) ? raw : {};
                        const validStages = ['dormant', 'awakened', 'exalted'];
                        const stages = {};
                        validStages.forEach(stage => {
                            const original = source.stages?.[stage] ?? {};
                            stages[stage] = {
                                lore: String(original.lore ?? '').slice(0, 30000),
                                features: (Array.isArray(original.features) ? original.features : [])
                                    .map(feature => ({
                                        title: String(feature?.title ?? '').slice(0, 120),
                                        description: String(feature?.description ?? '').slice(0, 10000),
                                        editor_open: !!feature?.editor_open,
                                        _editor_uid: feature?._editor_uid || 'vault-feature-' + (++this.featureEditorSequence),
                                        usage: this.normalizeFeatureUsage(feature?.usage),
                                    })),
                            };
                        });
                        return {
                            enabled: this.toBoolean(source.enabled),
                            // Campo legado: compatibilidade com o contrato atual da API.
                            // Não determina o grau do Vestígio na ficha do Player.
                            current_stage: validStages.includes(source.current_stage) ? source.current_stage : defaults.current_stage,
                            stages,
                        };
                    },

                    get editingVestige() {
                        return this.form?.type === 'wonderful'
                            && this.form?.rarity === 'artifact'
                            && !!this.form?.properties?.vestige?.enabled;
                    },

                    // Os mesmos editores completos de habilidades e trackers são
                    // utilizados tanto para itens comuns quanto para cada etapa do Vestígio.
                    get editingAbilityFeatures() {
                        if (this.editingVestige) {
                            const stage = ['dormant', 'awakened', 'exalted'].includes(this.vestigeEditorStage)
                                ? this.vestigeEditorStage : 'dormant';
                            return this.form?.properties?.vestige?.stages?.[stage]?.features ?? [];
                        }
                        return this.form?.properties?.features ?? [];
                    },

                    // Edita as três etapas do Vestígio sem misturar suas habilidades.
                    get editingAbilityGroups() {
                        if (this.editingVestige) {
                            return [
                                { key: 'dormant', label: 'Dormente', order: 'I', range: 'Proficiência +2 a +3' },
                                { key: 'awakened', label: 'Desperta', order: 'II', range: 'Proficiência +4 a +5' },
                                { key: 'exalted', label: 'Exaltada', order: 'III', range: 'Proficiência +6' },
                            ].map(stage => ({
                                ...stage,
                                features: this.form.properties.vestige.stages[stage.key].features,
                            }));
                        }
                        return [{ key: 'normal', label: 'Habilidades do Item', features: this.form?.properties?.features ?? [] }];
                    },

                    featuresForEditorGroup(groupKey = null) {
                        const stage = groupKey || (this.editingVestige ? this.vestigeEditorStage : 'normal');
                        if (['dormant', 'awakened', 'exalted'].includes(stage) && this.editingVestige) {
                            return this.form?.properties?.vestige?.stages?.[stage]?.features ?? null;
                        }
                        return this.form?.properties?.features ?? null;
                    },

                    get editingVestigeStageInfo() {
                        return {
                            dormant: { label: 'Dormente', range: 'Proficiência +2 a +3' },
                            awakened: { label: 'Desperta', range: 'Proficiência +4 a +5' },
                            exalted: { label: 'Exaltada', range: 'Proficiência +6' },
                        }[this.vestigeEditorStage] ?? { label: 'Dormente', range: 'Proficiência +2 a +3' };
                    },

                    toggleVestige() {
                        if (!this.form?.properties) return;
                        const vestige = this.normalizeVestige(this.form.properties.vestige);
                        vestige.enabled = !vestige.enabled;
                        this.form.properties.vestige = vestige;
                        if (vestige.enabled) this.vestigeEditorStage = 'dormant';
                    },

                    addVestigeFeature(stage) {
                        if (!this.editingVestige || !this.form?.properties?.vestige?.stages?.[stage]) return;
                        const features = this.form.properties.vestige.stages[stage].features;
                        features.push(this.blankFeature());
                    },

                    removeVestigeFeature(stage, index) {
                        this.form?.properties?.vestige?.stages?.[stage]?.features?.splice(index, 1);
                    },

                    vestigeStageLabel(stage) {
                        return ({ dormant: 'Dormente', awakened: 'Desperta', exalted: 'Exaltada' })[stage] || 'Dormente';
                    },

                    isVestige(item) {
                        return item?.rarity === 'artifact' && item?.properties?.vestige?.enabled === true;
                    },

                    // Progressão NORMAL por proficiência: 2–3 Dormente,
                    // 4–5 Desperta e 6+ Exaltada.
                    // A substituição manual é por personagem/item na ficha;
                    // nunca deve alterar a definição global do Cofre.
                    suggestedVestigeStage(proficiency) {
                        const bonus = Number(proficiency);
                        if (!Number.isFinite(bonus)) return 'dormant';
                        if (bonus >= 6) return 'exalted';
                        if (bonus >= 4) return 'awakened';
                        return 'dormant';
                    },

                    natureCount(nature) {
                        return this.items.filter(item => item.type === nature).length;
                    },

                    natureLabel(nature) {
                        return {
                            wonderful: 'Maravilhoso',
                            technological: 'Tecnológico',
                            mundane: 'Mundano',
                        }[nature] ?? 'Item';
                    },

                    rarityLabel(rarity) {
                        return {
                            common: 'Comum',
                            uncommon: 'Incomum',
                            rare: 'Raro',
                            very_rare: 'Muito Raro',
                            legendary: 'Lendário',
                            artifact: 'Artefato',
                        }[rarity] ?? rarity ?? '';
                    },

                    recoveryLabel(recovery) {
                        return this.recoveryLabels?.[recovery]
                            ?? recovery
                            ?? '';
                    },

                    weaponCategoryLabel(category) {
                        return {
                            simple: 'Arma Simples',
                            martial: 'Arma Marcial',
                            custom: 'Arma Custom',
                        }[category] ?? 'Arma';
                    },

                    armorCategoryLabel(category) {
                        return {
                            light: 'Armadura Leve',
                            medium: 'Armadura Média',
                            heavy: 'Armadura Pesada',
                            custom: 'Armadura Especial',
                        }[category] ?? 'Armadura';
                    },

                    previewCategoryLabel(item) {
                        if (item?.properties?.weapon) {
                            return this.weaponCategoryLabel(
                                item.properties.weapon.category
                            );
                        }

                        if (item?.properties?.armor) {
                            return this.armorCategoryLabel(
                                item.properties.armor.category
                            );
                        }

                        if (item?.properties?.shield) {
                            return 'Escudo';
                        }

                        return 'Outro Item';
                    },

                    // Exibe HTML do Tiptap com tags permitidas, mantendo **negrito** legado.
                    formatVaultText(raw) {
                        const source = String(raw ?? '');
                        const escape = value => String(value).replace(/[&<>"']/g, char => ({
                            '&': '&amp;', '<': '&lt;', '>': '&gt;',
                            '"': '&quot;', "'": '&#39;',
                        })[char]);
                        const rich = /<(?:p|br|strong|b|em|i|ul|ol|li|blockquote|h[1-6]|s|del|hr)\b/i.test(source);
                        if (!rich) {
                            const formatted = escape(source.replace(/\r\n?/g, '\n'))
                                .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>');
                            const output = [];
                            let lines = [];
                            const flush = () => { if (lines.length) {output.push('<p>' + lines.join('<br>') + '</p>'); lines = [];} };
                            for (const line of formatted.split('\n')) {
                                if (/^\s*(?:-{3,}|\*{3,}|_{3,})\s*$/.test(line)) {flush(); output.push('<hr>');}
                                else if (!line.trim()) flush();
                                else lines.push(line);
                            }
                            flush();
                            return output.join('');
                        }
                        const documentFragment = new DOMParser().parseFromString('<body>' + source + '</body>', 'text/html');
                        const allowed = new Set(['p', 'br', 'strong', 'em', 's', 'ul', 'ol', 'li', 'blockquote', 'h3', 'h4', 'code', 'hr']);
                        const blocked = new Set(['script', 'style', 'iframe', 'svg', 'math', 'object', 'embed', 'form', 'template']);
                        const clean = node => {
                            if (node.nodeType === 3) return escape(node.textContent)
                                .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
                                .replace(/^\*\*([^*\n]+)$/g, '<strong>$1</strong>')
                                .replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
                            if (node.nodeType !== 1) return '';
                            const tag = node.tagName.toLowerCase();
                            if (blocked.has(tag)) return '';
                            if (tag === 'hr' || ((tag === 'p' || tag === 'div') && /^(?:-{3,}|\*{3,}|_{3,})$/.test(node.textContent.trim()))) return '<hr>';
                            const contents = Array.from(node.childNodes).map(clean).join('');
                            if (tag === 'b') return '<strong>' + contents + '</strong>';
                            if (tag === 'i') return '<em>' + contents + '</em>';
                            if (tag === 'del' || tag === 'strike') return '<s>' + contents + '</s>';
                            return allowed.has(tag) ? '<' + tag + '>' + contents + '</' + tag + '>' : contents;
                        };
                        return Array.from(documentFragment.body.childNodes).map(clean).join('');
                    },


                    // Resumo usado nos cards: formatação rica sem exibir tags HTML.
                    plainVaultText(raw) {
                        const html = this.formatVaultText(raw);
                        const body = new DOMParser().parseFromString(html, 'text/html').body;
                        body.querySelectorAll('hr').forEach(hr => hr.replaceWith(' — '));
                        body.querySelectorAll('p,li,blockquote,h3,h4').forEach(block => block.append(' '));
                        return body.textContent?.replace(/\s+/g, ' ').trim() ?? '';
                    },

                    cardNatureLine(item) {
                        if (!item) return '';

                        const parts = [
                            `Item ${this.natureLabel(item.type)}`,
                        ];

                        if (item.rarity) {
                            parts.push(
                                this.rarityLabel(item.rarity)
                            );
                        }

                        let line =
                            parts.join(', ');

                        if (
                            this.toBoolean(
                                item.requires_attunement
                            )
                        ) {
                            line += ' (Sintonizável)';
                        }

                        return line;
                    },

                    cardCategoryLine(item) {
                        if (!item) return '';

                        if (item?.properties?.spell_scroll) {
                            const scroll = item.properties.spell_scroll;
                            const level = scroll.spell_level === 0 || String(scroll.spell_level) === '0'
                                ? 'Truque' : (Number(scroll.spell_level) > 0
                                    ? Number(scroll.spell_level) + 'º círculo' : 'Nível a definir');
                            return 'Pergaminho de Feitiço · ' + level;
                        }

                        if (item?.properties?.weapon) {
                            const weapon =
                                item.properties.weapon;

                            const category =
                                this.weaponCategoryLabel(
                                    weapon.category
                                );

                            const type =
                                String(
                                    weapon.weapon_type
                                    ?? ''
                                ).trim();

                            const damage =
                                this.weaponBaseDamageDice(
                                    weapon
                                );

                            const damageType =
                                String(
                                    weapon?.damage?.type
                                    ?? ''
                                ).trim();

                            const base =
                                type
                                    ? `${category} - ${type}`
                                    : category;

                            const damageLabel =
                                [damage, damageType]
                                    .filter(Boolean)
                                    .join(' ');

                            return damageLabel
                                ? `${base} (${damageLabel})`
                                : base;
                        }

                        if (item?.properties?.armor) {
                            const armor =
                                item.properties.armor;

                            const category =
                                this.armorCategoryLabel(
                                    armor.category
                                );

                            const type =
                                String(
                                    armor.armor_type
                                    ?? ''
                                ).trim();

                            return type
                                ? `${category} - ${type}`
                                : category;
                        }

                        if (item?.properties?.shield) {
                            const label =
                                String(
                                    item.properties.shield.label
                                    ?? ''
                                ).trim();

                            return label
                                ? `Escudo - ${label}`
                                : 'Escudo';
                        }

                        return '';
                    },

                    vestigePowerCount(item) {
                        if (!this.isVestige(item)) return 0;
                        const stages = item.properties?.vestige?.stages ?? {};
                        return ['dormant', 'awakened', 'exalted'].reduce((total, stage) =>
                            total + (Array.isArray(stages?.[stage]?.features) ? stages[stage].features.length : 0), 0);
                    },

                    cardFeatures(item) {
                        // O Cofre exibe a fase Dormente como referência inicial.
                        // Na ficha do Player a etapa deve ser calculada a partir
                        // da proficiência, com override individual do Mestre.
                        if (this.isVestige(item)) {
                            const vestige = this.normalizeVestige(item.properties.vestige);
                            return vestige.stages?.dormant?.features ?? [];
                        }
                        return Array.isArray(item?.properties?.features)
                            ? item.properties.features : [];
                    },

                    openPreview(item) {
                        if (!item) return;

                        this.previewItem =
                            this.toPlainData(
                                this.normalizeItem(item)
                            );

                        this.previewOpen = true;
                    },

                    closePreview() {
                        this.previewOpen = false;
                        this.previewItem = null;
                    },

                    previewAdjacent(direction) {
                        const index =
                            this.previewIndex;

                        if (
                            index < 0
                            || ![-1, 1].includes(direction)
                        ) {
                            return;
                        }

                        const nextIndex =
                            index + direction;

                        if (
                            nextIndex < 0
                            || nextIndex >= this.filteredItems.length
                        ) {
                            return;
                        }

                        this.openPreview(
                            this.filteredItems[nextIndex]
                        );
                    },

                    editFromPreview() {
                        if (!this.previewItem) return;

                        const item =
                            this.toPlainData(
                                this.previewItem
                            );

                        this.closePreview();
                        this.openEdit(item);
                    },

                    toggleActionMenu(item) {
                        this.movePickerItemId = null;
                        this.closeFolderMenu();
                        const id =
                            parseInt(item?.id);

                        if (!Number.isFinite(id)) {
                            return;
                        }

                        this.actionMenuId =
                            this.actionMenuId === id
                                ? null
                                : id;
                    },

                    closeActionMenu() {
                        this.actionMenuId = null;
                        this.movePickerItemId = null;
                    },

                    toggleMovePicker(item) {
                        const id = parseInt(item?.id);
                        if (!this.folderSystemReady || !Number.isFinite(id)) return;
                        this.movePickerItemId = this.movePickerItemId === id ? null : id;
                    },

                    async moveItemFromMenu(item, folderId) {
                        if (!item || this.busyId !== null) return;
                        this.busyId = item.id;
                        try {
                            await this.moveVaultItem(item.id, folderId);
                            const target = folderId ? this.folders.find(folder => parseInt(folder.id) === parseInt(folderId)) : null;
                            this.showToast(target ? `Item enviado para ${target.name}.` : 'Item movido para itens soltos.');
                            this.closeActionMenu();
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível mover o item.', true);
                        } finally {
                            this.busyId = null;
                        }
                    },

                    downloadItemJson(item) {
                        if (!item) return;
                        const payload = this.toPlainData(this.normalizeItem(item));
                        delete payload.image_url;
                        const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json;charset=utf-8' });
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'spellbound-item-' + String(item.id || 'export') + '.json';
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        window.setTimeout(() => URL.revokeObjectURL(url), 1000);
                        this.closeActionMenu();
                    },

                    editFromMenu(item) {
                        this.closeActionMenu();
                        this.openEdit(item);
                    },

                    requestDuplicate(item) {
                        if (!item) return;

                        this.closeActionMenu();

                        this.confirmMode =
                            'duplicate';

                        this.confirmItem =
                            this.toPlainData(
                                this.normalizeItem(item)
                            );

                        this.confirmOpen =
                            true;
                    },

                    requestDelete(item) {
                        if (!item) return;

                        this.closeActionMenu();

                        this.confirmMode =
                            'delete';

                        this.confirmItem =
                            this.toPlainData(
                                this.normalizeItem(item)
                            );

                        this.confirmOpen =
                            true;
                    },

                    closeConfirm() {
                        if (this.confirmBusy) {
                            return;
                        }

                        this.confirmOpen =
                            false;

                        this.confirmMode =
                            null;

                        this.confirmItem =
                            null;
                    },

                    confirmTitle() {
                        if (this.confirmMode === 'duplicate') {
                            return 'Criar uma cópia?';
                        }

                        if (this.confirmMode === 'delete') {
                            return 'Excluir item do Vault?';
                        }

                        return 'Confirmar ação';
                    },

                    confirmMessage() {
                        const name =
                            this.confirmItem?.name
                            || 'este item';

                        if (this.confirmMode === 'duplicate') {
                            return `Uma nova cópia de "${name}" será criada no Vault. O original não será alterado.`;
                        }

                        if (this.confirmMode === 'delete') {
                            return `"${name}" será removido do Vault. Espólios que já receberam um snapshot não serão alterados.`;
                        }

                        return '';
                    },

                    async confirmVaultAction() {
                        if (
                            this.confirmBusy
                            || !this.confirmItem
                        ) {
                            return;
                        }

                        this.confirmBusy =
                            true;

                        const mode =
                            this.confirmMode;

                        const item =
                            this.toPlainData(
                                this.confirmItem
                            );

                        try {
                            if (mode === 'duplicate') {
                                await this.duplicateItem(
                                    item
                                );
                            }

                            if (mode === 'delete') {
                                await this.deleteVaultItem(
                                    item
                                );
                            }

                            this.confirmOpen =
                                false;

                            this.confirmMode =
                                null;

                            this.confirmItem =
                                null;
                        } finally {
                            this.confirmBusy =
                                false;
                        }
                    },

                    normalizeSearch(value) {
                        return String(value ?? '')
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .toLocaleLowerCase('pt-BR')
                            .trim();
                    },

                    itemSearchHaystack(item) {
                        if (!item) return '';

                        const properties =
                            item?.properties
                            && typeof item.properties === 'object'
                                ? item.properties
                                : {};

                        const weapon = properties?.weapon ?? null;
                        const armor = properties?.armor ?? null;
                        const shield = properties?.shield ?? null;

                        const features = Array.isArray(properties?.features)
                            ? properties.features
                            : [];

                        const customProperties = Array.isArray(properties?.custom_properties)
                            ? properties.custom_properties
                            : [];

                        const weaponTraits = Array.isArray(weapon?.traits)
                            ? weapon.traits
                            : [];

                        const weaponMasteries = Array.isArray(weapon?.masteries)
                            ? weapon.masteries
                            : [];

                        const extraDamage = Array.isArray(weapon?.extra_damage)
                            ? weapon.extra_damage
                            : [];

                        const terms = [
                            item.name,
                            item.description,

                            // Valores crus do banco.
                            item.type,
                            item.rarity,

                            // Labels que o Mestre realmente enxerga.
                            this.natureLabel(item.type),
                            this.rarityLabel(item.rarity),
                            this.cardNatureLine(item),
                            this.cardCategoryLine(item),
                            this.previewCategoryLabel(item),

                            // Tags explícitas do Vault.
                            ...(Array.isArray(item.tags) ? item.tags : []),

                            // Termos semânticos úteis.
                            weapon ? 'arma' : '',
                            armor ? 'armadura' : '',
                            shield ? 'escudo' : '',
                            item.requires_attunement
                                ? 'sintonizacao sintonia sintonizavel'
                                : '',
                            item.is_magical
                                ? 'magico magica magia'
                                : '',

                            // Arma.
                            weapon?.category,
                            weapon ? this.weaponCategoryLabel(weapon.category) : '',
                            weapon?.weapon_type,
                            weapon ? this.weaponBaseDamageDice(weapon) : '',
                            weapon ? this.weaponDamagePreview(weapon) : '',
                            weapon?.damage?.type,
                            weapon?.loading_capacity,

                            ...weaponTraits.flatMap(trait => [
                                trait?.key,
                                trait?.name,
                                trait?.description,
                                this.weaponRuleCompactValue(trait, weapon),
                            ]),

                            ...weaponMasteries.flatMap(mastery => [
                                mastery?.key,
                                mastery?.name,
                                mastery?.description,
                            ]),

                            ...extraDamage.flatMap(damage => [
                                damage?.name,
                                damage?.type,
                                damage?.description,
                            ]),

                            // Armadura / escudo.
                            armor?.category,
                            armor ? this.armorCategoryLabel(armor.category) : '',
                            armor?.armor_type,
                            shield?.label,

                            // Habilidades e propriedades personalizadas.
                            ...features.flatMap(feature => [
                                feature?.title,
                                feature?.description,
                            ]),

                            ...customProperties.flatMap(property => [
                                property?.title,
                                property?.value,
                                property?.description,
                            ]),

                            // Fallback para estruturas futuras.
                            JSON.stringify(properties),
                        ];

                        return this.normalizeSearch(
                            terms
                                .filter(value =>
                                    value !== null
                                    && value !== undefined
                                    && String(value).trim() !== ''
                                )
                                .join(' ')
                        );
                    },

                    blankScroll() {
                        return {
                            spell_id: null, spell_name: '', spell_level: null,
                            spell_source: '', spell_info: {}, spell_dc: null,
                            spell_attack_bonus: null, auto_rarity: '', auto_cast_title: '',
                            auto_cast_description: '',
                        };
                    },

                    // Regras da mesa preservadas no formato das habilidades antigas:
                    // uma habilidade de USO e outra com o NOME do feitiço.
                    scrollCastDefaultUsage() {
                        return this.normalizeFeatureUsage({
                            enabled: true, counter_mode: 'spend',
                            current: 1, max: 1, recovery: 'single_use',
                            initial_mode: 'full', recovery_enabled: false,
                        });
                    },

                    scrollCastHtml(value) {
                        return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    },

                    // Valores iniciais conhecidos para pergaminhos. São configuráveis;
                    // não interferem na CD/ataque da ficha do personagem.
                    scrollSpellDefaultStats(level) {
                        const table = [
                            [13, 5], [13, 5], [13, 5], [15, 7], [15, 7],
                            [17, 9], [17, 9], [18, 10], [18, 10], [19, 11],
                        ];
                        if (level === null || level === undefined || level === '') {
                            return { dc: null, attack: null };
                        }
                        const index = Number(level);
                        return Number.isInteger(index) && index >= 0 && index < table.length
                            ? { dc: table[index][0], attack: table[index][1] }
                            : { dc: null, attack: null };
                    },

                    // Raridade do pergaminho pela tabela informada pelo mestre.
                    // CD e ataque continuam na tabela independente abaixo.
                    scrollSpellDefaultRarity(level) {
                        const rarity = [
                            'common', 'common', 'uncommon', 'uncommon', 'rare',
                            'rare', 'very_rare', 'very_rare', 'very_rare', 'legendary',
                        ];
                        if (level === null || level === undefined || level === '') return null;
                        const index = Number(level);
                        return Number.isInteger(index) && index >= 0 && index < rarity.length
                            ? rarity[index] : null;
                    },

                    scrollSpellRarityLabel(rarity) {
                        return ({
                            common:'Comum', uncommon:'Incomum', rare:'Raro',
                            very_rare:'Muito Raro', legendary:'Lendário',
                        })[rarity] || 'A definir';
                    },

                    // A sugestão acompanha a mudança do feitiço, mas não apaga
                    // uma raridade que o mestre escolheu manualmente.
                    applyScrollRecommendedRarity(previousScroll = null, force = false) {
                        const scroll = this.form?.properties?.spell_scroll;
                        if (!scroll) return;
                        const suggested = this.scrollSpellDefaultRarity(scroll.spell_level);
                        if (!suggested) return;
                        const oldSuggestion = previousScroll?.auto_rarity
                            || this.scrollSpellDefaultRarity(previousScroll?.spell_level)
                            || scroll.auto_rarity;
                        const isNew = !previousScroll?.spell_name;
                        if (force || (isNew && this.form.rarity === 'common')
                            || (oldSuggestion && this.form.rarity === oldSuggestion)
                            || (scroll.auto_rarity && this.form.rarity === scroll.auto_rarity)) {
                            this.form.rarity = suggested;
                        }
                        scroll.auto_rarity = suggested;
                    },

                    syncScrollLevelAndRarity() {
                        this.applyScrollRecommendedRarity();
                        this.syncScrollCastFeature();
                    },

                    scrollSpellStats(scroll) {
                        const defaults = this.scrollSpellDefaultStats(scroll?.spell_level);
                        const input = (value, min, max) => {
                            if (value === null || value === undefined || value === '') return null;
                            const number = Number(value);
                            return Number.isInteger(number) && number >= min && number <= max ? number : null;
                        };
                        return {
                            dc: input(scroll?.spell_dc, 1, 99) ?? defaults.dc,
                            attack: input(scroll?.spell_attack_bonus, -30, 99) ?? defaults.attack,
                        };
                    },

                    scrollCastDefaultTitle(scroll) {
                        const name = String(scroll?.spell_name ?? '').trim();
                        if (!name) return '';
                        const { dc, attack } = this.scrollSpellStats(scroll);
                        if (dc === null || attack === null) return name;
                        return name + ' (CD ' + dc + ' ' + (attack >= 0 ? '+' : '') + attack + ' Ataque)';
                    },

                    // Texto da habilidade de Uso. Não é a descrição do feitiço:
                    // o mestre pode editar essas regras individualmente.
                    scrollRulesDefaultDescription() {
                        return [
                            '<p>Qualquer criatura que possua proficiência na perícia Arcanismo ou a capacidade de conjurar pelo menos uma magia (desconsiderando magias concedidas por itens mágicos) pode ler e conjurar a magia de um pergaminho.</p>',
                            '<p>Com o pergaminho em mãos, você pode conjurar a magia utilizando o tempo de conjuração normal dela, sem a necessidade de componentes materiais. Para ativá-lo, realize um teste de habilidade usando o seu modificador de conjuração (caso não possua uma habilidade de conjuração de classe, utilize seu modificador de Inteligência). A Classe de Dificuldade é CD 10 + nível da magia.</p>',
                            '<p><strong>Sucesso:</strong> A magia é conjurada normalmente e o pergaminho se desfaz em pó.</p>',
                            '<p><strong>Falha:</strong> A magia falha e o pergaminho é destruído sem gerar nenhum efeito.</p>',
                            '<p><strong>Interrupção:</strong> Se a conjuração for interrompida antes de ser concluída, o pergaminho não é perdido.</p>',
                        ].join('');
                    },

                    // Descrição apenas em texto, com parágrafos preservados.
                    // É armazenada SOMENTE no pergaminho escolhido, nunca no
                    // catálogo inteiro, para permitir a leitura futura no Player.
                    scrollTooltipPlainText(html, maxLength = 16000) {
                        if (!html) return '';
                        const doc = new DOMParser().parseFromString(String(html), 'text/html');
                        doc.body.querySelectorAll('script,style,iframe,svg,object,embed, .spell-rule-tooltip, [role="tooltip"]').forEach(node => node.remove());
                        doc.body.querySelectorAll('br').forEach(node => node.replaceWith('\n'));
                        doc.body.querySelectorAll('p,li,blockquote,h1,h2,h3,h4,h5,h6').forEach(node => node.append('\n\n'));
                        return (doc.body.textContent || '').replace(/\r/g, '')
                            .replace(/[ \t]+\n/g, '\n').replace(/\n{3,}/g, '\n\n')
                            .trim().slice(0, maxLength);
                    },

                    scrollSelectedSpellInfo(spell) {
                        return {
                            school: String(spell?.school || ''),
                            casting_time: String(spell?.casting_time_display || ''),
                            range: String(spell?.range_display || ''),
                            components: String(spell?.components_display || ''),
                            duration: String(spell?.duration_display || ''),
                            ritual: !!spell?.ritual,
                            concentration: !!spell?.concentration,
                            description_text: this.scrollTooltipPlainText(spell?.description_html, 16000),
                            higher_level_text: this.scrollTooltipPlainText(spell?.higher_level_html, 7000),
                            higher_level_label: String(spell?.higher_level_label || ''),
                        };
                    },

                    // Segunda habilidade: resumo curto e legível; a descrição
                    // extensa fica no snapshot spell_info para o Inventário.
                    scrollCastDefaultDescription(scroll) {
                        const name = String(scroll?.spell_name ?? '').trim();
                        if (!name) return '';
                        const esc = value => this.scrollCastHtml(value);
                        const level = scroll?.spell_level === 0 || String(scroll?.spell_level) === '0'
                            ? 'Truque' : (Number(scroll?.spell_level) > 0
                                ? Number(scroll.spell_level) + 'º nível' : 'Nível não definido');
                        const info = scroll?.spell_info && typeof scroll.spell_info === 'object'
                            ? scroll.spell_info : {};
                        const school = info.school ? ' (' + esc(this.scrollSchoolLabel(info.school)) + ')' : '';
                        const rows = [
                            ['Conjuração', info.casting_time],
                            ['Alcance', info.range],
                            ['Componentes', info.components],
                            ['Duração', info.duration],
                        ].filter(([, value]) => String(value ?? '').trim() !== '');
                        let body = '<p><em>' + esc(level) + school + '</em></p>';
                        if (rows.length) {
                            const midpoint = Math.ceil(rows.length / 2);
                            for (const group of [rows.slice(0, midpoint), rows.slice(midpoint)]) {
                                if (group.length) body += '<p>' + group.map(([label, value]) =>
                                    '<strong>' + label + ':</strong> ' + esc(value)).join(' &nbsp; · &nbsp; ') + '</p>';
                            }
                        }
                        if (info.ritual || info.concentration) {
                            body += '<p><em>' + [info.ritual ? 'Ritual' : '', info.concentration ? 'Concentração' : '']
                                .filter(Boolean).join(' · ') + '</em></p>';
                        }
                        return body;
                    },

                    scrollCardDefaultDescription(scroll) {
                        const name = String(scroll?.spell_name ?? '').trim();
                        if (!name) return '';
                        const level = scroll.spell_level === 0 || String(scroll.spell_level) === '0'
                            ? 'Truque' : (Number(scroll.spell_level) > 0
                                ? Number(scroll.spell_level) + 'º círculo' : 'Nível não definido');
                        const school = scroll?.spell_info?.school
                            ? ' · ' + this.scrollSchoolLabel(scroll.spell_info.school) : '';
                        return 'Pergaminho de Feitiço · ' + level + school + '. Permite conjurar ' + name + '.';
                    },

                    scrollCastUsageCaption() {
                        const feature = (this.form?.properties?.features || []).find(row => row.system_key === 'spell_scroll_rules');
                        const usage = feature?.usage;
                        if (!usage?.enabled) return 'Regras de uso · sem rastreador';
                        if (usage.counter_mode === 'build') return 'Regras de uso · acúmulo de cargas';
                        const max = Math.max(1, parseInt(usage.max, 10) || 1);
                        return max + (max === 1 ? ' uso' : ' usos') +
                            (usage.recovery === 'single_use' ? ' · sem recuperação' : ' · recuperação configurada');
                    },

                    ensureScrollCastFeature(properties, previousScroll = null) {
                        if (!properties) return null;
                        if (!Array.isArray(properties.features)) properties.features = [];
                        const features = properties.features;
                        const scroll = properties.spell_scroll;
                        const systemKeys = ['spell_scroll_rules', 'spell_scroll_cast'];
                        if (!scroll || !String(scroll.spell_name ?? '').trim()) {
                            properties.features = features.filter(feature => !systemKeys.includes(feature?.system_key));
                            return null;
                        }

                        const name = String(scroll.spell_name).trim();
                        const rulesTitle = 'Uso e Conjuração de Pergaminhos';
                        const generatedTitle = this.scrollCastDefaultTitle(scroll);
                        const generatedDescription = this.scrollCastDefaultDescription(scroll);
                        const oldTitle = String(previousScroll?.auto_cast_title ?? scroll.auto_cast_title ?? '');
                        const oldDescription = String(previousScroll?.auto_cast_description ?? scroll.auto_cast_description ?? '');
                        const normTitle = row => String(row?.title ?? '').trim();

                        // Reconhece também habilidades manuais do formato antigo, sem system_key.
                        let rules = features.find(row => row?.system_key === 'spell_scroll_rules')
                            ?? features.find(row => !row?.system_key && normTitle(row).toLowerCase() === rulesTitle.toLowerCase());
                        const legacyCast = features.find(row => row?.system_key === 'spell_scroll_cast')
                            ?? features.find(row => row !== rules && !row?.system_key && normTitle(row) === 'Conjurar Feitiço')
                            ?? features.find(row => row !== rules && !row?.system_key &&
                                normTitle(row).startsWith(name + ' (CD ') && / Ataque\)$/i.test(normTitle(row)));
                        let cast = legacyCast;

                        if (!rules) {
                            rules = this.blankFeature();
                            rules.system_key = 'spell_scroll_rules';
                            rules.title = rulesTitle;
                            rules.description = this.scrollRulesDefaultDescription();
                            // Migra os usos do modelo V66.2.73 para a habilidade de regras.
                            rules.usage = cast?.usage?.enabled
                                ? this.normalizeFeatureUsage(cast.usage)
                                : this.scrollCastDefaultUsage();
                            rules.editor_open = false;
                            const castIndex = cast ? features.indexOf(cast) : -1;
                            features.splice(castIndex < 0 ? 0 : castIndex, 0, rules);
                        } else {
                            rules.system_key = 'spell_scroll_rules';
                            if (!normTitle(rules)) rules.title = rulesTitle;
                            if (!String(rules.description ?? '').trim()) rules.description = this.scrollRulesDefaultDescription();
                            rules.usage = this.normalizeFeatureUsage(rules.usage);
                        }

                        if (!cast) {
                            cast = this.blankFeature();
                            cast.system_key = 'spell_scroll_cast';
                            cast.title = generatedTitle;
                            cast.description = generatedDescription;
                            cast.editor_open = false;
                            features.splice(features.indexOf(rules) + 1, 0, cast);
                        } else {
                            cast.system_key = 'spell_scroll_cast';
                            // Nome e descrição personalizados nunca são sobrescritos.
                            if (!normTitle(cast) || normTitle(cast) === 'Conjurar Feitiço'
                                || (oldTitle && normTitle(cast) === oldTitle)) {
                                cast.title = generatedTitle;
                            }
                            if (!String(cast.description ?? '').trim()
                                || (oldDescription && cast.description === oldDescription)) {
                                cast.description = generatedDescription;
                            }
                        }
                        // O uso pertence SOMENTE à habilidade de regras.
                        cast.usage = this.normalizeFeatureUsage({ enabled: false });

                        // Remove duplicatas automáticas sem tocar nas habilidades manuais.
                        properties.features = features.filter(feature => {
                            if (feature?.system_key === 'spell_scroll_rules') return feature === rules;
                            if (feature?.system_key === 'spell_scroll_cast') return feature === cast;
                            return true;
                        });
                        scroll.auto_cast_title = generatedTitle;
                        scroll.auto_cast_description = generatedDescription;
                        return cast;
                    },

                    syncScrollCastFeature(previousScroll = null) {
                        if (!this.form?.properties) return;
                        this.ensureScrollCastFeature(this.form.properties, previousScroll);
                    },

                    openScrollCastFeature(which = 'rules') {
                        this.ensureScrollCastFeature(this.form?.properties);
                        const feature = (this.form?.properties?.features || []).find(row =>
                            row.system_key === (which === 'spell' ? 'spell_scroll_cast' : 'spell_scroll_rules'));
                        if (!feature) return;
                        (this.form.properties.features || []).forEach(entry => { entry.editor_open = entry === feature; });
                        this.$nextTick(() => this.$refs.vaultEditorAbilities?.scrollIntoView({
                            behavior: 'smooth', block: 'start',
                        }));
                    },

                    // O tooltip de termos do Grimório só pode ocupar a coluna
                    // do verbete; evita sobrepor a lista e a barra superior.
                    positionScrollRuleTooltip(event) {
                        const element = event?.target;
                        if (!(element instanceof Element)) return;
                        const term = element.closest('.spell-rule-term');
                        const tooltip = term?.querySelector('.spell-rule-tooltip');
                        const panel = term?.closest('.vault-v270-spell-paper');
                        if (!tooltip || !panel) return;
                        const bounds = panel.getBoundingClientRect();
                        tooltip.style.maxWidth = Math.max(160, Math.floor(bounds.width - 30)) + 'px';
                        tooltip.style.left = '50%';
                        tooltip.classList.remove('vault-v273-tip-below');
                        let rect = tooltip.getBoundingClientRect();
                        if (rect.top < bounds.top + 12) {
                            tooltip.classList.add('vault-v273-tip-below');
                            rect = tooltip.getBoundingClientRect();
                        }
                        const leftEdge = bounds.left + 13;
                        const rightEdge = bounds.right - 13;
                        const offset = rect.left < leftEdge ? leftEdge - rect.left
                            : (rect.right > rightEdge ? rightEdge - rect.right : 0);
                        tooltip.style.left = 'calc(50% + ' + Math.round(offset) + 'px)';
                    },

                    scrollSourceName(source) {
                        const names = {
                            XPHB: 'Livro básico · 2024', PHB: 'Livro básico · clássico',
                            XGE: 'Xanathar', TCE: 'Tasha', SCC: 'Strixhaven',
                            FTD: 'Dragões', EGW: 'Wildemount', GGR: 'Ravnica',
                            EFA: 'Eberron', ERLW: 'Eberron · clássico',
                            AAG: 'Guia Astral', AI: 'Acquisitions Inc.',
                            BMT: 'Livro de Muitas Coisas', IDROTF: 'Icewind Dale',
                            FRHOF: 'Forgotten Realms', AU: 'Unearthed Arcana',
                        };
                        const code = String(source ?? '').toUpperCase();
                        return names[code] ?? code;
                    },

                    // V66.2.68 — TODOS os feitiços oficiais locais são ativos.
                    // O número vem dos registros reais, não de uma constante 969.
                    get scrollBaseCount() {
                        return this.spellCatalog.filter(spell => spell.is_official).length;
                    },

                    // Fontes não oficiais são agrupadas por provedor + origem.
                    // Assim um homebrew com a mesma sigla de um livro publicado
                    // nunca ganha acesso automático por coincidência de sigla.
                    get scrollBookOptions() {
                        // O catálogo não muda durante a edição. Evita reagrupar
                        // milhares de feitiços a cada pintura/ação no modal.
                        if (this._scrollBooksCache) return this._scrollBooksCache;
                        const totals = new Map();
                        this.spellCatalog.forEach(spell => {
                            if (spell.is_official) return;
                            const code = spell.extra_key;
                            if (!code) return;
                            const entry = totals.get(code);
                            if (entry) {
                                entry.total++;
                            } else {
                                const registeredName = String(this.spellSourceNames?.[code] ?? '').trim();
                                const label = registeredName || (spell.source === 'PERSONALIZADO'
                                    ? 'Feitiços personalizados'
                                    : this.scrollSourceName(spell.source));
                                totals.set(code, {
                                    code,
                                    source: spell.source,
                                    provider: spell.provider,
                                    total: 1,
                                    label,
                                });
                            }
                        });
                        this._scrollBooksCache = [...totals.values()].sort((a,b) => a.label.localeCompare(b.label, 'pt-BR'));
                        return this._scrollBooksCache;
                    },

                    get filteredScrollBookOptions() {
                        const terms = this.normalizeSearch(this.scrollBookSearch).split(/\s+/).filter(Boolean);
                        return this.scrollBookOptions.filter(book => {
                            const searchable = this.normalizeSearch(book.label + ' ' + book.source);
                            return terms.every(term => searchable.includes(term));
                        });
                    },

                    get scrollActiveCampaign() {
                        return this.spellCampaigns.find(campaign => campaign.id === String(this.scrollCampaignId)) || null;
                    },

                    applyScrollCampaign(id) {
                        const campaign = this.spellCampaigns.find(entry => entry.id === String(id));
                        this.scrollCampaignId = campaign ? campaign.id : '';
                        const available = this.scrollBookOptions.map(book => book.code);
                        if (!campaign) {
                            this.scrollExtraBooks = [];
                        } else if (campaign.allow_all) {
                            this.scrollExtraBooks = available;
                        } else {
                            const enabled = new Set(campaign.sources);
                            this.scrollExtraBooks = available.filter(key => enabled.has(key));
                        }
                        this.scrollInspectBookKey = '';
                        this.scrollInspectLimit = 18;
                        this.scrollInspectSearch = '';
                        this.scrollListLimit = 60;
                        this.scrollPreviewId = null;
                        this.spellSearch = '';
                        this.scrollLevelFilter = 'all';
                        this.scrollSchoolFilter = 'all';
                    },

                    toggleScrollBook(key) {
                        const code = String(key || '').trim();
                        if (!code || !this.scrollBookOptions.some(book => book.code === code)) return;
                        this.scrollCampaignId = ''; // qualquer alteração vira seleção personalizada
                        if (this.scrollExtraBooks.includes(code)) {
                            this.scrollExtraBooks = this.scrollExtraBooks.filter(value => value !== code);
                        } else {
                            this.scrollExtraBooks = [...this.scrollExtraBooks, code];
                        }
                        this.scrollListLimit = 60;
                    },

                    clearScrollExtraBooks() {
                        this.scrollCampaignId = '';
                        this.scrollExtraBooks = [];
                        this.scrollListLimit = 60;
                    },

                    inspectScrollBook(key) {
                        const code = String(key || '').trim();
                        if (!this.scrollBookOptions.some(book => book.code === code)) return;
                        this.scrollInspectBookKey = code;
                        this.scrollInspectLimit = 30;
                        this.scrollInspectSearch = '';
                    },

                    get scrollInspectedBook() {
                        return this.scrollBookOptions.find(book => book.code === this.scrollInspectBookKey) || null;
                    },

                    get scrollInspectedBookSpells() {
                        if (!this.scrollInspectedBook) return [];
                        if (!this._scrollBookSpellsCache) {
                            this._scrollBookSpellsCache = new Map();
                            this.spellCatalog.forEach(spell => {
                                if (spell.is_official) return;
                                if (!this._scrollBookSpellsCache.has(spell.extra_key)) this._scrollBookSpellsCache.set(spell.extra_key, []);
                                this._scrollBookSpellsCache.get(spell.extra_key).push(spell);
                            });
                            this._scrollBookSpellsCache.forEach(spells => spells.sort((a, b) => a.level - b.level || a.name.localeCompare(b.name, 'pt-BR')));
                        }
                        const list = this._scrollBookSpellsCache.get(this.scrollInspectBookKey) || [];
                        const search = this.normalizeSearch(this.scrollInspectSearch);
                        return search ? list.filter(spell => this.normalizeSearch(spell.name).includes(search)) : list;
                    },

                    get displayedScrollInspectedSpells() {
                        return this.scrollInspectedBookSpells.slice(0, this.scrollInspectLimit);
                    },

                    previewSpellFromBook(spell) {
                        if (!spell || spell.is_official || !this.scrollBookOptions.some(book => book.code === spell.extra_key)) return;
                        // Abrir o feitiço no catálogo também habilita apenas o livro escolhido.
                        if (!this.scrollExtraBooks.includes(spell.extra_key)) {
                            this.scrollCampaignId = '';
                            this.scrollExtraBooks = [...this.scrollExtraBooks, spell.extra_key];
                        }
                        this.spellSearch = spell.name;
                        this.scrollLevelFilter = 'all';
                        this.scrollSchoolFilter = 'all';
                        this.scrollPreviewId = spell.id;
                        this.loadScrollSpellDetail(spell);
                        this.scrollBooksOpen = false;
                        this.scrollLibraryTab = 'spells';
                        this.scrollListLimit = 60;
                    },

                    scrollSpellAvailable(spell) {
                        return !!spell && (spell.is_official || this.scrollExtraBooks.includes(spell.extra_key));
                    },

                    scrollSchoolLabel(school) {
                        const names = {
                            abjuration:'Abjuração', conjuration:'Conjuração', divination:'Adivinhação',
                            enchantment:'Encantamento', evocation:'Evocação', illusion:'Ilusão',
                            necromancy:'Necromancia', transmutation:'Transmutação',
                            a:'Abjuração', c:'Conjuração', d:'Adivinhação', e:'Encantamento',
                            v:'Evocação', i:'Ilusão', n:'Necromancia', t:'Transmutação',
                        };
                        return names[String(school ?? '').toLowerCase()] || String(school || '—');
                    },

                    // Texto editorial do próprio Grimório; formatVaultText é o
                    // sanitizador HTML já empregado no Cofre.
                    // V66.2.71: verbete sob demanda; consulta autenticada e cache no item.
                    // Não executa novamente se o conteúdo já foi carregado.
                    applySelectedScrollSpellInfo(spell) {
                        const scroll = this.form?.properties?.spell_scroll;
                        if (!scroll || !spell?._detail_loaded
                            || String(scroll.spell_id) !== String(spell.id)) return;
                        const previous = { ...scroll };
                        scroll.spell_info = this.scrollSelectedSpellInfo(spell);
                        this.syncScrollCastFeature(previous);
                    },

                    // Busca assíncrona de um ÚNICO registro; não aumenta o payload
                    // da página que já teve estouro de memória na V66.2.70.
                    async loadScrollSpellDetail(spell) {
                        if (!spell || spell._detail_loading) return;
                        if (spell._detail_loaded) {
                            this.applySelectedScrollSpellInfo(spell);
                            return;
                        }
                        if (!this.urls?.spellDetail) {
                            spell._detail_error = true;
                            return;
                        }
                        spell._detail_loading = true;
                        spell._detail_error = false;
                        try {
                            const url = new URL(this.urls.spellDetail, window.location.origin);
                            url.searchParams.set('_vault_spell_detail', String(spell.id));
                            // NÃO pedir application/json: o index() existente devolve
                            // outro payload se a requisição espera resposta JSON.
                            const response = await fetch(url.toString(), {
                                credentials: 'same-origin',
                                headers: { Accept: 'text/html' },
                            });
                            if (!response.ok) throw new Error('Falha HTTP ' + response.status);
                            const data = await response.json();
                            if (!data?.ok || String(data.spell?.id) !== String(spell.id)) {
                                throw new Error('Detalhes indisponíveis');
                            }
                            const detail = data.spell;
                            // Reutiliza o payload editorial do Player.
                            // NÃO reinterpreta marcações de scaledamage no navegador.
                            spell.subtitle_pt = String(detail.subtitle_pt ?? '');
                            spell.source_label = String(detail.source_label ?? spell.source);
                            spell.casting_time_display = String(detail.casting_time_display ?? '');
                            spell.range_display = String(detail.range_display ?? '');
                            spell.duration_display = String(detail.duration_display ?? '');
                            spell.components_display = String(detail.components ?? '');
                            spell.ritual = !!detail.ritual;
                            spell.concentration = !!detail.concentration;
                            spell.description_html = String(detail.description_html ?? '');
                            spell.higher_level_html = String(detail.higher_level_html ?? '');
                            spell.higher_level_label = String(detail.higher_level_label ?? '');
                            spell.scaling_level_dice = Array.isArray(detail.scaling_level_dice)
                                ? detail.scaling_level_dice : [];
                            spell._detail_loaded = true;
                            this.applySelectedScrollSpellInfo(spell);
                        } catch (error) {
                            spell._detail_error = true;
                            console.warn('[Spellbound] Falha ao carregar detalhes do feitiço:', error);
                        } finally {
                            spell._detail_loading = false;
                        }
                    },

                    get scrollSchoolOptions() {
                        const available = new Set(this.spellCatalog
                            .filter(spell => this.scrollSpellAvailable(spell))
                            .map(spell => spell.school).filter(Boolean));
                        return [...available].sort((a,b) => this.scrollSchoolLabel(a).localeCompare(this.scrollSchoolLabel(b), 'pt-BR'));
                    },

                    get filteredSpellCatalog() {
                        const query = this.normalizeSearch(this.spellSearch);
                        const terms = query.split(/\s+/).filter(Boolean);
                        return this.spellCatalog.filter(spell =>
                            this.scrollSpellAvailable(spell)
                            && (this.scrollLevelFilter === 'all' || spell.level === Number(this.scrollLevelFilter))
                            && (this.scrollSchoolFilter === 'all' || spell.school === this.scrollSchoolFilter)
                            && terms.every(term => this.normalizeSearch(spell.name).includes(term))
                        );
                    },

                    get scrollVisibleCount() { return this.filteredSpellCatalog.length; },
                    get displayedScrollSpells() { return this.filteredSpellCatalog.slice(0,this.scrollListLimit); },

                    // Sugestões instantâneas na criação, limitadas e sem lista enorme.
                    get scrollQuickMatches() {
                        const search = this.normalizeSearch(this.scrollQuickSearch);
                        if (!search) return [];
                        const matches = this.spellCatalog.filter(spell =>
                            this.scrollSpellAvailable(spell) && this.normalizeSearch(spell.name).includes(search));
                        // Correspondências exatas e prefixos aparecem antes das parciais.
                        matches.sort((a,b) => {
                            const aName = this.normalizeSearch(a.name), bName = this.normalizeSearch(b.name);
                            const score = value => value === search ? 0 : (value.startsWith(search) ? 1 : 2);
                            return score(aName)-score(bName) || aName.localeCompare(bName, 'pt-BR');
                        });
                        return matches.slice(0,8);
                    },

                    get scrollPreviewSpell() {
                        const id = this.scrollPreviewId ?? this.form?.properties?.spell_scroll?.spell_id;
                        if (id === null || id === undefined || id === '') return null;
                        return this.spellCatalog.find(spell => String(spell.id) === String(id)) || null;
                    },

                    previewScrollSpell(spell) {
                        if (!spell) return;
                        this.scrollPreviewId = spell.id;
                        this.loadScrollSpellDetail(spell);
                    },

                    openScrollLibrary() {
                        this.scrollBookModalOpen = true;
                        this.scrollLibraryTab = 'spells';
                        this.scrollQuickFocused = false;
                        this.scrollBooksOpen = false;
                        this.scrollInspectBookKey = '';
                        this.scrollInspectSearch = '';
                        this.scrollListLimit = 60;
                        this.scrollPreviewId = this.form?.properties?.spell_scroll?.spell_id ?? null;
                        if (this.scrollPreviewSpell) this.loadScrollSpellDetail(this.scrollPreviewSpell);
                        this.$nextTick(() => this.$refs.scrollLibrarySearch?.focus());
                    },

                    closeScrollLibrary() {
                        this.scrollBookModalOpen = false;
                        this.scrollBooksOpen = false;
                        this.scrollLibraryTab = 'spells';
                    },

                    clearScrollLinkForManualInput() {
                        const data = this.form?.properties?.spell_scroll;
                        if (!data) return;
                        const previous = { ...data };
                        data.spell_id = null;
                        data.spell_source = '';
                        data.spell_info = {};
                        this.scrollPreviewId = null;
                        this.$nextTick(() => this.syncScrollCastFeature(previous));
                    },

                    selectScrollSpell(spell) {
                        if (!this.form || !spell || spell.source === 'PHB') return;
                        const previous = this.form.properties?.spell_scroll;
                        const priorDefaultName = 'Pergaminho de ' + String(previous?.spell_name ?? '');
                        this.form.properties.spell_scroll = {
                            spell_id: spell.id, spell_name: spell.name,
                            spell_level: spell.level, spell_source: spell.source ?? '',
                            spell_info: spell._detail_loaded
                                ? this.scrollSelectedSpellInfo(spell)
                                : {
                                    school: spell.school || '',
                                    casting_time: String(spell.casting_time ?? ''),
                                    range: String(spell.range ?? ''),
                                    duration: String(spell.duration ?? ''),
                                },
                            auto_rarity: String(previous?.auto_rarity ?? ''),
                            auto_cast_title: String(previous?.auto_cast_title ?? ''),
                            auto_cast_description: String(previous?.auto_cast_description ?? ''),
                            spell_dc: null,
                            spell_attack_bonus: null,
                        };
                        this.applyScrollRecommendedRarity(previous);
                        this.syncScrollCastFeature(previous);
                        // Na busca rápida, os metadados do verbete podem não estar em cache.
                        // Carrega somente o feitiço selecionado, como na V66.2.71.
                        if (spell._detail_loaded) this.syncScrollCastFeature(previous);
                        else this.loadScrollSpellDetail(spell);
                        if (!String(this.form.name ?? '').trim() || this.form.name === priorDefaultName) {
                            this.form.name = 'Pergaminho de ' + spell.name;
                        }
                        if (!String(this.form.description ?? '').trim() ||
                            this.form.description === this.scrollCardDefaultDescription(previous)) {
                            this.form.description = this.scrollCardDefaultDescription(this.form.properties.spell_scroll);
                        }
                        this.scrollPreviewId = spell.id;
                        this.scrollQuickSearch = '';
                        this.scrollQuickFocused = false;
                        this.scrollBookModalOpen = false;
                        this.scrollBooksOpen = false;
                    },

                    resetScrollBrowser() {
                        this.spellSearch = '';
                        this.scrollQuickSearch = '';
                        this.scrollQuickFocused = false;
                        this.scrollBookModalOpen = false;
                        this.scrollLibraryTab = 'spells';
                        this.scrollBookSearch = '';
                        this.scrollInspectSearch = '';
                        this.scrollBooksOpen = false;
                        this.scrollExtraBooks = [];
                        this.scrollLevelFilter = 'all';
                        this.scrollSchoolFilter = 'all';
                        this.scrollListLimit = 60;
                        this.scrollPreviewId = this.form?.properties?.spell_scroll?.spell_id ?? null;
                    },

                    // V66.2.48: máscara em kg enquanto o usuário digita em gramas.
                    // Digitar 1 -> 0,001; 100 -> 0,100; 1000 -> 1,000; 10000 -> 10,000.
                    // Banco/API permanecem em kg, como nas versões anteriores.
                    weightToGramsInput(kg) {
                        if (kg === null || kg === '' || typeof kg === 'undefined') return '';
                        const amount = Number(String(kg).replace(',', '.'));
                        if (!Number.isFinite(amount) || amount < 0) return '';
                        return this.formatWeightGramsMask(String(Math.round(amount * 1000)));
                    },

                    formatWeightGramsMask(rawDigits) {
                        const digits = String(rawDigits ?? '').replace(/\D/g, '').replace(/^0+(?=\d)/, '').slice(0, 12);
                        if (!digits) return '';
                        const grams = Number(digits);
                        const whole = Math.floor(grams / 1000).toLocaleString('pt-BR');
                        const fraction = String(grams % 1000).padStart(3, '0');
                        return `${whole},${fraction}`;
                    },

                    setWeightFromGrams(event) {
                        if (!this.form || !event?.target) return;
                        const input = event.target;
                        const digits = String(input.value ?? '').replace(/\D/g, '').replace(/^0+(?=\d)/, '').slice(0, 12);
                        this.weightGramsText = this.formatWeightGramsMask(digits);
                        const grams = digits ? Number(digits) : null;
                        this.form.weight = grams === null ? null : Number((grams / 1000).toFixed(3));
                        // Mantém o valor formatado dentro do campo, sem textos auxiliares.
                        input.value = this.weightGramsText;
                        if (typeof input.setSelectionRange === 'function') {
                            const end = input.value.length;
                            input.setSelectionRange(end, end);
                        }
                    },

                    formatWeightKg(kg) {
                        const value = Number(String(kg ?? '').replace(',', '.'));
                        if (!Number.isFinite(value)) return 'Peso opcional';
                        return value.toLocaleString('pt-BR', {maximumFractionDigits:6}) + ' kg';
                    },

                    blankProperties() {
                        return {
                            nature: 'mundane',
                            inventory: { equippable: false },
                            custom_properties: [],
                            features: [],
                            vestige: this.blankVestige(),
                            optional_rules: {},
                        };
                    },

                    blankWeapon() {
                        return {
                            category: 'simple',
                            category_custom: '',
                            weapon_type: '',
                            magic_bonus: 0,
                            enchantment_enabled: false,
                            allowed_abilities: ['strength'],
                            traits: [],
                            damage: {
                                count: 1,
                                die: 'd4',
                                ability: 'strength',
                                ability_custom: '',
                                type: 'Perfurante',
                                type_custom_mode: false,
                                type_options: ['Perfurante'],
                                custom_type: '',
                                include_ability: true,
                                ability_modifiers: ['primary'],
                                modifiers_open: false,
                                flat_bonus: 0,
                                bonus_condition: '',
                                extra_bonus_open: false,
                            },

                            /*
                            |--------------------------------------------------------------------------
                            | Distância e Alcance
                            |--------------------------------------------------------------------------
                            |
                            | Mantemos os nomes já compreendidos pelas integrações
                            | do Inventário/Ataques:
                            |
                            | range_normal_ft
                            | range_long_ft
                            | reach_ft
                            |
                            */
                            range_normal_ft: null,
                            range_long_ft: null,
                            reach_ft: 10,
                            loading_capacity: null,

                            extra_damage: [],
                            masteries: [],
                        };
                    },

                    blankArmor() {
                        return {
                            category: 'light',
                            category_custom: '',
                            armor_type: '',
                            base_ac: 11,
                            magic_bonus: 0,
                            enchantment_enabled: false,
                            dexterity_mode: 'full', // compatibilidade com o formato anterior
                            dexterity_cap: 2,
                            defense_ability: 'dexterity',
                            defense_ability_mode: 'full',
                            defense_ability_cap: 2,
                            ability_modifiers: [],
                            stealth_disadvantage: false,
                            strength_requirement_enabled: false,
                            strength_requirement: null,
                        };
                    },

                    blankShield() {
                        return {
                            label: 'Escudo',
                            ac_bonus: 2,
                            magic_bonus: 0,
                            enchantment_enabled: false,
                        };
                    },

                    blankFeature() {
                        return {
                            title: '',
                            description: '',
                            editor_open: true,
                            _editor_uid: 'vault-feature-' + (++this.featureEditorSequence),
                            usage: {
                                enabled: false,
                                counter_mode: 'spend',
                                current: 1,
                                max: 1,
                                recovery: 'day',
                                recovery_enabled: false, // opcional apenas em Acúmulo
                                recovery_custom: '',
                                initial_mode: 'full',
                                initial_formula: '2d6+1',
                                roll_trigger: 'manual',
                                roll_preview: null,
                            },
                        };
                    },

                    toBoolean(value) {
                        if (typeof value === 'boolean') {
                            return value;
                        }

                        if (typeof value === 'number') {
                            return value !== 0;
                        }

                        if (typeof value === 'string') {
                            const normalized =
                                value
                                    .trim()
                                    .toLowerCase();

                            if (
                                [
                                    '',
                                    '0',
                                    'false',
                                    'off',
                                    'no',
                                    'null',
                                    'undefined',
                                ].includes(normalized)
                            ) {
                                return false;
                            }

                            if (
                                [
                                    '1',
                                    'true',
                                    'on',
                                    'yes',
                                ].includes(normalized)
                            ) {
                                return true;
                            }
                        }

                        return !!value;
                    },

                    blankForm() {
                        return {
                            name: '',
                            type: 'mundane',
                            description: '',
                            image_url: null,
                            remove_image: false,
                            default_quantity: 1,
                            weight: null,
                            is_magical: false,
                            rarity: 'common',
                            requires_attunement: false,
                            is_cursed: false,
                            curse_name: '',
                            curse_description: '',
                            armor_class: null,
                            damage: '',
                            attack_bonus: null,
                            damage_bonus: null,
                            ability_bonuses: {},
                            properties: this.blankProperties(),
                            modifiers: {},
                            notes: '',
                            tags: [],
                        };
                    },

                    // V66.2.55 — máximo e origem de cargas são independentes.
                    // Os campos atuais permanecem numéricos para o Inventário existente.
                    normalizeFeatureUsage(raw) {
                        const source = raw && typeof raw === 'object' && !Array.isArray(raw) ? raw : {};
                        const enabled = this.toBoolean(source.enabled);
                        const max = Math.max(1, Math.min(999999, parseInt(source.max, 10) || 1));
                        const counterMode = ['spend','build'].includes(source.counter_mode) ? source.counter_mode : 'spend';
                        const parsed = parseInt(source.current, 10);
                        const defaultCurrent = counterMode === 'build' ? 0 : max;
                        let current = Math.max(0, Math.min(max, Number.isFinite(parsed) ? parsed : defaultCurrent));
                        // Preservar itens legados: 3/5 é "Definir", 5/5 é "Completa".
                        const initialMode = counterMode === 'build' ? 'fixed'
                            : (['full','fixed','roll'].includes(source.initial_mode)
                                ? source.initial_mode : (current === max ? 'full' : 'fixed'));
                        if (counterMode === 'build') current = 0;
                        else if (initialMode === 'full') current = max;
                        // Em Acúmulo, recuperar cargas é uma escolha explícita.
                        // Itens antigos não tinham esta opção: não habilitar por inferência
                        // a partir de um valor legado como 'day'.
                        let recovery = Object.prototype.hasOwnProperty.call(this.recoveryLabels, source.recovery)
                            ? source.recovery : 'day';
                        const recoveryEnabled = this.toBoolean(source.recovery_enabled);
                        if (counterMode === 'build' && recovery === 'single_use') recovery = 'day';
                        return {
                            enabled,
                            counter_mode: counterMode,
                            current,
                            max,
                            recovery,
                            recovery_enabled: recoveryEnabled,
                            recovery_custom: String(source.recovery_custom ?? ''),
                            initial_mode: initialMode,
                            initial_formula: String(source.initial_formula || '2d6+1').replace(/\s+/g,'').slice(0,20),
                            roll_trigger: initialMode === 'roll' ? 'manual'
                                : (source.roll_trigger === 'manual' ? 'manual' : 'auto'),
                            roll_preview: null, // valor de demonstração nunca é persistido
                        };
                    },

                    normalizeWeapon(raw) {
                        const source = raw && typeof raw === 'object' && !Array.isArray(raw)
                            ? raw
                            : {};

                        const weapon = {
                            ...this.blankWeapon(),
                            ...source,
                        };

                        weapon.damage = {
                            ...this.blankWeapon().damage,
                            ...(source.damage && typeof source.damage === 'object'
                                ? source.damage
                                : {}),
                        };

                        if (source.damage && typeof source.damage === 'object' && source.damage.include_ability === undefined) {
                            // Armas antigas sem configuração explícita passam a usar o modificador padrão.
                            weapon.damage.include_ability = true;
                        }
                        weapon.damage = this.normalizeWeaponDamagePart(weapon.damage);
                        weapon.enchantment_enabled = source.enchantment_enabled === undefined
                            ? (Number(weapon.magic_bonus) > 0)
                            : this.toBoolean(source.enchantment_enabled);
                        if (weapon.enchantment_enabled && !(Number(weapon.magic_bonus) > 0)) weapon.magic_bonus = 1;
                        weapon.allowed_abilities = Array.isArray(source.allowed_abilities)
                            ? [...new Set(source.allowed_abilities.filter(key =>
                                ['strength','dexterity','constitution','intelligence','wisdom','charisma','none'].includes(key)))]
                            : (weapon.traits?.some(t => t.key === 'finesse')
                                ? ['strength','dexterity']
                                : [weapon.damage.ability || 'dexterity']);
                        if (!weapon.allowed_abilities.length) weapon.allowed_abilities = ['strength'];
                        if (weapon.allowed_abilities.includes('none')) weapon.allowed_abilities = ['none'];
                        this.ensureFinesseWeaponAbilities(weapon);
                        if (weapon.allowed_abilities[0] === 'none') {
                            weapon.damage.ability_modifiers=weapon.damage.ability_modifiers.filter(key=>key!=='primary');
                            weapon.damage.include_ability=false;
                        }
                        if (!weapon.allowed_abilities.includes(weapon.damage.ability)) {
                            weapon.damage.ability = weapon.allowed_abilities[0];
                        }

                        /*
                        | Compatibilidade com a V10.5:
                        | se existir o formato temporário aninhado, migramos
                        | automaticamente para os campos flat usados pelo sistema.
                        */
                        const legacyRange =
                            source.range
                            && typeof source.range === 'object'
                            && !Array.isArray(source.range)
                                ? source.range
                                : null;

                        const legacyReach =
                            source.reach
                            && typeof source.reach === 'object'
                            && !Array.isArray(source.reach)
                                ? source.reach
                                : null;

                        weapon.range_normal_ft =
                            this.nullableNumber(
                                source.range_normal_ft
                                ?? (
                                    legacyRange?.unit === 'm'
                                        ? (
                                            legacyRange?.normal !== null
                                            && legacyRange?.normal !== undefined
                                                ? Math.round(
                                                    Number(legacyRange.normal) * 3.28084
                                                )
                                                : null
                                        )
                                        : legacyRange?.normal
                                ),
                                true
                            );

                        weapon.range_long_ft =
                            this.nullableNumber(
                                source.range_long_ft
                                ?? (
                                    legacyRange?.unit === 'm'
                                        ? (
                                            legacyRange?.max !== null
                                            && legacyRange?.max !== undefined
                                                ? Math.round(
                                                    Number(legacyRange.max) * 3.28084
                                                )
                                                : null
                                        )
                                        : legacyRange?.max
                                ),
                                true
                            );

                        weapon.reach_ft =
                            this.nullableNumber(
                                source.reach_ft
                                ?? (
                                    legacyReach?.unit === 'm'
                                        ? (
                                            legacyReach?.distance !== null
                                            && legacyReach?.distance !== undefined
                                                ? Math.round(
                                                    Number(legacyReach.distance) * 3.28084
                                                )
                                                : 10
                                        )
                                        : (
                                            legacyReach?.distance
                                            ?? 10
                                        )
                                ),
                                true
                            );

                        weapon.loading_capacity =
                            this.nullableNumber(
                                source.loading_capacity,
                                false
                            );

                        delete weapon.range;
                        delete weapon.reach;

                        if (!['simple', 'martial', 'custom'].includes(weapon.category)) {
                            weapon.category = 'custom';
                            weapon.category_custom = source.category ?? '';
                        }

                        weapon.traits = Array.isArray(source.traits)
                            ? source.traits.map(entry => {
                                const key = entry?.key ?? '';
                                const custom = this.toBoolean(entry?.custom);
                                const definition = this.weaponTraitDefinitions.find(
                                    current => current.key === key
                                );

                                return {
                                    key,
                                    name: custom
                                        ? (entry?.name ?? '')
                                        : (definition?.name ?? entry?.name ?? ''),
                                    description: custom
                                        ? (entry?.description ?? '')
                                        : (definition?.description ?? entry?.description ?? ''),
                                    custom,
                                };
                            })
                            : [];

                        weapon.masteries = Array.isArray(source.masteries)
                            ? source.masteries.map(entry => {
                                const key = entry?.key ?? '';
                                const custom = this.toBoolean(entry?.custom);
                                const definition = this.masteryDefinitions.find(
                                    current => current.key === key
                                );

                                return {
                                    key,
                                    name: custom
                                        ? (entry?.name ?? '')
                                        : (definition?.name ?? entry?.name ?? ''),
                                    description: custom
                                        ? (entry?.description ?? '')
                                        : (definition?.description ?? entry?.description ?? ''),
                                    custom,
                                };
                            })
                            : [];

                        weapon.extra_damage = Array.isArray(source.extra_damage)
                            ? source.extra_damage.map(part => this.normalizeWeaponDamagePart(part))
                            : [];
                        this.ensureFinesseWeaponAbilities(weapon);
                        weapon.damage.ability = weapon.allowed_abilities[0];

                        return weapon;
                    },

                    armorDexterityDefaults(category) {
                        if (category === 'light') {
                            return { mode: 'full', cap: 2 };
                        }

                        if (category === 'medium') {
                            return { mode: 'capped', cap: 2 };
                        }

                        return { mode: 'none', cap: 2 };
                    },

                    normalizeArmor(raw) {
                        const source = raw && typeof raw === 'object' && !Array.isArray(raw) ? raw : {};
                        const defaults = this.blankArmor();
                        const armor = {...defaults, ...source};
                        const category = ['light','medium','heavy','custom'].includes(armor.category)
                            ? armor.category : 'custom';
                        const categoryDefaults = this.armorDexterityDefaults(category);
                        const validAbilities = ['strength','dexterity','constitution','intelligence','wisdom','charisma','none'];
                        const legacyMode = ['none','full','capped'].includes(source.dexterity_mode)
                            ? source.dexterity_mode : categoryDefaults.mode;
                        const defenseAbility = validAbilities.includes(source.defense_ability)
                            ? source.defense_ability
                            : (legacyMode === 'none' ? 'none' : 'dexterity');
                        const chosenMode = ['full','capped'].includes(source.defense_ability_mode)
                            ? source.defense_ability_mode
                            : (legacyMode === 'capped' ? 'capped' : 'full');
                        const mode = defenseAbility === 'none' ? 'none' : chosenMode;
                        const sourceCap = source.defense_ability_cap ?? source.dexterity_cap ?? categoryDefaults.cap;
                        const parsedCap = Number.parseInt(sourceCap, 10);
                        const cap = Number.isFinite(parsedCap) ? Math.max(0, Math.min(99, parsedCap)) : 2;
                        const abilities = Array.isArray(source.ability_modifiers)
                            ? [...new Set(source.ability_modifiers.filter(ability => validAbilities.includes(ability) && ability !== 'none' && ability !== defenseAbility))]
                            : [];
                        const strengthValue = Number.parseInt(source.strength_requirement, 10);
                        const strengthEnabled = source.strength_requirement_enabled === undefined
                            ? (Number.isFinite(strengthValue) && strengthValue > 0)
                            : this.toBoolean(source.strength_requirement_enabled);
                        const strengthRequirement = strengthEnabled
                            ? Math.max(1, Math.min(30, Number.isFinite(strengthValue) ? strengthValue : 13))
                            : null;
                        const enchanted = source.enchantment_enabled === undefined
                            ? Number(source.magic_bonus) > 0
                            : this.toBoolean(source.enchantment_enabled);
                        const {preset_key: _unusedPresetKey, ...rest} = armor;
                        return {
                            ...rest,
                            category,
                            category_custom: category === 'custom' ? (armor.category_custom ?? '') : '',
                            armor_type: String(armor.armor_type ?? ''),
                            base_ac: Math.max(0, Math.min(99, Number.parseInt(armor.base_ac, 10) || 0)),
                            enchantment_enabled: enchanted,
                            magic_bonus: enchanted ? Math.max(1, Math.min(6, Number.parseInt(source.magic_bonus, 10) || 1)) : 0,
                            defense_ability: defenseAbility,
                            defense_ability_mode: mode,
                            defense_ability_cap: cap,
                            dexterity_mode: defenseAbility === 'dexterity' ? mode : 'none',
                            dexterity_cap: cap,
                            ability_modifiers: abilities,
                            stealth_disadvantage: this.toBoolean(armor.stealth_disadvantage),
                            strength_requirement_enabled: strengthEnabled,
                            strength_requirement: strengthRequirement,
                        };
                    },

                    normalizeShield(raw) {
                        const source = raw && typeof raw === 'object' && !Array.isArray(raw) ? raw : {};
                        const shield = {...this.blankShield(), ...source};
                        const enabled = source.enchantment_enabled === undefined
                            ? Number(source.magic_bonus) > 0
                            : this.toBoolean(source.enchantment_enabled);
                        return {
                            ...shield,
                            label: String(source.label ?? 'Escudo'),
                            ac_bonus: Math.max(0, Math.min(99, parseInt(source.ac_bonus, 10) || 0)),
                            enchantment_enabled: enabled,
                            magic_bonus: enabled
                                ? Math.max(1, Math.min(6, parseInt(source.magic_bonus, 10) || 1))
                                : 0,
                        };
                    },

                    toPlainData(value) {
                        if (
                            value === null
                            || typeof value !== 'object'
                        ) {
                            return value;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Alpine usa Proxy para dados reativos
                        |--------------------------------------------------------------------------
                        |
                        | A API nativa de clonagem profunda do navegador não aceita
                        | Proxy. Alpine.raw() remove a camada reativa do nível atual
                        | e a recursão converte toda a árvore para dados simples.
                        |
                        */
                        const rawValue =
                            window.Alpine
                            && typeof window.Alpine.raw === 'function'
                                ? window.Alpine.raw(value)
                                : value;

                        if (Array.isArray(rawValue)) {
                            return rawValue.map(
                                entry => this.toPlainData(entry)
                            );
                        }

                        const plain = {};

                        Object.keys(rawValue).forEach(
                            key => {
                                const child = rawValue[key];

                                if (
                                    typeof child === 'function'
                                    || typeof child === 'undefined'
                                ) {
                                    return;
                                }

                                plain[key] =
                                    this.toPlainData(child);
                            }
                        );

                        return plain;
                    },

                    normalizeProperties(raw, rawItem = null) {
                        const properties = raw && typeof raw === 'object' && !Array.isArray(raw)
                            ? this.toPlainData(raw)
                            : {};

                        let nature = properties.nature;

                        if (!['wonderful', 'technological', 'mundane'].includes(nature)) {
                            nature = ['wonderful', 'technological', 'mundane'].includes(rawItem?.type)
                                ? rawItem.type
                                : (rawItem?.is_magical ? 'wonderful' : 'mundane');
                        }

                        properties.nature = nature;
                        properties.vestige = this.normalizeVestige(properties.vestige);
                        properties.inventory = {
                            equippable: !!properties?.inventory?.equippable,
                        };
                        if (properties.spell_scroll && typeof properties.spell_scroll === 'object') {
                            properties.spell_scroll = {
                                spell_id: properties.spell_scroll.spell_id ?? null,
                                spell_name: String(properties.spell_scroll.spell_name ?? ''),
                                spell_level: properties.spell_scroll.spell_level ?? null,
                                spell_source: String(properties.spell_scroll.spell_source ?? '').toUpperCase(),
                                spell_info: (() => {
                                    const meta = properties.spell_scroll.spell_info;
                                    if (!meta || typeof meta !== 'object' || Array.isArray(meta)) return {};
                                    return {
                                        school: String(meta.school ?? ''),
                                        casting_time: String(meta.casting_time ?? ''),
                                        range: String(meta.range ?? ''),
                                        components: String(meta.components ?? ''),
                                        duration: String(meta.duration ?? ''),
                                        ritual: this.toBoolean(meta.ritual),
                                        concentration: this.toBoolean(meta.concentration),
                                        description_text: String(meta.description_text ?? '').slice(0, 16000),
                                        higher_level_text: String(meta.higher_level_text ?? '').slice(0, 7000),
                                        higher_level_label: String(meta.higher_level_label ?? '').slice(0, 160),
                                    };
                                })(),
                                auto_rarity: ['common','uncommon','rare','very_rare','legendary']
                                    .includes(properties.spell_scroll.auto_rarity)
                                    ? properties.spell_scroll.auto_rarity : '',
                                auto_cast_title: String(properties.spell_scroll.auto_cast_title ?? ''),
                                auto_cast_description: String(properties.spell_scroll.auto_cast_description ?? ''),
                                spell_dc: properties.spell_scroll.spell_dc ?? null,
                                spell_attack_bonus: properties.spell_scroll.spell_attack_bonus ?? null,
                            };
                        } else {
                            delete properties.spell_scroll;
                        }

                        if (properties.weapon && typeof properties.weapon === 'object') {
                            properties.weapon = this.normalizeWeapon(properties.weapon);
                        } else {
                            delete properties.weapon;
                        }

                        if (properties.armor && typeof properties.armor === 'object') {
                            properties.armor = this.normalizeArmor(properties.armor);
                        } else {
                            delete properties.armor;
                        }

                        if (properties.shield && typeof properties.shield === 'object') {
                            properties.shield = this.normalizeShield(properties.shield);
                        } else {
                            delete properties.shield;
                        }

                        if (properties.armor || properties.shield) {
                            properties.inventory.equippable = true;
                        }

                        properties.custom_properties = Array.isArray(properties.custom_properties)
                            ? properties.custom_properties.map(entry => ({
                                title: entry?.title ?? '',
                                value: entry?.value ?? '',
                                description: entry?.description ?? '',
                            }))
                            : [];

                        properties.features = Array.isArray(properties.features)
                            ? properties.features.map(feature => ({
                                title: feature?.title ?? '',
                                description: feature?.description ?? '',
                                editor_open: !!feature?.editor_open,
                                _editor_uid: feature?._editor_uid || 'vault-feature-' + (++this.featureEditorSequence),
                                system_key: ['weapon_enchantment','armor_enchantment','shield_enchantment','spell_scroll_rules','spell_scroll_cast'].includes(feature?.system_key)
                                    ? feature.system_key : null,
                                usage: this.normalizeFeatureUsage(feature?.usage),
                            }))
                            : [];
                        // Feitiços já cadastrados sem habilidade (versões anteriores)
                        // recebem a habilidade no próprio card e ao editar o item.
                        this.ensureScrollCastFeature(properties);
                        properties.optional_rules =
                            properties.optional_rules
                            && typeof properties.optional_rules === 'object'
                                ? properties.optional_rules
                                : {};

                        return properties;
                    },

                    normalizeItem(raw) {
                        const properties = this.normalizeProperties(
                            raw?.properties,
                            raw
                        );

                        return {
                            id: parseInt(raw?.id),
                            name: raw?.name ?? '',
                            type: properties.nature,
                            description: String(raw?.description ?? '').trim() ||
                                (properties.spell_scroll?.spell_name
                                    ? this.scrollCardDefaultDescription(properties.spell_scroll) : ''),
                            image_url: raw?.image_url ?? null,
                            default_quantity: Math.max(
                                1,
                                parseInt(raw?.default_quantity) || 1
                            ),
                            weight: raw?.weight ?? null,
                            is_magical: properties.nature === 'wonderful',
                            rarity: raw?.rarity ?? 'common',
                            requires_attunement: this.toBoolean(raw?.requires_attunement),
                            is_cursed: this.toBoolean(raw?.is_cursed),
                            curse_name: raw?.curse_name ?? '',
                            curse_description: raw?.curse_description ?? '',
                            armor_class: raw?.armor_class ?? null,
                            damage: raw?.damage ?? '',
                            attack_bonus: raw?.attack_bonus ?? null,
                            damage_bonus: raw?.damage_bonus ?? null,
                            ability_bonuses: raw?.ability_bonuses ?? {},
                            properties,
                            modifiers: raw?.modifiers ?? {},
                            notes: raw?.notes ?? '',
                            tags: Array.isArray(raw?.tags) ? raw.tags : [],
                            folder_id: raw?.folder_id ? parseInt(raw.folder_id) : null,
                        };
                    },

                    openCreate() {
                        this.closeCropEditor();
                        this.editingId = null;
                        this.form = this.blankForm();
                        this.weightGramsText = '';
                        this.resetScrollBrowser();
                        this.formFolderId = this.activeFolderId;
                        this.editorTab = 'general';
                        this.vestigeEditorStage = 'dormant';
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.saveError = null;
                        this.editorOpen = true;
                    },

                    openEdit(item) {
                        this.closeCropEditor();
                        const normalized = this.normalizeItem(item);

                        this.editingId = normalized.id;
                        this.form = this.toPlainData(normalized);
                        // Preserva a habilidade do encantamento na lista real, editável.
                        this.ensureWeaponEnchantmentFeature();
                        this.ensureDefenseEnchantmentFeature('armor');
                        this.ensureDefenseEnchantmentFeature('shield');
                        this.weightGramsText = this.weightToGramsInput(this.form.weight);
                        this.resetScrollBrowser();
                        // Atualiza pergaminhos legados que tinham apenas o nível.
                        // Carrega UM verbete quando abre a edição, sem mexer no banco.
                        const scrollId = this.form?.properties?.spell_scroll?.spell_id;
                        const spellFromCatalog = scrollId === null || scrollId === undefined
                            ? null : this.spellCatalog.find(spell => String(spell.id) === String(scrollId));
                        if (spellFromCatalog && (!this.form.properties.spell_scroll.spell_info?.casting_time
                            || !this.form.properties.spell_scroll.spell_info?.description_text)) {
                            this.loadScrollSpellDetail(spellFromCatalog);
                        }
                        this.formFolderId = normalized.folder_id;
                        this.editorTab = 'general';
                        // Editar as habilidades não modifica a etapa do personagem.
                        this.vestigeEditorStage = 'dormant';
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.saveError = null;
                        this.editorOpen = true;
                    },

                    closeEditor() {
                        if (this.saving) return;
                        this.closeCropEditor();

                        this.scrollBookModalOpen = false;
                        this.editorOpen = false;
                        this.editingId = null;
                        this.form = null;
                        this.formFolderId = null;
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.saveError = null;
                    },

                    setNature(nature) {
                        if (!['wonderful', 'technological', 'mundane'].includes(nature)) {
                            return;
                        }

                        this.form.type = nature;
                        this.form.properties.nature = nature;
                        this.form.is_magical = nature === 'wonderful';

                        if (nature !== 'wonderful') {
                            this.form.requires_attunement = false;
                            // Apenas um Maravilhoso/Artefato pode ser Vestígio.
                            // O conteúdo cadastrado continua guardado, mas o modo fica inativo.
                            if (this.form.properties?.vestige) {
                                this.form.properties.vestige.enabled = false;
                            }
                        }
                    },

                    setEquippable(value) {
                        if (!this.form) return;
                        this.form.properties.inventory.equippable = !!value;
                    },

                    hasModule(module) {
                        return !!this.form?.properties?.[module];
                    },

                    itemCategory() {
                        if (this.form?.properties?.spell_scroll) return 'spell_scroll';
                        if (this.form?.properties?.weapon) return 'weapon';
                        if (this.form?.properties?.armor) return 'armor';
                        if (this.form?.properties?.shield) return 'shield';
                        return 'other';
                    },

                    setItemCategory(category) {
                        if (
                            !this.form
                            || !['weapon', 'armor', 'shield', 'other', 'spell_scroll'].includes(category)
                        ) {
                            return;
                        }

                        const properties = this.form.properties;
                        if (Array.isArray(properties.features)) {
                            // Apenas a habilidade mecânica da categoria atual pode permanecer.
                            const allowed = category === 'weapon' ? 'weapon_enchantment'
                                : category === 'armor' ? 'armor_enchantment'
                                : category === 'shield' ? 'shield_enchantment'
                                : null;
                            const scrollKeysAllowed = category === 'spell_scroll'
                                ? ['spell_scroll_rules', 'spell_scroll_cast'] : [];
                            properties.features = properties.features.filter(feature =>
                                !['weapon_enchantment','armor_enchantment','shield_enchantment','spell_scroll_rules','spell_scroll_cast'].includes(feature.system_key)
                                || feature.system_key === allowed || scrollKeysAllowed.includes(feature.system_key));
                        }
                        if (category === 'spell_scroll') {
                            // Um pergaminho recém-criado é mágico por padrão.
                            // Itens antigos e natureza personalizada permanecem intactos.
                            if (!this.editingId && this.form.type === 'mundane') {
                                this.setNature('wonderful');
                            }
                            properties.spell_scroll = properties.spell_scroll ?? this.blankScroll();
                            delete properties.weapon;
                            delete properties.armor;
                            delete properties.shield;
                            properties.inventory.equippable = false;
                            this.resetScrollBrowser();
                            this.syncScrollCastFeature();
                            return;
                        }
                        delete properties.spell_scroll;

                        if (category === 'weapon') {
                            properties.weapon =
                                properties.weapon ?? this.blankWeapon();

                            delete properties.armor;
                            delete properties.shield;

                            properties.inventory.equippable = true;
                            return;
                        }

                        if (category === 'armor') {
                            properties.armor =
                                properties.armor ?? this.blankArmor();

                            delete properties.weapon;
                            delete properties.shield;

                            properties.inventory.equippable = true;
                            return;
                        }

                        if (category === 'shield') {
                            properties.shield =
                                properties.shield ?? this.blankShield();

                            delete properties.weapon;
                            delete properties.armor;

                            properties.inventory.equippable = true;
                            return;
                        }

                        delete properties.weapon;
                        delete properties.armor;
                        delete properties.shield;
                    },

                    addModule(module) {
                        if (!this.form) return;

                        if (module === 'weapon' && !this.form.properties.weapon) {
                            this.form.properties.weapon = this.blankWeapon();
                        }

                        if (module === 'armor' && !this.form.properties.armor) {
                            this.form.properties.armor = this.blankArmor();
                            this.form.properties.inventory.equippable = true;
                        }

                        if (module === 'shield' && !this.form.properties.shield) {
                            this.form.properties.shield = this.blankShield();
                            this.form.properties.inventory.equippable = true;
                        }
                    },

                    removeModule(module) {
                        if (!this.form || !['weapon', 'armor', 'shield'].includes(module)) {
                            return;
                        }

                        delete this.form.properties[module];
                    },

                    traitSelected(key) {
                        return this.form?.properties?.weapon?.traits?.some(
                            trait => !trait.custom && trait.key === key
                        ) ?? false;
                    },

                    toggleTrait(definition) {
                        const traits = this.form.properties.weapon.traits;
                        const index = traits.findIndex(
                            trait => !trait.custom && trait.key === definition.key
                        );

                        if (index >= 0) {
                            traits.splice(index, 1);
                            return;
                        }

                        traits.push({
                            key: definition.key,
                            name: definition.name,
                            description: definition.description,
                            custom: false,
                        });
                        if (definition.key === 'finesse') {
                            const weapon=this.form.properties.weapon;
                            const wasNone=weapon.allowed_abilities?.includes('none');
                            this.ensureFinesseWeaponAbilities(weapon);
                            weapon.damage.ability=weapon.allowed_abilities[0];
                            if (wasNone) weapon.damage.include_ability=true;
                        }

                        /*
                        | Se a propriedade exige medidas adicionais,
                        | inicializa a configuração sem forçar um valor
                        | específico para Distância.
                        */
                        if (
                            definition.key === 'range'
                            && !this.form.properties.weapon.range
                        ) {
                            this.form.properties.weapon.range =
                                this.blankWeapon().range;
                        }

                        if (
                            definition.key === 'reach'
                            && !this.form.properties.weapon.reach
                        ) {
                            this.form.properties.weapon.reach =
                                this.blankWeapon().reach;
                        }

                        if (
                            definition.key === 'loading'
                            && !this.form.properties.weapon.loading_capacity
                        ) {
                            this.form.properties.weapon.loading_capacity = null;
                        }
                    },

                    addCustomTrait() {
                        this.form.properties.weapon.traits.push({
                            key: `custom-trait-${Date.now()}`,
                            name: '',
                            description: '',
                            custom: true,
                        });
                    },

                    removeTrait(index) {
                        this.form.properties.weapon.traits.splice(index, 1);
                    },

                    masterySelected(key) {
                        return this.form?.properties?.weapon?.masteries?.some(
                            mastery => !mastery.custom && mastery.key === key
                        ) ?? false;
                    },

                    toggleMastery(definition) {
                        const masteries = this.form.properties.weapon.masteries;
                        const index = masteries.findIndex(
                            mastery => !mastery.custom && mastery.key === definition.key
                        );

                        if (index >= 0) {
                            masteries.splice(index, 1);
                            return;
                        }

                        masteries.push({
                            key: definition.key,
                            name: definition.name,
                            description: definition.description,
                            custom: false,
                        });
                    },

                    addCustomMastery() {
                        this.form.properties.weapon.masteries.push({
                            key: `custom-mastery-${Date.now()}`,
                            name: '',
                            description: '',
                            custom: true,
                        });
                    },

                    removeMastery(index) {
                        this.form.properties.weapon.masteries.splice(index, 1);
                    },

                    weaponHasTrait(weapon, key) {
                        return Array.isArray(weapon?.traits)
                            && weapon.traits.some(
                                trait =>
                                    String(trait?.key ?? '')
                                    === String(key ?? '')
                            );
                    },

                    weaponRuleCompactValue(trait, weapon) {
                        const key =
                            String(trait?.key ?? '');

                        if (key === 'range') {
                            return this.weaponRangeText(weapon);
                        }

                        if (key === 'reach') {
                            return this.weaponReachText(weapon);
                        }

                        if (key === 'loading') {
                            const capacity = this.nullableNumber(weapon?.loading_capacity, false);
                            return capacity && capacity > 0
                                ? `${capacity} ${capacity === 1 ? 'munição' : 'munições'}`
                                : '';
                        }

                        return '';
                    },

                    weaponRangeText(weapon) {
                        if (!weapon) {
                            return '';
                        }

                        const normal =
                            this.nullableNumber(
                                weapon.range_normal_ft,
                                true
                            );

                        const max =
                            this.nullableNumber(
                                weapon.range_long_ft,
                                true
                            );

                        if (
                            normal === null
                            && max === null
                        ) {
                            return '';
                        }

                        if (
                            normal !== null
                            && max !== null
                        ) {
                            return `${normal}/${max} Ft`;
                        }

                        if (normal !== null) {
                            return `${normal} Ft`;
                        }

                        return `máx. ${max} Ft`;
                    },

                    weaponReachText(weapon) {
                        if (!weapon) {
                            return '';
                        }

                        const reach =
                            this.nullableNumber(
                                weapon.reach_ft,
                                true
                            );

                        return reach === null
                            ? ''
                            : `${reach} Ft`;
                    },

                    damageTypeIsStandard(value) {
                        return this.damageTypeDefinitions.includes(
                            String(value ?? '').trim()
                        );
                    },

                    // V66.2.51: dano antigo continua legível; campos novos são opcionais.
                    normalizeWeaponDamagePart(raw) {
                        const part = raw && typeof raw === 'object' ? {...raw} : {};
                        if (typeof part.expression_input === 'string') {
                            const match=part.expression_input.replace(/\s+/g,'').match(/^(\d{1,2})d(\d{1,3})(?:\+(atributo|\d{1,2}))?$/i);
                            if (match) {
                                part.count=Number(match[1]);
                                part.die='d'+match[2];
                                part.include_ability=String(match[3]||'').toLowerCase()==='atributo';
                                part.flat_bonus=part.include_ability ? 0 : Number(match[3]||0);
                            }
                            delete part.expression_input;
                        }
                        part.count = Math.max(1, Math.min(99, parseInt(part.count) || 1));
                        part.die = String(part.die || 'd6').trim().toLowerCase();
                        const rawType = String(part.type || '').trim();
                        const types = Array.isArray(part.type_options)
                            ? part.type_options
                            : rawType.split(/\s+ou\s+/i);
                        part.type_options = [...new Set(types.map(t=>String(t).trim())
                            .filter(t=>this.damageTypeIsStandard(t)))];
                        part.custom_type = String(part.custom_type || '').trim() ||
                            (rawType.split(/\s+ou\s+/i).find(t=>t.trim() && !this.damageTypeIsStandard(t.trim())) || '').trim();
                        part.type_custom_mode = false;
                        part.flat_bonus = Number.isFinite(Number(part.flat_bonus)) ?
                            Math.max(0,Math.min(99,Number(part.flat_bonus))) : 0;
                        part.include_ability = part.include_ability === true;
                        const knownAbilities=['primary','strength','dexterity','constitution','intelligence','wisdom','charisma'];
                        part.ability_modifiers = Array.isArray(part.ability_modifiers)
                            ? [...new Set(part.ability_modifiers.filter(key=>knownAbilities.includes(key)))]
                            : (part.include_ability ? ['primary'] : []);
                        part.include_ability = part.ability_modifiers.includes('primary'); // compatibilidade com leitor antigo
                        part.modifiers_open=false;
                        part.bonus_condition = String(part.bonus_condition || '').slice(0,180);
                        part.extra_bonus_open = false;
                        if (part.flat_bonus > 0 || part.bonus_condition) part.extra_bonus_open = true;
                        return part;
                    },

                    damageTypeSelected(part, type) {
                        return Array.isArray(part?.type_options) && part.type_options.includes(type);
                    },
                    syncDamageTypes(part) {
                        const options = Array.isArray(part.type_options) ? part.type_options : [];
                        const custom = String(part.custom_type || '').trim();
                        part.type = [...options, ...(custom ? [custom] : [])].join(' ou ');
                    },
                    toggleDamageType(part, type) {
                        if (!part) return;
                        if (!Array.isArray(part.type_options)) part.type_options = [];
                        const i=part.type_options.indexOf(type);
                        if (i >= 0) part.type_options.splice(i,1);
                        else part.type_options.push(type);
                        this.syncDamageTypes(part);
                    },
                    // V66.2.54 — quantidade e faces editáveis, com setas e leitura de 2d20.
                    damageExpressionValid(part) {
                        if (!part) return false;
                        const count = Number(part.count);
                        const die = String(part.die || '').trim().toLowerCase();
                        const bonus = Number(part.flat_bonus ?? 0);
                        return Number.isInteger(count) && count >= 1 && count <= 99
                            && /^d(?:[2-9]|[1-9]\d|100)$/.test(die)
                            && Number.isInteger(bonus) && bonus >= 0 && bonus <= 99;
                    },
                    weaponDieSides(part) {
                        const n = parseInt(String(part?.die || 'd6').replace(/^d/i,''),10);
                        return Number.isInteger(n) ? n : 6;
                    },
                    adjustWeaponDiceCount(part, direction) {
                        if (!part) return;
                        part.count = Math.max(1,Math.min(99,(parseInt(part.count,10)||1) + direction));
                        delete part.expression_input;
                    },
                    adjustWeaponDie(part, direction) {
                        if (!part) return;
                        const faces=[2,3,4,6,8,10,12,20,100];
                        const current=this.weaponDieSides(part);
                        const next = direction > 0
                            ? faces.find(n=>n>current) ?? 100
                            : [...faces].reverse().find(n=>n<current) ?? 2;
                        part.die = `d${next}`;
                        delete part.expression_input;
                    },
                    previewWeaponDiceNumber(part, field, value) {
                        if (!part || !/^\d+$/.test(String(value).trim())) return;
                        const numeric=Number(value);
                        const limit=field === 'count' ? 99 : 100;
                        const minimum=field === 'count' ? 1 : 2;
                        if (!Number.isInteger(numeric) || numeric<minimum || numeric>limit) return;
                        if (field === 'count') part.count=numeric;
                        else part.die=`d${numeric}`;
                    },
                    commitWeaponDiceNumber(part, field, value) {
                        if (!part) return;
                        const raw=String(value??'').toLowerCase().replace(/\s+/g,'');
                        // Também é possível colar a expressão completa em qualquer campo.
                        const full=raw.match(/^(\d{1,2})d(\d{1,3})$/);
                        if (full) {
                            const count=Number(full[1]), faces=Number(full[2]);
                            if (count>=1 && count<=99 && faces>=2 && faces<=100) {
                                part.count=count;part.die=`d${faces}`;
                            }
                            return;
                        }
                        const justNumber=field === 'die' ? raw.replace(/^d/,'') : raw;
                        if (!/^\d{1,3}$/.test(justNumber)) return;
                        this.previewWeaponDiceNumber(part,field,justNumber);
                    },
                    clampWeaponExtraBonus(part) {
                        if (!part) return;
                        part.flat_bonus=Math.max(0,Math.min(99,Math.round(Number(part.flat_bonus)||0)));
                    },
                    weaponAbilityName(key) {
                        return ({strength:'Força',dexterity:'Destreza',constitution:'Constituição',
                            intelligence:'Inteligência',wisdom:'Sabedoria',charisma:'Carisma',none:'Sem Atributo'})[key] || 'Força';
                    },
                    damageModifierKeys(part, weapon=null) {
                        if (!part) return [];
                        const target=weapon || this.form?.properties?.weapon;
                        const primary=target?.allowed_abilities?.[0] || 'strength';
                        const entries=Array.isArray(part.ability_modifiers)
                            ? part.ability_modifiers : (part.include_ability ? ['primary'] : []);
                        return [...new Set(entries.map(key=>key==='primary' ? primary : key)
                            .filter(key=>key!=='none' && ['strength','dexterity','constitution','intelligence','wisdom','charisma'].includes(key)))];
                    },
                    damageModifierText(part,weapon=null) {
                        return this.damageModifierKeys(part,weapon).map(key=>this.weaponAbilityName(key)).join(' + ');
                    },
                    damageModifierSelected(part,key) {
                        return Array.isArray(part?.ability_modifiers) && part.ability_modifiers.includes(key);
                    },
                    // O atributo inicial do ataque tem o marcador visual, sem criar um chip duplicado.
                    // A chave persistida "primary" continua dinâmica para a ficha do Player.
                    weaponPrimaryAbilityKey(weapon=null) {
                        const key=weapon?.allowed_abilities?.[0] || this.form?.properties?.weapon?.allowed_abilities?.[0] || 'strength';
                        return key === 'none' ? null : key;
                    },
                    damageModifierSelectedForAbility(part,key) {
                        return this.damageModifierSelected(part,key) ||
                            (this.weaponPrimaryAbilityKey()===key && this.damageModifierSelected(part,'primary'));
                    },
                    toggleDamageModifierForAbility(part,key) {
                        if (!part) return;
                        const primary=this.weaponPrimaryAbilityKey();
                        let keys=Array.isArray(part.ability_modifiers) ? [...part.ability_modifiers] : [];
                        if (primary === key) {
                            // Selecionar o atributo do ataque preserva a ligação dinâmica à ficha.
                            const active=keys.includes('primary') || keys.includes(key);
                            keys=keys.filter(k=>k!=='primary' && k!==key);
                            if (!active) keys.push('primary');
                        } else {
                            const active=keys.includes(key);
                            keys=keys.filter(k=>k!==key);
                            if (!active) keys.push(key);
                        }
                        part.ability_modifiers=keys;
                        part.include_ability=keys.includes('primary');
                    },
                    toggleDamageModifier(part,key) {
                        if (!part) return;
                        const keys=Array.isArray(part.ability_modifiers) ? [...part.ability_modifiers] : [];
                        const current=keys.indexOf(key);
                        if (current>=0) keys.splice(current,1); else keys.push(key);
                        part.ability_modifiers=keys;
                        part.include_ability=keys.includes('primary');
                    },
                    damageExpression(part,weapon=null) {
                        if (!part) return '';
                        const dice=`${Math.max(1,parseInt(part.count)||1)}${part.die||'d6'}`;
                        const attrs=this.damageModifierText(part,weapon);
                        const suffix=attrs ? ' + '+attrs : '';
                        const bonus=Number(part.flat_bonus)>0 ? ` + ${part.flat_bonus}` : '';
                        return dice+suffix+bonus;
                    },
                    weaponDamageCompact(weapon) {
                        if (!weapon?.damage) return 'Defina o dano';
                        const parts=[weapon.damage,...(weapon.extra_damage||[])];
                        const dice=parts.map(part=>`${Math.max(1,parseInt(part.count)||1)}${part.die||'d6'}`).join(' + ');
                        const mods=parts.reduce((sum,part)=>sum+this.damageModifierKeys(part,weapon).length,0);
                        const magic=weapon.enchantment_enabled ? Math.max(1,Number(weapon.magic_bonus)||1) : 0;
                        return dice + (mods ? ` · ${mods} mod.` : '') + (magic ? ` · +${magic} mágico` : '');
                    },
                    weaponAbilityAllowed(key) {
                        return this.form?.properties?.weapon?.allowed_abilities?.includes(key) || false;
                    },
                    weaponAbilityOrder(key) {
                        const idx=this.form?.properties?.weapon?.allowed_abilities?.indexOf(key) ?? -1;
                        return idx>=0 ? idx+1 : '';
                    },
                    weaponAbilityLocked(key) {
                        return (['strength','dexterity','none'].includes(key)) && this.traitSelected('finesse');
                    },
                    weaponPrimaryAbilityName(weapon = null) {
                        const key = weapon?.allowed_abilities?.[0] || this.form?.properties?.weapon?.allowed_abilities?.[0] || 'strength';
                        return ({strength:'Força',dexterity:'Destreza',constitution:'Constituição',
                            intelligence:'Inteligência',wisdom:'Sabedoria',charisma:'Carisma',none:'Sem Atributo'})[key] || 'Força';
                    },
                    ensureFinesseWeaponAbilities(weapon) {
                        if (!weapon || !Array.isArray(weapon.traits) ||
                            !weapon.traits.some(t=>!t.custom && t.key==='finesse')) return;
                        if (!Array.isArray(weapon.allowed_abilities)) weapon.allowed_abilities=[];
                        const selected=weapon.allowed_abilities.filter(key=>key!=='none');
                        const preferred=selected.filter(key=>['strength','dexterity'].includes(key));
                        const ordered=preferred.length ? preferred : ['strength'];
                        for (const key of ['strength','dexterity']) {
                            if (!ordered.includes(key)) ordered.push(key);
                        }
                        weapon.allowed_abilities=[...ordered,...selected.filter(key=>!ordered.includes(key))];
                    },
                    toggleWeaponAbility(key) {
                        const weapon=this.form?.properties?.weapon;
                        if (!weapon || this.weaponAbilityLocked(key)) return;
                        if (!Array.isArray(weapon.allowed_abilities)) weapon.allowed_abilities=[];
                        if (key === 'none') {
                            weapon.allowed_abilities=['none'];
                            weapon.damage.ability='none';
                            [weapon.damage,...(weapon.extra_damage||[])].forEach(part=>{
                                part.ability_modifiers=(part.ability_modifiers||[]).filter(key=>key!=='primary');
                                part.include_ability=false;
                            });
                            return;
                        }
                        const previouslyNone = weapon.allowed_abilities.includes('none');
                        if (previouslyNone) weapon.allowed_abilities=[];
                        const idx=weapon.allowed_abilities.indexOf(key);
                        if (idx>=0) {
                            if (weapon.allowed_abilities.length===1) return;
                            weapon.allowed_abilities.splice(idx,1);
                        } else weapon.allowed_abilities.push(key);
                        this.ensureFinesseWeaponAbilities(weapon);
                        weapon.damage.ability=weapon.allowed_abilities[0];
                        if (previouslyNone) {
                            weapon.damage.ability_modifiers=['primary',...(weapon.damage.ability_modifiers||[]).filter(key=>key!=='primary')];
                            weapon.damage.include_ability=true;
                        }
                    },
                    get weaponEnchantFeature() {
                        return (this.form?.properties?.features||[]).find(feature=>feature?.system_key==='weapon_enchantment') || null;
                    },
                    enchantDefaultDescription(weapon,bonus=null) {
                        const level=Math.max(1,Math.min(6,Number(bonus ?? weapon?.magic_bonus)||1));
                        return `Você recebe +${level} em testes de ataque e rolagens de dano usando esta arma.`;
                    },
                    isAutoWeaponEnchantmentFeature(feature) {
                        return feature?.system_key==='weapon_enchantment' || (
                            String(feature?.title || '').trim() === 'Arma Encantada' &&
                            /^Você recebe \+\d+ em testes de ataque e rolagens de dano usando esta arma\.$/.test(
                                String(feature?.description || '').replace(/<[^>]*>/g,'').trim())
                        );
                    },
                    ensureWeaponEnchantmentFeature() {
                        const weapon=this.form?.properties?.weapon;
                        const features=this.form?.properties?.features;
                        if (!weapon || !Array.isArray(features)) return;
                        const oldName=String(weapon.enchantment_custom?.title||'').trim();
                        const oldDescription=String(weapon.enchantment_custom?.description||'').trim();
                        const existing=features.find(feature=>this.isAutoWeaponEnchantmentFeature(feature)) ||
                            (oldName ? features.find(feature=>feature.title===oldName && feature.description===oldDescription) : null);
                        if (!weapon.enchantment_enabled) {
                            if (existing) {
                                weapon.enchantment_custom={title:existing.title,description:existing.description};
                                features.splice(features.indexOf(existing),1);
                            }
                            return;
                        }
                        if (existing) {
                            existing.system_key='weapon_enchantment';
                            existing.usage={enabled:false};
                            return;
                        }
                        const cached=weapon.enchantment_custom || {};
                        const feature=this.blankFeature();
                        feature.system_key='weapon_enchantment';
                        feature.title=String(cached.title||'Arma Encantada');
                        feature.description=String(cached.description||this.enchantDefaultDescription(weapon));
                        feature.usage={enabled:false};
                        feature.editor_open=false;
                        features.unshift(feature);
                    },
                    toggleWeaponEnchantment() {
                        const weapon=this.form?.properties?.weapon;
                        if (!weapon) return;
                        weapon.enchantment_enabled=!weapon.enchantment_enabled;
                        weapon.magic_bonus=weapon.enchantment_enabled ? Math.max(1, Number(weapon.magic_bonus)||1) : 0;
                        this.ensureWeaponEnchantmentFeature();
                    },
                    setWeaponEnchantmentBonus(bonus) {
                        const weapon=this.form?.properties?.weapon;
                        if (!weapon) return;
                        const before=this.enchantDefaultDescription(weapon);
                        weapon.magic_bonus=Math.max(1,Math.min(6,Number(bonus)||1));
                        const feature=this.weaponEnchantFeature;
                        // Atualiza somente a descrição padrão: edições pessoais nunca são sobrescritas.
                        if (feature && String(feature.description||'').replace(/<[^>]*>/g,'').trim()===before) {
                            feature.description=this.enchantDefaultDescription(weapon);
                        }
                    },
                    weaponEnchantmentDescription(weapon) {
                        return this.enchantDefaultDescription(weapon);
                    },
                    openWeaponEnchantFeature() {
                        this.ensureWeaponEnchantmentFeature();
                        const feature=this.weaponEnchantFeature;
                        if (feature) {
                            (this.form?.properties?.features||[]).forEach(item=>item.editor_open=item===feature);
                        }
                    },

                    // V66.2.60 — Defesa encantada usa o mesmo contrato mecânico da arma:
                    // o bônus fica em armor/shield.magic_bonus, a habilidade é editável.
                    defenseEnchantmentConfig(category) {
                        const options = {
                            armor: {key:'armor_enchantment', title:'Armadura Encantada', noun:'esta armadura'},
                            shield: {key:'shield_enchantment', title:'Escudo Mágico', noun:'este escudo'},
                        };
                        return options[category] || null;
                    },
                    defenseEnchantmentFeature(category) {
                        const key = this.defenseEnchantmentConfig(category)?.key;
                        return (this.form?.properties?.features || []).find(feature => feature?.system_key === key) || null;
                    },
                    defenseEnchantmentDefaultDescription(category, bonus) {
                        const level = Math.max(1, Math.min(6, parseInt(bonus, 10) || 1));
                        return category === 'armor'
                            ? `Enquanto estiver vestindo esta armadura, você recebe +${level} de bônus mágico na Classe de Armadura.`
                            : `Enquanto estiver empunhando este escudo, você recebe +${level} de bônus mágico na Classe de Armadura.`;
                    },
                    ensureDefenseEnchantmentFeature(category) {
                        const def = this.defenseEnchantmentConfig(category);
                        const equipment = this.form?.properties?.[category];
                        const features = this.form?.properties?.features;
                        if (!def || !equipment || !Array.isArray(features)) return;
                        const cached = equipment.enchantment_custom || {};
                        const current = features.find(feature => feature.system_key === def.key)
                            || (cached.title ? features.find(feature =>
                                feature.title === cached.title && feature.description === cached.description) : null);
                        if (!equipment.enchantment_enabled) {
                            if (current) {
                                equipment.enchantment_custom = {title:current.title, description:current.description};
                                features.splice(features.indexOf(current), 1);
                            }
                            return;
                        }
                        if (current) {
                            current.system_key = def.key;
                            current.usage = {enabled:false};
                            return;
                        }
                        const feature = this.blankFeature();
                        feature.system_key = def.key;
                        feature.title = String(cached.title || def.title);
                        feature.description = String(cached.description ||
                            this.defenseEnchantmentDefaultDescription(category, equipment.magic_bonus));
                        feature.usage = {enabled:false};
                        feature.editor_open = false;
                        features.unshift(feature);
                    },
                    toggleDefenseEnchantment(category) {
                        if (!this.defenseEnchantmentConfig(category)) return;
                        const equipment = this.form?.properties?.[category];
                        if (!equipment) return;
                        equipment.enchantment_enabled = !equipment.enchantment_enabled;
                        equipment.magic_bonus = equipment.enchantment_enabled
                            ? Math.max(1, Math.min(6, parseInt(equipment.magic_bonus, 10) || 1)) : 0;
                        this.ensureDefenseEnchantmentFeature(category);
                    },
                    setDefenseEnchantmentBonus(category, bonus) {
                        if (!this.defenseEnchantmentConfig(category)) return;
                        const equipment = this.form?.properties?.[category];
                        if (!equipment?.enchantment_enabled) return;
                        const oldText = this.defenseEnchantmentDefaultDescription(category, equipment.magic_bonus);
                        equipment.magic_bonus = Math.max(1, Math.min(6, parseInt(bonus, 10) || 1));
                        const feature = this.defenseEnchantmentFeature(category);
                        if (feature && String(feature.description || '').replace(/<[^>]*>/g,'').trim() === oldText) {
                            feature.description = this.defenseEnchantmentDefaultDescription(category, equipment.magic_bonus);
                        }
                    },
                    openDefenseEnchantmentFeature(category) {
                        this.ensureDefenseEnchantmentFeature(category);
                        const feature = this.defenseEnchantmentFeature(category);
                        if (feature) (this.form?.properties?.features || []).forEach(item => item.editor_open = item === feature);
                    },

                    setDamageType(part, value) {
                        if (!part) return;

                        part.type = String(value ?? '').trim();
                        part.type_custom_mode = false;
                    },

                    setCustomDamageType(part) {
                        if (!part) return;

                        if (this.damageTypeIsStandard(part.type)) {
                            part.type = '';
                        }

                        part.type_custom_mode = true;
                    },

                    addExtraDamage() {
                        this.form.properties.weapon.extra_damage.push({
                            count: 1,
                            die: 'd6',
                            type: 'Elétrico',
                            type_custom_mode: false,
                            type_options: ['Elétrico'],
                            custom_type: '',
                            include_ability: false,
                            ability_modifiers: [],
                            modifiers_open: false,
                            flat_bonus: 0,
                            bonus_condition: '',
                            extra_bonus_open: false,
                        });
                    },

                    removeExtraDamage(index) {
                        this.form.properties.weapon.extra_damage.splice(index, 1);
                    },

                    setArmorCategory(category) {
                        const armor = this.form?.properties?.armor;
                        if (!armor || !['light','medium','heavy','custom'].includes(category)) return;
                        armor.category = category;
                        if (category !== 'custom') armor.category_custom = '';
                        const defaults = this.armorDexterityDefaults(category);
                        armor.defense_ability = defaults.mode === 'none' ? 'none' : 'dexterity';
                        armor.defense_ability_mode = defaults.mode;
                        armor.defense_ability_cap = defaults.cap;
                        this.syncArmorLegacyDexterity();
                    },

                    armorAbilityLabel(ability) {
                        return {
                            strength: 'Força', dexterity: 'Destreza', constitution: 'Constituição',
                            intelligence: 'Inteligência', wisdom: 'Sabedoria', charisma: 'Carisma', none: 'Nenhum',
                        }[ability] ?? 'Nenhum';
                    },

                    setArmorDefenseAbility(ability) {
                        const armor = this.form?.properties?.armor;
                        if (!armor || !['strength','dexterity','constitution','intelligence','wisdom','charisma','none'].includes(ability)) return;
                        armor.defense_ability = ability;
                        armor.defense_ability_mode = ability === 'none' ? 'none'
                            : (armor.defense_ability_mode === 'capped' ? 'capped' : 'full');
                        // Um atributo não pode ser somado duas vezes à CA.
                        armor.ability_modifiers = (armor.ability_modifiers ?? []).filter(extra => extra !== ability);
                        this.syncArmorLegacyDexterity();
                    },

                    setArmorAbilityMode(mode) {
                        const armor = this.form?.properties?.armor;
                        if (!armor || armor.defense_ability === 'none' || !['full','capped'].includes(mode)) return;
                        armor.defense_ability_mode = mode;
                        this.syncArmorLegacyDexterity();
                    },

                    setArmorDexterityMode(mode) {
                        // Compatibilidade: método mantido para eventuais chamadas legadas.
                        if (mode === 'none') this.setArmorDefenseAbility('none');
                        else this.setArmorAbilityMode(mode);
                    },

                    syncArmorLegacyDexterity() {
                        const armor = this.form?.properties?.armor;
                        if (!armor) return;
                        armor.dexterity_mode = armor.defense_ability === 'dexterity' ? armor.defense_ability_mode : 'none';
                        armor.dexterity_cap = armor.defense_ability_cap;
                    },

                    setArmorAbilityCap(raw) {
                        const armor = this.form?.properties?.armor;
                        if (!armor) return;
                        const digits = String(raw ?? '').replace(/\D/g, '');
                        const number = Number.parseInt(digits, 10);
                        if (Number.isFinite(number)) armor.defense_ability_cap = Math.max(0, Math.min(99, number));
                        this.syncArmorLegacyDexterity();
                    },

                    stepArmorAbilityCap(delta) {
                        const armor = this.form?.properties?.armor;
                        if (!armor) return;
                        armor.defense_ability_cap = Math.max(0, Math.min(99, (Number.parseInt(armor.defense_ability_cap,10) || 0) + delta));
                        this.syncArmorLegacyDexterity();
                    },

                    setArmorStealthDisadvantage(enabled) {
                        const armor = this.form?.properties?.armor;
                        if (armor) armor.stealth_disadvantage = !!enabled;
                    },

                    toggleArmorStrengthRequirement() {
                        const armor = this.form?.properties?.armor;
                        if (!armor) return;
                        armor.strength_requirement_enabled = !armor.strength_requirement_enabled;
                        armor.strength_requirement = armor.strength_requirement_enabled
                            ? Math.max(1, Math.min(30, Number.parseInt(armor.strength_requirement, 10) || 13)) : null;
                    },

                    setArmorStrengthRequirement(raw) {
                        const armor = this.form?.properties?.armor;
                        if (!armor || !armor.strength_requirement_enabled) return;
                        const digits = String(raw ?? '').replace(/\D/g, '');
                        const value = Number.parseInt(digits, 10);
                        if (Number.isFinite(value)) armor.strength_requirement = Math.max(1, Math.min(30, value));
                    },

                    stepArmorStrengthRequirement(delta) {
                        const armor = this.form?.properties?.armor;
                        if (!armor || !armor.strength_requirement_enabled) return;
                        this.setArmorStrengthRequirement((Number.parseInt(armor.strength_requirement,10) || 13) + delta);
                    },

                    toggleArmorAbilityModifier(ability) {
                        const armor = this.form?.properties?.armor;
                        if (!armor || ability === armor.defense_ability || !['strength','dexterity','constitution','intelligence','wisdom','charisma'].includes(ability)) return;
                        const current = Array.isArray(armor.ability_modifiers) ? [...armor.ability_modifiers] : [];
                        armor.ability_modifiers = current.includes(ability)
                            ? current.filter(value => value !== ability)
                            : [...current, ability];
                    },

                    abilityShortLabel(ability) {
                        return {
                            strength: 'FOR', dexterity: 'DES', constitution: 'CON',
                            intelligence: 'INT', wisdom: 'SAB', charisma: 'CAR',
                        }[ability] ?? String(ability ?? '').toUpperCase();
                    },

                    armorFormulaText(armor) {
                        if (!armor) return '';
                        const parts = [`CA ${Math.max(0, Number.parseInt(armor.base_ac,10) || 0)}`];
                        const ability = armor.defense_ability ?? (armor.dexterity_mode === 'none' ? 'none' : 'dexterity');
                        const mode = armor.defense_ability_mode ?? armor.dexterity_mode;
                        const cap = Number.parseInt(armor.defense_ability_cap ?? armor.dexterity_cap,10) || 0;
                        if (ability !== 'none' && mode === 'full') parts.push('+ ' + this.abilityShortLabel(ability));
                        if (ability !== 'none' && mode === 'capped') parts.push('+ ' + this.abilityShortLabel(ability) + ' (máx. +' + Math.max(0,cap) + ')');
                        (armor.ability_modifiers ?? []).forEach(extra => {
                            if (extra !== ability) parts.push('+ ' + this.abilityShortLabel(extra));
                        });
                        const magic = Number.parseInt(armor.magic_bonus,10) || 0;
                        if (magic) parts.push((magic > 0 ? '+' : '') + magic + ' mágico');
                        return parts.join(' ');
                    },

                    shieldTotalBonus(shield) {
                        if (!shield) return 0;

                        return (
                            (parseInt(shield.ac_bonus) || 0)
                            + (parseInt(shield.magic_bonus) || 0)
                        );
                    },

                    signedNumber(value) {
                        const parsed = parseInt(value) || 0;
                        return parsed > 0 ? `+${parsed}` : String(parsed);
                    },

                    weaponBaseDamageDice(weapon) {
                        if (!weapon?.damage) return '';

                        const count =
                            Math.max(
                                1,
                                parseInt(weapon.damage.count) || 1
                            );

                        let die =
                            String(
                                weapon.damage.die
                                ?? ''
                            ).trim();

                        if (!die) return '';

                        if (!die.toLowerCase().startsWith('d')) {
                            die = `d${die}`;
                        }

                        return `${count}${die}`;
                    },

                    weaponDamagePreview(weapon) {
                        if (!weapon?.damage) return '';
                        const render = part => {
                            if (!part) return '';
                            const count=Math.max(1,parseInt(part.count)||1);
                            const die=String(part.die||'').trim();
                            const formula=die ? `${count}${die}` : '';
                            const attr=this.damageModifierText(part,weapon) ? ' + ' + this.damageModifierText(part,weapon) : '';
                            const extra=Number(part.flat_bonus)>0 ? ` + ${part.flat_bonus}` : '';
                            const cond=extra && String(part.bonus_condition||'').trim()
                                ? ` (${String(part.bonus_condition).trim()})` : '';
                            const suffix=attr+extra+cond;
                            const types=[...(Array.isArray(part.type_options)?part.type_options:[]),
                                ...(part.custom_type ? [part.custom_type] : [])];
                            const type=types.length ? types.join(' ou ') : String(part.type||'').trim();
                            return `${formula}${suffix}${type?' '+type:''}`.trim();
                        };
                        const bonus=weapon.enchantment_enabled ? Math.max(1,Number(weapon.magic_bonus)||1) : 0;
                        let base=render(weapon.damage);
                        if (bonus) base+=` +${bonus}`;
                        return [base,...(weapon.extra_damage||[]).map(render)].filter(Boolean).join(' + ');
                    },

                    addCustomProperty() {
                        this.form.properties.custom_properties.push({
                            title: '',
                            value: '',
                            description: '',
                        });
                    },

                    removeCustomProperty(index) {
                        this.form.properties.custom_properties.splice(index, 1);
                    },

                    addFeature(groupKey = null) {
                        const features = this.featuresForEditorGroup(groupKey);
                        if (!Array.isArray(features)) return;

                        features.forEach(
                            feature => {
                                feature.editor_open = false;
                            }
                        );

                        features.push(
                            this.blankFeature()
                        );
                    },

                    toggleFeatureDrawer(feature) {
                        if (!feature) return;

                        feature.editor_open =
                            !feature.editor_open;
                    },

                    moveFeature(index, direction, groupKey = null) {
                        const features = this.featuresForEditorGroup(groupKey);

                        if (
                            !Array.isArray(features)
                            || ![-1, 1].includes(direction)
                        ) {
                            return;
                        }

                        const target =
                            index + direction;

                        if (
                            target < 0
                            || target >= features.length
                        ) {
                            return;
                        }

                        const [moved] =
                            features.splice(index, 1);

                        features.splice(
                            target,
                            0,
                            moved
                        );
                    },

                    removeFeature(index, groupKey = null) {
                        const features = this.featuresForEditorGroup(groupKey);
                        if (!Array.isArray(features) || index < 0 || index >= features.length) return;
                        const systemKey = features[index]?.system_key;
                        if (systemKey === 'spell_scroll_cast' || systemKey === 'spell_scroll_rules') return;
                        if (systemKey === 'weapon_enchantment') {
                            this.toggleWeaponEnchantment();
                            return;
                        }
                        if (systemKey === 'armor_enchantment' || systemKey === 'shield_enchantment') {
                            this.toggleDefenseEnchantment(systemKey === 'armor_enchantment' ? 'armor' : 'shield');
                            return;
                        }
                        features.splice(index, 1);
                    },

                    setFeatureUsage(feature, enabled) {
                        if (!feature) return;
                        if (!enabled) {
                            feature.usage.enabled = false;
                            return;
                        }
                        feature.usage = this.normalizeFeatureUsage({...feature.usage, enabled:true});
                        this.clampFeatureUsage(feature);
                    },

                    setFeatureCounterMode(feature, mode) {
                        if (!feature?.usage || !['spend','build'].includes(mode)) return;
                        feature.usage.counter_mode = mode;
                        if (mode === 'build') {
                            // Uso único é um comportamento de Consumo, nunca de Acúmulo.
                            if (feature.usage.recovery === 'single_use') {
                                feature.usage.recovery = 'day';
                                feature.usage.recovery_enabled = false;
                            }
                            feature.usage.initial_mode = 'fixed';
                            feature.usage.current = 0;
                            feature.usage.roll_preview = null;
                        } else {
                            this.setFeatureInitialMode(feature, 'full');
                        }
                        this.clampFeatureUsage(feature);
                    },

                    setFeatureInitialMode(feature, mode) {
                        if (!feature?.usage || !['full','fixed','roll'].includes(mode)) return;
                        const usage=feature.usage;
                        if (usage.counter_mode === 'build') return;
                        usage.initial_mode=mode;
                        if (mode === 'roll') usage.roll_trigger = 'manual'; // futuro diálogo no Inventário: dados físicos ou digitais
                        usage.roll_preview=null;
                        if (mode==='full') usage.current=usage.max;
                        if (mode==='fixed') usage.current=Math.max(0,Math.min(usage.max,Number.isFinite(Number(usage.current))?Number(usage.current):usage.max));
                        if (mode==='roll') usage.current=usage.max; // valor-modelo; a rolagem real só acontece ao receber
                        this.clampFeatureUsage(feature);
                    },

                    featureRollFormulaParts(value) {
                        const match=String(value??'').replace(/\s+/g,'').toLowerCase().match(/^(\d{1,2})d(2|3|4|6|8|10|12|20|100)(?:([+-])(\d{1,3}))?$/);
                        if (!match) return null;
                        const count=Number(match[1]);
                        const bonus=Number(match[4]||0)*(match[3]==='-'?-1:1);
                        return count>=1 && count<=99 && Math.abs(bonus)<=999
                            ? {count,faces:Number(match[2]),bonus} : null;
                    },
                    featureRollFormulaValid(value) {
                        return !!this.featureRollFormulaParts(value);
                    },
                    normalizeFeatureRollFormula(feature) {
                        if (!feature?.usage) return;
                        feature.usage.initial_formula=String(feature.usage.initial_formula??'').replace(/\s+/g,'').toLowerCase().slice(0,20);
                        feature.usage.roll_preview=null;
                    },
                    rollFeatureUsagePreview(feature) {
                        if (!feature?.usage) return;
                        const spec=this.featureRollFormulaParts(feature.usage.initial_formula);
                        if (!spec) return;
                        let total=spec.bonus;
                        for(let i=0;i<spec.count;i++) total+=1+Math.floor(Math.random()*spec.faces);
                        feature.usage.roll_preview=Math.max(0,Math.min(Number(feature.usage.max)||1,total));
                    },
                    clampFeatureUsage(feature) {
                        if (!feature?.usage) return;
                        const usage=feature.usage;
                        usage.max=Math.max(1,Math.min(999999,parseInt(usage.max,10)||1));
                        if (usage.counter_mode === 'build') {
                            usage.initial_mode = 'fixed';
                            usage.current = 0;
                        } else if (usage.initial_mode==='full' || usage.initial_mode==='roll') {
                            usage.current = usage.max;
                        } else {
                            usage.current=Math.max(0,Math.min(usage.max,Number.isFinite(Number(usage.current)) ? Math.floor(Number(usage.current)) : 0));
                        }
                        usage.roll_preview=null;
                    },

                    setFeatureBuildRecovery(feature, enabled) {
                        if (!feature?.usage || feature.usage.counter_mode !== 'build') return;
                        feature.usage.recovery_enabled = !!enabled;
                        if (enabled && (!Object.prototype.hasOwnProperty.call(this.recoveryLabels, feature.usage.recovery) || feature.usage.recovery === 'single_use')) {
                            feature.usage.recovery = 'day';
                        }
                        this.clampFeatureUsage(feature);
                    },

                    handleFeatureRecoveryChange(feature) {
                        if (!feature?.usage) return;
                        if (feature.usage.counter_mode === 'build') {
                            if (feature.usage.recovery === 'single_use') feature.usage.recovery = 'day';
                            this.clampFeatureUsage(feature);
                            return;
                        }

                        if (feature.usage.recovery === 'single_use') {
                            /*
                            | Uso Único = cargas permanentes, não "sempre 1/1".
                            | O Mestre define quantas cargas o item possui.
                            */
                            const wasBuild =
                                feature.usage.counter_mode
                                === 'build';

                            feature.usage.counter_mode =
                                'spend';

                            feature.usage.max =
                                Math.max(
                                    1,
                                    parseInt(
                                        feature.usage.max
                                    ) || 1
                                );

                            if (wasBuild) {
                                feature.usage.current =
                                    feature.usage.max;
                            }

                            feature.usage.recovery_custom =
                                '';

                            this.clampFeatureUsage(feature);
                            return;
                        }

                        this.clampFeatureUsage(feature);
                    },

                    cleanProperties() {
                        const properties = this.normalizeProperties(
                            this.form.properties,
                            this.form
                        );

                        if (this.form.type === 'wonderful' && this.form.rarity === 'artifact'
                            && this.form.properties?.vestige?.enabled) {
                            properties.vestige = this.normalizeVestige(this.form.properties.vestige);
                            for (const stage of ['dormant', 'awakened', 'exalted']) {
                                properties.vestige.stages[stage].lore =
                                    properties.vestige.stages[stage].lore.trim();
                                properties.vestige.stages[stage].features =
                                    properties.vestige.stages[stage].features
                                        .map(feature => {
                                            const usage = this.normalizeFeatureUsage(feature.usage);
                                            delete usage.roll_preview;
                                            return {
                                                title: String(feature.title ?? '').trim(),
                                                description: String(feature.description ?? '').trim(),
                                                usage: usage.enabled ? usage : { enabled: false },
                                            };
                                        })
                                        .filter(feature => feature.title || feature.description);
                            }
                        } else {
                            delete properties.vestige;
                        }

                        properties.custom_properties = properties.custom_properties
                            .map(entry => ({
                                title: String(entry.title ?? '').trim(),
                                value: String(entry.value ?? '').trim(),
                                description: String(entry.description ?? '').trim(),
                            }))
                            .filter(entry => entry.title || entry.value || entry.description);

                        properties.features = properties.features
                            .map(feature => {
                                const usage =
                                    this.normalizeFeatureUsage(
                                        feature.usage
                                    );
                                delete usage.roll_preview;

                                return {
                                    title:
                                        String(
                                            feature.title
                                            ?? ''
                                        ).trim(),

                                    description:
                                        String(
                                            feature.description
                                            ?? ''
                                        ).trim(),
                                    ...(['weapon_enchantment','armor_enchantment','shield_enchantment','spell_scroll_rules','spell_scroll_cast'].includes(feature.system_key)
                                        ? {system_key:feature.system_key} : {}),

                                    /*
                                    | Passiva não possui contador. Mantemos
                                    | somente o booleano e não carregamos
                                    | 1/1 / Dia como se fosse uma regra real.
                                    */
                                    usage:
                                        usage.enabled
                                            ? usage
                                            : {
                                                enabled: false,
                                            },
                                };
                            })
                            .filter(
                                feature =>
                                    feature.title
                                    || feature.description
                            );

                        // Vestígios NÃO possuem uma segunda lista de habilidades comuns.
                        // Guardamos os três conjuntos exclusivamente em properties.vestige.stages.
                        if (properties.vestige?.enabled && this.form.type === 'wonderful'
                            && this.form.rarity === 'artifact') {
                            properties.features = [];
                        }

                        if (properties.weapon) {
                            if (properties.weapon.damage) {
                                this.syncDamageTypes(properties.weapon.damage);
                                delete properties.weapon.damage.type_custom_mode;
                                delete properties.weapon.damage.expression_input;
                                delete properties.weapon.damage.extra_bonus_open;
                                delete properties.weapon.damage.modifiers_open;
                                properties.weapon.damage.include_ability=(properties.weapon.damage.ability_modifiers||[]).includes('primary');
                                properties.weapon.damage.flat_bonus=Math.max(0,Math.min(99,parseInt(properties.weapon.damage.flat_bonus,10)||0));
                                properties.weapon.damage.bonus_condition=String(properties.weapon.damage.bonus_condition||'').trim().slice(0,180);
                            }
                            properties.weapon.enchantment_enabled=!!properties.weapon.enchantment_enabled;
                            properties.weapon.magic_bonus=properties.weapon.enchantment_enabled
                                ? Math.max(1,Math.min(6,parseInt(properties.weapon.magic_bonus)||1)) : 0;
                            const customEnchant=properties.features?.find(feature=>feature.system_key==='weapon_enchantment');
                            if (customEnchant) {
                                properties.weapon.enchantment_custom={title:customEnchant.title,description:customEnchant.description};
                            }
                            properties.weapon.allowed_abilities=Array.isArray(properties.weapon.allowed_abilities)
                                ? [...new Set(properties.weapon.allowed_abilities)].filter(key=>
                                    ['strength','dexterity','constitution','intelligence','wisdom','charisma','none'].includes(key))
                                : ['strength'];
                            if (!properties.weapon.allowed_abilities.length) properties.weapon.allowed_abilities=['strength'];
                            if (properties.weapon.allowed_abilities.includes('none')) properties.weapon.allowed_abilities=['none'];
                            this.ensureFinesseWeaponAbilities(properties.weapon);
                            properties.weapon.damage.ability=properties.weapon.allowed_abilities[0];
                            if (properties.weapon.damage.ability==='none') {
                                properties.weapon.damage.ability_modifiers=(properties.weapon.damage.ability_modifiers||[]).filter(key=>key!=='primary');
                                properties.weapon.damage.include_ability=false;
                                (properties.weapon.extra_damage||[]).forEach(part=>{
                                    part.ability_modifiers=(part.ability_modifiers||[]).filter(key=>key!=='primary');
                                    part.include_ability=false;
                                });
                            }

                            properties.weapon.traits = properties.weapon.traits
                                .map(trait => ({
                                    key: String(trait.key ?? '').trim(),
                                    name: String(trait.name ?? '').trim(),
                                    description: String(trait.description ?? '').trim(),
                                    custom: !!trait.custom,
                                }))
                                .filter(trait => trait.name);

                            properties.weapon.masteries = properties.weapon.masteries
                                .map(mastery => ({
                                    key: String(mastery.key ?? '').trim(),
                                    name: String(mastery.name ?? '').trim(),
                                    description: String(mastery.description ?? '').trim(),
                                    custom: !!mastery.custom,
                                }))
                                .filter(mastery => mastery.name);

                            properties.weapon.extra_damage = properties.weapon.extra_damage
                                .map(part => {
                                    this.syncDamageTypes(part);
                                    return {
                                        count: Math.max(1,parseInt(part.count)||1),
                                        die: String(part.die||'').trim(),
                                        type: String(part.type||'').trim(),
                                        type_options: [...(part.type_options||[])],
                                        custom_type: String(part.custom_type||'').trim(),
                                        include_ability: (part.ability_modifiers||[]).includes('primary'),
                                        ability_modifiers: [...(part.ability_modifiers||[])],
                                        flat_bonus: Math.max(0,Math.min(99,parseInt(part.flat_bonus,10)||0)),
                                        bonus_condition: String(part.bonus_condition||'').trim().slice(0,180),
                                    };
                                })
                                .filter(part=>part.die||part.type);

                            const hasRange =
                                properties.weapon.traits.some(
                                    trait =>
                                        !trait.custom
                                        && trait.key === 'range'
                                );

                            const hasReach =
                                properties.weapon.traits.some(
                                    trait =>
                                        !trait.custom
                                        && trait.key === 'reach'
                                );

                            const hasLoading =
                                properties.weapon.traits.some(
                                    trait =>
                                        !trait.custom
                                        && trait.key === 'loading'
                                );

                            if (hasRange) {
                                properties.weapon.range_normal_ft =
                                    this.nullableNumber(
                                        properties.weapon.range_normal_ft,
                                        true
                                    );

                                properties.weapon.range_long_ft =
                                    this.nullableNumber(
                                        properties.weapon.range_long_ft,
                                        true
                                    );
                            } else {
                                delete properties.weapon.range_normal_ft;
                                delete properties.weapon.range_long_ft;
                            }

                            if (hasReach) {
                                properties.weapon.reach_ft =
                                    this.nullableNumber(
                                        properties.weapon.reach_ft,
                                        true
                                    ) ?? 10;
                            } else {
                                delete properties.weapon.reach_ft;
                            }

                            if (hasLoading) {
                                const capacity = this.nullableNumber(
                                    properties.weapon.loading_capacity,
                                    false
                                );

                                properties.weapon.loading_capacity =
                                    capacity !== null
                                        ? Math.max(1, Math.min(999999, capacity))
                                        : null;
                            } else {
                                delete properties.weapon.loading_capacity;
                            }

                            /*
                            | Garante que o formato temporário da V10.5 nunca
                            | seja persistido novamente.
                            */
                            delete properties.weapon.range;
                            delete properties.weapon.reach;
                        }

                        // A habilidade encantada agora é uma habilidade real e editável.
                        // Preserva título/descrição personalizados e o marcador estável.
                        if (properties.weapon?.enchantment_enabled) {
                            const matches=properties.features.filter(f=>this.isAutoWeaponEnchantmentFeature(f));
                            if (matches.length) {
                                matches[0].system_key='weapon_enchantment';
                                matches[0].usage={enabled:false};
                                properties.features=properties.features.filter(f=>!this.isAutoWeaponEnchantmentFeature(f) || f===matches[0]);
                            } else {
                                properties.features.unshift({system_key:'weapon_enchantment',title:'Arma Encantada',
                                    description:this.weaponEnchantmentDescription(properties.weapon),usage:{enabled:false}});
                            }
                        } else {
                            properties.features=properties.features.filter(f=>f.system_key!=='weapon_enchantment');
                        }

                        for (const category of ['armor','shield']) {
                            const def = this.defenseEnchantmentConfig(category);
                            const equip = properties[category];
                            if (!equip) {
                                properties.features = properties.features.filter(feature => feature.system_key !== def.key);
                                continue;
                            }
                            equip.enchantment_enabled = !!equip.enchantment_enabled;
                            equip.magic_bonus = equip.enchantment_enabled
                                ? Math.max(1,Math.min(6,parseInt(equip.magic_bonus,10) || 1)) : 0;
                            const matches = properties.features.filter(feature => feature.system_key === def.key);
                            if (!equip.enchantment_enabled) {
                                if (matches[0]) equip.enchantment_custom = {
                                    title:matches[0].title, description:matches[0].description};
                                properties.features = properties.features.filter(feature => feature.system_key !== def.key);
                            } else if (matches.length) {
                                equip.enchantment_custom = {title:matches[0].title, description:matches[0].description};
                                matches[0].usage = {enabled:false};
                                properties.features = properties.features.filter(feature =>
                                    feature.system_key !== def.key || feature === matches[0]);
                            } else {
                                const custom = equip.enchantment_custom || {};
                                properties.features.unshift({
                                    system_key:def.key,
                                    title:custom.title || def.title,
                                    description:custom.description || this.defenseEnchantmentDefaultDescription(category, equip.magic_bonus),
                                    usage:{enabled:false},
                                });
                            }
                        }
                        return properties;
                    },

                    deriveLegacyFields(properties) {
                        const result = {
                            armor_class: null,
                            damage: null,
                            attack_bonus: null,
                            damage_bonus: null,
                        };

                        const weapon = properties?.weapon;

                        if (weapon) {
                            const magic = parseInt(weapon.magic_bonus) || 0;
                            const count = Math.max(
                                1,
                                parseInt(weapon?.damage?.count) || 1
                            );
                            const die = String(
                                weapon?.damage?.die ?? ''
                            ).trim();

                            result.damage = die
                                ? `${count}${die}`
                                : null;

                            result.attack_bonus = magic !== 0
                                ? magic
                                : null;

                            result.damage_bonus = magic !== 0
                                ? magic
                                : null;
                        }

                        const armor = properties?.armor;

                        if (armor) {
                            const base = this.nullableNumber(armor.base_ac);
                            const magic = parseInt(armor.magic_bonus) || 0;

                            result.armor_class = base !== null
                                ? base + magic
                                : null;
                        }

                        return result;
                    },

                    // V66.1.39: o ajuste gera um PNG normal e reaproveita o upload existente.
                    handleImage(event) {
                        const file = event?.target?.files?.[0];
                        if (file) this.openCropEditor(file);
                    },

                    openCropEditor(file = null) {
                        if (!file && !(this.imagePreview || this.form?.image_url)) return;
                        this.closeCropEditor();
                        this.cropZoom = 1;
                        this.cropOffsetX = 0;
                        this.cropOffsetY = 0;
                        this.cropBaseW = this.cropFrameSize;
                        this.cropBaseH = this.cropFrameSize;
                        this.cropReady = false;
                        this.cropSource = this.imagePreview || this.form?.image_url || null;
                        this.cropOpen = true;
                        if (file) this.useCropFile(file);
                    },

                    useCropFile(file) {
                        if (!file || !['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 8 * 1024 * 1024) {
                            this.showToast('Use PNG, JPG ou WebP de até 8 MB.', true);
                            return;
                        }
                        if (this.cropTempUrl) URL.revokeObjectURL(this.cropTempUrl);
                        this.cropTempUrl = URL.createObjectURL(file);
                        this.cropReady = false;
                        this.cropZoom = 1;
                        this.cropOffsetX = 0;
                        this.cropOffsetY = 0;
                        this.cropSource = this.cropTempUrl;
                    },

                    replaceCropImage(event) {
                        const file = event?.target?.files?.[0];
                        if (file) this.useCropFile(file);
                    },

                    cropImageLoaded(event) {
                        const image = event?.target;
                        if (!image?.naturalWidth || !image?.naturalHeight) return;
                        const aspect = image.naturalWidth / image.naturalHeight;
                        this.cropBaseW = aspect >= 1 ? this.cropFrameSize * aspect : this.cropFrameSize;
                        this.cropBaseH = aspect >= 1 ? this.cropFrameSize : this.cropFrameSize / aspect;
                        this.cropReady = true;
                        this.clampCropOffset();
                    },

                    cropImageStyle() {
                        return {
                            width: this.cropBaseW + 'px',
                            height: this.cropBaseH + 'px',
                            transform: 'translate(calc(-50% + ' + this.cropOffsetX + 'px), calc(-50% + ' + this.cropOffsetY + 'px)) scale(' + this.cropZoom + ')',
                        };
                    },

                    // Um pouco de folga permite alinhar fotos transparentes sem precisar ampliar.
                    cropAllowance(axis) {
                        const dimension = axis === 'x' ? this.cropBaseW : this.cropBaseH;
                        const outside = Math.max(0, (dimension * this.cropZoom - this.cropFrameSize) / 2);
                        return outside + this.cropFrameSize * 0.13;
                    },

                    cropHorizontalPercent() {
                        const max = this.cropAllowance('x');
                        return max ? Math.round(50 + this.cropOffsetX / max * 50) : 50;
                    },

                    cropVerticalPercent() {
                        const max = this.cropAllowance('y');
                        return max ? Math.round(50 + this.cropOffsetY / max * 50) : 50;
                    },

                    setCropHorizontalPercent(value) {
                        const percentage = Math.max(0, Math.min(100, Number(value) || 0));
                        this.cropOffsetX = (percentage / 50 - 1) * this.cropAllowance('x');
                    },

                    setCropVerticalPercent(value) {
                        const percentage = Math.max(0, Math.min(100, Number(value) || 0));
                        this.cropOffsetY = (percentage / 50 - 1) * this.cropAllowance('y');
                    },

                    alignCropHorizontal(position) {
                        this.setCropHorizontalPercent(position === 'start' ? 0 : position === 'end' ? 100 : 50);
                    },

                    clampCropOffset() {
                        const allowanceX = this.cropAllowance('x');
                        const allowanceY = this.cropAllowance('y');
                        this.cropOffsetX = Math.max(-allowanceX, Math.min(allowanceX, this.cropOffsetX));
                        this.cropOffsetY = Math.max(-allowanceY, Math.min(allowanceY, this.cropOffsetY));
                    },

                    resetCrop() {
                        this.cropZoom = 1;
                        this.cropOffsetX = 0;
                        this.cropOffsetY = 0;
                    },

                    startCropDrag(event) {
                        if (!this.cropReady || event.button !== 0) return;
                        const bounds = event.currentTarget.getBoundingClientRect();
                        this.cropPointer = {
                            id: event.pointerId,
                            x: event.clientX,
                            y: event.clientY,
                            scale: this.cropFrameSize / Math.max(1, bounds.width),
                            offsetX: this.cropOffsetX,
                            offsetY: this.cropOffsetY,
                        };
                        event.currentTarget.setPointerCapture?.(event.pointerId);
                    },

                    moveCropDrag(event) {
                        if (!this.cropPointer || event.pointerId !== this.cropPointer.id) return;
                        this.cropOffsetX = this.cropPointer.offsetX + (event.clientX - this.cropPointer.x) * this.cropPointer.scale;
                        this.cropOffsetY = this.cropPointer.offsetY + (event.clientY - this.cropPointer.y) * this.cropPointer.scale;
                        this.clampCropOffset();
                    },

                    stopCropDrag(event) {
                        if (this.cropPointer?.id === event.pointerId) this.cropPointer = null;
                    },

                    async applyCropImage() {
                        const image = this.$refs.vaultCropImage;
                        if (!this.cropReady || !image?.complete || this.cropSaving) return;
                        this.cropSaving = true;
                        try {
                            const size = 640;
                            const scale = size / this.cropFrameSize;
                            const canvas = document.createElement('canvas');
                            canvas.width = size;
                            canvas.height = size;
                            const ctx = canvas.getContext('2d');
                            if (!ctx) throw new Error('Não foi possível criar o recorte.');
                            ctx.imageSmoothingEnabled = true;
                            ctx.imageSmoothingQuality = 'high';
                            const w = this.cropBaseW * this.cropZoom * scale;
                            const h = this.cropBaseH * this.cropZoom * scale;
                            ctx.drawImage(image,
                                (size - w) / 2 + this.cropOffsetX * scale,
                                (size - h) / 2 + this.cropOffsetY * scale,
                                w, h);
                            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                            if (!blob) throw new Error('Não foi possível preparar a imagem.');
                            if (blob.size > 8 * 1024 * 1024) throw new Error('Imagem resultante maior que 8 MB.');
                            const file = new File([blob], 'item-enquadrado.png', {type:'image/png'});
                            this.clearImagePreview();
                            this.imageFile = file;
                            this.imagePreview = URL.createObjectURL(file);
                            this.form.remove_image = false;
                            this.closeCropEditor();
                            this.showToast('Enquadramento aplicado. Salve o item para confirmar.');
                        } catch (error) {
                            this.showToast(error?.message || 'Não foi possível editar esta imagem.', true);
                        } finally {
                            this.cropSaving = false;
                        }
                    },

                    closeCropEditor() {
                        this.cropOpen = false;
                        this.cropReady = false;
                        this.cropPointer = null;
                        this.cropSource = null;
                        if (this.cropTempUrl) {
                            URL.revokeObjectURL(this.cropTempUrl);
                            this.cropTempUrl = null;
                        }
                    },

                    removeImage() {
                        this.closeCropEditor();
                        this.imageFile = null;
                        this.clearImagePreview();
                        this.form.remove_image = true;
                        this.form.image_url = null;
                    },

                    clearImagePreview() {
                        if (this.imagePreview) {
                            URL.revokeObjectURL(this.imagePreview);
                            this.imagePreview = null;
                        }
                    },

                    nullableNumber(value, decimal = false) {
                        if (value === null || value === '' || typeof value === 'undefined') return null;
                        const parsed = decimal ? parseFloat(value) : parseInt(value);
                        return Number.isNaN(parsed) ? null : parsed;
                    },

                    buildPayload() {
                        const properties = this.cleanProperties();
                        properties.nature = this.form.type;

                        const legacy = this.deriveLegacyFields(properties);
                        const wonderful = properties.nature === 'wonderful';

                        return {
                            name: String(this.form.name ?? '').trim(),
                            type: properties.nature,
                            description:
                                String(this.form.description ?? '').trim()
                                || null,
                            default_quantity: Math.max(
                                1,
                                parseInt(this.form.default_quantity) || 1
                            ),
                            // A entrada visual usa vírgula; a API recebe número decimal.
                            weight: this.nullableNumber(
                                String(this.form.weight ?? '').replace(',', '.'),
                                true
                            ),
                            is_magical: wonderful,
                            rarity: this.form.rarity || 'common',
                            requires_attunement: wonderful
                                ? !!this.form.requires_attunement
                                : false,
                            is_cursed: !!this.form.is_cursed,
                            curse_name: this.form.is_cursed
                                ? (String(this.form.curse_name ?? '').trim() || null)
                                : null,
                            curse_description: this.form.is_cursed
                                ? (this.form.curse_description ?? '')
                                : null,
                            armor_class: legacy.armor_class,
                            damage: legacy.damage,
                            attack_bonus: legacy.attack_bonus,
                            damage_bonus: legacy.damage_bonus,
                            ability_bonuses: this.form.ability_bonuses ?? {},
                            properties,
                            modifiers: this.form.modifiers ?? {},
                            notes: this.form.notes ?? '',
                            tags: [], // Tags do Vault descontinuadas no editor.
                        };
                    },

                    appendFormValue(formData, key, value) {
                        if (Array.isArray(value)) {
                            value.forEach((entry, index) => this.appendFormValue(formData, `${key}[${index}]`, entry));
                            return;
                        }

                        if (value && typeof value === 'object' && !(value instanceof File)) {
                            Object.entries(value).forEach(([childKey, childValue]) => {
                                this.appendFormValue(formData, `${key}[${childKey}]`, childValue);
                            });
                            return;
                        }

                        if (typeof value === 'boolean') {
                            formData.append(key, value ? '1' : '0');
                            return;
                        }

                        formData.append(key, value === null ? '' : String(value));
                    },

                    buildFormData(payload) {
                        const formData = new FormData();
                        Object.entries(payload).forEach(([key, value]) => this.appendFormValue(formData, key, value));
                        if (this.imageFile) formData.append('image', this.imageFile);
                        if (this.form?.remove_image) formData.append('remove_image', '1');
                        return formData;
                    },

                    headers() {
                        return {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                            'X-Requested-With': 'XMLHttpRequest',
                        };
                    },

                    async saveItem() {
                        if (this.saving || !this.form) return;

                        const weapon = this.form?.properties?.weapon;
                        if (this.itemCategory() === 'weapon' && weapon) {
                            if (!Array.isArray(weapon.allowed_abilities) || !weapon.allowed_abilities.length) {
                                this.saveError = 'Selecione ao menos um atributo permitido para a arma.';
                                return;
                            }
                            const parts = [weapon.damage, ...(weapon.extra_damage || [])];
                            if (parts.some(part => !this.damageExpressionValid(part))) {
                                this.saveError = 'Escolha uma quantidade e um tipo de dado válidos para cada dano da arma.';
                                return;
                            }
                        }

                        // A rolagem inicial só pode ser configurada com uma expressão válida.
                        const featureGroups = [this.form?.properties?.features || []];
                        const stageGroups = this.form?.properties?.vestige?.stages;
                        if (stageGroups && this.form?.properties?.vestige?.enabled) {
                            for (const stage of ['dormant','awakened','exalted']) {
                                featureGroups.push(stageGroups[stage]?.features || []);
                            }
                        }
                        if (featureGroups.some(group => group.some(feature =>
                            feature?.usage?.enabled && feature.usage.counter_mode !== 'build' && feature.usage.initial_mode === 'roll' &&
                            !this.featureRollFormulaValid(feature.usage.initial_formula)
                        ))) {
                            this.saveError = 'Confira a rolagem inicial do rastreador (ex.: 2d6+1).';
                            return;
                        }

                        const payload = this.buildPayload();
                        if (!payload.name) {
                            this.saveError = 'Informe um nome para o item.';
                            this.editorTab = 'general';
                            return;
                        }

                        this.saving = true;
                        this.saveError = null;
                        const editing = this.editingId !== null;
                        const url = editing
                            ? this.urls.update.replace('__ITEM__', this.editingId)
                            : this.urls.store;

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: this.headers(),
                                body: this.buildFormData(payload),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) {
                                const errors = data?.errors ? Object.values(data.errors).flat().filter(Boolean) : [];
                                throw new Error(errors.length ? errors.join(' ') : (data?.message ?? 'Não foi possível salvar o item.'));
                            }

                            const savedItem = this.upsertItem(data.item);
                            // Não informar sucesso quando a resposta do servidor perdeu HTML rico.
                            // Conferimos a descrição principal, habilidades comuns e cada etapa.
                            const richPairs = [[payload.description ?? '', savedItem?.description ?? '']];
                            const expectedFeatures = payload.properties?.features ?? [];
                            const returnedFeatures = savedItem?.properties?.features ?? [];
                            expectedFeatures.forEach((feature, index) => {
                                richPairs.push([feature.description ?? '', returnedFeatures[index]?.description ?? '']);
                            });
                            if (payload.properties?.vestige?.enabled) {
                                for (const stage of ['dormant', 'awakened', 'exalted']) {
                                    const wanted = payload.properties.vestige.stages?.[stage];
                                    const actual = savedItem?.properties?.vestige?.stages?.[stage];
                                    richPairs.push([wanted?.lore ?? '', actual?.lore ?? '']);
                                    (wanted?.features ?? []).forEach((feature, index) => {
                                        richPairs.push([feature.description ?? '', actual?.features?.[index]?.description ?? '']);
                                    });
                                }
                            }
                            const richHtml = /<(?:p|br|strong|em|s|h3|h4|ul|ol|li|blockquote|hr)\b/i;
                            if (richPairs.some(([sent, returned]) =>
                                richHtml.test(String(sent)) && String(sent).trim() !== String(returned).trim()
                            )) {
                                this.editingId = savedItem?.id ?? this.editingId;
                                this.saving = false;
                                this.saveError = 'O servidor não devolveu todas as formatações do texto. O item foi recebido, mas o editor continuará aberto para evitar perda de conteúdo. Verifique a validação de properties e a persistência do HTML.';
                                return;
                            }
                            if (payload.properties?.vestige?.enabled) {
                                // Nunca afirmar sucesso se o backend descartou as etapas ou poderes.
                                const expected = payload.properties.vestige;
                                const received = savedItem?.properties?.vestige;
                                const savedStages = received?.stages;
                                const powersPreserved = ['dormant', 'awakened', 'exalted'].every(stage => {
                                    const wanted = expected.stages?.[stage]?.features ?? [];
                                    const actual = savedStages?.[stage]?.features;
                                    return Array.isArray(actual)
                                        && wanted.length === actual.length
                                        && wanted.every((power, index) =>
                                            String(power.title ?? '') === String(actual[index]?.title ?? '')
                                            && String(power.description ?? '') === String(actual[index]?.description ?? '')
                                            && (!power.usage?.enabled || actual[index]?.usage?.enabled === true)
                                        );
                                });
                                if (!received?.enabled || received.current_stage !== expected.current_stage || !powersPreserved) {
                                    this.editingId = savedItem.id;
                                    this.saving = false;
                                    this.saveError = 'O servidor não confirmou todas as habilidades das três etapas. Verifique o suporte a properties.vestige no controller antes de usar este item.';
                                    return;
                                }
                            }

                            // A arma sem atributo deve continuar assim depois da gravação.
                            if (payload.properties?.weapon?.allowed_abilities?.includes('none') &&
                                !savedItem?.properties?.weapon?.allowed_abilities?.includes('none')) {
                                this.editingId = savedItem?.id ?? this.editingId;
                                this.saving = false;
                                this.saveError = 'O servidor não preservou Sem Atributo. Confira a validação de properties.weapon.allowed_abilities.';
                                return;
                            }

                            // Não tratar como salvas opções novas se o backend descartou os campos.
                            const configuredUses = [];
                            const checkUses = (wanted, actual) => {
                                (wanted || []).forEach((feature, index) => {
                                    const usage = feature?.usage;
                                    if (!usage?.enabled) return;
                                    const returned = actual?.[index]?.usage || {};
                                    if (usage.counter_mode === 'build') {
                                        // A ausência da flag não é equivalente a 'não recupera':
                                        // sem ela, leitores antigos poderiam assumir recovery='day'.
                                        if (returned.counter_mode !== 'build' || Number(returned.current) !== 0 ||
                                            !Object.prototype.hasOwnProperty.call(returned, 'recovery_enabled') ||
                                            this.toBoolean(returned.recovery_enabled) !== !!usage.recovery_enabled ||
                                            (usage.recovery_enabled && String(returned.recovery) !== String(usage.recovery))) {
                                            configuredUses.push(feature.title || 'Habilidade');
                                        }
                                        return;
                                    }
                                    if (!['roll','fixed'].includes(usage.initial_mode)) return;
                                    if (returned.initial_mode !== usage.initial_mode ||
                                        (usage.initial_mode === 'roll' &&
                                         (String(returned.initial_formula || '').toLowerCase() !== String(usage.initial_formula || '').toLowerCase() ||
                                          returned.roll_trigger !== usage.roll_trigger))) {
                                        configuredUses.push(feature.title || 'Habilidade');
                                    }
                                });
                            };
                            checkUses(payload.properties?.features, savedItem?.properties?.features);
                            for (const stage of ['dormant','awakened','exalted']) {
                                checkUses(payload.properties?.vestige?.stages?.[stage]?.features,
                                    savedItem?.properties?.vestige?.stages?.[stage]?.features);
                            }
                            if (configuredUses.length) {
                                this.editingId = savedItem?.id ?? this.editingId;
                                this.saving = false;
                                this.saveError = 'O servidor não preservou a configuração inicial de cargas em: ' +
                                    configuredUses.join(', ') + '. Verifique a persistência de properties.features[].usage.';
                                return;
                            }

                            if (this.folderSystemReady) {
                                const targetFolderId = this.formFolderId
                                    ? parseInt(this.formFolderId)
                                    : null;
                                const currentFolderId = savedItem.folder_id
                                    ? parseInt(savedItem.folder_id)
                                    : null;

                                if (targetFolderId !== currentFolderId) {
                                    await this.moveVaultItem(savedItem.id, targetFolderId, true);
                                }
                            }

                            this.saving = false;
                            this.closeEditor();
                            this.showToast(editing ? 'Item atualizado no Vault.' : 'Item criado no Vault.');
                        } catch (error) {
                            this.saveError = error?.message ?? 'Não foi possível salvar o item.';
                            this.saving = false;
                        }
                    },

                    payloadForExisting(item) {
                        const normalized = this.normalizeItem(item);
                        const properties = this.normalizeProperties(
                            normalized.properties,
                            normalized
                        );

                        const legacy = this.deriveLegacyFields(properties);
                        const wonderful = properties.nature === 'wonderful';

                        return {
                            name: normalized.name,
                            type: properties.nature,
                            description: normalized.description || null,
                            default_quantity: normalized.default_quantity,
                            weight: normalized.weight,
                            is_magical: wonderful,
                            rarity: normalized.rarity || 'common',
                            requires_attunement: wonderful
                                ? !!normalized.requires_attunement
                                : false,
                            is_cursed: !!normalized.is_cursed,
                            curse_name: normalized.is_cursed
                                ? (String(normalized.curse_name ?? '').trim() || null)
                                : null,
                            curse_description: normalized.is_cursed
                                ? normalized.curse_description
                                : null,
                            armor_class: legacy.armor_class,
                            damage: legacy.damage,
                            attack_bonus: legacy.attack_bonus,
                            damage_bonus: legacy.damage_bonus,
                            ability_bonuses: normalized.ability_bonuses ?? {},
                            properties,
                            modifiers: normalized.modifiers ?? {},
                            notes: normalized.notes ?? '',
                            tags: normalized.tags ?? [],
                        };
                    },

                    async duplicateItem(item) {
                        if (this.busyId !== null) return;
                        this.busyId = item.id;

                        try {
                            const response = await fetch(this.urls.duplicate.replace('__ITEM__', item.id), {
                                method: 'POST',
                                headers: this.headers(),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível duplicar o item.');
                            this.upsertItem(data.item);
                            this.showToast('Cópia criada no Vault.');
                        } catch (error) {
                            this.showToast(error?.message ?? 'Não foi possível duplicar o item.', true);
                        } finally {
                            this.busyId = null;
                        }
                    },

                    async deleteVaultItem(item) {
                        if (
                            this.busyId !== null
                            || !item?.id
                        ) {
                            return false;
                        }

                        this.busyId =
                            item.id;

                        try {
                            const response =
                                await fetch(
                                    this.urls.destroy.replace(
                                        '__ITEM__',
                                        item.id
                                    ),
                                    {
                                        method: 'DELETE',
                                        headers: this.headers(),
                                    }
                                );

                            const data =
                                await response
                                    .json()
                                    .catch(
                                        () => ({})
                                    );

                            if (!response.ok) {
                                throw new Error(
                                    data?.message
                                    ?? 'Não foi possível excluir o item.'
                                );
                            }

                            const id =
                                parseInt(item.id);

                            this.items =
                                this.items.filter(
                                    current =>
                                        parseInt(current.id)
                                        !== id
                                );

                            if (
                                this.previewItem
                                && parseInt(this.previewItem.id)
                                    === id
                            ) {
                                this.closePreview();
                            }

                            if (
                                parseInt(this.editingId)
                                === id
                            ) {
                                this.closeEditor();
                            }

                            this.showToast(
                                'Item removido do Vault.'
                            );

                            return true;
                        } catch (error) {
                            this.showToast(
                                error?.message
                                ?? 'Não foi possível excluir o item.',
                                true
                            );

                            return false;
                        } finally {
                            this.busyId =
                                null;
                        }
                    },

                    async deleteItem() {
                        if (!this.editingId || this.saving) return;
                        if (!window.confirm('Remover este item do Vault? Espólios que já receberam um snapshot não serão alterados.')) return;

                        this.saving = true;

                        try {
                            const response = await fetch(this.urls.destroy.replace('__ITEM__', this.editingId), {
                                method: 'DELETE',
                                headers: this.headers(),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) throw new Error(data?.message ?? 'Não foi possível excluir o item.');

                            const id = parseInt(this.editingId);
                            this.items = this.items.filter(item => parseInt(item.id) !== id);
                            this.saving = false;
                            this.closeEditor();
                            this.showToast('Item removido do Vault.');
                        } catch (error) {
                            this.saveError = error?.message ?? 'Não foi possível excluir o item.';
                            this.saving = false;
                        }
                    },

                    upsertItem(raw) {
                        const item = this.normalizeItem(raw);
                        const index = this.items.findIndex(current => parseInt(current.id) === parseInt(item.id));

                        if (index >= 0) {
                            if (raw?.folder_id === undefined || raw?.folder_id === null) {
                                item.folder_id = this.items[index].folder_id ?? null;
                            }
                            this.items.splice(index, 1, item);
                        } else {
                            this.items.push(item);
                        }

                        return item;
                    },

                    showToast(message, error = false) {
                        this.toastMessage = message;
                        this.toastError = !!error;
                        this.toastOpen = true;
                        clearTimeout(this.toastTimer);
                        this.toastTimer = setTimeout(() => this.toastOpen = false, 3600);
                    },
